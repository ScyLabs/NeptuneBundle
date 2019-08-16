<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 19/06/2018
 * Time: 10:18
 */

namespace ScyLabs\NeptuneBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory){
        $this->targetDirectory = $targetDirectory;
    }
    public function upload(SymfonyFile $file){
        $mime = $file->getMimeType();
        $filesize = $file->getSize();
        $name = md5(uniqid());
        
        if(null === $ext = $file->guessExtension()){
            if($file->getMimeType() == 'image/svg+xml'){
                $ext = 'svg';
            }
            else{
                $ext = explode('/',$file->getMimeType())[1];
            }
        }        
        $fileName = $name.'.'.$ext;
        
        $file->move($this->getTargetDirectory(),$fileName);

        $minesok = array(
            'image/jpeg',
            'image/png',
            'image/gif',
        );

        if(in_array($mime,$minesok))
        {
            $type = explode('/',$mime)[1];

            if(($filesize / 1000000) > 2){
                $image = new \Imagick($this->getTargetDirectory().'/'.$fileName);
                $image->setImageCompressionQuality(75);
                $image->writeImage();
                $image->destroy();
            }

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