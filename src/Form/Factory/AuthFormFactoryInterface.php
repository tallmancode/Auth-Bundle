<?php

namespace TallmanCode\AuthBundle\Form\Factory;

interface AuthFormFactoryInterface
{
    public function createForm(array $options = [], $data = null);
}
