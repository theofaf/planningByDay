<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleFormationController extends AbstractController
{
    #[Route('/module/formation', name: 'app_module_formation')]
    public function index(): Response
    {
        return $this->render('module_formation/index.html.twig', [
            'controller_name' => 'ModuleFormationController',
        ]);
    }
}
