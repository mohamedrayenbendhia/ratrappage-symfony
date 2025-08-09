<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[\Symfony\Component\Validator\Constraints\NotBlank(message: 'Email cannot be blank.')]
    #[\Symfony\Component\Validator\Constraints\Email(message: "The email address '{{ value }}' is not valid.")]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];


    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 20)]
    #[\Symfony\Component\Validator\Constraints\Length(min: 3, max: 20, minMessage: 'Name must be at least {{ limit }} characters', maxMessage: 'Name cannot be longer than {{ limit }} characters')]
    #[\Symfony\Component\Validator\Constraints\NotBlank(message: 'Name is required')]
    private $name;

    #[ORM\Column(type: 'string', length: 8)]
    #[\Symfony\Component\Validator\Constraints\NotBlank(message: 'Phone number cannot be blank.')]
    #[\Symfony\Component\Validator\Constraints\Regex(pattern: '/^\d{8}$/', message: 'Phone number must be exactly 8 digits.')]
    private $phoneNumber;


    #[ORM\Column(type: 'boolean')]
    private $isBlocked = false;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastConnexion;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    // NOTE: We will treat authCode as a transient value (not persisted in the DB)
    private ?string $authCode = null; // Do not store this in the database

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
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
        
        // Si l'utilisateur n'a aucun rôle spécifique, lui donner ROLE_USER par défaut
        if (empty($roles) || (count($roles) === 1 && in_array('ROLE_USER', $roles))) {
            $roles[] = 'ROLE_USER';
        }
        
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }


    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function isEmailAuthEnabled(): bool
    {
        return true; // This can be a persisted field to switch email code authentication on/off
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    /**
     * Get the email authentication code (for 2FA).
     * Throws an exception if the authCode is not set.
     *
     * @throws \LogicException if the auth code has not been set.
     */
    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    /**
     * Set the email authentication code (for 2FA).
     * This should only be set when generating a new code for 2FA.
     */
    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastConnexion(): ?\DateTime
    {
        return $this->lastConnexion;
    }

    public function setLastConnexion(?\DateTime $lastConnexion): self
    {
        $this->lastConnexion = $lastConnexion;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }
}
