<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/09/2019
 * Time: 09:02
 */

namespace ScyLabs\NeptuneBundle\Model;


use ScyLabs\NeptuneBundle\Entity\ZoneType;

interface CodexExporterInterface
{
    public function getBase64ZipZoneFile(ZoneType $zoneType) : array;

}