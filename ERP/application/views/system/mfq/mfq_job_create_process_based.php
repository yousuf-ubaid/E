<?php echo head_page($_POST['page_name'], false);
$primaryLanguage = getPrimaryLanguage();
$date_format_policy = date_format_policy();
$segment = fetch_mfq_segment(true);
$umo_arr2 = all_umo_new_drop();
$this->lang->load('mfq', $primaryLanguage);
$current_date = current_format_date();
$suom_policy = getPolicyValues('SUOM', 'All');
$data_set = array(0 => array('estimateMasterID' => '', 'estimateDetailID' => '', 'bomMasterID' => '', 'mfqCustomerAutoID' => '', 'description' => '', 'mfqItemID' => '', 'unitDes' => '', 'type' => 1, 'itemDescription' => '', 'expectedQty' => 0, 'mfqSegmentID' => '', 'mfqWarehouseAutoID' => ''));
if ($data_arr) {
    $data_set = $data_arr;
}
$flowserve = getPolicyValues('MANFL', 'All');
$employeedrop = load_employee_drop_mfq();
$flowserveLanguagePolicy = (getPolicyValues('LNG', 'All')) ? getPolicyValues('LNG', 'All') : 0;


?>
<div id="filter-panel" class="collapse filter-panel"></div>
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }

    .affix-content .container .page-header {
        margin-top: 0;
    }

    .affix-sidebar {
        padding-right: 0;
        font-size: small;
        padding-left: 0;
    }

    .affix-row, .affix-container, .affix-content {
        height: 100%;
        overflow: scroll;
        margin-left: 0;
        margin-right: 0;
    }

    .affix-content {
        background-color: white;
    }

    .sidebar-nav .navbar .navbar-collapse {
        padding: 0;
        max-height: none;
    }

    .sidebar-nav .navbar {
        border-radius: 0;
        margin-bottom: 0;
        border: 0;
    }

    .sidebar-nav .navbar ul {
        float: none;
        display: block;
    }

    .sidebar-nav .navbar li {
        float: none;
        display: block;
    }

    .sidebar-nav .navbar li a {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    }

    @media (min-width: 769px) {
        .affix-content .container {
            width: 600px;
        }

        .affix-content .container .page-header {
            margin-top: 0;
        }
    }

    @media (min-width: 992px) {
        .affix-content .container {
            width: 900px;
        }

        .affix-content .container .page-header {
            margin-top: 0;
        }
    }

    @media (min-width: 1220px) {
        .affix-row {
            overflow: hidden;
        }

        .affix-content {
            overflow: auto;
        }

        .affix-content .container {
            width: 1000px;
        }

        .affix-content .container .page-header {
            margin-top: 0;
        }

        .affix-content {
            padding-right: 30px;
            padding-left: 10px;
        }

        .affix-title {
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 10px;
        }

        .navbar-nav {
            margin: 0;
        }

        .navbar-collapse {
            padding: 0;
        }

        .sidebar-nav .navbar li a:hover {
            background-color: #428bca;
            color: white;
        }

        .sidebar-nav .navbar li a > .caret {
            margin-top: 8px;
        }
    }

    .sidebar {
        padding-bottom: 0px;
    }

    div.bhoechie-tab-container {
        background-color: #ffffff;
        padding: 0 !important;
        border-radius: 4px;
        -moz-border-radius: 4px;
        border: 1px solid #ddd;
        -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        -moz-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
        background-clip: padding-box;
        opacity: 0.97;
        filter: alpha(opacity=97);
    }

    div.bhoechie-tab-menu {
        padding-right: 0;
        padding-left: 0;
        padding-bottom: 0;
    }

    div.bhoechie-tab-menu div.list-group {
        margin-bottom: 0;
    }

    div.bhoechie-tab-menu div.list-group > a {
        margin-bottom: 0;
    }

    div.bhoechie-tab-menu div.list-group > a .glyphicon,
    div.bhoechie-tab-menu div.list-group > a .fa {
        color: #E78800;
    }

    div.bhoechie-tab-menu div.list-group > a .glyphicon .badge {
        display: inline-block;
        min-width: 10px;
        padding: 6px 9px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 24px;
        color: #555;
        border: 2px solid #555;
        background-color: rgba(119, 119, 119, 0);
    }

    div.bhoechie-tab-menu div.list-group > a:first-child {
        border-top-right-radius: 0;
        -moz-border-top-right-radius: 0;
    }

    div.bhoechie-tab-menu div.list-group > a:last-child {
        border-bottom-right-radius: 0;
        -moz-border-bottom-right-radius: 0;
    }

    div.bhoechie-tab-menu div.list-group > a.active,
    div.bhoechie-tab-menu div.list-group > a.active .glyphicon,
    div.bhoechie-tab-menu div.list-group > a.active .fa {
        background-color: #E78800;
        color: #ffffff;
    }

    div.bhoechie-tab-menu div.list-group > a.active .badge {
        display: inline-block;
        min-width: 10px;
        padding: 6px 9px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 24px;
        color: #ffffff;
        border: 2px solid #ffffff;
        background-color: rgba(119, 119, 119, 0);
    }

    div.bhoechie-tab-menu div.list-group > a.active:after {
        content: '';
        position: absolute;
        left: 100%;
        top: 50%;
        margin-top: -13px;
        border-left: 0;
        border-bottom: 13px solid transparent;
        border-top: 13px solid transparent;
        border-left: 10px solid #E78800;
    }

    div.bhoechie-tab-content {
        background-color: #ffffff;
        /* border: 1px solid #eeeeee; */
        padding-left: 20px;
        padding-top: 10px;
    }

    div.bhoechie-tab div.bhoechie-tab-content:not(.active) {
        display: none;
    }

    .list-group-item.active, .list-group-item.active:focus, .list-group-item.active:hover {
        border: 1px solid #ddd;
    }

    .bhoechie-tab {
        border: solid 2px #E78800;
        margin-left: -2px;
        margin-top: 1px;
        margin-bottom: 1px;
        min-height: 300px;
    }

    .disabledbutton {
        pointer-events: none;
    }

    .sub_itemView{
        pointer-events: auto;
    }

    .table-responsive {
        overflow: visible !important
    }

