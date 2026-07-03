<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\PageSection;
use App\Repository\PageSectionRepository;

final class PageSectionProvider
{
    public function __construct(
        private readonly PageSectionRepository $pageSectionRepository,
    ) {
    }

    public function get(string $key, string $defaultTitle = '', string $defaultSubtitle = '', string $defaultBody = ''): PageSection
    {
        $section = $this->pageSectionRepository->findOneByKey($key);
        if ($section) {
            return $section;
        }

        return (new PageSection())
            ->setSectionKey($key)
            ->setTitle($defaultTitle)
            ->setSubtitle($defaultSubtitle)
            ->setBody($defaultBody);
    }
}
