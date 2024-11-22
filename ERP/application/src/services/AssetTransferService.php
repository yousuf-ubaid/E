<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Event\AssetTransferEvent;
use App\Exception\EntityNotFoundException;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Src\Builders\AssetTransferBuilder;
use App\Src\DTO\AssetTransferDto;
use App\Src\Entities\Asset;
use App\Src\Entities\AssetTransfer;
use App\Src\Entities\AssetTransferDetail;
use App\Src\Repositories\AssetRepository;
use App\Src\Repositories\AssetTransferRepository;
use DateTime;
use InvalidArgumentException;
use function array_map;

/**
 * Class AssetTransferService
 *
 * @package App\Src\Services
 */
final class AssetTransferService extends Service
{
    /**
     * Document id
     *
     * @const string
     */
    private const string DOCUMENT_ID = 'FAT';

    /**
     * Status
     *
     * @const string
     */
    private const string STATUS_CONFIRMED = 'confirmed';
    private const string STATUS_DRAFT = 'draft';

    /**
     * Status
     *
     * @var string[]
     */
    private static array $status = [
        self::STATUS_CONFIRMED,
        self::STATUS_DRAFT,
    ];

    /**
     * Asset repository
     *
     * @var AssetTransferRepository
     */
    private AssetTransferRepository $assetTransferRepository;

    /**
     * Asset repository
     *
     * @var AssetRepository
     */
    private AssetRepository $assetRepository;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->ci->load->library('doctrine');
        $this->ci->load->library('sequence');
        $this->ci->load->service('AssetLocationService');
        $this->ci->load->service('EmployeeService');
        $this->ci->load->service('AssetMasterService');
        $this->ci->load->library('EventService');

