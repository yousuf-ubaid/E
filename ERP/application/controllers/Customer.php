<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class customer extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Customer_model');
        $this->load->helper('Customer_helper');
        $this->load->library('s3');
    }



    
    function fetch_customer()
    {
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
        $deletedYN_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        $deleted = $this->input->post('deletedYN');

        if ($deleted == 1) {
            $deletedYN_filter = " AND deletedYN = " . $deleted;
        } else {
            $deletedYN_filter = " AND (deletedYN IS NULL OR deletedYN = " . $deleted . ")";
        }

        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_customermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_customermaster.companyID = " . $companyid . $customer_filter . $category_filter . $currency_filter . $deletedYN_filter . "";
        $this->datatables->select('srp_erp_customermaster.deletedYN as deletedYN, srp_erp_partycategories.categoryDescription as categoryDescription,customerAutoID,customerSystemCode,secondaryCode,customerName,customerAddress1,customerAddress2,customerCountry,customerTelephone,customerEmail,customerUrl,customerFax,isActive,customerCurrency,customerEmail,customerTelephone,customerCurrencyID,ROUND(cust.Amount, 2) as Amount_search,cust.Amount as Amount,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces,IdCardNumber,customerCreditPeriod ,customerCreditLimit,rebatePercentage,vatEligible,vatIdNo')
            ->where($where)
            ->from('srp_erp_customermaster')
            ->join('srp_erp_partycategories', 'srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID', 'left')
            ->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate) as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "CUS" AND subLedgerType=3 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_customermaster.customerAutoID', 'left');
        //$this->datatables->add_column('customer_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8 <br><b>Id Card No :</b> $9', 'customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone,IdCardNumber');
        $this->datatables->add_column('customer_detail', '$1', 'load_cusomer_master_detail(customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone,IdCardNumber,customerCreditPeriod,rebatePercentage,customerCreditLimit,vatEligible,partyCurrencyDecimalPlaces,vatIdNo)');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '$1', 'editcustomer(customerAutoID, deletedYN)');
        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(Amount,partyCurrencyDecimalPlaces),customerCurrency');
        //$this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',$1,\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'customerAutoID');
        echo $this->datatables->generate();
    }


    public function new_customer()
    {
        $this->form_validation->set_rules('customercode', 'Customer Secondary Code', 'trim|required');
        $this->form_validation->set_rules('customerName', 'Customer Name', 'trim|required');
        $this->form_validation->set_rules('receivableAccount', 'receivable Account', 'trim|required');
        $this->form_validation->set_rules('currency_code', 'customer Code', 'trim|required');
        $this->form_validation->set_rules('customercountry', 'customer Country', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Customer_model->save_customer());
        } 

    }

    function customer_detailed_report()
    {
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
        $deletedYN_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        $deleted = $this->input->post('deletedYN');

        if ($deleted == 1) {
            $deletedYN_filter = " AND deletedYN = " . $deleted;
        } else {
            $deletedYN_filter = " AND (deletedYN IS NULL OR deletedYN = " . $deleted . ")";
        }

        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_customermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_customermaster.companyID = " . $companyid . $customer_filter . $category_filter . $currency_filter . $deletedYN_filter . "";
        $this->datatables->select('srp_erp_customermaster.deletedYN as deletedYN, srp_erp_partycategories.categoryDescription as categoryDescription,customerAutoID,customerSystemCode,secondaryCode,customerName,customerAddress1,customerAddress2,customerCountry,customerTelephone,customerEmail,customerUrl,customerFax,isActive,customerCurrency,customerEmail,customerTelephone,customerCurrencyID,ROUND(cust.Amount, 2) as Amount_search,cust.Amount as Amount,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces,IdCardNumber,customerCreditPeriod ,customerCreditLimit,rebatePercentage,vatEligible,vatIdNo')
            ->where($where)
            ->from('srp_erp_customermaster')
            ->join('srp_erp_partycategories', 'srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID', 'left')
            ->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate) as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "CUS" AND subLedgerType=3 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_customermaster.customerAutoID', 'left');
    
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '$1', 'editcustomer(customerAutoID, deletedYN)');
        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(Amount,partyCurrencyDecimalPlaces),customerCurrency');
        echo $this->datatables->generate();
    }

    function fetch_sales_person()
    {
        $this->datatables->select('salesPersonID as salesPersonID,wareHouseCode,SalesPersonName,wareHouseCode,wareHouseLocation,wareHouseDescription, isActive,salesPersonCurrency,SalesPersonCode,SecondaryCode,SalesPersonEmail,contactNumber')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->from('srp_erp_salespersonmaster');
        $this->datatables->add_column('SalesPerson_detail', '<b>Name : </b>$1 <b>Location : </b> $2 <b> Contact Number : </b> $3 <b> Email : </b> $4 ', 'SalesPersonName,wareHouseLocation, contactNumber,SalesPersonEmail');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Sales person\',\'REP\');"><span class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;<a onclick="fetchPage(\'system/sales/erp_sales_person_new\',\'$1\',\'Sales person\')"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a onclick="delete_sales_person($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'salesPersonID');
        echo $this->datatables->generate();
    }





    function save_customer()
    {
        if (!$this->input->post('customerAutoID')) {
            $this->form_validation->set_rules('customerCurrency', 'customer Currency', 'trim|required');
        }
        $this->form_validation->set_rules('customercode', 'customer Code', 'trim|required');
        $this->form_validation->set_rules('customerName', 'customer Name', 'trim|required');
        $this->form_validation->set_rules('customercountry', 'customer country', 'trim|required');
        //$this->form_validation->set_rules('IdCardNumber', 'ID card number', 'trim|required');
        /*        $this->form_validation->set_rules('customerTelephone', 'customer Telephone', 'trim|required');
                $this->form_validation->set_rules('customerEmail', 'customer Email', 'trim|required');
                $this->form_validation->set_rules('customerAddress1', 'Address 1', 'trim|required');
                $this->form_validation->set_rules('customerAddress2', 'Address 2', 'trim|required');
                $this->form_validation->set_rules('customerCreditLimit', 'Credit Limit', 'trim|required');
                $this->form_validation->set_rules('customerCreditPeriod', 'Credit Period', 'trim|required|max_length[3]');*/
        $this->form_validation->set_rules('receivableAccount', 'Receivable Account', 'trim|required');
        if(trim($this->input->post('rebatePercentage') ?? '') > 0) {
            $this->form_validation->set_rules('rebateGL', 'Rebate GL', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Customer_model->save_customer());
        }
    }

    function save_customer_buyback()
    {
        if (!$this->input->post('customerAutoID')) {
            $this->form_validation->set_rules('customerCurrency', 'customer Currency', 'trim|required');
        }
        $this->form_validation->set_rules('customercode', 'customer Code', 'trim|required');
        $this->form_validation->set_rules('customerName', 'customer Name', 'trim|required');
        $this->form_validation->set_rules('customercountry', 'customer country', 'trim|required');
        $this->form_validation->set_rules('receivableAccount', 'Receivable Account', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Customer_model->save_customer_buyback());
        }
    }

    function save_customer_otherlang()
    {
        if (!$this->input->post('customerAutoID')) {
            $this->form_validation->set_rules('customerCurrency', 'customer Currency', 'trim|required');
        }
        $this->form_validation->set_rules('customercode', 'customer Code', 'trim|required');
        $this->form_validation->set_rules('customerName', 'customer Name', 'trim|required');
        $this->form_validation->set_rules('customercountry', 'customer country', 'trim|required');
        /*        $this->form_validation->set_rules('customerTelephone', 'customer Telephone', 'trim|required');
                $this->form_validation->set_rules('customerEmail', 'customer Email', 'trim|required');
                $this->form_validation->set_rules('customerAddress1', 'Address 1', 'trim|required');
                $this->form_validation->set_rules('customerAddress2', 'Address 2', 'trim|required');
                $this->form_validation->set_rules('customerCreditLimit', 'Credit Limit', 'trim|required');
                $this->form_validation->set_rules('customerCreditPeriod', 'Credit Period', 'trim|required|max_length[3]');*/
        $this->form_validation->set_rules('receivableAccount', 'Receivable Account', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Customer_model->save_customer_otherlang());
        }
    }

    function fetch_sales_person_details()
    {
        echo json_encode($this->Customer_model->fetch_sales_person_details());
    }

    function delete_sales_target()
    {
        echo json_encode($this->Customer_model->delete_sales_target());
    }

    function laad_sale_target()
    {
        echo json_encode($this->Customer_model->laad_sale_target());
    }

    // function edit_customer()
    // {
    //     if($this->input->post('id') !=""){
    //         echo json_encode($this->Customer_model->get_customer());
    //     }
    //     else{
    //         echo json_encode(FALSE);
    //     }
    // }

    function load_customer_header()
    {
        echo json_encode($this->Customer_model->load_customer_header());
    }

    function laad_sale_person_header()
    {
        echo json_encode($this->Customer_model->laad_sale_person_header());
    }

    function delete_customer()
    {
        echo json_encode($this->Customer_model->delete_customer());
    }

    function delete_sales_person()
    {
        echo json_encode($this->Customer_model->delete_sales_person());
    }

    function fetch_customer_category()
    {
        $this->datatables->select('partyCategoryID,partyType,categoryDescription')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->where('partyType', 1)
            ->from('srp_erp_partycategories');
        $this->datatables->add_column('edit', '$1', 'editcustomercategory(partyCategoryID)');
        echo $this->datatables->generate();
    }

    function saveCategory()
    {


        if (empty($this->input->post('categoryDescription'))) {
            echo json_encode(['e', 'Enter Category']);
        } else {
            echo json_encode($this->Customer_model->saveCategory());
        }
    }

    function getCategory()
    {
        echo json_encode($this->Customer_model->getCategory());
    }

    function fetch_employee_detail()
    {
        echo json_encode($this->Customer_model->fetch_employee_detail());
    }

    function delete_category()
    {
        echo json_encode($this->Customer_model->delete_category());
    }

    function load_sale_conformation()
    {
        $salesPersonID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('salesPersonID') ?? '');
        $data['extra'] = $this->Customer_model->fetch_template_data($salesPersonID);


        $data['salespersonimage'] =  $this->s3->createPresignedRequest($data['extra']['head']['salesPersonImage'], '1 hour');
        $data['noimage'] = $this->s3->createPresignedRequest('images/default.gif', '1 hour');

        $html = $this->load->view('system/sales/erp_sales_person_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);
        }
    }

    function img_uplode()
    {
        $this->form_validation->set_rules('salesPersonID', 'Sales Person ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Customer_model->img_uplode());
        }
    }

    //get sales target end amount
    function load_sales_target_endamount()
    {
        echo json_encode($this->Customer_model->load_sales_target_endamount());
    }

    function fetch_customer_percentage()
    {
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_customermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_customermaster.companyID = " . $companyid . $customer_filter . $category_filter . $currency_filter . "";
        $this->datatables->select('srp_erp_partycategories.categoryDescription as categoryDescription,customerAutoID,customerSystemCode,secondaryCode,customerName,customerAddress1,customerAddress2,customerCountry,customerTelephone,customerEmail,customerUrl,customerFax,isActive,customerCurrency,customerEmail,customerTelephone,customerCurrencyID,cust.Amount as Amount,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces,finCompanyPercentage,pvtCompanyPercentage,capAmount')
            ->where($where)
            ->from('srp_erp_customermaster')
            ->join('srp_erp_partycategories', 'srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID', 'left')
            ->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate) as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "CUS" AND subLedgerType=3 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_customermaster.customerAutoID', 'left');
        $this->datatables->add_column('customer_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8', 'customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone');
        $this->datatables->add_column('DT_RowId', 'common_$1', 'customerAutoID');

        $this->datatables->add_column('capAmount', '<input style="width: 50%" type="text" class="form-control cap number"
                                   value="$1"
                                   name="capAmount[]" onkeypress="return validateFloatKeyPress(this,event)">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'capAmount');

        $this->datatables->add_column('fc', '<input style="width: 50%" type="text" class="form-control fc number"
                                   value="$1"
                                   name="finCompanyPercentage[]" onkeyup="validatePercentage(this,\'fc\')" onkeypress="return validateFloatKeyPress(this,event)">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'finCompanyPercentage');

        $this->datatables->add_column('pc', '<input style="width: 50%" type="text" class="form-control pc number"
                                   value="$2"
                                   name="pvtCompanyPercentage[]" onkeyup="validatePercentage(this,\'pc\')" onkeypress="return validateFloatKeyPress(this,event,5)">
                                   <input type="hidden" name="customerAutoID[]" value="$1">
                    <span class="applytoAll">
                        <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"><i
                                class="fa fa-arrow-circle-down arrowDown"></i></button></span>', 'customerAutoID,pvtCompanyPercentage');

        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(Amount,partyCurrencyDecimalPlaces),customerCurrency');
        //$this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',$1,\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'customerAutoID');
        echo $this->datatables->generate();
    }

    function save_customer_percentage()
    {
        echo json_encode($this->Customer_model->save_customer_percentage());
    }

    function fetch_customer_othherlang()
    {
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
        $deletedYN_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        $deleted = $this->input->post('deletedYN');
        if ($deleted == 1) {
            $deletedYN_filter = " AND deletedYN = " . $deleted;
        } else {
            $deletedYN_filter = " AND (deletedYN IS NULL OR deletedYN = " . $deleted . ")";
        }
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_customermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_customermaster.companyID = " . $companyid . $customer_filter . $category_filter . $currency_filter . $deletedYN_filter . "";
        $this->datatables->select('srp_erp_customermaster.deletedYN as deletedYN, srp_erp_partycategories.categoryDescription as categoryDescription,customerAutoID,customerSystemCode,secondaryCode,customerName,customerAddress1,customerAddress2,customerCountry,customerTelephone,customerEmail,customerUrl,customerFax,isActive,customerCurrency,customerEmail,customerTelephone,customerCurrencyID,ROUND(cust.Amount,2) as Amount_search,cust.Amount as Amount,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces,IdCardNumber,customerCreditPeriod ,customerCreditLimit,rebatePercentage,vatEligible ,vatIdNo')
            ->where($where)
            ->from('srp_erp_customermaster')
            ->join('srp_erp_partycategories', 'srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID', 'left')
            ->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate) as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "CUS" AND subLedgerType=3 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_customermaster.customerAutoID', 'left');
        //$this->datatables->add_column('customer_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8 <br><b>Id Card No :</b> $9', 'customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone,IdCardNumber');
        $this->datatables->add_column('customer_detail', '$1', 'load_cusomer_master_detail(customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone,IdCardNumber,customerCreditPeriod,rebatePercentage,customerCreditLimit,vatEligible,partyCurrencyDecimalPlaces,vatIdNo)');
        
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '$1', 'editcustomerotherlang(customerAutoID, deletedYN)');
        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(Amount,partyCurrencyDecimalPlaces),customerCurrency');
        //$this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',$1,\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'customerAutoID');
        echo $this->datatables->generate();
    }

    function fetch_customer_buyback_item() // created to add custer wise price for items
    {
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
        $deletedYN_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
        $deleted = $this->input->post('deletedYN');
        if ($deleted == 1) {
            $deletedYN_filter = " AND deletedYN = " . $deleted;
        } else {
            $deletedYN_filter = " AND (deletedYN IS NULL OR deletedYN = " . $deleted . ")";
        }
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND srp_erp_customermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join("' , '", $currency) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_customermaster.companyID = " . $companyid . $customer_filter . $category_filter . $currency_filter . $deletedYN_filter . "";
        $this->datatables->select('srp_erp_customermaster.deletedYN as deletedYN, srp_erp_partycategories.categoryDescription as categoryDescription,customerAutoID,customerSystemCode,secondaryCode,customerName,customerAddress1,customerAddress2,customerCountry,customerTelephone,customerEmail,customerUrl,customerFax,isActive,customerCurrency,customerEmail,customerTelephone,customerCurrencyID,cust.Amount as Amount,ROUND(cust.Amount, 2) as Amount_search,cust.partyCurrencyDecimalPlaces as partyCurrencyDecimalPlaces,IdCardNumber,customerCreditPeriod ,customerCreditLimit,rebatePercentage,vatEligible,vatIdNo ')
            ->where($where)
            ->from('srp_erp_customermaster')
            ->join('srp_erp_partycategories', 'srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID', 'left')
            ->join('(SELECT sum(srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate) as Amount,partyAutoID,partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE partyType = "CUS" AND subLedgerType=3 GROUP BY partyAutoID) cust', 'cust.partyAutoID = srp_erp_customermaster.customerAutoID', 'left');
        //$this->datatables->add_column('customer_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8', 'customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone');
        $this->datatables->add_column('customer_detail', '$1', 'load_cusomer_master_detail(customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone,IdCardNumber,customerCreditPeriod,rebatePercentage,customerCreditLimit,vatEligible,partyCurrencyDecimalPlaces,vatIdNo)');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '$1', 'editcustomerBuyback(customerAutoID, deletedYN)');
        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(Amount,partyCurrencyDecimalPlaces),customerCurrency');
        //$this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="attachment_modal($1,\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',$1,\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>', 'customerAutoID');
        echo $this->datatables->generate();
    }

    function Fetch_ItemDetail()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $customerAutoID = $this->input->post('customerAutoID');
        $this->form_validation->set_rules('customerAutoID', 'Sales Person ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $convertFormat = convert_date_format_sql();

            $this->db->select('im.itemAutoID, cip.customerPriceID, im.itemSystemCode, cip.isActive, im.seconeryItemCode,DATE_FORMAT(applicableDateFrom,\'' . $convertFormat . '\') AS applicableDateFrom,DATE_FORMAT(applicableDateTo,\'' . $convertFormat . '\') AS applicableDateTo, isModificationAllowed, im.itemDescription,im.companyLocalCurrency, im.companyLocalSellingPrice as DefaultPrice, cip.salesPrice as salesPrice');
            $this->db->from('srp_erp_itemmaster im');
            $this->db->join('srp_erp_customeritemprices cip', 'im.itemAutoID = cip.itemAutoID AND cip.customerAutoID = ' . $customerAutoID . '', 'LEFT');
            $this->db->where("im.companyID", $companyid);
            //   if($this->input->post('mainCategoryID')) {
            $this->db->where("im.mainCategoryID", $this->input->post('mainCategoryID'));
            //   }
            $this->db->where("im.subcategoryID", $this->input->post('subcategoryID'));
            //  }
            //   if($this->input->post('subSubCategoryID')) {
            $this->db->where("im.subSubCategoryID", $this->input->post('subSubCategoryID'));
            //   }
            $this->db->order_by("itemAutoID DESC");
            $data['itemDetails'] = $this->db->get()->result_array();
            $data['View'] = 0;
            $data['cpsAutoID'] = $this->input->post('cpsAutoID');
            //  var_dump($data);
            $this->load->view('system/customer/erp_customer_sales_price_addView', $data);
        }
    }

    function fetch_customer_SalesItemList()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('cps.cpsAutoID as cpsAutoID,cip.customerPriceID as customerPriceID , im.itemSystemCode, im.itemDescription, im.companyLocalCurrencyDecimalPlaces as decimalPlaces, im.companyLocalSellingPrice as SellingPrice, cip.salesPrice as salesPrice, cip.isModificationAllowed, DATE_FORMAT(applicableDateFrom,\'' . $convertFormat . '\') AS applicableDateFrom, DATE_FORMAT(applicableDateTo,\'' . $convertFormat . '\') AS applicableDateTo');
        $this->datatables->from('srp_erp_customeritempricesetup cps');
        $this->datatables->join('srp_erp_customeritemprices cip', 'cps.cpsAutoID = cip.cpsAutoID');
        $this->datatables->join('srp_erp_itemmaster im', 'im.itemAutoID = cip.itemAutoID');
        $this->datatables->where('cps.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('cps.cpsAutoID', $this->input->post('cpsAutoID') );
        $this->datatables->edit_column('SellingPrice', '<div class="pull-right"> $1 </div>', 'format_number(SellingPrice,decimalPlaces)');
        $this->datatables->edit_column('salesPrice', '<div class="pull-right"> $1 </div>', 'format_number(salesPrice,decimalPlaces)');
        $this->datatables->edit_column('action', '<div class="pull-right"> $1 </div>', 'customer_SalesItemList_action(customerPriceID)');
        echo $this->datatables->generate();
    }

    function Save_Customer_ItemPrice()
    {
        $itemAutoIDs = $this->input->post('itemID');
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $this->form_validation->set_rules("itemID[{$key}]", 'Item ID', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Customer_model->Save_Customer_ItemPrice());
        }
    }

    function fetch_customerPriceSetup()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('cpsAutoID,documentSystemCode,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate, narration, confirmedYN, approvedYN, CONCAT(customerSystemCode,\'  -  \',customerName) as customerSystemCode')
            ->from('srp_erp_customeritempricesetup');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customeritempricesetup.customerAutoID', 'LEFT');
        $this->datatables->where('srp_erp_customeritempricesetup.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('confirmed', '$1', 'confirm(confirmedYN)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN, "CPS", cpsAutoID)');
        $this->datatables->add_column('edit', '$1', 'edit_customerPriceSetup(cpsAutoID,confirmedYN,approvedYN)');
        echo $this->datatables->generate();
    }

    function fetch_customerPriceSetup_new()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('cpsAutoID,documentSystemCode,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate, narration, confirmedYN, approvedYN')
            ->from('srp_erp_customeritempricesetup');
        $this->datatables->where('srp_erp_customeritempricesetup.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('confirmed', '$1', 'confirm(confirmedYN)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN, "CPS", cpsAutoID)');
        $this->datatables->add_column('edit', '$1', 'edit_customerPriceSetup_new(cpsAutoID,confirmedYN,approvedYN)');
        echo $this->datatables->generate();
    }

    function fetch_customerWisePrice()
    {
        $this->datatables->select('srp_erp_customermaster.customerAutoID as cusAutoID,srp_erp_customeritempricesetup.customerAutoID, CONCAT(customerSystemCode,\'  -  \',customerName) as customerSystemCode')
            ->from('srp_erp_customeritempricesetup');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customeritempricesetup.customerAutoID', 'LEFT');
        $this->datatables->where('srp_erp_customeritempricesetup.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->group_by('srp_erp_customeritempricesetup.customerAutoID');
        $this->datatables->add_column('edit', '$1', 'edit_customerWisePriceList(cusAutoID)');
        echo $this->datatables->generate();
    }

    function fetch_customerWisePrice_new()
    {
        $this->datatables->select('srp_erp_customermaster.customerAutoID as cusAutoID,srp_erp_customeritemprices.customerAutoID, CONCAT(customerSystemCode,\'  -  \',customerName) as customerSystemCode')
            ->from('srp_erp_customeritemprices');
        $this->datatables->join('srp_erp_customeritempricesetup', 'srp_erp_customeritempricesetup.cpsAutoID = srp_erp_customeritemprices.cpsAutoID', 'LEFT');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_customeritemprices.customerAutoID', 'LEFT');
        $this->datatables->where('srp_erp_customeritemprices.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('srp_erp_customeritempricesetup.approvedYN', 1);
        $this->datatables->group_by('srp_erp_customeritemprices.customerAutoID');
        $this->datatables->add_column('edit', '$1', 'edit_customerWisePriceList(cusAutoID)');
        echo $this->datatables->generate();
    }

    function save_customerPriceSetup_header()
    {

        $this->form_validation->set_rules("documentDate", 'Document Date', 'required');
        $this->form_validation->set_rules("customerAutoID", 'Customer', 'required');
        $this->form_validation->set_rules("narration", 'Narration', 'required');


        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Customer_model->save_customerPriceSetup_header());
        }
    }

    function save_customerPriceSetup_header_new()
    {
        $this->form_validation->set_rules("documentDate", 'Document Date', 'required');
        $this->form_validation->set_rules("customerAutoID[]", 'Customer', 'required');
        $this->form_validation->set_rules("narration", 'Narration', 'required');
        $this->form_validation->set_rules("currency", 'Currency', 'required');
        $this->form_validation->set_rules("itemTo[]", 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            $items = $this->input->post('itemTo');
            $applicableDateFrom = $this->input->post('applicableDateFrom');
            $applicableDateTo = $this->input->post('applicableDateTo');
            if(!empty($applicableDateTo) && empty($applicableDateFrom)) {
                echo json_encode(array('e', 'Applicable Date From is Required'));
            } else {

                    echo json_encode($this->Customer_model->save_customerPriceSetup_header_new());

            }
        }
    }

    function load_customerSalesPrice_header()
    {
        echo json_encode($this->Customer_model->load_customerSalesPrice_header());
    }

    function Fetch_ItemSalesPriceDetails()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $customerAutoID = $this->input->post('customerAutoID');
        $this->form_validation->set_rules('customerAutoID', 'Sales Person ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $this->db->select('cip.itemAutoID, cip.customerPriceID, im.itemSystemCode, cip.applicableDateFrom, cip.applicableDateTo, cip.isActive, cip.isModificationAllowed, im.seconeryItemCode, im.itemDescription,im.companyLocalCurrency, im.companyLocalSellingPrice as DefaultPrice, cip.salesPrice as salesPrice');
            $this->db->from('srp_erp_customeritemprices cip');
            $this->db->join('srp_erp_itemmaster im', 'im.itemAutoID = cip.itemAutoID', 'LEFT');
            $this->datatables->join('srp_erp_customeritempricesetup', 'srp_erp_customeritempricesetup.cpsAutoID = cip.cpsAutoID', 'LEFT');
            $this->datatables->where('srp_erp_customeritempricesetup.approvedYN', 1);
            $this->db->where("cip.companyID", $companyid);
            $this->db->where("cip.isActive", 1);
            $this->db->where("cip.customerAutoID", $customerAutoID);
            $this->db->order_by("customerPriceID ASC");
            $data['itemDetails'] = $this->db->get()->result_array();
            $data['View'] = $this->input->post('view');
            $data['type'] = 'html';
            $customerAutoID = $this->input->post('customerAutoID');
            $data['customerdetail'] = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName FROM srp_erp_customermaster WHERE companyID = $companyid AND customerAutoID = {$customerAutoID} ")->row_array();

            $this->load->view('system/customer/load_erp_customer_itemPriceEdit', $data);
        }
    }

    function load_Customer_PriceConfirmation()
    {
        $data['type'] = $this->input->post('html');
        $cpsAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('cpsAutoID') ?? '');
        $data['extra'] = $this->Customer_model->load_Customer_PriceConfirmation($cpsAutoID);
        $html = $this->load->view('system/customer/customer_salesPrice_print', $data, TRUE);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function load_Customer_PriceConfirmation_new()
    {
        $data['type'] = $this->input->post('html');
        $cpsAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('cpsAutoID') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $data['header'] = $this->db->query("SELECT srp_erp_customeritemprices.itemAutoID, srp_erp_itemmaster.itemSystemCode, seconeryItemCode, itemDescription FROM `srp_erp_customeritemprices` 
	                    LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_customeritemprices.itemAutoID
                        WHERE srp_erp_customeritemprices.companyID = {$companyID} AND cpsAutoID = {$cpsAutoID} 
                        GROUP BY srp_erp_customeritemprices.itemAutoID")->result_array();

        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("select cps.cpsAutoID,  CONCAT(cm.customerSystemCode, ' | ',cm.customerName) AS customer, DATE_FORMAT(cps.documentDate,'$convertFormat') AS documentDate, cps.documentSystemCode, cps.narration, cps.confirmedYN, cps.confirmedByEmpID, cps.confirmedByName, cps.confirmedDate, cm.customerCurrencyDecimalPlaces, cps.approvedYN, cps.approvedDate, cps.approvedbyEmpName FROM srp_erp_customeritempricesetup cps LEFT JOIN srp_erp_customermaster cm ON cm.customerAutoID = cps.customerAutoID WHERE cpsAutoID = $cpsAutoID ")->row_array();

        $data['details'] = $this->Customer_model->fetch_CustomerPrice_details($cpsAutoID);

        $html = $this->load->view('system/customer/customer_salesPrice_print_new', $data, TRUE);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function customer_SalesPrice_confirmation()
    {
        echo json_encode($this->Customer_model->customer_SalesPrice_confirmation());
    }

    function delete_customerSalesPrice_document()
    {
        echo json_encode($this->Customer_model->delete_customerSalesPrice_document());
    }

    function referback_Customer_priceSetup()
    {
        $cpsAutoID = trim($this->input->post('cpsAutoID') ?? '');
        $this->load->library('Approvals');
        $status = $this->approvals->approve_delete($cpsAutoID, 'CPS');
        if ($status != 1) {
            echo json_encode(array('e', ' Error in refer back.', $status));
        } else {
            $dataUpdate = array(
                'confirmedYN' => 0,
                'confirmedByEmpID' => '',
                'confirmedByName' => '',
                'confirmedDate' => '',
            );

            $this->db->where('cpsAutoID', $cpsAutoID);
            $this->db->update('srp_erp_customeritempricesetup', $dataUpdate);

            echo json_encode(array('s', ' Referred Back Successfully.', $status));
          //  echo json_encode(array('s', 'Customer Price  Referred Back Successfully.'));
        }
    }

    function customer_PriceSetup_approval()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN= $this->input->post('approvedYN');
        if($approvedYN == 0)
        {
            $this->datatables->select("documentApprovedID,customerSystemCode,approvedYN,approvalLevelID, cpsAutoID,confirmedYN,documentSystemCode,documentDate,documentID,customerAutoID, narration");
            $this->datatables->from("(SELECT documentApprovedID,cps.approvedYN,approvalLevelID, cps.documentID, confirmedYN, cps.cpsAutoID, cps.customerAutoID, cps.documentSystemCode, cps.narration, cps.documentDate, CM.customerSystemCode FROM srp_erp_customeritempricesetup cps LEFT JOIN srp_erp_customermaster CM ON CM.customerAutoID = cps.customerAutoID LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = cps.cpsAutoID AND approvalLevelID = currentLevelNo LEFT JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = cps.currentLevelNo WHERE srp_erp_documentapproved.documentID = 'CPS' AND srp_erp_approvalusers.documentID = 'CPS' AND employeeID = '{$this->common_data['current_userID']}' AND cps.approvedYN={$this->input->post('approvedYN')} AND cps.confirmedYN = 1 AND cps.companyID={$companyID} ORDER BY cpsAutoID DESC )t");
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "CPS", cpsAutoID)');
            $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
            $this->datatables->add_column('edit', '$1',
                'customer_PriceSetup_approval_action(cpsAutoID,approvalLevelID,approvedYN,documentApprovedID,documentID)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select("documentApprovedID,customerSystemCode,approvedYN,approvalLevelID, cpsAutoID,confirmedYN,documentSystemCode,documentDate,documentID,customerAutoID, narration");
            $this->datatables->from("(SELECT documentApprovedID,cps.approvedYN,approvalLevelID, cps.documentID, confirmedYN, cps.cpsAutoID, cps.customerAutoID, cps.documentSystemCode, cps.narration, cps.documentDate, CM.customerSystemCode FROM srp_erp_customeritempricesetup cps LEFT JOIN srp_erp_customermaster CM ON CM.customerAutoID = cps.customerAutoID LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = cps.cpsAutoID  WHERE srp_erp_documentapproved.documentID = 'CPS' AND cps.confirmedYN = 1 AND cps.approvedYN = 1 AND srp_erp_documentapproved.approvedEmpID = '{$this->common_data['current_userID']}'  AND cps.companyID={$companyID} GROUP BY
	cps.cpsAutoID,srp_erp_documentapproved.approvalLevelID ORDER BY cpsAutoID DESC )t");
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "CPS", cpsAutoID)');
            $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
            $this->datatables->add_column('edit', '$1',
                'customer_PriceSetup_approval_action(cpsAutoID,approvalLevelID,approvedYN,documentApprovedID,documentID)');
            echo $this->datatables->generate();
        }
    }

    function customer_PriceSetup_approval_new()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN= $this->input->post('approvedYN');
        if($approvedYN == 0)
        {
            $this->datatables->select("documentApprovedID,customerSystemCode,approvedYN,approvalLevelID, cpsAutoID,confirmedYN,documentSystemCode,documentDate,documentID,customerAutoID, narration");
            $this->datatables->from("(SELECT documentApprovedID,cps.approvedYN,approvalLevelID, cps.documentID, confirmedYN, cps.cpsAutoID, cps.customerAutoID, cps.documentSystemCode, cps.narration, cps.documentDate, CM.customerSystemCode FROM srp_erp_customeritempricesetup cps LEFT JOIN srp_erp_customermaster CM ON CM.customerAutoID = cps.customerAutoID LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = cps.cpsAutoID AND approvalLevelID = currentLevelNo LEFT JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = cps.currentLevelNo WHERE srp_erp_documentapproved.documentID = 'CPS' AND srp_erp_approvalusers.documentID = 'CPS' AND employeeID = '{$this->common_data['current_userID']}' AND cps.approvedYN={$this->input->post('approvedYN')} AND cps.confirmedYN = 1 AND cps.companyID={$companyID} ORDER BY cpsAutoID DESC )t");
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "CPS", cpsAutoID)');
            $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
            $this->datatables->add_column('edit', '$1',
                'customer_PriceSetup_approval_edit(cpsAutoID,approvalLevelID,approvedYN,documentApprovedID,documentID)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select("documentApprovedID,customerSystemCode,approvedYN,approvalLevelID, cpsAutoID,confirmedYN,documentSystemCode,documentDate,documentID,customerAutoID, narration");
            $this->datatables->from("(SELECT documentApprovedID,cps.approvedYN,approvalLevelID, cps.documentID, confirmedYN, cps.cpsAutoID, cps.customerAutoID, cps.documentSystemCode, cps.narration, cps.documentDate, CM.customerSystemCode FROM srp_erp_customeritempricesetup cps LEFT JOIN srp_erp_customermaster CM ON CM.customerAutoID = cps.customerAutoID LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = cps.cpsAutoID  WHERE srp_erp_documentapproved.documentID = 'CPS' AND cps.confirmedYN = 1 AND cps.approvedYN = 1 AND srp_erp_documentapproved.approvedEmpID = '{$this->common_data['current_userID']}'  AND cps.companyID={$companyID} GROUP BY
	cps.cpsAutoID,srp_erp_documentapproved.approvalLevelID ORDER BY cpsAutoID DESC )t");
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "CPS", cpsAutoID)');
            $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
            $this->datatables->add_column('edit', '$1',
                'customer_PriceSetup_approval_edit(cpsAutoID,approvalLevelID,approvedYN,documentApprovedID,documentID)');
            echo $this->datatables->generate();
        }


    }

    function save_CustomerPriceSetup_approval()
    {
        $system_code = trim($this->input->post('cpsAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'CPS', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('cpsAutoID');
                $this->db->where('cpsAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_customeritempricesetup');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('cpsAutoID', 'Customer Price Setup ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Customer_model->save_CustomerPriceSetup_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('cpsAutoID');
            $this->db->where('cpsAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_customeritempricesetup');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'CPS', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Dispatch Note Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('cpsAutoID', 'Customer Price Setup ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Customer_model->save_CustomerPriceSetup_approval());
                    }
                }
            }
        }
    }

    function save_CustomerPriceSetup_approval_new()
    {
        $system_code = trim($this->input->post('cpsAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'CPS', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('cpsAutoID');
                $this->db->where('cpsAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_customeritempricesetup');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('cpsAutoID', 'Customer Price Setup ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Customer_model->save_CustomerPriceSetup_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('cpsAutoID');
            $this->db->where('cpsAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_customeritempricesetup');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'CPS', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Dispatch Note Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('cpsAutoID', 'Customer Price Setup ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Customer_model->save_CustomerPriceSetup_approval());
                    }
                }
            }
        }
    }

    function deactivate_CustomerWisePrice()
    {
        $this->form_validation->set_rules("customerPriceID", 'Customer Price ID', 'required');
        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Customer_model->deactivate_CustomerWisePrice());
        }
    }

    function modifyCustomerPrice()
    {
        $this->form_validation->set_rules("customerPriceID", 'Customer Price ID', 'required');
        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Customer_model->modifyCustomerPrice());
        }
    }

    function delete_Customer_itemprice()
    {
        echo json_encode($this->Customer_model->delete_Customer_itemprice());
    }
    function delete_Customer_itemprice_all()
    {
        echo json_encode($this->Customer_model->delete_Customer_itemprice_all());
    }

    function load_CustomerPrice_detailsView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $cpsAutoID = trim($this->input->post('cpsAutoID') ?? '');
        $data['header'] = $this->db->query("SELECT srp_erp_customeritemprices.itemAutoID, srp_erp_itemmaster.itemSystemCode, seconeryItemCode, itemDescription FROM `srp_erp_customeritemprices` 
	                    LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_customeritemprices.itemAutoID
                        WHERE srp_erp_customeritemprices.companyID = {$companyID} AND cpsAutoID = {$cpsAutoID} 
                        GROUP BY srp_erp_customeritemprices.itemAutoID")->result_array();
        $data['details'] = $this->Customer_model->fetch_CustomerPrice_details($cpsAutoID);

        $this->load->view('system/customer/load_CustomerPrice_detailsView', $data);
    }

    function update_customer_price()
    {
        echo json_encode($this->Customer_model->update_customer_price());
    }

    function update_all_customer_price()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $myArray= $this->input->post('myArray');
