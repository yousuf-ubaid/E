<?php echo head_page($_POST['page_name'], false);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$segment_arr = fetch_segment();
$supplier_arr = all_supplier_drop();
$farms_arr = load_all_farms();
$currency_arr = all_currency_new_drop();//array('' => 'Select Currency');
$location_arr = load_all_locations(false);
$location_arr_default = default_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$uom_arr = array('' => 'Select UOM');
$customer_arr = all_employee_drop();
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
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Collection Header</a>
    <a class="btn btn-default" href="#step2" data-toggle="tab">Step 2 - Collection Details</a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_confirmation();" data-toggle="tab">Step 3 - Collection Confirmation</a>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>Live Collection Filters</h2>
                </header>
                <?php echo form_open('', 'role="form" id="buyback_collection_frm" autocomplete="off"'); ?>
                <input type="hidden" name="collectionid" id="collectionid">

                <div class="row">
                    <div class="form-group col-sm-2">
                        <label for="supplierPrimaryCode">Date From<?php required_mark(); ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="batchmasterDatefrom"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   id="batchmasterDatefrom" class="form-control" value="">
                        </div>
                    </div>

                    <div class="form-group col-sm-2">
                        <label for="supplierPrimaryCode">&nbsp&nbspTo&nbsp&nbsp<?php required_mark(); ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="batchmasterDateto"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   id="batchmasterDateto" class="form-control" value="">
                        </div>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="area">Area<?php required_mark(); ?></label><br>
                        <?php echo form_dropdown('locationID[]', $location_arr, '', 'class="form-control" id="locationID_filter" multiple="" '); ?>
                    </div>

                    <div class="form-group col-sm-2">
                        <label for="area">Sub Area<?php required_mark(); ?></label><br>
                        <div id="div_load_subloacations">
                            <select name="subLocationID[]" class="form-control" id="filter_sublocation" multiple="">

                            </select>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="area">Farm<?php required_mark(); ?></label><br>
                        <div id="div_load_farm">
                            <select name="farmer[]" class="form-control" id="filter_farm" multiple="">
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-1">
                        <label for=""> </label>
                        <button style="margin-top: 23px;margin-left: -140%;" type="button" onclick="save_buyback_collection()"
                                class="btn btn-primary">
                            Generate
                        </button>
                    </div>

                </div>
            </div>
            </form>
            <div class="row">
                <div class="col-sm-12">
                    <div id="collection_detail_view"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>Live Collection Header Details</h2>
                </header>
                <?php echo form_open('', 'role="form" id="buyback_collection_header_frm" autocomplete="off"'); ?>
                <input type="hidden" name="collectionid" id="collectionid">

                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title" for="">Document Date</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="input-group documentdate">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="documentdate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   id="documentdate" class="form-control" value="<?php echo $current_date; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Trip No</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control number" id="tripNo" name="tripNo">
                    </div>
                </div>

                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title"
                               for="Driver Name">Driver Name <?php required_mark(); ?></label>
                    </div>
                    <div class="form-group  col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="driveName" name="driveName" required>
                            <input type="hidden" class="form-control" id="DriverID" name="DriverID">
                            <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clearEmployee()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Driver" rel="tooltip"
                                onclick="link_employee_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                        </div>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Vehicle No</label>
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
                        <label class="title">Journey Time</label>
                    </div>
                    <div class="form-group col-sm-2" style="margin-top: 5px;">
                        <input type="text" class="form-control input-sm startdateDatepic" id="JourneyFrom"
                               name="JourneyFrom" placeholder="Journey Starts" style="width: 150px;">
                    </div>
                    <div class="form-group col-sm-2" style="margin-top: 5px;">
                        <input type="text" class="form-control input-sm startdateDatepic" id="JourneyTo"
                               placeholder="Journey Ends" name="JourneyTo">
                    </div>

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
                </div>

                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title">Whether Condition</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="whetherCondition" name="whetherCondition">
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"
                            for="SalesPersonName">Helper Name <?php required_mark(); ?></label>
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
                        <label class="title">Transport Mortality</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control number" id="MortalChickstrnspt" name="MortalChickstrnspt">
                    </div>
                    <div class="form-group col-sm-2"></div>
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
                        <label class="title">feed in crop deduction</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="cropDeduction" name="cropDeduction">
                    </div>
                    <div class="form-group col-sm-2"></div>
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

                <div class="row" style="margin-top: 10px; margin-right: 2px">
                    <div class="form-group col-sm-2">
                        <label class="title"
                            for="SalesPersonName">Narration<?php required_mark(); ?> </label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea class="form-control" rows="2" name="comment" id="comment"></textarea>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Comment</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea type="text" class="form-control " id="TransportComment" name="TransportComment"></textarea>
                    </div>
                </div>
            </div>
            </form>
        </div>

        <div class="text-right m-t-xs" style="margin-top: 10px;">
            <button class="btn btn-default prev">Previous</button>
            <button class="btn btn-primary " onclick="UpdateHeaderDetails()">Save</button>
        </div>

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

