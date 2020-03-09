<?php

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractAvancedDetail;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\PageDetailRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PageDetail extends AbstractAvancedDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Page", inversedBy="details",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $page;


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
    public function getParent(){
        return $this->page;
    }
    public function getParentClassName(){
        return 'page';
    }
    public function setParent($parent){
        return $this->page = $parent;
    }

    
   
}
