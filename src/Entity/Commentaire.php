<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentaireRepository;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idComm;

    #[ORM\Column(length: 255)]
    private ?string $contenucommentaire;


    #[ORM\Column(name: "id_mededcin", nullable: true)] // Add this line
    private ?int $id_mededcin;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "commentaires")]
    #[ORM\JoinColumn(name: "id_mededcin", referencedColumnName: "id",nullable: true)]
    private ?User $medecin = null ;



 

    #[ORM\ManyToOne(targetEntity: Publications::class, inversedBy: "commentaires")]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id" ,nullable: true)]
    private ?Publications $publications = null;
   

    public function getIdComm(): ?int
    {
        return $this->idComm;
    }

    public function getContenucommentaire(): ?string
    {
        return $this->contenucommentaire;
    }

    public function setContenucommentaire(string $contenucommentaire): static
    {
        $this->contenucommentaire = $contenucommentaire;

        return $this;
    }
    public function getMedecin(): ?User
{
    return $this->medecin;
}


public function setMedecin(?User $medecin): self
{
    $this->medecin = $medecin;

    return $this;
}

public function getPublications(): ?Publications
{
    return $this->publications;
}

public function setPublications(?Publications $publications): self
{
    $this->publications = $publications;

    return $this;
}
    
}
