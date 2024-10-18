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

    #[ORM\Column(length: 255)]
    private ?string $mois = null;

    #[ORM\Column]
    private ?int $nbJustificatifs = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantValid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateModif = null;

    #[ORM\ManyToOne(inversedBy: 'fichefrais')]
    #[ORM\JoinColumn(nullable: false)]

    /**
     * @var Collection<int, LigneFraisForfait>
     */
    #[ORM\OneToMany(targetEntity: LigneFraisForfait::class, mappedBy: 'fichefrais')]
    private Collection $lignefraisforfait;

    /**
     * @var Collection<int, LigneFraisHorsForfait>
     */
    #[ORM\OneToMany(targetEntity: LigneFraisHorsForfait::class, mappedBy: 'fichefrais')]
    private Collection $lignefraishorsforfait;

    #[ORM\ManyToOne(inversedBy: 'fichefrais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $etat = null;

    public function __construct()
    {
        $this->lignefraisforfait = new ArrayCollection();
        $this->lignefraishorsforfait = new ArrayCollection();
    }

    #[ORM\Column]
    private ?int $oldID = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getMois(): ?string
    {
        return $this->mois;
    }

    public function setMois(string $mois): static
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

    public function getMontantValid(): ?string
    {
        return $this->montantValid;
    }

    public function setMontantValid(string $montantValid): static
    {
        $this->montantValid = $montantValid;

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

    /**
     * @return Collection<int, LigneFraisForfait>
     */
    public function getLignefraisforfait(): Collection
    {
        return $this->lignefraisforfait;
    }

    public function addLignefraisforfait(LigneFraisForfait $lignefraisforfait): static
    {
        if (!$this->lignefraisforfait->contains($lignefraisforfait)) {
            $this->lignefraisforfait->add($lignefraisforfait);
            $lignefraisforfait->setFichefrais($this);
        }

        return $this;
    }

    public function removeLignefraisforfait(LigneFraisForfait $lignefraisforfait): static
    {
        if ($this->lignefraisforfait->removeElement($lignefraisforfait)) {
            // set the owning side to null (unless already changed)
            if ($lignefraisforfait->getFichefrais() === $this) {
                $lignefraisforfait->setFichefrais(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LigneFraisHorsForfait>
     */
    public function getLignefraishorsforfait(): Collection
    {
        return $this->lignefraishorsforfait;
    }

    public function addLignefraishorsforfait(LigneFraisHorsForfait $lignefraishorsforfait): static
    {
        if (!$this->lignefraishorsforfait->contains($lignefraishorsforfait)) {
            $this->lignefraishorsforfait->add($lignefraishorsforfait);
            $lignefraishorsforfait->setFichefrais($this);
        }

        return $this;
    }

    public function removeLignefraishorsforfait(LigneFraisHorsForfait $lignefraishorsforfait): static
    {
        if ($this->lignefraishorsforfait->removeElement($lignefraishorsforfait)) {
            // set the owning side to null (unless already changed)
            if ($lignefraishorsforfait->getFichefrais() === $this) {
                $lignefraishorsforfait->setFichefrais(null);
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
    public function getOldID(): ?int
    {
        return $this->oldID;
    }

    public function setOldID(int $oldID): static
    {
        $this->oldID = $oldID;

        return $this;
    }
}
