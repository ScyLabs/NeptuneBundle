<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19/11/2019
 * Time: 11:02
 */

namespace ScyLabs\NeptuneBundle\Model;


use Symfony\Component\HttpFoundation\Request;

interface NeptuneFrontVarsInterface
{
    public function getVars(Request $request) : array;
}