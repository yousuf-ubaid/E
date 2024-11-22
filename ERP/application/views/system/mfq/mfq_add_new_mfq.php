<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();
$from = convert_date_format($this->common_data['company_data']['FYPeriodDateFrom']);
$currency_arr = all_currency_new_drop();
$current_date = current_format_date();
$segment = fetch_mfq_segment(true);
$bom = fetch_bill_of_material(true);
$page_id = isset($page_id) && $page_id ? $page_id : 0;
$employeedrop = load_employee_drop_mfq();
$crmSource = load_crm_source_mfq(true);
$country = load_srm_country_mfq(true);
$orderStatusArr=load_cus_inquiry_order_status(true);
$docStatusArr=load_cus_inquiry_document_status(true);
$rfqStatusArr=load_cus_inquiry_rfq_status(true);
$orderJobs = all_mfq_jobs_drop(true);
$catArr=load_cus_inquiry_category(true);
$submissionArr=load_cus_inquiry_submission_status(true);
$emp=getemployee();
$emp_dropdown = array('' => 'Select');
foreach ($emp as $employee) {
    $emp_dropdown[$employee['EIdNo']] = $employee['employee'];
}
$employeedrop_prp_eng = load_employee_drop_mfq(1);
$languageflowserve = getPolicyValues('LNG', 'All');
$flowserve = getPolicyValues('MANFL', 'All');
?>
<?php echo head_page($_POST["page_name"], false); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/typehead.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }
    .titledays {
        color: #aaa;
        font-size: 13px;
    }

    .tddata {
        color: #333;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }

    .arrow-steps .step.current {
        color: #fff !important;
        background-color: #657e5f !important;
    }

    .table-responsive {
        overflow: visible !important
    }

    .form-group .select2-container {
        position: relative;
        z-index: 2;
        float: left;
        width: 150%;
        margin-bottom: 0;
        display: table;
        table-layout: fixed;
    }

</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <div class="steps">       
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('manufacturing_customer_inquiry_simple') ?><!--Customer Inquiry--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('common_attachment') ?><!--Attachment--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="customer_inquiry_print();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('common_confirmation') ?><!--Confirmation--></span>
            </a>
    </div>        
</div>
<hr>
<div class="tab-content">
    <div class="tab-pane active" id="step1">
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="tab-content">
                    <div class="row">
                        <div class="col-md-12 animated zoomIn">
                            <form id="frm_customerInquiry" class="frm_customerInquiry" method="post">
                                <input type="hidden" id="ciMasterID" name="ciMasterID"
                                       value="<?php echo $page_id ?>">
                                <header class="head-title">
                                    <h2><?php echo $this->lang->line('manufacturing_customer_inquiry_information') ?><!--Customer Inquiry Information--> </h2>
                                </header>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-md-offset-0">
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_client') ?><!--Client--> </label>
                                            </div>

                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('mfqCustomerAutoID', all_mfq_customer_drop(), '', 'class="form-control select2" id="mfqCustomerAutoID" ');
                                                    ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-1">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(2)"
                                                      data-original-title="Status History"></span>
                                                <!-- statusNo-->
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_contact_person_name') ?><!--Contact Person Name--> </label>
                                            </div>

                                            <div class="form-group col-sm-6">
                                                <div class="input-req">

                                                    <input type="text" class="form-control" id="contactpersonname" name="contactpersonname">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-1">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(31)"
                                                      data-original-title="Status History"></span>
                                                <!-- statusNo-->
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_contact_phone_number') ?><!--Customer Phone Number--> </label>
                                            </div>

                                            <div class="form-group col-sm-6">
                                                <div class="input-req">

                                                    <input type="text" class="form-control number" id="customerphone" name="customerphone">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-1">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(32)"
                                                      data-original-title="Status History"></span>
                                                <!-- statusNo-->
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                            <?php if($languageflowserve =='FlowServe'){ ?>
                                                <label class="title">Service Type </label>
                                                <?php }else{ ?>
                                                    <label class="title"><?php echo $this->lang->line('manufacturing_type') ?><!--Manufacturing Type--> </label>
                                                    <?php }?>
                                            </div>

                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('manufacturingType', ['' => 'Select', '1' => 'Customer', '2' => 'In House','3'=>'Third Party & In House'], '', 'class="form-control select2" id="manufacturingType"');
                                                    ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-2">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(3)"
                                                      data-original-title="Status History"></span>
                                                <!-- statusNo-->
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_inquiry_date') ?><!--Inquiry Date--> </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                    <div class='input-group date filterDate' id="">
                                                        <input type='text' class="form-control"
                                                               name="documentDate"
                                                               id="documentDate"
                                                               value="<?php echo $current_date; ?>"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               readonly>
                                                        <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                                    </div>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <?php if($flowserve=='Micoda'){ ?>
                                                    <label class="title"><?php echo 'Committed Submission Date' ?><!--Actual Submission Date--> </label>
                                                <?php } else { ?>
                                                    <label class="title"><?php echo $this->lang->line('manufacturing_actual_submission_date') ?><!--Actual Submission Date--> </label>

                                                <?php } ?>
                                                </div>
                                            <div class="form-group col-sm-6">
                                                <?php 
                                                if($flowserve=='GCC'){?>
                                                    <div>
                                                        <div class='input-group' id="actualsubmissiondate">
                                                            <input type='text' class="form-control"
                                                                name="deliveryDate"
                                                                id="deliveryDate"
                                                                value=""
                                                                data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php
                                                }else{?>
                                                    <div class="input-req" title="Required Field">
                                                        <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                        <div class='input-group' id="actualsubmissiondate">
                                                            <input type='text' class="form-control"
                                                                name="deliveryDate"
                                                                id="deliveryDate"
                                                                value="<?php echo $current_date; ?>"
                                                                data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                        </div>
                                                        <!-- <span class="input-req-inner"></span> -->
                                                    </div>
                                                <?php }
                                                ?>
                                            </div>
                                            <div class="form-group col-sm-2">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(4)"
                                                      data-original-title="Status History"></span>
                                                <!-- statusNo-->
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_required_submission_date') ?><!--Required Submission Date--> </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                    <div class='input-group' id="plannedsubmissiondat">
                                                        <input type='text' class="form-control"
                                                               name="dueDate"
                                                               id="dueDate"
                                                               value="<?php echo $current_date; ?>"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                        <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                                                    </div>
                                                    <!-- <span class="input-req-inner"></span> -->
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-2">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(5)"
                                                      data-original-title="Required Submission Date History"></span>
                                                <!-- statusNo-->
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_customer_inquiry_simple') ?><!--Delay In Days--> </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <input type='text' class="form-control" name="delayindays"
                                                       id="delayindays" value="0" readonly/>
                                            </div>

                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_send_reminder_email') ?><!--Send Reminder Email--></label>
                                            </div>
                                            <div class="form-group col-sm-2">
                                                <input type='text' class="form-control" name="remainindays"
                                                       id="remainindays" value=" " onkeypress="return validateFloatKeyPress(this,event)">
                                            </div>
                                            <label class="titledays"><?php echo $this->lang->line('manufacturing_in_days') ?><!--In Days--></label>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title">Category </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('cat', $catArr, '0', 'class="form-control select2" id="cat"'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title">Submission Status </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('submission_status', $submissionArr, '0', 'class="form-control select2" id="submission_status"'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title">Order Status </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('order_status', $orderStatusArr, '0', 'class="form-control select2" id="order_status"'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <?php if($flowserve =='Micoda'||$flowserve =='GCC'){ ?>
                                            <div class="row" style="margin-top: 10px;">
                                                <div class="form-group col-sm-4">
                                                    <label class="title"><?php echo $this->lang->line("manufacturing_sales_manager")?><!-- Sales Manager--></label>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('SalesManagerID',$emp_dropdown,'','class="form-control select2" id="SalesManagerID" "'); 
                                                    ?>
                                                        <span class="input-req-inner"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if($flowserve =='FlowServe'){ ?>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title">JOB</label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('order_job', $orderJobs, '', 'class="form-control select2" id="order_job"'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <?php } ?>

                                        

                                        <!--<div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4 md-offset-2">
                                                <label class="title">Payment Terms </label>
                                            </div>
                                            <div class="form-group col-sm-8">
                                                <div class="input-req" title="Required Field">
                                                            <textarea class="form-control" id="paymentTerm"
                                                                      name="paymentTerm" rows="2"></textarea>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                        </div>-->
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_client_reference_no') ?><!--Client Reference No--> </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <input type="text" class="form-control" id="referenceNo"
                                                           name="referenceNo">
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-2">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(7)"
                                                      data-original-title="Status History"></span>
                                                <!-- statusNo-->
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_customer_email') ?><!--Customer Email--> </label>
                                            </div>

                                            <div class="form-group col-sm-6">
                                                <div class="input-req">

                                                    <input type="text" class="form-control" id="contactpersonemail" name="contactpersonemail">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-1">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(33)"
                                                      data-original-title="Status History"></span>
                                                <!-- statusNo-->
                                            </div>
                                        </div>


                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('common_currency') ?><!--Currency--> </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('currencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="currencyID" onchange="currency_validation(this.value,\'CI\')" required'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('common_description') ?><!--Description--> </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                            <textarea class="form-control" id="description"
                                                                      name="description" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-2">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(8)"
                                                      data-original-title="Status History"></span>
                                                <!-- statusNo-->
                                            </div>

                                        </div>
                                        <?php 
                                        if($flowserve!='GCC'){?>
                                            <div class="row" style="margin-top: 10px;">
                                                <div class="form-group col-sm-4">
                                                    <label class="title"><?php echo $this->lang->line('common_segment') ?><!--Segment--> </label>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <div class="input-req" title="Required Field">
                                                        <?php echo form_dropdown('DepartmentID', $segment,'', 'class="form-control select2" id="DepartmentID"  required'); ?>
                                                    </div>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                                <div class="form-group col-sm-2">
                                                    <span title="History" rel="tooltip"
                                                        class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(30)"
                                                        data-original-title="Segment History"></span>
                                                    <!-- statusNo-->
                                                </div>

                                            </div>
                                        <?php }?>
                                        

                                        <div class="row hide" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('common_status') ?><!--Status--> </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                    <?php echo form_dropdown('statusID', all_mfq_status(1), 1, 'class="form-control" id="statusID"'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-2">
                                                <span title="History" rel="tooltip"
                                                      class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(6)"
                                                      data-original-title="Status History"></span>
                                                <!-- statusNo-->
                                            </div>


                                        </div>

                                        <?php if($flowserve =="Micoda"){?>
                                            <div class="row" style="margin-top: 10px;">
                                                <div class="form-group col-sm-4">
                                                    <label class="title"><?php echo $this->lang->line('manufacturing_inquiry_type') ?><!--Inquiry Type--> </label>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <div class="input-req" title="Required Field">
                                                        <?php if($flowserve =='Micoda'){ ?>
                                                            <?php echo form_dropdown('type', array('' => 'Select', '1' => 'Tender', '2' => 'RFQ'), '2', 'class="form-control" id="type"'); ?>
                                                        <?php } else { ?>
                                                            <?php echo form_dropdown('type', array('' => 'Select', '1' => 'Tender', '2' => 'RFQ', '3' => 'SPC'), '2', 'class="form-control" id="type"'); ?>
                                                        <?php } ?>
                                                        <span class="input-req-inner"></span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-sm-2">
                                                    <span title="History" rel="tooltip"
                                                        class="fa fa-info-circle fa-1x fa-fw" onclick="open_history(9)"
                                                        data-original-title="Status History"></span>
                                                    <!-- statusNo-->
                                                </div>
                                            </div>
                                        <?php }?>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title">Operation</label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('micoda', $country,'', 'class="form-control select2" id="micoda"  required'); ?>
                                                </div>
                                                <span class="input-req-inner"></span>
                                            </div>
                                            

                                        </div>

                                        

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"> Source</label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('sourceID', $crmSource,'', 'class="form-control select2" id="sourceID"  required'); ?>
                                                </div>
                                                <span class="input-req-inner"></span>
                                            </div>
                                            

                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title">RFQ Status </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('rfq_status', $rfqStatusArr, '0', 'class="form-control select2" id="rfq_status"'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title">Document Status </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('document_status',$docStatusArr , '0', 'class="form-control select2" id="document_status"'); ?>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                            
                                        </div>

                                        
                                        
                                        <?php
                                        if($flowserve !='GCC'){?>
                                            <div class="row" style="margin-top: 10px;">
                                            <div class="form-group col-sm-4">
                                                <label class="title"><?php echo $this->lang->line('manufacturing_proposal_engineer') ?><!--Proposal Engineer--> </label>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('prpengineer', $employeedrop_prp_eng,'', 'class="form-control select2" id="prpengineer"'); ?>
                                                </div>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                        
                                        
                                        <?php if($flowserve =='Micoda'){ ?>
                                            <div class="row" style="margin-top: 10px;">
                                                <div class="form-group col-sm-4">
                                                    <label class="title"><?php echo $this->lang->line("manufacturing_estimated_employee")?><!-- Estimated Employee--> </label>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <div class="input-req" title="Required Field">
                                                    <?php echo form_dropdown('estimatedEmpID',$emp_dropdown,'','class="form-control select2" id="estimatedEmpID" "'); ?>
                                                    </div>
                                                    <span class="input-req-inner"></span>
                                                </div>
                                            </div>
                                        <?php } ?>


                                    </div>
                                    
                                    <div class="row" style="margin-right: 10px;">
                                            <div class="col-sm-12 ">
                                                <div class="pull-right">
                                                    <button class="btn btn-primary" onclick="saveCustomerInquiry(1)"
                                                            type="button"
                                                            id="submitBtn">
                                                        <?php echo $this->lang->line('common_save') ?><!--Save-->
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <br>

                                <div class="row">
                                    <div class="col-md-12 animated zoomIn">
                                        <header class="head-title">
                                            <h2>&nbsp;</h2>
                                        </header>
                                        <div class="row">
                                            <div class="col-xs-4">
                                                <input type="hidden" name="engineering_tab_id" id="engineering_tab_id" value="1">
                                                <table class="table" id="profileInfoTable"
                                                    style="background-color: #ffffff;border-width:thin;height:40px;border: 2px solid #ddd;">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <strong>
                                                                <?php 
                                                                if($flowserve=='GCC'){
                                                                    echo $this->lang->line('manufacturing_sales_marketing');
                                                                }
                                                                else{
                                                                    echo $this->lang->line('manufacturing_engineering');
                                                                }
                                                                ?><!--Engineering--> </strong>
                                                        </td>
                                                        <td></td>
                                                        <td>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--></strong>
                                                        </td>
                                                        <td class="form-group" style="width: 100%">
                                                            <?php echo form_dropdown('engineeringemployee[]', $employeedrop, '', 'class="form-control" multiple="multiple" id="engineeringemployee"'); ?>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history(10)"
                                                                data-original-title="History"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_required_date') ?><!--Required Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="enddateengineering">
                                                                <input type='text' class="form-control"
                                                                    name="EngineeringDeadLine"
                                                                    id="EngineeringDeadLine"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history(11)"
                                                                data-original-title="History"></span>
                                                        </td>


                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="submissiondateengineering">
                                                                <input type='text' class="form-control"
                                                                    name="submissiondatDeadLine"
                                                                    id="submissiondatDeadLine"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip" onclick="open_history(21)"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                data-original-title="History"></span>
                                                        </td>


                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--></strong>
                                                        </td>
                                                        <td style="width: 100%;"><input type="text" class="form-control"
                                                                                        id="noofdays" name="noofdays"
                                                                                        value="0" readonly></td>
                                                        <td style="width: 100%;"></td>


                                                    </tr>


                                                    </tbody>
                                                </table>


                                            </div>
                                            <div class="col-xs-4">

                                                <input type="hidden" name="purchasing_tab_id" id="purchasing_tab_id" value="2">
                                                <table class="table" id="profileInfoTable"
                                                    style="background-color: #ffffff;border-width:thin;height:40px;border: 2px solid #ddd;">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_purchasing') ?><!--Purchasing--> </strong>
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--></strong>
                                                        </td>
                                                        <td class="form-group"
                                                            style="width: 100%"> <?php echo form_dropdown('purchasingemployee[]', $employeedrop, '', 'class="form-control " multiple="multiple" id="purchasingemployee"'); ?></td>
                                                        <td>
                                                            <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history(12)"
                                                                data-original-title="History"></span>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_required_date') ?><!--Required Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="Purchasingenddate">
                                                                <input type='text' class="form-control"
                                                                    name="purchasingDeadLine"
                                                                    id="purchasingDeadLine"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history(13)"
                                                                data-original-title="History"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="submissiondatepurchasing">
                                                                <input type='text' class="form-control"
                                                                    name="submissiondatDeadLinepurchasing"
                                                                    id="submissiondatDeadLinepurchasing"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip" onclick="open_history(22)"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                data-original-title="History"></span>
                                                        </td>


                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--></strong>
                                                        </td>
                                                        <td style="width: 100%;"><input type="text" class="form-control"
                                                                                        id="noofdayspurchasing"
                                                                                        name="noofdayspurchasing" value="0"
                                                                                        readonly></td>
                                                        <td style="width: 100%;"></td>


                                                    </tr>


                                                    </tbody>
                                                </table>
                                            </div>


                                            <div class="col-xs-4">
                                                <input type="hidden" name="production_tab_id" id="production_tab_id" value="3">
                                                <table class="table" id="profileInfoTable"
                                                    style="background-color: #ffffff;border-width:thin;height:40px;border: 2px solid #ddd;">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_production') ?><!--Production--> </strong>
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--></strong>
                                                        </td>
                                                        <td class="form-group"
                                                            style="width: 100%"> <?php echo form_dropdown('productionemployee[]', $employeedrop, '', 'class="form-control" multiple="multiple"  id="productionemployee"'); ?></td>
                                                        <td>
                                                            <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history(14)"
                                                                data-original-title="History"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_required_date') ?><!--Required Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="enddateproduction">
                                                                <input type='text' class="form-control"
                                                                    name="DeadLineproduction"
                                                                    id="DeadLineproduction"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip" onclick="open_history(15)"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                data-original-title="History"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="submissiondateProduction">
                                                                <input type='text' class="form-control"
                                                                    name="submissiondatDeadLineproduction"
                                                                    id="submissiondatDeadLineproduction"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history(23)"
                                                                data-original-title="History"></span>
                                                        </td>


                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--></strong>
                                                        </td>
                                                        <td style="width: 100%;"><input type="text" class="form-control"
                                                                                        id="noofdaysproduction"
                                                                                        name="noofdaysproduction" value="0"
                                                                                        readonly></td>
                                                        <td style="width: 100%;"></td>


                                                    </tr>

                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-4">
                                                <input type="hidden" name="qc_tab_id" id="qc_tab_id" value="4">
                                                <table class="table" id="profileInfoTable"
                                                    style="background-color: #ffffff;border-width:thin;height:40px;border: 2px solid #ddd;">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_quality_assurance_or_quality_control') ?><!--QA/QC--></strong>
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--></strong>
                                                        </td>
                                                        <td class="form-group"
                                                            style="width: 100%"> <?php echo form_dropdown('qaqcemployee[]', $employeedrop, '', 'class="form-control" multiple="multiple" id="qaqcemployee"'); ?></td>
                                                        <td>
                                                            <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history(16)"
                                                                data-original-title="Status History"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_required_date') ?><!--Required Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="enddateqaqc">
                                                                <input type='text' class="form-control"
                                                                    name="DeadLineqaqc"
                                                                    id="DeadLineqaqc"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip" onclick="open_history(17)"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                data-original-title="Status History"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="submissiondateqaqc">
                                                                <input type='text' class="form-control"
                                                                    name="submissiondateqaqcDeadLinepurchasing"
                                                                    id="submissiondateqaqcDeadLinepurchasing"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip" onclick="open_history(24)"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                data-original-title="History"></span>
                                                        </td>


                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--></strong>
                                                        </td>
                                                        <td style="width: 100%;"><input type="text" class="form-control"
                                                                                        id="noofdaysqaqc"
                                                                                        name="noofdaysqaqc" value="0"
                                                                                        readonly></td>
                                                        <td></td>


                                                    </tr>

                                                    </tbody>
                                                </table>

                                            </div>

                                            <div class="col-xs-4">
                                                <input type="hidden" name="finance_tab_id" id="finance_tab_id" value="5">
                                                <table class="table" id="profileInfoTable"
                                                    style="background-color: #ffffff;border-width:thin;height:40px;border: 2px solid #ddd;">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_Finance') ?><!--Finance --> </strong>
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--></strong>
                                                        </td>
                                                        <td class="form-group"
                                                            style="width: 100%"> <?php echo form_dropdown('financeemployee[]', $employeedrop, '', 'class="form-control" multiple="multiple" id="financeemployee"'); ?></td>
                                                        <td>
                                                            <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history()"
                                                                data-original-title="History"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_required_date') ?><!--Required Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="enddatefinance">
                                                                <input type='text' class="form-control"
                                                                    name="DeadLinefinance"
                                                                    id="DeadLinefinance"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip" onclick="open_history()"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                data-original-title="History"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group' id="submissiondatefinance">
                                                                <input type='text' class="form-control"
                                                                    name="submissiondatDeadLinefinance"
                                                                    id="submissiondatDeadLinefinance"
                                                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history()"
                                                                data-original-title="History"></span>
                                                        </td>


                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--></strong>
                                                        </td>
                                                        <td style="width: 100%;"><input type="text" class="form-control"
                                                                                        id="noofdaysfinance"
                                                                                        name="noofdaysfinance" value="0"
                                                                                        readonly></td>
                                                        <td style="width: 100%;"></td>


                                                    </tr>

                                                    </tbody>
                                                </table>

                                            </div>

                                        </div>
                                          
                                        
                                    </div>
                                </div>


                                <br>
                                <div class="row">
                                    <div class="col-md-12 animated zoomIn">
                                        <header class="head-title">
                                            <?php if($languageflowserve=='FlowServe'){ ?>
                                                <h2>Product Line Details</h2>
                                            <?php } elseif($languageflowserve=='Micoda'){?>
                                                <h2><?php echo $this->lang->line('manufacturing_detail') ?></h2>
                                            <?php }else{ ?>
                                                <h2><?php echo $this->lang->line('manufacturing_item_detail') ?><!--Item Details--></h2>
                                            <?php } ?>
                                        </header>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table id="mfq_customer_inquiry"
                                                           class="table table-condensed">
                                                        <thead>
                                                        <tr>
                                                            <th style="min-width: 12%">
                                                                <?php if($languageflowserve=='FlowServe'){ ?>
                                                                    Product Line
                                                                <?php }else{ ?>
                                                                    <?php echo $this->lang->line('common_item') ?>
                                                                <?php } ?>
                                                                
                                                            </th>
                                                            <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_expected_quantity') ?><!--Expected Qty--></th>
                                                            <th style="min-width: 12%"><?php echo $this->lang->line('common_uom') ?><!--UOM--></th>
                                                            <?php 
                                                            if($flowserve != 'GCC'){
                                                            ?>
                                                                <th style="min-width: 12%"><?php echo $this->lang->line('common_department') ?><!--Department--></th>
                                                            <?php } ?>

                                                            <?php 
                        
                                                            if($flowserve == 'Micoda' || $flowserve == 'SOP'){
                                                            ?>
                                                                <th style="min-width: 12%"><?php echo 'Budget' ?><!--Department--></th>
                                                            <?php } ?>
                                                            <th style="min-width: 12%">Expected Delivery Period (Week)</th>
                                                            <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_remarks') ?><!--Remarks--></th>
                                                            <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_delivery_terms') ?><!--Delivery Terms--></th>
                                                            <th style="min-width: 5%">
                                                                <div class=" pull-right">
                                                                <span class="button-wrap-box">
                                                                    <button type="button" data-text="Add" id="btnAdd"
                                                                            onclick="add_more_material()"
                                                                            class="button button-square button-tiny button-royal button-raised">
                                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                                    </button>
                                                                </span>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="customer_inquiry_body">

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <br>

                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-sm-12 ">
                                        <div class="pull-right">
                                            <button class="btn btn-primary" onclick="saveCustomerInquiry(1)"
                                                    type="button"
                                                    id="submitBtn">
                                                <?php echo $this->lang->line('common_save') ?><!--Save-->
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="step2">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('common_attachment') ?><!--Attachment--></h2>
                </header>
                <div class="row">
                    <?php echo form_open_multipart('', 'id="attachment_uplode_form" class="form-inline"'); ?>
                    <input type="hidden" name="documentSystemCode" id="documentSystemCode"
                           value="<?php echo $page_id ?>">
                    <input type="hidden" name="documentID" id="documentID" value="CI">
                    <input type="hidden" name="document_name" id="document_name" value="Customer Inquiry">
                    <div class="col-sm-12">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control"
                                       name="attachmentDescription" placeholder="Description..." id="attachmentDescription"
                                       style="width: 240%;">
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
                            <button type="button" class="btn btn-default"
                                    onclick="document_uplode()"><span
                                    class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                            </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12" id="show_all_attachments">
                        <header class="infoarea">
                            <div class="search-no-results">NO ATTACHMENT FOUND
                            </div>
                        </header>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="step3">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <div id="review">
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-12 ">
                <div class="pull-right">
                    <button class="btn btn-success" onclick="confirmCustomerInquiry()"
                            type="button"
                            id="confirmBtn">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee"
         id="mfq_customerinquirychangehistory_model">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Customer Inquiry Change History </h4>
                </div>
                <div class="modal-body">

                    <div id="sysnc">
                        <div class="table-responsive">
                            <table id="employee_sync" class="table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 12%">Previous Value</abbr></th>
                                    <th style="width: 12%">Changed Value</abbr></th>
                                    <th style="width: 12%">Changed By</th>
                                    <th style="width: 12%">Changed Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee"
         id="mfq_customerinquirychangehistory_model_itemdetail">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Customer Inquiry Change History </h4>
                </div>
                <div class="modal-body">

                    <div id="sysnc">
                        <div class="table-responsive">
                            <table id="employee_sync_detail_history" class="table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 12%">Changed Value</abbr></th>
                                    <th style="width: 12%">Changed By</th>
                                    <th style="width: 12%">Changed Date</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script>
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        var search_id = 1;
        var ciMasterID = "";
        
        var engineeringdrop = $('#engineeringemployee');
        var purchasingemployee = $('#purchasingemployee');
        var productionemployee = $('#productionemployee');
        var qaqcemployee = $('#qaqcemployee');
        var financeemployee = $('#financeemployee');

        financeemployee.multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            maxHeight: 200,
            numberDisplayed: 1,
            enableFiltering: true,  // Enables the search box
            filterPlaceholder: 'Search...'
        });

        qaqcemployee.multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            maxHeight: 200,
            numberDisplayed: 1,
            enableFiltering: true,  // Enables the search box
            filterPlaceholder: 'Search...'
        });
       
        engineeringdrop.multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            maxHeight: 200,
            numberDisplayed: 1,
            enableFiltering: true,  // Enables the search box
            filterPlaceholder: 'Search...',
            onDropdownShow: function(event) {
                // This function is called when the dropdown is opened
                $('#engineeringemployee option:first').hide();
            }
        });

        purchasingemployee.multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            maxHeight: 200,
            numberDisplayed: 1,
            enableFiltering: true,  // Enables the search box
            filterPlaceholder: 'Search...'
        });

        productionemployee.multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            maxHeight: 200,
            numberDisplayed: 1,
            enableFiltering: true,  // Enables the search box
            filterPlaceholder: 'Search...'
        });

        $(document).ready(function () {
            $("[rel=tooltip]").tooltip();
            $('.select2').select2();
            $('.filterDate').datetimepicker({
                useCurrent: false,
                format: date_format_policy
            });
            $('.headerclose').click(function () {
                fetchPage('system/mfq/mfq_rfq', '', 'Customer Inquiry');
            });
            Inputmask().mask(document.querySelectorAll("input"));
            <?php
            if ($page_id) {
            ?>
            ciMasterID = parseInt("<?php echo $page_id  ?>");
            loadCustomerInquiry();
            load_customer_inquiry_detail('<?php echo $page_id  ?>');
            load_attachments('CI',<?php echo $page_id  ?>);
            <?php
            }else{
            ?>
            $('.btn-wizard').addClass('disabled');
            init_customerInquiryDetailForm();
            <?php
            }
            ?>
            initializeCustomerInquiryDetailTypeahead(1);
            $(document).on('click', '.remove-tr', function () {
                $(this).closest('tr').remove();
            });

            $(document).on('click', '.remove-tr2', function () {
                $(this).closest('tr').remove();
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $('a[data-toggle="tab"]').removeClass('btn-primary');
                $('a[data-toggle="tab"]').addClass('btn-default');
                $(this).removeClass('btn-default');
                $(this).addClass('btn-primary');
            });
        });

        function add_more_material() {
            search_id += 1;
            var appendData = $('#mfq_customer_inquiry tbody tr:first').clone();
            appendData.find('.f_search').attr('id', 'f_search_' + search_id);
            appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
            appendData.find('input').val('');
            appendData.find('textarea').val('');
            appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
            $('#mfq_customer_inquiry').append(appendData);
            var lenght = $('#mfq_customer_inquiry tbody tr').length - 1;

            number_validation();
            initializeCustomerInquiryDetailTypeahead(search_id);
            $('.filterDate').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            });
            Inputmask().mask(document.querySelectorAll("input"));
        }

        function load_customer_inquiry_detail(ciMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {ciMasterID: ciMasterID},
                url: "<?php echo site_url('MFQ_CustomerInquiry/load_mfq_customerInquiryDetail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                 
                    $('#customer_inquiry_body').html('');
                    var i = 0;
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (k, v) {
                            var flowserve ='<?php echo getPolicyValues('MANFL', 'All')?>'?'<?php echo getPolicyValues('MANFL', 'All'); ?>':'Default';
                            var segment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="ci_\'+search_id+\'"'), form_dropdown('segmentID[]', $segment, 'Each', 'class="form-control segmentID"  required'))
                                ?>';

                            var billofmaterial = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="bom_\'+search_id+\'"'), form_dropdown('bom[]', $bom, 'Each', 'class="form-control bom"  required'))
                                ?>';

                             if(flowserve == 'GCC'){
                                $('#customer_inquiry_body').append('<tr id="rowMC_' + v.ciDetailID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="Item ID, Item Description..." value="' + v.itemDescription + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control ciDetailID" name="ciDetailID[]" value="' + v.ciDetailID + '"> </td> <td><input type="text" name="expectedQty[]" id="expectedQty" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedQty" onfocus="this.select();" value="' + v.expectedQty + '"> </td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" value="' + v.UnitDes + '" readonly> </td> <td>' + segment + '</td> <td><input type="text" name="expectedDeliveryWeeks[]" id="expectedDeliveryWeeks" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedDeliveryWeeks" onfocus="this.select();" value="' + v.expectedDeliveryWeeks + '"> </td> <td><textarea name="remarks[]" id="remarks" class="form-control" rows="1">' + v.remarks + '</textarea> </td> <td><textarea name="deliveryTerms[]" id="deliveryTerms" class="form-control" rows="1">' + v.deliveryTerms + '</textarea> </td>  <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_customerInquiryDetail(' + v.ciDetailID + ',' + v.ciMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td></tr>');
                                initializeCustomerInquiryDetailTypeahead(search_id);
                             } else if(flowserve == 'Micoda' || flowserve == 'SOP'){
                                $('#customer_inquiry_body').append('<tr id="rowMC_' + v.ciDetailID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="Item ID, Item Description..." value="' + v.itemDescription + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control ciDetailID" name="ciDetailID[]" value="' + v.ciDetailID + '"> </td> <td><input type="text" name="expectedQty[]" id="expectedQty" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedQty" onfocus="this.select();" value="' + v.expectedQty + '"> </td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" value="' + v.UnitDes + '" readonly> </td> <td>' + segment + '</td><td>'+ billofmaterial +'</td> <td><input type="text" name="expectedDeliveryWeeks[]" id="expectedDeliveryWeeks" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedDeliveryWeeks" onfocus="this.select();" value="' + v.expectedDeliveryWeeks + '"> </td> <td><textarea name="remarks[]" id="remarks" class="form-control" rows="1">' + v.remarks + '</textarea> </td> <td><textarea name="deliveryTerms[]" id="deliveryTerms" class="form-control" rows="1">' + v.deliveryTerms + '</textarea> </td>  <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_customerInquiryDetail(' + v.ciDetailID + ',' + v.ciMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td></tr>');
                                initializeCustomerInquiryDetailTypeahead(search_id);
                             }
                             else{
                                $('#customer_inquiry_body').append('<tr id="rowMC_' + v.ciDetailID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="Item ID, Item Description..." value="' + v.itemDescription + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control ciDetailID" name="ciDetailID[]" value="' + v.ciDetailID + '"> </td> <td><input type="text" name="expectedQty[]" id="expectedQty" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedQty" onfocus="this.select();" value="' + v.expectedQty + '"> </td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" value="' + v.UnitDes + '" readonly> </td> <td><input type="text" name="expectedDeliveryWeeks[]" id="expectedDeliveryWeeks" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedDeliveryWeeks" onfocus="this.select();" value="' + v.expectedDeliveryWeeks + '"> </td> <td><textarea name="remarks[]" id="remarks" class="form-control" rows="1">' + v.remarks + '</textarea> </td> <td><textarea name="deliveryTerms[]" id="deliveryTerms" class="form-control" rows="1">' + v.deliveryTerms + '</textarea> </td>  <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_customerInquiryDetail(' + v.ciDetailID + ',' + v.ciMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td></tr>');
                                initializeCustomerInquiryDetailTypeahead(search_id);
                             }
                            $('.filterDate').datetimepicker({
                                useCurrent: false,
                                format: date_format_policy,
                            });
                            Inputmask().mask(document.querySelectorAll("input"));
                            $('#ci_' + search_id).val(v.segmentID);
                            $('#bom_' + search_id).val(v.bomMasterID2);
                            search_id++;
                            i++;
                        });
                    } else {
                        init_customerInquiryDetailForm();
                    }
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function initializeCustomerInquiryDetailTypeahead(id) {
            $('#f_search_' + id).autocomplete({
                serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_finish_goods/',
                onSelect: function (suggestion) {
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.mfqItemID').val(suggestion.mfqItemID);
                        $('#f_search_' + id).closest('tr').find('.uom').val(suggestion.uom);
                    }, 200);
                },
            });
            $(".tt-dropdown-menu").css("top", "");
        }

        function init_customerInquiryDetailForm() {

            var languagePolicy= '<?php echo getPolicyValues('LNG', 'All'); ?>'?'<?php echo getPolicyValues('LNG', 'All'); ?>':'Default';
            var placeholder_text='';
            if(languagePolicy =='FlowServe'){
                placeholder_text = 'Product Line ID, Product Line Description...';
            }else{
                placeholder_text = 'Item ID, Item Description...';
            }

            var flowserve ='<?php echo getPolicyValues('MANFL', 'All')?>'?'<?php echo getPolicyValues('MANFL', 'All'); ?>':'Default';
            var segment = '<?php
                    echo str_replace(array("\n", '<select'), array('', '<select id="ci_1"'), form_dropdown('segmentID[]', $segment, 'Each', 'class="form-control segmentID"  required'))
                ?>';

            var billofmaterial = '<?php
                    echo str_replace(array("\n", '<select'), array('', '<select id="ci_\'+search_id+\'"'), form_dropdown('bom[]', $bom, 'Each', 'class="form-control bom"  required'))
                ?>';
           
            $('#customer_inquiry_body').html('');
        
            if(flowserve == 'GCC'){
                $('#customer_inquiry_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="'+placeholder_text+'" id="f_search_1"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]"> <input type="hidden" class="form-control ciDetailID" name="ciDetailID[]"> </td> <td><input type="text" name="expectedQty[]" id="expectedQty" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedQty" onfocus="this.select();"> </td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" readonly> </td> <td><input type="text" name="expectedDeliveryWeeks[]" id="expectedDeliveryWeeks" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedDeliveryWeeks" onfocus="this.select();"></td> <td><textarea name="remarks[]" id="remarks" class="form-control" rows="1"></textarea> </td> <td><textarea name="deliveryTerms[]" id="deliveryTerms" class="form-control" rows="1"></textarea> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
                 
            }else if(flowserve == 'Micoda' || flowserve == 'SOP'){
                $('#customer_inquiry_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="'+placeholder_text+'" id="f_search_1"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]"> <input type="hidden" class="form-control ciDetailID" name="ciDetailID[]"> </td> <td><input type="text" name="expectedQty[]" id="expectedQty" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedQty" onfocus="this.select();"> </td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" readonly> </td><td>' + segment + '</td><td>' + billofmaterial + '</td><td><input type="text" name="expectedDeliveryWeeks[]" id="expectedDeliveryWeeks" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedDeliveryWeeks" onfocus="this.select();"></td> <td><textarea name="remarks[]" id="remarks" class="form-control" rows="1"></textarea> </td> <td><textarea name="deliveryTerms[]" id="deliveryTerms" class="form-control" rows="1"></textarea> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
            }
            else{
                $('#customer_inquiry_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="'+placeholder_text+'" id="f_search_1"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]"> <input type="hidden" class="form-control ciDetailID" name="ciDetailID[]"> </td> <td><input type="text" name="expectedQty[]" id="expectedQty" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedQty" onfocus="this.select();"> </td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" readonly> </td><td>' + segment + '</td> <td><input type="text" name="expectedDeliveryWeeks[]" id="expectedDeliveryWeeks" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number expectedDeliveryWeeks" onfocus="this.select();"></td> <td><textarea name="remarks[]" id="remarks" class="form-control" rows="1"></textarea> </td> <td><textarea name="deliveryTerms[]" id="deliveryTerms" class="form-control" rows="1"></textarea> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
            }
            number_validation();
            $('.filterDate').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
                sideBySide: true,
                widgetPositioning: {
                    horizontal: 'right',
                    vertical: 'top'
                }
            });
            Inputmask().mask(document.querySelectorAll("input"));
            setTimeout(function () {
                initializeCustomerInquiryDetailTypeahead(1);
            }, 500);
        }


        function loadCustomerInquiry() {
            if (ciMasterID > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("MFQ_CustomerInquiry/load_mfq_customerInquiry"); ?>',
                    dataType: 'json',
                    data: {ciMasterID: ciMasterID},
                    async: false,
                    success: function (data) {
                        $("#contactpersonname").val(data['contactpresongrfq']);
                        $("#contactpersonemail").val(data['customerEmailrfq']);
                        $("#customerphone").val(data['customerPhoneNorfq']);
                        $("#mfqCustomerAutoID").val(data['mfqCustomerAutoID']).change();
                        $("#documentDate").val(data['documentDate']).change();
                        $("#deliveryDate").val(data['deliveryDate']).change();
                        $("#dueDate").val(data['dueDate']).change();
                        $("#manufacturingType").val(data['manufacturingType']).change();
                        $("#DepartmentID").val(data['rfqheadersegmentid']).change();
                        
                        // $("#engineeringemployee").val(data['engineeringResponsibleEmpID']).change();
                        $("#EngineeringDeadLine").val(data['engineeringEndDate']).change();
                        $("#submissiondatDeadLine").val(data['engineeringSubmissionDatecon']).change();
                        // $("#purchasingemployee").val(data['purchasingResponsibleEmpID']).change();
                        $("#purchasingDeadLine").val(data['purchasingEndDate']).change();
                        // $("#productionemployee").val(data['productionResponsibleEmpID']).change();
                        $("#DeadLineproduction").val(data['productionEndDate']).change();
                        // $("#qaqcemployee").val(data['QAQCResponsibleEmpID']).change();
                        $("#DeadLineqaqc").val(data['QAQCEndDate']).change();
                        $("#remainindays").val(data['remindEmailBefore']);

                        $("#description").val(data['description']);
                        $("#referenceNo").val(data['referenceNo']);
                        // $("#statusID").val(data['statusID']);
                        $("#type").val(data['type']);
                        $("#noofdays").val(data['Engineeringnoofdays']);
                        $("#noofdayspurchasing").val(data['purchasingnoofdays']);
                        $("#submissiondatDeadLinepurchasing").val(data['purchasingSubmissionDatecon']).change();
                        $("#submissiondatDeadLineproduction").val(data['productionSubmissionDatecon']).change();
                        $("#submissiondateqaqcDeadLinepurchasing").val(data['QAQCSubmissionDatecon']).change();
                        $("#noofdaysqaqc").val(data['qaqcnoofdays']);
                        $("#noofdaysproduction").val(data['productionnoofdays']);
                        $("#delayindays").val(data['noofdaysdelaydeliverydue']);
                        $("#prpengineer").val(data['proposalEngineerID']).change();
                        $("#currencyID").val(data['transactionCurrencyID']).change();
                        $("#micoda").val(data['locationAssigned']).change();
                        $("#sourceID").val(data['inquirySource']).change();

                        $("#rfq_status").val(data['rfqStatus']).change();
                        $("#document_status").val(data['documentStatus']).change();
                        $("#order_status").val(data['orderStatus']).change();


                        $("#estimatedEmpID").val(data['estimatedEmpID']).change();
                        $("#SalesManagerID").val(data['SalesManagerID']).change();
                        // $("#financeemployee").val(data['financeResponsibleEmpID']).change();
                        $("#submissiondatDeadLinefinance").val(data['financeSubmissionDate']).change();
                        $("#DeadLinefinance").val(data['financeEndDate']).change();

                        let engineeringResponsibleEmpID = data['engineeringResponsibleEmpID'] ? data['engineeringResponsibleEmpID'].split(',') : [];
                        let purchasingResponsibleEmpIDs = data['purchasingResponsibleEmpID'] ? data['purchasingResponsibleEmpID'].split(',') : [];
                        let productionResponsibleEmpIDs = data['productionResponsibleEmpID'] ? data['productionResponsibleEmpID'].split(',') : [];
                        let QAQCResponsibleEmpIDs = data['QAQCResponsibleEmpID'] ? data['QAQCResponsibleEmpID'].split(',') : [];
                        let financeResponsibleEmpIDs = data['financeResponsibleEmpID'] ? data['financeResponsibleEmpID'].split(',') : [];
                        $("#engineeringemployee").val(engineeringResponsibleEmpID).change();
                        $("#purchasingemployee").val(purchasingResponsibleEmpIDs).change();
                        $("#productionemployee").val(productionResponsibleEmpIDs).change();
                        $("#qaqcemployee").val(QAQCResponsibleEmpIDs).change();
                        $("#financeemployee").val(financeResponsibleEmpIDs).change();
  
                        $("#engineeringemployee").multiselect2('refresh');
                        $("#purchasingemployee").multiselect2('refresh');
                        $("#productionemployee").multiselect2('refresh');
                        $("#qaqcemployee").multiselect2('refresh');
                        $("#financeemployee").multiselect2('refresh');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        myAlert('e', xhr.responseText);
                    }
                });
            }
        }

        function saveCustomerInquiry(type) {
            var data = $(".frm_customerInquiry").serializeArray();
            data.push({'name': 'status', 'value': type});
            data.push({'name': 'transactioncurrency', 'value': $('#currencyID option:selected').text()});
            $.ajax({
                url: "<?php echo site_url('MFQ_CustomerInquiry/save_CustomerInquiry'); ?>",
                type: 'post',
                data: data,
                dataType: 'json',
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        if (type == 2) {
                            $('.headerclose').trigger('click');
                        } else {
                            $("#ciMasterID").val(data[2]);
                            ciMasterID = data[2];
                            $("#documentSystemCode").val(data[2]);
                            $('.btn-wizard').removeClass('disabled');
                            load_customer_inquiry_detail(data[2]);
                        }

                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    stopLoad();
                    myAlert('e', xhr.responseText);
                }
            });
        }


        function confirmCustomerInquiry() {
            swal({
                    title: "Are you sure?",
                    text: "You want to confirm?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: true
                },
                function () {
                    $.ajax({
                        url: "<?php echo site_url('MFQ_CustomerInquiry/customer_inquiry_confirmation'); ?>",
                        type: 'post',
                        data: {ciMasterID: ciMasterID},
                        dataType: 'json',
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                $('.headerclose').trigger('click');
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            stopLoad();
                            myAlert('e', xhr.responseText);
                        }
                    });
                });
        }

        function delete_customerInquiryDetail(ciDetailID, masterID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to Delete this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "delete",
                    closeOnConfirm: false
                },
                function () {
                    $.ajax({
                        url: "<?php echo site_url('MFQ_CustomerInquiry/delete_customerInquiryDetail'); ?>",
                        type: 'post',
                        data: {ciDetailID: ciDetailID, masterID: masterID},
                        dataType: 'json',
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 1) {
                                swal("Error!", data['message'], "error");
                            }
                            else if (data['error'] == 0) {
                                if (data.code == 1) {
                                    init_customerInquiryDetailForm();
                                }
                                $("#rowMC_" + ciDetailID).remove();
                                swal("Deleted!", data['message'], "success");
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            stopLoad();
                            myAlert('e', xhr.responseText);
                        }
                    });
                });
        }

        function document_uplode() {
            var formData = new FormData($("#attachment_uplode_form")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
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
                    $('#attachmentDescription').val('');
                    $('#remove_id').click();
                    if (data['status']) {
                        load_attachments('CI', $('#documentSystemCode').val());
                    }
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
            return false;
        }

        function load_attachments(documentID, ciMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {documentID: documentID, documentSystemCode: ciMasterID},
                url: "<?php echo site_url('MFQ_CustomerInquiry/load_attachments'); ?>",
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

        function delete_attachment(id, myFileName) {
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
                        data: {attachmentID: id, myFileName: myFileName},
                        url: "<?php echo site_url('Attachment/delete_attachments_AWS_s3'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data) {
                                myAlert('s', 'Deleted Successfully');
                                load_attachments('CI', $('#documentSystemCode').val());
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

        function customer_inquiry_print() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    ciMasterID: $('#ciMasterID').val(),
                },
                url: "<?php echo site_url('MFQ_CustomerInquiry/fetch_customer_inquiry_print'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#review").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function clearitemAutoID(e, ths) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9) {
                //e.preventDefault();
            } else {
                $(ths).closest('tr').find('.mfqItemID').val('');
                $(ths).closest('tr').find('.uom').val('');
            }
        }
        function open_history(historyid) {
            template_userGroupDetail(historyid);
            $('#mfq_customerinquirychangehistory_model').modal('show');
        }
        function template_userGroupDetail(historyid) {
            oTable2 = $('#employee_sync').DataTable({
                "ordering": false,
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": false,
                "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
                "sAjaxSource": "<?php echo site_url('MFQ_CustomerInquiry/fetch_mfq_customer_inquiry_history'); ?>",
                language: {
                    paginate: {
                        previous: '‹‹',
                        next: '››'
                    }
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
                    {"mData": "mfqChangeHistoryID"},
                    {"mData": "previousvalue"},
                    {"mData": "changedvalue"},
                    {"mData": "changedby"},
                    {"mData": "changeddate"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "historyid", "value": historyid});
                    aoData.push({"name": "ciMasterID", "value": ciMasterID});
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
        function open_history_item_detail(historyid, detailid) {
            template_itemdetails_historydetails(historyid, detailid);
            $('#mfq_customerinquirychangehistory_model_itemdetail').modal('show');
        }
        function template_itemdetails_historydetails(historyid, detailid) {
            oTable2 = $('#employee_sync_detail_history').DataTable({
                "ordering": false,
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": false,
                "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
                "sAjaxSource": "<?php echo site_url('MFQ_CustomerInquiry/fetch_mfq_customer_inquiry_history_detail'); ?>",
                language: {
                    paginate: {
                        previous: '‹‹',
                        next: '››'
                    }
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
                    {"mData": "mfqChangeHistoryID"},
                    {"mData": "changedvalue"},
                    {"mData": "changedby"},
                    {"mData": "changeddate"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "historyid", "value": historyid});
                    aoData.push({"name": "ciMasterID", "value": ciMasterID});
                    aoData.push({"name": "detailid", "value": detailid});
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

        $('#enddateengineering').datetimepicker({
            useCurrent: true,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            calculateEngineeringdays()
        });

        $('#submissiondateengineering').datetimepicker({
            useCurrent: false,
            format: date_format_policy,

        }).on('dp.change', function (ev) {
            calculateEngineeringdays()
        });
        function calculateEngineeringdays() {
            var startDate = moment($("#EngineeringDeadLine").val(), "DD.MM.YYYY");
            var endDate = moment($("#submissiondatDeadLine").val(), "DD.MM.YYYY");
            var startdatevalid = startDate.isValid()
            var enddatevalid = endDate.isValid()
            if ((startdatevalid != false) && (enddatevalid != false)) {
                var days = endDate.diff(startDate, 'days');
                var formattedDate = days;
                $('#noofdays').val(formattedDate);
            } else {
                $('#noofdays').val(0);
            }
        }


        $('#Purchasingenddate').datetimepicker({
            useCurrent: true,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            calculatepurchasingdays()
        });

        $('#submissiondatepurchasing').datetimepicker({
            useCurrent: false,
            format: date_format_policy,

        }).on('dp.change', function (ev) {
            calculatepurchasingdays()
        });
        function calculatepurchasingdays() {
            var startDate = moment($("#purchasingDeadLine").val(), "DD.MM.YYYY");
            var endDate = moment($("#submissiondatDeadLinepurchasing").val(), "DD.MM.YYYY");
            var startdatevalid = startDate.isValid()
            var enddatevalid = endDate.isValid()
            if ((startdatevalid != false) && (enddatevalid != false)) {
                var days = endDate.diff(startDate, 'days');
                var formattedDate = days;
                $('#noofdayspurchasing').val(formattedDate);
            } else {
                $('#noofdayspurchasing').val(0);
            }
        }


        $('#enddateproduction').datetimepicker({
            useCurrent: true,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            calculateproductiondate()
        });

        $('#submissiondateProduction').datetimepicker({
            useCurrent: false,
            format: date_format_policy,

        }).on('dp.change', function (ev) {
            calculateproductiondate()
        });

        function calculateproductiondate() {
            var startDate = moment($("#DeadLineproduction").val(), "DD.MM.YYYY");
            var endDate = moment($("#submissiondatDeadLineproduction").val(), "DD.MM.YYYY");
            var startdatevalid = startDate.isValid()
            var enddatevalid = endDate.isValid()
            if ((startdatevalid != false) && (enddatevalid != false)) {
                var days = endDate.diff(startDate, 'days');
                var formattedDate = days;
                $('#noofdaysproduction').val(formattedDate);
            } else {
                $('#noofdaysproduction').val(0);
            }


        }

        function validateFloatKeyPress(el, evt) {
            //alert(currency_decimal);
            var charCode = (evt.which) ? evt.which : event.keyCode;
            var number = el.value.split('.');
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            //just one dot
            if (number.length > 1 && charCode == 46) {
                return false;
            }
            //get the carat position
            var caratPos = getSelectionStart(el);
            var dotPos = el.value.indexOf(".");
            if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
                return false;
            }
            return true;
        }

        $('#enddateqaqc').datetimepicker({
            useCurrent: true,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            calculateqaqc()
        });

        $('#submissiondateqaqc').datetimepicker({
            useCurrent: false,
            format: date_format_policy,

        }).on('dp.change', function (ev) {
            calculateqaqc()
        });
        function calculateqaqc() {
            var startDate = moment($("#DeadLineqaqc").val(), "DD.MM.YYYY");
            var endDate = moment($("#submissiondateqaqcDeadLinepurchasing").val(), "DD.MM.YYYY");
            var startdatevalid = startDate.isValid()
            var enddatevalid = endDate.isValid()
            if ((startdatevalid != false) && (enddatevalid != false)) {
                var days = endDate.diff(startDate, 'days');
                var formattedDate = days;
                $('#noofdaysqaqc').val(formattedDate);
            } else {
                $('#noofdaysqaqc').val(0);
            }
        }
        $('#actualsubmissiondate').datetimepicker({
            useCurrent: true,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            calculatnoofdaysheader();
        });

        $('#plannedsubmissiondat').datetimepicker({
            useCurrent: false,
            format: date_format_policy,

        }).on('dp.change', function (ev) {
            calculatnoofdaysheader()
        });

         $('#enddatefinance').datetimepicker({
            useCurrent: true,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            calculateqaqc()
        });

        $('#submissiondatefinance').datetimepicker({
            useCurrent: false,
            format: date_format_policy,

        }).on('dp.change', function (ev) {
            calculateqaqc()
        });

        function calculatnoofdaysheader() {
            var startDate = moment($("#deliveryDate").val(), "DD.MM.YYYY");
            var endDate = moment($("#dueDate").val(), "DD.MM.YYYY");
            var days = startDate.diff(endDate, 'days');
            var formattedDate = days;
            $('#delayindays').val(formattedDate);
        }

        $( "#mfqCustomerAutoID" ).change(function() {
            fetch_contactpersongemail($(this).val());
        });
        function fetch_contactpersongemail(clientid)

        {
            if(clientid)
            {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {customerid: clientid},
                    url: "<?php echo site_url('MFQ_CustomerInquiry/fetchcontactpersonemail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $("#contactpersonemail").val(data['customerEmail']);
                        $("#customerphone").val(data['customerTelephone']);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }else
            {
                $("#contactpersonemail").val('');
                $("#customerphone").val('');
            }

        }

        function currency_validation(CurrencyID,documentID){
            if (CurrencyID) {
                currency_validation_modal(CurrencyID,documentID,'','');
            }
        }



    </script>
