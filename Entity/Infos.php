<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\InfosRepository")
 */
class Infos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $adress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $cp;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $siret;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $codeApe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $phone;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $fax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $mobile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $gmap;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $facebook;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $twitter;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $insta;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $resa;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $analyticsId;


    public function getId()
    {
        return $this->id;
    }

    public function getAnalyticsId() : ?string{
        return $this->analyticsId;
    }
    public function setAnalyticsId(?string $analyticsId) : self{
        $this->analyticsId = $analyticsId;
        return $this;
    }
    public function getResa() : ?string{
        return $this->resa;
    }
    public function setResa(string $resa) : self{
        $this->resa = $resa;
        return $this;
    }

    public function getTel(){
        return $this->phone;
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

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }
    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }
    public function getCodeApe(): ?string
    {
        return $this->codeApe;
    }

    public function setCodeApe(?string $codeApe): self
    {
        $this->codeApe = $codeApe;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getGmap(): ?string
    {
        return $this->gmap;
    }

    public function setGmap(?string $gmap): self
    {
        $this->gmap = $gmap;

        return $this;
    }
    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }
    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }
    public function getInsta(): ?string
    {
        return $this->insta;
    }
    public function setInsta(?string $insta): self
    {
        $this->insta = $insta;

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
