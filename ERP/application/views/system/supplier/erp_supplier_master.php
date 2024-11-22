<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
//$this->lang->load('config', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('accounts_payable_sm_supplier_master');
echo head_page($title, true);

/*echo head_page('Supplier Master', true);*/
$supplier_arr = all_supplier_drop(false);
$customerCategory    = party_category(2, false);
$currncy_arr    = all_currency_new_drop(false);
$usergroupcompanywiseallow = getPolicyValuesgroup('SM','All');
?>

<div id="filter-panel" class="collapse filter-panel">
    <div class="row">
        <?php echo form_open('', 'role="form" id="suppliermaster_filter_form"'); ?>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_supplier_name');?><!--Supplier Name--></label><br>
            <?php echo form_dropdown('supplierCode[]', $supplier_arr, '', 'class="form-control" id="supplierCode" onchange="Otable.draw(), ODeltable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_category');?><!--Category--></label><br>
            <?php echo form_dropdown('category[]', $customerCategory, '', 'class="form-control" id="category" onchange="Otable.draw(), ODeltable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_currency');?><!--Currency--></label><br>
            <?php echo form_dropdown('currency[]', $currncy_arr, '', 'class="form-control" id="currency" onchange="Otable.draw(), ODeltable.draw()" multiple="multiple"'); ?>
        </div>
        <div class="form-group col-sm-3">
            <label for="supplierPrimaryCode">&nbsp;</label><br>

            <button type="button" class="btn btn-sm btn-primary pull-right"
                    onclick="clear_all_filters()" style=""><i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('common_clear');?><!--Clear-->
            </button>
        </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td>
                    <span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_active');?><!--Active-->
                </td>
                <td>
                    <span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('accounts_payable_sm_inactive');?><!--Inactive-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-7 text-right">
        <?php if($usergroupcompanywiseallow == 0){?>
            <button type="button" class="btn btn-primary-new size-sm "
                        onclick="createcustomer()"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_payable_sm_create_supplier');?><!--Create Supplier-->
            </button>
        <?php } else { ?>
            <button type="button" class="btn btn-primary-new size-sm "
                        onclick="fetchPage('system/supplier/erp_supplier_master_new',null,'<?php echo $this->lang->line('accounts_payable_trans_add_new_supplier')?>','SUP');"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_payable_sm_create_supplier');?><!--Create Supplier-->
            </button>
        <?php }?>
        <a href="#" type="button" class="btn btn-excel btn-success-new size-sm" onclick="excel_export()">
            <i class="fa fa-file-excel-o"></i> Excel <!--Excel-->
        </a>
    </div>
</div>
<hr>
<div class="row supply_master_style" style="padding-left: 2%">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="btn-default-new size-sm tab-style-one mr-1 active"><a href="#supplier" data-toggle="tab" onclick="Otable.draw()"><?php echo $this->lang->line('common_supplier')?><!--Supplier--></a></li>
        <li class="btn-default-new size-sm tab-style-one"><a href="#deleted" data-toggle="tab" onclick="ODeltable.draw()"><?php echo $this->lang->line('accounts_payable_deleted_supplier')?><!--Deleted Suppliers--></a></li>
    </ul>
