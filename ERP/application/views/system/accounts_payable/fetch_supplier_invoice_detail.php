<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .boldtab {
        font-weight: bold;
        border-left-color: #ead8d8 !important;
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
$this->lang->load('accounts_payable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$segment_arr = fetch_segment();
$location_arr = all_delivery_location_drop();
$location_arr_default = default_delivery_location_drop();
$projectExist = project_is_exist();
$umo_arr = array('' => 'Select UOM');
$transaction_total = 0;
$totalwithTax = 0;
$placeholder = '0.00';
$currencyID = $master['transactionCurrency'];
$currency_decimal = $master['transactionCurrencyDecimalPlaces'];
$showPurchasePrice = getPolicyValues('SPP', 'All');
$itemBatchPolicy= getPolicyValues('IB', 'All');
$advanceCostCapturing = getPolicyValues('ACC', 'All');
$retensionEnabled = getPolicyValues('RETO', 'All');

$activityCode_arr = get_activity_codes();
if($showPurchasePrice==' ' || $showPurchasePrice== null || empty($showPurchasePrice)){
    $showPurchasePrice = 0;
}
if($currencyID == 'OMR')
{
    $placeholder = '0.000';
}
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


    if ($invoiceType == 'GRV Base') { ?>
            <div class="row">
                <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i>
                        <?php echo $this->lang->line('accounts_payable_add_item_grv_base'); ?><!--Add Item GRV Base--> </h4></div>
                <div class="col-md-4">
                    <button type="button" data-toggle="modal" data-target="#grv_base_modal" class="btn btn-primary pull-right">
                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_payable_add_grv'); ?><!--Add GRV-->
                    </button>
                </div>
            </div>
            <div class="modal fade" id="grv_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false" data-backdrop="static">
                <div class="modal-dialog" role="document" style="width: 75%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">
                                <?php echo $this->lang->line('accounts_payable_grv_base'); ?><!--GRV Base--></h4>
                            <div class="row">
                                <div class="form-group col-sm-10">

                                </div>
                                <div class="form-group col-sm-2">
                                    <div class="skin skin-square">
                                        <div class="skin-section extraColumns">Select All &nbsp;<input id="issubtask" type="checkbox"
                                                                                                    data-caption="" class="columnSelected add_allinvoices"
                                                                                                    name="issubtask" value="1"><label
                                                    for="checkbox">&nbsp;</label></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-body">
                            <table class="table table-bordered table-striped table-condesed ">
                                <thead>
                                <tr>
                                    <th colspan="4">
                                        <?php echo $this->lang->line('accounts_payable_grv_detail'); ?><!--GRV Details--></th>
                                    <th colspan="4"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span></th>
                                </tr>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('accounts_payable_grv_code'); ?><!--GRV Code--></th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('accounts_payable_grv_date'); ?><!--GRV Date--></th>
                                    <th style="width: 10%">
                                        <?php echo $this->lang->line('accounts_payable_reference_no'); ?><!--Reference No--></th>
                                    <th style="width: 10%">
                                        <?php echo $this->lang->line('accounts_payable_grv_total'); ?><!--GRV Total--></th>
                                    <th style="width: 10%">
                                        <?php echo $this->lang->line('accounts_payable_invoiced'); ?><!--Invoiced--></th>
                                    <th style="width: 10%">
                                        <?php echo $this->lang->line('accounts_payable_balance'); ?><!--Balance--></th>
                                    <th style="width: 15%"><?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                                </tr>
                                </thead>
                                <tbody id="table_body">

                                <?php
                                if (!empty($supplier_grv)) {
                                    for ($i = 0; $i < count($supplier_grv); $i++) {
                                        $grvtot = 0;
                                        echo "<tr>";
                                        echo "<td>" . ($i + 1) . "</td>";
                                        echo ($supplier_grv[$i]['isAddon']) ? "<td>" . $supplier_grv[$i]['grvPrimaryCode'] . " - Addon </td>" : "<td>" . $supplier_grv[$i]['grvPrimaryCode'] . " </td>";
                                        echo "<td>" . $supplier_grv[$i]['grvDate'] . "</td>";
                                        echo "<td>" . $supplier_grv[$i]['grvDocRefNo'] . "</td>";
                                        $grvtot = $supplier_grv[$i]['bookingAmount'];
                                        if ($grvtot > 0) {
                                            echo "<td class='text-right'>" . number_format($grvtot, $master['transactionCurrencyDecimalPlaces']) . "</td>";
                                        } else {
                                            echo "<td class='text-right'>" . number_format(0, $master['transactionCurrencyDecimalPlaces']) . "</td>";
                                        }
                                        echo "<td class='text-right'>" . number_format($supplier_grv[$i]['invoicedTotalAmount'], $master['transactionCurrencyDecimalPlaces']) . "</td>";
                                        echo "<td class='text-right'>  <span class='supplierinvoicebalance'> " . number_format(($grvtot - $supplier_grv[$i]['invoicedTotalAmount']), $master['transactionCurrencyDecimalPlaces']) . "
                                                &nbsp;<a class='hoverbtn grvaddbtn' onclick='applybtn(this, " . round(($grvtot - $supplier_grv[$i]['invoicedTotalAmount']), $master['transactionCurrencyDecimalPlaces']) . ", " . $supplier_grv[$i]['match_supplierinvoiceAutoID'] . ")'><i class='fa fa-arrow-circle-o-right' aria-hidden='true'></i></a></span></td>";
                                        echo '<td><input type="hidden" name="grv[]" id="grv_' . $supplier_grv[$i]['match_supplierinvoiceAutoID'] . '" value="' . $supplier_grv[$i]['grvAutoID'] . '">
                                            <input type="hidden" name="match[]" id="match_' . $supplier_grv[$i]['match_supplierinvoiceAutoID'] . '" value="' . $supplier_grv[$i]['match_supplierinvoiceAutoID'] . '">
                                                <input type="hidden" class="supplierAutoID" value="' . $supplier_grv[$i]['match_supplierinvoiceAutoID'] . '">

                                            <input type="text" name="amount[]" id="amount_' . $supplier_grv[$i]['match_supplierinvoiceAutoID'] . '" onkeyup="select_check_box(this.value,' . $supplier_grv[$i]['match_supplierinvoiceAutoID'] . ',' . round(($grvtot - $supplier_grv[$i]['invoicedTotalAmount']), $master['transactionCurrencyDecimalPlaces']) . ',' . $master['transactionCurrencyDecimalPlaces'] . ')" class="number grvamount amountadd">
                                            &nbsp;&nbsp;<i class="fa fa-times" onclick="clear_grv_selected(this,' . $supplier_grv[$i]['match_supplierinvoiceAutoID'] .')" aria-hidden="true"></i></td>';
                                        echo '<td class="text-right" style="display:none;"><input class="checkbox" id="check_' . $supplier_grv[$i]['match_supplierinvoiceAutoID'] . '" type="checkbox" value="' . $supplier_grv[$i]['match_supplierinvoiceAutoID'] . '"></td>';
                                        echo "</tr>";
                                    }
                                } else {
                                    $norec = $this->lang->line('common_no_records_found');
                                    echo '<tr class="danger"><td colspan="7" class="text-center"><b>' . $norec . '<!--No Records Found--></b></td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                            <button type="button" class="btn btn-primary" onclick="save_grv_base_items()">
                                <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="add_new_grv_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class='theadtr' colspan="4">
                        <?php echo $this->lang->line('accounts_payable_grv_details'); ?><!--GRV Details--></th>
                    <th class='theadtr' colspan="5"><?php echo $this->lang->line('common_amount'); ?> <!--Amount--></th>
                </tr>
                <tr>
                    <th class='theadtr' style="min-width: 3%">#</th>
                    <th class='theadtr' style="min-width: 15%">
                        <?php echo $this->lang->line('accounts_payable_grv_code'); ?><!--GRV Code--></th>
                    <th class='theadtr' style="min-width: 10%">
                        <?php echo $this->lang->line('accounts_payable_grv_date'); ?><!--GRV Date--></th>
                    <th class='theadtr' style="min-width: 40%">
                        <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                    <th class='theadtr' style="min-width: 12%">
                        <?php echo $this->lang->line('common_transaction'); ?>  (<?php echo $master['transactionCurrency']; ?>)   
                    <?php if($retensionEnabled == 1){ ?>
                        <th> <?php echo $this->lang->line('common_retension'); ?></th>
                        <th> <?php echo $this->lang->line('common_taxapplicable'); ?></th>
                    <?php } ?>       
                    <?php if($isGroupBasedTaxEnable == 1){ ?>
                        <th class='theadtr' style="min-width: 10%">
                            <?php echo 'Tax'; ?></th>
                        <th class='theadtr' style="min-width: 20%">
                            <?php echo 'Tax Amount' ?></th>
                    <?php } ?>
                    <th class='theadtr' style="min-width: 5%">
                        <?php echo 'Net Amount' ?> (<?php echo $master['transactionCurrency']; ?>) </th> 
                    </th>
                    <th style="min-width: 5%">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $Local_total = 0;
                $supplier_total = 0;
                
                if (!empty($detail['detail'])) {
                    for ($i = 0; $i < count($detail['detail']); $i++) {
                        echo '<tr>';
                        echo '<td>' . ($i + 1) . '</td>';
                        echo '<td>' . $detail['detail'][$i]['grvPrimaryCode'] . '</td>';
                        echo '<td>' . $detail['detail'][$i]['grvDate'] . '</td>';
                        echo '<td>' . $detail['detail'][$i]['description'] . '</td>';

                        
                        if($retensionEnabled == 1){
                            echo '<td class="text-right"> <input type="hidden" class="" value="'.$detail['detail'][$i]['transactionAmount'].'"> ' . format_number($detail['detail'][$i]['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                            echo '<td class="text-right"> ('.format_number($detail['detail'][$i]['retensionPercentage'], $master['transactionCurrencyDecimalPlaces']).'%) <input type="hidden" class="" value="'.$detail['detail'][$i]['retensionValue'].'"> ' . format_number($detail['detail'][$i]['retensionValue'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                            echo '<td class="text-right"> <input type="hidden" class="amount" value="'. ($detail['detail'][$i]['transactionAmount'] - $detail['detail'][$i]['retensionValue'])  .'"> ' . format_number(($detail['detail'][$i]['transactionAmount'] - $detail['detail'][$i]['retensionValue']), $master['transactionCurrencyDecimalPlaces']) . '</td>';
                        }else{
                            echo '<td class="text-right"> <input type="hidden" class="amount" value="'.$detail['detail'][$i]['transactionAmount'].'"> ' . format_number($detail['detail'][$i]['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';

                        }
                        
                        if($isGroupBasedTaxEnable == 1){
                            echo '<td class ="text-right">'.
                                    form_dropdown('tax_type',all_tax_formula_drop_groupByTax(), $detail['detail'][$i]['taxCalculationformulaID'], 'class="form-control tax_type" id="tax_type_edit_expenceGL" onchange="load_line_tax_amount_expenceGL(this,'.$detail['detail'][$i]['InvoiceDetailAutoID'].',1)" ')
                                .'</td>';
                            echo '<td class="lintax" style="width: 5%;"><span class="linetaxamntExpenceGl pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">'.format_number($detail['detail'][$i]['taxAmount'], $master['transactionCurrencyDecimalPlaces']) .'</span></td>';
                        }
                        echo '<td class="text-right"> <input type="hidden" class="total_amount" value="'.($detail['detail'][$i]['transactionAmount']+($detail['detail'][$i]['taxAmountLedger']) - ($detail['detail'][$i]['retensionValue'])).'"> <input type="text" name="Netamount" class="form-control number Netnumber" value=' . format_number(($detail['detail'][$i]['transactionAmount']+($detail['detail'][$i]['taxAmountLedger']) - ($detail['detail'][$i]['retensionValue'])), $master['transactionCurrencyDecimalPlaces']) . ' readonly></td>';

                        echo '<td class="text-right"><a onclick="delete_item(' . $detail['detail'][$i]['InvoiceDetailAutoID'] . ',\'' . $detail['detail'][$i]['GLDescription'] . '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td>';
                        
                        echo '</tr>';
                        
                        //$gran_total             += ($detail['detail'][$i]['transactionAmount']);
                        $transaction_total += ($detail['detail'][$i]['transactionAmount']+$detail['detail'][$i]['taxAmount']);
                        //$Local_total            += ($detail['detail'][$i]['companyLocalAmount']);
                        //$supplier_total         += ($detail['detail'][$i]['supplierAmount']);
                        // $tax_transaction_total  += ($detail['detail'][$i]['transactionAmount']);
                        // $tax_Local_total        += ($detail['detail'][$i]['companyLocalAmount']);
                        // $tax_supplier_total     += ($detail['detail'][$i]['supplierAmount']);
                    }
                } else {
                    $norecfo = $this->lang->line('accounts_payable_grv_detail');
                    echo '<tr class="danger"><td colspan="8" class="text-center"><b>' . $norecfo . '<!--No Records Found--></b></td></tr>';
                }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right sub_total hide" colspan="7">
                            <?php echo $this->lang->line('accounts_payable_grv_total'); ?><!--GRV Total--></td>
                        <td class="text-right total hide"><span id="t_total"><?php echo format_number($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?></span></td>
                        <td>&nbsp;</td>
                            <!-- <td class="text-right total"><?php //echo format_number($Local_total,$master['companyLocalCurrencyDecimalPlaces']); ?></td>
                                <td class="text-right total"><?php //echo format_number($supplier_total,$master['supplierCurrencyDecimalPlaces']); ?></td> -->
                            <!--<td class="sub_total"> &nbsp; </td>-->
                        </tr>
                </tfoot>
            </table>
            <br>
    <?php } else { ?>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs pull-right">
            <?php if($invoiceType == 'Standard' || $invoiceType == 'StandardExpense' || $invoiceType == 'StandardItem' || $invoiceType == 'StandardPO'){ ?>
                <li class="<?php if($invoiceType != 'StandardPO'){echo 'active ';}?> itmtb" id="tabli_1"><a class="boldtab" data-toggle="tab" href="#tab_1"
                                                         aria-expanded="false">Expense GL</a></li>
            <?php } ?>
            <?php if($invoiceType == 'Standard' || $invoiceType == 'StandardItem'){ ?>
                <li class="itmtb" id="tabli_2"><a class="boldtab" data-toggle="tab" href="#tab_2" aria-expanded="false">
                        <?php echo $this->lang->line('common_item'); ?><!--Item--></a></li>
            <?php } ?>
            <?php if($invoiceType == 'Standard' || $invoiceType == 'StandardPO' || $invoiceType == 'StandardItem'){ ?>
                <li class="tab_3_Item_BSI <?php if($invoiceType == 'StandardPO'){echo 'active ';}?> <?php if($invoiceType == 'StandardItem') { echo 'hide';}?> itmtb" id="tabli_3">
                    <a class="boldtab" data-toggle="tab" href="#tab_3"
                                                  aria-expanded="<?php if($invoiceType == 'StandardPO'){echo 'true';} else {echo 'false';}?>">PO</a></li>
            <?php } ?>
            <li class="pull-left header"><i class="fa fa-hand-o-right"></i> Add Detail</li>



        </ul>
        <?php if($isRcmApplicable == 1){?>
            <div class="rcmStatus">
                <span class="label label-danger" style="font-size: 9px;" title="Not Received" rel="tooltip">Reverse Charge Mechanism Activated</span>
            </div>
        <?php }?>

        <div class="tab-content">
            <?php if($invoiceType == 'Standard' || $invoiceType == 'StandardExpense' || $invoiceType == 'StandardItem' || $invoiceType == 'StandardPO'){ ?>
                <div id="tab_1" class="tab-pane itmtb active">
                    <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                            <th colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? 5:3) ?>"><?php echo $this->lang->line('common_amount'); ?> <!--Amount--></th>
                            <th>
                                <button type="button" onclick="load_supplier_detail_modal()" class="btn btn-primary btn-xs">
                                    <i
                                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?><!--Add-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                            <th style="min-width: 25%">
                                <?php echo $this->lang->line('common_gl_code_description'); ?><!--GL Code Description--></th>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                            <?php if($advanceCostCapturing == 1){ ?>
                            <th style="min-width: 10%">Activity Code</th>
                            <?php } ?>
                            <th style="min-width: 12%">
                                <?php echo $this->lang->line('common_transaction'); ?><!--Transaction-->
                                (<?php echo $master['transactionCurrency']; ?>)
                            </th>
                            <th>Discount Amount</th>
                            <?php if($retensionEnabled == 1){ ?>
                                <th> <?php echo $this->lang->line('common_retension'); ?></th>
                            <?php } ?>

                            <?php if($isGroupBasedTaxEnable == 1){?>
                                <th style="min-width: 15%">Tax</th>
                                <th style="min-width: 15%">Tax Amount</th>
                            <?php }?>

                            <th>Net Amount <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 5%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="bsi_table_body">
                        <?php ;
                        $Local_total = 0;
                        $supplier_total = 0;
                        if (!empty($detail['detail'])) {
                            for ($i = 0; $i < count($detail['detail']); $i++) {




                                echo '<tr>';
                                echo '<td>' . ($i + 1) . '</td>';
                                echo '<td>' . $detail['detail'][$i]['GLCode'] . '</td>';
                                echo '<td>' . $detail['detail'][$i]['GLDescription'] . ' - ' . $detail['detail'][$i]['description'] . '</td>';
                                echo '<td class="text-center">' . $detail['detail'][$i]['segmentCode'] . '</td>';
                                if($advanceCostCapturing == 1){
                                    echo '<td class="text-center">' . $detail['detail'][$i]['activityCodeName'] . '</td>'; 
                                }
                                echo '<td class="text-right">' . format_number($detail['detail'][$i]['transactionAmount']+$detail['detail'][$i]['discountAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                echo '<td class="text-right">('.format_number($detail['detail'][$i]['discountPercentage'], 2).'%)  ' . format_number($detail['detail'][$i]['discountAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                               
                                if($retensionEnabled == 1){ 
                                    echo '<td class="text-right">('.format_number($detail['detail'][$i]['retensionPercentage'], 2).'%)  ' . format_number($detail['detail'][$i]['retensionValue'], $master['transactionCurrencyDecimalPlaces']) . '</td>';

                                    $detail['detail'][$i]['transactionAmount'] = $detail['detail'][$i]['transactionAmount'] - $detail['detail'][$i]['retensionValue'];
                                }
                                

                                if($isGroupBasedTaxEnable == 1){
                                    echo '<td>'.$detail['detail'][$i]['DescriptionTax'].'</td>';
                                    if($detail['detail'][$i]['taxAmountLedger'] > 0){
                                        echo '<td class ="text-right">
                                                <a onclick="open_tax_dd(\''.$detail['detail'][$i]['taxDetailAutoID'].'\','.$detail['detail'][$i]['InvoiceAutoID'] .',\'BSI\','.$master['transactionCurrencyDecimalPlaces'].','.$detail['detail'][$i]['InvoiceDetailAutoID'].',\'srp_erp_paysupplierinvoicedetail\',\'InvoiceDetailAutoID\')"> ' . format_number($detail['detail'][$i]['taxAmountLedger'], $master['transactionCurrencyDecimalPlaces']) . '</a>
                                              </td>';
                                    }else {
                                        echo '<td> ' . format_number($detail['detail'][$i]['taxAmountLedger'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                    }

                                    echo '<td class="text-right">' . format_number((($detail['detail'][$i]['transactionAmount'])+($detail['detail'][$i]['taxAmountLedger'])), $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                }else {
                                    echo '<td class="text-right">' . format_number($detail['detail'][$i]['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) . '</td>';
                                }


                                echo '<td class="text-right"><a onclick="edit_item(' . $detail['detail'][$i]['InvoiceDetailAutoID'] . ');"><span style="color:#3c8dbc;" class="glyphicon glyphicon-pencil"></span></a>&nbsp&nbsp|&nbsp&nbsp<a onclick="delete_item(' . $detail['detail'][$i]['InvoiceDetailAutoID'] . ',1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td>';
                                echo '</tr>';

                                if($isGroupBasedTaxEnable == 1){
                                $transaction_total += (($detail['detail'][$i]['transactionAmount'])+$detail['detail'][$i]['taxAmountLedger']);
                                }else{
                                $transaction_total += ($detail['detail'][$i]['transactionAmount']);
                                }

                                $Local_total += ($detail['detail'][$i]['companyLocalAmount']);
                                $supplier_total += ($detail['detail'][$i]['supplierAmount']);
                            }
                        } else {
                            $norecfound = $this->lang->line('common_no_records_found');
                            echo '<tr class="danger"><td colspan="8" class="text-center"><b>' . $norecfound . '<!--No Records Found--></b></td></tr>';
                        }
                        //<a onclick="edit_item('.$detail['detail'][$i]['InvoiceDetailAutoID'].',\''.$detail['detail'][$i]['GLDescription'].'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <?php if($retensionEnabled == 1){  ?>
                                <td class="text-right" colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? 9 :7)?>">
                                <?php echo $this->lang->line('common_total'); ?><!--Total--></td>
                            <?php } else { ?>
                                <td class="text-right" colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? 8 :6)?>">
                                <?php echo $this->lang->line('common_total'); ?><!--Total--></td>
                            <?php } ?>
                           
                            <td class="text-right total"><?php echo format_number($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?></td>
                            <!-- <td class="text-right total"><?php //echo format_number($Local_total, $master['companyLocalCurrencyDecimalPlaces']); ?></td>
            <td class="text-right total"><?php //echo format_number($supplier_total, $master['supplierCurrencyDecimalPlaces']); ?></td> -->
                            <td>&nbsp;</td>
                        </tr>
                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
            <?php } ?>
            <?php if($invoiceType == 'Standard' || $invoiceType == 'StandardItem'){ ?>
                <div id="tab_2" class="tab-pane itmtb">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="5"><?php echo $this->lang->line('accounts_payable_tr_pv_item_details'); ?>
                                <!--Item Details--></th>
                            <th colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? 6 : 3)?>"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency">(<?php echo $master['transactionCurrency']; ?>
                                )</span></th>
                            <th>
                                <button type="button" onclick="bsi_item_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i
                                            class="fa fa-plus"></i><?php echo $this->lang->line('common_add_item'); ?>
                                    <!--Add Item-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                            <?php if ($itemBatchPolicy == 1) { ?>
                                <th>Batch Number</th>
                            <?php } ?>
                            <th style="min-width: 36%">
                                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                            <th style="min-width: 15%">Discount</th>
                            <?php if($retensionEnabled == 1){ ?>
                                <th> <?php echo $this->lang->line('common_retension'); ?></th>
                            <?php } ?>

                            <?php if($isGroupBasedTaxEnable == 1){?>
                            <th style="min-width: 15%">Tax</th>
                            <th style="min-width: 15%">Tax Amount</th>
                            <th style="min-width: 15%">Total</th>
                            <th style="min-width: 15%">Net Amount</th>
                            <?php }else { ?>
                                <th style="min-width: 15%"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                           <?php }?>


                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <?php
                        $itemtransaction_total = 0;
                        $discountAmount = 0;
                        if (!empty($detail['ItemDetail'])) {
                            $x = 1;
                            foreach ($detail['ItemDetail'] as $itmd) {
                                ?>
                                <tr>
                                    <td><?php echo $x ?></td>
                                    <td><?php echo $itmd['itemSystemCode'] ?></td>
                                    <?php if ($itemBatchPolicy == 1) { ?>
                                    <td><?php echo $itmd['batchNumber'] ?></td>
                                    <?php } ?>
                                    <td><?php echo $itmd['Itemdescriptionpartno'] ?></td>
                                    <td><?php echo $itmd['unitOfMeasure'] ?></td>
                                    <td><?php echo $itmd['requestedQty'] ?></td>
                                    <td><?php echo format_number(($itmd['unittransactionAmount']), $master['transactionCurrencyDecimalPlaces']) ?></td>
                                    <?php if ($itmd['discountAmount']) { ?>
                                        <td style="text-align: right;"><?php echo format_number($itmd['discountAmount'],$master['transactionCurrencyDecimalPlaces']) . '( ' .format_number($itmd['discountPercentage'],2)  . '%)' ?></td>
                                    <?php } else { ?>
                                        <td style="text-align: right;"><?php echo '0' ?></td>
                                    <?php } ?>

                                    <?php if ($retensionEnabled) { 
                                        echo '<td class="text-right">('.format_number($itmd['retensionPercentage'], 2).'%)  ' . format_number($itmd['retensionValue'], $master['transactionCurrencyDecimalPlaces']) . '</td>';

                                        $itmd['transactionAmount'] = $itmd['transactionAmount'] - $itmd['retensionValue'];
                                    } ?>
                                

                                    <?php if($isGroupBasedTaxEnable == 1){?>
                                    <td><?php echo $itmd['Description'] ?></td>
                                    <td style="text-align: right;">
                                    <?php if($itmd['taxAmountLedger'] > 0){?>
                                        <a onclick="open_tax_dd('<?php echo $itmd['taxDetailAutoID']?>',<?php echo $itmd['InvoiceAutoID']?>,'BSI',<?php echo $master['transactionCurrencyDecimalPlaces']?>,<?php echo $itmd['InvoiceDetailAutoID']?>,'srp_erp_paysupplierinvoicedetail','InvoiceDetailAutoID')"> <?php echo format_number(($itmd['taxAmountLedger']), $master['transactionCurrencyDecimalPlaces']) ?> </a>
                                    <?php }else {?>
                                    <?php echo format_number(($itmd['taxAmountLedger']), $master['transactionCurrencyDecimalPlaces']) ?>
                                    <?php }?>


                                    </td>





                                    <td style="text-align: right;"><?php echo format_number(($itmd['transactionAmount']), $master['transactionCurrencyDecimalPlaces']) ?></td>
                                    <td style="text-align: right;"><?php echo format_number(($itmd['transactionAmount']) + ($itmd['taxAmountLedger']), $master['transactionCurrencyDecimalPlaces']) ?></td>


                                    <?php }else { ?>
                                    <td style="text-align: right;"><?php echo format_number(($itmd['transactionAmount']), $master['transactionCurrencyDecimalPlaces']) ?></td>
                                    <?php }?>
                                    <td class="text-right"><a
                                                onclick="edit_bsi_item(<?php echo $itmd['InvoiceDetailAutoID'] ?>);"><span
                                                    style="color:#3c8dbc;" class="glyphicon glyphicon-pencil"></span></a>
                                        &nbsp;&nbsp;|&nbsp;&nbsp; <a
                                                onclick="delete_item(<?php echo $itmd['InvoiceDetailAutoID'] ?>,2);"><span
                                                    style="color:rgb(209, 91, 71);"
                                                    class="glyphicon glyphicon-trash"></span></a></td>
                                </tr>
                                <?php

                                if($isGroupBasedTaxEnable == 1){
                                    $itemtransaction_total += (($itmd['transactionAmount'])+$itmd['taxAmountLedger']);
                                    $totalwithTax += (($itmd['transactionAmount'])+$itmd['taxAmountLedger']);

                                }else {
                                    $itemtransaction_total += ($itmd['transactionAmount']);
                                    $totalwithTax += ($itmd['transactionAmount']);
                                }

                                $transaction_total += ($itmd['transactionAmount']);
                                $discountAmount += ($itmd['discountAmount']);

                                $x = $x++;
                            }
                        } else {
                            ?>
                            <tr class="danger">
                                <td colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? 12 : 9)?>" class="text-center"><b>
                                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>

                        </tbody>
                        <tfoot id="item_table_tfoot">
                        <tr>
                            <td colspan="<?php echo ($isGroupBasedTaxEnable == 1 ? 10 : 7)?>" style="text-align: right;">Total</td>
                            <td style="text-align: right;"><?php echo format_number(($itemtransaction_total), $master['transactionCurrencyDecimalPlaces']); ?></td>
                            <td>&nbsp</td>
                        </tr>
                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
            <?php } ?>
            <?php if($invoiceType == 'Standard' || $invoiceType == 'StandardPO' || $invoiceType == 'StandardItem'){ ?>
                <div id="tab_3" class="tab-pane <?php if($invoiceType == 'StandardPO'){echo 'active';}?> itmtb ">

                    <br>

                    <div class="table-responsive">
                        <table id="add_new_grv_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th colspan="4">Item Details</th><!---->
                                <th colspan="3">Ordered Item (<?php echo $master['transactionCurrency'] ?>)</th>
                                <th colspan="<?php echo ($isGroupBasedTaxEnable == 1? 6 :3) ?>">Received Item (<?php echo $master['transactionCurrency'] ?>)</th>
                                <th><button type="button" data-toggle="modal" data-target="#po_base_modal"
                                            class="btn btn-primary btn-xs pull-right"><i
                                                class="fa fa-plus"></i> Add PO
                                    </button></th>
                            </tr>
                            <tr>
                                <th style="min-width: 3%">#</th>
                                <th style="min-width: 10%">Item Code</th><!--Item Code-->
                                <th style="min-width: 23%"><?php echo $this->lang->line('common_item_description'); ?>  </th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('common_uom'); ?> </th><!--UOM-->
                                <th style="min-width: 5%">Qty</th><!--Qty-->
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_unit_cost'); ?>  </th>
                                <!--Unit Cost-->
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_net_amount'); ?>  </th>
                                <!--Net Amount-->
                                <th style="min-width: 5%"><?php echo $this->lang->line('common_qty'); ?> </th><!--Qty-->
                                <th style="min-width: 12%"><?php echo $this->lang->line('common_unit_cost'); ?> </th>
                                
                                <!--Unit Cost-->
                                <?php if($isGroupBasedTaxEnable == 1){?>
                                    <th style="min-width: 15%">Tax</th>
                                    <th style="min-width: 15%">Tax Amount</th>
                                    <th style="min-width: 15%">Total</th>
                                    <th style="min-width: 15%">Net Amount</th>
                                <?php  }else { ?>
                                    <th style="min-width: 10%"><?php echo $this->lang->line('common_net_amount'); ?> </th>
                                <?php }?>
                                <!--Net Amount-->
                                <th style="min-width: 7%">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="po_table_body">
                            <?php
                            $orded_total = 0;
                            $received_total = 0;
                            if (!empty($detail['poDetail'])) {
                                $p = 1;

                                foreach ($detail['poDetail'] as $itmd) {
                                    ?>
                                    <tr>
                                        <td><?php echo $p ?></td>
                                        <td><?php echo $itmd['itemSystemCode'] ?></td>
                                        <td><?php echo $itmd['purchaseOrderCode'] ?>
                                            - <?php echo $itmd['Itemdescriptionpartno'] ?></td>
                                        <td class="text-center"><?php echo $itmd['unitOfMeasure'] ?></td>
                                        <td class="text-center"><?php echo $itmd['orderedQty'] ?></td>
                                        <td class="text-right"><?php echo format_number($itmd['orderedAmount'], $master['transactionCurrencyDecimalPlaces']) ?></td>
                                        <td class="text-right"><?php echo format_number(($itmd['orderedQty'] * $itmd['orderedAmount']), $master['transactionCurrencyDecimalPlaces']) ?></td>
                                        <td class="text-center"><?php echo $itmd['requestedQty'] ?></td>
                                        <td class="text-right"><?php echo format_number($itmd['unittransactionAmount'], $master['transactionCurrencyDecimalPlaces']) ?></td>

                                        <?php if($isGroupBasedTaxEnable == 1){?>
                                            <td class="text-left"><?php echo $itmd['DescriptionTax']?></td>
                                            <td class="text-right">  <a onclick="open_tax_dd('<?php echo $itmd['taxDetailAutoID']?>',<?php echo $itmd['InvoiceAutoID']?>,'BSI',<?php echo $master['transactionCurrencyDecimalPlaces']?>,<?php echo $itmd['InvoiceDetailAutoID']?>,'srp_erp_paysupplierinvoicedetail','InvoiceDetailAutoID')"> <?php echo format_number(($itmd['taxAmountLedger']), $master['transactionCurrencyDecimalPlaces']) ?> </a></td>
                                            <td class="text-right"> <?php echo format_number($itmd['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) ?></td>
                                            <td class="text-right"><?php echo format_number(($itmd['transactionAmount']+$itmd['taxAmountLedger']), $master['transactionCurrencyDecimalPlaces']) ?></td>

                                        <?php }else { ?>
                                            <td class="text-right"><?php echo format_number($itmd['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) ?></td>
                                        <?php }?>
                                        <td class="text-right"><a
                                                    onclick="edit_item_po(<?php echo $itmd['InvoiceDetailAutoID'] ?> ,<?php echo $itmd['purchaseOrderMastertID'] ?>)"><span
                                                        class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                                            <a onclick="delete_item(<?php echo $itmd['InvoiceDetailAutoID'] ?>,3);"><span
                                                        style="color:rgb(209, 91, 71);"
                                                        class="glyphicon glyphicon-trash"></span></a></td>
                                    </tr>
                                    <?php
                                    $transaction_total += ($itmd['transactionAmount']);
                                    $orded_total += $itmd['orderedQty'] * $itmd['orderedAmount'];
                                    if ($isGroupBasedTaxEnable == 1) {

                                        $received_total += ($itmd['transactionAmount']+$itmd['taxAmountLedger']);
                                    }else {
                                        $received_total += $itmd['transactionAmount'];
                                    }



                                    $p = $p++;
                                }
                            } else {
                                ?>
                                <tr class="danger">
                                    <td colspan="11" class="text-center"><b>No Records Found</b></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td class="text-right" colspan="6">Ordered Item Total
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </td>
                                <td class="text-right total"><?php echo format_number($orded_total, $master['transactionCurrencyDecimalPlaces']) ?> </td>
                                <td class="text-right" colspan="<?php echo ($isGroupBasedTaxEnable == 1? 5 :2) ?>">Received Item Total
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </td>
                                <td class="text-right total"> <?php echo format_number($received_total, $master['transactionCurrencyDecimalPlaces']) ?></td>
                                <td>&nbsp;</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <br>

                    <div class="modal fade" id="po_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                         data-width="95%" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog modal-lg" style="width: 85%">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title"
                                        id="myModalLabel"> Purchase Order Base </h4>
                                    <!--Purchase Order Base-->
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="box box-widget widget-user-2">
                                                <div class="widget-user-header bg-yellow">
                                                    <h4> Purchase Orders </h4>
                                                    <!--Purchase Orders-->
                                                </div>
                                                <div class="box-footer no-padding">
                                                    <ul class="nav nav-stacked">
                                                        <?php
                                                        if (!empty($supplier_po)) {
                                                            for ($i = 0; $i < count($supplier_po); $i++) {
                                                                $id = 'pull-'.$supplier_po[$i]['purchaseOrderID'];
                                                                echo '<li id="'.$id.'" title="PO Date :- ' . $supplier_po[$i]['documentDate'] . '" rel="tooltip" class="pull-li"><a onclick="fetch_po_detail_table(' . $supplier_po[$i]['purchaseOrderID'] . ')">' . $supplier_po[$i]['purchaseOrderCode'] . ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>';
                                                            }
                                                        } else {
                                                            $norec = $this->lang->line('common_no_records_found');
                                                            echo '<li><a>' . $norec . '<!--No Records found--></a></li>';
                                                        }
                                                        ?>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="hidden" id="purchaseOrderID" name="purchaseOrderID">
                                            <input type="hidden" id="purchaseOrderDetailID" name="purchaseOrderDetailID">
                                            <table class="table table-bordered table-striped table-condesed ">
                                                <thead>
                                                <tr>
                                                    <th colspan='<?php echo (($isGroupBasedTaxEnable == 1)?7:5) ?>>'><?php echo $this->lang->line('common_item'); ?> </th>
                                                    <!--Item-->
                                                    <th colspan='2'>Ordered Item
                                                        (<?php echo $master['transactionCurrency'] ?>)
                                                    </th><!--Ordered Item-->
                                                    <th colspan='3'>Received Item
                                                        (<?php echo $master['transactionCurrency'] ?>)
                                                    </th><!--Received Item-->
                                                <tr>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Code</th><!--Code-->
                                                    <th class="text-left">Description</th>
                                                    <!--Description-->
                                                    <th>UOM</th><!--UOM-->
                                                    <th>Warehouse</th>
                                                    <?php if($isGroupBasedTaxEnable == 1){ ?>
                                                    <th>Tax</th>
                                                    <th>Tax Amount</th>
                                                    <?php }?>
                                                    <th>Qty</th><!--Qty-->
                                                    <th>Cost</th><!--Cost-->
                                                    <th style="width: 13%;">Qty</th><!--Qty-->
                                                    <th>Cost</th><!--Cost-->
                                                    <th>Total</th><!--Total-->
                                                    <th style="display: none;">&nbsp;</th>
                                                </tr>
                                                </thead>
                                                <tbody id="table_body_po">
                                                <tr class="danger">
                                                    <td colspan="10" class="text-center">
                                                        <b><?php echo $this->lang->line('common_no_records_found'); ?> </b>
                                                    </td>
                                                    <!--No Records Found-->
                                                </tr>
                                                </tbody>
                                                <tfoot id="table_tfoot_po">

                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default"
                                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                                    <!--Close-->
                                    <button type="button" class="btn btn-primary"
                                            onclick="save_po_base_items()"><?php echo $this->lang->line('common_save_change'); ?> </button>
                                    <!--Save changes-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div><!-- /.tab-content -->
        <br>
        <!--tax from pv-->
        <br>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="exampleInputName2"
                   id="discount_tot">Discount Applicable Amount
                ( <?php echo number_format($transaction_total, $master['transactionCurrencyDecimalPlaces']); ?>
                )</label>
            <form class="form-inline" id="discount_form">
                <input type="hidden" id="discount_tot_hn" name="discounttotal" value="<?php echo $transaction_total ?>">
                <div class="form-group">
                    <input type="text" id="discdesc" value="Discount" disabled>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control number" value="" id="discountPercentage" name="discountPercentage"
                               style="width: 80px;"
                               onkeyup="cal_discount_general(this.value,<?php echo $transaction_total; ?>)">
                        <input type="hidden" id="discountPercentageTothn" value="<?php echo $master['generalDiscountPercentage']; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control number" value="" id="discount_trans_amount" name="discount_amount"
                           style="width: 100px;"
                           onkeypress="return validateFloatKeyPress(this,event);"
                           onkeyup="cal_discount_amount_general(this.value,<?php echo $transaction_total; ?>)">
                </div>
                <button type="button" onclick="save_general_discount()" class="btn btn-primary"><i class="fa fa-plus"></i></button>
            </form>
        </div>
        <div class="col-md-6">
            <table class="<?php echo table_class(); ?>">Discount Details
                <!--Tax Details-->
                <thead>
                <tr>
                    <th>Type </th>
                    <th>Discount </th>
                    <th>Amount
                        (<?php echo $master['transactionCurrency']; ?>)
                    </th>
                    <td class="text-right">&nbsp;</td>
                </tr>
                </thead>
                <tbody id="discount_table_body_recode">
                    <tr>
                        <td>Discount</td>
                        <td class="text-right"><?php echo number_format($master['generalDiscountPercentage'], 2); ?>%</td>
                        <td class="text-right"><?php echo number_format(($master['generalDiscountPercentage']/100)*$transaction_total, $master['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><a onclick="edit_discount();"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_discount();"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td>
                    </tr>
                <?php
                $discount_total = 0;

                ?>
                </tbody>

            </table>
        </div>
    </div>


    <div aria-hidden="true" role="dialog" id="bsi_item_detail_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 95%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">
                        <?php echo $this->lang->line('accounts_payable_tr_pv_add_item_detail'); ?><!--Add Item Detail--></h5>
                </div>

                <div class="modal-body">
                    <form role="form" id="bsi_item_detail_form" class="form-horizontal">
                        <table class="table table-bordered table-condensed" id="bsi_Item_table">
                            <thead>
                            <tr>
                                <th>
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--> <?php required_mark(); ?>
                                </th>
                                <?php if ($itemBatchPolicy == 1) { ?>
                                   <th>Batch Number</th>
                                <?php } ?>
                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                         <th>Project Category</th>
                                         <th>Project Subcategory</th>
                                <?php } ?>
                                <th>
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                <th style="width: 120px;">
                                    <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <?php required_mark(); ?>
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th colspan="2"
                                    class="directdiscount" style="width: 120px;">
                                    Discount
                                </th>

                                <?php if($isGroupBasedTaxEnable == 1) {?>
                                <th colspan="2"
                                    class="tax" style="width: 120px;">
                                    Tax
                                </th>
                                <?php }?>



                                <th style="width: 120px;">
                                    <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th><?php echo $this->lang->line('common_remarks'); ?><!--Comment--></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs"
                                            onclick="add_more_item()"><i
                                                class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td  style="<?php echo $stylewidth1?>">
                                    <input type="text" class="form-control search input-mini f_search"
                                           name="search[]" id="f_search_1"
                                           placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?>..."
                                           onkeydown="remove_item_all_description(event,this)"><!--Item ID-->
                                    <!--Item Description-->
                                    <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                </td>
                                <td>
                                    <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID" onchange="checkitemavailable(this)" '); ?>
                                </td>

                                <?php if($itemBatchPolicy == 1){ ?>
                                    <td>
                                    <?php echo form_dropdown('batch_number[0][]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_1" multiple="multiple" required'); ?>
                                    </td>
                                <?php } ?>

                                <?php if ($projectExist == 1) { ?>
                                    <td class="form-group" style="<?php echo $stylewidth1?>">
                                        <div class="div_projectID_item">
                                            <select name="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
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
                                <td  style="<?php echo $stylewidth3?>"><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini"  required'); ?>
                                </td>
                                <td><input type="text" onchange="change_amount(this,1),changediscountamount(this,1);"
                                           name="quantityRequested[]"
                                           placeholder="0.00"
                                           onkeyup="validatetb_row(this)"
                                           class="form-control quantityRequested number input-mini" value="0" required>

                                </td>
                                <td>
                                    <input type="text" onchange="change_amount(this,1),changediscountamount(this,1);"
                                           name="estimatedAmount[]"
                                           placeholder="<?php echo $placeholder ?>"
                                           onkeyup="validatetb_row(this)"
                                           value="0"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number estimatedAmount input-mini">

                                </td>
                                <td style="width: 100px;">
                                    <div class="input-group">
                                        <input type="text" name="discount[]" placeholder="0.00" value="0"
                                               onkeyup="cal_discount(this)" onchange="change_amount(this,5)" value="0"
                                               onfocus="this.select();"
                                               class="form-control number discount">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </td>
                                <td style="width: 100px;">
                                    <input type="text" name="discount_amount[]" placeholder="<?php echo $placeholder ?>" value="0"
                                           onkeyup="cal_discount_amount(this)" onchange="change_amount(this,5)"
                                           onfocus="this.select();"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           value="0"
                                           class="form-control number discount_amount">
                                </td>

                            <?php if($isGroupBasedTaxEnable == 1) {?>
                            <td style="width: 100px;">
                                <?php echo form_dropdown('tax_type[]',array(''=>'Select Tax'), '', 'class="form-control tax_type" style="width: 134px;" onchange="load_line_tax_amount(this)" '); ?>
                            </td>

                            <td class="lintax" style="width: 5%;"><span class="linetaxamnt pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>

                            <input type="hidden"
                                           name="netAmount[]"
                                           placeholder="<?php echo $placeholder ?>"
                                           value="0"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number netAmount input-mini">


                            </td>
                            <td>&nbsp;<span class="net_unit_cost pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <?php }else { ?>

                                <td>
                                    <input type="text" onchange="change_amount(this,2),changediscountamount(this);"
                                           name="netAmount[]"
                                           placeholder="<?php echo $placeholder ?>"
                                           value="0"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number netAmount input-mini">

                                </td>

                            <?php }?>



                                <td><textarea class="form-control input-mini" rows="1" name="comment[]"
                                              placeholder="<?php echo $this->lang->line('accounts_payable_tr_pv_item_comment'); ?>..."></textarea>
                                    <!--Item Comment-->
                                </td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="savePaymentVoucher_ID_item(2)">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div aria-hidden="true" role="dialog" tabindex="-1" id="edit_bsi_item_detail_modal" class="modal fade"
         style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('accounts_payable_edit_item_detail'); ?><!--Edit Item Detail--></h4>
                </div>

                <div class="modal-body">
                    <form role="form" id="edit_bsi_item_detail_form" class="form-horizontal">
                        <table class="table table-bordered table-condensed" id="edit_payment_voucher_Item_table">
                            <thead>
                            <tr>
                                <th>
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--> <?php required_mark(); ?>
                                </th>
                                <?php if ($itemBatchPolicy == 1) { ?>
                                   <th>Batch Number</th>
                                <?php } ?>
                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                    <th>Project Category</th>
                                    <th>Project Subcategory</th>

                                <?php } ?>
                                <th>
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                <th style="width: 100px;">
                                    <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                <th style="width: 120px;">
                                    <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <?php required_mark(); ?>
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th colspan="2"
                                    class="directdiscount" style="width: 120px;">
                                    Discount
                                </th>

                                <?php if($isGroupBasedTaxEnable == 1) {?>
                                <th colspan="2"
                                    class="tax" style="width: 120px;">
                                    Tax
                                </th>
                                <?php }?>

                                <th style="width: 120px;">
                                    <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                    (<?php echo $master['transactionCurrency']; ?>)
                                </th>
                                <th><?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="form-group" style="<?php echo $stylewidth1?>">
                                    <input type="text" class="form-control input-mini" name="search"
                                           placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?>..."
                                           id="search"
                                           onkeydown="remove_item_all_description_edit(event,this)"><!--Item ID-->
                                    <!--Item Description-->
                                    <input type="hidden" class="form-control" name="itemAutoID"
                                           id="edit_itemAutoID">

                                </td>
                                <td>
                                    <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2 input-mini" onChange="load_batch_number_single_edit(this)" id="edit_wareHouseAutoID"'); ?>
                                </td>
                                <?php if($itemBatchPolicy == 1){ ?>
                                <td>
                                  <?php echo form_dropdown('batch_number[]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_edit" multiple="multiple" required'); ?>
                                </td>
                                <?php } ?>
                                <?php if ($projectExist == 1) { ?>
                                    <td class="form-group" style="<?php echo $stylewidth1?>">
                                        <div id="edit_div_projectID_item">
                                            <select name="projectID" id="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
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
                                <td style="<?php echo $stylewidth3?>"><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control input-mini"  id="edit_UnitOfMeasureID" required'); ?>
                                </td>
                                <td><input type="text" onchange="change_amount_edit(this,1),changediscountamount_edit(this,1);"
                                           name="quantityRequested" placeholder="0.00"
                                           class="form-control number input-mini" id="edit_quantityRequested"
                                           required>
                                </td>
                                <td>
                                    <input type="text" onchange="change_amount_edit(this,1),changediscountamount_edit(this,1);" name="estimatedAmount"
                                           placeholder="0.00"
                                           class="form-control number input-mini"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           id="edit_estimatedAmount">
                                </td>
                                <td style="width: 100px;" class="directdiscount">
                                    <div class="input-group">
                                        <input type="text" name="discount" placeholder="0.00" value="0"
                                               id="edit_discount" onfocus="this.select();"
                                               onkeyup="edit_cal_discount(this.value)"
                                               onchange="change_amount_edit(this,5)"
                                               class="form-control number">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </td>
                                <td style="width: 100px;" class="directdiscount">
                                    <input type="text" name="discount_amount" id="edit_discount_amount"
                                           placeholder="0.00"
                                           onkeyup="edit_cal_discount_amount()" onchange="change_amount_edit(this,5)"
                                           onfocus="this.select();" value="0"
                                           class="form-control number">
                                </td>


                                <?php if($isGroupBasedTaxEnable == 1) {?>
                                <td class="lintax"><?php echo form_dropdown('tax_type',array(''=>'Select Tax'), '', 'class="form-control tax_type" id="tax_type_edit" style="width: 134px;" onchange="load_line_tax_amount_edit(this)" '); ?></td>

                                <td class="lintax" style="width: 5%;"><span class="pull-right" id="linetaxamnt_edit" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <td>&nbsp;<span id="totalAmount_edit" class="net_amount pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>

                                <input  type="hidden"
                                           id="editNetAmount"
                                           name="netAmount[]" placeholder="0.00"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number netAmount input-mini">


                                </td>

                                <?php } else { ?>
                                    <td>
                                    <input type="text"
                                           onchange="change_amount_edit(this,2),changediscountamount_edit(this);"
                                           id="editNetAmount"
                                           name="netAmount[]" placeholder="0.00"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number netAmount input-mini">
                                </td>

                                <?php }?>






                                <td><textarea class="form-control input-mini" rows="1" name="comment"
                                              placeholder="<?php echo $this->lang->line('accounts_payable_tr_pv_item_comment'); ?>..."
                                              id="edit_comment"></textarea><!--Item Comment-->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="Update_bsi_ID_item()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <br>
        <?php }
        $gl_code_arr = fetch_all_gl_codes(null);
        //$gl_code_arr_income = fetch_all_gl_codes('PLE');
        ?>
        <div aria-hidden="true" role="dialog" id="bsi_st_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 99%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h5 class="modal-title">
                            <?php echo $this->lang->line('common_add_detail'); ?><!--Add Detail--></h5>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="bsi_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed no-color"
                                   id="supplier_invoice_detail_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 150px;">Activity Code</th>
                                    <?php }
                                    if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th style="width: 150px;"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> (<span class="currency"> (LKR)</span>) <?php required_mark(); ?></th>

                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>


                                    <?php if($isGroupBasedTaxEnable == 1) {?>
                                        <th>Tax</th>
                                        <th>Tax Amount</th>
                                    <?php }?>

                                    <th>Net Amount <span class="currency"> (<span class="currency"> (LKR)</span>) <?php required_mark(); ?></th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                                    class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <?php echo form_dropdown('gl_code[]', $gl_code_arr, '', 'class="form-control select2" id="gl_code" required'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd" id="segment_gl" onchange="load_segmentBase_projectID_income(this)"'); ?>
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                            <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                        </td>
                                    <?php }
                                    if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1?>">
                                            <div class="div_projectID_income">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
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
                                    <td>
                                        <input type="text" name="amount[]" id="amount" onfocus="this.select();"
                                            onkeyup="calculateNetAmount(this,'amount')"
                                            class="amount number" onkeypress="return validateFloatKeyPress(this,event)">
                                    </td>
                                    <td><input type="text" name="discountPercentage[]" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'discountPercentage')" value="00" class="form-control number discountPercentage"></td>
                                    <td><input type="text" name="discountAmount[]" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'discountAmount')" value="00" class="form-control number discountAmount"></td>

                                    <?php if($isGroupBasedTaxEnable == 1) {?>
                                        <td style="width: 100px;">
                                            <?php echo form_dropdown('tax_type[]',all_tax_formula_drop_groupByTax(), '', 'class="form-control tax_type select2" style="width: 134px;" onchange="load_line_tax_amount_expenceGL(this)" '); ?>
                                        </td>

                                        <td class="lintax" style="width: 5%;"><span class="linetaxamntExpenceGl pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                    <?php }?>




                                    <td><input type="text" name="Netamount[]" value="00" class="form-control number Netnumber" readonly></td>
                                    <td>
                                        <textarea class="form-control" rows="1" id="description"
                                                  name="description[]"></textarea>
                                    </td>
                                    <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="saveSupplierINvoiceDetails()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save Changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div aria-hidden="true" role="dialog" id="edit_bsi_st_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 90%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h5 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_edit_item_detail'); ?><!--Edit Item Detail--></h5>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="edit_bsi_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed no-color"
                                   id="edit_supplier_invoice_detail_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <!--GL Code--><?php echo $this->lang->line('common_gl_code'); ?> <?php required_mark(); ?></th>
                                    <th><!--Segment--><?php echo $this->lang->line('common_segment'); ?></th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 150px;">Activity Code</th>
                                    <?php } 
                                    if ($projectExist == 1) { ?>
                                        <th>
                                            <!--Project--><?php echo $this->lang->line('common_project'); ?> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> (<span
                                                class="currency"> (LKR)</span>) <?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <?php if($isGroupBasedTaxEnable == 1) {?>
                                        <th>Tax</th>
                                        <th>Tax Amount</th>
                                    <?php }?>

                                    <th>Net Amount <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <?php echo form_dropdown('gl_code', $gl_code_arr, '', 'class="form-control select2" id="edit_gl_code" required'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="edit_segment_gl" onchange="load_segmentBase_projectID_incomeEdit(this)"'); ?>
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                            <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                        </td>
                                    <?php } 
                                    if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1?>">
                                            <div id="edit_div_projectID_item1">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2?>">
                                            <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2?>">
                                            <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount" id="edit_amount" onfocus="this.select();"
                                               onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'amount')"
                                               class="form-control number">
                                    </td>
                                    <td><input type="text" name="discountPercentage" id="discountPercentage_edit" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00" class="form-control number "></td>
                                    <td><input type="text" name="discountAmount" id="discountAmount_edit" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00" class="form-control number "></td>

                                    <?php if($isGroupBasedTaxEnable == 1) {?>
                                        <td style="width: 100px;">
                                            <?php echo form_dropdown('tax_type',all_tax_formula_drop_groupByTax(), '', 'class="form-control tax_type" id="tax_type_edit_expenceGL" style="width: 134px;" onchange="load_line_tax_amount_expenceGL_edit(this)" '); ?>
                                        </td>

                                        <td class="lintax" style="width: 5%;"><span class="linetaxamntExpenceGl_edit pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                    <?php }?>
                                    <td><input type="text" name="Netamount" id="Netamount_edit" value="00" class="form-control number " readonly></td>
                                    <td>
                                        <textarea class="form-control" rows="1" id="edit_description"
                                                  name="description"></textarea>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="UpdateSupplierINvoiceDetails()">
                            <?php echo $this->lang->line('common_update_changes'); ?><!--Update Changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div aria-hidden="true" role="dialog" id="edit_bsi_po_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 80%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h5 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_edit_item_detail'); ?><!--Edit Item Detail--></h5>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="edit_bsi_po_detail_form" class="form-horizontal">
                            <input type="hidden" name="purchaseOrderDetailsID" id="purchaseOrderDetailsIDbsi">
                            <input type="hidden" name="purchaseOrderMasterID" id="purchaseOrderMasterIDbsi">
                            <table class="table table-bordered table-condensed no-color"
                                   id="edit_supplier_invoice_po_detail_table">
                                <thead>
                                <tr>

                                    <th style="width: 250px;">Warehouse <?php required_mark(); ?></th>
                                    <th style="width: 100px;">Order Qty</th>
                                    <th style="width: 100px;">Qty <?php required_mark(); ?></th>
                                    <th>Unit Cost (<span class="currency"> (LKR)</span>) <?php required_mark(); ?></th>

                                    <?php if($isGroupBasedTaxEnable == 1){?>
                                        <th>Tax </th>
                                        <th>Tax Amount</th>
                                    <?php }?>

                                    <th style="width: 150px;">Net Amount (<span
                                                class="currency"> (LKR)</span>) <?php required_mark(); ?></th>
                                    <th style="width: 200px;">Description <?php required_mark(); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID', $location_arr, $location_arr_default, 'class="form-control select2" id="EditwareHouseAutoID" required'); ?>
                                    </td>
                                    <td><input id="editOrdQty" class="form-control number" readonly type="text"/></td>
                                    <td >
                                        <input type="text" onchange="po_edit_amount_cal(1)" name="requestedQty"
                                               id="edit_qtyPO" onfocus="this.select();"
                                               onkeyup="qtyValidation();"
                                               class="form-control number">
                                    </td>
                                    <?php if($isGroupBasedTaxEnable == 1){?>
                                    <td style="width: 10%;">
                                        <input type="text"  id="edit_UnitamountPO"   name="unittransactionAmount" class="form-control number" readonly>
                                    </td>
                                    <?php }else {?>
                                    <td style="width: 10%;">
                                        <input type="text" onchange="po_edit_amount_cal(2)" name="unittransactionAmount"
                                               id="edit_UnitamountPO" onfocus="this.select();"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number">  </td>
                                    <?php }?>


                                    <?php if($isGroupBasedTaxEnable == 1){?>
                                    <td>
                                        <span id="taxCalculationFormuladescription" class="pull-left" style="font-size: 12px;margin-top: 7px;">-</span>
                                        <input type="hidden" id="taxCalculationFormulaIDBSI" name="taxCalculationFormulaIDBSI">
                                    </td>
                                    <td style="width: 10%;">
                                        <span id="taxamountBSI" class="pull-right" style="font-size: 12px;margin-top: 7px;">0.00</span>

                                    </td>

                                        <td>
                                            <input type="text"  name="transactionAmount"
                                                   id="edit_transactionAmountPO" onfocus="this.select();"
                                                   class="form-control number" readonly>
                                        </td>

                                    <?php }else { ?>
                                        <td>
                                            <input type="text" onchange="po_edit_amount_cal(3)" name="transactionAmount"
                                                   id="edit_transactionAmountPO" onfocus="this.select();"
                                                   onkeypress="return validateFloatKeyPress(this,event)"
                                                   class="form-control number">
                                        </td>
                                    <?php }?>



                                    <td>
                                        <textarea class="form-control" rows="1" id="edit_descriptionPO"
                                                  name="description"></textarea>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="UpdateSupplierINvoicePODetails()">
                            <?php echo $this->lang->line('common_update_changes'); ?><!--Update Changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($isGroupBasedTaxEnable != 1) { ?>
        <div class="row">
            <div class="col-md-6">
                <label for="exampleInputName2">
                    <?php
                    $discunttot=($master['generalDiscountPercentage']/100)*$transaction_total;
                    ?>
                    <?php echo $this->lang->line('accounts_payable_tax_applicable_amount'); ?><!--Tax Applicable Amount-->
                    ( <?php echo number_format($transaction_total-$discunttot, $master['transactionCurrencyDecimalPlaces']); ?>
                    ) </label>

                <form class="form-inline" id="tax_form">
                    <input type="hidden" id="isGroupBasedTax" name="isGroupBasedTax" value="<?php echo $isGroupBasedTaxEnable?>">
                    <div class="form-group">
                        <?php $amnt=$transaction_total-$discunttot;
                        $taxDrop = all_tax_drop();
                        if($isGroupBasedTaxEnable ==1){
                            $taxDrop = all_tax_formula_drop_groupByTax();
                        }

                        echo form_dropdown('text_type', $taxDrop, '', 'class="form-control" id="text_type" required onchange="select_text(this,' . $amnt . ','.$isGroupBasedTaxEnable.')" style="width: 200px;"'); ?>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="hidden" class="form-control number" id="afterdiscounttot" name="afterdiscounttot" value="<?php echo $transaction_total-$discunttot; ?>">
                            <input type="text" class="form-control number percentage" id="percentage" name="percentage"
                                   style="width: 80px;" onkeyup="cal_tax(this.value,<?php echo $transaction_total-$discunttot; ?>)">
                            <span class="input-group-addon percentage">%</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                               style="width: 100px;"
                               onkeypress="return validateFloatKeyPress(this,event);"
                               onkeyup="cal_tax_amount(this.value,<?php echo $transaction_total-$discunttot; ?>)">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                </form>
            </div>
            <div class="col-md-6">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('accounts_payable_tax_type'); ?><!--Tax Type--></th>
                        <th><?php echo $this->lang->line('accounts_payable_tax_detail'); ?><!--Tax Detail--></th>
                        <th><?php echo $this->lang->line('accounts_payable_tax'); ?><!--Tax--></th>
                        <th><?php echo $this->lang->line('common_amount'); ?><!--Amount-->
                            (<?php echo $master['transactionCurrency']; ?>)
                        </th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <body>
                    <?php $tax_total = 0;
                    if (!empty($detail['tax'])) {
                        for ($i = 0; $i < count($detail['tax']); $i++) {
                            echo '<tr>';
                            echo '<td>' . ($i + 1) . '</td>';
                            echo '<td>' . $detail['tax'][$i]['taxShortCode'] . '</td>';
                            echo '<td>' . $detail['tax'][$i]['taxDescription'] . '</td>';
                            echo '<td class="text-right">' .number_format( $detail['tax'][$i]['taxPercentage'], $master['transactionCurrencyDecimalPlaces']) . ' % </td>';
                            echo '<td class="text-right">' . number_format((($detail['tax'][$i]['taxPercentage'] / 100) * ($transaction_total-$discunttot)), $master['transactionCurrencyDecimalPlaces']) . '</td>';
                            // echo '<td class="text-right">'.format_number($detail['detail'][$i]['companyLocalAmount'],$master['companyLocalCurrencyDecimalPlaces']).'</td>';
                            // echo '<td class="text-right">'.format_number($detail['detail'][$i]['supplierAmount'],$master['supplierCurrencyDecimalPlaces']).'</td>';
                            echo '<td class="text-right"><a onclick="delete_tax(' . $detail['tax'][$i]['taxDetailAutoID'] . ',\'' . $detail['tax'][$i]['taxShortCode'] . '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td>';
                            echo '</tr>';
                            $tax_total += ($detail['tax'][$i]['taxPercentage'] / 100) * ($transaction_total-$discunttot);
                        }
                    } else {
                        $norec = $this->lang->line('common_no_records_found');
                        echo '<tr class="danger"><td colspan="6" class="text-center"><b>' . $norec . '<!--No Records Found--></b></td></tr>';
                    }
                    ?>
                    </body>
                    <tfoot>
                    <?php
                    $taxtotlanguage = $this->lang->line('accounts_payable_tax_total');
                    if (!empty($detail['tax'])) {
                        echo '<tr>';
                        echo '<td class="text-right" colspan="4">' . $taxtotlanguage . '<!--Tax Total--> ( ' . $master['transactionCurrency'] . ' )</td>';
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
    <hr>
    <div class="text-right m-t-xs">
        <!-- <button class="btn btn-default prev" onclick="">Previous</button> -->
        <!-- <button class="btn btn-primary next" onclick="load_conformation();" >Save & Next</button> -->
    </div>
    <script type="text/javascript">
        var search_id = 1;
        var InvoiceDetailAutoID;
        var invoiceType;
        var supplierID;
        var currencyID;
        var projectID;
        var projectcategory;
        var projectsubcat;
        var defaultSegment = <?php echo json_encode($this->common_data['company_data']['default_segment']); ?>;
        var showPurchasePrice = <?php echo $showPurchasePrice ?>;
        var isGroupBasedTaxEnable = 0;
        var select_VAT_value = '';
        var currentEditWareHouseAutoID='';
        var currentEditTextBatchData='';
        $(document).ready(function () {
            <?php if (!empty($detail['poDetail'])) { ?>
                $('.tab_3_Item_BSI').removeClass('hide');
            <?php } ?>
            $('.select2').select2();

            InvoiceDetailAutoID = null;
            projectID = null;
            InvoiceAutoID = <?php echo json_encode(trim($InvoiceAutoID)); ?>;
            invoiceType = <?php echo json_encode(trim($master['invoiceType'] ?? '')); ?>;
            supplierID = <?php echo json_encode(trim($master['supplierID'] ?? '')); ?>;
            currencyID = <?php echo json_encode(trim($master['transactionCurrencyID'] ?? '')); ?>;
            isGroupBasedTaxEnable = <?php echo json_encode(trim($isGroupBasedTaxEnable)); ?>;
            $('.currency').text(<?php echo json_encode(trim($master['transactionCurrency'] ?? '')); ?>);
            //initializeitemTypeahead();

            number_validation();

            $('#tax_form').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    text_type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tax_type_is_required');?>.'}}},/*Tax Type is required*/
                    percentage: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_percentage_is_required');?>.'}}}/*Percentage is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                data.push({'name': 'InvoiceAutoID', 'value': InvoiceAutoID});
                data.push({'name': 'transactionCurrency', 'value': currencyID});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Payable/save_bsi_tax_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $form.bootstrapValidator('resetForm', true);
                        InvoiceDetailAutoID = null;
                        refreshNotifications(true);
                        if (data['status']) {
                            setTimeout(function () {
                                fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID);
                            }, 300);
                        }
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
            $('.extraColumns input').iCheck({
                checkboxClass: 'icheckbox_square_relative-blue',
                radioClass: 'iradio_square_relative-blue',
                increaseArea: '20%'
            });
        });

        function cal_tax_amount(discount_amount, total_amount) {
            if (total_amount && discount_amount) {
                $('#percentage').val(((parseFloat(discount_amount) / total_amount) * 100).toFixed(currency_decimal));
            } else {
                $('#percentage').val(0);
            }
        }

        function cal_tax(discount, total_amount) {
            if (total_amount && discount) {
                $('#tax_amount').val(((total_amount / 100) * parseFloat(discount)).toFixed(currency_decimal));
            } else {
                $('#tax_amount').val(0);
            }
        }

        function load_supplier_detail_modal() {
            if (InvoiceAutoID) {
                $('#bsi_detail_form')[0].reset();
                $('#gl_code').val('').change();
                $('.linetaxamntExpenceGl').html('0.00');
                $('.segment_glAdd').val(defaultSegment).change();
                $('#supplier_invoice_detail_table tbody tr').not(':first').remove();
                $("#bsi_st_detail_modal").modal({backdrop: "static"});
            }
        }

        function select_check_box(data, id, total, decimal) {
            $("#check_" + id).prop("checked", false);
            if (data >= 0) {
                if (total >= data) {
                    $("#check_" + id).prop("checked", true);
                } else {
                    $("#check_" + id).prop("checked", false);
                    $("#amount_" + id).val('');
                    myAlert('w', '<?php echo $this->lang->line('accounts_payable_you_canot_enter_an_invoice');?>');/*You can not enter an invoice amount greater than selected GRV Balance Amount*/
                }
            }
        }

        function select_text(data, total_amount,isGroupBased) {

            if (data.value != 0) {
                var result = $('#text_type option:selected').text().split('|');
                $('#percentage').val(parseFloat(result[2]));
                cal_tax(parseFloat(result[2]), total_amount);
                $('#tax_form').bootstrapValidator('revalidateField', 'percentage');
            }
        }

        function save_grv_base_items() {
            var selected = [];
            var amount = [];
            var match = [];
            var grv = [];
            $('#table_body input:checked').each(function () {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                match.push($('#match_' + $(this).val()).val());
                grv.push($('#grv_' + $(this).val()).val());
            });
            if (!jQuery.isEmptyObject(selected)) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'grvAutoID': grv, 'InvoiceAutoID': InvoiceAutoID, 'amounts': amount, 'match': match},
                    url: "<?php echo site_url('Payable/save_grv_base_items'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        $('#grv_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID);
                        }, 300);
                    }, error: function () {
                        $('#grv_base_modal').modal('hide');
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            }
        }

        function delete_item(id, value) {
            if (InvoiceAutoID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('accounts_payable_trans_are_you_want_to_delete');?>",/*You want to delete this file*/
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
                            data: {'InvoiceDetailAutoID': id,'InvoiceAutoID': InvoiceAutoID},
                            url: "<?php echo site_url('Payable/delete_bsi_detail'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                refreshNotifications(true);
                                setTimeout(function () {
                                    fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID, value);
                                }, 300);
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
        }

        function delete_tax(id, value) {
            if (InvoiceAutoID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('accounts_payable_trans_are_you_want_to_delete');?>",/*You want to delete this file!*/
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
                            data: {'taxDetailAutoID': id},
                            url: "<?php echo site_url('Payable/delete_tax_detail'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                refreshNotifications(true);
                                setTimeout(function () {
                                    fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID);
                                }, 300);
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
            ;
        }

        function edit_item(id) {
            if (InvoiceAutoID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('accounts_payable_you_want_to_edit_this_file');?>",/*You want to edit this file!*/
                        type: "warning",
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
                            data: {'InvoiceDetailAutoID': id},
                            url: "<?php echo site_url('Payable/fetch_bsi_detail'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                InvoiceDetailAutoID = data['InvoiceDetailAutoID'];
                                projectID = data['projectID'];

                                projectcategory = data['project_categoryID'];
                                projectsubcat = data['project_subCategoryID'];
                                /*$('#search').val(data['itemDescription'] +" ("+data['itemCode']+")");
                                 $('#umo').val(data['unitOfMeasure']);
                                 $('#quantityRequested').val(data['receivedQty']);
                                 $('#estimatedAmount').val(data['receivedAmount']);
                                 $('#itemPrimaryCode').val(data['itemPrimaryCode']);
                                 $('#search_id').val(data['itemCode']);
                                 $('#itemCode').val(data['itemCode']);
                                 $('#itemCodeSystem').val(data['itemPrimaryCode']);
                                 $('#itemDescription').val(data['itemDescription']);
                                 $('#comment').val(data['comment']);
                                 $('#remarks').val(data['remarks']);*/

                                load_segmentBase_projectID_glEdit(data['segmentID'], data['projectID']);
                                $('#edit_gl_code').val(data['GLAutoID']).change();
                                if(<?php echo $advanceCostCapturing; ?> == 1){
                                    $('#edit_activityCode').val(data['activityCodeID']).change();
                                }
                                $('#edit_description').val(data['description']);
                                $('#tax_type_edit_expenceGL').val(data['taxCalculationformulaID']);
                                $('.linetaxamntExpenceGl_edit').html((parseFloat(data['taxAmountledger'])).toFixed(currency_decimal));
                                $('#edit_amount').val((parseFloat(data['transactionAmount'])+parseFloat(data['discountAmount'])).toFixed(currency_decimal));
                                $('#discountPercentage_edit').val(parseFloat(data['discountPercentage']).toFixed(2));
                                $('#discountAmount_edit').val(parseFloat(data['discountAmount']).toFixed(currency_decimal));
                                $('#Netamount_edit').val((parseFloat(data['transactionAmount'])+parseFloat(data['taxAmountledger'])).toFixed(currency_decimal));

                                $('#edit_segment_gl').val(data['segmentID'] + '|' + data['segmentCode']).change();
                                $("#edit_bsi_st_detail_modal").modal({backdrop: "static"});
                                stopLoad();
                                //refreshNotifications(true);
                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Try Again ", "error");
                            }
                        });
                    });
            }
        }

        function edit_item_po(id) {
            if (InvoiceAutoID) {
                swal({
                        title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                        text: "<?php echo $this->lang->line('accounts_payable_you_want_to_edit_this_file');?>",/*You want to edit this file!*/
                        type: "warning",
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
                            data: {'InvoiceDetailAutoID': id},
                            url: "<?php echo site_url('Payable/fetch_bsi_detail'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                InvoiceDetailAutoID = data['InvoiceDetailAutoID'];
                                projectID = data['projectID'];

                                $('#edit_qtyPO').val(data['requestedQty']);
                                $('#editOrdQty').val(data['orderedQty']);
                                $('#EditwareHouseAutoID').val(data['wareHouseAutoID']).change();
                                $('#edit_UnitamountPO').val(parseFloat(data['unittransactionAmount']).toFixed(currency_decimal) );
                                $('#edit_transactionAmountPO').val( ( parseFloat(data['transactionAmount']) + parseFloat(data['taxAmountledger'])).toFixed(currency_decimal) );
                                $('#edit_descriptionPO').val(data['description']);
                                $('#taxCalculationFormuladescription').html(data['taxdescription']);
                                $('#taxamountBSI').html(data['taxAmountledger']);
                                $('#taxCalculationFormulaIDBSI').val(data['taxCalculationformulaID']);

                                $('#purchaseOrderDetailsIDbsi').val(data['purchaseOrderDetailsID']);
                                $('#purchaseOrderMasterIDbsi').val(data['purchaseOrderMastertID']);
                                $("#edit_bsi_po_detail_modal").modal({backdrop: "static"});
                                stopLoad();
                                //refreshNotifications(true);
                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Try Again ", "error");
                            }
                        });
                    });
            }
        }

        function add_more() {
            $('select.select2').select2('destroy');
            var appendData = $('#supplier_invoice_detail_table tbody tr:first').clone();

            appendData.find('input').val('');
            appendData.find('textarea').val('');
            appendData.find('.linetaxamntExpenceGl').html('0.00');

            appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
            $('#supplier_invoice_detail_table').append(appendData);
            var lenght = $('#supplier_invoice_detail_table tbody tr').length - 1;

            $(".select2").select2();
            number_validation();
        }

        $(document).on('click', '.remove-tr', function () {
            $(this).closest('tr').remove();
        });

        function saveSupplierINvoiceDetails() {
            var $form = $('#bsi_detail_form');
            var data = $form.serializeArray();
            data.push({'name': 'InvoiceAutoID', 'value': InvoiceAutoID});
            data.push({'name': 'InvoiceDetailAutoID', 'value': InvoiceDetailAutoID});

            $('select[name="gl_code[]"] option:selected').each(function () {
                data.push({'name': 'gl_code_des[]', 'value': $(this).text()})
            })

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payable/save_bsi_detail_multiple'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    stopLoad();
                    if (data[0] == 's') {
                        InvoiceDetailAutoID = null;
                        $('#bsi_detail_form')[0].reset();
                        $("#segment_gl").select2("");
                        $("#activityCode").select2("");
                        $("#gl_code").select2("");
                        fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID);
                        $('#bsi_st_detail_modal').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }


        function UpdateSupplierINvoiceDetails() {
            var $form = $('#edit_bsi_detail_form');
            var data = $form.serializeArray();
            data.push({'name': 'InvoiceAutoID', 'value': InvoiceAutoID});
            data.push({'name': 'InvoiceDetailAutoID', 'value': InvoiceDetailAutoID});
            data.push({'name': 'gl_code_des', 'value': $('#edit_gl_code option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payable/save_bsi_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    stopLoad();
                    if (data[0] == 's') {
                        InvoiceDetailAutoID = null;
                        $('#edit_bsi_detail_form')[0].reset();
                        $("#segment_gl").select2("");
                        $("#activityCode").select2("");
                        $("#gl_code").select2("");
                        fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID);
                        $('#edit_bsi_st_detail_modal').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function qtyValidation() {
            var edit_qtyPO = parseFloat($("#edit_qtyPO").val());
            var editOrdQty = parseFloat($("#editOrdQty").val());
            if(editOrdQty<edit_qtyPO){
                swal("Warning", "Qty cannot be greater than PO balance Qty!", "warning");
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
                    $('.select2').select2();
                    stopLoad();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    stopLoad();
                }
            });
        }

        function load_segmentBase_projectID_incomeEdit(segment) {
            var type = 'item';
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Procurement/load_project_segmentBase"); ?>',
                dataType: 'html',
                data: {segment: segment.value, type: type},
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


        function bsi_item_detail_modal() {
            if (InvoiceAutoID) {
                $('.search').typeahead('destroy');
                $('select[name="wareHouseAutoID[]"]').val('').change();
                $('#bsi_item_detail_form')[0].reset();
                $('#bsi_Item_table tbody tr').not(':first').remove();
                initializeitemTypeahead(1);
                load_segmentBase_projectID_item();
                $('.f_search').closest('tr').css("background-color", 'white');
                $('.quantityRequested').closest('tr').css("background-color", 'white');
                $('.estimatedAmount').closest('tr').css("background-color", 'white');
                $('.wareHouseAutoID').closest('tr').css("background-color", 'white');
                $("#bsi_item_detail_modal").modal({backdrop: "static"});
            }
        }

        function getItemBatchDetails(itemAutoID,id,wareHouseAutoID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemId': itemAutoID,'wareHouseAutoID':wareHouseAutoID},
                url: "<?php echo site_url('Inventory/fetch_batch_details_byId'); ?>",
                success: function (data) {
                    $('#batch_number_'+id).empty();
                    var mySelect = $('#batch_number_'+id);
                    mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                    /*Select batch*/
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']+' - - '+text['batchExpireDate']));
                        });
                        
                    }
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }

        function initializeitemTypeahead(id) {
             var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
            $('#f_search_' + id).autocomplete({
                serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN',
                onSelect: function (suggestion) {

                    // if(itemBatchPolicy==1){
                    //   getItemBatchDetails(suggestion.itemAutoID,id);
                    // }

                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    }, 200);
                    fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                    if(showPurchasePrice == 1){
                        fetch_purchase_price(suggestion.companyLocalPurchasingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);

                    }

                    if(isGroupBasedTaxEnable == 1){
                        fetch_line_tax_and_vat(suggestion.itemAutoID, this);
                    }

                    $(this).closest('tr').find('.quantityRequested').focus();
                    $(this).closest('tr').css("background-color", 'white');
                }
            });
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


        function checkitemavailable(det) {
            var itmID = $(det).closest('tr').find('.itemAutoID').val();
            var warehouseid = det.value;
            var searchID = $(det).closest('tr').find('.f_search').attr('id');
            var concatarr = new Array();

            var arrSearchID =searchID.split("f_search_");
            var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
            if(itemBatchPolicy==1){

                if(itmID!="" && warehouseid!="" && arrSearchID.length>0){
                    getItemBatchDetails(itmID,arrSearchID[1],warehouseid);
                }
                
            }
            
            if (itmID && warehouseid) {
                var mainconcat = itmID.concat('|').concat(warehouseid);
            }

            $('.itemAutoID').each(function () {
                if (this.value) {
                    var itm = this.value;
                    var wareHouseAutoID = $(this).closest('tr').find('.wareHouseAutoID').val();
                    var concatvalue = itm.concat('|').concat(wareHouseAutoID);
                    if (mainconcat) {
                        concatarr.push(concatvalue);
                    }
                }
            });
            if (concatarr.length > 1) {
                if (jQuery.inArray(mainconcat, concatarr)) {

                } else {
                    $(det).closest('tr').find('.f_search').val('');
                    $(det).closest('tr').find('.itemAutoID').val('');
                    $(det).closest('tr').find('.wareHouseAutoID').val('').change();
                    $(det).closest('tr').find('.quantityRequested').val('');
                    $(det).closest('tr').find('.estimatedAmount').val('');
                    $(det).closest('tr').find('.netAmount').val('');
                    $(det).closest('tr').find('.umoDropdown').val('');
                    $(det).closest('tr').find('.b_number').val('').change();
                    myAlert('w', 'Selected item is already selected');
                }
            }
            if (det.value > 0) {
                $(det).closest('tr').css("background-color", 'white');
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

        function change_amount(field, val) {
            var quantityRequested = 0;
            var estimatedAmount = 0;
            var netAmount = 0;
            var discount = 0;
            var discount_percentage = 0;
            var discount_amount_p = 0;
            var totamt = 0;
            var estimatedAmounttotal = 0;


            quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
            estimatedAmount = $(field).closest('tr').find('.estimatedAmount').val();
            discount = $(field).closest('tr').find('.discount_amount').val();
            discount_percentage = $(field).closest('tr').find('.discount').val();
            netAmount = $(field).closest('tr').find('.netAmount').val();

            if ((quantityRequested != '')) {
                if (quantityRequested == 0) {
                    swal("Cancelled", "Qty should be greater than 0", "error");
                    $(field).closest('tr').find('.netAmount').val('');
                    $(field).closest('tr').find('.estimatedAmount').val('');
                    $(field).closest('tr').find('.discount_amount').val('');
                    $(field).closest('tr').find('.discount').val('');
                } else {
                    if (val == 1) {

                        totamt = (parseFloat(quantityRequested) * parseFloat(estimatedAmount));
                        if (discount != '') {
                            $(field).closest('tr').find('.netAmount').val((parseFloat(totamt) - parseFloat(discount)).toFixed(currency_decimal));
                        } else {
                            $(field).closest('tr').find('.netAmount').val((parseFloat(totamt)).toFixed(currency_decimal));
                        }
                    }
                    if (val == 2) {
                        $(field).closest('tr').find('.estimatedAmount').val(parseFloat(netAmount) / parseFloat(quantityRequested));
                    }
                    if (val == 5) {
                        quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
                        estimatedAmount = $(field).closest('tr').find('.estimatedAmount').val();
                        discount = $(field).closest('tr').find('.discount_amount').val();
                        var totamt = ((quantityRequested * estimatedAmount) - discount);
                        $(field).closest('tr').find('.netAmount').val(totamt);
                    }
                }

            } else {
                swal("Cancelled", "Qty should be greater than 0", "error");
                $(field).closest('tr').find('.netAmount').val('');
                $(field).closest('tr').find('.estimatedAmount').val('');
                $(field).closest('tr').find('.discount_amount').val('');
                $(field).closest('tr').find('.discount').val('');
            }


            /* if((discount == 0)||(discount == ''))
             {
                 $(field).closest('tr').find('.discount_amount').val('');
                 $(field).closest('tr').find('.discount').val('');

             }*/
            estimatedAmounttotal = (netAmount / quantityRequested);
             if(isGroupBasedTaxEnable == 1){
                load_line_tax_amount(field)
             }

        }

        function change_amount_edit(field, val) {
            var quantityRequested = 0;
            var estimatedAmount = 0;
            var editNetAmount = 0;
            var editdiscout = 0;
            var netAmount = 0;

            quantityRequested = $('#edit_quantityRequested').val();
            estimatedAmount = $('#edit_estimatedAmount').val();
            editdiscout = $('#edit_discount_amount').val();
            netAmount = $('#editNetAmount').val();


            if ((quantityRequested != '')) {
                if (quantityRequested == 0) {
                    swal("Cancelled", "Qty should be greater than 0", "error");
                    $('#edit_estimatedAmount').val('');
                    $('#edit_discount_amount').val('');
                    $('#editNetAmount').val('');
                    $('#edit_discount').val('');

                } else {
                    if (val == 1) {

                        totamt = (parseFloat(quantityRequested) * parseFloat(estimatedAmount));
                        if (editdiscout != '') {
                            $('#editNetAmount').val((parseFloat(totamt) - parseFloat(editdiscout)).toFixed(currency_decimal));
                        } else {
                            $('#editNetAmount').val((parseFloat(totamt)).toFixed(currency_decimal));
                        }

                    }
                    if (val == 2) {
                        $('#edit_estimatedAmount').val(((netAmount) / (quantityRequested)));
                    }
                    if (val == 5) {
                        var totamt = ((quantityRequested * estimatedAmount) - editdiscout);
                        $('#editNetAmount').val(totamt);
                    }
                }

            } else {
                swal("Cancelled", "Qty should be greater than 0", "error");
                $('#edit_estimatedAmount').val('');
                $('#edit_discount_amount').val('');
                $('#editNetAmount').val('');
                $('#edit_discount').val('');
            }
            if(isGroupBasedTaxEnable == 1){
            load_line_tax_amount_edit(field);
            }

        }

        function cal_amounts(field, val) {
            var quantityRequested = 0;
            var netAmount = 0;
            var discount = 0;
            var discount_percentage = 0;
            var unitamt = 0;
            var esitmatedamt = 0;

            quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
            discount = $(field).closest('tr').find('.discount_amount').val();

            netAmount = $(field).closest('tr').find('.netAmount ').val();
            discount_percentage = $(field).closest('tr').find('.discount').val();
            esitmatedamt = $(field).closest('tr').find('.estimatedAmount').val();
            if (discount != '') {
                unitamt = (((parseFloat(netAmount)) + parseFloat(discount)) / parseFloat(quantityRequested));
            } else if (quantityRequested != '') {
                unitamt = (((parseFloat(netAmount))) / parseFloat(quantityRequested));
            }
            if (quantityRequested == '') {

            }
            $(field).closest('tr').find('.estimatedAmount').val((unitamt));

        }

        function changediscountamount(field, val) {
            var quantityRequested = 0;
            var estimatedAmount = 0;
            var netAmount = '';
            var discount = 0;
            var discount_amt = 0;
            var discount_percentage = 0;
            var totalamt = 0;
            if ((discount_percentage > 0) && ((discount == 0) || (discount == ''))) {
                $(field).closest('tr').find('.discount_amount').val('');
                $(field).closest('tr').find('.discount').val('');
                //$(field).closest('tr').find('.estimatedAmount').val('');
            }
            quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
            estimatedAmount = $(field).closest('tr').find('.estimatedAmount').val();
            netAmount = $(field).closest('tr').find('.netAmount ').val();
            discount = $(field).closest('tr').find('.discount_amount').val();
            discount_percentage = $(field).closest('tr').find('.discount').val();
            if (val == 1) {
                discount_amt = ((parseFloat(discount)/((parseFloat(quantityRequested))*(parseFloat(estimatedAmount))))*100)
              if(estimatedAmount == 0)
              {
                  $(field).closest('tr').find('.discount').val(0);
              }else
              {
                  $(field).closest('tr').find('.discount').val(discount_amt);
              }
            } else {
                totalamt = ((parseFloat(netAmount) + (parseFloat(discount))) / parseFloat(quantityRequested));
                if ((quantityRequested == '') || (quantityRequested == 0)) {
                    $(field).closest('tr').find('.estimatedAmount').val(0);
                    $(field).closest('tr').find('.discount').val(0);
                    $(field).closest('tr').find('.discount_amount').val(0);
                    $(field).closest('tr').find('.netAmount').val(0);
                } else
                {
                    $(field).closest('tr').find('.estimatedAmount').val(totalamt);
                }
            }
        }

        function changediscountamount_edit(field,val) {
            var quantityRequested = 0;
            var estimatedAmount = 0;
            var netAmount = '';
            var discount = 0;
            var discount_percentage = 0;
            var totalamt = 0;
            var discount_amt = 0;

            quantityRequested = $('#edit_quantityRequested').val();
            estimatedAmount = $('#edit_estimatedAmount').val();
            discount = $('#edit_discount_amount').val();
            netAmount = $('#editNetAmount').val();

            if (val == 1) {
                discount_amt = ((parseFloat(discount)/((parseFloat(quantityRequested))*(parseFloat(estimatedAmount))))*100)
                if(estimatedAmount == 0)
                {
                    $('#edit_discount').val(0);
                }else
                {
                    $('#edit_discount').val(discount_amt);
                }
            } else {
                totalamt = ((parseFloat(netAmount) + (parseFloat(discount))) / parseFloat(quantityRequested));
                $('#edit_estimatedAmount').val((totalamt));
            }
        }

        function validatetb_row(det) {
            if (det.value > 0) {
                $(det).closest('tr').css("background-color", 'white');
            }
        }

        function savePaymentVoucher_ID_item(tabid) {
            var $form = $('#bsi_item_detail_form');
            var data = $form.serializeArray();
            if (InvoiceAutoID) {
                data.push({'name': 'InvoiceAutoID', 'value': InvoiceAutoID});
                data.push({'name': 'InvoiceDetailAutoID', 'value': InvoiceDetailAutoID});

                $('select[name="wareHouseAutoID[]"] option:selected').each(function () {
                    data.push({'name': 'wareHouse[]', 'value': $(this).text()})
                })
                $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                    data.push({'name': 'uom[]', 'value': $(this).text()})
                });
                $('.itemAutoID').each(function () {
                    if (this.value == '') {
                        $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    }
                });
                $('.quantityRequested').each(function () {
                    if (this.value == '' || this.value == 0) {
                        $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    }
                });
                $('.estimatedAmount').each(function () {
                    if (this.value == '' || this.value == 0) {
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
                    url: "<?php echo site_url('Payable/save_bsi_item_detail_multiple'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            InvoiceDetailAutoID = null;
                            $('#bsi_item_detail_form')[0].reset();
                            setTimeout(function () {
                                $('#bsi_item_detail_modal').modal('hide');
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                                fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID, tabid);
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


        function edit_bsi_item(id, value) {
            $("#edit_wareHouseAutoID").val('').change();
            $('#edit_bsi_item_detail_form')[0].reset();
            currentEditWareHouseAutoID='';
            var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                    type: "warning",
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
                        data: {'InvoiceDetailAutoID': id},
                        url: "<?php echo site_url('Payable/fetch_bsi_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            currentEditWareHouseAutoID=data['wareHouseAutoID'];
                            currentEditTextBatchData=data['batchNumber'];

                            if(itemBatchPolicy==1){
                                //select_edit_item_batch_details(data['batchNumber'],data['itemAutoID']);
                                var textBatchData=data['batchNumber'];
                                $.ajax({
                                    async: true,
                                    type: 'post',
                                    dataType: 'json',
                                    data: {'itemId': data['itemAutoID'],'wareHouseAutoID':data['wareHouseAutoID']},
                                    url: "<?php echo site_url('Inventory/fetch_batch_details_byId'); ?>",
                                    success: function (data) {
                                        $('#batch_number_edit').empty();
                                        var mySelect = $('#batch_number_edit');
                                        //mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                                        /*Select batch*/
                                        if (!jQuery.isEmptyObject(data)) {
                                            $.each(data, function (val, text) {
                                                mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']));
                                            });

                                            var optionsToSelect = textBatchData.split(",");
                                            var select = document.getElementById( 'batch_number_edit' );

                                            for ( var i = 0, l = select.options.length, o; i < l; i++ )
                                            {
                                                o = select.options[i];
                                                if ( optionsToSelect.indexOf( o.text ) != -1 )
                                                {
                                                    o.selected = true;
                                                }
                                            }
                                            
                                        }
                                    }, error: function () {
                                        swal("Cancelled", "Your " + value + " file is safe :)", "error");
                                    }
                                });

                            }

                            var taxAmount = parseFloat(data['taxAmountledger']);
                            var totAmount = ((parseFloat(data['transactionAmount'])) - (parseFloat(data['discountAmount']))) ;
                            InvoiceDetailAutoID = data['InvoiceDetailAutoID'];
                            load_segmentBase_projectID_itemEdit(data['segmentID'], data['projectID']);
                            $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - "+data['seconeryItemCode']);
                            projectcategory = data['project_categoryID'];
                            projectsubcat = data['project_subCategoryID'];
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#edit_quantityRequested').val(data['requestedQty']);
                            $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount'])));
                            $('#editNetAmount').val((parseFloat(data['transactionAmount']).toFixed(currency_decimal)));
                            $('#edit_search_id').val(data['itemSystemCode']);
                            $('#edit_itemSystemCode').val(data['itemSystemCode']);
                            $('#edit_itemAutoID').val(data['itemAutoID']);
                            $('#edit_itemDescription').val(data['itemDescription']);
                            $('#edit_wareHouseAutoID').val(data['wareHouseAutoID']).change();
                            $('#edit_comment').val(data['description']);
                            $('#edit_discount').val(data['discountPercentage']);
                            $('#edit_discount_amount').val(data['discountAmount']);
                            select_VAT_value = data['taxCalculationformulaID'];
                            $('#linetaxamnt_edit').text((taxAmount).formatMoney(currency_decimal, '.', ','));
                            $('#totalAmount_edit').text((parseFloat(totAmount) +  parseFloat(taxAmount) ).formatMoney(currency_decimal, '.', ','));
                            edit_fetch_line_tax_and_vat(data['itemAutoID']);
                            $("#edit_bsi_item_detail_modal").modal({backdrop: "static"});
                            initializeitemTypeahead_edit();
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }

        function load_batch_number_single_edit(){
            var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
            var itemAutoID = $('#edit_itemAutoID').val();
            var wareHouseAutoID = $('#edit_wareHouseAutoID').val();
            if(currentEditWareHouseAutoID!='' && currentEditTextBatchData!=''){
                if(currentEditWareHouseAutoID ==wareHouseAutoID){

                        var textBatchData=currentEditTextBatchData;
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'itemId': itemAutoID,'wareHouseAutoID':wareHouseAutoID},
                            url: "<?php echo site_url('Inventory/fetch_batch_details_byId'); ?>",
                            success: function (data) {
                                $('#batch_number_edit').empty();
                                var mySelect = $('#batch_number_edit');
                                //mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                                /*Select batch*/
                                if (!jQuery.isEmptyObject(data)) {
                                    $.each(data, function (val, text) {
                                        mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']));
                                    });

                                    var optionsToSelect = textBatchData.split(",");
                                    var select = document.getElementById( 'batch_number_edit' );

                                    for ( var i = 0, l = select.options.length, o; i < l; i++ )
                                    {
                                        o = select.options[i];
                                        if ( optionsToSelect.indexOf( o.text ) != -1 )
                                        {
                                            o.selected = true;
                                        }
                                    }
                                    
                                }
                            }, error: function () {
                                swal("Cancelled", "Your " + value + " file is safe :)", "error");
                            }
                        });


                }else{
                   
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'itemId': itemAutoID,'wareHouseAutoID':wareHouseAutoID},
                        url: "<?php echo site_url('Inventory/fetch_batch_details_byId'); ?>",
                        success: function (data) {
                            $('#batch_number_edit').empty();
                            var mySelect = $('#batch_number_edit');
                            //mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                            /*Select batch*/
                            if (!jQuery.isEmptyObject(data)) {
                                $.each(data, function (val, text) {
                                    mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']));
                                });
                                
                            }
                        }, error: function () {
                            swal("Cancelled", "Your " + value + " file is safe :)", "error");
                        }
                    });
                    
                }
            }
        }


        function load_segmentBase_projectID_itemEdit(segment, selectValue) {
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
                    if (selectValue) {
                        $("#projectID_item").val(selectValue).change()
                    }
                    stopLoad();
                },
                error: function (xhr, ajaxOptions, thrownError) {
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

        function Update_bsi_ID_item() {
            var $form = $('#edit_bsi_item_detail_form');
            var data = $form.serializeArray();
            if (InvoiceAutoID) {
                data.push({'name': 'InvoiceAutoID', 'value': InvoiceAutoID});
                data.push({'name': 'InvoiceDetailAutoID', 'value': InvoiceDetailAutoID});
                data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
                data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Payable/save_bsi_item_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            InvoiceDetailAutoID = null;
                            $('#bsi_item_detail_form')[0].reset();
                            setTimeout(function () {
                                fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID, 2);
                                $('#edit_bsi_item_detail_modal').modal('hide');
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


        function remove_item_all_description_edit(e, ths) {
            //$('#edit_itemAutoID').val('');
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9 || keyCode == 13) {
                //e.preventDefault();
            } else {
                $('#edit_itemAutoID').val('');
            }
        }

        function initializeitemTypeahead_edit() {
            $('#search').autocomplete({
                serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN',
                onSelect: function (suggestion) {
                    setTimeout(function () {
                        $('#edit_itemAutoID').val(suggestion.itemAutoID);
                    }, 200);
                    fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                    if(isGroupBasedTaxEnable == 1){
                        fetch_line_tax_and_vat(suggestion.itemAutoID, this);






                    }

                    $(this).closest('tr').find('#edit_quantityRequested').focus();
                }
            });
        }

        var batch_number=0;

        function add_more_item() {
            search_id += 1;
            batch_number += 1;
            $('select.select2').select2('destroy');
            var appendData = $('#bsi_Item_table tbody tr:first').clone();
            appendData.find('.f_search').attr('id', 'f_search_' + search_id);

            var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';

            if(itemBatchPolicy==1){
                appendData.find('.b_number').empty();
                appendData.find('.b_number').attr('id', 'batch_number_' + search_id);
                appendData.find('.b_number').attr('name', 'batch_number[' + batch_number+'][]');
            }

            appendData.find('input').val('');
            appendData.find('.linetaxamnt').html('0.00');
            appendData.find('.net_unit_cost').html('0.00');
            appendData.find('.tax_type').html('<select name="tax_type[]" class="form-control tax_type" style="width: 134px;"><option value="">Select Tax</option></select>');
            appendData.find('.estimatedAmount ').val(0);
            appendData.find('.quantityRequested').val(0);
            appendData.find('.discount').val(0);
            appendData.find('.discount_amount').val(0);
            appendData.find('.netAmount ').val(0);
            appendData.find('textarea').val('');
            appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
            $('#bsi_Item_table').append(appendData);
            var lenght = $('#bsi_Item_table tbody tr').length - 1;
            $('#f_search_' + search_id).closest('tr').css("background-color", 'white');

            $(".select2").select2();
            initializeitemTypeahead(search_id);
            number_validation();
        }

        function fetch_po_detail_table(purchaseOrderID) {
            $('.pull-li').removeClass('pulling-based-li');
            $('#pull-'+purchaseOrderID).addClass('pulling-based-li');
            if (purchaseOrderID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'purchaseOrderID': purchaseOrderID},
                    url: "<?php echo site_url('Payable/fetch_po_detail_table'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#table_body_po').empty();
                        $('#table_tfoot_po').empty();
                        $('#purchaseOrderID').val(purchaseOrderID);

                        x = 1;
                        if (jQuery.isEmptyObject(data['detail'])) {
                            $('#table_body_po').append('<tr class="danger"><td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                            <!--No Records Found-->
                        } else {
                            tot_amount = 0;
                            receivedQty = 0;
                            var tax = '';
                            var taxAmount = '';
                            var warehus = '<option value="">Select Warehouse</option>';
                            $.each(data['warehouse'], function (key, values) {
                                warehus += '<option value="' + values['wareHouseAutoID'] + '">' + values['wareHouseCode'] + ' | ' + values['wareHouseLocation'] + ' | ' + values['wareHouseDescription'] + ' </option>';
                            });
                            $.each(data['detail'], function (key, value) {
                                if(value['generalTaxAmount']==null){
                                    value['generalTaxAmount']=0;
                                }
                                if(isGroupBasedTaxEnable == 1){
                                    tax = '<td>'+value['taxDescription']+' <input class="hidden" id="taxCalculationFormulaID_' + value['purchaseOrderDetailsID'] + '" value="' + value['taxCalculationformulaID'] + '"></td>';
                                    taxAmount = '<td> <p id="totTaxCal_' + value['purchaseOrderDetailsID'] + '"> </p> </td>'
                                    potaxamnt=0;
                                }else {
                                    potaxamnt=parseFloat((value['taxAmount']+value['generalTaxAmount'])/value['requestedQty']);
                                }



                                if (value['receivedQty'] != null && value['receivedQty'] !== undefined) {
                                    receivedQty = value['receivedQty'];
                                }
                                cost_status = '<input type="text" class="number" size="15" onkeypress="return validateFloatKeyPress(this,event)" id="amount_' + value['purchaseOrderDetailsID'] + '" value="' + parseFloat(value['unitAmount'] - (value['generalDiscountAmount'] / value['requestedQty'])+potaxamnt) + '" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ')" >';

                                if (data['policy_po_cost_change'] == 0) {
                                    cost_status = '<input type="hidden" class="number" size="15" onkeypress="return validateFloatKeyPress(this,event)" id="amount_' + value['purchaseOrderDetailsID'] + '" value="' + parseFloat(value['unitAmount'] - (value['generalDiscountAmount'] / value['requestedQty'])+potaxamnt) + '" onkeyup="select_value(' + value['purchaseOrderDetailsID'] + ')" >' + parseFloat(value['unitAmount']);
                                }


                                $('#purchaseOrderDetailID').val(value['purchaseOrderDetailsID']);
                                $('#table_body_po').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['Itemdescriptionpartno'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td>    <td><select style="width: 100px;" id="location_' + value['purchaseOrderDetailsID'] + '">' + warehus + '</select></td>'+tax+''+taxAmount+'<td class="text-right" id="balQty_' + value['purchaseOrderDetailsID'] + '">' + (value['requestedQty'] - value['receivedQty']) + '</td><td class="text-right">' + parseFloat(value['unitAmount'] - (value['generalDiscountAmount'] / value['requestedQty'])+potaxamnt).toFixed(currency_decimal) + '</td><td class="text-center"><i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' +value['purchaseOrderDetailsID'] + ',' + value['unitAmount']+')" aria-hidden="true"></i> <input type="text" class="number" size="8" id="qty_' + value['purchaseOrderDetailsID'] + '" onkeyup="select_check_box_po(this,' + value['purchaseOrderDetailsID'] + ',' + value['unitAmount'] + ')" ></td><td class="text-center">' + cost_status + '</td><td class="text-center"><p id="tot_' + value['purchaseOrderDetailsID'] + '"> </p></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['purchaseOrderDetailsID'] + '" type="checkbox" value="' + value['purchaseOrderDetailsID'] + '"></td></tr>');
                                x++;
                                tot_amount += (parseFloat(value['totalAmount']).toFixed(currency_decimal));
                                //.formatMoney(currency_decimal, '.', ',')
                            });
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
            ;
        }


        function select_value(id) {
            var qty = $('#qty_' + id).val();
            if (qty < 0) {
                qty = 0;
            }
            amount = $('#amount_' + id).val();
            if (amount < 0) {
                amount = 0;
            }
            var total = qty * amount;
            var totalnew = (parseFloat(total).toFixed(currency_decimal));//.formatMoney(currency_decimal, '.', ',')
            $('#tot_' + id).text(totalnew);
        }

        function select_check_box_po(data, id, amount) {
            var qty = $('#qty_'+id).val();

            var taxCalculationFormulaID = $('#taxCalculationFormulaID_' + id).val();
            var purchaseOrderID = $('#purchaseOrderID').val();

            var balQty =  $('#balQty_'+id).text();

            if(parseFloat(qty) > parseFloat(balQty)){
                $('#qty_' + id).val('');
                $('#tot_' + id).text('');
                swal("Warning", "Qty cannot be greater than PO balance Qty!", "warning");
            }else{
                $("#check_" + id).prop("checked", false);
                if (data.value > 0) {
                    $("#check_" + id).prop("checked", true);
                }
                amount = $('#amount_' + id).val();
                if (amount < 0) {
                    amount = 0;
                }
                var total = qty * amount;
                var totalnew = parseFloat(total).formatMoney(currency_decimal, '.', ',');
                if(isGroupBasedTaxEnable == 1 && taxCalculationFormulaID!=0){
                    linewiseTax(id,taxCalculationFormulaID,(qty*amount),purchaseOrderID,qty);
                }else {
                    $('#tot_' + id).text(totalnew);
                }
            }

        }


        function save_po_base_items() {
            var selected = [];
            var amount = [];
            var qty = [];
            var location = [];
            var taxCalculationFormulaID = [];
            $('#table_body_po input:checked').each(function () {
                if ($('#amount_' + $(this).val()).val() == '') {
                    swal("Cancelled", "Received cost cannot be blank !", "error");
                } else if ($('#location_' + $(this).val()).val() == '') {
                    swal("Cancelled", "Warehouse cannot be blank !", "error");
                } else {
                    selected.push($(this).val());
                    amount.push($('#amount_' + $(this).val()).val());
                    qty.push($('#qty_' + $(this).val()).val());
                    location.push($('#location_' + $(this).val()).val());
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
                        'InvoiceAutoID': InvoiceAutoID,
                        'amount': amount,
                        'qty': qty,
                        'location': location,
                        'taxCalculationFormulaID': taxCalculationFormulaID
                    },
                    url: "<?php echo site_url('Payable/save_po_base_items'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data['status']) {
                            $('#po_base_modal').modal('hide');
                            setTimeout(function () {
                                fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID, 3);
                            }, 300);
                        } else {
                            myAlert('w', data['data'], 1000);
                        }

                    }, error: function () {
                        $('#po_base_modal').modal('hide');
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            }
        }

        function po_edit_amount_cal(id) {
            if (id == 1) {
                var edit_qtyPO = $('#edit_qtyPO').val();
                var edit_UnitamountPO = $('#edit_UnitamountPO').val();
                var tot = edit_qtyPO * edit_UnitamountPO;
                $('#edit_transactionAmountPO').val(tot);
            } else if (id == 2) {
                var edit_UnitamountPO = $('#edit_UnitamountPO').val();
                var edit_qtyPO = $('#edit_qtyPO').val();
                var tot = edit_qtyPO * edit_UnitamountPO;
                $('#edit_transactionAmountPO').val(tot);
            } else {
                var edit_transactionAmountPO = $('#edit_transactionAmountPO').val();
                var edit_qtyPO = $('#edit_qtyPO').val();
                var tot = edit_transactionAmountPO / edit_qtyPO;
                $('#edit_UnitamountPO').val(tot);

            }

            if(isGroupBasedTaxEnable == 1){
                linewiseTaxEdit(edit_qtyPO);
            }

        }

        function UpdateSupplierINvoicePODetails() {
            var data = $("#edit_bsi_po_detail_form").serializeArray();
            data.push({'name': 'InvoiceAutoID', 'value': InvoiceAutoID});
            data.push({'name': 'InvoiceDetailAutoID', 'value': InvoiceDetailAutoID});


            var edit_qtyPO = parseFloat($("#edit_qtyPO").val());
            var editOrdQty = parseFloat($("#editOrdQty").val());

            if(editOrdQty<edit_qtyPO){
                swal("Warning", "Qty cannot be greater than PO balance Qty!", "warning");
            }else{
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Payable/Update_PO_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data['status'] == true) {

                            $('#edit_bsi_po_detail_modal').modal('hide');
                            InvoiceDetailAutoID = null;
                        }

                        if (data['status']) {
                            setTimeout(function () {
                                fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID, 3);
                            }, 300);
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

        function cal_discount(element) {
            if (element.value < 0 || element.value > 100 || element.value == '') {
                swal("Cancelled", "Discount % should be between 0 - 100", "error");
                $(element).closest('tr').find('.discount').val(parseFloat(0));
                $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
                $(element).closest('tr').find('.estimatedAmount').val(parseFloat(0));
            } else {
                var quantityRequested = parseFloat($(element).closest('tr').find('.quantityRequested').val());
                var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
                if (estimatedAmount > 0) {
                    $(element).closest('tr').find('.discount_amount').val(((estimatedAmount * quantityRequested) / 100) * parseFloat(element.value))
                } else {
                    $(element).closest('tr').find('.discount').val(0);
                }
            }
        }

        function cal_discount_amount(element) {
            var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
            var quantityRequested = parseFloat($(element).closest('tr').find('.quantityRequested').val());
            if (element.value > (estimatedAmount * quantityRequested)) {
                myAlert('w', 'Discount amount should be less than or equal to Amount');
                $(element).closest('tr').find('.discount').val(0);
                $(element).val(0)
            } else {
                if (estimatedAmount) {
                    $(element).closest('tr').find('.discount').val(((parseFloat(element.value) / (estimatedAmount * quantityRequested)) * 100).toFixed(3))
                } else {
                    $(element).closest('tr').find('.discount_amount').val(0);
                }
            }
            //net_amount(element);
        }

        function edit_cal_discount(discount) {
            var estimatedAmount = parseFloat($('#edit_estimatedAmount').val());
            var qtyreq = parseFloat($('#edit_quantityRequested').val());

            if (discount < 0 || discount > 100) {
                swal("Cancelled", "Discount % should be between 0 - 100", "error");
                $('#edit_discount').val(0);
                $('#edit_discount_amount').val(0);
            } else {
                if (estimatedAmount) {
                    $('#edit_discount_amount').val(((estimatedAmount * qtyreq) / 100) * parseFloat($('#edit_discount').val()))
                }
            }
        }

        function edit_cal_discount_amount() {
            var estimatedAmount = parseFloat($('#edit_estimatedAmount').val());
            var discountAmount = parseFloat($('#edit_discount_amount').val());
            var qtyreq = parseFloat($('#edit_quantityRequested').val());
            if (discountAmount > (estimatedAmount * qtyreq)) {
                swal("Cancelled", "Discount Amount should be less than the Sales Price", "error");
                $('#edit_discount').val(0);
                $('#edit_discount_amount').val(0);
            } else {
                if (estimatedAmount) {
                    $('#edit_discount').val(((parseFloat(discountAmount) / (estimatedAmount * qtyreq)) * 100).toFixed(3))
                }
            }
        }

        function cal_discount_amount_general(discount_amount, total_amount) {
            var total_amount_disc=$('#discount_tot_hn').val();
            if (total_amount_disc && discount_amount) {
                $('#discountPercentage').val(((parseFloat(discount_amount) / total_amount_disc) * 100));
                var d_percent=$('#discountPercentageTothn').val();
                var discper=((parseFloat(discount_amount) / total_amount_disc) * 100);
                if(discper>100){
                    myAlert('w','Discount percentage total canot be greater than 100%');
                    $('#discountPercentage').val('');
                    $('#discount_trans_amount').val('');
                }
            } else {
                $('#discountPercentage').val(0);
            }
        }

        function cal_discount_general(discount, total_amount) {
            var total_amount_disc=$('#discount_tot_hn').val();
            if (total_amount_disc && discount) {
                $('#discount_trans_amount').val(parseFloat((total_amount_disc / 100) * parseFloat(discount)).toFixed(currency_decimal));
                var d_percent=$('#discountPercentageTothn').val();
                var discper=$('#discountPercentage').val();
                if(discper>100){
                    myAlert('w','Discount percentage total canot be greater than 100%');
                    $('#discountPercentage').val('');
                    $('#discount_trans_amount').val('');
                }
            } else {
                $('#discount_trans_amount').val(0);
            }
        }

        function save_general_discount(){
            var data = $("#discount_form").serializeArray();
            data.push({'name': 'InvoiceAutoID', 'value': InvoiceAutoID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payable/save_general_discount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                   stopLoad();
                   myAlert(data[0],data[1]);
                    if (data[0] == 's') {
                        $('#discountPercentage').val('');
                        $('#discount_trans_amount').val('');
                    }
                    if (data[0] == 's') {
                        setTimeout(function () {
                            fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID, 3);
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function edit_discount(){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'InvoiceAutoID': InvoiceAutoID},
                url: "<?php echo site_url('Payable/edit_discount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#discountPercentage').val(data['generalDiscountPercentage']);
                    $('#discount_trans_amount').val(data['generalDiscountAmount']);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function delete_discount(){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'InvoiceAutoID': InvoiceAutoID},
                url: "<?php echo site_url('Payable/delete_discount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    if (data[0] == 's') {
                        $('#discountPercentage').val('');
                        $('#discount_trans_amount').val('');
                    }
                    if (data[0] == 's') {
                        setTimeout(function () {
                            fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID, 3);
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
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
            if(isGroupBasedTaxEnable == 1){
                load_line_tax_amount_expenceGL(val);
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
            if(isGroupBasedTaxEnable == 1){
                load_line_tax_amount_expenceGL_edit(val);
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

        function load_segmentBase_projectID_glEdit(segment, selectValue) {
            var type = 'gl';
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
                    $('#edit_div_projectID_item1').html(data);
                    $('.select2').select2();
                    if (selectValue) {
                        $("#projectID_gl").val(selectValue).change()
                    }
                    stopLoad();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    stopLoad();
                }
            });
        }

        function applybtn(id, total, dataID) {
            $(id).closest('tr').find('.grvamount').val(total);
            select_check_box(total, dataID, total, 2);
        }
        function clear_grv_selected(data, id) {
            $("#check_" + id).prop("checked", false);
            $("#amount_" + id).val('');
            total_calculation();
        }
        $('input').on('ifChecked', function (event) {
            if ($(this).hasClass('add_allinvoices')) {
                add_all_grvbase(1);

                //

            }
        });


        $('input').on('ifUnchecked', function (event) {
            if ($(this).hasClass('add_allinvoices')) {
                add_all_grvbase(2);

            }

        });
        function add_all_grvbase(id) {
            if(id == 1)
            {
                $("#table_body tr").each(function () {
                    var balance = ($(this).find('.supplierinvoicebalance').text().replace(/,/g,''));
                    balance = balance.trim();
                    var supplierAutoID = $(this).find('.supplierAutoID').val();
                    $(this).find('.amountadd').val(balance);
                    if (balance > 0) {
                        if (balance >= balance) {
                            $("#check_" + supplierAutoID).prop("checked", true);
                            var tot_TotalCostoverhead = 0;
                            $('.invoice_base tr').each(function () {
                                var tot_valueoverhead = getNumberAndValidate($('td', this).eq(6).find('input').val());
                                tot_TotalCostoverhead += tot_valueoverhead;
                            });

                        } else {
                            $("#check_" + supplierAutoID).prop("checked", false);
                            $("#amount_" + supplierAutoID).val('');
                            //myAlert('w', '<?php echo $this->lang->line('accounts_payable_tr_cannot_enter_an_invoice');?>');
                            /*You can not enter an invoice amount greater than selected invoice Balance Amount*/
                        }
                    }

                });
            }else
            {
                $("#table_body tr").each(function () {
                    var supplierAutoID = $(this).find('.supplierAutoID').val();
                    $(this).find('.amountadd').val('');
                    $("#check_" + supplierAutoID).prop("checked", false);
                    $("#amount_" + supplierAutoID).val('');
                });
            }

        }
        function fetch_purchase_price(purchaseprice, element, unitOfMeasureID, itemAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    id: InvoiceAutoID,
                    purchaseprice: purchaseprice,
                    //unitOfMeasureID: unitOfMeasureID,
                    //itemAutoID: itemAutoID,
                    tableName: 'srp_erp_paysupplierinvoicemaster',
                    primaryKey: 'InvoiceAutoID',

                },
                url: "<?php echo site_url('ItemMaster/fetch_purchase_price'); ?>",
                success: function (data) {
                    $(element).closest('tr').find('.estimatedAmount').empty();
                    if (data['status']) {
                        $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                    }
                    refreshNotifications(true);
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function setQty(orderDetailsID, amount) {
            var reqQtyId = "#balQty_"+orderDetailsID;
            var ordQtyId = "#qty_"+orderDetailsID;
            $(ordQtyId).val($(reqQtyId).text());
            var data = {value:$(ordQtyId).val()};
            select_check_box_po(data,orderDetailsID,amount);
        }
        function load_line_tax_amount(ths){

         var qut = $(ths).closest('tr').find('.quantityRequested').val();
         var itemAutoID = $(ths).closest('tr').find('.itemAutoID').val();
         var amount = $(ths).closest('tr').find('.estimatedAmount').val();
         var discoun = $(ths).closest('tr').find('.discount_amount').val();
         var taxtype = $(ths).closest('tr').find('.tax_type').val();
        

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
         lintaxappamnt=(qut*(amount));
         if (!jQuery.isEmptyObject(taxtype)) {

             $.ajax({
                 async: true,
                 type: 'post',
                 dataType: 'json',
                 data: {'InvoiceAutoID':InvoiceAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'itemAutoID':itemAutoID,'discount':discoun,'InvoiceDetailAutoID':InvoiceDetailAutoID},
                 url: "<?php echo site_url('Payable/load_line_tax_amount'); ?>",
                 beforeSend: function () {
                     startLoad();
                 },
                 success: function (data) {
                     refreshNotifications();
                     stopLoad();
                    $(ths).closest('tr').find('.linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.net_unit_cost').text((((parseFloat(data)  +  parseFloat(lintaxappamnt )) ) - parseFloat(discoun)).toFixed(currency_decimal));
                
                 }, error: function () {
                     alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                     stopLoad();
                 }
             });
         }else{
             $(ths).closest('tr').find('.linetaxamnt').text('0');
             $(ths).closest('tr').find('.net_unit_cost').text(((parseFloat(lintaxappamnt)-parseFloat(discoun))).toFixed(currency_decimal));
         }

     }
     function fetch_line_tax_and_vat(itemAutoID, element)
    {
        select_VAT_value = '';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'InvoiceAutoID': InvoiceAutoID,'itemAutoID':itemAutoID},
            url: "<?php echo site_url('Payable/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.tax_type').empty();

              load_line_tax_amount_edit(element);

                var mySelect = $(element).parent().closest('tr').find('.tax_type');
                mySelect.append($('<option></option>').val('').html('Select Tax'));
                if (!jQuery.isEmptyObject(data['tax_drop'])) {
                    $.each(data['tax_drop'], function (val, text) {
                        mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                    });
                    if(select_VAT_value == ''){
                        if(data['selected_itemTax']!=0){
                            $(element).closest('tr').find('.tax_type').val(data['selected_itemTax']).change();
                        }else{

                            $(element).closest('tr').find('.tax_type').val(null).change();
                        }
                        //change_amount(element,1);
                    }

                    if (select_VAT_value) {
                        $(element).closest('tr').find('.tax_type').val(select_VAT_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }
    function load_line_tax_amount_edit(ths){

      var qut = $('#edit_quantityRequested').val();
      var amount = $('#edit_estimatedAmount').val();
      var discoun = $('#edit_discount_amount').val();
      var taxtype = $('#tax_type_edit').val();
      var itemAutoID = $('#itemAutoID_edit').val();
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
      lintaxappamnt=(qut*(amount));
      if (!jQuery.isEmptyObject(taxtype)) {
          $.ajax({
              async: true,
              type: 'post',
              dataType: 'json',
              data: {'InvoiceAutoID':InvoiceAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'itemAutoID':itemAutoID,'discount':discoun,'InvoiceDetailAutoID':InvoiceDetailAutoID},
              url: "<?php echo site_url('Payable/load_line_tax_amount'); ?>",
              beforeSend: function () {
                  startLoad();
              },
              success: function (data) {
                  refreshNotifications();
                  stopLoad();
                $('#linetaxamnt_edit').text((data).toFixed(currency_decimal));
                $('#totalAmount_edit').text((((parseFloat(data)  +  parseFloat(lintaxappamnt )) ) - parseFloat(discoun)).toFixed(currency_decimal));
            
              }, error: function () {
                  alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                  stopLoad();
              }
          });
      }else{
          $('#linetaxamnt_edit').text('0');
          $('#totalAmount_edit').text(((parseFloat(lintaxappamnt)-parseFloat(discoun))).toFixed(currency_decimal));
      }

  }


  function edit_fetch_line_tax_and_vat(itemAutoID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'InvoiceAutoID': InvoiceAutoID,'itemAutoID':itemAutoID},
            url: "<?php echo site_url('Payable/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                $('#tax_type_edit').empty();
                var mySelect = $('#tax_type_edit');
                mySelect.append($('<option></option>').val('').html('Select Tax'));
                if (!jQuery.isEmptyObject(data['tax_drop'])) {
                    $.each(data['tax_drop'], function (val, text) {
                        mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                    });

                    if(select_VAT_value == ''){
                        if(data['selected_itemTax']!=0){
                            $('#tax_type_edit').val(data['selected_itemTax']).change();
                        }else{

                            $('#tax_type_edit').val(null).change();
                        }
                        //change_amount_edit(element,1);
                    }
                    if (select_VAT_value) {
                        $('#tax_type_edit').val(select_VAT_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }


    function load_line_tax_amount_expenceGL(ths,invoiceDetailID = null,isTaxAdd = null){


         var itemAutoID = $(ths).closest('tr').find('.itemAutoID').val();
         var amount = $(ths).closest('tr').find('.amount').val();
         var discoun = $(ths).closest('tr').find('.discountAmount').val();
         var taxtype = $(ths).closest('tr').find('.tax_type').val();
         var lintaxappamnt = 0;
         InvoiceDetailAutoID = invoiceDetailID;

         if (jQuery.isEmptyObject(amount)) {
             amount=0;
         }
         if (jQuery.isEmptyObject(discoun)) {
             discoun=0;
         }
         lintaxappamnt=((amount));
         if (!jQuery.isEmptyObject(taxtype)) {

             $.ajax({
                 async: true,
                 type: 'post',
                 dataType: 'json',
                 data: {'InvoiceAutoID':InvoiceAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'itemAutoID':itemAutoID,'discount':discoun,'InvoiceDetailAutoID':InvoiceDetailAutoID,'isTaxAdd':isTaxAdd},
                 url: "<?php echo site_url('Payable/load_line_tax_amount'); ?>",
                 beforeSend: function () {
                     startLoad();
                 },
                 success: function (data) {
                     refreshNotifications();
                     stopLoad();
                    $(ths).closest('tr').find('.linetaxamntExpenceGl').text(parseFloat(data).toFixed(currency_decimal));
                    $(ths).closest('tr').find('.Netnumber').val( (parseFloat(data))+((parseFloat(lintaxappamnt)) - (parseFloat(discoun))) );
                     
                 }, error: function () {
                     alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                     stopLoad();
                 }
             });
         }else{
             $(ths).closest('tr').find('.linetaxamntExpenceGl').text('0');
             $(ths).closest('tr').find('.Netnumber').val((parseFloat(lintaxappamnt)) - (parseFloat(discoun)));
         }

     }


     function load_line_tax_amount_expenceGL_edit(ths){

         var amount = $('#edit_amount').val();
         var discoun = $('#discountAmount_edit').val();
         var taxtype =$('#tax_type_edit_expenceGL').val();
         var lintaxappamnt=0;

         if (jQuery.isEmptyObject(amount)) {
             amount=0;
         }
         if (jQuery.isEmptyObject(discoun)) {
             discoun=0;
         }

         lintaxappamnt=((amount));
         if (!jQuery.isEmptyObject(taxtype)) {

             $.ajax({
                 async: true,
                 type: 'post',
                 dataType: 'json',
                 data: {'InvoiceAutoID':InvoiceAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype,'discount':discoun,'InvoiceDetailAutoID':InvoiceDetailAutoID},
                 url: "<?php echo site_url('Payable/load_line_tax_amount'); ?>",
                 beforeSend: function () {
                     startLoad();
                 },
                 success: function (data) {
                     refreshNotifications();
                     stopLoad();
                    $(ths).closest('tr').find('.linetaxamntExpenceGl_edit').text(data.toFixed(currency_decimal));
                    $('#Netamount_edit').val( (parseFloat(data))+((parseFloat(lintaxappamnt)) - (parseFloat(discoun))));
                     
                 }, error: function () {
                     alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                     stopLoad();
                 }
             });
         }else{
             $(ths).closest('tr').find('.linetaxamntExpenceGl_edit').text('0');
             $('#Netamount_edit').val((parseFloat(lintaxappamnt)) - (parseFloat(discoun)));
         }

     }
        function linewiseTax(id,taxCalculationFormulaID,total,purchaseOrderID,qty){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taxCalculationFormulaID': taxCalculationFormulaID,'total':total,'InvoiceDetailAutoID':InvoiceDetailAutoID,'InvoiceAutoID':InvoiceAutoID,'purchaseOrderID':purchaseOrderID,'purchaseOrderDetailID':id,'qty':qty},
                url: "<?php echo site_url('Payable/fetch_lineWiseTax'); ?>",
                success: function (data) {
                    refreshNotifications();
                    $('#totTaxCal_'+id).text(data);
                    $('#tot_' + id).text((parseFloat(total)+parseFloat(data)).toFixed(currency_decimal));
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }

        function load_general_groupbased_tax(taxType,taxApplicableAmout){
            if(taxType){
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'InvoiceAutoID':InvoiceAutoID, 'applicableAmnt':taxApplicableAmout, 'taxtype':taxType,'discount':0},
                    url: "<?php echo site_url('Payable/load_line_tax_amount'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications();
                        stopLoad();
                        $('#tax_amount').val(data);
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        stopLoad();
                    }
                });
            }else{
                $('#tax_amount').val(0);
            }



        }

        function linewiseTaxEdit(qty){
           var purchaseOrderDetails = $('#purchaseOrderDetailsIDbsi').val();
           var taxCalculationFormulaID = $('#taxCalculationFormulaIDBSI').val();
           var purchaseOrderID = $('#purchaseOrderMasterIDbsi').val();
           var unitcost = $('#edit_UnitamountPO').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taxCalculationFormulaID': taxCalculationFormulaID,'InvoiceDetailAutoID':InvoiceDetailAutoID,'InvoiceAutoID':InvoiceAutoID,'purchaseOrderID':purchaseOrderID,'purchaseOrderDetailID':purchaseOrderDetails,'qty':qty},
                url: "<?php echo site_url('Payable/fetch_lineWiseTax'); ?>",
                success: function (data) {
                    refreshNotifications();
                    $('#taxamountBSI').html(parseFloat(data).toFixed(currency_decimal));
                    $('#edit_transactionAmountPO').val((( (parseFloat(qty))* (parseFloat(unitcost)))  + (parseFloat(data))).toFixed(currency_decimal));
                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }


    </script>
