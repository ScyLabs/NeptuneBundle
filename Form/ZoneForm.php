<?php

namespace ScyLabs\NeptuneBundle\Form;

use ScyLabs\NeptuneBundle\Controller\Admin\PageController;
use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageType;
use ScyLabs\NeptuneBundle\Entity\Zone;
use ScyLabs\NeptuneBundle\Entity\ZoneType;
use ScyLabs\NeptuneBundle\Repository\PageRepository;

use ScyLabs\NeptuneBundle\Repository\PageTypeRepository;
use ScyLabs\NeptuneBundle\Repository\ZoneTypeRepository;
use Doctrine\ORM\MScyLabs\NeptuneBundleing\Entity;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
class ZoneForm extends AbstractType
{

    private $container;
    public function  __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('name',TextType::class,[
                'label'=>'Nom de la zone'
            ]);

        if($options['action'] !== null){
            $builder->setAction($options['action']);
        }
        $builder->add('type',EntityType::class,[
            'label'         => 'Type de zone',
            'class'         => ZoneType::class,
            'choice_label'  => 'title',
            'query_builder' =>  function(ZoneTypeRepository $r){
                return $r->createQueryBuilder('t')
                    ->where('t.remove = 0');
            },
        ])
            ->add('subType',ChoiceType::class,array(
                'choices'=> array(
                    'Type 1'    => 'subtype1',
                    'Type 2'    => 'subtype2',
                    'Type 3'    => 'subtype3'
                )
            ));
        if(in_array('ROLE_SUPER_ADMIN',$options['roles'])){
            $builder->add('typeHead',ChoiceType::class,array(
                'choices'=> array(
                    'H2'    => 2,
                    'H3'    => 3,
                    'H4'    => 4,
                    'H5'    => 5,
                    'H6'    => 6,
                )
            ));
        }

        if(null !== $icons = $this->container->getParameter('scy_labs_neptune.icons')){
            if(is_array($icons)){
                $choices = array('Aucune'=>'');
                foreach ($icons as $key => $icon){

                    $choices[$key] = $key;
                }
                $builder->add('icon',ChoiceType::class,array(
                    'label' => "Icone",
                    "choices"   => $choices,
                    'required'  => false

                ));
            }
        }else{
            $builder->add('icon',TextType::class,array(
                'required'=>false,
                'label'=> 'Icone'
            ));
        }

        $builder->add('pageLink',EntityType::class,array(
            'label' => 'Page liée (PageLink)',
            'class' => Page::class,
            'choice_label'      => 'name',
            'required'  => false,
            'query_builder'     => function(PageRepository $r){
                return $r->createQueryBuilder('p')
                    ->where('p.remove = 0');
            }
        ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event){
            $zone = $event->getData();

            if(null === $zone){
                return;
            }

            if($zone->getType() !== null){

                $event->getForm()->add('subType',ChoiceType::class,array(
                    'choices'    => array(
                        $zone->getType()->getTitle().' - Type 1'    => 'subtype1',
                        $zone->getType()->getTitle().' - Type 2'    => 'subtype2',
                        $zone->getType()->getTitle().' - Type 3'    => 'subtype3'
                    )
                ));
            }

            if($zone->getPage() !== null){
                $event->getForm()->add('page',HiddenType::class,[
                    'property_path' => 'page.name',
                ]);
            }
            if($zone->getElement() !== null){
                $event->getForm()->add('element',HiddenType::class,[
                    'property_path' =>  'element.name',
                ]);
            }
        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => null,
            'data_class' => Zone::class,
            'roles'     => ['ROLE_ADMIN'],
        ])
        ;
    }
}
