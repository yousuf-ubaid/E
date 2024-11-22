<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$all_states_arr = all_states();
$countries_arr = load_all_countrys();
$countryCode_arr = all_country_codes();
$currency_arr = all_currency_new_drop();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
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

<?php echo form_open('', 'role="form" id="contact_form"'); ?>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('operationngo_donor_name_and_detail');?><!--DONOR NAME AND DETAIL--></h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_name');?><!--Name--></label>
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="name"
                                                                                     id="name"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('common_name');?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--Name-->
                <input type="hidden" name="contactID" id="contactID_edit">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_email');?><!--Email--></label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="email" id="email" class="form-control" placeholder="<?php echo $this->lang->line('common_email');?>"><!--Email-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_currency');?><!--Currency--></label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID"'); ?><span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_phone');?> (<?php echo $this->lang->line('operationngo_primary');?>) </label><!--Phone (Primary)-->
            </div>
            <div class="form-group col-sm-1" style="width: 12%">
                                     <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('countryCodePrimary', $countryCode_arr, '', 'class="form-control" id="countryCodePrimary"'); ?>
                                         <span class="input-req-inner"></span></span>
            </div>
            <div class="form-group col-sm-1" style="padding-left: 0px;">
            <input type="text" name="phoneAreaCodePrimary" id="phoneAreaCodePrimary" class="form-control" placeholder="<?php echo $this->lang->line('operationngo_area_code');?>"><!--Area Code-->
            </div>
            <div class="form-group col-sm-3" style="padding-left: 0px;">
                  <span class="input-req" title="Required Field">
            <input type="text" name="phonePrimary" id="phonePrimary" class="form-control" placeholder="<?php echo $this->lang->line('operationngo_phone_number');?>" required><!--Phone Number-->
                      <span class="input-req-inner"></span></span>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-1">
            &nbsp
        </div>
        <div class="form-group col-sm-2">
            <label class="title"><!--Phone--><?php echo $this->lang->line('common_phone');?> (<?php echo $this->lang->line('operationngo_secondary');?>)</label><!--Secondary-->
        </div>
        <div class="form-group col-sm-1" style="width: 12%">
            <?php echo form_dropdown('countryCodeSecondary', $countryCode_arr, '', 'class="form-control" id="countryCodeSecondary"'); ?>
        </div>
        <div class="form-group col-sm-1" style="padding-left: 0px;">
            <input type="text" name="phoneAreaCodeSecondary" id="phoneAreaCodeSecondary" class="form-control" placeholder="<?php echo $this->lang->line('operationngo_area_code');?>"><!--Area Code-->
        </div>
        <div class="form-group col-sm-3" style="padding-left: 0px;">
            <input type="text" name="phoneSecondary" id="phoneSecondary" class="form-control"
                   placeholder="<?php echo $this->lang->line('operationngo_phone_number');?>"><!--Phone Number-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-1">
            &nbsp
        </div>
        <div class="form-group col-sm-2">
            <label class="title"><?php echo $this->lang->line('common_fax');?><!--Fax--></label>
        </div>
        <div class="form-group col-sm-4">
            <input type="text" name="fax" id="fax" class="form-control" placeholder="<?php echo $this->lang->line('common_fax');?>"><!--Fax-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="form-group col-sm-1">
            &nbsp
        </div>
        <div class="form-group col-sm-2">
            <label class="title"><?php echo $this->lang->line('operationngo_website');?><!--Website--></label>
        </div>
        <div class="form-group col-sm-4">
            <input type="text" name="website" id="website" class="form-control"
                   placeholder="<?php echo $this->lang->line('operationngo_website');?>"><!--Website-->
        </div>
    </div>
</div>
</div>
<br>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('operationngo_address');?><!--ADDRESS--></h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_address');?><!--Address--></label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field"><textarea class="form-control" id="address"
                                                                         name="address" rows="2"></textarea><span
                        class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('operationngo_city');?><!--City--></label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="city" id="city" class="form-control" placeholder="<?php echo $this->lang->line('operationngo_city');?>"><!--City-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('operationngo_province');?><!--Province--></label>
            </div>
            <div class="form-group col-sm-4">

                <?php echo form_dropdown('state', $all_states_arr, '', 'class="form-control select2" id="state"'); ?>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('operationngo_postal_code');?><!--Postal Code--></label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="postalcode" id="postalcode" class="form-control" placeholder="<?php echo $this->lang->line('operationngo_postal_code');?>"><!--Postal Code-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_Country');?><!--Country--></label>
            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2" id="countryID"'); ?>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-6">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/donors_master', '', 'Donors');
        });
        $('.select2').select2();

        contactID = null;

        Inputmask().mask(document.querySelectorAll("input"));

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            contactID = p_id;
            load_donor_header();
        }

        $('#contact_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                name: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_name_is_required');?>.'}}},/*Name is required*/
                //email: {validators: {notEmpty: {message: 'Email is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
                phonePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_phone_primary_is_required');?>.'}}},/*Phone (Primary) is required*/
                countryCodePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_country_code_primary_is_required');?>.'}}},/*Country Code (Primary) is required*/
                address: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_address_is_required');?>.'}}},/*Address is required*/
                countryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_country_is_required');?>.'}}}/*Country is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('OperationNgo/save_donor_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetchPage('system/operationNgo/donors_master', '', 'Donors');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });


    function load_donor_header() {
        if (contactID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contactID': contactID},
                url: "<?php echo site_url('OperationNgo/load_donor_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#contactID_edit').val(contactID);
                        $('#name').val(data['name']);
                        $('#email').val(data['email']);
                        $('#transactionCurrencyID').val(data['currencyID']).change();
                        $('#countryCodePrimary').val(data['phoneCountryCodePrimary']);
                        $('#phoneAreaCodePrimary').val(data['phoneAreaCodePrimary']);
                        $('#phonePrimary').val(data['phonePrimary']);
                        $('#countryCodeSecondary').val(data['phoneCountryCodeSecondary']);
                        $('#phoneAreaCodeSecondary').val(data['phoneAreaCodeSecondary']);
                        $('#phoneSecondary').val(data['phoneSecondary']);
                        $('#fax').val(data['fax']);
                        $('#postalcode').val(data['postalCode']);
                        $('#city').val(data['city']);
                        $('#website').val(data['website']);
                        $('#state').val(data['state']).change();
                        $('#countryID').val(data['countryID']).change();
                        $('#address').val(data['address']);
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