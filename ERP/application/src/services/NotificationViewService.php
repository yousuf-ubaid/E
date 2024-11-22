<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Exception\InvalidOperationException;
use App\Exception\NotFoundException;
use Exception;

/**
 * Class NotificationViewService
 * 
 * @package App\Src\Services
 */
final class NotificationViewService extends Service
{
   /**
   * Constructor
   */
   public function __construct() {
      parent::__construct();
      $this->ci->load->model('NotificationView_model');
   }

   /**
    * create a view entry
    *
    * @param array<string, mixed> $data 
    * @throws InvalidOperationException 
    * @return void
    */
   public function create(array $data): void
   {
      $result = $this->ci->NotificationView_model->create($data);
      if (!$result) {
         throw new InvalidOperationException('Failed to create view entry');
      }
   }

   /**
    * Get details of a company update by ID
    *
    * @param int $id
    * @throws NotFoundException 
    * @return array<string, mixed>
    */
   public function getById(int $id): array
   {
      $data = $this->ci->NotificationView_model->getById($id);
      if(empty($data)) {
         throw new NotFoundException('No record found');
      }
      return $data;
   }

}