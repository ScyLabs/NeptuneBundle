<?php

namespace ScyLabs\NeptuneBundle\AbstractEntity;


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
    protected $h1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $metaTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metaDesc;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $metaKeys;


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
