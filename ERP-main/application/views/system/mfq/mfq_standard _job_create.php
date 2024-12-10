<?php echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$data_set = array(0 => array('estimateMasterID' => '', 'estimateDetailID' => '', 'bomMasterID' => '', 'mfqCustomerAutoID' => '', 'description' => '', 'mfqItemID' => '', 'unitDes' => '', 'type' => 1, 'itemDescription' => '', 'expectedQty' => 0, 'mfqSegmentID' => '', 'mfqWarehouseAutoID' => ''));
if ($data_arr) {
    $data_set = $data_arr;
}
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<!--<script src="<?php /*echo base_url('plugins/html5sortable/jquery.sortable.js'); */ ?>"></script>-->
<!--<link rel="stylesheet"
      href="<?php /*echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); */ ?>"/>-->
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

    .table-responsive {
        overflow: visible !important
    }

</style>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary size-sm" href="#step1" data-toggle="tab">Step 1 - Standard Job Job Header</a>
    <a class="btn btn-default size-sm btn-wizard" href="#step2" onclick="load_workflow_design()" data-toggle="tab">Step
        2 - Job Detail</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="job_form"'); ?>
       <!-- <input type="hidden" name="workProcessID" id="workProcessID">
        <input type="hidden" name="completeStatus" id="completeStatus" value="0">
        <input type="hidden" name="fromType" id="fromType" value="<?php /*echo $policy_id; */?>">-->
        <div class="row">
            <div class="col-md-6 animated zoomIn">
                <div class="row">
                    <div class="form-group col-sm-4" style="margin-top: 10px;">
                        <label class="title">Customer Name</label>
                    </div>
                    <div class="form-group col-sm-7" style="margin-top: 10px;">
                        <span class="input-req"
                              title="Required Field"><?php echo form_dropdown('mfqCustomerAutoID', all_mfq_customer_drop(), $data_set[0]['mfqCustomerAutoID'], 'class="form-control select2" id="mfqCustomerAutoID"');
                            ?>
                            <span class="input-req-inner"></span></span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Type</label>
                    </div>
                    <div class="form-group col-sm-7">
                         <span class="input-req"
                               title="Required Field">
                        <?php echo form_dropdown('type', array(1 => "General", 2 => "Based on Estimate"), $data_set[0]['type'], 'class="form-control select2" id="type"');
                        ?>
                             <span
                                     class="input-req-inner"></span></span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Description</label>
                    </div>
                    <div class="form-group col-sm-7">
                <span class="input-req" title="Required Field"><input type="text" name="description" id="description"
                                                                      class="form-control"
                                                                      value="<?php echo $data_set[0]['description']; ?>"
                                                                      required><span
                            class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Template</label>
                    </div>
                    <div class="form-group col-sm-7">
                <span class="input-req"
                      title="Required Field">
                    <select class="form-control" id="workFlowTemplateID" name="workFlowTemplateID">
                        <option value="">Select</option>
                        <?php
                        $templates = get_all_mfq_template();
                        foreach ($templates as $row) {
                            echo '<option value="' . $row["templateMasterID"] . '" data-isdefault="' . $row["isDefault"] . '">' . $row["templateDescription"] . '</option>';
                        }
                        ?>
                    </select>
                    <?php //echo form_dropdown('workFlowTemplateID', get_all_mfq_template(), '', 'class="form-control" id="workFlowTemplateID"  required'); ?>
                    <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-1" style="padding: 0px">
                        <button type="button" id="addTemplate" onclick="customizeTemplate()"
                                class="btn btn-primary" title="Customize Template"><i class="fa fa-cog"
                                                                                      aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="row" id="general">
                    <div class="form-group col-sm-4">
                        <label class="title">Item</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req"
                              title="Required Field">
                        <?php /*echo form_dropdown('mfqItemID', all_finish_goods_drop(), '', 'class="form-control select2" id="finishGoods"');
                        */ ?>
                            <div id="itemSearchBox">
                            <input type="text" class="form-control finishgoods_search" name="search[]"
                                   placeholder="Item ID, Item Description..." id="finishgoods_search"> <input
                                        type="hidden" class="form-control mfqItemID" name="mfqItemID" id="finishGoods"></div>
                            <span class="input-req-inner"></span></span>
                        <input type="hidden" name="" value="" id="finishGoods2">
                    </div>
                </div>

                <div class="row" id="estimate" style="display: none;">
                    <div class="form-group col-sm-4">
                        <label class="title">Item</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req"
                              title="Required Field">
                                <input type="text" class="form-control" id="itemDescription"
                                       value="<?php echo $data_set[0]['itemDescription']; ?>" disabled>
                                <input type="hidden" class="" name="estimateDetailID" id="estimateDetailID"
                                       value="<?php echo $data_set[0]['estimateDetailID']; ?>">
                                <input type="hidden" class="" name="estMfqItemID" id="estMfqItemID"
                                       value="<?php echo $data_set[0]['mfqItemID']; ?>">
                                <input type="hidden" class="" name="bomMasterID" id="bomMasterID"
                                       value="<?php echo $data_set[0]['bomMasterID']; ?>">
                            <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-1" style="padding: 0px">
                        <button type="button" name="addEstimate" id="addEstimate" onclick="loadEstimateItem()"
                                class="btn btn-primary" title="Add item from estimate"><i class="fa fa-level-down"
                                                                                          aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Qty</label>
                    </div>
                    <div class="form-group col-sm-7">
                         <span class="input-req"
                               title="Required Field">
                        <input type="text" name="qty" id="qty" class="form-control"
                               onkeypress="return validateFloatKeyPress(this,event)"
                               value="<?php echo $data_set[0]['expectedQty']; ?>">
                             <span class="input-req-inner"></span></span>
                        <input type="hidden" name="qty2" id="qty2">
                    </div>
                </div>

            </div>
            <div class="col-md-6 animated zoomIn">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Document Date</label>
                    </div>
                    <div class="form-group col-sm-7">
                <span class="input-req"
                      title="Required Field"><div class="input-group datepic" id="dateStartDate">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="startDate" id="startDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" class="form-control startDate" required>
                    </div>
                    <span class="input-req-inner"></span></span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Expiry date</label>
                    </div>
                    <div class="form-group col-sm-7">
                <span class="input-req"
                      title="Required Field"><div class="input-group datepic" id="dateEndDate">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="endDate" id="endDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" class="form-control endDate" required>
                    </div>
                    <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">UoM</label>
                    </div>
                    <div class="form-group col-sm-7">
                         <span class="input-req" title="Required Field">
                        <input type="text" name="itemUoM" id="itemUoM" class="form-control"
                               value="<?php echo $data_set[0]['unitDes']; ?>" readonly>
                             <span class="input-req-inner"></span></span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Department</label>
                    </div>
                    <div class="form-group col-sm-7">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('mfqSegmentID', fetch_mfq_segment(), $data_set[0]['mfqSegmentID'], 'class="form-control select2" id="mfqSegmentID"');
                        ?><span class="input-req-inner"></span></span>
                    </div>
                </div>


                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Warehouse</label>
                    </div>
                    <div class="form-group col-sm-7">
                         <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('mfqWarehouseAutoID', all_mfq_warehouse_drop(), $data_set[0]['mfqWarehouseAutoID'], 'class="form-control select2" id="mfqWarehouseAutoID"'); ?>
                             <span class="input-req-inner"></span></span>

                    </div>
                </div>

            </div>
            <div class="row col-md-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" type="submit" id="saveJob">Save</button>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div id="" class="row review hide">
            <div class="col-md-12"><span class="no-print pull-right"> <a class="btn btn-default btn-sm" id="de_link"
                                                                         target="_blank" href="#"><span
                                class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review Entries </a> </span>
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
                        <button class="btn btn-primary-new size-lg" type="button" onclick="close_job()"><i class="fa fa-times"
                                                                                               aria-hidden="true"></i>
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
                <div class="row">
                    <div class="col-md-12">

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="title">Close Date</label>
                            </div>
                            <div class="form-group col-sm-7">
                <span class="input-req"
                      title="Required Field"><div class="input-group datepic" id="closeDateDiv">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="closedDate" id="closedDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" class="form-control endDate" required>
                    </div>
                    <span class="input-req-inner"></span></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="title">Close Comment</label>
                            </div>
                            <div class="form-group col-sm-7">
                <span class="input-req"
                      title="Required Field">
                       <textarea name="closedComment" id="closedComment" rows="2" class="form-control"></textarea>
                    <span class="input-req-inner"></span></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Close Job
                </button>
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

