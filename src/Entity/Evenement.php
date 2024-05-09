<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private $reference;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    #[Assert\NotBlank(message: "Le nom doit être non vide")]
    private $nomEvenement;

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: "La date doit être supérieure ou égale à aujourd'hui"
    )]
    private $dateDebut;

    #[ORM\Column(type: 'datetime', nullable: false)]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: "La date doit être supérieure ou égale à aujourd'hui"
    )]
    #[Assert\Expression(
        "this.getDateFin() >= this.getDateDebut()",
        message: "La date de fin doit être postérieure à la date de début"
    )]
    private $dateFin;


    #[ORM\Column(type: 'float', nullable: false)]
    #[Assert\Positive(message: "Le prix doit être positif")]
    #[Assert\NotBlank(message: "Le prix doit être non vide")]
    private $prix;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private $image;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "La description doit être non vide")]
    private $description;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "L'emplacement doit être non vide")]
    private $emplacement;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $nbrParticipant = 0;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $nbrMaxParticipant;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $selected = 0;

    #[ORM\ManyToOne(targetEntity: Organisateur::class, inversedBy: 'evenements')]
    #[ORM\JoinColumn(nullable: false)]
    private $organisateur;

    // Getters and setters

    public function __toString()
    {
        return $this->nomEvenement . " " . $this->description . " " . $this->prix . " " . $this->emplacement;
    }
    public function getReference(): ?int
    {
        return $this->reference;
    }

    public function getNomEvenement()
    {
        return $this->nomEvenement;
    }

    public function setNomEvenement( $nomEvenement)
    {
        $this->nomEvenement = $nomEvenement;

        return $this;
    }

    public function getDateDebut(): ?datetime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(datetime $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?datetime
    {
        return $this->dateFin;
    }

    public function setDateFin(datetime $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

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

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): self
    {
        $this->emplacement = $emplacement;

        return $this;
    }



    public function getNbrParticipant(): ?int
    {
        return $this->nbrParticipant;
    }

    public function setNbrParticipant(int $nbrParticipant): self
    {
        $this->nbrParticipant = $nbrParticipant;

        return $this;
    }

    public function getNbrMaxParticipant(): ?int
    {
        return $this->nbrMaxParticipant;
    }

    public function setNbrMaxParticipant(int $nbrMaxParticipant): self
    {
        $this->nbrMaxParticipant = $nbrMaxParticipant;

        return $this;
    }

    public function getSelected(): ?int
    {
        return $this->selected;
    }

    public function setSelected(int $selected): self
    {
        $this->selected = $selected;

        return $this;
    }



    public function getOrganisateur(): ?Organisateur
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Organisateur $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }


}
