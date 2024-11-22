<?php
$date_format_policy = date_format_policy();
$from = convert_date_format($this->common_data['company_data']['FYPeriodDateFrom']);
$currency_arr = all_currency_new_drop();
$current_date = current_format_date();
$umo_arr = array('' => 'Select UOM');
$umo_arr2 = all_umo_new_drop();
$category = get_mfq_category();
$flowserveLanguagePolicy = (getPolicyValues('LNG', 'All')) ? getPolicyValues('LNG', 'All') : 0;
$segment = fetch_mfq_segment(true);

$page_id = isset($page_id) && $page_id ? $page_id : 0;
$master_page_url = isset($master_page_url) && $master_page_url ? $master_page_url : 0;

$manufacturing_Flow = getPolicyValues('MANFL', 'All');

?>
<?php
if ($policy_id == 'BOM') {

    if($flowserveLanguagePolicy=='FlowServe'){
        echo head_page('Budget', false);
    }else{
        echo head_page($_POST["page_name"], false);
    }
    
}
?>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/typehead.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-12">
        <div class="tab-content">
            <div class="tab-pane active" id="display">
                <div class="row">
                    <div class="col-md-12 animated zoomIn">

                        <form id="from_add_edit_BoM_master" class="frm_billOfMaterial" method="post"
                              enctype="multipart/form-data">
                            <?php
                            if ($policy_id == 'EST') {
                                ?>
                                <input type="hidden" id="estimateDetailID" name="estimateDetailID"
                                       value="<?php echo $data_arr['estimateDetailID'] ?? null ?>">
                                <?php
                            }
                            ?>

                            <input type="hidden" id="bomMasterID" name="bomMasterID" value="<?php echo $page_id ?>">

                            <header class="head-title">
                            <?php if($flowserveLanguagePolicy=='FlowServe'){ ?>
                                <h2>Budget Information </h2>
                            <?php }else{ ?>
                                <h2><?php echo $this->lang->line('manufacturing_bom_information'); ?><!--BOM Information--> </h2>
                            <?php }?>
                                
                            </header>

                            <div class="row">
                                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                                    <div class="row hide" style="margin-top: 10px;" id="bomCodeDiv">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_bom_code'); ?><!--Industry Type--> </label>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <!-- //description -->
                                                <input type="text" name="bomCode" class="form-control" id="bomCode" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title"><?php echo $this->lang->line('common_type'); ?><!--Type--> </label>
                                        </div>
                                        <?php  ?>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <?php
                                                if($flowserveLanguagePolicy == 'FlowServe' || $flowserveLanguagePolicy == 'SOP'){
                                                    echo form_dropdown('bomType', array('1'=>'Product'), $data_arr['bomType'] , 'id="bomType" class="form-control select2" required');
                                                } else {
                                                    echo form_dropdown('bomType', array('1'=>'Product', '2'=>'Process'), $data_arr['bomType'] ?? null , 'id="bomType" class="form-control select2" required');
                                                }
                                                ?>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title" id="product_process_label"><?php echo $this->lang->line('manufacturing_product'); ?><!--Product--> </label>
                                        </div>

                                        <div class="form-group col-sm-6"  id="product_process">
                                            <div class="input-req" title="Required Field" >
                                                <div id="product_dropdown">
                                                    <?php
                                                        echo form_dropdown('product', get_finishedgoods_drop(), $data_arr['mfqItemID'] ?? null, 'id="product" class="form-control select2" required');
                                                    ?>
                                                </div>
                                                <div id="process_dropdown" class="hide">
                                                <?php
                                                    echo form_dropdown('process', get_jobcard_workflow_template(), $data_arr['mfqProcessID'] ?? null, 'id="process" class="form-control select2" required');
                                                ?>
                                                </div>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row " style="margin-top: 10px;">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_description'); ?><!--Industry Type--> </label>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <!-- //description -->
                                                <input type="text" name="bomDescription" class="form-control" id="bomDescription">
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_date'); ?><!--Date--></label>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <div class="input-req" title="Required Field">
                                                <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                <div class='input-group date filterDate' id="">
                                                    <input type='text' class="form-control" name="documentDate"
                                                           id="documentDate" value="<?php echo $current_date; ?>"
                                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                                </div>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row hide" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_industry_type'); ?><!--Industry Type--> </label>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <?php echo form_dropdown('industryTypeID', get_industryType_drop(), '', 'id="industryTypeID" class="form-control" required') ?>

                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_unit_of_measure'); ?><!--Unit of Measure--></label>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <input type="text" name="UOM" class="form-control" id="itemUoM"
                                                       disabled>
                                                <input type="hidden" name="uomID" id="uomID">
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_quantity'); ?><!--Qty--></label>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <div class="input-req" title="Required Field">
                                                <input type="text" name="Qty" id="Qty" class="form-control"
                                                       placeholder="Qty" value="1" required>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4 md-offset-2">
                                            <label class="title"><?php echo $this->lang->line('manufacturing_currency'); ?><!--Currency--></label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'BOM\')" required disabled'); ?>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-2">
                                            <label class="title">&nbsp; </label>
                                        </div>
                                        <div class="form-group col-sm-7">
                                            <div class="file-upload-container">
                                                <div class="input-req">
                                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail"
                                                             style="width: 200px; height: 150px;">
                                                            <img src="<?php echo base_url('images/no-logo.png') ?>"
                                                                 alt="No Image" id="img_showcase">
                                                            <input type="file" name="productImage" id=productImage"
                                                                   style="display: none;"/>
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail"
                                                             style="max-width: 200px; max-height: 150px;"></div>
                                                        <div>
                                                            <span class="btn btn-default btn-file"><span
                                                                        class="fileinput-new"><?php echo $this->lang->line('manufacturing_select_image'); ?><!--Select Image--></span><span
                                                                        class="fileinput-exists"><?php echo $this->lang->line('manufacturing_change'); ?><!--Change--></span><input
                                                                        type="file" name="..."></span>
                                                            <a href="#" class="btn btn-default fileinput-exists"
                                                               data-dismiss="fileinput"><?php echo $this->lang->line('manufacturing_remove'); ?><!--Remove--></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2><?php echo $this->lang->line('manufacturing_add_bom_material_consumption_head'); ?><!--Material Consumption--></h2>
                                    </header>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="mfq_material_consumption" class="table table-condensed">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 25%"><?php echo $this->lang->line('manufacturing_material_consumption'); ?><!--Material Consumption--></th>
                                                        <th style="width: 8%"><?php echo $this->lang->line('manufacturing_part_no'); ?><!--Part No--></th>
                                                        <th style="width: 5%"><?php echo $this->lang->line('manufacturing_unit_of_measure_short'); ?><!--UoM--></th>
                                                        <th style="width: 8%"><?php echo $this->lang->line('manufacturing_quantity_required'); ?><!--Qty Required--></th>
                                                        <th style="width: 8%"><?php echo $this->lang->line('manufacturing_cost_type'); ?><!--Cost Type--></th>
                                             
                                                        <th style="width: 10%"><?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost--></th>
                                                        <th style="width: 12%"><?php echo $this->lang->line('manufacturing_material_cost'); ?><!--Material Cost--></th>
                                                        <?php if($flowserveLanguagePolicy != 'FlowServe') { ?>
                                                            <th style="width: 10%"><?php echo $this->lang->line('manufacturing_standard_loss'); ?><!--Standard Loss--></th>
                                                        <?php } ?>
                                                        <th style="width: 12%"><?php echo $this->lang->line('manufacturing_material_change'); ?><!--Material Charge--></th>
                                                        <th style="width: 5%">
                                                            <div class=" pull-right">
                                                                <span class="button-wrap-box">
                                                                    <button type="button" data-text="Add"
                                                                            onclick="add_more_material('BOM')"
                                                                            class="button button-square button-tiny button-royal button-raised">
                                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                                    </button>
                                                                </span>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="material_consumption_body">

                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="3">
                                                            <div class="text-right"><?php echo $this->lang->line('manufacturing_material_totals'); ?><!--Material Totals--></div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_qtyUsed" style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td class="uom_area">

                                                        </td>
                                                        <td>
                                                            <div id="tot_unitCost" style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_materialCost"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <?php if($flowserveLanguagePolicy != 'FlowServe') { ?>
                                                            <td>
                                                                <div id="tot_markupPrc" style="text-align: right"></div>
                                                            </td>
                                                        <?php } ?>
                                                        <td>
                                                            <div id="tot_materialCharge"
                                                                 style="text-align: right">0.00
                                                            </div>
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
            <?php if($flowserveLanguagePolicy != 'FlowServe') { ?>
                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2><?php echo $this->lang->line('manufacturing_packaging'); ?><!--Material Packaging--></h2>
                                    </header>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="mfq_material_consumption_packaging" class="table table-condensed">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 25%"><?php echo 'Packaging'; ?><!--Material Consumption--></th>
                                                        <th style="width: 8%"><?php echo $this->lang->line('manufacturing_part_no'); ?><!--Part No--></th>
                                                        <th style="width: 5%"><?php echo $this->lang->line('manufacturing_unit_of_measure_short'); ?><!--UoM--></th>
                                                        <th style="width: 8%"><?php echo $this->lang->line('manufacturing_quantity_required'); ?><!--Qty Required--></th>
                                                        <th style="width: 8%"><?php echo $this->lang->line('manufacturing_cost_type'); ?><!--Cost Type--></th>
                                                        <th style="width: 10%"><?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost--></th>
                                                        <th style="width: 12%"><?php echo 'Cost'; ?><!--Material Cost--></th>
                                                        <th style="width: 10%"><?php echo $this->lang->line('manufacturing_standard_loss'); ?><!--Standard Loss--></th>
                                                        <th style="width: 12%"><?php echo 'Charge'; ?><!--Material Charge--></th>
                                                        <th style="width: 5%">
                                                            <div class=" pull-right">
                                                            <span class="button-wrap-box">
                                                                <button type="button" data-text="Add"
                                                                        onclick="add_more_material('BOM_PACKAGING')"
                                                                        class="button button-square button-tiny button-royal button-raised">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="material_consumption_packaging_body">

                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="3">
                                                            <div class="text-right"><?php echo 'Packaging Totals'; ?><!--Material Totals--></div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_qtyUsed" style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>

                                                        </td>
                                                        <td>
                                                            <div id="tot_unitCost" style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_materialCost"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_markupPrc" style="text-align: right"></div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_materialCharge"
                                                                 style="text-align: right">0.00
                                                            </div>
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

        <?php } ?>
                            <br>
                            <!-- Labour Task -->
                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2><?php echo $this->lang->line('manufacturing_labour_tasks'); ?><!--LABOUR TASKS--></h2>
                                    </header>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="mfq_labour_task" class="table table-condensed">
                                                    <thead>
                                                    <tr>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_labour_tasks'); ?><!--Labour Tasks--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_activity_code'); ?><!--Activity Code--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_of_measure_short'); ?><!--UoM--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_department'); ?><!--Department--></th>
                                                        <?php if($flowserveLanguagePolicy != 'FlowServe') { ?>
                                                        <th style="min-width: 12%">Sub Department</th>
                                                        <?php } ?>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate'); ?><!--Unit Rate--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours'); ?><!--Total Hours--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value'); ?><!--Total Value--></th>
                                                        <th style="min-width: 5%">
                                                            <div class=" pull-right">
                                                            <span class="button-wrap-box">
                                                                <button type="button" data-text="Add"
                                                                        onclick="add_more_labour()"
                                                                        class="button button-square button-tiny button-royal button-raised">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="labour_task_body">


                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="<?php echo ($flowserveLanguagePolicy == 'FlowServe') ? '4':'5' ?>">
                                                            <div class="text-right"><?php echo $this->lang->line('manufacturing_labour_totals'); ?><!--labour Tasks--></div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_lb_hourRate"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_lb_totalHours"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_lb_totalValue"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
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
                            <!-- Overhead Cost -->
                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <?php if($manufacturing_Flow == 'Micoda'){?>
                                            <h2>Subcontracting Cost</h2>
                                        <?php }else{?>
                                            <h2><?php echo $this->lang->line('manufacturing_overhead_cost'); ?><!--OVERHEAD COST--></h2>
                                        <?php } ?>
                                    </header>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="mfq_overhead" class="table table-condensed">
                                                    <thead>
                                                    <tr>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_overhead_cost'); ?><!--Overhead Cost--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_activity_code'); ?><!--Activity Code--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_of_measure_short'); ?><!--UoM--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_department'); ?><!--Department--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate'); ?><!--Unit Rate--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours'); ?><!--Total Hours--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value'); ?><!--Total Values--></th>
                                                        <th style="min-width: 5%">
                                                            <div class=" pull-right">
                                                            <span class="button-wrap-box">
                                                                <button type="button" data-text="Add"
                                                                        onclick="add_more_overhead()"
                                                                        class="button button-square button-tiny button-royal button-raised">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="over_head_body">

                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="4">
                                                            <div class="text-right"><?php echo $this->lang->line('manufacturing_overhead_totals'); ?><!--Overhead Totals--></div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_oh_hourRate"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_oh_totalHours"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_oh_totalValue"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
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
                            <!-- Third Party Service -->
                            <div class="row">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                        <h2>THIRD PARTY SERVICE</h2>
                                    </header>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="mfq_thirdPartyService" class="table table-condensed">
                                                    <thead>
                                                    <tr>
                                                        <th style="min-width: 12%"><?php echo 'Third Party Service'; ?><!--Overhead Cost--></th>
                                                        <th style="min-width: 12%">Activity Code</th><!--Activity Code-->
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_of_measure_short'); ?><!--UoM--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate'); ?><!--Unit Rate--></th>
                                                        <?php if($flowserveLanguagePolicy != 'FlowServe') { ?>
                                                            <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours'); ?><!--Total Hours--></th>
                                                        <?php } ?>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value'); ?><!--Total Values--></th>
                                                        <th style="min-width: 5%">
                                                            <div class=" pull-right">
                                                            <span class="button-wrap-box">
                                                                <button type="button" data-text="Add"
                                                                        onclick="add_more_third_party_service()"
                                                                        class="button button-square button-tiny button-royal button-raised">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="third_party_service_body">

                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="<?php echo ($flowserveLanguagePolicy == 'FlowServe') ? '3':'3' ?>">
                                                            <div class="text-right">Third Party Service Total</div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_tps_hourRate"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <?php if($flowserveLanguagePolicy != 'FlowServe') { ?>
                                                            <td>
                                                                <div id="tot_tps_totalHours"
                                                                    style="text-align: right">0.00
                                                                </div>
                                                            </td>
                                                        <?php } ?>
                                                        <td>
                                                            <div id="tot_tps_totalValue"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
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
                            <!-- Machine Cost -->
                            <div class="row <?php echo ($flowserveLanguagePolicy=='FlowServe') ? 'hide' : '' ?>">
                                <div class="col-md-12 animated zoomIn">
                                    <header class="head-title">
                                    <?php if($flowserveLanguagePolicy=='FlowServe'){ ?>
                                        <h2>MACHINE </h2>
                                    <?php }else{ ?>
                                        <h2><?php echo $this->lang->line('manufacturing_machine'); ?><!--MACHINE--></h2>
                                    <?php }?>
                                        
                                    </header>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="mfq_machine_cost" class="table table-condensed">
                                                    <thead>
                                                    <tr>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_machine'); ?><!--Machine--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_activity_code'); ?><!--Activity Code--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_of_measure_short'); ?><!--UoM--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_department'); ?><!--Department--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate'); ?><!--Unit Rate--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours'); ?><!--Total Hours--></th>
                                                        <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value'); ?><!--Total Value--></th>
                                                        <th style="min-width: 5%">
                                                            <div class=" pull-right">
                                                            <span class="button-wrap-box">
                                                                <button type="button" data-text="Add"
                                                                        onclick="add_more_machine_cost()"
                                                                        class="button button-square button-tiny button-royal button-raised">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="machine_body">

                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="4">
                                                            <div class="text-right"><?php echo $this->lang->line('manufacturing_total_value'); ?><!--Machine Totals--></div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_mc_hourRate"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_mc_totalHours"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id="tot_mc_totalValue"
                                                                 style="text-align: right">0.00
                                                            </div>
                                                        </td>
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
                            <div class="row">
                                <div class="table-responsive">
                                    <div class="col-md-12" style="font-size:15px;color: #4a8cdb">
                                        <div class="col-md-6"><strong><?php echo $this->lang->line('manufacturing_total_cost'); ?><!--Total Cost--></strong> <span
                                                    id="totalCost">0.00</span>
                                        </div>
                                        <div class="col-md-6"><strong><?php echo $this->lang->line('manufacturing_cost_per_unit'); ?><!--Cost per Unit--></strong> <span
                                                    id="costperunit">0.00</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 10px;">
                                <div class="col-sm-12 ">
                                    <div class="pull-right">
                                        <button class="btn btn-primary" onclick="saveAllBoM(1)" type="button">
                                            <?php echo $this->lang->line('common_save'); ?><!--Save-->
                                        </button>
                                        <button class="btn btn-primary" onclick="saveAllBoM(2)" type="button">
                                            <?php echo $this->lang->line('common_confirm'); ?><!--Confirm-->
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
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<?php
    $data["documentID"] = 'BOM';
    $this->load->view('system/mfq/mfq_common_js', $data);
