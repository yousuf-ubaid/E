<?php

class TaxCalculationGroup extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Taxcalculationgroup_model');
        $this->load->helpers('tax_formula');
    }

    function fetch_calculation_group()
    {

        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_taxcalculationformulamaster.companyID = " . $companyid . "";
        $this->datatables->select('taxCalculationformulaID,Description,taxType')
            ->where($where)
            ->from('srp_erp_taxcalculationformulamaster');
        $this->datatables->add_column('type_detail', '$1', 'get_tax_type(taxType)');
        $this->datatables->add_column('edit', '<span class="pull-right"><a onclick=\'fetchPage("system/tax/tax_formula_edit_buyback",$1,"Edit Tax Formula Group","TAX"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="assign_items($1,$2)"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link"></span></a> </span>', 'taxCalculationformulaID,taxType');
        echo $this->datatables->generate();
    }

    function save_tax_calculation_header()
    {
        $this->form_validation->set_rules('Description', 'Description', 'trim|required');
        $this->form_validation->set_rules('taxType', 'Tax Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            echo json_encode($this->Taxcalculationgroup_model->save_tax_calculation_header());
        }
    }

    function load_calculation_group()
    {
        echo json_encode($this->Taxcalculationgroup_model->load_calculation_group());
    }

    function delete_authority()
    {
        echo json_encode($this->Authoritymaster_model->delete_authority());
    }

    function fetch_formula_detail()
    {
        $url = site_url('TaxCalculationGroup/formulaDecodeTax');
        $companyid = $this->common_data['company_data']['company_id'];
        $taxCalculationformulaID = $this->input->post('taxCalculationformulaID');
        $where = "srp_erp_taxcalculationformuladetails.companyID = " . $companyid . " And taxCalculationformulaID = " . $taxCalculationformulaID . "";
        $this->datatables->select('formulaDetailID,description,sortOrder,srp_erp_taxmaster.taxDescription as taxDescription,srp_erp_taxmaster.taxShortCode as taxShortCode,srp_erp_taxcalculationformuladetails.taxPercentage as taxPercentage')
            ->where($where)
            ->join('srp_erp_taxmaster ', 'srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID')
            ->from('srp_erp_taxcalculationformuladetails');
        $this->datatables->add_column('type_detail', '<b>Description : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$2', 'taxDescription,taxShortCode');
        $this->datatables->add_column('edit', '<span class="pull-right"><a onclick="formulaModalOpen(\'$2\',$1,\''.$url.'\', \'\',1)"><span title="" rel="tooltip" class="fa fa-superscript" data-original-title="Formula"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="open_formula_detail_edit($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_tax_calculation($1)"><span title="Delete" rel="tooltip" style="color:rgb(209, 91, 71)" class="glyphicon glyphicon-trash"></span></a></span>', 'formulaDetailID,description');
        echo $this->datatables->generate();
    }

    function save_tax_formula_detail_form(){
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('taxMasterAutoID', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'Sort Order', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'Sort Order', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            echo json_encode($this->Taxcalculationgroup_model->save_tax_formula_detail_form());
        }
    }

    function load_formula_detail(){
        echo json_encode($this->Taxcalculationgroup_model->load_formula_detail());
    }

    function formulaDecodeTax()
    {
        $payGroupID = $this->input->post('payGroupID');
        $decodeType = $this->uri->segment(3);
        $companyID = current_companyID();

        $formula = $this->db->select('formula')->from('srp_erp_taxcalculationformuladetails')
            ->where('formulaDetailID', $payGroupID)->where('companyID', $companyID)->get()->row('formula');


        $sortOrder = $this->db->query("SELECT sortOrder,taxCalculationformulaID FROM srp_erp_taxcalculationformuladetails WHERE formulaDetailID='$payGroupID' ")->row_array();
        $taxCalculationformulaID=$sortOrder['taxCalculationformulaID'];
        $sortOrder=$sortOrder['sortOrder'];

        $tax_categories = $this->db->query("SELECT
	srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription
FROM
	srp_erp_taxcalculationformuladetails
LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
WHERE
	taxCalculationformulaID = $taxCalculationformulaID
AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();


        $formulaDecodeData = ['decodedList' => '','taxes' => ''];

        if (!empty($formula) && $formula != null) {
            $formulaDecodeData['decodedList'] = formulaDecodeTax($formula);
        }

        if (!empty($tax_categories)) {
            $formulaDecodeData['taxes'] = $tax_categories;
        }
        $formulaDecodeData['from-tax'] = 1;
        echo json_encode($formulaDecodeData);
    }

    function saveFormula_tax(){
        $this->form_validation->set_rules('payGroupID', 'ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Taxcalculationgroup_model->saveFormula_tax());
        }
    }

    function fetch_item()
    {
      /*  $isMult = getPolicyValues('AMT', 'All');*/
        $isMult = 1;
        $type = ($this->input->post('taxType') == 1)? 'salesTaxFormulaID': 'purchaseTaxFormulaID';
        $type_id = $this->input->post('taxType');
        $linkID = $this->input->post('linkID');
        $company_id = current_companyID();
        $taxFrmID=$this->input->post('taxCalculationformulaID');
        //$where = "imMas.companyID = " . $companyid . " And (imMas.$type is null OR imMas.$type = " .$taxFrmID. ") ";
        $where = "imMas.companyID = {$company_id}";
        if($isMult == 0){
            $where .= " And (imMas.$type is null OR imMas.$type = {$taxFrmID}) ";
        }
        else{
            // $type = "frmTB{$type}";
            $type_frm = 'frmTBpurchaseTaxFormulaID';
            if($linkID == 1){
                $type = 'frm.taxFormulaID AS frmTBpurchaseTaxFormulaID';
            }else{
                $type = 'imMas.salesTaxFormulaID AS frmTBpurchaseTaxFormulaID';
            }
            
        }

        $this->db->select('itemAutoID');
        $this->db->from('srp_erp_itemtaxformula as frm');
        $this->db->where('frm.taxType',$type_id);
        $this->db->where('frm.taxFormulaID',$taxFrmID);
        $items_arr = $this->db->get()->result_array();

        $str_items_arr = array();

        foreach($items_arr as $key => $value){
            $str_items_arr[] = $value['itemAutoID'];
        }


        $this->datatables->select('imMas.itemAutoID AS itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory
                ,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,
                assteDescription,isActive,companyLocalWacAmount,subcat.description as SubCategoryDescription,subsubcat.description as SubSubCategoryDescription,
                CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount,
                CONCAT(itemSystemCode," - ",itemDescription) as description, isSubitemExist,'.$type.'', false)
            ->from('srp_erp_itemmaster imMas')
            ->join('srp_erp_itemcategory subcat', 'imMas.subcategoryID = subcat.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'imMas.subSubCategoryID = subsubcat.itemCategoryID','left');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }

        if($isMult == 1){
            // $this->datatables->join("(SELECT itemAutoID, taxFormulaID AS $type FROM srp_erp_itemtaxformula WHERE taxType = {$type_id} AND taxFormulaID = {$taxFrmID}) AS frm",
            //     'frm.itemAutoID = imMas.itemAutoID', 'left');
            if($linkID == 1){
                $this->datatables->join("srp_erp_itemtaxformula as frm",'frm.itemAutoID = imMas.itemAutoID','left');
                $this->datatables->where('frm.taxType',$type_id);
                $this->datatables->where('frm.taxFormulaID',$taxFrmID);
            }else{
                if(count($str_items_arr) > 0){
                    $this->datatables->where_in('imMas.itemAutoID NOT',$str_items_arr);
                }
                //$this->datatables->join("srp_erp_itemtaxformula as frm",'frm.itemAutoID = imMas.itemAutoID','left');
                
            }
        }
           

        

        $this->datatables->where($where);
        
        /*$this->datatables->where('imMas.'.$type.'', null);
        $this->datatables->or_where('imMas.'.$type.'', $this->input->post('taxCalculationformulaID'));*/
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('TotalWacAmount', '$1  $2', 'format_number(companyLocalWacAmount,2),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '$1', 'taxChkbox(itemAutoID,'.$type_frm.','.$taxFrmID.')');


        echo $this->datatables->generate();
    }

    function update_item_taxid(){
        echo json_encode($this->Taxcalculationgroup_model->update_item_taxid());
    }

    function fetch_tax_category(){ 
        $companyID = current_companyID();
        $this->datatables->select('srp_erp_tax_vat_main_categories.taxVatMainCategoriesAutoID,mainCategoryDescription,isActive, IFNULL(`srp_erp_taxcalculationformulamaster`.`Description`,\'-\')  as Description,taxType,taxCalculationformulaID, srp_erp_tax_vat_type.Description as vatTypeDescription, srp_erp_taxledger.taxFormulaMasterID as detailExist')
        ->where('srp_erp_tax_vat_main_categories.companyID',$companyID)
        ->from('srp_erp_tax_vat_main_categories')
        ->join('srp_erp_taxcalculationformulamaster','srp_erp_taxcalculationformulamaster.taxVatMainCategoriesAutoID = srp_erp_tax_vat_main_categories.taxVatMainCategoriesAutoID','left')
        ->join('srp_erp_tax_vat_type','srp_erp_taxcalculationformulamaster.vatTypeID = srp_erp_tax_vat_type.vatTypeID','left')
        ->join("(SELECT taxformulaMasterID FROM srp_erp_taxledger WHERE companyID = {$companyID} GROUP BY taxFormulaMasterID)srp_erp_taxledger",'srp_erp_taxledger.taxFormulaMasterID = srp_erp_taxcalculationformulamaster.taxCalculationformulaID','left')
        ->add_column('type_detail', '$1', 'get_tax_type_new(taxType)')
        ->add_column('edit', '$1','load_tax_category_action(taxCalculationformulaID,taxType, detailExist)');
        echo $this->datatables->generate();
    }
    function fetch_tax_group_detail() { 
        $companyID = current_companyID();
        $taxDetailAutoID = trim($this->input->post('taxDetailAutoID') ?? '');
        $documentMasterAutoID =  trim($this->input->post('documentMasterAutoID') ?? '');
        $documentID = trim($this->input->post('documentID') ?? '');
        $documentDetailAutoID = trim($this->input->post('documentDetailID') ?? '');
        $data['documentID']  = $documentID;
        $colTaxDetail = '';
        if(!empty($taxDetailAutoID)){
            $colTaxDetail = ' AND srp_erp_taxledger.taxDetailAutoID = '.$taxDetailAutoID.' ';
        }else {
            $colTaxDetail = ' AND documentDetailAutoID = '.$documentDetailAutoID.' ';
        }

        switch ($documentID) {
            case "GRV": case "CINV": case 'PO-PRQ': case 'DO': case 'RV': case 'CNT': case 'BSI': case 'CN':case 'DN':case 'PV' :

               if($documentID == 'PO-PRQ'){
                    $documentID = 'PO';
               }else{ 
                    $documentID = $documentID;
               }

                $detail = $this->db->query("SELECT
                srp_erp_taxledger.amount,
                srp_erp_taxledger.taxPercentage,
                srp_erp_taxledger.taxFormulaDetailID,
                srp_erp_taxledger.taxLedgerAutoID,
                srp_erp_taxcalculationformuladetails.description,
                srp_erp_taxmaster.taxDescription,
                srp_erp_taxcalculationformulamaster.Description as taxdescriptioncat,
                srp_erp_taxmaster.taxShortCode
                FROM 
                srp_erp_taxledger
                LEFT JOIN  srp_erp_taxmaster ON  srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                LEFT JOIN srp_erp_taxcalculationformuladetails ON  srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID 
                LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxcalculationformuladetails.taxCalculationformulaID
                WHERE
	            srp_erp_taxledger.companyID = $companyID 
                AND documentID = '{$documentID}'
                AND documentMasterAutoID = $documentMasterAutoID
	            AND documentDetailAutoID = $documentDetailAutoID
                ORDER BY
	            sortOrder 
	            ASC")->result_array();

            break;
 
            default:
            $detail = $this->db->query("SELECT 
            srp_erp_taxledger.amount,
            srp_erp_taxledger.taxPercentage,
            srp_erp_taxledger.taxFormulaDetailID,
            srp_erp_taxledger.taxLedgerAutoID,
            srp_erp_taxcalculationformuladetails.description,
            srp_erp_taxmaster.taxDescription,
            srp_erp_taxcalculationformulamaster.Description as taxdescriptioncat,
            srp_erp_taxmaster.taxShortCode
            FROM 
            srp_erp_taxledger
            LEFT JOIN  srp_erp_taxmaster ON  srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
            LEFT JOIN srp_erp_taxcalculationformuladetails ON  srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID 
            LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxcalculationformuladetails.taxCalculationformulaID
            Where
            srp_erp_taxledger.companyID = $companyID
            $colTaxDetail
            AND documentID = '{$documentID}'
            AND documentMasterAutoID = $documentMasterAutoID
            ORDER BY
            sortOrder 
            ASC")->result_array();
            

        }
        $taxname = array_unique(array_column($detail, 'taxdescriptioncat'));
        $data['taxCatName'] = $taxname[0];
        $data['taxDetail']  =  $detail;
        $data['detailTBL']  = trim($this->input->post('detailTBL') ?? '');
        $data['detailColName']  = trim($this->input->post('detailColName') ?? '');
        $data['currency_decimal']  = trim($this->input->post('currency_decimal') ?? '');
        $data['documentMasterAutoID']  = $documentMasterAutoID;
        $data['documentDetailAutoID']  = $documentDetailAutoID;
       
        $data['taxDetailAutoID'] = $taxDetailAutoID;
        $data['isFromView'] = trim($this->input->post('isFromView') ?? '');

        $this->load->view('system/tax/tax_calculation_drill_down', $data);
    }

    function update_tax_calculation_DD(){ 
        $companyID = current_companyID();
        $taxLedgerAutoID = trim($this->input->post('taxLedgerAutoID') ?? '');
        $taxFormulaDetailID = trim($this->input->post('taxFormulaDetailID') ?? '');
        $documentMasterAutoID = trim($this->input->post('documentMasterAutoID') ?? '');
        $documentDetailID = trim($this->input->post('documentDetailID') ?? '');
        $value = trim($this->input->post('value') ?? '');
        $detailTBL = trim($this->input->post('detailTBL') ?? '');
        $detailColName = trim($this->input->post('detailColName') ?? '');
        $documentID = trim($this->input->post('documentID') ?? '');
        $FieldToUpdate = trim($this->input->post('FieldToUpdate') ?? ''); //1- Percentage 2-Amount
     
        $taxCalculationformulaID = $this->db->query("SELECT
                                                    taxCalculationformulaID 
                                                    FROM 
                                                    `srp_erp_taxcalculationformuladetails` 
                                                    where 
                                                    companyID = $companyID 
                                                    AND 
                                                    formulaDetailID = $taxFormulaDetailID")->row('taxCalculationformulaID');
        
        switch ($documentID) {
    
            case "GRV":
                  $query = $this->db->query("SELECT
	                                      IF(purchaseOrderMastertID!='',( ($detailTBL.receivedQty * unitAmount) + ($detailTBL.receivedQty * (IFNULL(discountAmount,0))  ) ),receivedTotalAmount ) AS taxApplicableAmount,
	                                      IF(purchaseOrderMastertID!='',(($detailTBL.receivedQty * (IFNULL(discountAmount,0)))),0 ) AS discountAmount 
                                          FROM
	                                      $detailTBL
	                                      LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderDetailsID	= $detailTBL.purchaseOrderDetailsID
                                          WHERE
	                                      $detailTBL.companyID = $companyID 
                                          AND $detailColName = $documentDetailID")->row_array();
                                        break;

            case "PO": case "PO-PRQ":
                    $query = $this->db->query("SELECT
                                        (totalAmount + (requestedQty * discountAmount)) as taxApplicableAmount,
                                        (requestedQty * discountAmount) as discountAmount
                                        FROM
                                        $detailTBL
                                        WHERE
                                        companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();
            break;
    
            case "CINV": case "RV":
                    $query = $this->db->query("SELECT
                                        (transactionAmount - taxAmount) as taxApplicableAmount,
                                        discountAmount as discountAmount
                                        FROM
                                        $detailTBL
                                        WHERE
                                        companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();
            break;
    
            case "DO":
                    $query = $this->db->query("SELECT
                                        ROUND(((deliveredTransactionAmount + (discountAmount * requestedQty)) - taxAmount), 3) AS taxApplicableAmount,
                                        (discountAmount * requestedQty) as discountAmount
                                        FROM
                                        $detailTBL
                                        WHERE
                                        companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();
            break;

            case "CNT":
                    $query = $this->db->query("SELECT
                                        transactionAmount as taxApplicableAmount,
                                        discountAmount as discountAmount
                                        FROM
                                        $detailTBL
                                        WHERE
                                        companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();
            break;

            case "BSI":

                $documentType = $this->db->query("SELECT
	                                                  type,grvType
                                                      FROM
	                                                  `srp_erp_paysupplierinvoicedetail`
	                                                  where 
	                                                  $detailColName = $documentDetailID")->row_array();

                if($documentType['type'] == "PO" && strtolower(trim($documentType['grvType'] ?? '')) == "standard"){

                    $query = $this->db->query("SELECT
                                           ((srp_erp_paysupplierinvoicedetail.requestedQty * unitAmount)+(srp_erp_paysupplierinvoicedetail.requestedQty* IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)	))  as taxApplicableAmount,
		                                   (srp_erp_paysupplierinvoicedetail.requestedQty * IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)) as discountAmount
                                        FROM
                                        $detailTBL
                                        LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderDetailsID = $detailTBL.purchaseOrderDetailsID
                                        WHERE
                                        $detailTBL.companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();

                }else {

                    $query = $this->db->query("SELECT
                                        (transactionAmount + IFNULL(discountAmount,0)) as taxApplicableAmount,
                                        discountAmount as discountAmount
                                        FROM
                                        $detailTBL
                                        WHERE
                                        companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();

                }
            break;

            case "CN":
                $query = $this->db->query("SELECT
                                        (transactionAmount - taxAmount) as taxApplicableAmount,
                                        0 as discountAmount
                                        FROM
                                        $detailTBL
                                        WHERE
                                        companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();
            break;

            case "PV":
                $query = $this->db->query("SELECT
                                        (transactionAmount + discountAmount) as taxApplicableAmount,
                                        0 as discountAmount
                                        FROM
                                        $detailTBL
                                        WHERE
                                        companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();
            break;
            case "DN":
                $query = $this->db->query("SELECT
                                        (transactionAmount) as taxApplicableAmount,
                                        0 as discountAmount
                                        FROM
                                        $detailTBL
                                        WHERE
                                        companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();
                break;
            case "GRV-ADD":
                $query = $this->db->query("SELECT
                                        (total_amount) as taxApplicableAmount,
                                        0 as discountAmount
                                        FROM
                                        $detailTBL
                                        WHERE
                                        companyID = $companyID 
                                        AND $detailColName = $documentDetailID")->row_array();
                break;


            default:
                echo json_encode(array('e', 'Document id not configured'));
                die();
        }

        switch ($documentID) {
            case  "PO-PRQ":
                $documentID = 'PO';
            break;
            $documentID = $documentID;
            default:
        }

        if($FieldToUpdate == 2 ) {




            $val = $this->db->query("SELECT 
                                          *,
	                                     `srp_erp_taxcalculationformuladetails`.`formula` AS `formulaString`,
	                                     `srp_erp_taxcalculationformuladetails`.`taxMasters` AS `payGroupCategories`,
	                                     IFNULL( ledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentagedetail,
	                                     `ledger`.`formula` AS `formulaLedger`,
	                                     `ledger`.`amount` AS `amountcal`,
	                                     `ledger`.`taxPercentage` AS `taxPercentagecal` 
                                         FROM
	                                     `srp_erp_taxcalculationformuladetails`
	                                     LEFT JOIN ( SELECT taxFormulaDetailID, taxPercentage, formula, amount,taxLedgerAutoID FROM `srp_erp_taxledger`   ) ledger ON `ledger`.`taxFormulaDetailID` = `srp_erp_taxcalculationformuladetails`.`formulaDetailID`
	                                     LEFT JOIN `srp_erp_taxmaster` ON `srp_erp_taxmaster`.`taxMasterAutoID` = `srp_erp_taxcalculationformuladetails`.`taxMasterAutoID` 
                                         WHERE
	                                     taxLedgerAutoID = {$taxLedgerAutoID}")->row_array();

            $sortOrder = $val['sortOrder'];

            $tax_categories = $this->db->query("SELECT
                                                    srp_erp_taxcalculationformuladetails.*,
                                                    srp_erp_taxmaster.taxDescription,
                                                    srp_erp_taxmaster.taxPercentage,
                                                    srp_erp_taxmaster.taxCategory as taxCategory
                                                    FROM
                                                    srp_erp_taxcalculationformuladetails
                                                    LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                                    WHERE
                                                    taxCalculationformulaID ={$val['taxCalculationformulaID']}
                                                    AND srp_erp_taxcalculationformuladetails.companyID = {$companyID} AND sortOrder < {$sortOrder}")->result_array();


        $formulaBuilder = tax_formulaBuilder_to_sql_vat($val, $tax_categories,$query['taxApplicableAmount'] , $query['discountAmount'], 100, $val['taxCalculationformulaID'], $documentID, $documentMasterAutoID, $documentDetailID, 0, $taxFormulaDetailID, $taxLedgerAutoID,$FieldToUpdate);
        $formulaDecodeval = $formulaBuilder['formulaDecode'];
        $amounttx = $this->db->query("SELECT $formulaDecodeval as amount")->row('amount');

        $this->db->query("UPDATE srp_erp_taxledger
                              SET amount = $value,
             ismanuallychanged =1
            WHERE
            taxLedgerAutoID = $taxLedgerAutoID");
            $value1 = ($value/($amounttx)*100);
            $value = $value1;
        }

     
        $this->db->query("UPDATE srp_erp_taxledger
                          SET taxPercentage = {$value},
                           ismanuallychanged =1
                          WHERE
                          taxLedgerAutoID = {$taxLedgerAutoID}");

        $taxAmount = update_tax_calculation_DD($query['taxApplicableAmount'],$query['discountAmount'],$value,$taxLedgerAutoID,$taxCalculationformulaID,$taxFormulaDetailID,$documentID,$documentMasterAutoID,$documentDetailID,1,$FieldToUpdate);
        $totalTaxAmount = array_column($taxAmount, 'amount');
       $totalTaxAmount = array_sum($totalTaxAmount);


        if(!empty($taxAmount)){ 
            foreach($taxAmount as $val){ 
                $this->db->query("UPDATE srp_erp_taxledger
                SET amount = {$val['amount']}
                WHERE taxFormulaDetailID = {$val['taxCalculationFormulaDetailID']}
                AND taxFormulaMasterID = $taxCalculationformulaID
                AND documentID = '{$documentID}'
                AND documentMasterAutoID = $documentMasterAutoID
                AND documentDetailAutoID = $documentDetailID
                ");
            }
        }

        if($totalTaxAmount > 0){
            switch ($documentID) {                
                case "CINV": case "CN": case "RV":
                    $this->db->query("UPDATE $detailTBL
                                        SET 
                                            transactionAmount = {$query['taxApplicableAmount']} + $totalTaxAmount,
                                            taxAmount = $totalTaxAmount
                                        WHERE
                                            $detailColName = $documentDetailID"); 
                break;
    
                case "DO":
                    $this->db->query("UPDATE $detailTBL
                                        SET 
                                            deliveredTransactionAmount = ({$query['taxApplicableAmount']} - ({$query['discountAmount']})) + $totalTaxAmount,
                                            transactionAmount = ({$query['taxApplicableAmount']} - ({$query['discountAmount']})) + $totalTaxAmount,
                                            taxAmount = $totalTaxAmount
                                        WHERE
                                            $detailColName = $documentDetailID"); 
                break;
                
                default:
                    $this->db->query("UPDATE $detailTBL
                                        SET 
                                            taxAmount = $totalTaxAmount
                                        WHERE
                                            $detailColName = $documentDetailID"); 
            }            
        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(array('s','Tax updated sucessfully'));
        } else {
            $this->db->trans_rollback();
            echo json_encode(array('e', validation_errors()));
        }
    }

    function delete_taxFormula(){
        $taxCalculationFormulaDetailID = trim($this->input->post('formulaDetailID') ?? '');
        $companyID = current_companyID();
        $isPulled = $this->db->query("SELECT
	                                      COUNT( taxLedgerAutoID ) AS taxCount 
                                          FROM
	                                      `srp_erp_taxledger` 
                                          WHERE
                                          companyID = $companyID  
	                                      AND taxFormulaDetailID = $taxCalculationFormulaDetailID")->row('taxCount');

        if($isPulled > 0){
            echo json_encode(array('e','You cannot delete the following document,formula has been already pulled'));
            exit;
        }else {
            $result = $this->db->delete('srp_erp_taxcalculationformuladetails',array('formulaDetailID'=>$taxCalculationFormulaDetailID));
            if ($result) {
                echo json_encode(array('s','Deleted Successfully'));
            }
        }
    }



}