<div class="modal fade bs-example-modal-lg" id="emp_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Link Driver</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3"
                               class="col-sm-3 control-label">Driver </label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('employee_id', $customer_arr, '', 'class="form-control select2" id="employee_id" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="fetch_employee_detail()">Add Driver </button>
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
                            echo form_dropdown('vehicle_id', load_all_fleet_vehicles(), '', 'class="form-control select2" id="vehicle_id" required'); ?>
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


<script type="text/javascript">
    var Otable;
    var collectionautoid;
    var EIdNo;
    var helperid;

    $(".select2").select2();
    $(document).ready(function () {
        EIdNo = null;
        helperid = null;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        load_locationbase_sub_location();
        $('.headerclose').click(function () {
            fetchPage('system/buyback/buyback_collection', '', 'Buyback Collection');
        });

        $('.startdateDatepic').datetimepicker({
            format: "DD/MM/YYYY hh:mm A",
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            collectionautoid = p_id;
            load_collection_header(collectionautoid);
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');

        }
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (e) {

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

        /* $('.documentdate').datetimepicker({
             useCurrent: false,
             format: date_format_policy,

         }).on('dp.change', function (e) {

         });
 */

    });

    $('#locationID_filter').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#locationID_filter").multiselect2('selectAll', false);
    $("#locationID_filter").multiselect2('updateButtonText');

    $('#filter_sublocation').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#filter_sublocation").multiselect2('selectAll', false);
    $("#filter_sublocation").multiselect2('updateButtonText');

    $('#filter_farm').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#filter_farm").multiselect2('selectAll', false);
    $("#filter_farm").multiselect2('updateButtonText');


    $('#searchTask').bind('input', function () {
        startMasterSearch();
    });

    $("#locationID_filter").change(function () {
        if ((this.value)) {
            load_locationbase_sub_location(this.value);
            return false;
        }

    });

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getBatchManagement_tableView();
    }

    function clearSearchFilter() {

        getBatchManagement_tableView();
    }

    function UpdateHeaderDetails() {
        var data = $('#buyback_collection_header_frm').serializeArray();
        data.push({'name': 'collectionid', 'value': collectionautoid});
   //     data.push({'name': 'employeeID', 'value': EIdNo});
    //    data.push({'name': 'requested', 'value': $('#employee_id option:selected').text()});
    //    data.push({'name': 'helperID', 'value':helperid });
    //    data.push({'name': 'helpername', 'value': $('#helper_id option:selected').text()});
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Buyback/update_buyback_collection_header"); ?>',
            dataType: 'json',
            data: data,
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('[href=#step3]').tab('show');
                    $('a[data-toggle="tab"]').removeClass('btn-primary');
                    $('a[data-toggle="tab"]').addClass('btn-default');
                    $('[href=#step3]').removeClass('btn-default');
                    $('[href=#step3]').addClass('btn-primary');
                    $('.btn-wizard').removeClass('disabled');
                    load_confirmation();
                }else
                {
                    load_buyback_collection();
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });

    }
    function save_buyback_collection() {
        var data = $('#buyback_collection_frm').serializeArray();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Buyback/save_buyback_collection_header"); ?>',
            dataType: 'json',
            data: data,
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    collectionautoid = data[2];
                    $('#collectionid').val(data[2]);
                    load_buyback_collection(collectionautoid);
                }else
                {
                    load_buyback_collection();
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function save_draft() {
        if (collectionautoid) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft",
                    cancelButtonText: "Cancel"
                },
                function () {
                    fetchPage('system/buyback/buyback_collection', '', 'Collection ')
                });
        }
    }

    function load_buyback_collection(collectionautoid) {
        var data = $('#buyback_collection_frm').serializeArray();
       // var collectionautoid =   $('#collectionid').val();
        data.push({"name": "collectionautoid", "value": collectionautoid});
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Buyback/load_buyback_collection_header"); ?>',
            dataType: 'html',
            data: data,
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#collection_detail_view').html(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });

    }

    function load_locationbase_sub_location() {
        var locationid = $('#locationID_filter').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {locationid: locationid},
            url: "<?php echo site_url('Buyback/fetch_buyback_preformance_sublocationDropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_subloacations').html(data);
                $('#filter_sublocation').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_sublocation").multiselect2('selectAll', false);
                $("#filter_sublocation").multiselect2('updateButtonText');
                fetch_farm();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function fetch_farm() {
        var sublocationid = $('#filter_sublocation').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {sublocationid: sublocationid},
            url: "<?php echo site_url('Buyback/fetch_farm_by_sub_location'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_farm').html(data);
                $('#filter_farm').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    selectAllValue: 'select-all-value',
                    //enableFiltering: true
                    buttonWidth: 150,
                    maxHeight: 200,
                    numberDisplayed: 1
                });
                $("#filter_farm").multiselect2('selectAll', false);
                $("#filter_farm").multiselect2('updateButtonText');

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function load_confirmation() {
        if (collectionautoid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'collectionautoid': collectionautoid, 'html': true},
                url: "<?php echo site_url('Buyback/load_buyback_collection_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#confirm_body').html(data);
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }
    function load_collection_header() {
        if (collectionautoid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'collectionautoid': collectionautoid},
                url: "<?php echo site_url('Buyback/load_collection_header'); ?>",
                beforeSend: function () {
                    startLoad();

                },
                success: function (data) {

                    if (!jQuery.isEmptyObject(data)) {
                        collectionautoid = data['collectionid'];
                        $('#batchmasterDatefrom').val(data['datefrom']);
                        $('#batchmasterDateto').val(data['dateto']);

                        setTimeout(function () {
                            $('#locationID_filter').multiselect2("deselectAll", false).multiselect2("refresh");
                            $('#locationID_filter').multiselect2('select',data['location']);

                            $('#filter_sublocation').multiselect2("deselectAll", false).multiselect2("refresh");
                            $('#filter_sublocation').multiselect2('select',data['sublocation']);

                         $('#filter_farm').multiselect2("deselectAll", false).multiselect2("refresh");
                            $('#filter_farm').multiselect2('select',data['farmer']);
                        }, 500);

                        if (data["driverID"] > 0) {
                            $('#driveName').prop('readonly', true);
                        }
                        $('#driveName').val(data['drivername']);

                        if (data["VehicleID"] > 0) {
                            $('#VehicleName').prop('readonly', true);
                        }
                        $('#VehicleID').val(data['vehicleID']);
                        $('#VehicleName').val(data['vehicleNo']);

                        $('#HelperIDOne').val(data['helperid']);
                        if (data["helperid"] > 0) {
                            $('#helperOne').prop('readonly', true);
                        }
                        $('#helperOne').val(data['helpername']);
                        $('#HelperIDTwo').val(data['helperTwoID']);
                        if (data["helperTwoID"] > 0) {
                            $('#helperTwo').prop('readonly', true);
                        }
                        $('#helperTwo').val(data['helperTwoName']);
                        $('#HelperIDThree').val(data['helperThreeID']);
                        if (data["helperThreeID"] > 0) {
                            $('#helperThree').prop('readonly', true);
                        }
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
                        $('#comment').val(data['narration']);
                        $('#employee_id').val(data['driverID']).change();
                        EIdNo = data['driverID'];

                        $('#helper_id').val(data['helperid']).change();
                        helperid = data['helperid'];

                        $('#collectionid').val(collectionautoid);

                        load_confirmation();
                        load_buyback_collection(collectionautoid);

                        if (data["helpername"] != '') {
                            $('[href=#step3]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step3]').removeClass('btn-default');
                            $('[href=#step3]').addClass('btn-primary');
                            $('.btn-wizard').removeClass('disabled');
                        } else {
                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                            $('.btn-wizard').addClass('disabled');
                        }
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
    function confirmation() {
        if (collectionautoid) {
            swal({

                    title: "Are you sure?",
                    text: "You want to confirm this document",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "Confirm", /*Confirm*/
                    cancelButtonText: "Cancel"/*Confirm*/
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'collectionautoid': collectionautoid},
                        url: "<?php echo site_url('Buyback/collection_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data) {
                                fetchPage('system/buyback/buyback_collection', collectionautoid, 'Collection');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function clearEmployee() {
        $('#employee_id').val('').change();
        $('#DriverID').val('').change();
        $('#driveName').val('').trigger('input');
        $('#driveName').prop('readonly', false);
        EIdNo = null;
    }

    function link_employee_model() {
        /*$('#employee_id').val('').change();*/
        $('#emp_model').modal('show');
    }

    function fetch_employee_detail() {
        var employee_id = $('#employee_id').val();
        if (employee_id == '') {
            //swal("", "Select An Employee", "error");
            myAlert('e', 'Select A Driver');
        } else {
            EIdNo = employee_id;
            var empName = $("#employee_id option:selected").text();
            /*  var empNameSplit = empName.split('|');*/
            $('#driveName').val($.trim(empName)).trigger('input');
            $('#DriverID').val($.trim(EIdNo));
            $('#driveName').prop('readonly', true);
            $('#emp_model').modal('hide');
        }
    }

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


    $('.select2').select2();



</script>


