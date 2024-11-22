<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_item_category');
echo head_page($title, false);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<style>
    .form1 {
        width: 290px !important;
    }

    .btn-primary {
        background-color: #34495e;
        border-color: #34495e;
        color: #FFFFFF;
    }
</style>
<!--<div class="row">
    <div class="col-md-9"></div>
    <div class="col-md-3" style="margin-bottom: 15px; margin-top:15px;">
        <button type="button" onclick="reset_form()" class="btn btn-xs btn-primary pull-right" data-toggle="modal" data-target="#itemcategory_model"><span
                class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create
            New
        </button>
    </div>
</div>-->
<div class="table-responsive">
    <table id="itemcategory_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 70%"><?php echo $this->lang->line('common_description') ?><!--Description--></th>
            <th style="min-width: 20%"><?php echo $this->lang->line('config_common_sub_group') ?><!--Sub Category--></th>
            <!--<th style="min-width: 5%">Edit</th>-->
        </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="itemcategory_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Item Category</h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="itemcategory_form"') ?>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="itemcategoryedit" name="itemcategoryedit">
                <div class="row" style="margin-left:0px !important;">
                    <div class="form-group col-sm-4">
                        <label for="">Description</label>
                        <input type="text" class="form-control form1" id="description" name="description">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Code Prefix</label>
                        <input type="text" class="form-control form1" id="codeprefix" name="codeprefix">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Start Serial</label>
                        <input type="number" class="form-control form1" id="startserial" name="startserial">
                    </div>
                </div>
                <div class="row" style="margin-left:0px !important;">
                    <div class="form-group col-sm-4">
                        <label for="">Code Length</label>
                        <input type="number" class="form-control form1" id="codelength" name="codelength">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Item Type</label>
                        <select name="itemtype" id="itemtype" class="form-control form1 searchbox">
                            <option value="">Please Select</option>
                            <option value="Inventory Item">Inventory Item</option>
                            <option value="Non Inventory Item">Non Inventory Item</option>
                            <option value="Service">Service</option>
                            <option value="Fixed Assets">Fixed Assets</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="categoryTypeID">Category Type</label>
                        <?php echo form_dropdown('categoryTypeID', category_type(), '', 'class="form-control" id="categoryTypeID" required'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-primary">Save <span class="glyphicon glyphicon-floppy-disk"
                                                                                aria-hidden="true"></span></button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="itemcategoryedit_model" role="dialog">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Item Category Edit</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" class="form-horizontal" id="itemcategoryedit_form"') ?>
                <input type="hidden" class="form-control" id="itemcategoryeditfrm" name="itemcategoryeditfrm"
                       value="<?php if (isset($_POST["page_id"])) echo $_POST["page_id"]; ?>">
                <div class="form-group col-sm-4">
                    <label for="">Description</label>
                    <input type="text" class="form-control form1" id="description" name="description">
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-primary">Save <span class="glyphicon glyphicon-floppy-disk"
                                                                                aria-hidden="true"></span></button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="itemCategoryLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="item_category_link_form"'); ?>
            <input type="hidden" name="itemCategoryIDhn" id="itemCategoryIDhn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Item Category Link <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="customerName"><h4>Item Category :- </h4></label>
                        <label style="color: cornflowerblue;font-size: 1.2em;" id="categoryName"></label>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadComapnyCategories">

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
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/GroupItemCategory/srp_group_itemcategory_view', '', 'Item Category');
        });
        $(".searchbox").select2({
            placeholder: "Please Select"
        });

        itemcategoryview();
        $('#itemcategory_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                codeprefix: {validators: {notEmpty: {message: 'Code Prefix is required.'}}},
                startserial: {validators: {notEmpty: {message: 'Start Serial is required.'}}},
                codelength: {validators: {notEmpty: {message: 'Code Length is required.'}}},
                itemtype: {validators: {notEmpty: {message: 'Item Type is required.'}}},
                description: {validators: {notEmpty: {message: 'Description is required.'}}}
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
                url: "<?php echo site_url('ItemCategoryGroup/save_itemcategory'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    HoldOn.close();
                    refreshNotifications(true);
                    if (data) {
                        $("#itemcategory_model").modal("hide");
                        itemcategoryview();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#item_category_link_form').bootstrapValidator({
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
                    url: "<?php echo site_url('ItemCategoryGroup/save_item_category_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        myAlert(data[0], data[1]);
                        $('#btnSave').attr('disabled', false);
                        if (data[0] == 's') {
                            /*load_item_category_details_table();
                            load_company($('#itemCategoryIDhn').val());
                            $('#companyID').val('').change();*/
                             $('#itemCategoryLinkModal').modal('hide');
                            load_all_companies_category();
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

    function itemcategoryview() {
        var Otable = $('#itemcategory_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('ItemCategoryGroup/load_category'); ?>",
            //"bJQueryUI": true,
            //"iDisplayStart ": 8,
            //"sEcho": 1,
            ///"sAjaxDataProp": "aaData",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "itemCategoryID"},
                {"mData": "description"},
                {"mData": "addsub"}
            ],
            "columnDefs": [{
                "targets": [2],
                "orderable": false
            }],
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

    function reset_form() {
        $("#itemtype").select2("val", "");
        $('#itemcategoryedit').val("");
        document.getElementById('itemcategory_form').reset();
    }

    $("#itemcategory_model").on("hidden.bs.modal", function () {
        itemcategoryview();
    });

    function openitemcateditmodel(id) {
        $("#itemcategory_model").modal("show");
        $('#itemcategoryedit').val(id);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id: id},
            url: "<?php echo site_url('ItemCategoryGroup/edit_itemcategory'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#description').val(data['description']);
                $('#codeprefix').val(data['codePrefix']);
                $('#startserial').val(data['StartSerial']);
                $('#codelength').val(data['codeLength']);
                $('#categoryTypeID').val(data['categoryTypeID']);
                $('#itemtype').select2('val', data['itemType']);
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');

            }
        });
    }

    function link_group_itemcategory(itemCategoryID) {
        $('#itemCategoryLinkModal').modal({backdrop: "static"});
        //$('#companyID').val('').change();
        $('#itemCategoryIDhn').val(itemCategoryID);
        $('#btnSave').attr('disabled', false);
        /*load_company(itemCategoryID);
         load_item_category_details_table();*/
        load_all_companies_category();
        load_category_header();
    }

    function load_company(itemCategoryID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {itemCategoryID: itemCategoryID, All: 'true'},
            url: "<?php echo site_url('ItemCategoryGroup/load_company'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapny').html(data);
                $('.select2').select2();
                load_comapny_itemcategories();
            }, error: function () {

            }
        });
    }

    function load_comapny_itemcategories() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: $('#companyID').val(), itemCategoryID: $('#itemCategoryIDhn').val(), All: 'true'},
            url: "<?php echo site_url('ItemCategoryGroup/load_company_itemcategory'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnySegments').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }


    function load_item_category_details_table() {
        Otable = $('#category_group_details').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('ItemCategoryGroup/fetch_category_Details'); ?>",
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
                {"mData": "groupItemCategoryDetailID"},
                {"mData": "company_name"},
                {"mData": "description"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [3], "orderable": false}, {}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "itemCategoryID", "value": $('#itemCategoryIDhn').val()});
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

    function delete_Item_group_category_link(id) {
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
                    data: {'groupItemCategoryDetailID': id},
                    url: "<?php echo site_url('ItemCategoryGroup/delete_item_category_link'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_item_category_details_table();
                            load_company($('#itemCategoryIDhn').val());
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_all_companies_category() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {groupItemCategoryID: $('#itemCategoryIDhn').val()},
            url: "<?php echo site_url('ItemCategoryGroup/load_all_companies_item_categories'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadComapnyCategories').removeClass('hidden');
                $('#loadComapnyCategories').html(data);
                $('.select2').select2();
            }, error: function () {

            }
        });
    }

    function clearcategory(id){
        $('#itemCategoryID_'+id).val('').change();
    }

    function load_category_header() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'groupItemCategoryID': $('#itemCategoryIDhn').val()},
                url: "<?php echo site_url('ItemCategoryGroup/load_category_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#categoryName').html(data['description']);
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
</script>