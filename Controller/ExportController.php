<?php

namespace ScyLabs\NeptuneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Doctrine\Common\Collections\ArrayCollection;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractAvancedDetail;
use ScyLabs\NeptuneBundle\Entity\ZoneDetail;
use ScyLabs\NeptuneBundle\Model\NotCompressedInterface;
use Symfony\Contracts\Translation\TranslatorInterface as TranslationTranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
class ExportController extends BaseController implements NotCompressedInterface
{

    private $translator;
    private $choices;

    public function __construct(TranslationTranslatorInterface $translator){
        $this->translator = $translator;
        $this->choices = array(
            'Elements'      => 'elementDetail',
            'Photos'        => 'photoDetail',
            'Pages'         => 'pageDetail',
            'Partenaires'   => 'partnerDetail',
            'Contenu'       => 'zoneDetail',
            'Documents'     => 'documentDetail', 
        );
    }
    

    /**
     * @Route("/export/text",name="neptune_export_text")
     */
    public function exportText(Request $request){
        
       
        $langs = $this->getParameter('langs');
        $finalLangs = array();
        foreach($langs as $lang){
            $finalLangs[$this->translator->trans($lang)] = $lang;
        }

        $form = $this->createFormBuilder([])->setMethod('post')

            ->setAction($this->generateUrl('neptune_export_text'))
            ->add('lang',ChoiceType::class,array(
                'mapped'    =>  false,
                'required'  => true,
                'label' => 'Quelle langue voulez vous exporter ?',
                'multiple'  => false,
                'expanded'  => true,
                'choices'   => $finalLangs
            ))
            ->add('choices',ChoiceType::class,array(
                'mapped'    => false,
                'required'  => false,
                'multiple'  => true,
                'expanded'  => true,
                'label' => 'Que voulez vous exporter ? Ne rien cocher pour tout prendre',
                'choices'   => $this->choices
            ))
            ->add('envoyer',SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $data = $request->get('form');
            
            $doctrine = $this->getDoctrine();

            $f = fopen('php://temp','rw');
            $result = array();
            
            $choices = (isset($data['choices']))? $data['choices'] : $this->choices;
            
            foreach($choices as $choice){

                $repo = $doctrine->getRepository($this->getClass($choice));
                $objects = $repo->findByLangAndParentIsActiveAndNotRemoved($data['lang']);
                
                
                fputcsv($f,[$choice],';');
                foreach($objects as $object){
                    
                    $line = array();
                    
                    $line[] = $object->getParent()->getId();
                    $line[] = $object->getName();
                    $line[] = $object->getTitle();
                    $line[] = $object->getDescription();

                    if($object instanceof ZoneDetail){
                        $line[] = $object->getTitle2();
                        $line[] = $object->getDescription2();
                        
                        $line[] = $object->getTitle3();
                        $line[] = $object->getDescription3();
                        
                        $line[] = $object->getTitle4();
                        $line[] = $object->getDescription4();


                    }

                    if($object instanceof AbstractAvancedDetail){
                        $line[] = $object->getMetaTitle();
                        $line[] = $object->getMetaDesc();
                        $line[] = $object->getMetaKeys();
                    }
                    foreach($line as $key => $val){

                        $val = preg_replace("/(\n|\r)+/Ui"," ",strip_tags($val));
                        $line[$key] = html_entity_decode($val);
                        
                    }
                    
                    fputcsv($f,$line,';');
                }
                
            }
            rewind($f);
            $result = stream_get_contents($f);
            
            
        
            fclose($f);
        
            //return new Response('<html><body></body></html>');
            return new Response($result,200,array(
                'Content-type' => 'application/csv',
                'Content-Disposition' => 'attachment; filename="export.csv"'
            ));
            
            
        }
        $params = array(
            'title' => 'Export des textes du site',
            'form' => $form->createView()
        );
        return $this->render('@ScyLabsNeptune/admin/export/export.html.twig',$params);
    }
}