<?php

declare(strict_types=1);

namespace App\Src\Services;

/**
 * Class GeneralDashboardService
 */
final class GeneralDashboardService extends Service
{
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->ci->load->model('Company_updates_model');
        $this->ci->load->library('S3');
    }

    /**
     * Get all company updates
     * 
     * @return array<int, mixed>
     */
    public function getAll(): array
    {
        return $this->ci->Company_updates_model->getAll();
    }

}