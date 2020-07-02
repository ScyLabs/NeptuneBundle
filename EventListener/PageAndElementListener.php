<?php


namespace ScyLabs\NeptuneBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use ScyLabs\NeptuneBundle\Entity\ElementDetail;
use ScyLabs\NeptuneBundle\Entity\PageDetail;

class PageAndElementListener
{
    public function prePersist(LifecycleEventArgs $event){
        $detail = $event->getEntity();

        if(!($detail instanceof PageDetail || $detail instanceof ElementDetail)){
            return;
        }

      //  $this->updateUrls($detail);

    }
}