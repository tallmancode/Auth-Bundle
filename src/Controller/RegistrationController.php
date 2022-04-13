<?php

namespace TallmanCode\AuthBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use TallmanCode\AuthBundle\EmailConfirmation\EmailConfirmationException;
use TallmanCode\AuthBundle\EmailConfirmation\EmailConfirmInterface;
use TallmanCode\AuthBundle\Form\Factory\AuthFormFactoryInterface;

class RegistrationController extends AbstractController
{
    private ?string $userClass;

    private EmailConfirmInterface $emailConfirm;

    public function __construct(?string $userClass, EmailConfirmInterface $emailConfirm)
    {
        $this->userClass = $userClass;
        $this->emailConfirm = $emailConfirm;
    }

    /**
     * @throws EmailConfirmationException|TransportExceptionInterface
     */
    public function index(Request $request, AuthFormFactoryInterface $formFactory, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $user = new $this->userClass();
        $form = $formFactory->createForm([], $user);
        $form->submit($request->toArray());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            try {
                $this->emailConfirm->send($user);
            } catch (\Exception $e) {
                $entityManager->remove($user);
                $entityManager->flush();
                throw new EmailConfirmationException($e);
            }

            return new JsonResponse('success', 201);
        }

        // return form errors
        $violations = [];
        foreach ($form->getErrors(true) as $error) {
            $violations[] = [
                'message' => $error->getMessage(),
                'propertyPath' => $error->getOrigin()->getName(),
            ];
        }

        return new JsonResponse(['violations' => $violations], 422);
    }

    private function wantsJson(Request $request)
    {
        if ('application/json' === $request->headers->get('content-type')) {
            return true;
        }

        return false;
    }
}
