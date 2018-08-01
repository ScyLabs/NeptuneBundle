<?php

namespace App\ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping\MappedSuperclass;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 *  @MappedSuperclass
 * @UniqueEntity("name")
 */

abstract class AbstractElemType
{
    public function __construct(){
        if($this->remove === NULL){
            $this->remove = false;
        }
        if($this->removable === null){
            $this->removable = true;
        }
    }

    /**
     * @ORM\Column(type="string", length=191,unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="boolean")
     */
    private $remove;

    /**
     * @ORM\Column(type="boolean")
     */
    private $removable;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getRemovable(): ?bool
    {
        return $this->removable;
    }

    public function setRemovable(bool $removable): self
    {
        $this->removable = $removable;

        return $this;
    }
}
