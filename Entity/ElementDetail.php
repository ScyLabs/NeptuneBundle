<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractAvancedDetail;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\ElementDetailRepository")
 */
class ElementDetail extends AbstractAvancedDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Element", inversedBy="details")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $element;

    public function getId()
    {
        return $this->id;
    }

    public function getElement(): ?Element
    {
        return $this->element;
    }

    public function setElement(?Element $element): self
    {
        $this->element = $element;

        return $this;
    }
    public function getParent(){
        return $this->element;
    }
    public function getParentClassName(){
        return 'element';
    }
    public function setParent($parent){
        return $this->element = $parent;
    }
}
