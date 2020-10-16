<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *      fields = {"email"},
 *      message = "Cet e-mail est déjà utilisé."
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="Votre mot de passe doit contenir 8 caractères minimums") 
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Les 2 mots de passe ne correspondent pas")
     */
    public $confirmPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="user", orphanRemoval=true)
     */
    private $reservations;

    /**
     * @ORM\OneToMany(targetEntity=Concert::class, mappedBy="organizer")
     */
    private $organizedConcerts;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->organizedConcerts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function setUsername(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }

    public function getSalt() {}
    public function getRoles() {
        return ['ROLE_USER'];
    }
    public function eraseCredentials() {}

    /**
     * @return Collection|Concert[]
     */
    public function getOrganizedConcerts(): Collection
    {
        return $this->organizedConcerts;
    }

    public function addOrganizedConcert(Concert $organizedConcert): self
    {
        if (!$this->organizedConcerts->contains($organizedConcert)) {
            $this->organizedConcerts[] = $organizedConcert;
            $organizedConcert->setOrganizer($this);
        }

        return $this;
    }

    public function removeOrganizedConcert(Concert $organizedConcert): self
    {
        if ($this->organizedConcerts->contains($organizedConcert)) {
            $this->organizedConcerts->removeElement($organizedConcert);
            // set the owning side to null (unless already changed)
            if ($organizedConcert->getOrganizer() === $this) {
                $organizedConcert->setOrganizer(null);
            }
        }

        return $this;
    }
}
