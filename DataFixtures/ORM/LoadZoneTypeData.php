<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 13/06/2018
 * Time: 12:19
 */

namespace ScyLabs\NeptuneBundle\DataFixtures\ORM;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Persistence\ObjectManager;
use ScyLabs\NeptuneBundle\Entity\ZoneType;


class LoadZoneTypeData extends Fixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager){

        $types = array();
        $types[] = new ZoneType();
        $types[0]
            ->setName('texte')
            ->setTitle('Texte')
            ->setRemovable(false);
        $manager->persist($types[0]);

        $manager->flush();


    }
    public function getOrder(){
        return 1;
    }
}