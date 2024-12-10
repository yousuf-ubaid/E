<?php
class ReportTemplate extends ERP_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Report_template_model');
    }

    function fetch_company_report_template_table()
    {
        $companyID = current_companyID();
        $group_masterID = getParentgroupMasterID();
        if($group_masterID==0)
        {
        $templateType = $this->input->post('templateType');
        }
        else{
        $templateType = 2;
        }
      
        
        $str = '<div class="pull-right"><a onclick="setupTemplate($1, \'$2\',$3)"><span title="Config" rel="tooltip" class="glyphicon glyphicon-cog" ></span></a>&nbsp;|&nbsp;';
        $str.= '<a onclick="delete_report_template_master($1)"><span title="Delete" style="color:rgb(209, 91, 71);" rel="tooltip" class="glyphicon glyphicon-trash" ></span></a></div>';
       
        $this->datatables->select('companyReportTemplateID,description,srp_erp_companyreporttemplate.reportID,srp_erp_reporttemplate.reportDescription as reportDescription,srp_erp_companyreporttemplate.templateType as templateType')
            ->from('srp_erp_companyreporttemplate')
            ->join('srp_erp_reporttemplate', 'srp_erp_companyreporttemplate.reportID = srp_erp_reporttemplate.reportID')
            ->where('srp_erp_companyreporttemplate.templateType', $templateType);

        if($templateType == 1){ //Fund management
            $this->datatables->where('srp_erp_companyreporttemplate.companyID', $companyID);
        }
        else{ // MPR
            $companyType = $this->session->userdata('companyType');
            $companyID = ($companyType == 1)? $companyID: $group_masterID;

            $this->datatables->where('srp_erp_companyreporttemplate.companyType', $companyType)->where('srp_erp_companyreporttemplate.companyID', $companyID);
        }


        $this->datatables->add_column('edit', $str, 'companyReportTemplateID, description,templateType');
        echo $this->datatables->generate();
    }

    function save_reportTemplateMaster()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('reportID', 'Type', 'trim|required');
        $this->form_validation->set_rules('templateType', 'Template Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_template_model->save_reportTemplateMaster());
        }
    }

    function delete_report_template_master()
    {
        $templateID = trim($this->input->post('companyReportTemplateID') ?? '');

        $isAssigned = $this->db->query("SELECT company_name FROM srp_erp_fm_companymaster 
                          WHERE (incomeStatementID = {$templateID} OR balanceSheet = {$templateID})")->result_array();

        if(!empty($isAssigned)){
            $msg = 'This template is assigned for following company.</br> &nbsp; &nbsp; -  ';
            $msg .= implode('</br> &nbsp; &nbsp; -  ', array_column($isAssigned, 'company_name'));
            $msg .= '<br/>You can not delete this template.';
            die( json_encode(['e', $msg]) );
        }


        $isAssigned = $this->db->query("SELECT documentCode FROM srp_erp_fm_financialsmaster 
                                        WHERE templateID = {$templateID}")->result_array();

        if(!empty($isAssigned)){
            $msg = 'Following submission made on this template.</br> &nbsp; &nbsp; -  ';
            $msg .= implode('</br> &nbsp; &nbsp; -  ', array_column($isAssigned, 'documentCode'));
            $msg .= '<br/>You can not delete this template.';
            die( json_encode(['e', $msg]) );
        }

        $this->db->trans_start();

        $this->db->delete('srp_erp_companyreporttemplatelinks', ['templateMasterID' => $templateID]);
        $this->db->delete('srp_erp_companyreporttemplatedetails', ['companyReportTemplateID' =>  $templateID]);
        $this->db->delete('srp_erp_companyreporttemplate', ['companyReportTemplateID' => $templateID]);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Deleted Successfully']);
        }else{
            echo json_encode(['e', 'Error in deletion.']);
        }
    }

    function save_reportTemplateDetail()
    {

        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('itemType', 'Type', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'Sort Order', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Report_template_model->save_reportTemplateDetail());
        }
    }

    function update_reportTemplateDetail()
    {

        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('edit_id', 'Auto ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'Sort Order', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }


        $dateTime = current_date();
        $companyID = current_companyID();

        $masterID = trim($this->input->post('masterID') ?? '');
        $edit_id = trim($this->input->post('edit_id') ?? '');

        $data['description'] = trim($this->input->post('description') ?? '');
        $data['sortOrder'] = trim($this->input->post('sortOrder') ?? '');
        $data['modifiedPCID'] = current_pc();
        $data['modifiedUserID'] = current_userID();
        $data['modifiedUserName'] = current_employee();
        $data['modifiedDateTime'] = $dateTime;
        $data['timestamp'] = $dateTime;


        $where = ['detID'=>$edit_id, 'companyReportTemplateID'=>$masterID, 'companyID'=>$companyID];

        $this->db->trans_start();

        $this->db->where($where)->update('srp_erp_companyreporttemplatedetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo json_encode( ['e', 'Template Save Failed'] );
        } else {
            echo json_encode( ['s', 'Template Saved Successfully'] );
        }

    }

    function load_templateConfig(){
        $id = trim($this->input->post('id') ?? '');
        $itemType = trim($this->input->post('itemType') ?? '');

        $masterDesc = $this->db->query("SELECT mas.description , det.description detDes
                            FROM srp_erp_companyreporttemplatedetails det 
                            JOIN srp_erp_companyreporttemplate mas ON mas.companyReportTemplateID = det.companyReportTemplateID
                            WHERE detID = {$id} ")->row_array();

        $data['masterDesc'] = $masterDesc;
        $this->load->view('system/ReportTemplate/ajax/template-config', $data);
    }

    function delete_reportTemplateDetail(){
        die( json_encode(array('e', ' Error in Deletion.', false)) );
        $this->db->delete('srp_erp_companyreporttemplatelinks', array('detID' => trim($this->input->post('detID') ?? '')));
        $status=$this->db->delete('srp_erp_companyreporttemplatedetails', array('detID' => trim($this->input->post('detID') ?? '')));
        if($status){
            echo json_encode(array('s', ' Deleted Successfully.', $status));
        }else {
            echo json_encode(array('e', ' Error in Deletion.', $status));
        }
    }

    function save_reportTemplateLink()
    {
        $glAutoID = $this->input->post('glAutoID');
        $linkType = $this->input->post('linkType');

        $msg = '';
        if(empty($glAutoID)){
            $msg = ($linkType == 'S')? 'GL': 'sub category';
            $msg = '<p>Select at least one '.$msg.'</p>';
        }


        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        $this->form_validation->set_rules('detID', 'Detail ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $msg .= validation_errors();
        }

        if($msg != ''){
            die( json_encode(['e', $msg]));
        }

        echo json_encode($this->Report_template_model->save_reportTemplateLink());
    }

    function fetch_company_report_template_links_table(){
        $companyID = current_companyID();
        $detID = $this->input->post('detID');
        $this->datatables->select('linkID,detID,sortOrder,srp_erp_companyreporttemplatelinks.glAutoID,srp_erp_chartofaccounts.GLDescription as GLDescription')
            ->where('srp_erp_companyreporttemplatelinks.companyID', $companyID)
            ->where('detID', $detID)
            ->from('srp_erp_companyreporttemplatelinks')
            ->join('srp_erp_chartofaccounts', 'srp_erp_companyreporttemplatelinks.glAutoID = srp_erp_chartofaccounts.GLAutoID');
        $this->datatables->add_column('edit', '<a onclick="delete_report_tempalte_Link($1)"><span title="Delete" style="color:rgb(209, 91, 71);" rel="tooltip" class="glyphicon glyphicon-trash" ></span></a>', 'linkID');
        echo $this->datatables->generate();
    }

    function load_gl_drop(){
        $data['companyReportTemplateID'] = $this->input->post('companyReportTemplateID');
        $html = $this->load->view('system/ReportTemplate/ajax/ajax-erp_load_gldescription', $data, true);
        echo $html;
    }

    function load_gl_data(){
        $templateID = $this->input->post('companyReportTemplateID');
        $data['templateID'] = $templateID;
        $data['reqType'] = $this->input->post('reqType');
        $data['id'] = $this->input->post('id');

        $master_data = $this->db->query("SELECT templateType, companyType, reportID, companyID FROM srp_erp_companyreporttemplate WHERE companyReportTemplateID = {$templateID}")->row_array();
        $data['master_data'] = $master_data;
        $html = $this->load->view('system/ReportTemplate/ajax/gl-config-view', $data, true);
        echo $html;
    }

    function delete_report_tempalte_link(){
        $status=$this->db->delete('srp_erp_companyreporttemplatelinks', array('linkID' => trim($this->input->post('linkID') ?? '')));
        if($status){
            echo json_encode(array('s', ' Deleted Successfully.', $status));
        }else {
            echo json_encode(array('e', ' Error in Deletion.', $status));
        }
    }

    function update_sortOrder()
    {
        $this->form_validation->set_rules('detSort[]', 'Sort Order', 'trim|required');
        if(array_key_exists('glSort', $_POST)){
            $this->form_validation->set_rules('glSort[]', 'Sort Order', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', 'Please fill all fields']) );
        }

        $dateTime = current_date();
        $detSortID = $this->input->post('detSortID');
        $detSort = $this->input->post('detSort');
        $glSortID = $this->input->post('glSortID');
        $glSort = $this->input->post('glSort');


        $this->db->trans_start();
        if(!empty($glSortID)){
            foreach ($glSortID as $key=>$row){
                $data[] = [
                    'linkID' => $row,
                    'sortOrder' => $glSort[$key],
                    'modifiedPCID' => current_pc(),
                    'modifiedUserID' => current_userID(),
                    'modifiedUserName' => current_employee(),
                    'modifiedDateTime' => $dateTime,
                    'timestamp' => $dateTime
                ];
            }

            $this->db->update_batch('srp_erp_companyreporttemplatelinks', $data, 'linkID');
        }


        if(!empty($detSortID)) {
            $data = [];
            foreach ($detSortID as $key => $row) {
                $data[] = [
                    'detID' => $row,
                    'sortOrder' => $detSort[$key],
                    'modifiedPCID' => current_pc(),
                    'modifiedUserID' => current_userID(),
                    'modifiedUserName' => current_employee(),
                    'modifiedDateTime' => $dateTime,
                    'timestamp' => $dateTime
                ];
            }

            $this->db->update_batch('srp_erp_companyreporttemplatedetails', $data, 'detID');
        }

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Sort order updated']);
        }else{
            echo json_encode(['e', 'Error in process.']);
        }

    }

    function update_title(){
        $this->form_validation->set_rules('title_id', 'ID', 'trim|required');
        $this->form_validation->set_rules('title_str', 'Title', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }
        $accountTypeCatergoryID = '';

        $title_id = $this->input->post('title_id');
        $title_str = $this->input->post('title_str');
        $dateTime = current_date();
        $companyID = current_companyID();

        $accountTypeCatergory = $this->input->post('accountTypecater_edit');
        $grossrevenue = $this->input->post('Isdefault');
        if(!empty($accountTypeCatergory))
        {
            $result = $this->db->query("SELECT detID,defaultType FROM `srp_erp_companyreporttemplatedetails` WHERE companyID = {$companyID}  AND itemType = 1 AND detID != {$title_id} AND defaultType = {$accountTypeCatergory}")->row_array();

            if(!empty($result))
            {
                if($result['defaultType'] == 1)
                {
                    $catName = 'Uncategorized Income';
                }else
                {
                    $catName = 'Uncategorized Expense';
                }
                echo json_encode(['e',  $catName.' Type Is Already Assigned']);
                exit();
            }else
            {
                $accountTypeCatergoryID = $accountTypeCatergory;
            }
        }
        $grossrev = 0;

            if($grossrevenue == 1)
            {
                $result = $this->db->query("SELECT detID FROM `srp_erp_companyreporttemplatedetails` WHERE companyID = {$companyID} AND itemType = 3 AND detID != {$title_id} AND is_gross_rev = 1")->result_array();
                if(!empty($result))
                {
                    echo json_encode(['e','Gross Revenue Type Is Already Assigned']);
                    exit();
                }else
                {
                    $grossrev = 1;
                }

            }


        $data = [
            'description' => $title_str,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'defaultType' => $accountTypeCatergoryID,
            'is_gross_rev' => $grossrev,
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->trans_start();

        $this->db->where(['detID'=>$title_id, 'companyID'=> $companyID])->update('srp_erp_companyreporttemplatedetails', $data);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Sort order updated']);
        }else{
            echo json_encode(['e', 'Error in process.']);
        }
    }

    function update_templateDescription(){
        $masterID = $this->input->get('masterID');
        $description = trim($this->input->post('value') ?? '');
        $dateTime = current_date();
        $companyID = current_companyID();

        if ($description == '') {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Description is required.');
        }

        $isExist = $this->db->query("SELECT companyReportTemplateID FROM srp_erp_companyreporttemplate WHERE companyID={$companyID}
                                         AND description='{$description}'")->row('companyReportTemplateID');

        if (!empty($isExist) && $isExist != $masterID) {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('This description is already exist.');
        }

        $data = [
            'description' => $description,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->trans_start();

        $this->db->where(['companyReportTemplateID'=>$masterID, 'companyID'=> $companyID])->update('srp_erp_companyreporttemplate', $data);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Description updated']);
        }else{
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Error in description update process');
        }
    }

    function delete_template_data(){
        $this->form_validation->set_rules('linkID', 'Title', 'trim|required');
        $this->form_validation->set_rules('d_type', 'Title', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $deleteID = trim($this->input->post('linkID') ?? '');
        $d_type = trim($this->input->post('d_type') ?? '');

        $this->db->trans_start();

        if($d_type == 'GL' || $d_type == 'G-Link'){ /*sub category GL or sub category link for a Group*/
            $this->db->delete('srp_erp_companyreporttemplatelinks', ['linkID' => $deleteID]);
        }
        else if($d_type == 'S'|| $d_type == 'G'){ /*sub category or Group total*/
            $this->db->delete('srp_erp_companyreporttemplatelinks', ['templateDetailID' => $deleteID]);
            $this->db->delete('srp_erp_companyreporttemplatedetails', ['detID' => $deleteID]); /*Delete related GLs or sub categories */
        }
        else if($d_type == 'H'){ /*Header*/
            $subItems = $this->db->query("SELECT detID FROM srp_erp_companyreporttemplatedetails WHERE masterID = {$deleteID}")->result_array();

            if(!empty($subItems)){
                $subItems = array_column($subItems, 'detID');
                $this->db->where_in('templateDetailID', $subItems)->delete('srp_erp_companyreporttemplatelinks'); /*Delete related GL`s of sub items*/

                $subItems[] = $deleteID;
                $this->db->where_in('detID', $subItems)->delete('srp_erp_companyreporttemplatedetails');  /*Delete header with sub items*/
            }else{
                /*If sub item not exists delete header*/
                $this->db->delete('srp_erp_companyreporttemplatedetails', ['detID' => $deleteID]);
            }

        }


        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', ' Deleted Successfully.']);
        }else{
            echo json_encode(['e', ' Error in Deletion.']);
        }
    }

    function update_gross_revenue_column(){
        $masterID = $this->input->post('masterID');
        $gross_column = $this->input->post('gross_column');

        $this->db->trans_start();

        $this->db->where(['companyReportTemplateID' => $masterID])->update('srp_erp_companyreporttemplatedetails', ['is_gross_rev' => 0]);
        $this->db->where(['companyReportTemplateID' => $masterID, 'detID' => $gross_column])->update('srp_erp_companyreporttemplatedetails', ['is_gross_rev' => 1]);

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            echo json_encode(['s', 'Updated successfully.']);
        } else {
            echo json_encode(['s', 'Error in updated process.']);
        }
    }
    function template_confirmation()
    {
        echo json_encode($this->Report_template_model->template_confirmation());
    }
    function template_unconfirmation()
    {
        echo json_encode($this->Report_template_model->template_unconfirmation());
    }
}
