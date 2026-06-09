<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HopitalController extends AbstractController
{
    #[Route('/hopital', name: 'hopital_index')]
    public function index(): Response
    {
        return $this->render('hopital/index.html.twig', [
            'title' => 'Hôpitaux'
        ]);
    }
        #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('hopital/login.html.twig');
    }
        #[Route('/hopital/inscription', name: 'hopital_inscription')]
    public function inscription(): Response
    {
        return $this->render('hopital/register.html.twig');
    }
}