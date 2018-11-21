<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 21/11/2018
 * Time: 14:06
 */

namespace ScyLabs\NeptuneBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /*
     * @Route("/admin", name="admin_home")
     */
    public function indexAction(){

        return $this->render('@ScyLabsNeptune/admin/index.html.twig');
    }
}