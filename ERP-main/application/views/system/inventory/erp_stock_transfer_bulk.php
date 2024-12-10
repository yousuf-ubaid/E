<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('inventory_helper');

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$umo_arr = array('' => 'Select UOM');//all_umo_drop();
$location_arr_from = all_delivery_location_drop_active(false);
$location_arr_to = all_delivery_location_drop_active(false); 
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$projectExist = project_is_exist();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$sub_category_arr = all_sub_category_drop();
 $pID = $this->input->post('page_id');
if($pID != '') {
    $Documentid = 'STB';
    $warehouseidcurrentdoc = all_warehouse_drop_isactive_inactive($pID,$Documentid);
    if(!empty($warehouseidcurrentdoc) && $warehouseidcurrentdoc['isActive'] == 0)
    {
        $location_arr_from[trim($warehouseidcurrentdoc['wareHouseAutoID'] ?? '')] = trim($warehouseidcurrentdoc['wareHouseCode'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseLocation'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseDescription'] ?? '');
       
    }
    $towarehouseidcurrentdocs = all_warehouse_drop_isactive_inactive_multiple($pID,$Documentid);
    if(!empty($towarehouseidcurrentdocs) )
    {
        foreach($towarehouseidcurrentdocs as $towarehouseidcurrentdoc){
            $location_arr_to[trim($towarehouseidcurrentdoc['wareHouseAutoID'] ?? '')] = trim($towarehouseidcurrentdoc['wareHouseCode'] ?? '') . ' | ' . trim($towarehouseidcurrentdoc['wareHouseLocation'] ?? '') . ' | ' . trim($towarehouseidcurrentdoc['wareHouseDescription'] ?? '');
        }
        
    }
} 
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one');?> - <?php echo $this->lang->line('transaction_bulk_transfer_header');?> </a><!--Step 1--><!--Bulk Transfer Header-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_detail()" data-toggle="tab"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two');?> - <?php echo $this->lang->line('transaction_bulk_transfer_detail');?></a><!--Step 2--><!-- Bulk Transfer Detail-->
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation_bulk_transfer();" data-toggle="tab"><?php echo $this->lang->line('transaction_goods_received_voucher_step_three');?> - <?php echo $this->lang->line('transaction_bulk_transfer_confirmation');?></a><!--Step 3--><!--Bulk Transfer Confirmation-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="stock_transfer_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="itemType"><?php echo $this->lang->line('transaction_stock_transfer_transfer_type');?> <?php required_mark(); ?></label><!--Item Type-->
                <?php echo form_dropdown('transferType', array('' =>  $this->lang->line('common_select_type')/*'Select Type'*/, 'standard' => $this->lang->line('transaction_direct'), 'materialRequest' => $this->lang->line('transaction_material_request')), 'standard', 'class="form-control select2" id="transferType" required disabled'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="itemType"><?php echo $this->lang->line('transaction_item_type');?> <?php required_mark(); ?></label><!--Item Type-->
                <?php echo form_dropdown('itemType', array('' =>  $this->lang->line('common_select_type')/*'Select Type'*/, 'Inventory' => $this->lang->line('transaction_inventory')/*'Inventory'*/, 'Non Inventory' => $this->lang->line('transaction_non_inventory')/*'Non Inventory'*/), 'Inventory', 'class="form-control select2" id="itemType" required disabled'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('transaction_primary_segment');?> <?php required_mark(); ?></label><!--Primary Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
            </div>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_transfer_date');?> <?php required_mark(); ?></label><!--Transfer Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="tranferDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="tranferDate"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_from_location');?> <?php required_mark(); ?></label><!--From Location-->
                <?php echo form_dropdown('form_location', $location_arr_from, '', 'class="form-control select2" id="form_location" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_to_location');?> <?php required_mark(); ?></label><!--To Location-->
                <?php echo form_dropdown('to_location[]', $location_arr_to, '', 'class="form-control" id="to_location" multiple="multiple" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4" style="margin-top: 10px">
                <label for="itemType">Receipt Type <?php required_mark(); ?></label><!--Item Type-->
                <?php echo form_dropdown('receiptType', array('' =>  'Select Receipt Type', '1' => 'Manual Receipt', '2' => 'Automatic Receipt'), '', 'class="form-control select2" id="receiptType" required'); ?>
            </div>
            <?php
            if($financeyearperiodYN==1){
                ?>
                <div class="form-group col-sm-4" style="margin-top: 10px">
                    <label for="financeyear"><?php echo $this->lang->line('transaction_common_financial_year');?> <?php required_mark(); ?></label><!--Financial Year-->
                    <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                </div>
                <div class="form-group col-sm-4" style="margin-top: 10px">
                    <label for="financeyear_period"><?php echo $this->lang->line('transaction_common_financial_period');?> <?php required_mark(); ?></label><!--Financial Period-->
                    <?php echo form_dropdown('financeyear_period', array('' => 'Finance Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                </div>
        </div>
        <div class="row">
            <?php } ?>
            <div class="form-group col-sm-4" style="margin-top: 10px">
                <label><?php echo $this->lang->line('transaction_common_referenc_no');?></label><!--Reference No-->
                <input type="text" class="form-control " id="referenceNo" name="referenceNo">
            </div>
            <div class="form-group col-sm-4" style="margin-top: 10px">
                <label><?php echo $this->lang->line('transaction_common_narration');?> </label><!--Narration-->
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('transaction_common_add_item_detail');?> </h4></div><!--Add Item Detail-->
            <div class="col-md-4">
                <input name="itemSearch" id="itemSearch" placeholder="search item...." style="width: 300px" onkeyup="fetch_detail()">
                <button type="button" onclick="bulk_item_pull_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus "></i> <?php echo $this->lang->line('common_add_item');?>
                </button><!--Add Item-->
            </div>
        </div>
        <div id="bulk_transfer_details"></div>
        <br/>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick=""><?php echo $this->lang->line('common_previous');?> </button>
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div><br/>
        <hr>
        <div id="conform_body_attachement">
            <br>
            <h4 class="modal-title" id="stockTransfer_attachment_label"><?php echo $this->lang->line('transaction_goods_received_voucher_modal_title');?> </h4><!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name');?></th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description');?></th><!--Description-->
                        <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_action');?></th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="stockTransfer_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?></td><!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?></button><!--Previous-->
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?></button><!--Save as Draft-->
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?></button><!--Confirm-->
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="" data-backdrop="static"
     id="bulkTransferPullItem">
    <div class="modal-dialog modal-lg" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('manufacturing_item_from_erp'); ?><!--Items from ERP--> </h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label>Sub Category</label>
                            <?php echo form_dropdown('subCategoryID', $sub_category_arr, 'Each', 'class="form-control select2" id="subCategoryID" onchange="LoadSubSubCategory()"'); ?>
                        </div>
                        <div class="form-group col-sm-3">
                            <label>Sub Sub Category</label>
                            <select name="subsubcategoryID" id="subsubcategoryID" class="form-control searchbox">
                                <option value=""><?php echo $this->lang->line('transaction_select_category'); ?> </option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="item_table_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 12%"><?php echo $this->lang->line('transaction_sub_category'); ?><!--Sub Category--></th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code'); ?><!--Item Code--></th>
                                <th style="min-width: 25%"><?php echo $this->lang->line('transaction_common_item_description'); ?><!--Item Description--></th>
                                <th style="min-width: 10%"><abbr title="Secondary Code"><?php echo $this->lang->line('erp_item_master_secondary_code'); ?><!--Secondary Code--></abbr></th>
                                <th style="min-width: 10%"><abbr title="Current Stock"><?php echo $this->lang->line('transaction_current_stock'); ?> <!--Current Stock--></abbr></th>
                                <th style="min-width: 5%; text-align: center !important;">
                                    <button type="button" data-text="Add" onclick="addItem_bulk_transfer()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i><?php echo $this->lang->line('common_add_item'); ?> <!--Add Items-->
                                    </button>
                                    <input id="isActive" type="checkbox" data-caption="" class="columnSelected addItemz" name="isActive"  onclick="oTable2.draw()">
                                </th>
                            </tr>
                            </thead>
                        </table>

                    </div>
                </div>


            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="insufficient_bulk_item_modal"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Insufficient Items</h4>
            </div>

            <form class="form-horizontal" id="insufficient_form">
                <div class="modal-body">
                    <div id="insufficient_item">
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>Current Warehouse</th>
                                <th>Current Stock</th>
                                <th>Updated Stock</th>
                            </tr>
                            </thead>
                            <tbody id="insufficient_item_body">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var bulkTransferAutoID;
    var search_id = 1;
    var type;
    var stockTransferDetailsID;
    var transferType;
    var projectID;
    var selectedItemsSync = [];
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/inventory/stock_transfer_management_bulk', bulkTransferAutoID, 'Stock Transfer');
        });
        $('.select2').select2();
        number_validation();
        type = 'Inventory';
        bulkTransferAutoID = null;
        stockTransferDetailsID = null;
        projectID = null;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#stock_transfer_form').bootstrapValidator('revalidateField', 'tranferDate');
        });

        $('.addItemz input').iCheck({
            checkboxClass: 'icheckbox_square_relative-purple',
            radioClass: 'iradio_square_relative-purple',
            increaseArea: '20%'
        });

        $('#to_location').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '420px',
            maxHeight: '30px'
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            bulkTransferAutoID = p_id;
            load_bulk_transfer_header();
            $("#a_link").attr("href", "<?php echo site_url('Inventory/load_bulk_transfer_conformation'); ?>/" + bulkTransferAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_bulk_transfer'); ?>/" + bulkTransferAutoID + '/STB');
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
        }

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#stock_transfer_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                transferType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_transfer_type_is_required');?>.'}}},/*Transfer Type is required*/
                tranferDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_grv_date_is_required');?>.'}}},/*GRV Date is required*/
                form_location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_from_location_is_required');?>.'}}},/*From Location is required*/
                itemType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_item_type_is_required');?>.'}}},/*Item Type is required*/
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_primary_segment_is_required');?>.'}}},
                receiptType: {validators: {notEmpty: {message: 'Receipt Type is Required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $('#tranferDate').prop("disabled", false);
            $('#receiptType').prop("disabled", false);
            $('#segment').prop("disabled", false);
            $("#form_location").prop("disabled", false);
            $('#to_location').multiselect2("enable");
            $("#itemType").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'stockTransferAutoID', 'value': bulkTransferAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'form_location_dec', 'value': $('#form_location option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_bulk_transfer_header'); ?>",
                beforeSend: function () {
                    startLoad();
                    $("#itemType").prop("disabled", true);
                },
                success: function (data) {
                    refreshNotifications(true);
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        bulkTransferAutoID = data['last_id'];
                        transferType = $('#transferType').val();
                        fetch_detail();
                        $("#a_link").attr("href", "<?php echo site_url('Inventory/load_bulk_transfer_conformation'); ?>/" + bulkTransferAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_bulk_transfer'); ?>/" + bulkTransferAutoID + '/STB');
                        $('[href=#step2]').tab('show');
                    }
                    stopLoad();
                    type = $('#itemType').val();
                    /*$('#search').typeahead('destroy');
                     initializeitemTypeahead(type);*/
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        sync_bulk_item_table();

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

        $("#subsubcategoryID").change(function () {
            oTable2.draw();
        });

        Inputmask().mask(document.querySelectorAll("input"));
    });

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Finance Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_bulk_transfer_header() {
        if (bulkTransferAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'stockTransferAutoID': bulkTransferAutoID},
                url: "<?php echo site_url('Inventory/load_bulk_transfer_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        bulkTransferAutoID = data['stockTransferAutoID'];
                        transferType = data['transferType'];
                        $('#tranferDate').val(data['transferDate']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#form_location").val(data['from_wareHouseAutoID']).change();
                        $("#receiptType").val(data['receiptType']).change();
                        // $('#to_location').val(data['toWarehouse']).change();
                        $('#narration').val(data['comment']);
                        $('#referenceNo').val(data['referenceNo']);
                        $('#transferType').val(data['transferType']).change();
                        $('#itemType').val(data['itemType']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        type = data['itemType'];

                        if (!jQuery.isEmptyObject(data['toWarehouse'])) {
                            $('#to_location').multiselect2('select', data['toWarehouse']).multiselect2("refresh");
                        }

                        fetch_detail();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function fetch_detail() {
        var itemSearch = $('#itemSearch').val();
        if (bulkTransferAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'stockTransferAutoID': bulkTransferAutoID, 'itemSearch': itemSearch},
                url: "<?php echo site_url('Inventory/fetch_bulkTransfer_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#bulk_transfer_details').html(data);
                    validateDetailExist();
                    stopLoad();<!--Total-->

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
    }

    function validateDetailExist() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'stockTransferAutoID': bulkTransferAutoID},
            url: "<?php echo site_url('Inventory/bulkTransfer_details'); ?>",
            success: function (data) {
                if ((data)) {
                    $("#form_location").prop("disabled", true);
                   $('#tranferDate').prop("disabled", true);
                   $('#receiptType').prop("disabled", true);
                    $('#segment').prop("disabled", true);
                    $("#to_location").multiselect2("disable");
                } else {
                    $('#tranferDate').prop("disabled", false);
                    $('#receiptType').prop("disabled", false);
                    $('#form_location').prop("disabled", false);
                    $('#segment').prop("disabled", false);
                    $('#to_location').multiselect2("enable");
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function bulk_item_pull_modal()
    {
        oTable2.draw();
        $('#isActive').iCheck('uncheck');
        $("#bulkTransferPullItem").modal({backdrop: "static"});
    }

    function sync_bulk_item_table() {
        oTable2 = $('#item_table_sync').DataTable({
            "pageLength": 100,
            lengthMenu: [10, 25, 50, 100, 250, 500, 1000],
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Inventory/fetch_sync_item'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {

                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');

                        // $("#selectItem_" + value).prop("checked", true);
                    });
                }
                if ($('#isActive').is(":checked")){
                    $('.item-iCheck').iCheck('check');
                    selectedItemsSync = [];
                    $('.columnSelected').each(function () {
                        var id = $(this).val();
                        if(id != 'on'){
                            selectedItemsSync.push(id);
                        }
                    });

                } else{
                    $('.item-iCheck').iCheck('uncheck');
                    selectedItemsSync = [];
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });
            },
            "aoColumns": [
                {"mData": "itemAutoID"},
                {"mData": "SubCategoryDescription"},
                {"mData": "itemSystemCode"},
                {"mData": "itemDescription"},
                {"mData": "seconeryItemCode"},
                {"mData": "CurrentStock"},
                {"mData": "edit"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "subcategory", "value": $("#subCategoryID").val()});
                aoData.push({"name": "subsubcategoryID", "value": $("#subsubcategoryID").val()});
                aoData.push({"name": "warehouseAutoID", "value": $("#form_location").val()});
                aoData.push({"name": "stockTransferAutoID", "value": bulkTransferAutoID});
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

    function ItemsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function addItem_bulk_transfer()
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Inventory/add_item_bulk_transfer"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync, 'stockTransferAutoID': bulkTransferAutoID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data['error'], data['message']);
                if (data['error'] == 's') {
                    $('#isActive').iCheck('uncheck');
                    stockTransferDetailsID = null;
                    oTable2.draw();
                    selectedItemsSync = [];
                    setTimeout(function () {
                        fetch_detail();
                    }, 300);
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function delete_bulk_transfer_details(stockTransferAutoID, itemAutoID)
    {
        swal(
            {
                title: "<?php echo $this->lang->line('common_are_you_sure') ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete') ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete') ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'stockTransferAutoID' : stockTransferAutoID, 'itemAutoID' : itemAutoID},
                    url: "<?php echo site_url('Inventory/delete_bulk_transfer_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_detail();
                        }
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        );
    }

    function load_conformation_bulk_transfer()
    {
        if (bulkTransferAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'stockTransferAutoID': bulkTransferAutoID, 'html': true},
                url: "<?php echo site_url('Inventory/load_bulk_transfer_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Inventory/load_bulk_transfer_conformation'); ?>/" + bulkTransferAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_bulk_transfer'); ?>/" + bulkTransferAutoID + '/STB');
                    attachment_modal_bulkTransfer(bulkTransferAutoID, "<?php echo $this->lang->line('transaction_bulk_transfer');?>", "STB");/*Stock Transfer*/
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }
    }

    function attachment_modal_bulkTransfer(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#stockTransfer_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#stockTransfer_attachment').empty();
                    $('#stockTransfer_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function save_draft() {
        if (bulkTransferAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/inventory/stock_transfer_management_bulk', 'Test', "<?php echo $this->lang->line('transaction_bulk_transfer'); ?>");
                });
        }
    }

    function confirmation() {
        if (bulkTransferAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'stockTransferAutoID': bulkTransferAutoID},
                        url: "<?php echo site_url('Inventory/bulk_transfer_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                                if(data['message']=='Some Item quantities are not sufficient to confirm this transaction.'){
                                    if(!$.isEmptyObject(data['itemDetails'])){
                                        $('#insufficient_item_body').html('');
                                        $.each(data['itemDetails'], function (item, value) {
                                            $('#insufficient_item_body').append('<tr><td>' + value['itemSystemCode'] + '</td> <td>' + value['itemDescription'] + '</td> <td>' + value['warehouse'] + '</td> <td>' + value['currentStock'] + '</td> <td>' + value['updatedStock'] + '</td></tr>')
                                        });
                                        $("#insufficient_bulk_item_modal").modal({backdrop: "static"});
                                    }
                                }
                            }else if(data['error']==2){
                                myAlert('w',data['message']);
                            }
                            else {
                                myAlert('s', data['message']);
                                fetchPage('system/inventory/stock_transfer_management_bulk', 'Test', "<?php echo $this->lang->line('transaction_bulk_transfer'); ?>");
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function LoadSubSubCategory() {
        $('#subsubcategoryID').val("");
        //$('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        load_itemMaster_subsubCategory();
        oTable2.draw();
    }

    function load_itemMaster_subsubCategory() {
        $('#subsubcategoryID').val("");
        $('#subsubcategoryID option').remove();
        var subsubid = $('#subCategoryID').val();
        if(subsubid) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
                dataType: 'json',
                data: {'subsubid': subsubid},
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#subsubcategoryID').empty();
                        var mySelect = $('#subsubcategoryID');
                        mySelect.append($('<option></option>').val('').html('Select Option'));
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                        });
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        } else {
            $('#subsubcategoryID').empty();
            var mySelect = $('#subsubcategoryID');
            mySelect.append($('<option></option>').val('').html('Select Option'));
        }
    }

    function delete_all_bulk_transfer_details(stockTransferAutoID)
    {
        swal({
                title: "Are you sure?",
                text: "You want to Delete All Records!",
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
                    data: {'stockTransferAutoID': stockTransferAutoID},
                    url: "<?php echo site_url('Inventory/delete_all_bulk_transfer_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_detail();
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
</script>