</style>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary size-sm" href="#step1" data-toggle="tab">Step 1 - Job Header</a>
    <a class="btn btn-default size-sm btn-wizard" href="#step2" onclick="load_workflow_design()" data-toggle="tab">Step 2 - Job Detail</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="job_form"'); ?>
        <input type="hidden" name="workProcessID" id="workProcessID">
        <input type="hidden" name="completeStatus" id="completeStatus" value="0">
        <input type="hidden" name="fromType" id="fromType" value="<?php echo $policy_id; ?>">
        <div class="row">
            <div class="col-md-6 animated zoomIn">
                <div class="row">
                    <div class="form-group col-sm-4" style="margin-top: 10px;">
                        <label class="title">Customer Name</label>
                    </div>
                    <div class="form-group col-sm-7" style="margin-top: 10px;">
                        <?php echo form_dropdown('mfqCustomerAutoID', all_mfq_customer_drop(), $data_set[0]['mfqCustomerAutoID'], 'class="form-control select2" id="mfqCustomerAutoID"'); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Type</label>
                    </div>
                    <div class="form-group col-sm-7">
                         <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('type', array(1 => "General", 2 => "Based on Estimate"), $data_set[0]['type'], 'class="form-control select2" id="type" disabled'); ?>
                             <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Description</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <input type="text" name="description" id="description" class="form-control" value="<?php echo $data_set[0]['description']; ?>" required>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

               

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Template</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            
                            <?php echo form_dropdown('workFlowTemplateID',all_mfq_template_drop(), '', 'class="form-control select2" id="workFlowTemplateID" onchange="selectItem(this.value)"'); ?>
                            
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                    <div class="form-group col-sm-1" style="padding: 0px">
                        <button type="button" id="addTemplate" onclick="customizeTemplate()" class="btn btn-primary" title="Customize Template">
                                <i class="fa fa-cog" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                 <div class="row" id="general">
                    <div class="form-group col-sm-4">
                        <label class="title">Item</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <div id="itemSearchBox">
                                <input type="text" class="form-control finishgoods_search" name="search[]" placeholder="Item ID, Item Description..." id="finishgoods_search"> 
                                <input type="hidden" class="form-control mfqItemID" name="mfqItemID" id="finishGoods">
                            </div>
                            <span class="input-req-inner"></span>
                        </span>
                        <input type="hidden" name="" value="" id="finishGoods2">
                    </div>
                </div>

                <div class="row" id="estimate" style="display: none;">
                    <div class="form-group col-sm-4">
                        <label class="title">Item</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <input type="text" class="form-control" id="itemDescription" value="<?php echo $data_set[0]['itemDescription']; ?>" disabled>
                            <input type="hidden" class="" name="estimateDetailID" id="estimateDetailID" value="<?php echo $data_set[0]['estimateDetailID']; ?>">
                            <input type="hidden" class="" name="estMfqItemID" id="estMfqItemID" value="<?php echo $data_set[0]['mfqItemID']; ?>">
                            <input type="hidden" class="" name="bomMasterID" id="bomMasterID" value="<?php echo $data_set[0]['bomMasterID']; ?>">
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                    <div class="form-group col-sm-1" style="padding: 0px">
                        <button type="button" name="addEstimate" id="addEstimate" onclick="loadEstimateItem()" class="btn btn-primary" title="Add item from estimate">
                                <i class="fa fa-level-down" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Qty</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <input type="text" name="qty" id="qty" class="form-control" onkeypress="return validateFloatKeyPress(this,event)" value="<?php echo $data_set[0]['expectedQty']; ?>"> 
                        <input type="hidden" name="qty2" id="qty2">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Job Owner</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <?php $employee_arr = all_employee_drop(true, 1); ?>
                            <?php   echo form_dropdown('ownerID',$employee_arr, '', 'class="form-control select2" id="ownerID" onchange="" '); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

            </div>
            <div class="col-md-6 animated zoomIn">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Start Date</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <div class="input-group datepic" id="dateStartDate">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="startDate" id="startDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" class="form-control startDate" required>
                            </div>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">End Date</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <div class="input-group datepic" id="dateEndDate">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="endDate" id="endDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" class="form-control endDate" required>
                            </div>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Expected Delivery Date</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <div class="input-group datepicExpected" id="dateDeliveryDate">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="deliveryDate" id="deliveryDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" class="form-control deliveryDate" required>
                            </div>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">UoM</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <input type="text" name="itemUoM" id="itemUoM" class="form-control" value="<?php echo $data_set[0]['unitDes']; ?>" readonly>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title"><?php echo ($flowserve == 'FlowServe') ?  'Segment' : 'Department' ?></label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('mfqSegmentID', fetch_mfq_segment(), $data_set[0]['mfqSegmentID'], 'class="form-control select2" id="mfqSegmentID"');?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Warehouse</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('mfqWarehouseAutoID', all_mfq_warehouse_drop(), $data_set[0]['mfqWarehouseAutoID'], 'class="form-control select2" id="mfqWarehouseAutoID"'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

                <?php if($flowserve =='FlowServe'){ ?>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Job type</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('jobType', array(''=> 'Select Job type', 1=>'New Job', 2=>'Repair', 3=>'Warranty Claim'), '', 'class="select2" id="jobType" onchange=""'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
                <?php } ?>

                <div class="row col-md-12 hide">
                    <div class="text-right m-t-xs">
                        <button class="btn btn-primary" type="submit" id="saveJob">Save</button>
                    </div>
                </div>
            </div>
            <?php if($flowserve =='FlowServe'){ ?>
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
                                                            <strong><?php echo $this->lang->line('manufacturing_engineering') ?><!--Engineering--> </strong>
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
                                                            <?php echo form_dropdown('engineeringemployee', $employeedrop, '', 'class="form-control select2" id="engineeringemployee"'); ?>
                                                        </td>
                                                        <td>
                                                            <!-- <span title="History" rel="tooltip"
                                                                class="fa fa-info-circle fa-1x fa-fw"
                                                                onclick="open_history(1,'engineeringResponsibleEmpID')"
                                                                data-original-title="History"></span> -->
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_required_date') ?><!--Required Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group emp_cat_date' id="enddateengineering">
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

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group emp_cat_date' id="submissiondateengineering">
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
                                                            style="width: 100%"> <?php echo form_dropdown('purchasingemployee', $employeedrop, '', 'class="form-control select2" id="purchasingemployee"'); ?></td>
                                                        <td>

                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_required_date') ?><!--Required Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group emp_cat_date' id="Purchasingenddate">
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

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group emp_cat_date' id="submissiondatepurchasing">
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
                                                            style="width: 100%"> <?php echo form_dropdown('productionemployee', $employeedrop, '', 'class="form-control select2" id="productionemployee"'); ?></td>
                                                        <td>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_required_date') ?><!--Required Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group emp_cat_date' id="enddateproduction">
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
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group emp_cat_date' id="submissiondateProduction">
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
                                                            style="width: 100%"> <?php echo form_dropdown('qaqcemployee', $employeedrop, '', 'class="form-control select2" id="qaqcemployee"'); ?></td>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_required_date') ?><!--Required Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group emp_cat_date' id="enddateqaqc">
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
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--></strong>
                                                        </td>
                                                        <td style="width: 100%;">
                                                            <div class='input-group emp_cat_date' id="submissiondateqaqc">
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
                                        </div>
                                          
                                        
                                    </div>
            </div>
            <?php } ?>

        
            <div class="row col-md-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" type="submit" id="saveJob">Save</button>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if($flowserve =='Micoda'){ ?>
               <div class="responsible_div" id="responsible_div"></div>
            <?php } ?>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div id="" class="row review hide">
            <div class="col-md-12">
                <span class="no-print pull-right"> 
                    <a class="btn btn-default btn-sm" id="de_link" target="_blank" href="#">
                    <span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review Entries
                    </a> 
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>JOB DETAIL</h2>
                </header>
                <div class="col-md-4" id="jobNumber" style="font-size: 18px;color: #4a8cdb;font-weight: bold"></div>
                <div class="col-md-4" id="estimateCode" style="font-size: 18px;color: #4a8cdb;font-weight: bold"></div>
                <div class="col-md-4" id="inquiryCode" style="font-size: 18px;color: #4a8cdb;font-weight: bold"></div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>WORKFLOW DESIGN</h2>
                </header>
                <input type="hidden" id="workFlowTemplateID2">
                <div id="workflow-design">
                </div>
                <div class="col-md-12" style="margin-top: 10px" id="closedYN">
                    <div class="pull-right">
                        <button class="btn btn-primary-new size-lg" type="button" onclick="close_job()"><i class="fa fa-times" aria-hidden="true"></i>
                            Close Job
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<div class="modal fade" id="estimate_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Estimate</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h5>Estimate</h5>
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked" id="ciCode">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-striped table-condesed mfqTable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th class="text-left">Description</th>
                                <th>UOM</th>
                                <th>Qty</th>
                                <th>Unit Cost</th>
                                <th>Salling Price</th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="table_body_ci_detail">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="add_estimate_items()">Add
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="closeDateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 40%">
        <div class="modal-content">
            <?php echo form_open('', 'role="form" id="job_close_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Close Job</h4>
            </div>
            <div class="modal-body">
                <input class="subItemUOM hidden" id="subItemUOM" name="subItemUOM">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="title">Close Date</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <span class="input-req" title="Required Field">
                                    <div class="input-group datepic" id="closeDateDiv">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="closedDate" id="closedDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" class="form-control endDate" required>
                                    </div>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="title">Output Warehouse</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('outputWarehouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2" id="outputWarehouseAutoID"'); ?>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="title">Output Qty</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <span class="input-req" title="Required Field">
                                    <div class="input-group" id="outputQty" style="padding-right:20px">
                                        <div class="input-group-addon"><span class="primaryQtyDesc">&nbsp;</span></div>
                                        <input type="text" name="primaryQty" id="primaryQty" class="form-control primaryQty number" required>
                                    </div>
                                    <span class="input-req-inner"></span>
                                </span>
                            </div>
                        </div>
                        <?php if($suom_policy == 1) { ?>
                            <div class="row secondaryQtyDiv">
                                <div class="form-group col-sm-4">
                                    <label class="title">&nbsp;</label>
                                </div>
                                <div class="form-group col-sm-7">
                                    <span class="input-req" title="Required Field">
                                        <div class="input-group" id="outputQty">
                                            <div class="input-group-addon"><span class="secondaryQtyDesc">&nbsp;</span></div>
                                            <input type="text" name="secondaryQty" id="secondaryQty" class="form-control secondaryQty number">
                                        </div>
                                        <span class="input-req-inner"></span>
                                    </span>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="title">Close Comment</label>
                            </div>
                            <div class="form-group col-sm-7">
                                 
                                    <textarea name="closedComment" id="closedComment" rows="2" class="form-control"></textarea>
                                     
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary closing_job">Close Job</button>
                <input type="button" onclick="Update_sub_item_details()" class="btn btn-primary update_sub_item" value="Update Sub Item">
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<div class="modal fade" id="semifinishgoods_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Semi finish goods</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="" class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">Item</th>
                                    <th style="min-width: 12%">Required Qty</th>
                                    <th style="min-width: 12%">Qty in stock</th>
                                    <th style="min-width: 12%">Qty in production</th>
                                    <th style="min-width: 12%">Qty in use</th>
                                    <th style="min-width: 12%">Remaining Qty</th>
                                </tr>
                                </thead>
                                <tbody id="table_body_semifinishgoods">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveSemiFinishGoodsJob()">Save
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="insufficient_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Insufficient Item</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="" class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">Item Code</th>
                                    <th style="min-width: 12%">Item Description</th>
                                    <th style="min-width: 12%">Qty</th>
                                </tr>
                                </thead>
                                <tbody id="table_body_insufficient">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="proceedJob()">Proceed</button>
                <button type="button" class="btn btn-default" onclick="close_modal()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="linkedDoc_notApproved_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Document Not Approved</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="" class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 12%">Document System Code</th>
                                    <th style="min-width: 12%">Document Type</th>
                                    <th style="min-width: 12%">Document Date</th>
                                </tr>
                                </thead>
                                <tbody id="table_body_linkedDoc">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="close_modal()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customize_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Customize Template</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="customizeTemplateBody">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_customize_template()">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="usage_history_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Usage History</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="" class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 3%">#</th>
                                <th style="width: 10%">Usage</th>
                                <th style="width: 20%">Date</th>
                                <th style="width: 20%">Created By</th>
                            </tr>
                            </thead>
                            <tbody id="usage_history_body">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="subItemMaster_modal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="subItemMaster_form">
                <div class="modal-header">
                    <h4 class="modal-title">Sub Item Master </h4>
                </div>
                <div class="modal-body" style="min-height: 300px;">
                    <div id="subItemMasterList" style="overflow: auto"></div>

                </div>
                <div class="modal-footer">
                 <button type="button" class="btn btn-primary" onclick="saveSubItemMasterTmp()">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                   
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="bomnotconfig">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="error-msg-title">BOM Not Configured</h4>
            </div>
            
            <div class="modal-body">
                        <div id="bom-not-config-error" class="callout callout-warning"> 
                        
                        </div>

                <div class="modal-footer">
                     <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close')?><!--Close--></button>
                </div>
                </form>
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
         id="mfq_thired_party_po_generate">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Purchase Order Generate </h4>
                </div>
                <div class="modal-body">
                    <?php echo form_open('', 'role="form" id="mfq_overhead_po_form"'); ?>
                    <div id="sysnc">
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 12%">Item Code</abbr></th>
                                    <th style="width: 12%">Item</abbr></th>
                                    <th style="width: 12%">Supplier</abbr></th>
                                    <th style="width: 12%">Currency</abbr></th>
                                    <th style="width: 12%">Unit Cost</th>
                                    <th style="width: 12%"></th>
                                </tr>
                                </thead>
                                <tbody id="mfq_po_body">

                                </tbody>
                            </table>
                          
                        </div>
                    </div>
                </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-xs" data-dismiss="modal" onclick="create_po_document()">Create PO</button>
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Employee"
         id="add_more_overhead_late_model">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Late Cost </h4>
                </div>
                <div class="modal-body">
                <?php echo form_open('', 'role="form" id="job_form_overhead_late"'); ?>
                <table id="mfq_overhead_late" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_overhead_cost')?><!--Overhead Cost--></th>
                                        <!--<th style="min-width: 12%">Activity Code</th>-->
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_uom')?><!--UoM--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_department')?><!--Department--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate')?><!--Unit Rate--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours')?><!--Total Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value')?><!--Total Value--></th>
                                        <th style="min-width: 5%">
                                            
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="over_head_body_late">

                                    <tr> 
                                        <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_late"> <input type="hidden" class="form-control overHeadID" name="overHeadID" id="overHeadID_late"> <input type="hidden" class="form-control jcOverHeadID" name="jcOverHeadID[]"> 
                                        </td>
                                        <td><?php echo form_dropdown('uomID', $umo_arr2, '', 'class="form-control select2" id="uomID_late"'); ?></td>
                                        <td><?php echo form_dropdown('segmentID', $segment, '', 'class="form-control select2" onchange ="getSegmentHours(this)" id="segmentID_late"'); ?></td>
                                          
                                        <td>
                                            <input type="text" name="oh_hourRate" id="oh_hourRate_late" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();"> 
                                        </td> 
                                        <td> 
                                            <div class="text-right oh_usageHours">0 <input type="hidden" name="oh_usageHours" id="oh_usageHours_late" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control ohh_usageHours"></div> 
                                        </td> 
                                        <td>
                                            <input type="text" name="oh_totalHours" id="oh_totalHours_late" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours totalHours" onfocus="this.select();" readonly> 
                                        </td>  
                                        <td>
                                            &nbsp;
                                            <!-- <span id="oh_totalValueTxt_late" class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></span> -->
                                            <input type="text" name="oh_totalValue" id="oh_totalValue_late" value="0" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control number" onfocus="this.select();"> 
                                            <!-- <input type="hidden" name="oh_totalValue" id="oh_totalValue_late" class="oh_totalValue">  -->
                                        </td> 
                                        <td class="remove-td" style="vertical-align: middle;text-align: center">
                                        </td> 
                                    </tr>
                                    </tbody>
                                    
                                </table>
                        </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveJobLateOverheadCost()">Save</button>
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php
$data["documentID"] = 'JOB';
$this->load->view('system/item/itemmastersub/inc-item-master-sub-config-edit', $data);
?>

<script type="text/javascript">
    var search_id3 = 1;
    var search_id4 = 1;
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var jobformData;
    var customizeData = [];
    var data;
    var itemAutoID = '';
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_job_process_based', '', 'Workflow');
        });
        
        $("#addTemplate").hide();
        $(".select2").select2();
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

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


        $('.datepicExpected').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#job_form').bootstrapValidator('revalidateField', 'deliveryDate');
        });

        $('#dateStartDate').datetimepicker().on('dp.change', function (e) {
            var incrementDay = moment(new Date(e.date));
            incrementDay.add(1, 'days');
            $('#dateEndDate').data('DateTimePicker').minDate(incrementDay);
            $(this).data("DateTimePicker").hide();
        });

        $('#dateEndDate').datetimepicker().on('dp.change', function (e) {
            var decrementDay = moment(new Date(e.date));
            decrementDay.subtract(1, 'days');
            $('#dateStartDate').data('DateTimePicker').maxDate(decrementDay);
            $(this).data("DateTimePicker").hide();
        });

        Inputmask().mask(document.querySelectorAll("input"));

        workProcessID = null;
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            workProcessID = p_id;
            load_job_header();
        } else {
            $('.btn-wizard').addClass('disabled');
        }

        // $("#addEstimate").hide();

        // setTimeout(function () {
        //     $('#type').trigger('change');
        // }, 500);

        $('#job_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
                // workFlowTemplateID: {validators: {notEmpty: {message: 'Template is required.'}}},
                startDate: {validators: {notEmpty: {message: 'Start Date is required.'}}},
                endDate: {validators: {notEmpty: {message: 'End Date is required.'}}},
                deliveryDate: {validators: {notEmpty: {message: 'delivery Date is required.'}}},
                // qty: {validators: {notEmpty: {message: 'Qty is required.'}}},
                itemUoM: {validators: {notEmpty: {message: 'UOM is required.'}}},
                // mfqCustomerAutoID: {validators: {notEmpty: {message: 'Customer is required.'}}},
                mfqSegmentID: {validators: {notEmpty: {message: 'Segment is required.'}}},
                mfqWarehouseAutoID: {validators: {notEmpty: {message: 'Warehouse is required.'}}},
            }
        }).on('success.form.bv', function (e) {
            $('#workFlowTemplateID').prop('disabled', false);
            $('#finishgoods_search').prop('disabled', false);
            $('#type').prop('disabled', false);
            // $('#mfqCustomerAutoID').prop('disabled', false);
            $('#mfqSegmentID').prop('disabled', false);
            $('#mfqWarehouseAutoID').prop('disabled', false);
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data = data.concat(customizeData);
            jobformData = data;
            <?php if($policy_id == 'EST') { ?>
                getSemiFinishGoods(data);
            <?php } else{ ?>
                saveJob(data);
            <?php  } ?>
        });

        $('#frm_jobDetail').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                mfqItemID: {validators: {notEmpty: {message: 'Item is required.'}}},
                qty: {validators: {notEmpty: {message: 'Qty is required.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({name: 'workProcessID', value: workProcessID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_Job/save_job_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#finishGoods2').val($('#finishGoods').val());
                    $('#qty2').val($('#qty').val());
                    load_workflow_design();
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#job_close_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                closedDate: {validators: {notEmpty: {message: 'Close date is required.'}}},
                
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({name: 'workProcessID', value: workProcessID});
            data.push({name: 'method', value: "1"});// close
            $.ajax({
                url: "<?php echo site_url('MFQ_Job/close_job_process_based'); ?>",
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
                        $("#closeDateModal").modal('hide');
                        setTimeout(function () {
                            $('.headerclose').trigger('click');
                        }, 500);
                    }
                    if (data[2]) {
                        $("#table_body_insufficient").html("");
                        $.each(data[2], function (k, v) {
                            $("#table_body_insufficient").append("<tr><td>" + v.itemSystemCode + "</td><td>" + v.itemDescription + "</td><td>" + v.remainingQty + "</td></tr>");
                        });
                        $("#insufficient_modal").modal();
                    }
                    if (data[3]) {
                        $("#closeDateModal").modal("hide");
                        $("#table_body_linkedDoc").html("");
                        var i = 1;
                        $.each(data[3], function (k, v) {
                            $("#table_body_linkedDoc").append("<tr><td>" + i + "</td><td>" + v.documentCode + "</td><td>" + v.documentType + "</td><td>" + v.documentDate + "</td></tr>");
                            i++;
                        });
                        $("#linkedDoc_notApproved_modal").modal();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    stopLoad();
                    myAlert('e', xhr.responseText);
                }
            });
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });

        $('#type').change(function () {
            if ($(this).val() == 1) {
                $('#estimate').hide();
                $('#general').show();
                $('#itemDescription').val('');
                $('#estimateDetailID').val('');
                $('#estMfqItemID').val('');
                $('#bomMasterID').val('');
                //$('#itemUoM').val('');
            } else {
                $('#estimate').show();
                $('#general').hide();
                $('#finishgoods_search').val('').change();
                //$('#itemUoM').val('');
            }
        });

        $('#workFlowTemplateID').change(function () {
            var isDefault = $(this).find(':selected').data('isdefault'); 
            var workID = ($('#workFlowTemplateID').val());
            if (isDefault == 1) {
                $("#addTemplate").show();
            } else {
                $("#addTemplate").hide();
            }
            initializeitemTypeahead(workID);
        });
      
    });

    function initializeitemTypeahead(workID) {

        $('#finishgoods_search').autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_finish_goods_jobcard/?&workProcessID='+workID,
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#itemSearchBox').find('.mfqItemID').val(suggestion.mfqItemID);
                    if ($('#type').val() == 1) {
                        $('#itemUoM').val(suggestion.uom);
                        $('#job_form').bootstrapValidator('revalidateField', 'itemUoM');
                    }
                    //get_item_wise_template(suggestion.mfqItemID);
                }, 200);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function proceedJob() {
        var data = $("#job_close_form").serializeArray();
        data.push({name: 'workProcessID', value: workProcessID});
        data.push({name: 'method', value: "2"});// proceed
        $.ajax({
            url: "<?php echo site_url('MFQ_Job/close_job_process_based'); ?>",
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
                    $("#closeDateModal").modal("hide");
                    $("#insufficient_modal").modal("hide");
                    $("#linkedDoc_notApproved_modal").modal("hide");
                    setTimeout(function () {
                        $('.headerclose').trigger('click');
                    }, 500);
                }
                if (data[3]) {
                    $("#closeDateModal").modal("hide");
                    $("#table_body_linkedDoc").html("");
                    var i = 1;
                    $.each(data[3], function (k, v) {
                        $("#table_body_linkedDoc").append("<tr><td>" + i + "</td><td>" + v.documentCode + "</td><td>" + v.documentType + "</td><td>" + v.documentDate + "</td></tr>");
                        i++;
                    });
                    $("#linkedDoc_notApproved_modal").modal();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }

    function close_modal() {
        $("#closeDateModal").modal("hide");
        $("#insufficient_modal").modal("hide");
        $("#linkedDoc_notApproved_modal").modal("hide");
    }

    load_work_process_responsible();

    function load_work_process_responsible(){

        if(workProcessID){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {workProcessID: workProcessID},
                url: "<?php echo site_url('MFQ_Template/load_work_process_responsible'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    stopLoad();

                    $('#responsible_div').empty();
                    $('#responsible_div').html(data);


                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();

                }
            });

        }

    }

    function load_job_header() {
        if (workProcessID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {workProcessID: workProcessID},
                url: "<?php echo site_url('MFQ_Job/load_job_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        itemAutoID = data['mfqItemID'];
                        workProcessID = data['workProcessID'];
                        $('#workProcessID').val(workProcessID);
                        $('#workFlowTemplateID').val(data["workFlowTemplateID"]).change();
                        $('#workFlowTemplateID2').val(data["workFlowTemplateID"]);
                        $('#deliveryDate').val(data["expectedDelDate"]);
                        $('#description').val(data['description']);
                        $('#documentDate').val(data['documentDate']).change();
                        $('#jobType').val(data['jobType']).change();
                        $('#jobNumber').html("<span style='color:#aaa'>Job Code: </span>" + data['documentCode']);
                        $('#estimateCode').html("<span style='color:#aaa'>Estimate Code: </span>" + data['estimateCode']);
                        $('#inquiryCode').html("<span style='color:#aaa'>Inquiry Code: </span>" + data['ciCode']);
                        $('#qty').val(data['qty']);
                        /*$('#qty').prop('readonly', true);*/
                        $('#qty2').val(data['qty']);
                        $('#itemUoM').val(data['UnitDes']);
                        $('#ownerID').val(data['ownerID']).change();
                        $('#mfqCustomerAutoID').val(data['mfqCustomerAutoID']).change();
                        $('#mfqSegmentID').val(data['mfqSegmentID']).change();
                        $('#type').val(data['type']).change();
                        if (data['type'] == 2) {
                            $('#itemDescription').val(data['itemDescription']);
                            $('#estMfqItemID').val(data['mfqItemID']);
                            $('#estimateDetailID').val(data['estimateDetailID']);
                            $('#bomMasterID').val(data['bomMasterID']);
                        } else {
                            $('#finishGoods').val(data['mfqItemID']);
                            $('#finishgoods_search').val(data['itemDescription']);
                        }
                        $('#finishGoods2').val(data['mfqItemID']);
                        $('#startDate').val(data['startDate']).change();
                        $('#endDate').val(data['endDate']).change();
                        $('#mfqWarehouseAutoID').val(data['mfqWarehouseAutoID']).change();
                        $('#completeStatus').val(data['completeStatus']);
                        if (data['completeStatus'] == 1) {
                            $('#description').prop('readonly', true);
                            $('#endDate').prop('readonly', true);
                            $('#startDate').prop('readonly', true);
                            $('#saveJob').hide();
                        }
                        $('#type').prop('disabled', true);
                        // $('#mfqCustomerAutoID').prop('disabled', true);
                        $('#mfqSegmentID').prop('disabled', true);
                        $('#mfqWarehouseAutoID').prop('disabled', true);
                        $('#finishgoods_search').prop('disabled', true);
                        $('#addEstimate').prop('disabled', true);
                        $('#workFlowTemplateID').prop('disabled', true);
                        $("#addTemplate").hide();
                        // get_item_wise_template(data['mfqItemID'], data["workFlowTemplateID"]);

                        if (data['isFromEstimate'] == 1) {
                            $('#type').prop('disabled', true);
                            // $('#mfqCustomerAutoID').prop('disabled', true);
                            $('#mfqSegmentID').prop('disabled', false);
                            $('#mfqWarehouseAutoID').prop('disabled', false);
                            $('#fromType').val('EST');
                            $("#addEstimate").hide();
                            $('#workFlowTemplateID').prop('disabled', false);
                            var workFlowTemplateIDValue = $("#workFlowTemplateID option[data-isdefault='1']").val();
                            $("#workFlowTemplateID").val(workFlowTemplateIDValue).change();
                            $('.btn-wizard').addClass('disabled');
                        }

                        if (workProcessID) {
                            $('.review').removeClass('hide');
                            de_link = "<?php echo site_url('MFQ_Job/fetch_double_entry_job_process_based'); ?>/" + workProcessID;
                            $("#de_link").attr("href", de_link);
                        }
                        if (data['confirmedYN'] == 1) {
                            $('#closedYN').hide();
                        } else {
                            $('#closedYN').show();
                        }

                        if (data['isSaved'] == 1 || data['levelNo'] == 3) {
                            $('#qty').prop('readonly', true);
                        }

                        if(data['eng']){
                            $("#engineeringemployee").val(data['eng']['empID']).change();
                            $("#EngineeringDeadLine").val(data['eng']['requiredDate']).change();
                            $("#noofdays").val(data['eng']['delays']);
                            $("#submissiondatDeadLine").val(data['eng']['submissionDate']).change();
                        }

                        if(data['purchasing']){
                            $("#purchasingemployee").val(data['purchasing']['empID']).change();
                            $("#purchasingDeadLine").val(data['purchasing']['requiredDate']).change();
                           $("#noofdayspurchasing").val(data['purchasing']['delays']);
                           $("#submissiondatDeadLinepurchasing").val(data['purchasing']['submissionDate']).change();
                        }

                        if(data['production']){
                            $("#productionemployee").val(data['production']['empID']).change();
                            $("#DeadLineproduction").val(data['production']['requiredDate']).change();
                            $("#submissiondatDeadLineproduction").val(data['production']['submissionDate']).change();
                            $("#noofdaysproduction").val(data['production']['delays']);
                        }

                        if(data['qc']){
                            $("#submissiondateqaqcDeadLinepurchasing").val(data['qc']['submissionDate']).change();
                            $("#noofdaysqaqc").val(data['qc']['delays']);
                            $("#qaqcemployee").val(data['qc']['empID']).change();
                            $("#DeadLineqaqc").val(data['qc']['requiredDate']).change();
                        }
                    }
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();

                }
            });
        }
    }

    function load_workflow_design() {
        if (workProcessID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {templateMasterID: $('#workFlowTemplateID2').val(), type: 2, workProcessID: workProcessID},
                url: "<?php echo site_url('MFQ_Template/load_workflow_process_design_process_based'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#workflow-design').html(data);
                    }
                    stopLoad();

                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();

                }
            });
        }
    }

    function get_workflow_template_process_based(pageNameLink, tabID, workFlowID, documentID, type, tab, templateDetailID, linkworkFlow, workFlowTemplateID = '') {
        if (workProcessID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    workProcessID: workProcessID,
                    pageNameLink: pageNameLink,
                    tabID: tabID,
                    workFlowID: workFlowID,
                    documentID: documentID,
                    type: type,
                    templateMasterID: $('#workFlowTemplateID2').val(),
                    tab: tab,
                    mfqItemID: $('#finishGoods2').val(),
                    templateDetailID: templateDetailID,
                    linkworkFlow: linkworkFlow,
                    workFlowTemplateID: workFlowTemplateID
                },
                url: "<?php echo site_url('MFQ_Template/get_workflow_template_process_based'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#workflow_template').addClass("active");
                    if (!jQuery.isEmptyObject(data)) {
                        $('#' + tabID).html(data);
                    }
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();

                }
            });
        }
    }

    function delete_task_detail(id) {
        if (taskID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this file!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'AssingeeID': id},
                        url: "<?php echo site_url('Crm/delete_task_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            setTimeout(function () {
                                fetch_detail();
                            }, 300);

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function workflow_document_uplode(documentID, workProcessID, workFlowID) {
        var formData = new FormData($("#attachment_uplode_form_" + documentID)[0]);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('MFQ_Template/attachement_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], 1000);
                if (data[0] == 's') {
                    /*$('#add_attachemnt_show').addClass('hide');
                     $('#remove_id').click();
                     $('#leadattachmentDescription').val('');*/
                    load_attachments(documentID, workProcessID, workFlowID);
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function load_attachments(documentID, workProcessID, workFlowID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {workProcessID: workProcessID, workFlowID: workFlowID, documentID: documentID},
            url: "<?php echo site_url('MFQ_Template/load_attachments'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                $('#show_all_attachments_' + documentID).empty();
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        $('#show_all_attachments_' + documentID).append('<div class="row"> <div class="col-sm-12"> <div class="past-info"> <div id="toolbar"> <div class="toolbar-title">Files</div> </div> <div class="post-area"> <article class="post"> <a target="_blank" class="nopjax" href="' + v.link + '"> <div class="item-label file">File</div> </a> <div class="time"><span class="hithighlight"></span></div><div class="icon"> <img src="<?php echo base_url('images/mfq/icon-file.png'); ?>" width="16" height="16" title="File"> </div> <header class="infoarea"> <strong class="attachemnt_title"> <img src="<?php echo base_url('images/mfq/icon_pic.gif'); ?>" style="vertical-align:top"> &nbsp;<a target="_blank" class="nopjax" href="' + v.link + '">' + v.myFileName + '</a> <span style="display: inline-block;">' + v.fileSize + ' KB</span> <div><span class="attachemnt_title">' + v.attachmentDescription + '</span> </div> <div><span class="attachemnt_title" style="display: inline-block;">By: ' + v.createdUserName + '</span> <span class="deleteSpan" style="display: inline-block;"><a onclick="delete_workprocess_attachment(' + v.attachmentID + ',\'' + v.myFileName + '\',\'' + documentID + '\',' + workProcessID + ',' + workFlowID + ');"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></span> </div> </strong> </header> </article></div></div></div></div>');
                    });
                } else {
                    $('#show_all_attachments_' + documentID).append('<div class="row"> <div class="col-sm-12"> <div class="past-info"> <div id="toolbar"> <div class="toolbar-title">Files</div> </div> <div class="post-area"> <article class="post"> <header class="infoarea"> <strong class="attachemnt_title"> <span style="text-align: center;font-size: 15px;font-weight: 800;">No Files Found </span> </strong> </header> </article> </div> </div> </div> </div>');
                }
                //stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function save_crew(documentID, workFlowID) {
        var data = $('#frm_crew_' + documentID).serializeArray();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Template/save_workprocess_crew'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                load_workprocess_crew(documentID, workFlowID)

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_machine(documentID, workFlowID) {
        var data = $('#frm_machine_' + documentID).serializeArray();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Template/save_workprocess_machine'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                load_workprocess_machine(documentID, workFlowID)

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function initializCrewTypeahead(id, documentID) {
        $('#c_search_' + documentID + '_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Template/fetch_crew/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.crewID').val(suggestion.crewID);
                    $('#c_search_' + documentID + '_' + id).closest('tr').find('.designation').val(suggestion.DesDescription);
                }, 200);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function initializMachineTypeahead(id, documentID) {
        $('#m_search_' + documentID + '_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Template/fetch_machine/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.mfq_faID').val(suggestion.mfq_faID);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.faCat').val(suggestion.faCat);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.faSubCat').val(suggestion.faSubCat);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.faSubSubCat').val(suggestion.faSubSubCat);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.faCode').val(suggestion.faCode);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.partNumber').val(suggestion.partNumber);
                    $('#m_search_' + documentID + '_' + id).closest('tr').find('.assetDescription').val(suggestion.assetDescription);
                }, 200);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function add_more_crew(documentID) {
        search_id3 += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_crew_' + documentID + ' tbody tr:first').clone();
        //appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.c_search').attr('id', 'c_search_' + documentID + '_' + search_id3);
        appendData.find('.startTime').removeClass('dateTimePicker_crew_' + documentID + '_1');
        appendData.find('.startTime').removeClass('dateTimePicker_crew_' + documentID + '_' + search_id3 - 1);
        appendData.find('.endTime').removeClass('dateTimePicker_crew_' + documentID + '_1');
        appendData.find('.endTime').removeClass('dateTimePicker_crew_' + documentID + '_' + search_id3 - 1);
        appendData.find('.startTime').addClass('dateTimePicker_crew_' + documentID + '_' + search_id3);
        appendData.find('.endTime').addClass('dateTimePicker_crew_' + documentID + '_' + search_id3);
        appendData.find('.startTime').attr('id', 'startDate_crew_' + documentID + '_' + search_id3);
        appendData.find('.startTime').attr('data-id', 'endDate_crew_' + documentID + '_' + search_id3);
        appendData.find('.endTime').attr('id', 'endDate_crew_' + documentID + '_' + search_id3);
        appendData.find('.endTime').attr('data-id', 'startDate_crew_' + documentID + '_' + search_id3);
        appendData.find('.c_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_crew_' + documentID).append(appendData);
        var lenght = $('#mfq_crew_' + documentID + ' tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializCrewTypeahead(search_id3, documentID);
        $('#startDate_crew_' + documentID + '_' + search_id3).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'top'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm"),
                duration = d2.diff(d1, 'hours');
            if ($.isNumeric(duration)) {
                $(this).closest('tr').find('.hoursSpent').val(duration);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
            }
        });

        $('#endDate_crew_' + documentID + '_' + search_id3).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            useCurrent: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'top'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm"),
                duration = d2.diff(d1, 'hours');
            if ($.isNumeric(duration)) {
                $(this).closest('tr').find('.hoursSpent').val(duration);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
            }
        });

        $('#startDate_crew_' + documentID + '_' + search_id3).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').minDate(e.date);
            $(this).data("DateTimePicker").hide();
        });

        $('#endDate_crew_' + documentID + '_' + search_id3).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
            $(this).data("DateTimePicker").hide();
        });
        //initializematerialTypeahead(1);
    }

    function add_more_machine(documentID) {
        search_id4 += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#mfq_machine_' + documentID + ' tbody tr:first').clone();
        //appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.m_search').attr('id', 'm_search_' + documentID + '_' + search_id4);
        appendData.find('.m_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('.startTime').removeClass('dateTimePicker_machine_' + documentID + '_1');
        appendData.find('.endTime').removeClass('dateTimePicker_machine_' + documentID + '_1');
        appendData.find('.startTime').removeClass('dateTimePicker_machine_' + documentID + '_' + search_id4 - 1);
        appendData.find('.endTime').removeClass('dateTimePicker_machine_' + documentID + '_' + search_id4 - 1);
        appendData.find('.startTime').addClass('dateTimePicker_machine_' + documentID + '_' + search_id4);
        appendData.find('.endTime').addClass('dateTimePicker_machine_' + documentID + '_' + search_id4);
        appendData.find('.startTime').attr('id', 'startDate_machine_' + documentID + '_' + search_id4);
        appendData.find('.startTime').attr('data-id', 'endDate_machine_' + documentID + '_' + search_id4);
        appendData.find('.endTime').attr('id', 'endDate_machine_' + documentID + '_' + search_id4);
        appendData.find('.endTime').attr('data-id', 'startDate_machine_' + documentID + '_' + search_id4);
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#mfq_machine_' + documentID).append(appendData);
        var lenght = $('#mfq_machine_' + documentID + ' tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
        initializMachineTypeahead(search_id4, documentID);
        $('#startDate_machine_' + documentID + '_' + search_id4).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'top'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm"),
                duration = d2.diff(d1, 'hours');
            if ($.isNumeric(duration)) {
                $(this).closest('tr').find('.hoursSpent').val(duration);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
            }
        });

        $('#endDate_machine_' + documentID + '_' + search_id4).datetimepicker({
            showTodayButton: true,
            format: date_format_policy + " hh:mm A",
            sideBySide: false,
            useCurrent: false,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'top'
            }
        }).on('dp.change', function (ev) {
            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                end_time = $(this).closest('tr').find('.txtendTime').val(),
                d1 = moment(start_time, date_format_policy + " HH:mm"),
                d2 = moment(end_time, date_format_policy + " HH:mm"),
                duration = d2.diff(d1, 'hours');
            if ($.isNumeric(duration)) {
                $(this).closest('tr').find('.hoursSpent').val(duration);
            } else {
                $(this).closest('tr').find('.hoursSpent').val(0);
            }
        });

        $('#startDate_machine_' + documentID + '_' + search_id4).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').minDate(e.date);
            $(this).data("DateTimePicker").hide();
        });

        $('#endDate_machine_' + documentID + '_' + search_id4).on('dp.change', function (e) {
            var d_id = $(this).data('id');
            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
            $(this).data("DateTimePicker").hide();
        });
        //initializematerialTypeahead(1);
    }


    function load_work_process_detail(documentID, workFlowID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {workProcessID: workProcessID, workFlowID: workFlowID},
            url: "<?php echo site_url('MFQ_Template/fetch_workprocess_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#crew_body_' + documentID).html('');
                var i = 0;
                if (!$.isEmptyObject(data["crew"])) {
                    $.each(data["crew"], function (k, v) {
                        $('#crew_body_' + documentID).append('<tr id="rowCR_' + documentID + '_' + v.workProcessCrewID + '"><td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control c_search" name="search[]" placeholder="Crew" id="c_search_' + documentID + '_' + search_id3 + '" value="' + v.Ename1 + '"> <input type="hidden" class="form-control crewID" name="crewID[]" value="' + v.crewID + '"> <input type="hidden" class="form-control workProcessCrewID" name="workProcessCrewID[]" value="' + v.workProcessCrewID + '"> </td> <td><input type="text" name="designation" class="form-control designation" value="' + v.DesDescription + '" readonly=""></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id3 + ' startTime" id="startDate_crew_' + documentID + '_' + search_id3 + '" data-id="endDate_crew_' + documentID + '_' + search_id3 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" value="' + v.startTime + '" class="form-control txtstartTime" required=""></div></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id3 + ' endTime" id="endDate_crew_' + documentID + '_' + search_id3 + '" data-id="startDate_crew_' + documentID + '_' + search_id3 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime" value="' + v.endTime + '" required=""></div></td> <td><input type="text" name="hoursSpent[]" value="' + v.hoursSpent + '" class="form-control hoursSpent" readonly></td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_workprocess_crew(' + v.workProcessCrewID + ',' + v.workProcessID + ', \'' + documentID + '\')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializCrewTypeahead(search_id3, documentID);
                        $('#startDate_crew_' + documentID + '_' + search_id3).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'top'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm"),
                                duration = d2.diff(d1, 'hours');
                            if ($.isNumeric(duration)) {
                                $(this).closest('tr').find('.hoursSpent').val(duration);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                            }
                        });

                        $('#endDate_crew_' + documentID + '_' + search_id3).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            useCurrent: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'top'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm"),
                                duration = d2.diff(d1, 'hours');
                            if ($.isNumeric(duration)) {
                                $(this).closest('tr').find('.hoursSpent').val(duration);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                            }
                        });

                        $('#startDate_crew_' + documentID + '_' + search_id3).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').minDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });

                        $('#endDate_crew_' + documentID + '_' + search_id3).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });
                        search_id3++;
                        i++;
                    });
                } else {
                    init_workprocess_crew(documentID);
                }

                $('#machine_body_' + documentID).html('');
                var i = 0;
                if (!$.isEmptyObject(data["machine"])) {
                    $.each(data["machine"], function (k, v) {
                        $('#machine_body_' + documentID).append('<tr id="rowMAC_' + documentID + '_' + v.workProcessMachineID + '"><td><input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control m_search" name="search[]" placeholder="Machine" id="m_search_' + documentID + '_' + search_id4 + '" value="' + v.assetDescription + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]" value="' + v.mfq_faID + '"> <input type="hidden" class="form-control workProcessMachineID" name="workProcessMachineID[]" value="' + v.workProcessMachineID + '"> </td> <td><input type="text" name="assetDescription" class="form-control assetDescription" value="' + v.assetDescription + '" readonly=""></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id4 + ' startTime" id="startDate_machine_' + documentID + '_' + search_id4 + '" data-id="endDate_machine_' + documentID + '_' + search_id4 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" class="form-control txtstartTime" value="' + v.startTime + '" required=""></div></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id4 + ' endTime" id="endDate_machine_' + documentID + '_' + search_id4 + '" data-id="startDate_machine_' + documentID + '_' + search_id4 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime" value="' + v.endTime + '" required=""></div></td> <td><input type="text" name="hoursSpent[]" value="' + v.hoursSpent + '"  class="form-control hoursSpent" readonly></td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_workprocess_machine(' + v.workProcessMachineID + ',' + v.workProcessID + ',\'' + documentID + '\')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializMachineTypeahead(search_id4, documentID);
                        $('#startDate_machine_' + documentID + '_' + search_id4).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'top'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm"),
                                duration = d2.diff(d1, 'hours');
                            if ($.isNumeric(duration)) {
                                $(this).closest('tr').find('.hoursSpent').val(duration);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                            }
                        });

                        $('#endDate_machine_' + documentID + '_' + search_id4).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            useCurrent: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'top'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm"),
                                duration = d2.diff(d1, 'hours');
                            if ($.isNumeric(duration)) {
                                $(this).closest('tr').find('.hoursSpent').val(duration);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                            }
                        });

                        $('#startDate_machine_' + documentID + '_' + search_id4).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').minDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });

                        $('#endDate_machine_' + documentID + '_' + search_id4).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });
                        search_id4++;
                        i++;
                    });
                } else {
                    init_workprocess_machine(documentID);
                }

                $('#show_all_attachments_' + documentID).empty();
                if (!$.isEmptyObject(data["attachment"])) {
                    $.each(data["attachment"], function (k, v) {
                        $('#show_all_attachments_' + documentID).append('<div class="row"> <div class="col-sm-12"> <div class="past-info"> <div id="toolbar"> <div class="toolbar-title">Files</div> </div> <div class="post-area"> <article class="post"> <a target="_blank" class="nopjax" href="' + v.link + '"> <div class="item-label file">File</div> </a> <div class="time"><span class="hithighlight"></span></div><div class="icon"> <img src="<?php echo base_url('images/mfq/icon-file.png'); ?>" width="16" height="16" title="File"> </div> <header class="infoarea"> <strong class="attachemnt_title"> <img src="<?php echo base_url('images/mfq/icon_pic.gif'); ?>" style="vertical-align:top"> &nbsp;<a target="_blank" class="nopjax" href="' + v.link + '">' + v.myFileName + '</a> <span style="display: inline-block;">' + v.fileSize + ' KB</span> <div><span class="attachemnt_title">' + v.attachmentDescription + '</span> </div> <div><span class="attachemnt_title" style="display: inline-block;">By: ' + v.createdUserName + '</span> <span class="deleteSpan" style="display: inline-block;"><a onclick="delete_workprocess_attachment(' + v.attachmentID + ',\'' + v.myFileName + '\',\'' + documentID + '\',' + workProcessID + ',' + workFlowID + ');"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></span> </div> </strong> </header> </article></div></div></div></div>');
                    });
                } else {
                    $('#show_all_attachments_' + documentID).append('<div class="row"> <div class="col-sm-12"> <div class="past-info"> <div id="toolbar"> <div class="toolbar-title">Files</div> </div> <div class="post-area"> <article class="post"> <header class="infoarea"> <strong class="attachemnt_title"> <span style="text-align: center;font-size: 15px;font-weight: 800;">No Files Found </span> </strong> </header> </article> </div> </div> </div> </div>');
                }
            }
        });
    }

    function load_workprocess_crew(documentID, workFlowID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {workProcessID: workProcessID, workFlowID: workFlowID},
            url: "<?php echo site_url('MFQ_Template/fetch_workprocess_crew'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                $('#crew_body_' + documentID).html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        $('#crew_body_' + documentID).append('<tr id="rowCR_' + documentID + '_' + v.workProcessCrewID + '"><td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control c_search" name="search[]" placeholder="Crew" id="c_search_' + documentID + '_' + search_id3 + '" value="' + v.Ename1 + '"> <input type="hidden" class="form-control crewID" name="crewID[]" value="' + v.crewID + '"> <input type="hidden" class="form-control workProcessCrewID" name="workProcessCrewID[]" value="' + v.workProcessCrewID + '"> </td> <td><input type="text" name="designation" class="form-control designation" value="' + v.DesDescription + '" readonly=""></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id3 + ' startTime" id="startDate_crew_' + documentID + '_' + search_id3 + '" data-id="endDate_crew_' + documentID + '_' + search_id3 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" value="' + v.startTime + '" class="form-control txtstartTime" required=""></div></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id3 + ' endTime" id="endDate_crew_' + documentID + '_' + search_id3 + '" data-id="startDate_crew_' + documentID + '_' + search_id3 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime" value="' + v.endTime + '" required=""></div></td> <td><input type="text" name="hoursSpent[]" value="' + v.hoursSpent + '" class="form-control hoursSpent" readonly></td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_workprocess_crew(' + v.workProcessCrewID + ',' + v.workProcessID + ', \'' + documentID + '\')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializCrewTypeahead(search_id3, documentID);
                        $('#startDate_crew_' + documentID + '_' + search_id3).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'top'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm"),
                                duration = d2.diff(d1, 'hours');
                            if ($.isNumeric(duration)) {
                                $(this).closest('tr').find('.hoursSpent').val(duration);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                            }
                        });

                        $('#endDate_crew_' + documentID + '_' + search_id3).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            useCurrent: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'top'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm"),
                                duration = d2.diff(d1, 'hours');
                            if ($.isNumeric(duration)) {
                                $(this).closest('tr').find('.hoursSpent').val(duration);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                            }
                        });

                        $('#startDate_crew_' + documentID + '_' + search_id3).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').minDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });

                        $('#endDate_crew_' + documentID + '_' + search_id3).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });
                        search_id3++;
                        i++;
                    });
                } else {
                    init_workprocess_crew(documentID);
                }
                //stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_workprocess_machine(documentID, workFlowID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {workProcessID: workProcessID, workFlowID: workFlowID},
            url: "<?php echo site_url('MFQ_Template/fetch_workprocess_machine'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                $('#machine_body_' + documentID).html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        $('#machine_body_' + documentID).append('<tr id="rowMAC_' + documentID + '_' + v.workProcessMachineID + '"><td><input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control m_search" name="search[]" placeholder="Machine" id="m_search_' + documentID + '_' + search_id4 + '" value="' + v.assetDescription + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]" value="' + v.mfq_faID + '"> <input type="hidden" class="form-control workProcessMachineID" name="workProcessMachineID[]" value="' + v.workProcessMachineID + '"> </td> <td><input type="text" name="assetDescription" class="form-control assetDescription" value="' + v.assetDescription + '" readonly=""></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id4 + ' startTime" id="startDate_machine_' + documentID + '_' + search_id4 + '" data-id="endDate_machine_' + documentID + '_' + search_id4 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" class="form-control txtstartTime" value="' + v.startTime + '" required=""></div></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id4 + ' endTime" id="endDate_machine_' + documentID + '_' + search_id4 + '" data-id="startDate_machine_' + documentID + '_' + search_id4 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime" value="' + v.endTime + '" required=""></div></td> <td><input type="text" name="hoursSpent[]" value="' + v.hoursSpent + '"  class="form-control hoursSpent" readonly></td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_workprocess_machine(' + v.workProcessMachineID + ',' + v.workProcessID + ',\'' + documentID + '\')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializMachineTypeahead(search_id4, documentID);
                        $('#startDate_machine_' + documentID + '_' + search_id4).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'top'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm"),
                                duration = d2.diff(d1, 'hours');
                            if ($.isNumeric(duration)) {
                                $(this).closest('tr').find('.hoursSpent').val(duration);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                            }
                        });

                        $('#endDate_machine_' + documentID + '_' + search_id4).datetimepicker({
                            showTodayButton: true,
                            format: date_format_policy + " hh:mm A",
                            sideBySide: false,
                            useCurrent: false,
                            widgetPositioning: {
                                horizontal: 'left',
                                vertical: 'top'
                            }
                        }).on('dp.change', function (ev) {
                            var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                                end_time = $(this).closest('tr').find('.txtendTime').val(),
                                d1 = moment(start_time, date_format_policy + " HH:mm"),
                                d2 = moment(end_time, date_format_policy + " HH:mm"),
                                duration = d2.diff(d1, 'hours');
                            if ($.isNumeric(duration)) {
                                $(this).closest('tr').find('.hoursSpent').val(duration);
                            } else {
                                $(this).closest('tr').find('.hoursSpent').val(0);
                            }
                        });

                        $('#startDate_machine_' + documentID + '_' + search_id4).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').minDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });

                        $('#endDate_machine_' + documentID + '_' + search_id4).on('dp.change', function (e) {
                            var d_id = $(this).data('id');
                            $('#' + d_id).data('DateTimePicker').maxDate(e.date);
                            $(this).data("DateTimePicker").hide();
                        });
                        search_id4++;
                        i++;
                    });
                } else {
                    init_workprocess_machine(documentID);
                }
                //stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function init_workprocess_machine(documentID) {
        $('#machine_body_' + documentID).append('<tr><td><input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control m_search" name="search[]" placeholder="Machine" id="m_search_' + documentID + '_' + search_id4 + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]"> <input type="hidden" class="form-control workProcessMachineID" name="workProcessMachineID[]"> </td> <td><input type="text" name="assetDescription" class="form-control assetDescription" readonly=""></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id4 + ' startTime" id="startDate_machine_' + documentID + '_' + search_id4 + '" data-id="endDate_machine_' + documentID + '_' + search_id4 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" class="form-control txtstartTime" required=""></div></td> <td><div class="input-group dateTimePicker_machine_' + documentID + '_' + search_id4 + ' endTime" id="endDate_machine_' + documentID + '_' + search_id4 + '" data-id="startDate_machine_' + documentID + '_' + search_id4 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime" required=""></div></td> <td><input type="text" name="hoursSpent[]" class="form-control hoursSpent" readonly></td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        setTimeout(function () {
            initializMachineTypeahead(search_id4, documentID);
            $('#startDate_machine_' + documentID + '_' + search_id4).datetimepicker({
                showTodayButton: true,
                format: date_format_policy + " hh:mm A",
                sideBySide: false,
                widgetPositioning: {
                    horizontal: 'left',
                    vertical: 'top'
                }
            }).on('dp.change', function (ev) {
                var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                    end_time = $(this).closest('tr').find('.txtendTime').val(),
                    d1 = moment(start_time, date_format_policy + " HH:mm"),
                    d2 = moment(end_time, date_format_policy + " HH:mm"),
                    duration = d2.diff(d1, 'hours');
                if ($.isNumeric(duration)) {
                    $(this).closest('tr').find('.hoursSpent').val(duration);
                } else {
                    $(this).closest('tr').find('.hoursSpent').val(0);
                }
            });

            $('#endDate_machine_' + documentID + '_' + search_id4).datetimepicker({
                showTodayButton: true,
                format: date_format_policy + " hh:mm A",
                sideBySide: false,
                useCurrent: false,
                widgetPositioning: {
                    horizontal: 'left',
                    vertical: 'top'
                }
            }).on('dp.change', function (ev) {
                var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                    end_time = $(this).closest('tr').find('.txtendTime').val(),
                    d1 = moment(start_time, date_format_policy + " HH:mm"),
                    d2 = moment(end_time, date_format_policy + " HH:mm"),
                    duration = d2.diff(d1, 'hours');
                if ($.isNumeric(duration)) {
                    $(this).closest('tr').find('.hoursSpent').val(duration);
                } else {
                    $(this).closest('tr').find('.hoursSpent').val(0);
                }
            });

            $('#startDate_machine_' + documentID + '_' + search_id4).on('dp.change', function (e) {
                var d_id = $(this).data('id');
                $('#' + d_id).data('DateTimePicker').minDate(e.date);
                $(this).data("DateTimePicker").hide();
            });

            $('#endDate_machine_' + documentID + '_' + search_id4).on('dp.change', function (e) {
                var d_id = $(this).data('id');
                $('#' + d_id).data('DateTimePicker').maxDate(e.date);
                $(this).data("DateTimePicker").hide();
            });
        }, 500);
    }

    function init_workprocess_crew(documentID) {
        $('#crew_body_' + documentID).append('<tr><td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control c_search" name="search[]" placeholder="Crew" id="c_search_' + documentID + '_' + search_id3 + '"> <input type="hidden" class="form-control crewID" name="crewID[]"> <input type="hidden" class="form-control workProcessCrewID" name="workProcessCrewID[]"> </td> <td><input type="text" name="designation" class="form-control designation" readonly=""></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id3 + ' startTime" id="startDate_crew_' + documentID + '_' + search_id3 + '" data-id="endDate_crew_' + documentID + '_' + search_id3 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="startTime[]" class="form-control txtstartTime" required=""></div></td> <td><div class="input-group dateTimePicker_crew_' + documentID + '_' + search_id3 + ' endTime" id="endDate_crew_' + documentID + '_' + search_id3 + '" data-id="startDate_crew_' + documentID + '_' + search_id3 + '"><div class="input-group-addon"><i class="fa fa-calendar"></i></div><input type="text" name="endTime[]" class="form-control txtendTime" required=""></div></td> <td><input type="text" name="hoursSpent[]" class="form-control hoursSpent" readonly></td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        setTimeout(function () {
            initializCrewTypeahead(search_id3, documentID);
            $('#startDate_crew_' + documentID + '_' + search_id3).datetimepicker({
                showTodayButton: true,
                format: date_format_policy + " hh:mm A",
                sideBySide: false,
                widgetPositioning: {
                    horizontal: 'left',
                    vertical: 'top'
                }
            }).on('dp.change', function (ev) {
                var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                    end_time = $(this).closest('tr').find('.txtendTime').val(),
                    d1 = moment(start_time, date_format_policy + " HH:mm"),
                    d2 = moment(end_time, date_format_policy + " HH:mm"),
                    duration = d2.diff(d1, 'hours');
                if ($.isNumeric(duration)) {
                    $(this).closest('tr').find('.hoursSpent').val(duration);
                } else {
                    $(this).closest('tr').find('.hoursSpent').val(0);
                }
            });

            $('#endDate_crew_' + documentID + '_' + search_id3).datetimepicker({
                showTodayButton: true,
                format: date_format_policy + " hh:mm A",
                sideBySide: false,
                useCurrent: false,
                widgetPositioning: {
                    horizontal: 'left',
                    vertical: 'top'
                }
            }).on('dp.change', function (ev) {
                var start_time = $(this).closest('tr').find('.txtstartTime').val(),
                    end_time = $(this).closest('tr').find('.txtendTime').val(),
                    d1 = moment(start_time, date_format_policy + " HH:mm"),
                    d2 = moment(end_time, date_format_policy + " HH:mm"),
                    duration = d2.diff(d1, 'hours');
                if ($.isNumeric(duration)) {
                    $(this).closest('tr').find('.hoursSpent').val(duration);
                } else {
                    $(this).closest('tr').find('.hoursSpent').val(0);
                }
            });

            $('#startDate_crew_' + documentID + '_' + search_id3).on('dp.change', function (e) {
                var d_id = $(this).data('id');
                $('#' + d_id).data('DateTimePicker').minDate(e.date);
                $(this).data("DateTimePicker").hide();
            });

            $('#endDate_crew_' + documentID + '_' + search_id3).on('dp.change', function (e) {
                var d_id = $(this).data('id');
                $('#' + d_id).data('DateTimePicker').maxDate(e.date);
                $(this).data("DateTimePicker").hide();
            });

        }, 500);
    }

    function delete_workprocess_attachment(id, fileName, documentID, workProcessID, workFlowID) {
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
                    data: {attachmentID: id, myFileName: fileName},
                    url: "<?php echo site_url('MFQ_Template/delete_workprocess_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            load_attachments(documentID, workProcessID, workFlowID);
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

    function delete_workprocess_crew(id, masterID, documentID) {
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
                    url: "<?php echo site_url('MFQ_Template/delete_workprocess_crew'); ?>",
                    type: 'post',
                    data: {workProcessCrewID: id, masterID: masterID},
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
                                init_workprocess_crew(documentID);
                            }
                            $("#rowCR_" + documentID + "_" + id).hide();
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

    function delete_workprocess_machine(id, masterID, documentID) {
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
                    url: "<?php echo site_url('MFQ_Template/delete_workprocess_machine'); ?>",
                    type: 'post',
                    data: {workProcessMachineID: id, masterID: masterID},
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
                                init_workprocess_machine();
                            }
                            $("#rowMAC_" + documentID + "_" + id).hide();
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

    function load_unit_of_measure() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {mfqItemID: $('#finishGoods').val()},
            url: "<?php echo site_url('MFQ_Job/load_unit_of_measure'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#itemUoM').val(data.UnitDes);
                $('#job_form').bootstrapValidator('revalidateField', 'itemUoM');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function loadEstimateItem() {
        var customer = $('#mfqCustomerAutoID').val();
        if (customer) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {mfqCustomerAutoID: customer},
                url: "<?php echo site_url('MFQ_Job/load_mfq_estimate'); ?>",
                success: function (data) {
                    $('#ciCode').empty();
                    $('#table_body_ci_detail').html('<tr class="danger"><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>');
                    var mySelect = $('#ciCode');
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (key, value) {
                            mySelect.append('<li><a onclick="fetch_estimate_detail(' + value['estimateMasterID'] + ')">' + value['estimateCode'] + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>');
                        });
                    } else {
                        mySelect.append('<li><a>No Records found</a></li>');
                    }
                    $("#estimate_detail_modal").modal({backdrop: "static"});
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        } else {
            myAlert('w', 'Please select a customer');
        }
    }

    function fetch_estimate_detail(estimateMasterID) {
        if (estimateMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {estimateMasterID: estimateMasterID},
                url: "<?php echo site_url('MFQ_Estimate/load_mfq_estimate_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body_ci_detail').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#table_body_ci_detail').append('<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $.each(data, function (key, value) {
                            $('#table_body_ci_detail').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td >' + value['UnitDes'] + '</td><td>' + value['expectedQty'] + '</td><td>' + commaSeparateNumber(value['estimatedCost'], value['companyLocalCurrencyDecimalPlaces']) + '</td><td>' + commaSeparateNumber(value['sellingPrice'], value['companyLocalCurrencyDecimalPlaces']) + '</td><td><input type="checkbox" class="chb" name="checked[]" value="1" data-mfqitemid="' + value["mfqItemID"] + '" data-estimatemasterid="' + value["estimateMasterID"] + '" data-estimatedetailid="' + value["estimateDetailID"] + '"  data-itemdescription="' + value["itemSystemCode"] + ' - ' + value["itemDescription"] + '" data-unitdes = "' + value['UnitDes'] + '" data-expectedqty = "' + value['expectedQty'] + '" data-bommasterid = "' + value['bomMasterID'] + '"></td></tr>');
                            x++;
                        });
                    }
                    $(".chb").change(function () {
                        $(".chb").prop('checked', false);
                        $(this).prop('checked', true);
                    });
                    number_validation();
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function add_estimate_items() {
        var selectedChk = $('input.chb:checked');
        $('#estMfqItemID').val(selectedChk.data('mfqitemid'));
        $('#estimateDetailID').val(selectedChk.data('estimatedetailid'));
        $('#itemDescription').val(selectedChk.data('itemdescription'));
        $('#qty').val(selectedChk.data('expectedqty'));
        $('#itemUoM').val(selectedChk.data('unitdes'));
        $('#bomMasterID').val(selectedChk.data('bommasterid'));
        $("#estimate_detail_modal").modal('hide');
    }

    function getSegmentHours(element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {segmentID: $(element).val()},
            url: "<?php echo site_url('MFQ_BillOfMaterial/load_segment_hours'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!$.isEmptyObject(data)) {
                    $(element).closest('tr').find('.totalHours').val(data.hours);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function close_job() {
        swal({
                title: "Are you sure?",
                text: "You want to close this job",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: true
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {workProcessID: workProcessID},
                    url: "<?php echo site_url('MFQ_Job/fetch_job_item_units'); ?>",
                    beforeSend: function () {
                        $('.secondaryQtyDiv').removeClass('hide');
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        if(data['status'] == 's'){
                            $('#secondaryQty').val('');
                            $('#primaryQty').val(data['qty']);
                            $('#subItemUOM').val(data['subItemUOM']);
                            if(data['subItemUOM'] == 2) {
                                $('#primaryQty').prop('disabled', true);
                            }
                            $('.primaryQtyDesc').text(data['defaultUnitOfMeasure']);
                            $('.secondaryQtyDesc').text(data['UnitDes']);
                            if(data['UnitID'] == null) {
                                $('.secondaryQtyDiv').addClass('hide');
                            }
                            if(data['isSubitemExist'] == 1) {
                                $('.closing_job').addClass('hide');
                                $('.update_sub_item').removeClass('hide');
                            } else {
                                $('.update_sub_item').addClass('hide');
                                $('.closing_job').removeClass('hide');
                            }
                            $('#outputWarehouseAutoID').val(data['wareHouseAutoID']).change();
                            $('#closeDateModal').modal();
                        }else{
                            myAlert(data['status'],data['message']);
                            return false;
                        }
                       
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();

                    }
                });
            });
    }

    function getSemiFinishGoods(formData) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                mfqWarehouseAutoID: $("#mfqWarehouseAutoID").val(),
                bomMasterID: $("#bomMasterID").val(),
                qty: $("#qty").val()
            },
            url: "<?php echo site_url('MFQ_Job/getSemifinishGoods'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                stopLoad();
                $('#table_body_semifinishgoods').empty();
                if (jQuery.isEmptyObject(data)) {
                    saveJob(formData);
                } else {
                    $("#semifinishgoods_modal").modal("show");
                    $.each(data, function (key, value) {
                        $('#table_body_semifinishgoods').append('<tr><td>' + value['itemSystemCode'] + ' - ' + value['itemDescription'] + '</td><td style="text-align: right">' + value['bomQty'] + '</td><td style="text-align: right">' + value['currentStock'] + '</td><td style="text-align: right">' + value['qtyInProduction'] + '</td><td style="text-align: right">' + value['qtyInUse'] + '</td><td style="text-align: right">' + value['remainingQty'] + '</td></tr>');
                    });
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();

            }
        });
    }

    function saveJob(data) {
        data.push({'name': 'isProcessBased', 'value': 1});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job/save_job_header'); ?>",
            beforeSend: function () {
                startLoad();
                if ($('#completeStatus').val() == 1) {
                    $('#type').prop('disabled', true);
                    $('#workFlowTemplateID').prop('disabled', true);
                    $('#finishgoods_search').prop('disabled', true);
                }
                <?php if($policy_id == 'EST') { ?>
                $('#type').prop('disabled', true);
                $('#mfqCustomerAutoID').prop('disabled', true);
                <?php } ?>
            },
            success: function (data) {
                stopLoad();
                
              
                myAlert(data[0], data[1],data[2]);
                if (data[0] == 's') {
                    if (!$('#workProcessID').val()) {
                        $('#jobNumber').html("<span style='color:#aaa'>Job Code: </span>" + data[3]);
                        $('#estimateCode').html("<span style='color:#aaa'>Estimate Code: </span>" + data[4]);
                        $('#inquiryCode').html("<span style='color:#aaa'>Inquiry Code: </span>" + data[5]);
                    }
                    workProcessID = data[2];
                    $('#workProcessID').val(workProcessID);
                    $('#workFlowTemplateID2').val($('#workFlowTemplateID').val());
                    $('#qty2').val($('#qty').val());

                    $('#workFlowTemplateID').prop('disabled', true);
                    $('#finishgoods_search').prop('disabled', true);
                    $('#addEstimate').prop('disabled', true);
                    // $('#mfqCustomerAutoID').prop('disabled', true);
                    $('#mfqSegmentID').prop('disabled', true);
                    $('#mfqWarehouseAutoID').prop('disabled', true);
                    $('#type').prop('disabled', true);
                    $('#qty').prop('readonly', true);

                    if (!$.isEmptyObject(data[6])) {
                        var jobMessage = data[6].join(",");
                        swal("New Job Created!", jobMessage, "success");
                    }
                    $('.btn-wizard').removeClass('disabled');
                    $('[href=#step2]').tab('show');
                    $('[href=#step2]').trigger('click');
                   
                    $(document).scrollTop(0);
                    $('.headerclose').click(function () {
                        fetchPage('system/mfq/mfq_job_process_based', '', 'Workflow');
                    });
                    customizeData = [];
                } else if (data[0] == 'e' && data[2]!='') { 
                    $('#bom-not-config-error').empty();
                    if (!jQuery.isEmptyObject(data[2])) {
                        $('#error-msg-title').html(data[1]);
                        $.each(data[2], function (key, value) {
                            $('#bom-not-config-error').append('<div>'+value+'</div>');
                        });   
                        $('#bomnotconfig').modal('show');
                        $('.btn-primary').prop('disabled', false);
                    }   
                }
                else {
                    $('.btn-primary').prop('disabled', false);
                    $('#workFlowTemplateID').prop('disabled', false);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();

            }
        });
    }

    function saveSemiFinishGoodsJob() {
        saveJob(jobformData);
        $("#semifinishgoods_modal").modal("hide");
    }

    function customizeTemplate() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {templateMasterID: $("#workFlowTemplateID").val()},
            url: "<?php echo site_url('MFQ_Template/load_custome_workflow_design'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $("#customizeTemplateBody").html(data);
                    $("#customize_modal").modal();
                } else {
                    $("#customize_modal").modal();
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function save_customize_template() {
        customizeData = [];
        if ($("#customize_workprocess li").length > 0) {
            var workflowtemplateid = [];
            var templatedetailid = [];
            var workflowid = [];
            var linkworkflow = [];
            var description = [];
            $("#customize_workprocess li").each(function () {
                workflowtemplateid.push($(this).data("workflowtemplateid"));
                templatedetailid.push($(this).data("templatedetailid"));
                workflowid.push($(this).data("workflowid"));
                linkworkflow.push($(this).data("linkworkflow"));
                description.push($(this).data("description"));
            });
            customizeData.push({name: "customWorkFlowTemplateID", value: workflowtemplateid});
            customizeData.push({name: "customTemplateDetailID", value: templatedetailid});
            customizeData.push({name: "customWorkFlowID", value: workflowid});
            customizeData.push({name: "customLinkWorkFlow", value: linkworkflow});
            customizeData.push({name: "customDescription", value: description});
            /*if ($("#linkProcess").is(":checked")) {*/
                customizeData.push({name: "linkProcess", value: 1});
           /* } else {
                customizeData.push({name: "linkProcess", value: 0});
            }*/
            $("#customize_modal").modal('hide');
        } else {
            myAlert('w', "Please select work process");
        }
    }

    function load_usage_history(workProcessID,jobCardID,autoID,typeID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {jobID: workProcessID,jobCardID:jobCardID,autoID:autoID,typeID:typeID},
            url: "<?php echo site_url('MFQ_Job/fetch_usage_history'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#usage_history_modal').modal();
                if (!$.isEmptyObject(data)) {
                    $('#usage_history_body').empty();
                    $.each(data, function (key, value) {
                        $('#usage_history_body').append('<tr> <td> ' + parseFloat(key+1) + ' </td><td class="text-right"> ' + value["usageAmount"] + ' </td><td> ' + value["createdDateTime"] + ' </td><td> ' + value["createdUserName"] + ' </td> </tr>');
                    });
                } else {
                    $('#usage_history_body').html("<tr><td colspan='4'><div class='callout callout-warning' style='margin-bottom: 0px'>No history found</td></td></tr>");
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        })
    }

    function get_item_wise_template(itemAutoID, workFlowTemplateID='') 
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            url: "<?php echo site_url('MFQ_Job/get_item_wise_template'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#workFlowTemplateID').empty();
                    var mySelect = $('#workFlowTemplateID');
                    mySelect.append($('<option></option>').val('').html('Select Template'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['templateMasterID']).html(text['templateDescription']));
                    });
                    if(workFlowTemplateID != '') {
                        $('#workFlowTemplateID').val(workFlowTemplateID).change();
                    }
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();

            }
        });
    }

    function Update_sub_item_details()
    {
        var outputQty = $('#primaryQty').val();
        var secondaryQty = $('#secondaryQty').val();
        var outputWarehouseAutoID = $('#outputWarehouseAutoID').val();
        var subItemUOM = $('#subItemUOM').val();
        if(outputQty > 0 || subItemUOM == 2) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'outputQty': outputQty, workProcessID: workProcessID, outputWarehouseAutoID: outputWarehouseAutoID,'secondaryQty':secondaryQty,'subItemUOM':subItemUOM},
                url: "<?php echo site_url('MFQ_Job/insert_sub_item_configuration'); ?>",
                beforeSend: function () {
                startLoad(); 
                $("#subItemMasterList").html('<div style="text-align: center; margin: 10px;"><i class="fa fa-refresh fa-spin"></i> Loading </div>');
                },
                success: function (data) {
                    stopLoad();
                    if(data[0] == 'e'){
                        myAlert(data[0], data[1]);
                    }else{
                        $("#subItemMaster_modal").modal('show');
                        $("#subItemMasterList").html(data.view);
                    } 
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        } else {
            myAlert('w', 'Please Update Output Qty to Proceed!');
        }
    }
    
    function saveSubItemMasterTmp() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#subItemMaster_form").serialize(),
            url: "<?php echo site_url('Grv/saveSubItemMasterTmpDynamic'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if($('#subItemUOM').val() == 2) {
                    $('#primaryQty').prop('disabled', false);
                    $('#primaryQty').val(data[2]);
                }
                $("#subItemMaster_modal").modal('hide');
                stopLoad();
                $('#job_close_form').submit();
            }, error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Error : ' + errorThrown);
            }
        });
    }

    var companyPolicy = '<?php echo $flowserveLanguagePolicy ?>';

    function selectItem(value){ 
        if(itemAutoID == '' && value > 0 ) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'workFlowID': value},
                url: "<?php echo site_url('MFQ_Job/get_workflowDefaultItem'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        if(companyPolicy == 'FlowServe'){
                            $('#finishgoods_search').val();
                        }else{
                            $('#finishgoods_search').val(data['item']);
                        }
                       
                        $('#finishGoods').val(data['mfqItemID']);
                        $('#itemUoM').val(data['defaultUnitOfMeasure']);

                    }else { 
                        $('#finishgoods_search').val(null);
                        $('#finishGoods').val(null);
                        $('#itemUoM').val(null);
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();

                }
            });
        }
    }

    function open_history(detID,field,type=0) {
        template_userGroupDetail(detID,field,type);
        $('#mfq_customerinquirychangehistory_model').modal('show');
    }
        function template_userGroupDetail(detID,field,type) {
            oTable2 = $('#employee_sync').DataTable({
                "ordering": false,
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": false,
                "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
                "sAjaxSource": "<?php echo site_url('MFQ_Job/fetch_mfq_job_inquiry_history'); ?>",
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
                    aoData.push({"name": "detID", "value": detID});
                    aoData.push({"name": "field", "value": field});
                    aoData.push({"name": "docID", "value": 'JOB'});
                    aoData.push({"name": "masterID", "value": workProcessID});
                    aoData.push({"name": "type", "value": type});
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
    
    
    
    function add_po_service(workprocesseID,jobCardID){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'workFlowID': workprocesseID,'jobCardID':jobCardID},
                url: "<?php echo site_url('MFQ_Job/get_added_third_party_suppliers'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    var num = 1;
                    $('#mfq_po_body').empty();
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (i, val) {

                            if(val.purchaseOrderID){
                                var checkbox = '<a class="btn btn-primary" onclick="documentPageView_modal(\'PO\', '+val.purchaseOrderID+')"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></a>';
                            }else{
                                var checkbox = '<input type="checkbox" class="overheadID" name="overheadID[]" value="'+val.jcOverHeadID+'">';
                            }
                           
                            var tr = '<tr><td>'+num+'</td><td>'+val.overHeadCode+'</td><td>'+val.description+'</td><td>'+val.supplierSystemCode+'-'+val.supplierName+'</td><td>'+val.transactionCurrency+'</td><td>'+val.transactionAmount+'</td><td>'+checkbox+'</td></tr>';
                            num++;
                            $('#mfq_po_body').append(tr);
                        });
                    }else{
                        var tr = '<tr><td colspan="3">No Items Available</td></tr>';
                        $('#mfq_po_body').append(tr);
                    }

               
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();

                }
            });
    
        $('#mfq_thired_party_po_generate').modal('toggle');
    }

    function create_po_document(){

        var data = $("#mfq_overhead_po_form").serializeArray();
        
        swal({
                    title: "Are you sure?",
                    text: "You want to create po for these?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Create"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('MFQ_Job/po_genearete_overhead'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                        
                        },
                        error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            stopLoad();

                        }
                    });
                });
    }
</script>