<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 08/11/2018
 * Time: 09:36
 */

namespace ScyLabs\NeptuneBundle\Twig;


use Twig\Extension\AbstractExtension;

use Twig\TwigFilter;
use Twig\TwigFunction;

class BaseExtension extends AbstractExtension
{

    public function getFilters()
    {
        return array(
            new TwigFilter('is_object',array($this,'isObject')),
        );
    }
    public function getFunctions() {
        return [
            new TwigFunction('webpActiveInImagick',array($this,'webpActiveInImagick'))
        ];
    }

    public function isObject($object){
        return is_object($object);
   }
   private function imagickExist(){
        return class_exists(\Imagick::class);
   }
   public function webpActiveInImagick(){
        if(!$this->imagickExist())
            return false;
        $formats = \Imagick::queryFormats();

        foreach($formats as $key => $format){
            $formats[$key] = strtolower($format);
        }
        return in_array('webp',$formats);
   }
}