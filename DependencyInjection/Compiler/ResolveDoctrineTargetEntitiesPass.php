<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 29/01/2019
 * Time: 17:03
 */

namespace ScyLabs\NeptuneBundle\DependencyInjection\Compiler;


use ScyLabs\NeptuneBundle\DependencyInjection\DoctrineTargetEntitiesResolver;
use ScyLabs\NeptuneBundle\Manager\HookManager;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ResolveDoctrineTargetEntitiesPass implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container){
        
        $resolver = new DoctrineTargetEntitiesResolver();
        $resolver->resolve($container);
    }
}