//        echo '<pre>'; print_r($myArray);
        if($myArray){
            foreach ($myArray as $value){
                $data['salesPrice']=$value['customerPrice'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $this->db->update('srp_erp_customeritemprices', $data, array('customerPriceID' => $value['customerPriceID'], 'companyID' => $companyID));
            }
        }
        echo json_encode(TRUE);
    }

    function load_customers()
    {
        $data_arr = array();
        $categoryID = $this->input->post('categoryID');
        $comapnyid = $this->common_data['company_data']['company_id'];
        $cpsAutoID = $this->input->post('DocID');
        $where = " ";
        if (!empty($categoryID)) {
            $filtercategoryID = join(',', $categoryID);
            $where = " AND (partyCategoryID IN ($filtercategoryID) OR partyCategoryID  IS NULL OR partyCategoryID  = '')";
        }
        $customerFilter='';
        if(!empty($cpsAutoID)){
            $customeridcurrentdoc = $this->all_customer_drop_isactive_inactive_cps($cpsAutoID);
            if (!empty($customeridcurrentdoc)) {
                $customerid = array_column($customeridcurrentdoc, 'customerAutoID');
                $custFilter = join(',', $customerid);
                $customerFilter = " OR (customerAutoID IN ($custFilter) OR partyCategoryID  IS NULL OR partyCategoryID  = '')";
            }
        }
        $customer = $this->db->query("SELECT customerAutoID, customerSystemCode, customerName FROM srp_erp_customermaster WHERE (isActive = 1 {$customerFilter})AND deletedYN = 0 AND companyID = {$comapnyid} {$where}")->result_array();
        if (!empty($customer)) {
            foreach ($customer as $row) {
                $data_arr[trim($row['customerAutoID'] ?? '')] = trim($row['customerSystemCode'] . ' | ' . $row['customerName']);
            }
        }
        echo form_dropdown('customerAutoID[]', $data_arr, '', 'class="form-control" id="customerAutoID" multiple="multiple"');
    }

    /* Function added */
    function export_excel_customer_master(){
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Customer Detail Master List');
        $this->load->database();
        $data = $this->Customer_model->export_excel_customer_master();
        $rebate = getPolicyValues('CRP', 'All');; // policy for customer rebate process
        if($rebate == 1){
            $header = ['#', 'Cutomer Code', 'Cutomer Name','Secondary Code','Address','Email','Telephone','URL','FAX','Tax Group','Category','VAT Identification No','Credit Period','Credit Limit','Id Card Number','Receivable Account','Currency','Balance','Rebate GL Code','Rebate Percentage'];
        } else {
            $header = ['#', 'Cutomer Code', 'Cutomer Name','Secondary Code','Address','Email','Telephone','URL','FAX','Tax Group','Category','VAT Identification No','Credit Period','Credit Limit','Id Card Number','Receivable Account','Currency','Balance'];
        }

        $body = $data['customers'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Customer List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($body, null, 'A6');
        //        ob_clean();
        //        ob_start(); # added
        $filename = 'Customer Master.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //        ob_clean();
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function export_excel_customer_master_arabic(){
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Customer Master List');
        $this->load->database();
        $data = $this->Customer_model->export_excel_customer_master_arabic();
        $rebate = getPolicyValues('CRP', 'All');; // policy for customer rebate process
        if($rebate == 1){
            $header = ['#', 'Cutomer Code', 'Cutomer Name','Customer Name others / أسماء العملاء الآخرين','Secondary Code','Primary Address','Primary Address Others / العنوان الرئيسي','Secondary Address','Secondary Address Others / العنوان الثانوي','Country','Email','Telephone','URL','FAX','Tax Group','Category','VAT Identification No','Credit Period','Credit Limit','Id Card Number','Receivable Account','Currency','Balance','Rebate GL Code','Rebate Percentage'];
        } else {
            $header = ['#', 'Cutomer Code', 'Cutomer Name','Customer Name others / أسماء العملاء الآخرين','Secondary Code','Primary Address','Primary Address Others / العنوان الرئيسي','Secondary Address','Secondary Address Others / العنوان الثانوي','Country','Email','Telephone','URL','FAX','Tax Group','Category','VAT Identification No','Credit Period','Credit Limit','Id Card Number','Receivable Account','Currency','Balance'];
        }

        
        $body = $data['customers_arabic'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Customer List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($body, null, 'A6');
        //        ob_clean();
        //        ob_start(); # added
        $filename = 'Customer Master.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        //        ob_clean();
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function all_customer_drop_isactive_inactive_cps($pID)
    {
        
        $companyID = current_companyID();
        $masterID = $pID;
        $output = array();
     
        $this->db->select(" `documentSystemCode` AS `documentsystemcode`,`cus`.`isActive`, `cus`.`customerAutoID`,
        `cus`.`customerName`,`cus`.`customerSystemCode`,  `cus`.`customerCountry` ");
        $this->db->from("srp_erp_customeritempricesetup");
        $this->db->join("srp_erp_customeritemprices ", " `srp_erp_customeritemprices`.`cpsAutoID` = `srp_erp_customeritempricesetup`.`cpsAutoID` ","left");
        $this->db->join("srp_erp_customermaster cus", " `cus`.`customerAutoID` = `srp_erp_customeritemprices`.`customerAutoID` ");
        $this->db->where("srp_erp_customeritempricesetup.cpsAutoID", $masterID);
        $this->db->where("srp_erp_customeritempricesetup.companyID", $companyID);
       

        $output = $this->db->get()->result_array();
        return $output;
    }
    /* End  Function */  
}
