<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ConcertRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert; 

/**
 * @ORM\Entity(repositoryClass=ConcertRepository::class)
 */
class Concert
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez préciser le nom de l'événement")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Veuillez entrer une description de l'événement")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual("today", message="La date est déjà passée")
     * @Assert\NotBlank(message = "Veuillez préciser la date de l'événement")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez préciser la ville de l'événément")
     */
    private $city;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message = "Veuillez préciser l'adresse de l'événement")
     */
    private $adress;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message = "Veuillez préciser un prix (0 si l'événement est gratuit)")
     * @Assert\Range(
     *      min = 0,
     *      max = 9999,
     *      notInRangeMessage = " Le prix doit être compris entre {{ min }} et {{ max }}",
     * )
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message = "Veuillez préciser le nombre de places maximum disponibles")
     * @Assert\GreaterThanOrEqual(propertyPath="reservation", message="Le nombre de places ne peut pas être inférieur aux réservations déjà effectuées !")
     * @Assert\Range(
     *      min = 1,
     *      max = 9999,
     *      notInRangeMessage = " Entre {{ min }} et {{ max }} places doivent être disponibles",
     * )
     */
    private $maxPlaces;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="concert", orphanRemoval=true)
     */
    private $reservations;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $reservation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="organizedConcerts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizer;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $imagePath;

    /**
     * @ORM\Column(type="string", length=2083, nullable=true)
     */
    private $videoURL;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMaxPlaces(): ?int
    {
        return $this->maxPlaces;
    }

    public function setMaxPlaces(int $maxPlaces): self
    {
        $this->maxPlaces = $maxPlaces;

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
            $reservation->setConcert($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getConcert() === $this) {
                $reservation->setConcert(null);
            }
        }

        return $this;
    }

    public function getReservation(): ?int
    {
        return $this->reservation;
    }

    public function setReservation(?int $reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getVideoURL(): ?string
    {
        return $this->videoURL;
    }

    public function setVideoURL(?string $videoURL): self
    {
        $this->videoURL = $videoURL;

        return $this;
    }
}
