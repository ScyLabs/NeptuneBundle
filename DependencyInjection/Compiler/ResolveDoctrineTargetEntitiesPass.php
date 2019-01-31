<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 29/01/2019
 * Time: 17:03
 */

namespace ScyLabs\NeptuneBundle\DependencyInjection\Compiler;


use ScyLabs\NeptuneBundle\DependencyInjection\DoctrineTargetEntitiesResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResolveDoctrineTargetEntitiesPass implements CompilerPassInterface
{
    public function __construct(){
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container){

        $resolver = new DoctrineTargetEntitiesResolver();
        $resolver->resolve($container);

    }
}