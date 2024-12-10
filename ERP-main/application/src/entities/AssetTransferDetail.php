<?php

declare(strict_types=1);

namespace App\Src\Entities;

use App\Src\Interface\AuditableEntityInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * Entity representing the details of an asset transfer.
 *
 * @package App\Src\Entities
 */
#[Entity]
#[Table(name: "srp_erp_fa_asset_transfer_detail")]
#[HasLifecycleCallbacks]
class AssetTransferDetail extends AuditableEntity implements AuditableEntityInterface
{
    /**
     * Unique identifier for the asset transfer detail record.
     */
    #[Id]
    #[Column(type: "integer")]
    #[GeneratedValue]
    private int $id;

    /**
     * ID of the associated asset transfer (foreign key).
     */
    #[ManyToOne(targetEntity: "App\Src\Entities\AssetTransfer")]
    #[JoinColumn(name: "transferID", referencedColumnName: "id", nullable: false)]
    private ?AssetTransfer $assetTransfer = null;

    /**
     * One-to-One relationship to the AssetMaster entity (Fixed Asset).
     */
    #[OneToOne(targetEntity: "App\Src\Entities\Asset")]
    #[JoinColumn(name: "faID", referencedColumnName: "faID", nullable: false)]
    private ?Asset $asset = null;

    /**
     * Description of the fixed asset being transferred.
     */
    #[Column(type: "string", length: 255, nullable: true)]
    private ?string $faDescription = null;

    /**
     * Comments or notes related to the asset transfer detail.
     */
    #[Column(type: "text", nullable: true)]
    private ?string $comment = null;

    /**
     * Sets the associated asset transfer for this detail.
     *
     * @param AssetTransfer $assetTransfer The asset transfer entity.
     * @return self
     */
    public function setAssetTransfer(AssetTransfer $assetTransfer): self
    {
        $this->assetTransfer = $assetTransfer;
        return $this;
    }

    /**
     * Gets the associated asset transfer for this detail.
     *
     * @return AssetTransfer|null The asset transfer entity.
     */
    public function getAssetTransfer(): ?AssetTransfer
    {
        return $this->assetTransfer;
    }

    /**
     * Sets the associated asset for this detail.
     *
     * @param Asset|null $asset
     * @return self
     */
    public function setAsset(?Asset $asset): self
    {
        $this->asset = $asset;
        return $this;
    }

    /**
     * Gets the associated asset for this detail.
     *
     * @return Asset|null
     */
    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    /**
     * Sets the description of the fixed asset being transferred.
     *
     * @param string|null $faDescription The asset description.
     * @return self
     */
    public function setFaDescription(?string $faDescription): self
    {
        $this->faDescription = $faDescription;
        return $this;
    }

    /**
     * Gets the description of the fixed asset being transferred.
     *
     * @return string|null The asset description.
     */
    public function getFaDescription(): ?string
    {
        return $this->faDescription;
    }

    /**
     * Sets the comment or notes related to the asset transfer.
     *
     * @param string|null $comment The comment.
     * @return self
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Gets the comment or notes related to the asset transfer.
     *
     * @return string|null The comment or notes.
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

}
