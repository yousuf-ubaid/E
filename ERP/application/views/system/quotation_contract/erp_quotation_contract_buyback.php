<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$SalesPerson = all_sales_person_drop();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_quotation_contract');
echo head_page($title, false);

/*echo head_page('New Quotation / Contract', false);*/

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$umo_arr = array('' => 'Select UOM'); //all_umo_drop();
$customer_arr = all_customer_drop();
$segment_arr = fetch_segment();
$projectExist = project_is_exist();
?>
<style>
    .chkboxlabl {
        border:1px solid #ccc;
        padding:10px;
        margin:0 0 10px;
        display:block;
        font-weight: normal;
    }

    .chkboxlabl:hover {
        background:#eee;
        cursor:pointer;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>" />
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('sales_markating_transaction_step_one');?> - <?php echo $this->lang->line('sales_markating_transaction_header');?></a><!--Step 1 - Header-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_detail_table();" data-toggle="tab"><?php echo $this->lang->line('sales_markating_transaction_step_two');?> - <?php echo $this->lang->line('sales_markating_transaction_detail');?> </a><!--Step 2 -
        Detail-->
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab"><?php echo $this->lang->line('sales_markating_transaction_step_three');?> - <?php echo $this->lang->line('sales_markating_transaction_confirmation');?> </a><!--Step 3 -
        Confirmation-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="quotation_contract_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_type');?> <?php required_mark(); ?></label><!--Document Type-->
                <?php echo form_dropdown('contractType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'Quotation' => $this->lang->line('sales_markating_transaction_quotation')/*'Quotation'*/, 'Contract' =>$this->lang->line('sales_markating_transaction_contract') /*'Contract'*/, 'Sales Order' => $this->lang->line('sales_markating_transaction_sales_order')/*'Sales Order'*/), 'Sales Order', 'class="form-control select2" onchange="load_default_note()" id="contractType" required'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></label><!--Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment" required'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo $this->lang->line('common_document_date');?><?php required_mark(); ?></label><!--Document Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="contractDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="contractDate"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_expiry_date');?> <?php required_mark(); ?></label><!--Document Expiry Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="contractExpDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo date('d-m-Y', strtotime($current_date . ' +1 day')); ?>" id="contractExpDate"
                           class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_reference');?> # </label><!--Reference-->
                <input type="text" name="referenceNo" id="referenceNo" class="form-control">
            </div>
            <div class="form-group col-sm-4">
                <label for="customerID"><span id="party_text"><?php echo $this->lang->line('common_customer_name');?> </span> <?php required_mark(); ?></label><!--Customer Name-->
                <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID" required onchange="Load_customer_currency(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('sales_markating_transaction_document_document_currency');?>  <?php required_mark(); ?></label><!--Document Currency -->
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="currency_validation(this.value)" id="transactionCurrencyID" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="contactPersonName"><?php echo $this->lang->line('sales_markating_transaction_document_contact_person_name');?> </label><!--Contact Person Name-->
                <input type="text" class="form-control " id="contactPersonName" name="contactPersonName">
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('sales_markating_transaction_document_persons_telephone_number');?> </label><!--Person's Telephone Number-->

                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <input type="text" class="form-control " id="contactPersonNumber" name="contactPersonNumber">
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_narration');?> </label><!--Narration-->
                <textarea class="form-control" rows="3" name="contractNarration" id="contractNarration"></textarea>
            </div>
            <div class="form-group col-sm-4">
                <label>Sales Person</label><!--Narration-->
                <?php echo form_dropdown('salesperson', $SalesPerson, '', 'class="form-control select2" id="salesperson"'); ?>
            </div>
        </div>
        <div class="row">
        <div class="form-group col-sm-12">
            <label><?php echo $this->lang->line('common_notes');?> </label><!--Notes-->
            <textarea class="form-control" rows="7" name="Note" id="Note"></textarea>
        </div>
        </div>
        <button class="btn btn-primary" type="button" onclick="open_all_notes()"><i class="fa fa-bookmark" aria-hidden="true"></i></button>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_markating_transaction_document_item_detail');?> </h4><h4></h4></div><!--Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i><?php echo $this->lang->line('sales_markating_transaction_document_add_item');?> <!--Add Item-->
                </button>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped table-condesed">
            <thead>
            <tr>
                <th colspan="6"> <?php echo $this->lang->line('sales_markating_transaction_document_item_details');?></th><!--Item Details-->
                <th colspan="4"><?php echo $this->lang->line('common_amount');?><!--Amount--> <span class="currency">(LKR)</span></th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?></th><!--Code-->
                <th style="min-width: 25%" class="text-left"><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                <th style="min-width: 5%"><?php echo $this->lang->line('sales_markating_transaction_document_uom');?> </th><!--UOM-->
                <th style="min-width: 5%"><?php echo $this->lang->line('sales_marketing_no_of_item');?>   </th><!--No Item-->
                <th style="min-width: 5%"><?php echo $this->lang->line('sales_markating_transaction_document_qty');?> </th><!--Qty-->
                <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_document_unit');?> </th><!--Unit-->
                <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_discount');?> </th><!--Discount-->
                <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_net_unit_price');?> </th><!--Net Unit Price-->
                <th style="min-width: 12%"><?php echo $this->lang->line('common_total');?> </th><!--Total-->
                <th style="min-width: 8%"><?php echo $this->lang->line('common_action');?> </th><!--Action-->
            </tr>
            </thead>
            <tbody id="table_body">
            <tr class="danger">
                <td colspan="11" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
            </tr>
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table>
        <br>

        <div class="row">
            <div class="col-md-5">
                <label for="exampleInputName2" id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for');?>  </label><!--Tax for-->

                <form class="form-inline" id="tax_form">
                    <div class="form-group">
                        <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this)" style="width: 150px;"'); ?>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control number" id="percentage" name="percentage"
                                   style="width: 80px;" onkeyup="cal_tax(this.value)">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                               style="width: 100px;" onkeyup="cal_tax_amount(this.value)">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                </form>
            </div>
            <div class="col-md-7">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('sales_markating_transaction_net_tax_type');?> </th><!--Tax Type-->
                        <th><?php echo $this->lang->line('sales_markating_transaction_detail');?> </th><!--Detail-->
                        <th><?php echo $this->lang->line('sales_markating_transaction_tax');?> </th><!--Tax-->
                        <th><?php echo $this->lang->line('common_amount');?><span class="currency">(LKR)</span></th><!--Amount-->
                        <th style="width: 75px !important;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="tax_table_body_recode">

                    </tbody>
                    <tfoot id="tax_table_footer">

                    </tfoot>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary next" onclick="load_conformation();"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <!--<div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank"
                   href="<?php /*echo site_url('Quotation_contract/load_contract_conformation/'); */?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div>-->
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="customerInvoice_attachment_label"><?php echo $this->lang->line('sales_markating_transaction_model_title');?> </h4><br><!--Modal title-->

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name');?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                        <th><?php echo $this->lang->line('common_type');?> </th><!--Type-->
                        <th><?php echo $this->lang->line('common_action');?>  </th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="customerInvoice_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?> </td><!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?></button><!--Previous-->
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?> </button><!--Save as Draft-->
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?> </button><!--Confirm-->
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" tabindex="-1" id="item_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Item Detail</h5>
            </div>
            <form role="form" id="item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-striped table-condesed" id="item_add_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_item_code');?> / <?php echo $this->lang->line('common_description');?> <?php required_mark(); ?></th><!--Item Code--><!--/--><!--Description-->
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_item_ref');?>  </th><!--Item ref-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_document_uom');?> <?php required_mark(); ?></th><!--UOM-->
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_marketing_no_of_item');?> </th><!--No of item-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty');?> <?php required_mark(); ?></th><!--Qty-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_waited_average_cost');?></th><!--WAC Cost-->
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_sales_price');?><!--Sales Price--> <span
                                    class="currency"> (LKR)</span><?php required_mark(); ?></th>
                            <th colspan="2" style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_discount');?> %</th><!--Discount-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_net_unit_cost');?> </th><!--Net Unit Cost-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_net_amount');?>  </th><!--Net Amount-->
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment');?> </th><!--Comment-->
                            <th style="display: none;"><?php echo $this->lang->line('sales_markating_transaction_remarks');?> </th><!--Remarks-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_item()">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeydown="clearitemAutoID(event,this)" class="form-control search f_search" name="search[]" id="f_search_1"
                                       placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_id');?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_code');?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_description');?>..."><!--Item ID--><!--Secondary Item Code--><!-- Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td>
                                <input type="text"  name="itemReferenceNo[]" class="form-control itemReferenceNo"/>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_item">
                                        <select name="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                            <?php } ?>
                            <td>
                                <select name="UnitOfMeasureID[]" class="form-control umoDropdown" disabled required'>
                                <option value=""><?php echo $this->lang->line('sales_markating_transaction_secondary_select_uom');?> </option><!--Select UOM-->
                                </select>
                            </td>
                            <td>
                                <input type="text" name="noOfItems[]" value="0"
                                       class="form-control noOfItems number"/>
                            </td>
                            <td>
                                <input type="text" name="quantityRequested[]" value="0" onfocus="this.select();" onkeyup="change_qty(this)"
                                       class="form-control quantityRequested number"/>
                            </td>
                            <td>&nbsp;<span class="wac_cost pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td>
                                <input type="text" name="estimatedAmount[]"  value="0"
                                       onkeyup="change_amount(this)" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number estimatedAmount">
                            </td>
                            <td style="width: 100px;">
                                <div class="input-group">
                                    <input type="text" name="discount[]"  value="0"
                                           onkeyup="cal_discount(this)" onfocus="this.select();" class="form-control number discount">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 100px;">
                                <input type="text" name="discount_amount[]" value="0"
                                       onkeyup="cal_discount_amount(this)" onfocus="this.select();" class="form-control number discount_amount">
                            </td>
                            <td>&nbsp;<span id="net_unit_cost" class="net_unit_cost pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td>&nbsp;<span class="net_amount pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td>
                                <textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_comment');?>..."></textarea><!--Item Comment-->
                            </td>
                            <td style="display: none;">
                                <textarea class="form-control" rows="1" name="remarks[]"
                                          placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_remarks');?>..."></textarea><!--Item Remarks-->
                            </td>
                            <td class="remove-td"
                                style="vertical-align: middle;text-align: center;display: block;"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="saveItemOrderDetail_buyback()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="edit_item_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_edit_item_details');?> </h4><!--Edit Item Detail-->
            </div>
            <form role="form" id="edit_item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-striped table-condesed" id="edit_item_add_form">
                        <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_item_code');?> / <?php echo $this->lang->line('common_description');?><?php required_mark(); ?></th><!--Item Code / Description-->
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_item_ref');?></th><!--Item ref-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_document_uom');?> <?php required_mark(); ?></th><!--UOM-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_marketing_no_of_item');?></th><!--Qty-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty');?> <?php required_mark(); ?></th><!--Qty-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_waited_average_cost');?> </th><!--WAC Cost-->
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_sales_price');?><!--Sales Price--> <span
                                    class="currency"> (LKR)</span><?php required_mark(); ?></th>
                            <th colspan="2" style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_discount');?> %</th><!--Discount-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_net_unit_cost');?>  </th><!--Net Unit Cost-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_net_amount');?> </th><!--Net Amount-->
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment');?>  </th><!--Comment-->
                            <th style="display: none;"><?php echo $this->lang->line('sales_markating_transaction_remarks');?> </th><!--Remarks-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeypress="clearitemAutoIDEdit(event,this)" class="form-control" name="search" id="search"
                                       placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_id');?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_code');?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_description');?>..."><!--Item ID,Secondary Item Code, Item Description-->
                                <input type="hidden" class="form-control" id="edit_itemAutoID" name="itemAutoID">
                            </td>
                            <td>
                                <input type="text" name="itemReferenceNo" id="edit_itemReferenceNo"
                                       class="form-control"/>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                            <?php } ?>
                            <td>
                                <?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" id="edit_UnitOfMeasureID" required'); ?>
                            </td>
                            <td>
                                <input type="text" name="noOfItems" id="edit_noOfItems" value="0"
                                       class="form-control noOfItems number"/>
                            </td>
                            <td>
                                <input type="text" name="quantityRequested" value="0" onkeyup="edit_change_qty()"
                                       id="edit_quantityRequested" onfocus="this.select();" class="form-control number">
                            </td>
                            <td>&nbsp;<span id="edit_wac_cost" class="pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td>
                                <input type="text" name="estimatedAmount" id="edit_estimatedAmount"
                                       onkeyup="edit_change_amount()" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" value="0" class="form-control number">
                            </td>
                            <td style="width: 100px;">
                                <div class="input-group">
                                    <input type="text" name="discount"  value="0"
                                           id="edit_discount" onfocus="this.select();" onkeyup="edit_cal_discount(this.value)"
                                           class="form-control number">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 100px;">
                                <input type="text" name="discount_amount" id="edit_discount_amount"
                                       onkeyup="edit_cal_discount_amount()" onfocus="this.select();" value="0"
                                       class="form-control number">
                            </td>
                            <td>&nbsp;<span id="edit_net_unit_cost" class="pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td>&nbsp;<span id="edit_totalAmount" class="pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td>
                                <textarea class="form-control" rows="1" name="comment" placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_comment');?>..."
                                          id="edit_comment"></textarea><!--Item Comment-->
                            </td>
                            <td style="display: none">
                                <textarea class="form-control" rows="1" name="remarks" placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_remarks');?>..."
                                          id="edit_remarks"></textarea><!--Item Remarks-->
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="updateItemOrderDetail()"><?php echo $this->lang->line('common_update_changes');?><!--Update changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="all_notes_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Notes</h5>
            </div>
            <div class="modal-body">
                <form role="form" id="all_notes_form" class="form-group">
                    <div id="allnotebody">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_notes()">Add Note</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    var search_id = 1;
    var contractAutoID;
    var contractDetailsAutoID;
    var contractType;
    var customerID;
    var currencyID;
    var tax_total;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/quotation_contract/quotation_contract_management_buyback', contractAutoID, 'Customer Quotation_contract');
        });
        $('.select2').select2();
        contractAutoID = null;
        contractDetailsAutoID = null;
        contractType = null;
        customerID = null;
        currencyID = null;
        segment = null;
        initializeitemTypeahead();
        initializeitemTypeahead_edit();
        number_validation();

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#quotation_contract_form').bootstrapValidator('revalidateField', 'contractDate');
            $('#quotation_contract_form').bootstrapValidator('revalidateField', 'contractExpDate');
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            contractAutoID = p_id;
            load_contract_header();
        } else {
            $("#Note").wysihtml5();
            $('.btn-wizard').addClass('disabled');
            load_default_note();
        }

        $('#tax_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                tax_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_tax_amount_required');?>.'}}},/*Tax Amount is required*/
                text_type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_tax_type_required');?>.'}}},/*Tax Type is required*/
                percentage: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_percentage_is_required');?>.'}}}/*Percentage is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'contractAutoID', 'value': contractAutoID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_inv_tax_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data['type'], data['data'], 1000);
                    stopLoad();
                    if (data['status']) {
                        $form.bootstrapValidator('resetForm', true);
                        setTimeout(function () {
                            fetch_detail_table();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#quotation_contract_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                contractType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_contract_type_is_required');?>.'}}},/*Contract Type is required*/
                contractDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_contract_date_is_required');?>.'}}},/*Contract Date is required*/
                contractExpDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_contract_exp_is_required');?>.'}}},/*Contract Exp Date is required.*/
                customerID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_customer_is_required');?>.'}}},/*Customer is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_currency_is_required');?>.'}}},
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}}/*Segment is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#contractType").prop("disabled", false);
            $("#customerID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#segment").prop("disabled", false);

            var referenceNo = $("#referenceNo").val();
            if (referenceNo == null || referenceNo == 0) {
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                data.push({'name': 'contractAutoID', 'value': contractAutoID});
                data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Quotation_contract/save_quotation_contract_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data['status']) {
                            contractAutoID = data['last_id'];
                            contractType = $('#contractType').val();
                            customerID = $('#supplier').val();
                            currencyID = $('#transactionCurrencyID').val();
                            $("#a_link").attr("href", "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>/" + contractAutoID);
                            $('[href=#step2]').tab('show');
                            $('.btn-wizard').removeClass('disabled');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                            fetch_detail_table();
                        }

                    },
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }else{

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'referenceNo': referenceNo, 'contractAutoID':contractAutoID},
                    url: "<?php echo site_url('Quotation_contract/fetch_referenceNo'); ?>",
                    beforeSend: function () {
                      //  $(':input[type="submit"]').prop('disabled', true);
                    },
                    success: function (data) {
                        //$(':input[type="submit"]').prop('disabled', false);
                        if (data['isExist'] == 1) {
                            bootbox.confirm('<div style="font-size: 18px; color: #bc9d00; line-height: 30px;"><strong><i class="fa fa-check fa-2x"></i> Confirmation </strong> <br/>Reference No '+referenceNo+' already exist. Are you sure want to proceed? </div>', function (result) {
                                if (result) {

                                    var $form = $(e.target);
                                    var bv = $form.data('bootstrapValidator');
                                    var data = $form.serializeArray();
                                    data.push({'name': 'contractAutoID', 'value': contractAutoID});
                                    data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
                                    $.ajax({
                                        async: true,
                                        type: 'post',
                                        dataType: 'json',
                                        data: data,
                                        url: "<?php echo site_url('Quotation_contract/save_quotation_contract_header'); ?>",
                                        beforeSend: function () {
                                            startLoad();
                                        },
                                        success: function (data) {
                                            stopLoad();
                                            refreshNotifications(true);
                                            if (data['status']) {
                                                contractAutoID = data['last_id'];
                                                contractType = $('#contractType').val();
                                                customerID = $('#supplier').val();
                                                currencyID = $('#transactionCurrencyID').val();
                                                $("#a_link").attr("href", "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>/" + contractAutoID);
                                                $('[href=#step2]').tab('show');
                                                $('.btn-wizard').removeClass('disabled');
                                                $('a[data-toggle="tab"]').removeClass('btn-primary');
                                                $('a[data-toggle="tab"]').addClass('btn-default');
                                                $('[href=#step2]').removeClass('btn-default');
                                                $('[href=#step2]').addClass('btn-primary');
                                                fetch_detail_table();
                                            }

                                        },
                                        error: function () {
                                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                                            stopLoad();
                                            refreshNotifications(true);
                                        }
                                    });
             

                                }
                            });

                        } else {
                            var $form = $(e.target);
                            var bv = $form.data('bootstrapValidator');
                            var data = $form.serializeArray();
                            data.push({'name': 'contractAutoID', 'value': contractAutoID});
                            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
                            $.ajax({
                                async: true,
                                type: 'post',
                                dataType: 'json',
                                data: data,
                                url: "<?php echo site_url('Quotation_contract/save_quotation_contract_header'); ?>",
                                beforeSend: function () {
                                    startLoad();
                                },
                                success: function (data) {
                                    stopLoad();
                                    refreshNotifications(true);
                                    if (data['status']) {
                                        contractAutoID = data['last_id'];
                                        contractType = $('#contractType').val();
                                        customerID = $('#supplier').val();
                                        currencyID = $('#transactionCurrencyID').val();
                                        $("#a_link").attr("href", "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>/" + contractAutoID);
                                        $('[href=#step2]').tab('show');
                                        $('.btn-wizard').removeClass('disabled');
                                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                                        $('a[data-toggle="tab"]').addClass('btn-default');
                                        $('[href=#step2]').removeClass('btn-default');
                                        $('[href=#step2]').addClass('btn-primary');
                                        fetch_detail_table();
                                    }

                                },
                                error: function () {
                                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                                    stopLoad();
                                    refreshNotifications(true);
                                }
                            });

                        }
                    }        
                });    


            }

           
        });

    });

    function currency_validation(CurrencyID) {
        if (CurrencyID) {
            documentID = $('#contractType').val();
            partyAutoID = $('#customerID').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'CUS');
        }
    }

    function fetch_detail_table() {
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID},
                url: "<?php echo site_url('Quotation_contract/fetch_item_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    tax_total = 0;
                    currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                    $('.currency').html('(' + data['currency']['transactionCurrency'] + ')');
                    $('#table_body').empty();
                    $('#table_tfoot').empty();
                    x = 1;
                    $("#contractType").prop("disabled", true);
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body').append('<tr class="danger"><td colspan="11" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                        $("#customerID").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);
                        $("#segment").prop("disabled", false);
                        currencyID = null;
                    } else {
                        $("#segment").prop("disabled", true);
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        tot_amount = 0;
                        currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                        $.each(data['detail'], function (key, value) {
                            $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['noOfItems'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + value['discountPercentage'] + '%) </td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['contractDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="delete_item(' + value['contractDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;
                            tot_amount += (parseFloat(value['transactionAmount']));
                            tax_total += parseFloat(value['transactionAmount']);
                        });
                        $('#table_tfoot').append('<tr><td colspan="9" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                    }
                    /*Tax Applicable Amount*/

                    $('#tax_tot').text('<?php echo $this->lang->line('sales_markating_transaction_tax_applicable_amount');?> ( ' + parseFloat(tax_total).formatMoney(currency_decimal, '.', ',') + ' )');
                    $('#tax_table_body_recode,#tax_table_footer').empty();
                    if (jQuery.isEmptyObject(data['tax_detail'])) {
                        $('#tax_table_body_recode').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                    } else {
                        x = 1;
                        t_total = 0;
                        $.each(data['tax_detail'], function (key, value) {
                            $('#tax_table_body_recode').append('<tr><td>' + x + '</td><td>' + value['taxShortCode'] + '</td><td>' + value['taxDescription'] + '</td><td class="text-right">' + value['taxPercentage'] + '% </td><td class="text-right">' + parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_tax(' + value['taxDetailAutoID'] + ',\'' + value['taxShortCode'] + '\',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;
                            t_total += parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total).toFixed(currency_decimal);
                        });
                        if (t_total > 0) {
                            $('#tax_table_footer').append('<tr><td colspan="4" class="text-right">Tax Total </td><td class="text-right total">' + parseFloat(t_total).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                        }
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
        ;
    }

    function initializeitemTypeahead(id) {
        /*var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "*/<?php // echo site_url();?>/*/Procurement/fetch_itemrecode/?q=%QUERY"
        });

        item.initialize();
        $('.search').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            /!*$('#itemAutoID').val(datum.itemAutoID);*!/
            $(this).closest('tr').find('.itemAutoID').val(datum.itemAutoID)
            LoaditemUnitPrice_againtsExchangerate(datum.companyLocalWacAmount, this);
            fetch_sales_price(datum.companyLocalSellingPrice, this, datum.defaultUnitOfMeasureID, datum.itemAutoID);
            //alert(datum.defaultUnitOfMeasureID);
            fetch_related_uom_id(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID, this);
        });*/

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
            onSelect: function (suggestion) {
                //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                setTimeout(function(){
                    $('#f_search_'+id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                LoaditemUnitPrice_againtsExchangerate(suggestion.companyLocalWacAmount, this);
                fetch_sales_price(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                $(this).closest('tr').find('.itemReferenceNo').focus();
                $(this).closest('tr').css("background-color", 'white');
                if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                    }, 200);
                    $('#f_search_' + id).val('');
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    myAlert('w','Revenue GL code not assigned for selected item')
                }
            }
        });
    }

   /* function clearitemAutoID(element){
        $(element).closest('tr').find('.itemAutoID').val('');
    }*/

    function clearitemAutoIDEdit(e,ths){

        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode==13) {
            //e.preventDefault();
        }else{
            $(ths).closest('tr').find('#edit_itemAutoID').val('');

            $(ths).closest('tr').find('#edit_quantityRequested ').val(0);
            $(ths).closest('tr').find('#edit_estimatedAmount').val('');
            $(ths).closest('tr').find('#edit_discount').val(0);
            $(ths).closest('tr').find('#edit_discount_amount').val(0);
            $(ths).closest('tr').find('#edit_net_unit_cost').html('0');
            $(ths).closest('tr').find('#edit_totalAmount').html('0');
        }

    }

    function clearitemAutoID(e,ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode==13) {
            //e.preventDefault();
        }else{
            $(ths).closest('tr').find('.itemAutoID').val('');
            $(ths).closest('tr').find('.quantityRequested ').val(0);
            $(ths).closest('tr').find('.estimatedAmount').val('');
            $(ths).closest('tr').find('.discount').val(0);
            $(ths).closest('tr').find('.discount_amount').val(0);
            $(ths).closest('tr').find('.net_unit_cost').html('0');
            $(ths).closest('tr').find('.net_amount').html('0');
        }
    }

    function initializeitemTypeahead_edit() {
        /*var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "*/<?php // echo site_url();?>/*/Procurement/fetch_itemrecode/?q=%QUERY"
        });

        item.initialize();
        $('#search').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $('#edit_net_unit_cost').text('0.00');
            $('#edit_totalAmount').text('0.00');
            $('#edit_itemAutoID').val(datum.itemAutoID);
            LoaditemUnitPrice_againtsExchangerate_edit(datum.companyLocalWacAmount);
            fetch_sales_price_edit(datum.companyLocalSellingPrice, datum.defaultUnitOfMeasureID, datum.itemAutoID);
            //alert(datum.defaultUnitOfMeasureID);
            fetch_related_uom_id_edit(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID);
        });*/


        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
            onSelect: function (suggestion) {
                setTimeout(function(){
                    $('#edit_net_unit_cost').text('0');
                    $('#edit_totalAmount').text('0');
                    $('#edit_itemAutoID').val(suggestion.itemAutoID);
                }, 200);

                LoaditemUnitPrice_againtsExchangerate_edit(suggestion.companyLocalWacAmount);
                fetch_sales_price_edit(suggestion.companyLocalSellingPrice, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                $(this).closest('tr').find('#edit_itemReferenceNo').focus();
                if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
                    $('#edit_itemAutoID').closest('tr').find('.itemAutoID').val('');
                    $('#edit_itemAutoID').val('');
                    $('#edit_itemAutoID').closest('tr').css("background-color", '#ffb2b2 ');
                    myAlert('w','Revenue GL code not assigned for selected item')
                }


            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate(LocalWacAmount, element) {
        poID = contractAutoID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('Quotation_contract/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    /*$('#estimatedAmount').val(data['amount']);*/
                    $(element).closest('tr').find('.wac_cost').text(parseFloat(data['amount']).formatMoney(currency_decimal, '.', ','));
//                    $('#item_detail_form').bootstrapValidator('revalidateField', 'estimatedAmount');
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate_edit(LocalWacAmount) {
        poID = contractAutoID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('Quotation_contract/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#edit_wac_cost').text(parseFloat(data['amount']).formatMoney(currency_decimal, '.', ','));
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_item_wacAmount(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            url: "<?php echo site_url('ItemMaster/load_item_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    LoaditemUnitPrice_againtsExchangerate_edit(data['companyLocalWacAmount'])
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function confirmation() {
        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document*/
                    type: "warning",/*warning*/
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
                        data: {'contractAutoID': contractAutoID},
                        url: "<?php echo site_url('Quotation_contract/contract_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if(data[0]=='s'){
                                fetchPage('system/quotation_contract/quotation_contract_management_buyback', contractAutoID, 'Quotation_contract');
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function item_detail_modal() {
        if (contractAutoID) {
            contractDetailsAutoID = null;
            $('.search').typeahead('destroy');
            $('#item_detail_form')[0].reset();
            $('#discount').val(0);
            $('#discount_amount').val(0);
            $('.net_amount,.net_unit_cost,.wac_cost').text('0');
            $('.search').typeahead('val', '');
            $('.itemAutoID').val('');
            initializeitemTypeahead(1);
            $('#item_add_table tbody tr').not(':first').remove();
            $('.f_search').closest('tr').css("background-color",'white');
            $('.quantityRequested').closest('tr').css("background-color",'white');
            $('.estimatedAmount').closest('tr').css("background-color",'white');

            fetch_segment_master();
            $("#item_detail_modal").modal({backdrop: "static"});

        }
    }

    function fetch_segment_master()
    {
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID},
                url: "<?php echo site_url('Invoices/fetch_quotation_segment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    load_segmentBase_projectID_item(data['segmentID'] + '|' + data['segmentCode'])
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }

    function load_segmentBase_projectID_item(segment) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_multiple"); ?>',
            dataType: 'html',
            data: {segment: segment},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.div_projectID_item').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function delete_item(id) {
        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                    type: "warning",/*warning*/
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
                        data: {'contractDetailsAutoID': id},
                        url: "<?php echo site_url('Quotation_contract/delete_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_detail_table();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function select_text(data) {
        if (data.value != 0) {
            var result = $('#text_type option:selected').text().split('|');
            $('#percentage').val(parseFloat(result[2]));
            cal_tax(parseFloat(result[2]), tax_total);
            $('#tax_form').bootstrapValidator('revalidateField', 'percentage');
        }
    }

    function cal_tax_amount(discount_amount) {
        if (tax_total && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / tax_total) * 100).toFixed(2));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_tax(discount) {
        if (tax_total && discount) {
            $('#tax_amount').val(((tax_total / 100) * parseFloat(discount)).toFixed(2));
        } else {
            $('#tax_amount').val(0);
        }
    }

    function delete_tax(id, value) {
        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",/*warning*/
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
                        data: {'taxDetailAutoID': id},
                        url: "<?php echo site_url('Quotation_contract/delete_tax_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            setTimeout(function () {
                                fetch_detail_table();
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id) {
        /*$('#item_add_table tbody tr').not(':first').remove();
         $('#item_add_table tbody tr').find('td:last-child').hide();
         $('#item_add_table thead tr').find('th:last-child').hide();*/

        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_document');?>",/*You want to edit this record!*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>",/*Edit*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'contractDetailsAutoID': id},
                        url: "<?php echo site_url('Quotation_contract/fetch_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            contractDetailsAutoID = data['contractDetailsAutoID'];
                            var totAmount = parseFloat(data['transactionAmount']);
                            var unitAmount = parseFloat(data['unittransactionAmount']);
                            $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            //$('#search').typeahead('val', data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID'], $('#UnitOfMeasureID'));
                            //LoaditemUnitPrice_againtsExchangerate_edit(data['companyLocalWacAmount']);
                            load_item_wacAmount(data['itemAutoID']);
                            $('#edit_noOfItems').val(data['noOfItems']);
                            $('#edit_quantityRequested').val(data['requestedQty']);
                            //$('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount']) + parseFloat(data['discountAmount'])));
                            $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount']) + parseFloat(data['discountAmount'])).formatMoney(currency_decimal, '.',''));
                            $('#edit_discount_amount').val(data['discountAmount']);
                            $('#search_id').val(data['itemSystemCode']);
                            $('#edit_itemSystemCode').val(data['itemSystemCode']);
                            $('#edit_itemAutoID').val(data['itemAutoID']);
                            $('#edit_itemReferenceNo').val(data['itemReferenceNo']);
                            $('#edit_itemDescription').val(data['itemDescription']);
                            $('#edit_comment').val(data['comment']);
                            $('#edit_remarks').val(data['remarks']);
                            $('#edit_discount').val(data['discountPercentage']);
                            $('#edit_net_unit_cost').text((unitAmount).formatMoney(currency_decimal, '.', ','));
                            $('#edit_totalAmount').text((totAmount).formatMoney(currency_decimal, '.', ','));
                            $('#edit_UnitOfMeasureID').prop("disabled", true);
                            load_segmentBase_projectID_itemEdit();
                            $("#edit_item_detail_modal").modal({backdrop: "static"});
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
        ;
    }

    function load_segmentBase_projectID_itemEdit() {
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
                $('#edit_div_projectID_item').html(data);
                $('.select2').select2();
                if (projectID) {
                    $("#projectID_item").val(projectID).change()
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function fetch_related_uom_id(masterUnitID, select_value, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty()
                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_related_uom_id_edit(masterUnitID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $('#edit_UnitOfMeasureID').empty();
                var mySelect = $('#edit_UnitOfMeasureID');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $('#edit_UnitOfMeasureID').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function cal_discount_amount(element) {
        var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
        if(element.value>estimatedAmount){
            myAlert('w','Discount amount should be less than or equal to sales price');
            $(element).closest('tr').find('.discount').val(0);
            $(element).val(0)
        }else{
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount').val(((parseFloat(element.value) / estimatedAmount) * 100).toFixed(3))
            }
            net_amount(element);
        }
    }

    function change_qty(element) {
        if(element.value>0)
        {
            $(element).closest('tr').css("background-color",'white');
        }
        net_amount(element);
    }

    function cal_discount(element) {
        if (element.value < 0 || element.value > 100 || element.value =='') {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $(element).closest('tr').find('.discount').val(parseFloat(0));
            $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        } else {
            var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount_amount').val((estimatedAmount / 100) * parseFloat(element.value).formatMoney(currency_decimal, '.', ','))
            }
            net_amount(element);
        }
    }

    function net_amount(element) {
        var qut = $(element).closest('tr').find('.quantityRequested').val();
        var amount = $(element).closest('tr').find('.estimatedAmount').val();
        var discoun = $(element).closest('tr').find('.discount_amount').val();
        if (qut == null || qut == 0) {
            $(element).closest('tr').find('.net_amount,.net_unit_cost').text('0');
        } else {
            $(element).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(currency_decimal, '.', ','));
            $(element).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(currency_decimal, '.', ','));
        }
    }

    function change_amount(element) {
        if(element.value>0)
        {
            $(element).closest('tr').css("background-color",'white');
        }
        $(element).closest('tr').find('.discount').val(parseFloat(0));
        $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        net_amount(element);
    }

    // edit functions for Item Detail

    function edit_cal_discount_amount() {
        var estimatedAmount = parseFloat($('#edit_estimatedAmount').val());
        var discountAmount = parseFloat($('#edit_discount_amount').val());
        if (discountAmount > estimatedAmount) {
            swal("Cancelled", "Discount Amount should be less than the Sales Price", "error");
            $('#edit_discount').val(0);
            $('#edit_discount_amount').val(0);
            edit_net_amount(estimatedAmount);
        } else {
            if (estimatedAmount) {
                $('#edit_discount').val(((parseFloat(discountAmount) / estimatedAmount) * 100).toFixed(3))
            }
            edit_net_amount(discountAmount);
        }
    }

    function edit_change_qty(element) {
        edit_net_amount(element);
    }

    function edit_cal_discount(discount) {
        var estimatedAmount = parseFloat($('#edit_estimatedAmount').val());
        if (discount < 0 || discount > 100) {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $('#edit_discount').val(0);
            $('#edit_discount_amount').val(0);
            edit_net_amount(estimatedAmount);
        } else {
            if (estimatedAmount) {
                $('#edit_discount_amount').val((estimatedAmount / 100) * parseFloat($('#edit_discount').val()))
            }
            edit_net_amount();
        }
    }

    function edit_net_amount() {
        var qut = $('#edit_quantityRequested').val();
        var amount = $('#edit_estimatedAmount').val();
        var discoun = $('#edit_discount_amount').val();
        if (qut == null || qut == 0) {
            $('#edit_totalAmount').text('0');
            $('#edit_net_unit_cost').text('0');
        } else {
            $('#edit_totalAmount').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(currency_decimal, '.', ','));
            $('#edit_net_unit_cost').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(currency_decimal, '.', ','));
        }
    }

    function edit_change_amount(element) {
        $('#edit_discount').val(parseFloat(0));
        $('#edit_discount_amount').val(parseFloat(0));
        edit_net_amount(element);
    }

    function load_conformation() {
        $('[href=#step3]').tab('show');
        $('.btn-wizard').removeClass('disabled');
        $('a[data-toggle="tab"]').removeClass('btn-primary');
        $('a[data-toggle="tab"]').addClass('btn-default');
        $('[href=#step3]').removeClass('btn-default');
        $('[href=#step3]').addClass('btn-primary');
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'contractAutoID': contractAutoID, 'html': true},
                url: "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>/" + contractAutoID);
                    //attachment_modal_customer_invoice(contractAutoID, "<?php echo $this->lang->line('sales_markating_quotation_Contract');?>", "QUT");/*Quotation / Contract*/
                    attachment_modal_Quotation_Contract(contractAutoID, "<?php echo $this->lang->line('sales_markating_quotation_Contract');?>", "QUT");/*Quotation / Contract*/
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_contract_header() {
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID},
                url: "<?php echo site_url('Quotation_contract/load_contract_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        contractAutoID = data['contractAutoID'];
                        contractType = data['contractType'];
                        customerID = data['customerID'];
                        currencyID = data['transactionCurrencyID'];
                        $('.currency').html('(' + data['transactionCurrency'] + ')');
                        $("#a_link").attr("href", "<?php echo site_url('Quotation_contract/load_Quotation_contract_conformation'); ?>/" + contractAutoID);
                        $("#paymentvouchercode").val(data['PVcode']);
                        $('#contractDate').val(data['contractDate']);
                        $('#contractExpDate').val(data['contractExpDate']);
                        $('#contractNarration').val(data['contractNarration']);
                        $('#contractType').val(data['contractType']).change();
                        $('#referenceNo').val(data['referenceNo']);
                        $('#customerID').val(data['customerID']).change();
                        $('#contactPersonName').val(data['contactPersonName']);
                        $('#salesperson').val(data['salesPersonID']).change();
                        $('#contactPersonNumber').val(data['contactPersonNumber']);
                        $("#Note").wysihtml5();
                        $('#Note').val(data['Note']);

                        if (data['segmentID'])
                        {
                            $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        }else
                        {
                            $('#segment').val('<?php echo $this->common_data['company_data']['default_segment']?>').change();
                        }

                        fetch_detail_table();
                        $("#contractType").prop("disabled", true);
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#segment").prop("disabled", true);
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
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
    }

    function save_draft() {
        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this document*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/quotation_contract/quotation_contract_management_buyback', contractAutoID, 'Quotation_contract');
                });
        }
        ;
    }

    function attachment_modal_Quotation_Contract(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Quotation_contract/fetch_documentID"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    attachment_modal_customer_invoice(documentSystemCode, document_name,data)
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function attachment_modal_customer_invoice(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#customerInvoice_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#customerInvoice_attachment').empty();
                    $('#customerInvoice_attachment').append('' +data+ '');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_customerInvoice_attachment(InvoiceAutoID, DocumentSystemCode,myFileName) {
        if (InvoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_delete_this_attachment_file');?>",/*You want to delete this attachment file!*/
                    type: "warning",/*warning*/
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
                        data: {'attachmentID': InvoiceAutoID,'myFileName': myFileName},
                        url: "<?php echo site_url('Quotation_contract/delete_quotationContract_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s','<?php echo $this->lang->line('common_deleted_successfully');?>');/*Deleted Successfully*/
                                attachment_modal_Quotation_Contract(DocumentSystemCode, "Quotation", "QUT");
                            }else{
                                myAlert('e','<?php echo $this->lang->line('common_deletion_failed');?>');/*Deletion Failed*/
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function add_more_item() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#item_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(this)');
        appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.number').val('0');
        appendData.find('.number,.wac_cost,.net_unit_cost,.net_amount').text('0');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#item_add_table').append(appendData);
        var lenght = $('#item_add_table tbody tr').length - 1;
        $('#f_search_'+ search_id).closest('tr').css("background-color",'white');
        $(".select2").select2();
        initializeitemTypeahead(search_id);
        number_validation();
    }

    function saveItemOrderDetail_buyback() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#item_detail_form').serializeArray();
        if (contractAutoID) {
            data.push({'name': 'contractAutoID', 'value': contractAutoID});
            data.push({'name': 'contractDetailsAutoID', 'value': contractDetailsAutoID});

            $('#item_detail_form' + ' select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            });

            $('#item_detail_form' + ' select[name="projectID[]"] option:selected').each(function () {
                data.push({'name': 'projectID[]', 'value': $(this).text()})
            });

            $('.itemAutoID').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });

            $('.quantityRequested').each(function () {
                if (this.value == '' || this.value == '0') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });

            $('.estimatedAmount').each(function () {
                if (this.value == '' || this.value == '0') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_item_order_detail_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {
                    contractDetailsAutoID = null;
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_detail_table();
                        $('#item_detail_modal').modal('hide');
                        $('#item_detail_form')[0].reset();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        } else {
            swal({
                title: "Good job!",
                text: "You clicked the button!",
                type: "success"
            });
        }
    }

    function updateItemOrderDetail() {
        $('#edit_UnitOfMeasureID').prop("disabled", false);
        var data = $('#edit_item_detail_form').serializeArray();
        if (contractAutoID) {
            data.push({'name': 'contractAutoID', 'value': contractAutoID});
            data.push({'name': 'contractDetailsAutoID', 'value': contractDetailsAutoID});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/update_item_order_detail_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('#edit_UnitOfMeasureID').prop("disabled", true)
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        contractDetailsAutoID = null;
                        fetch_detail_table();
                        $('#edit_item_detail_modal').modal('hide');
                        $('#edit_item_detail_form')[0].reset();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        } else {
            swal({
                title: "Good job!",
                text: "You clicked the button!",
                type: "success"
            });
        }
    }

    function fetch_sales_price(salesprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: contractAutoID,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_contractmaster',
                primaryKey: 'contractAutoID',
                customerAutoID : customerID
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price_customerWise'); ?>",
            success: function (data) {
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_sales_price_edit(salesprice, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: contractAutoID,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_contractmaster',
                primaryKey: 'contractAutoID'
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price'); ?>",
            success: function (data) {
                if (data['status']) {
                    $('#edit_estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function Load_customer_currency(customerAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'customerAutoID': customerAutoID},
            url: "<?php echo site_url('Payable/fetch_customer_currency_by_id'); ?>",
            beforeSend: function () {
                $(':input[type="submit"]').prop('disabled', true);
            },
            success: function (data) {
                $(':input[type="submit"]').prop('disabled', false);
                if (currencyID) {
                    $("#transactionCurrencyID").val(currencyID).change()
                } else {
                    if (data.customerCurrencyID) {
                        $("#transactionCurrencyID").val(data.customerCurrencyID).change();
                        //currency_validation_modal(data.customerCurrencyID, 'BSI', customerAutoID, 'SUP');
                    }
                }
            }
        });
    }


    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if(number.length>1 && charCode == 46){
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if( caratPos > dotPos && dotPos>-(currency_decimal-1) && (number[1] && number[1].length > (currency_decimal-1))){
            return false;
        }
        return true;
    }

    //thanks: http://javascript.nwbox.com/cursor_position/
    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function load_default_note(){
        if (p_id) {

        }else{
            var docType=$('#contractType').val();
            var docid='QUT';
            if(docType=='Quotation'){
                docid='QUT';
            }else if(docType=='Contract'){
                docid='CNT';
            }else{
                docid='SO';
            }
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'docid': docid},
                url: "<?php echo site_url('Invoices/load_default_note'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#Note ~ iframe').contents().find('.wysihtml5-editor').html('');
                        $('#Note ~ iframe').contents().find('.wysihtml5-editor').html(data['description']);
                    }else{
                        //myAlert('w','Default Note not set')
                    }
                }
            });
        }
    }

    function open_all_notes(){
        var docType=$('#contractType').val();
        var docid='QUT';
        if(docType=='Quotation'){
            docid='QUT';
        }else if(docType=='Contract'){
            docid='CNT';
        }else{
            docid='SO';
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'docid': docid},
            url: "<?php echo site_url('Invoices/open_all_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#allnotebody').empty();
                    var x=1;
                    $.each(data, function (key, value) {
                        $('#allnotebody').append('<label class="chkboxlabl" ><input type="radio" name="allnotedesc" value="' + value['autoID'] + '" id="chkboxlabl_' + value['autoID'] + '">' + value['description'] + '</label>')
                        x++;
                    });
                    $("#all_notes_modal").modal({backdrop: "static"});
                }else{
                    myAlert('w','No Notes assigned')
                }
            }
        });
    }

    function save_notes(){
        var data = $("#all_notes_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/load_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#Note ~ iframe').contents().find('.wysihtml5-editor').html('');
                $('#Note ~ iframe').contents().find('.wysihtml5-editor').html(data['description']);
                $("#all_notes_modal").modal('hide');
            }, error: function () {
                stopLoad();
            }
        });
    }
</script>