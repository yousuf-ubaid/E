<?php
    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('sales&marketing_salescom', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    $this->load->helper('jobs_helper');
    $title = 'Create Job';
    echo head_page($title, false);

    $job_id = $data_arr;

    $current_date_new = date('Y-m-d H:i:s');
    $current_date=date('Y-m-j H:i:s',strtotime($current_date_new));
    $current_time = date('H:i:s');
    $default_date = date('Y-m-d H:i:s',strtotime($current_date));
    $default_time = date('H:i:s',strtotime($current_time));
    $customer_arr = all_customer_drop(true);
    $default_date2 = date('Y-m-d',strtotime($current_date));

    $date_format_policy = date_format_policy();
    $status_arr = array(''=>'Select Status','1'=>'Scheduled Job','2'=>'In Progress','3' => 'Job Hold','4' => 'Job Completed','5'=>'Call Off');
    $JobTypeArr = array(''=>'Select Job Type','Completion'=>'Completion','WorkOver'=>'WorkOver','Drilling' => 'Drilling','Plugging' => 'Plugging','Repair'=>'Repair');
    $field_arr = get_field_list(true);
    $rig_hoist = get_rig_hoist_list(true);
    $activity_type_arr = get_activity_category(true);

    $contract_activity = get_job_activity_details($job_id,null,1);
    $contract_price = get_contract_pricelist($job_id);
    $job_contract_details = get_jobs_master_detail($job_id);
    
    $doc_id = $this->sequence->sequence_generator_job('CJOB', 0, 1);
    $billingID = $this->common_data['company_data']['company_code'].'/';
    $fuelty = all_fuel_type_drop();
    $umo_arr = all_umo_new_drop();
    ///$loademp = load_employee_drop($status = false);
    $loademp =get_employee_current_company();
    $sectionCode="";
    $visibilityUserID="";
    $companyID = current_companyID();

    $operation_flow = getPolicyValues('OPF', 'All'); // policy for operation
    $segment_arr = fetch_segment();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<!-- Include flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .checkbox-section{
        display:flex;
        flex-direction: row;
    }
    .p3{padding:10px;}

    #activity_shift_data_modal, #add_billing_data {
        overflow-y:scroll;
    }
    
    /*********attachement css********* */
    .past-info {
    background: #fff;
    border-radius: 3px;
    -webkit-border-radius: 3px;
    padding: 0 0 8px 10px;
    margin-left: 2px;
    }
    .past-info #toolbar, .past-info .toolbar {
        background: #f8f8f8;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        border-radius: 3px 3px 0 0;
        -webkit-border-radius: 3px 3px 0 0;
        border: #dcdcdc solid 1px;
        padding: 5px 15px 12px 10px;
        height: 20px;
    }
    .past-info #toolbar .toolbar-title, .past-info .toolbar .toolbar-title {
        font-size: 13px;
        font-weight: bold;
        color: #000;
        float: left;
        margin-top: -4px;
    }
    .past-info .post {
        position: relative;
        border-width: 0 1px 1px 1px;
        border-color: #dcdcdc;
        border-style: solid;
        padding: 10px 20px 7px 21px;
        font-size: 11px;
        min-height: 40px;
    }
    .past-info .post:last-child {
        border-radius: 0 0 3px 3px;
        -webkit-border-radius: 0 0 3px 3px;
    }
    .item-label {
        color: #fff;
        height: 20px;
        width: 90px;
        position: absolute;
        font-weight: bold;
        padding-left: 8px;
        padding-top: 3px;
        top: 10px;
        right: -5px;
        margin-right: 0;
        border-radius: 3px 3px 0 3px;
        box-shadow: 0 3px 3px -2px #ccc;
        text-transform: capitalize;
    }
    .item-label:after {
        top: 20px;
        right: 0;
        border-top: 4px solid #585858;
        border-right: 4px solid rgba(0, 0, 0, 0);
        content: "";
        position: absolute;
    }
    .past-info .post div.item-label.file {
        background-color: #11a9cc;
    }
    .past-info .post .time {
        float: right;
        margin: 25px 0 0 10px;
        text-align: right;
        color: #666;
        line-height: 14px;
    }
    .past-info .post .icon {
        position: absolute;
        left: -14px;
        top: 8px px;
    }
    .past-info .post .icon {
        left: -16px;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        background-color: #f8f8f8;
        border: solid 1px #d1d1d1;
    }
    .past-info .post .icon img {
        margin-left: 6px;
        margin-top: 6px;
    }
    .past-info .post .infoarea {
        overflow: hidden;
        padding: 0 0 7px;
    }
    .past-info .post .attachemnt_title {
        color: #262626;
        display: block;
        font-size: 12px;
    }
    .past-info .post .attachemnt_title span {
        display: block;
        font-weight: normal;
        color: #666;
        font-size: 12px;
    }

    #job_assets_datatbl tr > *:nth-child(3) {
        display: none;
    }

    #job_assets_datatbl tr > *:nth-child(2) {
        display: none;
    }
 
</style>

