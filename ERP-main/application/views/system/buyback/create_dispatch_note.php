<?php echo head_page($_POST['page_name'], false);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$segment_arr = fetch_segment();
$supplier_arr = all_supplier_drop();
$farms_arr = load_all_farms();
$currency_arr = all_currency_new_drop();//array('' => 'Select Currency');
$location_arr = all_delivery_location_drop();
$location_arr_default = default_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$uom_arr = array('' => 'Select UOM');
$batch_arr = array('' => 'Select Batch');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
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
</style>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Dispatch Note Header</a>
    <!--    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_details()" data-toggle="tab">Step 2 - Dispatch
            Note
            Detail</a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="fetch_addon_cost()" data-toggle="tab">Step 3 - Dispatch
            Note
            Addon Cost</a>-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="load_confirmation();" data-toggle="tab">Step 2 -
        Dispatch Note
        Confirmation</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="dispatchNote_header_form" autocomplete="off"'); ?>
        <input type="hidden" name="dispatchAutoID" id="dispatchAutoID_edit">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>DISPATCH NOTE HEADER</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Issued From</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('dispatchType', array('' => 'Select a type', '1' => 'Direct','2' => 'Load Change'), '', 'class="form-control" id="dispatchType"'); ?>
                            <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Segment</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment"'); ?>
                            <span class="input-req-inner"></span></span>

                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Farm</label>
                    </div>
                    <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                <?php echo form_dropdown('farmID', $farms_arr, '', 'class="form-control select2" id="farmID" onchange="fetch_farmer_currencyID(this.value),fetch_farmBatch(this.value),fetch_farmerMasterDetails(this.value)" required'); ?>
                                <span class="input-req-inner"></span></span>

                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Batch</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <div id="div_loadBatch">
                                <?php echo form_dropdown('batchMasterID', $batch_arr, 'Each', 'class="form-control" id="batchMasterID" '); ?>
                            </div>
                            <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
