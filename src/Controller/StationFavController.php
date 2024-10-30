<?php

namespace App\Controller;

use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StationFavController extends AbstractController
{
    #[Route('/station/fav', name: 'app_station_fav')]

    public function index(): Response
    {


        return $this->render('station_fav/index.html.twig', [
            'controller_name' => 'StationFavController',
        ]);
    }

}
