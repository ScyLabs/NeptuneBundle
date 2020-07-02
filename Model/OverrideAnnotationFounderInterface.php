<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 07/11/2019
 * Time: 12:21
 */

namespace ScyLabs\NeptuneBundle\Model;


interface OverrideAnnotationFounderInterface
{
    public function getAnnotations(string $directoryNameSpace,string $directoryPath);
}