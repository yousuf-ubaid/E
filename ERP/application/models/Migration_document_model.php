<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_document_model extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
       // $this->load->library('../controllers/Payable');
       $this->load->library('../controllers/Payable');
       // $this->load->controllers('Payable');
    }

    function save_debitnote_header($data)
    {
        $this->db->trans_start();

        $ss=$this->db->insert('srp_erp_migration_header', $data);

            
        $last_id = $this->db->insert_id();

        return $last_id;

    }

    function post_validated_excel_data()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $migrationHeaderMasterID = trim($this->input->post('migrationHeaderMasterID') ?? '');
        $companyID = current_companyID();
        $currency_decimal=$this->common_data['company_data']['company_reporting_decimal'];

        $this->db->select('*');
        $this->db->from('srp_erp_migration_header');
        $this->db->where('migrationHeaderMasterID', trim($migrationHeaderMasterID));
        $master = $this->db->get()->row_array();

       // $date_format_policy = date_format_policy();
        


        if($master){

            //genarate Table header
            // $migtation_config_details = $this->db->query("SELECT * FROM srp_erp_migration_config WHERE documentID='" . $master['documentID'] . "' AND companyID = {$companyID} AND isExcel=1 order by sortOrder asc")->result_array();

            // $header_data = [];
            // $header_data[0]='LineID';
            // $header_data[1]='headerReferenceID';
    
            // if (!empty($migtation_config_details)) {
            //     foreach ($migtation_config_details as $row) {
            //         $header_data[] = $row['tempColoumnName'];
            //     }
            // }
            // $data['master_data']=$master;
            // $data['table_header']=$header_data;

            // $csv_data=[];

            $migration_header_details_refIDs=$this->db->query("SELECT detTB.headerReferenceID
                                        FROM  srp_erp_migration_header_details detTB
                                        WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID}
                                        GROUP BY  detTB.headerReferenceID" )->result_array();
            
            //print_r( $migration_header_details);exit;
            $line =[];

            if (!empty($migration_header_details_refIDs)) {
                foreach ($migration_header_details_refIDs as $key=>$row) {

                    $migration_header_details_lineIDs=$this->db->query("SELECT detTB.excelLineID
                    FROM  srp_erp_migration_header_details detTB
                    WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND detTB.headerReferenceID ={$row['headerReferenceID']}
                    GROUP BY  detTB.excelLineID" )->result_array();

                    ///arrange details array

                   $migration_header_detailsx=$this->db->query("SELECT detTB.value,detTB.excelLineID ,detTB.headerReferenceID,hTB.documentID,hTB.noOfRecords,confTB.tempColoumnName,confTB.sortOrder,confTB.tableColumnName
                   FROM  srp_erp_migration_header_details detTB
                   JOIN srp_erp_migration_header hTB ON hTB.migrationHeaderMasterID=detTB.migrationHeaderMasterID
                   JOIN srp_erp_migration_config confTB ON confTB.migrationConfigID=detTB.migrationConfMasterID
                   WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND confTB.isheader=0 AND
                   detTB.headerReferenceID ={$row['headerReferenceID']} order by confTB.sortOrder asc" )->result_array();

                    if($master['documentID']=='BSI' && $master['documentType']=='StandardExpense' ){

                        if($migration_header_detailsx){
                            $gl_code=[];
                            $gl_code_des=[];
                            $segment_gl=[];
                            $amount=[];
                            $description=[];
                            $discountPercentage=[];
                            $discountAmount=[];
                            $Netamount=[];
                            $details_arr=[];
                            foreach($migration_header_detailsx as $detail_arr){
                                if($detail_arr['tableColumnName']=='gl_code'){
                                    $ql_det = $this->db->query("SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory FROM srp_erp_chartofaccounts WHERE Systemaccountcode='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    $gl_code[]=$ql_det['GLAutoID'];
                                    $details_arr[$detail_arr['tableColumnName']]=$gl_code;

                                    $gl_code_des[]=$ql_det['systemAccountCode'].'|'.$ql_det['GLSecondaryCode'].'|'.$ql_det['GLDescription'].'|'.$ql_det['subCategory'];
                                    $details_arr['gl_code_des']=$gl_code_des;
                                }

                                if($detail_arr['tableColumnName']=='segment_gl'){
                                    $segment_det = $this->db->query("SELECT * FROM srp_erp_segment WHERE segmentCode='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    $segment_gl[]=$segment_det['segmentID'].'|'.$segment_det['segmentCode'];
                                    $details_arr[$detail_arr['tableColumnName']]=$segment_gl;

                                }

                                if($detail_arr['tableColumnName']=='amount'){
                                    $amount[]=number_format($detail_arr['value'],$currency_decimal) ;
                                    $details_arr[$detail_arr['tableColumnName']]=$amount;
                                }

                                if($detail_arr['tableColumnName']=='description'){
                                    $description[]=$detail_arr['value'];
                                    $details_arr[$detail_arr['tableColumnName']]=$description;
                                }

                                if($detail_arr['tableColumnName']=='discountPercentage'){
                                    $discountPercentage[]=$detail_arr['value'];
                                    $details_arr[$detail_arr['tableColumnName']]=$discountPercentage;

                                }

                            }
                        
                            if(count($amount)==count($discountPercentage)){
                                foreach($amount as $keyAm => $am){
                                    $discamnt=(number_format($am,$currency_decimal) * number_format($discountPercentage[$keyAm ],$currency_decimal))/100;

                                    $netAm=number_format($am,$currency_decimal)-number_format($discamnt,$currency_decimal);
                                    $discountAmount[]=number_format($discamnt,$currency_decimal);
                                    $Netamount[]=number_format($netAm,$currency_decimal);

                                }

                                $details_arr['discountAmount'] = $discountAmount;
                                $details_arr['Netamount'] = $Netamount;
                            }
                        }

                        if($migration_header_details_lineIDs){

                            $migration_header_details=$this->db->query("SELECT detTB.value,detTB.excelLineID ,detTB.headerReferenceID,hTB.documentID,hTB.noOfRecords,confTB.tempColoumnName,confTB.sortOrder,confTB.tableColumnName
                            FROM  srp_erp_migration_header_details detTB
                            JOIN srp_erp_migration_header hTB ON hTB.migrationHeaderMasterID=detTB.migrationHeaderMasterID
                            JOIN srp_erp_migration_config confTB ON confTB.migrationConfigID=detTB.migrationConfMasterID
                            WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND confTB.isheader=1 AND
                            detTB.excelLineID ={$migration_header_details_lineIDs[0]['excelLineID']} order by confTB.sortOrder asc" )->result_array();

                            $data_arr=[];

                            foreach($migration_header_details as $detail){

                                if($detail['tableColumnName']=='segment'){
                                    
                                    $segment = $this->db->query("SELECT * FROM srp_erp_segment WHERE segmentCode='" . $detail['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$segment['segmentID'];
                                }

                                if($detail['tableColumnName']=='transactionCurrencyID'){
                                    
                                    $CurrencyCode = $this->db->query("SELECT * FROM srp_erp_companycurrencyassign WHERE CurrencyCode='" . $detail['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$CurrencyCode['currencyID'];
                                    $data_arr['currency_code']=$CurrencyCode['CurrencyCode'].'|'.$CurrencyCode['CurrencyName'];
                                    
                                }

                                if($detail['tableColumnName']=='supplierID'){
                                    
                                    $CurrencyCode = $this->db->query("SELECT * FROM srp_erp_suppliermaster WHERE suppliersystemcode='" . $detail['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$CurrencyCode['supplierAutoID'];
                                }

                                // if($detail['tableColumnName']=='supplierID'){
                                    
                                //     $CurrencyCode = $this->db->query("SELECT * FROM srp_erp_suppliermaster WHERE suppliersystemcode='" . $detail['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                //     $data_arr[$detail['tableColumnName']]=$CurrencyCode['supplierAutoID'];
                                // }

                                if($detail['tableColumnName']=='bookingDate'){
                                    $bookingDate = input_format_date(trim($detail['value'] ?? ''), $date_format_policy);
                                    //$newDate = date('Y-m-d' , strtotime($detail['value']));
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];


                                    $this->db->SELECT("*");
                                    $this->db->FROM('srp_erp_companyfinanceyear');
                                    $this->db->WHERE('companyID', $companyID);
                                    $this->db->where("'{$bookingDate}' BETWEEN beginingDate AND endingDate");
                                    $this->db->where('isActive',1);
                                    $this->db->where('isClosed',0);
                                    $financeYearDetails = $this->db->get()->row_array();

                                    $data_arr['financeyear']=$financeYearDetails['companyFinanceYearID'];


                                    $this->db->SELECT("*");
                                    $this->db->FROM('srp_erp_companyfinanceperiod');
                                    $this->db->WHERE('companyID', $companyID);
                                    $this->db->where("'{$bookingDate}' BETWEEN dateFrom AND dateTo");
                                    $financePeriodDetails = $this->db->get()->row_array();

                                    $data_arr['financeyear_period']=$financePeriodDetails['companyFinancePeriodID'];
                                // print_r( $financePeriodDetails );exit;

                                $convertFormat=convert_date_format_sql();
                                $this->db->select('companyFinanceYearID,DATE_FORMAT(beginingDate,\''.$convertFormat.'\') AS dateFrom,DATE_FORMAT(endingDate,\''.$convertFormat.'\') AS dateTo ');
                                $this->db->from('srp_erp_companyfinanceyear');
                                $this->db->where('companyFinanceYearID',$financeYearDetails['companyFinanceYearID']);
                                $this->db->where('isActive',1);
                                $this->db->where('isClosed',0);
                                $y=$this->db->get()->row_array();
    
                                $data_arr['companyFinanceYear']=$y['dateFrom'].' - '.$y['dateTo'];
                                }

                                if($detail['tableColumnName']=='supplierInvoiceDueDate'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                $data_arr['invoiceType']='StandardExpense';
                                $data_arr['header_post_url']='Payable/save_supplier_invoice_header';
                                $data_arr['detail_post_url']='Payable/save_bsi_detail_multiple';                  

                                if($detail['tableColumnName']=='referenceno'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='supplier_invoice_no'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='invoiceDate'){
                                // $invoiceDate = input_format_date(trim($detail['value'] ?? ''), $date_format_policy);
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='supplier_invoice_no'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='comments'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }
                            }

                            $data_arr['invoice_details']=$details_arr;

                            $line[]=$data_arr;
                        
                        }
                    }

                    if($master['documentID']=='CINV' && $master['documentType']=='DirectIncome'){

                        if($migration_header_detailsx){
                            $gl_code=[];
                            $gl_code_des=[];
                            $segment_gl=[];
                            $amount=[];
                            $description=[];
                            $discountPercentage=[];
                            $discountAmount=[];
                            $Netamount=[];
                            $details_arr=[];
                            foreach($migration_header_detailsx as $detail_arr){
                                if($detail_arr['tableColumnName']=='gl_code'){
                                    $ql_det = $this->db->query("SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory FROM srp_erp_chartofaccounts WHERE Systemaccountcode='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    $gl_code[]=$ql_det['GLAutoID'];
                                    $details_arr[$detail_arr['tableColumnName']]=$gl_code;

                                    $gl_code_des[]=$ql_det['systemAccountCode'].' | '.$ql_det['GLSecondaryCode'].' | '.$ql_det['GLDescription'].' | '.$ql_det['subCategory'];
                                    $details_arr['gl_code_des']=$gl_code_des;
                                }

                                if($detail_arr['tableColumnName']=='segment_gl'){
                                    $segment_det = $this->db->query("SELECT * FROM srp_erp_segment WHERE segmentCode='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    $segment_gl[]=$segment_det['segmentID'].'|'.$segment_det['segmentCode'];
                                    $details_arr[$detail_arr['tableColumnName']]=$segment_gl;

                                }

                                if($detail_arr['tableColumnName']=='amount'){
                                    $amount[]=number_format($detail_arr['value'],$currency_decimal) ;
                                    $details_arr[$detail_arr['tableColumnName']]=$amount;
                                }

                                if($detail_arr['tableColumnName']=='description'){
                                    $description[]=$detail_arr['value'];
                                    $details_arr[$detail_arr['tableColumnName']]=$description;
                                }

                                if($detail_arr['tableColumnName']=='discountPercentage'){
                                    $discountPercentage[]=$detail_arr['value'];
                                    $details_arr[$detail_arr['tableColumnName']]=$discountPercentage;

                                }

                            }
                        
                            if(count($amount)==count($discountPercentage)){
                                foreach($amount as $keyAm => $am){
                                    $discamnt=(number_format($am,$currency_decimal) * number_format($discountPercentage[$keyAm ],$currency_decimal))/100;

                                    $netAm=number_format($am,$currency_decimal)-number_format($discamnt,$currency_decimal);
                                    $discountAmount[]=number_format($discamnt,$currency_decimal);
                                    $Netamount[]=number_format($netAm,$currency_decimal);

                                }

                                $details_arr['discountAmount'] = $discountAmount;
                                $details_arr['Netamount'] = $Netamount;
                            }
                        }

                        if($migration_header_details_lineIDs){

                            $migration_header_details=$this->db->query("SELECT detTB.value,detTB.excelLineID ,detTB.headerReferenceID,hTB.documentID,hTB.noOfRecords,confTB.tempColoumnName,confTB.sortOrder,confTB.tableColumnName
                            FROM  srp_erp_migration_header_details detTB
                            JOIN srp_erp_migration_header hTB ON hTB.migrationHeaderMasterID=detTB.migrationHeaderMasterID
                            JOIN srp_erp_migration_config confTB ON confTB.migrationConfigID=detTB.migrationConfMasterID
                            WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND confTB.isheader=1 AND
                            detTB.excelLineID ={$migration_header_details_lineIDs[0]['excelLineID']} order by confTB.sortOrder asc" )->result_array();

                            $data_arr=[];

                            foreach($migration_header_details as $detail){

                                if($detail['tableColumnName']=='segment'){
                                
                                    $segment = $this->db->query("SELECT * FROM srp_erp_segment WHERE segmentCode='" . $detail['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$segment['segmentID'].'|'.$segment['segmentCode'];
                                }

                                if($detail['tableColumnName']=='transactionCurrencyID'){
                                    
                                    $CurrencyCode = $this->db->query("SELECT * FROM srp_erp_companycurrencyassign WHERE CurrencyCode='" . $detail['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$CurrencyCode['currencyID'];
                                    $data_arr['currency_code']=$CurrencyCode['CurrencyCode'].'|'.$CurrencyCode['CurrencyName'];
                                    
                                }

                                if($detail['tableColumnName']=='customerID'){
                                    
                                    $CurrencyCode = $this->db->query("SELECT * FROM srp_erp_customermaster WHERE customerSystemCode='" . $detail['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$CurrencyCode['customerAutoID'];
                                }

                                if($detail['tableColumnName']=='RVbankCode'){
                                    
                                    $bank = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE bankAccountNumber='" . $detail['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$bank['GLAutoID'];
                                }

                                if($detail['tableColumnName']=='salesPersonID'){
                                    
                                    $sale = $this->db->query("SELECT * FROM srp_erp_salespersonmaster WHERE SalesPersonCode='" . $detail['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$sale['salesPersonID'];
                                    $data_arr['salesPerson']=$sale['salesPersonID'].' | '+$sale['SalesPersonName'];
                                }

                                if($detail['tableColumnName']=='invoiceDate'){
                                    $bookingDate = input_format_date(trim($detail['value'] ?? ''), $date_format_policy);
                                    //$newDate = date('Y-m-d' , strtotime($detail['value']));
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];


                                    $this->db->SELECT("*");
                                    $this->db->FROM('srp_erp_companyfinanceyear');
                                    $this->db->WHERE('companyID', $companyID);
                                    $this->db->where("'{$bookingDate}' BETWEEN beginingDate AND endingDate");
                                    $this->db->where('isActive',1);
                                    $this->db->where('isClosed',0);
                                    $financeYearDetails = $this->db->get()->row_array();

                                    $data_arr['financeyear']=$financeYearDetails['companyFinanceYearID'];


                                    $this->db->SELECT("*");
                                    $this->db->FROM('srp_erp_companyfinanceperiod');
                                    $this->db->WHERE('companyID', $companyID);
                                    $this->db->where("'{$bookingDate}' BETWEEN dateFrom AND dateTo");
                                    $financePeriodDetails = $this->db->get()->row_array();

                                    $data_arr['financeyear_period']=$financePeriodDetails['companyFinancePeriodID'];
                                    // print_r( $financePeriodDetails );exit;

                                    $convertFormat=convert_date_format_sql();
                                    $this->db->select('companyFinanceYearID,DATE_FORMAT(beginingDate,\''.$convertFormat.'\') AS dateFrom,DATE_FORMAT(endingDate,\''.$convertFormat.'\') AS dateTo ');
                                    $this->db->from('srp_erp_companyfinanceyear');
                                    $this->db->where('companyFinanceYearID',$financeYearDetails['companyFinanceYearID']);
                                    $this->db->where('isActive',1);
                                    $this->db->where('isClosed',0);
                                    $y=$this->db->get()->row_array();
        
                                    $data_arr['companyFinanceYear']=$y['dateFrom'].' - '.$y['dateTo'];
                                }

                                if($detail['tableColumnName']=='customerInvoiceDate'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='invoiceDueDate'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                $data_arr['invoiceType']='DirectIncome';
                                $data_arr['header_post_url']='Invoices/save_invoice_header';
                                $data_arr['detail_post_url']='Invoices/save_direct_invoice_detail';                  

                                if($detail['tableColumnName']=='referenceNo'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='contactPersonName'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='contactPersonNumber'){
                                // $invoiceDate = input_format_date(trim($detail['value'] ?? ''), $date_format_policy);
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='invoiceNote'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='invoiceNarration'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='supplyDate'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='isPrintDN'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }
                            }

                            $data_arr['invoice_details']=$details_arr;

                            $line[]=$data_arr;
                        
                        }

                    }

                    // Employee Master
                    if($master['documentID']=='EM'){

                        $emp_detailstab_data=[];

                        if($migration_header_detailsx){

                            $details_arr_contact=[];
                            $details_arr_employment=[];
                            $details_arr_designation=[];
                            $details_arr_department=[];
                            $details_arr_bank=[];
                            $details_arr_ref_mg=[];
                            $details_arr_leave_group=[];
                            foreach($migration_header_detailsx as $detail_arr){

                                //update employe contact details
                                $details_arr_contact['details_url']='Employee/contactDetails_update';
                                if($detail_arr['tableColumnName']=='ep_address1'){
                                    
                                    $details_arr_contact[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='ep_address2'){
                                    
                                    $details_arr_contact[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='ep_address3'){
                                    
                                    $details_arr_contact[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='ep_address4'){
                                    
                                    $ep_address4 = $this->db->query("SELECT countryID,CountryDes FROM srp_countrymaster WHERE CountryDes='" . $detail_arr['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $details_arr_contact[$detail_arr['tableColumnName']]=$ep_address4['countryID'];
                                    $details_arr_contact['ec_address4']=$ep_address4['countryID'];
                                }

                                if($detail_arr['tableColumnName']=='personalEmail'){
                                    
                                    $details_arr_contact[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='emp_mobile'){
                                    
                                    $details_arr_contact[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                //update employment details
                                $details_arr_employment['details_url']='Employee/save_employmentData_envoy';

                                if($detail_arr['tableColumnName']=='empDoj'){
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='dateAssumed'){
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='employeeConType'){
                                    
                                    $employeeConType = $this->db->query("SELECT EmpContractTypeID, Description FROM srp_empcontracttypes WHERE Description='" . $detail_arr['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$employeeConType['EmpContractTypeID'];
                                }

                                if($detail_arr['tableColumnName']=='empCurrency'){

                                    $empCurrency=$this->db->query("SELECT hTB.currencyID,detTB.CurrencyCode,detTB.CurrencyName
                                    FROM  srp_erp_currencymaster detTB
                                    JOIN srp_erp_companycurrencyassign hTB ON hTB.currencyID=detTB.currencyID
                                    WHERE hTB.companyID = {$companyID} AND hTB.CurrencyCode='" . $detail_arr['value'] . "' " )->row_array();
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$empCurrency['currencyID'];
                                }

                                if($detail_arr['tableColumnName']=='empSegment'){
                                    
                                    $empSegment = $this->db->query("SELECT segmentCode,description,segmentID FROM srp_erp_segment WHERE segmentCode='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$empSegment['segmentID'];
                                }

                                if($detail_arr['tableColumnName']=='probationPeriod'){
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='isPayrollEmployee'){

                                    if($detail_arr['value']==1){
                                        $ispayroll=1;
                                    }else{
                                        $ispayroll=0;
                                    }
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$ispayroll;
                                }

                                if($detail_arr['tableColumnName']=='pass_portNo'){
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='passPort_expiryDate'){
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='visa_expiryDate'){
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='airport_destination'){
                                    
                                    $details_arr_employment[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }


                                //set emp designation details
                                $details_arr_designation['details_url']='Employee/save_empDesignations';
                                if($detail_arr['tableColumnName']=='designationID'){
                                    
                                    $designationID = $this->db->query("SELECT DesignationID,DesDescription FROM srp_designation WHERE DesDescription='" . $detail_arr['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $details_arr_designation[$detail_arr['tableColumnName']]=$designationID['DesignationID'];
                                }

                                if($detail_arr['tableColumnName']=='startDate'){
                                    
                                    $details_arr_designation[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                    $details_arr_designation['isMajor']=1;
                                }

                                //set emp departments
                                $details_arr_departmrnt['details_url']='Employee/save_empDepartments';
                                if($detail_arr['tableColumnName']=='items'){
                                    
                                    $items = $this->db->query("SELECT DepartmentMasterID, DepartmentDes FROM srp_departmentmaster WHERE DepartmentDes='" . $detail_arr['value'] . "' AND Erp_companyID = {$companyID} AND isActive=1")->row_array();
                                    $data_b=[];
                                    $data_b[]=$items['DepartmentMasterID'];
                                    $details_arr_departmrnt['items']=$data_b;
                                }

                                //set emp bank details
                                $details_arr_bank['details_url']='Employee/save_empBankAccounts';
                                if($detail_arr['tableColumnName']=='bank_id'){
                                    
                                    $bank_id = $this->db->query("SELECT bankID, bankCode, bankName, bankSwiftCode FROM srp_erp_pay_bankmaster WHERE bankName='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $details_arr_bank['bank_id']=$bank_id['bankID'];
                                }

                                if($detail_arr['tableColumnName']=='branch_id'){
                                  
                                    $branch_id = $this->db->query("SELECT branchID, branchCode, branchName FROM srp_erp_pay_bankbranches WHERE branchName='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $details_arr_bank['branch_id']=$branch_id['branchID'];
                                }

                                if($detail_arr['tableColumnName']=='accHolder'){
                                    
                                    $details_arr_bank[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                   
                                }

                                if($detail_arr['tableColumnName']=='accountNo'){
                                    
                                    $details_arr_bank[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                   
                                }

                                if($detail_arr['tableColumnName']=='salPerc'){
                                    
                                    $details_arr_bank[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                   
                                }

                                if($detail_arr['tableColumnName']=='payrollType'){
                                    $data=[];
                                    $data[]=$detail_arr['value'];
                                    
                                    $details_arr_bank['payrollType']=$data;
                                   
                                }

                                // emp reporting manager

                                if($detail_arr['tableColumnName']=='managerID'){
                                    $data_mg = $this->db->query("SELECT EIdNo, Ename1, CONCAT(ECode,' _ ', Ename2) AS nameWithCode
                                    FROM srp_employeesdetails WHERE Erp_companyID={$companyID} AND ECode='" . $detail_arr['value'] . "'
                                    AND
                                    empConfirmedYN=1 AND isDischarged=0 
                                    
                                    ")->row_array();

                                    $details_arr_ref_mg['managerID']=$data_mg['EIdNo'];
                                    $details_arr_ref_mg['details_url']='Employee/save_reportingManager';
                                }

                                //emp leave group 
                                if($detail_arr['tableColumnName']=='leaveGroupID'){
                                    $data_leave = $this->db->query("SELECT leaveGroupID,description
                                    FROM srp_erp_leavegroup WHERE companyID={$companyID} AND description='" . $detail_arr['value'] . "'")->row_array();

                                    $details_arr_leave_group['leaveGroupID']=$data_leave['leaveGroupID'];
                                    $details_arr_leave_group['details_url']='Employee/save_attendanceData';
                                }

                            }

                            $emp_detailstab_data[]=$details_arr_contact;
                            $emp_detailstab_data[]=$details_arr_employment;
                            $emp_detailstab_data[]=$details_arr_designation;
                            $emp_detailstab_data[]=$details_arr_departmrnt;
                            $emp_detailstab_data[]=$details_arr_bank;
                            $emp_detailstab_data[]=$details_arr_ref_mg;
                            $emp_detailstab_data[]=$details_arr_leave_group;
                        
                            
                        }

                        if($migration_header_details_lineIDs){

                            $migration_header_details=$this->db->query("SELECT detTB.value,detTB.excelLineID ,detTB.headerReferenceID,hTB.documentID,hTB.noOfRecords,confTB.tempColoumnName,confTB.sortOrder,confTB.tableColumnName
                            FROM  srp_erp_migration_header_details detTB
                            JOIN srp_erp_migration_header hTB ON hTB.migrationHeaderMasterID=detTB.migrationHeaderMasterID
                            JOIN srp_erp_migration_config confTB ON confTB.migrationConfigID=detTB.migrationConfMasterID
                            WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND confTB.isheader=1 AND
                            detTB.excelLineID ={$migration_header_details_lineIDs[0]['excelLineID']} order by confTB.sortOrder asc" )->result_array();

                            $data_arr=[];

                            foreach($migration_header_details as $detail){

                                if($detail['tableColumnName']=='EmpSecondaryCode'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='emp_title'){
                                    $isExist = $this->db->query("SELECT * FROM srp_titlemaster WHERE Erp_companyID={$companyID} AND TitleDescription='" . $detail['value'] . "' ")->row_array();

                                    if($isExist){
                                        $data_arr[$detail['tableColumnName']]=$isExist['TitleID'];
                                    }else{
                                        $data = array(
                                            'TitleDescription' => $detail['value'],
                                            'SchMasterId' => current_schMasterID(),
                                            'branchID' => current_schBranchID(),
                                            'Erp_companyID' => current_companyID(),
                                            'CreatedPC' => current_pc(),
                                            'CreatedUserName' => current_employee(),
                                            'CreatedDate' => current_date()
                                        );
                            
                                        $this->db->insert('srp_titlemaster', $data);

                                        if ($this->db->affected_rows() > 0) {
                                            $titleID = $this->db->insert_id();

                                            $data_arr[$detail['tableColumnName']]=$titleID;
                                    
                                        }

                                    }
                                   
                                }

                                if($detail['tableColumnName']=='shortName'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                    $data_arr['Ename4']=$detail['value'];
                                }

                                if($detail['tableColumnName']=='initial'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                    
                                }

                                if($detail['tableColumnName']=='fullName'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                    
                                }

                                if($detail['tableColumnName']=='Ename3'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                    
                                }

                                if($detail['tableColumnName']=='emp_gender'){

                                    if($detail['value'] =='Male'){
                                        $data_arr[$detail['tableColumnName']]=1;
                                    }else{
                                        $data_arr[$detail['tableColumnName']]=0;
                                    }
                                    
                                }

                                if($detail['tableColumnName']=='Nationality'){
                                    
                                    $Nationality = $this->db->query("SELECT NId,Nationality FROM srp_nationality WHERE Nationality='" . $detail['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$Nationality['NId'];
                                    
                                }

                                if($detail['tableColumnName']=='religion'){
                                    
                                    $religion = $this->db->query("SELECT RId,Religion FROM srp_religion WHERE Religion='" . $detail['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$religion['RId'];
                                    
                                }

                                if($detail['tableColumnName']=='MaritialStatus'){
                                    
                                    $MaritialStatus = $this->db->query("SELECT maritialstatusID FROM srp_erp_maritialstatus WHERE description='" . $detail['value'] . "'")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$MaritialStatus['maritialstatusID'];
                                    
                                }

                                if($detail['tableColumnName']=='empDob'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='BloodGroup'){
                                    
                                    $BloodGroup = $this->db->query("SELECT BloodTypeID,BloodDescription FROM srp_erp_bloodgrouptype WHERE BloodDescription='" . $detail['value'] . "'")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$BloodGroup['BloodTypeID'];
                                    
                                } 

                                if($detail['tableColumnName']=='emp_email'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='NIC'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='confirmDate'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                $data_arr['invoiceType']='EM';
                                $data_arr['header_post_url']='Employee/new_employee';
                               
                            }

                            $data_arr['emp_details']=$emp_detailstab_data;

                            $line[]=$data_arr;
                        
                        }

                    }

                    //customer master
                    if($master['documentID']=='CM'){

                        $data_arr=[];

                        
                        
                        if($migration_header_detailsx){

                            foreach($migration_header_detailsx as $detail_arr){
                                // receivableAccount
                                if($detail_arr['tableColumnName']=='receivableSystemGLCode'){

                                    $details_arr_GLCode = $detail_arr['value'];
                                   
                                    $this->db->SELECT("*");
                                        $this->db->FROM('srp_erp_chartofaccounts');
                                        $this->db->WHERE('systemAccountCode', $details_arr_GLCode);
                                        $this->db->where('companyID', current_companyID());
                                    $GL = $this->db->get()->row_array();

                                    $data_arr['receivableAccount']= trim($GL['GLAutoID'] ?? '');
                                        
                                    //print_r($data_arr['receivableAccount']); exit; 
                                    /*
                                    $sect = explode('|', $detail_arr['value']);
                                    $sections = array_map('trim', $sect);
                                    $receivableSystemGLCode = $sections[0];
                                    $receivableGLAccount = $sections[1];
                                    $receivableDescription = $sections[2];
                                    $receivableType = $sections[3];

                                    $data_arr = [ 
                                        'receivableSystemGLCode'=> $receivableSystemGLCode,                             
                                        'receivableGLAccount'=> $receivableGLAccount,                              
                                        'receivableDescription'=> $receivableDescription,                              
                                        'receivableType'=> $receivableType
                                        ];
                                    */
                                }
                                //currency
                                if($detail_arr['tableColumnName']=='customerCurrency'){
                                    
                                    $customerCurrency = $detail_arr['value'];
                                   
                                    $this->db->SELECT("*");
                                        $this->db->FROM('srp_erp_currencymaster');
                                        $this->db->WHERE('CurrencyCode', $customerCurrency);
                                    $data = $this->db->get()->row_array();
                                    $currency = $data['CurrencyCode'] . '|' . $data['CurrencyName'];
                                   
                                    $data_arr['currency_code']= trim($currency);
                                }
                                //country
                                if($detail_arr['tableColumnName']=='customerCountry'){  
                                    $customerCountry = $detail_arr['value'];
                                    $data_arr['customercountry']= trim($customerCountry);
                                }
                                //tax group
                                if($detail_arr['tableColumnName']=='taxGroup'){    
                                    $customertaxGroup = $this->db->query("SELECT taxGroupID, Description FROM srp_erp_taxgroup WHERE Description='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    $details_arr_taxGroup = $customertaxGroup['taxGroupID'];
                                    $data_arr['taxGroupID'] = $details_arr_taxGroup;
                                }
                                //fax
                                if($detail_arr['tableColumnName']=='customerFax'){
                                    $details_arr_fax = $detail_arr['value'];
                                    $data_arr[$detail_arr['tableColumnName']] = $details_arr_fax;    
                                }
                                //url
                                if($detail_arr['tableColumnName']=='customerUrl'){
                                    $details_arr_url = $detail_arr['value'];
                                    $data_arr[$detail_arr['tableColumnName']]= $details_arr_url;    
                                }
                                //pushToVendor
                                if($detail_arr['tableColumnName']=='isSync'){
                                    $details_arr_vendor = $detail_arr['value'];
                                    $data_arr[$detail_arr['tableColumnName']] = $details_arr_vendor;   
                                }
                                //vatPercentage
                                if($detail_arr['tableColumnName']=='vatPercentage'){    
                                    $details_arr_percentage = $detail_arr['value'];
                                    $data_arr[$detail_arr['tableColumnName']] = $details_arr_percentage;   
                                }
                                //group to
                                if($detail_arr['tableColumnName']=='groupTo'){
                                    $name = $detail_arr['value'];
                                    $this->db->SELECT("customerAutoID");
                                    $this->db->FROM('srp_erp_customermaster');
                                    $this->db->WHERE('customerName', $name);
                                    $this->db->where('masterID', null);
                                    $this->db->where('isActive', 1);
                                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                                    $data = $this->db->get()->result_array();
                                        if ($data) {
                                            foreach ($data as $row) {
                                                $data_arr['masterID'] = trim($row['customerAutoID'] ?? '');
                                            }
                                        }
                                }


                            }
                        
                        }

                        if($migration_header_details_lineIDs){

                            $migration_header_details=$this->db->query("SELECT detTB.value,detTB.excelLineID ,detTB.headerReferenceID,hTB.documentID,hTB.noOfRecords,confTB.tempColoumnName,confTB.sortOrder,confTB.tableColumnName
                            FROM  srp_erp_migration_header_details detTB
                            JOIN srp_erp_migration_header hTB ON hTB.migrationHeaderMasterID=detTB.migrationHeaderMasterID
                            JOIN srp_erp_migration_config confTB ON confTB.migrationConfigID=detTB.migrationConfMasterID
                            WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND confTB.isheader=1 AND
                            detTB.excelLineID ={$migration_header_details_lineIDs[0]['excelLineID']} order by confTB.sortOrder asc" )->result_array();

                            foreach($migration_header_details as $detail){

                                //secondary code
                                if($detail['tableColumnName']=='secondaryCode'){
                                    $data_arr['customercode'] = trim($detail['value'] ?? '');
                                    //  $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }
                                //customer name
                                if($detail['tableColumnName']=='customerName'){    
                                    $data_arr[$detail['tableColumnName']]=trim($detail['value'] ?? '');
                                }
                                // Category
                                if($detail['tableColumnName']=='partyCategoryID'){ 
                                    $category = $detail['value'];
                                   
                                    $this->db->SELECT("*");
                                        $this->db->FROM('srp_erp_partycategories');
                                        $this->db->WHERE('categoryDescription', $category);
                                        $this->db->where('companyID', current_companyID());
                                    $data = $this->db->get()->row_array();

                                    $data_arr['receivableAccount']= trim($data['partyCategoryID'] ?? ''); 

                                }
                                //idCardNo
                                if($detail['tableColumnName']=='IdCardNumber'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];   
                                }
                                //telephone
                                if($detail['tableColumnName']=='customerTelephone'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }
                                //email
                                if($detail['tableColumnName']=='customerEmail'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }
                                //creit period
                                if($detail['tableColumnName']=='customerCreditPeriod'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }
                                //creit Limit
                                if($detail['tableColumnName']=='customerCreditLimit'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }
                                //vatEligible
                                if($detail['tableColumnName']=='vatEligible'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }
                                //vatIdNo
                                if($detail['tableColumnName']=='vatIdNo'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }
                                //isActive
                                if($detail['tableColumnName']=='isActive'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }
                                //rebatePercentage
                                if($detail['tableColumnName']=='rebatePercentage'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }
                                //rebatePercentage
                                if($detail['tableColumnName']=='customerAddress1'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }
                                //rebatePercentage
                                if($detail['tableColumnName']=='customerAddress2'){    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];    
                                }

                                $data_arr['invoiceType']='CM';
                                $data_arr['header_post_url']='Customer/new_customer';
   
                            }

                            $line[]=$data_arr;
                        
                        }    
                    }
                }
            }
            
            return $line;

        }

    }

    function fetch_excel_upload_migration_details_edit()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $migrationHeaderMasterID = trim($this->input->post('migrationHeaderMasterID') ?? '');
        $companyID = current_companyID();
        $currency_decimal=$this->common_data['company_data']['company_reporting_decimal'];

        $this->db->select('*');
        $this->db->from('srp_erp_migration_header');
        $this->db->where('migrationHeaderMasterID', trim($migrationHeaderMasterID));
        $master = $this->db->get()->row_array();

       // $date_format_policy = date_format_policy();

        if($master){

            $migration_header_details_refIDs=$this->db->query("SELECT detTB.headerReferenceID
                                        FROM  srp_erp_migration_header_details detTB
                                        WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID}
                                        GROUP BY  detTB.headerReferenceID" )->result_array();
            
            //print_r( $migration_header_details);exit;
            $line =[];

            if (!empty($migration_header_details_refIDs)) {
                foreach ($migration_header_details_refIDs as $key=>$row) {

                    $migration_header_details_lineIDs=$this->db->query("SELECT detTB.excelLineID
                    FROM  srp_erp_migration_header_details detTB
                    WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND detTB.headerReferenceID ={$row['headerReferenceID']}
                    GROUP BY  detTB.excelLineID" )->result_array();

                    

                    ///arrange details array

                   $migration_header_detailsx=$this->db->query("SELECT detTB.value,detTB.excelLineID ,detTB.headerReferenceID,hTB.documentID,hTB.noOfRecords,confTB.tempColoumnName,confTB.sortOrder,confTB.tableColumnName
                   FROM  srp_erp_migration_header_details detTB
                   JOIN srp_erp_migration_header hTB ON hTB.migrationHeaderMasterID=detTB.migrationHeaderMasterID
                   JOIN srp_erp_migration_config confTB ON confTB.migrationConfigID=detTB.migrationConfMasterID
                   WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND confTB.isheader=0 AND
                   detTB.headerReferenceID ={$row['headerReferenceID']} order by confTB.sortOrder asc" )->result_array();

                    // Employee Master
                    if($master['documentID']=='EM'){

                        $emp_detailstab_data=[];
                        $data_arr=[];

                        if($migration_header_detailsx){

                            $details_arr_contact=[];
                            $details_arr_employment=[];
                            $details_arr_designation=[];
                            $details_arr_department=[];
                            $details_arr_bank=[];
                            $details_arr_ref_mg=[];
                            $details_arr_leave_group=[];
                            foreach($migration_header_detailsx as $detail_arr){
                               
                                //update employe contact details
                                $details_arr_contact['details_url']='Employee/contactDetails_update';
                                if($detail_arr['tableColumnName']=='ep_address1'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='ep_address2'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='ep_address3'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='ep_address4'){
                                    
                                    $ep_address4 = $this->db->query("SELECT countryID,CountryDes FROM srp_countrymaster WHERE CountryDes='" . $detail_arr['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$ep_address4['countryID'];
                                    $data_arr['ec_address4']=$ep_address4['countryID'];
                                }

                                if($detail_arr['tableColumnName']=='personalEmail'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='emp_mobile'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                //update employment details
                                $details_arr_employment['details_url']='Employee/save_employmentData_envoy';

                                if($detail_arr['tableColumnName']=='empDoj'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='dateAssumed'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='employeeConType'){
                                    
                                    $employeeConType = $this->db->query("SELECT EmpContractTypeID, Description FROM srp_empcontracttypes WHERE Description='" . $detail_arr['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$employeeConType['EmpContractTypeID'];
                                }

                                if($detail_arr['tableColumnName']=='empCurrency'){

                                    $empCurrency=$this->db->query("SELECT hTB.currencyID,detTB.CurrencyCode,detTB.CurrencyName
                                    FROM  srp_erp_currencymaster detTB
                                    JOIN srp_erp_companycurrencyassign hTB ON hTB.currencyID=detTB.currencyID
                                    WHERE hTB.companyID = {$companyID} AND hTB.CurrencyCode='" . $detail_arr['value'] . "' " )->row_array();
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$empCurrency['currencyID'];
                                }

                                if($detail_arr['tableColumnName']=='empSegment'){
                                    
                                    $empSegment = $this->db->query("SELECT segmentCode,description,segmentID FROM srp_erp_segment WHERE segmentCode='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$empSegment['segmentID'];
                                }

                                if($detail_arr['tableColumnName']=='probationPeriod'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='isPayrollEmployee'){

                                    if($detail_arr['value']==1){
                                        $ispayroll=1;
                                    }else{
                                        $ispayroll=2;
                                    }
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$ispayroll;
                                }

                                if($detail_arr['tableColumnName']=='pass_portNo'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='passPort_expiryDate'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='visa_expiryDate'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }

                                if($detail_arr['tableColumnName']=='airport_destination'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                }


                                //set emp designation details
                                $details_arr_designation['details_url']='Employee/save_empDesignations';
                                if($detail_arr['tableColumnName']=='designationID'){
                                    
                                    $designationID = $this->db->query("SELECT DesignationID,DesDescription FROM srp_designation WHERE DesDescription='" . $detail_arr['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$designationID['DesignationID'];
                                }

                                if($detail_arr['tableColumnName']=='startDate'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                    $data_arr['isMajor']=1;
                                }

                                //set emp departments
                                $details_arr_departmrnt['details_url']='Employee/save_empDepartments';
                                if($detail_arr['tableColumnName']=='items'){
                                    
                                    $items = $this->db->query("SELECT DepartmentMasterID, DepartmentDes FROM srp_departmentmaster WHERE DepartmentDes='" . $detail_arr['value'] . "' AND Erp_companyID = {$companyID} AND isActive=1")->row_array();
                                    // $data_b=[];
                                    // $data_b[]=$items['DepartmentMasterID'];
                                    $data_arr['items']=$items['DepartmentMasterID'];
                                }

                                //set emp bank details
                                $details_arr_bank['details_url']='Employee/save_empBankAccounts';
                                if($detail_arr['tableColumnName']=='bank_id'){
                                    
                                    $bank_id = $this->db->query("SELECT bankID, bankCode, bankName, bankSwiftCode FROM srp_erp_pay_bankmaster WHERE bankName='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr['bank_id']=$bank_id['bankID'];
                                }

                                if($detail_arr['tableColumnName']=='branch_id'){
                                  
                                    $branch_id = $this->db->query("SELECT branchID, branchCode, branchName FROM srp_erp_pay_bankbranches WHERE branchName='" . $detail_arr['value'] . "' AND companyID = {$companyID}")->row_array();
                                    
                                    $data_arr['branch_id']=$branch_id['branchID'];
                                }

                                if($detail_arr['tableColumnName']=='accHolder'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                   
                                }

                                if($detail_arr['tableColumnName']=='accountNo'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                   
                                }

                                if($detail_arr['tableColumnName']=='salPerc'){
                                    
                                    $data_arr[$detail_arr['tableColumnName']]=$detail_arr['value'];
                                   
                                }

                                if($detail_arr['tableColumnName']=='payrollType'){
                                    // $data=[];
                                    // $data[]=$detail_arr['value'];
                                    
                                    $data_arr['payrollType']=$detail_arr['value'];
                                   
                                }

                                // emp reporting manager

                                if($detail_arr['tableColumnName']=='managerID'){
                                    $data_mg = $this->db->query("SELECT EIdNo, Ename1, CONCAT(ECode,' _ ', Ename2) AS nameWithCode
                                    FROM srp_employeesdetails WHERE Erp_companyID={$companyID} AND ECode='" . $detail_arr['value'] . "'
                                    AND
                                    empConfirmedYN=1 AND isDischarged=0 
                                    
                                    ")->row_array();

                                    $data_arr['managerID']=$data_mg['EIdNo'];
                                    $data_arr['details_url']='Employee/save_reportingManager';
                                }

                                //emp leave group 
                                if($detail_arr['tableColumnName']=='leaveGroupID'){
                                    $data_leave = $this->db->query("SELECT leaveGroupID,description
                                    FROM srp_erp_leavegroup WHERE companyID={$companyID} AND description='" . $detail_arr['value'] . "'")->row_array();

                                    $data_arr['leaveGroupID']=$data_leave['leaveGroupID'];
                                    $data_arr['details_url']='Employee/save_attendanceData';
                                }

                            }

                            $emp_detailstab_data[]=$details_arr_contact;
                            $emp_detailstab_data[]=$details_arr_employment;
                            $emp_detailstab_data[]=$details_arr_designation;
                            $emp_detailstab_data[]=$details_arr_departmrnt;
                            $emp_detailstab_data[]=$details_arr_bank;
                            $emp_detailstab_data[]=$details_arr_ref_mg;
                            $emp_detailstab_data[]=$details_arr_leave_group;
                        
                            
                        }

                        if($migration_header_details_lineIDs){

                            $migration_header_details=$this->db->query("SELECT detTB.value,detTB.excelLineID ,detTB.headerReferenceID,hTB.documentID,hTB.noOfRecords,confTB.tempColoumnName,confTB.sortOrder,confTB.tableColumnName
                            FROM  srp_erp_migration_header_details detTB
                            JOIN srp_erp_migration_header hTB ON hTB.migrationHeaderMasterID=detTB.migrationHeaderMasterID
                            JOIN srp_erp_migration_config confTB ON confTB.migrationConfigID=detTB.migrationConfMasterID
                            WHERE detTB.migrationHeaderMasterID = {$migrationHeaderMasterID} AND confTB.isheader=1 AND
                            detTB.excelLineID ={$migration_header_details_lineIDs[0]['excelLineID']} order by confTB.sortOrder asc" )->result_array();

                           

                            foreach($migration_header_details as $detail){
                                $data_arr['excelLineID']=$detail['excelLineID'];
                                if($detail['tableColumnName']=='EmpSecondaryCode'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='emp_title'){
                                    $isExist = $this->db->query("SELECT * FROM srp_titlemaster WHERE Erp_companyID={$companyID} AND TitleDescription='" . $detail['value'] . "' ")->row_array();

                                    if($isExist){
                                        $data_arr[$detail['tableColumnName']]=$isExist['TitleID'];
                                    }else{
                                        $data = array(
                                            'TitleDescription' => $detail['value'],
                                            'SchMasterId' => current_schMasterID(),
                                            'branchID' => current_schBranchID(),
                                            'Erp_companyID' => current_companyID(),
                                            'CreatedPC' => current_pc(),
                                            'CreatedUserName' => current_employee(),
                                            'CreatedDate' => current_date()
                                        );
                            
                                        $this->db->insert('srp_titlemaster', $data);

                                        if ($this->db->affected_rows() > 0) {
                                            $titleID = $this->db->insert_id();

                                            $data_arr[$detail['tableColumnName']]=$titleID;
                                    
                                        }

                                    }
                                   
                                }

                                if($detail['tableColumnName']=='shortName'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                    $data_arr['Ename4']=$detail['value'];
                                }

                                if($detail['tableColumnName']=='initial'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                    
                                }

                                if($detail['tableColumnName']=='fullName'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                    
                                }

                                if($detail['tableColumnName']=='Ename3'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                    
                                }

                                if($detail['tableColumnName']=='emp_gender'){

                                    if($detail['value'] =='Male'){
                                        $data_arr[$detail['tableColumnName']]=1;
                                    }else{
                                        $data_arr[$detail['tableColumnName']]=2;
                                    }
                                    
                                }

                                if($detail['tableColumnName']=='Nationality'){
                                    
                                    $Nationality = $this->db->query("SELECT NId,Nationality FROM srp_nationality WHERE Nationality='" . $detail['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$Nationality['NId'];
                                    
                                }

                                if($detail['tableColumnName']=='religion'){
                                    
                                    $religion = $this->db->query("SELECT RId,Religion FROM srp_religion WHERE Religion='" . $detail['value'] . "' AND Erp_companyID = {$companyID}")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$religion['RId'];
                                    
                                }

                                if($detail['tableColumnName']=='MaritialStatus'){
                                    
                                    $MaritialStatus = $this->db->query("SELECT maritialstatusID FROM srp_erp_maritialstatus WHERE description='" . $detail['value'] . "'")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$MaritialStatus['maritialstatusID'];
                                    
                                }

                                if($detail['tableColumnName']=='empDob'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='BloodGroup'){
                                    
                                    $BloodGroup = $this->db->query("SELECT BloodTypeID,BloodDescription FROM srp_erp_bloodgrouptype WHERE BloodDescription='" . $detail['value'] . "'")->row_array();
                                    
                                    $data_arr[$detail['tableColumnName']]=$BloodGroup['BloodTypeID'];
                                    
                                } 

                                if($detail['tableColumnName']=='emp_email'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='NIC'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                if($detail['tableColumnName']=='confirmDate'){
                                    
                                    $data_arr[$detail['tableColumnName']]=$detail['value'];
                                }

                                $data_arr['invoiceType']='EM';
                                $data_arr['header_post_url']='Employee/new_employee';

                                $data_arr['migrationHeaderMasterID']=$migrationHeaderMasterID;
                               
                            }

                           // $data_arr['emp_details']=$emp_detailstab_data;

                            $line[]=$data_arr;
                        
                        }

                    }
                    
                }

            }

            //print_r($line);exit;

            if(count($line>0)){
                $date_format_policy = date_format_policy();
                
                $this->db->where('migrationHeaderMasterID', $migrationHeaderMasterID);
                $result=$this->db->delete('srp_employeesdetails_migration_temp');
                foreach($line as $val){

                    //$dob = input_format_date( $val['empDob'], $date_format_policy);

                    $data = array(
                        'EmpTitleId' => $val['emp_title'],
                        'Ename1' => $val['fullName'],
                        'EmpShortCode' => $val['shortName'],
                        'Gender' => $val['emp_gender'],
                        'EEmail' => $val['emp_email'],
                        'EDOB' =>input_format_date( $val['empDob'], $date_format_policy),
                        'rid' => $val['religion'],
                        
                        //'SchMasterId' => current_schMasterID(),
                       // 'branchID' => current_schBranchID(),
                        'Erp_companyID' => $companyID,
                        'CreatedPC' => current_pc(),
                        'CreatedUserName' => current_employee(),
                        'CreatedDate' => current_date(),
                        'EmpSecondaryCode' => $val['EmpSecondaryCode'],
                        'Nid' => $val['Nationality'],
                        'MaritialStatus' => $val['MaritialStatus'],
                        'BloodGroup' => $val['BloodGroup'],
                        'UserName' => $val['emp_email'],
                        'Password' => md5('Welcome@123'),
                        'Ename2' => $val['initial'].' '.$val['Ename4'],
                        'Ename3' => $val['Ename3'],
                        'initial' => $val['initial'],
                        'Ename4' => $val['Ename4'],
                        'NIC' => $val['NIC'],
                        'empConfirmDate' => input_format_date(  $val['confirmDate'], $date_format_policy),
                        'migrationHeaderMasterID'=>$val['migrationHeaderMasterID'],

                        //contact details
                        'EpAddress1' => $val['ep_address1'], 
                        'EpAddress2' => $val['ep_address2'], 
                        'EpAddress3' => $val['ep_address3'], 
                        'EpAddress4' => $val['ep_address4'],
                        'EcAddress4' => $val['ep_address4'],
                        'personalEmail' => $val['personalEmail'],
                        'EcMobile' => $val['emp_mobile'],

                        //employement details

                        'EDOJ' => input_format_date( $val['empDoj'], $date_format_policy),
                        'DateAssumed' => input_format_date( $val['dateAssumed'], $date_format_policy),
                        'EmployeeConType' => $val['employeeConType'],
                        'payCurrencyID' => $val['empCurrency'],
                        'segmentID' =>$val['empSegment'], 
                        'probationPeriod' => input_format_date( $val['probationPeriod'], $date_format_policy),
                        'isPayrollEmployee' => $val['isPayrollEmployee'],
                        'EPassportNO' =>$val['pass_portNo'], 
                        'AirportDestination' => $val['airport_destination'],
                        'EPassportExpiryDate' =>  input_format_date( $val['passPort_expiryDate'], $date_format_policy),
                        'EVisaExpiryDate' =>  input_format_date( $val['visa_expiryDate'], $date_format_policy),

                        'managerID'=>$val['managerID'],
                        'EmpDesignationId'=>$val['designationID'],
                        'startDate'=> input_format_date( $val['startDate'], $date_format_policy),
                        'departmentID'=>$val['items'],

                        //bank
                        'accHolder'=>$val['accHolder'],
                        'accountNo'=>$val['accountNo'],
                        'salPerc'=>$val['salPerc'],
                        'bank_id'=>$val['bank_id'],
                        'branch_id'=>$val['branch_id'],
                        'payrollType'=>$val['payrollType'],

                        'leaveGroupID'=>$val['leaveGroupID'],
                        'excelLineID'=>$val['excelLineID'],

                    );

                    

                 //   $this->db->trans_start();

                    $this->db->insert('srp_employeesdetails_migration_temp', $data);
                   // $empID = $this->db->insert_id();

                   
                }

            }
            $this->db->trans_complete();

           return $line;

        }
    
    }

    function fetch_header_migration_details()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $migrationHeaderMasterID = $this->input->post('migrationHeaderMasterID');

        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_migration_header');
        $this->db->WHERE('migrationHeaderMasterID', $migrationHeaderMasterID);
        $this->db->WHERE('companyID', $companyID);
        return $this->db->get()->row_array();

    }


 
    function validated_excel_data()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $migrationHeaderMasterID = $this->input->post('migrationHeaderMasterID');

       $stored_procedure = "CALL MigrationValidationProcedure(?)";
       $result = $this->db->query($stored_procedure,array('migHeaderMasterID'=>$migrationHeaderMasterID));
       

       $this->db->SELECT("*");
       $this->db->FROM('srp_erp_migration_validation_results');
       $this->db->WHERE('migrationHeaderMasterID', $migrationHeaderMasterID);
       $error_arr=$this->db->get()->result_array();

       if(count($error_arr)==0){

            $data_update['isValidated']=1;
            $this->db->WHERE('migrationHeaderMasterID', $migrationHeaderMasterID);
            $this->db->update('srp_erp_migration_header', $data_update);
       }

       $this->db->trans_complete();
        return $error_arr;

    }

    function fetch_document_type(){
        $this->db->trans_start();
        $companyID = current_companyID();
        $documentID = $this->input->post('documentID');

        $this->db->SELECT("documentType,documentTypeName");
        $this->db->FROM('srp_erp_migration_config');
        $this->db->WHERE('documentID', $documentID);
        $this->db->WHERE('companyID', $companyID);
        $this->db->where('documentType is NOT NULL', NULL, FALSE);
        $this->db->WHERE('isExcel', 1);
        $this->db->group_by('documentType');

        $data=$this->db->get()->result_array();

        if($data){
            $result =array("status"=>true,"result"=>$data);
        }else{
            $result =array("status"=>false,"result"=>[]);
        }

        return $result;
    }

    function delete_migration_recode()
    {

        $this->db->where('migrationHeaderMasterID', $this->input->post('migrationHeadeID'));
        $this->db->delete('srp_erp_migration_header_details');

        $this->db->where('migrationHeaderMasterID', $this->input->post('migrationHeadeID'));
        $this->db->delete('srp_erp_migration_header');

        $this->session->set_flashdata('s', 'Recode deleted Successfully.');

        return true;

    }

    function fetchbankBranches()
    {
        $bankID = $this->input->post('bankID');
        return $this->db->query("SELECT branchID, branchCode, branchName FROM srp_erp_pay_bankbranches WHERE bankID={$bankID} ORDER BY branchName")->result_array();
    }
}