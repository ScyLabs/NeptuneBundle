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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\Loader\JsonFileLoader;

use Symfony\Component\Routing\Annotation\Route;
class CodexController extends BaseController
{



    private $codexUrl;
    private $kernel;
    public function __construct(KernelInterface $kernel) {
        $this->kernel = $kernel;
    }

    /**
     * @Route("/codex/zones",name="codex_zones")
     */
    public function getZones(){

        if(null === $this->codexUrl = $this->getCodexUrl()){
            return $this->redirectToRoute('neptune_home');
        }
        if(($token = $this->getApiToken()) instanceof Response)
            return $token;


        $result = $this->curl([
            'url'   => $this->codexUrl.'/zone',
            'token' => $token
        ],$code);

        $categories = json_decode($this->curl([
            'url'   =>  $this->codexUrl.'/category',
            'token' =>  $token
        ]));
        if(null === $categories){
            $categories = [];
        }

        $zones = json_decode($result,true);

        if($code !== Response::HTTP_OK){
            $zones = [];
        }

        return $this->render('@ScyLabsNeptune/admin/codex/list.html.twig',[
            'zones'         =>  $zones,
            'categories'    =>  $categories,
            'codexUrl'      =>  $this->getParameter('scy_labs_neptune.codex.url')
        ]);
    }

