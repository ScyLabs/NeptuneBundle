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
use Symfony\Component\Yaml\Yaml;


class ScyLabsNeptuneExtension extends Extension
{
    public function load(array $configs,ContainerBuilder $container){

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration,$configs);
        $originalClasses = Yaml::parseFile(dirname(__DIR__).'/Resources/config/original_classes.yaml');

        foreach ($originalClasses as $key => $class) {
            if (!array_key_exists($key, $config['override'])) {
                $config['override'][$key] = $class;
            }
        }

        $container->setParameter($this->getAlias().'.override',$config['override']);
        $container->setParameter($this->getAlias().'.compress',$config['compress']);
        $container->setParameter($this->getAlias().'.sitemap',$config['sitemap']);
        $container->setParameter($this->getAlias().'.icons',$config['icons']);



        $bundleRoot = new FileLocator(dirname(__DIR__));

        $loader = new YamlFileLoader($container,$bundleRoot);


        $loader->load('Resources/config/services.yaml');


    }
}