<?php

declare(strict_types=1);

namespace App\Src\Services;

use App\Exception\InvalidOperationException;
use App\Exception\NotFoundException;
use Exception;

/**
 * Class EmployeeService
 * 
 * @package App\Src\Services
 */
final class EmployeeService extends Service
{
   /**
   * Constructor
   */
   public function __construct() {
      parent::__construct();
      $this->ci->load->model('Employee_model');
   }

   /**
    * Get employee by ID
    *
    * @param int $empId Employee ID
    * @return array
    * @throws NotFoundException
    */
   public function getById(int $empId): array 
   {
      $data = $this->ci->Employee_model->getById($empId);
      if (!$data) {
         throw new NotFoundException('Employee not found');
      }
      return $data;
   }
}