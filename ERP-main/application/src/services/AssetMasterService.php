<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Exception\NotFoundException;

/**
 * Class AssetMasterService
 * 
 * @package App\Src\Services
 */
final class AssetMasterService extends Service
{
   /**
   * Constructor
   */
   public function __construct() {
      parent::__construct();
      $this->ci->load->model('AssetTransfer_model');
   }

   /**
    * Fetch item code
    *
    * @throws NotFoundException
    * @return array<string, mixed>
    */
   public function fetchAssets(): array
   {
      return $this->ci->AssetTransfer_model->fetchAssets();
   }

   /**
    * Load all asset transfer.
    *
    * @return void
    */
   function getAssetTransfer(): void
   {
      $this->ci->AssetTransfer_model->getAssetTransfer();
   }

   /**
    * Fetch Asset details by ID
    *
    * @param int $faId
    * @throws NotFoundException
    * @return array
    */
   public function getById(int $faId): array
   {
      $data = $this->ci->AssetTransfer_model->getById($faId);
      if (empty($data)) {
         throw new NotFoundException('Asset not found');
      }
      return $data;
   }
}