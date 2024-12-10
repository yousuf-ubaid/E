<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$type_arr = array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'Standard' => $this->lang->line('common_standard')/*'Standard'*/, 'PR' => $this->lang->line('procurement_approval_purchase_request')/*'Purchase Request'*/);
$currency_arr = all_currency_new_drop();
$supplier_arr = all_supplier_drop();
$sold_arr = sold_to();
$ship_arr = ship_to();
$projectExist = project_is_exist();
$invoice_arr = invoice_to();
$umo_arr = array('' => $this->lang->line('common_select_uom')/*'Select UOM'*/);
$segment_arr = fetch_segment();
$transaction_total = 100;
$showPurchasePrice = getPolicyValues('SPP', 'All');
if($showPurchasePrice==' ' || $showPurchasePrice== null || empty($showPurchasePrice)){
    $showPurchasePrice = 0;
}
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">
        <?php echo $this->lang->line('common_step'); ?><!--Step--> 1 -
        <?php echo $this->lang->line('procurement_approval_purchase_order_header'); ?><!--Purchase Order Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_po_detail_table()" data-toggle="tab">
        <?php echo $this->lang->line('common_step'); ?><!--Step--> 2 -
        <?php echo $this->lang->line('procurement_approval_purchase_order_detail'); ?><!--Purchase Order Detail--></a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
        <?php echo $this->lang->line('common_step'); ?><!--Step--> 3 -
        <?php echo $this->lang->line('procurement_approval_purchase_order_confirmation'); ?><!--Purchase Order Confirmation--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="purchase_order_form"'); ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="purchaseOrderType">
                        <?php echo $this->lang->line('common_type'); ?><!--Type--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('purchaseOrderType', $type_arr, 'Standard', 'class="form-control select2" id="purchaseOrderType" required'); ?>
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
                <label for="supplierPrimaryCode">
                    <?php echo $this->lang->line('common_supplier'); ?><!--Supplier--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('supplierPrimaryCode', $supplier_arr, '', 'class="form-control select2" id="supplierPrimaryCode" onchange="fetch_supplier_currency_by_id(this.value)" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID">
                    <?php echo $this->lang->line('common_currency'); ?><!--Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'PO\')" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="">
                    <?php echo $this->lang->line('procurement_approval_po_date'); ?><!--PO Date--> <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="POdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="POdate" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
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
            <div class="form-group col-sm-4" style="display: none;">
                <label for="soldToAddressID">
                    <?php echo $this->lang->line('procurement_approval_sold_to'); ?><!--Sold To--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('soldToAddressID', $sold_arr, '3', 'class="form-control select2" id="soldToAddressID" '); ?>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="shippingAddressID">
                        <?php echo $this->lang->line('procurement_approval_ship_to'); ?><!--Ship To--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('shippingAddressID', $ship_arr, '1', 'class="form-control select2" id="shippingAddressID" required onchange="fetch_ship_to(this.value)"'); ?>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="shippingAddressDescription">
                        <?php echo $this->lang->line('procurement_approval_shipping_address'); ?><!--Shipping Address--> </label>
                    <textarea class="form-control" id="shippingAddressDescription" name="shippingAddressDescription"
                              rows="2"></textarea>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group " style="display: none;">
                    <label for="delivery_terms">
                        <?php echo $this->lang->line('procurement_approval_invoice_to'); ?><!--Invoice To--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('invoiceToAddressID', $invoice_arr, '2', 'class="form-control select2" id="invoiceToAddressID"'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="referenceNumber">
                    <?php echo $this->lang->line('procurement_approval_contact_person'); ?><!--Contact Person--></label>
                <input type="text" class="form-control" id="contactperson" name="contactperson"
                       placeholder="<?php echo $this->lang->line('procurement_approval_contact_person'); ?>">
                <!--Contact Person-->
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="creditPeriod">
                        <?php echo $this->lang->line('common_contact_number'); ?><!--Contact Number--></label>

                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                        <input type="text" class="form-control" id="contactnumber" name="contactnumber"
                               placeholder="<?php echo $this->lang->line('common_contact_number'); ?>">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="creditPeriod">
                        <?php echo $this->lang->line('procurement_approval_credit_period'); ?><!--Credit Period--> (
                        <?php echo $this->lang->line('common_days'); ?><!--Days--> )</label>

                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-qrcode" aria-hidden="true"></i></div>
                        <input type="text" class="form-control" id="creditPeriod" name="creditPeriod" placeholder="00">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if ($projectExist == 1) { ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="shippingAddressID">
                            <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></label>
                        <div id="div_projectID">
                            <select name="projectID" id="projectID" class="form-control select2">
                                <option value="">
                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                            </select>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="deliveryTerms">
                        <?php echo $this->lang->line('procurement_approval_delivery_terms'); ?><!--Delivery Terms--></label>
                    <textarea class="form-control" id="deliveryTerms" name="deliveryTerms" rows="2"></textarea>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="paymentTerms">
                    <?php echo $this->lang->line('procurement_approval_payment_terms'); ?><!--Payment Terms--></label>
                <textarea class="form-control" id="paymentTerms" name="paymentTerms" rows="2"></textarea>
            </div>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="deliveryTerms">  <?php echo $this->lang->line('procurement_penalty_terms'); ?></label><!--Penalty Terms-->
                    <textarea class="form-control" id="penaltyTerms" name="penaltyTerms" rows="2"></textarea>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="creditPeriod"> <?php echo $this->lang->line('common_driver_name'); ?></label><!--Driver Name--></label>

                    <div class="form-group">
                        <input type="text" class="form-control" id="drivername" name="drivername"
                               placeholder="Driver Name">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="creditPeriod"> <?php echo $this->lang->line('common_vehicle_no'); ?></label><!--Vehicle No--></label>

                    <div class="form-group">
                        <input type="text" class="form-control" id="vehicleNo" name="vehicleNo"
                               placeholder="Helper Name">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for=""> <?php echo $this->lang->line('transaction_document_tax_type'); ?></label><!--Document Tax Type--> <?php required_mark(); ?></label>
                    <select name="documentTaxType" class="form-control" id="documentTaxType">
                        <option value="0">General Tax</option>
                        <option value="1">Lineby Tax</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="narration">
                        <?php echo $this->lang->line('procurement_approval_narration'); ?><!--Narration--> </label>
                    <textarea class="form-control" id="narration" name="narration" rows="2"></textarea>
                </div>
            </div>
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
                <button type="button" onclick="purchase_order_detail_modal()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                </button>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped table-condesed">
            <thead>
            <!--<tr>
                <th colspan="5">-->
                    <?php //echo $this->lang->line('procurement_approval_item_details'); ?><!--Item Details--><!--</th>
                <th colspan="4">--><?php //echo $this->lang->line('common_amount'); ?><!--Amount--> <!--<span class="currency">(LKR)</span>
                </th>
                <th>&nbsp;</th>
            </tr>-->
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                <th style="min-width: 25%" class="text-left">
                    <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_net_cost'); ?><!--Net Cost--></th>
                <th style="min-width: 10%" class="lintax">Tax</th>
                <th style="min-width: 10%" class="lintax">Tax Amount</th>
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


        <div class="row">
            <div class="col-md-5">
                <label for="exampleInputName2" id="discount_tot">Discount for </label>

                <form class="form-inline" id="discount_form">
                    <div class="form-group">
                        <input type="text" class="form-control" id="disctype" name="disctype" disabled value="Discount">
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control number" id="discpercentage" name="discpercentage"
                                   style="width: 80px;" onkeyup="cal_disc(this.value)">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control number" id="disc_amount" name="disc_amount"
                               style="width: 100px;" onkeyup="cal_disc_amount(this.value)">
                    </div>
                    <button type="submit" class="btn btn-primary" id="discsubmitbtn"><i class="fa fa-plus"></i></button>
                </form>
            </div>
            <div class="col-md-7">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>Discount Percentage</th>
                        <th>Discount Amount <span class="currency">(LKR)</span></th>
                        <th style="width: 75px !important;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="discount_table_body_recode">

                    </tbody>
                    <tfoot id="discount_table_footer">

                    </tfoot>
                </table>
            </div>
        </div>
        <br>
        <br>

        <div class="row" id="genTax">
            <div class="col-md-5">
                <label for="exampleInputName2" id="tax_tot">Tax Applicable Amount(0.00) </label>
                <input type="hidden" id="taxaplcamnt">
                <form class="form-inline" id="tax_form">
                    <div class="form-group">
                        <?php echo form_dropdown('text_type', all_tax_formula_drop(), '', 'class="form-control" id="text_type" required style="width: 150px;"'); ?>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                </form>
            </div>
            <div class="col-md-7">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Detail</th>
                        <th>Amount <span class="currency">(LKR)</span></th>
                        <th style="width: 75px !important;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="tax_table_body_recode">

                    </tbody>
                    <tfoot id="tax_table_footer">

                    </tfoot>
                </table>
            </div>
        </div>


        <hr>
        <div class="text-right m-t-xs">
            <!-- <button class="btn btn-default prev" onclick="">Previous</button> -->
            <button class="btn btn-primary next" onclick="load_conformation();">Save & Next</button>
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
                        <th>File Name</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="purchaseOrder_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">No Attachment Found</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev">Previous</button>
            <button class="btn btn-primary " onclick="save_draft()">Save as Draft</button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">Confirm</button>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="purchase_order_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Item Detail</h5>
            </div>
            <div class="modal-body">
                <form role="form" id="purchase_order_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;">Item Code <?php required_mark(); ?></th>
                            <th style="width: 150px;">UOM <?php required_mark(); ?></th>
                            <th style="width: 100px;">PO Qty <?php required_mark(); ?></th>
                            <th style="width: 150px;">Unit Cost <span
                                    class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th colspan="2" style="width: 50px;">Discount %</th>
                            <th style="width: 100px;">Net Unit Cost</th>
                            <th class="lintax">Tax</th>
                            <th class="lintax">Tax Amount</th>
                            <th style="width: 100px;">Net Amount</th>
                            <th style="width: 150px;">Comment</th>
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
                                       placeholder="item id , description, part no or secondary code " title="search by itemDescription - itemSystemCode -  partNo - secondary ItemCode" id="f_search_1">
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown"  required'); ?></td>
                            <td><input type="text" name="quantityRequested[]" value="0" onchange="load_line_tax_amount(this)" onkeyup="change_qty(this)"
                                       class="form-control number quantityRequested" onfocus="this.select();" required>
                            </td>
                            <td><input type="text" name="estimatedAmount[]" value="0" placeholder="0.00"
                                       onkeyup="change_amount(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number estimatedAmount" onchange="load_line_tax_amount(this)" onfocus="this.select();"></td>
                            <td style="width: 100px">
                                <div class="input-group">
                                    <input type="text" name="discount[]" placeholder="0"
                                           class="form-control number discount" value="0"
                                           onkeyup="cal_discount(this.value, this)" onchange="load_line_tax_amount(this)" onfocus="this.select();">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td><input type="text" name="discount_amount" placeholder="0.00"
                                       onkeyup="cal_discount_amount(this.value, this)" onchange="load_line_tax_amount(this)"
                                       class="form-control number discount_amount" value="0" onfocus="this.select();">
                            </td>
                            <td>&nbsp;<span class="net_unit_cost pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span></td>
                            <td class="lintax"><?php echo form_dropdown('text_type[]', all_tax_formula_drop(), '', 'class="form-control text_type" style="width: 134px;" onchange="load_line_tax_amount(this)" '); ?></td>
                            <td class="lintax"><span class="linetaxamnt pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td>&nbsp;<span class="net_amount pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span></td>
                            <td><textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="Item Comment..."></textarea></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="savePurchaseOrderDetails()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="purchase_order_detail_edit_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Edit Item Detail</h5>
            </div>
            <div class="modal-body">
                <form role="form" id="purchase_order_detail_edit_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;">Item Code <?php required_mark(); ?></th>
                            <th style="width: 150px;">UOM <?php required_mark(); ?></th>
                            <th style="width: 100px;">PO Qty <?php required_mark(); ?></th>
                            <th style="width: 150px;">Unit Cost <span
                                    class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th colspan="2" style="width: 50px;">Discount %</th>
                            <th style="width: 100px;">Net Unit Cost</th>
                            <th class="lintax">Tax</th>
                            <th class="lintax">Tax Amount</th>
                            <th style="width: 100px;">Net Amount</th>
                            <th style="width: 150px;">Comment</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" id="search_edit"
                                       class="form-control" name="search"
                                       placeholder="Item ID, Item Description...">
                                <input type="hidden" id="itemAutoID_edit" class="form-control" name="itemAutoID">
                            </td>
                            <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" id="UnitOfMeasureID_edit"  required'); ?></td>

                            <td><input type="text" name="quantityRequested" onchange="load_line_tax_amount_edit(this)" id="quantityRequested_edit" value="0"
                                       onkeyup="change_qty_edit()" class="form-control number"
                                       required onfocus="this.select();"><input type="hidden" id="prQtyEdit"></td>
                            <td><input type="text" name="estimatedAmount" onchange="load_line_tax_amount_edit(this)" value="0" id="estimatedAmount_edit"
                                       placeholder="0.00" onkeyup="change_amount_edit()"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number" onfocus="this.select();"></td>
                            <td style="width: 100px">
                                <div class="input-group">
                                    <input type="text" name="discount" onchange="load_line_tax_amount_edit(this)" placeholder="0" id="discount_edit"
                                           class="form-control number" value="0"
                                           onkeyup="cal_discount_edit(this.value)" onfocus="this.select();">
                                    <span class="input-group-addon">%</span></div>
                            </td>
                            <td><input type="text" id="discount_amount_edit" onchange="load_line_tax_amount_edit(this)" name="discount_amount" placeholder="0.00"
                                       onkeyup="cal_discount_amount_edit()" class="form-control number"
                                       value="0" onfocus="this.select();"></td>
                            <td>&nbsp;<span id="net_unit_cost_edit" class="pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span></td>
                            <td class="lintax"><?php echo form_dropdown('text_type', all_tax_formula_drop(), '', 'class="form-control" id="text_type_edit" style="width: 134px;" onchange="load_line_tax_amount_edit(this)" '); ?></td>
                            <td class="lintax"><span class="pull-right" id="linetaxamnt_edit" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td>&nbsp;<span id="totalAmount_edit" class="net_amount pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span></td>
                            <td><textarea class="form-control" rows="1" id="comment_edit" name="comment"
                                          placeholder="Item Comment..."></textarea></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="updatePurchaseOrderDetails()">Update changes
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="prq_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Purchase Request Base</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h5>Purchase Request</h5>
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked" id="prqcode">


                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr>
                                <th colspan='4'>Item</th>
                                <th colspan='2'>Requested Item <span
                                        class="currency"> </span></th>
                                <th class="lintaxcols" colspan='5'>Ordered Item <span
                                        class="currency"> </span></th>
                                <th class="lintax" colspan='7'>Ordered Item <span
                                        class="currency"> </span></th>
                            <tr>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th class="text-left">Description</th>
                                <th>UOM</th>
                                <th>Qty</th>
                                <th>Cost</th>
                                <th>Qty</th>
                                <th>Cost</th>
                                <th colspan="2">Discount (%)</th>
                                <th class="lintax">Tax</th>
                                <th class="lintax">Tax Amount</th>
                                <th>Total</th>
                                <th style="display: none;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="table_body_pr_detail">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="save_prq_base_items()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var search_id = 1;
    var itemAutoID;
    var purchaseOrderID;
    var purchaseOrderDetailsID;
    var currency_decimal;
    var documentCurrency;
    var projectID;
    var tax_total=0;
    var disc_total=0;
    var purchaseOrderType;
    var item;
    var defaultSegment = <?php echo json_encode($this->common_data['company_data']['default_segment']); ?>;
    var showPurchasePrice = <?php echo $showPurchasePrice ?>;
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
            fetchPage('system/procurement/erp_purchase_order', purchaseOrderID, 'Purchase Order');
        });


        $('.select2').select2();
        purchaseOrderID = null;
        purchaseOrderDetailsID = null;
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
            $('#purchase_order_form').bootstrapValidator('revalidateField', 'POdate');
            $('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (p_id) {
            purchaseOrderID = p_id;
            laad_po_header();
            $("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + purchaseOrderID);
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            $('#segment').val(defaultSegment).change();
        }

        $('#discount_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                disc_amount: {validators: {notEmpty: {message: 'Discount Amount is required.'}}},
                discpercentage: {validators: {notEmpty: {message: 'Percentage is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'taxtotal', 'value': $('#taxaplcamnt').val()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Procurement/save_inv_disc_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data['type'], data['data'], 1000);
                    stopLoad();
                    if (data['status']) {
                        $form.bootstrapValidator('resetForm', true);
                        setTimeout(function () {
                            fetch_po_detail_table();
                        }, 300);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
        $('#tax_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                tax_amount: {validators: {notEmpty: {message: 'Tax Amount is required.'}}}, /*Tax Amount is required*/
                text_type: {validators: {notEmpty: {message: 'Tax Type is required.'}}}, /*Tax Type is required*/
                percentage: {validators: {notEmpty: {message: 'Tax Amount is required.'}}}/*Percentage is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'tax_total', 'value': tax_total});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Procurement/save_po_general_tax'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    myAlert(data['type'],data['data']);
                    if (data['status']) {
                        setTimeout(function () {
                            fetch_po_detail_table();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#purchase_order_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                supplierPrimaryCode: {validators: {notEmpty: {message: 'Supplier is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Supplier Currency is required.'}}},
                expectedDeliveryDate: {validators: {notEmpty: {message: 'Delivery Date is required.'}}},
                POdate: {validators: {notEmpty: {message: 'PO Date is required.'}}},
                purchaseOrderType: {validators: {notEmpty: {message: 'Type is required.'}}},
                segment: {validators: {notEmpty: {message: 'Segment is required.'}}},
                shippingAddressID: {validators: {notEmpty: {message: 'Ship To is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#purchaseOrderType").prop("disabled", false);
            $("#segment").prop("disabled", false);
            $("#documentTaxType").prop("disabled", false);
            $("#supplierPrimaryCode").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Procurement/save_purchase_order_header_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    var result = $('#transactionCurrencyID option:selected').text().split('|');
                    $('.currency').html('( ' + result[0] + ' )');
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        purchaseOrderID = data['last_id'];
                        purchaseOrderType = data['purchaseOrderType'];
                        $("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + purchaseOrderID);
                        $("#purchaseOrderType").prop("disabled", true);
                        $("#segment").prop("disabled", true);
                        $("#documentTaxType").prop("disabled", true);
                        $("#supplierPrimaryCode").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        fetch_po_detail_table();
                        $('[href=#step2]').tab('show');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
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

    function fetch_po_detail_table() {
        if (purchaseOrderID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseOrderID': purchaseOrderID},
                url: "<?php echo site_url('Procurement/fetch_po_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('.currency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                    currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                    $('#table_body').empty();
                    $('#table_tfoot').empty();
                    x = 1;
                    if($('#documentTaxType').val()==0){
                        $('#genTax').removeClass('hidden');
                        $('.lintax').addClass('hidden');
                        $('.lintaxcols').removeClass('hidden');
                    }else{
                        $('#genTax').addClass('hidden');
                        $('.lintax').removeClass('hidden');
                        $('.lintaxcols').addClass('hidden');
                    }
                    $('#taxaplcamnt').val(0);
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $("#purchaseOrderType").prop("disabled", false);
                        $("#documentTaxType").prop("disabled", false);
                        $("#segment").prop("disabled", false);
                        $("#supplierPrimaryCode").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);
                        $('#discount_tot').text('Discount Total (0.00)');
                        $('#tax_tot').text('Tax Applicable Amount (0.00)');
                        $('#taxaplcamnt').val(0);
                        if($("#documentTaxType").val()==0){
                            $('#table_body').append('<tr class="danger"><td colspan="10" class="text-center"><b>No Records Found</b></td></tr>');
                        }else{
                            $('#table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>');
                        }
                    } else {
                        $("#purchaseOrderType").prop("disabled", true);
                        $("#documentTaxType").prop("disabled", true);
                        $("#segment").prop("disabled", true);
                        $("#supplierPrimaryCode").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        tot_amount = 0;
                        tot_amount_line = 0;
                        tax_total = 0;
                        disc_total = 0;
                        total_discount = 0;
                        var disc_foottotal = 0;
                        currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                        $.each(data['detail'], function (key, value) {
                            tot_amount_line = parseFloat(value['totalAmount'])+parseFloat(value['taxAmount']);
                            if($("#documentTaxType").val()==0){
                                tot_amount_line=value['totalAmount'];
                            }
                            $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td><b>Description :</b> ' + value['Itemdescriptionpartno'] + ' <br> <b>comment :</b> ' + value['comment'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + (parseFloat(value['unitAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" style="width: 80px;">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + value['discountPercentage'] + '%) </td><td class="text-right">' + parseFloat(value['unitAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right lintax">' + value['lineTaxDesc'] + '</td><td class="text-right lintax">' + parseFloat(value['taxAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['purchaseOrderDetailsID'] + ',' + value['prQty'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['purchaseOrderDetailsID'] + ',' + value['totalAmount'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            /* <a onclick="edit_item(' + value['purchaseOrderDetailsID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |*/
                            x++;

                            tot_amount += parseFloat(value['totalAmount']);
                            tax_total += parseFloat(value['totalAmount']);
                            disc_total += parseFloat(value['totalAmount']);
                            total_discount += (tot_amount - parseFloat((data['currency']['generalDiscountPercentage'] / 100) * disc_total))

                        });
                        if($("#documentTaxType").val()==0){
                            $('#table_tfoot').append('<tr><td colspan="8" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + parseFloat(tot_amount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                        }else{
                            $('#table_tfoot').append('<tr><td colspan="10" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + parseFloat(tot_amount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                        }

                        $('#discount_tot').text('Discount Applicable Amount ( ' + parseFloat(disc_total).formatMoney(2, '.', ',') + ' )');
                        $('#tax_tot').text('Tax Applicable Amount ( ' + parseFloat(tax_total).formatMoney(currency_decimal, '.', ',') + ' )');
                        $('#taxaplcamnt').val(parseFloat(tax_total).toFixed(currency_decimal));
                    }

                    $('#tax_table_body_recode,#tax_table_footer').empty();
                    if (jQuery.isEmptyObject(data['tax_detail'])) {
                        $('#tax_table_body_recode').append('<tr class="danger"><td colspan="4" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        x = 1;
                        t_total = 0;
                        $.each(data['tax_detail'], function (key, value) {
                            $('#tax_table_body_recode').append('<tr><td>' + x + '</td><td>' + value['taxDescription'] + '</td><td class="text-right">' + parseFloat(value['amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_tax_po(' + value['taxDetailAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;
                            t_total += parseFloat(value['amount']);
                        });
                        if (t_total > 0) {
                            $('#tax_table_footer').append('<tr><td colspan="2" class="text-right">Tax Total </td><td class="text-right total">' + parseFloat(t_total).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                        }
                    }

                    $('#discount_table_body_recode,#discount_table_footer').empty();
                     if (data['currency']['generalDiscountPercentage']<=0) {
                     $('#discount_table_body_recode').append('<tr class="danger"><td colspan="3" class="text-center"><b>No Records Found</b></td></tr>');
                         $('#discsubmitbtn').removeClass('hidden');
                     } else {
                         $('#discsubmitbtn').addClass('hidden');
                         $('#discount_table_body_recode').append('<tr><td class="text-right">'+ parseFloat(data['currency']['generalDiscountPercentage']).formatMoney(currency_decimal, '.', ',')+'% </td><td class="text-right">' + parseFloat((data['currency']['generalDiscountPercentage'] / 100) * disc_total).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_discount(' + purchaseOrderID + ');"><span  class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_discount(' + purchaseOrderID + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');

                         disc_foottotal=parseFloat((data['currency']['generalDiscountPercentage'] / 100)* disc_total);
                         $('#tax_tot').text('Tax Applicable Amount ( ' + parseFloat(tax_total-disc_foottotal).formatMoney(currency_decimal, '.', ',') + ' )');
                         $('#taxaplcamnt').val(parseFloat(tax_total-disc_foottotal).toFixed(currency_decimal));
                         tax_total=tax_total-disc_foottotal;

                     }
                    if($('#documentTaxType').val()==0){
                        $('#genTax').removeClass('hidden');
                        $('.lintax').addClass('hidden');
                        $('.lintaxcols').removeClass('hidden');
                    }else{
                        $('#genTax').addClass('hidden');
                        $('.lintax').removeClass('hidden');
                        $('.lintaxcols').addClass('hidden');
                    }
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
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

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN',
            onSelect: function (suggestion) {

                var cont = true;
                $('.itemAutoID').each(function () {
                    if (this.value) {
                        if (this.value == suggestion.itemAutoID && suggestion.categoryTypeID==1) {
                            $('#f_search_' + id).val('');
                            $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                            $('#f_search_' + id).closest('tr').find('.currentWareHouseStock').val('');
                            $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                            $('#f_search_' + id).closest('tr').find('.unitCost').val('');
                            myAlert('w', 'The item selected has already been added');
                            cont = false;
                        }
                    }
                });
                if (cont) {

                }
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                //LoaditemUnitPrice_againtsExchangerate(datum.companyLocalWacAmount, this);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                if(showPurchasePrice == 1){
                    fetch_purchase_price(suggestion.companyLocalPurchasingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                }else{
                    fetch_last_grn_amount(this, suggestion.itemAutoID, $('#transactionCurrencyID').val(), $('#supplierPrimaryCode').val());
                }
                //fetch_last_grn_amount(this, suggestion.itemAutoID, $('#transactionCurrencyID').val(), $('#supplierPrimaryCode').val());
                $(this).closest('tr').find('.quantityRequested').focus();
                $(this).closest('tr').css("background-color", 'white');
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function initializeitemTypeahead_edit() {
        /**var item = new Bloodhound({
         datumTokenizer: function (d) {
         return Bloodhound.tokenizers.whitespace(d.Match);
         },
         queryTokenizer: Bloodhound.tokenizers.whitespace,
         remote: "
        <?php echo site_url();?>Procurement/fetch_itemrecode/?q=%QUERY"
         });
         item.initialize();
         $('#search_edit').typeahead(null, {
         displayKey: 'Match',
         source: item.ttAdapter()
         }).on('typeahead:selected', function (object, datum) {
         $('#estimatedAmount_edit').val('0.00');
         $('#discount_amount_edit').val('0.00');
         $('#totalAmount_edit').text('0.00');
         $('#net_unit_cost_edit').text('0.00');
         $('#quantityRequested_edit').val(0);
         $('#discount_edit').val(0);
         $('#itemAutoID_edit').val(datum.itemAutoID);
         //LoaditemUnitPrice_againtsExchangerate_edit(datum.companyLocalWacAmount);
         fetch_related_uom_id_edit(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID);
         });*/

        $('#search_edit').autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#estimatedAmount_edit').val('0.00');
                    $('#discount_amount_edit').val('0.00');
                    $('#totalAmount_edit').text('0.00');
                    $('#net_unit_cost_edit').text('0.00');
                    $('#quantityRequested_edit').val(0);
                    $('#discount_edit').val(0);
                    $('#itemAutoID_edit').val(suggestion.itemAutoID);
                }, 200);

                //LoaditemUnitPrice_againtsExchangerate_edit(datum.companyLocalWacAmount);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                fetch_last_grn_amount_edit(suggestion.itemAutoID, $('#transactionCurrencyID').val(), $('#supplierPrimaryCode').val());
                $(this).closest('tr').find('#quantityRequested_edit').focus();
            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate(LocalWacAmount, element) {
        poID = purchaseOrderID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'poID': purchaseOrderID, 'LocalWacAmount': LocalWacAmount},
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
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate_edit(LocalWacAmount) {
        poID = purchaseOrderID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'poID': purchaseOrderID, 'LocalWacAmount': LocalWacAmount},
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
                alert('An Error Occurred! Please Try Again.');
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
            $('#discount_form').bootstrapValidator('revalidateField', 'percentage');
        }
    }

    function cal_tax_amount(discount_amount) {
        if (tax_total && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / tax_total) * 100).toFixed(2));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_disc_amount(discount_amount) {
        if(disc_total>=discount_amount){
            if (disc_total && discount_amount) {
                $('#discpercentage').val(((parseFloat(discount_amount) / disc_total) * 100));
            } else {
                $('#discpercentage').val(0);
            }
        }else{
            myAlert('w','discount amount canot be greater than PO amount');
            $('#disc_amount').val(0);
            $('#discpercentage').val(0);
        }

    }

    function cal_tax(discount) {
        if (tax_total && discount) {
            $('#tax_amount').val(((total_discount / 100) * parseFloat(discount)).toFixed(2));
        } else {
            $('#tax_amount').val(0);
        }
    }

    function cal_disc(discount) {
        if (disc_total && discount) {
            $('#disc_amount').val(((disc_total / 100) * parseFloat(discount)).toFixed(2));
        } else {
            $('#disc_amount').val(0);
        }
    }

    function delete_tax(id, value) {
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
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
                        data: {'taxDetailAutoID': id},
                        url: "<?php echo site_url('Procurement/delete_tax_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            setTimeout(function () {
                                fetch_po_detail_table();
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

    function laad_po_header() {
        if (purchaseOrderID) {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'purchaseOrderID': purchaseOrderID},
                    url: "<?php echo site_url('Procurement/load_purchase_order_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            fetch_po_detail_table();
                            projectID = data['projectID'];
                            $('.currency').html('( ' + data['transactionCurrency'] + ' )');
                            $('#referenceNumber').val(data['referenceNumber']);
                            $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            documentCurrency = data['transactionCurrencyID'];
                            $("#supplierPrimaryCode").val(data['supplierPrimaryCode']).change();
                            $('#expectedDeliveryDate').val(data['expectedDeliveryDate']);
                            $('#POdate').val(data['documentDate']);
                            $('#narration').val(data['narration']);
                            $('#documentTaxType').val(data['documentTaxType']);
                            if(data['documentTaxType']==0){
                                $('#genTax').removeClass('hidden');
                            }else{
                                $('#genTax').addClass('hidden');
                            }
                            $('#soldToAddressID').val(data['soldToAddressID']);
                            $('#shippingAddressID').val(data['shippingAddressID']).change();
                            $('#invoiceToAddressID').val(data['invoiceToAddressID']);
                            $('#deliveryTerms').val(data['deliveryTerms']);
                            $('#penaltyTerms').val(data['penaltyTerms']);
                            $('#paymentTerms').val(data['paymentTerms']);
                            $('#purchaseOrderType').val(data['purchaseOrderType']).change();
                            purchaseOrderType = data['purchaseOrderType'];
                            $('#contactnumber').val(data['contactPersonNumber']);
                            $('#contactperson').val(data['contactPersonName']);
                            $('#creditPeriod').val(data['creditPeriod']);
                            $('#drivername').val(data['driverName']);
                            $('#vehicleNo').val(data['vehicleNo']);
                            $('#shippingAddressDescription').val(data['shippingAddressDescription']);
                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                        }
                        stopLoad();
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
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

    function purchase_order_detail_modal() {
        if (purchaseOrderID) {
            if (purchaseOrderType == 'PR') {

                load_prq_codes();
                $("#prq_base_modal").modal({backdrop: "static"});
            } else {
                $('.f_search').typeahead('destroy');
                purchaseOrderDetailsID = null;
                $('#purchase_order_detail_form')[0].reset();
                $('#discount').val(0);
                $('#discount_amount').val(0);
                $('#po_detail_add_table tbody tr').not(':first').remove();
                $('.net_amount,.net_unit_cost').text('0.00');
                $('.f_search').typeahead('val', '');
                $('.itemAutoID').val('');
                initializeitemTypeahead(1);
                $('.f_search').closest('tr').css("background-color", 'white');
                $('.quantityRequested').closest('tr').css("background-color", 'white');
                $('.estimatedAmount').closest('tr').css("background-color", 'white');
                $("#purchase_order_detail_modal").modal({backdrop: "static"});
            }
        }
    }

    function load_conformation() {
        if (purchaseOrderID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'purchaseOrderID': purchaseOrderID, 'html': true},
                url: "<?php echo site_url('Procurement/load_purchase_order_conformation_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    stopLoad();
                    refreshNotifications(true);
                    attachment_modal_purchaseOrder(purchaseOrderID, "Purchase Order", "PO");
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to confirm this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'purchaseOrderID': purchaseOrderID},
                        url: "<?php echo site_url('Procurement/purchase_order_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data) {
                                fetchPage('system/procurement/erp_purchase_order', purchaseOrderID, 'Purchase Order');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/procurement/erp_purchase_order', purchaseOrderID, 'Purchase Order');
                });
        }
    }

    function currency_validation(CurrencyID, documentID) {
        if (CurrencyID) {
            partyAutoID = $('#supplierPrimaryCode').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'SUP');
        }
    }

    function delete_item(id,totalAmount) {
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
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
                        data: {'purchaseOrderDetailsID': id,'purchaseOrderID': purchaseOrderID,'taxtotal': $('#taxaplcamnt').val(),'totalAmount':totalAmount},
                        url: "<?php echo site_url('Procurement/delete_purchase_order_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_po_detail_table();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id, prQty) {
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to edit this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Edit"
                },
                function () {
                    $('#po_detail_add_table tbody tr').not(':first').remove();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'purchaseOrderDetailsID': id},
                        url: "<?php echo site_url('Procurement/fetch_purchase_order_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            var taxAmount = parseFloat(data['taxAmount']);
                            var totAmount = parseFloat(data['totalAmount']);
                            var unitAmount = parseFloat(data['unitAmount']);

                            purchaseOrderDetailsID = data['purchaseOrderDetailsID'];
                            $('#search_edit').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            //$('#search_edit').typeahead('val', data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested_edit').val(data['requestedQty']);
                            $('#prQtyEdit').val(data['prQty']);
                            $('#estimatedAmount_edit').val((parseFloat(data['unitAmount']) + parseFloat(data['discountAmount'])));
                            $('#net_unit_cost_edit').text((unitAmount).formatMoney(currency_decimal, '.', ','));
                            $('#discount_amount_edit').val(data['discountAmount']);
                            $('#search_id').val(data['itemSystemCode']);
                            $('#itemSystemCode').val(data['itemSystemCode']);
                            $('#itemAutoID_edit').val(data['itemAutoID']);
                            $('#itemDescription').val(data['itemDescription']);
                            $('#comment_edit').val(data['comment']);
                            $('#discount_edit').val(data['discountPercentage']);
                            $('#text_type_edit').val(data['taxCalculationformulaID']);
                            $('#linetaxamnt_edit').text((taxAmount).formatMoney(currency_decimal, '.', ','));
                            $('#totalAmount_edit').text((totAmount+taxAmount).formatMoney(currency_decimal, '.', ','));
                            $("#purchase_order_detail_edit_modal").modal({backdrop: "static"});
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
                $(element).closest('tr').find('.discount_amount').val((estimatedAmount / 100) * parseFloat(discount).toFixed(2));
            }
            net_amount(element);
        }
    }

    function change_amount(element) {
        if (element.value > 0) {
            $(element).closest('tr').css("background-color", 'white');
        }
        $(element).closest('tr').find('.discount').val(parseFloat(0));
        $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        net_amount(element);
    }

    function change_qty(element) {
        if (element.value > 0) {
            $(element).closest('tr').css("background-color", 'white');
        }
        net_amount(element);
    }

    function net_amount(element) {
        var qut = $(element).closest('tr').find('.quantityRequested').val();
        var amount = $(element).closest('tr').find('.estimatedAmount').val();
        var discoun = $(element).closest('tr').find('.discount_amount').val();
        if (qut == null || qut == 0) {
            $(element).closest('tr').find('.net_amount,.net_unit_cost').text('0.00');
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
                $('#discount_edit').val(((parseFloat(discount_amount) / estimatedAmount) * 100).toFixed(currency_decimal));
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
        if(purchaseOrderType == 'PR'){
            var prQtyEdit = getNumberAndValidate($('#prQtyEdit').val());
            var quantityRequested = getNumberAndValidate($('#quantityRequested_edit').val());
            if (quantityRequested <= prQtyEdit) {
                net_amount_edit();
            } else {
                $('#quantityRequested_edit').val(0);
                net_amount_edit();
                swal("PO Qty should be less than requested Qty", "error");
            }
        }else{
            net_amount_edit();
        }


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
            $('#totalAmount_edit').text('0.00');
            $('#net_unit_cost_edit').text('0.00');
        } else {
            $('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(currency_decimal, '.', ','));
            $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(currency_decimal, '.', ','));
        }
    }

    function attachment_modal_purchaseOrder(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': 0},
                success: function (data) {
                    $('#purchaseOrder_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " Attachments");
                    $('#purchaseOrder_attachment').empty();
                    $('#purchaseOrder_attachment').append('' + data + '');

                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_purchaseOrder_delete(purchaseOrderID, DocumentSystemCode, fileName) {
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this attachment file!",
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
                        data: {'attachmentID': purchaseOrderID, 'myFileName': fileName},
                        url: "<?php echo site_url('Procurement/delete_purchaseOrder_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            attachment_modal_purchaseOrder(DocumentSystemCode, "Purchase Order", "PO");
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                            } else {
                                myAlert('e', 'Deletion Failed');
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
        appendData.find('.net_amount,.net_unit_cost').text('0.00');
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
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
        var data = $('#purchase_order_detail_form').serializeArray();
        if (purchaseOrderID) {
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'taxtotal', 'value': $('#taxaplcamnt').val()});
            data.push({'name': 'purchaseOrderDetailsID', 'value': purchaseOrderDetailsID});
            $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            });

            $('.itemAutoID').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $('.quantityRequested').each(function () {
                if (this.value == '' || this.value == 0) {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $('.estimatedAmount').each(function () {
                if (this.value == '' || this.value == 0) {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });

            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Procurement/save_purchase_order_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            purchaseOrderDetailsID = null;
                            fetch_po_detail_table();
                            $('#purchase_order_detail_modal').modal('hide');
                        }
                    }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
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
        var data = $('#purchase_order_detail_edit_form').serializeArray();
        if (purchaseOrderID) {
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'purchaseOrderDetailsID', 'value': purchaseOrderDetailsID});
            data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
            data.push({'name': 'taxtotal', 'value': $('#taxaplcamnt').val()});
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Procurement/update_purchase_order_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data) {
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                purchaseOrderDetailsID = null;
                                $('#purchase_order_detail_edit_modal').modal('hide');
                                fetch_po_detail_table();
                            }
                        }

                    }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
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

    function load_prq_codes() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': purchaseOrderID},
            url: "<?php echo site_url('Procurement/fetch_prq_code'); ?>",
            success: function (data) {
                $('#prqcode').empty();
                $('#table_body_pr_detail').empty();
                var mySelect = $('#prqcode');
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, value) {
                        var bal=value['requestedQty']-value['prQty'];
                        if(bal>0){
                            mySelect.append('<li title="PR Date :- ' + value['documentDate'] + ' Requestd By:- ' + value['requestedByName'] + '"  rel="tooltip"><a onclick="fetch_prq_detail_table(' + value['purchaseRequestID'] + ')">' + value['purchaseRequestCode'] + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>');
                            $("[rel=tooltip]").tooltip();
                        }

                    });
                } else {
                    mySelect.append('<li><a>No Records found</a></li>');
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }

        });

    }

    function fetch_prq_detail_table(purchaseRequestID) {
        if (purchaseRequestID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseRequestID': purchaseRequestID},
                url: "<?php echo site_url('Procurement/fetch_prq_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body_pr_detail').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body_pr_detail').append('<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        tot_amount = 0;
                        receivedQty = 0;

                        $.each(data['detail'], function (key, value) {
                            if (value['receivedQty'] != null && value['receivedQty'] !== undefined) {
                                receivedQty = value['receivedQty'];
                            }
                            cost_status = '<input type="text"  onchange="load_line_tax_amount_prq(' + value['purchaseRequestDetailsID'] + ')" class="number" size="10" id="amount_' + value['purchaseRequestDetailsID'] + '" onkeypress="return validateFloatKeyPress(this,event)" value="' + parseFloat(value['unitAmount']).toFixed(currency_decimal) + '" onkeyup="select_value(' + value['purchaseRequestDetailsID'] + ')" >';
                            discount_status = '<td> <input type="text" placeholder="0" id="discount_prq_' + value['purchaseRequestDetailsID'] + '"  size="5" class="number" value="0" onkeyup="cal_discount_prq(' + value['purchaseRequestDetailsID'] + ')" onfocus="this.select();"> </td> <td><input type="text" size="3" onchange="load_line_tax_amount_prq(' + value['purchaseRequestDetailsID'] + ')" id="discount_amount_prq_' + value['purchaseRequestDetailsID'] + '" style="width: 80px;" placeholder="0.00" class="number" onkeyup="cal_discount_amt(' + value['purchaseRequestDetailsID'] + ');" onkeypress="return validateFloatKeyPress(this,event);"  value="0" ></td>';
                            /*var qty=value['requestedQty'] - value['prQty'];*/
                            var qty=value['balQty'];
                            if(qty>0){
                                $('#table_body_pr_detail').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + qty + '</td><td class="text-right">' + parseFloat(value['unitAmount']).toFixed(currency_decimal) + '</td><td class="text-center"><input type="text" onchange="load_line_tax_amount_prq(' + value['purchaseRequestDetailsID'] + ')" class="number" size="8" id="qty_' + value['purchaseRequestDetailsID'] + '" onkeyup="select_check_box(this,' + value['purchaseRequestDetailsID'] + ',' + value['unitAmount'] + ',' + (value['requestedQty'] - value['prQty'] ) + ' )" ></td><td class="text-center">' + cost_status + '</td>' + discount_status + ' <td class="lintax"><select class="lntax_drop" onchange="load_line_tax_amount_prq(' + value['purchaseRequestDetailsID'] + ')" style="width: 110px;"  id="text_type_' + value['purchaseRequestDetailsID'] + '"><option value="">Select Tax Type</option></select></td> <td class="lintax"><span class="pull-right" id="lintaxamntprq_' + value['purchaseRequestDetailsID'] + '" >0</span></td> <td class="text-center"><p id="tot_' + value['purchaseRequestDetailsID'] + '"> </p></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['purchaseRequestDetailsID'] + '" type="checkbox" value="' + value['purchaseRequestDetailsID'] + '"></td></tr>');
                            }
                            x++;
                            tot_amount += (parseFloat(value['totalAmount']).toFixed(currency_decimal));
                            //.formatMoney(currency_decimal, '.', ',')
                        });

                        if (!jQuery.isEmptyObject(data['taxdrop'])) {
                            $('.lntax_drop').empty();
                            var mySelect = $('.lntax_drop');
                            mySelect.append($('<option></option>').val('').html('Select Tax Type'));
                            $.each(data['taxdrop'], function (val, text) {
                                mySelect.append($('<option></option>').val(text['taxCalculationformulaID']).html(text['Description']));
                            });
                        }
                        if($('#documentTaxType').val()==0){
                            $('#genTax').removeClass('hidden');
                            $('.lintax').addClass('hidden');
                            $('.lintaxcols').removeClass('hidden');
                        }else{
                            $('#genTax').addClass('hidden');
                            $('.lintax').removeClass('hidden');
                            $('.lintaxcols').addClass('hidden');
                        }
                    }
                    number_validation();
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
        ;
    }

    function select_check_box(data, id, amount, reqqty) {
        var qty = $('#qty_' + id).val();
        if (qty <= reqqty) {
            $("#check_" + id).prop("checked", false);
            if (data.value > 0) {
                $("#check_" + id).prop("checked", true);
            }
            amount = $('#amount_' + id).val();
            if (amount < 0) {
                amount = 0;
            }
            var total = qty * amount;
            var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', ',');
            $('#tot_' + id).text(totalnew);
        } else {
            $('#discount_prq_' + id).val(0);
            $('#discount_amount_prq_' + id).val(0);
            $('#qty_' + id).val(0);
            $('#tot_' + id).text('');
            swal("Ordered Qty should be less than requested Qty", "error");
        }
        cal_discount_prq(id)
    }

    function select_value(id) {
        var qty = $('#qty_' + id).val();
        if (qty < 0) {
            qty = 0;
        }
        amount = $('#amount_' + id).val();
        if (amount < 0) {
            amount = 0;
        }
        var total = qty * amount;
        var totalnew = parseFloat(total);//.formatMoney(currency_decimal, '.', ',')
        $('#tot_' + id).text(totalnew);
        cal_discount_prq(id)
    }

    function save_prq_base_items() {
        var selected = [];
        var amount = [];
        var qty = [];
        var discount = [];
        var discountamt = [];
        var taxtype = [];
        $('#table_body_pr_detail input:checked').each(function () {
            if ($('#amount_' + $(this).val()).val() == '') {
                swal("Cancelled", "Ordered Item cannot be blank !", "error");
            } else {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                qty.push($('#qty_' + $(this).val()).val());
                discount.push($('#discount_prq_' + $(this).val()).val());
                discountamt.push($('#discount_amount_prq_' + $(this).val()).val());
                taxtype.push($('#text_type_' + $(this).val()).val());
            }
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'DetailsID': selected,
                    'purchaseOrderID': purchaseOrderID,
                    'amount': amount,
                    'qty': qty,
                    'discount': discount,
                    'discountamt': discountamt,
                    'taxtotal': $('#taxaplcamnt').val(),
                    'taxtype': taxtype
                },
                url: "<?php echo site_url('Procurement/save_prq_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('#prq_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_po_detail_table();
                        }, 300);
                    } else {
                        myAlert('w', data['data'], 1000);
                    }

                }, error: function () {
                    $('#prq_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function cal_discount_prq(id) {
        var discount = $('#discount_prq_' + id).val();
        var qty = $('#qty_' + id).val();
        var amount = $('#amount_' + id).val();
        var estimatedAmount = parseFloat(amount * qty);
        var totalval = 0;
        var discountval = 0;
        if (discount < 0 || discount > 100) {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $('#discount_prq_' + id).val(0);
            $('#discount_amount_prq_' + id).val(0);
        } else {
            if (discount > 0) {
                discountval = (parseFloat(amount) / 100) * parseFloat(discount);
                totalval = (amount - discountval) * qty;
                $('#tot_' + id).text(totalval);
                $('#discount_amount_prq_' + id).val(discountval);
            }
        }
        if (discount > 0) {
            cal_discount_amt(id)
        } else {
            $('#discount_prq_' + id).val(0);
            $('#discount_amount_prq_' + id).val(0);
        }

    }

    function cal_discount_amt(id) {
        var discountamt = parseFloat($('#discount_amount_prq_' + id).val());
        var qty = $('#qty_' + id).val();
        var amount = $('#amount_' + id).val();
        var totalval = 0;
        var discountpercent = 0;
        if (discountamt < 0 || discountamt > amount) {
            swal("Cancelled", "Discount Amount should be less than unit cost", "error");
            $('#discount_prq_' + id).val(0);
            $('#discount_amount_prq_' + id).val(0);
        } else {
            if (discountamt > 0) {
                //discountpercent = (parseFloat(discountamt) / amount) *100;
                discountpercent = (parseFloat(discountamt) / amount) * 100;
                totalval = (amount - discountamt) * qty;
                $('#tot_' + id).text(totalval);
                $('#discount_prq_' + id).val(discountpercent);
            } else {
                $('#discount_prq_' + id).val(0);
                $('#discount_amount_prq_' + id).val(0);
            }
        }
    }


    function fetch_last_grn_amount(det, itemAutoId, currencyID, supplierPrimaryCode) {

        $(det).closest('tr').find('.estimatedAmount').val('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoId': itemAutoId, 'currencyID': currencyID, 'supplierPrimaryCode': supplierPrimaryCode},
            url: "<?php echo site_url('Procurement/fetch_last_grn_amount'); ?>",
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

    function fetch_last_grn_amount_edit(itemAutoId, currencyID, supplierPrimaryCode) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoId': itemAutoId, 'currencyID': currencyID, 'supplierPrimaryCode': supplierPrimaryCode},
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

    function delete_discount(id){
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
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
                        data: {'purchaseOrderID': id,'taxtotal': $('#taxaplcamnt').val()},
                        url: "<?php echo site_url('Procurement/delete_purchase_order_discount'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1])
                            if(data[0]=='s'){
                                fetch_po_detail_table();
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_discount(id){
        $('#discsubmitbtn').removeClass('hidden');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': id},
            url: "<?php echo site_url('Procurement/edit_discount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $('#discpercentage').val(data['generalDiscountPercentage']);
                    $('#disc_amount').val(data['generalDiscountAmount']);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function delete_tax_po(id) {
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this record!",
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
                        data: {'taxDetailAutoID': id ,'purchaseOrderID': purchaseOrderID},
                        url: "<?php echo site_url('Procurement/delete_tax_po'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            setTimeout(function () {
                                fetch_po_detail_table();
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }


    function load_line_tax_amount(ths){
        var qut = $(ths).closest('tr').find('.quantityRequested').val();
        var amount = $(ths).closest('tr').find('.estimatedAmount').val();
        var discoun = $(ths).closest('tr').find('.discount_amount').val();
        var taxtype = $(ths).closest('tr').find('.text_type').val();
        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun=0;
        }

        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt=(qut*(amount-discoun));
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {purchaseOrderID:purchaseOrderID,applicableAmnt:lintaxappamnt,taxtype:taxtype},
                url: "<?php echo site_url('Procurement/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $(ths).closest('tr').find('.linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.net_amount').text((data+lintaxappamnt).toFixed(currency_decimal));
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $(ths).closest('tr').find('.linetaxamnt').text('0');
        }

    }

    function load_line_tax_amount_edit(ths){
        var qut = $('#quantityRequested_edit').val();
        var amount = $('#estimatedAmount_edit').val();
        var discoun = $('#discount_amount_edit').val();
        var taxtype = $('#text_type_edit').val();
        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun=0;
        }

        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt=(qut*(amount-discoun));
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {purchaseOrderID:purchaseOrderID,applicableAmnt:lintaxappamnt,taxtype:taxtype},
                url: "<?php echo site_url('Procurement/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#linetaxamnt_edit').text(data.toFixed(currency_decimal));
                    $('#totalAmount_edit').text((data+lintaxappamnt).toFixed(currency_decimal));
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $('#linetaxamnt_edit').text('0');
        }

    }


    function load_line_tax_amount_prq(id){
        var qut = $('#qty_'+id).val();
        var amount = $('#amount_'+id).val();
        var discoun = $('#discount_amount_prq_'+id).val();
        var taxtype = $('#text_type_'+id).val();

        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt=(qut*(amount-discoun));
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {purchaseOrderID:purchaseOrderID,applicableAmnt:lintaxappamnt,taxtype:taxtype},
                url: "<?php echo site_url('Procurement/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#lintaxamntprq_'+id).text(data.toFixed(currency_decimal));
                    $('#tot_'+id).text((data+lintaxappamnt).toFixed(currency_decimal));
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $('#lintaxamntprq_'+id).text('0');
        }

    }

    function fetch_purchase_price(purchaseprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: purchaseOrderID,
                purchaseprice: purchaseprice,
                //unitOfMeasureID: unitOfMeasureID,
                //itemAutoID: itemAutoID,
                tableName: 'srp_erp_purchaseordermaster',
                primaryKey: 'purchaseOrderID',
            },
            url: "<?php echo site_url('ItemMaster/fetch_purchase_price'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.estimatedAmount').empty();
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
</script>