<?php

namespace App\ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\ScyLabs\NeptuneBundle\Repository\FileRepository")
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $file;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\FileType", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Photo", mappedBy="file", orphanRemoval=true,cascade={"remove"})
     */
    private $photos;

    /**
     * @ORM\OneToMany(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Document", mappedBy="file", orphanRemoval=true,cascade={"remove"})
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Video", mappedBy="file", orphanRemoval=true,cascade={"remove"})
     */
    private $videos;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $ext;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }


    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setRemove(bool $remove): self
    {
        $this->remove = $remove;

        return $this;
    }

    public function getType(): ?FileType
    {
        return $this->type;
    }

    public function setType(?FileType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setFile($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getFile() === $this) {
                $photo->setFile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setFile($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getFile() === $this) {
                $document->setFile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setFile($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getFile() === $this) {
                $video->setFile(null);
            }
        }

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file) : ?self
    {
        $this->file = $file;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getExt(): ?string
    {
        return $this->ext;
    }

    public function setExt(string $ext): self
    {
        $this->ext = $ext;

        return $this;
    }
}
