<?php

namespace App\Controller\Client;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/produits')]
class ProduitClientController extends AbstractController
{
    #[Route('/', name: 'client_produits')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $produits = $em->getRepository(Produit::class)->createQueryBuilder('p')
            ->where('p.stock > 0')
            ->getQuery()
            ->getResult();

        $panier = $request->getSession()->get('panier', []);

        return $this->render('client/liste_produits.html.twig', [
            'produits' => $produits,
            'panier' => $panier,
        ]);
    }

    #[Route('/ajouter-au-panier', name: 'client_panier_ajouter', methods: ['POST'])]
    public function ajouterAuPanier(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $id = $request->request->get('produit_id');
        $quantite = (int) $request->request->get('quantite');

        $produit = $em->getRepository(Produit::class)->find($id);
        if (!$produit) {
            throw $this->createNotFoundException("Produit introuvable.");
        }

        $panier = $session->get('panier', []);
        $panier[$id] = ($panier[$id] ?? 0) + $quantite;

        if ($panier[$id] <= 0) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);
        $this->addFlash('success', '✅ Panier mis à jour !');

        return $this->redirectToRoute('client_produits');
    }
}
