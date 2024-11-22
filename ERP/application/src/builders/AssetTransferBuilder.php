<?php

declare(strict_types=1);

namespace App\Src\Builders;

use App\Src\Entities\AssetTransfer;
use DateTime;

/**
 * Builder class for constructing and updating an AssetTransfer entity.
 *
 * @package App\Src\Builders
 */
final class AssetTransferBuilder
{
    /**
     * The unique identifier associated with the asset transfer document.
     *
     * @var string
     */
    private string $documentID;

    /**
     * The code associated with the asset transfer document.
     *
     * @var string|null
     */
    private ?string $documentCode;

    /**
     * The date when the asset transfer document was created or issued.
     *
     * @var DateTime
     */
    private DateTime $documentDate;

    /**
     * The ID of the location from which the asset is being transferred.
     * This field is nullable, as it may not be required in all cases.
     *
     * @var int
     */
    private int $locationFromID;

    /**
     * The ID of the location to which the asset is being transferred.
     * This field is nullable, as it may not be required in all cases.
     *
     * @var int
     */
    private int $locationToId;

    /**
     * The ID of the employee who requested the asset transfer.
     * This field is nullable, as it may not be applicable for every transfer scenario.
     *
     * @var int
     */
    private int $requestedEmpID;

    /**
     * The ID of the employee who issued the asset transfer.
     * This field is nullable and may be populated when required.
     *
     * @var int
     */
    private int $issuedEmpID;

    /**
     * A description or additional notes regarding the asset transfer.
     * It provides context or additional information about the transfer.
     * This field is nullable.
     *
     * @var string|null
     */
    private ?string $narration = null;

    /**
     * The current status of the asset transfer.
     * The possible values are typically 'draft' or 'confirmed'.
     * The default value is 'draft'.
     *
     * @var string
     */
    private string $status = 'draft';

    /**
     * The ID of the company associated with the asset transfer.
     * This field is nullable, as some transfers may not be tied to a specific company.
     *
     * @var int
     */
    private int $companyID;

    /**
     * A flag indicating whether the asset transfer has been logically deleted.
     * Default is 0 (not deleted); 1 indicates the transfer has been deleted.
     *
     * @var int
     */
    private int $isDeleted = 0;

    /**
     * A flag indicating whether the asset transfer has been confirmed.
     * Default is 0 (not confirmed); 1 indicates the transfer has been confirmed.
     *
     * @var int
     */
    private int $confirmedYN = 0;

    /**
     * The name of the person who confirmed the asset transfer.
     * This field is nullable.
     *
     * @var string|null
     */
    private ?string $confirmedByName = null;

    /**
     * The employee ID of the person who confirmed the asset transfer.
     * This field is nullable.
     *
     * @var string|null
     */
    private ?string $confirmedByEmpID = null;

    /**
     * The date and time when the asset transfer was confirmed.
     * This field is nullable.
     *
     * @var \DateTime|null
     */
    private ?\DateTime $confirmedDate = null;

    /**
     * Sets the document ID for the AssetTransfer.
     *
     * @param string $documentID The document ID to set.
     * @return self Fluent interface for method chaining.
     */
    public function setDocumentID(string $documentID): self
    {
        $this->documentID = $documentID;
        return $this;
    }

    /**
     * Sets the document code for the AssetTransfer.
     *
     * @param string|null $documentCode The document code to set.
     * @return self Fluent interface for method chaining.
     */
    public function setDocumentCode(?string $documentCode): self
    {
        $this->documentCode = $documentCode;
        return $this;
    }

    /**
     * Sets the document date for the AssetTransfer.
     *
     * @param DateTime $documentDate The document date to set.
     * @return self Fluent interface for method chaining.
     */
    public function setDocumentDate(DateTime $documentDate): self
    {
        $this->documentDate = $documentDate;
        return $this;
    }

    /**
     * Sets the location ID from which the asset is transferred.
     *
     * @param int $locationFromID The location ID to set (nullable).
     * @return self Fluent interface for method chaining.
     */
    public function setLocationFromID(int $locationFromID): self
    {
        $this->locationFromID = $locationFromID;
        return $this;
    }

    /**
     * Sets the location ID to which the asset is transferred.
     *
     * @param int $locationToId The location ID to set (nullable).
     * @return self Fluent interface for method chaining.
     */
    public function setLocationToId(int $locationToId): self
    {
        $this->locationToId = $locationToId;
        return $this;
    }

