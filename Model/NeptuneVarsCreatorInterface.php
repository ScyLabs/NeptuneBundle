<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/09/2019
 * Time: 15:00
 */

namespace ScyLabs\NeptuneBundle\Model;


use ScyLabs\NeptuneBundle\Entity\ZoneType;

interface NeptuneVarsCreatorInterface
{
    public function initVars(ZoneType $zoneType): array;
}