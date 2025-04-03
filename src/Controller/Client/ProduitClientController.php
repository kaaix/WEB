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
    // 🔸 Liste des produits visibles par le client
    #[Route('/', name: 'client_produits')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $produits = $em->getRepository(Produit::class)->findAll();
        $panier = $request->getSession()->get('panier', []);

        return $this->render('client/liste_produits.html.twig', [
            'produits' => $produits,
            'panier' => $panier,
        ]);
    }

    // 🔸 Traitement du formulaire (ajout/suppression produit dans panier)
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

        // Si quantité à 0 on retire le produit
        if ($panier[$id] === 0) {
            unset($panier[$id]);
        }

        // Mise à jour du stock (sécurité, si tu veux)
        // $produit->setStock($produit->getStock() - $quantite);
        // $em->flush();

        $session->set('panier', $panier);
        $this->addFlash('success', '✅ Panier mis à jour !');

        return $this->redirectToRoute('client_produits');
    }
}
