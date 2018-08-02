<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01/08/2018
 * Time: 12:16
 */

namespace ScyLabs\NeptuneBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class ScyLabsNeptuneExtension extends Extension
{
    public function load(array $configs,ContainerBuilder $container){
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__).'/Resources/config')
        );
        $loader->load('services.yaml');
        $loader->load('routing.yaml');
    }
}