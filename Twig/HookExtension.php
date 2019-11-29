<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 20/11/2019
 * Time: 16:56
 */

namespace ScyLabs\NeptuneBundle\Twig;


use ScyLabs\NeptuneBundle\DataCollector\HookCollector;
use ScyLabs\NeptuneBundle\Manager\HookManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;



class HookExtension extends AbstractExtension
{

    private $container;
    private $hookCollector;
    public function __construct(ContainerInterface $container,HookCollector $hookCollector) {
        $this->container = $container;
        $this->hookCollector = $hookCollector;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('ScyLabs_showHook',array($this,'showHook'))
        );
    }

    public function showHook(string $template,string $hookName){

        $session = $this->container->get('session');

        $manager = $this->container->get(HookManager::class);
        $hooksByPriority = $manager->getHooks($hookName);
        ksort($hooksByPriority);

        if($this->container->has('profiler')){
            $sessionHooks = $session->get('scy_labs_neptune_hooks');
            $hooksLinks = [];
            foreach ($hooksByPriority as $prio){
                foreach ($prio as $hook){
                    $hooksLinks[] = get_class($hook);
                }
            }
            if(null === $sessionHooks || !is_array($sessionHooks)){
                $session->set('scy_labs_neptune_hooks',[
                    $hookName    =>  [
                        "template"      =>  $template,
                        "links"         => $hooksLinks
                    ]
                ]);
            }else{

                $session->set('scy_labs_neptune_hooks',array_merge([$hookName    =>  $template],$sessionHooks));
            }
        }

        $render = [];




        foreach ($hooksByPriority as $hooks){
            foreach ($hooks as $hook){
                $render[] = $hook->showHook();
            }
        }
        return implode('',$render);
    }
}