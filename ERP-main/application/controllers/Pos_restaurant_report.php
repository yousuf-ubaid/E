<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* -- =============================================
-- File Name : Pos_restaurant_report.php
-- Project Name : POS
-- Module Name : POS Restaurant Report Controller
-- Create date : 19 - April 2018
-- Description : SME POS Report .

--REVISION HISTORY
--Date: 19 - April 2018 : comment started



-- =============================================
 */


class Pos_restaurant_report extends ERP_Controller
{


    function __construct()
    {
        parent::__construct();
        $status = $this->session->has_userdata('status');
        if (!$status) {
            header('Location: ' . site_url('Login'));
            exit;
        }

        $this->load->library('pos_policy');
        $this->load->model('Pos_restaurant_model');
        $this->load->model('Inventory_modal');
        $this->load->model('Pos_restaurant_accounts');
        $this->load->model('Pos_restaurant_accounts_gl_fix');
        $this->load->helper('cookie');
        $this->load->helper('pos');
    }

    function load_collection_detail_report()
    {
        $tmpFromDate = $this->input->post('startdate');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d 00:00:00');
        }

        $filterTo = $this->input->post('enddate');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d 23:59:59');
        }

        if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
            $tmpCashier = join(",", $tmpCashierSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }

        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $collection_detail = $this->Pos_restaurant_model->get_paymentCollection($dateFrom, $dateTo, $Outlets, $cashier);
        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster($Outlets);
        $data['companyInfo'] = $companyInfo;
        $data['collection_detail'] = $collection_detail;

        $this->load->view('system/pos/reports/pos-collection-detail-report', $data);
    }

    function load_kotCountdown_report()
    {
        $this->form_validation->set_rules('cashier[]', 'cashier', 'trim|required');
        $this->form_validation->set_rules('filterFrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('filterTo', 'Date To', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo '<div class="alert alert-danger">' . $errors . '</div>';
        } else {
            $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
            $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
            $outlet = $this->input->post('outletID_f');
            $tmpCashierSource = $this->input->post('cashier');

            if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
                $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
            } else {
                $filterDate = date('Y-m-d 00:00:00');
            }

            if (!empty($tmpFilterDateTo)) {
                $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
            } else {
                $date2 = date('Y-m-d 23:59:59');
            }

            if (isset($tmpCashierSource) && !empty($tmpCashierSource)) {
                $tmpCashier = join(",", $tmpCashierSource);
                $cashier = $tmpCashier;
            } else {
                $cashier = null;
            }

            if (isset($outlet) && !empty($outlet)) {
                $tmpOutlet = join(",", $outlet);
                $Outlets = $tmpOutlet;
            } else {
                $Outlets = null;
            }

            $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
            //$profitability_report = $this->Pos_restaurant_model->get_kot_countdown_report($filterDate, $date2, $Outlets);

            $data['companyInfo'] = $companyInfo;
            //$data['profitability_report'] = $profitability_report;
            $data['profitability_report'] = $this->Pos_restaurant_model->get_kot_countdown_report($filterDate, $date2, $Outlets, $cashier);
            // $data['cashier'] = $tmpCashierSource;
            //  $data['cashierTmp'] = get_cashiers();
//($data['profitability_report']);
            $this->load->view('system/pos/reports/kot_countdown_report', $data);
        }
    }

    function bill_status()
    {
        $this->datatables->select('menuSalesID as menuSalesID,invoiceCode as invoiceCode,menuSalesDate as menuSalesDate,createdDateTime as createdDateTime,modifiedUserName as modifiedUserName,isUpdated as isUpdated', false)
            ->from('srp_erp_pos_menusalesmaster');
        //$this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"DO",DOAutoID)');
        echo $this->datatables->generate();
    }

    function shift_details(){
        $this->datatables->select('sd.shiftID as shiftID,sd.id_store as id_store,sd.empID as empID,ed.Ename1 as Ename1,wm.wareHouseDescription
        ,sd.startTime,sd.endTime,wm.wareHouseAutoID as wareHouseAutoID', false);
        $this->datatables->from('srp_erp_pos_shiftdetails sd');
        $this->datatables->join('srp_employeesdetails ed', 'ed.EIdNo = sd.empID');
        $this->datatables->join('srp_erp_warehousemaster wm', 'wm.wareHouseAutoID=sd.id_store');
        $this->datatables->where('sd.isFinanceClosed', 0);
        $this->datatables->where('sd.isClosed', 1);
        $this->datatables->add_column('action', '$1', 'finance_posting_button(shiftID,wareHouseAutoID)');
        echo $this->datatables->generate();
    }

}

