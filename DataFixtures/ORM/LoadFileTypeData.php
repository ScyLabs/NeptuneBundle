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
use Doctrine\Persistence\ObjectManager;


class LoadFileTypeData extends Fixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager){

        $types = array();
        $types[] = (new FileType())
            ->setName('no_classified')
            ->setTitle('Non Classé')
            ->setRemovable(false)
        ;
        $types[] = (new FileType())
            ->setName('photo')
           ->setTitle('Photo')
           ->setRemovable(false)
           ;
        $types[] = (new FileType())
            ->setName('document')
            ->setTitle('Document')
            ->setRemovable(false);

        $types[] = (new FileType())
            ->setName('video')
            ->setTitle('Video')
            ->setRemovable(false);
        $types[] = (new FileType())
            ->setName('music')
            ->setTitle('Musique')
            ->setRemovable(false);

        foreach ($types as $type){
            $manager->persist($type);
        }

        $manager->flush();


    }
    public function getOrder(){
        return 1;
    }
}