<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 28/06/2018
 * Time: 14:11
 */

namespace Scylabs\NeptuneBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\MappedSuperclass;


abstract class AbstractChild extends AbstractElem
{

    protected $page;

    protected $type;

    protected $forms;


    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->forms = new ArrayCollection();
        parent::__construct();
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

    /**
     * @return Collection|Form[]
     */
    public function getForms(): ?Collection
    {
        return $this->forms;
    }

    public function addForm(Form $form): self
    {
        if (!$this->forms->contains($form)) {
            $this->forms[] = $form;
            $form->addChild($this);
        }

        return $this;
    }

    public function removeForm(Form $form)
    {
        if ($this->forms->contains($form)) {
            $this->forms->removeElement($form);
            $form->removeChild($this);
        }

        return $this;
    }
    public function setParent(AbstractElem $parent) {
        $this->page = $parent;
        return $this;
    }
    public function getparent() :? AbstractElem{
         return $this->page;
    }

    public function getType(): ?AbstractElemType
    {
        return $this->type;
    }

    public function setType(?AbstractElemType $type)
    {
        $this->type = $type;

        return $this;
    }

}