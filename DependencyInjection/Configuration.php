<?php

/**
 * @author Takieddine Messaoudi <tmessaoudi@smart-team.tn>
 */


namespace SmartTeam\DoctrineBehaviorsDependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package SmartTeam\DoctrineBehaviorsDependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    public const ROOT_NAMESPACE = 'smartteam_doctrinebehaviors';
    public const ROOT_PARAMETERS_NAMESPACE = 'smartteam.doctrinebehaviors';

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(self::ROOT_NAMESPACE);

        $rootNode
            ->children()
                ->arrayNode('available_languages')
                    ->scalarPrototype()
                    ->end()
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
