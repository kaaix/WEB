<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuperAdminController extends AbstractController
{
    #[Route('/superadmin/admins', name: 'superadmin_admins')]
    public function listAdmins(EntityManagerInterface $em): Response
    {
        // Récupère tous les utilisateurs dont le champ roles contient "ROLE_ADMIN"
        $admins = $em->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();

        return $this->render('superadmin/admin.html.twig', [
            'admins' => $admins,
        ]);
    }

    #[Route('/superadmin/admins/delete/{id}', name: 'superadmin_admins_delete')]
    public function deleteAdmin(User $user, EntityManagerInterface $em): Response
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            throw $this->createAccessDeniedException("Ce n'est pas un admin.");
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Administrateur supprimé.');

        return $this->redirectToRoute('superadmin_admins');
    }
}
