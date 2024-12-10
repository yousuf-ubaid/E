<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('manufacturing_segments');
echo head_page($title, false);
$main_category_arr = all_main_category_drop();
$key = array_filter($main_category_arr, function ($a) {
    return $a == 'FA | Fixed Assets';
});
unset($main_category_arr[key($key)])
?>

<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">

<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    #segment_table th{
        text-transform: uppercase !important;
    }
    td.details-control {
        background: url('http://www.pskreporter.de/public/images/details_open.png') no-repeat center center;
        cursor: pointer;
    }

    tr.shown td.details-control {
        background: url('http://www.pskreporter.de/public/images/details_close.png') no-repeat center center;
    }

    .hiddenRow {
        padding: 0 !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="col-md-12 text-right">
        <div class="pull-right">
            <button type="button" data-text="Add" id="btnAdd" onclick="fetchPage('system/mfq/master/manage-segment','','Segment')" class="btn btn-sm btn-default">
                <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('manufacturing_add_segment') ?><!--Add Segment-->
            </button>
            <button type="button" data-text="Sync" id="btnSync_fromErp" class="btn button-royal "
                    onclick=""><i class="fa fa-level-down" aria-hidden="true"></i> <?php echo $this->lang->line('manufacturing_pull_item_from_erp');?>
            </button>
        </div>
    </div>
</div>


<div id="itemmaster" style="margin-top:20px;">
    <div class="table-responsive">
        <table id="segment_table" class="table table-striped table-condensed">
            <thead>
            <tr>
                <th style="min-width: 5%">&nbsp;</th>
                <th style="min-width: 5%">&nbsp;</th>
                <th ><?php echo $this->lang->line('manufacturing_segment_ID');?><!--SEGMENT ID--></th>
                <th><?php echo $this->lang->line('manufacturing_segment_description');?><!--SEGMENT DESCRIPTION--></th>
                <th><?php echo $this->lang->line('manufacturing_segment_linked_to_erp');?><!--SEGMENT LINKED TO ERP--></th>
                <th style="width: 60px">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     id="item_img_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Image Upload </h4>
            </div>
            <div class="modal-body">
                <center>
                    <form id="img_uplode_form">
                        <input type="hidden" id="img_item_id" name="item_id">

                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 250px; height: 150px;">
                                <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="item_img" alt="...">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail"
                                 style="max-width: 250px; max-height: 150px;"></div>
                            <div>
                        <span class="btn btn-default btn-file">
                            <span class="fileinput-new">Select image</span>
                            <span class="fileinput-exists">Change</span>
                            <input type="file" name="img_file" onchange="img_uplode()">
                        </span>
                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                            </div>
                        </div>
                    </form>
                </center>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Crew From ERP"
     id="itemMasterFromERP">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('manufacturing_segments_from_erp');?><!--Segments from ERP--> </h4>
            </div>
            <div class="modal-body">
                <div id="sysnc">
                    <div class="table-responsive">
                        <table id="segment_table_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_segment_ID');?></abbr></th>
                                <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_segment_description');?></th>
                                <th style="min-width: 5%; text-align: center !important;">
                                    <button type="button" data-text="Add" onclick="addSegment()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('manufacturing_add_segment');?>
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Crew From ERP"
     id="linkitemMasterFromERP">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Segments from ERP </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="mfqSegmentID">
                <div id="sysnc">
                    <div class="table-responsive">
                        <table id="segment_table_link" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">&nbsp;</th>
                                <th style="min-width: 12%">Segment ID</abbr></th>
                                <th style="min-width: 12%">Segment Description</th>
                                <th style="min-width: 5%; text-align: center !important;">
                                    <button type="button" data-text="Add" onclick="linksegment()"
                                            class="btn btn-xs btn-primary">
                                        <i class="fa fa-plus" aria-hidden="true"></i> Add Segment
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Sub Item Category Modal" data-backdrop="static"
     data-keyboard="false"
     id="subItemCategoryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times text-red"></i></span></button>
                <h4 class="modal-title" id="modal_title_category">Title </h4>
            </div>
            <div class="modal-body">
                <form id="frm_mfq_assign_categories">
                    <input type="hidden" value="0" id="frm_itemAutoID" name="itemAutoID">


                    <header class="head-title">
                        <h2>Categories </h2>
                    </header>

                    <div class="row">

                        <div class="form-group col-sm-4">
                            <label class="title">Main</label>
                        </div>
                        <div class="form-group col-sm-6">
                        <span class="input-req"
                              title="Required Field">
                            <?php echo form_dropdown('categoryID', get_mfq_category_drop(0, 2), '', 'class="form-control" id="categoryID"  required'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Sub </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <select name="subCategory" class="form-control" id="frm_subCategory">
                                    <option value=""></option>
                                </select>
                                <span class="input-req-inner"></span>
                                <!--<input type="text" name="description" id="sub_category_description"
                                       class="form-control" required>
                                <span class="input-req-inner"></span>-->
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Sub Sub </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                               <select name="subSubCategory" class="form-control" id="frm_subSubCategory">
                                    <option value=""></option>
                                </select>
                                <span class="input-req-inner"></span>

                            </span>
                        </div>
                    </div>
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" onclick="assign_itemCategory_children()" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Add
                </button>

                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<div class="modal" tabindex="-1" role="dialog"
     id="mfq_sub_segment">
    <div class="modal-dialog modal-lg" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Sub Segment</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>Add Sub Segment</h2>
                    </header>
                </div>
                <form method="post" id="add_sub_segment">
                    <input type="hidden" id="mfqSegmentID_sub" name="mfqSegmentID_sub"/>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        &nbsp;
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Code</label>
                    </div>

                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <input type="text" name="segmentCode" id="segmentCode" class="form-control" placeholder="Code"
                           required>
                    <span class="input-req-inner"></span>
                </span>
                    </div>

                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        &nbsp;
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Description</label>
                    </div>

                    <div class="form-group col-sm-4">
                 <span class="input-req" title="Required Field">
                    <input type="text" name="description" id="description" class="form-control" placeholder="Description"
                           required>
                    <span class="input-req-inner"></span>
                    </span>
                    </div>

                </div>
            </div>
            </form>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary"
                        onclick="save_subsegment()">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    function assign_itemCategory_children() {

        update_itemCategory();


    }

    function update_itemCategory() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: $("#frm_mfq_assign_categories").serialize(),
            url: "<?php echo site_url('MFQ_AssetMaster/assign_itemCategory_children'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    myAlert('s', data.message);
                    $("#subItemCategoryModal").modal('hide');
                    segment_table();
                } else {
                    myAlert('e', data.message);
                }

            }, error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', "Code" + xhr.status + " : Error : " + thrownError)
            }
        });
    }

    function add_mainCategory(id, title) {
        $("#frm_itemAutoID").val(id);
        $("#frm_subCategory").empty();
        $("#frm_subSubCategory").empty();
        $("#subItemCategoryModal").modal('show');
        $("#modal_title_category").html(title);
        $("#categoryID").val(-1);
    }


    var oTable;
    var oTable2;
    var selectedItemsSync = [];
    $(document).ready(function () {
        $("#categoryID").change(function (e) {
            var categoryID = $("#categoryID").val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {parentID: categoryID},
                url: "<?php echo site_url('MFQ_ItemMaster/get_mfq_subCategory'); ?>",
                beforeSend: function () {
                    $("#frm_subCategory").empty();
                    $("#frm_subSubCategory").empty();
                },

                success: function (data) {
                    if (data) {
                        $("#frm_subCategory").append('<option value="-1">Select</option>');
                        $.each(data, function (key, value) {
                            $("#frm_subCategory").append('<option value="' + value['itemCategoryID'] + '">' + value['description'] + '</option>');
                        });
                    }
                }, error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', "Code" + xhr.status + " : Error : " + thrownError)
                }
            });
        });

        $("#frm_subCategory").change(function (e) {
            var subCategoryID = $("#frm_subCategory").val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {parentID: subCategoryID},
                url: "<?php echo site_url('MFQ_ItemMaster/get_mfq_subCategory'); ?>",
                beforeSend: function () {
                    $("#frm_subSubCategory").empty();
                },

                success: function (data) {
                    if (data) {
                        $("#frm_subSubCategory").append('<option value="-1">Select</option>');
                        $.each(data, function (key, value) {
                            $("#frm_subSubCategory").append('<option value="' + value['itemCategoryID'] + '">' + value['description'] + '</option>');
                        });
                    }
                }, error: function (xhr, ajaxOptions, thrownError) {
                    myAlert('e', "Code" + xhr.status + " : Error : " + thrownError)
                }
            });
        });

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
            fetchPage('system/mfq/mfq_segment','','Segment')
        });

        segment_table();
        sync_segment_table();


        $("#btnSync_fromErp").click(function () {
            sync_segment_table();
            $("#itemMasterFromERP").modal('show');
        });
    });


    function LoadMainCategory() {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        load_sub_cat();
        segment_table();
    }

    function LoadMainCategorySync() {
        $('#syncSubcategoryID').val("");
        $('#syncSubcategoryID option').remove();
        load_sub_cat_sync();
        sync_segment_table();
    }


    function segment_table() {
        oTable = $('#segment_table').DataTable({

            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "order": [[ 4, "desc" ]],
            "sAjaxSource": "<?php echo site_url('MFQ_SegmentMaster/fetch_segments'); ?>",
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

                {"mData": "serialNo"},
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": '',
                    "defaultContent": ''
                },
                {"mData": "segmentCode"},
                {"mData": "description"},
                {"mData": "masterdescription"},
                {"mData": "edit"},
                {"mData": "mfqSegmentID"}

            ],
            "columnDefs": [{"targets": [6], "visible": false},{"targets": [0,5,6], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
               /* aoData.push({"name": "mainCategory", "value": $("#mainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});*/
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
        $('#segment_table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = oTable.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child(segment_drilldown(row.data())).show();
                fetch_segment_drilldown(row.data());
                tr.addClass('shown');
            }
        });
    }

    function sync_segment_table() {
        oTable2 = $('#segment_table_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_SegmentMaster/fetch_sync_segment'); ?>",
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
                {"mData": "segmentID"},
                {"mData": "companyCode"},
                {"mData": "description"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#syncMainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#syncSubcategoryID").val()});
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
                oTable.draw();
                //segment_table();
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
                    url: "<?php echo site_url('MFQ_ItemMaster/delete_item'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        oTable.draw();
                        //segment_table();
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
                url: "<?php echo site_url('MFQ_ItemMaster/changeitemactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        oTable.draw();
                        //segment_table();
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
                url: "<?php echo site_url('MFQ_ItemMaster/changeitemactive'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        oTable.draw();
                        //segment_table();
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
            url: '<?php echo site_url("MFQ_ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Sub Category'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });

                    $("#subcategoryID").select2();
                }

            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    function load_sub_cat_sync(select_val) {
        $('#syncSubcategoryID').val("");
        $('#syncSubcategoryID option').remove();
        var subid = $('#syncMainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#syncSubcategoryID').empty();
                    var mySelect = $('#syncSubcategoryID');
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

    function addSegment() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_SegmentMaster/add_segments"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedItemsSync},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    oTable.draw();
                    //segment_table();
                    sync_segment_table();
                    selectedItemsSync = [];
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function  link_segment_master(id)
    {
        $('#mfqSegmentID').val(id);
        $('#linkitemMasterFromERP').modal('show');
       link_segment_table();


    }

    function link_segment_table() {
        oTable2 = $('#segment_table_link').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('MFQ_SegmentMaster/fetch_link_segment'); ?>",
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
                {"mData": "segmentID"},
                {"mData": "companyCode"},
                {"mData": "description"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#syncMainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#syncSubcategoryID").val()});
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

    function linksegment()
    {
        var selectedVal = $("input:radio.radioChk:checked");
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_SegmentMaster/link_segment"); ?>',
            dataType: 'json',
            data: {'selectedItemsSync': selectedVal.val(),mfqSegmentID:$('#mfqSegmentID').val()},
            async: false,
            success: function (data) {
                if (data['status']) {
                    refreshNotifications(true);
                    link_segment_table();
                    sync_segment_table();
                    oTable.draw();
                    $("#linkitemMasterFromERP").modal('hide');
                } else {
                    refreshNotifications(true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function segment_drilldown(d) {
        var segmentID = d.mfqSegmentID;
        return '<div id="drilldown_' + segmentID + '"></div>';
    }

    function fetch_segment_drilldown(d) {
        var segmentID = d.mfqSegmentID;
      $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {mfqSegmentID: segmentID},
            url: '<?php echo site_url('MFQ_SegmentMaster/get_mfq_sub_segment'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#drilldown_" + segmentID).html(data);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }
    function add_sub_segment(segmentID) {
        $('#mfqSegmentID_sub').val(segmentID);
        $('#segmentCode').val('');
        $('#description').val('');

        $('#mfq_sub_segment').modal('show');
    }
    function save_subsegment() {
        var data = $('#add_sub_segment').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_SegmentMaster/save_subsegment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1])
                if(data[0] =='s')
                {
                    oTable.draw();
                    $('#mfq_sub_segment').modal('hide');
                }

                //segment_table();
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

</script>