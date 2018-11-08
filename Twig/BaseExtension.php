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
class BaseExtension extends AbstractExtension
{

    public function getFilters()
    {
        return array(
            new TwigFilter('is_object',array($this,'isObject'))
        );
    }
   public function isObject($object){
        return is_object($object);
   }
}