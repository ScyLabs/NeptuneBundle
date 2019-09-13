<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/08/2019
 * Time: 11:21
 */

namespace ScyLabs\NeptuneBundle\Controller;


use Gedmo\Sluggable\Sluggable;
use ScyLabs\NeptuneBundle\Entity\ZoneType;
use ScyLabs\NeptuneBundle\Model\CodexExporterInterface;
use ScyLabs\NeptuneBundle\Model\CodexImporterInterface;
use ScyLabs\NeptuneBundle\Model\NeptuneVarsCreatorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Loader\JsonFileLoader;

use Symfony\Component\Routing\Annotation\Route;
class CodexController extends BaseController
{
    const codexUrl = "http://codex.developpement";

    public function getZones(){
        $url = self::codexUrl;
        $data = array(

        );
        $params = "?";
        foreach ($data as  $key => $val){
            $params .= $key.'='.$val.'&';
        }
        $params = trim($params,'&');

        $curl = curl_init();

        $opts = array(
            CURLOPT_RETURNTRANSFER 	=>	true,
            CURLOPT_HEADER			=> false,
            CURLOPT_URL             => $url
        );
        curl_setopt_array($curl,$opts);
        $result = curl_exec($curl);
        curl_close($curl);

        $zones = json_decode($result,true);


        return $this->render('@ScyLabsNeptune/admin/codex/list.html.twig',[
            'zones' => $zones
        ]);
    }

    public function exportZone(Request $request,CodexExporterInterface $codexExporter,ZoneType $zoneType){
        $url = self::codexUrl.'/';
        $curlOpts = [
            'type'  => 'POST'
        ];


        $categories = $this->curl([
            'url'   => self::codexUrl.'/category'
        ],$code);
        $codexZone = null;
        if(null !== $zoneType->getCodexId()){
            $url .= $zoneType->getCodexId();
            $curlOpts['type']  = 'PUT';
            $zoneOpts = [
              'id'  =>  $zoneType->getCodexId()
            ];
            $codexZone = json_decode($this->curl([
                'url'       =>  self::codexUrl,
                'content'   =>  json_encode($zoneOpts)
            ]));

            if(null !== $codexZone ){
                if(sizeof($codexZone ) === 0){
                    $codexZone = null;
                    $curlOpts['type'] = 'POST';
                    $zoneType->setCodexHash(null)->setCodexId(null);
                    $this->getDoctrine()->getManager()->flush();
                }else{
                    $codexZone =  $codexZone[0];
                }
            }
        }

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('codex_export',['id'=>$zoneType->getId()]));
        $form->add('title',TextType::class,[
            'label' =>  'Choisissez un titre décrivant votre zone (côté client)',
            'data'  => (null !== $codexZone) ? $codexZone->title : null,
        ]);

        if($code === 200 && null !== $categories = json_decode($categories)){

            $zoneCategories = [];
            if(null !== $codexZone){
                $tmpCategories = $codexZone->categories;
                foreach ($tmpCategories as $tmpCategory){
                    $zoneCategories[]  = $tmpCategory->id;
                }

            }

            $choices = [];
            foreach ($categories as $category){
                $choices[$category->name] = $category->id;
            }
            $form->add('categories',ChoiceType::class,[
                'label'     =>  'Dans quelles catégories ranger la zone',
                'choices'   =>  $choices,
                'multiple'  =>  true,
                'data'      =>  $zoneCategories,
                'attr'      =>  [
                    'class' =>  'select2'
                ]
            ]);
        }


        $form->add('Verif',CheckboxType::class,[
            'label' =>  "J'ai vérifié la zone et elle fonctionne correctement"
        ]);

        $form = $form->getForm();

        $form->handleRequest($request);


        $questiontitle = ((null === $zoneType->getCodexId()) ? 'Exporter' : 'Mettre à jour').' la zone : "'.$zoneType->getTitle().'" ('.$zoneType->getName().')';
        if(!$form->isSubmitted() || $form->isSubmitted() && !$form->isValid()){
            return $this->render('@ScyLabsNeptune/admin/codex/question.html.twig',[
                'form'          =>  $form->createView(),
                'questionTitle' =>  $questiontitle,
                'iframe'        =>  $this->generateUrl('codex_zone_show_template',['id'    =>  $zoneType->getId()])
            ]);
        }

        $data = $form->getData();

        $zoneContent = [
            'name'          =>  $zoneType->getName(),
            'title'         =>  $data['title'],
            'categories'    =>  $data['categories'],
            'file'          =>  $codexExporter->getBase64ZipZoneFile($zoneType)
        ];

        $curlOpts['url'] = $url;
        $curlOpts['content'] = json_encode($zoneContent);

        $result = $this->curl($curlOpts,$code);
        if(!in_array($code,[201,200])){
            return $this->json($result,Response::HTTP_BAD_REQUEST);
        }

