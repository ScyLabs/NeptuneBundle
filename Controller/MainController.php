<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 21/11/2018
 * Time: 14:06
 */

namespace ScyLabs\NeptuneBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    /**
     * @Route("/",name="neptune_home")
     */
    public function indexAction(){

        return $this->render('@ScyLabsNeptune/admin/index.html.twig');
    }
    /**
     * @Route("/changelogs",name="neptune_changelogs")
     */
    public function changelogs(Request $request){

        $changelogs = $this->getParameter('changelogs');

        if($request->cookies->get('changelogs-'.str_replace('.','_',$changelogs['version'])) !== null || $changelogs['active'] === false){
            return $this->json(array(
                    'active'    =>  false
                ));
        }
        $response = $this->render('@ScyLabsNeptune/admin/changelogs.json.twig',array('changelogs' => $this->getParameter('changelogs')));
        $response->headers->set('Content-type','application/json');

        return $response;
    }
    /**
     * @Route("/changelogs/{version}",name="neptune_changelog_cookie")
     */
    public function setCookieChangelogs($version){

        $response = new RedirectResponse($this->generateUrl('neptune_home'));
        $response->headers->setCookie(new Cookie('changelogs-'.$version,true,strtotime('now + 1 year')));
        $response->headers->set('Content-type','application/json');
        return $response;
    }
}