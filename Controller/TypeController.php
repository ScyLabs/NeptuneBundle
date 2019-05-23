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
        $class = $this->getClass($classType,$formClass);
        if($class === null){
            return $this->redirectToRoute('neptune_home');
        }


        $route = $this->generateUrl('neptune_type_add',array('type'=>$type));
        $params = array("title"=>"Ajout d'un type de ".$type);

        $em = $this->getDoctrine()->getManager();



        $types = $em->getRepository($class)->findByRemove(0);




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
        if(true === $result = $this->validForm($classType,$formClass,new $class(),$request,$form,$route)){
            if($request->isXmlHttpRequest()){
                return $this->json(array('success'=>true,'message'=>'Votre '.ucfirst($type).' à bien été ajouté'));
            }
            return $this->redirectToRoute('neptune_type',array('type'=>$type));
        }
        else{
            if($result !== false){
                return $this->json($result);
            }
            $params['form'] = $form->createview();
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
        $class = $this->getClass($classType,$formClass);
        if($class === null){
            return $this->redirectToRoute('neptune_home');
        }
        $repo = $this->getDoctrine()->getRepository($class);
        $oType = $repo->find($id);

        if(null === $oType){
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

        if($this->validForm($classType,$formClass,$oType,$request,$form,$route) === true){

            $this->get('session')->getFlashBag()->add('notice',"Votre type de page à bien été modifié");
            return $this->redirectToRoute('neptune_type',array('type'=>$type));
        }
        else{
            $params['form'] = $form->createView();
            return $this->render('@ScyLabsNeptune/admin/type/add.html.twig',$params);
        }
    }

    public function removeAction(Request $request,$id,$type){
        $classType = $type.'Type';
        $class = $this->getClass($classType);
        if($class === null){
            return $this->redirectToRoute('neptune_home');
        }
        $repo = $this->getDoctrine()->getRepository($class);

        $oType = $repo->find($id);

        if(null === $oType ||( is_object($oType) && $oType->getRemovable() === false)){
            $this->redirectToRoute('neptune_type');
        }
        $em = $this->getDoctrine()->getManager();
        $oType->setRemove(true);
        $em->persist($oType);
        $em->flush();
        
        if($request->isXmlHttpRequest()){
            return $this->json(array('success'=>true,'message'=>'Votre '.ucfirst($type).' à bien été supprimé'));
        }
        return $this->redirect($request->headers->get('referer'));

    }
}