<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DecouvrirVelikoController extends AbstractController
{
    #[Route('/decouvrir/veliko', name: 'app_decouvrir_veliko')]
    public function index(): Response
    {
        return $this->render('decouvrir_veliko/index.html.twig', [
            'controller_name' => 'DecouvrirVelikoController',
        ]);
    } 
}