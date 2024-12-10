<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Company extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Company_model');
        $this->load->library('s3');
    }

    function fetch_company()
    {
        $this->datatables->select('company_id,company_code as company_code,company_name as company_name,company_start_date,company_url,company_email,company_phone,company_address1,company_address2,company_city,company_province,company_postalcode,company_country,company_logo')
            ->from('srp_erp_company');
        $this->datatables->where('company_id', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('company_detail', '<h4> $1 ( $2 ) </h4>', 'company_name,company_code');
        //$this->datatables->add_column('img', "<center><img class='img-thumbnail' src='$2/$1' style='width:90px;height: 80px;'><center>", 'company_logo,base_url("images/logo/")');
        $this->datatables->add_column('img','$1', 'fetch_aws_companyimage(company_logo)');
        $this->datatables->add_column('edit', '<spsn class="pull-right"><a onclick="fetchPage(\'system/company/erp_company_configuration_new\',$1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>', 'company_id');
        echo $this->datatables->generate();
    }

    function save_company(){
        $this->form_validation->set_rules('companycode', 'Company Code', 'trim|required');
        $this->form_validation->set_rules('companyname', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('companystartdate', 'Company Start Date', 'trim|required');
        //$this->form_validation->set_rules('companyurl', 'Company URL', 'trim|required');
        //$this->form_validation->set_rules('companyemail', 'Company Email', 'trim|required');
        //$this->form_validation->set_rules('companyphone', 'Company Phone', 'trim|required');
        $this->form_validation->set_rules('companyaddress1', 'Company Address 1', 'trim|required');
        $this->form_validation->set_rules('companyaddress2', 'Company Address 2', 'trim|required');
        $this->form_validation->set_rules('companycity', 'Company City', 'trim|required');
        //$this->form_validation->set_rules('companyprovince', 'Company Province', 'trim|required');
        $this->form_validation->set_rules('companypostalcode', 'Company Postal Code', 'trim|required');
        $this->form_validation->set_rules('companycountry', 'Company Country', 'trim|required');
        //$this->form_validation->set_rules('default_segment', 'Default Segment', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Company_model->save_company_master());
        }
    }

    function save_company_control_accounts(){
        $this->form_validation->set_rules('APA', 'Accounts Payable', 'trim|required');
        $this->form_validation->set_rules('ARA', 'Accounts Receivable', 'trim|required');
        $this->form_validation->set_rules('INVA', 'Inventory Control', 'trim|required');
        $this->form_validation->set_rules('ACA', 'Asset Control Account', 'trim|required');
        $this->form_validation->set_rules('PCA', 'Payroll Control Account', 'trim|required');
        $this->form_validation->set_rules('UGRV', 'Unbilled GRV', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Company_model->save_company_control_accounts());
        }
    }

    function load_company_header()
    {
        $data['detail'] = $this->Company_model->load_company_header();
        $company_id = $this->input->post('companyid');
        $data['supportToken'] = $this->Company_model->getSupportToken($company_id);
        $data['noimage'] = $this->s3->createPresignedRequest('images/item/no-image.png', '1 hour');
        $data['logo'] = $this->s3->createPresignedRequest('images/logo/'.$data['detail']['company_logo'], '1 hour');
        $data['secondarylogo']  = $this->s3->createPresignedRequest('images/item/no-image.png', '1 hour');
        if(!empty($data['detail']['company_secondary_logo'])||($data['detail']['company_secondary_logo'])!='')
        {
            $data['secondarylogo'] = $this->s3->createPresignedRequest('images/logo/'.$data['detail']['company_secondary_logo'], '1 hour');
        }


        echo json_encode($data);
    }

    function fetch_company_api_urls(){
        $data['detail'] = $this->Company_model->load_company_api_urls();
        echo json_encode($data);
    }

    function get_company_config_details()
    {
        echo json_encode($this->Company_model->get_company_config_details());
    }

    function save_state()
    {
        echo json_encode($this->Company_model->save_state());
    }

    function fetch_company_control_account()
    {
        echo json_encode($this->Company_model->fetch_company_control_account());
    }

    function save_control_account()
    {
        $this->form_validation->set_rules('controlAccountsAutoID', 'Accounts ID', 'trim|required');
        $this->form_validation->set_rules('GLSecondaryCode', 'GL Secondary Code', 'trim|required');
        $this->form_validation->set_rules('GLDescription', 'GL Description', 'trim|required');
        if($this->form_validation->run()==FALSE)
        {
            $this->session->set_flashdata($msgtype='e',validation_errors());
            echo json_encode(FALSE);
        }
        else
        { 
            echo json_encode($this->Company_model->save_control_account());
        } 
    }

    function save_chartofcontrol_account(){
        $this->form_validation->set_rules('GLDescription', 'GL Description', 'trim|required');
        $this->form_validation->set_rules('masterAccountYN', 'Is Master Account', 'trim|required');
        $this->form_validation->set_rules('GLSecondaryCode', 'Secondary Code', 'trim|required');
        $this->form_validation->set_rules('accountCategoryTypeID', 'Account Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $companyID=current_companyID();
            $GLSecondaryCode=$this->input->post('GLSecondaryCode');
            if($GLSecondaryCode !=''){
           $exit= $this->db->query("SELECT * FROM `srp_erp_chartofaccounts` WHERE companyID = {$companyID}  AND GLSecondaryCode ='{$GLSecondaryCode}' ")->row_array();
                if(!empty($exit)){
                    $this->session->set_flashdata('e', 'GL secondary code is already exist');
                    echo json_encode(FALSE);
                    exit;
                }

            }

          $masterAccount_dec=  $this->input->post('masterAccount_dec');
            if($masterAccount_dec == 'Select Master Account'){
                $this->session->set_flashdata('e', 'Please select a Master Account');
                echo json_encode(FALSE);
                exit;
            }

            echo json_encode($this->Company_model->save_chartofcontrol_account());
        }
    }

    function company_image_upload()
    {
        $this->form_validation->set_rules('faID', 'Company Id is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Company_model->company_image_upload());
        }
    }

    function fetch_company_codeMaster()
    {
        $company_id = $this->common_data['company_data']['company_id'];

        $documentMaster = $this->db->query("SELECT * FROM srp_erp_documentcodemaster JOIN `srp_erp_documentcodes` ON `srp_erp_documentcodes`.`documentID` = `srp_erp_documentcodemaster`.`documentID` WHERE companyID='{$company_id}' AND `isApprovalDocument` = 1")->result_array();

        $YN_arr = array( '1' => 'Both', '2' => 'Header', '3' => 'Footer', '0' => 'None');
        $YN_arr_SalesOrder = array( '1' => 'Both', '2' => 'Header', '3' => 'Footer', '0' => 'None', '4' => 'Setup Invoice');
        $div_arr = array('' => 'Blank', '/' => '/', '-' => '-');
        $div_prefix = array('' => 'Blank', 'prefix' => 'Prefix', 'yyyy' => 'YYYY', 'yy' => 'YY', 'mm' => 'MM');
        $div_isFYBasedSerialNo = array('0' => 'standard', '1' => 'Finance Year based');
        $div_postDate = array('0' => 'Document date', '1' => 'Approval date');
        $div_prefix_category = array('' => 'Blank', 'prefix' => 'Prefix', 'subCat' => 'subCat', 'subsubCat' => 'subsubCat', 'subsubsubCat' => 'subsubsubCat');
        //$approval_types_arr = array('1' => 'Standard', '2' => 'Standard and Amount base', '3' => 'Standard and Segment base', '4' => 'Standard & Amount base segment & Segment base');

        // $result = $this->db->query("SELECT approvalTypes FROM srp_erp_documentcodes  WHERE documentID = 'PO'")->result_array();
        // //var_dump($result[0]['approvalType']);

        // if($result[0]['approvalTypes'] == 1){
        //     $approval_types_arr = array('1' => 'Standard');
        // } else if($result[0]['approvalTypes'] == 2){
        //     $approval_types_arr = array('1' => 'Standard', '2' => 'Amount base');
        // } else if($result[0]['approvalTypes'] == 3){
        //     $approval_types_arr = array('1' => 'Standard', '2' => 'Segment base');
        // } else if($result[0]['approvalTypes'] == 4){
        //     $approval_types_arr = array('1' => 'Standard', '2' => 'Amount base', '3' => 'Segment base', '4' => 'Amount & segment base');
        // } else{
        //     //$approval_types_arr = array('0' => '');
        // }

        $data = '<table class="table table-bordered table-striped table-condesed" id="standedtbl">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 5%">Document</th>
                <th style="min-width: 5%">Approval Types</th>
                <th style="min-width: 10%">Serialization</th>
                <th style="width: 10%">Post Date</th>
                <th style="min-width: 10%">Prefix</th>
                <th style="width: 8%">Serial <br> No</th>
                <th style="width: 7%">Format Length</th>
                <th style="min-width: 10%">Format 1</th>
                <th style="min-width: 10%">Format 2</th>
                <th style="min-width: 10%">Format 3</th>
                <th style="min-width: 10%">Format 4</th>
                <th style="min-width: 10%">Format 5</th>
                <th style="min-width: 10%">Format 6</th>
                <th style="width: 4%">Approval Level</th>
                <th style="min-width: 100px">Print Header / Footer</th>
            </tr>
            </thead>
            <tbody>';
            $i= 1;
            foreach($documentMaster as $val){

                   /******* load approval types start*/
                   $result = $this->db->query("SELECT approvalTypes FROM srp_erp_documentcodes  WHERE documentID = '".$val['documentID']."' ")->result_array();

                   if($result[0]['approvalTypes'] == 1){
                       $approval_types_arr = array('1' => 'Standard');
                   } else if($result[0]['approvalTypes'] == 2){
                       $approval_types_arr = array('1' => 'Standard', '2' => 'Amount base');
                   } else if($result[0]['approvalTypes'] == 3){
                        $approval_types_arr = array('1' => 'Standard', '3' => 'Segment base');
                   } else if($result[0]['approvalTypes'] == 4){
                       $approval_types_arr = array('1' => 'Standard', '2' => 'Amount base', '3' => 'Segment base', '4' => 'Amount & segment base');
                   }else if($result[0]['approvalTypes'] == 5){
                        $approval_types_arr = array('1' => 'Standard', '2' => 'Amount base', '3' => 'Segment base', '4' => 'Amount & segment base','5' => 'Category base');
                    } else{
                       $approval_types_arr = array('0' => '');
                   }
                   /******* load approval types end*/
                   
                if($val['isFinance']==0 || $val['isFinance']==null){
                    $isFYBasedSerial="disabled";
                }else{
                    $isFYBasedSerial="";
                }
                if($val['documentID']=='DO'){
                    $isDO="";
                }else{
                    $isDO="disabled";
                }
                if($val['documentID'] == 'CINV'){
                    $data .= '<tr>
                    <td>' . $i .' </td>
                    <td>' . $val['documentID'] .' - ' . $val['document'] .' <input type="hidden" value="' . $val['codeID'] .'"  name="codeID[]"></td>
                    <td>'.form_dropdown('approvalType[]',$approval_types_arr , $val['approvalType'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('isFYBasedSerialNo[]',$div_isFYBasedSerialNo , $val['isFYBasedSerialNo'], 'class="form-control" onchange="update_Serialization('.$val['codeID'].')" '.$isFYBasedSerial.' id="isFYBasedSerialNo_'.$val['codeID'].'"  ').'</td>
                    <td>'.form_dropdown('postDate[]',$div_postDate , $val['postDate'], 'class="form-control" onchange="update_postDate('.$val['codeID'].')" '.$isDO.' id="postDate_'.$val['codeID'].'" ').'</td>
                    <td style="width:120px;"><input type="text" class="form-control" name="prefix[]" value="' . $val['prefix'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="serialno[]" value="' . $val['serialNo'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="format_length[]" value="' . $val['formatLength'] .'"></td>
                    <td>'.form_dropdown('format_1[]',$div_prefix , $val['format_1'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_2[]',$div_arr , $val['format_2'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_3[]',$div_prefix , $val['format_3'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_4[]',$div_arr , $val['format_4'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_5[]',$div_prefix , $val['format_5'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_6[]',$div_arr , $val['format_6'], 'class="form-control"').'</td>
                    <td style="width:120px;"><input type="number" class="form-control" name="approvalLevel[]" value="' . $val['approvalLevel'] .'"></td>
                    <td>'.form_dropdown('printHeaderFooterYN[]',$YN_arr_SalesOrder , $val['printHeaderFooterYN'], 'class="form-control" id="setup_invoice"').'</td>
                </tr>';
                }
                else if($val['documentID'] == 'INV' || $val['documentID'] == 'NINV' || $val['documentID'] == 'FA' || $val['documentID'] == 'SRV') {
                    $data .= '<tr>
                    <td>' . $i .' </td>
                    <td>' . $val['documentID'] .' - ' . $val['document'] .' <input type="hidden" value="' . $val['codeID'] .'"  name="codeID[]"></td>
                    <td>'.form_dropdown('approvalType[]',$approval_types_arr , $val['approvalType'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('isFYBasedSerialNo[]',$div_isFYBasedSerialNo , $val['isFYBasedSerialNo'], 'class="form-control" onchange="update_Serialization('.$val['codeID'].')" '.$isFYBasedSerial.' id="isFYBasedSerialNo_'.$val['codeID'].'"  ').'</td>
                    <td>'.form_dropdown('postDate[]',$div_postDate , $val['postDate'], 'class="form-control" onchange="update_postDate('.$val['codeID'].')" '.$isDO.' id="postDate_'.$val['codeID'].'" ').'</td>
                    <td style="width:120px;"><input type="text" class="form-control" name="prefix[]" value="' . $val['prefix'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="serialno[]" value="' . $val['serialNo'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="format_length[]" value="' . $val['formatLength'] .'"></td>
                    <td>'.form_dropdown('format_1[]',$div_prefix , $val['format_1'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_2[]',$div_arr , $val['format_2'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_3[]',$div_prefix_category , $val['format_3'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_4[]',$div_prefix_category , $val['format_4'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_5[]',$div_prefix_category , $val['format_5'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_6[]',$div_arr , $val['format_6'], 'class="form-control"').'</td>
                    <td style="width:120px;"><input type="number" class="form-control" name="approvalLevel[]" value="' . $val['approvalLevel'] .'"></td>
                    <td>'.form_dropdown('printHeaderFooterYN[]',$YN_arr , $val['printHeaderFooterYN'], 'class="form-control"').'</td>
                </tr>';
                }
                 else {
                    $data .= '<tr>
                    <td>' . $i .' </td>
                    <td>' . $val['documentID'] .' - ' . $val['document'] .' <input type="hidden" value="' . $val['codeID'] .'"  name="codeID[]"></td>
                    <td>'.form_dropdown('approvalType[]',$approval_types_arr , $val['approvalType'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('isFYBasedSerialNo[]',$div_isFYBasedSerialNo , $val['isFYBasedSerialNo'], 'class="form-control" onchange="update_Serialization('.$val['codeID'].')" '.$isFYBasedSerial.' id="isFYBasedSerialNo_'.$val['codeID'].'"  ').'</td>
                    <td>'.form_dropdown('postDate[]',$div_postDate , $val['postDate'], 'class="form-control" onchange="update_postDate('.$val['codeID'].')" '.$isDO.' id="postDate_'.$val['codeID'].'" ').'</td>
                    <td style="width:120px;"><input type="text" class="form-control" name="prefix[]" value="' . $val['prefix'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="serialno[]" value="' . $val['serialNo'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="format_length[]" value="' . $val['formatLength'] .'"></td>
                    <td>'.form_dropdown('format_1[]',$div_prefix , $val['format_1'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_2[]',$div_arr , $val['format_2'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_3[]',$div_prefix , $val['format_3'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_4[]',$div_arr , $val['format_4'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_5[]',$div_prefix , $val['format_5'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_6[]',$div_arr , $val['format_6'], 'class="form-control"').'</td>
                    <td style="width:120px;"><input type="number" class="form-control" name="approvalLevel[]" value="' . $val['approvalLevel'] .'"></td>
                    <td>'.form_dropdown('printHeaderFooterYN[]',$YN_arr , $val['printHeaderFooterYN'], 'class="form-control"').'</td>
                </tr>';
                }
                

            $i++;
            }

        echo $data;

    }

    function fetch_company_codeMaster_fin()
    {
        $financeyear=$this->input->post('financeyear');
        $company_id = $this->common_data['company_data']['company_id'];

        $documentMaster = $this->db->query("SELECT * FROM srp_erp_financeyeardocumentcodemaster  WHERE companyID='{$company_id}' AND financeYearID='{$financeyear}'")->result_array();

        $div_arr    = ['' => 'Blank', '/' => '/', '-' => '-'];
        $div_prefix = ['' => 'Blank', 'prefix' => 'Prefix', 'yyyy' => 'YYYY', 'yy' => 'YY', 'mm' => 'MM'];

        $data = '<table class="table table-bordered table-striped table-condesed">
            <thead>
            <tr>
              <th style="min-width: 5%">#</th>
              <th style="min-width: 5%">Document</th>
              <th style="min-width: 10%">Prefix</th>
              <th style="min-width: 10%">Serial No</th>
              <th style="min-width: 10%">Format Length</th>
              <th style="min-width: 10%">Format 1</th>
              <th style="min-width: 10%">Format 2</th>
              <th style="min-width: 10%">Format 3</th>
              <th style="min-width: 10%">Format 4</th>
              <th style="min-width: 10%">Format 5</th>
              <th style="min-width: 10%">Format 6</th>
          </tr>
            </thead>
            <tbody>';
            $i= 1;
        if($documentMaster){
            foreach($documentMaster as $val){
                $data .= '<tr>
                    <td>' . $i .' </td>
                    <td>' . $val['documentID'] .' - ' . $val['document'] .' <input type="hidden" value="' . $val['codeID'] .'"  name="codeID[]"></td>
                    <td style="width:120px;"><input type="text" class="form-control" name="prefix[]" value="' . $val['prefix'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="serialno[]" value="' . $val['serialNo'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="format_length[]" value="' . $val['formatLength'] .'"></td>
                    <td>'.form_dropdown('format_1[]',$div_prefix , $val['format_1'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_2[]',$div_arr , $val['format_2'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_3[]',$div_prefix , $val['format_3'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_4[]',$div_arr , $val['format_4'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_5[]',$div_prefix , $val['format_5'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_6[]',$div_arr , $val['format_6'], 'class="form-control"').'</td>
                </tr>';

                $i++;
            }
        }else{
            $data .= '<tr><td colspan="11" align="center">No Records Found</td></tr>';
        }


        echo $data;

    }

    function update_company_codes_prefixChange(){
        echo json_encode($this->Company_model->update_company_codes_prefixChange());
    }

    function update_company_url_change(){
        echo json_encode($this->Company_model->update_company_url_change());
    }

    function currency_validation(){
        echo json_encode($this->Company_model->currency_validation());
    }

    function update_Serialization(){
        echo json_encode($this->Company_model->update_Serialization());
    }

    function update_approval_types(){
        echo json_encode($this->Company_model->update_approval_types());
    }

    function add_missing_document_code(){
        echo json_encode($this->Company_model->add_missing_document_code());
    }

    function fetch_comapny_code_location()
    {
        $location =$this->input->post('location');
        $company_id = $this->common_data['company_data']['company_id'];
        $financeyear = $this->input->post('financeyearlocation');

        $documentMaster = $this->db->query("SELECT * FROM srp_erp_locationdocumentcodemaster  WHERE companyID='{$company_id}' AND locationID ='{$location}' AND financeYearID ='{$financeyear}'")->result_array();



        $div_arr    = ['' => 'Blank', '/' => '/', '-' => '-'];
        $div_prefix = ['' => 'Blank', 'prefix' => 'Prefix', 'yyyy' => 'YYYY', 'yy' => 'YY', 'mm' => 'MM','Location'=>'Location'];

        $data = '<table class="table table-bordered table-striped table-condesed">
            <thead>
            <tr>
              <th style="min-width: 5%">#</th>
              <th style="min-width: 5%">Document</th>
              <th style="min-width: 10%">Prefix</th>
              <th style="min-width: 10%">Serial No</th>
              <th style="min-width: 10%">Format Length</th>
              <th style="min-width: 10%">Format 1</th>
              <th style="min-width: 10%">Format 2</th>
              <th style="min-width: 10%">Format 3</th>
              <th style="min-width: 10%">Format 4</th>
              <th style="min-width: 10%">Format 5</th>
              <th style="min-width: 10%">Format 6</th>
          </tr>
            </thead>
            <tbody>';
        $i= 1;
        if($documentMaster){
            foreach($documentMaster as $val){
                $data .= '<tr>
                    <td>' . $i .' </td>
                    <td>' . $val['documentID'] .' - ' . $val['document'] .' <input type="hidden" value="' . $val['codeID'] .'"  name="codeID[]"></td>
                    <td style="width:120px;"><input type="text" class="form-control" name="prefix[]" value="' . $val['prefix'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="serialno[]" value="' . $val['serialNo'] .'"></td>
                    <td style="width:120px;"><input type="number" class="form-control" name="format_length[]" value="' . $val['formatLength'] .'"></td>
                    <td>'.form_dropdown('format_1[]',$div_prefix , $val['format_1'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_2[]',$div_arr , $val['format_2'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_3[]',$div_prefix , $val['format_3'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_4[]',$div_arr , $val['format_4'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_5[]',$div_prefix , $val['format_5'], 'class="form-control"').'</td>
                    <td>'.form_dropdown('format_6[]',$div_arr , $val['format_6'], 'class="form-control"').'</td>
                </tr>';

                $i++;
            }
        }else
        {
            $data .= '<tr><td colspan="11" align="center">No Records Found</td></tr>';
        }

        echo $data;

    }
    function add_missing_document_code_location(){
        echo json_encode($this->Company_model->add_missing_document_code_location());
    }
    function update_company_location_codes_prefixChange(){
        echo json_encode($this->Company_model->update_company_location_codes_prefixChange());
    }
    function document_code_location_finacebasedup(){
        echo json_encode($this->Company_model->document_code_location_finacebasedup());
    }

    function subscription(){        
        $data['title'] = 'Subscription';
        $data['extra'] = '';
        $data['pay_pal_client_id'] = $this->config->item('pay_pal_client_id');

        $this->load->helper('cookie');
        $this->load->view('include/header',$data);
        $this->load->view('include/top-mpr',$data);
        $this->load->view('system/company/subscription-invoice',$data);
        $this->load->view('include/footer');
        $this->load->view('system/company/subscription-invoice-js');
    }

    function subscription_invoices(){
        $companyID = current_companyID();

        $db2 = $this->load->database('db2', TRUE);
        $db_config['hostname'] = trim($db2->hostname);
        $db_config['username'] = trim($db2->username);
        $db_config['password'] = trim($db2->password);
        $db_config['database'] = trim($db2->database);
        $db_config['dbdriver'] = 'mysqli';
        $db_config['db_debug'] = TRUE;

        $this->datatables->set_database($db_config, FALSE, TRUE);

        $this->datatables->select("inv_mas.invID AS invID, invNo, FORMAT(invTotal, invDecPlace) AS invTotal, isAmountPaid,  
                            cur_mas.CurrencyCode AS cur_code, DATE_FORMAT(inv_mas.createdDateTime, '%Y-%m-%d') AS invDate, 
                            IF(sub_det.itemID = 1, his.dueDate, '') AS dueDate", false)
            ->from('subscription_invoice_master AS inv_mas')
            ->join('srp_erp_currencymaster AS cur_mas', 'cur_mas.currencyID = inv_mas.invCur')
            ->join("(SELECT invID, itemID FROM subscription_invoice_details WHERE companyID = {$companyID} AND itemID = 1) AS sub_det",
                   'sub_det.invID = inv_mas.invID', 'left')
            ->join('companysubscriptionhistory AS his', 'his.subscriptionID = inv_mas.subscriptionID', 'left')
            ->edit_column('invTotal', '<div style="text-align: right">$1 &nbsp; $2</div>', 'invTotal, cur_code')
            ->add_column('payStatus', '$1', 'subscription_pay_status_action(isAmountPaid)')
            ->add_column('action', '$1', 'subscription_action(invID, isAmountPaid)')
            ->where('inv_mas.companyID', $companyID);
        echo $this->datatables->generate();
    }

    function load_subscription_invoice_view(){
        $company_id = current_companyID();
        $inv_id = $this->input->post('inv_id');

        $db2 = $this->load->database('db2', TRUE);
        $mas_data = $db2->query("SELECT inv_mas.invNo, inv_mas.invTotal, inv_mas.invDecPlace, cur_mas.CurrencyCode,
                              inv_mas.createdDateTime, com.company_name, companyPrintAddress, inv_mas.isAmountPaid,  
                              inv_mas.invID, inv_mas.paymentType, inv_mas.payRecDate, inv_mas.invCur
                              FROM subscription_invoice_master AS inv_mas 
                              JOIN srp_erp_company AS com ON com.company_id = inv_mas.companyID
                              JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID=inv_mas.invCur
                              WHERE inv_mas.invID = {$inv_id} AND inv_mas.companyID = {$company_id}")->row_array();

        if(empty($mas_data)){
            die( json_encode(['e', 'Invoice master deatils not found']) );
        }

        $det_data = $db2->query("SELECT amountBeforeDis, amount, discountPer, discountAmount, inv_det.itemDescription  
                              FROM subscription_invoice_details AS inv_det
                              LEFT JOIN system_invoice_item_type AS item_type ON item_type.type_id=inv_det.itemID
                              WHERE inv_det.invID = {$inv_id} AND inv_det.companyID = {$company_id}")->result_array();
    

        $att_data = $this->subscription_attachment_view($inv_id);

        $inv_balance = $this->subscription_invoice_balance($inv_id);

        if($mas_data['invCur'] != 2){
            $invCur = $mas_data['invCur'];
            $exchange = $db2->query("SELECT conversion FROM srp_erp_currencyconversion 
                            WHERE masterCurrencyID = {$invCur} AND subCurrencyID = 2")->row('conversion');
            $mas_data['pay_pal_amount'] = round(($inv_balance/$exchange), 2);
        }
        else{
            $mas_data['pay_pal_amount'] = round($inv_balance, $mas_data['invDecPlace']);
        }

        $data['mas_data'] = $mas_data;
        $data['det_data'] = $det_data;
        $data['att_data'] = $att_data;
        $data['paymentDet'] = $this->invoice_payment_details_view($inv_id, $mas_data['invDecPlace']);

        $view = $this->load->view('system/company/subscription-invoice-view.php', $data, true);
        echo json_encode(['s', 'view'=>$view, 'isAmountPaid'=>$mas_data['isAmountPaid']]);
    }

    function invoice_payment_details_view($inv_id, $dPlace){   
        $db2 = $this->load->database('db2', TRUE);

        $data = $db2->select("payAutoID,narration,pay_date,amount,pt.pay_description, invPay.pay_type")
                ->join('system_payment_types AS pt', 'pt.id=invPay.pay_type', 'left')
                ->where(['inv_id'=> $inv_id])
                ->get('subscription_invoice_payment_details AS invPay')
                ->result_array();

        if(empty($data)){
            return '<tr><td colspan="5"  style="text-align: center">No records found</td></tr>';
        }
        
        $i = 1; $paid_tot = 0; $view = '';
        foreach ($data as $row){
            $paid_tot += round($row['amount'], $dPlace);
            $view .= '<tr>
                         <td>'.$i.'</td>                        
                         <td>'.$row['pay_description'].'</td>                        
                         <td>'.$row['narration'].'</td>                        
                         <td>'.$row['pay_date'].'</td>                        
                         <td align="right">'.number_format($row['amount'], $dPlace).'</td>';

             
            $view .= '</tr>';

            $i++;
        }
       
        $view .= '<tr>
                    <td colspan="4" align="right"><b>Total Payment</b></td>
                    <td align="right"><b>'.number_format($paid_tot, $dPlace).'</b></td>
                  </tr>';


        return $view;
    }

    function subscription_attachment_view($inv_id){
        $db2 = $this->load->database('db2', TRUE);
        $company_id = current_companyID();

        $where = ['companyID'=>$company_id, 'invID'=>$inv_id];
        $isAmountPaid = $db2->get_where('subscription_invoice_master', $where)->row('isAmountPaid');

        $att_data = $db2->query("SELECT attachmentID, attachmentDescription, fileName, fileType FROM documentattachments  
                        WHERE documentSystemCode = {$inv_id} AND companyID = {$company_id}")->result_array();

        $view = '';
        if(!empty($att_data)){
            $i = 1;
            foreach ($att_data as $row) {
                $att_id = $row['attachmentID'];
                $link = $this->s3->createPresignedRequest(current_companyCode()."/subscription/".$row['fileName'], '1 hour');

                $delete_str = '';
                if($isAmountPaid == 0){
                    $delete_str = '<span class="delete-rel-items"> &nbsp; | &nbsp; </span>
                                  <a onclick="sub_attachment_delete(' . $att_id . ',\'' . $row['fileName'] . '\')" title="Delete" class="delete-rel-items">
                                     <span rel="tooltip" class="glyphicon glyphicon-trash delete-icon"></span>
                                  </a>';
                }

                $view .= '<tr class="">
                              <td>'.$i.'</td>
                              <td >'.$row['fileName'].'</td>
                              <td>'.$row['attachmentDescription'].'</td>
                              <td class="text-center">'.file_type_icon($row['fileType']).'</td>
                              <td class="text-center">                                            
                                 <a target="_blank" href="' . $link . '" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>
                                 '.$delete_str.'
                              </td>
                          </tr>';
                $i++;
            }
        }

        return $view;
    }

    function subscription_attachment_upload(){
        $this->form_validation->set_rules('att_inv_id', 'ID', 'trim|required');
        $this->form_validation->set_rules('att_description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $att_inv_id = $this->input->post('att_inv_id');
        $dateTime = current_date();
        $company_id = current_companyID();


        if (empty($_FILES['att_file']['name'])) {
            die( json_encode(['e', 'File upload field is empty']) );
        }
        $file = $_FILES['att_file'];
        $att_des = trim($this->input->post('att_description') ?? '');

        if($file['error'] == 1){
            die( json_encode(['e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)."]) );
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if(!in_array($ext, $allowed_types)){
            die( json_encode(['e', "The file type you are attempting to upload is not allowed. ( .{$ext} )"]) );
        }

        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if($size > 5){
            die( json_encode(['e', "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]) );
        }

        $fileName = "INV_{$att_inv_id}_".time().".$ext";
        $fileName_s3 = current_companyCode()."/subscription/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $fileName_s3);

        if (!$s3Upload) {
            die( json_encode(['e', 'Error in document upload location configuration']) );
        }

        $db2 = $this->load->database('db2', TRUE);

        $db2->trans_start();

        $emp_id = current_userID();
        $inv_data = [
            'documentID' => 'INV', 'documentSystemCode' => $att_inv_id, 'attachmentDescription' => $att_des,
            'fileName' => $fileName, 'fileType' => $ext, 'fileSize' => $size, 'companyID' => $company_id,
            'createdPCID' => current_pc(),  'createdUserID' => $emp_id, 'createdDateTime' => $dateTime, 'timestamp' => $dateTime
        ];

        $db2->insert('documentattachments', $inv_data);

        $db2->trans_complete();
        if($db2->trans_status() == true){
            $att_data = $this->subscription_attachment_view($att_inv_id);
            echo json_encode(['s', 'Attachment successfully uploaded', 'att_data'=>$att_data]);
        }else{
            echo json_encode(['e', 'Error in document attachment upload']);
        }
    }

    function subscription_attachment_delete() {
        $attachmentID = trim($this->input->post('attachmentID') ?? '');
        $fileName = trim($this->input->post('fileName') ?? '');

        $db2 = $this->load->database('db2', TRUE);
        $inv_data = $db2->query("SELECT mas.isAmountPaid, mas.invID FROM documentattachments AS att
                           JOIN subscription_invoice_master AS mas ON mas.invID = att.documentSystemCode
                           WHERE att.attachmentID = {$attachmentID}")->row_array();
        $isAmountPaid = $inv_data['isAmountPaid'];

        if($isAmountPaid != 0){
            die( json_encode(['e', 'This document is on paid status.You can not delete this attachment']) );
        }


        $fileName = current_companyCode()."/subscription/$fileName";



        $result = $this->s3->delete($fileName);
        if ($result) {
            $db2->delete('documentattachments', ['attachmentID' => $attachmentID]);
            $att_data = $this->subscription_attachment_view($inv_data['invID']);
            echo json_encode(['s', 'Attachment successfully deleted', 'att_data'=>$att_data]);
        } else {
            echo json_encode(['e', 'Error in attachment delete process']);
        }
    }

    function subscription_payment_confirmation(){
        $this->form_validation->set_rules('att_inv_id', 'ID', 'trim|required');
        $this->form_validation->set_rules('pay_type', 'Payment Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $att_inv_id = $this->input->post('att_inv_id');
        $payment_type = $this->input->post('pay_type');
        $dateTime = current_date();
        $company_id = current_companyID();
        $emp_id = current_userID();

        $db2 = $this->load->database('db2', TRUE);
        $db2->trans_start();

        $where = ['companyID'=>$company_id, 'invID'=>$att_inv_id];
        $paymentType_status = $db2->get_where('subscription_invoice_master', $where)->row('paymentType');
        if($paymentType_status > 0){
            die( json_encode(['e', 'Payment already done for this invoice.']) );
        }

        /*Check is there is any attachment uploaded for this invoice*/
        $attachment = $db2->get_where('documentattachments', [ 'documentSystemCode'=>$att_inv_id, 'companyID'=>$company_id ])->row('documentSystemCode');
        if(empty($attachment)){
            die( json_encode(['e', 'To confirm the Payment, You should upload at least one attachment.']) );
        }


        $update_data = [
            'isAmountPaid' => -1, 'paymentType' => $payment_type, 'payRecDate' => current_date(), 'modifiedPCID' => current_pc(),
            'modifiedUserID' => $emp_id, 'modifiedDateTime' => $dateTime, 'timestamp' => $dateTime
        ];
        $db2->where($where)->update('subscription_invoice_master', $update_data);

        $db2->trans_complete();
        if($db2->trans_status() == true){
            $payment_det = $this->get_payment_detail_view($payment_type, $dateTime);
            echo json_encode(['s', 'Payment successfully confirmed', 'payment_det'=>$payment_det]);
        }else{
            echo json_encode(['e', 'Error in payment confirmation']);
        }
    }

    function pay_pal_payment_verify(){
        $orderID = $this->input->post('orderID');
        $inv_id = $this->input->post('inv_id');

        /*$orderID = '5PT73782SX596380A'; //$this->input->post('orderID');
        $inv_id = '51'; //$this->input->post('inv_id');*/

        $response = $this->pay_pal_verify_curl($orderID);
        //echo '<pre>'; print_r($response); echo '</pre>';        die();

        $db2 = $this->load->database('db2', TRUE);
        $dateTime = current_date(); $company_id = current_companyID(); $emp_id = current_userID(); $pc = current_pc();
        $error = 0;
        if(!array_key_exists('id', $response)){
            $error = 1;
        }
        else if($response['status'] != 'COMPLETED'){
            $error = 1;
        }

        if($error == 1){
            $response = serialize($response);
            $insertData = [
                'invID'=> $inv_id, 'orderID'=> $orderID, 'errorMsg'=> $response, 'companyID'=> $company_id,
                'createdPCID'=> current_pc(), 'createdUserID'=> $emp_id, 'createdDateTime'=> $dateTime, 'timestamp'=> $dateTime
            ];

            $db2->insert('paypal_error_log', $insertData);

            die( json_encode(['e', 'Error in payment verification.<p>Order ID : '.$orderID]));
        }

        $payer_id = $response['payer']['payer_id'];
        $purchase_data = $response['purchase_units'][0];
        $payments_data = $purchase_data['payments']['captures'][0];
        $paymentsID = $payments_data['id'];
        $payments_data = $payments_data['seller_receivable_breakdown'];

        $mas_data = $db2->query("SELECT inv_mas.invCur
                              FROM subscription_invoice_master AS inv_mas  
                              WHERE inv_mas.invID = {$inv_id}")->row_array();


        $att_data = $this->subscription_attachment_view($inv_id);

        $exchange = $db2->query("SELECT con.conversion FROM subscription_invoice_master AS inv_mas  
                                JOIN srp_erp_currencyconversion AS con ON con.masterCurrencyID = inv_mas.invCur
                                AND con.subCurrencyID = 2
                                WHERE inv_mas.invID = {$inv_id} ")->row('conversion');
        
        $verifyData = [
            'paypalOrderID' => $orderID,
            'paypalPayeeMailID'=> $purchase_data['payee']['email_address'],
            'paypalMerchantID'=> $purchase_data['payee']['merchant_id'],
            'paypalPaymentsID'=> $paymentsID,
            'paypalExchangeRate' => $exchange,
            'paypalFee' => $payments_data['paypal_fee']['value'],
            'paypalNetAmount' => $payments_data['net_amount']['value'],
            'paypalPayerID'=> $payer_id,

            'isAmountPaid' => 1, 'paymentType' => 2, 'payRecDate' => $dateTime, 'modifiedPCID' => $pc,
            'modifiedUserID' => $emp_id, 'modifiedDateTime' => $dateTime, 'timestamp' => $dateTime
        ];
        

        $db2->trans_start();

        $where = ['companyID'=>$company_id, 'invID'=>$inv_id];
        $db2->where($where)->update('subscription_invoice_master', $verifyData);

        //inster to payment detail table 
        $db2->insert('subscription_invoice_payment_details', [
            'inv_id'=> $inv_id, 'pay_type'=> 2, 'amount'=> $payments_data['net_amount']['value'],
            'pay_date'=> $dateTime, 'narration'=> '',
            'companyID'=> $company_id, 'createdPCID'=> $pc, 'createdUserID'=> $emp_id,
            'createdDateTime'=> $dateTime, 'timestamp'=> $dateTime
        ]);

        $db2->trans_complete();
        if($db2->trans_status() == true){
            $payment_det = $this->get_payment_detail_view(2, $dateTime);
            echo json_encode(['s', 'Payment successfully confirmed', 'payment_det'=>$payment_det]);
        }else{
            echo json_encode(['e', 'Error in payment confirmation <p>Order ID : '.$orderID]);
        }
    }

    function pay_pal_verify_curl($orderID){
        $client_id = $this->config->item('pay_pal_client_id');
        $secret_key = $this->config->item('pay_pal_secret_key');
        $api_url = $this->config->item('pay_pal_verify_url');
        $url = "{$api_url}{$orderID}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$secret_key");
        $response = curl_exec($ch);
        //var_dump($response);

        if (curl_errno($ch)) {
            $msg = curl_error($ch);
            curl_close($ch);
            $response = ['error', $msg];
        }
        else {
            $response = json_decode($response, true);
            curl_close($ch);
        }

        return $response;
    }

    function hide_subscription_alert(){
        $this->session->unset_userdata('subscription_expire_notification');
    }

    function get_payment_detail_view($type, $date){
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);

       if($type == 1)
       {
           $type = 'Bank Transfer';
       }else if($type==2)
       {
           $type = 'Pay Pal';

       }else if($type==4){
           $type = 'Credit Card';
       }

        $date = date('Y-m-d', strtotime($date));

        return '<div class="form-group">
                    <label class="pay-input" style="margin-top: 3px;">&nbsp; '.$this->lang->line('common_payment_type').' : </label>
                    <label style="margin-top: 3px; font-weight: normal">&nbsp; '.$type.'</label>
                </div>
                <div class="form-group">
                    <label class="pay-input" style="margin-top: 3px;">&nbsp; '.$this->lang->line('common_payment_received_date').' : </label>
                    <label style="margin-top: 3px; font-weight: normal">&nbsp;'.$date.' </label>
                </div>';
    }

    function pay_pal_view_error($orderID=''){
        $db2 = $this->load->database('db2', TRUE);

        if(empty($orderID)){
            die("Order id is required");
        }

        $data = $db2->get_where('paypal_error_log', ['orderID'=>$orderID])->row_array();

        $errorMsg = $data['errorMsg'];
        if(empty($errorMsg)){
            die("$orderID id not found");
        }
        $errorMsg = unserialize($errorMsg);

        $data['errorMsg_str'] = $errorMsg;
        echo '<pre>'; print_r($data); echo '</pre>';

    }

    function insert_system_audit_log_nav(){
        echo json_encode($this->Company_model->insert_system_audit_log_nav());
    }

    function upload_attachments(){
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $company_id = current_companyID();
        $dateTime = date('Y-m-d H:i:s');

        if (empty($_FILES['document_file']['name'])) {
            die( json_encode(['e', 'File upload field is empty']) );
        }

        $file = $_FILES['document_file'];
        $att_des = trim($this->input->post('description') ?? '');
        $expireDate = $this->input->post('expireDate');

        $date_format_policy = date_format_policy();
        $expireDate = (!empty($expireDate)) ? input_format_date_php($expireDate, $date_format_policy) : null;

        if($file['error'] == 1){
            die( json_encode(['e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB).", $file]) );
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if(!in_array($ext, $allowed_types)){
            die( json_encode(['e', "The file type you are attempting to upload is not allowed. ( .{$ext} )"]) );
        }

        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if($size > 5){
            die( json_encode(['e', "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]) );
        }

        $company_code = current_companyCode();

        $fileName = "SUB_{$company_id}_".time().".$ext";
        $fileName_s3 = "{$company_code}/subscription/{$fileName}";
        $s3Upload = $this->s3->upload($file['tmp_name'], $fileName_s3);

        if (!$s3Upload) {
            die( json_encode(['e', 'Error in document upload location configuration']) );
        }

        $emp_id = current_userID();
        $inv_data = [
            'documentID' => 'SUB', 'documentSystemCode' => $company_id, 'attachmentDescription' => $att_des, 'docExpiryDate' => $expireDate,
            'fileName' => $fileName, 'fileType' => $ext, 'fileSize' => $size, 'companyID' => $company_id,
            'createdPCID' => current_pc(),  'createdUserID' => $emp_id, 'createdDateTime' => $dateTime, 'timestamp' => $dateTime
        ];

        $db2 = $this->load->database('db2', TRUE);
        $db2->insert('documentattachments', $inv_data);

        echo json_encode(['s', 'Attachment successfully uploaded']);

    }

    function load_subscription_attachment_view(){
        $company_id = current_companyID();
        $db2 = $this->load->database('db2', TRUE);
        $format = convert_date_format_sql();

        $att_data = $db2->query("SELECT attachmentID, attachmentDescription, fileName, fileType, 
                        DATE_FORMAT(docExpiryDate,'{$format}') AS docExpiryDate
                        FROM documentattachments  
                        WHERE documentSystemCode = {$company_id} AND documentID = 'SUB' AND companyID = {$company_id}")->result_array();

        $view = '';
        if(!empty($att_data)){
            $i = 1;
            $company_code = current_companyCode();
            foreach ($att_data as $row) {

                $link = $this->s3->createPresignedRequest("{$company_code}/subscription/".$row['fileName'], '1 hour');
                $delete_str = '<span class="delete-rel-items"> &nbsp; | &nbsp; </span>
                                  <a onclick="sub_attachment_delete(' . $row['attachmentID'] . ',\'' . $row['fileName'] . '\')" title="Delete" class="delete-rel-items">
                                     <span rel="tooltip" class="glyphicon glyphicon-trash delete-icon"></span>
                                  </a>';

                $view .= '<tr class="">
                              <td>'.$i.'</td>
                              <td >'.$row['fileName'].'</td>
                              <td>'.$row['attachmentDescription'].'</td>
                              <td>'.$row['docExpiryDate'].'</td>
                              <td class="text-center">'.file_type_icon($row['fileType']).'</td>
                              <td class="text-center">                                            
                                 <a target="_blank" href="' . $link . '" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>
                                 '.$delete_str.'
                              </td>
                          </tr>';
                $i++;
            }
        }
        else{
            $view = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }

        echo $view;
    }

    function company_subscription_attachment_delete() {
        $attachmentID = trim($this->input->post('attachmentID') ?? '');
        $fileName = trim($this->input->post('fileName') ?? '');


        $db2 = $this->load->database('db2', TRUE);
        $company_code = current_companyCode();
        $fileName = "{$company_code}/subscription/$fileName";

        $result = $this->s3->delete($fileName);
        if ($result) {
            $db2->delete('documentattachments', ['attachmentID' => $attachmentID]);

            echo json_encode(['s', 'Attachment successfully deleted',]);
        } else {
            echo json_encode(['e', 'Error in attachment delete process']);
        }
    }

    public function QHSE_user_authentication()
    {
        $userID = current_userID();
        $companyID = current_companyID();
        $loginID = $this->db->query("SELECT UserName FROM srp_employeesdetails WHERE Erp_companyID = {$companyID} 
                                      AND EIdNo = {$userID}")->row('UserName');

        $post_data = ["username"=> $loginID];
        $url = 'api/v1/oauth/login_token';

        $res_data = $this->Company_model->QHSE_api_requests($post_data, $url);

        if($res_data['status'] == 's'){
            $res_data['url'] = $this->config->item('QHSE_login_url');;
            $res_data['token'] = $res_data['data'];
            $res_data['loginID'] = $loginID;
            unset($res_data['data']);
        }
        echo json_encode($res_data);
    }

    public function insert_QHSE_login_test()
    {
        $data = [
            "name"=> "Nasir Ahamed",
            "email"=> "nasirpakistani@mail.com",
            "password" => "123456",
            "password_confirmation" => "123456",
            "activeYN" => 1
        ];

        $url = 'api/v1/user/create';
        $res_data = $this->Company_model->QHSE_api_requests($data, $url);
        echo '<pre>'; print_r($res_data); echo '</pre>';
    }
    function company_secondarylogoimage_upload()
    {
        $this->form_validation->set_rules('faID', 'Company Id is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Company_model->company_secondarylogo_image_upload());
        }
    }
    public function relamax_user_authentication()
    {
        $url = 'api/v1/getLoginToken';
        $res_data = $this->Company_model->realmax_api_requests($url);

        if($res_data['status'] == 's'){
            $res_data['url'] = $this->config->item('realmax_login_url');
            $res_data['token'] = $res_data['data'];
            unset($res_data['data']);
        }else
        {
            $res_data['url'] = $this->config->item('realmax_login_url');
        }
        echo json_encode($res_data);
    }

    public function fetch_support_contact_info(){
        echo json_encode(fetch_support_contact_info());
    }

    function credit_card_payment(){
        $storage_key = $uri_inv = $this->uri->segment(2);
        $uri_inv = explode('-', $uri_inv);
        $inv_id = $uri_inv[1];
        $data['storage_key'] = $storage_key;
        $data['inv_id'] = $inv_id;

        $this->load->view('system/company/credit-card-payemnt-view', $data);
        //echo $inv_id;
    }

    function get_mastercard_sessionID()
    {
        $invoiceID = $this->input->post('invoiceID');
        $companyID = current_companyID();

        $db2 = $this->load->database('db2', TRUE);
        $invoiceamount = $db2->query("SELECT
	                                      IFNULL(amount,0) as amount,
	                                      com.company_name,	
                                          com.companyPrintAddress,
                                          inv_master.invID
                                          FROM
	                                      subscription_invoice_master AS inv_master 
	                                      LEFT JOIN  subscription_invoice_details AS inv_det ON inv_det.invID = inv_master.invID
	                                      JOIN srp_erp_company AS com ON com.company_id = inv_master.companyID
                                          WHERE
                                          inv_det.companyID = $companyID
	                                      AND inv_det.invID = $invoiceID 
	                                      GROUP BY
                                          inv_det.invID")->row_array();
                                          
        if(empty($invoiceamount)){
             die( json_encode( ['session_id'=> 0, 'error_msg'=> 'Invoice details not found']) );
        }

        $merchant = $this->config->item('merchant_id');
        $apipassword = $this->config->item('api_password');
        $base_url  = $this->config->item('merchant_base_url');
        $currency  = $this->config->item('mastercard_currency');
        $version  = $this->config->item('mastercard_version');
        
        $ch = curl_init();
        $url = $base_url.'/api/rest/version/'.$version.'/merchant/'.$merchant.'/session';
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,TRUE);        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt($ch, CURLOPT_USERPWD, "merchant.$merchant:$apipassword");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $session=curl_exec($ch);
        curl_close($ch);
        $session=json_decode($session);
        $data['session_id']  = 0;
        if($session->result == 'SUCCESS')
        {
            $balance = $this->subscription_invoice_balance($invoiceID);
            $data['merchant'] = $merchant;
            $data['session_id'] = $session->session->id;
            $data['invoice_amount'] = $balance;
            $data['invoice_name'] = $invoiceamount['company_name'];
            $data['address'] = $invoiceamount['companyPrintAddress'];
            $data['invID'] = $invoiceamount['invID'];
            //$data['invID'] = rand(120, 1100); //to debug
            $data['mastercard_currency'] = $currency;
        }else {
            $gate_way_error = $session->error->explanation;
            $data['error_msg'] = $gate_way_error;

            $gate_way_error = (is_string($gate_way_error))? $gate_way_error: json_encode($gate_way_error);            
            $date_time = current_date();

            $insertData = [
                'invID'=> $invoiceID, 'summary'=> 'Error', 'payLog'=> $gate_way_error, 'paymentType'=> 4, 
                'payStatus'=> 2, 'companyID'=> $companyID, 'createdPCID'=> current_pc(), 
                'createdUserID'=> current_userID(), 'createdDateTime'=> $date_time, 'timestamp'=> $date_time
            ];
                
            $db2->insert('payment_log', $insertData);   
        }
        echo json_encode($data);
    }

    function save_mastercard_details(){ //creditr card
        $this->db->trans_start();
        $company_id = current_companyID();
        $emp_id = current_userID();
        $inv_id = $this->input->post('invoiceID');        
        //$inv_id = 173;
    
        $dateTime = date('Y-m-d H:i:s');
        $pc = current_pc();

        $merchant = $this->config->item('merchant_id');
        $apipassword = $this->config->item('api_password');
        $base_url  = $this->config->item('merchant_base_url');
        $currency  = $this->config->item('mastercard_currency');
        $version  = $this->config->item('mastercard_version');
        $ch=curl_init();
        $url = $base_url.'/api/rest/version/'.$version.'/merchant/'.$merchant.'/order/'.$inv_id;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt($ch, CURLOPT_USERPWD, "merchant.$merchant:$apipassword");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $response=curl_exec($ch);
        curl_close($ch);
        $response=json_decode($response);

        //echo '<pre>'; print_r($response);exit;
        $db2 = $this->load->database('db2', TRUE);
        $db2->trans_start();

        $payID = 0;
        $msg = "Error in payment verification";
        $summary = 'Error';        
        $pay_status = 2;
        $payLog = json_encode($response);
        
        if( property_exists($response, 'status') ){ 
            
            $summary = $response->status;

            if( strtoupper($response->status) == 'CAPTURED'){
                $pay_status = 1;
                $msg = "Payment successfully confirmed"; 
                
                $cardDet = $response->transaction[0]->sourceOfFunds->provided->card;
                $verifyData = [
                    'paymentInformation'=> serialize($response),
                    'OrderID'=> $response->id,
                    'OrderRefNo'=> $response->reference,
                    'TransRefNo'=> $response->transaction[0]->transaction->reference,
                    'cardBrand'=> $cardDet->brand,
                    'nameOnCard'=> $cardDet->nameOnCard,
                    'Cardnumber'=> $cardDet->number,
                    'modifiedUserID' => $emp_id,
                    'modifiedDateTime' => $dateTime,
                    'timestamp' => $dateTime,
                    'isAmountPaid' => 1,
                    'paymentType' => 4,
                    'payRecDate' => $dateTime,
                    'modifiedPCID' => $pc,
                ];
                
                $where = ['companyID'=> $company_id, 'invID'=> $inv_id];
                $db2->where($where)->update('subscription_invoice_master', $verifyData);
                
                //inster to payment detail table            
                $recive_amount = $response->totalCapturedAmount;
                $db2->insert('subscription_invoice_payment_details', [
                    'inv_id'=> $inv_id, 'pay_type'=> 4, 'amount'=> $recive_amount,
                    'pay_date'=> $dateTime, 'narration'=> '',
                    'companyID'=> $company_id, 'createdPCID'=> $pc, 'createdUserID'=> $emp_id,
                    'createdDateTime'=> $dateTime, 'timestamp'=> $dateTime
                ]);            
                $payID = $db2->insert_id();                   
            }                            
        }
         

        $insertData = [
            'invID'=> $inv_id, 'summary'=> $summary, 'payLog'=> $payLog, 'paymentType'=> 4, 
            'payStatus'=> $pay_status, 'companyID'=> $company_id, 'createdPCID'=> $pc, 
            'createdUserID'=> $emp_id, 'createdDateTime'=> $dateTime, 'timestamp'=> $dateTime
        ];

        $db2->insert('payment_log', $insertData);

        $db2->trans_complete();
        if($db2->trans_status() == true){
            $this->db->trans_commit();
            $payment_det = $this->get_payment_detail_view(4, $dateTime);
            $res_status = ($pay_status == 1)? 's': 'e';

            echo json_encode([ $res_status, $msg, 'payment_det'=> $payment_det, 'payID'=> $payID ]);
        }else{
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in payment confirmation']);
        }

    }

    function pay_debicardpayment()
    {
        $invoiceID = $this->input->post('invoiceID');
        $resourcePath = $this->config->item('gateway.resource.path');
        $javabridgepath = $this->config->item('php.java.bridge.url');
        $aliasName = $this->config->item('aliasName');
        $action = $this->config->item('tran.action');
        $companyID = current_companyID();
        $language = $this->config->item('language_omannet');
        $receiptURL = $this->config->item('receiptURL');
        $errorURL = $this->config->item('errorURL');
        $udf3 = $this->config->item('udf3');
        $current_userID = current_userID();
        $db2 = $this->load->database('db2', TRUE);
        $invoiceamount = $db2->query("SELECT
	                                      IFNULL(amount,0) as amount,
	                                      com.company_name,	
                                          com.companyPrintAddress,
                                          inv_master.invID
                                          FROM
	                                      subscription_invoice_master AS inv_master 
	                                      LEFT JOIN  subscription_invoice_details AS inv_det ON inv_det.invID = inv_master.invID
	                                      JOIN srp_erp_company AS com ON com.company_id = inv_master.companyID
                                          WHERE
                                          inv_det.companyID = $companyID
	                                      AND inv_det.invID = $invoiceID 
	                                      GROUP BY
	                                      inv_det.invID")->row_array();



        $rnd = substr(number_format(time() * rand(),0,'',''),0,10);
        $trackid = $rnd;
        $_amount = $this->subscription_invoice_balance($invoiceID);

        /*$db2->query("UPDATEs
                     subscription_invoice_master
                     SET
                     OrderID = '{$trackid}'
                     WHERE
                     invID = {$invoiceID}");*/

                     


        require_once($javabridgepath);
        $myObj = new Java("com.fss.plugin.iPayPipe");
        $myObj->setResourcePath(trim($resourcePath));
        $myObj->setKeystorePath(trim($resourcePath));
        $myObj->setAlias(trim($aliasName));
        $myObj->setAction(trim($action));
        $myObj->setCurrency("512");
        $myObj->setLanguage(trim($language));
        $myObj->setResponseURL(trim($receiptURL));
        $myObj->setErrorURL(trim($errorURL));
        $myObj->setAmt($_amount);
        $myObj->setTrackId($trackid);
        $myObj->setUdf1("");
        $myObj->setUdf2("");
        $myObj->setUdf3($udf3);
        $myObj->setUdf4("");
        $myObj->setUdf5("");

        $data['url']='';
        if(trim($myObj->performPaymentInitializationHTTP())!=0)
        {
            echo("ERROR OCCURED! SEE CONSOLE FOR MORE DETAILS");
            return -1;
            exit();
        }
        else
        {
            $payID = $myObj->getPaymentId();
            $payURL =$myObj->getPaymentPage();
            $url=$myObj->getWebAddress();
            $data['url']= $url;
            $update_data = ['mrchTrackID' => $trackid,'issendemailYN'=>0];
            $db2->where('invID',$invoiceID)->update('subscription_invoice_master', $update_data);
            
        }

        echo  $data['url'];
    }

    function subscription_invoice_balance($inv_id){
        $db2 = $this->load->database('db2', TRUE);

        $invTot = $db2->select('invTotal')->where('invID', $inv_id)
                      ->get('subscription_invoice_master')->row('invTotal');

        $paidSum = $db2->select('SUM(amount) AS paidSum')->where('inv_id', $inv_id)
                       ->get('subscription_invoice_payment_details')->row('paidSum');

        return $invTot - $paidSum;
    }

    function subscription_result()
    {
        
        $company_id = current_companyID();
        $current_userID = current_userID();
        $resourcePath = $this->config->item('gateway.resource.path');
        $javabridgepath = $this->config->item('php.java.bridge.url');
        $aliasName = $this->config->item('aliasName');
        $getUserEmail = $this->db->query("SELECT EEmail FROM srp_employeesdetails WHERE Erp_companyID = $company_id AND EIdNo = $current_userID")->row('EEmail');
        $companyInfo = get_companyInfo();
        $productID = $companyInfo['productID'];
        $this->load->library('s3');
        $logo= LOGO;
        $data['productlogo']  = $this->s3->createPresignedRequest('images/'.$logo.'', '1 hour');
        $data['visa'] = $this->s3->createPresignedRequest('images/visa.png', '1 hour');
        require_once($javabridgepath);
        #java_require('http://localhost//iPAYPlugin//iPayPipe.jar');
        $myObj = new Java("com.fss.plugin.iPayPipe");
        $myObj->setKeystorePath(trim($resourcePath));
        $myObj->setAlias(trim($aliasName));
        $myObj->setResourcePath(trim($resourcePath));
        $data['TransactionStatus'] = '';
        $data['PostDate'] = '';
        $data['TransactionRefeID'] = '';
        $data['MrchTrackID'] = 0;
        $data['TransactionID'] = '';
        $data['TransAmount'] = '';
        $data['PaymentID'] = '';
        $data['error'] = '';
        $data['invoieID'] = '';
        $data['transid']  = $_GET['transid'];
    
        
        if(isset($data['transid']) && trim($myObj->parseEncryptedRequest(trim($data['transid'] ?? '')))!=0) {
            $data['error'] = $myObj->getError();          
            $data['MrchTrackID'] = $myObj->getTrackId();
        }
        else {
            if(!isset($_GET["ErrorText"]) && isset($_GET["result"])){

                $data['TransactionStatus'] = trim($_GET["result"]);
                $data['PostDate'] = trim($_GET["postdate"]);
                $data['TransactionRefeID'] = trim($_GET["ref"]);
                $data['MrchTrackID'] = trim($_GET["trackid"]);
                $data['TransactionID'] = trim($_GET["tranid"]);
                $data['TransAmount'] = trim($_GET["amt"]);
                $data['PaymentID'] = trim($_GET["paymentid"]);
            }
            else if(!isset($_GET["ErrorText"]) && !isset($_GET["result"])) {

                $data['error'] = $myObj->getError();
                $data['TransactionStatus'] = $myObj->getResult();
                $data['PostDate'] = $myObj->getDate();
                $data['TransactionRefeID'] = $myObj->getRef();
                $data['MrchTrackID'] = $myObj->getTrackId();
                $data['TransactionID'] = $myObj->getTransId();
                $data['TransAmount'] = $myObj->getAmt();
                $data['PaymentID'] = $myObj->getPaymentId(); 
            }
            else {
                $data['error'] =  $_GET["ErrorText"]; 
                $data['MrchTrackID'] = trim($_GET["trackid"]);               
            }
        }
        
        $payLog = $data['error'];
        $payStatus = 2;        
        $summary = $data['TransactionStatus'];
        $date_time = current_date();
        $pc = current_pc();

        $db2 = $this->load->database('db2', TRUE);
        
        $data['master_data'] = $db2->query("SELECT inv_mas.invNo, inv_mas.invTotal, inv_mas.invDecPlace, cur_mas.CurrencyCode,
                      inv_mas.createdDateTime, com.company_name, companyPrintAddress, inv_mas.isAmountPaid,  
                      inv_mas.invID, inv_mas.paymentType, inv_mas.payRecDate, inv_mas.invCur,issendemailYN
                      FROM subscription_invoice_master AS inv_mas 
                      JOIN srp_erp_company AS com ON com.company_id = inv_mas.companyID
                      JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID=inv_mas.invCur
                      WHERE inv_mas.mrchTrackID = '{$data['MrchTrackID']}' AND inv_mas.companyID = {$company_id}")->row_array();

        $inv_id = (!empty($data['master_data']))? $data['master_data']['invID']: 0;
        
        if($data['TransactionStatus'] == 'CAPTURED'){
            $payStatus = 1;
            $payLog = json_encode($myObj);

            $update_data_email_payment = ['paymentInformation'=>$data['transid'],'OrderID'=>$data['TransactionID'],'TransRefNo'=>$data['TransactionRefeID'],
                                           'postDate'=>$data['PostDate'],'transactionStatus'=>$data['TransactionStatus'],'mrchTrackID'=>$data['MrchTrackID'],
                                            'paymentID'=>$data['PaymentID'],'isAmountPaid'=>1,'paymentType'=>5
                                        ];
            $db2->where('mrchTrackID',$data['MrchTrackID'])->update('subscription_invoice_master', $update_data_email_payment);


            //inster to payment detail table 
            $db2->insert('subscription_invoice_payment_details', [
                'inv_id'=> $inv_id, 'pay_type'=> 5, 'amount'=> $data['TransAmount'],
                'pay_date'=> $date_time, 'narration'=> '',
                'companyID'=> $company_id, 'createdPCID'=> $pc, 'createdUserID'=> $emp_id,
                'createdDateTime'=> $date_time, 'timestamp'=> $date_time
            ]);
        }

        $data['det_data'] = $db2->query("SELECT amountBeforeDis, amount, discountPer, discountAmount, inv_det.itemDescription  
                    FROM subscription_invoice_details AS inv_det
                    LEFT JOIN system_invoice_item_type AS item_type ON item_type.type_id=inv_det.itemID
                    WHERE inv_det.invID = '{$data['master_data']['invID']}' AND inv_det.companyID = {$company_id}")->result_array();

        
        $insertData = [
            'invID'=> $inv_id, 'summary'=> $summary, 'payLog'=> $payLog, 'paymentType'=> 5, 
            'payStatus'=> $payStatus, 'companyID'=> $company_id, 'createdPCID'=> $pc, 
            'createdUserID'=> $current_userID, 'createdDateTime'=> $date_time, 'timestamp'=> $date_time
        ];
            
        $db2->insert('payment_log', $insertData);   


        $data['isview'] = 0;

        if($data['master_data']['issendemailYN']!=1)
        { 
            $this->load->library('email_manual');
            $config['charset'] = "utf-8";
            $config['mailtype'] = "html";
            $config['wordwrap'] = TRUE;
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = $this->config->item('email_smtp_host');
            $config['smtp_user'] = $this->config->item('email_smtp_username');
            $config['smtp_pass'] = $this->config->item('email_smtp_password');
            $config['smtp_crypto'] = 'tls';
            $config['smtp_port'] = '587';
            $config['crlf'] = "\r\n";
            $config['newline'] = "\r\n";
            $this->load->library('email', $config);
            if(hstGeras==1){
                $this->email->from($this->config->item('email_smtp_from'), EMAIL_SYS_NAME);
            }else{
                $this->email->from($this->config->item('email_smtp_from'), EMAIL_SYS_NAME);
            }
            $this->email->to($getUserEmail);
            $this->email->subject('SPUR Payment');
            $this->email->message($this->load->view('system/company/subscription-invoice-payment.php', $data, TRUE));
            $this->email->send();
        }
        $update_data_email = ['issendemailYN'=>1];
        $db2->where('mrchTrackID',$data['MrchTrackID'])->update('subscription_invoice_master', $update_data_email);
    
        $data['isview'] = 1;

        $this->load->view('system/company/subscription-invoice-payment.php',$data);
 
    }

    function credit_card_receipt_view()
    { 
        $db2 = $this->load->database('db2', TRUE);
        $company_id = current_companyID();
        $companyInfo = get_companyInfo();
        $current_userID = current_userID();
        $invoiceID = $this->input->post('invoiceID');
        $payID = $this->input->post('payID');
        $data['results']  = $this->input->post('results');
        $getUserEmail = $this->db->query("SELECT EEmail FROM srp_employeesdetails WHERE Erp_companyID = $company_id AND EIdNo = $current_userID")->row('EEmail');
        $productID = $companyInfo['productID'];
        $this->load->library('s3');
        $logo= LOGO;
        $data['productlogo']  = $this->s3->createPresignedRequest('images/'.$logo.'', '1 hour');
        $data['visa'] = $this->s3->createPresignedRequest('images/visa.png', '1 hour');

        $data['master_data'] = $db2->query("SELECT inv_mas.invNo, inv_mas.invTotal, inv_mas.invDecPlace, cur_mas.CurrencyCode,
                      inv_mas.createdDateTime, com.company_name, companyPrintAddress, inv_mas.isAmountPaid,  
                      inv_mas.invID, inv_mas.paymentType, inv_mas.payRecDate, inv_mas.invCur,issendemailYN,TransRefNo
                      FROM subscription_invoice_master AS inv_mas 
                      JOIN srp_erp_company AS com ON com.company_id = inv_mas.companyID
                      JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID=inv_mas.invCur
                      WHERE  inv_mas.companyID = {$company_id} AND inv_mas.invID = '{$invoiceID}' ")->row_array();
       
        $data['inv_pay_amount'] = null;
        if($payID > 0){
            $data['inv_pay_amount'] = $db2->get_where('subscription_invoice_payment_details', ['payAutoID'=>$payID])->row('amount');
        }

       $data['det_data'] = $db2->query("SELECT amountBeforeDis, amount, discountPer, discountAmount, inv_det.itemDescription  
        FROM subscription_invoice_details AS inv_det
        LEFT JOIN system_invoice_item_type AS item_type ON item_type.type_id=inv_det.itemID
        WHERE inv_det.invID = '{$invoiceID}' AND inv_det.companyID = {$company_id}")->result_array();
     
        $data['isview'] = 0;
        $this->load->library('email_manual');
         $config['charset'] = "utf-8";
         $config['mailtype'] = "html";
         $config['wordwrap'] = TRUE;
         $config['protocol'] = 'smtp';
         $config['smtp_host'] = 'smtp.sendgrid.net';
         $config['smtp_user'] = 'apikey';
         //$config['smtp_pass'] = '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';ee
         $config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
         $config['smtp_crypto'] = 'tls';
         $config['smtp_port'] = '587';
         $config['crlf'] = "\r\n";
         $config['newline'] = "\r\n";
         $this->load->library('email', $config);
         if(hstGeras==1){
             $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
         }else{
             $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
         }
         $this->email->to($getUserEmail);
         $this->email->subject('SPUR Payment');
         $this->email->message($this->load->view('system/company/subscription-invoice-paymentcreditcard.php', $data, TRUE));
         $this->email->send();


         $data['isview'] = 1;
         $this->load->view('system/company/subscription-invoice-paymentcreditcard.php',$data);
    }

    function subscription_payment_succes($id){
        die(' id : '. $id);
    }

    function addTo_error_log(){
        $company_id = current_companyID();
        $user_id = current_userID();
        $date_time = current_date();
        $pc = current_pc();

        $inv_id = $this->input->post('invID');
        $pay_type = $this->input->post('pay_type');
        $error_log = $this->input->post('log');
    
        $msg = 'There is a issue in payment gateway, Please try again later.';
        $error_type = 'Undefined error';

        switch($pay_type){
            case 2:
                $error_type = 'Undefined error';      
                if(!empty($error_log)){  
                    $error_log_arr = json_decode($error_log);
        
                    if( array_key_exists('cause', $error_log_arr) ){
                        $error_type = $error_log_arr->cause;           
                    }
                }
            break;

            case 3:            
                $error_type = 'Cancelled by user';
                $msg = '';
            break;

            case 4:                
                $error_type = 'Time out';
                $msg = 'Time out';
            break;            
        }

            
        $insertData = [
            'invID'=> $inv_id, 'summary'=> $error_type, 'payLog'=> $error_log, 'paymentType'=> 4, 
            'payStatus'=> $pay_type, 'companyID'=> $company_id, 'createdPCID'=> $pc, 
            'createdUserID'=> $user_id, 'createdDateTime'=> $date_time, 'timestamp'=> $date_time
        ];

        $db2 = $this->load->database('db2', TRUE);
        $db2->insert('payment_log', $insertData);        

        echo json_encode(['msg'=> $msg] );
    }

    function update_postDate(){
        echo json_encode($this->Company_model->update_postDate());
    }

    function fetch_audit_log(){
        $date_format_policy = date_format_policy();
        $date_from = $this->input->post('date_from');
        $date_from_convert = input_format_date($date_from, $date_format_policy);
        $date_to = $this->input->post('date_to');
        $date_to_convert = input_format_date($date_to, $date_format_policy);
        $date_filter = (!empty($date_from) && !empty($date_to))? " AND ( DATE(createdDateTime) BETWEEN '{$date_from_convert}' AND '{$date_to_convert}')" : '';


        $employee = $this->input->post('employee');
        $employee_filter = (!empty($employee))? " AND EIdNo IN ({$employee})": '';

        $companyID = current_companyID();

        $where = "companyID = " . $companyID . $employee_filter .  $date_filter ."";

        $db2 = $this->load->database('db2', TRUE);
        $main_db = $db2->database;
        $this->datatables->select("auditlogID,empDetailTBL.Ecode,Ename2,IFNULL(documentID,IF(transactionType=0,'Logged In to system',IF(transactionType=2,'Logged out from the system',documentID))) as documentID,createdDateTime");
        $this->datatables->from("$main_db.system_audit_log auditTBL");
        $this->datatables->join("srp_employeesdetails  empDetailTBL","empDetailTBL.EIdNo = auditTBL.empID");
        $this->datatables->where($where);
        echo $this->datatables->generate();
    }


    function fetch_control_account()
    {
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);

        $this->datatables->select('controlAccountsAutoID,controlAccountType,controlAccountDescription, 
        controlaccounts.systemAccountCode as systemAccountCode, chartofaccounts.GLAutoID as GLAutoID, chartofaccounts.GLSecondaryCode,chartofaccounts.GLDescription,chartofaccounts.controllAccountYN as controllAccountYN');
        $this->datatables->from('srp_erp_companycontrolaccounts controlaccounts');
        $this->datatables->join('srp_erp_chartofaccounts chartofaccounts', 'chartofaccounts.GLAutoID = controlaccounts.GLAutoID', 'INNER');
        $this->datatables->where('controlaccounts.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('edit', '$1', 'load_controlaccount_status(controlAccountsAutoID,GLAutoID,controllAccountYN)');

        echo $this->datatables->generate();
    }

    function fetch_control_account_log()
    {
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);
        $companyID = current_companyID();
        $controlAccount = $this->input->post('controlAccounDrop');
        $controlAccount_filter = '';
        if (!empty($controlAccount)) {
            $controlAccount_arr = explode(',', $controlAccount);
            $whereIN = "( ".('"' . join('","', $controlAccount_arr) . '"'). " )";
            $controlAccount_filter = " AND controlaccounts.controlAccountsAutoID IN " . $whereIN;
        }
        $where = "log.companyID = " . $companyID . $controlAccount_filter ;

        $this->datatables->select("controlAccountLogAutoID,
        controlaccounts.controlAccountType,
        controlaccounts.controlAccountDescription ,
        chartofaccounts.systemAccountCode,
        chartofaccounts.GLSecondaryCode,
        chartofaccounts.GLDescription,
        log.createdUserName,
        DATE_FORMAT(log.createdDateTime, '%Y-%m-%d') AS createdDateTimeformat,
        log.createdDateTime as createdDateTime , 
        status");
        $this->datatables->from('control_account_log log');
        $this->datatables->join('srp_erp_chartofaccounts chartofaccounts', 'chartofaccounts.GLAutoID = log.GLAutoID', 'LEFT');
        $this->datatables->join('srp_erp_companycontrolaccounts controlaccounts', 'controlaccounts.controlAccountsAutoID = log.controlAccountsAutoID ' , 'LEFT');
        $this->datatables->edit_column('status', '$1', 'controlAcountLogStatus(status)');
        $this->datatables->where($where);
        echo $this->datatables->generate();
    }

    public function statusChangeControlAccount()
    {
        $this->form_validation->set_rules('controlAccountsAutoID', 'controlAccountsAutoID', 'required');
        $this->form_validation->set_rules('GLAutoID', 'GLAutoID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Company_model->statusChangeControlAccount());
        }
    }

    function export_excel_controlaccountlog()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Control Account Log');
        $this->load->database();
        $data = $this->Company_model->fetch_controlaccountlog_excel();

        $header = ['#', 'Control Account Type', 'Control Account Description', 'GL System Code','GL Code', 'GL Description', 'Status', 'Created User', 'Created Date'];
       
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->mergeCells("A1:E1");
        $this->excel->getActiveSheet()->mergeCells("A2:E2");

        $this->excel->getActiveSheet()->getStyle('A4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Control Account Log List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:I4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:I4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');

        $y=6;
        foreach ($data as $val) {
            $this->excel->getActiveSheet()->setCellValue('A' . $y, $val['Num']);
            $this->excel->getActiveSheet()->setCellValue('B' . $y, $val['controlAccountType']);
            $this->excel->getActiveSheet()->setCellValue('C' . $y, $val['controlAccountDescription']);
            $this->excel->getActiveSheet()->setCellValue('D' . $y, $val['systemAccountCode']);
            $this->excel->getActiveSheet()->setCellValue('E' . $y, $val['GLSecondaryCode']);
            $this->excel->getActiveSheet()->setCellValue('F' . $y, $val['GLDescription']);
            $this->excel->getActiveSheet()->setCellValue('G' . $y, $val['status']);
            $this->excel->getActiveSheet()->setCellValue('H' . $y, $val['createdUserName']);
            $this->excel->getActiveSheet()->setCellValue('I' . $y, $val['createdDateTime']);
            
            $y++;
        }

        $filename = 'Control Account Log.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function export_excel_controlaccount()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Control Account');
        $this->load->database();
        $data = $this->Company_model->fetch_controlaccount_excel();
        
        $header = ['#', 'Document Code', 'Control Account Description', 'GL System Code','GL Code', 'GL Description'];
       
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->mergeCells("A1:E1");
        $this->excel->getActiveSheet()->mergeCells("A2:E2");

        $this->excel->getActiveSheet()->getStyle('A4:F4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Control Account List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');

        $y=6;
        foreach ($data as $val) {
            $this->excel->getActiveSheet()->setCellValue('A' . $y, $val['Num']);
            $this->excel->getActiveSheet()->setCellValue('B' . $y, $val['controlAccountType']);
            $this->excel->getActiveSheet()->setCellValue('C' . $y, $val['controlAccountDescription']);
            $this->excel->getActiveSheet()->setCellValue('D' . $y, $val['systemAccountCode']);
            $this->excel->getActiveSheet()->setCellValue('E' . $y, $val['GLSecondaryCode']);
            $this->excel->getActiveSheet()->setCellValue('F' . $y, $val['GLDescription']);
                    
            $y++;
        }

        $filename = 'Control Account Log.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function export_excel_audit_log()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Audit Log');
        $this->load->database();
        $data = $this->Company_model->fetch_audit_log_excel();
        
        $header = ['#', 'EMployee Short Code', 'Employee Name', 'Document','Date'];
       
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->mergeCells("A1:E1");
        $this->excel->getActiveSheet()->mergeCells("A2:E2");

        $this->excel->getActiveSheet()->getStyle('A4:E4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Audit Log List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:E4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');

        $y=6;
        foreach ($data as $val) {
            $this->excel->getActiveSheet()->setCellValue('A' . $y, $val['Num']);
            $this->excel->getActiveSheet()->setCellValue('B' . $y, $val['Ecode']);
            $this->excel->getActiveSheet()->setCellValue('C' . $y, $val['Ename2']);
            $this->excel->getActiveSheet()->setCellValue('D' . $y, $val['documentID']);
            $this->excel->getActiveSheet()->setCellValue('E' . $y, $val['createdDateTime']);
                    
            $y++;
        }

        $filename = 'Control Account Log.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function save_invoice_template() {

        $this->form_validation->set_rules('template_name', 'Template Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode("error");
        } else {
            echo json_encode($this->Company_model->save_invoice_template());
        }

    }

    function fetch_invoice_templates(){
        $data['detail'] = $this->Company_model->fetch_invoice_templates();   
        //var_dump($data);
        $this->load->view('system/srm/customer-order/ajax/load_generated_invoice_template_table', $data);
    }

    function get_invoice_template(){

        $companyID = current_companyID();
        $invoiceTemplateMasterID = $this->input->post('invoiceTemplateMasterID');
        $invoiceTemplateDetails = $this->db->query("SELECT * FROM srp_erp_invoicetemplatemaster WHERE companyID = {$companyID} 
                                      AND invoiceTemplateMasterID = {$invoiceTemplateMasterID}")->row_array();
       
        echo json_encode($invoiceTemplateDetails);
        //var_dump($invoiceTemplateDetails);
        
    }

    function change_status_company_invoice_template(){
        $invoiceTemplateMasterID = $this->input->post('invoiceTemplateMasterID');
        $temp_status = $this->input->post('temp_status');
        if ($invoiceTemplateMasterID) {
            $data = array(
                'status' => $temp_status
            );
            $this->db->where('invoiceTemplateMasterID', $invoiceTemplateMasterID);
            $result = $this->db->update('srp_erp_invoicetemplatemaster', $data);
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }        
    }

    function update_company_invoice_template(){
        $invoiceTemplateMasterID = $this->input->post('invoice_template_masterID');
        $data['invoiceTemplateName'] = $this->input->post('template_name_edit');
        $data['customerName'] = $this->input->post('customer_name_edit');
        $data['customerAddress'] = $this->input->post('customer_address_edit');
        $data['customerTelephone'] = $this->input->post('customer_tel_edit');
        $data['contactPerson'] = $this->input->post('contact_person_name_edit');
        $data['contactPersonTel'] = $this->input->post('contact_person_tel_edit');
        $data['customerVatNo'] = $this->input->post('customer_vat_edit');
        $data['narration'] = $this->input->post('contact_narration_edit');
        $data['documentDate'] = $this->input->post('document_date_edit');

        $data['referenceNumber'] = $this->input->post('reference_number_edit');
        $data['currency'] = $this->input->post('currency_edit');
        $data['invoiceNumber'] = $this->input->post('invoice_no_edit');
        $data['invoiceDate'] = $this->input->post('invoice_date_edit');
        $data['invoiceDueDate'] = $this->input->post('invoice_due_date_edit');

        $data['topHeight'] = $this->input->post('top_height_edit');
        $data['bottomHeight'] = $this->input->post('bottom_height_edit');
        $data['leftWidth'] = $this->input->post('left_width_edit');
        $data['rightWidth'] = $this->input->post('right_width_edit');        
        
        if ($invoiceTemplateMasterID) {            
            $this->db->where('invoiceTemplateMasterID', $invoiceTemplateMasterID);
            $result = $this->db->update('srp_erp_invoicetemplatemaster', $data);
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }        
    }

    function delete_company_invoice_template(){
        $invoiceTemplateMasterID = $this->input->post('invoiceTemplateMasterID');

        if ($invoiceTemplateMasterID) {
            $this->db->delete('srp_erp_invoicetemplatemaster', array('invoiceTemplateMasterID' => trim($invoiceTemplateMasterID)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }        
    }
}
