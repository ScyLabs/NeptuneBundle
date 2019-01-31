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

        $rootNode
            ->children()
                ->arrayNode('classes')->addDefaultsIfNotSet()

                    // PAGE -----------
                    ->children()
                        ->arrayNode('page')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(Page::class)->end()
                                ->variableNode('original')->defaultValue(Page::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('pageForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PageForm::class)->end()
                                ->variableNode('original')->defaultValue(PageForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('pageUrl')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PageUrl::class)->end()
                                ->variableNode('original')->defaultValue(PageUrl::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('pageDetail')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PageDetail::class)->end()
                                ->variableNode('original')->defaultValue(PageDetail::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('pageDetailForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PageDetailForm::class)->end()
                                ->variableNode('original')->defaultValue(PageDetailForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('pageType')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PageType::class)->end()
                                ->variableNode('original')->defaultValue(PageType::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('pageTypeForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PageTypeForm::class)->end()
                                ->variableNode('original')->defaultValue(PageTypeForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    // END PAGE -----------
                    // ELEMENT
                    ->children()
                        ->arrayNode('element')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(Element::class)->end()
                                ->variableNode('original')->defaultValue(Element::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('elementUrl')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ElementUrl::class)->end()
                                ->variableNode('original')->defaultValue(ElementUrl::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('elementForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ElementForm::class)->end()
                                ->variableNode('original')->defaultValue(ElementForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('elementDetail')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ElementDetail::class)->end()
                                ->variableNode('original')->defaultValue(ElementDetail::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('elementDetailForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ElementDetailForm::class)->end()
                                ->variableNode('original')->defaultValue(ElementDetailForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('elementType')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ElementType::class)->end()
                                ->variableNode('original')->defaultValue(ElementType::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('elementTypeForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ElementTypeForm::class)->end()
                                ->variableNode('original')->defaultValue(ElementTypeForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    // END ELEMENT ----------------
                    // ZONE------------------------
                    ->children()
                        ->arrayNode('zone')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(Zone::class)->end()
                                ->variableNode('original')->defaultValue(Zone::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('zoneForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ZoneForm::class)->end()
                                ->variableNode('original')->defaultValue(ZoneForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('zoneDetail')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ZoneDetail::class)->end()
                                ->variableNode('original')->defaultValue(ZoneDetail::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('zoneDetailForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ZoneDetailForm::class)->end()
                                ->variableNode('original')->defaultValue(ZoneDetailForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('zoneType')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ZoneType::class)->end()
                                ->variableNode('original')->defaultValue(ZoneType::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('zoneTypeForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(ZoneTypeForm::class)->end()
                                ->variableNode('original')->defaultValue(ZoneTypeForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    // END ZONE
                    //  Partner
                    ->children()
                        ->arrayNode('partner')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(Partner::class)->end()
                                ->variableNode('original')->defaultValue(Partner::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('partnerForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PartnerForm::class)->end()
                                ->variableNode('original')->defaultValue(PartnerForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('partnerDetail')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PartnerDetail::class)->end()
                                ->variableNode('original')->defaultValue(PartnerDetail::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('partnerDetailForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PartnerDetailForm::class)->end()
                                ->variableNode('original')->defaultValue(PartnerDetailForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    // END PARTNER
                    // FILE
                    ->children()
                        ->arrayNode('file')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(File::class)->end()
                                ->variableNode('original')->defaultValue(File::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('fileForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(FileForm::class)->end()
                                ->variableNode('original')->defaultValue(FileForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('fileType')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(FileType::class)->end()
                                ->variableNode('original')->defaultValue(FileType::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('fileTypeForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(FileTypeForm::class)->end()
                                ->variableNode('original')->defaultValue(FileTypeForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    // END FILE
                    // PHOTO
                    ->children()
                        ->arrayNode('photo')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(Photo::class)->end()
                                ->variableNode('original')->defaultValue(Photo::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('photoDetail')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PhotoDetail::class)->end()
                                ->variableNode('original')->defaultValue(PhotoDetail::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('photoDetailForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(PhotoDetailForm::class)->end()
                                ->variableNode('original')->defaultValue(PhotoDetailForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    // END PHOTO
                    // DOCUMENT
                    ->children()
                        ->arrayNode('document')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(Document::class)->end()
                                ->variableNode('original')->defaultValue(Document::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('documentDetail')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(DocumentDetail::class)->end()
                                ->variableNode('original')->defaultValue(DocumentDetail::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('documentDetailForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(DocumentDetailForm::class)->end()
                                ->variableNode('original')->defaultValue(DocumentDetailForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    // END DOCUMENT
                    // VIDEO
                    ->children()
                        ->arrayNode('video')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(Video::class)->end()
                                ->variableNode('original')->defaultValue(Video::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('videoDetail')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(VideoDetail::class)->end()
                                ->variableNode('original')->defaultValue(VideoDetail::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    // FIN VIDEO
                    // INFOS
                    ->children()
                        ->arrayNode('infos')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(Infos::class)->end()
                                ->variableNode('original')->defaultValue(Infos::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('infosForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(InfosForm::class)->end()
                                ->variableNode('original')->defaultValue(InfosForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    // FIN INFOS
                    // USER
                    ->children()
                        ->arrayNode('user')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(User::class)->end()
                                ->variableNode('original')->defaultValue(User::class)->end()
                            ->end()
                        ->end()
                    ->end()
                    ->children()
                        ->arrayNode('userForm')->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('class')->defaultValue(UserForm::class)->end()
                                ->variableNode('original')->defaultValue(UserForm::class)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}