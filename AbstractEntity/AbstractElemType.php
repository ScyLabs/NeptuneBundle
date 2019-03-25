<?php

namespace ScyLabs\NeptuneBundle\AbstractEntity;

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

    /**
     * @ORM\Column(type="string", length=191,unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $remove;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $removable;

    public function __construct(){
        if($this->remove === NULL){
            $this->remove = false;
        }
        if($this->removable === null){
            $this->removable = true;
        }
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
    public function toArray(){
        $array = new ArrayCollection();
        foreach ($this as $key => $value){
            $array->add($key);
        }

        return $array;
    }
}
