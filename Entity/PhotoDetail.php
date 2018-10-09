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
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\PhotoDetailRepository")
 */
class PhotoDetail extends AbstractDetail
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Photo", inversedBy="details")
     * @ORM\JoinColumn(nullable=false)
     */
    private $photo;

    public function getId(){
        return $this->id;
    }
    public function setPhoto(Photo $photo) : self{
        $this->photo = $photo;
        return $this;
    }
    public function getPhoto() : Photo{
        return $this->photo;
    }

}