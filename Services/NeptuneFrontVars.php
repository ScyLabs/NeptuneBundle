<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19/11/2019
 * Time: 11:02
 */

namespace ScyLabs\NeptuneBundle\Services;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ScyLabs\NeptuneBundle\Services\ClassFounder;
use ScyLabs\NeptuneBundle\Model\NeptuneFrontVarsInterface;
use Symfony\Component\HttpFoundation\Request;

class NeptuneFrontVars implements NeptuneFrontVarsInterface
{
    private $entityManager;
    private $classFounder;
    public function __construct(EntityManagerInterface $entityManager,ClassFounder $classFounder) {
        $this->entityManager = $entityManager;
        $this->classFounder = $classFounder;
    }


    public function getVars(Request $request): array {

        $pages = $this->entityManager->getRepository($this->getClass('page'))->findBy(array(
            'parent' => null,
            'remove' => false,
            'active' => true
        ),
            ['prio'=>'ASC']
        );
        $page = $pages[0];

        if($page->getZones()->count() > 0){
            $page->getZones()[0]->setTypeHead(1);
        }

        $infos = $this->entityManager->getRepository($this->getClass('infos'))->findOneBy([],['id'=>'ASC']);
        $partners = $this->entityManager->getRepository($this->getClass('partner'))->findAll();
        $contactPages = new ArrayCollection();
        foreach ($pages as $thisPage){
            if($thisPage->getType()->getName() == 'contact'){
                $contactPages->add($thisPage);
                break;
            }
        }
        return [
            'pages'         =>  $pages,
            'page'          =>  $page,
            'infos'         =>  $infos,
            'partners'      =>  $partners,
            'locale'        =>  $request->getLocale(),
            'contactPages'  =>  $contactPages
        ];
    }

    private function getClass(string $key){
        return $this->classFounder->getClass($key);
    }
}