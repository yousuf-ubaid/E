<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Jobs_model');
        $this->load->helper('jobs_helper');
        $this->load->library('s3');
    }

    function save_jobs_master_field(){

        $this->form_validation->set_rules('master_field_name', 'Field', 'trim|required',array('required' => 'Field name is required.'));

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_jobs_master_field());
        }
    }

    function get_jobs_master_fileds(){

        $data = array();

        $data['category'] = $this->Jobs_model->get_jobs_master_fileds();

        $this->load->view('system/sales/master/load_fields_category', $data);

    }

    function get_jobs_rigs_hoist(){

        $data = array();

        $data['category'] = $this->Jobs_model->get_jobs_master_fileds_rig_hoist();

        $this->load->view('system/sales/master/load_rig_section', $data);

    }

    // section jobs master
    function fetch_jobs_master(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');

        $this->datatables->select('srp_erp_jobsmaster.*,srp_erp_jobsmaster.localCurrencyCode as localCurrencyCode,srp_erp_jobsmaster.doc_date as doc_date,srp_erp_jobsmaster.job_status as status,srp_erp_jobsmaster.id as id,srp_erp_jobsmaster.confirmed as confirmed');
        $this->datatables->where('srp_erp_jobsmaster.companyID',$companyID);
        $this->datatables->from('srp_erp_jobsmaster');
        //$this->datatables->join('srp_erp_op_checklist_header','srp_erp_jobsmaster.id = srp_erp_op_checklist_header.job_id','left');
        $this->datatables->add_column('action','$1','get_action_job_table(id)');
        $this->datatables->edit_column('doc_date','$1','format_date(doc_date)');
        $this->datatables->edit_column('confirmed','$1','get_action_notify_ele(confirmed)');
        $this->datatables->edit_column('localTotalAmount','<div class="pull-right"><b>$2 : </b> $1 </div>','get_total_local_amount(id),localCurrencyCode');
        $this->datatables->add_column('invstatus','$1','get_job_invoice_status(status,id)');

        echo $this->datatables->generate();

    }

    function fetch_crew_list_contract(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        $group_crew = $this->input->post('group_crew');
        $group_type = $this->input->post('group_type');

        $job_master = get_jobs_master_detail($job_id);
        $group_arr = explode(',',$group_crew);

        if($job_master){

            $this->datatables->select('con_crew.*,con_crew.empID, con_crew.empCode as empCode,con_crew.empName as empName,con_crew.empDesignation as empDesignation,con_crew.id as id');
            $this->datatables->where('con_crew.companyID',$companyID);
            $this->datatables->where('con_crew.contractAutoID',$job_master['contract_po_id']);
            $this->datatables->where('crew.empID IS NULL',null);

            if($group_crew){
                $this->datatables->where('group.groupType',$group_type);
                $this->datatables->where_in('group.groupAutoID',$group_arr);
            }
            
            $this->datatables->from('srp_erp_contractcrew as con_crew');
            $this->datatables->join('srp_erp_op_module_group_to as group','con_crew.groupToID = group.groupAutoID','left');
            $this->datatables->join('srp_erp_job_crewdetail as crew',"con_crew.empID = crew.empID AND crew.job_id = {$job_id}",'left');

            
            $this->datatables->add_column('action','$1','get_add_crew_actions(empID,designation)');
            $this->datatables->add_column('status','$1','get_add_crew_status()');
            $this->datatables->add_column('checkbox','$1','getCheckBoxActivity(id)');
            
            echo $this->datatables->generate();

        }

    }

    

    function fetch_item_details_from_contact(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');

        $job_master = get_jobs_master_detail($job_id);

        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( con_det.typeItemName Like '%$search%' ESCAPE '!') OR ( con_det.itemReferenceNo Like '%$sSearch%' ESCAPE '!') ) ";
        }


        $where = "con_det.companyID = " . $companyID  . $searches . "";
        

        if($job_master){

            $this->datatables->select('con_det.*,con_det.contractDetailsAutoID as contractDetailsAutoIDs,srp_erp_unit_of_measure.UnitShortCode,con_det.unittransactionAmount as unittransactionAmountnew,con_det.transactionAmount as transactionAmountnew');
            $this->datatables->where($where);
            $this->datatables->where('con_det.contractAutoID',$job_master['contract_po_id']);
            $this->datatables->from('srp_erp_contractdetails as con_det');
            $this->datatables->join('srp_erp_unit_of_measure','srp_erp_unit_of_measure.UnitID = con_det.unitOfMeasureID');
            //$this->datatables->add_column('action','$1','get_add_crew_actions(empID,designation)');
            $this->datatables->add_column('status','$1','get_add_crew_status()');
            $this->datatables->add_column('checkbox','$1','getCheckBoxActivityStandard(contractDetailsAutoIDs)');
            $this->datatables->edit_column('unit_amount','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(unittransactionAmountnew,2),'.$job_master['localCurrencyCode'].'');
            $this->datatables->edit_column('total_amount','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(transactionAmountnew,2),'.$job_master['localCurrencyCode'].'');
            
            echo $this->datatables->generate();

        }

    }

    function fetch_crew_list_common(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');


        if($job_id){

            $this->datatables->select('se.*,se.EIdNo as empID, se.ECode as ECode,se.Ename1 as Ename1,sd.DesDescription as designation');
            $this->datatables->from('srp_employeesdetails as se');
            $this->datatables->join('srp_designation as sd','se.EmpDesignationId = sd.DesignationID','left');
            $this->datatables->where('se.Erp_companyID',$companyID);
            $this->datatables->add_column('action','$1','get_add_crew_actions(empID,designation)');
            $this->datatables->add_column('status','$1','get_add_crew_status()');
            echo $this->datatables->generate();

        }

    }

    function fetch_added_crew_details(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        $filter = $this->input->post('filter');
        $group_arr = array();

        if($filter){
            $group_arr = explode(',',$filter);
        }


        $job_master = get_jobs_master_detail($job_id);

        if($job_master){

            $this->datatables->select('sjc.*,se.ECode as ECode,group.groupName,group.groupType,group.groupAutoID as groupAutoID,sjc.dateFrom as dateFrom,sjc.dateTo as dateTo,sjc.empID as empID,sjc.id as id,sjc.competency_check,sjc.training_check,sjc.ssc_check');
            $this->datatables->where('sjc.companyID',$companyID);
            $this->datatables->where('sjc.job_id',$job_id);
            $this->datatables->where('shift_group.empID IS NULL',null);
            $this->datatables->from('srp_erp_job_crewdetail as sjc');
            $this->datatables->join('srp_employeesdetails as se','sjc.empID = se.EIdNo','left');
            $this->datatables->join('srp_erp_op_module_group_to as group','sjc.groupID = group.groupAutoID','left');
            $this->datatables->join('srp_erp_job_shift_crewdetail as shift_group',"sjc.empID = shift_group.empID AND shift_group.job_id = {$job_id}",'left');
           
            if($filter){
                $this->datatables->where_in('sjc.groupID',$group_arr);
            }

            $this->datatables->add_column('dateFromT','$1','getDateFromField(dateFrom)');
            $this->datatables->add_column('dateToT','$1','getDateToField(dateTo)');
            $this->datatables->add_column('schedule','$1','getEmployeeSchedule(empID)');
            $this->datatables->add_column('dateHours','$1','getDayWiseDifference(dateFrom,dateTo)');
            $this->datatables->add_column('jobStatus','$1','getJobStatus(dateFrom,dateTo)');
            $this->datatables->add_column('action','$1','get_added_actions(id,srp_erp_job_crewdetail)');
            $this->datatables->add_column('competencyChk','$1','getCheckBox(1,competency_check,id)');
            $this->datatables->add_column('trainingChk','$1','getCheckBox(2,training_check,id)');
            $this->datatables->add_column('sscChk','$1','getCheckBox(3,ssc_check,id)');
            $this->datatables->add_column('action_activity','$1','getCheckBoxActivity(id)');
            
            // $this->datatables->add_column('status','$1','get_add_crew_status()');
            echo $this->datatables->generate();

        }

    }

    function fetch_added_crew_details_shift(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        $filter = $this->input->post('filter');
        $group_arr = array();

        if($filter){
            $group_arr = explode(',',$filter);
        }


        $job_master = get_jobs_master_detail($job_id);

        if($job_master){

            $this->datatables->select('sjc.*,se.ECode as ECode,group.groupName,group.groupType,group.groupAutoID as groupAutoID,sjc.dateFrom as dateFrom,sjc.dateTo as dateTo,sjc.empID as empID,sjc.id as id,sjc.competency_check,sjc.training_check,sjc.ssc_check');
            $this->datatables->where('sjc.companyID',$companyID);
            $this->datatables->where('sjc.job_id',$job_id);
            $this->datatables->where('shift_group.empID IS NULL',null);
            $this->datatables->from('srp_erp_job_crewdetail as sjc');
            $this->datatables->join('srp_employeesdetails as se','sjc.empID = se.EIdNo','left');
            $this->datatables->join('srp_erp_op_module_group_to as group','sjc.groupID = group.groupAutoID','left');
            $this->datatables->join('srp_erp_job_shift_crewdetail as shift_group',"sjc.empID = shift_group.empID AND shift_group.job_id = {$job_id}",'left');
           
            if($filter){
                $this->datatables->where_in('sjc.groupID',$group_arr);
            }

            $this->datatables->add_column('dateFromT','$1','getDateFromField(dateFrom)');
            $this->datatables->add_column('dateToT','$1','getDateToField(dateTo)');
            $this->datatables->add_column('schedule','$1','getEmployeeSchedule(empID)');
            $this->datatables->add_column('dateHours','$1','getDayWiseDifference(dateFrom,dateTo)');
            $this->datatables->add_column('jobStatus','$1','getJobStatus(dateFrom,dateTo)');
            $this->datatables->add_column('action','$1','get_added_actions_shift_add(id,srp_erp_job_crewdetail)');
            $this->datatables->add_column('competencyChk','$1','getCheckBox(1,competency_check,id)');
            $this->datatables->add_column('trainingChk','$1','getCheckBox(2,training_check,id)');
            $this->datatables->add_column('sscChk','$1','getCheckBox(3,ssc_check,id)');
            $this->datatables->add_column('action_activity','$1','getCheckBoxActivityCrew(id)');
            
            // $this->datatables->add_column('status','$1','get_add_crew_status()');
            echo $this->datatables->generate();

        }

    }

    function fetch_added_visitors_log_details(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');

        if($job_id){

            $this->datatables->select('id,full_name,full_company,position,purpose_visit,mobile_no,medication,h2s_validity,safety_briefing,proper_ppe,time_in,time_out,date');
            $this->datatables->where('companyID',$companyID);
            $this->datatables->where('job_id',$job_id);
            $this->datatables->from('srp_erp_op_visitors_log');
            $this->datatables->add_column('action','$1','get_added_actions(id,srp_erp_op_visitors_log)');
            echo $this->datatables->generate();

        }

    }

    function fetch_visitor_request(){

        $job_id = $this->input->post('job_id');
        $companyID = $this->common_data['company_data']['company_id'];

        if($job_id){

            $this->datatables->select('srp_erp_op_visitor_log_link.*,srp_erp_op_visitor_log_link.id as id,srp_erp_op_visitor_log_link.status as status');
            $this->datatables->where('companyID',$companyID);
            $this->datatables->where('job_id',$job_id);
            $this->datatables->from('srp_erp_op_visitor_log_link');
            $this->datatables->add_column('action','$1','get_visitor_log_actions(id)');
            $this->datatables->add_column('status','$1','get_visitor_status(id,status)');
            echo $this->datatables->generate();

        }
    }

    function fetch_fuel_utilization_details(){
        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');

        if($job_id){

              $this->datatables->select("id,
                Documentdate,
                UnitDes AS UoM,
                srp_erp_job_fueldetails.Description AS description,
                fleet_fuel_type.description AS fueldes,
                (CASE WHEN transactiontypeID = 1 THEN Qty ELSE 0 END) AS receivedQty,
                (CASE WHEN transactiontypeID = 2 THEN Qty ELSE 0 END) AS issuedQty,
                
                @runningTotal := CASE WHEN fueltypeID = @currentFuelType THEN @runningTotal + 
                    (CASE WHEN transactiontypeID = 1 THEN Qty WHEN transactiontypeID = 2 THEN - Qty ELSE 0 END ) ELSE ( CASE WHEN transactiontypeID = 1 THEN Qty WHEN transactiontypeID = 2 THEN - Qty ELSE 0 END) END AS balance,
                    (CASE WHEN srp_erp_job_fueldetails.userID IS NOT NULL THEN srp_employeesdetails.Ename2 ELSE srp_erp_job_fueldetails.userName END) AS usernameemp
                    ");
                   
            $this->datatables->select('
                    (CASE WHEN @currentFuelType := fueltypeID THEN 0 END),
                    srp_erp_job_fueldetails.id,
                    srp_erp_job_fueldetails.transactiontypeID,
                    srp_erp_job_fueldetails.DocumentID,
                    srp_erp_job_fueldetails.Documentdate,
                    srp_erp_job_fueldetails.Description AS description,
                    srp_erp_job_fueldetails.Qty,
                    srp_erp_job_fueldetails.userName,
                    srp_erp_job_fueldetails.userID,
                    srp_employeesdetails.Ename2,
                    fleet_fuel_type.description AS fueldes,
                    srp_erp_unit_of_measure.UnitDes as UoM' );
    

            $this->datatables->join('srp_erp_unit_of_measure', 'srp_erp_job_fueldetails.uomID = srp_erp_unit_of_measure.UnitID', 'left');

            $this->datatables->join('srp_employeesdetails', 'srp_erp_job_fueldetails.userID = srp_employeesdetails.EIdNo', 'left');

            $this->datatables->join('fleet_fuel_type', 'srp_erp_job_fueldetails.fuelid = fleet_fuel_type.fuelTypeID', 'left');

            $this->datatables->where('srp_erp_job_fueldetails.companyID', $companyID);

            $this->datatables->where('srp_erp_job_fueldetails.jobID', $job_id);

            $this->datatables->from('srp_erp_job_fueldetails ,(SELECT @runningTotal := 0 , @currentFuelType := NULL) as init');

            $this->db->order_by('srp_erp_job_fueldetails.fuelid,srp_erp_job_fueldetails.id');

            $this->datatables->add_column('action', '$1', 'get_added_actions(id,srp_erp_job_fueldetails)');
            
            echo $this->datatables->generate();


        }


    }

    function get_setup_visitor_log_details($type = null){

        $id = $this->input->post('id');
        $type = $this->input->post('type');

        $details = get_visitors_log_request($id);

        //$currentURL = $this->config->config['host_url'];
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
        
        $currentURL = "$protocol$_SERVER[HTTP_HOST]".'/'.'index.php/';

        if($type == 'link'){
            $company_id = $this->common_data['company_data']['company_id'];
            $encrypt_company_id = urlencode($this->encryption->encrypt($company_id));

            $url = $currentURL.'Ilooops/visitorTicket?id='.$details['validReference'].'&setting='.$encrypt_company_id;

            echo $url;

        }

    }

    function get_setup_checklist_link(){

        $id = $this->input->post('id');
        $type = $this->input->post('type');

        $details = get_checklist_header_record($id);
       // $currentURL = $this->config->config['host_url'];
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
        
        $currentURL = "$protocol$_SERVER[HTTP_HOST]".'/'.'index.php/';

        if($details && empty($details['link_reference'])){

            $date = date('Y-m-d H:i:s');
            $pin = rand(1111,9999);
            $link_ref = strtotime($date).rand(11111,99999).rand(11111,99999).rand(11111,99999);

            $data = array();
            $data['link_reference'] = $link_ref;
            $data['pin_reference'] = $pin;
            $data['filled_status'] = 0;

            $res = $this->db->where('id',$id)->update('srp_erp_op_checklist_header',$data);
        }else{
            $link_ref = $details['link_reference'];
            $pin = $details['pin_reference'];
        }

        if($link_ref){

            $company_id = $this->common_data['company_data']['company_id'];
            $encrypt_company_id = urlencode($this->encryption->encrypt($company_id));
            $encrypt_pin = urlencode($this->encryption->encrypt($pin));

            $url = $currentURL.'Ilooops/onlineCheckList?id='.$link_ref.'&setting='.$encrypt_company_id.'&pin='.$encrypt_pin;

            echo $url;
        }

    }


    function fetch_pipe_tally_details(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');

        if($job_id){

            $this->datatables->select('id,running_number,od_inches,item_length,cum_length,landing_depth_bottom');
            $this->datatables->where('companyID',$companyID);
            $this->datatables->where('job_id',$job_id);
            $this->datatables->from('srp_erp_op_pipe_tally');
            $this->datatables->add_column('action','$1','get_added_actions(id,srp_erp_op_pipe_tally)');
            echo $this->datatables->generate();

        }

    }

    function fetch_job_item_details(){
        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');

        $this->datatables->select('srp_erp_job_itemdetail.*,srp_erp_job_itemdetail.value as value,srp_erp_job_itemdetail.discount as discount,srp_erp_job_itemdetail.id as id,srp_erp_job_itemdetail.transactionAmount as transactionAmount,srp_erp_job_itemdetail.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
        srp_erp_job_itemdetail.transactionCurrency as transactionCurrency');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('job_id',$job_id);
        $this->datatables->from('srp_erp_job_itemdetail');
        $this->datatables->add_column('netAmount', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(transactionAmount,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->edit_column('value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->edit_column('discount', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(discount,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('action','$1','get_action_job_table(id,job_item,srp_erp_job_itemdetail)');
        // $this->datatables->add_column('invstatus','$1','get_job_invoice_status(status)');
        echo $this->datatables->generate();
    }

    function fetch_job_asset_details(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');

        $this->datatables->select('srp_erp_job_assetsdetail.*, srp_erp_job_assetsdetail.id as id, srp_erp_job_assetsdetail.assetCode as assetCode,srp_erp_job_assetsdetail.assetName as assetName, srp_erp_job_assetsdetail.dateFrom as dateFrom,srp_erp_job_assetsdetail.dateTo as dateTo,srp_erp_job_assetsdetail.faID as faID,maintenance_check');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('job_id',$job_id);
        $this->datatables->from('srp_erp_job_assetsdetail');
        // $this->datatables->add_column('action','$1','get_action_job_table(id,faID)');
        $this->datatables->add_column('dateFromT','$1','getDateFromField(dateFrom,faID)');
        $this->datatables->add_column('dateToT','$1','getDateToField(dateTo,faID)');
        $this->datatables->add_column('dateHours','$1','getDayWiseDifference(dateFrom,dateTo,"hours_minute")');
        $this->datatables->add_column('jobStatus','$1','getJobStatus(dateFrom,dateTo)');
        $this->datatables->add_column('action','$1','get_added_actions(id,srp_erp_job_assetsdetail)');
        $this->datatables->add_column('maintenanceChk','$1','getCheckBox(4,maintenance_check,id)');
        // $this->datatables->add_column('invstatus','$1','get_job_invoice_status(status)');
        echo $this->datatables->generate();

    }

    function fetch_job_activity(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        $shift_id = $this->input->post('shift_id');
        
        $this->datatables->select('srp_erp_job_activitydetail.*,srp_erp_job_activitydetail.type as type, srp_erp_job_activitydetail.id as id,srp_erp_job_activitydetail.dateFrom as dateFrom,srp_erp_job_activitydetail.dateTo as dateTo,srp_erp_job_activitydetail.isStandby as isStandby,srp_erp_job_activitydetail.isNpt as isNpt');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('job_id',$job_id);
        $this->datatables->where('shift_id',$shift_id);
        $this->datatables->from('srp_erp_job_activitydetail');
        $this->datatables->add_column('dateHours','$1','getDayWiseDifference(dateFrom,dateTo,"hours_minute")');
        $this->datatables->add_column('isStandbyT','$1','get_is_yes_no(isStandby)');
        $this->datatables->add_column('isNptT','$1','get_is_yes_no(isNpt)');
        $this->datatables->edit_column('type','$1','get_activity_name(type)');
        $this->datatables->add_column('action','$1','get_added_actions(id,srp_erp_job_activitydetail)');
        // $this->datatables->add_column('status','$1','get_add_crew_status()');
        echo $this->datatables->generate();


    }

    function fetch_job_activity_shift(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        
        $this->datatables->select('srp_erp_job_activityshift.*,srp_erp_job_activityshift.id as id,srp_erp_job_activityshift.check_list as check_list');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('job_id',$job_id);
        $this->datatables->from('srp_erp_job_activityshift');
        $this->datatables->add_column('action','$1','get_added_actions_shift(id,"srp_erp_job_activityshift")');
        $this->datatables->add_column('checklist','$1','get_check_list_tbl(check_list)');
        echo $this->datatables->generate();

    }

    function fetch_daily_report(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        
        $this->datatables->select('srp_erp_job_dailyreport.*,srp_erp_job_dailyreport.id as id,srp_erp_job_dailyreport.confirmedYN as confirmedYN');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('job_id',$job_id);
        $this->datatables->from('srp_erp_job_dailyreport');
        $this->datatables->add_column('action','$1','get_added_actions_reports(id,"srp_erp_job_dailyreport")');
        $this->datatables->edit_column('confirmedYN','$1','confirm_user_approval_drilldown(confirmedYN,"Job",id)');
        // $this->datatables->add_column('status','$1','get_add_crew_status()');
        echo $this->datatables->generate();

    }

    function fetch_billing_detail_added(){

        $companyID = $this->common_data['company_data']['company_id'];
        $billing_header_id = $this->input->post('billing_header_id');
        
        $this->datatables->select('srp_erp_job_billing_detail.*,srp_erp_job_billing_detail.total_amount as total_amount,srp_erp_job_billing_detail.unit_amount as unit_amount,srp_erp_jobsmaster.localCurrencyCode as localCurrencyCode,srp_erp_job_billing_detail.id as id,srp_erp_job_billing_detail.isStandby as isStandby,srp_erp_job_billing_detail.isNpt as isNpt');
        $this->datatables->where('srp_erp_job_billing_detail.companyID',$companyID);
        $this->datatables->where('billing_header',$billing_header_id);
        $this->datatables->from('srp_erp_job_billing_detail');
        $this->datatables->join('srp_erp_jobsmaster','srp_erp_job_billing_detail.job_id = srp_erp_jobsmaster.id','left');
        $this->datatables->add_column('action','$1','get_added_actions_reports(id,"billing")');   
        $this->datatables->edit_column('isStandby','$1','get_is_yes_no(isStandby)');
        $this->datatables->edit_column('isNpt','$1','get_is_yes_no(isNpt)');
        $this->datatables->edit_column('unit_amount','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(unit_amount,2),localCurrencyCode');
        $this->datatables->edit_column('total_amount','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(total_amount,2),localCurrencyCode');
        // 
        echo $this->datatables->generate();

    }

    function fetch_billing_detail_added_standard(){

        $companyID = $this->common_data['company_data']['company_id'];
        $billing_header_id = $this->input->post('billing_header_id');
        
        $this->datatables->select('srp_erp_job_billing_detail.*,srp_erp_job_billing_detail.dateFrom as dateFroms,srp_erp_job_billing_detail.dateTo as dateTos,srp_erp_job_billing_detail.total_amount as total_amount,srp_erp_job_billing_detail.unit_amount as unit_amount,srp_erp_jobsmaster.localCurrencyCode as localCurrencyCode,srp_erp_job_billing_detail.id as id,srp_erp_job_billing_detail.isStandby as isStandby,srp_erp_job_billing_detail.isNpt as isNpt,srp_erp_job_billing_detail.qty as qtys,srp_erp_contractdetails.typeItemName,srp_erp_unit_of_measure.UnitShortCode');
        $this->datatables->where('srp_erp_job_billing_detail.companyID',$companyID);
        $this->datatables->where('billing_header',$billing_header_id);
        $this->datatables->from('srp_erp_job_billing_detail');
        $this->datatables->join('srp_erp_jobsmaster','srp_erp_job_billing_detail.job_id = srp_erp_jobsmaster.id','left');
        $this->datatables->join('srp_erp_contractdetails','srp_erp_job_billing_detail.price_id = srp_erp_contractdetails.contractDetailsAutoID','left');
        $this->datatables->join('srp_erp_unit_of_measure','srp_erp_unit_of_measure.UnitID = srp_erp_contractdetails.unitOfMeasureID');
        $this->datatables->add_column('action','$1','get_added_actions_reports(id,"billing")');   
        $this->datatables->edit_column('isStandby','$1','get_is_yes_no(isStandby)');
        $this->datatables->edit_column('isNpt','$1','get_is_yes_no(isNpt)');
        $this->datatables->edit_column('unit_amount','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(unit_amount,2),localCurrencyCode');
        $this->datatables->edit_column('total_amount','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(total_amount,2),localCurrencyCode');
        $this->datatables->edit_column('editQty','$1','edit_standard_billing_qty(id,qtys)');
        $this->datatables->edit_column('editformdate','$1','edit_standard_billing_from_date(id,dateFroms)');
        $this->datatables->edit_column('edittodate','$1','edit_standard_billing_to_date(id,dateTos)');
        // 
        echo $this->datatables->generate();

    }

    
    function fetch_billing_report(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        
        $this->datatables->select('srp_erp_job_billing.*,srp_erp_job_billing.id as id,srp_erp_job_billing.confirmedYN as confirmedYN,srp_erp_job_billing.sales_order_id as sales_order_id');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('job_id',$job_id);
        $this->datatables->from('srp_erp_job_billing');
        $this->datatables->add_column('action','$1','get_added_actions_shift(id,"billing",confirmedYN,sales_order_id)');
        $this->datatables->add_column('orderStatus','$1','get_invoice_status(id,"billing",sales_order_id)');
        $this->datatables->edit_column('confirmedYN','$1','fetch_checklist_status_job(confirmedYN)');
        $this->datatables->add_column('value','<div class="pull-right"><b>USD</b> : $1</div','0.00');
        $this->datatables->add_column('discount','<div class="pull-right"><b>USD</b> : $1</div','0.00');
        $this->datatables->add_column('netAmount','<div class="pull-right"><b>USD</b> : $1</div>','get_net_amount(id)');
        echo $this->datatables->generate();

    }

    function fetch_billing_report_standard(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        
        $this->datatables->select('srp_erp_job_billing.*,srp_erp_job_billing.id as id,srp_erp_job_billing.confirmedYN as confirmedYN,srp_erp_job_billing.sales_order_id as sales_order_id');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('job_id',$job_id);
        $this->datatables->from('srp_erp_job_billing');
        $this->datatables->add_column('action','$1','get_added_actions_shift_standard(id,"billing",confirmedYN,sales_order_id)');
        $this->datatables->add_column('orderStatus','$1','get_invoice_status(id,"billing",sales_order_id)');
        $this->datatables->edit_column('confirmedYN','$1','fetch_checklist_status_job(confirmedYN)');
        $this->datatables->add_column('value','<div class="pull-right"><b>USD</b> : $1</div','0.00');
        $this->datatables->add_column('discount','<div class="pull-right"><b>USD</b> : $1</div','0.00');
        $this->datatables->add_column('netAmount','<div class="pull-right"><b>USD</b> : $1</div>','get_net_amount(id)');
        echo $this->datatables->generate();

    }

    function fetch_billing_report_modify(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        
        $this->datatables->select('srp_erp_job_billing.*,srp_erp_job_billing.id as id,srp_erp_job_billing.confirmedYN as confirmedYN,srp_erp_job_billing.sales_order_id as sales_order_id');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('job_id',$job_id);
        $this->datatables->from('srp_erp_job_billing');
        $this->datatables->add_column('action','$1','get_added_actions_shift_modify(id,"billing",confirmedYN,sales_order_id)');
        $this->datatables->add_column('orderStatus','$1','get_invoice_status(id,"billing",sales_order_id)');
        $this->datatables->edit_column('confirmedYN','$1','fetch_checklist_status_job(confirmedYN)');
        $this->datatables->add_column('value','<div class="pull-right"><b>USD</b> : $1</div','0.00');
        $this->datatables->add_column('discount','<div class="pull-right"><b>USD</b> : $1</div','0.00');
        $this->datatables->add_column('netAmount','<div class="pull-right"><b>USD</b> : $1</div>','get_net_amount(id)');
        echo $this->datatables->generate();

    }

    function fetch_employees_added_to_job(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        
        $this->datatables->select('srp_erp_job_billing.*,srp_erp_job_billing.id as id');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('job_id',$job_id);
        $this->datatables->from('srp_erp_job_billing');
        $this->datatables->add_column('action','$1','get_added_actions_shift(id,"billing")');
        $this->datatables->add_column('value','$1','0.00');
        $this->datatables->add_column('discount','$1','0.00');
        $this->datatables->add_column('netAmount','$1','get_net_amount(id)');
        echo $this->datatables->generate();

    }

    function fetch_job_activity_crew(){

        $companyID = $this->common_data['company_data']['company_id'];
        $shift_id = $this->input->post('shift_id');
        $job_id = $this->input->post('job_id');

        $filter = $this->input->post('filter');
        $group_arr = explode(',',$filter ?? '');
        
        $this->datatables->select('srp_erp_job_shift_crewdetail.*,srp_erp_job_crewdetail.empID as empID, srp_erp_job_shift_crewdetail.id as id');
        $this->datatables->where('srp_erp_job_shift_crewdetail.companyID',$companyID);
        $this->datatables->where('srp_erp_job_shift_crewdetail.job_id',$job_id);
        $this->datatables->where('srp_erp_job_shift_crewdetail.shift_id',$shift_id);
        $this->datatables->from('srp_erp_job_shift_crewdetail');
        $this->datatables->join('srp_erp_job_crewdetail','srp_erp_job_shift_crewdetail.empID = srp_erp_job_crewdetail.empID AND srp_erp_job_crewdetail.job_id = srp_erp_job_shift_crewdetail.job_id','left');
        if($filter){
            $this->datatables->where_in('srp_erp_job_crewdetail.groupID',$group_arr);
        }
        $this->datatables->add_column('action','$1','get_action_job_table(id,1,"srp_erp_job_shift_crewdetail")');

        echo $this->datatables->generate();

    }

    

    function fetch_job_header_details(){

        $job_id = $this->input->post('job_id');

        $detail = get_jobs_master_detail($job_id);

        echo json_encode($detail);

    }

    function fetch_contract_list(){

        $customerID = $this->input->post('customerID');
        $detail = get_contract_list($customerID,true,true);
        echo json_encode($detail);
    }

    function fetch_contract_details(){

        $contractID = $this->input->post('contractID');

        $detail = get_contract_detail($contractID,true);

        echo json_encode($detail);

    }

    function fetch_well_details(){

        $fieldID = $this->input->post('fieldID');

        $detail = get_well_list($fieldID,true);

        echo json_encode($detail);
    }

    //Save
    function add_crew_job(){
        echo json_encode($this->Jobs_model->add_crew_job());
    }

    function add_crew_multiple(){
        echo json_encode($this->Jobs_model->add_crew_multiple());
    }

    function add_crew_from_date(){
        echo json_encode($this->Jobs_model->add_crew_from_date());
    }

    function add_crew_to_date(){
        echo json_encode($this->Jobs_model->add_crew_to_date());
    }

    function add_asset_from_date(){
        echo json_encode($this->Jobs_model->add_asset_from_date());
    }

    function add_asset_to_date(){
        echo json_encode($this->Jobs_model->add_asset_to_date());
    }

    function remove_added_record(){
        echo json_encode($this->Jobs_model->remove_added_record());
    }

    function load_job_item_detail(){
        echo json_encode($this->Jobs_model->load_job_item_detail());
    }

    function load_activity_detail(){
        echo json_encode($this->Jobs_model->load_activity_detail());
    }

    function save_activity_detail_edit(){

        $this->form_validation->set_rules('edit_description', 'Description', 'trim|required');
     
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_activity_detail_edit());
        }
        
    }

    

    
    function get_employee_schdule(){
        $this->form_validation->set_rules('dateFrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('dateTo', 'Date To', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->get_employee_schdule());
        }
    }

    function save_jobs_detail_header(){

        $this->form_validation->set_rules('contractAutoID', 'Contract', 'trim|required');
        $this->form_validation->set_rules('toDate', 'Date To', 'trim|required');
        $this->form_validation->set_rules('fromDate', 'Date From', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('job_type', 'Job Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_jobs_detail_header());
        }

    }

    function fetch_itemrecode_po(){

        $job_id = trim($this->input->get('job_id'));

        echo json_encode($this->Jobs_model->fetch_itemrecode_po());

    }
    function generate_sequence() {
        $job_id = $this->input->post('job_id');
        if (is_null($job_id)) {
            $doc_id = $this->sequence->sequence_generator('CJOB', 0, null);
        } else {
            $doc_id = $this->sequence->sequence_generator('CJOB', 0, null);
        }
        
        echo json_encode(['status' => true, 'doc_id' => $doc_id]);
    }

    function save_item_detail(){
        echo json_encode($this->Jobs_model->save_item_detail());
    }

    function save_item_detail_edit(){
        echo json_encode($this->Jobs_model->save_item_detail_edit());
    }


    //Assets Section

    function save_activity_detail(){
        echo json_encode($this->Jobs_model->save_activity_detail());
    }

    function fetch_asset_contract_list(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        $group_asset = $this->input->post('group_asset');
        $group_type = $this->input->post('group_type');
        
        $job_master = get_jobs_master_detail($job_id);
        $group_arr = explode(',',$group_asset);

        if($job_master){

            $this->datatables->select('sc.*,sc.faID as faID,sc.faCode as faCode');
            $this->datatables->where('sc.companyID',$companyID);
            $this->datatables->where('sc.contractAutoID',$job_master['contract_po_id']);
            $this->datatables->where('asset.faID IS NULL',null);

            if($group_asset){
                $this->datatables->where('group.groupType',$group_type);
                $this->datatables->where_in('group.groupAutoID',$group_arr);
            }

            $this->datatables->from('srp_erp_contractassets as sc');
            $this->datatables->join('srp_erp_op_module_group_to as group','sc.groupToID = group.groupAutoID','left');
            $this->datatables->join('srp_erp_job_assetsdetail as asset',"sc.faID = asset.faID AND asset.job_id = {$job_id}",'left');

            $this->datatables->add_column('action','$1','get_add_job_assets_actions(faID)');
            $this->datatables->add_column('status','$1','get_job_assets_status(faID)');
            echo $this->datatables->generate();

        }
    }

    function add_assets_job(){
        echo json_encode($this->Jobs_model->add_assets_job());
    }

    function get_total_hours(){
        echo json_encode($this->Jobs_model->get_total_hours());
    }

    function save_fields_well(){
        $this->form_validation->set_rules('filed_well_name', 'Name', 'trim|required');
        $this->form_validation->set_rules('filed_well_type', 'Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_fields_well());
        }

    }

    function save_daily_report_detail(){

        $this->form_validation->set_rules('reportFromDate', 'Report Start', 'trim|required');
        $this->form_validation->set_rules('reportToDate', 'Report To', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_daily_report_detail());
        }

    }

    function delete_field_well(){
        echo json_encode($this->Jobs_model->delete_field_well());
    }

    function get_job_confirmation_view(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $printtype = trim($this->input->post('printtype') ?? '');
        $data = array();

        $data['logo']=mPDFImage;

        //master details
        
        $data['master'] = get_jobs_master_detail($job_id);
        $data['item'] = get_job_items_list($job_id);
        $data['crew'] = get_job_crew_list($job_id);
        $data['checklist'] = get_checklist_status_job($job_id);
        $data['job_id'] = $job_id;

        // Pass the checklist status to the JavaScript code
        if (!empty($data['checklist'])) {
            $data['checklist_status'] = isset($data['checklist'][0]['is_confirmed']) ? $data['checklist'][0]['is_confirmed'] : null;
        } else {
            // Handle the case when the array is empty.
            $data['checklist_status'] = 1; 
        }
        

        if ($printtype == 'html') {
            $data['logo'] = htmlImage;
            echo $this->load->view('system/sales/master/ajax/job_confirmation', $data, true);
        }

    }

    function job_confirmation(){
        echo json_encode($this->Jobs_model->job_confirmation());
    }

    function save_jobs_crew_check_status(){
        echo json_encode($this->Jobs_model->save_jobs_crew_check_status());
    }

    function save_shift_activity_detail(){
        $this->form_validation->set_rules('shiftFromDate', 'Shift Start', 'trim|required');
        $this->form_validation->set_rules('shiftToDate', 'Shift To', 'trim|required');
        $this->form_validation->set_rules('shift_notes', 'Special Notes', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_shift_activity_detail());
        }
    }

    function get_load_check_list(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        $printtype = $this->input->post('printtype');
        $companyID = $this->input->post('companyID');

        if($printtype == 'html'){
            $header_id = $this->input->post('header_id');
        }else{
            $header_id = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        }

        $data = array();

        $header_record = get_checklist_header_record($header_id);

        if($header_record){

            $template_id = $header_record['master_id'];

            //get job details
            $job_master = get_jobs_master_detail($job_id);

            //get master record
            $master_record = get_checklist_master_record($template_id);

            //get question list
            $question_list = get_checklist_questions_details($template_id);

            //get response list for questions
            $qt_response_list = get_checklist_questions_response_details($header_id);
            $response_qt_arranged = array();

            foreach($qt_response_list as $value){
                $response_qt_arranged[$value['detail_id']] = array('status'=>$value['status'],'comments'=>$value['comments']);
            }

            $data['details'] = $question_list;
            $data['page_type'] = $printtype;
            $data['job_details'] = $job_master;
            $data['header_record'] = $header_record; 
            $data['response_list'] = $response_qt_arranged; 

            $html = $this->load->view($master_record['view_path'], $data,true);
            
            if ($printtype == 'html') {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4');
            }

        }

    }

    function save_checklist_response(){
        echo json_encode($this->Jobs_model->save_checklist_response());
       

    }

    function job_attachement_upload()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');

        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('*');
            $this->db->where('job_id', trim($this->input->post('job_id') ?? ''));
            $num = $this->db->get('srp_erp_op_job_attachments')->result_array();


            $fileName = current_companyCode() . '_' . $this->input->post('document_name') . '_' . $this->input->post('job_id') . '_' . (count($num) + 1);
            $file = $_FILES['document_file'];
            if($file['error'] == 1){
                die( json_encode(['status' => 0, 'type' => 'e','message' => 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).']) );
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if(!in_array($ext, $allowed_types)){
                die( json_encode(['status' => 0, 'type' => 'e','message' =>  "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if($size > 5){
                die( json_encode(['status' => 0, 'type' => 'e','message' =>"The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]) );
            }

            $path = "attachments/JOB/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                die( json_encode(['status' => 0, 'type' => 'e','message' =>'Error in document upload location configuration']) );
            }

            $this->db->trans_start();
                $data['job_id'] = trim($this->input->post('job_id') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                $data['fileName'] = $path;
                $data['fileType'] = trim($ext);;
                $data['fileSize'] = trim($file["size"]);
                $data['timestamp'] = date('Y-m-d H:i:s');
                //$data['companyID'] = $this->common_data['company_data']['company_id'];

                $this->db->insert('srp_erp_op_job_attachments', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $fileName . ' uploaded.'));
                }

        }
    }

    function delete_job_attachment()
    {
        $id = $this->input->post('id');
        $fileName = $this->input->post('fileName');
        $result = $this->s3->delete($fileName);
        /** end of AWS s3 delete object */
        if ($result) {
            $this->db->delete('srp_erp_op_job_attachments', array('id' => trim($id)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }

    }

    function job_attachement_well_upload()
    {
        $this->form_validation->set_rules('document_file_well', 'File', 'trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $fileName = current_companyCode() . '_' . $this->input->post('document_name') . '_' . $this->input->post('job_id');
            $file = $_FILES['document_file_well'];
            if($file['error'] == 1){
                die( json_encode(['status' => 0, 'type' => 'e','message' => 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).']) );
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if(!in_array($ext, $allowed_types)){
                die( json_encode(['status' => 0, 'type' => 'e','message' =>  "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if($size > 5){
                die( json_encode(['status' => 0, 'type' => 'e','message' =>"The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]) );
            }

            $path = "attachments/WELL/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                die( json_encode(['status' => 0, 'type' => 'e','message' =>'Error in document upload location configuration']) );
            }

            $this->db->trans_start();                
                $data['wellFileName'] = $path;      
                $job_id = trim($this->input->post('job_id') ?? '');
                $this->db->where('id', $job_id);
                $this->db->update('srp_erp_jobsmaster', $data);                
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $fileName . ' uploaded.'));
                }

        }
    }

    function job_attachement_bob_upload()
    {
        $this->form_validation->set_rules('document_file_bob', 'File', 'trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $fileName = current_companyCode() . '_' . $this->input->post('document_name') . '_' . $this->input->post('job_id');
            $file = $_FILES['document_file_bob'];
            if($file['error'] == 1){
                die( json_encode(['status' => 0, 'type' => 'e','message' => 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).']) );
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if(!in_array($ext, $allowed_types)){
                die( json_encode(['status' => 0, 'type' => 'e','message' =>  "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if($size > 5){
                die( json_encode(['status' => 0, 'type' => 'e','message' =>"The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]) );
            }

            $path = "attachments/BOB/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                die( json_encode(['status' => 0, 'type' => 'e','message' =>'Error in document upload location configuration']) );
            }

            $this->db->trans_start();                
                $data['bobFileName'] = $path;      
                $job_id = trim($this->input->post('job_id') ?? '');
                $this->db->where('id', $job_id);
                $this->db->update('srp_erp_jobsmaster', $data);                
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $fileName . ' uploaded.'));
                }

        }
    }

    function checklist_attachement_upload()
    {
        $this->form_validation->set_rules('document_file_bob', 'File', 'trim');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $fileName = current_companyCode() . '_' . $this->input->post('document_name') . '_' . $this->input->post('checklist_header_id');
            $file = $_FILES['document_file_bob'];
            if($file['error'] == 1){
                die( json_encode(['status' => 0, 'type' => 'e','message' => 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).']) );
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if(!in_array($ext, $allowed_types)){
                die( json_encode(['status' => 0, 'type' => 'e','message' =>  "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if($size > 5){
                die( json_encode(['status' => 0, 'type' => 'e','message' =>"The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]) );
            }

            $path = "attachments/chk/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                die( json_encode(['status' => 0, 'type' => 'e','message' =>'Error in document upload location configuration']) );
            }

            $this->db->trans_start();                
                $data['attachment_description'] = $path;     
                $data['attachment_name'] = $path;      
                $header_id = trim($this->input->post('checklist_header_id') ?? '');
                $this->db->where('id', $header_id);
                $this->db->update('srp_erp_op_checklist_header', $data);                
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $fileName . ' uploaded.'));
                }

        }
    }

    function load_daily_job_report(){
        
        $shift_id = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $html  = $this->input->post('html');
        
        if($html){
            $shift_id = $this->input->post('id');
        }
    
        $companyID = $this->common_data['company_data']['company_id'];   
     
        $data = array();
        $total_hours = '0:00';
        
        $report_record = get_daily_report_master($shift_id);

        if($report_record){

            $get_jobs_master_detail = get_jobs_master_detail($report_record['job_id']);

            $get_jobs_detail = get_daily_report_details($report_record['id']);

            $get_total_job_hours_arr = get_daily_report_master_total_hours($report_record['job_id']);

            if($get_total_job_hours_arr){
                $total_hours = getDayWiseDifference($get_total_job_hours_arr['dateFrom'],$get_total_job_hours_arr['dateTo'],'hours_minute_num');
            }

            $data['report_header'] = $report_record;
            $data['job_master'] = $get_jobs_master_detail;
            $data['report_detail'] = $get_jobs_detail;
            $data['job_id'] = $report_record['job_id'];
            $data['report_id'] = $report_record['id'];
            $data['total_hours_job'] = $total_hours;
            
            if($html){
                $html = $this->load->view('system/operations/reports/templates/daily_job_report_edit', $data,true);
            }else{
                $data['pdf'] = 1;
                $html = $this->load->view('system/operations/reports/templates/daily_job_report_pdf', $data,true);
            }

            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');

                
                $pdf = $this->pdf->printed($html, 'A4');
            }

        }
       
    }

    function load_work_over_rig_daily_report(){
        
        $shift_id = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $html  = $this->input->post('html');
        
        if($html){
            $shift_id = $this->input->post('id');
        }
    
        $companyID = $this->common_data['company_data']['company_id'];   
     
        $data = array();
        $total_hours = '0:00';
        
        $report_record = get_daily_report_master($shift_id);

        

        if($report_record){

            $get_jobs_master_detail = get_jobs_master_detail($report_record['job_id']);
            $get_jobs_header_detail = get_jobs_header_detail($get_jobs_master_detail['customer_id']); // get header details workover services

            $dateFrom = $report_record['dateFrom'];
            $dateTo = $report_record['dateTo'];

            $operation_details = $this->db->query("SELECT
                                `srp_erp_job_activitydetail`.*,
                                `srp_erp_job_activitydetail`.`type` AS `type`,
                                `srp_erp_job_activitydetail`.`id` AS `id`,
                                `srp_erp_job_activitydetail`.`dateFrom` AS `dateFrom`,
                                `srp_erp_job_activitydetail`.`dateTo` AS `dateTo`,
                                `srp_erp_job_activitydetail`.`isStandby` AS `isStandby`,
                                `srp_erp_job_activitydetail`.`isNpt` AS `isNpt` 
                            FROM
                                `srp_erp_job_activitydetail` 
                            WHERE
                                `companyID` = {$companyID} 
                                AND `job_id` = {$report_record['job_id']}
                                AND `shift_id` = {$shift_id}
                                AND (
                                    (`dateFrom` BETWEEN '{$dateFrom}' AND '{$dateTo}') OR
                                    (`dateTo` BETWEEN '{$dateFrom}' AND '{$dateTo}') OR
                                    (`dateFrom` <= '{$dateFrom}' AND `dateTo` >= '{$dateTo}')
                                )
                            ORDER BY
                                `id` ASC ")->result_array();


            //$job_master = get_jobs_master_detail($job_id);

            $get_jobs_detail = get_daily_report_details($report_record['id']);

            $get_total_job_hours_arr = get_daily_report_master_total_hours($report_record['job_id']);
            //var_dump($get_jobs_master_detail); exit;
            if($get_total_job_hours_arr){
                $total_hours = getDayWiseDifference($get_total_job_hours_arr['dateFrom'],$get_total_job_hours_arr['dateTo'],'hours_minute_num');
            }

            $data['report_header'] = $report_record;
            $data['report_header_wll'] = $get_jobs_header_detail;
            $data['job_master'] = $get_jobs_master_detail;
            $data['report_detail'] = $get_jobs_detail;
            $data['job_id'] = $report_record['job_id'];
            $data['report_id'] = $report_record['id'];
            $data['total_hours_job'] = $total_hours;
            $data['operation_details'] = $operation_details;
            $data['dateFrom'] = $dateFrom;
            $data['dateTo'] = $dateTo;
            
            if($html){
                $html = $this->load->view('system/operations/reports/templates/work_over_rig_daily_report_pdf', $data,true);
            }else{
                $data['pdf'] = 1;
                $html = $this->load->view('system/operations/reports/templates/work_over_rig_daily_report_pdf', $data,true);
            }

            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');

                
                $pdf = $this->pdf->printed($html, 'A4');
            }

        }
       
    }

    function load_well_report(){
        
        $shift_id = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $html  = $this->input->post('html');
        
        if($html){
            $shift_id = $this->input->post('id');
        }
    
        $companyID = $this->common_data['company_data']['company_id'];   
     
        $data = array();
        $total_hours = '0:00';
        
        $report_record = get_daily_report_master($shift_id);

        

        if($report_record){

            $get_jobs_master_detail = get_jobs_master_detail($report_record['job_id']);
            $get_jobs_header_detail = get_jobs_header_detail($get_jobs_master_detail['customer_id']); // get header details workover services

            //var_dump($get_jobs_master_detail);
            //exit;

            $credetails_list = $this->db->query("SELECT
                                    `sjc`.*,
                                    `se`.`ECode` AS `ECode`,
                                    `group`.`groupName`,
                                    `group`.`groupType`,
                                    `group`.`groupAutoID` AS `groupAutoID`,
                                    `sjc`.`dateFrom` AS `dateFrom`,
                                    `sjc`.`dateTo` AS `dateTo`,
                                    `sjc`.`empID` AS `empID`,
                                    `sjc`.`id` AS `id`,
                                    `sjc`.`competency_check`,
                                    `sjc`.`training_check`,
                                    `sjc`.`ssc_check` 
                                FROM
                                    `srp_erp_job_crewdetail` AS `sjc`
                                    LEFT JOIN `srp_employeesdetails` AS `se` ON `sjc`.`empID` = `se`.`EIdNo`
                                    LEFT JOIN `srp_erp_op_module_group_to` AS `group` ON `sjc`.`groupID` = `group`.`groupAutoID`
                                    LEFT JOIN `srp_erp_job_shift_crewdetail` AS `shift_group` ON `sjc`.`empID` = `shift_group`.`empID` 
                                    AND `shift_group`.`job_id` = {$report_record['job_id']} 
                                WHERE
                                    `sjc`.`companyID` = {$companyID} 
                                    AND `sjc`.`job_id` = {$report_record['job_id']}
                                    AND `shift_group`.`empID` IS NULL 
                                ORDER BY
                                    `id` DESC")->result_array();

            $asset_list = $this->db->query("SELECT
                            `srp_erp_job_assetsdetail`.*,
                            `srp_erp_job_assetsdetail`.`id` AS `id`,
                            `srp_erp_job_assetsdetail`.`assetCode` AS `assetCode`,
                            `srp_erp_job_assetsdetail`.`assetName` AS `assetName`,
                            `srp_erp_job_assetsdetail`.`dateFrom` AS `dateFrom`,
                            `srp_erp_job_assetsdetail`.`dateTo` AS `dateTo`,
                            `srp_erp_job_assetsdetail`.`faID` AS `faID`,
                            `maintenance_check` 
                        FROM
                            `srp_erp_job_assetsdetail` 
                        WHERE
                            `companyID` = {$companyID} 
                            AND `job_id` = {$report_record['job_id']}
                        ORDER BY
                            `id` DESC")->result_array();



            //$job_master = get_jobs_master_detail($job_id);

            $get_jobs_detail = get_daily_report_details($report_record['id']);

            $get_total_job_hours_arr = get_daily_report_master_total_hours($report_record['job_id']);
            //var_dump($get_jobs_master_detail); exit;
            if($get_total_job_hours_arr){
                $total_hours = getDayWiseDifference($get_total_job_hours_arr['dateFrom'],$get_total_job_hours_arr['dateTo'],'hours_minute_num');
            }

            $data['report_header'] = $report_record;
            $data['report_header_wll'] = $get_jobs_header_detail;
            $data['job_master'] = $get_jobs_master_detail;
            $data['report_detail'] = $get_jobs_detail;
            $data['job_id'] = $report_record['job_id'];
            $data['report_id'] = $report_record['id'];
            $data['total_hours_job'] = $total_hours;
            $data['credetails_list'] = $credetails_list;
            $data['asset_list'] = $asset_list;
            
            if($html){
                $html = $this->load->view('system/operations/reports/templates/well_report_pdf', $data,true);
            }else{
                $data['pdf'] = 1;
                $html = $this->load->view('system/operations/reports/templates/well_report_pdf', $data,true);
            }

            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');

                
                $pdf = $this->pdf->printed($html, 'A4');
            }

        }
       
    }
    

    function load_print_billing_detail_report(){
        
        $billing_id = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $html  = $this->input->post('html');
        
        if($html){
            $billing_id = $this->input->post('id');
        }
    
        $companyID = $this->common_data['company_data']['company_id'];   
        $companyCode = $this->common_data['company_data']['company_code']; 
        $data = array();
        $get_jobs_detail = array();
        
        $report_record = get_billing_master_record($billing_id);


        if($report_record){

            $get_jobs_master_detail = get_jobs_master_detail($report_record['job_id']);

            $get_defined_shifts = get_job_defined_shifts($report_record['job_id'],$report_record['dateFrom'],$report_record['dateTo']);

            $get_defined_prices = get_defined_prices_for_contract($get_jobs_master_detail['contract_po_id'],2);

          

            foreach($get_defined_shifts as $shift_key => $shift_details){


                $defined_shift_from = $shift_details['dateFrom'];
                $defined_shift_to = $shift_details['dateTo'];

                $get_jobs_detail = get_billing_detail_record($report_record['id'],$defined_shift_from,$defined_shift_to);

             

                $defined_price_arr = array();
                foreach($get_jobs_detail as $job_details){
                    if(isset($defined_price_arr[$job_details['price_id']])){
                        $defined_price_arr[$job_details['price_id']] = $defined_price_arr[$job_details['price_id']] + $job_details['qty'];
                    }else{
                        $defined_price_arr[$job_details['price_id']] = $job_details['qty'];
                    }
                    

                }

                

                $get_defined_shifts[$shift_key]['values'] = $defined_price_arr;

            }

            // echo '<pre>';
            // print_r($get_defined_prices); exit;

            $data['report_header'] = $report_record;
            $data['job_master'] = $get_jobs_master_detail;
            $data['report_detail'] = $get_jobs_detail;
            $data['job_id'] = $report_record['job_id'];
            $data['report_id'] = $report_record['id'];
            $data['get_defined_prices'] = $get_defined_prices;
            $data['get_defined_shifts'] = $get_defined_shifts;
            $data['transactionCurrency'] = $get_jobs_master_detail['localCurrencyCode'];
            $data['job_code'] = $get_jobs_master_detail['job_code'];
            $data['well_name'] = $get_jobs_master_detail['well_name'];
            $data['companyCode'] = $companyCode;
            $data['billing_id'] = $billing_id;


            if($html){
                $html = $this->load->view('system/operations/reports/templates/cost_tracking_daily_basis_edit', $data,true);
            }else{
                $html = $this->load->view('system/operations/reports/templates/cost_tracking_daily_basis_edit', $data,true);
            }

            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html,'A4',$report_record['confirmedYN']);
            }

        }
       
    }

    function update_daily_report_values(){

        $this->form_validation->set_rules('value', 'Value', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->update_daily_report_values());
        }
        
    }

    function load_daily_job_report_print()
    {                
        $data["details"] = "";
        $html = $this->load->view('system/operations/reports/templates/daily_job_report', $data,true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function load_job_all_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $job_id = trim($this->input->post('job_id') ?? '');

        $where = "job_id = $job_id";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_op_job_attachments');
        $this->db->where($where);
        $this->db->order_by('id', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_all_op_job_attachements', $data);
    }

    function load_job_all_attachments_well()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $job_id = trim($this->input->post('job_id') ?? '');

        $where = "id = $job_id";
        $this->db->select('*');
        $this->db->from('srp_erp_jobsmaster');
        $this->db->where($where);
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_all_op_job_well_attachements', $data);
    }

    function load_job_all_attachments_bob()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $job_id = trim($this->input->post('job_id') ?? '');

        $where = "id = $job_id";
        $this->db->select('*');
        $this->db->from('srp_erp_jobsmaster');
        $this->db->where($where);
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_all_op_job_bob_attachements', $data);
    }

    function load_checklist_attachment()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $checklist_header_id = trim($this->input->post('checklist_header_id') ?? '');

        $where = "id = $checklist_header_id";
        $this->db->select('*');
        $this->db->from('srp_erp_op_checklist_header');
        $this->db->where($where);
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/crm/ajax/load_checklist_attachements', $data);
    }

    function fetch_prejob_checklists(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        $data = array();
        $check_lists = array();
        $added_arr = array();

        $job_master = get_jobs_master_detail($job_id);

        if($job_master){

            if(!isset($job_master['pre_job_checklist'])) {

                $contract_id = $job_master['contract_po_id'];

                $contract_checklist = get_checklist_added_to_contract($contract_id,'perJob');
    
                foreach($contract_checklist as $key => $checklists){
                    
                    $header_id = create_checklist_header_record($checklists['checklistID'],$contract_id,$job_id);
                   
                    $check_lists[] = $header_id;
                    $contract_checklist[$key]['header_master_id'] = $header_id;
                
                }   
                $checklist_str = join(',',$check_lists);
    
                //update master
                $res = update_table_record_field($job_id,$checklist_str,'pre_job_checklist','srp_erp_jobsmaster');
    
                
                foreach($contract_checklist as $added_details){
        
                    $header_master = get_checklist_header_record($added_details['header_master_id']);
                    $temp_arr = array('confirmed' => $header_master['is_confirmed'],'checklist_name' => $added_details['checklist_name'],'header_id' => $added_details['header_master_id']);
                    $added_arr[] = $temp_arr;
                }

            }else{
                $pre_checklist = $job_master['pre_job_checklist'];

                $pre_checklist_arr = explode(',',$pre_checklist);

                if(!isset($pre_checklist_arr)){
                    foreach($pre_checklist_arr as $checklistID){

                        $header_master = get_checklist_header_record($checklistID);                    
                        $temp_arr = array('confirmed' => $header_master['is_confirmed'],'checklist_name' => $header_master['checklist_name'],'header_id' => $checklistID);
                        $added_arr[] = $temp_arr;

                    }
                }
            }
           
        }

        $data['checklist'] = $added_arr;
        

        $this->load->view('system/operations/ajax/pre_job_checklist_tbl', $data);

    }

    function save_billing_header(){
        $this->form_validation->set_rules('reportFromDate', 'Shift Start', 'trim|required');
        $this->form_validation->set_rules('reportToDate', 'Shift To', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_billing_header());
        }
    }

    function get_activity_details(){
        echo json_encode($this->Jobs_model->get_activity_details());
    }

    function get_price_details(){
        echo json_encode($this->Jobs_model->get_price_details());
    }

    function save_billing_detail_item(){
        
        $this->form_validation->set_rules('activity_id[]', 'Activity', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_billing_detail_item());
        }

    }

    function assign_contact_item_for_job_billing(){
        
        $this->form_validation->set_rules('job_id', 'Job', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->assign_contact_item_for_job_billing());
        }

    }

    function load_billing_detail_section(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        $billing_header_id = $this->input->post('billing_header_id');
        $data = array();
        $results = array();

        $billing_config = get_billing_master_record($billing_header_id);

        if($billing_config){
            $dateFrom = $billing_config['dateFrom'];
            $dateTo = $billing_config['dateTo'];

            $results = get_job_activity_details_between_date($job_id,$dateFrom,$dateTo,1);

        }

        $data['activity'] = $results;
        $data['job_id'] = $job_id;

        $this->load->view('system/operations/ajax/job_billing_detail', $data);

    }

    function assignItem_checklist_view_job()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $job_id = $this->input->post('job_id');
        $text = trim($this->input->post('Search') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) 
        { 
            $search_string = "AND ((name Like '%" . $text . "%') OR (	document_reference_code Like '%" . $text . "%'))";
        }

        $data['checklists'] = $this->db->query("SELECT * FROM srp_erp_op_checklist_master where companyID = {$companyID} AND status =1  AND id NOT IN (SELECT master_id FROM srp_erp_op_checklist_header WHERE job_id = {$job_id} AND is_deleted = 1 AND companyID = {$companyID}) $search_string")->result_array();

        $this->load->view('system/sales/master/ajax/job_checklist_table', $data);
    }

    function assignCheckListForContract_job()
    {
        echo json_encode($this->Jobs_model->assignCheckListForContract_job());
    }

    function fetch_check_list_job(){
        
        //see jobs
        $job_id = $this->input->post('job_id');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select('id as id ,master_id as checklist_id,is_confirmed as is_confirmed,doc_code,doc_name,job_id as job_id,is_deleted as is_deleted,completed_by as completed_by');
        $this->datatables->from('srp_erp_op_checklist_header');
        $this->datatables->where('srp_erp_op_checklist_header.companyID',$companyID);
        $this->datatables->where('srp_erp_op_checklist_header.job_id',$job_id);
        $this->datatables->add_column('action','$1','fetch_checklist_contract_action_job(id,checklist_id,is_deleted)');
        $this->datatables->add_column('doc_status','$1','fetch_checklist_status_job(is_confirmed)');
        echo $this->datatables->generate();

    }

    function delete_job_manual_checklist(){
        echo json_encode($this->Jobs_model->delete_job_manual_checklist());
    }

    function save_activity_crew_detail(){

        $this->form_validation->set_rules('job_id', 'Job', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_activity_crew_detail());
        }

        
    }

    function fetch_other_checklists(){

        $companyID = $this->common_data['company_data']['company_id'];
        $job_id = $this->input->post('job_id');
        $data = array();
        $check_lists = array();
        $added_arr = array();

        $job_master = get_jobs_master_detail($job_id);

        if($job_master){

            ///add checklist for job user asign
            $contract_add_all_checklist = get_checklist_added_to_contract_for_add_users($job_master['contract_po_id']);

            if($contract_add_all_checklist){
                foreach($contract_add_all_checklist as $key => $checklists){

                    $this->Jobs_model->save_job_checklist_users_detail($job_id,$checklists['checklistID']);
                
                } 
            }

            if(!isset($job_master['other_checklist'])) {

                $contract_id = $job_master['contract_po_id'];

                $contract_checklist = get_checklist_added_to_contract($contract_id,'other');
    
                foreach($contract_checklist as $key => $checklists){
                    
                    $header_id = create_checklist_header_record($checklists['checklistID'],$contract_id,$job_id);
                   
                    $check_lists[] = $header_id;
                    $contract_checklist[$key]['header_master_id'] = $header_id;
                
                }   
                $checklist_str = join(',',$check_lists);
    
                //update master
                $res = update_table_record_field($job_id,$checklist_str,'other_checklist','srp_erp_jobsmaster');
    
                
                foreach($contract_checklist as $added_details){
        
                    $header_master = get_checklist_header_record($added_details['header_master_id']);
                    $temp_arr = array('confirmed' => $header_master['is_confirmed'],'checklist_name' => $added_details['checklist_name'],'header_id' => $added_details['header_master_id']);
                    $added_arr[] = $temp_arr;
                }

            }else{
                $pre_checklist = $job_master['other_checklist'];

                $pre_checklist_arr = explode(',',$pre_checklist);

               

                foreach($pre_checklist_arr as $checklistID){

                    $header_master = get_checklist_header_record($checklistID);
                    $temp_arr = array('confirmed' => $header_master['is_confirmed'],'checklist_name' => $header_master['checklist_name'],'header_id' => $checklistID);
                    $added_arr[] = $temp_arr;

                }

            }
           
        }

    }

    function get_group_list(){
        echo json_encode($this->Jobs_model->get_group_list());
    }

    function save_job_group_to(){
        echo json_encode($this->Jobs_model->save_job_group_to());
    }

    function save_pipe_tally_detail(){
        echo json_encode($this->Jobs_model->save_pipe_tally_detail());
    }


    function save_visitor_log_detail(){
        echo json_encode($this->Jobs_model->save_visitor_log_detail());
    }
    function save_fuel_recived_detail(){
        $this->form_validation->set_rules('startdate', 'Date Required', 'required');
        $this->form_validation->set_rules('fuelusageID', 'Fuel Type Required', 'required');
        $this->form_validation->set_rules('qtynum', 'Quantity Required', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_fuel_detail());
        }
        
       
    }

    function save_fuel_Issue_detail(){
        $this->form_validation->set_rules('startdate', 'Date Required', 'required');
        $this->form_validation->set_rules('fuelusageID', 'Fuel Type Required', 'required');
        $this->form_validation->set_rules('qtynum', 'Quantity Required', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_fuel_detail());
        }
        
    }
    

    function fetch_check_list_contract_job()
    {
        $jobAutoID = $this->input->post('job_id');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->db->select('*');
        $this->db->from('srp_erp_op_module_job_checklist_users');
        $this->db->where('srp_erp_op_module_job_checklist_users.companyID',$companyID);
        $this->db->where('srp_erp_op_module_job_checklist_users.jobAutoID',$jobAutoID);
        $this->db->order_by('srp_erp_op_module_job_checklist_users.jobChecklistUserAutoID','desc');

        $data['details'] = $this->db->get()->result_array();

        $data['job_master'] = get_jobs_master_detail($jobAutoID);

        $html = $this->load->view('system/sales/master/ajax/job_checklist_user_html_table', $data, true);

        echo $html;
    }

    public function selectChecklistUserUpdate()
    {
        $this->form_validation->set_rules('masterID', 'Check List', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Jobs_model->selectChecklistUserUpdate());
        }
    }

    public function confirm_billing(){
        echo json_encode($this->Jobs_model->confirm_billing());
    }

    public function confirm_billing_standard(){
        echo json_encode($this->Jobs_model->confirm_billing_standard());
    }

    public function generate_sales_order(){
        echo json_encode($this->Jobs_model->generate_sales_order());
    }

    public function edit_billing_details(){
        echo json_encode($this->Jobs_model->edit_billing_details());
    }

    public function edit_report_details(){
        echo json_encode($this->Jobs_model->edit_report_details());
    }

    public function save_visitor_log_request(){
        $this->form_validation->set_rules('userName', 'User Name', 'required');
        $this->form_validation->set_rules('userEmail', 'Email', 'required');
        $this->form_validation->set_rules('userMessage', 'Message', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Jobs_model->save_visitor_log_request());
        }
    }

    function operation_document_save()
    {
        $this->form_validation->set_rules('documentName', 'Document name', 'trim|required');
        //$this->form_validation->set_rules('doc_file', 'file', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $documentName = $this->input->post('documentName');

            $existingDataID = $this->db->select('id')->from('srp_erp_operation_documents')
                ->where(['companyID' => $companyID, 'documentDescription' => $documentName])
                ->get()->row('id');

            if (!empty($existingDataID)) {
                die(json_encode(['e', 'This description is already exist']));
            }


            $fileName = $companyID . $documentName;
            $fileName = str_replace(' ', '', strtolower($fileName)) . '_' . time();
            $file = $_FILES['doc_file'];

            if ($file['error'] == 1) {
                die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)."]));
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if (!in_array($ext, $allowed_types)) {
                die(json_encode(['e', "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }

            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if ($size > 5) {
                die(json_encode(['e', "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]));
            }


            $fileName = "documents/operation_documents/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $fileName);

            if (!$s3Upload) {
                die(json_encode(['e', 'Error in document upload location configuration']));
            }

            /*$config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '500000';*/

            $data = array(
                'documentDescription' => $documentName,
                'documentFile' => $fileName,
                'companyID' => $companyID,
                'createdPCID' => current_pc(),
                'createdUserGroup' => current_user_group(),
                'createdUserID' => current_userID(),
                'createdUserName' => current_employee(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('srp_erp_operation_documents', $data);


            if ($this->db->affected_rows() > 0) {
                echo json_encode(['s', 'Document successfully uploaded']);
            } else {
                echo json_encode(['e', 'Error in document upload']);
            }
        }
    }

    function edit_operationDocument()
    {
        $this->form_validation->set_rules('documentName', 'Document name', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'id', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $documentName = $this->input->post('documentName');
            $id = $this->input->post('hidden-id');

            $existingDataID = $this->db->select('id')->from('srp_erp_operation_documents')
                ->where(['companyID' => $companyID, 'documentDescription' => $documentName])
                ->get()->row('id');

            if (!empty($existingDataID) && $existingDataID != $id) {
                die(json_encode(['e', 'This description is already exist']));
            }


            $updateData = array(
                'documentDescription' => $documentName,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => current_userID(),
                'modifiedUserName' => current_employee(),
                'modifiedDateTime' => current_date()
            );

            $this->db->trans_start();

            $this->db->where(['id' => $id, 'companyID' => $companyID])->update('srp_erp_operation_documents', $updateData);


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in document update']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['s', 'Document successfully updated']);
            }

        }
    }

    function delete_operationDocument()
    {

        $this->form_validation->set_rules('hidden-id', 'id', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $id = $this->input->post('hidden-id');


            $file = $this->db->get_where('srp_erp_operation_documents', ['id' => $id, 'companyID' => $companyID])->row('documentFile');
            $this->s3->delete($file);

            $this->db->trans_start();
            $this->db->where(['id' => $id, 'companyID' => $companyID])->delete('srp_erp_operation_documents');


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['e', 'Error in document delete process']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['s', 'Document successfully deleted']);
            }

        }
    }

    function assign_assets_common_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $contractAutoID = $this->input->post('contractAutoID');
        $text = trim($this->input->post('Search') ?? '');
        $jobID = trim($this->input->post('jobID') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) 
        { 
            $search_string = "AND ((faCode Like '%" . $text . "%') OR (	assetDescription Like '%" . $text . "%'))";
        }

        $data['common_asset'] = $this->db->query("SELECT * FROM srp_erp_fa_asset_master where companyID = {$companyID} AND confirmedYN =1 AND  approvedYN=1 AND  faID NOT IN (SELECT faID FROM srp_erp_contractassets WHERE contractAutoID = {$contractAutoID} AND companyID = {$companyID}) AND  faID NOT IN (SELECT faID FROM srp_erp_job_assetsdetail WHERE job_id = {$jobID} AND companyID = {$companyID}) $search_string")->result_array();

        //$data['checklists'] = $this->db->query("SELECT * FROM srp_erp_checklistmaster where companyID = {$companyID}  AND isActive =1  $search_string")->result_array();

        $this->load->view('system/operations/ajax/table_common_asset', $data);
    }

    function assignCommon_AssetListForContract_job(){
        echo json_encode($this->Jobs_model->assignCommon_AssetListForContract_job());
    }

    function fetch_contact_details()
    {
        echo json_encode($this->Jobs_model->fetch_contact_details());
    }

    function save_standard_billing_item_qty()
    {
        echo json_encode($this->Jobs_model->save_standard_billing_item_qty());
    }

    function save_standard_billing_date()
    {
        echo json_encode($this->Jobs_model->save_standard_billing_date());
    }

    function load_print_billing_detail_report_standard(){
        
        $billing_id = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $html  = $this->input->post('html');
        
        if($html){
            $billing_id = $this->input->post('id');
        }
    
        $companyID = $this->common_data['company_data']['company_id'];   
        $companyCode = $this->common_data['company_data']['company_code']; 
        $data = array();
        $get_jobs_detail = array();
        
        $report_record = get_billing_master_record($billing_id);


        if($report_record){

            $get_jobs_master_detail = get_jobs_master_detail($report_record['job_id']);

            $this->db->select('*');
            $this->db->from('srp_erp_job_billing_detail');
            $this->db->where('srp_erp_job_billing_detail.companyID',$companyID);
            $this->db->where('srp_erp_job_billing_detail.job_id',$report_record['job_id']);
            $this->db->where('srp_erp_job_billing_detail.billing_header',$billing_id);
            $data_billing_details = $this->db->get()->result_array();

            $data_inv =[];
            $data_service =[];
            $total =0;

            if(count( $data_billing_details)>0){
                foreach( $data_billing_details as $val){
                    $this->db->select('srp_erp_contractdetails.*,srp_erp_itemcategory.codePrefix');
                    $this->db->from('srp_erp_contractdetails');
                    $this->db->join('srp_erp_itemcategory','srp_erp_itemcategory.itemCategoryID = srp_erp_contractdetails.mainCategoryID AND srp_erp_itemcategory.companyID = srp_erp_contractdetails.companyID','left');
                    $this->db->where('srp_erp_contractdetails.contractDetailsAutoID',$val['price_id']);
                    $contract_billing_details = $this->db->get()->row_array();
                    $total =$total+$val['total_amount'];
                    if($contract_billing_details){

                        if($contract_billing_details['codePrefix']=='SRV'){
                            $data_service[] =["description"=>$val['description'],"comment"=>$contract_billing_details['comment'],"fromdate"=>$val['dateFrom'],"todate"=>$val['dateTo'],"qty"=>$val['qty'],"unit"=>$val['unit_amount'],"total"=>$val['total_amount']] ;
                        }else{
                            $data_inv[] =["description"=>$val['description'],"comment"=>$contract_billing_details['comment'],"fromdate"=>$val['dateFrom'],"todate"=>$val['dateTo'],"qty"=>$val['qty'],"unit"=>$val['unit_amount'],"total"=>$val['total_amount']] ;
                        }

                    }

                }
            }

           
            $jobs_master_with_contract =get_jobs_master_with_contract_details($report_record['job_id']);

             $data['report_header'] = $report_record;
             $data['job_master'] = $get_jobs_master_detail;
            // $data['report_detail'] = $get_jobs_detail;
            // $data['job_id'] = $report_record['job_id'];
            // $data['report_id'] = $report_record['id'];
            // $data['get_defined_prices'] = $get_defined_prices;
            // $data['get_defined_shifts'] = $get_defined_shifts;
             $data['transactionCurrency'] = $get_jobs_master_detail['localCurrencyCode'];
             $data['job_code'] = $get_jobs_master_detail['job_code'];
             $data['well_name'] = $get_jobs_master_detail['well_name'];
            // $data['companyCode'] = $companyCode;
             $data['billing_id'] = $billing_id;
            $data['total']=$total;
            $data['inv_data'] = $data_inv;
            $data['service_data'] = $data_service;
            $data['code'] =$report_record['code'];
            $data['comment']=$report_record['confirmComment'];
            $data['jobs_master_with_contract']= $jobs_master_with_contract;
           // print_r($data);exit;


            if($html){
                $html = $this->load->view('system/operations/reports/templates/billing_standard', $data,true);
            }else{
                $html = $this->load->view('system/operations/reports/templates/billing_standard', $data,true);
            }

            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html,'A4',$report_record['confirmedYN']);
            }

        }
       
    }

    function save_item_order_detail_job_billing_modify()
    {
        $pID = $this->input->post('pID');

        $description = $this->input->post('description');
        $datefrom_modify = $this->input->post('datefrom_modify');
        $dateto_modify = $this->input->post('dateto_modify');
        $isStandby_modify = $this->input->post('isStandby_modify');
        $isNpt_modify = $this->input->post('isNpt_modify');
        $min_modify = $this->input->post('min_modify');
        $pID = $this->input->post('pID');
        $qty_modify = $this->input->post('qty_modify');
        $rate_modify = $this->input->post('rate_modify');

        $total_modify = $this->input->post('total_modify');
        $job_id = $this->input->post('job_id');
        $billing_id = $this->input->post('billing_id');

        $movingcost = $this->input->post('movingcost');
        $Additionalcost = $this->input->post('Additionalcost');

        $this->db->select('*');
        $this->db->where('id',$billing_id);
        $this->db->where('companyID',$this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_job_billing');
        $result_exist_header = $this->db->get()->row_array();

        $dateFrom=strtotime($result_exist_header['dateFrom']);
        $dateTo=strtotime($result_exist_header['dateTo']);
        

        foreach ($pID as $key => $search) {

           // $this->form_validation->set_rules("description[{$key}]", 'Description', 'trim|required');
            $this->form_validation->set_rules("datefrom_modify[{$key}]", 'From Date', 'trim|required');
            $this->form_validation->set_rules("dateto_modify[{$key}]", 'To Date', 'trim|required');
            $this->form_validation->set_rules("pID[{$key}]", 'Price List', 'trim|required');

            $this->form_validation->set_rules("min_modify[{$key}]", 'Hours/mins', 'trim|required');
            $this->form_validation->set_rules("qty_modify[{$key}]", 'Qty', 'trim|required');
            $this->form_validation->set_rules("rate_modify[{$key}]", 'Rate', 'trim|required');
            $this->form_validation->set_rules("total_modify[{$key}]", 'Total', 'trim|required');
            $this->form_validation->set_rules("movingcost[{$key}]", 'moving cost', 'trim|required');
            $this->form_validation->set_rules("Additionalcost[{$key}]", 'Additional Rental Cost', 'trim|required');

            $dateFrom_new = $datefrom_modify[$key];
            $dateTo_new = $dateto_modify[$key];
            $dateFrom_new1=strtotime($dateFrom_new);
            $dateTo_new1=strtotime($dateTo_new);
            if($dateFrom_new1 >= $dateFrom && $dateTo>=$dateTo_new1)
            {
                
            }else{
                $this->form_validation->set_rules("total_modify", 'Please add date between Billing master fromDate and toDate', 'trim|required');
            }
            
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
            echo json_encode($this->Jobs_model->save_item_order_detail_job_billing_modify());
        }
    }

    function load_print_billing_detail_report_modify(){
        
        $billing_id = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $html  = $this->input->post('html');
        
        if($html){
            $billing_id = $this->input->post('id');
        }
    
        $companyID = $this->common_data['company_data']['company_id'];   
        $companyCode = $this->common_data['company_data']['company_code']; 
        $data = array();
        $get_jobs_detail = array();
        
        $report_record = get_billing_master_record($billing_id);


        if($report_record){

            $get_jobs_master_detail = get_jobs_master_detail($report_record['job_id']);

            $get_defined_shifts = get_job_defined_shifts($report_record['job_id'],$report_record['dateFrom'],$report_record['dateTo']);

            $get_defined_prices = get_defined_prices_for_contract_with_category_group($get_jobs_master_detail['contract_po_id'],2);

            $get_jobs_detail = get_billing_detail_record($report_record['id']);

            // $get_jobs_detail_with_date_group = get_billing_detail_record_with_date_group($report_record['id']);
            // $final = [];
            // print_r($get_jobs_detail_with_date_group);exit;
            // foreach($get_jobs_detail_with_date_group as $shift_key => $shift_details){

                
            //     $get_jobs_detail_x = get_billing_detail_record($report_record['id'],$shift_details['dateFrom'],$shift_details['dateTo']);

            //     foreach($get_jobs_detail_x as $val2){
            //         $final[] = ["dateFrom"=>$shift_details['dateFrom'],"dateTo"=>$shift_details['dateTo'],"qty"=>$shift_details['qty'],"unit_amount"=>$shift_details['unit_amount'],"price_id"=>];
            //     }


            //     print_r($get_jobs_detail);exit;

            // }

            // echo '<pre>';
            // print_r($get_defined_prices); exit;

            $data['report_header'] = $report_record;
            $data['job_master'] = $get_jobs_master_detail;
            $data['report_detail'] = $get_jobs_detail;
            $data['job_id'] = $report_record['job_id'];
            $data['report_id'] = $report_record['id'];
            $data['get_defined_prices'] = $get_defined_prices;
            $data['get_defined_shifts'] = $get_jobs_detail;
            $data['transactionCurrency'] = $get_jobs_master_detail['localCurrencyCode'];
            $data['job_code'] = $get_jobs_master_detail['job_code'];
            $data['well_name'] = $get_jobs_master_detail['well_name'];
            $data['companyCode'] = $companyCode;
            $data['billing_id'] = $billing_id;


            if($html){
                $html = $this->load->view('system/operations/reports/templates/billing_report_job_contract_edit_mode', $data,true);
            }else{
                $html = $this->load->view('system/operations/reports/templates/billing_report_job_contract_edit_mode', $data,true);
            }

            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html,'A4',$report_record['confirmedYN']);
            }

        }
       
    }

    function fetch_billing_detail_added_modify(){

        $companyID = $this->common_data['company_data']['company_id'];
        $billing_header_id = $this->input->post('billing_header_id');
        
        $this->datatables->select('srp_erp_job_billing_detail.*,srp_erp_job_billing_detail.total_amount as total_amount,srp_erp_job_billing_detail.movingCost as movingCosts,srp_erp_job_billing_detail.additionalCost as additionalCosts,srp_erp_job_billing_detail.unit_amount as unit_amount,srp_erp_jobsmaster.localCurrencyCode as localCurrencyCode,srp_erp_job_billing_detail.id as id,srp_erp_job_billing_detail.isStandby as isStandby,srp_erp_job_billing_detail.isNpt as isNpt');
        $this->datatables->where('srp_erp_job_billing_detail.companyID',$companyID);
        $this->datatables->where('billing_header',$billing_header_id);
        $this->datatables->from('srp_erp_job_billing_detail');
        $this->datatables->join('srp_erp_jobsmaster','srp_erp_job_billing_detail.job_id = srp_erp_jobsmaster.id','left');
        $this->datatables->add_column('action','$1','get_added_actions_reports(id,"billing")');   
        $this->datatables->edit_column('isStandby','$1','get_is_yes_no(isStandby)');
        $this->datatables->edit_column('isNpt','$1','get_is_yes_no(isNpt)');
        $this->datatables->edit_column('unit_amount','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(unit_amount,2),localCurrencyCode');
        $this->datatables->edit_column('total_amount','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(total_amount,2),localCurrencyCode');
        $this->datatables->edit_column('movingCost','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(movingCosts,2),localCurrencyCode');
        $this->datatables->edit_column('additionalCost','<div class="pull-right"><b>$2 : </b> $1 </div>','format_number(additionalCosts,2),localCurrencyCode');
        // 
        echo $this->datatables->generate();

    }

}