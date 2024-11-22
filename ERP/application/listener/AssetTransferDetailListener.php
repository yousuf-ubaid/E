<?php

declare(strict_types=1);

namespace App\Listener;

use App\Src\Entities\AssetTransferDetail;
use DateTime;

/**
 * Listener for doctrine
 *
 * @package App\Listener
 */
final class AssetTransferDetailListener
{
    /**
     * Ci
     *
     * @var \CI_Controller $ci
     */
    private \CI_Controller $ci;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->ci =& get_instance();
    }

    /**
     * Pre persist
     *
     * @param AssetTransferDetail $assetTransferDetail
     * @return void
     */
    public function prePersist(AssetTransferDetail $assetTransferDetail): void
    {
        $assetTransferDetail->setCreatedDateTime(new DateTime());
        $assetTransferDetail->setCreatedPCID($this->ci->common_data['current_pc']);
        $assetTransferDetail->setCreatedUserID($this->ci->common_data['current_userID']);
        $assetTransferDetail->setCreatedUserName($this->ci->common_data['current_user']);

    }

    /**
     * Pre Update
     *
     * @param AssetTransferDetail $assetTransferDetail
     * @return void
     */
    public function preUpdate(AssetTransferDetail $assetTransferDetail): void
    {
        $assetTransferDetail->setModifiedDateTime(new DateTime());
        $assetTransferDetail->setModifiedPCID($this->ci->common_data['current_pc']);
        $assetTransferDetail->setModifiedUserID($this->ci->common_data['current_userID']);
        $assetTransferDetail->setModifiedUserName($this->ci->common_data['current_user']);
    }

}

