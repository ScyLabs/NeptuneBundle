<?php
/**
 * Created by PhpStorm.
 * User: assis
 * Date: 09/05/2019
 * Time: 09:18
 */

namespace ScyLabs\NeptuneBundle\Controller;



use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageDetail;
use ScyLabs\NeptuneBundle\Entity\Partner;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

class TwigExceptionController implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    public function __construct(Environment $twig, bool $debug){
    
        parent::__construct($twig, $debug);
    }

    public function showException(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null){
        if($this->debug == false){

            return new RedirectResponse(
                $this->container->get('router')->generate('homepage',array(
                    '_locale' => $request->getLocale()
                )),
                301);
        }
        return parent::showAction($request,$exception,$logger);
    }
}