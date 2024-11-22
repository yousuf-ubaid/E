<?php
$isRptCost = false;
$isLocCost = false;
$statusText = "";
?>

<div id="tbl_purchase_order_list">
    <div class="table-responsive">
        <table style="width: 90%">
            <tbody>
            <tr>
                <td style="width:50%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 140px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                            <td style="text-align:center;font-size: 18px;font-family: tahoma;">
                                <strong style="font-weight: bold;"><?php echo $this->common_data['company_data']['company_name']; ?>.</strong><br>

                                <strong style="font-weight: bold;">  <?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?>.</strong><br>
                                <strong style="font-weight: bold;">  Tel :  <?php echo $this->common_data['company_data']['company_phone'] ?></strong><br>
                                <br>
                                Disaster Assessment Summary Report
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            </tbody>
        </table>
    </div>
    <br>
    <div class="col-sm-12" style="width:100%;font-size: 15px;font-family: tahoma;font-weight: 900;">
        <strong>Project : </strong> <?php echo $project[0]['projectName'] ?>
    </div>
    <br>

        <div class="row">
            <div class="col-sm-6" style="font-size: 10px;font-family: tahoma;">
                <strong> Country : </strong> <?php if (!empty($country_drop)) {
                    $country = $this->lang->line('country');
                    echo '' . $country . ' ';
                    $tmpArray = array();
                    foreach ($country_drop as $row) {
                        $tmpArray[] = $row['CountryDes'];
                    }
                    echo join(', ', $tmpArray);
                } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" style="font-size: 10px;font-family: tahoma;">
                <strong> Province : </strong> <?php if (!empty($province_drop)) {
                    $province = $this->lang->line('province');
                    echo '' . $province . ' ';
                    $tmpArray = array();
                    foreach ($province_drop as $row) {
                        $tmpArray[] = $row['Description'];
                    }
                    echo join(', ', $tmpArray);
                } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" style="font-size: 10px;font-family: tahoma;">
                <strong> District : </strong> <?php if (!empty($area_drop)) {
                    $district = $this->lang->line('district');
                    echo '' . $district . ' ';
                    $tmpArray = array();
                    foreach ($area_drop as $row) {
                        $tmpArray[] = $row['Description'];
                    }
                    echo join(', ', $tmpArray);
                } ?>
            </div>
        </div>
    <div class="row">
        <div class="col-sm-6" style="font-size: 10px;font-family: tahoma;">
            <strong> Division : </strong> <?php if (!empty($da_division_drop)) {
                $division = $this->lang->line('division');
                echo '' . $division . ' ';
                $tmpArray = array();
                foreach ($da_division_drop as $row) {
                    $tmpArray[] = $row['Description'];
                }
                echo join(', ', $tmpArray);
            } ?>
        </div>
    </div>
        <?php
        $houseRepairTotal = 0;
        $humanInjuryTotal = 0;
        $houseDamageLossTotal = 0;
        $businessPropertyTotal = 0;
        $overAllTotal = 0;
        $humancount = 0;
        $houseitemcount = 0;
        $businessdamagecount = 0;
        $buildingdamagecount = 0;
        $housedamagecount = 0;
        $overalcounttotal = 0;

        if (!empty($damageRecord)) {
            foreach ($damageRecord as $row) {
                $lineTotal = 0;
                //echo '<pre>'; echo($row["da_estimatedRepairingCost"]); echo '</pre><br>';
                $houseRepairTotal += $row["da_estimatedRepairingCost"];
                $humanInjuryTotal += $row["humanInjuryAmount"];
                $houseDamageLossTotal += $row["houseDamageLoss"];
                $businessPropertyTotal += $row["businessPropertyValue"];
                $humancount += $row["humanCount"];
                $lineTotal = $row["da_estimatedRepairingCost"] + $row["humanInjuryAmount"] + $row["houseDamageLoss"] + $row["businessPropertyValue"];
                $houseitemcount += $row["houseitemcount"];
                $businessdamagecount += $row["businessdamagecount"];
                $housedamagecount += $row["housedamagecount"];
                $overAllTotal += $lineTotal;
            }
        } ?>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="reportHeaderColor" style="font-size: 15px;font-family: tahoma; font-weight: 900">Summary</div>
            </div>
        </div>
        <?php if (!empty($damageRecord)) { ?>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report" style="width: 50%">
                        <thead class="report-header">
                        <tr>
                            <th>Damage Type</th>
                            <th>Count</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>House</td>
                            <td style='text-align: center'><?php echo $housedamagecount ?></td>
                            <td style='text-align: right'><?php echo number_format($houseRepairTotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Human Injury</td>
                            <td style='text-align: center'><?php echo $humancount ?></td>
                            <td style='text-align: right'><?php echo number_format($humanInjuryTotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td>House Items</td>
                            <td style='text-align: center'><?php echo $houseitemcount ?></td>
                            <td style='text-align: right'><?php echo number_format($houseDamageLossTotal, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Business Property</td>
                            <td style='text-align: center'><?php echo $businessdamagecount ?></td>
                            <td style='text-align: right'><?php echo number_format($businessPropertyTotal, 2); ?></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="text-right sub_total" colspan="2" style="text-align: center;">Total</td>
                            <td class="text-right total"><?php echo number_format($overAllTotal, 2); ?></td>
                        </tr>
                        </tfoot>

                    </table>
                </div>
                <?php
                } else {
                    echo warning_message("No Records Found!");
                }
                ?>
            </div>
        </div>
        <br>
        <?php if (!empty($damageRecord)) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="reportHeader reportHeaderColor" style="font-size: 15px;font-family: tahoma; font-weight: 900">Disaster Assessment Detail</div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Occupation</th>
                            <th>Contact No</th>
                            <th>House</th>
                            <th>Human Injury</th>
                            <th>House Items</th>
                            <th>Business Property</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x = 1;
                        $houseRepairTotal = 0;
                        $humanInjuryTotal = 0;
                        $houseDamageLossTotal = 0;
                        $businessPropertyTotal = 0;
                        $overAllTotal = 0;
                        $humancount = 0;
                        $houseitemcount = 0;
                        $businessdamagecount = 0;
                        $buildingdamagecount = 0;
                        $housedamagecount = 0;
                        $overalcounttotal = 0;

                        if (!empty($damageRecord)) {
                            foreach ($damageRecord as $row) {
                                $lineTotal = 0;
                                echo "<tr>";
                                echo "<td>" . $x . "</td>";
                                echo "<td>" . $row["fullName"] . "</td>";
                                echo "<td>" . $row["address"] . "</td>";
                                echo "<td>" . $row["JobCatDescription"] . "</td>";
                                echo "<td>" . $row["phonePrimary"] . "</td>";
                                echo "<td style='text-align: right'>" . number_format($row["da_estimatedRepairingCost"], 2) . "</td>";
                                echo "<td style='text-align: right'>" . number_format($row["humanInjuryAmount"], 2) . "</td>";
                                echo "<td style='text-align: right'>" . number_format($row["houseDamageLoss"], 2) . "</td>";
                                echo "<td style='text-align: right'>" . number_format($row["businessPropertyValue"], 2) . "</td>";
                                $lineTotal = $row["da_estimatedRepairingCost"] + $row["humanInjuryAmount"] + $row["houseDamageLoss"] + $row["businessPropertyValue"];
                                echo "<td style='text-align: right'>" . number_format($lineTotal, 2) . "</td>";
                                echo "</tr>";
                                $x++;
                                $houseRepairTotal += $row["da_estimatedRepairingCost"];
                                $humanInjuryTotal += $row["humanInjuryAmount"];
                                $houseDamageLossTotal += $row["houseDamageLoss"];
                                $businessPropertyTotal += $row["businessPropertyValue"];
                                $humancount += $row["humanCount"];
                                $houseitemcount += $row["houseitemcount"];
                                $businessdamagecount += $row["businessdamagecount"];
                                $housedamagecount += $row["housedamagecount"];
                                $overAllTotal += $lineTotal;
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="text-right sub_total" colspan="5" style="text-align: center;">Total</td>
                            <td class="text-right total"><?php echo number_format($houseRepairTotal, 2); ?></td>
                            <td class="text-right total"><?php echo number_format($humanInjuryTotal, 2); ?></td>
                            <td class="text-right total"><?php echo number_format($houseDamageLossTotal, 2); ?></td>
                            <td class="text-right total"><?php echo number_format($businessPropertyTotal, 2); ?></td>
                            <td class="text-right total"><?php echo number_format($overAllTotal, 2); ?></td>
                        </tr>
                        </tfoot>

                    </table>
                </div>
                <hr style="border-top: 1px solid #8e2828;">

                <?php
                } else {
                    echo warning_message("No Records Found!");
                }
                ?>
            </div>
        </div>
    </div>