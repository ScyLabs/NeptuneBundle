<?php

namespace ScyLabs\NeptuneBundle\EventListener;

use ScyLabs\NeptuneBundle\Model\NotCompressedInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class CompressListener  {

    private $container;
    private $routeNotCompress;
    public function __construct(ContainerInterface $container) {
        $this->container = $container;

        /**
         * @deprecated
         */
        $this->routeNotCompress = ($container->hasParameter('routesNotCompress')) ? $container->getParameter('routesNotCompress') : [];
    }

    public function onKernelController(ControllerEvent $event){

        if($this->container->getParameter('scy_labs_neptune.compress') == false ||  getenv('APP_ENV') != 'prod'){
            return;
        }
    
        $request = $event->getRequest();
        $route = $request->get('_route');
        
        $controller = get_class($event->getController()[0]);
        $controllerImplements = class_implements($controller);
        
        if(array_key_exists(NotCompressedInterface::class,$controllerImplements) || in_array($route,$this->routeNotCompress))
            return;

        ob_start(RequestListener::class."::ob_html_compress");

    }
    public static function ob_html_compress($buf){return preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/"),array('',' '),str_replace(array("\n","\r","\t"),'',$buf)); }
}