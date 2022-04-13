<?php

namespace TallmanCode\AuthBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TallmanCode\AuthBundle\DependencyInjection\TallmanCodeAuthExtension;

class TallmanCodeAuthBundle extends Bundle
{
    /**
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new TallmanCodeAuthExtension();
        }

        return $this->extension;
    }
}
