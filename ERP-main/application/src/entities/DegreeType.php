<?php

declare(strict_types=1);

namespace App\Src\Entities;

use App\Src\Interface\AuditableEntityInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use App\Src\Enum\DegreeType as EnumDegreeType;

/**
 * Class DegreeType
 *
 * Represents a degree type entity in the system.
 */
#[Entity(repositoryClass: "App\Src\Repositories\DegreeTypeRepository")]
#[Table(name: "srp_erp_degreetype")]
class DegreeType extends AuditableEntity implements AuditableEntityInterface
{
    /**
     * Primary key for the degree type.
     *
     * @var int
     */
    #[Id]
    #[Column(name: "degreeTypeID", type: "integer")]
    #[GeneratedValue]
    private int $id;

    /**
     * Description of the degree type.
     *
     * @var string|null
     */
    #[Column(name: "degreeDescription", type: "string", length: 45, nullable: true)]
    private ?string $description = null;

    /**
     * Type of the degree.
     *
     * Options: academic, professional, technical.
     * Default: academic.
     *
     * @var EnumDegreeType
     */
    #[Column(name: "type", type: "string", length: 45, enumType: EnumDegreeType::class, options: ["default" => "academic"])]
    private EnumDegreeType $type;


    /**
     * Get the degree type ID.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the degree description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the degree description.
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
     * Get the degree type.
     *
     * @return EnumDegreeType
     */
    public function getType(): EnumDegreeType
    {
        return $this->type;
    }

    /**
     * Set the degree type.
     *
     * @param EnumDegreeType $type
     * @return self
     */
    public function setType(EnumDegreeType $type): self
    {
        $this->type = $type;
        return $this;
    }
}
