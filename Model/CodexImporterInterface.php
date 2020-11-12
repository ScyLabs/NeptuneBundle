<?php

namespace ScyLabs\NeptuneBundle\Model;

use ScyLabs\NeptuneBundle\Entity\ZoneType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

interface  CodexImporterInterface{
    public function copyDir(string $source,string $destination);
    public function rmdir(string $dir);
    public function clearTemp();
    public function import(array $dataFile,ZoneType $zone,array $colors);
    public function renameFiles(string $path,string $slug);
}