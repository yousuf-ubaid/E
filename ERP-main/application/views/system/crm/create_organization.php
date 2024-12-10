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
$groupmaster_arr = all_crm_groupMaster();
$employees_arr = fetch_employees_by_company_multiple(false);
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
</style>

<?php echo form_open('', 'role="form" id="organization_form"'); ?>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_organization_name');?></h2><!--ORGANIZATION NAME-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_name');?></label><!--Name-->
            </div>
            <div class="form-group col-sm-4">
                               <span class="input-req" title="Required Field"><input type="text" name="Name"
                                                                                     id="Name"
                                                                                     class="form-control"
                                                                                     placeholder="<?php echo $this->lang->line('common_name');?>"
                                                                                     required><span
                                       class="input-req-inner"></span></span><!--Name-->
                <input type="hidden" name="organizationID" id="organizationID_edit">
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_industry');?></label><!--Industry-->

            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="industry" id="industry" class="form-control"
                       placeholder="<?php echo $this->lang->line('crm_industry');?>"><!--Industry-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_number_of_employees');?></label><!--Number of Employees-->

            </div>
            <div class="form-group col-sm-4">
                <input type="number" name="numberofEmployees" id="numberofEmployees" class="form-control"
                       placeholder="<?php echo $this->lang->line('crm_number_of_employees');?>"><!--Number of Employees-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_website');?></label><!--Website-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="website" id="website" class="form-control"
                       placeholder="<?php echo $this->lang->line('crm_website');?>"><!--Website-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_link_to_customer');?></label><!--Link To Customer-->
            </div>
            <div class="form-group col-sm-4">
                <?php echo form_dropdown('linktocustomerID', all_customer_drop(), '', 'class="form-control select2" id="linktocustomerID"'); ?>
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
                <label class="title"><?php echo $this->lang->line('common_email');?></label><!--Email-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="email" id="email" class="form-control" placeholder="<?php echo $this->lang->line('common_email');?>" ><!--Email-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_phone');?></label><!--Phone-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="telephoneNo" id="telephoneNo" class="form-control" placeholder="<?php echo $this->lang->line('crm_phone_no');?>"><!--Phone No-->
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
            <h2><?php echo $this->lang->line('crm_billing_address');?></h2><!--BILLING ADDRESS-->
        </header>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_address');?></label><!--Address-->
            </div>
            <div class="form-group col-sm-4">
               <textarea class="form-control" id="billingAddress" name="billingAddress" rows="2"></textarea>
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
                <input type="text" name="billingCity" id="billingCity" class="form-control" placeholder="<?php echo $this->lang->line('crm_city');?>"><!--City-->
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
                <input type="text" name="billingState" id="billingState" class="form-control" placeholder="<?php echo $this->lang->line('crm_state');?>"><!--State-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('crm_postal_code');?></label> <!--Postal Code-->
            </div>
            <div class="form-group col-sm-4">
                <input type="text" name="billingPostalCode" id="billingPostalCode" class="form-control" placeholder="<?php echo $this->lang->line('crm_postal_code');?>"><!--Postal Code-->
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
                <?php echo form_dropdown('billingCountryID', $countries_arr, '', 'class="form-control select2" id="billingCountryID"'); ?>
            </div>
        </div>

    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('crm_shipping_address');?></h2><!--SHIPPING ADDRESS-->
        </header>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <button class="btn btn-xs btn-primary pull-right" id="save_itm_btn" type="button" onclick="organizationBillingDetail();"><?php echo $this->lang->line('crm_copy_detail');?></button><!--Copy Detail-->
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="form-group col-sm-1">
                &nbsp
            </div>
            <div class="form-group col-sm-2">
                <label class="title"><?php echo $this->lang->line('common_address');?></label><!--Address-->
            </div>
            <div class="form-group col-sm-4">
                <textarea class="form-control" id="shippingAddress" name="shippingAddress" rows="2"></textarea>
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
                <input type="text" name="shippingCity" id="shippingCity" class="form-control" placeholder="<?php echo $this->lang->line('crm_city');?>"><!--City-->
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
                <input type="text" name="shippingState" id="shippingState" class="form-control" placeholder="<?php echo $this->lang->line('crm_state');?>"><!--State-->
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
                <input type="text" name="shippingPostalCode" id="shippingPostalCode" class="form-control" placeholder="<?php echo $this->lang->line('crm_postal_code');?>"><!--Postal Code-->
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
                <?php echo form_dropdown('shippingCountryID', $countries_arr, '', 'class="form-control select2" id="shippingCountryID"'); ?>
            </div>
        </div>
    </div>
