<?php

namespace App\ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\ScyLabs\NeptuneBundle\Repository\VideoRepository")
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
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\File", inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Page", inversedBy="videos")
     */
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Zone", inversedBy="videos")
     */
    protected $zone;

    /**
     * @ORM\ManyToOne(targetEntity="App\ScyLabs\NeptuneBundle\Entity\Element", inversedBy="videos")
     */
    protected $element;

    public function getId()
    {
        return $this->id;
    }


}
