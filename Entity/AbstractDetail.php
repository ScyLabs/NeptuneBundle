<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
/**
 * @MappedSuperclass
 */
abstract class AbstractDetail
{


    /**
     * @ORM\Column(type="string", length=2)
     */
    private $lang;

    /**
     * @ORM\Column(type="string", length=255,nullable = true)
     */
    private $title;

    /**
     * @ORM\Column(type="text",nullable = true)
     */
    private $description;

    public function getId()
    {
        return $this->id;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
