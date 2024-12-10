<?php

declare(strict_types=1);

namespace App\Src\DTO;

/**
 * Data Transfer Object for Asset Transfer.
 * Represents the details of an asset transfer operation.
 *
 * @package App\Src\DTO
 */
final readonly class AssetTransferDto
{
    /**
     * Constructor for AssetTransferDto.
     *
     * @param int $id
     * @param string $documentID
     * @param string $documentDate
     * @param int $locationFromID
     * @param int $locationToID
     * @param int $requestedEmpID
     * @param int $issuedEmpID
     * @param string $status
     * @param int $companyID
     * @param string|null $documentCode
     * @param string|null $narration
     * @param int $isDeleted
     * @param int $confirmedYN
     * @param string|null $confirmedByName
     * @param string|null $confirmedByEmpID
     * @param string|null $confirmedDate
     * @param array<int, mixed>|null $detail
     */
    public function __construct(
        public int     $id,
        public string  $documentID,
        public string  $documentDate,
        public int     $locationFromID,
        public int     $locationToID,
        public int     $requestedEmpID,
        public int     $issuedEmpID,
        public string  $status,
        public int     $companyID,
        public ?string $documentCode = null,
        public ?string $narration = null,
        public int     $isDeleted = 0,
        public int     $confirmedYN = 0,
        public ?string $confirmedByName = null,
        public ?string $confirmedByEmpID = null,
        public ?string $confirmedDate = null,
        public ?array  $detail = []
    ) {

    }

}
