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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateReservation = null;

    #[ORM\Column(length: 255)]
    private ?string $Station_Depart = null;

    #[ORM\Column(length: 255)]
    private ?string $Station_arFin = null;

    #[ORM\Column]
    private ?int $IdUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->DateReservation;
    }

    public function setDateReservation(\DateTimeInterface $DateReservation): static
    {
        $this->DateReservation = $DateReservation;

        return $this;
    }

    public function getStationDepart(): ?string
    {
        return $this->Station_Depart;
    }

    public function setStationDepart(string $Station_Depart): static
    {
        $this->Station_Depart = $Station_Depart;

        return $this;
    }

    public function getStationArFin(): ?string
    {
        return $this->Station_arFin;
    }

    public function setStationArFin(string $Station_arFin): static
    {
        $this->Station_arFin = $Station_arFin;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->IdUser;
    }

    public function setIdUser(int $IdUser): static
    {
        $this->IdUser = $IdUser;

        return $this;
    }
}