<!--                    <div class="form-group col-sm-2">
                        <label class="title">Supplier</label>
                    </div>
                    <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                <?php /*echo form_dropdown('supplierID', $supplier_arr, '', 'class="form-control select2" id="supplierID" onchange="fetch_supplier_currency_by_id(this.value)" required'); */?>
                                <span class="input-req-inner"></span></span>

                    </div>-->

                    <div class="form-group col-sm-2">
                        <label class="title">Dispatched Date</label>

                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="dispatchedDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="dispatchedDate" class="form-control" required>
                </div>
                <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Reference No</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="referenceno" name="referenceno">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title">Delivered Date</label>

                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="deliveredDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="deliveredDate" class="form-control" required>
                </div>

                <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Delivery From</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('location', $location_arr, $location_arr_default, 'class="form-control select2" id="location" required'); ?>
                            <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title">Narration</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
                    </div>
                </div>
                <hr>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Contact Person Name</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="contactPersonName" name="contactPersonName">
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Telephone Number</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                            <input type="text" class="form-control " id="contactPersonNumber"
                                   name="contactPersonNumber">
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Financial Year</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                            <span class="input-req-inner"></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Financial Period</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                <?php echo form_dropdown('financeyear_period', array('' => 'Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                            <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">




                    <div class="form-group col-sm-2">
                        <label class="title">Currency</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID"  required'); ?>
                    <span class="input-req-inner"></span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button class="btn btn-primary pull-right" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <br>

        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>DISPATCH NOTE DETAILS</h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="dispatchNote_item_add_modal()">
                            <i class="fa fa-plus"></i> Add Item
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="dispatchDetial_item"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="row hidden">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>DISPATCH NOTE ADDON COST</h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="dispatchNote_addon_cost_modal()">
                            <i class="fa fa-plus"></i> Add Addon Cost
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="dispatchDetial_addonCost"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="step2" class="tab-pane">
        <div id="confirm_body"></div>
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
<div aria-hidden="true" role="dialog" tabindex="-1" id="dispatchNote_add_item_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" ><b>Add Item Detail</b> &nbsp;&nbsp; <input id="farmerBatchName" name="farmerBatchName" size="100" style="border: none" readonly>
                </h5>
            </div>
            <div class="modal-body">
                <div style="font-size: 16px; font-weight: 700;">Current Birds : <input id="currenct" name="currenct" style="border: none" readonly></div>
                <form role="form" id="dispatchNote_add_item_form" class="form-horizontal">
                    <input type="hidden" name="dispatchAutoID" id="dispatchAutoID_edit_itemAdd">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Item Code <?php required_mark(); ?></th>
                            <th style="width: 150px;">UOM <?php required_mark(); ?></th>
                            <th style="width: 150px;">Current Stock</th>
                            <th style="width: 150px;">Qty <?php required_mark(); ?></th>
                            <th style="width: 150px;">Unit Cost <span class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th style="width: 200px;">Comment</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control search f_search"
                                       name="search[]"
                                       placeholder="Item ID, Item Description..." id="f_search_1">
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $uom_arr, 'Each', 'class="form-control umoDropdown"  required'); ?></td>
                            <td><input type="text" name="currentstock[]"
                                       class="form-control currentstockadditem number" id="currentstockadditem" readonly></td>
                            <td><input type="text" name="quantityRequested[]" onfocus="this.select();"
                                       onkeyup="validatetb_row(this)"
                                       class="form-control quantityRequested number" id="quantityrequestedadditem" required></td>
                            <td><input type="text" name="estimatedAmount[]" placeholder="0.00"
                                       onchange="change_amount(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       class="form-control number estimatedAmount"></td>
                            <td><textarea class="form-control" rows="1" name="comment[]" placeholder="Item Comment..."
                                ></textarea></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_dispatchNote_item_form()">Save changes
                </button>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="dispatchNote_edit_item_modal" data-backdrop="static"
     data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Item Detail</h4>
            </div>
            <form role="form" id="dispatchNote_edit_item_form" class="form-horizontal">
                <input type="hidden" name="dispatchAutoID" id="dispatchAutoID_edit_itemEdit">
                <input type="hidden" name="dispatchDetailsID" id="dispatchDetailsID_edit">

                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Item Code <?php required_mark(); ?></th>
                            <th style="width: 150px;">UOM <?php required_mark(); ?></th>
                            <th style="width: 150px;">Current Stock</th>
                            <th style="width: 150px;">Qty <?php required_mark(); ?></th>
                            <th style="width: 150px;">Unit Cost <span
                                    class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th style="width: 200px;">Comment</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" class="form-control "
                                       name="search" id="search"
                                       placeholder="Item ID, Item Description...">
                                <input type="hidden" class="form-control itemAutoID" id="itemAutoID" name="itemAutoID">
                            </td>
                            <td>
                                <?php echo form_dropdown('UnitOfMeasureID', $uom_arr, 'Each', 'class="form-control" id="UnitOfMeasureID"'); ?>
                            </td>
                            <td><input type="text" name="currentstock[]"
                                       class="form-control currentstockedititem number" id="currentstockedititem" readonly></td>
                            <td>
                                <input type="text" name="quantityRequested" onfocus="this.select();" onkeyup="chk_current_stock()"
                                       id="quantityRequested"
                                       class="form-control number">
                            </td>
                            <td>
                                <?php if (isset($this->common_data['company_policy']['CPG']['GRV'][0]["policyvalue"]) == 0) { ?>
                                    <input type="text" name="estimatedAmount" onfocus="this.select();"
                                           id="estimatedAmount" value="00"
                                           class="form-control number" readonly>
                                <?php } else { ?>
                                    <input type="text" name="estimatedAmount"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           onfocus="this.select();" id="estimatedAmount" value="00"
                                           class="form-control number">
                                <?php } ?>
                            </td>
                            <td>
                                <textarea class="form-control" rows="1" name="comment" placeholder="Item Comment..."
                                          id="comment"></textarea>
                            </td>
                            <td style="display: none;">
                                <textarea class="form-control" rows="1" name="remarks" placeholder="Item Remarks ..."
                                          id="remarks"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="update_dispatchNote_item_form()">Save changes
                </button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="dispatchNote_addon_cost_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="dispatchNote_addon_cost_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Addon Cost</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Addon Category</label>

                        <div class="col-sm-5">
                             <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('addonCatagory', buyback_addon_catagory(), '', 'class="form-control select2" id="addonCatagory" required'); ?>
                                 <span class="input-req-inner"></span>
                            <input type="hidden" class="form-control" id="dispatchAddonAutoID_edit" name="dispatchAddonAutoID">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="referenceNo" class="col-sm-4 control-label">Reference No</label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="referenceNo" name="referenceNo">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Amount</label>

<!--                        <div class="col-sm-2">
                            <span class="input-req" title="Required Field">
                            <?php /*echo form_dropdown('bookingCurrencyID', $currency_arr, '', 'class="form-control select2" id="bookingCurrencyID" required'); */?>
                                <span class="input-req-inner"></span>
                        </div>-->
                        <div class="col-sm-5">
                            <span class="input-req" title="Required Field">
                            <input type="text" class="form-control number" id="total_amount" value="0"
                                   name="total_amount">
                                <span class="input-req-inner"></span>
                        </div>
                    </div>
                    <div class="form-group show_gl">
                        <label class="col-sm-4 control-label">GL Code</label>

                        <div class="col-sm-5">
                            <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('GLAutoID', buyback_all_gl_codes(), '', 'class="form-control select2" id="GLAutoID"'); ?>
                                <span class="input-req-inner"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="dispatchSubItem_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="dispatchSubItemTitle">Sub Item Configuration</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="dispatchSubItemView_form"'); ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <div id="dispatchSubItem_view"></div>
                    </div>
                </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_dispatchNote_sub_items()">Save Sub Item</button>
            </div>
        </div>
    </div>
</div>

<script>
    var documentCurrency;
    var dispatchAutoID;
    var search_id = 1;
    var currency_decimal = 1;
    var dispatchDetailsID;
    var batchMasterID;
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/buyback/dispatch_note', '', 'Dispatch Note')
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });

        $('.select2').select2();

        Inputmask().mask(document.querySelectorAll("input"));

        documentCurrency = null;
        dispatchAutoID = null;
        dispatchDetailsID = null;
        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            dispatchAutoID = p_id;
            load_dispatchNote_header();
            $("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + dispatchAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + dispatchAutoID + '/GRV');
            $('.btn-wizard').removeClass('disabled');
        } else {

            $('.btn-wizard').addClass('disabled');
            $('.addTableView').addClass('hide');

        }

        $('#advance_batchMasterID').change(function () {
            batchMasterID = $(this).val();
        });

        initializeitemTypeahead(search_id);
        number_validation();
        currency_decimal = 2;

        $('#dispatchNote_header_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                segment: {validators: {notEmpty: {message: 'Segment is required.'}}},
                dispatchType: {validators: {notEmpty: {message: 'Issued From is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                deliveredDate: {validators: {notEmpty: {message: 'Delivered Date is required.'}}},
                location: {validators: {notEmpty: {message: 'Delivery Location is required.'}}},
                financeyear: {validators: {notEmpty: {message: 'Financial Year is required.'}}},
                financeyear_period: {validators: {notEmpty: {message: 'Financial Period is required.'}}},
                dispatchedDate: {validators: {notEmpty: {message: 'Dispatched Date is required.'}}},
                farmID: {validators: {notEmpty: {message: 'Farm ID is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#segment").prop("disabled", false);
            $("#farmID").prop("disabled", false);
            $("#batchMasterID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#dispatchedDate").prop("disabled", false);
            $("#location").prop("disabled", false);
            $("#financeyear").prop("disabled", false);
            $("#financeyear_period").prop("disabled", false);
            $("#deliveredDate").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'delivery_location', 'value': $('#location option:selected').text()});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Buyback/save_dispatch_note_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        dispatchAutoID = data[2];
                        $('#dispatchAutoID_edit').val(dispatchAutoID);
                        $('#dispatchAutoID_edit_itemAdd').val(dispatchAutoID);
                        $('#dispatchAutoID_edit_itemEdit').val(dispatchAutoID);
                        $('.addTableView').removeClass('hide');
                        batchMasterID = $("#batchMasterID").val();
                        getDispatchDetailItem_tableView(dispatchAutoID);
                        //getDispatchDetailAddonCost_tableView(dispatchAutoID);
                        $('.btn-wizard').removeClass('disabled');
                        load_confirmation();
                        //$('[href=#step2]').tab('show');

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

        $('#dispatchNote_addon_cost_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                addonCatagory: {validators: {notEmpty: {message: 'Addon Category is required.'}}},
                bookingCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                total_amount: {validators: {notEmpty: {message: 'Unit Cost is required.'}}},
                GLAutoID: {validators: {notEmpty: {message: 'GL Code is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'dispatchAutoID', 'value': dispatchAutoID});
            data.push({'name': 'glcode_dec', 'value': $('#GLAutoID option:selected').text()});
            data.push({'name': 'booking_code', 'value': $('#bookingCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Buyback/save_dispatchNote_addon'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        $('#dispatchNote_addon_cost_modal').modal('hide');
                        getDispatchDetailAddonCost_tableView(dispatchAutoID);

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

    function getDispatchDetailItem_tableView(dispatchAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {dispatchAutoID: dispatchAutoID},
            url: "<?php echo site_url('Buyback/load_dispatch_detail_items_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#dispatchDetial_item').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function disableDispatchcolumn() {
        $("#segment").prop("disabled", true);
        $("#farmID").prop("disabled", true);
        $("#batchMasterID").prop("disabled", true);
        $("#transactionCurrencyID").prop("disabled", true);
        $("#dispatchedDate").prop("disabled", true);
        $("#location").prop("disabled", true);
        $("#financeyear").prop("disabled", true);
        $("#financeyear_period").prop("disabled", true);
        $("#deliveredDate").prop("disabled", true);

    }

    function enableDispatchcolumn() {
        $("#segment").prop("disabled", false);
        $("#farmID").prop("disabled", false);
        $("#batchMasterID").prop("disabled", false);
        $("#transactionCurrencyID").prop("disabled", false);
        $("#dispatchedDate").prop("disabled", false);
        $("#location").prop("disabled", false);
        $("#financeyear").prop("disabled", false);
        $("#financeyear_period").prop("disabled", false);
        $("#deliveredDate").prop("disabled", false);

    }

    function getDispatchDetailAddonCost_tableView(dispatchAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {dispatchAutoID: dispatchAutoID},
            url: "<?php echo site_url('Buyback/load_dispatch_detail_addonCost_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#dispatchDetial_addonCost').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_farmer_currencyID(farmID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'farmID': farmID},
            url: "<?php echo site_url('Buyback/fetch_farmer_currencyID'); ?>",
            success: function (data) {
             /*   if (documentCurrency) {
                    $("#transactionCurrencyID").val(documentCurrency).change()
                } else {*/
                    if (data.farmerCurrencyID) {
                        $("#transactionCurrencyID").val(data.farmerCurrencyID).change();

                }

            }
        });
    }

    function fetch_farmerMasterDetails(farmID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'farmID': farmID},
            url: "<?php echo site_url('Buyback/fetch_farmerDetails_For_dispatchNote'); ?>",
            success: function (data) {
                if(data){
                    $("#contactPersonName").val(data.contactPerson);
                    $("#contactPersonNumber").val(data.phoneHome);
                }
            }
        });
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
                mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_dispatchNote_header() {
        if (dispatchAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'dispatchAutoID': dispatchAutoID},
                url: "<?php echo site_url('Buyback/load_dispatchNote_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        dispatchAutoID = data['dispatchAutoID'];
                        $('#dispatchAutoID_edit').val(dispatchAutoID);
                        $('#dispatchAutoID_edit_itemAdd').val(dispatchAutoID);
                        $('#dispatchAutoID_edit_itemEdit').val(dispatchAutoID);
                        $('.currency').html('( ' + data['transactionCurrency'] + ' )');
                        $('#dispatchedDate').val(data['documentDate']);
                        $('#dispatchType').val(data['dispatchType']);
                        $('#deliveredDate').val(data['dispatchedDate']);
                        documentCurrency = data['transactionCurrencyID'];
                        $("#supplierID").val(data['supplierID']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#narration').val(data['Narration']);
                        $('#location').val(data['wareHouseAutoID']).change();
                        $('#contactPersonName').val(data['contactPersonName']);
                        $('#contactPersonNumber').val(data['contactPersonNumber']);
                        $('#referenceno').val(data['referenceNo']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        $('#farmID').val(data['farmID']).change();
                        batchMasterID = data['batchMasterID'];
                        $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        setTimeout(function () {
                            $('#batchMasterID').val(data['batchMasterID']).change();
                        }, 500);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        getDispatchDetailItem_tableView(data['dispatchAutoID']);
                        getDispatchDetailAddonCost_tableView(data['dispatchAutoID']);
                        load_confirmation();

                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function dispatchNote_addon_cost_modal() {
        $("#addonCatagory").val('').trigger('change');
        $("#supplier").val('').trigger('change');
        $("#bookingCurrencyID").val('').trigger('change');
        $('#dispatchNote_addon_cost_form')[0].reset();
        $('#dispatchNote_addon_cost_form').bootstrapValidator('resetForm', true);
        //fetch_all_item();
        $('#addon_supplier').attr("readonly", false);
        $("#id").val("");
        $("#paid_by").val("paid_by_company");
        $("#addon_qty").val(1);
        $("#isChargeToExpense").val(0);
        $("#addon_uom").val("Each");
        $("#dispatchNote_addon_cost_modal").modal({backdrop: "static"});
    }

    function load_confirmation() {
        var farmid = $('#farmID').val();
        if (dispatchAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'dispatchAutoID': dispatchAutoID,'farmid': farmid,'batchid':batchMasterID, 'html': true},
                url: "<?php echo site_url('Buyback/load_dispatchNote_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#confirm_body').html(data);
                    //$("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + dispatchAutoID);
                    //$("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + dispatchAutoID + '/GRV');
                    attachment_modal_dispatchNote(dispatchAutoID, "Dispatch Note", "BBDPN");
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function dispatchNote_item_add_modal() {
        if (dispatchAutoID)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'dispatchAutoID': dispatchAutoID},
                url: "<?php echo site_url('Buyback/fetchBatchChicksTotal'); ?>",
                beforeSend: function ()
                {
                    startLoad();
                },
                success: function (data)
                {
                    stopLoad();
                    $('.umoDropdown').attr('disabled', true);
                    $('#dispatchNote_add_item_form')[0].reset();
                    $('#currenct').val(data['chickTotal']);
                    $('#farmerBatchName').val(data['farmer']);
                    $('#discount').val(0);
                    $('#discount_amount').val(0);
                    $('#po_detail_add_table tbody tr').not(':first').remove();
                    $("#dispatchNote_add_item_modal").modal({backdrop: "static"});
                    $('.f_search').closest('tr').css("background-color", 'white');
                    $('.deliverydat').closest('tr').css("background-color", 'white');
                    $('.quantityRequested').closest('tr').css("background-color", 'white');
                }, error: function ()
                {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function initializeitemTypeahead(id) {
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Buyback/fetch_buyback_item_recode/',
            onSelect: function (suggestion) {

                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                /*fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);*/// Commented by shafry as Muba said not using this.
                fetch_related_uom_id_b(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this, suggestion['isSubitemExist']);
                $(this).closest('tr').find('.estimatedAmount').val(suggestion.companyLocalSellingPrice);
                $(this).closest('tr').find('.currentstockadditem').val(suggestion.currentStock);
                $(this).closest('tr').css("background-color", 'white');
                //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);

            }
        });

    }

    function initializeitemTypeahead_edit() {

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Buyback/fetch_buyback_item_recode/',
            onSelect: function (suggestion) {
                /*fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);*/ // Commented by shafry as Muba said not using this.
                fetch_related_uom_id_b(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this, suggestion['isSubitemExist']);
                // $(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                setTimeout(function () {
                    $('#search').closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
            }
        });
    }

    function add_more() {
        search_id += 1;
        //$('select.select2').select2('destroy');
        var appendData = $('#po_detail_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.umoDropdown').prop('disabled', false);
        appendData.find('.umoDropdown').removeClass('uom_disabled');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#po_detail_add_table').append(appendData);
        var lenght = $('#po_detail_add_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');

        //$(".select2").select2();
        initializeitemTypeahead(search_id);
        number_validation();
    }

    function fetch_related_uom_id(masterUnitID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('Dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $('#UnitOfMeasureID').empty();
                var mySelect = $('#UnitOfMeasureID');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $("#UnitOfMeasureID").val(select_value);
                        //$('#grv_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasureID');
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_related_uom_id_b(masterUnitID, select_value, element, isSubItemExist) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('Dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty();

                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');

                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        if (isSubItemExist == 1) {
                            $(element).closest('tr').find('.umoDropdown').prop('disabled', true);
                            $(element).closest('tr').find('.umoDropdown').addClass('uom_disabled', true);
                        }
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_farmBatch(farmID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {farmID: farmID},
            url: "<?php echo site_url('Buyback/fetch_farm_BatchesDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_loadBatch').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function save_dispatchNote_item_form() {
        $('.umoDropdown').attr('disabled', false);
        var data = $("#dispatchNote_add_item_form").serializeArray();
        $('#dispatchNote_add_item_form' + ' select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });

        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $('.quantityRequested').each(function () {
            if (this.value == '' || this.value == 0) {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/save_dispatchNote_item_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.uom_disabled').prop('disabled', true);
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    dispatchDetailsID = null;

                    $('#dispatchNote_add_item_modal').modal('hide');
                    setTimeout(function () {
                        getDispatchDetailItem_tableView(dispatchAutoID);
                    }, 300);
                   // dispatchAutoID = null;
                }
            }, error: function () {
                $('.uom_disabled').prop('disabled', true);
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function update_dispatchNote_item_form() {
     /*   $('#UnitOfMeasureID').attr('disabled', false);*/
        var data = $("#dispatchNote_edit_item_form").serializeArray();
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/update_dispatchNote_item_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $("#dispatchNote_edit_item_modal").modal('hide');
                    getDispatchDetailItem_tableView(dispatchAutoID);
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }

    function clearitemAutoIDEdit(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('#itemAutoID_edit').val('');
        }
    }

    function edit_dispatchNote_item(id) {
        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", 'white');
            }
        });
        $('.quantityRequested').each(function () {
            if (this.value == '' || this.value == 0) {
                $(this).closest('tr').css("background-color", 'white');
            }
        });
        if (dispatchAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to edit this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Edit"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'dispatchDetailsID': id},
                        url: "<?php echo site_url('Buyback/fetch_dispatchNote_item_detail'); ?>",
                        beforeSend: function () {
                            $("#dispatchNote_edit_item_modal").modal('show');
                            startLoad();
                        },
                        success: function (data) {
                            dispatchDetailsID = data['dispatchDetailsID'];
                            $('#dispatchDetailsID_edit').val(data['dispatchDetailsID']);
                            $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            fetch_related_uom_id(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested').val(data['qty']);
                            $('#estimatedAmount').val(data['unitTransferCost']);
                            $('#search_id').val(data['itemSystemCode']);
                            $('#itemSystemCode').val(data['itemSystemCode']);
                            $('#itemAutoID').val(data['itemAutoID']);
                            $('#itemDescription').val(data['itemDescription']);
                            $('#comment').val(data['comment']);
                            $('#remarks').val(data['remarks']);
                            $('#currentstockedititem').val(data['itemstock']);
                         /*   $('#UnitOfMeasureID').attr('disabled', true);*/
                            initializeitemTypeahead_edit();
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function delete_dispatchNote_item(id) {
        if (dispatchAutoID) {
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
                        data: {'dispatchDetailsID': id},
                        url: "<?php echo site_url('Buyback/delete_dispatchNote_detail_item'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            getDispatchDetailItem_tableView(dispatchAutoID);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function delete_dispatchNote_addon(id){
        if (dispatchAutoID) {
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
                        data: {'dispatchAddonAutoID': id},
                        url: "<?php echo site_url('Buyback/delete_dispatchNote_detail_addon'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            getDispatchDetailAddonCost_tableView(dispatchAutoID);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_dispatchNote_addon(id) {
        if (dispatchAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to edit this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Edit"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'dispatchAddonAutoID': id},
                        url: "<?php echo site_url('Buyback/fetch_dispatchNote_addonCost_detail'); ?>",
                        beforeSend: function () {
                            $("#dispatchNote_addon_cost_modal").modal('show');
                            startLoad();
                        },
                        success: function (data) {
                            //dispatchDetailsID = data['dispatchDetailsID'];
                            $('#dispatchAddonAutoID_edit').val(data['dispatchAddonAutoID']);
                            $('#addonCatagory').val(data['addonCatagory']).change();
                            $('#referenceNo').val(data['referenceNo']);
                            //$('#bookingCurrencyID').val(data['transactionCurrencyID']).change();
                            $('#total_amount').val(data['total_amount']);
                            $('#GLAutoID').val(data['GLAutoID']).change();
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function attachment_modal_dispatchNote(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                beforeSend: function () {
                    $('#purchaseOrder_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " Attachments");
                },
                success: function (data) {
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

    function save_draft() {
        if (dispatchAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/buyback/dispatch_note', dispatchAutoID, 'Dispatch Note');
                });
        }
    }

    function confirmation() {
        if (dispatchAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want confirm this document!",
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
                        data: {'dispatchAutoID': dispatchAutoID},
                        url: "<?php echo site_url('Buyback/dispatch_note_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            }else if (data['error'] == 2)
                            {
                                myAlert('w', data['message']);
                            }

                            else {
                                myAlert('s', data['message']);
                                fetchPage('system/buyback/dispatch_note', dispatchAutoID, 'Dispatch Note');
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
    function validatetb_row(det) {
        var currentStock= $(det).closest('tr').find('.currentstockadditem').val();
        if(det.value > parseFloat(currentStock))
        {
            myAlert('w','Quantity should be less than or equal to current stock')
            $(det).val('');
        }

        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }
    function chk_current_stock() {
            var currentStock=$('#currentstockedititem').val();
            var qtyrequested=$('#quantityRequested').val();
            if(parseFloat(qtyrequested) > parseFloat(currentStock)){
                myAlert('w','Quantity should be less than or equal to current stock');
                $('#quantityRequested').val('');
            }

    }

    function save_item_subID_view(id){
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Buyback/load_sub_itemDispatch_view"); ?>',
            dataType: 'html',
            data: {dispatchDetailsID: id,  type: ''},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#dispatchSubItem_view').html(data);
                $("#dispatchSubItem_model").modal({backdrop: "static"});
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // $('#dispatchSubItem_view').html(xhr.responseText);

            }
        });
    }

    function save_dispatchNote_sub_items(){
        var data = $("#dispatchSubItemView_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/save_dispatchNote_sub_items'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#dispatchSubItem_model').modal('hide');
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


</script>
