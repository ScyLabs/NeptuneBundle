<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01/08/2018
 * Time: 12:16
 */

namespace ScyLabs\NeptuneBundle\DependencyInjection;


use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use ScyLabs\GiftCodeBundle\Entity\GiftCode;
use ScyLabs\NeptuneBundle\Annotation\ScyLabsNeptune\Override;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Model\OverrideAnnotationFounderInterface;
use ScyLabs\NeptuneBundle\ScyLabsNeptuneBundle;
use ScyLabs\NeptuneBundle\Services\OverrideAnnotationFounder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader as TranslationLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\Mapping as ORM;


class ScyLabsNeptuneExtension extends Extension
{

    private $annotationFounder;
    public function __construct() {
        $this->annotationFounder = new OverrideAnnotationFounder();
    }

    public function load(array $configs,ContainerBuilder $container){

        $configuration = new Configuration();

        $projectDir = $container->getParameter('kernel.project_dir');
        $config = $this->processConfiguration($configuration,$configs);

        $bundles = require $projectDir.'/config/bundles.php';

        foreach ($bundles as $bundle => $env){

            if($bundle === ScyLabsNeptuneBundle::class)
                continue;
            if(method_exists(new $bundle,'getParent') && (new $bundle)->getParent() === ScyLabsNeptuneBundle::class){

                $reflector = new \ReflectionClass($bundle);
                $childBundleRoot = dirname($reflector->getFileName());

                if(file_exists($childBundleRoot.'/Resources/config/scylabs_neptune_config.yaml')){
                    $scyLabsConfig = Yaml::parseFile($childBundleRoot.'/Resources/config/scylabs_neptune_config.yaml');

                    if(is_array($scyLabsConfig) && array_key_exists('scy_labs_neptune',$scyLabsConfig) && array_key_exists('override',$scyLabsConfig['scy_labs_neptune'])){
                        foreach ($scyLabsConfig['scy_labs_neptune']['override'] as $key => $class){
                            if(!array_key_exists($key,$config['override']) || (array_key_exists($key,$config['override']) && !class_exists($config['override'][$key])))
                                $config['override'][$key] = $class;
                        }
                    }
                }


                $overrideAnnotations = $this->annotationFounder->getAnnotations($reflector->getNamespaceName(),$childBundleRoot);
                foreach ($overrideAnnotations as $overrideAnnotation){
                    if(!array_key_exists($overrideAnnotation->key,$config['override']) || (array_key_exists($overrideAnnotation->key,$config['override']) && !class_exists($config['override'][$overrideAnnotation->key])))
                        $config['override'][$overrideAnnotation->key] = $overrideAnnotation->class;
                }

            }
        }
        $originalClasses = Yaml::parseFile(dirname(__DIR__).'/Resources/config/original_classes.yaml');

        if(file_exists($projectDir.'/composer.json')){
            if (null !== $composerJson = json_decode(file_get_contents($projectDir.'/composer.json'))){

                $projectNameSpaceConfig = ((array)$composerJson->autoload)['psr-4'];
                $projectNameSpace = null;
                $sourcesDir = null;
                foreach ($projectNameSpaceConfig as $nameSpace => $directory){
                    $projectNameSpace = trim($nameSpace,'\\');
                    $sourcesDir = trim($directory,'\\');
                }
                $overrideAnnotations = $this->annotationFounder->getAnnotations($projectNameSpace,$projectDir.'/'.$sourcesDir);
                foreach ($overrideAnnotations as $overrideAnnotation){
                    $config['override'][$overrideAnnotation->key] = $overrideAnnotation->class;
                }

            }

        }

        foreach ($originalClasses as $key => $class) {
            if (!array_key_exists($key, $config['override'])) {
                $config['override'][$key] = $class;
            }
        }
        $container->setParameter($this->getAlias().'.override',$config['override']);
        $container->setParameter($this->getAlias().'.compress',$config['compress']);
        $container->setParameter($this->getAlias().'.sitemap',$config['sitemap']);
        $container->setParameter($this->getAlias().'.icons',$config['icons']);



        if(!array_key_exists('codex',$config)){
            $config['codex'] = [
                'url'       =>  null,
                'cdn'       =>  null,
                'publicKey' =>  $projectDir.'/public.pem'
            ];
        }elseif(array_key_exists('codex',$config) && null === $config['codex']['publicKey']){
            $config['codex']['publicKey'] = $projectDir.'/public.pem';
        }

        $container->setParameter($this->getAlias().'.codex',$config['codex']);
        $container->setParameter($this->getAlias().'.codex.url',$config['codex']['url']);
        $container->setParameter($this->getAlias().'.codex.cdn',$config['codex']['cdn']);
        $container->setParameter($this->getAlias().'.codex.publicKey',$config['codex']['publicKey']);


        $bundleRoot = new FileLocator(dirname(__DIR__));

        $loader = new YamlFileLoader($container,$bundleRoot);


        $loader->load('Resources/config/services.yaml');

    }
}