<?php

declare(strict_types=1);

namespace App\Src\Repositories;

use App\Exception\EntityNotFoundException;
use App\Src\Entities\Alert;
use Doctrine\ORM\EntityRepository;

/**
 * Repository for alert
 *
 * @extends EntityRepository<Alert>
 * @package App\Src\Repositories
 */
final class AlertRepository extends EntityRepository
{
    /**
     * Find AssetTransfer by ID
     *
     * @param int $id
     * @throws EntityNotFoundException
     * @return Alert
     */
    public function findById(int $id): Alert
    {
        $asset = $this->find($id);
        if (!$asset instanceof Alert) {
            throw new EntityNotFoundException('Alert not found');
        }
        return $asset;
    }

    /**
     * Get not sent email
     *
     * @return array<int, Alert>
     */
    public function getNotSentEmail(): array
    {
        return $this->createQueryBuilder('alert')
            ->andWhere('alert.isEmailSend = :status')
            ->setParameter('status', 0)
            ->orderBy('alert.timeStamp', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Persist
     *
     * @param Alert $alert
     * @return void
     */
    public function persist(Alert $alert): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($alert);
        $entityManager->flush();
    }

}

