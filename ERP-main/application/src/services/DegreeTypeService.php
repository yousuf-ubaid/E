<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Exception\EntityNotFoundException;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Src\Entities\DegreeType;
use App\Src\Enum\DegreeType as EnumDegreeType;
use App\Src\Repositories\DegreeTypeRepository;
use InvalidArgumentException;

/**
 * Class DegreeType
 *
 * @package App\Src\Services
 */
final class DegreeTypeService extends Service
{
    /**
     * Degree type repository
     *
     * @var DegreeTypeRepository
     */
    private DegreeTypeRepository $degreeTypeRepository;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->ci->load->library('doctrine');

        $entityManager = $this->ci->doctrine->getEntityManager();
        $this->degreeTypeRepository = $entityManager->getRepository(DegreeType::class);
    }

    /**
     * Create degree type
     *
     * @param array<string, int|string> $data
     * @throws ForbiddenException
     * @throws ValidationException
     * @return void
     */
    public function create(array $data): void
    {
        $type = EnumDegreeType::tryFrom($data['type']);

        if (null === $type) {
            throw new InvalidArgumentException('Invalid degree type');
        }

        $exist = $this->degreeTypeRepository->getByDescription((string)$data['description'], $type);
        if ($exist) {
            throw new ValidationException('Already exist');
        }

        $degreeType = new DegreeType();
        $degreeType->setDescription((string)$data['description']);
        $degreeType->setType($type);

        $this->commit($degreeType);
    }

    /**
     * Update an existing degree type
     *
     * @param int $id
     * @param array<string, int|string> $data
     * @throws InvalidArgumentException
     * @return void
     */
    public function update(int $id, array $data): void
    {
        $type = EnumDegreeType::tryFrom($data['type']);

        if (null === $type) {
            throw new InvalidArgumentException('Invalid degree type');
        }

        $degreeType = $this->findById($id);
        $degreeType->setDescription((string)$data['description']);
        $degreeType->setType($type);

        $this->commit($degreeType);
    }

    /**
     * Find a degree by type
     *
     * @param int $id
     * @throws NotFoundException
     * @return DegreeType
     */
    public function findById(int $id): DegreeType
    {
        try {
            return $this->degreeTypeRepository->findById($id);
        } catch (EntityNotFoundException $e){
            throw new NotFoundException($e->getMessage());
        }
    }

    /**
     * Find a degree by type
     *
     * @param string $type
     * @throws InvalidArgumentException
     * @return array<int, array<string, mixed>>
     */
    public function getByType(string $type): array
    {
        $type = EnumDegreeType::tryFrom($type);

        if (null === $type) {
            throw new InvalidArgumentException('Invalid degree type');
        }

        return $this->degreeTypeRepository->getByType($type);
    }

    /**
     * Commit
     *
     * @param DegreeType $degreeType
     * @return void
     */
    private function commit(DegreeType $degreeType): void
    {
        $this->degreeTypeRepository->persist($degreeType);
    }

}