<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractFileLink;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\VideoRepository")
 */
class Video extends AbstractFileLink
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\File", inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Page", inversedBy="videos")
     */
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Zone", inversedBy="videos")
     */
    protected $zone;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Element", inversedBy="videos")
     */
    protected $element;
    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Partner", inversedBy="videos")
     */
    protected $partner;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\User", inversedBy="videos")
     */
    protected $user;
    /**

    /**
     * @ORM\OneToMany(targetEntity="ScyLabs\NeptuneBundle\Entity\VideoDetail", mappedBy="document", orphanRemoval=true,cascade={"persist","remove"})
     */
    protected $details;

    public function addDetail(VideoDetail $detail): self
    {
        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setVideo($this);
        }
        return $this;
    }

    public function removeDetail(VideoDetail $detail): self
    {
        if ($this->details->contains($detail)) {
            $this->details->removeElement($detail);
            // set the owning side to null (unless already changed)
            if ($detail->getVideo() === $this) {
                $detail->setVideo(null);
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
        return new VideoDetail();
    }
    public function getId()
    {
        return $this->id;
    }

}
