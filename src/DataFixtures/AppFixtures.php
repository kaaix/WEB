<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Pays;
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

        // Sauvegarde en base
        $manager->flush();
    }
}
