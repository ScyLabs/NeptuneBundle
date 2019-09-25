<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElemType;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\ZoneTypeRepository")
 */
class ZoneType extends AbstractElemType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Zone", mappedBy="type", orphanRemoval=true)
     */
    protected $zones;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $codexHash;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $codexId;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $version;


    public function getCodexHash() : ?string {
        return $this->codexHash;
    }
    public function setCodexHash(?string $codexHash) : self {
        $this->codexHash = $codexHash;
        return $this;
    }

    public function getCodexId() : ?int{
        return $this->codexId;
    }
    public function setCodexId(?int $codexId) : self{
        $this->codexId = $codexId;
        return $this;
    }
    public function getVersion() : ?int{
        return $this->version;
    }
    public function setVersion(?int $version) : self{
        $this->version = $version;
        return $this;
    }

    public function __construct()
    {
        $this->zones = new ArrayCollection();
        if(null === $this->version)
            $this->version = 0;
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Zone[]
     */
    public function getZones(): Collection
    {
        return $this->zones;
    }

    public function addZone(Zone $zone): self
    {
        if (!$this->zones->contains($zone)) {
            $this->zones[] = $zone;
            $zone->setType($this);
        }

        return $this;
    }

    public function removeZone(Zone $zone): self
    {
        if ($this->zones->contains($zone)) {
            $this->zones->removeElement($zone);
            // set the owning side to null (unless already changed)
            if ($zone->getType() === $this) {
                $zone->setType(null);
            }
        }

        return $this;
    }
}

