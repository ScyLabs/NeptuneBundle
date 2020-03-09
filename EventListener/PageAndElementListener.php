<?php


namespace ScyLabs\NeptuneBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Model\ClassFounderInterface;

class PageAndElementListener
{

    private $classFounder;

    private $elementDetailClass;

    public function __construct(ClassFounderInterface $classFounder) {
        $this->classFounder = $classFounder;

    }

    public function preUpdate(LifecycleEventArgs $event){
        $object = $event->getEntity();

        if(!($object instanceof Page || $object instanceof Element)){
            return;
        }

        $exp = explode('\\',get_class($object));
        $className =
        dump(basename(get_class($object)));
        die('ok');
    }
}