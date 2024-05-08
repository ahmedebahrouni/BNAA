<?php

namespace App\Entity;

use App\Repository\OrganisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: OrganisateurRepository::class)]
class Organisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private $id;

    #[ORM\Column(name: 'nom', type: 'string', length: 50, nullable: false)]
    #[Assert\NotBlank(message: 'Le nom doit être non vide')]
    private $nom;

    #[ORM\Column(name: 'type', type: 'string', length: 50, nullable: false)]
    #[Assert\NotBlank(message: 'Le type doit être non vide')]
    private $type;

    #[ORM\Column(name: 'email_organisateur', type: 'string', length: 50, nullable: false)]
    #[Assert\NotBlank(message: "L'email doit être non vide")]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private $emailOrganisateur;

    #[ORM\Column(name: 'numtel_organisateur', type: 'integer', nullable: false)]
    #[Assert\NotBlank(message: 'Le numéro doit être non vide')]
    #[Assert\Length(
        min: 8,
        max: 8,
        minMessage: 'La taille du numéro est inférieure à 8',
        maxMessage: 'La taille du numéro est supérieure à 8'
    )]
    #[Assert\Positive(message: 'Le numéro est invalide')]
    private $numtelOrganisateur;

    #[ORM\OneToMany(targetEntity: Evenement::class, mappedBy: 'organisateur')]
    private $evenements;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getEmailOrganisateur(): ?string
    {
        return $this->emailOrganisateur;
    }

    public function setEmailOrganisateur(string $emailOrganisateur): self
    {
        $this->emailOrganisateur = $emailOrganisateur;
        return $this;
    }

    public function getNumtelOrganisateur(): ?int
    {
        return $this->numtelOrganisateur;
    }

    public function setNumtelOrganisateur(int $numtelOrganisateur): self
    {
        $this->numtelOrganisateur = $numtelOrganisateur;
        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->setOrganisateur($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getOrganisateur() === $this) {
                $evenement->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }
}
