<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;

/**
 * Class Designation
 */
class Designation extends ERP_Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->service('DesignationService');
    }

    /**
     * Update designation.
     *
     * @return void
     */
    public function update(): void
    {
        $this->form_validation->set_rules('JDDescription', 'Job description', 'trim|required');
        $this->form_validation->set_rules('id', 'Id', 'trim|required');

        if (!$this->form_validation->run()) {
            $this->sendResponse('e', validation_errors());
            return;
        }

        try {
            $this->DesignationService->update(
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
            $data = $this->DesignationService->findById((int)$id);
        } catch (NotFoundException $e) {
            $this->sendResponse('e', $e->getMessage());
            return;
        }

        $data = $this->DesignationService->entityToDTO($data);

        $this->sendResponse('s','Successfully retrieved', $data);
    }

    /**
     * Delete
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
            $this->DesignationService->delete((int)$id);
        } catch (NotFoundException|ForbiddenException $e) {
            $this->sendResponse('e', $e->getMessage());
            return;
        }

        $this->sendResponse('s','Successfully deleted', []);
    }
}
