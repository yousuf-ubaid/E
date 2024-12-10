<?php

use App\Exception\InvalidOperationException;
use App\Exception\NotFoundException;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class CompanyUpdates
 * 
 */
class CompanyUpdates extends ERP_Controller
{
   /**
   * Constructor
   * 
   */
   public function __construct(){
      parent::__construct();
      $this->load->service('CompanyUpdatesService');
   }

   /**
    * Create a new company update.
    * 
    * @return void
    */
   public function create(): void
   { 
      $this->form_validation->set_rules('title', 'Title', 'trim|required');
      $this->form_validation->set_rules('description', 'Description', 'trim|required');
      $this->form_validation->set_rules('expiryDate', 'Expiry Date', 'trim|required|validate_datetime');
       
      if ($this->form_validation->run() == FALSE) {
         $this->sendResponse('e', validation_errors());
         return;
      } 

      try {
         $this->CompanyUpdatesService->create($this->input->post(NULL, TRUE));
      } catch (InvalidOperationException $e) {
         $this->sendResponse('e', $e->getMessage());
         return;
      }

      $this->sendResponse('s', 'Successfully created');
   }
   
   /**
    * Delete a company update by ID.
    * 
    * @return void
    */
   public function delete(): void
   {
      $id = $this->input->post('id');
      if (!$id) {
         $this->sendResponse('e', 'Id not found');
         return;
      }

      try {
         $this->CompanyUpdatesService->getById((int)$id);
      } catch (NotFoundException $e) {
         $this->sendResponse('e', $e->getMessage());
         return;
      }

      try {
         $this->CompanyUpdatesService->delete((int)$id);
      } catch (InvalidOperationException $e) {
         $this->sendResponse('e', $e->getMessage());
         return;
      }

      $this->sendResponse('s','Successfully deleted');
   }
   
   /**
    * Update an existing company update by ID.
    * 
    * @return void
    */
   public function update(): void
   {
      $this->form_validation->set_rules('id', 'Id', 'trim|required');
      $this->form_validation->set_rules('title', 'Title', 'trim|required');
      $this->form_validation->set_rules('description', 'Description', 'trim|required');
      $this->form_validation->set_rules('expiryDate', 'Expiry Date', 'trim|required|validate_datetime');

      if ($this->form_validation->run() == FALSE) {
         $this->sendResponse('e', validation_errors());
         return;
      }

      $id = $this->input->post('id');

      try {
         $this->CompanyUpdatesService->getById((int)$id);
      } catch (NotFoundException $e) {
         $this->sendResponse('e', $e->getMessage());
         return;
      }

      try {
         $this->CompanyUpdatesService->update(
            (int)$id,
            $this->input->post(NULL, TRUE)
         );
      } catch (InvalidOperationException $e) {
         $this->sendResponse('e', $e->getMessage());
         return;
      }

      $this->sendResponse('s', 'Successfully created');
   }

   /**
    * Load all company updates.
    *
    * @return void
    */
   function loadCompanyUpdates(): void
   {
      $this->CompanyUpdatesService->loadCompanyUpdates();
   }

   /**
    * Delete a company update by ID.
    * 
    * @return void
    */
    public function getById(): void
    {
      $id = $this->input->post('id');
 
      try {
         $this->sendResponse('s', 'Success', $this->CompanyUpdatesService->getById((int)$id));
      } catch (NotFoundException $e) {
         $this->sendResponse('e', $e->getMessage());
      }
    }
}