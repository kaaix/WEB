<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

class FixturesController extends AbstractController
{
    private $projectDir;

    public function __construct(ParameterBagInterface $params)
    {
        $this->projectDir = $params->get('kernel.project_dir');
    }

    #[Route('/admin/fixtures/load', name: 'admin_fixtures_load')]
    public function loadFixtures(Request $request): Response
    {
        // Exécute la commande doctrine:fixtures:load sans interaction.
        // ATTENTION : cela purgera la base de données et rechargera les fixtures !
        $process = new Process(
            ['php', 'bin/console', 'doctrine:fixtures:load', '--no-interaction'],
            $this->projectDir
        );
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->addFlash('success', 'Fixtures rechargées avec succès ! Veuillez vous reconnecter.');

        // Invalide la session pour déconnecter l'utilisateur.
        $request->getSession()->invalidate();

        // Redirige l'utilisateur vers la page de connexion.
        return new RedirectResponse($this->generateUrl('app_login'));
    }
}
