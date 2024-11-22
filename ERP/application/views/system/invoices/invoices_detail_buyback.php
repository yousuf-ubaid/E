<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    textarea.form-control {
        padding: 5px !important;
    }
</style>
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$buybackinvoice = array('' => 'Select Sales Order');
$umo_arr = array('' => 'UOM');
$projectExist = project_is_exist();
$transaction_total = 0;
$transaction_discount_total = 0;
$transaction_extra_total = 0;
$tax_transaction_total = 0;
for ($x = 0; $x < count($detail['detail']); $x++) {
    $transaction_total += ($detail['detail'][$x]['transactionAmount']);
    $tax_transaction_total += ($detail['detail'][$x]['transactionAmount'] - $detail['detail'][$x]['totalAfterTax']);
}
for ($x = 0; $x < count($detail['discount']); $x++) {
    $transaction_discount_total += ($tax_transaction_total/100)*$detail['discount'][$x]['discountPercentage'] ;
}
for ($x = 0; $x < count($detail['extraCharge']); $x++) {
    if($detail['extraCharge'][$x]['isTaxApplicable']==1){
        $transaction_extra_total += $detail['extraCharge'][$x]['transactionAmount'] ;
    }
}
$tax_transaction_total=($tax_transaction_total-$transaction_discount_total)+$transaction_extra_total;
$placeholder = '0.00';
$currencyID = $master['transactionCurrency'];
$currency_decimal = $master['transactionCurrencyDecimalPlaces'];
if($currencyID == 'OMR')
{
    $placeholder = '0.000';
}

