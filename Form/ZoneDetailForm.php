<?php

namespace App\ScyLabs\NeptuneBundle\Form;

use App\ScyLabs\NeptuneBundle\Entity\ZoneDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZoneDetailForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,array(
                'required'=> false
            ))
            ->add('description',TextareaType::class,array(
                'required'=> false
            ))
            ->add('title2',TextType::class,array(
                'required'=> false
            ))
            ->add('description2',TextareaType::class,array(
                'required'=> false
            ))
            ->add('Valider',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ZoneDetail::class,
        ]);
    }
}
