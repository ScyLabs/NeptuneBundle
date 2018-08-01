<?php

namespace Scylabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Scylabs\NeptuneBundle\Repository\PageDetailRepository")
 */
class PageDetail extends AbstractAvancedDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Scylabs\NeptuneBundle\Entity\Page", inversedBy="details")
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

    public function getId()
    {
        return $this->id;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }
}
