<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\AssetTransferEvent;
use App\Src\Entities\Asset;
use App\Src\Entities\AssetTransfer;
use App\Src\Repositories\AssetRepository;
use App\Src\Repositories\AssetTransferRepository;
use League\Event\Listener;

/**
 * Listener for Asset transfer confirm
 *
 * @package App\Listener
 */
class AssetTransferConfirmListener implements Listener
{
    /**
     * Ci
     *
     * @var \CI_Controller $ci
     */
    private \CI_Controller $ci;

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
        $this->ci =& get_instance();
        $this->ci->load->library('doctrine');
        $entityManager = $this->ci->doctrine->getEntityManager();
        $this->assetTransferRepository = $entityManager->getRepository(AssetTransfer::class);
        $this->assetRepository = $entityManager->getRepository(Asset::class);
    }

    /**
     * Invoke
     *
     * @param object $event
     * @return void
     */
    public function __invoke(object $event): void
    {
        if (!($event instanceof AssetTransferEvent)) {
            return;
        }

        $faId = $event->getId();

        $assetTransfer = $this->assetTransferRepository->findById($faId);
        $details = $assetTransfer->getDetails();
        foreach ($details as $detail) {
            $asset = $this->assetRepository->findById($detail->getAsset()->getId());
            $asset->setCurrentLocation($assetTransfer->getLocationToId());
            $this->assetRepository->persist($asset);
        }


    }

}

