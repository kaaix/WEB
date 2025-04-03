<?php

namespace App\Controller\Client;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'client_panier')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer le panier en session
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        // Récupérer les produits à partir des IDs du panier
        $ids = array_keys($panier);
        $produits = [];

        if (!empty($ids)) {
            $produits = $em->getRepository(Produit::class)->findBy(['id' => $ids]);
        }

        return $this->render('client/panier.html.twig', [
            'produits' => $produits,
            'panier' => $panier,
        ]);
    }
    #[Route('/vider', name: 'client_panier_vider', methods: ['POST'])]
public function vider(Request $request): Response
{
    $request->getSession()->remove('panier');
    $this->addFlash('success', '🧹 Panier vidé avec succès.');
    return $this->redirectToRoute('client_panier');
}

}
