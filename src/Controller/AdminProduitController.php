<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/produit')]
class AdminProduitController extends AbstractController
{
    #[Route('/', name: 'admin_produit_lister')]
    public function lister(EntityManagerInterface $em): Response
    {
        $produits = $em->getRepository(Produit::class)->findAll();

        return $this->render('admin/admin_produits.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/ajouter', name: 'admin_produit_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitFormType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère le fichier envoyé depuis le champ 'image'
            $file = $form->get('image')->getData();

            // Si un fichier a été uploadé
            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                try {
                    // On essaie de déplacer le fichier vers le répertoire configuré
                    $file->move(
                        $this->getParameter('produit_images_dir'),
                        $filename
                    );
                    // On stocke le nom du fichier dans l'entité
                    $produit->setImage($filename);
                } catch (FileException $e) {
                    // Gestion propre de l’erreur
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image : ' . $e->getMessage());
                }
            }

            // Enregistrement en base
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('admin_produit_lister');
        }

        return $this->render('admin/ajouter_produit.html.twig', [
            'form' => $form->createView(),
        ]);
    }



  #[Route('/admin/produit/supprimer/{id}', name: 'admin_produit_supprimer', methods: ['POST'])]
   public function supprimer(Request $request, Produit $produit, EntityManagerInterface $em): Response{
    if ($this->isCsrfTokenValid('delete_produit_' . $produit->getId(), $request->request->get('_token'))) {
        $em->remove($produit);
        $em->flush();
        $this->addFlash('success', 'Produit supprimé avec succès.');
    }

    return $this->redirectToRoute('admin_produit_lister');
  }

}
