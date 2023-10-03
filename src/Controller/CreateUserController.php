<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CreateUserController extends AbstractController
{
    #[Route('/createuser', name: 'app_create_user')]
    public function index(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {
        $lesUser = $doctrine->getRepository(User::class)->findAll();

        //Creation du Login de l'utilisateur
        $theUser = new User();
        $theUser->setEmail('dimitri.dechamp@lycee-faure.fr');
        $theUser->setNom('Dechamp');
        $theUser->setPrenom('dimitri');
        $theUser->setLogin('Admin');
        $theUser->setAdresse('9bis rue de la paix');
        $theUser->setCp('74000');
        $theUser->setVille('Annecy');
        $theUser->setDateEmbauche(new \DateTime('Now'));
        $theUser->setRoles(['ROLE_ADMIN']);
        $plaintextPassword = '123';

        //Création du mot de passe Hasher
        $hashedPassword = $passwordHasher->hashPassword(
            $theUser,
            $plaintextPassword
        );

        $theUser->setPassword($hashedPassword);

        $doctrine->getManager()->persist($theUser);
        $doctrine->getManager()->flush();

        return $this->render('create_user/index.html.twig', [
            'controller_name' => 'CreateUserController',
        ]);
    }
}
