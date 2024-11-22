<?php
$Documentid = $policy_id;
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$location_arr = all_delivery_location_drop_active();
$pID = $this->input->post('page_id');
$supplier_arr = all_supplier_drop(true,1);
$supplier_arr_master = array(''=>'Select Supplier');
if($pID != '') {
    $grvAutoid = $pID;
    $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($pID, $Documentid);
    if($supplieridcurrentdoc['isActive'] == 0)
    {
        $supplier_arr[trim($supplieridcurrentdoc['supplierAutoID'] ?? '')] = (trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') ? trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') . ' | ' : '') . trim($supplieridcurrentdoc['supplierName'] ?? '') . (trim($supplieridcurrentdoc['supplierCountry'] ?? '') ? ' | ' . trim($supplieridcurrentdoc['supplierCountry'] ?? '') : '');

    }

    $warehouseidcurrentdoc = all_warehouse_drop_isactive_inactive($pID, $Documentid);
    if(!empty($warehouseidcurrentdoc) && $warehouseidcurrentdoc['isActive'] == 0)
    {
        $location_arr[trim($warehouseidcurrentdoc['wareHouseAutoID'] ?? '')] = trim($warehouseidcurrentdoc['wareHouseCode'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseLocation'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseDescription'] ?? '');
    }
}

$warehouse_consignment = get_warehouse_according_to_type();
$location_arr_default = default_delivery_location_drop();
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$financeyear_arr = all_financeyear_drop(true);
$grvType_arr = array('' => 'Select Type', 'Standard' => 'Direct', 'PO Base' => 'PO Base', 'LOG' => 'Logistic');
$projectExist = project_is_exist();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$createmasterrecords = getPolicyValues('CMR','All');

