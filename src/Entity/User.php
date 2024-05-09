<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    private ?int $id_user = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Veuillez insérer votre mot de passe")]
    private string $mdp;

    #[ORM\Column(name: 'agence', type: 'string', length: 50, nullable: false)]


    private ?string $agence;
    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Length(min: 3)]
    #[Assert\Regex(
        pattern: "/\d/",
        match: false,
        message: "Votre nom ne doit pas contenir un numéro"
    )]
    private string $nom;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\Length(min: 3)]
    #[Assert\Regex(
        pattern: "/\d/",
        match: false,
        message: "Votre prénom ne doit pas contenir un numéro"
    )]
    private ?string $prenom;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    #[Assert\Length(min: 3)]
    private ?string $email;

    #[ORM\Column(type: 'string', length: 50)]
    private string $role = 'CLIENT';

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Length(min: 8)]
    private ?int $numtel_user;
    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Length(min: 8)]
    #[Assert\Length(max: 8)]
    private ?int $cin;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $adresse_user;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $salaire ;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $connected = 0;
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $RIB;
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $statuscompte='CTX';
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $solde = 0;
    public function getStatuscompte(): ?string
    {
        return $this->statuscompte;
    }

    public function setStatuscompte(string $statuscompte): self
    {
        $this->statuscompte = $statuscompte;

        return $this;
    }

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(int $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getSalaire(): ?string
    {
        return $this->salaire;
    }

    public function setSalaire(int $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }


    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = "CLIENT";

        return $this;
    }

    public function getNumtelUser(): ?int
    {
        return $this->numtel_user;
    }

    public function setNumtelUser(int $numtel_user): self
    {
        $this->numtel_user = $numtel_user;

        return $this;
    }

    public function getAdresseUser(): ?string
    {
        return $this->adresse_user;
    }

    public function setAdresseUser(string $adresse_user): self
    {
        $this->adresse_user = $adresse_user;

        return $this;
    }

    public function getConnected(): ?int
    {
        return $this->connected;
    }

    public function setConnected(int $connected): self
    {
        $this->connected = $connected;

        return $this;
    }


    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getPassword(): ?string
    {
        return $this->mdp;
    }

    public function getSalt()
    {
        // not needed for most applications
    }

    public function eraseCredentials()
    {
        // not needed for most applications
    }

    public function getUsername()
    {
        // not needed for most applications
    }
    public function getRIB(): ?string
    {
        return $this->RIB;
    }

    public function setRIB(?string $RIB): self
    {
        $this->RIB = $RIB;

        return $this;
    }





    public function getAgence(): ?string
    {
        return $this->agence;
    }

    public function setAgence(?string $agence): self
    {
        $this->agence = $agence;

        return $this;
    }
    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(?string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }
}
