<?php

namespace Scylabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Scylabs\NeptuneBundle\Repository\ElementDetailRepository")
 */
class ElementDetail extends AbstractAvancedDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Scylabs\NeptuneBundle\Entity\Element", inversedBy="details")
     * @ORM\JoinColumn(nullable=false)
     */
    private $element;

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
}
