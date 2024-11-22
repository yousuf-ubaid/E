<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('config_group_warehouse_master');
echo head_page($title, false);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<!-- <div class="row">
    <div class="col-md-9"></div>
    <div class="col-md-3">
        <button type="button" onclick="resetform()"  class="btn btn-xs btn-primary pull-right" data-toggle="modal"
                data-target="#warehousemaster_model"><span
                class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create
            New
        </button>
    </div>
</div> -->
<div class="row">
    <!-- <div class="col-md-5">
        <table class="<?php //echo table_class(); 
                        ?>">
                <tr>
                    <td><span class="label label-success">&nbsp;</span> Confirmed /
                        Approved
                    </td>
                    <td><span class="label label-danger">&nbsp;</span> Not Confirmed
                        / Not Approved
                    </td>
                    <td><span class="label label-warning">&nbsp;</span> Refer-back
                    </td>
                </tr>
            </table>
    </div> -->
    <div class="col-md-9 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" onclick="open_warehouse_model()" class="btn btn-primary pull-right"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new') ?><!-- Create New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="warehousemaster_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 20%"><?php echo $this->lang->line('erp_warehouse_master_warehouse_code') ?></th>
                <th style="min-width: 40%"><?php echo $this->lang->line('common_description') ?></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_Location') ?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_action') ?></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="warehousemaster_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="WarehouseHead"></h3>
            </div>
            <form role="form" id="warehousemaster_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="warehouseredit" name="warehouseredit">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_code') ?> </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="warehousecode" name="warehousecode">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_description') ?></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" rows="2" id="warehousedescription"
                                    name="warehousedescription"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_Location') ?></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="warehouselocation" name="warehouselocation">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_address') ?></label>
                            <div class="col-sm-6">
                                <textarea rows="2" class="form-control" id="warehouseAddress"
                                    name="warehouseAddress"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_telephone') ?> </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="warehouseTel" name="warehouseTel">
                            </div>
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('erp_warehouse_pos_location') ?></label>
                            <div class="col-sm-6" style="">
                                <input type="checkbox" value="1" id="isPosLocation" name="isPosLocation">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close') ?> </button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save') ?> </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="WarehouseLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="warehouse_link_form"'); ?>
            <input type="hidden" name="groupwareHouseAutoID" id="groupwareHouseAutoID">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Ware House Link <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="warehouseName">
                            <h4>Warehouse :- </h4>
                        </label>
                        <label style="color: cornflowerblue;font-size: 1.2em;" id="warehouseName"></label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnywarehouse">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave">Add Link
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="WarehouseDuplicateModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="Warehouse_duplicate_form"'); ?>
            <input type="hidden" name="WarehouseIdDuplicatehn" id="WarehouseIdDuplicatehn">
            <!-- <input type="hidden" name="masterAccountYNhn" id="masterAccountYNhn"> -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_warehouse_replication') ?><!--Chart of account Replication--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadWarehouseDuplicate">

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSavedup"><?php echo $this->lang->line('config_duplicate') ?><!--Duplicate-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.headerclose').click(function() {
            fetchPage('system/GroupWarehouse/Group_warehousemaster_view', '', 'Group Warehouse Master ');
        });
        $('.skin-square input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
        warehousemasterview();
        $('#warehousemaster_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                warehousecode: {
                    validators: {
                        notEmpty: {
                            message: ' Code is required.'
                        }
                    }
                },
                warehousedescription: {
                    validators: {
                        notEmpty: {
                            message: ' Description is required.'
                        }
                    }
                },
                warehouselocation: {
                    validators: {
                        notEmpty: {
                            message: ' Location is required.'
                        }
                    }
                }
                /*             warehouseAddress: {validators: {notEmpty: {message: ' Address is required.'}}},
                 warehouseTel: {validators: {notEmpty: {message: ' Telephone is required.'}}}*/
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Group_warehouseMaster/save_warehousemaster'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    refreshNotifications(true);
                    if (data) {
                        $("#warehousemaster_model").modal("hide");
                        warehousemasterview();
                        //fetchPage('system/srp_mu_suppliermaster_view','Test','Supplier Master');
                    }
                },
                error: function() {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#warehouse_link_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //companyID: {validators: {notEmpty: {message: 'Company is required.'}}}
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Group_warehouseMaster/save_warehouse_link'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    myAlert(data[0], data[1]);
                    $('#btnSave').attr('disabled', false);
                    if (data[0] == 's') {
                        /*load_warehouse_details_table();
                         load_company($('#groupwareHouseAutoID').val());
                         $('#companyID').val('').change();*/
                        load_all_companies_warehouses();
                        $('#WarehouseLinkModal').modal('hide');
                    }

                },
                error: function() {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#Warehouse_duplicate_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {},
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Group_warehouseMaster/save_warehouse_duplicate'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    myAlert(data[0], data[1]);
                    $('#btnSavedup').attr('disabled', false);
                    if (data[0] == 's') {
                        load_all_companies_duplicate();
                        $('#WarehouseDuplicateModal').modal('hide');
                    }

                    if (jQuery.isEmptyObject(data[2])) {

                    } else {
                        $('#errormsg').empty();
                        $.each(data[2], function(key, value) {
                            $('#errormsg').append('<tr><td>' + value['companyname'] + '</td><td>' + value['message'] + '</td></tr>');
                        });
                        // $('#invalidinvoicemodal').modal('show');
                        $('#WarehouseDuplicateModal').modal('hide');
                    }

                },
                error: function() {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

    });


    function warehousemasterview() {
        var Otable = $('#warehousemaster_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Group_warehouseMaster/load_warehousemastertable'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "fnDrawCallback": function(oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i;
                    (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [{
                    "mData": "wareHouseAutoID"
                },
                {
                    "mData": "wareHouseCode"
                },
                {
                    "mData": "wareHouseDescription"
                },
                {
                    "mData": "wareHouseLocation"
                },
                {
                    "mData": "action"
                }
            ],
            "fnServerData": function(sSource, aoData, fnCallback) {
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

    function open_warehouse_model() {
        $('#warehousemaster_form')[0].reset();
        $('#warehousemaster_form').bootstrapValidator('resetForm', true);
        $('#WarehouseHead').html('<?php echo $this->lang->line('erp_warehouse_add_new_warehouse') ?>');
        $("#warehousemaster_model").modal({
            backdrop: "static"
        });
        //$('#warehouseredit').val("");
        //document.getElementById('warehousemaster_form').reset();
    }

    function openwarehousemastermodel(id) {
        //$("#warehousemaster_model").modal("show");
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: id
            },
            url: "<?php echo site_url('Group_warehouseMaster/edit_warehouse'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                open_warehouse_model();
                $('#WarehouseHead').html('Edit Warehouse');
                $('#warehouseredit').val(id);
                $('#warehousecode').val(data['wareHouseCode']);
                $('#warehousedescription').val(data['wareHouseDescription']);
                $('#warehouselocation').val(data['wareHouseLocation']);
                $('#warehouseAddress').val(data['warehouseAddress']);
                $('#warehouseTel').val(data['warehouseTel']);
                if (data['isPosLocation'] == 1) {
                    $('#isPosLocation').prop('checked', true);
                }
            },
            error: function() {
                alert('An Error Occurred! Please Try Again.');

            }
        });
    }

    function link_group_warehouse(wareHouseAutoID) {
        $('#WarehouseLinkModal').modal({
            backdrop: "static"
        });
        $('#companyID').val('').change();
        $('#groupwareHouseAutoID').val(wareHouseAutoID);
        $('#btnSave').attr('disabled', false);
        /*load_company(wareHouseAutoID);
         load_warehouse_details_table();*/
        load_all_companies_warehouses();
        load_warehouse_header();
    }

    function load_company(wareHouseAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                wareHouseAutoID: wareHouseAutoID,
                All: 'true'
            },
            url: "<?php echo site_url('Group_warehouseMaster/load_company'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadComapny').html(data);
                $('.select2').select2();
                load_comapny_warehouse();
            },
            error: function() {

            }
        });
    }

    function load_comapny_warehouse() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                companyID: $('#companyID').val(),
                groupwareHouseAutoID: $('#groupwareHouseAutoID').val(),
                All: 'true'
            },
            url: "<?php echo site_url('Group_warehouseMaster/load_Warehouse'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadComapnywarehouse').html(data);
                $('.select2').select2();
            },
            error: function() {

            }
        });
    }

    function load_warehouse_details_table() {
        Otable = $('#warehouse_group_details').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Group_warehouseMaster/fetch_warehouse_Details'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "searching": false,
            "bLengthChange": false,
            "fnInitComplete": function() {

            },
            "fnDrawCallback": function(oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [{
                    "mData": "groupWarehouseDetailID"
                },
                {
                    "mData": "company_name"
                },
                {
                    "mData": "wareHouseCode"
                },
                {
                    "mData": "wareHouseDescription"
                },
                {
                    "mData": "edit"
                }
            ],
            "columnDefs": [{
                "targets": [4],
                "orderable": false
            }, {}],
            "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "groupWarehouseMasterID",
                    "value": $('#groupwareHouseAutoID').val()
                });
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

    function delete_warehouse_link(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this link!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'groupWarehouseDetailID': id
                    },
                    url: "<?php echo site_url('Group_warehouseMaster/delete_warehouse_link'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_warehouse_details_table();
                            load_company($('#groupwareHouseAutoID').val());
                        }

                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_all_companies_warehouses() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                groupwareHouseAutoID: $('#groupwareHouseAutoID').val()
            },
            url: "<?php echo site_url('Group_warehouseMaster/load_all_companies_warehouses'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadComapnywarehouse').removeClass('hidden');
                $('#loadComapnywarehouse').html(data);
                $('.select2').select2();
            },
            error: function() {

            }
        });
    }

    function clearcustomer(id) {
        $('#warehosueMasterID_' + id).val('').change();
    }

    function load_duplicate_warehouse(id) {
        $('#WarehouseDuplicateModal').modal({
            backdrop: "static"
        });
        $('#WarehouseIdDuplicatehn').val(id);
        // $('#masterAccountYNhn').val(masterAccountYN);
        $('#btnSavedup').attr('disabled', false);
        load_all_companies_duplicate();
    }

    function load_all_companies_duplicate() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                warehouseID: $('#WarehouseIdDuplicatehn').val()
            },
            url: "<?php echo site_url('Group_warehouseMaster/load_all_companies_duplicate'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadWarehouseDuplicate').removeClass('hidden');
                $('#loadWarehouseDuplicate').html(data);
                $('.select2').select2();
            },
            error: function() {

            }
        });
    }

    function load_warehouse_header() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'groupwareHouseAutoID': $('#groupwareHouseAutoID').val()
            },
            url: "<?php echo site_url('Group_warehouseMaster/load_warehouse_header'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#warehouseName').html(data['wareHouseLocation']);
                }
                stopLoad();
                refreshNotifications(true);
            },
            error: function() {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }
</script>