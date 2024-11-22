<?php

declare(strict_types=1);

namespace App\Listener;

use App\Src\Entities\AssetTransfer;
use DateTime;

/**
 * Listener for doctrine
 *
 * @package App\Listener
 */
final class AssetTransferListener
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
     * @param AssetTransfer $assetTransfer
     * @return void
     */
    public function prePersist(AssetTransfer $assetTransfer): void
    {
        $assetTransfer->setCreatedDateTime(new DateTime());
        $assetTransfer->setCreatedPCID($this->ci->common_data['current_pc']);
        $assetTransfer->setCreatedUserID($this->ci->common_data['current_userID']);
        $assetTransfer->setCreatedUserName($this->ci->common_data['current_user']);
    }

    /**
     * Pre Update
     *
     * @param AssetTransfer $assetTransfer
     * @return void
     */
    public function preUpdate(AssetTransfer $assetTransfer): void
    {
        $assetTransfer->setModifiedDateTime(new DateTime());
        $assetTransfer->setModifiedPCID($this->ci->common_data['current_pc']);
        $assetTransfer->setModifiedUserID($this->ci->common_data['current_userID']);
        $assetTransfer->setModifiedUserName($this->ci->common_data['current_user']);
    }

}

