<?php

namespace App\Entity;

use App\Repository\LigneFraisForfaitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneFraisForfaitRepository::class)]
class LigneFraisForfait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'lignefraisforfait')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FicheFrais $fichefrais = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?FraisForfait $fraisforfait = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getFichefrais(): ?FicheFrais
    {
        return $this->fichefrais;
    }

    public function setFichefrais(?FicheFrais $fichefrais): static
    {
        $this->fichefrais = $fichefrais;

        return $this;
    }

    public function getFraisforfait(): ?FraisForfait
    {
        return $this->fraisforfait;
    }

    public function setFraisforfait(?FraisForfait $fraisforfait): static
    {
        $this->fraisforfait = $fraisforfait;

        return $this;
    }
}
