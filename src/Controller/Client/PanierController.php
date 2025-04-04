<?php

namespace App\Controller\Client;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/client/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'client_panier_afficher')]
    public function afficher(EntityManagerInterface $em, Request $request): Response
    {
        $panier = $request->getSession()->get('panier', []);
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

        $panier = $request->getSession()->get('panier', []);
        unset($panier[$id]);
        $request->getSession()->set('panier', $panier);

        $this->addFlash('success', '❌ Produit retiré du panier.');
        return $this->redirectToRoute('client_panier_afficher');
    }

    #[Route('/commander', name: 'client_panier_commander', methods: ['POST'])]
    public function commander(Request $request, EntityManagerInterface $em): Response
    {
        $panier = $request->getSession()->get('panier', []);

        foreach ($panier as $produitId => $quantite) {
            $produit = $em->getRepository(Produit::class)->find($produitId);
            if ($produit && $produit->getStock() >= $quantite) {
                $produit->setStock($produit->getStock() - $quantite);
            }
        }

        $em->flush();
        $request->getSession()->remove('panier');

        $this->addFlash('success', '✅ Commande validée avec succès !');
        return $this->redirectToRoute('client_produits');
    }
}
