<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PublicationsRepository;
use App\Entity\Medecin;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


#[ORM\Entity(repositoryClass: PublicationsRepository::class)]
class Publications
{ 
    
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $texte = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datedepublication= null;

    #[ORM\Column(length: 255)]
    private ?string $specialiteassocie;

    #[ORM\Column(length: 255)]
    private ?string $tags = null;

    #[ORM\Column(length: 255)]
    private ?string $imagepath;

    #[ORM\Column(name: "nbJaime")]
    private ?int $nbjaime =0;

    #[ORM\Column(name: "id_med", type: Types::INTEGER, nullable: false)]
    private ?int $id_med = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "publications")]
    #[ORM\JoinColumn(name: "id_med", referencedColumnName: "id",nullable: false)]
    private ?User $medecin = null;
 
    #[ORM\OneToMany(mappedBy: 'publications', targetEntity: Commentaire::class,cascade:["remove","persist","merge"], orphanRemoval: true )]
    private Collection $commentaires;
    public function __construct()
    {
        $this->commentaires= new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): static
    {
        $this->texte = $texte;

        return $this;
    }

    public function getDatedepublication(): ?\DateTimeInterface
    {
        return $this->datedepublication;
    }

    public function setDatedepublication(\DateTimeInterface $datedepublication): static
    {
        $this->datedepublication = $datedepublication;

        return $this;
    }

    public function getSpecialiteassocie(): ?string
    {
        return $this->specialiteassocie;
    }

    public function setSpecialiteassocie(string $specialiteassocie): static
    {
        $this->specialiteassocie = $specialiteassocie;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(string $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getImagepath(): ?string
    {
        return $this->imagepath;
    }

    public function setImagePath(string $imagepath): static
    {
        $this->imagepath = $imagepath ?? 'téléchargement.jpg';

        return $this;
    }

    public function getNbjaime(): ?int
    {
        return $this->nbjaime;
    }

    public function setNbjaime(?int $nbjaime): static
    {
        $this->nbjaime = $nbjaime;

        return $this;
    }
    public function incrementNbjaime(): void
    {
        $this->nbjaime++;
    }

    public function decrementNbjaime(): void
    {
        $this->nbjaime = max(0, $this->nbjaime - 1);
    }
   
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }
    
    public function setCommentaires(Collection $commentaires): self
    {
        $this->commentaires = $commentaires;
    
        return $this;
    }

    public function setMedecin(?User $medecin): self
{
    $this->medecin = $medecin;

    return $this;
}
public function getMedecin(): ?User
{
    return $this->medecin;
}

   public function getIdMed(): ?int
    {
        return $this->id_med;
    }

    public function setIdMed(?int $idMed): self
    {
        $this->id_med = $idMed;

        return $this;
    }


}
