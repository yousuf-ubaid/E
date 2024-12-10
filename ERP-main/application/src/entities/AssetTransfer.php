<?php

declare(strict_types=1);

namespace App\Src\Entities;

use App\Src\Interface\AuditableEntityInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * Entity representing an asset transfer record.
 *
 * @package App\Src\Entities
 */
#[Entity(repositoryClass: "App\Src\Repositories\AssetTransferRepository")]
#[Table(name: "srp_erp_fa_asset_transfer")]
#[HasLifecycleCallbacks]
class AssetTransfer extends AuditableEntity implements AuditableEntityInterface
{
    /**
     * Primary key identifier.
     */
    #[Id]
    #[Column(type: "integer")]
    #[GeneratedValue]
    private int $id;

    /**
     * Document identifier for the asset transfer.
     */
    #[Column(type: "string", length: 45)]
    private string $documentID;

    /**
     * Code associated with the document.
     */
    #[Column(type: "string", length: 45, nullable: true)]
    private ?string $documentCode;

    /**
     * Date of the document.
     */
    #[Column(type: "date")]
    private \DateTime $documentDate;

    /**
     * ID of the location from which the asset is transferred.
     */
    #[Column(type: "integer")]
    private int $locationFromID;

    /**
     * ID of the location to which the asset is transferred.
     */
    #[Column(type: "integer")]
    private int $locationToId;

    /**
     * ID of the employee who requested the transfer.
     */
    #[Column(type: "integer")]
    private int $requestedEmpID;

    /**
     * ID of the employee who issued the transfer.
     */
    #[Column(type: "integer")]
    private int $issuedEmpID;

    /**
     * Additional notes or comments on the transfer.
     */
    #[Column(type: "text", nullable: true)]
    private ?string $narration = null;

    /**
     * Status of the transfer (e.g., draft or confirmed).
     */
    #[Column(type: "string", length: 10, nullable: false, options: ["default" => "draft"], columnDefinition: "ENUM('draft', 'confirmed')")]
    private string $status = 'draft';

    /**
     * ID of the associated company.
     */
    #[Column(type: "integer")]
    private int $companyID;

    /**
     * Indicates if the transfer record is deleted.
     */
    #[Column(type: "integer", options: ["default" => 0])]
    private int $isDeleted = 0;

    /**
     * Indicates if the transfer has been confirmed.
     */
    #[Column(type: "integer", options: ["default" => 0])]
    private int $confirmedYN = 0;

    /**
     * Name of the employee who confirmed the transfer.
     */
    #[Column(type: "string", length: 200, nullable: true)]
    private ?string $confirmedByName = null;

    /**
     * Employee ID of the person who confirmed the transfer.
     */
    #[Column(type: "string", length: 50, nullable: true)]
    private ?string $confirmedByEmpID = null;

    /**
     * Date when the transfer was confirmed.
     */
    #[Column(type: "datetime", nullable: true)]
    private ?\DateTime $confirmedDate = null;

