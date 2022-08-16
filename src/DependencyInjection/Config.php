<?php
namespace Siejka\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationimplementsConfigurationInterface{
    public function getConfigTreeBuilder(){
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('user_bundle');
        return $treeBuilder;
    }
}