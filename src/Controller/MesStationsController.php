<?php
namespace App\Controller;

use App\Entity\Station;
use App\Entity\StationUser;
use App\Entity\User;
use App\Repository\StationRepository;
use App\Repository\StationUserRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesStationsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private StationRepository $stationRepository;
    private StationUserRepository $stationUserRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        StationRepository $stationRepository,
        StationUserRepository $stationUserRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->stationRepository = $stationRepository;
        $this->stationUserRepository = $stationUserRepository;
    }

    #[Route('/mes/stations', name: 'app_mes_stations')]
    public function index(): Response
    {
        // Récupérer toutes les stations
        $stations = $this->stationRepository->findAll();

        // Récupérer l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        // Récupérer les stations favorites de l'utilisateur
        $stationUsers = $this->stationUserRepository->findStationsByUserId($userId);
        $stationNames = [];

        foreach ($stationUsers as $stationUser) {
            $stationName = $this->stationRepository->find($stationUser['idStation'])->getName();
            $stationNames[] = [
                'name' => $stationName,
                'id' => $stationUser['idStation']
            ];
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

        return $this->render('mes_stations/index.html.twig', [
            'controller_name' => 'MesStationsController',
            'station_names' => $stationNames,
            'stations' => $stations,
            'favoriteStationIds' => $favoriteStationIds
        ]);
    }

    #[Route('/station/delete/{id}', name: 'app_station_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        $stationUser = $this->stationUserRepository->findOneBy([
            'idUser' => $userId,
            'idStation' => $id,
        ]);

        if ($stationUser) {
            $this->entityManager->remove($stationUser);
            $this->entityManager->flush();
            $this->addFlash('success', 'Station supprimée.');
        } else {
            $this->addFlash('error', 'Station non trouvée dans vos favoris.');
        }

        return $this->redirectToRoute('app_mes_stations');
    }

    #[Route('/station/add_favorite/{id}', name: 'app_add_favorite', methods: ['POST'])]
    public function addFavorite(int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        // Vérifier si la station est déjà dans les favoris de l'utilisateur
        $existingFavorite = $this->stationUserRepository->findOneBy([
            'idUser' => $userId,
            'idStation' => $id,
        ]);

        if ($existingFavorite) {
            $this->addFlash('info', 'Station déjà dans les favoris.');
        } else {
            $stationUser = new StationUser();
            $stationUser->setIdUser($userId);
            $stationUser->setIdStation($id);

            $this->entityManager->persist($stationUser);
            $this->entityManager->flush();

            $this->addFlash('success', 'Station ajoutée aux favoris.');
        }
        return $this->redirectToRoute('app_mes_stations');
    }
}
