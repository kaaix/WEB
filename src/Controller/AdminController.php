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
public function addProduit(EntityManagerInterface $em): Response
{
    // On récupère les produits depuis la BDD
    $produits = $em->getRepository(\App\Entity\Produit::class)->findAll();

    // On passe la variable à la vue
    return $this->render('admin/admin_produits.html.twig', [
        'produits' => $produits
    ]);
}

}