</div>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="supplier">
        <div class="table-responsive">
            <table id="supplier_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_sm_supplier_code');?><!--Supplier Code--></th>
                    <th style="min-width: 40%"><?php echo $this->lang->line('accounts_payable_sm_supplier_details');?><!--Supplier Details--></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_category');?><!--Category--></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_balance');?><!--Balance--></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="deleted">
        <div class="table-responsive">
            <table id="supplier_deleted_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_sm_supplier_code');?><!--Supplier Code--></th>
                    <th style="min-width: 40%"><?php echo $this->lang->line('accounts_payable_sm_supplier_details');?><!--Supplier Details--></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_category');?><!--Category--></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('accounts_payable_balance');?><!--Balance--></th>
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>

                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var Otable;
    var ODeltable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/supplier/erp_supplier_master', '', 'Supplier Master');
        });
        supplier_table();
        supplier_deleted_table();
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
    });

    function supplier_table() {
         Otable = $('#supplier_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Supplier/fetch_supplier'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
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
                {"mData": "supplierAutoID"},
                {"mData": "supplierSystemCode"},
                {"mData": "supplier_detail"},
                {"mData": "categoryDescription"},
                {"mData": "amt"},
                {"mData": "supplierApprovalStatus"},
                {"mData": "edit"},
                {"mData": "supplierName"},
                {"mData": "supplierAddress1"},
                {"mData": "supplierAddress2"},
                {"mData": "supplierCountry"},
                {"mData": "secondaryCode"},
                {"mData": "supplierCurrency"},
                {"mData": "supplierEmail"},
                {"mData": "supplierTelephone"},
                {"mData": "Amount_search"},
                {"mData": "supplierCurrency"}
            ],
            "columnDefs": [{"targets": [6], "orderable": false},{"targets": [0,2,4,5,6], "searchable": false},{"visible":false,"searchable": true,"targets": [7,8,9,10,11,12,13,14,15,16] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                aoData.push({"name": "supplierCode", "value": $("#supplierCode").val()});
                aoData.push({"name": "category", "value": $("#category").val()});
                aoData.push({"name": "currency", "value": $("#currency").val()});
                aoData.push({"name": "deleted", "value": 0});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function supplier_deleted_table() {
        ODeltable = $('#supplier_deleted_table').DataTable({
             "language": {
                 "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
             },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('supplier/fetch_supplier'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
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
                {"mData": "supplierAutoID"},
                {"mData": "supplierSystemCode"},
                {"mData": "supplier_detail"},
                {"mData": "categoryDescription"},
                {"mData": "amt"},
                {"mData": "confirmed"},
                {"mData": "edit"},
                {"mData": "supplierName"},
                {"mData": "supplierAddress1"},
                {"mData": "supplierAddress2"},
                {"mData": "supplierCountry"},
                {"mData": "secondaryCode"},
                {"mData": "supplierCurrency"},
                {"mData": "supplierEmail"},
                {"mData": "supplierTelephone"},
                {"mData": "Amount"}
            ],
            "columnDefs": [{"targets": [5], "orderable": false},{"visible":false,"searchable": true,"targets": [6,7,8,9,10,11,12,13,14,15] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                aoData.push({"name": "supplierCode", "value": $("#supplierCode").val()});
                aoData.push({"name": "category", "value": $("#category").val()});
                aoData.push({"name": "currency", "value": $("#currency").val()});
                aoData.push({"name": "deleted", "value": 1});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function supplierbank() {
        Otable = $('#supplierbank_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('supplier/fetch_supplierbank'); ?>",
            "aaSorting": [[1, 'desc']],
            "fnInitComplete": function () {

            },
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
                {"mData": "supplierBankMasterID"},
                {"mData": "bankName"},
                {"mData": "bankAddress"},
                {"mData": "accountName"},
                {"mData": "accountNumber"},
                {"mData": "CurrencyCode"},
                {"mData": "swiftCode"},
                {"mData": "IbanCode"},




                {"mData": "edit"}
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {

                aoData.push({"name": "supplierAutoID", "value": supplierAutoID});

                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }


    function delete_supplierbank(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
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
                    data: {'supplierBankMasterID': id},
                    url: "<?php echo site_url('supplier/delete_supplierbank'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        supplierbank();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function delete_supplier(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
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
                    data: {'supplierAutoID': id},
                    url: "<?php echo site_url('supplier/delete_supplier'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        Otable.draw();
                        ODeltable.draw();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function clear_all_filters() {
        $('#supplierCode').multiselect2('deselectAll', false);
        $('#supplierCode').multiselect2('updateButtonText');
        $('#category').multiselect2('deselectAll', false);
        $('#category').multiselect2('updateButtonText');
        $('#currency').multiselect2('deselectAll', false);
        $('#currency').multiselect2('updateButtonText');
        Otable.draw();
        ODeltable.draw();
    }
    function createcustomer() {
        swal(" ", "You do not have permission to create  supplier master at company level,please contact your system administrator.", "error");
    }

    /* Function added */
    function excel_export() {
        var form = document.getElementById('suppliermaster_filter_form');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#suppliermaster_filter_form').serializeArray();
        form.action = '<?php echo site_url('Supplier/export_excel_supplier_master'); ?>';
        form.submit();
    }

    function referback_supplier(id) {

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'supplierAutoID': id},
                    url: "<?php echo site_url('Supplier/referback_supplier'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        Otable.draw();
                        ODeltable.draw();

                    }, error: function () {
                        //swal("Cancelled", "Your file is safe :)", "error");
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    }
                });
            });
    }
</script>