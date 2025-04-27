<?php

namespace App\Entity;

use App\Repository\FicheFraisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheFraisRepository::class)]
class FicheFrais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $mois = null;

    #[ORM\Column]
    private ?int $nbJustificatifs = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateModif = null;

    #[ORM\ManyToOne(inversedBy: 'ficheFrais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'ficheFrais', targetEntity: LigneFraisHorsForfait::class, orphanRemoval: true, cascade: ["persist"])]
    private Collection $ligneFraisHorsForfaits;

    #[ORM\ManyToOne(inversedBy: 'ficheFrais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etat = null;

    #[ORM\OneToMany(mappedBy: 'ficheFrais', targetEntity: LigneFraisForfait::class, orphanRemoval: true, cascade: ["persist"])]
    private Collection $ligneFraisForfaits;

    #[ORM\Column]
    private ?float $montantValid = null;

    public function __construct()
    {
        $this->ligneFraisHorsForfaits = new ArrayCollection();
        $this->ligneFraisForfaits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMois(): ?\DateTimeImmutable
    {
        return $this->mois;
    }

    public function setMois(\DateTimeImmutable $mois): static
    {
        $this->mois = $mois;
        return $this;
    }

    public function getNbJustificatifs(): ?int
    {
        return $this->nbJustificatifs;
    }

    public function setNbJustificatifs(int $nbJustificatifs): static
    {
        $this->nbJustificatifs = $nbJustificatifs;
        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDateModif(\DateTimeInterface $dateModif): static
    {
        $this->dateModif = $dateModif;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, LigneFraisHorsForfait>
     */
    public function getLigneFraisHorsForfaits(): Collection
    {
        return $this->ligneFraisHorsForfaits;
    }

    public function addLigneFraisHorsForfait(LigneFraisHorsForfait $ligneFraisHorsForfait): static
    {
        if (!$this->ligneFraisHorsForfaits->contains($ligneFraisHorsForfait)) {
            $this->ligneFraisHorsForfaits->add($ligneFraisHorsForfait);
            $ligneFraisHorsForfait->setFicheFrais($this);
        }

        return $this;
    }

    public function removeLigneFraisHorsForfait(LigneFraisHorsForfait $ligneFraisHorsForfait): static
    {
        if ($this->ligneFraisHorsForfaits->removeElement($ligneFraisHorsForfait)) {
            if ($ligneFraisHorsForfait->getFicheFrais() === $this) {
                $ligneFraisHorsForfait->setFicheFrais(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    /**
     * @return Collection<int, LigneFraisForfait>
     */
    public function getLigneFraisForfaits(): Collection
    {
        return $this->ligneFraisForfaits;
    }

    public function addLigneFraisForfait(LigneFraisForfait $ligneFraisForfait): static
    {
        if (!$this->ligneFraisForfaits->contains($ligneFraisForfait)) {
            $this->ligneFraisForfaits->add($ligneFraisForfait);
            $ligneFraisForfait->setFicheFrais($this);
        }

        return $this;
    }

    public function removeLigneFraisForfait(LigneFraisForfait $ligneFraisForfait): static
    {
        if ($this->ligneFraisForfaits->removeElement($ligneFraisForfait)) {
            if ($ligneFraisForfait->getFicheFrais() === $this) {
                $ligneFraisForfait->setFicheFrais(null);
            }
        }

        return $this;
    }

    public function getMontantValid(): float
    {
        return (float) $this->montantValid;
    }

    public function setMontantValid(string $montantValid): static
    {
        $this->montantValid = $montantValid;
        return $this;
    }

    public function getMontantTotalForfait(): float
    {
        $montantTotalForfait = 0.0;

        foreach ($this->ligneFraisForfaits as $ligneFraisForfait) {
            $montantTotalForfait += $ligneFraisForfait->getQuantite() * $ligneFraisForfait->getFraisForfait()->getMontant();
        }

        return $montantTotalForfait;
    }

    public function getMontantTotalHorsForfait(): float
    {
        $montantTotalHorsForfait = 0.0;

        foreach ($this->ligneFraisHorsForfaits as $ligneFraisHorsForfait) {
            $montantTotalHorsForfait += $ligneFraisHorsForfait->getMontant();
        }

        return $montantTotalHorsForfait;
    }

    public function getMontantTotal(): float
    {
        return $this->getMontantTotalForfait() + $this->getMontantTotalHorsForfait();
    }

    public function calculateTotalAmount(): float
    {
        return $this->getMontantTotalForfait() + $this->getMontantValid();
    }

    public function getLigneFraisForfait(): Collection
    {
        return $this->ligneFraisForfaits;
    }

    public function TotalLigneForfait(): float
    {
        return $this->getMontantTotalForfait();
    }


}
