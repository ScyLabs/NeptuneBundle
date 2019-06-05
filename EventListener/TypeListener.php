<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 23/05/2019
 * Time: 11:15
 */

namespace ScyLabs\NeptuneBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElemType;
use ScyLabs\NeptuneBundle\Services\CleanText;

class TypeListener
{

    private $cleanText;

    public function __construct(CleanText $cleanText){
        $this->cleanText = $cleanText;

    }

    public function prePersist(LifecycleEventArgs $args){

        $object = $args->getEntity();
        if($object instanceof AbstractElemType){
            $object->setName($this->cleanText->clean($object->getName()));
        }
    }

    public function preUpdate(PreUpdateEventArgs $args){
        $object = $args->getEntity();

        if($object instanceof AbstractElemType){
            $object->setName($this->cleanText->clean($object->getName()));
        }
    }

}