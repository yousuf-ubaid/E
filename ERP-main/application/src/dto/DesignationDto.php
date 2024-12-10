<?php

declare(strict_types=1);

namespace App\Src\DTO;

/**
 * Data Transfer Object for Designation.
 * Represents the details of a designation operation.
 *
 * @package App\Src\DTO
 */
final readonly class DesignationDto
{
    /**
     * Constructor for DesignationDto.
     *
     * @param int $id
     * @param string|null $jobDescription
     * @param string|null $jobResponsibilities
     * @param int|null $experience
     * @param string|null $jobRoles
     * @param string|null $otherNotes
     * @param array|null $professionalQualifications
     * @param array|null $academicQualifications
     * @param array|null $technicalQualifications
     */
    public function __construct(
        public int     $id,
        public ?string $jobDescription,
        public ?string $jobResponsibilities,
        public ?int    $experience,
        public ?string $jobRoles,
        public ?string $otherNotes,
        public ?array  $professionalQualifications = [],
        public ?array  $academicQualifications = [],
        public ?array  $technicalQualifications = []
    ) {

    }

}
