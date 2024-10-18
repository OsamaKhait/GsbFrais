<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, FicheFrais>
     */
    #[ORM\OneToMany(targetEntity: FicheFrais::class, mappedBy: 'etat')]
    private Collection $fichefrais;

    public function __construct()
    {
        $this->fichefrais = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, FicheFrais>
     */
    public function getFichefrais(): Collection
    {
        return $this->fichefrais;
    }

    public function addFichefrai(FicheFrais $fichefrai): static
    {
        if (!$this->fichefrais->contains($fichefrai)) {
            $this->fichefrais->add($fichefrai);
            $fichefrai->setEtat($this);
        }

        return $this;
    }

    public function removeFichefrai(FicheFrais $fichefrai): static
    {
        if ($this->fichefrais->removeElement($fichefrai)) {
            // set the owning side to null (unless already changed)
            if ($fichefrai->getEtat() === $this) {
                $fichefrai->setEtat(null);
            }
        }

        return $this;
    }
}
