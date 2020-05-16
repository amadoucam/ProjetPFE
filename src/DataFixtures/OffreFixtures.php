<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Offre;
use Doctrine\ORM\EntityManagerInterface;

class OffreFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        //3 catégories fakées
        for ($i = 1; $i <= 3; $i++) {
            $category = new Categorie();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category);

            //créer entre 4 et 6 offres(mt_rand 4 et 6 aleatoire)
            for ($j = 1; $j <= mt_rand(4, 6); $j++) {
                $offre = new Offre();

                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                $offre->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategorie($category);

                $manager->persist($offre);
        } 

        /* for ($i = 1; $i <= 10; $i++) {
            $offre = new Offre();
            $offre->setTitle("Titre de l'article n°$i")
                  ->setContent("<p>Contenu de l'article n°$i</p>")
                  ->setImage("http://via.placeholder.com/100x50")
                  ->setCreatedAt(new \DateTime());

            $manager->persist($offre);  */
        }
        $manager->flush();

    }
}
