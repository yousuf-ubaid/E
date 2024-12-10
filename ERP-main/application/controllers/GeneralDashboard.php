<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * GeneralDashboard
 */
class GeneralDashboard extends ERP_Controller
{
    /**
     * Construct
     */
    public function __construct(){
        parent::__construct();
        $this->load->service('GeneralDashboardService');
    }

    /**
     * Get datas for Company updates
     *
     * @return void
     */
    public function getCompanyUpdates()
    {
        $data["datas"] = $this->GeneralDashboardService->getAll();
        $this->load->view('system/erp_ajax_company_updates', $data);
    }

}