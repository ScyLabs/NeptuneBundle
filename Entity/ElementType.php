<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElemType;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\ElementTypeRepository")
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
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Element", mappedBy="type", orphanRemoval=true)
     */
    private $elements;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Page",inversedBy="elementTypes")
     */
    private $page;


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
    public function getElements(array $orderBy = array('prio' => Criteria::ASC),bool $showAll = false): Collection
    {
        $criteria = Criteria::create();
        $criteria->orderBy($orderBy);
        if($showAll !== true){
            $criteria->where(Criteria::expr()->eq('remove',false));
        }
        return $this->elements->matching($criteria);
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

    public function getPage() : ?Page{
        return $this->page;
    }
    public function setPage(?Page $page) : self{
        $this->page = $page;
        return $this;
    }
}
