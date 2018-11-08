<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/08/2018
 * Time: 14:25
 */

namespace ScyLabs\NeptuneBundle\Controller;



use Doctrine\Common\Collections\ArrayCollection;
use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageUrl;
use ScyLabs\NeptuneBundle\Entity\Partner;
use ScyLabs\NeptuneBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends Controller
{
    /**
     * @Route("/{_locale}",name="homepage",requirements={"_locale"="[a-z]{2}"},defaults={"_locale"="fr"})
     */
    public function homeAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository(Page::class)->findBy(array(
            'parent' => null,
            'remove' => false
            ),
            ['prio'=>'ASC']
        );
        $page = $pages[0];

        $infos = $em->getRepository(Infos::class)->findOneBy([],['id'=>'ASC']);
        $partners = $em->getRepository(Partner::class)->findAll();
        $contactPages = new ArrayCollection();
        foreach ($pages as $thisPage){
            if($thisPage->getType()->getName() == 'contact'){
                $contactPages->add($thisPage);
                break;
            }
        }
        $params = array('pages'=>$pages,'page'=>$page,'infos'=>$infos,'partners'=>$partners,'locale'=>$request->getLocale(),'contactPages'=>$contactPages);
        return $this->render('page/home.html.twig',$params);
    }



    /**
     * @Route("/{_locale}/{slug}",name="page",requirements={"slug"="^(?!admin)[a-z-_0-9/]+$","_locale"="[a-z]{2}"})
     */
    public function pageAction(Request $request,$slug){
        $em = $this->getDoctrine()->getManager();
        $url = $em->getRepository(PageUrl::class)->findOneBy(array(
            'url' => $slug
        ));

        if($url === null)
            return $this->redirectToRoute('homepage');

        if($url->getLang() !== $request->getLocale()){
            $url = $em->getRepository(PageUrl::class)->findOneBy(
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

        if($page->getActive() === false){
            return $this->redirectToRoute('homepage');
        }

        if($page->getType()->getName() == 'contact'){
            return $this->redirectToRoute('contact');
        }

        $pages = $em->getRepository(Page::class)->findBy(array(
            'parent'    =>  null,
            'remove'    =>  false,
            ),
            ['prio'=>'ASC']
        );


        $infos = $em->getRepository(Infos::class)->findOneBy([],['id'=>'ASC']);
        $partners = $em->getRepository(Partner::class)->findAll();

        $contactPages = new ArrayCollection();

        foreach ($pages as $thisPage){
            if($thisPage->getType()->getName() == 'contact'){
                $contactPages->add($thisPage);
                break;
            }
        }

        $params = array('pages'=>$pages,'page'=>$page,'infos'=>$infos,'partners'=>$partners,'locale'=>$request->getLocale(),'contactPages'=>$contactPages);



        if(file_exists($this->getParameter('kernel.project_dir').'/templates/page/'.$page->getType()->getName().'.html.twig')){
            return $this->render('page/'.$page->getType()->getName().'.html.twig',$params);
        }

        return $this->render('page/page.html.twig',$params);
    }


    /**
     * @Route("{_locale}/element/{slug}",name="detail_element",requirements={"slug"="^(?!admin)[a-z-_0-9/]+$","_locale"="[a-z]{2}"},defaults={"_locale"="fr"})
     */
    public function detailElememController(Request $request,$slug){

        $em = $this->getDoctrine()->getManager();
        $url = $em->getRepository(ElementUrl::class)->findOneBy(array(
            'url' => $slug
        ));

        if($url === null)
            return $this->redirectToRoute('homepage');

        if($url->getLang() !== $request->getLocale()){
            $url = $em->getRepository(ElementUrl::class)->findOneBy(
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
        if($element->getActive() === false){
            return $this->redirectToRoute('homepage');
        }

        $pages = $em->getRepository(Page::class)->findBy(array(
            'parent'    =>  null,
            'remove'    =>  false,
        ),
            ['prio'=>'ASC']
        );


        $infos = $em->getRepository(Infos::class)->findOneBy([],['id'=>'ASC']);
        $partners = $em->getRepository(Partner::class)->findAll();

        $params = array('pages'=>$pages,'page'=>$element,'infos'=>$infos,'partners'=>$partners,'locale'=>$request->getLocale());

        return $this->render('page/page.html.twig',$params);
    }

}