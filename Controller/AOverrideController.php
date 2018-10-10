<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 09/10/2018
 * Time: 11:02
 */

namespace ScyLabs\NeptuneBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AOverrideController extends Controller
{
    /**
     * @Route("/admin/user")
     */
    public function userList(){
        return $this->forward(UserController::class."::listingAction");
    }
    /**
     * @Route("/admin/user/add")
     */
    public function userAdd(){
        return $this->forward(UserController::class."::addAction");
    }
    /**
     * @Route("/admin/user/active/{id}")
     */
    public function UserSitchActive($id){
        return $this->forward(UserController::class."::switchActiveAction",array(
            'id'    => $id
        ));
    }

}