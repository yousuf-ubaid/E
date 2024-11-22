<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_item_master');
echo head_page($title, false);
$group_main_category_arr = all_group_main_category_drop();
$policydescription = getPolicydescription_masterid(5);
$policyvalue = getgrouppolicyvalues($policydescription['grouppolicymasterID']);
$policyvalue_detail = getPolicydescription_values_detail($policydescription['grouppolicymasterID']);
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-6">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="glyphicon glyphicon-stop" style="color:green; font-size:15px;"></span><?php echo $this->lang->line('common_active') ?><!--Active-->
                </td>
                <td><span class="glyphicon glyphicon-stop" style="color:red; font-size:15px;"></span><?php echo $this->lang->line('config_common_inactive') ?><!--Inactive-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6 text-right">
        <button type="button" class="btn btn-primary pull-right"
                onclick="fetchPage('system/GroupItemMaster/erp_group_item_new',null,' <?php echo $this->lang->line('config_add_new_item') ?>','SUP');"><i
                class="fa fa-plus"></i>
            <?php echo $this->lang->line('transaction_create_item') ?><!--Create Item-->
        </button>
    </div>
</div>
<br>
<div class="row">
    <div class="form-group col-sm-3">
        <label><?php echo $this->lang->line('transaction_main_category') ?><!--Main Category--></label>
        <?php echo form_dropdown('mainCategoryID', $group_main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="LoadMainCategory()"'); ?>
    </div>
    <div class="form-group col-sm-3">
        <label><?php echo $this->lang->line('transaction_sub_category') ?><!--Sub Category--></label>
        <select name="subcategoryID" id="subcategoryID" class="form-control searchbox">
            <option value="">Select Category</option>
        </select>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-4">
        <label for=""><?php echo $policydescription['groupPolicyDescription'] ?></label>
    </div>
    <div class="col-md-1">
        <?php echo form_dropdown('isallow',$policyvalue, $policyvalue_detail['value'], 'class="form-control" id="isallow" onchange="updatepolicy(this.value)" '); ?>
    </div>
</div>
<br>

<div class="table-responsive">
    <table id="item_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 12%"><?php echo $this->lang->line('transaction_main_category') ?><!--Main Category--></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('transaction_sub_category') ?><!--Sub Category--></th>
            <th><?php echo $this->lang->line('common_description') ?><!--Description--></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('config_secondary_code') ?><!--Secondary Code--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('common_price'); ?></th><!--Price-->
            <th style="min-width: 10%"><?php echo $this->lang->line('transaction_current_stock') ?><!--Current Stock--></th>
            <!--<th style="min-width: 10%">WAC Cost</th>-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status') ?><!--Status--></th>
            <th style="min-width: 65px"><?php echo $this->lang->line('common_action') ?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="ItemMasterLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="item_link_form"'); ?>
            <input type="hidden" name="groupItemMasterID" id="groupItemMasterID">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_item_master_link') ?><!--Item Master Link--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="customerName"><h4><?php echo $this->lang->line('common_item_name') ?><!--Item Name--> :- </h4></label>
                        <label style="color: cornflowerblue;font-size: 1.2em;" id="itemName"></label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnyItem">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('config_common_add_link') ?><!--Add Link-->
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="itemDuplicateModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="item_duplicate_form"'); ?>
            <input type="hidden" name="itemAutoIDDuplicatehn" id="itemAutoIDDuplicatehn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_item_replicate') ?><!--Item Replicate--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnyitemDuplicate">

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSavedup"><?php echo $this->lang->line('common_replicate') ?><!--Replicate-->
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
                <h4 class="modal-title"><?php echo $this->lang->line('config_chart_of_accounts_or_category_not_linked') ?><!--Chart of account or category not linked--></h4>
            </div>
            <div class="modal-body">
                <div >
                    <table  class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th ><?php echo $this->lang->line('common_company') ?><!--Company--></th>
                            <th><?php echo $this->lang->line('common_message') ?><!--Message--></th>
                        </tr>
                        </thead>
                        <tbody id="errormsg">

                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>

        </div>
    </div>
</div>
<?php

/*subItemConfigList_modal*/
$this->load->view('system/item/itemmastersub/item-master-list-view-modal');
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/GroupItemMaster/erp_item_master', 'Test', 'Item Master');
        });
        item_table();

        $("#subcategoryID").change(function () {
            Otable.draw();
        });

        $('#item_link_form').bootstrapValidator({
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
                    url: "<?php echo site_url('GroupItemMaster/save_item_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSave').attr('disabled', false);
                        if (data[0] == 's') {
                            /*load_item_master_details_table();
                             load_company($('#groupItemMasterID').val());
                             $('#companyID').val('').change();*/
                            load_all_companies_items();
                            $('#ItemMasterLinkModal').modal('hide');
                        }

                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        HoldOn.close();
                        refreshNotifications(true);
                    }
                });
        });


        $('#item_duplicate_form').bootstrapValidator({
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
                    url: "<?php echo site_url('GroupItemMaster/save_item_duplicate'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSavedup').attr('disabled',false);
                        if (data[0] == 's') {
                            load_all_companies_duplicate();
                            $('#itemDuplicateModal').modal('hide');
                        }
                        if (jQuery.isEmptyObject(data[2])) {

                        } else {
                            $('#errormsg').empty();
                            $.each(data[2], function (key, value) {
                                $('#errormsg').append('<tr><td>' + value['companyname'] + '</td><td>' + value['message'] + '</td></tr>');
                            });
                            $('#invalidinvoicemodal').modal('show');
                            $('#itemDuplicateModal').modal('hide');
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

    function LoadMainCategory() {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        load_sub_cat();
        Otable.draw();
    }


    function item_table() {
        Otable = $('#item_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('GroupItemMaster/fetch_item'); ?>",
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
                $("[name='itemchkbox']").bootstrapSwitch();
            },
            "aoColumns": [
                {"mData": "itemAutoID"},
                {"mData": "mainCategory"},
                {"mData": "SubCategoryDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "secondaryItemCode"},
                {"mData": "price"},
                {"mData": "CurrentStock"},
                //{"mData": "TotalWacAmount"},
                {"mData": "confirmed"},
                {"mData": "edit"},
                {"mData": "itemDescription"},
                {"mData": "itemSystemCode"}
            ],
            "columnDefs": [{"targets": [2], "orderable": false}, {
                "visible": false,
                "searchable": true,
                "targets": [9, 10]
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#mainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});
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

    function change_img(item_id, img) {
        $('#img_uplode_form')[0].reset();
        $('#img_uplode_form').bootstrapValidator('resetForm', true);
        $('#img_item_id').val(item_id);
        $('#item_img').attr('src', img);
        $("#item_img_modal").modal({backdrop: "static"});
    }

    function img_uplode() {
        var data = new FormData($('#img_uplode_form')[0]);
        $.ajax({
            url: "<?php echo site_url('ItemMaster/img_uplode'); ?>",
            type: 'post',
            data: data,
            mimeType: "multipart/form-data",
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#img_uplode_form')[0].reset();
                $('#img_uplode_form').bootstrapValidator('resetForm', true);
                $("#item_img_modal").modal('hide');
                stopLoad();
                refreshNotifications(true);
                item_table();
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_item_master(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'itemAutoID': id},
                    url: "<?php echo site_url('ItemMaster/delete_item'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        item_table();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function changeitemactive(id) {

        var compchecked = 0;
        if ($('#itemchkbox_' + id).is(":checked")) {
            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {itemAutoID: id, chkedvalue: compchecked},
                url: "<?php echo site_url('ItemMaster/changeitemactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        item_table();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }
        else if (!$('#itemchkbox_' + id).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {itemAutoID: id, chkedvalue: 0},
                url: "<?php echo site_url('ItemMaster/changeitemactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        item_table();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

    }

    function load_sub_cat(select_val) {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("GroupItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function link_group_item_master(itemAutoID) {
        $('#ItemMasterLinkModal').modal({backdrop: "static"});
        $('#companyID').val('').change();
        $('#groupItemMasterID').val(itemAutoID);
        $('#btnSave').attr('disabled', false);
        load_all_companies_items();
        load_item_header();
        /*load_company(itemAutoID);
         load_item_master_details_table();*/
    }

    function load_item_master_details_table() {
        Otable = $('#item_group_details').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('GroupItemMaster/fetch_item_Details'); ?>",
            "aaSorting": [[0, 'desc']],
            "searching": false,
            "bLengthChange": false,
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "groupItemDetailID"},
                {"mData": "company_name"},
                {"mData": "itemSystemCode"},
                {"mData": "description"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [4], "orderable": false}, {}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "groupItemMasterID", "value": $('#groupItemMasterID').val()});
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

    function load_company(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupItemMasterID: itemAutoID, All: 'true'},
            url: "<?php echo site_url('GroupItemMaster/load_company'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapny').html(data);
                $('.select2').select2();
                load_comapny_item();
            }, error: function () {

            }
        });
    }

    function load_comapny_item() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: $('#companyID').val(), groupItemMasterID: $('#groupItemMasterID').val(), All: 'true'},
            url: "<?php echo site_url('GroupItemMaster/load_item'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyItem').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function delete_item_link(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this link!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'groupItemDetailID': id},
                    url: "<?php echo site_url('GroupItemMaster/delete_item_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_item_master_details_table();
                            load_company($('#groupItemMasterID').val());
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_all_companies_items() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupItemMasterID: $('#groupItemMasterID').val()},
            url: "<?php echo site_url('GroupItemMaster/load_all_companies_items'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyItem').removeClass('hidden');
                $('#loadComapnyItem').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function clearcustomer(id) {
        $('#ItemAutoID_' + id).val('').change();
    }

    function load_item_header() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': $('#groupItemMasterID').val()},
            url: "<?php echo site_url('GroupItemMaster/load_item_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#itemName').html(data['itemName']);
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_duplicate_item(itemAutoID){
        $('#itemDuplicateModal').modal({backdrop: "static"});
        $('#itemAutoIDDuplicatehn').val(itemAutoID);
        $('#btnSavedup').attr('disabled', false);
        load_all_companies_duplicate();
    }

    function load_all_companies_duplicate(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupItemAutoID: $('#itemAutoIDDuplicatehn').val()},
            url: "<?php echo site_url('GroupItemMaster/load_all_companies_duplicate'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyitemDuplicate').removeClass('hidden');
                $('#loadComapnyitemDuplicate').html(data);
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
            data: {policyValue: value,groupPolicymasterID:5},
            url: "<?php echo site_url('GroupItemMaster/updategroppolicy'); ?>",
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