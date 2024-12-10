<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('erp_uom_heading');
echo head_page($title, false); ?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-9 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" onclick="open_uom_model()" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>
            <?php echo $this->lang->line('common_create_new') ?><!-- Create New-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="umo_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%"><?php echo $this->lang->line('erp_uom_code') ?></th>
                <th style="min-width: 50%"><?php echo $this->lang->line('erp_uom_description') ?></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('erp_uom_created_by') ?></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_action') ?></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="uom_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" id="UOMHead"></h3>
            </div>
            <form role="form" id="uom_form" class="form-horizontal">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="UnitID" name="UnitID">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_code') ?></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="UnitShortCode" name="UnitShortCode">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"> <?php echo $this->lang->line('common_description') ?></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="UnitDes" name="UnitDes">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close') ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_description') ?><?php echo $this->lang->line('common_Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="uom_detail_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Edit UOM Conversion</h3>
            </div>
            <div class="modal-body">
                <form class="form-inline pull-right" id="add_conversion_form">
                    <div class="form-group">
                        <label for="exampleInputName2">UOM</label>
                        <?php echo form_dropdown('subUnitID', array('' => 'Select UOM'), '', 'class="form-control select2" id="subUnitID" required'); ?>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail2">Rate</label>
                        <input type="text" class="form-control number" id="conversion" name="conversion">
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
                <br><br>
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Master UOM</td>
                            <td>Sub UOM</td>
                            <td>Conversion</td>
                        </tr>
                    </thead>
                    <tbody id="table_body">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uomLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="uom_link_form"'); ?>
            <input type="hidden" name="groupUOMMasterID" id="groupUOMMasterID">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">UOM Link <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="uomName">
                            <h4>UOM :- </h4>
                        </label>
                        <label style="color: cornflowerblue;font-size: 1.2em;" id="uomName"></label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnyuom">

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
<div class="modal fade" id="uomDuplicateModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="uom_duplicate_form"'); ?>
            <input type="hidden" name="uomIdDuplicatehn" id="uomIdDuplicatehn">
            <!-- <input type="hidden" name="masterAccountYNhn" id="masterAccountYNhn"> -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_uom_replication') ?><!--Chart of account Replication--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loaduomDuplicate">

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
    var masterUnitID;
    var desc;
    var code;
    $(document).ready(function() {
        $('.headerclose').click(function() {
            fetchPage('system/GroupUOM/srp_group_unit_of_measurement_view', 'Test', 'Unit Of Measurement');
        });
        masterUnitID = null;
        desc = null;
        code = null;
        fetch_umo_data();
        number_validation();
        $('#add_conversion_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                subUnitID: {
                    validators: {
                        notEmpty: {
                            message: ' UOM is required.'
                        }
                    }
                },
                conversion: {
                    validators: {
                        notEmpty: {
                            message: ' Conversion is required.'
                        }
                    }
                }
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({
                'name': 'masterUnitID',
                'value': masterUnitID
            });
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('GroupUom/save_uom_conversion'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    refreshNotifications(true);
                    if (data) {
                        fetch_umo_detail_con(masterUnitID, desc, code);
                    }
                },
                error: function() {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#uom_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                UnitShortCode: {
                    validators: {
                        notEmpty: {
                            message: ' Code is required.'
                        }
                    }
                },
                UnitDes: {
                    validators: {
                        notEmpty: {
                            message: ' Description is required.'
                        }
                    }
                }
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
                url: "<?php echo site_url('GroupUom/save_uom'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    refreshNotifications(true);
                    if (data) {
                        $("#uom_model").modal("hide");
                        fetch_umo_data();
                    }
                },
                error: function() {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#uom_link_form').bootstrapValidator({
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
                url: "<?php echo site_url('GroupUom/save_uom_link'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    myAlert(data[0], data[1]);
                    $('#btnSave').attr('disabled', false);
                    if (data[0] == 's') {
                        /*load_uom_details_table();
                         load_company($('#groupUOMMasterID').val());
                         $('#companyID').val('').change();*/
                        load_all_companies_uom();
                        $('#uomLinkModal').modal('hide');
                    }

                },
                error: function() {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#uom_duplicate_form').bootstrapValidator({
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
                url: "<?php echo site_url('GroupUom/save_uom_duplicate'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    myAlert(data[0], data[1]);
                    $('#btnSavedup').attr('disabled', false);
                    if (data[0] == 's') {
                        load_all_companies_duplicate();
                        $('#uomDuplicateModal').modal('hide');
                    }

                    if (jQuery.isEmptyObject(data[2])) {

                    } else {
                        $('#errormsg').empty();
                        $.each(data[2], function(key, value) {
                            $('#errormsg').append('<tr><td>' + value['companyname'] + '</td><td>' + value['message'] + '</td></tr>');
                        });
                        // $('#invalidinvoicemodal').modal('show');
                        $('#uomDuplicateModal').modal('hide');
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

    function fetch_umo_data() {
        var Otable = $('#umo_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('GroupUom/fetch_umo_data'); ?>",
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
                    "mData": "UnitID"
                },
                {
                    "mData": "UnitShortCode"
                },
                {
                    "mData": "UnitDes"
                },
                {
                    "mData": "modifiedUserName"
                },
                {
                    "mData": "edit"
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

    function fetch_umo_detail_con(id, desc, code) {
        masterUnitID = id;
        desc = desc;
        code = code;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'masterUnitID': masterUnitID
            },
            url: "<?php echo site_url('GroupUom/fetch_convertion_detail_table'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                $('#table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#table_body').append('<tr class="danger"><td colspan="4" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    $.each(data['detail'], function(key, value) {
                        status = '';
                        if (masterUnitID == value['subUnitID']) {
                            status = 'readonly';
                        }
                        $('#table_body').append('<tr><td>' + x + '</td><td>' + value['m_dese'] + ' - ' + value['m_code'] + '</td><td> = ' + value['sub_dese'] + ' - ' + value['sub_code'] + '</td><td class="pull-right"><input type="text" class="form-control number" id="conversion" name="conversion" onchange="change_conversion(' + masterUnitID + ',' + value['subUnitID'] + ',this.value)" value="' + value['conversion'] + '" ' + status + '></td></tr>');
                        x++;
                    });

                    $('#subUnitID').empty();
                    var mySelect = $('#subUnitID');
                    mySelect.append($('<option></option>').val('').html('Select  UOM'));
                    if (!jQuery.isEmptyObject(data['drop'])) {
                        $.each(data['drop'], function(val, text) {
                            mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                        });
                    }
                }
                $('#add_conversion_form')[0].reset();
                $('#add_conversion_form').bootstrapValidator('resetForm', true);
                $("#uom_detail_model").modal({
                    backdrop: "static"
                });
                stopLoad();
            },
            error: function() {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function open_uom_model() {
        $('#uom_form')[0].reset();
        $('#UOMHead').html('<?php echo $this->lang->line('erp_uom_add_new_uom') ?>'); /*Add New UOM*/
        $('#uom_form').bootstrapValidator('resetForm', true);
        $("#uom_model").modal({
            backdrop: "static"
        });
    }

    function change_conversion(masterUnitID, subUnitID, conversion) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'masterUnitID': masterUnitID,
                'subUnitID': subUnitID,
                'conversion': conversion
            },
            url: "<?php echo site_url('GroupUom/change_conversion'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                HoldOn.close();
                refreshNotifications(true);
                // if(data){
                //     fetch_umo_detail_con(masterUnitID,desc,code);
                // }
            },
            error: function() {
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    }

    function link_uom(groupUOMMasterID) {
        $('#uomLinkModal').modal({
            backdrop: "static"
        });
        $('#companyID').val('').change();
        $('#groupUOMMasterID').val(groupUOMMasterID);
        $('#btnSave').attr('disabled', false);
        load_all_companies_uom();
        load_uom_header();
        /* load_company(groupUOMMasterID);
         load_uom_details_table();*/
    }

    function load_company(groupUOMMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                groupUOMMasterID: groupUOMMasterID,
                All: 'true'
            },
            url: "<?php echo site_url('GroupUom/load_company'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadComapny').html(data);
                $('.select2').select2();
                load_comapny_uom();
            },
            error: function() {

            }
        });
    }

    function load_comapny_uom() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                companyID: $('#companyID').val(),
                groupUOMMasterID: $('#groupUOMMasterID').val(),
                All: 'true'
            },
            url: "<?php echo site_url('GroupUom/load_comapny_uom'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadComapnyuom').html(data);
                $('.select2').select2();
            },
            error: function() {

            }
        });
    }

    function load_uom_details_table() {
        Otable = $('#uom_group_details').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('GroupUom/fetch_uom_Details'); ?>",
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
                    "mData": "groupUOMDetailID"
                },
                {
                    "mData": "company_name"
                },
                {
                    "mData": "UnitShortCode"
                },
                {
                    "mData": "UnitDes"
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
                    "name": "groupUOMMasterID",
                    "value": $('#groupUOMMasterID').val()
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

    function delete_uom_link(id) {
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
                        'groupUOMDetailID': id
                    },
                    url: "<?php echo site_url('GroupUom/delete_uom_link'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_uom_details_table();
                            load_company($('#groupUOMMasterID').val());
                        }

                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_all_companies_uom() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                groupUOMMasterID: $('#groupUOMMasterID').val()
            },
            url: "<?php echo site_url('GroupUom/load_all_companies_uom'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadComapnyuom').removeClass('hidden');
                $('#loadComapnyuom').html(data);
                $('.select2').select2();
            },
            error: function() {

            }
        });
    }

    function clearcustomer(id) {
        $('#UOMMasterID_' + id).val('').change();
    }

    function load_duplicate_group_uom(id) {
        $('#uomDuplicateModal').modal({
            backdrop: "static"
        });
        $('#uomIdDuplicatehn').val(id);
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
                groupUomID: $('#uomIdDuplicatehn').val()
            },
            url: "<?php echo site_url('GroupUom/load_all_companies_duplicate'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loaduomDuplicate').removeClass('hidden');
                $('#loaduomDuplicate').html(data);
                $('.select2').select2();
            },
            error: function() {

            }
        });
    }

    function load_uom_header() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'groupUOMMasterID': $('#groupUOMMasterID').val()
            },
            url: "<?php echo site_url('GroupUom/load_uom_header'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#uomName').html(data['UnitDes']);
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