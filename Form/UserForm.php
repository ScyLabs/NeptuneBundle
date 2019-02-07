<?php

namespace ScyLabs\NeptuneBundle\Form;

use ScyLabs\NeptuneBundle\Entity\User;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Repository\PageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['action'] !== null){
            $builder->setAction($options['action']);
        }
        $builder
            ->add('name',TextType::class,array(
                'label'     => 'Nom',
                'required'  => false
            ))
            ->add('firstname',TextType::class,array(
                'label'     => 'Prénom',
                'required'  => false
            ))
            ->add('username',RepeatedType::class,array(
                'type'              => EmailType::class,
                'invalid_message'   => 'Les 2 emails ne correspondent pas',
                'required'          => true,
                'first_options'     => array('label'    =>  'E-mail','attr'=>array('class'=>'form-control')),
                'second_options'    => array('label'    =>  'Vérification de l\'E-mail','attr'=>array('class'=>'form-control')),


            ))
            ->add('tmpRole',ChoiceType::class,array(
                'label'     => 'Rôle',


                'choices'   => array(
                    'Utilisateur'       =>  'ROLE_USER',
                    'Administrateur'    =>  'ROLE_ADMIN' ,
                ),

            ))
            ->add('address',TextType::class,array(
                'label'     => 'Adresse',
                'required'  => false
            ))
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action'     => null,
            'data_class' => User::class,
            'roles'     => ['ROLE_ADMIN']
        ]);
    }
}
