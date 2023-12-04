<?php

namespace App\Entity;

use App\Repository\ListecRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListecRepository::class)]
class Listec
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomproduit = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\Column(length: 255)]
    private ?string $nomdest = null;

    #[ORM\Column(length: 255)]
    private ?string $emailc_ = null;

    #[ORM\Column(length: 255)]
    private ?string $adressec_ = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomproduit(): ?string
    {
        return $this->nomproduit;
    }

    public function setNomproduit(string $nomproduit): static
    {
        $this->nomproduit = $nomproduit;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getNomdest(): ?string
    {
        return $this->nomdest;
    }

    public function setNomdest(string $nomdest): static
    {
        $this->nomdest = $nomdest;

        return $this;
    }

    public function getEmailc(): ?string
    {
        return $this->emailc_;
    }

    public function setEmailc(string $emailc_): static
    {
        $this->emailc_ = $emailc_;

        return $this;
    }

    public function getAdressec(): ?string
    {
        return $this->adressec_;
    }

    public function setAdressec(string $adressec_): static
    {
        $this->adressec_ = $adressec_;

        return $this;
    }
  /**
     * @ORM\ManyToOne(targetEntity=ProduitsInfo::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $produitsInfo;

    // ... (other methods)

    public function getProduitsInfo(): ?ProduitsInfo
    {
        return $this->produitsInfo;
    }

    public function setProduitsInfo(?ProduitsInfo $produitsInfo): self
    {
        $this->produitsInfo = $produitsInfo;

        return $this;
    }


}
