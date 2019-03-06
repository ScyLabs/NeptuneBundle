<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 21/11/2018
 * Time: 14:06
 */

namespace ScyLabs\NeptuneBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MainController extends Controller
{

    public function indexAction(){

        return $this->render('@ScyLabsNeptune/admin/index.html.twig');
    }
    public function changeLogsAction(){

        $response = $this->render('@ScyLabsNeptune/admin/changes.json.twig');
        $response->headers->set('Content-type','application/json');

        return $response;

    }
}