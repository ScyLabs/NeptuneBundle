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
use ScyLabs\NeptuneBundle\Entity\User;
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

class BaseController extends AbstractController
{
    /* Fonction de génération de formulaire (surement future service)*/
    protected function validForm($type,$object,$request,&$param,$action = null){

        $form = $this->createForm($type,$object,['action'=>$action]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $object = $form->getData();
            if($object->getid() == null){
                if(null !== $classDetail = $this->getDetailClass(get_class($object))){
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
        $param = $form->createView();
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
        $pages = $em->getRepository(Page::class)->findBy(array(
            'remove'=>false,
            'parent'=>null
        ),['prio'=>'ASC']);

        if($pages !== null){
            $collection['Pages'] = $pages;
        }
        if($class !== Zone::class && $class !== Element::class){
            $zones = $em->getRepository(Zone::class)->findBy(array(
                'remove'=>false,
            ),['prio'=>'ASC']);
            if($zones !== null){
                $collection['Zones'] = $zones;
            }
        }
        if($class !== Element::class){
            $elements = $em->getRepository(Element::class)->findBy(array(
                'remove'=>false
            ),['prio'=>'ASC']);
            if($elements !== null){
                $collection['Elements'] =$elements;
            }
        }
        return $collection;
    }


    protected function getClass($name,&$form = null){
        if($name == 'page'){
            $form = PageForm::class;
            return Page::class;
        }
        elseif($name == 'element'){
            $form = ElementForm::class;
            return Element::class;

        }
        elseif($name == 'zone'){
            $form = ZoneForm::class;
            return Zone::class;
        }
        elseif($name == 'partner'){
            $form = PartnerForm::class;
            return Partner::class;
        }
        elseif($name == 'user'){
            $form = UserForm::class;
            return User::class;
        }
        elseif($name == 'photo'){
            return Photo::class;
        }
        elseif($name == 'video'){
            return Video::class;
        }
        elseif($name == 'document'){
            return Document::class;
        }

        else{
            return null;
        }
    }

    protected function getTypeClass($name,&$form = null){
        if($name == 'page'){
            $form = PageTypeForm::class;
            return PageType::class;
        }
        elseif($name == 'element'){
            $form = ElementTypeForm::class;
            return ElementType::class;

        }
        elseif($name == 'zone'){
            $form = ZoneTypeForm::class;
            return ZoneType::class;
        }
        else{
            $form = FileTypeForm::class;
            return FileType::class;
        }
    }
    protected function getDetailClass($name,&$form = null){
        if($name == Page::class){
            $form = PageDetailForm::class;
            return PageDetail::class;
        }
        elseif($name == Element::class){
            $form = ElementDetailForm::class;
            return ElementDetail::class;
        }
        elseif($name == Partner::class){
            $form = PartnerDetailForm::class;
            return PartnerDetail::class;
        }
        elseif($name == Zone::class){
            $form = ZoneDetailForm::class;
            return ZoneDetail::class;
        }
        elseif($name == Photo::class){
            $form = PhotoDetailForm::class;
            return PhotoDetail::class;
        }
        elseif($name == Document::class){
            $form = DocumentDetailForm::class;
            return DocumentDetail::class;
        }
        return null;

    }

}