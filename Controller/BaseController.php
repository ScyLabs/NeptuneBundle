<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 18/06/2018
 * Time: 11:23
 */

namespace ScyLabs\NeptuneBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractChild;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElem;
use ScyLabs\NeptuneBundle\Entity\Document;
use ScyLabs\NeptuneBundle\Entity\DocumentDetail;
use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\ElementDetail;
use ScyLabs\NeptuneBundle\Entity\ElementType;
use ScyLabs\NeptuneBundle\Entity\FileType;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageDetail;
use ScyLabs\NeptuneBundle\Entity\PageType;
use ScyLabs\NeptuneBundle\Entity\Partner;
use ScyLabs\NeptuneBundle\Entity\PartnerDetail;
use ScyLabs\NeptuneBundle\Entity\Photo;
use ScyLabs\NeptuneBundle\Entity\PhotoDetail;
use ScyLabs\UserBundle\Entity\User;
use ScyLabs\NeptuneBundle\Entity\Video;
use ScyLabs\NeptuneBundle\Entity\Zone;
use ScyLabs\NeptuneBundle\Entity\ZoneDetail;
use ScyLabs\NeptuneBundle\Entity\ZoneType;
use ScyLabs\NeptuneBundle\Form\DocumentDetailForm;
use ScyLabs\NeptuneBundle\Form\ElementDetailForm;
use ScyLabs\NeptuneBundle\Form\ElementForm;
use ScyLabs\NeptuneBundle\Form\ElementTypeForm;
use ScyLabs\NeptuneBundle\Form\FileTypeForm;
use ScyLabs\NeptuneBundle\Form\PageDetailForm;
use ScyLabs\NeptuneBundle\Form\PageForm;
use ScyLabs\NeptuneBundle\Form\PageTypeForm;
use ScyLabs\NeptuneBundle\Form\PartnerDetailForm;
use ScyLabs\NeptuneBundle\Form\PartnerForm;
use ScyLabs\NeptuneBundle\Form\PhotoDetailForm;
use ScyLabs\NeptuneBundle\Form\UserForm;
use ScyLabs\NeptuneBundle\Form\ZoneDetailForm;
use ScyLabs\NeptuneBundle\Form\ZoneForm;
use ScyLabs\NeptuneBundle\Form\ZoneTypeForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

abstract class BaseController extends AbstractController
{




    /* Fonction de génération de formulaire (surement future service)*/

    protected function ajaxFormResult(FormInterface $form){


    }
    protected function validForm($type,$formClass,$object,Request $request,&$form,$action = null){


        $options = ['action'=>$action,'data_class'=>$this->getClass($type)];
        if($this->getUser() !== null){
            $options['roles'] = $this->getUser()->getRoles();
        }
        $form = $this->createForm($formClass,$object,$options);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $object = $form->getData();
            if($object->getid() == null){
                if(null !== $classDetail = $this->getClass($type.'Detail')){
                    $langs = $this->getParameter('langs');
                    if(is_array($langs)){
                        foreach ($langs as $lang){
                            $detail = new $classDetail();
                            $detail->setLang($lang);
                            $detail->setName($object->getName());
                            $object->addDetail($detail);
                        }
                    }
                }
            }
            $em = $this->getDoctrine()->getManager();

            $em->persist($object);
            $em->flush();

            return true;
        }
        if($request->isXmlHttpRequest() && $form->isSubmitted() && !$form->isValid()){
            $result = array('success'   =>  false,'errors'=>new ArrayCollection());

            foreach ($object->toArray() as $data ){
                if($form->has($data))   {
                    $input = $form->get($data);

                    if($input->getErrors()->count() > 0) {

                        $result['errors']->add(array($data => $input->getErrors()));
                    }
                }
            }
            return $result;
        }
        return false;


    }
    public function getEntities(AbstractElem $object,?string $typeParent = null){
        $objects = null;

        $params = array('remove'=>false);
        if($typeParent == null && $object instanceof AbstractChild){
            $typeParent = $object->getParentType();
        }
        if($typeParent !== null){
            $params[$typeParent] = ($object->getParent() === null) ? null : $object->getParent()->getId();
        }
        else{
            if($object instanceof Page)
            $params['parent'] = null;
        }
        $objects =  $this->getDoctrine()->getRepository(get_class($object))->findBy($params,['prio'=>'ASC']);

        return $objects;
    }
    public function getLastPrio(AbstractElem $object,$typeParent = null){
        $prio = - 1;
        
        $params = array('remove'=>false);
        if($typeParent !== null){
            $parentId = ($object->getParent() === null) ? null : $object->getParent()->getId();
            $params[$typeParent] = $parentId;
        }
        $last = $this->getDoctrine()->getRepository(get_class($object))->findOneBy($params,['prio'=>'DESC']);
        if($last !== null)
        {
            $prio = $last->getPrio();
        }

        return $prio;
    }
    public function getAllEntities($class){
        $collection = new ArrayCollection();
        $em  = $this->getDoctrine()->getManager();
        if($class === Page::class){
            return null;
        }
        $pages = $em->getRepository($this->getClass('page'))->findBy(array(
            'remove'=>false,
            'parent'=>null
        ),['prio'=>'ASC']);

        if($pages !== null){
            $collection['Pages'] = $pages;
        }
        if($class !== $this->getClass('zone') && $class !== $this->getClass('element')){
            $zones = $em->getRepository($this->getClass('zone'))->findBy(array(
                'remove'=>false,
            ),['prio'=>'ASC']);
            if($zones !== null){
                $collection['Zones'] = $zones;
            }
        }
        if($class !== $this->getClass('element')){
            $elements = $em->getRepository($this->getClass('element'))->findBy(array(
                'remove'=>false
            ),['prio'=>'ASC']);
            if($elements !== null){
                $collection['Elements'] =$elements;
            }
        }
        return $collection;
    }


    protected function getClass($name,&$form = null){

        $originalClasses = Yaml::parseFile(dirname(__DIR__).'/Resources/config/original_classes.yaml');
        $classes = $this->getParameter('scy_labs_neptune.override');
        if(isset($classes[$name])){
            if(!isset($classes[$name.'Form'])){
                $form = null;
            }else{
                // If New Class Exist . Use new Class , else if original Class Exist use Original class Else set null
                $form = (class_exists($classes[$name.'Form'])) ? $classes[$name.'Form'] : ((class_exists($originalClasses[$name.'Form'])) ? $originalClasses[$name.'Form'] : null ) ;
            }
            return (class_exists($classes[$name])) ? $classes[$name] : (array_key_exists($name,$originalClasses) && class_exists($originalClasses[$name]) ? $originalClasses[$name] : null);

        }
        return null;
    }


}