<?php

declare(strict_types=1);

namespace App\Src\DTO;

/**
 * Data Transfer Object for Asset Transfer.
 * Represents the details of an asset transfer operation.
 */
final class AssetTransferDto
{
    /**
     * The unique identifier for the asset transfer.
     *
     * @var int $id
     */
    public int $id;

    /**
     * The document ID associated with the asset transfer.
     *
     * @var string $documentID
     */
    public string $documentID;

    /**
     * Optional code for the document.
     *
     * @var string|null $documentCode
     */
    public ?string $documentCode = null;

    /**
     * The date of the document (format: YYYY-MM-DD).
     *
     * @var string $documentDate
     */
    public string $documentDate;

    /**
     * The ID of the location the asset is transferred from.
     *
     * @var int $locationFromID
     */
    public int $locationFromID;

    /**
     * The ID of the location the asset is transferred to.
     *
     * @var int $locationToID
     */
    public int $locationToID;

    /**
     * The employee ID of the person who requested the transfer.
     *
     * @var int $requestedEmpID
     */
    public int $requestedEmpID;

    /**
     * The employee ID of the person who issued the transfer.
     *
     * @var int $issuedEmpID
     */
    public int $issuedEmpID;

    /**
     * Optional narration or notes for the transfer.
     *
     * @var string|null $narration
     */
    public ?string $narration = null;

    /**
     * The status of the asset transfer (e.g., pending, confirmed).
     *
     * @var string $status
     */
    public string $status;

    /**
     * The ID of the company associated with the transfer.
     *
     * @var int $companyID
     */
    public int $companyID;

    /**
     * Indicates if the record is deleted (0 = not deleted, 1 = deleted).
     *
     * @var int $isDeleted
     */
    public int $isDeleted = 0;

    /**
     * Indicates if the transfer is confirmed (0 = no, 1 = yes).
     *
     * @var int $confirmedYN
     */
    public int $confirmedYN = 0;

    /**
     * The name of the person who confirmed the transfer, if applicable.
     *
     * @var string|null $confirmedByName
     */
    public ?string $confirmedByName = null;

    /**
     * The employee ID of the person who confirmed the transfer, if applicable.
     *
     * @var string|null $confirmedByEmpID
     */
    public ?string $confirmedByEmpID = null;

    /**
     * The date when the transfer was confirmed (format: YYYY-MM-DD), if applicable.
     *
     * @var string|null $confirmedDate
     */
    public ?string $confirmedDate = null;

    /**
     * An array of details related to the transfer, if any.
     *
     * @var array<int, mixed>|null $detail
     */
    public ?array $detail = [];

    /**
     * Constructor for AssetTransferDto.
     *
     * @param int $id The unique identifier for the asset transfer.
     * @param string $documentID The document ID associated with the transfer.
     * @param string $documentDate The date of the document.
     * @param int $locationFromID The ID of the origin location.
     * @param int $locationToID The ID of the destination location.
     * @param int $requestedEmpID The employee ID who requested the transfer.
     * @param int $issuedEmpID The employee ID who issued the transfer.
     * @param string $status The status of the transfer.
     * @param int $companyID The company ID related to the transfer.
     * @param string|null $documentCode Optional document code.
     * @param string|null $narration Optional narration.
     * @param int $isDeleted Indicates if the record is deleted.
     * @param int $confirmedYN Indicates if the transfer is confirmed.
     * @param string|null $confirmedByName Name of the person who confirmed.
     * @param string|null $confirmedByEmpID Employee ID of confirmer.
     * @param string|null $confirmedDate Confirmation date.
     * @param array<int, mixed>|null $detail Transfer details array.
     */
    public function __construct(
        int $id,
        string $documentID,
        string $documentDate,
        int $locationFromID,
        int $locationToID,
        int $requestedEmpID,
        int $issuedEmpID,
        string $status,
        int $companyID,
        ?string $documentCode = null,
        ?string $narration = null,
        int $isDeleted = 0,
        int $confirmedYN = 0,
        ?string $confirmedByName = null,
        ?string $confirmedByEmpID = null,
        ?string $confirmedDate = null,
        ?array $detail = []
    ) {
        $this->id = $id;
        $this->documentID = $documentID;
        $this->documentDate = $documentDate;
        $this->locationFromID = $locationFromID;
        $this->locationToID = $locationToID;
        $this->requestedEmpID = $requestedEmpID;
        $this->issuedEmpID = $issuedEmpID;
        $this->status = $status;
        $this->companyID = $companyID;
        $this->documentCode = $documentCode;
        $this->narration = $narration;
        $this->isDeleted = $isDeleted;
        $this->confirmedYN = $confirmedYN;
        $this->confirmedByName = $confirmedByName;
        $this->confirmedByEmpID = $confirmedByEmpID;
        $this->confirmedDate = $confirmedDate;
        $this->detail = $detail;
    }
}
