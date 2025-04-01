<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_users')]
    public function listUsers(EntityManagerInterface $em): Response
    {
        // Récupère tous les utilisateurs (pour simplifier)
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/produits', name: 'admin_produits')]
    public function addProduit(): Response
    {
        // Pour l'instant, on affiche simplement un message placeholder
        return $this->render('admin/produits.html.twig');
    }
}
