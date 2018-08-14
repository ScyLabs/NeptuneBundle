<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 11/06/2018
 * Time: 16:30
 */

namespace ScyLabs\NeptuneBundle\Controller;





use ScyLabs\NeptuneBundle\Entity\Document;
use ScyLabs\NeptuneBundle\Entity\Element;
use ScyLabs\NeptuneBundle\Entity\File;

use ScyLabs\NeptuneBundle\Entity\FileType;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\Partner;
use ScyLabs\NeptuneBundle\Entity\Photo;
use ScyLabs\NeptuneBundle\Entity\Video;
use ScyLabs\NeptuneBundle\Entity\Zone;
use ScyLabs\NeptuneBundle\Services\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class FileController extends BaseController
{
    /**
     * @Route("/admin/gallery",name="admin_file")
     */
    public function addAction(Request $request){
        $em = $this->getDoctrine();
        $repoFiles = $em->getRepository(File::class);

        $files = $repoFiles->findBy(array(),['id'=>'DESC']);

        $filesTypes = $em->getRepository(FileType::class)->findBy(array(
            'remove'=>false,
        ));
        $params = array(
            'title' =>  'Mediathèque',
            'files' => $files,
            'fileTypes'=>$filesTypes
        );
        $collection = $this->getAllEntities(File::class);
        if($collection !== null){
            $params['collection'] = $collection;
        }
        // Génération du fil d'ariane

        $ariane = array(
            [
                'link'=>$this->generateUrl('admin_home'),
                'name'=>'Accueil'
            ],
            [
                'link'=>'#',
                'name'=>'Médiathèque'
            ]
        );
        $params['ariane'] = $ariane;
        return $this->render('@ScyLabsNeptune/admin/file/listing.html.twig',$params);
    }

    /**
     * @Route("/admin/file/link" , name="admin_file_link")
     * @Method("POST")
     */
    public function linkAction(Request $request){

        $select = $request->request->get('selection');
        $id = $request->request->get('id');
        $typeElement = $request->request->get('type');
        if($select === null || $id === null ||$typeElement === ''){
            $this->get('session')->getFlashBag()->add('notice',"Une erreur est survenue lors de la liaison de vos fichiers");
            return $this->redirectToRoute('admin_file');
        }
        if(null ===  $files = json_decode($select)){
            $this->get('session')->getFlashBag()->add('notice',"Une erreur est survenue lors de la liaison de vos fichiers");
            return $this->redirectToRoute('admin_file');
        }

        $em = $this->getDoctrine()->getManager();
        if($typeElement == 'page'){
            $repo = $em->getRepository(Page::class);
            $obj = $repo->find($id);
        }
        elseif($typeElement == 'zone'){
            $repo = $em->getRepository(Zone::class);
            $obj = $repo->find($id);
        }
        else{
            $repo = $em->getRepository(Element::classs);
            $obj = $repo->find($id);
            $typeElement = 'element';
        }
        if($obj === null){

            $this->get('session')->getFlashBag()->add('notice',"Une erreur est survenue lors de la liaison de vos fichiers");
            return $this->redirectToRoute('admin_file');
        }

        $repoFiles = $em->getRepository(File::class);

        $files = $repoFiles->findBy(
            array(
                'id'=>$files,
            )
        );


        /* On supprime les valeurs déjà rentrées */
        /*Si l'utilisateur ne les a pas déselectionnés , ils se re-rentrerons quoi qu'il arrive */
        foreach ($obj->getPhotos() as $photo){
            $obj->removePhoto($photo);
            $em->remove($photo);
        }
        foreach ($obj->getDocuments() as $document){
            $obj->removeDocument($document);
            $em->remove($document);
        }
        foreach ($obj->getVideos() as $video){
            $obj->removeVideo($video);
            $em->remove($video);
        }

        // On parcours les fichiers et on affecte a l'élément selectionné .
        $prioPhoto = 0;
        $prioDocument = 0;
        $prioVideo = 0;
        foreach ($files as $file){
            $type = $file->getType()->getName();
            if($type == 'photo'){
                $photo = (new Photo())
                    ->setName($obj->getName())
                    ->setFile($file)
                    ->setPrio($prioPhoto++);
                ;

                $obj->addPhoto($photo);
            }
            elseif($type == 'video'){
                $video = (new Video())
                    ->setName($obj->getName())
                    ->setFile($file)
                    ->setPrio($prioDocument++);
                $obj->addVideo($video);
            }
            else{
                $document = (new Document())
                    ->setName($obj->getName())
                    ->setFile($file)
                    ->setPrio($prioVideo++)
                ;

                $obj->addDocument($document);
            }
        }
        $em->persist($obj);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice',"Vos fichiers ont bien été liés.");
        return $this->redirectToRoute('admin_file_gallery_prio',array('type'=>$typeElement,'id'=>$obj->getId()));

    }
    /**
     * @Route("/admin/file/upload",name="admin_file_upload")
     */
    public function uploadAction(Request $request,FileUploader $fileUploader){


        if(!$res = $request->files->get('file')){
            return new Response('Type de fichier non pris en compte',403);
        }

        $minesok = array(
            'ScyLabs\NeptuneBundlelication/pdf',
            'image/jpeg',
            'image/png',
            'image/svg+xml',
            'audio/*',
            'image/gif',
            'video/mp4',
            'ScyLabs\NeptuneBundlelication/zip',
            'ScyLabs\NeptuneBundlelication/x-7z-compressed',
            'ScyLabs\NeptuneBundlelication/x-rar-compressed'
        );

        $uploadFile = new SymfonyFile($res);
        if(!in_array($uploadFile->getMimeType(),$minesok))
            return new Response('Type de fichier non autorisé',403);

        $typeRepo = $this->getDoctrine()->getRepository(FileType::class);

        switch($uploadFile->getMimeType()){
            case 'ScyLabs\NeptuneBundlelication/pdf':
                $name = 'document';
                break;
            case 'image/jpeg':
                $name ='photo';
                break;
            case 'image/png':
                $name = 'photo';
                break;
            case 'image/svg+xml':
                $name = 'photo';
                break;
            case 'image/gif':
                $name ='photo';
                break;
            default:
                $name = 'no_classified';
                break;
        }
        $type = $typeRepo->findOneByName($name);

        $file = new File();


        $file->setFile($res)
            ->setExt($uploadFile->guessExtension())
            ->setType($type);

        $em = $this->getDoctrine()->getManager();
        $em->persist($file);
        $em->flush();

        $result = array(
            'file'=>$file->getFile(),
            'id'=>$file->getId(),
            'type'=>$file->getType()->getName(),
        );

        return $this->json($result);
    }

    /**
     * @Route("admin/{type}/{id}/files", name="admin_file_gallery_prio" , requirements={"id"="\d+","type"="(page|zone|element|partner)"})
     */
    public function galleryprioAction(Request $request,$id,$type){

        $em = $this->getDoctrine()->getManager();


        if($type == 'page'){
            $repo = $em->getRepository(Page::class);
            $object = $repo->find($id);
        }
        elseif($type == 'zone'){
            $repo = $em->getRepository(Zone::class);
            $object = $repo->find($id);
        }
        elseif($type == 'partner'){
            $repo = $em->getRepository(Partner::class);
            $object = $repo->find($id);
        }
        else{
            $repo = $em->getRepository(Element::class);
            $object = $repo->find($id);
        }

        $ariane = array(
            ['link'=>$this->generateUrl('admin_home'),'name'=>'Accueil'],
            [
                'link'=>$this->generateUrl('admin_entity',array('type'=>$type)),
                'name'=>'Pages'
            ],
            [
                'link'=> '#',
                'name' => 'Fichiers'
            ]
        );
        $repoFiles = $em->getRepository(File::class);

        $files = $repoFiles->findBy(array(),['id'=>'DESC']);
        $filesTypes = $em->getRepository(FileType::class)->findBy(array(
            'remove'=>false,
        ));

        dump($object);
        $params = array(
            'title'     => 'Gestion des fichiers de '.(($type == 'page' ||$type == 'zone') ? 'la ' : "l'").ucfirst($type).' : '.$object->getName(),
            'ariane'    => $ariane,
            'object'    => $object,
            'files'     => $files,
            'fileTypes' => $filesTypes,

        );

        return $this->render('@ScyLabsNeptune/admin/file/gallery_prio.html.twig',$params);
    }

    /**
     * @Route("admin/file/prio",name="admin_file_prio")
     * @Method("POST")
     */
    public function priosAction(Request $request){
        $ajax = $request->isXmlHttpRequest();
        $prios = json_decode($request->request->get('prio'),true);
        $type = $request->request->get('type');
        if($request->request->get('prio') === null || $prios === false || $type === null){
            if($ajax)
                return new Response('');
            else
                return $this->redirectToRoute('admin_page');
        }

        $em = $this->getDoctrine()->getManager();
        if($type == 'photo'){
            $files = $em->getRepository(Photo::class)->findBy(array(
                'id'=>$prios,
            ));
        }
        elseif($type == 'video'){
            $files = $em->getRepository(Video::class)->findBy(array(
                'id'=>$prios
            ));
        }
        else{
            $files = $em->getRepository(Document::class)->findBy(array(
                'id'=>$prios
            ));
        }
        $em = $this->getDoctrine()->getManager();
        $filesTab = array();
        foreach ($files as $file){
            $filesTab[$file->getId()] = $file;
        }

        $i = 0;
        foreach ($prios as $id){

            if(is_object($filesTab[$id])){
                $filesTab[$id]->setPrio($i);
                $em->persist($filesTab[$id]);
                $i++;
            }
        }

        $em->flush();
        if($ajax){
            return new Response('');
        }
        else{
            return $this->redirectToRoute('admin_file_gallery_prio');
        }
    }

    /**
     * @Route("admin/file/delete/{id}",name="admin_file_delete",requirements={"id" = "\d+"})
     */
    public function deleteAction(Request $request,$id){
        $repo = $this->getDoctrine()->getRepository(File::class);
        $file = $repo->find($id);
        if(null === $file){
            $this->redirectToRoute('admin_file');
        }

        $form = $this->createFormBuilder($file)->setMethod('PUT')
            ->setAction($this->generateUrl('admin_file_delete',array('id'=>$file->getId())))
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->remove($file);
            $em->flush();
            return $this->redirect($request->headers->get('referer'));
        }
        $params = array(
            'form'  =>  $form->createView(),
        );

        return $this->render('@ScyLabsNeptune/admin/delete.html.twig',$params);
    }


}