    /**
     * @Route("/codex/export/{id}",name="codex_export")
     */
    public function exportZone(Request $request,CodexExporterInterface $codexExporter,ZoneType $zoneType){
        if(($token = $this->getApiToken()) instanceof Response)
            return $token;
        $url = $this->codexUrl.'/zone';
        $curlOpts = [
            'type'  =>  'POST',
            'token' =>  $token
        ];


        $categories = $this->curl([
            'url'   =>  $this->codexUrl.'/category',
            'token' =>  $token
        ],$code);
        $codexZone = null;
        if(null !== $zoneType->getCodexId()){
            $url .= '/'.$zoneType->getCodexId();
            $curlOpts['type']  = 'PUT';
            $zoneOpts = [
                'id'    =>  $zoneType->getCodexId(),
            ];
            $codexZone = json_decode($this->curl([
                'url'       =>  $this->codexUrl.'/zone',
                'token'     =>  $token,
                'content'   =>  json_encode($zoneOpts)
            ],$code));


            if($code !== 200){
                return $this->json([
                    'success'   =>  false,
                    'message'   =>  'An error has occured'
                ],Response::HTTP_BAD_REQUEST);
            }
            if(null !== $codexZone){

                if(sizeof($codexZone ) === 0){
                    $codexZone = null;
                    $curlOpts['type'] = 'POST';
                    $zoneType->setCodexHash(null)->setCodexId(null);
                    $this->getDoctrine()->getManager()->flush();
                }else{
                    $codexZone =  $codexZone[0];

                    if($codexZone->version > $zoneType->getVersion()){

                        return $this->json([
                            'success'   =>  false,
                            'message'   =>  'La zone à une version plus récente que celle du site  sur le codex(mettre à jour avant de renvoyer)'
                        ],Response::HTTP_BAD_REQUEST);
                    }

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
        $zoneType->setCodexId($result->object->id)->setCodexHash($result->object->hash)->setVersion($zoneType->getVersion() + 1);

        $em->flush();

        $message = "Ta zone à bien été ";
        $message .= ($code == 200) ? 'mise à jour sur le codex' : 'ajoutée sur le codex';
        return $this->json([
            'success'   =>  true,
            'message'   =>  $message
        ]);

    }

    /**
     * @Route("/show/{id}",name="codex_zone_show_template",requirements={"id"="\d+"},methods={"GET"})
     */
    public function showTemplate(Request $request,NeptuneVarsCreatorInterface $neptuneVariablesCreator,$id){

        $repo = $this->getDoctrine()->getRepository(ZoneType::class);
        if(null === $zoneType = $repo->find($id)){
            throw new NotFoundHttpException();
        }
        $params = array_merge(
            ['cdn'   => $this->getParameter('scy_labs_neptune.codex.cdn')],
            $neptuneVariablesCreator->initVars($zoneType)
        );


        return $this->render('@ScyLabsNeptune/admin/codex/show.html.twig',$params);
    }


    /**
     * @Route("/photo-show/{id}/{width}/{height}/{multiplicator}/{truncate}{monochrome}/{name}",name="codex_photo",defaults={"height"=0,"multiplicator"=100,"truncate"=0,"monochrome"="","name"=""},requirements={
     * "id"= "[0-9]+",
     * "width"= "[0-9]{1,4}",
     * "height"= "[0-9]{0,4}",
     * "truncate"= "[01]",
     * "monochrome"= "(/[a-zA-Z0-9]{6}-[a-fA-F0-9]{6})?",
     * "multiplicator"= "[0-9]{2,3}"
     * })
     */
    public function generatePhoto(Request $request,$id,$width,$height,$multiplicator,$truncate,$monochrome,$name){
        $response = new File($this->getParameter('kernel.project_dir').'/public/bundles/scylabsneptune/admin/img/demo.jpg');
        return $this->file($response,'',ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @Route("/codex/maj/{id}",name="codex_maj")
     */
    public function importZone(Request $request,CodexImporterInterface $codexImporter,$id){
        if(null === $this->codexUrl = $this->getCodexUrl()){
            return $this->redirectToRoute('neptune_home');
        }

        if(($token = $this->getApiToken()) instanceof Response)
            return $token;

        $url = $this->codexUrl.'/zone/'.$id;
        $result = $this->curl([
            'url'   =>  $url,
            'token' =>  $token
        ],$code);

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
        if($code !== Response::HTTP_OK){
            return $this->json([
                'success'    =>  false,
                'message'   =>  ($code === Response::HTTP_NOT_FOUND) ? 'Zone not found' : 'An error has occured'
            ]);
        }
        $dataZone = $data['zone'];
        $dataFile = $data['file'];


        if($request->attributes->get('_route') === 'codex_maj'){
            if(null === $zone = $this->getDoctrine()->getRepository(ZoneType::class)->findOneByCodexId($dataZone['id'])){
                return $this->json([
                    'success'   => false,
                    'message'   =>  'Pas de zone'
                ],Response::HTTP_BAD_REQUEST);
            }
            if($zone->getVersion() >= $dataZone['version']){
                return $this->json([
                    'success'   =>  false,
                    'message'   =>  'Ta zone est déjà à jour'
                ],Response::HTTP_BAD_REQUEST);
            }

        }

        $bundleRoot = dirname(__DIR__);


        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl($request->attributes->get('_route'),['id'=>$id]));
        if($request->attributes->get('_route') !== 'codex_maj'){

            $form->add('importType',ChoiceType::class,[
                'multiple'  =>  false,
                'expanded'  =>  true,
                'required'  =>  true,
                'label'     =>  "Quel type d'import voulez vous effectuer ?",
                'choices'   =>  [
                    'Utiliser la zone telle quelle'         =>  0,
                    'Créer une zone à partir de celle-ci'   =>  1,

                ]
            ]);

        }
        $css = $this->curl([
            'url'   => $this->codexUrl.'/css/zone/'.$dataZone['slug'].'.less'
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
                'iframe'        =>  $this->codexUrl.'/show/'.$id.'?token='.$token,
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



        $zone =  $repo->findOneByCodexId($dataZone['id']);
        if($request->attributes->get('_route') !== 'codex_maj' && ($form->has('importType') && $form->get('importType')->getData() === 0) && null !== $zone){
            return $this->json([
                'success'   =>  false,
                'message'   =>  'This zone early installed'
            ],Response::HTTP_BAD_REQUEST);
        }


        $slug = $dataZone['slug'];

        if(null === $zone){
            $i = 0;
            while (null !== $tmpZone = $repo->findOneByName($dataZone['slug'])){
                $i++;
                $dataZone['slug'] = $slug.'-'.$i;
            }
        }


        if(null === $zone){
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
        }


        $zone->setVersion($dataZone['version']);


        $em = $this->getDoctrine()->getManager();

        $em->persist($zone);
        $codexImporter->import($dataFile,$zone,$colors);
        $em->flush();
        $message = ($request->attributes->get('_route') === 'codex_maj') ? 'mise à jour' : 'importée';
        return $this->json([
            'success'   =>  true,
            'message'   =>  'La zone à bien été '.$message
        ]);
    }
    /**
     * @Route("/codex/delete/{id}",name="codex_delete")
     */
    public function deleteZone(Request $request,$id){
        if(null === $this->codexUrl = $this->getCodexUrl()){
            return $this->redirectToRoute('neptune_home');
        }
        if(($token = $this->getApiToken()) instanceof Response)
            return $token;
        $curlOpts = [
            'url'   =>  $this->codexUrl.'/zone/'.$id,
            'type'  =>  'DELETE',
            'token' =>  $token
        ];
        $result = $this->curl($curlOpts);

        return $this->json([
            'success'   =>  true,
            'message'   => 'La zone à bien été supprimée'
        ]);
    }

    /**
     * @Route("/zonecategory/add",name="codex_category_add")
     */
    public function addCategory(Request $request){
        if(null === $this->codexUrl = $this->getCodexUrl()){
            return $this->redirectToRoute('neptune_home');
        }
        if(($token = $this->getApiToken()) instanceof Response)
            return $token;

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
            'url'       =>  $this->codexUrl.'/category',
            'type'      =>  'POST',
            'content'   =>  json_encode($data),
            'token'     =>  $token
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
    /**
     * @Route("/zonecategory/{id}",name="codex_category_edit",requirements={"id"="\d+"})
     */
    public function editCategory(Request $request,$id){
        if(null === $this->codexUrl = $this->getCodexUrl()){
            return $this->redirectToRoute('neptune_home');
        }
        if(($token = $this->getApiToken()) instanceof Response)
            return $token;
        $curlOpts = [
            'url'   =>  $this->codexUrl.'/category/'.$id,
            'token' =>  $token
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
    /**
     * @Route("/zonecategory/remove/{id}",name="codex_category_delete",requirements={"id"="\d+"})
     */
    public function deleteCategory(Request $request,$id){
        if(null === $this->codexUrl = $this->getCodexUrl()){
            return $this->redirectToRoute('neptune_home');
        }
        if(($token = $this->getApiToken()) instanceof Response)
            return $token;
        $curlOpts = [
            'url'   =>  $this->codexUrl.'/category/'.$id,
            'type'  =>  'DELETE',
            'token' =>  $token
        ];
        $result = $this->curl($curlOpts);
        return $this->json([
            'success'   =>  true,
            'message'   => 'La catégorie à bien été supprimée'
        ]);
    }

    /**
     * @Route("/zonecategory",name="codex_category")
     */
    public function categories(Request $request){
        if(null === $this->codexUrl = $this->getCodexUrl()){
            return $this->redirectToRoute('neptune_home');
        }
        if(($token = $this->getApiToken()) instanceof Response)
            return $token;

        $curlOpts = [
            'url'   =>  $this->codexUrl.'/category',
            'token' =>  $token
        ];
        $result = $this->curl($curlOpts,$code);

        if((null === $categories = json_decode($result)) || $code !== Response::HTTP_OK){
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

    private function getApiToken(){
        if(null === $this->codexUrl = $this->getCodexUrl()){
            return $this->redirectToRoute('neptune_home');
        }
        $env = $this->kernel->getEnvironment();
        $user = $this->getUser();

        if(null === $user)
            return $this->redirectToRoute('neptune_home');
        if(null !== $user->getApiToken())
            return $user->getApiToken();



        $publicKey = $this->getParameter('scy_labs_neptune.codex.publicKey');
        if(!file_exists($publicKey)){
            if($env === 'dev')
                throw new AccessDeniedHttpException('Public key not found');
            return $this->redirectToRoute('neptune_home');
        }

        $tokenRequestContent = [
            'username'  =>  $user->getUserName(),
            'site'      =>  $_SERVER['HTTP_HOST'],
            'name'      =>  $user->getName(),
            'firstname' =>  $user->getFirstName(),
        ];


        $result = $this->curl([
            'url'       =>  $this->codexUrl.'/create-apikey',
            'content'   =>  json_encode($tokenRequestContent),
            'type'      =>  'POST',
            'token'     =>  base64_encode(file_get_contents($publicKey))
        ],$code);

        if(null === $result = json_decode($result)){
            if($env === 'dev')
                throw new BadRequestHttpException('An error has occured');
            return $this->redirectToRoute('neptune_home');
        }
        if($code !== Response::HTTP_CREATED){
            if($this->kernel->getEnvironment() === 'dev')
                throw new BadRequestHttpException('Codex error : '.$result->message);
            return $this->redirectToRoute('neptune_home');
        }

        $token = $result->token;
        $user->setApiToken($token);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $token;
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
        if(array_key_exists('token',$opts)){

            $headers[] = "X-API-KEY: ".$opts['token'];

            unset($opts['token']);
        }

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

    private function getCodexUrl(){
        return $this->getParameter('scy_labs_neptune.codex.url');
    }
}