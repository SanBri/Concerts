<?php

namespace App\Entity;

use App\Entity\Concert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository; 
use Symfony\Component\Validator\Constraints as Assert; 

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Concert::class, inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $concert;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0, message="Veuillez réserver au moins 1 place")
     */
    private $reservedPlaces;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mailSent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConcert(): ?Concert
    {
        return $this->concert;
    }

    public function setConcert(Concert $concert): self
    {
        $this->concert = $concert;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getReservedPlaces(): ?int
    {
        return $this->reservedPlaces;
    }

    public function setReservedPlaces(int $reservedPlaces): self
    {
        $this->reservedPlaces = $reservedPlaces;

        return $this;
    }

    public function getMailSent(): ?bool
    {
        return $this->mailSent;
    }

    public function setMailSent(bool $mailSent): self
    {
        $this->mailSent = $mailSent;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
