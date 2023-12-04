<?php

namespace App\Entity;

use App\Repository\ProduitsInfoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitsInfoRepository::class)]
class ProduitsInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nomProd = null;

    #[ORM\Column]
    private ?int $prixProd = null;

    #[ORM\Column(length: 255)]
    private ?string $descProd = null;

    #[ORM\Column(length: 255)]
    private ?string $statutProd = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProd(): ?string
    {
        return $this->nomProd;
    }

    public function setNomProd(string $nomProd): static
    {
        $this->nomProd = $nomProd;

        return $this;
    }

    public function getPrixProd(): ?int
    {
        return $this->prixProd;
    }

    public function setPrixProd(int $prixProd): static
    {
        $this->prixProd = $prixProd;

        return $this;
    }

    public function getDescProd(): ?string
    {
        return $this->descProd;
    }

    public function setDescProd(string $descProd): static
    {
        $this->descProd = $descProd;

        return $this;
    }

    public function getStatutProd(): ?string
    {
        return $this->statutProd;
    }

    public function setStatutProd(string $statutProd): static
    {
        $this->statutProd = $statutProd;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
