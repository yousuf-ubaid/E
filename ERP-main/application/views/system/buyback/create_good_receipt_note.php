<?php echo head_page($_POST['page_name'], false);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$segment_arr = fetch_segment();
$supplier_arr = all_supplier_drop();
$farms_arr = load_all_farms();
$vehicle_arr = load_all_fleet_vehicles();
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
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Goods Received Note Header</a>
    <a class="btn btn-default btn-wizard" href="#step2"  data-toggle="tab">Step 2 - Goods Received Note Transport Details</a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_confirmation();" data-toggle="tab">Step 3 - Goods Received Note Confirmation</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="GoodReceiptNote_header_form" autocomplete="off"'); ?>
        <input type="hidden" name="grnAutoID" id="grnAutoID_edit">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>GOODS RECEIVED NOTE HEADER</h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">GRN Type</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('grnType', array('' => 'Select Type', '1' => 'Buyback'), '', 'class="form-control" id="grnType"'); //, '2' => 'Others'?>
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
                    <div id="ShowFarm">
                        <div class="form-group col-sm-2">
                            <label class="title">Farm</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                <?php echo form_dropdown('farmID', $farms_arr, '', 'class="form-control select2" id="farmID" onchange="fetch_farmer_currencyID(this.value),fetch_farmBatch(this.value),fetch_farmerMasterDetails(this.value)"'); ?>
                                <span class="input-req-inner"></span></span>

                        </div>
                    </div>
                     <div id="ShowParty" class="hide">
                        <div class="form-group col-sm-2">
                            <label class="title">Party</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                 <input type="text" class="form-control " id="partyName" name="partyName">
                                <span class="input-req-inner"></span></span>

                        </div>
                    </div>
                    <div id="showBatch">
                        <div class="form-group col-sm-2">
                            <label class="title">Batch</label>
                        </div>
                        <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <div id="div_loadBatch">
                                <?php echo form_dropdown('batchMasterID', $batch_arr, 'Each', 'class="form-control" '); ?>
                            </div>
                            <span class="input-req-inner"></span></span>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Received Date</label>

                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="documentDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
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
                        <label class="title">Delivery To</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('location', $location_arr, $location_arr_default, 'class="form-control select2" id="location" required'); ?>
                            <span class="input-req-inner"></span>
                    </div>
                </div>
                <div class="row hidden" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Driver Name</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <?php
                        echo form_dropdown('driverID', all_employee_drop(), '', 'class="form-control select2" id="driverID"'); ?>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Helper Name</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <?php
                        echo form_dropdown('helperID', all_employee_drop(), '', 'class="form-control select2" id="helperID"'); ?>
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
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" required'); ?>
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
                    <h2>GOODS RECEIVED NOTE DETAILS</h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="goodReceiptNote_item_add_detailModal()">
                            <i class="fa fa-plus"></i> Add Item
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="GoodReceiptNote_Detial_item"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
    <div id="step2" class="tab-pane">
        <?php echo form_open('', 'role="form" id="GoodReceiptNote_transport_form" autocomplete="off"'); ?>
        <input type="hidden" name="grnAutoID" id="grnAutoID_transport_edit">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>GOODS RECEIVED NOTE TRANSPORT DETAILS</h2>
                </header>
                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title">Trip No</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control number" id="tripNo" name="tripNo">
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Vehicle</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="VehicleName" name="VehicleName" required>
                            <input type="hidden" class="form-control" id="VehicleID" name="VehicleID">
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clear_vehicle()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Vehicle" rel="tooltip"
                                onclick="link_vehicle_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title">Driver Name</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="driverName" name="driverName" required>
                            <input type="hidden" class="form-control" id="DriverID" name="DriverID">
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clear_driver()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Driver" rel="tooltip"
                                onclick="link_driver_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Journey Time</label>
                    </div>
                    <div class="form-group col-sm-2" style="margin-top: 5px;">
                        <input type="hidden" id="JourneyStart" value="">
                        <input type="hidden" id="JourneyEnd" value="">
                        <input type="text" class="form-control input-sm startdateDatepic" id="JourneyFrom"
                               name="JourneyFrom" placeholder="Journey Starts" style="width: 150px;">
                    </div>
                    <div class="form-group col-sm-2" style="margin-top: 5px;">
                        <input type="text" class="form-control input-sm startdateDatepic" id="JourneyTo"
                               placeholder="Journey Ends" name="JourneyTo">
                    </div>
                </div>

                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title">Whether Condition</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="whetherCondition" name="whetherCondition">
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Transport Mortality</label>
                    </div>
                    <div class="form-group col-sm-4">
                            <input type="text" class="form-control number" id="MortalChickstrnspt" name="MortalChickstrnspt">
                    </div>
                </div>

                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title">feed in crop deduction</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="cropDeduction" name="cropDeduction">
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Helper Name</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="helperOne" name="helperOne" data-placeholder="Helper one">
                            <input type="hidden" class="form-control" id="HelperIDOne" name="HelperIDOne">
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clear_helper(1)"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Helper" rel="tooltip"
                                onclick="link_helper_model(1)"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title">Meter Reading</label>
                    </div>
                    <div class="form-group col-sm-2" style="">
                        <input type="text" class="form-control input-sm number" id="meterStart"
                               name="meterStart" placeholder="Start Meter Reading" style="width: 150px;">
                    </div>
                    <div class="form-group col-sm-2" style="">
                        <input type="text" class="form-control input-sm number" id="meterEnd"
                               placeholder="End Meter Reading" name="meterEnd">
                    </div>
                    <div class="form-group col-sm-2">
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="helperTwo" name="helperTwo" data-placeholder="Helper two">
                            <input type="hidden" class="form-control" id="HelperIDTwo" name="HelperIDTwo">
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clear_helper(2)"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Helper" rel="tooltip"
                                onclick="link_helper_model(2)"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title">Comment</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea type="text" class="form-control " id="TransportComment" name="TransportComment"></textarea>
                    </div>
                    <div class="form-group col-sm-2">
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="helperThree" name="helperThree" data-placeholder="Helper three">
                            <input type="hidden" class="form-control" id="HelperIDThree" name="HelperIDThree">
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clear_helper(3)"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Helper" rel="tooltip"
                                onclick="link_helper_model(3)"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right m-t-xs" style="margin: 10px;">
                        <button class="btn btn-primary" type="submit">Save</button>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div id="step3" class="tab-pane">
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

