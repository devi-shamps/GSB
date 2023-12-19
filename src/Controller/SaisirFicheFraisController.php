<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\LigneFraisHorsForfait;
use App\Form\EtatType;
use App\Form\SaisirFicheFraisForfaitType;
use App\Form\SaisirFicheFraisHorsForfaitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFicheFraisController extends AbstractController
{
    #[Route('/saisirfichefrais', name: 'app_saisir_fiche_frais')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $formFicheFraisForfait = $this->createForm(SaisirFicheFraisForfaitType::class);
        $formFicheFraisForfait->handleRequest($request);

        if ($formFicheFraisForfait->isSubmitted() && $formFicheFraisForfait->isValid()) {
            $formFicheFraisForfait->getData();
            dd($formFicheFraisForfait);
        }

        $ligneFraisHorsForfait = new LigneFraisHorsForfait();
        $formFicheFraisHorsForfait = $this->createForm(SaisirFicheFraisHorsForfaitType::class, $ligneFraisHorsForfait);
        $formFicheFraisHorsForfait->handleRequest($request);

        if ($formFicheFraisHorsForfait->isSubmitted() && $formFicheFraisHorsForfait->isValid()) {
            $entityManager->persist($ligneFraisHorsForfait);
            $entityManager->flush();

            return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('saisir_fiche_frais/index.html.twig', [
            'controller_name' => 'SaisirFicheFraisController',
            'formFicheFraisForfait' => $formFicheFraisForfait,
            'formFicheFraisHorsForfait' => $formFicheFraisHorsForfait
        ]);
    }
}
