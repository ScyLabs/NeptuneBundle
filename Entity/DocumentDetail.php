<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 04/09/2018
 * Time: 10:35
 */

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractDetail;

/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\DocumentDetailRepository")
 */
class DocumentDetail extends AbstractDetail
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Document", inversedBy="details")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $document;

    public function getId(){
        return $this->id;
    }
    public function setDocument(Document $document) : self{
        $this->document = $document;
        return $this;
    }
    public function getDocument() : Document{
        return $this->document;
    }
    public function getParent(){
        return $this->document;
    }
    public function setParent($parent){
        return $this->document = $parent;
    }
    public function getParentClassName(){
        return 'document';
    }

}