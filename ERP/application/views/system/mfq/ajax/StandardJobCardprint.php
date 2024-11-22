<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true, true);
$totalrow_material = 0;
$totallabour = 0;
$totaloverhead = 0;
$totalOutPut = 0;
?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name']?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'] . ', ' . $this->common_data['company_data']['company_address2'] . ', ' . $this->common_data['company_data']['company_city'] . ', ' . $this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('manufacturing_standard_job_card') ?><!--Standard Job Card--></h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('manufacturing_job_number') ?><!--Job Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $header['documentSystemCode'] ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('manufacturing_production_date') ?><!--Production Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td> <?php echo $header['ProductionDate'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
<div class="table-responsive"><br>

    <table style="width: 100%">
        <tbody>

        <tr>
            <td><strong><?php echo $this->lang->line('common_warehouse') ?><!--Ware House--></strong></td>
            <td><strong>:</strong></td>
            <td> <?php echo $header['warehouseDescription'] ?></td>
            <td><strong><?php echo $this->lang->line('common_segment') ?><!--Segment--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $header['segmentdescription'] ?></td>
        </tr>
        <tr>
            <td><strong><?php echo $this->lang->line('manufacturing_created_date') ?><!--Created Date--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $header['createdDate'] ?></td>
            <td><strong><?php echo $this->lang->line('common_expire_date') ?><!--Expiry Date--></strong></td>
            <td><strong>:</strong></td>
            <td colspan="4"> <?php echo $header['ExpiryDate'] ?></td>
        </tr>
        <tr>
            <td><strong>	<?php echo $this->lang->line('common_currency') ?><!--Currency--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $header['CurrencyName'] ?></td>
            <td><strong>	<?php echo $this->lang->line('manufacturing_batch_number') ?><!--Batch Number--></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $header['batchNumber'] ?></td>
        </tr>
        <tr>

            <td><strong><?php echo $this->lang->line('common_narration') ?><!--Narration--></strong></td>
            <td><strong>:</strong></td>
            <td colspan="4"> <?php echo $header['narration'] ?></td>
        </tr>
        </tbody>
    </table>
</div>
<br>


<h5>
    <strong><b><?php echo $this->lang->line('manufacturing_input') ?><!--Input--></b></strong>
</h5>

<?php if(!empty($row_material)) {?>
    <div class="table-responsive">
        <h5>
            <strong><u><?php echo $this->lang->line('manufacturing_raw_material') ?><!--Raw Material--></u></strong>
        </h5>
        <table class="table table-bordered table-striped">
            <thead class='thead'>
            <tr>
                <th style="min-width: 4%" class='theadtr'>#</th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_item_description') ?><!--Item Description-->	</th><!--Code-->
                <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_uom') ?><!--UOM--></th><!--Description-->
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty') ?><!--Qty--></th><!--UOM-->
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_unit_cost') ?><!--Unit Cost-->	</th><!--Qty-->
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('manufacturing_total_cost') ?><!--Total Cost--></th><!--Unit-->
            </tr>
            </thead>
            <tbody>
            <?php
            $totalrow_material = 0;
            $gran_total = 0;
            $tax_transaction_total = 0;
            $num = 1;
            if (!empty($row_material)) {
                foreach ($row_material as $val) { ?>
                    <tr>
                        <td class="text-right"><?php echo $num;?>.&nbsp;</td>
                        <td class="text-left"><?php echo $val['itemdessys']; ?></td>
                        <td class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                        <td class="text-right"><?php echo $val['qty']; ?></td>
                        <td class="text-right"><?php echo number_format($val['unitCost'],$val['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><?php echo number_format($val['totalCost'],$val['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $totalrow_material += $val['totalCost'];
                }
            } else {
                $norecordsfound= $this->lang->line('common_no_records_found');;

                echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecordsfound.'</td></tr>';
            } ?>

            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="5"> <?php echo $this->lang->line('common_total') ?><!--Total--></td>
                <td class="text-right total"><?php echo format_number($totalrow_material, $val['transactionCurrencyDecimalPlaces']); ?></td>

            </tr>
            </tfoot>
        </table>
    </div>


<?php }?>



<br>
<?php if(!empty($labour)) {?>
    <div class="table-responsive">
        <h5>
            <strong><u><?php echo $this->lang->line('manufacturing_labour') ?><!--Labour--></u></strong>
        </h5>
        <table class="table table-bordered table-striped">
            <thead class='thead'>
            <tr>
                <th style="min-width: 4%" class='theadtr'>#</th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('manufacturing_labour') ?><!--Labour--> 	</th><!--Code-->
                <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_uom') ?><!--UOM--></th><!--Description-->
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('manufacturing_unit_rate') ?><!--Unit Rate--></th><!--UOM-->
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_usage_hours') ?><!--Usage Hours-->	</th><!--Qty-->
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('manufacturing_total_amount') ?><!--Total Amount--></th><!--Unit-->
            </tr>
            </thead>
            <tbody>
            <?php
            $totallabour = 0;
            $gran_total = 0;
            $tax_transaction_total = 0;
            $num = 1;
            if (!empty($labour)) {
                foreach ($labour as $val) { ?>
                    <tr>
                        <td class="text-right"><?php echo $num;?>.&nbsp;</td>
                        <td class="text-left"><?php echo $val['description']; ?></td>
                        <td class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                        <td class="text-right"><?php echo number_format($val['hourlyRate'],$val['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><?php echo $val['totalHours']; ?></td>
                        <td class="text-right"><?php echo number_format( $val['totalValue'],$val['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $totallabour += $val['totalValue'];
                }
            } else {
                $norecordsfound= $this->lang->line('common_no_records_found');;

                echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecordsfound.'</td></tr>';
            } ?>

            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="5"> <?php echo $this->lang->line('common_total') ?><!--Total--></td>
                <td class="text-right total"><?php echo format_number($totallabour, $val['transactionCurrencyDecimalPlaces']); ?></td>

            </tr>
            </tfoot>
        </table>
    </div>
<?php }?>

<br>
<?php if(!empty($overhead)) {?>
    <div class="table-responsive">
        <h5>
            <strong><u><?php echo $this->lang->line('manufacturing_overhead') ?><!--Over Head--></u></strong>
        </h5>
        <table class="table table-bordered table-striped">
            <thead class='thead'>
            <tr>
                <th style="min-width: 4%" class='theadtr'>#</th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('manufacturing_overhead') ?><!--Over Head-->	</th><!--Code-->
                <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_uom') ?><!--UOM--></th><!--Description-->
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('manufacturing_unit_rate') ?><!--Unit Rate--></th><!--UOM-->
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_usage_hours') ?><!--Usage Hours--></th><!--Qty-->
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('manufacturing_total_amount') ?><!--Total Amount--></th><!--Unit-->
            </tr>
            </thead>
            <tbody>
            <?php
            $totaloverhead = 0;
            $gran_total = 0;
            $tax_transaction_total = 0;
            $num = 1;
            if (!empty($overhead)) {
                foreach ($overhead as $val) { ?>
                    <tr>
                        <td class="text-right"><?php echo $num;?>.&nbsp;</td>
                        <td class="text-left"><?php echo $val['Description']; ?></td>
                        <td class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                        <td class="text-right"><?php echo number_format($val['hourlyRate'],$val['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><?php echo $val['totalHours']; ?></td>
                        <td class="text-right"><?php echo number_format($val['hourlyRate'],$val['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $totaloverhead += $val['totalValue'];
                }
            } else {
                $norecordsfound= $this->lang->line('common_no_records_found');;

                echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecordsfound.'</td></tr>';
            } ?>

            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="5"> <?php echo $this->lang->line('common_total') ?><!--Total--></td>
                <td class="text-right total"><?php echo format_number($totaloverhead, $val['transactionCurrencyDecimalPlaces']); ?></td>

            </tr>
            </tfoot>
        </table>
    </div>
<?php }?>
<br>
<div class="table-responsive">
    <h5 class="text-right"><strong><?php echo $this->lang->line('manufacturing_total_input')?><!--Total Input :-->  <?php echo number_format($totalrow_material + $totallabour + $totaloverhead,$header['transactionCurrencyDecimalPlaces'])?></strong></h5>
</div>
<?php if(!empty($output)) {?>
    <h5>
        <strong><b><?php echo $this->lang->line('manufacturing_output')?><!--output--></b></strong>
    </h5>

    <div class="table-responsive">
        <h5>
            <strong><u><?php echo $this->lang->line('manufacturing_finish_goods')?><!--Finish Goods--></u></strong>
        </h5>
        <table class="table table-bordered table-striped">
            <thead class='thead'>
            <tr>
                <th style="min-width: 4%" class='theadtr'>#</th>
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_item_description')?><!--Item Description-->	</th><!--Code-->
                <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_uom')?><!--UOM--></th><!--Description-->
                <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_warehouse')?><!--Warehouse--></th><!--Description-->
                <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_expire_date')?><!--Expiry Date--></th><!--Description-->

                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_qty')?><!--Qty--></th><!--UOM-->
                <th style="min-width: 5%" class='theadtr'>%</th><!--UOM-->
                <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_unit_cost')?><!--Unit Cost-->	</th><!--Qty-->
                <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('manufacturing_total_cost')?><!--Total Cost--></th><!--Unit-->
            </tr>
            </thead>
            <tbody>
            <?php
            $totalOutPut = 0;
            $gran_total = 0;
            $tax_transaction_total = 0;
            $num = 1;
            if (!empty($output)) {
                foreach ($output as $val) { ?>
                    <tr>
                        <td class="text-right"><?php echo $num;?>.&nbsp;</td>
                        <td class="text-left"><?php echo $val['itemdessys']; ?></td>
                        <td class="text-center"><?php echo $val['unitOfMeasure']; ?></td>
                        <td class="text-center"><?php echo $val['wareHouseDescription']; ?></td>
                        <td class="text-center"><?php echo $val['expiryDate']; ?></td>
                        <td class="text-right"><?php echo $val['qty']; ?></td>
                        <td class="text-right"><?php echo $val['costAllocationPrc']; ?></td>
                        <td class="text-right"><?php echo number_format($val['unitCost'],$val['transactionCurrencyDecimalPlaces']); ?></td>
                        <td class="text-right"><?php echo number_format($val['totalCost'],$val['transactionCurrencyDecimalPlaces']); ?></td>
                    </tr>
                    <?php
                    $num++;
                    $totalOutPut += $val['totalCost'];
                }
            } else {
                $norecordsfound= $this->lang->line('common_no_records_found');;

                echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecordsfound.'</td></tr>';
            } ?>

            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="8"> Total</td>
                <td class="text-right total"><?php echo format_number($totalOutPut, $val['transactionCurrencyDecimalPlaces']); ?></td>

            </tr>
            </tfoot>
        </table>
    </div>
    <div class="table-responsive">
        <h5 class="text-right"><strong><?php echo $this->lang->line('manufacturing_total_output')?><!--Total Output--> :  <?php echo number_format($totalOutPut,$header['transactionCurrencyDecimalPlaces'])?> </strong> </h5>
    </div>





<?php }?>
<br>
<br>
<br>
<?php if(!empty($crew) || !empty($machine)){?>
    <pagebreak />
    <?php if(!empty($crew)){?>
        <div class="table-responsive">
            <h5>
                <strong><u><?php echo $this->lang->line('common_crew')?><!--CREW--></u></strong>
            </h5>
            <table class="table table-bordered table-striped">
                <thead class='thead'>
                <tr>
                    <th style="min-width: 4%" class='theadtr'>#</th>
                    <th style="min-width: 10%" class='theadtr'><?php echo $this->lang->line('common_name')?><!--Name-->	</th><!--Code-->
                    <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_designation')?><!--Designation--></th><!--Description-->
                    <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_start_time')?><!--Start Time--></th><!--UOM-->
                    <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_end_time')?><!--End Time--></th><!--UOM-->
                    <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_hours_spent')?><!--Hours Spent--></th><!--Qty-->
                </tr>
                </thead>
                <tbody>
                <?php
                $totalOutPut = 0;
                $gran_total = 0;
                $tax_transaction_total = 0;
                $num = 1;
                if (!empty($crew)) {
                    foreach ($crew as $val) { ?>
                        <tr>
                            <td class="text-right"><?php echo $num;?>.&nbsp;</td>
                            <td class="text-left"><?php echo $val['name']; ?></td>
                            <td class="text-left"><?php echo $val['Description']; ?></td>
                            <td class="text-left"><?php echo $val['startTime']; ?></td>
                            <td class="text-left"><?php echo $val['endTime']; ?></td>
                            <td class="text-right"><?php echo $val['hoursSpent']; ?></td>
                            <!-- <td class="text-center"><?php /*echo $val['unitOfMeasure']; */?></td>
                    <td class="text-right"><?php /*echo $val['qty']; */?></td>
                    <td class="text-right"><?php /*echo $val['costAllocationPrc']; */?></td>
                    <td class="text-right"><?php /*echo $val['unitCost']; */?></td>-->
                        </tr>
                        <?php
                        $num++;
                        /*
                          $totalOutPut += $val['totalCost'];*/
                    }
                } else {
                    $norecordsfound= $this->lang->line('common_no_records_found');;

                    echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecordsfound.'</td></tr>';
                } ?>

                </tbody>
            </table>
        </div>
    <?php }?>
    <br>
    <br>
    <br>
    <?php if(!empty($machine)){?>
        <div class="table-responsive">
            <h5>
                <strong><u><?php echo $this->lang->line('manufacturing_machine')?><!--MACHINE--></u></strong>
            </h5>
            <table class="table table-bordered table-striped">
                <thead class='thead'>
                <tr>
                    <th style="min-width: 4%" class='theadtr'>#</th>
                    <th style="min-width: 30%" class="text-left theadtr"><?php echo $this->lang->line('common_description')?>Description</th><!--Description-->
                    <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_start_time')?><!--Start Time--></th><!--UOM-->
                    <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_end_time')?><!--End Time--></th><!--UOM-->
                    <th style="min-width: 5%" class='theadtr'><?php echo $this->lang->line('common_hours_spent')?><!--Hours Spent--></th><!--Qty-->
                </tr>
                </thead>
                <tbody>
                <?php
                $totalOutPut = 0;
                $gran_total = 0;
                $tax_transaction_total = 0;
                $num = 1;
                if (!empty($machine)) {
                    foreach ($machine as $val) { ?>
                        <tr>
                            <td class="text-right"><?php echo $num;?>.&nbsp;</td>
                            <td class="text-left"><?php echo $val['Description']; ?></td>
                            <td class="text-left"><?php echo $val['startTime']; ?></td>
                            <td class="text-left"><?php echo $val['endTime']; ?></td>
                            <td class="text-right"><?php echo $val['hoursSpent']; ?></td>
                            <!-- <td class="text-center"><?php /*echo $val['unitOfMeasure']; */?></td>
                    <td class="text-right"><?php /*echo $val['qty']; */?></td>
                    <td class="text-right"><?php /*echo $val['costAllocationPrc']; */?></td>
                    <td class="text-right"><?php /*echo $val['unitCost']; */?></td>-->
                        </tr>
                        <?php
                        $num++;
                        /*$totalOutPut += $val['totalCost'];*/
                    }
                } else {
                    $norecordsfound= $this->lang->line('common_no_records_found');;

                    echo '<tr class="danger"><td colspan="9" class="text-center">'.$norecordsfound.'</td></tr>';
                } ?>

                </tbody>
            </table>
        </div>
    <?php }?>
<?php }?>

<div class="table-responsive">
    <table style="width: 100%">
        <tr>
            <td style="width:80%;">

                <table style="width: 100%">
                    <tbody>
                    <?php if($header['confirmedYN']==1){ ?>
                        <tr>
                            <td style="font-size:15px;font-family: tahoma;"><b><?php echo $this->lang->line('common_confirmed_by')?><!--Confirmed By--></b></td>
                            <td><strong>:</strong></td>
                            <td style="font-size:15px;font-family: tahoma;"><?php echo $header['confirmedByNamemfq']?></td>
                        </tr>
                    <?php } ?>
                    <?php if($header['approvedYN']){ ?>
                        <tr>
                            <td style="font-size:15px;font-family: tahoma;"><b><?php echo $this->lang->line('common_electronically_approved_by')?><!--Electronically Approved By--></b></td><!--Electronically Approved By-->
                            <td><strong>:</strong></td>
                            <td style="font-size:15px;font-family: tahoma;"><?php echo $header['approvedbyEmpName']; ?></td>
                        </tr>
                        <tr>
                            <td style="font-size:15px;font-family: tahoma;"><b><?php echo $this->lang->line('common_electronically_approved_date')?><!--Electronically Approved Date--></b></td><!--Electronically Approved Date-->
                            <td><strong>:</strong></td>
                            <td style="font-size:15px;font-family: tahoma;"><?php echo $header['approvedDate']; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

            </td>
            <td style="width:60%;">
                &nbsp;
            </td>
        </tr>
    </table>
</div>

<script>
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('MFQ_Job_standard/load_standardjobcard_print'); ?>/<?php echo $header['jobAutoID'] ?>";
    de_link = "<?php echo site_url('MFQ_Job_standard/fetch_double_entry_standardjobcard'); ?>/" + <?php echo $header['jobAutoID'] ?> +'/STJOB';
    $("#a_link").attr("href", a_link);
    $(".de_link").attr("href", de_link);
    $("#a_link").attr("href", a_link);
</script>