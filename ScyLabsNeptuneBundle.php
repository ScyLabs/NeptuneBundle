<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01/08/2018
 * Time: 09:46
 */

namespace ScyLabs\NeptuneBundle;


use ScyLabs\NeptuneBundle\DependencyInjection\Compiler\OverrideAnnotationPass;
use ScyLabs\NeptuneBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use ScyLabs\NeptuneBundle\Manager\HookManager;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use ScyLabs\NeptuneBundle\DependencyInjection\ScyLabsNeptuneExtension;

class ScyLabsNeptuneBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ScyLabsNeptuneExtension();
    }

    public function build(ContainerBuilder $container){
        parent::build($container);
        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass(),PassConfig::TYPE_BEFORE_OPTIMIZATION,1000);


    }
}