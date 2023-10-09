<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleFormationUtilisateurController extends AbstractController
{
    #[Route('/module/formation/utilisateur', name: 'app_module_formation_utilisateur')]
    public function index(): Response
    {
        return $this->render('module_formation_utilisateur/index.html.twig', [
            'controller_name' => 'ModuleFormationUtilisateurController',
        ]);
    }
}
