<?php
/*$main_category_arr = all_main_category_drop();
$key = array_filter($main_category_arr, function ($a) {
    return $a == 'FA | Fixed Assets';
});
unset($main_category_arr[key($key)]);*/
$mfqCustomerAutoID = isset($page_id) && !empty($page_id) ? $page_id : 0;
?>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_manage_customer');
echo head_page($title, false);
?>


<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<form method="post" id="from_add_edit_customer">
    <input type="hidden" value="" id="mfqCustomerAutoID" name="mfqCustomerAutoID"/>

    <div class="row">
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('manufacturing_customer_detail'); ?><!--Customer Detail--> </h2>
            </header>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_name'); ?><!--Name--></label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="CustomerName" id="CustomerName" class="form-control" placeholder="<?php echo $this->lang->line('common_name'); ?>"
                           required>
                    <span class="input-req-inner"></span>
                </span>
                </div>

            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_Country'); ?><!--Country--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('customerCountry', load_all_countryName_drop(), '', 'id="customerCountry" class="form-control" required') ?>
                    <!--<select name="Gender" id="Gender" class="form-control" required>
                        <option value="">Select</option>
                        <option value="1">Male</option>
                        <option value="2">Female</option>
                    </select>-->
                    <span class="input-req-inner"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-12 animated zoomIn">
            <header class="head-title">
                <h2><?php echo $this->lang->line('manufacturing_contact_detail'); ?><!--Contact Detail--> </h2>
            </header>

            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_address'); ?><!--Address--> </label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="CustomerAddress1" id="CustomerAddress1" class="form-control"
                           placeholder="<?php echo $this->lang->line('common_address'); ?>"
                           required>
                    <span class="input-req-inner"></span>
                </span>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title"><?php echo $this->lang->line('common_telephone'); ?> <!--Telephone--></label>
                </div>

                <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="customerTelephone" id="customerTelephone" class="form-control"
                           placeholder="<?php echo $this->lang->line('common_telephone'); ?>">
                    <!--<span class="input-req-inner"></span>-->
                </span>
                </div>
            </div>
            <div class="row" id="">
                <div class="form-group col-sm-4">

                </div>
                <div class="form-group col-sm-7 " style="margin-left: -300px">
                    <button type="button" class="btn btn-primary btn-xs pull-right" id="btn_add_email"
                            onclick="add_more()"><i
                            class="fa fa-plus"></i></button>
                </div>

                <div class="form-group col-sm-1">
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div id="append_related_data">
                    <div class="append_data">
                        <div class="row removable-div" id="tr_1">
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="title lbl_mail"><?php echo $this->lang->line('common_email'); ?><!--Email--> </label>
                            </div>
                            <div class="form-group col-sm-4">
                                            <span class="input-req" title="Required Field">
                                                <input type="email" name="customerEmail[]" id="customerEmail"
                                                       class="form-control customerEmail "
                                                       placeholder="@<?php echo $this->lang->line('common_email'); ?>"
                                                       required>
                                                <input type="hidden" name="customerEmailid[]" id="customerEmailid"
                                                       class="form-control customerEmail"
                                                >
                                                <span class="input-req-inner"></span>
                                            </span>
                            </div>
                            <div class="form-group col-sm-1" style="margin-left: -10px;">
                                <input type="checkbox" value="1"
                                       class="maildefaultcheck">
                            </div>
                            <div class="form-group col-sm-2  remove-td " style="margin-left: -49px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-1">
                    &nbsp;
                </div>
                <div class="form-group col-sm-2">
                    <label class="title">Pre Qulified</label>
                </div>

                <div class="form-group col-sm-4">
               
                <div class="skin-section extraColumns"><input id="preQualifiedYN" type="checkbox" data-caption="" class="columnSelected isadd" name="preQualifiedYN" value="1"><label for="checkbox">&nbsp;</label></div>
                
                </div>
            </div>
            <div class="col-md-12 animated zoomIn">
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-7">
                        <div class="pull-right">
                            <button class="btn btn-primary" type="submit" id="submitCustomerBtn"><i
                                    class="fa fa-plus"></i> <?php echo $this->lang->line('manufacturing_add_customer'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
</form>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var search_id = 1;
    $(document).ready(function () {
        setTimeout(function () {
            $("#CustomerName").focus();
        }, 1000);

        //$("#customerCountry").select2();
        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_customers', '', 'Customers');
        });

        $("#from_add_edit_customer").submit(function (e) {
            addEditCustomer();
            return false;
        });
        loadCustomerDetail();

    });


    function addEditCustomer() {
        var mfqCustomerAutoID = '<?php echo $mfqCustomerAutoID ?>';
        var form_data = $("#from_add_edit_customer").serializeArray();
        $('.maildefaultcheck').each(function (i, obj) {
            if (this.checked) {
                form_data.push({name: 'default[]', value: 1});
            } else {
                form_data.push({name: 'default[]', value: 0});
            }
        });

        if ($('#preQualifiedYN').is(':checked')){
            form_data.push({name: 'preQualifiedYN', value: 1});
        }else{
            form_data.push({name: 'preQualifiedYN', value: 0});
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_CustomerMaster/add_edit_customer"); ?>',
            dataType: 'json',
            data: form_data,
            async: false,
            success: function (data) {
                if (data['error'] == 1) {
                    myAlert('e', data['message']);

                }
                else if (data['error'] == 0) {
                    if (data['code'] == 1) {
                        $("#from_add_edit_customer")[0].reset();
                        fetchPage('system/mfq/crew/manage-customer', '', 'Customer');
                    }
                    myAlert('s', data['message']);

                    loadCustomerEmailDetail();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e', xhr.responseText);
            }
        });
    }


    function loadCustomerDetail() {
        var mfqCustomerAutoID = '<?php echo $mfqCustomerAutoID ?>';
        if (mfqCustomerAutoID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_CustomerMaster/loadCustomerDetail"); ?>',
                dataType: 'json',
                data: {mfqCustomerAutoID: mfqCustomerAutoID},
                async: false,
                success: function (data) {
                    if (data['error'] == 0) {
                        //myAlert('s', data['message']);
                        $("#submitCustomerBtn").html('<i class="fa fa-pencil"></i> Save Customer');
                        $("#mfqCustomerAutoID").val(data['master']['mfqCustomerAutoID']);
                        $("#CustomerName").val(data['master']['CustomerName']);
                        $("#customerCountry").val(data['master']['customerCountry']);
                        $("#CustomerAddress1").val(data['master']['CustomerAddress1']);

                        $("#customerTelephone").val(data['master']['customerTelephone']);

                        if (data['master']['preQualifiedYN'] == 1) {
                            $('#preQualifiedYN').iCheck('check');
                        }else{
                            $('#preQualifiedYN').iCheck('uncheck');
                        }

                        if (!jQuery.isEmptyObject(data['details'])) {

                            $(".append_data").html('');
                            var i = 1;
                            $.each(data['details'], function (key, value) {
                                var checked = '';
                                if (value.isDefault == 1) {
                                    checked = 'checked';
                                }

                                $(".append_data").append('<div class="row removable-div" id="tr_' + i + '"><div class="form-group col-sm-1">&nbsp;</div><div class="form-group col-sm-2"><label class="title lbl_mail hide" id="Emaillable">Email </label></div><div class="form-group col-sm-4"><span class="input-req" title="Required Field"><input type="email" name="customerEmail[]" id="customerEmail" value="' + value.email + '" class="form-control customerEmail " placeholder="@email" required> <input type="hidden" name="customerEmailid[]" id="customerEmailid" class="form-control customerEmail" value="' + value.customerEmailAutoID + '" >' +
                                    '<span class="input-req-inner"></span></span></div><div class="form-group col-sm-1" style="margin-left: -10px;">' +
                                    '<input type="checkbox" value="1" ' + checked + ' class="maildefaultcheck" id="checkboxSC"></div><div class="form-group col-sm-2 remove-td " style="margin-left: -49px;" id="deleteIC" > <a onclick="delete_mail(' + value.customerEmailAutoID + ',' + i + ');" class="" id="deletlink">' +
                                    '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" ></span></a></div></div>');

                                i++;
                                if ($('#tr_1')) {
                                    $('#Emaillable').removeClass('hide');

                                }
                            });
                        }


                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }

    function loadCustomerEmailDetail() {
        var mfqCustomerAutoID = '<?php echo $mfqCustomerAutoID ?>';
        if (mfqCustomerAutoID > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_CustomerMaster/loadCustomerDetail"); ?>',
                dataType: 'json',
                data: {mfqCustomerAutoID: mfqCustomerAutoID},
                async: false,
                success: function (data) {
                    if (data['error'] == 0) {
                        if (!jQuery.isEmptyObject(data['details'])) {
                            $(".append_data").html('');
                            var i = 1;
                            $.each(data['details'], function (key, value) {
                                var checked = '';
                                if (value.isDefault == 1) {
                                    checked = 'checked';
                                }
                                $(".append_data").append('<div class="row removable-div" id="tr_' + i + '"><div class="form-group col-sm-1">&nbsp;</div><div class="form-group col-sm-2"><label class="title lbl_mail hide" id="Emaillable">Email </label></div><div class="form-group col-sm-4"><span class="input-req" title="Required Field"><input type="email" name="customerEmail[]" id="customerEmail" value="' + value.email + '" class="form-control customerEmail " placeholder="@email" required> <input type="hidden" name="customerEmailid[]" id="customerEmailid" class="form-control customerEmail" value="' + value.customerEmailAutoID + '" >' +
                                    '<span class="input-req-inner"></span></span></div><div class="form-group col-sm-1" style="margin-left: -10px;">' +
                                    '<input type="checkbox" value="1" ' + checked + ' class="maildefaultcheck" id="checkboxSC"></div><div class="form-group col-sm-2 remove-td " style="margin-top: 10px;" id="deleteIC" > <a onclick="delete_mail(' + value.customerEmailAutoID + ',' + i + ');" class="" id="deletlink">' +
                                    '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" ></span></a></div></div>');

                                i++;
                                if ($('#tr_1')) {
                                    $('#Emaillable').removeClass('hide');

                                }
                            });
                        }
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', xhr.responseText);
                }
            });
        }
    }


    function add_more() {
        search_id += 1;
        var appendData = $('#tr_1').clone();
        appendData.find('.customerEmail').val('');
        appendData.find('.lbl_mail').html('<span class="remove-tr" style="color:rgb(209, 91, 71);"></span>');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" onclick="remove_app_div(this)" style="color:rgb(209, 91, 71);"></span>');
        $('.append_data').append(appendData);
        /*$( ".bt_remove" ).remove();*/

    }


    function remove_app_div(obj) {
        $(obj).closest('.removable-div').remove()
    }

    function delete_mail(id, element) {
        swal({
                title: "Are you sure?!", /*/!*Are you sure?*!/*/
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete", /*Delete*/
                cancelButtonText: "cencel"

            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'customerEmailAutoID': id},
                    url: "<?php echo site_url('MFQ_CustomerMaster/delete_mail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {

                        stopLoad();
                        if (element == 1) {
                            $('#customerEmail').val('');
                            $('#checkboxSC').prop("checked", false);


                        } else {

                            $('#tr_' + element).hide();


                        }


                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

</script>