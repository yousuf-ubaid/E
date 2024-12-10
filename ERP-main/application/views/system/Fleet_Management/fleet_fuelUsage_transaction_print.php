

<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo fetch_account_review(false,true,$approval); ?>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:60%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo $logo.$this->common_data['company_data']['company_logo']; ?>">
                        </td>

                    </tr>
                </table>
            </td>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ')'; ?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                            <h4><?php echo $this->lang->line('fleet_fuel_usage');?><!--Fuel Usage--> </h4>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('fleet_document_Code');?><!-- Document Code --></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentCode']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('fleet_document_Date');?><!--Document Date--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['documentDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('fleet_supplier_name');?><!--Supplier Name--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['supplier']; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_reference');?><!--Reference Number--></strong></td>
                        <td><strong>:</strong></td>
                        <td><?php echo $extra['master']['referenceNumber']; ?></td>
                    </tr>
                    <tr>
                        <td style="width:15%;"><strong><?php echo $this->lang->line('common_currency');?><!--Currency--> </strong></td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:33%;"><?php echo $extra['master']['CurrencyDes'] . ' ( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
                    </tr>
                    <tr>
                        <td style="width:15%;vertical-align: top"><strong><?php echo $this->lang->line('common_narration');?><!--Narration--> </strong></td>
                        <td style="width:2%;vertical-align: top"><strong>:</strong></td>
                        <td style="width:33%;">
                            <table>
                                <tr>
                                    <td><?php echo str_replace(PHP_EOL, '<br /> ',  $extra['master']['narration']);?></td>
                                </tr>
                            </table>
                            <?php //echo $extra['master']['narration']; ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>

<br>
<br>
<br>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class='thead'>
        <tr>
            <th colspan="5"><?php echo $this->lang->line('fleet_Vehicle_Master'); ?></th>
            <th colspan="3"><?php echo $this->lang->line('fuel_purchase_details'); ?></th>
            <th colspan="3"><?php echo $this->lang->line('common_amount'); ?> <span class="currency"></span>
                 <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></th>
        </tr>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_category'); ?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('common_segment'); ?></th>
            <th style="min-width: 25%" class="text-center"><?php echo $this->lang->line('fleet_vehicle_usage'); ?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('fleet_driverName'); ?></th>

            <th style="min-width: 10%" class="text-left">
                <?php echo $this->lang->line('fleet_vehicle_fuel'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('fleet_start_km'); ?></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('fleet_end_km'); ?></th>
            <th style="min-width: 10%"><?php echo $this->lang->line('fuel_rate'); ?></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('common_total'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        $gran_total = 0;
        $tax_transaction_total = 0;
        $num = 1;
        if (!empty($extra['detail'])) {
            foreach ($extra['detail'] as $val) { ?>
                <tr>
                    <td class="text-right"><?php echo $num; ?>.&nbsp;</td>
                    <td class="text-center"><?php echo $val['glConfigDescription']; ?></td>
                    <td class="text-center"><?php echo $val['segmentCode']; ?></td>
                    <td class="text-left"><?php echo  $val['vehicale'];?> </td>
                    <td class="text-center"><?php echo $val['driverName']; ?></td>
                    <td class="text-center"><?php echo $val['fuel']; ?></td>
                    <td class="text-center"><?php echo $val['startKm']; ?></td>
                    <td><?php echo $val['endKm']; ?></td>
                    <td class="text-center"><?php echo $val['fuelRate']; ?></td>
                    <td class="text-right"><?php echo number_format($val['totalAmount'],$extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
                </tr>
                <?php


                $num++;
                $total += $val['totalAmount'];
                $gran_total += $val['totalAmount'];
                $tax_transaction_total += $val['totalAmount'];
            }
        } else {
            $NoRecordsFound = $this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="13" class="text-center">'.$NoRecordsFound.'<!--No Records Found--></td></tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td style="min-width: 85%  !important" class="text-right sub_total" colspan="9">
                <?php echo $this->lang->line('common_total');?><!--Total--> <?php echo '( ' . $extra['master']['transactionCurrency'] . ' )'; ?></td>
            <td style="min-width: 15% !important"
                class="text-right total"><?php echo number_format($total, $extra['master']['transactionCurrencyDecimalPlaces']); ?></td>
        </tr>
        </tfoot>
    </table>
</div>
<br>
<div class="table-responsive">
  <!--  <h5 class="text-right"> <?php echo $this->lang->line('common_total');?> (<?php echo $extra['master']['transactionCurrency']; ?> )</h5> -->
</div>

<script>
    $('.review').removeClass('hide');
    a_link=  "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>/<?php echo $extra['master']['fuelusageID'] ?>";
    de_link = "<?php echo site_url('Fleet/fetch_double_fuelusage'); ?>/<?php echo $extra['master']['fuelusageID'] ?>";
    $("#a_link").attr("href",a_link);
    $("#de_link").attr("href", de_link);
</script>



