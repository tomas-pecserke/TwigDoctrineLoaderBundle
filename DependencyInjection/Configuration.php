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
                    ->values(array('orm', 'mongodb', 'couchdb'))
                    ->defaultValue('orm') // use ORM by default
                    ->info('chooses doctrine backend for model')
                ->end()
                ->scalarNode('template_class')
                    ->defaultValue()
                    ->validate(self::DEFAULT_TEMPLATE_CLASS)
                        ->ifTrue(function($class) {
                            return $class !== self::DEFAULT_TEMPLATE_CLASS &&
                                !is_subclass_of($class, self::DEFAULT_TEMPLATE_CLASS)
                            ;
                        })
                        ->thenInvalid('Template class "%s" is not subclass of "' . self::DEFAULT_TEMPLATE_CLASS . '".')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
