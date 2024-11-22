<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* -- =============================================
-- File Name : Pos_general_report.php
-- Project Name : POS General
-- Module Name : General POS Controller
-- Create date : 29 - June 2018
-- Description : SME POS System.

--REVISION HISTORY
--Date: 29 - June 2016 : comment started

-- =============================================
 */


class Pos_general_report extends ERP_Controller
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
        $this->load->model('Pos_general_report_model');
        $this->load->model('Inventory_modal');
        $this->load->model('Pos_restaurant_accounts');
        $this->load->model('Pos_restaurant_accounts_gl_fix');
        $this->load->helper('cookie');
        $this->load->helper('pos');
        $this->load->helper('pos_report');
    }

    function generate_pdf($html = '')
    {
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }


    function sales_report_pdf()
    {
        $result = $this->load_gpos_PaymentSalesReportAdmin(true, true);
        $this->generate_pdf($result);
    }

    function sales_report_pdf_settlement()
    {
        $result = $this->load_gpos_PaymentSalesReportAdmin_settlement(true, true);
        $this->generate_pdf($result);
    }

    function load_gpos_OuntletwisePaymentSalesReportAdmin($html = false, $pdf = false)
    {
        $data['pdf'] = $pdf;
//        $_POST['outletID'] = $this->input->post('outletID_f');
//        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');


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

//        if (isset($outletIDs) && !empty($outletIDs)) {
//            $outlet = join(",", $outletIDs);
//            $outlets = $outlet;
//        } else {
//            $outlets = null;
//        }

        $outlets = get_outletID();//modified for single outlet.

        $lessAmounts_discounts = $this->Pos_general_report_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_general_discounts = $this->Pos_general_report_model->get_report_salesReport_general_discount_admin($filterDate, $date2, $cashier, $outlets);
        $promotionDiscount = $this->Pos_general_report_model->get_report_salesReport_promo_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmountsAll = array_merge($lessAmounts_general_discounts, $lessAmounts_discounts, $promotionDiscount);

        $data['companyInfo'] = $this->Pos_general_report_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_general_report_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);
        $data['creditSales'] = $this->Pos_general_report_model->get_report_credit_sales_admin($filterDate, $date2, $cashier, $outlets);
        $data['customerTypeCount'] = $this->Pos_general_report_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets);
        $data['lessAmounts'] = $lessAmountsAll;
        $data['totalSales'] = $this->Pos_general_report_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalTaxes'] = $this->Pos_general_report_model->get_report_salesReport_totalTaxes_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalServiceCharge'] = $this->Pos_general_report_model->get_report_salesReport_ServiceCharge_admin($filterDate, $date2, $cashier, $outlets);
        $data['giftCardTopUp'] = $this->Pos_general_report_model->get_report_giftCardTopUp_admin($filterDate, $date2, $cashier, $outlets);
        $data['voidBills'] = $this->Pos_general_report_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalBills'] = $this->Pos_general_report_model->get_report_totalBills_admin($filterDate, $date2, $cashier, $outlets);
        /*$data['creditSales'] = $this->Pos_general_report_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets);*/
        $data['returnBills'] = $this->Pos_general_report_model->get_report_return_bills($filterDate, $date2, $cashier, $outlets);
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();
        /*echo '<pre>';
        print_r($data['creditSales']);
        exit;*/


        return $this->load->view('system/pos-general/reports/gpos-payment-sales-report-admin', $data, $html);
    }

    function load_gpos_PaymentSalesReportAdmin($html = false, $pdf = false)
    {
        $data['pdf'] = $pdf;
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');


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

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }


        $lessAmounts_discounts = $this->Pos_general_report_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_general_discounts = $this->Pos_general_report_model->get_report_salesReport_general_discount_admin($filterDate, $date2, $cashier, $outlets);
        $promotionDiscount = $this->Pos_general_report_model->get_report_salesReport_promo_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmountsAll = array_merge($lessAmounts_general_discounts, $lessAmounts_discounts, $promotionDiscount);

        $data['companyInfo'] = $this->Pos_general_report_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_general_report_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);
        $data['creditSales'] = $this->Pos_general_report_model->get_report_credit_sales_admin($filterDate, $date2, $cashier, $outlets);
        $data['customerTypeCount'] = $this->Pos_general_report_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets);
        $data['lessAmounts'] = $lessAmountsAll;
        $data['totalSales'] = $this->Pos_general_report_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalTaxes'] = $this->Pos_general_report_model->get_report_salesReport_totalTaxes_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalServiceCharge'] = $this->Pos_general_report_model->get_report_salesReport_ServiceCharge_admin($filterDate, $date2, $cashier, $outlets);
        $data['giftCardTopUp'] = $this->Pos_general_report_model->get_report_giftCardTopUp_admin($filterDate, $date2, $cashier, $outlets);
        $data['voidBills'] = $this->Pos_general_report_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalBills'] = $this->Pos_general_report_model->get_report_totalBills_admin($filterDate, $date2, $cashier, $outlets);
        $data['returnBills'] = $this->Pos_general_report_model->get_report_return_bills($filterDate, $date2, $cashier, $outlets);
        /*$data['creditSales'] = $this->Pos_general_report_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets);*/

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();
        /*echo '<pre>';
        print_r($data['creditSales']);
        exit;*/


        return $this->load->view('system/pos-general/reports/gpos-payment-sales-report-admin', $data, $html);
    }

    function load_gpos_PaymentSalesReportAdmin_settlement($html = false, $pdf = false)
    {
        $data['pdf'] = $pdf;
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');
        $data['companyInfo'] = $this->Pos_general_report_model->get_currentCompanyDetail();
        $data['settlementDetails_cash'] = $this->Pos_general_report_model->get_settlement_report_details(0);
        $data['settlementDetails_credit'] = $this->Pos_general_report_model->get_settlement_report_details(1);
        $data['salesmans'] = $this->Pos_general_report_model->get_settlement_salesman_details();
        $data['item_wise_details'] = $this->Pos_general_report_model->get_settlement_item_wise_details();
      
        return $this->load->view('system/pos-general/reports/gpos-load-settlement-report', $data, $html);
    }

    function load_gpos_settlement_report($html = false, $pdf = false)
    {
        $data['pdf'] = $pdf;
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');

        $data['companyInfo'] = $this->Pos_general_report_model->get_currentCompanyDetail();

        $data['settlementDetails_cash'] = $this->Pos_general_report_model->get_settlement_report_details(0);
        $data['settlementDetails_credit'] = $this->Pos_general_report_model->get_settlement_report_details(1);
        $data['salesmans'] = $this->Pos_general_report_model->get_settlement_salesman_details();
        $data['item_wise_details'] = $this->Pos_general_report_model->get_settlement_item_wise_details();

        return $this->load->view('system/pos-general/reports/gpos-load-settlement-report', $data, $html);
    }

    function get_gpos_outletwise_cashier()
    {
        $outletID = get_outletID();
        echo json_encode($this->Pos_general_report_model->get_gpos_outletwise_cashier($outletID));
    }

    function get_gpos_outlet_cashier()
    {
        echo json_encode($this->Pos_general_report_model->get_gpos_outlet_cashier());
    }

    function get_gpos_outlet_cashier_settlement()
    {
        echo json_encode($this->Pos_general_report_model->get_gpos_outlet_cashier_settlement());
    }

    function load_item_wise_sales_report_admin()
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
            $filterTo = $this->input->post('filterTo');
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

            $companyInfo = $this->Pos_general_report_model->get_currentCompanyDetail();
            $itemizedSalesReport = $this->Pos_general_report_model->get_item_wise_profitability_Report($filterDate, $date2, $Outlets, $cashier);


            $data['companyInfo'] = $companyInfo;
            $data['reportData'] = $itemizedSalesReport;
            $data['cashier'] = $tmpCashierSource;
            $data['cashierTmp'] = get_cashiers();


            $this->load->view('system/pos-general/reports/posg-item-wise-profitability-report-admin', $data);
        }
    }

    function load_gpos_outletwise_sales_report()
    {
        $_POST['outletID'] = get_outletID();
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');
        $customerAutoIDs = $this->input->post('customerAutoID');


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

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }

        if (isset($customerAutoIDs) && !empty($customerAutoIDs)) {
            $customer = join(",", $customerAutoIDs);
            $customerids = $customer;
        } else {
            $customerids = null;
        }


        $data['companyInfo'] = $this->Pos_general_report_model->get_currentCompanyDetail();
        $data['reportData'] = $this->Pos_general_report_model->get_gpos_detail_sales_report($filterDate, $date2, $cashier, $outlets, $customerids);
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $this->load->view('system/pos-general/reports/gpos-sales-detail-report', $data);
    }

    function load_gpos_detail_sales_report()
    {
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');
        $customerAutoIDs = $this->input->post('customerAutoID');
        $search = $this->input->post('search_filter');


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

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }

        if (isset($customerAutoIDs) && !empty($customerAutoIDs)) {
            // $customer = join(",", $customerAutoIDs);
            $customerids = $customerAutoIDs;
        } else {
            $customerids = null;
        }


        $data['companyInfo'] = $this->Pos_general_report_model->get_currentCompanyDetail();
        //$data['reportData'] = $this->Pos_general_report_model->get_gpos_detail_sales_report($filterDate, $date2, $cashier, $outlets, $customerids, $search);
        $data['refund_report'] = $this->Pos_general_report_model->get_gpos_detail_refund_sales_report($filterDate, $date2, $cashier, $outlets, $customerids);
        $data['paymentMethod'] = $this->Pos_general_report_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);
        $cash_collection = 0;
        foreach ($data['paymentMethod'] as $paymentMethod) {
            if (strtolower($paymentMethod['paymentDescription']) == 'cash') {
                $cash_collection += $paymentMethod['NetTotal'];
            }
        }
        $data['cash_collection'] = $cash_collection;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $this->load->view('system/pos-general/reports/gpos-sales-detail-report', $data);
    }

    function load_gpos_detail_void_sales_report()
    {
        $_POST['outletID'] = $this->input->post('void_outletID_f');
        $data['outletID'] = $this->input->post('void_outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('void_filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('void_filterTo')));
        $tmpCashierSource = $this->input->post('void_cashier');
        $outletIDs = $this->input->post('void_outletID_f');
        $customerAutoIDs = $this->input->post('void_customerAutoID');


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

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }

        if (isset($customerAutoIDs) && !empty($customerAutoIDs)) {
            // $customer = join(",", $customerAutoIDs);
            $customerids = $customerAutoIDs;
        } else {
            $customerids = null;
        }


        $data['companyInfo'] = $this->Pos_general_report_model->get_currentCompanyDetail();
        $data['reportData'] = $this->Pos_general_report_model->get_gpos_detail_void_sales_report($filterDate, $date2, $cashier, $outlets, $customerids);
        //$data['refund_report'] = $this->Pos_general_report_model->get_gpos_detail_refund_sales_report($filterDate, $date2, $cashier, $outlets, $customerids);
        $data['paymentMethod'] = $this->Pos_general_report_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);
        $cash_collection = 0;
        foreach ($data['paymentMethod'] as $paymentMethod) {
            if (strtolower($paymentMethod['paymentDescription']) == 'cash') {
                $cash_collection += $paymentMethod['NetTotal'];
            }
        }
        $data['cash_collection'] = $cash_collection;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $this->load->view('system/pos-general/reports/gpos-void-sales-detail-report', $data);
    }

    function load_return_pos_invoices()
    {
        echo json_encode($this->Pos_general_report_model->load_return_pos_invoices());
    }

    function load_outlet_item_wise_sales_report()
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
            //$outlet = $this->input->post('outletID_f');
            $filterTo = $this->input->post('filterTo');
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


            $Outlets = get_outletID();

            $companyInfo = $this->Pos_general_report_model->get_currentCompanyDetail();
            $itemizedSalesReport = $this->Pos_general_report_model->get_outlet_item_wise_profitability_Report($filterDate, $date2, $Outlets, $cashier);


            $data['companyInfo'] = $companyInfo;
            $data['reportData'] = $itemizedSalesReport;
            $data['cashier'] = $tmpCashierSource;
            $data['cashierTmp'] = get_cashiers();


            $this->load->view('system/pos-general/reports/posg-item-wise-profitability-report-admin', $data);
        }
    }


    public function fetch_till_management_report()
    {
        $f_outletID = $this->input->post('f_outletID');
        $startTime = trim(str_replace('/', '-', $this->input->post('startdate')));

        if (isset($startTime) && !empty($startTime)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($startTime));
        }

        $endTime = trim(str_replace('/', '-', $this->input->post('enddate')));

        if (isset($endTime) && !empty($endTime)) {
            $filterEndDate = date('Y-m-d H:i:s', strtotime($endTime));
        }

        $this->datatables->select('shift.shiftID as shiftID, startTime, endTime, shift.closingCashBalance_transaction as closingCashBalance_transaction,  shift.different_transaction as different_transaction, shift.cashSales as cashSales, shift.startingBalance_transaction as startingBalance_transaction, shift.endingBalance_transaction as endingBalance_transaction,concat(IF(wm.wareHouseDescription IS NULL,"",wm.wareHouseDescription) , " ",  IF(wm.wareHouseCode IS NULL,"",wm.wareHouseCode)  ) AS wareHouseDescription, wm.wareHouseLocation, c.counterCode, c.counterName, e.Ename2 as empName, shift.giftCardTopUp as  giftCardTopUp, IFNULL(tmpMSP.pAmount,0)  as pAmount, (  IFNULL(tmpMSP.pAmount, 0) + IFNULL(startingBalance_transaction,0)  + IFNULL(giftCardTopUp,0)   ) as tmp_startingBalance, ( (IFNULL(tmpMSP.pAmount, 0) + IFNULL(startingBalance_transaction,0)  + IFNULL(giftCardTopUp,0)  ) - IFNULL(shift.endingBalance_transaction,0)  ) as  difAmount ', false)
            ->from('srp_erp_pos_shiftdetails shift')
            ->join('srp_erp_warehousemaster wm', 'wm.wareHouseAutoID = shift.wareHouseID')
            ->join('srp_erp_pos_counters c', 'c.counterID = shift.counterID')
            ->join('srp_employeesdetails e', 'e.EIdNo = shift.empID')
            ->join('( SELECT sum( IFNULL(payment.amount,0) ) as pAmount, shiftID , srp_erp_pos_invoice.wareHouseAutoID FROM
                    srp_erp_pos_invoice 
                    LEFT JOIN ( SELECT SUM( IFNULL(amount ,0) ) AS amount, invoiceID FROM srp_erp_pos_invoicepayments WHERE paymentConfigMasterID =1 GROUP BY invoiceID ) AS payment ON payment.invoiceID = srp_erp_pos_invoice.invoiceID WHERE isVoid = 0 GROUP BY srp_erp_pos_invoice.shiftID  , srp_erp_pos_invoice.wareHouseAutoID ) AS tmpMSP', 'tmpMSP.shiftID = shift.shiftID AND tmpMSP.wareHouseAutoID = shift.wareHouseID', 'left')
            ->add_column('startingBal', '$1', 'till_report_numberFormat(startingBalance_transaction)')
            ->add_column('EndingBal', '$1', 'till_report_numberFormat(endingBalance_transaction)')
            ->add_column('cashSalesCol', '$1', 'till_report_numberFormat(pAmount)')
            ->add_column('different_transaction', '$1', 'till_report_numberFormat_dif(difAmount)')
            ->add_column('closingCashBalance', '$1', 'till_report_numberFormat(tmp_startingBalance)')
            ->add_column('tmp_giftCardTopUp', '$1', 'till_report_numberFormat(giftCardTopUp)');
        //->add_column('wareHouseColumn', '$1  -  $2', 'wareHouseCode,wareHouseLocation')
        $this->datatables->where('shift.companyID', current_companyID());
        $this->datatables->where('shift.isClosed', 1);

        if (!empty($f_outletID)) {
            $this->datatables->where('shift.wareHouseID', $f_outletID);
        }
        if (!empty($filterDate)) {
            $this->datatables->where('shift.startTime>=', $filterDate);
        }
        if (!empty($filterEndDate)) {
            $this->datatables->where('shift.startTime<=', $filterEndDate);
        }

        $r = $this->datatables->generate();
        //echo $this->db->last_query();

        echo $r;


    }

    function load_till_management_report()
    {
        $f_outletID = $this->input->post('outletID_f');
        $startTime = trim(str_replace('/', '-', $this->input->post('startdate')));

        if (isset($startTime) && !empty($startTime)) {
            $filterDate = ' AND shift.startTime >="' . date('Y-m-d H:i:s', strtotime($startTime)) . '"';
        } else {
            $filterDate = '';
        }

        $endTime = trim(str_replace('/', '-', $this->input->post('enddate')));

        if (isset($endTime) && !empty($endTime)) {
            $filterEndDate = ' AND shift.startTime <="' . date('Y-m-d H:i:s', strtotime($endTime)) . '"';
        } else {
            $filterEndDate = '';
        }
        if (!empty($f_outletID)) {
            $f_outlet = 'AND shift.wareHouseID =' . $f_outletID . '';
        } else {
            $f_outlet = '';
        }

        $data['extra'] = $this->db->query('SELECT
	shift.shiftID AS shiftID,
	startTime,
	endTime,
	shift.closingCashBalance_transaction AS closingCashBalance_transaction,
	shift.different_transaction AS different_transaction,
	shift.cashSales AS cashSales,
	shift.startingBalance_transaction AS startingBalance_transaction,
	shift.endingBalance_transaction AS endingBalance_transaction,
	wm.wareHouseDescription,
	wm.wareHouseLocation,
	c.counterCode,
	c.counterName,
	e.Ename2 AS empName
FROM
	`srp_erp_pos_shiftdetails` `shift`
JOIN `srp_erp_warehousemaster` `wm` ON `wm`.`wareHouseAutoID` = `shift`.`wareHouseID`
JOIN `srp_erp_pos_counters` `c` ON `c`.`counterID` = `shift`.`counterID`
JOIN `srp_employeesdetails` `e` ON `e`.`EIdNo` = `shift`.`empID`
WHERE
	`shift`.`companyID` = ' . current_companyID() . '
AND `shift`.`isClosed` = 1
' . $f_outlet . '
' . $filterDate . '
' . $filterEndDate . ' ')->result_array();

        $html = $this->load->view('system/pos/reports/till_management_report_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 1);
        }
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
        //Pos_general_report_model
        $companyInfo = $this->Pos_general_report_model->get_currentCompanyDetail();
        $collection_detail = $this->Pos_general_report_model->get_paymentCollection($dateFrom, $dateTo, $Outlets, $cashier);
        $data['paymentglConfigMaster'] = $this->Pos_general_report_model->get_srp_erp_pos_paymentglconfigmaster($Outlets);
        $data['companyInfo'] = $companyInfo;
        $data['collection_detail'] = $collection_detail;

        $this->load->view('system/pos-general/reports/gpos-collection-detail-report', $data);
    }

    function LoadDiscountReport()
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

        $companyInfo = $this->Pos_general_report_model->get_currentCompanyDetail();
        $deliveryPersonReport = $this->Pos_general_report_model->get_discountReport($dateFrom, $dateTo, $Outlets, $cashier);

        $data['cashierTmp'] = get_cashiers();
        $data['cashier'] = $tmpCashierSource;
        $data['companyInfo'] = $companyInfo;
        $data['deliveryPersonReport'] = $deliveryPersonReport;

        $this->load->view('system/pos-general/reports/gpos-discount-report', $data);
    }


    function LoadFastMovingReport()
    {
        $tmpFromDate = $this->input->post('startdate');
        $currency = $this->input->post('currency');


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


        $companyInfo = $this->Pos_general_report_model->get_currentCompanyDetail();
        $fastmovingReport = $this->Pos_general_report_model->get_item_fast_moving_report($dateFrom, $dateTo, $currency);

        $data['companyInfo'] = $companyInfo;
        $data['fastMovingRpt'] = $fastmovingReport;

        $this->load->view('system/pos-general/reports/gpos-fast-moving-report', $data);
    }

    function load_statusbased_customer_gpos()
    {
        $customer_arr = array();
        $activeStatus = $this->input->post("activeStatus");
        $tab = $this->input->post("tab");

        $status_filter = '';
        $companyID = current_companyID();
        if (!empty($activeStatus)) {
            if($activeStatus==1){
                $status_filter = "AND isActive = 1 ";
            }elseif($activeStatus==2){
                $status_filter = "AND isActive = 0 ";
            }else{
                $status_filter = '';
            }
        }
        $companyID = current_companyID();
        $type = $this->input->post("type");

        if($type == 1){
            
            $customer= $this->db->query("SELECT srp_erp_customermaster.customerAutoID,srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,srp_erp_customermaster.customerCountry,srp_erp_customermaster.companyCode,srp_erp_customermaster.customerTelephone
                                              FROM `srp_erp_customermaster` 
                                              INNER JOIN srp_erp_pos_invoice ON srp_erp_pos_invoice.customerID = srp_erp_customermaster.customerAutoID
                                              WHERE srp_erp_customermaster.`companyID` = $companyID  $status_filter")->result_array();
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customer_arr[trim($row['customerAutoID'] ?? '')] = trim($row['customerName'] ?? '') . ' | ' . (trim($row['customerTelephone'] ?? ''));
                    
                }
            }
        }
        if($tab==1){
            echo form_dropdown('customerAutoID[]', $customer_arr, '', 'multiple id="customerAutoID"  class="form-control input-sm"'); 
        }else{
            echo form_dropdown('void_customerAutoID[]', $customer_arr, '', 'multiple id="void_customerAutoID"  class="form-control input-sm"'); 
        }
    }
  

}

