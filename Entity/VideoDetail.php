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
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\VideoDetailRepository")
 */
class VideoDetail extends AbstractDetail
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Video", inversedBy="details")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $video;

    public function getId(){
        return $this->id;
    }
    public function setVideo(Video $video) : self{
        $this->video = $video;
        return $this;
    }
    public function getVideo() : Video{
        return $this->video;
    }

}