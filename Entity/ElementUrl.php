<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 02/10/2018
 * Time: 16:03
 */

namespace ScyLabs\NeptuneBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="ScyLabs\NeptuneBundle\Repository\ElementUrlRepository")
 */
class ElementUrl
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lang;


    /**
     * @ORM\ManyToOne(targetEntity="ScyLabs\NeptuneBundle\Entity\Element",inversedBy="urls")
     */
    private $element;

    public function getId()
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }
    public function setElement(?Element $element) : self{
        $this->element = $element;
        return $this;
    }
    public function getElement() : Element{
        return $this->element;
    }

}