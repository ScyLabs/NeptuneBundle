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
            new TwigFilter('clean',array($this,'clean')),
        );
    }
    public function getFunctions() {
        return [
            new TwigFunction('webpActiveInImagick',array($this,'webpActiveInImagick'))
        ];
    }

    public function clean(string $text) : string {

        $text = str_replace(
            array(" ", "À", "Á", "Â", "Ã", "Ä", "Å", "à", "á", "â", "ã", "ä", "å", "Ò", "Ó", "Ô", "Õ", "Ö", "Ø", "ò", "ó", "ô", "õ", "ö", "ø", "È", "É", "Ê", "Ë", "è", "é", "ê", "ë", "Ç", "ç", "Ì", "Í", "Î", "Ï", "ì", "í", "î", "ï", "Ù", "Ú", "Û", "Ü", "ù", "ú", "û", "ü", "ÿ", "Ñ", "ñ", "(", ")", "[", "]", "'", "#", "~", "$", "&", "%", "*", "@", "ç", "!", "?", ";", ",", ":", "/", "^", "¨", "€", "{", "}", "|", "+", ".", "²"),
            array("-", "A", "A", "A", "A", "A", "A", "a", "a", "a", "a", "a", "a", "O", "O", "O", "O", "O", "O", "o", "o", "o", "o", "o", "o", "E", "E", "E", "E", "e", "e", "e", "e", "C", "c", "I", "I", "I", "I", "i", "i", "i", "i", "U", "U", "U", "U", "u", "u", "u", "u", "y", "N", "n", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "euro", "", "", "", "", "", "2"),
            $text
        );

        $text = preg_replace("#[^a-zA-Z_0-9.-]#","",$text);


        return $text;
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