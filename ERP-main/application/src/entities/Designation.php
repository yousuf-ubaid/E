<?php

declare(strict_types=1);

namespace App\Src\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Designation
 *
 * @package App\Src\Entities
 */
#[Entity(repositoryClass: "App\Src\Repositories\DesignationRepository")]
#[Table(name: "srp_designation")]
class Designation
{
    /**
     * Primary key for the designation.
     *
     * @var int
     */
    #[Id]
    #[Column(name: "DesignationID", type: "integer")]
    #[GeneratedValue]
    private int $id;

    /**
     * Description of the designation.
     *
     * @var string|null
     */
    #[Column(name: "DesDescription", type: "string", length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * Company ID associated with the designation.
     *
     * @var int|null
     */
    #[Column(name: "Erp_companyID", type: "integer", nullable: true)]
    private ?int $companyId = null;

    /**
     * Soft delete flag (0 = not deleted, 1 = deleted).
     *
     * @var int
     */
    #[Column(name: "isDeleted", type: "integer", options: ["default" => 0])]
    private int $isDeleted = 0;

    /**
     * Job description for the designation.
     *
     * @var string|null
     */
    #[Column(name: "JDDescription", type: "text", nullable: true)]
    private ?string $jobDescription = null;

    /**
     * Job code for the designation.
     *
     * @var string|null
     */
    #[Column(name: "JobCode", type: "string", length: 255, nullable: true)]
    private ?string $jobCode = null;

    /**
     * Roles associated with the job.
     *
     * @var string|null
     */
    #[Column(name: "jobRoles", type: "text", nullable: true)]
    private ?string $jobRoles = null;

    /**
     * Responsibilities of the job.
     *
     * @var string|null
     */
    #[Column(name: "jobResponsibilities", type: "text", nullable: true)]
    private ?string $jobResponsibilities = null;

    /**
     * Experience level required for the job.
     *
     * @var int|null
     */
    #[Column(name: "experience", type: "integer", nullable: true)]
    private ?int $experience = null;

    /**
     * Additional notes for the designation.
     *
     * @var string|null
     */
    #[Column(name: "otherNotes", type: "text", nullable: true)]
    private ?string $otherNotes = null;

    /**
     * Professional qualifications associated with the designation.
     *
     * @var Collection<int, DegreeType>
     */
    #[JoinTable(name: 'srp_erp_designationprofessionalqualification')]
    #[JoinColumn(name: 'designationID', referencedColumnName: 'DesignationID')]
    #[InverseJoinColumn(name: 'degreeTypeID', referencedColumnName: 'degreeTypeID')]
    #[ManyToMany(targetEntity: DegreeType::class, cascade: ['persist', 'remove'])]
    private Collection $professionalQualifications;

    /**
     * Academic qualifications associated with the designation.
     *
     * @var Collection<int, DegreeType>
     */
    #[JoinTable(name: 'srp_erp_designationacademicqualification')]
    #[JoinColumn(name: 'designationID', referencedColumnName: 'DesignationID')]
    #[InverseJoinColumn(name: 'degreeTypeID', referencedColumnName: 'degreeTypeID')]
    #[ManyToMany(targetEntity: DegreeType::class, cascade: ['persist', 'remove'])]
    private Collection $academicQualifications;

    /**
     * Technical qualifications associated with the designation.
     *
     * @var Collection<int, DegreeType>
     */
    #[JoinTable(name: 'srp_erp_designationtechnicalqualification')]
    #[JoinColumn(name: 'designationID', referencedColumnName: 'DesignationID')]
    #[InverseJoinColumn(name: 'degreeTypeID', referencedColumnName: 'degreeTypeID')]
    #[ManyToMany(targetEntity: DegreeType::class, cascade: ['persist', 'remove'])]
    private Collection $technicalQualifications;

    /**
     * Modified user name for the designation.
     *
     * @var string|null
     */
    #[Column(name: "ModifiedUserName", type: "string", length: 255, nullable: true)]
    private ?string $ModifiedUserName = null;

    /**
     * Modified PC for the designation.
     *
     * @var string|null
     */
    #[Column(name: "ModifiedPC", type: "string", length: 255, nullable: true)]
    private ?string $ModifiedPC = null;


    /**
     * Construct
     */
    public function __construct()
    {
        $this->professionalQualifications = new ArrayCollection();
        $this->academicQualifications = new ArrayCollection();
        $this->technicalQualifications = new ArrayCollection();
    }

    /**
     * Get the ID of the designation.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the description of the designation.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of the designation.
     *
     * @param string|null $description
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the company ID associated with the designation.
     *
     * @return int|null
     */
    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    /**
     * Set the company ID associated with the designation.
     *
     * @param int|null $companyId
     * @return self
     */
    public function setCompanyId(?int $companyId): self
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * Get the soft delete status of the designation.
     *
     * @return int
     */
    public function getIsDeleted(): int
    {
        return $this->isDeleted;
    }

    /**
     * Set the soft delete status of the designation.
     *
     * @param int $isDeleted
     * @return self
     */
    public function setIsDeleted(int $isDeleted): self
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    /**
     * Get the job description for the designation.
     *
     * @return string|null
     */
    public function getJobDescription(): ?string
    {
        return $this->jobDescription;
    }

    /**
     * Set the job description for the designation.
     *
     * @param string|null $jobDescription
     * @return self
     */
    public function setJobDescription(?string $jobDescription): self
    {
        $this->jobDescription = $jobDescription;
        return $this;
    }

