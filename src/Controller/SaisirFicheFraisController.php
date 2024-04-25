<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Form\EtatType;
use App\Form\SaisirFicheFraisForfaitType;
use App\Form\SaisirFicheFraisHorsForfaitType;
use App\Repository\EtatRepository;
use App\Repository\FicheFraisRepository;
use App\Repository\FraisForfaitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFicheFraisController extends AbstractController
{
    #[Route('/saisirfichefrais', name: 'app_saisir_fiche_frais')]
    public function index(Request $request, EntityManagerInterface $entityManager,
                          FicheFraisRepository $ficheFraisRepository, FraisForfaitRepository $forfaitRepository,
                          EtatRepository $etatRepository): Response
    {
        $dateActuel = new \DateTime('now');
        $moisEnCours = $dateActuel->format('Ym');
        $ficheMoisUser = $ficheFraisRepository->findOneBy(['mois' => $moisEnCours, 'user' => $this->getUser()]);

        $userData = $this->getUser();

        //Verification fiche du mois existante ou non
        if ($ficheMoisUser === null) {
            // Creation des objets
            $ficheMoisUser = new FicheFrais();
            $ligneFraisForfaitKilometre = new LigneFraisForfait();
            $ligneFraisForfaitEtape = new LigneFraisForfait();
            $ligneFraisForfaitNuitee = new LigneFraisForfait();
            $ligneFraisForfaitRepas = new LigneFraisForfait();

            // Initialisation des objets
            $ligneFraisForfaitKilometre->setFicheFrais($ficheMoisUser);
            $ligneFraisForfaitEtape->setFicheFrais($ficheMoisUser);
            $ligneFraisForfaitNuitee->setFicheFrais($ficheMoisUser);
            $ligneFraisForfaitRepas->setFicheFrais($ficheMoisUser);
            $ligneFraisForfaitKilometre->setQuantite(0);
            $ligneFraisForfaitEtape->setQuantite(0);
            $ligneFraisForfaitNuitee->setQuantite(0);
            $ligneFraisForfaitRepas->setQuantite(0);
            $ligneFraisForfaitKilometre->setFraisForfait($forfaitRepository->find(2));
            $ligneFraisForfaitEtape->setFraisForfait($forfaitRepository->find(1));
            $ligneFraisForfaitNuitee->setFraisForfait($forfaitRepository->find(3));
            $ligneFraisForfaitRepas->setFraisForfait($forfaitRepository->find(4));

            // Creation est iniatlisation de la fiche de frais
            $ficheMoisUser->setMois($moisEnCours);
            $ficheMoisUser->setEtat($entityManager->getReference(Etat::class, 2));
            $ficheMoisUser->setUser($this->getUser());
            $ficheMoisUser->setNbJustificatifs(0);
            $ficheMoisUser->setMontant(0);
            $ficheMoisUser->setDateModif($dateActuel);

            // Persiste des objets
            $entityManager->persist($ficheMoisUser);
            $entityManager->persist($ligneFraisForfaitKilometre);
            $entityManager->persist($ligneFraisForfaitEtape);
            $entityManager->persist($ligneFraisForfaitNuitee);
            $entityManager->persist($ligneFraisForfaitRepas);

            $entityManager->flush();
        }

        //Verification fiche du mois existante ou non
        $formFraisF = $this->createForm(SaisirFicheFraisForfaitType::class);
        $formFraisF->handleRequest($request);

        if ($formFraisF->isSubmitted() && $formFraisF->isValid()) {
            $toutesLesLignes = $ficheMoisUser->getLigneFraisForfaits();
            foreach ($toutesLesLignes as $lignes){
                if ($lignes->getFraisForfait()->getId() == 1){
                    $lignes->setQuantite($formFraisF->get('forfaitEtape')->getData());
                }
                elseif ($lignes->getFraisForfait()->getId() == 2){
                    $lignes->setQuantite($formFraisF->get('fraisKilometrique')->getData());
                }
                elseif ($lignes->getFraisForfait()->getId() == 3){
                    $lignes->setQuantite($formFraisF->get('nuiteeHotel')->getData());
                }
                else {
                    $lignes->setQuantite($formFraisF->get('repasRestaurant')->getData());
                }
            }

            $entityManager->persist($ficheMoisUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
        }

        $ficheMoisUser = $ficheFraisRepository->findOneBy(['mois' => $moisEnCours, 'user' => $this->getUser()]);
        if ($ficheMoisUser !== null) {
            foreach ($ficheMoisUser->getLigneFraisForfaits() as $ligneFraisForfait) {
                if ($ligneFraisForfait->getFraisForfait()->getId() == 1) {
                    $formFraisF->get('forfaitEtape')->setData($ligneFraisForfait->getQuantite());
                } elseif ($ligneFraisForfait->getFraisForfait()->getId() == 2) {
                    $formFraisF->get('fraisKilometrique')->setData($ligneFraisForfait->getQuantite());
                } elseif ($ligneFraisForfait->getFraisForfait()->getId() == 3) {
                    $formFraisF->get('nuiteeHotel')->setData($ligneFraisForfait->getQuantite());
                } else {
                    $formFraisF->get('repasRestaurant')->setData($ligneFraisForfait->getQuantite());
                }
            }
        }


        $message = false;
        $ligneFraisHorsForfait = new LigneFraisHorsForfait();
        $formFraisHF = $this->createForm(SaisirFicheFraisHorsForfaitType::class, $ligneFraisHorsForfait);
        $formFraisHF->handleRequest($request);

        if ($formFraisHF->isSubmitted() && $formFraisHF->isValid()) {
            // Définir le FicheFrais pour le LigneFraisHorsForfait
            $ligneFraisHorsForfait->setFicheFrais($ficheMoisUser);

            $ligneFraisHorsForfait->setDate($formFraisHF->get('date')->getData());
            $ligneFraisHorsForfait->setMontant($formFraisHF->get('montant')->getData());
            $ligneFraisHorsForfait->setLibelle($formFraisHF->get('libelle')->getData());

            // Définir une variable de session
            $request->getSession()->set('message', true);

            $entityManager->persist($ligneFraisHorsForfait);
            $entityManager->flush();

            return $this->redirectToRoute('app_saisir_fiche_frais', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('saisir_fiche_frais/index.html.twig', [
            'controller_name' => 'SaisirFicheFraisController',
            'ficheMoisUser' => $ficheMoisUser,
            'formFraisHF' => $formFraisHF,
            'message' => $message,
            'formFraisF' =>$formFraisF

        ]);
    }
}
