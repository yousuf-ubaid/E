<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('tax', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('tax_new_tax');
echo head_page($title, false);

/*echo head_page('New Tax',false);*/
//$current_date        = format_date($this->common_data['current_date']);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$this->load->helpers('expense_claim');
$supplier_arr = all_authority_drop();
$gl_code_arr = authority_gl_drop_without_control_accounts();//authority_gl_drop();
$gl_code_arr_drop_new = all_chart_of_accounts();
$main_category_arr = all_main_category_drop();
$tax_type_arr        = array('' => $this->lang->line('tax_select_tax_type')/*'Select Tax Type'*/,'1' => $this->lang->line('tax_sales_tax')/*'Sales Tax'*/,'2' =>$this->lang->line('tax_purchase_tax') /*'Purchase Tax'*/);
?>

<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<!-- <div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Tax Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_details()" data-toggle="tab">Step 2 - Tax Detail</a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="fetch_addon_cost()" data-toggle="tab">Step 3 - Tax Confirmation</a>
</div><hr>
<div class="tab-content"> -->
    <div id="step1" class="tab-pane active">
        <?php echo form_open('','role="form" id="tax_form"'); ?>
            <div class="row" >
            

                <div class="form-group col-sm-2">
                    <label for="taxCategory"><?php echo $this->lang->line('tax_tax_category');?><!--Tax Category--> <?php  required_mark(); ?></label>
                    <?php echo form_dropdown('taxCategory',array('1' => $this->lang->line('common_other')/*'Other'*/,'2' =>'VAT'/*'VAT'*/),'1','class="form-control" id="taxCategory" required onchange="change_taxXategory(this.value)"'); ?>
                </div>

                <div class="form-group col-sm-4 tax_type">
                   <label for="taxType"><?php echo $this->lang->line('tax_type');?><!--Tax Type--> <?php  required_mark(); ?></label>
                   <?php echo form_dropdown('taxType', $tax_type_arr, '','class="form-control" id="taxType"'); ?>
                </div>


                <div class="form-group col-sm-2">
                    <label for="effectiveFrom"><?php echo $this->lang->line('tax_from_date');?><!--From Date--> <?php required_mark();  ?></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type='text' class="form-control" id="effectiveFrom" name="effectiveFrom" value="<?php echo $current_date; ?>" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" />
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="taxDescription"><?php echo $this->lang->line('tax_description');?><!--Tax Description--> <?php required_mark();  ?></label>
                    <input type="text" class="form-control " id="taxDescription" name="taxDescription">
                </div>
            </div>
            <div class="row" >
                <div class="form-group col-sm-2">
                    <label for="taxShortCode"><?php echo $this->lang->line('tax_code');?><!--Tax Code--> <?php  required_mark(); ?></label>
                    <input type="text" class="form-control " id="taxShortCode" name="taxShortCode">
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('tax_percentage');?><!--Tax Percentage--> <?php  required_mark(); ?></label>
                    <div class="input-group">
                        <input type="text" class="form-control " id="taxPercentage" name="taxPercentage">
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="supplierID"><?php echo $this->lang->line('tax_paying_authority');?><!--Tax Paying Authority--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('supplierID', $supplier_arr, '', 'class="form-control select2" id="supplierID" onchange="changesupplierGLAutoID()" required'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="liabilityAccount"><?php echo $this->lang->line('tax_liability_account');?><!--Liability Account--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('supplierGLAutoID', $gl_code_arr, '', 'class="form-control select2" id="supplierGLAutoID" required'); ?>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-4 vatCategoryColumn hidden">
                    <label for="inputVatGLAccountAutoID"><?php echo $this->lang->line('tax_input_vat_gl_account');?><!--Input Vat GL Account--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('inputVatGLAccountAutoID', $gl_code_arr_drop_new, '', 'class="form-control select2" id="inputVatGLAccountAutoID"'); ?>
                </div>
                <div class="form-group col-sm-4 vatCategoryColumn hidden">
                    <label for="inputVatTransferGLAccountAutoID"><?php echo $this->lang->line('tax_input_vat_transfer_gl_account');?><!--Input Vat Transfer GL Account--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('inputVatTransferGLAccountAutoID', $gl_code_arr_drop_new, '', 'class="form-control select2" id="inputVatTransferGLAccountAutoID" '); ?>
                </div>
                
                <div class="form-group col-sm-4 vatCategoryColumn hidden">
                    <label for="outputVatGLAccountAutoID"><?php echo $this->lang->line('tax_output_vat_gl_account');?><!--Output Vat GL Account--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('outputVatGLAccountAutoID', $gl_code_arr_drop_new, '', 'class="form-control select2" id="outputVatGLAccountAutoID" '); ?>
                </div>
                <div class="form-group col-sm-4 vatCategoryColumn hidden">
                    <label for="outputVatTransferGLAccountAutoID"><?php echo $this->lang->line('tax_output_vat_transfer_gl_account');?><!--Output Vat Transfer GL Account--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('outputVatTransferGLAccountAutoID', $gl_code_arr_drop_new, '', 'class="form-control select2" id="outputVatTransferGLAccountAutoID"'); ?>
                </div>



                <div class="form-group col-sm-2">
                    <label for="isActive"><?php echo $this->lang->line('tax_status');?><!--Tax Status--> <?php  required_mark(); ?></label>
                    <?php echo form_dropdown('isActive',array('1' => $this->lang->line('common_active')/*'Active'*/,'0' =>$this->lang->line('tax_deactive')/*'Deactive'*/),'1','class="form-control" id="isActive" required'); ?>
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('tax_reference_no');?><!--Reference No--></label>
                    <input type="text" class="form-control " id="taxReferenceNo" name="taxReferenceNo">
                </div>
                <div class="col-md-4 isvatTaxEnableYN">
                    <div class="form-group">
                        <label for=""><?php echo $this->lang->line('tax_is_claimable');?><!--Is Claimable--></label>
                        <div class="skin skin-square">
                            <div class="skin-section" id="extraColumns">
                                <input id="isClaimable" type="checkbox" data-caption="" class="columnSelected" name="isClaimable" value="1" checked>
                                <label for="checkbox">
                                    &nbsp;
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 isvatTaxEnableYN">
                    <div class="form-group">
                        <label for=""><?php echo $this->lang->line('tax_is_vat');?><!--Is Claimable--></label>
                        <div class="skin skin-square">
                            <div class="skin-section" id="extraColumns">
                                <input id="isVat" type="checkbox" data-caption="" class="columnSelected" name="isVat" value="1" checked>
                                <label for="checkbox">
                                    &nbsp;
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-right m-t-xs">
                <button class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save');?><!--Save--></button>
            </div>
        </form>
        
    </div>
                    
    <!-- <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i> Addon Cost </h4><h4></h4></div>
            <div class="col-md-4"><button type="button" onclick="addon_cost_modal()" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Addon Cost</button></div>
        </div><br> 
        <div class="table-responsive">
            <table class="<?php echo table_class(); ?>">
                <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 20%">Addon Catagory</th>
                        <th style="min-width: 15%">Supplier</th>
                        <th style="min-width: 10%">Reference No</th>
                        <th style="min-width: 30%">Description</th>
                        <th style="min-width: 10%">Amount <span class="currency"> ( LK )</span></th>
                        <th style="min-width: 10%">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="addon_table_body">
                    <tr class="danger">
                        <td class="text-center" colspan="8">No Records Found</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">Addons Total <span class="currency"> ( LKR )</span></td>
                        <td id="t_total" class="total text-right">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <hr> 
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick="">Previous</button>-->
            <!-- <button class="btn btn-primary next" onclick="load_conformation();" >Save & Next</button> -->
        <!-- </div>
    </div>
    <div id="step3" class="tab-pane">
        <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank" href="<?php //echo site_url('Double_entry/fetch_double_entry_grv/'); ?>"><span class="glyphicon glyphicon-random" aria-hidden="true"></span>  &nbsp;&nbsp;&nbsp;Account Review entries   
                </a>
                </span>
            </div>
        </div><hr>
        <div id="conform_body"></div>
        <hr> 
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" >Previous</button>
            <button class="btn btn-primary " onclick="save_draft()">Save & Draft</button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">Confirm</button>
        </div>
    </div>
</div> -->

<div class="modal fade bs-example-modal-lg" id="add_new_main_category" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">Add VAT Main Category</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="add_new_main_category_form">
                    <input type="hidden" name="taxMasterAutoID" id="taxMasterAutoID"/>
                    <input type="hidden" name="taxVatMainCategoriesAutoID" id="taxVatMainCategoriesAutoID"/>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"> Main Category</label>
                        <div class="col-sm-7">
                            <input type="text" name="mainCategoryDesc" id="mainCategoryDesc" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="isActiveMainCat" class="col-sm-3 control-label">Is Active</label>
                        <div class="col-sm-7">
                            <div class="skin skin-square">
                                <div class="skin-section" id="extraColumns">
                                    <input id="isActiveMainCat" type="checkbox" data-caption="" class="columnSelected" name="isActive" value="1" checked>
                                    <label for="checkbox">&nbsp;</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="Save_new_main_category()"><?php echo $this->lang->line('common_save'); ?><!--Add--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="add_new_sub_category" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">Add VAT Sub Category</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="add_new_sub_category_form">
                    <input type="hidden" name="taxMasterAutoID" id="taxMasterAutoIDSubCat"/>
                    <input type="hidden" name="taxVatSubCategoriesAutoID" id="taxVatSubCategoriesAutoID"/>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Main Category</label>
                        <div class="col-sm-7">
                            <?php echo form_dropdown('mainCategoryVAT', array(''=> 'Select Main Category'), '', 'class="form-control select2" id="mainCategoryVAT" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Sub Category</label>
                        <div class="col-sm-7">
                            <input type="text" name="subCategoryDesc" id="subCategoryDesc" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Percentage</label>
                        <div class="col-sm-7">
                            <input type="text" name="subPercentage" id="subPercentage" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Applicable On</label>
                        <div class="col-sm-7">
                            <?php echo form_dropdown('applicableOn', array('1'=> 'Net Amount', '2'=> 'Gross Amount'), '', 'class="form-control select2" id="applicableOn" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Is Active</label>
                        <div class="col-sm-7">
                            <div class="skin skin-square">
                                <div class="skin-section" id="extraColumns">
                                    <input id="isActiveSubCat" type="checkbox" data-caption="" class="columnSelected" name="isActive" value="1" checked>
                                    <label for="checkbox">&nbsp;</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="Save_new_sub_category()"><?php echo $this->lang->line('common_save'); ?><!--Add--></button>
            </div>
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
                            <a class="" data-id="0" href="#step11" data-toggle="tab" aria-expanded="true">
                                <span>
                                    <i class="fa fa-cog tachometerColor" aria-hidden="true"
                                       style="color: #50749f;font-size: 16px;"></i>&nbsp;&nbsp;<?php echo $this->lang->line('common_add_item'); ?> <!--Add Item-->
                                </span>
                            </a>
                        </li>
                        <li class="">
                            <a class="" data-id="0" href="#step12" data-toggle="tab" aria-expanded="true">
                                <span>
                                    <i class="fa fa-list tachometerColor" aria-hidden="true" style="color: #50749f;font-size: 16px;"></i>&nbsp;Item List
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div id="step11" class="tab-pane active">
                        <div id="sysnc">
                            <input class="hidden" id='taxVatSubCategoriesAutoID_AddItem' name='taxVatSubCategoriesAutoID'>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="syncMainCategoryID" onchange="LoadMainCategorySync()"'); ?>
                                </div>
                                <div class="form-group col-sm-3">
                                    <select name="subcategoryID" id="syncSubcategoryID" class="form-control searchbox"
                                            onchange="sync_item_table()">
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="table-responsive">
                                <table id="item_table_sync" class="table table-striped table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">&nbsp;</th>
                                        <th style="min-width: 12%">Main Category</th>
                                        <th style="min-width: 12%">Sub Category</th>
                                        <th style="min-width: 25%">
                                            <?php echo $this->lang->line('common_item'); ?><!-- Item --></th>
                                        <th style="min-width: 10%">Secondary Code</th>
                                        <th style="min-width: 5%; text-align: center !important;">
                                            <button type="button" data-text="Add" onclick="addItemForDiscount()" class="btn btn-xs btn-primary">
                                                <i class="fa fa-plus" aria-hidden="true"></i><?php echo $this->lang->line('common_add_item'); ?>
                                            </button>
                                        </th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="step12" class="tab-pane">
                        <div class="row">
                            <div class="form-group col-sm-3">
                                <?php echo form_dropdown('listMainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="listMainCategoryID" onchange="LoadMainCategoryInList()"'); ?>
                            </div>
                            <div class="form-group col-sm-3">
                                <select name="listSubcategoryID" id="listSubcategoryID" class="form-control searchbox"
                                        onchange="item_table_view()">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin: 20px;">
                            <div class="table-responsive">
                                <table id="item_table_view" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">&nbsp;</th>
                                        <th style="min-width: 12%">Main Category</th>
                                        <th style="min-width: 12%">Sub Category</th>
                                        <th style="min-width: 25%"><?php echo $this->lang->line('common_item'); ?><!-- Item --></th>
                                        <th style="min-width: 10%">Secondary Code</th>
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
<script type="text/javascript">
    var taxMasterAutoID;
    $( document ).ready(function() {
        $('.select2').select2();
        $('.headerclose').click(function(){
            fetchPage('system/tax/tax_management','Test','TAX');
        });
        taxMasterAutoID=null;
        /*$('#effectiveFrom').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(ev){
            $('#tax_form').bootstrapValidator('revalidateField', 'effectiveFrom');
            $(this).datepicker('hide');
        });*/

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev) {
            $('#tax_form').bootstrapValidator('revalidateField', 'effectiveFrom');
        });

        p_id         = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            taxMasterAutoID =p_id;
            laad_tax_header();
            //$('.btn-wizard').removeClass('disabled');
        }else{
            //$('.btn-wizard').addClass('disabled');
        }

        $('#tax_form').bootstrapValidator({
            live            : 'enabled',
            message         : '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
           // excluded        : [':disabled'],
            fields          : {
              //  taxType                 : {validators : {notEmpty:{message:'<?php echo $this->lang->line('tax_type_is_required');?>.'}}},/*Tax Type is required*/
                supplierID              : {validators : {notEmpty:{message:'<?php echo $this->lang->line('tax_supplier_is_required');?>.'}}},/*Supplier is required*/
                effectiveFrom           : {validators : {notEmpty:{message:'<?php echo $this->lang->line('tax_effective_date_is_required');?>.'}}},/*Effective Date is required*/
                taxShortCode            : {validators : {notEmpty:{message:'<?php echo $this->lang->line('tax_short_code_is_required');?>.'}}},/*Short Code is required*/
                taxDescription          : {validators : {notEmpty:{message:'<?php echo $this->lang->line('tax_short_tax_description_is_required');?>.'}}},/*Tax Description is required*/
                supplierGLAutoID        : {validators: {notEmpty: {message: '<?php echo $this->lang->line('tax_liability_account_is_required');?>.'}}},/*Liability Account is required*/
                taxCategory             : {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_category_is_required');?>.'}}}/*Category is required*/
            },
            }).on('success.form.bv', function(e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                var taxCategory = $('#taxCategory').val();
                data.push({'name' : 'taxMasterAutoID', 'value' : taxMasterAutoID },{'name' : 'taxCategory', 'value' : taxCategory});
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : data,
                    url :"<?php echo site_url('Tax/save_tax_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){ 
                        refreshNotifications(true); 
                        if (data['status']) {
                            $('.btn-wizard').removeClass('disabled');
                            taxMasterAutoID = data['last_id'];
                            fetchPage('system/tax/tax_management','Test','TAX');
                        };
                        stopLoad();
                    },error : function(){
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });   
        });
            
        // $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        //     $('a[data-toggle="tab"]').removeClass('btn-primary');
        //     $('a[data-toggle="tab"]').addClass('btn-default');
        //     $(this).removeClass('btn-default');
        //     $(this).addClass('btn-primary');
        // });

        // $('.next').click(function(){
        //     var nextId = $(this).parents('.tab-pane').next().attr("id");
        //     $('[href=#'+nextId+']').tab('show');
        // });

        // $('.prev').click(function(){
        //     var prevId = $(this).parents('.tab-pane').prev().attr("id");
        //     $('[href=#'+prevId+']').tab('show');
        // });

        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    });

    function laad_tax_header(){
        if (taxMasterAutoID) {
            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : {'taxMasterAutoID':taxMasterAutoID},
                url :"<?php echo site_url('Tax/laad_tax_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    if(!jQuery.isEmptyObject(data)){
                        $('#taxType').val(data['taxType']);
                        $("#effectiveFrom").val(data['effectiveFrom']);
                        $("#supplierID").val(data['supplierAutoID']).change();
                        $('#taxReferenceNo').val(data['taxReferenceNo']);
                        $('#taxDescription').val(data['taxDescription']);
                        $('#taxShortCode').val(data['taxShortCode']);
                        $('#taxPercentage').val(data['taxPercentage']);

                        $('#taxCategory').val(data['taxCategory']);
                       // $('#taxRegistrationNo').val(data['registrationNo']);
                       // $('#taxIdentificationNo').val(data['identificationNo']);
                        $("#inputVatGLAccountAutoID").val(data['inputVatGLAccountAutoID']).change();
                        $("#inputVatTransferGLAccountAutoID").val(data['inputVatTransferGLAccountAutoID']).change();
                        $("#outputVatGLAccountAutoID").val(data['outputVatGLAccountAutoID']).change();
                        $("#outputVatTransferGLAccountAutoID").val(data['outputVatTransferGLAccountAutoID']).change();
                        change_taxXategory(data['taxCategory']);
                        if(data['taxCategory'] == 2) {
                            $('.vatDetailsTable').removeClass('hide');
                            load_VAT_main_category();
                            load_VAT_sub_category();
                        } else {
                            $('.vatDetailsTable').addClass('hide');
                        }
                        if (data['isClaimable'] == 1) {
                            $('#isClaimable').iCheck('check');
                        } else {
                            $('#isClaimable').iCheck('uncheck');
                        }

                        if (data['isVat'] == 1) {
                            $('#isVat').iCheck('check');
                        } else {
                            $('#isVat').iCheck('uncheck');
                        }

                        setTimeout(function(){
                            $("#supplierGLAutoID").val(data['supplierGLAutoID']).change();
                        }, 1000);

                        //$('#taxPercentage').val(data['taxPercentage']);
                        //$('#financeyear').val(data['companyFinanceYearID']);
                        $('#isActive').val(data['isActive']);
                    }
                    stopLoad();
                    refreshNotifications(true);
                },error : function(){
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });        
        }    
    }

    function changesupplierGLAutoID() {
        $supplierID= $('#supplierID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierID': $supplierID},
            url: "<?php echo site_url('Tax/changesupplierGLAutoID'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#supplierGLAutoID").val(data['taxPayableGLAutoID']).change();
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function change_taxXategory(taxCategory) {
        if(taxCategory == 1) {
            $('.vatCategoryColumn').addClass('hidden');
            $('.tax_type').removeClass('hidden');
            $('.isvatTaxEnableYN').removeClass('hide');
            $('#isClaimable').iCheck('check');
        } else {
            $('.vatCategoryColumn').removeClass('hidden');
            $('.tax_type').addClass('hidden');
            $('.isvatTaxEnableYN').addClass('hide');
            $('#isClaimable').iCheck('uncheck');

        }
    }

    function add_vat_main_category() {
            $('#taxMasterAutoID').val(taxMasterAutoID);
            $('#taxVatMainCategoriesAutoID').val('');
            $('#mainCategoryDesc').val('');
            $('#isActiveMainCat').iCheck('uncheck');
            $('#add_new_main_category').modal('show');
    }

    function Save_new_main_category()
    {
        taxID = $('#taxMasterAutoID').val();
        var data = $("#add_new_main_category_form").serializeArray();
        if(taxID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Tax/save_vat_main_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('#add_new_main_category').modal('hide');
                        load_VAT_main_category();
                        load_VAT_sub_category();
                    };                
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_VAT_main_category() {
        if(taxMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taxMasterAutoID': taxMasterAutoID},
                url: "<?php echo site_url('Tax/fetch_VAT_main_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#vat_main_category_table_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#vat_main_category_table_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    } else {
                        $.each(data, function (key, value) {
                            if(value['isActive'] == 1) {
                                var activelable = '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Active</span>';
                            } else {
                                var activelable = '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">In Active</span>';
                            }
                            $('#vat_main_category_table_body').append(
                                '<tr>'+
                                '<td>' + x + '</td>'+
                                '<td>' + value['mainCategoryDescription'] + '</td>'+
                                '<td style="text-align: center">' + activelable + '</td>'+
                                '<td style="text-align: center"><a onclick="edit_VAT_main_Category('+ value['taxVatMainCategoriesAutoID'] +', '+ value['taxMasterAutoID'] +')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a class="text-yellow" onclick="delete_VAT_main_category(' + value['taxVatMainCategoriesAutoID'] + ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td>'+
                                '</tr>'
                            );
                            x++;
                        });
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function edit_VAT_main_Category(taxVatMainCategoriesAutoID, taxID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taxVatMainCategoriesAutoID': taxVatMainCategoriesAutoID},
            url: "<?php echo site_url('Tax/load_vat_main_category'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#taxVatMainCategoriesAutoID').val(taxVatMainCategoriesAutoID);
                $('#taxMasterAutoID').val(taxID);
                $('#mainCategoryDesc').val(data['mainCategoryDescription']);
                if(data['isActive'] == 1) {
                    $('#isActiveMainCat').iCheck('check');
                } else {
                    $('#isActiveMainCat').iCheck('uncheck');
                }
                $('#add_new_main_category').modal('show');
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_VAT_main_category(taxVatMainCategoriesAutoID) {
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taxVatMainCategoriesAutoID': taxVatMainCategoriesAutoID},
                url: "<?php echo site_url('Tax/delete_vat_main_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    load_VAT_main_category();
                    load_VAT_sub_category();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    }

    function add_vat_sub_category()
    {
        $('#taxMasterAutoIDSubCat').val(taxMasterAutoID);
        $('#taxVatSubCategoriesAutoID').val('');
        $('#subCategoryDesc').val('');
        $('#subPercentage').val('');
        $('#isActiveSubCat').iCheck('uncheck');
        load_main_category_dropdown();
        $('#add_new_sub_category').modal('show');
    }

    function load_main_category_dropdown(mainCategory = null)
    {
        if(taxMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taxMasterAutoID': taxMasterAutoID},
                url: "<?php echo site_url('Tax/load_main_category_dropdown'); ?>",
                beforeSend: function () {},
                success: function (data) {
                    if (data) {
                        $('#mainCategoryVAT').empty();
                        var mySelect = $('#mainCategoryVAT');
                        mySelect.append($('<option></option>').val('').html('Select Main Category'));
                        if (!jQuery.isEmptyObject(data)) {
                            $.each(data, function (val, text) {
                                mySelect.append($('<option></option>').val(text['taxVatMainCategoriesAutoID']).html(text['mainCategoryDescription']));
                            });
                        }
                    }
                    if(mainCategory) {
                        $('#mainCategoryVAT').val(mainCategory).change();
                    }    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
           
        }
    }

    function Save_new_sub_category() {
        taxID = $('#taxMasterAutoIDSubCat').val();
        var data = $("#add_new_sub_category_form").serializeArray();
        if(taxID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Tax/save_vat_sub_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('#add_new_sub_category').modal('hide');
                        load_VAT_sub_category();
                    };                
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_VAT_sub_category() {
        if(taxMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taxMasterAutoID': taxMasterAutoID},
                url: "<?php echo site_url('Tax/fetch_VAT_sub_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#vat_sub_category_table_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#vat_sub_category_table_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    } else {
                        $.each(data, function (key, value) {
                            if(value['isActive'] == 1) {
                                var activelable = '<span class="label" style="background-color:#75C181; color:#ffffff; font-size: 11px;">Active</span>';
                            } else {
                                var activelable = '<span class="label" style="background-color:#EE6363; color:#ffffff; font-size: 11px;">In Active</span>';
                            }
                            $('#vat_sub_category_table_body').append(
                                '<tr>'+
                                '<td>' + x + '</td>'+
                                '<td>' + value['mainCategoryDescription'] + '</td>'+
                                '<td>' + value['subCategoryDescription'] + '</td>'+
                                '<td style="text-align: center">' + value['percentage'] + '</td>'+
                                '<td style="text-align: center">' + activelable + '</td>'+
                                '<td style="text-align: center">'+
                                    '<a class="text-yellow" onclick="assign_item(' + value['taxVatSubCategoriesAutoID'] + ');"><span title="Assign Item" rel="tooltip" class="glyphicon glyphicon-th"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;'+    
                                    '<a onclick="edit_VAT_sub_Category('+ value['taxVatSubCategoriesAutoID'] +', '+ value['taxMasterAutoID'] +')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;'+
                                    '<a class="text-yellow" onclick="delete_VAT_sub_category(' + value['taxVatSubCategoriesAutoID'] + ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>'+
                                '</td>'+
                                '</tr>'
                            );
                            x++;
                        });
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function edit_VAT_sub_Category(taxVatSubCategoriesAutoID, taxID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taxVatSubCategoriesAutoID': taxVatSubCategoriesAutoID},
            url: "<?php echo site_url('Tax/load_vat_sub_category'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#taxMasterAutoIDSubCat').val(taxID);
                $('#taxVatSubCategoriesAutoID').val(taxVatSubCategoriesAutoID);
                $('#subCategoryDesc').val(data['subCategoryDescription']);
                $('#subPercentage').val(data['percentage']);
                $('#applicableOn').val(data['applicableOn']).change();
                $('#isActiveSubCat').iCheck('uncheck');
                if(data['isActive'] == 1) {
                    $('#isActiveSubCat').iCheck('check');
                } else {
                    $('#isActiveSubCat').iCheck('uncheck');
                }
                load_main_category_dropdown(data['mainCategory']);
                $('#add_new_sub_category').modal('show');
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_VAT_sub_category(taxVatSubCategoriesAutoID) 
    {
        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
            text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taxVatSubCategoriesAutoID': taxVatSubCategoriesAutoID},
                url: "<?php echo site_url('Tax/delete_vat_sub_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    load_VAT_sub_category();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    }

    function assign_item(taxVatSubCategoriesAutoID) {
        selectedItemsSync = [];
        $('#taxVatSubCategoriesAutoID_AddItem').val(taxVatSubCategoriesAutoID);
        sync_item_table();
        item_table_view();
        $("#linkItemForDiscount").modal('show');
    }

    function LoadMainCategorySync() {
        $('#syncSubcategoryID').val("");
        $('#syncSubcategoryID option').remove();
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

    function sync_item_table() {
        oTable2 = $('#item_table_sync').DataTable({
            "ordering": false,
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Tax/fetch_VAT_add_item'); ?>",
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
                {"mData": "item_inventryCode"},
                {"mData": "seconeryItemCode"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#syncMainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#syncSubcategoryID").val()});
                aoData.push({"name": "id", "value": $("#taxVatSubCategoriesAutoID_AddItem").val()});
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
            "sAjaxSource": "<?php echo site_url('Tax/fetch_VAT_item_view'); ?>",
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
                {"mData": "seconeryItemCode"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "mainCategory", "value": $("#listMainCategoryID").val()});
                aoData.push({"name": "subcategory", "value": $("#listSubcategoryID").val()});
                aoData.push({"name": "id", "value": $("#taxVatSubCategoriesAutoID_AddItem").val()});
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
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Tax/assign_item_VAT"); ?>',
            dataType: 'json',
            data: {
                'selectedItemsSync': selectedItemsSync,
                'taxVatSubCategoriesAutoID' : $("#taxVatSubCategoriesAutoID_AddItem").val()
            },
            async: false,
            success: function (data) {
                refreshNotifications(true);
                if (data['status']) {
                    sync_item_table();
                    item_table_view();
                    selectedItemsSync = [];
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
</script>