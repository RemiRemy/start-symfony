<?php

namespace App\DataFixtures;


use App\Entity\Produit;
use App\Entity\Slide;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();

        $tableauImage = ["cafe1.jpg", "cafe2.jpg", "cafe3.jpg", "cafe4.jpg", "cafe5.jpg", "cafe6.jpg", "cafe7.jpg", "cafe8.jpg", "cafe9.jpg", "cafe10.jpg"];

        for ($i = 0; $i < 10; $i++) {

            $produit = new Produit();
            $produit->setDesignation('Café "' . $faker->sentence(3) . '"')
                ->setDescription($faker->text(100))
                ->setPrix($faker->randomFloat(2, 5, 10))
                ->setNomImage($faker->randomElement($tableauImage));
            //->setNomImage($tableauImage[$i]);

            $manager->persist($produit);
        }
        $admin = new User();
        $admin->setPrenom("Rémi");
        $admin->setNom("Remy");
        $admin->setEmail("rr@g.com");
        $admin->setPassword($this->hasher->hashPassword($admin, "azerty"));
        $admin->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);


        $utilisateur = new User();
        $utilisateur->setPrenom("John");
        $utilisateur->setNom("DOE");
        $utilisateur->setEmail("jd@g.com");
        $utilisateur->setPassword($this->hasher->hashPassword($utilisateur, "azerty"));

        $manager->persist($utilisateur); // prépare la sauvegarde jusqu'au flush


        // Ajouter slides

        for ($i = 1; $i < 4; $i++) {


            $slide = new Slide();

            $slide->setNomImage("header" . $i . ".png")
                ->setTitre($faker->text(10))
                ->setTexte($faker->text(40));
            $manager->persist($slide);
        }

        $manager->flush(); // enregistre tout dans la bdd 
    }
}
