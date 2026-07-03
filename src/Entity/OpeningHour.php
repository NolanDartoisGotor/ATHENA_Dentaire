<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OpeningHourRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OpeningHourRepository::class)]
class OpeningHour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    private ?string $dayLabel = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    private ?string $hours = null;

    #[ORM\Column]
    private int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayLabel(): ?string
    {
        return $this->dayLabel;
    }

    public function setDayLabel(string $dayLabel): static
    {
        $this->dayLabel = $dayLabel;

        return $this;
    }

    public function getHours(): ?string
    {
        return $this->hours;
    }

    public function setHours(string $hours): static
    {
        $this->hours = $hours;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}
