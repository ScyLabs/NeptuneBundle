<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 23/08/2018
 * Time: 17:20
 */

namespace ScyLabs\NeptuneBundle\Controller;

use ScyLabs\NeptuneBundle\Entity\File;
use ScyLabs\NeptuneBundle\Entity\Photo;
use ScyLabs\NeptuneBundle\Model\NotCompressedInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends BaseController implements NotCompressedInterface
{
    public function generateAction(Request $request,$id,$width,$height,$multiplicator,$truncate,$monochrome,$name){

        $monochrome = trim($monochrome,'/');

        $monochrome = (empty($monochrome)) ? false : $monochrome;


        if($width == 0 && $height == 0) {
            $width = 1000;
        }

        // Récupération de la photo
        $ph = $this->getDoctrine()->getRepository($this->getClass('photo'))->find($id);

        if($ph === null){
            throw new HttpException(404,"La photo n'existe pas");
        }

        /* On récupère le fichier et son chemin */
        $file = $ph->getFile();

        $formats = \Imagick::queryFormats();

        foreach($formats as $key => $format){
            $formats[$key] = strtolower($format);
        }


        $nameExp = explode('.',$name);
        $ext = $nameExp[sizeof($nameExp) -1];
        if(empty($ext)){
            $ext = $file->getExt();
        }

        if(in_array($file->getExt(),['jpeg','jpg'])){

            $ext = (!in_array('webp',$formats)) ? $file->getExt() : $ext;
        }else{
            $ext = $file->getExt();
        }

        $dir = $this->getParameter('uploads_directory');
        $filePath = $dir.$file->getFile();

        if($file->getExt() == 'svg'){
            return new Response(file_get_contents($filePath),200,array('Content-type'=>'image/svg+xml'));
        }

        if(!file_exists($filePath)){
            throw new HttpException(404,"La photo n'existe pas");
        }


        // On applique le multiplicateur de taille
        $width *= ($multiplicator / 100);
        $height *= ($multiplicator / 100);


        // Si width != 0 , on calcule la meilleure taille
        if($width != 0 && $truncate == 0){
            $width  = $this->calcScale($width);
        }
        // Si Height n'est pas vide , on calcule la meilleure taille
        elseif($height != 0 && $truncate == 0){
            $height = $this->calcScale($height);

        }

        $localThumb = $dir.'thumbnails';

        $nameExp = explode('.',$file->getFile());

        $fileName = '';
        // Récupération du nom du fichier
        for($i =0; $i < sizeof($nameExp ) - 1;$i++){
            $fileName .= $nameExp[$i];
        }

        $wh = '';

        if($width != 0 || $height != 0)
            $wh = '_'.(($width != 0) ? $width : 'auto').'x'.(($height != 0) ? $height : 'auto');

        if($monochrome !== false){
            $monochrome = explode('-',$monochrome);
            $wh .= '_monochrome_'.$monochrome[0].'_'.$monochrome[1];
        }
        if($truncate != 0){
            $wh .= '_truncate';
        }

        $path = $localThumb.'/'.$fileName.$wh.'.'.$ext;

        if(file_exists($path)){
            if(filesize($path) > filesize($filePath)){
                $path = $filePath;
            }

            $this->headers($file,$path);
            $response = new \Symfony\Component\HttpFoundation\File\File($path);
            return $this->file($response,'',ResponseHeaderBag::DISPOSITION_INLINE);
        }


        // On récupère L'image de base
        $img = new \Imagick($filePath);
        // Si height != 0 && width != 0 et que on ne calcule pas le ratio .. Alors teste le ratio

        if($height != 0  && $width != 0 && $truncate == 0){

            $resolution = $img->getImageGeometry();
            $ratioImg  = $resolution['width'] / $resolution['height'];
            $ratioResult = $width / $height;
            // Si le résultat doit être en portrait on défini Width a 0
            if($ratioResult <= $ratioImg){
                $width = 0;
            }
            else{
                // Si l'image doit être en paysage on défini  heightr a 0
                $height = 0;
            }

        }

        if(!file_exists($localThumb)){
            mkdir($localThumb);
        }

        if(!file_exists($path)){
            $img->setImageCompressionQuality($this->calcQuality($width));
            if($truncate == 0)
                $img->thumbnailImage($width,$height);
            else{
                $img->cropThumbnailImage($width,$height);
            }
            if($monochrome !== false){

                $img->modulateImage(100,0,100);
                $clut = new \Imagick();
                $clut->newPseudoImage(255,1,"gradient:#".$monochrome[0]."-#".$monochrome[1]);
                // Apply duotone CLUT to image
                $img->clutImage($clut);
                $clut->destroy();
            }
            $img->writeImage($path);
        }

        $img->destroy();

        if(filesize($path) > filesize($filePath)){
            $path = $filePath;
        }
        $this->headers($file,$path);

        $response = new \Symfony\Component\HttpFoundation\File\File($path);
        return $this->file($response,'',ResponseHeaderBag::DISPOSITION_INLINE);


    }
    private function calcScale($val){
        return (round($val /100,0,PHP_ROUND_HALF_UP) * 100 + 50 );
    }
    private function headers(File $file,$path){

        $last_modified_time = filemtime($path);
        $etag = 'W/"' . md5($last_modified_time) . '"';

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified_time) . " GMT");
        header('Cache-Control: public, max-age=604800'); // On peut ici changer la durée de validité du cache
        header("Etag: $etag");
        $result = '';


    }

    private function calcQuality($width,$type = 'webp'){
        if($type == 'webp'){

            return 80;
        }
        return 90;
    }
}