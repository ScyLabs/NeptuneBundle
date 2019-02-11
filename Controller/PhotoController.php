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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{


    private $photoQuality = 90;


    public function generateAction(Request $request,$id,$width,$height,$multiplicator,$truncate){

        if($width == 0 && $height == 0) {
           $width = 1000;
        }

        // Récupération de la photo
        $ph = $this->getDoctrine()->getRepository(Photo::class)->find($id);
        if($ph === null){
            throw new HttpException(404,"La photo n'existe pas");
        }

        /* On récupère le fichier et son chemin */
        $file = $ph->getFile();
        $dir = $this->getParameter('uploads_directory');
        $filePath = $dir.$file->getFile();


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


        $path = $localThumb.'/'.$fileName.$wh.'.'.$file->getExt();
        if(file_exists($path)){
            $this->headers($file,$path);
            return new Response(readfile($path));
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
            $img->setCompressionQuality($this->photoQuality);
            if($truncate == 0)
                $img->thumbnailImage($width,$height);
            else{
                $img->cropThumbnailImage($width,$height);

            }
            $img->writeImage($path);
        }

        // Récupération de l'extension du fichier

        // Si jpg , deviens Jpeg (pour norme HTTP)
        $this->headers($file,$path);

        $img->destroy();
        return new Response(readfile($path));

    }
    private function calcScale($val){
        return (round($val /100,0,PHP_ROUND_HALF_UP) * 100 + 50 );
    }
    private function headers(File $file,$path){

        $last_modified_time = filemtime($path);
        $etag = 'W/"' . md5($last_modified_time) . '"';

        $result = ($file->getExt() == 'jpg') ? 'jpeg' : $file->getExt();
        header('Content-Type: image/'.$result);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified_time) . " GMT");
        header('Cache-Control: public, max-age=604800'); // On peut ici changer la durée de validité du cache
        header("Etag: $etag");
        $result = '';

        if ((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $last_modified_time) ||
            (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag === trim($_SERVER['HTTP_IF_NONE_MATCH'])) ) {
            // On renvoit une 304 si on n'a rien modifié depuis
            header('HTTP/1.1 304 Not Modified');
            exit();
        }
    }
}