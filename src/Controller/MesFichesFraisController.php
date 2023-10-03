<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesFichesFraisController extends AbstractController
{
    #[Route('/mesfichesfrais', name: 'app_mes_fiches_frais')]
    public function index(): Response
    {
        return $this->render('mes_fiches_frais/index.html.twig', [
            'controller_name' => 'MesFichesFraisController',
        ]);
    }
}
