<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/09/2019
 * Time: 09:13
 */

namespace ScyLabs\NeptuneBundle\Services;


use ScyLabs\NeptuneBundle\Entity\ZoneType;
use ScyLabs\NeptuneBundle\Model\CodexExporterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CodexExporter implements CodexExporterInterface
{
    private $parameterBag;

    private $tmpFile;

    public function __construct(ParameterBagInterface $parameterBag) {
        $this->parameterBag = $parameterBag;

    }
    private function getParameter(string $string){
        return $this->parameterBag->get($string);
    }

    public function getBase64ZipZoneFile(ZoneType $zoneType): array {
        $projectRoot = $this->getParameter('kernel.project_dir');
        $zip = new \ZipArchive();
        $tmpDir = $projectRoot.'/var/tmp';
        $this->tmpFile = $tmpDir.'/'.hash('sha1',time());

        $zip->open($this->tmpFile,\ZipArchive::CREATE);
        $zoneFiles = [
            'templates/zone/'.$zoneType->getName().'.html.twig' =>  $projectRoot.'/templates/zone/'.$zoneType->getName().'.html.twig',
            'public/css/zone/'.$zoneType->getName().'.less'     =>  $projectRoot.'/public/css/zone/'.$zoneType->getName().'.less',
            'public/js/zone/'.$zoneType->getName().'.js'        =>  $projectRoot.'/public/js/zone/'.$zoneType->getName().'.js',
            'public/img/zone/'.$zoneType->getName()             =>  $projectRoot.'/public/img/zone/'.$zoneType->getName()
        ];
        foreach ($zoneFiles as $key => $zoneFile){
            if(file_exists($zoneFile)){
                if(is_dir($zoneFile)){
                    $this->addDirToZip($zoneFile,$zip,'/public/img/zone/'.$zoneType->getName());
                    continue;
                }
                $zip->addFile($zoneFile,$key);
            }
        }
        $zip->close();
        return [
            'hash'      =>  hash_file('sha1',$this->tmpFile),
            'content'   =>  base64_encode(file_get_contents($this->tmpFile))
        ];

    }
    public function clearTmp(){

    }
    private function addDirToZip(string $dir,\ZipArchive &$zip,$zipPath = ''){
        if(!is_dir($dir)){
            return false;
        }
        foreach (scandir($dir) as $object){
            if($object === '.' || $object === '..')
                continue;
            if(is_dir($dir.'/'.$object)){
                $this->addDirToZip($dir.'/'.$object,$zip,$zipPath.'/'.$object);
            }else{
                $zip->addFile($dir.'/'.$object,$zipPath.'/'.$object);
            }
        }
    }
}