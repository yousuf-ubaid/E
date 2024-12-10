<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_warehouse');
echo head_page($title, false);
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>


<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-12 text-right">
        <div class="pull-right">
            <button type="button" data-text="Add" id="btnAdd" onclick="fetchPage('system/mfq/manage_warehouse','','Warehouse')" class="btn btn-sm btn-default">
                <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('manufacturing_add_warehouse');?><!--Add Warehouse-->
            </button>
            <button type="button" data-text="Sync" id="btnSync_fromErp" class="btn button-royal "
                    onclick=""><i class="fa fa-level-down" aria-hidden="true"></i> <?php echo $this->lang->line('manufacturing_warehouse_from_erp');?>
            </button>
        </div>
    </div>
</div>


<div id="itemmaster" style="margin-top:20px;">
    <div class="table-responsive">
        <table id="warehouse_table" class="table table-striped table-condensed">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('erp_warehouse_master_warehouse_code');?> </th><!--Warehouse Code-->
                <th style="min-width: 40%"><?php echo $this->lang->line('common_description');?></th><!--Description-->
                <th style="min-width: 20%"><?php echo $this->lang->line('common_Location');?> </th><!--Location-->
                <th style="min-width: 20%"><?php echo $this->lang->line('manufacturing_link_description');?> </th><!--Location-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_action');?></th><!--Action-->
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Crew From ERP"
     id="itemMasterFromERP">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('manufacturing_warehouse_from_erp')?><!--Warehouse from ERP--> </h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="table-responsive">
                        <table id="warehouse_table_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 20%"><?php echo $this->lang->line('erp_warehouse_master_warehouse_code');?> </th><!--Warehouse Code-->
                                <th style="min-width: 40%"><?php echo $this->lang->line('common_description');?></th><!--Description-->
                                <th style="min-width: 20%"><?php echo $this->lang->line('common_Location');?> </th><!--Location-->
                                <th style="min-width: 5%; text-align: center !important;">
                                    <button type="button" data-text="Add" onclick="addWarehouse()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('manufacturing_add_warehouse')?><!--Add Warehouse-->
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close')?><!--Close--></button>
            </div>

        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Crew From ERP"
     id="linkFromERP">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Link from ERP </h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <input type="hidden" id="mfqWarehouseAutoID" name="mfqWarehouseAutoID">
                    <div class="table-responsive">
                        <table id="warehouse_table_link" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 20%"><?php echo $this->lang->line('erp_warehouse_master_warehouse_code');?> </th><!--Warehouse Code-->
                                <th style="min-width: 40%"><?php echo $this->lang->line('common_description');?></th><!--Description-->
                                <th style="min-width: 20%"><?php echo $this->lang->line('common_Location');?> </th><!--Location-->

                                <th style="min-width: 5%; text-align: center !important;">
                                    <button type="button" data-text="Add" onclick="link_warehouse()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('manufacturing_add_warehouse')?><!--Add Warehouse-->
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close')?><!--Close--></button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    var oTable;
    var oTable2;
    var selectedItemsSync = [];
    $(document).ready(function () {
        $.fn.extend({
            toggleText: function (a, b, c, d) {
                if (this.data("text") == c) {
                    this.data("text", d);
                    this.html(b);
                }
                else {
                    this.data("text", c);
                    this.html(a);
                }
            }
        });

        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_warehouse','','Warehouse')
        });

        warehouse_table();
        sync_warehouse_table();
        link_warehouse_table();

        $("#btnSync_fromErp").click(function () {
            sync_warehouse_table();
            $("#itemMasterFromERP").modal('show');
        });
    });

    function warehouse_table() {
        oTable = $('#warehouse_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "order": [[ 4, "desc" ]],
            "sAjaxSource": "<?php echo site_url('MFQ_warehouse/fetch_warehouse'); ?>",
            language: {paginate: {previous: '‹‹', next: '››'}},
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                /*$("[name='itemchkbox']").bootstrapSwitch();*/
                $("[rel='tooltip']").tooltip();
            },

            "aoColumns": [
                {"mData": "mfq_mfqWarehouseAutoID"},
                {"mData": "mfq_warehouseCode"},
                {"mData": "mfq_warehouseDescription"},
                {"mData": "mfq_warehouseLocation"},
                {"mData": "erpwareHouseDescription"},
                {"mData": "action"}
            ],
            "columnDefs": [{"targets": [0,5], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function sync_warehouse_table() {
        oTable2 = $('#warehouse_table_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_warehouse/fetch_sync_warehouse'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {paginate: {previous: '‹‹', next: '››'}},

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {

                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');

                        // $("#selectItem_" + value).prop("checked", true);
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });

            },
            "aoColumns": [
                {"mData": "warehouseAutoID"},
                {"mData": "warehouseCode"},
                {"mData": "warehouseDescription"},
                {"mData": "warehouseLocation"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function link_warehouse_table() {
        oTable2 = $('#warehouse_table_link').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_warehouse/fetch_link_warehouse'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
                $('.item-iCheck').iCheck('uncheck');
                if (selectedItemsSync.length > 0) {
                    $.each(selectedItemsSync, function (index, value) {
                        $("#selectItem_" + value).iCheck('check');
                    });
                }
                $('.extraColumns input').iCheck({
                    checkboxClass: 'icheckbox_square_relative-purple',
                    radioClass: 'iradio_square_relative-purple',
                    increaseArea: '20%'
                });
                $('input').on('ifChecked', function (event) {
                    ItemsSelectedSync(this);
                });
                $('input').on('ifUnchecked', function (event) {
                    ItemsSelectedSync(this);
                });
            },
            "aoColumns": [
                {"mData": "warehouseAutoID"},
                {"mData": "warehouseCode"},
                {"mData": "warehouseDescription"},
                {"mData": "warehouseLocation"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function link_warehouse_master(id) {
        $('#mfqWarehouseAutoID').val(id);
        $("#linkFromERP").modal('show');
    }

    function link_warehouse() {
        var selectedVal = $("input:radio.radioChk:checked");
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_warehouse/link_warehouse"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedVal.val(),mfqWarehouseAutoID:$("#mfqWarehouseAutoID").val()},
            async: false,
            success: function (data) {
                if (data['error'] == 0) {
                    warehouse_table();
                    oTable.draw();
                    $("#linkFromERP").modal('hide');
                    myAlert('s', data['message']);
                    selectedItemsSync = [];
                }else{
                    myAlert('e', data['message']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function ItemsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function addWarehouse() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_warehouse/add_warehouse"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync},
            async: false,
            success: function (data) {
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    oTable.draw();
                    sync_warehouse_table();
                    selectedItemsSync = [];
                } else {
                    myAlert('e', data['message']);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


</script>