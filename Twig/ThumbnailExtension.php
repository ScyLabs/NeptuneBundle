<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 13/07/2018
 * Time: 14:34
 */

namespace ScyLabs\NeptuneBundle\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ThumbnailExtension extends AbstractExtension
{

    public function getFilters()
    {
        return array(
          new TwigFilter('thumb',array($this,'thumbFilter'))
        );
    }
    public function thumbFilter($path,$w=0,$h = 0,$q = 75){

        if(!is_string($path))
            return '';
        $realPath = $_SERVER['DOCUMENT_ROOT'].$path;
        if(!file_exists($realPath))
            return '';


        $exp = explode('.',$path);
        $nameExp = explode('/',$path);
        $dir  = '';

        // Récupération du dirname() , mais en URL WEB.
        for($i = 1;$i < sizeof($nameExp) - 1;$i++){
            $dir .= '/'.$nameExp[$i];
        }

        $tmpName = $nameExp[sizeof($nameExp) - 1];
        $nameExp = explode('.',$tmpName);

        $fileName = '';
        // Récupération du nom du fichier
        for($i =0; $i < sizeof($nameExp ) - 1;$i++){
            $fileName .= $nameExp[$i];
        }
        $realDir = dirname($realPath);

        if(!file_exists($realDir.'/thumbnails/')){
            mkdir($realDir.'/thumbnails/');
        }

        $wh = '';

        if($w != 0 || $h != 0)
            $wh = '_'.(($w != 0) ? $w : 'auto').'x'.(($h != 0) ? $h : 'auto');

        $localThumb = $realDir.'/thumbnails/'.$fileName;
        $webThumb = $dir.'/thumbnails/'.$fileName;

        // Si pdf , génération de la miniature
        $ext = $exp[sizeof($exp) - 1];

        if($ext == 'pdf'){

            if(file_exists($localThumb.'jpg') && ($w == 0 && $h == 0)){
                return $webThumb;
            }
            elseif(file_exists($localThumb.$wh.'.jpg')){
                return $webThumb.$wh.'.jpg';

            }

            $img = new \Imagick($realPath);
            $img->setCompressionQuality(90);
            $img->setIteratorIndex(0);
            $img->writeImage($localThumb.'.jpg');

            if($w != 0 || $h != 0) {

                $img = new \Imagick($localThumb.'.jpg');
                $img->setCompressionQuality(90);
                $img->thumbnailImage($w,$h);
                $img->writeImage($localThumb.$wh.'.jpg');
                $webThumb .= $wh.'.jpg';

            }

            return $webThumb;

        }
        else{

            if(file_exists($localThumb.$wh.'.'.$ext)){
                return $webThumb.$wh.'.'.$ext;
            }

            $img = new \Imagick($realPath);
            $img->setCompressionQuality($q);
            if($w != 0 || $h != 0){
                
                $img->thumbnailImage($w,$h);
            }
            $img->writeImage($localThumb.$wh.'.'.$ext);
            return $webThumb.$wh.'.'.$ext;
        }

    }
}