<?php

namespace BestIt\CtOrderExportBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Loads the config for the best_it_ct_order_export.
 * @author lange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage DependencyInjection
 * @version $id$
 */
class BestItCtOrderExportExtension extends Extension
{
    /**
     * Loads the bundle config.
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setAlias(
            'best_it_ct_order_export.export.filesystem',
            $config['filesystem']
        );

        $container->setAlias('best_it_ct_order_export.logger', $config['logger']);

        $container->setParameter(
            'best_it_ct_order_export.commercetools.client.id',
            (string) @ $config['commercetools_client']['id']
        );

        $container->setParameter(
            'best_it_ct_order_export.commercetools.client.secret',
            (string) @ $config['commercetools_client']['secret']
        );

        $container->setParameter(
            'best_it_ct_order_export.commercetools.client.project',
            (string) @ $config['commercetools_client']['project']
        );

        $container->setParameter(
            'best_it_ct_order_export.commercetools.client.scope',
            (string) @ $config['commercetools_client']['scope']
        );

        $container->setParameter(
            'best_it_ct_order_export.orders.with_pagination',
            (bool) ( $config['orders']['with_pagination'] ?? true )
        );

        $container->setParameter('best_it_ct_order_export.orders.file_template', $config['orders']['file_template']);
        $container->setParameter('best_it_ct_order_export.orders.name_scheme', $config['orders']['name_scheme']);

        $container->setParameter(
            'best_it_ct_order_export.orders.default_where',
            $config['orders']['default_where'] ?? []
        );
    }
}
