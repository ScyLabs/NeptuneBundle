<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 06/08/2018
 * Time: 16:01
 */

namespace ScyLabs\NeptuneBundle\EventListener;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PostUpdate;
use Doctrine\ORM\Mapping\PreUpdate;
use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\ElementDetail;
use ScyLabs\NeptuneBundle\Entity\ElementUrl;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageDetail;
use ScyLabs\NeptuneBundle\Entity\PageUrl;

class EntityListener
{

    private $langs;
    private $entity;

    public function __construct($langs = array()){

        $this->langs = $langs;

    }

    public function postPersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if($entity instanceof Page) {
            foreach ($entity->getDetails() as $detail) {
                $url = new PageUrl();
                $urlParent = '';
                if($entity->getParent() !== null){
                    $urlParent = $entity->getParent()->getUrl($detail->getLang());
                    if($urlParent === null){
                        $urlParent = '';
                    }
                    else{
                        $urlParent = $urlParent->getUrl().'/';
                    }
                }
                $url->setLang($detail->getLang())
                    ->setUrl($urlParent.$detail->getSlug());
                $entity->addUrl($url);
                $em->persist($url);
                $this->childsUrl($entity,$em);
            }
            $em->flush();
        }
        elseif($entity instanceof Element){
            foreach ($entity->getDetails() as $detail) {
                $url = new ElementUrl();
                $url->setLang($detail->getLang())
                    ->setUrl($detail->getSlug());
                $entity->addUrl($url);
                $em->persist($url);

            }
            $em->flush();
        }
    }

    public function preUpdate(PreUpdateEventArgs $args){

        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if($entity instanceof PageDetail ){

            $page = $entity->getPage();

            $url = $page->getUrl($entity->getLang());
            if($url === null){
                $url = new PageUrl();
                $url->setLang($entity->getLang());
                $page->addUrl($url);
            }

            $urlParent = '';
            if($page->getParent() !== null){
                $urlParent = $page->getParent()->getUrl($entity->getLang());
                if($urlParent === null){
                    $urlParent = '';
                }
                else{
                    $urlParent = $urlParent->getUrl().'/';
                }
            }

            $url->setUrl($urlParent.$entity->getSlug());
            $this->childsUrl($page,$em);


        }
        elseif($entity instanceof ElementDetail){
            $element = $entity->getElement();
            $url = $element->getUrl($entity->getLang());
            if($url === null){
                $url = new ElementUrl();
                $url->setLang($entity->getLang());
                $element->addUrl($url);
            }
            $url->setUrl($entity->getSlug());
        }
        elseif($entity instanceof  Page){
            if($args->hasChangedField('parent')){

                foreach($entity->getDetails() as $detail){

                    $url = $entity->getUrl($detail->getLang());

                    if($url === null){
                        $url = new PageUrl();
                        $url->setLang($detail->getLang());
                        $entity->addUrl($url);
                    }

                    $urlParent = '';

                    if($entity->getParent() !== null){
                        $urlParent = $entity->getParent()->getUrl($detail->getLang());
                        if($urlParent === null){
                            $urlParent = '';
                        }
                        else{
                            $urlParent = $urlParent->getUrl().'/';
                        }
                    }

                    $url->setUrl($urlParent.$entity->getDetail($url->getLang())->getSlug());

                    if($entity->getChilds()->count() > 0)
                        $this->childsUrl($entity,$em);

                }


            }
        }

    }



    private function childsUrl(Page $page){

        foreach ($page->getChilds() as $child){
            foreach($child->getDetails() as $detail){

                $url = $child->getUrl($detail->getLang());
                if($url === null){
                    $url = new PageUrl();
                    $url->setLang($detail->getLang());
                    $child->addUrl($url);
                }
                $urlParent = '';
                $urlParent = $page->getUrl($url->getLang());
                if($urlParent === null){
                    $urlParent = '';
                }
                else{
                    $urlParent = $urlParent->getUrl().'/';
                }
                $url->setUrl($urlParent.$child->getDetail($url->getLang())->getSlug());

                if($child->getChilds()->count() > 0){
                    $this->childsUrl($child);
                }
            }
        }
    }


}