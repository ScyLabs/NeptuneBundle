<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/08/2018
 * Time: 14:25
 */

namespace ScyLabs\NeptuneBundle\Controller\Front;



use Doctrine\Common\Collections\ArrayCollection;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElem;
use ScyLabs\NeptuneBundle\Entity\ElementUrl;
use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageUrl;
use ScyLabs\NeptuneBundle\Entity\Partner;
use ScyLabs\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class PageController extends AbstractController
{

    /**
     * @Route("/{_locale}",name="homepage",requirements={" _locale"="[a-z]{2}"},defaults={"_locale"="fr"})
     */
    public function home(Request $request,?array $options = null,AdapterInterface $adapter){
        
        $em = $this->getDoctrine()->getManager();
        $locale = $request->getLocale();
        if(!in_array($locale,$this->getParameter('langs'))){
            return $this->redirectToRoute("homepage",array(
                '_locale'   => 'fr'
            ));
        }
        $pages = $em->getRepository($this->getClass('page'))->findBy(array(
            'parent' => null,
            'remove' => false,
            'active' => true
            ),
            ['prio'=>'ASC']
        );
        $page = $pages[0];

        if($page->getZones()->count() > 0){
            $page->getZones()[0]->setTypeHead(1);
        }

        $infos = $em->getRepository($this->getClass('infos'))->findOneBy([],['id'=>'ASC']);
        $partners = $em->getRepository($this->getClass('partner'))->findAll();
        $contactPages = new ArrayCollection();
        foreach ($pages as $thisPage){
            if($thisPage->getType()->getName() == 'contact'){
                $contactPages->add($thisPage);
                break;
            }
        }
    
        $params = array('pages'=>$pages,'page'=>$page,'infos'=>$infos,'partners'=>$partners,'locale'=>$request->getLocale(),'contactPages'=>$contactPages,'jsZones'=>$this->getZonesDeps($page));
        if($options !== null){
            $params = array_merge($params,$options);
        }

        return $this->render('page/home.html.twig',$params);
    }

    /**
     * @Route("/{_locale}/element/{slug}{anchor}",name="detail_element",requirements={"_locale"="[a-z]{2}","slug"="^[a-z-_0-9/]+$","anchor"="^(\\#)[a-z-_]+$"},defaults={"_locale"="fr","anchor"=""})
     */
    public function detailElement(Request $request,$slug,?array $options = null){
        $em = $this->getDoctrine()->getManager();
        $url = $em->getRepository($this->getClass('elementUrl'))->findOneBy(array(
            'url' => $slug
        ));
        if($url === null)
            return $this->redirectToRoute('homepage');
        if($url->getLang() !== $request->getLocale()){
            $url = $em->getRepository($this->getClass('elementUrl'))->findOneBy(
                array(
                    'lang'  => $request->getLocale(),
                    'element'  => $url->getElement()
                )
            );
            if($url === null){
                return $this->redirectToRoute('homepage');
            }
            return $this->redirectToRoute('detail_actuality',array('_locale'=>$request->getLocale(),'slug' => $url->getUrl()));
        }
        $element = $url->getElement();
        if($element->getActive() === false || $element->getRemove() === true){
            return $this->redirectToRoute('homepage');
        }
        if($element->getZones()->count() > 0){
            $element->getZones()[0]->setTypeHead(1);
        }
        $pages = $em->getRepository($this->getClass('page'))->findBy(array(
            'parent'    =>  null,
            'remove'    =>  false,
            'active'    => true
        ),
            ['prio'=>'ASC']
        );
        $infos = $em->getRepository($this->getClass('infos'))->findOneBy([],['id'=>'ASC']);
        $partners = $em->getRepository($this->getClass('partner'))->findAll();
        $params = array('pages'=>$pages,'page'=>$element,'infos'=>$infos,'partners'=>$partners,'locale'=>$request->getLocale(),'jsZones'=>$this->getZonesDeps($element));

        if($options !== null){
            $params = array_merge($params,$options);
        }

        return $this->render('page/page.html.twig',$params);
    }
    
    /**
     * @Route("/{_locale}/{slug}{anchor}",name="page",requirements={"slug"="^[a-z-_0-9/]+$","_locale"="[a-z]{2}","anchor"="^(\\#)[a-z-_]+$"},defaults={"anchor"="","_locale"="fr"})
     */
    public function page(Request $request,$slug,?array $options = null){

        $locale = $request->getLocale();
        if(!in_array($locale,$this->getParameter('langs'))){
            return $this->redirectToRoute('homepage',array(
                '_locale'   => 'fr'
            ));
        }

        $em = $this->getDoctrine()->getManager();
        $url = $em->getRepository($this->getClass('pageUrl'))->findOneBy(array(
            'url' => $slug
        ));

        if($url === null)
            return $this->redirectToRoute('homepage');

        if($url->getLang() !== $request->getLocale()){
            $url = $em->getRepository($this->getClass('pageUrl'))->findOneBy(
                array(
                    'lang'  => $request->getLocale(),
                    'page'  => $url->getPage()
                )
            );
            if($url === null){
                return $this->redirectToRoute('homepage');
            }
            return $this->redirectToRoute('page',array('_locale'=>$request->getLocale(),'slug' => $url->getUrl()));
        }


        $page = $url->getPage();

        if($page->getPrio() === 0 && $page->getParent() === null){
            return $this->redirectToRoute('homepage',['_locale'=>$request->getLocale()]);
        }
        if($page->getZones()->count() > 0){
            $page->getZones()[0]->setTypeHead(1);
        }

        if(!$page->getActive() || $page->getRemove()){
            return $this->redirectToRoute('homepage');
        }
        

        $pages = $em->getRepository($this->getClass('page'))->findBy(array(
            'parent'    =>  null,
            'remove'    =>  false,
            'active'    => true,
            ),
            ['prio'=>'ASC']
        );


        $infos = $em->getRepository($this->getClass('infos'))->findOneBy([],['id'=>'ASC']);
        $partners = $em->getRepository($this->getClass('partner'))->findBy(['remove'=>false]);

        $contactPages = new ArrayCollection();

        foreach ($pages as $thisPage){
            if($thisPage->getType()->getName() == 'contact'){
                $contactPages->add($thisPage);
                break;
            }
        }

        $params = array('pages'=>$pages,'page'=>$page,'infos'=>$infos,'partners'=>$partners,'locale'=>$request->getLocale(),'contactPages'=>$contactPages,'jsZones'=>$this->getZonesDeps($page));

        if($options !== null){
            $params = array_merge($params,$options);
        }
        if(file_exists($this->getParameter('kernel.project_dir').'/templates/page/'.$page->getType()->getName().'.html.twig')){
            return $this->render('page/'.$page->getType()->getName().'.html.twig',$params);
        }

        return $this->render('page/page.html.twig',$params);
    }

    private function getZonesDeps(AbstractElem $page){
        $tabJs = [];
        $assetsDirectory = $this->getParameter('scy_labs_neptune.assetsDirectory');
        if($_ENV['APP_ENV'] == 'dev' && file_exists($publicDir.'/css/import.less')){
            $import_less = file_get_contents($publicDir.'/css/import.less');
            $new_import = $import_less;
        }

        foreach ($page->getZones() as $zone) {

                if (!in_array($zone->getType()->getName(), $tabJs) && file_exists($publicDir.'/js/zone/'.$zone->getType()->getName().'.js')) {
                    $tabJs[] = $zone->getType()->getName();
                }
                if(isset($import_less) && isset($new_import)){

                    if(!preg_match("#zone\/".$zone->getType()->getName()."\.less#",$new_import) && file_exists($publicDir.'/css/zone/'.$zone->getType()->getName().'.less')){
                        $new_import .= "\n".'@import "zone/'.$zone->getType()->getName().'.less";';
                    }
                }
        }

        if(isset($import_less) && strlen($new_import) > strlen($import_less)){
            $f = fopen($publicDir.'/css/import.less','w+');
            fwrite($f,$new_import);
            fclose($f);
        }
        return $tabJs;
    }
    private function getClass($name,&$form = null){

        $originalClasses = Yaml::parseFile(dirname(__DIR__,2).'/Resources/config/original_classes.yaml');
        $classes = $this->getParameter('scy_labs_neptune.override');
        if(isset($classes[$name])){
            if(!isset($classes[$name.'Form'])){
                $form = null;
            }else{
                // If New Class Exist . Use new Class , else if original Class Exist use Original class Else set null
                $form = (class_exists($classes[$name.'Form'])) ? $classes[$name.'Form'] : ((class_exists($originalClasses[$name.'Form'])) ? $originalClasses[$name.'Form'] : null ) ;
            }

            return (class_exists($classes[$name])) ? $classes[$name] : (class_exists($originalClasses[$name]) ? $originalClasses[$name] : null);

        }
        return null;
    }
}

