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
            ['email' => 'user1@example.com', 'nom' => 'Dupont', 'prenom' => 'Jean', 'ville' => 'Paris', 'codePostale' => '75001', 'dateNaissance' => '1985-06-15', 'adresse' => '5 Avenue des Champs'],
            ['email' => 'user2@example.com', 'nom' => 'Martin', 'prenom' => 'Pierre', 'ville' => 'Lyon', 'codePostale' => '69001', 'dateNaissance' => '1992-08-21', 'adresse' => '12 Rue de la République'],
            ['email' => 'user3@example.com', 'nom' => 'Bernard', 'prenom' => 'Sophie', 'ville' => 'Marseille', 'codePostale' => '13001', 'dateNaissance' => '1990-03-10', 'adresse' => '45 Boulevard de la Mer'],
            ['email' => 'user4@exemple.com', 'nom' => 'Doe', 'prenom' => 'John', 'ville' => 'Lille', 'codePostale' => '59000', 'dateNaissance' => '1987-11-30', 'adresse' => '78 Place du Général'],
            ['email' => 'user5@exemple.com', 'nom' => 'Doe', 'prenom' => 'Jane', 'ville' => 'Bordeaux', 'codePostale' => '33000', 'dateNaissance' => '1995-07-05', 'adresse' => '101 Quai des Chartrons'],
            ['email' => 'user6@example.com', 'nom' => 'Durand', 'prenom' => 'Alice', 'ville' => 'Nantes', 'codePostale' => '44000', 'dateNaissance' => '1983-04-18', 'adresse' => '23 Rue du Château'],
            ['email' => 'user7@example.com', 'nom' => 'Petit', 'prenom' => 'Louis', 'ville' => 'Toulouse', 'codePostale' => '31000', 'dateNaissance' => '1989-12-02', 'adresse' => '56 Allée des Capitouls'],
            ['email' => 'user8@example.com', 'nom' => 'Moreau', 'prenom' => 'Emma', 'ville' => 'Nice', 'codePostale' => '06000', 'dateNaissance' => '1993-09-25', 'adresse' => '34 Promenade des Anglais'],
            ['email' => 'user9@example.com', 'nom' => 'Lefevre', 'prenom' => 'Lucas', 'ville' => 'Strasbourg', 'codePostale' => '67000', 'dateNaissance' => '1986-05-14', 'adresse' => '89 Rue de la Cathédrale'],
            ['email' => 'user10@example.com', 'nom' => 'Roux', 'prenom' => 'Chloe', 'ville' => 'Montpellier', 'codePostale' => '34000', 'dateNaissance' => '1997-10-08', 'adresse' => '17 Rue de l’Esplanade'],
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles([]);
            $user->setPassword($this->hasher->hashPassword($user, 'Motdepasse123//'));
            $user->setVerified(1);
            $user->setDateNaissance(new \DateTime($data['dateNaissance']));
            $user->setAdresse($data['adresse']);
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


//marche ps
        //---------Reservations----------
        //---------Reservations----------
        $users = $manager->getRepository(User::class)->findAll();
        $stations = $manager->getRepository(Station::class)->findAll();
        $typesVelo = ['Electrique', 'Mecanique'];

        if (count($users) > 0 && count($stations) > 1) {
            for ($i = 1; $i <= 10; $i++) {
                $reservation = new Reservation();
                $reservation->setIdUser($users[array_rand($users)]);

                $randomDate = new \DateTime();
                $randomDate->setTimestamp(mt_rand(strtotime('2010-01-01'), strtotime('2024-12-31')));
                $reservation->setDateReservation($randomDate);

                $station = $stations[array_rand($stations)]; // Même station possible
                $reservation->setIdStationDepart($station);
                $reservation->setIdStationArrivee($station);

                $reservation->setTypeVelo($typesVelo[array_rand($typesVelo)]);

                $manager->persist($reservation);
            }
        }

        $manager->flush();

    }
}