<!--<script src="<?php /*echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); */ ?>"></script>
<script src="<?php /*echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); */ ?>"></script>
<script src="<?php /*echo base_url('plugins/bootstrap-slider-master/dist/bootstrap-slider.min.js'); */ ?>"></script>-->
<script type="text/javascript">

    var search_id3 = 1;
    var search_id4 = 1;
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    var jobformData;
    var customizeData = [];
    var data;
    $(document).ready(function () {
        $("#addTemplate").hide();
        $(".select2").select2();
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
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

        <?php if($policy_id == 'MFQ') { ?>
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_standard_job_card', '', 'Workflow');
        });
        <?php
        } else {
        ?>
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_standard_job_card', '', 'Workflow');
            /*if (workProcessID) {
                fetchPage('system/mfq/mfq_job', '', 'Workflow');
            } else {
                fetchPage('system/mfq/mfq_estimate', '', 'Estimate');
            }*/
        });
        setTimeout(function () {
            var workFlowTemplateIDValue = $("#workFlowTemplateID option[data-isdefault='1']").val();
            $("#workFlowTemplateID").val(workFlowTemplateIDValue).change();
        }, 500);
        $("#addEstimate").hide();

        setTimeout(function () {
            $('#type').trigger('change');
        }, 500);
        <?php
        }
        ?>

        $('#job_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: 'Description is required.'}}},
                workFlowTemplateID: {validators: {notEmpty: {message: 'Template is required.'}}},
                startDate: {validators: {notEmpty: {message: 'Start Date is required.'}}},
                endDate: {validators: {notEmpty: {message: 'End Date is required.'}}},
                /*mfqItemID: {validators: {notEmpty: {message: 'Item is required.'}}},*/
                qty: {validators: {notEmpty: {message: 'Qty is required.'}}},
                itemUoM: {validators: {notEmpty: {message: 'UOM is required.'}}},
                mfqCustomerAutoID: {validators: {notEmpty: {message: 'Customer is required.'}}},
                mfqSegmentID: {validators: {notEmpty: {message: 'Segment is required.'}}},
                mfqWarehouseAutoID: {validators: {notEmpty: {message: 'Warehouse is required.'}}},
            }
        }).on('success.form.bv', function (e) {
            $('#workFlowTemplateID').prop('disabled', false);
            $('#finishgoods_search').prop('disabled', false);
            $('#type').prop('disabled', false);
            $('#mfqCustomerAutoID').prop('disabled', false);
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
            <?php } else{
            ?>
            saveJob(data);
            <?php
            } ?>
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
                closedComment: {validators: {notEmpty: {message: 'Comment is required.'}}},
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({name: 'workProcessID', value: workProcessID});
            data.push({name: 'method', value: "1"});// close
            $.ajax({
                url: "<?php echo site_url('MFQ_Job/close_job'); ?>",
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
                            $("#table_body_linkedDoc").append("<tr><td>" + i + "</td><td>" + v.documentCode + "</td><td>" + v.documentDate + "</td></tr>");
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
            if (isDefault == 1) {
                $("#addTemplate").show();
            } else {
                $("#addTemplate").hide();
            }
        });
        initializeitemTypeahead();
    });

    function initializeitemTypeahead() {
        $('#finishgoods_search').autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_finish_goods/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#itemSearchBox').find('.mfqItemID').val(suggestion.mfqItemID);
                    if ($('#type').val() == 1) {
                        $('#itemUoM').val(suggestion.uom);
                        $('#job_form').bootstrapValidator('revalidateField', 'itemUoM');
                    }
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
            url: "<?php echo site_url('MFQ_Job/close_job'); ?>",
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
                        $("#table_body_linkedDoc").append("<tr><td>" + i + "</td><td>" + v.documentCode + "</td><td>" + v.documentDate + "</td></tr>");
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
                        workProcessID = data['workProcessID'];
                        $('#workProcessID').val(workProcessID);
                        $('#workFlowTemplateID').val(data["workFlowTemplateID"]).change();
                        $('#workFlowTemplateID2').val(data["workFlowTemplateID"]);
                        $('#description').val(data['description']);
                        $('#documentDate').val(data['documentDate']).change();
                        $('#jobNumber').html("<span style='color:#aaa'>Job Code: </span>" + data['documentCode']);
                        $('#estimateCode').html("<span style='color:#aaa'>Estimate Code: </span>" + data['estimateCode']);
                        $('#inquiryCode').html("<span style='color:#aaa'>Inquiry Code: </span>" + data['ciCode']);
                        $('#qty').val(data['qty']);
                        /*$('#qty').prop('readonly', true);*/
                        $('#qty2').val(data['qty']);
                        $('#itemUoM').val(data['UnitDes']);
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
                        $('#mfqCustomerAutoID').prop('disabled', true);
                        $('#mfqSegmentID').prop('disabled', true);
                        $('#mfqWarehouseAutoID').prop('disabled', true);
                        $('#finishgoods_search').prop('disabled', true);
                        $('#addEstimate').prop('disabled', true);
                        $('#workFlowTemplateID').prop('disabled', true);
                        $("#addTemplate").hide();

                        if (data['isFromEstimate'] == 1) {
                            $('#type').prop('disabled', true);
                            $('#mfqCustomerAutoID').prop('disabled', true);
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
                            de_link = "<?php echo site_url('MFQ_Job/fetch_double_entry_job'); ?>/" + workProcessID;
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
                url: "<?php echo site_url('MFQ_Template/load_workflow_process_design'); ?>",
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

    function get_workflow_template(pageNameLink, tabID, workFlowID, documentID, type, tab, templateDetailID, linkworkFlow) {
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
                    linkworkFlow: linkworkFlow
                },
                url: "<?php echo site_url('MFQ_Template/get_workflow_template'); ?>",
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
                        $('#show_all_attachments_' + documentID).append('<div class="row"> <div class="col-sm-12"> <div class="past-info"> <div id="toolbar"> <div class="toolbar-title">Files</div> </div> <div class="post-area"> <article class="post"> <a target="_blank" class="nopjax" href="<?php echo base_url() ?>attachments/mfq/' + v.myFileName + '"> <div class="item-label file">File</div> </a> <div class="time"><span class="hithighlight"></span></div><div class="icon"> <img src="<?php echo base_url('images/mfq/icon-file.png'); ?>" width="16" height="16" title="File"> </div> <header class="infoarea"> <strong class="attachemnt_title"> <img src="<?php echo base_url('images/mfq/icon_pic.gif'); ?>" style="vertical-align:top"> &nbsp;<a target="_blank" class="nopjax" href="<?php echo base_url() ?>attachments/mfq/' + v.myFileName + '">' + v.myFileName + '</a> <span style="display: inline-block;">' + v.fileSize + ' KB</span> <div><span class="attachemnt_title">' + v.attachmentDescription + '</span> </div> <div><span class="attachemnt_title" style="display: inline-block;">By: ' + v.createdUserName + '</span> <span class="deleteSpan" style="display: inline-block;"><a onclick="delete_workprocess_attachment(' + v.attachmentID + ',\'' + v.myFileName + '\',\'' + documentID + '\',' + workProcessID + ',' + workFlowID + ');"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></span> </div> </strong> </header> </article></div></div></div></div>');
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
                        $('#show_all_attachments_' + documentID).append('<div class="row"> <div class="col-sm-12"> <div class="past-info"> <div id="toolbar"> <div class="toolbar-title">Files</div> </div> <div class="post-area"> <article class="post"> <a target="_blank" class="nopjax" href="<?php echo base_url() ?>attachments/mfq/' + v.myFileName + '"> <div class="item-label file">File</div> </a> <div class="time"><span class="hithighlight"></span></div><div class="icon"> <img src="<?php echo base_url('images/mfq/icon-file.png'); ?>" width="16" height="16" title="File"> </div> <header class="infoarea"> <strong class="attachemnt_title"> <img src="<?php echo base_url('images/mfq/icon_pic.gif'); ?>" style="vertical-align:top"> &nbsp;<a target="_blank" class="nopjax" href="<?php echo base_url() ?>attachments/mfq/' + v.myFileName + '">' + v.myFileName + '</a> <span style="display: inline-block;">' + v.fileSize + ' KB</span> <div><span class="attachemnt_title">' + v.attachmentDescription + '</span> </div> <div><span class="attachemnt_title" style="display: inline-block;">By: ' + v.createdUserName + '</span> <span class="deleteSpan" style="display: inline-block;"><a onclick="delete_workprocess_attachment(' + v.attachmentID + ',\'' + v.myFileName + '\',\'' + documentID + '\',' + workProcessID + ',' + workFlowID + ');"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></span> </div> </strong> </header> </article></div></div></div></div>');
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
                $('#closeDateModal').modal();
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
                myAlert(data[0], data[1]);
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
                    $('#mfqCustomerAutoID').prop('disabled', true);
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
                    <?php if($policy_id == 'MFQ') { ?>
                    $('.headerclose').click(function () {
                        fetchPage('system/mfq/mfq_job', '', 'Workflow');
                    });
                    <?php
                    } else {
                    ?>
                    $('.headerclose').click(function () {
                        fetchPage('system/mfq/mfq_job', '', 'Workflow');
                        /*if (workProcessID) {
                            fetchPage('system/mfq/mfq_job', '', 'Workflow');
                        } else {
                            fetchPage('system/mfq/mfq_estimate', '', 'Estimate');
                        }*/
                    });
                    <?php
                    }
                    ?>
                    customizeData = [];
                } else {
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
            if ($("#linkProcess").is(":checked")) {
                customizeData.push({name: "linkProcess", value: 1});
            } else {
                customizeData.push({name: "linkProcess", value: 0});
            }
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

</script>