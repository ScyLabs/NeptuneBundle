<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 18/11/2019
 * Time: 12:17
 */

namespace ScyLabs\NeptuneBundle\Services;
use ScyLabs\NeptuneBundle\Model\ClassFounderInterface;
use Symfony\Component\Yaml\Yaml;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ClassFounder implements ClassFounderInterface,ContainerAwareInterface
{

    use ContainerAwareTrait;

    public function getClass(string $alias){

        $originalClasses = Yaml::parseFile(dirname(__DIR__).'/Resources/config/original_classes.yaml');
        $classes = $this->getParameter('scy_labs_neptune.override');

        return
            (array_key_exists($alias,$classes) &&  class_exists($classes[$alias])) ? $classes[$alias] :
                (array_key_exists($alias,$originalClasses) && (class_exists($originalClasses[$alias])) ? $originalClasses[$alias] : null);

    }

    private function getParameter(string $parameterName){
        return $this->container->getParameter($parameterName);
    }
}