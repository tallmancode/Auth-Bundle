<?php

namespace TallmanCode\AuthBundle\EmailConfirmation;

use Symfony\Component\Security\Core\User\UserInterface;

interface EmailConfirmInterface
{
    public function send(UserInterface $user): void;
}