?>
<script>
    var currency_decimal = 3;
    var companyPolicy = '<?php echo $flowserveLanguagePolicy ?>';

    $(document).ready(function () {
        $('.select2').select2();
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.filterDate').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });
        Inputmask().mask(document.querySelectorAll("input"));

        $(document).on('click', '.remove-tr', function () {
            $(this).closest('tr').remove();
        });

        $(document).on('click', '.remove-tr-tps', function () {
            $(this).closest('tr').remove();
            calculateThirdPartyServiceCostTotal();
            calculateTotalCost();
        });

        $('.calculate').change(function () {
            var qty = $(this).closest('tr').find('.transactionQty').val();
            var cost = $(this).closest('tr').find('.transactionCostAmount').val();
            $(this).closest('tr').find('.transactionTotalCostAmount').val(qty * cost);
        });
        loadBoMDetail(<?php echo $page_id  ?>);

        <?php
        if ($page_id) {
        ?>
        /*load_bom_material_consumption('<?php echo $page_id  ?>');
        load_bom_labour_task('<?php echo $page_id  ?>');
        load_bom_overhead_cost('<?php echo $page_id  ?>');
        load_bom_machine_cost('<?php echo $page_id  ?>');*/
        <?php
        }else{
        ?>
        init_materialConsupmtionForm();
        init_bom_labour_task();
        init_bom_overhead_cost();
        init_bom_machine_cost();
        init_bom_third_party_service_cost();
        <?php
        }
        ?>
        initializematerialTypeahead(1);
        initializelabourtaskTypeahead(1);
        initializeoverheadTypeahead(1);
        initializemachinecostTypeahead(1);
        initializethirdpartyserviceTypeahead(1);
        $('#bomType').change(function () {
            processOrProductDropdown();
        });
        $('#product').change(function () {
            checkItemInBom();
        });
        <?php if($policy_id == 'BOM') { ?>
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_bill_of_material', '', 'Bill of Material');
        });
        <?php }else{
            if($page_id == 0){

            } else{
            ?>
                $('#product').prop('disabled', true);
                $('#product').change();
            <?php
            }
        }
        ?>
        if(companyPolicy == 'FlowServe'){
            $('#mfq_labour_task td:nth-child(5),mfq_labour_task th:nth-child(5)').hide();
        }
    });

    function initializematerialTypeahead(id,search='MATERIAL') {
   
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_material/'+search+'/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.mfqItemID').val(suggestion.mfqItemID);
                    $('#f_search_' + id).closest('tr').find('.partNo').val(suggestion.partNo);
                    
                    $('#f_search_' + id).closest('tr').find('.f_search').val(suggestion.value);
                
                    $('#f_search_' + id).closest('tr').find('.uom').val(suggestion.uom);
                    $('#f_search_' + id).closest('tr').find('.unitCost').val(suggestion.companyLocalWacAmount);
                    $('#f_search_' + id).closest('tr').find('.qtyUsed').trigger('keyup');

                    fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, this);

                }, 200);
               
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function initializelabourtaskTypeahead(id) {
        $('#l_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_BillOfMaterial/fetch_labourTask/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#l_search_' + id).closest('tr').find('.labourTask').val(suggestion.overHeadID);
                    $('#l_search_' + id).closest('tr').find('.uomID').val(suggestion.uom);
                    $('#l_search_' + id).closest('tr').find('.segmentID').val(suggestion.segment);
                    $('#l_search_' + id).closest('tr').find('.lb_hourRate').val(suggestion.rate);
                    $('#l_search_' + id).closest('tr').find('.lb_totalHours').val(suggestion.hours);
                    $('#l_search_' + id).closest('tr').find('.lb_hourRate').keyup();
                }, 200);
                fetch_related_subsegment(this,suggestion.segment);
            }
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function initializeoverheadTypeahead(id) {
        $('#o_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_BillOfMaterial/fetch_overhead/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#o_search_' + id).closest('tr').find('.overheadID').val(suggestion.overHeadID);
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
            serviceUrl: '<?php echo site_url();?>MFQ_BillOfMaterial/fetch_machine/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#mc_search_' + id).closest('tr').find('.mfq_faID').val(suggestion.mfq_faID);
                    $('#mc_search_' + id).closest('tr').find('.uomID').val(suggestion.uom);
                    $('#mc_search_' + id).closest('tr').find('.segmentID').val(suggestion.segment);
                    $('#mc_search_' + id).closest('tr').find('.mc_totalHours').val(suggestion.hours);
                    $('#mc_search_' + id).closest('tr').find('.mc_hourRate').val(suggestion.rate);
                    $('#mc_search_' + id).closest('tr').find('.mc_hourRate').keyup();
                }, 200);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function saveAllBoM(type) {
        saveBoMMaster(type);
    }

    function load_bom_material_consumption(bomMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {bomMasterID: bomMasterID},
            url: "<?php echo site_url('MFQ_BillOfMaterial/load_bom_material_consumption'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#material_consumption_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var bomMaterialConsumptionID = v.bomMaterialConsumptionID;
                        var costingType = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="ct_\'+bomMaterialConsumptionID+\'"'), form_dropdown('costingType[]', array(1 => 'Average', 2 => 'PO', 3 => 'Manual'), 1, 'onchange="costingType(this)" class="form-control costingType"  required'))
                            ?>';
                       
                       $('#material_consumption_body').append('<tr id="rowMC_' + v.bomMaterialConsumptionID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search_mc[]" placeholder="Item ID, Item Description..." value="' + v.itemName + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control bomMaterialConsumptionID" name="bomMaterialConsumptionID[]" value="' + v.bomMaterialConsumptionID + '"> </td> <td><input type="text" name="partNo" class="form-control partNo" value="' + v.partNo + '" readonly> </td> <td><input type="text" value="' + v.defaultUnitOfMeasure + '" class="form-control uom" disabled/></td> <td><input type="text" name="qtyUsed[]" value="' + v.qtyUsed + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();"> </td> <td>' + costingType + '</td> <td><input type="text" name="unitCost[]" value="' + v.unitCost + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();"> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCost[]" value="' + v.materialCost + '" class="materialCost"> </td> <td style="width: 100px"> <div class="input-group"> <input type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="' + v.markUp + '" onkeyup="cal_bom_material_total(this)" onfocus="this.select();"> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCharge, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCharge[]" value="' + v.materialCharge + '" class="materialCharge"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_materialConsumption(' + v.bomMaterialConsumptionID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializematerialTypeahead(search_id);
                        $("#ct_" + v.bomMaterialConsumptionID).val(v.costingType);
                        if (v.costingType == 1) {
                            $("#rowMC_" + v.bomMaterialConsumptionID).find('.unitCost').attr('readonly', 'readonly');
                        }
                        search_id++;
                        i++;
                    });
                } else {
                    init_materialConsupmtionForm();
                }
                calculateMaterialConsumtionTotal();
                calculateTotalCost();

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function load_bom_material_consumption_packaging(bomMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {bomMasterID: bomMasterID},
            url: "<?php echo site_url('MFQ_BillOfMaterial/load_bom_material_consumption'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#material_consumption_packaging_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var bomMaterialConsumptionID = v.bomMaterialConsumptionID;
                        var costingType = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="ct_\'+bomMaterialConsumptionID+\'"'), form_dropdown('costingType[]', array(1 => 'Average', 2 => 'PO', 3 => 'Manual'), 1, 'onchange="costingType(this)" class="form-control costingType"  required'))
                            ?>';
                        $('#material_consumption_packaging_body').append('<tr id="rowMC_' + v.bomMaterialConsumptionID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search_mc[]" placeholder="Item ID, Item Description..." value="' + v.itemName + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control bomMaterialConsumptionID" name="bomMaterialConsumptionID[]" value="' + v.bomMaterialConsumptionID + '"> </td> <td><input type="text" name="partNo" class="form-control partNo" value="' + v.partNo + '" readonly> </td> <td><input type="text" value="' + v.defaultUnitOfMeasure + '" class="form-control uom" disabled/></td> <td><input type="text" name="qtyUsed[]" value="' + v.qtyUsed + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();"> </td> <td>' + costingType + '</td> <td><input type="text" name="unitCost[]" value="' + v.unitCost + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();"> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCost[]" value="' + v.materialCost + '" class="materialCost"> </td> <td style="width: 100px"> <div class="input-group"> <input type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="' + v.markUp + '" onkeyup="cal_bom_material_total(this)" onfocus="this.select();"> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCharge, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCharge[]" value="' + v.materialCharge + '" class="materialCharge"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_materialConsumption(' + v.bomMaterialConsumptionID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializematerialTypeahead(search_id);
                        $("#ct_" + v.bomMaterialConsumptionID).val(v.costingType);
                        if (v.costingType == 1) {
                            $("#rowMC_" + v.bomMaterialConsumptionID).find('.unitCost').attr('readonly', 'readonly');
                        }
                        search_id++;
                        i++;
                    });
                } else {
                    init_materialConsupmtionForm();
                }
                calculateMaterialConsumtionTotal();
                calculateTotalCost();

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function load_bom_labour_task(bomMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {bomMasterID: bomMasterID},
            url: "<?php echo site_url('MFQ_BillOfMaterial/fetch_bom_labour_task'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#labour_task_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var bomLabourTaskID = v.bomLabourTaskID;
                        var segment = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="lb_\'+search_id5+\'"'), form_dropdown('la_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this),fetch_related_subsegment(this,this.value,\'+v.subsegmentID+\')" class="form-control segmentID"  required'))
                            ?>';
                        var subsegment = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="lbsub_\'+search_id5+\'"'), form_dropdown('la_subsegmentID[]',array(''=>'Select a Sub Segment'),'', 'class="form-control subsegmentID"  required'))
                            ?>';



                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="lbu_\'+search_id5+\'"'), form_dropdown('la_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                        $('#labour_task_body').append('<tr id="rowLB_' + v.bomLabourTaskID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control l_search" name="search[]" placeholder="Labour" id="l_search_' + search_id5 + '" value="' + v.description + '"> <input type="hidden" class="form-control labourTask" name="labourTask[]" value="' + v.labourTask + '"> <input type="hidden" class="form-control" name="bomLabourTaskID[]" value="' + v.bomLabourTaskID + '"> </td> <td><input type="text" name="la_activityCode[]"  class="form-control" value="' + v.activityCode + '"></td> <td>' + uom + '</td> <td>' + segment + '</td><td>'+subsegment+'</td> <td><input type="text" name="la_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="la_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_totalHours totalHours" onfocus="this.select();" value="' + v.totalHours + '"> </td> <td>&nbsp;<span class="lb_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="la_totalValue[]" class="lb_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_labour_task(' + v.bomLabourTaskID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        initializelabourtaskTypeahead(search_id5);
                        $("#lb_" + search_id5).val(v.segmentID);
                        $('#lbu_' +search_id5).val(v.uomID);
                        search_id5++;
                    });
                } else {
                    init_bom_labour_task();
                }
                calculateLabourTaskTotal();
                calculateTotalCost();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_bom_overhead_cost(bomMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {bomMasterID: bomMasterID},
            url: "<?php echo site_url('MFQ_BillOfMaterial/fetch_bom_overhead_cost'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#over_head_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var segment = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="oh_\'+search_id2+\'"'), form_dropdown('oh_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                            ?>';
                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ohu_\'+search_id2+\'"'), form_dropdown('oh_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                        $('#over_head_body').append('<tr id="rowOC_' + v.bomOverheadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_' + search_id2 + '" value="' + v.description + '"> <input type="hidden" class="form-control overheadID" name="overheadID[]" value="' + v.overheadID + '"> <input type="hidden" class="form-control bomOverheadID" name="bomOverheadID[]" value="' + v.bomOverheadID + '"> </td> <td><input type="text" name="oh_activityCode[]"  class="form-control" value="' + v.activityCode + '"></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="oh_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="oh_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="oh_totalValue[]" class="oh_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_overhead_cost(' + v.bomOverheadID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        $('#oh_' + search_id2).val(v.segmentID);
                        $('#ohu_' + search_id2).val(v.uomID);
                        initializeoverheadTypeahead(search_id2);
                        search_id2++;
                        i++;
                    });
                } else {
                    init_bom_overhead_cost();
                }
                calculateOverheadCostTotal();
                calculateTotalCost();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_bom_machine_cost(bomMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {bomMasterID: bomMasterID},
            url: "<?php echo site_url('MFQ_BillOfMaterial/fetch_bom_machine_cost'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#machine_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var segment = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="mc_\'+search_id3+\'"'), form_dropdown('mc_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                            ?>';
                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="mcu_\'+search_id3+\'"'), form_dropdown('mc_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                        $('#machine_body').append('<tr id="rowMC_' + v.bomMachineID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control mc_search" name="search[]" placeholder="Machine" id="mc_search_' + search_id3 + '" value="' + v.assetDescription + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]" value="' + v.mfq_faID + '"> <input type="hidden" class="form-control bomMachineID" name="bomMachineID[]" value="' + v.bomMachineID + '"> </td> <td><input type="text" name="mc_activityCode[]"  class="form-control" value="' + v.activityCode + '"></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="mc_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="mc_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="mc_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="mc_totalValue[]" class="mc_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_overhead_cost(' + v.bomOverheadID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        $('#mc_' + search_id3).val(v.segment);
                        $('#mcu_' + search_id3).val(v.uomID);
                        initializemachinecostTypeahead(search_id3);
                        search_id3++;
                        i++;
                    });
                } else {
                    init_bom_machine_cost();
                }
                calculateMachineCostTotal();
                calculateTotalCost();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function init_materialConsupmtionForm() {
        var costingType = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="ct_1"'), form_dropdown('costingType[]', array(1 => 'Average', 2 => "PO", 3 => "Manual"), 1, 'onchange="costingType(this)" class="form-control costingType"  required'))
            ?>';
        $('#material_consumption_body').html('');

        if(companyPolicy == 'FlowServe'){
            $('#material_consumption_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search_mc[]" placeholder="Item ID, Item Description..." id="f_search_1"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]"> <input type="hidden" class="form-control jcMaterialConsumptionID" name="jcMaterialConsumptionID[]"> </td> <td><input type="text" name="partNo" onkeyup="clearitemAutoID(event,this)"  class="form-control partNo" id="f_search_1" > </td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" readonly value=""></td> <td><input type="text" name="qtyUsed[]" value="0.00" placeholder="0.00" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();"> </td> <td>' + costingType + '</td> <td><input type="text" name="unitCost[]" value="0.00" placeholder="0.00" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="materialCost[]" value="0" class="materialCost"> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="materialCharge[]" value="0" class="materialCharge"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        }else{
            $('#material_consumption_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search_mc[]" placeholder="Item ID, Item Description..." id="f_search_1"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]"> <input type="hidden" class="form-control jcMaterialConsumptionID" name="jcMaterialConsumptionID[]"> </td> <td><input type="text" name="partNo" class="form-control partNo" readonly> </td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" readonly value=""></td> <td><input type="text" name="qtyUsed[]" value="0.00" placeholder="0.00" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();"> </td> <td>' + costingType + '</td> <td><input type="text" name="unitCost[]" value="0.00" placeholder="0.00" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="materialCost[]" value="0" class="materialCost"> </td> <td style="width: 100px"> <div class="input-group"> <input type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="0" onkeyup="cal_material_total(this)" onfocus="this.select();"> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="materialCharge[]" value="0" class="materialCharge"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');

        }

        setTimeout(function () {
            initializematerialTypeahead(1);
        }, 500);
    }

    function cal_material_total(element) {
        var usedQty = parseFloat($(element).closest('tr').find('.qtyUsed').val());
        var unitCost = parseFloat($(element).closest('tr').find('.unitCost').val());
        if(isNaN(unitCost) == true) {
            unitCost = parseFloat(0);
            parseFloat($(element).closest('tr').find('.unitCost').val(unitCost.toFixed(<?php echo $this->common_data['company_data']['company_default_decimal']; ?>)));
        }
        var markup = parseFloat($(element).closest('tr').find('.markupPrc').val());
        $(element).closest('tr').find('.materialCostTxt').text(commaSeparateNumber(parseFloat(usedQty) * unitCost,<?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $(element).closest('tr').find('.materialCost').val(((parseFloat(usedQty) * unitCost)).toFixed(<?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $(element).closest('tr').find('.materialChargeTxt').text(commaSeparateNumber((((parseFloat(usedQty) * unitCost) * markup) / 100) + (parseFloat(usedQty) * unitCost),<?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $(element).closest('tr').find('.materialCharge').val(((((parseFloat(usedQty) * unitCost) * markup) / 100) + (parseFloat(usedQty) * unitCost)).toFixed(<?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        calculateMaterialConsumtionTotal();
        calculateTotalCost();
    }

    function loadBoMDetail(bomMasterID) {
        if (bomMasterID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_BillOfMaterial/load_mfq_billOfMaterial"); ?>',
                dataType: 'json',
                data: {bomMasterID: bomMasterID},
                async: false,
                success: function (data) {
                    if (data['error'] == 0) {
                        //myAlert('s', data['message']);
                        $("#submitBtn").html('<i class="fa fa-pencil"></i> Save');
                        $("#bomType").val(data['bomType']).change();
                        processOrProductDropdown();
                        $("#process").val(data['mfqProcessID']).change();
                        $("#product").val(data['mfqItemID']).change();
                        $("#documentDate").val(data['documentDate']);
                        $('#bomCodeDiv').removeClass('hide');
                        $('#bomCode').val(data['documentCode']);
                        $("#bomDescription").val(data['description']);
                        $("#industryTypeID").val(data['industryTypeID']);
                        $("#Qty").val(data['Qty']);
                        $("#uomID").val(data['uomID']);
                        $('#itemUoM').val(data['UnitDes']);


                        if (data['productImage'] != null) {
                            $("#img_showcase").attr("src", '<?php echo base_url(); ?>uploads/' + data['productImage']);
                        }
                        loadBoMDetailTable(bomMasterID);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }


    function loadBoMDetailTable(bomMasterID) {
        if (bomMasterID > 0) {

            var Qty = $('#Qty').val();

            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_BillOfMaterial/load_mfq_billOfMaterial_detail"); ?>',
                dataType: 'json',
                data: {bomMasterID: bomMasterID},
                async: false,
                success: function (data) {
                    //myAlert('s', data['message']);
                    $('#material_consumption_body').html('');
                    if (!$.isEmptyObject(data["material"])) {
                        $.each(data["material"], function (k, v) {
                            var bomMaterialConsumptionID = v.bomMaterialConsumptionID;
                            var costingType = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="ct_\'+bomMaterialConsumptionID+\'"'), form_dropdown('costingType[]', array(1 => 'Average', 2 => 'PO', 3 => 'Manual'), 1, 'onchange="costingType(this)" class="form-control costingType"  required'))
                                ?>';

                            var sel = '<select name="uom[]" class="form-control uom" onchange="change_unit_of_measure(this,'+v.defaultUnitOfMeasureID+')">';
                            var conversion = [];

                            $(v.uom_details).each(function(ka,va) {
                                sel += '<option value='+va.subUnitID+'>'+va.UnitShortCode+'</option>';
                                conversion[va.subUnitID] = va.conversion;
                            });

                            sel += '</select> <input type="hidden" class="estQty" value="'+v.qtyUsed+'"> <input type="hidden" class="unitcostdefault" value="'+v.unitCost+'">';

                            //v.defaultUnitOfMeasure
                            //<input type="text" value="' +  + '" class="form-control uom"/>

                            v.qtyUsed = v.qtyUsed * Qty;
                            // v.unitCost =  v.unitCost * Qty;
                            v.materialCost = v.materialCost * Qty
                            v.materialCharge =  v.materialCharge * Qty;
                            if(companyPolicy == 'FlowServe'){
                                $('#material_consumption_body').append('<tr id="rowMC_' + v.bomMaterialConsumptionID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search_mc[]" placeholder="Item ID, Item Description..." value="' + v.itemName + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control bomMaterialConsumptionID" name="bomMaterialConsumptionID[]" value="' + v.bomMaterialConsumptionID + '"> </td> <td><input type="text" name="partNo" class="form-control partNo" value="' + v.partNo + '" readonly> </td> <td>'+sel+'</td> <td><input type="text" name="qtyUsed[]" value="' + v.qtyUsed + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();"> </td> <td>' + costingType + '</td> <td><input type="text" name="unitCost[]" value="' + v.unitCost + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();"> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCost[]" value="' + v.materialCost + '" class="materialCost"> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCharge, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCharge[]" value="' + v.materialCharge + '" class="materialCharge"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_materialConsumption(' + v.bomMaterialConsumptionID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');

                            }else{
                                $('#material_consumption_body').append('<tr id="rowMC_' + v.bomMaterialConsumptionID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search_mc[]" placeholder="Item ID, Item Description..." value="' + v.itemName + '" id="f_search_' + search_id + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control bomMaterialConsumptionID" name="bomMaterialConsumptionID[]" value="' + v.bomMaterialConsumptionID + '"> </td> <td><input type="text" name="partNo" class="form-control partNo" value="' + v.partNo + '" readonly> </td> <td>'+sel+'</td> <td><input type="text" name="qtyUsed[]" value="' + v.qtyUsed + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();"> </td> <td>' + costingType + '</td> <td><input type="text" name="unitCost[]" value="' + v.unitCost + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();"> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCost[]" value="' + v.materialCost + '" class="materialCost"> </td> <td style="width: 100px"> <div class="input-group"> <input type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="' + v.markUp + '" onkeyup="cal_bom_material_total(this)" onfocus="this.select();"> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCharge, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCharge[]" value="' + v.materialCharge + '" class="materialCharge"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_materialConsumption(' + v.bomMaterialConsumptionID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');

                            }
                            initializematerialTypeahead(search_id);
                            $("#ct_" + v.bomMaterialConsumptionID).val(v.costingType);
                            if (v.costingType == 1) {
                                $("#rowMC_" + v.bomMaterialConsumptionID).find('.unitCost').attr('readonly', 'readonly');
                            }
                            search_id++;
                        });
                        calculateMaterialConsumtionTotal();
                    } else {
                        init_materialConsupmtionForm();
                    }

                    if (!$.isEmptyObject(data["packaging"])) {
                        $.each(data["packaging"], function (k, v) {
                            var bomMaterialConsumptionID = v.bomMaterialConsumptionID;
                            var costingType = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="ct_\'+bomMaterialConsumptionID+\'"'), form_dropdown('costingType[]', array(1 => 'Average', 2 => 'PO', 3 => 'Manual'), 1, 'onchange="costingType(this)" class="form-control costingType"  required'))
                                ?>';
                            $('#material_consumption_packaging_body').append('<tr id="rowMC_' + v.bomMaterialConsumptionID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search_mc[]" placeholder="Item ID, Item Description..." value="' + v.itemName + '" id="f_pack_search_' + search_id + '"> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '"> <input type="hidden" class="form-control bomMaterialConsumptionID" name="bomMaterialConsumptionID[]" value="' + v.bomMaterialConsumptionID + '"> </td> <td><input type="text" name="partNo" class="form-control partNo" value="' + v.partNo + '" readonly> </td> <td><input type="text" value="' + v.defaultUnitOfMeasure + '" class="form-control uom" disabled/></td> <td><input type="text" name="qtyUsed[]" value="' + v.qtyUsed + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();"> </td> <td>' + costingType + '</td> <td><input type="text" name="unitCost[]" value="' + v.unitCost + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();"> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCost[]" value="' + v.materialCost + '" class="materialCost"> </td> <td style="width: 100px"> <div class="input-group"> <input type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="' + v.markUp + '" onkeyup="cal_bom_material_total(this)" onfocus="this.select();"> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCharge, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="materialCharge[]" value="' + v.materialCharge + '" class="materialCharge"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_materialConsumption(' + v.bomMaterialConsumptionID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            initializematerialTypeahead(search_id,'PACKAGING');
                            $("#ct_" + v.bomMaterialConsumptionID).val(v.costingType);
                            if (v.costingType == 1) {
                                $("#rowMC_" + v.bomMaterialConsumptionID).find('.unitCost').attr('readonly', 'readonly');
                            }
                            search_id++;
                        });
                       // calculateMaterialConsumtionTotal();
                    } else {
                       // init_materialConsupmtionForm();
                    }

                    $('#labour_task_body').html('');
                    if (!$.isEmptyObject(data["labour"])) {
                        $.each(data["labour"], function (k, v) {
                            var bomLabourTaskID = v.bomLabourTaskID;
                            var segment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="lb_\'+search_id5+\'"'), form_dropdown('la_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this),fetch_related_subsegment(this,this.value,\'+v.subsegmentID+\')" class="form-control segmentID"  required'))
                                ?>';
                            var subsegment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="lbsub_\'+search_id5+\'"'), form_dropdown('la_subsegmentID[]',array(''=>'Select a Sub Segment'),'', 'class="form-control subsegmentID"  required'))
                                ?>';

                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="lbu_\'+search_id5+\'"'), form_dropdown('la_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                           
                            v.totalHours = v.totalHours * Qty;
                            v.totalValue =  v.totalValue * Qty;
                          
                            $('#labour_task_body').append('<tr id="rowLB_' + v.bomLabourTaskID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control l_search" name="search[]" placeholder="Labour" id="l_search_' + search_id5 + '" value="' + v.description + '"> <input type="hidden" class="form-control labourTask" name="labourTask[]" value="' + v.labourTask + '"> <input type="hidden" class="form-control" name="bomLabourTaskID[]" value="' + v.bomLabourTaskID + '"> </td> <td><input type="text" name="la_activityCode[]"  class="form-control" value="' + v.activityCode + '"></td> <td>' + uom + '</td> <td>' + segment + '</td> <td>'+subsegment+'</td> <td><input type="text" name="la_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="la_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_totalHours totalHours" onfocus="this.select();" value="' + v.totalHours + '"> </td> <td>&nbsp;<span class="lb_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="la_totalValue[]" class="lb_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_labour_task(' + v.bomLabourTaskID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            
                            initializelabourtaskTypeahead(search_id5);
                            $("#lb_" + search_id5).val(v.segmentID).change();


                            $('#lbu_' + search_id5).val(v.uomID);
                            search_id5++;
                        });
                        calculateLabourTaskTotal();
                    } else {
                        init_bom_labour_task();
                    }

                    $('#over_head_body').html('');
                    if (!$.isEmptyObject(data["overhead"])) {
                        $.each(data["overhead"], function (k, v) {
                            var segment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="oh_\'+search_id2+\'"'), form_dropdown('oh_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                                ?>';
                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ohu_\'+search_id2+\'"'), form_dropdown('oh_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                            
                            v.totalHours = v.totalHours * Qty;
                            v.totalValue =  v.totalValue * Qty;
                            if(companyPolicy == 'FlowServe'){

                            }else{

                            }
                            $('#over_head_body').append('<tr id="rowOC_' + v.bomOverheadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_' + search_id2 + '" value="' + v.description + '"> <input type="hidden" class="form-control overheadID" name="overheadID[]" value="' + v.overheadID + '"> <input type="hidden" class="form-control bomOverheadID" name="bomOverheadID[]" value="' + v.bomOverheadID + '"> </td> <td><input type="text" name="oh_activityCode[]"  class="form-control" value="' + v.activityCode + '"></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="oh_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="oh_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, 2) + '</span> <input type="hidden" name="oh_totalValue[]" class="oh_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_overhead_cost(' + v.bomOverheadID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            $('#oh_' + search_id2).val(v.segmentID);
                            $('#ohu_' + search_id2).val(v.uomID);
                            initializeoverheadTypeahead(search_id2);
                            search_id2++;
                        });
                        calculateOverheadCostTotal();
                    } else {
                        init_bom_overhead_cost();
                    }

                    $('#third_party_service_body').html('');
                    if (!$.isEmptyObject(data["third_party_service"])) {
                        $.each(data["third_party_service"], function (k, v) {

                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="tpsu_\'+search_id2+\'"'), form_dropdown('tps_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                           
                            v.totalHours = v.totalHours * Qty;
                            v.totalValue =  v.totalValue * Qty;
                            
                            if(companyPolicy == 'FlowServe'){
                                $('#third_party_service_body').append('<tr id="rowTPS_' + v.bomOverheadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control tps_search" name="tps_search[]" placeholder="Third Party Service" id="tps_search_' + search_id2 + '" value="' + v.description + '"> <input type="hidden" class="form-control tpsID" name="tpsID[]" value="' + v.overheadID + '"> <input type="hidden" class="form-control bomtpsID" name="bomtpsID[]" value="' + v.bomOverheadID + '"> </td> <td><input type="text" name="tps_activityCode[]"  class="form-control" value="' + v.activityCode + '"></td>  <td>' + uom + '</td>   <td><input type="text" name="tps_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_hourRate" onfocus="this.select();"> </td> <td class="hide"><input type="text" name="tps_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="tps_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="tps_totalValue[]" class="tps_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_tps_cost(' + v.bomOverheadID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            }else{
                                $('#third_party_service_body').append('<tr id="rowTPS_' + v.bomOverheadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control tps_search" name="tps_search[]" placeholder="Third Party Service" id="tps_search_' + search_id2 + '" value="' + v.description + '"> <input type="hidden" class="form-control tpsID" name="tpsID[]" value="' + v.overheadID + '"> <input type="hidden" class="form-control bomtpsID" name="bomtpsID[]" value="' + v.bomOverheadID + '"> </td> <td><input type="text" name="tps_activityCode[]"  class="form-control" value="' + v.activityCode + '"></td>  <td>' + uom + '</td>   <td><input type="text" name="tps_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="tps_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="tps_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="tps_totalValue[]" class="tps_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_tps_cost(' + v.bomOverheadID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            }
                            //$('#oh_' + search_id2).val(v.segmentID);
                            $('#tpsu_' + search_id2).val(v.uomID);
                            initializethirdpartyserviceTypeahead(search_id2);
                            search_id2++;
                        });
                        calculateThirdPartyServiceCostTotal();
                        //calculateOverheadCostTotal();
                    } else {
                        init_bom_third_party_service_cost();
                    }

                    $('#machine_body').html('');
                    if (!$.isEmptyObject(data["machine"])) {
                        $.each(data["machine"], function (k, v) {
                            var segment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="mc_\'+search_id3+\'"'), form_dropdown('mc_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                                ?>';
                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="mcu_\'+search_id3+\'"'), form_dropdown('mc_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                            $('#machine_body').append('<tr id="rowMC_' + v.bomMachineID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control mc_search" name="search[]" placeholder="Machine" id="mc_search_' + search_id3 + '" value="' + v.assetDescription + '"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]" value="' + v.mfq_faID + '"> <input type="hidden" class="form-control bomMachineID" name="bomMachineID[]" value="' + v.bomMachineID + '"> </td> <td><input type="text" name="mc_activityCode[]"  class="form-control" value="' + v.activityCode + '"></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="mc_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="mc_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="mc_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>) + '</span> <input type="hidden" name="mc_totalValue[]" class="mc_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_overhead_cost(' + v.bomOverheadID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                            $('#mc_' + search_id3).val(v.segment);
                            $('#mcu_' + search_id3).val(v.uomID);
                            initializemachinecostTypeahead(search_id3);
                            search_id3++;
                        });
                        calculateMachineCostTotal();
                    } else {
                        init_bom_machine_cost();
                    }

                    calculateTotalCost();

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }

    function change_unit_of_measure(ev,uomID){

        var unitID = $(ev).val();

        if(unitID == uomID){

            var qtyUsed = $(ev).closest('tr').find('.estQty').val();
            var unitCost = $(ev).closest('tr').find('.unitcostdefault').val();
            // var material = $(ev).closest('tr').find('.materialQtyUsage').val();

            if(qtyUsed && unitCost){
                $(ev).closest('tr').find('.qtyUsed').val(qtyUsed);
                $(ev).closest('tr').find('.unitCost').val(unitCost);
            }

         
            // $(ev).closest('tr').find('.materialQtyUsage').val(material);
            // $(ev).closest('tr').find('.usageQty').val(material);

        }

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'subUnitID':unitID,'defaultUomID':uomID},
                url: "<?php echo site_url('MFQ_Job_Card/get_unit_of_measure_conversion'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    stopLoad();

                    var qtyUsed = $(ev).closest('tr').find('.qtyUsed').val();
                    var unitCost = $(ev).closest('tr').find('.unitCost').val();
                    // var materialQtyUsage = $(ev).closest('tr').find('.materialQtyUsage').val();
                    
                    var converted_qtyUsed = parseFloat(qtyUsed) * parseFloat(data);
                    var converted_unitCost = (parseFloat(unitCost) / parseFloat(data));
                    // var converted_materialQtyUsage = parseFloat(materialQtyUsage) * parseFloat(data);

                    if(converted_qtyUsed > 0 ){
                        $(ev).closest('tr').find('.qtyUsed').val(converted_qtyUsed);
                        $(ev).closest('tr').find('.unitCost').val(converted_unitCost);
                    }else{
                        $(ev).closest('tr').find('.qtyUsed').val(1).trigger('onkeyup');
                    }
                  
                    // $(ev).closest('tr').find('.materialQtyUsage').val(converted_materialQtyUsage);
                    // $(ev).closest('tr').find('.usageQty').val(converted_materialQtyUsage);
                
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }


    function fetch_related_uom_id(unitID,ev){

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'masterUnitID':unitID},
                url: "<?php echo site_url('MFQ_BillOfMaterial/fetch_related_uom_fn'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    stopLoad();

                    // var sel = '<select name="uom[]" class="form-control uom" onchange="change_unit_of_measure(this,'+v.defaultUnitOfMeasureID+')">';
                    // var conversion = [];
                    var select = $(ev).closest('tr').find('.uom');

                    $(data).each(function(ka,va) {
                        //sel = '<option value='+va.subUnitID+'>'+va.UnitShortCode+'</option>';
                        // conversion[va.subUnitID] = va.conversion;
                      //  select.append(sel);
                        select.append($('<option>', { 
                            value: va.UnitID,
                            text : va.UnitShortCode 
                        }));
                    });

                    
                  
                
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });


    }

    function saveBoMMaster(type) {
        <?php if($policy_id == 'EST') { ?>
        $('#product').prop('disabled', false);
        <?php } ?>
        var data = new FormData($('.frm_billOfMaterial')[0]);
        data.append('status', type);
        data.append('estimateDetailID', <?php echo $master_page_url?>);
        $.ajax({
            url: "<?php echo site_url('MFQ_BillOfMaterial/add_edit_BillOfMaterial'); ?>",
            type: 'post',
            data: data,
            mimeType: "multipart/form-data",
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,

            beforeSend: function () {
                startLoad();
                <?php if($policy_id == 'EST') { ?>
                $('#product').prop('disabled', true);
                <?php } ?>
            },
            uploadProgress: function (event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                /*bar.width(percentVal);
                 percent.html(percentVal);*/
            },

            success: function (data) {
                stopLoad();
                if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
                else if (data['error'] == 0) {
                    $("#bomMasterID").val(data['masterID']);
                    /*load_bom_labour_task(data['masterID']);
                    load_bom_material_consumption(data['masterID']);
                    load_bom_overhead_cost(data['masterID']);
                    load_bom_machine_cost(data['masterID']);*/
                    loadBoMDetailTable(data['masterID']);
                    <?php
                    if ($policy_id == 'EST') {
                    ?>
                    load_estimate_detail(data['estimateMasterID']);
                    <?php
                    }
                    ?>
                    myAlert('s', data['message']);
                    if (type==2) {
                        <?php
                        if ($policy_id != 'EST') {
                        ?>
                        $(".headerclose").trigger("click");
                        <?php
                        }else{
                        ?>
                        $('#bom_detail_modal').modal('hide');
                        <?php } ?>
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }

    function init_bom_labour_task() {
        var segment = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="lb_1"'), form_dropdown('la_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this),fetch_related_subsegment(this,this.value)" class="form-control segmentID"  required'))
            ?>';
        var subsegment = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="lbsub_1"'), form_dropdown('la_subsegmentID[]',array(''=>'Select a Sub Segment'),'', 'class="form-control subsegmentID"  required'))
            ?>';
        var uom = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="lbu_1"'), form_dropdown('la_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required'))
            ?>';
        $('#labour_task_body').html('');
        $('#labour_task_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control l_search" name="search[]" placeholder="Labour" id="l_search_1"> <input type="hidden" class="form-control labourTask" name="labourTask[]"> <input type="hidden" class="form-control jcLabourTaskID" name="jcLabourTaskID[]"> </td> <td><input type="text" name="la_activityCode[]"  class="form-control"></td><td>' + uom + '</td> <td>' + segment + '</td><td>'+subsegment+'</td><td><input type="text" name="la_hourlyRate[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="la_totalHours[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="lb_totalValueTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="la_totalValue[]" class="lb_totalValue"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        setTimeout(function () {
            initializelabourtaskTypeahead(1);
            search_id5++;
        }, 500);
    }

    function init_bom_overhead_cost() {
        var segment = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="oh_1"'), form_dropdown('oh_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
            ?>';
        var uom = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="ohu_1"'), form_dropdown('oh_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required'))
            ?>';
        $('#over_head_body').html('');
        $('#over_head_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_1"> <input type="hidden" class="form-control overheadID" name="overheadID[]"> <input type="hidden" class="form-control bomOverheadID" name="bomOverheadID[]"> </td> <td><input type="text" name="oh_activityCode[]"  class="form-control"></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="oh_hourlyRate[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="oh_totalHours[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="oh_totalValue[]" class="oh_totalValue"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        setTimeout(function () {
            initializeoverheadTypeahead(1);
        }, 500);
    }


    function init_bom_machine_cost() {
        var segment = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="mc_1"'), form_dropdown('mc_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
            ?>';
        var uom = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="mcu_1"'), form_dropdown('mc_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required'))
            ?>';
        $('#machine_body').html('');
        $('#machine_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control mc_search" name="search[]" placeholder="Machine" id="mc_search_1"> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]"> <input type="hidden" class="form-control bomMachineID" name="bomMachineID[]"> </td> <td><input type="text" name="mc_activityCode[]"  class="form-control"></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="mc_hourlyRate[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="mc_totalHours[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="mc_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="mc_totalValue[]" class="mc_totalValue"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        setTimeout(function () {
            initializemachinecostTypeahead(1);
        }, 500);
    }

    function delete_labour_task(id, masterID) {
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
                    url: "<?php echo site_url('MFQ_BillOfMaterial/delete_labour_task'); ?>",
                    type: 'post',
                    data: {bomLabourTaskID: id, masterID: masterID},
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
                                init_bom_labour_task();
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
                    url: "<?php echo site_url('MFQ_BillOfMaterial/delete_materialConsumption'); ?>",
                    type: 'post',
                    data: {bomMaterialConsumptionID: id, masterID: masterID},
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
                                init_materialConsupmtionForm();
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

    function delete_overhead_cost(id, masterID) {
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
                    url: "<?php echo site_url('MFQ_BillOfMaterial/delete_overhead_cost'); ?>",
                    type: 'post',
                    data: {bomOverheadID: id, masterID: masterID},
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
                            $("#rowOC_" + id).remove();
                            if (data.code == 1) {
                                init_bom_overhead_cost();
                            }
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
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_BillOfMaterial/delete_machine_cost'); ?>",
                    type: 'post',
                    data: {bomMachineID: id, masterID: masterID},
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
                                init_bom_machine_cost();
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
        var tot_unitCost = 0;
        var tot_materialCost = 0;
        var tot_markupPrc = 0;
        var tot_materialCharge = 0;
        $('#material_consumption_body tr').each(function () {
            var tot_qtyUsed_value = parseFloat($('td', this).eq(3).find('input').val());
            if (!isNaN(tot_qtyUsed_value)) {
                tot_qtyUsed += tot_qtyUsed_value;
            }

            var tot_unitCost_value = parseFloat($('td', this).eq(5).find('input').val());
            if (!isNaN(tot_unitCost_value)) {
                tot_unitCost += tot_unitCost_value;
            }

            var tot_materialCost_value = parseFloat($('td', this).eq(6).find('input').val());
            if (!isNaN(tot_materialCost_value)) {
                tot_materialCost += tot_materialCost_value;
            }

            
            if(companyPolicy == 'FlowServe'){
                var tot_materialCharge_value = parseFloat($('td', this).eq(7).find('input').val());
            }else{
                var tot_materialCharge_value = parseFloat($('td', this).eq(8).find('input').val());
            }
    
            if (!isNaN(tot_materialCharge_value)) {
                tot_materialCharge += tot_materialCharge_value;
            }
        });

        $("#tot_qtyUsed").text(commaSeparateNumber(tot_qtyUsed, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_unitCost").text(commaSeparateNumber(tot_unitCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_materialCost").text(commaSeparateNumber(tot_materialCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_materialCharge").text(commaSeparateNumber(tot_materialCharge, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }

    function calculateLabourTaskTotal() {
        var tot_hourRate = 0;
        var tot_totalHours = 0;
        var tot_totalValue = 0;
        $('#labour_task_body tr').each(function () {
            var tot_hourRate_value = parseFloat($('td', this).eq(5).find('input').val());
            if (!isNaN(tot_hourRate_value)) {
                tot_hourRate += tot_hourRate_value;
            }

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
        $("#tot_lb_totalHours").text(commaSeparateNumber(tot_totalHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_lb_totalValue").text(commaSeparateNumber(tot_totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }


    function calculateOverheadCostTotal() {
        var tot_hourRate = 0;
        var tot_totalHours = 0;
        var tot_totalValue = 0;
        $('#over_head_body tr').each(function () {
            var tot_hourRate_value = parseFloat($('td', this).eq(4).find('input').val());
            if (!isNaN(tot_hourRate_value)) {
                tot_hourRate += tot_hourRate_value;
            }

            var tot_totalHours_value = parseFloat($('td', this).eq(5).find('input').val());
            if (!isNaN(tot_totalHours_value)) {
                tot_totalHours += tot_totalHours_value;
            }

            var tot_totalValue_value = parseFloat($('td', this).eq(6).find('input').val());
            if (!isNaN(tot_totalValue_value)) {
                tot_totalValue += tot_totalValue_value;
            }
        });

        $("#tot_oh_hourRate").text(commaSeparateNumber(tot_hourRate, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_oh_totalHours").text(commaSeparateNumber(tot_totalHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_oh_totalValue").text(commaSeparateNumber(tot_totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }

    function calculateMachineCostTotal() {
        var tot_hourRate = 0;
        var tot_totalHours = 0;
        var tot_totalValue = 0;
        $('#machine_body tr').each(function () {
            var tot_hourRate_value = parseFloat($('td', this).eq(4).find('input').val());
            if (!isNaN(tot_hourRate_value)) {
                tot_hourRate += tot_hourRate_value;
            }

            var tot_totalHours_value = parseFloat($('td', this).eq(5).find('input').val());
            if (!isNaN(tot_totalHours_value)) {
                tot_totalHours += tot_totalHours_value;
            }

            var tot_totalValue_value = parseFloat($('td', this).eq(6).find('input').val());
            if (!isNaN(tot_totalValue_value)) {
                tot_totalValue += tot_totalValue_value;
            }
        });

        $("#tot_mc_hourRate").text(commaSeparateNumber(tot_hourRate, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_mc_totalHours").text(commaSeparateNumber(tot_totalHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_mc_totalValue").text(commaSeparateNumber(tot_totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }


    function calculateTotalCost() {
        var totalMateialConsumption = parseFloat($('#tot_materialCharge').text().replace(/,/g, ''));
        var totalLabourTask = parseFloat($('#tot_lb_totalValue').text().replace(/,/g, ''));
        var totalOverhead = parseFloat($('#tot_oh_totalValue').text().replace(/,/g, ''));
        var totalMachine = parseFloat($('#tot_mc_totalValue').text().replace(/,/g, ''));
        var totalThirdPartyService = parseFloat($('#tot_tps_totalValue').text().replace(/,/g, ''));

        var totalCost = (totalMateialConsumption + totalLabourTask + totalOverhead + totalMachine + totalThirdPartyService);
        $("#totalCost").text(commaSeparateNumber((totalMateialConsumption + totalLabourTask + totalOverhead + totalMachine + totalThirdPartyService), <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        if ($('#Qty').val() > 0) {
            $("#costperunit").text(commaSeparateNumber((totalCost / $('#Qty').val()), <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        } else {
            $("#costperunit").text(0);
        }
    }

    function load_unit_of_measure() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {mfqItemID: $('#product').val()},
            url: "<?php echo site_url('MFQ_Job/load_unit_of_measure'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#itemUoM').val(data.UnitDes);
                $('#uomID').val(data.defaultUnitOfMeasureID);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function checkItemInBom() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {mfqItemID: $('#product').val(), bomMasterID: $("#bomMasterID").val()},
            url: "<?php echo site_url('MFQ_BillOfMaterial/checkItemInBom'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    myAlert('w', 'Already a bom created for selected item');
                    $('#product').val(null).trigger('change.select2');
                } else {
                    load_unit_of_measure();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function cal_bom_material_total(element) {
        var usedQty = parseFloat($(element).closest('tr').find('.qtyUsed').val());
        var unitCost = parseFloat($(element).closest('tr').find('.unitCost').val());
        if(isNaN(unitCost) == true) {
            unitCost = parseFloat(0);
            parseFloat($(element).closest('tr').find('.unitCost').val(unitCost.toFixed(<?php echo $this->common_data['company_data']['company_default_decimal']; ?>)));
        }
        var markup = parseFloat($(element).closest('tr').find('.markupPrc').val());
        $(element).closest('tr').find('.materialCostTxt').text(commaSeparateNumber(parseFloat(usedQty) * unitCost, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $(element).closest('tr').find('.materialCost').val(((parseFloat(usedQty) * unitCost)).toFixed(<?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        var materialCost = parseFloat($(element).closest('tr').find('.materialCost').val());
        $(element).closest('tr').find('.materialChargeTxt').text(commaSeparateNumber((parseFloat(materialCost) + (parseFloat(materialCost) * (markup / 100))), <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $(element).closest('tr').find('.materialCharge').val((parseFloat(materialCost) + (parseFloat(materialCost) * (markup / 100))).toFixed(<?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        calculateMaterialConsumtionTotal();
        calculateTotalCost();
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

    function costingType(element) {
        var mfqItemID = $(element).closest('tr').find('.mfqItemID').val();
        if ($(element).val() == 1) {
            $(element).closest('tr').find('.unitCost').attr('readonly', 'readonly');
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_Job_Card/fetch_material_by_id"); ?>',
                dataType: 'json',
                data: {mfqItemID: mfqItemID},
                async: false,
                success: function (data) {
                    if (data) {
                        $(element).closest('tr').find('.unitCost').val(data.companyLocalWacAmountMod);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        } else if ($(element).val() == 2) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_Job_Card/fetch_po_unit_cost"); ?>',
                dataType: 'json',
                data: {mfqItemID: mfqItemID},
                async: false,
                success: function (data) {
                    if (data) {
                        $(element).closest('tr').find('.unitCost').val(data.companyLocalWacAmount);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        } else {
            $(element).closest('tr').find('.unitCost').prop('readonly', false);
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
    function init_bom_third_party_service_cost() {

        var uom = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="tpsu_1"'), form_dropdown('tps_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required'))
            ?>';
        $('#third_party_service_body').html('');

        if(companyPolicy == 'FlowServe'){
        $('#third_party_service_body').append('<tr> ' +
            '<td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control tps_search" name="tps_search[]" placeholder="Third Party Service" id="tps_search_1">' +
                ' <input type="hidden" class="form-control tpsID" name="tpsID[]"> <input type="hidden" class="form-control bomtpsID" name="bomtpsID[]"> </td>' +
                ' <td><input type="text" name="tps_activityCode[]" value="" placeholder="" onkeyup="" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_activityCode" onfocus="this.select();"></td>' +
                ' <td>' + uom + '</td>' +
                ' <td><input type="text" name="tps_hourlyRate[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_third_party_service_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_hourRate" onfocus="this.select();"> </td>' +
                ' <td class="hide"><input type="text" name="tps_totalHours[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_third_party_service_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_totalHours totalHours" onfocus="this.select();"> </td>' +
                ' <td>&nbsp;<span class="tps_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="tps_totalValue[]" class="tps_totalValue"> </td>' +
                ' <td class="remove-td" style="vertical-align: middle;text-align: center"></td> ' +
            '</tr>');

        } else {
            $('#third_party_service_body').append('<tr> ' +
            '<td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control tps_search" name="tps_search[]" placeholder="Third Party Service" id="tps_search_1">' +
                ' <input type="hidden" class="form-control tpsID" name="tpsID[]"> <input type="hidden" class="form-control bomtpsID" name="bomtpsID[]"> </td>' +
                ' <td><input type="text" name="tps_activityCode[]" value="" placeholder="" onkeyup="" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_activityCode" onfocus="this.select();"></td>' +
                ' <td>' + uom + '</td>' +
                ' <td><input type="text" name="tps_hourlyRate[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_third_party_service_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_hourRate" onfocus="this.select();"> </td>' +
                ' <td><input type="text" name="tps_totalHours[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_third_party_service_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_totalHours totalHours" onfocus="this.select();"> </td>' +
                ' <td>&nbsp;<span class="tps_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="tps_totalValue[]" class="tps_totalValue"> </td>' +
                ' <td class="remove-td" style="vertical-align: middle;text-align: center"></td> ' +
            '</tr>');
        }
        setTimeout(function () {
            initializethirdpartyserviceTypeahead(1);
        }, 500);
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
        var tot_totalValue_final = 0;
        
        $('#third_party_service_body tr').each(function () {
            var tot_hourRate_value = parseFloat($('td', this).eq(2).find('input').val());
            if (!isNaN(tot_hourRate_value)) {
                tot_hourRate += tot_hourRate_value;
            }

            var tot_totalHours_value = parseFloat($('td', this).eq(3).find('input').val());
            if (!isNaN(tot_totalHours_value)) {
                tot_totalHours += tot_totalHours_value;
            }

            var tot_totalValue_value_final = parseFloat($('td', this).eq(5).find('input').val());
            if (!isNaN(tot_totalValue_value_final)) {
                tot_totalValue_final += tot_totalValue_value_final;
            }

            var tot_totalValue_value = parseFloat($('td', this).eq(4).find('input').val());
            if (!isNaN(tot_totalValue_value)) {
                tot_totalValue += tot_totalValue_value;
            }
        });

        if(companyPolicy == 'FlowServe'){
            tot_totalValue = tot_totalHours;
        }
        
        if(companyPolicy == 'Micoda'){
            tot_hourRate = tot_totalHours;
            tot_totalHours = tot_totalValue;
            tot_totalValue = tot_totalValue_final;
        }
        

        $("#tot_tps_hourRate").text(commaSeparateNumber(tot_hourRate, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_tps_totalHours").text(commaSeparateNumber(tot_totalHours, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
        $("#tot_tps_totalValue").text(commaSeparateNumber(tot_totalValue, <?php echo $this->common_data['company_data']['company_default_decimal']; ?>));
    }

    /*function load_bom_third_party_service_cost(bomMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {bomMasterID: bomMasterID},
            url: "<?php echo site_url('MFQ_BillOfMaterial/fetch_bom_overhead_cost'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#over_head_body').html('');
                var i = 0;
                if (!$.isEmptyObject(data)) {
                    $.each(data, function (k, v) {
                        var segment = '<?php
                            echo str_replace(array("\n", '<select'), array('', '<select id="oh_\'+search_id2+\'"'), form_dropdown('oh_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  required'))
                            ?>';
                        var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ohu_\'+search_id2+\'"'), form_dropdown('oh_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required')) ?>';
                        $('#over_head_body').append('<tr id="rowOC_' + v.bomOverheadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_' + search_id2 + '" value="' + v.description + '"> <input type="hidden" class="form-control overheadID" name="overheadID[]" value="' + v.overheadID + '"> <input type="hidden" class="form-control bomOverheadID" name="bomOverheadID[]" value="' + v.bomOverheadID + '"> </td> <td><input type="text" name="oh_activityCode[]"  class="form-control" value="' + v.activityCode + '"></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="oh_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();"> </td> <td><input type="text" name="oh_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours totalHours" onfocus="this.select();"> </td> <td>&nbsp;<span class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, 2) + '</span> <input type="hidden" name="oh_totalValue[]" class="oh_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_overhead_cost(' + v.bomOverheadID + ',' + v.bomMasterID + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        $('#oh_' + search_id2).val(v.segmentID);
                        $('#ohu_' + search_id2).val(v.uomID);
                        initializeoverheadTypeahead(search_id2);
                        search_id2++;
                        i++;
                    });
                } else {
                    init_bom_overhead_cost();
                }
                calculateOverheadCostTotal();
                calculateTotalCost();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }*/


    function delete_tps_cost(id, masterID) {
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
                    url: "<?php echo site_url('MFQ_BillOfMaterial/delete_third_party_service_cost'); ?>",
                    type: 'post',
                    data: {bomOverheadID: id, masterID: masterID},
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
                            $("#rowTPS_" + id).remove();
                            if (data.code == 1) {
                                init_bom_third_party_service_cost();
                            }
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

    function processOrProductDropdown(){
        if ($('#bomType').val() == 1 ) {
            $("#product_process_label").html('Product');
            $("#product_dropdown").show();
            $("#process_dropdown").addClass("hide");
        }else{
            $("#product_process_label").html('Process');
            $("#process_dropdown").removeClass("hide");
            $("#product_dropdown").hide();
        }
    }
</script>