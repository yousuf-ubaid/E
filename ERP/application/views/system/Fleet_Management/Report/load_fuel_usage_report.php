<?php
$primaryLanguage = getPrimaryLanguage();
//$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('fleet_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('fuelUsageReport', 'Fuel usage', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="fuelUsageReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Fuel Usage</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Document Code</th>
                        <th>Document Date</th>
                        <th>Party</th>
                        <th>Segment</th>
                        <th>Vehicle</th>
                        <th>Exp Km/L</th>
                        <th>Fuel Type</th>
                        <th>Fuel Rate</th>
                        <th>Start Km</th>
                        <th>End Km</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $details = array_group_by($details, 'documentCode');
                        foreach ($details as  $key => $value) {

                          //  var_dump($value['fuelusageID']);
                            ?>
                            <tr>
                        <!--        <td class="mailbox-name"><a href="#" style="color: #40adff;" onclick="fetchPage('system/Fleet_Management/fleet_saf_fuelusage_approval/PageView_modal','<?php echo $key ?>','View Document','FU')"><?php echo $key; ?></a></td>
                           -->
                                <td style="text-align: right"><a href="#" onclick="PageView_modal('FU','<?php echo $key ?>')"><?php echo $key ?></a>
                                </td>
                              <!--  <td class="" colspan="7"><?php echo $key ?></td>
                              -->
                            </tr>

                            <?php
                            $total=0;
                            foreach ($value as $val) {
                                ?>
                                <tr>

                                    <td ><?php //echo $val["documentCode"] ?></td>
                                    <td ><?php echo $val["documentDate"] ?></td>
                                    <td ><?php echo $val["supplier"] ?></td>
                                    <td ><?php echo $val["segmentCode"] ?></td>
                                    <td class="text-center"><?php echo $val["VehicleNo"] ?></td>
                                    <td class="pull-right"><?php echo $val["expKMperLiter"] ?></td>
                                    <td class="text-center"><?php echo $val["FuelType"] ?></td>
                                    <td class="pull-right"><?php echo number_format($val['fuelRate'],$val['transactionCurrencyDecimalPlaces']) ?></td>
                                    <td class="text-center"><?php echo $val["startKm"] ?></td>
                                    <td class="text-center"><?php echo $val["endKm"] ?></td>
                                    <td class="pull-right"><?php echo number_format($val['totalAmount'],$val['transactionCurrencyDecimalPlaces']) ?></td>
                           <!--         <td ></td>
                                    <td ></td>
                                    <td ></td>
                                    -->
                                </tr>
                                <?php

                                $total += $val["totalAmount"];
                            }
                            ?>
                            <tr>
                                <td colspan="10" class="text-right"><b>Total Amount </b></td>
                                <td class="text-right reporttotal"><?php echo number_format($total,$val['transactionCurrencyDecimalPlaces']) ?></td>

                            </tr>

                            <?php
                        }
                    } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
    <script>
        $('#div_fuelusage_history').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 10
        });

    </script>

