<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 29/01/2019
 * Time: 16:55
 */

namespace ScyLabs\NeptuneBundle\DependencyInjection;


use Doctrine\ORM\Version;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineTargetEntitiesResolver
{
    public function resolve(ContainerBuilder $container){


        if(!$container->hasDefinition('doctrine.orm.listeners.resolve_target_entity')){
            throw new \RuntimeException('Cannot find Doctrine RTEL');
        }


        $resolveTargetEntityListener = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');


        $classes = $container->getParameter('scy_labs_neptune.classes');
        foreach ($classes as $class){
            if($class['class'] != $class['original'] && class_exists($class['class'])){
                $resolveTargetEntityListener
                    ->addMethodCall('addResolveTargetEntity', array(
                        $this->getInterface($container,$class['original']),
                        $this->getClass($container,$class['class']),
                        array()
                    ))
                ;
            }
        }


        if (version_compare(Version::VERSION, '2.5.0-DEV') < 0) {
            $resolveTargetEntityListener->addTag('doctrine.event_listener', array('event' => 'loadClassMetadata'));
        } else {
            $resolveTargetEntityListener->addTag('doctrine.event_subscriber', array('connection' => 'default'));
        }
        
    }


    private function getInterface(ContainerBuilder $container, $key)
    {
        if ($container->hasParameter($key)) {
            return $container->getParameter($key);
        }
        if (interface_exists($key) || class_exists($key)) {
            return $key;
        }
        throw new \InvalidArgumentException(
            sprintf('The interface or class %s does not exists.', $key)
        );
    }

    private function getClass(ContainerBuilder $container, $key)
    {
        if ($container->hasParameter($key)) {
            return $container->getParameter($key);
        }
        if (class_exists($key)) {
            return $key;
        }
        throw new \InvalidArgumentException(
            sprintf('The class %s does not exists.', $key)
        );
    }
}