<?php echo head_page($_POST['page_name'], false);
$this->load->helper('buyback_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$countries_arr = load_all_countries();
$location_arr = load_all_locations();
$currency_arr = all_currency_new_drop();
$farmMasterLibabilty_arr = farm_master_gl_drop();
$depositLibabilty_arr = deposit_gl_drop();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #060606
    }

    span.input-req-inner {
        width: 20px;
        height: 40px;
        position: absolute;
        overflow: hidden;
        display: block;
        right: 4px;
        top: -15px;
        -webkit-transform: rotate(135deg);
        -ms-transform: rotate(135deg);
        transform: rotate(135deg);
        z-index: 100;
    }

    span.input-req-inner:before {
        font-size: 20px;
        content: "*";
        top: 15px;
        right: 1px;
        color: #fff;
        position: absolute;
        z-index: 2;
        cursor: default;
    }

    span.input-req-inner:after {
        content: '';
        width: 35px;
        height: 35px;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        background: #f45640;
        position: absolute;
        top: 7px;
        right: -29px;
    }

    .search_cancel {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }
</style>

<?php echo form_open('', 'role="form" id="farmMaster_form"  autocomplete="off"'); ?>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>FARM DETAIL</h2>
        </header>
        <div class="row hidden" id="farmCodeView" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title" style="padding-right: 0px;">Farm Code</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="farmCode"
                       id="farmCode"
                       class="form-control"
                       placeholder="Farm Name"
                       disabled>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title" style="padding-right: 0px;">Farm Name</label>
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="description"
                                                                                     id="description"
                                                                                     class="form-control"
                                                                                     placeholder="Farm Name"
                                                                                     required><span
                                       class="input-req-inner"></span></span>
                <input type="hidden" name="farmID" id="farmID_edit">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title" style="padding-right: 0px;">Farm Secondary Code</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="farmSecondaryCode" id="farmSecondaryCode" class="form-control"
                       placeholder="Farm Secondary Code">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title" style="padding-right: 0px;">Email</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="email" id="email" class="form-control" placeholder="Email">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title" style="padding-right: 0px;">Area</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field" style="z-index: 12;"><!--<div class="input-group">-->
                            <!--<span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="add-location"
                                        style="height: 29px; padding: 2px 10px;">
                                    <i class="fa fa-plus" style="font-size: 11px"></i>
                                </button>
                            </span>-->
                        <?php echo form_dropdown('locationID', $location_arr, '', 'class="form-control select2" id="locationID"'); ?>
