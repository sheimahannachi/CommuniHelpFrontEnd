<?php

namespace App\Entity;


use App\Repository\DonationformsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DonationformsRepository::class)]
class Donationforms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Please enter a valid amount')]
    #[Assert\Type(type: 'float', message: 'The value {{ value }} is not a valid {{ type }}')]
    #[ORM\Column]
    private ?float $montant = null;

    #[Assert\NotBlank(message: 'Email should not be blank')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'Nom should not be blank')]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[Assert\NotBlank(message: 'Prenom should not be blank')]
    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[Assert\Choice(
        choices: [
            'tunis', 'sfax', 'sousse', 'kairouan', 'bizerte', 'gabes', 'ariana',
            'gafsa', 'la_marsa', 'ben_arous', 'monastir', 'medenine', 'nabeul',
            'tataouine', 'hammamet', 'mahdia', 'kasserine', 'djerba', 'siliana',
            'tozeur', 'kebili', 'beja', 'le_kef', 'jendouba'
        ],
        message: 'Please select a valid ville'
    )]
        #[ORM\Column(length: 255)]
    private ?string $ville = null;

 #[Assert\Regex(
        pattern: '/^\d{16}$/',
        message: 'Carte bancaire should be a 16-digit number'
    )]
    #[Assert\NotBlank(message: 'Carte bancaire should not be blank')]
    #[ORM\Column(length: 100)]
    private ?string $carteBancaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCarteBancaire(): ?string
    {
        return $this->carteBancaire;
    }

    public function setCarteBancaire(string $carteBancaire): static
    {
        $this->carteBancaire = $carteBancaire;

        return $this;
    }




    #[ORM\OneToMany(targetEntity: History::class, mappedBy: 'donationform')]
    private $histories;

    // Constructor to initialize the $histories property
    public function __construct()
    {
        $this->histories = new ArrayCollection();
    }

    // Add a method to manage History association
    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setDonationform($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->removeElement($history)) {
            // Set the owning side to null (unless already changed)
            if ($history->getDonationform() === $this) {
                $history->setDonationform(null);
            }
        }

        return $this;
    }
}
