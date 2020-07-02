<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 23/05/2019
 * Time: 15:06
 */

namespace ScyLabs\NeptuneBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use ScyLabs\NeptuneBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Annotation\Route;

class CloningController extends BaseController
{

    /**
     * @Route("/{type}/clone/{id}",name="neptune_entity_clone",requirements={"type"="[a-z]{2,20}","id"="[0-9]+"})
     */
    public function clone(Request $request,$type,$id){
        if(null === $class = $this->getClass($type,$formClass)){
            return $this->redirectToRoute('neptune_home');
        }

        $repo = $this->getDoctrine()->getRepository($class);
        $object = $repo->find($id);
        if($object === null){
            return $this->redirectToRoute('neptune_entity',array('type'=>$type));
        }

        $clone = clone $object;

        $em = $this->getDoctrine()->getManager();

        $em->persist($clone);
        $em->flush();

        $prio = $request->request->get('prio');

        if($prio === null || false === $prios = json_decode($prio,true)){

            if($request->isXmlHttpRequest()){
                return $this->json(array('success'=>false,'message'=>'Une erreur est survenue pendant le clonnage'));

            }
            else{
                return $this->redirectToRoute('neptune_entity',array('type'=>$type));
            }
        }

        $prios = $this->addPrioAfterSelection($prios,$id,$clone->getId());

        $objects = $repo->findAll();

        $tabObjects = array();
        foreach ($objects as $object){
            $tabObjects[$object->getId()] = $object;
        }


        $this->prios($prios,$tabObjects);
        $em->flush();

        if($request->isXmlHttpRequest()){
            return $this->json(array('success'=>true,'message'=>'Votre '.ucfirst($type).' à bien été clonné'));
        }
        return $this->redirect($request->headers->get('referer'));
    }

    private function addPrioAfterSelection($prios,$cible,$id){

        $_prios = array();

        foreach ($prios as $prio){
            $_prios[] = $prio;

            if($prio['id'] === (int)$cible){
                $_prios[]  = ['id' => $id];
            }
            if(isset($prio['children'])){
                $_prios[sizeof($_prios) - 1]['children'] = $this->addPrioAfterSelection($prio['children'],$cible,$id);
            }

        }
        return $_prios;
    }

    private function prios(array $prios, array &$objects, $parent = 0){
        $i = 0;
        foreach ($prios as $prio){
            $id = $prio['id'];
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
                if(isset($prio['children']) && sizeof($prio['children']) > 0){
                    $this->prios($prio['children'],$objects,$id);
                }
            }
        }
    }

}