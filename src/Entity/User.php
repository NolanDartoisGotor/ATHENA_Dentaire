<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_EDITOR = 'ROLE_EDITOR';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /** @var list<string> */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $fullName = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $emailAuthEnabled = true;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $authCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $authCodeExpiresAt = null;

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

    /** @return list<string> */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /** @param list<string> $roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
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
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function isEmailAuthEnabled(): bool
    {
        return $this->emailAuthEnabled || \in_array(self::ROLE_ADMIN, $this->roles, true);
    }

    public function setEmailAuthEnabled(bool $emailAuthEnabled): static
    {
        $this->emailAuthEnabled = $emailAuthEnabled;

        return $this;
    }

    public function getEmailAuthRecipient(): string
    {
        return (string) $this->email;
    }

    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException('Le code de vérification email n\'a pas été généré.');
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getAuthCodeExpiresAt(): ?\DateTimeImmutable
    {
        return $this->authCodeExpiresAt;
    }

    public function setAuthCodeExpiresAt(?\DateTimeImmutable $authCodeExpiresAt): static
    {
        $this->authCodeExpiresAt = $authCodeExpiresAt;

        return $this;
    }

    public function clearEmailAuthCode(): void
    {
        $this->authCode = null;
        $this->authCodeExpiresAt = null;
    }

    public function isEmailAuthCodeExpired(): bool
    {
        return null === $this->authCodeExpiresAt
            || $this->authCodeExpiresAt < new \DateTimeImmutable();
    }
}
