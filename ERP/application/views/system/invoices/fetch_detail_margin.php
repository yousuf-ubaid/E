<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .boldtab{
       font-weight: bold;
       border-left-color: #ead8d8 !important;
    }
</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$qty_validate = getPolicyValues('VSQ', 'All');
$costChange_validate = getPolicyValues('CSC', 'All');
$openContractPolicy = getPolicyValues('OCE', 'All');
$open_contract = getPolicyValues('OCE', 'All');
$taxEnabled = getPolicyValues('TAX', 'All');

$umo_arr = array('' => 'Select UOM');
$projectExist = project_is_exist();
$transaction_total = 0;
for ($x = 0; $x < count($detail['detail']); $x++) {
    $transaction_total += ($detail['detail'][$x]['transactionAmount'] - $detail['detail'][$x]['totalAfterTax']);
}
switch ($invoiceType) {
    case "Direct":
    case "Manufacturing":?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> </a>
                </li><!--Income-->
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_2"
                                aria-expanded="false"><?php echo $this->lang->line('common_item'); ?> </a></li>
                <!--Item-->
                <li class="pull-left header"><i
                            class="fa fa-hand-o-right"></i><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_direct_invoice_for'); ?>
                    :
                    - <?php echo $master['customerName']; ?></li> <!--Direct Invoice for-->
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_gl_code'); ?></th><!--GL Code-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_add_new_gl_description'); ?> </th>
                            <!--GL Code Description-->
                            <th><?php echo $this->lang->line('common_segment'); ?> </th><!--Segment-->
                            <th>Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Margin %</th>
                            <th>Margin Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Total Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="width: 75px !important;"><button type="button" onclick="invoice_detail_modal(1)"
                                                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i></button></th>
                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <td colspan="9" class="text-center">
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
                <?php if($group_based_tax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <?php if(($taxEnabled == 1) || ($taxEnabled == null) ){ ?>
                                <label for="exampleInputName2"
                                    id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                    ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                    )</label>
                                <!--Tax for-->
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
                                            onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>)">
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
                <?php } ?>
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
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> </a>
                </li><!--Income-->
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_2"
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
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_gl_code'); ?>  </th><!--GL Code-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_gl_code_description'); ?> </th>
                            <!--GL Code Description-->
                            <th><?php echo $this->lang->line('common_segment'); ?> </th><!--Segment-->
                            <th>Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Margin %</th>
                            <th>Margin Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Total Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="width: 75px !important;"><button type="button" onclick="invoice_detail_modal(1)"
                                                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i></button></th>
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
                <div id="tab_2" class="tab-pane">
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
                <?php if($group_based_tax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <?php if($taxEnabled == 1 || ($taxEnabled == null)){ ?>
                                <label for="exampleInputName2"
                                    id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?> </label>
                                <!--Tax for-->
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
                <?php } ?>
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
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> </a>
                </li><!--Income-->
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_2"
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
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_gl_code'); ?> </th><!--GL Code-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_gl_code_description'); ?> </th>
                            <!--GL Code Description-->
                            <th><?php echo $this->lang->line('common_segment'); ?>  </th><!--Segment-->
                            <th>Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Margin %</th>
                            <th>Margin Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Total Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="width: 75px !important;"><button type="button" onclick="invoice_detail_modal(1)"
                                                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i></button></th>
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
                <div id="tab_2" class="tab-pane">
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
                <?php if($group_based_tax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <?php if($taxEnabled == 1 || ($taxEnabled == null)){ ?>
                                <label for="exampleInputName2"
                                    id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for'); ?>
                                    ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                                    )</label>
                                <!--Tax for-->
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
                <?php } ?>
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
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1"
                                      aria-expanded="false"><?php echo $this->lang->line('sales_markating_transaction_income'); ?> </a>
                </li><!--Income-->
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_2"
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
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_gl_code'); ?> </th><!--GL Code-->
                            <th><?php echo $this->lang->line('sales_markating_transaction_gl_code_description'); ?> </th>
                            <!--GL Code Description-->
                            <th><?php echo $this->lang->line('common_segment'); ?></th><!--Segment-->
                            <th>Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Margin %</th>
                            <th>Margin Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th>Total Amount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>

                            <th style="width: 75px !important;"><button type="button" onclick="invoice_detail_modal(1)"
                                                                        class="btn btn-primary pull-right btn-xs" data-toggle="tooltip"
                                                                        data-placement="left" title="Add GL"><i class="fa fa-plus"></i></button></th>

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
                <div id="tab_2" class="tab-pane">
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
                            <th class="hideTaxpolicy"><?php echo $this->lang->line('common_total'); ?> </th><!--Total-->
                            <th class="hideTaxpolicy"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
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
                                            onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total; ?>);" onkeypress="return validateFloatKeyPress(this,event)">

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
                <?php } ?>
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
                                            echo '<li><a onclick="fetch_con_detail_table(' . $customer_con[$i]['contractAutoID'] . ')">' . $customer_con[$i]['contractCode'] . ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>';
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
                                    <span
                                            class="currency"> (<?php echo $master['transactionCurrency'] ?>)</span></th>
                                <!--Invoiced Item -->
                            <tr>
                            <tr>
                                <th>#</th>
                                <th class="text-left"><?php echo $this->lang->line('common_description'); ?> </th>
                                <!--Description-->
                                <th><?php echo $this->lang->line('common_warehouse'); ?> </th><!--Ware House-->
                                <?php if($openContractPolicy != 1) { ?>
                                    <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?> </th><!--Qty-->
                                 <?php } ?>
                                <th><?php echo $this->lang->line('common_amount'); ?> </th><!--Amount-->
                                <th>Discount</th>
                                <th><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?></th>
                                <!--Qty-->
                                <th><?php echo $this->lang->line('common_price'); ?> </th><!--Price-->
                                <th>Discount</th>
                                <?php if($taxEnabled == 1 || $taxEnabled == null){ ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th><!--Tax-->
                                <?php } else { ?>
                                    <th>&nbsp;</th>
                                <?php } ?>
                                <th><?php echo $this->lang->line('common_total'); ?>  </th><!--Total-->
                                <th>&nbsp;</th>
                                <th style="display: none;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="table_body">
                            <tr class="danger">
                                <td colspan="12" class="text-center">
                                    <b><?php echo $this->lang->line('common_no_records_found'); ?>  </b></td>
                                <!--No Records Found-->
                            </tr>
                            </tbody>
                            <tfoot id="table_tfoot">

                            </tfoot>
                        </table>
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
    <div class="modal-dialog" style="width: 80%">
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
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th><?php echo $this->lang->line('common_amount'); ?> <span
                                        class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span> <?php required_mark(); ?></th><!--Amount-->
                            <th>Margin %</th>
                            <th>Margin Amount</th>
                            <th>Total Amount</th>
                            <th><?php echo $this->lang->line('common_description'); ?><?php required_mark(); ?></th><!--Description-->
                            <th>
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
                            <td><input type="text" name="amount[]" onkeypress="return validateFloatKeyPress(this,event)" onchange="calculate_total(this,'amount')" value="00" class="form-control m_number number amount"></td>
                            <td><input type="text" name="marginPercentage[]" onchange="calculate_total(this,'marginPercentage')" value="0" class="form-control number marginPercentage"></td>
                            <td><input type="text" name="marginAmount[]" onkeypress="return validateFloatKeyPress(this,event)" onchange="calculate_total(this,'marginAmount')" value="00" class="form-control m_number number marginAmount"></td>
                            <td><input type="text" name="transactionAmount[]"  class="form-control transactionAmount" readonly></td>
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
    <div class="modal-dialog" style="width: 80%">
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
                            <th>Margin %</th>
                            <th>Margin Amount</th>
                            <th>Total Amount</th>
                            <th><?php echo $this->lang->line('common_description'); ?> <?php required_mark(); ?></th><!--Description-->
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
                            <td><input type="text" name="amount" id="edit_amount" onchange="calculate_total(this,'amount')"
                                       onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                       class="form-control m_number number amount"></td>
                            <td><input type="text" name="marginPercentage" id="edit_marginPercentage" onchange="calculate_total(this,'marginPercentage')" class="form-control number marginPercentage"></td>
                            <td><input type="text" name="marginAmount" id="edit_marginAmount" onkeypress="return validateFloatKeyPress(this,event)" onchange="calculate_total(this,'marginAmount')" value="00" class="form-control m_number number marginAmount"></td>
                            <td><input type="text" name="transactionAmount" id="edit_transactionAmount"  class="form-control transactionAmount" readonly></td>
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
                            <th><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th><?php echo $this->lang->line('common_warehouse'); ?><?php required_mark(); ?></th>
                            <!--Warehouse-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?><?php required_mark(); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width:100px;">Current Stock<?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?><?php required_mark(); ?></th>
                            <!--Qty-->
                            <th style="width: 120px;"><?php echo $this->lang->line('common_amount'); ?> <?php required_mark(); ?>
                                <span
                                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!--Amount-->
                            <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th>
                            <!--Discount-->
                            <?php if(($taxEnabled == 1 || $taxEnabled == null)){ ?>
                            <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?>  </th>
                            <!--Tax-->
                            <?php } ?>
                            <th><?php echo $this->lang->line('common_comment'); ?>  </th><!--Comment-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more('item_add_table')"><i
                                            class="fa fa-plus"></i></button>
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
                            </td>
                            <td>
                                <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID" onchange="checkitemavailable(this)" required'); ?>
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
                            <?php } ?>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini" disabled  required'); ?></td>

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
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number estimatedAmount input-mini">
                                </div>
                            </td>
                            <td style="width: 100px;">
                                <div class="input-group">
                                    <input type="text" name="discount[]"  value="0"
                                           onkeyup="cal_discount(this)" onfocus="this.select();"
                                           class="form-control number discount">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 100px;">
                                <input type="text" name="discount_amount[]"  value="0"
                                       onkeyup="cal_discount_amount(this)" onfocus="this.select();"
                                       class="form-control number discount_amount">
                            </td>
                            <?php if($taxEnabled == 1 || $taxEnabled == null){ ?>
                            <td>
                                <?php echo form_dropdown('item_text[]', all_tax_drop(1), '', 'class="form-control item_text input-mini" id="" onchange="select_text_item(this)"'); ?>
                            </td>
                            <td style="width: 120px">
                                <div class="input-group">
                                    <input type="text" name="item_taxPercentage[]" id=""
                                           onfocus="this.select();"
                                           class="form-control number item_taxPercentage input-mini" value="0" readonly>
                                    <span class="input-group-addon input-group-addon-mini">%</span>
                                </div>
                            </td>
                            <?php } ?>
                            <td>
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
                            <th><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th><?php echo $this->lang->line('common_warehouse'); ?><?php required_mark(); ?></th>
                            <!--Warehouse-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width:100px;">Current Stock<?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?><?php required_mark(); ?></th>
                            <!--Qty-->
                            <th style="width: 120px;"><?php echo $this->lang->line('common_amount'); ?> <?php required_mark(); ?>
                                <span
                                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!--Amount-->
                            <th colspan="2"
                                class="directdiscount"><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th>
                            <!--Discount-->
                            <th class="hideTaxpolicy_edit" colspan="2"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?> </th>
                            <!--Tax-->
                            <th><?php echo $this->lang->line('common_comment'); ?> </th><!--Comment-->
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
                            <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control input-mini" id="edit_UnitOfMeasureID" disabled required'); ?></td>

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
                                    <input type="text" name="estimatedAmount"
                                           class="form-control number input-mini" onfocus="this.select();"
                                           onkeyup="edit_change_amount()"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           id="edit_estimatedAmount">
                                </div>
                            </td>
                            <td style="width: 100px;" class="directdiscount">
                                <div class="input-group">
                                    <input type="text" name="discount"  value="0"
                                           id="edit_discount" onfocus="this.select();"
                                           onkeyup="edit_cal_discount(this.value)"
                                           class="form-control number">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 100px;" class="directdiscount">
                                <input type="text" name="discount_amount" id="edit_discount_amount"
                                       onkeyup="edit_cal_discount_amount()" onfocus="this.select();" value="0"
                                       class="form-control number">
                            </td>
                            <td class="hideTaxpolicy_edit">
                                <?php echo form_dropdown('item_text', all_tax_drop(1), '', 'class="form-control item_text input-mini" id="edit_item_text" onchange="select_text_item(this)"'); ?>
                            </td>
                            <td style="width: 120px" class="hideTaxpolicy_edit">
                                <div class="input-group">
                                    <input type="text" name="item_taxPercentage" id="edit_item_taxPercentage"
                                            onfocus="this.select();"
                                           class="form-control number item_taxPercentage input-mini" value="0" readonly>
                                    <span class="input-group-addon input-group-addon-mini">%</span>
                                </div>
                            </td>
                            <td>
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
    var taxYN = '<?php echo $taxEnabled?>';
    //var defaultSegment = <?php //echo json_encode($this->common_data['company_data']['default_segment']); ?>;
    var defaultSegment = '<?php echo $master['segmentID'] ?>|<?php echo $master['segmentCode'] ?>'
    $(document).ready(function () {

        $("[rel=tooltip]").tooltip();
       //$('.select2').select2();
        $(".select2").select2({ width: '100px importent' });
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
                url: "<?php echo site_url('Receivable/save_inv_tax_detail'); ?>",
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
                        <!--No Records Found-->
                    } else {
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


                            str = '<tr>';
                            str += '<td>' + x + '</td>';
                            str += '<td>' + value['itemSystemCode'] + ' - ' + value['itemDescription'] + '<br><b> UOM - </b>' + value['unitOfMeasure'] + '</td>';
                            str += '<td class="text-center"><select class="whre_drop" style="width: 110px;"  id="whre_' + value['contractDetailsAutoID'] + '" onchange="getWareHouseQty('+ value['contractDetailsAutoID'] +')">' +
                                    '<option value="">Select WareHouse</option></select><br/> Current Stock : <span id="wareHouseQty_' + value['contractDetailsAutoID'] + '">0</span></td>';
                            <?php if($openContractPolicy != 1) { ?>
                            str += '<td class="text-right">' + (value['requestedQty'] - value['receivedQty']) + '</td>';
                            <?php } ?>
                            str += '<td class="text-right">' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td>';
                            str += '<td class="text-right">' + (value['discountAmount']) + '</td>';

                            <?php  if($qty_validate == 1) {?>
                            str += '<td class="text-center"><input type="text" class="number" size="8" id="qty_' + value['contractDetailsAutoID'] + '" onkeyup="validate_Qty_invoice(' + value['contractDetailsAutoID'] + ',this.value, ' + balQty + ',\''+ value['invmaincat']+'\')" ></td>';
                            <?php } else { ?>
                            str += '<td class="text-center"><input type="text" class="number" size="8" id="qty_' + value['contractDetailsAutoID'] + '" onkeyup="validate_with_wareHouse_qty(' + value['contractDetailsAutoID'] + ',\''+ value['invmaincat']+'\'), select_check_box(' + value['contractDetailsAutoID'] + ')" ></td>';
                            <?php } if($costChange_validate == 1) { ?>
                            str += '<td class="text-center"><input type="text" class="number" size="10" value="' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])) + '" onkeypress="return validateFloatKeyPress(this,event);"  id="amount_' + value['contractDetailsAutoID'] + '" onkeyup="select_amount(' + value['contractDetailsAutoID'] + ',this.value)"></td>';
                            <?php } else { ?>
                            str += '<td class="text-center"><input type="text" class="number" size="10" value="' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])) + '" onkeypress="return validateFloatKeyPress(this,event);"  id="amount_' + value['contractDetailsAutoID'] + '" onkeyup="select_amount(' + value['contractDetailsAutoID'] + ',this.value)" disabled></td>';
                            <?php } ?>
                            str += '<td class="text-center"><input type="text" class="number" size="10" value="' + parseFloat(value['discountAmount']).toFixed(currency_decimal) + '" id="discount_' + value['contractDetailsAutoID'] + '" onkeyup="cal_tot_amount(' + value['contractDetailsAutoID'] + ',this.value);" onkeypress="return validateFloatKeyPress(this,event);"></td>';
                            <?php if($taxEnabled == 1 || $taxEnabled == null){ ?>
                            str += '<td class="text-center"><select class="tax_drop" id="tax_drop_' + value['contractDetailsAutoID'] + '" name="tex_type[]" onchange="cal_con_base_tax(' + value['contractDetailsAutoID'] + ',this)"><option value="">Select Tax</option></select><div class="input-group"> <input name="item_taxPercentage[]" onkeyup="change_tax_per(' + value['contractDetailsAutoID'] + ',this.value)" id="item_taxPercentage_' + value['contractDetailsAutoID'] + '" placeholder="0.00" class="form-control number item_taxPercentage input-mini" value="0" style="width:60px;" readonly="" autocomplete="off" type="text"><span class="input-group-addon input-group-addon-mini">%</span><input class="form-control number" id="tax_amount_' + value['contractDetailsAutoID'] + '" name="tax_amount[]" style="width: 50px;" onkeyup="change_tax_amount(' + value['contractDetailsAutoID'] + ',this.value);" onkeypress="return validateFloatKeyPress(this,event)" autocomplete="off" type="text" value="0" readonly=""></div></td>';
                            <?php } else{?>
                            str += '<td style="display: none;" class="text-center"></td>';
                            <?php } ?>
                            str += '<td class="text-right" id="tot_' + value['contractDetailsAutoID'] + '">0</td>';
                            str += '<td><input placeholder="Remarks" type="text" size="13" id="remarks_' + value['contractDetailsAutoID'] + '"></td>';
                            str += '<td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['contractDetailsAutoID'] + '" type="checkbox" value="' + value['contractDetailsAutoID'] + '"></td></tr>';


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
        var Cont_policy = <?php echo $open_contract; ?>;
        validate_with_wareHouse_qty(id,invmaincat);
        if(Cont_policy != 1) {
            if(balanceQty < Qty) {
                $('#qty_' + id).val('');
                myAlert('w', 'Qty can not be greater than balance Qty');
            }
        }
        select_check_box(id);
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

    function getWareHouseQty(contractDetID){
        let wareHouse = $('#whre_'+contractDetID).val();
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
        var tex_type = $('#tax_drop_' + id).val();
        $("#check_" + id).prop("checked", false);
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

        if (qty => 0 && amount > 0 || tex_type > 0)
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
                $(this).closest('tr').find('.wareHouseAutoID').val('').change();
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


            }
        });
    }

    function fetch_invoice_direct_details() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('Invoices/fetch_invoice_direct_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                tax_total = 0;
                transactionDecimalPlaces = 2;

                $('#gl_table_body,#item_table_body,#item_table_tfoot,#gl_table_tfoot').empty();
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#gl_table_body,#item_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
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
                } else {
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
                    gl_trans_amount = 0;
                    gl_tot_amount = 0;
                    gl_margin_percentage = 0;
                    gl_margin_amount = 0;
                    gl_local_amount = 0;
                    gl_party_amount = 0;
                    item_trans_amount = 0;
                    item_local_amount = 0;
                    item_party_amount = 0;
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
                                string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] +  '-' + wareLocation + ' - ' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '(' + value['discountPercentage'] + '%)</td><td class="text-right">' + parseFloat(value['unittransactionAmount'] - value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount'] - value['discountAmount']))).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat((value['requestedQty'] * value['taxAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['invoiceDetailsAutoID'] + ',\'CINV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                            } else {
                                if (data['currency']['invoiceType'] == "Direct") {
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] + '-' + wareLocation + ' - ' + partnoremarks  + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '(' + value['discountPercentage'] + '%)</td><td class="text-right">' + parseFloat(value['unittransactionAmount'] - value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right taxshowYN">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount'] - value['discountAmount']))).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right taxshowYN">' + parseFloat( value['totalAfterTax']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                } else {
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + val + value['itemDescription'] + '-' + wareLocation + ' - ' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>' + value['discountAmount'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount'] - value['discountAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right taxshowYN">' + parseFloat((value['requestedQty'] * (value['unittransactionAmount'] - value['discountAmount']))).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right taxshowYN">' + parseFloat( value['totalAfterTax']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + (parseFloat(value['transactionAmount'])).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['invoiceDetailsAutoID'] + ',' + value['expenseGLAutoID'] + ',' + value['revenueGLAutoID'] + ');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['invoiceDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }

                            }
                            $('#item_table_body').append(string);

                            x++;
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            item_trans_amount += (parseFloat(value['transactionAmount']));
                            item_local_amount += (parseFloat(value['companyLocalAmount']));
                            item_party_amount += (parseFloat(value['customerAmount']));
                            if (data['currency']['invoiceType'] == "Direct") {
                                $('#item_table_tfoot').append('<tr><td colspan="10" class="text-right taxenablefooter"><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?> </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                                <!--Item Total-->
                            } else {
                                $('#item_table_tfoot').append('<tr><td colspan="10" class="text-right taxenablefooter"><?php echo $this->lang->line('sales_markating_transaction_sales_Item_tot');?>  </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            }
                            <!--Item Total-->
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


                        } else {
                            $('#gl_table_tfoot').empty();
                            var amountmr=parseFloat(value['transactionAmount'])-parseFloat(value['marginAmount']);
                            $('#gl_table_body').append('<tr><td>' + y + '</td><td>' + value['revenueGLCode'] + '</td><td>' + value['revenueGLDescription'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + amountmr.formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['marginPercentage']).formatMoney(2, '.', ',') + ' </td><td class="text-right">' + parseFloat(value['marginAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_gl_item(' + value['invoiceDetailsAutoID'] + ',\'' + value['GLDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['invoiceDetailsAutoID'] + ',\'' + value['GLDescription'] + '\',1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            //<td class="text-right">' + parseFloat(value['companyLocalAmount']).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['customerAmount']).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                            y++;
                            tax_total += (parseFloat(value['transactionAmount']) - parseFloat(value['totalAfterTax']));
                            gl_tot_amount += (parseFloat(value['transactionAmount']));
                            gl_margin_percentage += (parseFloat(value['marginPercentage']));
                            gl_margin_amount += (parseFloat(value['marginAmount']));
                            gl_trans_amount += (parseFloat(value['transactionAmount'])-parseFloat(value['marginAmount']));
                            //gl_local_amount += (parseFloat(value['companyLocalAmount']));
                            //gl_party_amount += (parseFloat(value['customerAmount']));
                            $('#gl_table_tfoot').append('<tr><td colspan="4" class="text-right"> Total </td><td class="text-right total">' + parseFloat(gl_trans_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right total" colspan="2">' + parseFloat(gl_margin_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right total">' + parseFloat(gl_tot_amount).formatMoney(transactionDecimalPlaces, '.', ',') + '</td></tr>');
                            //<td class="text-right total">' + parseFloat(gl_local_amount).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right total">' + parseFloat(gl_party_amount).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                        }
                    });
                }
                $('#tax_tot').text('<?php echo $this->lang->line('sales_markating_transaction_tax_applicable_amount');?>( ' + parseFloat(tax_total).formatMoney(transactionDecimalPlaces, '.', ',') + ' )');
                $('#tax_tot_hn_val').val(tax_total);
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

        if (total_amount && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / tottaxappamnt) * 100).toFixed(0));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_tax(discount, total_amount) {
        var tottaxappamnt= $('#tax_tot_hn_val').val();
        if (total_amount && discount) {
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
                    url: "<?php echo site_url('Invoices/fetch_customer_invoice_detail'); ?>",
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
                        $('#contractBalance').val(data['balanceQty']);
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
                        $('#edit_remarks').val(data['remarks']);
                        if(data['mainCategory'] == "Service"){
                            $('#currentstock_edit').val(0);

                        }else{
                            $('#currentstock_edit').val(data.currentStock);
                        }
                        $('#mainCategoryhn').val(data['mainCategory']);
                        $('#edit_item_text').val(data['taxMasterAutoID']);
                        $('#edit_item_taxPercentage').val(data['taxPercentage']);
                        if (data['taxPercentage'] != 0) {
                            $('#edit_item_taxPercentage').prop('readonly', false);
                        } else {
                            $('#edit_item_taxPercentage').prop('readonly', true);
                        }
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
                    url: "<?php echo site_url('Invoices/fetch_customer_invoice_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        invoiceDetailsAutoID = data['invoiceDetailsAutoID'];
                        load_segmentBase_projectID_incomeEdit(data['segmentID'], data['projectID']);
                        $('#edit_gl_code').val(data['revenueGLAutoID']).change();
                        $('#edit_segment_gl').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#edit_amount').val(parseFloat(parseFloat(data['transactionAmount'])-parseFloat(data['marginAmount'])).toFixed(currency_decimal));
                        $('#edit_marginPercentage').val(data['marginPercentage']);
                        $('#edit_marginAmount').val(data['marginAmount']);
                        $('#edit_transactionAmount').val(parseFloat(data['transactionAmount']));
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
                wareHouseAutoID.push($('#whre_' + $(this).val()).val());
                whrehouse.push($('#whre_' + $(this).val() + ' option:selected').text());
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
                    myAlert(data[0], data[1]);
                    stopLoad();
                    if (data[0] == 's') {
                        $('#invoice_con_base_modal').modal('hide');
                        //$('#myTab li:eq(2) a').tab('show');
                        setTimeout(function () {
                            fetch_details(tabID);
                        }, 300);
                        setTimeout(function () {
                            tab_active(tabID);
                        }, 1000);

                    }
                }, error: function () {
                    $('#invoice_con_base_modal').modal('hide');
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

    function saveInvoiceItemDetail() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#invoice_item_detail_form').serializeArray();
        if (invoiceAutoID) {
            data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
            data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
//            data.push({'name': 'wareHouse', 'value': $('#wareHouseAutoID option:selected').text()});
//            data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
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
                    url: "<?php echo site_url('Invoices/save_invoice_item_detail'); ?>",
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
            url: "<?php echo site_url('Invoices/save_direct_invoice_detail_margin'); ?>",
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
            url: "<?php echo site_url('Invoices/update_income_invoice_detail_margin'); ?>",
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
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Invoices/update_invoice_item_detail'); ?>",
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
                primaryKey: 'invoiceAutoID'
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price'); ?>",
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
                primaryKey: 'invoiceAutoID'
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
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }

    }

    function checkCurrentStock(det) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        if (det.value > parseFloat(currentStock)) {
            myAlert('w', 'Transfer quantity should be less than or equal to current stock');
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
                myAlert('w', 'Transfer quantity should be less than or equal to current stock');
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
                myAlert('w', 'Transfer quantity should be less than or equal to current stock');
                $('#edit_quantityRequested').val(0);
            }
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
                    <!--No Records Found-->
                } else {
                    $.each(data, function (key, value) {

                        if(value['mainCategory'] == "Service"){
                            currentstock_all=0;
                        }else{
                            currentstock_all= value['currentStock'];
                        }

                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" disabled  required')) ?>';
                        var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID"  onchange="checkitemavailable(this)" required')) ?>';
                        var taxfield = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="taxfield_\'+key+\'"'), form_dropdown('item_text[]', all_tax_drop(1), '', 'class="form-control item_text input-mini"  onchange="select_text_item(this)"')) ?>';

                        var string = '<tr><td> <input type="text" class="form-control search f_search" name="search[]" id="f_search_' + x + '" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]"> <input type="hidden" class="form-control invoiceDetailsAutoID" name="invoiceDetailsAutoID[]" value="' + value['invoiceDetailsAutoID'] + '"> </td> <td> ' + wareHouseAutoID + ' </td> <td> ' + UOM + ' </td> <td> <div class="input-group"> <input type="text" name="currentstock[]" value="' + currentstock_all + '" class="form-control currentstock" required disabled> </div> </td> <td><input type="text" onfocus="this.select();" name="quantityRequested[]" onkeyup="checkCurrentStockAll(this,\'' + value['mainCategory'] + '\')" value="' + value['requestedQty'] + '" placeholder="0.00" class="form-control number input-mini quantityRequested" required></td> <td> <div class="input-group"> <input type="text" onfocus="this.select();" name="estimatedAmount[]" value="' + value['unittransactionAmount'] + '" onkeyup="change_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" placeholder="0.00" class="form-control number estimatedAmount input-mini"> </div> </td> <td style="width: 100px;"> <div class="input-group"> <input type="text" name="discount[]" placeholder="0.00" onkeyup="cal_discount(this)" onfocus="this.select();" value="' + value['discountPercentage'] + '" class="form-control number discount"> <span class="input-group-addon">%</span></div> </td> <td style="width: 100px;"> <input type="text" name="discount_amount[]" placeholder="0.00" value="' + value['discountAmount'] + '" onkeyup="cal_discount_amount(this)" onfocus="this.select();" class="form-control number discount_amount"> </td> <td class="taxshowYN"> ' + taxfield + ' </td> <td style="width: 120px" class="taxshowYN"> <div class="input-group"> <input type="text" name="item_taxPercentage[]" placeholder="0.00" onfocus="this.select();" class="form-control number item_taxPercentage input-mini" id="item_taxPercentage_all_' + x + '" value="' + value['taxPercentage'] + '" value="0" readonly> <span class="input-group-addon input-group-addon-mini">%</span> </div> </td> <td> <textarea class="form-control input-mini" rows="1" name="remarks[]" placeholder="Item Remarks...">' + value['remarks'] + '</textarea> </td> <td class="remove-td"><a onclick="delete_customer_invoiceDetailsEdit(' + value['invoiceDetailsAutoID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';


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
                <!--Total-->

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
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Invoices/updateCustomerInvoice_edit_all_Item'); ?>",
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
                    url: "<?php echo site_url('Invoices/delete_item_direct'); ?>",
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

    function calculate_total(ths,fild){
        var amount=$(ths).closest('tr').find('.amount').val();
        var marginPercentage=$(ths).closest('tr').find('.marginPercentage').val();
        var marginAmount=$(ths).closest('tr').find('.marginAmount').val();
        var transactionAmount=$(ths).closest('tr').find('.transactionAmount').val();

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
            $(ths).closest('tr').find('.transactionAmount').val(amount)
        }else if(amount>0 && marginPercentage>0 && marginAmount<=0){
            var margamnt=(amount*marginPercentage)/100;
            $(ths).closest('tr').find('.transactionAmount').val(parseFloat(amount)+parseFloat(margamnt));
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
                $(ths).closest('tr').find('.transactionAmount').val(parseFloat(amount));
            }else{
                $(ths).closest('tr').find('.marginPercentage').val(percentage);
                $(ths).closest('tr').find('.transactionAmount').val(parseFloat(amount)+parseFloat(marginAmount));
            }
        }else if(amount>0 && marginPercentage>0 && marginAmount>0){
            if(fild=='marginAmount'){
                var percentage = (parseFloat(marginAmount)/parseFloat(amount)) * 100;
                $(ths).closest('tr').find('.transactionAmount').val(parseFloat(amount)+parseFloat(marginAmount));
                $(ths).closest('tr').find('.marginPercentage').val(percentage);
            }else{
                var margamnt=(amount*marginPercentage)/100;
                $(ths).closest('tr').find('.transactionAmount').val(parseFloat(amount)+parseFloat(margamnt));
                $(ths).closest('tr').find('.marginAmount').val(margamnt);
            }

        }else{
            myAlert('w','Enter Amount');
                $(ths).closest('tr').find('.amount').val(0);
                $(ths).closest('tr').find('.marginPercentage').val('');
                $(ths).closest('tr').find('.marginAmount').val(0);
                $(ths).closest('tr').find('.transactionAmount').val(0);
        }
    }

    function edit_validateQtyContract(qty) {
        var Cont_policy = <?php echo $open_contract; ?>;
        if(Cont_policy != 1) {
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
    }
</script>