<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$type_arr = array('' => 'Select Type', 'Standard' => 'Standard');
$currency_arr = all_currency_new_drop();
$supplier_arr = all_supplier_drop();
$sold_arr = sold_to();
$ship_arr = ship_to();
$projectExist = project_is_exist();
$invoice_arr = invoice_to();
$umo_arr = array('' => $this->lang->line('common_select_uom')/*'Select UOM'*/);
$segment_arr = fetch_segment();
$transaction_total = 100;
$currentuser = current_userID();
$jobNumberMandatory = getPolicyValues('JNP', 'All');
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">
        <?php echo $this->lang->line('common_step'); ?><!--Step--> 1 -
        <?php echo $this->lang->line('procurement_approval_purchase_request_header'); ?><!--Purchase Request Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_pqr_detail_table()" data-toggle="tab">
        <?php echo $this->lang->line('common_step'); ?><!--Step--> 2 -
        <?php echo $this->lang->line('procurement_approval_purchase_request_detail'); ?><!--Purchase Request Detail--></a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
        <?php echo $this->lang->line('common_step'); ?><!--Step--> 3 -
        <?php echo $this->lang->line('procurement_approval_purchase_request_confirmation'); ?><!--Purchase Request Confirmation--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="purchase_request_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="">
                    <?php echo $this->lang->line('procurement_approval_prq_date'); ?><!--PRQ Date--> <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment">
                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment" onchange="load_segmentBase_projectID()" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <div class="form-group">
                    <label for="referenceNumber"> <?php echo $this->lang->line('common_reference'); ?><!--Reference-->
                        # </label>
                    <input type="text" class="form-control" id="referenceNumber" name="referenceNumber"
                           placeholder="<?php echo $this->lang->line('common_reference'); ?> #"><!--Reference-->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="SalesPersonName">
                    Requested By<!--Name--> <?php required_mark(); ?></label>
                <div class="input-group">
                    <input type="text" class="form-control" id="requestedByName" name="requestedByName" required autocomplete="off">
                    <input type="hidden" class="form-control" id="requestedEmpID" name="requestedEmpID">
                    <span class="input-group-btn">
                       <!-- <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clearEmployee()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>-->
                        <button class="btn btn-default" type="button" title="Add Employee" rel="tooltip"
                                onclick="link_employee_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                    </span>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID">
                    <?php echo $this->lang->line('common_currency'); ?><!--Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'PO\')" required'); ?>
            </div>
            <div class="col-sm-4">
                <label>
                    <?php echo $this->lang->line('procurement_approval_expected_delivery_date'); ?><!--Expected Delivery Date--> <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="expectedDeliveryDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="expectedDeliveryDate" class="form-control" required>
                </div>
            </div>

        </div>
        <div class="row">
            <?php if ($projectExist == 1) { ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="shippingAddressID">Project <?php required_mark(); ?></label>
                        <div id="div_projectID">
                            <select name="projectID" id="projectID" class="form-control select2">
                                <option value="">Select Project</option>
                            </select>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="narration">
                        <?php echo $this->lang->line('procurement_approval_narration'); ?><!--Narration--></label>
                    <textarea class="form-control" id="narration" name="narration" rows="2"></textarea>
                </div>
            </div>
            <?php if ($jobNumberMandatory == 1) { ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="jobNumber"> Job Number <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="jobNumber" name="jobNumber" placeholder="Job Number">
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="row">
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit">
                <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
        </div>
        </form>
    </div>

    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('procurement_approval_item_details'); ?><!--Item Detail--> </h4>
                <h4></h4></div>
            <div class="col-md-4">
                <button type="button" onclick="purchase_request_detail_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                </button>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped table-condesed">
            <thead>
            <tr>
                <th colspan="6">
                    <?php echo $this->lang->line('procurement_approval_item_details'); ?><!--Item Details--></th>
                <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span class="currency">(LKR)</span>
                </th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                <th style="min-width: 25%" class="text-left">
                    <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                <th style="min-width: 10%" class="text-left">
                    <?php echo $this->lang->line('procurement_approval_expected_delivery_date'); ?><!--Expected Delivery Date--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                <!--<th style="min-width: 10%">Discount</th>-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_net_cost'); ?><!--Net Cost--></th>
                <th style="min-width: 12%"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
            </thead>
            <tbody id="table_body">
            <tr class="danger">
                <td colspan="10" class="text-center"><b>
                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
            </tr>
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table>
        <br>
        <hr>
        <div class="text-right m-t-xs">
            <!-- <button class="btn btn-default prev" onclick="">Previous</button> -->
            <button class="btn btn-primary next" onclick="load_conformation();">
                <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
        </div>
    </div>

    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="purchaseOrder_attachment_label">Modal title</h4>
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="purchaseOrder_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev">
                <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()">
                <?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">
                <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="purchase_request_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">
                    <?php echo $this->lang->line('procurement_approval_add_Item_detail'); ?><!--Add Item Detail--></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="purchase_request_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;">
                                <?php echo $this->lang->line('procurement_approval_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                            <th style="width: 200px;">
                                <?php echo $this->lang->line('procurement_approval_expected_delivery_date'); ?><!--Expected Delivery Date--> <?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                            <th style="width: 100px;">
                                <?php echo $this->lang->line('common_qty'); ?><!--PO Qty--> <?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost--> <span
                                        class="currency"> (LKR)</span></th>
                            <!--<th colspan="2" style="width: 50px;">Discount %</th>-->
                            <th style="width: 100px;" class="hidden">
                                <?php echo $this->lang->line('procurement_approval_net_unit_cost'); ?><!--Net Unit Cost--></th>
                            <th style="width: 100px;">
                                <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search"
                                       name="search[]"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>..."
                                       id="f_search_1"><!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td><input type="text" name="expectedDeliveryDateDetail[]" onkeyup="deliverydate_val(this)" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="expectedDeliveryDateDetail"
                                       class="form-control deliverydat" required></td>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown"  required'); ?></td>
                            <td><input type="text" name="quantityRequested[]" value="0" onkeyup="change_qty(this)"
                                       class="form-control number quantityRequested" onfocus="this.select();" required>
                            </td>
                            <td><input type="text" name="estimatedAmount[]" value="0" placeholder="0.00"
                                       onkeyup="change_amount(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number estimatedAmount" onfocus="this.select();"></td>
                            <td style="width: 100px" class="hidden">
                                <div class="input-group">
                                    <input type="text" name="discount[]" placeholder="0"
                                           class="form-control number discount" value="0"
                                           onkeyup="cal_discount(this.value, this)" onfocus="this.select();">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td class="hidden"><input type="text" name="discount_amount" style="width: 80px;"
                                                      id="discount_amount" placeholder="0.00"
                                                      onkeyup="cal_discount_amount(this.value, this)"
                                                      class="form-control number discount_amount" value="0"
                                                      onfocus="this.select();">
                            </td>
                            <td class="hidden">&nbsp;<span class="net_unit_cost pull-right "
                                                           style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span>
                            </td>
                            <td>&nbsp;<span class="net_amount pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td><textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="<?php echo $this->lang->line('procurement_approval_item_comment'); ?>..."></textarea>
                            </td><!--Item Comment-->
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="savePurchaseOrderDetails()">
                    <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="purchase_request_detail_edit_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">
                    <?php echo $this->lang->line('procurement_approval_edit_item_detail'); ?><!--Edit Item Detail--></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="purchase_request_detail_edit_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;">
                                <?php echo $this->lang->line('procurement_approval_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                            <th style="width: 200px;">
                                <?php echo $this->lang->line('procurement_approval_expected_delivery_date'); ?><!--Expected Delivery Date--> <?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                            <th style="width: 100px;">
                                Qty <?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_unit_cost'); ?><!--Unit Cost--> <span
                                        class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <!--<th colspan="2" style="width: 50px;">Discount %</th>-->
                            <th style="width: 100px;" class="hidden">
                                <?php echo $this->lang->line('procurement_approval_net_unit_cost'); ?><!--Net Unit Cost--></th>
                            <th style="width: 100px;">
                                <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" id="search_edit"
                                       class="form-control" name="search"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>...">
                                <!--Item ID--><!--Item Description-->
                                <input type="hidden" id="itemAutoID_edit" class="form-control" name="itemAutoID">
                            </td>
                            <td><input type="text" name="expectedDeliveryDateDetailEdit"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="expectedDeliveryDateDetailEdit"
                                       class="form-control deliverydat" required></td>
                            <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" id="UnitOfMeasureID_edit"  required'); ?></td>

                            <td><input type="text" name="quantityRequested" id="quantityRequested_edit" value="0"
                                       onkeyup="change_qty_edit()" class="form-control number"
                                       required onfocus="this.select();"></td>
                            <td><input type="text" name="estimatedAmount" value="0" id="estimatedAmount_edit"
                                       placeholder="0.00" onkeyup="change_amount_edit()"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number" onfocus="this.select();"></td>
                            <td style="width: 100px" class="hidden">
                                <div class="input-group">
                                    <input type="text" name="discount" placeholder="0" id="discount_edit"
                                           class="form-control number" value="0"
                                           onkeyup="cal_discount_edit(this.value)" onfocus="this.select();">
                                    <span class="input-group-addon">%</span></div>
                            </td>
                            <td class="hidden"><input type="text" id="discount_amount_edit" style="width: 80px;"
                                                      name="discount_amount" placeholder="0.00"
                                                      onkeyup="cal_discount_amount_edit()" class="form-control number"
                                                      value="0" onfocus="this.select();"></td>
                            <td class="hidden">&nbsp;<span id="net_unit_cost_edit" class="pull-right"
                                                           style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span>
                            </td>
                            <td>&nbsp;<span id="totalAmount_edit" class="net_amount pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td><textarea class="form-control" rows="1" id="comment_edit" name="comment"
                                          placeholder="<?php echo $this->lang->line('procurement_approval_item_comment'); ?>..."></textarea>
                            </td><!--Item Comment-->
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="updatePurchaseOrderDetails()">
                    <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="emp_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel">
                    <?php echo $this->lang->line('procurement_approval_link_employee'); ?><!--Link Employee--></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_employee'); ?><!--Employee--></label>
                        <div class="col-sm-7">
                            <?php
                            $employee_arr = all_employee_drop();
                            echo form_dropdown('employee_id', $employee_arr,$currentuser, 'class="form-control select2" id="employee_id" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="fetch_employee_detail()">
                    <?php echo $this->lang->line('common_add_employee'); ?><!--Add employee--></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var search_id = 1;
    var itemAutoID;
    var purchaseRequestID;
    var purchaseRequestDetailsID;
    var currency_decimal;
    var documentCurrency;
    var projectID;
    var tax_total;
    var item;
    var deliverydat;
    $(document).ready(function () {

        item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Procurement/fetch_itemrecode/?q=%QUERY"
        });
        item.initialize();

        $('.headerclose').click(function () {
            fetchPage('system/PurchaseRequest/erp_purchase_request_employee', purchaseRequestID, 'Purchase Request');
        });

        $('.select2').select2();
        purchaseRequestID = null;
        purchaseRequestDetailsID = null;
        itemAutoID = null;
        currency_decimal = 2;
        documentCurrency = null;
        projectID = null;
        initializeitemTypeahead_edit();
        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#purchase_request_form').bootstrapValidator('revalidateField', 'documentDate');
            $('#purchase_request_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (p_id) {
            purchaseRequestID = p_id;
            laad_pqr_header();
            $("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + purchaseRequestID);
            $('.btn-wizard').removeClass('disabled');
        } else {
            fetch_employee_detail();
            $('.btn-wizard').addClass('disabled');
        }


        $('#purchase_request_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                requestedByName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_name_is_required');?>.'}}}, /*Name is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_supplier_currency_is_required');?>.'}}}, /*Supplier Currency is required*/
                expectedDeliveryDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('procurement_approval_delivery_Date_is_required');?>.'}}}, /*Delivery Date is required*/
                documentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('procurement_approval_prq_date_is_required');?>.'}}}, /*PRQ Date is required*/
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}}/*Segment is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#segment").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'purchaseRequestID', 'value': purchaseRequestID});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('PurchaseRequest/save_purchase_request_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    var result = $('#transactionCurrencyID option:selected').text().split('|');
                    $('.currency').html('( ' + result[0] + ' )');
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        purchaseRequestID = data['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('PurchaseRequest/load_purchase_order_conformation'); ?>/" + purchaseRequestID);
                        $("#segment").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $('[href=#step2]').tab('show');
                    }
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

    function fetch_pqr_detail_table() {
        if (purchaseRequestID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseRequestID': purchaseRequestID},
                url: "<?php echo site_url('PurchaseRequest/fetch_pqr_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('.currency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                    currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                    $('#table_body').empty();
                    $('#table_tfoot').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $("#segment").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);

                        $('#table_body').append('<tr class="danger"><td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                        <!--No Records Found-->
                    } else {
                        $("#segment").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        tot_amount = 0;
                        currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                        $.each(data['detail'], function (key, value) {
                            $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['Itemdescriptionpartno'] + '</td><td>' + value['expectedDeliveryDate'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + (parseFloat(value['unitAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['unitAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['purchaseRequestDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['purchaseRequestDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');

                            x++;
                            tot_amount += parseFloat(value['totalAmount']);
                        });
                        $('#table_tfoot').append('<tr><td colspan="8" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
    }

    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }

    function clearitemAutoIDEdit(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('#itemAutoID_edit').val('');
        }
    }

    function initializeitemTypeahead(id) {
        Inputmask().mask(document.querySelectorAll("input"));
        $('#f_search_' + id).autocomplete({

            serviceUrl: '<?php echo site_url();?>PurchaseRequest/fetch_itemrecode_pqr/',
            onSelect: function (suggestion) {

                var cont = true;
                $('.itemAutoID').each(function () {
                    if (this.value) {
                        if (this.value == suggestion.itemAutoID) {
                            $('#f_search_' + id).val('');
                            $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                            $('#f_search_' + id).closest('tr').find('.deliverydat').val('');
                            $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                            $('#f_search_' + id).closest('tr').find('.estimatedAmount').val('');
                            $('#f_search_' + id).closest('tr').find('.net_amount').html('0');
                            myAlert('w', 'Selected item is already selected');
                            cont = false;
                        }
                    }
                });
                if (cont) {
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    }, 200);
                    //LoaditemUnitPrice_againtsExchangerate(datum.companyLocalWacAmount, this);
                    fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                    fetch_last_grn_amount(this,suggestion.itemAutoID,$('#transactionCurrencyID').val());
                    $(this).closest('tr').find('.deliverydat').focus();
                    $(this).closest('tr').css("background-color", 'white');
                }
            }
        });
        $(".tt-dropdown-menu").css("top", "");
        $('#f_search_' + id).off('focus.autocomplete');
    }

    function initializeitemTypeahead_edit() {
        $('#search_edit').autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#estimatedAmount_edit').val('0.00');
                    $('#discount_amount_edit').val('0.00');
                    $('#totalAmount_edit').text('0');
                    $('#net_unit_cost_edit').text('0.00');
                    $('#quantityRequested_edit').val(0);
                    $('#discount_edit').val(0);
                    $('#itemAutoID_edit').val(suggestion.itemAutoID);
                }, 200);

                //LoaditemUnitPrice_againtsExchangerate_edit(datum.companyLocalWacAmount);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                fetch_last_grn_amount_edit(suggestion.itemAutoID,$('#transactionCurrencyID').val());
                $(this).closest('tr').find('#expectedDeliveryDateDetailEdit').focus();
            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate(LocalWacAmount, element) {
        poID = purchaseRequestID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'poID': purchaseRequestID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('ItemMaster/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data['amount']);
                    net_amount(element);
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate_edit(LocalWacAmount) {
        poID = purchaseRequestID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'poID': purchaseRequestID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('ItemMaster/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#estimatedAmount_edit').val(data['amount']);
                    $('#discount_edit').val('');
                    $('#discount_amount_edit').val('');
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_sub_cat(select_val) {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subSubCategoryID').val("");
        $('#subSubCategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function load_sub_sub_cat() {
        $('#subSubCategoryID option').remove();
        $('#subSubCategoryID').val("");
        var subsubid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subSubCategoryID').empty();
                    var mySelect = $('#subSubCategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function select_text(data) {
        if (data.value != 0) {
            var result = $('#text_type option:selected').text().split('|');
            $('#percentage').val(parseFloat(result[2]));
            cal_tax(parseFloat(result[2]), tax_total);
            $('#tax_form').bootstrapValidator('revalidateField', 'percentage');
        }
    }

    function cal_tax_amount(discount_amount) {
        if (tax_total && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / tax_total) * 100).toFixed(2));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_tax(discount) {
        if (tax_total && discount) {
            $('#tax_amount').val(((tax_total / 100) * parseFloat(discount)).toFixed(2));
        } else {
            $('#tax_amount').val(0);
        }
    }

    function delete_tax(id, value) {
        if (purchaseRequestID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'taxDetailAutoID': id},
                        url: "<?php echo site_url('Procurement/delete_tax_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            setTimeout(function () {
                                fetch_pqr_detail_table();
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function fetch_supplier_currency_by_id(supplierAutoID, select_value) {
        if (supplierAutoID) {
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
                            currency_validation_modal(data.supplierCurrencyID, 'PO', supplierAutoID, 'SUP');
                        }
                    }

                }
            });
        }
    }

    function fetch_related_uom_id(masterUnitID, select_value, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty();

                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');

                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        ;
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_related_uom_id_edit(masterUnitID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $('#UnitOfMeasureID_edit').empty();
                var mySelect = $('#UnitOfMeasureID_edit');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $("#UnitOfMeasureID_edit").val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function laad_pqr_header() {
        if (purchaseRequestID) {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'purchaseRequestID': purchaseRequestID},
                    url: "<?php echo site_url('PurchaseRequest/load_purchase_request_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            fetch_pqr_detail_table();
                            projectID = data['projectID'];
                            $('.currency').html('( ' + data['transactionCurrency'] + ' )');
                            $('#referenceNumber').val(data['referenceNumber']);
                            $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            documentCurrency = data['transactionCurrencyID'];
                            $("#transactionCurrencyID").val(documentCurrency).change()
                            $('#expectedDeliveryDate').val(data['expectedDeliveryDate']);
                            $('#documentDate').val(data['documentDate']);
                            $('#narration').val(data['narration']);
                            $('#requestedByName').val(data['requestedByName']);
                            $('#employee_id').val(data['requestedByName']);
                            if (data['requestedEmpID'] > 0) {
                                $('#requestedByName').prop('readonly', true);
                                $('#requestedEmpID').val(data['requestedEmpID']);
                            }

                            $('#jobNumber').val(data['jobNumber']);
                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                        }
                        stopLoad();
                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            )
            ;
        }
    }

    function fetch_ship_to(val) {
        if (val) {
            var ship = $('#shippingAddressID option:selected').text();
            var res = ship.split(" | ");
            $('#shippingAddressDescription').val(res[2]);
        }
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function purchase_request_detail_modal() {
        if (purchaseRequestID) {
            $('.f_search').typeahead('destroy');
            purchaseRequestDetailsID = null;
            $('#purchase_request_detail_form')[0].reset();
            $('#discount').val(0);
            $('#discount_amount').val(0);
            $('#discount_amount').text('0.00');
            $('#po_detail_add_table tbody tr').not(':first').remove();
            $('.net_amount,.net_unit_cost').text('0');
            $('.f_search').typeahead('val', '');
            $('.itemAutoID').val('');
            fetchExpectedDate();
            initializeitemTypeahead(1);
            Inputmask().mask(document.querySelectorAll("input"));
            $("#purchase_request_detail_modal").modal({backdrop: "static"});
            $('.f_search').closest('tr').css("background-color", 'white');
            $('.deliverydat').closest('tr').css("background-color", 'white');
            $('.quantityRequested').closest('tr').css("background-color", 'white');

        }
    }

    function load_conformation() {
        if (purchaseRequestID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'purchaseRequestID': purchaseRequestID, 'html': true},
                url: "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    stopLoad();
                    refreshNotifications(true);
                    attachment_modal_purchaseRequest(purchaseRequestID, "<?php echo $this->lang->line('procurement_approval_purchase_request');?>", "PRQ");
                    /*Purchase Request*/
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        if (purchaseRequestID) {
            swal({

                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*Confirm*/
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'purchaseRequestID': purchaseRequestID},
                        url: "<?php echo site_url('PurchaseRequest/purchase_request_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data)
                            {
                                fetchPage('system/PurchaseRequest/erp_purchase_request_employee', purchaseRequestID, 'Purchase Request');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (purchaseRequestID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>", /*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>", /*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/PurchaseRequest/erp_purchase_request_employee', purchaseRequestID, 'Purchase Request');
                });
        }
    }

    function currency_validation(CurrencyID, documentID) {
        if (CurrencyID) {
            partyAutoID = $('#supplierPrimaryCode').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'SUP');
        }
    }

    function delete_item(id) {
        if (purchaseRequestID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'purchaseRequestDetailsID': id},
                        url: "<?php echo site_url('PurchaseRequest/delete_purchase_request_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_pqr_detail_table();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id) {
        if (purchaseRequestID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>"/*Edit*/
                },
                function () {
                    $('#po_detail_add_table tbody tr').not(':first').remove();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'purchaseRequestDetailsID': id},
                        url: "<?php echo site_url('PurchaseRequest/fetch_purchase_request_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            var totAmount = parseFloat(data['totalAmount']);
                            var unitAmount = parseFloat(data['unitAmount']);
                            purchaseRequestDetailsID = data['purchaseRequestDetailsID'];
                            $('#search_edit').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            //$('#search_edit').typeahead('val', data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested_edit').val(data['requestedQty']);
                            $('#estimatedAmount_edit').val((parseFloat(data['unitAmount']) + parseFloat(data['discountAmount'])));
                            $('#net_unit_cost_edit').text((unitAmount).formatMoney(2, '.', ','));
                            $('#discount_amount_edit').val(data['discountAmount']);
                            $('#search_id').val(data['itemSystemCode']);
                            $('#itemSystemCode').val(data['itemSystemCode']);
                            $('#itemAutoID_edit').val(data['itemAutoID']);
                            $('#itemDescription').val(data['itemDescription']);
                            $('#comment_edit').val(data['comment']);
                            $('#discount_edit').val(data['discountPercentage']);
                            $('#expectedDeliveryDateDetailEdit').val(data['expectedDeliveryDate']);
                            $('#totalAmount_edit').text((totAmount).formatMoney(currency_decimal, '.', ','));
                            $("#purchase_request_detail_edit_modal").modal({backdrop: "static"});
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function cal_discount_amount(discount_amount, element) {
        var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
        if (estimatedAmount) {
            $(element).closest('tr').find('.discount').val(((parseFloat(discount_amount) / estimatedAmount) * 100).toFixed(2));
        }
        net_amount(element);
    }

    function cal_discount(discount, element) {
        if (discount < 0 || discount > 100) {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $(element).closest('tr').find('.discount').val(parseFloat(0));
        } else {
            var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount_amount').val((estimatedAmount / 100) * parseFloat(discount));
                //$(element).closest('tr').find('.discount_amount').val(23);
            }
            net_amount(element);
        }
    }

    function change_amount(element) {
        $(element).closest('tr').find('.discount').val(parseFloat(0));
        $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        net_amount(element);
    }

    function change_qty(element) {
        if(element.value>0)
        {
            $(element).closest('tr').css("background-color",'white');
        }
        net_amount(element);
    }

    function net_amount(element) {
        var qut = $(element).closest('tr').find('.quantityRequested').val();
        var amount = $(element).closest('tr').find('.estimatedAmount').val();
        var discoun = $(element).closest('tr').find('.discount_amount').val();
        if (qut == null || qut == 0) {
            $(element).closest('tr').find('.net_amount,.net_unit_cost').text('0');
        } else {
            $(element).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(currency_decimal, '.', ','));
            $(element).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(currency_decimal, '.', ','));
        }
    }

    //update function for inline editing
    function cal_discount_amount_edit() {
        var estimatedAmount = parseFloat($('#estimatedAmount_edit').val());
        var discount_amount = parseFloat($('#discount_amount_edit').val());
        if (discount_amount > estimatedAmount) {
            swal("Cancelled", "Discount Amount should be less than the Unit Cost", "error");
            $('#discount_amount_edit').val(0);
            $('#discount_edit').val(0);
            net_amount_edit(estimatedAmount);
        } else {
            if (estimatedAmount) {
                $('#discount_edit').val(((parseFloat(discount_amount) / estimatedAmount) * 100).toFixed(3));
            }
            net_amount_edit(estimatedAmount);
        }
    }

    function cal_discount_edit(discount) {
        var estimatedAmount = parseFloat($('#estimatedAmount_edit').val());
        if (discount < 0 || discount > 100) {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $('#discount_edit').val(0);
            $('#discount_amount_edit').val(0);
            net_amount_edit(estimatedAmount);
        } else {

            if (estimatedAmount) {
                $('#discount_amount_edit').val((estimatedAmount / 100) * parseFloat(discount));
            }
            net_amount_edit(estimatedAmount);
        }

    }

    function change_qty_edit() {
        net_amount_edit();
    }

    function change_amount_edit() {
        $('#discount_edit').val(parseFloat(0));
        $('#discount_amount_edit').val(parseFloat(0));
        net_amount_edit();
    }

    function net_amount_edit() {
        var qut = $('#quantityRequested_edit').val();
        var amount = $('#estimatedAmount_edit').val();
        var discoun = $('#discount_amount_edit').val();
        if (qut == null || qut == 0) {
            $('#totalAmount_edit').text('0');
            $('#net_unit_cost_edit').text('0.00');
        } else {
            $('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(currency_decimal, '.', ','));
            $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(2, '.', ','));
        }
    }

    function attachment_modal_purchaseRequest(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#purchaseOrder_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");
                    <!--Attachments-->
                    $('#purchaseOrder_attachment').empty();
                    $('#purchaseOrder_attachment').append('' +data+ '');


                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_purchaseOrder_delete(purchaseRequestID, DocumentSystemCode, fileName) {
        if (purchaseRequestID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': purchaseRequestID, 'myFileName': fileName},
                        url: "<?php echo site_url('Procurement/delete_purchaseOrder_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            attachment_modal_purchaseRequest(DocumentSystemCode, "<?php echo $this->lang->line('procurement_approval_purchase_request');?>", "PRQ");
                            /*Purchase Request*/
                            if (data == true) {
                                myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');
                                /*Deleted Successfully*/
                            } else {
                                myAlert('e', '<?php echo $this->lang->line('common_deletion_failed');?>');
                                /*Deletion Failed*/
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function add_more() {
        //$('#f_search_1').typeahead('destroy');
        search_id += 1;
        //$('.f_search').typeahead('destroy');

        $('select.select2').select2('destroy');
        var appendData = $('#po_detail_add_table tbody tr:first').clone();
        appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.net_amount,.net_unit_cost').text('0');
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('.deliverydat').val(deliverydat);
        appendData.find('textarea').val('');
        appendData.find('.discount_amount').val('0');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#po_detail_add_table').append(appendData);
        var lenght = $('#po_detail_add_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');

        $(".select2").select2();
        number_validation();
        initializeitemTypeahead(search_id);
        //initializeitemTypeahead(1);
    }

    function savePurchaseOrderDetails() {
        var data = $('#purchase_request_detail_form').serializeArray();
        if (purchaseRequestID) {
            data.push({'name': 'purchaseRequestID', 'value': purchaseRequestID});
            data.push({'name': 'purchaseRequestDetailsID', 'value': purchaseRequestDetailsID});
            $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            });
            $('.itemAutoID').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });

            $('.deliverydat').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $('.quantityRequested').each(function () {
                if (this.value == '' || this.value ==0) {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('PurchaseRequest/save_purchase_request_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            purchaseRequestDetailsID = null;
                            fetch_pqr_detail_table();
                            $('#purchase_request_detail_modal').modal('hide');
                        }
                    }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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

    function updatePurchaseOrderDetails() {
        var data = $('#purchase_request_detail_edit_form').serializeArray();
        if (purchaseRequestID) {
            data.push({'name': 'purchaseRequestID', 'value': purchaseRequestID});
            data.push({'name': 'purchaseRequestDetailsID', 'value': purchaseRequestDetailsID});
            data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('PurchaseRequest/update_purchase_request_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data) {
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                purchaseRequestDetailsID = null;
                                $('#purchase_request_detail_edit_modal').modal('hide');
                                fetch_pqr_detail_table();
                            }
                        }

                    }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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

    //thanks: http://javascript.nwbox.com/cursor_position/
    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function load_segmentBase_projectID() {
        var segment = $('#segment').val();
        var type = 'master';
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase"); ?>',
            dataType: 'html',
            data: {segment: segment, type: type},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_projectID').html(data);
                $('.select2').select2();
                if (projectID) {
                    $("#projectID_master").val(projectID).change()
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function clearEmployee() {
        $('#employee_id').val('').change();
        $('#requestedByName').val('').trigger('input');
        $('#requestedEmpID').val('');
        $('#requestedByName').prop('readonly', false);
    }

    function link_employee_model() {
        var empidselected = $('#requestedEmpID').val();
        $('#employee_id').val(empidselected).change();
        $('#emp_model').modal('show');
    }

    function fetch_employee_detail() {
        var employee_id = $('#employee_id').val();
        if (employee_id) {
            window.EIdNo = employee_id;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'employee_id': employee_id},
                url: "<?php echo site_url('Customer/fetch_employee_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data) {
                        $('#requestedByName').val(data['Ename2']).trigger('input');
                        $('#requestedEmpID').val(employee_id);

                        $('#requestedByName').prop('readonly', true);
                        $('#emp_model').modal('hide');
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        } else {

        }
    }

    function fetchExpectedDate() {
        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseRequestID': purchaseRequestID},
                url: "<?php echo site_url('PurchaseRequest/load_purchase_request_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#expectedDeliveryDateDetail').val(data['expectedDeliveryDate']);
                        deliverydat = data['expectedDeliveryDate'];
                    }
                    stopLoad();
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            }
        )
        ;
    }

    function fetch_last_grn_amount(det,itemAutoId,currencyID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoId': itemAutoId,'currencyID': currencyID},
            url: "<?php echo site_url('PurchaseRequest/fetch_last_grn_amount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $(det).closest('tr').find('.estimatedAmount').val(data['receivedAmount']);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function fetch_last_grn_amount_edit(itemAutoId,currencyID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoId': itemAutoId,'currencyID': currencyID},
            url: "<?php echo site_url('PurchaseRequest/fetch_last_grn_amount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $('#estimatedAmount_edit').val(data['receivedAmount']);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function deliverydate_val(det) {
        if(det.value!=0)
        {
            $(det).closest('tr').css("background-color",'white');
        }

    }


</script>