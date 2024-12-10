<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Exception\EntityNotFoundException;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Src\DTO\DesignationDto;
use App\Src\Entities\DegreeType;
use App\Src\Entities\Designation;
use App\Src\Repositories\DegreeTypeRepository;
use App\Src\Repositories\DesignationRepository;
use App\Src\Enum\DegreeType as EnumDegreeType;
use InvalidArgumentException;
use function array_map;

/**
 * Class Designation
 *
 * @package App\Src\Services
 */
final class DesignationService extends Service
{
    /**
     * Designation repository
     *
     * @var DesignationRepository
     */
    private DesignationRepository $designationRepository;

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
        $this->designationRepository = $entityManager->getRepository(Designation::class);
        $this->degreeTypeRepository = $entityManager->getRepository(DegreeType::class);
    }

    /**
     * Update an existing designation
     *
     * @param int $id
     * @param array<string, int|string|string[]> $data
     * @throws ForbiddenException
     * @return void
     */
    public function update(int $id, array $data): void
    {
        $designation = $this->findById($id);

        if ($designation->getIsDeleted()) {
            throw new ForbiddenException('You cannot update designation as it is deleted');
        }

        if (isset($data['JDDescription'])) {
            $designation->setJobDescription(is_string($data['JDDescription']) ? $data['JDDescription'] : null);
        }

        if (isset($data['jobRoles'])) {
            $designation->setJobRoles(is_string($data['jobRoles']) ? $data['jobRoles'] : null);
        }

        if (isset($data['jobResponsibilities'])) {
            $designation->setJobResponsibilities(is_string($data['jobResponsibilities']) ? $data['jobResponsibilities'] : null);
        }

        if (isset($data['experience'])) {
            $designation->setExperience($data['experience'] ? (int)$data['experience'] : null);
        }

        if (isset($data['otherNotes'])) {
            $designation->setOtherNotes(is_string($data['otherNotes']) ? $data['otherNotes'] : null);
        }

        $this->addQualification($data, $designation);

        $designation->setModifiedUserName($this->ci->common_data['current_user']);
        $designation->setModifiedPC($this->ci->common_data['current_pc']);

        $this->commit($designation);

    }

    /**
     * Add qualification
     *
     * @param array<string, int|string|string[]> $data
     * @param Designation $designation
     * @return void
     *@throws InvalidArgumentException
     * @throws ValidationException
     */
    private function addQualification(array $data, Designation $designation): void
    {
        $designation->getProfessionalQualifications()->clear();
        if (isset($data['professionalQualifications']) && is_array($data['professionalQualifications'])) {
            foreach ($data['professionalQualifications'] as $professionalQualificationID) {
                try {
                    $degreeType = $this->degreeTypeRepository->findById((int)$professionalQualificationID);
                } catch (EntityNotFoundException $e) {
                    throw new ValidationException($e->getMessage());
                }

                if ($degreeType->getType() !== EnumDegreeType::PROFESSIONAL) {
                    throw new InvalidArgumentException('Invalid Degree Type');
                }

                $designation->addProfessionalQualification($degreeType);
            }
        }

        $designation->getAcademicQualifications()->clear();
        if (isset($data['academicQualifications']) && is_array($data['academicQualifications'])) {
            foreach ($data['academicQualifications'] as $academicQualificationID) {
                try {
                    $degreeType = $this->degreeTypeRepository->findById((int)$academicQualificationID);
                } catch (EntityNotFoundException $e) {
                    throw new ValidationException($e->getMessage());
                }

                if ($degreeType->getType() !== EnumDegreeType::ACADEMIC) {
                    throw new InvalidArgumentException('Invalid Degree Type');
                }

                $designation->addAcademicQualification($degreeType);
            }
        }

        $designation->getTechnicalQualifications()->clear();
        if (isset($data['technicalQualifications']) && is_array($data['technicalQualifications'])) {
            foreach ($data['technicalQualifications'] as $technicalQualificationID) {
                try {
                    $degreeType = $this->degreeTypeRepository->findById((int)$technicalQualificationID);
                } catch (EntityNotFoundException $e) {
                    throw new ValidationException($e->getMessage());
                }

                if ($degreeType->getType() !== EnumDegreeType::TECHNICAL) {
                    throw new InvalidArgumentException('Invalid Degree Type');
                }

                $designation->addTechnicalQualification($degreeType);
            }
        }
    }

    /**
     * Find a Designation by ID
     *
     * @param int $id
     * @throws NotFoundException
     * @return Designation
     */
    public function findById(int $id): Designation
    {
        try {
            return $this->designationRepository->findById($id);
        } catch (EntityNotFoundException $e){
            throw new NotFoundException($e->getMessage());
        }
    }

    /**
     * Delete
     *
     * @param int $id
     * @throws ForbiddenException
     * @return void
     */
    public function delete(int $id): void
    {
        $designation = $this->findById($id);

        if ($designation->getIsDeleted()) {
            throw new ForbiddenException('You cannot delete designation as it is deleted');
        }

        $designation->setIsDeleted(1);
        $designation->setModifiedUserName($this->ci->common_data['current_user']);
        $designation->setModifiedPC($this->ci->common_data['current_pc']);

        $this->commit($designation);
    }

    /**
     * Commit
     *
     * @param Designation $designation
     * @return void
     */
    private function commit(Designation $designation): void
    {
        $this->designationRepository->persist($designation);
    }

    /**
     * DTO
     *
     * @param Designation $designation
     * @return DesignationDto
     */
    public function entityToDTO(Designation $designation): DesignationDto
    {
        return new DesignationDto(
            $designation->getId(),
            html_entity_decode((string)$designation->getJobDescription()),
            html_entity_decode((string)$designation->getJobResponsibilities()),
            $designation->getExperience(),
            html_entity_decode((string)$designation->getJobRoles()),
            $designation->getOtherNotes(),
            array_map(function ($detail) {
                return (string)$detail->getId();
            }, $designation->getProfessionalQualifications()->toArray()),
            array_map(function ($detail) {
                return (string)$detail->getId();
            }, $designation->getAcademicQualifications()->toArray()),
            array_map(function ($detail) {
                return (string)$detail->getId();
            }, $designation->getTechnicalQualifications()->toArray())
        );

    }

}