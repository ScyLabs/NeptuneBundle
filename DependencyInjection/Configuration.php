<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/01/2019
 * Time: 09:18
 */

namespace ScyLabs\NeptuneBundle\DependencyInjection;


use ScyLabs\NeptuneBundle\Entity\Document;
use ScyLabs\NeptuneBundle\Entity\DocumentDetail;
use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\ElementDetail;
use ScyLabs\NeptuneBundle\Entity\ElementType;
use ScyLabs\NeptuneBundle\Entity\ElementUrl;
use ScyLabs\NeptuneBundle\Entity\File;
use ScyLabs\NeptuneBundle\Entity\FileType;
use ScyLabs\NeptuneBundle\Entity\Infos;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageDetail;
use ScyLabs\NeptuneBundle\Entity\PageType;
use ScyLabs\NeptuneBundle\Entity\PageUrl;
use ScyLabs\NeptuneBundle\Entity\Partner;
use ScyLabs\NeptuneBundle\Entity\PartnerDetail;
use ScyLabs\NeptuneBundle\Entity\Photo;
use ScyLabs\NeptuneBundle\Entity\PhotoDetail;
use ScyLabs\NeptuneBundle\Entity\User;
use ScyLabs\NeptuneBundle\Entity\Video;
use ScyLabs\NeptuneBundle\Entity\VideoDetail;
use ScyLabs\NeptuneBundle\Entity\Zone;
use ScyLabs\NeptuneBundle\Entity\ZoneDetail;
use ScyLabs\NeptuneBundle\Entity\ZoneType;
use ScyLabs\NeptuneBundle\Form\DocumentDetailForm;
use ScyLabs\NeptuneBundle\Form\ElementDetailForm;
use ScyLabs\NeptuneBundle\Form\ElementForm;
use ScyLabs\NeptuneBundle\Form\ElementTypeForm;
use ScyLabs\NeptuneBundle\Form\FileForm;
use ScyLabs\NeptuneBundle\Form\FileTypeForm;
use ScyLabs\NeptuneBundle\Form\InfosForm;
use ScyLabs\NeptuneBundle\Form\PageDetailForm;
use ScyLabs\NeptuneBundle\Form\PageForm;
use ScyLabs\NeptuneBundle\Form\PageTypeForm;
use ScyLabs\NeptuneBundle\Form\PartnerDetailForm;
use ScyLabs\NeptuneBundle\Form\PartnerForm;
use ScyLabs\NeptuneBundle\Form\PhotoDetailForm;
use ScyLabs\NeptuneBundle\Form\UserForm;
use ScyLabs\NeptuneBundle\Form\VideoDetailForm;
use ScyLabs\NeptuneBundle\Form\ZoneDetailForm;
use ScyLabs\NeptuneBundle\Form\ZoneForm;
use ScyLabs\NeptuneBundle\Form\ZoneTypeForm;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(){
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('neptune');


        $this->addClassesNode('override',$rootNode);
    ;


            //$rootNode->end();
        ;
        return $treeBuilder;
    }

    private function addClassesNode($node,&$rootNode){
        // PAGE -----------
        $rootNode
            ->children()
                ->arrayNode('override')->addDefaultsIfNotSet()
                    ->children()
                        ->variableNode('page')->defaultValue(Page::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('pageForm')->defaultValue(PageForm::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('pageUrl')->defaultValue(PageUrl::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('pageDetail')->defaultValue(PageDetail::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('pageDetailForm')->defaultValue(PageDetailForm::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('pageType')->defaultValue(PageType::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('pageTypeForm')->defaultValue(PageTypeForm::class)->end()
                    ->end()
                    // END PAGE -----------
                    // ELEMENT
                    ->children()
                        ->variableNode('element')->defaultValue(Element::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('elementUrl')->defaultValue(ElementUrl::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('elementForm')->defaultValue(ElementForm::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('elementDetail')->defaultValue(ElementDetail::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('elementDetailForm')->defaultValue(ElementDetailForm::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('elementType')->defaultValue(ElementType::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('elementTypeForm')->defaultValue(ElementTypeForm::class)->end()
                    ->end()
                    // END ELEMENT ----------------
                    // ZONE------------------------
                    ->children()
                        ->variableNode('zone')->defaultValue(Zone::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('zoneForm')->defaultValue(ZoneForm::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('zoneDetail')->defaultValue(ZoneDetail::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('zoneDetailForm')->defaultValue(ZoneDetailForm::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('zoneType')->defaultValue(ZoneType::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('zoneTypeForm')->defaultValue(ZoneTypeForm::class)->end()
                    ->end()
                    // END ZONE
                    //  Partner
                    ->children()
                        ->variableNode('partner')->defaultValue(Partner::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('partnerForm')->defaultValue(PartnerForm::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('partnerDetail')->defaultValue(PartnerDetail::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('partnerDetailForm')->defaultValue(PartnerDetailForm::class)->end()
                    ->end()
                    // END PARTNER
                    // FILE
                    ->children()
                        ->variableNode('file')->defaultValue(File::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('fileForm')->defaultValue(FileForm::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('fileType')->defaultValue(FileType::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('fileTypeForm')->defaultValue(FileTypeForm::class)->end()
                    ->end()
                    // END FILE
                    // PHOTO
                    ->children()
                        ->variableNode('photo')->defaultValue(Photo::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('photoDetail')->defaultValue(PhotoDetail::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('photoDetailForm')->defaultValue(PhotoDetailForm::class)->end()
                    ->end()
                    // END PHOTO
                    // DOCUMENT
                    ->children()
                        ->variableNode('document')->defaultValue(Document::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('documentDetail')->defaultValue(DocumentDetail::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('documentDetailForm')->defaultValue(DocumentDetailForm::class)->end()
                    ->end()
                    // END DOCUMENT
                    // VIDEO
                    ->children()
                        ->variableNode('video')->defaultValue(Video::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('videoDetail')->defaultValue(VideoDetail::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('videoDetailForm')->defaultValue(VideoDetailForm::class)->end()
                    ->end()
                    // FIN VIDEO
                    // INFOS
                    ->children()
                        ->variableNode('infos')->defaultValue(Infos::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('infosForm')->defaultValue(InfosForm::class)->end()
                    ->end()
                    // FIN INFOS
                    // USER
                    ->children()
                        ->variableNode('user')->defaultValue(User::class)->end()
                    ->end()
                    ->children()
                        ->variableNode('userForm')->defaultValue(UserForm::class)->end()
                    ->end()
                ->end()
            ->end();
    }
}