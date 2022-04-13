<?php

namespace TallmanCode\AuthBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TallmanCode\AuthBundle\EmailConfirmation\EmailConfirmInterface;

class ConfirmEmailController extends AbstractController
{
    private ?string $userClass;

    private EmailConfirmInterface $emailConfirm;

    private ?string $failedRedirect;

    public function __construct(?string $userClass, ?string $failedRedirect, EmailConfirmInterface $emailConfirm)
    {
        $this->userClass = $userClass;
        $this->emailConfirm = $emailConfirm;
        $this->failedRedirect = $failedRedirect;
    }

    public function verfiy(Request $request, EntityManagerInterface $manager)
    {
        $userRepository = $manager->getRepository($this->userClass);

        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute($this->failedRedirect);
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute($this->failedRedirect);
        }

        try {
            $this->emailConfirm->handleEmailConfirmation($request, $user);
        } catch (\Exception $exception) {
            return $this->redirectToRoute($this->failedRedirect);
        }

        return new JsonResponse('success', 201);
    }
}
