<?php

namespace TallmanCode\AuthBundle\EmailConfirmation;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailConfirm implements EmailConfirmInterface
{
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private string $verifyEmailRouteName;
    private string $from_email;
    private bool $enabled;
    private EntityManagerInterface $manager;

    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer, EntityManagerInterface $manager, bool $enabled, ?string $verifyEmailRouteName, ?string $from_email)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
        $this->verifyEmailRouteName = $verifyEmailRouteName;
        $this->from_email = $from_email;
        $this->enabled = $enabled;
        $this->manager = $manager;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(UserInterface $user): void
    {
        // Check confirm email is enabled
        if (!$this->enabled) {
            return;
        }

        // TODO verify route name
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $this->verifyEmailRouteName,
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $email = new TemplatedEmail();
        $email->from($this->from_email);
        $email->to($user->getEmail());
        $email->htmlTemplate('@TallmanCodeAuth/confirmation_email.html.twig');
        $email->context([
            'signedUrl' => $signatureComponents->getSignedUrl(),
            'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
            'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
        ]);

        $this->mailer->send($email);
    }

    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

        $user->setIsVerified(true);

        $this->manager->persist($user);
        $this->manager->flush();
    }
}
