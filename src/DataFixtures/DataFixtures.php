<?php

namespace App\DataFixtures;

use DateTime;

use App\Entity\Line;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $linea1 = new Line();
        $linea1->setDescription("Aula");
        $linea1->setPhoneNumber("91 679 01 80");

        $linea2 = new Line();
        $linea2->setDescription("Despacho");
        $linea2->setPhoneNumber("91 679 01 80");

        foreach([$linea1, $linea2] as $l) {
            $l->setStatus("idle");
            $l->setLastOpen(new DateTime());
            $l->setLastClose(new DateTime());
            
            $manager->persist($l);
        }

        $manager->flush();
    }
}
