<?php

namespace ScyLabs\NeptuneBundle\Entity;

use ScyLabs\NeptuneBundle\Form\ValidForm;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;

/**
 *  @MappedSuperclass
 */
abstract class AbstractElem
{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer",nullable = true)
     */
    private $prio;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="boolean")
     */
    private $remove;

    protected $photos;
    protected $videos;
    protected $documents;

    public function __construct()
    {
        if ($this->active === NULL){
            $this->active = true;
        }
        if($this->remove === NULL){
            $this->remove = false;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
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
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setParent($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getParent() === $this) {
                $photo->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): ?Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setParent($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
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
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setPage($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
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
        if($this->photos != null){

            foreach ($this->photos as $photo){
                $tab[] = $photo->getFile()->getId();
            }
        }
        if($this->documents != null){
            foreach ($this->documents as $document){
                $tab[] = $document->getFile()->getId();
            }
        }
        if($this->videos != null){
            foreach ($this->videos as $video){
                $tab[] = $video->getFile()->getId();
            }
        }
        return json_encode($tab);
    }

}
