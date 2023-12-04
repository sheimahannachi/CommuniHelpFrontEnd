<?php


namespace App\Entity;

use App\Repository\HistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
class History
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $articleId = null;

    #[ORM\Column(length: 255)]
    private ?string $articleVille = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $donationId = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $donationVille = null;

    #[ORM\Column(length: 255)]
    private ?string $carteBancaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): static
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function getArticleVille(): ?string
    {
        return $this->articleVille;
    }

    public function setArticleVille(string $articleVille): static
    {
        $this->articleVille = $articleVille;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDonationId(): ?int
    {
        return $this->donationId;
    }

    public function setDonationId(int $donationId): static
    {
        $this->donationId = $donationId;

        return $this;
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

    public function getDonationVille(): ?string
    {
        return $this->donationVille;
    }

    public function setDonationVille(string $donationVille): static
    {
        $this->donationVille = $donationVille;

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
    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'histories')]
    #[ORM\JoinColumn(name: 'article_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Article $article = null;
    

    // Getters and setters for Article association
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;
        return $this;
    }
    #[ORM\ManyToOne(targetEntity: Donationforms::class, inversedBy: 'histories')]
    #[ORM\JoinColumn(name: 'donation_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Donationforms $donationform = null;
    // Getters and setters for Donationforms association
    public function getDonationform(): ?Donationforms
    {
        return $this->donationform;
    }

    public function setDonationform(?Donationforms $donationform): static
    {
        $this->donationform = $donationform;
        return $this;
    }
}
