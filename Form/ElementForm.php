<?php

namespace ScyLabs\NeptuneBundle\Form;

use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageType;
use ScyLabs\NeptuneBundle\Entity\ElementType;
use ScyLabs\NeptuneBundle\Repository\PageRepository;

use ScyLabs\NeptuneBundle\Repository\PageTypeRepository;
use ScyLabs\NeptuneBundle\Repository\ElementTypeRepository;
use Doctrine\ORM\Mapping\Entity;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ElementForm extends AbstractType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('creationDate',DateType::class,array(
                'label'     => 'Date',
                'html5'     =>  true,
                'widget'    =>  'single_text'
            ))
            ->add('name',TextType::class,[
                'label'=>'Nom de la Element'
            ])
            ->add('price',NumberType::class,[
                'label'=>'Price',
                'required'  => false,
                'attr'      =>  [
                    'class' =>  'price'
                ],
                'constraints'   =>  [
                    new PositiveOrZero()
                ]
            ]);;

        if($options['action'] !== null){
            $builder->setAction($options['action']);
        }
        $builder->add('type',EntityType::class,[
            'label'         => 'Type de Element',
            'class'         => ElementType::class,
            'choice_label'  => 'title',
            'query_builder' =>  function(ElementTypeRepository $r){
                return $r->createQueryBuilder('t')
                    ->where('t.remove = 0');
            },
        ]);

        // if(null !== $icons = $this->container->getParameter('scy_labs_neptune.icons')){
        //     if(is_array($icons)){
        //         $choices = array('Aucune'=>'');
        //         foreach ($icons as $key => $icon){

        //             $choices[$key] = $key;
        //         }
        //         $builder->add('icon',ChoiceType::class,array(
        //             'label' => "Icone",
        //             "choices"   => $choices,
        //             "required"  => false

        //         ));
        //     }
        // }else{
        //     $builder->add('icon',TextType::class,array(
        //         'required'=>false,
        //         'label'=> 'Icone'
        //     ));
        // }


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => null,
            'data_class' => Element::class,
            'roles'     => ['ROLE_ADMIN']
        ]);
    }
}
