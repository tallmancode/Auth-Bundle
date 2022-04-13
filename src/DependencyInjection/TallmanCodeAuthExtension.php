<?php

namespace TallmanCode\AuthBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TallmanCodeAuthExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);

        if (!$configuration) {
            throw new InvalidArgumentException('Tmc Settings Bundle configuration is null.');
        }

        $config = $this->processConfiguration($configuration, $configs);
        $formFactory = $container->getDefinition('tallman_code_auth.registration.form.factory');
        $formFactory->setArgument(1, $config['registration']['form']['name']);
        $formFactory->setArgument(2, $config['registration']['form']['type']);

        $formFactory = $container->getDefinition('tallman_code_auth.registration.registration_form');
        $formFactory->setArgument(0, $config['user']['class']);

        $registerController = $container->getDefinition('tallman_code_auth.registration_controller');
        $registerController->setArgument(0, $config['user']['class']);

        $confirmEmailController = $container->getDefinition('tallman_code_auth.email_confirmation_controller');
        $confirmEmailController->setArgument(0, $config['user']['class']);
        $confirmEmailController->setArgument(1, $config['registration']['confirm']['failed_redirect_route']);

        $confirmEmail = $container->getDefinition('tallman_code_auth.email_confirmation.email_confirm');
        $confirmEmail->setArgument(3, $config['registration']['confirm']['enabled']);
        $confirmEmail->setArgument(4, $config['registration']['confirm']['verify_route_name']);
        $confirmEmail->setArgument(5, $config['registration']['confirm']['from_address']);
    }

    public function getAlias()
    {
        return 'tallman_code_auth';
    }
}
