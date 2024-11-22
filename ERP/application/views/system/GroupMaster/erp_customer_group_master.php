<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_customer_master');
echo head_page($title, false);

/*echo head_page('Customer Master', true);*/
$customer_arr = all_customer_grp_drop(false);
$customerCategory = party_category(1, false);
$currncy_arr = all_currency_new_drop(false);
$policydescription = getPolicydescription_masterid(2);
$policyvalue = getgrouppolicyvalues($policydescription['grouppolicymasterID'] ?? '');
$policyvalue_detail = getPolicydescription_values_detail($policydescription['grouppolicymasterID'] ?? '');

//$customerLinkingCompany = customer_company_link();

?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_customer_name');?> <!--Customer Name--></label><br>
            <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_category');?> <!--Category--></label><br>
            <?php echo form_dropdown('category[]', $customerCategory, '', 'class="form-control" id="category" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_currency');?> <!--Currency--></label><br>
            <?php echo form_dropdown('currency[]', $currncy_arr, '', 'class="form-control" id="currency" onchange="Otable.draw()" multiple="multiple"'); ?>
        </div>

        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp;</label><br>

            <button type="button" class="btn btn-sm btn-primary pull-right"
                    onclick="clear_all_filters()" style=""><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?> <!--Clear-->
            </button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td>
                    <span class="glyphicon glyphicon-stop" style="color:green; font-size:15px;"></span> <?php echo $this->lang->line('common_active');?><!--Active-->
                </td>
                <td>
                    <span class="glyphicon glyphicon-stop" style="color:red; font-size:15px;"></span> <?php echo $this->lang->line('config_common_inactive');?><!--Inactive-->
                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/GroupMaster/erp_customer_group_master_new',null,'<?php echo $this->lang->line('config_add_new_customer');?>','CUS');"><!--Add New Customer-->
            <i class="fa fa-plus"></i> <?php echo $this->lang->line('config_create_customer');?><!--Create Customer-->
        </button>

    </div>
</div>
<br>
<div class="row">
    <div class="col-md-4">
        <label for=""><?php echo $policydescription['groupPolicyDescription'] ?? '' ?></label>
    </div>
        <div class="col-md-1">
            <?php echo form_dropdown('isallow', $policyvalue, $policyvalue_detail['value'] ?? '', 'class="form-control" id="isallow" onchange="updatepolicy(this.value)" '); ?>
        </div>
    </div>
