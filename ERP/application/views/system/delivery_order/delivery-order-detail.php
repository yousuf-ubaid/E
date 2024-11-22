<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .boldtab{
        font-weight: bold;
        border-left-color: #ead8d8 !important;
    }

    .text-area-style{
        height: 30px !important;
        padding: 6px 4px 4px !important;
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
</style>
<?php

$primaryLanguage = getPrimaryLanguage();
$show_price_delivery_order = getPolicyValues('HPDO', 'All');
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$umo_arr = array('' => 'Select UOM');
$projectExist = project_is_exist();
$transaction_total = 0;
$margin = 0;
for ($x = 0; $x < count($detail['detail']); $x++) {
    $transaction_total += ($detail['detail'][$x]['transactionAmount'] - $detail['detail'][$x]['totalAfterTax']);
}

$placeholder = '0.00';
$currencyID = $master['transactionCurrency'];
$currency_decimal = $master['transactionCurrencyDecimalPlaces'];
if($currencyID == 'OMR')
{
    $placeholder = '0.000';
}
$qty_validate = getPolicyValues('VSQ', 'All');
$costChange_validate = getPolicyValues('CSC', 'All');
$open_contract = getPolicyValues('OCE', 'All');
$promotionPolicy = getPolicyValues('CDP', 'All');
if(!isset($promotionPolicy)) {
    $promotionPolicy = 0;
}
$group_based_tax;
switch ($invoiceType) {
    case "Direct":?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false"><?php echo $this->lang->line('common_item'); ?> </a></li>
                <li class="pull-left header">
                    <i class="fa fa-hand-o-right"></i><?php echo $this->lang->line('sales_marketing_direct_order_for'); ?> : - <?php echo $master['customerName']; ?>
                </li>
            </ul>
            <div class="tab-content">
                <div id="tab_2" class="tab-pane active">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="5"><?php echo $this->lang->line('sales_markating_transaction_document_item_details'); ?> </th>
                            <th colspan="6">
                                <?php echo $this->lang->line('common_price'); ?> <span class="currency">( <?php echo $master['transactionCurrency']; ?><!--Price--> )</span>
                            </th>
                            <th>
                                <button type="button" onclick="invoice_item_detail_modal(2)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add Item"><i class="fa fa-plus"></i></button>
                                <button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;"
                                        id="editallbtn" class="btn  btn-xs btn-default hidden pull-right"><span
                                        class="glyphicon glyphicon-pencil"></span> <?php echo $this->lang->line('common_document_edit_all'); ?><!-- Edit All-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_code'); ?> </th><!--Code-->
                            <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th> <!--Description-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?> </th> <!--UOM-->
                            <th style="width: 15%;"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?></th> <!--Qty-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_unit'); ?> </th> <!--Unit-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th> <!--Discount-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_unit_price'); ?> </th> <!--Net Unit Cost-->
                            <th><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                            <th><?php echo $this->lang->line('sales_markating_net_totl'); ?> </th><!--Net Total-->
                            <th><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <tr class="danger">
                            <td colspan="12" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b> <!--No Records Found-->
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="item_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <?php if($group_based_tax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="exampleInputName2"
                                id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <input type="hidden" id="taxtotalamnt" name="taxtotalamnt">
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
                                    <input type="text" class="form-control number" onkeypress="return validateFloatKeyPress(this,event);" id="tax_amount" name="tax_amount"
                                        style="width: 100px;"
                                        onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>)">
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
                                    <th><?php echo $this->lang->line('sales_markating_transaction_net_tax_type'); ?> </th> <!--Tax Type-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_detail'); ?> </th> <!--Detail-->
                                    <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <th>
                                        <?php echo $this->lang->line('common_amount'); ?> (<?php echo $master['transactionCurrency']; ?>) <!--Amount-->
                                    </th>
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
                                $tax_total_str = $this->lang->line('sales_markating_transaction_sales_tax_tot');
                                if (!empty($detail['tax'])) {
                                    echo '<tr>';
                                    echo '<td class="text-right" colspan="4">' . $tax_total_str . ' ( ' . $master['transactionCurrency'] . ' )</td>';
                                    echo '<td class="text-right total">' . format_number($tax_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php
    break;
    case "Quotation":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="active">
                    <a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false">
                        <?php echo $this->lang->line('sales_markating_transaction_quotation'); ?>
                    </a>
                </li>
                <li class="pull-left header">
                    <i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_marketing_quotation_base_order_for'); ?>
                    : - <?php echo $master['customerName']; ?>
                </li>
            </ul>
            <div class="tab-content">
                <div id="tab_2" class="tab-pane active">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                        <?php 
                        if($show_price_delivery_order == 1 && $invoiceType != 'Direct'){ 
                            $colspan_qtn = 7;
                        } else{
                            $colspan_qtn = 5;
                        }
                        ?>
                            <th colspan="<?php echo $colspan_qtn; ?>"><?php echo $this->lang->line('sales_markating_transaction_quotation_details'); ?>  </th>
                            <!--Quotation Details-->
                            <?php 
                            if($show_price_delivery_order == 1 && $invoiceType != 'Direct'){ ?>
                            <th colspan="4"><?php echo $this->lang->line('common_price'); ?> <span
                                    class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th><!--Price-->
                            <?php } ?>
                            <th>
                                <button type="button" onclick="invoice_con_modal('<?php echo $this->lang->line('sales_marketing_quotation_base'); ?>',2)"
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
                            <?php                             
                            if($show_price_delivery_order == 1 && $invoiceType != 'Direct'){ ?>
                            
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_unit'); ?></th>
                            <th><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_unit_price'); ?><!--Net Unit Price--></th>
                            <!--Unit-->
                            <th><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_amount'); ?> </th>
                            <!--Net Amount-->

                            <?php } ?>

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
                        <?php 
                        if($show_price_delivery_order == 1 && $invoiceType != 'Direct'){ ?>
                        <tfoot id="item_table_tfoot">

                        </tfoot>
                        <?php } ?>
                    </table>
                </div><!-- /.tab-pane -->
                <?php if($group_based_tax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="exampleInputName2"
                                id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?> </label>
                            <!--Tax for-->
                            <input type="hidden" id="taxtotalamnt" name="taxtotalamnt">
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
                                    <input type="text" class="form-control number" onkeypress="return validateFloatKeyPress(this,event);" id="tax_amount" name="tax_amount"
                                        style="width: 100px;"
                                        onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>)">
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
                                $tax_total_str = $this->lang->line('sales_markating_transaction_sales_tax_tot');
                                if (!empty($detail['tax'])) {
                                    echo '<tr>';
                                    echo '<td class="text-right" colspan="4">' . $tax_total_str . '( ' . $master['transactionCurrency'] . ' )</td>';
                                    echo '<td class="text-right total">' . format_number($tax_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php
        break;
    case "Contract":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="active">
                    <a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_contract'); ?></a>
                </li><!--Contract-->
                <li class="pull-left header">
                    <i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('sales_marketing_contract_base_order_for'); ?> : - <?php echo $master['customerName']; ?>
                </li>
            </ul>
            <div class="tab-content">
                <div id="tab_2" class="tab-pane active">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="7"><b><?php echo $this->lang->line('sales_markating_transaction_contract_details'); ?></th>
                            <!--Contract Details-->
                            <th colspan="4">
                                <?php echo $this->lang->line('common_price'); ?> <span class="currency">( <?php echo $master['transactionCurrency']; ?><!--Price--> )</span>
                            </th>
                            <th>
                                <button type="button" onclick="invoice_con_modal('<?php echo $this->lang->line('sales_marketing_contract_base'); ?>',2)"
                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                        data-placement="left" title="Add Contract"><i class="fa fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_code'); ?> </th>
                            <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?> </th>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?> </th>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_unit'); ?> </th>
                            <th><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_unit_price'); ?><!--Net Unit Price--></th>
                            <th><?php echo $this->lang->line('common_total'); ?> </th>
                            <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th>
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_amount'); ?> </th>
                            <th style="width: 75px !important;">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <tr class="danger">
                            <td colspan="10" class="text-center">
                                <b><?php echo $this->lang->line('common_no_records_found'); ?> </b><!--No Records Found-->
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="item_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <?php if($group_based_tax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="exampleInputName2"
                                id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <input type="hidden" id="taxtotalamnt" name="taxtotalamnt">
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
                                    <input type="text" class="form-control number" onkeypress="return validateFloatKeyPress(this,event);" id="tax_amount" name="tax_amount"
                                        style="width: 100px;"
                                        onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>)">
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
                                $tax_total_str = $this->lang->line('sales_markating_transaction_sales_tax_tot');
                                if (!empty($detail['tax'])) {
                                    echo '<tr>';
                                    echo '<td class="text-right" colspan="4">' . $tax_total_str . '<!--Tax Total--> ( ' . $master['transactionCurrency'] . ' )</td>';
                                    echo '<td class="text-right total">' . format_number($tax_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php
        break;
    case "Sales Order":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right" id="myTabs">
                <li class="active">
                    <a data-toggle="tab" class="boldtab" href="#tab_2"
                                aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_sales_order'); ?>
                    </a>
                </li><!--Sales Order-->
                <li class="pull-left header">
                    <i class="fa fa-hand-o-right"></i><?php echo $this->lang->line('sales_marketing_sales_order_base_order_for'); ?>
                    : - <?php echo $master['customerName']; ?>
                </li>
            </ul>
            <div class="tab-content">
                <div id="tab_2" class="tab-pane active">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="7"><?php echo $this->lang->line('sales_markating_transaction_sales_order_details'); ?>  </th>
                            <!--Sales Order Details-->
                            <th colspan="4">
                                <?php echo $this->lang->line('common_price'); ?> <span class="currency">(<?php echo $master['transactionCurrency']; ?> )</span></th>
                            <!--Price-->
                            <th>
                                <button type="button" onclick="invoice_con_modal('<?php echo $this->lang->line('sales_marketing_sales_order_base'); ?>',2)"
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
                            <th><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                            <th><?php echo $this->lang->line('sales_markating_transaction_net_unit_price'); ?><!--Net Unit Price--></th>
                            <!--Unit-->
                            <th><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
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
                <?php if($group_based_tax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <!--Tax for-->
                            <label for="exampleInputName2"
                                id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                )</label>
                            <input type="hidden" id="taxtotalamnt" name="taxtotalamnt">
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
                                    <input type="text" class="form-control number" id="tax_amount" onkeypress="return validateFloatKeyPress(this,event);" name="tax_amount"
                                        style="width: 100px;"
                                        onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>)">
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
                                $tax_total_str = $this->lang->line('sales_markating_transaction_sales_tax_tot');

                                if (!empty($detail['tax'])) {
                                    echo '<tr>';
                                    echo '<td class="text-right" colspan="4">' . $tax_total_str . '<!--Tax Total--> ( ' . $master['transactionCurrency'] . ' )</td>';
                                    echo '<td class="text-right total">' . format_number($tax_total, $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    echo '<td>&nbsp;</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <?php } ?>
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
                    <div class="col-md-2">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h4 class="invoice_con_title">&nbsp;</h4>
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked">
                                    <?php
                                    $norecfound = $this->lang->line('common_no_records_found');

                                    if (!empty($customer_con)) {
                                        for ($i = 0; $i < count($customer_con); $i++) {
//                                            if (($customer_con[$i]['Total'] > 0)) {
                                                $con_id = $customer_con[$i]['contractAutoID'];
                                                echo '<li id="pull-'.$con_id.'" class="pull-li"><a onclick="fetch_con_detail_table(' . $con_id . ')">' . $customer_con[$i]['contractCode'] . '<br> <strong> Date : </strong>' . $customer_con[$i]['contractDate'] . ' <br>  <strong> Ref : </strong>' . $customer_con[$i]['referenceNo'] . '<span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>';
//                                            }
                                        }
                                    } else {
                                        echo '<li><a>' . $norecfound . '</a></li>';
                                    }
                                    ?>
                                    <!--No Records found-->
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div style="min-height: .01%; overflow-x: auto;" >
                            <table class="table table-bordered table-striped table-condesed " id="dodelvteble">
                                <thead>
                                <?php if($show_price_delivery_order == 1 && $invoiceType != 'Direct'){ ?>
                                <tr>
                                    <?php if($openContractPolicy != 1) {?>
                                        <th colspan='4'><?php echo $this->lang->line('common_item'); ?></th><!--Item-->
                                    <?php } else { ?>
                                        <th colspan='3'><?php echo $this->lang->line('common_item'); ?></th><!--Item-->
                                    <?php } ?>
                                    <?php if($group_based_tax == 1) { ?>
                                        <th colspan='3'><?php echo $this->lang->line('common_item'); ?> <span
                                                class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span></th>
                                    <?php } else { ?>
                                        <th colspan='2'><?php echo $this->lang->line('common_item'); ?> <span
                                                class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span></th>
                                        <!--Item-->
                                    <?php } ?>
                                   
                                    <th colspan='6'> <?php echo $this->lang->line('sales_marketing_delivery_item'); ?><!--Delivery Item-->
                                        <span
                                            class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span></th>
                                    <!--Invoiced Item -->                                    
                                <tr>
                                <?php } ?>
                                <tr>
                                    <th>#</th>
                                    <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                                    <!--Description-->
                                    <th><?php echo $this->lang->line('common_warehouse'); ?> </th><!--Ware House-->
                                    <?php if($openContractPolicy != 1) {?>
                                        <th>Ordered Qty</th> <!--Qty-->
                                    <?php } ?>
                                    <?php 
                                    if($show_price_delivery_order == 1 && $invoiceType != 'Direct'){ ?>
                                    <th><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
                                    <th><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                                    <?php } ?>
                            <!--     <?php /*if($group_based_tax == 1) { */?>
                                        <th>Tax</th>
                                    --><?php /*} */?>
                                    <th style="width: 10%;">Received Qty</th>
                                    <!--Qty-->
                                    <?php 
                                    if($show_price_delivery_order == 1 && $invoiceType != 'Direct'){ ?>

                                    <th><?php echo $this->lang->line('common_price'); ?> </th><!--Price-->
                                    <th><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                    <?php if($group_based_tax == 1) { ?>
                                    <th>Tax Amount</th>

                                    <?php } ?>

                                    <th><?php echo $this->lang->line('common_total'); ?>  </th><!--Total-->
                                    <?php } ?>
                                    <th>&nbsp;</th>
                                    <th style="display: none;">&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody id="table_body">
                                <tr class="danger">
                                    <td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?>  </b></td><!--No Records Found-->
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
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
                <button type="button" class="btn btn-primary"
                        onclick="save_con_base_items()"><?php echo $this->lang->line('common_save_change'); ?> </button>
                <!--Save changes-->
            </div>
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
                            <th><?php echo $this->lang->line('sales_markating_transaction_item_code'); required_mark(); ?></th> <!--Item Code-->
                            <th><?php echo $this->lang->line('common_warehouse'); required_mark(); ?></th>
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project');?></th><!--Project-->
                                <th><?php echo $this->lang->line('common_project_category'); ?><!-- Project Category --></th>
                                <th><?php echo $this->lang->line('common_project_subcategory'); ?></th>
                            <?php } ?>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); required_mark(); ?></th><!--UOM-->
                            <th style="width:80px;"><abbr title="Current Stock">Stock</abbr></th>
                            <th style="width:80px;"><abbr title="Park Qty">Park Qty</abbr></th>
                            <th style="width: 80px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); required_mark(); ?></th>
                            <th style="width: 120px;">
                                <?php echo $this->lang->line('common_amount'); required_mark(); ?> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                            </th>
                            <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th> <!--Discount-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?>  </th> <!--Tax-->
                            <th>Tax Amount </th> <!--Tax-->
                            <th><?php echo $this->lang->line('common_comment'); ?>  </th><!--Comment-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more('item_add_table')"> <i class="fa fa-plus"></i> </button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control search input-mini f_search" name="search[]"
                                       id="f_search_1"
                                       placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_id'); ?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_description'); ?> ..."
                                       onkeydown="remove_item_all_description(event,this)"><!--Item Id-->
                                <!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                <input type="hidden" class="form-control mainCategory" name="mainCategory[]">
                            </td>
                            <td>
                                <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop_active(), '', 'class="form-control select2 input-mini wareHouseAutoID" onchange="checkitemavailable(this)" required'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_item">
                                        <select name="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                </td>
                                <td>
                                    <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                </td>
                            <?php } ?>
                            <td>
                                <input class="hidden conversionRate_DO" id="conversionRate_DO" name="conversionRate_DO">
                                <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini" onchange="convertPrice_DO(this)" required'); ?>
                            </td>

                            <td>
                                <div class="input-group">
                                    <input type="text" name="currentstock[]" class="form-control currentstock" required disabled>
                                    <input type="hidden" name="currentstock_pulleddocument[]" class="form-control currentstock_pulleddocument" required disabled>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="parkQty[]" class="form-control parkQty" required readonly> 
                                </div>
                            </td>
                            <td>
                                <input type="text" onfocus="this.select();" name="quantityRequested[]" onkeyup="checkCurrentStock(this)" onchange="load_line_tax_amount(this);checkCurrentStock_unapproveddocument(this)"
                                        placeholder="0.00"  class="form-control number input-mini quantityRequested" required>
                            </td>

                            <td>
                                <div class="input-group">
                                    <input type="text" onfocus="this.select();" name="estimatedAmount[]" onkeyup="change_amount(this)" placeholder="0.00" onchange="load_line_tax_amount(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)" class="form-control number estimatedAmount input-mini">
                                </div>
                            </td>
                            <td style="width: 65px;">
                                <input class="hidden promotionID" name="promotionID[]" id="promotionID">
                                <div class="input-group">
                                    <input type="text" name="discount[]" placeholder="0.00" value="0" onkeyup="cal_discount(this)" onchange="load_line_tax_amount(this)"
                                           onfocus="this.select();" class="form-control number discount">
                                    <span class="input-group-addon" style="padding: 5px 3px">%</span>
                                </div>
                            </td>
                            <td style="width: 60px;">
                                <input type="text" name="discount_amount[]" placeholder="0.00" value="0" onkeyup="cal_discount_amount(this)" onchange="load_line_tax_amount(this)"
                                       onfocus="this.select();" class="form-control number discount_amount">
                            </td>
                            <td style="width: 120px">
                                <?php $taxDrop = ($group_based_tax == 1 ? array(''=>'Select Tax Types'): all_tax_drop(1));
                                echo form_dropdown('item_text[]', $taxDrop, '', 'class="form-control item_text input-mini" onchange="load_line_tax_amount(this), select_text_item(this)"'); ?>
                            </td>
        
                            <?php if($group_based_tax == 1) { ?>
                                <td><span class="linetaxamnt pull-right" style="width: 72px;">0</span></td>
                            <?php } else { ?>
                                <td style="width: 72px;">
                                    <div class="input-group">
                                        <input type="text" name="item_taxPercentage[]" placeholder="0.00" onfocus="this.select();"
                                            class="form-control number item_taxPercentage input-mini" value="0" readonly>
                                        <span class="input-group-addon input-group-addon-mini" style="padding: 5px 3px">%</span>
                                    </div>
                                </td>
                            <?php } ?>
                            <td>
                                <textarea class="form-control input-mini text-area-style" rows="1" name="remarks[]"
                                          placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_remarks'); ?>..."></textarea>
                                <!--Item Remarks-->
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"> </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="add_direct_delivery_order_items()"><?php echo $this->lang->line('common_save_change'); ?>
                    </button><!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="edit_invoice_item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 99% !important;">
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
                            <th><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th><?php echo $this->lang->line('common_warehouse'); ?><?php required_mark(); ?></th>
                            <!--Warehouse-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?></th><!--Project-->
                                <th><?php echo $this->lang->line('common_project_category'); ?><!-- Project Category --></th>
                                <th><?php echo $this->lang->line('common_project_subcategory'); ?></th>
                            <?php } ?>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width:100px;">Current Stock<?php required_mark(); ?></th>
                            <th style="width:100px;">Park Qty<?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?><?php required_mark(); ?></th>
                            <!--Qty-->
                            <?php 
                            if($show_price_delivery_order == 1 && $invoiceType != 'Direct'){ ?>
                            <th style="width: 120px;"><?php echo $this->lang->line('common_amount'); ?> <?php required_mark(); ?>
                                <span
                                    class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!--Amount-->
                            <th colspan="2"
                                class="directdiscount"><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th>
                            <!--Discount-->
                            <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th>
                            <!--Tax-->
                            <th><?php echo $this->lang->line('common_comment'); ?> </th><!--Comment-->
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control input-mini" name="search"
                                       placeholder="Item ID, Item Description..." id="search"
                                       onkeydown="remove_item_all_description_edit(event)">
                                <input type="hidden" class="form-control" id="edit_itemAutoID" name="itemAutoID">
                                <input type="hidden" class="form-control" id="invoicecat" name="invoicecat">
                            </td>
                            <td>
                                <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop_active(), '', 'class="form-control select2 input-mini" id="edit_wareHouseAutoID" onchange="editstockwarehousestock(this), fetch_discount_setup_edit(this)"  required'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item">
                                        <select name="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('sales_markating_transaction_select_project'); ?> </option>
                                            <!--Select Project-->
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
                            <td>
                                <input class="hidden conversionRate_DOEdit" id="conversionRate_DOEdit" name="conversionRate_DOEdit">
                                <?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control input-mini" id="edit_UnitOfMeasureID" onchange="convertPrice_DOedit(this)" required'); ?>
                            </td>

                            <td>
                                <div class="input-group">
                                    <input type="hidden" name="mainCategoryhn"
                                           id="mainCategoryhn"
                                           class="form-control">
                                    <input type="text" name="currentstock_edit"
                                           id="currentstock_edit"
                                           class="form-control" required disabled>
                                    <input type="hidden" name="currentstock_pulleddocument_edit"
                                           id="currentstock_pulleddocument_edit"
                                           class="form-control">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="parkQty_edit"
                                           id="parkQty_edit"
                                           class="form-control parkQty" required readonly>
                                </div>
                            </td>

                            <td><input type="text" name="quantityRequested" placeholder="0.00"
                                       class="form-control number input-mini" onfocus="this.select();"
                                       id="edit_quantityRequested" onkeyup="checkCurrentStockEdit(this), validateQty_edit()"
                                       onchange="checkCurrentStockpulleddocEdit(this), load_line_tax_amount_edit(this)"
                                       required>
                                <input type="hidden" class="form-control" id="edit_isDeliveredQtyUpdated" name="isDeliveredQtyUpdated">
                                <input class="hidden" id="contractBalance" name="contractBalance">
                                <input class="hidden" id="contractItem" name="contractItem">
                                <input type="hidden" class="form-control" id="edit_deliveredQty" name="deliveredQty">
                            </td>
                            <?php 
                            if($show_price_delivery_order == 1 && $invoiceType != 'Direct'){
                                $ele = '';
                            }else {
                                $ele = 'hide-elements-td';
                            } ?>
                            <td class="<?php echo $ele; ?>">
                                <div class="input-group">
                                    <input type="text" name="estimatedAmount" placeholder="<?php echo $placeholder ?>"
                                           class="form-control number input-mini milee" onfocus="this.select();" onchange="load_line_tax_amount_edit(this)"
                                           onkeyup="edit_change_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" id="edit_estimatedAmount">
                                </div>
                            </td>
                            <td style="width: 100px;" class="directdiscount <?php echo $ele; ?>">
                                <input class="hidden" name="promotionID" id="edit_promotionID">
                                <div class="input-group">
                                    <input type="text" name="discount" placeholder="0.00" value="0"
                                           id="edit_discount" onfocus="this.select();" onchange="load_line_tax_amount_edit(this)"
                                           onkeyup="edit_cal_discount(this.value)" class="form-control number">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 100px;" class="directdiscount <?php echo $ele; ?>">
                                <input type="text" name="discount_amount" id="edit_discount_amount" placeholder="<?php echo $placeholder ?>"
                                       placeholder="<?php echo $placeholder ?>" onchange="load_line_tax_amount_edit(this)"
                                       onkeyup="edit_cal_discount_amount()" onfocus="this.select();" value="0"
                                       class="form-control number">
                            </td>                            
                            <td class="<?php echo $ele; ?>">
                                <?php $taxDrop = ($group_based_tax == 1 ? array(''=>'Select Tax Types'): all_tax_drop(1));
                                echo form_dropdown('item_text', all_tax_drop(1), '', 'class="form-control item_text input-mini" id="edit_item_text" onchange="load_line_tax_amount_edit(this), select_text_item(this)"'); ?>
                            </td>

                                <?php if($group_based_tax == 1) { ?>
                                    <td class="<?php echo $ele; ?>"><span class="pull-right linetaxamnt_edit <?php echo $ele; ?>" id="linetaxamnt_edit" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <?php } else { ?>
                                    <td class="<?php echo $ele; ?>" style="width: 120px">
                                        <div class="input-group">
                                            <input type="text" name="item_taxPercentage" id="edit_item_taxPercentage"
                                                placeholder="0.00" onfocus="this.select();"
                                                class="form-control number item_taxPercentage input-mini" value="0" readonly>
                                            <span class="input-group-addon input-group-addon-mini">%</span>
                                        </div>
                                    </td>
                                <?php } ?>
                                <td class="<?php echo $ele; ?>">
                                    <textarea class="form-control input-mini text-area-style" rows="1" name="remarks" id="edit_remarks" style="width: 115px"
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

<div aria-hidden="true" role="dialog" id="gl-update-model" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_gl_account'); ?> </h5>
            </div>
            <div class="modal-body" id="gl-change-container">
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?>  </button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="customer_order_GLUpdate(1)"><?php echo $this->lang->line('sales_markating_transaction_apply_to_all'); ?>
                </button><!--Apply to All-->
                <button class="btn btn-primary" type="button"
                        onclick="customer_order_GLUpdate(0)"><?php echo $this->lang->line('common_save_change'); ?>
                </button><!--Save changes-->
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
    var projectcategory;
    var projectsubcat;
    var select_VAT_value = '';
                            
    var defaultSegment = '<?php echo $master['segmentID'] ?>|<?php echo $master['segmentCode'] ?>';
    var no_records_found = '<?php echo $this->lang->line('common_no_records_found');?>';
    var current_trigger_event = null;
    var isGroupWiseTax = <?php echo json_encode(trim($group_based_tax)); ?>;
    var show_price_delivery_order = '<?php echo $show_price_delivery_order; ?>';
    var invoiceType = '<?php echo $invoiceType; ?>';

    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.select2').select2();
        invoiceDetailsAutoID = null;
        projectID = null;
        projectcategory = null;
        projectsubcat = null;
                            
        invoiceAutoID = <?php echo json_encode($invoiceAutoID); ?>;
        invoiceType = <?php echo json_encode($invoiceType); ?>;
        customerID = <?php echo json_encode(trim($master['customerID'] ?? '')); ?>;
        currencyID = <?php echo json_encode(trim($master['transactionCurrency'] ?? '')); ?>;
        currency_decimal = <?php echo json_encode(trim($master['transactionCurrencyDecimalPlaces'] ?? '')); ?>;
        tax_total = 0;

        if(show_price_delivery_order == 1 && invoiceType != 'Direct'){
            ele = '';
        }else {
            ele = 'hide-elements-td';
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
                tax_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_tax_amount_required');?>.'}}}, /*Tax Amount is required*/
                text_type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_tax_type_required');?>.'}}}, /*Tax Type is required*/
                percentage: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_percentage_is_required');?>.'}}}/*Percentage is required*/
            },
        })
         .on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'masterID', 'value': invoiceAutoID});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Delivery_order/save_order_tax_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    InvoiceDetailAutoID = null;
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        setTimeout(function () {
                            fetch_invoice_direct_details();
                        }, 300);
                    }
                }, error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        });
    });

    function invoice_con_modal(title, tab) {
        $('.pull-li').removeClass('pulling-based-li');
        tabID = tab;
        if (invoiceAutoID) {
            $('.invoice_con_title').html(title);
            $('#table_body').empty().append('<tr class="danger"><td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td></tr>');
            $("#invoice_con_base_modal").modal({backdrop: "static"});
        }
    }

    function invoice_item_detail_modal(tab) {
        tabID = tab;
        if (invoiceAutoID) {
            current_trigger_event = 'item';
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
            current_trigger_event = 'income';

            $('#invoice_detail_form')[0].reset();
            $('#income_add_table tbody tr').not(':first').remove();
            $('.gl_code').val('').change();
            $('.segment_glAdd').val(defaultSegment).change();
            $("#invoice_detail_modal").modal({backdrop: "static"});
        }
    }

    function select_text(data, total_amount) {
        //$('#tax_form').bootstrapValidator('resetForm', true);
        if (data.value != 0) {
            var result = $('#text_type option:selected').text().split('|');
            $('#percentage').val(parseFloat(result[2]));
            cal_tax(parseFloat(result[2]), total_amount);
            $('#tax_form').bootstrapValidator('revalidateField', 'percentage');
        }
    }

    function fetch_con_detail_table(contractAutoID) {
        if (contractAutoID) {
            $('.pull-li').removeClass('pulling-based-li');
            $('#pull-'+contractAutoID).addClass('pulling-based-li');

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
                    $('#table_body, #table_tfoot').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $("#invoiceType").prop("disabled", false);
                        $("#customerID").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);
                        currencyID = '';
                        $('#table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b>'+no_records_found+'</b></td></tr>');
                        <!--No Records Found-->
                    }
                    else {
                        var str = '';
                        $("#invoiceType").prop("disabled", true);
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        tot_amount = 0;
                        receivedQty = 0;

                        $.each(data['detail'], function (key, value) {
                            if (value['receivedQty'] != null && value['receivedQty'] !== undefined) {
                                receivedQty = value['receivedQty'];
                            }
                            //var balQty = (value['requestedQty'] - value['receivedQty']);
                            var balQty = value['balance'];
                            balQty = (balQty > 0) ? balQty : 0;
                            var warehouselocation = '';

                            if (x == 1) {
                                warehouselocation = '<select class="whre_drop" style="width: 110px;"  id="whre_' + value['contractDetailsAutoID'] + '" onchange="getWareHouseQty('+ value['contractDetailsAutoID'] +')"><option value="">Select WareHouse</option></select> <button class="btn btn-xs btn-default" type="button" onclick="applytoall('+value['contractDetailsAutoID']+', this)">\n' +
                                    '                            <i class="fa fa-arrow-circle-down arrowDown"></i>\n' +
                                    '                     </button>';
                            } else
                            {
                                warehouselocation = '<select class="whre_drop" style="width: 110px;"  id="whre_' + value['contractDetailsAutoID'] + '" onchange="getWareHouseQty('+ value['contractDetailsAutoID'] +')"><option value="">Select WareHouse</option></select>';
                            }


                            // if (balQty > 0) {
                            str = '<tr><td>' + x + '</td><td><input class="hidden" id="itemAutoID_' + value['contractDetailsAutoID'] + '" value="' + value['itemAutoID'] + '">' + value['itemSystemCode'] + ' - ' + value['itemSecondaryCode'] + ' - ' + value['itemDescription'] + '<br><b> UOM - </b>' + value['unitOfMeasure'] + '<br><b> Part No - </b>' + value['partNo'] +'</td>';
                            str += '<td class="text-center " style="width: 13%;">'+warehouselocation+' <br/>Current Stock : <span id="wareHouseQty_' + value['contractDetailsAutoID'] + '">0</span> <br/>Park Qty : <span id="parkQty_' + value['contractDetailsAutoID'] + '">0</span> </td>';
                            <?php  if($openContractPolicy != 1) {?>
                                str += '<td class="text-right">' + balQty + '</td>';
                            <?php  } ?>

                            if(value['isBackToBack'] == 1){
                                value['discountAmount'] = value['discountAmount'] / value['requestedQty'];
                                str += '<td class="text-right '+ele+'">' + (parseFloat(value['unittransactionAmount'])).formatMoney(currency_decimal, '.', ',') + '</td>';
                                str += '<td class="text-right '+ele+'">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>';
                            }else{
                                str += '<td class="text-right">' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td>';
                                str += '<td class="text-right">' + (value['discountAmount']) + '</td>';
                            }
                            
                            
                            
                         /* <?php if($group_based_tax == 1){?>
                                str += '<td class="text-right"><p id="totTaxCal_'+value['contractDetailsAutoID'] + '"></p><input class="hidden" id="taxCalculationFormulaID_' + value['contractDetailsAutoID'] + '" value="' + value['taxCalculationformulaID'] + '"></td>';
                            <?php } ?>*/
                           
                            <?php  if($qty_validate == 1) {?>
                                str += '<td class="text-center"><i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' +value['contractDetailsAutoID']+ ',' + balQty + ',\''+ value['invmaincat']+'\')" aria-hidden="true"></i> <input type="text" class="number" size="8" id="qty_' + value['contractDetailsAutoID'] + '" onkeyup="validate_Qty(' + value['contractDetailsAutoID'] + ',this.value, ' + balQty + ',\''+ value['invmaincat']+'\')" ></td>';
                            <?php  } else {?>
                                str += '<td class="text-center"><i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty2(' +value['contractDetailsAutoID']+ ',' + balQty + ',\''+ value['invmaincat']+'\')" aria-hidden="true"></i> <input type="text" class="number" size="8" id="qty_' + value['contractDetailsAutoID'] + '" onkeyup="validate_with_wareHouse_qty(' + value['contractDetailsAutoID'] + ',\''+ value['invmaincat']+'\')" onchange="select_check_box(' + value['contractDetailsAutoID'] + ')" ></td>';
                            <?php  } ?>

                            
                            
                           
                                if(value['isBackToBack'] == 1){
                                    <?php  if($costChange_validate == 1) {?>
                                        str += '<td class="text-center '+ele+'"><input type="text" class="number" size="10" onkeypress="return validateFloatKeyPress(this,event)" value="' + (parseFloat(value['unittransactionAmount'])) + '" id="amount_' + value['contractDetailsAutoID'] + '" onkeyup="select_amount(' + value['contractDetailsAutoID'] + ',this.value)"></td>';
                                    <?php  } else {?>
                                        str += '<td class="text-center '+ele+'"><input type="text" class="number" size="10" onkeypress="return validateFloatKeyPress(this,event)" value="' + (parseFloat(value['unittransactionAmount'])) + '" id="amount_' + value['contractDetailsAutoID'] + '" onkeyup="select_amount(' + value['contractDetailsAutoID'] + ',this.value)" disabled></td>';
                                    <?php  } ?>
                                } else {
                                    <?php  if($costChange_validate == 1) {?>
                                        str += '<td class="text-center '+ele+'"><input type="text" class="number" size="10" onkeypress="return validateFloatKeyPress(this,event)" value="' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])) + '" id="amount_' + value['contractDetailsAutoID'] + '" onkeyup="select_amount(' + value['contractDetailsAutoID'] + ',this.value)"></td>';
                                    <?php  } else {?>
                                        str += '<td class="text-center '+ele+'"><input type="text" class="number" size="10" onkeypress="return validateFloatKeyPress(this,event)" value="' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])) + '" id="amount_' + value['contractDetailsAutoID'] + '" onkeyup="select_amount(' + value['contractDetailsAutoID'] + ',this.value)" disabled></td>';
                                    <?php  } ?>
                                }
                           
                        
                            
                          
                                str += '<td class="text-center '+ele+'"><input type="text" class="number" size="10" onkeypress="return validateFloatKeyPress(this,event)" ';
                                str += 'value="' + parseFloat(value['discountAmount']).toFixed(currency_decimal) + '" id="discount_' + value['contractDetailsAutoID'] + '" onkeyup="cal_tot_amount(' + value['contractDetailsAutoID'] + ',this.value)"></td>';
                           
                           
                           
                            <?php if($group_based_tax == 1){?>
                                str += '<td class="text-center '+ele+'">'+value['Description']+'</td>';
                                str += '<td class="text-right '+ele+'"><p id="totTaxCal_'+value['contractDetailsAutoID'] + '"></p><input class="hidden" id="taxCalculationFormulaID_' + value['contractDetailsAutoID'] + '" value="' + value['taxCalculationformulaID'] + '"></td>';
                            <?php }else{?>
                                str += '<td class="text-center '+ele+'"><select class="tax_drop" id="tax_drop_' + value['contractDetailsAutoID'] + '" name="tex_type[]" onchange="cal_con_base_tax(' + value['contractDetailsAutoID'] + ',this)">';
                                str += '<option value="">Select Tax</option></select><div class="input-group"> <input name="item_taxPercentage[]" onkeyup="change_tax_per(' + value['contractDetailsAutoID'] + ',this.value)" ';
                                str += 'id="item_taxPercentage_' + value['contractDetailsAutoID'] + '" placeholder="0.00" class="form-control number item_taxPercentage input-mini" value="0" style="width:60px;" ';
                                str += 'readonly="" autocomplete="off" type="text"><span class="input-group-addon input-group-addon-mini">%</span><input class="form-control number" onkeypress="return validateFloatKeyPress(this,event)"  id="tax_amount_' + value['contractDetailsAutoID'] + '" ';
                                str += 'name="tax_amount[]" style="width: 50px;" onkeyup="change_tax_amount(' + value['contractDetailsAutoID'] + ',this.value)" autocomplete="off" type="text" value="0" readonly=""></div></td>';
                           <?php } ?>
                           
                            
                                str += '<td class="text-right '+ele+'" id="tot_' + value['contractDetailsAutoID'] + '">0</td><td class="'+ele+'"><input placeholder="Remarks" type="text" size="13" id="remarks_' + value['contractDetailsAutoID'] + '"></td>';
                            
                            str += '<td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['contractDetailsAutoID'] + '" type="checkbox" value="' + value['contractDetailsAutoID'] + '"></td></tr>';

                            $('#table_body').append(str);
                            x++;

                            tot_amount += (parseFloat(value['totalAmount']));
                            // }
                        });

                        if(x == 1){
                            $('#table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b>'+no_records_found+'</b></td></tr>');
                        }

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
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
    }

    function getWareHouseQty(contractDetID){
        let wareHouse = $('#whre_'+contractDetID).val();
        $('#wareHouseQty_'+contractDetID).text(0);
        $('#qty_'+contractDetID).val('');
        $('#parkQty_'+contractDetID).text(0);
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
                    getParkQty(contractDetID,wareHouse);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
    }

    function validate_Qty(id,Qty, balanceQty,invmaincat) {
        var Cont_policy = <?php echo $open_contract; ?>;
        validate_with_wareHouse_qty(id,invmaincat);
        if(Cont_policy != 1) {
            if(balanceQty < Qty) {
                $('#qty_' + id).val('');
                myAlert('w', 'Qty cannot be greater than balance Qty');
            }
        }
        select_check_box(id);
    }

    function validate_with_wareHouse_qty(id,invmaincat){
        if(invmaincat =='Inventory')
        {
            let qty = $('#qty_' + id).val();
            let wareHouseQty = $('#wareHouseQty_'+id).text();
            let parkQty = $('#parkQty_'+id).text();

            qty = (qty === '')? 0: qty;
            parkQty = (parkQty === '')? 0: parkQty;
            if(parseFloat(qty) > (parseFloat(wareHouseQty) - parseFloat(parkQty))){
                myAlert('w', 'Qty Can not be greater than available qty');
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
        var taxCalculationFormulaID = $('#taxCalculationFormulaID_'+ id).val();
        var tex_type = $('#tax_drop_' + id).val();
        $("#check_" + id).prop("checked", false);

        if(isGroupWiseTax == 1) {
            tax_amount = linewiseTax(id,taxCalculationFormulaID,qty,amount,discount);
            tex_type = taxCalculationFormulaID;
        }
        if(!tax_amount) {
            tax_amount = 0;
        }

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

    function linewiseTax(id,taxCalculationFormulaID,qty,amount,discount){ 

          var total = qty * amount;
          var calcualted_total = (amount - discount) * qty;

          $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'documentMasterID': invoiceAutoID, 'documentID': 'DO', 'taxCalculationFormulaID': taxCalculationFormulaID,'total':total,'discount':discount},
            url: "<?php echo site_url('Invoices/fetch_lineWiseTax'); ?>",
            success: function (data) {
                refreshNotifications();
                $('#totTaxCal_'+id).text(data);
                $('#tot_'+id).text(((parseFloat(calcualted_total))+parseFloat(data)).toFixed(currency_decimal));

               
           
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
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
        var taxCalculationFormulaID = $('#taxCalculationFormulaID_'+ id).val();
        $("#check_" + id).prop("checked", false);
       
        if(isGroupWiseTax == 1) {
            tax_amount = linewiseTax(id,taxCalculationFormulaID,qty,amount,discount);
            $("#check_" + id).prop("checked", true);
        }else { 
            if (qty >  0 && amount > 0 || tex_type > 0 ) {
                $("#check_" + id).prop("checked", true);
            }
            if (discount == '') {

                var total = parseFloat(qty) * (parseFloat(amount) + parseFloat(tax_amount));
                var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', '');
                $('#tot_' + id).text(totalnew);
            }
            else {
              
                var total = ((parseFloat(amount) - parseFloat(discount)) * parseFloat(qty) + parseFloat(tax_amount));
                var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', '');
                $('#tot_' + id).text(totalnew);
            }
        }
    }

    function change_tax_per(id, percentage) {
        if (percentage > 0) {
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

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Receipt_voucher/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    $('#f_search_' + id).closest('tr').find('.mainCategory').val(suggestion.mainCategory);
                }, 200);


                fetch_sales_price(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                if ($('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val()) {
                    fetch_rv_warehouse_item(suggestion.itemAutoID, this, $('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val());
                }

                $(this).closest('tr').find('.quantityRequested').focus();
                $(this).closest('tr').css("background-color", 'white');
             //   $(this).closest('tr').find('.wareHouseAutoID').val('').change();
                checkitemavailable(this, suggestion.itemAutoID);
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
               // check_item_not_approved_document_wise(suggestion.itemAutoID,'add','DO',id);
               fetch_line_tax_and_vat(suggestion.itemAutoID, this);
               check_item_not_approved_in_document(suggestion.itemAutoID,id,''); 
            //    if((<?php echo $promotionPolicy;?>) && <?php echo $promotionPolicy;?> == 1) {
            //         fetch_discount_setup(this, suggestion.itemAutoID);
            //     }
               
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');
    }


    function initializeitemTypeahead_edit() {

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Receipt_voucher/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
            onSelect: function (suggestion) {

                setTimeout(function () {
                    $('#edit_itemAutoID').val(suggestion.itemAutoID);
                    $('#invoicecat').val(suggestion.mainCategory);
                    $('#edit_quantityRequested').val(0);
                    
                }, 200);

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
                    $('#edit_wareHouseAutoID').attr('onchange', 'editstockwarehousestock(this), fetch_discount_setup_edit(this)');
                }
                edit_fetch_line_tax_and_vat(suggestion.itemAutoID, this);
                //check_item_not_approved_document_wise(suggestion.itemAutoID,'edit','DO',1);
                check_item_not_approved_in_document(suggestion.itemAutoID,id,'DO'); 
                fetch_discount_setup_edit(this, suggestion.itemAutoID);
            }
        });
    }

    function fetch_invoice_direct_details() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': invoiceAutoID},
            url: "<?php echo site_url('Delivery_order/fetch_direct_delivery_order_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                tax_total = 0;
                var tr_dPlace = 2;                

                $('#gl_table_body,#gl_insurance_table_body,#item_table_body,#item_table_tfoot,#gl_table_tfoot,#gl_insurance_table_tfoot').empty();
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#gl_table_body,#gl_insurance_table_body,#item_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b>'+no_records_found+' </b></td></tr>');
                    /*No Records Found*/
                    $("#invoiceType").prop("disabled", false);
                    $("#customerID").prop("disabled", false);
                    $("#addcustomer").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#editallbtn").addClass("hidden");
                    currencyID = '';
                }
                else {
                    $("#invoiceType").prop("disabled", true);
                    $("#customerID").prop("disabled", true);
                    $("#addcustomer").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#editallbtn").removeClass("hidden");
                    var x = 1;
                    tr_dPlace = data['currency']['transactionCurrencyDecimalPlaces'];
                    var item_trans_amount = 0;
                    var colour = '';

                    $.each(data['detail'], function (key, value) {
                        var wareLocation='';
                        var part_no_remarks ='';
                        var detID = value['DODetailsAutoID'];
                        if (value['type'] == 'Item') {

                            $('#item_table_tfoot').empty();
                            var val = '';
                            if (value['contractCode'] !== null) {
                                val = value['contractCode'] + ' - ';
                            }
                            if (value['wareHouseLocation'] !== null) {
                                wareLocation = value['wareHouseLocation'];
                            }
                            if((value['remarks']!= ' ') && (value['partNo'] == '')) {
                                part_no_remarks = value['remarks'];
                            }
                            else if((value['remarks']!= ' ') && (value['partNo']!= '')) {
                                part_no_remarks = value['remarks'] + ' - Part No : ' + value['partNo'];
                            }
                            else if((value['partNo']!=' ')){
                                part_no_remarks =  ' -  Part No : ' + value['partNo'];
                            }


                            var str = '<tr><td>' + x + '</td>';
                            str += '<td>' + value['itemSystemCode'] + '</td>';

                            if (data['currency']['DOType'] == "Direct") {
                                str +=  '<td>' + val + value['itemDescription'] + '-' + wareLocation + ' - ' + part_no_remarks  + '</td>';
                            }
                            else{
                                str += '<td>' + val + value['itemDescription'] +  '-' + wareLocation + ' - ' + value['remarks'] + '</td>';
                            }

                            str += '<td>'+value['unitOfMeasure'] + '</td>';
                            str += '<td class="text-center">' +  commaSeparateNumber(value['requestedQty'],2) + '</td>';
                            
                            if(show_price_delivery_order == 1 && invoiceType != 'Direct'){
                                str += '<td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(tr_dPlace, '.', ',')+'</td>';
                                str += '<td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(tr_dPlace, '.', ',') + '(' + parseFloat(value['discountPercentage']).toFixed(2) + '%)</td>';
                                str += '<td class="text-right">' + parseFloat(value['unittransactionAmount'] - value['discountAmount']).formatMoney(tr_dPlace, '.', ',')+'</td>';
                                str += '<td class="text-right">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount'] - value['discountAmount']))).formatMoney(tr_dPlace, '.', ',') + '</td>';
                            
                                if(isGroupWiseTax == 1) {
                                    if(value['taxAmount'] > 0) {
                                        str += '<td class="text-right"><a onclick="open_tax_dd(\'\',' + invoiceAutoID + ',\'DO\',' + tr_dPlace +', '+ value['DODetailsAutoID'] +', \'srp_erp_deliveryorderdetails\', \'DODetailsAutoID\')">' + parseFloat(value['taxAmount']).formatMoney(tr_dPlace, '.', ',') + '</a></td>';
                                    } else {
                                        str += '<td class="text-right">' + parseFloat(value['taxAmount']).formatMoney(tr_dPlace, '.', ',') + '</td>';
                                    }
                                } else {
                                    str += '<td class="text-right">' + parseFloat((value['requestedQty'] * value['taxAmount'])).formatMoney(tr_dPlace, '.', ',') + '</td>';
                                }
                            
                                str += '<td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(tr_dPlace, '.', ',') + '</td>';
                            }   

                            str += '<td class="text-right" style="width: 120px">';
                            str += '<a onclick="edit_glaccount(' + detID + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a>';
                            if (value['isSubitemExist'] == 1) {
                                str += '&nbsp; | &nbsp;';
                                str += '<a style="' + colour + '" onclick="load_itemMasterSub_config_modal('+detID + ',\'DO\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a>';
                            }
                            str += ' &nbsp; | &nbsp; <a onclick="edit_item('+detID + ');"><span class="glyphicon glyphicon-pencil"></span>';
                            str += '</a> &nbsp; | &nbsp;<a onclick="delete_item_direct('+ detID + ',2);"><span class="glyphicon glyphicon-trash delete-icon"></span></a></td></tr>';


                            $('#item_table_body').append(str);

                            x++;
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            item_trans_amount += (parseFloat(value['transactionAmount']));


                            if (data['currency']['invoiceType'] == "Direct") {
                                $('#item_table_tfoot').append('<tr><td colspan="10" class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?> </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(tr_dPlace, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            } else {
                                $('#item_table_tfoot').append('<tr><td colspan="10" class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?>  </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(tr_dPlace, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            }

                        }
                    });

                    if( $('#item_table_body>tr').length == 0 ){
                        $('#item_table_body').html('<tr class="danger"><td colspan="12" class="text-center"><b>'+no_records_found+' </b></td></tr>');
                    }

                }
                $('#tax_tot').text('<?php echo $this->lang->line('sales_markating_transaction_tax_applicable_amount');?>( ' + parseFloat(tax_total).formatMoney(tr_dPlace, '.', ',') + ' )');
                $('#taxtotalamnt').val(parseFloat(tax_total));
                $('#tax_table_body_recode,#tax_table_footer').empty();

                /*Tax Applicable Amount */
                if (jQuery.isEmptyObject(data['tax_detail'])) {
                    $('#tax_table_body_recode').append('<tr class="danger"><td colspan="7" class="text-center"><b>'+no_records_found+' </b></td></tr>');
                    <!--No Records Found-->
                }
                else {
                    x = 1;
                    t_total = 0;
                    $.each(data['tax_detail'], function (key, value) {
                        var str = '<tr><td>' + x + '</td><td>' + value['taxShortCode'] + '</td><td>' + value['taxDescription'] + '</td><td class="text-right">' + value['taxPercentage'] + '% </td>';
                        str += '<td class="text-right">' + parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total).formatMoney(tr_dPlace, '.', ',') + '</td><td class="text-right">';
                        str += '<a onclick="delete_tax(' + value['taxDetailAutoID'] + ',\'' + value['taxShortCode'] + '\',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash">';
                        str += '</span></a></td></tr>';
                        $('#tax_table_body_recode').append(str);
                        x++;
                        t_total += parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total);
                    });
                    if (t_total > 0) {
                        $('#tax_table_footer').append('<tr><td colspan="4" class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_tax_tot');?> </td><td class="text-right total">' + parseFloat(t_total).formatMoney(tr_dPlace, '.', ',') + '</td><td>&nbsp;</td></tr>');
                    }
                    <!--Tax Total-->
                }
                stopLoad();
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function customer_order_GLUpdate(all) {
        var $form = $('#stock_adjustment_gl_form');
        var data = $form.serializeArray();
        data.push({name: "applyAll", value: all});
        data.push({name: "masterID", value: invoiceAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Delivery_order/customer_order_GLUpdate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                setTimeout(function () {
                    fetch_details(2);
                }, 800);

                $('#gl-update-model').modal('hide');

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
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
                $('#gl-change-container').html(data);
                $('#detailID').val(invoiceDetailsAutoID);
                $('#gl-update-model').modal('show');

                stopLoad();
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function cal_tax_amount(discount_amount, total_amount) {
        var total_amount=$('#taxtotalamnt').val();
        if (total_amount && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / total_amount) * 100).toFixed(0));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_tax(discount, total_amount) {
        var total_amount=$('#taxtotalamnt').val();
        if (total_amount && discount) {
            $('#tax_amount').val(parseFloat((total_amount / 100) * parseFloat(discount)).toFixed(currency_decimal));
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
                        data: {'taxDetailAutoID': id, 'masterID': invoiceAutoID},
                        url: "<?php echo site_url('Delivery_order/delete_tax_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);

                            if(data[0] == 's'){
                                setTimeout(function () {
                                    tab_active(2);
                                }, 300);
                            }

                        }, error: function () {
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            stopLoad();
                        }
                    });
                }
            );
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
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
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
                    data: {'order_det_id': id},
                    url: "<?php echo site_url('Delivery_order/fetch_delivery_order_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        //pv_item_detail_modal();
                        invoiceDetailsAutoID = data['DODetailsAutoID'];
                        projectID = data['projectID'];
                        projectcategory = data['project_categoryID'];
                        projectsubcat = data['project_subCategoryID'];
                        $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                        $('#conversionRate_DOEdit').val(data['conversionRateUOM']);
                        fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                        $('#edit_isDeliveredQtyUpdated').val(data['isDeliveredQtyUpdated']);
                        $('#edit_deliveredQty').val(data['deliveredQty']);
                        $('#edit_quantityRequested').val(data['requestedQty']);
                        $('#contractBalance').val(data['balanceQty']);
                        $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount'])));
                        $('#edit_promotionID').val(data['promotionID']);
                        $('#edit_discount_amount').val(data['discountAmount']);
                        $('#edit_discount').val(data['discountPercentage']);
                        if(data['promotionID'] != '' && data['promotionID'] != 0) {
                            $('#edit_discount_amount').prop('disabled', true);
                            $('#edit_discount').prop('disabled', true);
                        } else {
                            $('#edit_discount_amount').prop('disabled', false);
                            $('#edit_discount').prop('disabled', false);
                        }
                       /* if (data['DOType'] != "Direct") {
                            var validate = document.getElementById("edit_quantityRequested");
                            validate.addEventListener("keyup", function () {
                                validateQty_edit();
                            });
                        }*/

                        $('#edit_itemAutoID').val(data['itemAutoID']);
                        $('#edit_itemDescription').val(data['itemDescription']);
                        $('#edit_wareHouseAutoID').val(data['wareHouseAutoID']).change();
                        $('#edit_remarks').val(data['remarks']);
                        if(data['mainCategory'] == "Service"){
                            $('#currentstock_edit').val(0);

                        }else{
                            // $('#currentstock_edit').val(data.currentStock);
                            var stock = data.currentStock;
                            if(parseFloat(data['conversionRateUOM']) > 0 ) {
                                stock = parseFloat(data.currentStock) * parseFloat(data['conversionRateUOM']);
                            }
                            // alert(stock)
                            $('#currentstock_edit').val(stock);
                        }
                        $('#mainCategoryhn').val(data['mainCategory']);
                        $('#edit_item_text').val(data['taxMasterAutoID']);
                        select_VAT_value = data['taxCalculationformulaID'];
                        $('#edit_item_taxPercentage').val(data['taxPercentage']);
                        if (data['taxPercentage'] != 0) {
                            $('#edit_item_taxPercentage').prop('readonly', false);
                        } else {
                            $('#edit_item_taxPercentage').prop('readonly', true);
                        }
                        if (InvoiceNewType == 'Quotation' || InvoiceNewType == 'Contract' || InvoiceNewType == 'Sales Order') {
                            $('#edit_UnitOfMeasureID').prop("disabled", true);
                            $('#search').prop("disabled", true);
                        }

                        if (InvoiceNewType == 'Quotation' || InvoiceNewType == 'Contract' || InvoiceNewType == 'Sales Order') {
                            $('#contractItem').val(1);
                        } else {
                            $('#contractItem').val(0);
                        }
                        load_segmentBase_projectID_itemEdit(data['segmentID']);
                        edit_fetch_line_tax_and_vat(data['itemAutoID']);
                        $('#linetaxamnt_edit').text(parseFloat(data['taxAmount']).toFixed(currency_decimal));
                        $("#edit_invoice_item_detail_modal").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        stopLoad();
                    }
                });
            });
    }


    function validateQty_edit() {
        var Cont_policy = <?php echo $open_contract; ?>;
        var qty = $('#edit_quantityRequested').val();
        var qtyUpdated = $('#edit_isDeliveredQtyUpdated').val();
        var deliveredQty = $('#edit_deliveredQty').val();
        var invmaincat = $('#invoicecat').val();
        if(qtyUpdated == 1) {
            if(deliveredQty > qty) {
                $('#edit_quantityRequested').val('');
                myAlert('w', 'Qty cannot be less than Delivered Qty');
            }
        }
        validate_with_wareHouse_qty(id,invmaincat);
        if(Cont_policy != 1) {
            var detailType = $('#contractItem').val();
            if(detailType == 1) {
                var contractbalance = $('#contractBalance').val();
                var policy = <?php echo $qty_validate; ?>;
                if (policy == 1) {
                    if (parseFloat(contractbalance) < parseFloat(qty)) {
                        $('#edit_quantityRequested').val('');
                        myAlert('e', 'Qty cannot be greater than Contracted Qty!');
                    }
                }
            }
        }
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
                    url: "<?php echo site_url('Invoices/fetch_customer_invoice_detail'); ?>",
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
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        stopLoad();
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
                    data: {'detail_id': id},
                    url: "<?php echo site_url('Delivery_order/delete_order_item_direct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if(data[0] == 's'){
                            setTimeout(function () {
                                fetch_details(tab);
                                tab_active(tab);
                            }, 300);
                        }
                    }, error: function () {
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        stopLoad();
                    }
                });
            });
    }

    function tab_active(id) {
        $(".nav-tabs a[href='#tab_" + id + "']").tab('show');
        fetch_invoice_direct_details();
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
        var taxCalculationFormulaID = [];
        $('#table_body input:checked').each(function () {
            if ($('#amount_' + $(this).val()).val() == '') {
                swal("Cancelled", "Received cost cannot be blank !", "error");
            } else {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                qty.push($('#qty_' + $(this).val()).val());
                wareHouseAutoID.push($('#whre_' + $(this).val()).val());
                whrehouse.push($('#whre_' + $(this).val() + ' option:selected').text());
                tex_id.push($('#tax_drop_' + $(this).val()).val());
                tex_type.push($('#tax_drop_' + $(this).val() + ' option:selected').text());
                tex_percntage.push($('#item_taxPercentage_' + $(this).val()).val());
                tex_amount.push($('#tax_amount_' + $(this).val()).val());
                remarks.push($('#remarks_' + $(this).val()).val());
                discount.push($('#discount_' + $(this).val()).val());
                taxCalculationFormulaID.push($('#taxCalculationFormulaID_' + $(this).val()).val());
            }
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'DetailsID': selected,
                    'orderAutoID': orderAutoID,
                    'amount': amount,
                    'qty': qty,
                    'wareHouseAutoID': wareHouseAutoID,
                    'whrehouse': whrehouse,
                    'tex_id': tex_id,
                    'tex_type': tex_type,
                    'tex_percntage': tex_percntage,
                    'tex_amount': tex_amount,
                    'remarks': remarks,
                    'discount': discount,
                    'taxCalculationFormulaID': taxCalculationFormulaID
                },
                url: "<?php echo site_url('Delivery_order/save_con_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if(data[0] == 's'){
                        $('#invoice_con_base_modal').modal('hide');

                        setTimeout(function () {
                            fetch_details(tabID);
                        }, 300);
                        setTimeout(function () {
                            tab_active(tabID);
                        }, 1000);

                    }
                }, error: function () {
                    $('#invoice_con_base_modal').modal('hide');
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
    }

    function add_more() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#item_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.discount_amount').val(0);
        appendData.find('.discount').val(0);
        appendData.find('.wareHouseAutoID').val($('#item_add_table tbody tr:first').find('.wareHouseAutoID').val());

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#item_add_table').append(appendData);
        $(".select2").select2();
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        initializeitemTypeahead(search_id);
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function add_direct_delivery_order_items() {
        if (invoiceAutoID < 1) {
            myAlert('e', 'Master id not found');
            return false;
        }

        $('.umoDropdown').prop("disabled", false);
        $('.discount').prop('disabled', false);
        $('.discount_amount').prop('disabled', false);
        var data = $('#invoice_item_detail_form').serializeArray();

        data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
        data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
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

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Delivery_order/add_direct_delivery_order_items'); ?>",
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
                } else {
                    $(element).closest('tr').find('.discount').prop('disabled', true);
                    $(element).closest('tr').find('.discount_amount').prop('disabled', true);
                }
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function Update_Invoice_Item_Detail() {
        var isDeliveredQtyUpdated = $('#edit_isDeliveredQtyUpdated').val();

        $('#edit_UnitOfMeasureID').prop("disabled", false);
        $('#edit_discount').prop('disabled', false);
        $('#edit_discount_amount').prop('disabled', false);
        $('#search').prop("disabled", false);
        var $form = $('#edit_invoice_item_detail_form');
        var data = $form.serializeArray();
        if (invoiceAutoID) {
        if(isDeliveredQtyUpdated == 0)
        {
            data.push({'name': 'updateDeliveredQty', 'value': 1});
            data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
            data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
            data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Delivery_order/update_delivery_order_item_detail'); ?>",
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
                            fetch_details(tabID);
                            $('#edit_invoice_item_detail_modal').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        }, 300);
                    } else {
                        $('#edit_discount').prop('disabled', true);
                        $('#edit_discount_amount').prop('disabled', true);
                    }
                }, error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "You want to update the delivery Amount?", /*You want to edit this record!*/
                    type: "warning", /*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_update');?>", /*Edit*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function(isConfirm){
                    if (isConfirm) {
                            data.push({'name': 'updateDeliveredQty', 'value': 1});
                            data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
                            data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
                            data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
                            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
                            $.ajax({
                                async: true,
                                type: 'post',
                                dataType: 'json',
                                data: data,
                                url: "<?php echo site_url('Delivery_order/update_delivery_order_item_detail'); ?>",
                                beforeSend: function () {
                                    startLoad();
                                },
                                success: function (data) {
                                    stopLoad();
                                    myAlert(data[0], data[1]);
                                    if(data[2] != '') {
                                        myAlert('e', data[2]);
                                    }
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
                                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                                    stopLoad();
                                }
                            });
                    } else {
                        data.push({'name': 'updateDeliveredQty', 'value': 0});
                        data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
                        data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
                        data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
                        data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            url: "<?php echo site_url('Delivery_order/update_delivery_order_item_detail'); ?>",
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
                                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                                stopLoad();
                            }
                        });
                    }
                });
            }
        }
    }

    function remove_item_all_description(e, ths) {
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
            $('#parkQty_edit').val('');

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
                customerAutoID: customerID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_deliveryorder',
                primaryKey: 'DOAutoID'
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price_customerWise'); ?>",
            success: function (data) {
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
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
                customerAutoID: customerID,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_deliveryorder',
                primaryKey: 'DOAutoID'
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price_customerWise'); ?>",
            success: function (data) {
                if (data['status']) {
                    $('#edit_estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
            }
        });
    }

    function validateFloatKeyPress(el, evt) {
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

    var hj = null;
    function cal_discount_amount(element) {
        var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
        hj = element.value;
        var disAmount = element.value;
        if($.isNumeric(disAmount) ){
            if (disAmount > estimatedAmount) {
                myAlert('w', 'Discount amount should be less than or equal to Amount');
                $(element).closest('tr').find('.discount').val(0);
                $(element).val(0)
            } else {
                if (estimatedAmount) {
                    $(element).closest('tr').find('.discount').val(((parseFloat(element.value) / estimatedAmount) * 100).toFixed(currency_decimal))
                }
            }
        }
        else{
            $(element).closest('tr').find('.discount').val('');
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
        if($(element).closest('tr').find('.promotionID').val() == '')
        {
            $(element).closest('tr').find('.discount').val(parseFloat(0));
            $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        } else {
            var discount = $(element).closest('tr').find('.discount').val();
            $(element).closest('tr').find('.discount_amount').val(parseFloat((element.value / 100) * parseFloat(discount)).toFixed(currency_decimal));
        }
    }

    function edit_change_amount(element) {
        if($('edit_promotionID').val() == '')
        {
            $('#edit_discount').val(parseFloat(0));
            $('#edit_discount_amount').val(parseFloat(0));
        } else {
            var discount = $('#edit_discount').val();
            $('#edit_discount_amount').val(parseFloat((element.value / 100) * parseFloat(discount)).toFixed(currency_decimal));
        }
    }


    function checkitemavailable(det, itemAutoID = '') {
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

        if((<?php echo $promotionPolicy;?>) && <?php echo $promotionPolicy;?> == 1) {
            if(itemAutoID == '') { itemAutoID = itmID; }
            fetch_discount_setup(det, itemAutoID, warehouseid);
        }
        //if (concatarr.length > 1) {
        // if (jQuery.inArray(mainconcat, concatarr) !== -1) {
        //     $(det).closest('tr').find('.f_search').val('');
        //     $(det).closest('tr').find('.itemAutoID').val('');
        //    // $(det).closest('tr').find('.wareHouseAutoID').val('').change();
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
        var qty = $(det).closest('tr').find('.quantityRequested').val();
        var mainCategory = $(det).closest('tr').find('.mainCategory').val();
        if(mainCategory!='Service'){
            if(currentStock == '')
        {
            currentStock = 0;
        }


        if (det.value > parseFloat(currentStock)) {
            myAlert('w', 'Transfer quantity should be less than or equal to current stock');
            $(det).val(0);
        }

        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
        }
       
    }

    function checkCurrentStock_unapproveddocument(det) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        var currentStock_pulled = $(det).closest('tr').find('.currentstock_pulleddocument').val();
        var category = $(det).closest('tr').find('.itemcatergory').val();
        var itemAutoID = $(det).closest('tr').find('.itemAutoID').val();
        var wareHouseAutoID = $(det).closest('tr').find('.wareHouseAutoID').val();
        var UoM =$(det).closest('tr').find('.umoDropdown option:selected').text().split('|');
        var conversionRate =$(det).closest('tr').find('.conversionRate_DO').val();
        if(category !=='Service') {

        if(det.value > parseFloat(currentStock_pulled)){
            document_by_warehouse_qty(itemAutoID,wareHouseAutoID,'',invoiceAutoID,UoM[0],conversionRate,parseFloat(currentStock))
                    $(det).val(0);
        }
        }
    }


    function checkCurrentStockAll(det,mainCategory) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        var parkQty = $(det).closest('tr').find('.parkQty').val();

        if(mainCategory !=='Service') {
            var qty = (det.value === '')? 0: det.value;
            parkQty = (parkQty === '')? 0: parkQty;

         //   if (det.value > parseFloat(currentStock)) {
           if (qty >( parseFloat(currentStock) - parseFloat(parkQty))) {

                    myAlert('w', 'Transfer quantity should be less than or equal to availabla stock');
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
        var mainCategory = $('#invoicecat').val();
        if(currentStock == '')
        {
            currentStock = 0;
        }
       
        if(mainCategory !=='Service'){
            if (parseFloat(TransferQty) > parseFloat(currentStock)) {
                myAlert('w', 'Transfer quantity should be less than or equal to current stock');
                $('#edit_quantityRequested').val(0);
            }
        }

    }



    function checkCurrentStockpulleddocEdit(){ 
        var currentStock = $('#currentstock_edit').val();
        var TransferQty = $('#edit_quantityRequested').val();
        var mainCategory = $('#invoicecat').val();
        var currentStock_pulled = $('#currentstock_pulleddocument_edit').val();
        var itemAutoID = $('#edit_itemAutoID').val();
        var wareHouseAutoID = $('#edit_wareHouseAutoID').val();
        var UoM =$('#edit_UnitOfMeasureID option:selected').text().split('|');
        var conversionRate =$('#conversionRate_DOEdit').val();
        if(mainCategory !=='Service'){

            if (parseFloat(TransferQty) > parseFloat(currentStock_pulled)) {
                document_by_warehouse_qty(itemAutoID,wareHouseAutoID,'DO',invoiceAutoID,UoM[0],conversionRate,parseFloat(currentStock),invoiceDetailsAutoID)
                $('#edit_quantityRequested').val(0);
           
        }


        }
    }

    function fetch_rv_warehouse_item(itemAutoID, element, wareHouseAutoID) {
        if(itemAutoID && wareHouseAutoID)
        {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'wareHouseAutoID': wareHouseAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item_deduct_qty_new'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {

                if(data['error'] == 0){
                    var conversionRate = $(element).closest('tr').find('.conversionRate_DO').val();

                    if(conversionRate !== '') {
                        data['currentStock'] = data['currentStock'] * conversionRate;
                    }
                    if(data['mainCategory']=='Service'){
                        $(element).closest('tr').find('.currentstock').val('');
                        $(element).closest('tr').find('.currentstock_pulleddocument').val('');
                        $(element).closest('tr').find('.parkQty').val('');
                    }else if(data['mainCategory']=='Non Inventory'){
                        $(element).closest('tr').find('.currentstock').val('');
                        $(element).closest('tr').find('.currentstock_pulleddocument').val('');
                        $(element).closest('tr').find('.parkQty').val('');

                    }else{
                        $(element).closest('tr').find('.currentstock').val(data['currentStock']);
                        $(element).closest('tr').find('.currentstock_pulleddocument').val(data['pulledstock']);
                        $(element).closest('tr').find('.parkQty').val(data['parkQty']);

                    }
                }
                else {
                    myAlert('w', data['message']);
                    $(element).typeahead('val', '');
                    $(element).closest('tr').find('.currentstock').val('');
                    $(element).closest('tr').find('.itemAutoID').val('');
                    $(element).closest('tr').find('.f_search').val('');
                    $(element).closest('tr').find('.parkQty').val('');

                }
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
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
                data: {'wareHouseAutoID': wareHouseAutoID, 'itemAutoID': itemAutoID, 'documentAutoID': invoiceAutoID, 'documentID': 'DO','documentDetAutoID':invoiceDetailsAutoID},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item_deduct_qty_new'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        if(data['mainCategory']=='Service'){
                            $('#currentstock_edit').val('');
                            $('#currentstock_pulleddocument_edit').val('');
                            if(data['currentStock']== null)
                            {
                                $('#edit_quantityRequested').val('');
                                $('#currentstock_pulleddocument_edit').val('');
                                $('#parkQty_edit').val('');
                            }
                        }else if(data['mainCategory']=='Non Inventory'){
                            $('#currentstock_edit').val('');
                            $('#currentstock_pulleddocument_edit').val('');
                            $('#parkQty_edit').val('');
                            if(data['currentStock']== null)
                            {
                                $('#edit_quantityRequested').val('');
                                $('#currentstock_pulleddocument_edit').val('');
                                $('#parkQty_edit').val('');
                            }
                        }else{
                            var conversionRate = $('#conversionRate_DOEdit').val();
                            if(parseFloat(conversionRate) > 0 && data['currentStock'] != null) {
                                data['currentStock'] = parseFloat(data['currentStock']) * parseFloat(conversionRate);
                            }
                            $('#currentstock_edit').val(data['currentStock']);
                            $('#currentstock_pulleddocument_edit').val(data['pulledstock']);
                            $('#parkQty_edit').val(data['parkQty']);

                            if(data['currentStock']== null)
                            {
                                $('#edit_quantityRequested').val('');
                                $('#currentstock_pulleddocument_edit').val('');
                                $('#parkQty_edit').val('');

                                
                            }
                            // $('#currentstock_edit').val(data['currentStock']);
                        }

                    } else {
                        $('#currentstock_edit').val('');
                        $('#currentstock_pulleddocument_edit').val('');
                        $('#parkQty_edit').val('');


                    }
                    refreshNotifications(true);
                }, error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
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
            url: "<?php echo site_url('Delivery_order/fetch_direct_delivery_order_all_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var current_stock_all = '';
                var parkQty='';
                $('#edit_item_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b>'+no_records_found+' </b></td></tr>');
                    <!--No Records Found-->
                } else {
                    $.each(data, function (key, value) {

                        if(value['mainCategory'] == "Service"){
                            current_stock_all = 0;
                            parkQty = 0;
                        }else{
                            current_stock_all = value['currentStock'];
                            parkQty = value['parkQty'];
                        }

                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" onchange="convertPrice_DO(this)" required')) ?>';
                        var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop_active(), '', 'class="form-control select2 input-mini wareHouseAutoID"  onchange="checkitemavailable(this)" required')) ?>';
                        var taxfield = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="taxfield_\'+key+\'"'), form_dropdown('item_text[]', all_tax_drop(1), '', 'class="form-control item_text input-mini"  onchange="select_text_item(this)"')) ?>';
                        var detAutoID = value['DODetailsAutoID'];

                        var str = '<tr><td> <input type="text" class="form-control search f_search" name="search[]" id="f_search_' + x + '" placeholder="Item Id,Item Description" ';
                        str += 'value="' + value['itemDescription']+' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)">';
                        str += '<input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]"> <input type="hidden" class="form-control';
                        str += ' invoiceDetailsAutoID" name="invoiceDetailsAutoID[]" value="' + detAutoID + '"> </td><td> ' + wareHouseAutoID + ' </td>';
                        str += ' <td><input class="hidden conversionRate_DO" id="conversionRate_DO" value="' + value['conversionRateUOM'] + '" name="conversionRate_DO"> ' + UOM + ' </td> ';
                        str += '<td> <div class="input-group"> <input type="text" name="currentstock[]" value="' + current_stock_all + '" class="form-control currentstock" required disabled> </div> </td> ';
                        str += '<td> <div class="input-group"> <input type="text" name="parkQty[]" value="' + parkQty + '" class="form-control parkQty" required disabled> </div> </td> ';
                        str += '<td><input type="text" onfocus="this.select();" name="quantityRequested[]" onkeyup="checkCurrentStockAll(this,\'' + value['mainCategory'] + '\')" value="' + value['requestedQty'] + '" ';
                        str += 'placeholder="0.00" class="form-control number input-mini quantityRequested" required></td> <td> <div class="input-group"> <input type="text" onfocus="this.select();" name="estimatedAmount[]"';
                        str += ' value="' + value['unittransactionAmount'] + '" onkeyup="change_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" placeholder="0.00" class="form-control number';
                        str += ' estimatedAmount input-mini"> </div> </td> <td style="width: 65px;"> <div class="input-group"> <input type="text" name="discount[]" placeholder="0.00" onkeyup="cal_discount(this)"';
                        str += ' onfocus="this.select();" value="' + parseFloat(value['discountPercentage']).toFixed(2) + '" class="form-control number discount"> <span class="input-group-addon" style="padding: 5px 3px">%</span></div> </td>';
                        str += '<td style="width: 60px;"> <input type="text" name="discount_amount[]" placeholder="0.00" value="' + value['discountAmount'] + '" onkeyup="cal_discount_amount(this)" onfocus="this.select();" ';
                        str += 'class="form-control number discount_amount"> </td> <td> ' + taxfield + ' </td> <td style="width: 72px"> <div class="input-group"> <input type="text" name="item_taxPercentage[]" placeholder="0.00" ';
                        str += 'onfocus="this.select();" class="form-control number item_taxPercentage input-mini" id="item_taxPercentage_all_' + x + '" value="' + value['taxPercentage'] + '" value="0" readonly> ';
                        str += '<span class="input-group-addon input-group-addon-mini" style="padding: 5px 3px">%</span> </div> </td> <td> <textarea class="text-area-style" ';
                        str += 'rows="1" name="remarks[]" placeholder="Item Remarks...">' + value['remarks'] + '</textarea> </td> <td class="remove-td" style="vertical-align: middle;text-align: center">';
                        str += '<a onclick="delete_Delivery_order_DetailsEdit('+detAutoID+',\'' + 2 + '\',this);"><span class="glyphicon glyphicon-trash delete-icon"></span></a></td></tr>';


                        $('#edit_item_table_body').append(str);
                        $('#ware_' + key).val(value['wareHouseAutoID']).change();
                        $('#taxfield_' + key).val(value['taxMasterAutoID']);
                        if (data['taxPercentage'] != 0) {
                            $('#item_taxPercentage_all_' + key).prop('readonly', false);
                        } else {
                            $('#item_taxPercentage_all_' + key).prop('readonly', true);
                        }
                        fetch_related_uom_id(value['defaultUOMID'], value['unitOfMeasureID'], $('#uom_' + key));
                        initializeitemTypeahead(x);
                        x++;
                    });
                    $('.select2').select2();
                    search_id = x - 1;
                    $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                }

                stopLoad();
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }


    function delete_Delivery_order_DetailsEdit(id, tabID,det) {
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
                      data: {'detail_id': id},
                    url: "<?php echo site_url('Delivery_order/delete_order_item_direct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $(det).closest('tr').remove();
                        if(tabID == 2) {
                            fetch_details(tabID);
                        } else {
                            load_confirmation();
                        }
                        setTimeout(function () {
                            // fetch_details('step2');
                            // tab_active('step3');
                        }, 300);
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function add_more_edit_customer_invoice() {
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

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Delivery_order/update_all_item_details'); ?>",
                beforeSend: function () {
                    startLoad();
                    // $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {
                    invoiceDetailsAutoID = null;
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        load_confirmation();
                        setTimeout(function () {
                            tab_active(2);
                        }, 300);
                        $('#all_item_edit_detail_modal').modal('hide');
                        $('#edit_all_item_detail_form')[0].reset();
                        $('.select2').select2('');
                    }
                },
                error: function () {
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
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
            $(ths).closest('tr').find('.marginAmount').val(margamnt);
        }else if(amount>0 && marginPercentage<=0 && marginAmount>0){
            var percentage = (parseFloat(marginAmount)/parseFloat(amount)) * 100;
            $(ths).closest('tr').find('.totalAmount').val(parseFloat(amount)+parseFloat(marginAmount));
            $(ths).closest('tr').find('.marginPercentage').val(percentage);
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

    if (typeof checkKeyPressed !== 'function'){
        function checkKeyPressed(evt) {
            if (evt.keyCode == 112) {
                evt.preventDefault();

                switch(current_trigger_event){
                    case 'item': add_more('item_add_table'); break;
                }
            }
        }
    }

    window.addEventListener("keydown", checkKeyPressed, false);


    function applytoall(element) {
        var warehouse = $('#whre_'+element).val();
        $('.whre_drop').val(warehouse);
        fetch_warehouse_stock(element, warehouse);
    }

    function fetch_warehouse_stock(element, warehouseAutoID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractDetID': element, 'warehouseAutoID': warehouseAutoID},
            url: "<?php echo site_url('Invoices/getWareHouseItemQty_bulk'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if(data) {
                    $.each(data, function (key, value) {
                        $('#wareHouseQty_'+value['contractDetailsAutoID']).text(value['currentStock'])
                        $('#qty_'+value['contractDetailsAutoID']).val('');
                    });
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function convertPrice_DO(element) {
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
                'tableName': 'srp_erp_deliveryorder',
                'primaryKey': 'DOAutoID',
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
                    $(element).closest('tr').find('.conversionRate_DO').val(data['conversionRate']);
                    $(element).closest('tr').find('.currentstock_pulleddocument').val(data['qty_pulleddoc']);
                    $(element).closest('tr').find('.quantityRequested').val(data['qty_pulleddoc']);
                    $(element).closest('tr').find('.parkQty').val(data['Unapproved_stock']);

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

    function convertPrice_DOedit(element) {
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
                'tableName': 'srp_erp_deliveryorder',
                'primaryKey': 'DOAutoID',
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
                    $(element).closest('tr').find('#conversionRate_DOEdit').val(data['conversionRate']);
                    $(element).closest('tr').find('#currentstock_pulleddocument_edit').val(data['qty_pulleddoc']);
                    $(element).closest('tr').find('#parkQty_edit').val(data['Unapproved_stock']);

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

    function fetch_discount_setup(element, itemAutoID, warehouseID)
    {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID, 'date': $('#invoiceDate').val(), 'warehouseAutoID' : warehouseID},
            url: '<?php echo site_url("Invoices/fetch_discount_setup_percentage"); ?>',
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.discount').prop('disabled', true);
                    $(element).closest('tr').find('.discount_amount').prop('disabled', true);

                    $(element).closest('tr').find('.discount').val(parseFloat(data['discountPercentage']));
                    $(element).closest('tr').find('.promotionID').val(parseFloat(data['promotionID']));
                    var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
                    if (estimatedAmount) {
                        $(element).closest('tr').find('.discount_amount').val(parseFloat((estimatedAmount / 100) * parseFloat(data['discountPercentage'])).toFixed(currency_decimal))
                    }
                } else {
                    $(element).closest('tr').find('.discount').val(parseFloat(0));
                    $(element).closest('tr').find('.promotionID').val('');
                    $(element).closest('tr').find('.discount_amount').val(0);
                    $(element).closest('tr').find('.discount').prop('disabled', false);
                    $(element).closest('tr').find('.discount_amount').prop('disabled', false);
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function fetch_discount_setup_edit(element,itemAutoID = '') {
        if(itemAutoID == ''){ itemAutoID = $('#edit_itemAutoID').val(); }
        var wareHouseAutoID = $('#edit_wareHouseAutoID').val();
        if((<?php echo $promotionPolicy;?>) && <?php echo $promotionPolicy;?> == 1) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID, 'date': $('#invoiceDate').val(), 'warehouseAutoID': wareHouseAutoID},
                url: '<?php echo site_url("Invoices/fetch_discount_setup_percentage"); ?>',
                async: true,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data) {
                        $('#edit_discount').prop('disabled', true);
                        $('#edit_discount_amount').prop('disabled', true);

                        $('#edit_discount').val(parseFloat(data['discountPercentage']));
                        $('#edit_promotionID').val(parseFloat(data['promotionID']));
                        var estimatedAmount = parseFloat($('#edit_estimatedAmount').val());
                        if (estimatedAmount) {
                            $('#edit_discount_amount').val(parseFloat((estimatedAmount / 100) * parseFloat(data['discountPercentage'])).toFixed(currency_decimal))
                        }
                    } else {
                        $('#edit_discount').val(parseFloat(0));
                        $('#edit_promotionID').val('');
                        $('#edit_discount_amount').val(0);
                        $('#edit_discount').prop('disabled', false);
                        $('#edit_discount_amount').prop('disabled', false);
                    }
                    stopLoad();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    stopLoad();
                }
            });
        } else {
            // $('#edit_discount').val(parseFloat(0));
            // $('#edit_promotionID').val('');
            // $('#edit_discount_amount').val(0);
            // $('#edit_discount').prop('disabled', false);
            // $('#edit_discount_amount').prop('disabled', false);
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
        projectID = $(element).closest('tr').find('.projectID').val();
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

    function fetch_line_tax_and_vat(itemAutoID, element)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'DOAutoID': invoiceAutoID,'itemAutoID':itemAutoID},
            url: "<?php echo site_url('Delivery_order/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                if(data['isGroupByTax'] == 1) {
                    $(element).closest('tr').find('.item_text').empty();
                    var mySelect = $(element).parent().closest('tr').find('.item_text');
                    mySelect.append($('<option></option>').val('').html('Select Tax'));
                    if (!jQuery.isEmptyObject(data['dropdown'])) {
                        $.each(data['dropdown'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                        });
                    }
                    if(data['selected_itemTax']!=0){
                        $(element).closest('tr').find('.item_text').val(data['selected_itemTax']).change();
                    }else{

                        $(element).closest('tr').find('.item_text').val(null).change();
                    }
                    change_amount(element);
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_line_tax_amount(ths){
        var qut = $(ths).closest('tr').find('.quantityRequested').val();
        var amount = $(ths).closest('tr').find('.estimatedAmount').val();
        var discoun = $(ths).closest('tr').find('.discount_amount').val();
        var taxtype = $(ths).closest('tr').find('.item_text').val();
        
        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun=0;
        }

        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = (qut * amount);
            discoun = discoun * qut;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'DOAutoID':invoiceAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'discount':discoun},
                url: "<?php echo site_url('Delivery_order/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $(ths).closest('tr').find('.linetaxamnt').text(parseFloat(data).toFixed(currency_decimal));
                
                    // $(ths).closest('tr').find('.net_amount').text((data+lintaxappamnt).toFixed(currency_decimal));
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $(ths).closest('tr').find('.linetaxamnt').text('0');
        }
    }
    
    function edit_fetch_line_tax_and_vat(itemAutoID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'DOAutoID': invoiceAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Delivery_order/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                if(data['isGroupByTax'] == 1) {
                    $('#edit_item_text').empty();
                    var mySelect = $('#edit_item_text');
                    mySelect.append($('<option></option>').val('').html('Select Tax'));
                    if (!jQuery.isEmptyObject(data['dropdown'])) {
                        $.each(data['dropdown'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                        });
                        if (select_VAT_value) {
                            $('#edit_item_text').val(select_VAT_value);
                            // load_line_tax_amount_edit();
                        } else {
                            if(data['selected_itemTax']!=0){
                                $(element).closest('tr').find('.edit_item_text').val(data['selected_itemTax']).change();
                            }else{

                                $(element).closest('tr').find('.edit_item_text').val(null).change();
                            }
                            change_amount(element);
                        }
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function load_line_tax_amount_edit(ths)
    {    
        var qut = $('#edit_quantityRequested').val();
        var amount = $('#edit_estimatedAmount').val();
        var discoun = $('#edit_discount_amount').val();
        var taxtype = $('#edit_item_text').val();
        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun=0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = (qut * amount);
            discoun = discoun * qut;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'DOAutoID':invoiceAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype,'discount':discoun},
                url: "<?php echo site_url('Delivery_order/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $('#linetaxamnt_edit').text(parseFloat(data).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $('#linetaxamnt_edit').text('0');
        }
    }

    function setQty(contractDetailsAutoID, balQty, invmaincat) {
        //var reqQtyId = "#requested_qty_"+purchaseRequestDetailsID;
        var ordQtyId = "#qty_"+contractDetailsAutoID;
        $(ordQtyId).val(balQty);
        var ordQtyReference = $(ordQtyId);
        validate_Qty(contractDetailsAutoID,ordQtyReference.value,balQty,invmaincat);
    }

    function setQty2(contractDetailsAutoID, balQty, invmaincat) {
        //var reqQtyId = "#requested_qty_"+purchaseRequestDetailsID;
        var ordQtyId = "#qty_"+contractDetailsAutoID;
        $(ordQtyId).val(balQty);
        select_check_box(contractDetailsAutoID);
        // var ordQtyReference = $(ordQtyId);
        // validate_Qty(contractDetailsAutoID,ordQtyReference.value,balQty,invmaincat);
    }

    function getParkQty(contractDetID,wareHouse){
        let itemAutoID = $('#itemAutoID_'+contractDetID).val();

        $('#parkQty_'+contractDetID).text(0);
        $('#qty_'+contractDetID).val('');
        if(wareHouse) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID, 'wareHouseID': wareHouse},
                url: "<?php echo site_url('Receipt_voucher/fetch_park_qty'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    let this_stk = 0;
                    if(data[0] === 's'){
                        this_stk = data['parkQty'];
                    }
                    $('#parkQty_'+contractDetID).text(this_stk)
                    $('#qty_'+contractDetID).val('');

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
    }
</script>


<?php
