<?php

namespace App\ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\ScyLabs\NeptuneBundle\Repository\PhotoRepository")
 */
class Photo extends AbstractFileLink
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\File", inversedBy="photos")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Page", inversedBy="photos")
     */
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Zone", inversedBy="photos")
     */
    protected $zone;

    /**
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Element", inversedBy="photos")
     */
    protected $element;

    public function getId()
    {
        return $this->id;
    }

}
