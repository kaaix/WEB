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

#[Route('/admin')]
class AdminUserController extends AbstractController
{    


    #[Route('', name: 'admin_dashboard')]
    public function dashboardRedirect(): Response
    {
    return $this->redirectToRoute('admin_users');
    }

    #[Route('/users', name: 'admin_users')]
    public function usersList(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAllVisibleForAdmin($this->getUser());

        return $this->render('admin/users_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/delete/{id}', name: 'admin_user_delete')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        if ($user === $this->getUser() || in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $this->addFlash('danger', 'Action interdite.');
            return $this->redirectToRoute('admin_users');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé.');
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/user/create', name: 'admin_user_create')]
public function createUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
{
    $user = new User();

    // Par défaut : rôle forcé selon le rôle du créateur
    $currentRoles = $this->getUser()->getRoles();
    if (in_array('ROLE_ADMIN', $currentRoles)) {
        $user->setRoles(['ROLE_CLIENT']);
    } elseif (in_array('ROLE_SUPER_ADMIN', $currentRoles)) {
        $user->setRoles(['ROLE_ADMIN']);
    }

    $form = $this->createForm(UserFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $plainPassword = $form->get('plainPassword')->getData();
        $user->setPassword($hasher->hashPassword($user, $plainPassword));
        $user->setActif(true);

        // 🔒 SÉCURITÉ : on écrase le rôle avec le seul autorisé (empêche de tricher avec DevTools)
        if (in_array('ROLE_ADMIN', $currentRoles)) {
            $user->setRoles(['ROLE_CLIENT']);
        } elseif (in_array('ROLE_SUPER_ADMIN', $currentRoles)) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur créé avec succès.');

        // ✅ Redirection propre
        return $this->redirectToRoute('admin_users');
    }

    return $this->render('admin/create_user.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
