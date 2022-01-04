<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        $slugger = new AsciiSlugger();

        for($i = 0;$i < 10;$i++)
        {
            $title = $faker->sentence(5);
            $article = (new Article())
                ->setTitle($title)
                ->setContent($faker->text(10000))
                ->setPublishedAt($faker->dateTime())
                ->setSlug(strtolower($slugger->slug($title)));

            $manager->persist($article);
        }

        $manager->flush();
    }
}
