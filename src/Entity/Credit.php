<?php
namespace App\Entity;

use App\Repository\CreditRepository;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\User; // Import the User entity


#[ORM\Entity(repositoryClass: CreditRepository::class)]
class Credit
{
    #[ORM\Id]
    #[ORM\Column(name: "idcredit", type: "integer", nullable: false)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $idcredit = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $AMOR ;
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $statuscompte;

    #[ORM\Column(name: "id_user", type: "integer", nullable: false)]
    private $idUser;

    #[ORM\Column(name: "date_credit", type: "string", length: 50, nullable: false)]
    private $date_credit;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $RIB;
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $decision;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $salaire ;


    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $solde = 0;
    #[ORM\Column(type: 'float', nullable: true)]
    private ?int $montant  ;
    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }
    public function getdecision(): ?string
    {
        return $this->decision;
    }

    public function setdecision(string $decision): self
    {
        $this->decision = $decision;

        return $this;
    }
    public function getAMOR(): ?string
    {
        return $this->AMOR;
    }

    public function setAMOR(int $AMOR): self
    {
        $this->AMOR = $AMOR;

        return $this;
    }
    public function getStatuscompte(): ?string
    {
        return $this->statuscompte;
    }

    public function setStatuscompte(?string $statuscompte): self
    {
        $this->statuscompte = $statuscompte;
        return $this;
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
    public function getSalaire(): ?int
    {
        return $this->salaire;
    }

    public function setSalaire(?int $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }


    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(?int $solde): self
    {
        // Check if $solde is null, if so, set it to 0
        $this->solde = $solde ?? 0;

        return $this;
    }



    public function getIdCredit(): ?int
    {
        return $this->idcredit;
    }
    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }
    public function getDateCredit(): ?string
    {
        return $this->date_credit;
    }

    public function setDateCredit(string $date_credit): self
    {
        $this->date_credit = $date_credit;

        return $this;
    }
}


