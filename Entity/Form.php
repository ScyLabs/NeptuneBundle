<?php

namespace App\ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\ScyLabs\NeptuneBundle\Repository\FormRepository")
 */
class Form
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
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Zone", inversedBy="forms")
     */
    private $zones;

    /**
     * @ORM\OneToMany(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Field", mappedBy="form", orphanRemoval=true)
     */
    private $fields;

    public function __construct()
    {
        $this->zones = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }

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
        }

        return $this;
    }
    public function addChild(AbstractChild $child) : self{
        if($child instanceof Zone){
            if(!$this->zones->contains($child)){
                $this->zones[]  = $child;
            }
        }
        return $this;
    }
    public function removeChild(AbstractChild $child) : self{
        if($child instanceof Zone){
            if($this->zones->contains($child)){
                $this->zones->removeElement($child);
            }
        }
        return $this;
    }
    public function removeZone(Zone $zone): self
    {
        if ($this->zones->contains($zone)) {
            $this->zones->removeElement($zone);
        }

        return $this;
    }

    /**
     * @return Collection|Field[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Field $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->setForm($this);
        }

        return $this;
    }

    public function removeField(Field $field): self
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
            // set the owning side to null (unless already changed)
            if ($field->getForm() === $this) {
                $field->setForm(null);
            }
        }

        return $this;
    }
}
