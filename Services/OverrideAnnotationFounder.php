<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 07/11/2019
 * Time: 12:21
 */

namespace ScyLabs\NeptuneBundle\Services;


use Doctrine\Common\Annotations\AnnotationReader;
use ScyLabs\NeptuneBundle\Annotation\ScyLabsNeptune\Override;
use ScyLabs\NeptuneBundle\Model\OverrideAnnotationFounderInterface;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\Mapping as ORM;

class OverrideAnnotationFounder implements OverrideAnnotationFounderInterface
{
    const ANNOTATIONS_DIRECTORIES = ['Entity','Form'];
    public function getAnnotations(string $directoryNameSpace, string $directoryPath) {
        $overrideAnnotations = [];

        $annotationReader = new AnnotationReader();
        new \ReflectionClass(ORM\Entity::class);
        new \ReflectionClass(Override::class);

        foreach (self::ANNOTATIONS_DIRECTORIES as $directoryName){
            if(is_dir($directory = $directoryPath.'/'.$directoryName)){
                foreach (scandir($directory) as $file){

                    if(!preg_match('/[^.].*\.php$/Ui',$file))
                        continue;

                    $explodeNameSpace = explode("\\",$directoryNameSpace);
                    $explodeNameSpace[] = $directoryName;
                    $explodeNameSpace[] = str_replace('.php','',$file);
                    $classNamespace = implode('\\',$explodeNameSpace);


                    $reflectionClass = new \ReflectionClass($classNamespace);

                    if (null !== $classAnotations = $annotationReader->getClassAnnotations($reflectionClass)){
                        foreach ($classAnotations as $classAnotation){
                            if($classAnotation instanceof Override && class_exists($classAnotation->class))
                                $overrideAnnotations[] = $classAnotation;
                        }
                    }
                }
            }
        }
        return $overrideAnnotations;
    }
}