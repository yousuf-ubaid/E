<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('buyback_helper');

$current_date = current_format_date();
$date_format_policy = date_format_policy();

$groupLocations = load_all_group_locations();
$farmMasterLibabilty_arr = farm_master_group_gl_drop();
$depositGroupAcc = group_deposit_gl_drop();
$country = load_country_drop();
$currncy_arr = all_currency_master_drop();
$country_arr = array('' => 'Select Country');
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['countryID'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>

<div>
    <?php echo form_open('', 'role="form" id="farm_master_group_form" autocomplete="off"'); ?>
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>FARM DETAILS</h2>
            </header>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Farm Name <?php required_mark(); ?></label>
                    <input type="text" class="form-control" id="farmName" name="farmName" required>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerName">Secondary Code</label>
                    <input type="text" class="form-control" id="secondaryCode" name="secondaryCode">
                </div>
                <div class="form-group col-sm-4">
                    <label for="">Email</label>
                    <input type="text" class="form-control" id="farmerEmail" name="farmerEmail">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Area <?php required_mark(); ?></label>
                    <?php  echo form_dropdown('groupLocationID', $groupLocations, '', 'class="form-control select2" id="groupLocationID"'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerName">Sub Area <?php required_mark(); ?></label>
                    <?php  echo form_dropdown('groupSubLocationID', array("" => "Select Sub Area"), "", 'class="form-control select2" id="groupSubLocationID"'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="">Farm Type <?php required_mark(); ?></label>
                    <?php echo form_dropdown('farmType', array('' => 'Select Type', '1' => 'Third Party', '2' => 'Own'), '1', 'class="form-control" id="farmType" required'); ?>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Registered Date</label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="registeredDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="registeredDate" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerName">Bird Capacity</label>
                    <input type="number" class="form-control" id="capacity" name="capacity">
                </div>
                <div class="form-group col-sm-4">
                    <label for="">Currency <?php required_mark(); ?></label>
                    <?php echo form_dropdown('transactionCurrencyID', $currncy_arr, '', 'class="form-control select2" id="transactionCurrencyID" required'); ?>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Deposit Liability Account <?php required_mark(); ?></label>
                    <?php  echo form_dropdown('depositLiabilityGLautoID', $depositGroupAcc, '', 'class="form-control select2" id="depositLiabilityGLautoID" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerName">Farmer Liability Account <?php required_mark(); ?></label>
                    <?php echo form_dropdown('farmersLiabilityGLautoID', $farmMasterLibabilty_arr, '', 'class="form-control select2" id="farmersLiabilityGLautoID" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="">Is Active</label>
                    <div class="skin skin-square" style="padding-left: 2%">
                        <div class="skin-section extraColumns">
                            <input id="isActive" type="checkbox" data-caption="" class="columnSelected" name="isActive" value="1">
                            <label for="checkbox">&nbsp;</label></div>
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
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Contact Person <?php required_mark(); ?></label>
                    <input type="text" class="form-control" id="contactPerson" name="contactPerson" required>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerName">NIC Number <?php required_mark(); ?></label>
                    <input type="text" class="form-control" id="nicNo" name="nicNo" required>
                </div>
                <div class="form-group col-sm-4">
                    <label for="">Phone (Mobile) <?php required_mark(); ?></label>
                    <input type="text" name="phoneMobile" data-inputmask="'alias': '999-999 9999'" id="phoneMobile" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Phone (Home)</label>
                    <input type="text" name="phoneHome" data-inputmask="'alias': '999-999 9999'" id="phoneHome" class="form-control" >
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
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Address <?php required_mark(); ?></label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerName">City</label>
                    <input type="text" class="form-control" id="city" name="city">
                </div>
                <div class="form-group col-sm-4">
                    <label for="">State</label>
                    <input type="text" class="form-control" id="state" name="state">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Postal Code</label>
                    <input type="text" class="form-control" id="postalCode" name="postalCode">
                </div>
                <div class="form-group col-sm-4">
                    <label for="">Country</label>
                    <?php echo form_dropdown('countryID', $country_arr, '', 'class="form-control select2" id="countryID"'); ?>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>BANK DETAILS</h2>
            </header>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Bank Name</label>
                    <input type="text" class="form-control" id="bankName" name="bankName">
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerName">Branch</label>
                    <input type="text" class="form-control" id="branch" name="branch">
                </div>
                <div class="form-group col-sm-4">
                    <label for="">Account Name</label>
                    <input type="text" class="form-control" id="accName" name="accName">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="">Account Number</label>
                    <input type="text" class="form-control" id="accNo" name="accNo">
                </div>
                <div class="form-group col-sm-4">
                    <label for="">Bank Address</label>
                    <textarea class="form-control" id="bankAddress" name="bankAddress"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2>DESCRIPTION</h2>
            </header>
            <div class="row">
                <div class="form-group col-sm-8" style="margin-top: 5px;">
                    <textarea class="form-control" rows="5" name="narration" id="narration"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="text-right m-t-xs">
                    <div class="form-group col-sm-12" style="margin-top: 10px;">
                        <button class="btn btn-primary" type="submit" id="btn-save-farm">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>


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

<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script>
    var groupfarmID;
    $(document).ready(function () {
        groupfarmID = null;
        $('.headerclose').click(function () {
            fetchPage('system/GroupWarehouse/Group_farmmaster_view', '', 'Farm Master');
        });
        $('.select2').select2();
        $("#narration").wysihtml5();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        $('#isActive').iCheck('uncheck');
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            groupfarmID = p_id;
            load_farm_header(p_id);
        }

        $('#farm_master_group_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                farmName: {validators: {notEmpty: {message: 'Farm Name is required.'}}},
                farmType: {validators: {notEmpty: {message: 'Farm Type is required.'}}},
                groupLocationID: {validators: {notEmpty: {message: 'Location is required.'}}},
                groupSubLocationID: {validators: {notEmpty: {message: 'Location is required.'}}},
                contactPerson: {validators: {notEmpty: {message: 'Contact Person is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'ID Number is required.'}}},
                depositLiabilityGLautoID: {validators: {notEmpty: {message: 'Deposite Liability Account is required.'}}},
                farmersLiabilityGLautoID: {validators: {notEmpty: {message: 'Farmer Liability Account is required.'}}},
                nicNo: {validators: {notEmpty: {message: 'Deposit Amount is required.'}}},
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
            data.push({'name': 'groupfarmID', 'value': groupfarmID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Buyback/save_group_farm_master'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetchPage('system/GroupWarehouse/Group_farmmaster_view', '', 'Farm Master');

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

        $("#groupLocationID").change(function () {
            get_buyback_group_subArea($(this).val(), '')
        });

    });

    function get_buyback_group_subArea(groupLocationID, groupSubLocationID) {
        if(groupLocationID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'groupLocationID': groupLocationID},
                url: "<?php echo site_url('Buyback/fetch_buyback_group_subArea'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#groupSubLocationID').empty();
                    var mySelect = $('#groupSubLocationID');
                    mySelect.append($('<option></option>').val("").html("Select Sub Area"));
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (k, text) {
                            mySelect.append($('<option></option>').val(text['groupLocationID']).html(text['description']));
                        });
                    }
                    if(groupSubLocationID){
                        mySelect.val(groupSubLocationID).change();
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } else {
            $('#groupSubLocationID').empty();
        }
    }

    function load_farm_header(groupfarmID) {
        if (groupfarmID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'groupfarmID': groupfarmID},
                url: "<?php echo site_url('Buyback/load_group_farm_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#btn-save-farm').text('Update Farm');
                        groupfarmID = data['groupfarmID'];
                        $("#farmName").val(data['description']);
                        $('#secondaryCode').val(data['farmSecondaryCode']);
                        $('#farmerEmail').val(data['email']);
                        $('#groupLocationID').val(data['groupLocationID']).change();

                        get_buyback_group_subArea(data['groupLocationID'], data['groupSubLocationID']);
                        $('#groupSubLocationID').val(data['groupSubLocationID']).change();
                        $('#farmType').val(data['farmType']);
                        $('#registeredDate').val(data['registeredDate']);
                        $('#capacity').val(data['capacity']);
                        $('#transactionCurrencyID').val(data['farmerCurrencyID']).change();
                        $("#transactionCurrencyID").prop("disabled", true);
                        $('#depositLiabilityGLautoID').val(data['depositLiabilityGLautoID']).change();
                        $('#farmersLiabilityGLautoID').val(data['farmersLiabilityGLautoID']).change();
                        $("#depositLiabilityGLautoID").prop("disabled", true);
                        $("#farmersLiabilityGLautoID").prop("disabled", true);

                        if (data['isActive'] == 1) {
                            $('#isActive').iCheck('check');
                        }else{
                            $('#isActive').iCheck('uncheck');
                        }
                        $('#contactPerson').val(data['contactPerson']);
                        $('#nicNo').val(data['NIC']);
                        $('#phoneMobile').val(data['phoneMobile']);
                        $('#phoneHome').val(data['phoneHome']);
                        $('#address').val(data['address']);
                        $('#city').val(data['city']);
                        $('#state').val(data['state']);
                        $('#postalCode').val(data['postalCode']);
                        $('#countryID').val(data['countryID']).change();
                        $('#bankName').val(data['bankName']);
                        $('#branch').val(data['bankBranch']);
                        $('#accName').val(data['bankAccountName']);
                        $('#accNo').val(data['bankAccountNo']);
                        $('#bankAddress').val(data['bankAddress']);
                        $('#narration').val(data['narration']);

                        //load_customer_link_table();
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }
</script>