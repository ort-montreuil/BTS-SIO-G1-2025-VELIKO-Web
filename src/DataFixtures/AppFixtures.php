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
            // Add more users as needed
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


//marche ps
        //---------Reservations----------
        $user = $manager->getRepository(User::class)->findAll();
        $stations = $manager->getRepository(Station::class)->findAll();

        if (count($user) > 0 && count($stations) > 1) {
            for ($i = 1; $i <= 10; $i++) {
                $reservation = new Reservation();
                $reservation->setIdUser($user[array_rand($user)]);
                $reservation->setDateReservation((new \DateTime())->setTimestamp(mt_rand(strtotime('2010-01-01'), strtotime('2024-12-31'))));
                $reservation->setIdStationDepart($stations[array_rand($stations)]);
                $reservation->setIdStationArrivee($stations[array_rand($stations)]);
                $reservation->setTypeVelo(['Electrique', 'Mecanique'][array_rand(['Electrique', 'Mecanique'])]);
                $manager->persist($reservation);
            }
        }

        // Save all entities
        $manager->flush();
    }
}
