<?php

class Jobs_model extends ERP_Model
{
    function __construct()
    {
        parent::__construct();
    }

   

    function get_jobs_master_fileds(){

        $get_fields_list = get_field_list_by_company(1);

        $base_array = array();

        foreach($get_fields_list as $field){
            $temp_filed_array = array('id' => $field['id'], 'name' => $field['field_name'] , 'parentid' => $field['mapping_field_well_id'], 'levelNo' => $field['levelNo'],'well_type_op' => $field['well_type_op'],'well_no_op' => $field['well_no_op']);

            $base_array[] = $temp_filed_array;

            $fieldID = $field['id'];

            $get_well_list = get_field_list_by_company(2,$fieldID);

            foreach($get_well_list as $well){

                $temp_well_array = array('id' => $well['id'], 'name' => $well['well_name'] , 'parentid' => $well['mapping_field_well_id'], 'levelNo' => $well['levelNo'],'well_type_op' => $well['well_type_op'],'well_no_op' => $well['well_no_op']);

                $base_array[] = $temp_well_array;

            }

        }

        return $base_array;

    }


    function get_jobs_master_fileds_rig_hoist(){

        $categoryType = trim($this->input->post('categoryType') ?? '');

        if($categoryType == 4){
            $get_fields_list = get_activity_category_list_by_company();
        }else{
            $get_fields_list = get_rig_hoist_list_by_company();
        }

        $base_array = array();

        foreach($get_fields_list as $field){
            $temp_filed_array = array('id' => $field['id'], 'name' => $field['rig_hoist_name'] , 'parentid' => $field['mapping_field_well_id'], 'levelNo' => $field['levelNo']);

            $base_array[] = $temp_filed_array;

        }

        return $base_array;

    }

    function add_crew_job(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $empID = trim($this->input->post('empID') ?? '');
        $designation = trim($this->input->post('designation') ?? '');
        $companyid = current_companyID();
        $data = array();
        $emp_already = 1;
        $date = date('Y-m-d H:i:s');

        $emp_details = get_employee_details_job($empID);

        if(empty($emp_details)){
            $this->session->set_flashdata('e', 'Something went wrong.');
            return true;
        }

        $emp_already_exists =  get_employee_added_crew_details($job_id,$empID);

        if($emp_already_exists){
            $this->session->set_flashdata('e', 'Employee is already added to this Job.');
            return true;
        } else {

            $data['job_id'] = $job_id;
            $data['empID'] = $empID;
            $data['name'] = $emp_details['Ename1'].' '.$emp_details['Ename2'];
            $data['designation'] = $designation;
            $data['dateFrom'] = date('Y-m-d H:i:s',strtotime($date));
            $data['dateTo'] = date('Y-m-d H:i:s',strtotime('+1 minute',strtotime($date)));
            $data['status'] = 1;
            $data['is_job_completed'] = 0;
            $data['companyID'] = $companyid;

            $this->db->insert('srp_erp_job_crewdetail',$data);

            $this->session->set_flashdata('s', 'Employee added Successfully.');
            return true;

        }

    }

    function add_crew_multiple(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $crew_id = $this->input->post('crew_id');
        $groupID = $this->input->post('group_jobcrew');
        
        $companyid = current_companyID();
        $data = array();
        $emp_already = 1;
        $date = date('Y-m-d H:i:s');
        $success_message = '';
        $error_message = '';


        //foreach($crew_ids_arr as $crew_id){

            $contract_crew = get_contract_crew_record($crew_id);
            

            if($contract_crew){

                $empID = $contract_crew['empID'];

                $emp_details = get_employee_details_job($empID);

                if(empty($emp_details)){
                    $this->session->set_flashdata('e', 'Something went wrong.');
                    return true;
                }

                $emp_already_exists =  get_employee_added_crew_details($job_id,$empID);
        
                if($emp_already_exists){
                    //$this->session->set_flashdata('e', $emp_already_exists['name'].'Employee is already added to this Job.');
                    $error_message .= $emp_already_exists['name'].'Employee is already added to this Job. <br> ';
                  
                } else {
        
                    $data['job_id'] = $job_id;
                    $data['empID'] = $empID;
                    $data['name'] = $emp_details['Ename1'].' '.$emp_details['Ename2'];
                    $data['designation'] = $contract_crew['empDesignation'];
                    $data['dateFrom'] = date('Y-m-d H:i:s',strtotime($date));
                    $data['dateTo'] = date('Y-m-d H:i:s',strtotime('+1 minute',strtotime($date)));
                    $data['status'] = 1;
                    $data['groupID'] = $groupID;
                    $data['is_job_completed'] = 0;
                    $data['companyID'] = $companyid;
        
                    $this->db->insert('srp_erp_job_crewdetail',$data);
        
                    // $this->session->set_flashdata('s', $data['name'].' Employee added Successfully.');
                    $success_message .= $data['name'].'Employee added Successfully. <br> ';
        
                }
                

            }

        //}

        return array('s'=>$success_message,'e'=>$error_message);

    }

    function add_crew_from_date(){
        $job_id = trim($this->input->post('job_id') ?? '');
        $dateFrom = trim($this->input->post('dateFrom') ?? '');
        $empID = trim($this->input->post('empID') ?? '');
        $taskID = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data_arr = array();
        $overlapped = null;

        $crew_details = get_employee_added_crew_details($job_id,$empID,$taskID);

        if($crew_details){

            $dateTo = $crew_details['dateTo'];
            $dateFromT = str_replace('T',' ',$dateFrom);
            
            if($dateTo && (strtotime($dateFromT) > strtotime($dateTo))){
                $this->session->set_flashdata('e', 'Date From is greater than date To.');
                return false;
            }else{
                $data_arr['dateFrom'] = $dateFromT;
            }

            //check for overlapping jobs
            if($dateFromT && $dateTo){
                $overlapped = $this->check_overlapped_task($empID,$dateFromT,$dateTo,$taskID);
            }
            
            if($overlapped){
                $this->session->set_flashdata('e', 'Date From is Overlapped with an another task assigned.');
                return false;
            }

    
            $this->db->where('id',$taskID);
            $this->db->where('companyID',$companyid);
            $this->db->update('srp_erp_job_crewdetail',$data_arr);

            $this->session->set_flashdata('s', 'Crew Member Date from Updated.');
            return true;

        }

    }

    function add_crew_to_date(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $dateTo = trim($this->input->post('dateTo') ?? '');
        $empID = trim($this->input->post('empID') ?? '');
        $taskID = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data_arr = array();
        $overlapped = null;

        $crew_details = get_employee_added_crew_details($job_id,$empID,$taskID);

        if($crew_details){

            $dateFrom = $crew_details['dateFrom'];
            $dateToT = str_replace('T',' ',$dateTo);

            if($dateFrom && (strtotime($dateFrom) > strtotime($dateToT))){
                $this->session->set_flashdata('e', 'Date From is greater than date To.');
                return true;
            }else{
                $data_arr['dateTo'] = $dateToT;
            }

            //check for overlapping jobs
            if($dateFrom && $dateToT){
                $overlapped = $this->check_overlapped_task($empID,$dateFrom,$dateToT,$taskID);
            }

            if($overlapped){
                $this->session->set_flashdata('e', 'Date From is Overlapped with an another task assigned.');
                return false;
            }

            $this->db->where('id',$taskID);
            $this->db->where('companyID',$companyid);
            $this->db->update('srp_erp_job_crewdetail',$data_arr);

            $this->session->set_flashdata('s', 'Crew Member Date To Updated.');
            return true;

        }

    }

    function add_asset_from_date(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $dateFrom = trim($this->input->post('dateFrom') ?? '');
        $faID = trim($this->input->post('faID') ?? '');
        $taskID = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data_arr = array();
        $overlapped = null;

        $asset_details = get_job_added_asset_details($job_id,$faID,$taskID);

        if($asset_details){

            $dateTo = $asset_details['dateTo'];
            $dateFromT = str_replace('T',' ',$dateFrom);
            
            if($dateTo && (strtotime($dateFromT) > strtotime($dateTo))){
                $this->session->set_flashdata('e', 'Date From is greater than date To.');
                return false;
            }else{
                $data_arr['dateFrom'] = $dateFromT;
            }

            //check for overlapping jobs
            if($dateFromT && $dateTo){
                $overlapped = $this->check_overlapped_task($faID,$dateFromT,$dateTo,$taskID,null,'fa');
            }
            
            if($overlapped){
                $this->session->set_flashdata('e', 'Date From is Overlapped with an another task assigned.');
                return false;
            }

    
            $this->db->where('id',$taskID);
            $this->db->where('companyID',$companyid);
            $this->db->update('srp_erp_job_assetsdetail',$data_arr);

            $this->session->set_flashdata('s', 'Assets Date from Updated.');
            return true;

        }

    }

    function add_asset_to_date(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $dateTo = trim($this->input->post('dateTo') ?? '');
        $faID = trim($this->input->post('faID') ?? '');
        $taskID = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data_arr = array();
        $overlapped = null;

        $asset_details = get_job_added_asset_details($job_id,$faID,$taskID);

        if($asset_details){

            $dateFrom = $asset_details['dateFrom'];
            $dateToT = str_replace('T',' ',$dateTo);

            if($dateFrom && (strtotime($dateFrom) > strtotime($dateToT))){
                $this->session->set_flashdata('e', 'Date From is greater than date To.');
                return true;
            }else{
                $data_arr['dateTo'] = $dateToT;
            }

            //check for overlapping jobs
            if($dateFrom && $dateToT){
                $overlapped = $this->check_overlapped_task($faID,$dateFrom,$dateToT,$taskID,null,'fa');
            }

            if($overlapped){
                $this->session->set_flashdata('e', 'Date From is Overlapped with an another task assigned.');
                return false;
            }

            $this->db->where('id',$taskID);
            $this->db->where('companyID',$companyid);
            $this->db->update('srp_erp_job_assetsdetail',$data_arr);

            $this->session->set_flashdata('s', 'Assets Date To Updated.');
            return true;

        }

    }
    
