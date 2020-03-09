<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 29/06/2018
 * Time: 14:23
 */

namespace ScyLabs\NeptuneBundle\Controller;


use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\ElementDetail;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageDetail;
use ScyLabs\NeptuneBundle\Entity\Photo;
use ScyLabs\NeptuneBundle\Entity\Zone;
use ScyLabs\NeptuneBundle\Entity\ZoneDetail;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends BaseController
{

    public function listAction(Request $request,$type,$id){

        if(null === $class = $this->getClass($type)){

            return $this->redirectToRoute('neptune_home');
        }

        $classType = $type.'Detail';
        $langs = $this->getParameter('langs');
        if(null === $classDetail = $this->getClass($classType,$form)){

            return$this->redirectToRoute('neptune_home');
        }

        $em = $this->getDoctrine()->getManager();
        $object = $em->getRepository($class)->find($id);


        $details = $object->getDetails();


        /*Si l'objet n'a aucun details */

        foreach ($details as $detail){
            if(!in_array($detail->getLang(),$langs)){
                $object->removeDetail($detail);
                if(null !== $url = $object->geturl($detail->getLang())){
                    $object->removeUrl($url);
                    $em->remove($url);
                }
                $em->remove($detail);
            }
        }

        foreach ($langs as $lang){
            $detail = $object->getDetail($lang);

            if(!$detail->getId()){
                $detail->setLang($lang);
                $detail->setName($object->getName());
                $object->addDetail($detail);
                $em->persist($detail);
            }
        }


        $em->flush();
        $details = $object->getDetails();

        $collection = array();

        foreach ($details as $detail){
            $route = $this->generateUrl('neptune_detail_edit',array(
                'type'  =>  $type,
                'id'    =>  $id,
                'lang'  => $detail->getLang(),
            ));

            $this->validForm($classType,$form,$detail,$request,$collection[$detail->getLang()]['form'],$route);
        }

        $params = array(
            'title'         =>  'Details de l'.(($type == 'element') ? "'" : 'a').' '.$type.'  :  '.$object->getName(),
            'object'        => $object
        );
        if(!$object instanceof Photo)
            $params['objects'] = $this->getEntities($object);


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
                'name' =>  'Details de l'.(($type == 'element') ? "'" : 'a').' '.$type.'  :  '.$object->getName(),
            ]
        );

        foreach ($collection as $key => $val){
            $collection[$key]['form'] = $collection[$key]['form']->createView();
        }
        $params['collection'] =   $collection;

        return $this->render('@ScyLabsNeptune/admin/detail/details.html.twig',$params);
    }


    public function editAction(Request $request,$type,$id,$lang){

        if(null === $class = $this->getClass($type)){
            return $this->redirectToRoute('neptune_home');
        }
        if(null === $object = $this->getDoctrine()->getRepository($class)->find($id)){
            return $this->redirectToRoute('neptune_home');
        }

        if(null === $classDetail = $this->getClass($type.'Detail',$formClass)){
            return $this->redirectToRoute('neptune_home');
        }


        $detail = $object->getDetail($lang);


        if(true === $result =$this->validForm($type.'Detail',$formClass,$detail,$request,$form)){
            if($request->isXmlHttpRequest())
                return $this->json(array('success'=>true,'message'=>'Votre '.ucfirst($type).' à bien été ajouté'));

            return $this->redirectToRoute('neptune_detail',[
                'type'  =>  $type,
                'id'    =>  $detail->getParent()->getId()
            ]);
        }

        if($result !== false){
            if($request->isXmlHttpRequest())
                return $this->json($result);
            
            return $this->redirectToRoute('neptune_detail',[
                'type'  =>  $type,
                'id'    =>  $detail->getParent()->getId()
            ]);

        }
        return $this->redirect($request->headers->get('referer'));


    }

}