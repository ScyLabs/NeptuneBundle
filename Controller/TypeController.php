<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 11/06/2018
 * Time: 16:30
 */

namespace ScyLabs\NeptuneBundle\Controller;


use ScyLabs\NeptuneBundle\Entity\ElementType;
use ScyLabs\NeptuneBundle\Entity\FileType;
use ScyLabs\NeptuneBundle\Entity\PageType;
use ScyLabs\NeptuneBundle\Entity\ZoneType;
use ScyLabs\NeptuneBundle\Form\ElementTypeForm;
use ScyLabs\NeptuneBundle\Form\FileTypeForm;
use ScyLabs\NeptuneBundle\Form\PageTypeForm;
use ScyLabs\NeptuneBundle\Form\ZoneTypeForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class TypeController extends BaseController
{

    public function addAction(Request $request,$type){

        $classType = $type.'Type';
        $class = $this->getClass($classType,$form);
        if($class === null){
            return $this->redirectToRoute('neptune_home');
        }


        $route = $this->generateUrl('neptune_type_add',array('type'=>$type));
        $params = array("title"=>"Ajout d'un type de ".$type);

        $em = $this->getDoctrine()->getManager();



        $types = $em->getRepository($class)->findByRemove(0);

        $result = $this->validForm($classType,$form,new $class(),$request,$params['form'],$route);


        $params['types'] = $types;

        // Génération du fil d'ariane

        $ariane = array(
            [
                'link'=>$this->generateUrl('neptune_home'),
                'name'=>'Accueil'
            ],
            [
                'link'=>$this->generateUrl('neptune_type',array('type'=>$type)),
                'name'=>'Types de '.$type.'s'
            ],
            [
                'link'=>'#',
                'name'=>'Créer'
            ]
        );
        $params['ariane'] = $ariane;
        if($result === true){

            return $this->redirectToRoute('neptune_type',array('type'=>$type));
        }
        else{

            return $this->render('@ScyLabsNeptune/admin/type/add.html.twig',$params);
        }

    }

    public function listAction(Request $request,$type){
        $classType = $type.'Type';
        $class = $this->getClass($classType);

        if($class === null){
            return $this->redirectToRoute('neptune_home');
        }
        $types = $this->getDoctrine()->getRepository($class)->findByRemove(false);


        $params = array(
            'title' =>  'Pages',
            'types' => $types
        );

        // Génération du fil d'ariane
        $ariane = array(
            [
                'link'=>$this->generateUrl('neptune_home'),
                'name'=>'Accueil'
            ],
            [
                'link'=>'#',
                'name'=>'Types de '.$type.'s'
            ]
        );
        $params['ariane'] = $ariane;

        return $this->render('@ScyLabsNeptune/admin/type/listing.html.twig',$params);
    }

    public function editAction(Request $request,$id,$type){
        $classType = $type.'Type';
        $class = $this->getClass($classType,$form);
        if($class === null){
            return $this->redirectToRoute('neptune_home');
        }
        $repo = $this->getDoctrine()->getRepository($class);
        $oType = $repo->find($id);

        if(null === $oType || ( is_object($oType) && $oType->getRemovable() === false)){
            return $this->redirectToRoute('neptune_type',array('type'=>$type));
        }
        $types = $repo->findBy(array(
            'remove'=>false,
        ));
        $params = array(
            'title' => "Modification du type de ".$type." : ".$oType->getName(),
            'types' => $types
        );

        $route = $this->generateUrl('neptune_type_edit',['id'=>$oType->getId(),'type'=>$type]);

        if($this->validForm($classType,$form,$oType,$request,$params['form'],$route) === true){

            $this->get('session')->getFlashBag()->add('notice',"Votre type de page à bien été modifié");
            return $this->redirectToRoute('neptune_type',array('type'=>$type));
        }
        else{
            return $this->render('@ScyLabsNeptune/admin/type/add.html.twig',$params);
        }
    }

    public function deleteAction(Request $request,$id,$type){
        $classType = $type.'Type';
        $class = $this->getClass($classType,$form);
        if($class === null){
            return $this->redirectToRoute('neptune_home');
        }
        $repo = $this->getDoctrine()->getRepository($class);

        $oType = $repo->find($id);

        if(null === $oType ||( is_object($oType) && $oType->getRemovable() === false)){
            $this->redirectToRoute('neptune_type');
        }


        $form = $this->createFormBuilder($type)->setMethod('POST')
            ->setAction($this->generateUrl('neptune_type_delete',array('id'=>$oType->getId(),'type'=>$type)))
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $oType->setRemove(true);
            $em->persist($oType);
            $em->flush();
            return $this->redirect($request->headers->get('referer'));
        }
        $params = array(
            'form'  =>  $form->createView(),
            'type'  =>  $oType,
        );

        return $this->render('@ScyLabsNeptune/admin/delete.html.twig',$params);
    }
}