<?php


namespace ScyLabs\NeptuneBundle\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractDetail;
use ScyLabs\NeptuneBundle\Entity\ElementDetail;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageDetail;
use ScyLabs\NeptuneBundle\Model\ClassFounderInterface;

class DetailListener
{

    private $entityManager;
    private $classFounder;

    private $pageUrlClass;
    private $elementUrlClass;

    public function __construct(EntityManagerInterface $entityManager,ClassFounderInterface $classFounder) {
        $this->entityManager = $entityManager;
        $this->classFounder = $classFounder;

        $this->pageUrlClass = $classFounder->getClass('pageUrl');
        $this->elementUrlClass = $classFounder->getClass('elementUrl');
    }

    public function postPersist(LifecycleEventArgs $event){
        $detail = $event->getEntity();

        if(!($detail instanceof PageDetail || $detail instanceof ElementDetail)){
            return;
        }

        $this->updateUrls($detail);

    }
    public function postUpdate(LifecycleEventArgs $event){
        $detail = $event->getEntity();

        if(!($detail instanceof PageDetail || $detail instanceof ElementDetail)){
            return;
        }


        $this->updateUrls($detail);
    }

    public function postRemove(LifecycleEventArgs $event){
        $detail = $event->getEntity();

        if(!($detail instanceof PageDetail || $detail instanceof ElementDetail)){
            return;
        }

    }

    private function updateUrls(AbstractDetail $detail){

        $parent = $detail->getParent();

        if(null === $url = $parent->getUrl($detail->getLang())){

            $url = ($parent instanceof Page) ? new $this->pageUrlClass : new $this->elementUrlClass;
            $url->setLang($detail->getLang());
            $parent->addUrl($url);
        }
        $urlParent = $this->getParentUrl($parent,$detail->getLang());

        $url->setUrl($urlParent.$detail->getSlug());
        if($parent instanceof Page){
            $this->majChildrenUrls($parent,$detail->getLang());
        }
        $this->entityManager->flush();
    }

    private function getParentUrl($parent,string $lang){
        $urlParent = '';
        if($parent instanceof Page && null !== $parent->getParent()){
            $urlParentObject = $parent->getParent()->getUrl($lang);
            if($urlParentObject !== null){
                $urlParent = $urlParentObject->getUrl().'/';
            }
        }
        return $urlParent;
    }
    private function majChildrenUrls(Page $page,string $lang){

        foreach ($page->getChilds() as $child){
            $detail = $page->getDetail($lang);
            if(!$detail->getId()){
                $detail
                    ->setLang($lang)
                    ->setName($child->getName());
                $child->addDetail($detail);
                $this->entityManager->persist($detail);
                continue;
            }

            if(null === $url = $child->getUrl($lang)){

                $url = new $this->pageUrlClass;
                $url->setLang($lang);
                $child->addUrl($url);
            }

            $urlParent = $this->getParentUrl($detail->getParent(),$lang);
            $url->setUrl($urlParent.$detail->getSlug());
            if(!$url->getId())
                $this->entityManager->persist($url);

            $this->majChildrenUrls($child,$lang);

        }

    }

}