<div class="m-b-md" id="wizardControl">

    <div class="steps" id="main-tabs">
        <?php 
        

        if(is_array($job_contract_details) && $job_contract_details['contract_po_id']){
            $result_v = checkVisibilitySection($job_contract_details['contract_po_id']);
        }else{
            $result_v = 1;
        }


        if($result_v){

            $checkSysAdminResult = checkSystemAdmin($this->common_data['current_userID']);         
            if($checkSysAdminResult['isSystemAdmin'] == 1){ ?>
                <a class="step-wiz step--incomplete step--active step-wiz" href="#details" data-toggle="tab">     
                    <img src="<?php echo base_url('images/opr-1.png') ?>"/>               
                    <span class="step__label">Details </span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain1){
                    //echo '"'.$keymain['visibilityuserIDs'].'"<br/><br/>';    
                    
                    $user1 = explode(',',$keymain1['visibilityuserIDs']);      

                    foreach($user1 as $key1) {                                                 
                            if($keymain1['sectionCode'] == "Details" && $key1 == $this->common_data['current_userID']){ ?>           
                                <a class="step-wiz step--incomplete step--active step-wiz" href="#details" data-toggle="tab">                                    
                                    <img src="<?php echo base_url('images/opr-1.png') ?>"/>
                                    <span class="step__label">Details</span>
                                </a>
                                <?php 
                            }                  
                    }
                }
            }
            ?>

            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#visitorsLog" data-toggle="tab" onclick="visitors_log_added_table()">
                    <img src="<?php echo base_url('images/opr-2.png') ?>"/>
                    <span class="step__label">Visitors Log </span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain2){
                    
                    $user2 = explode(',',$keymain2['visibilityuserIDs']);      

                    foreach($user2 as $key2) {    
                        
                        if($keymain2['sectionCode'] == "VisitorsLog" && $key2 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#visitorsLog" data-toggle="tab" onclick="visitors_log_added_table()">
                                <img src="<?php echo base_url('images/opr-2.png') ?>"/>
                                <span class="step__label">Visitors Log </span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
            ?>

            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#crew" data-toggle="tab">
                    <img src="<?php echo base_url('images/opr-3.png') ?>"/>
                    <span class="step__label">Crew </span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain3){
                    
                    $user3 = explode(',',$keymain3['visibilityuserIDs']);      

                    foreach($user3 as $key3) {    
                        
                        if($keymain3['sectionCode'] == "Crew" && $key3 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#crew" data-toggle="tab">
                                <img src="<?php echo base_url('images/opr-3.png') ?>"/>
                                <span class="step__label">Crew </span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
            ?>

            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#assets" data-toggle="tab" onclick="asset_added_table()">
                    <img src="<?php echo base_url('images/opr-4.png') ?>"/>
                    <span class="step__label">Assets </span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain4){
                    
                    $user4 = explode(',',$keymain4['visibilityuserIDs']);      

                    foreach($user4 as $key4) {    
                        
                        if($keymain4['sectionCode'] == "Assets" && $key4 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#assets" data-toggle="tab" onclick="asset_added_table()">
                                <img src="<?php echo base_url('images/opr-4.png') ?>"/> 
                                <span class="step__label">Assets </span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
            ?>

            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#fuelutilization" data-toggle="tab" onclick="fuel_utilization_table()">
                    <img src="<?php echo base_url('images/opr-5.png') ?>"/> 
                    <span class="step__label">Fuel Utilization </span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain5){
                    
                    $user5 = explode(',',$keymain5['visibilityuserIDs']);      

                    foreach($user5 as $key5) {    
                        
                        if($keymain5['sectionCode'] == "fuel" && $key5 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#fuelutilization" data-toggle="tab" onclick="fuel_utilization_table()">
                                <img src="<?php echo base_url('images/opr-5.png') ?>"/> 
                                <span class="step__label">Fuel Utilization </span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
            ?>








            <?php    
            if($operation_flow != 'Almansoori'){
                if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                    <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#pipeTally" data-toggle="tab" id="tally_tab_admin" onclick="pipe_tally_table()">
                    <img src="<?php echo base_url('images/opr-5.png') ?>"/>
                        <span class="step__label">Pipe Tally </span>
                    </a>
                <?php
                }else {
                    foreach($result_v as $keymain6){
                        
                        $user6 = explode(',',$keymain6['visibilityuserIDs']);      

                        foreach($user6 as $key6) {    
                            
                            if($keymain6['sectionCode'] == "PipeTally" && $key6 == $this->common_data['current_userID']){ ?>           
                                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#pipeTally" id="tally_tab_user" data-toggle="tab" onclick="pipe_tally_table()">
                                    <img src="<?php echo base_url('images/opr-5.png') ?>"/>
                                    <span class="step__label">Pipe Tally </span>
                                </a>
                                <?php 
                            }  
                        }
                    }
                }
            }
            ?>

            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#activities" data-toggle="tab" onclick="activity_shift_added_table()">
                    <img src="<?php echo base_url('images/opr-7.png') ?>"/>
                    <span class="step__label">Activities </span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain7){
                    
                    $user7 = explode(',',$keymain7['visibilityuserIDs']);      

                    foreach($user7 as $key7) {    
                        
                        if($keymain7['sectionCode'] == "Activities" && $key7 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#activities" data-toggle="tab" onclick="activity_shift_added_table()">
                                <img src="<?php echo base_url('images/opr-7.png') ?>"/>
                                <span class="step__label">Activities </span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
            ?>

            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#dailyReport" data-toggle="tab"  onclick="daily_report_added_table()">
                    <img src="<?php echo base_url('images/opr-8.png') ?>"/>
                    <span class="step__label">Daily Reports </span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain8){
                    
                    $user8 = explode(',',$keymain8['visibilityuserIDs']);      

                    foreach($user8 as $key8) {    
                        
                        if($keymain8['sectionCode'] == "dailyReports" && $key8 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#dailyReport" data-toggle="tab"  onclick="daily_report_added_table()">
                                <img src="<?php echo base_url('images/opr-8.png') ?>"/>
                                <span class="step__label">Daily Reports </span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
            ?>       

            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#checkList" data-toggle="tab" onclick="fetch_assign_checklist_table()">
                    <img src="<?php echo base_url('images/opr-9.png') ?>"/>
                    <span class="step__label">Check List</span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain9){
                    
                    $user9 = explode(',',$keymain9['visibilityuserIDs']);      

                    foreach($user9 as $key9) {    
                        
                        if($keymain9['sectionCode'] == "checkList" && $key9 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#checkList" data-toggle="tab" onclick="fetch_assign_checklist_table()">
                                <img src="<?php echo base_url('images/opr-9.png') ?>"/>
                                <span class="step__label">Check List </span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
            ?>

            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#attachment" data-toggle="tab" onclick="op_job_attachments()">
                    <img src="<?php echo base_url('images/opr-10.png') ?>"/>
                    <span class="step__label">Attachment</span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain10){
                    
                    $user10 = explode(',',$keymain10['visibilityuserIDs']);      

                    foreach($user10 as $key10) {    
                        
                        if($keymain10['sectionCode'] == "attachment" && $key10 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#attachment" data-toggle="tab" onclick="op_job_attachments()">
                                <img src="<?php echo base_url('images/opr-10.png') ?>"/>
                                <span class="step__label">Attachment </span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
            ?>       

            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" id="billing_job_admin" href="#billing" data-toggle="tab" onclick="daily_billing_table()">
                    <img src="<?php echo base_url('images/opr-11.png') ?>"/>
                    <span class="step__label">Billing</span>
                </a>

                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz hide" id="billing_standard_admin" href="#billing_standard" data-toggle="tab" onclick="daily_billing_table_standard()">
                    <img src="<?php echo base_url('images/opr-11.png') ?>"/>
                    <span class="step__label">Billing</span>
                </a>

                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz hide" id="billing_modify_admin" href="#billing_modify" data-toggle="tab" onclick="daily_billing_table_standard()">
                    <img src="<?php echo base_url('images/opr-11.png') ?>"/>
                    <span class="step__label">Billing</span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain11){
                    
                    $user11 = explode(',',$keymain11['visibilityuserIDs']);      

                    foreach($user11 as $key11) {    
                        
                        if($keymain11['sectionCode'] == "billing" && $key11 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" id="billing_job_user" href="#billing" data-toggle="tab" onclick="daily_billing_table()">
                                <img src="<?php echo base_url('images/opr-11.png') ?>"/>
                                <span class="step__label">Billing </span>
                            </a>

                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz hide" id="billing_standard_user" href="#billing_standard" data-toggle="tab" onclick="daily_billing_table_standard()">
                                <img src="<?php echo base_url('images/opr-11.png') ?>"/>
                                <span class="step__label">Billing </span>
                            </a>

                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz hide" id="billing_modify_user" href="#billing_modify" data-toggle="tab" onclick="daily_billing_table_modify()">
                                <img src="<?php echo base_url('images/opr-11.png') ?>"/>
                                <span class="step__label">Billing</span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
            ?>       
            
            <?php    
            if($checkSysAdminResult['isSystemAdmin'] == 1){  ?>
                <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#confirm" data-toggle="tab" onclick="call_confirm_view()">
                    <img src="<?php echo base_url('images/opr-12.png') ?>"/>
                    <span class="step__label">Close Job</span>
                </a>
            <?php
            }else {
                foreach($result_v as $keymain12){
                    
                    $user12 = explode(',',$keymain12['visibilityuserIDs']);      

                    foreach($user12 as $key12) {    
                        
                        if($keymain12['sectionCode'] == "confirm" && $key12 == $this->common_data['current_userID']){ ?>           
                            <a class="step-wiz step--incomplete step--inactive btn-wizard step-wiz" href="#confirm" data-toggle="tab" onclick="call_confirm_view()">
                                <img src="<?php echo base_url('images/opr-12.png') ?>"/>
                                <span class="step__label">Close Job </span>
                            </a>
                            <?php 
                        }  
                    }
                }
            }
        }else{
       
            echo "Currently there are no sections assign to the user.";
        }
        ?>       
       
    </div>

</div>
<hr>

<?php 
// $result_v2 = checkVisibilitySection($job_contract_details['contract_po_id']);



if($result_v){ ?>
<div class="tab-content">
   
    <div class="tab-pane active" id="details">
        <form class="form-horizontal-" id="job_details_form">
        <input type="hidden" name="job_id" id="job_id" value="<?php echo $job_id ?>">
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Document ID' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>

                    <?php 
                        if($job_id){
                            $doc_id_new = get_job_code($job_id); 
                             ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id_new['job_code']; ?>"  required readonly>
                    <?php } else{ ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>"  required readonly>
                    <?php } ?>
                    
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                <div id="customer_div">
                    <?php echo form_dropdown('customerCode', $customer_arr, '', 'class="form-control select2" id="customer"onchange="change_customer()"'); ?>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Contract/PO' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <?php echo form_dropdown('contractAutoID', '',isset($job_contract_details['contract_po_id']) ? $job_contract_details['contract_po_id'] : '', 'class="form-control select2" id="contract" required onchange="change_contract()"'); ?>
                </div>
            </div>

        </div>

        <div class="row">   

            <div class="form-group col-sm-3">
                <label for="segment"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></label><!--Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment" required'); ?>
            </div>

            
            <div class="form-group col-sm-4">
                <label><?php echo 'Currency' ?>
                <input type="hidden" id="currencyID" name="currencyID" value="" >
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" class="form-control" name="currencyCode" id="currencyCode" value=""  readonly>
                </div>
            </div>
            <?php 
            if($operation_flow != 'Almansoori'){ ?>
            <div class="form-group col-sm-3">
                <label><?php echo 'PO Value' ?>
                    </label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" class="form-control" name="po_value" id="po_value" value="">
                </div>
            </div>

            <div class="form-group col-sm-3">
                <label><?php echo 'PO Number' ?>
                   </label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" class="form-control" name="po_number" id="po_number" value="">
                </div>
            </div>
            <?php } ?>

            <div class="form-group <?php ($operation_flow == 'Almansoori') ? "col-sm-4" : "col-sm-3" ?>">
                <label><?php echo 'Document Date' ?>
                   <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" class="w-100" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="doc_date" class="form-control" required>
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Job Name' ?>
                  <?php required_mark(); ?></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" class="w-100" name="doc_name" id="doc_name" value=""  >
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Type' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <?php echo form_dropdown('job_type', $JobTypeArr, '', 'class="form-control select2" id="job_type" required onchange=""'); ?>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Reference' ?>
                    <!--Receipt Voucher Date--> </label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" class="w-100" name="doc_ref" id="doc_ref" value=""  >
                </div>
            </div>

            
          
        </div>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="financeyear"><?php echo 'Field';?></label><!--Financial Period-->
                <div id="customer_div">
                    <?php echo form_dropdown('field_id', $field_arr, '', 'class="form-control select2" id="field_id" required onchange="change_field($(this))"'); ?>
                </div>
            </div>

            <div class="form-group col-sm-3" style="">
                <label for="financeyear"><?php echo 'Well';?></label><!--Financial Period-->
                <div id="customer_div">
                    <?php echo form_dropdown('well_id', '', '', 'class="form-control select2" required id="well_id" onchange=""'); ?>
                </div>
            </div>

            <div class="form-group col-sm-3">
                <label for="well_number"><?php echo 'Well Number';?></label><!--Financial Period-->
                <input type="text" class="form-control" name="well_number" id="well_number" value="">
            </div>

            <div class="form-group col-sm-3">
                <label for="financeyear"><?php echo 'Rig / Hoist';?></label><!--Financial Period-->
                <div id="customer_div">
                    <?php echo form_dropdown('rig_id', $rig_hoist, '', 'class="form-control select2" required id="rig_id" onchange="Otable.draw()"'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="ptw_number"><?php echo 'PTW Number';?></label><!--Financial Period-->

                <input type="text" class="form-control" name="ptw_number" id="ptw_number" value="">
              
            </div>

            <div class="form-group col-sm-3">
                <label for="iso_certificate"><?php echo 'Isolation Certificate';?></label><!--Financial Period-->
                <input type="text" class="form-control" name="iso_certificate" id="iso_certificate" value="" >
            </div>

            <div class="form-group col-sm-3">
                <label for="hot_permit_number"><?php echo 'Hotwork Permit Number';?></label><!--Financial Period-->
                <input type="text" class="form-control" name="hot_permit_number" id="hot_permit_number" value="">
            </div>
            <?php 
            if($operation_flow != 'Almansoori'){ ?>
            <div class="form-group col-sm-3">
                <label for="muster_area"><?php echo 'Muster Area';?></label><!--Financial Period-->
                <input type="text" class="form-control" name="muster_area" id="muster_area" value="">
            </div>

            <div class="form-group col-sm-3">
                <label for="sftp_number"><?php echo 'SFTP Number';?></label><!--Financial Period-->
                <input type="text" class="form-control" name="sftp_number" id="sftp_number" value="">
            </div>
            <div class="form-group col-sm-3">
                <label for="pw_pump_number"><?php echo 'Produce Water Pump';?></label><!--Financial Period-->
                <input type="text" class="form-control" name="pw_pump_number" id="pw_pump_number" value="">
            </div>
            <div class="form-group col-sm-3">
                <label for="weight"><?php echo 'Weight';?></label><!--Financial Period-->
                <input type="text" class="form-control" name="weight" id="weight" value="">
            </div>
            <div class="form-group col-sm-3">
                <label for="prv_set"><?php echo 'PRV Set At';?></label><!--Financial Period-->
                <input type="text" class="form-control" name="prv_set" id="prv_set" value="">
            </div>
            <?php } ?>

        </div>

        <div class="row">
            <div class="form-group col-sm-12">
                <label for="financeyear"><?php echo 'Job Objective Summary';?></label><!--Financial Period-->
                <div id="">
                   <textarea class="form-control" name="job_obj_summary" id="job_obj_summary"></textarea>
                </div>
            </div>
        </div>


        <div class="row">
            
            <div class="form-group col-sm-2">
                <label><?php echo 'Job From' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="fromDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="fromDate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'Job To'  ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="toDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="toDate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label for="financeyear"><?php echo 'Job Status';?></label><!--Financial Period-->
                <div id="customer_div">
                    <?php echo form_dropdown('status', $status_arr, '', 'class="form-control select2" id="status" required onchange="change_job_status($(this))"'); ?>
                </div>
            </div>

            <div class="form-group col-sm-3">
                <label for="financeyear"><?php echo 'Job Start Time';?></label><!--Financial Period-->
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="start_time" value="" id="start_time" class="form-control">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="shift_hours"><?php echo 'Shift Hours';?></label><!--Financial Period-->
                <input type="text" class="form-control" name="shift_hours" id="shift_hours" value="">
            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-12">
                <label for="financeyear"><?php echo 'Job Description';?></label><!--Financial Period-->
                <div id="customer_div">
                   <textarea class="form-control" name="job_description" id="job_description"></textarea>
                </div>
            </div>
        </div>

        <div class="row">

            <button id="checklist_users_add" type="button" onclick="open_checklist_user_assign_model()" class="btn btn-primary-new size-xs pull-right mb-1 hide"><i
                        class="fa fa-plus"></i> Add Checklist Users
            </button>
            
            <div class="form-group col-sm-6 hide" id="pre_job_checklist_area">

 
                
            </div>
        </div>

        <div class="row">
            <div class="pull-right">
                <button type="submit" class="btn btn-lg btn-primary-new size-lg">Save & Next</button>
            </div>
           
        </div>
        </form>


        <div class="row">
            <div class="form-group col-sm-3 hide" id="well_program_att">
                <label for="well_number"><?php echo 'Well Program';?></label><!--Financial Period-->
                
                <div class="row">
                    <?php echo form_open_multipart('', 'id="job_attachment_uplode_form_one" class="form-inline"'); ?>
                    <div class="col-sm-12">                        
                        <div class="col-sm-12"  style="margin-top: -8px;">
                                <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                                    value="Well">
                                <input type="hidden" class="form-control" id="job_id"
                                    name="job_id"
                                    value="<?php echo $job_id ?>">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                    style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                            class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                            class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                            class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                        aria-hidden="true"></span></span><span
                                            class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                            aria-hidden="true"></span></span><input
                                            type="file" name="document_file_well" id="document_file_well"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                    data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="document_uplode_well()"><span
                                    class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="show_all_attachments_well"></div>
            </div>
            <div class="form-group col-sm-3 hide"  id="bob_chart_recorder_att">
                <label for="bob_number"><?php echo 'BOB Chart Recorder';?></label><!--Financial Period-->        
                
                <div class="row">
                    <?php echo form_open_multipart('', 'id="job_attachment_uplode_form_two" class="form-inline"'); ?>
                    <div class="col-sm-12">                        
                        <div class="col-sm-12"  style="margin-top: -8px;">
                                <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                                    value="BOB">
                                <input type="hidden" class="form-control" id="job_id"
                                    name="job_id"
                                    value="<?php echo $job_id ?>">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                    style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                            class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                            class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                            class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                        aria-hidden="true"></span></span><span
                                            class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                            aria-hidden="true"></span></span><input
                                            type="file" name="document_file_bob" id="document_file_bob"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id1"
                                    data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="document_uplode_bob()"><span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form>
                        </div>
                    </div>
                </div>

                <div id="show_all_attachments_bob"></div>
            </div>
        </div>


        <hr>
        <div class="table-section" id="item-details-table-section" style="display:none">

            <div class="title">
                <h4>Details</h4>
            </div>

            <div class="">
                <div class="pull-right">
                    <button class="btn btn-default mb-2" onclick="add_more_items()">
                        <i class="fa fa-plus"></i> Add
                    </button>
                </div>
                <table id="job_item_datatbl" class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 15%"><?php echo 'Code'; ?></th>
                            <!--Invoice Code-->
                            <th style="min-width: 43%"><?php echo 'Description'; ?></th>
                            <!--Details-->
                            <th style="min-width: 15%"><?php echo 'UOM'; ?></th>
                            <!--Total Value-->
                            <th style="min-width: 5%"><?php echo 'Qty' ?></th>
                            <!--Total Value-->
                            <th style="min-width: 5%"><?php echo 'Value' ?></th>
                            <!--Total Value-->
                            <th style="min-width: 5%"><?php echo 'Discount' ?></th>
                            <!--Total Value-->
                            <th style="min-width: 5%"><?php echo 'Net Amount' ?></th>
                            <!--Total Value-->
                            <th style="min-width: 5%"><?php echo 'Comment' ?></th>
                            <!--Total Value-->
                            <th style="min-width: 5%"><?php echo '' ?></th>
                         
                        </tr>
                    </thead>
                </table>
            </div>


        </div>
    </div>
    <div class="tab-pane" id="crew">
        
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Document ID' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_id_sub" id="doc_id_sub" class="doc_id_sub w-100" value="<?php echo $doc_id ?>"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Document Date' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="doc_date_sub" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="doc_date_sub" class="doc_date_sub form-control" readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Reference' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_ref_sub" id="doc_ref_sub" value="" class="doc_ref_sub w-100"  readonly>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                <div id="customer_div">
                    <input type="text" name="doc_customer" id="doc_customer" value="" class="doc_customer form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Contract/PO' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_po_num" id="doc_contract_po_num" value="" class="doc_contract_po_num form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'Currency' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_currency" id="doc_contract_currency" value="" class="doc_contract_currency form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'PO Number' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_po_number" id="doc_po_number" value="" class="doc_po_number form-control"  readonly>
                </div>
            </div>

        </div>

        <hr>

        <div class="table-crew">

            <div class="title">
                <h4>Crew</h4>
            </div>    
            <div class="text-right">
                <button class="btn btn-primary size-sm mb-2" onclick="add_crew()">
                    <i class="fa fa-plus"></i> Add 
                </button>
            </div>
            <div class="table-responsive">                    
                <table id="crew_added_table" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>

                                <th style="min-width: 15%"><?php echo 'EMPID'; ?></th>
                                <th style="min-width: 15%;"><?php echo 'Task ID'; ?></th>

                                <th style="min-width: 15%"><?php echo 'EMP ID'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 43%"><?php echo 'Name'; ?></th>
                                <!--Details-->
                                <th style="min-width: 15%"><?php echo 'Designation'; ?></th>
                             
                                <th style="min-width: 5%"><?php echo 'Grouping' ?></th>
                                 <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Competency Y/N' ?></th>
                                 <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Training Y/N' ?></th>
                                 <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'SSC Y/N' ?></th>
                              
                                <th style="min-width: 5%"><?php echo 'Action' ?></th>
                            
                            </tr>
                        </thead>
                </table>
            </div>

        </div>
        
    </div>

    <div class="tab-pane" id="visitorsLog">        
        <div class="row">
        <div class="col-md-12">
                <div class="title">
                    <h4>Site Visitors Log</h4>
                </div>    
                <div class="text-right">
                    <button class="btn btn-primary size-sm mb-2" onclick="add_online_link_request('visitor_log')">
                        <i class="fa fa-envelope"></i> Link
                    </button>
                    <button class="btn btn-primary size-sm mb-2" onclick="add_visitor()">
                        <i class="fa fa-plus"></i> Add
                    </button>
                </div>
                <div class="table-responsive">                    
                    <table id="visitors_log_added_table" class="<?php echo table_class(); ?>">
                            <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>

                                    <th style="min-width: 10%">Date </th>
                                    <th style="min-width: 10%;">Full Name </th>

                                    <th style="min-width: 10%">Full Company</th>
                                    <!--Invoice Code-->
                                    <th style="min-width: 10%">Position</th>
                                    <!--Details-->
                                    <th style="min-width: 10%">Purpose of Visit</th>
                                
                                    <th style="min-width: 10%">Mobile No.</th>
                                    <!--Total Value-->
                                    <th style="min-width: 5%">Medication</th>
                                    <!--Total Value-->
                                    <th style="min-width: 10%">H2S Validity</th>
                                    <!--Total Value-->
                                    <th style="min-width: 5%">Rig Safety Briefing</th>
                                
                                    <th style="min-width: 5%">Proper PPE in Use</th>
                                    <th style="min-width: 5%">Time In</th>
                                    <th style="min-width: 5%">Time Out</th>
                                    <th style="min-width: 5%">Action</th>
                                </tr>
                            </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="assets">
        
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Document ID' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <!--<input type="text" name="doc_id" id="doc_id" class="w-100" value="<?php //echo $doc_id ?>"  readonly>-->
                    <?php 
                        if($job_id){
                            $doc_id_new = get_job_code($job_id); 
                             ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id_new['job_code']; ?>" readonly>
                    <?php } else{ ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" readonly>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Document Date' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVdate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Reference' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_ref_sub" id="doc_ref_sub" value="" class="doc_ref_sub w-100"  readonly>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                <div id="customer_div">
                    <input type="text" name="doc_customer" id="doc_customer" value="" class="doc_customer form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Contract/PO' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_po_num" id="doc_contract_po_num" value="" class="doc_contract_po_num form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'Currency' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_currency" id="doc_contract_currency" value="" class="doc_contract_currency form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'PO Number' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_po_number" id="doc_po_number" value="" class="doc_po_number form-control"  readonly>
                </div>
            </div>

        </div>

        <hr>

        <div class="table-crew">

            <div class="title">
                <h4>Assets</h4>
            </div>    
            <div class="text-right">
                <button class="btn btn-primary size-sm mb-2" onclick="add_asset()">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
                <div class="table-responsive">
                    
                    <table id="job_assets_datatbl" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th>#</th>

                                <th><?php echo 'FA ID'; ?></th> <!--this column is hide on css reason => get table id (if you add new column plz check add_asset_from_date and add_asset_to_date functions)-->
                                <th>#</th> <!--this column is hide on css reason => get table id (if you add new column plz check add_asset_from_date and add_asset_to_date functions)-->
                     
                                <th><?php echo 'Code'; ?></th>
                                <!--Invoice Code-->
                                <th width="200px"><?php echo 'Name'; ?></th>
                                <!--Details-->
                                <th><?php echo 'Reference'; ?></th>
                                <!--Total Value-->
                                <th><?php echo 'Date From' ?></th>
                                <!--Total Value-->
                                <th><?php echo 'Date To' ?></th>
                                <!--Total Value-->
                                <th><?php echo 'Days/Hours' ?></th>
                                <!--Total Value-->
                                <!-- <th><?php echo 'Comment' ?></th> -->
                                 <!--Total Value-->
                                <th><?php echo 'Maintenance Y/N' ?></th>
                                 <!--Total Value-->
                                 <!-- <th style="min-width: 5%"><?php //echo 'Status' ?></th> -->
                                <!--Total Value-->
                                <th><?php echo 'Action' ?></th>
                            
                            </tr>
                        </thead>
                    </table>
                </div>

        </div>

    </div>

    

    <!--- Fuel Utilization--->
    <div class="tab-pane" id="fuelutilization">        
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h4>Fuel Utilization</h4>
                </div>    
                <div class="text-right">
                    <button class="btn btn-primary size-sm mb-2" onclick="add_fuel_receipt()">
                        <i class="fa fa-plus"></i> Receipt
                    </button>

                    <button class="btn btn-primary size-sm mb-2" onclick="add_issue_fuel()">
                        <i class="fa fa-plus"></i> Issue
                    </button>
                </div>
                <table id="fuel_utilization_table" class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            
                            <th>#</th>
                            <th>Fuel Type</th>
                            <th>Document Id</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>UoM</th>
                            <th>Receipt</th>      
                            <th>Issue</th>
                            <th>Balance</th>  
                            <th>Rec/Iss by</th>
                            <th>Action</th>  

                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>










    <div class="tab-pane" id="pipeTally">        
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h4>Pipe Tally</h4>
                </div>    
                <div class="text-right">
                    <button class="btn btn-primary size-sm mb-2" onclick="add_pipe_tally()">
                        <i class="fa fa-plus"></i> Add
                    </button>
                </div>
                <table id="pipe_tally_table" class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item & Running Number</th>
                            <th>OD Inches</th>
                            <th>Item length</th>
                            <th>Cum. Length</th>
                            <th>Landing depth bottom</th>      
                            <th>Action</th>                         
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="activities">
        
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Document ID' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <!--<input type="text" name="doc_id" id="doc_id" class="w-100" value="<?php //echo $doc_id ?>"  readonly>-->
                    <?php 
                        if($job_id){
                            $doc_id_new = get_job_code($job_id); 
                             ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id_new['job_code']; ?>" readonly>
                    <?php } else{ ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" readonly>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Document Date' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVdate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Reference' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_ref_sub" id="doc_ref_sub" value="" class="doc_ref_sub w-100"  readonly>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                <div id="customer_div">
                    <input type="text" name="doc_customer" id="doc_customer" value="" class="doc_customer form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Contract/PO' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_po_num" id="doc_contract_po_num" value="" class="doc_contract_po_num form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'Currency' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_currency" id="doc_contract_currency" value="" class="doc_contract_currency form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'PO Number' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_po_number" id="doc_po_number" value="" class="doc_po_number form-control"  readonly>
                </div>
            </div>

        </div>

        <hr>

        <div class="table-crew">

            <div class="title">
                <h4>Activities</h4>
            </div>    
            <div class="text-right">
                <button class="btn btn-primary-new size-sm mb-2" onclick="add_shift()">
                    <i class="fa fa-plus"></i> Add Shift
                </button>
            </div>

            <div class="table-responsive">
                    
                    <table id="activity_shift_added_datatbl" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">S/N</th>
                                <!--Invoice Code-->
                                <th style="min-width: 43%"><?php echo 'Description'; ?></th>
                                <!--Details-->
                                <th style="min-width: 15%"><?php echo 'Type'; ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Date From' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Date To' ?></th>
                                 <!--Total Value-->
                                 <th style="min-width: 20%"><?php echo 'Check List' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo '#' ?></th>
                            
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8">No data to Show</td>
                            </tr>
                        </tbody>
                    </table>
            </div>


        </div>
        


    </div>
    <div class="tab-pane" id="communication">
        
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Document ID' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <?php 
                        if($job_id){
                            $doc_id_new = get_job_code($job_id); 
                             ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id_new['job_code']; ?>" readonly>
                    <?php } else{ ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" readonly>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Document Date' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVdate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Reference' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_ref_sub" id="doc_ref_sub" value="" class="doc_ref_sub w-100"  readonly>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                <div id="customer_div">
                    <input type="text" name="doc_customer" id="doc_customer" value="" class="doc_customer form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Contract/PO' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_po_num" id="doc_contract_po_num" value="" class="doc_contract_po_num form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'Currency' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_currency" id="doc_contract_currency" value="" class="doc_contract_currency form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'PO Number' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_po_number" id="doc_po_number" value="" class="doc_po_number form-control"  readonly>
                </div>
            </div>

        </div>

        <hr>

        <div class="table-crew">

            <div class="title">
                <h4>Client Communication</h4>
            </div>    
            <div class="table-responsive">
                    <div class="pull-right">
                        <button class="btn btn-default mb-2">
                            <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                    <table id="invoice_table" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 15%"><?php echo 'Code'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 43%"><?php echo 'Description'; ?></th>
                                <!--Details-->
                                <th style="min-width: 15%"><?php echo 'UOM'; ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Qty' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Value' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Discount' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Net Amount' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Comment' ?></th>
                            
                            </tr>
                        </thead>
                    </table>
                </div>

        </div>
        


    </div>
    <div class="tab-pane" id="dailyReport">
        
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Document ID' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <?php 
                        if($job_id){
                            $doc_id_new = get_job_code($job_id); 
                             ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id_new['job_code']; ?>" readonly>
                    <?php } else{ ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" readonly>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Document Date' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVdate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Reference' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_ref_sub" id="doc_ref_sub" value="" class="doc_ref_sub w-100"  readonly>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                <div id="customer_div">
                    <input type="text" name="doc_customer" id="doc_customer" value="" class="doc_customer form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Contract/PO' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_po_num" id="doc_contract_po_num" value="" class="doc_contract_po_num form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'Currency' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_currency" id="doc_contract_currency" value="" class="doc_contract_currency form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'PO Number' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_po_number" id="doc_po_number" value="" class="doc_po_number form-control"  readonly>
                </div>
            </div>

        </div>

        <hr>

        <div class="table-crew">

            <div class="title">
                <h4>Daily Report</h4>
            </div>    
            <div class="text-right">
                <button class="btn btn-default-new size-sm mb-2" onclick="add_daily_report()">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
            <div class="table-responsive">
                    
                    <table id="dailyReporttbl" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <!--Invoice Code-->
                                <th style="min-width: 50%"><?php echo 'Date From'; ?></th>
                                <!--Details-->
                                <th style="min-width: 15%"><?php echo 'Date To'; ?></th>
                                <!--Total Value-->
                                <th style="min-width: 10%"><?php echo 'Description' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Status' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 5%"><?php echo 'Confirmed' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 10%"><?php echo 'Action' ?></th>
                            
                            </tr>
                        </thead>
                    </table>
                </div>

        </div>
        


    </div>
    <div class="tab-pane" id="billing">
        
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Document ID' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <?php 
                        if($job_id){
                            $doc_id_new = get_job_code($job_id); 
                             ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id_new['job_code']; ?>" readonly>
                    <?php } else{ ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" readonly>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Document Date' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVdate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Reference' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_ref_sub" id="doc_ref_sub" value="" class="doc_ref_sub w-100"  readonly>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                <div id="customer_div">
                    <input type="text" name="doc_customer" id="doc_customer" value="" class="doc_customer form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Contract/PO' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_po_num" id="doc_contract_po_num" value="" class="doc_contract_po_num form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'Currency' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_currency" id="doc_contract_currency" value="" class="doc_contract_currency form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'PO Number' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_po_number" id="doc_po_number" value="" class="doc_po_number form-control"  readonly>
                </div>
            </div>

        </div>

        <hr>

        <div class="table-crew">

            <div class="title">
                <h4>Billing Log</h4>
            </div>    
            <div class="text-right">
                <button class="btn btn-primary-new size-sm mb-2" onclick="add_billing()">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
            <div class="table-responsive">
                    
                    <table id="billing_table" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%"><?php echo 'Code'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 20%"><?php echo 'Description'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 10%"><?php echo 'From Date'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 10%"><?php echo 'To Date'; ?></th>
                                <!--Total Value-->
                                <th style="min-width: 10%"><?php echo 'Value' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 10%"><?php echo 'Net Amount' ?></th>
                                 <!--Total Value-->
                                 <th style="min-width: 10%"><?php echo 'Status' ?></th>
                                 <!--Total Value-->
                                 <th style="min-width: 5%"><?php echo 'ConfirmedYN' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 20%"><?php echo 'Action' ?></th>
                            
                            </tr>
                        </thead>
                    </table>
                </div>

        </div>
    </div>
    <div class="tab-pane" id="billing_standard">
        
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Document ID' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <?php 
                        if($job_id){
                            $doc_id_new = get_job_code($job_id); 
                             ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id_new['job_code']; ?>" readonly>
                    <?php } else{ ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" readonly>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Document Date' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVdate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Reference' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_ref_sub" id="doc_ref_sub" value="" class="doc_ref_sub w-100"  readonly>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                <div id="customer_div">
                    <input type="text" name="doc_customer" id="doc_customer" value="" class="doc_customer form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Contract/PO' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_po_num" id="doc_contract_po_num" value="" class="doc_contract_po_num form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'Currency' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_currency" id="doc_contract_currency" value="" class="doc_contract_currency form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'PO Number' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_po_number" id="doc_po_number" value="" class="doc_po_number form-control"  readonly>
                </div>
            </div>

        </div>

        <hr>

        <div class="table-crew">

            <div class="title">
                <h4>Billing Log</h4>
            </div>    
            <div class="text-right">
                <button class="btn btn-primary-new size-sm mb-2" onclick="add_billing()">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
            <div class="table-responsive">
                    
                    <table id="billing_table_standard" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%"><?php echo 'Code'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 20%"><?php echo 'Description'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 10%"><?php echo 'From Date'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 10%"><?php echo 'To Date'; ?></th>
                                <!--Total Value-->
                                <th style="min-width: 10%"><?php echo 'Value' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 10%"><?php echo 'Net Amount' ?></th>
                                 <!--Total Value-->
                                 <th style="min-width: 10%"><?php echo 'Status' ?></th>
                                 <!--Total Value-->
                                 <th style="min-width: 5%"><?php echo 'ConfirmedYN' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 20%"><?php echo 'Action' ?></th>
                            
                            </tr>
                        </thead>
                    </table>
                </div>

        </div>
    </div>
    <div class="tab-pane" id="billing_modify">
        
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo 'Document ID' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <?php 
                        if($job_id){
                            $doc_id_new = get_job_code($job_id); 
                             ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id_new['job_code']; ?>" readonly>
                    <?php } else{ ?>
                            <input type="text" class="w-100" name="doc_id" id="doc_id" value="<?php echo $doc_id ?>" readonly>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Document Date' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVdate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Job Reference' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_ref_sub" id="doc_ref_sub" value="" class="doc_ref_sub w-100"  readonly>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                <div id="customer_div">
                    <input type="text" name="doc_customer" id="doc_customer" value="" class="doc_customer form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo 'Contract/PO' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_po_num" id="doc_contract_po_num" value="" class="doc_contract_po_num form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'Currency' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_contract_currency" id="doc_contract_currency" value="" class="doc_contract_currency form-control"  readonly>
                </div>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo 'PO Number' ?>
                    <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-pencil"></i></div>
                    <input type="text" name="doc_po_number" id="doc_po_number" value="" class="doc_po_number form-control"  readonly>
                </div>
            </div>

        </div>

        <hr>

        <div class="table-crew">

            <div class="title">
                <h4>Billing Log</h4>
            </div>    
            <div class="text-right">
                <button class="btn btn-primary-new size-sm mb-2" onclick="add_billing()">
                    <i class="fa fa-plus"></i> Add
                </button>
            </div>
            <div class="table-responsive">
                    
                    <table id="billing_table_modify" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%"><?php echo 'Code'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 20%"><?php echo 'Description'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 10%"><?php echo 'From Date'; ?></th>
                                <!--Invoice Code-->
                                <th style="min-width: 10%"><?php echo 'To Date'; ?></th>
                                <!--Total Value-->
                                <th style="min-width: 10%"><?php echo 'Value' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 10%"><?php echo 'Net Amount' ?></th>
                                 <!--Total Value-->
                                 <th style="min-width: 10%"><?php echo 'Status' ?></th>
                                 <!--Total Value-->
                                 <th style="min-width: 5%"><?php echo 'ConfirmedYN' ?></th>
                                <!--Total Value-->
                                <th style="min-width: 20%"><?php echo 'Action' ?></th>
                            
                            </tr>
                        </thead>
                    </table>
                </div>

        </div>
    </div>
    <div class="tab-pane" id="checkList">


        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Check List Details </h4><h4></h4></div><!--Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="open_Check_list_model_job()" class="btn btn-primary-new size-xs pull-right"><i
                        class="fa fa-plus"></i>&nbsp;Add <!--Add Assets-->
                </button>
            </div>
        </div>
        <br>
        <div class="table-responsive">
            <table class="<?php echo table_class(); ?>" id="job_checklist_table">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 30%"><?php echo 'Code';?></th><!--Code-->
                    <th style="min-width: 45%"><?php echo 'Name';?></th><!--Code-->
                    <th style="min-width: 10%"><?php echo 'Status';?>
                    <th style="min-width: 10%"><?php echo 'Action';?>
                
                    
                </tr>
                </thead>
                <tbody id="table_body">
            
                </tbody>
                <tfoot id="table_tfoot">

                </tfoot>
            </table>
        </div>
        <br>

     
        <hr>
        <!-- <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg next" onclick="op_job_attachments();"><?php echo $this->lang->line('common_save_and_next');?> </button>
        </div> -->
    </div>
    <div class="tab-pane" id="attachment">

            <div class="row" id="show_add_files_button">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Job Attachment </h4></div>
                        <div class="col-md-4">
                            <?php
                            // if ($header['closeStatus'] == 0) { ?>
                                <button type="button" onclick="show_add_file()" class="btn btn-primary pull-right"><i
                                        class="fa fa-plus"></i> Add Files
                                </button>
                            <?php // } ?>
                        </div>
                    </div>
                </div>                
            </div>
            <div class="row hide" id="add_attachemnt_show">
                <?php echo form_open_multipart('', 'id="opportunity_attachment_uplode_form" class="form-inline"'); ?>
                <div class="col-sm-10" style="margin-left: 3%">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="text" class="form-control" id="opportunityattachmentDescription"
                                   name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                            <input type="hidden" class="form-control" id="campaign_document_name" name="document_name"
                                   value="Job">
                            <input type="hidden" class="form-control" id="job_id"
                                   name="job_id"
                                   value="<?php echo $job_id ?>">
                        </div>
                    </div>
                    <div class="col-sm-8" style="margin-top: -8px;">
                        <div class="form-group">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                 style="margin-top: 8px;">
                                <div class="form-control" data-trigger="fileinput"><i
                                        class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                        class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                          class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span></span><span
                                          class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                         aria-hidden="true"></span></span><input
                                          type="file" name="document_file" id="document_file"></span>
                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                   data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                  aria-hidden="true"></span></a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-default" onclick="document_uplode()"><span
                                class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                        </form>
                    </div>
                </div>

            </div>
            <br>

            <div id="show_all_attachments"></div>
    </div>
    <div class="tab-pane" id="confirm">
        <div id="job_confirmation_area">

        </div>
    </div>
</div>
<?php } ?>

<!-- Modals section -->

<div aria-hidden="true" role="dialog" id="invoice_item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 99% !important;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
            <form role="form" id="invoice_item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="item_add_table">
                        <thead>
                        <tr>
                            <th><?php echo 'Item'; ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th><?php echo 'Item Code' ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th><?php echo 'UOM'; ?><?php required_mark(); ?></th>
                           
                            <th style="width:100px;"><?php echo 'Amount'; ?><?php required_mark(); ?></th>
                            <th style="width:80px;"><abbr title="Qty">Quantity</abbr></th>
                            <th style="width:80px;"><abbr title="Qty">Discount</abbr></th>
                            <th style="width:80px;"><abbr title="Qty">Net Amount</abbr></th>                          
                            <!--Tax-->
                            <th><?php echo $this->lang->line('common_comment'); ?>  </th><!--Comment-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_item_tbl()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="">
                                <input type="text" class="form-control search input-mini f_search" name="search[]"
                                       id="f_search_1"
                                       placeholder=""
                                       onkeydown="remove_item_all_description(event,this)"><!--Item Id-->

                                <!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                <input type="hidden" class="form-control itemcatergory" name="itemcatergory[]">
                            </td>
                            <td>
                                <input type="text" name="itemSystemCode[]"
                                           class="form-control itemSystemCode" required readonly>
                            </td>

                            <td>
                                <input type="text" name="itemUOM[]"
                                           class="form-control itemUOM" required readonly>
                            </td>
                           
                            <td>
                                <input type="text" name="amount[]"
                                           class="form-control amount"  onchange="change_item_claculation($(this))" required>
                            </td>

                            <td>
                                <input type="text" name="quantity[]"
                                           class="form-control quantity" onchange="change_item_claculation($(this))" required>
                            </td>

                            <td>
                                <input type="text" name="discount[]"
                                           class="form-control discount"  onchange="change_item_claculation($(this))" required>
                            </td>
                            
                            <td>
                                <input type="text" name="netAmount[]"
                                           class="form-control netAmount" required>
                               
                            </td>

                            <td>
                                <input type="text" name="comment[]"
                                           class="form-control comment" required>
                               
                            </td>
        
                            <td class="remove-td" style="vertical-align: middle;text-align: center">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="saveInvoiceItemDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                    </button><!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="invoice_item_detail_edit_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 99% !important;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
               
            </div>
            <form role="form" id="invoice_item_detail_edit_form" class="form-horizontal">
                <input type="hidden" name="id" id="item_tbl_id" value="" />
                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="item_add_edit_table">
                        <thead>
                        <tr>
                            <th><?php echo 'Item'; ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th><?php echo 'Item Code' ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th><?php echo 'UOM'; ?><?php required_mark(); ?></th>
                           
                            <th style="width:100px;"><?php echo 'Amount'; ?><?php required_mark(); ?></th>
                            <th style="width:80px;"><abbr title="Qty">Quantity</abbr></th>
                            <th style="width:80px;"><abbr title="Qty">Discount</abbr></th>
                            <th style="width:80px;"><abbr title="Qty">Net Amount</abbr></th>                          
                            <!--Tax-->
                            <th><?php echo $this->lang->line('common_comment'); ?>  </th><!--Comment-->
                            <th style="width: 40px;">
                              
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="">
                                <input type="text" class="form-control search input-mini f_search_edit" name="search[]"
                                       id="f_search_edit"
                                       placeholder=""
                                       onkeydown="remove_item_all_description(event,this)"><!--Item Id-->

                                <!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID">
                                <input type="hidden" class="form-control itemcatergory" name="itemcatergory">
                            </td>
                            <td>
                                <input type="text" name="itemSystemCode"
                                           class="form-control itemSystemCode" required readonly>
                            </td>

                            <td>
                                <input type="text" name="itemUOM"
                                           class="form-control itemUOM" required readonly>
                            </td>
                           
                            <td>
                                <input type="text" name="amount"
                                           class="form-control amount"  onchange="change_item_claculation($(this))" required readonly>
                            </td>

                            <td>
                                <input type="text" name="quantity"
                                           class="form-control quantity" onchange="change_item_claculation($(this))" required>
                            </td>

                            <td>
                                <input type="text" name="discount"
                                           class="form-control discount"  onchange="change_item_claculation($(this))" required >
                            </td>
                            
                            <td>
                                <input type="text" name="netAmount"
                                           class="form-control netAmount" required readonly>
                               
                            </td>

                            <td>
                                <input type="text" name="comment"
                                           class="form-control comment" required>
                               
                            </td>
        
                            <td class="remove-td" style="vertical-align: middle;text-align: center">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="saveInvoiceItemDetailEdit()"><?php echo $this->lang->line('common_save_change'); ?>
                    </button><!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" id="crew_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>

            <div class="modal-body">
                <div class="row pt-0">
                    <div class="col-sm-12">
                        <div class="supply_master_style">
                            <ul class="nav nav-tabs" id="main-tabs">
                                <li class="btn-default-new size-sm tab-style-one mr-1 active"><a href="#crew_contract" data-toggle="tab">From Selected Contract</a></li>
                                <li class="btn-default-new size-sm tab-style-one"><a href="#crew_common" data-toggle="tab">From Common</a></li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="crew_contract">
                                    
                                <form role="form" id="crew_contract_select_form" class="form-horizontal">
                                
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-sm-6 col-md-4 btn-w-100">
                                                <label for="group_crew"><?php echo $this->lang->line('common_group');?></label><!--Financial Period-->
                                                <div id="customer_div">
                                                    <?php echo form_dropdown('group_crew',array(), '', 'class="form-control" id="group_crew"  multiple="multiple" onchange="change_crew_group(1)" '); ?>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-md-7">
                                                <label for="group_crew"><?php echo 'Assign Crew Grouping'?></label>
                                                <button type="button" class="btn btn-primary size-xs" onclick="add_job_group(3)"><i class="fa fa-plus"></i></button> 
                                                <div id="customer_div">
                                                    <?php echo form_dropdown('group_jobcrew',array() , '', 'class="form-control select2" id="group_jobcrew" onchange="" '); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="<?php echo table_class(); ?>" id="crew_select_contract_table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employee Code</th>
                                                    <th>Name</th>
                                                    <th>Employee Desigantion</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            
                                                </tbody>
                                            </table>
                                        </div>    
                                    </div>
                                    <div class="modal-footer">
                                        <!--<button data-dismiss="modal" class="btn btn-primary size-sm"
                                                type="button" onclick="crew_select_add()"><?php //echo $this->lang->line('common_add'); ?> </button>-->
                                        <button data-dismiss="modal" class="btn btn-default size-sm"
                                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                                    
                                    </div>
                                </form>

                            </div>
                            <div class="tab-pane" id="crew_common">
                                <form role="form" id="crew_contract_common_form" class="form-horizontal">                                
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="<?php echo table_class(); ?>" id="crew_select_common_table">
                                                <thead>
                                                <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Employee Code</th>
                                                <th>Employee Desigantion</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                       <!-- <button data-dismiss="modal" class="btn btn-primary size-sm"
                                                type="button" onclick="crew_select_add_common()"><?php //echo $this->lang->line('common_add'); ?> </button>-->
                                        <button data-dismiss="modal" class="btn btn-default size-sm"
                                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                                    
                                    </div>
                                
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="assets_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
            <div class="modal-body">
                <div class="row pt-0">
                    <div class="col-sm-12">
                        <div class="supply_master_style">
                            <ul class="nav nav-tabs" id="main-tabs">
                                <li class="btn-default-new size-sm tab-style-one mr-1 active"><a href="#crew_contract_asset" data-toggle="tab">From Selected Contract</a></li>
                                <li class="btn-default-new size-sm tab-style-one"><a href="#crew_common_asset" data-toggle="tab" onclick="load_common_assest()">From Common</a></li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="crew_contract_asset">
                                <div class="row">
                                    <div class="col-sm-4 col-md-4 btn-w-100">
                                        <label for="group_asset"><?php echo $this->lang->line('common_group');?></label><!--Financial Period-->
                                        <div id="customer_div">
                                            <?php echo form_dropdown('group_asset',array(), '', 'class="form-control" id="group_asset"  multiple="multiple" onchange="change_crew_group(2)" '); ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="col-sm-4">
                                    <label for="group_crew"><?php echo 'Assign Crew Grouping'?></label>
                                    <button type="button" class="btn btn-danger" onclick="add_job_group(3)"><i class="fa fa-plus"></i></button> 
                                    <div id="customer_div">
                                        <?php // echo form_dropdown('group_jobcrew',array() , '', 'class="form-control select2" id="group_jobcrew" onchange="" '); ?>
                                    </div>
                                </div> -->

                                <form role="form" id="crew_detail_form" class="form-horizontal">
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-condensed" id="asset_add_table_contract">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Asset Code</th>
                                                    <th>Name</th>
                                                    <th>Reference</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                </form>
                            </div>
                            <div class="tab-pane" id="crew_common_asset">
                                <!-- <form role="form" id="crew_detail_form" class="form-horizontal">
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-condensed" id="asset_add_table_common">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Asset Code</th>
                                                    <th>Name</th>
                                                    <th>Reference</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button data-dismiss="modal" class="btn btn-default"
                                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button>
                                    
                                    </div>
                                </form> -->
                                <div class="row" style="margin: 6px 0px;">
                                        <div class="col-md-6">&nbsp;</div>
                                        <div class="col-md-6">
                                            <div class="box-tools">
                                                <div class="has-feedback">
                                                    <input name="searchOrder_asset" type="text" class="form-control input-sm"
                                                        placeholder="Search"
                                                        id="searchOrder_asset" onkeyup="search_asset_common()">
                                                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <div class="row">
                                        <div class="col-sm-12">
                                            
                                            <div id="assign_common_asset"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="row pull-right">
                                    <div class="col-sm-12 ">
                                    
                                        <button class="btn btn-primary-new size-sm" onclick="assign_job_common_assets()">Assign</button>
                                    </div>

                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    
                    <button data-dismiss="modal" class="btn btn-default size-sm"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button>
                
                </div> -->
            </div>    
            
        </div>
    </div>
</div>


<div class="modal fade" id="textInputModal" role="dialog"  style="display: none;z-index:1060">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="textInputModalLabel">NPT Justification</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="npt_form" class="form-horizontal">
                    <!-- <input type="text" id="textInput" > -->
                    <textarea id="npt_comment" name="npt_comment" class="form-control"></textarea>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12 pull-right">
                    <button class="btn btn-primary-new size-sm"  id="saveModalButton">Save</button>
                </div>

            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" id="visitor_log_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?> Visitor Log </h4>
                <!--Add Item Detail-->
            </div>
            
                <form role="form" id="visitor_log_detail_form" class="form-horizontal">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" id="activity_add_table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Full Name</th>
                                    <th>Company</th>
                                    <th>Position</th>
                                    <th>Purpose of Visit</th>
                                    <th>Mobile No.</th>
                                    <th>Medication</th>
                                    <th>H2S Validity</th>
                                    <th>Rig Safety Briefing</th>
                                    <th>Proper PPE in Use</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="date" id="fromDate" class="form-control" name="fromDate" value="<?php echo $default_date2 ?>" onchange="change_dateFrom_activity($(this))">
                                        </td>
                                        <td>
                                            <input type="text" name="fullName"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="fullCompany"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="position"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="purposeVisit"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="mobileNumber"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="medication"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="H2SValidity"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="rigSafetyBriefing"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="text" name="properPPE"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="time" id="time_in" class="form-control" name="time_in" value="<?php echo $default_time ?>" onchange="change_dateFrom_activity($(this))">
                                        </td>
                                        <td>
                                            <input type="time" id="time_out" class="form-control" name="time_out" value="<?php echo $default_time ?>" onchange="CCchange_dateFrom_activity($(this))">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveVisitorLogDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                        
                    </div>
                </form>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="fuel_receipt_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content col-md-10">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Fuel Receipt</h4>
                <!--Add Item Detail-->
            </div>
                <form role="form" id="fuel_recived_detail_form">
                    <div class="modal-body">                       
                        <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="fuel_add">Doc Num</label>
                                    <input  type="number" name="docunumber"  class="form-control" required>
                                    
                                </div>
                             

                                <div class="form-group col-md-6">
                                    <label>Date <?php required_mark(); ?></label>
                                    <div class="input-group-addon bg-white">
                                    <input type="date" id="fromDate" class="form-control" name="startdate" value="<?php echo $default_date2 ?>" onchange="change_dateFrom_activity($(this))">
                                         </div>
                                </div>
                            </div>
 

                <div class="row"  id="Employee_text"> 
                            <div class="form-group col-sm-12">
                                <label>Received By</label>
                            </div>
                            <div class="form-group col-sm-8">
                                <input type="text" name="reuser" id="reuser" class="form-control" placeholder="Employee">
                            </div>
                            <div class="col-sm-1 search_cancel" style="width: 3%;">
                                <i class="fa fa-link" onclick="linkemployee()" title="Link to Employee" aria-hidden="true"  style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                            </div>
                </div>

                <div class="row hide" id="linkemployee_text">
                        <div class="form-group col-sm-12">
                            <label class="title">Received By</label>
                        </div>
                        <div class="form-group col-sm-8">
                                <?php echo form_dropdown('linkemployee', $loademp, '', 'class="form-control select2" id="linkemployee"  '); ?>
                        </div>
                        <div class="col-sm-1 search_cancel" style="width: 3%;">
                                <i class="fa fa-external-link" onclick="unlinkemployee()" title="Link to Employee" aria-hidden="true" style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                        </div>
                </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="fuel_add">Narration</label>
                                      <input type="text" name="rnarration"   class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="fuel_add">Fuel Type</label>
                                    <?php echo form_dropdown('fuelusageID', $fuelty, '', 'class="form-control select2 fuelDropdown" id="fuelusageID"'); ?>
                                </div>

                            
                                <div class="form-group col-md-4">
                                    <label for="fuel_add">UoM</label>
                                    <?php echo form_dropdown('UOMid', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" id="UOMid"'); ?>
                                </div> 
                            
                                <div class="form-group col-md-4">
                                    <label for="fuel_add">Qty</label>
                                        <input type="number" name="qtynum"  class="form-control" required>
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                         <input type="hidden" name="transid" value="1" class="form-control" required>
                                </div>
                            </div>
                   
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary-new size-sm" type="button"
                            onclick="savefuelreceiptDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                        
                    </div>
                </form>

                
            </div>

        </div>
    </div>
   </div>


<!--Fuel Issuesd -->


<div aria-hidden="true" role="dialog" id="fuel_Issue_detail_model" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content col-md-10">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Fuel Issue</h4>
                <!--Add Item Detail-->
            </div>
                <form role="form" id="fuel_Issue_detail_form">
                <div class="modal-body">                       
                        <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="fuel_add">Doc Num</label>
                                    <input  type="number" name="docunumber"  class="form-control" required>
                                    
                                </div>
                             

                                <div class="form-group col-md-6">
                                    <label>Date <?php required_mark(); ?></label>
                                    <div class="input-group-addon bg-white">
                                    <input type="date" id="fromDate" class="form-control" name="startdate" value="<?php echo $default_date2 ?>" onchange="change_dateFrom_activity($(this))">
                                         </div>
                                </div>
                            </div>
                <div class="row"  id="Employee_text_issue"> 
                            <div class="form-group col-sm-12">
                                <label>Issued By</label>
                            </div>
                            <div class="form-group col-sm-8">
                                <input type="text" name="reuser" id="fuelemployee" class="form-control" placeholder="Employee">
                            </div>
                            <div class="col-sm-1 search_cancel" style="width: 3%;">
                                <i class="fa fa-link" onclick="rlinkemployee()" title="Link to Employee" aria-hidden="true"  style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                            </div>
                </div>
       
                <div class="row hide" id="linkemployee_text_issue">
                        <div class="form-group col-sm-12">
                            <label class="title">Issued By</label>
                        </div>
                        <div class="form-group col-sm-8">
                                <?php echo form_dropdown('linkemployee', $loademp, '', 'class="form-control select2" id="linkemployee"'); ?>
                        </div>
                        <div class="col-sm-1 search_cancel" style="width: 3%;">
                                <i class="fa fa-external-link" onclick="runlinkemployee()" title="Link to Employee" aria-hidden="true" style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
                        </div>
                </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="fuel_add">Narration</label>
                                      <input type="text" name="rnarration"   class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="fuel_add">Fuel Type</label>
                                    <?php echo form_dropdown('fuelusageID', $fuelty, 'Each', 'class="form-control select2 fuelDropdown fuelDropdown_fuel" '); ?>
                                </div>

                            
                                <div class="form-group col-md-4">
                                    <label for="fuel_add">UoM</label>
                                    <?php echo form_dropdown('UOMid', $umo_arr, 'Each', 'class="form-control select2 umoDropdown umoDropdown_fuel input-mini" '); ?>
                                </div>
                            
                            
                                <div class="form-group col-md-4">
                                    <label for="fuel_add">Qty</label>
                                        <input type="number" name="qtynum"  class="form-control" required>
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                         <input type="hidden" name="transid" value="2" class="form-control" required>
                                </div>
                            </div>
                   
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary-new size-sm" type="button"
                            onclick="savefuelissueDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                        
                    </div>
                </form>           
            </div>
        </div>
    </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="pipe_tally_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl px-xs-0 px-md-2" style="width:100%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?> Pipe Tally </h4>
                <!--Add Item Detail-->
            </div>
            
                <form role="form" id="pipe_tally_detail_form" class="form-horizontal">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" id="activity_add_table">
                                <thead>
                                <tr>
                                    <th>Item & Running Number</th>
                                    <th>OD Inches</th>
                                    <th>Item length</th>
                                    <th>Cum. Length</th>
                                    <th>Landing depth bottom</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>    
                                        <td>
                                            <input type="text" name="runningNumber"
                                                    class="form-control" required>
                                        </td>                               
                                        <td>
                                            <!--<input type="text" name="ODInches"
                                                    class="form-control" required>-->
                                                    <?php echo form_dropdown('ODInches', $umo_arr, 'Each', 'class="form-control select2 umoDropdown input-mini" onchange="convertPrice_DO(this)" required'); ?>
                                        </td>
                                        <td>
                                            <input type="number" name="itemLength"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="number" name="cumLength"
                                                    class="form-control" required>
                                        </td>
                                        <td>
                                            <input type="number" name="landingDepthBottom"
                                                    class="form-control" required>
                                        </td>
                                    </tr>                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary-new size-sm" type="button"
                            onclick="savePipeTallyDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                        
                    </div>
                </form>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="activity_detail_modal_crew" class="modal fade" style="display: none; z-index:999999">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
            
                <form role="form" id="activity_detail_crew_form" class="form-horizontal">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <div class="row">
                                <div class="col-sm-6 col-md-4 btn-w-100">
                                        <label for="group_crew"><?php echo $this->lang->line('common_group');?></label><!--Financial Period-->
                                        <div id="customer_div">
                                            <?php echo form_dropdown('group_activity_crew',array(), '', 'class="form-control" id="group_activity_crew"  multiple="multiple" onchange="change_crew_group(3)" '); ?>
                                        </div>
                                </div>
                            </div>
                                
                            <table class="table table-bordered table-condensed" id="activity_crew_add_table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Employee Name</th>
                                    <th>Designation</th>
                                    <th style="width: 40px;">
                                        <!-- <button type="button" class="btn btn-primary btn-xs"
                                                onclick="add_more_activity_tbl()"><i
                                                    class="fa fa-plus"></i></button> -->
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <!-- <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveCrewForActivity()"><?php echo $this->lang->line('common_add'); ?> -->
                        
                    </div>
                </form>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="activity_shift_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-md" >
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
            
                <form role="form" id="activity_shift_form">
                    <div class="modal-body">
                            <input type="hidden" name="edit_shift" id="edit_shift" />
                        
                            <div class="row">
                                <div class="form-group col-md-6 milee">
                                    <label><?php echo 'Shift Start' ?>
                                        <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                                        <div class="input-group-addon bg-white">                                           
                                            <input type="text" id="shiftFromDate" name="shiftFromDate" class="form-control" value="" >
                                        </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label><?php echo 'Shift End'  ?>
                                        <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                                    <div class="input-group-addon bg-white">
                                        <!--<input type="datetime-local" id="shiftToDate" name="shiftToDate"  class="form-control" value="" onchange="">-->
                                        <input type="text" id="shiftToDate" name="shiftToDate" class="form-control" value="" >
                                    </div>
                                </div>

                                <div class="form-group col-md-12 pb-20">
                                    <label for="shift_notes"><?php echo 'Special Notes';?></label><!--Financial Period-->
                                    <div id="">
                                        <textarea class="form-control" name="shift_notes" id="shift_notes"></textarea>
                                    </div>
                                </div>

                            </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveShiftActivityDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                        
                    </div>
                </form>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="employee_schedule_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo 'Search for Overlapping Tasks' ?> </h4>
                <!--Add Item Detail-->
            </div>
            
                <form role="form" id="employee_schedule_form" class="form-horizontal">
                    <input type="hidden" id="es_empID" value="" >

                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-3 form-group" style="padding-right:20px;">
                            <label>Start Date / Time</label>
                            <input type="datetime-local" class="form-control" id="start_date_time" >
                        </div>
                        <div class="col-md-3 form-group">
                            <label>End Date / Time</label>
                            <input type="datetime-local" class="form-control" id="end_date_time" >
                        </div>
                        <div class="col-md-3">
                            <a class="btn btn-primary-new" onclick="search_task_ov()"><i class="fa fa-magnifer"></i> Search</a>
                        </div>
                    </div>

                    <div class="modal-body">
                        <table class="table table-bordered table-condensed" id="employee_schedule_table">
                            <thead>
                            <tr>
                                <th>Job ID</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Job Code</th>
                                <th>Job Name</th>
                                <th>Job Description</th>
                                <th>Date From</th>
                                <th>Date To</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                        
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        
                    </div>
                </form>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="activity_shift_data_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">                
                        <div class="supply_master_style">
                            <ul class="nav nav-tabs" id="main-tabs">
                                <li class="btn-default-new size-sm tab-style-one mr-1 active"><a href="#Activity" data-toggle="tab">Activity</a></li>
                                <li class="btn-default-new size-sm tab-style-one"><a href="#Crew" data-toggle="tab">Crew</a></li>
                            </ul>
                        </div>
                        <br>

                        <div class="tab-content">
                            <div class="tab-pane active" id="Activity">
                                
                                    <div class="text-right">
                                        <button class="btn btn-default mb-2" onclick="add_activities()">
                                            <i class="fa fa-plus"></i> Add
                                        </button>
                                    </div>

                                    <input type="hidden" id="shift_id" name="shift_id" value="" />
                                    <div class="table-responsive">
                                        <table id="activity_added_datatbl" class="<?php echo table_class(); ?>">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 5%">S/N</th>
                                                    <!--Invoice Code-->
                                                    <th style="min-width: 20%"><?php echo 'Description'; ?></th>
                                                    <!--Details-->
                                                    <th style="min-width: 10%"><?php echo 'Type'; ?></th>
                                                    <!--Total Value-->
                                                    <th style="min-width: 10%"><?php echo 'Date From' ?></th>
                                                    <!--Total Value-->
                                                    <th style="min-width: 10%"><?php echo 'Date To' ?></th>
                                                    <!--Total Value-->
                                                    <th style="min-width: 8%"><?php echo 'Days / Hours' ?></th>
                                                    <!--Total Value-->
                                                    <th style="min-width: 8%"><?php echo 'is Standby' ?></th>
                                                    <!--Total Value-->
                                                    <th style="min-width: 8%"><?php echo 'is NPT' ?></th>
                                                    <!--Total Value-->
                                                    <th style="min-width: 10%"><?php echo '#' ?></th>
                                                
                                                
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="8">No data to Show</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="total-area pull-right" style="display:flex;margin-top:25px;">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputPassword3" class="col-sm-6 control-label">Total</label><!--Comments-->
                                                <!-- <input type="text" class="col-sm-6 form-control" name="total_hours" id="total_hours" /> -->
                                                <p class="col-sm-12" id="total_hours"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputPassword3" class="col-sm-6 control-label">Stand by</label><!--Comments-->
                                                <!-- <input type="text" class="form-control" name="total_standby_hours" id="total_standby_hours" /> -->
                                                <p class="col-sm-12" id="total_standby_hours"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="inputPassword3" class="col-sm-6 control-label">NPT</label><!--NPT-->
                                                <p class="col-sm-12" id="total_npt_hours"></p>
                                            </div>
                                        </div>
                                    </div>
                                
                            </div>
                            <div class="tab-pane" id="Crew">
                                    
                                        <div class="text-right">
                                            <button class="btn btn-default mb-2" onclick="add_activities_crew()">
                                                <i class="fa fa-plus"></i> Add
                                            </button>
                                        </div>
                                  

                                    <input type="hidden" id="shift_id" name="shift_id" value="" />
                                    <div class="table-responsive">
                                       
                                        <table id="activity_added_crew_datatbl" class="<?php echo table_class(); ?>">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 5%">S/N</th>
                                                    <!--Invoice Code-->
                                                    <th style="min-width: 43%"><?php echo 'Employee Code'; ?></th>
                                                    <!--Details-->
                                                    <th style="min-width: 15%"><?php echo 'Employee Name'; ?></th>
                                                    <!--Total Value-->
                                                    <th style="min-width: 5%"><?php echo 'Designation' ?></th>
                                                    <!--Total Value-->
                                                    <!--Total Value-->
                                                    <th style="min-width: 5%"><?php echo '#' ?></th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="8">No data to Show</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>    
    </div>
</div>

<div aria-hidden="true" role="dialog" id="activity_detail_edit_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
            
                <form role="form" id="activity_detail_edit_form" class="form-horizontal">
                    <input type="hidden" name="edit_acctivity_id" id="edit_acctivity_id" value="" />
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" id="activity_add_table">
                                <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Date From</th>
                                    <th>Date To</th>
                                    <th>Is Standby</th>
                                    <th>Is NPT</th>
                                    <th>Action</th>
                                    <th style="width: 40px;">
                                    
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td>
                                        <input type="text" name="edit_description" id="edit_description"
                                                class="form-control itemSystemCode" required>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('edit_activity_type', $activity_type_arr, '', 'class="form-control select2" id="edit_activity_type"'); ?>
                                    </td>
                                    <td>
                                        <input type="datetime-local" id="edit_fromDate" class="form-control" name="edit_fromDate" value="<?php echo $default_date ?>" onchange="change_dateFrom_activity($(this))">
                                    </td>
                                    <td>
                                        <input type="datetime-local" id="edit_toDate" class="form-control" name="edit_toDate" value="<?php echo $default_date ?>" onchange="change_dateFrom_activity($(this))">
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <input type="checkbox" class="edit_isStandby" name="edit_isStandby" id="edit_isStandby" value="1" onchange="">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <input type="checkbox" class="edit_isNPT" name="edit_isNPT" id="edit_isNPT" value="1"  onchange="">
                                        </div>
                                    </td>
                                    <td class="remove-td" style="vertical-align: middle;text-align: center"></td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveInvoiceActivityDetailEdit()"><?php echo $this->lang->line('common_save_change'); ?>
                        
                    </div>
                </form>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="activity_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
            
                <form role="form" id="activity_detail_form" class="form-horizontal">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed" id="activity_add_new_table">
                                <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Date From</th>
                                    <th>Date To</th>
                                    <th>Is Standby</th>
                                    <th>Is NPT</th>
                                    <th>Action</th>
                                    <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs"
                                            onclick="add_more_activity_tbl()"><i
                                                class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td>
                                        <input type="text" name="description[]"
                                                class="form-control itemSystemCode" required>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('activity_type[]', $activity_type_arr, '', 'class="form-control select2" id="activity_type"'); ?>
                                    </td>
                                    <td>
                                        <input type="text" id="fromDate" class="form-control d_from" name="fromDate[]" value="<?php echo $default_date ?>" onchange="change_dateFrom_activity($(this))">
                                    </td>
                                    <td>
                                        <input type="text" id="toDate" class="form-control d_to" name="toDate[]" value="<?php echo $default_date ?>" onchange="change_dateFrom_activity($(this))">
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <input type="hidden" name="isStandby[]" id="hid_isStandby" class="hid_isStandby" value=''>
                                            <input type="checkbox" class="isStandby" value="1" onchange="checkbox_changed($(this),'isStandby')">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <input type="hidden" name="isNpt[]" id="hid_isNPT"  class="hid_isNPT" value=''>
                                            <input type="checkbox" class="isNPT" value="1"  onchange="checkbox_changed($(this),'isNpt')">
                                            
                                        </div>
                                        <div class="text-center">
                                            <i rel="tooltip" class="fa fa-bars" style="display: none;" id="notifyicon"></i>
                                            <input type="hidden" name="NPTcomment" id="NPTcomment"  class="NPTcomment" >
                                        </div>
                                    </td>
                                    <td class="remove-td" style="vertical-align: middle;text-align: center"></td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveInvoiceActivityDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                        
                    </div>
                </form>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="activity_shift_data_modal_x" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>            

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
                            <button class="btn btn-default mb-2" onclick="add_activities()">
                                <i class="fa fa-plus"></i> Add
                            </button>
                        </div>

                        <input type="hidden" id="billing_id" name="billing_id" value="" />
                        <div class="table-responsive">
                            <table id="billing_added_datatbl_x" class="<?php echo table_class(); ?>">
                                <thead>
                                    <tr>
                                        <th style="min-width: 5%">S/N</th>
                                        <!--Invoice Code-->
                                        <th style="min-width: 25%"><?php echo 'Description'; ?></th>
                                        <!--Details-->
                                        <th style="min-width: 15%"><?php echo 'Type'; ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 10%"><?php echo 'Date From' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 10%"><?php echo 'Date To' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 10%"><?php echo 'Days / Hours' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'is Standby' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'is NPT' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 15%"><?php echo '#' ?></th>
                                    
                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8">No data to Show</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="daily_report_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
                <form role="form" id="daily_report_form">
                    <div class="modal-body">
                        <input type="hidden" name="daily_report_id" id="daily_report_id" />                        
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label><?php echo 'Report Start' ?>
                                        <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                                        <div class="input-group-addon bg-white">
                                            <input type="datetime-local" id="reportFromDate" name="reportFromDate"  class="form-control" value="" onchange="change_date_populate_fn($(this),'dailyReport')">
                                        </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label><?php echo 'Report End'  ?>
                                        <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                                    <div class="input-group-addon bg-white">
                                        <input type="datetime-local" id="reportToDate" name="reportToDate"  class="form-control" value="" onchange="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="shift_notes"><?php echo 'Description';?></label><!--Financial Period-->
                                    <div id="">
                                        <textarea class="form-control" name="description" id="description"></textarea>
                                    </div>
                                </div>
                            </div>                        
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveDailyReportDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                        
                    </div>
                </form>

                
            </div>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="add_billing_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
                    <form role="form" id="period_billing_form">
                        <div class="modal-body">
                            <input type="hidden" name="final_billing_id" id="final_billing_id" value="" />
                        
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label><?php echo 'Report Start' ?>
                                        <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                                        <div class="input-group-addon bg-white">
                                            <input type="datetime-local" id="billingFromDate" name="reportFromDate"  class="form-control" value="" onchange="">
                                        </div>
                                </div>

                                <div class="form-group col-sm-6">
                                    <label><?php echo 'Report End'  ?>
                                        <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                                    <div class="input-group-addon bg-white">
                                        <input type="datetime-local" id="billingToDate" name="reportToDate"  class="form-control" value="" onchange="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="shift_notes"><?php echo 'Description';?></label><!--Financial Period-->
                                    <div id="">
                                        <textarea class="form-control" name="description" id="billingDescription"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="shift_notes"><?php echo 'Code';?></label><!--Financial Period-->
                                    <div id="">
                                        <input type="text" name="code" id="billing_code"
                                           class="form-control discount">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                    type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                            <button class="btn btn-primary-new size-sm" type="button"
                                onclick="saveBillingPeriodDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                            
                        </div>
                    </form>

                
            </div>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="add_billing_data" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
            

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <button class="btn btn-primary-new size-sm mb-2" onclick="add_billing_data_activity()">
                                    <i class="fa fa-plus"></i> From Activity
                                </button>
                            </div>

                            <input type="hidden" id="billing_header_id" name="billing_header_id" value="" />

                            <table id="billing_added_datatbl" class="<?php echo table_class(); ?>">
                                <thead>
                                    <tr>
                                        <th style="min-width: 5%">S/N</th>
                                        <!--Invoice Code-->
                                        <th style="min-width: 43%"><?php echo 'Description'; ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Date From' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Date To' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Days / Hours' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'is Standby' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'is NPT' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Price List' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Rate' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Total' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo '#' ?></th>
                                    
                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8">No data to Show</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="add_billing_data_modify" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
            

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <button class="btn btn-primary-new size-sm mb-2" onclick="add_billing_data_activity_modify()">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </div>

                            <input type="hidden" id="billing_header_id_modify" name="billing_header_id_modify" value="" />

                            <table id="billing_added_datatbl_modify" class="<?php echo table_class(); ?>">
                                <thead>
                                    <tr>
                                        <th style="min-width: 5%">S/N</th>
                                        <!--Invoice Code-->
                                        <th style="min-width: 43%"><?php echo 'Description'; ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Date From' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Date To' ?></th>
                                        <th style="min-width: 5%"><?php echo 'Moving Cost' ?></th>
                                        <th style="min-width: 5%"><?php echo 'Additional Rental Cost' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Days / Hours' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'is Standby' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'is NPT' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Price List' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Rate' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Total' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo '#' ?></th>
                                    
                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8">No data to Show</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="add_billing_data_modify_add" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_sales_add_item_detail');?><!--Add Item Detail--></h4>
            </div>
            <form role="form" id="item_detail_form_billing_modify" class="form-horizontal">
                <div class="modal-body" style="overflow-x: scroll;">
                    <table class="table table-bordered table-striped table-condesed" id="item_add_table_modify" style="table-layout: fixed">
                        <thead>
                        <tr>
                            <th style="width: 200px;">Description </th>
                            <th style="width: 200px;">Date From <?php required_mark(); ?></th>
                            <th style="width: 200px;">Date To <?php required_mark(); ?></th>
                            <th style="width: 200px;">Moving Cost <?php required_mark(); ?></th>
                            <th style="width: 200px;">Additional Rental Cost <?php required_mark(); ?></th>
                            <th style="width: 150px;">Price List <?php required_mark(); ?></th>                         
                            <th style="width: 150px;">Is Standby <?php required_mark(); ?> </th>
                            <th style="width: 150px;">Is NPT <?php required_mark(); ?></th>
                            <th style="width: 100px;">Hours/Mins <?php required_mark(); ?></th>
                            <th style="width: 200px;">Qty <?php required_mark(); ?></th>
                            <th style="width: 200px;">Rate <?php required_mark(); ?></th>
                            <th style="width: 200px;"> Total <?php required_mark(); ?></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_item_modify_billing()">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text"  name="description[]" class="form-control description"/>
                            </td>
                            <td>
                               
                                <input type="text" name="datefrom_modify[]" class="form-control datefrom_modify" value="">
                            </td>
                            <td>
                                <input type="text" name="dateto_modify[]" class="form-control dateto_modify" value="">
                            </td>
                            <td>
                                <input type="number"  name="movingcost[]" class="form-control movingcost"/>
                            </td>
                            <td>
                                <input type="number"  name="Additionalcost[]" class="form-control Additionalcost"/>
                            </td>
                            <td>
                                <?php echo form_dropdown('pID[]',  $contract_price, '', 'class="pID form-control select2" onchange="change_pricing_billing_modify($(this),1)" id="pID_1"'); ?>
            
                            </td>
                            <td>
                                <div class="text-center">
                                    <input type="hidden" name="isStandby_modify[]" id="hid_isStandby_modify" class="hid_isStandby_modify" value=''>
                                    <input type="checkbox" class="isStandby_modify" value="1" onchange="checkbox_changed_modify($(this),'isStandby_modify')">
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <input type="hidden" name="isNpt_modify[]" id="hid_isNPT_modify"  class="hid_isNPT_modify" value=''>
                                    <input type="checkbox" class="isNPT_modify" value="1"  onchange="checkbox_changed_modify($(this),'isNpt_modify')">
                                    
                                </div>
                                
                            </td>
                            <td>
                                <input type="number"  name="min_modify[]" class="form-control min_modify" id="min_modify_1" onchange="change_requested_modify_qty($(this))"/>
                            </td>

                            <td>
                                <input type="text"  name="qty_modify[]" class="form-control qty_modify" id="qty_modify_1" disabled required />
                            </td>
                            <td>
                                <input type="text"  name="rate_modify[]" class="form-control rate_modify" id="rate_modify_1" disabled required />
                            </td>
                            <td>
                                <input type="text"  name="total_modify[]" class="form-control total_modify" id="total_modify_1" disabled required />
                            </td>
                            
                            
                            <td class="remove-td"
                                style="vertical-align: middle;text-align: center;display: block;"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button">Close </button><!--Close-->
                    <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveBillingItemOrderDetailModify()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="add_billing_element_detail_modal" class="modal fade" style="display: none; z-index:999999">
    <div class="modal-dialog" style="width: 100%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>
            
                <form role="form" id="billing_detail_form" class="form-horizontal">
                    <div class="modal-body" id="billing_detail_section_modal">
                       
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveBillingDetailForm()"><?php echo $this->lang->line('common_add'); ?>
                        
                    </div>
                </form>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="link_online_request_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- <div class="color-line"></div> -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="link_title">Online Visitor Log Request</h4>
                <!--Add Item Detail-->
            </div>
            
                
                    <div class="modal-body">
                        <div class="col-md-12">
                            
                            <div class="table-responsive" >
                                <form role="form" id="visitor_log_online_request_form" class="form-horizontal">
                                    <div class="row" style="margin-bottom: 25px;">
                                        <div class="col-md-4">
                                            <label for="fuel_add">User Name</label>
                                            <input type="text" name="userName" id="userName"   class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="fuel_add">Email</label>
                                            <input type="text" name="userEmail" id="userEmail" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="fuel_add">Message</label>
                                            <input type="text" name="userMessage" id="userMessage" class="form-control" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary-new btn-md" type="button" onclick="add_visitor_log_request()">+ Add</button>
                                        </div>
                                    </div>
                                </form>

                                    <table id="visitor_log_request" class="<?php echo table_class(); ?>">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 5%">S/N</th>
                                                <!--Invoice Code-->
                                                <th style="min-width: 20%"><?php echo 'User'; ?></th>
                                                <!--Total Value-->
                                                <th style="min-width: 15%"><?php echo 'Email' ?></th>
                                                <!--Total Value-->
                                                <th style="min-width: 25%"><?php echo 'Message' ?></th>
                                                <!--Total Value-->
                                                <th style="min-width: 10%"><?php echo 'Created At' ?></th>
                                                <!--Total Value-->
                                                <th style="min-width: 5%"><?php echo 'Status' ?></th>
                                                <!--Total Value-->
                                                <th style="min-width: 5%"><?php echo 'Link' ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="6">No data to Show</td>
                                            </tr>
                                        </tbody>
                                    </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default-new size-sm"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                        
                    </div>
               

        </div>
    </div>
</div>



<!-----modal start----------->
<div aria-hidden="true" role="dialog"  id="checklist_view_modal_response" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>    
                            <h5 class="modal-title">&nbsp;</h5>            
            </div>
            <div class="modal-body" id="checklist_view">

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default-new size-lg" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<!-----modal end----------->

<!-----modal start----------->
<div aria-hidden="true" role="dialog"  id="daily_job_report_view_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>    
                            <h5 class="modal-title">&nbsp;</h5>            
            </div>
            <div class="modal-body" id="checklist_view_modal">

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default-new size-lg" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<!-----modal end----------->
<!-----modal start----------->
<div class="modal fade" id="assignChecklist_model_job" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Check List</h4>
            </div>
            <div class="modal-body">

            <div class="row" style="margin: 6px 0px;">
                    <div class="col-md-6">&nbsp;</div>
                    <div class="col-md-6">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchOrder" type="text" class="form-control input-sm"
                                       placeholder="Search"
                                       id="searchOrder" onkeyup="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
            </div>
                <div class="row">
                    <div class="col-sm-12">
                        
                        <div id="assignChecklist_item_Content_job"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12 pull-right">
                    <button type="button" class="btn btn-default-new size-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button class="btn btn-primary-new size-sm" onclick="assign_checklist()">Assign</button>
                </div>

            </div>
        </div>
    </div>
</div>
<!-----modal end----------->

<!-----modal start----------->
<div aria-hidden="true" role="dialog"  id="billing_detail_report_view_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>    
                            <h5 class="modal-title">&nbsp;</h5>            
            </div>
            <div class="modal-body" id="billing_detail_report_view">

            </div>
            <div class="modal-footer">                
                <button class="btn btn-primary-new size-lg float-right" type="button" id="btn-confirm-billing" onclick="confirmBilling()"><!--Close--><?php echo $this->lang->line('common_confirm');?></button>
                <button data-dismiss="modal" class="btn btn-default-new size-lg mr-1" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<!-----modal end----------->

<!-----modal start----------->
<div aria-hidden="true" role="dialog"  id="billing_detail_report_view_modal_modify" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>    
                            <h5 class="modal-title">&nbsp;</h5>            
            </div>
            <div class="modal-body" id="billing_detail_report_view_modify">

            </div>
            <div class="modal-footer">                
                <button class="btn btn-primary-new size-lg float-right" type="button" id="btn-confirm-billing-modify" onclick="confirmBillingModify()"><!--Close--><?php echo $this->lang->line('common_confirm');?></button>
                <button data-dismiss="modal" class="btn btn-default-new size-lg mr-1" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<!-----modal end----------->

<!-----modal start----------->
<div aria-hidden="true" role="dialog"  id="billing_detail_report_view_modal_standard" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>    
                            <h5 class="modal-title">&nbsp;</h5>            
            </div>
            <div class="modal-body" id="billing_detail_report_view_standard">

            </div>
            <div class="modal-footer">                
                <button class="btn btn-primary-new size-lg float-right" type="button" id="btn-confirm-billing-standard" onclick="confirmBillingStandard()"><!--Close--><?php echo $this->lang->line('common_confirm');?></button>
                <button data-dismiss="modal" class="btn btn-default-new size-lg mr-1" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<!-----modal end----------->

<!-----modal start----------->
<div aria-hidden="true" role="dialog"  id="checklist_view_modal_common" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>    
                            <h5 class="modal-title">&nbsp;</h5>            
            </div>
            <div class="modal-body" id="checklist_view_modal_body">

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default-new size-lg" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<!-----modal end----------->

<!-----modal start----------->
<div aria-hidden="true" role="dialog" id="groupTo_add_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo 'Add Group To';?><!--Add Item Detail--></h4>
            </div>
            
            <form role="form" id="crew_group_form" class="form-horizontal">
                <table class="table table-bordered table-striped table-condesed" id="crew_group_table" style="table-layout: fixed">
                    <thead>
                        <tr>
                            <th style="width: 200px; "><?php echo 'Group Name'?> <?php required_mark(); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr >
                            <td>
                                <input type="hidden" name="type_open" id="type_open">
                                <input type="hidden" name="tb_open" id="tb_open">
                                <input type="hidden" name="groupType" id="groupType" >
                                <input type="text"  name="groupName" id="groupName" class="form-control groupName"/>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button"><?php echo $this->lang->line('common_close');?> </button><!--Close-->
                    <button data-dismiss="modal" class="btn btn-primary-new size-sm" type="button"
                            onclick="saveGroupToDetails()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-----modal end----------->

<div class="modal fade bs-example-modal-lg" id="checklist_user_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Check List Users</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">

                <div id ="checklist_user_section">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default size-sm"
                        data-dismiss="modal">Close </button>
                <!-- <button type="button" class="btn btn-primary size-sm"
                        onclick="save_visibility()">Add Visibility </button> -->
            </div>
            </form>
        </div>
    </div>
</div>

<!-----modal start----------->
<div aria-hidden="true" role="dialog" id="load_share_link_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo 'Share Link';?><!--Add Item Detail--></h4>
            </div>
            
            <form role="form" id="share_link_form" class="form-horizontal">
                <div class="col-md-12" style="margin:20px 10px;">
                    <label for="setLinkForShare">Link</label>
                    <textarea id="setLinkForShare" class="form-control" rows="8" ></textarea>  
                </div>
                             
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button"><?php echo $this->lang->line('common_close');?> </button><!--Close-->
                   
                </div>
            </form>
        </div>
    </div>
</div>
<!-----modal end----------->



<div aria-hidden="true" role="dialog" id="add_billing_data_standard" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>

            <div class="modal-body">
                <!-- <div class="row pt-0">
                    <div class="col-sm-12">
                        <div class="supply_master_style">
                            <ul class="nav nav-tabs" id="main-tabs">
                                <li class="btn-default-new size-sm tab-style-one mr-1 active"><a href="#billling_add_standard" data-toggle="tab">From Selected Contract</a></li>
                               
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="billling_add_standard">
                                    
                                

                            </div>
                            
                        </div>

                    </div>
                </div> -->
                <form role="form" id="billling_add_standard_select_form" class="form-horizontal">
                                <input type="hidden" name="billing_header_id_standard" id="billing_header_id_standard">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-8 btn-w-100">
                                            &nbsp;
                                            </div>

                                            <div class="col-md-4 text-right">
                                
                                                <button type="button" class="btn btn-primary size-xs" onclick="open_billing_standard_item_model()">Add Item &nbsp;<i class="fa fa-search"></i></button> 
                                                
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                        <table id="billing_added_datatbl_standard" class="<?php echo table_class(); ?>">
                                <thead>
                                    <tr>
                                        <th style="min-width: 5%">S/N</th>
                                        <th style="min-width: 10%"><?php echo 'Code'; ?></th>
                                        <!--Invoice Code-->
                                        <th style="min-width: 43%"><?php echo 'Description'; ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'UOM' ?></th>
                                        <th style="min-width: 10%"><?php echo 'From Date' ?></th>
                                        <th style="min-width: 10%"><?php echo 'To Date' ?></th>
                                        <th style="min-width: 5%"><?php echo 'Qty' ?></th>
                                        
                                        <!--Total Value-->
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Unit Price' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo 'Total' ?></th>
                                        <!--Total Value-->
                                        <th style="min-width: 5%"><?php echo '#' ?></th>
                                    
                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8">No data to Show</td>
                                    </tr>
                                </tbody>
                                </table>
                                        </div>
                                           
                                    </div>
                                    <div class="modal-footer">
                                        <!--<button data-dismiss="modal" class="btn btn-primary size-sm"
                                                type="button" onclick="crew_select_add()"><?php //echo $this->lang->line('common_add'); ?> </button>-->
                                        <button data-dismiss="modal" class="btn btn-default size-sm"
                                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                                    
                                    </div>
                                </form>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="add_billing_data_standard_item_model" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 99%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"
                    style="width: 98%;">Billing
                    
                </h5>

            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="<?php echo table_class(); ?>" id="add_billing_data_standard_item_model_table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>UOM</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                    
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                
            </div>
        </div>
    </div>
</div>


<!-- Include flatpickr JavaScript -->
<script src="<?php echo base_url('plugins/dist/js/flatpickr.js'); ?>"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('a.step-wiz[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a.step-wiz[data-toggle="tab"]').removeClass('btn-primary');
            $('a.step-wiz[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });          
        
        // showHideAttachment();

        $('.headerclose').click(function(){
            fetchPage('system/sales/master/jobs_master','','Contracts');
        });
    });    
</script>

<script>

    var job_id = '';
    var Otable1;
    var OtableItem;
    var OtableAsset;
    var OtableActivity;
    var OtableActivityShift;
    var OtableBilling;
    var OtableBilling_standard;
    var OtableDailyReport;
    var OtableCrewCommon;
    var Otablevlog;
    var Otablefuel;
    var Otablepipe;
    var OtableAssetContract;
    var OtableActivityCrew;
    var OtableActivityAddCrew;
    var OtableBillingAddedDetail;
    var OtableVisitorRequest;
    var assignCheckListSync = [];
    var assignAssetListSync =[];
    var search_id = 1;
    var ticketTemplateType ='';
    var isBillingModifyMode =0;
    var OtableBillingAddedDetailStandard;
    var search_id_activity = 1;
    var OtableBillingAddedDetailModify;
    var search_id_modify =1;
    var OtableBilling_modify;

    load_job_header();
    initializeitemTypeahead(search_id);
    crew_added_table();
    item_added_table();
    asset_added_table();
    
    // activity_added_table();
    activity_shift_added_table();
    update_values_cross_view();
    daily_report_added_table();
    daily_billing_table();
    daily_billing_table_standard();
    daily_billing_table_modify();
    load_pre_job_checklist();
    op_job_attachments_well();
    op_job_attachments_bob();
    showHideAttachment();


    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var currency_decimal = <?php echo json_encode($this->common_data['company_data']['company_default_decimal']) ?>;
    
    $('.select2').select2();
    //$( "#fromDate" ).datepicker();
    //$( "#toDate" ).datepicker();

    

  

    if(job_id){
        fetch_assign_checklist_table() ;
        load_other_checklist();
    }

    // $( "#shiftFromDate" ).datepicker();
    // $( "#shiftToDate" ).datepicker();

    $('a[data-toggle="tab"]').on('click', function(e){
        
        var job_id = $('#job_id').val();

        update_values_cross_view();

        if(job_id){
            return true;
        }

        return false;

    });

    function update_values_cross_view(){
        var doc_date = $('#doc_date').val();
        var job_ref = $('#doc_ref').val();
        var customer = $('#customer :selected').text();
        var contract = $('#contract :selected').text();
        var currencyCode = $('#currencyCode').val();
        var po_number = $('#po_number').val();

        $('#doc_date_sub').val(doc_date);
        $('.doc_ref_sub').val(job_ref).blur();
        $('.doc_customer').val(customer).blur();
        $('.doc_contract_po_num').val(contract).blur();
        $('.doc_contract_currency').val(currencyCode).blur();
        $('.doc_po_number').val(po_number).blur();
        
    }

    function load_job_header(){

        job_id = $('#job_id').val();

        if(job_id){

            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'job_id': job_id
                    },
                    url: "<?php echo site_url('Jobs/fetch_job_header_details'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        refreshNotifications(true);
                        stopLoad();

                        if(data){

                            $('#customer').val(data.customer_id).change();
                            $('#doc_name').val(data.job_name).change();
                            $('#doc_ref').val(data.job_reference).change();
                            $('#field_id').val(data.field_id).change();
                            $('#rig_id').val(data.rig_hoist_id).change();
                            $('#status').val(data.job_status).change();
                            $('#po_number').val(data.po_number);
                            $('#job_description').text(data.job_description);
                            $('#fromDate').val(data.job_date_from).change();
                            $('#toDate').val(data.job_date_to).change();

                            $('#ptw_number').val(data.ptw_number).change();
                            $('#iso_certificate').val(data.iso_certificate).change();
                            $('#hot_permit_number').val(data.hot_permit_number).change();
                            $('#muster_area').val(data.muster_area).change();
                            $('#sftp_number').val(data.sftp_number).change();
                            $('#weight').val(data.weight).change();
                            $('#prv_set').val(data.prv_set).change();
                            $('#job_obj_summary').val(data.job_obj_summary).change();
                            $('#start_time').val(data.job_start_time).change();
                            $('#shift_hours').val(data.shift_hours).change();
                            $('#well_number').val(data.well_number).change();
                            $('#pw_pump_number').val(data.pw_pump_number).change();
                            $('#job_type').val(data.job_type).change();

                            setTimeout(() => {
                               $('#contract').val(data.contract_po_id).change();
                               $('#well_id').val(data.well_id).change();
                                //$('#item-details-table-section').css('display','block');
                            }, 1000);
                          
                        }
                       
                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                        stopLoad();
                    }
                });
            

        }

    }

    function crew_select_contract_table(){

        Otable = $('#crew_select_contract_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_crew_list_contract'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
              
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "empCode", "bSearchable": true},
                {"mData": "empName", "bSearchable": true},
                {"mData": "empDesignation", "bSearchable": true},
                {"mData": "status"},
                {"mData": "checkbox"}
        
            ],
            "columnDefs": [
              
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
                aoData.push({"name": "group_crew", "value": $("#group_crew").val()});
                aoData.push({"name": "group_type", "value": '1'});

                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
    }

    function crew_added_table(){

        Otable1 = $('#crew_added_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_added_crew_details'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "empID"},
                {"mData": "id"},
                {"mData": "ECode"},
                {"mData": "name"},
                {"mData": "designation"},
               // {"mData": "dateFromT"},
               // {"mData": "dateToT"},
               // {"mData": "dateHours"},
               // {"mData": "schedule"},
                {"mData": "groupName"},
                {"mData": "competencyChk"},
                {"mData": "trainingChk"},
                {"mData": "sscChk"},
                // {"mData": "jobStatus"},
                {"mData": "action"}

            ],
            "columnDefs": [
                { 'visible': false, 'targets': [1,2] }
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
    }

    function crew_select_common_table(){

        OtableCrewCommon = $('#crew_select_common_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_crew_list_common'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
              
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "Ename1"},
                {"mData": "ECode"},
                {"mData": "designation"},
                {"mData": "status"},
                {"mData": "action"}
        
            ],
            "columnDefs": [
                { searchable: true, targets: 1 }, { searchable: false, targets: 3 }
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
              
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
    }

    function asset_select_contract_table(){
        
        OtableAssetContract = $('#asset_add_table_contract').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_asset_contract_list'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
              
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "faCode"},
                {"mData": "assetName"},
                {"mData": "assetRef"},
                {"mData": "status"},
                {"mData": "action"},
        
            ],
            "columnDefs": [
              
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
                aoData.push({"name": "group_asset", "value": $("#group_asset").val()});
                aoData.push({"name": "group_type", "value": 2});
              
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
    }

    function item_added_table(){

        OtableItem = $('#job_item_datatbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_job_item_details'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                //check table empty
                if (OtableItem.data().any() ) {
                  //  $('#contract').attr('disabled',true);
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "code"},
                {"mData": "itemDescription"},
                {"mData": "uomCode"},
                {"mData": "qty"},
                {"mData": "value"},
                {"mData": "discount"},
                {"mData": "netAmount"},
                {"mData": "comment"},
                {"mData": "action"},
             
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
    }

    function asset_added_table(){

        // job_assets_datatbl

        OtableAsset = $('#job_assets_datatbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_job_asset_details'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "faID"},
                {"mData": "id"},
                {"mData": "assetCode"},
                {"mData": "assetName"},
                {"mData": "assetRef"},
                {"mData": "dateFromT"},
                {"mData": "dateToT"},
                {"mData": "dateHours"},
                // {"mData": "comment"},
                {"mData": "maintenanceChk"},
                // {"mData": "jobStatus"},
                {"mData": "action"},
             
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });

        setTimeout(function () {
            
        flatpickr(".assestFromDate_Def", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
        });
        }, 500);

        setTimeout(function () {
            
            flatpickr(".assestoDate_Def", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
            });
        }, 500);


    }

    function activity_added_table(){

        OtableActivity = $('#activity_added_datatbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_job_activity'); ?>",
            "aaSorting": [[0, 'asc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                update_total_hours();

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "description"},
                {"mData": "type"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "dateHours"},
                {"mData": "isStandbyT"},
                {"mData": "isNptT"},
                {"mData": "action"},
             
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
                aoData.push({"name": "shift_id", "value": $("#shift_id").val()});

                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });

    }

    function activity_added_crew_table(){

        OtableActivityCrew = $('#activity_added_crew_datatbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_job_activity_crew'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "empCode"},
                {"mData": "name"},
                {"mData": "designation"},
                {"mData": "action"},
            ],

            "columnDefs": [
            
            ],

            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
                aoData.push({"name": "shift_id", "value": $("#shift_id").val()});
               
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });

    }

    function activity_load_crew_table(){

        OtableActivityAddCrew = $('#activity_crew_add_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_added_crew_details_shift'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "ECode"},
                {"mData": "name"},
                {"mData": "designation"},
                {"mData": "action_activity"},
            
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
                aoData.push({"name": "shift_id", "value": $("#shift_id").val()});
                aoData.push({"name": "filter", "value": $("#group_activity_crew").val()});

                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });

    }

    function activity_shift_added_table(){

        OtableActivityShift = $('#activity_shift_added_datatbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_job_activity_shift'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                update_total_hours();

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "description"},
                {"mData": "type"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "checklist"},
                {"mData": "action"},
             
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
    }

    function billing_added_detail_table(){
        

        OtableBillingAddedDetail = $('#billing_added_datatbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_billing_detail_added'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "description"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "qty"},
                {"mData": "isStandby"},
                {"mData": "isNpt"},
                {"mData": "price_text"},
                {"mData": "unit_amount"},
                {"mData": "total_amount"},
                {"mData": "action"},
             
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
                aoData.push({"name": "billing_header_id", "value": $("#billing_header_id").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
    }



    function daily_report_added_table(){

        OtableDailyReport = $('#dailyReporttbl').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_daily_report'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                update_total_hours();

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "description"},
                {"mData": "type"},
                {"mData": "confirmedYN"},
                {"mData": "action"}
            
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
    }

    function daily_billing_table(){

        OtableBilling = $('#billing_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_billing_report'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                update_total_hours();

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "code"},
                {"mData": "description"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "value"},
                {"mData": "netAmount"},
                {"mData": "orderStatus"},
                {"mData": "confirmedYN"},
                {"mData": "action"}
            
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
            });
    }

    

    function daily_billing_table_standard(){

        OtableBilling_standard = $('#billing_table_standard').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_billing_report_standard'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                update_total_hours();

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "code"},
                {"mData": "description"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "value"},
                {"mData": "netAmount"},
                {"mData": "orderStatus"},
                {"mData": "confirmedYN"},
                {"mData": "action"}
            
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
            });
    }

    function daily_billing_table_modify(){

        OtableBilling_modify = $('#billing_table_modify').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_billing_report_modify'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

                update_total_hours();

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "code"},
                {"mData": "description"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "value"},
                {"mData": "netAmount"},
                {"mData": "orderStatus"},
                {"mData": "confirmedYN"},
                {"mData": "action"}
            
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
            });
    }
       

    //change functions
    function change_customer(){
        var customerID = $('#customer :selected').val();
        var contract = $('#contract');

        if(customerID){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'customerID': customerID
                },
                url: "<?php echo site_url('Jobs/fetch_contract_list'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();

                    contract.empty();
                    contract.append($('<option></option>').val('').attr('selected','selected').attr('hidden','hidden').html('Select Contract'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function(val, text) {
                            contract.append($('<option></option>').val(val).html(text));
                        });
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }
    }

    function change_contract(){

        var contract = $('#contract :selected').val();

        if(contract){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'contractID': contract
                },
                url: "<?php echo site_url('Jobs/fetch_contract_details'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();

                    if(data){
                        $('#currencyID').val(data['transactionCurrencyID']);
                        $('#currencyCode').val(data['transactionCurrency']);

                        if(data['ticketTemplate']=='Standard'){
                            ticketTemplateType = data['ticketTemplate'];
                            //hide pipe tally
                            $('#tally_tab_admin').addClass('hide');
                            $('#tally_tab_user').addClass('hide');
                            //show hide billing tab base on job template type
                            $('#billing_job_user').addClass('hide');
                            $('#billing_job_admin').addClass('hide');

                            $('#billing_modify_user').addClass('hide');
                            $('#billing_modify_admin').addClass('hide');

                            $('#billing_standard_admin').removeClass('hide');
                            $('#billing_standard_user').removeClass('hide');
                        }else{
                            isBillingModifyMode = data['editJobBillingYN'];
                            if(data['editJobBillingYN']==1){
                                $('#tally_tab_admin').removeClass('hide');
                                $('#tally_tab_user').removeClass('hide');
                                //show hide billing tab base on job template type
                                $('#billing_job_user').addClass('hide');
                                $('#billing_job_admin').addClass('hide');

                                $('#billing_modify_user').removeClass('hide');
                                $('#billing_modify_admin').removeClass('hide');

                                $('#billing_standard_admin').addClass('hide');
                                $('#billing_standard_user').addClass('hide');
                            }else{
                                //show pipe tally
                                $('#tally_tab_admin').removeClass('hide');
                                $('#tally_tab_user').removeClass('hide');

                                //show hide billing tab base on job template type
                                $('#billing_job_user').removeClass('hide');
                                $('#billing_job_admin').removeClass('hide');

                                $('#billing_standard_admin').addClass('hide');
                                $('#billing_standard_user').addClass('hide');

                                $('#billing_modify_user').addClass('hide');
                                $('#billing_modify_admin').addClass('hide');
                            }
                            
                        }
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }

        // Call prejob checklist
        // call_pre_job_checklists();

    }

    function change_crew_group(ev){

        if(ev == '1'){
            crew_select_contract_table();
        }else if(ev == '2'){
            asset_select_contract_table();
        }else if(ev == '3'){
            // activity_added_crew_table();
            add_activities_crew();
        }

    }

    function group_select(type_ex){

        job_id = $('#job_id').val();
        contract_id = $("#contract").val();
       
        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': job_id,'type':type_ex,'contract_id':contract_id
                },
                url: "<?php echo site_url('Jobs/get_group_list'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {  
                    stopLoad();
                    
                    if(type_ex == 1){
                        var crew_drop = $('#group_crew');
                        crew_drop.empty();
                        //.attr('hidden','hidden')
                        // crew_drop.append($('<option></option>').val('').attr('selected','selected').html('Select Contract'));
                        if (!jQuery.isEmptyObject(data)) {
                            $.each(data, function(val, text) {
                                crew_drop.append($('<option></option>').val(text.groupAutoID).html(text.groupName));
                            });
                        }
                        
                       
                    } else if(type_ex == 2){
                        var asset_drop = $('#group_asset');
                        asset_drop.empty();
                        //.attr('hidden','hidden')
                        // crew_drop.append($('<option></option>').val('').attr('selected','selected').html('Select Contract'));
                        if (!jQuery.isEmptyObject(data)) {
                            $.each(data, function(val, text) {
                                asset_drop.append($('<option></option>').val(text.groupAutoID).html(text.groupName));
                            });
                        }

                       // call_multiselect();
                    } else if(type_ex == 3){
                        var crew_drop_activity = $('#group_activity_crew');
                        crew_drop_activity.empty();
                        //.attr('hidden','hidden')
                        // crew_drop.append($('<option></option>').val('').attr('selected','selected').html('Select Contract'));
                        if (!jQuery.isEmptyObject(data)) {
                            $.each(data, function(val, text) {
                                crew_drop_activity.append($('<option></option>').val(text.groupAutoID).html(text.groupName));
                            });
                        }

                       // call_multiselect();
                    }

                    call_multiselect(type_ex);
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }

    }

    function group_job_select(type){

        job_id = $('#job_id').val();
        var crew_drop = $('#group_jobcrew');

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': job_id,'type':type
                },
                url: "<?php echo site_url('Jobs/get_group_list'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {  
                    stopLoad();

                    crew_drop.empty();
                    //.attr('hidden','hidden')
                    crew_drop.append($('<option></option>').val('').attr('selected','selected').html('Select Job Group'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function(val, text) {
                            crew_drop.append($('<option></option>').val(text.groupAutoID).html(text.groupName));
                        });
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }
    }

    function call_multiselect(type){

        if(type == 1){
            $('#group_crew').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: 150,
                maxHeight: 200,
                numberDisplayed: 1
            });
        }
      
        if(type == 3){
            $('#group_activity_crew').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: 150,
                maxHeight: 200,
                numberDisplayed: 1
            });
        }

        if(type == 2){
            $('#group_asset').multiselect2({
                includeSelectAllOption: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: 150,
                maxHeight: 200,
                numberDisplayed: 1
            });
        }

    }

    function change_field(ev){

        var field = ev.val();
        var well = $('#well_id');

        if(field){
           
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'fieldID': field
                },
                url: "<?php echo site_url('Jobs/fetch_well_details'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();
                    if(data){
                        well.empty();
                        well.append($('<option></option>').val('').attr('selected','selected').attr('hidden','hidden').html('Select Contract'));
                        if (!jQuery.isEmptyObject(data)) {
                            $.each(data, function(val, text) {
                                well.append($('<option></option>').val(val).html(text));
                            });
                        }
                        
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }

    }

    function change_dateFrom(ev){
        var empID = ev.closest('tr').find('td:eq(1)').text();
        var taskID = ev.closest('tr').find('td:eq(2)').text();
        var picked_date_from = ev.val();
        job_id = $('#job_id').val();

        if(job_id){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'empID': empID,'dateFrom':picked_date_from,'job_id':job_id,'taskID':taskID
                },
                url: "<?php echo site_url('Jobs/add_crew_from_date'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();
                    setTimeout(() => {
                        Otable1.draw();
                    }, 1000);
                    
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });
        }
        
    }

    function change_dateTo(ev){
        var empID = ev.closest('tr').find('td:eq(1)').text();
        var taskID = ev.closest('tr').find('td:eq(2)').text();
        var picked_date_to = ev.val();
        job_id = $('#job_id').val();

        
        if(job_id){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'empID': empID,'dateTo':picked_date_to,'job_id':job_id,'taskID':taskID
                },
                url: "<?php echo site_url('Jobs/add_crew_to_date'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();

                    setTimeout(() => {
                        Otable1.draw();
                    }, 1000);
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });
        }

    }

    function change_dateTo_asset(ev){
        var faID = ev.closest('tr').find('td:eq(1)').text();
        var taskID = ev.closest('tr').find('td:eq(2)').text();
        var picked_date_to = ev.val();
        job_id = $('#job_id').val();

        
        if(job_id){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'faID': faID,'dateTo':picked_date_to,'job_id':job_id,'taskID':taskID
                },
                url: "<?php echo site_url('Jobs/add_asset_to_date'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();

                    setTimeout(() => {
                        OtableAsset.draw();
                    }, 1000);
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });
        }

    }

    function change_dateFrom_asset(ev){
        var faID = ev.closest('tr').find('td:eq(1)').text();
        var taskID = ev.closest('tr').find('td:eq(2)').text();
        var picked_date_from = ev.val();
        job_id = $('#job_id').val();

        if(job_id){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'faID': faID,'dateFrom':picked_date_from,'job_id':job_id,'taskID':taskID
                },
                url: "<?php echo site_url('Jobs/add_asset_from_date'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();
                    setTimeout(() => {
                        Otable1.draw();
                    }, 1000);
                    
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });
        }
        
    }

    function change_job_status(ev){
        var selected_status = ev.val();
        job_id = $('#job_id').val();
    }

    function change_item_claculation(ev){

       var amount = ev.closest('tr').find('.amount').val();
       var quantity = ev.closest('tr').find('.quantity').val();
       var discount = ev.closest('tr').find('.discount').val();


       //calculation 
       var netTotal = (amount*quantity) - discount;

       ev.closest('tr').find('.amount').val(parseFloat(amount).toFixed(currency_decimal));
       ev.closest('tr').find('.discount').val(parseFloat(discount).toFixed(currency_decimal));
       ev.closest('tr').find('.netAmount').val(parseFloat(netTotal).toFixed(currency_decimal));

    }

    function checkbox_changed(ev, type) {
    if (type == 'isStandby') {
        var value = ev.is(":checked") ? ev.val() : 0;
        ev.siblings("input[name='isStandby[]']").val(value);
    } else if (type == 'isNpt') {
        var value = ev.is(":checked") ? ev.val() : 0;
        ev.siblings("input[name='isNpt[]']").val(value);

        if (ev.is(":checked")) {

            refreshNotifications(true);
            $('#textInputModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#npt_comment').val('');
           
            

            // Show notifyicon when the checkbox is checked
            $('#notifyicon').show();

            // Attach click event handler to notifyicon
            $('#notifyicon').on('click', function () {
                var savedComment = $('#notifyicon').val();
                // You can use 'savedComment' to display or use the saved comment as needed
                $('#textInputModal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    savedComment
                });
            });
        } else {
            // Hide notifyicon and clear the value when the checkbox is unchecked
            $('#notifyicon').hide();
            $('#notifyicon').val('');
        }

        // Attach click event handler to save button within the modal
      // Attach click event handler to save button within the modal
     $('#saveModalButton').on('click', function () {
    // Get the comment value from the textarea
    var commentValue = $('#npt_comment').val(); // Fix: Use #npt_comment

    
    // Check if the comment is mandatory and not empty
    if (ev.is(":checked") && commentValue.trim() === '') {
        toastr.error('Please Enter NPT Justification');
        return; // Prevent closing the modal
    }


    // Set the comment value to the hidden input field
    $('#NPTcomment').val(commentValue); // Fix: Use #NPTcomment

    // Close the modal
    $('#textInputModal').modal('hide');
});

    }
}

    function change_date_populate_fn(ev,type){

        if(type == 'shift'){
            $('#shiftToDate').val(ev.val());
        }else if(type == 'dailyReport'){
            $('#reportToDate').val(ev.val())
        }

    }

    function change_check_box(ev,id,type){

        var value = $(ev).is(":checked") ? $(ev).val() : 0;
        var name = $(ev).attr('name');

        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': id,'empID':type, 'value':value,'name':name
                },
                url: "<?php echo site_url('Jobs/save_jobs_crew_check_status'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }

    $('#job_details_form').bootstrapValidator({
            
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
              
            },
    }).on('success.form.bv', function (e) {
           
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            // data.push({"name": "contractAutoID", "value": $("#contract").val()});
            
            $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Jobs/save_jobs_detail_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    job_id1 = $('#job_id').val();
                    if (!job_id1) {
                        generateSequence();  
                    }
                    if(data.status){
                        $('#job_id').val(data.last_id);
                        job_id = data.last_id;
                        showHideAttachment();
                        load_pre_job_checklist();
                        load_other_checklist();
                        //$('#item-details-table-section').css('display','block');
                       
                    }
                   
                    
                 
                    
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    });

    function generateSequence() {
    $.ajax({
        async: false,
        type: 'post',
        dataType: 'json',
        data: { job_id: job_id ? job_id : null },
        url: "<?php echo site_url('Jobs/generate_sequence'); ?>",
        success: function (response) {
            },
        error: function () {
            alert('Error generating sequence.');
        }
    });
}

    //append function

    //Modals functions
    /*function add_employee_job(empID,designation = null){

        var data = $('#crew_contract_common_form').serializeArray();
        
        job_id = $('#job_id').val();

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Jobs/add_crew_job'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);
                    if(data){
                        Otable1.draw();
                        $("#crew_detail_modal").modal('hide');
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }


    }*/

    function add_employee_job(empID,designation = null){
        job_id = $('#job_id').val();

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': job_id,'empID':empID,'designation':designation
                },
                url: "<?php echo site_url('Jobs/add_crew_job'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);
                    if(data){
                        Otable1.draw();
                        $("#crew_detail_modal").modal('hide');
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }


    }

    //Modals
    function add_more_items(){
        $("#invoice_item_detail_modal").modal({backdrop: "static"});
    }

    function edit_added_record(id,type){

        if(type == 'activity'){

            $('#edit_acctivity_id').val(id);
            load_activity_detail(id);
            $("#activity_detail_edit_modal").modal({backdrop: "static"});

        }else{
            $('#item_tbl_id').val(id);
            load_added_item_detail(id);
            initializeitemTypeahead('edit');
            $("#invoice_item_detail_edit_modal").modal({backdrop: "static"});
        }

        

    }

    function load_added_item_detail(id){

        if(id){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'id': id
                },
                url: "<?php echo site_url('Jobs/load_job_item_detail'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();

                    //toFixed(currency_decimal)
                    $('#invoice_item_detail_edit_form .f_search_edit').val(data.code+'|'+data.itemDescription);
                    $('#invoice_item_detail_edit_form .itemAutoID').val(data.itemAutoID);
                    $('#invoice_item_detail_edit_form .itemSystemCode').val(data.code);
                    $('#invoice_item_detail_edit_form .itemUOM').val(data.uomCode);
                    $('#invoice_item_detail_edit_form .amount').val(parseFloat(data.value).toFixed(currency_decimal));
                    $('#invoice_item_detail_edit_form .quantity').val(data.qty);
                    $('#invoice_item_detail_edit_form .discount').val(parseFloat(data.discount).toFixed(currency_decimal));
                    $('#invoice_item_detail_edit_form .netAmount').val(parseFloat(data.transactionAmount).toFixed(currency_decimal));
                    $('#invoice_item_detail_edit_form .comment').val(data.comment);
                    
                    setTimeout(() => {
                        Otable1.draw();
                    }, 1000);
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });
        }

    }

    function load_activity_detail(id){

        if(id){
            // var data = $('#activity_detail_edit_form').serializeArray();

            // data.push({'name': 'job_id', 'value': $('#job_id').val()});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'activity_id': id,
                    'job_id': $('#job_id').val()
                },
                url: "<?php echo site_url('Jobs/load_activity_detail'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();

                    $('#activity_detail_edit_form #edit_description').val(data.description);
                    $('#activity_detail_edit_form #edit_activity_type').val(data.type).change();
                    $('#activity_detail_edit_form #edit_fromDate').val(data.dateFrom);
                    $('#activity_detail_edit_form #edit_toDate').val(data.dateTo);

                    if(data.isStandby == 1){
                         $('#activity_detail_edit_form #edit_isStandby').prop('checked',true);
                    }else{
                        $('#activity_detail_edit_form #edit_isStandby').prop('checked',false);
                    }

                    if(data.isNpt == 1){
                         $('#activity_detail_edit_form #edit_isNPT').prop('checked',true);
                    }else{
                        $('#activity_detail_edit_form #edit_isNPT').prop('checked',false);
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });
        }
    }

    function add_crew(){

        crew_select_contract_table();
        crew_select_common_table();
        group_select(1);
        group_job_select(3);

        $("#crew_detail_modal").modal({backdrop: "static"});
    }

    function add_asset(){
        asset_select_contract_table();
        group_select(2);
        // asset_select_common_table();
        load_common_assest();
        $("#assets_detail_modal").modal({backdrop: "static"});
    }

    function add_activities(){
        $('#activity_type').val('').change();
        $('#hid_isStandby').val(0).change();
        $('#hid_isNPT').val(0).change();
        $("#activity_detail_modal").modal('show');
        $('#activity_detail_form')[0].reset();
        $("#npt_form")[0].reset();

    }

    function add_activities_crew(){

        activity_load_crew_table();

        $("#activity_detail_modal_crew").modal({backdrop: "static"});
    }

    function add_billing_data_activity(){

        load_billing_detail_activity();

        $("#add_billing_element_detail_modal").modal({backdrop: "static"});
    }

    function add_billing_data_activity_modify(){

        //load_billing_detail_activity();
        flatpickr(".datefrom_modify", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });

        flatpickr(".dateto_modify", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });

        $("#add_billing_data_modify_add").modal({backdrop: "static"});
    }

    function add_more_item_modify_billing() {
        search_id_modify += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#item_add_table_modify tbody tr:first').clone();
        appendData.find('.pID').attr('id', 'pID_' + search_id_modify);

        appendData.find('.pID').attr('onchange', 'change_pricing_billing_modify($(this),'+search_id_modify+')');

        

        appendData.find('.min_modify').attr('id', 'min_modify_' + search_id_modify);

        appendData.find('.min_modify').attr('onchange', 'change_requested_modify_qty($(this))');
       // appendData.find('.umoDropdown').empty();
        //appendData.find('.DetailctivityCode').empty();
        appendData.find('input').val('');
        appendData.find('.number').val('0');
        appendData.find('.number,.wac_cost,.net_unit_cost,.net_amount').text('0');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#item_add_table_modify').append(appendData);
        var lenght = $('#item_add_table_modify tbody tr').length - 1;
        $('#f_search_'+ search_id_modify).closest('tr').css("background-color",'white');
        $(".select2").select2();
       //initializeitemTypeahead(search_id);
        //number_validation();

        flatpickr(".datefrom_modify", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });

        flatpickr(".dateto_modify", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
        });
    }


    function add_shift(){
        $("#activity_shift_modal").modal({backdrop: "static"});
    }

    function add_billing(){

        $('#billing_code').val("<?php echo $billingID ?>" + Math.floor(Math.random()*1000000)+1)

        $("#add_billing_modal").modal({backdrop: "static"});
    }

    function add_daily_report(){
        $("#daily_report_modal").modal({backdrop: "static"});
    }

    function add_activity(shift_id){

        $('#shift_id').val(shift_id);

        activity_added_table();
        update_total_hours();
        group_select(3);
        activity_added_crew_table();

        $("#activity_shift_data_modal").modal({backdrop: "static"});
    }

    function add_billing_detail(billing){

        $('#billing_header_id').val(billing);

        billing_added_detail_table();

        $("#add_billing_data").modal({backdrop: "static"});
    }

    function add_billing_detail_standard(billing){
        $('#billing_header_id_standard').val('');
        $('#billing_header_id_standard').val(billing);
        billing_added_detail_table_standard();
        
        $("#add_billing_data_standard").modal({backdrop: "static"});
    }

    function add_billing_detail_modify(billing){
        $('#billing_header_id_modify').val('');
        $('#billing_header_id_modify').val(billing);
        billing_added_detail_table_modify();
        
        $("#add_billing_data_modify").modal({backdrop: "static"});
    }

    function add_get_employee_schedule(empID){
  
        $('#es_empID').val(empID);
        $("#employee_schedule_modal").modal({backdrop: "static"});
    }

    function add_visitor(){
        $("#visitor_log_detail_modal").modal({backdrop: "static"});
    }

    function add_pipe_tally(){
        $("#pipe_tally_detail_modal").modal({backdrop: "static"});
    }

    function add_fuel_receipt(){
        $('#fuelusageID').val('').change();
        $('#UOMid').val('').change();
        $("#fuel_receipt_detail_modal").modal({backdrop: "static"});
        
    }

    function add_issue_fuel(){
        $('.fuelDropdown_fuel').val('').change();
        $('.umoDropdown_fuel').val('').change();
        
        $("#fuel_Issue_detail_model").modal({backdrop: "static"});
        
    }


    function search_task_ov(){
        var empID =  $('#es_empID').val();
        var dateFrom = $('#start_date_time').val();
        var dateTo = $('#end_date_time').val();
        var table_row = '';

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': job_id,'empID':empID,'dateFrom':dateFrom,'dateTo':dateTo
                },
                url: "<?php echo site_url('Jobs/get_employee_schdule'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    $("#employee_schedule_table > tbody").empty();
                    $.each(data , function(i, value) { 
                        table_row = '<tr><td>'+value.id+'</td><td>'+value.empID+'</td><td>'+value.name+'</td><td>'+value.job_code+'</td><td>'+value.job_name+'</td><td>'+value.job_description+'</td><td>'+value.dateFrom+'</td><td>'+value.dateTo+'</td><td><buttton class="btn btn-primary-new btn-sm"><i class="fa fa-plus"></i> Request</button></td></tr>';
                        $("#employee_schedule_table > tbody:last-child").append(table_row);
                    }); 
            
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });


    }


    function remove_item_all_description(e, ths) {
        //$('#edit_itemAutoID').val('');
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }

    }
    
    function initializeitemTypeahead(id) {
    
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        var job_id = $('#job_id').val();
      
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>jobs/fetch_itemrecode_po/?column='+ 'allowedtoSellYN'+'&job_id='+job_id,
            onSelect: function (suggestion) {
           
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    $('#f_search_' + id).closest('tr').find('.itemcatergory').val(suggestion.mainCategory);
                    $('#f_search_' + id).closest('tr').find('.itemSystemCode').val(suggestion.itemSystemCode);
                    $('#f_search_' + id).closest('tr').find('.itemUOM').val(suggestion.uom);
                    $('#f_search_' + id).closest('tr').find('.amount').val(parseFloat(suggestion.unittransactionAmount).toFixed(currency_decimal));
                    $('#f_search_' + id).closest('tr').find('.quantity').val(1);
                    $('#f_search_' + id).closest('tr').find('.netAmount').val(parseFloat(suggestion.unittransactionAmount).toFixed(currency_decimal));
                    $('#f_search_' + id).closest('tr').find('.discount').val(parseFloat(0).toFixed(currency_decimal));
                    $('#f_search_' + id).closest('tr').find('.amount').attr('readonly',true);
                    $('#f_search_' + id).closest('tr').find('.netAmount').attr('readonly',true);
                }, 200);

                //$(this).closest('tr').find('.estimatedAmount').val(suggestion.companyLocalSellingPrice);


                // fetch_sales_price(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                // fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                // if ($('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val() && suggestion.mainCategory != 'Service') {
                //     fetch_rv_warehouse_item(suggestion.itemAutoID, this, $('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val());
                // }

                // $(this).closest('tr').find('.quantityRequested').focus();
                // $(this).closest('tr').css("background-color", 'white');
                // // $(this).closest('tr').find('.wareHouseAutoID').val('').change();
                // checkitemavailable(this, suggestion.itemAutoID,'');
                // fetch_line_tax_and_vat(suggestion.itemAutoID, this);
                // if (suggestion.revanueGLCode == null || suggestion.revanueGLCode == '' || suggestion.revanueGLCode == 0) {
                //     setTimeout(function () {
                //         $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                //     }, 200);
                //     $('#f_search_' + id).val('');
                //     $(this).closest('tr').css("background-color", '#ffb2b2 ');
                //     myAlert('w', 'Revenue GL code not assigned for selected item')
                // }
                // if (suggestion.mainCategory == 'Service') {
                //     $(this).closest('tr').find('.wareHouseAutoID').removeAttr('onchange');
                // } else {
                //     $(this).closest('tr').find('.wareHouseAutoID').attr('onchange', 'checkitemavailable(this)');
                // }
                // check_item_not_approved_in_document(suggestion.itemAutoID,id);
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');
    }

    function add_more_item_tbl() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#item_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        //appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#item_add_table').append(appendData);
        var lenght = $('#item_add_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        $(".select2").select2();
    
        initializeitemTypeahead(search_id)
    }

    function add_more_activity_tbl(){

        search_id_activity += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#activity_add_new_table tbody tr:first').clone();

        //appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find("input[type='checkbox']").attr('checked',false);
        appendData.find("input[type='checkbox']").val(1);
        appendData.find('.d_from').attr('id', 'datefrom_' + search_id_activity);
        appendData.find('.d_to').attr('id', 'dateto_' + search_id_activity);
       // appendData.find("input[type='datetime-local']").val("<?php echo $default_date ?>");
       //setTimeout(() => {
            
        //}, 1000);
      

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#activity_add_new_table').append(appendData);
        var lenght = $('#activity_add_new_table tbody tr').length - 1;
     
        $(".select2").select2();

            flatpickr("#datefrom_"+ search_id_activity, {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true
            });

            flatpickr("#dateto_"+search_id_activity, {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true
            });

    }

    $(document).on('click', '.remove-tr', function () {
            $(this).closest('tr').remove();
        });

    function saveInvoiceItemDetail(){

        //invoice_item_detail_form
        var data = $('#invoice_item_detail_form').serializeArray();

        data.push({'name': 'job_id', 'value': $('#job_id').val()});

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Jobs/save_item_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                 
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    
                    if(data){
                        $('#invoice_item_detail_modal').modal('hide');
                        $('#invoice_item_detail_form')[0].reset();
                        OtableItem.draw();
                    }
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            });

    }

    function saveInvoiceItemDetailEdit(){

        //invoice_item_detail_form
        var data = $('#invoice_item_detail_edit_form').serializeArray();

        data.push({'name': 'job_id', 'value': $('#job_id').val()});

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Jobs/save_item_detail_edit'); ?>",
                beforeSend: function () {
                    startLoad();
                
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    
                    if(data){
                        $('#invoice_item_detail_edit_modal').modal('hide');
                        $('#invoice_item_detail_edit_form')[0].reset();
                        OtableItem.draw();
                    }
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            });

    }

    function saveInvoiceActivityDetail(){

        //invoice_item_detail_form
        var data = $('#activity_detail_form').serializeArray();

        data.push({'name': 'job_id', 'value': $('#job_id').val()});
        data.push({'name': 'shift_id', 'value': $('#shift_id').val()});

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Jobs/save_activity_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    
                    if(data){
                        $('#activity_detail_modal').modal('hide');
                        $('#activity_detail_form')[0].reset();
                        $('#notifyicon').hide();
                        OtableActivity.draw();
                    }
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            });

    }

    function saveInvoiceActivityDetailEdit(){

        var data = $('#activity_detail_edit_form').serializeArray();

        data.push({'name': 'job_id', 'value': $('#job_id').val()});
        data.push({'name': 'shift_id', 'value': $('#shift_id').val()});

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Jobs/save_activity_detail_edit'); ?>",
                beforeSend: function () {
                    startLoad();
                
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    
                    if(data){
                        $('#activity_detail_edit_modal').modal('hide');
                        $('#activity_detail_edit_form')[0].reset();
                        OtableActivity.draw();
                    }
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            });

    }

    function saveCrewForActivity(id){

        // var data = $('#activity_detail_crew_form').serializeArray();

        // data.push({'name': 'job_id', 'value': $('#job_id').val()});
        // data.push({'name': 'shift_id', 'value': $('#shift_id').val()});

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'shift_id':  $('#shift_id').val(),
                    'job_id': $('#job_id').val(),
                    'crew_id':id
                },
                url: "<?php echo site_url('Jobs/save_activity_crew_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    
                    if(data){
                        $('#activity_detail_modal_crew').modal('hide');
                        // $('#activity_detail_form')[0].reset();
                        OtableActivityCrew.draw();
                    }
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            });

    }


    function saveShiftActivityDetail(){

        //invoice_item_detail_form
        var data = $('#activity_shift_form').serializeArray();

        data.push({'name': 'job_id', 'value': $('#job_id').val()});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Jobs/save_shift_activity_detail'); ?>",
            beforeSend: function () {
                startLoad();
            
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
                if(data){
                    $('#activity_shift_modal').modal('hide');
                    OtableActivityShift.draw();
                }
                
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });

    }

    function saveDailyReportDetail(){

        //invoice_item_detail_form
        var data = $('#daily_report_form').serializeArray();

        data.push({'name': 'job_id', 'value': $('#job_id').val()});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Jobs/save_daily_report_detail'); ?>",
            beforeSend: function () {
                startLoad();
            
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
                if(data){
                    $('#daily_report_modal').modal('hide');
                    OtableDailyReport.draw();
                }
                
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });

    }

    function saveBillingPeriodDetail(){
        
        var data = $('#period_billing_form').serializeArray();

        data.push({'name': 'job_id', 'value': $('#job_id').val()});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Jobs/save_billing_header'); ?>",
            beforeSend: function () {
                startLoad();
            
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
                if(data){
                    $('#add_billing_modal').modal('hide');
                    
                    if(ticketTemplateType=='Standard'){
                        OtableBilling_standard.draw();
                    }else{

                        if(isBillingModifyMode ==1){
                            daily_billing_table_modify();
                        }else{
                            OtableBilling.draw();
                        }
                        
                    }
                }
                
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });

    }

    function add_asset_for_job(faID){
        
        job_id = $('#job_id').val();
        contract = $('#contract').val();

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': job_id,'faID':faID,'contract':contract
                },
                url: "<?php echo site_url('Jobs/add_assets_job'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);
                    if(data){
                    
                        OtableAsset.draw();
                        OtableAssetContract.draw();
                        // $("#crew_detail_modal").modal('hide');
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }
    }

    function update_total_hours(){

        job_id = $('#job_id').val();
        var shift_id = $('#shift_id').val();

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': job_id,
                    'shift_id': shift_id
                },
                url: "<?php echo site_url('Jobs/get_total_hours'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);
                    if(data){
                    
                        if(data){
                            $('#total_hours').html('<br><b>'+data.total_hours+' Hours '+data.total_minutes+' Minutes </b>');
                            $('#total_standby_hours').html('<br><b>'+data.standby_hours+' Hours '+data.standby_minutes+' Minutes </b>');
                            $('#total_npt_hours').html('<br><b>'+data.npt_hours+' Hours '+data.npt_minutes+' Minutes </b>');
                        }
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }

    }

    function delete_added_record(id,table){
        
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "Are you sure to delete this",/*You want to delete this customer!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id,'table': table},
                    url: "<?php echo site_url('Jobs/remove_added_record'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                      
                        if(table == 'srp_erp_job_crewdetail'){
                            Otable1.draw();
                        }else if(table == 'srp_erp_job_billing_detail'){
                            
                            if(ticketTemplateType=='Standard'){
                                OtableBillingAddedDetailStandard.draw();
                            }else{

                                if(isBillingModifyMode ==1){
                                    billing_added_detail_table_modify();
                                }else{
                                    OtableBillingAddedDetail.draw();
                                    OtableBilling.draw();
                                }
                                
                            }
                            
                        }else if(table == 'srp_erp_job_dailyreport'){
                            OtableDailyReport.draw();
                        }else if(table == 'srp_erp_job_shift_crewdetail'){
                            OtableActivityCrew.draw();
                        }else if(table == 'srp_erp_job_activityshift'){
                            OtableActivityShift.draw();
                        }else if(table == 'srp_erp_op_pipe_tally'){
                            Otablepipe.draw();
                        }else if(table == 'srp_erp_job_fueldetails'){
                            Otablefuel.draw();
                        }else if(table == 'srp_erp_op_visitors_log'){
                            Otablevlog.draw();
                        }else if(table == 'srp_erp_job_billing'){
                            
                            
                            if(ticketTemplateType=='Standard'){
                                OtableBilling_standard.draw();
                            }else{
                                OtableBilling.draw();
                            }
                            
                        }else{
                            if(ticketTemplateType=='Standard'){
                                OtableBilling_standard.draw();
                            }

                            if(isBillingModifyMode ==1){
                                daily_billing_table_modify();
                            }
                            OtableItem.draw();
                            OtableAsset.draw();
                            OtableActivity.draw();
                            
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function call_confirm_view(){
        job_id = $('#job_id').val();

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    'job_id': job_id,
                    'printtype':'html'
                },
                url: "<?php echo site_url('Jobs/get_job_confirmation_view'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);
                    $('#job_confirmation_area').empty();
                    $('#job_confirmation_area').html(data);

                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }

    }

    function load_checklist_edit(header_id){

        job_id = $('#job_id').val();

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    'job_id': job_id,
                    'header_id':header_id,
                    'printtype':'html'
                },
                url: "<?php echo site_url('Jobs/get_load_check_list'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);
                    $('#checklist_view').empty();
                    $('#checklist_view').html(data);

                    $("#checklist_view_modal_response").modal({backdrop: "static"});

                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }

    }

    function store_checklist_response(header_id){
        alert(header_id);
    }

    function show_add_file() {
        $('#add_attachemnt_show').removeClass('hide');
    }

    function document_uplode() {
        var formData = new FormData($("#opportunity_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Jobs/job_attachement_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id').click();
                    $('#opportunityattachmentDescription').val('');
                    op_job_attachments();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function document_uplode_well() {
        var formData = new FormData($("#job_attachment_uplode_form_one")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Jobs/job_attachement_well_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id').click();
                    //$('#opportunityattachmentDescription').val('');
                    op_job_attachments_well();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function document_uplode_bob() {
        var formData = new FormData($("#job_attachment_uplode_form_two")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Jobs/job_attachement_bob_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id1').click();
                    //$('#opportunityattachmentDescription').val('');
                    op_job_attachments_bob();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function op_job_attachments() {
        var job_id = $('#job_id').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {job_id: job_id},
            url: "<?php echo site_url('Jobs/load_job_all_attachments'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_attachments').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function op_job_attachments_well() {
        var job_id = $('#job_id').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {job_id: job_id},
            url: "<?php echo site_url('Jobs/load_job_all_attachments_well'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_attachments_well').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function op_job_attachments_bob() {
        var job_id = $('#job_id').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {job_id: job_id},
            url: "<?php echo site_url('Jobs/load_job_all_attachments_bob'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#show_all_attachments_bob').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_job_attachment(id, fileName) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'id': id, 'fileName': fileName},
                    url: "<?php echo site_url('Jobs/delete_job_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            op_job_attachments();
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function open_daily_job_report(shift_id){
        $('#daily_job_report_view_modal').modal('show');
        load_daily_job_report(shift_id);
    }

    function load_daily_job_report(shift_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Jobs/load_daily_job_report"); ?>',
            dataType: 'html',
            data: {'id': shift_id,'html':'yes'},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                //$('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }    

    function open_billing_detail_report(shift_id){
        $('#billing_detail_report_view_modal').modal('show');
        load_print_billing_detail_report(shift_id);
    }

    function load_print_billing_detail_report(shift_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Jobs/load_print_billing_detail_report"); ?>',
            dataType: 'html',
            data: {'id': shift_id,'html':'yes'},
            async: false,
            success: function (data) {
                $('#billing_detail_report_view').html(data);
                //$('#checklist_view_modal .btn-primary-new').hide();
                var document_confirmed = $('#activity_confirmed_yn').val();

                if(document_confirmed == 1){
                    $('#btn-confirm-billing').css('display','none');
                }else{
                    $('#btn-confirm-billing').css('display','block');
                }

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }  

    function print_well_report(header_id){       
        // window.open("<?php //echo site_url('Jobs/load_daily_job_report_print') ?>");
        window.open("<?php echo site_url('Jobs/load_well_report') ?>"+'/'+header_id);
    }

    function print_work_over_rig_daily_report(header_id){       
        // window.open("<?php //echo site_url('Jobs/load_daily_job_report_print') ?>");
        window.open("<?php echo site_url('Jobs/load_work_over_rig_daily_report') ?>"+'/'+header_id);
    }

    function print_daily_job_report(header_id){       
        // window.open("<?php //echo site_url('Jobs/load_daily_job_report_print') ?>");
        window.open("<?php echo site_url('Jobs/load_daily_job_report') ?>"+'/'+header_id);
    }

    function print_checklist(header_id){
        window.open("<?php echo site_url('Jobs/get_load_check_list')?>"+ '/' +header_id);
    }

    function print_billing(header_id){
        window.open("<?php echo site_url('Jobs/load_print_billing_detail_report')?>"+ '/' +header_id);
    }

    function print_billing_standard(header_id){
        window.open("<?php echo site_url('Jobs/load_print_billing_detail_report_standard')?>"+ '/' +header_id);
    }

    function print_billing_modify(header_id){
        window.open("<?php echo site_url('Jobs/load_print_billing_detail_report_modify')?>"+ '/' +header_id);
    }

    function change_activity_billing(ev){

        job_id = $('#job_id').val();
        var activity_id = ev.val();

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': job_id,
                    'activity_id': activity_id
                },
                url: "<?php echo site_url('Jobs/get_activity_details'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);
                    if(data){
                        ev.closest('tr').find('.billing_description').val(data.description).prop('readonly',true);;
                        ev.closest('tr').find('.billing_fromDate').val(data.dateFrom).prop('readonly',true);
                        ev.closest('tr').find('.billing_toDate').val(data.dateTo).prop('readonly',true);
                        ev.closest('tr').find('.billing_isStandby').val((data.isStandby == 1) ? 'Yes' : 'No').prop('readonly',true);
                        ev.closest('tr').find('.billing_isNPT').val((data.isNpt == 1) ? 'Yes' : 'No').prop('readonly',true);
                        ev.closest('tr').find('.billing_hours_qty').val(data.hours).prop('readonly',true);
                        ev.closest('tr').find('.hid_isStandby').val((data.isStandby == 1) ? '1' : '0').prop('readonly',true);
                        ev.closest('tr').find('.hid_isNpt').val((data.isNpt == 1) ? '1' : '0').prop('readonly',true);

                        var price = ev.closest('tr').find('.billing_price').val();

                        ev.closest('tr').find('.billing_price').val(price).change();

                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

            }

    }

    function change_pricing_billing(ev){

        job_id = $('#job_id').val();
        var price_id = ev.val();
        var price_text = ev.find('option:selected').text();

        var product_or_service = price_text.split(' | ');
  

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': job_id,
                    'price_id': price_id
                },
                url: "<?php echo site_url('Jobs/get_price_details'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);

                    if(data){
                        ev.closest('tr').find('.billing_rate').val(data.unittransactionAmount).prop('disabled',false).prop('readonly',true);
                        var hour_min = ev.closest('tr').find('.billing_hours_qty').val();

                        if(product_or_service[1] == 'Product'){
                            ev.closest('tr').find('.billing_qty').val(0).prop('disabled',false).prop('readonly',false);
                        }else{
                            ev.closest('tr').find('.billing_qty').val(hour_min).prop('disabled',false).prop('readonly',true);
                        }

                        var qty = ev.closest('tr').find('.billing_qty').val();
                        var total = qty * data.unittransactionAmount;

                        ev.closest('tr').find('.billing_rate_total').val(total).prop('disabled',false).prop('readonly',true);
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }


    }

    function change_pricing_billing_modify(ev,sh_id){

        job_id = $('#job_id').val();
        var price_id = ev.val();
        var price_text = ev.find('option:selected').text();

        var product_or_service = price_text.split(' | ');


        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'job_id': job_id,
                    'price_id': price_id
                },
                url: "<?php echo site_url('Jobs/get_price_details'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);

                    if(data){
                        ev.closest('tr').find('.rate_modify').val(data.unittransactionAmount).prop('disabled',false).prop('readonly',true);
                        var hour_min = ev.closest('tr').find('.min_modify').val();

                        //if(product_or_service[1] == 'Product'){
                            ev.closest('tr').find('.qty_modify').val(data.requestedQty).prop('disabled',false).prop('readonly',true);
                        //}else{
                          //  ev.closest('tr').find('.qty_modify').val(hour_min).prop('disabled',false).prop('readonly',false);
                        //}

                        var qty = ev.closest('tr').find('.qty_modify').val();
                        var total = qty * data.unittransactionAmount;

                        ev.closest('tr').find('.total_modify').val(total).prop('disabled',false).prop('readonly',false);
                    }
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }


    }

    

    function change_requested_modify_qty(ev){
        var qty = ev.val();
        var billing_rate = ev.closest('tr').find('.rate_modify').val();
        ev.closest('tr').find('.qty_modify').val(qty).prop('disabled',false).prop('readonly',true);
        var total = qty * billing_rate;

        ev.closest('tr').find('.total_modify').val(total).prop('disabled',false).prop('readonly',true);
    }  

    function change_price_product_qty(ev){
        var qty = ev.val();
        var billing_rate = ev.closest('tr').find('.billing_rate').val();
        var total = qty * billing_rate;

        ev.closest('tr').find('.billing_rate_total').val(total).prop('disabled',false).prop('readonly',true);
    }   

    function saveBillingDetailForm(){

        var data = $('#billing_detail_form').serializeArray();

        data.push({'name': 'job_id', 'value': $('#job_id').val()});
        data.push({'name': 'billing_header_id', 'value': $('#billing_header_id').val()});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Jobs/save_billing_detail_item'); ?>",
            beforeSend: function () {
                startLoad();
            
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
                if(data){
                    $('#add_billing_element_detail_modal').modal('hide');
                    $('#billing_detail_form')[0].reset();
                    OtableBillingAddedDetail.draw();
                    OtableBilling.draw();
                }
                
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });

    }

    function load_billing_detail_activity(){
        
        job_id = $('#job_id').val();
        billing_header_id = $('#billing_header_id').val();

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                data: {
                    'job_id': job_id,'billing_header_id':billing_header_id
                },

                url: "<?php echo site_url('Jobs/load_billing_detail_section'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    
                    stopLoad();
                    refreshNotifications(true);
                    $('#billing_detail_section_modal').empty();
                    $('#billing_detail_section_modal').html(data);
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }

    }

    function open_Check_list_model_job(search=null) {
        var id = $('#job_id').val();
       var search_index = '';
        
        search_index  = search;
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {Search:search_index,job_id:id},
            url: "<?php echo site_url('Jobs/assignItem_checklist_view_job'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
       
                $('#assignChecklist_item_Content_job').html(data);
           
                $("#assignChecklist_model_job").modal({backdrop: "static"});
                 
            
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function startMasterSearch(){ 
       // var itemAutoID =  $('#assignedSupplier_itemID').val();
        var search = $('#searchOrder').val();
        open_Check_list_model_job(search);
    }

    function assign_checklist_selected_check(sup) {
       
       var value = $(sup).val();
       if ($(sup).is(':checked')) {
           var inArray = $.inArray(value, assignCheckListSync);
           if (inArray == -1) {
               assignCheckListSync.push(value);
           }
       }
       else {
           var i = assignCheckListSync.indexOf(value);
           if (i != -1) {
               assignCheckListSync.splice(i, 1);
           }
       }
   }

    function assign_checklist() {
        var id = $('#job_id').val();
        if(id && assignCheckListSync.length>0){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'assignCheckListSync': assignCheckListSync,
                    'job_id':id,
                },
                url: "<?php echo site_url('Jobs/assignCheckListForContract_job'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                    // generate_order_itemView();
                    fetch_assign_checklist_table() ;
                        assignCheckListSync =[];
                        $("#assignChecklist_model_job").modal('hide');
                    } else {

                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }else{
            myAlert('e', 'please select checklist');
        }
        
    }

    function fetch_assign_checklist_table(){
        var id = $('#job_id').val();

        if(id){
            Otable = $('#job_checklist_table').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Jobs/fetch_check_list_job'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {
                
                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }

                },
                "aoColumns": [
                    {"mData": "id"},
                    {"mData": "doc_code"},
                    {"mData": "doc_name"},
                    {"mData": "doc_status"},
                    {"mData": "action"},
                
                
                ],
                "columnDefs": [],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "job_id", "value": id});
                
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                        });
                    }
            });

        }

    }


    function visitors_log_added_table(){

        var id = $('#job_id').val();

        if(id){
            Otablevlog = $('#visitors_log_added_table').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Jobs/fetch_added_visitors_log_details'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {
                
                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }

                },
                "aoColumns": [
                    {"mData": "id"},
                    {"mData": "date"},
                    {"mData": "full_name"},
                    {"mData": "full_company"},
                    {"mData": "position"},
                    {"mData": "purpose_visit"},
                    {"mData": "mobile_no"},
                    {"mData": "medication"},
                    {"mData": "h2s_validity"},
                    {"mData": "safety_briefing"},
                    {"mData": "proper_ppe"},
                    {"mData": "time_in"},
                    {"mData": "time_out"},
                    {"mData": "action"}

                ],
                "columnDefs": [],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "job_id", "value": id});
                
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                        });
                    }
            });
        }
    }

    function pipe_tally_table(){

        var id = $('#job_id').val();

        if(id){
            Otablepipe = $('#pipe_tally_table').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": true,
                "sAjaxSource": "<?php echo site_url('Jobs/fetch_pipe_tally_details'); ?>",
                "aaSorting": [[0, 'desc']],
                "fnInitComplete": function () {
                
                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }

                },
                "aoColumns": [
                    {"mData": "id"},
                    {"mData": "running_number"},
                    {"mData": "od_inches"},
                    {"mData": "item_length"},
                    {"mData": "cum_length"},
                    {"mData": "landing_depth_bottom"},
                    {"mData": "action"},    

                ],
                "columnDefs": [
                    { className: "text-right", "targets": [3,4,5] }
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "job_id", "value": id});
                
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                        });
                    }
            });
        }
}

    

    function delete_job_manual_checklist(id){
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'id': id},
                        url: "<?php echo site_url('Jobs/delete_job_manual_checklist'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_assign_checklist_table();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function load_pre_job_checklist(){

        job_id = $('#job_id').val();

        if(job_id){

            $.ajax({
            async: true,
            type: 'post',
            //dataType: 'json',
            data: {
                'job_id': job_id,
                'type':'detail'
            },
            url: "<?php echo site_url('Jobs/fetch_prejob_checklists'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                refreshNotifications(true);
                stopLoad();

                $('#pre_job_checklist_area').empty();
                $('#pre_job_checklist_area').html(data);
                $('#pre_job_checklist_area').removeClass('hide');
                
            },
            error: function() {
                swal("Cancelled", "Your file is safe :)", "error");
                stopLoad();
            }
        });

        }



    }

    function showHideAttachment(){
        // well and BOB attachment hide and show when job_id is set to value
        var jobid= $("#job_id").val();
        if(jobid){
            $('#well_program_att').removeClass('hide');
            $('#bob_chart_recorder_att').removeClass('hide');
        } else{
            $('#well_program_att').addClass('hide');
            $('#bob_chart_recorder_att').addClass('hide');
        }    
    }


    function open_contract_checklist(id) {
        if(id == 3){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 4){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 2){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 5){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 6){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 7){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 8){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        }  else{
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        }
        
    }

    function load_checklist_single(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_single"); ?>',
            dataType: 'html',
            data: {'id': id},
            async: false,
            success: function (data) {
                $('#checklist_view_modal_body').html(data);                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }
    //load other checklist
    function load_other_checklist(){
        $('#checklist_users_add').removeClass('hide');
        job_id = $('#job_id').val();

        if(job_id){

            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {
                    'job_id': job_id,
                    'type':'detail'
                },
                url: "<?php echo site_url('Jobs/fetch_other_checklists'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    refreshNotifications(true);
                    stopLoad();

                    // $('#pre_job_checklist_area').empty();
                    // $('#pre_job_checklist_area').html(data);
                    // $('#pre_job_checklist_area').removeClass('hide');
                    
                },
                error: function() {
                    swal("Cancelled", "Your file is safe :)", "error");
                    stopLoad();
                }
            });

        }

    }

function crew_select_add(crewID){
        
        var job_id = $('#job_id').val();
        var crew_drop = $('#group_jobcrew').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                    'job_id': job_id,'crew_id':crewID,'group_jobcrew':crew_drop
                },
            url: "<?php echo site_url('Jobs/add_crew_multiple'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                
                stopLoad();
                //refreshNotifications(true);

                jQuery.each(data, function(index,value){
                    if(value){
                        myAlert(index, value, 1000);
                    }
                });

                if(data){
                    crew_select_contract_table();
                    crew_added_table();
                }
                
            },
            error: function() {
                swal("Cancelled", "Your file is safe :)", "error");
                stopLoad();
            }
        });


}

function crew_select_add_common(){
        
        var data = $('#crew_contract_common_form').serializeArray();
        data.push({'name': 'job_id', 'value': $('#job_id').val()});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Jobs/add_crew_multiple'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                
                stopLoad();
                //refreshNotifications(true);

                jQuery.each(data, function(index,value){
                    if(value){
                        myAlert(index, value, 1000);
                    }
                });

                if(data){
                    Otable1.draw();
                    // $("#crew_detail_modal").modal('hide');
                }
                
            },
            error: function() {
                swal("Cancelled", "Your file is safe :)", "error");
                stopLoad();
            }
        });


}

function add_job_group(type){
    $('#groupType').val(type);
    $("#groupTo_add_modal").modal({backdrop: "static"});
}

function saveGroupToDetails(){

    var data = $('#crew_group_form').serializeArray();

    data.push({'name': 'job_id', 'value': $('#job_id').val()});
    data.push({'name': 'contractAutoID', 'value': $('#contract').val()});

    $.ajax({
        async: true,
        type: 'post',
        data: data,
        url: "<?php echo site_url('Quotation_contract/save_contract_group_to'); ?>",
        beforeSend: function() {
            startLoad();
        },
        success: function(data) {
            refreshNotifications(true);
            stopLoad();

            group_job_select(3);
            
        },
        error: function() {
            swal("Cancelled", "Your file is safe :)", "error");
            stopLoad();
        }
    });

}

    
    
function fuel_utilization_table(){
    var id = $('#job_id').val();
    if(id){
    Otablefuel = $('#fuel_utilization_table').DataTable({
        "language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
        "bProcessing": true,
        "bServerSide": true,
        "bDestroy": true,
        "bStateSave": true,
        "sAjaxSource": "<?php echo site_url('Jobs/fetch_fuel_utilization_details'); ?>",
        "aaSorting": [[0, 'desc']],
        "fnInitComplete": function () {
        
        },
        "fnDrawCallback": function (oSettings) {
            $("[rel=tooltip]").tooltip();
            var tmp_i = oSettings._iDisplayStart;
            var iLen = oSettings.aiDisplay.length;
            var x = 0;
            for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                x++;
            }
            var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=  null;

                api.column(1, {page:'current'} ).data().each( function ( group, i) {
                    if ( last !== group ) {
                        
                        var rowData = api.row(i).data();        
                        $(rows).eq(i).before(
                        
                        '<tr class="group"><td colspan="5"><b><i class="fa fa-long-arrow-right" aria-hidden="true" style="margin-left:22px;"></i> '+group+'</b></td></tr>'
                       );

                       last = group;
                    }
                } 
                );

        },
        "aoColumns": [
            {"mData": "id"},
            {"mData": "fueldes"},
            {"mData": "DocumentID"},
            {"mData": "Documentdate"},
            {"mData": "description"},
            {"mData": "UoM"},
            {"mData": "receivedQty"},
            {"mData": "issuedQty"},
            {"mData": "balance"},  
            {"mData": "usernameemp"},
            {"mData": "action"},

        ],
        "columnDefs": [
            { className: "text-right", "targets": [3,4,5,6,7,8,] }
        ],
        "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "job_id", "value": id});
        
            $.ajax({
                'dataType': 'json',
                'type': 'POST',
                'url': sSource,
                'data': aoData,
                'success': fnCallback
                });
            }
    });
    }
}

function linkemployee() {
    $('#fuelemployee').val('');
    $('#linkemployee_text').removeClass('hide');
    $('#Employee_text').addClass('hide');
    refreshNotifications(true);
}

function unlinkemployee() {
    $('#linkemployee_text').addClass('hide');
    $('#Employee_text').removeClass('hide');
    refreshNotifications(true);
}

function rlinkemployee() {
    $('#fuelemployee').val('');
    $('#linkemployee_text_issue').removeClass('hide');
    $('#Employee_text_issue').addClass('hide');
    refreshNotifications(true);
}

function runlinkemployee() {
    $('#linkemployee_text_issue').addClass('hide');
    $('#Employee_text_issue').removeClass('hide');
    refreshNotifications(true);
}

function savefuelreceiptDetail(){

    var data = $('#fuel_recived_detail_form').serializeArray();

    data.push({'name': 'job_id', 'value': $('#job_id').val()});

    $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Jobs/save_fuel_recived_detail'); ?>",
            beforeSend: function () {
                startLoad();
            
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                if(data){

                    $('#fuel_receipt_detail_modal').modal('hide');
                    $('#fuel_recived_detail_form')[0].reset();
                    refreshNotifications(true);
                    Otablefuel.draw();
                }
                
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });

}

function savefuelissueDetail(){
    var data = $('#fuel_Issue_detail_form').serializeArray();

    data.push({'name': 'job_id', 'value': $('#job_id').val()});

    $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Jobs/save_fuel_Issue_detail'); ?>",
            beforeSend: function () {
                startLoad();
            
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
                if(data){
                    $('#fuel_Issue_detail_model').modal('hide');
                    $('#fuel_Issue_detail_form')[0].reset();
                    Otablefuel.draw();
                }
                
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
}

function savePipeTallyDetail(){

    //invoice_item_detail_form
    var data = $('#pipe_tally_detail_form').serializeArray();

    data.push({'name': 'job_id', 'value': $('#job_id').val()});

    $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Jobs/save_pipe_tally_detail'); ?>",
            beforeSend: function () {
                startLoad();
            
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
                if(data){
                    $('#pipe_tally_detail_modal').modal('hide');
                    $('#pipe_tally_detail_form')[0].reset();
                    Otablepipe.draw();
                }
                
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });

}

function saveVisitorLogDetail(){

    //invoice_item_detail_form
    var data = $('#visitor_log_detail_form').serializeArray();

    data.push({'name': 'job_id', 'value': $('#job_id').val()});

    $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Jobs/save_visitor_log_detail'); ?>",
            beforeSend: function () {
                startLoad();
            
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
                if(data){
                    $('#visitor_log_detail_modal').modal('hide');
                    $('#visitor_log_detail_form')[0].reset();
                    Otablevlog.draw();
                }
                
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });

}

function loadSelectOptionDrop(){
    // $('#select_hod_emp').select2();
    $('.select_user').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });

    $('.select_user_edit').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        numberDisplayed: 1,
        buttonWidth: '180px',
        maxHeight: '30px'
    });
}

function open_checklist_user_assign_model(){
    fetch_assign_checklist_user_job();
    $('#checklist_user_model').modal('show');
}

function fetch_assign_checklist_user_job(){
    setTimeout(loadSelectOptionDrop, 500);

    var jobid= $('#job_id').val();
    if (jobid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'job_id': jobid, 'html': true},
            url: "<?php echo site_url('Jobs/fetch_check_list_contract_job'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#checklist_user_section').html(data);
                
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }

}

    
function selectChecklistUserUpdate(ths,id,type){



        if(type==1){
            var users = $(ths).closest('tr').find('.select_user').val();
        }
        else{
            var users = $(ths).closest('tr').find('.select_user_edit').val();
        }
        
        if(users){
            $.ajax({
                async : true,
                url :"<?php echo site_url('Jobs/selectChecklistUserUpdate'); ?>",
                type : 'post',
                dataType : 'json',
                data : {'users':users,'masterID':id,'type':type},
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if( data[0] == 's'){ 
                        //fetch_assign_checklist_table();
                    }
                },error : function(){
                    stopLoad();
                    myAlert('e', 'error');
                }
            });
        }

        
}

function confirmBilling(){

    

    var document_confirmed = $('#activity_confirmed_yn').val();

    swal({
        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
        text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to delete this record*/
        type: "warning",/*warning*/
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Delete*/
        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
    },
    function () {
        $.ajax({
            async: true,
            type: 'post',
            // dataType: 'json',
            data: {'billing_id': $('#activity_billing_id').val(),'job_id':$('#job_id').val(),'confirmedYN':1},
            url: "<?php echo site_url('Jobs/confirm_billing'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                daily_billing_table();
            }, error: function () {
                startLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    });

}

