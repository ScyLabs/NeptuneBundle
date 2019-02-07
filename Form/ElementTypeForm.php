<?php

namespace ScyLabs\NeptuneBundle\Form;

use ScyLabs\NeptuneBundle\Entity\ElementType;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Repository\PageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['action'] !== null){
            $builder->setAction($options['action']);
        }
        $builder
            ->add('name')
            ->add('title')
            ->add('page',EntityType::class,[
                'label'         => 'Liée à la page',
                'class'         => Page::class,
                'choice_label'  => 'name',
                'required'      => false,
                'query_builder' =>  function(PageRepository $r){
                    return $r->createQueryBuilder('t')
                        ->where('t.remove = 0');
                },
            ])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action'     => null,
            'data_class' => ElementType::class,
            'roles'     => ['ROLE_ADMIN']
        ]);
    }
}