<!--                    </div>-->
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="padding-right: 0px;">
                <label class="title">Sub Area</label>

            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field" style="z-index: 12;"><!--<div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="add-sub-location"
                                        style="height: 29px; padding: 2px 10px;">
                                    <i class="fa fa-plus" style="font-size: 11px"></i>
                                </button>
                            </span>-->
                        <?php echo form_dropdown('subLocationID', array("" => "Select Sub Area"), "", 'class="form-control select2" id="subLocationID"'); ?>
                   <!-- </div>-->
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="padding-right: 0px;">
                <label class="title">Farm Type</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req"
                      title="Required Field"><?php echo form_dropdown('farmType', array('' => 'Select Type', '1' => 'Third Party', '2' => 'Own'), '1', 'class="form-control" id="farmType" required'); ?>
                    <span
                        class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="padding-right: 0px;">
                <label class="title">Registered Date</label>
            </div>
            <div class="form-group col-sm-4">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="registeredDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="registeredDate" class="form-control">
                </div>
            </div>
        </div>
        <!--<div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">No Of Cages</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="number" name="noOfCages" id="noOfCages" class="form-control"
                       placeholder="No Of Cages">
            </div>
        </div>-->
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title" style="padding-right: 0px;">Birds Capacity</label>
            </div>
            <div class="form-group col-sm-4">

                <input type="text" name="capacity" id="capacity" class="form-control number"
                       placeholder="Birds Capacity">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="padding-right: 0px;">
                <label class="title">Currency</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" required'); ?>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
       <!-- <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="padding-right: 0px;">
                <label class="title">Deposit Amount</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <input type="text" name="depositAmount" id="depositAmount" class="form-control number"
                        onkeypress="return validateFloatKeyPress(this,event)">
                <span class="input-req-inner"></span></span>
            </div>
        </div>-->
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="padding-right: 0px;">
                <label class="title text-right">Deposit Liability Account</label>
            </div>
            <div class="form-group col-sm-4">
                 <span class="input-req" title="Required Field">
                 <?php echo form_dropdown('depositLiabilityGLautoID', $depositLibabilty_arr, '', 'class="form-control select2" id="depositLiabilityGLautoID" required'); ?>
                     <span class="input-req-inner"></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="padding-right: 0px;">
                <label class="title">Farmer Liability Account</label>
            </div>
            <div class="form-group col-sm-4">
                 <span class="input-req" title="Required Field">
                 <?php echo form_dropdown('farmersLiabilityGLautoID', $farmMasterLibabilty_arr, '', 'class="form-control select2" id="farmersLiabilityGLautoID" required'); ?>
                     <span class="input-req-inner"></span>
            </div>
        </div>
        <div class="row"  style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2" style="padding-right: 0px;">
                <label class="title">IS Active</label>
            </div>
            <div class="form-group col-sm-4" style="padding-left: 0px;">
                <div class="col-sm-1">
                    <div class="skin skin-square">
                        <div class="skin-section extraColumns">
                            <input id="isActive" type="checkbox" data-caption="" class="columnSelected" name="isActive" value="1">
                            <label for="checkbox">&nbsp;</label></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>CONTACT DETAILS</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Contact Person</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"><input type="text" name="contactPerson"
                                                                      id="contactPerson" class="form-control"
                                                                      placeholder="Contact Person"><span
                        class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">NIC Number</label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"><input type="text" name="idNumber"
                                                                      id="idNumber" class="form-control"
                                                                      placeholder="NIC Number"><span
                        class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Phone (Mobile)</label>
            </div>
            <div class="form-group col-sm-4">
             <!--   <span class="input-req" title="Required Field"><input type="text" name="phoneMobile" id="phoneMobile"
                                                                      class="form-control" placeholder="Phone (Mobile)"><span
                        class="input-req-inner"></span></span> -->
                <span class="input-req" title="Required Field">
                        <input type="text" name="phoneMobile"
                               data-inputmask="'alias': '999-999 9999'"
                               id="phoneMobile" class="form-control" required><span
                        class="input-req-inner"></span></span>

            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Phone (Home)</label>
            </div>
            <div class="form-group col-sm-4">
              <!--  <input type="text" name="phoneHome" id="phoneHome" class="form-control"
                       placeholder="Phone (Home)"> -->
                <input type="text" name="phoneHome"
                       data-inputmask="'alias': '999-999 9999'"
                       id="phoneHome" class="form-control" >

            </div>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>ADDRESS</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Address</label>
            </div>
            <div class="form-group col-sm-4">
                  <span class="input-req" title="Required Field"> <textarea class="form-control" id="address" name="address" rows="2"></textarea><span
                              class="input-req-inner"></span></span>

            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">City</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="city" id="city" class="form-control" placeholder="City">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">State</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="state" id="state" class="form-control" placeholder="State">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Postal Code</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="postalcode" id="postalcode" class="form-control" placeholder="Postal Code">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Country</label>
            </div>
            <div class="form-group col-sm-4">
                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2" id="countryID"'); ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>BANK DETAILS</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Bank Name</label>
            </div>
            <div class="form-group col-sm-4">
                <input id="bankName" name="bankName" type="text" class="form-control">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Branch</label>
            </div>
            <div class="form-group col-sm-4">
                <input id="branchName" name="branchName" type="text" class="form-control">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Account Name</label>
            </div>
            <div class="form-group col-sm-4">
                <input id="accountName" name="accountName" type="text" class="form-control">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Account Number</label>
            </div>
            <div class="form-group col-sm-4">
                <input id="accountNumber" name="accountNumber" type="text" class="form-control">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Bank Address</label>
            </div>
            <div class="form-group col-sm-4">
                <textarea class="form-control" id="bankAddress" name="bankAddress"></textarea>
            </div>
        </div>
    </div>
