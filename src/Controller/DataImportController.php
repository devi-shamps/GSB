<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class DataImportController extends AbstractController
{
    #[Route('/data/import/users', name: 'app_data_import')]
    public function index(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $usersjson = file_get_contents('./gsbfraisusers.json');
        $users = json_decode($usersjson,true);

        foreach ($users as $user) {

            $newUser = new User();
            $mail = strtolower($user["prenom"]) . '.' . strtolower($user["nom"]) . '@gsb.fr';
            $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
            $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
            $varMaChaine = str_replace($search, $replace, $mail);
            $newUser->setEmail($varMaChaine);
            $newUser->setNom($user['nom']);
            $newUser->setPrenom($user['prenom']);
            $newUser->setLogin($user['login']);
            $newUser->setAdresse($user['adresse']);
            $newUser->setCp($user['cp']);
            $newUser->setVille($user['ville']);
            $dateEmbauche = new DateTime($user['dateEmbauche']);
            $newUser->setDateEmbauche($dateEmbauche);
            $newUser->setOldId($user['id']);

            $plaintextPassword = $user['mdp'];
            //Création du mot de passe Hasher
            $hashedPassword = $passwordHasher->hashPassword(
                $newUser,
                $plaintextPassword
            );
            $newUser->setPassword($hashedPassword);

            $doctrine->getManager()->persist($newUser);
            $doctrine->getManager()->flush();
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/data/import/fichefrais', name: 'app_data_import_fichesfrais')]
    public function indexFichesFrais(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $ffraisjson = file_get_contents('./fichefrais.json');
        $fichesFrais = json_decode($ffraisjson,true);

        foreach ($fichesFrais as $ficheFrais) {

            $newFF = new FicheFrais();
            $newFF->setMois($ficheFrais["mois"]);
            $newFF->setMontant($ficheFrais["montantValide"]);
            $newFF->setNbJustificatifs($ficheFrais["nbJustificatifs"]);
            $dateModif = new DateTime($ficheFrais['dateModif']);
            $newFF->setDateModif($dateModif);
            $user = $doctrine->getRepository(User::class)->findOneBy(["oldId" => $ficheFrais["idVisiteur"]]);
            $newFF->setUser($user);
            // A CHANGER
            switch ($ficheFrais["idEtat"]) {
                case 'CL':
                    $etat = $doctrine->getRepository(Etat::class)->find("1");
                    break;
                case 'CR':
                    $etat = $doctrine->getRepository(Etat::class)->find("2");
                    break;
                case 'RB':
                    $etat = $doctrine->getRepository(Etat::class)->find("3");
                    break;
                case 'VA':
                    $etat = $doctrine->getRepository(Etat::class)->find("4");
                    break;
                default:
                    $etat = $doctrine->getRepository(Etat::class)->find("1");
                    break;
            }
            $newFF->setEtat($etat);

            $doctrine->getManager()->persist($newFF);
            $doctrine->getManager()->flush();
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/data/import/ligneff', name: 'app_data_import_ligneff')]
    public function indexLigneFF(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $ligneffjson = file_get_contents('./lignefraisforfait.json');
        $LigneFFs = json_decode($ligneffjson, true);

        foreach ($LigneFFs as $LigneFF) {

            $newLigneFF = new LigneFraisForfait();
            $newLigneFF->setQuantite($LigneFF["quantite"]);
            switch ($LigneFF["idFraisForfait"]) {
                case 'ETP':
                    $fraisForfait = $doctrine->getRepository(FraisForfait::class)->find("1");
                    break;
                case 'KM':
                    $fraisForfait = $doctrine->getRepository(FraisForfait::class)->find("2");
                    break;
                case 'NUI':
                    $fraisForfait = $doctrine->getRepository(FraisForfait::class)->find("3");
                    break;
                case 'REP':
                    $fraisForfait = $doctrine->getRepository(FraisForfait::class)->find("4");
                    break;
                default:
                    $fraisForfait = $doctrine->getRepository(FraisForfait::class)->find("1");
                    break;
            }
            $newLigneFF->setFraisForfait($fraisForfait);

            $user = $doctrine->getRepository(User::class)->findOneBy(["oldId" => $LigneFF["idVisiteur"]]);
            $newLigneFF->setFicheFrais($doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $user, 'mois' => $LigneFF["mois"]]));

            $doctrine->getManager()->persist($newLigneFF);
            $doctrine->getManager()->flush();
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/data/import/lignefhf', name: 'app_data_import_lignefhf')]
    public function indexLigneFHF(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $lignefhfjson = file_get_contents('./lignefraishorsforfait.json');
        $LigneFHFs = json_decode($lignefhfjson, true);

        foreach ($LigneFHFs as $LigneFHF) {

            $newLigneFHF = new LigneFraisHorsForfait();
            $newLigneFHF->setLibelle($LigneFHF['libelle']);
            $newLigneFHF->setMontant($LigneFHF['montant']);
            $user = $doctrine->getRepository(User::class)->findOneBy(["oldId" => $LigneFHF["idVisiteur"]]);
            $newLigneFHF->setFicheFrais($doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $user, 'mois' => $LigneFHF["mois"]]));
            $dateFHF = new DateTime($LigneFHF['date']);
            $newLigneFHF->setDate($dateFHF);

            $doctrine->getManager()->persist($newLigneFHF);
            $doctrine->getManager()->flush();
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }



}
