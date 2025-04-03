<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/superadmin')]
class SuperAdminController extends AbstractController
{

     #[Route('/dashboard', name: 'superadmin_dashboard')]
public function redirectToAdminList(): Response
{
    $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
    return $this->redirectToRoute('superadmin_admins');
}

    #[Route('/admins', name: 'superadmin_admins')]
    public function listAdmins(UserRepository $userRepository): Response
    {
        $admins = $userRepository->findByRole('ROLE_ADMIN');

        return $this->render('superadmin/admins_list.html.twig', [
            'admins' => $admins,
        ]);
    }

    #[Route('/admin/create', name: 'superadmin_admin_create')]
    public function createAdmin(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']); // on force le rôle admin

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($hasher->hashPassword($user, $plainPassword));
            $user->setActif(true);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Administrateur créé avec succès.');
            return $this->redirectToRoute('superadmin_admins');
        }

        return $this->render('superadmin/create_admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/delete/{id}', name: 'superadmin_admin_delete')]
    public function deleteAdmin(User $user, EntityManagerInterface $em): Response
    {
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer un super-admin.');
        } else {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Administrateur supprimé.');
        }

        return $this->redirectToRoute('superadmin_admins');
    }
}
