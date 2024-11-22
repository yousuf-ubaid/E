<?php
$date_format_policy = date_format_policy();
$segment = fetch_mfq_segment(true);
$jobCardID = 0;
$prevJobCard = "";
$prevJobCardID = "";
$unitPriceUpdatePolicy = getPolicyValues('JUP', 'All');
$readUnitPrice = '';
if($unitPriceUpdatePolicy != 1) {
    $readUnitPrice = 'readonly';
}
$jobCardRec = get_job_cardID($workProcessID, $workFlowID, $templateDetailID);
$jobMasterRec = get_job_master($workProcessID);
$umo_arr2 = all_umo_new_drop();
if ($linkWorkFlowID) {
    $prevJobCard = get_prev_job_card($workProcessID, $workFlowID, $linkWorkFlowID, $templateDetailID, $templateMasterID);
    $prevJobCardID = $prevJobCard["jobcard"]["jobcardID"];
} else {
    $linkWorkFlowID = 0;
}
if ($jobCardRec) {
    $jobCardID = $jobCardRec["jobcardID"];
}
$disablebutton = "";
if ($type == 1) {
    $disablebutton = "disabledbutton";
}
if ($type == 2) {
    if ($jobCardRec["status"] == 1) {
        $disablebutton = "disabledbutton";
    }
}
if(!isset($workFlowTemplateID)) { $workFlowTemplateID = ''; }
/*echo $jobMasterRec['bomMasterID'];
exit;*/
?>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
?>
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
</style>
<ul class="nav nav-tabs" id="main-tabs">
    <li class="active"><a href="#jobcard_<?php echo $documentID ?>" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('manufacturing_job')?><!--Job--></a>
    </li>
    <li><a href="#crew_<?php echo $documentID ?>" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('common_crew')?><!--Crew--> </a></li>
    <li><a href="#machine_<?php echo $documentID ?>" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('manufacturing_machine')?><!--Machine--> </a>
    </li>
    <li><a href="#pulledDoc_<?php echo $documentID ?>" onclick="load_pulled_document()" data-toggle="tab"><i class="fa fa-television"></i>Pulled Documents </a></li>
    <li><a href="#attachment_<?php echo $documentID ?>" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('common_attachment')?><!--Attachment-->
        </a></li>
    <li><a href="#review_<?php echo $documentID ?>" onclick="load_jobcard_print()" data-toggle="tab"><i
                    class="fa fa-television"></i><?php echo $this->lang->line('manufacturing_review_or_print')?><!--Review/Print-->
        </a>
    </li>
