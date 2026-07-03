<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SiteSettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SiteSettingsRepository::class)]
class SiteSettings
{
    #[ORM\Id]
    #[ORM\Column]
    private int $id = 1;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $organizationName = 'Cabinet Athéna Dentaire';

    #[ORM\Column(length: 255)]
    private string $tagline = 'Votre santé bucco-dentaire, notre priorité.';

    #[ORM\Column(length: 255)]
    private string $address = '1, rue Jean Addams 14280 Saint-Contest';

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $phone = '02 31 85 22 40';

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $emergencyPhone = '02 31 86 13 72';

    #[ORM\Column(length: 180)]
    #[Assert\Email]
    private string $contactEmail = 'contact@athena-dentaire.fr';

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email]
    private ?string $formRecipientEmail = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $googleMapsEmbedUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heroImage = 'images/hero.jpg';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mapImage = 'images/map.jpg';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accessPlanImage = 'images/access-plan.jpg';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $assistantsList = 'Céline • Sabrina • Christine • Céline • Océane • Maryline • Chloé';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contactFormNotice = 'Aucune prise de rendez-vous ou annulation ne sera traitée via ce formulaire. Merci de contacter le secrétariat par téléphone.';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $footerText = '© 2024 Cabinet Athéna Dentaire - Mentions Légales - Politique de Confidentialité - Site réalisé par GWM Agency';

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }

    public function setOrganizationName(string $organizationName): static
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    public function getTagline(): string
    {
        return $this->tagline;
    }

    public function setTagline(string $tagline): static
    {
        $this->tagline = $tagline;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmergencyPhone(): ?string
    {
        return $this->emergencyPhone;
    }

    public function setEmergencyPhone(?string $emergencyPhone): static
    {
        $this->emergencyPhone = $emergencyPhone;

        return $this;
    }

    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getFormRecipientEmail(): ?string
    {
        return $this->formRecipientEmail;
    }

    public function setFormRecipientEmail(?string $formRecipientEmail): static
    {
        $this->formRecipientEmail = $formRecipientEmail;

        return $this;
    }

    public function getRecipientEmail(): string
    {
        return $this->formRecipientEmail ?: $this->contactEmail;
    }

    public function getGoogleMapsEmbedUrl(): ?string
    {
        return $this->googleMapsEmbedUrl;
    }

    public function setGoogleMapsEmbedUrl(?string $googleMapsEmbedUrl): static
    {
        $this->googleMapsEmbedUrl = $googleMapsEmbedUrl;

        return $this;
    }

    public function getHeroImage(): ?string
    {
        return $this->heroImage;
    }

    public function setHeroImage(?string $heroImage): static
    {
        $this->heroImage = $heroImage;

        return $this;
    }

    public function getMapImage(): ?string
    {
        return $this->mapImage;
    }

    public function setMapImage(?string $mapImage): static
    {
        $this->mapImage = $mapImage;

        return $this;
    }

    public function getAccessPlanImage(): ?string
    {
        return $this->accessPlanImage;
    }

    public function setAccessPlanImage(?string $accessPlanImage): static
    {
        $this->accessPlanImage = $accessPlanImage;

        return $this;
    }

    public function getAssistantsList(): ?string
    {
        return $this->assistantsList;
    }

    public function setAssistantsList(?string $assistantsList): static
    {
        $this->assistantsList = $assistantsList;

        return $this;
    }

    public function getContactFormNotice(): ?string
    {
        return $this->contactFormNotice;
    }

    public function setContactFormNotice(?string $contactFormNotice): static
    {
        $this->contactFormNotice = $contactFormNotice;

        return $this;
    }

    public function getFooterText(): ?string
    {
        return $this->footerText;
    }

    public function setFooterText(?string $footerText): static
    {
        $this->footerText = $footerText;

        return $this;
    }
}
