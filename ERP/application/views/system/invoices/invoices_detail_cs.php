<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .boldtab{
        font-weight: bold;
        border-left-color: #ead8d8 !important;
    }

    .pulling-based-li{
        background: #547698;
    }

    .pulling-based-li > a{
        color: #ffffff !important;
    }

    .nav>li.pull-li>a:hover{
        color: #444 !important;
        cursor: pointer;
        background: #d4d3d3 !important
    }

    .form-group .select2-container {
        position: relative;
        z-index: 2;
        float: left;
        width: 150%;
        margin-bottom: 0;
        display: table;
        table-layout: fixed;
    }
</style>
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$no_rec_found = $this->lang->line('common_no_records_found');

$umo_arr = array('' => 'Select UOM');
$projectExist = project_is_exist();
$transaction_total = 0;
for ($x = 0; $x < count($detail['detail']); $x++) {
    $transaction_total += ($detail['detail'][$x]['transactionAmount'] - $detail['detail'][$x]['totalAfterTax']);
}
$margin=$marginpercent;

$qty_validate = getPolicyValues('VSQ', 'All');
$costChange_validate = getPolicyValues('CSC', 'All');
$designation_arr = array('' => 'Select Designation');
$placeholder = '0.00';
$currencyID = $master['transactionCurrency'];
$currency_decimal = $master['transactionCurrencyDecimalPlaces'];
if($currencyID == 'OMR')
{
    $placeholder = '0.000';
}
//$salesPersonArray = array('' => 'Select Category Types');
//$salesPersonArray[''] = 'Select Category Types';

//$salesPersonArray = all_employee_drop();
//print_r($salesPersonArray);
$taxEnabled = getPolicyValues('TAX', 'All');
/*var_dump($taxEnabled);
exit();*/
$stylewidth1='';
$stylewidth2='';
$stylewidth3='';
$stylewidth4='';
$stylewidth5='';
if($projectExist == 1)
{
    $stylewidth1='width: 12%';
    $stylewidth2='width: 10%';
    $stylewidth3='width: 10%';
    $stylewidth4='width: 6%';
    $stylewidth5='width: 6%;';
}


