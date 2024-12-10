<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$title = $this->lang->line('config_supplier_master');
echo head_page($title, true);

/*echo head_page('Supplier Master', true);*/
$supplier_arr = all_supplier_group_drop(false);
$customerCategory    = party_category(2, false);
$currncy_arr    = all_currency_new_drop(false);

$policydescription = getPolicydescription_masterid(3);
$policyvalue = getgrouppolicyvalues($policydescription['grouppolicymasterID'] ?? '');
$policyvalue_detail = getPolicydescription_values_detail($policydescription['grouppolicymasterID'] ?? '');


?>
<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_supplier_name');?><!--Supplier Name--></label><br>
            <?php echo form_dropdown('supplierCode[]', $supplier_arr, '', 'class="form-control" id="supplierCode" onchange="supplier_table()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_category');?><!--Category--></label><br>
            <?php echo form_dropdown('category[]', $customerCategory, '', 'class="form-control" id="category" onchange="supplier_table()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_currency');?><!--Currency--></label><br>
            <?php echo form_dropdown('currency[]', $currncy_arr, '', 'class="form-control" id="currency" onchange="supplier_table()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp;</label><br>

            <button type="button" class="btn btn-sm btn-primary pull-right"
                    onclick="clear_all_filters()" style=""><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
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
    <div class="col-md-7 text-right"><!--Add New Supplier-->
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/GroupMaster/erp_supplier_group_master_new',null,'<?php echo $this->lang->line('config_add_new_supplier');?>','SUP');"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('config_create_supplier');?><!--Create Supplier-->
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-4">
        <label for=""><?php echo $policydescription['groupPolicyDescription'] ?? '' ?></label>
    </div>
    <div class="col-md-1">
        <?php echo form_dropdown('isallow',$policyvalue, $policyvalue_detail['value'] ?? '', 'class="form-control" id="isallow" onchange="updatepolicy(this.value)" '); ?>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="supplier_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('config_common_supplier_code');?><!--Supplier Code--></th>
            <th style="min-width: 40%"><?php echo $this->lang->line('config_common_supplier_details');?><!--Supplier Details--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_category');?><!--Category--></th>
           <!-- <th style="min-width: 15%">Balance</th>
            <th style="min-width: 5%">Status</th>-->
            <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="supplierLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="supplierlink_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_supplier_link');?><!--Supplier Link--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="customerName"><h4><?php echo $this->lang->line('config_supplier_name');?><!--Supplier Name--> :- </h4></label>
                        <label style="color: cornflowerblue;font-size: 1.2em;" id="supplierName"></label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">

                        <div class="row">
                            <div class="form-group col-sm-12" id="loadComapnySuppliers">
                            </div>
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

<div class="modal fade" id="supplierDuplicateModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="supplier_duplicate_form"'); ?>
            <input type="hidden" name="supplierAutoIDDuplicatehn" id="supplierAutoIDDuplicatehn">
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
    var groupSupplierMasterID;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/GroupMaster/erp_supplier_group_master', '', 'Supplier Master');
        });
        supplier_table();
        $('#supplierCode').multiselect2({
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
        //$("#supplierCode").multiselect2('selectAll', true);
        $('#supplierlink_form').bootstrapValidator({
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
            data.push({'name': 'groupSupplierMasterID', 'value': groupSupplierMasterID});
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('SupplierGroup/save_supplier_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSave').attr('disabled',false);
                        if (data[0] == 's') {
                            //$('#supplierLinkModal').modal('hide');
                            /*load_supplier_link_table();
                            load_company(groupSupplierMasterID);
                            $('#companyID').val('').change();*/
                            $('#supplierLinkModal').modal('hide');
                            load_all_companies_suppliers();
                        }

                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });

        $('#supplier_duplicate_form').bootstrapValidator({
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
                    url: "<?php echo site_url('SupplierGroup/save_supplier_duplicate'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSavedup').attr('disabled',false);
                        if (data[0] == 's') {
                            load_all_companies_duplicate();
                            $('#supplierDuplicateModal').modal('hide');
                        }
                        if (jQuery.isEmptyObject(data[2])) {

                        } else {
                            $('#errormsg').empty();
                            $.each(data[2], function (key, value) {
                                $('#errormsg').append('<tr><td>' + value['companyname'] + '</td><td>' + value['message'] + '</td></tr>');
                            });
                            $('#invalidinvoicemodal').modal('show');
                            $('#supplierDuplicateModal').modal('hide');
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

    function supplier_table() {
        var Otable = $('#supplier_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('SupplierGroup/fetch_supplier'); ?>",
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
                {"mData": "groupSupplierAutoID"},
                {"mData": "groupSupplierSystemCode"},
                {"mData": "supplier_detail"},
                {"mData": "categoryDescription"},
                {"mData": "edit"},
                {"mData": "groupSupplierName"},
                {"mData": "supplierAddress1"},
                {"mData": "supplierAddress2"},
                {"mData": "supplierCountry"},
                {"mData": "secondaryCode"},
                {"mData": "supplierCurrency"},
                {"mData": "supplierEmail"},
                {"mData": "supplierTelephone"}
            ],
            "columnDefs": [{"targets": [4], "orderable": false},{"visible":false,"searchable": true,"targets": [5,6,7,8,9,10,11,12] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "supplierCode", "value": $("#supplierCode").val()});
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


    function clear_all_filters() {
        $('#supplierCode').multiselect2('deselectAll', false);
        $('#supplierCode').multiselect2('updateButtonText');
        $('#category').multiselect2('deselectAll', false);
        $('#category').multiselect2('updateButtonText');
        $('#currency').multiselect2('deselectAll', false);
        $('#currency').multiselect2('updateButtonText');
        supplier_table();
    }

    function openLinkModal(id) {
        $('#supplierLinkModal').modal({backdrop: "static"});
        $('#companyID').val('').change();
        $('#btnSave').attr('disabled', false);
        groupSupplierMasterID = id;
        /*load_company(id);
        load_supplier_link_table()*/;
        load_all_companies_suppliers();
        load_supplier_heading();
        //detailotable.draw();
        //$('#customerlink_form').bootstrapValidator('revalidateField', 'companyID');
    }

    function load_company(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupSupplierMasterID: id, All: 'true'},
            url: "<?php echo site_url('SupplierGroup/load_company'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapny').html(data);
                $('.select2').select2();
                $('#loadComapny').removeClass('hidden');
                load_comapny_suppliers();
            }, error: function () {

            }
        });
    }

    function load_comapny_suppliers() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: $('#companyID').val(), All: 'true'},
            url: "<?php echo site_url('SupplierGroup/load_comapny_suppliers'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnySuppliers').html(data);
                $('.select2').select2();
                $('#loadComapnySuppliers').removeClass('hidden');
            }, error: function () {

            }
        });
    }

    function load_supplier_link_table() {
        var Otable = $('#supplier_link_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('SupplierGroup/fetch_supplier_link'); ?>",
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
                {"mData": "groupSupplierDetailID"},
                {"mData": "supplierSystemCode"},
                {"mData": "company_name"},
                {"mData": "supplier_detail"},
                {"mData": "GLDescription"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "groupSupplierMasterID", "value": groupSupplierMasterID});
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

    function delete_supplier_link(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('config_common_you_want_to_delete_this_supplier');?>",/*You want to delete this supplier!*/
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
                    data: {'groupSupplierDetailID': id},
                    url: "<?php echo site_url('SupplierGroup/delete_supplier_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_supplier_link_table();
                        }
                        stopLoad();
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });
    }

    function load_all_companies_suppliers(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupSupplierMasterID: groupSupplierMasterID},
            url: "<?php echo site_url('SupplierGroup/load_all_companies_supliers'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnySuppliers').removeClass('hidden');
                $('#loadComapnySuppliers').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function clearcustomer(id){
        $('#SupplierMasterID_'+id).val('').change();
    }

    function load_supplier_heading() {
        if (groupSupplierMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'groupSupplierMasterID': groupSupplierMasterID},
                url: "<?php echo site_url('SupplierGroup/load_supplier_heading'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#supplierName').html(data['groupSupplierName']);
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

    function load_duplicate_supplier(supplierAutoID){
        $('#supplierDuplicateModal').modal({backdrop: "static"});
        $('#supplierAutoIDDuplicatehn').val(supplierAutoID);
        $('#btnSavedup').attr('disabled', false);
        load_all_companies_duplicate();
    }

    function load_all_companies_duplicate(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupSupplierAutoID: $('#supplierAutoIDDuplicatehn').val()},
            url: "<?php echo site_url('SupplierGroup/load_all_companies_duplicate'); ?>",
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
            data: {policyValue: value,groupPolicymasterID:3},
            url: "<?php echo site_url('SupplierGroup/updategroppolicy'); ?>",
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