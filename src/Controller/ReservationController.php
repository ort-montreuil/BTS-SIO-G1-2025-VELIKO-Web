<?php
namespace App\Controller;

use App\Entity\Station;
use App\Form\ReservationType;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(Request $request, EntityManagerInterface $entityManager, Security $security, SessionInterface $session): Response
    {
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }


        $form = $this->createForm(ReservationType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            //location velo: venir retirer le velo


            $idStationDepart = $form->get('idStationDepart')->getData()->getStationId();

            $typeVelo = $form->get('typeVelo')->getData();

            $idStationArrivee = $form->get('idStationArrivee')->getData()->getStationId();

            $response = $this->makeCurl("/api/velos", "GET", "");



            foreach ($response as $velo) {
                $idVelo = $velo["velo_id"];



                // Vérifier si le vélo est disponible à la station de départ
                if ((int) $velo["station_id_available"] == (int) $idStationDepart
                    && $velo["status"] == "available"
                    && ($velo["type"] == $typeVelo)) {

                    // Mettre le vélo en location
                    $this->makeCurl("/api/velo/{$idVelo}/location", "PUT", "RG6F8do7ERFGsEgwkPEdW1Feyus0LXJ21E2EZRETTR65hN9DL8a3O8a");

                    $majResponse = $this->makeCurl("/api/velos", "GET", "");
                    foreach ($majResponse as $veloMaj)

                        // Vérifier si le vélo est en location et doit être ramené à la station de fin
                        if ((int) $veloMaj["station_id_available"] != (int) $idStationArrivee
                            && $veloMaj["status"] == "location") {
                            // Restauration du vélo à la station de fin
                            $this->makeCurl("/api/velo/{$idVelo}/restore/{$idStationArrivee}", "PUT", "RG6F8do7ERFGsEgwkPEdW1Feyus0LXJ21E2EZRETTR65hN9DL8a3O8a");

                        }else{
                            $this->addFlash('danger', 'Pas de possibilité de remettre le vélo à la station d\'arrivée');
                        }
                    $reservation = $form->getData();
                    $reservation->setIdUser($user->getId());
                   // $reservation->setIdStationDepart($form->get('idStationDepart')->getData()->getStationId());
                    $stationDep = $entityManager->getRepository(Station::class)->find((int) $idStationDepart);

                    if (!$stationDep) {
                        throw $this->createNotFoundException("Station non trouvée pour l'ID " . $idStationDepart);
                    }

                    $reservation->setIdStationDepart($stationDep);

                    //$reservation->setIdStationArrivee($form->get('idStationArrivee')->getData()->getStationId());
                    $stationArr = $entityManager->getRepository(Station::class)->find((int) $idStationArrivee);

                    if (!$stationArr) {
                        throw $this->createNotFoundException("Station non trouvée pour l'ID " . $idStationArrivee);
                    }

                    $reservation->setIdStationArrivee($stationArr);
                    $entityManager->persist($reservation);
                    $entityManager->flush();

                    $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');

                    return $this->redirectToRoute('app_home');
                }else{
                    $this->addFlash('danger', 'Pas de vélo disponible à la station de départ');

                }
            }
        }else{
            $this->addFlash('danger', 'Veuillez remplir correctement le formulaire');
        }
        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    public function makeCurl(string $url, string $methode, string $token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_PORT => "9042",
            CURLOPT_URL => "http://127.0.0.1:9042" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $methode,
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => [
                "Authorization:" . $token]
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
        }
        return $response;
    }


}