    /**
     * Sets the employee ID requesting the asset transfer.
     *
     * @param int $requestedEmpID The employee ID to set (nullable).
     * @return self Fluent interface for method chaining.
     */
    public function setRequestedEmpID(int $requestedEmpID): self
    {
        $this->requestedEmpID = $requestedEmpID;
        return $this;
    }

    /**
     * Sets the employee ID who issued the asset transfer.
     *
     * @param int $issuedEmpID The employee ID to set (nullable).
     * @return self Fluent interface for method chaining.
     */
    public function setIssuedEmpID(int $issuedEmpID): self
    {
        $this->issuedEmpID = $issuedEmpID;
        return $this;
    }

    /**
     * Sets the narration or description for the asset transfer.
     *
     * @param string|null $narration The narration text (nullable).
     * @return self Fluent interface for method chaining.
     */
    public function setNarration(?string $narration): self
    {
        $this->narration = $narration;
        return $this;
    }

    /**
     * Sets the status of the asset transfer.
     *
     * @param string $status The status to set (e.g., 'draft' or 'confirmed').
     * @return self Fluent interface for method chaining.
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Sets the company ID related to the asset transfer.
     *
     * @param int $companyID The company ID to set (nullable).
     * @return self Fluent interface for method chaining.
     */
    public function setCompanyID(int $companyID): self
    {
        $this->companyID = $companyID;
        return $this;
    }

    /**
     * Sets the delete flag for the asset transfer (soft delete).
     *
     * @param int $isDeleted The delete flag (0 or 1).
     * @return self Fluent interface for method chaining.
     */
    public function setIsDeleted(int $isDeleted): self
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    /**
     * Sets the confirmation flag for the asset transfer.
     *
     * @param int $confirmedYN The confirmation flag (0 or 1).
     * @return self Fluent interface for method chaining.
     */
    public function setConfirmedYN(int $confirmedYN): self
    {
        $this->confirmedYN = $confirmedYN;
        return $this;
    }

    /**
     * Sets the name of the employee who confirmed the asset transfer.
     *
     * @param string|null $confirmedByName The name of the confirming employee (nullable).
     * @return self Fluent interface for method chaining.
     */
    public function setConfirmedByName(?string $confirmedByName): self
    {
        $this->confirmedByName = $confirmedByName;
        return $this;
    }

    /**
     * Sets the employee ID who confirmed the asset transfer.
     *
     * @param string|null $confirmedByEmpID The employee ID to set (nullable).
     * @return self Fluent interface for method chaining.
     */
    public function setConfirmedByEmpID(?string $confirmedByEmpID): self
    {
        $this->confirmedByEmpID = $confirmedByEmpID;
        return $this;
    }

    /**
     * Sets the confirmation date for the asset transfer.
     *
     * @param \DateTime|null $confirmedDate The confirmation date to set (nullable).
     * @return self Fluent interface for method chaining.
     */
    public function setConfirmedDate(?DateTime $confirmedDate): self
    {
        $this->confirmedDate = $confirmedDate;
        return $this;
    }

    /**
     * Builds and returns an AssetTransfer entity based on the set values.
     *
     * @return AssetTransfer The constructed AssetTransfer entity.
     */
    public function build(): AssetTransfer
    {
        $assetTransfer = new AssetTransfer();
        $assetTransfer->setDocumentID($this->documentID);
        $assetTransfer->setDocumentCode($this->documentCode);
        $assetTransfer->setDocumentDate($this->documentDate);
        $assetTransfer->setLocationFromID($this->locationFromID);
        $assetTransfer->setLocationToId($this->locationToId);
        $assetTransfer->setRequestedEmpID($this->requestedEmpID);
        $assetTransfer->setIssuedEmpID($this->issuedEmpID);
        $assetTransfer->setNarration($this->narration);
        $assetTransfer->setStatus($this->status);
        $assetTransfer->setCompanyID($this->companyID);
        $assetTransfer->setIsDeleted($this->isDeleted);
        $assetTransfer->setConfirmedYN($this->confirmedYN);
        $assetTransfer->setConfirmedByName($this->confirmedByName);
        $assetTransfer->setConfirmedByEmpID($this->confirmedByEmpID);
        $assetTransfer->setConfirmedDate($this->confirmedDate);

        return $assetTransfer;
    }

}
