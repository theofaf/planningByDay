<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtablisseController extends AbstractController
{
    #[Route('/etablisse', name: 'app_etablisse')]
    public function index(): Response
    {
        return $this->render('etablisse/index.html.twig', [
            'controller_name' => 'EtablisseController',
        ]);
    }
}
