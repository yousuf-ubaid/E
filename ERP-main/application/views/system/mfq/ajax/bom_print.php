<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$date_format_policy = date_format_policy();
$from = convert_date_format($this->common_data['company_data']['FYPeriodDateFrom']);
$currency_arr = all_currency_new_drop();
$current_date = current_format_date();
$umo_arr = array('' => 'Select UOM');
$umo_arr2 = all_umo_new_drop();
$category = get_mfq_category();
$segment = fetch_mfq_segment(true);



if (!empty($bomMasterID)){
?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('plugins/mfq/typehead.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
    <script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<div class="row">
    <div class="col-md-12">
        <header class="head-title">
            <h2><?php echo $this->lang->line('manufacturing_bom_information'); ?><!--BOM Information--> </h2>
        </header>
        <div class="row">
            <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4 md-offset-2">
                        <label class="title"><?php echo $this->lang->line('manufacturing_product'); ?><!--Product--> </label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php
                        echo form_dropdown('product', get_finishedgoods_drop(), $mfqItemID, 'id="product" class="form-control select2" disabled');
                        ?>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4 md-offset-2">
                        <label class="title"><?php echo $this->lang->line('manufacturing_date'); ?><!--Date--></label>
                    </div>
                    <div class="form-group col-sm-4">
                         <div class='input-group date filterDate' id="">
                             <input type='text' class="form-control" name="documentDate"
                                       id="documentDate" value="<?php echo $documentDate ?>"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'  " disabled />
                             <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                             </span>
                         </div>
                    </div>
                </div>

                <div class="row hide" style="margin-top: 10px;">
                    <div class="form-group col-sm-4 md-offset-2">
                        <label class="title"><?php echo $this->lang->line('manufacturing_industry_type'); ?><!--Industry Type--> </label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('industryTypeID', get_industryType_drop(), $industryTypeID, 'id="industryTypeID" class="form-control" required') ?>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4 md-offset-2">
                        <label class="title"><?php echo $this->lang->line('manufacturing_unit_of_measure'); ?><!--Unit of Measure--></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" name="UOM" class="form-control" id="itemUoM" value="<?php echo $UnitDes ?>"
                               readonly>
                        <input type="hidden" name="uomID" id="uomID"  value="<?php echo $uomID   ?>">
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4 md-offset-2">
                        <label class="title"><?php echo $this->lang->line('manufacturing_quantity'); ?><!--Qty--></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" name="Qty" id="Qty" class="form-control"
                                   placeholder="Qty" value="<?php echo $Qty ?>" required readonly>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4 md-offset-2">
                        <label class="title"><?php echo $this->lang->line('manufacturing_currency'); ?><!--Currency--></label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'BOM\')" required disabled'); ?>
                    </div>

                </div>
            </div>
            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
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
                                    <th style="width: 10%"><?php echo $this->lang->line('manufacturing_standard_loss'); ?><!--Standard Loss--></th>
                                    <th style="width: 12%"><?php echo $this->lang->line('manufacturing_material_change'); ?><!--Material Charge--></th>
                                    <th style="width: 5%">

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
                                    <th style="min-width: 12%">Sub Department</th>
                                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate'); ?><!--Unit Rate--></th>
                                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours'); ?><!--Total Hours--></th>
                                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value'); ?><!--Total Value--></th>
                                    <th style="min-width: 5%">

                                    </th>
                                </tr>
                                </thead>
                                <tbody id="labour_task_body">


                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="5">
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
                    <h2><?php echo $this->lang->line('manufacturing_overhead_cost'); ?><!--OVERHEAD COST--></h2>
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
                                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_overhead_cost'); ?><!--Overhead Cost--></th>

                                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_of_measure_short'); ?><!--UoM--></th>

                                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_unit_rate'); ?><!--Unit Rate--></th>
                                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_hours'); ?><!--Total Hours--></th>
                                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_total_value'); ?><!--Total Values--></th>
                                </tr>
                                </thead>
                                <tbody id="third_party_service_body">

                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <div class="text-right">Third Party Service Total</div>
                                    </td>
                                    <td>
                                        <div id="tot_tps_hourRate"
                                             style="text-align: right">0.00
                                        </div>
                                    </td>
                                    <td>
                                        <div id="tot_tps_totalHours"
                                             style="text-align: right">0.00
                                        </div>
                                    </td>
                                    <td>
                                        <div id="tot_tps_totalValue"
                                             style="text-align: right">0.00
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
        <!-- Machine Cost -->
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('manufacturing_machine'); ?><!--MACHINE--></h2>
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

    </div>
</div>
    <?php
    $data["documentID"] = 'BOM';
    $this->load->view('system/mfq/mfq_common_js', $data);
    ?>
<script>
    var bomMasterID=<?php echo $bomMasterID ?>;
    $(document).ready(function () {
        loadBoMDetailTable(bomMasterID);
    });
    function loadBoMDetailTable() {
        if (bomMasterID > 0) {

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
                                echo str_replace(array("\n", '<select'), array('', '<select id="ct_\'+bomMaterialConsumptionID+\'"'), form_dropdown('costingType[]', array(1 => 'Average', 2 => 'PO', 3 => 'Manual'), 1, 'onchange="costingType(this)" class="form-control costingType"  disabled '))
                                ?>';
                            $('#material_consumption_body').append('<tr id="rowMC_' + v.bomMaterialConsumptionID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="Item ID, Item Description..." value="' + v.itemName + '" id="f_search_' + search_id + '" readonly> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" value="' + v.mfqItemID + '" readonly> <input type="hidden" class="form-control bomMaterialConsumptionID" name="bomMaterialConsumptionID[]" value="' + v.bomMaterialConsumptionID + '" readonly> </td> <td><input type="text" name="partNo" class="form-control" value="' + v.partNo + '" readonly> </td> <td><input type="text" value="' + v.defaultUnitOfMeasure + '" class="form-control uom" disabled/></td> <td><input type="text" name="qtyUsed[]" value="' + v.qtyUsed + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();" readonly> </td> <td>' + costingType + '</td> <td><input type="text" name="unitCost[]" value="' + v.unitCost + '" placeholder="0.00" onkeyup="cal_bom_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCost, 2) + '</span> <input type="hidden" name="materialCost[]" value="' + v.materialCost + '" class="materialCost" readonly> </td> <td style="width: 100px"> <div class="input-group"> <input type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="' + v.markUp + '" onkeyup="cal_bom_material_total(this)" onfocus="this.select();" readonly> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.materialCharge, 2) + '</span> <input type="hidden" name="materialCharge[]" value="' + v.materialCharge + '" class="materialCharge" readonly> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
                            //initializematerialTypeahead(search_id);
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

                    $('#labour_task_body').html('');
                    if (!$.isEmptyObject(data["labour"])) {
                        $.each(data["labour"], function (k, v) {
                            var bomLabourTaskID = v.bomLabourTaskID;
                            var segment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="lb_\'+search_id5+\'"'), form_dropdown('la_segmentID[]', $segment, 'Each', 'onchange ="fetch_related_subsegment(this,this.value,\'+v.subsegmentID+\')" class="form-control segmentID"  disabled'))
                                ?>';
                            var subsegment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="lbsub_\'+search_id5+\'"'), form_dropdown('la_subsegmentID[]',array(''=>'Select a Sub Segment'),'', 'class="form-control subsegmentID"  disabled'))
                                ?>';

                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="lbu_\'+search_id5+\'"'), form_dropdown('la_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  disabled')) ?>';
                            $('#labour_task_body').append('<tr id="rowLB_' + v.bomLabourTaskID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control l_search" name="search[]" placeholder="Labour" id="l_search_' + search_id5 + '" value="' + v.description + '" readonly> <input type="hidden" class="form-control labourTask" name="labourTask[]" value="' + v.labourTask + '" readonly> <input type="hidden" class="form-control" name="bomLabourTaskID[]" value="' + v.bomLabourTaskID + '" readonly> </td> <td><input type="text" name="la_activityCode[]"  class="form-control" value="' + v.activityCode + '" readonly></td> <td>' + uom + '</td> <td>' + segment + '</td><td>'+subsegment+'</td><td><input type="text" name="la_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_hourRate" onfocus="this.select();" readonly> </td> <td><input type="text" name="la_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_totalHours totalHours" onfocus="this.select();" value="' + v.totalHours + '" readonly> </td> <td>&nbsp;<span class="lb_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, 2) + '</span> <input type="hidden" name="la_totalValue[]" class="lb_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');

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
                                echo str_replace(array("\n", '<select'), array('', '<select id="oh_\'+search_id2+\'"'), form_dropdown('oh_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  disabled'))
                                ?>';
                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ohu_\'+search_id2+\'"'), form_dropdown('oh_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  disabled')) ?>';
                            $('#over_head_body').append('<tr id="rowOC_' + v.bomOverheadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_' + search_id2 + '" value="' + v.description + '" readonly> <input type="hidden" class="form-control overheadID" name="overheadID[]" value="' + v.overheadID + '" readonly> <input type="hidden" class="form-control bomOverheadID" name="bomOverheadID[]" value="' + v.bomOverheadID + '" readonly> </td> <td><input type="text" name="oh_activityCode[]"  class="form-control" value="' + v.activityCode + '" readonly></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="oh_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();" readonly> </td> <td><input type="text" name="oh_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, 2) + '</span> <input type="hidden" name="oh_totalValue[]" class="oh_totalValue" value="' + v.totalValue + '"> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
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
                            $('#third_party_service_body').append('<tr id="rowTPS_' + v.bomOverheadID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control tps_search" name="tps_search[]" placeholder="Third Party Service" id="tps_search_' + search_id2 + '" value="' + v.description + '" readonly> <input type="hidden" class="form-control tpsID" name="tpsID[]" value="' + v.overheadID + '" readonly> <input type="hidden" class="form-control bomtpsID" name="bomtpsID[]" value="' + v.bomOverheadID + '" readonly> </td><td>' + uom + '</td> <td><input type="text" name="tps_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_hourRate" onfocus="this.select();" readonly> </td> <td><input type="text" name="tps_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_tps_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="tps_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, 2) + '</span> <input type="hidden" name="tps_totalValue[]" class="tps_totalValue" value="' + v.totalValue + '"> </td></tr>');
                            // $('#oh_' + search_id2).val(v.segmentID);
                            $('#tpsu_' + search_id2).val(v.uomID);
                            initializethirdpartyserviceTypeahead(search_id2);
                            search_id2++;
                        });
                        calculateThirdPartyServiceCostTotal();
                    } else {
                        init_bom_third_party_service_cost();
                    }

                    $('#machine_body').html('');
                    if (!$.isEmptyObject(data["machine"])) {
                        $.each(data["machine"], function (k, v) {
                            var segment = '<?php
                                echo str_replace(array("\n", '<select'), array('', '<select id="mc_\'+search_id3+\'"'), form_dropdown('mc_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  disabled'))
                                ?>';
                            var uom = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="mcu_\'+search_id3+\'"'), form_dropdown('mc_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  disabled')) ?>';
                            $('#machine_body').append('<tr id="rowMC_' + v.bomMachineID + '"> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control mc_search" name="search[]" placeholder="Machine" id="mc_search_' + search_id3 + '" value="' + v.assetDescription + '" readonly> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]" value="' + v.mfq_faID + '" readonly> <input type="hidden" class="form-control bomMachineID" name="bomMachineID[]" value="' + v.bomMachineID + '" readonly> </td> <td><input type="text" name="mc_activityCode[]"  class="form-control" value="' + v.activityCode + '" readonly></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="mc_hourlyRate[]" value="' + v.hourlyRate + '" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_hourRate" onfocus="this.select();" readonly> </td> <td><input type="text" name="mc_totalHours[]" value="' + v.totalHours + '" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="mc_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">' + commaSeparateNumber(v.totalValue, 2) + '</span> <input type="hidden" name="mc_totalValue[]" class="mc_totalValue" value="' + v.totalValue + '" readonly> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
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

            var tot_materialCharge_value = parseFloat($('td', this).eq(8).find('input').val());
            if (!isNaN(tot_materialCharge_value)) {
                tot_materialCharge += tot_materialCharge_value;
            }
        });

        $("#tot_qtyUsed").text(commaSeparateNumber(tot_qtyUsed, 2));
        $("#tot_unitCost").text(commaSeparateNumber(tot_unitCost, 2));
        $("#tot_materialCost").text(commaSeparateNumber(tot_materialCost, 2));
        $("#tot_materialCharge").text(commaSeparateNumber(tot_materialCharge, 2));
    }

    function init_materialConsupmtionForm() {
        var costingType = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="ct_1"'), form_dropdown('costingType[]', array(1 => 'Average', 2 => "PO", 3 => "Manual"), 1, 'onchange="costingType(this)" class="form-control costingType"  disabled'))
            ?>';
        $('#material_consumption_body').html('');
        $('#material_consumption_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search" name="search[]" placeholder="Item ID, Item Description..." id="f_search_1" readonly> <input type="hidden" class="form-control mfqItemID" name="mfqItemID[]" readonly> <input type="hidden" class="form-control jcMaterialConsumptionID" name="jcMaterialConsumptionID[]" readonly> </td> <td><input type="text" name="partNo" class="form-control" readonly> </td> <td><input type="text" name="uom[]" id="uom" class="form-control uom" readonly value="" readonly></td> <td><input type="text" name="qtyUsed[]" value="0.00" placeholder="0.00" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number qtyUsed" onfocus="this.select();" readonly> </td> <td>' + costingType + '</td> <td><input type="text" name="unitCost[]" value="0.00" placeholder="0.00" onkeyup="cal_material_total(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number unitCost" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="materialCostTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="materialCost[]" value="0" class="materialCost" readonly> </td> <td style="width: 100px"> <div class="input-group"> <input type="text" name="markUp[]" placeholder="0" class="form-control number markupPrc" value="0" onkeyup="cal_material_total(this)" onfocus="this.select();" readonly> <span class="input-group-addon">%</span> </div> </td> <td>&nbsp;<span class="materialChargeTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="materialCharge[]" value="0" class="materialCharge" readonly> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        setTimeout(function () {
            initializematerialTypeahead(1);
        }, 500);
    }

    function initializematerialTypeahead(id) {
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MFQ_Job_Card/fetch_material/',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.mfqItemID').val(suggestion.mfqItemID);
                    $('#f_search_' + id).closest('tr').find('.partNo').val(suggestion.partNo);
                    $('#f_search_' + id).closest('tr').find('.uom').val(suggestion.uom);
                    $('#f_search_' + id).closest('tr').find('.unitCost').val(suggestion.companyLocalWacAmount);
                    $('#f_search_' + id).closest('tr').find('.qtyUsed').trigger('keyup');
                }, 200);
                //fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
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

        $("#tot_lb_hourRate").text(commaSeparateNumber(tot_hourRate, 2));
        $("#tot_lb_totalHours").text(commaSeparateNumber(tot_totalHours, 2));
        $("#tot_lb_totalValue").text(commaSeparateNumber(tot_totalValue, 2));
    }

    function init_bom_labour_task() {
        var segment = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="lb_1"'), form_dropdown('la_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  disabled'))
            ?>';
        var subsegment = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="lbsub_1"'), form_dropdown('la_subsegmentID[]',array(''=>'Select a Sub Segment'),'', 'class="form-control subsegmentID"  disabled'))
            ?>';
        var uom = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="lbu_1"'), form_dropdown('la_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  disabled'))
            ?>';
        $('#labour_task_body').html('');
        $('#labour_task_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control l_search" name="search[]" placeholder="Labour" id="l_search_1" readonly> <input type="hidden" class="form-control labourTask" name="labourTask[]" readonly> <input type="hidden" class="form-control jcLabourTaskID" name="jcLabourTaskID[]" readonly> </td> <td><input type="text" name="la_activityCode[]"  class="form-control" readonly></td><td>' + uom + '</td> <td>' + segment + '</td> <td>'+subsegment+'</td> <td><input type="text" name="la_hourlyRate[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_hourRate" onfocus="this.select();" readonly> </td> <td><input type="text" name="la_totalHours[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_labour_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number lb_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="lb_totalValueTxt pull-right" style="font-size: 13px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="la_totalValue[]" class="lb_totalValue" readonly> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        setTimeout(function () {
            initializelabourtaskTypeahead(1);
            search_id5++;
        }, 500);
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

    function init_bom_overhead_cost() {
        var segment = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="oh_1"'), form_dropdown('oh_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  disabled'))
            ?>';
        var uom = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="ohu_1"'), form_dropdown('oh_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  disabled'))
            ?>';
        $('#over_head_body').html('');
        $('#over_head_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control o_search" name="search[]" placeholder="Overhead" id="o_search_1" readonly> <input type="hidden" class="form-control overheadID" name="overheadID[]"> <input type="hidden" class="form-control bomOverheadID" name="bomOverheadID[]" readonly> </td> <td><input type="text" name="oh_activityCode[]"  class="form-control" readonly></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="oh_hourlyRate[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_hourRate" onfocus="this.select();" readonly> </td> <td><input type="text" name="oh_totalHours[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_overhead_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number oh_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="oh_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="oh_totalValue[]" class="oh_totalValue" readonly> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        setTimeout(function () {
            initializeoverheadTypeahead(1);
        }, 500);
    }

    function init_bom_third_party_service_cost() {
        var uom = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="tpsu_1"'), form_dropdown('tps_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  required'))
            ?>';
        $('#third_party_service_body').html('');
        $('#third_party_service_body').append('<tr>' +
            ' <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control tps_search" name="tps_search[]" placeholder="Third Party Service" id="tps_search_1" readonly> ' +
            '<input type="hidden" class="form-control tpsID" name="tpsID[]"> <input type="hidden" class="form-control bomtpsID" name="bomtpsID[]" readonly> ' +
            '</td> ' + uom + '</td>' +
            ' <td><input type="text" name="tps_hourlyRate[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_third_party_service_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_hourRate" onfocus="this.select();" readonly>' +
            ' </td> <td><input type="text" name="tps_totalHours[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_third_party_service_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number tps_totalHours totalHours" onfocus="this.select();" readonly> ' +
            '</td> <td>&nbsp;<span class="tps_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="tps_totalValue[]" class="tps_totalValue" readonly> </td> ' +
            '</tr>');
        setTimeout(function () {
            initializeoverheadTypeahead(1);
        }, 500);
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

        $("#tot_mc_hourRate").text(commaSeparateNumber(tot_hourRate, 2));
        $("#tot_mc_totalHours").text(commaSeparateNumber(tot_totalHours, 2));
        $("#tot_mc_totalValue").text(commaSeparateNumber(tot_totalValue, 2));
    }

    function init_bom_machine_cost() {
        var segment = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="mc_1"'), form_dropdown('mc_segmentID[]', $segment, 'Each', 'onchange ="getSegmentHours(this)" class="form-control segmentID"  disabled'))
            ?>';
        var uom = '<?php
            echo str_replace(array("\n", '<select'), array('', '<select id="mcu_1"'), form_dropdown('mc_uomID[]', $umo_arr2, 'Each', 'class="form-control uomID"  disabled'))
            ?>';
        $('#machine_body').html('');
        $('#machine_body').append('<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control mc_search" name="search[]" placeholder="Machine" id="mc_search_1" readonly> <input type="hidden" class="form-control mfq_faID" name="mfq_faID[]" readonly> <input type="hidden" class="form-control bomMachineID" name="bomMachineID[]" readonly> </td> <td><input type="text" name="mc_activityCode[]"  class="form-control" readonly></td><td>' + uom + '</td> <td>' + segment + '</td> <td><input type="text" name="mc_hourlyRate[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_hourRate" onfocus="this.select();" readonly> </td> <td><input type="text" name="mc_totalHours[]" value="0.00" placeholder="0.00" onkeyup="cal_bom_machine_tot_value(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number mc_totalHours totalHours" onfocus="this.select();" readonly> </td> <td>&nbsp;<span class="mc_totalValueTxt pull-right" style="font-size: 12px;text-align: right;margin-top: 8%;">0.00</span> <input type="hidden" name="mc_totalValue[]" class="mc_totalValue" readonly> </td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        setTimeout(function () {
            initializemachinecostTypeahead(1);
        }, 500);
    }

    function calculateTotalCost() {
        var totalMateialConsumption = parseFloat($('#tot_materialCharge').text().replace(/,/g, ''));
        var totalLabourTask = parseFloat($('#tot_lb_totalValue').text().replace(/,/g, ''));
        var totalOverhead = parseFloat($('#tot_oh_totalValue').text().replace(/,/g, ''));
        var totalMachine = parseFloat($('#tot_mc_totalValue').text().replace(/,/g, ''));
        var totalCost = (totalMateialConsumption + totalLabourTask + totalOverhead + totalMachine);

        $("#totalCost").text(commaSeparateNumber((totalMateialConsumption + totalLabourTask + totalOverhead + totalMachine), 2));
        if ($('#Qty').val() > 0) {
            $("#costperunit").text(commaSeparateNumber((totalCost / $('#Qty').val()), 2));
        } else {
            $("#costperunit").text(0);
        }
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

        $("#tot_oh_hourRate").text(commaSeparateNumber(tot_hourRate, 2));
        $("#tot_oh_totalHours").text(commaSeparateNumber(tot_totalHours, 2));
        $("#tot_oh_totalValue").text(commaSeparateNumber(tot_totalValue, 2));
    }

    function calculateThirdPartyServiceCostTotal() {
        var tot_hourRate = 0;
        var tot_totalHours = 0;
        var tot_totalValue = 0;
        $('#third_party_service_body tr').each(function () {
            var tot_hourRate_value = parseFloat($('td', this).eq(2).find('input').val());
            if (!isNaN(tot_hourRate_value)) {
                tot_hourRate += tot_hourRate_value;
            }

            var tot_totalHours_value = parseFloat($('td', this).eq(3).find('input').val());
            if (!isNaN(tot_totalHours_value)) {
                tot_totalHours += tot_totalHours_value;
            }

            var tot_totalValue_value = parseFloat($('td', this).eq(4).find('input').val());
            if (!isNaN(tot_totalValue_value)) {
                tot_totalValue += tot_totalValue_value;
            }
        });
        $("#tot_tps_hourRate").text(commaSeparateNumber(tot_hourRate, 2));
        $("#tot_tps_totalHours").text(commaSeparateNumber(tot_totalHours, 2));
        $("#tot_tps_totalValue").text(commaSeparateNumber(tot_totalValue, 2));
    }
    function fetch_related_subsegment(element,value,selectedvalue) {
        if(value){
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
</script>
<?php } else{ ?>
    <div class="text-danger" >
        <div class="text-center"><b>Bill of Material not configured</b></div>
    </div>
<?php } ?>