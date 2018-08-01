<?php

namespace Scylabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Scylabs\NeptuneBundle\Repository\ZoneRepository")
 */
class Zone extends AbstractChild
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Scylabs\NeptuneBundle\Entity\Page", inversedBy="zones")
     */
    protected $page;

    /**
     * @ORM\OneToMany(targetEntity="Scylabs\NeptuneBundle\Entity\Photo", mappedBy="zone",cascade={"persist","remove"})
     */
    protected $photos;

    /**
     * @ORM\OneToMany(targetEntity="Scylabs\NeptuneBundle\Entity\Document", mappedBy="zone",cascade={"persist","remove"})
     */
    protected $documents;


    /**
     * @ORM\OneToMany(targetEntity="Scylabs\NeptuneBundle\Entity\Video", mappedBy="zone",cascade={"persist","remove"})
     */
    protected $videos;


    /**
     * @ORM\ManyToOne(targetEntity="Scylabs\NeptuneBundle\Entity\ZoneType", inversedBy="zones")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $type;


    /**
     * @ORM\OneToMany(targetEntity="Scylabs\NeptuneBundle\Entity\ZoneDetail", mappedBy="zone", orphanRemoval=true,cascade={"persist","remove"})
     */
    protected $details;

    /**
     * @ORM\ManyToOne(targetEntity="Scylabs\NeptuneBundle\Entity\Element", inversedBy="zones")
     */
    private $element;

    /**
     * @ORM\ManyToMany(targetEntity="Scylabs\NeptuneBundle\Entity\Form", mappedBy="zones")
     */
    protected $forms;


    public function __construct()
    {
        $this->details = new ArrayCollection();

        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|ZoneDetail[]
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(ZoneDetail $detail): self
    {
        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setZone($this);
        }

        return $this;
    }

    public function removeDetail(ZoneDetail $detail): self
    {
        if ($this->details->contains($detail)) {
            $this->details->removeElement($detail);
            // set the owning side to null (unless already changed)
            if ($detail->getZone() === $this) {
                $detail->setZone(null);
            }
        }

        return $this;
    }
    public function getDetail($locale){
        foreach ($this->details as $detail){
            if($detail->getLang() == $locale){
                return $detail;
            }
        }
        return new ZoneDetail();
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
    public function setParent(AbstractElem $parent) : self{

        if($parent instanceof Page){
            $this->setPage($parent);
            return $this;
        }
        elseif($parent instanceof Element){
            $this->setElement($parent);
            return $this;
        }
        return $this;

    }
    public function getParent() : ?AbstractElem{
        if($this->page !== null){
            return $this->page;
        }
        elseif($this->element !== null){
            return $this->element;
        }
        else{
            return null;
        }
    }

}
