<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

use App\Exception\NotFoundException;
use App\Exception\ValidationException;

/**
 * Class DegreeType
 */
class DegreeType extends ERP_Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->service('DegreeTypeService');
    }

    /**
     * Create degree type.
     *
     * @return void
     */
    public function create(): void
    {
        $this->form_validation->set_rules('type', 'type', 'trim|required');

        if (!$this->form_validation->run()) {
            $this->sendResponse('e', validation_errors());
            return;
        }

        try {
            $this->DegreeTypeService->create($this->input->post(NULL, TRUE));
        } catch (ValidationException|InvalidArgumentException $e) {
            $this->sendResponse('e', $e->getMessage());
            return;
        }

        $this->sendResponse('s', 'Successfully created');
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
            $data = $this->DegreeTypeService->findById((int)$id);
        } catch (NotFoundException $e) {
            $this->sendResponse('e', $e->getMessage());
            return;
        }

        $data = $this->DegreeTypeService->entityToDTO($data);

        $this->sendResponse('s','Successfully retrieved', $data);
    }

    /**
     * Get by type
     *
     * @return void
     */
    public function getByType(): void
    {
        $type = $this->input->post('type');
        if (!$type) {
            $this->sendResponse('e', 'Type not found');
            return;
        }

        try {
            $data = $this->DegreeTypeService->getByType($type);
        } catch (NotFoundException $e) {
            $this->sendResponse('e', $e->getMessage());
            return;
        }

        $this->sendResponse('s','Successfully retrieved', $data);
    }
}
