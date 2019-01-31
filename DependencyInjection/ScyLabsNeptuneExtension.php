<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01/08/2018
 * Time: 12:16
 */

namespace ScyLabs\NeptuneBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Translation\Loader\YamlFileLoader as TranslationLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class ScyLabsNeptuneExtension extends Extension
{
    public function load(array $configs,ContainerBuilder $container){


        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration,$configs);
        $container->setParameter($this->getAlias().'.classes',$config['classes']);



        $bundleRoot = new FileLocator(dirname(__DIR__));
        $loader = new YamlFileLoader(
            $container,$bundleRoot


        );
        $transLoader = new TranslationLoader($container,$bundleRoot);

        //$transLoader->load('translations/messages-fr.yaml','fr');

    }
}