function generate_sales_order(id){

    swal({
        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
        text: "<?php echo $this->lang->line('common_you_want_to_generate_sales_order');?>",/*You want to delete this record*/
        type: "warning",/*warning*/
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Delete*/
        cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
    },
    function () {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'billing_id': id,'job_id':$('#job_id').val()},
            url: "<?php echo site_url('Jobs/generate_sales_order'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);

                if(ticketTemplateType=='Standard'){
                    OtableBilling_standard.draw();
                }else{
                    if(isBillingModifyMode==1){
                        daily_billing_table_modify();
                    }else{
                        daily_billing_table();
                    }
                    
                }
               
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    });

}

function edit_billing_detail(id){

    $('#final_billing_id').val(id);

    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'billing_id': id},
        url: "<?php echo site_url('Jobs/edit_billing_details'); ?>",
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            refreshNotifications(true);
            $('#billingFromDate').val(data.dateFrom).change();
            $('#billingToDate').val(data.dateTo).change();
            $('#billingDescription').val(data.description).change();
            $('#billing_code').val(data.code).change();
            $('#billing_code').prop('readonly',true);

            $("#add_billing_modal").modal({backdrop: "static"});

        }, error: function () {
            stopLoad();
            swal("Cancelled", "Your file is safe :)", "error");
        }
    });
}