$country = load_country_drop();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$country_arr = array('' => $this->lang->line('common_select_country'));/*Select Country*/
$customerCategory = party_category(2);
$taxGroup_arr = supplier_tax_groupMaster();
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
$jobNumberMandatory = getPolicyValues('JNP', 'All');
$jobs = all_mfq_jobs_drop(TRUE);
?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one'); ?>
        - <?php echo $Documentid === 'SRN' ? 'SRN header' : $this->lang->line('transaction_grv_header'); ?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_details();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two'); ?>
        - <?php echo $Documentid === 'SRN' ? 'SRN detail' : $this->lang->line('transaction_grv_detail'); ?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" id="step31"  href="#step3" onclick="fetch_addon_cost();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_three'); ?>
        - <?php echo $Documentid === 'SRN' ? 'SRN addon cost' : $this->lang->line('transaction_grv_add_on_cost'); ?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step4" onclick="load_conformation();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_four'); ?>
        - <?php echo $Documentid === 'SRN' ? 'SRN confirmation' : $this->lang->line('transaction_approval_grv_confirmaton'); ?></span>
            </a>
        </div>


    
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="grv_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="grvType"><?php echo $Documentid === 'SRN' ? 'SRN Type' : $this->lang->line('transaction_goods_received_voucher_grv_type'); ?><?php required_mark(); ?></label>
                <!--GRV Type-->
                <?php echo form_dropdown('grvType', $grvType_arr, 'Standard', 'class="form-control select2" id="grvType" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></label>
                <!--Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('transaction_common_referenc_no'); ?> </label>
                <!--Reference No-->
                <input type="text" class="form-control " id="referenceno" name="referenceno">
            </div>
        </div>
        <div class="row">


            <div class="form-group col-sm-4">

                <?php if($createmasterrecords==1){?>
                        <label for="supplierID">  <?php echo $this->lang->line('common_supplier'); ?><!--Supplier--> <?php required_mark(); ?></label>
                        <div class="input-group">
                            <div id="div_supplier_drop">

                                <?php echo form_dropdown('supplierID', $supplier_arr_master, '', 'class="form-control select2" id="supplierID" onchange="fetch_supplier_currency_by_id(this.value)"'); ?>
                            </div>
                            <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Supplier" rel="tooltip" onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                        </div>

                <?php }else {?>
                    <label for="supplierID"><?php echo $this->lang->line('common_supplier'); ?><?php required_mark(); ?></label>
                    <!--Supplier-->
                    <?php echo form_dropdown('supplierID', $supplier_arr, '', 'class="form-control select2" id="supplierID" onchange="fetch_supplier_currency_by_id(this.value)"'); ?>
                <?php }?>


            </div>



            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('common_currency'); ?><?php required_mark(); ?></label>
                <!--Currency-->
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'GRV\')" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="grvDate"><?php echo $this->lang->line('transaction_common_delivered_date'); ?><?php required_mark(); ?></label>
                <!--Delivered Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="deliveredDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="deliveredDate"
                           class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('transaction_common_delivery_location'); ?><?php required_mark(); ?></label>
                <input class="hidden" name="mfqWarehouseAutoID" id="mfqWarehouseAutoID">
                <!--Delivery Location-->
                <?php echo form_dropdown('location', $location_arr, $location_arr_default, 'class="form-control select2" id="location" onchange="validate_mfq_warehouse_grv(this.value)" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="contactPersonName"><?php echo $this->lang->line('transaction_common_contact_person_name'); ?> </label>
                <!--Contact Person Name-->
                <input type="text" class="form-control " id="contactPersonName" name="contactPersonName">
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('transaction_common_telephone_number'); ?> </label>
                <!--Telephone Number-->

                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <input type="text" class="form-control " id="contactPersonNumber" name="contactPersonNumber">
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            if($financeyearperiodYN==1){
            ?>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('transaction_common_financial_year'); ?><?php required_mark(); ?></label>
                <!--Financial Year-->
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear_period"><?php echo $this->lang->line('transaction_common_financial_period'); ?><?php required_mark(); ?></label>
                <!--Financial Period-->
                <?php echo form_dropdown('financeyear_period', array('' => 'Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
                <?php
            }
            ?>
            <div class="form-group col-sm-4">
                <label for="grvDate"><?php echo $Documentid === 'SRN' ? 'SRN Date' : $this->lang->line('transaction_goods_received_voucher_grv_date'); ?><?php required_mark(); ?></label>
                <!--GRV Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="grvDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="grvDate"
                           class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            if($jobNumberMandatory==1){
                ?>
                <div class="form-group col-sm-4 hidden" id="jobNumberAdd_grv">
                    <label for="financeyear"><?php echo $this->lang->line('inventory_job_number');?><?php required_mark(); ?></label><!--Job Number-->
                    <!-- <div class="div_job_number_grv">
                        <select name="jobID" id="jobID" onchange="fetchReference_number_GRV()" class="form-control select2">
                            <option value="">
                                Select Job Number</option>
                        </select>
                    </div> -->
                    <?php echo form_dropdown('jobID', $jobs, '', 'class="form-control select2" id="jobID"'); ?>
                </div>
            <?php } ?>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('transaction_common_narration'); ?></label>
                <!--Narration-->
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
            <div class="form-group col-sm-4">
                <label for="isconsignment"><?php echo $Documentid === 'SRN' ? 'Is Consignment SRN' : 'Is Consignment GRV';?></label><!-- Show Item Image-->

                <div class="skin skin-square">
                    <div class="skin-section">
                        <input id="isconsignment" type="checkbox" name="isconsignment" value="1" onchange="change_to_consignment(this)">
                        <label for="checkbox">
                            &nbsp;
                        </label>
                    </div>
                </div>    

            </div>
            <div class="form-group col-sm-4 hide" id="consignment_drop">
                <label for="segment"><?php echo 'Select Consignment Warehouse'; ?><?php required_mark(); ?></label>
                <!--Segment-->
                <?php echo form_dropdown('conID', $warehouse_consignment, '' , 'class="form-control select2" id="conID"'); ?>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary"
                    type="submit"><?php echo $this->lang->line('common_save_and_next'); ?> </button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">

    </div>
    <div id="step3" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                            class="fa fa-hand-o-right"></i><?php echo $this->lang->line('transaction_addon_cost'); ?>
                </h4><h4></h4><!--Addon Cost-->
            </div>
            <div class="col-md-4">
                <button type="button" onclick="addon_cost_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('transaction_goods_received_voucher_add_on_cost'); ?>
                </button><!--Add Addon Cost-->
            </div>
        </div>
        <br>

        <div class="table-responsive">
            <table class="<?php echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_referenc_no'); ?></th>
                    <!--Reference No-->
                    <th style="min-width: 25%"><?php echo $this->lang->line('transaction_goods_received_voucher_add_on_category'); ?></th>
                    <!--Addon Category-->
                    <th style="min-width: 20%"><?php echo $this->lang->line('common_supplier'); ?></th><!--Supplier-->
                    <th style="min-width: 15%"><?php echo $this->lang->line('transaction_common_booking_amount'); ?></th>
                    <!--Booking Amount-->

                    <th style="min-width: 15%" class="groupBasedTax hide">Tax</th>
                    <th style="min-width: 15%" class="groupBasedTax hide">Tax Amount</th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_amount'); ?> <span class="currency"> (LKR)</span>


                    </th><!--Amount-->
                    <th style="min-width: 10%">&nbsp;</th>
                </tr>
                </thead>
                <tbody id="addon_table_body">
                <tr class="danger">
                    <td class="text-center" colspan="8"><b><?php echo $this->lang->line('common_no_records_found'); ?>
                            <span class="currency"></b></td><!--No Records Found-->
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5" class="addon_total text-right">
                        <?php echo $this->lang->line('transaction_goods_received_voucher_add_on_total'); ?><!--Addons Total-->
                        <span class="currency"> ( LKR )</span></td>
                    <td id="t_total" class="total text-right">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                </tfoot>
            </table>
        </div>
        <hr>
    </div>
    <div id="step4" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="goodReceiptVoucher_attachment_label"> Modal
                title<?php /*echo $this->lang->line('transaction_goods_received_voucher_modal_title');*/ ?> </h4>
            <!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description'); ?></th><!--Description-->
                        <th><?php echo $this->lang->line('common_type'); ?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_action'); ?></th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="goodReceiptVoucher_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5"
                            class="text-center"><?php echo $this->lang->line('common_no_attachment_found'); ?> </td>
                        <!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <!-- <button class="btn btn-default prev">Previous</button> -->
            <button class="btn btn-primary "
                    onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?></button>
            <!--Save as Draft-->
            <button class="btn btn-success submitWizard"
                    onclick="confirmation()"><?php echo $this->lang->line('common_confirm'); ?></button><!--Confirm-->
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>

<?php
/** sub item master modal created by Shafry */
$this->load->view('system/grv/sub-views/inc-sub-item-master');
?>

<div class="modal fade" id="addon_cost_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="addon_cost_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"
                        id="myModalLabel"><?php echo $this->lang->line('transaction_addon_cost'); ?></h4>
                    <!--Addon Cost-->
                </div>
                <div class="modal-body">
                    <div class="form-group" style="display: none;">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('transaction_goods_received_voucher_paid_by'); ?> </label>
                        <!--Paid By-->

                        <div class="col-sm-5">
                            <?php echo form_dropdown('paid_by', array('paid_by_supplier' => $this->lang->line('transaction_goods_received_voucher_paid_by_supplier')/*'Paid By Supplier'*/, 'paid_by_company' => $this->lang->line('transaction_goods_received_voucher_paid_by_company') /*'Paid By company'*/), 'paid_by_company', 'class="form-control" id="paid_by" onchange="select_supp(this.value)" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('transaction_goods_received_voucher_add_on_category'); ?> </label>
                        <!--Addon Category-->

                        <div class="col-sm-5">
                            <?php echo form_dropdown('addonCatagory', addon_catagory(), '', 'class="form-control select2" id="addonCatagory" required'); ?>
                            <input type="hidden" class="form-control" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3"
                               class="col-sm-4 control-label"><?php echo $this->lang->line('common_supplier'); ?> </label>
                        <!--Supplier-->

                        <div class="col-sm-5">
                            <?php echo form_dropdown('supplier', all_supplier_drop(), '', 'class="form-control select2" id="supplier" required'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="referencenos"
                               class="col-sm-4 control-label"><?php echo $this->lang->line('transaction_common_referenc_no'); ?> </label>
                        <!--Reference No-->

                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="referencenos" name="referencenos">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_amount'); ?> </label>
                        <!--Amount-->

                        <div class="col-sm-2">
                            <?php echo form_dropdown('bookingCurrencyID', $currency_arr, '', 'class="form-control select2" id="bookingCurrencyID" onchange="fetch_tax_calculation()" required'); ?>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control number" id="total_amount" value="0"
                                   name="total_amount" onchange="fetch_tax_calculation()">
                        </div>
                    </div>

                    <div class="form-group groupbasedTax hide">
                        <label class="col-sm-4 control-label"> Tax</label>

                        <div class="col-sm-3">
                            <?php echo form_dropdown('taxtype', all_tax_formula_drop_groupByTax(2), '', 'class="form-control select2" id="taxtype" onchange="fetch_tax_calculation()"'); ?>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control number" id="total_tax_amount" value="0"
                                   name="total_tax_amount" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('transaction_goods_received_voucher_charge_to_expence'); ?></label>
                        <!--Charge To Expense-->

                        <div class="col-sm-5">
                            <?php echo form_dropdown('isChargeToExpense', array('1' => $this->lang->line('common_yes')/*'Yes'*/, '0' => $this->lang->line('transaction_goods_received_voucher_no')/*'No'*/), '0', 'class="form-control" id="isChargeToExpense" onchange="show_gl(this.value)" required'); ?>
                        </div>
                    </div>
                    <?php if ($projectExist == 1) { ?>
                        <div class="form-group project_showDiv">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('transaction_goods_received_project'); ?> </label>
                            <!--Project-->
                            <div class="col-sm-5">
                                <div id="edit_div_projectID_addonCost">
                                    <select name="projectID" class="form-control select2">
                                        <option value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?>  </option>
                                        <!--Select Project-->
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group show_gl" style="display:none;">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_gl_code'); ?>  </label>
                        <!--GL Code-->

                        <div class="col-sm-5">
                            <?php echo form_dropdown('GLAutoID', fetch_all_gl_codes(), '', 'class="form-control select2" id="GLAutoID"'); ?>
                        </div>
                    </div>
                    <div class="form-group impect_drp">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('transaction_goods_received_impact_for'); ?> </label>
                        <!--Impact for -->

                        <div class="col-sm-5">
                            <?php echo form_dropdown('impactFor', array('' => $this->lang->line('transaction_goods_received_all_item')/*'All Item'*/), '', 'class="form-control" id="impactFor" required'); ?>
                        </div>
                    </div>
                    <div class="form-group" style="display: none;">
                        <label for="inputPassword3"
                               class="col-sm-4 control-label"><?php echo $this->lang->line('common_description'); ?> </label>
                        <!--Description-->

                        <div class="col-sm-5">
                            <textarea class="form-control" rows="3" id="narrations" name="narrations"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button type="submit"
                            class="btn btn-primary"><?php echo $this->lang->line('common_save'); ?></button><!--Save-->
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="emp_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Add New Supplier</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <?php echo form_open('','role="form" id="suppliermaster_form"'); ?>
                <input type="hidden" id="isActive" name="isActive" value="1">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Secondary Code<!--Secondary Code--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="suppliercode" name="suppliercode">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierName">Company Name / Name<!--Company Name / Name--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="supplierName" name="supplierName" required>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierName">Name On Cheque<!--Name On Cheque--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="nameOnCheque" name="nameOnCheque">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for=""><?php echo $this->lang->line('common_category');?><!--Category--></label>
                            <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="liabilityAccount">Liability Account<!--Liability Account--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('liabilityAccount', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" id="liabilityAccount" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierCurrency">Currency<!--Currency--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="supplierCurrency" required'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for=""><?php echo $this->lang->line('common_Country');?><!--Country--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('suppliercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="suppliercountry" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">Tax Group<!--Tax Group--></label>
                            <?php echo form_dropdown('suppliertaxgroup', $taxGroup_arr, '', 'class="form-control select2"  id="suppliertaxgroup"'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">VAT Identification No</label>
                            <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="supplierTelephone"><?php echo $this->lang->line('common_telephone');?><!--Telephone--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierTelephone" name="supplierTelephone">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierEmail"><?php echo $this->lang->line('common_email');?><!--Email--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierEmail" name="supplierEmail">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierFax">FAX<!--FAX--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierFax" name="supplierFax">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="suppliersupplierCreditPeriod">Credit Period<!--Credit Period--></label>
                            <div class="input-group">
                                <div class="input-group-addon">Month<!--Month--></div>
                                <input type="text" class="form-control number" id="supplierCreditPeriod"
                                       name="supplierCreditPeriod">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="suppliersupplierCreditLimit">Credit Limit<!--Credit Limit--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><span class="currency">LKR</span></div>
                                <input type="text" class="form-control number" id="supplierCreditLimit" name="supplierCreditLimit">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierUrl">URL</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierUrl" name="supplierUrl">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="supplierAddress1">Address 1<!--Address 1--></label>
                            <textarea class="form-control" rows="2" id="supplierAddress1" name="supplierAddress1"></textarea>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierAddress2">Address 2<!--Address 2--></label>
                            <textarea class="form-control" rows="2" id="supplierAddress2" name="supplierAddress2"></textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="save_supplier_master()">Add Supplier </button>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var grvAutoID;
    var documentCurrency;
    var referenceNo = null;
    var isGroupBasedTax =  '';
    let documentId = '<?php echo $Documentid; ?>'
    $(document).ready(function () {
        $('.headerclose').click(function () {
            if('GRV' === documentId){
                fetchPage('system/grv/erp_grv_management', grvAutoID, 'Goods Received Voucher');
            }
            else{
                fetchPage('system/srn/erp_srn_management', grvAutoID, 'Service Received Note');
            }
        });

        $('.select2').select2();
        grvAutoID = null;
        documentCurrency = null;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#grv_form').bootstrapValidator('revalidateField', 'deliveredDate');
            $('#grv_form').bootstrapValidator('revalidateField', 'grvDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            grvAutoID = p_id;
            <?php if($createmasterrecords==1){?>
            fetch_supplierdrop('',grvAutoID)
            <?php }?>

            laad_grv_header();
            $("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + grvAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + grvAutoID + '/GRV');
            $('.btn-wizard').removeClass('disabled');
        } else {
            <?php if($createmasterrecords==1){?>
            fetch_supplierdrop();
            <?php }?>
            $('.btn-wizard').addClass('disabled');
        }

        $('#grvType').change(function (){
            if (this.value === 'LOG') {
                $('#step31').show();
            }
            else{
                $('#step31').hide();
            }

        })

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#addon_cost_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                addonCatagory: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_voucher_add_on_category_required');?>.'}}}, /*Addon Category is required*/
                bookingCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}}, /*Currency is required*/
                supplier: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_supplier_is_required');?>.'}}}, /*Supplier is required*/
                paid_by: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_paid_by_is_required');?>.'}}}, /*Paid By is required*/
                total_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_unit_cost_required');?>.'}}}/*Unit Cost is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'grvAutoID', 'value': grvAutoID});
            data.push({'name': 'supplier_name', 'value': $('#supplier option:selected').text()});
            data.push({'name': 'glcode_dec', 'value': $('#GLAutoID option:selected').text()});
            data.push({'name': 'booking_code', 'value': $('#bookingCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Grv/save_addon'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        $('#addon_cost_modal').modal('hide');
                        fetch_addon_cost();
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#grv_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                grvType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_grv_type_is_required');?>.'}}}, /*GRV Type is required*/
                //supplierID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_supplier_is_required');?>.'}}}, /*Supplier is required*/
                grvDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_grv_date_is_required');?>.'}}}, /*GRV Date is required*/
                deliveredDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_deliver_date_is_required');?>.'}}}, /*Delivered Date is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}}, /*Currency is required*/
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}}, /*Segment is required*/
                location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_delivery_location_is_required');?>.'}}} /*Delivery Location is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#transactionCurrencyID").prop("disabled", false);
            $("#supplierID").prop("disabled", false);
            $("#grvType").prop("disabled", false);
            $("#segment").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'grvAutoID', 'value': grvAutoID});
            data.push({'name': 'delivery_location', 'value': $('#location option:selected').text()});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            data.push({'name': 'jobNumber', 'value': $('#jobID option:selected').text()});
            data.push({'name': 'documentID', 'value': '<?php echo $Documentid; ?>'});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Grv/save_grv_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if (data['status']) {
                        if(data['mfqWarehouseAutoID']) {
                            swal({   title: "",
                                text: "Transactions Will Be Recorded to WIP GL Account!",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Ok",
                                closeOnConfirm: true 
                            });
                        }
                        $('.btn-wizard').removeClass('disabled');
                        grvAutoID = data['last_id'];
                        laad_grv_header();
                        fetch_details();
                        $("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + grvAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + grvAutoID + '/GRV');
                        $('[href=#step2]').tab('show');
                        isGroupBasedTax = data['isGroupBasedTax']
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        $('.next').click(function () {
            var nextId = $(this).parents('.tab-pane').next().attr("id");
            $('[href=#' + nextId + ']').tab('show');
        });

        $('.prev').click(function () {
            var prevId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#' + prevId + ']').tab('show');
        });
    });

    function fetch_details() {

        $("#transactionCurrencyID").prop("disabled", false);
        $("#supplierID").prop("disabled", false);
        $("#grvType").prop("disabled", false);

        grvType = $("#grvType").val();
        supplierID = $("#supplierID").val();
        currencyID = $("#transactionCurrencyID").val();
        fetch_detail(grvType, supplierID, currencyID);

        $("#transactionCurrencyID").prop("disabled", true);
        $("#supplierID").prop("disabled", true);
        $("#grvType").prop("disabled", true);
    }

    function addon_cost_modal() {
        var mfqWarehouse = $('#mfqWarehouseAutoID').val();
        var jobID = $('#jobID').val();
        if(mfqWarehouse != '' && jobID == '') {
            myAlert('e', 'Please Assign Job Before adding Details!');
        } else {
            //$('.project_showDiv').hide();
            $("#addonCatagory").val('').trigger('change');
            $("#supplier").val('').trigger('change');
            $("#bookingCurrencyID").val('').trigger('change');
            $("#projectID").val('').trigger('change');
            $('#addon_cost_form')[0].reset();
            $('#addon_cost_form').bootstrapValidator('resetForm', true);
            fetch_all_item();
            $('#supplier').attr("readonly", false);
            $("#id").val("");
            $("#paid_by").val("paid_by_company");
            $("#addon_qty").val(1);
            $("#isChargeToExpense").val(0);
            $("#addon_uom").val("Each");
            load_segmentBase_projectID_addonCost();
            $("#addon_cost_modal").modal({backdrop: "static"});
        }
    }

    function fetch_all_item(select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'grvAutoID': grvAutoID},
            url: "<?php echo site_url('Grv/fetch_all_item'); ?>",
            success: function (data) {
                $('#impactFor').empty();
                var mySelect = $('#impactFor');
                mySelect.append($('<option></option>').val('0').html('All Item'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['grvDetailsID']).html(text['itemSystemCode'] + '-' + text['itemDescription']));
                    });
                    if (select_value) {
                        $("#impactFor").val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID,'documentID':'GRV'},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function select_supp(val) {
        if (val == 'paid_by_supplier') {
            $('#supplier').val(supplierID);
            $('#supplier').attr("readonly", true);
        } else {
            $('#supplier').attr("readonly", false);
        }
    }

    function show_gl(val) {
        $('.show_gl').hide();
        $('.impect_drp').show();
        if (val == 1) {
            $('.show_gl').show();
            $('.impect_drp').hide();
            $('.project_showDiv').show();
        } else {
            $('.project_showDiv').hide();
        }

    }

    function fetch_addon_cost() {
        isGroupBasedTax = $('#isGroupBasedTaxEnable').val();

        if (grvAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'grvAutoID': grvAutoID},
                url: "<?php echo site_url('Grv/fetch_addons'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#addon_table_body').empty();
                    $('#t_total').html(0);
                    x = 1;

                    if(isGroupBasedTax == 1){
                        $('.groupbasedTax').removeClass('hide');
                        $('.groupBasedTax').removeClass('hide');
                    }else {
                        $('.groupbasedTax').addClass('hide');
                        $('.groupBasedTax').addClass('hide');
                    }


                    if (jQuery.isEmptyObject(data)) {
                        if(isGroupBasedTax == 1) {
                            $('#addon_table_body').append('<tr class="danger"><td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                            $('.addon_total').attr('colspan', 7);
                        }else{
                            $('#addon_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                        }
                        <!--No Records Found-->
                    }
                    else {
                        tot_amount = 0;
                        receivedQty = 0;
                        currency_decimal = 2;
                        var Tax  = '';
                        var TaxAmount  = '';
                        var TaxAmountcal  = 0;
                        $.each(data, function (key, value){

                            if(isGroupBasedTax == 1){

                                if(parseFloat(value['taxAmount'])>0){
                                    Tax = '<td>'+value['description']+'</td>';
                                    TaxAmount = '<td style="text-align: right"><a onclick="open_tax_dd(\'\',' + grvAutoID + ',\'GRV-ADD\',' + value['bookingCurrencyDecimalPlaces'] +', '+ value['id'] +', \'srp_erp_grv_addon\', \'id\',1)">'+parseFloat(value['taxAmount']).formatMoney(value['bookingCurrencyDecimalPlaces'], '.', ',')+'</a> </td>';
                                }else{
                                    Tax = '<td>-</td>';
                                    TaxAmount = '<td style="text-align: right">0</td>';
                                }
                                TaxAmountcal = parseFloat(value['taxAmount']);
                                $('.addon_total').attr('colspan', 7);
                            }else {
                                $('.addon_total').attr('colspan', 5);
                            }

                            $('#addon_table_body').append('<tr><td>' + x + '</td><td>' + value['referenceNo'] + '</td><td>' + value['addonCatagory'] + '</td><td>' + value['supplierName'] + '</td><td class="text-right" >' + value['bookingCurrency'] + ' : ' + parseFloat(value['bookingCurrencyAmount']).formatMoney(value['bookingCurrencyDecimalPlaces'], '.', ',') + '</td>'+Tax+' '+TaxAmount+'<td class="text-right">' + (parseFloat(value['total_amount'])+ parseFloat(TaxAmountcal)).formatMoney(value['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right"><a onclick="edit_addon_cost_model(' + value['id'] + ')"><span class="glyphicon glyphicon-pencil" style="color:blue;"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_addon(' + value['id'] + ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></td></tr>');
                            tot_amount += (parseFloat(value['total_amount'])+ parseFloat(TaxAmountcal))
                            currency_decimal = value['transactionCurrencyDecimalPlaces'];
                            x++;
                        });
                        $('#t_total').html(parseFloat(tot_amount).formatMoney(currency_decimal, '.', ','));
                    }

                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    }

    function fetch_supplier_currency_by_id(supplierAutoID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierAutoID': supplierAutoID},
            url: "<?php echo site_url('Procurement/fetch_supplier_currency_by_id'); ?>",
            success: function (data) {
                if (documentCurrency) {
                    $("#transactionCurrencyID").val(documentCurrency).change()
                } else {
                    if (data.supplierCurrencyID) {
                        $("#transactionCurrencyID").val(data.supplierCurrencyID).change();
                        //currency_validation_modal(data.supplierCurrencyID,'GRV',supplierAutoID,'SUP');
                    }
                }

            }
        });
    }

    function currency_validation(CurrencyID, documentID) {
        if (CurrencyID) {
            partyAutoID = $('#supplierID').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'SUP');
        }
    }

    function laad_grv_header() {
        if (grvAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'grvAutoID': grvAutoID},
                url: "<?php echo site_url('Grv/load_grv_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        referenceNo = data['grvDocRefNo'];
                        $('.currency').html('( ' + data['transactionCurrency'] + ' )');
                        $('#grvType').val(data['grvType']).change();
                        $('#grvDate').val(data['grvDate']);
                        $('#deliveredDate').val(data['deliveredDate']);
                        documentCurrency = data['transactionCurrencyID'];
                        $("#supplierID").val(data['supplierID']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#narration').val(data['grvNarration']);
                        $('#location').val(data['wareHouseAutoID']).change();
                        $('#mfqWarehouseAutoID').val(data['mfqWarehouseAutoID']);
                        $('#contactPersonName').val(data['contactPersonName']);
                        $('#contactPersonNumber').val(data['contactPersonNumber']);
                        $('#referenceno').val(data['grvDocRefNo']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        setTimeout(function () {
                            $("#jobID").val(data['jobID']).change();
                        }, 1000);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        fetch_detail(data['grvType'], data['supplierID'], data['transactionCurrencyID']);
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');

                        if(data['isconsignment'] == 1){
                            $('#isconsignment').prop('checked',true).change();
                        }else{
                            $('#isconsignment').prop('checked',false).change();
                        }

                        $('#conID').val(data['consignmentWarehouseID']).change();

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

    function fetch_detail(type, supplierID, currencyID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'grvAutoID': grvAutoID, 'grvType': type, 'supplierID': supplierID, 'currencyID': currencyID},
            url: "<?php echo site_url('Grv/fetch_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#step2').html(data);
                check_detail_dataExist(grvAutoID);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function check_detail_dataExist(grvAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'grvAutoID': grvAutoID},
            url: "<?php echo site_url('Grv/fetch_detail_header_lock'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (jQuery.isEmptyObject(data)) {
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#supplierID").prop("disabled", false);
                    $("#grvType").prop("disabled", false);
                    $("#segment").prop("disabled", false);
                    $("#addcustomer").prop("disabled", false);
                } else {
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#supplierID").prop("disabled", true);
                    $("#grvType").prop("disabled", true);
                    $("#segment").prop("disabled", true);
                    $("#addcustomer").prop("disabled", true);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_conformation() {
        if (grvAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'grvAutoID': grvAutoID, 'html': true},
                url: "<?php echo site_url('Grv/load_grv_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + grvAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + grvAutoID + '/' + '<?php echo $Documentid; ?>');
                    let documentName = '<?php echo $this->lang->line('transaction_good_received_note');?>';
                    if ('<?php echo $Documentid; ?>' === 'SRN'){
                        documentName = 'Service Received Note';
                    }
                    attachment_modal_purchaseOrder(grvAutoID, documentName, '<?php echo $Documentid; ?>');
                    /*Good Received Note*/
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        if (grvAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'grvAutoID': grvAutoID},
                        url: "<?php echo site_url('Grv/grv_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            } else if (data['error'] == 2) {
                                myAlert('w', data['message']);
                            }else if (data['error'] == 4){ 
                                
                             $('#wac_minus_calculation_validation_body').empty();
                             x = 1;
                             if (jQuery.isEmptyObject(data['message'])) {
                                 $('#wac_minus_calculation_validation_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                            } else {
                                $.each(data['message'], function (key, value) {
                       
                                 $('#wac_minus_calculation_validation_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>'+value['itemName']+'</td><td>' + value['Amount'] + '</td></tr>');
                                x++;
                      
        
                            });
                            }
                                $('#wac_minus_calculation_validation').modal('show');
                            }
                            else {
                                myAlert('s', data['message']);
                                if('GRV' === documentId){
                                    fetchPage('system/grv/erp_grv_management', grvAutoID, 'Goods Received Voucher');
                                }
                                else{
                                    fetchPage('system/srn/erp_srn_management', grvAutoID, 'Service Received Note');
                                }
                                refreshNotifications(true);
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function save_draft() {
        if (grvAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>", /*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>", /*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/grv/erp_grv_management', grvAutoID, 'Goods Received Voucher');
                });
        }
        ;
    }

    function edit_addon_cost_model(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'id': id},
            url: "<?php echo site_url('Grv/get_addon_details_projectBase'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#addon_cost_form')[0].reset();
                    $('#addon_cost_form').bootstrapValidator('resetForm', true);
                    $("#addon_cost_modal").modal({backdrop: "static"});
                    $("#id").val(data['id']);
                    $("#addonCatagory").val(data['addonCatagory']);
                    $("#GLAutoID").val(data['GLAutoID']);
                    $("#narrations").val(data['narrations']);
                    $("#isChargeToExpense").val(data['isChargeToExpense']);
                    $("#bookingCurrencyID").val(data['bookingCurrencyID']);
                    fetch_all_item(data['impactFor']);
                    show_gl(data['isChargeToExpense']);
                    $("#referencenos").val(data['referenceNo']);
                    $('#supplier').val(data['supplierID']);
                    $('#addon_uom').val(data['unitOfMeasure']);
                    $('#addon_qty').val(data['qty']);
                    $('#paid_by').val(data['paidBy']);
                    $('#total_amount').val(data['bookingCurrencyAmount']);

                    $('#taxtype').val(data['taxCalculationformulaID']).change();
                    $('#total_tax_amount').val(data['taxAmount']);

                    load_segmentBase_projectID_addonCost_Edit(data['segmentID'], data['projectID'])
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_addon(id) {
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
                    data: {'id': id},
                    url: "<?php echo site_url('Grv/delete_addondetails'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        fetch_addon_cost();
                    },
                    error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function attachment_modal_purchaseOrder(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#goodReceiptVoucher_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");
                    <!--Attachments-->
                    $('#goodReceiptVoucher_attachment').empty();
                    $('#goodReceiptVoucher_attachment').append('' +data+ '');

                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_goodReceiptVoucher_attachement(attachmentID, DocumentSystemCode, myFileName) {
        if (attachmentID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>", /*You want to delete this attachment file!*/
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
                        data: {'attachmentID': attachmentID, 'myFileName': myFileName},
                        url: "<?php echo site_url('Attachment/delete_attachment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                attachment_modal_purchaseOrder(DocumentSystemCode, "Good Received Note", "GRV");
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function load_segmentBase_projectID_addonCost() {
        var segment = $('#segment').val();
        var type = 'item';
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase"); ?>',
            dataType: 'html',
            data: {segment: segment, type: type},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#edit_div_projectID_addonCost').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_segmentBase_projectID_addonCost_Edit(segment, selectValue) {
        var type = 'item';
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase"); ?>',
            dataType: 'html',
            data: {segment: segment, type: type},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#edit_div_projectID_addonCost').html(data);
                $('.select2').select2();
                if (selectValue) {
                    $('#projectID_item').val(selectValue).change();
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
    function link_employee_model() {
        $('#suppliercode').val('');
        $('#supplierName').val('');
        $('#vatIdNo').val('');
        $('#supplierTelephone').val('');
        $('#nameOnCheque').val('');
        $('#supplierEmail').val('');
        $('#supplierFax').val('');
        $('#supplierCreditPeriod').val('');
        $('#supplierCreditLimit').val('');
        $('#supplierUrl').val('');
        $('#supplierAddress2').val('');
        $('#supplierAddress1').val('');
        $('#partyCategoryID').val(null).trigger('change');
        $('#liabilityAccount').val('<?php echo $this->common_data['controlaccounts']['APA']?>').change();
        $('#supplierCurrency').val('<?php echo $this->common_data['company_data']['company_default_currencyID']?>').change();
        $('#suppliercountry').val('<?php echo $this->common_data['company_data']['company_country']?>').change();
        $('#suppliertaxgroup').val(null).trigger('change');
        $('#emp_model').modal('show');

    }
    function changecreditlimitcurr() {
        var currncy;
        var split;
        currncy = $('#supplierCurrency option:selected').text();
        split = currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#supplierCurrency').val();
        currency_validation_modal(CurrencyID, 'SUP', '', 'SUP');
    }
    function save_supplier_master() {
        var data = $('#suppliermaster_form').serializeArray();
        data.push({'name': 'currency_code', 'value': $('#supplierCurrency option:selected').text()});
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Supplier/save_suppliermaster'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if(data['status'] == true)
                {
                    $('#emp_model').modal('hide');
                    fetch_supplierdrop(data['last_id'],' ');
                    fetch_supplier_currency_by_id(data['last_id']);

                }else
                {
                    $('#emp_model').modal('show');

                }
                stopLoad();
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                $('#emp_model').modal('show');
                refreshNotifications(true);
            }
        });


    }
    function fetch_supplierdrop(id,Grvautoid) {
        var supplier_id;
        var page = '';

        if(id)
        {
            supplier_id = id
        }else
        {
            supplier_id = '';
        }
        if(Grvautoid)
        {
            page = Grvautoid
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {supplier:supplier_id,DocID:page,Documentid:'GRV'},
            url: "<?php echo site_url('Procurement/fetch_supplier_Dropdown_all_grv'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_supplier_drop').html(data);
                stopLoad();
                $('.select2').select2();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function validate_mfq_warehouse_grv(warehouseAutoID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'warehouseAutoID': warehouseAutoID},
            url: "<?php echo site_url('inventory/check_mfq_warehouse'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['status'] === 1)
                {
                    if(data['mfqWarehouseAutoID']) {
                        $('#mfqWarehouseAutoID').val(data['mfqWarehouseAutoID']);
                    } else {
                        $('#mfqWarehouseAutoID').val();
                    }

                    $('.div_job_number_grv').html(data['dropdown']);
                    $('.select2').select2();
                    $('#jobNumberAdd_grv').removeClass('hidden');
                    $('#jobID').val('').change();
                    $('#jobID').attr('onchange', 'fetchReference_number()');
                } else {
                    $('#jobNumberAdd_grv').addClass('hidden');
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function fetchReference_number()
    {
        var jobID = $('#jobID').val();
        if(referenceNo) {
            $('#referenceno').val(referenceNo);
        } else if(jobID) {
            $('#referenceno').val($('#jobID option:selected').text());
        }
    }
    function fetch_tax_calculation(){
        var amount = $('#total_amount').val();
        var Tax = $('#taxtype').val();
        var Totalamount = amount ? amount : 0;
        var bookingCurrencyID = $('#bookingCurrencyID').val();
        if(Totalamount > 0 && Tax!=''){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'grvAutoID': grvAutoID,
                    'applicableAmnt': Totalamount,
                    'taxtype': Tax,
                    'discount': 0,
                    'bookingCurrencyID':bookingCurrencyID
                },
                url: "<?php echo site_url('Grv/load_line_tax_amount_grv'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $('#total_tax_amount').val((data));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else {
            $('#total_tax_amount').val(parseFloat(0));
        }

    }

    function change_to_consignment(ev){
      
      if($(ev).is(':checked')){
          $('#consignment_drop').removeClass('hide');

      }else{
          $('#consignment_drop').addClass('hide');
      }
          
    }
</script>