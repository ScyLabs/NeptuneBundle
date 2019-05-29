<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 27/06/2018
 * Time: 17:14
 */

namespace ScyLabs\NeptuneBundle\AbstractEntity;

use Doctrine\ORM\Mapping\MappedSuperclass;
use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\File;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\Partner;
use ScyLabs\NeptuneBundle\Entity\User;
use ScyLabs\NeptuneBundle\Entity\Zone;
use Doctrine\ORM\Mapping as ORM;


/**
 *  @MappedSuperclass
 */
class AbstractFileLink extends AbstractElem
{

    protected $id;
    /**
     * @ORM\Column(type="string",nullable=true)
     */

    protected $name;
    protected $file;
    protected $page;
    protected $zone;
    protected $element;
    protected $partner;
    protected $user;

    public function __construct(){
        parent::__construct();
    }
    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
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

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getElement(): ?Element
    {
        return $this->element;
    }


    public function setElement(?Element $element): self
    {
        $this->element = $element;

        return $this;
    }
    public function getPartner() : ?Partner{
        return $this->partner;
    }
    public function setPartner(?Partner $partner) : self {
        $this->partner = $partner;
        return $this;
    }
    public function getParent(){
        if($this->page !== null){
            return $this->page;
        }
        elseif($this->zone !== null){
            return $this->zone;
        }
        elseif($this->partner !== null){
            return $this->partner;
        }
        elseif($this->user != null){
            return $this->user;
        }
        else{
            return $this->element;
        }
    }
    public function setParent($parent){
        if($parent instanceof  Page){
            $this->page = $parent;
        }
        elseif($parent instanceof Zone){
            $this->zone = $parent;
        }
        elseif($parent instanceof Partner){
            $this->partner = $parent;
        }
        elseif($parent instanceof User){
            $this->user = $parent;
        }
        else{
            $this->element = $parent;
        }
    }
    public function getPath(){
        return $this->getFile()->getFile();
    }
    public function __clone(){
        $this->id = null;
        return $this;

    }
}