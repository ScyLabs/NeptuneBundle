<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 13/06/2018
 * Time: 12:19
 */

namespace ScyLabs\NeptuneBundle\DataFixtures\ORM;


use ScyLabs\NeptuneBundle\Entity\ElementType;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Persistence\ObjectManager;


class LoadElementTypeData extends Fixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager){

        $types = array();
        $types[] = new ElementType();
        $types[0]
            ->setName('produit')
            ->setTitle('Produit')
            ->setRemovable(false);
        $manager->persist($types[0]);

        $manager->flush();


    }
    public function getOrder(){
        return 1;
    }
}