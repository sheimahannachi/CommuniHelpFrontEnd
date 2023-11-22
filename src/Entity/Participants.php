<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ParticipantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantsRepository::class)]
class Participants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_p = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $num = null;

    #[ORM\Column(length: 500)]
    private ?string $mail = null;

    #[ORM\Column]
    private ?int $id_ev = null;

    #[ORM\ManyToOne(targetEntity: Test::class)]
    #[ORM\JoinColumn(name: 'id_ev', referencedColumnName: 'id')] // Vérifiez que les noms de colonnes sont corrects
    private ?Test $test = null;

    public function getId_p(): ?int
    {
        return $this->id_p;
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

    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(string $num): static
    {
        $this->num = $num;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getIdEv(): ?int
    {
        return $this->id_ev;
    }

    public function setIdEv(int $id_ev): static
    {
        $this->id_ev = $id_ev;

        return $this;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): static
    {
        $this->test = $test;

        return $this;
    }
}
