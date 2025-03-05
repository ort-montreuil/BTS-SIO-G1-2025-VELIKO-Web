<?php

namespace App\Controller;

use App\Entity\StationUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/forced_mdp', name: 'app_forced_mdp')]
    public function forced_mpd(): Response
    {
        return $this->render('home/forced_mdp.html.twig');
    }

    #[Route('/blocked', name: 'app_blocked')]
    public function bloqued(): Response
    {
        return $this->render('home/blocked.html.twig');
    }



    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        if($this->isGranted('IS_AUTHENTICATED_FULLY')){
            /**@var User $user */
            $user = $this->getUser();
            if ($user->isBloqued()) {
                return $this->redirectToRoute('app_blocked');
            }
        }
        // Récupérer les informations des stations
        $curl2 = curl_init();

        curl_setopt_array($curl2, [
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . "/stations/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response2 = curl_exec($curl2);
        $err2 = curl_error($curl2);

        curl_close($curl2);

        if ($err2) {
            return new Response("cURL Error #:" . $err2, Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            $response2 = json_decode($response2, true);
        }
        //
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . "/stations",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return new Response("cURL Error #:" . $err, Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            $response = json_decode($response, true);
        }

        $stations = [];

        // afficher les informations des stations
        foreach ($response as $infostat) {
            foreach ($response2 as $infovelo) {
                if ($infostat['station_id'] == $infovelo['station_id']) {
                    $stations_data = [
                        'nom' => $infostat['name'],
                        'lat' => $infostat['lat'],
                        'lon' => $infostat['lon'],
                        'vélosDisponible' => $infovelo['num_bikes_available'],
                        'véloMechanique' => $infovelo['num_bikes_available_types'][0]['mechanical'],
                        'véloElectrique' => $infovelo['num_bikes_available_types'][1]['ebike'],
                        "id" => $infostat["station_id"]
                    ];
                    $stations[] = $stations_data;
                    break;

                }
            }
        }
        $user = $this->getUser();
        $favoriteStationIds = [];

        if ($user) {
            $stationUserRepository = $this->entityManager->getRepository(StationUser::class);

            // Obtenir les stations favorites de l'utilisateur
            $favorites = $stationUserRepository->findBy(['idUser' => $user->getId()]);
            $favoriteStationIds = array_map(function ($favorite) {
                return $favorite->getIdStation();
            }, $favorites);
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'response' => $response,
            'response2' => $response2,
            'stations' => $stations,
            'favoriteStationIds' => $favoriteStationIds
        ]);
    }
}