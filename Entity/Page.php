<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElem;
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
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Page", inversedBy="childs")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Page", mappedBy="parent",orphanRemoval=true,cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $childs;

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
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Zone", mappedBy="page",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $zones;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\ElementType",mappedBy="page")
     */
    protected $elementTypes;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\PageDetail", mappedBy="page", orphanRemoval=true,cascade={"persist","remove"})
     */
    protected $details;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\PageType", inversedBy="pages")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $type;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128,unique=true)
     */
    protected $slug;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\PageUrl", mappedBy="page",cascade={"persist","remove","refresh"})
     */

    protected $urls;

    protected $elements;


    public function getElements(array $opts = []){
        if($this->elements === null){
            $criteria = Criteria::create();
            $criteria->where(Criteria::expr()->eq('remove',false));
            $this->elements = new ArrayCollection();
            foreach ($this->elementTypes->matching($criteria) as $elementType){
                foreach ($elementType->getElements() as $element){
                    if( !$this->elements->contains($element)){
                        $this->elements->add($element);
                    }
                }
            }
        }
        $criteria = Criteria::create();

        if(!array_key_exists('remove',$opts))
            $opts['remove'] = false;
        if(sizeof($opts) === 0 && !array_key_exists('active',$opts))
            $opts['active'] = true;
        if(!array_key_exists('order',$opts))
            $opts['order'] = ['prio' => 'ASC'];
        
        $criteria->orderBy($opts['order']);

        $criteria->where(Criteria::expr()->eq('remove',$opts['remove']));

        if(array_key_exists('active',$opts))
            $criteria->andWhere(Criteria::expr()->eq('active',$opts['active']));

        return $this->elements->matching($criteria);
    }

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
        $this->elementTypes = new ArrayCollection();

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
    public function getElementTypes(array $opts = []) : Collection
    {
        $criteria = Criteria::create();

        if(!array_key_exists('remove',$opts))
            $opts['remove'] = false;
        if(!array_key_exists('order',$opts))
            $opts['order'] = ['prio' => 'ASC'];

        $criteria->orderBy($opts['order']);

        $criteria->where(Criteria::expr()->eq('remove',$opts['remove']));



        return $this->elementTypes->matching($criteria);
    }

    public function addElementType(ElementType $elementType): self
    {
        if (!$this->elementTypes->contains($elementType)) {
            $this->elementTypes[] = $elementType;
            $elementType->setPage($this);
        }

        return $this;
    }

    public function removeElementType(ElementType $elementType): self
    {
        if ($this->elementTypes->contains($elementType)) {
            // set the owning side to null (unless already changed)
            if ($elementType->getPage() === $this) {
                $elementType->setPage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Page[]
     */
    public function getChilds(array $opts = []): Collection
    {
        $criteria = Criteria::create();

        if(!array_key_exists('remove',$opts))
            $opts['remove'] = false;
        if(sizeof($opts) === 0 && !array_key_exists('active',$opts))
            $opts['active'] = true;
        if(!array_key_exists('order',$opts))
            $opts['order'] = ['prio' => 'ASC'];

        $criteria->orderBy($opts['order']);

        $criteria->where(Criteria::expr()->eq('remove',$opts['remove']));
        if(array_key_exists('active',$opts))
            $criteria->andWhere(Criteria::expr()->eq('active',$opts['active']));

        return $this->childs->matching($criteria);
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
    public function getZones(array $opts = []): Collection
    {

        $criteria = Criteria::create();

        if(!array_key_exists('remove',$opts))
            $opts['remove'] = false;
        if(sizeof($opts) === 0 && !array_key_exists('active',$opts))
            $opts['active'] = true;
        if(!array_key_exists('order',$opts))
            $opts['order'] = ['prio' => 'ASC'];

        $criteria->orderBy($opts['order']);

        $criteria->where(Criteria::expr()->eq('remove',$opts['remove']));

        if(array_key_exists('active',$opts)){
            $criteria->andWhere(Criteria::expr()->eq('active',$opts['active']));
        }

        return $this->zones->matching($criteria);
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
        if($this->zones->count() > 0){
            foreach ($this->zones as $zone){
                $tab[] =  $zone->getId();
            }
        }
        return json_encode($tab);
    }
    public function getJsonElements(){
        $tab = array();
        if($this->elementTypes->count() > 0){
            foreach ($this->elementTypes as $elementType){
                foreach ($elementType->getElements() as $element){
                    $tab[] = $element->getId();
                }

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

    public function getUrl(string $locale = 'fr') : ?PageUrl{
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
    public function __clone() {

        parent::__clone();

        $details = $this->details;
        $this->details = new ArrayCollection();

        foreach($details as $detail){
            $this->addDetail(clone $detail);
        }
        $childs = $this->childs;
        $this->childs = new ArrayCollection();

        foreach($childs as $child){
            $this->addChild(clone $child);
        }
        $zones = $this->zones;
        $this->zones = new ArrayCollection();
        foreach ($zones as $zone){
            $this->addZone(clone $zone);
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
