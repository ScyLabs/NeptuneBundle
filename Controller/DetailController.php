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

   //const VALID_ENTITIES = "(page|element|zone|partner|photo)";
    const VALID_ENTITIES = "[a-z]{2,20}";
    /**
     * @Route("admin/{type}/details/{id}",defaults={"parentType"=null},name="admin_detail", requirements={"type"=DetailController::VALID_ENTITIES,"id"="\d+"})
     */
    public function listAction(Request $request,$type,$id,$parentType){
        $class = $this->getClass($type);
        if($class === null){
            return $this->redirectToRoute('admin_home');
        }
        $langs = $this->getParameter('langs');
        $classDetail = $this->getDetailClass($class,$form);
        $em = $this->getDoctrine()->getManager();
        $object = $em->getRepository($class)->find($id);

        if(null === $object){
            return $this->redirectToRoute('admin_home');
        }
        $details = $object->getDetails();

        /*Si l'objet n'a aucun details */

        if($details->count() === 0){

            foreach ($langs as $lang){
                $detail = new $classDetail();
                $detail->setLang($lang);
                $detail->setName($object->getName());
                $object->addDetail($detail);
                $em->persist($detail);
            }
        }
        elseif(sizeof($langs) !== $details->count()){
            if(sizeof($langs ) < $details->count() ){
                $tabLang = array();
                foreach ($details as $detail){
                    $tabLang[] = $detail->getLang();
                }

                $diff = array_diff($tabLang,$langs);
                foreach ($diff as $lang){
                    $em->remove($object->getDetail($lang));
                }
            }
            else{
                /* Si l'object a des details , mais qu'il n'en a pas autant que de langues actives */
                foreach ($langs as $lang){
                    $i = 0;
                    foreach ($details as $detail){
                        $i++;
                        if($detail->getLang() == $lang){
                            break;
                        }
                        if($i == sizeof($details)){
                            $newDetail = new $classDetail();
                            $newDetail->setLang($lang);
                            $newDetail->setName($object->getName());
                            $object->addDetail($newDetail);
                            $em->persist($newDetail);
                        }
                    }
                }
            }
        }
        $em->flush();
        $details = $object->getDetails();

        $collection = array();

        foreach ($details as $detail){
            $route = $this->generateUrl('admin_detail_edit',array(
                'type'  =>  $type,
                'id'    =>  $id,
                'lang'  => $detail->getLang(),
            ));
            $this->validForm($form,$detail,$request,$collection[$detail->getLang()]['form'],$route);
        }
        $params = array(
            'title'         =>  'Details de l'.(($type == 'element') ? "'" : 'a').' '.$type.'  :  '.$object->getName(),
            'collection'    =>  $collection,
            'object'        => $object
        );
        if(!$object instanceof Photo)
            $params['objects'] = $this->getEntities($object,$parentType);


        $params['ariane'] = array(
            [
                'link'  =>  $this->generateUrl('admin_home'),
                'name' =>  'Accueil'
            ],
            [
                'link'  =>  $this->generateUrl('admin_entity',array('type'=>$type)),
                'name' =>  ucfirst($type).'s',
            ],
            [
                'link'  =>  '#',
                'name' =>  'Details de l'.(($type == 'element') ? "'" : 'a').' '.$type.'  :  '.$object->getName(),
            ]
        );

        return $this->render('@ScyLabsNeptune/admin/detail/details.html.twig',$params);
    }
    /**
     * @Method("POST")
     * @Route("/admin/{type}/detail/{lang}/{id}",name="admin_detail_edit",requirements={"type"=DetailController::VALID_ENTITIES,"id"="\d+","lang"="[a-z]{2}"})
     */
    public function editAction(Request $request,$type,$id,$lang){

        $class = $this->getClass($type);
        if(null === $class){
            return $this->redirectToRoute('admin_home');
        }
        if(null === $object = $this->getDoctrine()->getRepository($class)->find($id)){
            return $this->redirectToRoute('admin_home');
        }

        $classDetail = $this->getDetailClass($class,$form);
        if(null === $classDetail){
            return $this->redirectToRoute('admin_home');
        }
        $detail = $object->getDetail($lang);

        $params = array();
        $this->validForm($form,$detail,$request,$params['form']);
        return $this->redirect($request->headers->get('referer'));

    }

}