function edit_dailyreport_detail(id){

$('#daily_report_id').val(id);

$.ajax({
    async: true,
    type: 'post',
    dataType: 'json',
    data: {'report_id': id},
    url: "<?php echo site_url('Jobs/edit_report_details'); ?>",
    beforeSend: function () {
        startLoad();
    },
    success: function (data) {
        stopLoad();
        refreshNotifications(true);
        $('#reportFromDate').val(data.dateFrom).change();
        $('#reportToDate').val(data.dateTo).change();
        $('#description').val(data.description).change();

        $("#daily_report_modal").modal({backdrop: "static"});

    }, error: function () {
        stopLoad();
        swal("Cancelled", "Your file is safe :)", "error");
    }
});
}


function add_online_link_request(type){
    visitor_log_request();
    $("#link_online_request_modal").modal({backdrop: "static"});
}

function add_visitor_log_request(){
     
    var data = $('#visitor_log_online_request_form').serializeArray();

    data.push({'name': 'job_id', 'value': $('#job_id').val()});

    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: data,
        url: "<?php echo site_url('Jobs/save_visitor_log_request'); ?>",
        beforeSend: function () {
            startLoad();
        
        },
        success: function (data) {
            stopLoad();
            refreshNotifications(true);
            $('#visitor_log_online_request_form')[0].reset();
            OtableVisitorRequest.draw();
            
        }, error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            stopLoad();
            myAlert(data[0], data[1]);
        }
    });
}