<hr>
<div class="table-responsive">
    <table id="customer_group_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 3%">#</th>
            <th style="min-width: 20%"> <?php echo $this->lang->line('config_group_customer_code');?><!--Group Customer Code--></th>
            <th style="min-width:60%"><?php echo $this->lang->line('config_group_customer_details');?><!--Group Customer Details--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_category');?><!--Category--></th>
            <th style="min-width: 2%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="customerLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="customerlink_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_customer_link');?><!--Customer Link--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="customerName"><h4><?php echo $this->lang->line('common_customer_name');?><!--Customer Name--> :- </h4></label>
                        <label style="color: cornflowerblue;font-size: 1.2em;" id="customerName"></label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnyCustomers">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('config_common_add_link');?><!--Add Link-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="customerDuplicateModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="customer_duplicate_form"'); ?>
            <input type="hidden" name="customerAutoIDDuplicatehn" id="customerAutoIDDuplicatehn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Customer Replicate <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnycustomerDuplicate">

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSavedup">Replicate
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="invalidinvoicemodal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Chart of account or category not linked</h4>
            </div>
            <div class="modal-body">
                <div >
                    <table  class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th >Company</th>
                            <th>Message</th>
                        </tr>
                        </thead>
                        <tbody id="errormsg">

                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var groupCustomerAutoID;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/GroupMaster/erp_customer_group_master', '', 'Customer Master');
        });
        $('#customerCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#category').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#currency').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        customer_table();
        $('#customerlink_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'groupCustomerMasterID', 'value': groupCustomerAutoID});
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('CustomerGroup/save_customer_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSave').attr('disabled',false);
                        if (data[0] == 's') {
                            /*load_customer_link_table();
                            load_company(groupCustomerAutoID);
                            $('#loadComapnyCustomers').addClass('hidden');*/
                            $('#customerLinkModal').modal('hide');
                            load_all_companies_customers();
                        }

                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        $('#btnSave').attr('disabled',false);
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });

        $('#customer_duplicate_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('CustomerGroup/save_customer_duplicate'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSavedup').attr('disabled',false);
                        if (data[0] == 's') {
                            load_all_companies_duplicate();
                            $('#customerDuplicateModal').modal('hide');
                        }
                        if (jQuery.isEmptyObject(data[2])) {

                        } else {
                            $('#errormsg').empty();
                            $.each(data[2], function (key, value) {
                                $('#errormsg').append('<tr><td>' + value['companyname'] + '</td><td>' + value['message'] + '</td></tr>');
                            });
                            $('#invalidinvoicemodal').modal('show');
                            $('#customerDuplicateModal').modal('hide');
                        }


                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });
    });

    function customer_table() {
        var Otable = $('#customer_group_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('CustomerGroup/fetch_customer'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "groupCustomerAutoID"},
                {"mData": "groupcustomerSystemCode"},
                {"mData": "customer_detail"},
                {"mData": "categoryDescription"},
                /*{"mData": "amt"},
                 {"mData": "confirmed"},*/
                {"mData": "edit"},
                {"mData": "groupCustomerName"},
                {"mData": "customerAddress1"},
                {"mData": "customerAddress2"},
                {"mData": "customerCountry"},
                {"mData": "secondaryCode"},
                {"mData": "customerCurrency"},
                {"mData": "customerEmail"},
                {"mData": "customerTelephone"}
            ],
            "columnDefs": [{"targets": [4], "orderable": false}, {
                "visible": false,
                "searchable": true,
                "targets": [5, 6, 7, 8, 9, 10, 11, 12]
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "customerCode", "value": $("#customerCode").val()});
                aoData.push({"name": "category", "value": $("#category").val()});
                aoData.push({"name": "currency", "value": $("#currency").val()});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function delete_customer(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_you_want_to_delete_this_customer');?>",/*You want to delete this customer!*/
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
                    data: {'groupCustomerAutoID': id},
                    url: "<?php echo site_url('CustomerGroup/delete_customer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        customer_table();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#customerCode').multiselect2('deselectAll', false);
        $('#customerCode').multiselect2('updateButtonText');
        $('#category').multiselect2('deselectAll', false);
        $('#category').multiselect2('updateButtonText');
        $('#currency').multiselect2('deselectAll', false);
        $('#currency').multiselect2('updateButtonText');
        customer_table();
    }

    function openLinkModal(id) {
        $('#customerLinkModal').modal({backdrop: "static"});
        $('#companyIDdrp').val('').change();
        $('#loadComapnyCustomers').addClass('hidden');
        $('#btnSave').attr('disabled', false);
        groupCustomerAutoID = id;
        /*load_company(id);
        load_customer_link_table();*/
        load_all_companies_customers();
        load_customer_header();
        //$('#customerlink_form').bootstrapValidator('revalidateField', 'companyID');
    }

    function load_comapny_customers() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: $('#companyIDdrp').val(), All: 'true'},
            url: "<?php echo site_url('CustomerGroup/load_comapny_customers'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyCustomers').html(data);
                $('.select2').select2();
                $('#loadComapnyCustomers').removeClass('hidden');
            }, error: function () {

            }
        });
    }

    function load_company(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupCustomerMasterID: id, All: 'true'},
            url: "<?php echo site_url('CustomerGroup/load_company'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapny').html(data);
                $('.select2').select2();
                $('#loadComapny').removeClass('hidden');
               // load_comapny_customers();
            }, error: function () {

            }
        });
    }

    function load_customer_link_table() {
        var Otable = $('#customer_link_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('CustomerGroup/fetch_customer_link'); ?>",
            "aaSorting": [[0, 'desc']],
            "searching": false,
            "bLengthChange": false,
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "groupCustomerDetailID"},
                {"mData": "customerSystemCode"},
                {"mData": "company_name"},
                {"mData": "customer_detail"},
                {"mData": "GLDescription"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "groupCustomerMasterID", "value": groupCustomerAutoID});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function delete_customer_link(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_you_want_to_delete_this_customer');?>",/*You want to delete this customer!*/
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
                    data: {'groupCustomerDetailID': id},
                    url: "<?php echo site_url('CustomerGroup/delete_customer_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            /*load_company(groupCustomerAutoID);
                            load_customer_link_table();*/
                        }
                        stopLoad();
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
    }

    function load_all_companies_customers(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupCustomerAutoID: groupCustomerAutoID},
            url: "<?php echo site_url('CustomerGroup/load_all_companies_customers'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyCustomers').removeClass('hidden');
                $('#loadComapnyCustomers').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function clearcustomer(id){
        $('#customerMasterID_'+id).val('').change();
    }

    function load_customer_header() {
        if (groupCustomerAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'groupCustomerAutoID': groupCustomerAutoID},
                url: "<?php echo site_url('CustomerGroup/load_customer_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#customerName').html(data['groupCustomerName']);
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

        function set_currency(val) {
            $('.currency').html(val);
        }
    }

    function load_duplicate_customer(customerAutoID){
        $('#customerDuplicateModal').modal({backdrop: "static"});
        $('#customerAutoIDDuplicatehn').val(customerAutoID);
        $('#btnSavedup').attr('disabled', false);
        load_all_companies_duplicate();
    }

    function load_all_companies_duplicate(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupCustomerAutoID: $('#customerAutoIDDuplicatehn').val()},
            url: "<?php echo site_url('CustomerGroup/load_all_companies_duplicate'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnycustomerDuplicate').removeClass('hidden');
                $('#loadComapnycustomerDuplicate').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }
    function updatepolicy(value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {policyValue: value,groupPolicymasterID:2},
            url: "<?php echo site_url('CustomerGroup/updategroppolicy'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                }
            }, error: function () {

            }
        });
    }
</script>