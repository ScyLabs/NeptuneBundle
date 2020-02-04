<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 30/01/2020
 * Time: 15:08
 */

namespace ScyLabs\NeptuneBundle\Model;


interface ClassFounderInterface
{
    public function getClass(string $alias);


}