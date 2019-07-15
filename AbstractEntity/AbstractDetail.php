<?php

namespace ScyLabs\NeptuneBundle\AbstractEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @MappedSuperclass
 */
abstract class AbstractDetail
{

    protected $id;
    /**
     * @ORM\Column(type="string", length=2)
     */
    protected $lang;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;
    /**
     * @ORM\Column(type="string", length=255,nullable = true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text",nullable = true)
     */
    protected $description;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128,unique=true)
     */
    protected $slug;


    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug() : ?string{
        return $this->slug;
    }
    public function setSlug(string $slug) : self {
        $this->slug = $slug;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitles(): ?array
    {
        $titles = array();
        foreach ($this as $key => $value){
            if(preg_match('#title#Ui',$key)){
                $titles[$key] = $value;

            }
        }
        return $titles;
    }
    public function getDescriptions(): ?array
    {
        $descriptions = array();
        foreach ($this as $key => $value){
            if(preg_match('#description#Ui',$key)){
                $descriptions[$key] = $value;

            }
        }
        return $descriptions;
    }

    public function __clone(){
        $this->id = null;
        return $this;
    }
}
