<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* -- =============================================
-- File Name : Pos_restaurant.php
-- Project Name : POS
-- Module Name : POS Restaurant Controller
-- Create date : 25 - October 2016
-- Description : SME POS System.

--REVISION HISTORY
--Date: 25 - Oct 2016 : comment started
--Date: 14 - NOV 2016 : POS Footer functions
--Date: 15 - NOV 2016 : POS Payment Receipt
--Date: 16 - NOV 2016 : Hold invoice modal
--Date: 31 - NOV 2019 : SME-1422 Policy based (KOT hold reference adding) needed in pos tablet terminal.

-- =============================================
 */


class Pos_restaurant extends ERP_Controller
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
        $this->load->model('Api_wowfood_model');

        $this->load->helper('cookie');
        $this->load->helper('pos');
    }

    function index()
    {
        $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();
        $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

        $isHaveNotClosedSession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_restaurant_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }

        /** Get Warehouse Menu Items */
        $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

        /** Get warehouse Category */
        $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

        $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();
        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invCodeDet['refCode'];
        $data['isHadSession'] = $isHadSession;
        $data['menuItems'] = $output;
        $data['menuCategory'] = $output2;
        $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'counterDet' => $counterDet,
        );
        $data['common_data'] = $this->common_data;
        $defaultCustomerType = defaultCustomerType();

        $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
        $data['warehouseID'] = $warehouseID;
        $data['tables_list'] = $this->Pos_restaurant_model->get_tableList();


        $this->load->view('system/pos/pos_restaurant', $data);
    }

    public function pos_terminal_1()
    {

        $b = get_isLocalPosEnabled();
        if ($b['error'] == 1) {
            $data['title'] = EMAIL_SYS_NAME . ' Login to Local POS';
            $data['main_content'] = 'system/pos/messages/local-pos-implemented-error-msg';
            $data['extra'] = $b;
            echo $this->load->view('include/template', $data, true);
            exit;
        }

        $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();
        $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

        $isHaveNotClosedSession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_restaurant_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }

        /** Get Warehouse Menu Items */
        $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

        /** Get warehouse Category */
        $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

        /** Get warehouse Sub Category */
        $output3 = $this->Pos_restaurant_model->get_warehouseSubCategory($warehouseID);

        $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();
        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invCodeDet['refCode'];
        $data['isHadSession'] = $isHadSession;
        $data['menuItems'] = $output;
        $data['menuCategory'] = $output2;
        $data['menuSubCategory'] = $output3;
        $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'counterDet' => $counterDet,
        );
        $data['common_data'] = $this->common_data;
        $defaultCustomerType = defaultCustomerType();

        $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
        $data['warehouseID'] = $warehouseID;
        $isPriceRequired = $this->pos_policy->isPriceRequired();
        $data['isPriceRequired'] = $isPriceRequired;
        $data['tables_list'] = $this->Pos_restaurant_model->get_tableList();
        $data['sampleBillPolicy'] = $this->pos_policy->isSampleBillRequired();
        $data['isHidePrintPreview'] = $this->pos_policy->isHidePrintPreview();
        $data['isOutletTaxEnabled'] = $this->pos_policy->isOutletTaxEnabled();
        $data['isPartialPaymentEnabled'] = $this->pos_policy->isPartialPaymentEnabled();
        $data['isSplitBillEnabled'] = $this->pos_policy->isSplitBillEnabled();
        $data['pinBasedAccess'] = $this->pos_policy->pinBasedAccess();
        $data['waiters'] = $this->Pos_restaurant_model->getWaiters($warehouseID);
        $data['dineInId'] = $this->Pos_restaurant_model->dineInId($warehouseID);
        $data['showStaffButton'] = $this->pos_policy->showStaffButton();
        $data['isPayButtonEnabled'] = $this->Pos_restaurant_model->isPayButtonEnabled();

        $data['isPowerButtonEnabled'] = $this->Pos_restaurant_model->isPowerButtonEnabled();
        $data['isOpenButtonEnabled'] = $this->Pos_restaurant_model->isOpenButtonEnabled();
        $data['isPrintSampleButtonEnabled'] = $this->Pos_restaurant_model->isPrintSampleButtonEnabled();
        $data['isHoldButtonEnabled'] = $this->Pos_restaurant_model->isHoldButtonEnabled();
        $data['isCancelButtonEnabled'] = $this->Pos_restaurant_model->isCancelButtonEnabled();
        $data['isKitchenButtonEnabled'] = $this->Pos_restaurant_model->isKitchenButtonEnabled();
        $data['isGiftCardButtonEnabled'] = $this->Pos_restaurant_model->isGiftCardButtonEnabled();
        $data['isClosedBillsButtonEnabled'] = $this->Pos_restaurant_model->isClosedBillsButtonEnabled();
        $data['isScreenLockButtonEnabled'] = $this->Pos_restaurant_model->isScreenLockButtonEnabled();
        /** load template */
        $templateLink = get_pos_templateView();
        $this->load->view($templateLink, $data);
    }

    public function pos_terminal_mobile()
    {

        $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();
        $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

        $isHaveNotClosedSession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_restaurant_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }

        /** Get Warehouse Menu Items */
        $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

        /** Get warehouse Category */
        $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

        /** Get warehouse Sub Category */
        $output3 = $this->Pos_restaurant_model->get_warehouseSubCategory($warehouseID);

        $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();
        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invCodeDet['refCode'];
        $data['isHadSession'] = $isHadSession;
        $data['menuItems'] = $output;
        $data['menuCategory'] = $output2;
        $data['menuSubCategory'] = $output3;
        $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'counterDet' => $counterDet,
        );
        $data['common_data'] = $this->common_data;
        $defaultCustomerType = defaultCustomerType();

        $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
        $data['warehouseID'] = $warehouseID;
        $isPriceRequired = $this->pos_policy->isPriceRequired();
        $data['isPriceRequired'] = $isPriceRequired;
        $data['tables_list'] = $this->Pos_restaurant_model->get_tableList();
        $data['sampleBillPolicy'] = $this->pos_policy->isSampleBillRequired();
        $data['pinBasedAccess'] = $this->pos_policy->pinBasedAccess();
        $data['waiters'] = $this->Pos_restaurant_model->getWaiters($warehouseID);
        $data['dineInId'] = $this->Pos_restaurant_model->dineInId($warehouseID);
        $data['showStaffButton'] = $this->pos_policy->showStaffButton();
        $data['isPayButtonEnabled'] = $this->Pos_restaurant_model->isPayButtonEnabled();

        $data['isPowerButtonEnabled'] = $this->Pos_restaurant_model->isPowerButtonEnabled();
        $data['isOpenButtonEnabled'] = $this->Pos_restaurant_model->isOpenButtonEnabled();
        $data['isPrintSampleButtonEnabled'] = $this->Pos_restaurant_model->isPrintSampleButtonEnabled();
        $data['isHoldButtonEnabled'] = $this->Pos_restaurant_model->isHoldButtonEnabled();
        $data['isCancelButtonEnabled'] = $this->Pos_restaurant_model->isCancelButtonEnabled();
        $data['isKitchenButtonEnabled'] = $this->Pos_restaurant_model->isKitchenButtonEnabled();
        $data['isGiftCardButtonEnabled'] = $this->Pos_restaurant_model->isGiftCardButtonEnabled();
        $data['isClosedBillsButtonEnabled'] = $this->Pos_restaurant_model->isClosedBillsButtonEnabled();
        $data['isScreenLockButtonEnabled'] = $this->Pos_restaurant_model->isScreenLockButtonEnabled();

        $this->load->view('system/pos/pos-restaurant-mobile', $data);
    }


    public function pos_terminal_2()
    {
        $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();

        $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

        $isHaveNotClosedSession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_restaurant_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }

        /** Get Warehouse Menu Items */
        $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

        /** Get warehouse Category */
        $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

        $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();
        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invCodeDet['refCode'];
        $data['isHadSession'] = $isHadSession;
        $data['menuItems'] = $output;
        $data['menuCategory'] = $output2;
        $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
            'counterDet' => $counterDet,
        );
        $data['common_data'] = $this->common_data;
        $defaultCustomerType = defaultCustomerType();

        $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
        $data['warehouseID'] = $warehouseID;
        $isPriceRequired = $this->pos_policy->isPriceRequired();
        $data['isPriceRequired'] = $isPriceRequired;

        $this->load->view('system/pos/pos_restaurant-view2', $data);
        //$this->load->view('system/pos/pos_restaurant', $data);
    }

    public function load_currencyDenominationPage()
    {
        $currencyCode = $this->common_data['company_data']['company_default_currency'];
        //$wareHouseID = $this->common_data['ware_houseID'];
        $wareHouseID = get_outletID();
        $data['session_data'] = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $data['denomination'] = $this->Pos_restaurant_model->currencyDenominations($currencyCode);
        $data['counters'] = $this->Pos_restaurant_model->load_wareHouseCounters($wareHouseID);
        $data['cardCollection'] = $this->Pos_restaurant_model->get_giftCardTopUpCashCollection();
        $data['dPlace'] = $this->common_data['company_data']['company_default_decimal'];
        $data['isRestaurant'] = true;
        $data['code'] = 1;

        $shiftID = $data['session_data']['shiftID'];

        $posSessionClosePayment = $this->pos_policy->posSessionClosePayment();
        if ($posSessionClosePayment) {
            //getting card payment details
            if (!empty($shiftID)) {
                $data['card'] = $this->Pos_restaurant_model->get_session_close_payment_details($shiftID, ["4", "3", "6"]);
                $data['credit'] = $this->Pos_restaurant_model->get_session_close_payment_details($shiftID, ["2", "5", "7", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46", "47", "48", "49", "51", "52", "53"]);
            }
        }


        $this->load->view('system/pos/ajax/currency_denomination_view', $data);
    }

    public function load_currencyDenominationPage_mobile()
    {
        $currencyCode = $this->common_data['company_data']['company_default_currency'];
        //$wareHouseID = $this->common_data['ware_houseID'];
        $wareHouseID = get_outletID();
        $data['session_data'] = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $data['denomination'] = $this->Pos_restaurant_model->currencyDenominations($currencyCode);
        $data['counters'] = $this->Pos_restaurant_model->load_wareHouseCounters($wareHouseID);
        $data['cardCollection'] = $this->Pos_restaurant_model->get_giftCardTopUpCashCollection();
        $data['dPlace'] = $this->common_data['company_data']['company_default_decimal'];
        $data['isRestaurant'] = true;
        $data['mobile'] = true;
        $data['code'] = 1;
        $data['isRestaurant_mobile'] = true;


        /*echo 'wareHouseID: '.$wareHouseID;
        print_r($data['counters']);
        exit;*/

        $this->load->view('system/pos/ajax/currency_denomination_view', $data);
    }

    public function shift_create()
    {
        $this->form_validation->set_rules('startingBalance', 'Starting Balance', 'trim|required');
        $this->form_validation->set_rules('counterID', 'Counter ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->shift_create());
        }
    }

    public function shift_close()
    /** Didn't use it here */
    {
        /*$this->form_validation->set_rules('startingBalance', 'Ending Balance', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->shift_close());
        }*/

        $code = $this->input->post('code');
        $this->form_validation->set_rules('startingBalance', 'Ending Balance', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $counterData = get_counterData();
            $result = $this->Pos_model->shift_close();
            if ($result) {
                $tmpResult = array('s', 'Shift Closed Successfully', 'code' => $code, 'counterData' => $counterData); /*code is to identify where it come from.*/
            } else {
                $tmpResult = array('e', 'Error In Shift Close');
            }
            echo json_encode($tmpResult);
        }
    }


    public function item_search()
    {
        echo json_encode($this->Pos_restaurant_model->item_search());
    }

    public function invoice_create()
    {

        $this->form_validation->set_rules('itemID[]', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemUOM[]', 'Item UOM', 'trim|required');
        $this->form_validation->set_rules('itemQty[]', 'Item QTY', 'trim|required');
        $this->form_validation->set_rules('itemPrice[]', 'Item Price', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer ID', 'trim|required');
        $this->form_validation->set_rules('_trCurrency', 'Transaction Currency', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $customerID = $this->input->post('customerID');
            $cashAmount = $this->input->post('_cashAmount');
            $chequeAmount = $this->input->post('_chequeAmount');
            $cardAmount = $this->input->post('_cardAmount');
            $paidAmount = ($cashAmount + $chequeAmount + $cardAmount);
            $netTotVal = $this->input->post('netTotVal');
            $balanceAmount = ($netTotVal - $paidAmount);

            if ($balanceAmount > 0 && $customerID == 0) {
                echo json_encode(array('e', 'Credit not allowed for Cash Customer'));
            } else {
                echo json_encode($this->Pos_restaurant_model->invoice_create());
            }
        }
    }

    public function invoice_hold()
    {
        $this->form_validation->set_rules('itemID[]', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemUOM[]', 'Item UOM', 'trim|required');
        $this->form_validation->set_rules('itemQty[]', 'Item QTY', 'trim|required');
        $this->form_validation->set_rules('itemPrice[]', 'Item Price', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->invoice_hold());
        }
    }

    public function invoice_cardDetail()
    {
        $this->form_validation->set_rules('invID', 'Invoice ID', 'trim|required|numeric');
        $this->form_validation->set_rules('referenceNO', 'Reference NO', 'trim|required');
        $this->form_validation->set_rules('cardNumber', 'cardNumber', 'trim|numeric');
        $this->form_validation->set_rules('bank', 'bank', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->invoice_cardDetail());
        }
    }

    public function customer_search()
    {
        echo json_encode($this->Pos_restaurant_model->customer_search());
    }

    public function recall_invoice()
    {
        echo json_encode($this->Pos_restaurant_model->recall_invoice());
    }

    public function recall_hold_invoice()
    {
        echo json_encode($this->Pos_restaurant_model->recall_hold_invoice());
    }

    public function invoice_search()
    {
        echo json_encode($this->Pos_restaurant_model->invoice_search());
    }

    public function load_holdInv()
    {
        echo json_encode($this->Pos_restaurant_model->load_holdInv());
    }

    public function new_counter()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('counterCode', 'Counter Code', 'trim|required');
        $this->form_validation->set_rules('counterName', 'Counter Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->new_counter());
        }
    }

    public function fetch_counters()
    {

        $this->datatables->select('counterID, counterCode, counterName, wareHouseID, wareHouseCode, wareHouseLocation', false)
            ->from('srp_erp_pos_counters t1')
            ->join('srp_erp_warehousemaster t2', 't2.wareHouseAutoID=t1.wareHouseID')
            ->add_column('action', '$1', 'actionCounter_fn(counterID, counterCode, counterName, wareHouseID)')
            ->add_column('wareHouse', '$1  -  $2', 'wareHouseCode,wareHouseLocation')
            ->where('t1.isActive', 1)
            ->where('t1.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function delete_counterDetails()
    {
        $this->form_validation->set_rules('counterID', 'Counter ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->delete_counterDetails());
        }
    }

    public function update_counterDetails()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('counterCode', 'Counter Code', 'trim|required');
        $this->form_validation->set_rules('counterName', 'Counter Name', 'trim|required');
        $this->form_validation->set_rules('updateID', 'Counter ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->update_counterDetails());
        }
    }

    public function load_counters()
    {
        $wareHouse = $this->input->post('wareHouseID');
        $thisWareHouseCounters = $this->Pos_restaurant_model->load_wareHouseCounters($wareHouse);
        $thisWareHouseUsers = $this->Pos_restaurant_model->load_wareHouseUsers($wareHouse);

        echo json_encode(
            array('counter' => $thisWareHouseCounters, 'users' => $thisWareHouseUsers)
        );
    }

    public function fetch_user_counters()
    {
        $this->datatables->select("t3.counterID, counterCode, counterName, t1.wareHouseID, wareHouseCode, wareHouseLocation,
             (SELECT CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) FROM srp_employeesdetails WHERE EIdNo=t1.userID) eName", false)
            ->from('srp_erp_warehouse_users t1')
            ->join('srp_erp_warehousemaster t2', 't1.wareHouseID=t2.wareHouseAutoID')
            ->join('srp_erp_pos_counters t3', 't1.counterID=t3.counterID')
            ->add_column('action', '$1', 'actionCounter_fn(counterID, counterCode, counterName, wareHouseID)')
            ->add_column('wareHouse', '$1  -  $2', 'wareHouseCode,wareHouseLocation')
            ->where('t3.isActive', 1)
            ->where('t1.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function fetch_ware_house_user()
    {

        $this->datatables->select("CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, ''))
              AS empName, userID, ECode, autoID, t1.wareHouseID, wareHouseCode, wareHouseLocation", false)
            ->from('srp_erp_warehouse_users t1')
            ->join('srp_employeesdetails t2', 't1.userID=t2.EIdNo')
            ->join('srp_erp_warehousemaster t3', 't1.wareHouseID=t3.wareHouseAutoID')
            ->add_column('wareHouse', '$1  -  $2', 'wareHouseCode,wareHouseLocation')
            ->add_column('action', '$1', 'actionWarehouseUser_fn(autoID, userID, empName, wareHouseID, wareHouseLocation)')
            ->where('t1.companyID', $this->common_data['company_data']['company_id'])
            ->where('t1.isActive', 1);
        echo $this->datatables->generate();
    }

    public function emp_search()
    {
        echo json_encode($this->Pos_restaurant_model->emp_search());
    }

    public function add_ware_house_user()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('employeeID', 'Employee ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->add_ware_house_user());
        }
    }

    public function update_ware_house_user()
    {
        $this->form_validation->set_rules('updateID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('employeeID', 'Employee ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->update_ware_house_user());
        }
    }

    public function delete_ware_house_user()
    {
        $this->form_validation->set_rules('autoID', 'Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->delete_ware_house_user());
        }
    }

    /*Promotion setups*/
    public function fetch_promotions()
    {
        $this->datatables->select('proMaster.promotionID, proType.Description AS typeDes, proMaster.Description AS masterDes,
               dateFrom, dateTo, isApplicableForAllItem, isActive,', false)
            ->from('srp_erp_pos_promotionsetupmaster proMaster')
            ->join('srp_erp_pos_promotiontypes proType', 'proMaster.promotionTypeID=proType.promotionTypeID')
            ->add_column('wareHouse', '$1', 'promoWarehouses(promotionID)')
            ->add_column('applicableItems', '$1', 'applicableItems(isApplicableForAllItem)')
            ->add_column('action', '$1', 'actionPromotion_fn(promotionID, masterDes)')
            //->where('proMaster.isActive', 1)
            ->where('proMaster.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function new_promotion()
    {
        $this->form_validation->set_rules('promoType', 'Promotion Type', 'trim|required');
        $this->form_validation->set_rules('warehouses[]', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('promotionDescr', 'Promotion Description', 'trim|required');
        $this->form_validation->set_rules('fromDate', 'From Date', 'trim|required|date');
        $this->form_validation->set_rules('endDate', 'End Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $fromDate = $this->input->post('fromDate');
            $endDate = $this->input->post('endDate');

            if ($fromDate <= $endDate) {
                echo json_encode($this->Pos_restaurant_model->new_promotion());
            } else {
                echo json_encode(array('e', 'End date should be greater than or equal to from date'));
            }
        }
    }

    public function get_promotionMasterDet()
    {
        $promo_ID = $this->input->post('promo_ID');
        echo json_encode($this->Pos_restaurant_model->get_promotionMasterDet($promo_ID));
    }

    public function load_promotion_template()
    {
        /*
         *
         * 1 => On Sale Disc
         * 2 => On Sale Coupon
         * 3 => Item Free Issue
         *
         * */

        $promoID = $this->input->post('promo_ID');
        $promoType = $this->input->post('promoType');

        switch ($promoType) {
            case  1:
                $template = 'on_sale_discount_template';
                break;

            case  2:
                $template = 'on_sale_coupon_template';
                break;

            case  3:
                $template = 'item_free_issue_template';
                break;
        }

        $data['detail'] = $this->Pos_restaurant_model->get_promotionDet($promoID);
        $this->load->view('system/pos/ajax/' . $template, $data);
    }

    public function update_promotion()
    {
        $this->form_validation->set_rules('updateID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('promoType', 'Promotion Type', 'trim|required');
        $this->form_validation->set_rules('warehouses[]', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('promotionDescr', 'Promotion Description', 'trim|required');
        $this->form_validation->set_rules('fromDate', 'From Date', 'trim|required|date');
        $this->form_validation->set_rules('endDate', 'End Date', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $fromDate = $this->input->post('fromDate');
            $endDate = $this->input->post('endDate');

            if ($fromDate <= $endDate) {
                echo json_encode($this->Pos_restaurant_model->update_promotion());
            } else {
                echo json_encode(array('e', 'End date should be greater than or equal to from date'));
            }
        }
    }

    public function delete_promotion()
    {
        $this->form_validation->set_rules('promoID', 'Promotion ID', 'trim|required|date');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->delete_promotion());
        }
    }

    public function load_applicableItems()
    {
        $promo_ID = $this->input->post('promo_ID');
        $data['w_items'] = $this->Pos_restaurant_model->load_applicableItems($promo_ID);

        $this->load->view('system/pos/ajax/promotion_items_load', $data);
    }

    public function fetch_allItems()
    {
        /*$wareHouseID = $this->common_data['ware_houseID'];

        $this->datatables->select('t1.itemAutoID, t1.itemSystemCode, t1.itemDescription')
            ->from('srp_erp_warehouseitems t1')
            ->join('srp_erp_itemmaster t2', 't1.itemAutoID = t2.itemAutoID')
            ->add_column('action', '$1' , 'item_tb_checkbox(itemAutoID, itemSystemCode, itemDescription)')
            ->where('t2.companyID', current_companyID())
            ->where('wareHouseAutoID', $wareHouseID);*/

        $this->datatables->select('itemAutoID, itemSystemCode, itemDescription')
            ->from('srp_erp_itemmaster')
            ->add_column('action', '$1', 'item_tb_checkbox(itemAutoID, itemSystemCode, itemDescription)')
            ->where('companyID', current_companyID());

        echo $this->datatables->generate();
    }

    function selectedItemCheck()
    {
        $selectedItems = $this->input->post('selectedItems[]');

        if (count($selectedItems) == 0) {
            $this->form_validation->set_message('selectedItemCheck', 'Please add at least one item');
            return false;
        } else {
            return true;
        }
    }

    public function save_promotionItems()
    {
        $this->form_validation->set_rules('promoID', 'Promotion ID', 'trim|required');
        $this->form_validation->set_rules('selectedItems[]', 'Items', 'callback_selectedItemCheck');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->save_promotionItems());
        }
    }

    /*End of Promotion setups*/

    function get_wareHouse()
    {
        $outletID = get_outletID();
        $this->db->select('wHouse.wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation, segmentID, segmentCode')
            ->from('srp_erp_warehousemaster wHouse')
            ->join('srp_erp_pos_segmentconfig conf', 'conf.wareHouseAutoID=wHouse.wareHouseAutoID', 'left')
            ->where('wHouse.wareHouseAutoID', $outletID);
        return $this->db->get()->row_array();
    }


    function LoadToInvoice()
    {
        $companyID = current_companyID();
        $wareHouseID = $this->common_data['ware_houseID'];
        $d = get_company_currency_decimal();
        $time = $this->input->post('currentTime');
        $curDateTmp = date('Y-m-d') . $time;
        $curDate = format_date_mysql_datetime($curDateTmp);
        $selectedWaiter = $this->input->post('selectedWaiter');
        $id = $this->input->post('id');
        $itemQty = $this->input->post('itemQty');

        $customerType = $this->input->post('customerType');
        if (!empty($id)) {


            $output = $this->Pos_restaurant_model->get_warehouseMenu_specific($id);

            $isPack = $output['isPack'];
            if (!empty($output)) {
                $Items = $this->db->query("SELECT
	srp_erp_pos_menudetails.itemAutoID    
FROM
	srp_erp_pos_warehousemenumaster
	LEFT JOIN srp_erp_pos_menumaster ON srp_erp_pos_warehousemenumaster.menuMasterID = srp_erp_pos_menumaster.menuMasterID
	LEFT JOIN srp_erp_pos_menudetails ON srp_erp_pos_menumaster.menuMasterID = srp_erp_pos_menudetails.menuMasterID 
WHERE
	warehouseMenuID = $id AND itemAutoID is not null")->result_array();
                //$Items = $this->db->query($q)->result_array();
                $cnt = count($Items);
                //var_dump($Items);exit;
                $isMinusBocked = getPolicyValues('MQT', 'All');

                if (!empty($Items) && $isMinusBocked == 1) {
                    $Items = array_column($Items, 'itemAutoID');
                    $itemIDs = join(",", $Items);

                    $itm = $this->db->query("
	SELECT
	t2.itemAutoID,
	t2.itemSystemCode,
	t2.itemDescription,
	IFNULL( SUM( IFNULL( t1.transactionQTY, 0 ) / IFNULL( t1.convertionRate, 0 ) ), 0 ) AS currentStock,
	t2.mainCategory AS mainCategory 
FROM
	srp_erp_itemmaster t2
	LEFT JOIN ( SELECT * FROM srp_erp_itemledger WHERE warehouseAutoID = '{$wareHouseID}' ) t1 ON t1.itemAutoID = t2.itemAutoID 
WHERE
	t2.companyID = '{$companyID}' 
	AND t2.itemAutoID IN ( $itemIDs ) 
	AND t2.isActive = 1 
GROUP BY
	t2.itemAutoID
	")->result_array();

                    $bal = 0;
                    $insuf = array();
                    foreach ($itm as $vl) {

                        $empID = current_userID();
                        $itemAutoID = $vl['itemAutoID'];
                        $usageQry = $this->db->query("select 
                        srp_erp_pos_menusalesitemdetails.itemAutoID,
	sum( srp_erp_pos_menusalesitemdetails.qty * menuSalesQty) AS currentUsage,
	srp_erp_pos_menudetails.qty
from srp_erp_pos_menusalesitemdetails
join srp_erp_pos_menusalesmaster on srp_erp_pos_menusalesmaster.menuSalesID=srp_erp_pos_menusalesitemdetails.menuSalesID
join srp_erp_pos_shiftdetails on srp_erp_pos_shiftdetails.shiftID=srp_erp_pos_menusalesmaster.shiftID
and srp_erp_pos_shiftdetails.isClosed=0
and srp_erp_pos_menusalesitemdetails.itemAutoID=$itemAutoID
AND srp_erp_pos_menusalesitemdetails.wareHouseAutoID='{$wareHouseID}'
LEFT JOIN srp_erp_pos_menudetails ON srp_erp_pos_menudetails.menuMasterID = srp_erp_pos_menusalesitemdetails.menuID
GROUP BY srp_erp_pos_menusalesitemdetails.itemAutoID");
                        //var_dump($this->db->last_query());exit;
                        //where srp_erp_pos_shiftdetails.empID!=$empID
                        if ($usageQry->num_rows() > 0) {
                            $currentUsage = $usageQry->row('currentUsage');
                            $itemUsageForThisMenu =  (float)$usageQry->row('qty');
                        } else {
                            $itemUsageForThisMenu  = (float)($this->db->query("SELECT                            
                            srp_erp_pos_menudetails.qty
                        FROM
                            srp_erp_pos_warehousemenumaster
                            LEFT JOIN srp_erp_pos_menumaster ON srp_erp_pos_warehousemenumaster.menuMasterID = srp_erp_pos_menumaster.menuMasterID
                            LEFT JOIN srp_erp_pos_menudetails ON srp_erp_pos_menumaster.menuMasterID = srp_erp_pos_menudetails.menuMasterID 
                        WHERE
                            warehouseMenuID = $id")->row('qty'));
                            $currentUsage = 0;
                        }
                        //                        var_dump($vl['currentStock']);
                        //                        var_dump('-/-');
                        //                        var_dump($currentUsage);exit;

                        $bal = $vl['currentStock'] - ($currentUsage + ($itemUsageForThisMenu * $itemQty));
                        $vl['currentUsage'] = $currentUsage;
                        $vl['quantityUsedInThisBill'] = $itemUsageForThisMenu * $itemQty;
                        $vl['balance'] = $bal;
                        if ($bal < $itemUsageForThisMenu && ($vl['mainCategory'] != 'Service' && $vl['mainCategory'] != 'Non Inventory')) {
                            array_push($insuf, $vl);
                            /*echo json_encode(array('error' => 1, 'message' => 'Selected menu has insufficient items'));
                            exit;*/
                        }
                    }
                    if (!empty($insuf)) {
                        echo json_encode(array('error' => 2, 'message' => 'Selected menu has insufficient items', 'insuf' => $insuf));
                        exit;
                    }
                }


                $code = 0;
                $output['warehouseMenuID'] = str_pad($output['warehouseMenuID'], 4, "0", STR_PAD_LEFT);
                $output['key'] = $output['warehouseMenuID'];

                $templateID = $this->input->post('pos_templateID');
                $sellingPrice = getSellingPricePolicy($templateID, $output['pricewithoutTax'], $output['totalTaxAmount'], $output['totalServiceCharge']);
                $output['sellingPrice'] = number_format($sellingPrice, $d, '.', '');

                $get_shift = $this->Pos_restaurant_model->get_srp_erp_pos_shiftdetails_employee();

                $invoiceID_tmp = isPos_invoiceSessionExist();

                if ($invoiceID_tmp) {
                    /** -------------------------------  INVOICE EXIST ------------------------------- */

                    /* Insert Menu */
                    $data_item['menuSalesID'] = $invoiceID_tmp;
                    $data_item['menuID'] = $output['menuMasterID'];
                    $data_item['menuCategoryID'] = $output['menuCategoryID'];
                    $data_item['wareHouseAutoID'] = get_outletID();

                    $data_item['warehouseMenuID'] = $output['warehouseMenuID'];
                    $data_item['warehouseMenuCategoryID'] = $output['warehouseMenuCategoryID'];
                    $data_item['defaultUOM'] = 'each';
                    $data_item['unitOfMeasure'] = 'each';
                    $data_item['conversionRateUOM'] = 1;

                    $data_item['menuCost'] = $output['menuCost'];
                    $data_item['menuSalesPrice'] = $output['pricewithoutTax'];
                    $data_item['qty'] = 1;
                    $data_item['discountPer'] = 0;
                    $data_item['discountAmount'] = 0;

                    /** KOT Kitchen order ticket detail */
                    $parentMenuSalesItemID = $this->input->post('parentMenuSalesItemID');
                    $data_item['kotID'] = $parentMenuSalesItemID > 0 ? 0 : $this->input->post('kotID');
                    $data_item['kitchenNote'] = trim($this->input->post('kitchenNote') ?? '');
                    $data_item['isOrderPending'] = -1;

                    /** Add-on */
                    $data_item['parentMenuSalesItemID'] = $parentMenuSalesItemID;

                    /** get kitchen current status */
                    $isOrderPending = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster_specific($invoiceID_tmp, 'isOrderPending', true);
                    if ($isOrderPending == 1) {
                        /** already pressed the send KOT button */
                        /** new item will not be taken in the KOT until user click on the kot button*/
                        $data_item['KOTAlarm'] = -1;
                    }


                    /** Tax Calculation */
                    $data_item['TAXpercentage'] = $output['TAXpercentage'];
                    $data_item['TAXAmount'] = NULL; //$output['TAXpercentage'] > 0 ? $output['sellingPrice'] * ($output['TAXpercentage'] / 100) : null;
                    $data_item['taxMasterID'] = NULL; //$output['taxMasterID'];

                    $transCurrencyID = getCurrencyID_byCurrencyCode($get_shift['transactionCurrency']);
                    $data_item['transactionCurrencyID'] = $transCurrencyID;
                    $data_item['transactionCurrency'] = $get_shift['transactionCurrency'];
                    $data_item['transactionAmount'] = $output['sellingPrice'];
                    $data_item['transactionCurrencyDecimalPlaces'] = $get_shift['transactionCurrencyDecimalPlaces'];
                    $data_item['transactionExchangeRate'] = $get_shift['transactionExchangeRate'];

                    $reportingCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
                    $conversion = currency_conversionID($transCurrencyID, $reportingCurrencyID, $output['sellingPrice']);

                    $data_item['companyReportingCurrency'] = $reportingCurrencyID;
                    $data_item['companyReportingAmount'] = $conversion['convertedAmount'];
                    $data_item['companyReportingCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];
                    $data_item['companyReportingExchangeRate'] = $conversion['conversion'];

                    $defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
                    $conversion = currency_conversionID($transCurrencyID, $defaultCurrencyID, $output['sellingPrice']);


                    $data_item['companyLocalCurrencyID'] = $defaultCurrencyID;
                    $data_item['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $data_item['companyLocalAmount'] = $conversion['convertedAmount'];
                    $data_item['companyLocalExchangeRate'] = $conversion['conversion'];
                    $data_item['companyLocalCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];


                    $data_item['companyID'] = current_companyID();
                    $data_item['companyCode'] = current_companyCode();

                    $data_item['revenueGLAutoID'] = $output['revenueGLAutoID'];

                    $data_item['createdUserGroup'] = user_group();
                    $data_item['createdPCID'] = current_pc();
                    $data_item['createdUserID'] = current_userID();
                    $data_item['createdDateTime'] = $curDate;
                    $data_item['createdUserName'] = current_user();
                    $data_item['modifiedPCID'] = null;
                    $data_item['modifiedUserID'] = null;
                    $data_item['modifiedDateTime'] = null;
                    $data_item['modifiedUserName'] = null;
                    $data_item['timestamp'] = format_date_mysql_datetime();
                    $data_item['id_store'] = current_warehouseID();

                    /*Insert Menu */
                    $code = $this->Pos_restaurant_model->insert_srp_erp_pos_menusalesitems($data_item);
                    if ($code == false) {
                        $menusalesitems_query_status['menusalesitems'] = false;
                    } else {
                        $menusalesitems_query_status['menusalesitems'] = true;
                        $menusalesitems_query_status['updateNetTotalForInvoice'] = $this->updateNetTotalForInvoice($invoiceID_tmp);
                        if ($menusalesitems_query_status['updateNetTotalForInvoice'] == true && $menusalesitems_query_status['menusalesitems'] == true) {
                            $menusalesitems_query_status_record = array(
                                "isUpdated" => 1
                            );
                            $this->db->where('menuSalesItemID', $code);
                            $this->db->update('srp_erp_pos_menusalesitems', $menusalesitems_query_status_record);
                        } else {
                            $menusalesitems_query_status_record = array(
                                "isUpdated" => 0
                            );
                            $this->db->where('menuSalesItemID', $code);
                            $this->db->update('srp_erp_pos_menusalesitems', $menusalesitems_query_status_record);
                        }
                    }


                    /** KOT order is still in progress */
                    $dataKOT['isOrderCompleted'] = 0;

                    $this->db->select('menuSalesID');
                    $this->db->from('srp_erp_pos_menusalesitems');
                    $this->db->where('isOrderInProgress', 1);
                    $this->db->where('menuSalesID', $invoiceID_tmp);
                    $result = $this->db->get()->row_array();

                    if ($result) {
                        $dataKOT['isOrderInProgress'] = 1;
                    } else {
                        $dataKOT['isOrderInProgress'] = 0;
                    }

                    $pinBasedAccess = $this->pos_policy->pinBasedAccess();
                    if ($pinBasedAccess) {
                        $dataKOT['waiterID'] = $selectedWaiter;
                    }
                    $this->db->where('menuSalesID', $invoiceID_tmp);
                    $this->db->update('srp_erp_pos_menusalesmaster', $dataKOT);
                    /** enf of KOT */
                } else {
                    /** -------------------------------  NEW INVOICE  ------------------------------- */

                    if (!empty($get_shift)) {
                        $warehouseDetail = $this->get_wareHouse();


                        /** -------------------------------  Create New Invoice ------------------------------- */
                        $tmpCustomerType = $this->input->post('customerType');

                        //                        if (!empty($tmpCustomerType)) {
                        //                            $CustomerType = $tmpCustomerType;
                        //                        } else {
                        //                            /***** setup default order type *** */
                        //                            if ($this->input->post('tabOrder') == 1) {
                        //                                $CustomerType = get_defaultOderType();
                        //                            } else {
                        //                                $CustomerType = null;
                        //                            }
                        //                        }
                        $CustomerType = !empty($tmpCustomerType) ? $tmpCustomerType : 1;
                        $SN = generate_pos_invoice_no();
                        $data['customerTypeID'] = $CustomerType;
                        $data['documentSystemCode'] = '';
                        $data['documentCode'] = '';
                        $data['serialNo'] = $SN;
                        $data['invoiceSequenceNo'] = $SN;
                        $data['invoiceCode'] = generate_pos_invoice_code();
                        $data['customerID'] = '';
                        $data['customerCode'] = '';
                        $data['shiftID'] = $get_shift['shiftID'];

                        if ($this->input->post('tabOrder') == 1) {
                            $data['counterID'] = null;
                            $data['isHold'] = -1;
                            $data['tabUserID'] = current_userID();
                        } else {
                            $data['counterID'] = $get_shift['counterID'];
                        }

                        $data['menuSalesDate'] = format_date_mysql_datetime();
                        $data['holdDatetime'] = format_date_mysql_datetime();
                        $data['companyID'] = current_companyID();
                        $data['companyCode'] = current_companyCode();

                        $data['subTotal'] = '';
                        $data['discountPer'] = '';
                        $data['discountAmount'] = '';
                        $data['netTotal'] = '';

                        $data['wareHouseAutoID'] = $get_shift['wareHouseID'];

                        $data['segmentID'] = $warehouseDetail['segmentID'];
                        $data['segmentCode'] = $warehouseDetail['segmentCode'];

                        $data['salesDay'] = date('l');
                        $data['salesDayNum'] = date('w');


                        $tr_currency = $this->common_data['company_data']['company_default_currency'];
                        $transConversion = currency_conversion($tr_currency, $tr_currency);

                        $data['transactionCurrencyID'] = $transConversion['currencyID'];
                        $data['transactionCurrency'] = $transConversion['CurrencyCode'];
                        $data['transactionExchangeRate'] = $transConversion['conversion'];
                        $data['transactionCurrencyDecimalPlaces '] = $transConversion['DecimalPlaces'];

                        $defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
                        $defaultConversion = currency_conversionID($transConversion['currencyID'], $defaultCurrencyID);

                        $data['companyLocalCurrencyID'] = $defaultCurrencyID;
                        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                        $data['companyLocalExchangeRate'] = $defaultConversion['conversion'];
                        $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];


                        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
                        $transConversion = currency_conversionID($transConversion['currencyID'], $repCurrencyID);

                        $data['companyReportingCurrencyID'] = $repCurrencyID;
                        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                        $data['companyReportingExchangeRate'] = $transConversion['conversion'];
                        $data['companyReportingCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_reporting_decimal'];


                        /*update the transaction currency detail for later use */
                        $tr_currency = $this->common_data['company_data']['company_default_currency'];
                        $customerCurrencyConversion = currency_conversion($tr_currency, $tr_currency);

                        $data['customerCurrencyID'] = $customerCurrencyConversion['currencyID'];
                        $data['customerCurrency'] = $customerCurrencyConversion['CurrencyCode'];
                        $data['customerCurrencyExchangeRate'] = $customerCurrencyConversion['conversion'];
                        $data['customerCurrencyDecimalPlaces'] = $customerCurrencyConversion['DecimalPlaces'];


                        /*Audit Data */
                        $data['createdUserGroup'] = current_user_group();
                        $data['createdPCID'] = current_pc();
                        $data['createdUserID'] = current_userID();
                        $data['createdUserName'] = current_user();
                        $data['createdDateTime'] = format_date_mysql_datetime();
                        $data['modifiedPCID'] = '';
                        $data['modifiedUserID'] = '';
                        $data['modifiedUserName'] = '';
                        $data['modifiedDateTime'] = '';
                        $data['timestamp'] = format_date_mysql_datetime();
                        $data['id_store'] = $this->config->item('id_store');
                        $data['isFromTablet'] = $this->input->post('isFromTablet');
                        $data['paymentMethod'] = 1;
                        $pinBasedAccess = $this->pos_policy->pinBasedAccess();
                        if ($pinBasedAccess) {
                            $data['waiterID'] = $selectedWaiter;
                        }

                        $this->db->trans_start();
                        // $invoiceID = $this->Pos_restaurant_model->insert_srp_erp_pos_menusalesmaster($data);
                        $data['wareHouseAutoID'] = current_warehouseID();
                        $data['id_store'] = current_warehouseID();
                        $result = $this->db->insert('srp_erp_pos_menusalesmaster', $data);
                        if ($result) {
                            $invoiceID = $this->db->insert_id();                            
                        } else {
                            $invoiceID = false;
                        }
                        $this->db->trans_complete();
                        if ($this->db->trans_status() == false) {
                            $this->db->trans_rollback();
                            $invoiceCode = $data['invoiceCode'];
                            $invCodeQ = $this->db->query("select invoiceCode from srp_erp_pos_menusalesmaster where invoiceCode='$invoiceCode'");
                            if ($invCodeQ->num_rows() > 0) {
                                echo json_encode(array('error' => 1, 'message' => 'Duplicate invoice code'));
                                exit;
                            } else {
                                echo json_encode(array('error' => 1, 'message' => 'An error has occurred please contact your support team'));
                                exit;
                            }
                        } else {
                            $this->db->trans_commit();
                            if ($invoiceID) {
                                set_session_invoiceID($invoiceID);

                                /* Insert Menu */
                                $data_item['menuSalesID'] = $invoiceID;
                                $data_item['wareHouseAutoID'] = get_outletID();
                                $data_item['menuID'] = $output['menuMasterID'];
                                $data_item['menuCategoryID'] = $output['menuCategoryID'];
                                $data_item['warehouseMenuID'] = $output['warehouseMenuID'];
                                $data_item['warehouseMenuCategoryID'] = $output['warehouseMenuCategoryID'];
                                $data_item['defaultUOM'] = 'each';
                                $data_item['unitOfMeasure'] = 'each';
                                $data_item['conversionRateUOM'] = 1;

                                $data_item['menuCost'] = $output['menuCost'];
                                $data_item['menuSalesPrice'] = $output['pricewithoutTax'];
                                $data_item['qty'] = 1;
                                $data_item['discountPer'] = 0;
                                $data_item['discountAmount'] = 0;

                                /** KOT Kitchen order ticket detail */
                                $parentMenuSalesItemID = $this->input->post('parentMenuSalesItemID');
                                $data_item['kotID'] = $parentMenuSalesItemID > 0 ? 0 : $this->input->post('kotID');
                                $data_item['kitchenNote'] = trim($this->input->post('kitchenNote') ?? '');
                                $data_item['isOrderPending'] = -1;

                                /** Add-on */
                                $data_item['parentMenuSalesItemID'] = $parentMenuSalesItemID;


                                /** Tax Calculation */
                                $data_item['TAXpercentage'] = $output['TAXpercentage'];
                                $data_item['TAXAmount'] = NULL; //$output['TAXpercentage'] > 0 ? $output['sellingPrice'] * ($output['TAXpercentage'] / 100) : null;
                                $data_item['taxMasterID'] = NULL; // $output['taxMasterID'];


                                $transCurrencyID = getCurrencyID_byCurrencyCode($get_shift['transactionCurrency']);
                                $data_item['transactionCurrencyID'] = $transCurrencyID;
                                $data_item['transactionCurrency'] = $get_shift['transactionCurrency'];
                                $data_item['transactionAmount'] = $output['sellingPrice'];
                                $data_item['transactionCurrencyDecimalPlaces'] = $get_shift['transactionCurrencyDecimalPlaces'];
                                $data_item['transactionExchangeRate'] = $get_shift['transactionExchangeRate'];

                                $reportingCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
                                $conversion = currency_conversionID($transCurrencyID, $reportingCurrencyID, $output['sellingPrice']);

                                $data_item['companyReportingCurrency'] = $reportingCurrencyID;
                                $data_item['companyReportingAmount'] = $conversion['convertedAmount'];
                                $data_item['companyReportingCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];
                                $data_item['companyReportingExchangeRate'] = $conversion['conversion'];

                                $defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
                                $conversion = currency_conversionID($transCurrencyID, $defaultCurrencyID, $output['sellingPrice']);

                                $data_item['companyLocalCurrencyID'] = $defaultCurrencyID;
                                $data_item['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                                $data_item['companyLocalAmount'] = $conversion['convertedAmount'];
                                $data_item['companyLocalExchangeRate'] = $conversion['conversion'];
                                $data_item['companyLocalCurrencyDecimalPlaces'] = $conversion['DecimalPlaces'];
                                $data_item['companyID'] = current_companyID();
                                $data_item['companyCode'] = current_companyCode();
                                $data_item['revenueGLAutoID'] = $output['revenueGLAutoID'];
                                $data_item['createdUserGroup'] = user_group();
                                $data_item['createdPCID'] = current_pc();
                                $data_item['createdUserID'] = current_userID();
                                $data_item['createdDateTime'] = format_date_mysql_datetime();
                                $data_item['createdUserName'] = current_user();
                                $data_item['modifiedPCID'] = null;
                                $data_item['modifiedUserID'] = null;
                                $data_item['modifiedDateTime'] = null;
                                $data_item['modifiedUserName'] = null;
                                $data_item['timestamp'] = format_date_mysql_datetime();
                                $data_item['id_store'] = current_warehouseID();


                                /*Insert Menu */
                                $code = $this->Pos_restaurant_model->insert_srp_erp_pos_menusalesitems($data_item);
                                if ($code == false) {
                                    $menusalesitems_query_status['menusalesitems'] = false;
                                } else {
                                    $menusalesitems_query_status['menusalesitems'] = true;
                                    $menusalesitems_query_status['updateNetTotalForInvoice'] = $this->updateNetTotalForInvoice($invoiceID);
                                    if ($menusalesitems_query_status['updateNetTotalForInvoice'] == true && $menusalesitems_query_status['menusalesitems'] == true) {
                                        $menusalesitems_query_status_record = array(
                                            "isUpdated" => 1
                                        );
                                        $this->db->where('menuSalesItemID', $code);
                                        $this->db->update('srp_erp_pos_menusalesitems', $menusalesitems_query_status_record);
                                    } else {
                                        $menusalesitems_query_status_record = array(
                                            "isUpdated" => 0
                                        );
                                        $this->db->where('menuSalesItemID', $code);
                                        $this->db->update('srp_erp_pos_menusalesitems', $menusalesitems_query_status_record);
                                    }
                                }
                            } else {
                                echo json_encode(array('error' => 1, 'message' => 'An error has occurred please contact your support team'));
                                exit;
                            }
                        }
                    } else {
                        echo json_encode(array('error' => 1, 'message' => 'shift not created'));
                        exit;
                    }
                }

                $tmpInvoiceID = isset($invoiceID) && !empty($invoiceID) ? padZeros_saleInvoiceID($invoiceID) : padZeros_saleInvoiceID(isPos_invoiceSessionExist());
                $tmpInvoiceID_code_tmp = isset($invoiceID) && !empty($invoiceID) ? $invoiceID : isPos_invoiceSessionExist();
                $outletID = get_outletID();
                $tmpInvoiceID_code = get_pos_invoice_code($tmpInvoiceID_code_tmp, $outletID);


                $result = array_merge(array('error' => 0, 'message' => 'done'), $output, array('tmpInvoiceID' => $tmpInvoiceID, 'tmpInvoiceID_code' => $tmpInvoiceID_code, 'code' => $code, 'isPack' => $isPack));
                echo json_encode($result);
            } else {
                echo json_encode(array('error' => 0, 'message' => 'Menu not found'));
            }
        } else {
            echo json_encode(array('error' => 0, 'message' => 'ID not found'));
        }
    }

    function checkPosSession()
    {
        $result = isPos_invoiceSessionExist();
        if ($result) {
            $get_invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($result);
            if ($get_invoice['customerTypeID'] == 1) {
                $outletID = get_outletID();
                $get_invoice['customerTypeID'] = $this->Pos_restaurant_model->dineInId($outletID);
            }
            if (!empty($get_invoice)) {

                $isDineIn = 0;
                if (trim(strtolower($get_invoice['customerDescription'])) == 'dine-in' || trim(strtolower($get_invoice['customerDescription'])) == 'eat-in') {
                    $isDineIn = 1;
                }

                $q = "SELECT SUM(amount) as tmpAmount FROM srp_erp_pos_menusalespayments WHERE menuSalesID = $result";
                $advancePayment = $this->db->query($q)->row('tmpAmount');
                $result = array_merge($get_invoice, array('error' => 0, 'message' => 'Invoice Exist', 'code' => $result, 'master' => $get_invoice, 'dine_in' => $isDineIn, 'advancePayment' => $advancePayment));
                echo json_encode($result);
            } else {
                echo json_encode(array('error' => 1, 'message' => 'This invoice is already closed', 'code' => 0));
            }
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Invoice not exist!', 'code' => 0));
        }
    }

    function Load_pos_holdInvoiceData()
    {
        $d = get_company_currency_decimal();
        $template = $this->input->post('template');
        $invoiceID = $this->input->post('invoiceID');
        $getMenuInvoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $output = '';
        if (!empty($getMenuInvoice)) {


            foreach ($getMenuInvoice as $data) {
                $data['warehouseMenuID'] = str_pad($data['warehouseMenuID'], 4, "0", STR_PAD_LEFT);

                $output .= '<div class="row itemList" id="item_row_' . $data['menuSalesItemID'] . '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;" onclick="selectMenuItem(this)">';

                if ($template == 2) {
                    $hide = ' hide ';
                    $col = '4';
                } else {
                    $hide = '';
                    $col = 3;
                }

                $output .= '<div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1 ' . $hide . '"><img src="' . $data['menuImage'] . '" style="max-height: 40px;" alt=""></div>';

                if ($template == 2) {
                    $output .= '<div class="col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . '</div> ';
                } else {
                    $output .= '<div class="col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . ' <br>[' . $data['warehouseMenuID'] . ']</div> ';
                }

                $templateID = get_pos_templateID();
                $sellingPrice = getSellingPricePolicy($templateID, $data['pricewithoutTax'], $data['totalTaxAmount'], $data['totalServiceCharge']);
                $data['sellingPrice'] = number_format($sellingPrice, $d, '.', '');


                $output .= '<div class="col-md-8">
                            <div class="receiptPadding">
                                <input type="text" data-wh_menuID="' . $data['warehouseMenuID'] . '" onfocus="keepTheExistingQuantity.call(this)" onkeyup="updateQtyWithAuth(' . $data['menuSalesItemID'] . ',\'onkeyup\',' . $data['warehouseMenuID'] . ')" onchange="updateQtyWithAuth(' . $data['menuSalesItemID'] . ',\'onchange\',' . $data['warehouseMenuID'] . ')" value="' . $data['qty'] . '" class="display_qty menuItem_input numberFloat" id="qty_' . $data['menuSalesItemID'] . '" name="qty[' . $data['menuSalesItemID'] . ']"  />
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_itemCost menuItemTxt">' . $data['sellingPrice'] . '</span> <!-- @rate -->
                                <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' . $data['menuSalesItemID'] . ']" value="' . $data['sellingPrice'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
<input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
                            <input type="hidden"  name="frm_isTaxEnabled[' . $data['menuSalesItemID'] . ']" value="' . $data['isTaxEnabled'] . '"/>
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_total menuItemTxt">0</span>  <!-- total -->
                            </div>

                            <div class="receiptPadding ' . $hide . '">
                                <input style="width:60%;" onchange="calculateFooter(\'P\')" name="discountPercentage[' . $data['menuSalesItemID'] . ']"  maxlength="3" type="text" value="' . $data['discountPer'] . '"
                                       class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % -->
                            </div>
                            <div class="receiptPadding ' . $hide . '">
                                <input style="width:90%;" onchange="calculateFooter(\'A\')" name="discountAmount[' . $data['menuSalesItemID'] . ']" type="text" value="' . $data['discountAmount'] . '"
                                       class="menu_discount_amount menu_qty menuItem_input numberFloat"><!-- disc. amount -->
                            </div>
                            <div class="receiptPadding">
                                <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt"> ' . $data['sellingPrice'] . '</div> <!-- net total -->
                                <div onclick="deleteDiv(' . $data['menuSalesItemID'] . ')" data-placement="bottom" rel="tooltip" title="Delete"
                                     style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 0px;" class="pull-right">';
                if ($template == 2) {
                    $output .= '<button type="button" class="btn btn-default btn-sm itemList-delBtn"><i class="fa fa-close closeColor"></i></button> </div>';
                } else {
                    $output .= '<i class="fa fa-close closeColor"></i></button>';
                }

                $output .= '</div>
                        </div>';
                $output .= '</div>';
            }
        }
        echo $output;
    }

    function Load_pos_holdInvoiceData_tab()
    {

        $d = get_company_currency_decimal();
        $template = $this->input->post('template');
        $invoiceID = $this->input->post('invoiceID');
        $getMenuInvoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $output = '';
        if (!empty($getMenuInvoice)) {

            foreach ($getMenuInvoice as $data) {
                $data['warehouseMenuID'] = str_pad($data['warehouseMenuID'], 4, "0", STR_PAD_LEFT);

                $output .= '<div class="row itemList" id="item_row_' . $data['menuSalesItemID'] . '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;" onclick="selectMenuItem(this)">';

                if ($template == 2) {
                    $hide = ' ';
                    $col = '4';
                } else {
                    $hide = '';
                    $col = 4;
                }

                $output .= '<div class=" hide"><img src="' . $data['menuImage'] . '" style="max-height: 40px;" alt=""></div>';

                if ($template == 2) {
                    $output .= '<div class="col-xs-4 col-sm-4 col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . '</div> ';
                } else {
                    $output .= '<div class="col-xs-4 col-sm-4 col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . ' <br>[' . $data['warehouseMenuID'] . ']</div> ';
                }

                $templateID = get_pos_templateID();
                $sellingPrice = getSellingPricePolicy($templateID, $data['pricewithoutTax'], $data['totalTaxAmount'], $data['totalServiceCharge']);
                $data['sellingPrice'] = number_format($sellingPrice, $d, '.', '');


                $output .= '<div class="col-md-8 col-sm-8 col-xs-8">
                            <div class="receiptPadding">
                                <input type="text" data-wh_menuID="' . $data['warehouseMenuID'] . '" onfocus="keepTheExistingQuantity.call(this)" onkeyup="updateQtyWithAuth(' . $data['menuSalesItemID'] . ',\'onkeyup\',' . $data['warehouseMenuID'] . ')" onchange="updateQtyWithAuth(' . $data['menuSalesItemID'] . ',\'onchange\',' . $data['warehouseMenuID'] . ')" value="' . $data['qty'] . '" class="display_qty menuItem_input numberFloat" id="qty_' . $data['menuSalesItemID'] . '" name="qty[' . $data['menuSalesItemID'] . ']"  />
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_itemCost menuItemTxt">' . $data['sellingPrice'] . '</span> <!-- @rate -->
                                <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' . $data['menuSalesItemID'] . ']" value="' . $data['sellingPrice'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount numberFloat pricewithoutTaxDiscount" name="pricewithoutTaxDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount totalMenuTaxAmountDiscount numberFloat" name="totalMenuTaxAmountDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount numberFloat totalMenuServiceChargeDiscount" name="totalMenuServiceChargeDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
                                <input type="hidden"  name="frm_isTaxEnabled[' . $data['menuSalesItemID'] . ']" value="' . $data['isTaxEnabled'] . '"/>
                                <input type="hidden"  class="isSamplePrintedFlag" id="isSamplePrinted_' . $data['menuSalesItemID'] . '" value="' . $data['isSamplePrinted'] . '"/>
                            </div>
                            <div class="receiptPadding hide">
                                <span class="menu_total menuItemTxt">0</span>  <!-- total -->
                            </div>

                            <div class="receiptPadding hide">
                                <input style="width:60%;" onchange="calculateFooter(\'P\')" name="discountPercentage[' . $data['menuSalesItemID'] . ']"  maxlength="3" type="text" value="' . $data['discountPer'] . '"
                                       class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % -->
                            </div>
                            <div class="receiptPadding hide">
                                <input style="width:90%;" onchange="calculateFooter(\'A\')" name="discountAmount[' . $data['menuSalesItemID'] . ']" type="text" value="' . $data['discountAmount'] . '"
                                       class="menu_discount_amount menu_qty menuItem_input numberFloat"><!-- disc. amount -->
                            </div>
                            <div class="receiptPadding">
                                <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt"> ' . $data['sellingPrice'] . '</div> <!-- net total -->
                                <div onclick="deleteDiv(' . $data['menuSalesItemID'] . ')" data-placement="bottom" rel="tooltip" title="Delete"
                                     style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 0px;" class="pull-right hide">';
                if ($template == 2) {
                    $output .= '<button type="button" class="btn btn-default btn-sm itemList-delBtn "><i class="fa fa-close closeColor"></i></button> </div>';
                } else {
                    $output .= '<i class="fa fa-close closeColor"></i></button>';
                }

                $output .= '</div>
                        </div>';
                $output .= '</div>';
            }
        }
        echo $output;
    }

    function Load_pos_holdInvoiceData_withDiscount()
    {

        $d = get_company_currency_decimal();
        $outletID = $this->input->post('outletID');
        $template = $this->input->post('template');
        $invoiceID = $this->input->post('invoiceID');
        $getMenuInvoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_forHoldBill($invoiceID, $outletID);
        $output = '';
        if (!empty($getMenuInvoice)) {


            foreach ($getMenuInvoice as $data) {
                $data['warehouseMenuID'] = str_pad($data['warehouseMenuID'], 4, "0", STR_PAD_LEFT);

                $output .= '<div class="row itemList" id="item_row_' . $data['menuSalesItemID'] . '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;" onclick="selectMenuItem(this)">';

                if ($template == 2) {
                    $hide = '  ';
                    $col = '3';
                } else {
                    $hide = '';
                    $col = 3;
                }

                $output .= '<div class="col-md-1 hidden-xs hidden-sm menuItem_pos_col_1 hide"><img src="' . $data['menuImage'] . '" style="max-height: 40px;" alt=""></div>';

                if ($template == 2) {
                    $output .= '<div class="col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . '</div> ';
                } else {
                    $output .= '<div class="col-md-' . $col . ' menuItem_pos_col_5">' . $data['menuMasterDescription'] . ' <br>[' . $data['warehouseMenuID'] . ']</div> ';
                }

                $templateID = get_pos_templateID();
                $sellingPrice = getSellingPricePolicy($templateID, $data['pricewithoutTax'], $data['totalTaxAmount'], $data['totalServiceCharge']);
                $data['sellingPrice'] = number_format($sellingPrice, $d, '.', '');
                $discountPolicy = show_item_level_discount();
                $discountPolicyClass = $discountPolicy ? '' : 'hide';

                $output .= '<div class="col-md-9">
                            <div class="receiptPadding">
                                <input type="text" data-wh_menuID="' . $data['warehouseMenuID'] . '" onfocus="keepTheExistingQuantity.call(this)" onkeyup="updateQtyWithAuth(' . $data['menuSalesItemID'] . ',\'onkeyup\',' . $data['warehouseMenuID'] . ')" onchange="updateQtyWithAuth(' . $data['menuSalesItemID'] . ',\'onchange\',' . $data['warehouseMenuID'] . ')" value="' . $data['qty'] . '" class="display_qty menuItem_input numberFloat" id="qty_' . $data['menuSalesItemID'] . '" name="qty[' . $data['menuSalesItemID'] . ']"  />
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_itemCost menuItemTxt">' . $data['sellingPrice'] . '</span> <!-- @rate -->
                                <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' . $data['menuSalesItemID'] . ']" value="' . $data['sellingPrice'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount numberFloat pricewithoutTaxDiscount" name="pricewithoutTaxDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
                                <input type="hidden"  class="menuItemTxt_inputDiscount totalMenuTaxAmountDiscount numberFloat" name="totalMenuTaxAmountDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
<input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
<input type="hidden"  class="menuItemTxt_inputDiscount numberFloat totalMenuServiceChargeDiscount" name="totalMenuServiceChargeDiscount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
                            <input type="hidden"  name="frm_isTaxEnabled[' . $data['menuSalesItemID'] . ']" value="' . $data['isTaxEnabled'] . '"/>
                            <input type="hidden"  class="isSamplePrintedFlag" id="isSamplePrinted_' . $data['menuSalesItemID'] . '" value="' . $data['isSamplePrinted'] . '"/>
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_total menuItemTxt">0</span>  <!-- total -->
                            </div>

                            <div class="receiptPadding ' . $discountPolicyClass . '">
                                <input style="width:60%;" onfocus="keepTheExistingDiscountPercentage.call(this)" id="discountPercentage_' . $data['menuSalesItemID'] . '" onchange="item_wise_discount(this,\'P\',' . $data['menuSalesItemID'] . ')" name="discountPercentage[' . $data['menuSalesItemID'] . ']"  maxlength="3" type="text" value="' . $data['discountPer'] . '"
                                       class="menu_discount_percentage menu_qty menuItem_input numberFloat numpad"> <!-- disc. % -->
                            </div>
                            <div class="receiptPadding ' . $discountPolicyClass . '">
                                <input style="width:90%;" onfocus="keepTheExistingDiscountAmount.call(this)" id="discountAmount_' . $data['menuSalesItemID'] . '" onchange="item_wise_discount(this,\'A\',' . $data['menuSalesItemID'] . ')" name="discountAmount[' . $data['menuSalesItemID'] . ']" type="text" value="' . $data['discountAmount'] . '"
                                       class="menu_discount_amount menu_qty menuItem_input numberFloat numpad"><!-- disc. amount -->
                            </div>
                            <div class="receiptPadding">
                                <div style="width:55px; text-align: right;" class="itemCostNet menuItemTxt set-delete"> ' . $data['sellingPrice'] . '</div> <!-- net total -->
                                <div onclick="deleteLineItem(25,\'' . $data['menuSalesItemID'] . '\')" data-placement="bottom" rel="tooltip" title="Delete"
                                     style="cursor:pointer; width: 12px; margin-top: -20px;     margin-right: 0px;" class="pull-right">';
                if ($template == 2) {
                    $output .= '<button type="button" class="btn btn-default btn-sm itemList-delBtn c-b-20"><i class="fa fa-close closeColor"></i></button> </div>';
                } else {
                    $output .= '<i class="fa fa-close closeColor"></i></button>';
                }

                $output .= '</div>
                        </div>';
                $output .= '</div>';
            }
        }
        echo $output;
    }


    function Load_pos_holdInvoiceData_touch()
    {

        $invoiceID = $this->input->post('invoiceID');
        $getMenuInvoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);
        $output = '';
        if (!empty($getMenuInvoice)) {
            /*echo '<pre>';
            print_r($getMenuInvoice );
            echo '</pre>';*/

            foreach ($getMenuInvoice as $data) {
                $data['warehouseMenuID'] = str_pad($data['warehouseMenuID'], 4, "0", STR_PAD_LEFT);

                $output .= '<div class="row itemList" id="item_row_' . $data['menuSalesItemID'] . '" style="margin: 0px; border-bottom: 1px solid #dddddd; padding-top: 5px; padding-bottom: 5px;">';
                if (!empty($data['remarkes'])) {
                    $output .= '<div class="col-md-12" style="padding-left: 0;"><button class="pull-left btn btn-lg btn-default" type="button" style="margin-right: 0.5%;" onclick="openaddonList(' . $data['menuSalesItemID'] . ',' . $invoiceID . ',\'' . $data['remarkes'] . '\')"><i class="fa fa-plus fa-lg text-green"   aria-hidden="true" ></i></button><div class="receiptPadding" style="text-align: left; font-weight: 800; cursor: pointer;"   title="' . $data['remarkes'] . '" rel="tooltip" > ' . $data['menuMasterDescription'] . '</div>';
                } else {
                    $output .= '<div class="col-md-12"style="padding-left: 0;"><button class="pull-left btn btn-lg btn-default"  type="button" style="margin-right: 0.5%;" onclick="openaddonList(' . $data['menuSalesItemID'] . ',' . $invoiceID . ')"><i class="fa fa-plus fa-lg text-green"   aria-hidden="true" ></i></button><div class="receiptPadding" style="text-align: left; font-weight: 800; cursor: pointer;"> ' . $data['menuMasterDescription'] . '</div>';
                }
                $output .= '<div class="receiptPadding">
                                <input type="text" data-wh_menuID="' . $data['warehouseMenuID'] . '" onfocus="keepTheExistingQuantity.call(this)" onkeyup="updateQtyWithAuth(' . $data['menuSalesItemID'] . ',\'onkeyup\',' . $data['warehouseMenuID'] . ')" onchange="updateQtyWithAuth(' . $data['menuSalesItemID'] . ',\'onchange\',' . $data['warehouseMenuID'] . ')" value="' . $data['qty'] . '" class="display_qty menuItem_input numberFloat" id="qty_' . $data['menuSalesItemID'] . '" name="qty[' . $data['menuSalesItemID'] . ']"  />
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_itemCost menuItemTxt">' . $data['sellingPrice'] . '</span> <!-- @rate -->
                                <input type="hidden"  class="menuItemTxt_input numberFloat" name="sellingPrice[' . $data['menuSalesItemID'] . ']" value="' . $data['sellingPrice'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input numberFloat pricewithoutTax" name="pricewithoutTax[' . $data['menuSalesItemID'] . ']" value="' . $data['pricewithoutTax'] . '"/>
                                <input type="hidden"  class="menuItemTxt_input totalMenuTaxAmount numberFloat" name="totalMenuTaxAmount[' . $data['menuSalesItemID'] . ']" value="' . $data['totalTaxAmount'] . '"/>
<input type="hidden"  class="menuItemTxt_input numberFloat totalMenuServiceCharge" name="totalMenuServiceCharge[' . $data['menuSalesItemID'] . ']" value="' . $data['totalServiceCharge'] . '"/>
<input type="hidden"  name="frm_isTaxEnabled[' . $data['menuSalesItemID'] . ']" value="' . $data['isTaxEnabled'] . '"/>
                            </div>
                            <div class="receiptPadding">
                                <span class="menu_total menuItemTxt">0</span>  <!-- total -->
                            </div>

                            <div class="receiptPadding hide">
                                <input style="width:60%;" onkeyup="calculateFooter(\'P\')" name="discountPercentage[' . $data['menuSalesItemID'] . ']"  maxlength="3" type="text" value="' . $data['discountPer'] . '"
                                       class="menu_discount_percentage menu_qty menuItem_input numberFloat"> <!-- disc. % -->
                            </div>
                            <div class="receiptPadding hide">
                                <input style="width:90%;" onkeyup="calculateFooter(\'A\')" name="discountAmount[' . $data['menuSalesItemID'] . ']" type="text" value="' . $data['discountAmount'] . '"
                                       class="menu_discount_amount menu_qty menuItem_input numberFloat"><!-- disc. amount -->s
                            </div>
                            <div class="receiptPadding" style="width:19%;">
                                <div style="text-align: right;" class="itemCostNet menuItemTxt"> ' . $data['sellingPrice'] . '</div> <!-- net total -->
                                <button onclick="deleteDiv(' . $data['menuSalesItemID'] . ')" data-placement="bottom" rel="tooltip" title="Delete"
                                     style="cursor:pointer; margin-top: -20px;     margin-right: -47px;" type="button" class="pull-right btn btn-default itemList-delBtn">
                                     <i class="fa fa-close closeColor"></i></button>
                             
                        </div></div>';
                $output .= '</div>';
            }
        }
        echo $output;
    }

    function delete_menuSalesItem()
    {
        $id = $this->input->post('id');
        $outletID = $this->input->post('outletID');
        $output = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_specific($id, $outletID);
        $bill_status = $this->db->query("select srp_erp_pos_menusalesmaster.isHold from srp_erp_pos_menusalesitems 
join srp_erp_pos_menusalesmaster on srp_erp_pos_menusalesmaster.menuSalesID=srp_erp_pos_menusalesitems.menuSalesID
where srp_erp_pos_menusalesitems.menuSalesItemID=$id")->row()->isHold;
        if (!empty($output)) {
            if ($bill_status != 0) {
                $isPack = $output['isPack'];
                if ($isPack == 1) {
                    $this->Pos_restaurant_model->delete_srp_erp_pos_valuepackdetail_by_ItemID($id);
                }
                $result = $this->Pos_restaurant_model->delete_menuSalesItem($id, $outletID);
                if ($result) {
                    /** Delete Ad-on */
                    $this->db->select("menuSalesItemID");
                    $this->db->from("srp_erp_pos_menusalesitems");
                    $this->db->where("parentMenuSalesItemID", $id);
                    $output = $this->db->get()->result_array();
                    if (!empty($output)) {
                        $this->db->where("parentMenuSalesItemID", $id);
                        $this->db->delete('srp_erp_pos_menusalesitems');
                    }
                    echo json_encode(array('error' => 0, 'message' => 'done', 'add_on' => $output));
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'Error deleting!, Please contact system support team'));
                }
            } else {
                echo json_encode(array('error' => 1, 'message' => 'Error deleting, This bill is already submitted!'));
            }
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error deleting, Record not found!'));
        }
    }

    function clearPosInvoiceSession()
    {
        $this->session->unset_userdata('pos_invoice_no');
        echo json_encode(array('error' => 0, 'message' => 'session unset'));
    }

    function clearPosInvoiceSession_return()
    {
        $this->session->unset_userdata('pos_invoice_no');
        return json_encode(array('error' => 0, 'message' => 'session unset'));
    }

    function cancelCurrentOrder()
    {
        $result = isPos_invoiceSessionExist();
        if ($result) {

            $menuSalesID = $result;

            $isClosed = $this->db->query("SELECT srp_erp_pos_shiftdetails.isClosed FROM srp_erp_pos_menusalesmaster 
            join srp_erp_pos_shiftdetails on srp_erp_pos_shiftdetails.shiftID=srp_erp_pos_menusalesmaster.shiftID
            where menuSalesID=$menuSalesID")->row('isClosed');

            if ($isClosed == "0") {
                $this->db->trans_begin();

                /** Release table */
                $this->Pos_restaurant_model->clear_pos_tables($menuSalesID);

                /** Delete related tables */
                $this->Pos_restaurant_model->delete_srp_erp_pos_menusalesmaster($menuSalesID);
                $this->Pos_restaurant_model->delete_srp_erp_pos_menusalesitems_byMenuSalesID($menuSalesID);
                $this->Pos_restaurant_model->delete_srp_erp_pos_menusalesitemdetails_byMenuSalesID($menuSalesID);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('error' => 1, 'message' => 'Error, while cancelling the invoice , Please refresh and check or contact your system support team'));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('error' => 0, 'message' => 'Invoice successfully canceled.'));
                }
            } else {
                echo json_encode(array('error' => 0, 'message' => 'Cannot cancel bill in a closed session.'));
            }
        } else {
            echo json_encode(array('error' => 1, 'message' => 'There is no current invoice to cancel, You can create new Invoice'));
        }
    }

    function update_posListItems()
    {
        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $post = $this->input->post();

            $billData = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID);

            $discountPer = $this->input->post('discount_percentage'); /*Bill Discount*/
            $promotionID = $this->input->post('promotionID');
            $promotionDiscount = $this->db->select('commissionPercentage')->from('srp_erp_pos_customers')->where('customerID', $promotionID)->get()->row('commissionPercentage');


            $modifiedUserName = current_user();
            $modifiedDateTime = format_date_mysql_datetime();
            $modifiedUserID = current_userID();
            $modifiedPCID = current_pc();

            $qty = $this->input->post('qty');
            $discountPercentage_post = $this->input->post('discountPercentage');
            $discountAmount_post = $this->input->post('discountAmount');
            $sellingPrice_post = $this->input->post('sellingPrice');
            $priceWithoutTax_post = $this->input->post('pricewithoutTax');
            $pricewithoutTaxDiscount_post = $this->input->post('pricewithoutTaxDiscount');
            $isTaxesEnabled = $this->input->post('frm_isTaxEnabled');

            $taxAmount_post = $this->input->post('totalMenuTaxAmount');
            $taxAmountDiscount_post = $this->input->post('totalMenuTaxAmountDiscount');
            $serviceCharge_post = $this->input->post('totalMenuServiceCharge');
            $serviceChargeDiscount_post = $this->input->post('totalMenuServiceChargeDiscount');


            $data = array();
            if (!empty($qty)) {
                $i = 0;
                foreach ($qty as $key => $value) {
                    $itemQty = $value;
                    $discountPercentage = $discountPercentage_post[$key];
                    $discountAmount = $discountAmount_post[$key];
                    $sellingPrice = $sellingPrice_post[$key];
                    $totalSellingPrice = $sellingPrice * $itemQty;
                    $priceWithoutTax = $priceWithoutTax_post[$key];
                    $isTaxEnabled = $isTaxesEnabled[$key];

                    $net_item_wise_discount = ($pricewithoutTaxDiscount_post[$key] + $taxAmountDiscount_post[$key] + $serviceChargeDiscount_post[$key]) * $itemQty;
                    $data[$i]['discountAmount'] = $discountAmount; //$net_item_wise_discount; // total discount only - item wise

                    $tmp_net_sales = (($priceWithoutTax_post[$key] + $taxAmount_post[$key] + $serviceCharge_post[$key]) * $itemQty) - $net_item_wise_discount;


                    if ($discountPer > 0 && $promotionDiscount > 0) {
                        $generalDiscount_amount = $tmp_net_sales * ($discountPer / 100);
                        $promotionDiscount_amount = ($tmp_net_sales - $generalDiscount_amount) * ($promotionDiscount / 100);
                        $salesPriceAfterDiscount = $tmp_net_sales - ($generalDiscount_amount + $promotionDiscount_amount);
                        /** output */
                    } else if ($discountPer > 0) {
                        $generalDiscount_amount = $tmp_net_sales * ($discountPer / 100);
                        $salesPriceAfterDiscount = $tmp_net_sales - ($generalDiscount_amount);
                        /** output */
                    } else if ($promotionDiscount > 0) {
                        $promotionDiscount_amount = $tmp_net_sales * ($promotionDiscount / 100);
                        $salesPriceAfterDiscount = $tmp_net_sales - $promotionDiscount_amount;
                        /** output */
                    } else {
                        $salesPriceAfterDiscount = $tmp_net_sales;
                        /** output */
                    }

                    $data[$i]['salesPriceAfterDiscount'] = $salesPriceAfterDiscount;


                    $data[$i]['menuSalesItemID'] = $key;
                    $data[$i]['qty'] = $itemQty;
                    $data[$i]['discountPer'] = $discountPercentage;
                    //$data[$i]['discountAmount'] = $discountAmount; wrong
                    $data[$i]['salesPriceSubTotal'] = $itemQty * $priceWithoutTax;

                    $netTotal = ($itemQty * $priceWithoutTax) - ($pricewithoutTaxDiscount_post[$key] * $itemQty);

                    $discountedTax = $taxAmount_post[$key] * $itemQty;

                    /** normal Discount */
                    if ($discountPer > 0) {
                        $netTotal = $netTotal * ((100 - $discountPer) / 100);
                        $discountedTax = ($discountedTax * ((100 - $discountPer) / 100));

                        $totalSellingPrice = ($totalSellingPrice * ((100 - $discountPer) / 100));
                    }

                    /** Promotional Discount */
                    if ($promotionDiscount > 0) {
                        $netTotal = $netTotal * ((100 - $promotionDiscount) / 100);
                        //$discountedTax = ($promotionDiscount * ((100 - $promotionDiscount) / 100)) * $itemQty;
                        $discountedTax = ($discountedTax * ((100 - $promotionDiscount) / 100));
                        /*echo $netTotal;*/
                        $totalSellingPrice = ($totalSellingPrice * ((100 - $promotionDiscount) / 100));
                    }

                    if ($isTaxEnabled == 0) {
                        $netTotal += ($discountedTax - ($taxAmountDiscount_post[$key] * $itemQty));
                    }


                    //$data[$i]['salesPriceAfterDiscount'] = $totalSellingPrice;
                    $data[$i]['salesPriceNetTotal'] = $netTotal;
                    $data[$i]['netRevenueTotal'] = $netTotal;
                    $data[$i]['isOrderPending'] = 1;
                    $data[$i]['isOrderPending'] = 1;

                    $data[$i]['totalMenuTaxAmount'] = $taxAmount_post[$key];
                    $data[$i]['totalMenuServiceCharge'] = $serviceCharge_post[$key];

                    $data[$i]['modifiedPCID'] = $modifiedPCID;
                    $data[$i]['modifiedUserID'] = $modifiedUserID;
                    $data[$i]['modifiedDateTime'] = $modifiedDateTime;
                    $data[$i]['modifiedUserName'] = $modifiedUserName;
                    $data[$i]['is_sync'] = 0;

                    $i++;
                }
            }
            if (!empty($data)) {
                $this->db->update_batch('srp_erp_pos_menusalesitems', $data, 'menuSalesItemID');
            }
            return array('error' => 0, 'message' => 'done');
        } else {
            return array('error' => 1, 'message' => 'Receipt not created yet');
        }
    }

    function isFinalPayment()
    {
        $return = true;
        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $isDeliveryConfirmed = isDeliveryConfirmedOrder($invoiceID);
            if ($isDeliveryConfirmed) {
                /** Total Bill Amount */
                $this->db->select('*');
                $this->db->from('srp_erp_pos_menusalesmaster');
                $this->db->where('menuSalesID', $invoiceID);
                $row = $this->db->get()->row();
                $totalBillAmount = $row->subTotal;
                $isDelivery = $row->isDelivery;
                $deliveryCommission = $row->deliveryCommission;

                /** Total Paid Amount*/
                $q = "SELECT SUM(amount) as totalPaid FROM srp_erp_pos_menusalespayments WHERE menuSalesID = '" . $invoiceID . "'";
                $totalPaid = $this->db->query($q)->row('totalPaid');

                if ($isDelivery == 1 && $deliveryCommission != 0) {
                    if (($totalBillAmount - ($totalBillAmount * ($deliveryCommission / 100))) != $totalPaid) {
                        $return = false;
                    }
                } else {
                    if ($totalBillAmount != $totalPaid) {
                        $return = false;
                    }
                }
            }
        } else {
            $return = false;
        }
        return $return;
    }

    function update_pos_submitted_payments()
    {
        /** update payments */
        $res = $this->Pos_restaurant_model->update_pos_submitted_payments(); // Sync DONE

        if ($res['status'] == true) {
            echo json_encode(array('error' => 0, 'message' => 'Payment Updated Successfully', 'invoiceID' => $res['invoice_id'], 'outletID' => ''));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error', 'invoiceID' => '', 'outletID' => ''));
        }
    }

    function submit_pos_payments()
    {
        //exit;
        //var_dump($this->input->post('gross_total_input'));exit;

        $this->db->trans_start();
        $time = $this->input->post('currentTime');
        $curDateTmp = date('Y-m-d') . $time;
        $curDate = format_date_mysql_datetime($curDateTmp);
        $own_delivery_amount = $this->input->post('own_delivery_amount');

        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $isItemsExist = $this->Pos_restaurant_model->is_items_exist($invoiceID);
            if ($isItemsExist) {
                $isPartialPaymentEnabled = $this->pos_policy->isPartialPaymentEnabled();

                if ($isPartialPaymentEnabled) {
                    $bill_amount = $this->input->post('netTotalAmount'); // amount has to be paid : net Amount
                    $paidAmount = $this->input->post('paid');
                    $q = "SELECT SUM(amount) as tmpAmount FROM srp_erp_pos_menusalespayments WHERE menuSalesID = '" . $invoiceID . "'";
                    $advancePayment = $this->db->query($q)->row('tmpAmount');
                    $advancePayment = $advancePayment + $paidAmount;
                    if ($bill_amount > $advancePayment) {
                        //save payment details and exit;
                        /** update payments and outlet taxes*/
                        $payment_update_res = $this->Pos_restaurant_model->update_partial_payment();
                        echo json_encode($payment_update_res);
                        exit;
                    }
                }

                $current_companyID = current_companyID();
                $outletID = get_outletID();
                $query = $this->db->query("SELECT ID FROM `srp_erp_pos_paymentglconfigdetail` where paymentConfigMasterID=42 and companyID=$current_companyID and warehouseID=$outletID");
                $post_index = "";
                if ($query->num_rows() > 0) {
                    $post_index = 'paymentTypes[' . $query->row()->ID . ']';
                    $loyaltypaymnt = $this->input->post($post_index);
                } else {
                    $loyaltypaymnt = "";
                }

                if (!empty($loyaltypaymnt)) {
                    $customerID = $this->input->post('customerID');
                    if (empty($customerID)) {
                        echo json_encode(array('error' => 1, 'message' => 'Trying to claim loyalty points without adding a customer.'));
                        exit;
                    }
                    //check card status
                    $loyaltycard = $this->db->query("SELECT cardMasterID,barcode FROM srp_erp_pos_loyaltycard WHERE companyID={$current_companyID} AND customerID=$customerID AND isActive=1");
                    if ($loyaltycard->num_rows() == 0) {
                        echo json_encode(array('error' => 1, 'message' => 'The customer does not have an active loyalty card.'));
                        exit;
                    }
                    $points_query = $this->db->query("SELECT SUM(points) as points FROM `srp_erp_pos_loyaltytopup` WHERE posCustomerAutoID=$customerID")->row_array();
                    $points = $this->db->query("SELECT amount as exchange_rate FROM srp_erp_loyaltypointsetup WHERE companyID={$current_companyID} AND isActive=1")->row_array();

                    $available_points = $points_query['points'];
                    $available_amount = $points['exchange_rate'] * $available_points;
                    if ($available_amount < $loyaltypaymnt) {
                        echo json_encode(array('error' => 1, 'message' => 'Loyalty point is not sufficient'));
                        exit;
                    }
                }
                $commissionGL = get_glInfo_for_MenuSalesMaster_update(4);
                $deliveryPersonID = $this->input->post('deliveryPersonID');
                $isDelivery = $this->input->post('isDelivery');
                if ($isDelivery > 0) {
                    $delper = open_delevery_person();
                    if ($delper == 1) {
                        if (empty($deliveryPersonID)) {
                            echo json_encode(array('error' => 1, 'message' => 'Please Select Delivery Person'));
                            exit;
                        }
                    }
                }
                $deliveryCommission = 0;
                if ($deliveryPersonID > 0) {
                    $r = $this->Pos_restaurant_model->get_customerInfo($deliveryPersonID);
                    if (!empty($r)) {
                        $deliveryCommission = $r['commissionPercentage'];
                    }
                    $data['isDelivery'] = 1;
                    $data['deliveryPersonID'] = $deliveryPersonID;
                    $data['deliveryCommission'] = $deliveryCommission;
                    $data['isOnTimeCommision'] = $r['isOnTimePayment'];
                }

                $grossSales = $this->input->post('gross_total_input');
                $disPercentage = $this->input->post('discount_percentage');

                $subTotal = $grossSales;
                if ($disPercentage > 0) {
                    $subTotal = $grossSales - ($disPercentage / 100) * $grossSales;
                }


                $wastage = false;
                $wastage_glID = '';
                $promotionID = $this->input->post('promotionID');
                if ($promotionID) {
                    $r = $this->Pos_restaurant_model->get_customerInfo($promotionID);
                    if (!empty($r)) {
                        $promotionDiscount = $r['commissionPercentage'];
                        if ($r['customerTypeMasterID'] == 3) {
                            $wastage = true;
                            $wastage_glID = $r['expenseGLAutoID'];
                        }
                    }
                    $data['isPromotion'] = 1;
                    $data['promotionID'] = $promotionID;
                    $data['promotionDiscount'] = $promotionDiscount;
                    if ($data['promotionDiscount'] > 0) {
                        $subTotal = $subTotal - ($data['promotionDiscount'] / 100) * $subTotal;
                        $data['promotionDiscountAmount'] = ($data['promotionDiscount'] / 100) * $this->input->post('total_payable_amt');
                    }
                } else {
                    $data['isPromotion'] = 0;
                    $data['promotionID'] = 0;
                    $data['promotionDiscount'] = 0;
                    $data['promotionDiscountAmount'] = 0;
                }


                $this->update_posListItems();  //salesPriceSubTotal update on this line

                $this->Pos_restaurant_model->updateTotalCost($invoiceID);
                $this->updateMenuSalesItemDetail($invoiceID, $wastage, $wastage_glID);
                $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID);
                $paid = $this->input->post('paid');
                $cardTotalAmount = $this->input->post('cardTotalAmount');

                $data['menuSalesID'] = $invoiceID;
                $data['isCreditSales'] = $this->input->post('isCreditSale');
                $data['cardRefNo'] = $this->input->post('card_numb');

                $data['subTotal'] = $grossSales;


                if ($own_delivery_amount > 0) {
                    $data['subTotal'] = $data['subTotal'] + $own_delivery_amount;
                }

                $data['discountPer'] = $this->input->post('discount_percentage');
                $data['discountAmount'] = $this->input->post('total_discount_amount');
                $data['netTotal'] = $this->input->post('total_payable_amt'); // amount has to be paid : net Amount

                $data['paidAmount'] = $this->input->post('netTotalAmount');

                $data['balanceAmount'] = $this->input->post('returned_change'); // remain amount
                $data['cashReceivedAmount'] = $paid;  // cash paid by user may be there will be return
                $data['cashAmount'] = ($paid + $invoice['cashAmount']) - $cardTotalAmount;  // cash paid by user may be there will be return
                $data['chequeAmount'] = 0;
                $data['chequeNo'] = $this->input->post('cheque');
                $data['serviceCharge'] = $this->input->post('serviceCharge');
                $isDelivery = isDeliveryConfirmedOrder($invoiceID);
                if ($isDelivery) {
                    $deliveryInfo = get_confirmedDeliveryOrder($invoiceID);
                    $data['isHold'] = 1;
                    $data['holdRemarks'] = 'Delivery Date:' . $deliveryInfo['deliveryDate'] . '  Time: ' . $deliveryInfo['deliveryTime'];
                } else {
                    $data['isHold'] = 0;
                }

                $data['grossAmount'] = $this->input->post('gross_total_amount_input');
                $data['grossTotal'] = $this->input->post('gross_total_input');
                $data['totalQty'] = $this->input->post('total_item_qty_input');
                $data['totalTaxPercentage'] = $this->input->post('totalTax_input');
                $data['totalTaxAmount'] = $this->input->post('display_tax_amt_input');
                $data['paymentMethod'] = $this->input->post('payment_method');

                $KOTStartDateTime = $this->input->post('KOTStartDateTime');
                $this->db->select('KOTStartDateTime');
                $this->db->from('srp_erp_pos_menusalesmaster');
                $this->db->where('menuSalesID', $invoiceID);
                $KOTStartexist = $this->db->get()->row_array();
                $data['isOrderPending'] = 1;
                if (empty($KOTStartexist['KOTStartDateTime'])) {
                    $data['KOTStartDateTime'] = "$KOTStartDateTime";
                }
                $data['preparationTime'] = 20;

                /** GL Updates **/
                $netTotal = $this->input->post('total_payable_amt');

                $bankCurrencyID = null; //$glInfo['bankCurrencyID'];
                $conversion = currency_conversionID($invoice['transactionCurrencyID'], $bankCurrencyID, $netTotal);

                if ($conversion['conversion'] != 0) {
                    $bankCurrencyAmount = $paid / $conversion['conversion'];
                } else {
                    $bankCurrencyAmount = $paid;
                }

                $data['bankGLAutoID'] = null;
                $data['bankCurrencyID'] = null;
                $data['bankCurrency'] = null;
                $data['bankCurrencyDecimalPlaces'] = null; //$glInfo['bankCurrencyDecimalPlaces'];
                $data['bankCurrencyExchangeRate'] = $conversion['conversion'];
                $data['bankCurrencyAmount'] = $bankCurrencyAmount;
                $data['commissionGLAutoID'] = $commissionGL["GLAutoID"];

                /**  End of GL Updates **/

                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_userID();
                $data['modifiedUserName'] = current_user();
                $data['modifiedDateTime'] = $curDate;
                $data['is_sync'] = 0;

                $customerID = $this->input->post('customerID');
                if (!empty($this->input->post('customerID')) && $customerID > 0) {
                    $tmpCustomer = $this->Pos_restaurant_model->get_pos_customer($customerID);
                    $data['customerName'] = $tmpCustomer['CustomerName'];
                    $data['customerTelephone'] = $tmpCustomer['customerTelephone'];
                    $data['customerID'] = $customerID;

                    $update_data = array(
                        'customerName' => $this->input->post('customerName'),
                        'CustomerAddress1' => $this->input->post('customerAddress'),
                        'customerEmail' => $this->input->post('customerEmail'),
                        'customerCountry' => $this->input->post('customerCountry_o'),
                        'customerCountryCode' => $this->input->post('customerCountryCode_o'),
                        'customerCountryId' => $this->input->post('customerCountryId_o'),
                    );
                    if (empty($loyaltypaymnt)) {
                        $this->db->where('posCustomerAutoID', $customerID);
                        $this->db->update('srp_erp_pos_customermaster', $update_data);
                    }
                }

                $isOwnDelivery = $this->input->post('isOwnDelivery');
                if ($isOwnDelivery == '1') {
                    $data['isDelivery'] = 1;
                    $data['deliveryRevenueGLID'] = $this->input->post('revGLID');
                    $data['ownDeliveryPercentage'] = $this->input->post('own_delivery_percentage');
                    $data['ownDeliveryAmount'] = $this->input->post('own_delivery_amount');
                    $data['deliveryPersonID'] = $this->input->post('own_delivery_person');
                }


                $this->Pos_restaurant_model->update_srp_erp_pos_menusalesmaster($data, $invoiceID); // Sync DONE
                //            $this->db->trans_complete();
                //            $this->db->trans_commit();
                //            var_dump($data);exit;

                $this->Pos_restaurant_model->update_wowfood_status($invoiceID, 2);


                $this->updateNetTotalForInvoice($invoiceID); // Sync DONE

                //EXECUTING LOYALTY PROGRAM.
                if (!empty($loyaltypaymnt)) {
                    if ($customerID == 0) {
                    } else {
                        $companyID = current_companyID();
                        $loyaltycard = $this->db->query("SELECT cardMasterID,barcode FROM srp_erp_pos_loyaltycard WHERE companyID={$companyID} AND customerID=$customerID AND isActive=1")->row_array();
                        $points = $this->db->query("SELECT poinforPuchaseAmount,purchaseRewardPoint,priceToPointsEarned,pointSetupID,currencyID,amount,loyaltyPoints FROM srp_erp_loyaltypointsetup WHERE companyID={$companyID} AND isActive=1")->row_array();
                        $netTotalAmount = $this->input->post('netTotalAmount');
                        $loyaltypaymnt = $this->input->post($post_index);

                        if (!empty($loyaltycard)) {
                            if (!empty($points)) {
                                if (!empty($loyaltypaymnt) && $loyaltypaymnt > 0) {
                                    $totpts = ($points['purchaseRewardPoint'] / $points['poinforPuchaseAmount']) * ($netTotalAmount - $loyaltypaymnt);
                                    $topUpAmount = ($netTotalAmount - $loyaltypaymnt);
                                    $ptsm['cardMasterID'] = $loyaltycard['cardMasterID'];
                                    $ptsm['barCode'] = $loyaltycard['barcode'];
                                    $ptsm['posCustomerAutoID'] = $customerID;
                                    $ptsm['topUpAmount'] = 0;
                                    $ptsm['transationType'] = 1;
                                    $ptsm['points'] = ($loyaltypaymnt / $points['amount']) * -1; //$points['amount'] is exchange rate of 1 point.
                                    $ptsm['pointSetupID'] = $points['pointSetupID'];
                                    $ptsm['invoiceID'] = $invoiceID;
                                    $ptsm['companyID'] = $companyID;
                                    $ptsm['createdPCID'] = $this->common_data['current_pc'];
                                    $ptsm['createdUserID'] = $this->common_data['current_userID'];
                                    $ptsm['createdUserName'] = $this->common_data['current_user'];
                                    $ptsm['createdUserGroup'] = $this->common_data['user_group'];
                                    $ptsm['createdDateTime'] = current_date();
                                    $this->db->insert('srp_erp_pos_loyaltytopup', $ptsm);
                                } else {
                                    $totpts = ($points['purchaseRewardPoint'] / $points['poinforPuchaseAmount']) * $netTotalAmount;
                                    $topUpAmount = $netTotalAmount;
                                }

                                if ($netTotalAmount > $points['priceToPointsEarned']) {
                                    if ($totpts > 0) {
                                        $pts['cardMasterID'] = $loyaltycard['cardMasterID'];
                                        $pts['barCode'] = $loyaltycard['barcode'];
                                        $pts['posCustomerAutoID'] = $customerID;
                                        $pts['topUpAmount'] = $topUpAmount;
                                        $pts['points'] = $totpts;
                                        $pts['pointSetupID'] = $points['pointSetupID'];
                                        $pts['invoiceID'] = $invoiceID;
                                        $pts['companyID'] = $companyID;
                                        $pts['createdPCID'] = $this->common_data['current_pc'];
                                        $pts['createdUserID'] = $this->common_data['current_userID'];
                                        $pts['createdUserName'] = $this->common_data['current_user'];
                                        $pts['createdUserGroup'] = $this->common_data['user_group'];
                                        $pts['createdDateTime'] = current_date();
                                        $this->db->insert('srp_erp_pos_loyaltytopup', $pts);
                                    }
                                }
                            }
                        }
                    }
                } else {

                    if ($customerID == 0) {
                    } else {
                        $companyID = current_companyID();
                        $loyaltycard_query = $this->db->query("SELECT cardMasterID,barcode FROM srp_erp_pos_loyaltycard WHERE companyID={$companyID} AND customerID=$customerID AND isActive=1");
                        if ($loyaltycard_query->num_rows() > 0) {
                            $loyaltycard = $loyaltycard_query->row_array();
                            $points = $this->db->query("SELECT poinforPuchaseAmount,purchaseRewardPoint,priceToPointsEarned,pointSetupID,currencyID,amount,loyaltyPoints FROM srp_erp_loyaltypointsetup WHERE companyID={$companyID} AND isActive=1")->row_array();
                            $netTotalAmount = $this->input->post('netTotalAmount');
                            $totpts = ($points['purchaseRewardPoint'] / $points['poinforPuchaseAmount']) * $netTotalAmount;
                            $topUpAmount = $netTotalAmount;

                            if ($netTotalAmount > $points['priceToPointsEarned']) {
                                if ($totpts > 0) {
                                    $pts['cardMasterID'] = $loyaltycard['cardMasterID'];
                                    $pts['barCode'] = $loyaltycard['barcode'];
                                    $pts['posCustomerAutoID'] = $customerID;
                                    $pts['topUpAmount'] = $topUpAmount;
                                    $pts['points'] = $totpts;
                                    $pts['pointSetupID'] = $points['pointSetupID'];
                                    $pts['invoiceID'] = $invoiceID;
                                    $pts['companyID'] = $companyID;
                                    $pts['createdPCID'] = $this->common_data['current_pc'];
                                    $pts['createdUserID'] = $this->common_data['current_userID'];
                                    $pts['createdUserName'] = $this->common_data['current_user'];
                                    $pts['createdUserGroup'] = $this->common_data['user_group'];
                                    $pts['createdDateTime'] = current_date();
                                    $this->db->insert('srp_erp_pos_loyaltytopup', $pts);
                                }
                            }
                        }
                    }
                }


                /** update payments and outlet taxes*/
                $this->Pos_restaurant_model->update_pos_payments(); // Sync DONE

                $isFinalPayment = $this->isFinalPayment();
                if ($isFinalPayment) {
                    $this->db->where('menuSalesID', $invoiceID);
                    $this->db->update('srp_erp_pos_menusalesmaster', array("isHold" => 0));
                    /*UPDATE TAXES */
                    $update_status['menuSalesTax'] = $this->Pos_restaurant_model->update_menuSalesTax($invoiceID); // Sync DONE

                    $is_dineIn_order = is_dineIn_order($invoiceID);
                    $get_pos_templateID = get_pos_templateID();
                    if ($get_pos_templateID == 2 || $get_pos_templateID == 4) {
                        if ($is_dineIn_order) {
                            /*UPDATE SERVICE CHARGES */
                            $update_status['menuSalesServiceCharge'] = $this->Pos_restaurant_model->update_menuSalesServiceCharge($invoiceID); // Sync DONE
                        } else {
                            $update_status['menuSalesServiceCharge'] = true;
                        }
                    } else {
                        /*UPDATE SERVICE CHARGES */
                        $update_status['menuSalesServiceCharge'] = $this->Pos_restaurant_model->update_menuSalesServiceCharge($invoiceID); // Sync DONE
                    }


                    /*UPDATE DELIVERY COMMISSION AMOUNT - ONLY FOR DELIVERY ORDERS */
                    $update_status['deliveryCommission'] = $this->Pos_restaurant_model->update_deliveryCommission($invoiceID); // Sync DONE

                    //update flag to confirm that update_menuSalesTax,update_menuSalesServiceCharge,update_deliveryCommission records are inserted successfully.
                    if ($update_status['menuSalesTax'] == true && $update_status['menuSalesServiceCharge'] == true && $update_status['deliveryCommission'] == true) {
                        $update_status_record = array(
                            "isUpdated" => 1
                        );
                        $this->db->where('menuSalesID', $invoiceID);
                        $this->db->update('srp_erp_pos_menusalesmaster', $update_status_record);
                    } else {
                        $update_status_record = array(
                            "isUpdated" => 0
                        );
                        $this->db->where('menuSalesID', $invoiceID);
                        $this->db->update('srp_erp_pos_menusalesmaster', $update_status_record);
                    }
                }

                $this->Pos_restaurant_model->update_diningTableReset($invoice['tableID']); // DO NOT NEED SYNC - because table status are doesn't need to be updated in the server.


                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('error' => 1, 'message' => 'error, please contact your support team' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();

                    /* New GL Entries Review */
                    //$this->restaurant_doubleEntry_for_bill($invoiceID);
                    $this->Pos_restaurant_model->checking_for_errors($invoiceID);

                    $this->session->unset_userdata('pos_invoice_no');
                    $outletID = get_outletID();
                    echo json_encode(array('error' => 0, 'message' => 'payment submitted', 'invoiceID' => $invoiceID, 'outletID' => $outletID));
                }
            }else{
                echo json_encode(array('error' => 1, 'message' => 'Items are not found. Please recheck and try again'));
            }
        } else {
            echo json_encode(array('error' => 2, 'message' => 'Receipt not created yet'));
        }
    }


    function restaurant_doubleEntry_for_billUpdate()
    {

        $invoiceID = $_POST['invoiceID'];
        $menusalesID = $_POST['invoiceID']; //menu sales id is similar to invoice id pass by frontend.

        //deleting previous record before insert new records
        $this->db->where('pos_menusalesID', $menusalesID);
        $this->db->delete('srp_erp_generalledger_review');
        $this->db->where('pos_menusalesID', $menusalesID);
        $this->db->delete('srp_erp_bankledger_review');


        /**
         * New GL Entries Review
         */
        /** 1. REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger_review($invoiceID);
        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger_review($invoiceID);
        /** 3. COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_review($invoiceID);
        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_review($invoiceID);

        /** 5. TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger_review($invoiceID);
        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger_review($invoiceID);
        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger_review($invoiceID);
        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger_review($invoiceID);
        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger_review($invoiceID);
        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger_review($invoiceID);
        /** 11. OUTLET TAX */
        $get_outletID = get_outletID();
        $current_companyID = current_companyID();
        $isOutletTaxEnabled = isOutletTaxEnabled($get_outletID, $current_companyID);
        if ($isOutletTaxEnabled == true) {
            $this->Pos_restaurant_accounts->update_outlet_tax_generalLedger_review($invoiceID);
        }

        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_bankLedger_review($invoiceID);
    }

    /**
     * New GL Entries Review
     * for bill wise
     */
    function restaurant_doubleEntry_for_bill()
    {

        $invoiceID = $_POST['invoiceID'];

        /**
         * New GL Entries Review
         */
        /** 1. REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger_review($invoiceID);
        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger_review($invoiceID);
        /** 3. COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_review($invoiceID);
        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_review($invoiceID);

        /** 5. TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger_review($invoiceID);
        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger_review($invoiceID);
        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger_review($invoiceID);
        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger_review($invoiceID);
        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger_review($invoiceID);
        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger_review($invoiceID);
        /** 11. OUTLET TAX */
        $get_outletID = get_outletID();
        $current_companyID = current_companyID();
        $isOutletTaxEnabled = isOutletTaxEnabled($get_outletID, $current_companyID);
        if ($isOutletTaxEnabled == true) {
            $this->Pos_restaurant_accounts->update_outlet_tax_generalLedger_review($invoiceID);
        }

        /** 12. OWN DELIVERY */
        $this->Pos_restaurant_accounts->update_own_delivery_generalLedger_review($invoiceID);


        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_bankLedger_review($invoiceID);

        // Customer invoice creation for credit sales entries.
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];
        $this->Pos_restaurant_accounts->pos_generate_invoices_on_bill_submit($shiftID, $invoiceID);  // outlet ID added - where done

        /** 1. CREDIT SALES  - REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger_credit_sales_review($invoiceID);
        /** 2. CREDIT SALES  - COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_credit_sales_review($invoiceID);
        /** 3. CREDIT SALES  - INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_credit_sales_review($invoiceID, true, $shiftID);

        /** 4.  CREDIT SALES - TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger_credit_sales_review($invoiceID);
        /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger_credit_sales_review($invoiceID);
        /** 6.  CREDIT SALES - COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger_credit_sales_review($invoiceID);
        /** 7.  CREDIT SALES - ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger_credit_sales_review($invoiceID);
        /** 8.  CREDIT SALES - ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger_credit_sales_review($invoiceID);
        /** 9. CREDIT SALES -  SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger_credit_sales_review($invoiceID);
        /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */
        $this->Pos_restaurant_accounts->update_creditSales_generalLedger_credit_sales_review($invoiceID);
        /** 11. OUTLET TAX */
        $get_outletID = get_outletID();
        $current_companyID = current_companyID();
        $isOutletTaxEnabled = isOutletTaxEnabled($get_outletID, $current_companyID);
        if ($isOutletTaxEnabled == true) {
            $this->Pos_restaurant_accounts->update_outlet_tax_generalLedger_credit_sales_review($invoiceID);
        }

        /** CREDIT SALES - ITEM LEDGER  */
        $this->Pos_restaurant_accounts->update_itemLedger_credit_sales_review($invoiceID);
        $this->Pos_restaurant_accounts->update_itemLedger_review($invoiceID);
    }

    /**
     * UPDATE NET TOTAL
     * @param $menuSalesID
     * @return mixed
     */
    function updateNetTotalForInvoice($menuSalesID)
    {
        $outletID = get_outletID();

        $q = "UPDATE srp_erp_pos_menusalesmaster
                SET netTotal = (
                    SELECT sum(IFNULL(salesPriceNetTotal,0)) AS totalNet FROM srp_erp_pos_menusalesitems WHERE menuSalesID = '" . $menuSalesID . "' AND warehouseMenuID = '" . $outletID . "'
                ),
                 netRevenueTotal = (
                    SELECT sum(IFNULL(salesPriceNetTotal,0)) AS totalNet FROM srp_erp_pos_menusalesitems WHERE menuSalesID = '" . $menuSalesID . "' AND warehouseMenuID = '" . $outletID . "'
                ), is_sync = 0
                WHERE
                    menuSalesID = '" . $menuSalesID . "' AND wareHouseAutoID = '" . $outletID . "'";

        $result = $this->db->query($q);
        if ($result == true) {
            return true;
        } else {
            return false;
        }
    }

    function updateMenuSalesItemDetail($invoiceID, $wastage = false, $wastage_glID = null)
    {

        /* Setup Default values */
        $warehouseInfo = $this->Pos_restaurant_model->get_wareHouse();

        $segmentID = $warehouseInfo['segmentID'];
        $segmentCode = $warehouseInfo['segmentCode'];
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserName = current_user();
        $createdUserGroup = current_user_group();
        $timeStamp = format_date_mysql_datetime();
        $warehouseID = get_outletID();


        $batchData = array();
        /*get items for the invoice */
        $result = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID);


        /*loop items */
        $i = 0;
        $cost_based_on_itemmaster_wac = cost_based_on_itemmaster_wac();
        foreach ($result as $item) {

            $menuMaster = $item['menuMasterID'];
            $is_pack = $this->Pos_restaurant_model->is_pack($menuMaster);
            if ($is_pack != '1') {
                $itemDetailList = $this->Pos_restaurant_model->get_srp_erp_pos_menudetails_by_menuMasterID($menuMaster);
                foreach ($itemDetailList as $itemDetail) {
                    if ($cost_based_on_itemmaster_wac) {
                        $cost = fetch_itemledger_transactionAmount($itemDetail['itemAutoID'], 'companyLocalExchangeRate');
                    } else {
                        $cost = $itemDetail['cost'];
                    }
                    $batchData[$i]['menuSalesItemID'] = $item['menuSalesItemID'];
                    $batchData[$i]['menuSalesID'] = $item['menuSalesID'];
                    $batchData[$i]['itemAutoID'] = $itemDetail['itemAutoID'];
                    $batchData[$i]['warehouseAutoID'] = $warehouseID;
                    $batchData[$i]['qty'] = $itemDetail['qty'];
                    $batchData[$i]['UOM'] = $itemDetail['UOM'];
                    $batchData[$i]['UOMID'] = $itemDetail['uomID'];
                    $batchData[$i]['cost'] = $cost;
                    $batchData[$i]['actualInventoryCost'] = $itemDetail['actualInventoryCost'];
                    $batchData[$i]['menuSalesQty'] = $item['qty'];
                    $batchData[$i]['menuID'] = $menuMaster;
                    $batchData[$i]['costGLAutoID'] = $wastage ? $wastage_glID : $itemDetail['costGLAutoID'];
                    $batchData[$i]['assetGLAutoID'] = $itemDetail['assetGLAutoID'];
                    $batchData[$i]['isWastage'] = $wastage ? 1 : 0;
                    $batchData[$i]['companyID'] = $companyID;
                    $batchData[$i]['companyCode'] = $companyCode;
                    $batchData[$i]['segmentID'] = $segmentID;
                    $batchData[$i]['segmentCode'] = $segmentCode;
                    $batchData[$i]['createdPCID'] = $createdPCID;
                    $batchData[$i]['createdUserID'] = $createdUserID;
                    $batchData[$i]['createdDateTime'] = $timeStamp;
                    $batchData[$i]['createdUserName'] = $createdUserName;
                    $batchData[$i]['createdUserGroup'] = $createdUserGroup;
                    $batchData[$i]['timeStamp'] = $timeStamp;
                    $batchData[$i]['id_store'] = $warehouseID;
                    $i++;
                }
            } else {
                $menuSalesItemID = $item['menuSalesItemID'];
                $qry_valuepackdetail = $this->db->query("SELECT * FROM `srp_erp_pos_valuepackdetail` WHERE menuSalesItemID=$menuSalesItemID");

                foreach ($qry_valuepackdetail->result() as $valuepackdetail) {
                    $menuID = $valuepackdetail->menuID;
                    $itemDetailList = $this->Pos_restaurant_model->get_srp_erp_pos_menudetails_by_menuMasterID($menuID);
                    $qty = (int)$valuepackdetail->qty;
                    $j = 0;

                    while ($j < $qty) {
                        foreach ($itemDetailList as $itemDetail) {
                            if ($cost_based_on_itemmaster_wac) {
                                $cost = fetch_itemledger_transactionAmount($itemDetail['itemAutoID'], 'companyLocalExchangeRate');
                            } else {
                                $cost = $itemDetail['cost'];
                            }
                            $batchData[$i]['menuSalesItemID'] = $item['menuSalesItemID'];
                            $batchData[$i]['menuSalesID'] = $item['menuSalesID'];
                            $batchData[$i]['itemAutoID'] = $itemDetail['itemAutoID'];
                            $batchData[$i]['warehouseAutoID'] = $warehouseID;
                            $batchData[$i]['qty'] = $itemDetail['qty'];
                            $batchData[$i]['UOM'] = $itemDetail['UOM'];
                            $batchData[$i]['UOMID'] = $itemDetail['uomID'];
                            $batchData[$i]['cost'] = $cost;
                            $batchData[$i]['actualInventoryCost'] = $itemDetail['actualInventoryCost'];
                            $batchData[$i]['menuSalesQty'] = $item['qty'];
                            $batchData[$i]['menuID'] = $menuMaster;
                            $batchData[$i]['costGLAutoID'] = $wastage ? $wastage_glID : $itemDetail['costGLAutoID'];
                            $batchData[$i]['assetGLAutoID'] = $itemDetail['assetGLAutoID'];
                            $batchData[$i]['isWastage'] = $wastage ? 1 : 0;
                            $batchData[$i]['companyID'] = $companyID;
                            $batchData[$i]['companyCode'] = $companyCode;
                            $batchData[$i]['segmentID'] = $segmentID;
                            $batchData[$i]['segmentCode'] = $segmentCode;
                            $batchData[$i]['createdPCID'] = $createdPCID;
                            $batchData[$i]['createdUserID'] = $createdUserID;
                            $batchData[$i]['createdDateTime'] = $timeStamp;
                            $batchData[$i]['createdUserName'] = $createdUserName;
                            $batchData[$i]['createdUserGroup'] = $createdUserGroup;
                            $batchData[$i]['timeStamp'] = $timeStamp;
                            $batchData[$i]['id_store'] = $warehouseID;
                            $i++;
                        }
                        $j++;
                    }
                }
            }
        }
        if (!empty($batchData)) {
            $this->Pos_restaurant_model->batch_insert_srp_erp_pos_menusalesitemdetails($batchData);
        }
    }

    function loadPrintTemplate()
    {
        $this->load->model('Pos_kitchen_model');
        $invoiceID = isPos_invoiceSessionExist();
        $invoiceID = ($invoiceID) ? $invoiceID : $this->input->post('invoiceID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);

        $data['outletTaxMaster'] = $this->Pos_restaurant_model->outlet_tax_list($masters['wareHouseAutoID']);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['auth'] = false;
        $data['wifi'] = true;
        $data['isHold'] = $masters['isHold'];
        if ($data['isHold'] == 1) {
            $last_partial_payment = $this->Pos_restaurant_model->get_last_partial_payment($invoiceID);
            $data['partial_payment_amount'] = $last_partial_payment['amount'];
        }
        $data['payment_references'] = $this->Pos_restaurant_model->get_payment_references($invoiceID);
        $data['waiterName'] = $this->Pos_kitchen_model->getWaiterNameByMenuSalesID($invoiceID);
        $template = get_print_template();
        //echo $template;exit;
        //$this->load->view('system/pos/printTemplate/restaurant-pos-thermal-printer', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);
        $this->load->view($template, $data);
    }

    function loadPrintTemplateForPortablePos()
    {
        $invoiceID = isPos_invoiceSessionExist();
        $invoiceID = ($invoiceID) ? $invoiceID : $this->input->post('invoiceID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);

        $data['outletTaxMaster'] = $this->Pos_restaurant_model->outlet_tax_list($masters['wareHouseAutoID']);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['auth'] = false;
        $data['wifi'] = true;
        $data['isHold'] = $masters['isHold'];
        if ($data['isHold'] == 1) {
            $last_partial_payment = $this->Pos_restaurant_model->get_last_partial_payment($invoiceID);
            $data['partial_payment_amount'] = $last_partial_payment['amount'];
        }
        $template = get_print_template();
        //echo $template;exit;
        //$this->load->view('system/pos/printTemplate/restaurant-pos-thermal-printer', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);.
        $template = str_replace("system/pos/printTemplate", "system/pos/printTemplatePortablePos", $template);
        //echo $template;exit;
        $this->load->view($template, $data);
    }

    function loadPrintTemplateSampleBill()
    {
        $outletID = get_outletID();
        $invoiceID = trim($this->input->post('invoiceID') ?? '');
        $promotionID = trim($this->input->post('promotionID') ?? '');
        $promotional_discount = trim($this->input->post('promotional_discount') ?? '');
        $promotionIDdatacp = trim($this->input->post('promotionIDdatacp') ?? '');
        $this->update_posListItems();

        $this->Pos_restaurant_model->update_isSampleBillPrintFlag($invoiceID, $outletID);

        if ($promotionID) {
            $data['isPromotion'] = 1;
            $data['promotionID'] = $promotionID;
            $data['promotionDiscount'] = $promotionIDdatacp;
            $data['promotionDiscountAmount'] = $promotional_discount;
            $this->db->where('menuSalesID', $invoiceID)->update('srp_erp_pos_menusalesmaster', $data);
        } else {
            $data['isPromotion'] = 0;
            $data['promotionID'] = null;
            $data['promotionDiscount'] = 0;
            $data['promotionDiscountAmount'] = 0;
            $this->db->where('menuSalesID', $invoiceID)->update('srp_erp_pos_menusalesmaster', $data);
        }

        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_forHoldBill($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['sampleBill'] = true;
        $data['auth'] = false;
        $data['isSample'] = true;
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);
        $data['outletTaxMaster'] = $this->Pos_restaurant_model->outlet_tax_list($outletID);
        //var_dump($data['outletTaxMaster']);exit;
        $template = get_print_template();
        $data['template'] = $template;
        $data['pinBasedAccess'] = $this->pos_policy->pinBasedAccess();
        $data['payment_references'] = $this->Pos_restaurant_model->get_payment_references($invoiceID);
        $this->load->view($template, $data);
    }

    function submitHoldReceipt()
    {
        $id = isPos_invoiceSessionExist();

        if ($id) {
            //$this->form_validation->set_rules('holdReference', 'Hold Reference', 'trim|required');
            $data = array();
            if ($this->form_validation->run() == FALSE && false) {
                echo json_encode(array('error' => 1, 'message' => validation_errors()));
            } else {
                $data['isHold'] = 1;
                $data['holdByUserID'] = current_pc();
                $data['holdByUsername'] = current_user();
                $data['holdPC'] = current_pc();
                $data['holdDatetime'] = format_date_mysql_datetime();
                $data['holdRemarks'] = $this->input->post('holdReference');
                $pinBasedAccess = $this->pos_policy->pinBasedAccess();
                if ($pinBasedAccess) {
                    $data['waiterID'] = $this->input->post('selectedWaiter');
                }

                $this->update_posListItems();
                $result = $this->Pos_restaurant_model->update_srp_erp_pos_menusalesmaster($data, $id);


                if ($result) {
                    echo json_encode(array('error' => 0, 'message' => 'Receipt hold successfully'));
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'error, while updating'));
                }
            }
        } else {
            echo json_encode(array('error' => 1, 'message' => '<strong>Invoice Not created.</strong> <br>Please create the invoice and hold the receipt.'));
        }
    }

    function load_pos_hold_receipt()
    {
        $warehouseInfo = $this->Pos_restaurant_model->get_wareHouse();
        $company_info = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['is_wowfood_enabled'] = $this->Api_wowfood_model->is_wowfood_enabled($company_info['company_id'], $warehouseInfo['wareHouseAutoID'], '18');
        $data['holdReceipt'] = null;
        $data['currency'] = $this->Pos_restaurant_model->get_currency();
        $data['pinBasedAccess'] = $pinBasedAccess = $this->pos_policy->pinBasedAccess();
        if ($data['pinBasedAccess']) {
            $data['waiters'] = $this->Pos_restaurant_model->getWaiters($warehouseInfo['wareHouseAutoID']);
        }
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-openHold', $data);
    }

    function load_pos_hold_receipt_tablet()
    {
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-openHold-tablet', $data);
    }

    function load_kitchen_ready()
    {
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-kitchen-ready', $data);
    }

    function load_kitchen_ready_tablet()
    {
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-kitchen-ready-tablet', $data);
    }

    function loadHoldListPOS()
    {
        $orderType = $this->input->post('orderType', true);
        $waiter = $this->input->post('waiter', true);
        if ($orderType != 'all') {
            $companyID = current_companyID();
            $cusMaterTypeQry = $this->db->query("select * from srp_erp_customertypemaster where customerDescription='$orderType' and company_id=$companyID");
            if ($cusMaterTypeQry->num_rows() > 0) {
                $orderTypeId = $cusMaterTypeQry->row()->customerTypeID;
            } else { //this is for avoid errors if customer type not configured for the specific company.
                $orderTypeId = -1; //all
            }
        } else {
            $orderTypeId = -1; //all
        }

        if ($waiter != 'clear') {
            //
        } else {
            $waiter = -1; //all
        }

        $this->datatables->select('master.menuSalesID as menuSalesID, master.wareHouseAutoID as wareHouseAutoID, master.invoiceCode as invoiceCode, netTotal as netTotal, holdByUsername as createdUser, holdDatetime  as holdDate, if(ISNULL(holdRemarks), "auto_remarks", holdRemarks) as remarks,  master.createdDateTime as createdDate, dt.diningTableDescription, master.BOT as BOT, master.isFromTablet as isFromTablet', false)
            ->from('srp_erp_pos_menusalesmaster master')
            ->join('srp_erp_pos_deliveryorders d', 'd.menuSalesMasterID = master.menuSalesID', 'left')
            ->join('srp_erp_pos_diningtables dt', 'dt.diningTableAutoID = master.tableID', 'left')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_openHold(menuSalesID,\'open\',wareHouseAutoID)')
            ->add_column('openHoldPreview', '$1', 'btn_hold_preview(menuSalesID,\'open\',wareHouseAutoID)')
            ->add_column('status', '$1', 'status_BOT(BOT,isFromTablet)')
            ->add_column('amount', '$1', 'get_hold_bill_amount(menuSalesID)')
            ->add_column('waiterName', '$1', 'getWaiterNameColumnByMenuSalesID(menuSalesID)')
            ->where('master.isHold', 1)
            ->where('master.wowFoodYN !=', 1)
            ->where('master.isCancelled', 0)
            ->where('master.companyID', current_companyID())
            ->where('master.wareHouseAutoID', get_outletID())
            ->where('d.deliveryOrderID IS NULL');
        if ($orderTypeId != -1) {
            $this->datatables->where('master.customerTypeID', $orderTypeId);
        }
        if ($waiter != -1) {
            $this->datatables->where('master.waiterID', $waiter);
        }
        $this->db->order_by('menuSalesID', 'DESC');
        echo $this->datatables->generate();
    }

    function loadHoldListPOS_tablet()
    {
        $this->datatables->select('master.menuSalesID as menuSalesID, master.wareHouseAutoID as wareHouseAutoID , master.invoiceCode as invoiceCode, netTotal as netTotal, holdByUsername as createdUser, holdDatetime  as holdDate, if(ISNULL(holdRemarks), "auto_remarks", holdRemarks) as remarks,  master.createdDateTime as createdDate, dt.diningTableDescription', false)
            ->from('srp_erp_pos_menusalesmaster master')
            ->join('srp_erp_pos_deliveryorders d', 'd.menuSalesMasterID = master.menuSalesID', 'left')
            ->join('srp_erp_pos_diningtables dt', 'dt.diningTableAutoID = master.tableID', 'left')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_openHold(menuSalesID,\'open\',wareHouseAutoID)')
            ->where('master.isHold', 1)
            ->where('master.companyID', current_companyID())
            ->where('master.wareHouseAutoID', get_outletID())
            ->where('master.BOT', 0)
            ->where('master.createdUserID', current_userID())
            ->where('d.deliveryOrderID IS NULL');
        echo $this->datatables->generate();
    }

    function loadDeliveryOrderPending()
    {
        $this->datatables->select('master.menuSalesID as menuSalesID,  master.wareHouseAutoID as wareHouseAutoID, master.invoiceCode as invoiceCode, netTotal as netTotal, holdByUsername as createdUser, holdDatetime  as holdDate, if(ISNULL(holdRemarks), "auto_remarks", holdRemarks) as remarks,  master.createdDateTime as createdDate, c.customerTelephone as customerTelephone, c.CustomerName as CustomerName', false)
            ->from('srp_erp_pos_menusalesmaster master')
            ->join('srp_erp_pos_deliveryorders d', 'd.menuSalesMasterID = master.menuSalesID', 'left')
            ->join('srp_erp_pos_customermaster c', 'd.posCustomerAutoID = c.posCustomerAutoID ', 'left')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_openHold(menuSalesID,\'open\',wareHouseAutoID)')
            ->add_column('amount', '$1', 'get_hold_bill_amount(menuSalesID)')
            ->where('master.isHold', 1)
            ->where('master.companyID', current_companyID())
            ->where('master.wareHouseAutoID', get_outletID())
            ->where('d.deliveryOrderID IS NOT NULL');
        $this->db->order_by('menuSalesID', 'DESC');
        echo $this->datatables->generate();
    }

    function loadKitchenReady()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];

        $this->datatables->select('menuSalesID as menuSalesID, invoiceCode as invoiceID, holdByUsername as createdUser, if(ISNULL(holdDatetime), "-",DATE_FORMAT(holdDatetime,\'%d-%b-%Y\'))  as holdDate, if(ISNULL(holdRemarks), "-", holdRemarks) as remarks, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, isHold as isHold , isOrderPending as isOrderPending, isOrderInProgress as isOrderInProgress, isOrderCompleted as isOrderCompleted', false)
            ->from('srp_erp_pos_menusalesmaster master')
            //->add_column('invoiceID', '$1', 'get_pos_invoice_code(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_viewKitchenStatus(menuSalesID,\'Open\')')
            ->add_column('status', '$1', 'kitchen_status(isHold,isOrderPending,isOrderInProgress,isOrderCompleted,PN)')
            // ->where('master.isHold', 1)
            ->where('master.isOrderPending', 1);
        //->where('master.isOrderCompleted', 1)
        //->where('master.isOrderCompleted', format_date_mysql_datetime())
        //$this->datatables->like('createdDateTime', date('Y-m-d'));

        $this->datatables->where('master.shiftID', $shiftID);
        $this->datatables->where('master.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    function load_menusalesmaster_data()
    {
        $id = $this->input->post('id');
        //srp_erp_pos_menusalesmaster
        $query = $this->db->query("SELECT * FROM `srp_erp_pos_menusalesmaster` WHERE menuSalesID='$id'");
        echo json_encode($query->row());
    }

    function openHold_sales()
    {
        $id = $this->input->post('id');
        $outletID = $this->input->post('outletID');
        //$outletID = get_outletID();
        if (!empty($id)) {
            set_session_invoiceID($id);
            $this->updateShift($id);

            $this->db->select('*');
            $this->db->from('srp_erp_pos_deliveryorders');
            $this->db->where('menuSalesMasterID', $id);
            $deliveryOrderID = $this->db->get()->row('deliveryOrderID');
            if ($deliveryOrderID) {
                $delivery = 1;
            } else {
                $delivery = 0;
            }

            $q = "SELECT SUM(amount) as tmpAmount FROM srp_erp_pos_menusalespayments WHERE menuSalesID = '" . $id . "'";
            $advancePayment = $this->db->query($q)->row('tmpAmount');
            //            $payment_query = $this->db->query("SELECT * FROM `srp_erp_pos_menusalespayments` WHERE menuSalesID=$id");
            echo json_encode(array('error' => 0, 'message' => 'set session', 'code' => get_pos_invoice_code($id, $outletID), 'advancePayment' => $advancePayment, 'isDeliveryOrder' => $delivery, 'deliveryOrderID' => $deliveryOrderID));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'ID Not found'));
        }
    }

    function updateShift($menuSalesID)
    {
        $outletID = get_outletID();
        $get_shift = $this->Pos_restaurant_model->get_srp_erp_pos_shiftdetails_employee();
        $currentShiftID = $get_shift['shiftID'];
        $data['shiftID'] = $currentShiftID;
        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->where('id_store', $outletID);
        $result = $this->db->update('srp_erp_pos_menusalesmaster', $data);
        return $result;
    }

    function load_packItemList()
    {
        $warehouseMenuID = $this->input->post('id');

        $warehouseMenu = $this->Pos_restaurant_model->get_warehouseMenuItem($warehouseMenuID);
        $packItemDetail = $this->Pos_restaurant_model->get_packGroup_menuItem($warehouseMenu['menuMasterID']);
        $data['id'] = $warehouseMenuID;
        $data['menuMasterID'] = $warehouseMenu['menuMasterID'];
        $data['warehouseMenu'] = $warehouseMenu;
        $data['packItemDetail'] = $packItemDetail;

        $this->load->view('system/pos/posRestaurant/terminal/ajax-load-packDetail', $data);
    }

    function savePackDetailItemList()
    {

        $id = $this->input->post('id');
        $pack_menuID = $this->input->post('pack_menuID');


        $menuSalesID = isPos_invoiceSessionExist();
        $warehouseMenuID = $this->input->post('warehouseMenuID');
        $menuMasterID = $this->input->post('menuMasterID');
        $menuSalesItemID = $this->input->post('menuSalesItemID');

        $createdBy = current_userID();
        $createdPc = current_pc();
        $createdDatetime = format_date_mysql_datetime();


        $data = array();

        $i = 0;

        /* Required Items */
        $requiredItem = $this->Pos_restaurant_model->get_srp_erp_pos_menupackitem_requiredItems($menuMasterID);
        if (!empty($requiredItem)) {


            foreach ($requiredItem as $item) {
                $data[$i]['menuSalesID'] = $menuSalesID;
                $data[$i]['menuMasterID'] = $menuMasterID;
                $data[$i]['warehouseMenuID'] = $warehouseMenuID;
                $data[$i]['menuPackItemID'] = $item['packgroupdetailID'];
                $data[$i]['menuID'] = $item['menuID'];
                $data[$i]['menuSalesItemID'] = $menuSalesItemID;
                $data[$i]['isRequired'] = 1;
                $data[$i]['qty'] = 1;
                $data[$i]['createdBy'] = $createdBy;
                $data[$i]['createdPc'] = $createdPc;
                $data[$i]['createdDatetime'] = $createdDatetime;
                $data[$i]['timestamp'] = $createdDatetime;
                $i++;
            }
        }

        /* Optional Item */
        if (!empty($id)) {

            foreach ($id as $key => $value) {


                if ($value > 0) {
                    $data[$i]['menuSalesID'] = $menuSalesID;
                    $data[$i]['menuMasterID'] = $menuMasterID;
                    $data[$i]['warehouseMenuID'] = $warehouseMenuID;
                    $data[$i]['menuPackItemID'] = $key;
                    $data[$i]['menuID'] = $pack_menuID[$key];
                    $data[$i]['menuSalesItemID'] = $menuSalesItemID;
                    $data[$i]['isRequired'] = 0;
                    $data[$i]['qty'] = $value;
                    $data[$i]['createdBy'] = $createdBy;
                    $data[$i]['createdPc'] = $createdPc;
                    $data[$i]['createdDatetime'] = $createdDatetime;
                    $data[$i]['timestamp'] = $createdDatetime;
                    $i++;
                }
            }
        }
        //$data = array_values($data);


        if (!empty($data)) {

            $this->Pos_restaurant_model->bulk_insert_srp_erp_pos_valuepackdetail($data);
            echo json_encode(array('error' => 0, 'message' => 'added', $data));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'data not received'));
        }
    }

    function updateCustomerType()
    {
        $customerType = $this->input->post('customerType');

        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $data['customerTypeID'] = $customerType;
            $result = $this->Pos_restaurant_model->update_srp_erp_pos_menusalesmaster($data, $invoiceID);
            if ($result) {
                echo json_encode(array('error' => '0', 'message' => 'customer type updated'));
            } else {
                echo json_encode(array('error' => '1', 'message' => 'not updated'));
            }
        } else {
            echo json_encode(array('error' => '1', 'message' => 'not updated'));
        }
    }

    function updateCustomerTypeH()
    {
        $customerType = $this->input->post('customerType');

        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $data['customerTypeID'] = $customerType;
            $result = $this->Pos_restaurant_model->update_srp_erp_pos_menusalesmaster($data, $invoiceID);
            if ($result) {
                echo json_encode(array('error' => '0', 'message' => 'customer type updated'));
            } else {
                echo json_encode(array('error' => '1', 'message' => 'not updated'));
            }
        } else {
            $get_shift = $this->Pos_restaurant_model->get_srp_erp_pos_shiftdetails_employee();
            $warehouseDetail = $this->get_wareHouse();
            $SN = generate_pos_invoice_no();
            $data['customerTypeID'] = $customerType;
            $data['documentSystemCode'] = '';
            $data['documentCode'] = '';
            $data['serialNo'] = $SN;
            $data['invoiceSequenceNo'] = $SN;
            $data['invoiceCode'] = generate_pos_invoice_code();
            $data['customerID'] = '';
            $data['customerCode'] = '';
            $data['shiftID'] = $get_shift['shiftID'];

            if ($this->input->post('tabOrder') == 1) {
                $data['counterID'] = null;
                $data['isHold'] = -1;
                $data['tabUserID'] = current_userID();
            } else {
                $data['counterID'] = $get_shift['counterID'];
            }

            $data['menuSalesDate'] = format_date_mysql_datetime();
            $data['holdDatetime'] = format_date_mysql_datetime();
            $data['companyID'] = current_companyID();
            $data['companyCode'] = current_companyCode();

            $data['subTotal'] = '';
            $data['discountPer'] = '';
            $data['discountAmount'] = '';
            $data['netTotal'] = '';

            $data['wareHouseAutoID'] = $get_shift['wareHouseID'];

            $data['segmentID'] = $warehouseDetail['segmentID'];
            $data['segmentCode'] = $warehouseDetail['segmentCode'];

            $data['salesDay'] = date('l');
            $data['salesDayNum'] = date('w');


            $tr_currency = $this->common_data['company_data']['company_default_currency'];
            $transConversion = currency_conversion($tr_currency, $tr_currency);

            $data['transactionCurrencyID'] = $transConversion['currencyID'];
            $data['transactionCurrency'] = $transConversion['CurrencyCode'];
            $data['transactionExchangeRate'] = $transConversion['conversion'];
            $data['transactionCurrencyDecimalPlaces '] = $transConversion['DecimalPlaces'];

            $defaultCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
            $defaultConversion = currency_conversionID($transConversion['currencyID'], $defaultCurrencyID);

            $data['companyLocalCurrencyID'] = $defaultCurrencyID;
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = $defaultConversion['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];


            $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
            $transConversion = currency_conversionID($transConversion['currencyID'], $repCurrencyID);

            $data['companyReportingCurrencyID'] = $repCurrencyID;
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $data['companyReportingExchangeRate'] = $transConversion['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_reporting_decimal'];


            /*update the transaction currency detail for later use */
            $tr_currency = $this->common_data['company_data']['company_default_currency'];
            $customerCurrencyConversion = currency_conversion($tr_currency, $tr_currency);

            $data['customerCurrencyID'] = $customerCurrencyConversion['currencyID'];
            $data['customerCurrency'] = $customerCurrencyConversion['CurrencyCode'];
            $data['customerCurrencyExchangeRate'] = $customerCurrencyConversion['conversion'];
            $data['customerCurrencyDecimalPlaces'] = $customerCurrencyConversion['DecimalPlaces'];


            /*Audit Data */
            $data['createdUserGroup'] = current_user_group();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdUserName'] = current_user();
            $data['createdDateTime'] = format_date_mysql_datetime();
            $data['modifiedPCID'] = '';
            $data['modifiedUserID'] = '';
            $data['modifiedUserName'] = '';
            $data['modifiedDateTime'] = '';
            $data['timestamp'] = format_date_mysql_datetime();
            $data['id_store'] = $this->config->item('id_store');
            $data['isFromTablet'] = $this->input->post('isFromTablet');
            $data['paymentMethod'] = 1;


            $invoiceID = $this->Pos_restaurant_model->insert_srp_erp_pos_menusalesmaster($data);
            set_session_invoiceID($invoiceID);
            echo json_encode(array('error' => '1', 'message' => 'customer type updated'));
        }
    }

    function loadPaymentSalesReport()
    {
        $tmpFilterDate = $this->input->post('filterFrom');
        $tmpFilterDateTo = $this->input->post('filterTo');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFilterDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $filterDate = $date;
        } else {
            $filterDate = date('Y-m-d');
        }


        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d');
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


        $customerTypeCount = $this->Pos_restaurant_model->get_report_customerTypeCount($filterDate, $date2, $cashier, $Outlets);
        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount($filterDate, $date2, $cashier, $Outlets);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion($filterDate, $date2, $cashier, $Outlets);

        $lessAmounts = array_merge($lessAmounts, $lessAmounts_promotion);
        $paymentMethod = $this->Pos_restaurant_model->get_report_paymentMethod($filterDate, $date2, $cashier, $Outlets);

        $data['companyInfo'] = $companyInfo;
        $data['customerTypeCount'] = $customerTypeCount;
        $data['lessAmounts'] = $lessAmounts;
        $data['paymentMethod'] = $paymentMethod;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();

        $this->load->view('system/pos/reports/pos-payment-sales-report', $data);
    }

    function loadPaymentSalesReport2()
    {
        $_POST['outletID'] = get_outletID();
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
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


        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount2($filterDate, $date2, $cashier);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion2($filterDate, $date2, $cashier);
        $lessAmounts_discounts = $this->Pos_restaurant_model->get_report_salesReport_discount($filterDate, $date2, $cashier);
        $lessAmounts_discountsJavaApp = $this->Pos_restaurant_model->get_report_salesReport_javaAppDiscount($filterDate, $date2, $cashier);
        $outlets = $_POST['outletID'];
        $lessAmounts_discounts_item_wise = $this->Pos_restaurant_model->get_report_salesReport_discount_item_wise_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmountsAll = array_merge($lessAmounts_discounts, $lessAmounts, $lessAmounts_promotion, $lessAmounts_discountsJavaApp, $lessAmounts_discounts_item_wise);


        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_restaurant_model->get_report_paymentMethod2($filterDate, $date2, $cashier);;
        //$data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount2($filterDate, $date2, $cashier);
        $data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount2_new($filterDate, $date2, $cashier);
        $data['lessAmounts'] = $lessAmountsAll;


        // var_dump($lessAmountsAll);
        $data['totalSales'] = $this->Pos_restaurant_model->get_report_salesReport_totalSales($filterDate, $date2, $cashier);
        $data['totalTaxes'] = $this->Pos_restaurant_model->get_report_salesReport_totalTaxes($filterDate, $date2, $cashier);
        $data['totalServiceCharge'] = $this->Pos_restaurant_model->get_report_salesReport_ServiceCharge($filterDate, $date2, $cashier);
        $data['giftCardTopUp'] = $this->Pos_restaurant_model->get_report_giftCardTopUp($filterDate, $date2, $cashier);
        $data['voidBills'] = $this->Pos_restaurant_model->get_report_voidBills($filterDate, $date2, $cashier);
        $outletID = get_outletID();
        $data['creditSales'] = $this->Pos_restaurant_model->get_report_creditSales($filterDate, $date2, $cashier, $outletID);
        $data['fullyDiscountBill'] = $this->Pos_restaurant_model->get_report_fullyDiscountBills_admin($filterDate, $date2, $cashier, $outlets);

        //var_dump($data['voidBills']);
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();
        $html = $this->load->view('system/pos/reports/pos-payment-sales-report2', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $data['pdf'] = 'pdf';
            $html = $this->load->view('system/pos/reports/pos-payment-sales-report2', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }


    function loadPaymentSalesReport3()
    {
        $_POST['outletID'] = array(get_outletID());
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID');
        $post = $this->input->post();


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


        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts = $this->Pos_restaurant_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts_item_wise = $this->Pos_restaurant_model->get_report_salesReport_discount_item_wise_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discountsJavaApp = $this->Pos_restaurant_model->get_report_salesReport_javaAppDiscount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmountsAll = array_merge($lessAmounts_discounts, $lessAmounts, $lessAmounts_promotion, $lessAmounts_discountsJavaApp, $lessAmounts_discounts_item_wise);

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_restaurant_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);

        $data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets);
        $data['lessAmounts'] = $lessAmountsAll;


        // var_dump($lessAmountsAll);
        $data['totalSales'] = $this->Pos_restaurant_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalTaxes'] = $this->Pos_restaurant_model->get_report_salesReport_totalTaxes_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalServiceCharge'] = $this->Pos_restaurant_model->get_report_salesReport_ServiceCharge_admin($filterDate, $date2, $cashier, $outlets);
        $data['giftCardTopUp'] = $this->Pos_restaurant_model->get_report_giftCardTopUp_admin($filterDate, $date2, $cashier, $outlets);
        $data['voidBills'] = $this->Pos_restaurant_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets);
        $data['creditSales'] = $this->Pos_restaurant_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets);
        $data['fullyDiscountBill'] = $this->Pos_restaurant_model->get_report_fullyDiscountBills_admin($filterDate, $date2, $cashier, $outlets);

        //var_dump($data['voidBills']);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $html = $this->load->view('system/pos/reports/pos-payment-sales-report3', $data, true);
        echo $html;
    }

    function loadItemizedSalesReport()
    {
        $_POST['outletID'] = get_outletID();
        $this->form_validation->set_rules('cashier[]', 'cashier', 'trim|required');
        $this->form_validation->set_rules('filterFrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('filterTo', 'Date To', 'trim|required');
        $this->form_validation->set_rules('orderType[]', 'Order Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo '<div class="alert alert-danger">' . $errors . '</div>';
        } else {
            $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
            $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
            $outlet = $this->input->post('outlet');
            $filterTo = $this->input->post('filterTo');
            $tmpCashierSource = $this->input->post('cashier');
            $orderTypeIDs = $this->input->post('orderType');

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

            if (isset($orderTypeIDs) && !empty($orderTypeIDs)) {
                $orderTypes = join(",", $orderTypeIDs);
            } else {
                $orderTypes = null;
            }

            $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
            $itemizedSalesReport = $this->Pos_restaurant_model->get_itemizedSalesReport($filterDate, $date2, $Outlets, $cashier, $orderTypes);

            $data['companyInfo'] = $companyInfo;
            $data['itemizedSalesReport'] = $itemizedSalesReport;
            $data['cashier'] = $tmpCashierSource;
            $data['cashierTmp'] = get_cashiers();


            $this->load->view('system/pos/reports/pos-itemized-sales-report', $data);
        }
    }

    function load_item_wise_sales_report_admin()
    {
        $this->form_validation->set_rules('cashier[]', 'cashier', 'trim|required');
        $this->form_validation->set_rules('filterFrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('filterTo', 'Date To', 'trim|required');
        $this->form_validation->set_rules('orderType[]', 'Order Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $errors = validation_errors();
            echo '<div class="alert alert-danger">' . $errors . '</div>';
        } else {
            $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
            $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
            $outlet = $this->input->post('outletID_f');
            $filterTo = $this->input->post('filterTo');
            $tmpCashierSource = $this->input->post('cashier');
            $orderTypeIDs = $this->input->post('orderType');

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

            if (isset($orderTypeIDs) && !empty($orderTypeIDs)) {
                $orderTypes = join(",", $orderTypeIDs);
            } else {
                $orderTypes = null;
            }

            $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
            $itemizedSalesReport = $this->Pos_restaurant_model->get_itemizedSalesReport($filterDate, $date2, $Outlets, $cashier, $orderTypes);


            $data['companyInfo'] = $companyInfo;
            $data['itemizedSalesReport'] = $itemizedSalesReport;
            $data['cashier'] = $tmpCashierSource;
            $data['cashierTmp'] = get_cashiers();
            //$data['cashier'] = $this;
            /*echo '<pre>';
            print_r($itemizedSalesReport);
            echo '</pre>';*/

            $this->load->view('system/pos/reports/pos-itemized-sales-report-admin', $data);
        }
    }

    function close_shift_touchWindow()
    {
        $mySession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $data['isClosed'] = 1;
        $data['endTime'] = format_date_mysql_datetime();
        $data['modifiedPCID'] = current_pc();
        $data['modifiedUserID'] = current_userID();
        $data['modifiedDateTime'] = format_date_mysql_datetime();
        $data['modifiedUserName'] = current_user();
        $this->db->where('shiftID', $mySession['shiftID']);
        $this->db->where('counterID', null);
        $result = $this->db->update('srp_erp_pos_shiftdetails', $data);
        echo json_encode(array('status' => $result));
    }


    function touchWindow()
    {
        $outletID = get_outletID();
        if ($outletID) {

            $isHaveSession = $this->Pos_restaurant_model->isHaveNotClosedSession_tabUsers();

            if (!empty($isHaveSession)) {

                $mySession = $this->Pos_restaurant_model->isHaveNotClosedSession();
                if (empty($mySession)) {
                    $this->Pos_restaurant_model->create_tmp_session($isHaveSession['shiftID']);
                }


                $tmpWarehouseID = $this->Pos_restaurant_model->get_srp_erp_warehouse_users_WarehouseID();
                $warehouseID = isset($tmpWarehouseID) && !empty($tmpWarehouseID) ? $tmpWarehouseID : 0;

                /** Get Warehouse Menu Items */
                $output = $this->Pos_restaurant_model->get_warehouseMenues($warehouseID);

                /** Get warehouse Category */
                $output2 = $this->Pos_restaurant_model->get_warehouseCategory($warehouseID);

                /** Get warehouse Sub Category */
                $output3 = $this->Pos_restaurant_model->get_warehouseSubCategory($warehouseID);

                $invCodeDet = $this->Pos_restaurant_model->getInvoiceCode();
                $data['title'] = 'POS';
                $data['extra'] = 'sidebar-collapse fixed';
                $data['refNo'] = $invCodeDet['refCode'];
                $data['menuItems'] = $output;
                $data['menuCategory'] = $output2;
                $data['menuSubCategory'] = $output3;
                $wareHouseData = $this->Pos_restaurant_model->get_wareHouse();
                $data['posData'] = array(
                    'wareHouseLocation' => $wareHouseData['wareHouseLocation'],
                    'counterDet' => '',
                );
                $data['common_data'] = $this->common_data;
                $defaultCustomerType = defaultCustomerType();
                $data['defaultCustomerType'] = !empty($defaultCustomerType) ? $defaultCustomerType : null;
                $data['warehouseID'] = $warehouseID;
                $data['isPriceRequired'] = $this->pos_policy->isPriceRequired();
                $data['tables_list'] = $this->Pos_restaurant_model->get_tableList();
                $data['sampleBillPolicy'] = $this->pos_policy->isSampleBillRequired();
                $data['pinBasedAccess'] = $this->pos_policy->pinBasedAccess();
                $data['waiters'] = $this->Pos_restaurant_model->getWaiters($warehouseID);
                $data['dineInId'] = $this->Pos_restaurant_model->dineInId($warehouseID);
                $this->load->view('system/pos/pos_restaurant_touch', $data);
            } else {
                $this->close_shift_touchWindow();
                $data['error_message'] = 'All counters are closed!';
                $this->load->view('system/pos/pos_restaurant_touch_errors', $data);
            }
        } else {
            $data['error_message'] = 'Outlet is not configured to this user.';
            $this->load->view('system/pos/pos_restaurant_touch_errors', $data);
        }
    }


    function updaterestaurantTable()
    {
        $tableType = $this->input->post('tableType');

        $invoiceID = isPos_invoiceSessionExist();
        if ($invoiceID) {
            $data['tableID'] = $tableType;
            $result = $this->Pos_restaurant_model->update_srp_erp_pos_updaterestaurantTable($data, $invoiceID);
            if ($result) {
                echo json_encode(array('error' => '0', 'message' => 'Table updated'));
            } else {
                echo json_encode(array('error' => '1', 'message' => 'not updated'));
            }
        } else {
            echo json_encode(array('error' => '1', 'message' => 'not updated'));
        }
    }

    function saveMenuSalesItemRemarkes()
    {
        $menuSalesItemID = $this->input->post('itmID');
        $menuSalesID = $this->input->post('invoiceIDMenusales');
        $menuItemRemarkes = $this->input->post('menuItemRemarkes');

        if (!empty($menuItemRemarkes)) {
            $data['remarkes'] = $menuItemRemarkes;

            $result = $this->Pos_restaurant_model->saveMenuSalesItemRemarkes($data, $menuSalesItemID, $menuSalesID);
            if ($result) {
                echo json_encode(array('error' => '0', 'message' => 'Remakes updated', 'invoiceID' => $menuSalesID));
            } else {
                echo json_encode(array('error' => '1', 'message' => 'not updated'));
            }
        } else {
            echo json_encode(array('error' => '1', 'message' => 'Please Enter Remakes'));
        }
    }

    function get_add_on_list()
    {
        $menuSalesItemID = $this->input->post('menuSalesItemID');
        $data["adonlist"] = $this->Pos_restaurant_model->get_add_on_list($menuSalesItemID);
        $data["menuSalesItemID"] = $this->input->post('menuSalesItemID');
        $this->load->view('system/pos/ajax/ajax_pos_load_add_on_list_view', $data);
    }


    function saveAddon()
    {
        echo json_encode($this->Pos_restaurant_model->saveAddon());
    }

    function updateQty()
    {
        $result = $this->Pos_restaurant_model->updateQty();
        $billNo = isPos_invoiceSessionExist();
        if ($billNo) {
            $this->updateNetTotalForInvoice($billNo);
        }

        echo json_encode($result);
    }

    function save_send_pos_email()
    {
        $this->Pos_restaurant_model->save_send_pos_email();
    }

    function load_void_receipt()
    {
        //$output = $this->Pos_restaurant_model->load_posHoldReceipt();
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-pos-void', $data);
    }

    function loadVoidOrders()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];
        $companyID = $counterData['companyID'];
        $outletID = $counterData['wareHouseID'];
        $userID = current_userID();

        $user = $this->db->query("select * from srp_erp_warehouse_users where isActive=1 and wareHouseID=$outletID and userID=$userID")->row();


        $from = $this->input->post('datefrom');
        $fromDate = date('Y-m-d', strtotime($from));
        $to = $this->input->post('dateto');
        $toDate = date('Y-m-d', strtotime($to));

        if ($user->superAdminYN) {
            $this->datatables->select('menuSalesID as menuSalesID, wareHouseAutoID as wareHouseAutoID, subTotal as subTotal, invoiceCode as invoiceCode, DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,\'%d-%b-%Y\') as createdDate, srp_erp_pos_menusalesmaster.createdUserName', false)
                ->from('srp_erp_pos_menusalesmaster')
                ->join('srp_erp_pos_shiftdetails', 'srp_erp_pos_shiftdetails.shiftID = srp_erp_pos_menusalesmaster.shiftID')
                ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
                ->add_column('voidBill', '$1', 'btn_voidBill(menuSalesID,\'View\',wareHouseAutoID)')
                //->add_column('subTotal', '$1', 'column_numberFormat(subTotal)')
                ->edit_column('subTotal', '<div>$1</div>', 'column_numberFormat(subTotal)')
                ->where('srp_erp_pos_shiftdetails.isClosed', 0)
                ->where('isHold', 0)
                ->where('isVoid', 0)
                ->where('srp_erp_pos_menusalesmaster.companyID', $companyID);
            echo $this->datatables->generate();
        } elseif ($user->wareHouseAdminYN) {
            $this->datatables->select('menuSalesID as menuSalesID, wareHouseAutoID as wareHouseAutoID, subTotal as subTotal, invoiceCode as invoiceCode, DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,\'%d-%b-%Y\') as createdDate, srp_erp_pos_menusalesmaster.createdUserName', false)
                ->from('srp_erp_pos_menusalesmaster')
                ->join('srp_erp_pos_shiftdetails', 'srp_erp_pos_shiftdetails.shiftID = srp_erp_pos_menusalesmaster.shiftID')
                ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
                ->add_column('voidBill', '$1', 'btn_voidBill(menuSalesID,\'View\',wareHouseAutoID)')
                //->add_column('subTotal', '$1', 'column_numberFormat(subTotal)')
                ->edit_column('subTotal', '<div>$1</div>', 'column_numberFormat(subTotal)')
                ->where('srp_erp_pos_shiftdetails.isClosed', 0)
                ->where('isHold', 0)
                ->where('isVoid', 0)
                ->where('srp_erp_pos_menusalesmaster.companyID', $companyID)
                ->where('srp_erp_pos_menusalesmaster.wareHouseAutoID', $outletID);
            echo $this->datatables->generate();
        } else {
            $this->datatables->select('menuSalesID as menuSalesID, wareHouseAutoID as wareHouseAutoID, subTotal as subTotal, invoiceCode as invoiceCode, DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,\'%d-%b-%Y\') as createdDate, srp_erp_pos_menusalesmaster.createdUserName', false)
                ->from('srp_erp_pos_menusalesmaster')
                ->join('srp_erp_pos_shiftdetails', 'srp_erp_pos_shiftdetails.shiftID = srp_erp_pos_menusalesmaster.shiftID')
                ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
                ->add_column('voidBill', '$1', 'btn_voidBill(menuSalesID,\'View\',wareHouseAutoID)')
                //->add_column('subTotal', '$1', 'column_numberFormat(subTotal)')
                ->edit_column('subTotal', '<div>$1</div>', 'column_numberFormat(subTotal)')
                ->where('srp_erp_pos_shiftdetails.isClosed', 0)
                ->where('isHold', 0)
                ->where('isVoid', 0)
                // ->where('DATE_FORMAT( createdDateTime ,\'%Y-%m-%d\')  BETWEEN "' . $fromDate . '" AND "' . $toDate . '"')
                ->where('srp_erp_pos_menusalesmaster.shiftID', $shiftID)
                ->where('srp_erp_pos_menusalesmaster.companyID', $companyID)
                ->where('srp_erp_pos_menusalesmaster.wareHouseAutoID', $outletID);
            echo $this->datatables->generate();
        }


        //echo $this->db->last_query();
    }

    function void_bill()
    {
        echo json_encode($this->Pos_restaurant_model->void_bill());
    }

    function un_void_bill()
    {
        echo json_encode($this->Pos_restaurant_model->un_void_bill());
    }

    function loadVaoidOrderHistory()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];
        $companyID = $counterData['companyID'];
        $outletID = $counterData['wareHouseID'];

        $userID = current_userID();
        $user = $this->db->query("select * from srp_erp_warehouse_users where isActive=1 and wareHouseID=$outletID and userID=$userID")->row();

        $fromDate = date('Y-m-d', time());
        $toDate = date('Y-m-d', time());

        if ($user->superAdminYN) {
            $this->datatables->select('menuSalesID as menuSalesID, wareHouseAutoID as wareHouseAutoID, invoiceCode as invoiceCode, subTotal as netTotal, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, createdUserName', false)
                ->from('srp_erp_pos_menusalesmaster')
                ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
                ->add_column('voidBill', '$1', 'btn_voidBillHistory(menuSalesID, wareHouseAutoID)')
                ->add_column('netTotalDisplay', '$1', 'column_numberFormat(netTotal)')
                ->where('isHold', 0)
                ->where('isVoid', 1)
                ->where('companyID', $companyID);
            echo $this->datatables->generate();
        } elseif ($user->wareHouseAdminYN) {
            $this->datatables->select('menuSalesID as menuSalesID, wareHouseAutoID as wareHouseAutoID, invoiceCode as invoiceCode, subTotal as netTotal, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, createdUserName', false)
                ->from('srp_erp_pos_menusalesmaster')
                ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
                ->add_column('voidBill', '$1', 'btn_voidBillHistory(menuSalesID, wareHouseAutoID)')
                ->add_column('netTotalDisplay', '$1', 'column_numberFormat(netTotal)')
                ->where('isHold', 0)
                ->where('isVoid', 1)
                ->where('companyID', $companyID)
                ->where('wareHouseAutoID', $outletID);
            echo $this->datatables->generate();
        } else {
            $this->datatables->select('menuSalesID as menuSalesID, wareHouseAutoID as wareHouseAutoID, invoiceCode as invoiceCode, subTotal as netTotal, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, createdUserName', false)
                ->from('srp_erp_pos_menusalesmaster')
                ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
                ->add_column('voidBill', '$1', 'btn_voidBillHistory(menuSalesID, wareHouseAutoID)')
                ->add_column('netTotalDisplay', '$1', 'column_numberFormat(netTotal)')
                ->where('isHold', 0)
                ->where('isVoid', 1)
                ->where('shiftID', $shiftID)
                ->where('companyID', $companyID)
                ->where('wareHouseAutoID', $outletID);
            echo $this->datatables->generate();
        }
    }

    function loadPaymentSalesReportPdf()
    {
        $tmpFilterDate = $this->input->post('filterFrom');
        $tmpFilterDateTo = $this->input->post('filterTo');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');
        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFilterDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $filterDate = $date;
        } else {
            $filterDate = date('Y-m-d');
        }


        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d');
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
        $customerTypeCount = $this->Pos_restaurant_model->get_report_customerTypeCount($filterDate, $date2, $cashier, $Outlets);
        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount($filterDate, $date2, $cashier, $Outlets);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion($filterDate, $date2, $cashier, $Outlets);

        /*echo '<pre>';
        print_r($lessAmounts);
        print_r($lessAmounts_promotion);
        echo '</pre>';*/
        $lessAmounts = array_merge($lessAmounts, $lessAmounts_promotion);
        $paymentMethod = $this->Pos_restaurant_model->get_report_paymentMethod($filterDate, $date2, $cashier, $Outlets);

        $data['companyInfo'] = $companyInfo;
        $data['customerTypeCount'] = $customerTypeCount;
        $data['lessAmounts'] = $lessAmounts;
        $data['paymentMethod'] = $paymentMethod;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();
        $data['pdf'] = true;

        $html = $this->load->view('system/pos/reports/pos-payment-sales-report', $data, true);
        //$html = $this->load->view('system/pos/pdf/dashboard/pos-payment-sales-report-pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function loadItemizedSalesReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d 00:00:00', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d 00:00:00');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d 00:00:00', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d 00:00:00');
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
        $itemizedSalesReport = $this->Pos_restaurant_model->get_itemizedSalesReport($dateFrom, $dateTo, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        $data['itemizedSalesReport'] = $itemizedSalesReport;

        $html = $this->load->view('system/pos/pdf/dashboard/pos-itemized-sales-report-pdf', $data, true);
        //echo $html;
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function LoadDeliveryPersonReport()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $customerID = $this->input->post('customerID');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
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
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $deliveryPersonReport = $this->Pos_restaurant_model->get_deliveryPersonReport($dateFrom, $dateTo, $customerID, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['deliveryPersonReport'] = $deliveryPersonReport;
        /*echo '<pre>';
        print_r($itemizedSalesReport);
        echo '</pre>';*/

        $this->load->view('system/pos/reports/pos-delivery-person-report', $data);
    }

    function LoadDiscountReport()
    {
        $tmpFromDate = $this->input->post('startdate');
        $customerID = $this->input->post('customerID');
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
        $deliveryPersonReport = $this->Pos_restaurant_model->get_discountReport($dateFrom, $dateTo, $customerID, $Outlets, $cashier);

        $data['cashierTmp'] = get_cashiers();
        $data['cashier'] = $tmpCashierSource;
        $data['companyInfo'] = $companyInfo;
        $data['deliveryPersonReport'] = $deliveryPersonReport;

        $this->load->view('system/pos/reports/pos-discount-report', $data);
    }

    function loadPrintTemplateVoid()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['voidBtn'] = true;
        $data['closedBill'] = true;
        $data['auth'] = true;

        //$this->load->view('system/pos/printTemplate/restaurant-pos-thermal-printer', $data);
        $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint', $data);
    }

    function loadPrintTemplateVoidHistory()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['closedBill'] = true;
        $data['auth'] = true;
        $data['void'] = true;
        $data['voidBtn'] = false;


        $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint-void', $data);
    }

    function loadPrintTemplateBillHistory()
    {
        $invoiceID = $this->input->post('menuSalesID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['voidBtn'] = false;
        $data['closedBill'] = true;
        $data['auth'] = true;

        //$this->load->view('system/pos/printTemplate/restaurant-pos-thermal-printer', $data);
        // $this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint', $data);
        $template = get_print_template();
        $data['template'] = $template;
        $this->load->view($template, $data);
    }


    function loadDeliveryPersonReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $customerID = $this->input->post('customerID');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');


        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
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
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $deliveryPersonReport = $this->Pos_restaurant_model->get_deliveryPersonReport($dateFrom, $dateTo, $customerID, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['deliveryPersonReport'] = $deliveryPersonReport;

        $html = $this->load->view('system/pos/pdf/dashboard/pos-delivery-person-report-pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }


    function batchJob_checkInvoices($limit = 10)
    {
        $tmpBillID = 0;
        $allInvoices = $this->Pos_restaurant_model->batch_get_srp_erp_pos_menusalesmaster_all($limit);
        $i = 0;
        $outputTxt = '';
        if (!empty($allInvoices)) {
            foreach ($allInvoices as $invoice) {

                $menuSalesID = $invoice['menuSalesID'];
                $netTotal = $invoice['netTotal'];
                $items = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_byMenusalesID($menuSalesID);
                $totalTmp = 0;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $totalTmp += ($item['menuSalesPrice'] * $item['qty']);
                    }
                }

                if ($netTotal != $totalTmp) {
                    $outputTxt .= 'Bill ID: ' . $menuSalesID . ' Bill Total: ' . $netTotal . ' - Item Total :  ' . $totalTmp . "\n";
                    $updateData[$i]['menuSalesID'] = $menuSalesID;
                    $updateData[$i]['netTotal'] = $totalTmp;

                    if (empty($invoice['paidAmount']) || $invoice['paidAmount'] == null || $invoice['paidAmount'] == 0) {
                        $outputTxt .= "Bill ID: " . $menuSalesID . " Paid Amount: " . $invoice['paidAmount'] . "\n";
                        $updateData[$i]['paidAmount'] = $totalTmp;
                    }
                    if (empty($invoice['cashReceivedAmount']) || $invoice['cashReceivedAmount'] == null || $invoice['cashReceivedAmount'] == 0) {
                        $outputTxt .= "Bill ID: " . $menuSalesID . " cashReceived Amount : " . $invoice['cashReceivedAmount'] . "\n";
                        $updateData[$i]['cashReceivedAmount'] = $totalTmp;
                    }
                }


                $i++;
            }

            if (isset($updateData) && !empty($updateData)) {
                /*** update batch Net Total ** */

                $rows = $this->Pos_restaurant_model->batch_update_srp_erp_pos_menusalesmaster($updateData);
                $outputTxt .= "\n\n\n Updated Affected : " . $rows;
                /*** write log ** */

                $logName = "batch_POS_NetTotal_PaidAmount_" . date("Y-m-d_H-i-s", time()) . ".txt";
                $logPath = UPLOAD_PATH_POS . '/batch_logs/' . $logName;

                $myfile = fopen($logPath, "w") or die("Unable to open file!");
                fwrite($myfile, $outputTxt);
                fclose($myfile);

                echo '<a target="_blank" href="' . base_url() . 'batch_logs/' . $logName . '"> Log File </a>';
            }
        } else {
            echo 'empty';
        }
    }

    function batchJob_checkInvoices_paidZero($limit = 10)
    {
        $allInvoices = $this->Pos_restaurant_model->batch_get_srp_erp_pos_menusalesmaster_all($limit);
        $i = 0;
        $outputTxt = '';
        if (!empty($allInvoices)) {
            foreach ($allInvoices as $invoice) {

                $menuSalesID = $invoice['menuSalesID'];
                $items = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_byMenusalesID($menuSalesID);
                $totalTmp = 0;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $totalTmp += ($item['menuSalesPrice'] * $item['qty']);
                    }
                }


                if (empty($invoice['cashReceivedAmount']) || $invoice['cashReceivedAmount'] == null || $invoice['cashReceivedAmount'] == 0) {
                    $updateData[$i]['menuSalesID'] = $menuSalesID;
                    $updateData[$i]['cashReceivedAmount'] = $totalTmp;

                    $outputTxt .= "Bill ID: " . $menuSalesID . " Paid Amount: " . $invoice['paidAmount'] . "\n";
                }

                $i++;
            }

            if (isset($updateData) && !empty($updateData)) {
                /*** update batch Net Total ** */

                $rows = $this->Pos_restaurant_model->batch_update_srp_erp_pos_menusalesmaster($updateData);
                $outputTxt .= "\n\n\n Updated Affected : " . $rows;
                /*** write log ** */

                $logName = "batch_POS_PaidAmount_null_" . date("Y-m-d_H-i-s", time()) . ".txt";
                $logPath = UPLOAD_PATH_POS . '/batch_logs/' . $logName;

                $myfile = fopen($logPath, "w") or die("Unable to open file!");
                fwrite($myfile, $outputTxt);
                fclose($myfile);

                echo '<a target="_blank" href="' . base_url() . 'batch_logs/' . $logName . '"> Log File </a>';
            }
        } else {
            echo 'empty';
        }
    }

    function batchJob_checkInvoices_view($limit = 10)
    {

        $tmpBillID = 0;
        $allInvoices = $this->Pos_restaurant_model->batch_get_srp_erp_pos_menusalesmaster_all($limit);
        $i = 0;
        $outputTxt = '';
        if (!empty($allInvoices)) {
            $updateData = array();
            foreach ($allInvoices as $invoice) {

                $menuSalesID = $invoice['menuSalesID'];
                $netTotal = $invoice['netTotal'];
                $items = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_byMenusalesID($menuSalesID);
                $totalTmp = 0;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $totalTmp += ($item['menuSalesPrice'] * $item['qty']);
                    }
                }

                if ($netTotal != $totalTmp) {
                    $outputTxt .= 'Bill ID: ' . $menuSalesID . ' Bill Total: ' . $netTotal . ' - Item Total :  ' . $totalTmp . "\n";
                    $updateData[$i]['menuSalesID'] = $menuSalesID;
                    $updateData[$i]['netTotal'] = $totalTmp;

                    if (empty($invoice['paidAmount']) || $invoice['paidAmount'] == null || $invoice['paidAmount'] == 0) {
                        $outputTxt .= "Bill ID: " . $menuSalesID . " Paid Amount: " . $invoice['paidAmount'] . "\n";
                        $updateData[$i]['paidAmount'] = $totalTmp;
                    }
                    if (empty($invoice['cashReceivedAmount']) || $invoice['cashReceivedAmount'] == null || $invoice['cashReceivedAmount'] == 0) {
                        $outputTxt .= "Bill ID: " . $menuSalesID . " cashReceived Amount : " . $invoice['cashReceivedAmount'] . "\n";
                        $updateData[$i]['cashReceivedAmount'] = $totalTmp;
                    }
                }


                $i++;
            }

            echo '<pre>';
            print_r($outputTxt);
        } else {
            echo 'empty';
        }
    }

    /**
     *
     * To update the menu ID in pack table this impact the product mix report.
     *
     */

    function batchJob_updatePackMenuID()
    {

        echo '<pre>';
        echo "Batch started <br/><br/>";
        /** get all packs */
        $q = 'SELECT vp.valuePackDetailID, vp.menuSalesID, vp.menuID vpMenuID, vp.menuPackItemID, pg.menuID FROM srp_erp_pos_valuepackdetail vp LEFT JOIN srp_erp_pos_packgroupdetail pg ON vp.menuPackItemID = pg.packgroupdetailID  WHERE ISNULL(vp.menuID)';
        $r = $this->db->query($q)->result_array();

        $i = 0;
        $updateData = array();
        $outputTxt = '';
        if (!empty($r)) {
            foreach ($r as $val) {

                if (!empty($val['menuID'])) {
                    $updateData[$i]['valuePackDetailID'] = $val['valuePackDetailID'];
                    $updateData[$i]['menuID'] = $val['menuID'];

                    $outputTxt .= "Bill ID: " . $val['menuSalesID'] . " updated menuID: " . $val['menuPackItemID'] . "\n";
                } else {
                    $outputTxt .= "Bill ID: " . $val['menuSalesID'] . " menuID NULL \n";
                }


                $i++;
            }


            if (isset($updateData) && !empty($updateData)) {
                /*** update batch Net Total ** */

                $this->db->update_batch('srp_erp_pos_valuepackdetail', $updateData, 'valuePackDetailID');
                $row = $this->db->affected_rows();

                $outputTxt .= "\n\n\n\n\n Number of row affected: " . $row . " \n\n";
                $outputTxt .= "\n\n query: " . $this->db->last_query() . " \n End.";

                /*** write log ** */

                $logName = "batch_POS_packMenu_" . date("Y-m-d_H-i-s", time()) . ".txt";
                $logPath = UPLOAD_PATH_POS . '/batch_logs/' . $logName;

                $myfile = fopen($logPath, "w") or die("Unable to open file!");
                fwrite($myfile, $outputTxt);
                fclose($myfile);

                echo '<a target="_blank" href="' . base_url() . 'batch_logs/' . $logName . '"> Log File </a><br/><br/>';
            }

            print_r($outputTxt);
        } else {
            echo 'empty';
        }

        echo "<br/><br/>Process Completed";
    }


    function loadProductMix()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
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
        $productMix_menuItem = $this->Pos_restaurant_model->productMix_menuItem($dateFrom, $dateTo, $Outlets, $cashier);
        $get_packs_sales = $this->Pos_restaurant_model->get_productMixPacks_sales($dateFrom, $dateTo, $Outlets, $cashier);

        if (!empty($productMix_menuItem)) {
            $tmpArray2 = array();
            foreach ($productMix_menuItem as $get_packs_sale) {
                $tmpArray2[$get_packs_sale['menuMasterID']]['menuID'] = $get_packs_sale['menuMasterID'];
                $tmpArray2[$get_packs_sale['menuMasterID']]['menuMasterDescription'] = $get_packs_sale['menuMasterDescription'] . ' ' . $get_packs_sale['menuSize'];
                $tmpArray2[$get_packs_sale['menuMasterID']]['qty'] = $get_packs_sale['qty'];
            }
            $productMix_menuItem = $tmpArray2;
        }

        if (!empty($get_packs_sales)) {
            $tmpArray = array();
            foreach ($get_packs_sales as $get_packs_sale) {
                $tmpArray[$get_packs_sale['menuID']]['menuID'] = $get_packs_sale['menuID'];
                $tmpArray[$get_packs_sale['menuID']]['menuMasterDescription'] = $get_packs_sale['menuMasterDescription'] . ' ' . $get_packs_sale['menuSize'];
                $tmpArray[$get_packs_sale['menuID']]['qty'] = $get_packs_sale['qty'];
            }
            $get_packs_sales = $tmpArray;
        }

        // $data['companyInfo'] = $companyInfo;

        $m = array_merge($productMix_menuItem, $get_packs_sales);

        /*Group by script from http://stackoverflow.com/questions/12706359/php-array-group*/
        $result = array();
        foreach ($m as $data) {
            $id = $data['menuID'];
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }
        $data['companyInfo'] = $companyInfo;
        $data['productMix'] = $result;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers_productmix();


        $this->load->view('system/pos/reports/pos-product-mix.php', $data);
    }


    function updateDiscount()
    {
        $menuSalesID = isPos_invoiceSessionExist();
        $data['discountPer'] = !empty($this->input->get('discount')) ? $this->input->get('discount') : 0;
        if ($menuSalesID) {
            if ($data['discountPer'] > -1) {
                $this->Pos_restaurant_model->update_srp_erp_pos_menusalesmaster($data, $menuSalesID);
            }
        }
        echo json_encode(array('billNo' => $menuSalesID, 'discount' => $data['discountPer']));
    }


    function loadFranchiseReport()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
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
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $franchiseReport = $this->Pos_restaurant_model->get_franchiseReport($dateFrom, $dateTo, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['franchiseReport'] = $franchiseReport;

        // $data['companyInfo'] = $companyInfo;
        $this->load->view('system/pos/reports/pos-franchise-report', $data);
    }

    function loadFranchiseReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
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
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $franchiseReport = $this->Pos_restaurant_model->get_franchiseReport($dateFrom, $dateTo, $Outlets, $cashier);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['franchiseReport'] = $franchiseReport;

        $html = $this->load->view('system/pos/pdf/dashboard/pos-franchise-report-pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }


    function loadProductMixReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $tmpCashierSource = $this->input->post('cashier');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
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
        $productMix_menuItem = $this->Pos_restaurant_model->productMix_menuItem($dateFrom, $dateTo, $Outlets, $cashier);
        $get_packs_sales = $this->Pos_restaurant_model->get_productMixPacks_sales($dateFrom, $dateTo, $Outlets, $cashier);

        if (!empty($productMix_menuItem)) {
            $tmpArray2 = array();
            foreach ($productMix_menuItem as $get_packs_sale) {
                $tmpArray2[$get_packs_sale['menuMasterID']]['menuID'] = $get_packs_sale['menuMasterID'];
                $tmpArray2[$get_packs_sale['menuMasterID']]['menuMasterDescription'] = $get_packs_sale['menuMasterDescription'] . ' ' . $get_packs_sale['menuSize'];
                $tmpArray2[$get_packs_sale['menuMasterID']]['qty'] = $get_packs_sale['qty'];
            }
            $productMix_menuItem = $tmpArray2;
        }

        if (!empty($get_packs_sales)) {
            $tmpArray = array();
            foreach ($get_packs_sales as $get_packs_sale) {
                $tmpArray[$get_packs_sale['menuID']]['menuID'] = $get_packs_sale['menuID'];
                $tmpArray[$get_packs_sale['menuID']]['menuMasterDescription'] = $get_packs_sale['menuMasterDescription'] . ' ' . $get_packs_sale['menuSize'];
                $tmpArray[$get_packs_sale['menuID']]['qty'] = $get_packs_sale['qty'];
            }
            $get_packs_sales = $tmpArray;
        }

        // $data['companyInfo'] = $companyInfo;

        $m = array_merge($productMix_menuItem, $get_packs_sales);

        /*Group by script from http://stackoverflow.com/questions/12706359/php-array-group*/
        $result = array();
        foreach ($m as $data) {
            $id = $data['menuID'];
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }
        $data['companyInfo'] = $companyInfo;
        $data['productMix'] = $result;

        $html = $this->load->view('system/pos/pdf/dashboard/pos-product-mix-report-pdf', $data, true);
        // echo $html;exit;
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function updateCurrentMenuWAC()
    {
        echo json_encode($this->Pos_restaurant_model->updateCurrentMenuWAC());
    }

    function updateSendToKitchen()
    {
        echo json_encode($this->Pos_restaurant_model->updateSendToKitchen());
    }


    function loadPaymentSalesReportAdmin()
    {
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');
        $orderTypeIDs = $this->input->post('orderType');

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

        if (isset($orderTypeIDs) && !empty($orderTypeIDs)) {
            $orderTypes = join(",", $orderTypeIDs);
        } else {
            $orderTypes = null;
        }


        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $lessAmounts_discounts = $this->Pos_restaurant_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $lessAmounts_discounts_item_wise = $this->Pos_restaurant_model->get_report_salesReport_discount_item_wise_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $lessAmounts_discountsJavaApp = $this->Pos_restaurant_model->get_report_salesReport_javaAppDiscount_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $lessAmountsAll = array_merge($lessAmounts_discounts, $lessAmounts, $lessAmounts_promotion, $lessAmounts_discountsJavaApp, $lessAmounts_discounts_item_wise);

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_restaurant_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);

        $data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $data['lessAmounts'] = $lessAmountsAll;


        // var_dump($lessAmountsAll);
        $data['totalSales'] = $this->Pos_restaurant_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $data['totalTaxes'] = $this->Pos_restaurant_model->get_report_salesReport_totalTaxes_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $data['totalServiceCharge'] = $this->Pos_restaurant_model->get_report_salesReport_ServiceCharge_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $data['giftCardTopUp'] = $this->Pos_restaurant_model->get_report_giftCardTopUp_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $data['voidBills'] = $this->Pos_restaurant_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $data['creditSales'] = $this->Pos_restaurant_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets, $orderTypes);
        $data['fullyDiscountBill'] = $this->Pos_restaurant_model->get_report_fullyDiscountBills_admin($filterDate, $date2, $cashier, $outlets, $orderTypes);

        //var_dump($data['voidBills']);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $this->load->view('system/pos/reports/pos-payment-sales-report-admin', $data);
    }

    function loadPaymentSalesReportAdminShiftClose()
    {

        $shiftID = $this->input->post('shiftID');
        $counterData = $this->db->query("select * from srp_erp_pos_shiftdetails where shiftID=$shiftID")->row();
        $tmpFilterDate = $counterData->startTime;
        $tmpFilterDateTo = $counterData->endTime;
        $tmpCashier = $counterData->empID;
        $outletID = $counterData->wareHouseID;
        $tmpCashierSource = array($tmpCashier);
        $outletIDs = array($outletID);


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


        $lessAmounts = $this->Pos_restaurant_model->get_report_lessAmount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_promotion = $this->Pos_restaurant_model->get_report_lessAmount_promotion_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts = $this->Pos_restaurant_model->get_report_salesReport_discount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discounts_item_wise = $this->Pos_restaurant_model->get_report_salesReport_discount_item_wise_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmounts_discountsJavaApp = $this->Pos_restaurant_model->get_report_salesReport_javaAppDiscount_admin($filterDate, $date2, $cashier, $outlets);
        $lessAmountsAll = array_merge($lessAmounts_discounts, $lessAmounts, $lessAmounts_promotion, $lessAmounts_discountsJavaApp, $lessAmounts_discounts_item_wise);

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['paymentMethod'] = $this->Pos_restaurant_model->get_report_paymentMethod_admin($filterDate, $date2, $cashier, $outlets);

        $data['customerTypeCount'] = $this->Pos_restaurant_model->get_report_customerTypeCount_2_admin($filterDate, $date2, $cashier, $outlets);
        $data['lessAmounts'] = $lessAmountsAll;


        // var_dump($lessAmountsAll);
        $data['totalSales'] = $this->Pos_restaurant_model->get_report_salesReport_totalSales_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalTaxes'] = $this->Pos_restaurant_model->get_report_salesReport_totalTaxes_admin($filterDate, $date2, $cashier, $outlets);
        $data['totalServiceCharge'] = $this->Pos_restaurant_model->get_report_salesReport_ServiceCharge_admin($filterDate, $date2, $cashier, $outlets);
        $data['giftCardTopUp'] = $this->Pos_restaurant_model->get_report_giftCardTopUp_admin($filterDate, $date2, $cashier, $outlets);
        $data['voidBills'] = $this->Pos_restaurant_model->get_report_voidBills_admin($filterDate, $date2, $cashier, $outlets);
        $data['creditSales'] = $this->Pos_restaurant_model->get_report_creditSales($filterDate, $date2, $cashier, $outlets);
        $data['fullyDiscountBill'] = $this->Pos_restaurant_model->get_report_fullyDiscountBills_admin($filterDate, $date2, $cashier, $outlets);

        //var_dump($data['voidBills']);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();


        $this->load->view('system/pos/reports/pos-payment-sales-report-admin', $data);
    }

    function clickPowerOff()
    {
        $holdBillCount = get_pos_holdBillCount();
        if ($holdBillCount) {
            $holdBillCount = $holdBillCount == 1 ? $holdBillCount . ' bill' : $holdBillCount . ' bills';
            echo json_encode(array('error' => 1, 'message' => "Please close all the pending hold bills. <br/><br/> $holdBillCount  found!"));
        } else {
            echo json_encode(array('error' => 0, 'message' => "clear"));
        }
    }

    function get_outlet_cashier()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier());
    }

    function get_outlet_waiter()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_waiter());
    }

    function get_gpos_outlet_cashier()
    {
        echo json_encode($this->Pos_restaurant_model->get_gpos_outlet_cashier());
    }

    function get_outlet_cashier_itemized()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier_itemized());
    }

    function get_outlet_cashier_Promotions()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier_Promotions());
    }

    function get_outlet_cashier_productmix()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier_productmix());
    }

    function get_outlet_cashier_franchise()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier_franchise());
    }


    function load_pos_detail_sales_report()
    {
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');
        $customerIDs = $this->input->post('customer');
        $orderTypeIDs = $this->input->post('orderType');

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

        if (isset($orderTypeIDs) && !empty($orderTypeIDs)) {
            $orderTypes = join(",", $orderTypeIDs);
        } else {
            $orderTypes = null;
        }

        if (isset($customerIDs) && !empty($customerIDs)) {
            $customers = join(",", $customerIDs);
        } else {
            $customers = null;
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();

        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster($outlets);
        //var_dump('test');exit;
        $data['recordDetail'] = $this->Pos_restaurant_model->get_report_salesDetailReport($filterDate, $date2, $cashier, $outlets, $customers, $orderTypes);
        //var_dump('test');exit;
        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();
        //error_reporting(0);
        $this->load->view('system/pos/reports/pos-payment-sales-detail-report', $data);
    }

    function load_pos_detail_sales_report_with_waiters()
    {
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpWaiterSource = $this->input->post('waiter');
        $outletIDs = $this->input->post('outletID_f');
        $customerIDs = $this->input->post('customer');
        $orderTypeIDs = $this->input->post('orderType');

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

        if (isset($tmpWaiterSource) && !empty($tmpWaiterSource)) {
            $tmpCashier = join(",", $tmpWaiterSource);
            $cashier = $tmpCashier;
        } else {
            $cashier = null;
        }

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = get_outletID();
        }

        if (isset($orderTypeIDs) && !empty($orderTypeIDs)) {
            $orderTypes = join(",", $orderTypeIDs);
        } else {
            $orderTypes = null;
        }

        if (isset($customerIDs) && !empty($customerIDs)) {
            $customers = join(",", $customerIDs);
        } else {
            $customers = null;
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();

        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster($outlets);
        $data['recordDetail'] = $this->Pos_restaurant_model->get_report_salesDetailReport_with_waiters($filterDate, $date2, $cashier, $outlets, $customers, $orderTypes);

        $data['cashier'] = $tmpWaiterSource;
        $data['cashierTmp'] = get_waiters();
        //error_reporting(0);
        $this->load->view('system/pos/reports/pos-payment-sales-detail-report-with-waiters', $data);
    }

    function load_pos_detail_void_report()
    {
        $_POST['outletID3'] = $this->input->post('outletID_f3');
        $data['outletID'] = $this->input->post('outletID_f3');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom3')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo3')));
        $tmpCashierSource = $this->input->post('cashier3');
        $outletIDs = $this->input->post('outletID_f3');
        $customerIDs = $this->input->post('customer3');
        $orderTypeIDs = $this->input->post('orderType2');

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

        if (isset($customerIDs) && !empty($customerIDs)) {
            $customers = join(",", $customerIDs);
        } else {
            $customers = null;
        }

        if (isset($orderTypeIDs) && !empty($orderTypeIDs)) {
            $orderTypes = join(",", $orderTypeIDs);
        } else {
            $orderTypes = null;
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();

        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster($outlets);
        $data['recordDetail'] = $this->Pos_restaurant_model->get_void_detail_report($filterDate, $date2, $cashier, $outlets, $customers, $orderTypes);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();

        $this->load->view('system/pos/reports/pos-payment-void-detail-report', $data);
    }

    function load_pos_detail_void_report_with_waiters()
    {
        $_POST['outletID3'] = $this->input->post('outletID_f3');
        $data['outletID'] = $this->input->post('outletID_f3');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom3')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo3')));
        $tmpWaiterSource = $this->input->post('waiter');
        $outletIDs = $this->input->post('outletID_f3');
        $customerIDs = $this->input->post('customer3');
        $orderTypeIDs = $this->input->post('orderType2');

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

        if (isset($tmpWaiterSource) && !empty($tmpWaiterSource)) {
            $tmpCashier = join(",", $tmpWaiterSource);
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

        if (isset($customerIDs) && !empty($customerIDs)) {
            $customers = join(",", $customerIDs);
        } else {
            $customers = null;
        }

        if (isset($orderTypeIDs) && !empty($orderTypeIDs)) {
            $orderTypes = join(",", $orderTypeIDs);
        } else {
            $orderTypes = null;
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();

        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster($outlets);
        $data['recordDetail'] = $this->Pos_restaurant_model->get_void_detail_report_with_waiters($filterDate, $date2, $cashier, $outlets, $customers, $orderTypes);

        $data['cashier'] = $tmpWaiterSource;
        $data['cashierTmp'] =  get_waiters();

        $this->load->view('system/pos/reports/pos-payment-void-detail-report-with-waiters', $data);
    }

    function load_pos_employee_performance_report()
    {
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

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();

        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster();
        $data['recordDetail'] = $this->Pos_restaurant_model->get_report_employeePerformance($filterDate, $date2, $cashier, $outlets);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();

        $this->load->view('system/pos/reports/pos-employee-performance-report', $data);
    }


    function loadPrintTemplate_salesDetailReport()
    {
        $invoiceID = isPos_invoiceSessionExist();
        $invoiceID = ($invoiceID) ? $invoiceID : $this->input->post('invoiceID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster_salesDetailReport($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['email'] = true;
        $data['wifi'] = false;
        $data['auth'] = false;
        $template = get_print_template();
        //$this->load->view('system/pos/printTemplate/restaurant-pos-thermal-printer', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-detail-reportView', $data);
        $this->load->view($template, $data);
    }

    function loadPrintTemplate_salesDetailForReport()
    {
        $invoiceID = $this->input->post('invoiceID');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster_salesDetailReport($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['email'] = true;
        $data['wifi'] = false;
        $data['auth'] = false;
        $data['outletID'] = $outletID;
        $data['payment_references'] = $this->Pos_restaurant_model->get_payment_references($invoiceID);
        $template = get_print_template();
        $this->load->view($template, $data);
    }

    function createShiftDoubleEntries($shiftID)
    {
        $this->Pos_restaurant_accounts->update_revenue_generalLedger($shiftID);
        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger($shiftID);
        /** 3. COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger($shiftID);
        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger($shiftID);
        /** 5. TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger($shiftID);
        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger($shiftID);
        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger($shiftID);
        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger($shiftID);
        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger($shiftID);
        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger($shiftID);
        /** 11. CREDIT CUSTOMER PAYMENTS - CREDIT SALES HANDLED SEPARATELY  */
        //$this->Pos_restaurant_accounts->update_creditSales_generalLedger($shiftID);
        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_bankLedger($shiftID);
        /** ITEM  LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_itemLedger($shiftID);
        /** CREDIT SALES ENTRIES  */
        $this->Pos_restaurant_accounts->pos_credit_sales_entries($shiftID);
        $this->Pos_restaurant_accounts->pos_credit_sales_entries_manual($shiftID);
    }


    /**
     * =========================================== FIX ===========================================
     * NON CREDIT SALES - GENERAL LEDGER
     * Developed on 03-Jan-2018 Requested by Hisham to fix the issue
     */
    function batch_create_shift_general_ledger_entries($shiftID)
    {
        /** 1. REVENUE */
        $this->Pos_restaurant_accounts_gl_fix->update_revenue_generalLedger($shiftID);
        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts_gl_fix->update_bank_cash_generalLedger($shiftID);
        /** 3. COGS */
        $this->Pos_restaurant_accounts_gl_fix->update_cogs_generalLedger($shiftID);
        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts_gl_fix->update_inventory_generalLedger($shiftID);
        /** 5. TAX */
        $this->Pos_restaurant_accounts_gl_fix->update_tax_generalLedger($shiftID);
        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts_gl_fix->update_commissionExpense_generalLedger($shiftID);
        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts_gl_fix->update_commissionPayable_generalLedger($shiftID);
        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts_gl_fix->update_royaltyPayable_generalLedger($shiftID);
        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts_gl_fix->update_royaltyExpenses_generalLedger($shiftID);
        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts_gl_fix->update_serviceCharge_generalLedger($shiftID);

        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts_gl_fix->update_bankLedger($shiftID);
    }


    /**
     *  =========================================== FIX ===========================================
     * CREDIT SALES - GENERAL LEDGER
     * Developed on 03-Jan-2018 Requested by Hisham to fix the issue
     */
    function batch_create_shift_general_ledger_entries_creditSales($shiftID, $billNo)
    {

        /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
        $this->Pos_restaurant_accounts_gl_fix->pos_generate_invoices_bill($shiftID, $billNo);
        /** 1. CREDIT SALES  - REVENUE */
        $this->Pos_restaurant_accounts_gl_fix->update_revenue_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 2. CREDIT SALES  - COGS */
        $this->Pos_restaurant_accounts_gl_fix->update_cogs_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 3. CREDIT SALES  - INVENTORY */
        $this->Pos_restaurant_accounts_gl_fix->update_inventory_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 4.  CREDIT SALES - TAX */
        $this->Pos_restaurant_accounts_gl_fix->update_tax_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts_gl_fix->update_commissionExpense_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 6.  CREDIT SALES - COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts_gl_fix->update_commissionPayable_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 7.  CREDIT SALES - ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts_gl_fix->update_royaltyPayable_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 8.  CREDIT SALES - ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts_gl_fix->update_royaltyExpenses_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 9. CREDIT SALES -  SERVICE CHARGE */
        $this->Pos_restaurant_accounts_gl_fix->update_serviceCharge_generalLedger_credit_sales_bill($shiftID, $billNo);
        /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */
        $this->Pos_restaurant_accounts_gl_fix->update_creditSales_generalLedger_credit_sales_bill($shiftID, $billNo);
    }

    /**
     *  =========================================== FIX ===========================================
     * NON CREDIT SALES  - ITEM LEDGER
     * Developed on 03-Jan-2018 Requested by Hisham to fix the issue
     */

    function batch_create_shift_item_ledger_entries($shiftID)
    {
        $this->Pos_restaurant_accounts_gl_fix->batch_update_itemLedger($shiftID);
    }

    /**
     *  =========================================== FIX ===========================================
     *  CREDIT SALES  - ITEM LEDGER
     * Developed on 03-Jan-2018 Requested by Hisham to fix the issue
     */
    function batch_create_shift_item_ledger_entries_creditSales($shiftID, $billNo)
    {
        $this->Pos_restaurant_accounts_gl_fix->batch_update_itemLedger_creditSales($shiftID, $billNo);
    }

    function update_tableOrder()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_pos_diningtables');
        $this->db->where('tmp_menuSalesID', $this->input->post('menuSalesID'));
        $diningTable = $this->db->get()->row_array();
        $menuSalesID = $diningTable['tmp_menuSalesID'];
        if (empty($diningTable)) {
            $validation = $this->Pos_restaurant_model->validate_tableOrder();
            if ($validation) {
                /** update table status */
                $result = $this->Pos_restaurant_model->update_diningTableStatus();

                if ($result) {
                    /** update it to menu sales master table  */
                    $this->Pos_restaurant_model->update_menuSalesMasterTableID();
                }

                /** get active ongoing tables */
                $result = $this->Pos_restaurant_model->get_diningTableUsed();
                $output = array('error' => 0, 'result' => $result, 'show_waiter' => 1, 'packs' => 0, 'menuSalesID' => $menuSalesID);
            } else {
                $output = array('error' => 1, 'message' => 'This table is already assigned to a bill, please select the different table. ', 'result' => null, 'show_waiter' => 1, 'packs' => 0, 'menuSalesID' => $menuSalesID);
            }
        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_pos_diningtables');
            $this->db->where('diningTableAutoID', $this->input->post('id'));
            $this->db->where('status', 1);
            $diningTable_tmp = $this->db->get()->row_array();
            $show_waiter = !empty($diningTable_tmp) ? 1 : 0;


            if ($show_waiter) {
                $crewID = !empty($diningTable_tmp['tmp_crewID']) ? $diningTable_tmp['tmp_crewID'] : 0;
                $numOfPacks = !empty($diningTable_tmp['tmp_numberOfPacks']) ? $diningTable_tmp['tmp_numberOfPacks'] : 0;
                $output = array('error' => 2, 'message' => 'This bill already assigned to a ' . $diningTable_tmp['diningTableDescription'], 'show_waiter' => $show_waiter, 'crewID' => $crewID, 'packs' => $numOfPacks, 'menuSalesID' => $menuSalesID);
            } else {
                $crewID = !empty($diningTable['tmp_crewID']) ? $diningTable['tmp_crewID'] : 0;
                $numOfPacks = !empty($diningTable['tmp_numberOfPacks']) ? $diningTable['tmp_numberOfPacks'] : 0;
                $output = array('error' => 3, 'message' => 'switch Table : ' . $diningTable['diningTableDescription'], 'show_waiter' => $show_waiter, 'id' => $this->input->post('id'), 'menuSalesID' => $this->input->post('menuSalesID'), 'fromKey' => $diningTable['diningTableAutoID'], 'crewID' => $crewID, 'packs' => $numOfPacks, 'menuSalesID' => $menuSalesID);
            }
        }
        echo json_encode($output);
    }

    function switchTable()
    {
        $this->db->where('diningTableAutoID', $this->input->post('id'));
        $this->db->update('srp_erp_pos_diningtables', array('status' => 1, 'tmp_menuSalesID' => $this->input->post('menuSalesID'), 'tmp_crewID' => $this->input->post('crewID'), 'tmp_numberOfPacks' => $this->input->post('packs')));

        $this->db->where('diningTableAutoID', $this->input->post('fromKey'));
        $this->db->update('srp_erp_pos_diningtables', array('status' => 0, 'tmp_menuSalesID' => null, 'tmp_crewID' => null, 'tmp_numberOfPacks' => 0));

        $this->db->where('menuSalesID', $this->input->post('menuSalesID'));
        $this->db->update('srp_erp_pos_menusalesmaster', array('tableID' => $this->input->post('id'), 'waiterID' => $this->input->post('crewID'), 'numberOfPacks' => $this->input->post('packs')));

        echo json_encode(array('error' => 0, 'message' => 'table switched'));
    }

    function update_waiter_info()
    {
        $crewID = $this->input->post('crewID');
        $tableID = $this->input->post('tableID');
        $numberOfPacks = $this->input->post('numberOfPack');

        $this->db->select('*');
        $this->db->from('srp_erp_pos_diningtables');
        $this->db->where('diningTableAutoID', $tableID);
        $menuSalesID = $this->db->get()->row('tmp_menuSalesID');

        $this->db->where('diningTableAutoID', $tableID);
        $this->db->update('srp_erp_pos_diningtables', array('tmp_crewID' => $crewID, 'tmp_numberOfPacks' => $numberOfPacks));

        $this->db->where('menuSalesID', $menuSalesID);
        $this->db->update('srp_erp_pos_menusalesmaster', array('waiterID' => $crewID, 'numberOfPacks' => $numberOfPacks));
        echo json_encode(array('error' => 0, 'message' => 'crew updated'));
    }

    function refreshDiningTables()
    {
        /** get active ongoing tables */
        $result = $this->Pos_restaurant_model->get_diningTableUsed();
        $output = array('error' => 0, 'result' => $result);

        echo json_encode($output);
    }


    /**
     *  Script requested by Hisham on 2018-02-18
     *  Sesatha Cost entry fixes
     *
     *  To Fix INVENTORY | COGS | ITEM LEDGER
     *  Logic
     *
     *  Menu sales item detail cost updated manually by hisham.
     *
     *
     */

    function batch_doubleEntry_manualUpdate_after_credit_sales($shiftID)
    {
        echo '<pre>';
        /**  GENERAL LEDGER
         * /pos_restaurant/batch_create_shift_general_ledger_entries/shiftID
         */

        /** 3. COGS |  4. INVENTORY  */
        $this->Pos_restaurant_accounts_gl_fix->update_cogs_generalLedger($shiftID, true);
        $this->Pos_restaurant_accounts_gl_fix->update_inventory_generalLedger($shiftID, true);


        /** GENERAL LEDGER : CREDIT SALES
         * /pos_restaurant/batch_create_shift_general_ledger_entries_creditSales/shiftID/billNo
         */
        $log = true;
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_credit_sales($shiftID, true, $log);
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_credit_sales($shiftID, true, $log);


        /**  ITEM LEDGER : CREDIT SALES
         * /pos_restaurant/batch_create_shift_item_ledger_entries_creditSales/shiftID/billNo /*
         */
        $this->Pos_restaurant_accounts->update_itemLedger_credit_sales($shiftID, true);


        /**--  ITEM LEDGER
         * /pos_restaurant/batch_create_shift_item_ledger_entries/shiftID
         */
        $this->Pos_restaurant_accounts_gl_fix->batch_update_itemLedger($shiftID, true);


        echo '</pre>';
    }

    function batch_doubleEntry_manualUpdate_before_credit_sales($shiftID)
    {
        echo '<pre>';
        /**  GENERAL LEDGER
         * /pos_restaurant/batch_create_shift_general_ledger_entries/shiftID
         */

        /** 3. COGS |  4. INVENTORY  */
        $this->Pos_restaurant_accounts_gl_fix->update_cogs_generalLedger($shiftID, false);
        $this->Pos_restaurant_accounts_gl_fix->update_inventory_generalLedger($shiftID, false);


        /** GENERAL LEDGER : CREDIT SALES
         * /pos_restaurant/batch_create_shift_general_ledger_entries_creditSales/shiftID/billNo
         */
        $log = true;
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_credit_sales($shiftID, false, $log);
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_credit_sales($shiftID, false, $log);


        /**  ITEM LEDGER : CREDIT SALES
         * /pos_restaurant/batch_create_shift_item_ledger_entries_creditSales/shiftID/billNo /*
         */
        $this->Pos_restaurant_accounts->update_itemLedger_credit_sales($shiftID, false);


        /**--  ITEM LEDGER
         * /pos_restaurant/batch_create_shift_item_ledger_entries/shiftID
         */
        $this->Pos_restaurant_accounts_gl_fix->batch_update_itemLedger($shiftID, false);

        echo '</pre>';
    }

    /** Created on 2018-02-09 */
    function batch_doubleEntry_manualUpdate_all_in_one($shiftID)
    {
        $this->db->trans_start();
        /** 1. REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger($shiftID);

        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger($shiftID);

        /** 3. COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger($shiftID);

        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger($shiftID);

        /** 5. TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger($shiftID);

        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger($shiftID);

        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger($shiftID);

        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger($shiftID);

        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger($shiftID);

        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger($shiftID);

        /** 11. CREDIT CUSTOMER PAYMENTS - CREDIT SALES HANDLED SEPARATELY  */
        //$this->Pos_restaurant_accounts->update_creditSales_generalLedger($shiftID);


        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_bankLedger($shiftID);

        /** STOCK UPDATE ITEM MASTER */
        $this->Pos_restaurant_accounts->update_itemMasterNewStock($shiftID);

        /** STOCK UPDATE WAREHOUSE ITEM MASTER */
        $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock($shiftID);

        /** ITEM  LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_itemLedger($shiftID);

        /** CREDIT SALES ENTRIES  */
        $this->Pos_restaurant_accounts->pos_credit_sales_entries($shiftID);


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo 'Error while updating:  <br/><br/>' . $this->db->_error_message();
            exit;
        } else {
            $this->db->trans_commit();
            echo 'Double Entries Updated on ' . date('Y-m-d H:i:s');
        }
    }

    function load_pos_detail_sales_report2()
    {
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpCashierSource = $this->input->post('cashier');
        $outletIDs = $this->input->post('outletID_f');
        $customerIDs = $this->input->post('customer');
        $orderTypeIDs = $this->input->post('orderType');


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

        if (isset($customerIDs) && !empty($customerIDs)) {
            $customers = join(",", $customerIDs);
        } else {
            $customers = null;
        }

        if (isset($orderTypeIDs) && !empty($orderTypeIDs)) {
            $orderTypes = join(",", $orderTypeIDs);
        } else {
            $orderTypes = null;
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();

        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster2();
        $data['recordDetail'] = $this->Pos_restaurant_model->get_report_salesDetailReport2($filterDate, $date2, $cashier, $outlets, $customers, $orderTypes);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();

        $this->load->view('system/pos/reports/pos-payment-sales-detail-report2', $data);
    }

    function load_pos_detail_void_report2()
    {
        $_POST['outletID3'] = $this->input->post('outletID_f3');
        $data['outletID'] = $this->input->post('outletID_f3');
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom3')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo3')));
        $tmpCashierSource = $this->input->post('cashier3');
        $outletIDs = $this->input->post('outletID_f3');
        $customerIDs = $this->input->post('customer3');
        $orderTypeIDs = $this->input->post('orderType2');

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

        if (isset($customerIDs) && !empty($customerIDs)) {
            $customers = join(",", $customerIDs);
        } else {
            $customers = null;
        }

        if (isset($orderTypeIDs) && !empty($orderTypeIDs)) {
            $orderTypes = join(",", $orderTypeIDs);
        } else {
            $orderTypes = null;
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();

        $data['paymentglConfigMaster'] = $this->Pos_restaurant_model->get_srp_erp_pos_paymentglconfigmaster2($outlets);
        $data['recordDetail'] = $this->Pos_restaurant_model->get_void_detail_report2($filterDate, $date2, $cashier, $outlets, $customers, $orderTypes);

        $data['cashier'] = $tmpCashierSource;
        $data['cashierTmp'] = get_cashiers();

        $this->load->view('system/pos/reports/pos-payment-void-detail-report2', $data);
    }

    function showNotificationUnclosedShift()
    {
        $sql = "SELECT
                    shiftID,
                    srp_erp_pos_shiftdetails.startTime,
                    DATE_ADD( srp_erp_pos_shiftdetails.startTime, INTERVAL 12 HOUR ) AS tmpTime ,
                    NOW() as now
                FROM
                    srp_erp_pos_shiftdetails 
                WHERE
                    srp_erp_pos_shiftdetails.empID = '" . current_userID() . "' 
                    AND srp_erp_pos_shiftdetails.companyID = '" . current_companyID() . "'
                    AND srp_erp_pos_shiftdetails.wareHouseID = '" . get_outletID() . "'
                    AND srp_erp_pos_shiftdetails.isClosed = 0 
                HAVING
                    now > tmpTime";
        $result = $this->db->query($sql)->row_array();

        if (!empty($result)) {
            echo json_encode(array('code' => 1, 'status' => true, $result, 'message' => ''));
        } else {
            echo json_encode(array('code' => 0, 'status' => false));
        }
    }

    function load_hold_refno()
    {
        echo json_encode($this->Pos_restaurant_model->load_hold_refno());
    }

    function submitBOT()
    {
        $this->form_validation->set_rules('id', 'Invoice ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'e_type' => 'e', 'message' => validation_errors()));
        } else {
            echo json_encode($this->Pos_restaurant_model->submitBOT());
        }
    }


    function get_delevery_order_rp_pdf()
    {
        $startdate = $this->input->post('startdate');
        $enddate = $this->input->post('enddate');
        $customers = $this->input->post('customers');


        $invoiceids = $this->Pos_restaurant_model->get_srp_deleveryorder_meneusalesID($startdate, $enddate, $customers);

        //$invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID();
        //$masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster();
        //$data['invoiceList'] = $invoice;
        $data['invoiceids'] = $invoiceids;
        //$data['masters'] = $masters;
        $data['auth'] = false;
        $data['wifi'] = true;
        //$template = get_print_template();
        $html = $this->load->view('system/pos/printTemplate/restaurant-pos-delevery-order-multiple-print', $data, true);
        //echo $html;exit;
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function get_srp_deleveryorder_meneusalesID($startdate, $enddate, $customers)
    {
        $date_format_policy = date_format_policy();
        $startdates = input_format_date($startdate, $date_format_policy);
        $enddates = input_format_date($enddate, $date_format_policy);

        $customerIds = join(",", $customers);
        $result = $this->db->query("SELECT
	deliveryOrderID AS deliveryOrderID,
	deliveryOrders.menuSalesMasterID AS invoiceID
FROM
	srp_erp_pos_deliveryorders AS deliveryOrders
INNER JOIN srp_erp_pos_menusalesmaster AS salesMaster ON deliveryOrders.menuSalesMasterID = salesMaster.menuSalesID
LEFT JOIN srp_erp_pos_customermaster AS customerMaster ON customerMaster.posCustomerAutoID = deliveryOrders.posCustomerAutoID
WHERE
deliveryOrders.posCustomerAutoID IN ($customerIds)
AND deliveryDate BETWEEN '$startdates' AND '$enddates'
AND deliveryOrders.isDispatched != 1
 ")->result_array(); //AND salesMaster.wareHouseAutoID
        return $result;
    }

    function fetch_pos_customer_details()
    {
        echo json_encode($this->Pos_restaurant_model->fetch_pos_customer_details());
    }

    function rpos_theme_set_to_ses()
    {

        //use session (optional)
        $this->session->set_userdata('rsctheme', $_POST['rsctheme']);

        $rsctheme = $_POST['rsctheme'];

        //use cookies
        $cookie_arr['name'] = '_rsctheme';
        $cookie_arr['value'] = $rsctheme;
        $cookie_arr['expire'] = (10 * 365 * 24 * 60 * 60);
        $cookie_arr['domain'] = '';
        $cookie_arr['path'] = '/';
        $cookie_arr['secure'] = false;
        $this->input->set_cookie($cookie_arr);
    }


    function loadPendingPaymentsReport()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $outlet = $this->input->post('outlet');
        $customer = $this->input->post('customer');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        /*$filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }*/


        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }

        if (isset($customer) && !empty($customer)) {
            $tmpCus = join(",", $customer);
            $Customers = $tmpCus;
        } else {
            $Customers = null;
        }


        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $pendngpayReport = $this->Pos_restaurant_model->get_pendingPaymentsReport($dateFrom, $Outlets, $Customers);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['penPayReport'] = $pendngpayReport;

        // $data['companyInfo'] = $companyInfo;
        $this->load->view('system/pos/reports/pos-pending-payments-report', $data);
    }


    function loadPendingPaymentsReportPdf()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $outlet = $this->input->post('outlet');
        $customer = $this->input->post('customer');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        /*$filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }*/


        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }

        if (isset($customer) && !empty($customer)) {
            $tmpCus = join(",", $customer);
            $Customers = $tmpCus;
        } else {
            $Customers = null;
        }


        $companyInfo = $this->Pos_restaurant_model->get_currentCompanyDetail();
        //$persn = $this->Pos_restaurant_model->get_deliveryPerson($customerID);
        $pendngpayReport = $this->Pos_restaurant_model->get_pendingPaymentsReport($dateFrom, $Outlets, $Customers);

        $data['companyInfo'] = $companyInfo;
        //$data['person'] = $persn;
        $data['penPayReport'] = $pendngpayReport;

        $html = $this->load->view('system/pos/reports/pos-pending-payments-report-pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function loadPendingPaymentDD()
    {
        echo json_encode($this->Pos_restaurant_model->loadPendingPaymentDD());
    }

    function load_pos_gift_card_report()
    {
        $_POST['outletID'] = $this->input->post('outletID_f');
        $data['outletID'] = $this->input->post('outletID_f');
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $outletIDs = $this->input->post('outletID_f');

        if (!empty($tmpFilterDateTo)) {
            $tmpFilterDateTo = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $tmpFilterDateTo = date('Y-m-d 23:59:59');
        }

        if (isset($outletIDs) && !empty($outletIDs)) {
            $outlet = join(",", $outletIDs);
            $outlets = $outlet;
        } else {
            $outlets = null;
        }
        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['gift_card_details'] = $this->Pos_restaurant_model->get_gift_card_details($tmpFilterDateTo, $outletIDs);

        $this->load->view('system/pos/reports/pos_gift_card_report', $data);
    }

    function load_pos_gift_card_topup_redeem_report()
    {
        $_POST['outletID'] = $this->input->post('outletID_f3');
        $data['outletID'] = $this->input->post('outletID_f3');
        $tmpFilterDateFrom = trim(str_replace('/', '-', $this->input->post('filterFrom3')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo3')));
        $outletIDs = $this->input->post('outletID_f3');
        $customer_ids = $this->input->post('customer');
        $data['customer_ids'] = $customer_ids;

        if (!empty($tmpFilterDateFrom)) {
            $tmpFilterDateFrom = date('Y-m-d H:i:s', strtotime($tmpFilterDateFrom));
        } else {
            $tmpFilterDateFrom = date('Y-m-d 23:59:59');
        }

        if (!empty($tmpFilterDateTo)) {
            $tmpFilterDateTo = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $tmpFilterDateTo = date('Y-m-d 23:59:59');
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['gift_card_details'] = $this->Pos_restaurant_model->get_gift_card_topup_redeem_details($tmpFilterDateFrom, $tmpFilterDateTo, $outletIDs, $customer_ids);

        $this->load->view('system/pos/reports/pos_gift_card_topup_redeem_report', $data);
    }

    function loadHistoryGiftCard()
    {
        $barCode = $this->input->post('barCode');
        $to_date = trim(str_replace('/', '-', $this->input->post('to_date')));
        $to_date = date('Y-m-d H:i:s', strtotime($to_date));

        $this->datatables->select('topUpCard.cardTopUpID as cardTopUpID,topUpAmount,invoiceCode, topUpCard.menuSalesID as menuSalesID,outlet.wareHouseDescription as outlet,  outlet.wareHouseCode as wHouseCode, topUpCard.createdDateTime as createdDateTime, topUpCard.giftCardReceiptID as receipt,topUpCard.menuSalesID as invoice,topUpCard.outletID as outletID,isRefund', false)
            ->from('srp_erp_pos_cardtopup topUpCard')
            ->join('srp_erp_warehousemaster outlet', 'outlet.wareHouseAutoID = topUpCard.outletID')
            ->join('srp_erp_pos_menusalesmaster master', 'master.menuSalesID = topUpCard.menuSalesID', 'left')
            ->add_column('gc_outlet', '$1 - $2', 'wHouseCode , outlet')
            ->add_column('invoice_code', '$1', 'invoiceCode , invoiceCode')
            ->add_column('gc_date', '$1', 'get_giftCardDatetime(createdDateTime)')
            ->add_column('gc_time', '$1', 'get_giftCardDatetime(createdDateTime,\'t\')')
            ->add_column('gc_amount', '$1', 'get_numberFormat(topUpAmount)')
            ->add_column('description', '$1', 'get_history_description(topUpAmount,isRefund)');
        $this->datatables->where('topUpCard.barCode', $barCode);
        $this->datatables->where('topUpCard.companyID', current_companyID());


        if (!empty($f_outletID)) {
            $this->datatables->where('barCode', $f_outletID);
        }
        if (!empty($to_date)) {
            $this->datatables->where('topUpCard.createdDateTime<=', $to_date);
        }
        $r = $this->datatables->generate();
        echo $r;
    }

    /*RPOS ITEM USAGE REPORT*/

    function rpos_item_usage_report()
    {
        $tmpFromDate = $this->input->post('filterFrom');
        $outlet = $this->input->post('outlet');

        if (isset($tmpFromDate) && !empty($tmpFromDate)) {
            $tmpFilterDate = str_replace('/', '-', $tmpFromDate);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateFrom = $date;
        } else {
            $dateFrom = date('Y-m-d');
        }

        $filterTo = $this->input->post('filterTo');
        if (isset($filterTo) && !empty($filterTo)) {
            $tmpFilterDate = str_replace('/', '-', $filterTo);
            $date = date('Y-m-d', strtotime($tmpFilterDate));
            $dateTo = $date;
        } else {
            $dateTo = date('Y-m-d');
        }

        if (isset($outlet) && !empty($outlet)) {
            $tmpOutlet = join(",", $outlet);
            $Outlets = $tmpOutlet;
        } else {
            $Outlets = null;
        }
        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $data['item_usage'] = $this->Pos_restaurant_model->get_item_usage_details($dateFrom, $dateTo, $Outlets);

        $this->load->view('system/pos/reports/rpos_item_usage.php', $data);
    }

    function get_delivery_order_by_invoice_id()
    {
        $invoice_id = isPos_invoiceSessionExist();
        if ($invoice_id) {
            $this->db->select('deliveryOrderID');
            $this->db->from('srp_erp_pos_deliveryorders');
            $this->db->where('menuSalesMasterID', $invoice_id);
            $result = $this->db->get()->row_array();

            if (count($result) > 0) {
                echo json_encode(array('isExistDeliveryInfo' => 1));
            } else {
                echo json_encode(array('isExistDeliveryInfo' => 0));
            }
        }
    }

    /*
     * exception handling
     * function - handle_item_mismatch_exception
     * param - invoice no, outlet id
     *
     * */
    private function handle_item_mismatch_exception($invoice_no, $outlet_id = 0)
    {
        if ($outlet_id == 0) {
            $outlet_id = get_outletID();
        }

        if ($invoice_no && $outlet_id) {
            $this->db->select('menuSalesItemID,menuSalesPrice,salesPriceSubTotal');
            $this->db->from('srp_erp_pos_menusalesitems');
            $this->db->where('menuSalesID', $invoice_no);
            $this->db->where('wareHouseAutoID', $outlet_id);
            $result = $this->db->get()->result_array();

            if (!empty($result)) {
                foreach ($result as $row) {
                    if ($row['menuSalesPrice'] != $row['salesPriceSubTotal']) {
                        $this->db->delete('srp_erp_pos_menusalesitems', array('menuSalesItemID' => $row['menuSalesItemID']));
                    }
                }
            }
        }
    }

    function handleItemListCountForCurrentInvoice()
    {
        $id_array = $this->input->post('id_array');
        $invoice_no = isPos_invoiceSessionExist();

        $this->db->select('menuSalesItemID');
        $this->db->from('srp_erp_pos_menusalesitems');
        $this->db->where('menuSalesID', $invoice_no);
        $this->db->where('wareHouseAutoID', get_outletID());
        $result = $this->db->get()->result_array();

        $table_array = array_column($result, "menuSalesItemID");
        $different = array_diff($table_array, $id_array);

        if (!empty($different)) {
            $i = 0;
            foreach ($different as $id) {
                $this->db->delete('srp_erp_pos_menusalesitems', array('menuSalesItemID' => $id));
                $i++;
            }

            if ($i > 0) {
                echo json_encode(array('is_handled' => 1));
                return;
            }
        }
        echo json_encode(array('is_handled' => 0));
    }

    function billHistoryForClosedBills()
    {
        $counterData = get_counterData();
        $companyID = $counterData['companyID'];
        $outletID = $counterData['wareHouseID'];

        $from = $this->input->post('datefrom');
        $fromDate = date('Y-m-d', strtotime($from));
        $to = $this->input->post('dateto');
        $toDate = date('Y-m-d', strtotime($to));
        $customer = $this->input->post('customer');
        $customer_array = explode(",", $customer);

        $userID = current_userID();
        $user = $this->db->query("select * from srp_erp_warehouse_users where isActive=1 and wareHouseID=$outletID and userID=$userID")->row();

        if ($user->superAdminYN) {
            $this->datatables->select('menuSalesID as menuSalesID, srp_erp_pos_menusalesmaster.wareHouseAutoID as wareHouseAutoID, subTotal as subTotal, invoiceCode as invoiceCode, DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,\'%d-%b-%Y\') as createdDate, srp_erp_pos_menusalesmaster.createdUserName as createdUserName,shiftID, CAST(srp_erp_pos_menusalesmaster.createdDateTime AS TIME) createdTime,IFNULL(srp_erp_pos_customermaster.customerName,"-")  as customerName', false)
                ->from('srp_erp_pos_menusalesmaster')
                ->join("srp_erp_pos_customermaster", "srp_erp_pos_menusalesmaster.customerID = srp_erp_pos_customermaster.posCustomerAutoID", "left")
                ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
                ->add_column('voidBill', '$1', 'btn_Bill_History(menuSalesID,\'View\',srp_erp_pos_menusalesmaster.wareHouseAutoID)')
                ->edit_column('subTotal', '<div>$1</div>', 'column_numberFormat(subTotal)')
                ->where('isHold', 0)
                ->where('CAST(srp_erp_pos_menusalesmaster.createdDateTime AS DATE)  BETWEEN "' . $fromDate . '" AND "' . $toDate . '"')
                ->where('srp_erp_pos_menusalesmaster.companyID', $companyID);
            if (!empty($customer)) {
                $this->datatables->where_in('customerID', $customer_array);
            }
            echo $this->datatables->generate();
        } else {
            $this->datatables->select('menuSalesID as menuSalesID, srp_erp_pos_menusalesmaster.wareHouseAutoID as wareHouseAutoID, subTotal as subTotal, invoiceCode as invoiceCode, DATE_FORMAT(srp_erp_pos_menusalesmaster.createdDateTime,\'%d-%b-%Y\') as createdDate, srp_erp_pos_menusalesmaster.createdUserName as createdUserName,shiftID, CAST(srp_erp_pos_menusalesmaster.createdDateTime AS TIME) createdTime,IFNULL(srp_erp_pos_customermaster.customerName,"-")  as customerName', false)
                ->from('srp_erp_pos_menusalesmaster')
                ->join("srp_erp_pos_customermaster", "srp_erp_pos_menusalesmaster.customerID = srp_erp_pos_customermaster.posCustomerAutoID", "left")
                ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
                ->add_column('voidBill', '$1', 'btn_Bill_History(menuSalesID,\'View\',srp_erp_pos_menusalesmaster.wareHouseAutoID)')
                ->edit_column('subTotal', '<div>$1</div>', 'column_numberFormat(subTotal)')
                ->where('isHold', 0)
                ->where('CAST(srp_erp_pos_menusalesmaster.createdDateTime AS DATE)  BETWEEN "' . $fromDate . '" AND "' . $toDate . '"')
                ->where('srp_erp_pos_menusalesmaster.companyID', $companyID)
                ->where('srp_erp_pos_menusalesmaster.wareHouseAutoID', $outletID);
            if (!empty($customer)) {
                $this->datatables->where_in('customerID', $customer_array);
            }
            echo $this->datatables->generate();
        }
    }

    function preview_hold_sales()
    {
        $this->load->model('Pos_kitchen_model');
        $invoiceID = $this->input->post('id');
        $outletID = $this->input->post('outletID');
        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_salesDetailReport($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['wifi'] = false;
        $data['auth'] = false;
        $data['email'] = true;
        $data['payment_references'] = $this->Pos_restaurant_model->get_payment_references($invoiceID);
        $data['waiterName'] = $this->Pos_kitchen_model->getWaiterNameByMenuSalesID($invoiceID);
        $template = get_print_template();
        $this->load->view($template, $data);
    }

    function load_category_wise_profitability_report()
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
            $profitability_report = $this->Pos_restaurant_model->get_category_wise_profitability_report($filterDate, $date2, $Outlets, $cashier);

            $data['companyInfo'] = $companyInfo;
            $data['profitability_report'] = $profitability_report;
            $data['cashier'] = $tmpCashierSource;
            $data['cashierTmp'] = get_cashiers();

            $this->load->view('system/pos/reports/category_wise_profitability_report', $data);
        }
    }

    function loadItemWiseProfitabilityReport()
    {
        $cat_id = $this->input->post('cat_id');
        $dateTo = trim(str_replace('/', '-', $this->input->post('to_date')));
        $dateTo = date('Y-m-d H:i:s', strtotime($dateTo));

        $dateFrom = trim(str_replace('/', '-', $this->input->post('from_date')));
        $dateFrom = date('Y-m-d H:i:s', strtotime($dateFrom));

        $cashiers = $this->input->post('cashiers');
        $outlets = $this->input->post('outlets');

        $this->datatables->select('
                menuMaster.menuMasterID,
                menuMaster.menuMasterDescription,
                menuCategory.menuCategoryDescription,
                sum( salesItem.salesPriceAfterDiscount ) AS sales,
                sum(salesItem.menuCost * salesItem.qty) as cos,
                sum( salesItem.salesPriceAfterDiscount )-(sum(salesMaster.menuCost * salesItem.qty)) as gp', false)
            ->from('srp_erp_pos_menusalesmaster salesMaster')
            ->join('srp_erp_pos_menusalesitems salesItem', 'salesItem.menuSalesID = salesMaster.menuSalesID AND salesMaster.wareHouseAutoID = salesItem.id_store', 'left')
            ->join('srp_erp_pos_warehousemenumaster warehouse', 'warehouse.warehouseMenuID = salesItem.warehouseMenuID', 'left')
            ->join('srp_erp_pos_menumaster menuMaster', 'menuMaster.menuMasterID = warehouse.menuMasterID', 'left')
            ->join('srp_erp_pos_menucategory menuCategory', 'menuCategory.menuCategoryID = menuMaster.menuCategoryID', 'left')
            ->add_column('sales_amount', '$1', 'get_numberFormat(sales)')
            ->add_column('cos_amount', '$1', 'get_numberFormat(cos)')
            ->add_column('gp_amount', '$1', 'get_numberFormat(gp)')
            ->add_column('gp_margin', '$1', 'get_gp_margin(gp,sales)');
        $this->datatables->where('salesMaster.isHold', 0);
        $this->datatables->where('salesMaster.isVoid', 0);
        $this->datatables->where('menuCategory.menuCategoryID', $cat_id);
        $this->datatables->where('salesMaster.companyID', current_companyID());
        $this->datatables->where('menuMaster.menuMasterID IS NOT NULL');

        if (!empty($dateFrom) && !empty($dateTo)) {
            $this->datatables->where("salesMaster.createdDateTime BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' ");
        }

        if (!empty($outlets)) {
            $this->datatables->where("salesMaster.wareHouseAutoID IN(" . $outlets . ")");
        }

        if (!empty($cashiers)) {
            $this->datatables->where("salesMaster.createdUserID IN(" . $cashiers . ")");
        }

        $this->datatables->group_by('menuMaster.menuMasterID');
        $r = $this->datatables->generate();
        echo $r;
    }

    function get_top_sales_items()
    {
        $from_date = $this->input->post('start_date');
        $to_date = $this->input->post('end_date');
        $outlet = $this->input->post('outlet');
        $company_id = current_companyID();

        if (!empty($from_date)) {
            $from_date = str_replace('/', '-', $from_date);
            $from_date = date('Y-m-d H:i:s', strtotime($from_date));
        } else {
            $from_date = date('Y-m-d 00:00:00');
        }

        if (!empty($to_date)) {
            $to_date = str_replace('/', '-', $to_date);
            $to_date = date('Y-m-d H:i:s', strtotime($to_date));
        } else {
            $to_date = date('Y-m-d 23:59:59');
        }

        $data['companyInfo'] = $this->Pos_restaurant_model->get_currentCompanyDetail();
        $rpt_data = $this->Pos_restaurant_model->get_top_sales_items($from_date, $to_date, $outlet, $company_id);
        $data['master_menus'] = $rpt_data['master_menus'];
        $data['ware_house'] = $rpt_data['ware_house'];

        $this->load->view('system/pos/reports/rpos_top_sales_items_ajax', $data);
    }

    public function get_wowfood_orders()
    {

        $where = "master.wowFoodYN=1 AND master.companyID=" . current_companyID() . " AND master.wareHouseAutoID=" . get_outletID() . " AND d.deliveryOrderID IS NULL AND (master.wowFoodStatus=0 OR master.wowFoodStatus=1)";
        $this->datatables->select('master.wowFoodStatus as wowFoodStatus,master.menuSalesID as menuSalesID, master.wareHouseAutoID as wareHouseAutoID, master.invoiceCode as invoiceCode, netTotal as netTotal, holdByUsername as createdUser, holdDatetime  as holdDate, if(ISNULL(holdRemarks), "auto_remarks", holdRemarks) as remarks,  master.createdDateTime as createdDate, dt.diningTableDescription, master.BOT as BOT, master.isFromTablet as isFromTablet', false)
            ->from('srp_erp_pos_menusalesmaster master')
            ->join('srp_erp_pos_deliveryorders d', 'd.menuSalesMasterID = master.menuSalesID', 'left')
            ->join('srp_erp_pos_diningtables dt', 'dt.diningTableAutoID = master.tableID', 'left')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(menuSalesID)')
            ->add_column('openHold', '$1', 'btn_openHold(menuSalesID,\'open\',wareHouseAutoID)')
            ->add_column('openHoldPreview', '$1', 'btn_wowfood_preview(menuSalesID,\'open\',wareHouseAutoID)')
            ->add_column('status', '$1', 'status_BOT(BOT,isFromTablet)')
            ->add_column('wowfood_status', '$1', 'wowfood_status(wowFoodStatus)')
            ->add_column('amount', '$1', 'get_hold_bill_amount(menuSalesID)')
            ->where($where);
        //        $this->datatables->generate();
        //        var_dump($this->db->last_query());
        echo $this->datatables->generate();
    }

    public function update_wowfood_status()
    {
        $company_id = $this->common_data['company_data']['company_id'];

        $config = $this->get_db_config($company_id);
        $warehouse_id = get_outletID();
        $shiftdetails = $this->Api_wowfood_model->get_srp_erp_pos_shiftdetails_employee($config, $warehouse_id);
        $warehouseDetail = $this->get_wareHouse();
        $status = $this->input->post("status", true);
        $menuSalesID = $this->input->post("menuSalesID", true);
        $update_row = array(
            "wowFoodStatus" => $status,
            "createdPCID" => $this->common_data['current_pc'],
            "createdUserID" => $this->common_data['current_userID'],
            "createdUserName" => $this->common_data['current_user'],
            "counterID" => $shiftdetails['counterID'],
            "shiftID" => $shiftdetails['shiftID'],
            "segmentID" => $warehouseDetail['segmentID'],
            "segmentCode" => $warehouseDetail['segmentCode']
        );
        $this->db->where('menuSalesID', $menuSalesID);
        $res = $this->db->update('srp_erp_pos_menusalesmaster', $update_row);
        if ($res == true) {
            $data['status'] = 'success';
        } else {
            $data['status'] = 'failed';
        }
        echo json_encode($data);
    }

    private function get_db_config($company_id)
    {
        $main_db = $this->load->database('default', TRUE);
        $query = $main_db->query("SELECT * FROM `srp_erp_company` where company_id=$company_id");
        $company_details = $query->row();
        $config['hostname'] = trim($this->encryption->decrypt($company_details->host));
        $config['username'] = trim($this->encryption->decrypt($company_details->db_username));
        $config['password'] = trim($this->encryption->decrypt($company_details->db_password));
        $config['database'] = trim($this->encryption->decrypt($company_details->db_name));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';
        $config['cachedir'] = '';
        $config['swap_pre'] = '';
        $config['encrypt'] = FALSE;
        $config['compress'] = FALSE;
        $config['stricton'] = FALSE;
        $config['failover'] = array();
        $config['save_queries'] = TRUE;
        return $config;
    }

    public function wowfood_orders_count()
    {

        $query = $this->db->query("SELECT * FROM `srp_erp_pos_menusalesmaster` `master` LEFT JOIN `srp_erp_pos_deliveryorders` `d` ON `d`.`menuSalesMasterID` = `master`.`menuSalesID` LEFT JOIN `srp_erp_pos_diningtables` `dt` ON `dt`.`diningTableAutoID` = `master`.`tableID` WHERE `master`.`wowFoodYN` = 1 AND `master`.`companyID` = " . current_companyID() . " AND `master`.`wareHouseAutoID` = " . get_outletID() . " AND `d`.`deliveryOrderID` IS NULL AND `master`.`wowFoodStatus` =0");
        $data['count'] = $query->num_rows();
        $data['is_wowfood_enabled'] = $this->Api_wowfood_model->is_wowfood_enabled(current_companyID(), get_outletID(), '18');
        echo json_encode($data);
    }

    public function load_payments_list()
    {
        $menusalesID = $this->input->post('menusalesID', true);
        $query = $this->db->query("SELECT * FROM `srp_erp_pos_menusalespayments` WHERE menuSalesID=$menusalesID");
        echo json_encode($query->result());
    }

    public function is_credit_sale()
    {
        $menusalesID = $this->input->post('menusalesID', true);
        $query = $this->db->query("SELECT * from srp_erp_pos_menusalespayments WHERE menuSalesID='$menusalesID' AND paymentConfigMasterID=7");
        if ($query->num_rows() > 0) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        echo json_encode($data);
    }

    function get_outlet_cashier_voidbills()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_cashier_voidbills());
    }

    function get_outlet_waiter_voidbills()
    {
        echo json_encode($this->Pos_restaurant_model->get_outlet_waiter_voidbills());
    }

    function ManualFinancePosting()
    {
        $shift_id = $this->input->post('shift_id', true);
        $result = $this->Pos_restaurant_model->restaurant_shift_doubleEntry_fromReview($shift_id);
        if (isset($result['error']) && $result['error'] == 1) {
            $this->db->trans_rollback();
            $data['status'] = "failed";
            $data['message'] = 'Error';
        } else {
            $this->db->where('shiftID', $shift_id);
            $r = $this->db->update('srp_erp_pos_shiftdetails', array("isFinanceClosed" => 1));
            if ($r) {
                $data['status'] = "success";
                $data['message'] = $result[1];
            } else {
                $this->db->trans_rollback();
                $data['status'] = "failed";
                $data['message'] = 'Error';
            }
        }

        echo json_encode($data);
    }

    function tax_detail_report_columns()
    {
        $query = $this->db->query("select DISTINCT(x.taxmasterID),srp_erp_taxmaster.taxShortCode from (SELECT
	menuSalesID,
	taxmasterID,	
	sum(taxAmount) as taxsum
FROM
	srp_erp_pos_menusalestaxes GROUP BY
	menuSalesID,taxmasterID
UNION 
SELECT
	menuSalesID,
	taxmasterID,	
	sum(taxAmount) as taxsum
FROM
	srp_erp_pos_menusalesoutlettaxes GROUP BY
	menuSalesID,taxmasterID) as x
JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID=x.taxmasterID");
        echo json_encode($query->result());
    }

    function tax_detail_report()
    {
        $start_date = $this->input->post('start_date', true);
        $end_date = $this->input->post('end_date', true);
        $outlet_list = $this->input->post('outlet_list', true);

        $query = $this->db->query("select DISTINCT(x.taxmasterID),srp_erp_taxmaster.taxShortCode from (SELECT
	menuSalesID,
	taxmasterID,	
	sum(taxAmount) as taxsum
FROM
	srp_erp_pos_menusalestaxes GROUP BY
	menuSalesID,taxmasterID
UNION 
SELECT
	menuSalesID,
	taxmasterID,	
	sum(taxAmount) as taxsum
FROM
	srp_erp_pos_menusalesoutlettaxes GROUP BY
	menuSalesID,taxmasterID) as x
JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID=x.taxmasterID");
        $columns = array("bill_no" => "", "outlet" => "");
        foreach ($query->result() as $row) {
            $columns[$row->taxmasterID] = 0;
        }
        $query = $this->db->query("select x.menuSalesID,
x.taxmasterID,
x.taxsum,
srp_erp_taxmaster.taxShortCode,
srp_erp_pos_menusalesmaster.invoiceCode,
srp_erp_pos_menusalesmaster.menuSalesDate,
srp_erp_warehousemaster.wareHouseDescription from (SELECT
	menuSalesID,
	taxmasterID,	
	sum(taxAmount) as taxsum	
FROM
	srp_erp_pos_menusalestaxes GROUP BY
	menuSalesID,taxmasterID
UNION 
SELECT
	menuSalesID,
	taxmasterID,	
	sum(taxAmount) as taxsum
FROM
	srp_erp_pos_menusalesoutlettaxes GROUP BY
	menuSalesID,taxmasterID) as x
JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID=x.taxmasterID
JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_menusalesmaster.menuSalesID=x.menuSalesID
JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID=srp_erp_pos_menusalesmaster.wareHouseAutoID
where srp_erp_warehousemaster.wareHouseAutoID in ($outlet_list)
AND srp_erp_pos_menusalesmaster.menuSalesDate BETWEEN CAST('$start_date' AS DATE) AND CAST('$end_date' AS DATE)
ORDER BY x.menuSalesID");
        $current_menusales_id = -1;
        $tax_detail_rows = array();
        $columns_copy = $columns;
        foreach ($query->result() as $row) {
            if ($row->menuSalesID == $current_menusales_id || $current_menusales_id == -1) { //if menu sales id is equal to previous index then put the value in same array.
                $columns_copy['bill_no'] = $row->invoiceCode;
                $columns_copy['outlet'] = $row->wareHouseDescription;
                $columns_copy[$row->taxmasterID] = $row->taxsum;
                $current_menusales_id = $row->menuSalesID;
            } else { //if menu sales id is equal to previous index
                array_push($tax_detail_rows, $columns_copy);
                $current_menusales_id = $row->menuSalesID;
                $columns_copy = $columns;
                $columns_copy['bill_no'] = $row->invoiceCode;
                $columns_copy['outlet'] = $row->wareHouseDescription;
                $columns_copy[$row->taxmasterID] = $row->taxsum;
            }
        }

        array_push($tax_detail_rows, $columns_copy); //last item putting in the array.
        echo json_encode($tax_detail_rows);
    }

    public function get_customers_by_phone()
    {
        $skey = $this->input->post('skey', true);
        $query = $this->db->query("SELECT posCustomerAutoID,CustomerName,customerTelephone FROM `srp_erp_pos_customermaster` WHERE customerTelephone LIKE '$skey%' LIMIT 10");
        echo json_encode($query->result());
    }

    public function get_loyalty_customers_by_phone()
    {
        $skey = $this->input->post('skey', true);
        $query = $this->db->query("SELECT posCustomerAutoID,CustomerName,customerTelephone FROM `srp_erp_pos_customermaster` 
JOIN srp_erp_pos_loyaltycard ON srp_erp_pos_loyaltycard.customerID=srp_erp_pos_customermaster.posCustomerAutoID
WHERE srp_erp_pos_customermaster.customerTelephone LIKE '$skey%' LIMIT 10");
        echo json_encode($query->result());
    }

    public function get_loyalty_customers_by_barcode()
    {
        $skey = $this->input->post('skey', true);
        $query = $this->db->query("SELECT posCustomerAutoID,CustomerName,customerTelephone,barcode FROM `srp_erp_pos_customermaster` 
JOIN srp_erp_pos_loyaltycard ON srp_erp_pos_loyaltycard.customerID=srp_erp_pos_customermaster.posCustomerAutoID
WHERE srp_erp_pos_loyaltycard.barcode LIKE '$skey%' LIMIT 10");
        echo json_encode($query->result());
    }


    function save_loyalty_card()
    {
        echo json_encode($this->Pos_restaurant_model->save_loyalty_card());
    }

    function load_loyalty_table()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $this->datatables->select('srp_erp_pos_loyaltycard.cardMasterID as cardMasterID,
         barcode,
          srp_erp_pos_loyaltycard.isActive as isActive,
          outletID,srp_erp_pos_customermaster.customerName as customerName,
          pts.totpoints as totpoints,
          srp_erp_pos_customermaster.customerTelephone')
            ->from('srp_erp_pos_loyaltycard')
            ->join('srp_erp_pos_customermaster', 'srp_erp_pos_loyaltycard.customerID = srp_erp_pos_customermaster.posCustomerAutoID', 'left')
            ->join('(SELECT cardMasterID,sum(points) as totpoints FROM srp_erp_pos_loyaltytopup  WHERE srp_erp_pos_loyaltytopup.companyID =  \'' . $companyid . '\'    GROUP BY cardMasterID) pts', '(pts.cardMasterID = srp_erp_pos_loyaltycard.cardMasterID)', 'left')
            ->add_column('action', '$1', 'loyalty_action(cardMasterID,isActive)')
            ->edit_column('total_pts', '<div class="pull-right"> $1 </div>', 'totpoints')
            ->edit_column('Active', '$1', 'load_rpos_active_loyalty(cardMasterID,isActive)')
            ->where('srp_erp_pos_loyaltycard.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_pos_loyaltycard.customerType', 1);
        echo $this->datatables->generate();
    }

    function edit_loyalty()
    {
        echo json_encode($this->Pos_restaurant_model->edit_loyalty());
    }

    function update_card_active()
    {
        echo json_encode($this->Pos_restaurant_model->update_card_active());
    }

    function get_loyalty_details()
    {
        $customerID = $this->input->post('customerID');
        $query = $this->db->query("SELECT barcode FROM `srp_erp_pos_loyaltycard` WHERE customerID=$customerID");
        $loyalty_setup = $this->db->query("SELECT * FROM `srp_erp_loyaltypointsetup` WHERE isActive=1");
        if ($query->num_rows() > 0) {
            $barcode = $query->row()->barcode;
            $exchange_rate = $loyalty_setup->row()->amount;
            $points_query = $this->db->query("SELECT SUM(points) as points FROM `srp_erp_pos_loyaltytopup` 
join srp_erp_pos_menusalesmaster on srp_erp_pos_menusalesmaster.menuSalesID=srp_erp_pos_loyaltytopup.invoiceID
WHERE posCustomerAutoID=$customerID and srp_erp_pos_menusalesmaster.isVoid!=1")->row_array();
            $available_points = $points_query['points'];
            $data['available_points'] = $available_points == null ? 0 : $available_points;
            $data['barcode'] = $barcode;
            $data['exchange_rate'] = $exchange_rate;
            $data['status'] = 'success';
            $data['message'] = '';
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Customer not registered to the loyalty program.';
        }
        echo json_encode($data);
    }

    function add_points()
    {
        $this->form_validation->set_rules('exchange_rate_amount', 'Exchange rate', 'trim|required');
        $this->form_validation->set_rules('price_to_point', 'Price to point', 'trim|required');
        $this->form_validation->set_rules('point_to_price', 'Point to price', 'trim|required');
        $this->form_validation->set_rules('minimum_points', 'Minimum points', 'trim|required');
        $this->form_validation->set_rules('amount_val', 'Points for purchases(Amount)', 'trim|required');
        $this->form_validation->set_rules('number_of_points', 'Points for purchases(Points)', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->Pos_restaurant_model->add_points());
        }
    }

    function load_points_table()
    {
        $this->datatables->select('pointSetupID,srp_erp_currencymaster.CurrencyName,amount,loyaltyPoints,isActive,
        priceToPointsEarned,
        pointsToPriceRedeemed,
        minimumPointstoRedeem,
        poinforPuchaseAmount,
        purchaseRewardPoint
        ')
            ->from('srp_erp_loyaltypointsetup')
            ->join('srp_erp_currencymaster', 'srp_erp_loyaltypointsetup.currencyID = srp_erp_currencymaster.currencyID', 'left')
            ->edit_column('active', '$1', 'load_active_points(pointSetupID,isActive)')
            ->where('srp_erp_loyaltypointsetup.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    function loyalty_points_details()
    {
        $companyID = current_companyID();
        $points = $this->db->query("SELECT pointSetupID,currencyID,amount,loyaltyPoints,
 priceToPointsEarned,
 pointsToPriceRedeemed,
 minimumPointstoRedeem,
 poinforPuchaseAmount,
 purchaseRewardPoint,
 amount
 FROM srp_erp_loyaltypointsetup WHERE companyID={$companyID} AND isActive=1");
        if ($points->num_rows() > 0) {
            $data['status'] = 'success';
            $data['loyalty_setup'] = $points->row_array();
        } else {
            $data['status'] = 'failed';
        }
        echo json_encode($data);
    }

    function save_customer_posres()
    {
        $this->form_validation->set_rules('customerNameTmp', 'Customer Name', 'trim|required');
        $this->form_validation->set_rules('customerTelephoneTmp', 'Customer Telephone', 'trim|required');
        $this->form_validation->set_rules('customerCountry', 'Customer Country', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->Pos_restaurant_model->save_customer_posres());
        }
    }

    function loadPrintTemplateSplitBill()
    {
        $outletID = get_outletID();
        $invoiceID = trim($this->input->post('invoiceID') ?? '');
        $promotionID = trim($this->input->post('promotionID') ?? '');
        $promotional_discount = trim($this->input->post('promotional_discount') ?? '');
        $promotionIDdatacp = trim($this->input->post('promotionIDdatacp') ?? '');
        $split_into = trim($this->input->post('split_into') ?? '');
        $this->update_posListItems();

        $this->Pos_restaurant_model->update_isSampleBillPrintFlag($invoiceID, $outletID);

        if ($promotionID) {
            $data['isPromotion'] = 1;
            $data['promotionID'] = $promotionID;
            $data['promotionDiscount'] = $promotionIDdatacp;
            $data['promotionDiscountAmount'] = $promotional_discount;
            $this->db->where('menuSalesID', $invoiceID)->update('srp_erp_pos_menusalesmaster', $data);
        } else {
            $data['isPromotion'] = 0;
            $data['promotionID'] = null;
            $data['promotionDiscount'] = 0;
            $data['promotionDiscountAmount'] = 0;
            $this->db->where('menuSalesID', $invoiceID)->update('srp_erp_pos_menusalesmaster', $data);
        }

        $invoice = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesitems_invoiceID_forHoldBill($invoiceID, $outletID);
        $masters = $this->Pos_restaurant_model->get_srp_erp_pos_menusalesmaster($invoiceID, $outletID);
        $data['invoiceList'] = $invoice;
        $data['masters'] = $masters;
        $data['sampleBill'] = false;
        $data['auth'] = false;
        $data['isSample'] = false;
        $data['splitBill'] = true;
        $data['split_qty'] = $split_into;
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint', $data);
        //$this->load->view('system/pos/printTemplate/restaurant-pos-dotmatric-printer', $data);
        $data['outletTaxMaster'] = $this->Pos_restaurant_model->outlet_tax_list($outletID);
        //var_dump($data['outletTaxMaster']);exit;
        $template = get_print_template();
        //        var_dump($template);exit;
        $data['template'] = $template;
        $this->load->view($template, $data);
    }

    public function restaurant_home()
    {
        $this->load->view('system/pos/life_theme_home_screen');
    }

    public function validate_daybook_email()
    {
        $email = $this->input->post('email', true);
        $posType = $this->input->post('posType', true);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $query = $this->db->query("select * from srp_erp_daybookemaillist where email='$email' and posType='$posType'");
            if ($query->num_rows() > 0) {
                $data['status'] = 'failed';
                $data['message'] = 'Email already exist.';
            } else {
                $data['status'] = 'success';
                $data['message'] = 'Email is valid.';
            }
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Email is not valid.';
        }

        echo json_encode($data);
    }

    public function save_daybook_email()
    {
        $email = $this->input->post('email', true);
        $posType = $this->input->post('posType', true);
        $name = $this->input->post('name', true);
        $record = array(
            "name" => $name,
            "email" => $email,
            "posType" => $posType,
            "createdUserID" => $this->common_data['current_userID'],
            "createdPCID" => $this->common_data['current_pc'],
            "createdDateTime" => current_date(),
            "timestamp" => current_date(),
            "companyID" => current_companyID()
        );
        if ($this->db->insert('srp_erp_daybookemaillist', $record)) {
            $data['status'] = 'success';
            $data['message'] = 'Successfully Saved';
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Failed';
        }
        echo json_encode($data);
    }

    public function edit_daybook_email()
    {
        $id = $this->input->post('id', true);
        $email = $this->input->post('email', true);
        $posType = $this->input->post('posType', true);
        $name = $this->input->post('name', true);
        $record = array(
            "name" => $name,
            "email" => $email,
            "posType" => $posType,
            "modifiedUserID" => $this->common_data['current_userID'],
            "modifiedPCID" => $this->common_data['current_pc'],
            "timestamp" => current_date()
        );
        $this->db->where('dayBookEmailID', $id);
        if ($this->db->update('srp_erp_daybookemaillist', $record)) {
            $data['status'] = 'success';
            $data['message'] = 'Successfully Saved';
        } else {
            $data['status'] = 'failed';
            $data['message'] = 'Failed';
        }
        echo json_encode($data);
    }


    public function get_daybook_emails()
    {
        $posType = $this->input->post('posType', true);
        $this->datatables->select('dayBookEmailID as dayBookEmailID,
        name as name,
        email as email,
        posType as posType', false)
            ->from('srp_erp_daybookemaillist')
            ->where('srp_erp_daybookemaillist.posType', $posType)
            ->where('srp_erp_daybookemaillist.companyID', current_companyID())
            ->add_column('action', '$1', 'daybook_email_action(dayBookEmailID)');
        $r = $this->datatables->generate();
        echo $r;
    }

    public function get_daybook_email_details()
    {
        $id = $this->input->post('id', true);
        $row = $this->db->query("select * from srp_erp_daybookemaillist where dayBookEmailID=$id")->row();
        echo json_encode($row);
    }

    public function delete_email()
    {
        $email_id = $this->input->post('email_id');
        $this->db->query("DELETE FROM srp_erp_daybookemaillist WHERE dayBookEmailID = $email_id");
        $data['status'] = 'success';
        $data['message'] = 'Successfully removed.';
        echo json_encode($data);
    }


    public function verify_payment_by_cashier()
    {
        $paymentID = $this->input->post('paymentID');
        $isVerified = $this->input->post('isVerified');
        $this->db->where('menuSalesPaymentID', $paymentID);
        $this->db->update('srp_erp_pos_menusalespayments', array(
            "isVerifiedByCashier" => $isVerified
        ));
        $error = $this->db->error();
        if ($error['code'] == 0) {
            $data['status'] = 'updated';
        } else {
            $data['status'] = 'error';
        }
        echo json_encode($data);
    }

    public function save_waiter_id()
    {

        $waiterId = $this->input->post('waiterId');
        $menuSalesId = $this->input->post('menuSalesId');
        $this->db->query("update srp_erp_pos_menusalesmaster set waiterID=$waiterId where menuSalesID=$menuSalesId");
        $error = $this->db->error();
        if ($error['code'] == 0) {
            $data['status'] = 'updated';
        } else {
            $data['status'] = 'error';
        }
        echo json_encode($data);
    }

    public function check_waiter_pin()
    {
        $waiterPin = $this->input->post('waiterPinInput');
        if ($waiterPin == "") {
            $data['status'] = 'failed';
        } else {
            $query = $this->db->query("select EIdNo from srp_employeesdetails where pos_barCode='$waiterPin'");
            if ($query->num_rows() > 0) {
                $employeeID = $query->row('EIdNo');
                $companyID = current_companyID();
                $warehouseID = get_outletID();
                $query2 = $this->db->query("select crewMemberID,crewFirstName from srp_erp_pos_crewmembers where EIdNo=$employeeID and companyID=$companyID and wareHouseAutoID=$warehouseID");
                if ($query2->num_rows() > 0) {
                    $data['crewMemberID'] = $query2->row('crewMemberID');
                    $data['crewFirstName'] = $query2->row('crewFirstName');
                    $data['status'] = 'success';
                } else {
                    $data['status'] = 'failed';
                }
            } else {
                $data['status'] = 'failed';
            }
        }

        echo json_encode($data);
    }

    public function check_bill_is_hold_by_user()
    {
        $menuSalesID = $this->input->post('menuSalesID', true);
        $holdByUserID = $this->db->query("select holdByUserID from srp_erp_pos_menusalesmaster where menuSalesID=$menuSalesID")->row('holdByUserID');
        if ($holdByUserID == null) {
            $data['status'] = false;
        } else {
            $data['status'] = true;
        }
        echo json_encode($data);
    }

    function load_statusbased_customer_rpos()
    {
        $customer_arr = array();
        $activeStatus = $this->input->post("activeStatus");
        $tab = $this->input->post("tab");

        $status_filter = '';
        $companyID = current_companyID();
        if (!empty($activeStatus)) {
            if ($activeStatus == 1) {
                $status_filter = "AND isActive = 1 ";
            } elseif ($activeStatus == 2) {
                $status_filter = "AND isActive = 0 ";
            } else {
                $status_filter = '';
            }
        }
        $companyID = current_companyID();
        $type = $this->input->post("type");

        if ($type == 1) {

            $customer = $this->db->query("SELECT posCustomerAutoID,CustomerAutoID,CustomerName,customerTelephone
                                            FROM `srp_erp_pos_customermaster` 
                                            WHERE `companyID` = $companyID AND isFromERP = 1 $status_filter")->result_array();
            if (isset($customer)) {
                foreach ($customer as $row) {
                    //$customer_arr[trim($row['customerAutoID'] ?? '')] = trim($row['customerName'] ?? '') . ' | ' . (trim($row['customerTelephone'] ?? ''));
                    $customers[trim($row['posCustomerAutoID'] ?? '')] = (trim($row['posCustomerAutoID'] ?? '') ? trim($row['CustomerName'] ?? '') . " - " . $row['customerTelephone'] : '');
                }
            }
        }
        if ($tab == 1) {
            echo form_dropdown('customer[]', $customers, '', 'multiple id="customer"  class="form-control input-sm"');
        } else {
            echo form_dropdown('customer3[]', $customers, '', 'multiple id="customer3"  class="form-control input-sm"');
        }
    }

    function load_statusbased_customer_rpos2()
    {
        $customer_arr = array();
        $activeStatus = $this->input->post("activeStatus");
        $tab = $this->input->post("tab");

        $status_filter = '';
        $companyID = current_companyID();
        if (!empty($activeStatus)) {
            if ($activeStatus == 1) {
                $status_filter = "AND srp_erp_pos_customermaster.isActive = 1 ";
            } elseif ($activeStatus == 2) {
                $status_filter = "AND (srp_erp_pos_customermaster.isActive = 0 || srp_erp_pos_customermaster.isActive is null )";
            } else {
                $status_filter = '';
            }
        }
        $companyID = current_companyID();
        $type = $this->input->post("type");

        if ($type == 1) {

            $customer = $this->db->query("SELECT srp_erp_pos_customermaster.posCustomerAutoID,
                                                IFNULL(srp_erp_customermaster.CustomerName,srp_erp_pos_customermaster.CustomerName) as CustomerName,
                                                IFNULL(srp_erp_customermaster.customerTelephone,srp_erp_pos_customermaster.customerTelephone) as customerTelephone,
                                                srp_erp_pos_customermaster.CustomerAutoID
                                            FROM `srp_erp_pos_customermaster` 
                                            LEFT JOIN srp_erp_customermaster ON srp_erp_pos_customermaster.CustomerAutoID=srp_erp_customermaster.CustomerAutoID
                                            WHERE srp_erp_pos_customermaster.`companyID` = $companyID  $status_filter")->result_array();
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customers[trim($row['posCustomerAutoID'] ?? '')] = (trim($row['posCustomerAutoID'] ?? '') ? trim($row['CustomerName'] ?? '') . " - " . $row['customerTelephone'] : '');
                }
            }
        }
        if ($tab == 1) {
            echo form_dropdown('customer[]', $customers, '', 'multiple id="customer"  class="form-control input-sm"');
        } else {
            echo form_dropdown('customer3[]', $customers, '', 'multiple id="customer3"  class="form-control input-sm"');
        }
    }

    function check_stock_on_qty_change()
    {
        $itemQty = $this->input->post('itemQty');
        $requestedQty = (float)($this->input->post('requestedQty'));
        $wareHouseID = $this->common_data['ware_houseID'];
        $id = $this->input->post('id');
        $companyID = current_companyID();

        if (!empty($id)) {


            $output = $this->Pos_restaurant_model->get_warehouseMenu_specific($id);

            $isPack = $output['isPack'];
            if (!empty($output)) {

                $Items = $this->db->query("SELECT
	srp_erp_pos_menudetails.itemAutoID 
FROM
	srp_erp_pos_warehousemenumaster
	LEFT JOIN srp_erp_pos_menumaster ON srp_erp_pos_warehousemenumaster.menuMasterID = srp_erp_pos_menumaster.menuMasterID
	LEFT JOIN srp_erp_pos_menudetails ON srp_erp_pos_menumaster.menuMasterID = srp_erp_pos_menudetails.menuMasterID 
WHERE
	warehouseMenuID = $id AND itemAutoID is not null")->result_array();

                $cnt = count($Items);
                $isMinusBocked = getPolicyValues('MQT', 'All');
                if (!empty($Items) && $isMinusBocked == 1) {

                    $Items = array_column($Items, 'itemAutoID');
                    $itemIDs = join(",", $Items);

                    $itm = $this->db->query("
        SELECT
        t2.itemAutoID,
        t2.itemSystemCode,
        t2.itemDescription,
        IFNULL( SUM( IFNULL( t1.transactionQTY, 0 ) / IFNULL( t1.convertionRate, 0 ) ), 0 ) AS currentStock,
        t2.mainCategory AS mainCategory 
        FROM
        srp_erp_itemmaster t2
        LEFT JOIN ( SELECT * FROM srp_erp_itemledger WHERE warehouseAutoID = '{$wareHouseID}' ) t1 ON t1.itemAutoID = t2.itemAutoID 
        WHERE
        t2.companyID = '{$companyID}' 
        AND t2.itemAutoID IN ( $itemIDs ) 
        AND t2.isActive = 1 
        GROUP BY
        t2.itemAutoID
        ")->result_array();

                    $bal = 0;
                    $insuf = array();
                    foreach ($itm as $vl) {

                        $empID = current_userID();
                        $itemAutoID = $vl['itemAutoID'];
                        $usageQry = $this->db->query("select 
                        srp_erp_pos_menusalesitemdetails.itemAutoID,
        sum( srp_erp_pos_menusalesitemdetails.qty * menuSalesQty ) AS currentUsage,
        srp_erp_pos_menudetails.qty
        from srp_erp_pos_menusalesitemdetails
        join srp_erp_pos_menusalesmaster on srp_erp_pos_menusalesmaster.menuSalesID=srp_erp_pos_menusalesitemdetails.menuSalesID
        join srp_erp_pos_shiftdetails on srp_erp_pos_shiftdetails.shiftID=srp_erp_pos_menusalesmaster.shiftID
        and srp_erp_pos_shiftdetails.isClosed=0
        and srp_erp_pos_menusalesitemdetails.itemAutoID=$itemAutoID
        AND srp_erp_pos_menusalesitemdetails.wareHouseAutoID='{$wareHouseID}'
        LEFT JOIN srp_erp_pos_menudetails ON srp_erp_pos_menudetails.menuMasterID = srp_erp_pos_menusalesitemdetails.menuID
        GROUP BY srp_erp_pos_menusalesitemdetails.itemAutoID");

                        if ($usageQry->num_rows() > 0) {
                            $currentUsage = $usageQry->row('currentUsage');
                            $itemUsageForThisMenu =  (float)$usageQry->row('qty');
                        } else {
                            $itemUsageForThisMenu  = (float)($this->db->query("SELECT                            
                                    srp_erp_pos_menudetails.qty
                                FROM
                                    srp_erp_pos_warehousemenumaster
                                    LEFT JOIN srp_erp_pos_menumaster ON srp_erp_pos_warehousemenumaster.menuMasterID = srp_erp_pos_menumaster.menuMasterID
                                    LEFT JOIN srp_erp_pos_menudetails ON srp_erp_pos_menumaster.menuMasterID = srp_erp_pos_menudetails.menuMasterID 
                                WHERE
                                    warehouseMenuID = $id")->row('qty'));
                            $currentUsage = 0;
                        }

                        if ($requestedQty > 0) {
                            $bal = $vl['currentStock'] - ($currentUsage + ($itemUsageForThisMenu * $itemQty));
                        } else {
                            $bal = $vl['currentStock'] - $currentUsage;
                        }

                        $vl['currentUsage'] = $currentUsage;
                        $vl['quantityUsedInThisBill'] = $itemUsageForThisMenu * $itemQty;

                        $requiredItemQty = $requestedQty * $itemUsageForThisMenu;
                        // var_dump($requiredItemQty);exit;
                        if ($bal < $requiredItemQty && ($vl['mainCategory'] != 'Service' && $vl['mainCategory'] != 'Non Inventory')) {

                            array_push($insuf, $vl);
                            /*echo json_encode(array('error' => 1, 'message' => 'Selected menu has insufficient items'));
                            exit;*/
                        }
                    }

                    if (!empty($insuf)) {
                        echo json_encode(array('error' => 2, 'message' => 'Selected menu has insufficient items', 'insuf' => $insuf));
                        exit;
                    } else {
                        echo json_encode(array('error' => 0, 'message' => ''));
                        exit;
                    }
                }
            } else {
                echo json_encode(array('error' => 2, 'message' => 'Empty dataset'));
                exit;
            }
        } else {
            echo json_encode(array('error' => 2, 'message' => 'Id is required'));
            exit;
        }
    }
}
