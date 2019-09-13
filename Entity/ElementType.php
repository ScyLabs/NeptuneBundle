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
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Element", mappedBy="type", orphanRemoval=true)
     */
    protected $elements;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Page",inversedBy="elementTypes")
     */
    protected $page;

    /**
     * @ORM\Column(type="integer",nullable = true)
     */
    protected $prio;


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
    public function getElements(array $opts = []){

        $criteria = Criteria::create();

        if(!array_key_exists('remove',$opts))
            $opts['remove'] = false;
        if(!array_key_exists('active',$opts))
            $opts['active'] = true;
        if(!array_key_exists('order',$opts))
            $opts['order'] = ['prio' => 'ASC'];

        $criteria->orderBy($opts['order']);

        $criteria->where(Criteria::expr()->eq('remove',$opts['remove']));
        if(null !== $opts['active'])
            $criteria->andWhere(Criteria::expr()->eq('active',$opts['active']));

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

    public function getPrio(): ?int
    {
        return $this->prio;
    }

    public function setPrio(int $prio): self
    {
        $this->prio = $prio;

        return $this;
    }
}
