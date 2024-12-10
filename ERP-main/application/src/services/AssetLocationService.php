<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Exception\InvalidOperationException;
use App\Exception\NotFoundException;
use Exception;

/**
 * Class AssetLocationService
 * 
 * @package App\Src\Services
 */
final class AssetLocationService extends Service
{
   /**
   * Constructor
   */
   public function __construct() {
      parent::__construct();
      $this->ci->load->model('AssetLocation_model');
   }

    /**
     * Fetch location details by ID
     *
     * @param int $locationID
     * @throws NotFoundException
     * @return array<string, mixed>
     */
   public function getById(int $locationID): array
   {
      $data = $this->ci->AssetLocation_model->getById($locationID);
      if (empty($data)) {
         throw new NotFoundException('Location not found');
      }
      return $data;
   }
}