function visitor_log_request(){

    var id = $('#job_id').val();

    if(id){
        OtableVisitorRequest = $('#visitor_log_request').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_visitor_request'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "empName"},
                {"mData": "empEmail"},
                {"mData": "empMessage"},
                {"mData": "createdDate"},
                {"mData": "status"},
                {"mData": "action"},    

            ],
            "columnDefs": [
                { className: "text-right", "targets": [3,4,5] }
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": id});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
    }
}

function get_visitor_log_link(id){

    $.ajax({
        async: true,
        type: 'post',
        //dataType: 'json',
        data: {"id":id,"type":"link"},
        url: "<?php echo site_url('Jobs/get_setup_visitor_log_details'); ?>",
        beforeSend: function () {
            startLoad();
        
        },
        success: function (data) {
            stopLoad();
            refreshNotifications(true);
            $('#setLinkForShare').text(data);
            $("#load_share_link_modal").modal({backdrop: "static"});
            
        }, error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            stopLoad();
            myAlert(data[0], data[1]);
        }
    });

}

function get_external_link_checklist(id){

    $.ajax({
        async: true,
        type: 'post',
        //dataType: 'json',
        data: {"id":id,"type":"checklist"},
        url: "<?php echo site_url('Jobs/get_setup_checklist_link'); ?>",
        beforeSend: function () {
            startLoad();
        
        },
        success: function (data) {
            stopLoad();
            refreshNotifications(true);
            $('#setLinkForShare').text(data);
            $("#load_share_link_modal").modal({backdrop: "static"});
            
        }, error: function () {
            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            stopLoad();
            myAlert(data[0], data[1]);
        }
    });

    
}