</ul>
<div class="tab-content jobcard">
    <div class="tab-pane active" id="jobcard_<?php echo $documentID ?>">
        <form action="" role="form" id="frm_jobcard_<?php echo $workFlowID ?>">
            <input type="hidden" name="workFlowID" value="<?php echo $workFlowID ?>">
            <input type="hidden" name="workProcessID" value="<?php echo $workProcessID ?>">
            <input type="hidden" name="jobCardID" id="jobCardID" value="<?php echo $jobCardID ?>">
            <input type="hidden" name="templateDetailID" id="templateDetailID" value="<?php echo $templateDetailID ?>">

            <?php
            $link_job = check_link_job_card($workProcessID, $templateDetailID);
            if (!empty($link_job) && $type == 1) {
                ?>
                <div class="row hide">
                    <br>
                    <div class="col-md-12 animated zoomIn">
                        <header class="head-title">
                            <h2>Link</h2>
                        </header>
                        <div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-2">
                                <label class="title">Job Card</label>
                            </div>
                            <div class="form-group col-sm-4">
                                <?php echo form_dropdown('link', link_job_card_drop_mfq($workProcessID, $templateDetailID,'',1), $linkWorkFlowID, 'class="form-control select2" id="link"');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="row <?php echo $disablebutton ?>">
                <br>
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('manufacturing_header')?><!--HEADER--></h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('manufacturing_job_no')?><!--Job No--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="jobNo" id="jobNo" class="form-control" value="<?php echo ($linkWorkFlowID && $type == 2 && !empty($prevJobCard["jobcard"]["jobNo"])) ? $prevJobCard["jobcard"]["jobNo"] : $type != 1 ? $jobMasterRec["documentCode"] : ""; ?>">
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('manufacturing_bom')?><!--BOM--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php echo form_dropdown('bomID2', all_bill_of_material_process_based_drop($workFlowTemplateID, $mfqItemID), '', 'class="form-control select2" id="bomID"'); ?>
                            <input type="hidden" name="bomID" value="" id="bomIDhidded">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('manufacturing_quotation_reference_id')?><!--Quotation Reference ID--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <input type="text" name="quotationRef" id="quotationRef" class="form-control"
                                   value="<?php echo ($linkWorkFlowID && $type == 2) ? $prevJobCard["jobcard"]["quotationRef"] : $type != 1 ? $jobMasterRec["estimateCode"] : ""; ?>">
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_description')?><!--Description--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="description" id="jobdescription" class="form-control" 
                                    value="<?php echo ($linkWorkFlowID && $type == 2 && !empty($prevJobCard["jobcard"]["jobDescription"])) ? $prevJobCard["jobcard"]["jobDescription"] : $type != 1 ? $jobMasterRec["description"] : ""; ?>">
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-2">
                            <label class="title">Start Date</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="jobCardStartDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="jobCardStartDate" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">Include Current Date</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php echo form_dropdown('includeCurrentDate', array(1=> 'No', 2=>'Yes'), 1, 'class="form-control select2" id="includeCurrentDate"'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <div class="row <?php echo $disablebutton ?>">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('manufacturing_material_consumption')?><!--Material Consumption--></h2>
                    </header>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="mfq_material_consumption" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%"></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_material_consumption')?><!--Material Consumption--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_part_no')?><!--Part No--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_of_measure_short')?><!--UoM--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_estimated_qty')?><!--Estimated Qty--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_usage_qty')?><!--Usage Qty--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_unit_cost')?><!--Unit Cost--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_material_cost')?><!--Material Cost--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_loss')?><!--Loss%--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_material_change')?><!--Material Charge--></th>
                                        <th style="min-width: 12%">Daily Update</th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_material('Process')"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="material_consumption_body">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-right"><?php echo $this->lang->line('manufacturing_material_totals')?><!--Material Totals--></div>
                                        </td>
                                        <td>
                                            <div id="tot_qtyUsed" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_qtyUsage" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_unitCost" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_materialCost" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_markupPrc" style="text-align: right"></div>
                                        </td>
                                        <td>
                                            <div id="tot_materialCharge" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id=""></div>
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <div class="row <?php echo $disablebutton ?>">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('manufacturing_labour_tasks')?><!--Labour Tasks--></h2>
                    </header>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="mfq_labour_task" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_labour_tasks')?><!--Labour Tasks--></th>
                                        <!--<th style="min-width: 12%">Activity Code</th>-->
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_uom')?><!--UoM--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_department')?><!--Department--></th>
                                        <th style="min-width: 12%">Sub Department</th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate')?><!--Unit Rate--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours')?><!--Total Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value')?><!--Total Value--></th>
                                        <th style="min-width: 12%">Daily Update</th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_labour('Process')"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="labour_task_body">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-right"><?php echo $this->lang->line('manufacturing_labour_totals')?><!--Labour Totals--></div>
                                        </td>
                                        <td>
                                            <div id="tot_lb_hourRate" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_lb_usageHours" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_lb_totalHours" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_lb_totalValue" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <div class="row <?php echo $disablebutton ?>">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('manufacturing_overhead_cost')?><!--Overhead Cost--></h2>
                    </header>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="mfq_overhead" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_overhead_cost')?><!--Overhead Cost--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_uom')?><!--UoM--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_department')?><!--Department--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate')?><!--Unit Rate--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours')?><!--Total Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value')?><!--Total Value--></th>
                                        <th style="min-width: 12%">Daily Update</th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_overhead('Process')"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="over_head_body">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            <div class="text-right"><?php echo $this->lang->line('manufacturing_overhead_totals')?><!--Overhead Totals--></div>
                                        </td>
                                        <td>
                                            <div id="tot_oh_hourRate" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_oh_usageHours" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_oh_totalHours" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_oh_totalValue" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>

            <div class="row <?php echo $disablebutton ?>">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>THIRD PARTY SERVICE</h2>
                    </header>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="mfq_thirdparty_service" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%">Tird Party Service</th>

                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_of_measure_short'); ?><!--UoM--></th>

                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate'); ?><!--Unit Rate--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours'); ?><!--Total Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value'); ?><!--Total Values--></th>
                                        <th style="min-width: 12%">Daily Update</th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <span class="button-wrap-box">
                                                    <button type="button" data-text="Add" onclick="add_more_third_party_service_job('Process')" class="button button-square button-tiny button-royal button-raised">
                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="third_party_service">

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="2">
                                            <div class="text-right">Third Party Service Total</div>
                                        </td>
                                        <td>
                                            <div id="tot_tps_hourRate"
                                                 style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div id="tot_tps_usageHours" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_tps_totalHours"
                                                 style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div id="tot_tps_totalValue"
                                                 style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>


            <div class="row <?php echo $disablebutton ?>">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('manufacturing_machine')?><!--Machine--></h2>
                    </header>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="mfq_machine_cost" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_machine')?><!--Machine--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_uom')?><!--UoM--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_department')?><!--Department--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate')?><!--Unit Rate--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours')?><!--Total Hours--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value')?><!--Total Value--></th>
                                        <th style="min-width: 12%">Daily Update</th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_machine_cost('Process')"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="machine_body"></tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            <div class="text-right"><?php echo $this->lang->line('manufacturing_machine_totals')?><!--Machine Totals--></div>
                                        </td>
                                        <td>
                                            <div id="tot_mc_hourRate" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_mc_usageHours" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_mc_totalHours" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td>
                                            <div id="tot_mc_totalValue" style="text-align: right"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <?php if ($type == 2) { ?>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <strong>Item:</strong>&nbsp; <?php echo $jobMasterRec['itemDescription'] ?></div>
                            <div class="col-md-3"><strong>UoM:</strong>&nbsp; <?php echo $jobMasterRec['UnitDes'] ?>
                            </div>
                            <div class="col-md-3"><strong>Qty:</strong>&nbsp; <?php echo $jobMasterRec['qty'] ?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="table-responsive">
                        <div class="col-md-12" style="font-size:15px;color: #4a8cdb">
                            <div class="col-md-6"><strong>Total Cost:</strong>&nbsp; <span id="totalCost"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></span>
                            </div>
                            <div class="col-md-6"><strong>Cost per unit:</strong>&nbsp; <span
                                        id="costperunit"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></span><input type="hidden" name="unitPrice" id="unitPrice"></div>
                        </div>
                    </div>
                </div>
                <br>
            <?php }
            if ($jobCardRec["status"] != 1) { ?>
                <div class="row <?php echo $disablebutton ?>">
                    <div class="col-md-12">
                        <div class="pull-right">
                            <button class="btn btn-primary" type="button" onclick="save_workprocess_jobcard_process_based(0)"><?php echo $this->lang->line('common_save')?><!--Save-->
                            </button>
                            <button class="btn btn-primary" type="button" onclick="save_workprocess_jobcard_process_based(1)"><?php echo $this->lang->line('common_save_and_complete')?><!--Save & Complete-->
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <br>
        </form>
    </div>
    <div class="tab-pane  <?php echo $disablebutton ?>" id="crew_<?php echo $documentID ?>">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('common_crew')?><!--Crew--></h2>
                </header>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" role="form" id="frm_crew_<?php echo $documentID ?>">
                            <input type="hidden" name="workProcessID"
                                   value="<?php echo $workProcessID ?>">
                            <input type="hidden" name="workFlowID"
                                   value="<?php echo $workFlowID ?>">
                            <div class="table-responsive">
                                <table id="mfq_crew_<?php echo $documentID ?>" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_name')?><!--Name--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_designation')?><!--Designation--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_start_time')?><!--Start Time--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_end_time')?><!--End Time--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_hours_spent')?><!--Hours Spent--></th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_crew('<?php echo $documentID ?>')"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="crew_body_<?php echo $documentID ?>">
                                    <tr>
                                        <td>
                                            <input type="text" onkeyup="clearitemAutoID(event,this)"
                                                   class="form-control c_search"
                                                   name="search[]"
                                                   placeholder="<?php echo $this->lang->line('common_crew')?>" id="c_search_<?php echo $documentID ?>_1">
                                            <input type="hidden" class="form-control crewID" name="crewID[]">
                                            <input type="hidden" class="form-control workProcessCrewID"
                                                   name="workProcessCrewID[]">
                                        </td>
                                        <td><input type="text" name="designation" class="form-control designation"
                                                   readonly>
                                        </td>
                                        <td><input type="text" name="startTime" class="form-control startTime"></td>
                                        <td><input type="text" name="endTime" class="form-control endTime">
                                        </td>
                                        <td><input type="text" name="hoursSpent" class="form-control hoursSpent"></td>
                                        <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <?php if ($jobCardRec["status"] != 1) { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right">
                                            <button class="btn btn-primary-new size-lg" type="button"
                                                    onclick="save_crew('<?php echo $documentID ?>',<?php echo $workFlowID ?>)">
                                                <?php echo $this->lang->line('common_save')?><!--Save-->
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane  <?php echo $disablebutton ?>" id="machine_<?php echo $documentID ?>">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('manufacturing_machine')?><!--Machine--></h2>
                </header>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" role="form" id="frm_machine_<?php echo $documentID ?>">
                            <input type="hidden" name="workProcessID"
                                   value="<?php echo $workProcessID ?>">
                            <input type="hidden" name="workFlowID" id="workFlowID_<?php echo $documentID ?>_1"
                                   value="<?php echo $workFlowID ?>">
                            <div class="table-responsive">
                                <table id="mfq_machine_<?php echo $documentID ?>" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%"><?php echo $this->lang->line('manufacturing_machine')?><!--Machine--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_description')?><!--Description--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_start_time')?><!--Start Time--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_end_time')?><!--End Time--></th>
                                        <th style="min-width: 12%"><?php echo $this->lang->line('common_hours_spent')?><!--Hours Spent--></th>
                                        <th style="min-width: 5%">
                                            <div class=" pull-right">
                                                <button type="button" data-text="Add"
                                                        onclick="add_more_machine('<?php echo $documentID ?>')"
                                                        class="button button-square button-tiny button-royal button-raised">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="machine_body_<?php echo $documentID ?>">
                                    <tr>
                                        <td>
                                            <input type="text" onkeyup="clearitemAutoID(event,this)"
                                                   class="form-control m_search"
                                                   name="search[]"
                                                   placeholder="<?php echo $this->lang->line('manufacturing_machine')?>" id="m_search_<?php echo $documentID ?>_1">
                                            <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]">
                                            <input type="hidden" class="form-control workProcessMachineID"
                                                   name="workProcessMachineID[]">
                                        </td>
                                        <td><input type="text" name="assetDescription" id="assetDescription"
                                                   class="form-control assetDescription" readonly></td>
                                        <td><input type="text" name="startTime" class="form-control startTime"></td>
                                        <td><input type="text" name="endTime" class="form-control endTime">
                                        </td>
                                        <td><input type="text" name="hoursSpent" class="form-control hoursSpent"></td>
                                        <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <?php if ($jobCardRec["status"] != 1) { ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right">
                                            <button class="btn btn-primary-new size-lg" type="button"
                                                    onclick="save_machine('<?php echo $documentID ?>',<?php echo $workFlowID ?>)">
                                                <?php echo $this->lang->line('common_save')?><!--Save-->
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane  <?php echo $disablebutton ?>" id="attachment_<?php echo $documentID ?>">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('common_attachment')?><!--Attachment--></h2>
                </header>
                <div class="row">
                    <?php echo form_open_multipart('', 'id="attachment_uplode_form_' . $documentID . '" class="form-inline"'); ?>
                    <input type="hidden" name="workProcessID" value="<?php echo $workProcessID ?>">
                    <input type="hidden" name="workFlowID"
                           value="<?php echo $workFlowID ?>">
                    <input type="hidden" name="documentID" id="documentID_<?php echo $documentID ?>"
                           value="<?php echo $documentID ?>">
                    <div class="col-sm-12">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control"
                                       name="attachmentDescription" placeholder="Description..." style="width: 240%;">
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
                                    onclick="workflow_document_uplode('<?php echo $documentID; ?>',<?php echo $workProcessID ?>,<?php echo $workFlowID ?>)"><span
                                        class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                            </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12" id="show_all_attachments_<?php echo $documentID; ?>">
                        <header class="infoarea">
                            <div class="search-no-results"><?php echo $this->lang->line('common_no_attachment_found')?><!--NO ATTACHMENT FOUND-->
                            </div>
                        </header>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane  <?php echo $disablebutton ?>" id="pulledDoc_<?php echo $documentID ?>">
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title"><h2>Pulled Documents</h2></header>
                <div class="row">
                    <div class="col-sm-12" id="pulled_docs">No Data Found</div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="review_<?php echo $documentID ?>">
    </div>
</div>

<?php
$data["documentID"] = $documentID;
$this->load->view('system/mfq/mfq_common_js', $data); 
?>
<script>
    var jobCardID = '<?php echo $jobCardID ?>';
    var jobType = '<?php echo $jobMasterRec['type']  ?>';
    var prevJobCardID = '<?php echo $prevJobCardID  ?>';
    var bomID = "";
    var workflowType = <?php echo $type ?>;
    var currency_decimal = 3;
    $(document).ready(function () {
        $('#jobCardStartDate').val($('#startDate').val());
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
          
        });
        <?php 
            $link_job = check_link_job_card($workProcessID, $templateDetailID);
            if (!empty($link_job) && $type == 1) {?>
                save_onchange();
        <?php }?>

        if (jobType == 2) {
            $('#bomID').select2({disabled: true});
        }
        
        //if Job Card is Generated
        if (jobCardID != 0) {
            if (jobType == 1) {
                $('#bomID').val('<?php echo $jobCardRec["bomID"] ?>').change();
            }
            bomID = '<?php echo $jobCardRec["bomID"] ?>';
            if (bomID || jobType == 2) {
                $('#bomID').select2({disabled: true});
            }
            <?php  if($type != "1"){ ?>
            $('#jobNo').val('<?php echo $jobCardRec["jobNo"] ?>');
            $('#bomIDhidded').val('<?php echo $jobCardRec["bomID"] ?>');
            $('#quotationRef').val('<?php echo $jobCardRec["quotationRef"] ?>');
            $('#jobdescription').val('<?php echo $jobCardRec["description"] ?>');
            <?php } ?>
        }
        
        if (workflowType == 2) {
            load_work_process_detail('<?php echo $documentID ?>',<?php echo $workFlowID ?>);
            initializCrewTypeahead(1, '<?php echo $documentID ?>');
            initializMachineTypeahead(1, '<?php echo $documentID ?>');
            initializemachinecostTypeahead(1);
            init_bom_third_party_service_cost(1);
            initializeoverheadTypeahead(1);
            initializelabourtaskTypeahead(1);
            initializematerialTypeahead(1);
            if (jobCardID == 0) {
                var bomMasterID = '<?php echo $jobMasterRec['bomMasterID'] ?>';
                var tab = '<?php echo isset($tab) ? $tab : "" ?>';
                if (workflowType == 2 && tab == 0 && bomMasterID != 0) {
                    $('#bomIDhidded').val(bomMasterID);
                    loadBomDetail(bomMasterID);
                } else {
                    init_jobcard_material_consumption();
                    init_jobcard_labour_task();
                    init_jobcard_overhead_cost();
                    init_jobcard_machine_cost();
                    init_bom_third_party_service_cost();
                }
            } else {
                jobDetail(<?php echo $workProcessID ?>, $("#jobCardID").val());
            }
        } else {
            init_jobcard_material_consumption();
            init_jobcard_labour_task();
            init_jobcard_overhead_cost();
            init_bom_third_party_service_cost();
            init_jobcard_machine_cost();
            calculateOverheadCostTotal();
            calculateMaterialConsumtionTotal();
            calculateLabourTaskTotal();
            calculateMachineCostTotal();
        }

        $('#bomID').change(function (e) {
            loadBomDetail($('#bomID').val());
            // swal({
            //         title: "Are you sure?",
            //         text: "You want to pull record from bill of material!",
            //         type: "warning",
            //         showCancelButton: true,
            //         confirmButtonColor: "#DD6B55",
            //         confirmButtonText: 'Yes, I am sure!',
            //         cancelButtonText: "No, cancel it!"
            //     },
            //     function (isConfirm) {
            //         if (isConfirm) {
            //             loadBomDetail($('#bomID').val());
            //         } else {
            //             $('#bomID').val('').change();
            //         }
            //     });
        });

        $('#link').change(function (e) {
            save_jobcard_link();
        })

        if(bomMasterID == '') {
            fetchBom('<?php echo $workFlowTemplateID;?>', '<?php echo $mfqItemID;?>');
        }
    });

    function init_jobcard_material_consumption() {
        if ($('#material_consumption_body tr').length == 0) {
            $('#material_consumption_body').append('<tr> <td></td> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="Item ID, Item Description..." id="f_search_' + search_id + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]"> <input type="hidden" class="form-control jcMaterialConsumptionID" name="jcMaterialConsumptionID[]"> </td> <td><input type="text" name="partNo" id="partNo[]" class="form-control" readonly> </td> <td><input type="text" name="uom[]" class="form-control uom" readonly value=""></td> <td><input type="text" name="qtyUsed[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();" readonly> </td> <td> <div class="text-tight materialQtyUsage">0 </div> <input type="hidden" name="usageQty[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control usageQty"></td> <td><input type="text" name="unitCost[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();" <?php echo $readUnitPrice; ?>> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></span> <input type="hidden" name="materialCost[]" value="0" class="materialCost"> </td> <td style="width: 100px"> <div class="input-group"> <input type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="0" onkeyup="cal_material_total(this)" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)"> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></span> <input type="hidden" name="materialCharge[]" value="0" class="materialCharge"> </td><td style="vertical-align: middle;text-align: center"><input type="checkbox" class="Daily" onchange="dailyCompute_materialCharge(this, 0)" value="1" name="Daily[]"> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
            setTimeout(function () {
                initializematerialTypeahead(search_id);
            }, 500);
        }
    }

    function save_onchange()
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {linkID: $('#link').val(), templateDetailID:<?php echo $templateDetailID ?>},
            url: "<?php echo site_url('MFQ_Template/link_workflow'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function save_jobcard_link()
    {
        swal({
                title: "Are you sure?",
                text: "You want to link the Job card!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: 'Yes',
                cancelButtonText: "No"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {linkID: $('#link').val(), templateDetailID:<?php echo $templateDetailID ?>},
                        url: "<?php echo site_url('MFQ_Template/link_workflow'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                        },
                        error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });

                } else {
                    $('#link').val('').change();
                }
            }
        );
    }
    
    function init_jobcard_labour_task() {
        if ($('#labour_task_body tr').length == 0) {
            var segment = '<?php
                echo str_replace(array("\n", '<select'), array('', '<select id="lb_\'+search_id5+\'"'), form_dropdown('la_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this),fetch_related_subsegment(this,this.value)" class="form-control segmentID"  required'))
                ?>';
            var subsegment = '<?php
                echo str_replace(array("\n", '<select'), array('', '<select id="lbsub_\'+search_id5+\'"'), form_dropdown('la_subsegmentID[]',array(''=>'Select a Sub Segment'),'', 'class="form-control subsegmentID"  required'))
                ?>';
            var uom = '<?php
                echo str_replace(array("\n", '<select'), array('', '<select id="lbu_\'+search_id5+\'"'), form_dropdown('la_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required'))
                ?>';
            $('#labour_task_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control l_search" name="search[]" placeholder="Labour" id="l_search_' + search_id5 + '"> <input type="hidden" class="form-control labourTask" name="labourTask[]"> <input type="hidden" class="form-control jcLabourTaskID" name="jcLabourTaskID[]"> </td> <td>' + uom + '</td> <td>' + segment + '</td><td>'+subsegment+'</td> <td><input type="text" name="la_hourlyRate[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_hourRate" onfocus="this.select();"> </td> <td> <div class="text-right lb_usageHours">0 <input type="hidden" name="la_usageHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control la_usageHours"></div> </td> <td><input type="text" name="la_totalHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_totalHours totalHours" onfocus="this.select();" readonly> </td>  <td>&nbsp;<span class="lb_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></span> <input type="hidden" name="la_totalValue[]" class="lb_totalValue"> </td><td style="vertical-align: middle;text-align: center"><input type="checkbox" class="la_Daily" onchange="dailyCompute_labourTask(this, 0)" value="1" name="la_Daily[]"></td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
            setTimeout(function () {
                initializelabourtaskTypeahead(search_id5);
                search_id5++;
            }, 500);
        }
    }

    function init_jobcard_overhead_cost() {
        if ($('#over_head_body tr').length == 0) {
            var segment = '<?php
                echo str_replace(array("\n", '<select'), array('', '<select id="oh_\'+search_id2+\'"'), form_dropdown('oh_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                ?>';
            var uom = '<?php
                echo str_replace(array("\n", '<select'), array('', '<select id="ohu_\'+search_id2+\'"'), form_dropdown('oh_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required'))
                ?>';
            $('#over_head_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_' + search_id2 + '"> <input type="hidden" class="form-control overHeadID" name="overHeadID[]"> <input type="hidden" class="form-control jcOverHeadID" name="jcOverHeadID[]"> </td> <td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="oh_hourlyRate[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();"> </td> <td> <div class="text-right oh_usageHours">0 <input type="hidden" name="oh_usageHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control ohh_usageHours"></div> </td> <td><input type="text" name="oh_totalHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours totalHours" onfocus="this.select();" readonly> </td>  <td>&nbsp;<span class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></span> <input type="hidden" name="oh_totalValue[]" class="oh_totalValue"> </td><td style="vertical-align: middle;text-align: center"><input type="checkbox" class="oh_Daily" onchange="dailyComputeOverhead(this, 0)" value="1" name="oh_Daily[]"></td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
            setTimeout(function () {
                initializeoverheadTypeahead(search_id2);
                search_id2++;
            }, 500);
        }
    }
    function init_bom_third_party_service_cost() {
        var uom = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="tpsu_1"'), form_dropdown('tps_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required'))
            ?>';
        $('#third_party_service').html('');
        $('#third_party_service').append('<tr> ' +
            '<td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control tps_search" name="tps_search[]" placeholder="Third Party Service" id="tps_search_1">' +
            ' <input type="hidden" class="form-control tpsID" name="tpsID[]"> <input type="hidden" class="form-control jcthirdpartyservice" name="jcthirdpartyservice[]"> </td>' +
            ' <td>' + uom + '</td>' +
            ' <td><input type="text" name="tps_hourlyRate[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_bom_third_party_service_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_hourRate" onfocus="this.select();"> </td>'+
            ' <td> <div class="text-right tps_usageHours1">0 <input type="hidden" name="tps_usageHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control tps_usageHours"></div> </td>'+
            ' <td><input type="text" name="tps_totalHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_bom_third_party_service_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_totalHours totalHours" onfocus="this.select();" readonly> </td>' +
            ' <td>&nbsp;<span class="tps_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></span> <input type="hidden" name="tps_totalValue[]" class="tps_totalValue"> </td>' +
            ' <td style="vertical-align: middle;text-align: center"><input type="checkbox" class="tps_Daily" onchange="dailyCompute_thirdParty(this, 0)" value="1" name="tps_Daily[]"></td> ' +
            ' <td class="remove-td" style="vertical-align: middle;text-align: center"></td> ' +
            '</tr>');
        setTimeout(function () {
            initializethirdpartyserviceTypeahead(1);
        }, 500);
    }

    function init_jobcard_machine_cost() {
        if ($('#machine_body tr').length == 0) {
            var segment = '<?php
                echo str_replace(array("\n", '<select'), array('', '<select id="mc_\'+search_id3+\'"'), form_dropdown('mc_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                ?>';
            var uom = '<?php
                echo str_replace(array("\n", '<select'), array('', '<select id="mcu_\'+search_id3+\'"'), form_dropdown('mc_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required'))
                ?>';
            $('#machine_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control mc_search" name="search[]" placeholder="Machine" id="mc_search_' + search_id3 + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]"> <input type="hidden" class="form-control jcMachineID" name="jcMachineID[]"> </td> <td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="mc_hourlyRate[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_hourRate" onfocus="this.select();"> </td> <td> <div class="text-right mc_usageHours">0 <input type="hidden" name="mc_usageHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control mcc_usageHours"></div> </td> <td><input type="text" name="mc_totalHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_totalHours totalHours" onfocus="this.select();" readonly> </td>  <td>&nbsp;<span class="mc_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;"><?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?></span> <input type="hidden" name="mc_totalValue[]" class="mc_totalValue"> </td><td style="vertical-align: middle;text-align: center"><input type="checkbox" class="mc_Daily" onchange="dailyCompute_machine(this, 0)" value="1" name="mc_Daily[]"></td><td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
            setTimeout(function () {
                initializemachinecostTypeahead(search_id3);
                search_id3++;
            }, 500);
        }
    }

    function initializematerialTypeahead(id) {
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_material/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.mfqItemID').val(suggestion.mfqItemID);
                    $('#f_search_' + id).closest('tr').find('.partNo').val(suggestion.partNo);
                    $('#f_search_' + id).closest('tr').find('.uom').val(suggestion.uom);
                }, 200);
                fetchUnitCost(suggestion.mfqItemID, this);
                //fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function initializeoverheadTypeahead(id) {
        $('#o_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_overhead/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#o_search_' + id).closest('tr').find('.overHeadID').val(suggestion.overHeadID);
                    $('#o_search_' + id).closest('tr').find('.uomID').val(suggestion.uom);
                    $('#o_search_' + id).closest('tr').find('.segmentID').val(suggestion.segment);
                    $('#o_search_' + id).closest('tr').find('.oh_hourRate').val(suggestion.rate);
                    $('#o_search_' + id).closest('tr').find('.oh_totalHours').val(suggestion.hours);
                    $('#o_search_' + id).closest('tr').find('.oh_hourRate').keyup();
                }, 200);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function initializemachinecostTypeahead(id) {
        $('#mc_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_machine/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#mc_search_' + id).closest('tr').find('.mfq_faID').val(suggestion.mfq_faID);
                    $('#mc_search_' + id).closest('tr').find('.uomID').val(suggestion.uom);
                    $('#mc_search_' + id).closest('tr').find('.segmentID').val(suggestion.segment);
                    $('#mc_search_' + id).closest('tr').find('.mc_totalHours').val(suggestion.hours);
                    $('#mc_search_' + id).closest('tr').find('.mc_hourRate').val(suggestion.rate);
                    $('#mc_search_' + id).closest('tr').find('.mcc_usageHours').val(0);
                    $('#mc_search_' + id).closest('tr').find('.mc_hourRate').keyup();
                }, 200);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function initializelabourtaskTypeahead(id) {
        $('#l_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_labourtask/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#l_search_' + id).closest('tr').find('.labourTask').val(suggestion.overHeadID);
                    $('#l_search_' + id).closest('tr').find('.uomID').val(suggestion.uom);
                    $('#l_search_' + id).closest('tr').find('.segmentID').val(suggestion.segment);
                    $('#l_search_' + id).closest('tr').find('.lb_hourRate').val(suggestion.rate);
                    $('#l_search_' + id).closest('tr').find('.lb_totalHours').val(suggestion.hours);
                    $('#l_search_' + id).closest('tr').find('.la_usageHours').val(0);
                    $('#l_search_' + id).closest('tr').find('.lb_hourRate').keyup();
                }, 200);
                fetch_related_subsegment(this,suggestion.segment);
            }
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function loadBomDetail(bomID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id: bomID, qty: $('#qty2').val(), jobID:<?php echo $workProcessID ?>},
            url: "<?php echo site_url('MFQ_Job_Card/load_data_from_bom'); ?>",
            beforeSend: function () {
                $('#bomIDhidded').val(bomID);
                $('#bomID').select2({disabled: true});
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    if ($('#material_consumption_body tr').length < 2) {
                        $('#material_consumption_body').html("");
                    }
                    if (!$.isEmptyObject(data["materialConsumption"])) {
                        var i = 0;
                        $.each(data["materialConsumption"], function (k, v) {
                            // if(v.dayComputation == 1) {
                                dailyCompute = '<input type="checkbox" class="Daily" onchange="dailyCompute_materialCharge(this, ' + v.jcMaterialConsumptionID + ', ' + v.bomMaterialConsumptionID + ')" value="1" name="Daily[]" checked>';
                            // } else {
                            //     dailyCompute = '<input type="checkbox" class="Daily" onchange="dailyCompute_materialCharge(this, ' + v.jcMaterialConsumptionID + ', ' + v.bomMaterialConsumptionID + ')" value="1" name="Daily[]">';
                            // }
                            var jobStatus = "";
                            if (v.itemType == 3 && v.linkedJobID != null) {
                                if (v.confirmedYN == 1) {
                                    jobStatus = '<div style="color:green;font-size: 18px"><i class="fa fa-arrow-right" id="htmlTooltip' + i + '" data-html="true" rel="tooltip" title="JOB NO: ' + v.documentCode + ' <br> Status : <span style=\'color:green\'>Completed</span>" aria-hidden="true"></i></div>';
                                } else {
                                    jobStatus = '<div style="color:darkorange;font-size: 18px"><i class="fa fa-arrow-right fa-2" id="htmlTooltip' + i + '" data-html="true" rel="tooltip" title="JOB NO: ' + v.documentCode + ' <br> Status : <span style=\'color:darkorange\'>Inprogress</span>" aria-hidden="true"></i></div>';
                                }
                            }
                            $('#material_consumption_body').append('<tr id="rowMC_' + v.bomMaterialConsumptionID + '"><td valign="middle" class="jobStatus">' + jobStatus + '</td> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="Item ID, Item Description..." id="f_search_' + search_id + '" value="' + v.itemDescription + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control jcMaterialConsumptionID" name="jcMaterialConsumptionID[]" value=""> </td> <td><input type="text" name="partNo[]" class="form-control" readonly value="' + v.partNo + '"> </td> <td><input type="text" name="uom[]" class="form-control uom" readonly value="' + v.UnitDes + '"></td> <td><input type="text" name="qtyUsed[]" value="' + v.qtyUsed + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();" readonly> </td> <td> <div class="text-right materialQtyUsage">0</div> <input type="hidden" name="usageQty[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control usageQty"></td> <td><input type="text" name="unitCost[]" value="' + v.unitCost + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();" <?php echo $readUnitPrice; ?>> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(0, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCost[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="materialCost"> </td> <td style="width: 100px"> <div class="input-group"> <input type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="' + v.markUp + '" onkeyup="cal_material_total(this)" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)"> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(0, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCharge[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="materialCharge"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            initializematerialTypeahead(search_id);
                            $("#htmlTooltip" + i).tooltip();
                            search_id++;
                            i++;
                        });
                    } else {
                        init_jobcard_material_consumption();
                    }
                    calculateMaterialConsumtionTotal();
                    if ($('#labour_task_body tr').length < 2) {
                        $('#labour_task_body').html("");
                    }
                    if (!$.isEmptyObject(data["labourTask"])) {
                        $.each(data["labourTask"], function (k, v) {
                            // if(v.dayComputation == 1) {
                                dailyCompute = '<input type="checkbox" class="la_Daily" onchange="dailyCompute_labourTask(this, ' + v.jcLabourTaskID + ', ' + v.bomLabourTaskID + ')" value="1" name="la_Daily[]" checked>';
                            // } else {
                            //     dailyCompute = '<input type="checkbox" class="la_Daily" onchange="dailyCompute_labourTask(this, ' + v.jcLabourTaskID + ', ' + v.bomLabourTaskID + ')" value="1" name="la_Daily[]">';
                            // }
                            var segment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="lb_\'+search_id5+\'"'), form_dropdown('la_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this),fetch_related_subsegment(this,this.value,\'+v.subsegmentID+\')" class="form-control segmentID"  required'))
                                ?>';
                            var subsegment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="lbsub_\'+search_id5+\'"'), form_dropdown('la_subsegmentID[]',array(''=>'Select a Sub Segment'),'', 'class="form-control subsegmentID"  required'))
                             ?>';
                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="lbu_\'+search_id5+\'"'), form_dropdown('la_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                            $('#labour_task_body').append('<tr id="rowLB_' + v.jcLabourTaskID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control l_search" name="search[]" placeholder="Labour" id="l_search' + search_id5 + '" value="' + v.description + '"> <input type="hidden" class="form-control labourTask" name="labourTask[]" value="' + v.labourTask + '"> <input type="hidden" class="form-control jcLabourTaskID" name="jcLabourTaskID[]" value=""> </td> <td>' + uom + '</td> <td>' + segment + '</td> <td>'+subsegment+'</td> <td><input type="text" name="la_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_hourRate" onfocus="this.select();"> </td> <td> <div class="text-right lb_usageHours">0</div> <input type="hidden" name="la_usageHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control la_usageHours"></td> <td><input type="text" name="la_totalHours[]" value="' + v.totalHours + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_totalHours totalHours" onfocus="this.select();" value="' + v.totalHours + '" readonly> </td>  <td>&nbsp;<span class="lb_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(0, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="la_totalValue[]" class="lb_totalValue" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            $('#lb_' + search_id5).val(v.segmentID).change();
                            $('#lbu_' + search_id5).val(v.uomID);
                            initializelabourtaskTypeahead(search_id5);
                            search_id5++;
                        });
                    } else {
                        init_jobcard_labour_task();
                    }
                    calculateLabourTaskTotal();
                    if ($('#over_head_body tr').length < 2) {
                        $('#over_head_body').html("");
                    }
                    if (!$.isEmptyObject(data["overheadCost"])) {
                        $.each(data["overheadCost"], function (k, v) {
                            // if(v.dayComputation == 1) {
                                dailyCompute = '<input type="checkbox" class="oh_Daily" onchange="dailyComputeOverhead(this, ' + v.jcOverHeadID + ', ' + v.bomOverheadID + ')" value="1" name="oh_Daily[]" checked>';
                            // } else {
                            //     dailyCompute = '<input type="checkbox" class="oh_Daily" onchange="dailyComputeOverhead(this, ' + v.jcOverHeadID + ', ' + v.bomOverheadID + ')" value="1" name="oh_Daily[]">';
                            // }
                            var segment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="oh_\'+search_id2+\'"'), form_dropdown('oh_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                                ?>';
                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ohu_\'+search_id2+\'"'), form_dropdown('oh_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                            $('#over_head_body').append('<tr id="rowOH_' + v.jcOverHeadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_' + search_id2 + '" value="' + v.description + '"> <input type="hidden" class="form-control overHeadID" name="overHeadID[]" value="' + v.overheadID + '"> <input type="hidden" class="form-control jcOverHeadID" name="jcOverHeadID[]" value=""> </td> <td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="oh_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();"> </td>  <td> <div class="text-right oh_usageHours">0</div> <input type="hidden" name="oh_usageHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control ohh_usageHours"></td> <td><input type="text" name="oh_totalHours[]" value="' + v.totalHours + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(0, 2) + '</span> <input type="hidden" name="oh_totalValue[]" class="oh_totalValue" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            $('#oh_' + search_id2).val(v.segmentID);
                            $('#ohu_' + search_id2).val(v.uomID);
                            initializeoverheadTypeahead(search_id2);
                            search_id2++;
                        });
                    } else {
                        init_jobcard_overhead_cost();
                    }
                    calculateOverheadCostTotal();
                    calculateTotalCost();

                    if ($('#third_party_service tr').length < 2) {
                        $('#third_party_service').html("");
                    }
                    if (!$.isEmptyObject(data["thirdparty"])) {
                        $.each(data["thirdparty"], function (k, v) {
                            // if(v.dayComputation == 1) {
                                dailyCompute = '<input type="checkbox" class="tps_Daily" onchange="dailyCompute_thirdParty(this, ' + v.jcOverHeadID + ', ' + v.bomOverheadID + ')" value="1" name="tps_Daily[]" checked>';
                            // } else {
                            //     dailyCompute = '<input type="checkbox" class="tps_Daily" onchange="dailyCompute_thirdParty(this, ' + v.jcOverHeadID + ', ' + v.bomOverheadID + ')" value="1" name="tps_Daily[]">';
                            // }
                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="tpsu_\'+search_id_thirdparty+\'"'), form_dropdown('tps_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                            $('#third_party_service').append('<tr id="rowTPS_' + v.jcOverHeadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control tps_search" name="tps_search[]" placeholder="Third Party Service" id="tps_search_' + search_id_thirdparty + '" value="' + v.description + '"> <input type="hidden" class="form-control tpsID" name="tpsID[]" value="' + v.overheadID + '"> <input type="hidden" class="form-control jcthirdpartyservice" name="jcthirdpartyservice[]" value=""> </td> <td>' + uom + '</td>  <td><input type="text" name="tps_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_hourRate" onfocus="this.select();"> </td><td> <div class="text-right tps_usageHours1">0</div> <input type="hidden" name="tps_usageHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>"  class="form-control tps_usageHours"></td> <td><input type="text" name="tps_totalHours[]" value="' + v.totalHours + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="tps_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="tps_totalValue[]" class="tps_totalValue" value="' + v.totalValue + '"><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td></td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_thirdparty_cost(' + v.jcOverHeadID + ',' + v.jobCardID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            //$('#oh_' + search_id2).val(v.segmentID);
                            $('#tpsu_' + search_id_thirdparty).val(v.uomID);
                            initializethirdpartyserviceTypeahead(search_id_thirdparty);
                            search_id_thirdparty++;
                        });
                        calculateThirdPartyServiceCostTotal();
                        //calculateOverheadCostTotal();
                    } else {
                        init_bom_third_party_service_cost();
                    }


                    if ($('#machine_body tr').length < 2) {
                        $('#machine_body').html("");
                    }
                    if (!$.isEmptyObject(data["machineCost"])) {
                        $.each(data["machineCost"], function (k, v) {
                            // if(v.dayComputation == 1) {
                                dailyCompute = '<input type="checkbox" class="mc_Daily" onchange="dailyCompute_machine(this, ' + v.jcMachineID + ', ' + v.bomMachineID + ')" value="1" name="mc_Daily[]" checked>';
                            // } else {
                            //     dailyCompute = '<input type="checkbox" class="mc_Daily" onchange="dailyCompute_machine(this, ' + v.jcMachineID + ', ' + v.bomMachineID + ')" value="1" name="mc_Daily[]">';
                            // }
                            var segment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="mc_\'+search_id3+\'"'), form_dropdown('mc_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                                ?>';
                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="mcu_\'+search_id3+\'"'), form_dropdown('mc_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                            $('#machine_body').append('<tr id="rowMC_' + v.jcMachineID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control mc_search" name="search[]" placeholder="Machine" id="mc_search_' + search_id3 + '" value="' + v.assetDescription + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]" value="' + v.mfq_faID + '"> <input type="hidden" class="form-control jcMachineID" name="jcMachineID[]" value=""> </td> <td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="mc_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_hourRate" onfocus="this.select();"> </td> <td> <div class="text-right mc_usageHours">0</div> <input type="hidden" name="mc_usageHours[]" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" class="form-control mcc_usageHours"></td> <td><input type="text" name="mc_totalHours[]" value="' + v.totalHours + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="mc_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(0, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="mc_totalValue[]" class="mc_totalValue" value="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_machine_cost(' + v.jcMachineID + ',' + v.jobCardID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            $('#mc_' + search_id3).val(v.segment);
                            $('#mcu_' + search_id3).val(v.uomID);
                            initializemachinecostTypeahead(search_id3);
                            search_id3++;
                            i++;
                        });
                    } else {
                        init_jobcard_machine_cost();
                    }
                    calculateMachineCostTotal();
                    calculateTotalCost();
                } else {
                    $('.btn-primary').prop('disabled', false);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        })
    }

    function jobDetail(workProcessID, jobCardID) {
        var status = '<?php echo $jobCardRec["status"]?>';
        if(status == '') {
            status = 0;
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {workProcessID: workProcessID, jobCardID: jobCardID},
            url: "<?php echo site_url('MFQ_Job_Card/fetch_job_detail'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                if(data['jobCard']) {
                    $('#jobCardStartDate').val(data['jobCard']['JCstartDate']);
                } else {
                    $('#jobCardStartDate').val($('#startDate').val());
                }
                
                $('#material_consumption_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data["material"])) {
                    $.each(data["material"], function (k, v) {
                        // if(v.dayComputation == 1) {
                            dailyCompute = '<input id="mc_daily_'+v.jcMaterialConsumptionID+'" type="checkbox" class="Daily" onchange="dailyCompute_materialCharge(this, ' + v.jcMaterialConsumptionID + ')" value="1" name="Daily[]" checked>';
                        // } else {
                        //     dailyCompute = '<input id="mc_daily_'+v.jcMaterialConsumptionID+'" type="checkbox" class="Daily" onchange="dailyCompute_materialCharge(this, ' + v.jcMaterialConsumptionID + ')" value="0" name="Daily[]">';
                        // }
                        var jobStatus = "";
                        if (v.itemType == 3 && v.linkedJobID != null) {
                            if (v.confirmedYN == 1) {
                                jobStatus = '<div style="color:green;font-size: 18px"><i class="fa fa-arrow-right" id="htmlTooltip' + i + '" data-html="true" rel="tooltip" title="JOB NO: ' + v.documentCode + ' <br> Status : <span style=\'color:green\'>Completed</span>" aria-hidden="true"></i></div>';
                            } else {
                                jobStatus = '<div style="color:darkorange;font-size: 18px"><i class="fa fa-arrow-right" id="htmlTooltip' + i + '" data-html="true" rel="tooltip" title="JOB NO: ' + v.documentCode + ' <br> Status : <span style=\'color:darkorange\'>Inprogress</span>" aria-hidden="true"></i></div>';
                            }
                        }
                        if(v.isSubitemExist == 1){
                            $('#material_consumption_body').append('<tr id="rowMC_' + v.jcMaterialConsumptionID + '"> <td>' + jobStatus + '<a style="" class="sub_itemView" onclick="load_itemMasterSub_config_modal(' + v.jcMaterialConsumptionID + ',\'JOB\', ' + v.warehouseAutoID + ','+ status +',' + v.subItemapplicableon + ')"><i class="fa fa-list"></i></a></td> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="Item ID, Item Description..." id="f_search_' + search_id + '" value="' + v.itemDescription + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control jcMaterialConsumptionID" name="jcMaterialConsumptionID[]" value="' + v.jcMaterialConsumptionID + '"> </td> <td><input type="text" name="partNo[]" class="form-control" readonly value="' + v.partNo + '"> </td> <td><input type="text" name="uom[]" class="form-control uom" readonly value="' + v.uom + '"></td> <td><input type="text" name="qtyUsed[]" value="' + v.qtyUsed + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();" readonly> </td> <td> <div id="mc_materialQtyUsage_'+v.jcMaterialConsumptionID+'" class="text-right materialQtyUsage" onclick="load_usage_history(' + workProcessID + ',' + v.jobCardID + ',' + v.jcMaterialConsumptionID + ',1)">' + v.usageQty + '</div>  <input type="hidden" name="usageQty[]" value="' + v.usageQty + '" class="form-control usageQty"></td> <td><input type="text" name="unitCost[]" value="' + v.unitCost + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();" <?php echo $readUnitPrice; ?> id="unitCost_material_'+ v.jcMaterialConsumptionID +'"> </td> <td>&nbsp;<span class="materialCostTxt pull-right" id="materialCostTxt_'+ v.jcMaterialConsumptionID+'" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCost[]" value="' + v.materialCost + '" class="materialCost"> </td> <td style="width: 100px"> <div class="input-group"> <input id="markupPrc_material_'+ v.jcMaterialConsumptionID +'" type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="' + v.markUp + '" onkeyup="cal_material_total(this)" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)"> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" id="materialChargeTxt_material_'+ v.jcMaterialConsumptionID +'" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCharge, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCharge[]" value="' + v.materialCharge + '" class="materialCharge"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_materialConsumption(' + v.jcMaterialConsumptionID + ',' + v.jobCardID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        }else{
                            $('#material_consumption_body').append('<tr id="rowMC_' + v.jcMaterialConsumptionID + '"> <td>' + jobStatus + '</td> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="Item ID, Item Description..." id="f_search_' + search_id + '" value="' + v.itemDescription + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control jcMaterialConsumptionID" name="jcMaterialConsumptionID[]" value="' + v.jcMaterialConsumptionID + '"> </td> <td><input type="text" name="partNo[]" class="form-control" readonly value="' + v.partNo + '"> </td> <td><input type="text" name="uom[]" class="form-control uom" readonly value="' + v.uom + '"></td> <td><input type="text" name="qtyUsed[]" value="' + v.qtyUsed + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();" readonly> </td> <td> <div id="mc_materialQtyUsage_'+v.jcMaterialConsumptionID+'" class="text-right materialQtyUsage" onclick="load_usage_history(' + workProcessID + ',' + v.jobCardID + ',' + v.jcMaterialConsumptionID + ',1)">' + v.usageQty + '</div>  <input type="hidden" name="usageQty[]" value="' + v.usageQty + '" class="form-control usageQty"></td> <td><input type="text" name="unitCost[]" value="' + v.unitCost + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();" <?php echo $readUnitPrice; ?> id="unitCost_material_'+ v.jcMaterialConsumptionID +'"> </td> <td>&nbsp;<span class="materialCostTxt pull-right" id="materialCostTxt_'+ v.jcMaterialConsumptionID+'" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCost[]" value="' + v.materialCost + '" class="materialCost"> </td> <td style="width: 100px"> <div class="input-group"> <input id="markupPrc_material_'+ v.jcMaterialConsumptionID +'" type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="' + v.markUp + '" onkeyup="cal_material_total(this)" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)"> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" id="materialChargeTxt_material_'+ v.jcMaterialConsumptionID +'" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCharge, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCharge[]" value="' + v.materialCharge + '" class="materialCharge"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_materialConsumption(' + v.jcMaterialConsumptionID + ',' + v.jobCardID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        }
                        dailyCompute_materialCharge('', v.jcMaterialConsumptionID);
                        initializematerialTypeahead(search_id);
                        $("#htmlTooltip" + i).tooltip();
                        search_id++;
                        i++;
                    });
                } else {
                    init_jobcard_material_consumption();
                }
                calculateMaterialConsumtionTotal();

                $('#labour_task_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data["labourTask"])) {
                    $.each(data["labourTask"], function (k, v) {
                        // if(v.dayComputation == 1) {
                            dailyCompute = '<input id="la_daily_'+v.jcLabourTaskID+'" type="checkbox" class="la_Daily" onchange="dailyCompute_labourTask(this, ' + v.jcLabourTaskID + ')" value="1" name="la_Daily[]" checked>';
                        // } else {
                        //     dailyCompute = '<input id="la_daily_'+v.jcLabourTaskID+'" type="checkbox" class="la_Daily" onchange="dailyCompute_labourTask(this, ' + v.jcLabourTaskID + ')" value="1" name="la_Daily[]">';
                        // }
                        var segment = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="lb_\'+search_id5+\'"'), form_dropdown('la_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this),fetch_related_subsegment(this,this.value,\'+v.subsegmentID+\')" class="form-control segmentID"  required'))
                            ?>';
                        var subsegment = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="lbsub_\'+search_id5+\'"'), form_dropdown('la_subsegmentID[]',array(''=>'Select a Sub Segment'),'', 'class="form-control subsegmentID"  required'))
                            ?>';
                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="lbu_\'+search_id5+\'"'), form_dropdown('la_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                        $('#labour_task_body').append('<tr id="rowLB_' + v.jcLabourTaskID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control l_search" name="search[]" placeholder="Labour" id="l_search' + search_id5 + '" value="' + v.description + '"> <input type="hidden" class="form-control labourTask" name="labourTask[]" value="' + v.labourTask + '"> <input type="hidden" class="form-control jcLabourTaskID" name="jcLabourTaskID[]" value="' + v.jcLabourTaskID + '"> </td> <td>' + uom + '</td> <td>' + segment + '</td><td>'+subsegment+'</td> <td><input type="text" name="la_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_hourRate" onfocus="this.select();" id="lb_hourRate_'+ v.jcLabourTaskID +'"> </td> <td> <div id="la_usageHours_'+ v.jcLabourTaskID +'" class="text-right lb_usageHours" onclick="load_usage_history(' + workProcessID + ',' + v.jobCardID + ',' + v.jcLabourTaskID + ',2)">' + v.usageHours + '</div>  <input type="hidden" name="la_usageHours[]" value="' + v.usageHours + '" class="form-control la_usageHours"></td>  <td><input type="text" name="la_totalHours[]" value="' + v.totalHours + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_totalHours totalHours" onfocus="this.select();" value="' + v.totalHours + '" readonly> </td>  <td>&nbsp;<span class="lb_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;" id="lb_totalValueTxt_'+ v.jcLabourTaskID +'">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="la_totalValue[]" class="lb_totalValue" value="' + v.totalValue + '"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_labour_task(' + v.jcLabourTaskID + ',' + v.jobCardID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        $('#lb_' + search_id5).val(v.segmentID).change();
                        $('#lbu_' + search_id5).val(v.uomID);
                        dailyCompute_labourTask('', v.jcLabourTaskID);
                        initializelabourtaskTypeahead(search_id5);
                        search_id5++;
                        i++;
                    });
                } else {
                    init_jobcard_labour_task();
                }
                calculateLabourTaskTotal();

                $('#over_head_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data["overhead"])) {
                    $.each(data["overhead"], function (k, v) {
                        // if(v.dayComputation == 1) {
                            dailyCompute = '<input id="oh_daily_'+v.jcOverHeadID+'" type="checkbox" class="oh_Daily" onchange="dailyComputeOverhead(this, '+ v.jcOverHeadID +')" value="1" name="oh_Daily[]" checked>';
                        // } else {
                        //     dailyCompute = '<input id="oh_daily_'+v.jcOverHeadID+'" type="checkbox" class="oh_Daily" onchange="dailyComputeOverhead(this, '+ v.jcOverHeadID +')" value="1" name="oh_Daily[]">';
                        // }
                        var segment = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="oh_\'+search_id2+\'"'), form_dropdown('oh_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                            ?>';
                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ohu_\'+search_id2+\'"'), form_dropdown('oh_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                        $('#over_head_body').append('<tr id="rowOH_' + v.jcOverHeadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_' + search_id2 + '" value="' + v.description + '"> <input type="hidden" class="form-control overHeadID" name="overHeadID[]" value="' + v.overHeadID + '"> <input type="hidden" class="form-control jcOverHeadID" name="jcOverHeadID[]" value="' + v.jcOverHeadID + '"> </td> <td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="oh_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();" id="oh_hourRate_'+ v.jcOverHeadID +'"> </td> <td> <div id="oh_usageHours_'+ v.jcOverHeadID +'" class="text-right oh_usageHours" onclick="load_usage_history(' + workProcessID + ',' + v.jobCardID + ',' + v.jcOverHeadID + ',3)">' + v.usageHours + '</div>  <input type="hidden" name="oh_usageHours[]" value="' + v.usageHours + '" class="form-control ohh_usageHours"></td> <td><input type="text" name="oh_totalHours[]" value="' + v.totalHours + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;" id="oh_totalValueTxt_'+ v.jcOverHeadID +'">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="oh_totalValue[]" class="oh_totalValue" value="' + v.totalValue + '"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_overhead_cost(' + v.jcOverHeadID + ',' + v.jobCardID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        $('#oh_' + search_id2).val(v.segmentID);
                        $('#ohu_' + search_id2).val(v.uomID);
                        dailyComputeOverhead('', v.jcOverHeadID);
                        initializeoverheadTypeahead(search_id2);
                        search_id2++;
                        i++;
                    });
                } else {
                    init_jobcard_overhead_cost();
                }
                calculateOverheadCostTotal();

                $('#third_party_service').html('');
                if (!$.isEmptyObject(data["thirdparty"])) {
                    $.each(data["thirdparty"], function (k, v) {
                        // if(v.dayComputation == 1) {
                            dailyCompute = '<input id="tps_daily_'+v.jcOverHeadID+'" type="checkbox" class="tps_Daily" onchange="dailyCompute_thirdParty(this, ' + v.jcOverHeadID + ')" value="1" name="tps_Daily[]" checked>';
                        // } else {
                        //     dailyCompute = '<input id="tps_daily_'+v.jcOverHeadID+'" type="checkbox" class="tps_Daily" onchange="dailyCompute_thirdParty(this, ' + v.jcOverHeadID + ')" value="1" name="tps_Daily[]">';
                        // }
                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="tpsu_\'+search_id_thirdparty+\'"'), form_dropdown('tps_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                        $('#third_party_service').append('<tr id="rowTPS_' + v.jcOverHeadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control tps_search" name="tps_search[]" placeholder="Third Party Service" id="tps_search_' + search_id_thirdparty + '" value="' + v.description + '"> <input type="hidden" class="form-control tpsID" name="tpsID[]" value="' + v.overHeadID + '"> <input type="hidden" class="form-control jcthirdpartyservice" name="jcthirdpartyservice[]" value="' + v.jcOverHeadID + '"> </td> <td>' + uom + '</td>  <td><input type="text" name="tps_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_hourRate" onfocus="this.select();" id="tps_hourRate_'+ v.jcOverHeadID +'"> </td><td> <div id="tps_usageHours1_'+ v.jcOverHeadID +'" class="text-right tps_usageHours1">' + v.usageHours + '<input type="hidden" name="tps_usageHours[]" value="' + v.usageHours + '"  class="form-control tps_usageHours"></div> </td> <td><input type="text" name="tps_totalHours[]" value="' + v.totalHours + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="tps_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;" id="tps_totalValueTxt_'+ v.jcOverHeadID +'">' + commaSeparateNumber(v.totalValue, 2) + '</span> <input type="hidden" name="tps_totalValue[]" class="tps_totalValue" value="' + v.totalValue + '"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_thirdparty_cost(' + v.jcOverHeadID + ',' + v.jobCardID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        //$('#oh_' + search_id2).val(v.segmentID);
                        $('#tpsu_' + search_id_thirdparty).val(v.uomID);
                        dailyCompute_thirdParty('', v.jcOverHeadID);
                        initializethirdpartyserviceTypeahead(search_id_thirdparty);
                        search_id_thirdparty++;
                    });
                    calculateThirdPartyServiceCostTotal();
                    //calculateOverheadCostTotal();
                } else {
                    init_bom_third_party_service_cost();
                }

                $('#machine_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data["machine"])) {
                    $.each(data["machine"], function (k, v) {
                        // if(v.dayComputation == 1) {
                            dailyCompute = '<input id="mc_daily_'+v.jcMachineID+'" type="checkbox" class="mc_Daily" onchange="dailyCompute_machine(this, ' + v.jcMachineID + ')" value="1" name="mc_Daily[]" checked>';
                        // } else {
                        //     dailyCompute = '<input id="mc_daily_'+v.jcMachineID+'" type="checkbox" class="mc_Daily" onchange="dailyCompute_machine(this, ' + v.jcMachineID + ')" value="1" name="mc_Daily[]">';
                        // }
                        var segment = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="mc_\'+search_id3+\'"'), form_dropdown('mc_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                            ?>';
                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="mcu_\'+search_id3+\'"'), form_dropdown('mc_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                        $('#machine_body').append('<tr id="rowMC_' + v.jcMachineID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control mc_search" name="search[]" placeholder="Machine" id="mc_search_' + search_id3 + '" value="' + v.assetDescription + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]" value="' + v.mfq_faID + '"> <input type="hidden" class="form-control jcMachineID" name="jcMachineID[]" value="' + v.jcMachineID + '"> </td> <td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="mc_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_hourRate" onfocus="this.select();"  id="mc_hourRate_'+ v.jcMachineID +'"> </td> <td> <div id="mc_usageHours_'+ v.jcMachineID +'" class="text-right mc_usageHours" onclick="load_usage_history(' + workProcessID + ',' + v.jobCardID + ',' + v.jcMachineID + ',4)">' + v.usageHours + '</div>  <input type="hidden" name="mc_usageHours[]" value="' + v.usageHours + '" class="form-control mcc_usageHours"></td> <td><input type="text" name="mc_totalHours[]" value="' + v.totalHours + '" placeholder="<?php echo number_format(0, $this->common_data["company_data"]["company_default_decimal"]); ?>" onkeyup="cal_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="mc_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;" id="mc_totalValueTxt_'+ v.jcMachineID +'">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="mc_totalValue[]" class="mc_totalValue" value="' + v.totalValue + '"> </td><td style="vertical-align: middle;text-align: center">' + dailyCompute + '</td><td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_machine_cost(' + v.jcMachineID + ',' + v.jobCardID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        $('#mc_' + search_id3).val(v.segment2);
                        $('#mcu_' + search_id3).val(v.uomID);
                        dailyCompute_machine('', v.jcMachineID)
                        initializemachinecostTypeahead(search_id3);
                        search_id3++;
                        i++;
                    });
                } else {
                    init_jobcard_machine_cost();
                }
                calculateMachineCostTotal();
                calculateTotalCost();
                //stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function save_workprocess_jobcard_process_based(type) {
        var data = $("#frm_jobcard_<?php echo $workFlowID ?>").serializeArray();
        data.push({'name': 'status', 'value': type});
        $('#material_consumption_body tr').each(function () {
            data.push({'name': 'Daily_material[]', 'value': ($('td', this).eq(10).find('.Daily').prop('checked'))? '1' : '0'});
        });
        $('#labour_task_body tr').each(function () {
            data.push({'name': 'Daily_labour[]', 'value': ($('td', this).eq(8).find('.la_Daily').prop('checked'))? '1' : '0'});
        });
        $('#over_head_body tr').each(function () {
            data.push({'name': 'Daily_overhead[]', 'value': ($('td', this).eq(7).find('.oh_Daily').prop('checked'))? '1' : '0'});
        });
        $('#third_party_service tr').each(function () {
            data.push({'name': 'Daily_thirdParty[]', 'value': ($('td', this).eq(6).find('.tps_Daily').prop('checked'))? '1' : '0'});
        });
        $('#machine_body tr').each(function () {
            data.push({'name': 'Daily_machine[]', 'value': ($('td', this).eq(7).find('.mc_Daily').prop('checked'))? '1' : '0'});
        });
        if(type == 1) {
            swal({
                title: "Are you sure?",
                text: "Cannot Update Details After Completing!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm",
                closeOnConfirm: true
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('MFQ_Job_Card/save_workprocess_jobcard_process_based'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $('#jobCardID').val(data[2]);
                            jobDetail(<?php echo $workProcessID ?>, $("#jobCardID").val());
                            if (type == 1) {
                                <?php if(isset($tab)){ ?>
                                $('#Tab_<?php echo $tab + 1; ?>').removeClass("disabledbutton");
                                $('#Tab_<?php echo $tab + 1; ?>').trigger('click');
                                $('#complete_process_<?php echo $templateDetailID; ?>').html('<i class="fa fa-check" style="color: green"></i>');
                                <?php } ?>
                            }
                            $('#type').prop('disabled', true);
                            $('#workFlowTemplateID').prop('disabled', true);
                            $('#finishGoods').prop('disabled', true);
                            $(document).scrollTop(0);
                        } else {
                            $('.btn-primary').prop('disabled', false);
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
        } else {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_Job_Card/save_workprocess_jobcard_process_based'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#jobCardID').val(data[2]);
                        jobDetail(<?php echo $workProcessID ?>, $("#jobCardID").val());
                        if (type == 1) {
                            <?php if(isset($tab)){ ?>
                            $('#Tab_<?php echo $tab + 1; ?>').removeClass("disabledbutton");
                            $('#Tab_<?php echo $tab + 1; ?>').trigger('click');
                            $('#complete_process_<?php echo $templateDetailID; ?>').html('<i class="fa fa-check" style="color: green"></i>');
                            <?php } ?>
                        }
                        $('#type').prop('disabled', true);
                        $('#workFlowTemplateID').prop('disabled', true);
                        $('#finishGoods').prop('disabled', true);
                        $(document).scrollTop(0);
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function delete_materialConsumption(id, masterID) {
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
                    url: "<?php echo site_url('MFQ_Job_Card/delete_materialConsumption'); ?>",
                    type: 'post',
                    data: {jcMaterialConsumptionID: id, masterID: masterID},
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
                                init_jobcard_material_consumption();
                            }
                            $("#rowMC_" + id).remove();
                            calculateMaterialConsumtionTotal();
                            calculateTotalCost();
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

    function delete_labour_task(id, masterID) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_Card/delete_labour_task'); ?>",
                    type: 'post',
                    data: {jcLabourTaskID: id, masterID: masterID},
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
                                init_jobcard_labour_task();
                            }
                            $("#rowLB_" + id).remove();
                            calculateLabourTaskTotal();
                            calculateTotalCost();
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

    function delete_overhead_cost(id, masterID) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_Card/delete_overhead_cost'); ?>",
                    type: 'post',
                    data: {jcOverHeadID: id, masterID: masterID},
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
                                init_jobcard_overhead_cost();

                            }


                            $("#rowOH_" + id).remove();
                            calculateOverheadCostTotal();
                            calculateTotalCost();
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

    function delete_machine_cost(id, masterID) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_Card/delete_machine_cost'); ?>",
                    type: 'post',
                    data: {jcMachineID: id, masterID: masterID},
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
                            $("#rowMC_" + id).remove();
                            if (data.code == 1) {
                                init_jobcard_machine_cost();
                            }
                            calculateMachineCostTotal();
                            calculateTotalCost();
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

    function calculateMaterialConsumtionTotal() {
        var tot_qtyUsed = 0;
        var tot_qtyUsage = 0;
        var tot_unitCost = 0;
        var tot_materialCost = 0;
        var tot_materialCharge = 0;
        $('#material_consumption_body tr').each(function () {
            var tot_qtyUsed_value = parseFloat($('td', this).eq(4).find('input').val());
            if (!isNaN(tot_qtyUsed_value)) {
                tot_qtyUsed += tot_qtyUsed_value;
            }

            var tot_qtyUsage_value = parseFloat($('td', this).eq(5).find('.materialQtyUsage').text());
            if (!isNaN(tot_qtyUsage_value)) {
                tot_qtyUsage += tot_qtyUsage_value;
            }

            var tot_unitCost_value = parseFloat($('td', this).eq(6).find('input').val());
            if (!isNaN(tot_unitCost_value)) {
                tot_unitCost += tot_unitCost_value;
            }

            var tot_materialCost_value = parseFloat($('td', this).eq(7).find('input').val());
            if (!isNaN(tot_materialCost_value)) {
                tot_materialCost += tot_materialCost_value;
            }

            var tot_materialCharge_value = parseFloat($('td', this).eq(9).find('input').val());
            if (!isNaN(tot_materialCharge_value)) {
                tot_materialCharge += tot_materialCharge_value;
            }
        });

        $("#tot_qtyUsed").text(tot_qtyUsed);
        $("#tot_qtyUsage").text(tot_qtyUsage);
        $("#tot_unitCost").text(commaSeparateNumber(tot_unitCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_materialCost").text(commaSeparateNumber(tot_materialCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_materialCharge").text(commaSeparateNumber(tot_materialCharge, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }

    function calculateLabourTaskTotal() {
        var tot_hourRate = 0;
        var tot_totalHours = 0;
        var tot_usageHours = 0;
        var tot_totalValue = 0;
        $('#labour_task_body tr').each(function () {
            var tot_hourRate_value = parseFloat($('td', this).eq(4).find('input').val());
            if (!isNaN(tot_hourRate_value)) {
                tot_hourRate += tot_hourRate_value;
            }

            var tot_usageHours_value = getNumberAndValidate($('td', this).eq(5).find('.lb_usageHours').text());
            tot_usageHours += tot_usageHours_value;

            var tot_totalHours_value = parseFloat($('td', this).eq(6).find('input').val());
            if (!isNaN(tot_totalHours_value)) {
                tot_totalHours += tot_totalHours_value;
            }

            var tot_totalValue_value = parseFloat($('td', this).eq(7).find('input').val());
            if (!isNaN(tot_totalValue_value)) {
                tot_totalValue += tot_totalValue_value;
            }
        });

        $("#tot_lb_hourRate").text(commaSeparateNumber(tot_hourRate, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_lb_usageHours").text(commaSeparateNumber(tot_usageHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_lb_totalHours").text(commaSeparateNumber(tot_totalHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_lb_totalValue").text(commaSeparateNumber(tot_totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }

    function calculateOverheadCostTotal() {
        var tot_hourRate = 0;
        var tot_totalHours = 0;
        var tot_totalValue = 0;
        var tot_usageHours = 0;
        $('#over_head_body tr').each(function () {
            var tot_hourRate_value = getNumberAndValidate($('td', this).eq(3).find('input').val());
            tot_hourRate += tot_hourRate_value;

            var tot_usageHours_value = getNumberAndValidate($('td', this).eq(4).find('.oh_usageHours').text());
            tot_usageHours += tot_usageHours_value;

            var tot_totalHours_value = getNumberAndValidate($('td', this).eq(5).find('input').val());
            tot_totalHours += tot_totalHours_value;

            var tot_totalValue_value = getNumberAndValidate($('td', this).eq(6).find('input').val());
            tot_totalValue += tot_totalValue_value;

        });

        $("#tot_oh_hourRate").text(commaSeparateNumber(tot_hourRate, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_oh_usageHours").text(commaSeparateNumber(tot_usageHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_oh_totalHours").text(commaSeparateNumber(tot_totalHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_oh_totalValue").text(commaSeparateNumber(tot_totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }

    function calculateMachineCostTotal() {
        var tot_hourRate = 0;
        var tot_totalHours = 0;
        var tot_totalValue = 0;
        var tot_usageHours = 0;
        $('#machine_body tr').each(function () {
            var tot_hourRate_value = getNumberAndValidate($('td', this).eq(3).find('input').val());
            tot_hourRate += tot_hourRate_value;

            var tot_usageHours_value = getNumberAndValidate($('td', this).eq(4).find('.mc_usageHours').text());
            tot_usageHours += tot_usageHours_value;

            var tot_totalHours_value = getNumberAndValidate($('td', this).eq(5).find('input').val());
            tot_totalHours += tot_totalHours_value;

            var tot_totalValue_value = getNumberAndValidate($('td', this).eq(6).find('input').val());
            tot_totalValue += tot_totalValue_value;

        });

        $("#tot_mc_hourRate").text(commaSeparateNumber(tot_hourRate, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_mc_usageHours").text(commaSeparateNumber(tot_usageHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_mc_totalHours").text(commaSeparateNumber(tot_totalHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_mc_totalValue").text(commaSeparateNumber(tot_totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }

    function calculateTotalCost() {
        var totalMateialConsumption = parseFloat($('#tot_materialCharge').text().replace(/,/g, ''));
        var totalLabourTask = parseFloat($('#tot_lb_totalValue').text().replace(/,/g, ''));
        var totalOverhead = parseFloat($('#tot_oh_totalValue').text().replace(/,/g, ''));
        var totalMachine = parseFloat($('#tot_mc_totalValue').text().replace(/,/g, ''));
        var totalthirdparty = parseFloat($('#tot_tps_totalValue').text().replace(/,/g, ''));
        var totalCost = (totalMateialConsumption + totalLabourTask + totalOverhead + totalMachine + totalthirdparty);
        $("#totalCost").text(commaSeparateNumber((totalMateialConsumption + totalLabourTask + totalOverhead + totalMachine + totalthirdparty), <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        if ($('#qty2').val() > 0) {
            $("#costperunit").text(commaSeparateNumber((totalCost / $('#qty2').val()), <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
            $("#unitPrice").val(totalCost / $('#qty2').val());
        } else {
            $("#costperunit").text(0);
            $("#unitPrice").val(0);
        }
    }

    function load_jobcard_print() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                workProcessID:  <?php echo $workProcessID ?>,
                jobCardID: $("#jobCardID").val(),
                workFlowID:<?php echo $workFlowID ?>,
                templateDetailID:<?php echo $templateDetailID ?>,
                linkworkFlow:<?php echo $linkWorkFlowID ?>,
                templateMasterID:<?php echo $templateMasterID ?>,
                type:<?php echo $type ?>,
                html: true
            },
            url: "<?php echo site_url('MFQ_Job_Card/fetch_jobcard_print'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#review_<?php echo $documentID ?>").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function getNumberAndValidate(thisVal, dPlace=2) {
        thisVal = $.trim(thisVal);
        thisVal = removeCommaSeparateNumber(thisVal);
        thisVal = thisVal.toFixed(dPlace);
        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
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
        if ((caratPos > dotPos) && (dotPos > -(currency_decimal - 1)) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function fetchUnitCost(mfqItemID, element) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_Job_Card/fetch_material_by_id"); ?>',
            dataType: 'json',
            data: {mfqItemID: mfqItemID},
            async: false,
            success: function (data) {
                if (data) {
                    var companyLocalWacAmount = parseInt(data.companyLocalWacAmount);
                    companyLocalWacAmount = companyLocalWacAmount.toFixed(<?php echo $this->common_data["company_data"]["company_default_decimal"]; ?>);
                    $(element).closest('tr').find('.unitCost').val(companyLocalWacAmount).keyup();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e', xhr.responseText);
            }
        });
    }

    function initializethirdpartyserviceTypeahead(id) {
        $('#tps_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_BillOfMaterial/fetch_third_party_service/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#tps_search_' + id).closest('tr').find('.tpsID').val(suggestion.tpsID);
                    $('#tps_search_' + id).closest('tr').find('.uomID').val(suggestion.uom);
                    //$('#tsp_search_' + id).closest('tr').find('.segmentID').val(suggestion.segment);
                    $('#tps_search_' + id).closest('tr').find('.tps_hourRate').val(suggestion.rate);
                    $('#tps_search_' + id).closest('tr').find('.tps_totalHours').val(suggestion.hours);
                    $('#tps_search_' + id).closest('tr').find('.tps_hourRate').keyup();
                }, 200);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function calculateThirdPartyServiceCostTotal() {
        var tot_hourRate = 0;
        var tot_totalHours = 0;
        var tot_totalValue = 0;
        var tot_usageHours = 0;
        $('#third_party_service tr').each(function () {
            var tot_hourRate_value = parseFloat($('td', this).eq(2).find('input').val());
            if (!isNaN(tot_hourRate_value)) {
                tot_hourRate += tot_hourRate_value;
            }

            var tot_usageHours_value = getNumberAndValidate($('td', this).eq(3).find('.tps_usageHours1').text());
            tot_usageHours += tot_usageHours_value;

            var tot_totalHours_value = parseFloat($('td', this).eq(4).find('input').val());
            if (!isNaN(tot_totalHours_value)) {
                tot_totalHours += tot_totalHours_value;
            }

            var tot_totalValue_value = parseFloat($('td', this).eq(5).find('input').val());
            if (!isNaN(tot_totalValue_value)) {
                tot_totalValue += tot_totalValue_value;
            }
        });
        $("#tot_tps_hourRate").text(commaSeparateNumber(tot_hourRate, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_tps_usageHours").text(commaSeparateNumber(tot_usageHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_tps_totalHours").text(commaSeparateNumber(tot_totalHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_tps_totalValue").text(commaSeparateNumber(tot_totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }

    function delete_thirdparty_cost(id, masterID) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job_Card/delete_thirdparty_cost'); ?>",
                    type: 'post',
                    data: {jcOverHeadID: id, masterID: masterID},
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
                                init_bom_third_party_service_cost();

                            }
                            $("#rowTPS_" + id).remove();
                            calculateThirdPartyServiceCostTotal();
                            calculateTotalCost();
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

    function fetch_related_subsegment(element,value,selectedvalue) {
        if(value)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'segmentID': value,'type':1},
                url: "<?php echo site_url('MFQ_SegmentMaster/fetch_mfq_subsegment'); ?>",
                success: function (data) {
                    $(element).closest('tr').find('.subsegmentID').empty();

                    var mySelect = $(element).parent().closest('tr').find('.subsegmentID');

                    mySelect.append($('<option></option>').val('').html('Select a Sub Segment'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['mfqSegmentID']).html(text['segmentcode']));
                        });

                    }
                    if (selectedvalue) {
                        $(element).closest('tr').find('.subsegmentID').val(selectedvalue).change();
                    }
                },  error: function (XMLHttpRequest, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                    // swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

    }

    function load_pulled_document()
    {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: { workProcessID:  <?php echo $workProcessID ?>},
            url: '<?php echo site_url('MFQ_Job/get_job_pulled_documents'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#pulled_docs").html(data);
                $('.drilldown-title').html("Job - " + documentCode);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function dailyComputeOverhead(element, jcOverHeadID, bomOverheadID = '') {
        if('<?php echo $disablebutton; ?>' != 'disabledbutton') {
            if(element == '') {
                if ($('#oh_daily_'+jcOverHeadID).is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            } else {
                if ($(element).closest('tr').find('.oh_Daily').is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            }
            var startDate = $('#jobCardStartDate').val();
            var qty = $('#qty').val();
            var includeCurrentDate = $('#includeCurrentDate').val();
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {includeCurrentDate: includeCurrentDate, qty: qty, jcOverHeadID: jcOverHeadID, 'value': value, startDate : startDate, bomOverheadID : bomOverheadID},
                url: '<?php echo site_url('MFQ_Job_Card/calculateDailyComputation_overhead'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if(element == '') {
                        $('#oh_usageHours_'+jcOverHeadID).text(data);

                        var unitCost = $('#oh_hourRate_'+jcOverHeadID).val();                   
                        $('#oh_totalValueTxt_'+jcOverHeadID).text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                    } else {
                        $(element).closest('tr').find('.oh_usageHours').text(data);

                        var unitCost = parseFloat($(element).closest('tr').find('.oh_hourRate').val());
                        $(element).closest('tr').find('.oh_totalValueTxt').text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                    }
                    calculateOverheadCostTotal();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function dailyCompute_thirdParty(element, jcOverHeadID, bomOverheadID = '') {
        if('<?php echo $disablebutton; ?>' != 'disabledbutton') {
            if(element == '') {
                if ($('#tps_daily_'+jcOverHeadID).is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            } else {
                if ($(element).closest('tr').find('.tps_Daily').is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            }
            var startDate = $('#jobCardStartDate').val();
            var qty = $('#qty').val();
            var includeCurrentDate = $('#includeCurrentDate').val();
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: { includeCurrentDate: includeCurrentDate, qty: qty, jcOverHeadID: jcOverHeadID, 'value': value, startDate : startDate, bomOverheadID : bomOverheadID},
                url: '<?php echo site_url('MFQ_Job_Card/calculateDailyComputation_overhead'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if(element == '') {
                        $('#tps_usageHours1_'+jcOverHeadID).text(data);

                        var unitCost = $('#tps_hourRate_'+jcOverHeadID).val();                   
                        $('#tps_totalValueTxt_'+jcOverHeadID).text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                    } else {
                        $(element).closest('tr').find('.tps_usageHours1').text(data);

                        var unitCost = parseFloat($(element).closest('tr').find('.tps_hourRate').val());
                        $(element).closest('tr').find('.tps_totalValueTxt').text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                    }
                    calculateThirdPartyServiceCostTotal();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function dailyCompute_machine(element, jcMachineID, bomMachineID = '') {
        if('<?php echo $disablebutton; ?>' != 'disabledbutton') {
            if(element == '') {
                if ($('#mc_daily_'+jcMachineID).is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            } else {
                if ($(element).closest('tr').find('.mc_Daily').is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            }
            var startDate = $('#jobCardStartDate').val();
            var qty = $('#qty').val();
            var includeCurrentDate = $('#includeCurrentDate').val();
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: { includeCurrentDate: includeCurrentDate, qty: qty, jcMachineID: jcMachineID, 'value': value, startDate : startDate, bomMachineID : bomMachineID},
                url: '<?php echo site_url('MFQ_Job_Card/calculateDailyComputation_machine'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if(element == '') {
                        var unitCost = $('#mc_hourRate_'+jcMachineID).val();                   
                        $('#mc_usageHours_'+jcMachineID).text(data);

                        $('#mc_totalValueTxt_'+jcMachineID).text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                    } else {
                        $(element).closest('tr').find('.mc_usageHours').text(data);

                        var unitCost = parseFloat($(element).closest('tr').find('.mc_hourRate').val());
                        $(element).closest('tr').find('.mc_totalValueTxt').text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                    }
                    calculateMachineCostTotal();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function dailyCompute_labourTask(element, jcLabourTaskID, bomLabourTaskID = '') {
        if('<?php echo $disablebutton; ?>' != 'disabledbutton') {
            if(element == '') {
                if ($('#la_daily_'+jcLabourTaskID).is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            } else {
                if ($(element).closest('tr').find('.la_Daily').is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            }
            var startDate = $('#jobCardStartDate').val();
            var qty = $('#qty').val();
            var includeCurrentDate = $('#includeCurrentDate').val();
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {'includeCurrentDate': includeCurrentDate, 'qty': qty, 'jcLabourTaskID': jcLabourTaskID, 'value': value, 'startDate' : startDate, 'bomLabourTaskID' : bomLabourTaskID},
                url: '<?php echo site_url('MFQ_Job_Card/calculateDailyComputation_labourTask'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if(element == '') {
                        var unitCost = $('#lb_hourRate_'+jcLabourTaskID).val();                   
                        $('#la_usageHours_'+jcLabourTaskID).text(data);

                        $('#lb_totalValueTxt_'+jcLabourTaskID).text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                    } else {
                        $(element).closest('tr').find('.lb_usageHours').text(data);

                        var unitCost = parseFloat($(element).closest('tr').find('.lb_hourRate').val());
                        $(element).closest('tr').find('.lb_totalValueTxt').text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                    }
                    calculateLabourTaskTotal();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function dailyCompute_materialCharge(element, jcMaterialConsumptionID, bomMaterialConsumptionID = '')
    {
        if('<?php echo $disablebutton; ?>' != 'disabledbutton') {
            if(element == '') {
                if ($('#mc_daily_'+jcMaterialConsumptionID).is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            } else {
                if ($(element).closest('tr').find('.Daily').is(":checked")) {
                    value = 1;
                } else {
                    value = 0;
                }
            }        
            var startDate = $('#jobCardStartDate').val();
            var qty = $('#qty').val();
            var includeCurrentDate = $('#includeCurrentDate').val();
            $.ajax({
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {'includeCurrentDate': includeCurrentDate, 'qty': qty, 'jcMaterialConsumptionID': jcMaterialConsumptionID, 'value': value, 'startDate' : startDate, 'bomMaterialConsumptionID' : bomMaterialConsumptionID},
                url: '<?php echo site_url('MFQ_Job_Card/calculateDailyComputation_materialCharge'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if(element == '') {
                        var unitCost = $('#unitCost_material_'+jcMaterialConsumptionID).val();
                        var markup = $('#markupPrc_material_'+jcMaterialConsumptionID).val();
                        $('#mc_materialQtyUsage_'+jcMaterialConsumptionID).text(data);
                        $('#materialCostTxt_'+jcMaterialConsumptionID).text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                        $('#materialChargeTxt_material_'+jcMaterialConsumptionID).closest('tr').find('.materialChargeTxt').text(commaSeparateNumber((((parseFloat(data) * unitCost) * markup) / 100) + (parseFloat(data) * unitCost),2));
                    } else {
                        $(element).closest('tr').find('.materialQtyUsage').text(data);
                        var unitCost = parseFloat($(element).closest('tr').find('.unitCost').val());
                        var markup = parseFloat($(element).closest('tr').find('.markupPrc').val());
                        $(element).closest('tr').find('.materialCostTxt').text(commaSeparateNumber(parseFloat(data) * unitCost,2));
                        $(element).closest('tr').find('.materialChargeTxt').text(commaSeparateNumber((((parseFloat(data) * unitCost) * markup) / 100) + (parseFloat(data) * unitCost),2));
                    }
                    calculateMaterialConsumtionTotal();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function fetchBom(workFlowTemplateID, mfqItemID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: { workFlowTemplateID: workFlowTemplateID, mfqItemID: mfqItemID},
            url: '<?php echo site_url('MFQ_Job_Card/fetch_bom_process_based'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data) {
                    $('#bomID').val(data).change();
                    $('#bomID').select2({disabled: true});
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
</script>