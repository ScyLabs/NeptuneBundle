<?php

namespace Scylabs\NeptuneBundle\Form;

use Scylabs\NeptuneBundle\Controller\Admin\PageController;
use Scylabs\NeptuneBundle\Entity\Element;
use Scylabs\NeptuneBundle\Entity\Page;
use Scylabs\NeptuneBundle\Entity\PageType;
use Scylabs\NeptuneBundle\Entity\Zone;
use Scylabs\NeptuneBundle\Entity\ZoneType;
use Scylabs\NeptuneBundle\Repository\PageRepository;

use Scylabs\NeptuneBundle\Repository\PageTypeRepository;
use Scylabs\NeptuneBundle\Repository\ZoneTypeRepository;
use Doctrine\ORM\MScylabs\NeptuneBundleing\Entity;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('submit',SubmitType::class,[
                'label' => 'Envoyer'
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA,function(FormEvent $event){
            $zone = $event->getData();
            
            if(null === $zone){
                return;
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
        ])
        ;
    }
}
