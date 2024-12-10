<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');


class Pos extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!isset($this->common_data['status']) || empty(trim($this->common_data['status']))) {
            header('Location: ' . site_url('Login/logout'));
            exit;
        } else {
            $this->load->library('pos_policy');
            $this->load->model('Pos_model');
            $this->load->model('Inventory_modal');
            $this->load->model('Pos_restaurant_model');
            $this->load->model('Pos_restaurant_accounts');
            $this->load->helper('cookie');
            $this->load->helper('pos');
        }
    }

    function index()
    {
        $isHaveNotClosedSession = $this->Pos_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            $counterDet = $this->Pos_model->get_counterData($isHaveNotClosedSession['counterID']);
            $counterDet = $counterDet['counterCode'];
        } else {
            $counterDet = '';
        }
        //Invoice No Start
        $WarehouseID = current_warehouseID();

        $querys = $this->db->select('wareHouseCode')->from('srp_erp_warehousemaster')->where('wareHouseAutoID', $WarehouseID)->get();
        $WarehouseCode = $querys->row_array();
        $code = $WarehouseCode['wareHouseCode'] ?? '';

        $query = $this->db->select('invoiceSequenceNo')->from('srp_erp_pos_invoice')->where('companyID', $this->common_data['company_data']['company_id'])->where('wareHouseAutoID', $WarehouseID)
            ->order_by('invoiceID', 'desc')->get();
        $lastRefArray = $query->row_array();
        $lastINVNo = $lastRefArray['invoiceSequenceNo'] ?? '';
        $lastINVNo = ($lastINVNo == null) ? 1 : $lastRefArray['invoiceSequenceNo'] + 1;
        $companyID = current_companyID();
        $queryscomp = $this->db->select('company_code')->from('srp_erp_company')->where('company_id', $companyID)->get();
        $compCode = $queryscomp->row_array();
        $company_code = $compCode['company_code'];

        $invNo = $company_code . '/' . $code . str_pad($lastINVNo, 6, '0', STR_PAD_LEFT);
        //Invoice No End
        $invCodeDet = $this->Pos_model->getInvoiceSequenceCode();
        $data['title'] = 'POS';
        $data['extra'] = 'sidebar-collapse fixed';
        $data['refNo'] = $invNo;
        $data['isHadSession'] = $isHadSession;
        $wareHouseData = $this->Pos_model->get_wareHouse();
        $data['posData'] = array(
            'wareHouseLocation' => $wareHouseData['wareHouseLocation'] ?? '',
            'counterDet' => $counterDet,
        );

        $companyID = current_companyID();
        $wareHouseID = $this->common_data['ware_houseID'];
 
        $items = $this->item_initialSearch(0);
        
        $data['items'] = $items;
        $this->load->view('system/pos/general-pos-terminal', $data);
    }

    public function item_initialSearch($isJson = 1)
    {
        $companyID = current_companyID();
        $wareHouseID = $this->common_data['ware_houseID'];
        $customer = $this->input->get('customer');

        $items = $this->db->query("SELECT
                    t2.itemAutoID,
                    t2.seconeryItemCode,
                    t2.itemSystemCode,
                    t2.itemDescription,
                    IFNULL( t1.currentStock, 0 ) AS currentStock,
                    t2.companyLocalSellingPrice,
                    t2.companyLocalCurrencyDecimalPlaces,
                    defaultUnitOfMeasure,
                    itemImage,
                    barcode,
                    IFNULL(j1.rSalesPrice,t2.companyLocalSellingPrice) AS companyLocalSellingPrice,
                    IFNULL(j2.rSalesPrice,IFNULL(j1.rSalesPrice,t2.companyLocalSellingPrice)) AS companyLocalSellingPrice
                FROM
                    srp_erp_warehouseitems t1
                LEFT JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                LEFT JOIN (
                        SELECT itemMasterID,wareHouseAutoID,salesPrice,rSalesPrice FROM srp_erp_item_master_pricing AS pt
                        WHERE pt.isActive = 1 AND pt.pricingType = 'Direct' AND pt.wareHouseAutoID = '{$wareHouseID}'
                        ) AS j1
                        ON t1.itemAutoID = j1.itemMasterID AND t1.wareHouseAutoID = j1.wareHouseAutoID
                LEFT JOIN (
                        SELECT itemMasterID,wareHouseAutoID,salesPrice,rSalesPrice FROM srp_erp_item_master_pricing AS pt
                        WHERE pt.isActive = 1 AND pt.pricingType = 'Selected' AND pt.customer ='{$customer}'
                        ) AS j2
                        ON t1.itemAutoID = j2.itemMasterID
                WHERE t2.companyID = '{$companyID}'
                AND t1.wareHouseAutoID = '{$wareHouseID}'
                AND t2.isActive = 1
                AND t2.allowedtoSellYN = 1
                LIMIT 50")->result_array();

        // GROUP BY
        // t1.itemAutoID

        //echo $this->db->last_query();
        if ($isJson == 1) {
            echo json_encode($items);
        } else {
            return $items;
        }
    }

    public function load_currencyDenominationPage()
    {
        $currencyCode = $this->common_data['company_data']['company_default_currency'];

        //$wareHouseID = $this->common_data['ware_houseID'];
        $wareHouseID = get_outletID();
        $data['session_data'] = $this->Pos_model->isHaveNotClosedSession();
        $data['denomination'] = $this->Pos_model->currencyDenominations($currencyCode);
        $data['counters'] = $this->Pos_model->load_wareHouseCounters($wareHouseID);
        $data['dPlace'] = $this->common_data['company_data']['company_default_decimal'];
        //echo '<pre>';print_r($data['counters']); echo '</pre>';die();
        $data['isRestaurant'] = false;
        $data['isRestaurant_mobile'] = false;

        $this->load->view('system/pos/ajax/currency_denomination_view', $data);
    }

    public function load_currencyDenominationPage_mobile()
    {
        $currencyCode = $this->common_data['company_data']['company_default_currency'];

        //$wareHouseID = $this->common_data['ware_houseID'];
        $wareHouseID = get_outletID();
        $data['session_data'] = $this->Pos_model->isHaveNotClosedSession();
        $data['denomination'] = $this->Pos_model->currencyDenominations($currencyCode);
        $data['counters'] = $this->Pos_model->load_wareHouseCounters($wareHouseID);
        $data['dPlace'] = $this->common_data['company_data']['company_default_decimal'];
        //echo '<pre>';print_r($data['counters']); echo '</pre>';die();
        $data['isRestaurant'] = false;
        $data['isRestaurant_mobile'] = false;

        $this->load->view('system/pos/ajax/currency_denomination_view', $data);
    }

    public function shift_create()
    {
        $this->form_validation->set_rules('startingBalance', 'Starting Balance', 'trim|required');
        $this->form_validation->set_rules('counterID', 'Counter ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->shift_create());
        }
    }

    public function shift_close()
    {
        $code = $this->input->post('code');
        $this->form_validation->set_rules('startingBalance', 'Ending Balance', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $this->db->trans_start();
            $counterData = get_counterData();
            $shiftID = $this->Pos_model->get_pos_shift();
            $shiftQuery = $this->db->last_query();
            $companyID = current_companyID();
            $wareHouseID = $this->common_data['ware_houseID'];
            $Items = $this->db->query("SELECT
	srp_erp_itemledger_review.itemAutoID,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.itemDescription,
	SUM( srp_erp_itemledger_review.transactionQTY / srp_erp_itemledger_review.convertionRate ) AS reviewsum,
	itmlegr.ledgrqty AS legrsum,
	itmlegr.ledgrqty + SUM(srp_erp_itemledger_review.transactionQTY / srp_erp_itemledger_review.convertionRate) AS currstk 
FROM
	srp_erp_itemledger_review
	INNER JOIN (
	SELECT
		sum( transactionqty / srp_erp_itemledger.convertionRate ) AS ledgrqty,
		itemAutoID,
		itemDescription 
	FROM
		srp_erp_itemledger 
	WHERE
		srp_erp_itemledger.companyID = $companyID 
		AND srp_erp_itemledger.wareHouseAutoID = $wareHouseID 
	GROUP BY
		itemAutoID 
	) itmlegr ON srp_erp_itemledger_review.itemAutoID = itmlegr.itemAutoID
	INNER JOIN srp_erp_itemmaster ON itmlegr.itemAutoID = srp_erp_itemmaster.itemAutoID 
WHERE
	pos_shiftID = $shiftID 
	AND srp_erp_itemledger_review.companyID = $companyID 
	AND srp_erp_itemledger_review.wareHouseAutoID = $wareHouseID 
GROUP BY
	srp_erp_itemledger_review.itemAutoID 
 HAVING
	 currstk <0")->result_array();
            $isMinusAllowed = getPolicyValues('MQT', 'All');
            if (!empty($Items) && $isMinusAllowed == 1) {
                //echo json_encode(array('error' => 1, 'message' => 'Selected menu has insufficient items'));
                $tmpResult = array('d', 'Selected menu has insufficient items.', $Items);
                echo json_encode($tmpResult);
                exit;
            }


            $result = $this->Pos_model->shift_close($shiftID);
            if ($result) {
                $tmpResult = array('s', 'Shift Closed Successfully', 'code' => $code, 'counterData' => $counterData); /*code is to identify where it come from.*/
                if ($code == 1) {
                    /** POS restaurant */
                    $companyID = current_companyID();
                    $this->db->select("*");
                    $this->db->from("srp_erp_company");
                    $this->db->where("company_id", $companyID);
                    $company = $this->db->get()->row_array();
                    $isRposFinancePostingEnabled = is_rpos_finance_posting_enabled();
                    if (!empty($company) && $company['pos_isFinanceEnables'] == 1 && $isRposFinancePostingEnabled == 1) {
                        /** Double Entry */
                        //$result = $this->restaurant_shift_doubleEntry($shiftID);
                        $this->Pos_model->taxLedgerRecordsForPosRestaurant($shiftID);
                        $result = $this->restaurant_shift_doubleEntry_fromReview($shiftID);
                        if (isset($result['error']) && $result['error'] == 1) {
                            $this->db->trans_rollback();
                        } else {
                            $this->db->where('shiftID', $shiftID);
                            $this->db->update('srp_erp_pos_shiftdetails', array("isFinanceClosed" => 1));
                        }
                    } else {
                        $this->db->where('shiftID', $shiftID);
                        $this->db->update('srp_erp_pos_shiftdetails', array("isFinanceClosed" => 0));
                    }

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $tmpResult = array('w', 'Transaction has been roll backed. Please refresh the page and re-do the shift closing.');
                    } else {
                        $this->db->trans_commit();
                    }
                } else {
                    $this->db->trans_commit();
                }
            } else {
                $tmpResult = array('w', 'Shift has already Closed, please refresh the page and check again', $shiftQuery);
            }


            echo json_encode($tmpResult);
        }
    }


    public function item_search()
    {
        echo json_encode($this->Pos_model->item_search());
    }

    public function item_search_barcode()
    {
        $barcode = true;
        echo json_encode($this->Pos_model->item_search($barcode));
    }

    public function item_search_outlet_barcode()
    {
        $barcode = true;
        echo json_encode($this->Pos_model->item_outlet_pricing_search($barcode));
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
            $creditNote = $this->input->post('_creditNoteAmount');
            $paidAmount = ($cashAmount + $chequeAmount + $cardAmount + $creditNote);
            $netTotVal = $this->input->post('netTotVal');
            $balanceAmount = ($netTotVal - $paidAmount);

            if ($balanceAmount > 0 && $customerID == 0) {
                echo json_encode(array('e', 'Credit not allowed for Cash Customer'));
            } else {
                echo json_encode($this->Pos_model->invoice_create());
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
            echo json_encode($this->Pos_model->invoice_hold());
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
            echo json_encode($this->Pos_model->invoice_cardDetail());
        }
    }

    public function customer_search()
    {
        echo json_encode($this->Pos_model->customer_search());
    }

    public function item_batch_search()
    {
        echo json_encode($this->Pos_model->item_batch_search());
    }

    public function recall_invoice()
    {
        echo json_encode($this->Pos_model->recall_invoice());
    }

    public function recall_hold_invoice()
    {
        echo json_encode($this->Pos_model->recall_hold_invoice());
    }

    public function creditNote_search()
    {
        echo json_encode($this->Pos_model->creditNote_search());
    }

    public function invoice_search()
    {
        echo json_encode($this->Pos_model->invoice_search());
    }

    public function invoice_searchLiveSearch()
    {
        $search_string = "%" . $this->input->get('query') . "%";
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $dataArr = array();
        $dataArr2 = array();

        $data = $this->db->query("SELECT documentSystemCode,invoiceCode FROM srp_erp_pos_invoice WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse}
                                  AND invoiceCode LIKE '{$search_string}' AND isVoid=0 ORDER BY documentSystemCode ASC LIMIT 20")->result_array();

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array(
                    'value' => $val["invoiceCode"],
                    'data' => $val['invoiceCode'],
                );
            }
        }

        $dataArr2['suggestions'] = $dataArr;

        echo json_encode($dataArr2);
    }

    public function invoice_return()
    {
        $this->form_validation->set_rules('itemID[]', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemUOM[]', 'Item UOM', 'trim|required');
        $this->form_validation->set_rules('itemQty[]', 'Item QTY', 'trim|required');
        $this->form_validation->set_rules('return_QTY[]', 'Return QTY', 'trim|required');
        $this->form_validation->set_rules('itemPrice[]', 'Item Price', 'trim|required');
        $this->form_validation->set_rules('return-customerID', 'Customer ID', 'trim|required');
        $this->form_validation->set_rules('returnMode', 'Return Mode', 'trim|required');

        if ($this->input->post('returnMode') == 'exchange') {
            $this->form_validation->set_rules('creditNoteID', 'Credi Note', 'required|callback_check_credit_note_gl_exist');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->invoice_return());
        }
    }

    function check_credit_note_gl_exist($str)
    {
        //Cash GL validation
        $paymentMethods_GLConfig = get_paymentMethods_GLConfig();
        $arry_exi = array_search(2, array_column($paymentMethods_GLConfig, 'autoID'));
        if (empty($arry_exi)) {
            $this->form_validation->set_message('check_credit_note_gl_exist', 'We can not exchange. Credit note is not configured. Please contact our support team.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function load_holdInv()
    {
        echo json_encode($this->Pos_model->load_holdInv());
    }

    public function invoice_print()
    {
        try {

            $invoiceID = $this->uri->segment(3);
            $doSysCode_refNo = $this->input->post('doSysCode_refNo');
            $invData = $this->Pos_model->invoice_search($invoiceID);
            $data['wHouse'] = wareHouseDetails($invData[1]['wareHouseAutoID']);
            $data['invData'] = $invData;
            $data['isVoid'] = $this->input->post('isVoid');
            $data['doSysCode_refNo'] = $doSysCode_refNo;

            $companyID = current_companyID();
            $data['companyAddress'] = $this->db->query("SELECT `company_address1` FROM `srp_erp_company` WHERE `company_id` = $companyID")->row('company_address1');


            $data['isOtherTaxExist'] = $this->db->query("SELECT
	                                                        COUNT(taxLedgerAutoID) as ledgerTaxCount
	                                                        FROM
                                                            srp_erp_taxledger
                                                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                            WHERE
                                                            documentID = 'GPOS' 
                                                            AND taxCategory != 2 
                                                            AND amount > 0
                                                            AND documentMasterAutoID = {$invoiceID}")->row('ledgerTaxCount');

            //get payments methods
            $data['gPosPayMethods'] = $this->Pos_model->get_payment_methods_by_invoice_id($invoiceID);


            if ($invData[1]['creditNoteID'] != 0 && $invData[1]['creditNoteID'] != null) {
                $data['returnDet'] = $this->Pos_model->get_returnCode($invData[1]['creditNoteID']);
            } else {
                $data['returnDet'] = null;
            }

            $outletID = get_outletID();
            $view_name = get_general_pos_print_templates($outletID);

            $styleChangePolicy = getPolicyValues('EXINV', 'All'); //
            
            if($styleChangePolicy == 1){
                $view_name = 'system/pos/printTemplate/gen-pos-invoice-print-arabic-english';
            }

            $this->load->view($view_name, $data);
            
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public function return_print()
    {
        $returnID = $this->uri->segment(3);
        $data['isOtherTaxExist'] = $this->db->query("SELECT
	                                                        COUNT(taxLedgerAutoID) as ledgerTaxCount
	                                                        FROM
                                                            srp_erp_taxledger
                                                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                            WHERE
                                                            documentID = 'RET' 
                                                            AND taxCategory != 2 
                                                            AND amount > 0
                                                            AND documentMasterAutoID = {$returnID}")->row('ledgerTaxCount');
        $data['invData'] = $this->Pos_model->invReturn_details($returnID);
        $data['wHouse'] = wareHouseDetails($data['invData'][1]['wareHouseAutoID']);

        $this->load->view('system/pos/printTemplate/gen-pos-inv-exchange-print', $data);
    }

    /*Start of Counter */
    public function new_counter()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('counterCode', 'Counter Code', 'trim|required');
        $this->form_validation->set_rules('counterName', 'Counter Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->new_counter());
        }
    }

    public function fetch_counters()
    {
        $this->datatables->select('counterID, counterCode, wareHouseDescription, counterName, wareHouseID, wareHouseCode, wareHouseLocation', false)
            ->from('srp_erp_pos_counters t1')
            ->join('srp_erp_warehousemaster t2', 't2.wareHouseAutoID=t1.wareHouseID')
            ->add_column('action', '$1', 'actionCounter_fn(counterID, counterCode, counterName, wareHouseID)')
            ->add_column('wareHouseColumn', '$1  -  $2 - $3', 'wareHouseCode, wareHouseDescription ,wareHouseLocation')
            ->where('t1.isActive', 1)
            ->where('t1.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function delete_counterDetails()
    {
        $isHaveNotClosedSession = $this->Pos_restaurant_model->isHaveNotClosedSession();
        $isHadSession = (empty($isHaveNotClosedSession)) ? 0 : $isHaveNotClosedSession;
        if ($isHadSession != 0) {
            echo json_encode(array('w', 'There is an Active session'));
        } else {
            $this->form_validation->set_rules('counterID', 'Counter ID', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {
                //echo json_encode(array('e', 'Deleted'));
                echo json_encode($this->Pos_model->delete_counterDetails());
            }
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
            echo json_encode($this->Pos_model->update_counterDetails());
        }
    }

    public function load_counters()
    {
        $wareHouse = $this->input->post('wareHouseID');
        $thisWareHouseCounters = $this->Pos_model->load_wareHouseCounters($wareHouse);
        $thisWareHouseUsers = $this->Pos_model->load_wareHouseUsers($wareHouse);

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
            ->where('t1.isActive', 1)
            ->where('t1.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function fetch_ware_house_user()
    {
        $campanyID = $this->common_data['company_data']['company_id'];

        $wareHouseID = $_POST['selectedWarehouse'];
        $userID = $_POST['selectedEmp'];


        $wareHouseID = join(',', $wareHouseID);
        $userID = join(',', $userID);

        $where = "t1.wareHouseID IN (" . $wareHouseID . ") AND t1.userID IN (" . $userID . ") AND t1.companyID = " . $campanyID . "  ";

        $this->datatables->select("Ename2 AS empName, userID, ECode, autoID, t1.wareHouseID AS WHID, wareHouseCode, t3.wareHouseDescription  as wareHouseDescription, t3.wareHouseLocation as  wareHouseLocation, t3.wareHouseDescription as  wareHouseDescription,wareHouseAdminYN,superAdminYN", false)
            ->from('srp_erp_warehouse_users t1')
            ->join('srp_employeesdetails t2', 't1.userID=t2.EIdNo')
            ->join('srp_erp_warehousemaster t3', 't1.wareHouseID=t3.wareHouseAutoID')
            ->edit_column('wareHouseDescription', '$1  -  $2 - $3', 'wareHouseCode,wareHouseDescription, wareHouseLocation')
            ->add_column('action', '$1', 'actionWarehouseUser_fn(autoID, userID, empName, WHID, wareHouseLocation)')
            ->add_column('superAdmn', '$1', 'actionSuperAdmin(autoID, superAdminYN,WHID)')
            ->add_column('wareAdmn', '$1', 'actionWarehouseAdmin(autoID, wareHouseAdminYN,WHID)')
            ->where($where)
            ->where('t1.isActive', 1);
        echo $this->datatables->generate();
    }

    public function emp_search()
    {
        echo json_encode($this->Pos_model->emp_search());
    }

    public function add_ware_house_user()
    {
        $this->form_validation->set_rules('wareHouseID', 'Ware house ID', 'trim|required');
        $this->form_validation->set_rules('employeeID', 'Employee ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->add_ware_house_user());
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
            $companyID = current_companyID();
            $autoID = $this->input->post('updateID');
            $empID = $this->db->select('*')
                ->from('srp_erp_warehouse_users')
                ->where('autoID', $autoID)
                ->where('companyID', $companyID)
                ->get()
                ->row('userID');

            $result = $this->db->select('sd.startTime,  wm.wareHouseCode, wm.wareHouseDescription, sd.shiftID')
                ->from('srp_erp_pos_shiftdetails sd')
                ->join('srp_erp_warehousemaster wm', 'wm.wareHouseAutoID = sd.wareHouseID', 'left')
                ->where('sd.empID', $empID)
                ->where('sd.isClosed', 0)
                ->where('sd.companyID', $companyID)
                ->get()
                ->row_array();


            if (!empty($result)) {
                $message = 'This user has an ongoing shift in ' . $result['wareHouseDescription'] . ' - ' . $result['wareHouseCode'] . ' <br/>';
                $message .= 'Shift Opened on : ' . date('d-m-Y', strtotime($result['startTime'])) . '<br/>';
                $message .= 'Shift ID: ' . $result['shiftID'];
                echo json_encode(array('w', $message));
            } else {
                echo json_encode($this->Pos_model->update_ware_house_user());
            }
        }
    }

    public function delete_ware_house_user()
    {
        $this->form_validation->set_rules('autoID', 'Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $autoID = $this->input->post('autoID');
            $empID = $this->db->select('*')
                ->from('srp_erp_warehouse_users')
                ->where('autoID', $autoID)
                ->where('companyID', $companyID)
                ->get()
                ->row('userID');

            $result = $this->db->select('sd.startTime,  wm.wareHouseCode, wm.wareHouseDescription, sd.shiftID')
                ->from('srp_erp_pos_shiftdetails sd')
                ->join('srp_erp_warehousemaster wm', 'wm.wareHouseAutoID = sd.wareHouseID', 'left')
                ->where('sd.empID', $empID)
                ->where('sd.isClosed', 0)
                ->where('sd.companyID', $companyID)
                ->get()
                ->row_array();

            if (!empty($result)) {
                $message = 'This user has an ongoing shift in ' . $result['wareHouseDescription'] . ' - ' . $result['wareHouseCode'] . ' <br/>';
                $message .= 'Shift Opened on : ' . date('d-m-Y', strtotime($result['startTime'])) . '<br/>';
                $message .= 'Shift ID: ' . $result['shiftID'];
                echo json_encode(array('w', $message));
            } else {
                echo json_encode($this->Pos_model->delete_ware_house_user());
            }
        }
    }

    /*Promotion setups*/
    public function fetch_promotions()
    {
        $this->datatables->select('proMaster.promotionID AS promotionID, proType.Description AS typeDes, proMaster.Description AS masterDes,
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
                echo json_encode($this->Pos_model->new_promotion());
            } else {
                echo json_encode(array('e', 'End date should be greater than or equal to from date'));
            }
        }
    }

    public function get_promotionMasterDet()
    {
        $promo_ID = $this->input->post('promo_ID');
        echo json_encode($this->Pos_model->get_promotionMasterDet($promo_ID));
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

        $data['detail'] = $this->Pos_model->get_promotionDet($promoID);
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
                echo json_encode($this->Pos_model->update_promotion());
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
            echo json_encode($this->Pos_model->delete_promotion());
        }
    }

    public function load_applicableItems()
    {
        $promo_ID = $this->input->post('promo_ID');
        $data['w_items'] = $this->Pos_model->load_applicableItems($promo_ID);

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
            echo json_encode($this->Pos_model->save_promotionItems());
        }
    }

    /*End of Promotion setups*/

    public function double()
    {
        $partyData = array(
            'cusID' => 01,
            'sysCode' => 'CASH',
            'cusName' => 'CASH',
            'partyCurID' => '',
            'partyCurrency' => 'OMR',
            'partyDPlaces' => 3,
            'partyER' => 1,
        );

        $do = $this->Pos_model->double_entry(110, $partyData);
    }

    function restaurant_bill_insertDoubleEntry($shiftID)
    {
        /* Get bill payments bank  */
        $data = array();
        $i = 0;
        /**  GL_Impact  **/
        /* 1st Entry : Revenue  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_revenue($shiftID);


        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();

            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;
                $i++;
            }
        }


        /* 2nd  Entry : Bank  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_bank($shiftID);

        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }


        /* 3rd  Entry : Sales Commission  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_sales_commission($shiftID);


        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }


        /* 4th  Entry : Inventory Asset Account  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_inventory($shiftID);


        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }


        /* 5th  Entry : COGS  */
        $result = $this->Pos_restaurant_accounts->get_bill_payments_cogs($shiftID);


        if (!empty($result)) {
            $companyID = current_companyID();
            $companyCode = current_companyCode();
            $userGroup = user_group();
            $currentPc = current_pc();
            $createdUserID = current_userID();
            $currentDatetime = format_date_mysql_datetime();
            $currentUser = current_user();
            $currentUserID = current_userID();


            foreach ($result as $item) {
                $data[$i]['documentCode'] = $item['documentCode'];
                $data[$i]['documentMasterAutoID'] = $item['documentMasterAutoID'];
                $data[$i]['documentDetailAutoID'] = null;
                $data[$i]['documentSystemCode'] = $item['documentSystemCode'];
                $data[$i]['documentType'] = null;
                $data[$i]['documentDate'] = $item['documentdate'];
                $data[$i]['documentYear'] = $item['documentYear'];
                $data[$i]['documentMonth'] = $item['documentMonth'];
                $data[$i]['documentNarration'] = $item['documentNarration'];
                $data[$i]['chequeNumber'] = $item['chequeNumber'];
                $data[$i]['GLAutoID'] = $item['GLAutoID'];
                $data[$i]['systemGLCode'] = $item['systemGLCode'];
                $data[$i]['GLCode'] = $item['GLCode'];
                $data[$i]['GLDescription'] = $item['GLDescription'];
                $data[$i]['GLType'] = $item['GLType'];
                $data[$i]['amount_type'] = $item['amount_type'];
                $data[$i]['transactionCurrencyID'] = $item['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $item['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $item['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = round($item['transactionAmount'], $item['transactionCurrencyDecimalPlaces']);
                $data[$i]['transactionCurrencyDecimalPlaces'] = $item['transactionCurrencyDecimalPlaces'];
                $data[$i]['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $item['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $item['companyLocalExchangeRate']; // calculate
                $data[$i]['companyLocalAmount'] = round($item['companyLocalAmount'], $item['companyLocalCurrencyDecimalPlaces']);
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces']; // calculate
                $data[$i]['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $item['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $item['companyReportingExchangeRate']; // calculate
                $data[$i]['companyReportingAmount'] = round($item['companyReportingAmount'], $item['companyReportingCurrencyDecimalPlaces']);
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingCurrencyDecimalPlaces']; // calculate
                $data[$i]['confirmedByEmpID'] = $currentUserID;
                $data[$i]['confirmedByName'] = $currentUser;
                $data[$i]['confirmedDate'] = $currentDatetime;
                $data[$i]['approvedDate'] = $currentDatetime;
                $data[$i]['approvedbyEmpID'] = $currentUserID;
                $data[$i]['approvedbyEmpName'] = $currentUser;
                $data[$i]['segmentID'] = $item['segmentID'];
                $data[$i]['segmentCode'] = $item['segmentCode'];
                $data[$i]['companyID'] = $companyID;
                $data[$i]['companyCode'] = $companyCode;
                $data[$i]['createdUserGroup'] = $userGroup;
                $data[$i]['createdPCID'] = $currentPc;
                $data[$i]['createdUserID'] = $createdUserID;
                $data[$i]['createdDateTime'] = $currentDatetime;
                $data[$i]['createdUserName'] = $currentUser;
                $data[$i]['timestamp'] = $currentDatetime;

                $i++;
            }
        }


        if (!empty($data)) {
            $result = $this->Pos_model->insert_batch_srp_erp_generalledger($data);
        }
        return array('error' => 0, 'message' => 'GL entries saved with ' . count($data) . ' records', 'result_batch1' => $result);


        /** /  GL_Impact  **/
    }


    
    function loadNewInvoiceNo()
    {
        $invCodeDet = $this->Pos_model->getInvoiceSequenceCode();

        /*get next bill no*/
        if (!empty($invCodeDet)) {
            echo json_encode(array('error' => 0, 'message' => 'done', 'refCode' => $invCodeDet['sequenceCode']));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'empty', 'refCode' => null));
        }
    }


    
    function itemLoadDefault()
    {
        return $items = $this->item_initialSearch(1);
    }


    function restaurant_shift_doubleEntry($shiftID)
    {
        $this->db->trans_start();

        $outletID = get_outletID();
        $exceededItem = true;

        if ($exceededItem) {
            /** 0. ITEM EXCEEDED */
            $this->Pos_restaurant_accounts->update_itemExceededRecord($shiftID, false);
        }


        /** 1. REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger($shiftID); // outlet ID added - where done //outletID added to shift

        /** 2. BANK OR CASH */
        $this->Pos_restaurant_accounts->update_bank_cash_generalLedger($shiftID); // outlet ID added - where done //outletID added to shift

        /** 3. COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger($shiftID); // outlet ID added - where done //outletID added to shift

        /** 4. INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger($shiftID); // outlet ID added - where done  //outletID added to shift
        $this->Pos_restaurant_accounts->update_exceededGL_generalLedger($shiftID); // outlet ID added - where done  //outletID added to shift

        if ($exceededItem) {
            /** Deduct Item Exceeded - COGS */
            //$this->Pos_restaurant_accounts->itemExceeded_adjustment_generalLedger_cogs($shiftID, false);

            /** 4. INVENTORY */
            //$this->Pos_restaurant_accounts->itemExceeded_adjustment_generalLedger_inventory($shiftID, false);
        }


        /** 5. TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger($shiftID); // outlet ID added // outletID added to shift

        /** 6. COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger($shiftID);  // outlet ID added - where done // outletID added to shift

        /** 7. COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger($shiftID); // outlet ID added - where done // outletID added to shift

        /** 8. ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger($shiftID); // outlet ID added - where done // outletID added to shift

        /** 9. ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger($shiftID); // outlet ID added - where done // outletID added to shift

        /** 10. SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger($shiftID); // outlet ID added - where done // outletID added to shift

        /** 11. CREDIT CUSTOMER PAYMENTS - CREDIT SALES HANDLED SEPARATELY  */
        //$this->Pos_restaurant_accounts->update_creditSales_generalLedger($shiftID);


        /** BANK LEDGER UPDATE  */
        $this->Pos_restaurant_accounts->update_bankLedger($shiftID); // outlet ID added - where done

        /** Stocks are not available in the outlet -> insert it from item master */
        $this->Pos_restaurant_accounts->insert_items_notExist_inWarehouseItem($shiftID); // where outlet ID


        if ($exceededItem) {
            /** STOCK UPDATE ITEM MASTER */
            $this->Pos_restaurant_accounts->update_itemMasterNewStock_itemExceeded($shiftID);

            /** STOCK UPDATE WAREHOUSE ITEM MASTER */
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_itemExceeded($shiftID);
        } else {
            /** STOCK UPDATE ITEM MASTER */
            $this->Pos_restaurant_accounts->update_itemMasterNewStock($shiftID); // outletID, WHERE, JOIN => done

            /** STOCK UPDATE WAREHOUSE ITEM MASTER */
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock($shiftID);
        }

        $this->Pos_restaurant_accounts->update_itemLedger($shiftID); // WHERE, JOIN => outlet ID - ADDED

        if ($exceededItem) {
            /** ITEM EXCEEDED - ITEM LEDGER */
            //$this->Pos_restaurant_accounts->itemExceeded_adjustment_itemLedger($shiftID); // is_sync =>0
        }


        /** ----------------- CREDIT SALES ENTRIES ------------------  */

        $CS = " SELECT *  FROM srp_erp_pos_menusalesmaster  WHERE isCreditSales = 1 AND wareHouseAutoID = '" . $outletID . "'  AND shiftID = '" . $shiftID . "'";
        $resultCS = $this->db->query($CS)->result_array();
        if (!empty($resultCS)) {
            foreach ($resultCS as $val) {
                $menuSalesID = $val['menuSalesID'];

                /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
                $this->Pos_restaurant_accounts->pos_generate_invoices($shiftID, $menuSalesID);  // outlet ID added - where done

                if ($exceededItem) {
                    $query = " SELECT documentMasterAutoID  FROM srp_erp_pos_menusalesmaster  WHERE menuSalesID = '" . $menuSalesID . "'  ";
                    $docid = $this->db->query($query)->row_array();
                    $documentMasterAutoID = $docid['documentMasterAutoID'];
                    $this->Pos_restaurant_accounts->update_itemExceededRecord_creditSales_menuSalesID($shiftID, $menuSalesID);
                    $this->Pos_restaurant_accounts->update_itemLedger_credit_sales($shiftID, true, $menuSalesID, $documentMasterAutoID);
                }
            }
        }

        /** 1. CREDIT SALES  - REVENUE */
        $this->Pos_restaurant_accounts->update_revenue_generalLedger_credit_sales($shiftID); // outletID => JOIN, WHERE condition corrected

        /** 2. CREDIT SALES  - COGS */
        $this->Pos_restaurant_accounts->update_cogs_generalLedger_credit_sales($shiftID); // outletID => JOIN, WHERE condition corrected
        /** 3. CREDIT SALES  - INVENTORY */
        $this->Pos_restaurant_accounts->update_inventory_generalLedger_credit_sales($shiftID); // outletID => JOIN, WHERE condition corrected
        $this->Pos_restaurant_accounts->update_exceededGL_generalLedger_credit_sales($shiftID); // outletID => JOIN, WHERE condition corrected


        if ($exceededItem) {
            /** Adjust General Ledger for Credit Sales  */
            //$this->Pos_restaurant_accounts->creditSales_adjust_inventory($shiftID);
            //$this->Pos_restaurant_accounts->creditSales_adjust_cogs($shiftID);
        }


        /** 4.  CREDIT SALES - TAX */
        $this->Pos_restaurant_accounts->update_tax_generalLedger_credit_sales($shiftID); // outletID => JOIN, WHERE condition corrected
        /** 5.  CREDIT SALES - COMMISSION EXPENSE  */
        $this->Pos_restaurant_accounts->update_commissionExpense_generalLedger_credit_sales($shiftID);   // outletID => JOIN, WHERE condition corrected
        /** 6.  CREDIT SALES - COMMISSION PAYABLE */
        $this->Pos_restaurant_accounts->update_commissionPayable_generalLedger_credit_sales($shiftID); // outletID => JOIN, WHERE condition corrected
        /** 7.  CREDIT SALES - ROYALTY PAYABLE */
        $this->Pos_restaurant_accounts->update_royaltyPayable_generalLedger_credit_sales($shiftID);  // outletID => JOIN, WHERE condition corrected
        /** 8.  CREDIT SALES - ROYALTY EXPENSES */
        $this->Pos_restaurant_accounts->update_royaltyExpenses_generalLedger_credit_sales($shiftID); // outletID => JOIN, WHERE condition corrected
        /** 9. CREDIT SALES -  SERVICE CHARGE */
        $this->Pos_restaurant_accounts->update_serviceCharge_generalLedger_credit_sales($shiftID);  // outletID => JOIN, WHERE condition corrected
        /** 10. CREDIT SALES -  CREDIT CUSTOMER PAYMENTS */
        $this->Pos_restaurant_accounts->update_creditSales_generalLedger_credit_sales($shiftID);


        if ($exceededItem) {
            /** CREDIT SALES - ITEM MASTER STOCK UPDATE - item exceeded */
            $this->Pos_restaurant_accounts->update_itemMasterNewStock_credit_sales_Item_exceeded($shiftID);
            /** CREDIT SALES - WAREHOUSE ITEM MASTER STOCK UPDATE */
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_credit_sales_Item_exceeded($shiftID);
        } else {
            /** CREDIT SALES - ITEM MASTER STOCK UPDATE */
            $this->Pos_restaurant_accounts->update_itemMasterNewStock_credit_sales($shiftID);
            /** CREDIT SALES - WAREHOUSE ITEM MASTER STOCK UPDATE */
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_credit_sales($shiftID);
        }

        /** CREDIT SALES - ITEM LEDGER  */


        if ($exceededItem) {
            // item ledger entry
            //$this->Pos_restaurant_accounts->creditSales_adjust_item_master($shiftID);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('w', 'Error while updating:  <br/><br/>' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Double Entries Updated');
        }
    }


    public function fetch_usergroup()
    {
        $posType = $this->input->post('posType');
        $this->datatables->select('userGroupMasterID, description, companyID, isActive')
            ->from('srp_erp_pos_auth_usergroupmaster')
            ->add_column('action', '$1', 'usergroup_action(userGroupMasterID, description, isActive)')
            ->edit_column('Active', '$1', 'load_active_usergroups(userGroupMasterID,isActive)')
            //->where('srp_erp_pos_auth_usergroupmaster.isActive', 1)
            ->where('srp_erp_pos_auth_usergroupmaster.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_pos_auth_usergroupmaster.posType', $posType);
        echo $this->datatables->generate();
    }

    function update_usergroup_isactive()
    {
        echo json_encode($this->Pos_model->update_usergroup_isactive());
    }

    public function save_userGroup()
    {
        $this->form_validation->set_rules('description', 'User Group', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->save_userGroup());
        }
    }

    public function update_userGroup()
    {
        $this->form_validation->set_rules('description', 'User Group', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->update_userGroup());
        }
    }

    public function fetch_user_for_group()
    {
        $search = $_REQUEST["sSearch"];
        $userGroupMasterID = $this->input->post('userGroupMasterID');
        $likesearch = '';
        if ($search) {
            $likesearch = "AND (`EIdNo` LIKE '%$search%' OR `ECode` LIKE '%$search%' OR `Ename2` LIKE '%$search%' OR `srp_designation`.`DesDescription` LIKE '%$search%')";
        }
        $where = "(srp_employeesdetails.pos_userGroupMasterID is null OR srp_employeesdetails.pos_userGroupMasterID = " . $userGroupMasterID . ") $likesearch";

        $this->datatables->select('EIdNo, ECode, Ename2, srp_designation.DesDescription as DesDescription')->from('srp_employeesdetails')->join('srp_designation', 'srp_employeesdetails.EmpDesignationId=srp_designation.DesignationID', 'left');
        $this->datatables->add_column('action', '$1', 'usergroupuser_action(EIdNo,' . $userGroupMasterID . ')');
        $this->datatables->where('srp_employeesdetails.isDischarged', 0);
        /*$this->datatables->where('srp_employeesdetails.pos_userGroupMasterID', null)
        $this->datatables->or_where('srp_employeesdetails.pos_userGroupMasterID', $userGroupMasterID)*/
        $this->datatables->where($where);
        /*if ($search) {
            $this->datatables->like('Ename2', $search);
            $this->datatables->or_like('ECode', $search);
            $this->datatables->or_like('srp_designation.DesDescription', $search);
        }*/
        $this->datatables->where('srp_employeesdetails.Erp_companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    public function fetch_assigned_users()
    {
        echo json_encode($this->Pos_model->fetch_assigned_users());
    }

    function save_usergroup_users()
    {
        /*$this->form_validation->set_rules('empID[]', 'Employee ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', 'Select Employee'));
        } else {*/
        echo json_encode($this->Pos_model->save_usergroup_users());
        //}
    }

    function getInvoiceCode()
    {
        echo json_encode($this->Pos_model->getInvoiceCode());
    }

    function submit_pos_payments()
    {
        $totalPayment = $this->input->post('paid');
        $netTotalAmount = $this->input->post('total_payable_amt');
        $customerID = $this->input->post('customerID');
        $cardTotalAmount = $this->input->post('cardTotalAmount');
        $CreditSalesAmnt = $this->input->post('CreditSalesAmnt');

        if ($totalPayment < $netTotalAmount) { /*&& $customerID == 0*/
            echo json_encode(array('e', 'Please enter payment amount greater than net total'));
            exit;
        }

        if ($cardTotalAmount > $netTotalAmount) {
            echo json_encode(array('e', 'Card and Cheque Amount sum can not be greater than net total.'));
            exit;
        }

        if (($CreditSalesAmnt < $netTotalAmount || $CreditSalesAmnt > $netTotalAmount) && $CreditSalesAmnt > 0) {
            echo json_encode(array('e', 'Payment not equal to Net total.'));
            exit;
        }

        $isMinusAllowed = getPolicyValues('MQT', 'All');
        if ($isMinusAllowed == ' ' || empty($isMinusAllowed) || $isMinusAllowed == null) {
            $isMinusAllowed = 0;
        }
        if ($isMinusAllowed == 1) {

            $itemQty = $this->input->post('itemQty');
            $itemID = $this->input->post('itemID');
            $itemUOM = $this->input->post('itemUOM');

            $itemtotal = 0;
            foreach ($itemQty as $row) {
                $itemtotal += $row;
            }

            $insufficent = array();
            foreach ($itemID as $key => $valu) {
                $itemtotals = 0;
               
                foreach ($itemID as $keys => $itmi) {
                    if ($valu == $itmi) {
                        $uom = $itemUOM[$keys];
                        $itemDetailsConversion = get_uom_details_items($valu,$uom);
                        $actual_default_qty =  $itemQty[$keys] / $itemDetailsConversion['conversion'];
                        $itemtotals += $actual_default_qty;
                    }
                }

                $companyID = current_companyID();
                $wareHouseID = $this->common_data['ware_houseID'];

                $mainCategory = $this->db->query("SELECT
                            mainCategory
                        FROM
                            srp_erp_itemmaster

                        WHERE
                        itemAutoID = '{$valu}'
                        ")->row_array();

                $items = $this->db->query(" SELECT
                            t2.itemAutoID,
                            t2.itemSystemCode,
                            t2.itemDescription,
                            ROUND(IFNULL( SUM( t1.transactionQTY / t1.convertionRate ), 0 ),5) AS currentStock,
                            srp_erp_warehouseitems.currentStock AS warehouseitemsCurrentStock,
                            t2.mainCategory AS mainCategory 
                        FROM
                            srp_erp_itemmaster t2
                            LEFT JOIN (select * from srp_erp_itemledger where wareHouseAutoID = '{$wareHouseID}') as t1 ON t1.itemAutoID = t2.itemAutoID 
                            LEFT JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID=t2.itemAutoID AND srp_erp_warehouseitems.wareHouseAutoID = '{$wareHouseID}'
                        WHERE
                            t2.companyID = '{$companyID}' 
                        AND t2.itemAutoID = '{$valu}' 
                            AND isActive = 1 
                        GROUP BY
                            t1.itemAutoID")->row_array();

                $bal = $items['currentStock'] - $itemtotals;


                if ($bal < 0 && ($mainCategory['mainCategory'] != 'Service' && $mainCategory['mainCategory'] != 'Non Inventory')) {
                    if ($items['currentStock'] == 0) {
                        $items['currentStock'] = $items['warehouseitemsCurrentStock'];
                    }
                    array_push($insufficent, array("itemCode" => $items['itemSystemCode'], "itemDesc" => $items['itemDescription'], "cruuentStock" => $items['currentStock']));
                }
            }

            if (!empty($insufficent)) {
                echo json_encode(array('w', 'Some items are insufficient.', $insufficent));
                exit;
            } else {
                echo json_encode($this->Pos_model->submit_pos_payments());
            }
        } else {
            echo json_encode($this->Pos_model->submit_pos_payments());
        }
    }

    function check_cash_gl_exist()
    {
        //Cash GL validation
        $paymentMethods_GLConfig = get_paymentMethods_GLConfig();
        $arry_exi = null;

        foreach ($paymentMethods_GLConfig as $data) {
            if ($data['autoID'] == 1) {
                $arry_exi = false;
                break;
            } else {
                $arry_exi = true;
            }
        }

        if ($arry_exi) {
            echo json_encode(array('e', 'Cash GL is not assigned to the outlet.'));
            exit;
        } else {
            echo json_encode(array());
        }
    }

    function creditNote_load()
    {
        echo json_encode($this->Pos_model->creditNote_load());
    }

    function savecustomer()
    {
        $this->form_validation->set_rules('customerName', 'customer Name', 'trim|required');
        $this->form_validation->set_rules('customerTelephone', 'Customer Telephone Number', 'trim|required');
        $this->form_validation->set_rules('customerEmail', 'Email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Pos_model->save_customer());
        }
    }

    /** remove this later */
    function batch_insert_credit_sales_invoice($shiftID)
    {
        $this->Pos_restaurant_accounts->pos_generate_invoices($shiftID);
    }

    function sync_customer_invoice()
    {
        echo json_encode($this->Pos_model->sync_customer_invoice());
    }

    function load_void_receipt()
    {
        $data['holdReceipt'] = null;
        $this->load->view('system/pos/ajax/ajax-restaurant-gpos-void', $data);
    }

    function loadVoidOrders()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];
        $companyID = $counterData['companyID'];
        $outletID = $counterData['wareHouseID'];

        $from = $this->input->post('datefrom');
        $fromDate = date('Y-m-d', strtotime($from));
        $to = $this->input->post('dateto');
        $toDate = date('Y-m-d', strtotime($to));


        $this->datatables->select('invoiceID as invoiceID, wareHouseAutoID as wareHouseAutoID, netTotal as subTotal, invoiceCode as invoiceCode, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, createdUserName,documentSystemCode as documentSystemCode', false)
            ->from('srp_erp_pos_invoice')
            ->join('srp_erp_reversal_documentsplit','srp_erp_pos_invoice.documentSystemCode = srp_erp_reversal_documentsplit.document_id','left')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(invoiceID)')
            ->add_column('voidBill', '$1', 'btn_voidBill_gpos(invoiceID,\'View\',wareHouseAutoID,documentSystemCode,invoiceCode)')
            //->add_column('subTotal', '$1', 'column_numberFormat(subTotal)')
            ->edit_column('subTotal', '<div>$1</div>', 'column_numberFormat(subTotal)')
            ->where('isVoid', 0)
            // ->where('DATE_FORMAT( createdDateTime ,\'%Y-%m-%d\')  BETWEEN "' . $fromDate . '" AND "' . $toDate . '"')
            ->where('shiftID', $shiftID)
            ->where(array('srp_erp_reversal_documentsplit.id'=>NULL))
            ->where('companyID', $companyID)
            ->where('wareHouseAutoID', $outletID);
        echo $this->datatables->generate();
        //echo $this->db->last_query();
    }

    function void_gpos()
    {
        echo json_encode($this->Pos_model->void_gpos());
    }

    function loadVaoidOrderHistory()
    {
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];
        $companyID = $counterData['companyID'];
        $outletID = $counterData['wareHouseID'];

        $from = $this->input->post('datefrom');
        $fromDate = date('Y-m-d', strtotime($from));
        $to = $this->input->post('dateto');
        $toDate = date('Y-m-d', strtotime($to));


        $this->datatables->select('invoiceID as invoiceID, wareHouseAutoID as wareHouseAutoID, netTotal as subTotal, invoiceCode as invoiceCode, DATE_FORMAT(createdDateTime,\'%d-%b-%Y\') as createdDate, createdUserName,documentSystemCode as documentSystemCode', false)
            ->from('srp_erp_pos_invoice')
            ->add_column('invoiceID', '$1', 'padZeros_saleInvoiceID(invoiceID)')
            ->add_column('voidBill', '$1', 'btn_voidBill_gpos_history(invoiceID,\'View\',wareHouseAutoID,documentSystemCode,invoiceCode)')
            ->edit_column('subTotal', '<div>$1</div>', 'column_numberFormat(subTotal)')
            ->where('isVoid', 1)
            ->where('shiftID', $shiftID)
            ->where('companyID', $companyID)
            ->where('wareHouseAutoID', $outletID);
        echo $this->datatables->generate();
    }

    function restaurant_shift_doubleEntry_fromReview($shiftID)
    {
        $this->db->trans_start();

        $outletID = get_outletID();
        $exceededItem = true;

        if ($exceededItem) {
            /** 0. ITEM EXCEEDED */
            $this->Pos_restaurant_accounts->update_itemExceededRecord_fromReview($shiftID, false);
        }
        /*item ledger insert feom review*/
        $this->Pos_restaurant_accounts->update_itemLedger_fromReview($shiftID, false);
        /** 1. REVENUE */
        $this->Pos_restaurant_accounts->update_generalLedger_fromReview($shiftID);
        $this->Pos_restaurant_accounts->update_bankLedger_fromReview($shiftID);
        $this->Pos_restaurant_accounts->update_exceededGL_generalLedger($shiftID);

        $this->Pos_restaurant_accounts->update_itemMasterNewStock_itemExceeded($shiftID);
        $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_itemExceeded($shiftID);

        $CS = " SELECT *  FROM srp_erp_pos_menusalesmaster  WHERE isCreditSales = 1 AND wareHouseAutoID = '" . $outletID . "'  AND shiftID = '" . $shiftID . "'";
        $resultCS = $this->db->query($CS)->result_array();
        if (!empty($resultCS)) {
            foreach ($resultCS as $val) {
                $menuSalesID = $val['menuSalesID'];
                $this->db->select('invoiceAutoID');
                $this->db->from('srp_erp_customerinvoicemaster');
                $this->db->where('posMasterAutoID', $menuSalesID);
                $row = $this->db->get()->row_array();
                if (!empty($row['invoiceAutoID'])) {
                    if (isset($val['isVoid']) && $val['isVoid'] == 1) {
                        //for voided bills - delete invoice details
                        $update_data = array(
                            'isDeleted' => 1,
                            'deletedEmpID' => current_userID(),
                            'deletedDate' => current_date(),
                        );
                        $this->db->delete('srp_erp_customerinvoicedetails', array('invoiceAutoID' => $row['invoiceAutoID']));
                    } else {
                        //update confirm and update columns
                        $this->db->select('*');
                        $this->db->from('srp_erp_pos_shiftdetails');
                        $this->db->where('shiftID', $shiftID);
                        $shiftDetails = $this->db->get()->row_array();
                        $createdUserID = isset($shiftDetails['createdUserID']) ? $shiftDetails['createdUserID'] : '';
                        $createdUserName = isset($shiftDetails['createdUserName']) ? $shiftDetails['createdUserName'] : '';
                        $startTime = isset($shiftDetails['startTime']) ? $shiftDetails['startTime'] : '';

                        $update_data = array(
                            'confirmedYN' => 1,
                            'confirmedByEmpID' => $createdUserID,
                            'confirmedByName' => $createdUserName,
                            'confirmedDate' => $startTime,
                            'approvedYN' => 1,
                            'approvedDate' => $startTime,
                            'approvedbyEmpID' => $createdUserID,
                            'approvedbyEmpName' => $createdUserName,
                        );
                        //Document Approved Table Entries
                        $this->Pos_restaurant_accounts->document_approved_entries_for_invoices($row['invoiceAutoID']);
                    }

                    $this->db->update('srp_erp_customerinvoicemaster', $update_data, array('posMasterAutoID' => $menuSalesID)); //update invoice master
                } else {
                    if ($row['isCreditSales'] == 1) {
                        $invSequenceCodeDet = $this->getInvoiceSequenceCode();
                        $lastINVNo = $invSequenceCodeDet['lastINVNo'];
                        $sequenceCode = $invSequenceCodeDet['sequenceCode'];
                        $this->db->select('customerAutoID');
                        $this->db->from('srp_erp_pos_menusalespayments');
                        $this->db->where('menuSalesID', $row['menuSalesID']);
                        $custmrs = $this->db->get()->row_array();
                        $customerID = $custmrs['customerAutoID'];
                        $cusData = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName, receivableAutoID,
                                             receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType,
                                             customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces,customerAddress1,customerTelephone
                                             FROM srp_erp_customermaster WHERE customerAutoID={$customerID}")->row_array();

                        $data_customer_invoice['invoiceType'] = 'Direct';
                        $data_customer_invoice['documentID'] = 'CINV';
                        $data_customer_invoice['posTypeID'] = 1;
                        $data_customer_invoice['referenceNo'] = $sequenceCode;
                        $data_customer_invoice['invoiceNarration'] = 'POS Credit Sales - ' . $sequenceCode;
                        $data_customer_invoice['posMasterAutoID'] = $row['menuSalesID'];
                        $data_customer_invoice['invoiceDate'] = current_date();
                        $data_customer_invoice['invoiceDueDate'] = current_date();
                        $data_customer_invoice['customerInvoiceDate'] = current_date();
                        $data_customer_invoice['invoiceCode'] = $this->sequence->sequence_generator($data_customer_invoice['documentID']);
                        $customerInvoiceCode = $data_customer_invoice['invoiceCode'];
                        $data_customer_invoice['companyFinanceYearID'] = $this->common_data['company_data']['companyFinanceYearID'];
                        $financialYear = get_financial_from_to($this->common_data['company_data']['companyFinanceYearID']);
                        $data_customer_invoice['companyFinanceYear'] = trim($financialYear['beginingDate'] ?? '') . ' - ' . trim($financialYear['endingDate'] ?? '');
                        $data_customer_invoice['FYBegin'] = trim($financialYear['beginingDate'] ?? '');
                        $data_customer_invoice['FYEnd'] = trim($financialYear['endingDate'] ?? '');
                        $data_customer_invoice['FYPeriodDateFrom'] = trim($this->common_data['company_data']['FYPeriodDateFrom']);
                        $data_customer_invoice['FYPeriodDateTo'] = trim($this->common_data['company_data']['FYPeriodDateTo']);
                        $data_customer_invoice['companyFinancePeriodID'] = $this->common_data['company_data']['companyFinancePeriodID'];
                        $data_customer_invoice['customerID'] = $customerID;
                        $data_customer_invoice['customerSystemCode'] = $cusData['customerSystemCode'];
                        $data_customer_invoice['customerName'] = $cusData['customerName'];
                        $data_customer_invoice['customerAddress'] = $cusData['customerAddress1'];
                        $data_customer_invoice['customerTelephone'] = $cusData['customerTelephone'];
                        $data_customer_invoice['customerFax'] = $cusData['customerTelephone'];
                        $data_customer_invoice['customerEmail'] = $cusData['customerTelephone'];
                        $data_customer_invoice['customerReceivableAutoID'] = $cusData['receivableAutoID'];
                        $data_customer_invoice['customerReceivableSystemGLCode'] = $cusData['receivableSystemGLCode'];
                        $data_customer_invoice['customerReceivableGLAccount'] = $cusData['receivableGLAccount'];
                        $data_customer_invoice['customerReceivableDescription'] = $cusData['receivableDescription'];
                        $data_customer_invoice['customerReceivableType'] = $cusData['receivableType'];
                        $data_customer_invoice['customerCurrency'] = $cusData['customerCurrency'];
                        $data_customer_invoice['customerCurrencyID'] = $cusData['customerCurrencyID'];
                        $data_customer_invoice['customerCurrencyDecimalPlaces'] = $cusData['customerCurrencyDecimalPlaces'];

                        $data_customer_invoice['confirmedYN'] = 1;
                        $data_customer_invoice['confirmedByEmpID'] = current_userID();
                        $data_customer_invoice['confirmedByName'] = current_user();
                        $data_customer_invoice['confirmedDate'] = current_date();
                        $data_customer_invoice['approvedYN'] = 1;
                        $data_customer_invoice['approvedDate'] = current_date();
                        $data_customer_invoice['currentLevelNo'] = 1;
                        $data_customer_invoice['approvedbyEmpID'] = current_userID();
                        $data_customer_invoice['approvedbyEmpName'] = current_user();

                        $data_customer_invoice['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                        $data_customer_invoice['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                        $data_customer_invoice['transactionExchangeRate'] = 1;
                        $data_customer_invoice['transactionAmount'] = 0;
                        $data_customer_invoice['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_customer_invoice['transactionCurrencyID']);
                        $data_customer_invoice['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                        $data_customer_invoice['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                        $default_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyLocalCurrencyID']);
                        $data_customer_invoice['companyLocalExchangeRate'] = $default_currency['conversion'];
                        $data_customer_invoice['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                        $data_customer_invoice['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                        $data_customer_invoice['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                        $reporting_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['companyReportingCurrencyID']);
                        $data_customer_invoice['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                        $data_customer_invoice['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                        $customer_currency = currency_conversionID($data_customer_invoice['transactionCurrencyID'], $data_customer_invoice['customerCurrencyID']);
                        $data_customer_invoice['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
                        $data_customer_invoice['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
                        $data_customer_invoice['companyCode'] = $this->common_data['company_data']['company_code'];
                        $data_customer_invoice['companyID'] = $this->common_data['company_data']['company_id'];
                        $data_customer_invoice['createdUserGroup'] = $this->common_data['user_group'];
                        $data_customer_invoice['createdPCID'] = $this->common_data['current_pc'];
                        $data_customer_invoice['createdUserID'] = $this->common_data['current_userID'];
                        $data_customer_invoice['createdUserName'] = $this->common_data['current_user'];
                        $data_customer_invoice['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_customerinvoicemaster', $data_customer_invoice);
                        $customerInvoiceMasterID = $this->db->insert_id();

                        if ($customerInvoiceMasterID) {
                            $doc_approved['departmentID'] = "CINV";
                            $doc_approved['documentID'] = "CINV";
                            $doc_approved['documentCode'] = $data_customer_invoice['invoiceCode'];
                            $doc_approved['documentSystemCode'] = $customerInvoiceMasterID;
                            $doc_approved['documentDate'] = current_date();
                            $doc_approved['approvalLevelID'] = 1;
                            $doc_approved['docConfirmedDate'] = current_date();
                            $doc_approved['docConfirmedByEmpID'] = current_userID();
                            $doc_approved['table_name'] = 'srp_erp_customerinvoicemaster';
                            $doc_approved['table_unique_field_name'] = 'invoiceAutoID';
                            $doc_approved['approvedEmpID'] = current_userID();
                            $doc_approved['approvedYN'] = 1;
                            $doc_approved['approvedComments'] = 'Approved from POS';
                            $doc_approved['approvedPC'] = current_pc();
                            $doc_approved['approvedDate'] = current_date();
                            $doc_approved['companyID'] = current_companyID();
                            $doc_approved['companyCode'] = current_company_code();
                            $this->db->insert('srp_erp_documentapproved', $doc_approved);

                            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,segmentID,segmentCode,transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,customerCurrencyDecimalPlaces');
                            $this->db->from('srp_erp_customerinvoicemaster');
                            $this->db->where('invoiceAutoID', $customerInvoiceMasterID);
                            $master = $this->db->get()->row_array();

                            $data_customer_invoice_detail['invoiceAutoID'] = $customerInvoiceMasterID;
                            $data_customer_invoice_detail['type'] = 'GL';
                            $data_customer_invoice_detail['description'] = 'POS Sales - ' . $sequenceCode;
                            $data_customer_invoice_detail['transactionAmount'] = round(0, $master['transactionCurrencyDecimalPlaces']);
                            $companyLocalAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyLocalExchangeRate'];
                            $data_customer_invoice_detail['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                            $companyReportingAmount = $data_customer_invoice_detail['transactionAmount'] / $master['companyReportingExchangeRate'];
                            $data_customer_invoice_detail['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                            $customerAmount = $data_customer_invoice_detail['transactionAmount'] / $master['customerCurrencyExchangeRate'];
                            $data_customer_invoice_detail['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);

                            $data_customer_invoice_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                            $data_customer_invoice_detail['companyID'] = $this->common_data['company_data']['company_id'];
                            $data_customer_invoice_detail['createdUserGroup'] = $this->common_data['user_group'];
                            $data_customer_invoice_detail['createdPCID'] = $this->common_data['current_pc'];
                            $data_customer_invoice_detail['createdUserID'] = $this->common_data['current_userID'];
                            $data_customer_invoice_detail['createdUserName'] = $this->common_data['current_user'];
                            $data_customer_invoice_detail['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_customerinvoicedetails', $data_customer_invoice_detail);
                        }
                    }
                }
                /** 0. CUSTOMER INVOICE - Credit Sales Entries  */
                // $this->Pos_restaurant_accounts->pos_generate_invoices_from_review($shiftID, $menuSalesID);  // updated on bill submit

                if ($exceededItem) {
                    $query = " SELECT documentMasterAutoID  FROM srp_erp_pos_menusalesmaster  WHERE menuSalesID = '" . $menuSalesID . "'  ";
                    $docid = $this->db->query($query)->row_array();
                    $this->Pos_restaurant_accounts->update_itemExceededRecord_creditSales_menuSalesID($shiftID, $menuSalesID);

                    $this->Pos_restaurant_accounts->update_itemLedger_fromReview_creditsales($shiftID, $menuSalesID, $docid['documentMasterAutoID']);
                    $this->Pos_restaurant_accounts->update_generalLedger_fromReview_creditsales($shiftID, $menuSalesID, $docid['documentMasterAutoID']);
                }
            }

            $this->Pos_restaurant_accounts->update_exceededGL_generalLedger_credit_sales($shiftID); // outletID => JOIN, WHERE condition corrected
            $this->Pos_restaurant_accounts->update_itemMasterNewStock_credit_sales_Item_exceeded($shiftID);
            $this->Pos_restaurant_accounts->update_warehouseItemMasterNewStock_credit_sales_Item_exceeded($shiftID);
        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('w', 'Error while updating:  <br/><br/>' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Double Entries Updated');
        }
    }

    function delete_UserGroup()
    {
        echo json_encode($this->Pos_model->delete_UserGroup());
    }

    function delete_Aut_process()
    {
        echo json_encode($this->Pos_model->delete_Aut_process());
    }

    function check_if_item_qty_exceeded()
    {
        $companyID = current_companyID();
        $wareHouseID = $this->common_data['ware_houseID'];
        $itemAutoID = $this->input->post('itemAutoID');

        $mainCategory = $this->db->query("SELECT
                    mainCategory
                FROM
                    srp_erp_itemmaster

                WHERE
                itemAutoID = '{$itemAutoID}'
                ")->row_array();


        $items = $this->db->query("SELECT
                t1.itemAutoID,
                t1.itemSystemCode,
                t1.itemDescription,
                -- ROUND(IFNULL(SUM(t1.transactionQTY/t1.convertionRate),0),5) AS currentStock,
                t1.currentStock,
                t2.mainCategory as mainCategory
            FROM
                srp_erp_warehouseitems t1
            JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
            WHERE
                t2.companyID = '{$companyID}'
            AND t1.wareHouseAutoID = '{$wareHouseID}'
            AND t1.itemAutoID = '{$itemAutoID}'
            AND t2.isActive = 1
            ")->row_array();

        //GROUP BY t1.itemAutoID

        $bal = $items['currentStock'];

        if ($bal <= 0 && ($mainCategory['mainCategory'] != 'Service' && $mainCategory['mainCategory'] != 'Non Inventory')) {
            echo json_encode(array('w', 'Selected item is insufficient.', $items['mainCategory']));
            exit;
        } else {
            echo json_encode(array('s', 'Success.'));
            exit;
        }

    }

    function fetch_auth_process()
    {
        $this->datatables->select('srp_erp_pos_auth_processassign.processMasterID as processMasterID,description,processAssignID,srp_erp_pos_auth_processassign.isActive as isActive,', false)
            ->from('srp_erp_pos_auth_processassign')
            ->join('srp_erp_pos_auth_processmaster', 'srp_erp_pos_auth_processmaster.processMasterID = srp_erp_pos_auth_processassign.processMasterID', 'left')
            ->where('srp_erp_pos_auth_processassign.companyID', current_companyID())
            ->where('srp_erp_pos_auth_processassign.posType', 2)
            ->edit_column('action', '$1', 'load_auth_process_action(processMasterID,description,processAssignID)')
            ->edit_column('active', '$1', 'load_active_auth_process(processMasterID,isActive)');
        echo $this->datatables->generate();
    }

    function addProcess()
    {
        $this->form_validation->set_rules('processMasterID[]', 'Process', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_model->addProcess());
        }
    }

    function add_user_group()
    {
        $this->form_validation->set_rules('wareHouseID', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('userGroupMasterID[]', 'User group', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_model->add_user_group());
        }
    }

    public function fetch_user_for_group_gpos()
    {
        $search = $_REQUEST["sSearch"];
        $userGroupMasterID = $this->input->post('userGroupMasterID');
        $likesearch = '';
        if ($search) {
            $likesearch = "AND (`EIdNo` LIKE '%$search%' OR `ECode` LIKE '%$search%' OR `Ename2` LIKE '%$search%' OR `srp_designation`.`DesDescription` LIKE '%$search%')";
        }
        $where = "(srp_employeesdetails.pos_userGroupMasterID_gpos is null OR srp_employeesdetails.pos_userGroupMasterID_gpos = " . $userGroupMasterID . ") $likesearch";

        $this->datatables->select('EIdNo, ECode, Ename2, srp_designation.DesDescription as DesDescription')->from('srp_employeesdetails')->join('srp_designation', 'srp_employeesdetails.EmpDesignationId=srp_designation.DesignationID', 'left');
        $this->datatables->add_column('action', '$1', 'usergroupuser_action(EIdNo,' . $userGroupMasterID . ')');
        $this->datatables->where('srp_employeesdetails.isDischarged', 0);
        $this->datatables->where($where);
        $this->datatables->where('srp_employeesdetails.Erp_companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    function save_usergroup_users_gpos()
    {
        echo json_encode($this->Pos_model->save_usergroup_users_gpos());
    }

    public function fetch_assigned_users_gpos()
    {
        echo json_encode($this->Pos_model->fetch_assigned_users_gpos());
    }

    function update_superadmin_warehouse()
    {
        echo json_encode($this->Pos_model->update_superadmin_warehouse());
    }

    function update_warehouse_admin()
    {
        echo json_encode($this->Pos_model->update_warehouse_admin());
    }

    function delete_gpos_hold_bills()
    {
        echo json_encode($this->Pos_model->delete_gpos_hold_bills());
    }

    function load_emp_from_warehouse()
    {
        $data_arr = array();
        $warehouse = $this->input->post('warehouse');
        $company = $this->common_data['company_data']['company_id'];
        if (!empty($warehouse)) {
            $wareHouseID = join(",", $warehouse);
        } else {
            $wareHouseID = 0;
        }

        $contract = $this->db->query("SELECT srp_employeesdetails.EIdNo as eidno,srp_employeesdetails.Ename2 AS empName FROM srp_erp_warehouse_users Left JOIN srp_employeesdetails ON srp_erp_warehouse_users.userID=srp_employeesdetails.EIdNo  WHERE wareHouseID IN ($wareHouseID) AND srp_erp_warehouse_users.companyID= $company group by srp_erp_warehouse_users.userID ")->result_array();
        if (!empty($contract)) {
            foreach ($contract as $row) {
                $data_arr[trim($row['eidno'] ?? '')] = trim($row['empName'] ?? '');
            }
        }
        echo form_dropdown('employee[]', $data_arr, '', 'class="form-control select2" id="employee"  multiple="" ');
    }


    function load_loyalty_table()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $this->datatables->select('srp_erp_pos_loyaltycard.cardMasterID as cardMasterID, barcode, srp_erp_pos_loyaltycard.isActive as isActive,outletID,srp_erp_customermaster.customerName as customerName,srp_erp_customermaster.customerTelephone as customerTelephone,pts.totpoints as totpoints')
            ->from('srp_erp_pos_loyaltycard')
            ->join('srp_erp_customermaster', 'srp_erp_pos_loyaltycard.customerID = srp_erp_customermaster.customerAutoID', 'left')
            ->join('(SELECT cardMasterID,sum(points) as totpoints FROM srp_erp_pos_loyaltytopup  WHERE srp_erp_pos_loyaltytopup.companyID =  \'' . $companyid . '\'    GROUP BY cardMasterID) pts', '(pts.cardMasterID = srp_erp_pos_loyaltycard.cardMasterID)', 'left')
            ->add_column('action', '$1', 'loyalty_action(cardMasterID,isActive)')
            ->edit_column('total_pts', '<div class="pull-right"> $1 </div>', 'totpoints')
            ->edit_column('Active', '$1', 'load_general_active_loyalty(cardMasterID,isActive)')
            ->where('srp_erp_pos_loyaltycard.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    function load_customer_name_for_telephone_no()
    {
        $telephone = $this->input->post('telephone');
        $customer = $this->db->select('customerName,customerAutoID')
            ->from('srp_erp_customermaster')
            ->where('customerTelephone', $telephone)
            ->get()->row_array();
        if (!empty($customer)) {
            echo json_encode(array('error' => 0, 'message' => 'done', 'CustomerName' => $customer['customerName'], 'posCustomerAutoID' => $customer['customerAutoID']));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'customer not registered!'));
        }
    }

    function load_barcode_loyalty()
    {
        echo json_encode($this->Pos_model->load_barcode_loyalty());
    }

    function save_loyalty_card()
    {
        $this->form_validation->set_rules('barcode', 'Barcode', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->save_loyalty_card());
        }
    }

    function edit_loyalty()
    {
        echo json_encode($this->Pos_model->edit_loyalty());
    }

    function load_points_table()
    {
        $this->datatables->select('pointSetupID,srp_erp_currencymaster.CurrencyName,amount,loyaltyPoints,isActive, priceToPointsEarned,pointsToPriceRedeemed,minimumPointstoRedeem,
        poinforPuchaseAmount,
        purchaseRewardPoint')
            ->from('srp_erp_loyaltypointsetup')
            ->join('srp_erp_currencymaster', 'srp_erp_loyaltypointsetup.currencyID = srp_erp_currencymaster.currencyID', 'left')
            ->edit_column('active', '$1', 'load_active_points(pointSetupID,isActive)')
            ->where('srp_erp_loyaltypointsetup.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
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
            echo json_encode($this->Pos_model->add_points());
        }
    }

    function update_point_active()
    {
        echo json_encode($this->Pos_model->update_point_active());
    }

    function delete_loyalty_card()
    {
        echo json_encode($this->Pos_model->delete_loyalty_card());
    }

    function load_redeem_details_from_barcode_telno()
    {
        $telephone = $this->input->post('telephone');
        $barcode = $this->input->post('barcode');
        $valu = $this->input->post('valu');
        $companyid = $this->common_data['company_data']['company_id'];
        $this->db->select('customerName,customerAutoID,pts.totpoints,customerTelephone,barcode,customerSystemCode,srp_erp_customermaster.customerCurrency as customerCurrency');
        $this->db->from('srp_erp_pos_loyaltycard');
        $this->db->join('srp_erp_customermaster', 'srp_erp_pos_loyaltycard.customerID = srp_erp_customermaster.customerAutoID', 'left');
        $this->db->join('(SELECT cardMasterID,sum(points) as totpoints FROM srp_erp_pos_loyaltytopup  WHERE srp_erp_pos_loyaltytopup.companyID =  \'' . $companyid . '\'    GROUP BY cardMasterID) pts', '(pts.cardMasterID = srp_erp_pos_loyaltycard.cardMasterID)', 'left');
        if ($valu == 'Tel') {
            $this->db->where('customerTelephone', $telephone);
        } else {
            $this->db->where('barcode', $barcode);
        }
        $this->db->where('srp_erp_pos_loyaltycard.isActive', 1);
        $customer = $this->db->get()->row_array();
        if (!empty($customer)) {
            echo json_encode(array('error' => 0, 'message' => 'done', 'CustomerName' => $customer['customerName'], 'posCustomerAutoID' => $customer['customerAutoID'], 'totpoints' => $customer['totpoints'], 'barcode' => $customer['barcode'], 'customerTelephone' => $customer['customerTelephone'], 'customerSystemCode' => $customer['customerSystemCode'], 'customerCurrency' => $customer['customerCurrency']));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'customer not registered!'));
        }
    }

    function set_customer_inloyalty()
    {
        $posCustomerAutoID = $this->input->post('posCustomerAutoID');
        $companyid = $this->common_data['company_data']['company_id'];
        $this->db->select('customerName,customerAutoID,IFNULL(pts.totpoints,0) as totpoints,customerTelephone,barcode,customerSystemCode,srp_erp_customermaster.customerCurrency as customerCurrency');
        $this->db->from('srp_erp_pos_loyaltycard');
        $this->db->join('srp_erp_customermaster', 'srp_erp_pos_loyaltycard.customerID = srp_erp_customermaster.customerAutoID', 'left');
        $this->db->join('(SELECT cardMasterID,sum(points) as totpoints FROM srp_erp_pos_loyaltytopup join srp_erp_pos_invoice on srp_erp_pos_invoice.invoiceID=srp_erp_pos_loyaltytopup.invoiceID WHERE srp_erp_pos_loyaltytopup.companyID =  \'' . $companyid . '\' and srp_erp_pos_invoice.isVoid!=1 GROUP BY cardMasterID) pts', '(pts.cardMasterID = srp_erp_pos_loyaltycard.cardMasterID)', 'left');
        $this->db->where('srp_erp_pos_loyaltycard.customerID', $posCustomerAutoID);
        $this->db->where('srp_erp_pos_loyaltycard.isActive', 1);
        $customer = $this->db->get()->row_array();
        $points = $this->db->query("SELECT pointsToPriceRedeemed,minimumPointstoRedeem,loyaltyPoints FROM srp_erp_loyaltypointsetup WHERE companyID={$companyid} AND isActive=1")->row_array();
        $loyalty_setup = $this->db->query("select * from srp_erp_loyaltypointsetup where isActive = 1")->row_array();

        //        $points_query = $this->db->query("SELECT SUM(points) as points FROM `srp_erp_pos_loyaltytopup`
        //join srp_erp_pos_invoice on srp_erp_pos_invoice.invoiceID=srp_erp_pos_loyaltytopup.invoiceID
        //WHERE posCustomerAutoID=$posCustomerAutoID and srp_erp_pos_invoice.isVoid!=1")->row_array();

        if (!empty($customer)) {
            echo json_encode(array('error' => 0, 'message' => 'done', 'CustomerName' => $customer['customerName'], 'posCustomerAutoID' => $customer['customerAutoID'], 'totpoints' => $customer['totpoints'], 'barcode' => $customer['barcode'], 'customerTelephone' => $customer['customerTelephone'], 'customerSystemCode' => $customer['customerSystemCode'], 'customerCurrency' => $customer['customerCurrency'], 'pointsToPriceRedeemed' => $points['pointsToPriceRedeemed'], 'minimumPointstoRedeem' => $points['minimumPointstoRedeem'], 'loyaltyPoints' => $points['loyaltyPoints'], 'exchangeRate' => $loyalty_setup['amount'], 'poinforPuchaseAmount' => $loyalty_setup['poinforPuchaseAmount'], 'purchaseRewardPoint' => $loyalty_setup['purchaseRewardPoint']));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'customer not registered!'));
        }
    }

    public function load_loyalty_cus()
    {
        $search_string = "%" . $this->input->get('query') . "%";
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $dataArr = array();
        $dataArr2 = array();


        $data = $this->db->query("SELECT customerName,customerAutoID,customerTelephone FROM srp_erp_customermaster WHERE companyID={$companyID}
                                  AND customerTelephone LIKE '{$search_string}' ORDER BY customerAutoID ASC LIMIT 20")->result_array();

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array(
                    'value' => $val['customerTelephone'] . ' - ' . $val["customerName"],
                    'customerName' => $val["customerName"],
                    'data' => $val['customerAutoID'],
                    'tel' => $val['customerTelephone'],
                );
            }
        }

        $dataArr2['suggestions'] = $dataArr;

        echo json_encode($dataArr2);
    }


    function load_redeem_details_from_telno()
    {
        $search_string = "%" . $this->input->get('query') . "%";
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $dataArr = array();
        $dataArr2 = array();


        $data = $this->db->query("SELECT customerName,customerAutoID,pts.totpoints,customerTelephone,barcode,customerSystemCode,srp_erp_customermaster.customerCurrency as customerCurrency FROM srp_erp_pos_loyaltycard
 Left Join srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_loyaltycard.customerID
 Left Join (SELECT cardMasterID,sum(points) as totpoints FROM srp_erp_pos_loyaltytopup  WHERE srp_erp_pos_loyaltytopup.companyID =   $companyID     GROUP BY cardMasterID) pts ON pts.cardMasterID = srp_erp_pos_loyaltycard.cardMasterID
 WHERE srp_erp_pos_loyaltycard.companyID={$companyID}
                                  AND customerTelephone LIKE '{$search_string}' ORDER BY customerAutoID ASC LIMIT 20")->result_array();

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array(
                    'value' => $val["customerTelephone"] . ' - ' . $val["customerName"],
                    'data' => $val['customerAutoID'],
                    'tel' => $val['customerTelephone'],
                    'CustomerName' => $val['customerName'],
                    'totpoints' => $val['totpoints'],
                    'barcode' => $val['barcode'],
                    'customerSystemCode' => $val['customerSystemCode'],
                    'customerCurrency' => $val['customerCurrency'],
                );
            }
        }

        $dataArr2['suggestions'] = $dataArr;

        echo json_encode($dataArr2);
    }

    function load_pos_loyalty_card_report()
    {
        $data['customerID'] = $this->input->post('customerID');
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $customerIDs = $this->input->post('customerID');
        $actvstatus = $this->input->post('actvstatus');

        if (!empty($tmpFilterDateTo)) {
            $tmpFilterDateTo = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $tmpFilterDateTo = date('Y-m-d 23:59:59');
        }

        if (isset($customerIDs) && !empty($customerIDs)) {
            $customer = join(",", $customerIDs);
            $customers = $customer;
        } else {
            $customers = null;
        }
        $data['gift_card_details'] = $this->Pos_model->get_loyalty_card_details($tmpFilterDateTo, $customers, $actvstatus);
        $loyalty_setup = $this->Pos_model->get_loyalty_setup_details();
        if ($loyalty_setup['status'] == 'success') {
            $data['exchange_rate'] = $loyalty_setup['loyalty_setup']['amount'];
        } else {
            $data['exchange_rate'] = null;
        }
        $this->load->view('system/pos-general/reports/pos_loyalty_card_report.php', $data);
    }


    function load_pos_loyalty_card_topup_redeem_report()
    {

        $tmpFilterDateFrom = trim(str_replace('/', '-', $this->input->post('filterFrom3')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo3')));

        $customerIDs = $this->input->post('customer');
        if (isset($customerIDs) && !empty($customerIDs)) {
            $customer = join(",", $customerIDs);
            $customers = $customer;
        } else {
            $customers = 0;
        }

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

        $data['gift_card_details'] = $this->Pos_model->pos_loyalty_card_topup_redeem_report($tmpFilterDateFrom, $tmpFilterDateTo, $customers);

        $this->load->view('system/pos-general/reports/pos_loyalty_card_topup_redeem_report', $data);
    }

    function updateDiscountConfig()
    {
        $this->form_validation->set_rules('capAmount', 'Cap Amount', 'trim|required');
        $this->form_validation->set_rules('capPercentage', 'Percentage', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->Pos_model->updateDiscountConfig());
        }
    }

    function assign_capamnt_percentage()
    {
        echo json_encode($this->Pos_model->assign_capamnt_percentage());
    }

    function invoice_searchLiveSearch_date_wise()
    {
        $invoiceCode = "%" . $this->input->post('invoiceCode') . "%";

        $fromdate = trim(str_replace('/', '-', $this->input->post('filterFrom2')));
        $todate = trim(str_replace('/', '-', $this->input->post('filterTo2')));

        if (isset($fromdate) && !empty($fromdate)) {
            $filterFrom = date('Y-m-d H:i:s', strtotime($fromdate));
        } else {
            $filterFrom = date('Y-m-d 00:00:00');
        }


        if (!empty($todate)) {
            $dateTo = date('Y-m-d H:i:s', strtotime($todate));
        } else {
            $dateTo = date('Y-m-d 23:59:59');
        }
        $where = '';
        if (!empty($invoiceCode)) {
            $where = " AND invoiceCode LIKE '{$invoiceCode}'";
        }

        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $dataArr = array();
        $dataArr2 = array();

        $data = $this->db->query("SELECT documentSystemCode,invoiceCode,invoiceDate FROM srp_erp_pos_invoice WHERE companyID={$companyID} AND wareHouseAutoID={$wareHouse}
                            AND ( invoiceDate  BETWEEN '" . $filterFrom . "' AND '" . $dateTo . "' ) AND isVoid=0 $where ORDER BY documentSystemCode ASC")->result_array();

        if (!empty($data)) {
            echo json_encode(array('s', 'Success', $data));
        } else {
            echo json_encode(array('e', 'No Invoices for given date'));
        }
    }


    function invoice_searchLiveSearch_item_wise()
    {
        $itemAutoID = $this->input->post('itemAutoID');

        if (empty($itemAutoID)) {
            echo json_encode(array('e', 'Select Item'));
            exit();
        }


        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];


        $data = $this->db->query("SELECT
	srp_erp_pos_invoice.documentSystemCode,
	srp_erp_pos_invoice.invoiceCode 
FROM
	srp_erp_pos_invoicedetail
	INNER JOIN srp_erp_pos_invoice ON srp_erp_pos_invoice.invoiceID = srp_erp_pos_invoicedetail.invoiceID 
WHERE
	srp_erp_pos_invoice.companyID = $companyID 
	AND srp_erp_pos_invoice.wareHouseAutoID = $wareHouse 
	AND srp_erp_pos_invoice.isVoid=0 
	AND srp_erp_pos_invoicedetail.itemAutoID = $itemAutoID 
GROUP BY
	srp_erp_pos_invoicedetail.invoiceID 
ORDER BY
	srp_erp_pos_invoice.documentSystemCode ASC 
	 ")->result_array();

        if (!empty($data)) {
            echo json_encode(array('s', 'Success', $data));
        } else {
            echo json_encode(array('e', 'No Invoices for selected Item'));
        }
    }


    function invoice_itemLiveSearch()
    {
        $search_string = "%" . $this->input->get('query') . "%";
        $companyID = current_companyID();
        $wareHouse = $this->common_data['ware_houseID'];
        $dataArr = array();
        $dataArr2 = array();

        $data = $this->db->query("SELECT itemAutoID,seconeryItemCode,CONCAT(seconeryItemCode, ' - ', itemName) as itemName FROM srp_erp_itemmaster WHERE companyID={$companyID}
                                  AND seconeryItemCode LIKE '{$search_string}' OR itemName LIKE '{$search_string}' OR itemSystemCode LIKE '{$search_string}' ORDER BY itemAutoID ASC LIMIT 20")->result_array();

        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array(
                    'value' => $val["itemName"],
                    'data' => $val['itemAutoID'],
                );
            }
        }

        $dataArr2['suggestions'] = $dataArr;

        echo json_encode($dataArr2);
    }

    function fetch_loyalitydetail()
    {
        $customerAutoID = $this->input->post('customerAutoID');
        $netTotalAmount = $this->input->post('totalpayment');
        $availablepoints = $this->input->post('availablepoints');
        $companyID = current_companyID();
        $data['totpts'] = '';
        $data['pos_loyality'] = $this->db->query("SELECT
	`customerName`,
	`customerAutoID`,
		IFNULL(`pts`.`totpoints`,0) as totpoints,
	`customerTelephone`,
	`barcode` as loyalitycardno,
	`customerSystemCode`,
	`srp_erp_customermaster`.`customerCurrency` AS `customerCurrency`,
	srp_erp_pos_loyaltycard.cardMasterID
FROM
	`srp_erp_pos_loyaltycard`
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_pos_loyaltycard`.`customerID` = `srp_erp_customermaster`.`customerAutoID`
	LEFT JOIN ( SELECT cardMasterID, sum( points ) AS totpoints FROM srp_erp_pos_loyaltytopup WHERE srp_erp_pos_loyaltytopup.companyID = $companyID GROUP BY cardMasterID ) pts ON ( `pts`.`cardMasterID` = srp_erp_pos_loyaltycard.cardMasterID ) 
WHERE
	`srp_erp_pos_loyaltycard`.`customerID` = $customerAutoID 
	AND `srp_erp_pos_loyaltycard`.`isActive` = 1")->row_array();
        $loyaltycard = $this->db->query("SELECT cardMasterID,barcode FROM srp_erp_pos_loyaltycard WHERE companyID={$companyID} AND customerID=$customerAutoID AND isActive=1")->row_array();
        $points = $this->db->query("SELECT pointSetupID,currencyID,amount,loyaltyPoints,priceToPointsEarned,minimumPointstoRedeem FROM srp_erp_loyaltypointsetup WHERE companyID={$companyID} AND isActive=1")->row_array();
        $loyalty_setup = $this->db->query("select * from srp_erp_loyaltypointsetup where isActive = 1")->row_array();
        if (!empty($loyaltycard)) {
            if (!empty($points) && ($netTotalAmount >= $points['priceToPointsEarned'])) {
                if (!empty($loyaltypaymnt) && $loyaltypaymnt > 0) {
                    $data['totpts'] = ($loyalty_setup['purchaseRewardPoint'] / $loyalty_setup['poinforPuchaseAmount']) * ($netTotalAmount - $loyaltypaymnt);
                } else {
                    $data['totpts'] = ($loyalty_setup['purchaseRewardPoint'] / $loyalty_setup['poinforPuchaseAmount']) * $netTotalAmount;
                }
            } else if (($netTotalAmount < $points['priceToPointsEarned'])) {
                $data['totpts'] = 0;
            }
        }
        $data['loyaltyPoints'] = $points['loyaltyPoints'];
        $data['loyalty_setup'] = $loyalty_setup;

        echo json_encode($data);
    }

    function update_card_active()
    {
        echo json_encode($this->Pos_model->update_card_active());
    }

    function fetch_customer_detail_loylity()
    {
        $customerAutoID = $this->input->post('customerAutoID');
        $companyID = current_companyID();
        $data = $this->db->query("SELECT
	customerName,
	customerAutoID,
	pts.totpoints,
	customerTelephone,
	barcode,
	customerSystemCode,
	srp_erp_customermaster.customerCurrency AS customerCurrency 
FROM
	srp_erp_pos_loyaltycard
	LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_loyaltycard.customerID
	LEFT JOIN ( SELECT cardMasterID, sum( points ) AS totpoints FROM srp_erp_pos_loyaltytopup WHERE srp_erp_pos_loyaltytopup.companyID = $companyID GROUP BY cardMasterID ) pts ON pts.cardMasterID = srp_erp_pos_loyaltycard.cardMasterID 
WHERE
	srp_erp_pos_loyaltycard.companyID = $companyID 
	AND customerAutoID = $customerAutoID")->row_array();

        echo json_encode($data);
    }

    function fetch_customers_loyality()
    {
        $comapnyID = current_companyID();
        $this->datatables->select('srp_erp_pos_customermaster.posCustomerAutoID AS posCustomerAutoID,srp_erp_pos_customermaster.CustomerName AS CustomerName,srp_erp_pos_customermaster.customerTelephone AS customerTelephone,srp_erp_pos_customermaster.CustomerAddress1 AS CustomerAddress1', false)
            ->from('srp_erp_pos_customermaster')
            ->where('companyID', $comapnyID)
            ->where('customerTelephone IS NOT NULL');
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_pos_loyaltycard WHERE srp_erp_pos_loyaltycard.customerID = `srp_erp_pos_customermaster`.`posCustomerAutoID` AND `companyID` = ' . $comapnyID . ' AND customerType = 1)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="" type="checkbox" class="columnSelected check_customersall"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'posCustomerAutoID');
        echo $this->datatables->generate();
    }

    function save_customers_loyality_card()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Customer', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Pos_model->save_customers_loyality_card());
        }
    }

    function generateloyalitycard()
    {
        $this->form_validation->set_rules('telephoneno', 'Telephone Numner', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->generateloyalitycard());
        }
    }

    function fetch_customers_loyality_general()
    {
        $comapnyID = current_companyID();
        $this->datatables->select('srp_erp_customermaster.customerAutoID AS posCustomerAutoID,srp_erp_customermaster.CustomerName AS CustomerName,	srp_erp_customermaster.customerTelephone AS customerTelephone,srp_erp_customermaster.CustomerAddress1 AS CustomerAddress1 ', false)
            ->from('srp_erp_customermaster')
            ->where('companyID', $comapnyID)
            ->where('customerTelephone  <>\'\'');
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_pos_loyaltycard WHERE srp_erp_pos_loyaltycard.customerID = `srp_erp_customermaster`.`customerAutoID` AND `companyID` = ' . $comapnyID . ' AND customerType = 0)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="" type="checkbox" class="columnSelected check_customersall"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'posCustomerAutoID');
        echo $this->datatables->generate();
    }

    function save_customers_loyality_card_general()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Customer', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Pos_model->save_customers_loyality_card_general());
        }
    }

    function fetch_pos_customer_details_general()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        //$data = $this->db->query('SELECT mainCategory,mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match" , isSubitemExist FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND financeCategory != 3 AND companyCode = "' . $companyCode . '" AND isActive="1"')->result_array();
        $data = $this->db->query('SELECT
	customerAutoID as posCustomerAutoID,
	CustomerName,
	CustomerAddress1,
	customerTelephone,
	CONCAT( customerTelephone, "- ", IFNULL( CustomerName, \'\' ) ) AS cusdet,
	customerEmail,
	country.countryCode as customerCountryCode,
	customerCountry,
	IFNULL( loyalitycard.barcode, 0 ) AS loyalityno 
FROM
	srp_erp_customermaster
		LEFT JOIN (SELECT barcode,customerID FROM srp_erp_pos_loyaltycard where customerType = 0) loyalitycard ON loyalitycard.customerID = srp_erp_customermaster.customerAutoID 
	LEFT JOIN srp_erp_countrymaster country on country.CountryDes = srp_erp_customermaster.customerCountry
WHERE
	( customerTelephone LIKE  "' . $search_string . '" OR CustomerName LIKE  "' . $search_string . '") 
	AND srp_erp_customermaster.companyID = "' . $companyID . '"')->result_array();


        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["cusdet"], 'data' => $val['customerTelephone'], 'posCustomerAutoID' => $val["posCustomerAutoID"], 'CustomerName' => $val['CustomerName'], 'CustomerAddress1' => $val['CustomerAddress1'], 'customerTelephone' => $val['customerTelephone'], 'customerEmail' => $val['customerEmail'], 'customerCountry' => $val['customerCountry'], 'customerCountryCode' => $val['customerCountryCode'], 'loyalityno' => $val['loyalityno']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        echo json_encode($dataArr2);
    }

    function save_customer_posgen()
    {

        $this->form_validation->set_rules('customerNameTmp', 'customer Name', 'trim|required');
        $this->form_validation->set_rules('customerTelephoneTmp', 'Customer Telephone', 'trim|required');
        $this->form_validation->set_rules('customerEmailTmp', 'Email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_model->save_customer_loylity());
        }
    }

    function load_customer_dropdown()
    {
        $companyID = current_companyID();
        $skey = $this->input->post('skey', true);


        $query = $this->db->query("SELECT
                `srp_erp_customermaster`.`customerAutoID`,
                `srp_erp_customermaster`.`customerName`,
                `srp_erp_customermaster`.`customerSystemCode`,
                `srp_erp_customermaster`.`customerCountry`,
                `srp_erp_customermaster`.`companyCode`,
                `srp_erp_customermaster`.`customerTelephone` 
            FROM
                `srp_erp_customermaster`
                INNER JOIN `srp_erp_pos_invoice` ON `srp_erp_pos_invoice`.`customerID` = `srp_erp_customermaster`.`customerAutoID` 
            WHERE
                `srp_erp_customermaster`.`companyID` = '$companyID'
                AND srp_erp_customermaster.customerName LIKE '%$skey%' limit 5");

        $customer = $query->result_array();

        $data['customers'] = $customer;
        echo json_encode($data);
    }

    function load_items_dropdown()
    {
        $companyID = current_companyID();
        $skey = $this->input->post('skey', true);
        $selected = $this->input->post('selected', true);
        if (!empty($selected)) {
            foreach ($selected as $index => $key) {
                if (!is_numeric($key)) {
                    unset($selected[$index]);
                }
            }
        }
        $systemcode = $this->input->post('issystemcode');
        $Itemsystemcode_union = '';
        if ($systemcode == 1) {
            $Itemsystemcode_union .= " UNION (SELECT
	itemSystemCode,
	itemName,
	itemAutoID,
	seconeryItemCode,
	itemSystemCode
FROM
	srp_erp_itemmaster 
WHERE
	isActive = 1 
	AND companyID = 13
	AND itemSystemCode LIKE  '$skey%' limit 5)";
        }

        if (!empty($selected)) {
            $selected = implode(",", $selected);
        } else {
            $selected = "0";
        }

        $query = $this->db->query("( SELECT 
        itemSystemCode, 
        itemName, 
        itemAutoID, 
        seconeryItemCode ,
        itemSystemCode
FROM 
    srp_erp_itemmaster 
WHERE 
    isActive = 1 
    AND companyID = $companyID 
    AND seconeryItemCode LIKE '$skey%' LIMIT 5 )
UNION
        (SELECT
	itemSystemCode,
	itemName,
	itemAutoID,
	seconeryItemCode,
	itemSystemCode
FROM
	srp_erp_itemmaster 
WHERE
	isActive = 1 
	AND companyID = $companyID 
	AND itemName LIKE '$skey%' limit 5)
UNION
        (SELECT
	itemSystemCode,
	itemName,
	itemAutoID,
	seconeryItemCode,
	itemSystemCode
FROM
	srp_erp_itemmaster 
WHERE
	isActive = 1 
	AND companyID = $companyID 
	AND itemName LIKE '%$skey%' limit 5)
$Itemsystemcode_union	
UNION
(SELECT
	itemSystemCode,
	itemName,
	itemAutoID,
	seconeryItemCode ,
	itemSystemCode
FROM
	srp_erp_itemmaster 
WHERE
    companyID = $companyID 
	AND itemAutoID IN ( $selected ))");
        //        var_dump($this->db->last_query());exit;
        $data['items'] = $query->result();
        echo json_encode($data);
    }


    function load_invoicecode_dropdown()
    {
        $companyID = current_companyID();
        $skey = $this->input->post('skey', true);
        $selected = $this->input->post('selected', true);
        if (!empty($selected)) {
            foreach ($selected as $index => $key) {
                if (!is_numeric($key)) {
                    unset($selected[$index]);
                }
            }
        }
        if (!empty($selected)) {
            $selected = implode(",", $selected);
        } else {
            $selected = "0";
        }

        $query = $this->db->query("(SELECT
                                        `srp_erp_pos_invoice`.`invoiceID`,
                                        `srp_erp_pos_invoice`.`invoiceCode` 
                                    FROM
                                        `srp_erp_pos_invoice` 
                                    WHERE
                                        `srp_erp_pos_invoice`.`isVoid` = 0 
                                        AND `srp_erp_pos_invoice`.`companyID` = $companyID
                                        AND invoiceCode LIKE '%$skey%' limit 10)
                                    UNION
                                    (SELECT
                                        `srp_erp_pos_invoice`.`invoiceID`,
                                        `srp_erp_pos_invoice`.`invoiceCode` 
                                    FROM
                                        `srp_erp_pos_invoice` 
                                    WHERE
                                        `srp_erp_pos_invoice`.`isVoid` = 0 
                                        AND `srp_erp_pos_invoice`.`companyID` = $companyID
                                        AND invoiceID IN ($selected))");
        $data['invoicecd'] = $query->result();
        echo json_encode($data);
    }
    function fetch_linewiseTax_calculation_gpos()
    {
        $discountAmount = trim($this->input->post('discount') ?? '');
        $taxtype = trim($this->input->post('taxtype') ?? '');
        $quantityRequested = trim($this->input->post('quantityRequested') ?? '');
        $applicableAmnt = trim($this->input->post('applicableAmnt') ?? '');

        $amnt = fetch_line_wise_itemTaxcalculation_gpos($taxtype, $applicableAmnt, $discountAmount, 'GPOS', null);

        echo json_encode($amnt);
    }
    function fetch_tax_drop_itemwise()
    {
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        //$data = fetch_line_wise_itemTaxFormulaID($itemAutoID,'taxMasterAutoID','taxDescription',2);
        $data['tax_drop'] = fetch_line_wise_itemTaxFormulaID($itemAutoID, 'taxMasterAutoID', 'taxDescription', 1);
        $selected_itemTax =   array_column($data['tax_drop'], 'assignedItemTaxFormula');
        $data['selected_itemTax'] =   isset($selected_itemTax[0]) ? $selected_itemTax[0] : '';
        echo json_encode($data);
    }

    function sales_details_report_v2()
    {
        $company_id = current_companyID();
        $customers = explode(',', $this->input->post('customers'));
        $warehouses = explode(',', $this->input->post('warehouses'));
        $cashiers = explode(',', $this->input->post('cashiers'));
        $fromdate = trim(str_replace('/', '-', $this->input->post('fromdate')));
        $todate = trim(str_replace('/', '-', $this->input->post('todate')));

        if (isset($fromdate) && !empty($fromdate)) {
            $fromdate = date('Y-m-d H:i:s', strtotime($fromdate));
        } else {
            $fromdate = date('Y-m-d 00:00:00');
        }

        if (!empty($todate)) {
            $todate = date('Y-m-d H:i:s', strtotime($todate));
        } else {
            $todate = date('Y-m-d 23:59:59');
        }

        $docTotal = $this->Pos_model->sales_details_report_v2_total();

        $this->datatables->select("invoice.invoiceID as invoiceID,
                                        invoice.documentSystemCode as documentSystemCode,
                                        invoice.createdDateTime,
                                        invoice.invoiceCode as invoiceCode,
                                        invoice.wareHouseLocation,
                                        ROUND((invoice_det.subTotal), invoice.transactionCurrencyDecimalPlaces) as subTotal,
                                        ROUND(invoice.netTotal, invoice.transactionCurrencyDecimalPlaces) as netTotal,
                                        ROUND(invoice.paidAmount, invoice.transactionCurrencyDecimalPlaces) as paidAmount,
                                        ROUND(invoice.balanceAmount, invoice.transactionCurrencyDecimalPlaces) as balanceAmount,
                                        ROUND(invoice.discountAmount, invoice.transactionCurrencyDecimalPlaces) as discountAmount,
                                        ROUND(invoice.generalDiscountAmount, invoice.transactionCurrencyDecimalPlaces) as generalDiscountAmount,
                                        ROUND(invoice.promotiondiscountAmount, invoice.transactionCurrencyDecimalPlaces) as promotiondiscountAmount,
                                        IFNULL( srp_erp_customermaster.customerName, 'Cash' ) AS customernam,
                                        srp_erp_customermaster.customerTelephone,
                                        IFNULL( rtn.totalreturn, 0 ) AS totalreturn,
                                        invoice.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,IFNULL(ROUND(amount,invoice.transactionCurrencyDecimalPlaces),0) as amount,IFNULL(ROUND(Otheramount,invoice.transactionCurrencyDecimalPlaces),0) as Otheramount", false)
            ->from('srp_erp_pos_invoice AS invoice')
            ->join('srp_erp_customermaster', 'invoice.customerID = srp_erp_customermaster.customerAutoID', 'left')
            ->join("( SELECT SUM(netTotal) AS totalreturn, invoiceID FROM srp_erp_pos_salesreturn WHERE companyID = $company_id GROUP BY invoiceID ) rtn", 'invoice.invoiceID = rtn.invoiceID', 'left')
            ->join("( SELECT (sum(qty * price)) AS subTotal,invoiceID FROM srp_erp_pos_invoicedetail WHERE companyID= $company_id GROUP BY invoiceID ) invoice_det", 'invoice.invoiceID = invoice_det.invoiceID', 'left')
            ->join("(SELECT
                        IFNULL( sum(amount), 0 ) AS amount,
                        documentMasterAutoID 
                    FROM
                        srp_erp_taxledger tax_led
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = tax_led.taxMasterID 
                    WHERE
                        documentID = 'GPOS' 
                        AND taxCategory = 2 
                        AND tax_led.companyID = $company_id 
                    GROUP BY
                    documentMasterAutoID) taxledgerDetails", 'taxledgerDetails.documentMasterAutoID = invoice.invoiceID', 'left')
            ->join("(SELECT
                    IFNULL( sum(amount), 0 ) AS Otheramount,
                    documentMasterAutoID 
                FROM
                    srp_erp_taxledger tax_led
                    LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = tax_led.taxMasterID 
                WHERE
                    documentID = 'GPOS' 
                    AND taxCategory = 1 
                    AND tax_led.companyID = $company_id 
                GROUP BY
                documentMasterAutoID) taxledgerDetailsOther", 'taxledgerDetailsOther.documentMasterAutoID = invoice.invoiceID', 'left')
            ->add_column('discount', '$1', 'discount_column_salesdetails(discountAmount,generalDiscountAmount,promotiondiscountAmount)')
            ->add_column('totalreturncol', '$1', 'totalreturn_column_salesdetails(totalreturn,invoiceID)')
            ->add_column('viewbill', '$1', 'viewbillcolumn(invoiceID,documentSystemCode)')
            ->add_column('subTotalTot', '$1', $docTotal['subTotalTot'])
            ->add_column('otherTaxTot', '$1', $docTotal['OtherTotalTot'])
            ->add_column('netTotalTot', '$1', $docTotal['netTotalTot'])
            ->add_column('paidAmountTot', '$1', $docTotal['paidAmountTot'])
            ->add_column('balanceAmountTot', '$1', $docTotal['balanceAmountTot'])
            ->add_column('totalreturncolTot', '$1', $docTotal['totalreturncolTot'])
            ->add_column('discountTot', '$1', $docTotal['discountTotalAmount'])
            ->add_column('vatTotalTot', '$1', $docTotal['vatTotalTot'])
            ->where_in('invoice.createdUserID',  $cashiers)
            ->where_in('wareHouseAutoID', $warehouses);
            if($customers[0]!=''){
                $this->datatables->where_in('customerID', $customers);
            }            
            $this->datatables->where('invoice.createdDateTime >=', $fromdate)
            ->where('invoice.createdDateTime <=', $todate)
            ->where('invoice.isVoid', 0)            
            ->where('invoice.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    function refund_sales_details_report_v2()
    {
        $company_id = current_companyID();
        $customers = $this->input->post('customers');
        $warehouses = explode(',', $this->input->post('warehouses'));
        $cashiers = explode(',', $this->input->post('cashiers'));
        $fromdate = trim(str_replace('/', '-', $this->input->post('fromdate')));
        $todate = trim(str_replace('/', '-', $this->input->post('todate')));

        if (isset($fromdate) && !empty($fromdate)) {
            $fromdate = date('Y-m-d H:i:s', strtotime($fromdate));
        } else {
            $fromdate = date('Y-m-d 00:00:00');
        }

        if (!empty($todate)) {
            $todate = date('Y-m-d H:i:s', strtotime($todate));
        } else {
            $todate = date('Y-m-d 23:59:59');
        }

        $docTotal = $this->Pos_model->sales_refund_details_report_v2_total();

        $this->datatables->select("srp_erp_pos_salesreturn.salesReturnID as salesReturnID,
                                    srp_erp_pos_salesreturn.documentSystemCode as documentSystemCode,
                                    srp_erp_pos_salesreturn.createdDateTime,
                                    ROUND(srp_erp_pos_salesreturn.netTotal, srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces) as netTotal,
                                    ROUND((CASE
                                        WHEN srp_erp_pos_salesreturn.returnMode = 2 THEN (srp_erp_pos_salesreturn.netTotal)
                                        ELSE 0
                                    END), srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces) as refund,
                                    ROUND((CASE
                                        WHEN srp_erp_pos_salesreturn.returnMode = 1 THEN (srp_erp_pos_salesreturn.netTotal)
                                        ELSE 0
                                    END), srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces) as exchange,
                                    srp_erp_pos_salesreturn.returnMode,
                                    srp_erp_pos_salesreturn.wareHouseLocation,
                                    invoice.invoiceID", false)
            ->from('srp_erp_pos_salesreturn')
            ->join('srp_erp_pos_invoice AS invoice', 'invoice.invoiceID = srp_erp_pos_salesreturn.invoiceID', 'left')
            ->join('srp_erp_customermaster', 'invoice.customerID = srp_erp_customermaster.customerAutoID', 'left')
            ->join("( SELECT SUM(netTotal) AS totalreturn, invoiceID FROM srp_erp_pos_salesreturn WHERE companyID = $company_id GROUP BY invoiceID ) rtn", 'invoice.invoiceID = rtn.invoiceID', 'left')
            ->add_column('viewreturnbillcolumn', '$1', 'viewreturnbillcolumn(salesReturnID,documentSystemCode)')
            ->add_column('netTotalTot', '$1', $docTotal['netTotalTot'])
            ->add_column('refundTot', '$1', $docTotal['refundTot'])
            ->add_column('exchangeTot', '$1', $docTotal['exchangeTot'])
            ->where_in('srp_erp_pos_salesreturn.createdUserID',  $cashiers)
            ->where_in('srp_erp_pos_salesreturn.wareHouseAutoID', $warehouses);
            if($customers != ''){
                $customers = explode(',', $customers);
                $this->datatables->where_in('srp_erp_pos_salesreturn.customerID', $customers);
            }            
            $this->datatables->where('srp_erp_pos_salesreturn.createdDateTime >=', $fromdate)
            ->where('srp_erp_pos_salesreturn.createdDateTime <=', $todate)
            ->where('invoice.isVoid', 0)            
            ->where('invoice.companyID', $this->common_data['company_data']['company_id']);
        echo $this->datatables->generate();
    }

    function sales_details_report_v2_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Sales Detail Report');
        $this->load->database();
        $header = ['#', 'Date & Time', 'Bill ID', 'Outlet', 'Customer', 'Contact No', 'Gross Total', 'Total Discount','VAT', 'Other Tax','Net Total', 'Paid Amount', 'Balance', 'Return'];
        $details = $this->Pos_model->sales_details_report_v2_excel();

        $fromdate =  $this->input->post('filterFrom');
        $todate = $this->input->post('filterTo');

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->fromArray(['Date From : ' . $fromdate . ' To : ' . $todate], null, 'A3');

        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri',
                'align' => 'center',
            )
        );

        $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->fromArray(['Sales Detail Report'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->getStyle('A5:U4')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A5');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A7');

        // Create a new worksheet, after the default sheet
        $this->excel->createSheet();
        // Add some data to the second sheet, resembling some different data types
        $this->excel->setActiveSheetIndex(1);
        // Rename 2nd sheet
        $this->excel->getActiveSheet()->setTitle('Refund Details');

        $header2 = ['#', 'Date & Time', 'Document Code', 'Outlet', 'Refund Amount', 'Exchange Amount', 'Total'];
        $details2 = $this->Pos_model->refund_sales_details_report_v2_excel();
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->fromArray(['Date From : ' . $fromdate . ' To : ' . $todate], null, 'A3');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Refund Detail Report'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A5:U4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A5:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header2, null, 'A5');
        $this->excel->getActiveSheet()->fromArray($details2['records'], null, 'A7');

        $cashCollected = $this->Pos_model->total_cash_collected(1);
        $styleArray3 = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri',
                'align' => 'center',
            )
        );
        $this->excel->getActiveSheet()->getStyle('A' . ($details2['count'] + 9))->applyFromArray($styleArray3);
        $this->excel->getActiveSheet()->setCellValue(['A' , ($details2['count'] + 9)], 'Total Cash Collection (Cash-Refund): ' . $cashCollected);
        $this->excel->setActiveSheetIndex(0);
        
        $filename = 'Sales Detail Report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function total_cash_collected()
    {
        echo json_encode($this->Pos_model->total_cash_collected());
    }
    function get_item_master_image(){

        echo json_encode($this->Pos_model->get_item_master_image());

    }

    function item_reserved_qty(){
        echo json_encode($this->Pos_model->item_reserved_qty());
    }

    function item_warehouse_search(){
        echo json_encode($this->Pos_model->item_warehouse_search());
    }


    function make_the_adjusment_batch(){
        echo json_encode($this->Pos_model->make_the_adjusment_batch());
    }

    function make_the_adjusment(){
        echo json_encode($this->Pos_model->make_the_adjusment());
    }

    function make_the_adjusment_step2(){
        echo json_encode($this->Pos_model->make_the_adjusment_step2());
    }

    function make_the_adjusment_step3(){
        echo json_encode($this->Pos_model->make_the_adjusment_step3());
    }

    function get_exchange_selected_items(){
        echo json_encode($this->Pos_model->get_exchange_selected_items());
    }

    
}