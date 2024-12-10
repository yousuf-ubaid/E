<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;

/**
 * Class AssetTransfer
 * 
 */
class AssetTransfer extends ERP_Controller
{
   /**
    * Constructor
    * 
    */
   public function __construct()
   {
      parent::__construct();
      $this->load->service('AssetTransferService');
      $this->load->service('AssetMasterService');
   }

   /**
    * Create a new asset transfer.
    * 
    * @return void
    */
   public function create(): void
   {
      $this->form_validation->set_rules('documentDate', 'DocumentDate', 'trim|required|validate_datetime');
      $this->form_validation->set_rules('requestedEmpID', 'RequestedEmpID', 'trim|required');
      $this->form_validation->set_rules('issuedEmpID', 'IssuedEmpID', 'trim|required');
      $this->form_validation->set_rules('locationToID', 'LocationToID', 'trim|required');
      $this->form_validation->set_rules('locationFromID', 'LocationFromID', 'trim|required');
      $this->form_validation->set_rules('status', 'Status', 'trim|required');

      if (!$this->form_validation->run()) {
         $this->sendResponse('e', validation_errors());
         return;
      }

      try {
         $id = $this->AssetTransferService->create($this->input->post(NULL, TRUE));
      } catch (ValidationException|InvalidArgumentException $e) {
         $this->sendResponse('e', $e->getMessage());
         return;
      }

      $this->sendResponse('s', 'Successfully created', ['id' => $id]);
   }

    /**
     * Update asset transfer.
     *
     * @return void
     */
    public function update(): void
    {
        $this->form_validation->set_rules('documentDate', 'DocumentDate', 'trim|required|validate_datetime');
        $this->form_validation->set_rules('requestedEmpID', 'RequestedEmpID', 'trim|required');
        $this->form_validation->set_rules('issuedEmpID', 'IssuedEmpID', 'trim|required');
        $this->form_validation->set_rules('locationToID', 'LocationToID', 'trim|required');
        $this->form_validation->set_rules('locationFromID', 'LocationFromID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');

        if (!$this->form_validation->run()) {
            $this->sendResponse('e', validation_errors());
            return;
        }

        try {
            $this->AssetTransferService->update(
                (int)$this->input->post('id'),
                $this->input->post(NULL, TRUE)
            );
        } catch (ValidationException|InvalidArgumentException|ForbiddenException $e) {
            $this->sendResponse('e', $e->getMessage());
            return;
        }

        $this->sendResponse('s', 'Successfully updated');
    }

   /**
    * Add asset details
    * 
    * @return void
    */
   public function addDetail(): void
   {
      $this->form_validation->set_rules('faID[]', 'FaID', 'trim|required');

      if (!$this->form_validation->run()) {
         $this->sendResponse('e', validation_errors());
         return;
      }

      try {
         $this->AssetTransferService->addDetail(
             (int)$this->input->post('id'),
             $this->input->post(NULL, TRUE)
         );
      } catch (ValidationException|InvalidArgumentException|ForbiddenException $e) {
         $this->sendResponse('e', $e->getMessage());
         return;
      }

      $this->sendResponse('s', 'Successfully saved');
   }

    /**
     * Remove detail
     *
     * @return void
     */
    public function removeDetail(): void
    {
        $id = $this->input->post('id');
        if (!$id) {
            $this->sendResponse('e', 'Id not found');
            return;
        }

        $detailId = $this->input->post('detailId');
        if (!$detailId) {
            $this->sendResponse('e', 'detail not found');
            return;
        }

        try {
            $this->AssetTransferService->removeDetail(
                (int)$id,
                (int)$detailId
            );
        } catch (NotFoundException|ForbiddenException $e) {
            $this->sendResponse('e', $e->getMessage());
            return;
        }

        $this->sendResponse('s', 'Successfully deleted');
    }

   /**
    * Delete a asset transfer by ID.
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
         $this->AssetTransferService->delete((int)$id);
      } catch (NotFoundException|ForbiddenException $e) {
         $this->sendResponse('e', $e->getMessage());
         return;
      }

      $this->sendResponse('s','Successfully deleted');
   }

    /**
     * Get by id
     *
     * @return void
     */
    public function getById(): void
    {
        $id = $this->input->post('id');
        if (!$id) {
            $this->sendResponse('e', 'Id not found');
            return;
        }

        try {
            $data = $this->AssetTransferService->findById((int)$id);
        } catch (NotFoundException $e) {
            $this->sendResponse('e', $e->getMessage());
            return;
        }

        $data = $this->AssetTransferService->entityToDTO($data);

        $this->sendResponse('s','Successfully retrieved', $data);
    }

   /**
    * Load all asset transfer.
    *
    * @return void
    */
   function getAssetTransfer(): void
   {
      $this->AssetMasterService->getAssetTransfer();
   }

   /**
    * Fetch asset master data.
    * 
    * @return void
    */
   public function fetchAssets(): void
   {
      echo json_encode($this->AssetMasterService->fetchAssets());
   }
}
