<?php

use App\Exception\InvalidOperationException;
use App\Exception\NotFoundException;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class NotificationView
 * 
 */
class NotificationView extends ERP_Controller
{
   /**
   * Constructor
   * 
   */
   public function __construct(){
      parent::__construct();
      $this->load->service('NotificationViewService');
      $this->load->service('DocumentAttachmentService');
      
   }

   /**
    * Create a view entry for a company update.
    *
    * @return void
    */
   public function create(): void
   {
      $this->form_validation->set_rules('id', 'ID', 'trim|required');
      $this->form_validation->set_rules('documentID', 'DocumentID', 'trim|required');

      if ($this->form_validation->run() == FALSE) {
         $this->sendResponse('e', validation_errors());
         return;
      } 
    
      try {
         $this->NotificationViewService->create($this->input->post(NULL, TRUE));
      } catch (InvalidOperationException $e) {
         $this->sendResponse('e', $e->getMessage());
      }
      
      $this->sendResponse('s', 'View logged successfully');
   }

   /**
    * Get a company detail by Id
    *
    * @return void
    */
   public function getDetail(): void
   {
      $this->form_validation->set_rules('id', 'ID', 'trim|required');
      $this->form_validation->set_rules('documentID', 'DocumentID', 'trim|required');

      if ($this->form_validation->run() == FALSE) {
         $this->sendResponse('e', validation_errors());
         return;
      }

      $attachments = [];
      $companyUpdateDetail = [];

      try {
         $attachments = $this->DocumentAttachmentService->getByDocumentId($this->input->post(NULL, TRUE));
      } catch (NotFoundException $e) {
        
      }

      try {
         $companyUpdateDetail = $this->NotificationViewService->getById($this->input->post('id'));
      } catch (InvalidOperationException $e) {
        
      }
    
      $data["companyUpdateDetail"] = $companyUpdateDetail;
      $data["attachments"] = $attachments;
      $this->load->view('system/dashboard/erp_ajax_company_updates_view', $data);
   }

}