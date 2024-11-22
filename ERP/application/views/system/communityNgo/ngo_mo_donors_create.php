<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$all_states_arr = all_states();
$countries_arr = load_all_countries();
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
                <h2><?php echo $this->lang->line('communityngo_donor_name_and_detail');?><!--DONOR NAME AND DETAIL--></h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_name');?><!--Name--></label>
                </div>
                <div class="form-group col-sm-4">

                    <div class="input-group">
                        <input type="text" class="form-control" id="name" name="name" placeholder="<?php echo $this->lang->line('common_name');?>" required>
                        <input type="hidden" class="form-control" id="requestedComMemID" name="requestedComMemID">
                        <input type="hidden" class="form-control" id="contactImage" name="contactImage">
                        <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clear_comMem_details()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Community Member" rel="tooltip"
                                onclick="link_comMem_modal()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                    </span>
                    </div>
                    <input type="hidden" name="contactID" id="contactID_edit">
                    
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
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
                    &nbsp;
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
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_phone');?> (<?php echo $this->lang->line('communityngo_TP_MobileNo');?>) </label><!--Phone (Primary)-->
                </div>
                <div class="form-group col-sm-1" style="width: 12%">
                                     <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('countryCodePrimary', $countryCode_arr, '', 'class="form-control" id="countryCodePrimary"'); ?>
                                         <span class="input-req-inner"></span></span>
                </div>
                <div class="form-group col-sm-1" style="padding-left: 0px;">
                    <input type="text" name="phoneAreaCodePrimary" id="phoneAreaCodePrimary" class="form-control" placeholder="<?php echo $this->lang->line('communityngo_AreaCode');?>"><!--Area Code-->
                </div>
                <div class="form-group col-sm-3" style="padding-left: 0px;">
                  <span class="input-req" title="Required Field">
            <input type="text" name="phonePrimary" id="phonePrimary" class="form-control" placeholder="<?php echo $this->lang->line('communityngo_TP_No');?>" required><!--Phone Number-->
                      <span class="input-req-inner"></span></span>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><!--Phone--><?php echo $this->lang->line('common_phone');?> (<?php echo $this->lang->line('communityngo_secondary');?>)</label><!--Secondary-->
                </div>
                <div class="form-group col-sm-1" style="width: 12%">
                    <?php echo form_dropdown('countryCodeSecondary', $countryCode_arr, '', 'class="form-control" id="countryCodeSecondary"'); ?>
                </div>
                <div class="form-group col-sm-1" style="padding-left: 0px;">
                    <input type="text" name="phoneAreaCodeSecondary" id="phoneAreaCodeSecondary" class="form-control" placeholder="<?php echo $this->lang->line('communityngo_AreaCode');?>"><!--Area Code-->
                </div>
                <div class="form-group col-sm-3" style="padding-left: 0px;">
                    <input type="text" name="phoneSecondary" id="phoneSecondary" class="form-control"
                           placeholder="<?php echo $this->lang->line('communityngo_TP_No');?>"><!--Phone Number-->
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
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
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_website');?><!--Website--></label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" name="website" id="website" class="form-control"
                           placeholder="<?php echo $this->lang->line('communityngo_website');?>"><!--Website-->
                </div>
            </div>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('communityngo_Job_Address');?><!--ADDRESS--></h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
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
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_city');?><!--City--></label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" name="city" id="city" class="form-control" placeholder="<?php echo $this->lang->line('communityngo_city');?>"><!--City-->
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_Province');?><!--Province--></label>
                </div>
                <div class="form-group col-sm-4">

                    <?php echo form_dropdown('state', $all_states_arr, '', 'class="form-control select2" id="state"'); ?>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('communityngo_PostalCode');?><!--Postal Code--></label>
                </div>
                <div class="form-group col-sm-4">
                    <input type="text" name="postalcode" id="postalcode" class="form-control" placeholder="<?php echo $this->lang->line('communityngo_Country');?>"><!--Postal Code-->
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
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
                    &nbsp;
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


    <div class="modal fade bs-example-modal-lg" id="com_mem_modal" role="dialog"
         aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="exampleModalLabel">
                        <?php echo $this->lang->line('communityngo_link_member'); ?></h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('communityngo_name_of_member'); ?><!--Memeber--></label>
                            <div class="col-sm-7">
                                <?php
                                $comMem_arr = all_member_drop();
                                echo form_dropdown('Com_MasterID', $comMem_arr, '', 'class="form-control select2" id="Com_MasterID" required'); ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" class="btn btn-primary" onclick="fetch_comMem_details()">
                        <?php echo $this->lang->line('communityngo_add_member'); ?><!--Add member--></button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_donors_master', '', 'Community Donors');
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
                    phonePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_TP_MobileNo_required');?>.'}}},/*Phone (Primary) is required*/
                    countryCodePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_country_code_primary_is_required');?>.'}}},/*Country Code (Primary) is required*/
                    address: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_address_is_required');?>.'}}},/*Address is required*/
                    countryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_country_is_required');?>.'}}}/*Country is required*/
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
                    url: "<?php echo site_url('CommunityNgo/save_com_donor_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/communityNgo/ngo_mo_donors_master', '', 'Community Donors');
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

        function clear_comMem_details() {
            $('#Com_MasterID').val('').change();
            $('#name').val('').trigger('input');
            $('#requestedComMemID').val('');
            $('#contactImage').val('');
            $('#name').prop('readonly', false);

            $('#email').val('');
            $('#countryCodePrimary').val('').change();
            $('#phoneAreaCodePrimary').val('');
            $('#phonePrimary').val('');
            $('#countryCodeSecondary').val('').change();
            $('#phoneAreaCodeSecondary').val('');
            $('#phoneSecondary').val('');
            $('#address').val('');

            $('#city').val('').change();
            $('#state').val('').change();
            $('#countryID').val('').change();
            $('#postalcode').val('');

        }

        function link_comMem_modal() {
            $('#Com_MasterID').val('').change();
            $('#com_mem_modal').modal('show');
        }


        function fetch_comMem_details() {
            var Com_MasterID = $('#Com_MasterID').val();

            if (Com_MasterID) {
                window.EIdNo = Com_MasterID;
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'Com_MasterID': Com_MasterID},
                    url: "<?php echo site_url('CommunityNgo/fetch_member_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data) {
                            $('#name').val(data['CName_with_initials']).trigger('input');
                            $('#requestedComMemID').val(Com_MasterID);
                            $('#contactImage').val(data['CImage']);

                            $('#name').prop('readonly', true);
                            $('#com_mem_modal').modal('hide');
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });

                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "CommunityNgo/searchCommunity_donor",
                    data: {Com_MasterID: Com_MasterID},
                    success: function (datum) {

                        $('#email').val(datum.EmailID);
                        $('#countryCodePrimary').val(datum.CountryCodePrimary).change();
                        $('#phoneAreaCodePrimary').val(datum.AreaCodePrimary);
                        $('#phonePrimary').val(datum.TP_Mobile);
                        $('#countryCodeSecondary').val(datum.countryCodeSecondary).change();
                        $('#phoneAreaCodeSecondary').val(datum.AreaCodeSecondary);
                        $('#phoneSecondary').val(datum.TP_home);
                        $('#address').val(datum.C_Address);

                        $('#city').val(datum.district).change();
                        $('#state').val(datum.province).change();
                        $('#countryID').val(datum.countyID).change();
                        $('#postalcode').val(datum.postalCode);

                    }
                });

            } else {

            }

        }

        function load_donor_header() {
            if (contactID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'contactID': contactID},
                    url: "<?php echo site_url('CommunityNgo/load_com_donor_header'); ?>",
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
                            if (data['Com_MasterID'] > 0) {
                                $('#name').prop('readonly', true);
                                $('#requestedComMemID').val(data['Com_MasterID']);
                                $('#contactImage').val(data['contactImage']);
                            }
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
<?php
