<?php

namespace App\ScyLabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\ScyLabs\NeptuneBundle\Repository\PageTypeRepository")
 */
class PageType extends AbstractElemType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Page", mappedBy="type", orphanRemoval=true,cascade={"persist","remove"})
     */
    private $pages;

    public function __construct()
    {
        $this->type = new ArrayCollection();
        $this->pages = new ArrayCollection();
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setType($this);
        }

        return $this;
    }
    public function setRemove(bool $remove) :self{

        parent::setRemove($remove);
        if($this->pages !== null){
            foreach ($this->pages as $page){
                $page->setRemove($remove);
            }
        }
        return $this;
    }
    public function removePage(Page $page): self
    {
        if ($this->pages->contains($page)) {
            $this->pages->removeElement($page);
            // set the owning side to null (unless already changed)
            if ($page->getType() === $this) {
                $page->setType(null);
            }
        }

        return $this;
    }


}
