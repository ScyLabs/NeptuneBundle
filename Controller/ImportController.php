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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface as TranslationTranslatorInterface;

class ImportController extends BaseController implements NotCompressedInterface
{

    private $translator;

    public function __construct(TranslationTranslatorInterface $translator){
        $this->translator = $translator;
    }
    
    public function importTextAction(Request $request){


        $langs = $this->getParameter('langs');
        $finalLangs = array();
        $em = $this->getDoctrine()->getManager();
        foreach($langs as $lang){
            $finalLangs[$this->translator->trans($lang)] = $lang;
        }

        $form = $this->createFormBuilder([])->setMethod('post')

            ->setAction($this->generateUrl('neptune_import_text'))
            ->add('lang',ChoiceType::class,array(
                'mapped'    =>  false,
                'required'  => true,
                'label' => 'Quelle langue voulez vous importer ?',
                'multiple'  => false,
                'expanded'  => true,
                'choices'   => $finalLangs
            ))
            ->add('file',FileType::class,array(
                'mapped'    =>  false,
                'constraints'   => new File(array(
                    'maxSize'   => '2M',
                    'mimeTypes' => array(
                        'text/csv',
                        'text/plain'
                    ),
                )),
                'attr'  => array(
                    'accept'   => 'text/csv text/plain'
                )
            ))
            ->add('envoyer',SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
        
            $res = $request->files->get('form')['file'];

            $lang = $request->get('form')['lang'];

            $f = fopen($res->getPathName(),'r+');
            $classActive = null;

            while(!feof($f)) {


                $line = fgetcsv($f,0,';');

                if($line === false)
                    break;

                if(is_array($line) && $this->getClass($line[0]) !== null){

                    $classActive = $line[0];
                    continue;
                }

                $class = $this->getClass($classActive);

                if($class === null)
                    continue;
                
                $object = $em->getRepository($class)->findOneByParentAndLang($line[0],$lang) ?? new $class();
                
                if($object->getParent() === null){

                    $parent = $em->getRepository($this->getClass($object->getParentClassName()))->find($line[0]);
                    if($parent === null) continue;
                    $object->setParent($parent);
                    
                }

                $i = 1 ;

                $object->setName($line[$i++]);
                $object->setLang($lang);
                $object->setTitle($line[$i++]);
                $object->SetDescription($line[$i++]);

                if($object instanceof ZoneDetail){
                    $object->setTitle2($line[$i++]);
                    $object->setDescription2($line[$i++]);
                    $object->setTitle3($line[$i++]);
                    $object->setDescription3($line[$i++]);

                    $object->setTitle4($line[$i++]);
                    $object->setDescription4($line[$i++]);
                }

                if($object instanceof AbstractAvancedDetail){
                    $object->setMetaTitle($line[$i++]);
                    $object->setMetaDesc($line[$i++]);
                    $object->setMetaKeys($line[$i++]);
                }

                $em->persist($object);
            }
            
            fclose($f);

            $em->flush();

            if($request->isXmlHttpRequest()){
                return $this->json(array('success'=>true,'message'=>'Vos textes ont bien été importés'));
            }
            return $this->redirectToRoute('neptune_import_text');
        }

        $params = array(
            'title' => 'Importer des textes dans le  site',
            'form' => $form->createView()
        );
        return $this->render('@ScyLabsNeptune/admin/export/export.html.twig',$params);
    }
    
}