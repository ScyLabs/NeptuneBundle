<?php

namespace App\ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

/**
 * @MappedSuperclass
 */
abstract class AbstractAvancedDetail extends AbstractDetail
{

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $h1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metaTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaDesc;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaKeys;

    public function getId()
    {
        return $this->id;
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

    public function getH1(): ?string
    {
        return $this->h1;
    }

    public function setH1(?string $h1): self
    {
        $this->h1 = $h1;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDesc(): ?string
    {
        return $this->metaDesc;
    }

    public function setMetaDesc(?string $metaDesc): self
    {
        $this->metaDesc = $metaDesc;

        return $this;
    }

    public function getMetaKeys(): ?string
    {
        return $this->metaKeys;
    }

    public function setMetaKeys(?string $metaKeys): self
    {
        $this->metaKeys = $metaKeys;

        return $this;
    }
}
