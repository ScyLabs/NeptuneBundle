<?php

namespace Scylabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Scylabs\NeptuneBundle\Repository\ElementTypeRepository")
 */
class ElementType extends AbstractElemType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer",length=191)
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Scylabs\NeptuneBundle\Entity\Element", mappedBy="type", orphanRemoval=true)
     */
    private $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Element[]
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements[] = $element;
            $element->setType($this);
        }

        return $this;
    }

    public function removeElement(Element $element): self
    {
        if ($this->elements->contains($element)) {
            $this->elements->removeElement($element);
            // set the owning side to null (unless already changed)
            if ($element->getType() === $this) {
                $element->setType(null);
            }
        }

        return $this;
    }
}
