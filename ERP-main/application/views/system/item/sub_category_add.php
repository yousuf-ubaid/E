<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('erp_item_category');

$categoryID = $this->input->post('page_id');
$LoadCategory = $this->db->query("SELECT itemCategoryID,categoryTypeID,description FROM `srp_erp_itemcategory` WHERE `itemCategoryID` = '$categoryID'")->row_array();
echo head_page($title, false);
$revenue_gl = all_revenue_gl_drop();
$cost_gl = all_cost_gl_drop();
$asset_gl = all_asset_gl_drop();
$stock_adjustment = stock_adjustment_control_drop();

$emp=load_employee_with_group_drop();

$fetch_cost_account = fetch_cost_account();
$fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
$fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
$pcid = 1;
$userid = current_userID();
$company = current_companyCode();
$username = current_user();
$currentdate = current_date();

$assignBuyersPolicy = (getPolicyValues('ABFC', 'All')) ? getPolicyValues('ABFC', 'All') : 0;

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<style>
    .form1 {
        width: 250px !important;
    }

    .btn-primary {
        background-color: #34495e;
        border-color: #34495e;
        color: #FFFFFF;
    }

    .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
        padding: 5px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px solid #ddd;
    }

    .header {
        color: #000080;
        font-weight: bolder;
        font-size: 13px;
        background-color: #E8F1F4;
    }
</style>
<div style="font-size: 13px; font-weight: 700;"><?php echo $LoadCategory['description']?></div>
<br>
<?php echo form_open('', 'role="form" class="form-horizontal" id="subcategory_form"') ?>
<input type="hidden" class="form-control" id="subcatregoryedit" name="subcatregoryedit">
<div class="row" style="margin-left:0px !important;">
    <input type="hidden" class="form-control" id="createdpcid" name="createdpcid" value="<?php echo $pcid; ?>">
    <input type="hidden" class="form-control" id="createduserid" name="createduserid" value="<?php echo $userid; ?>">
    <input type="hidden" class="form-control" id="schoolid" name="schoolid" value="<?php echo $company; ?>">
    <input type="hidden" class="form-control" id="createdusername" name="createdusername"
           value="<?php echo $username; ?>">
    <input type="hidden" class="form-control" id="createddate" name="createddate" value="<?php echo $currentdate; ?>">
    <?php if ($LoadCategory['categoryTypeID'] != 3) { ?>
        <div class="col-sm-3">
            <!--<div class="form-group">-->
                <label for=""><?php echo $this->lang->line('transaction_sub_category');?><!--Sub Category--> </label>
                <input type="text" class="form-control form1 " id="subcategory" name="subcategory">
           <!-- </div>-->
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for=""> Prefix </label>
                <input type="text" class="form-control " id="codePrefix" name="codePrefix">
            </div>
        </div>
        <div class="col-sm-3">
            <!--<div class="form-group">-->
                <label for=""><?php echo $this->lang->line('erp_item_master_revenue_gl_code');?><!--Revenue GL Code--></label>
                <?php echo form_dropdown('revnugl', $revenue_gl, '', 'class="form-control form1 select2" id="revnugl" '); ?>
            <!--</div>-->
        </div>

        <div class="col-sm-3">
            <!--<div class="form-group">-->
                <label for=""><?php echo $this->lang->line('erp_item_master_cost_gl_code');?><!--Cost GL Code--></label>
                <?php echo form_dropdown('costgl', $cost_gl, '', 'class="form-control form1 select2" id="costgl" required'); ?>
            <!--</div>-->
        </div>
        <?php if ($LoadCategory['categoryTypeID'] != 2) { ?>
        <div class="col-sm-3">
            <!--<div class="form-group">-->
            <label for="">Stock Adjustment Control</label>
            <?php echo form_dropdown('stockadjust', $stock_adjustment, '', 'class="form-control form1 select2" id="stockadjust" required '); ?>
            <!--</div>-->
        </div>



            <div class="col-sm-3">
               <!-- <div class="form-group">-->
                    <label for=""><?php echo $this->lang->line('erp_item_master_asset_gl_code');?><!--Asset GL Code--></label>
                    <?php echo form_dropdown('assetgl', $asset_gl, '', 'class="form-control form1 select2" id="assetgl" required'); ?>
               <!-- </div>-->
            </div>
        <?php }
    } else { ?>
        <div class="col-sm-3">
            <!--<div class="form-group">-->
                <label for=""><?php echo $this->lang->line('transaction_sub_category');?> <!--Sub Category--> <?php required_mark(); ?></label>
                <input type="text" class="form-control form1 " id="subcategory" name="subcategory" required>
            <!--</div>-->
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for=""> Prefix </label>
                <input type="text" class="form-control " id="codePrefix" name="codePrefix">
            </div>
        </div>
        <div class="col-md-3">
            <!--<div class="form-group">-->
                <label for=""><?php echo $this->lang->line('erp_item_master_cost_account');?><!--Cost Account--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('COSTGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "COSTGLCODEdes" required'); ?>
          <!--  </div>-->
        </div>
        <div class="col-md-3">
            <!--<div class="form-group">-->
                <label for=""><?php echo $this->lang->line('erp_item_master_acc_dep_gl_code');?><!--Acc Dep GL Code--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('ACCDEPGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "ACCDEPGLCODEdes" required'); ?>
            <!--</div>-->
        </div>
        <div class="col-md-3">
           <!-- <div class="form-group">-->
                <label for=""><?php echo $this->lang->line('erp_item_master_dep_gl_code');?><!--Dep GL Code--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('DEPGLCODEdes', $fetch_dep_gl_code, '', 'class="form-control form1 select2" id = "DEPGLCODEdes" required'); ?>
           <!-- </div>-->
        </div>
        <div class="col-md-3">
           <!-- <div class="form-group">-->
                <label for=""><?php echo $this->lang->line('erp_item_master_disposal_gl_code');?><!--Disposal GL Code--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('DISPOGLCODEdes', $fetch_disposal_gl_code, '', 'class="form-control form1 select2" id = "DISPOGLCODEdes" required'); ?>
           <!-- </div>-->
        </div>
    <?php } ?>
