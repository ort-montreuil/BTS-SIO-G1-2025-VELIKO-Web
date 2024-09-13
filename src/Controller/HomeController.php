<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $curl2 = curl_init();

        curl_setopt_array($curl2, [
            CURLOPT_URL => "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_status.json",
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

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://velib-metropole-opendata.smovengo.cloud/opendata/Velib_Metropole/station_information.json",
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

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'response'    => $response,
            'response2'    => $response2
        ]);
    }
}