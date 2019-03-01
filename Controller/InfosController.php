<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 13/08/2018
 * Time: 15:09
 */

namespace ScyLabs\NeptuneBundle\Controller;


use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Form\InfosForm;
use ScyLabs\NeptuneBundle\Repository\InfosRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InfosController extends BaseController
{

    public function editAction(Request $request){
        $class = $this->getClass('infos',$formClass);
        $repo = $this->getDoctrine()->getRepository($class);

        $object = $repo->findOneBy([],['id'=>'ASC']);

        if($object === null){
            $object = new Infos();
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();
        }

        $params = array(
            'title'     =>  'Modification des Informations du site ',
            'objects'   =>  null
        );
        $route = $this->generateUrl('neptune_infos_edit');

        if(true === $result = $this->validForm('infos',$formClass,$object,$request,$form,$route)){
            if($request->isXmlHttpRequest()){
                dump('wah?');
                return $this->json(array('success'=>true,'message'=>'Vos Coordonnées ont bien été modifiées'));
            }
            $this->get('session')->getFlashBag()->add('notice','Les informations de votre site ont bien été modifiées');
            return $this->redirectToRoute('neptune_infos_edit');
        }
        else{
            if($result !== false){
                return $this->json($result);
            }
            $params['form'] = $form->createView();
            return $this->render('@ScyLabsNeptune/admin/entity/add.html.twig',$params);
        }
    }

}