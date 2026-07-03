<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PageSectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PageSectionRepository::class)]
class PageSection
{
    public const KEY_EMERGENCY = 'emergency';
    public const KEY_BEFORE_CONSULTATION = 'before_consultation';
    public const KEY_TECHNICAL_INTRO = 'technical_intro';
    public const KEY_SERVICES_INTRO = 'services_intro';
    public const KEY_TEAM_INTRO = 'team_intro';
    public const KEY_APPOINTMENT = 'appointment';
    public const KEY_LEGAL_NOTICE = 'legal_notice';
    public const KEY_PRIVACY_POLICY = 'privacy_policy';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, unique: true)]
    #[Assert\NotBlank]
    private ?string $sectionKey = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitle = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $body = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSectionKey(): ?string
    {
        return $this->sectionKey;
    }

    public function setSectionKey(string $sectionKey): static
    {
        $this->sectionKey = $sectionKey;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }
}
