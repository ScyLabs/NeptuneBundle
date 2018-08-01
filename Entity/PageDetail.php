<?php

namespace App\ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\ScyLabs\NeptuneBundle\Repository\PageDetailRepository")
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
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Page", inversedBy="details")
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
