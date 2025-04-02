<?php

namespace App\Controller;

use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function edit(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', '✅ Profil mis à jour avec succès.');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
