<?php echo fetch_account_review(true,true,$approval && $extra['master']['approvedYN']);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$retantnINV=0;
$taxEnabled = getPolicyValues('TAX', 'All');
if(!empty($extra['master']['retensionInvoiceID'])){
    $retantnINV=1;
}
$POView = '';
if(!empty($extra['po_numberEST'])) {
    //$POView = implode(',&nbsp;&nbsp;', (array_column($extra['po_numberEST'], 'poNumber')));
    $po_numberEST=array_unique(array_column($extra['po_numberEST'], 'poNumber'));
    $POView = implode(',&nbsp;&nbsp;', ($po_numberEST));
}
$secondarycode =  getPolicyValues('SSC', 'All');
$acknowledgementDateYN = 0; //getPolicyValues('SAD', 'All');
$retensionEnabled = getPolicyValues('RETO', 'All');

?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="max-height: 100px; max-width:200px;" src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?>.</strong></h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            <br>
                            <h4><?php echo $this->lang->line('sales_markating_view_invoice_sales_invoice');?></h4><!--Sales Invoice -->
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_invoice_number');?></strong></td><!--Invoice Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['invoiceCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_document_date');?></strong></td><!--Document Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['invoiceDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
                        <td><strong>:</strong></td>
                        <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['referenceNo']); ?></td>
                    </tr>
                    <?php if($extra['master']['logisticContainerNo']){?>
                        <tr>
                            <td><strong>Container No</strong></td><!--Reference Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['logisticContainerNo']; ?></td>
                        </tr>
                    <?php }?>
                    <?php if($extra['master']['logisticBLNo']){?>
                        <tr>
                            <td><strong>BL No </strong></td><!--Reference Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['logisticBLNo']; ?></td>
                        </tr>
                    <?php }?>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<hr>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:23%;"><strong> <?php echo $this->lang->line('common_customer_name');?></strong></td><!--Customer Name-->
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:75%;"> <?php echo (empty($extra['customer']['customerSystemCode'])) ? $extra['customer']['customerName'] : $extra['customer']['customerName'].' ( '.$extra['customer']['customerSystemCode'].' )'; ?></td>
        </tr>
        <?php if (!empty($extra['customer']['customerSystemCode'])) { ?>
            <tr>
                <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?></strong></td><!--Customer Address -->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['customerAddress']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('common_customer_telephone');?> </strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['customerTelephone']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('common_customer_email');?> </strong></td>
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['customerEmail']; ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td><strong> <?php echo $this->lang->line('common_contact_person');?></strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['contactPersonName']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('sales_marketing_contact_person_tel');?></strong></td><!--Contact Person Tel-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['contactPersonNumber']; ?></td>
        </tr>
        <?php if(!empty($extra['master']['salesPersonID'])) { ?>
            <tr>
                <td><strong> Sales Person</strong></td><!--Sales Person -->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['SalesPersonName']; ?> (<?php echo $extra['master']['SalesPersonCode']; ?>)</td>
            </tr>
        <?php } ?>

        <tr>
            <td><strong><?php echo $this->lang->line('common_currency');?> </strong></td><!--Currency-->
            <td><strong>:</strong></td>
            <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
        </tr>

        <?php if($extra['master']['invoiceType'] == 'Contract') { ?>
            <tr>
                <td><strong> <?php echo $this->lang->line('common_payment_terms');?></strong></td><!--Invoice Date-->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['contractPaymentTerms']. ' Days'; ?></td>
            </tr>
        <?php } ?>
        
        <tr>
            <td><strong> <?php echo $this->lang->line('sales_markating_view_invoice_invoice_date');?></strong></td><!--Invoice Date-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['customerInvoiceDate']; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_invoice_due_date');?></strong></td><!--Invoice Due Date-->
            <td><strong>:</strong></td>
            <td> <?php echo $extra['master']['invoiceDueDate']; ?></td>
        </tr>
        <?php if($acknowledgementDateYN == 1) { ?>
            <tr>
                <td><strong><?php echo $this->lang->line('sales_marketing_acknowledgment_date');?></strong></td><!--Acknowledgement Date-->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['acknowledgementDate']; ?></td>
            </tr>
        <?php } ?>
        <?php if($group_based_tax == 1) { ?>
            <tr>
                <td style="vertical-align: top"><strong> Date Of Supply </strong></td>
                <td style="vertical-align: top"><strong>:</strong></td>
                <td>
                    <table>
                        <tr>
                            <td><a href="#" data-type="combodate" data-placement="bottom"
                                   id="acknowledgementDate"
                                   data-pk="<?php echo $extra['master']['invoiceAutoID'] . '|' . $extra['master']['supplyDate']; ?>"
                                   data-name="dateOfSupply" data-title="Date Of Supply"
                                   class="xEditableDate date_change_<?php echo $extra['master']['invoiceAutoID']; ?>"
                                   data-value="<?php echo format_date($extra['master']['supplyDate']); ?>"
                                   data-related="_supplyDate"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td style="vertical-align: top"><strong> <?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo str_replace(PHP_EOL, '<br />' , $extra['master']['invoiceNarration']);?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['invoiceNarration']; ?>
            </td>
        </tr>
        <?php if($extra['master']['invoiceType'] == 'Manufacturing'){?>
        <tr>
            <td style="vertical-align: top"><strong> MFQ Invoice Number </strong></td><!--Narration-->
            <td style="vertical-align: top"><strong>:</strong></td>
            <td><?php echo  $extra['mfqInvoiceCode'];?></td>
        </tr>
        <tr>
            <td style="vertical-align: top"><strong> Sub Job Numbers </strong></td><!--Narration-->
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo  $extra['linkedSubJobs'];?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['invoiceNarration']; ?>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top"><strong> PO Number </strong></td><!--Narration-->
            <td style="vertical-align: top"><strong>:</strong></td>
            <td>
                <table>
                    <tr>
                        <td><?php echo $POView;?></td>
                    </tr>
                </table>
                <?php //echo $extra['master']['invoiceNarration']; ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div><br>


<?php if($invoiceType == 'Project') {?>
<strong>Billing based on Completion %</strong>
<table class="table table-bordered table-condensed">
    <thead>
    <tr>
        <th style="width: 30%">Description</th>
        <th style="width: 20%">Total Amount - As per BOQ selleing price</th>
        <th style="width: 10%">Previous % caimed</th>
        <th style="width: 10%">current % claimed</th>
        <th style="width: 10%">Invoice Amount</th>
        <th style="width: 10%">Remaining</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $category = array();
    $totalvariationcontract = 0;
    $grandtotalinvoice = 0;
    foreach ($extra['invoiceproject'] as $val) {
        $category[$val["isVariation"]][] = $val;
    }
    if (!empty($category)) {

        foreach ($category as $key => $mainCategory) {
            $totalamount = 0;
            $totalinvoiceamount = 0;
            foreach ($mainCategory as $key2 => $subCategory) {

                if($subCategory['boqPreviousClaimPercentage'] > 0)
                {
                    $remainingamount = number_format((($subCategory['totalTransCurrency'] -$subCategory['transactionAmount'])-($subCategory['totalTransCurrency']*($subCategory['boqPreviousClaimPercentage']/100))),$extra['master']['transactionCurrencyDecimalPlaces']);
                }else
                {
                    $remainingamount = number_format(($subCategory['totalTransCurrency'] -$subCategory['transactionAmount']),$extra['master']['transactionCurrencyDecimalPlaces']);

                }


                echo "<tr>
                    <input type='hidden' id='prevclaimedpercentage' name='prevclaimedpercentage' value=".$subCategory['boqPreviousClaimPercentage'].">
                    <input type='hidden' id='remainingamount' name='remainingamount' value=".$remainingamount.">

                   <td>" . $subCategory["itemDescription"] . "</td>
                  <td style='text-align: right;'>" . number_format($subCategory['totalTransCurrency'],$extra['master']['transactionCurrencyDecimalPlaces']) . "</td>
                  <td style='text-align: right;'>".number_format($subCategory['boqPreviousClaimPercentage'],2) ."%</td>
                  <td style='text-align: right;'>".number_format($subCategory['boqTotalClaimPercentage'],2)."&nbsp;%</td>
                     

                    <td style='text-align: right;'>
                        ".number_format($subCategory['transactionAmount'],$extra['master']['transactionCurrencyDecimalPlaces'])."
                      </td>

                      <td style='text-align: right;'>
                      
                        <label id='remaining_".$subCategory['invoiceDetailsAutoID']."'>".$remainingamount."</label>
                      </td>

              </tr>";

                $totalamount+= $subCategory['totalTransCurrency'];
                $totalinvoiceamount += $subCategory['transactionAmount'];
                $totalvariationcontract+= $subCategory['totalTransCurrency'];
                $grandtotalinvoice  += $subCategory['transactionAmount'];


            }

            if($subCategory["isVariation"] == 0)
            {
                echo "
                        <tr style='background: #e1e1e18c'>
                        <td><b>Contract Value</b></td>
                              <td style='text-align: right;'><b>".number_format($totalamount,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                              <td style='text-align: right;'>&nbsp;</td>
                              <td style='text-align: right;'>&nbsp;</td>
                              <td style='text-align: right;'><b>".number_format($totalinvoiceamount,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                              <td style='text-align: right;'>&nbsp;</td>
                       </tr>
                        ";
                echo "<tr><td colspan='6'>&nbsp;</td></tr>";
                echo "<tr style='background: #e1e1e18c'><td colspan='6'><b>Variations</b></td></tr>";
            }

        }
        echo "<tr style='background: #e1e1e18c'>

                       <td>total variations Amount</td>
                       <td style='text-align: right;'><b>".number_format($totalamount,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'><b>".number_format($totalinvoiceamount,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       </tr>";
        echo "<tr style='background: #e1e1e18c'>
                        <td><b>Total contract Value+ variations Amount</b>
                        <td style='text-align: right;'><b>".number_format($totalvariationcontract,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                        <td style='text-align: right;'>&nbsp;</td>
                        <td style='text-align: right;'>&nbsp;</td>
                        <td style='text-align: right;'><b>".number_format($grandtotalinvoice,$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                        <td style='text-align: right;'>&nbsp;</td>
                        </td>
                        </tr>";

        echo "<tr><td colspan='6'>&nbsp;</td></tr>";
        echo "<tr><td colspan='6'>&nbsp;</td></tr>";
        echo "<tr style='background: #e1e1e18c'><td colspan='6'><b>Deductions</b></td></tr>";



        echo "<tr>

                       <td>Advance
                    
                        </td>

                         <td style='text-align: right;'>&nbsp;</td>
                         <td style='text-align: right;'>&nbsp;</td>
                         <td style='text-align: right;'>&nbsp;</td>
                        <td style='text-align: right;'><b>".number_format(get_advance_amount($subCategory['invoiceAutoID']),$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                     
                        </tr>";
        echo "<tr>

                    <td colspan='4'>Retention (".$subCategory['retensionPercentage']."%)</td>


                  <td style='text-align: right;'><b>".number_format((($grandtotalinvoice)*($subCategory['retensionPercentage']/100)),$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                    </tr>";
        echo "<tr style='background: #e1e1e18c'>

                       <td><b>Total Dedections</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'><b>".number_format((get_advance_amount($subCategory['invoiceAutoID'])+(($grandtotalinvoice)*($subCategory['retensionPercentage']/100))),$extra['master']['transactionCurrencyDecimalPlaces'])."</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       </tr>";

        echo "<tr style='background: #e1e1e18c'>
                       <td><b>Net Total</b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'>&nbsp;</td>
                       <td style='text-align: right;'><b>


      ".number_format(($grandtotalinvoice-(get_advance_amount($subCategory['invoiceAutoID'])+(($grandtotalinvoice)*($subCategory['retensionPercentage']/100)))),$extra['master']['transactionCurrencyDecimalPlaces'])."



      </b></td>
                       <td style='text-align: right;'>&nbsp;</td>
                       </tr>";


    }
    ?>
 </tbody>
 <?php }else {?>
<?php $is_item_active = 0; $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;
if(!empty($extra['item_detail'])){ ?>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <?php
                $colspan = 6;
                $footercolspan = 11;
                $istaxEnable  = 1;

                if(($taxEnabled==1)||($taxEnabled==null) || ($extra['item_detail_tax'] > 0) && $group_based_tax!=1) {
                    $colspan = 6;
                    $istaxEnable  = 1;
                    $footercolspan = 11;
                }else if($group_based_tax == 1) {
                    $colspan = 6;
                    $istaxEnable  = 1;
                    $footercolspan = 11;
                } else{
                    $colspan = 4;
                    $istaxEnable = 0;
                    $footercolspan = 9;
                }
                ?>

                <th class='theadtr'  colspan="<?php echo $colspan?>"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?></th><!--Item Details-->
                <th class='theadtr' colspan="<?php echo ($group_based_tax == 1?'8':'6') ?>"><?php echo $this->lang->line('common_price');?> (<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Price-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
                <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                <th class='theadtr' style="min-width: 35%">WareHouse</th><!--Item Description-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_qty');?></th><!--Qty-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit');?></th><!--Unit-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_discount');?></th><!--Discount-->

                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_sales_net_unit_price');?></th><!--Net Unit Cost-->
                <?php if($istaxEnable == 1 && $group_based_tax!=1) {?>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?></th><!--Total-->

                <?php if($retensionEnabled == 1) { ?>
                    <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_retension'); ?></th><!--Discount-->
                <?php } ?>

                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                <?php } else if ($group_based_tax == 1){?>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?></th><!--Total-->

                <?php if($retensionEnabled == 1) {  $footercolspan = $footercolspan + 1;?>
                    <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_retension'); ?></th><!--Discount-->
                <?php } ?>

                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                <?php }?>
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_net');?></th><!--Net-->
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;$item_total = 0;
                $is_item_active = 1;

                foreach ($extra['item_detail'] as $val) {
                    if($val['contractCode'])
                    {
                        $link = '<a  onclick="requestPageView_model(\'' . $val['documentID'] . '\','.$val['contractAutoID'].')">'.$val['contractCode'].'</a>';
                    }

                    ?>
                <tr>
                    <td style="text-align:right; font-size: 12px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="font-size: 12px;"><?php echo ( ($secondarycode == 1) ? $val['itemSystemCode']:  $val['itemSystemCode'] . ' - ' . $val['itemSecondaryCode']); ?></td>
                    <td style="font-size: 12px;"><?php echo ($val['contractCode'] ? $link.' - ' : '').$val['itemDescription']; ?>

                        <?php if(!empty($val['remarks']) && empty($val['partNo']))
                        {
                            echo ' - ' .  $val['remarks'];
                        }else if(!empty($val['remarks']) && !empty($val['partNo']))
                        {
                            echo ' - ' .  $val['remarks'] . ' - ' .'Part No : ' .$val['partNo'];
                        }
                        else if(!empty($val['partNo']))
                        {
                            echo  ' - ' . 'Part No : ' .$val['partNo'];
                        }
                        ?>

                    </td>
                    <td style="text-align:center; font-size: 12px;"><?php echo $val['warehouse']; ?></td>
                    <td style="text-align:center; font-size: 12px;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo $val['requestedQty']; ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    
                    

                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['unittransactionAmount']-$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php if($istaxEnable == 1 && $group_based_tax != 1) {?>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['totalAfterTax'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php } else if ($group_based_tax == 1){ ?>
                        <td style="text-align:right; font-size: 12px;"><?php echo format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

                        <?php if($retensionEnabled == 1) { 
                        $val['transactionAmount'] = $val['transactionAmount'] - $val['retensionValue'];
                        ?>
                        <td style="text-align:right; font-size: 12px;"><?php echo '('.format_number($val['retensionPercentage'],$extra['master']['transactionCurrencyDecimalPlaces']).'%) '.format_number($val['retensionValue'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php } ?>
                        
                         <td style="text-align:right; font-size: 12px;"><?php
                            if($val['taxAmount'] > 0) {
                                echo '  <a onclick="open_tax_dd(null,'.$val['invoiceAutoID'].',\'CINV\','. $extra['master']['transactionCurrencyDecimalPlaces'].' ,'. $val['invoiceDetailsAutoID'].', \'srp_erp_customerinvoicedetails\',\'invoiceDetailsAutoID\',0,1) ">'. number_format($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']).'</a> ';
                            }else {
                                echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                            }


                             ?></td>


                    <?php }?>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                    $num ++;
                    $gran_total += $val['transactionAmount'];
                    $item_total += $val['transactionAmount'];
                    $p_total    += $val['transactionAmount'];

                    //$gran_total += ($val['transactionAmount']-$val['totalAfterTax']);
                    $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
                    // $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                    // $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
                } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="<?php echo $footercolspan?>"><?php echo $this->lang->line('sales_markating_view_invoice_item_total');?><!--Item Total -->(<?php echo $extra['master']['transactionCurrency']; ?>) </td>
                <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<?php  } ?>
<?php $transaction_total = 0;$Local_total = 0;$party_total = 0; if(!empty($extra['gl_detail'])){  ?>
<br>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class='theadtr' style="width: 5%">#</th>
                <th class='theadtr' style="min-width: 45%;text-align: left;"><?php echo $this->lang->line('common_description');?></th><!--Description-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_segment');?></th><!--Segment-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_amount');?>(<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Amount-->
                <th class='theadtr' style="min-width: 12%">Discount</th>
                <?php if($retensionEnabled == 1){ ?>
                    <th class='theadtr' style="min-width: 12%"><?php echo $this->lang->line('common_retension');?></th>
                <?php } ?>
                <?php if($group_based_tax == 1) { ?>
                    <th class='theadtr' style="min-width: 12%">Tax</th>
                <?php } ?>
                <th class='theadtr' style="min-width: 12%">Net Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $num =1;
                foreach ($extra['gl_detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right; font-size: 12px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="font-size: 12px;"><?php echo $val['revenueGLDescription']; ?></td>
                    <td style="text-align:center; font-size: 12px;"><?php echo $val['segmentCode']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount']-$val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php if($retensionEnabled == 1){ ?>
                        <?php $val['transactionAmount'] = $val['transactionAmount'] - $val['retensionValue']; ?>
                        <td style="text-align:right;"><?php echo '('.format_number($val['retensionPercentage'],$extra['master']['transactionCurrencyDecimalPlaces']).'%) '.format_number($val['retensionValue'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php } ?>
                   
                    <?php if($group_based_tax == 1) { ?>
                        <td style="text-align:right;"><?php echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <?php } ?>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                    $num ++;
                    $gran_total         += $val['transactionAmount'];
                    $transaction_total  += $val['transactionAmount'];
                    //$Local_total        += $val['companyLocalAmount'];
                    //$party_total        += $val['customerAmount'];
                    $p_total            += $val['transactionAmount'];

                    //$gran_total += ($val['transactionAmount']-$val['totalAfterTax']);
                    $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
                    // $tax_Local_total += ($val['companyLocalAmount']-$val['totalAfterTax']);
                    // $tax_customer_total += ($val['customerAmount']-$val['totalAfterTax']);
                }
             ?>
        </tbody>
        <tfoot>
            <tr>
                <?php $col_plus = 0; 
                    if($retensionEnabled == 1) { $col_plus = 1; }
                 ?>
        
                <?php if($group_based_tax == 1) { ?>
                    <td class="text-right sub_total" colspan="<?php echo $col_plus + 6 ?>"> <?php echo $this->lang->line('common_total');?> </td><!--Total-->
                <?php } else { ?>
                    <td class="text-right sub_total" colspan="<?php echo $col_plus + 5 ?>"> <?php echo $this->lang->line('common_total');?> </td><!--Total-->
                <?php } ?>
                <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                <!-- <td class="text-right sub_total"><?php //echo format_number($Local_total,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                <td class="text-right sub_total"><?php //echo format_number($party_total,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
            </tr>

        </tfoot>
    </table>
</div>
<?php } ?>

<?php $transaction_total = 0;$Local_total = 0;$party_total = 0; if(!empty($extra['op_detail'])){  ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class='theadtr' style="width: 5%">#</th>
                <th class='theadtr' style="min-width: 45%;text-align: left;"><?php echo $this->lang->line('common_description');?></th><!--Description-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_segment');?></th><!--Segment-->
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_amount');?>(<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Amount-->
                <th class='theadtr' style="min-width: 12%">Discount</th>
                <th class='theadtr' style="min-width: 12%">Net Amount (<?php echo $extra['master']['transactionCurrency']; ?>)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num =1;
            foreach ($extra['op_detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right; font-size: 12px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="font-size: 12px;"><?php echo $val['description']; ?></td>
                    <td style="text-align:center; font-size: 12px;"><?php echo $val['segmentCode']; ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount']+$val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['discountAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td style="text-align:right;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php
                $num ++;
                $gran_total         += $val['transactionAmount'];
                $transaction_total  += $val['transactionAmount'];
                //$Local_total        += $val['companyLocalAmount'];
                //$party_total        += $val['customerAmount'];
                $p_total            += $val['transactionAmount'];

                //$gran_total += ($val['transactionAmount']-$val['totalAfterTax']);
                $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
                // $tax_Local_total += ($val['companyLocalAmount']-$val['totalAfterTax']);
                // $tax_customer_total += ($val['customerAmount']-$val['totalAfterTax']);
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="5"> <?php echo $this->lang->line('common_total');?> </td><!--Total-->
                <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($transaction_total,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>

            </tr>

            </tfoot>
        </table>
    </div>
<?php } ?>


<?php $transaction_total = 0;$Local_total = 0;$party_total = 0;$disc_nettot=0;$t_extraCharge=0;
if(!empty($isDOItemWisePolicy)==1  ){  ?>

    <?php

    $is_item_active = 0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0;
    if(!empty($extra['delivery_order_itemwise'])){ ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php
                    $colspan = 6;
                    $footercolspan = 11;
                    $istaxEnable  = 1;

                    if(($taxEnabled==1)||($taxEnabled==null) || ($extra['item_detail_tax'] > 0) && $group_based_tax!=1) {
                        $colspan = 5;
                        $istaxEnable  = 1;
                        $footercolspan = 6;
                    }else if($group_based_tax == 1) {
                        $colspan = 7;
                        $istaxEnable  = 1;
                        $footercolspan = 6;
                    } else{
                        $colspan = 4;
                        $istaxEnable = 0;
                        $footercolspan = 8;
                    }
                    ?>

                    <th class='theadtr'  colspan="<?php echo $colspan?>"><?php echo $this->lang->line('sales_marketing_delivery_order_details');?></th><!--Item Details-->
                    <th class='theadtr' colspan="<?php echo $colspan ?>"><?php echo $this->lang->line('common_price');?> (<?php echo $extra['master']['transactionCurrency']; ?>) </th><!--Price-->
                </tr>
                <tr>
                    <th class='theadtr' style="min-width: 5%">#</th>
                    <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
                    <th class='theadtr' style="min-width: 35%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                    <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
                    <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('common_qty');?></th><!--Qty-->
                    <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_unit');?></th><!--Unit-->
                    <?php if($istaxEnable == 1 && $group_based_tax!=1) {?>
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?></th><!--Total-->
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                    <?php } else if ($group_based_tax == 1){?>
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('common_total');?></th><!--Total-->
                        <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                    <?php }?>
                  
                    <th><?php echo $this->lang->line('sales_markating_view_invoice_net_total'); ?> </th><!--Net Amount-->
                    <th class="hide" style="width: 10%"><?php echo $this->lang->line('common_due');?></th>
                    <th class='theadtr hide' style="width: 10%"><?php echo $this->lang->line('common_amount');?></th>
                    <th class="hide" style="width: 10%"><?php echo $this->lang->line('common_balance');?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $num =1;$item_total = 0;$tax_total_amount = 0;$net_Amount_total = "";
                $is_item_active = 1;

                foreach ($extra['delivery_order_itemwise'] as $val) {
                    if($val['DOCode'])
                    {
                        $link = '<a  onclick="requestPageView_model(\'' . $val['documentID'] . '\','.$val['DOMasterID'].')">'.$val['DOCode'].'</a>';
                    }

                    ?>
                    <tr>
                        <td style="text-align:right; font-size: 12px;"><?php echo $num; ?>.&nbsp;</td>
                        <td style="text-align:center; font-size: 12px;"><?php echo ( ($secondarycode == 1) ? $val['itemSystemCode']:  $val['itemSystemCode'] . ' - ' . $val['itemSecondaryCode']); ?></td>
                        <td style="font-size: 12px;"><?php echo ($val['DOCode'] ? $link.' - ' : '').$val['itemDescription']; ?>

                            <?php if(!empty($val['remarks']) && empty($val['partNo']))
                            {
                                echo ' - ' .  $val['remarks'];
                            }else if(!empty($val['remarks']) && !empty($val['partNo']))
                            {
                                echo ' - ' .  $val['remarks'] . ' - ' .'Part No : ' .$val['partNo'];
                            }
                            else if(!empty($val['partNo']))
                            {
                                echo  ' - ' . 'Part No : ' .$val['partNo'];
                            }
                            ?>

                        </td>
                        <td style="text-align:center; font-size: 12px;"><?php echo $val['unitOfMeasure']; ?></td>
                        <td style="text-align:right; font-size: 12px;"><?php echo $val['requestedQty']; ?></td>
                        <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['unittransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php if($istaxEnable == 1 && $group_based_tax != 1) {?>
                            <td style="text-align:right; font-size: 12px;"><?php echo format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['totalAfterTax'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <?php } else if ($group_based_tax == 1){ ?>
                            <td style="text-align:right; font-size: 12px;"><?php echo format_number((($val['unittransactionAmount']-$val['discountAmount'])*$val['requestedQty']), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                            <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);?></td>
                        <?php }?>
                        <td style="text-align:right;font-size: 12px;"><?php echo format_number(($val['requestedQty'] * $val['unittransactionAmount']) + $val['taxAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);?></td>
                        <td class="hide" style="text-align:right; font-size: 12px;"><?php echo format_number($val['due_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="hide" style="text-align:right; font-size: 12px;"><?php echo format_number($val['transactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="hide" style="text-align:right; font-size: 12px;"><?php echo format_number($val['balance_amount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num ++;
                    $gran_total += ($val['transactionAmount'] + $val['taxAmount']);
                    $item_total += $val['transactionAmount'];
                    $p_total    += $val['transactionAmount'];
                    $tax_total_amount += $val['taxAmount'];
                    $net_Amount_total += ($val['requestedQty'] * $val['unittransactionAmount']) + $val['taxAmount'];

                    //$gran_total += ($val['transactionAmount']-$val['totalAfterTax']);
                    $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
                    // $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                    // $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
                } ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="text-right sub_total" colspan="<?php echo $footercolspan?>"><?php echo $this->lang->line('sales_markating_view_invoice_item_total');?><!--Item Total -->(<?php echo $extra['master']['transactionCurrency']; ?>) </td>
                    <td class="text-right total" style="font-size: 12px;"><?php echo format_number($item_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right total" style="font-size: 12px;"><?php echo format_number($tax_total_amount, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                    <td class="text-right total" style="font-size: 12px;"><?php echo format_number($net_Amount_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php  }  ?>

<?php } else { ?>

    <?php //$transaction_total = 0;$Local_total = 0;$party_total = 0;$disc_nettot=0;$t_extraCharge=0;

if(!empty($extra['delivery_order'])  ){  ?>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th colspan="4"><?php echo $this->lang->line('sales_marketing_delivery_order_based');?></th>
                <?php if($group_based_tax == 1) { ?>
                    <th colspan="5">
                        <?php echo $this->lang->line('common_amount');?>
                        <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?> )</span>
                    </th>
                <?php } else { ?>
                    <th colspan="4">
                        <?php echo $this->lang->line('common_amount');?>
                        <span class="currency"> (<?php echo $extra['master']['transactionCurrency']; ?> )</span>
                    </th>
                <?php } ?>
            </tr>
            <tr>
                <th class='theadtr' style="width: 5%">#</th>
                <th class='theadtr' style="width: 15%;text-align: left;"><?php echo $this->lang->line('common_code');?></th>
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_date');?></th>
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_reference_no');?></th>
                <?php if($group_based_tax == 1) { ?>
                    <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_tax');?></th>
                <?php } ?>
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_order_total');?></th>
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_due');?></th>
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_amount');?></th>
                <th class='theadtr' style="width: 15%"><?php echo $this->lang->line('common_balance');?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $num =1;
            $dPlace = $extra['master']['transactionCurrencyDecimalPlaces'];
            foreach ($extra['delivery_order'] as $val) { ?>
                <tr>
                    <td style="text-align:right; font-size: 12px;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="font-size: 12px;">
                        <?php if ($extra['master']['invoiceType'] == 'Direct'){  ?>
                            <a  onclick="requestPageView_model('DO',  <?php echo $val['DOMasterID'] ?>)"><?php echo $val['DOCode'] ?></a>
                        <?php }
                        else{
                            echo $val['DOCode'];
                        } ?>
                    </td>
                    <td style="text-align:center; font-size: 12px;"><?php echo $val['DODate']; ?></td>
                    <td style="text-align:center; font-size: 12px;"><?php echo $val['referenceNo']; ?></td>
                    <?php if($group_based_tax == 1) { ?>
                        <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['taxAmount'], $dPlace); ?></td>
                    <?php } ?>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['do_tr_amount'], $dPlace); ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['due_amount'], $dPlace); ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['transactionAmount'], $dPlace); ?></td>
                    <td style="text-align:right; font-size: 12px;"><?php echo format_number($val['balance_amount'], $dPlace); ?></td>
                </tr>
                <?php
                $num ++;
                $gran_total         += $val['due_amount'];
                $transaction_total  += $val['transactionAmount'];
                $p_total            += $val['transactionAmount'];
                $tax_transaction_total += ($val['transactionAmount']-$val['totalAfterTax']);
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="6"> <?php echo $this->lang->line('common_total');?> </td>
                <td class="text-right sub_total" style="font-size: 12px;"><?php echo format_number($transaction_total, $dPlace); ?></td>
                <td class="text-right sub_total"> </td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php } ?>
<?php } ?>


<?php  if (!empty($extra['discount'])) { ?>
    <br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%; " class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <td class='theadtr' colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Discount</strong></td>
                        </tr>
                        <tr>
                            <th class='theadtr'>#</th>
                            <th class='theadtr'>Description</th>
                            <th class='theadtr'>Percentage</th>
                            <th class='theadtr'>Transaction (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x=1;
                        foreach ($extra['discount'] as $value) {
                            $disc_total=0;
                            $disc_total= ($gran_total*$value['discountPercentage'])/100;
                            echo '<tr>';
                            echo '<td>'.$x.'.</td>';
                            echo '<td>'.$value['discountDescription'].'</td>';
                            echo '<td class="text-right">'.format_number($value['discountPercentage'],2).'%</td>';
                            echo '<td class="text-right">'.format_number($disc_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                            echo '</tr>';
                            $x++;
                            $disc_nettot += $disc_total;
                        }
                        $gran_total=$gran_total-$disc_nettot;
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3" class="text-right sub_total">Total</td>
                            <td class="text-right sub_total"><?php echo format_number($disc_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br>
<?php } ?>


<?php  if (!empty($extra['extracharge'])) { ?>
    <br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tr>
                <td style="width:40%;">
                    &nbsp;
                </td>
                <td style="width:60%;padding: 0;">
                    <table style="width: 100%">
                        <tr>
                            <td style="width:50%;padding: 0;">
                                <table style="width: 100%" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <td class='theadtr'  colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;<strong>Extra Charges</strong></td>
                                    </tr>
                                    <tr>
                                        <th class='theadtr' >#</th>
                                        <th class='theadtr' >Description</th>
                                        <th class='theadtr' >Transaction (<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $x=1;
                                    $extra_nettot=0;
                                    foreach ($extra['extracharge'] as $value) {
                                        $extra_total=0;
                                        $extra_total= $value['transactionAmount'];
                                        echo '<tr>';
                                        echo '<td>'.$x.'.</td>';
                                        echo '<td>'.$value['extraChargeDescription'].'</td>';
                                        echo '<td class="text-right">'.format_number($extra_total,$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                        echo '</tr>';
                                        $x++;
                                        $extra_nettot += $extra_total;
                                        if($value['isTaxApplicable']==1){
                                            $t_extraCharge += $extra_total;
                                        }
                                    }
                                    $gran_total=$gran_total+$extra_nettot;
                                    ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right sub_total">Total</td>
                                        <td class="text-right sub_total"><?php echo format_number($extra_nettot,$extra['master']['transactionCurrencyDecimalPlaces']) ; ?></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br>
<?php } ?>

<?php  if (!empty($extra['tax'])) { ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tr>
           <td style="width:40%;">
                &nbsp;
           </td>
           <td style="width:60%;padding: 0;">
                    <table style="width: 100%" class="<?php echo table_class(); ?>">
                        <thead>
                            <tr>
                                <td class='theadtr' colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $this->lang->line('sales_markating_view_invoice_tax_details');?></strong></td><!--Tax Details-->
                            </tr>
                            <tr>
                                <th class='theadtr'>#</th>
                                <th class='theadtr'><?php echo $this->lang->line('common_type');?></th><!--Type-->
                                <th class='theadtr'> <?php echo $this->lang->line('sales_markating_view_invoice_detail');?></th><!--Detail-->
                                <th class='theadtr'><?php echo $this->lang->line('sales_markating_view_invoice_tax');?></th><!--Tax-->
                                <th class='theadtr'><?php echo $this->lang->line('common_transaction');?><!--Transaction -->(<?php echo $extra['master']['transactionCurrency']; ?>) </th>
                                <!-- <th class='theadtr'>Local  (<?php //echo $extra['master']['companyLocalCurrency']; ?>)</th>
                                <th class='theadtr'>Customer (<?php //echo $extra['master']['customerCurrency']; ?>)</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tax_Local_total += ($tax_transaction_total/$extra['master']['companyLocalExchangeRate']);
                            $tax_customer_total += ($tax_transaction_total/$extra['master']['customerCurrencyExchangeRate']);
                            $x=1; $tr_total_amount=0;$cu_total_amount=0;$loc_total_amount=0;
                            foreach ($extra['tax'] as $value) {
                                echo '<tr>';
                                echo '<td>'.$x.'.</td>';
                                echo '<td>'.$value['taxShortCode'].'</td>';
                                echo '<td>'.$value['taxDescription'].'</td>';
                                echo '<td class="text-right">'.$value['taxPercentage'].' % </td>';
                                echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * ($tax_transaction_total-$disc_nettot+$t_extraCharge)),$extra['master']['transactionCurrencyDecimalPlaces']).'</td>';
                                //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_Local_total),$extra['master']['companyLocalCurrencyDecimalPlaces']).'</td>';
                                //echo '<td class="text-right">'.format_number((($value['taxPercentage']/ 100) * $tax_customer_total),$extra['master']['customerCurrencyDecimalPlaces']).'</td>';
                                echo '</tr>';
                                $x++;
                                $gran_total += (($value['taxPercentage']/ 100) * ($tax_transaction_total-$disc_nettot+$t_extraCharge));
                                $tr_total_amount+=(($value['taxPercentage']/ 100) * ($tax_transaction_total-$disc_nettot+$t_extraCharge));
                                //$loc_total_amount+=(($value['taxPercentage']/ 100) * $tax_Local_total);
                                //$cu_total_amount+=(($value['taxPercentage']/ 100) * $tax_customer_total);
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right sub_total"><?php echo $this->lang->line('sales_markating_view_invoice_tax_total');?></td><!--Tax Total-->
                                <td class="text-right sub_total"><?php echo format_number($tr_total_amount,$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                                <!-- <td class="text-right sub_total"><?php //echo format_number($loc_total_amount,$extra['master']['companyLocalCurrencyDecimalPlaces']); ?></td>
                                <td class="text-right sub_total"><?php //echo format_number($cu_total_amount,$extra['master']['customerCurrencyDecimalPlaces']); ?></td> -->
                            </tr>
                        </tfoot>
                    </table>
           </td>
        </tr>
    </table>
</div>
<?php } ?>
<div class="table-responsive">
    <table style="100%">
        <tr>
            <td style="width: 95%"><h5 class="text-right"><?php echo $this->lang->line('common_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
                    : </h5></td>
            <td><h5 class="text-right"><?php echo format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5></td>
        </tr>

        <?php
        $nettot_final = 0;
        if($extra['master']['rebateAmount']>0){
            $nettot_final += $extra['master']['rebateAmount']; ?>
            <tr>
                <td><h5 class="text-right">Rebate Amount  (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
                        : </h5></td>
                <td><h5 style="text-align: right"> <?php echo format_number($extra['master']['rebateAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5></td>
            </tr>
        <?php }
        if($extra['master']['retensionTransactionAmount']>0){
            $nettot_final += $extra['master']['retensionTransactionAmount'];?>
            <tr>
                <td><h5 class="text-right">Retention Amount  (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
                        : </h5></td>
                <td><h5 style="text-align: right"> <?php echo format_number($extra['master']['retensionTransactionAmount'], $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5></td>
            </tr>

        <?php }
        if($nettot_final > 0) { ?>
            <tr>
                <td><h5 class="text-right">Net Total  (<?php echo $extra['master']['transactionCurrency']; ?> )<!--Total-->
                        : </h5></td>
                <td><h5 style="text-align: right"> <?php echo format_number($gran_total-$nettot_final, $extra['master']['transactionCurrencyDecimalPlaces']); ?></h5></td>
            </tr>
       <?php }?>
    </table>

</div>
<?php if ($extra['master']['bankGLAutoID']) {
    if($gran_total < 0)
    {
        $gran_total = 0;
    }
    $a=$this->load->library('NumberToWords');
    $numberinword= $this->numbertowords->convert_number(ROUND($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']));
    $point=format_number($gran_total, $extra['master']['transactionCurrencyDecimalPlaces']);
    $str_arr = explode('.',$point);
    $str1='';
    if($str_arr[1]>0){
        if($extra['master']['transactionCurrency']=="OMR"){
            $str1=' and '.$str_arr[1].' / 1000 Only';
        }else{
            $str1=' and '.$str_arr[1].' / 100 Only';
        }
    }
    ?>
    <div class="table-responsive">
        <h6><?php echo $this->lang->line('sales_markating_view_invoice_remittance_details');?></h6><!--Remittance Details-->
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="width: 18%"><strong><?php echo $this->lang->line('common_bank');?></strong></td><!--Bank-->
                    <td style="width: 2%"><strong>:</strong></td>
                    <td style="width: 80%"><?php echo $extra['master']['invoicebank']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $this->lang->line('common_branch');?></strong></td><!--Branch-->
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['invoicebankBranch']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $this->lang->line('common_address');?></strong></td><!--Branch-->
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['bankChartOfAccount']['bankAddress']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_swift_code');?></strong></td><!--Swift Code-->
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['invoicebankSwiftCode']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $this->lang->line('common_account');?></strong></td><!--Account-->
                    <td><strong>:</strong></td>
                    <td><?php echo $extra['master']['invoicebankAccount']; ?></td>
                </tr>
                <tr>
                    <td><strong>Amount in words</strong></td><!--Account-->
                    <td><strong>:</strong></td>
                    <td><?php echo $numberinword.$str1; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php } ?>
<br>

  <?php

    $data['documentCode'] = 'CINV';
    $data['transactionCurrency'] = $extra['master']['transactionCurrency'];
    $data['transactionCurrencyDecimal'] = $extra['master']['transactionCurrencyDecimalPlaces'];
    $data['documentID'] = $extra['master']['invoiceAutoID'];
    echo $this->load->view('system/tax/tax_detail_view.php',$data,true);

  ?>




<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <?php if ($ALD_policyValue == 1) { 
                $created_user_designation = designation_by_empid($extra['master']['createdUserID']);
                $confirmed_user_designation = designation_by_empid($extra['master']['confirmedByEmpID']);
                ?>
                <tr>
                    <td style="width:30%;"><b>
                            <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                    <td style="width:2%;"><strong>:</strong></td>
                    <td style="width:70%;"><?php echo $extra['master']['createdUserName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['createdDateTime']; ?></td>
                </tr>
                <?php if($extra['master']['confirmedYN']==1){ ?>
                <tr>
                    <td style="width:30%;"><b>Confirmed By </b></td>
                    <td><strong>: </strong></td>
                    <td style="width:70%;"><?php echo $extra['master']['confirmedByName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $extra['master']['confirmedDate'];?></td>
                </tr>
                <?php } ?>
                <?php if(!empty($approver_details)) {
                    foreach ($approver_details as $val) {
                        echo '<tr>
                                <td style="width:30%;"><b>Level '. $val['approvalLevelID'] .' Approved By</b></td>
                                <td><strong>:</strong></td>
                                <td style="width:70%;"> '. $val['Ename2'] .' ('. $val['DesDescription'] .') on '.$val['approvedDate'].'</td>
                            </tr>';
                    }
                }
            } else {?>
                <tr>
                    <td style="width:30%;"><b>
                            <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                    <td style="width:2%;"><strong>:</strong></td>
                    <td style="width:70%;"><?php echo $extra['master']['createdUserName']; ?> on <?php echo $extra['master']['createdDateTime']; ?></td>
                </tr>
                <?php if ($extra['master']['confirmedYN']==1) { ?>
                    <tr>
                        <td><b>Confirmed By</b></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['confirmedYNn'];?></td>

                    </tr>
                <?php } ?>
                <?php if($extra['master']['approvedYN']){?>
                    <tr>
                        <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['approvedbyEmpName']; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['approvedDate']; ?></td>
                    </tr>
        <?php       } 
            } ?>
        </tbody>
    </table>
</div>

<?php if ($extra['master']['invoiceNote']) { ?>
<div class="table-responsive"><br>
    <h6><?php echo $this->lang->line('sales_markating_view_invoice_notes');?></h6><!--Notes-->
    <table style="width: 100%">
        <tbody>
            <tr>
                <td><?php echo $extra['master']['invoiceNote']; ?></td>
            </tr>
        </tbody>
    </table>
<?php } ?>
<?php if ($extra['master']['isPrintDN']==1 && $html!=1 && $is_item_active==1) { ?>
<pagebreak />
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px" src="<?php echo $this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td colspan="3">
                                <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?>.</strong></h3>
                                <h4><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note');?></h4><!--Delivery note-->
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note_number');?></strong></td><!--DN Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['deliveryNoteSystemCode']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_note_date');?></strong></td><!--DN Date-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['invoiceDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $this->lang->line('common_reference_number');?></strong></td><!--Reference Number-->
                            <td><strong>:</strong></td>
                            <td><?php echo $extra['master']['referenceNo']; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:23%;"><strong><?php echo $this->lang->line('common_customer_name');?> </strong></td><!--Customer Name-->
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:75%;"> <?php echo (empty($extra['master']['customerSystemCode'])) ? $extra['master']['customerName'] : $extra['master']['customerName'].' ( '.$extra['master']['customerSystemCode'].' )'; ?></td>
            </tr>
            <?php if (!empty($extra['master']['customerSystemCode'])) { ?>
            <tr>
                <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_customer_address');?>  </strong></td><!--Customer Address-->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['customerAddress']; ?></td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('common_telephone');?>/<?php echo $this->lang->line('common_fax');?></strong></td><!--Telephone / Fax -->
                <td><strong>:</strong></td>
                <td> <?php echo $extra['master']['customerTelephone'].' / '.$extra['master']['customerFax']; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td><strong><?php echo $this->lang->line('common_currency');?> </strong></td><!--Currency-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['CurrencyDes'].' ( '.$extra['master']['transactionCurrency'].' )'; ?></td>
            </tr>
            <tr>
                <td style="vertical-align: top"><strong><?php echo $this->lang->line('sales_markating_narration');?> </strong></td><!--Narration-->
                <td style="vertical-align: top"><strong>:</strong></td>
                <td colspan="4">
                    <table>
                        <tr>
                            <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['invoiceNarration']);?></td>
                        </tr>
                    </table>
                    <?php //echo $extra['master']['invoiceNarration']; ?>
                </td>
            </tr>
            <tr>
                <td><strong><?php echo $this->lang->line('sales_markating_view_invoice_delivery_due_date');?></strong></td><!--Delivery Date-->
                <td><strong>:</strong></td>
                <td colspan="4"> <?php echo $extra['master']['invoiceDueDate']; ?></td>
            </tr>
       </tbody>
    </table>
</div><br>
<?php $gran_total=0; $tax_transaction_total = 0;$tax_Local_total = 0;$tax_customer_total = 0; $p_total=0; if(!empty($extra['item_detail'])){ ?>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class='theadtr' colspan="5"><?php echo $this->lang->line('sales_markating_view_invoice_item_details');?></th><!--Item Details-->
            </tr>
            <tr>
                <th class='theadtr' style="min-width: 5%">#</th>
                <th class='theadtr' style="min-width: 15%"><?php echo $this->lang->line('sales_markating_view_invoice_item_code');?></th><!--Item Code-->
                <th class='theadtr' style="min-width: 65%"><?php echo $this->lang->line('sales_markating_view_invoice_item_description');?></th><!--Item Description-->
                <th class='theadtr' style="min-width: 10%"><?php echo $this->lang->line('sales_markating_view_invoice_uom');?></th><!--UOM-->
                <th class='theadtr' style="min-width: 5%"><?php echo $this->lang->line('sales_markating_view_invoice_qty');?></th><!--Qty-->
            </tr>
        </thead>
        <tbody>
            <?php
        $norecordfound =    $this->lang->line('common_no_records_found');
            $num =1;$item_total = 0;
            if (!empty($extra['item_detail'])) {
                foreach ($extra['item_detail'] as $val) { ?>
                <tr>
                    <td style="text-align:right;"><?php echo $num; ?>.&nbsp;</td>
                    <td style="text-align:center;"><?php echo $val['itemSystemCode'] . ' - ' . $val['itemSecondaryCode']; ?></td>
                    <td><?php echo ($val['contractCode'] ? $val['contractCode'].' - ' : '').$val['itemDescription'].' - '.$val['remarks']; ?></td>
                    <td style="text-align:center;"><?php echo $val['unitOfMeasure']; ?></td>
                    <td style="text-align:right;"><?php echo $val['requestedQty']; ?></td>
                </tr>
                <?php
                    $num ++;
                }
            }else{
                echo '<tr class="danger"><td colspan="5" class="text-center">'.$norecordfound.'</td></tr>';
            } ?><!--No Records Found-->
        </tbody>
    </table>
</div>
        <?php }?>

<div class="table-responsive"><br>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:30%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['createdUserName']; ?> on <?php echo $extra['master']['createdDateTime']; ?></td>
            </tr>
          <?php if ($extra['master']['confirmedYN']==1) { ?>
            <tr>
                <td><b>Confirmed By</b></td>
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['confirmedYNn'];?></td>

            </tr>
             <?php } ?>
        <?php if($extra['master']['approvedYN']){ ?>
            <tr>
                <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?></b></td><!--Electronically Approved By -->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?> </b></td><!--Electronically Approved Date-->
                <td><strong>:</strong></td>
                <td><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php } } ?>
    <br>
    <br>
    <br>
    <?php if($extra['master']['approvedYN']){ ?>
    <?php
    if ($signature) { ?>

        <?php
        if ($signature['approvalSignatureLevel'] <= 2) {
            $width = "width: 50%";
        } else {
            $width = "width: 100%";
        }
        ?>
        <div class="table-responsive">
            <table style="<?php echo $width ?>">
                <tbody>
                <tr>
                    <?php
                    for ($x = 0; $x < $signature['approvalSignatureLevel']; $x++) {
                        ?>
                        <td>
                            <span>____________________________</span><br><br><span><b>&nbsp; Authorized Signature</b></span>
                        </td>

                        <?php
                    }
                    ?>
                </tr>

                </tbody>
            </table>
        </div>
    <?php } ?>
    <?php } ?>
<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('invoices/load_invoices_conformation'); ?>/<?php echo $extra['master']['invoiceAutoID'] ?>";
    de_link="<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + <?php echo $extra['master']['invoiceAutoID'] ?> + '/CINV/'+ <?php echo $retantnINV ?>;
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href",de_link);


    $('.xEditableDate').editable({
        url: '<?php echo site_url('Invoices/update_supply_date') ?>',
        send: 'always',
        ajaxOptions: {
            type: 'post',
            dataType: 'json',
            success: function(data) {
                if(data[0]=='e')
                {
                    myAlert(data[0], data[1],data[2],data[3]);
                    setTimeout(function () {
                        $('#acknowledgementDate').editable('setValue', data[2],true);
                    }, 1500);
                }
            },
            error: function(xhr) {
                myAlert('e', xhr.responseText);
            }
        }
    });
    $('.xEditableDate').editable({
        format: 'DD-MM-YYYY',
        viewformat: 'DD.MM.YYYY',
        template: 'D/MMMM/YYYY ',
        combodate: {
            minYear: 1930,
            maxYear: <?php echo format_date_getYear() + 10 ?>,
            minuteStep: 1
        },
        success: function(response) {
            if (response) {
                myAlert('s', 'Acknowledgement Date Updated Successfully!');
            }
        }
    });
</script>