switch ($invoiceType) {
    case "Direct": ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="active"><a data-toggle="tab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> </a>
                </li><!--Income-->
                <li class=""><a data-toggle="tab" href="#tab_2"
                                aria-expanded="false"><?php echo $this->lang->line('common_item'); ?> </a></li>
                <!--Item-->
                <li class="pull-left header"><i
                        class="fa fa-hand-o-right"></i><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_direct_invoice_for'); ?>
                    :
                    - <?php echo $master['customerName']; ?> - <?php echo $master['warehouseAutoID']; ?></li> <!--Direct Invoice for-->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_details'); ?></th>
                            <!--GL Details-->
                            <th><?php echo $this->lang->line('common_amount'); ?></th><!--Amount-->
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
                            <!-- <th>Local <span

                                    class="locurrency"><!--(--><?php /*//echo $master['companyLocalCurrency']; */ ?></span></th>
                            <!-- <th style="min-width: 12%"><?php /*echo $this->lang->line('common_customer');*/ ?><!--Customer-->
                            <span
                                class="sucurrency"><!--(-->
                                <?php /*//echo $master['customerCurrency']; */ ?><!--)--></span></th>
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
                            <th colspan="9"><?php echo $this->lang->line('sales_markating_transaction_document_item_details'); ?> </th>
                            <!--Item Details-->
                            <?php
                            $policy = getPolicyValues('ATT', 'All');
                            if($policy==0 || $policy==null) {
                                ?>
                                <th colspan="6"><?php echo $this->lang->line('common_price'); ?> <span class="currency">(
                                        <?php echo $master['transactionCurrency']; ?><!--Price-->
                                    )</span></th>
                                <?php
                            }else{
                                ?>
                                <th colspan="6"><?php echo $this->lang->line('common_price'); ?> <span class="currency">(
                                        <?php echo $master['transactionCurrency']; ?><!--Price-->
                                    )</span></th>
                                <?php
                            }
                            ?>
                            <th>
                                <button type="button" onclick="invoice_item_detail_modal(2)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add Item"><i class="fa fa-plus"></i></button>
                                <!--<button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;" id="editallbtn" class="btn  btn-xs btn-default hidden pull-right"><span class="glyphicon glyphicon-pencil"></span> Edit All</button>-->
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_code'); ?> </th><!--Code-->
                            <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                            <th> No of Birds</th>
                            <th> Gross Weight</th>
                            <th> No of Buckets</th>
                            <th> Bucket Size</th>
                            <th> Bucket Weight</th>
                            <th>Net Weight</th>
                            <!--Qty-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_unit'); ?> </th>
                            <!--Unit-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th>
                            <!--Discount-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_unit_price'); ?> </th>
                            <!--Net Unit Cost-->
                            <th><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <?php
                            $policy = getPolicyValues('ATT', 'All');
                            if($policy==0 || $policy==null) {
                                ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                <?php
                            }
                            ?>
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
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2"
                               id="discount_tot">Discount Applicable Amount
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            )</label>
                        <form class="form-inline" id="discount_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeID', all_discount_drop(1), '', 'class="form-control" id="discountExtraChargeID" required style="width: 150px;"'); ?>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" id="discount_trans_amount" name="discount_amount"
                                       style="width: 100px;"
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
                            $taxtotal = 'Discount Total';
                            if (!empty($detail['discount'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="4">' . $taxtotal . ' ( ' . $master['transactionCurrency'] . ' )</td>';
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
                        <!--<label for="exampleInputName2"
                               id="extraCharge_tot">Extra Charges Applicable Amount
                            ( <?php /*echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); */?>
                            )</label>-->
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
                                <input type="text" class="form-control number" id="extra_trans_amount" name="extra_amount" style="width: 100px;">
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
                            $taxtotal = 'Extra Charge Total';
                            if (!empty($detail['extraCharge'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="3">' . $taxtotal . ' ( ' . $master['transactionCurrency'] . ' )</td>';
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
                <?php
                $policy = getPolicyValues('ATT', 'All');
                if($policy==1 || $policy==null) {
                    ?>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="exampleInputName2"
                                   id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($tax_transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <!--Tax for-->
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $tax_transaction_total . ')" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                               style="width: 80px;"
                                               onkeyup="cal_tax(this.value,<?php echo $tax_transaction_total; ?>)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                           style="width: 100px;"
                                           onkeyup="cal_tax_amount(this.value,<?php echo $tax_transaction_total; ?>)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        </div>
                        <div class="col-md-7">
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
                        </div>
                    </div>
                    <?php
                }
                ?>
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
                <li class="active"><a data-toggle="tab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> </a>
                </li><!--Income-->
                <li class=""><a data-toggle="tab" href="#tab_2"
                                aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_quotation'); ?> </a>
                </li><!--Quotation-->
                <li class="pull-left header"><i
                        class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_markating_transaction_quotation_base_invoice_for'); ?>
                    :
                    - <?php echo $master['customerName']; ?></li><!--Quotation base Invoice for-->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_details'); ?> </th>
                            <!--GL Details-->
                            <th><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
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
                            <!-- <th>Local <span
                                    class="locurrency">(<?php //echo $master['companyLocalCurrency'];
                            ?>)</span></th>
                            <th style="min-width: 12%">Customer <span
                                    class="sucurrency">(<?php //echo $master['customerCurrency'];
                            ?>)</span></th> -->
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
                <div id="tab_2" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="5"><?php echo $this->lang->line('sales_markating_transaction_quotation_details'); ?>  </th>
                            <!--Quotation Details-->
                            <th colspan="4"><?php echo $this->lang->line('common_price'); ?> <span
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
                            <!--Unit-->
                            <th><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
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
                               id="discount_tot">Discount Applicable Amount
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            )</label>
                        <form class="form-inline" id="discount_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeID', all_discount_drop(1), '', 'class="form-control" id="discountExtraChargeID" required style="width: 150px;"'); ?>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" id="discount_trans_amount" name="discount_amount"
                                       style="width: 100px;"
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
                            $taxtotal = 'Discount Total';
                            if (!empty($detail['discount'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="4">' . $taxtotal . ' ( ' . $master['transactionCurrency'] . ' )</td>';
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
                        <!--<label for="exampleInputName2"
                               id="extraCharge_tot">Extra Charges Applicable Amount
                            ( <?php /*echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); */?>
                            )</label>-->
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
                                <input type="text" class="form-control number" id="extra_trans_amount" name="extra_amount" style="width: 100px;">
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
                            $taxtotal = 'Extra Charge Total';
                            if (!empty($detail['extraCharge'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="3">' . $taxtotal . ' ( ' . $master['transactionCurrency'] . ' )</td>';
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
                <?php
                $policy = getPolicyValues('ATT', 'All');
                if($policy==1 || $policy==null) {
                    ?>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="exampleInputName2"
                                   id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?> </label>
                            <!--Tax for-->
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $tax_transaction_total . ')" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                               style="width: 80px;"
                                               onkeyup="cal_tax(this.value,<?php echo $tax_transaction_total; ?>)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                           style="width: 100px;"
                                           onkeyup="cal_tax_amount(this.value,<?php echo $tax_transaction_total; ?>)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        </div>
                        <div class="col-md-7">
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
                        </div>
                    </div>
                    <?php
                }
                ?>
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
                <li class="active"><a data-toggle="tab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> </a>
                </li><!--Income-->
                <li class=""><a data-toggle="tab" href="#tab_2"
                                aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_contract'); ?> </a>
                </li><!--Contract-->
                <li class="pull-left header"><i
                        class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_markating_transaction_contract_base_Invoice_for'); ?>
                    :
                    - <?php echo $master['customerName']; ?></li><!--Contract base Invoice for-->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_details'); ?> </th>
                            <!--GL Details-->
                            <th><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
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
                            <!-- <th>Local <span
                                    class="locurrency">(<?php //echo $master['companyLocalCurrency'];
                            ?>)</span></th>
                            <th style="min-width: 12%">Customer <span
                                    class="sucurrency">(<?php //echo $master['customerCurrency'];
                            ?>)</span></th> -->
                            <th style="width: 75px !important;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <td colspan="6" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
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
                            <th colspan="5">
                                <b><?php echo $this->lang->line('sales_markating_transaction_contract_details'); ?></th>
                            <!--Contract Details-->
                            <th colspan="4"><?php echo $this->lang->line('common_price'); ?> <span class="currency">(
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
                            <!--Unit-->
                            <th><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
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
                               id="discount_tot">Discount Applicable Amount
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            )</label>
                        <form class="form-inline" id="discount_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeID', all_discount_drop(1), '', 'class="form-control" id="discountExtraChargeID" required style="width: 150px;"'); ?>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" id="discount_trans_amount" name="discount_amount"
                                       style="width: 100px;"
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
                            $taxtotal = 'Discount Total';
                            if (!empty($detail['discount'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="4">' . $taxtotal . ' ( ' . $master['transactionCurrency'] . ' )</td>';
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
                        <!--<label for="exampleInputName2"
                               id="extraCharge_tot">Extra Charges Applicable Amount
                            ( <?php /*echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); */?>
                            )</label>-->
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
                                <input type="text" class="form-control number" id="extra_trans_amount" name="extra_amount" style="width: 100px;">
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
                            $taxtotal = 'Extra Charge Total';
                            if (!empty($detail['extraCharge'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="3">' . $taxtotal . ' ( ' . $master['transactionCurrency'] . ' )</td>';
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
                <?php
                $policy = getPolicyValues('ATT', 'All');
                if($policy==1 || $policy==null) {
                    ?>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="exampleInputName2"
                                   id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($tax_transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <!--Tax for-->
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $tax_transaction_total . ')" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                               style="width: 80px;"
                                               onkeyup="cal_tax(this.value,<?php echo $tax_transaction_total; ?>)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                           style="width: 100px;"
                                           onkeyup="cal_tax_amount(this.value,<?php echo $tax_transaction_total; ?>)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        </div>
                        <div class="col-md-7">
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
                        </div>
                    </div>
                    <?php
                }
                ?>
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
                <li class="active"><a data-toggle="tab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> </a>
                </li><!--Income-->
                <li class=""><a data-toggle="tab" href="#tab_2"
                                aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_sales_order'); ?> </a>
                </li><!--Sales Order-->
                <li class="pull-left header"><i
                        class="fa fa-hand-o-right"></i><?php echo $this->lang->line('sales_markating_transaction_sales_order_base_invoice_for'); ?>
                    :
                    - <?php echo $master['customerName']; ?></li><!--Sales Order base Invoice for-->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_details'); ?> </th>
                            <!--GL Details-->
                            <th><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
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
                            <th><?php echo $this->lang->line('common_segment'); ?> </th><!--Segment-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_transaction'); ?> <span
                                    class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!-- <th>Local <span
                                    class="locurrency">(<?php //echo $master['companyLocalCurrency'];
                            ?>)</span></th>
                            <th style="min-width: 12%">Customer <span
                                    class="sucurrency">(<?php //echo $master['customerCurrency'];
                            ?>)</span></th> -->
                            <!--Transaction-->
                            <th style="width: 75px !important;">&nbsp;</th>

                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <td colspan="6" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                            <!--No Records Found-->
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <div id="tab_2" class="tab-pane">






                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr>
                                <th colspan='5'><?php echo $this->lang->line('common_item'); ?></th><!--Item-->
                                <th colspan='2'><?php echo $this->lang->line('common_item'); ?> <span
                                        class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span></th>
                                <!--Item-->
                                <th colspan='6'><?php echo $this->lang->line('sales_markating_transaction_invoice_item'); ?>
                                    <span
                                        class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span></th>
                                <!--Invoiced Item -->
                                <th>
                                    <button type="button" onclick="invoice_con_modal('Sales Order Base',2)"
                                            class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                            data-placement="left" title="Add Sale Order"><i class="fa fa-plus"></i>
                                    </button>
                                </th>
                            <tr>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_code'); ?></th><!--Code-->
                                <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                                <!--Description-->
                                <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?> </th>
                                <!--UOM-->
                                <!-- <th><?php echo $this->lang->line('common_warehouse'); ?> </th>Ware House-->
                                <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?> </th>
                                <!--Qty-->
                                <th>Unit <?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
                                <th>No Item</th>
                                <th>Gross Qty</th>
                                <th>Deduction</th>

                                <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?></th>
                                <!--Qty-->
                                <th>Total</th>
                               <!-- <th>Tax </th>--><!--Price-->
                                <?php
                                $policy = getPolicyValues('ATT', 'All');
                                if($policy==0 || $policy==null) {
                                    ?>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <?php
                                }
                                ?>
                                 <th>Net Amount</th>
                                <th>Action</th>
                                <!--<th>&nbsp;</th>-->
                                <th style="display: none;">&nbsp;</th>
                                <th style="display: none;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="item_table_body">
                            <!-- <tr class="danger">
                                <td colspan="16" class="text-center">
                                    <b><?php echo $this->lang->line('common_no_records_found'); ?>  </b></td>
                                No Records Found
                            </tr>-->
                            </tbody>
                            <tfoot id="item_table_tfoot">

                            </tfoot>
                        </table>
                    </div>







                </div><!-- /.tab-pane -->
                <br><br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2"
                               id="discount_tot">Discount Applicable Amount
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            )</label>
                        <form class="form-inline" id="discount_form">
                            <div class="form-group">
                                <?php echo form_dropdown('discountExtraChargeID', all_discount_drop(1), '', 'class="form-control" id="discountExtraChargeID" required style="width: 150px;"'); ?>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control number" id="discountPercentage" name="discountPercentage"
                                           style="width: 80px;"
                                           onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" id="discount_trans_amount" name="discount_amount"
                                       style="width: 100px;"
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
                            $taxtotal = 'Discount Total';
                            if (!empty($detail['discount'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="4">' . $taxtotal . ' ( ' . $master['transactionCurrency'] . ' )</td>';
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
                        <label for="exampleInputName2"
                               id="extraCharge_tot">Extra Charges Details
                            <!-- Applicable Amount
                            ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                            ) --></label>
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
                                <input type="text" class="form-control number" id="extra_trans_amount" name="extra_amount" style="width: 100px;">
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
                            $taxtotal = 'Extra Charge Total';
                            if (!empty($detail['extraCharge'])) {
                                echo '<tr>';
                                echo '<td class="text-right" colspan="3">' . $taxtotal . ' ( ' . $master['transactionCurrency'] . ' )</td>';
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
                <?php
                $policy = getPolicyValues('ATT', 'All');
                if($policy==1 || $policy==null) {
                    ?>
                    <div class="row">
                        <div class="col-md-5">
                            <!--Tax for-->
                            <label for="exampleInputName2"
                                   id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($tax_transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $tax_transaction_total . ')" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                               style="width: 80px;"
                                               onkeyup="cal_tax(this.value,<?php echo $tax_transaction_total; ?>)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                           style="width: 100px;"
                                           onkeyup="cal_tax_amount(this.value,<?php echo $tax_transaction_total; ?>)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <table class="<?php echo table_class(); ?>"><?php echo $this->lang->line('sales_markating_tax_details'); ?>
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
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <!-- <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick="">Previous</button>
        </div> -->
        <?php
        break;
    default:
        echo "Conntect system Admin";
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
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title invoice_con_title">&nbsp;</h4>
            </div>


            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <?php echo form_dropdown('salesorderbase',$buybackinvoice, '', 'class="form-control select2 salesorderbase" id="salesorderbase" onchange="fetch_con_detail_table(this.value)"'); ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr>
                                <th colspan='5'><?php echo $this->lang->line('common_item'); ?></th><!--Item-->
                                <th colspan='2'><?php echo $this->lang->line('common_item'); ?> <span
                                        class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span></th>
                                <!--Item-->
                                <th colspan='9'><?php echo $this->lang->line('sales_markating_transaction_invoice_item'); ?>
                                    <span
                                        class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span></th>
                                <!--Invoiced Item -->
                            <tr>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_code'); ?></th><!--Code-->
                                <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                                <!--Description-->
                                <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?> </th>
                                <!--UOM-->
                                <!-- <th><?php echo $this->lang->line('common_warehouse'); ?> </th>Ware House-->
                                <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?> </th>
                                <!--Qty-->
                                <th><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
                                <th>No Item</th><!--Amount-->
                                <th>Gross Qty</th>
                                <th>Buckets</th>
                                <th>B weigh</th>
                                <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?></th>


                                <!--Qty-->
                                <th><?php echo $this->lang->line('common_price'); ?> </th><!--Price-->
                                <?php
                                $policy = getPolicyValues('ATT', 'All');
                                if($policy==0 || $policy==null) {
                                    ?>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <?php
                                }
                                ?>
                                <th><?php echo $this->lang->line('common_total'); ?>  </th><!--Total-->
                                <th>&nbsp;</th>
                                <th style="display: none;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="table_body">
                            <!-- <tr class="danger">
                                <td colspan="16" class="text-center">
                                    <b><?php echo $this->lang->line('common_no_records_found'); ?>  </b></td>
                                No Records Found
                            </tr>-->
                            </tbody>
                            <tfoot id="table_tfoot">

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveChanges"
                        onclick="save_con_base_item_buyback()" ><?php echo $this->lang->line('common_save_change'); ?> </button>
                <!--Save changes-->
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="invoice_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
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
                            <!--Segment-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th><?php echo $this->lang->line('common_amount'); ?> <span
                                    class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span> <?php required_mark(); ?></th><!--Amount-->
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
                                <td>
                                    <div class="div_projectID_income">
                                        <select name="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?></option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                            <?php } ?>
                            <td><input type="text" name="amount[]" onkeypress="return validateFloatKeyPress(this,event)"
                                       value="00" class="form-control m_number"></td>
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
                            onclick="saveDirectInvoiceDetails_buyback()"><?php echo $this->lang->line('common_save_change'); ?>
                    </button><!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="edit_invoice_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
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
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th><?php echo $this->lang->line('common_amount'); ?> <span
                                    class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span> <?php required_mark(); ?></th><!--Amount-->
                            <th><?php echo $this->lang->line('common_description'); ?> </th><!--Description-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo form_dropdown('gl_code', $gl_code_arr, '', 'id="edit_gl_code" class="form-control select2" required '); ?></td>
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
                                       class="form-control m_number"></td>
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
                <h5 class="modal-title"><b><?php echo $this->lang->line('common_add'); ?> <?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?></b>&nbsp;&nbsp;&nbsp;(<?php echo $master['customerSystemCode'] . ' | ' . $master['customerName']; ?>)</h5>
                <!--Add Item Detail-->
            </div>
            <form role="form" id="invoice_item_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="item_add_table">
                        <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>UOM</th>
                            <th>Warehouse</th>
                            <th>No of Birds</th>
                            <th>Gross Weight</th>
                            <th>No of Buckets</th>
                            <th style="width: 7%;">Bucket Size</th>
                            <th>Net Weight</th>
                            <th>Amount</th>
                            <th colspan="2">Discount</th>
                            <?php
                            $policy = getPolicyValues('ATT', 'All');
                            if($policy==0 || $policy==null) {
                                ?>
                                <th >Tax</th>
                                <?php
                            }
                            ?>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more('item_add_table')"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="width: 20%">
                                <input type="text" class="form-control search input-mini f_search" name="search[]"
                                       id="f_search_1"
                                       placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_id'); ?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_description'); ?> ..."
                                       onkeydown="remove_item_all_description(event,this)"><!--Item Id-->
                                <!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td style="width: 6%"><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini" disabled  required'); ?></td>
                            <td style="width: 8%">
                                <?php /*echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID" onchange="checkitemavailable(this)" required'); */?>
                                <?php /*echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop_invoices(), '', 'class="form-control select2 input-mini wareHouseAutoID" required');*/ ?>
                                <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID" required'); ?>
                            </td>
                            <td><input type="text" onfocus="this.select();" name="noOfItems[]"
                                       onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                       class="form-control number input-mini noOfItems" required>
                            </td>
                            <td><input type="text" onfocus="this.select();" name="grossQty[]"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQty(this)" placeholder="0.00"
                                       class="form-control number input-mini grossQty" required>
                            </td>
                            <td><input type="text" onfocus="this.select();" name="noOfUnits[]"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQty(this)" placeholder="0.00"
                                       class="form-control number input-mini noOfUnits" required>
                            </td>
                            <td style="width: 7% !important;"><!--<input type="text" onfocus="this.select();" name="deduction[]"
                                       onkeyup="cal_deduction_netQty(this)" placeholder="0.00"
                                       class="form-control number input-mini deduction" required>-->

                                <?php echo form_dropdown('deduction[]',all_bucketweight_drop(),' ', 'class="form-control deduction input-mini" onchange="cal_deduction_netQty(this)"  required'); ?>

                            </td>
                            <td><input type="text" onfocus="this.select();" name="quantityRequested[]"
                                       onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                       class="form-control number input-mini quantityRequested" readonly>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" onfocus="this.select();" name="estimatedAmount[]"
                                           onkeyup="change_amount(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)" placeholder="<?php echo $placeholder ?>"
                                           class="form-control number estimatedAmount input-mini">
                                </div>
                            </td>
                            <td style="width: 100px;">
                                <div class="input-group">
                                    <input type="text" name="discount[]" placeholder="0.00" value="0"
                                           onkeyup="cal_discount(this)" onfocus="this.select();"
                                           class="form-control number discount">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 60px;">
                                <input type="text" name="discount_amount[]" placeholder="0.00" value="0"
                                       onkeyup="cal_discount_amount(this)" onfocus="this.select();"
                                       class="form-control number discount_amount">
                            </td>
                            <?php
                            $policy = getPolicyValues('ATT', 'All');
                            if($policy==0 || $policy==null) {
                                ?>
                                <td style="width: 5%">
                                    <select name="item_text[]" class="form-control item_text input-mini" id="item_text">

                                    </select>
                                </td>
                                <!--<td style="width: 120px">
                                    <div class="input-group">
                                        <input type="text" name="item_taxPercentage[]" id=""
                                               placeholder="0.00" onfocus="this.select();"
                                               class="form-control number item_taxPercentage input-mini" value="0"
                                               readonly>
                                        <span class="input-group-addon input-group-addon-mini">%</span>
                                    </div>
                                </td>-->
                                <?php
                            }
                            ?>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="saveInvoiceItemDetail_buyback()"><?php echo $this->lang->line('common_save_change'); ?>
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
                            <th>Item Code</th>
                            <th>UOM</th>
                            <th>Warehouse</th>
                            <th>No of Birds</th>
                            <th>Gross Weight</th>
                            <th>No of Buckets</th>
                            <th style="width: 10%;">Bucket Size</th>
                            <th>Net Weight</th>
                            <th style="width: 120px;"><?php echo $this->lang->line('common_amount'); ?> <?php required_mark(); ?>
                                <span
                                    class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!--Amount-->
                            <th colspan="2"
                                class="directdiscount"><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th>
                            <!--Discount-->
                            <?php
                            $policy = getPolicyValues('ATT', 'All');
                            if($policy==0 || $policy==null) {
                                ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th>
                                <?php
                            }
                            ?>
                            <!--Tax-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control input-mini" name="search"
                                       placeholder="Item ID, Item Description..." id="search"
                                       onkeydown="remove_item_all_description_edit(event)">
                                <input type="hidden" class="form-control" id="edit_itemAutoID" name="itemAutoID">
                            </td>
                            <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini" disabled id="edit_UnitOfMeasureID"  required'); ?></td>
                            <td>
                                <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2 input-mini" id="edit_wareHouseAutoID" onchange="editstockwarehousestock(this)"  required'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                            <?php } ?>
                            <td><input type="text" onfocus="this.select();" name="noOfItems"
                                       onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                       class="form-control number input-mini" id="edit_noOfItems" >
                            </td>
                            <td><input type="text" onfocus="this.select();" name="grossQty"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQtyEdit()" placeholder="0.00"
                                       class="form-control number input-mini" id="edit_grossQty">
                            </td>
                            <td><input type="text" onfocus="this.select();" name="noOfUnits"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQtyEdit()" placeholder="0.00"
                                       class="form-control number input-mini" id="edit_noOfUnits">
                            </td>
                            <td><!--<input type="text" onfocus="this.select();" name="deduction"
                                       onkeyup="cal_deduction_netQty(this),cal_deduction_netQtyEdit()" placeholder="0.00"
                                       class="form-control number input-mini" id="edit_deduction">-->
                                <?php echo form_dropdown('deduction',all_bucketweight_drop(),' ', 'class="form-control deduction input-mini" onchange="cal_deduction_netQty(this),cal_deduction_netQtyEdit()" id="edit_deduction"  required'); ?>
                            </td>

                            <td><input type="text" name="quantityRequested" placeholder="0.00"
                                       class="form-control number input-mini" onfocus="this.select();"
                                       id="edit_quantityRequested" onkeyup="checkCurrentStockEdit(this)" readonly></td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="estimatedAmount" placeholder="<?php echo $placeholder ?>"
                                           class="form-control number input-mini" onfocus="this.select();"
                                           onkeyup="edit_change_amount()"

                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           id="edit_estimatedAmount">
                                </div>
                            </td>
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
                                <input type="text" name="discount_amount" id="edit_discount_amount" placeholder="0.00"
                                       onkeyup="edit_cal_discount_amount()" onfocus="this.select();" value="0"
                                       class="form-control number">
                            </td>
                            <?php
                            $policy = getPolicyValues('ATT', 'All');
                            if($policy==0 || $policy==null) {
                                ?>

                                <!--<td style="width: 120px">
                                    <div class="input-group">
                                        <input type="text" name="item_taxPercentage" id="edit_item_taxPercentage"
                                               placeholder="0.00" onfocus="this.select();"
                                               class="form-control number item_taxPercentage input-mini" value="0" readonly>
                                        <span class="input-group-addon input-group-addon-mini">%</span>
                                    </div>
                                </td>-->
                                <?php
                            }
                            ?>
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
<div>

    <?php echo form_open('login/loginSubmit', ' name="tageline" id="tageline" class="form-group" role="form"'); ?>
    <input class="hidden" id="invoiceID" name="invoiceID" value="">
    </form>
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
    var defaultSegment = '<?php echo $master['segmentID'] ?>|<?php echo $master['segmentCode'] ?>';

    var current_trigger_event = null;
    if (typeof checkKeyPressing !== 'function'){
        function checkKeyPressing(evt) {
            if (evt.keyCode == 112) {
                evt.preventDefault();
                switch(current_trigger_event){
                    case 'income': add_more_income();
                        break;
                    case 'item': add_more('item_add_table');
                        break;
                }

            }
        }
    }
    window.addEventListener("keydown", checkKeyPressing, false);

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
                tax_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_tax_amount_required');?>.'}}}, /*Tax Amount is required*/
                text_type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_tax_type_required');?>.'}}}, /*Tax Type is required*/
                percentage: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_percentage_is_required');?>.'}}}/*Percentage is required*/
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
                url: "<?php echo site_url('InvoicesPercentage/save_inv_tax_detail'); ?>",
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

        $('#discount_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                discount_amount: {validators: {notEmpty: {message: 'Discount amount is required.'}}},
                discountExtraChargeID: {validators: {notEmpty: {message: 'Discount Type is required.'}}},
                discountPercentage: {validators: {notEmpty: {message: 'Discount Percentage is required.'}}}
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
                url: "<?php echo site_url('InvoicesPercentage/save_inv_discount_detail'); ?>",
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
                url: "<?php echo site_url('InvoicesPercentage/save_inv_extra_detail'); ?>",
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
        tabID = tab;
        if (invoiceAutoID) {
            // $("#wareHouseAutoID").val(null).trigger("change");
            // $('#pv_item_detail_form')[0].reset();
            if(title == 'Sales Order Base')
            {
                title = 'Sales Order Base (<?php echo $master['customerSystemCode'] . ' | ' . $master['customerName']; ?>)';
                fetch_sales_order_base();
            }
            $('.invoice_con_title').html(title);
            $('#table_body').empty();
            $('#table_body').append('<tr class="danger"><td colspan="14" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
            $('#table_tfoot').empty();
            $("#invoice_con_base_modal").modal({backdrop: "static"});
        }
    }

    function invoice_item_detail_modal(tab) {

        current_trigger_event = 'item';
        tabID = tab;
        if (invoiceAutoID) {
            invoiceDetailsAutoID = null;
            $('.search').typeahead('destroy');
            $('#invoice_item_detail_form')[0].reset();
            /*$('#item_taxPercentage').val(0);
             $('#item_taxPercentage').prop('readonly', true);*/
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
        current_trigger_event = 'income';
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
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID},
                url: "<?php echo site_url('InvoicesPercentage/fetch_con_detail_table'); ?>",
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
                        currencyID = '';
                        $('#table_body').append('<tr class="danger"><td colspan="14" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                        <!--No Records Found-->
                    } else {
                        $("#invoiceType").prop("disabled", true);
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        tot_amount = 0;
                        receivedQty = 0;
                        $.each(data['detail'], function (key, value) {


                            if (value['receivedQty'] != null && value['receivedQty'] !== undefined) {
                                receivedQty = value['receivedQty'];
                            }
                            $('#table_body').append('<tr id="tx_' + value['contractDetailsAutoID'] + '"><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center hide"><select class="whre_drop" style="width: 110px;"  id="whre_' + value['contractDetailsAutoID'] + '"><option value="">Select WareHouse</option></select></td><td class="text-right">' + (value['requestedQty'] - value['receivedQty']) + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-center"><input type="text" class="number" size="8" id="noitem_' + value['contractDetailsAutoID'] + '"  ></td><td class="text-center"><input type="text" class="number" size="8" value='+(value['requestedQty'] - value['receivedQty'])+' id="grossqty_' + value['contractDetailsAutoID'] + '"  onchange="calbucketweight(' + value['contractDetailsAutoID'] + ',this)" ></td><td class="text-center"><input type="text" class="number" size="8" id="buckets_' + value['contractDetailsAutoID'] + '" onchange="calbucketweight(' + value['contractDetailsAutoID'] + ',this)"  ></td><td class="text-center"><select class="whre_bweight" style="width: 110px;" onchange="calbucketweight(' + value['contractDetailsAutoID'] + ',this)"  id="whre_bweight_' + value['contractDetailsAutoID'] + '"><option value=" ">Select B weigh</option></select></td><td class="text-center"><input type="text" class="number qty" size="8" id="qty_' + value['contractDetailsAutoID'] + '" onkeyup="select_check_box(' + value['contractDetailsAutoID'] + ')" onchange="calbucketweight(' + value['contractDetailsAutoID'] + ',this)"  ></td><td class="text-center"><input type="text" class="number" size="10" value="' + parseFloat(value['unittransactionAmount']).toFixed(currency_decimal) + '" id="amount_' + value['contractDetailsAutoID'] + '" onkeyup="select_amount(' + value['contractDetailsAutoID'] + ',this.value)"></td><td class="text-center"><select onkeydown="keychange(event)" name="item_text[]" class="form-control item_text input-mini" id="item_text_' + value['contractDetailsAutoID'] + '"> <option value=" " selected="selected">Select Tax Types</option> </select></td><td class="text-right" id="tot_' + value['contractDetailsAutoID'] + '">0.00</td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['contractDetailsAutoID'] + '" type="checkbox" value="' + value['contractDetailsAutoID'] + '"></td></tr>');
                            x++;
                            tot_amount += (parseFloat(value['totalAmount']));
                            var ths=$('#tx_' + value['contractDetailsAutoID'] + '');
                            get_tax_drop(ths, value['itemAutoID']);
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
                        if (!jQuery.isEmptyObject(data['bucketweightdrop'])) {
                            $('.whre_bweight').empty();
                            var mySelect = $('.whre_bweight');
                            mySelect.append($('<option></option>').val('').html('Select B weight'));
                            $.each(data['bucketweightdrop'], function (val, text) {
                                mySelect.append($('<option></option>').val(text['weightAutoID']).html(text['bucketWeight']));
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

    function select_amount(id, value) {
        var tax_percentage = $('#item_taxPercentage_' + id).val();
        $('#tax_amount_' + id).val(parseFloat((tax_percentage / 100) * value).toFixed(2));
        select_check_box(id);
    }

    function cal_con_base_tax(id, element) {
        var result = $(element).children(':selected').val();
        if (result) {
            var result = $(element).children(':selected').text().split('|');
            var percentage = parseFloat(result[1].replace("%", ""));
            $('#item_taxPercentage_' + id).val(parseFloat(percentage).toFixed(2));
            $('#item_taxPercentage_' + id).prop('readonly', false);
            var qty = parseFloat($('#qty_' + id).val());
            var amount = parseFloat($('#amount_' + id).val());
            $('#tax_amount_' + id).prop('readonly', false);
            $('#tax_amount_' + id).val(parseFloat((percentage / 100) * amount).toFixed(2));
        } else {
            $('#tax_amount_' + id).val(0);
            $('#item_taxPercentage_' + id).val(0);
            $('#tax_amount_' + id).prop('readonly', true);
            $('#item_taxPercentage_' + id).prop('readonly', true);
        }

        select_check_box(id);
    }

    function select_check_box(id) {
        var qty = $('#qty_'+ id).val();
        var amount = $('#amount_' + id).val();
        var tax_amount = $('#tax_amount_' + id).val();
        var tex_type = $('#tax_drop_' + id).text();
        var whre_bweight = $('#whre_bweight_' + id).text();
        $("#check_" + id).prop("checked", false);
        if (qty > 0 && amount >= 0 || tex_type > 0) {
            $("#check_" + id).prop("checked", true);
        }
        if(tex_type)
        {
            var total = parseFloat(qty) * (parseFloat(amount) + parseFloat(tax_amount));
        }else
        {
            var total = parseFloat(qty) * (parseFloat(amount));
        }

        var totalnew = parseFloat(total).formatMoney(2, '.', '');
        $('#tot_' + id).text(totalnew);


    }

    function change_tax_per(id, percentage) {
        if (percentage > 0) {
            var amount = $('#amount_' + id).val();
            $('#tax_amount_' + id).val((parseFloat((percentage / 100) * amount)).toFixed(2));
        } else {
            $('#tax_amount_' + id).val(0);
        }
        select_check_box(id);
    }

    function change_tax_amount(id, discount_amount) {
        if (discount_amount > 0) {
            var total_amount = $('#amount_' + id).val();
            $('#item_taxPercentage_' + id).val(((parseFloat(discount_amount) / total_amount) * 100).toFixed(2));
        } else {
            $('#item_taxPercentage_' + id).val(0);
        }
        select_check_box(id);
    }

    function select_text_item(element) {
        if (element.value != 0) {
            var result = $(element).children(':selected').text().split('|');
            var res = parseFloat(result[2].replace("%", "")).toFixed(2);
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
                $(this).closest('tr').find('.wareHouseAutoID').val('').change();
                checkitemavailable(this);
                if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                    }, 200);
                    $('#f_search_' + id).val('');
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    myAlert('w','Revenue GL code not assigned for selected item')
                }
                get_tax_drop(this,suggestion.itemAutoID);
                $(this).closest('tr').find('.wareHouseAutoID').removeAttr('onchange');
                $(this).closest('tr').find('.wareHouseAutoID').val(<?php echo $master['warehouseAutoID']; ?>).change();
                /*if(suggestion.mainCategory=='Service'){
                 $(this).closest('tr').find('.wareHouseAutoID').removeAttr('onchange');
                 }else{
                 $(this).closest('tr').find('.wareHouseAutoID').attr('onchange', 'checkitemavailable(this)');
                 }*/
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
            serviceUrl: '<?php echo site_url();?>Receipt_voucher/fetch_itemrecode_po/',
            onSelect: function (suggestion) {

                setTimeout(function () {
                    $('#edit_itemAutoID').val(suggestion.itemAutoID);

                }, 200);
                //$('#edit_estimatedAmount').val(suggestion.companyLocalSellingPrice);
                $(this).closest('tr').find('#edit_wareHouseAutoID').val('').change();
                fetch_sales_price_edit(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);

                if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
                    $('#edit_itemAutoID').closest('tr').find('.itemAutoID').val('');
                    $('#edit_itemAutoID').val('');
                    $('#edit_itemAutoID').closest('tr').css("background-color", '#ffb2b2 ');
                    myAlert('w','Revenue GL code not assigned for selected item')
                }
                get_tax_drop_edit(suggestion.itemAutoID,0)
                $('#edit_wareHouseAutoID').removeAttr('onchange');
                /*if(suggestion.mainCategory=='Service'){
                 $('#edit_wareHouseAutoID').removeAttr('onchange');
                 }else{
                 $('#edit_wareHouseAutoID').attr('onchange', 'editstockwarehousestock(this)');
                 }*/


            }
        });
    }

    function fetch_invoice_direct_details() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('InvoicesPercentage/fetch_invoice_direct_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                tax_total = 0;
                discount_total = 0;
                transactionDecimalPlaces = 2;

                $('#gl_table_body,#item_table_body,#item_table_tfoot,#gl_table_tfoot').empty();
                if (jQuery.isEmptyObject(data['detail'])) {
                    <?php
                    $policy = getPolicyValues('ATT', 'All');
                    if($policy==0 || $policy==null) {
                    ?>
                    $('#gl_table_body,#item_table_body').append('<tr class="danger"><td colspan="15" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    <?php
                    }else{
                    ?>
                    $('#gl_table_body,#item_table_body').append('<tr class="danger"><td colspan="14" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    <?php
                    }
                    ?>
                    /*No Records Found*/
                    $("#invoiceType").prop("disabled", false);
                    $("#customerID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#editallbtn").addClass("hidden");
                    currencyID = '';
                } else {
                    $("#invoiceType").prop("disabled", true);
                    $("#customerID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#editallbtn").removeClass("hidden");
                    x = 1;
                    y = 1;
                    transactionDecimalPlaces = data['currency']['transactionCurrencyDecimalPlaces'];
                    LocalDecimalPlaces = data['currency']['companyLocalCurrencyDecimalPlaces'];
                    partyDecimalPlaces = data['currency']['customerCurrencyDecimalPlaces'];
                    gl_trans_amount = 0;
                    gl_local_amount = 0;
                    gl_party_amount = 0;
                    item_trans_amount = 0;
                    item_local_amount = 0;
                    item_party_amount = 0;
                    $.each(data['detail'], function (key, value) {

                        if (value['type'] == 'Item') {
                            $('#item_table_tfoot').empty();
                            val = '';
                            if (value['contractCode'] !== null) {
                                val = value['contractCode'] + ' - ';
                            }
                            if (value['isSubitemExist'] == 1) {


                                var colour = 'color: #dad835 !important';
                                colour = '';
                                <?php
                                $policy = getPolicyValues('ATT', 'All');
                                if($policy==0 || $policy==null) {
                                ?>
                                string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] + '-' + value['wareHouseLocation'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount']))).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * value['taxAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['invoiceDetailsAutoID'] + ',\'CINV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                <?php
                                }else{
                                ?>
                                string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] + '-' + value['wareHouseLocation'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount']))).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['invoiceDetailsAutoID'] + ',\'CINV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                <?php
                                }
                                ?>
                            } else {
                                if (data['currency']['invoiceType'] == "Direct") {

                                    if(value['printTagYN'] == 1)
                                    {
                                        var printag = '<a onclick="add_print_tagline(' + value['invoiceDetailsAutoID'] + ',1);"> <span title="Remove Tag" rel="tooltip" class="glyphicon glyphicon-remove"> |&nbsp;<a target="_blank" href="<?php echo site_url('InvoicesPercentage/print_tageline_buyback/') . '/' .'\' + value[\'invoiceDetailsAutoID\'] + \'' ?>"><span title="Print Tag" rel="tooltip" class="glyphicon glyphicon-print"></span></a>'
                                    }else
                                    {
                                        var printag = '<a onclick="add_print_tagline(' + value['invoiceDetailsAutoID'] + ',2);"> <span title="Add Tag" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>'
                                    }


                                    <?php
                                    $policy = getPolicyValues('ATT', 'All');
                                    if($policy==0 || $policy==null) {
                                    ?>
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] + '-' + value['wareHouseLocation'] + '</td><td>' + value['noOfItems'] + '</td><td>' + value['grossQty'] + '</td><td>' + value['noOfUnits'] + '</td><td>' + value['deduction'] + '</td><td>' + parseFloat((value['noOfUnits'] * value['deduction'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + (parseFloat(value['unittransactionAmount'])+ parseFloat(value['taxAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '(' + value['discountPercentage'] + '%)</td><td class="text-right">' + (parseFloat(value['unittransactionAmount'] - value['discountAmount'])+ parseFloat(value['taxAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['requestedQty'] * (parseFloat(value['unittransactionAmount'])+ parseFloat(value['taxAmount']) /*(value['unittransactionAmount'] - value['discountAmount'])*/)).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * value['taxAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right"> '+ printag +' &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['invoiceDetailsAutoID'] +');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    <?php
                                    }else{
                                    ?>
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] + '-' + value['wareHouseLocation'] + '</td><td>' + value['noOfItems'] + '</td><td>' + value['grossQty'] + '</td><td>' + value['noOfUnits'] + '</td><td>' + value['deduction'] + '</td><td>' + parseFloat((value['noOfUnits'] * value['deduction'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '(' + value['discountPercentage'] + '%)</td><td class="text-right">' + parseFloat(value['unittransactionAmount'] - value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount'] - value['discountAmount']))).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">'+ printag +' &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"> <span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    <?php
                                    }
                                    ?>
                                } else {
                                    if(value['printTagYN'] == 1)
                                    {
                                        var printag = '<a onclick="add_print_tagline(' + value['invoiceDetailsAutoID'] + ',1);"> <span title="Remove Tag" rel="tooltip" class="glyphicon glyphicon-remove"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a target="_blank" href="<?php echo site_url('InvoicesPercentage/print_tageline_buyback/') . '/' .'\' + value[\'invoiceDetailsAutoID\'] + \'' ?>"><span title="Print Tag" rel="tooltip" class="glyphicon glyphicon-print"></span></a>'
                                    }else
                                    {
                                        var printag = '<a onclick="add_print_tagline(' + value['invoiceDetailsAutoID'] + ',2);"> <span title="Add Tag" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>'
                                    }
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] + '-' + value['wareHouseLocation'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['grossQty'] + '</td><td class="text-right">' + (parseFloat(value['unittransactionAmount'])+ parseFloat(value['taxAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-center">' + value['noOfItems'] + '</td><td class="text-center">' + value['grossQty'] + '</td><td class="text-center">' + value['deduction'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * ((parseFloat(value['unittransactionAmount'])+ parseFloat(value['taxAmount']))) /*(value['unittransactionAmount'])*/)).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * value['taxAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">'+ printag +'&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a>  | &nbsp;&nbsp; <a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';

                                }

                            }
                            $('#item_table_body').append(string);

                            x++;
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            discount_total += (parseFloat(value['transactionAmount']));
                            item_trans_amount += (parseFloat(value['transactionAmount']));
                            item_local_amount += (parseFloat(value['companyLocalAmount']));
                            item_party_amount += (parseFloat(value['customerAmount']));
                            if (data['currency']['invoiceType'] == "Direct") {
                                <?php
                                $policy = getPolicyValues('ATT', 'All');
                                if($policy==0 || $policy==null) {
                                ?>
                                $('#item_table_tfoot').append('<tr><td colspan="14" class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?> </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                                <?php
                                }else{
                                ?>
                                $('#item_table_tfoot').append('<tr><td colspan="12" class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?> </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                                <?php
                                }
                                ?>
                                <!--Item Total-->
                            } else {
                                $('#item_table_tfoot').append('<tr><td colspan="12" class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?>  </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            }
                            <!--Item Total-->

                        } else {
                            $('#gl_table_tfoot').empty();
                            $('#gl_table_body').append('<tr><td>' + y + '</td><td>' + value['revenueGLCode'] + '</td><td>' + value['revenueGLDescription'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_gl_item(' + value['invoiceDetailsAutoID'] + ',\'' + value['GLDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            //<td class="text-right">' + parseFloat(value['companyLocalAmount']).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['customerAmount']).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                            y++;
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            discount_total += (parseFloat(value['transactionAmount']));
                            gl_trans_amount += (parseFloat(value['transactionAmount']));
                            //gl_local_amount += (parseFloat(value['companyLocalAmount']));
                            //gl_party_amount += (parseFloat(value['customerAmount']));
                            $('#gl_table_tfoot').append('<tr><td colspan="5" class="text-right"> Total </td><td class="text-right total">' + parseFloat(gl_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td></tr>');
                            //<td class="text-right total">' + parseFloat(gl_local_amount).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right total">' + parseFloat(gl_party_amount).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                        }
                    });
                }
                d_total = 0;
                $('#discount_tot').text('Discount Applicable Amount( ' + parseFloat(discount_total).formatMoney(2, '.', ',') + ' )');
                $('#discount_table_body_recode,#discount_table_footer').empty();
                /*Tax Applicable Amount */
                if (jQuery.isEmptyObject(data['discount_detail'])) {
                    $('#discount_table_body_recode').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    <!--No Records Found-->
                } else {
                    d = 1;
                    $.each(data['discount_detail'], function (key, value) {
                        $('#discount_table_body_recode').append('<tr><td>' + d + '</td><td>Discount</td><td>' + value['discountDescription'] + '</td><td class="text-right">' + value['discountPercentage'] + '% </td><td class="text-right">' + parseFloat((parseFloat(value['discountPercentage']) / 100) * discount_total).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="delete_discount_gen(' + value['discountDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        d++;
                        d_total += parseFloat((parseFloat(value['discountPercentage']) / 100) * discount_total);
                    });
                    if (d_total > 0) {
                        $('#discount_table_footer').append('<tr><td colspan="4" class="text-right">Discount Total </td><td class="text-right total">' + parseFloat(d_total).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                    }
                }

                //$('#extraCharge_tot').text('Extra Charges Applicable Amount( ' + parseFloat(gl_trans_amount).formatMoney(2, '.', ',') + ' )');
                t_extraCharge=0
                $('#extra_table_body_recode,#extra_table_footer').empty();
                if (jQuery.isEmptyObject(data['extraChargeDetail'])) {
                    $('#extra_table_body_recode').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    <!--No Records Found-->
                } else {
                    e = 1;
                    ex_total = 0;
                    $.each(data['extraChargeDetail'], function (key, value) {
                        $('#extra_table_body_recode').append('<tr><td>' + e + '</td><td>Extra Charge</td><td>' + value['extraChargeDescription'] + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="delete_extra_gen(' + value['extraChargeDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        e++;
                        ex_total += parseFloat(value['transactionAmount']);
                        if(value['isTaxApplicable']==1){
                            t_extraCharge += parseFloat(value['transactionAmount']);
                        }
                    });
                    if (ex_total > 0) {
                        $('#extra_table_footer').append('<tr><td colspan="3" class="text-right">Extra Charge Total </td><td class="text-right total">' + parseFloat(ex_total).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                    }
                }
                tax_total = (tax_total-d_total)+t_extraCharge;
                $('#tax_tot').text('<?php echo $this->lang->line('sales_markating_transaction_tax_applicable_amount');?>( ' + parseFloat(tax_total).formatMoney(2, '.', ',') + ' )');
                $('#tax_table_body_recode,#tax_table_footer').empty();
                /*Tax Applicable Amount */
                if (jQuery.isEmptyObject(data['tax_detail'])) {
                    $('#tax_table_body_recode').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    <!--No Records Found-->
                } else {
                    x = 1;
                    t_total = 0;
                    $.each(data['tax_detail'], function (key, value) {
                        $('#tax_table_body_recode').append('<tr><td>' + x + '</td><td>' + value['taxShortCode'] + '</td><td>' + value['taxDescription'] + '</td><td class="text-right">' + value['taxPercentage'] + '% </td><td class="text-right">' + parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="delete_tax(' + value['taxDetailAutoID'] + ',\'' + value['taxShortCode'] + '\',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        x++;
                        t_total += parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total);
                    });
                    if (t_total > 0) {
                        $('#tax_table_footer').append('<tr><td colspan="4" class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_tax_tot');?> </td><td class="text-right total">' + parseFloat(t_total).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                    }
                    <!--Tax Total-->
                }

                $("[rel=tooltip]").tooltip();


                stopLoad();
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
            url: "<?php echo site_url('InvoicesPercentage/customerinvoiceGLUpdate'); ?>",
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
    function add_print_tagline(invoiceDetailsAutoID,type)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {invoiceDetailsAutoID: invoiceDetailsAutoID,type:type},
            url: "<?php echo site_url('InvoicesPercentage/add_print_taglineYN'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetch_invoice_direct_details();
                }


            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function cal_tax_amount(discount_amount, total_amount) {
        if (total_amount && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / total_amount) * 100).toFixed(0));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_tax(discount, total_amount) {
        if (total_amount && discount) {
            $('#tax_amount').val((total_amount / 100) * parseFloat(discount));
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
                        url: "<?php echo site_url('InvoicesPercentage/delete_tax_detail'); ?>",
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

    function edit_item(id) {
        var InvoiceNewType = $('#invoiceType').val();
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
                    url: "<?php echo site_url('InvoicesPercentage/fetch_customer_invoice_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        //pv_item_detail_modal();
                        invoiceDetailsAutoID = data['invoiceDetailsAutoID'];
                        projectID = data['projectID'];
                        $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                        //$('#search').typeahead('val', data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                        fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                        $('#edit_quantityRequested').val(data['requestedQty']);
                        $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmountANDtax'])));
                        if (data['invoiceType'] == "Direct") {
                            $('#edit_discount_amount').val(data['discountAmount']);
                            $('#edit_discount').val(data['discountPercentage']);
                        } else {
                            $('.directdiscount').addClass('hidden')
                        }
                        //$('#edit_search_id').val(data['itemSystemCode']);
                        //$('#edit_itemSystemCode').val(data['itemSystemCode']);
                        $('#edit_itemAutoID').val(data['itemAutoID']);
                        $('#edit_itemDescription').val(data['itemDescription']);
                        $('#edit_wareHouseAutoID').val(data['wareHouseAutoID']).change();
                        //$('#edit_UnitOfMeasureID').val(data['defaultUOMID']).change();
                        $('#edit_remarks').val(data['remarks']);
                        $('#edit_noOfItems').val(data['noOfItems']);
                        $('#edit_grossQty').val(data['grossQty']);
                        $('#edit_noOfUnits').val(data['noOfUnits']);
                        $('#edit_deduction').val(data['bucketWeightID']).change();
                        $('#currentstock_edit').val(data.currentStock);
                        //$('#edit_item_text').val(data['taxMasterAutoID']);
                        /*$('#edit_item_taxPercentage').val(data['taxPercentage']);
                         if (data['taxPercentage'] != 0) {
                         $('#edit_item_taxPercentage').prop('readonly', false);
                         } else {
                         $('#edit_item_taxPercentage').prop('readonly', true);
                         }*/
                        if (InvoiceNewType == 'Quotation' || InvoiceNewType == 'Contract' || InvoiceNewType == 'Sales Order') {
                            $('#edit_UnitOfMeasureID').prop("disabled", true);
                            $('#search').prop("disabled", true);
                        }
                        get_tax_drop_edit(data['itemAutoID'],data['taxMasterAutoID']);
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

    function edit_gl_item(id, value) {
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
                    url: "<?php echo site_url('InvoicesPercentage/fetch_customer_invoice_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        invoiceDetailsAutoID = data['invoiceDetailsAutoID'];
                        load_segmentBase_projectID_incomeEdit(data['segmentID'], data['projectID']);
                        $('#edit_gl_code').val(data['revenueGLAutoID']).change();
                        $('#edit_segment_gl').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#edit_amount').val(data['transactionAmount']);
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
                    url: "<?php echo site_url('InvoicesPercentage/delete_item_direct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
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

    function save_con_base_item_buyback() {
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
        var noofitems = [];
        var grossqty = [];
        var buckets = [];
        var bucketweightID = [];
        var bucketweight = [];
        $('#table_body input:checked').each(function () {
            if ($('#amount_' + $(this).val()).val() == '') {
                swal("Cancelled", "Received cost cannot be blank !", "error");
            } else {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                qty.push($('#qty_' + $(this).val()).val());
                wareHouseAutoID.push($('#whre_' + $(this).val()).val());
                // whrehouse.push($('#whre_' + $(this).val() + ' option:selected').text());
                tex_id.push($('#item_text_' + $(this).val()).val());
                tex_type.push($('#tax_drop_' + $(this).val() + ' option:selected').text());
                tex_percntage.push($('#item_taxPercentage_' + $(this).val()).val());
                tex_amount.push($('#tax_amount_' + $(this).val()).val());
                remarks.push($('#remarks_' + $(this).val()).val());
                noofitems.push($('#noitem_' + $(this).val()).val());
                grossqty.push($('#grossqty_' + $(this).val()).val());
                buckets.push($('#buckets_' + $(this).val()).val());
                bucketweightID.push($('#whre_bweight_' + $(this).val()).val());
                bucketweight.push($('#whre_bweight_' + $(this).val() + ' option:selected').text());
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
                    //  'whrehouse': whrehouse,
                    'tex_id': tex_id,
                    'tex_type': tex_type,
                    'tex_percntage': tex_percntage,
                    'tex_amount': tex_amount,
                    'remarks': remarks,
                    'noofitems': noofitems,
                    'grossqty': grossqty,
                    'buckets': buckets,
                    'bucketweightID': bucketweightID,
                    'bucketweight': bucketweight
                },
                url: "<?php echo site_url('InvoicesPercentage/save_con_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    fetch_con_detail_table(data[2]);
                    fetch_invoice_direct_details();

                    if (data[0]=='s') {
                        fetch_con_detail_table(data[2]);
                        fetch_invoice_direct_details();

                    }
                    swal({
                            title: "Are you sure?",
                            text: "You want to Print a tag?",
                            type: "warning",
                            showCancelButton: true,
                            cancelButtonText: "No",
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Print"
                        },
                        function () {
                            $("#invoiceID").val(data[3]);

                            generateprint_tageline_buyback(data[3]);
                        });
                }, error: function () {
                    $('#invoice_con_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function generateprint_tageline_buyback(id) {
        var form= document.getElementById('tageline');
        form.target='_blank';
        form.action='<?php echo site_url('InvoicesPercentage/generateprint_tageline_buyback'); ?>';
        form.submit();
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
        appendData.find('.item_text').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.discount_amount').val(0);
        appendData.find('.discount').val(0);
        ;
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

    function saveInvoiceItemDetail_buyback() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#invoice_item_detail_form').serializeArray();
        if (invoiceAutoID) {
            data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
            data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
//            data.push({'name': 'wareHouse', 'value': $('#wareHouseAutoID option:selected').text()});
//            data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
            $('select[name="wareHouseAutoID[]"] option:selected').each(function () {
                data.push({'name': 'wareHouse[]', 'value': $(this).text()})
            })

            $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            })
            $('select[name="deduction[]"] option:selected').each(function () {
                data.push({'name': 'deductionvalue[]', 'value': $(this).text()})
            })
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
                    url: "<?php echo site_url('InvoicesPercentage/save_invoice_item_detail_buyback'); ?>",
                    beforeSend: function () {
                        startLoad();
                        $('.umoDropdown').prop("disabled", true);
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
                            swal({
                                    title: "Are you sure?",
                                    text: "You want to Print a tag?",
                                    type: "warning",
                                    showCancelButton: true,
                                    cancelButtonText: "No",
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Print"
                                },
                                function () {
                                    $("#invoiceID").val(data[2]);

                                    generateprint_tageline_buyback(data[2]);
                                });
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

    function saveDirectInvoiceDetails_buyback() {
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
            url: "<?php echo site_url('InvoicesPercentage/save_direct_invoice_detail_buyback'); ?>",
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
            url: "<?php echo site_url('InvoicesPercentage/update_income_invoice_detail'); ?>",
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
            data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            data.push({'name': 'deductionvalue', 'value': $('#edit_deduction option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('InvoicesPercentage/update_invoice_item_detail'); ?>",
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
                tableName: 'srp_erp_customerinvoicemaster_temp',
                primaryKey: 'invoiceAutoID'
            },
            url: "<?php echo site_url('InvoicesPercentage/fetch_sales_price'); ?>",
            success: function (data) {
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                    if(data['isModificationAllowed']==1){
                        $(element).closest('tr').find('.estimatedAmount').attr('readonly',false);
                    }else{
                        $(element).closest('tr').find('.estimatedAmount').attr('readonly',true);
                    }
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
                primaryKey: 'invoiceAutoID'
            },
            url: "<?php echo site_url('InvoicesPercentage/fetch_sales_price'); ?>",
            success: function (data) {
                if (data['status']) {
                    $('#edit_estimatedAmount').val(data.amount);
                    if(data['isModificationAllowed']==1){
                        $('#edit_estimatedAmount').attr('readonly',false);
                    }else{
                        $('#edit_estimatedAmount').attr('readonly',true);
                    }
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function keychange(e){
        if(e.which == 9){
            e.preventDefault();
            $("#saveChanges").focus();
        }
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
                $(element).closest('tr').find('.discount_amount').val((estimatedAmount / 100) * parseFloat(element.value))
            }
        }
    }

    function cal_discount_amount(element) {
        var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
        if(element.value>estimatedAmount){
            myAlert('w','Discount amount should be less than or equal to Amount');
            $(element).closest('tr').find('.discount').val(0);
            $(element).val(0)
        }else{
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount').val(((parseFloat(element.value) / estimatedAmount) * 100).toFixed(3))
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
                $('#edit_discount').val(((parseFloat(discountAmount) / estimatedAmount) * 100).toFixed(3))
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
        if (jQuery.inArray(mainconcat, concatarr) !== -1) {
            $(det).closest('tr').find('.f_search').val('');
            $(det).closest('tr').find('.itemAutoID').val('');
            $(det).closest('tr').find('.wareHouseAutoID').val('').change();
            $(det).closest('tr').find('.quantityRequested').val('');
            $(det).closest('tr').find('.estimatedAmount').val('');
            $(det).closest('tr').find('.discount').val('');
            $(det).closest('tr').find('.discount_amount').val('');
            $(det).closest('tr').find('.currentstock').val('');
            myAlert('w', 'Selected item is already selected');
        }

        //}
        if(det.value>0)
        {
            $(det).closest('tr').css("background-color",'white');
        }

    }

    function checkCurrentStock(det) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        if (det.value > parseFloat(currentStock)) {
            myAlert('w', 'Transfer quantity should be less than or equal to current stock');
            $(det).val(0);
        }

        if(det.value > 0)
        {
            $(det).closest('tr').css("background-color",'white');
        }
    }

    function checkCurrentStockEdit() {
        var currentStock = $('#currentstock_edit').val();
        var TransferQty = $('#edit_quantityRequested').val();
        if (parseFloat(TransferQty) > parseFloat(currentStock)) {
            myAlert('w', 'Transfer quantity should be less than or equal to current stock');
            $('#edit_quantityRequested').val(0);
        }
    }

    function fetch_rv_warehouse_item(itemAutoID, element, wareHouseAutoID) {
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
                    $(element).closest('tr').find('.currentstock').val(data['currentStock']);

                } else {

                    $(element).typeahead('val', '');
                    $(element).closest('tr').find('.currentstock').val('');

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
                        $('#currentstock_edit').val(data['currentStock']);

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


    function edit_all_item_detail_modal(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('InvoicesPercentage/fetch_customer_invoice_all_detail_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var descm = 2;
                $('#edit_item_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                } else {
                    $.each(data, function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" disabled  required')) ?>';
                        var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID"  required')) ?>';
                        var taxfield = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="taxfield_\'+key+\'"'), form_dropdown('item_text[]', all_tax_drop(1), '', 'class="form-control item_text input-mini"  onchange="select_text_item(this)"')) ?>';

                        var string = '<tr><td> <input type="text" class="form-control search f_search" name="search[]" id="f_search_'+ x +'" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]"> <input type="hidden" class="form-control invoiceDetailsAutoID" name="invoiceDetailsAutoID[]" value="' + value['invoiceDetailsAutoID'] + '"> </td> <td> '+ wareHouseAutoID +' </td> <td> '+ UOM +' </td> <td> <div class="input-group"> <input type="text" name="currentstock[]" value="' + value['currentStock'] + '" class="form-control currentstock" required disabled> </div> </td> <td><input type="text" onfocus="this.select();" name="quantityRequested[]" onkeyup="checkCurrentStock(this)" value="' + value['requestedQty'] + '" placeholder="0.00" class="form-control number input-mini quantityRequested" required></td> <td> <div class="input-group"> <input type="text" onfocus="this.select();" name="estimatedAmount[]" value="' + value['unittransactionAmount'] + '" onkeyup="change_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" placeholder="0.00" class="form-control number estimatedAmount input-mini"> </div> </td> <td style="width: 100px;"> <div class="input-group"> <input type="text" name="discount[]" placeholder="0.00" onkeyup="cal_discount(this)" onfocus="this.select();" value="' + value['discountPercentage'] + '" class="form-control number discount"> <span class="input-group-addon">%</span></div> </td> <td style="width: 100px;"> <input type="text" name="discount_amount[]" placeholder="0.00" value="' + value['discountAmount'] + '" onkeyup="cal_discount_amount(this)" onfocus="this.select();" class="form-control number discount_amount"> </td> <td> '+ taxfield +' </td> <td style="width: 120px"> <div class="input-group"> <input type="text" name="item_taxPercentage[]" placeholder="0.00" onfocus="this.select();" class="form-control number item_taxPercentage input-mini" id="item_taxPercentage_all_'+ x +'" value="' + value['taxPercentage'] + '" value="0" readonly> <span class="input-group-addon input-group-addon-mini">%</span> </div> </td> <td> <textarea class="form-control input-mini" rows="1" name="remarks[]" placeholder="Item Remarks...">' + value['remarks'] + '</textarea> </td> <td class="remove-td"><a onclick="delete_customer_invoiceDetailsEdit(' + value['invoiceDetailsAutoID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';


                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        $('#ware_'+key).val(value['wareHouseAutoID']).change();
                        $('#taxfield_'+key).val(value['taxMasterAutoID']);
                        if (data['taxPercentage'] != 0) {
                            $('#item_taxPercentage_all_'+key).prop('readonly', false);
                        } else {
                            $('#item_taxPercentage_all_'+key).prop('readonly', true);
                        }
                        fetch_related_uom_id( value['defaultUOMID'],value['unitOfMeasureID'],$('#uom_'+key));
                        initializeitemTypeahead(x);
                        x++;
                    });
                    $('.select2').select2();
                    search_id=x-1;
                    $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                }
                stopLoad();<!--Total-->

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
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
        $('#f_search_'+ search_id).closest('tr').css("background-color",'white');
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
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('InvoicesPercentage/updateCustomerInvoice_edit_all_Item'); ?>",
                    beforeSend: function () {
                        startLoad();
                        $('.umoDropdown').prop("disabled", true);
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
                    url: "<?php echo site_url('InvoicesPercentage/delete_item_direct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        load_conformation();
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

    function cal_discount_amount_general(discount_amount, total_amount) {
        if (total_amount && discount_amount) {
            $('#discountPercentage').val(((parseFloat(discount_amount) / total_amount) * 100).toFixed(0));
        } else {
            $('#discountPercentage').val(0);
        }
    }

    function cal_discount_general(discount, total_amount) {
        if (total_amount && discount) {
            $('#discount_trans_amount').val((total_amount / 100) * parseFloat(discount));
        } else {
            $('#discount_trans_amount').val(0);
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
                        url: "<?php echo site_url('InvoicesPercentage/delete_discount_gen'); ?>",
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
                        url: "<?php echo site_url('InvoicesPercentage/delete_extra_gen'); ?>",
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

    function get_tax_drop(det,itemAutoId){
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("InvoicesPercentage/get_tax_drop_buyback"); ?>',
            dataType: 'json',
            data: {'itemAutoId': itemAutoId},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
              
                    $(det).closest('tr').find('.item_text').empty();
                    var mySelect =  $(det).closest('tr').find('.item_text');
                    mySelect.append($('<option></option>').val('').html('Select tax type'));
                    $.each(data, function (val, text) {
                     mySelect.append($('<option></option>').val(text['taxFormulaID']).html(text['Description']));
                     });
                    /*mySelect.append($('<option></option>').val(data['taxCalculationformulaID']).html(data['Description']));*/

                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function get_tax_drop_edit(itemAutoId,taxMasterAutoID){
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("InvoicesPercentage/get_tax_drop"); ?>',
            dataType: 'json',
            data: {'itemAutoId': itemAutoId},
            async: false,
            success: function (data) {
                $('#edit_item_text').empty();
                if (!jQuery.isEmptyObject(data)) {
                    var mySelect =  $('#edit_item_text');
                    mySelect.append($('<option></option>').val('').html('Select tax type'));
                    /*$.each(data, function (val, text) {
                     mySelect.append($('<option></option>').val(text['taxCalculationformulaID']).html(text['Description']));
                     });*/
                    if(taxMasterAutoID===data['taxCalculationformulaID']){
                        mySelect.append($('<option selected></option>').val(data['taxCalculationformulaID']).html(data['Description']));
                    }else{
                        mySelect.append($('<option></option>').val(data['taxCalculationformulaID']).html(data['Description']));
                    }

                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function fetch_sales_order_base()
    {
        $('#salesorderbase').empty();
        var mySelect = $('#salesorderbase');
        mySelect.append($('<option></option>').val('').html('Select Sales Order Base'));
        <?php if (!empty($customer_con)) {
        foreach($customer_con as $val) {?>
        mySelect.append($('<option></option>').val(<?php echo $val['contractAutoID']?>).html('<?php echo $val['contractCode'] . ' | ' . $val['contractDate']?>'));
        <?php   }
        }?>
        //onchange="calbucketweight(this.value

    }
    function calbucketweight(id,buccketweight)
    {
        var grossqty = $('#grossqty_' + id).val();
        var buckets = $('#buckets_' + id).val();
        var price = $('#amount_' + id).val();

        //$('#whre_bweight_' + contractDetailsAutoID).val('#dropDownId :selected');
        var deduction = parseFloat($(buccketweight).closest('tr').find('.whre_bweight :selected').text());
        var qtynew = ((parseFloat(grossqty - (buckets * deduction)).toFixed(2)));
        $('#qty_' + id).val(qtynew);
        select_check_box(id);
        /*var totalnew = parseFloat(qtynew * price).formatMoney(2, '.', '');
         $('#tot_' + id).text(totalnew);*/

    }



</script>