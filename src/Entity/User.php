<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $cp = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateEmbauche = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FicheFrais::class, orphanRemoval: true, fetch: 'EAGER')]
    private Collection $ficheFrais;

    #[ORM\Column(length: 255)]
    private ?string $oldId = null;

    // 2FA properties
    #[ORM\Column(type: 'boolean')]
    private bool $isTotpAuthenticationEnabled = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $totpSecret = null;

    public function __construct()
    {
        $this->ficheFrais = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Clear sensitive data if needed
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): static
    {
        $this->cp = $cp;

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

    public function getDateEmbauche(): ?\DateTimeInterface
    {
        return $this->dateEmbauche;
    }

    public function setDateEmbauche(\DateTimeInterface $dateEmbauche): static
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    public function getFicheFrais(): Collection
    {
        return $this->ficheFrais;
    }

    public function addFicheFrai(FicheFrais $ficheFrai): static
    {
        if (!$this->ficheFrais->contains($ficheFrai)) {
            $this->ficheFrais->add($ficheFrai);
            $ficheFrai->setUser($this);
        }

        return $this;
    }

    public function removeFicheFrai(FicheFrais $ficheFrai): static
    {
        if ($this->ficheFrais->removeElement($ficheFrai)) {
            if ($ficheFrai->getUser() === $this) {
                $ficheFrai->setUser(null);
            }
        }

        return $this;
    }

    public function getOldId(): ?string
    {
        return $this->oldId;
    }

    public function setOldId(string $oldId): static
    {
        $this->oldId = $oldId;

        return $this;
    }

    public function getFicheFraisByMonth($month): ?FicheFrais
    {
        foreach ($this->ficheFrais as $ficheFrais) {
            if ($ficheFrais->getMois() == $month) {
                return $ficheFrais;
            }
        }
        return null;
    }

    // 2FA methods

    public function isTotpAuthenticationEnabled(): bool
    {
        return $this->isTotpAuthenticationEnabled;
    }

    public function getTotpSecret(): ?string
    {
        return $this->totpSecret;
    }

    public function setTotpSecret(?string $totpSecret): static
    {
        $this->totpSecret = $totpSecret;
        return $this;
    }

    public function enableTotpAuthentication(): void
    {
        $this->isTotpAuthenticationEnabled = true;
    }

    public function disableTotpAuthentication(): void
    {
        $this->isTotpAuthenticationEnabled = false;
    }

    // Obligations de l'interface TwoFactorInterface (Google Authenticator)

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return $this->isTotpAuthenticationEnabled;
    }

    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->email ?? '';
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->totpSecret;
    }
}
