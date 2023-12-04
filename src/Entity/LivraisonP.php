<?php

namespace App\Entity;

use App\Repository\LivraisonPRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivraisonPRepository::class)]
class LivraisonP
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est requis.')]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.'
    )]
    private ?string $nomliv = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom est requis.')]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.'
    )]
    private ?string $prenomliv = null;

    /*#[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone est requis.')]
    #[Assert\Regex(
        pattern: '/^\d{8}$/',
        message: 'Le numéro de téléphone doit être composé de 8 chiffres.'
    )]*/
    private ?string $phonelivr = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'adresse est requise.')]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'adresse email est requise.')]
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide.')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        message: 'L\'adresse email doit avoir la forme user@example.com.'
    )]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomliv(): ?string
    {
        return $this->nomliv;
    }

    public function setNomliv(string $nomliv): static
    {
        $this->nomliv = $nomliv;

        return $this;
    }

    public function getPrenomliv(): ?string
    {
        return $this->prenomliv;
    }

    public function setPrenomliv(string $prenomliv): static
    {
        $this->prenomliv = $prenomliv;

        return $this;
    }

    public function getPhonelivr(): ?string
    {
        return $this->phonelivr;
    }

    public function setPhonelivr(string $phonelivr): static
    {
        $this->phonelivr = $phonelivr;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