    /**
     * Collection of associated transfer details.
     *
     * @var Collection<int, AssetTransferDetail>
     */
    #[OneToMany(targetEntity: "App\Src\Entities\AssetTransferDetail", mappedBy: "assetTransfer", cascade: ["persist", "remove"], fetch:"EAGER", orphanRemoval:true)]
    private Collection $assetTransferDetails;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->assetTransferDetails = new ArrayCollection();
    }

    /**
     * Sets the document ID.
     *
     * @param string $documentID The document ID.
     * @return self
     */
    public function setDocumentID(string $documentID): self
    {
        $this->documentID = $documentID;
        return $this;
    }

    /**
     * Gets the document ID.
     *
     * @return string The document ID.
     */
    public function getDocumentID(): string
    {
        return $this->documentID;
    }

    /**
     * Sets the document code.
     *
     * @param string|null $documentCode The document code.
     * @return self
     */
    public function setDocumentCode(?string $documentCode): self
    {
        $this->documentCode = $documentCode;
        return $this;
    }

    /**
     * Gets the document code.
     *
     * @return string|null The document code.
     */
    public function getDocumentCode(): ?string
    {
        return $this->documentCode;
    }

    /**
     * Sets the document date.
     *
     * @param DateTime $documentDate The document date.
     * @return self
     */
    public function setDocumentDate(DateTime $documentDate): self
    {
        $this->documentDate = $documentDate;
        return $this;
    }

    /**
     * Gets the document date.
     *
     * @return DateTime The document date.
     */
    public function getDocumentDate(): DateTime
    {
        return $this->documentDate;
    }

    /**
     * Sets the location from which the assets are being transferred.
     *
     * @param int $locationFromID The location from ID.
     * @return self
     */
    public function setLocationFromID(int $locationFromID): self
    {
        $this->locationFromID = $locationFromID;
        return $this;
    }

    /**
     * Gets the location from which the assets are being transferred.
     *
     * @return int The location from ID.
     */
    public function getLocationFromID(): int
    {
        return $this->locationFromID;
    }

    /**
     * Sets the location to which the assets are being transferred.
     *
     * @param int $locationToId The location to ID.
     * @return self
     */
    public function setLocationToId(int $locationToId): self
    {
        $this->locationToId = $locationToId;
        return $this;
    }

    /**
     * Gets the location to which the assets are being transferred.
     *
     * @return int The location to ID.
     */
    public function getLocationToId(): int
    {
        return $this->locationToId;
    }

    /**
     * Sets the employee ID requesting the asset transfer.
     *
     * @param int $requestedEmpID The employee ID requesting the transfer.
     * @return self
     */
    public function setRequestedEmpID(int $requestedEmpID): self
    {
        $this->requestedEmpID = $requestedEmpID;
        return $this;
    }

    /**
     * Gets the employee ID requesting the asset transfer.
     *
     * @return int The requested employee ID.
     */
    public function getRequestedEmpID(): int
    {
        return $this->requestedEmpID;
    }

    /**
     * Sets the employee ID who issued the asset transfer.
     *
     * @param int $issuedEmpID The employee ID who issued the transfer.
     * @return self
     */
    public function setIssuedEmpID(int $issuedEmpID): self
    {
        $this->issuedEmpID = $issuedEmpID;
        return $this;
    }

    /**
     * Gets the employee ID who issued the asset transfer.
     *
     * @return int The issued employee ID.
     */
    public function getIssuedEmpID(): int
    {
        return $this->issuedEmpID;
    }

    /**
     * Sets the narration or description for the asset transfer.
     *
     * @param string|null $narration The narration text.
     * @return self
     */
    public function setNarration(?string $narration): self
    {
        $this->narration = $narration;
        return $this;
    }

    /**
     * Gets the narration or description for the asset transfer.
     *
     * @return string|null The narration text.
     */
    public function getNarration(): ?string
    {
        return $this->narration;
    }

    /**
     * Sets the status of the asset transfer.
     *
     * @param string $status The status (e.g., 'draft' or 'confirmed').
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Gets the status of the asset transfer.
     *
     * @return string The status (e.g., 'draft' or 'confirmed').
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Sets the company ID related to the asset transfer.
     *
     * @param int $companyID The company ID.
     * @return self
     */
    public function setCompanyID(int $companyID): self
    {
        $this->companyID = $companyID;
        return $this;
    }

    /**
     * Gets the company ID related to the asset transfer.
     *
     * @return int The company ID.
     */
    public function getCompanyID(): int
    {
        return $this->companyID;
    }

    /**
     * Sets the delete flag for the asset transfer (soft delete).
     *
     * @param int $isDeleted The delete flag (0 or 1).
     * @return self
     */
    public function setIsDeleted(int $isDeleted): self
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    /**
     * Gets the delete flag for the asset transfer (soft delete).
     *
     * @return int The delete flag (0 or 1).
     */
    public function getIsDeleted(): int
    {
        return $this->isDeleted;
    }

    /**
     * Sets the confirmation flag for the asset transfer.
     *
     * @param int $confirmedYN The confirmation flag (0 or 1).
     * @return self
     */
    public function setConfirmedYN(int $confirmedYN): self
    {
        $this->confirmedYN = $confirmedYN;
        return $this;
    }

    /**
     * Gets the confirmation flag for the asset transfer.
     *
     * @return int The confirmation flag (0 or 1).
     */
    public function getConfirmedYN(): int
    {
        return $this->confirmedYN;
    }

    /**
     * Sets the name of the person who confirmed the asset transfer.
     *
     * @param string|null $confirmedByName The name of the person who confirmed.
     * @return self
     */
    public function setConfirmedByName(?string $confirmedByName): self
    {
        $this->confirmedByName = $confirmedByName;
        return $this;
    }

    /**
     * Gets the name of the person who confirmed the asset transfer.
     *
     * @return string|null The name of the person who confirmed.
     */
    public function getConfirmedByName(): ?string
    {
        return $this->confirmedByName;
    }

    /**
     * Sets the employee ID of the person who confirmed the asset transfer.
     *
     * @param string|null $confirmedByEmpID The employee ID of the confirmer.
     * @return self
     */
    public function setConfirmedByEmpID(?string $confirmedByEmpID): self
    {
        $this->confirmedByEmpID = $confirmedByEmpID;
        return $this;
    }

    /**
     * Gets the employee ID of the person who confirmed the asset transfer.
     *
     * @return string|null The employee ID of the confirmer.
     */
    public function getConfirmedByEmpID(): ?string
    {
        return $this->confirmedByEmpID;
    }

    /**
     * Sets the confirmation date and time of the asset transfer.
     *
     * @param DateTime|null $confirmedDate The confirmation date and time.
     * @return self
     */
    public function setConfirmedDate(?DateTime $confirmedDate): self
    {
        $this->confirmedDate = $confirmedDate;
        return $this;
    }

    /**
     * Gets the confirmation date and time of the asset transfer.
     *
     * @return DateTime|null The confirmation date and time.
     */
    public function getConfirmedDate(): ?DateTime
    {
        return $this->confirmedDate;
    }

    /**
     * Get details
     *
     * @return Collection<int, AssetTransferDetail>
     */
    public function getDetails(): Collection
    {
        return $this->assetTransferDetails;
    }

    /**
     * Gets the id
     *
     * @return int The confirmation date and time.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Adds an asset transfer detail to the asset transfer.
     *
     * @param AssetTransferDetail $assetTransferDetail The asset transfer detail entity.
     * @return self
     */
    public function addDetail(AssetTransferDetail $assetTransferDetail): self
    {
        if (!$this->assetTransferDetails->contains($assetTransferDetail)) {
            $assetTransferDetail->setAssetTransfer($this);
            $this->assetTransferDetails->add($assetTransferDetail);
        }
        return $this;
    }

    /**
     * Removes an asset transfer detail from the asset transfer.
     *
     * @param AssetTransferDetail $assetTransferDetail The asset transfer detail entity.
     * @return self
     */
    public function removeDetail(AssetTransferDetail $assetTransferDetail): self
    {
        if ($this->assetTransferDetails->contains($assetTransferDetail)) {
            $this->assetTransferDetails->removeElement($assetTransferDetail);
        }
        return $this;
    }

}
