<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\DatePickerFormType;



class MesFichesFraisController extends AbstractController
{
    #[Route('/mesfichesfrais', name: 'app_mes_fiches_frais')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $selectedFiche = null;

        $fichesFraisUser = $doctrine->getRepository(FicheFrais::class)->findBy(['user' => $user]);
        $form = $this->createForm(DatePickerFormType::class, $fichesFraisUser);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FicheFrais $selectedFiche */
            $selectedFiche = $form->get('mois')->getData();
        }

        return $this->render('mes_fiches_frais/index.html.twig', [
            'controller_name' => 'MesFichesFraisController',
            'form' => $form->createView(),
            'selectedFiche' => $selectedFiche,
            'fichesFraisUser' => $fichesFraisUser,
        ]);
    }
}
