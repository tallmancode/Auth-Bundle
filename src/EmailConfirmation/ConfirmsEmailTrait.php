<?php

namespace TallmanCode\AuthBundle\EmailConfirmation;

use Doctrine\ORM\Mapping as ORM;

trait ConfirmsEmailTrait
{
    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): void
    {
        $this->isVerified = $isVerified;
    }
}
