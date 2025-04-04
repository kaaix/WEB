<?php

namespace App\Controller\Client;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/client/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'client_panier_afficher')]
    public function afficher(EntityManagerInterface $em, Request $request): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier', []);
        $produits = [];

        if (!empty($panier)) {
            $produits = $em->getRepository(Produit::class)->findBy([
                'id' => array_keys($panier),
            ]);
        }

        return $this->render('client/panier.html.twig', [
            'produits' => $produits,
            'panier' => $panier,
        ]);
    }

    #[Route('/ajouter', name: 'client_panier_ajouter', methods: ['POST'])]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $id = $request->request->get('produit_id');
        $quantite = (int) $request->request->get('quantite', 1);
        $produit = $em->getRepository(Produit::class)->find($id);

        if (!$produit || $quantite < 1 || $quantite > $produit->getStock()) {
            $this->addFlash('error', 'Produit invalide ou quantité incorrecte.');
            return $this->redirectToRoute('client_produits');
        }

        $session = $request->getSession();
        $panier = $session->get('panier', []);
        $panier[$id] = ($panier[$id] ?? 0) + $quantite;
        $session->set('panier', $panier);

        $this->addFlash('success', '🛒 Produit ajouté au panier.');
        return $this->redirectToRoute('client_produits');
    }

    #[Route('/vider', name: 'client_panier_vider', methods: ['POST'])]
    public function vider(Request $request): Response
    {
        $request->getSession()->remove('panier');
        $this->addFlash('success', '🧹 Panier vidé.');
        return $this->redirectToRoute('client_panier_afficher');
    }

    #[Route('/supprimer/{id}', name: 'client_panier_supprimer_produit', methods: ['POST'])]
    public function supprimerProduit(int $id, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('supprimer_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('client_panier_afficher');
        }

        $session = $request->getSession();
        $panier = $session->get('panier', []);
        unset($panier[$id]);
        $session->set('panier', $panier);

        $this->addFlash('success', '❌ Produit retiré du panier.');
        return $this->redirectToRoute('client_panier_afficher');
    }

    #[Route('/commander', name: 'client_panier_commander', methods: ['POST'])]
    public function commander(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        foreach ($panier as $produitId => $quantite) {
            $produit = $em->getRepository(Produit::class)->find($produitId);
            if ($produit && $produit->getStock() >= $quantite) {
                $produit->setStock($produit->getStock() - $quantite);

                // Retirer le produit si son stock est épuisé
                if ($produit->getStock() === 0) {
                    $em->remove($produit);
                }
            }
        }

        $em->flush();
        $session->remove('panier');

        $this->addFlash('success', '✅ Commande validée avec succès !');
        return $this->redirectToRoute('client_produits');
    }
}
