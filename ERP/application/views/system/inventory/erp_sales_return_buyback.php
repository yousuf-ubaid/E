<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_create_sales_return');
echo head_page($title, false);
/*echo head_page('Create Sales Return', false);*/
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$customer_arr = all_customer_drop();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$uom_arr = array('' => 'Select UOM');
$projectExist = project_is_exist();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1"
       data-toggle="tab"> <?php echo $this->lang->line('sales_markating_transaction_step_one'); ?>
        - <?php echo $this->lang->line('sales_markating_transaction_sales_return_header'); ?> </a><!--Step 1-->
    <!--Sales Return Header-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_detail()"
       data-toggle="tab"><?php echo $this->lang->line('sales_markating_transaction_step_two'); ?>
        - <?php echo $this->lang->line('sales_markating_transaction_sales_return_detail'); ?> </a><!--Sales
        Return Detail--><!--Step 2-->
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();"
       data-toggle="tab"><?php echo $this->lang->line('sales_markating_transaction_step_three'); ?>
        - <?php echo $this->lang->line('sales_markating_transaction_sales_return_confirmation'); ?>
    </a><!--Step 3--><!--Sales Return Confirmation-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="sales_return_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_sales_return_date'); ?><?php required_mark(); ?></label>
                <!--Return Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="returnDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="returnDate"
                           class="form-control" required>
                </div>
            </div>
            <?php
            if($financeyearperiodYN==1){
            ?>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year'); ?><?php required_mark(); ?></label>
                <!--Financial Year-->
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear_period"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period'); ?><?php required_mark(); ?></label>
                <!--Financial Period-->
                <?php echo form_dropdown('financeyear_period', array('' => 'Finance Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
                <?php
            }
            ?>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_sales_reference_no'); ?> </label>
                <!--Reference No-->
                <input type="text" class="form-control " id="referenceNo" name="referenceNo">
            </div>
            <div class="form-group col-sm-4">
                <label for="customerID"><?php echo $this->lang->line('common_customer'); ?><?php required_mark(); ?></label>
                <!--Customer-->
                <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID" onchange="fetch_supplier_currency_by_id(this.value)" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('common_currency'); ?><?php required_mark(); ?></label>
                <!--Currency-->
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation_modal(this.value,\'SLR\',\'\',\'\')" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_sales_warehouse_location'); ?><?php required_mark(); ?></label>
                <!--Warehouse Location-->
                <?php echo form_dropdown('location', $location_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="location" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_narration'); ?></label>
                <!--Narration-->
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary"
                    type="submit"><?php echo $this->lang->line('common_save_and_next'); ?> </button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                            class="fa fa-hand-o-right"></i><?php echo $this->lang->line('sales_markating_transaction_sales_add_item_detail'); ?>
                </h4></div><!--Add Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_document_add_item'); ?>
                </button><!--Add Item-->
            </div>
        </div>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_document_item_details'); ?> </th>
                <!--Item Details-->
                <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?> </th>
                <!--Qty-->
                <th>&nbsp;</th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?>  </th>
                <!--Item Code-->
                <th style="min-width: 30%"><?php echo $this->lang->line('sales_markating_transaction_secondary_item_description'); ?> </th>
                <!--Item Description-->
                <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?></th>
                <!--UOM-->
                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_return'); ?></th>
                <!--return-->
                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_recived'); ?></th>
                <!--Received-->
                <th style="min-width: 10%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="item_table_body">
            <tr class="danger">
                <td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?> </b>
                </td><!--No Records Found-->
            </tr>
            </tbody>
        </table>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick=""><?php echo $this->lang->line('common_previous'); ?></button>
            <!--Previous-->
            <!-- <button class="btn btn-primary next" onclick="load_conformation();" >Save & Next</button> -->
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <!--    <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank" href="<?php /*echo site_url('Double_entry/fetch_double_entry_sales_return/'); */ ?>"><span class="glyphicon glyphicon-random" aria-hidden="true"></span>  &nbsp;&nbsp;&nbsp;Account Review entries
                </a>
                <a class="btn btn-default btn-sm" id="a_link" target="_blank" href="<?php /*echo site_url('Inventory/load_sales_return_conformation/'); */ ?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div><hr>-->
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title"
                id="purchaseReturn_attachment_label"><?php echo $this->lang->line('sales_markating_transaction_model_title'); ?> </h4>
            <!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description'); ?> </th><!--Description-->
                        <th><?php echo $this->lang->line('common_type'); ?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="purchaseReturn_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5"
                            class="text-center"><?php echo $this->lang->line('common_no_attachment_found'); ?> </td>
                        <!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <!--<button class="btn btn-default prev">Previous XX</button>-->
            <button class="btn btn-primary "
                    onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?></button>
            <!--Save as Draft-->
            <button class="btn btn-success submitWizard"
                    onclick="confirmation()"><?php echo $this->lang->line('common_confirm'); ?></button><!--Confirm-->
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="item_detail_modal_edit_stock" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Edit Item Detail</h5>
                <!--Edit Item Detail-->
            </div>
            <div class="modal-body">
                <form role="form" id="edit_item_detail_form" class="form-horizontal">
                    <input type="hidden" id="stockreturndetailid" name="stockreturndetailid">
                    <table class="table table-bordered table-condensed no-color" id="StockAdjustment_detail_edit_table">
                        <thead>
                        <tr>
                            <th style="width:200px;" class="typestock">No of Birds<?php required_mark(); ?></th>
                            <th style="width:200px;" class="typestock">Gross Weight<?php required_mark(); ?></th>
                            <th style="width:200px;" class="typestock">No of Buckets<?php required_mark(); ?></th>
                            <th style="width:200px;" class="typestock">Bucket Size<?php required_mark(); ?></th>
                            <th style="width:200px;" class="typestock">Return Qty<?php required_mark(); ?></th>
                            <th style="width:200px;" class="typestock">Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>

                            <td class="typestock"><input type="text" onfocus="this.select();" name="noOfItems"
                                                         onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                                         class="form-control number input-mini noOfItems" id="edit_noOfItems" required>
                                <input type="hidden" onfocus="this.select();" name="salesprice" placeholder="0.00"
                                       class="form-control number input-mini editsalesprice" id="editsalesprice" required>

                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="grossQty"
                                                         onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                                         class="form-control number input-mini grossQty" id="edit_grossQty" onchange="cal_deduction_netQtyEdit_buyback(this)" required>
                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="noOfUnits"
                                                         onkeyup="checkCurrentStock(this))" placeholder="0.00"
                                                         class="form-control number input-mini noOfUnits" id="edit_noOfUnits" onchange="cal_deduction_netQtyEdit_buyback(this)"  required>
                            </td>

                            <td class="typestock"><!--<input type="text" onfocus="this.select();" name="deduction"
                                                         onkeyup="cal_deduction_netQty(this),cal_deduction_netQtyEdit_buyback()" placeholder="0.00"
                                                         class="form-control number input-mini deduction" id="edit_deduction">-->
                                <?php echo form_dropdown('deductionedit',all_bucketweight_drop(),' ', 'class="form-control deduction input-mini"  id="edit_deduction" onchange="cal_deduction_netQtyEdit_buyback(this)"  required'); ?>
                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="returnqty"
                                                         onkeyup="checkCurrentStock(this))" placeholder="0.00"
                                                         class="form-control number input-mini noOfUnits" id="returnqty" onkeypress="cal_deduction_netQtyEdit_buyback(this)"  required>
                            </td>
                            <td class="typestock">

                                    <input type="text" name="adjustment_Stock" onfocus="this.select();"
                                           class="form-control number" id="adjustment_Stock_edit"  readonly >
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?></button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="Stockreturn_Detail_Update()"><?php echo $this->lang->line('common_save_change'); ?>
                </button><!--Save changes-->
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var salesReturnAutoID;
    var salesreturnDetailsID;
    var documentCurrency;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/invoices/sales_return_buyback', '', 'Sales Return ')
        });
        $('.select2').select2();
        number_validation();
        salesReturnAutoID = null;
        salesreturnDetailsID = null;
        documentCurrency = null;
        //initializeitemTypeahead();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#sales_return_form').bootstrapValidator('revalidateField', 'returnDate');
        });
        var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            salesReturnAutoID = p_id;
            load_sales_return_header();
            $("#a_link").attr("href", "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>/" + salesReturnAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_sales_return_buyback'); ?>/" + salesReturnAutoID + '/SLR');
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            var CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
            currency_validation_modal(CurrencyID, 'PR', '', '');
        }

        var FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        var DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        var periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);
        $('#sales_return_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                customerID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_customer_id_required');?>.'}}}, /*Customer ID is required*/
                //financeyear: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year_is_required');?>.'}}}, /*Financial Year is required*/
                //financeyear_period: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period_is_required');?>.'}}}, /*Financial Period is required*/
                returnDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_return_date_is_required');?>.'}}}, /*Return Date is required*/
                location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_location_is_required');?>.'}}}, /*Location is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_cutomer_currency_is_required');?>.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#transactionCurrencyID").prop("disabled", false);
            $("#customerID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'salesReturnAutoID', 'value': salesReturnAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_sales_return_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        salesReturnAutoID = data['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>/" + salesReturnAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_sales_return_buyback'); ?>/" + salesReturnAutoID + '/SLR');
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#customerID").prop("disabled", true);
                        fetch_detail();
                        $('[href=#step2]').tab('show');
                    }
                    ;
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#item_detail_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                itemAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}}, /*Item is required*/
                search: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}}, /*Item is required*/
                itemSystemCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}}, /*Item is required*/
                itemDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}}, /*Item is required*/
                unitOfMeasure: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_uom_is_required');?>.'}}}, /*Unit Of Measure is required*/
                return_QTY: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_return_qty_is_required');?>.'}}}/*return Quantity is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'salesReturnAutoID', 'value': salesReturnAutoID});
            data.push({'name': 'salesreturnDetailsID', 'value': salesreturnDetailsID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_stock_return_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    salesreturnDetailsID = null;
                    refreshNotifications(true);
                    stopLoad();
                    $('#item_detail_modal').modal('hide');
                    fetch_material_item_detail();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
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
    });

    function load_sales_return_header() {
        if (salesReturnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'salesReturnAutoID': salesReturnAutoID},
                url: "<?php echo site_url('Inventory/load_sales_return_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        documentCurrency = data['transactionCurrencyID'];
                        salesReturnAutoID = data['salesReturnAutoID'];
                        $('#returnDate').val(data['returnDate']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#location").val(data['wareHouseAutoID']).change();
                        $('#customerID').val(data['customerID']).change();
                        $('#narration').val(data['comment']);
                        $('#referenceNo').val(data['referenceNo']);
                        fetch_detail();
                        check_detail_dataExist(salesReturnAutoID);
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }


    function item_detail_modal() {
        if (salesReturnAutoID) {
            $('#item_detail_form')[0].reset();
            $('#item_detail_form').bootstrapValidator('resetForm', true);
            $("#item_detail_modal").modal({backdrop: "static"});
        }
    }

    function fetch_detail() {
        if (salesReturnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'salesReturnAutoID': salesReturnAutoID},
                url: "<?php echo site_url('Inventory/fetch_sales_return_detail_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#step2').html(data);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
        ;
    }

    function check_detail_dataExist(salesReturnAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'salesReturnAutoID': salesReturnAutoID},
            url: "<?php echo site_url('Inventory/fetch_return_direct_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (jQuery.isEmptyObject(data['detail'])) {
                    $("#customerID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                } else {
                    $("#customerID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function fetch_related_uom(short_code, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'short_code': short_code},
            url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
            success: function (data) {
                $('#unitOfMeasure').empty();
                var mySelect = $('#unitOfMeasure');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitShortCode']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $("#unitOfMeasure").val(select_value);
                        $('#item_detail_form').bootstrapValidator('revalidateField', 'unitOfMeasure');
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_conformation() {
        if (salesReturnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'salesReturnAutoID': salesReturnAutoID, 'html': true},
                url: "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>/" + salesReturnAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_sales_return_buyback'); ?>/" + salesReturnAutoID + '/SLR');
                    attachment_modal_purchaseReturn(salesReturnAutoID, "<?php echo $this->lang->line('sales_markating_transaction_sales_return');?>", "SLR");
                    /*Sales Return*/
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            });
        }
    }

    function fetch_supplier_currency_by_id(supplierAutoID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierAutoID': supplierAutoID},
            url: "<?php echo site_url('Procurement/fetch_supplier_currency_by_id'); ?>",
            success: function (data) {
                if (documentCurrency) {
                    $("#transactionCurrencyID").val(documentCurrency).change()
                } else {
                    if (data.supplierCurrencyID) {
                        $("#transactionCurrencyID").val(data.supplierCurrencyID).change();
                    }
                }
            }
        });
    }

    function confirmation() {
        if (salesReturnAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"

                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'salesReturnAutoID': salesReturnAutoID},
                        url: "<?php echo site_url('Inventory/sales_return_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();

                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            } else if (data['error'] == 2) {
                                myAlert('w', data['message']);
                            }
                            else {
                                refreshNotifications(true);
                                //fetchPage('system/inventory/stock_return_management', salesReturnAutoID, 'Stock Return');
                                fetchPage('system/invoices/sales_return_buyback', '', 'Sales Return ')
                            }

                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function save_draft() {
        if (salesReturnAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>", /*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>", /*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/invoices/sales_return_buyback', '', 'Sales Return ')
                    /*fetchPage('system/inventory/invoices_management', salesReturnAutoID, 'Stock Return');*/
                });
        }
    }

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

    function attachment_modal_purchaseReturn(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#purchaseReturn_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "<?php echo $this->lang->line('common_attachments');?>");
                    <!--Attachments-->
                    $('#purchaseReturn_attachment').empty();
                    $('#purchaseReturn_attachment').append('' +data+ '');


                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_purchaseReturn_attachement(salesReturnAutoID, DocumentSystemCode, myFileName) {
        if (salesReturnAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_delete_attach_this_file');?>", /*You want to delete this attachment file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': salesReturnAutoID, 'myFileName': myFileName},
                        url: "<?php echo site_url('inventory/delete_purchaseReturn_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                attachment_modal_purchaseReturn(DocumentSystemCode, "Sales Return", "SLR");
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
    function edit_sales_return(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_edit_this_document');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_edit');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                /*$("#edit_gl_code").val(null).trigger("change");
                $('#edit_invoice_detail_form').trigger("reset");*/
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'salesreturnid': id},
                    url: "<?php echo site_url('Inventory/fetch_stock_return_detail_buyback'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        var taxAmount = Number(data['totalAfterTax'] / data['requestedQty'])  + Number(data['salesPrice']);
                        var total = Number(data['return_Qty']) * Number(taxAmount);
                        $('#edit_noOfItems').val(data['noOfItems']);
                        $('#edit_grossQty').val(data['grossQty']);
                        $('#edit_noOfUnits').val(data['noOfUnits']);
                        $('#edit_deduction').val(data['bucketWeightID']).change();
                        $('#adjustment_Stock_edit').val(total);
                        $('#returnqty').val(data['return_Qty']);
                        $('#stockreturndetailid').val(id);
                        $('#editsalesprice').val(taxAmount);
                        $("#item_detail_modal_edit_stock").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });


    }
    function cal_deduction_netQtyEdit_buyback() {
        var grossQty = parseFloat($('#edit_grossQty').val());
        var salesprice = parseFloat($('#editsalesprice').val());
        var deduction = parseFloat($('#edit_deduction :selected').text());
        var edit_noOfUnits = parseFloat($('#edit_noOfUnits').val());
         $('#returnqty').val(grossQty - (edit_noOfUnits * deduction ));
        if (grossQty) {
            $('#adjustment_Stock_edit').val((parseFloat((grossQty - (deduction * edit_noOfUnits))* salesprice).toFixed(2)));
        }

    }
    function Stockreturn_Detail_Update()
    {
        $('#UnitOfMeasureID_edit').prop("disabled", false);
            var $form = $('#edit_item_detail_form');
            var data = $form.serializeArray();
            $('select[name="deductionedit"] option:selected').each(function () {
                data.push({'name': 'deductionvalue', 'value': $(this).text()})
            })
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_stock_return_detail_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {

                        $('#edit_item_detail_form')[0].reset();
                        setTimeout(function () {
                            $('#stockreturndetailid').val('');
                            fetch_detail();
                            $('#item_detail_modal_edit_stock').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        }, 300);

                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }


</script>