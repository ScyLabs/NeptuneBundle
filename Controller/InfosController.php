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

    /*
     *
     * @Route("admin/infos/edit",name="admin_infos_edit")
     */
    public function editAction(Request $request){
        $class = Infos::class;
        $form = InfosForm::class;
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
        $route = $this->generateUrl('admin_infos_edit');

        if($this->validForm($form,$object,$request,$params['form'],$route)){
            $this->get('session')->getFlashBag()->add('notice','Les informations de votre site ont bien été modifiées');
            return $this->redirectToRoute('admin_infos_edit');
        }
        else{
            return $this->render('@ScyLabsNeptune/admin/entity/add.html.twig',$params);
        }
    }

}