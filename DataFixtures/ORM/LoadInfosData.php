<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 13/06/2018
 * Time: 12:19
 */

namespace ScyLabs\NeptuneBundle\DataFixtures\ORM;

use ScyLabs\NeptuneBundle\Entity\FileType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ScyLabs\NeptuneBundle\Entity\Infos;


class LoadInfosData extends Fixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager){


        $object = new Infos();

        $manager->persist($object);

        $manager->flush();


    }
    public function getOrder(){
        return 1;
    }
}