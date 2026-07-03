<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PageSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<PageSection> */
class PageSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageSection::class);
    }

    public function findOneByKey(string $key): ?PageSection
    {
        return $this->findOneBy(['sectionKey' => $key]);
    }

    /** @return array<string, PageSection> */
    public function findAllIndexedByKey(): array
    {
        $indexed = [];
        foreach ($this->findAll() as $section) {
            $indexed[(string) $section->getSectionKey()] = $section;
        }

        return $indexed;
    }
}
