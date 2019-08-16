<?php

namespace ScyLabs\NeptuneBundle\Form;


use ScyLabs\NeptuneBundle\Entity\ElementType;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Repository\PageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElementTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['action'] !== null){
            $builder->setAction($options['action']);
        }
        $builder
            ->add('name',TextType::class)
            ->add('title',TextType::class)
            ->add('page',EntityType::class,[
                'label'         => 'Liée à la page',
                'class'         => Page::class,
                'choice_label'  => 'name',
                'required'      => false,
                'query_builder' =>  function(PageRepository $r){
                    return $r->createQueryBuilder('t')
                        ->where('t.remove = 0')
                        ->andWhere('t.active = 1')
                        ;
                },
            ])
            ->add('submit',SubmitType::class)
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event){

            $elementType = $event->getData();
            $form = $event->getForm();
            if(null === $elementType ){
                return;
            }
            if( ! $elementType instanceof ElementType){
                return;
            }
            if($elementType->getRemovable() === false){
                $form
                    ->add('name',TextType::class,array(
                        'disabled'  =>  true
                    ))
                    ->add('title',TextType::class,array(
                        'disabled'  =>  true
                    ))
                ;
            }


        });
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
