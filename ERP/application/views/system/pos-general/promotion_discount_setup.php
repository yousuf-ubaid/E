<link rel="stylesheet" href="<?php echo base_url('plugins/iCheck/minimal/_all.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script src="<?php echo base_url('plugins/iCheck/icheck.min.js') ?>"></script>
<style>
    .textCenter {
        text-align: center;
    }
</style>
<?php
$customerType = all_customer_type();
$main_category_arr = all_main_category_drop();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$date_format_policy = date_format_policy();
?>
<div class="box box-warning">
    <div class="box-body">
        <div class="nav-tabs-custom">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="posContainer">

                        <h4 style="font-size:16px; font-weight: 800;">
                            <?php echo $this->lang->line('pos_config_promotion_and_order_setup'); ?><!--Promotion & Order Setup-->
                            - <?php echo current_companyCode() ?>
                            <span class="btn btn-primary btn-xs pull-right" onclick="openAddCustomerModal()"><i
                                        class="fa fa-plus"></i>
                                <?php echo $this->lang->line('common_update_add_new'); ?><!--Add New--></span>
                        </h4>

                        <table class="<?php echo table_class_pos(1) ?>" id="tbl_customerType" style="font-size:12px;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--> </th>
                                <th>
                                    <?php echo $this->lang->line('common_effective_date'); ?><!-- Effective Date --></th>
                                <th><?php echo $this->lang->line('common_percentage'); ?><!--Percentage--> (%)</th>
                                <th><?php echo $this->lang->line('apply_to_all'); ?><!--Apply To All--></th>
                                <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    <div id="menu_edit_container2"></div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="customer_Modal" role="dialog">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('pos_config_add_promotion_or_order'); ?><!--Add Promotion or Order-->
                </h4>
            </div>
            <form role="form" id="fromcustomer" class="form-horizontal">
                <input type="hidden" class="form-control" id="customerIDhn" name="customerIDhn">
                <input type="hidden" class="form-control" id="posType" name="posType" value="2">
                <input type="hidden" id="customerTypeMasterID" name="customerTypeMasterID" value="2">
                <div class="modal-body" style="min-height: 100px; ">

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <fieldset>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"
                                           for="customerName"><?php echo $this->lang->line('common_description'); ?><?php required_mark(); ?></label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control input-md" id="customerName"
                                               name="customerName">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="">
                                        <?php echo $this->lang->line('pos_config_apply_to_all_item'); ?></label>
                                    <div class="col-md-4 extraColumnsgreen">
                                        <input id="applyToallItems" type="checkbox" value="1" name="applyToallItem">
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="">
                                        <?php echo $this->lang->line('common_date_from'); ?><?php required_mark(); ?></label>
                                    <div class="col-md-4">
                                        <div class="input-group datepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="datefrom"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                   id="datefrom" class="form-control input-md">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="">
                                        <?php echo $this->lang->line('common_date_to'); ?><?php required_mark(); ?></label>
                                    <div class="col-md-4">
                                        <div class="input-group datepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="dateto"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                   id="dateto" class="form-control input-md">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="commissionPercentageDiv">
                                    <label class="col-md-4 control-label" for="">
                                        <?php echo $this->lang->line('common_percentage'); ?><!--Percentage-->
                                        (%) <?php required_mark(); ?></label>
                                    <div class="col-md-4">
                                        <input type="number" step="any" class="form-control input-md"
                                               id="commissionPercentage" name="commissionPercentage">
                                    </div>
                                </div>

                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" onclick="addcustomer()" class="btn btn-primary btn-xs"><i class="fa fa-check"
                                                                                                    aria-hidden="true"></i>
                        <?php echo $this->lang->line('common_add'); ?><!--Add-->
                    </button>
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade pddLess" data-backdrop="static" id="itemDiscountEditModal" role="dialog" style="z-index: 9999;">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    Edit Discount Percentage<!--Add Promotion or Order-->
                </h4>
            </div>
            <form role="form" id="fromcustomer" class="form-horizontal">

                <div class="modal-body" style="min-height: 100px; ">

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <fieldset>
                                <div class="form-group" id="commissionPercentageDiv">
                                    <label class="col-md-4 control-label" for="">
                                        <?php echo $this->lang->line('common_percentage'); ?><!--Percentage-->
                                        (%) <?php required_mark(); ?></label>
                                    <div class="col-md-4">
                                        <input type="number" step="any" class="form-control input-md"
                                               id="currentDiscountPercentage" name="currentDiscountPercentage">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" onclick="saveEditedDiscount()" class="btn btn-primary btn-xs"><i
                                class="fa fa-check"
                                aria-hidden="true"></i>
                        <?php echo $this->lang->line('common_save'); ?><!--Add-->
                    </button>
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade pddLess" data-backdrop="static" id="itemDiscountModal" role="dialog" style="z-index: 9999;">
    <div class="modal-dialog" style="min-width: 60%; margin-top: 6px;">
        <div class="modal-content"> <!-- <div class="color-line"></div>-->
            <div class="modal-header modal-header-mini" style="padding: 5px 10px; ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"
                          style="color:red;">&times;</span>
                </button>
                <h4 class="modal-title">
                    Add Discount Percentage<!--Add Promotion or Order-->
                </h4>
            </div>
            <form role="form" id="fromcustomer" class="form-horizontal">

                <div class="modal-body" style="min-height: 100px; ">

                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <fieldset>
                                <div class="form-group" id="commissionPercentageDiv">
                                    <label class="col-md-4 control-label" for="">
                                        <?php echo $this->lang->line('common_percentage'); ?><!--Percentage-->
                                        (%) <?php required_mark(); ?></label>
                                    <div class="col-md-4">
                                        <input type="number" step="any" class="form-control input-md"
                                               id="discountPercentage" name="discountPercentage">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 5px 10px;">
                    <button type="button" onclick="addProductToPromotion()" class="btn btn-primary btn-xs"><i
                                class="fa fa-check"
                                aria-hidden="true"></i>
                        <?php echo $this->lang->line('common_add'); ?><!--Add-->
                    </button>
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Add Item"
     id="linkItemForDiscount">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add_item'); ?><!--Add Item--> </h4>
            </div>
            <div class="modal-body">
                <div class="row" style="margin: 1%">
                    <ul class="nav nav-tabs mainpanel">
                        <li class="active">
                            <a class="" data-id="0" href="#step1" data-toggle="tab" aria-expanded="true">
                                <span>
                                    <i class="fa fa-cog tachometerColor" aria-hidden="true"
                                       style="color: #50749f;font-size: 16px;"></i>&nbsp;&nbsp;<?php echo $this->lang->line('common_add_item'); ?> <!--Add Item-->
                                </span>
                            </a>
                        </li>
                        <li class="">
                            <a class="" data-id="0" href="#step2" data-toggle="tab" aria-expanded="true">
                                <span>
                                    <i class="fa fa-list tachometerColor" aria-hidden="true"
                                       style="color: #50749f;font-size: 16px;"></i>&nbsp;<?php echo $this->lang->line('pos_config_item_list'); ?>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div id="step1" class="tab-pane active">
                        <div id="sysnc">
                            <input class="hidden" id='discountDetAddID' name='discountDetAddID'>
                            <div class="row">
    
                                <div class="form-group col-sm-3">
                                    <!--<label>Main Category</label>-->
                                    <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="syncMainCategoryID" onchange="LoadMainCategorySync()"'); ?>
                                </div>
                                <div class="form-group col-sm-3">
                                    <!--<label>Sub Category</label>-->
                                    <select name="subcategoryID" id="syncSubcategoryID" class="form-control"
                                            onchange="load_subCat()"> <!-- sync_item_table() -->
                                        <option value="">Subcategory</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3">
                                    <!--<label>Sub Sub Category</label>-->
                                    <select name="subsubcategoryID" id="syncSubSubcategoryID" class="form-control"
                                            onchange="sync_item_table()">
                                        <option value="">Sub Subcategory</option>
                                    </select>  
                                </div>
                                <div class="form-group col-sm-3"></div>
                            </div>
                            <hr>
                            <div class="table-responsive">
                                <table id="item_table_sync" class="table table-striped table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">&nbsp;</th>
                                        <th style="min-width: 12%">
                                            <?php echo $this->lang->line('pos_config_main_category'); ?><!-- Main Category --></th>
                                        <th style="min-width: 12%">
                                            <?php echo $this->lang->line('pos_config_sub_category'); ?><!-- Sub Category --></th>
                                        <th style="min-width: 12%">
                                            <?php echo $this->lang->line('pos_config_subsub_category'); ?><!-- Subsub Category --></th>
                                        <th style="min-width: 25%">
                                            <?php echo $this->lang->line('common_item'); ?><!-- Item --></th>
                                        <th style="min-width: 10%">
                                            <?php echo $this->lang->line('pos_config_secondary_code'); ?><!-- Secondary Code --></th>
                                        <th style="min-width: 10%">
                                            <?php echo $this->lang->line('current_stock'); ?><!-- Current Stock --></th>
                                        <th style="min-width: 10%">
                                            Hidden</th>
                                        <th style="min-width: 10%">
                                            Hidden</th>
                                        <th style="min-width: 5%; text-align: center !important;">
                                            <button type="button" data-text="Add" onclick="addItemForDiscount()"
                                                    class="btn btn-xs btn-primary">
                                                <i class="fa fa-plus"
                                                   aria-hidden="true"></i><?php echo $this->lang->line('common_add_item'); ?>
                                                <!--Add Items-->
                                            </button>
                                        </th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="step2" class="tab-pane">
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <!--<label>Main Category</label>-->
                                <?php echo form_dropdown('listMainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="listMainCategoryID" onchange="LoadMainCategoryInList()"'); ?>
                            </div>
                            <div class="form-group col-sm-3">
                                <!--<label>Sub Category</label>-->
                                <select name="listSubcategoryID" id="listSubcategoryID" class="form-control"
                                        onchange="load_list_subCat()"> <!-- item_table_view()-->
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-3">
                                    <!--<label>Sub Sub Category</label>-->
                                    <select name="listSubsubcategoryID" id="listSubSubcategoryID" class="form-control"
                                            onchange="sync_item_table()">
                                        <option value="">Select Category</option>
                                    </select>  
                                </div>
                            <div class="form-group col-sm-3"></div>
                        </div>
                        <div class="row" style="margin: 20px;">

                            <div class="table-responsive">
                                <table id="item_table_view" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">&nbsp;</th>
                                        <th style="min-width: 12%">
                                            <?php echo $this->lang->line('pos_config_main_category'); ?><!-- Main Category --></th>
                                        <th style="min-width: 12%">
                                            <?php echo $this->lang->line('pos_config_sub_category'); ?><!-- Sub Category --></th>
                                        <th style="min-width: 25%">
                                            <?php echo $this->lang->line('common_item'); ?><!-- Item --></th>
                                        <th style="min-width: 10%">
                                            <?php echo $this->lang->line('pos_config_secondary_code'); ?><!-- Secondary Code --></th>
                                        <th style="min-width: 10%">
                                            <?php echo $this->lang->line('current_stock'); ?><!-- Current Stock --></th>
                                        <th style="min-width: 10%">
                                            <?php echo $this->lang->line('disc_percentage'); ?><!-- Discount Percentage --></th>
                                        <th style="min-width: 10%"><?php echo $this->lang->line('common_status'); ?></th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Add Item"
     id="addWarehouseModal">
    <div class="modal-dialog modal-lg" style="width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add warehouse </h4>
            </div>
            <div class="modal-body">
                <input class="hidden" id='promoID' name='promoID'>
                <div class="row" style="margin: 1%">
                    <ul class="nav nav-tabs mainpanel">
                        <li class="active">
                            <a class="" data-id="0" href="#step3" data-toggle="tab" aria-expanded="true">
                                <span>
                                    <i class="fa fa-cog tachometerColor" aria-hidden="true"
                                       style="color: #50749f;font-size: 16px;"></i>&nbsp;&nbsp;Add Warehouse
                                </span>
                            </a>
                        </li>
                        <li class="">
                            <a class="" data-id="0" href="#step4" data-toggle="tab" aria-expanded="true">
                                <span>
                                    <i class="fa fa-list tachometerColor" aria-hidden="true"
                                       style="color: #50749f;font-size: 16px;"></i>Warehouse List
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div id="step3" class="tab-pane active">
                        <div class="row" style="margin: 20px;">
                            <div class="table-responsive">
                                <table id="warehousesToAdd" class="table table-striped table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">&nbsp;</th>
                                        <th style="min-width: 12%">
                                            Warehouse
                                        </th>
                                        <th style="min-width: 12%">
                                        </th>

                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="step4" class="tab-pane">

                        <div class="row" style="margin: 20px;">

                            <div class="table-responsive">
                                <table id="warehousesList" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">&nbsp;</th>
                                        <th style="min-width: 12%">
                                            Warehouse
                                        </th>
                                        <th style="min-width: 12%">
                                        </th>

                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<script>
    var selectedItemsSync = [];
    $(document).ready(function (e) {
        fetchCustomerType();
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        $('.datepicto').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });

        $('.extraColumnsgreen input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });
    });

    function openAddCustomerModal() {
        $('#applyToallItems').iCheck('uncheck');
        $("#commissionPercentageDiv").hide();
        $('#customerIDhn').val('');
        $('#fromcustomer')[0].reset();
        $("#expenseGLAutoID").val("").change();
        $("#liabilityGLAutoID").val("").change();
        $('#fromcustomer').bootstrapValidator('resetForm', true);
        $(".clsDeliveryType").hide();
        $("#customer_Modal").modal('show');

    }

    function refreshMenuSize() {
        fetchPage('system/pos/settings/menu_size', '', 'Menu Size Master');
    }


    $("#applyToallItems").on('ifChecked', function () {
        $("#commissionPercentageDiv").show();
    });

    $("#applyToallItems").on('ifUnchecked', function () {
        $("#commissionPercentageDiv").hide();
    });

    function addcustomer() {
        $("#customerTypeMasterID").val(2);//promotion;
        var data = $('#fromcustomer').serializeArray();
        var applyall = 0;
        if ($('#applyToallItems').is(":checked")) {
            applyall = 1;
        }
        data.push({'name': 'applyToall', 'value': applyall});

        //requirment changed.
        //if(applyall == 1) {
        //    swal({
        //        title: "Are you sure?",
        //        text: "Deactivate Other Promotion setups for this date range!",
        //        type: "warning",
        //        showCancelButton: true,
        //        confirmButtonColor: "#DD6B55",
        //        confirmButtonText: "Confirm"
        //    },
        //    function () {
        //        $.ajax({
        //            type: "POST",
        //            url: "<?php //echo site_url('Pos_config/saveDiscountSetup') ?>//",
        //            data: data,
        //            dataType: "json",
        //            cache: false,
        //            beforeSend: function () {
        //                startLoad();
        //            },
        //            success: function (data) {
        //                stopLoad();
        //                if (data['error'] == 0) {
        //                    myAlert('s', data['message']);
        //                    $("#customer_Modal").modal('hide');
        //                    fetchCustomerType();
        //                } else if (data['error'] == 1) {
        //                    myAlert('e', data['message']);
        //                }
        //            },
        //            error: function (jqXHR, textStatus, errorThrown) {
        //                stopLoad();
        //                myAlert('e', '<br>Message: ' + errorThrown);
        //            }
        //        });
        //    }
        //);
        //} else {
        //    $.ajax({
        //        type: "POST",
        //        url: "<?php //echo site_url('Pos_config/saveDiscountSetup') ?>//",
        //        data: data,
        //        dataType: "json",
        //        cache: false,
        //        beforeSend: function () {
        //            startLoad();
        //        },
        //        success: function (data) {
        //            stopLoad();
        //            if (data['error'] == 0) {
        //                myAlert('s', data['message']);
        //                $("#customer_Modal").modal('hide');
        //                fetchCustomerType();
        //            } else if (data['error'] == 1) {
        //                myAlert('e', data['message']);
        //            }
        //        },
        //        error: function (jqXHR, textStatus, errorThrown) {
        //            stopLoad();
        //            myAlert('e', '<br>Message: ' + errorThrown);
        //        }
        //    });
        //}

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Pos_config/saveDiscountSetup') ?>",
            data: data,
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 0) {
                    myAlert('s', data['message']);
                    $("#customer_Modal").modal('hide');
                    fetchCustomerType();
                } else if (data['error'] == 1) {
                    myAlert('e', data['message']);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

        return false;
    }

    function delete_menuSize(id) {
        bootbox.confirm('<?php echo deleteConfirmationMsg() ?>', function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('Pos_config/delete_menuSize') ?>",
                    data: {id: id},
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 0) {
                            fetchMenuSize();
                            myAlert('s', data['message']);
                        } else {
                            myAlert('e', '<div>' + data['message'] + '</div>');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', 'Status: ' + textStatus + '<br>Message: ' + errorThrown);
                    }
                });
            }
        });
    }

    function fetchCustomerType() {
        $('#tbl_customerType').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_promotion_type'); ?>",
            "aaSorting": [[0, 'asc']],
            "aoColumnDefs": [{"bSortable": false, "aTargets": [2, 3, 4, 5]}],
            /*"language": {
             processing: '<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
             },*/
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[name='menueCustomerTypeIsactive']").bootstrapSwitch();
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');

                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);


                    if (parseInt(oSettings.aoData[x]._aData['EIdNo']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "customerID"},
                {"mData": "customerName"},
                {"mData": "daterange"},
                {"mData": "commissionPercentageCol", sClass: "textCenter"},
                {"mData": "applyToAllStatus"},
                {"mData": "Active"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "posType", "value": 2});// 2 = GPOS
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

    function edit_customerType(id) {
        $('#fromcustomer').bootstrapValidator('resetForm', true);
        $("#customer_Modal").modal("show");
        $('#customerIDhn').val(id);
        //$('#menuSizeHead').html('Edit Menu Size');
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {customerID: id},
            url: "<?php echo site_url('Pos_config/edit_customer'); ?>",
            success: function (data) {
                $('#customerName').val(data['customerName']);
                $('#datefrom').val(data['dateFrom']);
                $('#dateto').val(data['dateTo']);
                //$('#applyToallItems').prop('checked', false);
                if (data['applyToAllItem'] == 1) {
                    $('#commissionPercentageDiv').show();
                    // $('#applyToallItems').prop('checked', true);
                    $('#applyToallItems').iCheck('check');
                    $('#commissionPercentage').val(data['commissionPercentage']);

                } else {
                    $('#applyToallItems').iCheck('uncheck');
                    $('#commissionPercentageDiv').hide();
                }

                // $('.extraColumnsgreen input').iCheck({
                //     checkboxClass: 'icheckbox_square_relative-green',
                //     radioClass: 'iradio_square_relative-green',
                //     increaseArea: '20%'
                // });

                $('#isOnTimePayment').val(data['isOnTimePayment']).change();
                $('#expenseGLAutoID').val(data['expenseGLAutoID']).change();
                $('#liabilityGLAutoID').val(data['liabilityGLAutoID']).change();
                if (data['customerTypeMasterID'] == 1) {
                    $(".clsDeliveryType").show();

                } else if (data['customerTypeMasterID'] == 3) {
                    $(".clsDeliveryType").hide();
                    $(".wastage").show();
                } else {
                    $(".clsDeliveryType").hide()
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }

    function changecustomertypeIsactive(customerID) {


        var compchecked = 0;
        if ($('#menueCustomerTypeIsactive_' + customerID).is(":checked")) {

            compchecked = 1;
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {customerID: customerID, chkedvalue: compchecked},
                url: "<?php echo site_url('Pos_config/update_discount_setup_status'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                    fetchCustomerType();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });


        } else if (!$('#menueCustomerTypeIsactive_' + customerID).is(":checked")) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {customerID: customerID, chkedvalue: 0},
                url: "<?php echo site_url('Pos_config/update_discount_setup_status'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert('s', data['message']);
                    if (data['message'] == 's') {

                    }
                    fetchCustomerType();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

    }

    function checkOnTimePayment(tmpThisVal) {
        var tmpValue = tmpThisVal.value;
        $("#commissionPercentageDiv").show();

        $(".RevenueGLDropdown").hide();

        if (tmpValue == 1) {
            $(".clsDeliveryType").show();
            $("#commissionPercentage").val('');
            $("#infodetails").hide();
        } else if (tmpValue == 3) {
            $(".clsDeliveryType").hide();
            $("#isOnTimePayment").val('');
            $(".wastage").show();
            $("#commissionPercentage").val(100);
        } else if (tmpValue == 4) {
            $(".clsDeliveryType").hide();
            $(".RevenueGLDropdown").show();
        } else {
            $("#commissionPercentage").val('');
            //$("#onlyForDelivery").hide();
            $(".clsDeliveryType").hide();
            $("#isOnTimePayment").val('');
        }
    }

    function showinfo() {
        $("#infodetails").show();
        document.getElementById("infoicon").innerHTML = "<i class='fa fa-info-circle'  aria-hidden='true' onclick='hideinfo()'></i>";
    }

    function hideinfo() {
        $("#infodetails").hide();
        document.getElementById("infoicon").innerHTML = "<i class='fa fa-info-circle'  aria-hidden='true' onclick='showinfo()'></i>";
    }

    function link_item(discountID) {
        selectedItemsSync = [];
        //$('#syncSubcategoryID option').remove();
        //$('#syncSubSubcategoryID option').remove();
        $("#syncMainCategoryID ").val($("#syncMainCategoryID option:first").val());
        $('#discountDetAddID').val(discountID);
        sync_item_table();
        item_table_view();
        $("#linkItemForDiscount").modal('show');
    }

    function link_warehouse(promoID) {
        $('#promoID').val(promoID);
        $("#addWarehouseModal").modal('show');
        warehousesToAddTable();
        warehousesListTable();
    }

    function warehouseStatusSwitch(wareHouseAutoID, companyID, promoID) {
        var checked = $('#warehouseStatus_' + wareHouseAutoID).is(":checked");
        if (checked) {
            isActive = 1;
        } else {
            isActive = 0;
        }

        if(checked){
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'promoID': promoID, 'wareHouseAutoID': wareHouseAutoID},
                url: "<?php echo site_url('Pos_config/is_warehouse_link_to_another_promo'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data.status == 1) {
                        swal({
                                title: "Warning",
                                text: data.message,
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Confirm"
                            },
                            function (isConfirmed) {
                                if (isConfirmed) {
                                    $.ajax({
                                        type: 'post',
                                        dataType: 'json',
                                        data: {
                                            'promoID': promoID,
                                            'wareHouseAutoID': wareHouseAutoID,
                                            'isActive': isActive
                                        },
                                        url: "<?php echo site_url('Pos_config/warehouse_link_to_promo'); ?>",
                                        beforeSend: function () {
                                            startLoad();
                                        },
                                        success: function (data) {
                                            stopLoad();
                                            if (data.error == 0) {
                                                myAlert('s', data.message);
                                                warehousesToAddTable();
                                                warehousesListTable();
                                            }
                                        }, error: function () {
                                            alert('An Error Occurred! Please Try Again.');
                                            stopLoad();
                                        }
                                    });
                                } else {
                                    $('#warehouseStatus_' + wareHouseAutoID).bootstrapSwitch('state', false);
                                }
                            }
                        );
                    } else {
                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            data: {'promoID': promoID, 'wareHouseAutoID': wareHouseAutoID, 'isActive': isActive},
                            url: "<?php echo site_url('Pos_config/warehouse_link_to_promo'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                if (data.error == 0) {
                                    myAlert('s', data.message);
                                    warehousesToAddTable();
                                    warehousesListTable();
                                }
                            }, error: function () {
                                alert('An Error Occurred! Please Try Again.');
                                stopLoad();
                            }
                        });
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }else{
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {'promoID': promoID, 'wareHouseAutoID': wareHouseAutoID, 'isActive': isActive},
                url: "<?php echo site_url('Pos_config/warehouse_link_to_promo'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data.error == 0) {
                        myAlert('s', data.message);
                        warehousesToAddTable();
                        warehousesListTable();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

    }

    function addWarehouseToPromotion(wareHouseAutoID) {
        var promoID = $('#promoID').val();
        var wareHouseAutoID = wareHouseAutoID;
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'promoID': promoID, 'wareHouseAutoID': wareHouseAutoID},
            url: "<?php echo site_url('Pos_config/is_warehouse_link_to_another_promo'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.status == 1) {
                    swal({
                            title: "Warning",
                            text: data.message,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Confirm"
                        },
                        function (isConfirmed) {
                            if (isConfirmed) {
                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    data: {'promoID': promoID, 'wareHouseAutoID': wareHouseAutoID, 'isActive': 1},
                                    url: "<?php echo site_url('Pos_config/warehouse_link_to_promo'); ?>",
                                    beforeSend: function () {
                                        startLoad();
                                    },
                                    success: function (data) {
                                        stopLoad();
                                        if (data.error == 0) {
                                            myAlert('s', data.message);
                                            warehousesToAddTable();
                                            warehousesListTable();
                                        }
                                    }, error: function () {
                                        alert('An Error Occurred! Please Try Again.');
                                        stopLoad();
                                    }
                                });
                            } else {

                            }
                        }
                    );
                } else {
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        data: {'promoID': promoID, 'wareHouseAutoID': wareHouseAutoID, 'isActive': 1},
                        url: "<?php echo site_url('Pos_config/warehouse_link_to_promo'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data.error == 0) {
                                myAlert('s', data.message);
                                warehousesToAddTable();
                                warehousesListTable();
                            }
                        }, error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function warehousesToAddTable() {
        oTable2 = $('#warehousesToAdd').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_warehouses_to_add'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },

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
            },
            "aoColumns": [
                {"mData": "wareHouseAutoID"},
                {"mData": "wareHouseDescription"},
                {"mData": "action"}
            ],
            // "columnDefs": [{"visible":false,"searchable": true,"targets": [7,8] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "promoID", "value": $("#promoID").val()});
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

    function warehousesListTable() {
        oTable2 = $('#warehousesList').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_warehouses_list'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[name='warehouseStatus']").bootstrapSwitch();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "wareHouseAutoID"},
                {"mData": "wareHouseDescription"},
                {"mData": "action"}
            ],
            // "columnDefs": [{"visible":false,"searchable": true,"targets": [7,8] }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "promoID", "value": $("#promoID").val()});
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

    function LoadMainCategorySync() {
        $('#syncSubcategoryID').val("");
        $('#syncSubcategoryID option').remove();
        $('#syncSubSubcategoryID').val("");
        $('#syncSubSubcategoryID option').remove();
        load_sub_cat_sync();
        sync_item_table();
    }

    function load_sub_cat_sync(select_val) {
        $('#syncSubcategoryID').val("");
        $('#syncSubcategoryID option').remove();
        var subid = $('#syncMainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/load_subcat"); ?>',
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

    function load_subCat() {
        $('#syncSubSubcategoryID').val("");
        $('#syncSubSubcategoryID option').remove();
        load_sub_sub_cat();
        sync_item_table();
        
    }

    function load_sub_sub_cat(select_val) {
        $('#syncSubSubcategoryID').val("");
        $('#syncSubSubcategoryID option').remove();
        var subcatID = $('#syncSubcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subcatID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#syncSubSubcategoryID').empty();
                    var mySelect1 = $('#syncSubSubcategoryID');
                    mySelect1.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect1.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function sync_item_table() {
        oTable2 = $('#item_table_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_discount_add_item'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },

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
                {"mData": "itemAutoID"},
                {"mData": "mainCategory"},
                {"mData": "SubCategoryDescription"},
                {"mData": "SubSubCategoryDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "seconeryItemCode"},
                {"mData": "calculatedCurrentStock"},
                {"mData": "itemSystemCode"},
                {"mData": "itemDescription"},
                {"mData": "edit"}
            ],
            "columnDefs": [{
                "targets": [ 6 ],
                "visible": false,
                "searchable": true
            },{
                "targets": [ 7 ],
                "visible": false,
                "searchable": true
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#syncMainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#syncSubcategoryID").val()});
                aoData.push({"name": "subsubcategoryID", "value": $("#syncSubSubcategoryID").val()});
                aoData.push({"name": "id", "value": $("#discountDetAddID").val()});
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

    function item_table_view() {
        oTable2 = $('#item_table_view').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Pos_config/fetch_discount_item_view'); ?>",
            //"aaSorting": [[1, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[name='discountItemActive']").bootstrapSwitch();
                $("[rel=tooltip]").tooltip();
                var selectedRowID = parseInt('<?php echo (!empty($this->input->post('page_id'))) ? $this->input->post('page_id') : 0; ?>');
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['EIdNo']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "itemAutoID"},
                {"mData": "mainCategory"},
                {"mData": "SubCategoryDescription"},
                {"mData": "item_inventryCode"},
                {"mData": "seconeryItemCode"},
                {"mData": "calculatedCurrentStock"},
                {"mData": "percentage"},
                {"mData": "active"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#listMainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#listSubcategoryID").val()});
                //aoData.push({"name": "subcategory", "value": $("#listSubcategoryID").val()});   
                aoData.push({"name": "id", "value": $("#discountDetAddID").val()});
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

    function ItemsSelectedSync(item) {
        var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        } else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function addItemForDiscount() {
        $("#itemDiscountModal").modal('show');

    }

    function addProductToPromotion() {

        var discountPercentage = $("#discountPercentage").val();
        if (discountPercentage == "") {
            myAlert('e', 'Discount percentage is required.');
        } else {
            discountPercentage = parseFloat(discountPercentage);
            if (discountPercentage <= 100 && discountPercentage >= 0) {
                var id = $('#discountDetAddID').val();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/add_discount_item"); ?>',
                    dataType: 'json',
                    data: {
                        'selectedItemsSync': selectedItemsSync,
                        'docID': id,
                        'discountPercentage': discountPercentage
                    },
                    async: false,
                    success: function (data) {
                        if (data['status']) {
                            refreshNotifications(true);
                            sync_item_table();
                            item_table_view();
                            selectedItemsSync = [];
                        } else {
                            refreshNotifications(true);
                        }
                        $("#itemDiscountModal").modal('hide');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {

                    }
                });
            } else {
                myAlert('e', 'Please enter a value between 0 and 100');
            }
        }
    }

    function changediscountItemActive(id, itemAutoID) {
        var compchecked = 0;
        if ($('#discountItemActive_' + id + '_' + itemAutoID).is(":checked")) {
            compchecked = 1;
        }
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {id: id, itemAutoID: itemAutoID, chkedvalue: compchecked},
            url: "<?php echo site_url('Pos_config/update_dicount_item_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert('s', data['message']);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function LoadMainCategoryInList() {
        $('#listSubcategoryID').val("");
        $('#listSubcategoryID option').remove();
        $('#listSubSubcategoryID').val("");
        $('#listSubSubcategoryID option').remove();
        load_sub_cat_list();
        item_table_view();
    }

    function load_list_subCat() {
        $('#listSubSubcategoryID').val("");
        $('#listSubSubcategoryID option').remove();
        load_sub_sub_cat_list();
        item_table_view();
        
    }

    function load_sub_cat_list(select_val) {
        $('#listSubcategoryID').val("");
        $('#listSubcategoryID option').remove();
        var subid = $('#listMainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#listSubcategoryID').empty();
                    var mySelect = $('#listSubcategoryID');
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

    function load_sub_sub_cat_list(select_val) {
        $('#listSubSubcategoryID').val("");
        $('#listSubSubcategoryID option').remove();
        var subcatID = $('#listSubcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Pos_config/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subcatID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#listSubSubcategoryID').empty();
                    var mySelect1 = $('#listSubSubcategoryID');
                    mySelect1.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect1.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function loadDiscountEditDialog() {
        $("#itemDiscountEditModal").modal('show');
        localStorage.setItem('selectedPromoItemId', $(this).data('promo_item_id'));
        $("#currentDiscountPercentage").val(this.value);
    }

    function saveEditedDiscount() {
        var discountPercentage = $("#currentDiscountPercentage").val();
        if (discountPercentage == "") {
            myAlert('e', 'Percentage field is required.');
        } else {
            if (discountPercentage >= 0 && discountPercentage <= 100) {
                var selectedPromoItemId = localStorage.getItem('selectedPromoItemId');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Pos_config/save_edited_discount_percentage"); ?>',
                    dataType: 'json',
                    data: {'selectedPromoItemId': selectedPromoItemId, 'discountPercentage': discountPercentage},
                    async: false,
                    success: function (data) {
                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                            $("#itemDiscountEditModal").modal('hide');
                            item_table_view();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {

                    }
                });
            } else {
                myAlert('e', 'Please enter a value between 0 and 100');
            }
        }
    }
</script>
