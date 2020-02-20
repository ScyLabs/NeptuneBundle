<?php

namespace ScyLabs\NeptuneBundle\AbstractEntity;

use Doctrine\Common\Collections\ArrayCollection;
use ScyLabs\NeptuneBundle\Entity\Document;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\Photo;
use ScyLabs\NeptuneBundle\Entity\Video;
use ScyLabs\NeptuneBundle\Form\ValidForm;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;


abstract class AbstractElem
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer",nullable = true)
     */
    protected $prio;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $remove;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDate;


    public function __construct()
    {
        if ($this->active === NULL){
            $this->active = true;
        }
        if($this->remove === NULL){
            $this->remove = false;
        }
        $this->photos = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->documents = new ArrayCollection();
        
        $this->creationDate = new \DateTime('now');
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getRemove(): ?bool
    {
        return $this->remove;
    }

    public function setRemove(bool $remove)
    {
        $this->remove = $remove;

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhotos(): ?Collection
    {
        if(!property_exists($this,'photos'))
            return new ArrayCollection();
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if(!property_exists($this,'photos'))
            return $this;

        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setParent($this);
        }

        return $this;
    }
    public function addFile(AbstractFileLink $file){

        if($file instanceof Photo){
            $this->addPhoto($file);
        }
        elseif($file instanceof Document){
            $this->addDocument($file);
        }
        else{
            $this->addVideo($file);
        }
    }

    public function removePhoto(Photo $photo): self
    {
        if(!property_exists($this,'photos'))
            return $this;
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getParent() === $this) {
                $photo->setParent(null);
            }
        }

        return $this;
    }

    public function getFiles(){
        $files = new ArrayCollection();
        if($this->photos != null)
        foreach ($this->photos as $photo){
            if($photo instanceof Photo){
                if(!$files->contains($photo->getFile()))
                    $files->add($photo->getFile());
            }
        }
        foreach ($this->documents as $document){
            if($document instanceof Document){
                if(!$files->contains($document->getFile()))
                    $files->add($document->getFile());
            }
        }
        foreach ($this->videos as $video){
            if($video instanceof Video){
                if(!$files->contains($video->getFile()))
                    $files->add($video->getFile());
            }
        }
        return $files;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): ?Collection
    {
        if(!property_exists($this,'documents'))
            return new ArrayCollection();
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if(!property_exists($this,'documents'))
            return $this;
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setParent($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if(!property_exists($this,'documents'))
            return $this;
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getParent() === $this) {
                $document->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): ?Collection
    {
        if(!property_exists($this,'videos'))
            return new ArrayCollection();
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if(!property_exists($this,'videos'))
            return $this;
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setParent($this);

        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if(!property_exists($this,'videos'))
            return $this;
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getPage() === $this) {
                $video->setPage(null);
            }
        }

        return $this;
    }

    public function getJsonFiles(){

        $tab = array();
        if(property_exists($this,'photos') && $this->photos != null){

            foreach ($this->photos as $photo){
                $tab[] = $photo->getFile()->getId();
            }
        }
        if(property_exists($this,'documents') && $this->documents != null){
            foreach ($this->documents as $document){
                $tab[] = $document->getFile()->getId();
            }
        }
        if(property_exists($this,'videos') && $this->videos != null){
            foreach ($this->videos as $video){
                $tab[] = $video->getFile()->getId();
            }
        }
        return json_encode($tab);
    }

    public function toArray(){
        $array = new ArrayCollection();
        foreach ($this as $key => $value){
            $array->add($key);
        }

        return $array;
    }
    public function __clone() {
        $this->creationDate = new \DateTime('now');
        $this->id = null;
        $this->prio++;
        return $this;
    }

}
