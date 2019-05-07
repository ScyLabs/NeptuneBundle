<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 07/11/2018
 * Time: 14:35
 */

namespace ScyLabs\NeptuneBundle\Controller;


use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneratorController extends BaseController
{

    public function generateSiteMapAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository(Page::class)->findBy(array(
            'parent'    =>  null,
            'remove'    =>  false,
            'active'    =>  true
        ),array('prio'=>'ASC'));
        $elements = $em->getRepository(Element::class)->findBy(array(
            'remove'    =>  false,
            'active'    =>  true
        ));

        $urls = $this->getUrls($pages);
        $urls = array_merge($urls,$this->getUrls($elements));
    
        return $this->render('@ScyLabsNeptuneBundle/Resources/views/sitemap.xml.twig',array(
            'urls'=>$urls
        ));
    }

    /**
     * @param $objects
     * @return array
     */
    public function getUrls($objects){
        $urls = array();
        $sitemapConfig = $this->getParameter('scy_labs_neptune.sitemap');
        foreach ($objects as $object){

            foreach ($object->getUrls() as $urlObj){
                
                $url = null;
                if(in_array($urlObj->getLang(),$this->getParameter('langs'))){

                        if($object instanceof Page){
                            if($object->getType()->getName() != 'not_accessible'){
                                $url = ($object->getPrio() == 0 && $object->getParent() === null && $object->getActive()) ?  $this->generateUrl('homepage',array(
                                    '_locale'  =>  $urlObj->getLang(),
                                )) :  $this->generateUrl('page',array(
                                    '_locale'  =>  $urlObj->getLang(),
                                    'slug'  =>  $urlObj->getUrl()
                                ));
                            } 
                            $urls = array_merge($urls,$this->getUrls($object->getChilds()));
                        }
                        else{
                            
                            if($sitemapConfig['elements'] === false)
                                continue;
                            $url = $this->generateUrl('detail_element',array(
                                '_locale'   =>  $urlObj->getLang(),
                                'slug'      =>  $urlObj->getUrl()
                            ));
                        }
   
                    if($url !== null){
                        $urls[] = array(
                            'loc'   => $url
                        );
                    }

                }
            }
        }
        return $urls;
    }
}