<?php

namespace App\Src\Repositories;

use App\Exception\EntityNotFoundException;
use App\Src\Entities\Asset;
use Doctrine\ORM\EntityRepository;

/**
 * Repository for Asset
 *
 * @extends EntityRepository<Asset>
 * @package App\Src\Repositories
 */
final class AssetRepository extends EntityRepository
{
    /**
     * Find AssetTransfer by ID
     *
     * @param int $id
     * @throws EntityNotFoundException
     * @return Asset
     */
    public function findById(int $id): Asset
    {
        $asset = $this->find($id);
        if (!$asset instanceof Asset) {
            throw new EntityNotFoundException('Asset found');
        }
        return $asset;
    }

    /**
     * Persist
     *
     * @param Asset $asset
     * @return void
     */
    public function persist(Asset $asset): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($asset);
        $entityManager->flush();
    }

}

