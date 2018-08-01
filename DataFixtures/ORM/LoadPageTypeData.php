<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 13/06/2018
 * Time: 12:19
 */

namespace Scylabs\NeptuneBundle\DataFixtures\ORM;


use Scylabs\NeptuneBundle\Entity\PageType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\Persistence\ObjectManager;


class LoadPageTypeData extends Fixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager){

        $types = array();
        $types[] = new PageType();
        $types[0]
            ->setName('page')
            ->setTitle('Page')
            ->setRemovable(false);
        $manager->persist($types[0]);

        $types[] = new PageType();
        $types[1]->setName('contact')
            ->setTitle('Contact')
            ->setRemovable(false);
        $manager->persist($types[1]);

        $manager->flush();


    }
    public function getOrder(){
        return 1;
    }
}