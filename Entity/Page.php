<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\PageRepository")
 */
class Page extends AbstractElem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Page", inversedBy="childs")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Page", mappedBy="parent",orphanRemoval=true,cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    private $childs;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Photo", mappedBy="page",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $photos;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Document", mappedBy="page",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $documents;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Video", mappedBy="page",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $videos;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Zone", mappedBy="page")
     * @OrderBy({"prio" = "ASC"})
     */
    private $zones;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Element", mappedBy="page")
     * @OrderBy({"prio" = "ASC"})
     */
    private $elements;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\PageDetail", mappedBy="page", orphanRemoval=true,cascade={"persist","remove"})
     */
    private $details;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\PageType", inversedBy="pages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128,unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\PageUrl", mappedBy="page",cascade={"persist","remove","refresh"})
     */

    private $urls;




    public function __construct()
    {
        parent::__construct();
        $this->childs = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->zones = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->details = new ArrayCollection();
        $this->urls = new ArrayCollection();

    }
    public function getId()
    {
        return $this->id;
    }

    /*Fonction de serialization*/


    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
    public function setRemove(bool $remove): self
    {
        parent::setRemove($remove);

        if($this->childs !== null){
            foreach ($this->childs as $page){
                $page->setRemove($remove);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Page[]
     */
    public function getChilds(): Collection
    {
        return $this->childs;
    }

    public function addChild(Page $child): self
    {
        if (!$this->childs->contains($child)) {
            $this->childs[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Page $child): self
    {
        if ($this->childs->contains($child)) {
            $this->childs->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|Zone[]
     */
    public function getZones(): Collection
    {
        return $this->zones;
    }

    public function addZone(Zone $zone): self
    {
        if (!$this->zones->contains($zone)) {
            $this->zones[] = $zone;
            $zone->setPage($this);
        }

        return $this;
    }

    public function removeZone(Zone $zone): self
    {
        if ($this->zones->contains($zone)) {
            $this->zones->removeElement($zone);
            // set the owning side to null (unless already changed)
            if ($zone->getPage() === $this) {
                $zone->setPage(null);
            }
        }

        return $this;
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
            $element->setPage($this);
        }

        return $this;
    }
    public function getElementsTypes() : self{
        $types = new ArrayCollection();
        foreach ($this->elements as $element){
            if(!$types->contains($element->getType())){
                $types->add($element->getType());
            }
        }
        return $types;
    }

    public function removeElement(Element $element): self
    {
        if ($this->elements->contains($element)) {
            $this->elements->removeElement($element);
            // set the owning side to null (unless already changed)
            if ($element->getPage() === $this) {
                $element->setPage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PageDetail[]
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(PageDetail $detail): self
    {
        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setPage($this);
        }

        return $this;
    }

    public function removeDetail(PageDetail $detail): self
    {
        if ($this->details->contains($detail)) {
            $this->details->removeElement($detail);
            // set the owning side to null (unless already changed)
            if ($detail->getPage() === $this) {
                $detail->setPage(null);
            }
        }

        return $this;
    }

    public function getType(): ?PageType
    {
        return $this->type;
    }

    public function setType(?PageType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getJsonZones(){
        $tab = array();
        if($this->zones !== null){
            foreach ($this->zones as $zone){
                $tab[] =  $zone->getId();
            }
        }
        return json_encode($tab);
    }
    public function getJsonElements(){
        $tab = array();
        if($this->zones !== null){
            foreach ($this->elements as $element){
                $tab[] =  $element->getId();
            }
        }
        return json_encode($tab);
    }

    public function getDetail($locale){
        foreach ($this->details as $detail){
            if($detail->getLang() == $locale){
                return $detail;
            }
        }
        return new PageDetail();
    }

    /**
     * @return Collection|PageUrl[]
     */
    public function getUrls(): Collection
    {
        return $this->urls;
    }

    public function getUrl($locale) : ?PageUrl{
        foreach ($this->urls as $url){
            if($url->getLang() == $locale){
                return $url;
            }
        }
        return null;
    }

    public function addUrl(PageUrl $url): self
    {
        if (!$this->urls->contains($url)) {
            $this->urls[] = $url;
            $url->setPage($this);
        }

        return $this;
    }

    public function removeUrl(PageUrl $url): self
    {
        if ($this->urls->contains($url)) {
            $this->urls->removeElement($url);
            // set the owning side to null (unless already changed)
            if ($url->getPage() === $this) {
                $url->setPage(null);
            }
        }

        return $this;
    }

}
