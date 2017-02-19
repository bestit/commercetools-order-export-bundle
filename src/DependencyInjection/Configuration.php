<?php

namespace BestIt\CtOrderExportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class for this bundle.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage DependencyInjection
 * @version $id$
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Parses the config.
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $rootNode = $builder->root('best_it_ct_order_export');

        $rootNode
            ->children()
                ->arrayNode('commercetools_client')
                    ->isRequired()
                    ->children()
                        ->scalarNode('id')->cannotBeEmpty()->isRequired()->end()
                        ->scalarNode('secret')->cannotBeEmpty()->isRequired()->end()
                        ->scalarNode('project')->cannotBeEmpty()->isRequired()->end()
                        ->scalarNode('scope')->cannotBeEmpty()->isRequired()->end()
                    ->end()
                ->end()
                ->scalarNode('filesystem')
                    ->info('Please provide the service id for your flysystem file system.')
                    ->isRequired()
                ->end()
                ->arrayNode('orders')
                    ->children()
                        ->booleanNode('with_pagination')
                            ->defaultValue(true)
                            ->info(
                                'Should we use a paginated list of orders (or is the result list changing by "itself")?'
                            )
                        ->end()
                        ->arrayNode('default_where')
                            ->info(
                                'Add where clauses for orders: ' .
                                    'https://dev.commercetools.com/http-api-projects-orders.html#query-orders'
                            )
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('file_template')
                            ->info('Which template is used for the export of a single order?')
                            ->defaultValue('detail.xml.twig')
                        ->end()
                        ->scalarNode('name_scheme')
                            ->defaultValue('order_{{id}}_{{YmdHis}}.xml')
                            ->info(
                                'Provide an order field name or a format string for the date function enclosed ' .
                                'with {{ and }}.'
                            )
                        ->end()
                    ->end()
                ->end();

        return $builder;
    }
}
