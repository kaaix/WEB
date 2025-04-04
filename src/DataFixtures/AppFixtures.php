<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Pays;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // --- Ajouter des pays ---
        $fr = new Pays();
        $fr->setNom('France')->setCode('FR');
        $be = new Pays();
        $be->setNom('Belgique')->setCode('BE');
        $ca = new Pays();
        $ca->setNom('Canada')->setCode('CA');
        $manager->persist($fr);
        $manager->persist($be);
        $manager->persist($ca);

        // --- Utilisateur : Super Admin ---
        $sadmin = new User();
        $sadmin->setLogin('sadmin')
            ->setNom('Root')
            ->setPrenom('Super')
            ->setDateNaissance(new \DateTime('1970-01-01'))
            ->setPays($fr)
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setActif(true);
        $sadmin->setPassword($this->hasher->hashPassword($sadmin, 'superpass'));
        $manager->persist($sadmin);

        // --- Utilisateur : Admin ---
        $admin = new User();
        $admin->setLogin('admin')
            ->setNom('Admin')
            ->setPrenom('Gilles')
            ->setDateNaissance(new \DateTime('1980-02-02'))
            ->setPays($be)
            ->setRoles(['ROLE_ADMIN'])
            ->setActif(true);
        $admin->setPassword($this->hasher->hashPassword($admin, 'adminpass'));
        $manager->persist($admin);

        // --- Utilisateur : Client 1 ---
        $client1 = new User();
        $client1->setLogin('rita')
            ->setNom('Rita')
            ->setPrenom('Smith')
            ->setDateNaissance(new \DateTime('1995-05-05'))
            ->setPays($ca)
            ->setRoles(['ROLE_CLIENT'])
            ->setActif(true);
        $client1->setPassword($this->hasher->hashPassword($client1, 'rita123'));
        $manager->persist($client1);

        // --- Utilisateur : Client 2 ---
        $client2 = new User();
        $client2->setLogin('boumediene')
            ->setNom('Boumediene')
            ->setPrenom('Ali')
            ->setDateNaissance(new \DateTime('1998-08-08'))
            ->setPays($fr)
            ->setRoles(['ROLE_CLIENT'])
            ->setActif(true);
        $client2->setPassword($this->hasher->hashPassword($client2, 'boubou'));
        $manager->persist($client2);

        // --- Ajouter des produits ---
        $products = [
            ['nom' => 'Zelda', 'description' => 'Jeu d’aventure épique', 'prix' => 59.99, 'stock' => 100, 'image' => 'zelda.jpg'],
            ['nom' => 'Mario Kart', 'description' => 'Course de kart multijoueur', 'prix' => 49.99, 'stock' => 80, 'image' => 'mario.jpg'],
            ['nom' => 'Minecraft', 'description' => 'Jeu de construction en blocs', 'prix' => 19.99, 'stock' => 200, 'image' => 'minecraft.jpg'],
            ['nom' => 'FIFA 24', 'description' => 'Football nouvelle génération', 'prix' => 69.99, 'stock' => 150, 'image' => 'fifa.jpg'],
            ['nom' => 'Call of Duty', 'description' => 'FPS de guerre réaliste', 'prix' => 59.99, 'stock' => 60, 'image' => 'cod.jpg'],
        ];

        foreach ($products as $data) {
            $produit = new Produit();
            $produit->setNom($data['nom']);
            $produit->setDescription($data['description']);
            $produit->setPrix($data['prix']);
            $produit->setStock($data['stock']);
            $produit->setImage($data['image']);
            $manager->persist($produit);
        }

        // ✅ Sauvegarde finale
        $manager->flush();
    }
}
