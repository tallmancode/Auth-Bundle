<?php

namespace TallmanCode\AuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use TallmanCode\AuthBundle\Form\Registration\RegistrationFormType;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('tallman_code_auth');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('user')
                    ->children()
                        ->scalarNode('class')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('registration')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('form')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('type')->defaultValue(RegistrationFormType::class)->end()
                                    ->scalarNode('name')->defaultValue('tallmancode_auth_registration_form')->end()
                                ->end()
                            ->end()
                            ->arrayNode('confirm')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->booleanNode('enabled')->defaultValue(true)->end()
                                    ->scalarNode('verify_route_name')->defaultValue('tallmancode_auth_verify')->end()
                                    ->scalarNode('failed_redirect_route')->defaultValue(null)->end()
                                    ->scalarNode('from_address')->defaultValue(null)->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
