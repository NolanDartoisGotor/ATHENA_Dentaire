<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OpeningHour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<OpeningHour> */
class OpeningHourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningHour::class);
    }

    /** @return list<OpeningHour> */
    public function findAllOrdered(): array
    {
        return $this->findBy([], ['position' => 'ASC']);
    }
}
