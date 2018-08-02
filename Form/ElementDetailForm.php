<?php

namespace ScyLabs\NeptuneBundle\Form;

use ScyLabs\NeptuneBundle\Entity\ElementDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementDetailForm extends AbstractType
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
            ->add('name',TextType::class,array(
                'required'=> false
            ))
            ->add('h1',TextType::class,array(
                'required'=> false
            ))
            ->add('metaTitle',TextType::class,array(
                'required'=> false
            ))
            ->add('metaDesc',TextareaType::class,array(
                'required'=> false
            ))
            ->add('metaKeys',TextareaType::class,array(
                'required'=> false
            ))
            ->add('Valider',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ElementDetail::class,
        ]);
    }
}
