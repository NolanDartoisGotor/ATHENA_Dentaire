<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TechnicalFacility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<TechnicalFacility> */
class TechnicalFacilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TechnicalFacility::class);
    }

    /** @return list<TechnicalFacility> */
    public function findPublishedOrdered(): array
    {
        return $this->findBy(['published' => true], ['position' => 'ASC']);
    }
}
