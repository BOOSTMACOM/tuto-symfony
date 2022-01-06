<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\PasswordHasherEncoder;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasherInterface;

    public function __construct(UserPasswordHasherInterface $passwordHasherInterface)
    {
        $this->passwordHasherInterface = $passwordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {

        // Création des articles
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

        // Création d'un user de test
        $user = (new User())
        ->setEmail('john@doe.fr')
        ->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasherInterface->hashPassword($user, 'azerty'));
        $manager->persist($user);

        // Création de catégories
        $categories = ['DIY','Sport','Food'];
        foreach($categories as $categorie)
        {
            $new = (new Category())
            ->setTitle($categorie);

            $manager->persist($new);
        }

        $manager->flush();
    }
}
