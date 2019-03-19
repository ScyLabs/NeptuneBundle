<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19/03/2019
 * Time: 11:38
 */

namespace ScyLabs\NeptuneBundle\EventListener;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    private $container;
    public function __construct(ContainerInterface $container) {
        $this->container = $container;

    }

    public function onKernelRequest(GetResponseEvent $event){

        if(!$event->isMasterRequest()){
            return;
        }

        if($this->container->getParameter('scy_labs_neptune.compress') == false ||  getenv('APP_ENV') != 'prod'){
            return;
        }
        $request = $event->getRequest();
        $route = $request->get('_route');
        if($route == 'generatePhoto')
            return;

         ob_start(RequestListener::class."::ob_html_compress");

    }
    public static function ob_html_compress($buf){return preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/"),array('',' '),str_replace(array("\n","\r","\t"),'',$buf)); }
}