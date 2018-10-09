<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElemType;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\FileTypeRepository")
 */
class FileType extends AbstractElemType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\File", mappedBy="type")
     */
    private $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setType($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getType() === $this) {
                $file->setType(null);
            }
        }

        return $this;
    }
}
