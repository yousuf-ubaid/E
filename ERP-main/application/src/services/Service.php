<?php

declare(strict_types=1);

namespace App\Src\Services;

/**
 * Class GeneralDashboardService
 * @property \CI_Controller $ci
 */
abstract class Service
{
    /**
     * Ci
     *
     * @var \CI_Controller
     */
    protected \CI_Controller $ci;

    /**
     * Constructor
     */
    public function __construct() {
        $this->ci =& get_instance();
    }
}