function load_common_assest(search=null) {
       var search_index = '';
        var contract_id_common =$('#contract').val();
        var job_id_common =$("#job_id").val();
        search_index  = search;
        $('#assign_common_asset').html('');

        if(contract_id_common && job_id_common){
            $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {Search:search_index,contractAutoID:contract_id_common,jobID:job_id_common},
            url: "<?php echo site_url('Jobs/assign_assets_common_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
       
                $('#assign_common_asset').html(data);
           
                //$("#assignChecklist_model").modal({backdrop: "static"});
                 $('#common_asset_btn').removeClass('hide');
            
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
            });
        }else{
            myAlert('e', 'Contract Not Found');
        }
        
    }

    function search_asset_common(){ 
       // var itemAutoID =  $('#assignedSupplier_itemID').val();
        var search = $('#searchOrder_asset').val();
        load_common_assest(search);
    }

    function assign_asset_common_selected_check(sup) {
       
       var value = $(sup).val();
       if ($(sup).is(':checked')) {
           var inArray = $.inArray(value, assignAssetListSync);
           if (inArray == -1) {
            assignAssetListSync.push(value);
           }
       }
       else {
           var i = assignAssetListSync.indexOf(value);
           if (i != -1) {
            assignAssetListSync.splice(i, 1);
           }
       }
   }

    function assign_job_common_assets() {
        var id = $('#job_id').val();
        if(id && assignAssetListSync.length>0){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'assignAssetListSync': assignAssetListSync,
                    'job_id':id,
                },
                url: "<?php echo site_url('Jobs/assignCommon_AssetListForContract_job'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                    // generate_order_itemView();
                    $("#assets_detail_modal").modal('hide');
                        OtableAsset.draw();
                        OtableAssetContract.draw();
                        assignAssetListSync =[];
                        
                    } else {

                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }else{
            myAlert('e', 'please select checklist');
        }
        
    }
   
