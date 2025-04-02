<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminUserController extends AbstractController
{
    #[Route('/users', name: 'admin_users')]
    public function usersList(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

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

        // Ajoute ici la logique pour vider le panier si nécessaire
        // Exemple (adapte selon ta relation exacte) :
        // foreach ($user->getPanierItems() as $item) {
        //     $em->remove($item);
        // }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur supprimé.');
        return $this->redirectToRoute('admin_users');
    }
}
