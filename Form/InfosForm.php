<?php

namespace ScyLabs\NeptuneBundle\Form;

use ScyLabs\NeptuneBundle\Entity\Element;

use ScyLabs\NeptuneBundle\Entity\ElementType;

use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Repository\ElementTypeRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class InfosForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name',TextType::class,[
                'label'=>'Nom',
                'required'=>false,
            ])
            ->add('adress',TextType::class,[
                'label'=>'Adresse',
                'required'=>false,
            ])
            ->add('cp',NumberType::class,[
                'label'=>'Code postal',
                'required'=>false,
            ])
            ->add('city',TextType::class,[
                'label'=>'Ville',
                'required'=>false,
            ])
            ->add('mail', EmailType::class,[
                'label'=>'E-mail',
                'required'=>false,
            ])
            ->add('tel',TelType::class,[
                'label'=>'Tel',
                'required'=>false,
            ])
            ->add('mobile',TelType::class,[
                'label'=>'Portable',
                'required'=>false,
            ])
            ->add('Iframe GoogleMaps',TextareaType::class,[
                'label'=>'gmap',
                'required'=>false,
            ])
            ->add('facebook',TextType::class,[
                'label'=>'Url Facebook',
                'required'=>false,
            ])
            ->add('twitter',TextType::class,[
                'label'=>'Url Twitter',
                'required'=>false,
            ])
            ->add('insta',TextType::class,[
                'label'=>'Url Instagram',
                'required'=>false,
            ])
            ->add('Valider',SubmitType::class,[
                'label'=>'Envoyer'
            ])

        ;



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => null,
            'data_class' => Infos::class,
        ])
        ;
    }
}
