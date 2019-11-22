<?php

namespace ScyLabs\NeptuneBundle\Form;

use ScyLabs\NeptuneBundle\Entity\Partner;
use ScyLabs\NeptuneBundle\Entity\PartnerType;
use ScyLabs\NeptuneBundle\Repository\PartnerRepository;

use ScyLabs\NeptuneBundle\Repository\PartnerTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
class PartnerForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name',TextType::class,[
                'label'=>'Nom du partenaire'
            ])
            ->add('url',TextType::class,[
                'label'=>'Site du partenaire',
                'required'=>false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => null,
            'data_class' => Partner::class,
            'roles'     => ['ROLE_ADMIN']
        ])
        ;
    }
}