        $result = json_decode($result);
        if(true !== $result->success){
            return $this->json($result,Response::HTTP_BAD_REQUEST);
        }
        $em = $this->getDoctrine()->getManager();
        $zoneType->setCodexId($result->object->id)->setCodexHash($result->object->hash);
        $em->flush();

        $message = "Ta zone à bien été ";
        $message .= ($code == 200) ? 'mise à jour sur le codex' : 'ajoutée sur le codex';
        return $this->json([
            'success'   =>  true,
            'message'   =>  $message
        ]);

    }

    public function showTemplate(Request $request,NeptuneVarsCreatorInterface $neptuneVariablesCreator,$id){
        $repo = $this->getDoctrine()->getRepository(ZoneType::class);
        if(null === $zoneType = $repo->find($id)){
            throw new NotFoundHttpException();
        }
        $params = array_merge(
            ['cdn'   => $this->getParameter('scy_labs_neptune.cdn')],
            $neptuneVariablesCreator->initVars($zoneType)
        );


        return $this->render('@ScyLabsNeptune/admin/codex/show.html.twig',$params);
    }


    public function generateAction(Request $request,$id,$width,$height,$multiplicator,$truncate,$monochrome,$name){
        return $this->file($this->getParameter('kernel.project_dir').'/public/img/demo.jpg','',ResponseHeaderBag::DISPOSITION_INLINE);
    }

    public function importZone(Request $request,CodexImporterInterface $codexImporter,$id){
        $url = self::codexUrl.'/'.$id;
        $result = $this->curl([
            'url'   => $url
        ]);
        if(false === $result){
            return $this->json([
                'success'   =>  false,
                'message'  =>  'Error in codex-request'
            ],Response::HTTP_BAD_REQUEST);

        }
        if(null === $data = json_decode($result,true)){
            return $this->json([
                'success'   =>  false,
                'message'   =>  'Error in codex-response decode'
            ],Response::HTTP_BAD_REQUEST);
        }
        $dataZone = $data['zone'];
        $dataFile = $data['file'];

        $bundleRoot = dirname(__DIR__);

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('codex_import',['id'=>$id]))
            ->add('importType',ChoiceType::class,[
                'multiple'  =>  false,
                'expanded'  =>  true,
                'required'  =>  true,
                'label'     =>  "Quel type d'import voulez vous effectuer ?",
                'choices'   =>  [
                    'Utiliser la zone telle quelle'         =>  0,
                    'Créer une zone à partir de celle-ci'   =>  1,

                ]
            ]);

        $css = $this->curl([
            'url'   => self::codexUrl.'/css/zone/'.$dataZone['slug'].'.less'
        ],$code);
        if($code !== 404){
            preg_match_all('/((#[a-fA-F0-9]{3,6})|rgba?\(.*\))([ ]*!important)?;/Ui',$css,$matches);
            $colors = [];
            foreach ($matches[1] as $match){
                $match = strtolower($match);
                $colors[$match] = $match;
            }

            $colorNames = json_decode(file_get_contents($bundleRoot.'/Resources/config/colorNames.json'),true);
            foreach ($colorNames as $colorName => $colorCode){
                $colorCode = strtolower($colorCode);
                $colorName = strtolower($colorName);
                if(preg_match('/'.$colorName.'/Ui',$css,$matches)){
                    $colors[strtolower($matches[0])] = $colorCode;
                }

            }
        }
        $i = 0;
        foreach ($colors as $key => $color) {
            $i++;

            $inputType = (preg_match('/rgb/',$color)) ? TextType::class : ColorType::class;
            $form
                ->add('color-'.$i,$inputType,[
                    'label' =>  false,
                    'empty_data'   => $key,
                    'attr'  => [
                        'data-color'    =>  $color,
                        'data-key'      => $key
                    ]
                ]);
        }
        $form = $form->getForm();
        $form->handleRequest($request);
        if(!$form->isSubmitted() || ($form->isSubmitted() && !$form->isValid())){
            return $this->render('@ScyLabsNeptune/admin/codex/question.html.twig',[
                'form'          =>  $form->createView(),
                'questionTitle' =>  'Importer la zone : "'.$dataZone['title'].'" ('.$dataZone['slug'].')"',
            ]);
        }


        $data = $form->getData();

        foreach ($data as $key => $val){
            if($key === 'importType')
                continue;
            $colors[$form->get($key)->getConfig()->getEmptyData()] = $val;
        };

        $repo = $this->getDoctrine()->getRepository(ZoneType::class);

        if(null !== $zone =  $repo->findOneByCodexId($dataZone['id'])){
            /*return $this->json([
                'success'   =>  false,
                'message'   =>  'This zone early installed'
            ],Response::HTTP_NOT_ACCEPTABLE);*/
        }
        $slug = $dataZone['slug'];
        $i = 0;
        while (null !== $zone = $repo->findOneByName($dataZone['slug'])){
            $i++;
            $dataZone['slug'] = $slug.'-'.$i;
        }

        $zone = new ZoneType();
        $zone
            ->setName($dataZone['slug'])
            ->setTitle($dataZone['title'])
            ->setRemovable(true)
            ->setRemove(false);
        if($data['importType'] === 0){
            $zone
                ->setCodexId($dataZone['id'])
                ->setCodexHash($dataZone['hash']);
        }
        $em = $this->getDoctrine()->getManager();

        $em->persist($zone);
        $codexImporter->import($dataFile,$zone,$colors);
        $em->flush();
        return $this->json([
            'success'   =>  true,
            'message'   =>  'La zone à bien été importée'
        ]);
    }

    public function addCategory(Request $request){
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('codex_category_add'))
            ->add('name',TextType::class)->getForm();

        $form->handleRequest($request);
        if(!$form->isSubmitted() || ($form->isSubmitted() && !$form->isValid())){
            return $this->render('@ScyLabsNeptune/admin/entity/add.html.twig',[
                'form'          =>  $form->createView(),
                "title"         =>  "Créer une Catégorie de zone"
            ]);
        }
        $data = $form->getData();
        $curlOpts = [
            'url'       =>  self::codexUrl.'/category',
            'type'      =>  'POST',
            'content'   =>  json_encode($data)
        ];
        $result = $this->curl($curlOpts,$code);
        if($code !== 201){
            return $this->json([
                'success'   =>  false,
                'message'   =>  "Quelque chose s'est mal passé lors de l'ajout"
            ]);
        }
        return $this->json([
            'success'   =>  true,
            'message'   =>  "La catégorie à bien été ajoutée"
        ]);

    }
    public function editCategory(Request $request,$id){
        $curlOpts = [
            'url'   =>  self::codexUrl.'/category/'.$id
        ];
        $result = $this->curl($curlOpts);
        if(null === $category = json_decode($result)){
            return $this->json([
                'success'   =>  false,
                'message'   =>  'Problème lors de la récupération de la catégorie'
            ]);
        }
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('codex_category_edit',['id'=>$id]))
            ->add('name',TextType::class,[
                'data'  =>   $category->name
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if(!$form->isSubmitted() || ($form->isSubmitted() && !$form->isValid())){
            return $this->render('@ScyLabsNeptune/admin/entity/add.html.twig',[
                'form'          =>  $form->createView(),
                'title'         =>  'Modification de la Catégorie : '.$category->name
            ]);
        }

        $curlOpts['type'] = 'PUT';
        $curlOpts['content'] = json_encode($form->getData());
        $this->curl($curlOpts);
        return $this->json([
            'success'   =>  true,
            'message'   =>  'La catégorie à bien été modifiée'
        ]);

    }
    public function deleteCategory(Request $request,$id){
        $curlOpts = [
            'url'   =>  self::codexUrl.'/category/'.$id,
            'type'  =>  'DELETE'
        ];
        $result = $this->curl($curlOpts);
        return $this->json([
            'success'   =>  true,
            'message'   => 'La catégorie à bien été supprimée'
        ]);
    }

    public function categories(Request $request){

        $curlOpts = [
            'url' => self::codexUrl.'/category',
        ];
        $result = $this->curl($curlOpts);

        if(null === $categories = json_decode($result)){
            $categories = [];
        }
        $request->attributes->add([
           'type'   =>  'zonecategory'
        ]);
        $params = array(
            'title'         =>  'Catégories du codex',
            'objects'       =>  $categories,
            'elemLisiting'  => false
        );
        $params['ariane'] = array(
            [
                'link'  => $this->generateUrl('neptune_home'),
                'name'  => 'Accueil'
            ],
            [
                'link'  =>  '#',
                'name'  =>  'Catégories du codex'
            ]
        );

        return $this->render('@ScyLabsNeptune/admin/entity/listing.html.twig',$params);
    }
    private function curl(array $opts,&$code = null){
        if(!array_key_exists('type',$opts)){
            $opts['type'] = 'GET';
        }
        if(!array_key_exists('content',$opts)){
            $opts['content'] = '{}';
        }
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        $curlOpts = [
            CURLOPT_URL             =>  $opts['url'],
            CURLOPT_RETURNTRANSFER  =>  true,
            CURLOPT_HEADER          =>  false,
            CURLOPT_CUSTOMREQUEST   =>  $opts['type'],
            CURLOPT_POSTFIELDS      =>  $opts['content'],
            CURLOPT_HTTPHEADER      =>  $headers,
        ];
        $curl = curl_init();
        curl_setopt_array($curl,$curlOpts);
        $result = curl_exec($curl);
        $code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $result;
    }
}