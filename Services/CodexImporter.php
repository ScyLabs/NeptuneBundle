<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 28/08/2019
 * Time: 09:46
 */

namespace ScyLabs\NeptuneBundle\Services;


use ScyLabs\NeptuneBundle\Entity\ZoneType;
use ScyLabs\NeptuneBundle\Model\CodexImporterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CodexImporter implements CodexImporterInterface
{
    private $parameterBag;

    private $tmpFile;
    private $tmpZipExtractDir;

    public function __construct(ParameterBagInterface $parameterBag) {
        $this->parameterBag = $parameterBag;
    }

    private function getParameter(string $param){
        return $this->parameterBag->get($param);
    }

    public function copyDir(string $source,string $destination){
        if(!is_dir($source))
            return false;

        if(!file_exists($destination))
            mkdir($destination);

        foreach (scandir($source) as $object){
            if($object === '..' || $object === '.')
                continue;
            $objectPath = $source.'/'.$object;
            if(is_dir($objectPath)){
                $this->copyDir($objectPath,$destination.'/'.$object);
            }else{
                copy($source.'/'.$object,$destination.'/'.$object);
            }


        }
    }
    public function rmdir(string $dir){
        if (is_dir($dir)) { // si le paramètre est un dossier
            $objects = scandir($dir); // on scan le dossier pour récupérer ses objets
            foreach ($objects as $object) { // pour chaque objet
                if ($object != "." && $object != "..") { // si l'objet n'est pas . ou ..
                    if (filetype($dir."/".$object) == "dir") $this->rmdir($dir."/".$object);
                    else unlink($dir."/".$object); // on supprime l'objet
                }
            }
            reset($objects); // on remet à 0 les objets
            rmdir($dir); // on supprime le dossier
        }
    }
    public function clearTemp(){
        if(file_exists($this->tmpFile))
            unlink($this->tmpFile);
        if(is_dir($this->tmpZipExtractDir)){
            $this->rmDir($this->tmpZipExtractDir);
        }
    }
    private function changeColors($zone,$colors){
        $cssFile = $this->tmpZipExtractDir.'/public/css/zone/'.$zone->getName().'.less';
        if(file_exists($cssFile)){
            $css = file_get_contents($cssFile);

            foreach ($colors as $key => $color){
                $css = str_replace($key,$color,$css);
                $css = preg_replace('/'.$key.'/Ui',$color,$css);

            }
            $f = fopen($cssFile,'w+');
            fwrite($f,$css);
            fclose($f);
        }

    }
    public function import(array $dataFile,ZoneType $zone,array $colors){

        $rootDir = $this->getParameter('kernel.project_dir');

        $tmpDir = $rootDir.'/var/tmp';
        $this->tmpFile = $tmpDir.'/'.$dataFile['hash'];
        if(!file_exists($tmpDir)){
            mkdir($tmpDir);
        }
        $f = fopen($this->tmpFile,'w+');
        fwrite($f,base64_decode($dataFile['content']));
        fclose($f);
        if(hash_file('sha1',$this->tmpFile) !== $dataFile['hash']){
            $this->clearTemp();
            throw new BadRequestHttpException('Une erreur est survenue lors du transfert du fichier');
        }
        $this->tmpZipExtractDir = $tmpDir.'/'.hash('sha1',time());
        $zip = new \ZipArchive();
        $zip->open($this->tmpFile);
        $zip->extractTo($this->tmpZipExtractDir);
        $zip->close();
        $this->renameFiles($this->tmpZipExtractDir,$zone->getName());
        $this->changeColors($zone,$colors);
        $this->copyDir($this->tmpZipExtractDir.'/public',$rootDir.'/public');
        $this->copyDir($this->tmpZipExtractDir.'/templates',$rootDir.'/templates');
        $this->clearTemp();
    }
    public function renameFiles(string $path,string $slug){
        if(!is_dir($path))
            return false;
        foreach (scandir($path) as $object){
            if($object === '..' || $object === '.')
                continue;
            $objectPath = $path.'/'.$object;
            if(is_dir($objectPath)){
                $dirExp = explode('/',$objectPath);
                if($slug !== $object && $dirExp[sizeof($dirExp) -3] === 'img'){
                    rename($objectPath,$path.'/'.$slug);
                }
                $this->renameFiles($objectPath,$slug);
            }
            else{
                $nameExp = explode('.',$object);
                if($slug !== $nameExp[0] && in_array($nameExp[sizeof($nameExp) -1 ],['js','css','twig','less'])){
                    $nameExp[0] = $slug;
                    rename($objectPath,$path.'/'.implode('.',$nameExp));
                }
            }
        }
    }
}