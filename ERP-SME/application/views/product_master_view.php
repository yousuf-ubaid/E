<?php
$month_start = date('Y-m-01');
$current_date = date('Y-m-d');
$audit_column_arr = audit_column_arr();
$payActiveCompany_arr = payActiveCompany_arr();
?>

<style>
    .scheduler-border legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 16px;
        font-weight: 500
    }

    fieldset.scheduler-border {
        border: 1px solid #ddd !important;
        padding: 10px 0px;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
        margin: 10px;
    }

    .multiselect2-container.dropdown-menu{
        width: 300px !important;
    }

    #close-btn{
        color: red;
        font-weight: bolder;
        padding: 0px 4px;
    }

    .product-assign{
        display: none;
    }

    .bootBox-btn-margin{
        margin-right: 10px;
    }
</style>

<section class="content">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Product Master</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border" id="product-frm-title"> New Product </legend>
                            <?php echo form_open('', 'id="frm-product" class="form-horizontal" autocomplete="off" role="form"'); ?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Description</label>
                                <div class="col-sm-5">
                                    <input type="text" name="description" id="description" class="form-control">
                                </div>
                                <input type="hidden" name="product_id" id="product_id" value="">
                            </div>

                            <div class="box-footer">
                                <div class="pull-right">
                                    <button class="btn btn-primary btn-sm " type="button" id="product-btn" onclick="add_product()">
                                        Save
                                    </button>
                                    &nbsp;
                                    <button class="btn btn-default btn-sm" type="button" onclick="reset_product_frm()">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <?php echo form_close(); ?>
                        </fieldset>
                    </div>

                    <div class="col-md-6">
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"> Product List </legend>
                            <div class="table-responsive">
                                <table id="product_tb" class="<?=table_class()?>">
                                    <thead>
                                    <tr>
                                        <th style="width: 15px">#</th>
                                        <th style="width: auto">Description</th>
                                        <th style="width: 70px"></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <hr class="product-assign"/>

                <div class="row product-assign" id="product-assign-container" style="margin: 0px">
                    <fieldset class="scheduler-border" style="">
                        <legend class="scheduler-border"><h3> Product - <span id="company-title"></span></h3></legend>
                        <div class="col-md-12" style="margin: -20px 0px;">
                            <button type="button" class="pull-right" id="close-btn" onclick="hide_product_assign_container()">×</button>
                        </div>

                        <div class="col-md-6" style="display: none">
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border" id="product-frm-title"> Assign to Company  </legend>
                                <?php echo form_open('', 'id="frm-product-assign" class="form-horizontal" autocomplete="off" role="form"'); ?>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Company</label>
                                    <div class="col-sm-5">
                                        <?=form_dropdown('company_drop[]', null, '', 'class="form-control select2" 
                                                            id="company_drop" multiple="multiple"');?>
                                    </div>
                                    <input type="hidden" name="product_id" id="assign_product_id" value="">
                                </div>

                                <div class="box-footer">
                                    <div class="pull-right">
                                        <button class="btn btn-primary btn-sm " type="button" onclick="assign_product()">
                                            Save
                                        </button>
                                    </div>
                                </div>

                                <?php echo form_close(); ?>
                            </fieldset>
                        </div>

                        <div class="col-md-6">
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border"> Company List  </legend>
                                <div class="table-responsive">
                                    <table id="product_assign_tb" class="<?=table_class()?>">
                                        <thead>
                                        <tr>
                                            <th style="width: 15px">#</th>
                                            <th style="width: auto"> Code </th>
                                            <th style="width: auto">Company Name</th>
                                            <th style="width: 70px"></th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </fieldset>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" type="text/css" href="<?= base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<script type="text/javascript" src="<?= base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>

<script type="text/javascript">
    $('#company_drop').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        buttonWidth:300,
        maxHeight: 200,
        numberDisplayed: 1
    });

    $(document).ready(function () {
        load_productMaster();
    });

    function load_productMaster() {
        $('#product_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_product_master'); ?>",
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
            "columnDefs": [
               { "targets": [0,2], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "id"},
                {"mData": "description"},
                {"mData": "action"},
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

    function add_product(){
        let post_data = $('#frm-product').serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/new_product'); ?>",
            data: post_data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    reset_product_frm();
                    load_productMaster();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function edit_product(id, des){
        $('#product-frm-title').text(' Update Product ');
        $('#product_id').val(id);
        $('#description').val(des);
        $('#frm-product').hide().fadeIn('slow');
    }

    function reset_product_frm(){
        $('#frm-product')[0].reset();
        $('#product_id').val('');
        $('#product-frm-title').text(' New Product ');
        $('#frm-product').hide().fadeIn('slow');
    }

    function confirm_delete_product(id, des) {
        hide_product_assign_container();
        swal({
                title: "Are you sure?",
                text: "You want to delete this product ( "+des+" )",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                delete_product(id);
            },
        );
    }

    function delete_product(id, verify=0) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?= site_url('Dashboard/delete_product');?>",
            data: {'id': id, 'verify': verify},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                ajax_toaster(data, null, delete_verify)
                if(data[0] == 's'){
                    load_productMaster()
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function delete_verify(data){
        bootbox.confirm({
            title: '<i class="fa fa-exclamation-triangle text-yellow"></i> <strong>Confirmation!</strong>',
            message: data[1],
            buttons: {
                'cancel': {
                    label: 'Cancel',
                    className: 'btn-default pull-right'
                },
                'confirm': {
                    label: 'Yes Proceed',
                    className: 'btn-primary pull-right bootBox-btn-margin'
                }
            },
            callback: function(result) {
                if (result) {
                    delete_product(data['id'], 1);
                }
            }
        });
    }

    function setup_product(id, title){
        $('.product-assign').show();
        $('#company-title').text(title);
        $('#assign_product_id').val(id);
        load_companies();
        fetch_product_company();
        $('html, body').animate({
            scrollTop: $("#product-assign-container").offset().top
        }, 1000);
    }

    function load_companies(){        

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?=site_url('Dashboard/get_companies_for_product');?>",            
            cache: false,
            beforeSend: function () {
                startLoad();
                $('#company_drop').empty();
            },
            success: function (data) {
                stopLoad();
                if(data[0] == 's'){

                    let str = '';
                    $.each(data['company'], function (i, val) {
                        str += '<option value="'+val['company_id']+'">'+val['company_name']+' ( '+val['company_code']+' )</option>'
                    });

                    $('#company_drop').html(str);
                    $("#company_drop").multiselect2('destroy');
                    $('#company_drop').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        selectAllValue: 'select-all-value',
                        buttonWidth:300,
                        maxHeight: 200,
                        numberDisplayed: 1
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function assign_product(){
        let post_data = $('#frm-product-assign').serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Dashboard/assign_product'); ?>",
            data: post_data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if(data[0] == 's'){
                    load_companies();
                    fetch_product_company();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    function fetch_product_company() {
        $('#product_assign_tb').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Dashboard/fetch_product_company'); ?>",
            "aaSorting": [[2, 'desc']],
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
            "columnDefs": [
                { "targets": [0,3], "orderable": false }
            ],
            "aoColumns": [
                {"mData": "id"},
                {"mData": "company_code"},
                {"mData": "company_name"},
                {"mData": "action"},
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                let pr_id = $('#assign_product_id').val();
                aoData.push({'name':'pr_id', 'value':pr_id});
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

    function remove_company(id){
        swal({
                title: "Are you sure?",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo site_url('Dashboard/remove_company_from_product');?>",
                    data: {'id': id},
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            fetch_product_company();
                            load_companies();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });

            },
        );
    }

    function hide_product_assign_container() {
        $('.product-assign').hide();
    }
</script>
