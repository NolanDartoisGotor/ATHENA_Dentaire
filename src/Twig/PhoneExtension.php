<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

final class PhoneExtension
{
    #[AsTwigFilter('tel_href')]
    public function telHref(?string $phone): string
    {
        if ($phone === null || $phone === '') {
            return '';
        }

        return preg_replace('/[^\d+]/', '', $phone) ?? '';
    }
}
