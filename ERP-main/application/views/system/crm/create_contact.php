<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$countries_arr = load_all_countrys();
$campaigns_arr = load_all_campaigns();
$organization_arr = load_all_organizations(false);
$leadSource_arr = all_crm_leadSource();

$employees_arr = fetch_employees_by_company_multiple(false);
$groupmaster_arr = all_crm_groupMaster();
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
            <h2><?php echo $this->lang->line('crm_name_and_occupation');?></h2><!--NAME AND OCCUPATION-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label><?php echo $this->lang->line('crm_first_name');?><!--First Name-->
            </div>
            <div class="form-group col-sm-1">
                               <span class="input-req" title="Required Field"><input type="text" name="prefix"
                                                                                     id="prefix"
                                                                                     class="form-control valcontact calcontactread"
                                                                                     placeholder="<?php echo $this->lang->line('crm_prefix');?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--Prefix-->
            </div>
            <div class="form-group col-sm-3">
                               <span class="input-req" title="Required Field"><input type="text" name="firstName"
                                                                                     id="firstName"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('crm_first_name');?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--First Name-->
                <input type="hidden" name="contactID" id="contactID_edit">
            </div>
            <div class="form-group col-sm-4">
                          <span class="input-req" title="Required Field"><input type="text" name="lastName"
                                                                                id="lastName" class="form-control"
                                                                                placeholder="<?php echo $this->lang->line('crm_last_name');?>"
                                                                                required><span
                                  class="input-req-inner"></span></span><!--Last Name-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_occupation');?></label><!--Occupation-->

            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="occupation" id="occupation" class="form-control"
                       placeholder="Role">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_department');?></label><!--Department-->

            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="department" id="department" class="form-control"
                       placeholder="Department">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;" id="organization_text">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_organization');?></label><!--Organization-->

            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="organization" id="organization" class="form-control"
                       placeholder="Organization">
            </div>
            <div class="col-sm-1 search_cancel" style="width: 3%;">
                <i class="fa fa-link" onclick="linkOrganization()" title="Link to Organization" aria-hidden="true"
                   style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
            </div>
        </div>
       
        <div class="row hide" id="linkorganization_text" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Link Organization</label>

            </div>
            <div class="form-group col-sm-4">
                <?php echo form_dropdown('linkorganization[]', $organization_arr, '', 'class="form-control select2" id="linkorganization"  multiple=""'); ?>
            </div>
            <div class="col-sm-1 search_cancel" style="width: 3%;">
                <i class="fa fa-external-link" onclick="unlinkOrganization()" title="Link to Organization"
                   aria-hidden="true" style="margin-left: 20%;color: #00a5e6;margin-top: 10%;font-size: 18px;"></i>
            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">

                <label class="title"><?php echo $this->lang->line('crm_lead_source');?></label><!--Lead Source-->

            </div>
            <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('sourceID', $leadSource_arr, '', 'class="form-control" id="sourceID"  required'); ?>
                    <span class="input-req-inner"></span></span>
            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Remark</label><!--Remark-->

            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="remarkContact" id="remarkContact" class="form-control"
                       placeholder="Remark">
            </div>
        </div>

    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_contact_details');?></h2><!--CONTACT DETAILS-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_email');?> </label><!--Email-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="email" id="email" class="form-control" placeholder="<?php echo $this->lang->line('common_email');?>" >
            </div><!--Email-->
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_phone_mobile');?> </label><!--Phone (Mobile)-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="phoneMobile" id="phoneMobile" class="form-control" placeholder="<?php echo $this->lang->line('crm_phone_mobile');?>"><!--Phone (Mobile)-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_phone_home');?> </label><!--Phone (Home)-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="phoneHome" id="phoneHome" class="form-control"
                       placeholder="<?php echo $this->lang->line('crm_phone_home');?>"><!--Phone (Home)-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_fax');?></label><!--Fax-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="fax" id="fax" class="form-control" placeholder="<?php echo $this->lang->line('common_fax');?>"><!--Fax-->
            </div>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_address_capital');?><!--ADDRESS--></h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_address');?> </label><!--Address-->
            </div>
            <div class="form-group col-sm-4">
                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_city');?></label><!--City-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="city" id="city" class="form-control" placeholder="<?php echo $this->lang->line('crm_city');?>"><!--City-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_state');?></label><!--State-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="state" id="state" class="form-control" placeholder="<?php echo $this->lang->line('crm_state');?>"><!--State-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_postal_code');?></label><!--Postal Code-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="postalcode" id="postalcode" class="form-control" placeholder="<?php echo $this->lang->line('crm_postal_code');?>"><!--Postal Code-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_Country');?></label><!--Country-->
            </div>
            <div class="form-group col-sm-4">
                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2" id="countryID"'); ?>
            </div>
        </div>
    </div>
