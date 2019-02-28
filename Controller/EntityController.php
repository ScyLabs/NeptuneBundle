<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 26/06/2018
 * Time: 15:48
 */

namespace ScyLabs\NeptuneBundle\Controller;

use ScyLabs\NeptuneBundle\AbstractEntity\AbstractChild;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractFileLink;
use ScyLabs\NeptuneBundle\Entity\Document;
use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\ElementType;
use ScyLabs\NeptuneBundle\Entity\File;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\Photo;
use ScyLabs\NeptuneBundle\Entity\User;
use ScyLabs\NeptuneBundle\Entity\Video;
use ScyLabs\NeptuneBundle\Entity\Zone;
use ScyLabs\NeptuneBundle\Form\ElementForm;
use ScyLabs\NeptuneBundle\Form\PageForm;
use ScyLabs\NeptuneBundle\Form\ZoneForm;
use ScyLabs\NeptuneBundle\Form\ZoneTypeForm;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class EntityController extends BaseController
{

    /* Quelles Entités sont acceptées pour un retour JSON avec la route neptune_entity_json */

    const JSON_IGNORED_ATTRIBUTES = array('page','pages','parent','document','zone','video','file','type','element','partner','photo','pageLink','files');

    /* Quelles Entités sont Acceptées dans la majorité de ce controller ? */


    public function listAction($type,$parentType,$parentId){
        if($parentType !== null && $parentId === null){
            return $this->redirectToRoute('neptune_entity',array('type'=>$type));
        }

        if(null === $class = $this->getClass($type)){
            return $this->redirectToRoute('neptune_home');
        }

        $repo = $this->getDoctrine()->getRepository($class);
        $child = false;
        $elemListing = false;

        if($class === Page::class){
            $objects = $repo->findBy(array(
               'parent' =>  null,
               'remove' =>  false,
            ),['prio'=>'ASC']);

        }
        elseif($class == Element::class){
            $objects = $this->getDoctrine()->getRepository(ElementType::class)->findBy(array(
                'remove'    =>  false
            ));
            $elemListing = true;
        }
        else{
            $objects = null;

            if(!(new $class() instanceof AbstractChild) || ($parentType !== null && $parentId !== null)){
                $objects = $repo->findBy(array(
                    'remove'=>false,
                ),['prio'=>'ASC']);

                if(null !== $classParent = $this->getClass($parentType)){
                    $parent = $this->getDoctrine()->getRepository($classParent)->find($parentId);
                }
            }
            else{

                $child = true;

            }
        }
        $params = array(
            'title'         =>  ucfirst($type).'s'.((isset($parent) && $parent != null) ? ' de - '.ucfirst($parentType).' : '.$parent->getName() : ''),
            'objects'       =>  $objects,
            'child'         =>  $child,
            'elemLisiting'  => $elemListing
        );
        $params['ariane'] = array(
            [
                'link'  => $this->generateUrl('neptune_home'),
                'name'  => 'Accueil'
            ],
            [
                'link'  =>  '#',
                'name'  =>  ucfirst($type).'s'
            ]
        );
        if(null !== $collection = $this->getAllEntities($class)){
            $params['collection'] = $collection;
        }

        return $this->render('@ScyLabsNeptune/admin/entity/listing.html.twig',$params);

    }

    public function jsonListingAction($type,$parentType,$parentId){

        if(null === $class = $this->getClass($type)){
            return $this->redirectToRoute('neptune_home');
        }
        $repo = $this->getDoctrine()->getRepository($class);
        $encoder = new JsonEncoder();
        $normalizer =new ObjectNormalizer();
        $parent = null;


        if($class === Page::class){
            $objects = $repo->findBy(array(
               'parent' =>  $parentId,
               'remove' =>  false,
            ),['prio'=>'ASC']);
        }
        else{
            $params = array(
                'remove'     =>  false
            );
            if($parentType !== null && $parentId != null){
                $params[$parentType] =  $parentId;
            }
            $objects = $repo->findBy($params,['prio'=>'ASC']);
        }
        $resultTab = array();

        foreach ($objects as $object){
            $actions = null;
            if(!($object instanceof AbstractFileLink)){
                $actions = array(
                    'active'    =>  $this->generateUrl('neptune_entity_active',array('type'=>$type,'id'=>$object->getId())),
                    'detail'    =>  $this->generateUrl('neptune_detail',array('type'=>$type,'id'=>$object->getId())),
                    'edit'      =>  $this->generateUrl('neptune_entity_edit',array('type'=>$type,'id'=>$object->getId())),
                    'gallery'   =>  $this->generateUrl('neptune_file_gallery_prio',array('type'=>$type,'id'=>$object->getId())),
                    'remove'    =>  $this->deleteAction(new Request(),$type,$object->getId()),
                );
            }
            $resultTab[] = array('object'=>$object,'actions'=>$actions);
        }
        $normalizer->setIgnoredAttributes(self::JSON_IGNORED_ATTRIBUTES);

        return  new JsonResponse((new Serializer(array($normalizer),array($encoder)))->serialize($resultTab,'json'));
    }

    public function addAction(Request $request,$type,$parentType,$parentId){

        if(null === $class = $this->getClass($type,$form)){
            return $this->redirectToRoute('neptune_home');
        }

        $object = new $class();

        if($parentType !== null && $parentId !== null && in_array($parentType,['page','element'])){
            $classParent = $this->getClass($parentType);

            $parent = $this->getDoctrine()->getRepository($classParent)->find($parentId);
            $object->setParent($parent);
        }

        $lastPrio = $this->getLastPrio($object,$parentType) +1;
        $object->setPrio($lastPrio);
        $objects = $this->getEntities($object,$parentType);


        $params = array(
            'title'     =>  "Ajout d'un".(($object instanceof Element) ? '' : 'e').' '.ucfirst($type),
            'objects'   => $objects,
        );


        $params['ariane'] = array(
            [
                'link'  =>  $this->generateUrl('neptune_home'),
                'name' =>  'Accueil'
            ],
            [
                'link'  =>  $this->generateUrl('neptune_entity',array('type'=>$type)),
                'name' =>  ucfirst($type).'s',
            ],
            [
                'link'  =>  '#',
                'name' =>  'Créer un'.(($object instanceof Element) ? '' : 'e').ucfirst($type)
            ]
        );
        $paramsRoute = array('type'=>$type);
        if($parentType !== null && $parentId !== null && in_array($parentType,['page','element'])){
            $paramsRoute['parentId'] = $parentId;$paramsRoute['parentType'] = $parentType;
        }
        $route = $this->generateUrl('neptune_entity_add',$paramsRoute);


        if($this->validForm($type,$form,$object,$request,$params['form'],$route) === true){
            return $this->redirectToRoute('neptune_entity_add',$paramsRoute);
        }
        else{
            return $this->render('@ScyLabsNeptune/admin/entity/add.html.twig',$params);
        }

    }

    public function editAction(Request $request,$id,$type){

        if(null === $class = $this->getClass($type,$form)){
            return $this->redirectToRoute('neptune_home');
        }

        $repo = $this->getDoctrine()->getRepository($class);
        $object = $repo->find($id);

        if($object === null){
            return $this->redirectToRoute('neptune_entity',array('type'=>$type));
        }

        if($object instanceof Page){
            $objects = $repo->findBy(array(
                'remove'    =>  false,
                'parent'    =>  null
            ));
        }
        elseif(!$object instanceof User){
            $objects = $repo->findBy(array(
                'remove'    =>  false
            ));
        }
        else{
            $objects = null;
        }
        $params = array(
            'title'     =>  'Modification de '.(($object instanceof Element) ? "l'" : 'la').ucfirst($type).' : '.$object->getName(),
            'objects'   =>  $objects
        );
        $route = $this->generateUrl('neptune_entity_edit',array('type'=>$type,'id'=>$object->getId()));
        if($this->validForm($type,$form,$object,$request,$params['form'],$route)){
            $this->get('session')->getFlashBag()->add('notice','Votre '.$type.' à bien été modifié');
            return $this->redirectToRoute('neptune_entity',array('type'=>$type));
        }
        else{
            return $this->render('@ScyLabsNeptune/admin/entity/add.html.twig',$params);
        }
    }

    public function deleteAction(Request $request,$type,$id){

        if(null === $class = $this->getClass($type,$form)){
            return $this->redirectToRoute('neptune_home');
        }
        $em = $this->getDoctrine()->getManager();
        $object = $em->getRepository($class)->find($id);
        if($object === null){
            return $this->redirectToRoute('neptune_entity',array('type'=>$type));
        }
        $form = $this->createFormBuilder($object)->setMethod('post')
            ->setAction($this->generateUrl('neptune_entity_delete',array('type'=>$type,'id'=>$id)))
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($object instanceof User && !$object->hasRole('ROLE_SUPER_ADMIN') && $object !== $this->getUser()){
                $em->remove($object);
                $em->flush();
                return $this->redirect($request->headers->get('referer'));
            }
            $object->setRemove(true);
            $em->persist($object);
            $em->flush();
            return $this->redirect($request->headers->get('referer'));
        }
        $params = array(
            'form'  =>  $form->createView()
        );
        return $this->render('@ScyLabsNeptune/admin/delete.html.twig',$params);
    }

    public function prioAction(Request $request,$type){
        $ajax = $request->isXmlHttpRequest();
        $prio = $request->request->get('prio');
        if($prio === null || false === $prios = json_decode($prio)){
            if($ajax){
                return new Response('');
            }
            else{
                return $this->redirectToRoute('neptune_entity',array('type'=>$type));
            }
        }
        $em = $this->getDoctrine()->getManager();

        if(null === $class = $this->getClass($type)){
            return new Response('');
        }
        $repo = $em->getRepository($class);
        $objects = $repo->findAll();
        $tabObjects = array();
        foreach ($objects as $object){
            $tabObjects[$object->getId()] = $object;
        }
        $this->prios($prios,$tabObjects);

        $em->flush();
        if($ajax){
            return new Response('success');
        }
        else{
            return $this->redirectToRoute('neptune_entity',array('type'=>$type));
        }

    }

    public function switchActiveAction(Request $request,$id,$type){

        if(null === $class = $this->getClass($type)){
            return $this->redirectToRoute('neptune_home');
        }

        $em = $this->getDoctrine()->getManager();
        $object = $em->getRepository($class)->find($id);

        if(null === $object){
            $this->redirectToRoute('neptune_entity',array('type'=>$type));
        }
        $referer = $request->headers->get('referer');
        $object->setActive(!$object->getActive());
        $em->persist($object);
        $em->flush();
        return $this->redirect($referer);
    }

    /**
     * @param array $prios
     * @param array $objects
     * @param int $parent
     */
    private function prios(array $prios, array &$objects, $parent = 0){
        $i = 0;
        foreach ($prios as $prio){
            $id = $prio->id;
            if(isset($objects[$id]) && is_object($objects[$id])){
                $objects[$id]->setPrio($i);
                if($objects[$id] instanceof Page){
                    if($parent > 0 && is_object($objects[$parent])){
                        $objects[$id]->setParent($objects[$parent]);
                    }else{
                        $objects[$id]->setParent(null);
                    }
                }
                $i++;
            }
            if($objects[$id] instanceof Page){
                if(isset($prio->children) && sizeof($prio->children) > 0){
                    self::prios($prio->children,$objects,$id);
                }
            }
        }
    }
}

