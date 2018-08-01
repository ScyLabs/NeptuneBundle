<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01/08/2018
 * Time: 09:46
 */

namespace Scylabs\NeptuneBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;
use Scylabs\NeptuneBundle\DependencyInjection\ScyLabsNeptuneExtension;

class ScyLabsNeptuneBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ScyLabsNeptuneExtension();
    }
}