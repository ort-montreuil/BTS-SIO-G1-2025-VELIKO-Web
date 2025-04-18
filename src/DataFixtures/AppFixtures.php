<?php
namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Reservation;
use App\Entity\Station;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        //---------Admin user----------
        $adminUser = new User();
        $adminUser->setEmail('toto@dev.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword($this->hasher->hashPassword($adminUser, 'Motdepasse123//'));
        $adminUser->setVerified(1);
        $adminUser->setDateNaissance(new \DateTime('1990-01-01'));
        $adminUser->setAdresse('1 rue de la rue');
        $adminUser->setVille('Paris');
        $adminUser->setCodePostale('75000');
        $adminUser->setNom('Toto');
        $adminUser->setPrenom('Mr');
        $adminUser->setBloqued(0);
        $adminUser->setForcedMdp(0);
        $manager->persist($adminUser);

        //---------Users----------
        $usersData = [
            ['email' => 'user1@example.com', 'nom' => 'Dupont', 'prenom' => 'Jean', 'ville' => 'Paris', 'codePostale' => '75001'],
            ['email' => 'user2@example.com', 'nom' => 'Martin', 'prenom' => 'Pierre', 'ville' => 'Lyon', 'codePostale' => '69001'],
            ['email' => 'user3@example.com', 'nom' => 'Bernard', 'prenom' => 'Sophie', 'ville' => 'Marseille', 'codePostale' => '13001'],
            ['email' => 'user4@exemple.com', 'nom' => 'Doe', 'prenom' => 'John', 'ville' => 'Lille', 'codePostale' => '59000'],
            ['email' => 'user5@exemple.com', 'nom' => 'Doe', 'prenom' => 'Jane', 'ville' => 'Bordeaux', 'codePostale' => '33000'],
            ['email' => 'user6@example.com', 'nom' => 'Durand', 'prenom' => 'Alice', 'ville' => 'Nantes', 'codePostale' => '44000'],
            ['email' => 'user7@example.com', 'nom' => 'Petit', 'prenom' => 'Louis', 'ville' => 'Toulouse', 'codePostale' => '31000'],
            ['email' => 'user8@example.com', 'nom' => 'Moreau', 'prenom' => 'Emma', 'ville' => 'Nice', 'codePostale' => '06000'],
            ['email' => 'user9@example.com', 'nom' => 'Lefevre', 'prenom' => 'Lucas', 'ville' => 'Strasbourg', 'codePostale' => '67000'],
            ['email' => 'user10@example.com', 'nom' => 'Roux', 'prenom' => 'Chloe', 'ville' => 'Montpellier', 'codePostale' => '34000'],
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles([]);
            $user->setPassword($this->hasher->hashPassword($user, 'Motdepasse123//'));
            $user->setVerified(1);
            $user->setDateNaissance(new \DateTime('1990-01-01'));
            $user->setAdresse('123 Rue Test');
            $user->setVille($data['ville']);
            $user->setCodePostale($data['codePostale']);
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setConfirmationToken(null);
            $user->setPasswordResetToken(null);
            $user->setPasswordResetTokenExpiresAt(null);
            $user->setBloqued(false);
            $user->setForcedMdp(false);
            $manager->persist($user);
        }

        //---------Reservations----------
        // Récupérer les utilisateurs et les stations depuis la base de données
        $users = $manager->getRepository(User::class)->findAll();
        $stations = $manager->getRepository(Station::class)->findAll();

        if (count($users) > 0 && count($stations) > 1) {
            for ($i = 1; $i <= 10; $i++) {
                $reservation = new Reservation();
                $reservation->setIdUser($users[array_rand($users)]);
                $reservation->setDateReservation((new \DateTime())->setTimestamp(mt_rand(strtotime('2010-01-01'), strtotime('2024-12-31'))));

                // Sélection aléatoire de la station de départ
                $stationDepart = $stations[array_rand($stations)];

                // Sélection de la station d'arrivée, différente de la station de départ
                do {
                    $stationArrivee = $stations[array_rand($stations)];
                } while ($stationDepart === $stationArrivee); // Boucle pour s'assurer qu'elles sont différentes

                $reservation->setIdStationDepart($stationDepart);
                $reservation->setIdStationArrivee($stationArrivee);
                $reservation->setTypeVelo(['Electrique', 'Mecanique'][array_rand(['Electrique', 'Mecanique'])]);

                // Sauvegarder la réservation
                $manager->persist($reservation);
            }
        }

        // Sauvegarder toutes les entités
        $manager->flush();
    }
}