        $entityManager = $this->ci->doctrine->getEntityManager();
        $this->assetTransferRepository = $entityManager->getRepository(AssetTransfer::class);
        $this->assetRepository = $entityManager->getRepository(Asset::class);
    }

    /**
     * Create a new asset transfer
     *
     * @param array<string, string|int> $data
     * @return int
     */
    public function create(array $data): int
    {
        $this->validate($data);

        $assetTransfer = (new AssetTransferBuilder())
            ->setDocumentID(self::DOCUMENT_ID)
            ->setDocumentCode(null)
            ->setDocumentDate(new DateTime((string)$data['documentDate']))
            ->setLocationFromID((int)$data['locationFromID'])
            ->setLocationToId((int)$data['locationToID'])
            ->setRequestedEmpID((int)$data['requestedEmpID'])
            ->setIssuedEmpID((int)$data['issuedEmpID'])
            ->setNarration(is_string($data['narration']) ? $data['narration'] : null)
            ->setStatus((string)$data['status'])
            ->setCompanyID((int)current_companyID());

        if ($data['status'] === self::STATUS_CONFIRMED) {
            $this->ci->sequence->sequence_generator("FAT");
            $assetTransfer
                ->setDocumentCode($this->ci->sequence->sequence_generator("FAT"))
                ->setConfirmedYN(1)
                ->setConfirmedByName($this->ci->common_data['current_user'])
                ->setConfirmedByEmpID($this->ci->common_data['current_userID'])
                ->setConfirmedDate(new DateTime());
        }

        $asset = $assetTransfer->build();
        $this->commit($asset);

        return $asset->getId();
    }

    /**
     * Update an existing asset transfer
     *
     * @param int $id
     * @param array<string, int|string> $data
     * @throws ForbiddenException
     * @throws ValidationException
     * @return void
     */
    public function update(int $id, array $data): void
    {
        $this->validate($data);

        $assetTransfer = $this->findById($id);

        if ($assetTransfer->getStatus() === self::STATUS_CONFIRMED) {
            throw new ForbiddenException('You cannot perform this action : Document is already confirmed');
        }

        if ($data['status'] === self::STATUS_CONFIRMED && $assetTransfer->getDetails()->isEmpty()){
            throw new ValidationException('You cannot confirm the document without detail');
        }

        if ($assetTransfer->getLocationFromID() !== (int)$data['locationFromID']) {
            throw new ForbiddenException('You cannot change the from location as detail exists');
        }

        $assetTransfer->setDocumentDate(new DateTime((string)$data['documentDate']))
        ->setLocationFromID((int)$data['locationFromID'])
        ->setLocationToId((int)$data['locationToID'])
        ->setRequestedEmpID((int)$data['requestedEmpID'])
        ->setIssuedEmpID((int)$data['issuedEmpID'])
        ->setNarration(is_string($data['narration']) ? $data['narration'] : null)
        ->setStatus((string)$data['status']);

        if ($data['status'] === self::STATUS_CONFIRMED) {
            $assetTransfer
                ->setDocumentCode($this->ci->sequence->sequence_generator(self::DOCUMENT_ID))
                ->setConfirmedYN(1)
                ->setConfirmedByName($this->ci->common_data['current_user'])
                ->setConfirmedByEmpID($this->ci->common_data['current_userID'])
                ->setConfirmedDate(new DateTime());
        }

        $this->commit($assetTransfer);

        if ($data['status'] === self::STATUS_CONFIRMED) {
            $this->ci->eventservice->dispatch(new AssetTransferEvent($id));
        }
    }

    /**
     * Add detail
     *
     * @param int $id
     * @param array<int, mixed> $data
     * @throws NotFoundException
     * @throws ForbiddenException
     * @throws InvalidArgumentException
     * @return void
     */
    public function addDetail(int $id, array $data): void
    {
        $assetTransfer = $this->findById($id);

        if ($assetTransfer->getStatus() === self::STATUS_CONFIRMED) {
            throw new ForbiddenException('You cannot perform this action : Document is already confirmed');
        }

        if ($data) {
            foreach ($data['faID'] as $key => $faId) {
                $faId = (int)$faId;
                try {
                    $assetMaster = $this->assetRepository->findById($faId);
                } catch (EntityNotFoundException $e) {
                    throw new ValidationException($e->getMessage());
                }

                if ($assetTransfer->getLocationFromID() !== $assetMaster->getCurrentLocation()) {
                    throw new ValidationException('Asset is not belongs to from location');
                }

                $itemExist = !$assetTransfer->getDetails()->isEmpty() && $assetTransfer->getDetails()->findFirst(function (int $key, AssetTransferDetail $transferDetail) use ($faId): bool {
                    return $transferDetail->getAsset()?->getId() === $faId;
                });

                if ($itemExist) {
                    throw new InvalidArgumentException('Already asset added');
                }

                $exist = $this->assetTransferRepository->isFaIdExistInNotConfirmed($faId);
                if ($exist) {
                    throw new ValidationException('Asset already pulled in other document');
                }

                if (isset($data['detailID'][$key]) && $data['detailID'][$key]) {
                    $detailId = (int)$data[$key]['detailID'];
                    $transferDetail = $assetTransfer->getDetails()->findFirst(function (int $key, AssetTransferDetail $transferDetail) use ($detailId): bool {
                        return $transferDetail->getId() === $detailId;
                    });

                    if ($transferDetail instanceof AssetTransferDetail) {
                        $transferDetail->setAsset($assetMaster)
                            ->setFaDescription($assetMaster->getAssetDescription())
                            ->setComment($data['comment'][$key] ?? null);
                    }
                } else {
                    $assetTransferDetail = (new AssetTransferDetail())
                        ->setAsset($assetMaster)
                        ->setFaDescription($assetMaster->getAssetDescription())
                        ->setComment($data['comment'][$key] ?? null);
                    $assetTransfer->addDetail($assetTransferDetail);
                }
            }
        }

        $this->commit($assetTransfer);
    }

    /**
     * Remove detail
     *
     * @param int $id
     * @param int $detailId
     * @throws ForbiddenException
     * @throws NotFoundException
     * @return void
     */
    public function removeDetail(int $id, int $detailId): void
    {
        $assetTransfer = $this->findById($id);

        if ($assetTransfer->getStatus() === self::STATUS_CONFIRMED) {
            throw new ForbiddenException('You cannot perform this action : Document is already confirmed');
        }

        $transferDetail = $assetTransfer->getDetails()->findFirst(function (int $key, AssetTransferDetail $transferDetail) use ($detailId): bool {
            return $transferDetail->getId() === $detailId;
        });

        if (!$transferDetail) {
            throw new NotFoundException('Detail not found');
        }

        $assetTransfer->removeDetail($transferDetail);
        $this->commit($assetTransfer);
    }

    /**
     * Find an AssetTransfer by ID
     *
     * @param int $id
     * @throws NotFoundException
     * @return AssetTransfer
     */
    public function findById(int $id): AssetTransfer
    {
        try {
            return $this->assetTransferRepository->findById($id);
        } catch (EntityNotFoundException $e){
            throw new NotFoundException($e->getMessage());
        }
    }

    /**
     * Delete detail
     *
     * @param int $id
     * @throws ForbiddenException
     * @return void
     */
    public function delete(int $id): void
    {
        $assetTransfer = $this->findById($id);

        if ($assetTransfer->getStatus() === self::STATUS_CONFIRMED) {
            throw new ForbiddenException('You cannot perform this action : Document is already confirmed');
        }

        $assetTransfer->setIsDeleted(1);
        $this->commit($assetTransfer);
    }

    /**
     * Commit
     *
     * @param AssetTransfer $assetTransfer
     * @return void
     */
    private function commit(AssetTransfer $assetTransfer): void
    {
        $this->assetTransferRepository->persist($assetTransfer);
    }

    /**
     * Validate data
     *
     * @param array<string, int|string> $data
     * @throws ValidationException
     * @throws InvalidArgumentException
     * @return void
     */
    private function validate(array $data): void
    {
        try {
            $this->ci->AssetLocationService->getById((int)$data['locationFromID']);
        } catch (NotFoundException $e) {
            throw new ValidationException('From location: ' . $e->getMessage());
        }

        try {
            $this->ci->AssetLocationService->getById((int)$data['locationToID']);
        } catch (NotFoundException $e) {
            throw new ValidationException('To location: ' . $e->getMessage());
        }

        try {
            $this->ci->EmployeeService->getById((int)$data['requestedEmpID']);
        } catch (NotFoundException $e) {
            throw new ValidationException('Requested employee:' . $e->getMessage());
        }

        try {
            $this->ci->EmployeeService->getById((int)$data['issuedEmpID']);
        } catch (NotFoundException $e) {
            throw new ValidationException('Issued employee:' . $e->getMessage());
        }

        if (!in_array($data['status'], self::$status)) {
            throw new InvalidArgumentException('Invalid status value');
        }

        if ((int)$data['locationFromID'] === (int)$data['locationToID']) {
            throw new ValidationException('Location from and to should not be equal');
        }
    }

    /**
     * DTO
     *
     * @param AssetTransfer $assetTransfer
     * @return AssetTransferDto
     */
    public function entityToDTO(AssetTransfer $assetTransfer): AssetTransferDto
    {
        return new AssetTransferDto(
            $assetTransfer->getId(),
            $assetTransfer->getDocumentID(),
            $assetTransfer->getDocumentDate()->format('d-m-Y'),
            $assetTransfer->getLocationFromID(),
            $assetTransfer->getLocationToID(),
            $assetTransfer->getRequestedEmpID(),
            $assetTransfer->getIssuedEmpID(),
            $assetTransfer->getStatus(),
            $assetTransfer->getCompanyID(),
            $assetTransfer->getDocumentCode(),
            $assetTransfer->getNarration(),
            $assetTransfer->getIsDeleted(),
            $assetTransfer->getConfirmedYN(),
            $assetTransfer->getConfirmedByName(),
            $assetTransfer->getConfirmedByEmpID(),
            $assetTransfer->getConfirmedDate()?->format('d-m-Y'),
            array_map(function ($detail) {
                return [
                    'id' => $detail->getId(),
                    'faCode' => $detail->getAsset()?->getFaCode(),
                    'faDescription' => $detail->getFaDescription(),
                    'comment' => $detail->getComment(),
                ];
            }, $assetTransfer->getDetails()->toArray())
        );

    }

}