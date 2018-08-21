<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 19/06/2018
 * Time: 10:18
 */

namespace ScyLabs\NeptuneBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory){
        $this->targetDirectory = $targetDirectory;
    }
    public function upload(UploadedFile $file){
        $mime = $file->getMimeType();
        $filesize = $file->getSize();

        $name = md5(uniqid());
        $fileName = $name.'.'.$file->guessExtension();

        $file->move($this->getTargetDirectory(),$fileName);


        // Future emplacement d'ImageMagic . (A retenter + tard)

        $minesok = array(
            'image/jpeg',
            'image/png',
        );

        if(in_array($mime,$minesok))
        {
            $type = explode('/',$mime)[1];

            if(($filesize / 1000000) > 2){
                $image = new \Imagick($this->getTargetDirectory().'/'.$fileName);
                $image->setImageCompressionQuality(75);
                $image->writeImage();
            }

        }
        elseif($mime == "application/pdf") {
            $thumbsDir = $this->getTargetDirectory().'/thumbnails/';
            if(!file_exists($thumbsDir)){
                mkdir($thumbsDir);
            }
            $image = new \Imagick($this->getTargetDirectory().'/'.$fileName);
            $image->setIteratorIndex(0);
            $image->setCompressionQuality(90);

            $image->writeImage($thumbsDir.$name.'.jpg');

        }


        return $fileName;
    }
    public function unlink($fileName){

        if(file_exists($this->getTargetDirectory().$fileName)){
            unlink($this->getTargetDirectory().$fileName);
        }
        else{
            return false;
        }
        return true;
    }
    public function getTargetDirectory(){
        return $this->targetDirectory;
    }
}