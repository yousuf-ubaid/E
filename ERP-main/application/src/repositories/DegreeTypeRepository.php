<?php

declare(strict_types=1);

namespace App\Src\Repositories;

use App\Exception\EntityNotFoundException;
use App\Src\Entities\DegreeType;
use App\Src\Enum\DegreeType as EnumDegreeType;
use Doctrine\ORM\EntityRepository;

/**
 * Repository for degreeType
 *
 * @extends EntityRepository<DegreeType>
 * @package App\Src\Repositories
 */
final class DegreeTypeRepository extends EntityRepository
{
    /**
     * Find degree type by ID
     *
     * @param int $id
     * @throws EntityNotFoundException
     * @return DegreeType
     */
    public function findById(int $id): DegreeType
    {
        $degreeType = $this->find($id);
        if(!$degreeType instanceof DegreeType){
            throw new EntityNotFoundException('Degree type not found');
        }
        return $degreeType;
    }

    /**
     * Persist
     *
     * @param DegreeType $degreeType
     * @return void
     */
    public function persist(DegreeType $degreeType): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($degreeType);
        $entityManager->flush();
    }

    /**
     * Find a degree by type
     *
     * @param EnumDegreeType $type
     * @return array<int, array<string, mixed>>
     */
    public function getByType(EnumDegreeType $type): array
    {
        return $this->createQueryBuilder('degreeType')
            ->andWhere('degreeType.type = :type')
            ->setParameter('type', $type->value)
            ->orderBy('degreeType.id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Find a degree by name
     *
     * @param string $description
     * @param EnumDegreeType $type
     * @return DegreeType|null
     */
    public function getByDescription(string $description, EnumDegreeType $type): ?DegreeType
    {
        return $this->findOneBy([
            'type'        => $type->value,
            'description' => $description
        ]);
    }

}

