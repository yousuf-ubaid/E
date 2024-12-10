<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('erp_item_master');
echo head_page($title, false);
$uom_arr = all_umo_new_drop();
$revenue_gl_arr = all_revenue_gl_drop();
$cost_gl_arr = all_cost_gl_drop();
$asset_gl_arr = all_asset_gl_drop();
$fetch_cost_account = fetch_cost_account();
$fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
$fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
$stock_adjustment = stock_adjustment_control_drop();
/*echo head_page('Item Master', false);*/
$main_category_arr = all_main_category_drop();
$usergroupcompanywiseallow = getPolicyValuesgroup('ITM','All');
?>
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .fc {
        height: 22px !important;
        width: 100% !important;
        display: inline !important;
        margin: 0px !important;
    }

    .arrowDown {
        vertical-align: sub;
        color: rgb(75, 138, 175);
        font-size: 13px;
    }

    .applytoAll {
        display: none;
        vertical-align: top;
    }
</style>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-6">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="glyphicon glyphicon-stop"
                          style="color:green; font-size:15px;"> </span><?php echo $this->lang->line('common_active'); ?>
                </td><!--Active-->
                <td><span class="glyphicon glyphicon-stop"
                          style="color:red; font-size:15px;"> </span><?php echo $this->lang->line('common_in_active'); ?>
                </td><!--Inactive-->
            </tr>
        </table>
    </div>
    <!--<div class="col-md-2">
        <button type="button" class="btn btn-primary pull-right hide"
                onclick="uploadExcelItemMaster()"><i class="fa fa-plus"></i>
            Bulk Insert
        </button>

    </div>-->
    <div class="col-md-6">

        <?php if($usergroupcompanywiseallow == 0){?>
            <button type="button" class="btn btn-primary hidden"
                    onclick="createcustomer(1)"><i class="fa fa-plus"></i>
                Bulk Upload
            </button>

        <?php } else if ($usergroupcompanywiseallow != 0) { ?>
            <button type="button" class="btn btn-primary hidden"
                    onclick="uploadExcelItemMasterServerdidemodel()"><i class="fa fa-plus"></i>
                <?php echo $this->lang->line('common_bulk_upload')?><!--Bulk Upload-->
            </button>

        <?php }?>


        <button type="button" class="btn btn-success hidden"
                onclick="openAttributeAssignModal()"><i class="fa fa-plus"></i>
            <?php echo $this->lang->line('common_add_attributes')?><!--Add Attributes-->
        </button>


        <?php if($usergroupcompanywiseallow == 0){?>
            <button type="button" class="btn btn-primary"
                    onclick="createcustomer()"><i class="fa fa-plus"></i>
                <?php echo $this->lang->line('transaction_create_item'); ?>
            </button>
        <?php } else if ($usergroupcompanywiseallow != 0) { ?>
            <button type="button" class="btn btn-primary"
                    onclick="fetchPage('system/item/erp_item_new_codification',null,'Add New Item','SUP');"><i class="fa fa-plus"></i>
                <?php echo $this->lang->line('transaction_create_item'); ?>
            </button>
        <?php }?>

    </div>
</div>
<br>
<div class="row">
    <!--<div class="form-group col-sm-3 hidden">
        <?php /*echo form_dropdown('defaultUnitOfMeasureIDs[]', $uom_arr, 'Each', 'id="uomdrp" class="form-control defaultUnitOfMeasureIDs" required'); */?>
    </div>
    <div class="form-group col-sm-3 hidden">
        <?php /*echo form_dropdown('revanueGLAutoIDs[]', $revenue_gl_arr, '', 'id="revgldrp" class="form-control select2 revanueGLAutoIDs " '); */?>
    </div>
    <div class="form-group col-sm-3 hidden">
        <?php /*echo form_dropdown('costGLAutoIDs[]', $cost_gl_arr, '', ' id="costgldrp" class="form-control select2 costGLAutoIDs " '); */?>
    </div>
    <div class="form-group col-sm-3 hidden">
        <?php /*echo form_dropdown('assteGLAutoIDs[]', $asset_gl_arr, $this->common_data['controlaccounts']['INVA'], 'id="assetgldrp" class="form-control select2 assteGLAutoIDs " '); */?>
    </div>
    <div class="form-group col-sm-3 hidden">
        <?php /*echo form_dropdown('COSTGLCODEdess[]', $fetch_cost_account, '', 'id="COSTGLCODEdesdrp" class="form-control form1 select2 COSTGLCODEdess "'); */?>
    </div>
    <div class="form-group col-sm-3 hidden">
        <?php /*echo form_dropdown('ACCDEPGLCODEdess[]', $fetch_cost_account, '', 'id="ACCDEPGLCODEdesdrp" class="form-control form1 select2 ACCDEPGLCODEdess" '); */?>
    </div>
    <div class="form-group col-sm-3 hidden">
        <?php /*echo form_dropdown('DEPGLCODEdess[]', $fetch_dep_gl_code, '', 'id="DEPGLCODEdesdrp" class="form-control form1 select2 DEPGLCODEdess "  '); */?>
    </div>
    <div class="form-group col-sm-3 hidden">
        <?php /*echo form_dropdown('DISPOGLCODEdess[]', $fetch_disposal_gl_code, '', 'id="DISPOGLCODEdesdrp" class="form-control form1 select2 DISPOGLCODEdess "'); */?>
    </div>
    <div class="form-group col-sm-3 hidden">
        <?php /*echo form_dropdown('stockadjusts[]', $stock_adjustment, '', 'id="stockadjustdrp" class="form-control form1 select2 stockadjusts " '); */?>
    </div>-->

    <div class="form-group col-sm-3">
        <label> <?php echo $this->lang->line('transaction_main_category'); ?> </label><!--Main Category-->
        <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="LoadMainCategory()"'); ?>
    </div>
    <div class="form-group col-sm-3">
        <label><?php echo $this->lang->line('transaction_sub_category'); ?> </label><!--Sub Category-->
        <select name="subcategoryID" id="subcategoryID" class="form-control searchbox" onchange="LoadSubSubCategory()">
            <option value=""><?php echo $this->lang->line('transaction_select_category'); ?> </option>
            <!--Select Category-->
        </select>
    </div>
    <div class="form-group col-sm-3">
        <label><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?> </label><!--Sub Sub Category-->
        <select name="subsubcategoryID" id="subsubcategoryID" class="form-control searchbox">
            <option value=""><?php echo $this->lang->line('transaction_select_category'); ?> </option>
            <!--Select Category-->
        </select>
    </div>
    <div class="col-sm-1" id="search_cancel" style="margin-top: 2%;">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
    </div>

</div>
<hr>

<div class="row" style="padding-left: 2%">
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#items" data-toggle="tab" onclick="Otable.draw()"><?php echo $this->lang->line('transaction_items');?></a></li>
        <li><a href="#deleted" data-toggle="tab" onclick="ODeltable.draw()"><?php echo $this->lang->line('erp_item_master_deleted_items');?></a></li>
    </ul>
