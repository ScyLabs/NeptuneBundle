<?php

namespace Scylabs\NeptuneBundle\Form;

use Scylabs\NeptuneBundle\Controller\Admin\PageController;
use Scylabs\NeptuneBundle\Entity\Page;
use Scylabs\NeptuneBundle\Entity\PageType;
use Scylabs\NeptuneBundle\Repository\PageRepository;

use Scylabs\NeptuneBundle\Repository\PageTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
class PageForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name',TextType::class,[
                'label'=>'Nom de la page'
            ]);

            if($options['action'] !== null){
                $builder->setAction($options['action']);
            }
            $builder->add('parent',EntityType::class,[
                'label'=> 'Parent',
                'class'         =>  Page::class,
                'choice_label'  => 'name',
                'multiple'      =>  false,
                'required'      => false,
                'query_builder' =>  function(PageRepository $r){
                    return $r->createQueryBuilder('p')
                        ->where('p.remove = 0');
                }]
            )
            ->add('type',EntityType::class,[
                'label'         => 'Type de page',
                'class'         => PageType::class,
                'choice_label'  => 'title',
                'query_builder' =>  function(PageTypeRepository $r){
                    return $r->createQueryBuilder('t')
                        ->where('t.remove = 0');
                },
            ])
            ->add('submit',SubmitType::class,[
                'label' => 'Envoyer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => null,
            'data_class' => Page::class,
        ])
        ;
    }
}
