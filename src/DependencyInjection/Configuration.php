<?php
namespace Odesskij\Bundle\GeneratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for OdesskijGeneratorBundle.
 *
 * @author Vladimir Odesskij <odesskij1992@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        // @formatter:off
        $treeBuilder
            ->root($this->alias)
            ->children()
                ->arrayNode('generator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('php_doc')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('author')->defaultValue('Vladimir Odesskij <odesskij1992@gmail.com>')->end()
                            ->end()
                        ->end()
                    ->end()
                 ->end()
            ->end()
        ;
        // @formatter:on
        return $treeBuilder;
    }
}