</div>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="items">
        <div class="table-responsive">
            <table id="item_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('transaction_main_category'); ?></th>
                    <!--Main Category-->
                    <th style="min-width: 12%"><?php echo $this->lang->line('transaction_sub_category'); ?></th>
                    <!--Sub Category-->
                    <th style="min-width: 12%"><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?></th>
                    <!--Sub Sub Category-->
                    <th style="min-width: 12%">Code</th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('erp_item_master_secondary_code'); ?></th>
                    <th><?php echo $this->lang->line('common_description'); ?></th><!--Description-->
                    <!--Secondary Code-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('transaction_current_stock'); ?></th>
                    <!--Current Stock-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('erp_item_master_wac_cost'); ?></th><!--WAC Cost-->
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?></th><!--Status-->
                    <th style="min-width: 65px"><?php echo $this->lang->line('common_action'); ?></th><!--Action-->
                </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="tab-pane" id="deleted">
        <div class="table-responsive">
            <table id="deleted_item_table" class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('transaction_main_category'); ?></th>
                    <!--Main Category-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('transaction_sub_category'); ?></th>
                    <!--Sub Category-->
                    <th style="min-width: 12%"><?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?></th>
                    <!--Sub Sub Category-->
                    <th style="min-width: 12%"><?php echo $this->lang->line('common_description'); ?></th><!--Description-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('erp_item_master_secondary_code'); ?></th>
                    <!--Secondary Code-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('transaction_current_stock'); ?></th>
                    <!--Current Stock-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('erp_item_master_wac_cost'); ?></th><!--WAC Cost-->
                    <th style="min-width: 5%"><?php echo $this->lang->line('common_status'); ?></th><!--Status-->
                </tr>
                </thead>
            </table>
        </div>
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
                <h4 class="modal-title"><?php echo $this->lang->line('erp_item_master_image_upload'); ?></h4>
                <!--Image Upload-->
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
                            <span
                                class="fileinput-new"><?php echo $this->lang->line('erp_item_master_select_image'); ?></span>
                            <!--Select image-->
                            <span class="fileinput-exists"><?php echo $this->lang->line('common_change'); ?></span>
                            <!--Change-->
                            <input type="file" name="img_file" onchange="img_uplode()">
                        </span>
                                <a href="#" class="btn btn-default fileinput-exists"
                                   data-dismiss="fileinput"><?php echo $this->lang->line('transaction_remove'); ?></a>
                                <!--Remove-->
                            </div>
                        </div>
                    </form>
                </center>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="attribute_assign_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Assign Attributes</h5>
            </div>
            <div class="modal-body">
                <form role="form" id="attribute_assign_form" class="form-horizontal">

                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="saveAssignedAttributes()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="excelUpload_Modal" style="z-index:10000000;"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Item Master upload form</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('', 'id="itemMasterUpload_form" class="form-inline"'); ?>
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="form-group">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                 style="margin-top: 8px;">
                                <div class="form-control" data-trigger="fileinput" style="min-width: 200px; width: 100%;
                                    border-bottom-left-radius: 3px !important; border-top-left-radius: 3px !important; ">
                                    <i class="glyphicon glyphicon-file color fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span>
                                    <span class="fileinput-exists"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></span>
                                    <input type="file" name="excelUpload_file" id="excelUpload_file" accept=".csv">
                                </span>
                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id" data-dismiss="fileinput">
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-default" onclick="excel_upload()">
                            <span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="col-sm-12" style="margin-left: 3%; color: red">
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg1'); ?><br/>
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg2'); ?>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
            </div>
            <form role="form" id="downloadTemplate_form">
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="excelSave_Modal_server" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " style="width: 100%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title" id="myModalLabel">Item Master Bulk Upload</h4>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label> <?php echo $this->lang->line('transaction_main_category'); ?> </label><!--Main Category-->
                        <?php echo form_dropdown('mainCategoryIDselect', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryIDselect" onchange="uploadExcelItemMasterServerdide()"'); ?>
                    </div>
                    <div class="form-group col-sm-1">
                        <br>
                        <button type="button" class="btn btn-success"
                                onclick="uploadExcel()"><i class="fa fa-file-excel-o"></i>
                            Upload
                        </button>
                    </div>
                    <div class="form-group col-sm-1">
                        <br>
                        <button type="button" class="btn btn-primary"
                                onclick="downloadExcel()"><i class="fa fa-arrow-circle-down"></i>
                            Download
                        </button>
                    </div>
                    <div class="form-group col-sm-1">
                        <br>
                        <button type="button" class="btn btn-warning"
                                onclick="Cleartemp()"><i class="fa fa-times"></i>
                            Clear
                        </button>
                    </div>
                </div>

                <br>
                <br>
                <form role="form" id="bulkSaveItem_form_server">
                    <!--<div class="fixHeader_Div" style="max-width: 100%; height: 400px">-->
                    <div class="row" >
                    <div class="col-sm-12" >
                    <div class="table-responsive" >
                        <table class="<?php echo table_class(); ?>" id="itemmultipletableserver" >
                            <thead>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>Main Category</td>
                                    <td>Sub Category<?php required_mark(); ?></td>
                                    <td>Sub Sub Category</td>
                                    <td>Short Description<?php required_mark(); ?></td>
                                    <td>Long Description<?php required_mark(); ?></td>
                                    <td>Secondary Code<?php required_mark(); ?></td>
                                    <td>Unit of Measure<?php required_mark(); ?></td>
                                    <td>Selling Price<?php required_mark(); ?></td>
                                    <td>Barcode</td>
                                    <td>Part No</td>
                                    <td class="cls_maximunQty">Maximum Qty</td>
                                    <td class="cls_minimumQty">Minimum Qty</td>
                                    <td class="cls_reorderPoint">Reorder Level</td>
                                    <td class="inventry_row_div" style="z-index: 100000;">Revenue GL Code</td>
                                    <td class="inventry_row_div" style="z-index: 100000;">Cost GL Code</td>
                                    <td class="inventry_row_div assetGlCode_div" style="z-index: 100000;">Asset GL Code</td>
                                    <td class="fixed_row_div " style="z-index: 100000;">Cost Account</td>
                                    <td class="fixed_row_div " style="z-index: 100000;">Acc Dep GL Code</td>
                                    <td class="fixed_row_div " style="z-index: 100000;">Dep GL Code</td>
                                    <td class="fixed_row_div " style="z-index: 100000;">Disposal GL Code</td>
                                    <td class="stockadjustment" style="z-index: 100000;">Stock Adjustment GL Code</td>
                                    <td >&nbsp;</td>
                                </tr>
                            </thead>

                        </table>
                </div>
                </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
                <button class="btn btn-primary" type="button" onclick="saveMultipleItemMaster()"><?php echo $this->lang->line('common_save'); ?><!--Save changes--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="item_pricing_report_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " style="width: 60%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title">Item : <span id="itemNameSpan"></span></h4>

            </div>
            <div class="modal-body">
                <div id="iteminquiryrpt" style="color: cornflowerblue; font-size: 1.1em; text-align: center;"></div>
                <table class="<?php echo table_class(); ?>" id="itempriceingPOtbl" >
                    <thead>
                        <tr>
                            <th colspan="8" style="text-align: left;font-size: 10px;">From Purchase Order</th>
                        </tr>
                        <tr>
                            <td>Doc Number</td>
                            <td>Doc Date</td>
                            <td>Supplier</td>
                            <td>Qty</td>
                            <td>UOM</td>
                            <td>Currency</td>
                            <td>Unit Cost</td>
                            <td>Total Cost</td>
                        </tr>
                    </thead>
                    <tbody id="itempriceingPObody">
                    <tr>
                        <td colspan="8">No Records Found</td>
                    </tr>
                    </tbody>

                </table>
                <br>
                <table class="<?php echo table_class(); ?>" id="itempriceingGRVtbl" >
                    <thead>
                    <tr>
                        <th colspan="8" style="text-align: left;font-size: 10px;">From Direct GRV</th>
                    </tr>
                    <tr>
                        <td>Doc Number</td>
                        <td>Doc Date</td>
                        <td>Supplier</td>
                        <td>Qty</td>
                        <td>UOM</td>
                        <td>Currency</td>
                        <td>Unit Cost</td>
                        <td>Total Cost</td>
                    </tr>
                    </thead>
                    <tbody id="itempriceingGRVbody">
                    <tr>
                        <td colspan="8">No Records Found</td>
                    </tr>
                    </tbody>

                </table>
                <br>
                <table class="<?php echo table_class(); ?>" id="itempriceingBSItbl" >
                    <thead>
                    <tr>
                        <th colspan="8" style="text-align: left;font-size: 10px;">From Direct Supplier Invoice</th>
                    </tr>
                    <tr>
                        <td>Doc Number</td>
                        <td>Doc Date</td>
                        <td>Supplier</td>
                        <td>Qty</td>
                        <td>UOM</td>
                        <td>Currency</td>
                        <td>Unit Cost</td>
                        <td>Total Cost</td>
                    </tr>
                    </thead>
                    <tbody id="itempriceingBSIbody">
                    <tr>
                        <td colspan="8">No Records Found</td>
                    </tr>
                    </tbody>

                </table>
                <br>
                <table class="<?php echo table_class(); ?>" id="itempriceingPVtbl" >
                    <thead>
                    <tr>
                        <th colspan="8" style="text-align: left;font-size: 10px;">From Payment Voucher</th>
                    </tr>
                    <tr>
                        <td>Doc Number</td>
                        <td>Doc Date</td>
                        <td>Supplier</td>
                        <td>Qty</td>
                        <td>UOM</td>
                        <td>Currency</td>
                        <td>Unit Cost</td>
                        <td>Total Cost</td>
                    </tr>
                    </thead>
                    <tbody id="itempriceingPVbody">
                    <tr>
                        <td colspan="8">No Records Found</td>
                    </tr>
                    </tbody>

                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
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
    var ODeltable;
    var Otables;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/item/erp_item_master_codification', 'Test', 'Item Master');
        });
        item_table();
        deleted_item_table();

        $("#subcategoryID").change(function () {
            Otable.draw();
            ODeltable.draw();
        });

        $("#subsubcategoryID").change(function () {
            Otable.draw();
            ODeltable.draw();
        });
        $('.select2').select2();

        $(".tdCol").hover(function (eventObject) {
            $(".applytoAll").hide();
            $(this).closest('td').find('span').show()
        });

        $('#itemmultipletableserver').tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });

    });

    function LoadMainCategory() {
        $('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        load_sub_cat();
        Otable.draw();
        ODeltable.draw();
    }

    function LoadSubSubCategory() {
        //$('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        //$('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        load_itemMaster_subsubCategory();
        Otable.draw();
        ODeltable.draw();
    }

    function item_table() {
        Otable = $('#item_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('ItemMaster/fetch_item_codification'); ?>",
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
                {"mData": "SubSubCategoryDescription"},
                {"mData": "itemSystemCode"},
                {"mData": "seconeryItemCode"},
                {"mData": "description"},
                {"mData": "CurrentStock"},
                {"mData": "TotalWacAmount"},
                {"mData": "confirmed"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [0], "searchable": false},{"targets": [2, 3], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#mainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});
                aoData.push({"name": "subsubcategoryID", "value": $("#subsubcategoryID").val()});
                aoData.push({"name": "deletedYN", "value": 0});
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

    function deleted_item_table() {
        ODeltable = $('#deleted_item_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('ItemMaster/fetch_item'); ?>",
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
                {"mData": "SubSubCategoryDescription"},
                {"mData": "description"},
                {"mData": "seconeryItemCode"},
                {"mData": "CurrentStock"},
                {"mData": "TotalWacAmount"},
                {"mData": "confirmed"},
            ],
            "columnDefs": [{"targets": [2, 3], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#mainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});
                aoData.push({"name": "subsubcategoryID", "value": $("#subsubcategoryID").val()});
                aoData.push({"name": "deletedYN", "value": 1});
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
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again.*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_item_master(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
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
                        Otable.draw();
                        ODeltable.draw();
                        // item_table();
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
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
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

    function load_itemMaster_subsubCategory(select_val) {
        $('#subsubcategoryID').val("");
        $('#subsubcategoryID option').remove();
        var subsubid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subsubcategoryID').empty();
                    var mySelect = $('#subsubcategoryID');
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

    function clearSearchFilter(){
        $('#mainCategoryID').val("");
        $('#subcategoryID').val("");
        $('#subsubcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subsubcategoryID option').remove();
        $('#subcategoryID').append($('<option>', {value: '', text: 'Select Sub Category'}));
        $('#subsubcategoryID').append($('<option>', {value: '', text: 'Select Sub Category'}));
        Otable.draw();
        ODeltable.draw();
    }


    function openAttributeAssignModal(){
        var id=0;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'expenseClaimDetailsID': id},
            url: "<?php echo site_url('AttributeAssign/get_attributes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#attribute_assign_form').empty();
                $('#attribute_assign_form').html(data);
                $('#attribute_assign_modal').modal('show');
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();

            }
        });
    }

    function saveAssignedAttributes(){
        $.ajax({
            async: true,
            type: 'post',
            data: $("#attribute_assign_form").serialize(),
            dataType: "json",
            url: "<?php echo site_url('AttributeAssign/save_assigned_attributes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#attribute_assign_modal').modal('hide');
                attribute_assign_table();
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();

            }
        });
    }

    function uploadExcelItemMaster(){
        $('#itemmultipletable tbody tr').not(':first').remove();
        $('#bulkSaveItem_form')[0].reset();
        $('.companyLocalSellingPrice').val(0);
        $('.subcategoryID').val('').change();
        $('.mainCategoryID').val('').change();
        $('.costGLAutoID').val('').change();
        $('.revanueGLAutoID').val('').change();
        $('.assteGLAutoID').val('').change();
        $('.stockadjust').val('').change();
        number_validation();
        $('.mainCategoryID').closest('tr').css("background-color", 'white');
        $('#excelSave_Modal').modal('show');
    }

    /*function excel_upload(){
        var formData = new FormData($("#itemMasterUpload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "*/<?php // echo site_url('ItemMaster/item_master_excelUpload'); ?>/*",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's' || data[0] == 'e') {
                    myAlert(data[0], data[1]);
                }

                if (data[0] == 'm') {
                    $('#excelUpload_Modal').modal('hide');
                    //$('#excelSave_Modal').modal('show');
                    if (!jQuery.isEmptyObject(data[1])) {
                        //return false;
                        $('#itemmultipletable tbody').empty();

                        var mnct = $('#mainCategoryID').html();
                        var mainCat = '<select name="mainCategoryID[]" class="form-control mainCategoryID" onchange="load_sub_cat_bulk(this)">'+mnct+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCols(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';

                        var uomdrp = $('#uomdrp').html();
                        var uom = '<select name="defaultUnitOfMeasureID[]" class="form-control defaultUnitOfMeasureID">'+uomdrp+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsUOM(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';

                        var revgldrp = $('#revgldrp').html();
                        var revgl = '<select name="revanueGLAutoID[]" class="form-control select2 revanueGLAutoID">'+revgldrp+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllrevanueGLAutoID(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';

                        var costgldrp = $('#costgldrp').html();
                        var cstgl = '<select name="costGLAutoID[]" class="form-control select2 costGLAutoID">'+costgldrp+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllcostGLAutoID(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';

                        var assetgldrp = $('#assetgldrp').html();
                        var astgl = '<select name="assteGLAutoID[]" class="form-control select2 assteGLAutoID">'+assetgldrp+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllassteGLAutoID(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';

                        var COSTGLCODEdesdrp = $('#COSTGLCODEdesdrp').html();
                        var COSTGLCODEgl = '<select name="COSTGLCODEdes[]" class="form-control form1 select2 COSTGLCODEdes">'+COSTGLCODEdesdrp+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllCOSTGLCODEdes(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';

                        var ACCDEPGLCODEdesdrp = $('#ACCDEPGLCODEdesdrp').html();
                        var ACCDEPGLCODEgl = '<select name="ACCDEPGLCODEdes[]" class="form-control form1 select2 ACCDEPGLCODEdes">'+ACCDEPGLCODEdesdrp+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllACCDEPGLCODEdes(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';

                        var DEPGLCODEdesdrp = $('#DEPGLCODEdesdrp').html();
                        var DEPGLCODEgl = '<select name="DEPGLCODEdes[]" class="form-control form1 select2 DEPGLCODEdes">'+DEPGLCODEdesdrp+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllDEPGLCODEdes(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';

                        var DISPOGLCODEdrp = $('#DISPOGLCODEdesdrp').html();
                        var DISPOGLCODEgl = '<select name="DISPOGLCODEdes[]" class="form-control form1 select2 DISPOGLCODEdes">'+DISPOGLCODEdrp+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllDISPOGLCODEdes(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';

                        var stockadjustdrp = $('#stockadjustdrp').html();
                        var stockadjustgl = '<select name="stockadjust[]" class="form-control form1 select2 stockadjust">'+stockadjustdrp+'</select> <span class="applytoAll"> <button class="btn btn-xs btn-default" type="button" onclick="applyToAllstockadjust(this)"> <i class="fa fa-arrow-circle-o-down"></i></button> </span>';
                        var x=0;
                        $.each(data[1], function (key, value) {
                            if(x == 100){
                                return false;
                            }
                            $('#itemmultipletable tbody').append('<tr class="commonrow'+x+'">&nbsp;<td><td class="main-cat tdCol"></td> <td class="tdCol"><select name="subcategoryID[]" class="form-control subcategoryID searchbox"onchange="load_sub_sub_cat_bulk(this),load_gl_codes(this)"> <option value="">Select Category</option></select><span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsSubCat(this)"> <i class="fa fa-arrow-circle-o-down"></i></button></span></td> <td class="tdCol"><select name="subSubCategoryID[]" class="form-control subSubCategoryID searchbox"><option value="">Select Category</option></select> <span class="applytoAll"><button class="btn btn-xs btn-default" type="button" onclick="applyToAllColsSubSubCat(this)"> <i class="fa fa-arrow-circle-o-down"></i></button></span></td> <td><input type="text" class="form-control itemName" value="' + value['ShortDescription'] + '" name="itemName[]"></td><td><input type="text" class="form-control itemDescription" value="' + value['LongDescription'] + '" name="itemDescription[]"></td><td><input type="text" class="form-control seconeryItemCode" value="' + value['SecondaryCode'] + '" name="seconeryItemCode[]"></td><td class="uom-drp tdCol"> </td><td><input type="text" step="any" class="form-control companyLocalSellingPrice number" value="' + value['SellingPrice'] + '"  name="companyLocalSellingPrice[]" value="0"></td><td><input type="text" class="form-control barcode" value="' + value['Barcode'] + '" name="barcode[]"></td><td><input type="text" class="form-control partno" value="' + value['PartNo'] + '" name="partno[]"></td><td><input type="text" class="form-control number maximunQty cls_maximunQty" value="' + value['MaximumQty'] + '" name="maximunQty[]"></td><td><input type="text" value="' + value['minimumQty'] + '" class="form-control number minimumQty cls_minimumQty"  name="minimumQty[]"></td><td><input type="text" class="form-control number reorderPoint cls_reorderPoint" value="' + value['ReorderLevel'] + '" name="reorderPoint[]"></td><td class="inventry_row_div tdCol rev-drp"> </td><td class="inventry_row_div tdCol cst-drp"> </td><td class="inventry_row_div tdCol assetGlCode_div ast-drp"> </td><td class="fixed_row_div tdCol COSTGLCODEgl-drp hide"> </td><td class="fixed_row_div tdCol ACCDEPGLCODEgl-drp hide"> </td><td class="fixed_row_div tdCol DEPGLCODEgl-drp hide"> </td><td class="fixed_row_div tdCol DISPOGLCODEgl-drp hide"> </td><td class="stockadjustment tdCol stockadjustgl-drp"> </td></tr>');


                            $('#itemmultipletable tr:last').find('.main-cat').html(mainCat);
                            $('#itemmultipletable tr:last').find('.uom-drp').html(uom);
                            $('#itemmultipletable tr:last').find('.rev-drp').html(revgl);
                            $('#itemmultipletable tr:last').find('.cst-drp').html(cstgl);
                            $('#itemmultipletable tr:last').find('.ast-drp').html(astgl);
                            $('#itemmultipletable tr:last').find('.COSTGLCODEgl-drp').html(COSTGLCODEgl);
                            $('#itemmultipletable tr:last').find('.ACCDEPGLCODEgl-drp').html(ACCDEPGLCODEgl);
                            $('#itemmultipletable tr:last').find('.DEPGLCODEgl-drp').html(DEPGLCODEgl);
                            $('#itemmultipletable tr:last').find('.DISPOGLCODEgl-drp').html(DISPOGLCODEgl);
                            $('#itemmultipletable tr:last').find('.stockadjustgl-drp').html(stockadjustgl);
                            x++;

                            $(".tdCol").hover(function (eventObject) {
                                $(".applytoAll").hide();
                                $(this).closest('td').find('span').show()
                            })

                        });

                        $('.subcategoryID').val('');
                        $('.mainCategoryID').val('');
                        /!*$('.costGLAutoID').val('').change();
                        $('.revanueGLAutoID').val('').change();
                        $('.assteGLAutoID').val('').change();
                        $('.stockadjust').val('').change();*!/

                        $('.select2').select2();
                    }

                }

                if (data[0] == 's') {
                    $('#excelUpload_Modal').modal('hide');

                    setTimeout(function(){
                        loadDetail_table()
                    }, 300);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }*/
    /*function load_sub_cat_bulk(des) {
        changeFormCode(des);
        //$('#subcategoryID').val("");
        $(des).closest('tr').find('.subcategoryID').val('');
        //$('#subcategoryID option').remove();
        $(des).closest('tr').find('.subcategoryID option').remove();
        //$('#subSubCategoryID').val("");
        $(des).closest('tr').find('.subSubCategoryID').val("");
        //$('#subSubCategoryID option').remove();
        $(des).closest('tr').find('.subSubCategoryID option').remove();
        var subid = $(des).val();
        $.ajax({
            type: 'POST',
            url: '*/<?php // echo site_url("ItemMaster/load_subcat"); ?>/*',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    //$('#subcategoryID').empty();
                    $(des).closest('tr').find('.subcategoryID').empty();
                    //var mySelect = $('#subcategoryID');
                    var mySelect = $(des).closest('tr').find('.subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }*/

    function load_sub_cat_bulk(des) {
        //changeFormCode(des);
        //$('#subcategoryID').val("");
        $('.subcategoryID').val('');
        //$('#subcategoryID option').remove();
        $('.subcategoryID option').remove();
        //$('#subSubCategoryID').val("");
        $('.subSubCategoryID').val("");
        //$('#subSubCategoryID option').remove();
        $('.subSubCategoryID option').remove();
        var subid = $(des).val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    //$('#subcategoryID').empty();
                    $('.subcategoryID').empty();
                    //var mySelect = $('#subcategoryID');
                    var mySelect = $('.subcategoryID');
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

    function changeFormCode(des) {
        //itemCategoryID = $('#mainCategoryID').val();
       var itemCategoryID = $(des).val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_category_type_id"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    alert(data['categoryTypeID']);
                    if ((data['categoryTypeID'] == 2) || (data['categoryTypeID'] == 4)) {
                        /*$("#assetGlCode_div").addClass("hide");
                        $("#cls_maximunQty").addClass("hide");
                        $("#cls_minimumQty").addClass("hide");
                        $("#cls_reorderPoint").addClass("hide");
                        $("#stockadjustment").addClass("hide");*/
                        $(des).closest('tr').find('.assetGlCode_div').show();
                        $(des).closest('tr').find('.cls_maximunQty').show();
                        $(des).closest('tr').find('.cls_minimumQty').show();
                        $(des).closest('tr').find('.cls_reorderPoint').show();
                        $(des).closest('tr').find('.stockadjustment').show();

                    } else {
                        /*$("#assetGlCode_div").removeClass("hide");
                        $("#cls_maximunQty").removeClass("hide");
                        $("#cls_minimumQty").removeClass("hide");
                        $("#cls_reorderPoint").removeClass("hide");
                        $("#stockadjustment").removeClass("hide");*/
                        $(des).closest('tr').find('.assetGlCode_div').hide();
                        $(des).closest('tr').find('.cls_maximunQty').hide();
                        $(des).closest('tr').find('.cls_minimumQty').hide();
                        $(des).closest('tr').find('.cls_reorderPoint').hide();
                        $(des).closest('tr').find('.stockadjustment').hide();
                    }
                    if (data['categoryTypeID'] == 3) {
                        /*$("#inventry_row_div").addClass("hide");
                        $("#fixed_row_div").removeClass("hide");
                        $("#stockadjustment").addClass("hide");*/
                        $(des).closest('tr').find('.inventry_row_div').hide();
                        $(des).closest('tr').find('.fixed_row_div').show();
                        $(des).closest('tr').find('.stockadjustment').hide();

                    } else {
                        /*$("#inventry_row_div").removeClass("hide");
                        $("#fixed_row_div").addClass("hide");*/
                        $(des).closest('tr').find('.inventry_row_div').show();
                        $(des).closest('tr').find('.fixed_row_div').hide();
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function load_sub_sub_cat_bulk(det) {
        //$('#subSubCategoryID option').remove();
        $(det).closest('tr').find('.subSubCategoryID option').remove();
        //$('#subSubCategoryID').val("");
        $(det).closest('tr').find('.subSubCategoryID').val("");
        //var subsubid = $('#subcategoryID').val();
        var subsubid = $(det).closest('tr').find('.subcategoryID').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    //$('#subSubCategoryID').empty();
                    $(det).closest('tr').find('.subSubCategoryID').empty();
                    //var mySelect = $('#subSubCategoryID');
                    var mySelect = $(det).closest('tr').find('.subSubCategoryID');
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

    function load_gl_codes(det) {
        //$('#revanueGLAutoID').val("");
        $(det).closest('tr').find('.revanueGLAutoID').val("");
        //$('#costGLAutoID').val("");
        $(det).closest('tr').find('.costGLAutoID').val("");
        //$('#stockadjust').val("");
        $(det).closest('tr').find('.stockadjust').val("");
        //$('#assteGLAutoID').val("");
        $(det).closest('tr').find('.assteGLAutoID').val("");
        //$('#COSTGLCODEdes').val("");
        $(det).closest('tr').find('.COSTGLCODEdes').val("");
        //$('#ACCDEPGLCODEdes').val("");
        $(det).closest('tr').find('.ACCDEPGLCODEdes').val("");
        //$('#DEPGLCODEdes').val("");
        $(det).closest('tr').find('.DEPGLCODEdes').val("");
        //$('#DISPOGLCODEdes').val("");
        $(det).closest('tr').find('.DISPOGLCODEdes').val("");
        //itemCategoryID = $('#subcategoryID').val();
        itemCategoryID = $(det).closest('tr').find('.subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_gl_codes"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    //$("#revanueGLAutoID").val(data['revenueGL']).change();
                    $(det).closest('tr').find('.revanueGLAutoID').val(data['revenueGL']).change();
                    //$("#costGLAutoID").val(data['costGL']).change();
                    $(det).closest('tr').find('.costGLAutoID').val(data['costGL']).change();
                    //$("#assteGLAutoID").val(data['assetGL']).change();
                    $(det).closest('tr').find('.assteGLAutoID').val(data['assetGL']).change();
                    //$("#COSTGLCODEdes").val(data['faCostGLAutoID']).change();
                    $(det).closest('tr').find('.COSTGLCODEdes').val(data['faCostGLAutoID']).change();
                    //$("#ACCDEPGLCODEdes").val(data['faACCDEPGLAutoID']).change();
                    $(det).closest('tr').find('.ACCDEPGLCODEdes').val(data['faACCDEPGLAutoID']).change();
                    //$("#DEPGLCODEdes").val(data['faDEPGLAutoID']).change();
                    $(det).closest('tr').find('.DEPGLCODEdes').val(data['faDEPGLAutoID']).change();
                    //$("#DISPOGLCODEdes").val(data['faDISPOGLAutoID']).change();
                    $(det).closest('tr').find('.DISPOGLCODEdes').val(data['faDISPOGLAutoID']).change();
                    //$("#stockadjust").val(data['stockAdjustmentGL']).change();
                    $(det).closest('tr').find('.stockadjust').val(data['stockAdjustmentGL']).change();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function add_more() {
        $('select.select2').select2('destroy');
        var rowid=$('#itemmultipletable tbody tr:last').closest('tr').attr('class');
        var res = rowid.split("commonrow");
        var cls=parseInt(res[1])+1;
        var appendData = $('#itemmultipletable tbody tr:first').clone();
        appendData.find('.itemName').val('');
        appendData.find('.itemDescription').val('');
        appendData.find('.seconeryItemCode').val('');
        appendData.find('.barcode').val('');
        appendData.find('.partno').val('');
        appendData.find('.maximunQty').val('');
        appendData.find('.minimumQty').val('');
        appendData.find('.reorderPoint').val('');
        //appendData.find('.mainCategoryID').val('').change();
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#itemmultipletable').append(appendData);
        var lenght = $('#itemmultipletable tbody tr').length - 1;
        $('#itemmultipletable tbody tr:last').closest('tr').css("background-color", 'white');
        $('#itemmultipletable tbody tr:last').closest('tr').removeClass("commonrow0");
        $('#itemmultipletable tbody tr:last').closest('tr').addClass("commonrow"+cls);
        $(".select2").select2();
        $(".tdCol").hover(function (eventObject) {
            $(".applytoAll").hide();
            $(this).closest('td').find('span').show()
        })
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function saveMultipleItemMaster(){

        var data = $('#bulkSaveItem_form_server').serializeArray();

        var count = 0 ;
            $('select[name="revanueGLAutoID[]"] option:selected').each(function () {
                data.push({'name': 'revanue[]', 'value': $(this).text()})
            });
            $('select[name="costGLAutoID[]"] option:selected').each(function () {
                data.push({'name': 'cost[]', 'value': $(this).text()})
            });
            $('select[name="assteGLAutoID[]"] option:selected').each(function () {
                data.push({'name': 'asste[]', 'value': $(this).text()})
            });
            $('select[name="mainCategoryID[]"] option:selected').each(function () {
                data.push({'name': 'mainCategory[]', 'value': $(this).text()})
            });
            $('select[name="defaultUnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            });
            $('select[name="stockadjust[]"] option:selected').each(function () {
                data.push({'name': 'stockadjustment[]', 'value': $(this).text()})
            });
           /* $('.mainCategoryID').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    count=count+1;
                }
            });*/
            data.push({'name': 'mainCategoryIDselect', 'value': $('#mainCategoryIDselect').val()})
            $('.defaultUnitOfMeasureID').each(function () {
                if (this.value == '' || this.value == 0) {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    count=count+1;
                }else{
                    $(this).closest('tr').css("background-color", 'white');
                }
            });
            $('.seconeryItemCode').each(function () {
                if (this.value == '' || this.value == 0) {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    count=count+1;
                }else{
                    $(this).closest('tr').css("background-color", 'white');
                }
            });
            $('.itemName').each(function () {
                    if (this.value == '' || this.value == 0) {
                        $(this).closest('tr').css("background-color", '#ffb2b2 ');
                        count=count+1;
                    }
            });
            $('.itemDescription').each(function () {
                    if (this.value == '' || this.value == 0) {
                        $(this).closest('tr').css("background-color", '#ffb2b2 ');
                        count=count+1;
                    }else{
                        $(this).closest('tr').css("background-color", 'white');
                    }
            });
            $('.subcategoryID').each(function () {
                if (this.value == '' || this.value == 0) {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    count=count+1;
                }else{
                    $(this).closest('tr').css("background-color", 'white');
                }
            });
        if(count==0){
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('ItemMaster/saveMultipleItemMaster'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Otables.draw();
                            $('#itemmultipletable tbody').closest('tr').css("background-color", 'white');
                            //$('#excelSave_Modal_server').modal('hide');
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

    function uploadExcel(){
        $('#excelUpload_Modal').modal('show');
    }
    function clearDownArrow() {
        $(".applytoAll").hide();
    }

    function applyToAllCols(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var mainCategoryID = $(element).closest('tr').find('.mainCategoryID').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.mainCategoryID').val(mainCategoryID).change();
                   // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }

    function applyToAllColsSubCat(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var subcategoryID = $(element).closest('tr').find('.subcategoryID').val();
                var elementTr = $(element).closest('tr').index()+1;
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow"+i).find('.subcategoryID').val(subcategoryID).change();
                   // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
        }

    function applyToAllColsSubSubCat(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var subsubcategoryID = $(element).closest('tr').find('.subSubCategoryID').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.subSubCategoryID').val(subsubcategoryID).change();
                   // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }
    function applyToAllColsUOM(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var defaultUnitOfMeasureID = $(element).closest('tr').find('.defaultUnitOfMeasureID').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.defaultUnitOfMeasureID').val(defaultUnitOfMeasureID).change();
                   // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }

    function applyToAllrevanueGLAutoID(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var revanueGLAutoID = $(element).closest('tr').find('.revanueGLAutoID').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.revanueGLAutoID').val(revanueGLAutoID).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }

    function applyToAllcostGLAutoID(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var costGLAutoID = $(element).closest('tr').find('.costGLAutoID').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.costGLAutoID').val(costGLAutoID).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }

    function applyToAllassteGLAutoID(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var assteGLAutoID = $(element).closest('tr').find('.assteGLAutoID').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.assteGLAutoID').val(assteGLAutoID).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }

    function applyToAllCOSTGLCODEdes(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var COSTGLCODEdes = $(element).closest('tr').find('.COSTGLCODEdes').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.COSTGLCODEdes').val(COSTGLCODEdes).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }
    function applyToAllACCDEPGLCODEdes(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var ACCDEPGLCODEdes = $(element).closest('tr').find('.ACCDEPGLCODEdes').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.ACCDEPGLCODEdes').val(ACCDEPGLCODEdes).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }
    function applyToAllDEPGLCODEdes(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var DEPGLCODEdes = $(element).closest('tr').find('.DEPGLCODEdes').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.DEPGLCODEdes').val(DEPGLCODEdes).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }

    function applyToAllDISPOGLCODEdes(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var DISPOGLCODEdes = $(element).closest('tr').find('.DISPOGLCODEdes').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.DISPOGLCODEdes').val(DISPOGLCODEdes).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }
    function applyToAllstockadjust(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var stockadjust = $(element).closest('tr').find('.stockadjust').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.stockadjust').val(stockadjust).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }

    function applyToAllmaximunQty(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var maximunQty = $(element).closest('tr').find('.maximunQty').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.maximunQty').val(maximunQty).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }

    function applyToAllminimumQty(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var minimumQty = $(element).closest('tr').find('.minimumQty').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.minimumQty').val(minimumQty).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }

    function applyToAllreorderPoint(element) {
        swal({
                title: "Are you sure?",
                text: "You want to apply this to all",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirm"
            },
            function () {
                var reorderPoint = $(element).closest('tr').find('.reorderPoint').val();
                var elementTr = $(element).closest('tr').index();
                var elementTd = $(element).closest('td').index();
                var totalTr = $('#itemmultipletableserver tr').length - 1;
                for (var i = elementTr; i <= totalTr; i++) {
                    $(".commonrow" + i).find('.minimumQty').val(reorderPoint).change();
                    // var oldval = $(elementTd+i).find('.mainCategoryID').val(mainCategoryID).change();
                }
            });
    }
function uploadExcelItemMasterServerdidemodel(){
    $('#excelSave_Modal_server').modal('show');
    Otables.draw();
}

    function uploadExcelItemMasterServerdide(){
        var mainCategoryIDselect=$('#mainCategoryIDselect').val();
        if (!jQuery.isEmptyObject(mainCategoryIDselect)) {
            Otables = $('#itemmultipletableserver').DataTable({
                "language": {
                    "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                },
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                /*"bStateSave": true,*/
                "sAjaxSource": "<?php echo site_url('ItemMaster/fetch_item_master_server'); ?>",
                "aaSorting": [[0, 'asc']],
                "fnInitComplete": function () {

                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    var z = 1;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        //$('#common_'+z).addClass("commonrow"+z);
                        //$(this.row).addClass("commonrow"+z);
                        //$(this).closest('tr').addClass("commonrow"+z);
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass("commonrow"+z);
                        x++;
                        z++;
                    }
                    $('.select2').select2();
                    $('.assteGLAutoID').val('').change();
                    $('.fixed_row_div').hide();
                    changeFormCodeselect();
                    $(".tdCol").hover(function (eventObject) {
                        $(".applytoAll").hide();
                        $(this).closest('td').find('span').show()
                    });
                    //$("[name='itemchkbox']").bootstrapSwitch();
                },
                "aoColumns": [
                    {"mData": "itemAutoID"},
                    {"mData": "mainCategoryIDdrp"},
                    {"mData": "subcategoryIDdrp"},
                    {"mData": "subSubCategoryIDdrp"},
                    {"mData": "itemNamedrp"},
                    {"mData": "itemDescriptiondrp"},
                    {"mData": "seconeryItemCodedrp"},
                    {"mData": "defaultUnitOfMeasureIDdrp"},
                    {"mData": "companyLocalSellingPricedrp"},
                    {"mData": "barcodedrp"},
                    {"mData": "partNodrp"},
                    {"mData": "maximunQtydrp"},
                    {"mData": "minimumQtydrp"},
                    {"mData": "reorderPointdrp"},
                    {"mData": "revanueGLAutoIDdrp"},
                    {"mData": "costGLAutoIDdrp"},
                    {"mData": "assteGLAutoIDdrp"},
                    {"mData": "faCostGLAutoIDdrp"},
                    {"mData": "faACCDEPGLAutoIDdrp"},
                    {"mData": "faDEPGLAutoIDdrp"},
                    {"mData": "faDISPOGLAutoIDdrp"},
                    {"mData": "stockAdjustmentGLAutoIDdrp"},
                    {"mData": "itemAutoIDhn"}
                ],
                "columnDefs": [{"targets": [1], "orderable": false}, { className: "inventry_row_div tdCol", "targets": [14,15] },{ className: "inventry_row_div assetGlCode_div tdCol", "targets": [16] },{ className: "fixed_row_div tdCol", "targets": [17,18,19,20] },{ className: "stockadjustment tdCol", "targets": [17,18,19,21] },{ className: "cls_maximunQty tdCol", "targets": [11] },{ className: "cls_minimumQty tdCol", "targets": [12] },{ className: "cls_reorderPoint tdCol", "targets": [13] },{ className: "tdCol", "targets": [2,3,7] }],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    /* aoData.push({"name": "mainCategory", "value": $("#mainCategoryID").val()});
                     aoData.push({"name": "subcategory", "value": $("#subcategoryID").val()});
                     aoData.push({"name": "subsubcategoryID", "value": $("#subsubcategoryID").val()});*/
                    $.ajax({
                        'dataType': 'json',
                        'type': 'POST',
                        'url': sSource,
                        'data': aoData,
                        'success': fnCallback
                    });
                }
            });
        }else{
            //myAlert('w','Select Main Category ')
        }


    }

    function excel_upload(){
        var mainCategoryIDselect=$('#mainCategoryIDselect').val();
        if(mainCategoryIDselect){
            var formData = new FormData($("#itemMasterUpload_form")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('ItemMaster/item_master_excelUpload'); ?>",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#excelUpload_Modal').modal('hide');
                        $('#excelSave_Modal_server').modal('hide');
                        //Otables.draw();
                        setTimeout(function(){ uploadExcelItemMasterServerdidemodel() }, 1500);

                    }
                },
                error: function () {
                    stopLoad();
                    myAlert('e', 'error');
                }
            });
        }else{
            myAlert('w', 'Select Main Category')
        }

    }


    function changeFormCodeselect() {
        var itemCategoryID=$('#mainCategoryIDselect').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_category_type_id"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    //alert(data['categoryTypeID']);
                    if ((data['categoryTypeID'] == 2) || (data['categoryTypeID'] == 4)) {
                        /*$("#assetGlCode_div").addClass("hide");
                         $("#cls_maximunQty").addClass("hide");
                         $("#cls_minimumQty").addClass("hide");
                         $("#cls_reorderPoint").addClass("hide");
                         $("#stockadjustment").addClass("hide");*/
                        $('.assetGlCode_div').hide();
                        $('.cls_maximunQty').hide();
                        $('.cls_minimumQty').hide();
                        $('.cls_reorderPoint').hide();
                        $('.stockadjustment').hide();

                    } else {
                        /*$("#assetGlCode_div").removeClass("hide");
                         $("#cls_maximunQty").removeClass("hide");
                         $("#cls_minimumQty").removeClass("hide");
                         $("#cls_reorderPoint").removeClass("hide");
                         $("#stockadjustment").removeClass("hide");*/
                        $('.assetGlCode_div').show();
                        $('.cls_maximunQty').show();
                        $('.cls_minimumQty').show();
                        $('.cls_reorderPoint').show();
                        $('.stockadjustment').show();
                    }
                    if (data['categoryTypeID'] == 3) {
                        /*$("#inventry_row_div").addClass("hide");
                         $("#fixed_row_div").removeClass("hide");
                         $("#stockadjustment").addClass("hide");*/
                        $('.inventry_row_div').hide();
                        $('.fixed_row_div').show();
                        $('.stockadjustment').hide();

                    } else {
                        /*$("#inventry_row_div").removeClass("hide");
                         $("#fixed_row_div").addClass("hide");*/
                        $('.inventry_row_div').show();
                        $('.fixed_row_div').hide();
                    }
                    if(data['categoryTypeID'] == 2){
                        $('.assetGlCode_div').hide();
                    }
                    $('.mainCategoryID').val(itemCategoryID).change();
                    $('.mainCategoryID').attr('disabled',true);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function downloadExcel(){

        var form= document.getElementById('downloadTemplate_form');
        form.target='_blank';
        form.action='<?php echo site_url('ItemMaster/downloadExcel'); ?>';
        form.submit();
    }

    function Cleartemp(){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "You want to delete all records",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'itemAutoID': id},
                    url: "<?php echo site_url('ItemMaster/clear_temp_table'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        uploadExcelItemMasterServerdide()
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function item_pricing_report(id){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': id},
            url: "<?php echo site_url('ItemMaster/item_pricing_report'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#itempriceingPObody').empty();
                $('#itempriceingGRVbody').empty();
                $('#itempriceingBSIbody').empty();
                $('#itempriceingPVbody').empty();
                $('#itemNameSpan').empty();
                $('#iteminquiryrpt').html('')
                if (jQuery.isEmptyObject(data['PO'])) {
                    $('#itempriceingPOtbl').addClass('hidden');
                    $('#itempriceingPObody').append('<tr class="danger"><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    $('#itempriceingPOtbl').removeClass('hidden');
                    $.each(data['PO'], function (key, value) {
                        $('#itempriceingPObody').append('<tr><td><a style="cursor: pointer;" onclick="documentPageView_modal(\'PO\',' + value['docid'] + ')">' + value['purchaseOrderCode'] + '</a></td><td>' + value['documentDate'] + '</td><td>' + value['supplierName'] + '</td><td style="text-align: right;">' + value['qty'] + '</td><td>' + value['unitOfMeasure'] + '</td><td>' + value['transactionCurrency'] + '</td><td style="text-align: right;">' + parseFloat(value['unitcost']).toFixed(value['currencydecimal']) + '</td><td style="text-align: right;">' + parseFloat(value['totalcost']).toFixed(value['currencydecimal']) + '</td></tr>');
                    });
                }

                if (jQuery.isEmptyObject(data['GRV'])) {
                    $('#itempriceingGRVtbl').addClass('hidden');
                    $('#itempriceingGRVbody').append('<tr class="danger"><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    $('#itempriceingGRVtbl').removeClass('hidden');
                    $.each(data['GRV'], function (key, value) {
                        $('#itempriceingGRVbody').append('<tr><td><a style="cursor: pointer;" onclick="documentPageView_modal(\'GRV\',' + value['docid'] + ')">' + value['grvPrimaryCode'] + '</a></td><td>' + value['documentDate'] + '</td><td>' + value['supplierName'] + '</td><td style="text-align: right;">' + value['qty'] + '</td><td>' + value['unitOfMeasure'] + '</td><td>' + value['transactionCurrency'] + '</td><td style="text-align: right;">' + parseFloat(value['unitcost']).toFixed(value['currencydecimal']) + '</td><td style="text-align: right;">' + parseFloat(value['totalcost']).toFixed(value['currencydecimal']) + '</td></tr>');
                    });
                }

                if (jQuery.isEmptyObject(data['BSI'])) {
                    $('#itempriceingBSItbl').addClass('hidden');
                    $('#itempriceingBSIbody').append('<tr class="danger"><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    $('#itempriceingBSItbl').removeClass('hidden');
                    $.each(data['BSI'], function (key, value) {
                        $('#itempriceingBSIbody').append('<tr><td><a style="cursor: pointer;" onclick="documentPageView_modal(\'BSI\',' + value['docid'] + ')">' + value['bookingInvCode'] + '</a></td><td>' + value['documentDate'] + '</td><td>' + value['supplierName'] + '</td><td style="text-align: right;">' + value['qty'] + '</td><td>' + value['unitOfMeasure'] + '</td><td>' + value['transactionCurrency'] + '</td><td style="text-align: right;">' + parseFloat(value['unitcost']).toFixed(value['currencydecimal']) + '</td><td style="text-align: right;">' + parseFloat(value['totalcost']).toFixed(value['currencydecimal']) + '</td></tr>');
                    });
                }

                if (jQuery.isEmptyObject(data['PV'])) {
                    $('#itempriceingPVtbl').addClass('hidden');
                    $('#itempriceingPVbody').append('<tr class="danger"><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    $('#itempriceingPVtbl').removeClass('hidden');
                    $.each(data['PV'], function (key, value) {
                        $('#itempriceingPVbody').append('<tr><td><a style="cursor: pointer;" onclick="documentPageView_modal(\'PV\',' + value['docid'] + ')">' + value['PVcode'] + '</a></td><td>' + value['documentDate'] + '</td><td>' + value['partyName'] + '</td><td style="text-align: right;">' + value['qty'] + '</td><td>' + value['unitOfMeasure'] + '</td><td>' + value['transactionCurrency'] + '</td><td style="text-align: right;">' + parseFloat(value['unitcost']).toFixed(value['currencydecimal']) + '</td><td style="text-align: right;">' + parseFloat(value['totalcost']).toFixed(value['currencydecimal']) + '</td></tr>');
                    });
                }
                $('#itemNameSpan').html(data['item']['itemSystemCode']+'-'+data['item']['itemName']);
                $('#item_pricing_report_model').modal('show');

                if(jQuery.isEmptyObject(data['PO']) && jQuery.isEmptyObject(data['GRV']) && jQuery.isEmptyObject(data['BSI']) && jQuery.isEmptyObject(data['PV'])){
                    $('#iteminquiryrpt').html('No Records Found')
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });

    }

    function createcustomer(val) {
        if(val == 1)
        {
            swal(" ", "You do not have permission to bulk upload at company level,Please Contact your system admin", "error");
        }else
        {
            swal(" ", "You do not have permission to create  item master at company level,please contact your system administrator.", "error");
        }

    }
</script>