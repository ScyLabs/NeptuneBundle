<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractDetail;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\ZoneDetailRepository")
 */
class ZoneDetail extends AbstractDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255,nullable = true)
     */
    protected $title2;

    /**
     * @ORM\Column(type="text", nullable=true,nullable = true)
     */
    protected $description2;
    /**
     * @ORM\Column(type="string", length=255,nullable = true)
     */
    protected $title3;

    /**
     * @ORM\Column(type="text", nullable=true,nullable = true)
     */
    protected $description3;
    /**
     * @ORM\Column(type="string", length=255,nullable = true)
     */
    protected $title4;

    /**
     * @ORM\Column(type="text", nullable=true,nullable = true)
     */
    protected $description4;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Zone", inversedBy="details")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $zone;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle2(): ?string
    {
        return $this->title2;
    }

    public function setTitle2(string $title2): self
    {
        $this->title2 = $title2;

        return $this;
    }

    public function getDescription2(): ?string
    {
        return $this->description2;
    }

    public function setDescription2(?string $description2): self
    {
        $this->description2 = $description2;

        return $this;
    }
    public function getTitle3(): ?string
    {
        return $this->title3;
    }

    public function setTitle3(string $title3): self
    {
        $this->title3 = $title3;

        return $this;
    }

    public function getDescription3(): ?string
    {
        return $this->description3;
    }
    public function setDescription3(?string $description3): self
    {
        $this->description3 = $description3;

        return $this;
    }
    public function getTitle4(): ?string
    {
        return $this->title4;
    }

    public function setTitle4(string $title4): self
    {
        $this->title4 = $title4;

        return $this;
    }

    public function getDescription4(): ?string
    {
        return $this->description4;
    }

    public function setDescription4(?string $description4): self
    {
        $this->description4 = $description4;

        return $this;
    }
    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }
    public function getDescriptions(): array
    {
        $descriptions = array();
        foreach ($this as $key => $value){
            if(preg_match('#description#Ui',$key)){
                $descriptions[$key] = $value;

            }
        }
        return $descriptions;
    }
    public function getTitles() : array{
        $titles = array();
        foreach ($this as $key => $value){
            if(preg_match('#title#Ui',$key)){
                $titles[$key] = $value;

            }
        }
        return $titles;
    }
}
