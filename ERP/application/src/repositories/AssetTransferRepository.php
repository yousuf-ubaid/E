<?php

namespace App\Src\Repositories;

use App\Exception\EntityNotFoundException;
use App\Src\Entities\AssetTransferDetail;
use Doctrine\ORM\EntityRepository;
use App\Src\Entities\AssetTransfer;

/**
 * Repository for asset transfer
 *
 * @extends EntityRepository<AssetTransfer>
 * @package App\Src\Repositories
 */
final class AssetTransferRepository extends EntityRepository
{
    /**
     * Find AssetTransfer by ID
     *
     * @param int $id
     * @throws EntityNotFoundException
     * @return AssetTransfer
     */
    public function findById(int $id): AssetTransfer
    {
        $assetTransfer = $this->find($id);
        if(!$assetTransfer instanceof AssetTransfer){
            throw new EntityNotFoundException('Asset transfer not found');
        }
        return $assetTransfer;
    }

    /**
     * Persist
     *
     * @param AssetTransfer $assetTransfer
     * @return void
     */
    public function persist(AssetTransfer $assetTransfer): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($assetTransfer);
        $entityManager->flush();
    }

    /**
     * Check asset is existed in non confirmed document
     *
     * @param int $faId
     * @return bool
     */
    public function isFaIdExistInNotConfirmed(int $faId): bool
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $queryBuilder->select('count(assetTransferDetail.id)')
            ->from(AssetTransferDetail::class, 'assetTransferDetail')
            ->innerJoin('assetTransferDetail.assetTransfer', 'assetTransfer')
            ->innerJoin('assetTransferDetail.asset', 'asset')
            ->where('asset.faID = :faId')
            ->andWhere('assetTransfer.confirmedYN = :confirmedStatus')
            ->setParameter('faId', $faId)
            ->setParameter('confirmedStatus', 0);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult() > 0;
    }

}