</div>
<hr>
<div class="text-right m-t-xs">
    <button type="submit" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_add');?><!--Add--> <span class="glyphicon glyphicon-floppy-disk"
                                                                   aria-hidden="true"></span></button>
    <a onclick="fetchPage('system/item/srp_itemcategory_view','Test','Item Category')">
        <button type="button" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-arrow-left"
                                                                   aria-hidden="true"></span> <?php echo $this->lang->line('transaction_back');?> <!--Back-->
        </button>
    </a>
</div>

</form>
<br>
<div id="subcategory_tableDiv">
    <!--Sub Category Table-->
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="subsubcategory_model" role="dialog">
    <div class="modal-dialog" style="width:40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('erp_item_add_add_sub_category');?><!--Add Sub Sub Category--></h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="subsubcategory_form"') ?>
                <div class="modal-body">
                    <table class="table table-bordered" id="sub-add-tb">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('erp_item_sub_sub_category');?></th>
                                    <th>Prefix</th>
                                    <th><button type="button" class="btn btn-primary btn-xs" onclick="add_more_sub()" ><i class="fa fa-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" class="form-control new-items" name="subsubcategory[]" id="subsubcategory"></td>
                                    <td><input type="text" class="form-control new-items" name="codePrefix[]" id="codePrefix"></td>
                                </tr>
                            </tbody>
                    </table>
                    <input type="hidden" class="form-control" id="subsubcategoryedit"  name="subsubcategoryedit">
                    <input type="hidden" class="form-control" id="subsubedit" name="subsubedit">

                    <input type="hidden" class="form-control" id="rvgl" name="rvgl">
                    <input type="hidden" class="form-control" id="cstgl" name="cstgl">
                    <input type="hidden" class="form-control" id="astgl" name="astgl">


                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-primary" id="sub_sub_category_submit" onclick="save_sub_sub_cat()"> <?php echo $this->lang->line('common_add');?><!--Add--> <span
                                    class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"> <?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="itemsubcategoryedit_model" role="dialog">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('erp_item_sub_category_edit');?><!--Item Sub Category Edit--></h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="itemsubcategoryedit_form"') ?>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="subcatregoryeditfrm" name="subcatregoryeditfrm"
                       value="<?php if (isset($_POST["page_id"])) echo $_POST["page_id"]; ?>">

                <div class="row" style="margin-left:0px !important;">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('erp_item_sub_category');?><!--Sub category--></label>
                        <input type="text" class="form-control form1" id="description" name="description">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Prefix</label>
                        <input type="text" class="form-control form1" id="codePrefix_subedit" name="codePrefix">
                    </div>
                </div>
                <div class="row" style="margin-left:0px !important;">
                    <div id="inventry_row_div">
                        <div class="form-group col-sm-3">
                            <label for=""><?php echo $this->lang->line('erp_item_master_revenue_gl_code');?><!--Revenue GL Code--></label>
                            <?php echo form_dropdown('revnugledit', $revenue_gl, '', 'class="form-control form1 select2" id="revnugledit" '); ?>
                        </div>
                        <div class="form-group col-sm-3">
                            <label for=""><?php echo $this->lang->line('erp_item_master_cost_gl_code');?><!--Cost GL Code--></label>
                            <?php echo form_dropdown('costgledit', $cost_gl, '', 'class="form-control form1 select2" id="costgledit" '); ?>
                        </div>
                        <div id="stockadjustment_div">
                        <div class="form-group col-sm-3">
                            <label for="">Stock Adjustment Control</label>
                            <?php echo form_dropdown('stockadjustedit', $cost_gl,  '', 'class="form-control form1 select2" id="stockadjustedit" '); ?>
                        </div>
                        </div>
                    </div>
                    <div id="assetGlCode_div">
                        <div class="form-group col-sm-4">
                            <label for=""><?php echo $this->lang->line('erp_item_master_asset_gl_code');?><!--Asset GL Code--></label>
                            <?php echo form_dropdown('assetgledit', $asset_gl, '', 'class="form-control form1 select2" id="assetgledit" '); ?>
                        </div>
                    </div>
                </div>
                <div class="row hide" id="fixed_row_div" style="margin-left:0px !important;">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for=""><?php echo $this->lang->line('erp_item_master_cost_account');?><!--Cost Account--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('COSTGLCODEdes_edit', $fetch_cost_account, '', 'class="form-control form1 select2" id = "COSTGLCODEdes_edit"'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for=""><?php echo $this->lang->line('erp_item_master_acc_dep_gl_code');?><!--Acc Dep GL Code--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('ACCDEPGLCODEdes_edit', $fetch_cost_account, '', 'class="form-control form1 select2" id = "ACCDEPGLCODEdes_edit"'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for=""><?php echo $this->lang->line('erp_item_master_dep_gl_code');?><!--Dep GL Code--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('DEPGLCODEdes_edit', $fetch_dep_gl_code, '', 'class="form-control form1 select2" id = "DEPGLCODEdes_edit" '); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for=""><?php echo $this->lang->line('erp_item_master_disposal_gl_code');?><!--Disposal GL Code--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('DISPOGLCODEdes_edit', $fetch_disposal_gl_code, '', 'class="form-control form1 select2" id = "DISPOGLCODEdes_edit"'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="subcategoryeditsave" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--> <span
                        class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="itemsubsubcategoryedit_model" role="dialog">
    <div class="modal-dialog" style="width:40%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('erp_item_sub_sub_category_edit');?><!--Item Sub Sub Category Edit--></h4>
            </div>
            <?php echo form_open('', 'role="form" class="form-horizontal" id="itemsubsubcategoryedit_form"') ?>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="subsubcatregoryeditfrm" name="subsubcatregoryeditfrm"
                       value="<?php if (isset($_POST["page_id"])) echo $_POST["page_id"]; ?>">

                <div class="row" style="margin-left:0px !important;">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('erp_item_sub_sub_category');?><!--Sub Sub category--></label>
                        <input type="text" class="form-control form1" id="descriptionsubsub" name="descriptionsubsub">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="">Prefix</label>
                        <input type="text" class="form-control form1" id="codePrefix_subsubedit" name="codePrefix">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="subsubcategoryeditsave" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--> <span
                        class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="buyers_view_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('hrms_attendance_new_shift'); ?><!--New Shift--></h4>
            </div>
            

            <div class="modal-body">

                <div class="row" style="margin-left:0px !important;">

                    <?php echo form_open('', 'role="form" class="form-horizontal" id="category_buyers_form"') ?>
                        <input type="hidden" name="selected_cat_id" id="selected_cat_id">
                        <input type="hidden" name="selected_cat_type" id="selected_cat_type">
                        <div class="form-group col-sm-4">
                            <label for="supplierPrimaryCode">Buyers</label><br>
                            <?php echo form_dropdown('buyers_for_cat[]', $emp, '', 'class="form-control" id="buyers_for_cat" onchange="" multiple="multiple"'); ?>
                        </div>
                    </form>

                    <div class="form-group col-sm-4">
                    <label for="supplierPrimaryCode">&nbsp;&nbsp;</label><br>
                    <button class="btn btn-success btn-sm" id="addAllBtn" style="font-size:12px;"
                        onclick="addAllRows()"> <?php echo $this->lang->line('common_save');?>
                    </div>
                </div>

                <hr>
               

                <div class="table-responsive">
                    <table id="added_cat_buyers_tb" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">Name</th>
                                <th style="min-width: 20%">Code</th>
                                <th style="min-width: 11%">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
           
            <div class="modal-footer">
                
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
            <input type="hidden" id="editID" name="editID">
          
        </div>
    </div>
</div>
<div class="modal fade" id="employee_model" role="dialog" data-keyboard="false" data-backdrop="static"
     style="z-index: 999999">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('common_employees');?><!--Employees--></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="isEmpLoad" value="0">
                    <div class="table-responsive col-md-7">
                        <div class="pull-right">
                            <button class="btn btn-primary btn-sm" id="selectAllBtn" style="font-size:12px;"
                                    onclick="selectAllRows()"> <?php echo $this->lang->line('common_select_all');?><!--Select All-->
                            </button>
                        </div>
                        <hr style="margin-top: 5%">
                        <table id="emp_modalTB" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 25%"><?php echo $this->lang->line('hrms_attendance_employee_code');?><!--EMP Code--></th>
                                <th style="width:auto"><?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></th>
                                <th style="width:auto"><?php echo $this->lang->line('common_designation');?><!--Designation--></th>
                                <th style="width: 5%">
                                    <div id="dataTableBtn"></div>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <div class=" col-md-5">
                        <div class="pull-right">
                            <button class="btn btn-default btn-sm" id="clearAllBtn" style="font-size:12px;"
                                    onclick="clearAllRows()"> <?php echo $this->lang->line('common_clear_all');?><!--Clear All-->
                            </button>
                        </div>
                        <hr style="margin-top: 7%">
                        <form id="tempTB_form">
                          
                            <table class="<?php echo table_class(); ?>" id="tempTB">
                                <thead>
                                <tr>
                                    <th style="max-width: 5%"><?php echo $this->lang->line('hrms_attendance_employee_code');?><!--EMP CODE--></th>
                                    <th style="max-width: 95%"><?php echo $this->lang->line('hrms_attendance_employee_name');?><!--EMP NAME--></th>
                                    <th>
                                        <div id="removeBtnDiv"></div>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success btn-sm" id="addAllBtn" style="font-size:12px;"
                        onclick="addAllRows()"> <?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button" style="font-size:12px;">
                    <?php echo $this->lang->line('common_Close');?><!--Close-->
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var pagename;
    var masterid;
    var sub_tb = $('#sub-add-tb');
    var buyersPolicy = 0;
    var key =0;
    var tempTB = $('#tempTB').DataTable({"bPaginate": false});
    var empTempory_arr = [];

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/item/srp_itemcategory_view','','Item Category');
        });
        $('.select2').select2();
        pagename = '<?php echo $_POST["page_name"]; ?>';
        masterid = <?php echo $this->input->post('page_id') ?>;

        buyersPolicy = <?php echo $assignBuyersPolicy?>;
        subcategoryview();

        ///new code 

        $('#buyers_for_cat').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
       

        /*        subcategory_view_master();*/
        $('#subcategory_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {},
        }).on('success.form.bv', function (e) {
            masterid = <?php echo $this->input->post('page_id') ?>;
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'master', 'value': masterid});
            data.push({'name': 'pagename', 'value': pagename});
            $.ajax(
                {
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Sub_Category/save_subcategory'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        HoldOn.close();
                        refreshNotifications(true);
                        if (data) {
                            subcategoryview();
                            document.getElementById('subcategory_form').reset();
                            $("#revnugl").val(null).trigger("change");
                            $("#costgl").val(null).trigger("change");
                            $("#assetgl").val(null).trigger("change");
                            $("#stockadjust").val(null).trigger("change");

                            $('#subcategory_form').bootstrapValidator('resetForm', true);

                        } else {
                            $("#revnugl").val(null).trigger("change");
                            $("#costgl").val(null).trigger("change");
                            $("#assetgl").val(null).trigger("change");
                            $("#assetgl").val(null).trigger("change");
                            $('#subcategory_form').bootstrapValidator('resetForm', true);

                        }
                    }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
                });
        });
    

        $('#itemsubcategoryedit_form').bootstrapValidator({
            live            : 'enabled',
            message         : '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            //excluded        : [':disabled'],
            fields          : {
                description          : {validators : {notEmpty:{message:'<?php echo $this->lang->line('erp_item_description_is_required');?>.'}}},/*description is required*/
                codePrefix          : {validators : {notEmpty:{message:'Code prefix'}}},/*description is required*/
                revnugledit         : {validators : {notEmpty:{message:'<?php echo $this->lang->line('erp_revenue_gl_code_is_required');?>.'}}},/*Revenue GL Code is required*/
                costgledit         : {validators : {notEmpty:{message:'<?php echo $this->lang->line('erp_cost_gl_code_is_required');?>.'}}},/*Cost GL Code is required*/
                assetgledit         : {validators : {notEmpty:{message:'<?php echo $this->lang->line('erp_asset_gl_code_is_required');?>.'}}}/*Asset GL Code is required*/
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'master', 'value': masterid});
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?php echo site_url('Sub_Category/update_subcategory'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    //fetchPage('system/srp_mu_itemcategory_view','Test','Item Category');
                    myAlert(data[0],data[1]);
                    $("#itemsubcategoryedit_model").modal("hide");
                    subcategoryview();
                    $('#subcategoryeditsave').attr('disabled', false)
                },error : function(){
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });




        $('#itemsubsubcategoryedit_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {},
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
                    url: "<?php echo site_url('Sub_Category/update_subsubcategory'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        //fetchPage('system/srp_mu_itemcategory_view','Test','Item Category');
                        myAlert(data[0],data[1]);
                        $('#subsubcategoryeditsave').attr('disabled', false)
                        if(data[0]=='s'){
                            $("#itemsubsubcategoryedit_model").modal("hide");
                            $("#subsubcategory_model").modal("hide");
                            subcategoryview();
                        }
                    }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
                });
        });


    });

    function subcategoryview() {
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'html',
                data: {idedit: <?php echo $this->input->post('page_id'); ?>},
                url: "<?php echo site_url('Sub_Category/load_subcategoryMaster'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('#subcategory_tableDiv').html(data);
                    $("[rel=tooltip]").tooltip();

                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/

            }
            });
    }

    function subcategoryviewMaster() {
        editid = <?php echo $this->input->post('page_id'); ?>;
        var Otable = $('#subcategory_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Sub_Category/load_subcategory'); ?>",
            //"bJQueryUI": true,
            //"iDisplayStart ": 8,
            //"sEcho": 1,
            ///"sAjaxDataProp": "aaData",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function () {
                $("[rel=tooltip]").tooltip();
            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "itemCategoryID"},
                {"mData": "description"},
                {"mData": "addsubsub"},
                {"mData": "action"}
            ],
            "columnDefs": [{
                "targets": [2, 3],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "idedit", "value": editid});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function subsubcategory(id) {
        $('#subsubcategoryedit').val('');
        $("#subsubcategory_model").modal("show");
        $('#subsubcategoryedit').val(id);
        //$('#rvgl').val(revanuegl);
        //$('#cstgl').val(costgl);
        //$('#astgl').val(assetgl);
        //subsubcategoryview();

    }
    function resetform() {
        document.getElementById('subsubcategory_form').reset();
        $('#sub-add-tb > tbody').find("tr:not(:nth-child(1))").remove();
        $('#sub_sub_category_submit').attr('disabled' , false);
        //subcategoryview();
    }

    /*$("#subsubcategory_model").on("hidden.bs.modal", function () {
     subcategoryview();

     });*/

    function subsubcategoryview() {
        var Otable = $('#subsubcategory_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Sub_Category/load_subsubcategory'); ?>",
            //"bJQueryUI": true,
            //"iDisplayStart ": 8,
            //"sEcho": 1,
            ///"sAjaxDataProp": "aaData",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function () {
                $("[rel=tooltip]").tooltip();
            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "itemCategoryID"},
                {"mData": "description"},
                {"mData": "action"}
            ],
            "columnDefs": [{
                "targets": [2],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "subsubcategoryedit", "value": $('#subsubcategoryedit').val()});
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }


    function opensubcategoryedit(id) {
        changeFormCode();
        $("#itemsubcategoryedit_model").modal("show");
        $('#subcatregoryedit').val(id);
        $('#subcatregoryeditfrm').val(id);
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {id: id},
                url: "<?php echo site_url('Sub_Category/edit_itemsubcategory'); ?>",
                beforeSend: function () {

                },
                success: function (data) {
                    $('#description').val(data['description']);
                    $('#codePrefix_subedit').val(data['codePrefix']);
                    $('#revnugledit').val(data['revenueGL']).change();
                    $('#costgledit').val(data['costGL']).change();
                    $('#assetgledit').val(data['assetGL']).change();
                    $('#COSTGLCODEdes_edit').val(data['faCostGLAutoID']).change();
                    $('#ACCDEPGLCODEdes_edit').val(data['faACCDEPGLAutoID']).change();
                    $('#DEPGLCODEdes_edit').val(data['faDEPGLAutoID']).change();
                    $('#DISPOGLCODEdes_edit').val(data['faDISPOGLAutoID']).change();
                    $('#stockadjustedit').val(data['stockAdjustmentGL']).change();

                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/

            }
            });
    }

    function opensubsubcategoryedit(id) {
        $('#subsubedit').val('');
        $("#itemsubsubcategoryedit_model").modal("show");
        $('#subsubedit').val(id);
        $('#subsubcatregoryeditfrm').val(id);
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: {id: id},
                url: "<?php echo site_url('Sub_Category/edit_itemsubsubcategory'); ?>",
                beforeSend: function () {

                },
                success: function (data) {

                    $('#descriptionsubsub').val(data['description']);
                    $('#codePrefix_subsubedit').val(data['codePrefix']);

                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/

            }
            });
    }

    function reset_form() {
        document.getElementById('subcategory_form').reset();
    }
    function reset_form1() {
        document.getElementById('subsubcategory_form').reset();
    }


    function changeFormCode() {
        itemCategoryID = <?php echo $categoryID ?>;
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_category_type_id"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if (data['categoryTypeID'] == 1) {
                        $("#inventry_row_div").removeClass("hide");
                        $("#assetGlCode_div").removeClass("hide");
                        $("#fixed_row_div").addClass("hide");
                    }
                    if (data['categoryTypeID'] == 2) {
                        $("#assetGlCode_div").addClass("hide");
                        $("#stockadjustment_div").addClass("hide");
                    }
                    if (data['categoryTypeID'] == 3) {
                        $("#inventry_row_div").addClass("hide");
                        $("#assetGlCode_div").addClass("hide");
                        $("#fixed_row_div").removeClass("hide");
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });

    }
    function add_more_sub(){
        var appendData = '<tr><td><input type="text" name="subsubcategory[]" id="subsubcategory" class="form-control new-items" /></td>';
        appendData += '<td><input type="text" name="codePrefix[]" class="form-control new-items" />';
        appendData += '<td align="center" style="vertical-align: middle">';
        appendData += '<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span></td></tr>';

        sub_tb.append(appendData);
    }
    $(document).on('click', '.remove-tr', function(){
        $(this).closest('tr').remove();
    });
    function save_sub_sub_cat() {
        var errorCount=0;
        $('.new-items').each(function(){
            if( $.trim($(this).val()) == '' ){
                errorCount++;
                return false;
            }
        });
        if(errorCount == 0){
            var data = $('#subsubcategory_form').serializeArray();
           // data.push({"name": "subsubcategoryedit", "value": $('#subsubcategoryedit').val()});
            $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Sub_Category/save_subsubcategory'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        $("#subsubcategory_model").modal("hide");
                        subcategoryview();
                        myAlert(data[0],data[1]);
                    }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
        } else {
            myAlert('e', '<?php echo $this->lang->line('common_please_fill_all_fields');?>');
        } 
    }   

    //new dev

    function open_buyers_model(id,type) {
        $('#selected_cat_id').val('');
        $('#buyers_view_model').modal({backdrop: "static"});
        $('#selected_cat_id').val(id);
        $('#selected_cat_type').val(type);
        $('#buyers_for_cat').val('').multiselect2("refresh");
        load_category_added_buyers();

    }

    function openEmployeeModal_buyers() {
        $('#employee_model').modal('show');
        load_employeeForModal();
    }

    function load_employeeForModal() {
        $('#emp_modalTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/getEmployeesDataTableShift'); ?>",
            "aaSorting": [[1, 'asc']],
            "aLengthMenu": [[10, 25, 50, 75, 100,200], [10, 25, 50, 75, 100,200]],
            "iDisplayLength": 200,
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
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "ECode"},
                {"mData": "empName"},
                {"mData": "DesDescription"},
                {"mData": "addBtn"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({'name': 'epfMasterID', 'value': epfMasterID});
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

    function addTempTB(det) {

        var table = $('#emp_modalTB').DataTable();
        var thisRow = $(det);

        var details = table.row(thisRow.parents('tr')).data();
        var empID = details.EIdNo;

        var inArray = $.inArray(empID, empTempory_arr);
        if (inArray == -1) {
            var empDet = '<div class="pull-right"><input type="hidden" name="empHiddenID[]"  class="modal_empID" value="' + empID + '">';
            empDet += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="' + details.last_ocGrade + '">';
            empDet += '<span class="glyphicon glyphicon-trash" onclick="removeTempTB(this)" style="color:rgb(209, 91, 71);"></span> </a></div>';

            tempTB.rows.add([{
                0: details.ECode,
                1: details.empName,
                2: empDet,
                3: empID
            }]).draw();

            empTempory_arr.push(empID);
        }else{
                myAlert('w', 'Already Added');
            }

    }

    function removeTempTB(det) {
        var table = $('#tempTB').DataTable();
        var thisRow = $(det);
        var details = table.row(thisRow.parents('tr')).data();
        empID = details[3];

        empTempory_arr = $.grep(empTempory_arr, function (data) {
            return parseInt(data) != empID
        });

        table.row(thisRow.parents('tr')).remove().draw();
    }

    function clearAllRows() {
        var table = $('#tempTB').DataTable();
        empTempory_arr = [];
        table.clear().draw();
    }

    function selectAllRows() {
        var tempTB = $('#tempTB').DataTable();
        var emp_modalTB = $('#emp_modalTB').DataTable();
        var empDet1;
        emp_modalTB.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            var empID = data.EIdNo;

            var inArray = $.inArray(empID, empTempory_arr);
            if (inArray == -1) {
                empDet1 = '<div class="pull-right"><input type="hidden" name="empHiddenID[]" class="modal_empID" value="' + empID + '">';
                empDet1 += '<input type="hidden" name="last_ocGrade[]" class="modal_ocGrade" value="' + data.last_ocGrade + '">';
                empDet1 += '<span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" onclick="removeTempTB(this)"></span> </a></div>';

                tempTB.rows.add([{
                    0: data.ECode,
                    1: data.empName,
                    2: empDet1,
                    3: empID
                }]).draw();

                empTempory_arr.push(empID);
            }else{
                myAlert('w', 'Already Added');
            }
        });
    }

    function load_category_added_buyers() {

        var id = $('#selected_cat_id').val();
        var type = $('#selected_cat_type').val();
       
        var Otable = $('#added_cat_buyers_tb').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Sub_Category/load_subcategory_added_buyers'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "EIdNo"},
                {"mData": "Ename1"},
                {"mData": "ECode"},
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "categoryID", "value": id});
                aoData.push({"name": "type", "value": type});
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

    
    function addAllRows() {

        var postData = $('#category_buyers_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Sub_Category/add_buyers_to_category'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                   // $('#employee_model').modal('hide');
                    load_category_added_buyers();
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    
    function delete_category_assign_buyers(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to remove this buyer",/*You want to confirm this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
               
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {id:id},
                    url: "<?php echo site_url('Sub_Category/delete_category_assign_buyers'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_category_added_buyers();
                        } else {

                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


</script>
