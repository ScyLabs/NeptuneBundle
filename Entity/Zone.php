<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractChild;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElem;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\ZoneRepository")
 */
class Zone extends AbstractChild
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Page", inversedBy="zones")
     */
    protected $page;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Photo", mappedBy="zone",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $photos;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Document", mappedBy="zone",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $documents;


    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Video", mappedBy="zone",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $videos;


    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\ZoneType", inversedBy="zones")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $type;


    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\ZoneDetail", mappedBy="zone", orphanRemoval=true,cascade={"persist","remove"})
     */
    protected $details;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Element", inversedBy="zones")
     */
    protected $element;

    /**
     * @ORM\ManyToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Form", mappedBy="zones")
     */
    protected $forms;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    protected $icon;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Page")
     */
    protected $pageLink;

    /**
     * @ORM\Column(type="string",length=255,nullable = false)
     */
    protected $subType;

    /**
     * @ORM\Column(type="integer",length=255,nullable=false)
     */
    protected $typeHead;


    public function __construct()
    {
        $this->details = new ArrayCollection();
        if($this->subType === null){
            $this->subType = 'subtype1';
        }
        if($this->typeHead === null){
            $this->typeHead = '2';
        }

        parent::__construct();
    }
    public function getId()
    {
        return $this->id;
    }
    public function getTypeHead() : int{
        return $this->typeHead;
    }
    public function setTypeHead(int $typeHead) :self{
        $this->typeHead = $typeHead;
        return $this;
    }
    public function getIcon() : ?string{
        return $this->icon;
    }
    public function setIcon(?string $icon) : self{
        $this->icon = $icon;
        return $this;
    }
    public function setPageLink(?Page $page) : self{
        $this->pageLink = $page;
        return $this;
    }
    public function getPageLink() : ?Page{
        return $this->pageLink;
    }

    public function getSubType() : string{
        return $this->subType;
    }
    public function setSubType(string $subType) : self{
        $this->subType = $subType;
        return $this;
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

    public function __clone(){
        parent::__clone();

        $details = $this->details;
        $this->details = new ArrayCollection();
        foreach ($details as $detail){
            $this->addDetail(clone $detail);
        }

        $photos = $this->photos;
        $this->photos = new ArrayCollection();
        foreach ($photos as $photo){
            $this->addPhoto(clone $photo);
        }

        $documents = $this->documents;
        $this->documents = new ArrayCollection();
        foreach ($documents as $document){
            $this->addDocument(clone $document);
        }

        $videos = $this->videos;
        $this->videos = new ArrayCollection();
        foreach ($videos as $video){
            $this->addVideo(clone $video);
        }

        return $this;
    }

}