    function check_overlapped_task($empID,$requestDateFrom,$requestDateTo,$taskID = null,$arr = null,$type = null){

        $overlapped_arr = array();
        if($type == 'fa'){
            $tasks_list = get_asset_added_job_details($empID,$taskID);
        }else{
            $tasks_list = get_employee_added_job_task_details($empID,$taskID);
        }
        

        foreach($tasks_list as $task){

            $taskFrom = $task['dateFrom'];
            $taskTo = $task['dateTo'];

            if(!empty($taskFrom) && !empty($taskTo)){
                 //check for between
                if($taskFrom <= $requestDateFrom && $requestDateFrom <= $taskTo){
                    $overlapped_arr[] = $task['id'];
                }elseif($taskFrom <= $requestDateTo && $requestDateTo <= $taskTo){
                    $overlapped_arr[] = $task['id'];
                }elseif($requestDateFrom <= $taskFrom && $taskFrom <= $requestDateTo){
                    $overlapped_arr[] = $task['id'];
                }elseif($requestDateFrom <= $taskTo && $taskTo <= $requestDateTo){
                    $overlapped_arr[] = $task['id'];
                }
            }
           
        }

        if($arr){
            return $overlapped_arr;
        }else{
            if(count($overlapped_arr) > 0) {
                return True;
            }
        }
       


    }

    function get_employee_schdule(){

        $dateFrom = trim($this->input->post('dateFrom') ?? '');
        $empID = trim($this->input->post('empID') ?? '');
        $dateTo = trim($this->input->post('dateTo') ?? '');
        $job_id = trim($this->input->post('job_id') ?? '');
        $companyid = current_companyID();
        $base_array = array();

        $dateFromT = str_replace('T',' ',$dateFrom);
        $dateTo = str_replace('T',' ',$dateTo);

        $overlapped = $this->check_overlapped_task($empID,$dateFromT,$dateTo,null,true);

        if(count($overlapped) > 0) {
            
            foreach($overlapped as $key => $crew){
                $crewdetail_id = $crew;

                //get crew detail records
                $this->db->where('sjc.id',$crewdetail_id);
                $this->db->where('sjc.companyID',$companyid);
                $this->db->from('srp_erp_job_crewdetail as sjc');
                $this->db->join('srp_erp_jobsmaster as sj','sjc.job_id = sj.id','left');
                $result = $this->db->get()->row_array();

                $base_array[] = $result;

            }

            return $base_array;

        }else{
            return false;
        }
        
    }

