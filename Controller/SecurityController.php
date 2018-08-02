<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 14/06/2018
 * Time: 17:11
 */

namespace ScyLabs\NeptuneBundle\Controller;


use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class SecurityController extends Controller
{

    private $eventDispatcher;
    private $formFactory;
    private $userManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, UserManagerInterface $userManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->userManager = $userManager;

    }
    /**
     * @Route("/check_redirect",name="check_redirect")
     */
    public function checkRedirectAction(AuthorizationCheckerInterface $authChecker){

        if(!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        if($this->isGranted('ROLE_ADMIN')){

            if($this->getUser()->getFirstConnexion() == true){
                return $this->redirectToRoute('security_first_connexion');
            }
            return $this->redirectToRoute('admin_home');
        }else{
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @Route("/first-connexion",name="security_first_connexion")
     */
    public function firstConnexionAction(Request $request){

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createFormBuilder($user)
            ->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array(
                'translation_domain' => 'FOSUserBundle',
                'attr' => array(
                    'autocomplete' => 'new-password',
                ),
            ),
            'constraints' => array(
                new NotBlank(),
                new Regex(
                    [
                        "pattern"   => '#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$#', //(?=.*\W) Carractères spéciaux
                        "message"    => "Le mot de passe doit être long d'au moins 8 carractères et contenir : Une majuscule , Une minuscule , Un chiffre"
                    ])
            ),
            'first_options' => array('label' => 'form.new_password'),
            'second_options' => array('label' => 'form.new_password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
        ))->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setFirstConnexion(false);
            $this->userManager->updateUser($user);

            return $this->redirectToRoute('admin_home');
        }

        return $this->render('@ScyLabsNeptune/security/first_connexion.html.twig', array(
            'form'      => $form->createView(),
            'title'     => "C'est votre première connexion , définissez votre mot de passe."
        ));

    }

}