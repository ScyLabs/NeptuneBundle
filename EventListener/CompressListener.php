<?php

namespace ScyLabs\NeptuneBundle\EventListener;

use ScyLabs\NeptuneBundle\Model\NotCompressedInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class CompressListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    private $routeNotCompress;

    public function __construct() {
     

    }

    public function onKernelController(ControllerEvent $event){

        if($this->container->getParameter('scy_labs_neptune.compress') == false ||  $_ENV['APP_ENV'] != 'prod'){
            return;
        }
    
        $request = $event->getRequest();
        $route = $request->get('_route');
        
        $controller = get_class($event->getController()[0]);
        $controllerImplements = class_implements($controller);
        
        if(array_key_exists(NotCompressedInterface::class,$controllerImplements))
            return;

        ob_start(RequestListener::class."::ob_html_compress");

    }
    public static function ob_html_compress($buf){return preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/"),array('',' '),str_replace(array("\n","\r","\t"),'',$buf)); }
}