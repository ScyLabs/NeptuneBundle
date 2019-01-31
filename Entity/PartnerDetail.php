<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14/08/2018
 * Time: 11:59
 */

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractDetail;
use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\PartnerDetailRepository")
 */
class PartnerDetail extends AbstractDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Partner", inversedBy="details")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $partner;


    public function getId()
    {
        return $this->id;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }
}