</script>


<script>
$(document).ready(function () {
    flatpickr("#shiftFromDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });
    flatpickr("#shiftToDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });

    flatpickr("#billingFromDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });
    flatpickr("#billingToDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });

    flatpickr("#reportFromDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });
    flatpickr("#reportToDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });

    flatpickr("#fromDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });
    flatpickr("#toDate", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true
    });
    flatpickr("#start_time", {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i", // Time format: 24-hour format
      time_24hr: true // Enables 24-hour time format
    });
});    

function open_billing_standard_item_model(search=null) {
       var search_index = '';
       $("#add_billing_data_standard_item_model").modal({backdrop: "static"});
        search_index  = search;
        
        OtableStandard = $('#add_billing_data_standard_item_model_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_item_details_from_contact'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "contractDetailsAutoID"},
                {"mData": "typeItemName", "bSearchable": true},
                {"mData": "itemReferenceNo", "bSearchable": true},
                {"mData": "UnitShortCode", "bSearchable": true},
                {"mData": "requestedQty", "bSearchable": true},
                {"mData": "unit_amount", "bSearchable": true},
                {"mData": "total_amount", "bSearchable": true},
                {"mData": "status"},
                {"mData": "checkbox"}

            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});

                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });

        
    }

    function contract_item_select_add_billing(contactDetailID){
        var id = $('#job_id').val();
        var billing_header_id_standard = $('#billing_header_id_standard').val();
        
        if(id){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'contactDetailID': contactDetailID,
                    'job_id':id,
                    'billing_header_id':billing_header_id_standard
                },
                url: "<?php echo site_url('Jobs/assign_contact_item_for_job_billing'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                    
                        OtableBillingAddedDetailStandard.draw();
                        
                    } else {

                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }else{
            myAlert('e', 'Job id not found');
        }
    }

    function billing_added_detail_table_standard(){
        

        OtableBillingAddedDetailStandard = $('#billing_added_datatbl_standard').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_billing_detail_added_standard'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "typeItemName"},
                {"mData": "description"},
                {"mData": "UnitShortCode"},
                {"mData": "editformdate"},
                {"mData": "edittodate"},
                {"mData": "editQty"},
                // {"mData": "isStandby"},
                // {"mData": "isNpt"},
                {"mData": "unit_amount"},
                {"mData": "total_amount"},
                {"mData": "action"},
             
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
                aoData.push({"name": "billing_header_id", "value": $("#billing_header_id_standard").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });

        setTimeout(function () {
            flatpickr(".d_from_standard", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true
            });
        }, 500);
       
    }

    function billing_added_detail_table_modify(){
        

        OtableBillingAddedDetailModify = $('#billing_added_datatbl_modify').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Jobs/fetch_billing_detail_added_modify'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "id"},
                {"mData": "description"},
                {"mData": "dateFrom"},
                {"mData": "dateTo"},
                {"mData": "movingCost"},
                {"mData": "additionalCost"},
                {"mData": "qty"},
                {"mData": "isStandby"},
                {"mData": "isNpt"},
                {"mData": "price_text"},
                {"mData": "unit_amount"},
                {"mData": "total_amount"},
                {"mData": "action"},
             
            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "job_id", "value": $("#job_id").val()});
                aoData.push({"name": "billing_header_id", "value": $("#billing_header_id_modify").val()});
            
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
        });
       
    }

    function save_standard_billing_item_qty(val,bill_id){

        
        var id_job = $('#job_id').val();
        if(bill_id && val){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'bill_id': bill_id,
                    'job_id':id_job,
                    'qty':val
                },
                url: "<?php echo site_url('Jobs/save_standard_billing_item_qty'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                    
                        OtableBillingAddedDetailStandard.draw();
                        
                    } else {

                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    }

    function open_billing_detail_report_standard(shift_id){
        $('#billing_detail_report_view_modal_standard').modal('show');
        load_print_billing_detail_report_standard(shift_id);
    }

    function load_print_billing_detail_report_standard(shift_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Jobs/load_print_billing_detail_report_standard"); ?>',
            dataType: 'html',
            data: {'id': shift_id,'html':'yes'},
            async: false,
            success: function (data) {
                $('#billing_detail_report_view_standard').html(data);
                //$('#checklist_view_modal .btn-primary-new').hide();
                var document_confirmed = $('#activity_confirmed_yn_standard').val();

                if(document_confirmed == 1){
                    $('#btn-confirm-billing-standard').css('display','none');
                }else{
                    $('#btn-confirm-billing-standard').css('display','block');
                }

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function save_standard_billing_date(date,bill_id,type){

        var id_job = $('#job_id').val();
        if(bill_id && date){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'bill_id': bill_id,
                    'job_id':id_job,
                    'type':type,
                    'date':date,
                },
                url: "<?php echo site_url('Jobs/save_standard_billing_date'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                    
                        OtableBillingAddedDetailStandard.draw();

                        setTimeout(function () {
                            flatpickr(".d_from_standard", {
                                enableTime: true,
                                dateFormat: "Y-m-d H:i",
                                time_24hr: true
                            });
                        }, 500);
                        
                    } else {

                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    }

    function confirmBillingStandard(){

    

        var document_confirmed = $('#activity_confirmed_yn_standard').val();

        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
            text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to delete this record*/
            type: "warning",/*warning*/
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                // dataType: 'json',
                data: {'billing_id': $('#activity_billing_id_standard').val(),'job_id':$('#job_id').val(),'confirmedYN':1,'comment':$('#comment_standard').val()},
                url: "<?php echo site_url('Jobs/confirm_billing_standard'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    $('#billing_detail_report_view_modal_standard').modal('hide');
                    OtableBilling_standard.draw();
                }, error: function () {
                    startLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });

    }

    function checkbox_changed_modify(ev, type) {
        if (type == 'isStandby_modify') {
            var value = ev.is(":checked") ? ev.val() : 0;
            ev.siblings("input[name='isStandby_modify[]']").val(value);
        } else if (type == 'isNpt_modify') {
            var value = ev.is(":checked") ? ev.val() : 0;
            ev.siblings("input[name='isNpt_modify[]']").val(value);

        }
    }

    function saveBillingItemOrderDetailModify() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#item_detail_form_billing_modify').serializeArray();
        if ($('#job_id').val()) {
            data.push({'name': 'job_id', 'value': $('#job_id').val()});
            data.push({'name': 'billing_id', 'value': $('#billing_header_id_modify').val()});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Jobs/save_item_order_detail_job_billing_modify'); ?>",
                beforeSend: function () {
                    startLoad();
                    // $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {
                    contractDetailsAutoID = null;
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                       // fetch_detail_table();
                        $('#add_billing_data_modify_add').modal('hide');
                        $('#item_detail_form_billing_modify')[0].reset();
                        billing_added_detail_table_modify();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        } else {
            swal({
                title: "Good job!",
                text: "You clicked the button!",
                type: "success"
            });
        }
    }

    function open_billing_detail_report_modify(shift_id){
        $('#billing_detail_report_view_modal_modify').modal('show');
        load_print_billing_detail_report_modify(shift_id);
    }

    function load_print_billing_detail_report_modify(shift_id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Jobs/load_print_billing_detail_report_modify"); ?>',
            dataType: 'html',
            data: {'id': shift_id,'html':'yes'},
            async: false,
            success: function (data) {
                $('#billing_detail_report_view_modify').html(data);
                //$('#checklist_view_modal .btn-primary-new').hide();
                var document_confirmed = $('#activity_confirmed_yn_modify').val();

                if(document_confirmed == 1){
                    $('#btn-confirm-billing-modify').css('display','none');
                }else{
                    $('#btn-confirm-billing-modify').css('display','block');
                }

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function confirmBillingModify(){
        var document_confirmed = $('#activity_confirmed_yn_modify').val();

        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
            text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to delete this record*/
            type: "warning",/*warning*/
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                // dataType: 'json',
                data: {'billing_id': $('#activity_billing_id_modify').val(),'job_id':$('#job_id').val(),'confirmedYN':1},
                url: "<?php echo site_url('Jobs/confirm_billing'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    daily_billing_table_modify();
                }, error: function () {
                    startLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });

    }
</script>
