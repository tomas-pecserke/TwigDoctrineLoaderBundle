<?php

/*
 * (c) Tomas Pecserke <tomas@pecserke.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pecserke\Bundle\TwigDoctrineLoaderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * @author Tomas Pecserke <tomas@pecserke.eu>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    const DEFAULT_TEMPLATE_CLASS = 'Pecserke\Bundle\TwigDoctrineLoaderBundle\Model\Template';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pecserke_twig_doctrine_loader');

        $rootNode
            ->children()
                ->enumNode('backend')
                    ->values(array('orm', 'mongodb'))
                    ->defaultValue('orm') // use ORM by default
                ->end()
                ->scalarNode('manager_name')
                    ->defaultValue('default')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('template_class')
                    ->defaultValue(static::DEFAULT_TEMPLATE_CLASS)
                    ->validate(static::DEFAULT_TEMPLATE_CLASS)
                        ->ifTrue(function($class) {
                            return $class !== static::DEFAULT_TEMPLATE_CLASS &&
                                !is_subclass_of($class, static::DEFAULT_TEMPLATE_CLASS)
                            ;
                        })
                        ->thenInvalid('Template class "%s" is not subclass of "' . static::DEFAULT_TEMPLATE_CLASS . '".')
                    ->end()
                ->end()
                ->scalarNode('cache_prefix')
                    ->defaultNull()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
