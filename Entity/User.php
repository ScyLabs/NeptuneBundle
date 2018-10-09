<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\UserRepository")
 */
class User extends BaseUser
{


    /**
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $civility;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $name;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adress;

    /**
     * @ORM\Column(type="boolean")
     */
    private $firstConnexion;
    /**
     * @ORM\Column(type="boolean")
     */
    private $remove;

    private $tmpRole;
    public function setTmpRole(string $role) : self{
        $this->tmpRole = $role;
        return $this;
    }
    public function getTmpRole(){
        return $this->tmpRole;
    }

    public function __construct(){
        if($this->firstConnexion === null){
            $this->firstConnexion = true;
        }
        if($this->enabled === null){
            $this->enabled = true;
        }
        if($this->remove === null){
            $this->remove = false;
        }
    }


    public function getRemove(){
        return $this->remove;
    }
    public function setRemove(bool $remove) : self{
        $this->remove = $remove;
        return $this;
    }


    public function getId() :?int{
        return $this->id;
    }
    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(?string $civility): self
    {
        $this->civility = $civility;

        return $this;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

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

    public function getFirstConnexion(): ?bool
    {
        return $this->firstConnexion;
    }

    public function setFirstConnexion(bool $firstConnexion): self
    {
        $this->firstConnexion = $firstConnexion;

        return $this;
    }

    public function getEnabled() : bool {
        return $this->enabled;
    }


}