</div>
<br>


<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>DESCRIPTION</h2>
        </header>
        <div class="row">
            <div class="form-group col-sm-10" style="margin-top: 5px;">
                <textarea class="form-control" rows="5" name="narration" id="narration"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="text-right m-t-xs">
                <div class="form-group col-sm-10" style="margin-top: 10px;">
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<div class="modal fade" id="location-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">New Area</h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Area</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="add_locationID" name="add_locationID">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn-location">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sub-location-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">New Sub Area</h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Area</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="selectedArea" name="selectedArea" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Sub Area</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="add_subLocationID" name="subLocationID">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn-sub-location">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script type="text/javascript">
    var subLocationID = '';
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/buyback/farm_management', '', 'Farms');
        });
        $('.select2').select2();

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        farmID = null;

        number_validation();

        Inputmask().mask(document.querySelectorAll("input"));

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            farmID = p_id;
            load_farm_header();
        } else {
            $("#narration").wysihtml5();
        }

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });


        $('#farmMaster_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                description: {validators: {notEmpty: {message: 'Farm Name is required.'}}},
                farmType: {validators: {notEmpty: {message: 'Farm Type is required.'}}},
                locationID: {validators: {notEmpty: {message: 'Location is required.'}}},
                contactPerson: {validators: {notEmpty: {message: 'Contact Person is required.'}}},
                idNumber: {validators: {notEmpty: {message: 'ID Number is required.'}}},
                phoneMobile: {validators: {notEmpty: {message: 'Phone Mobile is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                depositLiabilityGLautoID: {validators: {notEmpty: {message: 'Deposite Liability Account is required.'}}},
                farmersLiabilityGLautoID: {validators: {notEmpty: {message: 'Farmer Liability Account is required.'}}},
              //  depositAmount: {validators: {notEmpty: {message: 'Deposit Amount is required.'}}},
                address: {validators: {notEmpty: {message: 'Address is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
          //  $("#depositAmount").prop("disabled", false);
            $("#depositLiabilityGLautoID").prop("disabled", false);
            $("#farmersLiabilityGLautoID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Buyback/save_farm_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetchPage('system/buyback/farm_management', '', 'Farms');

                    } else {
                        $('.btn-wizard').removeClass('disabled');
                    }

                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#isPermissionEveryone').iCheck('check');

        $("#isPermissionEveryone").on("ifChanged", function () {
            $("#employeesID").val(null).trigger("change");
            $("#show_multiplePermission").addClass('hide');
            $("#groupID").val('');
            $("#show_groupPermission").addClass('hide');
        });

        $("#isPermissionCreator").on("ifChanged", function () {
            $("#employeesID").val(null).trigger("change");
            $("#show_multiplePermission").addClass('hide');
            $("#groupID").val('');
            $("#show_groupPermission").addClass('hide');
        });

        $("#isPermissionGroup").on("ifChanged", function () {
            $("#employeesID").val(null).trigger("change");
            $("#show_groupPermission").removeClass('hide');
            $("#show_multiplePermission").addClass('hide');
        });

        $("#isPermissionMultiple").on("ifChanged", function () {
            $("#groupID").val('');
            $("#show_groupPermission").addClass('hide');
            $("#show_multiplePermission").removeClass('hide');
        });

        $('#isActive').iCheck('check');

        $("#locationID").change(function () {
            get_buyback_subArea($(this).val())
        });

    });

    function load_farm_header() {
        if (farmID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'farmID': farmID},
                url: "<?php echo site_url('Buyback/load_farm_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        subLocationID = data['subLocationID'];
                        $('#farmID_edit').val(farmID);
                        $('#farmCode').val(data['farmSystemCode']);
                        $('#farmCodeView').removeClass('hidden');
                        $('#description').val(data['description']);
                        $('#farmType').val(data['farmType']);
                        $('#email').val(data['email']);
                        $('#farmSecondaryCode').val(data['farmSecondaryCode']);
                        $('#noOfCages').val(data['noOfCages']);
                        $('#capacity').val(data['capacity']);
                        $('#contactPerson').val(data['contactPerson']);
                        $('#idNumber').val(data['NIC']);
                        $('#phoneMobile').val(data['phoneMobile']);
                        $('#phoneHome').val(data['phoneHome']);
                        $('#postalcode').val(data['postalCode']);
                        $('#city').val(data['city']);
                        $('#state').val(data['state']);
                        $('#countryID').val(data['countryID']).change();
                        $('#transactionCurrencyID').val(data['farmerCurrencyID']).change();
                        $('#farmersLiabilityGLautoID').val(data['farmersLiabilityGLautoID']).change();
                        $('#depositLiabilityGLautoID').val(data['depositLiabilityGLautoID']).change();
                        $('#address').val(data['address']);
                        $('#locationID').val(data['locationID']).change();
                        $("#narration").wysihtml5();
                        $('#narration').val(data['narration']);

                        $('#bankName').val(data['bankName']);
                        $('#branchName').val(data['bankBranch']);
                        $('#accountName').val(data['bankAccountName']);
                        $('#accountNumber').val(data['bankAccountNo']);
                        $('#bankAddress').val(data['bankAddress']);
                  //      $('#depositAmount').val(data['depositAmount']);
                        if (data['isActive'] == 1) {
                            $('#isActive').iCheck('check');
                        }else{
                            $('#isActive').iCheck('uncheck');
                        }
                   //     $("#depositAmount").prop("disabled", true);
                        $("#depositLiabilityGLautoID").prop("disabled", true);
                        $("#farmersLiabilityGLautoID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    $('#add-location').click(function () {
        $('#add_locationID').val('');
        $('#location-modal').modal({backdrop: 'static'});
    });

    $('#add-sub-location').click(function () {
        $('#selectedArea').val('');
        var selectedArea = $('#locationID option:selected').text();
        $('#selectedArea').val(selectedArea);
        $('#add_subLocationID').val('');
        $('#sub-location-modal').modal({backdrop: 'static'});
    });

    $('#save-btn-location').click(function (e) {
        e.preventDefault();
        var location = $.trim($('#add_locationID').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'location': location},
            url: '<?php echo site_url("Buyback/new_location"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                var location_drop = $('#locationID');
                if (data[0] == 's') {
                    location_drop.append('<option value="' + data[2] + '">' + location + '</option>');
                    location_drop.val(data[2]);
                    $('#location-modal').modal('hide');

                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });

    $('#save-btn-sub-location').click(function (e) {
        e.preventDefault();
        var location = $.trim($('#locationID').val());
        var subLocation = $.trim($('#add_subLocationID').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'location': location, 'subLocation' :subLocation},
            url: '<?php echo site_url("Buyback/save_buyback_sub_location"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                var location_drop = $('#subLocationID');
                if (data[0] == 's') {
                    location_drop.append('<option value="' + data[2] + '">' + subLocation + '</option>');
                    location_drop.val(data[2]);
                    $('#sub-location-modal').modal('hide');
                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });

    function validateFloatKeyPress(el, evt) {
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

    function get_buyback_subArea(locationID) {
        if(locationID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {locationID: locationID},
                url: "<?php echo site_url('Buyback/fetch_buyback_subArea'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#subLocationID').empty();
                    var mySelect = $('#subLocationID');
                    mySelect.append($('<option></option>').val("").html("Select"));
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (k, text) {
                            mySelect.append($('<option></option>').val(text['locationID']).html(text['description']));
                        });
                    }
                    if(subLocationID){
                        mySelect.val(subLocationID).change();
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } else {
            $('#subLocationID').empty();
        }
    }


</script>