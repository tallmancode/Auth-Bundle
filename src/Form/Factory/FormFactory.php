<?php

namespace TallmanCode\AuthBundle\Form\Factory;

use Symfony\Component\Form\FormFactoryInterface;

class FormFactory implements AuthFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $validationGroups;

    public function __construct(FormFactoryInterface $formFactory, $name, $type, array $validationGroups = null)
    {
        $this->formFactory = $formFactory;
        $this->name = $name;
        $this->type = $type;
        $this->validationGroups = $validationGroups;
    }

    public function createForm(array $options = [], $data = null)
    {
        $options = array_merge(['validation_groups' => $this->validationGroups, 'csrf_protection' => false], $options);

        return $this->formFactory->createNamed($this->name, $this->type, $data, $options);
    }
}
