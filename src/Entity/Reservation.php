<?php
namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idUser = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReservation = null;

    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(name: "station_id_depart", referencedColumnName: "station_id", nullable: false)]
    private ?Station $idStationDepart = null;

    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(name: "station_id_arrivee", referencedColumnName: "station_id", nullable: false)]
    private ?Station $idStationArrivee = null;

    #[ORM\Column(length: 25)]
    private ?string $typeVelo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTimeInterface $dateReservation): static
    {
        $this->dateReservation = $dateReservation;
        return $this;
    }

    public function getIdStationDepart(): ?Station
    {
        return $this->idStationDepart;
    }

    public function setIdStationDepart(?Station $idStationDepart): static
    {
        $this->idStationDepart = $idStationDepart;
        return $this;
    }

    public function getIdStationArrivee(): ?Station
    {
        return $this->idStationArrivee;
    }

    public function setIdStationArrivee(?Station $idStationArrivee): static
    {
        $this->idStationArrivee = $idStationArrivee;
        return $this;
    }

    public function getTypeVelo(): ?string
    {
        return $this->typeVelo;
    }

    public function setTypeVelo(string $typeVelo): static
    {
        $this->typeVelo = $typeVelo;
        return $this;
    }
}
