<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('configuration', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_customer_category');
echo head_page($title, false);
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row table-responsive">
    <div class="col-md-5">

    </div>
    <div class="col-md-7 text-right">
        <button type="button" class="btn btn-primary-new size-sm pull-right" onclick="opencustomercategorymodel()"><i class="fa fa-plus"></i></span><?php echo $this->lang->line('common_create_category') ?><!-- Create Category--> </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="customer_category_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th style="min-width: 40%"><?php echo $this->lang->line('common_description') ?></th>
                <th style="min-width: 2%"><?php echo $this->lang->line('common_action') ?></th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="customer_category_modal" class=" modal fade bs-example-modal-lg"
    style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="categoryHead"></h5>
            </div>
            <?php echo form_open('', 'role="form" id="customer_category_form"'); ?>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="partyCategoryID" name="partyCategoryID">
                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('common_category') ?></label>
                        <input type="text" name="categoryDescription" id="categoryDescription" class="form-control">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary " onclick="saveCategory()"> <i class="fa fa-plus"></i><?php echo $this->lang->line('common_save') ?>
                    </button>
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close') ?></button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customerCategoryLinkModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="customer_category_link_form"'); ?>
            <input type="hidden" name="partyCategoryIDhn" id="partyCategoryIDhn">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Customer Category Link <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="customerName">
                            <h4>Customer Category :- </h4>
                        </label>
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
<div class="modal fade" id="customerCategoryDuplicateModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" id="">
            <?php echo form_open('', 'role="form" id="customer_category_duplicate_form"'); ?>
            <input type="hidden" name="partyCategoryIdDuplicatehn" id="partyCategoryIdDuplicatehn">
            <!-- <input type="hidden" name="masterAccountYNhn" id="masterAccountYNhn"> -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('config_customer_category_replication') ?><!--Chart of account Replication--> <span id=""></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row" id="loadpartyCategoryDuplicate">

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
        customer_category_table_table();

        $('#customer_category_link_form').bootstrapValidator({
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
                url: "<?php echo site_url('GroupCategory/save_customer_category_link'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    myAlert(data[0], data[1]);
                    $('#btnSave').attr('disabled', false);
                    if (data[0] == 's') {
                        /*load_item_category_details_table();
                         load_company($('#itemCategoryIDhn').val());
                         $('#companyID').val('').change();*/
                        $('#customerCategoryLinkModal').modal('hide');
                        load_all_companies_category();
                    }

                },
                error: function() {
                    alert('An Error Occurred! Please Try Again.');
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        });

        $('#customer_category_duplicate_form').bootstrapValidator({
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
                url: "<?php echo site_url('GroupCategory/save_customer_category_duplicate'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    HoldOn.close();
                    myAlert(data[0], data[1]);
                    $('#btnSavedup').attr('disabled', false);
                    if (data[0] == 's') {
                        load_all_companies_duplicate();
                        $('#customerCategoryDuplicateModal').modal('hide');
                    }

                    if (jQuery.isEmptyObject(data[2])) {

                    } else {
                        $('#errormsg').empty();
                        $.each(data[2], function(key, value) {
                            $('#errormsg').append('<tr><td>' + value['companyname'] + '</td><td>' + value['message'] + '</td></tr>');
                        });
                        // $('#invalidinvoicemodal').modal('show');
                        $('#customerCategoryDuplicateModal').modal('hide');
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

    function customer_category_table_table() {
        var Otable = $('#customer_category_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('GroupCategory/fetch_customer_category'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "fnInitComplete": function() {

            },
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
                    "mData": "partyCategoryID"
                },
                {
                    "mData": "categoryDescription"
                },
                {
                    "mData": "edit"
                }
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function(sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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


    function saveCategory() {
        var data = $("#customer_category_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('GroupCategory/saveCategory'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    customer_category_table_table();
                    $('#customer_category_modal').modal('hide');
                    $('#categoryDescription').val('');
                    $('#partyCategoryID').val('');
                }
            },
            error: function() {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                myAlert(data[0], data[1]);
            }
        });
    }

    function editcustomercategory(partyCategoryID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'partyCategoryID': partyCategoryID
            },
            url: "<?php echo site_url('GroupCategory/getCategory'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                if (data) {
                    $('#categoryDescription').val(data['categoryDescription']);
                    $('#partyCategoryID').val(partyCategoryID);
                    $('#categoryHead').html('<?php echo $this->lang->line('config_edit_category') ?>'); /*Edit Category*/
                    $('#customer_category_modal').modal('show');
                }
            },
            error: function() {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function opencustomercategorymodel() {
        $('#partyCategoryID').val('');
        $('#categoryHead').html('<?php echo $this->lang->line('config_add_new_category') ?>'); /*Add New Category*/
        $('#customer_category_form')[0].reset();
        $('#customer_category_modal').modal('show');
    }

    function delete_category(partyCategoryID) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this Category!",
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
                        'partyCategoryID': partyCategoryID
                    },
                    url: "<?php echo site_url('GroupCategory/delete_category'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            customer_category_table_table();
                        }
                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function link_group_customer_category(partyCategoryID) {
        $('#customerCategoryLinkModal').modal({
            backdrop: "static"
        });
        //$('#companyID').val('').change();
        $('#partyCategoryIDhn').val(partyCategoryID);
        $('#btnSave').attr('disabled', false);
        load_all_companies_category();
        load_category_header();
    }

    function load_all_companies_category() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                groupCustomerCategoryID: $('#partyCategoryIDhn').val()
            },
            url: "<?php echo site_url('GroupCategory/load_all_companies_customer_categories'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadComapnyCategories').removeClass('hidden');
                $('#loadComapnyCategories').html(data);
                $('.select2').select2();

            },
            error: function() {

            }
        });
    }

    function load_duplicate_group_customer_category(id) {
        $('#customerCategoryDuplicateModal').modal({
            backdrop: "static"
        });
        $('#partyCategoryIdDuplicatehn').val(id);
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
                groupCustomerCategoryID: $('#partyCategoryIdDuplicatehn').val()
            },
            url: "<?php echo site_url('GroupCategory/load_all_customer_companies_duplicate'); ?>",
            beforeSend: function() {

            },
            success: function(data) {
                $('#loadpartyCategoryDuplicate').removeClass('hidden');
                $('#loadpartyCategoryDuplicate').html(data);
                $('.select2').select2();
            },
            error: function() {

            }
        });
    }

    function load_category_header() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'groupCustomerCategoryID': $('#partyCategoryIDhn').val()
            },
            url: "<?php echo site_url('GroupCategory/load_category_header'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#categoryName').html(data['categoryDescription']);
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

    function clearcategory(id) {
        $('#partyCategoryID_' + id).val('').change();
    }
</script>