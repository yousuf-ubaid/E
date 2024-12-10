<?php

declare(strict_types=1);

namespace App\Src\Repositories;

use App\Exception\EntityNotFoundException;
use App\Src\Entities\Designation;
use Doctrine\ORM\EntityRepository;

/**
 * Repository for designation
 *
 * @extends EntityRepository<Designation>
 * @package App\Src\Repositories
 */
final class DesignationRepository extends EntityRepository
{
    /**
     * Find designation by ID
     *
     * @param int $id
     * @throws EntityNotFoundException
     * @return Designation
     */
    public function findById(int $id): Designation
    {
        $designation = $this->find($id);
        if (!$designation instanceof Designation) {
            throw new EntityNotFoundException('Designation not found');
        }
        return $designation;
    }

    /**
     * Persist
     *
     * @param Designation $designation
     * @return void
     */
    public function persist(Designation $designation): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($designation);
        $entityManager->flush();
    }

}

