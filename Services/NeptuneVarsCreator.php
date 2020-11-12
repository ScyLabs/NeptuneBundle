<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/09/2019
 * Time: 15:02
 */

namespace ScyLabs\NeptuneBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractElem;
use ScyLabs\NeptuneBundle\AbstractEntity\AbstractFileLink;
use ScyLabs\NeptuneBundle\Entity\Document;
use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\ElementType;
use ScyLabs\NeptuneBundle\Entity\File;
use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageType;
use ScyLabs\NeptuneBundle\Entity\PageUrl;
use ScyLabs\NeptuneBundle\Entity\Photo;
use ScyLabs\NeptuneBundle\Entity\Video;
use ScyLabs\NeptuneBundle\Entity\Zone;
use ScyLabs\NeptuneBundle\Entity\ZoneType;
use ScyLabs\NeptuneBundle\Model\NeptuneVarsCreatorInterface;

class NeptuneVarsCreator implements NeptuneVarsCreatorInterface
{
    private $zoneType;
    const locale = "fr";
    const title = "Lorem ipsum dolor sit amet.";
    const desc = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam dignissim maximus nisl id faucibus. Duis nec diam congue, mollis ipsum a, condimentum mi. Nulla dignissim est sit amet nunc egestas dapibus. Integer diam dui, pulvinar in purus non, pulvinar mollis arcu. Nam et ex varius, accumsan mi sed, pharetra lorem. Nulla facilisi.";
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function initVars(ZoneType $zoneType) : array{
        $this->zoneType = $zoneType;
        return array(
            'locale'    => self::locale ,
            'zone'  => $this->initZone(),
            'page'  => $this->initPage(),
            'pages' => [$this->initPage(),$this->initPage(),$this->initPage(),$this->initPage(),$this->initPage()],
            'infos' =>  new Infos()
        );
    }

    // Page Initialization,
    private function initPage(){
        $page = new Page();
        $this->init($page);
        $page->setType(new PageType());
        $elementType = new ElementType();
        $elementType->setName(self::title)->setTitle(self::title)->setRemove(false)->setRemovable(false);
        for($i = 0;$i < 10;$i++){
            $elementType->addElement($this->initElement());
        }
        $page->addElementType($elementType);
        $page->addUrl((new PageUrl())->setLang('fr')->setUrl('cette-page'));
        return $page;
    }

    // Zone Initialization
    private function initZone(){
        $zone = new Zone();
        $this->init($zone);
        $zone
            ->setType($this->zoneType)
            ->setPageLink($this->initPage())
        ;

        return $zone;

    }
    private function initElement(){
        $element = new Element();
        $this->init($element);
        return $element;
    }
    private function init(AbstractElem &$elem){
        $elem
            ->setName(self::title)
            ->setActive(true)
            ->setPrio(0)
            ->setRemove(false)
            ->addDetail($this->generateDetail(get_class($elem).'Detail'))
        ;

        if(! $elem instanceof AbstractFileLink){
            $this->createPhotos($elem);
            $this->createDocuments($elem);
            $this->createVideos($elem);
        }
    }

    private function generateDetail($detail){
        $detail = new $detail();

        return $detail->setName(self::title)
            ->setTitle(self::title)
            ->setLang(self::locale)
            ->setDescription(self::desc);

    }

    private function createPhotos(AbstractElem &$elem){
        for($i = 0;$i < 3;$i++){
            $photo = new Photo();
            $photo->simulateId();
            $this->init($photo);
            $file = new File();
            $file->setFile('demo.jpg')->setExt('jpeg');
            $photo->setFile($file);
            $elem->addPhoto($photo);
            $this->entityManager->persist($photo);
        }
    }
    private function createDocuments(AbstractElem &$elem){
        for($i = 0;$i < 3;$i++){
            $document = new Document();
            $this->init($document);
            $file = new File();
            $file->setFile('demo.pdf')->setExt('pdf');
            $document->setFile($file);
            $elem->addDocument($document);
        }

    }
    private function createVideos(AbstractElem &$elem){
        for($i = 0;$i < 3;$i++){
            $video = new Video();
            $this->init($video);
            $file = new File();
            $file->setFile('demo.mp4')->setExt('mp4');
            $video->setFile($file);
            $elem->addVideo($video);
        }
    }
}