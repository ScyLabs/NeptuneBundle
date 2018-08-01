<?php

namespace Scylabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Scylabs\NeptuneBundle\Repository\DocumentRepository")
 */
class Document extends AbstractFileLink
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Scylabs\NeptuneBundle\Entity\File", inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="Scylabs\NeptuneBundle\Entity\Page", inversedBy="documents")
     */
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="Scylabs\NeptuneBundle\Entity\Zone", inversedBy="documents")
     */
    protected $zone;

    /**
     * @ORM\ManyToOne(targetEntity="Scylabs\NeptuneBundle\Entity\Element", inversedBy="documents")
     */
    protected $element;

    public function getId()
    {
        return $this->id;
    }

}
