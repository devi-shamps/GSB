<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\User;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable')]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        dd($request->get('id'));


        return $this->render('comptable/index.html.twig', [
            'controller_name' => 'ComptableController',
            'users' => $users,
        ]);
    }

}
