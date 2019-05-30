<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/08/2018
 * Time: 11:56
 */

namespace ScyLabs\NeptuneBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElem;
use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\PartnerRepository")
 */
class Partner extends AbstractElem
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var
     * @ORM\Column(type="string",nullable=true)
     */
    protected $url;
    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\PartnerDetail", mappedBy="partner", orphanRemoval=true,cascade={"persist","remove"})
     */
    protected $details;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Photo", mappedBy="partner",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $photos;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Document", mappedBy="partner",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $documents;

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\Video", mappedBy="partner",cascade={"persist","remove"})
     * @OrderBy({"prio" = "ASC"})
     */
    protected $videos;


    public function __construct()
    {
        $this->details = new ArrayCollection();
        $this->documents = new ArrayCollection();
        parent::__construct();

    }

    public function getId()
    {
        return $this->id;
    }
    public function getUrl() : ?string{
        return $this->url;
    }
    public function setUrl(?string $url) : self{
        $this->url = $url;
        return $this;
    }

    /**
     * @return Collection|PartnerDetail[]
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(PartnerDetail $detail): self
    {
        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setPartner($this);
        }

        return $this;
    }

    public function removeDetail(PartnerDetail $detail): self
    {
        if ($this->details->contains($detail)) {
            $this->details->removePartner($detail);
            // set the owning side to null (unless already changed)
            if ($detail->getPartner() === $this) {
                $detail->setPartner(null);
            }
        }

        return $this;
    }

    public function getDetail($locale){
        foreach ($this->details as $detail){
            if($detail->getLang() == $locale){
                return $detail;
            }
        }
        return new PartnerDetail();
    }

}