</div>
<br>
<!--<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2>ADDITIONAL INFORMATION</h2>
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Account Name</label>
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="AccountID" id="AccountID" class="form-control" placeholder="Account Name">
            </div>
        </div>
        <div class="row hide" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title">Campaign</label>
            </div>
            <div class="form-group col-sm-4">
                <?php /*echo form_dropdown('campaignID', $campaigns_arr, '', 'class="form-control select2" id="campaignID"'); */?>
            </div>
        </div>
    </div>
</div>
<br>-->
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_permissions');?></h2><!--PERMISSIONS-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_visibility');?></label><!--Visibility-->
            </div>
            <div class="form-group col-sm-1">
                <div class="iradio_square-blue">
                    <div class="skin-section extraColumns"><input id="isPermissionEveryone" type="radio"
                                                                  data-caption="" class="columnSelected"
                                                                  name="userPermission"
                                                                  value="1"><label for="checkbox">&nbsp;</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2" style="margin-left: -6%;">
                <label style="font-weight: 400"><?php echo $this->lang->line('crm_everyone');?></label><!--Everyone-->
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-1">
                <div class="iradio_square-blue">
                    <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionCreator" type="radio" data-caption="" class="columnSelected" value="2"><label for="checkbox">&nbsp;</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2" style="margin-left: -6%;">
                <label style="font-weight: 400"><?php echo $this->lang->line('crm_only_for_me');?></label><!--Only For Me-->
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-1">
                <div class="iradio_square-blue">
                    <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionGroup" type="radio"
                                                                  data-caption="" class="columnSelected"
                                                                  onclick="leadPermission(3)"
                                                                  value="3"><label for="checkbox">&nbsp;</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2" style="margin-left: -6%;">
                <label style="font-weight: 400"><?php echo $this->lang->line('crm_select_a_group');?></label><!--Select a Group-->
            </div>
        </div>
        <div class="row hide" id="show_groupPermission">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-4" style="margin-left: 2%;">
                <?php echo form_dropdown('groupID', $groupmaster_arr, '', 'class="form-control" id="groupID"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-1">
                <div class="iradio_square-blue">
                    <div class="skin-section extraColumns"><input name="userPermission" id="isPermissionMultiple"
                                                                  type="radio"
                                                                  data-caption="" class="columnSelected"
                                                                  onclick="leadPermission(4)"
                                                                  value="4"><label for="checkbox">&nbsp;</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-sm-2" style="margin-left: -6%;">
                <label style="font-weight: 400"><?php echo $this->lang->line('crm_select_multiple_pepole');?></label><!--Select Multiple People-->
            </div>
        </div>
        <div class="row hide" id="show_multiplePermission">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"></label>
            </div>
            <div class="form-group col-sm-4" style="margin-left: 2%;">
                <?php echo form_dropdown('employees[]', $employees_arr, '', 'class="form-control select2" id="employeesID"  multiple="" style="z-index: 0;"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-1" style="margin-top: 10px;">
                &nbsp
            </div>
            <div class="form-group col-sm-11">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save');?></button><!--Save-->
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/crm/contact_management', '', 'Contact');
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
            load_contact_header();
        }


        var related_document = '<?php if(isset($_POST['policy_id']) && !empty($_POST['policy_id'])) { echo $_POST['policy_id']; } ?>';
        var masterID = '<?php if(isset($_POST['data_arr']) && !empty($_POST['data_arr'])) { echo json_encode($_POST['data_arr']); } ?>';

        if (masterID != null && masterID.length > 0) {
            var masterIDNew = JSON.parse(masterID);
            if (related_document == 8) {
                load_organization(related_document,masterIDNew)
                $('.headerclose').click(function () {

                        fetchPage('system/crm/organization_edit_view', masterIDNew, 'View Organizations','organizationcontact');


                });
            }
        }

            $('#contact_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                firstName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_first_name_is_required');?>.'}}},/*First Name is required*/
                lastName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_last_name_is_required');?>.'}}}/*Last Name is required*/
            },/*Address is required*/
        }).on('success.form.bv', function (e) {
                $('#linkorganization').prop('disabled', false);
            e.preventDefault();
            var $form = $(e.target);
         /*   var related_document = $('#related_document').val();*/
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
             /*   $('#linkorganization').prop('disabled', false);*/
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Crm/save_contact_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        if(related_document == 8)
                        {
                            setTimeout(function(){
                                fetchPage('system/crm/organization_edit_view', masterIDNew, 'View Organizations');
                            }, 250);
                        }else
                        {
                            fetchPage('system/crm/contact_management', '', 'Contact');
                        }

                    } else {
                        $('.btn-primary').removeAttr('disabled');
                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
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

    });

    function fetch_detail() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'MasterAutoID': contactID},
            url: "<?php echo site_url('Crm/fetch_contact_employee_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data)) {
                    $('#table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                    ;
                }
                else {
                    $.each(data, function (key, value) {
                        $('#table_body').append('<tr><td>' + x + '</td><td>' + value['ECode'] + '</td><td>' + value['Ename2'] + '</td><td class="text-right"><a onclick="delete_contact_detail(' + value['AssingeeID'] + ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td></tr>');
                        x++;
                    });
                    $('#t_total').html(parseFloat(tot_amount).formatMoney(currency_decimal, '.', ','));
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_contact_header() {
        if (contactID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contactID': contactID},
                url: "<?php echo site_url('Crm/load_contact_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['header'])) {
                        $('#contactID_edit').val(contactID);
                        $('#prefix').val(data['header']['prefix']);
                        $('#firstName').val(data['header']['firstName']);
                        $('#lastName').val(data['header']['lastName']);
                        $('#occupation').val(data['header']['occupation']);
                        $('#department').val(data['header']['department']);
                        $('#email').val(data['header']['email']);
                        $('#phoneMobile').val(data['header']['phoneMobile']);
                        $('#phoneHome').val(data['header']['phoneHome']);
                        $('#fax').val(data['header']['fax']);
                        $('#postalcode').val(data['header']['postalCode']);
                        $('#city').val(data['header']['city']);
                        $('#state').val(data['header']['state']);
                        $('#countryID').val(data['header']['countryID']).change();
                        $('#address').val(data['header']['address']);
                        //$('#AccountID').val(data['AccountID']).change();
                        $('#campaignID').val(data['header']['campaignID']).change();
                        $('#sourceID').val(data['header']['sourceID']).change();
                        $('#remarkContact').val(data['header']['remark']);
                        if (data['header']['organization'] != '') {
                            $('#organization').val(data['header']['organization']);
                        } else {
                            linkOrganization();
                        }
                    }
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        var selectedItems = [];
                        $.each(data['detail'], function (key, value) {
                            selectedItems.push(value.relatedDocumentMasterID);
                            $('#linkorganization').val(selectedItems).change();
                        });
                    }
                    if (!jQuery.isEmptyObject(data['permission'])) {
                        var selectedItems = [];
                        $.each(data['permission'], function (key, value) {
                            if(value.permissionID == 1){
                                $('#isPermissionEveryone').iCheck('check');
                            } else if(value.permissionID == 2){
                                $('#isPermissionCreator').iCheck('check');
                            } else if(value.permissionID == 3){
                                $('#isPermissionGroup').iCheck('check');
                                $('#groupID').val(value.permissionValue);
                            } else if(value.permissionID == 4){
                                $('#isPermissionMultiple').iCheck('check');
                                selectedItems.push(value.empID);
                                $('#employeesID').val(selectedItems).change();
                            }
                        });
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

    function delete_contact_detail(id) {
        if (contactID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('crm_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'AssingeeID': id},
                        url: "<?php echo site_url('Crm/delete_contact_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            setTimeout(function () {
                                fetch_detail();
                            }, 300);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function linkOrganization() {
        $('#organization').val('');
        $('#linkorganization_text').removeClass('hide');
        $('#organization_text').addClass('hide');
    }

    function unlinkOrganization() {
        $('#linkorganization_text').addClass('hide');
        $('#organization_text').removeClass('hide');
    }
    function load_organization(related_document,masterIDNew)
    {
        $('#linkorganization_text').removeClass('hide');
        $('#organization_text').addClass('hide');
        $('#linkorganization').val(masterIDNew).change();
        $('#related_document').val(related_document);
        $('#linkorganization').prop('disabled', true);
    }

</script>