<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Exception\InvalidOperationException;
use App\Exception\NotFoundException;
use Exception;

/**
 * Class CompanyUpdatesService
 * 
 * @package App\Src\Services
 */
final class CompanyUpdatesService extends Service
{
   /**
   * Constructor
   */
   public function __construct() {
      parent::__construct();
      $this->ci->load->model('Company_updates_model');
   }
   
    /**
    * Create a new company update.
    *
    * @param array<string, mixed> $data 
    * @throws InvalidOperationException 
    * @return void 
    */
   public function create(array $data): void
   {
      $result = $this->ci->Company_updates_model->create($data);
      if (!$result) {
         throw new InvalidOperationException('Create failed');
      } 
   }

   /**
    * Delete a company update by ID.
    *
    * @param int $id
    * @throws InvalidOperationException 
    * @return void 
    */
   public function delete(int $id): void
   {
      $result = $this->ci->Company_updates_model->delete($id);
      if (!$result) {
         throw new InvalidOperationException('Delete failed');
      }
   }

   /**
    * Update an existing company update.
    *
    * @param int $id
    * @param array<string, mixed> $data
    * @throws InvalidOperationException 
    * @return void
    */
   public function update(int $id, array $data): void
   {
      $result = $this->ci->Company_updates_model->update($id, $data);
      if (!$result) {
         throw new InvalidOperationException('Update failed');
      } 
   }

    /**
    * Get a company update by ID.
    *
    * @param int $id
    * @throws NotFoundException 
    * @return array<string, mixed>
    */
   public function getById(int $id): array
   {
      $data = $this->ci->Company_updates_model->getById($id);
      if(empty($data)) {
         throw new NotFoundException('No record found');
      }
      return $data;
   }

   /**
    * Load all company updates.
    * 
    * @return void
    */
   public function loadCompanyUpdates(): void
   {
      $this->ci->Company_updates_model->loadCompanyUpdates();
   }

}