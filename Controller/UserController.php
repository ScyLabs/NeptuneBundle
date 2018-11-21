<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 08/10/2018
 * Time: 09:10
 */

namespace ScyLabs\NeptuneBundle\Controller;

use ScyLabs\NeptuneBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /*
     * @Route("admin/user",name="admin_user")
     */
    public function listingAction(Request $request){

        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();

        $params = array(
            'users' => $users
        );
        $params['ariane'] = array(
        [
            'link'  =>  $this->generateUrl('admin_home'),
            'name'  => 'Accueil'
        ],
        [
            'link'  =>  '#',
            'name'  => 'Utilisateurs'
        ]);

        return $this->render('@ScyLabsNeptune/admin/user/listing.html.twig',$params);

    }

    /*
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/admin/user/active/{id}", name="admin_user_active" ,requirements={"type"=EntityController::VALID_ENTITIES,"id"="\d+"})
     */
    public function switchActiveAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $class = $this->getClass('user');
        $user = $em->getRepository($class)->find($id);


        if(null === $user  || ! $user instanceof User){
            $this->redirectToRoute('admin_user');
        }

        $referer = $request->headers->get('referer');
        if($user->hasRole('ROLE_SUPER_ADMIN')){
            return $this->redirect($referer);
        }

        $user->setEnabled(!$user->getEnabled());
        $em->persist($user);
        $em->flush();
        return $this->redirect($referer);
    }


    /*
     * @Route("/admin/user/add", name="admin_user_add")
     */
    public function addAction(Request $request){

        $class = $this->getClass('user',$form);
        $object = new $class();
        $params = array();

        $route = $this->generateUrl('admin_user_add');
        $form = $this->createForm($form,$object,['action'=>$route]);


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $object = $form->getData();
            if(!$object instanceof User){
                return $this->render('@ScyLabsNeptune/admin/entity/add.html.twig',$params);
            }

            $userManager = $this->container->get('fos_user.user_manager');
            $templating = $this->container->get('templating');
            $mailer = $this->container->get('mailer');

            $pass = substr(hash('sha256',random_bytes(10)),0,10);

            $user = $userManager->createUser()
                ->setUsername($object->getUsername())
                ->setEmail($object->getUsername())
                ->setFirstConnexion(true)
                ->setRoles(array($object->getTmpRole()))
                ->setPlainPassword($pass);


            $message = (new \Swift_Message('Création de compte sur le site '.$request->getHttpHost()))
                ->setFrom('web@e-corses.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $templating->render(
                        '@ScyLabsNeptune/mail/mail_account.html.twig',
                        array(
                            'login' =>  $user->getEmail(),
                            'pass'  =>  $pass,
                        )
                    )
                    ,'text/html');
            $mailer->send($message);
            $userManager->updateUser($user);
            $this->get('session')->getFlashBag()->add('notice','Votre Utilisateur à bien été ajouté');

            return $this->redirectToRoute('admin_user');
        }

        $params['form'] = $form->createView();

        return $this->render('@ScyLabsNeptune/admin/entity/add.html.twig',$params);

    }
    

}