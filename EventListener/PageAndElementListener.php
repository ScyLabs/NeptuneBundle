<?php


namespace ScyLabs\NeptuneBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;

class PageListener
{
    public function prePersist(LifecycleEventArgs $event){
        $detail = $event->getEntity();

        if(!($detail instanceof PageDetail || $detail instanceof ElementDetail)){
            return;
        }

        $this->updateUrls($detail);

    }
}