<div aria-hidden="true" role="dialog" tabindex="-1" id="goodReceiptNote_add_item_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg modal_resize" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" ><b>Add Item Detail</b> &nbsp;&nbsp; <input id="farmerBatchName" name="farmerBatchName" size="100" style="border: none" readonly>
                </h5>
            </div>
            <div class="modal-body">
                <form role="form" id="goodReceiptNote_add_item_form" class="form-horizontal">
                    <input type="hidden" name="grnAutoID" id="grnAutoID_edit_itemAdd">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Item Code <?php required_mark(); ?></th>
                            <th id="qtyChicksShow" style="width: 150px;">Qty Live Stock<?php required_mark(); ?></th>
                            <th style="width: 150px;">UOM <?php required_mark(); ?></th>
                            <th id="mortalityShow" style="width: 100px;">Mortality <?php required_mark(); ?></th>
                            <th id="returnShow" style="width: 100px;">Return <?php required_mark(); ?></th>
                            <th id="balanceShow" style="width: 100px;">Balance <?php required_mark(); ?></th>
                            <th colspan="3" style="width: 200px;">Qty Received</th>
                            <th style="width: 40px;">
                                <!--<button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>-->
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
                            <td id="valueqtyChicksShow"><input type="text" name="qtybirds[]" onfocus="this.select();"
                                      class="form-control number qtybirds" required readonly></td>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $uom_arr, 'Each', 'class="form-control umoDropdown"  required disabled'); ?></td>

                            <td id="valuemortalityShow"><input type="text" name="mortality[]" onfocus="this.select();"
                                       class="form-control number mortality" required readonly></td>

                            <td id="valuereturnShow"><input type="text" name="return[]" onfocus="this.select();"
                                       class="form-control number return" required readonly></td>

                            <td id="valuebalanceShow"><input type="text" name="balance[]" class="form-control number balance" readonly></td>

                            <td><input type="text" name="noofbirds[]" onfocus="this.select();"
                                       class="form-control number noofbirds"  onkeyup="birdvalidation(this)" placeholder="No of Birds"></td>
                            <td><input type="text" name="kgweight[]" onfocus="this.select();"
                                       class="form-control number kgweight" placeholder="KG"></td>
                            <td><input type="text" name="Amount[]" onfocus="this.select();"
                                       class="form-control number Amount" placeholder="Amount"></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_GoodReceiptNote_item()">Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="goodReceiptNote_edit_item_modal" data-backdrop="static"
     data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Item Detail</h4>
            </div>
            <form role="form" id="goodReceiptNote_edit_item_form" class="form-horizontal">
                <input type="hidden" name="grnAutoID" id="grnAutoID_edit_itemEdit">
                <input type="hidden" name="grnDetailsID" id="grnDetailsID_edit">

                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="">
                        <thead>
                        <tr>
                            <th style="width: 250px;">Item Code <?php required_mark(); ?></th>
                            <th id="qtyChicksShowEdit" style="width: 150px;">Qty Chicks<?php required_mark(); ?></th>
                            <th style="width: 150px;">UOM <?php required_mark(); ?></th>
                            <th id="mortalityShowEdit" style="width: 100px;">Mortality <?php required_mark(); ?></th>
                            <th id="returnShowEdit" style="width: 100px;">Return <?php required_mark(); ?></th>
                            <th id="balanceShowEdit" style="width: 100px;">Balance <?php required_mark(); ?></th>
                            <th colspan="3" style="width: 200px;">Qty Received</th>
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
                            <td id="valueqtyChicksShowEdit"><input type="text" name="qtybirds" onfocus="this.select();"
                                       class="form-control number qtybirds" id="qtybirds_edit" required readonly></td>
                            <td><?php echo form_dropdown('UnitOfMeasureID', $uom_arr, 'Each', 'class="form-control umoDropdown" id="UnitOfMeasureID"  required disabled'); ?></td>

                            <td id="valuemortalityShowEdit"><input type="text" name="mortality" onfocus="this.select();"
                                       class="form-control number mortality" id="mortality_edit" required readonly>

                            <td id="valuereturnShowEdit"><input type="text" name="returnedit" onfocus="this.select();"
                                       class="form-control number mortality" id="return_edit" required readonly></td>

                            <td id="valuebalanceShowEdit"><input type="text" name="balance" class="form-control number balance" id="balance_edit" readonly></td>

                            <td><input type="text" name="noofbirds" onfocus="this.select();"
                                       class="form-control number noofbirds"  onkeyup="birdvalidation(this)" id="noofbirds_edit" placeholder="No of Birds"></td>
                            <td><input type="text" name="kgweight" onfocus="this.select();"
                                       class="form-control number kgweight" placeholder="KG" id="kgweight_edit"></td>
                            <td><input type="text" name="Amount" onfocus="this.select();"
                                       class="form-control number Amount" id="amount_edit" placeholder="Amount"></td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="update_goodReceiptNote_item()">Update changes
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="vehicle_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Link Vehicle</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3"
                               class="col-sm-3 control-label">Vehicle </label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('vehicle_id', $vehicle_arr, '', 'class="form-control select2" id="vehicle_id" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="fetch_vehicle_detail()">Add Vehicle</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="driver_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Link Employee</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3"
                               class="col-sm-3 control-label">Employee</label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('driver_id', all_employee_drop(), '', 'class="form-control select2" id="driver_id" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="fetch_driver_detail()">Add Driver</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="helper_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Link Employee</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <input type="hidden" id="helperNo" name="helperNo">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3"
                               class="col-sm-3 control-label">Employee</label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('helper_id', all_employee_drop(), '', 'class="form-control select2" id="helper_id" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="fetch_helper_detail()">Add Helper</button>
            </div>
        </div>
    </div>
</div>

<!--<div class="modal fade" id="goodReceiptNote_changed_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="goodReceiptNote_changed_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Good Receipt Note Detail</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="referenceNo" class="col-sm-4 control-label">Item</label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="detail_itemDescription"
                                   name="detail_itemDescription" readonly>
                            <input type="hidden" name="itemAutoID" id="detail_itemAutoID">
                            <input type="hidden" name="grnDetailsID" id="detail_grnDetailsID">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="referenceNo" class="col-sm-4 control-label">Qty Chicks</label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="detail_qtyChicks"
                                   name="detail_qtyChicks" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="referenceNo" class="col-sm-4 control-label">Mortality</label>

                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="detail_qtyMortality"
                                   name="detail_qtyMortality" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="referenceNo" class="col-sm-4 control-label">Qty Received</label>

                        <div class="col-sm-2">
                            <span class="input-req" title="Required Field">
                            <input type="text" class="form-control" id="detail_qtyRecived_birds"
                                   name="noOfBirds" placeholder="No Of Birds">
                                <span class="input-req-inner"></span>
                        </div>
                        <div class="col-sm-3">
                            <span class="input-req-inner"></span>

                            <div class="input-group">
                                <span class="input-group-addon input-group-addon-mini d_uom">Kg</span>
                                <input type="text" name="quantityRequested"
                                       class="form-control" id="detail_qtyRecived_weight">
                                <input type="hidden" name="UnitOfMeasureID"
                                       class="form-control" id="detail_UnitOfMeasureID">
                            </div>
                            <span class="input-req-inner" style="z-index: 10;"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="referenceNo" class="col-sm-4 control-label">Amount</label>

                        <div class="col-sm-5">
                            <span class="input-req" title="Required Field">
                            <input type="text" class="form-control number" id="detail_amount"
                                   name="estimatedAmount">
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
</div>-->


<div class="modal fade" id="grn_SubItem_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
    <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="grn_SubItemTitle">Sub Item Configuration</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="grn_SubItemView_form"'); ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <div id="grn_SubItem_view"></div>
                    </div>
                </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_grn_sub_items()">Update Sub Item GRN</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="grnView_SubItem_model"  role="dialog" aria-labelledby="myModalLabel" >
    <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="grn_SubItemTitle">Sub Item Configuration</h4>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <div id="grnView_SubItem_view"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    var documentCurrency;
    var grnAutoID;
    var search_id = 1;
    var currency_decimal = 1;
    var batchMasterID;
    var grnDetailsID;
    $(document).ready(function () {

        $('.startdateDatepic').datetimepicker({
            format: "DD/MM/YYYY hh:mm A",
        });

        $('.headerclose').click(function () {
            fetchPage('system/buyback/good_received_note', '', 'GRN')
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
        grnAutoID = null;
        grnDetailsID = null;

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            grnAutoID = p_id;
            load_GoodReceiptNote_header();
            $("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + grnAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + grnAutoID + '/GRV');
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            $('.addTableView').addClass('hide');

        }

        initializeitemTypeahead(search_id);
        number_validation();
        currency_decimal = 2;

        $('#GoodReceiptNote_header_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                segment: {validators: {notEmpty: {message: 'Segment is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                deliveredDate: {validators: {notEmpty: {message: 'Delivered Date is required.'}}},
                location: {validators: {notEmpty: {message: 'Delivery Location is required.'}}},
                financeyear: {validators: {notEmpty: {message: 'Financial Year is required.'}}},
                financeyear_period: {validators: {notEmpty: {message: 'Financial Period is required.'}}},
                documentDate: {validators: {notEmpty: {message: 'Document Date is required.'}}},
             //   farmID: {validators: {notEmpty: {message: 'Farm ID is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#segment").prop("disabled", false);
            $("#farmID").prop("disabled", false);
            $("#partyName").prop("disabled", false);
            $("#grnType").prop("disabled", false);
            $("#batchMasterID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#location").prop("disabled", false);
            $("#financeyear").prop("disabled", false);
            $("#financeyear_period").prop("disabled", false);
            $("#deliveredDate").prop("disabled", false);
            $("#documentDate").prop("disabled", false);
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
                url: "<?php echo site_url('Buyback/save_good_receipt_note_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        grnAutoID = data[2];
                        $('#grnAutoID_edit').val(grnAutoID);
                        $('#grnAutoID_transport_edit').val(grnAutoID);
                        $('.addTableView').removeClass('hide');
                        $('#grnAutoID_edit_itemAdd').val(grnAutoID);
                        $('#grnAutoID_edit_itemEdit').val(grnAutoID);
                        get_GoodReceiptNoteDetailItem_tableView(grnAutoID);
                        $('.btn-wizard').removeClass('disabled');
                        $('.btn-wizard').removeClass('disabled');
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

        $('#GoodReceiptNote_transport_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                driverName: {validators: {notEmpty: {message: 'Driver.'}}},
                VehicleName: {validators: {notEmpty: {message: 'Vehicle.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
           // data.push({'name': 'grnAutoID', 'value': grnAutoID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Buyback/save_grn_transportDetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                      //  $('#goodReceiptNote_changed_model').modal('hide');
                        get_GoodReceiptNoteDetailItem_tableView(grnAutoID);
                        load_confirmation();
                        $('[href=#step3]').tab('show');
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

    $("#grnType").change(function () {
        if (this.value == 2) {
            $('#ShowFarm').addClass('hide');
            $('#showBatch').addClass('hide');
            $('#ShowParty').removeClass('hide');
        }else {
            $('#ShowParty').addClass('hide');
            $('#ShowFarm').removeClass('hide');
            $('#showBatch').removeClass('hide');
        }
    });

    function clear_vehicle() {
        $('#VehicleID').val('').change();
        $('#VehicleName').val('').trigger('input');
        $('#VehicleName').prop('readonly', false);
        EIdNo = null;
    }
    function link_vehicle_model() {
        /*$('#employee_id').val('').change();*/
        $('#vehicle_model').modal('show');
    }
    function fetch_vehicle_detail() {
        var vehicle_id = $('#vehicle_id').val();
        if (vehicle_id == '') {
            //swal("", "Select An Employee", "error");
            myAlert('e', 'Select A Vehicle');
        } else {
            EIdNo = vehicle_id;
            var vehName = $("#vehicle_id option:selected").text();
            /*  var empNameSplit = empName.split('|');*/
            $('#VehicleName').val($.trim(vehName)).trigger('input');
            $('#VehicleID').val($.trim(EIdNo));
            $('#VehicleName').prop('readonly', true);
            $('#vehicle_model').modal('hide');
        }
    }

    function clear_driver() {
        $('#DriverID').val('').change();
        $('#driverName').val('').trigger('input');
        $('#driverName').prop('readonly', false);
        EIdNo = null;
    }
    function link_driver_model() {
        /*$('#employee_id').val('').change();*/
        $('#driver_model').modal('show');
    }
    function fetch_driver_detail() {
        var driver_id = $('#driver_id').val();
        if (driver_id == '') {
            //swal("", "Select An Employee", "error");
            myAlert('e', 'Select A Driver');
        } else {
            EIdNo = driver_id;
            var vehName = $("#driver_id option:selected").text();
            /*  var empNameSplit = empName.split('|');*/
            $('#driverName').val($.trim(vehName)).trigger('input');
            $('#DriverID').val($.trim(EIdNo));
            $('#driverName').prop('readonly', true);
            $('#driver_model').modal('hide');
        }
    }

    function clear_helper(val) {
        if(val == 1){
            $('#HelperIDOne').val('').change();
            $('#helperOne').val('').trigger('input');
            $('#helperOne').prop('readonly', false);
            EIdNo = null;
        } else if(val == 2){
            $('#HelperIDTwo').val('').change();
            $('#helperTwo').val('').trigger('input');
            $('#helperTwo').prop('readonly', false);
            EIdNo = null;
        } else if(val == 3){
            $('#HelperIDThree').val('').change();
            $('#helperThree').val('').trigger('input');
            $('#helperThree').prop('readonly', false);
            EIdNo = null;
        }
    }
    function link_helper_model(val) {
        $('#helperNo').val(val);
        $('#helper_id').val('').change();
        if(val == 1){
            var selectedHelper =  $('#HelperIDOne').val();
        } else if(val == 2){
            var selectedHelper =  $('#HelperIDTwo').val();
        } else if(val == 3){
            var selectedHelper =  $('#HelperIDThree').val();
        }
        $('#helper_id').val(selectedHelper).change();
        $('#helper_model').modal('show');
    }
    function fetch_helper_detail() {
        var helperNo = $('#helperNo').val();
        var helper_id = $('#helper_id').val();
        if (helper_id == '') {
            //swal("", "Select An Employee", "error");
            myAlert('e', 'Select A Helper');
        } else {
            /*  var empNameSplit = empName.split('|');*/
            if(helperNo == 1){
                EIdNo = helper_id;
                var vehName = $("#helper_id option:selected").text();
                $('#helperOne').val($.trim(vehName)).trigger('input');
                $('#HelperIDOne').val($.trim(EIdNo));
                $('#helperOne').prop('readonly', true);
                $('#helper_model').modal('hide');
            }
           else if(helperNo == 2){
                EIdNo = helper_id;
                var vehName = $("#helper_id option:selected").text();
                $('#helperTwo').val($.trim(vehName)).trigger('input');
                $('#HelperIDTwo').val($.trim(EIdNo));
                $('#helperTwo').prop('readonly', true);
                $('#helper_model').modal('hide');
            }
            else if(helperNo == 3){
                EIdNo = helper_id;
                var vehName = $("#helper_id option:selected").text();
                $('#helperThree').val($.trim(vehName)).trigger('input');
                $('#HelperIDThree').val($.trim(EIdNo));
                $('#helperThree').prop('readonly', true);
                $('#helper_model').modal('hide');
            }
        }
    }

    function get_GoodReceiptNoteDetailItem_tableView(grnAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {grnAutoID: grnAutoID},
            url: "<?php echo site_url('Buyback/load_GoodReceiptNote_detail_items_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#GoodReceiptNote_Detial_item').html(data);
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
                if (documentCurrency) {
                    $("#transactionCurrencyID").val(documentCurrency).change()
                } else {
                    if (data.farmerCurrencyID) {
                        $("#transactionCurrencyID").val(data.farmerCurrencyID).change();
                    }
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
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_GoodReceiptNote_header() {
        if (grnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'grnAutoID': grnAutoID},
                url: "<?php echo site_url('Buyback/load_good_receiptNote_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        grnAutoID = data['grnAutoID'];
                        $('#grnAutoID_edit').val(grnAutoID);
                        $('#grnAutoID_transport_edit').val(grnAutoID);
                        $('#grnAutoID_edit_itemAdd').val(grnAutoID);
                        $('#grnAutoID_edit_itemEdit').val(grnAutoID);
                        $('.currency').html('( ' + data['transactionCurrency'] + ' )');
                        $('#grnType').val(data['grnType']);
                        $('#dispatchedDate').val(data['documentDate']);
                        $('#deliveredDate').val(data['deliveryDate']);
                        documentCurrency = data['transactionCurrencyID'];
                        $("#supplierID").val(data['supplierID']).change();
                        $("#transactionCurrencyID").val(data['transactionCurrencyID']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#narration').val(data['Narration']);
                        $('#location').val(data['wareHouseAutoID']).change();
                        $('#contactPersonName').val(data['contactPersonName']);
                        $('#contactPersonNumber').val(data['contactPersonNumber']);
                        $('#referenceno').val(data['referenceNo']);
                        $('#financeyear').val(data['companyFinanceYearID']);

                        if(data['grnType'] == 1 || data['grnType'] == 3)
                        {
                            $('#farmID').val(data['farmID']).change();
                            batchMasterID = data['batchMasterID'];
                            setTimeout(function () {
                                $('#batchMasterID').val(batchMasterID);
                            }, 500);
                            $('#ShowParty').addClass('hide');
                            $('#ShowFarm').removeClass('hide');
                            $('#showBatch').removeClass('hide');
                        }else{
                            $('#partyName').val(data['partyName']);
                            $('#ShowFarm').addClass('hide');
                            $('#showBatch').addClass('hide');
                            $('#ShowParty').removeClass('hide');
                        }
                        $('#driverID').val(data['driverID']).change();
                        $('#helperID').val(data['helperID']).change();
                        $('#DriverID').val(data['driverID']);
                        $('#driverName').val(data['DriverName']);
                        $('#VehicleID').val(data['vehicleID']);
                        $('#VehicleName').val(data['vehicleNo']);

                        $('#HelperIDOne').val(data['helperID']);
                        $('#helperOne').val(data['HelperName']);
                        $('#HelperIDTwo').val(data['helperTwoID']);
                        $('#helperTwo').val(data['helperTwoName']);
                        $('#HelperIDThree').val(data['helperThreeID']);
                        $('#helperThree').val(data['helperThreeName']);

                        $('#JourneyFrom').val(data['JourneyStartTime']);
                        $('#JourneyTo').val(data['JourneyEndTime']);
                        $('#meterStart').val(data['startMeterReading']);
                        $('#meterEnd').val(data['endMeterReading']);
                        $('#TransportComment').val(data['transportComment']);
                        $('#tripNo').val(data['tripNo']);
                        $('#whetherCondition').val(data['whetherCondition']);
                        $('#MortalChickstrnspt').val(data['transportMortality']);
                        $('#cropDeduction').val(data['feedInCropDeduction']);

                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        get_GoodReceiptNoteDetailItem_tableView(data['grnAutoID']);
                        load_confirmation();
                        $('[href=#step3]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step3]').removeClass('btn-default');
                        $('[href=#step3]').addClass('btn-primary');
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

    function disablegrncolumn() {
        $("#segment").prop("disabled", true);
        $("#farmID").prop("disabled", true);
        $("#grnType").prop("disabled", true);
        $("#partyName").prop("disabled", true);
        $("#batchMasterID").prop("disabled", true);
        $("#transactionCurrencyID").prop("disabled", true);
        $("#location").prop("disabled", true);
        $("#financeyear").prop("disabled", true);
        $("#financeyear_period").prop("disabled", true);
        $("#deliveredDate").prop("disabled", true);
        $("#documentDate").prop("disabled", true);
    }

    function enablegrncolumn() {
        $("#segment").prop("disabled", false);
        $("#farmID").prop("disabled", false);
        $("#partyName").prop("disabled", false);
        $("#grnType").prop("disabled", false);
        $("#batchMasterID").prop("disabled", false);
        $("#transactionCurrencyID").prop("disabled", false);
        $("#location").prop("disabled", false);
        $("#financeyear").prop("disabled", false);
        $("#financeyear_period").prop("disabled", false);
        $("#deliveredDate").prop("disabled", false);
        $("#documentDate").prop("disabled", false);
    }

    function load_confirmation() {
        if (grnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'grnAutoID': grnAutoID, 'html': true},
                url: "<?php echo site_url('Buyback/load_goodReceiptNote_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#confirm_body').html(data);
                    //$("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + grnAutoID);
                    //$("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + grnAutoID + '/GRV');
                    attachment_modal_dispatchNote(grnAutoID, "Good Receipt Note", "BBGRN");
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

    function goodReceiptNote_item_add_modal() {
        if (grnAutoID) {
            $('#goodReceiptNote_add_item_form')[0].reset();
            $('#po_detail_add_table tbody tr').not(':first').remove();
            $("#goodReceiptNote_add_item_modal").modal({backdrop: "static"});
        }
    }

    function initializeitemTypeahead(id) {
        $('#f_search_' + id ).autocomplete({
            serviceUrl: '<?php echo site_url();?>Buyback/fetch_buyback_item_recode_grn/',
            onSelect: function (suggestion) {
                /*fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);*/// Commented by shafry as Muba said not using this.
                fetch_related_uom_id_b(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this, suggestion['isSubitemExist']);
                fetch_goodReciptNote_batch_chicks(this);
                fetch_goodReciptNote_batch_mortality(this);
                fetch_return_qty_chicks(this);
                fetch_balancechicks(this);

                //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
            }
        });

    }

    function initializeitemTypeahead_edit() {
        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Buyback/fetch_buyback_item_recode_grn/',
            onSelect: function (suggestion) {
                /*fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);*/ // Commented by shafry as Muba said not using this.
                fetch_related_uom_id_b(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this, suggestion['isSubitemExist']);
                //$(this).closest('tr').find('.estimatedAmount').val(suggestion.companyLocalSellingPrice);
                // $(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                setTimeout(function () {
                    $('#search').closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
            }
        });
    }

    function add_more() {
        search_id += 1;
        //$('.select2').select2('destroy');
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

                mySelect.append($('<option></option>').val('').html('Select UOM'));
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

    function save_GoodReceiptNote_item() {
        $('.umoDropdown').attr('disabled', false);
        var data = $("#goodReceiptNote_add_item_form").serializeArray();

        $('#goodReceiptNote_add_item_form select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/save_goodReceiptNote_item_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.uom_disabled').prop('disabled', true);
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    grnDetailsID = null;
                    $('#goodReceiptNote_add_item_modal').modal('hide');
                    setTimeout(function () {
                        get_GoodReceiptNoteDetailItem_tableView(grnAutoID);
                    }, 300);
                    $('.umoDropdown').attr('disabled', true);
                }
            }, error: function () {
                $('.uom_disabled').prop('disabled', true);
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function update_goodReceiptNote_item() {
        $('.umoDropdown').attr('disabled', false);
        var data = $("#goodReceiptNote_edit_item_form").serializeArray();
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/update_goodReceiptNote_item_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $("#goodReceiptNote_edit_item_modal").modal('hide');
                    get_GoodReceiptNoteDetailItem_tableView(grnAutoID);
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function edit_goodReceiptNote_item(id) {
        if (grnAutoID) {
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
                        data: {'grnDetailsID': id},
                        url: "<?php echo site_url('Buyback/fetch_goodReceiptNote_item_detail'); ?>",
                        beforeSend: function () {

                            startLoad();
                        },
                        success: function (data) {
                            if (data[0] == 'e'){
                                myAlert(data[0], data[1]);
                            } else {
                                grnDetailsID = data['grnDetailsID'];
                                $('#grnDetailsID_edit').val(data['grnDetailsID']);
                                $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                                $('#search_id').val(data['itemSystemCode']);
                                $('#itemSystemCode').val(data['itemSystemCode']);
                                $('#itemAutoID').val(data['itemAutoID']);
                                $('#noofbirds_edit').val(data['noOfBirds']);
                                $('#kgweight_edit').val(data['qty']);
                                $('#amount_edit').val(data['unitTransferCost']);
                                fetch_related_uom_id(data['defaultUOMID'], data['unitOfMeasureID']);

                                var type = $("#grnType").val();
                                if (type == 2 || type == 3)
                                {
                                    $('#qtyChicksShowEdit').addClass('hide');
                                    $('#valueqtyChicksShowEdit').addClass('hide');
                                    $('#mortalityShowEdit').addClass('hide');
                                    $('#valuemortalityShowEdit').addClass('hide');
                                    $('#balanceShowEdit').addClass('hide');
                                    $('#valuebalanceShowEdit').addClass('hide');
                                    $('#returnShowEdit').addClass('hide');
                                    $('#valuereturnShowEdit').addClass('hide');
                                }else {
                                    $('#qtyChicksShowEdit').removeClass('hide');
                                    $('#valueqtyChicksShowEdit').removeClass('hide');
                                    $('#mortalityShowEdit').removeClass('hide');
                                    $('#valuemortalityShowEdit').removeClass('hide');
                                    $('#balanceShowEdit').removeClass('hide');
                                    $('#valuebalanceShowEdit').removeClass('hide');
                                    $('#returnShowEdit').removeClass('hide');
                                    $('#valuereturnShowEdit').removeClass('hide');
                                    fetch_goodReciptNote_batch_chicks_edit(data['grnDetailsID']);
                                    fetch_goodReciptNote_batch_mortality_edit();
                                    fetch_balancechicks_edit(data['noOfBirds']);
                                    fetch_return_qty_chicks_edit();
                                }
                                initializeitemTypeahead_edit();

                                $("#goodReceiptNote_edit_item_modal").modal('show');
                            }

                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function delete_goodReceiptNote_item(id) {
        if (grnAutoID) {
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
                        data: {'grnDetailsID': id},
                        url: "<?php echo site_url('Buyback/delete_goodReceiptNote_detail_item'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            get_GoodReceiptNoteDetailItem_tableView(grnAutoID);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
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
        if (grnAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/buyback/good_received_note', grnAutoID, 'GRN');
                });
        }
    }

    function confirmation() {
        if (grnAutoID) {
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
                        data: {'grnAutoID': grnAutoID},
                        url: "<?php echo site_url('Buyback/good_receipt_note_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            }else if (data['error'] == 2)
                            {
                                myAlert('w', data['message']);
                            } else {
                                myAlert('s', data['message']);
                                fetchPage('system/buyback/good_received_note', grnAutoID, 'Goods Received Note');
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function goodReceiptNote_item_add_detailModal() {

        $('#detail_grnDetailsID').val('');
        $('#goodReceiptNote_add_item_form')[0].reset();
        $('#goodReceiptNote_add_item_form').bootstrapValidator('resetForm', true);
        farmBatchName();
        var type = $("#grnType").val();
        if (type == 2 || type == 3)
        {
            $('#qtyChicksShow').addClass('hide');
            $('#valueqtyChicksShow').addClass('hide');
            $('#mortalityShow').addClass('hide');
            $('#valuemortalityShow').addClass('hide');
            $('#balanceShow').addClass('hide');
            $('#valuebalanceShow').addClass('hide');
            $('#returnShow').addClass('hide');
            $('#valuereturnShow').addClass('hide');
        }else {
            $('#qtyChicksShow').removeClass('hide');
            $('#valueqtyChicksShow').removeClass('hide');
            $('#mortalityShow').removeClass('hide');
            $('#valuemortalityShow').removeClass('hide');
            $('#balanceShow').removeClass('hide');
            $('#valuebalanceShow').removeClass('hide');
            $('#returnShow').removeClass('hide');
            $('#valuereturnShow').removeClass('hide');
            fetch_goodReciptNote_batch_chicks();
            fetch_goodReciptNote_batch_mortality();
            fetch_return_qty_chicks();

        }
        fetch_goodReciptNote_liveBirds_item();
        $("#goodReceiptNote_add_item_modal").modal({backdrop: "static"});
    }

    function farmBatchName()
    {
        if(grnAutoID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'grnAutoID': grnAutoID},
                url: "<?php echo site_url('Buyback/fetchFarmBatch_grn'); ?>",
                success: function (data)
                {
                    if(data){
                        $('#farmerBatchName').val(data);
                    }
                }
            });
        }
    }

    function fetch_goodReciptNote_liveBirds_item() {
        var type = $("#grnType").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'grnType' : type},
            url: "<?php echo site_url('Buyback/fetch_goodReciptNote_liveBirds_item'); ?>",
            success: function (data) {
                if (data) {
                    $("#detail_itemAutoID").val(data.itemAutoID);
                    $("#detail_itemDescription").val(data.itemName);
                    $("#detail_UnitOfMeasureID").val(data.defaultUnitOfMeasureID);
                }
            }
        });
    }

    function fetch_goodReciptNote_batch_chicks(element) {
        var batchID = $('#batchMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/fetch_goodReciptNote_batch_chicks'); ?>",
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.qtybirds').val(data['chicksTotal']);
                }
            }
        });
    }
    function fetch_balancechicks(element) {
        var batchID = $('#batchMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/fetch_balance_chicks'); ?>",
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.balance').val(data);
                }
            }
        });
    }

    function fetch_goodReciptNote_batch_chicks_edit(grnDetailsID) {
        var batchID = $('#batchMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID, grnDetailsID:grnDetailsID},
            url: "<?php echo site_url('Buyback/fetch_goodReciptNote_batch_chicks_edit'); ?>",
            success: function (data) {
                if (data) {
                    $("#qtybirds_edit").val(data.chicksTotal);
                }
            }
        });
    }

    function fetch_goodReciptNote_batch_mortality(element) {
        var batchID = $('#batchMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/fetch_goodReciptNote_batch_mortality'); ?>",
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.mortality').val(data['mortalityTotal']);
                }
            }
        });
    }
    function fetch_goodReciptNote_batch_mortality_edit() {
        var batchID = $('#batchMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/fetch_goodReciptNote_batch_mortality'); ?>",
            success: function (data) {
                if (data) {
                    $('#mortality_edit').val(data['mortalityTotal']);
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
    function clearitemAutoID(e,ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode==13) {
            //e.preventDefault();
        }else{
            $(ths).closest('tr').find('.itemAutoID').val('');
            $(ths).closest('tr').find('.noOfBirds ').val('');
            $(ths).closest('tr').find('.balance ').val('');
            $(ths).closest('tr').find('.mortality').val('');
            $(ths).closest('tr').find('.balance').val('');

        }
    }
    function birdvalidation(det) {
        var currentbalncebirds = $(det).closest('tr').find('.balance').val();
        if(det.value > parseFloat(currentbalncebirds)){
            myAlert('w','No of birds should be less than or equal to Balance Birds');
            $(det).val('');
        }

    }
    function fetch_balancechicks_edit(noOfBirds) {
        var batchID = $('#batchMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/fetch_balance_chicks'); ?>",
            success: function (data) {
                if (data) {
                    var balance = Number(data) + Number(noOfBirds);
                    $('#balance_edit').val(balance);
                }
            }
        });
    }
    function fetch_return_qty_chicks(element) {
        var batchID = $('#batchMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/returnqtychicks'); ?>",
            success: function (data) {
                if (data) {

                    $(element).closest('tr').find('.return').val(data['qtynew']);
                }
            }
        });
    }

    function fetch_return_qty_chicks_edit() {
        var batchID = $('#batchMasterID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {batchID: batchID},
            url: "<?php echo site_url('Buyback/returnqtychicks'); ?>",
            success: function (data) {
                if (data) {
                    $('#return_edit').val(data['qtynew']);
                }
            }
        });
    }

    function save_grn_itemSubID_view(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Buyback/load_sub_item_grn_view"); ?>',
            dataType: 'html',
            data: {grnDetailsID: id,  type: ''},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#grn_SubItem_view').html(data);
                $("#grn_SubItem_model").modal({backdrop: "static"});
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function save_grn_sub_items(id) {
        var data = $("#grn_SubItemView_form").serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Buyback/save_grn_subItem_received'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#grn_SubItem_model').modal('hide');
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function view_grn_subItems(id){
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Buyback/load_sub_item_grn_view"); ?>',
            dataType: 'html',
            data: {grnDetailsID: id, type: 'View'},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#grnView_SubItem_view').html(data);
                $("#grnView_SubItem_model").modal('show');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // $('#dispatchSubItem_view').html(xhr.responseText);

            }
        });
    }

</script>
