<?php
/**
 * Created by PhpStorm.
 * User: assis
 * Date: 21/03/2019
 * Time: 09:30
 */

namespace ScyLabs\NeptuneBundle\DataFixtures\ORM;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ScyLabs\NeptuneBundle\Entity\Page;
use ScyLabs\NeptuneBundle\Entity\PageDetail;
use ScyLabs\NeptuneBundle\Entity\PageType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPageData extends Fixture implements OrderedFixtureInterface,ContainerAwareInterface
{

    private $container;

    public function load(ObjectManager $manager){
        $page = new Page();
        $page->setName('Accueil');
        $type = $manager->getRepository(PageType::class)->findOneBy(array(
            'name'  => 'page'
        ));
        $page->setType($type);
        foreach ($this->container->getParameter('langs') as $lang){
            $detail = new PageDetail();
            $detail->setLang($lang);
            $detail->setName($page->getName());
            $page->addDetail($detail);
        }
        $manager->persist($page);
        $manager->flush();


    }
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getOrder(){
        return 6;
    }
}