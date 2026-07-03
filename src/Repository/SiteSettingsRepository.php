<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SiteSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<SiteSettings> */
class SiteSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteSettings::class);
    }

    public function getSettings(): SiteSettings
    {
        $settings = $this->find(1);
        if (!$settings) {
            $settings = new SiteSettings();
            $this->getEntityManager()->persist($settings);
            $this->getEntityManager()->flush();
        }

        return $settings;
    }
}