    /**
     * Get the job code for the designation.
     *
     * @return string|null
     */
    public function getJobCode(): ?string
    {
        return $this->jobCode;
    }

    /**
     * Set the job code for the designation.
     *
     * @param string|null $jobCode
     * @return self
     */
    public function setJobCode(?string $jobCode): self
    {
        $this->jobCode = $jobCode;
        return $this;
    }

    /**
     * Get the job roles for the designation.
     *
     * @return string|null
     */
    public function getJobRoles(): ?string
    {
        return $this->jobRoles;
    }

    /**
     * Set the job roles for the designation.
     *
     * @param string|null $jobRoles
     * @return self
     */
    public function setJobRoles(?string $jobRoles): self
    {
        $this->jobRoles = $jobRoles;
        return $this;
    }

    /**
     * Get the job responsibilities for the designation.
     *
     * @return string|null
     */
    public function getJobResponsibilities(): ?string
    {
        return $this->jobResponsibilities;
    }

    /**
     * Set the job responsibilities for the designation.
     *
     * @param string|null $jobResponsibilities
     * @return self
     */
    public function setJobResponsibilities(?string $jobResponsibilities): self
    {
        $this->jobResponsibilities = $jobResponsibilities;
        return $this;
    }

    /**
     * Get the experience level required for the job.
     *
     * @return int|null
     */
    public function getExperience(): ?int
    {
        return $this->experience;
    }

    /**
     * Set the experience level required for the job.
     *
     * @param int|null $experience
     * @return self
     */
    public function setExperience(?int $experience): self
    {
        $this->experience = $experience;
        return $this;
    }

    /**
     * Get additional notes for the designation.
     *
     * @return string|null
     */
    public function getOtherNotes(): ?string
    {
        return $this->otherNotes;
    }

    /**
     * Set additional notes for the designation.
     *
     * @param string|null $otherNotes
     * @return self
     */
    public function setOtherNotes(?string $otherNotes): self
    {
        $this->otherNotes = $otherNotes;
        return $this;
    }

    /**
     * Add a professional qualification to the designation.
     *
     * @param DegreeType $degreeType
     * @return self
     */
    public function addProfessionalQualification(DegreeType $degreeType): self
    {
        if (!$this->professionalQualifications->contains($degreeType)) {
            $this->professionalQualifications->add($degreeType);
        }

        return $this;
    }

    /**
     * Add an academic qualification to the designation.
     *
     * @param DegreeType $degreeType
     * @return self
     */
    public function addAcademicQualification(DegreeType $degreeType): self
    {
        if (!$this->academicQualifications->contains($degreeType)) {
            $this->academicQualifications->add($degreeType);
        }

        return $this;
    }

    /**
     * Add a technical qualification to the designation.
     *
     * @param DegreeType $degreeType
     * @return self
     */
    public function addTechnicalQualification(DegreeType $degreeType): self
    {
        if (!$this->technicalQualifications->contains($degreeType)) {
            $this->technicalQualifications->add($degreeType);
        }

        return $this;
    }

    /**
     * Remove a professional qualification from the designation.
     *
     * @param DegreeType $degreeType
     * @return self
     */
    public function removeProfessionalQualification(DegreeType $degreeType): self
    {
        $this->professionalQualifications->removeElement($degreeType);

        return $this;
    }

    /**
     * Remove an academic qualification from the designation.
     *
     * @param DegreeType $degreeType
     * @return self
     */
    public function removeAcademicQualification(DegreeType $degreeType): self
    {
        $this->academicQualifications->removeElement($degreeType);

        return $this;
    }

    /**
     * Remove a technical qualification from the designation.
     *
     * @param DegreeType $degreeType
     * @return self
     */
    public function removeTechnicalQualification(DegreeType $degreeType): self
    {
        $this->technicalQualifications->removeElement($degreeType);

        return $this;
    }

    /**
     * Get all professional qualifications.
     *
     * @return Collection<int, DegreeType>
     */
    public function getProfessionalQualifications(): Collection
    {
        return $this->professionalQualifications;
    }

    /**
     * Get all academic qualifications.
     *
     * @return Collection<int, DegreeType>
     */
    public function getAcademicQualifications(): Collection
    {
        return $this->academicQualifications;
    }

    /**
     * Get all technical qualifications.
     *
     * @return Collection<int, DegreeType>
     */
    public function getTechnicalQualifications(): Collection
    {
        return $this->technicalQualifications;
    }

    /**
     * Get the ModifiedUserName.
     *
     * @return string|null
     */
    public function getModifiedUserName(): ?string
    {
        return $this->ModifiedUserName;
    }

    /**
     * Set the ModifiedUserName.
     *
     * @param string|null $ModifiedUserName
     * @return self
     */
    public function setModifiedUserName(?string $ModifiedUserName): self
    {
        $this->ModifiedUserName = $ModifiedUserName;
        return $this;
    }

    /**
     * Get the ModifiedPC.
     *
     * @return string|null
     */
    public function getModifiedPC(): ?string
    {
        return $this->ModifiedPC;
    }

    /**
     * Set the ModifiedPC.
     *
     * @param string|null $ModifiedPC
     * @return self
     */
    public function setModifiedPC(?string $ModifiedPC): self
    {
        $this->ModifiedPC = $ModifiedPC;
        return $this;
    }


}
