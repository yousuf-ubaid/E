<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_markating_approval', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

echo fetch_account_review(true, true, $approval);
/*echo "<pre>";
print_r($extra['invoice']);
echo "</pre>";
exit;*/
$num = count($extra['invoice']);
if (!empty($extra['invoice'])) {
    $i = 0;
    foreach ($extra['invoice'] as $val) {
        $invoice_total = 0;
        $percentage = $val['salesperson']['percentage'];
        $commission_total = 0;
        $adjustment = $val['salesperson']['adjustment']; ?>
        <div class="table-responsive">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td style="width:60%;">
                        <table>
                            <tr>
                                <td>
                                    <img alt="Logo" style="height: 130px" src="<?php
                                    echo $logo . $this->common_data['company_data']['company_logo']; ?>">
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:40%;">
                        <table>
                            <tr>
                                <td colspan="3">
                                    <h3>
                                        <strong><?php echo $this->common_data['company_data']['company_name']; ?></strong>
                                    </h3>
                                    <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                                    <h4><?php echo $this->lang->line('sales_markating_sales_sales_commission');?></h4><!--Sales Commission-->
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $this->lang->line('sales_markating_sales_sales_commission_number');?></strong></td><!--SC Number-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['salesCommisionCode']; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo $this->lang->line('sales_markating_sales_sales_commission_sc_as_of_date');?></strong></td><!--SC As of Date-->
                                <td><strong>:</strong></td>
                                <td><?php echo $extra['master']['asOfDate']; ?></td>
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
                    <td style="width:20%;vertical-align: top;">
                    <?php if($val['salesperson']['salesPersonImage']!='images/users/default.gif'){?>
                        <img style="width: 130px; height: 150px;" src="<?php echo $this->s3->createPresignedRequest($val['salesperson']['salesPersonImage'], '1 hour'); ?>" id="changeImg">

                    <?php }else {?>
                        <img style="width: 130px; height: 150px;" src="<?php echo $this->s3->createPresignedRequest('images/default.gif', '1 hour'); ?>" id="changeImg">
                    <?php }?>

                    </td>
                    <td style="width:30%;">
                        <table>
                            <tr>
                                <td style="width:25%;"><?php echo $this->lang->line('common_name');?></td><!-- Name-->
                                <td style="width:5%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $val['salesperson']['SalesPersonName']; ?></td>
                            </tr>
                            <tr>
                                <td style="width:25%;"><?php echo $this->lang->line('common_code');?> </td><!--Code-->
                                <td style="width:5%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $val['salesperson']['SalesPersonCode']; ?></td>
                            </tr>
                            <tr>
                                <td style="width:25%;"><?php echo $this->lang->line('sales_markating_sales_sales_commission_secondary_code');?> </td><!--Secondary Code-->
                                <td style="width:5%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $val['salesperson']['SecondaryCode']; ?></td>
                            </tr>
                            <tr>
                                <td style="width:25%;"><?php echo $this->lang->line('common_telephone');?> </td><!--Telephone-->
                                <td style="width:5%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $val['salesperson']['contactNumber']; ?></td>
                            </tr>
                            <tr>
                                <td style="width:25%;"><?php echo $this->lang->line('common_email');?> </td><!--Email-->
                                <td style="width:5%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $val['salesperson']['SalesPersonEmail']; ?></td>
                            </tr>
                            <tr>
                                <td style="width:25%;"><?php echo $this->lang->line('common_Location');?> </td><!--Location-->
                                <td style="width:5%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $val['salesperson']['wareHouseDescription']; ?></td>
                            <tr>
                                <td style="width:25%;"><?php echo $this->lang->line('common_address');?></td><!--Address-->
                                <td style="width:5%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $val['salesperson']['SalesPersonAddress']; ?></td>
                            </tr>
                            <tr>
                                <td style="width:25%;"><?php echo $this->lang->line('common_currency');?> </td><!--Currency-->
                                <td style="width:5%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo $val['salesperson']['salesPersonCurrency']; ?></td>
                            </tr>
                            <tr>
                                <td style="width:25%;"><?php echo $this->lang->line('sales_markating_sales_sales_commission_target');?><!--Target-->


                                    [ <?php echo($val['salesperson']['salesPersonTargetType'] == 1 ? 'Yearly': 'Monthly'); ?>
                                    ]
                                </td>
                                <td style="width:5%;"><strong>:</strong></td>
                                <td style="width:70%;"><?php echo number_format($val['salesperson']['salesPersonTarget'], $val['salesperson']['salesPersonCurrencyDecimalPlaces']); ?></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width:50%;vertical-align: top;">
                        <table class="table table-bordered table-striped table-condensed table-row-select">
                            <thead>
                            <tr>
                                <th style="min-width: 5px">#</th>
                                <!--<th>Date from</th>
                                <th>Date to</th>-->
                                <th style="min-width: 50px"><?php echo $this->lang->line('sales_markating_sales_sales_commission_start_amount');?><!--Start Amount--> <span
                                            class="currency">( <?php echo $val['salesperson']['salesPersonCurrency']; ?>
                                        )</span></th>
                                <th style="min-width: 50px"><?php echo $this->lang->line('sales_markating_sales_sales_commission_end_amount');?><!--End Amount--> <span
                                            class="currency">( <?php echo $val['salesperson']['salesPersonCurrency']; ?>
                                        )</span></th>
                                <th style="min-width: 10px">%</th>
                            </tr>
                            </thead>
                            <tbody id="table_body">
                            <?php $x = 1;
                            if (!empty($val['salestarget'])) {
                                foreach ($val['salestarget'] as $value) {
                                    $amount = number_format($value['fromTargetAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . ' - ' . number_format($value['toTargetAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                    $startamount = number_format($value['fromTargetAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                    $endamount = number_format($value['toTargetAmount'], $extra['master']['transactionCurrencyDecimalPlaces']);
                                    echo "<tr><td>{$x}</td><td class='text-right'>{$startamount}</td><td class='text-right'>{$endamount}</td><td class='text-right'>{$value['percentage']}%</td></tr>";
                                    $x++;
                                }
                            } else {
                                $norecordsfound = $this->lang->line('common_no_records_found');

                                echo "<tr class='danger'><td colspan='5'><center>'.$norecordsfound.'</center></td></tr>";
                            }
                            ?>
                            <!--No record found-->

                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div><br>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                <tr>
                    <th style="width: 20px;">#</th>
                    <th><?php echo $this->lang->line('sales_markating_sales_sales_commission_customer_invoice');?></th><!--Customer Invoice-->
                    <th style="width: 150px;"><?php echo $this->lang->line('common_total');?></th><!--Total-->
                </tr>
                </thead>
                <tbody>
                <?php $x = 1;
                if (!empty($val['invoice'])) {
                    foreach ($val['invoice'] as $value) {
                        $amount = number_format($value['companyLocalAmount'], $value['companyLocalCurrencyDecimalPlaces']);
                        echo "<tr><td>{$x}</td><td>{$value['invoiceCode']} - {$value['invoiceDate']} - {$value['customerName']} - {$value['invoiceNarration']}</td><td class='text-right'>{$value['companyLocalCurrency']} <span>{$amount}</span></td></tr>";
                        $x++;
                        $invoice_total += $value['companyLocalAmount'];
                        $commission_total = (($invoice_total / 100) * $percentage);
                    }
                } else {
                    $norecordsfound = $this->lang->line('common_no_records_found');

                    echo "<tr class='danger'><td colspan='3'><center>$norecordsfound</center></td></tr>";
                }

                ?>
                <!--No record found-->
                </tbody>
            </table>
        </div>
        <br>
        <div class="table-responsive">
            <table>
                <tr>
                    <td class="text-right"><?php echo $this->lang->line('sales_markating_sales_sales_commission_customer_invoice_total');?><!--Invoice total --><span
                                class="currency">( <?php echo $val['salesperson']['salesPersonCurrency']; ?> )</span>
                    </td>
                    <td class="text-right"><?php echo number_format($invoice_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-right"><?php echo $this->lang->line('common_percentage');?><!--Percentage --><span
                                class="currency">( <?php echo $val['salesperson']['salesPersonCurrency']; ?> )</span>
                    </td>
                    <td class="text-right">
                        <?php echo $percentage; ?>%
                    </td>
                </tr>
                <tr>
                    <td class="text-right"><?php echo $this->lang->line('sales_markating_sales_sales_commission_customer_invoice_commission_total');?><!--Commission total--> <span
                                class="currency">( <?php echo $val['salesperson']['salesPersonCurrency']; ?> )</span>
                    </td>
                    <td class="text-right"><?php echo number_format($commission_total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <tr>
                    <td class="text-right"><?php echo $this->lang->line('sales_markating_sales_sales_commission_customer_invoice_adjustments');?><!--Adjustments--> <span
                                class="currency">( <?php echo $val['salesperson']['salesPersonCurrency']; ?>
                            )</span> </td>
                    <td class="text-right">
                        <?php echo number_format($adjustment, $extra['master']['transactionCurrencyDecimalPlaces']); ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-right"><?php echo $this->lang->line('sales_markating_sales_sales_commission_customer_net_commission');?><!--Net Commission--> <span
                                class="currency">( <?php echo $val['salesperson']['salesPersonCurrency']; ?> )</span>
                    </td>
                    <td class="text-right"><?php echo number_format(($commission_total + $adjustment), $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
            </table>
        </div>
        <?php
        if ($num != ($i + 1)) {
            echo '<pagebreak>';
        }
        $i++;
    }
} ?>
<br>
<br>
<br>
<br>

<div class="table-responsive">
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
                <td style="width:30%;"><b>Confirmed By </b></td>
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['confirmedYNn'];?></td>
            </tr>
        <?php } ?>
        <?php if ($extra['master']['approvedYN']) { ?>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_by');?> </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedbyEmpName']; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b><?php echo $this->lang->line('sales_markating_view_invoice_electronically_approved_date');?></b></td><!--Electronically Approved Date -->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $extra['master']['approvedDate']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
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
    a_link = "<?php echo site_url('Sales/load_sc_conformation'); ?>/<?php echo $extra['master']['salesCommisionID'] ?>";
    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_SC'); ?>/" + <?php echo $extra['master']['salesCommisionID'] ?> +'/SC';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);
</script>