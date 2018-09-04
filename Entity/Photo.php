<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\PhotoRepository")
 */
class Photo extends AbstractFileLink
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\File", inversedBy="photos")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Page", inversedBy="photos")
     */
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Zone", inversedBy="photos")
     */
    protected $zone;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Element", inversedBy="photos")
     */
    protected $element;
    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Partner", inversedBy="photos")
     */
    protected $partner;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\PhotoDetail", mappedBy="photo", orphanRemoval=true,cascade={"persist","remove"})
     */
    protected $details;

    public function  __construct(){
        $this->details = new ArrayCollection();
    }

    /**
     * @return Collection|ZoneDetail[]
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(PhotoDetail $detail): self
    {
        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setPhoto($this);
        }
        return $this;
    }

    public function removeDetail(PhotoDetail $detail): self
    {
        if ($this->details->contains($detail)) {
            $this->details->removeElement($detail);
            // set the owning side to null (unless already changed)
            if ($detail->getPhoto() === $this) {
                $detail->setPhoto(null);
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
        return new PhotoDetail();
    }
    public function getId()
    {
        return $this->id;
    }
    public function getParent() : ?AbstractElem{
        if($this->page !== null){
            return $this->page;
        }
        elseif($this->element !== null){
            return $this->element;
        }
        elseif($this->zone !== null){
            return $this->zone;
        }
        elseif($this->partner !== null){
            return $this->partner;
        }
        else{
            return null;
        }
    }

}
