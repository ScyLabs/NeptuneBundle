<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 08/10/2018
 * Time: 09:10
 */

namespace ScyLabs\NeptuneBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ScyLabs\NeptuneBundle\Entity\Admin;
use ScyLabs\NeptuneBundle\Repository\AdminRepository;
use ScyLabs\NeptuneBundle\Form\AdminCreationForm;
use ScyLabs\NeptuneBundle\Model\PasswordGeneratorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class UserController extends BaseController
{

    /**
     * @Route("/user",name="neptune_user")
     */
    public function list(Request $request){

        $class = $this->getClass('admin');
        $repo = $this->getDoctrine()->getRepository($class);
        $users = $repo->findAll();

        $params = array(
            'users' => $users
        );
        $params['ariane'] = array(
        [
            'link'  =>  $this->generateUrl('neptune_home'),
            'name'  => 'Accueil'
        ],
        [
            'link'  =>  '#',
            'name'  => 'Utilisateurs'
        ]);

        return $this->render('@ScyLabsNeptune/admin/user/listing.html.twig',$params);

    }


    /**
     * @Route("/user/active/{id}",name="neptune_user_active",requirements={"type"="[a-z]{2,20}","id"="[0-9]+"})
     */
    public function switchActive(Request $request,$id,AdminRepository $adminRepository){

        $em = $this->getDoctrine()->getManager();
       
        $admin = $adminRepository->find($id);


        if(!$admin instanceof User){
            $this->redirectToRoute('neptune_user');
        }

        $referer = $request->headers->get('referer');

        if($admin->hasRole('ROLE_SUPER_ADMIN')){
            return $this->redirect($referer);
        }

        $admin->setEnable(!$admin->getEnable());
        $em->persist($admin);
        $em->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route("/user/add",name="neptune_user_add")
     */
    public function add(Request $request,\Swift_Mailer $mailer,Environment $templating,PasswordGeneratorInterface $passwordGenerator,UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $entityManager){

        
        $object = new Admin();
        $params = array();

        $route = $this->generateUrl('neptune_user_add');
        $form = $this->createForm(AdminCreationForm::class,$object,['action'=>$route]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $admin = $form->getData();

            if(null !== $this->getDoctrine()->getRepository(Admin::class)->findOneBy(['email'  =>  strtolower($admin->getEmail())])){
                
                if($request->isXmlHttpRequest()){
                    $emailForm = $form->get('email');
                    $emailForm->addError(new FormError('Un utilisateur existe déjà avec cet e-mail'));
                
                    return $this->json(array('success'=>false,'errors'  =>   [
                        ['email' =>  $emailForm->getErrors()]
                    ]));
                }
                return $this->redirectToRoute('neptune_user');
            }

            $pass = $passwordGenerator->generate([
                'minCapital'    =>  2,
                'minDigit'      =>  2
            ]);

            $admin->setPassWord($passwordEncoder->encodePassword($admin,$pass));
            $admin->setRoles([
                'ROLE_ADMIN'
            ]);
            $entityManager->persist($admin);
            $entityManager->flush();

            $message = (new \Swift_Message('Création de compte sur le site '.$request->getHttpHost()))
                ->setFrom('web@e-corses.com')
                ->setTo($admin->getEmail())
                ->setBody(
                    $templating->render(
                        '@ScyLabsNeptune/mail/mail_account.html.twig',
                        array(
                            'login' =>  $admin->getEmail(),
                            'pass'  =>  $pass,
                        )
                    )
                    ,'text/html');
            $mailer->send($message);
            
            $this->get('session')->getFlashBag()->add('notice','Votre Utilisateur à bien été ajouté');

            if($request->isXmlHttpRequest()){
                return $this->json(array('success'=>true,'message'=>'Votre utilisateur à bien été ajouté'));
            }
            
            return $this->redirectToRoute('neptune_user');
        }

        $params['form'] = $form->createView();
        $params['title'] = "Ajout d'un utilisateur";



        return $this->render('@ScyLabsNeptune/admin/entity/add.html.twig',$params);

    }
    /**
     * @Route("/admin/delete/{id}",name="neptune_admin_delete",requirements={"id"="[0-9]+"})
     */
    public function delete(Request $request,$id,AdminRepository $adminRepository,EntityManagerInterface $entityManager){
        $admin = $adminRepository->find($id);
        if(null === $admin)
            return $this->redirectToRoute('neptune_home');
        
        $entityManager->remove($admin);
        $entityManager->flush();

        if($request->isXmlHttpRequest()){
            return $this->json(array('success'=>true,'message'=>'Le compte administrateur à bien été supprimé'));
        }
        return $this->redirect($request->headers->get('referer'));
    }


}