switch ($invoiceType) {
    case "Direct": case "DeliveryOrder": case "DirectItem": case "DirectIncome":
    case "Manufacturing":?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <?php if($invoiceType != 'DeliveryOrder'){ ?>
                    <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1"
                                          aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> GL</a>
                    </li><!--Income-->
                <?php } ?>
                <?php if($invoiceType == 'Direct' || $invoiceType == 'DirectItem' || $invoiceType == 'Manufacturing'){ ?>
                    <li class="">
                        <a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false"><?php echo $this->lang->line('common_item'); ?> </a>
                    </li><!--Item-->
                <?php } ?>
                <?php if($invoiceType == 'Direct' || $invoiceType == 'DeliveryOrder' || $invoiceType == 'DirectItem'){ ?>
                    <li class="tab_3_item_CINV <?php if($invoiceType == 'DeliveryOrder'){ echo 'active';}?> <?php if($invoiceType == 'DirectItem') { echo 'hide';}?>">
                        <a data-toggle="tab" class="boldtab" href="#tab_3" aria-expanded="false"><?php echo $this->lang->line('sales_marketing_delivery_order'); ?> </a>
                    </li><!--Delivery Order-->
                <?php } ?>

                <li class="pull-left header">
                    <i class="fa fa-hand-o-right"></i><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_direct_invoice_for'); ?>
                    : - <?php echo $master['customerName']; ?>
                </li> <!--Direct Invoice for-->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane <?php if($invoiceType != 'DeliveryOrder'){ echo 'active';}?>">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_details'); ?></th>
                            <!--GL Details-->
                            <th colspan="3"><?php echo $this->lang->line('common_amount'); ?></th><!--Amount-->
                            <th>
                                <button type="button" onclick="invoice_detail_modal(1)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_gl_code'); ?></th><!--GL Code-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_description'); ?> </th>
                            <!--GL Code Description-->
                            <th><?php echo $this->lang->line('common_segment'); ?> </th><!--Segment-->
                            <th>
                                <?php echo $this->lang->line('sales_markating_transaction_transaction'); ?><!--Transaction--><span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th><?php echo $this->lang->line('sales_marketing_discount_amount'); ?><!--Discount Amount--></th>
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_amount'); ?><!--Net Amount--> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>

                            <th style="width: 75px !important;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <td colspan="8" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?></b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <div id="tab_2" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="5"><?php echo $this->lang->line('sales_markating_transaction_document_item_details'); ?> </th>
                            <!--Item Details-->
                            <th class="taxenable" colspan="6"><?php echo $this->lang->line('common_price'); ?> <span class="currency">(
                                    <?php echo $master['transactionCurrency']; ?><!--Price-->
                                    )</span></th>
                            <th>
                                <button type="button" onclick="invoice_item_detail_modal(2)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add Item"><i class="fa fa-plus"></i></button>
                                <button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;"
                                        id="editallbtn" class="btn  btn-xs btn-default hidden pull-right"><span
                                            class="glyphicon glyphicon-pencil"></span> Edit All
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_code'); ?> </th><!--Code-->
                            <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                            <!--Description-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?> </th>
                            <!--UOM-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?></th>
                            <!--Qty-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_unit'); ?> </th>
                            <!--Unit-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th>
                            <!--Discount-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_unit_price'); ?> </th>
                            <!--Net Unit Cost-->
                            <th class="hideTaxpolicy"><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th class="hideTaxpolicy"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                            <th><?php echo $this->lang->line('sales_markating_net_totl'); ?> </th><!--Net Total-->
                            <th><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
                            <!--<th style="width: 105px !important;">&nbsp;</th>-->
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <tr class="danger">
                            <td colspan="12" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="item_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <div id="tab_3" class="tab-pane <?php if($invoiceType == 'DeliveryOrder'){ echo 'active';}?>">

                    <table class="table table-bordered table-striped table-condesed ">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_marketing_delivery_order_details');?></th>
                            <th colspan="5">
                                <?php echo $this->lang->line('common_amount');?> <span class="currency"> (<?php echo $master['transactionCurrency']; ?> )</span>
                                <button type="button" onclick="delivery_order_modal(3)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add Delivery Order"><i class="fa fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 15%"><?php echo $this->lang->line('common_code');?></th>
                            <th style="width: 15%"><?php echo $this->lang->line('common_date');?></th>
                            <th style="width: 15%"><?php echo $this->lang->line('common_reference_no');?></th>
                            <th style="width: 10%"><?php echo $this->lang->line('common_order_total');?></th>
                            <th style="width: 10%"><?php echo $this->lang->line('common_due');?></th>
                            <th style="width: 10%"><?php echo $this->lang->line('common_paid');?></th>
                            <th style="width: 10%"><?php echo $this->lang->line('common_balance');?></th>
                            <th style="width: 12px"> </th>
                        </tr>
                        </thead>
                        <tbody id="delivery_table_body">
                        <tr class="danger"><td colspan="9" class="text-center"><b><?php echo $no_rec_found; ?></b></td></tr>
                        </tbody>
                        <tfoot id="delivery_table_tfoot">

                        </tfoot>
                    </table>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2"
                               id="discount_tot"><?php echo $this->lang->line('sales_marketing_discount_applicable_amount');?><!--Discount Applicable Amount-->
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            )</label>
                        <input type="hidden" id="discount_tot_hn">
                        <form class="form-inline" id="discount_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeID', all_discount_drop(1), '', 'class="form-control" id="discountExtraChargeID" required style="width: 150px;"'); ?>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                                    <input type="hidden" id="discountPercentageTothn">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" id="discount_trans_amount" name="discount_amount"
                                       style="width: 100px;"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       onkeyup="cal_discount_amount_general(this.value,<?php echo $transaction_total; ?>)">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>"><?php echo $this->lang->line('common_discount_details');?><!--Discount Details-->
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_type');?><!--Type--> </th>
                                <th><?php echo $this->lang->line('sales_markating_transaction_detail');?><!--Detail--> </th>
                                <th><?php echo $this->lang->line('sales_markating_transaction_discount');?><!--Discount--> </th>
                                <th><?php echo $this->lang->line('common_amount');?><!--Amount-->
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="discount_table_body_recode">
                            <?php
                            $discount_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="discount_table_footer">
                            <?php
                            $dischtml = 'Discount Total';
                            if (!empty($detail['discount'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="4">' . $dischtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($discount_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2" id=""><?php echo $this->lang->line('sales_marketing_extra_charges');?><!--Extra Charges--></label>
                        <form class="form-inline" id="extraCharges_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeIDExtra', all_discount_drop(2), '', 'class="form-control" id="discountExtraChargeIDExtra" required style="width: 150px;"'); ?>
                            </div>
                            <!--<div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php /*echo $transaction_total; */?>)">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>-->
                            <div class="form-group">
                                <input type="text" class="form-control number" onkeypress="return validateFloatKeyPress(this,event)" id="extra_trans_amount" name="extra_amount" style="width: 100px;">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>"><?php echo $this->lang->line('sales_marketing_extra_charge_details');?><!--Extra Charges Details-->
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_type');?><!--Type--> </th>
                                <th><?php echo $this->lang->line('sales_markating_transaction_detail');?><!--Detail--> </th>
                                <th><?php echo $this->lang->line('common_amount');?><!--Amount-->

                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="extra_table_body_recode">
                            <?php
                            $extra_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="extra_table_footer">
                            <?php
                            $extrahtml = 'Extra Charge Total';
                            if (!empty($detail['extraCharge'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="3">' . $extrahtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($extra_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-md-5">
                        <?php if(($taxEnabled == 1) || ($taxEnabled == null) ){ ?>
                            <label for="exampleInputName2"
                                   id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <input type="hidden" id="tax_tot_hn_val">
                            <!--Tax for-->
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $transaction_total . ')" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                               style="width: 80px;"
                                               onkeyup="cal_tax(this.value,<?php echo $transaction_total; ?>)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                           style="width: 100px;"
                                           onkeypress="return validateFloatKeyPress(this,event);"
                                           onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>);">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="col-md-7">
                        <?php if(($taxEnabled == 1 || $taxEnabled == null) || !empty($detail['tax'])){ ?>
                            <table class="<?php echo table_class(); ?>"><?php echo $this->lang->line('sales_markating_tax_details'); ?>
                                <!--Tax Details-->
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_net_tax_type'); ?> </th>
                                    <!--Tax Type-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_detail'); ?> </th>
                                    <!--Detail-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <th><?php echo $this->lang->line('common_amount'); ?>
                                        (<?php echo $master['transactionCurrency']; ?>)
                                    </th><!--Amount-->
                                    <th style="width: 75px !important;">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="tax_table_body_recode">
                                <?php
                                $tax_total = 0;

                                ?>
                                </tbody>
                                <tfoot id="tax_table_footer">
                                <?php
                                $taxtotal = $this->lang->line('sales_markating_transaction_sales_tax_tot');
                                if (!empty($detail['tax'])) {
                                    echo '<tr>';
                                    echo '<td class="text-right" colspan="4">' . $taxtotal . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                    echo '<td class="text-right total">' . format_number($tax_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tfoot>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick="">Previous</button>
        </div> -->
        <?php
        break;
    case "Quotation":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li><a data-toggle="tab" class="boldtab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> GL </a>
                </li><!--Income-->
                <li  class="active"><a data-toggle="tab" class="boldtab" href="#tab_2"
                                aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_quotation'); ?> </a>
                </li><!--Quotation-->
                <li class="pull-left header"><i
                            class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_markating_transaction_quotation_base_invoice_for'); ?>
                    :
                    - <?php echo $master['customerName']; ?></li><!--Quotation base Invoice for-->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_details'); ?> </th>
                            <!--GL Details-->
                            <th colspan="3"><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
                            <th>
                                <button type="button" onclick="invoice_detail_modal(1)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_gl_code'); ?>  </th><!--GL Code-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_gl_code_description'); ?> </th>
                            <!--GL Code Description-->
                            <th><?php echo $this->lang->line('common_segment'); ?> </th><!--Segment-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_transaction'); ?> <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!--Transaction-->
                            <th>Discount Amount</th>
                            <th>Net Amount <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="width: 75px !important;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <td colspan="8" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <div id="tab_2" class="tab-pane active">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="7"><?php echo $this->lang->line('sales_markating_transaction_quotation_details'); ?>  </th>
                            <!--Quotation Details-->
                            <th class="taxenable" colspan="4"><?php echo $this->lang->line('common_price'); ?> <span
                                        class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th><!--Price-->
                            <th>
                                <button type="button" onclick="invoice_con_modal('Quotation Base',2)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add Quotation"><i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_code'); ?>  </th><!--Code-->
                            <th class="text-left"><?php echo $this->lang->line('common_description'); ?>  </th>
                            <!--Description-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?>  </th>
                            <!--UOM-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?></th>
                            <!--Qty-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_unit'); ?></th>
                            <th>Discount</th>
                            <th>Net Unit Price</th>
                            <!--Unit-->
                            <th class="hideTaxpolicy"><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th class="hideTaxpolicy"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_amount'); ?> </th>
                            <!--Net Amount-->
                            <th style="width: 75px !important;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <tr class="danger">
                            <td colspan="10" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="item_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->

                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2"
                               id="discount_tot"><?php echo $this->lang->line('sales_marketing_discount_applicable_amount');?><!--Discount Applicable Amount-->
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            )</label>
                        <input type="hidden" id="discount_tot_hn">
                        <form class="form-inline" id="discount_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeID', all_discount_drop(1), '', 'class="form-control" id="discountExtraChargeID" required style="width: 150px;"'); ?>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                                    <input type="hidden" id="discountPercentageTothn">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" id="discount_trans_amount" name="discount_amount"
                                       style="width: 100px;"
                                       onkeypress="return validateFloatKeyPress(this,event);"
                                       onkeyup="cal_discount_amount_general(this.value,<?php echo $transaction_total; ?>)">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">Discount Details
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Type </th>
                                <th>Detail </th>
                                <th>Discount </th>
                                <th>Amount
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="discount_table_body_recode">
                            <?php
                            $discount_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="discount_table_footer">
                            <?php
                            $dischtml = 'Discount Total';
                            if (!empty($detail['discount'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="4">' . $dischtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($discount_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2" id="">Extra Charges</label>
                        <form class="form-inline" id="extraCharges_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeIDExtra', all_discount_drop(2), '', 'class="form-control" id="discountExtraChargeIDExtra" required style="width: 150px;"'); ?>
                            </div>
                            <!--<div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php /*echo $transaction_total; */?>)">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>-->
                            <div class="form-group">
                                <input type="text" class="form-control number" onkeypress="return validateFloatKeyPress(this,event)" id="extra_trans_amount" name="extra_amount" style="width: 100px;">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">Extra Charges Details
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Type </th>
                                <th>Detail </th>
                                <th>Amount
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="extra_table_body_recode">
                            <?php
                            $extra_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="extra_table_footer">
                            <?php
                            $extrahtml = 'Extra Charge Total';
                            if (!empty($detail['extraCharge'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="3">' . $extrahtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($extra_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <?php if($taxEnabled == 1 || ($taxEnabled == null)){ ?>
                            <label for="exampleInputName2"
                                   id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?> </label>
                            <input type="hidden" id="tax_tot_hn_val">
                            <!--Tax for-->
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $transaction_total . ')" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                               style="width: 80px;"
                                               onkeyup="cal_tax(this.value,<?php echo $transaction_total; ?>)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                           style="width: 100px;"
                                           onkeypress="return validateFloatKeyPress(this,event);"
                                           onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="col-md-7">
                        <?php if($taxEnabled == 1  || !empty($detail['tax']) || ($taxEnabled == null)){ ?>
                            <table class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_net_tax_type'); ?> </th>
                                    <!--Tax Type-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_detail'); ?> </th>
                                    <!--Detail-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <th><?php echo $this->lang->line('common_amount'); ?>
                                        (<?php echo $master['transactionCurrency']; ?>)
                                    </th><!--Amount-->
                                    <th style="width: 75px !important;">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="tax_table_body_recode">
                                <?php
                                $tax_total = 0;
                                ?>
                                </tbody>
                                <tfoot id="tax_table_footer">
                                <?php
                                $taxtotal = $this->lang->line('sales_markating_transaction_sales_tax_tot');
                                if (!empty($detail['tax'])) {
                                    echo '<tr>';
                                    echo '<td class="text-right" colspan="4">' . $taxtotal . '( ' . $master['transactionCurrency'] . ' )</td>';
                                    echo '<td class="text-right total">' . format_number($tax_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tfoot>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick="">Previous</button>
        </div> -->
        <?php
        break;
    case "Contract":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> GL </a>
                </li><!--Income-->
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_2"
                                aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_contract'); ?> </a>
                </li><!--Contract-->
                <li class="pull-left header"><i
                            class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_markating_transaction_contract_base_Invoice_for'); ?>
                    :
                    - <?php echo $master['customerName']; ?></li><!--Contract base Invoice for-->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_details'); ?> </th>
                            <!--GL Details-->
                            <th colspan="3"><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
                            <th>
                                <button type="button" onclick="invoice_detail_modal(1)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_gl_code'); ?> </th><!--GL Code-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_gl_code_description'); ?> </th>
                            <!--GL Code Description-->
                            <th><?php echo $this->lang->line('common_segment'); ?>  </th><!--Segment-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_transaction'); ?> <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!--Transaction-->
                            <th>Discount Amount</th>
                            <th>Net Amount <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="width: 75px !important;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <td colspan="9" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <div id="tab_2" class="tab-pane active">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="7">
                                <b><?php echo $this->lang->line('sales_markating_transaction_contract_details'); ?></th>
                            <!--Contract Details-->
                            <th class="taxenable" colspan="4"><?php echo $this->lang->line('common_price'); ?> <span class="currency">(
                                    <?php echo $master['transactionCurrency']; ?><!--Price-->
                                    )</span></th>
                            <th>
                                <button type="button" onclick="invoice_con_modal('Contract Base',2)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add Contract"><i class="fa fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_code'); ?> </th><!--Code-->
                            <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                            <!--Description-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?> </th>
                            <!--UOM-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?> </th>
                            <!--Qty-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_unit'); ?> </th>
                            <th>Discount</th>
                            <th>Net Unit Price</th>
                            <!--Unit-->
                            <th class="hideTaxpolicy"><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th class="hideTaxpolicy"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_amount'); ?> </th>
                            <!--Net Amount-->
                            <th style="width: 75px !important;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <tr class="danger">
                            <td colspan="10" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="item_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2"
                               id="discount_tot"><?php echo $this->lang->line('sales_marketing_discount_applicable_amount');?><!--Discount Applicable Amount-->
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            )</label>
                        <input type="hidden" id="discount_tot_hn">
                        <form class="form-inline" id="discount_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeID', all_discount_drop(1), '', 'class="form-control" id="discountExtraChargeID" required style="width: 150px;"'); ?>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                                    <input type="hidden" id="discountPercentageTothn">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" id="discount_trans_amount" name="discount_amount"
                                       style="width: 100px;"
                                       onkeypress="return validateFloatKeyPress(this,event);"
                                       onkeyup="cal_discount_amount_general(this.value,<?php echo $transaction_total; ?>)">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">Discount Details
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Type </th>
                                <th>Detail </th>
                                <th>Discount </th>
                                <th>Amount
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="discount_table_body_recode">
                            <?php
                            $discount_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="discount_table_footer">
                            <?php
                            $dischtml = 'Discount Total';
                            if (!empty($detail['discount'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="4">' . $dischtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($discount_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2" id="">Extra Charges</label>
                        <form class="form-inline" id="extraCharges_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeIDExtra', all_discount_drop(2), '', 'class="form-control" id="discountExtraChargeIDExtra" required style="width: 150px;"'); ?>
                            </div>
                            <!--<div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php /*echo $transaction_total; */?>)">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>-->
                            <div class="form-group">
                                <input type="text" class="form-control number" onkeypress="return validateFloatKeyPress(this,event)" id="extra_trans_amount" name="extra_amount" style="width: 100px;">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">Extra Charges Details
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Type </th>
                                <th>Detail </th>
                                <th>Amount
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="extra_table_body_recode">
                            <?php
                            $extra_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="extra_table_footer">
                            <?php
                            $extrahtml = 'Extra Charge Total';
                            if (!empty($detail['extraCharge'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="3">' . $extrahtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($extra_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <?php if($taxEnabled == 1 || ($taxEnabled == null)){ ?>
                            <label for="exampleInputName2"
                                   id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <input type="hidden" id="tax_tot_hn_val">
                            <!--Tax for-->
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $transaction_total . ')" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                               style="width: 80px;"
                                               onkeyup="cal_tax(this.value,<?php echo $transaction_total; ?>)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                           style="width: 100px;"
                                           onkeypress="return validateFloatKeyPress(this,event);"
                                           onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="col-md-7">
                        <?php if($taxEnabled == 1  || !empty($detail['tax']) || ($taxEnabled == null)){ ?>
                            <table class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_net_tax_type'); ?> </th>
                                    <!--Tax Type-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_detail'); ?> </th>
                                    <!--Detail-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <th><?php echo $this->lang->line('common_amount'); ?>
                                        (<?php echo $master['transactionCurrency']; ?>)
                                    </th>
                                    <th style="width: 75px !important;">&nbsp;</th><!--Amount-->
                                </tr>
                                </thead>
                                <tbody id="tax_table_body_recode">
                                <?php
                                $tax_total = 0;

                                ?>
                                </tbody>
                                <tfoot id="tax_table_footer">
                                <?php
                                $taxtotal = $this->lang->line('sales_markating_transaction_sales_tax_tot');
                                if (!empty($detail['tax'])) {
                                    echo '<tr>';
                                    echo '<td class="text-right" colspan="4">' . $taxtotal . '<!--Tax Total--> ( ' . $master['transactionCurrency'] . ' )</td>';
                                    echo '<td class="text-right total">' . format_number($tax_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tfoot>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick="">Previous</button>
        </div> -->
        <?php
        break;
    case "Sales Order":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right" id="myTabs">
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> GL </a>
                </li><!--Income-->
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_2"
                                aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_sales_order'); ?> </a>
                </li><!--Sales Order-->
                <li class="pull-left header"><i
                            class="fa fa-hand-o-right"></i><?php echo $this->lang->line('sales_markating_transaction_sales_order_base_invoice_for'); ?>
                    :
                    - <?php echo $master['customerName']; ?></li><!--Sales Order base Invoice for-->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane ">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_details'); ?> </th>
                            <!--GL Details-->
                            <th colspan="3"><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
                            <th>
                                <button type="button" onclick="invoice_detail_modal(1)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_gl_code'); ?> </th><!--GL Code-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_gl_code_description'); ?> </th>
                            <!--GL Code Description-->
                            <th><?php echo $this->lang->line('common_segment'); ?></th><!--Segment-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_transaction'); ?> <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Discount Amount</th>
                            <th>Net Amount <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="width: 75px !important;">&nbsp;</th>

                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <td colspan="8" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <div id="tab_2" class="tab-pane active">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="7"><?php echo $this->lang->line('sales_markating_transaction_sales_order_details'); ?>  </th>
                            <!--Sales Order Details-->
                            <th class="taxenable" colspan="4"><?php echo $this->lang->line('common_price'); ?> <span
                                        class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                            <!--Price-->
                            <th>
                                <button type="button" onclick="invoice_con_modal('Sales Order Base',2)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add Sale Order"><i class="fa fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_code'); ?> </th><!--Code-->
                            <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                            <!--Description-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?> </th>
                            <!--UOM-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?> </th>
                            <!--Qty-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_unit'); ?> </th>
                            <th>Discount</th>
                            <th>Net Unit Price</th>
                            <!--Unit-->
                            <th class="hideTaxpolicy" ><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th class="hideTaxpolicy" ><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_amount'); ?> </th>
                            <!--Net Amount-->
                            <th style="width: 75px;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <tr class="danger">
                            <td colspan="10" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?>  </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="item_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2"
                               id="discount_tot"><?php echo $this->lang->line('sales_marketing_discount_applicable_amount');?><!--Discount Applicable Amount-->
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            )</label>
                        <input type="hidden" id="discount_tot_hn">
                        <form class="form-inline" id="discount_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeID', all_discount_drop(1), '', 'class="form-control" id="discountExtraChargeID" required style="width: 150px;"'); ?>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                                    <input type="hidden" id="discountPercentageTothn">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" id="discount_trans_amount" name="discount_amount"
                                       style="width: 100px;"
                                       onkeypress="return validateFloatKeyPress(this,event);"
                                       onkeyup="cal_discount_amount_general(this.value,<?php echo $transaction_total; ?>)">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">Discount Details
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Type </th>
                                <th>Detail </th>
                                <th>Discount </th>
                                <th>Amount
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="discount_table_body_recode">
                            <?php
                            $discount_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="discount_table_footer">
                            <?php
                            $dischtml = 'Discount Total';
                            if (!empty($detail['discount'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="4">' . $dischtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($discount_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2" id="">Extra Charges</label>
                        <form class="form-inline" id="extraCharges_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeIDExtra', all_discount_drop(2), '', 'class="form-control" id="discountExtraChargeIDExtra" required style="width: 150px;"'); ?>
                            </div>
                            <!--<div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php /*echo $transaction_total; */?>)">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>-->
                            <div class="form-group">
                                <input type="text" class="form-control number" id="extra_trans_amount" onkeypress="return validateFloatKeyPress(this,event)" name="extra_amount" style="width: 100px;">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">Extra Charges Details
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Type </th>
                                <th>Detail </th>
                                <th>Amount
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="extra_table_body_recode">
                            <?php
                            $extra_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="extra_table_footer">
                            <?php
                            $extrahtml = 'Extra Charge Total';
                            if (!empty($detail['extraCharge'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="3">' . $extrahtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($extra_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <?php if($taxEnabled == 1 || ($taxEnabled == null)){ ?>
                            <!--Tax for-->
                            <label for="exampleInputName2"
                                   id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <input type="hidden" id="tax_tot_hn_val">
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $transaction_total . ')" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                               style="width: 80px;"
                                               onkeyup="cal_tax(this.value,<?php echo $transaction_total; ?>)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                           style="width: 100px;"
                                           onkeypress="return validateFloatKeyPress(this,event);"
                                           onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="col-md-7">
                        <?php if($taxEnabled == 1  || !empty($detail['tax']) || ($taxEnabled == null)){ ?>
                            <table class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_net_tax_type'); ?> </th>
                                    <!--Tax Type-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_detail'); ?> </th>
                                    <!--Detail-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <th><?php echo $this->lang->line('common_amount'); ?>
                                        (<?php echo $master['transactionCurrency']; ?>)
                                    </th>
                                    <th style="width: 75px !important;">&nbsp;</th><!--Amount-->
                                </tr>
                                </thead>
                                <tbody id="tax_table_body_recode">
                                <?php
                                $tax_total = 0;

                                ?>
                                </tbody>
                                <tfoot id="tax_table_footer">
                                <?php
                                $taxtotal = $this->lang->line('sales_markating_transaction_sales_tax_tot');

                                if (!empty($detail['tax'])) {
                                    echo '<tr>';
                                    echo '<td class="text-right" colspan="4">' . $taxtotal . '<!--Tax Total--> ( ' . $master['transactionCurrency'] . ' )</td>';
                                    echo '<td class="text-right total">' . format_number($tax_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tfoot>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    case "Insurance":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right" id="myTabs">
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> GL </a>
                </li><!--Income-->
                <li class="pull-left header"><i
                            class="fa fa-hand-o-right"></i>Insurance based Invoice</li>
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th><?php echo $this->lang->line('common_segment'); ?></th><!--Segment-->
                            <th>Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Margin (%)</th>
                            <th>Margin Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Total Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="width: 75px !important;"><button type="button" onclick="invoice_detail_modal_insurance(1)"
                                                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i>
                                </button></th>

                        </tr>
                        </thead>
                        <tbody id="gl_insurance_table_body">
                        <tr class="danger">
                            <td colspan="8" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="gl_insurance_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2"
                               id="discount_tot"><?php echo $this->lang->line('sales_marketing_discount_applicable_amount');?><!--Discount Applicable Amount-->
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            )</label>
                        <input type="hidden" id="discount_tot_hn">
                        <input type="hidden" id="discount_footer_tot_hn">
                        <input type="hidden" id="discount_margin_tot_hn">
                        <form class="form-inline" id="discount_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeID', all_discount_drop(1), '', 'class="form-control" id="discountExtraChargeID" required style="width: 150px;"'); ?>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                                    <input type="hidden" id="discountPercentageTothn">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" id="discount_trans_amount" name="discount_amount"
                                       style="width: 100px;"
                                       onkeypress="return validateFloatKeyPress(this,event);"
                                       onkeyup="cal_discount_amount_general(this.value,<?php echo $transaction_total; ?>)">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">Discount Details
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Type </th>
                                <th>Detail </th>
                                <th>Discount </th>
                                <th>Amount
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="discount_table_body_recode">
                            <?php
                            $discount_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="discount_table_footer">
                            <?php
                            $dischtml = 'Discount Total';
                            if (!empty($detail['discount'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="4">' . $dischtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($discount_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2" id="">Extra Charges</label>
                        <form class="form-inline" id="extraCharges_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeIDExtra', all_discount_drop(2), '', 'class="form-control" id="discountExtraChargeIDExtra" required style="width: 150px;"'); ?>
                            </div>
                            <!--<div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php /*echo $transaction_total; */?>)">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>-->
                            <div class="form-group">
                                <input type="text" class="form-control number" id="extra_trans_amount" onkeypress="return validateFloatKeyPress(this,event)" name="extra_amount" style="width: 100px;">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">Extra Charges Details
                            <!--Tax Details-->
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Type </th>
                                <th>Detail </th>
                                <th>Amount
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th style="width: 75px !important;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="extra_table_body_recode">
                            <?php
                            $extra_total = 0;

                            ?>
                            </tbody>
                            <tfoot id="extra_table_footer">
                            <?php
                            $extrahtml = 'Extra Charge Total';
                            if (!empty($detail['extraCharge'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="3">' . $extrahtml . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                echo '<td class="text-right total">' . format_number($extra_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td>&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <?php if($taxEnabled == 1 || ($taxEnabled == null)){ ?>
                            <!--Tax for-->
                            <label for="exampleInputName2"
                                   id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <input type="hidden" id="tax_tot_hn_val">
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $transaction_total . ')" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                               style="width: 80px;"
                                               onkeyup="cal_tax(this.value,<?php echo $transaction_total; ?>)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                           style="width: 100px;"
                                           onkeypress="return validateFloatKeyPress(this,event);"
                                           onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        <?php } ?>
                    </div>
                    <div class="col-md-7">
                        <?php if($taxEnabled == 1  || !empty($detail['tax']) || ($taxEnabled == null)){ ?>
                            <table class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_net_tax_type'); ?> </th>
                                    <!--Tax Type-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_detail'); ?> </th>
                                    <!--Detail-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <th><?php echo $this->lang->line('common_amount'); ?>
                                        (<?php echo $master['transactionCurrency']; ?>)
                                    </th>
                                    <th style="width: 75px !important;">&nbsp;</th><!--Amount-->
                                </tr>
                                </thead>
                                <tbody id="tax_table_body_recode">
                                <?php
                                $tax_total = 0;

                                ?>
                                </tbody>
                                <tfoot id="tax_table_footer">
                                <?php
                                $taxtotal = $this->lang->line('sales_markating_transaction_sales_tax_tot');

                                if (!empty($detail['tax'])) {
                                    echo '<tr>';
                                    echo '<td class="text-right" colspan="4">' . $taxtotal . '<!--Tax Total--> ( ' . $master['transactionCurrency'] . ' )</td>';
                                    echo '<td class="text-right total">' . format_number($tax_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tfoot>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>



        <div aria-hidden="true" role="dialog" id="invoice_detail_modal_insurance" class="modal fade" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_add_income_details'); ?> </h4>
                        <!--Add Income Detail-->
                    </div>
                    <form role="form" id="invoice_detail_insurance_form" class="form-horizontal">
                        <div class="modal-body">
                            <table class="table table-bordered table-condensed" id="income_add_table_insurance">
                                <thead>
                                <tr>
                                    <th>Supplier <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_segment'); ?> <?php required_mark(); ?></th>
                                    <!--Segment vsdfdf-->
                                    <?php if ($projectExist == 1) { ?>
                                        <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?></th><!--Project-->
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_amount'); ?> <span
                                                class="currency">(<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th><!--Amount-->
                                    <th>Margin (%) <?php required_mark(); ?></th>
                                    <th>Margin Amount <?php required_mark(); ?></th>
                                    <th class="hidden">Total Amount <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_description'); ?> <?php required_mark(); ?></th><!--Description-->
                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs"
                                                onclick="add_more_income_insurance()"><i
                                                    class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?php echo form_dropdown('supplierAutoID[]', $supplier_arr, '', 'class="form-control select2 supplierAutoID" required'); ?></td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd_insurance" onchange="load_segmentBase_projectID_income(this)"'); ?>
                                    </td>
                                    <?php if ($projectExist == 1) { ?>
                                        <td>
                                            <div class="div_projectID_income">
                                                <select name="projectID" class="form-control select2">
                                                    <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?></option>
                                                    <!--Select Project-->
                                                </select>
                                            </div>
                                        </td>
                                        <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                        </td>
                                        <td>
                                            <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                        </td>

                                    <?php } ?>
                                    <td><input type="text" name="amount[]" onkeypress="return validateFloatKeyPress(this,event)" onchange="calculate_total(this,'amount')" value="00" class="form-control amount m_number number"></td>
                                    <td><input type="text" name="marginPercentage[]" value="<?php echo $margin ?>" onchange="calculate_total(this,'marginPercentage')" class="form-control number marginPercentage"></td>
                                    <td><input type="text" name="marginAmount[]" value="0" onchange="calculate_total(this,'marginAmount')" class="form-control number marginAmount"></td>
                                    <td class="hidden"><input type="text" name="totalAmount[]" value="0" class="form-control number totalAmount" readonly></td>
                                    <td><textarea class="form-control" rows="1" name="description[]"></textarea></td>
                                    <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default"
                                    type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                            <button class="btn btn-primary" type="button"
                                    onclick="saveinsuranceInvoiceDetails()"><?php echo $this->lang->line('common_save_change'); ?>
                            </button><!--Save changes-->
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div aria-hidden="true" role="dialog" id="edit_invoice_detail_modal_insurance" class="modal fade" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_edit_income_details'); ?> </h4>
                        <!--Edit Income Detail-->
                    </div>
                    <form role="form" id="edit_invoice_insurance_detail_form" class="form-horizontal">
                        <div class="modal-body">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                                    <!--Segment-->
                                    <?php if ($projectExist == 1) { ?>
                                        <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?></th><!--Project-->
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_amount'); ?> <span
                                                class="currency">(<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th><!--Amount-->
                                    <th>Margin %</th>
                                    <th>Margin Amount</th>
                                    <th class="hidden">Total Amount</th>
                                    <th><?php echo $this->lang->line('common_description'); ?> <?php required_mark(); ?></th><!--Description-->
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?php echo form_dropdown('supplierAutoID', $supplier_arr, '', 'id="edit_supplierAutoID" class="form-control select2" required '); ?></td>
                                    <td><?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'id="edit_segment_gl" class="form-control select2"'); ?></td>
                                    <?php if ($projectExist == 1) { ?>
                                        <td>
                                            <div id="edit_div_projectID_income">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?>
                                                        <span class="currency"></option><!--Select Project-->
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                        </td>
                                        <td>
                                            <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                        </td>

                                    <?php } ?>
                                    <td><input type="text" name="amount" id="edit_amount"
                                               onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                               class="form-control m_number number amount" onchange="calculate_total(this,'amount')"></td>
                                    <td><input type="text" name="marginPercentage" id="edit_marginPercentage" onchange="calculate_total(this,'marginPercentage')" class="form-control number marginPercentage"></td>
                                    <td><input type="text" name="marginAmount" id="edit_marginAmount" onkeypress="return validateFloatKeyPress(this,event)" onchange="calculate_total(this,'marginAmount')" value="00" class="form-control m_number number marginAmount"></td>
                                    <td class="hidden"><input type="text" name="totalAmount" id="edit_totalAmount"  class="form-control totalAmount" readonly></td>
                                    <td><textarea class="form-control" id="edit_description" rows="1"
                                                  name="description"></textarea></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default"
                                    type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                            <button class="btn btn-primary" type="button"
                                    onclick="Update_Invoice_insurance()"><?php echo $this->lang->line('common_update_changes'); ?>
                            </button><!--Update changes-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        break;
    case "Operation":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right" id="myTabs">
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1"
                                      aria-expanded="false">Ticket </a>
                </li><!--Income-->
                <li class="pull-left header"><i
                            class="fa fa-hand-o-right"></i>Operation based Invoice</li>
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="operation_based_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>GL Account</th>
                            <th>Department</th>
                            <th>Client Contract</th>
                            <th>Comments</th>
                            <th>UOM</th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                            <th>Amount</th>
                            <th style="width: 75px !important;"><button type="button" onclick="invoice_detail_modal_operation(1)"
                                                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i>
                                </button></th>

                        </tr>
                        </thead>
                        <tbody id="operation_based_table_body">
                        <tr class="danger">
                            <td colspan="10" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="operation_based_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
            </div>
        </div>



        <div aria-hidden="true" role="dialog" id="invoice_detail_modal_operation" class="modal fade" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Pull Ticket </h4>
                        <!--Add Income Detail-->
                    </div>
                    <form role="form" id="invoice_detail_operation_form" class="form-horizontal">
                        <div class="modal-body">
                            <table class="table table-bordered table-condensed" id="operation_add_table">
                                <thead>
                                <tr>
                                    <th>Ticket No</th>
                                    <th>Created Date</th>
                                    <th>Contract No</th>
                                    <th>Total Product Value</th>
                                    <th>Total Service Value</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="operation_add_table_body">

                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default"
                                    type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                            <button class="btn btn-primary" type="button"
                                    onclick="saveopDetails()"><?php echo $this->lang->line('common_save_change'); ?>
                            </button><!--Save changes-->
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div aria-hidden="true" role="dialog" id="edit_invoice_detail_modal_insurance" class="modal fade" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_edit_income_details'); ?> </h4>
                        <!--Edit Income Detail-->
                    </div>
                    <form role="form" id="edit_invoice_insurance_detail_form" class="form-horizontal">
                        <div class="modal-body">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                                    <!--Segment-->
                                    <?php if ($projectExist == 1) { ?>
                                        <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?></th><!--Project-->
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_amount'); ?> <span
                                                class="currency">(<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th><!--Amount-->
                                    <th>Margin %</th>
                                    <th>Margin Amount</th>
                                    <th class="hidden">Total Amount</th>
                                    <th><?php echo $this->lang->line('common_description'); ?> <?php required_mark(); ?></th><!--Description-->
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?php echo form_dropdown('supplierAutoID', $supplier_arr, '', 'id="edit_supplierAutoID" class="form-control select2" required '); ?></td>
                                    <td><?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'id="edit_segment_gl" class="form-control select2"'); ?></td>
                                    <?php if ($projectExist == 1) { ?>
                                        <td>
                                            <div id="edit_div_projectID_income">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?>
                                                        <span class="currency"></option><!--Select Project-->
                                                </select>
                                            </div>
                                        </td>
                                    <?php } ?>
                                    <td><input type="text" name="amount" id="edit_amount"
                                               onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                               class="form-control m_number number amount" onchange="calculate_total(this,'amount')"></td>
                                    <td><input type="text" name="marginPercentage" id="edit_marginPercentage" onchange="calculate_total(this,'marginPercentage')" class="form-control number marginPercentage"></td>
                                    <td><input type="text" name="marginAmount" id="edit_marginAmount" onkeypress="return validateFloatKeyPress(this,event)" onchange="calculate_total(this,'marginAmount')" value="00" class="form-control m_number number marginAmount"></td>
                                    <td class="hidden"><input type="text" name="totalAmount" id="edit_totalAmount"  class="form-control totalAmount" readonly></td>
                                    <td><textarea class="form-control" id="edit_description" rows="1"
                                                  name="description"></textarea></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default"
                                    type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                            <button class="btn btn-primary" type="button"
                                    onclick="Update_Invoice_insurance()"><?php echo $this->lang->line('common_update_changes'); ?>
                            </button><!--Update changes-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        break;

    case "Project":
        $data['invoiceID'] = $invoiceAutoID;
        $data['projectdetail'] = $invoiceproject;
        $this->load->view('system/invoices/boq_project_view',$data);
        break;

    case "Commission": 
        /* $data['invoiceID'] = $invoiceAutoID;
        $data['projectdetail'] = $invoiceproject;
        $this->load->view('system/invoices/invoice_commission_view',$data); */
    ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right" id="myTabs">
                
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_1" onclick="hideExcelFunctions()"
                    aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> </a>
                </li><!--Income-->
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_2" onclick="showExcelFunctions()"
                    aria-expanded="false">Commission  </a>
                    
                </li>
                <li class="excelFunctions hide">
                <?php
              
                    $add_emp=  $this->lang->line('hrms_payroll_add_employee');
                    $uploadExcel =  "Upload";
                    $downloadExcel =  "Download Excel";
                    $downloadUrl =  site_url('Invoices/invoice_commission_download_csv');
                    if( $master['confirmedYN'] != 1){
                        echo '
                            <button type="button" class="btn btn-success btn-sm saveBtn" onclick="open_uploadModal()">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp; '.$uploadExcel.'
                            </button>
                            <button type="button" class="btn btn-success btn-sm"  onclick="window.open(\''.$downloadUrl.'\')">
                        <i class="fa fa-cloud-download" aria-hidden="true"></i>&nbsp; '.$downloadExcel.'
                      </button>';
                    } 
                ?>
                </li>
                <li class="pull-left header"><i
                    class="fa fa-hand-o-right"></i>Commission based Invoice
                </li>
                <!-- HRMS/Payroll/Monthly Addition -->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane ">
                <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_details'); ?> </th>
                            <!--GL Details-->
                            <th colspan="3"><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
                            <th>
                                <button type="button" onclick="invoice_detail_modal(1)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_gl_code'); ?> </th><!--GL Code-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_gl_code_description'); ?> </th>
                            <!--GL Code Description-->
                            <th><?php echo $this->lang->line('common_segment'); ?></th><!--Segment-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_transaction'); ?> <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Discount Amount</th>
                            <th>Net Amount <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="width: 75px !important;">&nbsp;</th>

                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <td colspan="8" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div>
                <div id="tab_2" class="tab-pane active">
                <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4">Sales Person Details</th>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_document_item_details'); ?> </th> <!--Item Details-->
                            <th class="" colspan="2"><?php echo $this->lang->line('common_price'); ?> <span class="currency">
                                (<?php echo $master['transactionCurrency']; ?><!--Price-->)</span></th>
                            <th>
                                <button type="button" onclick="invoice_item_detail_modal(2)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add Item"><i class="fa fa-plus"></i></button>
                              <!--   <button type="button" onclick="eedit_all_item_detail_modal()" style="margin-right:8px;"
                                        id="editallbtn" class="btn  btn-xs btn-default hidden pull-right"><span
                                            class="glyphicon glyphicon-pencil"></span> Edit All
                                </button> -->
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_code'); ?></th><!--Code-->
                            <th><?php echo $this->lang->line('common_name'); ?></th><!--Name-->
                            <th><?php echo $this->lang->line('common_designation'); ?></th><!--Designation-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?> </th><!--Item Code-->
                            <th class="text-left"><?php echo $this->lang->line('sales_markating_transaction_secondary_item_description'); ?> </th><!--Item Description-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?> </th><!--UOM-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?></th><!--Qty-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_unit'); ?> </th><!--Unit-->
                           <!--  <th><?php //echo $this->lang->line('sales_markating_transaction_discount'); ?> </th> -->
                            <!--Discount-->
                            <!-- <th><?php //echo $this->lang->line('sales_markating_transaction_net_unit_price'); ?> </th> -->
                            <!--Net Unit Cost-->
                            <!-- <th class="hideTaxpolicy"><?php //echo $this->lang->line('common_total'); ?> </th> --><!--Total-->
                            <!-- <th class="hideTaxpolicy"><?php //echo $this->lang->line('sales_markating_transaction_tax'); ?> </th> --><!--Tax-->
                            <th><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
                            <!--<th style="width: 105px !important;">&nbsp;</th>-->
                        </tr>
                        </thead>
                        <tbody id="commission_table_body">
                        <tr class="danger">
                            <td colspan="10" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?>  </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <form id="CommissionForm">
                        <tfoot id="commission_table_tfoot">

                        </tfoot>
                        </form>
                    </table>
                </div>
            </div>
        </div> 
        <?php
        break;

    default:
        echo "Contact system Admin";
}
?>
<?php
$data['documentID'] = 'CINV';
$data['invoiceAutoID'] = $invoiceAutoID;
$data['master'] = $master;
$data['invoiceType'] = $invoiceType;

$this->load->view('system/item/itemmastersub/inc-item-master-sub-config-edit', $data);

?>
<div class="modal fade" id="invoice_con_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 95%;" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title invoice_con_title">&nbsp;</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h4 class="invoice_con_title">&nbsp;</h4>
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked">
                                    <?php
                                    if (!empty($customer_con)) {
                                        for ($i = 0; $i < count($customer_con); $i++) {
//                                            if($customer_con[$i]['Total'] > 0){
                                            $con_id = $customer_con[$i]['contractAutoID'];
                                            echo '<li id="pull-'.$con_id.'" class="pull-li"><a onclick="fetch_con_detail_table(' . $con_id . ')">' . $customer_con[$i]['contractCode'] . ' <br> <strong> Date : </strong>' . $customer_con[$i]['contractDate'] . ' <br>  <strong> Ref : </strong>' . $customer_con[$i]['referenceNo'] . '<span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>';
//                                            }
                                        }
                                    } else {
                                        echo '<li><a>' . $no_rec_found . '</a></li>';
                                    }
                                    ?>
                                    <!--No Records found-->
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div style="min-height: .01%; overflow-x: auto;" >
                            <table class="table table-bordered table-striped table-condesed ">
                                <thead>
                                <tr>
                                    <?php if($openContractPolicy != 1) { ?>
                                        <th colspan='4'><?php echo $this->lang->line('common_item'); ?></th><!--Item-->
                                    <?php } else { ?>
                                        <th colspan='3'><?php echo $this->lang->line('common_item'); ?></th><!--Item-->
                                    <?php } ?>
                                    <th colspan='2'><?php echo $this->lang->line('common_item'); ?> <span
                                                class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span></th>
                                    <!--Item-->
                                    <th colspan='6'><?php echo $this->lang->line('sales_markating_transaction_invoice_item'); ?>
                                        <span class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span>
                                    </th>
                                    <!--Invoiced Item -->
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                                    <!--Description-->
                                    <th><?php echo $this->lang->line('common_warehouse'); ?> </th><!--Ware House-->
                                    <?php if($openContractPolicy != 1) { ?>
                                        <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?> </th>
                                    <?php } ?>
                                    <!--Qty-->
                                    <th><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
                                    <th>Discount</th>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?></th>
                                    <!--Qty-->
                                    <th><?php echo $this->lang->line('common_price'); ?> </th><!--Price-->
                                    <th>Discount</th>
                                    <?php if(($taxEnabled == 1) || ($taxEnabled == null)){ ?>
                                        <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <?php }else{ ?>
                                        <th style="display: none;"></th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_total'); ?>  </th><!--Total-->
                                    <th>&nbsp;</th>
                                    <th style="display: none;">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="table_body">
                                <tr class="danger">
                                    <td colspan="12" class="text-center"><b><?php echo $no_rec_found; ?>  </b></td>
                                    <!--No Records Found-->
                                </tr>
                                </tbody>
                                <tfoot id="table_tfoot">

                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"
                        onclick="save_con_base_items()"><?php echo $this->lang->line('common_save_change'); ?> </button>
                <!--Save changes-->
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="invoice_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 99%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_add_income_details'); ?> </h4>
                <!--Add Income Detail-->
            </div>
            <form role="form" id="invoice_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="income_add_table">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_gl_code'); ?><?php required_mark(); ?></th>
                            <!--GL Code-->
                            <th><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                            <!--Segment vsdfdf-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?></th><!--Project-->
                                <th>Project Category</th>
                                <th>Project Subcategory</th>

                            <?php } ?>
                            <th><?php echo $this->lang->line('common_amount'); ?> <span
                                        class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span> <?php required_mark(); ?></th><!--Amount-->
                            <th>Discount Percentage</th>
                            <th>Discount Amount</th>
                            <th>Net Amount <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th><?php echo $this->lang->line('common_description'); ?></th><!--Description-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_income()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('gl_code[]', $gl_code_arr, '', 'class="form-control select2 gl_code" required'); ?></td>
                            <td>
                                <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd" onchange="load_segmentBase_projectID_income(this)"'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td class="form-group" style="<?php echo $stylewidth1?>">
                                    <div class="div_projectID_income">
                                        <select name="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?></option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                                <td class="form-group" style="<?php echo $stylewidth2?>">
                                    <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                </td>
                                <td class="form-group" style="<?php echo $stylewidth2?>">
                                    <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                </td>

                            <?php } ?>
                            <td><input type="text" name="amount[]" onkeyup="calculateNetAmount(this,'amount')" onkeypress="return validateFloatKeyPress(this,event)"
                                       value="00" class="form-control m_number amount number"></td>
                            <td><input type="text" name="discountPercentage[]" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'discountPercentage')" value="00" class="form-control number discountPercentage"></td>
                            <td><input type="text" name="discountAmount[]" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'discountAmount')" value="00" class="form-control number discountAmount"></td>
                            <td><input type="text" name="Netamount[]" value="00" class="form-control number Netnumber" readonly></td>
                            <td><textarea class="form-control" rows="1" name="description[]"></textarea></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="saveDirectInvoiceDetails()"><?php echo $this->lang->line('common_save_change'); ?>
                    </button><!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="edit_invoice_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog " style="width:99%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_edit_income_details'); ?> </h4>
                <!--Edit Income Detail-->
            </div>
            <form role="form" id="edit_invoice_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_gl_code'); ?><?php required_mark(); ?></th>
                            <!--GL Code-->
                            <th><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                            <!--Segment-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?></th><!--Project-->
                                <th>Project Category</th>
                                <th>Project Subcategory</th>
                            <?php } ?>
                            <th><?php echo $this->lang->line('common_amount'); ?> <span
                                        class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span> <?php required_mark(); ?></th><!--Amount-->
                            <th><?php echo $this->lang->line('common_discount_percentagae'); ?></th>
                            <th><?php echo $this->lang->line('common_discount_amount'); ?></th>
                            <th><?php echo $this->lang->line('common_net_amount'); ?><span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th><?php echo $this->lang->line('common_description'); ?> </th><!--Description-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('gl_code', $gl_code_arr, '', 'id="edit_gl_code" class="form-control select2" required '); ?></td>
                            <td><?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'id="edit_segment_gl" class="form-control select2" onchange="load_segmentBase_projectID_incomeEdit(this.value)"'); ?></td>
                            <?php if ($projectExist == 1) { ?>
                                <td class="form-group" style="<?php echo $stylewidth1?>">
                                    <div id="edit_div_projectID_income">
                                        <select name="projectID" id="projectID" class="form-control select2 projectID_gl">
                                            <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?>
                                                <span class="currency"></option><!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                                <td class="form-group" style="<?php echo $stylewidth2?>">
                                    <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                </td>
                                <td class="form-group" style="<?php echo $stylewidth2?>">
                                    <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit"'); ?>
                                </td>

                            <?php } ?>
                            <td><input type="text" name="amount" id="edit_amount" onkeyup="calculateNetAmount_edit(this,'amount')"
                                       onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                       class="form-control m_number number"></td>
                            <td><input type="text" name="discountPercentage" id="discountPercentage_edit" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00" class="form-control number "></td>
                            <td><input type="text" name="discountAmount" id="discountAmount_edit" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00" class="form-control number "></td>
                            <td><input type="text" name="Netamount" id="Netamount_edit" value="00" class="form-control number " readonly></td>
                            <td><textarea class="form-control" id="edit_description" rows="1"
                                          name="description"></textarea></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="Update_Invoice_Income()"><?php echo $this->lang->line('common_update_changes'); ?>
                    </button><!--Update changes-->
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="invoice_item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 99% !important;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h5>
                <!--Add Item Detail-->
            </div>
            <form role="form" id="invoice_item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="item_add_table">
                        <thead>
                        <tr>
                            <th style="width:60px;" class="commissonYN hide"><?php echo $this->lang->line('sales_markating_transaction_sales_person'); ?><?php required_mark(); ?></th><!--Sales Person-->
                            <th style="width:100px;" class="commissonYN hide"><?php echo $this->lang->line('common_designation'); ?><?php required_mark(); ?></th><!--Designation-->

                            <th><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th><?php echo $this->lang->line('common_warehouse'); ?><?php required_mark(); ?></th>
                            <!--Warehouse-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?></th><!--Project-->
                                <th>Project Category</th>
                                <th>Project Subcategory</th>
                            <?php } ?>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width:100px;"><?php echo $this->lang->line('sales_marketing_current_stock'); ?><?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?><?php required_mark(); ?></th>
                            <!--Qty-->
                            <th style="width: 120px;"><?php echo $this->lang->line('common_amount'); ?> <?php required_mark(); ?>
                                <span
                                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!--Amount-->
                            <?php if($invoiceType == "Commission"){ } else { ?>
                            <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th>
                            <!--Discount-->
                            <?php if(($taxEnabled == 1 || $taxEnabled == null)){ ?>
                                <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?>  </th>
                            <?php } 
                            }?>
                            <!--Tax-->
                            <th><?php echo $this->lang->line('common_comment'); ?>  </th><!--Comment-->
                            <th style="width: 60px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more('item_add_table')"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="commissonYN hide">
                                <?php echo form_dropdown('salesPersonID[]', load_employee_drop(true), "Select Salesperson ", 'class="form-control select2" id="salesPersonID" onchange="loadDesignation(this,this.value)" '); ?>
                            </td>
                            <td class="commissonYN hide">
                                <?php echo form_dropdown('designationID[]', $designation_arr, "Select Designation", 'class="form-control select2 designationID"  required'); ?>

                            </td>
                            <td style="<?php echo $stylewidth1?>">
                                <input type="text" class="form-control search input-mini f_search" name="search[]"
                                       id="f_search_1"
                                       placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_id'); ?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_description'); ?> ..."
                                       onkeydown="remove_item_all_description(event,this)"><!--Item Id-->
                                <!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td>
                                <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID" onchange="checkitemavailable(this)" required'); ?>
                            </td>
                            
                            <?php if ($projectExist == 1) { ?>
                                <td class="form-group" style="width: 10%">
                                    <div class="div_projectID_item">
                                        <select name="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                                <td class="form-group" style="<?php echo $stylewidth2?>">
                                    <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                </td>
                                <td class="form-group" style="<?php echo $stylewidth3?>">
                                    <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                </td>
                            <?php } ?>
                            <td style="<?php echo $stylewidth4?>">
                                <input class="hidden conversionRate" id="conversionRate" name="conversionRate">
                                <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini" onchange="convertPrice(this)" required'); ?>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="currentstock[]"
                                           class="form-control currentstock" required disabled>
                                </div>
                            </td>

                            <td><input type="text" onfocus="this.select();" name="quantityRequested[]"
                                       onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                       class="form-control number input-mini quantityRequested" required></td>


                            <td>
                                <div class="input-group">
                                    <input type="text" onfocus="this.select();" name="estimatedAmount[]"
                                           onkeyup="change_amount(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)" placeholder="<?php echo $placeholder ?>"
                                           class="form-control number estimatedAmount input-mini">
                                </div>
                            </td>
                            <?php if($invoiceType == "Commission"){ } else { ?>
                            <td style="width: 100px;">
                                <div class="input-group">
                                    <input type="text" name="discount[]" placeholder="0.00" value="0"
                                           onkeyup="cal_discount(this)" onfocus="this.select();"
                                           class="form-control number discount">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 100px;">
                                <input type="text" name="discount_amount[]" placeholder="<?php echo $placeholder ?>" onkeypress="return validateFloatKeyPress(this,event)" value="0"
                                       onkeyup="cal_discount_amount(this)" onfocus="this.select();"
                                       class="form-control number discount_amount">
                            </td>
                            <?php if($taxEnabled == 1 || $taxEnabled == null){ ?>
                                <td style="<?php echo $stylewidth5?>">
                                    <?php echo form_dropdown('item_text[]', all_tax_drop(1), '', 'class="form-control item_text input-mini" id="" onchange="select_text_item(this)"'); ?>
                                </td>
                                <td style="width: 120px">
                                    <div class="input-group">
                                        <input type="text" name="item_taxPercentage[]" id=""
                                               placeholder="0.00" onfocus="this.select();"
                                               class="form-control number item_taxPercentage input-mini" value="0" readonly>
                                        <span class="input-group-addon input-group-addon-mini">%</span>
                                    </div>
                                </td>
                            <?php } 
                            } ?>
                            <td style="<?php echo $stylewidth5?>">
                                <textarea class="form-control input-mini" rows="1" name="remarks[]"
                                          placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_remarks'); ?>..."></textarea>
                                <!--Item Remarks-->
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="saveInvoiceItemDetail()"><?php echo $this->lang->line('common_save_change'); ?>
                    </button><!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="edit_invoice_item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90% !important;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_edit_item_details'); ?> </h4>
                <!--Edit Item Detail-->
            </div>
            <form role="form" id="edit_invoice_item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="item_edit_table">
                        <thead>
                        <tr>
                            <th style="width:60px;" class="commissonYN hide"><?php echo $this->lang->line('sales_markating_transaction_sales_person'); ?><?php required_mark(); ?></th><!--Sales Person-->
                            <th style="width:60px;" class="commissonYN hide"><?php echo $this->lang->line('common_designation'); ?><?php required_mark(); ?></th><!--Sales Person-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th><?php echo $this->lang->line('common_warehouse'); ?><?php required_mark(); ?></th>
                            <!--Warehouse-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?></th><!--Project-->
                                <th>Project Category</th>
                                <th>Project Subcategory</th>

                            <?php } ?>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width:100px;"><?php echo $this->lang->line('sales_marketing_current_stock'); ?><?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?><?php required_mark(); ?></th>
                            <!--Qty-->
                            <th style="width: 120px;"><?php echo $this->lang->line('common_amount'); ?> <?php required_mark(); ?>
                                <span
                                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!--Amount-->
                            <?php if($invoiceType == "Commission"){ } else { ?>
                            <th colspan="2"
                                class="directdiscount"><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th>
                            <!--Discount-->

                                <th class="hideTaxpolicy_edit" colspan="2"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th>
                            <!--Tax-->
                            <?php } ?> 
                            <th><?php echo $this->lang->line('common_comment'); ?> </th><!--Comment-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="commissonYN hide">
                                <?php echo form_dropdown('salesPersonID', load_employee_drop(), "Select Salesperson ", 'class="form-control select2" id="edit_salesPersonID" onchange="loadDesignation(this,this.value)" required'); ?>
                                
                            </td>
                            <td class="commissonYN hide">
                                <?php echo form_dropdown('designationID', $designation_arr, "Select Designation", 'class="form-control select2 designationID" id="edit_designationID"  required'); ?>

                            </td>
                            <td style="<?php echo $stylewidth1?>">
                                <input type="text" class="form-control input-mini" name="search"
                                       placeholder="Item ID, Item Description..." id="search"
                                       onkeydown="remove_item_all_description_edit(event)">
                                <input type="hidden" class="form-control" id="edit_itemAutoID" name="itemAutoID">
                            </td>
                            <td>
                                <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2 input-mini" id="edit_wareHouseAutoID" onchange="editstockwarehousestock(this)"  required'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td class="form-group" style="<?php echo $stylewidth2?>">
                                    <div id="edit_div_projectID_item">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>

                                <td class="form-group" style="<?php echo $stylewidth2?>">
                                    <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                </td>
                                <td class="form-group" style="<?php echo $stylewidth3?>">
                                    <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                </td>
                            <?php } ?>
                            <td style="<?php echo $stylewidth4?>">
                                <input class="hidden conversionRateEdit" id="conversionRateEdit" name="conversionRateEdit">
                                <?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control input-mini" id="edit_UnitOfMeasureID" onchange="convertPrice_edit(this)" required'); ?>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="hidden" name="mainCategoryhn"
                                           id="mainCategoryhn"
                                           class="form-control">
                                    <input type="text" name="currentstock_edit"
                                           id="currentstock_edit"
                                           class="form-control" required disabled>
                                </div>
                            </td>

                            <td><input type="text" name="quantityRequested" placeholder="0.00"
                                       class="form-control number input-mini" onfocus="this.select();"
                                       id="edit_quantityRequested" onkeyup="checkCurrentStockEdit(this), edit_validateQtyContract(this.value)" required>
                                <input class="hidden" id="contractBalance" name="contractBalance">
                                <input class="hidden" id="contractItem" name="contractItem">
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="estimatedAmount" placeholder="<?php echo $placeholder ?>"
                                           class="form-control number input-mini" onfocus="this.select();"
                                           onkeyup="edit_change_amount()"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           id="edit_estimatedAmount">
                                </div>
                            </td>
                            <?php if($invoiceType == "Commission"){ } else { ?>
                            <td style="width: 100px;" class="directdiscount">
                                <div class="input-group">
                                    <input type="text" name="discount" placeholder="0.00" value="0"
                                           id="edit_discount" onfocus="this.select();"
                                           onkeyup="edit_cal_discount(this.value)"
                                           class="form-control number">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 100px;" class="directdiscount">
                                <input type="text" name="discount_amount" id="edit_discount_amount" placeholder="<?php echo $placeholder ?>"
                                       onkeyup="edit_cal_discount_amount()"  onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();" value="0"
                                       class="form-control number">
                            </td>
                           
                                <td class="hideTaxpolicy_edit" style="<?php echo $stylewidth4?>">
                                    <?php echo form_dropdown('item_text', all_tax_drop(1), '', 'class="form-control item_text input-mini" id="edit_item_text" onchange="select_text_item(this)"'); ?>
                                </td>
                                <td class="hideTaxpolicy_edit" style="width: 120px">
                                    <div class="input-group">
                                        <input type="text" name="item_taxPercentage" id="edit_item_taxPercentage"
                                               placeholder="0.00" onfocus="this.select();"
                                               class="form-control number item_taxPercentage input-mini" value="0" readonly>
                                        <span class="input-group-addon input-group-addon-mini">%</span>
                                    </div>
                                </td>
                            <?php } ?>        


                            <td style="style="style="<?php echo $stylewidth5?>">
                                <textarea class="form-control input-mini" rows="1" name="remarks" id="edit_remarks"
                                          placeholder="Item Remarks..."></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="Update_Invoice_Item_Detail()"><?php echo $this->lang->line('common_update_changes'); ?>
                    </button><!--Update changes-->
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="stockadjustmentSwitch" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_gl_account'); ?> </h5>
                <!--GL Account-->
            </div>
            <div class="modal-body" id="divglAccount">
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?>  </button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="customerinvoiceGLUpdate(1)"><?php echo $this->lang->line('sales_markating_transaction_apply_to_all'); ?>
                </button><!--Apply to All-->
                <button class="btn btn-primary" type="button"
                        onclick="customerinvoiceGLUpdate(0)"><?php echo $this->lang->line('common_save_change'); ?>
                </button><!--Save changes-->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delivery_order_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('sales_marketing_delivery_order_based');?></h4>
            </div>
            <div class="modal-body">
                <form autocomplete="off">
                    <table class="table table-bordered table-striped table-condesed ">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_marketing_delivery_order_based');?></th>
                            <th colspan="4">
                                <?php echo $this->lang->line('common_amount');?>
                                <span class="currency"> (<?php echo $master['transactionCurrency']; ?> )</span>
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 30%"><?php echo $this->lang->line('common_code');?></th>
                            <th style="width: 30%"><?php echo $this->lang->line('common_date');?></th>
                            <th style="width: 20%"><?php echo $this->lang->line('common_reference_no');?></th>
                            <th style="width: 15%"><?php echo $this->lang->line('common_order_total');?></th>
                            <th style="width: 10%"><?php echo $this->lang->line('common_invoiced_return');?></th>
                            <th style="width: 10%"><?php echo $this->lang->line('common_balance');?></th>
                            <th style="width: 10%"><?php echo $this->lang->line('common_amount');?></th>
                        </tr>
                        </thead>
                        <tbody id="table_body_un_billed_orders"> </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="delivery_order_invoice()"><?php echo $this->lang->line('common_save_change');?><!--Save changes--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="excelUpload_Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Commission Upload Form</h4>
            </div>
            <div class="modal-body">
            <?php echo form_open_multipart('', 'id="commissionUpload_form" class="form-inline"'); ?>
                <div class="row">
                    <div class="col-sm-7" style="margin-left: 3%">
                        <div class="form-group">
                            <label for="wareHouseAutoID"><?php echo $this->lang->line('common_warehouse'); ?></label><!--Warehouse--> 
                            <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2 input-mini" id="wareHouseAutoID" onchange="editstockwarehousestock(this)"  required'); ?>
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12" style="margin-left: 3%">
                        
                        <div class="form-group">
                            <input type="hidden" name="masterID" value="<?php echo $master['invoiceAutoID'] ?>">
                            <input type="hidden" name="type_m" value="<?php echo $this->input->post('type_m');?>">
                            <input type="hidden" name="docDate" id="docDate" value="">
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
                        <button type="button" class="btn btn-success" onclick="excel_upload()">
                            <span class="" aria-hidden="true"><b>Upload</b></span>
                        </button>
                    </div>
                    <div class="col-sm-12" style="margin-left: 3%; color: red">
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg1'); ?><br/>
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg2'); ?>
                    </div>
                    <div class="col-sm-12">
                        <div class="alert alert-danger " id="upload-msg-div"> </div>
                    </div>
                </div>
              
                   
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var search_id = 1;
    var invoiceAutoID;
    var invoiceDetailsAutoID;
    var currency_decimal;
    var invoiceType;
    var customerID;
    var currencyID;
    var tax_total;
    var tabID;
    var projectID;
    var projectIDEdit;
    var projectsubcat;
    var projectcategory;
    var taxYN = '<?php echo $taxEnabled?>';
    //var defaultSegment = <?php //echo json_encode($this->common_data['company_data']['default_segment']); ?>;
    var defaultSegment = '<?php echo $master['segmentID'] ?>|<?php echo $master['segmentCode'] ?>'
    var no_records_found_str = '<?php echo $this->lang->line('common_no_records_found');?>';
    $(document).ready(function () {

        $("[rel=tooltip]").tooltip();
        $('.select2').select2();
        invoiceDetailsAutoID = null;
        projectID = null;
        invoiceAutoID = <?php echo json_encode(trim($master['invoiceAutoID'] ?? '')); ?>;
        invoiceType = <?php echo json_encode(trim($master['invoiceType'] ?? '')); ?>;
        customerID = <?php echo json_encode(trim($master['customerID'] ?? '')); ?>;
        currencyID = <?php echo json_encode(trim($master['transactionCurrency'] ?? '')); ?>;
        currency_decimal = <?php echo json_encode(trim($master['transactionCurrencyDecimalPlaces'] ?? '')); ?>;
        tax_total = 0;
        if(invoiceType == "Commission"){
            $('.commissonYN').removeClass('hide');
        }else{
            $('.commissonYN').addClass('hide');
        }
        setTimeout(function () {
            tab_active(<?php echo($tabID != '' ? $tabID : 1) ?>);
        }, 300);
        initializeitemTypeahead();
        initializeitemTypeahead_edit();
        $('.currency').html('(' + currencyID + ')');
        number_validation();
        $('#tax_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                //tax_amount: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('sales_markating_transaction_tax_amount_required');?>.'}}}, /*Tax Amount is required*/
                text_type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_tax_type_required');?>.'}}} /*Tax Type is required*/
                //percentage: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('sales_markating_transaction_percentage_is_required');?>.'}}}/*Percentage is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'InvoiceAutoID', 'value': invoiceAutoID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Receivable/save_inv_tax_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    $('#text_type').val('');
                    $('#percentage').val('');
                    $('#tax_amount').val('');
                    InvoiceDetailAutoID = null;
                    refreshNotifications(true);
                    if (data['status']) {
                        setTimeout(function () {
                            fetch_invoice_direct_details();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });



        $('#discount_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                //discount_amount: {validators: {notEmpty: {message: 'Discount amount is required.'}}},
                discountExtraChargeID: {validators: {notEmpty: {message: 'Discount Type is required.'}}}
                //discountPercentage: {validators: {notEmpty: {message: 'Discount Percentage is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'InvoiceAutoID', 'value': invoiceAutoID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Invoices/save_inv_discount_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    $('#discountExtraChargeID').val('');
                    $('#discountPercentage').val('');
                    $('#discount_trans_amount').val('');
                    InvoiceDetailAutoID = null;
                    refreshNotifications(true);
                    if (data['status']) {
                        setTimeout(function () {
                            fetch_invoice_direct_details();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });



        $('#extraCharges_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                extra_amount: {validators: {notEmpty: {message: 'Extra amount is required.'}}},
                discountExtraChargeIDExtra: {validators: {notEmpty: {message: 'Extra Type is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'InvoiceAutoID', 'value': invoiceAutoID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Invoices/save_inv_extra_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    InvoiceDetailAutoID = null;
                    refreshNotifications(true);
                    if (data['status']) {
                        setTimeout(function () {
                            fetch_invoice_direct_details();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });



    });

    function invoice_con_modal(title, tab) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#table_body').html('<tr class="danger"><td colspan="12" class="text-center"><b><?php echo $no_rec_found; ?>  </b></td></tr>');
        tabID = tab;
        if (invoiceAutoID) {
            // $("#wareHouseAutoID").val(null).trigger("change");
            // $('#pv_item_detail_form')[0].reset();
            $('.invoice_con_title').html(title);
            $("#invoice_con_base_modal").modal({backdrop: "static"});
        }
    }

    function invoice_item_detail_modal(tab) {
        tabID = tab;
        if (invoiceAutoID) {
            invoiceDetailsAutoID = null;
            $('.search').typeahead('destroy');
            $('#invoice_item_detail_form')[0].reset();
            $('#item_taxPercentage').val(0);
            $('#item_taxPercentage').prop('readonly', true);
            $('.wareHouseAutoID').val('').change();
            $('#item_add_table tbody tr').not(':first').remove();
            $('.search').typeahead('val', '');
            $('.itemAutoID').val('');
            $('.f_search').closest('tr').css("background-color", 'white');
            $('.quantityRequested').closest('tr').css("background-color", 'white');
            $('.wareHouseAutoID').closest('tr').css("background-color", 'white');

            initializeitemTypeahead(1);
            load_segmentBase_projectID_item();
            $("#invoice_item_detail_modal").modal({backdrop: "static"});
        }
    }

    function invoice_detail_modal(tab) {
        tabID = tab;
        if (invoiceAutoID) {
            $('#invoice_detail_form')[0].reset();
            $('.gl_code').val('').change();
            $('.segment_glAdd').val(defaultSegment).change();
            $("#invoice_detail_modal").modal({backdrop: "static"});
            $('#income_add_table tbody tr').not(':first').remove();
        }
    }

    function select_text(data, total_amount) {
        if (data.value != 0) {
            var result = $('#text_type option:selected').text().split('|');
            $('#percentage').val(parseFloat(result[2]));
            cal_tax(parseFloat(result[2]), total_amount);
            $('#tax_form').bootstrapValidator('revalidateField', 'percentage');
        }
    }

    function fetch_con_detail_table(contractAutoID) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#pull-'+contractAutoID).addClass('pulling-based-li');

        if (contractAutoID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID},
                url: "<?php echo site_url('Invoices/fetch_con_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body').empty();
                    $('#table_tfoot').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $("#invoiceType").prop("disabled", false);
                        $("#customerID").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);
                        $("#addcustomer").prop("disabled", false);
                        currencyID = '';
                        $('#table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');

                    }
                    else {
                        var str = '';
                        $("#invoiceType").prop("disabled", true);
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#addcustomer").prop("disabled", true);
                        tot_amount = 0;
                        receivedQty = 0;
                        $.each(data['detail'], function (key, value) {
                            if (value['receivedQty'] != null && value['receivedQty'] !== undefined) {
                                receivedQty = value['receivedQty'];
                            }
                            var balQty = (value['requestedQty'] - value['receivedQty']);
                            balQty = (balQty > 0) ? balQty : 0;

                            let this_contDetID = value['contractDetailsAutoID'];

                            str = '<tr><td>' + x + '</td>';
                            str += '<td>' + value['itemSystemCode'] + ' - ' + value['itemSecondaryCode'] + ' - ' + value['itemDescription'] + '<br><b> UOM - </b>' + value['unitOfMeasure'] + '</td>';
                            str += '<td class="text-center"><select class="whre_drop" style="width: 110px;"  id="wareHouseAutoID' + this_contDetID + '" onchange="getWareHouseQty('+this_contDetID+')"> ';
                            str += '<option value="">Select WareHouse</option></select> <br/>Current Stock : <span id="wareHouseQty_' + this_contDetID + '">0</span></td>';
                            <?php if($openContractPolicy != 1) { ?>
                            str += '<td class="text-right">' +commaSeparateNumber((value['requestedQty'] - value['receivedQty']),2)  + '</td>';
                            
                            <?php } ?>

                            str += '<td class="text-right">' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td>';
                            str += '<td class="text-right">' + (value['discountAmount']) + '</td>';

                            <?php  if($qty_validate == 1) {?>
                            str += '<td class="text-center"><input type="text" class="number" size="8" id="qty_' + this_contDetID + '" onkeyup="validate_Qty_invoice(' + this_contDetID + ',this.value, ' + balQty + ',\''+ value['invmaincat']+'\')" ></td>';
                            <?php } else { ?>
                            str += '<td class="text-center"><input type="text" class="number" size="8" id="qty_' + this_contDetID + '" onkeyup="validate_with_wareHouse_qty(' + this_contDetID + ',\''+ value['invmaincat']+'\'),select_check_box(' + this_contDetID + ')" ></td>';
                            <?php } if($costChange_validate == 1) { ?>
                            str += '<td class="text-center"><input type="text" class="number" size="10" value="' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])) + '" id="amount_' + this_contDetID + '" onkeyup="select_amount(' + this_contDetID + ',this.value)"  onkeypress="return validateFloatKeyPress(this,event)"></td>';
                            <?php } else { ?>
                            str += '<td class="text-center"><input type="text" class="number" size="10" value="' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])) + '" id="amount_' + this_contDetID + '" onkeyup="select_amount(' + this_contDetID + ',this.value)"  onkeypress="return validateFloatKeyPress(this,event)" disabled></td>';
                            <?php } ?>
                            str += '<td class="text-center"><input type="text" class="number" size="10" value="' + parseFloat(value['discountAmount']).toFixed(currency_decimal) + '" id="discount_' + this_contDetID + '" onkeyup="cal_tot_amount(' + this_contDetID + ',this.value)" onkeypress="return validateFloatKeyPress(this,event)"></td>';
                            <?php if(($taxEnabled == 1 || $taxEnabled == null) ){ ?>
                            str += '<td class="text-center"><select class="tax_drop" id="tax_drop_' + this_contDetID + '" name="tex_type[]" onchange="cal_con_base_tax(' + this_contDetID + ',this)"><option value="">Select Tax</option></select><div class="input-group"> <input name="item_taxPercentage[]" onkeyup="change_tax_per(' + this_contDetID + ',this.value)" id="item_taxPercentage_' + this_contDetID + '" placeholder="0.00" class="form-control number item_taxPercentage input-mini" value="0" style="width:60px;" readonly="" autocomplete="off" type="text"><span class="input-group-addon input-group-addon-mini">%</span><input class="form-control number" id="tax_amount_' + this_contDetID + '" name="tax_amount[]" style="width: 50px;" onkeyup="change_tax_amount(' + this_contDetID + ',this.value);" onkeypress="return validateFloatKeyPress(this,event)" autocomplete="off" type="text" value="0" readonly=""></div></td>';
                            <?php } else{?>
                            str += '<td style="display: none;" class="text-center"></td>';
                            <?php } ?>
                            str += '<td class="text-right" id="tot_' + this_contDetID + '">0</td>';
                            str += '<td><input placeholder="Remarks" type="text" size="13" id="remarks_' + this_contDetID + '"></td>';
                            str += '<td class="text-right" style="display: none;"><input class="checkbox" id="check_' + this_contDetID + '" type="checkbox" value="' + this_contDetID + '"></td></tr>';



                            // $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + ' - ' + value['itemSecondaryCode'] + ' - ' + value['itemDescription'] + '<br><b> UOM - </b>' + value['unitOfMeasure'] + '</td><td class="text-center"><select class="whre_drop" style="width: 110px;"  id="whre_' + this_contDetID + '"><option value="">Select WareHouse</option></select></td><td class="text-right">' + (value['requestedQty'] - value['receivedQty']) + '</td><td class="text-right">' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + (value['discountAmount']) + '</td><td class="text-center"><input type="text" class="number" size="8" id="qty_' + this_contDetID + '" onkeyup="select_check_box(' + this_contDetID + ')" ></td><td class="text-center"><input type="text" class="number" size="10" value="' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])) + '" id="amount_' + this_contDetID + '" onkeyup="select_amount(' + this_contDetID + ',this.value)"  onkeypress="return validateFloatKeyPress(this,event)"></td><td class="text-center"><input type="text" class="number" size="10" value="' + parseFloat(value['discountAmount']).toFixed(currency_decimal) + '" id="discount_' + this_contDetID + '" onkeyup="cal_tot_amount(' + this_contDetID + ',this.value)" onkeypress="return validateFloatKeyPress(this,event)"></td><td class="text-center"><select class="tax_drop" id="tax_drop_' + this_contDetID + '" name="tex_type[]" onchange="cal_con_base_tax(' + this_contDetID + ',this)"><option value="">Select Tax</option></select><div class="input-group"> <input name="item_taxPercentage[]" onkeyup="change_tax_per(' + this_contDetID + ',this.value)" id="item_taxPercentage_' + this_contDetID + '" placeholder="0.00" class="form-control number item_taxPercentage input-mini" value="0" style="width:60px;" readonly="" autocomplete="off" type="text"><span class="input-group-addon input-group-addon-mini">%</span><input class="form-control number" id="tax_amount_' + this_contDetID + '" name="tax_amount[]" style="width: 50px;" onkeyup="change_tax_amount(' + this_contDetID + ',this.value);" onkeypress="return validateFloatKeyPress(this,event)" autocomplete="off" type="text" value="0" readonly=""></div></td><td class="text-right" id="tot_' + this_contDetID + '">0</td><td><input placeholder="Remarks" type="text" size="13" id="remarks_' + this_contDetID + '"></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + this_contDetID + '" type="checkbox" value="' + this_contDetID + '"></td></tr>');

                            $('#table_body').append(str);
                            x++;
                            tot_amount += (parseFloat(value['totalAmount']));
                        });

                        if (!jQuery.isEmptyObject(data['ware_house'])) {
                            $('.whre_drop').empty();
                            var mySelect = $('.whre_drop');
                            mySelect.append($('<option></option>').val('').html('Select WareHouse'));
                            $.each(data['ware_house'], function (val, text) {
                                mySelect.append($('<option></option>').val(text['wareHouseAutoID']).html(text['wareHouseCode'] + ' | ' + text['wareHouseLocation']));
                            });
                        }

                        if (!jQuery.isEmptyObject(data['tax_master'])) {
                            $('.tax_drop').empty();
                            var mySelect = $('.tax_drop');
                            mySelect.append($('<option></option>').val('').html('Select Tax'));
                            $.each(data['tax_master'], function (val, text) {
                                mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxShortCode'] + ' | ' + text['taxPercentage']));
                            });
                        }
                    }
                    number_validation();
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


    function validate_Qty_invoice(id,Qty, balanceQty,invmaincat) {
        validate_with_wareHouse_qty(id,invmaincat);
        select_check_box(id);
        if(balanceQty < Qty) {
            $('#qty_' + id).val('');
            myAlert('w', 'Qty can not be greater than balance Qty');
        }
    }

    function getWareHouseQty(contractDetID){
        let wareHouse = $('#wareHouseAutoID'+contractDetID).val();
        $('#wareHouseQty_'+contractDetID).text(0);
        $('#qty_'+contractDetID).val('');
        if(wareHouse) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractDetID': contractDetID, 'wareHouseID': wareHouse},
                url: "<?php echo site_url('Invoices/getWareHouseItemQty'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    let this_stk = 0;
                    if(data[0] === 's'){
                        this_stk = data['stock'];
                    }
                    $('#wareHouseQty_'+contractDetID).text(this_stk)
                    $('#qty_'+contractDetID).val('');

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
    }

    function validate_with_wareHouse_qty(id,invmaincat){
        if(invmaincat=='Inventory')
        {
            let qty = $('#qty_' + id).val();
            let wareHouseQty = $('#wareHouseQty_'+id).text();

            qty = (qty === '')? 0: qty;

            if(parseFloat(qty) > parseFloat(wareHouseQty)){
                myAlert('w', 'Qty Can not be greater than warehouse qty');
                $('#qty_' + id).val('');
                return false;
            }
        }

    }

    function select_amount(id, value) {
        var tax_percentage = $('#item_taxPercentage_' + id).val();
        $('#tax_amount_' + id).val(parseFloat((tax_percentage / 100) * value).toFixed(currency_decimal));
        var amount = parseFloat($('#amount_' + id).val());
        var discount = parseFloat($('#discount_' + id).val());
        if (amount < discount) {
            myAlert('w', 'Price can not be less than discount');
            $('#amount_' + id).val('');
            $('#discount_' + id).val('0');
            $('#tot_' + id).val('0');
        }
        select_check_box(id);
    }

    function cal_tot_amount(id, value) {
        var qty = $('#qty_' + id).val();
        var amount = parseFloat($('#amount_' + id).val());
        var discount = parseFloat($('#discount_' + id).val());
        var tax_amount = $('#tax_amount_' + id).val();
        if(!tax_amount) {
            tax_amount = 0;
        }
        var tex_type = $('#tax_drop_' + id).val();
        $("#check_" + id).prop("checked", false);
        if (qty => 0 && amount > 0 || tex_type > 0)
        {
            $("#check_" + id).prop("checked", true);
        }
        if (discount > amount) {
            myAlert('w', 'Discount Can not be greater than unit price');
            $('#discount_' + id).val('0');
            var total = parseFloat(qty) * (parseFloat(amount) + parseFloat(tax_amount));
            var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', '');
            $('#tot_' + id).text(totalnew);
        }
        else {
            if (discount) {
                var total = parseFloat(qty) * (parseFloat(amount) - parseFloat(discount) + parseFloat(tax_amount));
                var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', '');
                $('#tot_' + id).text(totalnew);
            }
            else {
                var total = parseFloat(qty) * (parseFloat(amount) + parseFloat(tax_amount));
                parseFloat($('#discount_' + id).val());
                var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', '');
                $('#tot_' + id).text(totalnew);
            }

        }
    }

    function cal_con_base_tax(id, element) {
        var result = $(element).children(':selected').val();
        var amount = $('#amount_' + id).val();
        var discount = $('#discount_' + id).val();
        if (discount) {
            var total = (parseFloat(amount) - parseFloat(discount));
            if (result) {
                var result = $(element).children(':selected').text().split('|');
                var percentage = parseFloat(result[1].replace("%", ""));
                $('#item_taxPercentage_' + id).val(parseFloat(percentage).toFixed(currency_decimal));
                $('#item_taxPercentage_' + id).prop('readonly', false);
                var qty = parseFloat($('#qty_' + id).val());
                var amount = parseFloat($('#amount_' + id).val());
                $('#tax_amount_' + id).prop('readonly', false);
                $('#tax_amount_' + id).val(parseFloat((percentage / 100) * total).toFixed(currency_decimal));
            } else {
                $('#tax_amount_' + id).val(0);
                $('#item_taxPercentage_' + id).val(0);
                $('#tax_amount_' + id).prop('readonly', true);
                $('#item_taxPercentage_' + id).prop('readonly', true);
            }
        }
        else {
            var total = (parseFloat(amount));
            if (result) {
                var result = $(element).children(':selected').text().split('|');
                var percentage = parseFloat(result[1].replace("%", ""));
                $('#item_taxPercentage_' + id).val(parseFloat(percentage).toFixed(currency_decimal));
                $('#item_taxPercentage_' + id).prop('readonly', false);
                var qty = parseFloat($('#qty_' + id).val());
                var amount = parseFloat($('#amount_' + id).val());
                $('#tax_amount_' + id).prop('readonly', false);
                $('#tax_amount_' + id).val(parseFloat((percentage / 100) * total).toFixed(currency_decimal));
            } else {
                $('#tax_amount_' + id).val(0);
                $('#item_taxPercentage_' + id).val(0);
                $('#tax_amount_' + id).prop('readonly', true);
                $('#item_taxPercentage_' + id).prop('readonly', true);
            }
        }


        select_check_box(id);
    }

    function select_check_box(id) {
        var qty = $('#qty_' + id).val();
        var amount = $('#amount_' + id).val();
        var discount = $('#discount_' + id).val();
        var tax_amount = $('#tax_amount_' + id).val();
        var tex_type = $('#tax_drop_' + id).val();
        $("#check_" + id).prop("checked", false);

        let isTaxEnabled = parseInt('<?=($taxEnabled == 1 || $taxEnabled == null)? 1 : 0?>');
        if(isTaxEnabled === 0){
            tax_amount = 0;
        }

        if (qty =>
            0 && amount > 0 || tex_type > 0
        )
        {
            $("#check_" + id).prop("checked", true);
        }
        if (discount == '') {
            var total = parseFloat(qty) * (parseFloat(amount) + parseFloat(tax_amount));
            var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', '');
            $('#tot_' + id).text(totalnew);
        }
        else {
            var total = parseFloat(qty) * (parseFloat(amount) - parseFloat(discount) + parseFloat(tax_amount));
            var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', '');
            $('#tot_' + id).text(totalnew);

        }


    }

    function change_tax_per(id, percentage) {
        if (percentage > 0) {
            var amount = $('#amount_' + id).val();
            var amount = $('#amount_' + id).val();
            var discount = $('#discount_' + id).val();
            if (discount) {
                var total = (parseFloat(amount) - parseFloat(discount));
                $('#tax_amount_' + id).val((parseFloat((percentage / 100) * total)).toFixed(currency_decimal));
            } else {
                var total = (parseFloat(amount));
                $('#tax_amount_' + id).val((parseFloat((percentage / 100) * total)).toFixed(currency_decimal));
            }
        } else {
            $('#tax_amount_' + id).val(0);
        }
        select_check_box(id);
    }

    function change_tax_amount(id, discount_amount) {
        if (discount_amount > 0) {
            var total_amount = $('#amount_' + id).val();
            var amount = $('#amount_' + id).val();
            var discount = $('#discount_' + id).val();
            if (discount) {
                var total = (parseFloat(amount) - parseFloat(discount));
            }
            else {
                var total = (parseFloat(amount));
            }
            $('#item_taxPercentage_' + id).val(((parseFloat(discount_amount) / total) * 100).toFixed(currency_decimal));
        } else {
            $('#item_taxPercentage_' + id).val(0);
        }
        select_check_box(id);
    }

    function select_text_item(element) {
        if (element.value != 0) {
            var result = $(element).children(':selected').text().split('|');
            var res = parseFloat(result[2].replace("%", "")).toFixed(currency_decimal);
            $(element).closest('tr').find('.item_taxPercentage').val(res);
            $(element).closest('tr').find('.item_taxPercentage').prop('readonly', false)
        } else {
            $(element).closest('tr').find('.item_taxPercentage').val(0);
            $(element).closest('tr').find('.item_taxPercentage').prop('readonly', true)
        }
    }

    function initializeitemTypeahead(id) {
        /**var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Procurement/fetch_itemrecode/?q=%QUERY"
        });

         item.initialize();
         $('.search').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $(this).closest('tr').find('.itemAutoID').val(datum.itemAutoID)
            $(this).closest('tr').find('.estimatedAmount').val(datum.companyLocalSellingPrice)
            fetch_related_uom_id(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID, this);
        });*/

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Receipt_voucher/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
            onSelect: function (suggestion) {
                //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                //$(this).closest('tr').find('.estimatedAmount').val(suggestion.companyLocalSellingPrice);


                fetch_sales_price(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                if ($('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val()) {
                    fetch_rv_warehouse_item(suggestion.itemAutoID, this, $('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val());
                }

                $(this).closest('tr').find('.quantityRequested').focus();
                $(this).closest('tr').css("background-color", 'white');
                // $(this).closest('tr').find('.wareHouseAutoID').val('').change();
                checkitemavailable(this);
                if (suggestion.revanueGLCode == null || suggestion.revanueGLCode == '' || suggestion.revanueGLCode == 0) {
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                    }, 200);
                    $('#f_search_' + id).val('');
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    myAlert('w', 'Revenue GL code not assigned for selected item')
                }
                if (suggestion.mainCategory == 'Service') {
                    $(this).closest('tr').find('.wareHouseAutoID').removeAttr('onchange');
                } else {
                    $(this).closest('tr').find('.wareHouseAutoID').attr('onchange', 'checkitemavailable(this)');
                }
                check_item_not_approved_document_wise(suggestion.itemAutoID,'add','CINV',id);
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');
    }


    function initializeitemTypeahead_edit() {
        /**var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Procurement/fetch_itemrecode/?q=%QUERY"
        });

         item.initialize();
         $('#search').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $('#edit_itemAutoID').val(datum.itemAutoID);
            $('#edit_estimatedAmount').val(datum.companyLocalSellingPrice);
            fetch_related_uom_id(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID, this);
        });*/

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Receipt_voucher/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
            onSelect: function (suggestion) {

                setTimeout(function () {
                    $('#edit_itemAutoID').val(suggestion.itemAutoID);

                }, 200);
                //$('#edit_estimatedAmount').val(suggestion.companyLocalSellingPrice);
                $(this).closest('tr').find('#edit_wareHouseAutoID').val('').change();
                fetch_sales_price_edit(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);

                if (suggestion.revanueGLCode == null || suggestion.revanueGLCode == '' || suggestion.revanueGLCode == 0) {
                    $('#edit_itemAutoID').closest('tr').find('.itemAutoID').val('');
                    $('#edit_itemAutoID').val('');
                    $('#edit_itemAutoID').closest('tr').css("background-color", '#ffb2b2 ');
                    myAlert('w', 'Revenue GL code not assigned for selected item')
                }
                if (suggestion.mainCategory == 'Service') {
                    $('#edit_wareHouseAutoID').removeAttr('onchange');
                } else {
                    $('#edit_wareHouseAutoID').attr('onchange', 'editstockwarehousestock(this)');
                }
                check_item_not_approved_document_wise(suggestion.itemAutoID,'edit','CINV',1);
            }
        });
    }

    function fetch_invoice_direct_details() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('Invoices/fetch_invoice_direct_details_commission'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                tax_total = 0;
                discount_total = 0;
                discount_total_do = 0;
                transactionDecimalPlaces = 2;

                $('#gl_table_body,#gl_insurance_table_body,#item_table_body,#item_table_tfoot,#gl_table_tfoot,#gl_insurance_table_tfoot,#delivery_table_body,#operation_based_table_body,#operation_based_table_tfoot,#commission_table_body').empty();
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#gl_table_body,#gl_insurance_table_body,#item_table_body,#commission_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b>'+no_records_found_str+'</b></td></tr>');
                    /*No Records Found*/

                      if(taxYN == 0 && !jQuery.isEmptyObject(taxYN))
                      {
                          $('.hideTaxpolicy').addClass('hide');
                          $('.taxshowYN').addClass('hide');

                          $('.taxenable').attr('colspan',4);
                          $('.taxenablefooter').attr('colspan',8);
                          if(data['currency']['invoiceType']== "Quotation" || data['currency']['invoiceType']== "Contract" || data['currency']['invoiceType']== "Sales Order"){
                              $('.taxenable').attr('colspan',2);
                          }
                      }else{
                          $('.hideTaxpolicy').removeClass('hide');
                          $('.taxshowYN').removeClass('hide');
                          $('.taxenable').attr('colspan',6);
                          $('.taxenablefooter').attr('colspan',10);
                          if(data['currency']['invoiceType']== "Quotation" || data['currency']['invoiceType']== "Contract" || data['currency']['invoiceType']== "Sales Order" ){
                              $('.taxenable').attr('colspan',4);
                          }
                      }

                    $("#invoiceType").prop("disabled", false);
                    $("#customerID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#addcustomer").prop("disabled", false);
                    $("#editallbtn").addClass("hidden");
                    currencyID = '';
                    delete_retention_amout(invoiceAutoID);
                }
                else {
                    $("#invoiceType").prop("disabled", true);
                    $("#customerID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#addcustomer").prop("disabled", true);
                    $("#editallbtn").removeClass("hidden");
                    x = 1;
                    y = 1;
                    transactionDecimalPlaces = data['currency']['transactionCurrencyDecimalPlaces'];
                    LocalDecimalPlaces = data['currency']['companyLocalCurrencyDecimalPlaces'];
                    partyDecimalPlaces = data['currency']['customerCurrencyDecimalPlaces'];
                    retentionPercentage = data['currency']['retentionPercentage'];
                    gl_trans_amount = 0;
                    gl_local_amount = 0;
                    gl_party_amount = 0;
                    item_trans_amount = 0;
                    item_local_amount = 0;
                    item_party_amount = 0;
                    insurance_amount = 0;
                    var margin_amount = 0;
                    var delivery_tot = 0;
                    commission_item_trans_amount = 0;
                    $.each(data['detail'], function (key, value) {
                        var wareLocation='';
                        var partnoremarks ='';
                        if (value['type'] == 'Item') {
                            $('#item_table_tfoot').empty();
                            var taxexist=data['Istaxexist'];
                            val = '';
                            if (value['contractCode'] !== null) {
                                val = value['contractCode'] + ' - ';
                            }
                            if (value['wareHouseLocation'] !== null) {
                                wareLocation=value['wareHouseLocation'];
                            }
                            if((value['remarks']!= ' ') && (value['partNo'] == ''))
                            {
                                partnoremarks = value['remarks'];
                            }
                            else if((value['remarks']!= ' ') && (value['partNo']!= ''))
                            {
                                partnoremarks = value['remarks'] + ' - Part No : ' + value['partNo'];
                            }
                            else if((value['partNo']!=' '))
                            {
                                partnoremarks =  ' -  Part No : ' + value['partNo'];
                            }
                            if (value['isSubitemExist'] == 1) {
                                var colour = 'color: #dad835 !important';
                                colour = '';


                                //string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] + '-' + value['wareHouseLocation'] + ' - ' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '(' + value['discountPercentage'] + '%)</td><td class="text-right">' + parseFloat(value['unittransactionAmount']-value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount']-value['discountAmount']))).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * value['taxAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['invoiceDetailsAutoID'] + ',\'CINV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',\'' + value['itemDescription'] + '\',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + ' - ' + value['itemSecondaryCode'] + '</td><td>' + val + value['itemDescription'] +  '-' + wareLocation + ' - ' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '(' + value['discountPercentage'] + '%)</td><td class="text-right">' + parseFloat(value['unittransactionAmount'] - value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount'] - value['discountAmount']))).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * value['taxAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['invoiceDetailsAutoID'] + ',\'CINV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                            } else {
                                if (data['currency']['invoiceType'] == "Direct") {
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + ' - ' + value['itemSecondaryCode'] + '</td><td>' + val + value['itemDescription'] + '-' + wareLocation + ' - ' + partnoremarks  + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '(' + value['discountPercentage'] + '%)</td><td class="text-right">' + parseFloat(value['unittransactionAmount'] - value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right taxshowYN">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount'] - value['discountAmount']))).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right taxshowYN">' + parseFloat( value['totalAfterTax']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                } else {
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + ' - ' + value['itemSecondaryCode'] + '</td><td>' + val + value['itemDescription'] + '-' + wareLocation + ' - ' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>' + value['discountAmount'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount'] - value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right taxshowYN">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount'] - value['discountAmount']))).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right taxshowYN">' + parseFloat( value['totalAfterTax']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }

                            }
                            $('#item_table_body').append(string);

                            x++;
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            item_trans_amount += (parseFloat(value['transactionAmount']));
                            item_local_amount += (parseFloat(value['companyLocalAmount']));
                            item_party_amount += (parseFloat(value['customerAmount']));
                            discount_total += (parseFloat(value['transactionAmount']));
                            if (data['currency']['invoiceType'] == "Direct") {
                                $('#item_table_tfoot').append('<tr><td colspan="10" class="text-right taxenablefooter"><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?> </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');

                            } else {
                                $('#item_table_tfoot').append('<tr><td colspan="10" class="text-right taxenablefooter"><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?>  </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            }
                            if((taxYN == 0 &&  !jQuery.isEmptyObject(taxYN))&& taxexist !=1)
                            {
                                $('.hideTaxpolicy').addClass('hide');
                                $('.taxshowYN').addClass('hide');

                                $('.taxenable').attr('colspan',4);
                                $('.taxenablefooter').attr('colspan',8);
                                if(data['currency']['invoiceType']== "Quotation" || data['currency']['invoiceType']== "Contract" || data['currency']['invoiceType']== "Sales Order"){
                                    $('.taxenable').attr('colspan',2);
                                }
                            }else{
                                $('.hideTaxpolicy').removeClass('hide');
                                $('.taxshowYN').removeClass('hide');
                                $('.taxenable').attr('colspan',6);
                                $('.taxenablefooter').attr('colspan',10);
                                if(data['currency']['invoiceType']== "Quotation" || data['currency']['invoiceType']== "Contract" || data['currency']['invoiceType']== "Sales Order" ){
                                    $('.taxenable').attr('colspan',4);
                                }
                            }

                        }

                        else if(value['type'] == 'insurance'){
                            $('#gl_insurance_table_tfoot').empty();
                            $('#gl_insurance_table_body').append('<tr><td>' + y + '</td><td>' + value['supplierName'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(value['transactionAmount']-value['marginAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + value['marginPercentage'] + '</td><td class="text-right">' + parseFloat(value['marginAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_insurance_detail(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');

                            y++;
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            gl_trans_amount += (parseFloat(value['transactionAmount']));
                            discount_total += (parseFloat(value['transactionAmount']));
                            insurance_amount += parseFloat(value['transactionAmount']-value['marginAmount']);
                            margin_amount += parseFloat(value['marginAmount']);
                            $('#gl_insurance_table_tfoot').append('<tr><td colspan="3" class="text-right"> Total </td><td class="text-right total">' + parseFloat(insurance_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right total" colspan="2">' + parseFloat(margin_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right total">' + parseFloat(gl_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td></tr>');
                        }
                        else if(value['type'] == 'DO'){
                            $('.tab_3_item_CINV').removeClass('hide');
                            $('#delivery_table_tfoot').empty();

                            var do_str = '<tr><td>' + y + '</td><td>' + value['DOCode'] + '</td><td style="text-align: center">' + value['DODate'] + '</td><td>' + value['referenceNo'] + '</td>';
                            do_str += '<td class="text-right">' + parseFloat(value['do_tr_amount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td>';
                            do_str += '<td class="text-right">' + parseFloat(value['due_amount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td>';
                            do_str += '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td>';
                            do_str += '<td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td>';
                            do_str += '<td class="text-right"><a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',3);"><span class="glyphicon glyphicon-trash delete-icon"></span></a></td></tr>';
                            $('#delivery_table_body').append(do_str);
                            discount_total_do +=  (parseFloat(value['transactionAmount']));
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            y++;

                            delivery_tot += parseFloat(value['transactionAmount']);

                            $('#delivery_table_tfoot').append('<tr><td colspan="6" class="text-right">Total</td><td class="text-right total">' + parseFloat(delivery_tot).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td colspan="3"></td></tr>');
                        }
                        else if(value['type'] == 'OP') {
                            $('#operation_based_table_tfoot').empty();
                            var transamnt=parseFloat(value['transactionAmount'])+parseFloat(value['discountAmount']);
                            $('#operation_based_table_body').append('<tr><td>' + y + '</td><td>' + value['revenueGLCode'] + '</td><td>' + value['segmentCode'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-center">' + value['description'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['contractQty'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(transactionDecimalPlaces, '.', ',') + '</td>  <td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct_op(' + value['invoiceDetailsAutoID'] + ',\'' + value['GLDescription'] + '\',1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            y++;
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            gl_trans_amount += (parseFloat(value['transactionAmount']));
                            discount_total += (parseFloat(value['transactionAmount']));
                            if(retentionPercentage>0){
                                var retamnt=((retentionPercentage/100)*gl_trans_amount);
                                var netamnt=gl_trans_amount-((retentionPercentage/100)*gl_trans_amount);
                            }else{
                                var retamnt= 0;
                                var netamnt= gl_trans_amount;
                            }

                            $('#operation_based_table_tfoot').append('<tr><td colspan="8" class="text-right"> Total </td><td class="text-right total">' + parseFloat(gl_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td></tr><tr><td colspan="8" class="text-right total">Retention % <input type="number" style="width: 44px;" onchange="saveRetentionAmnt(' + invoiceAutoID + ',' + gl_trans_amount + ')" step="any" id="retentionPercentage_' + invoiceAutoID + '" name="retentionPercentage" value="' + retentionPercentage + '"></td><td class="text-right total">' + parseFloat(retamnt).formatMoney(transactionDecimalPlaces, '.', ',') + '</td></tr><tr><td colspan="8" class="text-right"> Net Total </td><td class="text-right total">' + parseFloat(netamnt).formatMoney(transactionDecimalPlaces, '.', ',') + '</td></tr>');
                        } 
                        else if(value['type'] == 'Commission'){
                            $('#commission_table_tfoot').empty();
                            //var taxexist=data['Istaxexist'];
                            val = '';
                            if (value['contractCode'] !== null) {
                                val = value['contractCode'] + ' - ';
                            }
                            if (value['wareHouseLocation'] !== null) {
                                wareLocation=value['wareHouseLocation'];
                            }
                            if((value['remarks']!= ' ') && (value['partNo'] == ''))
                            {
                                partnoremarks = value['remarks'];
                            }
                            else if((value['remarks']!= ' ') && (value['partNo']!= ''))
                            {
                                partnoremarks = value['remarks'] + ' - Part No : ' + value['partNo'];
                            }
                            else if((value['partNo']!=' '))
                            {
                                partnoremarks =  ' -  Part No : ' + value['partNo'];
                            }
                            if (value['isSubitemExist'] == 1) {
                                var colour = 'color: #dad835 !important';
                                colour = '';
                                //string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] + '-' + value['wareHouseLocation'] + ' - ' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '(' + value['discountPercentage'] + '%)</td><td class="text-right">' + parseFloat(value['unittransactionAmount']-value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount']-value['discountAmount']))).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * value['taxAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['invoiceDetailsAutoID'] + ',\'CINV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',\'' + value['itemDescription'] + '\',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                string = '<tr><td>' + x + '</td><td>'+ value['SalesPersonCode'] +'</td><td>'+ value['SalesPersonName'] +'</td><td>'+ value['DesDescription'] +'</td><td>' + value['itemSystemCode'] + '' + value['itemSecondaryCode'] + '</td><td>' + val + value['itemDescription'] +  '-' + wareLocation + ' - ' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">&nbsp;&nbsp;<a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['invoiceDetailsAutoID'] + ',\'CINV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                            } else {
                                /* if (data['currency']['invoiceType'] == "Direct") {
                                    string = '<tr><td>' + x + '</td><td>'+ value['SalesPersonCode'] +'</td><td>'+ value['SalesPersonName'] +'</td><td>' + value['itemSystemCode'] + ' - ' + value['itemSecondaryCode'] + '</td><td>' + val + value['itemDescription'] + '-' + wareLocation + ' - ' + partnoremarks  + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                } else { */
                                string = '<tr><td>' + y + '</td>><td>'+ value['SalesPersonCode'] +'</td><td>'+ value['SalesPersonName'] +'</td><td>'+ value['DesDescription'] +'</td><td>' + value['itemSystemCode'] + ' - ' + value['itemSecondaryCode'] + '</td><td>' + val + value['itemDescription'] + '-' + wareLocation + ' - ' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">&nbsp;&nbsp;<a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                //}

                            }
                            $('#commission_table_body').append(string);

                            y++;
                            //tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            //alert(item_trans_amount);
                            commission_item_trans_amount += (parseFloat(value['transactionAmount']));
                           // commission_item_local_amount += (parseFloat(value['companyLocalAmount']));
                            //commission_item_party_amount += (parseFloat(value['customerAmount']));
                            //commission_discount_total += (parseFloat(value['transactionAmount']));
                           // if (data['currency']['invoiceType'] == "Direct") {
                               // $('#commission_table_tfoot').append('<tr><td colspan="8" class="text-right "><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?> </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');

                            //} else {
                                $('#commission_table_tfoot').append('<tr><td colspan="9" class="text-right "><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?>  </td><td class="text-right total">' + parseFloat(commission_item_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                           // }
                            if((taxYN == 0 &&  !jQuery.isEmptyObject(taxYN))&& taxexist !=1)
                            {
                                /* $('.hideTaxpolicy').addClass('hide');
                                $('.taxshowYN').addClass('hide');

                                $('.taxenable').attr('colspan',4);
                                $('.taxenablefooter').attr('colspan',8);
                                if(data['currency']['invoiceType']== "Quotation" || data['currency']['invoiceType']== "Contract" || data['currency']['invoiceType']== "Sales Order"){
                                    $('.taxenable').attr('colspan',2);
                                } */
                            }else{
                                /* $('.hideTaxpolicy').removeClass('hide');
                                $('.taxshowYN').removeClass('hide');
                                $('.taxenable').attr('colspan',6);
                                $('.taxenablefooter').attr('colspan',10);
                                if(data['currency']['invoiceType']== "Quotation" || data['currency']['invoiceType']== "Contract" || data['currency']['invoiceType']== "Sales Order" ){
                                    $('.taxenable').attr('colspan',4);
                                } */
                            }
                        }
                        else {
                            $('#gl_table_tfoot').empty();
                            var transamnt=parseFloat(value['transactionAmount'])+parseFloat(value['discountAmount']);
                            $('#gl_table_body').append('<tr><td>' + y + '</td><td>' + value['revenueGLCode'] + '</td><td>' + value['revenueGLDescription'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(transactionDecimalPlaces, '.', ',') + '</td> <td class="text-right">(' + parseFloat(value['discountPercentage']).formatMoney(2, '.', ',') + ' %) ' + parseFloat(value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td> <td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_gl_item(' + value['invoiceDetailsAutoID'] + ',\'' + value['GLDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',\'' + value['GLDescription'] + '\',1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            //<td class="text-right">' + parseFloat(value['companyLocalAmount']).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['customerAmount']).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                            y++;
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            gl_trans_amount += (parseFloat(value['transactionAmount']));
                            discount_total += (parseFloat(value['transactionAmount']));
                            //gl_local_amount += (parseFloat(value['companyLocalAmount']));
                            //gl_party_amount += (parseFloat(value['customerAmount']));
                            $('#gl_table_tfoot').append('<tr><td colspan="6" class="text-right"> Total </td><td class="text-right total">' + parseFloat(gl_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td></tr>');
                            //<td class="text-right total">' + parseFloat(gl_local_amount).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right total">' + parseFloat(gl_party_amount).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                        }
                    });
                }
                //general discount
                var d_total = 0;
                var d_percent_total = 0;
                $('#discount_tot').text('<?php echo $this->lang->line('sales_marketing_discount_applicable_amount');?> ( ' + parseFloat((discount_total+discount_total_do)).formatMoney(data['currency']['transactionCurrencyDecimalPlaces'], '.', ',') + ' )');
                $('#discount_tot_hn').val((discount_total+discount_total_do));
                if(invoiceType=="Insurance") {
                    $('#discount_margin_tot_hn').val(margin_amount);
                }
                $('#discount_table_body_recode,#discount_table_footer').empty();
                if (jQuery.isEmptyObject(data['discount_detail'])) {
                    $('#discount_table_body_recode').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    $('#discountPercentageTothn').val(d_percent_total)
                    if(invoiceType=="Insurance") {
                        $('#discount_footer_tot_hn').val(parseFloat(d_total));
                    }
                } else {
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#invoiceType").prop("disabled", true);
                    d = 1;
                    $.each(data['discount_detail'], function (key, value) {
                        $('#discount_table_body_recode').append('<tr><td>' + d + '</td><td>Discount</td><td>' + value['discountDescription'] + '</td><td class="text-right">' + parseFloat(value['discountPercentage']).toFixed(currency_decimal) + '% </td><td class="text-right">' + parseFloat((parseFloat(value['discountPercentage']) / 100) * (discount_total+discount_total_do)).formatMoney(data['currency']['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right"><a onclick="delete_discount_gen(' + value['discountDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        d++;
                        d_total += parseFloat((parseFloat(value['discountPercentage']) / 100) * (discount_total+discount_total_do));
                        d_percent_total += parseFloat(value['discountPercentage']) ;
                    });
                    if (d_total > 0) {
                        $('#discount_table_footer').append('<tr><td colspan="4" class="text-right">Discount Total </td><td class="text-right total">' + parseFloat(d_total).formatMoney(data['currency']['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td>&nbsp;</td></tr>');
                        $('#discountPercentageTothn').val(d_percent_total)
                    }

                    if(invoiceType=="Insurance") {
                        if (d_total > 0) {
                            $('#discount_footer_tot_hn').val(parseFloat(d_total));
                        }
                    }
                }

                //Extra Charges
                t_extraCharge=0;
                $('#extra_table_body_recode,#extra_table_footer').empty();
                if (jQuery.isEmptyObject(data['extraChargeDetail'])) {
                    $('#extra_table_body_recode').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                   
                } else {
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#invoiceType").prop("disabled", true);
                    e = 1;
                    ex_total = 0;
                    $.each(data['extraChargeDetail'], function (key, value) {
                        $('#extra_table_body_recode').append('<tr><td>' + e + '</td><td>Extra Charge</td><td>' + value['extraChargeDescription'] + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(data['currency']['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td class="text-right"><a onclick="delete_extra_gen(' + value['extraChargeDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        e++;
                        ex_total += parseFloat(value['transactionAmount']);
                        if(value['isTaxApplicable']==1){
                            t_extraCharge += parseFloat(value['transactionAmount']);
                        }
                    });
                    if (ex_total > 0) {
                        $('#extra_table_footer').append('<tr><td colspan="3" class="text-right">Extra Charge Total </td><td class="text-right total">' + parseFloat(ex_total).formatMoney(data['currency']['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td>&nbsp;</td></tr>');
                    }
                }
                $('#tax_tot').text('<?php echo $this->lang->line('sales_markating_transaction_tax_applicable_amount');?>( ' + parseFloat(tax_total-d_total+t_extraCharge).formatMoney(data['currency']['transactionCurrencyDecimalPlaces'], '.', ',') + ' )');
                $('#tax_tot_hn_val').val(tax_total-d_total+t_extraCharge);
                $('#tax_table_body_recode,#tax_table_footer').empty();
                /*Tax Applicable Amount */
                if (jQuery.isEmptyObject(data['tax_detail'])) {
                    $('#tax_table_body_recode').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                   
                } else {
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#invoiceType").prop("disabled", true);
                    x = 1;
                    t_total = 0;
                    $.each(data['tax_detail'], function (key, value) {
                        $('#tax_table_body_recode').append('<tr><td>' + x + '</td><td>' + value['taxShortCode'] + '</td><td>' + value['taxDescription'] + '</td><td class="text-right">' + value['taxPercentage'] + '% </td><td class="text-right">' + parseFloat((parseFloat(value['taxPercentage']) / 100) * (tax_total-d_total+t_extraCharge)).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="delete_tax(' + value['taxDetailAutoID'] + ',\'' + value['taxShortCode'] + '\',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        x++;
                        t_total += parseFloat((parseFloat(value['taxPercentage']) / 100) * (tax_total-d_total+t_extraCharge));
                    });
                    if (t_total > 0) {
                        $('#tax_table_footer').append('<tr><td colspan="4" class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_tax_tot');?> </td><td class="text-right total">' + parseFloat(t_total).formatMoney(data['currency']['transactionCurrencyDecimalPlaces'], '.', ',') + '</td><td>&nbsp;</td></tr>');
                    }
                }
                stopLoad();

                if( $('#item_table_body > tr').length == 0 ){
                    $('#item_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b>'+no_records_found_str+'</b></td></tr>')

                    if((taxYN == 0 &&  !jQuery.isEmptyObject(taxYN)))
                    {
                        $('.hideTaxpolicy').addClass('hide');
                        $('.taxshowYN').addClass('hide');

                        $('.taxenable').attr('colspan',4);
                        $('.taxenablefooter').attr('colspan',8);
                        if(data['currency']['invoiceType']== "Quotation" || data['currency']['invoiceType']== "Contract" || data['currency']['invoiceType']== "Sales Order"){
                            $('.taxenable').attr('colspan',2);
                        }
                    }else{
                        $('.hideTaxpolicy').removeClass('hide');
                        $('.taxshowYN').removeClass('hide');
                        $('.taxenable').attr('colspan',6);
                        $('.taxenablefooter').attr('colspan',10);
                        if(data['currency']['invoiceType']== "Quotation" || data['currency']['invoiceType']== "Contract" || data['currency']['invoiceType']== "Sales Order" ){
                            $('.taxenable').attr('colspan',4);
                        }
                    }

                }

                if( $('#operation_based_table_body > tr').length == 0 ){
                    $('#operation_based_table_body').append('<tr class="danger"><td colspan="10" class="text-center"><b>'+no_records_found_str+'</b></td></tr>')
                }

                if( $('#gl_table_body > tr').length == 0 ){
                    $('#gl_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b>'+no_records_found_str+'</b></td></tr>')
                }

                if( $('#delivery_table_body > tr').length == 0 ){
                    $('#delivery_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b>'+no_records_found_str+'</b></td></tr>')
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function customerinvoiceGLUpdate(all) {
        var $form = $('#stock_adjustment_gl_form');
        var data = $form.serializeArray();
        data.push({name: "applyAll", value: all});
        data.push({name: "masterID", value: invoiceAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/customerinvoiceGLUpdate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                setTimeout(function () {
                    fetch_details(2);
                }, 800);

                $('#stockadjustmentSwitch').modal('hide');

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function edit_glaccount(invoiceDetailsAutoID, PLGLAutoID, BLGLAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {PLGLAutoID: PLGLAutoID, BLGLAutoID: BLGLAutoID},
            url: "<?php echo site_url('Inventory/stockAdjustment_load_gldropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#divglAccount').html(data);
                $('#detailID').val(invoiceDetailsAutoID);
                $('#stockadjustmentSwitch').modal('show');

                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function cal_tax_amount(discount_amount, total_amount) {
        var tottaxappamnt= $('#tax_tot_hn_val').val();

        if (tottaxappamnt && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / tottaxappamnt) * 100).toFixed(0));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_tax(discount, total_amount) {
        var tottaxappamnt= $('#tax_tot_hn_val').val();
        if (tottaxappamnt && discount) {
            $('#tax_amount').val(parseFloat((tottaxappamnt / 100) * parseFloat(discount)).toFixed(currency_decimal));
        } else {
            $('#tax_amount').val(0);
        }
    }

    function delete_tax(id, value) {
        if (invoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning", /*warning*/
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
                        data: {'taxDetailAutoID': id},
                        url: "<?php echo site_url('Receivable/delete_tax_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            setTimeout(function () {
                                tab_active(2);
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
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

    function edit_item(id, value) {
        var InvoiceNewType = $('#invoiceType').val();
        //alert(InvoiceNewType);
        //$("#edit_wareHouseAutoID").val(null).trigger("change");
        $('#edit_invoice_item_detail_form')[0].reset();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                type: "warning", /*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'invoiceDetailsAutoID': id},
                    url: "<?php echo site_url('Invoices/fetch_customer_invoice_detail_commission'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        //pv_item_detail_modal();
                        invoiceDetailsAutoID = data['invoiceDetailsAutoID'];
                        projectID = data['projectID'];

                        projectcategory = data['project_categoryID'];
                        projectsubcat = data['project_subCategoryID'];
                        $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                        //$('#search').typeahead('val', data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                        $('#conversionRateEdit').val(data['conversionRateUOM']);
                        fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                        $('#edit_quantityRequested').val(data['requestedQty']);
                        $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount'])));
                        if (data['invoiceType'] == "Direct") {
                            $('#edit_discount_amount').val(data['discountAmount']);
                            $('#edit_discount').val(data['discountPercentage']);
                        } else {
                            $('#edit_discount_amount').val(data['discountAmount']);
                            $('#edit_discount').val(data['discountPercentage']);
                        }
                        //$('#edit_search_id').val(data['itemSystemCode']);
                        //$('#edit_itemSystemCode').val(data['itemSystemCode']);
                        $('#edit_itemAutoID').val(data['itemAutoID']);
                        $('#edit_itemDescription').val(data['itemDescription']);
                        $('#edit_wareHouseAutoID').val(data['wareHouseAutoID']).change();
                        $('#edit_salesPersonID').val(data['salesPersonID']).change();

                        $('#edit_remarks').val(data['remarks']);
                        if (data['mainCategory'] == "Service") {
                            $('#currentstock_edit').val(0);
                        } else {
                            var stock = data.currentStock;
                            if (parseFloat(data['conversionRateUOM']) > 0) {
                                stock = parseFloat(data.currentStock) * parseFloat(data['conversionRateUOM']);
                            }
                            $('#currentstock_edit').val(stock);
                        }
                        $('#mainCategoryhn').val(data['mainCategory']);
                        $('#edit_item_text').val(data['taxMasterAutoID']);
                        $('#edit_item_taxPercentage').val(data['taxPercentage']);
                        if (data['taxPercentage'] != 0) {
                            $('#edit_item_taxPercentage').prop('readonly', false);
                        } else {
                            $('#edit_item_taxPercentage').prop('readonly', true);
                        }
                        $('#contractBalance').val(data['balanceQty']);
                        if (InvoiceNewType == 'Quotation' || InvoiceNewType == 'Contract' || InvoiceNewType == 'Sales Order') {
                            $('#edit_UnitOfMeasureID').prop("disabled", true);
                            $('#search').prop("disabled", true);
                            $('#contractItem').val(1);
                        } else {
                            $('#contractItem').val(0);
                        }
                        $('.hideTaxpolicy_edit').removeClass('hide');
                        if(taxYN == 0  && (!jQuery.isEmptyObject(taxYN)) && data['taxPercentage'] <=0)
                        {
                            $('.hideTaxpolicy_edit').addClass('hide');
                        }else{
                            $('.hideTaxpolicy_edit').removeClass('hide');
                        }
                        load_segmentBase_projectID_itemEdit(data['segmentID']);
                        $("#edit_invoice_item_detail_modal").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function edit_gl_item(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_edit_this_document');?>", /*You want to edit this record!*/
                type: "warning", /*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $("#edit_gl_code").val(null).trigger("change");
                $('#edit_invoice_detail_form').trigger("reset");
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'invoiceDetailsAutoID': id},
                    url: "<?php echo site_url('Invoices/fetch_customer_invoice_detail_commission'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        invoiceDetailsAutoID = data['invoiceDetailsAutoID'];
                        projectcategory = data['project_categoryID'];
                        projectsubcat = data['project_subCategoryID'];
                        projectIDEdit = data['projectID'];
                        load_segmentBase_projectID_incomeEdit(data['segmentID'], data['projectID']);
                        $('#edit_gl_code').val(data['revenueGLAutoID']).change();
                        $('#edit_segment_gl').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#edit_amount').val((parseFloat(data['transactionAmount'])+parseFloat(data['discountAmount'])).toFixed(currency_decimal));
                        $('#discountPercentage_edit').val(parseFloat(data['discountPercentage']).toFixed(2));
                        $('#discountAmount_edit').val(parseFloat(data['discountAmount']).toFixed(currency_decimal));
                        $('#Netamount_edit').val(parseFloat(data['transactionAmount']).toFixed(currency_decimal));
                        $('#edit_description').val(data['description']);
                        $("#edit_invoice_detail_modal").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function delete_item_direct(id,tab) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning", /*warning*/
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
                    data: {'invoiceDetailsAutoID': id},
                    url: "<?php echo site_url('Invoices/delete_item_direct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        fetch_details(tab);
                        setTimeout(function () {
                            tab_active(tab);
                        }, 300);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function tab_active(id) {

        $(".nav-tabs a[href='#tab_" + id + "']").tab('show');
        fetch_invoice_direct_details();
        //fetch_details();
    }

    function save_con_base_items() {
        var selected = [];
        var amount = [];
        var qty = [];
        var wareHouseAutoID = [];
        var whrehouse = [];
        var tex_id = [];
        var tex_type = [];
        var tex_percntage = [];
        var tex_amount = [];
        var remarks = [];
        var discount = [];
        $('#table_body input:checked').each(function () {
            if ($('#amount_' + $(this).val()).val() == '') {
                swal("Cancelled", "Received cost cannot be blank !", "error");
            } else {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                qty.push($('#qty_' + $(this).val()).val());
                wareHouseAutoID.push($('#wareHouseAutoID' + $(this).val()).val());
                whrehouse.push($('#wareHouseAutoID' + $(this).val() + ' option:selected').text());
                tex_id.push($('#tax_drop_' + $(this).val()).val());
                tex_type.push($('#tax_drop_' + $(this).val() + ' option:selected').text());
                tex_percntage.push($('#item_taxPercentage_' + $(this).val()).val());
                tex_amount.push($('#tax_amount_' + $(this).val()).val());
                remarks.push($('#remarks_' + $(this).val()).val());
                discount.push($('#discount_' + $(this).val()).val());
            }
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'DetailsID': selected,
                    'invoiceAutoID': invoiceAutoID,
                    'amount': amount,
                    'qty': qty,
                    'wareHouseAutoID': wareHouseAutoID,
                    'whrehouse': whrehouse,
                    'tex_id': tex_id,
                    'tex_type': tex_type,
                    'tex_percntage': tex_percntage,
                    'tex_amount': tex_amount,
                    'remarks': remarks,
                    'discount': discount
                },
                url: "<?php echo site_url('Invoices/save_con_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#invoice_con_base_modal').modal('hide');
                        //$('#myTab li:eq(2) a').tab('show');
                        // setTimeout(function () {

                        // }, 300);
                        setTimeout(function () {
                            fetch_details(tabID);
                            tab_active(tabID);
                        }, 1000);

                    }
                }, error: function () {
                    //$('#invoice_con_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function add_more() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#item_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        /*appendData.find('#expenseGlAutoID_1').attr('id', '')
         appendData.find('#liabilityGlAutoID_1').attr('id', '')
         appendData.find('#ifSlab_1').attr('id', '')*/
        appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.discount_amount').val(0);
        appendData.find('.discount').val(0);
        appendData.find('.wareHouseAutoID').val($('#item_add_table tbody tr:first').find('.wareHouseAutoID').val());
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#item_add_table').append(appendData);
        var lenght = $('#item_add_table tbody tr').length - 1;
        $(".select2").select2();
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        initializeitemTypeahead(search_id);
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function saveInvoiceItemDetail() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#invoice_item_detail_form').serializeArray();
        if (invoiceAutoID) {
            data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
            data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
            data.push({'name': 'invoiceType', 'value': invoiceType});

            //data.push({'name': 'wareHouse', 'value': $('#wareHouseAutoID option:selected').text()});
            //data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
            $('select[name="wareHouseAutoID[]"] option:selected').each(function () {
                data.push({'name': 'wareHouse[]', 'value': $(this).text()})
            });

            $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
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

            $('.wareHouseAutoID').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });

            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Invoices/save_invoice_item_detail_commission'); ?>",
                    beforeSend: function () {
                        startLoad();
                        // $('.umoDropdown').prop("disabled", true);
                    },
                    success: function (data) {
                        invoiceDetailsAutoID = null;
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            setTimeout(function () {
                                tab_active(tabID);
                            }, 300);
                            $('#invoice_item_detail_modal').modal('hide');
                            $('#invoice_item_detail_form')[0].reset();
                            $('.select2').select2('');
                        }
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
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

    function add_more_income() {
        $('select.select2').select2('destroy');
        var appendData = $('#income_add_table tbody tr:first').clone();

        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.segment_glAdd ').val(defaultSegment);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#income_add_table').append(appendData);
        $(".select2").select2();
        number_validation();
    }

    function saveDirectInvoiceDetails() {
        var data = $('#invoice_detail_form').serializeArray();
        data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
        data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});

        $('select[name="gl_code[]"] option:selected').each(function () {
            data.push({'name': 'gl_code_des[]', 'value': $(this).text()})
        })

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/save_direct_invoice_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                invoiceDetailsAutoID = null;
                //myAlert(data['type'], data['mesage']);
                refreshNotifications(true);
                stopLoad();

                if (data != false) {
                    setTimeout(function () {
                        tab_active(tabID);
                    }, 300);
                    $('#invoice_detail_modal').modal('hide');
                    $('#invoice_detail_form')[0].reset();
                    $('.select2').select2('');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function Update_Invoice_Income() {
        var $form = $('#edit_invoice_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
        data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
        data.push({'name': 'gl_code_des', 'value': $('#edit_gl_code option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/update_income_invoice_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    invoiceDetailsAutoID = null;
                    setTimeout(function () {
                        tab_active(tabID);
                    }, 300);
                    $('#edit_invoice_detail_modal').modal('hide');
                    $('#edit_invoice_detail_form')[0].reset();
                    $('.select2').select2('');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function Update_Invoice_Item_Detail() {
        $('#edit_UnitOfMeasureID').prop("disabled", false);
        $('#search').prop("disabled", false);
        var $form = $('#edit_invoice_item_detail_form');
        var data = $form.serializeArray();
        if (invoiceAutoID) {
            data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
            data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
            data.push({'name': 'invoiceType', 'value': invoiceType});            
            data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Invoices/update_invoice_item_detail_commission'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        invoiceDetailsAutoID = null;
                        $('#edit_invoice_item_detail_form')[0].reset();
                        setTimeout(function () {
                            tab_active(tabID);
                            $('#edit_invoice_item_detail_modal').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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

    function remove_item_all_description(e, ths) {
        //$('#edit_itemAutoID').val('');
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }

    }

    function remove_item_all_description_edit(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $('#edit_itemAutoID').val('');
            $('#currentstock_edit').val('').change();

        }

    }

    function fetch_sales_price(salesprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: invoiceAutoID,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_customerinvoicemaster',
                primaryKey: 'invoiceAutoID',
                customerAutoID: '<?php echo $master['customerID']; ?>'
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

    function fetch_sales_price_edit(salesprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: invoiceAutoID,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_customerinvoicemaster',
                primaryKey: 'invoiceAutoID',
                customerAutoID: '<?php echo $master['customerID']; ?>'
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price_customerWise'); ?>",
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

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
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


    function load_segmentBase_projectID_item() {
        var segment = $('#segment').val();
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

    function load_segmentBase_projectID_itemEdit(segment) {
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

    function load_segmentBase_projectID_income(segment) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_multiple"); ?>',
            dataType: 'html',
            data: {segment: segment.value},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(segment).closest('tr').find('.div_projectID_income').html(data);
                //$('.div_projectID_income').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_segmentBase_projectID_incomeEdit(segment, selectValue) {
        var type = 'income';
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
                $('#edit_div_projectID_income').html(data);
                $('.select2').select2();

                if (selectValue) {
                    $("#projectID_income").val(selectValue).change()
                } else if(projectIDEdit){
                    $("#projectID_income").val(projectIDEdit).change()
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function cal_discount(element) {
        if (element.value < 0 || element.value > 100 || element.value == '') {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $(element).closest('tr').find('.discount').val(parseFloat(0));
            $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        } else {
            var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount_amount').val(parseFloat((estimatedAmount / 100) * parseFloat(element.value)).toFixed(currency_decimal))
            }
        }
    }

    function cal_discount_amount(element) {
        var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
        if (element.value > estimatedAmount) {
            myAlert('w', 'Discount amount should be less than or equal to Amount');
            $(element).closest('tr').find('.discount').val(0);
            $(element).val(0)
        } else {
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount').val(((parseFloat(element.value) / estimatedAmount) * 100).toFixed(currency_decimal))
            }
        }
        //net_amount(element);
    }

    function edit_cal_discount(discount) {
        var estimatedAmount = parseFloat($('#edit_estimatedAmount').val());
        if (discount < 0 || discount > 100) {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $('#edit_discount').val(0);
            $('#edit_discount_amount').val(0);
        } else {
            if (estimatedAmount) {
                $('#edit_discount_amount').val((estimatedAmount / 100) * parseFloat($('#edit_discount').val()))
            }
        }
    }

    function edit_cal_discount_amount() {
        var estimatedAmount = parseFloat($('#edit_estimatedAmount').val());
        var discountAmount = parseFloat($('#edit_discount_amount').val());
        if (discountAmount > estimatedAmount) {
            swal("Cancelled", "Discount Amount should be less than the Sales Price", "error");
            $('#edit_discount').val(0);
            $('#edit_discount_amount').val(0);
        } else {
            if (estimatedAmount) {
                $('#edit_discount').val(((parseFloat(discountAmount) / estimatedAmount) * 100).toFixed(currency_decimal))
            }
        }
    }

    function change_amount(element) {
        $(element).closest('tr').find('.discount').val(parseFloat(0));
        $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
    }

    function edit_change_amount(element) {
        $('#edit_discount').val(parseFloat(0));
        $('#edit_discount_amount').val(parseFloat(0));
    }


    function checkitemavailable(det) {
        var itmID = $(det).closest('tr').find('.itemAutoID').val();
        var searchID = $(det).closest('tr').find('.f_search').attr('id');
        var warehouseid = $(det).closest('tr').find('.wareHouseAutoID').val();
        var concatarr = new Array();
        var mainconcat;
        if (itmID && warehouseid) {
            mainconcat = itmID.concat('|').concat(warehouseid);
        }

        $('.itemAutoID').each(function (key, value) {
            var itm = this.value;
            var wareHouseAutoID = $(this).closest('tr').find('.wareHouseAutoID').val();
            var searchID2 = $(this).closest('tr').find('.f_search').attr('id');
            var concatvalue = itm.concat('|').concat(wareHouseAutoID);
            if (searchID != searchID2) {
                if (mainconcat) {
                    concatarr.push(concatvalue);
                }
            }
        });

        if (warehouseid != '') {
            fetch_rv_warehouse_item(itmID, det, warehouseid)
        }

        //if (concatarr.length > 1) {
        // if (jQuery.inArray(mainconcat, concatarr) !== -1) {
        //     $(det).closest('tr').find('.f_search').val('');
        //     $(det).closest('tr').find('.itemAutoID').val('');
        //     $(det).closest('tr').find('.wareHouseAutoID').val('').change();
        //     $(det).closest('tr').find('.quantityRequested').val('');
        //     $(det).closest('tr').find('.estimatedAmount').val('');
        //     $(det).closest('tr').find('.discount').val('');
        //     $(det).closest('tr').find('.discount_amount').val('');
        //     $(det).closest('tr').find('.currentstock').val('');
        //     myAlert('w', 'Selected item is already selected');
        // }

        //}
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }

    }

    function checkCurrentStock(det) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        if (det.value > parseFloat(currentStock)) {
            myAlert('w', 'quantity should be less than or equal to current stock');
            $(det).val(0);
        }

        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }

    function checkCurrentStockAll(det,mainCategory) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        if(mainCategory !=='Service') {
            if (det.value > parseFloat(currentStock)) {
                myAlert('w', 'quantity should be less than or equal to current stock');
                $(det).val(0);
            }
        }
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }

    }

    function checkCurrentStockEdit() {
        var currentStock = $('#currentstock_edit').val();
        var TransferQty = $('#edit_quantityRequested').val();
        var mainCategory = $('#mainCategoryhn').val();
        if(mainCategory !=='Service'){
            if (parseFloat(TransferQty) > parseFloat(currentStock)) {
                myAlert('w', 'quantity should be less than or equal to current stock');
                $('#edit_quantityRequested').val(0);
            }
        }

    }

    function fetch_rv_warehouse_item(itemAutoID, element, wareHouseAutoID) {
        if((itemAutoID)&&(wareHouseAutoID))
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'wareHouseAutoID': wareHouseAutoID, 'itemAutoID': itemAutoID},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        var conversionRate = $(element).closest('tr').find('.conversionRate').val();
                        if(conversionRate !== '') {
                            data['currentStock'] = data['currentStock'] * conversionRate;
                        }
                        if(data['mainCategory']=='Service'){
                            $(element).closest('tr').find('.currentstock').val('');
                        }else if(data['mainCategory']=='Non Inventory'){
                            $(element).closest('tr').find('.currentstock').val('');
                        }else{
                            $(element).closest('tr').find('.currentstock').val(data['currentStock']);
                        }
                    } else {

                        $(element).typeahead('val', '');
                        $(element).closest('tr').find('.currentstock').val('');
                        $(element).closest('tr').find('.itemAutoID').val('');
                        $(element).closest('tr').find('.f_search').val('');

                    }
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

    function editstockwarehousestock(det) {
        var itemAutoID = $('#edit_itemAutoID').val();
        var wareHouseAutoID = $('#edit_wareHouseAutoID').val();
        if (wareHouseAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'wareHouseAutoID': wareHouseAutoID, 'itemAutoID': itemAutoID},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    if (data['status']) {

                        if(data['mainCategory']=='Service'){
                            $('#currentstock_edit').val('');
                        }else if(data['mainCategory']=='Non Inventory'){
                            $('#currentstock_edit').val('');
                        }else{
                            var conversionRate = $('#conversionRateEdit').val();
                            if(parseFloat(conversionRate) > 0 && data['currentStock'] != null) {
                                data['currentStock'] = parseFloat(data['currentStock']) * parseFloat(conversionRate);
                            }
                            $('#currentstock_edit').val(data['currentStock']);
                        }

                    } else {
                        $('#currentstock_edit').val('');


                    }
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


    function edit_all_item_detail_modal() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('Invoices/fetch_customer_invoice_all_detail_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var descm = 2;
                var taxTotal = 0;
                var currentstock_all = '';
                $('#edit_item_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    
                } else {
                    $.each(data, function (key, value) {

                        if(value['mainCategory'] == "Service"){
                            currentstock_all=0;
                        }else{
                            currentstock_all= value['currentStock'];
                        }

                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" onchange="convertPrice(this)" required')) ?>';
                        var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID"  onchange="checkitemavailable(this)" required')) ?>';
                        var taxfield = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="taxfield_\'+key+\'"'), form_dropdown('item_text[]', all_tax_drop(1), '', 'class="form-control item_text input-mini"  onchange="select_text_item(this)"')) ?>';

                        var string = '<tr><td> <input type="text" class="form-control search f_search" name="search[]" id="f_search_' + x + '" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]"> <input type="hidden" class="form-control invoiceDetailsAutoID" name="invoiceDetailsAutoID[]" value="' + value['invoiceDetailsAutoID'] + '"> </td> <td> ' + wareHouseAutoID + ' </td> <td> <input class="hidden conversionRate" id="conversionRate" value="' + value['conversionRateUOM'] + '" name="conversionRate"> ' + UOM + ' </td> <td> <div class="input-group"> <input type="text" name="currentstock[]" value="' + currentstock_all + '" class="form-control currentstock" required disabled> </div> </td> <td><input type="text" onfocus="this.select();" name="quantityRequested[]" onkeyup="checkCurrentStockAll(this,\'' + value['mainCategory'] + '\')" value="' + value['requestedQty'] + '" placeholder="0.00" class="form-control number input-mini quantityRequested" required></td> <td> <div class="input-group"> <input type="text" onfocus="this.select();" name="estimatedAmount[]" value="' + value['unittransactionAmount'] + '" onkeyup="change_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" placeholder="0.00" class="form-control number estimatedAmount input-mini"> </div> </td> <td style="width: 100px;"> <div class="input-group"> <input type="text" name="discount[]" placeholder="0.00" onkeyup="cal_discount(this)" onfocus="this.select();" value="' + value['discountPercentage'] + '" class="form-control number discount"> <span class="input-group-addon">%</span></div> </td> <td style="width: 100px;"> <input type="text" name="discount_amount[]" placeholder="0.00" value="' + value['discountAmount'] + '" onkeyup="cal_discount_amount(this)" onfocus="this.select();" class="form-control number discount_amount"> </td> <td class="taxshowYN" > ' + taxfield + ' </td> <td class="taxshowYN" style=""> <div class="input-group"> <input type="text" name="item_taxPercentage[]" placeholder="0.00" onfocus="this.select();" class="form-control number item_taxPercentage input-mini" id="item_taxPercentage_all_' + x + '" value="' + value['taxPercentage'] + '" value="0" readonly> <span class="input-group-addon input-group-addon-mini">%</span> </div> </td> <td> <textarea class="form-control input-mini" rows="1" name="remarks[]" placeholder="Item Remarks...">' + value['remarks'] + '</textarea> </td> <td class="remove-td"><a onclick="delete_customer_invoiceDetailsEdit(' + value['invoiceDetailsAutoID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';


                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        $('#ware_' + key).val(value['wareHouseAutoID']).change();
                        $('#taxfield_' + key).val(value['taxMasterAutoID']);
                        if (data['taxPercentage'] != 0) {
                            $('#item_taxPercentage_all_' + key).prop('readonly', false);
                        } else {
                            $('#item_taxPercentage_all_' + key).prop('readonly', true);
                        }
                        taxTotal+= parseFloat(value['taxAmount']);
                        fetch_related_uom_id(value['defaultUOMID'], value['unitOfMeasureID'], $('#uom_' + key));
                        initializeitemTypeahead(x);
                        x++;
                    });
                    if(taxTotal > 0 ){
                        var taxexist= 1;
                    }else{
                        var taxexist= 0;
                    }
                    if((taxYN == 0 && !jQuery.isEmptyObject(taxYN))  && taxexist !=1)
                    {
                        $('.hideTaxpolicy').addClass('hide');
                        $('.taxshowYN').addClass('hide');
                    }else{
                        $('.hideTaxpolicy').removeClass('hide');
                        $('.taxshowYN').removeClass('hide');
                    }
                    $('.select2').select2();
                    search_id = x - 1;
                    $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                }
                stopLoad();
                

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }


    function add_more_edit_customer_invoice() {
        //$('.search').typeahead('destroy');
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#customer_invoice_detail_all_edit_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#customer_invoice_detail_all_edit_table').append(appendData);
        var lenght = $('#customer_invoice_detail_all_edit_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        $(".select2").select2();
        initializeitemTypeahead(search_id);
        number_validation();
    }

    function updateCustomerInvoice_edit_all_Item() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#edit_all_item_detail_form').serializeArray();
        if (invoiceAutoID) {
            data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
            //data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
//            data.push({'name': 'wareHouse', 'value': $('#wareHouseAutoID option:selected').text()});
//            data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
            $('#edit_all_item_detail_form select[name="wareHouseAutoID[]"] option:selected').each(function () {
                data.push({'name': 'wareHouse[]', 'value': $(this).text()})
            });

            $('#edit_all_item_detail_form select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
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
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Invoices/updateCustomerInvoice_edit_all_Item'); ?>",
                    beforeSend: function () {
                        startLoad();
                        // $('.umoDropdown').prop("disabled", true);
                    },
                    success: function (data) {
                        invoiceDetailsAutoID = null;
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_conformation();
                            setTimeout(function () {
                                tab_active(2);
                            }, 300);
                            $('#all_item_edit_detail_modal').modal('hide');
                            $('#edit_all_item_detail_form')[0].reset();
                            $('.select2').select2('');
                        }
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
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


    function delete_customer_invoiceDetailsEdit(id, value, det) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning", /*warning*/
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
                    data: {'invoiceDetailsAutoID': id},
                    url: "<?php echo site_url('Invoices/delete_item_direct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        load_conformation();
                        // fetch_details(tabID);
                        $(det).closest('tr').remove();
                        setTimeout(function () {
                            tab_active(2);
                        }, 300);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function invoice_detail_modal_insurance(tab) {
        tabID = tab;
        if (invoiceAutoID) {
            $('#invoice_detail_insurance_form')[0].reset();
            $('.gl_code_insurance').val('').change();
            $('.segment_glAdd_insurance').val(defaultSegment).change();
            $("#invoice_detail_modal_insurance").modal({backdrop: "static"});
            $('#income_add_table_insurance tbody tr').not(':first').remove();
        }
    }

    function add_more_income_insurance() {
        $('select.select2').select2('destroy');
        var appendData = $('#income_add_table_insurance tbody tr:first').clone();

        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.segment_glAdd ').val(defaultSegment);
        appendData.find('.marginPercentage ').val(<?php echo $margin ?>);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#income_add_table_insurance').append(appendData);
        $(".select2").select2();
        number_validation();
    }


    function calculate_total(ths,fild){
        var amount=$(ths).closest('tr').find('.amount').val();
        var marginPercentage=$(ths).closest('tr').find('.marginPercentage').val();
        var marginAmount=$(ths).closest('tr').find('.marginAmount').val();
        var totalAmount=$(ths).closest('tr').find('.totalAmount').val();

        if (jQuery.isEmptyObject(amount)) {
            amount=0
        }
        if (jQuery.isEmptyObject(marginPercentage)) {
            marginPercentage=0
        }
        if (jQuery.isEmptyObject(marginAmount)) {
            marginAmount=0
        }

        if(amount>0 && marginPercentage<=0 && marginAmount<=0){
            $(ths).closest('tr').find('.totalAmount').val(amount)
        }else if(amount>0 && marginPercentage>0 && marginAmount<=0){
            var margamnt=(amount*marginPercentage)/100;
            $(ths).closest('tr').find('.totalAmount').val(parseFloat(amount)+parseFloat(margamnt));
            if(fild=='marginAmount' && marginAmount==0) {
                $(ths).closest('tr').find('.marginPercentage').val(0);
                $(ths).closest('tr').find('.marginAmount').val(0);
            }else{
                $(ths).closest('tr').find('.marginAmount').val(margamnt);
            }
        }else if(amount>0 && marginPercentage==0 && marginAmount>0){
            var percentage = (parseFloat(marginAmount)/parseFloat(amount)) * 100;
            if(fild=='marginPercentage') {
                $(ths).closest('tr').find('.marginPercentage').val(0);
                $(ths).closest('tr').find('.marginAmount').val(0);
                $(ths).closest('tr').find('.totalAmount').val(parseFloat(amount));
            }else{
                $(ths).closest('tr').find('.marginPercentage').val(percentage);
                $(ths).closest('tr').find('.totalAmount').val(parseFloat(amount)+parseFloat(marginAmount));
            }
        }else if(amount>0 && marginPercentage>0 && marginAmount>0){
            if(fild=='marginAmount'){
                var percentage = (parseFloat(marginAmount)/parseFloat(amount)) * 100;
                $(ths).closest('tr').find('.totalAmount').val(parseFloat(amount)+parseFloat(marginAmount));
                $(ths).closest('tr').find('.marginPercentage').val(percentage);
            }else{
                var margamnt=(amount*marginPercentage)/100;
                $(ths).closest('tr').find('.totalAmount').val(parseFloat(amount)+parseFloat(margamnt));
                $(ths).closest('tr').find('.marginAmount').val(margamnt);
            }

        }else{
            myAlert('w','Enter Amount');
            $(ths).closest('tr').find('.amount').val(0);
            $(ths).closest('tr').find('.marginPercentage').val('');
            $(ths).closest('tr').find('.marginAmount').val(0);
            $(ths).closest('tr').find('.totalAmount').val(0);
        }
    }

    function saveinsuranceInvoiceDetails() {
        var data = $('#invoice_detail_insurance_form').serializeArray();
        data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
        data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/save_insurance_invoice_detail_margin'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                invoiceDetailsAutoID = null;
                //myAlert(data['type'], data['mesage']);
                refreshNotifications(true);
                stopLoad();

                if (data != false) {
                    setTimeout(function () {
                        tab_active(1);
                    }, 300);
                    $('#invoice_detail_modal_insurance').modal('hide');
                    $('#invoice_detail_insurance_form')[0].reset();
                    $('.select2').select2('');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function edit_insurance_detail(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_edit_this_document');?>", /*You want to edit this record!*/
                type: "warning", /*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $("#edit_gl_code").val(null).trigger("change");
                $('#edit_invoice_detail_form').trigger("reset");
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'invoiceDetailsAutoID': id},
                    url: "<?php echo site_url('Invoices/fetch_customer_invoice_detail_commission'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        invoiceDetailsAutoID = data['invoiceDetailsAutoID'];
                        load_segmentBase_projectID_incomeEdit(data['segmentID'], data['projectID']);
                        $('#edit_supplierAutoID').val(data['supplierAutoID']).change();
                        $('#edit_segment_gl').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#edit_amount').val(data['transactionAmount']);
                        $('#edit_marginPercentage').val(data['marginPercentage']);
                        $('#edit_marginAmount').val(data['marginAmount']);
                        $('#edit_totalAmount').val(data['transactionAmount']);
                        $('#edit_description').val(data['description']);
                        $("#edit_invoice_detail_modal_insurance").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function Update_Invoice_insurance() {
        var $form = $('#edit_invoice_insurance_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
        data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/update_income_invoice_detail_insurance'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    invoiceDetailsAutoID = null;
                    setTimeout(function () {
                        tab_active(1);
                    }, 300);
                    $('#edit_invoice_detail_modal_insurance').modal('hide');
                    $('#edit_invoice_insurance_detail_form')[0].reset();
                    $('.select2').select2('');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function delivery_order_modal(tab){
        tabID = tab;

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID':invoiceAutoID},
            url: "<?php echo site_url('Invoices/load_un_billed_delivery_orders'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#table_body_un_billed_orders').html(data['view']);
                    $('#delivery_order_modal').modal('show');

                    $(".invoicing_amount").numeric({negative: false});
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function applybtn(id, totalAmount) {
        $("#amount_" + id).val(totalAmount);
    }

    function validate_max_receivable(obj, max_amount, dPlace){
        var this_amount = $(obj).val();
        if($.isNumeric(this_amount)){
            this_amount = parseFloat( parseFloat(this_amount).toFixed(dPlace) );
            max_amount = parseFloat( parseFloat(max_amount).toFixed(dPlace) );

            if(this_amount > max_amount){
                $(obj).val('');
                myAlert('w', 'Invoicing amount can not be greater than the balance amount.')
            }

        }
    }

    function amount_round(obj, dPlace){
        var this_amount = $(obj).val();
        if($.isNumeric(this_amount)){
            $(obj).val( parseFloat(this_amount).toFixed(dPlace) );
        }
    }

    function delivery_order_invoice() {
        var amount = [];
        var deliveryOrders = [];

        $('.invoicing_amount').each(function () {
            if( $.trim($(this).val()) != ''){
                var del_auto_id =  $(this).attr('data-auto-id');
                amount.push( $('#amount_' + del_auto_id).val() );
                deliveryOrders.push( $('#delivery_order_' + del_auto_id).val() );
            }
        });

        if ($.isEmptyObject(deliveryOrders)) {
            alert('No data found to proceed.');
            return false;
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID, 'deliveryOrders': deliveryOrders, 'amounts': amount},
            url: "<?php echo site_url('Invoices/delivery_order_invoice'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();

                if(data[0] == 's'){
                    $('#delivery_order_modal').modal('hide');
                    setTimeout(function () {
                        setTimeout(function () {
                            tab_active(tabID);
                        }, 300);
                    }, 300);
                }

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function cal_discount_amount_general(discount_amount, total_amount) {
        if(invoiceType=="Insurance"){
            var total_amount_disc=$('#discount_tot_hn').val();
            var total_amount_margin= $('#discount_margin_tot_hn').val();
            var discount_footer_tot_hn= $('#discount_footer_tot_hn').val();
            if (total_amount_disc && discount_amount) {
                $('#discountPercentage').val(((parseFloat(discount_amount) / total_amount_disc) * 100));
                var d_percent=$('#discountPercentageTothn').val();
                var discper=((parseFloat(discount_amount) / total_amount_disc) * 100);
                var totdiscpercentg=parseFloat(d_percent)+parseFloat(discper);
                if(totdiscpercentg>100){
                    myAlert('w','Discount percentage total canot be greater than 100%');
                    $('#discountExtraChargeID').val('');
                    $('#discountPercentage').val('');
                    $('#discount_trans_amount').val('');
                }else if((parseFloat(discount_amount)+parseFloat(discount_footer_tot_hn))>parseFloat(total_amount_margin)){
                    myAlert('w','Discount Amonut canot be greater than margin amount');
                    $('#discountExtraChargeID').val('');
                    $('#discountPercentage').val('');
                    $('#discount_trans_amount').val('');
                }
            } else {
                $('#discountPercentage').val(0);
            }
        }else{
            var total_amount_disc=$('#discount_tot_hn').val();
            if (total_amount_disc && discount_amount) {
                $('#discountPercentage').val(((parseFloat(discount_amount) / total_amount_disc) * 100));
                var d_percent=$('#discountPercentageTothn').val();
                var discper=((parseFloat(discount_amount) / total_amount_disc) * 100);
                var totdiscpercentg=parseFloat(d_percent)+parseFloat(discper);
                if(totdiscpercentg>100){
                    myAlert('w','Discount percentage total canot be greater than 100%');
                    $('#discountExtraChargeID').val('');
                    $('#discountPercentage').val('');
                    $('#discount_trans_amount').val('');
                }
            } else {
                $('#discountPercentage').val(0);
            }
        }
    }

    function cal_discount_general(discount, total_amount) {
        if(invoiceType=="Insurance"){
            var total_amount_disc=$('#discount_tot_hn').val();
            var total_amount_margin= $('#discount_margin_tot_hn').val();
            var discount_footer_tot_hn= $('#discount_footer_tot_hn').val();
            if (total_amount_disc && discount) {
                $('#discount_trans_amount').val(parseFloat((total_amount_disc / 100) * parseFloat(discount)).toFixed(currency_decimal));
                var discamnt=(total_amount_disc / 100) * parseFloat(discount);
                var d_percent=$('#discountPercentageTothn').val();
                var discper=$('#discountPercentage').val();
                var totdiscpercentg=parseFloat(d_percent)+parseFloat(discper);
                if(totdiscpercentg>100){
                    myAlert('w','Discount percentage total canot be greater than 100%');
                    $('#discountExtraChargeID').val('');
                    $('#discountPercentage').val('');
                    $('#discount_trans_amount').val('');
                }else if((parseFloat(discamnt)+parseFloat(discount_footer_tot_hn))>parseFloat(total_amount_margin)){
                    myAlert('w','Discount Amonut canot be greater than margin amount');
                    $('#discountExtraChargeID').val('');
                    $('#discountPercentage').val('');
                    $('#discount_trans_amount').val('');
                }
            } else {
                $('#discount_trans_amount').val(0);
            }
        }else{
            var total_amount_disc=$('#discount_tot_hn').val();
            if (total_amount_disc && discount) {
                $('#discount_trans_amount').val(parseFloat((total_amount_disc / 100) * parseFloat(discount)).toFixed(currency_decimal));
                var d_percent=$('#discountPercentageTothn').val();
                var discper=$('#discountPercentage').val();
                var totdiscpercentg=parseFloat(d_percent)+parseFloat(discper);
                if(totdiscpercentg>100){
                    myAlert('w','Discount percentage total canot be greater than 100%');
                    $('#discountExtraChargeID').val('');
                    $('#discountPercentage').val('');
                    $('#discount_trans_amount').val('');
                }
            } else {
                $('#discount_trans_amount').val(0);
            }
        }
    }


    function delete_discount_gen(id, value) {
        if (invoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning", /*warning*/
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
                        data: {'discountDetailID': id},
                        url: "<?php echo site_url('Invoices/delete_discount_gen'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            setTimeout(function () {
                                fetch_invoice_direct_details();
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }


    function delete_extra_gen(id, value) {
        if (invoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                    type: "warning", /*warning*/
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
                        data: {'extraChargeDetailID': id},
                        url: "<?php echo site_url('Invoices/delete_extra_gen'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            setTimeout(function () {
                                fetch_invoice_direct_details();
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function calculateNetAmount(val,fld){
        var incamount=$(val).closest('tr').find('.amount').val();
        var incdiscountPercentage=$(val).closest('tr').find('.discountPercentage').val();
        var incdiscountAmount=$(val).closest('tr').find('.discountAmount').val();

        if(fld=='amount'){
            if (jQuery.isEmptyObject(incdiscountPercentage) || incdiscountPercentage==0) {
                $(val).closest('tr').find('.Netnumber').val(parseFloat(incamount).toFixed(currency_decimal));
            }else{
                var discamnt=(parseFloat(incamount)*parseFloat(incdiscountPercentage))/100;
                $(val).closest('tr').find('.discountAmount').val(parseFloat(discamnt).toFixed(currency_decimal));
                $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount)-parseFloat(discamnt)).toFixed(currency_decimal));
            }
        }else if(fld=='discountPercentage'){
            if (jQuery.isEmptyObject(incamount) || incamount==0) {
                myAlert('w','Enter Discount Amount');
                $(val).closest('tr').find('.discountPercentage').val(0);
                $(val).closest('tr').find('.discountAmount').val(0);
                $(val).closest('tr').find('.Netnumber').val(0);
            }else{
                var discamnt=(parseFloat(incamount)*parseFloat(incdiscountPercentage))/100;
                $(val).closest('tr').find('.discountAmount').val(parseFloat(discamnt).toFixed(currency_decimal));
                $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount)-parseFloat(discamnt)).toFixed(currency_decimal));
            }
        }else{
            if (jQuery.isEmptyObject(incamount) || incamount==0) {
                myAlert('w','Enter Discount Amount');
                $(val).closest('tr').find('.discountPercentage').val(0);
                $(val).closest('tr').find('.discountAmount').val(0);
                $(val).closest('tr').find('.Netnumber').val(0);
            }else{
                var discprc=(parseFloat(incdiscountAmount)*100)/parseFloat(incamount);

                $(val).closest('tr').find('.discountPercentage').val(parseFloat(discprc));
                $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount)-parseFloat(incdiscountAmount)).toFixed(currency_decimal));
            }
        }
    }

    function calculateNetAmount_edit(val,fld){
        var incamount=$('#edit_amount').val();
        var incdiscountPercentage=$('#discountPercentage_edit').val();
        var incdiscountAmount=$('#discountAmount_edit').val();

        if(fld=='amount'){
            if (jQuery.isEmptyObject(incdiscountPercentage) || incdiscountPercentage==0) {
                $('#Netamount_edit').val(incamount);
            }else{
                var discamnt=(parseFloat(incamount)*parseFloat(incdiscountPercentage))/100;
                $('#discountAmount_edit').val(parseFloat(discamnt).toFixed(currency_decimal));
                $('#Netamount_edit').val((parseFloat(incamount)-parseFloat(discamnt)).toFixed(currency_decimal));
            }
        }else if(fld=='discountPercentage'){
            if (jQuery.isEmptyObject(incamount) || incamount==0) {
                myAlert('w','Enter Discount Amount');
                $('#discountPercentage_edit').val(0);
                $('#discountAmount_edit').val(0);
                $('#Netamount_edit').val(0);
            }else{
                var discamnt=(parseFloat(incamount)*parseFloat(incdiscountPercentage))/100;
                $('#discountAmount_edit').val(parseFloat(discamnt).toFixed(currency_decimal));
                $('#Netamount_edit').val((parseFloat(incamount)-parseFloat(discamnt)).toFixed(currency_decimal));
            }
        }else{
            if (jQuery.isEmptyObject(incamount) || incamount==0) {
                myAlert('w','Enter Discount Amount');
                $('#discountPercentage_edit').val(0);
                $('#discountAmount_edit').val(0);
                $('#Netamount_edit').val(0);
            }else{
                var discprc=(parseFloat(incdiscountAmount)*100)/parseFloat(incamount);

                $('#discountPercentage_edit').val(parseFloat(discprc));
                $('#Netamount_edit').val((parseFloat(incamount)-parseFloat(incdiscountAmount)).toFixed(currency_decimal));
            }
        }
    }

    function invoice_detail_modal_operation() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('Invoices/invoice_detail_modal_operation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#operation_add_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#operation_add_table_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                } else {
                    $.each(data, function (key, value) {
                        $('#operation_add_table_body').append('<tr><td>' + value['ticketNo'] + '</td><td>' + value['createdDateTime'] + '</td><td>' + value['contractRefNo'] + '</td><td>' + value['productValue'] + '</td><td>' + value['serviceValue'] + '</td><td><input type="radio" class="ticketidAtuto" id="ticketidAtuto" name="ticketidAtuto" value="' + value['ticketidAtuto'] + '"></td></tr>');
                    });
                }
                $("#invoice_detail_modal_operation").modal({backdrop: "static"});
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function saveopDetails() {

        var data = $("#invoice_detail_operation_form").serializeArray();
        data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/saveopDetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                if(data[0]=='s'){
                    setTimeout(function () {
                        fetch_invoice_direct_details();
                    }, 300);
                    $("#invoice_detail_modal_operation").modal('hide');
                }

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function saveRetentionAmnt(invoiceAutoID,gl_trans_amount) {
        var retentionPercentage=$('#retentionPercentage_'+invoiceAutoID).val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID,'retentionPercentage': retentionPercentage,'trans_amount': gl_trans_amount},
            url: "<?php echo site_url('Invoices/saveRetentionAmnt'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                setTimeout(function () {
                    fetch_invoice_direct_details();
                }, 300);

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }


    function delete_item_direct_op(id,tab) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
                type: "warning", /*warning*/
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
                    data: {'invoiceDetailsAutoID': id},
                    url: "<?php echo site_url('Invoices/delete_item_direct_op'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        setTimeout(function () {
                            fetch_invoice_direct_details();
                        }, 300);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function edit_validateQtyContract(qty) {
        var detailType = $('#contractItem').val();
        if(detailType == 1) {
            var contractbalance = $('#contractBalance').val();
            var policy = <?php echo $qty_validate; ?>;
            if(policy == 1) {
                if(parseFloat(contractbalance) < parseFloat(qty)) {
                    $('#edit_quantityRequested').val('');
                    myAlert('e', 'Qty cannot be greater than Contracted Qty!');
                }
            }
        }
    }

    function load_project_segmentBase_category(element,projectID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_category"); ?>',
            dataType: 'json',
            data: {projectID: projectID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(element).parent().closest('tr').find('.project_subCategoryID').empty();
                var subCat = $(element).parent().closest('tr').find('.project_subCategoryID');
                subCat.append($('<option></option>').val('').html('Select Project Subcategory'));
                $(element).parent().closest('tr').find('.project_categoryID').empty();
                var mySelect =   $(element).parent().closest('tr').find('.project_categoryID');
                mySelect.append($('<option></option>').val('').html('Select Project Category'));

                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['categoryID']).html(text['categoryCode']+' - '+text['categoryDescription']));
                    });
                    if (projectcategory) {
                        $("#project_categoryID_edit").val(projectcategory).change();
                        $("#project_categoryID_edit1").val(projectcategory).change();
                    }
                }
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
    function fetch_project_sub_category(element,categoryID) {
        var projectID = $(element).closest('tr').find('.projectID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/fetch_project_sub_category"); ?>',
            dataType: 'json',
            data: {categoryID: categoryID,projectID:projectID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(element).parent().closest('tr').find('.project_subCategoryID').empty();
                var mySelect =  $(element).parent().closest('tr').find('.project_subCategoryID');
                mySelect.append($('<option></option>').val('').html('Select Project Subcategory'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['subCategoryID']).html(text['description']));
                    });
                    if (projectsubcat) {
                        $("#project_subCategoryID_edit").val(projectsubcat).change();
                        $("#project_subCategoryID_edit1").val(projectsubcat).change();

                    };
                }
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function delete_retention_amout(invoiceAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('Invoices/delete_retention_amout'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
            }, error: function () {
                stopLoad();
            }
        });
    }

    function convertPrice(element) {
        var itemAutoID = $(element).closest('tr').find('.itemAutoID').val();
        var wareHouseAutoID = $(element).closest('tr').find('.wareHouseAutoID option:selected').val();
        var estimatedAmount = $(element).closest('tr').find('.estimatedAmount').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID,
                'uomID' : element.value,
                'wareHouseAutoID' : wareHouseAutoID,
                'estimatedAmount' : estimatedAmount,
                'tableName': 'srp_erp_customerinvoicemaster',
                'primaryKey': 'invoiceAutoID',
                'id': invoiceAutoID,
                'customerAutoID': '<?php echo $master['customerID']; ?>'},
            url: '<?php echo site_url("Invoices/fetch_converted_price_qty_invoice"); ?>',
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.currentstock').val(data['qty']);
                    $(element).closest('tr').find('.estimatedAmount').val(data['price']);
                    $(element).closest('tr').find('.conversionRate').val(data['conversionRate']);
                } else {
                    // $('#search').empty();
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function convertPrice_edit(element) {
        var itemAutoID = $(element).closest('tr').find('#edit_itemAutoID').val();
        var wareHouseAutoID = $(element).closest('tr').find('#edit_wareHouseAutoID option:selected').val();
        var estimatedAmount = $(element).closest('tr').find('#edit_estimatedAmount').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID,
                'uomID' : element.value,
                'wareHouseAutoID' : wareHouseAutoID,
                'estimatedAmount' : estimatedAmount,
                'tableName': 'srp_erp_customerinvoicemaster',
                'primaryKey': 'invoiceAutoID',
                'id': invoiceAutoID,
                'customerAutoID': '<?php echo $master['customerID']; ?>'},
            url: '<?php echo site_url("Invoices/fetch_converted_price_qty_invoice"); ?>',
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('#currentstock_edit').val(data['qty']);
                    $(element).closest('tr').find('#edit_estimatedAmount').val(data['price']);
                    $(element).closest('tr').find('#conversionRateEdit').val(data['conversionRate']);
                } else {
                    // $('#search').empty();
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
    function open_uploadModal(){
        $('#upload-msg-div').addClass('hide');
        $('#excelUpload_file').val('');
        $('#upload-msg-div').val('');

        $('#excelUpload_Modal').modal('show');
        var desDate = $('#desDate').val();
        $('#docDate').val(desDate);
    }

    function excel_upload(){
        var formData = new FormData($("#commissionUpload_form")[0]);
        
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Invoices/commission_excelUpload'); ?>",
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
                    $('#upload-msg-div').removeClass('hide');
                    $('#excelUploadMsg_Modal').modal('show');
                    
                    $('#upload-msg-div').html(data[1]);

                }

                if (data[0] == 's') {
                    $('#upload-msg-div').addClass('hide');
                    $('#excelUpload_Modal').modal('hide');

                    setTimeout(function(){
                        fetch_invoice_direct_details();
                        //loadDetail_table();
                    }, 300);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function showExcelFunctions(){
        $('.excelFunctions').removeClass('hide');
    }
    function hideExcelFunctions(){
        $('.excelFunctions').addClass('hide');
    }

    function loadDesignation(element,employee) {
        if(employee) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'invoiceDetailsAutoID':invoiceDetailsAutoID,'employeeID': employee},
                url: "<?php echo site_url('CommissionScheme/fetchSalespersonRelatedDesignation'); ?>",
                success: function (data) {
                    $(element).parent().closest('tr').find('.designationID').empty();
                    var mySelect = $(element).parent().closest('tr').find('.designationID');
                    mySelect.append($('<option></option>').val('').html('Select Designation'));

                    var select_value='';
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['DesignationID']).html(text['DesDescription']));
                            if(text['isMajor']==2){
                                select_value = text['DesignationID'];
                            }else if(text['isMajor']==1){
                                select_value = text['DesignationID'];
                            }
                        });
                    }
                    if (invoiceDetailsAutoID) {
                        $("#edit_designationID").val(select_value).change();
                    }else{
                        $(element).parent().closest('tr').find('.designationID').val(select_value);
                    }
                    //$("#designationID").val(select_value);
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    }
</script>