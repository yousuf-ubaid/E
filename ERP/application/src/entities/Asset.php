<?php

declare(strict_types=1);

namespace App\Src\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Entity representing the asset
 *
 * @package App\Src\Entities
 */
#[Entity(repositoryClass: "App\Src\Repositories\AssetRepository")]
#[Table(name: "srp_erp_fa_asset_master")]
class Asset
{
    /**
     * Unique identifier for the asset record.
     */
    #[Id]
    #[Column(type: "integer")]
    #[GeneratedValue]
    private int $faID;

    /**
     * Fixed asset code.
     */
    #[Column(type: "string", length: 255, nullable: true)]
    private ?string $faCode = null;

    /**
     * Fixed asset description.
     */
    #[Column(type: "string", length: 255, nullable: true)]
    private ?string $assetDescription = null;

    /**
     * Current location
     */
    #[Column(type: "integer", nullable: true)]
    private ?int $currentLocation = null;

    /**
     * Get asset code
     *
     * @return string|null
     */
    public function getFaCode(): ?string
    {
        return $this->faCode;
    }

    /**
     * Get asset description
     *
     * @return string|null
     */
    public function getAssetDescription(): ?string
    {
        return $this->assetDescription;
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->faID;
    }

    /**
     * Get location
     *
     * @return int|null
     */
    public function getCurrentLocation(): ?int
    {
        return $this->currentLocation;
    }

    /**
     * Gets Id
     *
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->faID = $id;
        return $this;
    }

    /**
     * Set location
     *
     * @param int $currentLocationId
     * @return self
     */
    public function setCurrentLocation(int $currentLocationId): self
    {
        $this->currentLocation = $currentLocationId;
        return $this;
    }

}