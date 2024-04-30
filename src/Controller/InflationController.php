<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InflationController extends AbstractController
{
    #[Route('/inflation', name: 'app_inflation')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $fichesFrais2023 = $doctrine->getRepository(FicheFrais::class)->findByYear(2023);

        $montantTotal = 0.0;
        foreach ($fichesFrais2023 as $ficheFrais) {
            $montantTotal += $ficheFrais->getMontant();
        }

        $primeInflation = $montantTotal * 9.5 / 100;
        $users = $doctrine->getRepository(User::class)->findAll();
        $nbUsers = count($users);
        $montantParVisiteur = $primeInflation / $nbUsers;

        // Afficher la vue avec les résultats
        return $this->render('inflation/index.html.twig', [
            'montantTotal' => $montantTotal,
            'primeInflation' => $primeInflation,
            'montantParVisiteur' => $montantParVisiteur,
        ]);
    }
}
