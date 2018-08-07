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
                $url->setLang($detail->getLang())
                    ->setUrl($detail->getSlug());
                $entity->addUrl($url);
                $em->persist($url);
            }
            $em->flush();
        }
    }

    public function postUpdate(LifecycleEventArgs $args){

        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if($entity instanceof PageDetail){
            $page = $entity->getPage();
            $url = $page->getUrl($entity->getLang());

            $url->setUrl($entity->getSlug());

            $em->persist($url);
            $em->flush();

        }
    }


}