    function save_jobs_detail_header(){

        $doc_id = trim($this->input->post('doc_id') ?? '');
        $doc_name = trim($this->input->post('doc_name') ?? '');
        $doc_date = trim($this->input->post('doc_date') ?? '');
        $doc_ref = trim($this->input->post('doc_ref') ?? '');
        $job_description = trim($this->input->post('job_description') ?? '');
        $customerCode = trim($this->input->post('customerCode') ?? '');
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');
        $currencyID = trim($this->input->post('currencyID') ?? '');
        $currencyCode = trim($this->input->post('currencyCode') ?? '');
        $po_number = trim($this->input->post('po_number') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $fromDate = trim($this->input->post('fromDate') ?? '');
        $toDate = trim($this->input->post('toDate') ?? '');
        $field_id = trim($this->input->post('field_id') ?? '');
        $well_id = trim($this->input->post('well_id') ?? '');
        $rig_id = trim($this->input->post('rig_id') ?? '');
        $job_id = trim($this->input->post('job_id') ?? '');
        $job_type = trim($this->input->post('job_type') ?? '');
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        
        $companyid = current_companyID();

        if($doc_id){

            $data_arr = array();

            $data_arr['job_name'] = $doc_name;
            $data_arr['job_status'] = $status;
            $data_arr['doc_date'] = date('Y-m-d H:i:s',strtotime($doc_date));
            $data_arr['job_code'] = $doc_id;
            $data_arr['job_description'] = $job_description;
            $data_arr['localCurrencyID'] = $currencyID;
            $data_arr['localCurrencyCode'] = $currencyCode;
            $data_arr['job_reference'] = $doc_ref;
            $data_arr['customer_id'] = $customerCode;
            $data_arr['contract_po_id'] = $contractAutoID;
            $data_arr['po_number'] = $po_number;
            $data_arr['job_date_from'] = date('Y-m-d H:i:s',strtotime($fromDate));
            $data_arr['job_date_to'] = date('Y-m-d H:i:s',strtotime($toDate));
            $data_arr['field_id'] = $field_id;
            $data_arr['well_id'] = $well_id;
            $data_arr['rig_hoist_id'] = $rig_id;
            $data_arr['job_type'] = $job_type;
            $data_arr['segmentID'] = trim($segment[0] ?? '');

            $data_arr['ptw_number'] = trim($this->input->post('ptw_number') ?? '');
            $data_arr['iso_certificate'] = trim($this->input->post('iso_certificate') ?? '');
            $data_arr['hot_permit_number'] = trim($this->input->post('hot_permit_number') ?? '');
            $data_arr['muster_area'] = trim($this->input->post('muster_area') ?? '');
            $data_arr['sftp_number'] = trim($this->input->post('sftp_number') ?? '');
            $data_arr['weight'] = trim($this->input->post('weight') ?? '');  
            $data_arr['prv_set'] = trim($this->input->post('prv_set') ?? '');
            $data_arr['job_obj_summary'] = trim($this->input->post('job_obj_summary') ?? '');
            $data_arr['job_start_time'] = trim($this->input->post('start_time') ?? '');
            $data_arr['shift_hours'] = trim($this->input->post('shift_hours') ?? '');
            $data_arr['well_number'] = trim($this->input->post('well_number') ?? '');
            $data_arr['pw_pump_number'] = trim($this->input->post('pw_pump_number') ?? '');

            $data_arr['companyID'] = $companyid;
            $data_arr['companyCode'] = $this->common_data['company_data']['company_code'];

            if($status == 4){
                // job complete


            }else{

                //check job code exsits

                try {

                    if($job_id){   

                        $this->db->where('id',$job_id)->update('srp_erp_jobsmaster',$data_arr);

                        $this->session->set_flashdata('s', "$doc_id Updated Successfully.");
                        return array('status'=>true,'last_id' => $job_id);

                    }else{

                        $this->db->insert('srp_erp_jobsmaster',$data_arr);

                        $this->session->set_flashdata('s', "$doc_id Created Successfully.");
                        return array('status'=>true,'last_id' => $this->db->insert_id());

                    }
                   
                    
                } catch (\Throwable $th) {
                    //throw $th;
                    $this->session->set_flashdata('e', "Something went wrong");
                    return array('status'=> false);
                }
                
               

            }

        }


    }

    function fetch_itemrecode_po(){

        $job_id = trim($this->input->get('job_id'));
        $query = trim($this->input->get('query'));

        $job_master_details = get_jobs_master_detail($job_id);
        $base_array = array();

        if($job_master_details){

            $contractID = $job_master_details['contract_po_id'];

            // $this->db->select('sc.*,um.UnitShortCode');
            // $this->db->where('sc.contractAutoID',$contractID);
            // $this->db->join('srp_erp_unit_of_measure as um', 'sc.defaultUOMID = um.UnitID','left');
            // $itemList = $this->db->from('srp_erp_contractdetails as sc')->get()->result_array();

            $itemList = $this->db->query("SELECT cd.*,um.UnitShortCode
                FROM srp_erp_contractdetails AS cd
                LEFT JOIN srp_erp_unit_of_measure AS um ON cd.defaultUOMID = um.UnitID
                WHERE cd.itemDescription LIKE '{$query}%' AND cd.contractAutoID = '$contractID'")->result_array();


            foreach($itemList as $item){

                $item_arr = array();

                $item_arr['value'] = $item['itemSystemCode'].' | '.$item['itemDescription'].' | '.$item['itemCategory'];
                $item_arr['uom'] = $item['UnitShortCode'];
                $item_arr['itemSystemCode'] = $item['itemSystemCode'];
                $item_arr['itemAutoID'] = $item['itemAutoID'];
                $item_arr['unittransactionAmount'] = $item['unittransactionAmount'];
                $item_arr['requestedQty'] = $item['requestedQty'];

                $base_array[] = $item_arr;

            }

        }
        return array('suggestions' => $base_array);

    }

    function save_item_detail(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $itemAutoID = $this->input->post('itemAutoID');
        $amount = $this->input->post('amount');
        $quantity = $this->input->post('quantity');
        $discount = $this->input->post('discount');
        $netAmount = $this->input->post('netAmount');
        $comment = $this->input->post('comment');
        $id = $this->input->post('id');

        foreach($itemAutoID as $key => $item){

            $item_details = get_job_item_detail($item);

            $item_exists = check_job_item_exists($item_details['itemAutoID'],$job_id);

            if($item_exists){
                $this->session->set_flashdata('e', $item_details['itemSystemCode']." added already.");
                continue;
            }

            $data_arr = array();

            if($item_details){

                // print_r($netAmount[$key]); exit;
                $data_arr['itemAutoID'] = $item;
                $data_arr['code'] = $item_details['itemSystemCode'];
                $data_arr['uomID'] = $item_details['defaultUnitOfMeasureID'];
                $data_arr['uomCode'] = $item_details['defaultUnitOfMeasure'];
                $data_arr['qty'] = $quantity[$key];
                $data_arr['value'] = $amount[$key];
                $data_arr['discount'] = $discount[$key];
                $data_arr['transactionAmount'] = number_format((float)$netAmount[$key],2);
                $data_arr['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $data_arr['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data_arr['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_arr['transactionCurrencyID']);;
                $data_arr['comment'] = $comment[$key];
                $data_arr['job_id'] = $job_id;
                $data_arr['itemDescription'] = $item_details['itemName'].' | '.$item_details['itemDescription'];
                $data_arr['status'] = 1;
                $data_arr['companyID'] =  $this->common_data['company_data']['company_id'];
                $data_arr['companyCode'] =  $this->common_data['company_data']['company_code'];
                

                try {
                    //code...
                    $res = $this->db->insert('srp_erp_job_itemdetail',$data_arr);

                    $this->session->set_flashdata('s', $item_details['itemSystemCode']." added Sucessfully.");


                } catch (\Throwable $th) {
                    
                    $this->session->set_flashdata('e', $item_details['itemSystemCode']." adding failed.");
                    return false;
                }


            }

        }

        return true;
    }

    function save_item_detail_edit(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $itemAutoID = $this->input->post('itemAutoID');
        $amount = $this->input->post('amount');
        $quantity = $this->input->post('quantity');
        $discount = $this->input->post('discount');
        $netAmount = $this->input->post('netAmount');
        $comment = $this->input->post('comment');
        $id = $this->input->post('id');

        $item_details = get_job_item_detail($itemAutoID);

        $item_exists = check_job_item_exists($item_details['itemAutoID'],$job_id);

        if($item_exists && ($id != $item_exists['id'])){
            $this->session->set_flashdata('e', $item_details['itemSystemCode']." added already.");
            return false;
        }

        $data_arr = array();

        if($item_details){

            // print_r($netAmount[$key]); exit;
            $data_arr['itemAutoID'] = $itemAutoID;
            $data_arr['code'] = $item_details['itemSystemCode'];
            $data_arr['uomID'] = $item_details['defaultUnitOfMeasureID'];
            $data_arr['uomCode'] = $item_details['defaultUnitOfMeasure'];
            $data_arr['qty'] = $quantity;
            $data_arr['value'] = $amount;
            $data_arr['discount'] = $discount;
            $data_arr['transactionAmount'] = number_format((float)$netAmount,2);
            $data_arr['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data_arr['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data_arr['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_arr['transactionCurrencyID']);;
            $data_arr['comment'] = $comment;
            $data_arr['job_id'] = $job_id;
            $data_arr['itemDescription'] = $item_details['itemName'].' | '.$item_details['itemDescription'];
            $data_arr['status'] = 1;
            $data_arr['companyID'] =  $this->common_data['company_data']['company_id'];
            $data_arr['companyCode'] =  $this->common_data['company_data']['company_code'];
            

            try {
                //code...
                $res = $this->db->where('id',$id)->update('srp_erp_job_itemdetail',$data_arr);

                $this->session->set_flashdata('s', $item_details['itemSystemCode']." updated Sucessfully.");


            } catch (\Throwable $th) {
                
                $this->session->set_flashdata('e', $item_details['itemSystemCode']." update failed.");
                return false;
            }


        }

    

        return true;
    }

    function add_assets_job(){

        $job_id = $this->input->post('job_id');
        $faID = $this->input->post('faID');
        $contract = $this->input->post('contract');

        $companyid = current_companyID();
        $data = array();
        $emp_already = 1;

        $fa_details = get_fa_assets_details_job($faID);

        if(empty($fa_details)){
            $this->session->set_flashdata('e', 'Something went wrong.');
            return true;
        }

        $getcontractassdetails = get_added_assets_contractdetails($faID,$contract);

        $emp_already_exists =  get_added_assets_details($faID,$job_id);

        if(empty($emp_already_exists)){

            $data['job_id'] = $job_id;
            $data['faID'] = $fa_details['faID'];
            $data['assetCode'] = $fa_details['faCode'];
            $data['assetName'] = $fa_details['assetDescription'];
            $data['assetRef'] =  $getcontractassdetails['assetRef'];
            $data['status'] = 1;
            $data['companyID'] =  $this->common_data['company_data']['company_id'];
            $data['companyCode'] =  $this->common_data['company_data']['company_code'];
    
            $this->db->insert('srp_erp_job_assetsdetail',$data);

            $this->session->set_flashdata('s', 'Asset added Successfully.');
            return true;

        } else{
        
            $this->session->set_flashdata('e', 'Asset is already added to this Job.');
            return true;
        }

    }

    function save_activity_detail(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $description = $this->input->post('description');
        $activity_type = $this->input->post('activity_type');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $isStandby = $this->input->post('isStandby');
        $isNpt = $this->input->post('isNpt');
        $toDate = $this->input->post('toDate');
        $shift_id = $this->input->post('shift_id');
        $Nptcommnt = $this->input->post('NPTcomment'); 
        $companyid = current_companyID();
        $base_array = array();


        foreach($description as $key => $des_value){

            $data_arr = array();

            $from_date = $fromDate[$key];
            $to_date = $toDate[$key];

            // $hourdiff = round((strtotime($to_date) - strtotime($from_date))/3600, 1);
            $hourdiff = getDayWiseDifference($from_date,$to_date,'hours_minute_only');

            $data_arr['job_id'] = $job_id;
            $data_arr['description'] = $des_value;
            $data_arr['type'] = $activity_type[$key];
            $data_arr['dateFrom'] = $fromDate[$key];
            $data_arr['dateTo'] = $toDate[$key];
            $data_arr['shift_id'] = $shift_id;
            $data_arr['hours'] = $hourdiff;
            $data_arr['isStandby'] = isset($isStandby[$key]) ? $isStandby[$key] : 0;
            $data_arr['isNpt'] = isset($isNpt[$key]) ? $isNpt[$key] : 0;
            $data_arr['companyID'] =  $this->common_data['company_data']['company_id'];
            $data_arr['companyCode'] =  $this->common_data['company_data']['company_code'];
            $data_arr['nptReason'] = $Nptcommnt;


            try {
                //code...
                $this->db->insert('srp_erp_job_activitydetail',$data_arr);
                $this->session->set_flashdata('s', 'Activity added Successfully.');

            } catch (\Throwable $th) {
                //throw $th;
                $this->session->set_flashdata('e', 'Activity adding failed.');
               return false;
           }
           

        }

        return true;



    }

    function save_activity_detail_edit(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $description = $this->input->post('edit_description');
        $activity_type = $this->input->post('edit_activity_type');
        $fromDate = $this->input->post('edit_fromDate');
        $toDate = $this->input->post('edit_toDate');
        $isStandby = $this->input->post('edit_isStandby');
        $isNpt = $this->input->post('edit_isNpt');
        $shift_id = $this->input->post('shift_id');
        $edit_acctivity_id = $this->input->post('edit_acctivity_id');
        
        $companyid = current_companyID();
        $base_array = array();

        $from_date = $fromDate;
        $to_date = $toDate;

        $hourdiff = getDayWiseDifference($from_date,$to_date,'hours_minute_only');

        $data_arr['job_id'] = $job_id;
        $data_arr['description'] = $description;
        $data_arr['type'] = $activity_type;
        $data_arr['dateFrom'] = $fromDate;
        $data_arr['dateTo'] = $toDate;
        $data_arr['shift_id'] = $shift_id;
        $data_arr['hours'] = $hourdiff;
        $data_arr['isStandby'] = isset($isStandby) ? $isStandby : 0;
        $data_arr['isNpt'] = isset($isNpt) ? $isNpt : 0;
        $data_arr['companyID'] =  $this->common_data['company_data']['company_id'];
        $data_arr['companyCode'] =  $this->common_data['company_data']['company_code'];


        try {
            //code...
            $this->db->where('id',$edit_acctivity_id)->update('srp_erp_job_activitydetail',$data_arr);
            $this->session->set_flashdata('s', 'Activity updaed Successfully.');
            return true;

        } catch (\Throwable $th) {
            //throw $th;
            $this->session->set_flashdata('e', 'Activity update failed.');
            return false;
        }

    }

    
    function save_fuel_detail(){
        $docnum = trim($this->input->post('docunumber') ?? '');
        $fudate = $this->input->post('startdate');
        $rueuser =$this->input->post('reuser');
        $rueuseremp =$this->input->post('linkemployee');
        $renaration = $this->input->post('rnarration');
        $uot =    $this->input->post('UOMid');
        $rqty =     $this->input->post('qtynum');
        $transactionid = $this->input->post('transid');
        $fueltype = $this->input->post('fuelusageID');
        $job_id = trim($this->input->post('job_id') ?? '');
        $companyID = current_companyID();

        if($job_id){

            $data_arr = array();
           
            
            $data_arr['DocumentID'] = $docnum;
            $data_arr['Documentdate'] = $fudate;
            if($rueuser){
                $data_arr['userName'] = $rueuser;
            }
            if($rueuseremp){
                $data_arr['userID'] = $rueuseremp;
            }
            $data_arr['Description'] = $renaration;
            $data_arr['uomID'] = $uot;
            $data_arr['Qty'] = $rqty;
            $data_arr['jobID'] = $job_id;
            $data_arr['companyID'] =  $companyID;
            $data_arr['transactiontypeID'] =  $transactionid;
            $data_arr['fuelid'] =  $fueltype;


            try {
                //code...
                $this->db->insert('srp_erp_job_fueldetails',$data_arr);
                $this->session->set_flashdata('s', 'Record Added Successfully.');

            } catch (\Throwable $th) {
                //throw $th;
                $this->session->set_flashdata('e', 'Adding failed.');
                return false;
           }
           

        }
        return true;

    }







    function save_pipe_tally_detail(){
        
        $running_number = trim($this->input->post('runningNumber') ?? '');
        $od_inches = $this->input->post('ODInches');
        $item_length = $this->input->post('itemLength');
        $cum_length = $this->input->post('cumLength');
        $landing_depth_bottom = $this->input->post('landingDepthBottom');

        $job_id = trim($this->input->post('job_id') ?? '');
        $companyID = current_companyID();

        if($job_id){

            $data_arr = array();
            
            $data_arr['job_id'] = $job_id;
            $data_arr['running_number'] = $running_number;
            $data_arr['od_inches'] = $od_inches;
            $data_arr['item_length'] = $item_length;
            $data_arr['cum_length'] = $cum_length;
            $data_arr['landing_depth_bottom'] = $landing_depth_bottom;
            $data_arr['companyID'] =  $companyID;

            try {
                //code...
                $this->db->insert('srp_erp_op_pipe_tally',$data_arr);
                $this->session->set_flashdata('s', 'Pipe Tally Record Added Successfully.');

            } catch (\Throwable $th) {
                //throw $th;
                $this->session->set_flashdata('e', 'Adding failed.');
               return false;
           }
           

        }
        return true;
    }

    function save_visitor_log_detail(){
        
        $full_name = trim($this->input->post('fullName') ?? '');
        $full_company = $this->input->post('fullCompany');
        $position = $this->input->post('position');
        $purpose_visit = $this->input->post('purposeVisit');
        $mobile_no = $this->input->post('mobileNumber');
        $medication = $this->input->post('medication');
        $h2s_validity = $this->input->post('H2SValidity');
        $safety_briefing = $this->input->post('rigSafetyBriefing');
        $proper_ppe = $this->input->post('properPPE');
        $time_in = $this->input->post('time_in');
        $time_out = $this->input->post('time_out');
        $date = $this->input->post('fromDate');
        
        $job_id = trim($this->input->post('job_id') ?? '');
        $companyID = current_companyID();

        if($job_id){

            $data_arr = array();
            
            $data_arr['job_id'] = $job_id;
            $data_arr['full_name'] = $full_name;
            $data_arr['full_company'] = $full_company;
            $data_arr['position'] = $position;
            $data_arr['purpose_visit'] = $purpose_visit;
            $data_arr['mobile_no'] = $mobile_no;
            $data_arr['medication'] = $medication;
            $data_arr['h2s_validity'] = $h2s_validity;
            $data_arr['safety_briefing'] = $safety_briefing;
            $data_arr['proper_ppe'] = $proper_ppe;
            $data_arr['time_in'] = $time_in;
            $data_arr['time_out'] = $time_out;
            $data_arr['date'] = $date;

            $data_arr['companyID'] =  $companyID;

            try {
                //code...
                $this->db->insert('srp_erp_op_visitors_log',$data_arr);
                $this->session->set_flashdata('s', 'Visitors Log Record Added Successfully.');

            } catch (\Throwable $th) {
                //throw $th;
                $this->session->set_flashdata('e', 'Adding failed.');
               return false;
           }
           

        }
        return true;
    }

    function get_total_hours(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $shift_id = trim($this->input->post('shift_id') ?? '');

        $activities = get_job_activity_details($job_id,$shift_id);
        $total_hours_days = 0;
        $total_hours_hours = 0;
        $total_hours_minutes = 0;
        $standby_hours_days = 0;
        $standby_hours_hours = 0;
        $standby_minutes = 0;
        $npt_hours_days = 0;
        $npt_hours_hours = 0;
        $npt_minutes = 0;

        $total_hours_new=0;
        $total_minutes=0;

        foreach($activities as $activity){

            $activity_from_date = $activity['dateFrom'];
            $activity_to_date = $activity['dateTo'];
            $standby = $activity['isStandby'];
            $npt = $activity['isNpt'];

            $get_difference = getDayWiseDifference($activity_from_date,$activity_to_date,"hours_minute_num");


            $days_hours_arr = explode('.',$get_difference);
            // $total_hours_hours += $days_hours_arr[0];
            // $total_hours_minutes += $days_hours_arr[1];
            $total_hours_new= $total_hours_new+$days_hours_arr[0];
            $total_minutes= $total_minutes+$days_hours_arr[1];

            if($standby == 1){
                $standby_hours_hours += $days_hours_arr[0];
                $standby_minutes += $days_hours_arr[1];
            }

            if($npt == 1){
                $npt_hours_hours += $days_hours_arr[0];
                $npt_minutes += $days_hours_arr[1];
            }

        }
        $minutes = ($total_hours_new*60)+$total_minutes;

        $output_hours = floor($minutes / 60); 
        $output_minutes = $minutes - ($output_hours * 60);
        
        // if($total_hours_hours > 24){
        //     $temp_total_hours_remain = $total_hours_hours % 24;
        //     $temp_total_days = ($total_hours_hours-$temp_total_hours_remain) / 24;
            
        //     $total_hours_days += $temp_total_days;
        //     $total_hours_hours = $temp_total_hours_remain;
        // }

        // if($standby_hours_hours > 24){
        //     $temp_total_hours_remain = $standby_hours_hours % 24;
        //     $temp_total_days = ($standby_hours_hours-$temp_total_hours_remain) / 24;
            
        //     $standby_hours_days += $temp_total_days;
        //     $standby_hours_hours = $temp_total_hours_remain;
        // }

        // if($npt_hours_hours > 24){
        //     $temp_total_hours_remain = $npt_hours_hours % 24;
        //     $temp_total_days = ($npt_hours_hours-$temp_total_hours_remain) / 24;
            
        //     $npt_hours_days += $temp_total_days;
        //     $npt_hours_hours = $temp_total_hours_remain;
        // }

        $base_arr = array('total_days'=>$total_hours_days,'total_hours'=>$output_hours,'total_minutes'=>$output_minutes,'standby_days'=>$standby_hours_days,'standby_hours'=>$standby_hours_hours,
        'standby_minutes'=>$standby_minutes,'npt_days'=>$npt_hours_days,'npt_hours'=>$npt_hours_hours,'npt_minutes'=>$npt_minutes);

        return $base_arr;
    }

    function save_fields_well(){

        $filed_well_id = trim($this->input->post('filed_well_id') ?? '');
        $filed_well_type = trim($this->input->post('filed_well_type') ?? '');
        $filed_well_name = trim($this->input->post('filed_well_name') ?? '');
        $action = trim($this->input->post('action') ?? '');
        $well_type_op = trim($this->input->post('well_type_op') ?? '');
        $well_no_op = trim($this->input->post('well_no_op') ?? '');
        $data = array();
        try {
            if($filed_well_type == 2){
                // well
                if($action == 'add'){

                    $data['well_name'] = $filed_well_name;
                    $data['mapping_field_well_id'] = $filed_well_id;
                    $data['well_type_op'] = $well_type_op;
                    $data['well_no_op'] = $well_no_op;
                    $data['type'] = $filed_well_type;
                    $data['levelNo'] = $filed_well_type;
                    $data['companyID'] =  $this->common_data['company_data']['company_id'];
                    $data['companyCode'] =  $this->common_data['company_data']['company_code'];
                    
                    $this->db->insert('srp_erp_jobs_field_rig_masters',$data);
        
                    $this->session->set_flashdata('s', 'Well added Successfully.');
                    return true;

                }elseif($action == 'edit'){

                    $data['well_name'] = $filed_well_name;
                    $data['well_type_op'] = $well_type_op;
                    $data['well_no_op'] = $well_no_op;
                    
                    $this->db->where('id',$filed_well_id)->update('srp_erp_jobs_field_rig_masters',$data);
        
                    $this->session->set_flashdata('s', 'Well Updated Successfully.');
                    return true;
                }
              
    
            }elseif($filed_well_type == 2){

                if($action == 'edit'){

                    $data['field_name'] = $filed_well_name;
                    
                    $this->db->where('id',$filed_well_id)->update('srp_erp_jobs_field_rig_masters',$data);
        
                    $this->session->set_flashdata('s', 'Field Updated Successfully.');
                    return true;
                }

            }elseif($filed_well_type == 3){

                if($action == 'edit'){

                    $data['rig_hoist_name'] = $filed_well_name;
                    
                    $this->db->where('id',$filed_well_id)->update('srp_erp_jobs_field_rig_masters',$data);
        
                    $this->session->set_flashdata('s', 'Field Updated Successfully.');
                    return true;
                }

            }
        } catch (\Throwable $th) {
            $this->session->set_flashdata('e', 'Well adding failed.');
            return false;
        }
        
    }

    function save_jobs_master_field(){
        
        $master_field_name = trim($this->input->post('master_field_name') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $data = array();

        try {
            if($master_field_name){

                $data['companyID'] =  $this->common_data['company_data']['company_id'];
                $data['companyCode'] =  $this->common_data['company_data']['company_code'];
               
                $data['mapping_field_well_id'] = 0;

                if($type == 3){
                    $data['type'] = 3;
                    $data['levelNo'] = 1;
                    $data['rig_hoist_name'] = $master_field_name;
                }elseif($type == 4){
                    $data['type'] = 4;
                    $data['levelNo'] = 1;
                    $data['rig_hoist_name'] = $master_field_name;
                }else{
                    $data['type'] = 1;
                    $data['levelNo'] = 1;
                    $data['field_name'] = $master_field_name;
                }
                
                $this->db->insert('srp_erp_jobs_field_rig_masters',$data);
                
            }

            $this->session->set_flashdata('s', 'Field added Successfully.');
            return true;

        } catch (\Throwable $th) {
            $this->session->set_flashdata('e', 'Activity adding failed.');
            return false;
        }
        
    }

    function delete_field_well(){
        $id = trim($this->input->post('id') ?? '');

        if($id){

            $this->db->where('id',$id)->delete('srp_erp_jobs_field_rig_masters');

            $this->session->set_flashdata('s', 'Delete Successfully');
            return true;

        }
    }

    function remove_added_record(){

        $id = trim($this->input->post('id') ?? '');
        $table = trim($this->input->post('table') ?? '');

        try {

            if($table == 'billing'){

                $table = 'srp_erp_job_billing';
                $records_ex = get_billing_detail_record($id);

                if(count($records_ex) > 0){
                    $this->session->set_flashdata('e', 'Can not delete, remove detail first.');
                    return true;
                } else{
                    $this->db->where('id',$id)->delete("$table");
                }

            } elseif($table == 'srp_erp_job_activitydetail'){

                $table = 'srp_erp_job_activitydetail';
                $records = check_daily_report_record($id);

                if(count($records) > 0){
                    $this->session->set_flashdata('e', 'This activity is included in the daily report and cannot be deleted.');
                    return true;
                } else{
                    $this->db->where('id',$id)->delete("$table");
                }

            } elseif($table == 'srp_erp_job_activityshift'){ //activity deleted check

                $table = 'srp_erp_job_activityshift';
                $records = check_activity_report_records($id);

                if(count($records) > 0){
                    $this->session->set_flashdata('e', 'Activities related to this shift are included in the daily report and cannot be deleted.');
                    return true;
                } else{
                    $this->db->where('id',$id)->delete("$table");
                }

            } elseif($table == 'srp_erp_job_shift_crewdetail'){ //activity deleted check

                $table = 'srp_erp_job_shift_crewdetail';

                if($id){
                    $this->db->where('id',$id)->delete("$table");   
                }

            } else{
                if($id && $table){  
                    $this->db->where('id',$id)->delete("$table");
                }
            }            

            $this->session->set_flashdata('s', 'Delete Successfully.');
            return true;

        } catch (\Throwable $th) {
            $this->session->set_flashdata('e', ' Delete Failed.');
            return false;
        }
        

    }

    function load_job_item_detail(){
        $id = trim($this->input->post('id') ?? '');

        if($id){
            $item_detail = get_job_item_record($id);
            return $item_detail;
        }else{
            $this->session->set_flashdata('e', 'Something went wrong.');
            return false;
        }
    }

    function job_confirmation(){
        $job_id = trim($this->input->post('job_id') ?? '');

        $data = array();

        $job_detail = get_jobs_master_detail($job_id);

        if($job_detail && $job_detail['confirmed'] == 1){
            $this->session->set_flashdata('w', 'Document is already Confirmed.');
            return false;
        }

        $data['confirmed'] = 1;

        $this->db->where('id',$job_id)->update('srp_erp_jobsmaster',$data);

        $this->session->set_flashdata('s', 'Confirmed Successfully.');
        return true;

    }

    function save_jobs_crew_check_status(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $name = trim($this->input->post('name') ?? '');
        $value = trim($this->input->post('value') ?? '');
        $companyid = current_companyID();

        try {

            if($name == 'competency'){
                $data['competency_check'] = $value;
            }elseif($name == 'training'){
                $data['training_check'] = $value;
            }elseif($name == 'ssc'){
                $data['ssc_check'] = $value;
            }elseif($name == 'maintenance'){
                $data['maintenance_check'] = $value;
            }
            
            if($name == 'maintenance'){
                $this->db->where('id',$job_id)->where('companyID',$companyid)->update('srp_erp_job_assetsdetail',$data);
            }else{
                $this->db->where('id',$job_id)->where('companyID',$companyid)->update('srp_erp_job_crewdetail',$data);
            }
    
            $this->session->set_flashdata('s', 'Updated Successfully.');
            return true;

        } catch (\Throwable $th) {
            $this->session->set_flashdata('e', 'Something went wrong.');
            return true;
        }
      

    }

    function update_daily_report_values(){

        $value = trim($this->input->post('value') ?? '');
        $job_id = trim($this->input->post('job_id') ?? '');
        $report_id = trim($this->input->post('report_id') ?? '');
        $field_name = trim($this->input->post('field_name') ?? '');
        $detail_id = trim($this->input->post('detail_id') ?? '');
        $data = array();
        $table = null;
        $field_id = 'id';


           
        if($field_name == 'supervisor_text'){
            $table = 'srp_erp_job_dailyreport_detail';
            $report_id = $detail_id;
       
        }else{
            $table = 'srp_erp_job_dailyreport';
        }

        //check document confirmed
        $report_record = get_daily_report_master($report_id);

        if($report_record && $report_record['confirmedYN'] == 1){
            $this->session->set_flashdata('e', 'Document already confirmed.');
            return true;
        }


        if($table){

            $data[$field_name] = $value;

            if($field_name == 'confirmedYN'){
                $data['confirmed_by'] = $this->common_data['current_user'];
                $data['confirmed_date'] = $this->common_data['current_date'];
            }

            $this->db->where($field_id,$report_id)->update($table,$data);
        
            $this->session->set_flashdata('s', 'Updated Successfully.');
            return TRUE;

        }else{
            $this->session->set_flashdata('e', 'Something went wrong.');
            return true;
        }
        
    }

    function save_shift_activity_detail(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $shift_start = $this->input->post('shiftFromDate');
        $shift_end = $this->input->post('shiftToDate');
        $note = $this->input->post('shift_notes');
        $edit_shift_id = $this->input->post('edit_shift');
        $data = array();

        $check_lists = array();
        $checklist_str = '';

        $job_master = get_jobs_master_detail($job_id);
        
        if($job_master){
            $contract_id = $job_master['contract_po_id'];

            $contract_checklist = get_checklist_added_to_contract($contract_id,'shiftChangeType');

            foreach($contract_checklist as $checklists){
            
                $header_id = create_checklist_header_record($checklists['checklistID'],$contract_id,$job_id);
               
                $check_lists[] = $header_id;
            
            }
            
            $checklist_str = join(',',$check_lists);

        }

        $data['job_id'] = $job_id;
        $data['dateFrom'] = $shift_start;
        $data['dateTo'] = $shift_end;
        $data['description'] = $note;
        $data['type'] = 'Shift';
        $data['check_list'] = $checklist_str;
        $data['companyID'] =  $this->common_data['company_data']['company_id'];
        $data['companyCode'] =  $this->common_data['company_data']['company_code'];

        if($edit_shift_id){
            $this->db->where('id',$edit_shift_id)->update('srp_erp_job_activityshift',$data);
            $this->session->set_flashdata('s', 'Updated Successfully.');
        }else{
            $this->db->insert('srp_erp_job_activityshift',$data);
            $this->session->set_flashdata('s', 'Shift added Successfully.');
        }

        return true;
      

    }

    function save_daily_report_detail(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $report_start = $this->input->post('reportFromDate');
        $report_end = $this->input->post('reportToDate');
        $description = $this->input->post('description');
        $daily_report_id = $this->input->post('daily_report_id');
        $data = array();

        $data['job_id'] = $job_id;
        $data['dateFrom'] = $report_start;
        $data['dateTo'] = $report_end;
        $data['description'] = $description;
        $data['type'] = 'Report';
        $data['companyID'] =  $this->common_data['company_data']['company_id'];
        $data['companyCode'] =  $this->common_data['company_data']['company_code'];

        if($daily_report_id){
            $updated = $this->db->where('id',$daily_report_id)->update('srp_erp_job_dailyreport',$data);
            $this->session->set_flashdata('s', 'Updated Successfully.');
        }else{
            $updated = $this->db->insert('srp_erp_job_dailyreport',$data);

            //update report header
            $last_added = $this->db->insert_id();

            //create daily report 
            $report = create_daily_report_header($last_added,$job_id);

            $this->session->set_flashdata('s', 'Shift added Successfully.');
        }

        return TRUE;

    }

    function save_checklist_response(){

        $header_id = $this->input->post('header_id');
        $checklist_id = $this->input->post('checklist_id');
        $confirmYN = $this->input->post('confirmYN');
        $date_format_policy = date_format_policy();

        //check record already exists and confirmed
        $ex_header_record = get_checklist_header_record($header_id);

        if($ex_header_record && $ex_header_record['is_confirmed'] == 1){
            $this->session->set_flashdata('e', 'Document already Confirmed.');
            return true;
        }

        //get question list
        $question_list = get_checklist_questions_details($checklist_id);

        foreach($question_list as $question){

            $id = $question['id'];
            $radio_name = 'radio_'.$id;
            $comment_name = 'comment_'.$id;

            $data = array();

            //check exists
            $ex_record = get_checklist_questions_response($id,$header_id);

            $data['detail_id'] = $id;
            $data['header_id'] = $header_id;
            $data['serial'] = $id;
            $data['description'] = $question['qtn_name'];
            $data['status'] = isset($_POST["$radio_name"]) ? $_POST["$radio_name"] : '3';
            $data['comments'] = isset($_POST["$comment_name"]) ? $_POST["$comment_name"] : '';
            $data['companyID'] =  $this->common_data['company_data']['company_id'];


            if($ex_record){
                $res = $this->db->where('detail_id',$id)->where('header_id',$header_id)->update('srp_erp_op_checklist_details',$data);
            }else{
                $res = $this->db->insert('srp_erp_op_checklist_details',$data);
            }

        }

        // Save header details
        $data_header = array();

        $data_header['driller_name'] = $this->input->post('driller_name');
        $data_header['driller_signature'] = $this->input->post('driller_signature');
        $data_header['rig_manager_name'] = $this->input->post('rig_manager_name');
        $data_header['rig_manager_signature'] = $this->input->post('rig_manager_signature');
        

        $data_header['inspection_done_by'] = $this->input->post('inspection_done_by') ;
        $data_header['inspection_position'] =  $this->input->post('inspection_position') ;
        $data_header['inspection_signature'] = $this->input->post('inspection_signature') ;
        $data_header['report_review_by'] =  $this->input->post('report_review_by') ;
        $data_header['report_review_position'] =  $this->input->post('report_review_position') ;
        $data_header['report_review_signature'] =  $this->input->post('report_review_signature');

        $data_header['additional_info_yn'] =  $this->input->post('additional_info_yn');
        $data_header['additional_info'] =  $this->input->post('additional_info');
        $data_header['completing_name'] =  $this->input->post('completing_name');
        $data_header['completing_signature'] =  $this->input->post('completing_signature');
        $data_header['completing_position'] =  $this->input->post('completing_position');
        $data_header['completing_datetime'] =  $this->input->post('completing_datetime');

        $data_header['checklist_comment'] =  $this->input->post('ChecklistComment');

        $date_inspection=$this->input->post('date_inspection');

        if($date_inspection){
            $date_inspection_format = input_format_date($date_inspection, $date_format_policy);

            $data_header['date_inspection'] = $date_inspection_format;
        }

        $data_header['time_inspection'] =  $this->input->post('time_inspection');
        $data_header['comment'] =  $this->input->post('comment_crane');
        //$data_arr['job_date_from'] = date('Y-m-d H:i:s',strtotime($fromDate));

        $data_header['current_well_num'] =  $this->input->post('current_well_num');
        $date_of_move =  $this->input->post('date_of_move');

        if($date_of_move){
            $date_inspection_format1 = input_format_date($date_of_move, $date_format_policy);

            $data_header['date_of_move'] = $date_inspection_format1;
        }

        $data_header['time_of_move'] =  $this->input->post('time_of_move');
        $data_header['distance'] =  $this->input->post('distance');

        $crane_date =  $this->input->post('crane_date');

        if($crane_date){
            $date_inspection_format2 = input_format_date($crane_date, $date_format_policy);

            $data_header['crane_date'] = $date_inspection_format2;
        }
        
        $data_header['crane_reg'] =  $this->input->post('crane_reg');
        $data_header['crane_operator'] =  $this->input->post('crane_operator');

        $forklift_reg_expire_date =  $this->input->post('forklift_reg_expire_date');

        if($forklift_reg_expire_date){
            $date_inspection_format3 = input_format_date($forklift_reg_expire_date, $date_format_policy);

            $data_header['forklift_reg_expire_date'] = $date_inspection_format3;
        }

        if($this->input->post('forklift_check_datetime')){
            $data_header['forklift_check_datetime'] =  date('Y-m-d H:i:s',strtotime($this->input->post('forklift_check_datetime')));
        }
      
        $data_header['forklift_reg'] =  $this->input->post('forklift_reg');

        if($confirmYN == 1){
            $data_header['is_confirmed'] = $this->input->post('confirmYN');
            $data_header['confirmed_by'] = $this->common_data['current_userID'];
            $data_header['confirmed_by_name'] = $this->common_data['current_user'];
            $data_header['date_confirmed'] = $this->common_data['current_date'];
        }
        
        $res = $this->db->where('id',$header_id)->update('srp_erp_op_checklist_header',$data_header);


        $this->session->set_flashdata('s', 'Added Successfully.');
        return true;

    }

    function save_billing_header(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $report_start = $this->input->post('reportFromDate');
        $report_end = $this->input->post('reportToDate');
        $description = $this->input->post('description');
        $billingcode = $this->input->post('code');
        $final_billing_id = $this->input->post('final_billing_id');
        $data = array();

        $data['job_id'] = $job_id;
        $data['dateFrom'] = $report_start;
        $data['dateTo'] = $report_end;
        $data['description'] = $description;
        $data['code'] = $billingcode;
        $data['type'] = 'Billing';
        $data['companyID'] =  $this->common_data['company_data']['company_id'];
        $data['companyCode'] =  $this->common_data['company_data']['company_code'];

        if($final_billing_id){
            $this->db->where('id',$final_billing_id)->update('srp_erp_job_billing',$data);
            $this->session->set_flashdata('s', 'Updated Successfully.');
        }else{
            $this->db->insert('srp_erp_job_billing',$data);
            $this->session->set_flashdata('s', 'Billing added Successfully.');
        }

        return true;

    }

    function get_activity_details(){

        $activity_id = $this->input->post('activity_id');
        $job_id = trim($this->input->post('job_id') ?? '');

        return get_job_activity_for_id($job_id,$activity_id);

    }

    function get_price_details(){

        $price_id = $this->input->post('price_id');

        return get_added_price_details($price_id); 

    }
    
    function save_billing_detail_item(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $billing_description  = $this->input->post('billing_description[]: ');
        $billing_activity = $this->input->post('billing_activity');
        $billing_fromDate = $this->input->post('billing_fromDate');
        $billing_toDate = $this->input->post('billing_toDate');
        $billing_price = $this->input->post('billing_price');
        $billing_qty = $this->input->post('billing_qty');
        $billing_rate = $this->input->post('billing_rate');
        $billing_rate_total = $this->input->post('billing_rate_total');
        $billing_header_id = $this->input->post('billing_header_id');
        $activity_id = $this->input->post('activity_id');
        
        foreach($activity_id as $key => $activity){

            $data = array();

            $job_activity = get_job_activity_for_id($job_id,$activity);
            $price_detail = get_added_price_details($billing_price[$key]);

            $data['job_id'] = $job_id;
            $data['description'] = $job_activity['description'];
            $data['dateFrom'] = $job_activity['dateFrom'];
            $data['dateTo'] = $job_activity['dateTo'];
            $data['companyID'] =  $this->common_data['company_data']['company_id'];
            $data['companyCode'] =  $this->common_data['company_data']['company_code'];
            $data['qty'] = $billing_qty[$key];
            $data['billing_header'] = $billing_header_id;
            $data['unit_amount'] = $billing_rate[$key];
            $data['total_amount'] = $billing_rate_total[$key];
            $data['isStandby'] = $job_activity['isStandby'];
            $data['isNpt'] = $job_activity['isNpt'];
            $data['price_id'] = $price_detail['contractDetailsAutoID'];
            $data['price_text'] = $price_detail['typeItemName'];
            $data['activity_id'] = $activity;

            $this->db->insert('srp_erp_job_billing_detail',$data);
            $this->session->set_flashdata('s', 'Billing added Successfully.');

        }

        return True;

    }

    function assign_contact_item_for_job_billing(){

        $job_id = trim($this->input->post('job_id') ?? '');
       
        $billing_header_id = $this->input->post('billing_header_id');
        $contactDetailID = $this->input->post('contactDetailID');

        $this->db->select('*');
        $this->db->where('id',$billing_header_id);
        $this->db->from('srp_erp_job_billing');
        $result_bill = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('contractDetailsAutoID',$contactDetailID);
        $this->db->from('srp_erp_contractdetails');
        $result_contract_details = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('job_id',$job_id);
        $this->db->where('price_id',$contactDetailID);
        $this->db->where('billing_header',$billing_header_id);
        $this->db->where('companyID',$this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_job_billing_detail');
        $result_exist = $this->db->get()->row_array();

        if($result_exist){
            return array('w', 'Item Already Added!');
        }else{
            //foreach($activity_id as $key => $activity){

                $data = array();

                $data['job_id'] = $job_id;
                $data['description'] = $result_contract_details['itemReferenceNo'];
                $data['dateFrom'] = $result_bill['dateFrom'];
                $data['dateTo'] = $result_bill['dateTo'];
                $data['companyID'] =  $this->common_data['company_data']['company_id'];
                $data['companyCode'] =  $this->common_data['company_data']['company_code'];
                $data['qty'] = $result_contract_details['requestedQty'];
                $data['billing_header'] = $billing_header_id;
                $data['unit_amount'] = $result_contract_details['unittransactionAmount'];
                $data['total_amount'] = $result_contract_details['transactionAmount'];
                
                $data['price_id'] = $contactDetailID;
                //$data['price_text'] = $price_detail['typeItemName'];
                //$data['activity_id'] = $activity;

                $this->db->insert('srp_erp_job_billing_detail',$data);
                $this->session->set_flashdata('s', 'Billing added Successfully.');

            //}
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item Assigned Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item Assigned Successfully');
        }

    }

    function save_standard_billing_item_qty(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $bill_id = $this->input->post('bill_id');

        $this->db->select('*');
        $this->db->where('job_id',$job_id);
        $this->db->where('id',$bill_id);
        $this->db->where('companyID',$this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_job_billing_detail');
        $result_exist = $this->db->get()->row_array();

        $data['qty'] = $this->input->post('qty');
        $data['total_amount'] =$result_exist['unit_amount']*$data['qty'];
        
        $this->db->where('job_id',$job_id);
        $this->db->where('id',$bill_id);
        $this->db->where('companyID',$this->common_data['company_data']['company_id']);
        $this->db->update('srp_erp_job_billing_detail',$data);
        //$this->session->set_flashdata('s', 'Billing update Successfully.');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Item update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Item update Successfully');
        }

    }

    function save_standard_billing_date(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $bill_id = $this->input->post('bill_id');
        $type = $this->input->post('type');
        $date = $this->input->post('date');

        $this->db->select('*');
        $this->db->where('job_id',$job_id);
        $this->db->where('id',$bill_id);
        $this->db->where('companyID',$this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_job_billing_detail');
        $result_exist = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('id',$result_exist['billing_header']);
        $this->db->where('companyID',$this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_job_billing');
        $result_exist_header = $this->db->get()->row_array();

        $dateFrom=strtotime($result_exist_header['dateFrom']);
        $dateTo=strtotime($result_exist_header['dateTo']);
        $mydate=strtotime($date);

       if($type==1){

            if($mydate >= $dateFrom && $dateTo>=$mydate)
            {
                $data['dateFrom'] = $date;
            }else{
                return array('e', 'Please add date between Billing master fromDate and toDate');
                exit;
            }
        
       }else{

            if($mydate >= $dateFrom && $dateTo>=$mydate)
            {
                $data['dateTo'] = $date;
            }else{
                return array('e', 'Please add date between Billing master fromDate and toDate');
                exit;
            }
       }
        
        $this->db->where('job_id',$job_id);
        $this->db->where('id',$bill_id);
        $this->db->where('companyID',$this->common_data['company_data']['company_id']);
        $this->db->update('srp_erp_job_billing_detail',$data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', ' update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', ' update Successfully');
        }

    }

    function assignCheckListForContract_job()
    {
        $assignCheckListSync = $this->input->post('assignCheckListSync');
        $job_id=$this->input->post('job_id');

        if (!empty($assignCheckListSync)) {
            foreach ($assignCheckListSync as $key => $assignCheckList) {

                $this->db->select('*');
                $this->db->from('srp_erp_op_checklist_master');
                $this->db->where('id', $assignCheckList);
                $check_master = $this->db->get()->row_array();

                $data['master_id'] = $assignCheckList;
                $data['is_deleted'] = 1;
                $data['doc_code'] = $check_master['document_reference_code'];
                $data['doc_name'] = $check_master['name'];
                $data['companyID'] = current_companyID();
                $data['job_id'] = $job_id;
                // $data['createdPCID'] = $this->common_data['current_pc'];
                // $data['createdUserID'] = $this->common_data['current_userID'];
                // $data['createdUserName'] = $this->common_data['current_user'];
                // $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_op_checklist_header', $data);
            }

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'CheckList Assigned Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'CheckList Assigned Successfully');
        }
    }

    function delete_job_manual_checklist(){

        $id = $this->input->post('id');

        try {
            $this->db->where('id',$id)->delete('srp_erp_op_checklist_header');
            $this->session->set_flashdata('s',  'Successfully deleted the records');
            return true;
        } catch (\Throwable $th) {
            $this->session->set_flashdata('e',  'Something went wrong');
            return false;
        }
    }

    function save_activity_crew_detail(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $shift_id = trim($this->input->post('shift_id') ?? '');
        $crew_tbl_id = $this->input->post('crew_id');
        $companyid = current_companyID();
        $data = array();
        $emp_already = 0;
        $date = date('Y-m-d H:i:s');

        try {
            
            //foreach($crew_tbl_id as $value){

                $job_crew_record = get_job_crew_record_id($crew_tbl_id);
    
                $empID = $job_crew_record['empID'];
                $designation = $job_crew_record['designation'];
                
                $emp_details = get_employee_details_job($empID);

                $ex_details = get_activity_crew_record($empID,$shift_id,$job_id);
    
                if($ex_details){
                    $this->session->set_flashdata('w', $ex_details['name'].' already added to the shift.');
                    $emp_already = 1;
                    return false;
                }
    
                $data['job_id'] = $job_id;
                $data['empID'] = $empID;
                $data['name'] = $emp_details['Ename1'].' '.$emp_details['Ename2'];
                $data['designation'] = $designation;
                $data['dateFrom'] = date('Y-m-d H:i:s',strtotime($date));
                $data['dateTo'] = date('Y-m-d H:i:s',strtotime('+1 minute',strtotime($date)));
                $data['status'] = 1;
                $data['is_job_completed'] = 0;
                $data['companyID'] = $companyid;
                $data['shift_id'] = $shift_id;
                $data['empCode'] = $emp_details['ECode'];
        
                $res = $this->db->insert('srp_erp_job_shift_crewdetail',$data);
    
            //}
    
            if($emp_already == 1){
                $this->session->set_flashdata('s', 'already added to the shift');
                return true;
            }else{
                $this->session->set_flashdata('s', 'Employee added Successfully.');
                return true;
            }

        } catch (\Throwable $th) {
            $this->session->set_flashdata('e', 'Something went wrong.');
            return true;
        }

        
    }

    function get_group_list(){

        $job_id = trim($this->input->post('job_id') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $contract_id = trim($this->input->post('contract_id') ?? '');

        $this->db->select('*');
        // $this->db->from('srp_erp_jobsmaster as sj');
        // $this->db->join('srp_erp_contractmaster as sc','sj.contract_po_id = sc.contractAutoID','left');
        $this->db->from('srp_erp_op_module_group_to as group');
        $this->db->where('group.companyID', $this->common_data['company_data']['company_id']);
        if($type == 1 || $type == 2){
            $this->db->where('group.contractAutoID', $contract_id);
        }else if($type == 3){
            $this->db->where('group.job_id', $job_id);
        }
        $this->db->where('group.groupType', $type);

        $results = $this->db->get()->result_array();

        return $results;

    }

    function save_job_checklist_users_detail($jobID,$checklistID){


        try {

            $this->db->select('*');
            $this->db->from('srp_erp_op_module_job_checklist_users');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('jobAutoID', $jobID);
            $this->db->where('checklistAutoID', $checklistID);
            $job_checklist_user = $this->db->get()->row_array();

            if($job_checklist_user){
                //
            }else{
                $job_detail = get_jobs_master_detail($jobID);

                $this->db->select('*');
                $this->db->from('srp_erp_op_checklist_master');
                $this->db->where('id', $checklistID);
                $check_master = $this->db->get()->row_array();
    
                $data_arr['jobAutoID'] = $jobID;
                $data_arr['contractAutoID'] = $job_detail['contract_po_id'];
                $data_arr['checklistAutoID'] = $checklistID;
                $data_arr['companyID'] =$this->common_data['company_data']['company_id'];
                $data_arr['documentCode'] = $check_master['document_reference_code'];
                $data_arr['documentName'] = $check_master['name'];
                $data_arr['createdUserID']  = $this->common_data['current_userID'];
                $data_arr['createdUserName'] = $this->common_data['current_user'];
                $data_arr['createdDateTime'] = $this->common_data['current_date'];
    
           
                //code...
                $res = $this->db->insert('srp_erp_op_module_job_checklist_users',$data_arr);
            }

        } catch (\Throwable $th) {
            
            return false;
        }

       
    }

    function selectChecklistUserUpdate()
    {

        $users = $this->input->post('users');
        $masterID = trim($this->input->post('masterID') ?? '');
        $type = trim($this->input->post('type') ?? '');

        if($users){
            $arraydata1 = implode(",", $users);

            if($type==1){
                $upData = array(
                    'confirmUsers' => $arraydata1,
                );
            }else{
                $upData = array(
                    'editUsers' => $arraydata1,
                );
            }
            

            $this->db->where('jobChecklistUserAutoID', $masterID)->update('srp_erp_op_module_job_checklist_users', $upData);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error');
        } else {
            $this->db->trans_commit();
            return array('s', 'Checklist Updated Successfully.');
        }
    }

    function load_activity_detail(){
        $activity_id = $this->input->post('activity_id');
        $job_id = $this->input->post('job_id');

        return get_job_activity_for_id($job_id,$activity_id);
    }


    function confirm_billing(){

        $billing_id = $this->input->post('billing_id');
        $job_id = $this->input->post('job_id');
        $confirmedYN = $this->input->post('confirmedYN');

        $data = array();
        $data['confirmedYN'] =  $confirmedYN;
        $data['confirmedDate'] =  $this->common_data['current_date'];
        $data['confirmedBy']  = $this->common_data['current_userID'].' - '.$this->common_data['current_user'];

        //Create MAterial Issue 
        // $material_issue = $this->set_material_issue($job_id, $billing_id);


        $issue_message = '';
        if(isset($material_issue['status']) && in_array($material_issue['status'],['error','success'])){
            $issue_message = $material_issue['message'];
        }

        try {
            $this->db->where('id',$billing_id)->where('companyID',$this->common_data['company_data']['company_id'])->update('srp_erp_job_billing',$data);
            $this->session->set_flashdata('s', "Billing confirmed Successfully. <br> {$issue_message}");
            return true;
        } catch (\Throwable $th) {
            $this->session->set_flashdata('e', 'Something went wrong.');
            throw $th;
        }

    }

    function set_material_issue($job_id, $billing_id){
    
        $billing_master = get_billing_master_record($billing_id);

        $billing_detail = get_billing_detail_record($billing_id);

        $job_contract_details = get_jobs_master_with_contract_details($job_id);

        $date_format_policy = date_format_policy();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        $data_item_arr = array();
        foreach($billing_detail as $billing_item){
            if($billing_item['itemAutoID']){
                $data_item_arr[] = $billing_item;
            }
        }

        if(count($data_item_arr) == 0){
            return array('status' => 'error','message' => 'No inventory item been used');
        }else{

            $this->load->model('Inventory_modal');

            //Create material issue header 
            $issueDate =  $job_contract_details['doc_date'];
            $financePeriodDetails = get_financial_period_date_wise($issueDate);
            $financeYearDetails = get_financial_year($issueDate);

            $_POST['issueType'] = 'Direct Issue';
            $_POST['segment'] = $job_contract_details['segmentID'].'|'.$job_contract_details['segmentCode'];
            $_POST['issueRefNo'] = '';
            $_POST['employeeName'] = $this->common_data['current_userCode'].'|'.$this->common_data['current_user'];
            $_POST['issueDate'] = date('Y-m-d',strtotime($issueDate));
            $_POST['location'] = 69;
            $_POST['financeyear'] = $financePeriodDetails['companyFinanceYearID'];
            $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            $_POST['companyFinanceYear'] = $financeYearDetails['beginingDate'].' - '.$financeYearDetails['endingDate'];
            $_POST['itemType'] = 'Inventory';
            $_POST['narration'] = '';
            $_POST['location_dec'] = 'KAN | kandy | Kandy';
            $_POST['isSystemGenerated'] = 1;

            $material_master = $this->Inventory_modal->save_material_issue_header();

            if($material_master){
                $last_id = $material_master['last_id'];

                $search = array();
                $itemAutoID_arr = array();
                $UnitOfMeasureID = array();
                $conversionRate = array();
                $currentWareHouseStockQty = array();
                $quantityRequested = array();
                $comment = array();
                $uom = array();

                foreach($data_item_arr as $items){

                    $itemAutoID = $items['itemAutoID'];
                    $item_details = fetch_item_data($itemAutoID);

                    $search[] = $item_details['itemDescription'].' - '.$item_details['itemSystemCode'].' -   - '.$item_details['seconeryItemCode'];
                    $itemAutoID_arr[] = $itemAutoID;
                    $itemcatergory = 'Inventory';
                    $UnitOfMeasureID[] =  $item_details['defaultUnitOfMeasureID'];
                    $conversionRate[] = 1;
                    $currentWareHouseStockQty[] = 0;
                    $quantityRequested[] = $items['qty'];
                    $comment[] = '';
                    $uom[] = $item_details['defaultUnitOfMeasure'].' | '.$item_details['defaultUnitOfMeasure'];

                }

                $_POST['search'] = $search;
                $_POST['itemAutoID'] = $itemAutoID_arr;
                $_POST['itemcatergory'] = $itemcatergory;
                $_POST['UnitOfMeasureID'] = $UnitOfMeasureID;
                $_POST['conversionRate'] = $conversionRate;
                $_POST['currentWareHouseStockQty'] = $currentWareHouseStockQty;
                $_POST['a_segment'] = $_POST['segment'];
                $_POST['quantityRequested'] = $quantityRequested;
                $_POST['comment'] = $comment;
                $_POST['itemIssueAutoID'] = $last_id;
                $_POST['uom'] = $uom;

                $detail_response = $this->Inventory_modal->save_material_detail_multiple();
                    
            }

            //Create material issue detail

            //Handle approvals
            $_POST['autoApproveDoc'] = 1;
            $detail_response = $this->Inventory_modal->material_item_confirmation();

            //return
            return array('status'=>'success','message'=>'Material Issue Generated Successfully');

        }


    }

    function generate_sales_order(){

        // Fetch details
        $billing_id = $this->input->post('billing_id');
        $job_id = $this->input->post('job_id');

        $job_details = get_jobs_master_detail($job_id);

        $billing_master = get_billing_master_record($billing_id);

        if($job_details){

            $contract_id = $job_details['contract_po_id'];

            $contract_details = get_contract_detail($contract_id);

            $data = array();
            $_POST['contractType'] = 'Sales Order';
            $_POST['segment'] = $contract_details['segmentID'].'|'.$contract_details['segmentCode'];
            $_POST['contractDate'] = $contract_details['contractDate'];
            $_POST['contractExpDate'] = $contract_details['contractExpDate'];
            $_POST['referenceNo'] = $job_details['job_code'];
            $_POST['customerID'] = $contract_details['customerID'];
            $_POST['transactionCurrencyID'] = $contract_details['transactionCurrencyID'];
            $_POST['contactPersonName'] = $contract_details['contactPersonName'];
            $_POST['contactPersonNumber'] = $contract_details['contactPersonNumber'];
            $_POST['RVbankCode'] = $contract_details['RVbankCode'];
            $_POST['paymentTerms'] = $contract_details['paymentTerms'];
            $_POST['contractNarration'] = $contract_details['contractNarration'];
            $_POST['currency_code'] = $contract_details['transactionCurrency'];
            $_POST['subDocumentReference'] = 'JOB-'.$job_details['job_code'].'-'.$job_details['id'];
            $_POST['isSystemGenerated'] = 1;

            // Call existing function
            $this->load->model('Quotation_contract_model');
            $header = $this->Quotation_contract_model->save_quotation_contract_header();

            if($header){
                $sales_order_id = $header['last_id'];

                $billing_details = get_billing_detail_record($billing_id);
                $price_id_detail = $price_id_arr = array();

                foreach($billing_details as $details){

                    if(isset($details['price_id']) && $details['price_id'] > 0){
                        if(!isset($price_id_arr[$details['price_id']])){
                            $price_id_detail[$details['price_id']] = get_grouping_billing_detail($billing_id,$details['price_id']);
                        } 
                    }

                }

                $_POST['contractAutoID'] = $sales_order_id;
                $details = $this->add_sales_order_detail($price_id_detail);
               

                //update sales order id
                $data_sales_order = array();
                $data_sales_order['sales_order_id'] = $sales_order_id; 
                $this->db->where('id',$billing_id)->update('srp_erp_job_billing',$data_sales_order);

                $this->session->set_flashdata('s', 'Sales Order Created Successfully.');
                return true;

            }

        }

         
        //Generate Sales Order Header

    }


    function add_sales_order_detail($sales_details){

      
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyID');
        $this->db->from('srp_erp_contractmaster');
        $this->db->where('contractAutoID', trim($this->input->post('contractAutoID') ?? ''));
        $contract_master = $this->db->get()->row_array();

        $this->db->select('taxMasterAutoID');
        $this->db->where('contractAutoID', $this->input->post('contractAutoID'));
        $tax_detail = $this->db->get('srp_erp_contracttaxdetails')->row_array();

        $this->db->trans_start();
        foreach ($sales_details as $key => $details) {

            $data['contractAutoID'] = trim($this->input->post('contractAutoID') ?? '');

            $data['itemAutoID'] = $details['itemAutoID'];
            $data['itemSystemCode'] = $details['price_text'];
            $data['itemDescription'] = $details['itemReferenceNo'];
            $data['itemCategory'] = $details['mainCategoryID'];
            $data['unitOfMeasure'] = $details['unitOfMeasure'];
            $data['unitOfMeasureID'] = $details['unitOfMeasureID'];
            $data['itemReferenceNo'] = $details['itemReferenceNo'];
            $data['defaultUOM'] = $details['unitOfMeasure'];
            $data['defaultUOMID'] = $details['unitOfMeasureID'];
            $data['conversionRateUOM'] = 1;
            // $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = 0;
            // $data['noOfItems'] = trim($noOfItems[$key]);
            $data['requestedQty'] = $details['qty'];;
            $data['unittransactionAmount'] = $details['unit_amount'];
            $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
            $data['companyLocalAmount'] = ($data['transactionAmount']/$contract_master['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount']/$contract_master['companyReportingExchangeRate']);
            $data['customerAmount'] = ($data['transactionAmount']/$contract_master['customerCurrencyExchangeRate']);
            $data['discountTotal'] = ($data['discountAmount'] * $data['requestedQty']);
            $data['comment'] = null;
            $data['remarks'] = null;
            
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_contractdetails', $data);
            $last_id = $this->db->insert_id();
                
            
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Order Detail : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Order Detail : Saved Successfully.');
        }

    }

    function edit_billing_details(){

        $billing_id = $this->input->post('billing_id');
        $billing_master = get_billing_master_record($billing_id);

        return $billing_master;

    }

    function edit_report_details(){

        $report_id = $this->input->post('report_id');
        $billing_master = get_daily_report_master($report_id);

        return $billing_master;

    }

    function save_visitor_log_request(){

        $job_id = $this->input->post('job_id');
        $userName = $this->input->post('userName');
        $userEmail = $this->input->post('userEmail');
        $userMessage = $this->input->post('userMessage');

        $date = $this->common_data['current_date'];

        $this->db->trans_start();
        
        $job_detail = get_jobs_master_detail($job_id);

        if($job_detail){

            $ref = strtotime($date).rand(11111,99999).rand(11111,99999).rand(11111,99999);

            $data = array();
            $data['empName'] = $userName;
            $data['empEmail'] = $userEmail;
            $data['empMessage'] = $userMessage;
            $data['createdDate'] = $date;
            $data['expDate'] = date('Y-m-d H:i:s',strtotime('+3 days',strtotime($date)));
            $data['validReference'] = $ref;
            $data['job_id'] = $job_id;
            $data['pin'] = rand(1111,9999);
            $data['contractAutoID'] = $job_detail['contract_po_id'];
            $data['status'] = 0;
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] =$this->common_data['company_data']['company_id'];
            $data['createdUserID']  = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            
            $res = $this->db->insert('srp_erp_op_visitor_log_link',$data);
    
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('e', 'Something went wrong.');
                return False;
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Vistor log link Created Successfully.');
                return True;
            }
        }
    }



    function assignCommon_AssetListForContract_job(){

        $job_id = $this->input->post('job_id');
        $assignAssetListSync = $this->input->post('assignAssetListSync');

        $companyid = current_companyID();
        $data = array();
        $emp_already = 1;

        if(count($assignAssetListSync)>0){
            foreach($assignAssetListSync as $val){
                $fa_details = get_fa_assets_details_job($val);

                if(!empty($fa_details)){
                    $emp_already_exists =  get_added_assets_details($val,$job_id);

                    if(empty($emp_already_exists)){
                        $data['job_id'] = $job_id;
                        $data['faID'] = $fa_details['faID'];
                        $data['assetCode'] = $fa_details['faCode'];
                        $data['assetName'] = $fa_details['assetDescription'];
                        $data['assetRef'] = '';
                        $data['status'] = 1;
                        $data['companyID'] =  $this->common_data['company_data']['company_id'];
                        $data['companyCode'] =  $this->common_data['company_data']['company_code'];
                
                        $this->db->insert('srp_erp_job_assetsdetail',$data);
                    }
                }
            }

            if($data){
                return array('s', 'Asset added Successfully.');
            }else{
                return array('e', 'Asset Not Found');
            }
        }else{
            return array('e', 'Asset Not Found');
        }
        
    }

    function confirm_billing_standard(){

        $billing_id = $this->input->post('billing_id');
        $job_id = $this->input->post('job_id');
        $confirmedYN = $this->input->post('confirmedYN');
        $comment = $this->input->post('comment');

        $data = array();
        $data['confirmComment']=$comment;
        $data['confirmedYN'] =  $confirmedYN;
        $data['confirmedDate'] =  $this->common_data['current_date'];
        $data['confirmedBy']  = $this->common_data['current_userID'].' - '.$this->common_data['current_user'];

        //Create MAterial Issue 
        // $material_issue = $this->set_material_issue($job_id, $billing_id);


        $issue_message = '';
        if(isset($material_issue['status']) && in_array($material_issue['status'],['error','success'])){
            $issue_message = $material_issue['message'];
        }

        try {
            $this->db->where('id',$billing_id)->where('companyID',$this->common_data['company_data']['company_id'])->update('srp_erp_job_billing',$data);
            $this->session->set_flashdata('s', "Billing confirmed Successfully. <br> {$issue_message}");
            return true;
        } catch (\Throwable $th) {
            $this->session->set_flashdata('e', 'Something went wrong.');
            throw $th;
        }

    }

    function save_item_order_detail_job_billing_modify()
    {
        $description = $this->input->post('description');
        $datefrom_modify = $this->input->post('datefrom_modify');
        $dateto_modify = $this->input->post('dateto_modify');
        $isStandby_modify = $this->input->post('isStandby_modify');
        $isNpt_modify = $this->input->post('isNpt_modify');
        $min_modify = $this->input->post('min_modify');
        $pID = $this->input->post('pID');
        $qty_modify = $this->input->post('qty_modify');
        $rate_modify = $this->input->post('rate_modify');
        $movingcost = $this->input->post('movingcost');
        $Additionalcost = $this->input->post('Additionalcost');

        $total_modify = $this->input->post('total_modify');
        $job_id = $this->input->post('job_id');
        $billing_id = $this->input->post('billing_id');
        
        $this->db->trans_start();
        foreach ($pID as $key => $val) {

            $price_detail = get_added_price_details($pID[$key]);

            $data['job_id'] = $job_id;
            $data['description'] = $description[$key];
            $data['dateFrom'] = $datefrom_modify[$key];
            $data['dateTo'] = $dateto_modify[$key];
            $data['companyID'] =  $this->common_data['company_data']['company_id'];
            $data['companyCode'] =  $this->common_data['company_data']['company_code'];
            $data['qty'] = $qty_modify[$key];
            $data['billing_header'] = $billing_id;
            $data['unit_amount'] = $rate_modify[$key];
            $data['total_amount'] = $total_modify[$key]+$movingcost[$key]+$Additionalcost[$key];
            $data['movingCost'] = $movingcost[$key];
            $data['additionalCost'] = $Additionalcost[$key];
            $data['isStandby'] = $isStandby_modify[$key];
            $data['isNpt'] = $isNpt_modify[$key];
            $data['price_id'] = $pID[$key];
            $data['price_text'] = $price_detail['typeItemName'];
            
            $this->db->insert('srp_erp_job_billing_detail', $data);
          
        }

        

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Order Detail : Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Order Detail : Saved Successfully.');
        }
        
    }
}