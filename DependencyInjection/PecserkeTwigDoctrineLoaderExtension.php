<?php

/*
 * (c) Tomas Pecserke <tomas@pecserke.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pecserke\Bundle\TwigDoctrineLoaderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Extension.
 *
 * @author Tomas Pecserke <tomas@pecserke.eu>
 */
class PecserkeTwigDoctrineLoaderExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(sprintf('%s.backend.%s', $this->getAlias(), $config['backend']), true);
        $container->setParameter(sprintf('%s.model_manager_name', $this->getAlias()), $config['manager_name']);
        switch ($config['backend']) {
            case 'mongodb':
                $objectManagerServiceId = 'doctrine_mongodb.odm.%s_document_manager';
                break;
            case 'orm':
            default:
                $objectManagerServiceId = 'doctrine.%s_entity_manager';
                break;
        }
        $objectManagerServiceId = sprintf($objectManagerServiceId, $config['manager_name']);
        $container->setAlias('pecserke_twig_doctrine_loader.object_manager', $objectManagerServiceId);
        $container->setParameter('pecserke_twig_doctrine_loader.model.template.class', $config['template_class']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $cachePrefix = $config['cache_prefix'] ?: sprintf(
            '%s/%s/',
            $container->getParameter('kernel.root_dir'),
            $this->getAlias()
        );
        $definition = $container->getDefinition('pecserke_twig_doctrine_loader.twig.loader.doctrine');
        $definition->replaceArgument(1, $cachePrefix);
    }
}