</div>
<br>
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
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12 animated zoomIn">
        <header class="head-title">
            <h2><?php echo $this->lang->line('common_description');?></h2><!--DESCRIPTION-->
        </header>
        <div class="row">
            <div class="form-group col-sm-10" style="margin-top: 5px;">
                               <textarea class="form-control" rows="5" name="description" id="description"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="text-right m-t-xs">
                <div class="form-group col-sm-10" style="margin-top: 10px;">
                    <button class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save');?></button><!--Save-->
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/crm/organization_management', '', 'Contact');
        });
        $('.select2').select2();

        organizationID = null;

        Inputmask().mask(document.querySelectorAll("input"));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            organizationID = p_id;
            load_organization_header();
        }else{
            $("#description").wysihtml5();
        }

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('#organization_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                Name: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_name_is_required');?>.'}}}/*Name is required*/
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
                url: "<?php echo site_url('Crm/save_organization_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetchPage('system/crm/organization_management', '', 'Organizations');
                    }  else {
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
            data: {'MasterAutoID': organizationID},
            url: "<?php echo site_url('Crm/fetch_organization_employee_detail'); ?>",
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
                        $('#table_body').append('<tr><td>' + x + '</td><td>' + value['ECode'] + '</td><td>' + value['Ename2'] + '</td><td class="text-right"><a onclick="delete_organization_detail(' + value['AssingeeID'] + ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td></tr>');
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

    function load_organization_header() {
        if (organizationID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'organizationID': organizationID},
                url: "<?php echo site_url('Crm/load_organization_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#organizationID_edit').val(organizationID);
                        $('#Name').val(data['header']['Name']);
                        $('#industry').val(data['header']['industry']);
                        $('#numberofEmployees').val(data['header']['numberofEmployees']);
                        $('#telephoneNo').val(data['header']['telephoneNo']);
                        $('#fax').val(data['header']['fax']);
                        $('#website').val(data['header']['website']);
                        $('#email').val(data['header']['email']);
                        $('#billingAddress').val(data['header']['billingAddress']);
                        $('#billingCity').val(data['header']['billingCity']);
                        $('#billingCountryID').val(data['header']['billingCountryID']).change();
                        $('#billingPostalCode').val(data['header']['billingPostalCode']);
                        $('#billingState').val(data['header']['billingState']);
                        $('#shippingAddress').val(data['header']['shippingAddress']);
                        $('#shippingCity').val(data['header']['shippingCity']);
                        $('#shippingCountryID').val(data['header']['shippingCountryID']).change();
                        $('#shippingPostalCode').val(data['header']['shippingPostalCode']);
                        $('#shippingState').val(data['header']['shippingState']);
                        $("#description").wysihtml5();
                        $('#description').val(data['header']['description']);
                        $('#linktocustomerID').val(data['header']['customerID']).change();
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

    function delete_organization_detail(id) {
        if (organizationID) {
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
                        url: "<?php echo site_url('Crm/delete_organization_detail'); ?>",
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

    function organizationBillingDetail(){
        $('#shippingAddress').val($('#billingAddress').val());
        $('#shippingCity').val($('#billingCity').val());
        $('#shippingState').val($('#billingState').val());
        $('#shippingPostalCode').val($('#billingPostalCode').val());
        $('#shippingCountryID').val($('#billingCountryID').val()).change();
    }

</script>