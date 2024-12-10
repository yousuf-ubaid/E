<!---- =============================================
-- File Name : production_report.php
-- Project Name : SME ERP
-- Module Name : Report - Production Report
-- Create date : 09 - September 2017
-- Description : This file contains Buyback Production Report.

-- REVISION HISTORY
-- =============================================-->
<style>
    hr {
        margin-top: 0px;
        margin-bottom: 0px;
        border: 0;
        border-top: 1px solid #eee;
    }

    .dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        display: inline-block;
    }
</style>
<?php
$isRptCost = false;
$isLocCost = false;
$statusText = "";
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);
?>
<div class="row">
    <div class="col-md-6">
        <div style="font-size: 16px; font-weight: 700;"></div>
    </div>

    <div class="col-md-6 damageassestmentrptcls_cls">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_purchase_order_list', 'Disaster Assesment Report');
        } ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="text-center reportHeaderColor">
            <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
        </div>
        <div class="text-center reportHeaderColor">
            <strong><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?> </strong>
        </div>
        <div class="text-center reportHeaderColor">
            <strong>Tel : <?php echo $this->common_data['company_data']['company_phone'] ?> </strong>
        </div>

        <div class="text-center reportHeader reportHeaderColor"> Disaster Assessment Report</div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-6">
        <strong> Project : </strong> <?php echo $project[0]['projectName'] ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-6">
        <strong>Filters <i class="fa fa-filter"></i></strong><br/>

        <!--    <div class="row">
            <div class="col-sm-2">
                Country :
            </div>
            <div class="col-sm-10">
                <?php /*if (!empty($country_drop)) {
                    $country = $this->lang->line('country');
                    echo '' . $country . ' ';
                    $tmpArray = array();
                    foreach ($country_drop as $row) {
                        $tmpArray[] = $row['CountryDes'];
                    }
                    echo join(', ', $tmpArray);
                } */ ?>
            </div>
        </div>-->
        <!--<div class="row">
            <div class="col-sm-2">
                Province:
            </div>
            <div class="col-sm-10">
                <?php /*if (!empty($province_drop)) {
                    $province = $this->lang->line('province');
                    echo '' . $province . ' ';
                    $tmpArray = array();
                    foreach ($province_drop as $row) {
                        $tmpArray[] = $row['Description'];
                    }
                    echo join(', ', $tmpArray);
                } */ ?>
            </div>
        </div>-->
        <!--   <div class="row">
            <div class="col-sm-2">
                District:
            </div>
            <div class="col-sm-10">
                <?php /*if (!empty($area_drop)) {
                    $district = $this->lang->line('district');
                    echo '' . $district . ' ';
                    $tmpArray = array();
                    foreach ($area_drop as $row) {
                        $tmpArray[] = $row['Description'];
                    }
                    echo join(', ', $tmpArray);
                } */ ?>
            </div>
        </div>-->
        <!--<div class="row">
            <div class="col-sm-2">
                Division:
            </div>
        <div class="col-sm-10">
            <?php
        /*            $tmpArray = array();
                    foreach ($da_division_drop as $row) {
                        $tmpArray[] = $row['Description'];
                    }
                    echo join(', ', $tmpArray);
                    */ ?>
        </div>-->
        <div class="col-sm-12" style="font-size: 12px;font-family: tahoma;">
            <strong> Country : </strong> <?php if (!empty($country_drop)) {
                $province = $this->lang->line('province');
                echo '' . $province . ' ';
                $tmpArray = array();
                foreach ($country_drop as $row) {
                    $tmpArray[] = $row['CountryDes'];
                }
                echo join(', ', $tmpArray);
            } ?>
        </div>
        <div class="col-sm-12" style="font-size: 12px;font-family: tahoma;">
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
        <div class="col-sm-12" style="font-size: 12px;font-family: tahoma;">
            <strong> District : </strong> <?php if (!empty($area_drop)) {
                $province = $this->lang->line('province');
                echo '' . $province . ' ';
                $tmpArray = array();
                foreach ($area_drop as $row) {
                    $tmpArray[] = $row['Description'];
                }
                echo join(', ', $tmpArray);
            } ?>
        </div>

        <div class="col-sm-12" style="font-size: 12px;font-family: tahoma;">
            <strong> Division : </strong> <?php if (!empty($da_division_drop)) {
                $province = $this->lang->line('province');
                echo '' . $province . ' ';
                $tmpArray = array();
                foreach ($da_division_drop as $row) {
                    $tmpArray[] = $row['Description'];
                }
                echo join(', ', $tmpArray);
            } ?>
        </div>
    </div>
</div>
</div>
<!--<div class="col-sm-6 reportHeaderColor">
        <div class="row">
            <div class="col-sm-3">
                <strong> Jamiya Division :</strong>
            </div>
            <div class="col-sm-9">
                <?php
/*              $jamiyadivision = $this->lang->line('da_jammiyahDivision');
                echo '' . $jamiyadivision . ' ';
                $tmpArray = array();
                foreach ($da_jammiyahDivision_drop as $row) {
                    $tmpArray[] = $row['Description'];
                }
                echo join(', ', $tmpArray);
                */ ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <strong> Division :</strong>
            </div>

        <div class="row">
            <div class="col-sm-2">
                <strong> Mahalla :</strong>
            </div>
            <div class="col-sm-10">
                <?php
/*                $tmpArray = array();
                foreach ($da_sub_division_drop as $row) {
                    $tmpArray[] = $row['Description'];
                }
                echo join(', ', $tmpArray);
                */ ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <strong> GN Division :</strong>
            </div>
            <div class="col-sm-9">
                <?php
/*               $tmpArray = array();
                foreach ($da_sub_gn_division_drop as $row) {
                    $tmpArray[] = $row['Description'];
                }
                echo join(', ', $tmpArray);
                */ ?>
            </div>
        </div>
    </div>-->
</div>

</div>
<br>
<ul class="nav nav-tabs" id="main-tabs">
    <li class="active"><a href="#summery" data-toggle="tab" onclick="tabwisepdf(1);"><i class="fa fa-television"></i>Summary</a>
    </li>
    <li><a href="#detail" data-toggle="tab" onclick="tabwisepdf(2);"><i class="fa fa-television"></i>Detail </a></li>
    <li><a href="#housedamage" data-toggle="tab" onclick="tabwisepdf(3);"><i class="fa fa-television"></i>House </a>
    </li>
    <li><a href="#building" data-toggle="tab" onclick="tabwisepdf(4);"><i class="fa fa-television"></i>Building </a>
    </li>
    <li><a href="#helpaid" data-toggle="tab" onclick="tabwisepdf(5);"><i class="fa fa-television"></i>Help Aid </a></li>
</ul>

<div class="tab-content">
    <input type="hidden" name="tabid" id="tabid" value="1" style="display:none;">
    <div class="tab-pane active" id="summery">
        <div id="tbl_purchase_order_list">
            <div class="row hide">
                <div class="col-md-12">
                    <div class="text-center reportHeaderColor">
                        <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
                    </div>
                    <div class="text-center reportHeaderColor">
                        <strong><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?> </strong>
                    </div>
                    <div class="text-center reportHeaderColor">
                        <strong>Tel : <?php echo $this->common_data['company_data']['company_phone'] ?> </strong>
                    </div>

                    <div class="text-center reportHeader reportHeaderColor"><u>Disaster Assessment Report</u></div>

                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <strong> Project : </strong> <?php echo $project[0]['projectName'] ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-6">
                        <strong>Filters <i class="fa fa-filter"></i></strong><br/>
                        <div class="col-sm-12" style="font-size: 11px;font-family: tahoma;">
                            <strong> Country : </strong> <?php if (!empty($country_drop)) {
                                $province = $this->lang->line('province');
                                echo '' . $province . ' ';
                                $tmpArray = array();
                                foreach ($country_drop as $row) {
                                    $tmpArray[] = $row['CountryDes'];
                                }
                                echo join(', ', $tmpArray);
                            } ?>
                        </div>
                        <div class="col-sm-12" style="font-size: 11px;font-family: tahoma;">
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
                        <div class="col-sm-12" style="font-size: 11px;font-family: tahoma;">
                            <strong> District : </strong> <?php if (!empty($area_drop)) {
                                $province = $this->lang->line('province');
                                echo '' . $province . ' ';
                                $tmpArray = array();
                                foreach ($area_drop as $row) {
                                    $tmpArray[] = $row['Description'];
                                }
                                echo join(', ', $tmpArray);
                            } ?>
                        </div>

                        <div class="col-sm-12" style="font-size: 11px;font-family: tahoma;">
                            <strong> Division : </strong> <?php if (!empty($da_division_drop)) {
                                $province = $this->lang->line('province');
                                echo '' . $province . ' ';
                                $tmpArray = array();
                                foreach ($da_division_drop as $row) {
                                    $tmpArray[] = $row['Description'];
                                }
                                echo join(', ', $tmpArray);
                            } ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($damageRecord)) { ?>
            <div class="row" style="margin-top: 10px">
                <div class="col-md-12">
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
                    foreach ($damageRecord as $row) {
                        $lineTotal = 0;
                        $totalcount = 0;
                        $x++;
                        $lineTotal = $row["da_estimatedRepairingCost"] + $row["humanInjuryAmount"] + $row["houseDamageLoss"] + $row["businessPropertyValue"];
                        $houseRepairTotal += $row["da_estimatedRepairingCost"];
                        $humanInjuryTotal += $row["humanInjuryAmount"];
                        $houseDamageLossTotal += $row["houseDamageLoss"];
                        $businessPropertyTotal += $row["businessPropertyValue"];
                        $humancount += $row["humanCount"];
                        $houseitemcount += $row["houseitemcount"];
                        $businessdamagecount += $row["businessdamagecount"];
                        $housedamagecount += $row["housedamagecount"];
                        $overAllTotal += $lineTotal;
                        $overalcounttotal += $totalcount;
                    }

                    } ?>
                </div>
            </div>
            <br>
            <?php if (!empty($damageRecord)) { ?>
            <div class="row" style="margin-top: 10px">
                <div class="form-group col-sm-6">
                    <div class="fixHeader_Div">
                        <table class="borderSpace report-table-condensed" id="tbl_report" style="width: 100%">
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
                    <br>
                    <?php
                    } else {
                        echo warning_message("No Records Found!");
                    }
                    ?>
                </div>
                <?php if (!empty($damageRecord)) { ?>
                <div class="form-group col-sm-6">
                    <div id="disasterasstmentrpt"
                         style="min-width: 390px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                    <span class="dot" style="background-color: #4885ed;"></span> <strong>Business Property</strong>&nbsp;&nbsp;
                    <span class="dot" style="background-color: #93CBEC;"></span> <strong>House</strong>&nbsp;&nbsp;
                    <span class="dot" style="background-color: #83D88E;"></span> <strong>Human Injury</strong>&nbsp;&nbsp;
                    <span class="dot" style="background-color: #FABEB1;"></span> <strong>House Items</strong>&nbsp;
                </div>
            </div>
        <?php } ?>
            <?php if (!empty($damageRecord)) { ?>
            <div class="row hide" style="margin-top: 10px">
                <div class="col-md-12">
                    <div class="fixHeader_Div">
                        <table class="borderSpace report-table-condensed" id="tbl_report" style="width: 100%">
                            <thead class="report-header">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Address</th>
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
                                    $totalcount = 0;
                                    echo "<tr>";
                                    echo "<td>" . $x . "</td>";
                                    echo "<td>" . $row["fullName"] . "</td>";
                                    echo "<td>" . $row["address"] . "</td>";
                                    echo "<td>" . $row["phonePrimary"] . "</td>";
                                    echo "<td style='text-align: right'>" . number_format($row["da_estimatedRepairingCost"], 2) . "</td>";
                                    echo "<td style='text-align: right'>" . number_format($row["humanInjuryAmount"], 2) . "</td>";
                                    echo "<td style='text-align: right'>" . number_format($row["houseDamageLoss"], 2) . "</td>";
                                    echo "<td style='text-align: right'>" . number_format($row["businessPropertyValue"], 2) . "</td>";
                                    $lineTotal = $row["da_estimatedRepairingCost"] + $row["humanInjuryAmount"] + $row["houseDamageLoss"] + $row["businessPropertyValue"];
                                    $totalcount = $row["humanCount"] + $row["houseitemcount"] + $row["businessdamagecount"] + $row["housedamagecount"];
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
                                    $overalcounttotal += $totalcount;
                                }
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td class="text-right sub_total" colspan="4" style="text-align: center;">Total</td>
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
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>


    <div class="tab-pane" id="detail">
        <div id="tbl_purchase_order_list">
            <?php if (!empty($damageRecord)) { ?>
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
                                    $totalcount = 0;
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
                                    $totalcount = $row["humanCount"] + $row["houseitemcount"] + $row["businessdamagecount"] + $row["housedamagecount"];
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
                                    $overalcounttotal += $totalcount;
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
    </div>


    <div class="tab-pane" id="housedamage">
        <div>
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Filters</legend>

                <form method="post" name="frm_rpt_house_damage" id="frm_rpt_house_damage" class="form-horizontal">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="projectid" id="projectid" value="<?php echo $project_id_for_house ?>">
                    <input type="hidden" name="countryid" id="countryid" value="<?php echo $country_id_for_house ?>">
                    <input type="hidden" name="provinceid[]" id="provinceid"
                           value="<?php echo $province_id_for_house ?>">
                    <input type="hidden" name="districid[]" id="districid" value="<?php echo $distric_id_for_house ?>">
                    <input type="hidden" name="occupation[]" id="occupation"
                           value="<?php echo $occupation_id_for_house ?>">
                    <input type="hidden" name="jamiyadivision[]" id="jamiyadivision"
                           value="<?php echo $jamiyadivision_id_for_house ?>">
                    <input type="hidden" name="division[]" id="division" value="<?php echo $division_id_for_house ?>">
                    <input type="hidden" name="subDivision[]" id="subDivision"
                           value="<?php echo $sub_division_id_for_house ?>">
                    <input type="hidden" name="da_GnDivision[]" id="da_GnDivision"
                           value="<?php echo $da_gn_division_id_for_house ?>">
                    <input type="hidden" name="dateto" id="dateto" value="<?php echo $date_to_for_house ?>">
                    <input type="hidden" name="datefrom" id="datefrom" value="<?php echo $date_for_id_for_house ?>">
                    <div class="col-md-12">
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <label class="col-md-6 control-label text-left"
                                   for="employeeID">Type of Damage</label>
                            <div class="form-group col-md-6">
                                <?php echo form_dropdown('da_typeOfhouseDamage[]', fetch_ngo_damageTypeMaster('1', 'HTD', true), '', 'class="form-control select2 valueHelp disableHelp" id="da_typeOfhouseDamage"  multiple="" '); ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <label class="col-md-5 control-label text-left"
                                   for="employeeID">House Type</label>
                            <div class="form-group col-md-6">
                                <?php echo form_dropdown('da_houseCategory[]', fetch_ngo_buildingtypemaster('1', true), '', 'class="form-control select2" id="da_houseCategory" multiple="" '); ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <label class="col-md-5 control-label text-left"
                                   for="employeeID">Building Damage</label>
                            <div class="form-group col-md-6">
                                <?php echo form_dropdown('da_buildingDamages[]', fetch_ngo_damageTypeMaster('1', 'HBD', true), '', 'class="form-control select2 valueHelp disableHelp" id="da_buildingDamages" multiple="" '); ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-1" style="margin-bottom: 0px;">
                            <button type="button" class="btn btn-primary pull-left" onclick="generateReport_houes()"
                                    name="filtersubmit" id="filtersubmit"><i class="fa fa-plus"></i> Load
                            </button>
                        </div>
                    </div>
                </form>
            </fieldset>
        </div>

        <hr style="margin: 0px;">

        <div id="div_house_damage">

        </div>

    </div>


    <div class="tab-pane" id="building">
        <div>
            <fieldset class="scheduler-border">
                <legend class="scheduler-border">Filters</legend>

                <form method="post" name="frm_rpt_building_damage" id="frm_rpt_building_damage" class="form-horizontal">
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                    <input type="hidden" name="projectidbs" id="projectidbs"
                           value="<?php echo $project_id_for_house ?>">
                    <input type="hidden" name="countryidbs" id="countryidbs"
                           value="<?php echo $country_id_for_house ?>">
                    <input type="hidden" name="provinceidbs[]" id="provinceidbs"
                           value="<?php echo $province_id_for_house ?>">
                    <input type="hidden" name="districidbs[]" id="districidbs"
                           value="<?php echo $distric_id_for_house ?>">
                    <input type="hidden" name="occupationbs[]" id="occupationbs"
                           value="<?php echo $occupation_id_for_house ?>">
                    <input type="hidden" name="jamiyadivisionbs[]" id="jamiyadivisionbs"
                           value="<?php echo $jamiyadivision_id_for_house ?>">
                    <input type="hidden" name="divisionbs[]" id="divisionbs"
                           value="<?php echo $division_id_for_house ?>">
                    <input type="hidden" name="subDivisionbs[]" id="subDivisionbs"
                           value="<?php echo $sub_division_id_for_house ?>">
                    <input type="hidden" name="da_GnDivisionbs[]" id="da_GnDivisionbs"
                           value="<?php echo $da_gn_division_id_for_house ?>">
                    <input type="hidden" name="datetobs" id="datetobs" value="<?php echo $date_to_for_house ?>">
                    <input type="hidden" name="datefrombs" id="datefrombs" value="<?php echo $date_for_id_for_house ?>">
                    <div class="col-md-12">
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <label class="col-md-6 control-label text-left"
                                   for="employeeID">Business Type</label>
                            <div class="form-group col-md-6">
                                <?php echo form_dropdown('buildingTypeIDbs[]', fetch_ngo_buildingtypemaster('2', true), '',
                                    'class="form-control select2" id="da_bp_buildingTypeIDbs" multiple = "" '); ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <label class="col-md-5 control-label text-left"
                                   for="employeeID">Type of Damage</label>
                            <div class="form-group col-md-6">
                                <?php echo form_dropdown('damageTypeIDbs[]', fetch_ngo_damageTypeMaster('4', 'BPD', true), '',
                                    'class="form-control select2 valueHelp disableHelp" id="da_bp_damageTypeIDbs" multiple = "" '); ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-4" style="margin-bottom: 0px">
                            <label class="col-md-5 control-label text-left"
                                   for="employeeID">Property Condition</label>
                            <div class="form-group col-md-6">
                                <?php echo form_dropdown('damageConditionIDbs[]', fetch_ngo_damageTypeMaster('4', 'BPC', true), '',
                                    'class="form-control select2 valueHelp disableHelp" id="da_bp_damageConditionIDbs" multiple = "" '); ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-1" style="margin-bottom: 0px;">
                            <button type="button" class="btn btn-primary pull-left" onclick="generateReport_business()"
                                    name="filtersubmitbs" id="filtersubmitbs"><i class="fa fa-plus"></i> Load
                            </button>
                        </div>
                    </div>
                </form>
            </fieldset>
        </div>

        <hr style="margin: 0px;">
        <div id="div_building_damage">
        </div>
    </div>


    <div class="tab-pane" id="helpaid">
        <div>


            <form method="post" name="frm_rpt_helpaid" id="frm_rpt_helpaid" class="form-horizontal">
                <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                <input type="hidden" name="projectidhelp" id="projectidhelp"
                       value="<?php echo $project_id_for_house ?>">
                <input type="hidden" name="countryidhelp" id="countryidhelp"
                       value="<?php echo $country_id_for_house ?>">
                <input type="hidden" name="provinceidhelp[]" id="provinceidhelp"
                       value="<?php echo $province_id_for_house ?>">
                <input type="hidden" name="districidhelp[]" id="districidhelp"
                       value="<?php echo $distric_id_for_house ?>">
                <input type="hidden" name="occupationhelp[]" id="occupationhelp"
                       value="<?php echo $occupation_id_for_house ?>">
                <input type="hidden" name="jamiyadivisionhelp[]" id="jamiyadivisionhelp"
                       value="<?php echo $jamiyadivision_id_for_house ?>">
                <input type="hidden" name="divisionhelp[]" id="divisionhelp"
                       value="<?php echo $division_id_for_house ?>">
                <input type="hidden" name="subDivisionhelp[]" id="subDivisionhelp"
                       value="<?php echo $sub_division_id_for_house ?>">
                <input type="hidden" name="da_GnDivisionhelp[]" id="da_GnDivisionhelp"
                       value="<?php echo $da_gn_division_id_for_house ?>">
                <input type="hidden" name="datetohelp" id="datetohelp" value="<?php echo $date_to_for_house ?>">
                <input type="hidden" name="datefromhelp" id="datefromhelp" value="<?php echo $date_for_id_for_house ?>">
            </form>
            <hr style="margin: 0px;">

            <div id="div_helpaid">
            </div>
        </div>


    </div>


    <script>

        Inputmask().mask(document.querySelectorAll("input"));

        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/donor_commitment_status', '', 'Donor Commitment Status')
        });
        $(document).ready(function (e) {
            generateReport_helpaid();
            //$('.select2').select2();
        });

        function generateReport_houes() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/damage_assesment_report_house') ?>",
                data: $("#frm_rpt_house_damage").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_house_damage").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateReport_business() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/damage_assesment_report_business') ?>",
                data: $("#frm_rpt_building_damage").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_building_damage").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateReport_helpaid() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/damage_assesment_report_help_aid') ?>",
                data: $("#frm_rpt_helpaid").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_helpaid").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        $('#da_typeOfhouseDamage').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#da_typeOfhouseDamage").multiselect2('selectAll', false);
        $("#da_typeOfhouseDamage").multiselect2('updateButtonText');

        $('#da_houseCategory').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#da_houseCategory").multiselect2('selectAll', false);
        $("#da_houseCategory").multiselect2('updateButtonText');


        $('#da_buildingDamages').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#da_buildingDamages").multiselect2('selectAll', false);
        $("#da_buildingDamages").multiselect2('updateButtonText');


        $('#da_bp_buildingTypeIDbs').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#da_bp_buildingTypeIDbs").multiselect2('selectAll', false);
        $("#da_bp_buildingTypeIDbs").multiselect2('updateButtonText');

        $('#da_bp_damageTypeIDbs').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#da_bp_damageTypeIDbs").multiselect2('selectAll', false);
        $("#da_bp_damageTypeIDbs").multiselect2('updateButtonText');

        $('#da_bp_damageConditionIDbs').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#da_bp_damageConditionIDbs").multiselect2('selectAll', false);
        $("#da_bp_damageConditionIDbs").multiselect2('updateButtonText');


        function tabwisepdf(tabVal) {
            document.getElementById('tabid').value = tabVal;

            if (tabVal == 1) {
                $('.damageassestmentrptcls_cls').removeClass('hide');
            } else if (tabVal == 2) {
                $('.damageassestmentrptcls_cls').removeClass('hide');
            }
            else if (tabVal == 3) {
                $('.damageassestmentrptcls_cls').addClass('hide');
            } else if (tabVal == 4) {
                $('.damageassestmentrptcls_cls').addClass('hide');
            }
            else if (tabVal == 5) {
                $('.damageassestmentrptcls_cls').addClass('hide');
            }
        }

        function generateReportPdf() {
            var tabid = document.getElementById('tabid').value;

            if ((tabid == 1)) {
                var fieldNameChk = [];
                var captionChk = [];
                $("input[name=fieldName]:checked").each(function () {
                    fieldNameChk.push($(this).val());
                    captionChk.push($(this).data('caption'));
                });
                var form = document.getElementById('buyback_productionReport_form');
                //document.getElementById('fieldNameChkpdf').value = fieldNameChk;
                //document.getElementById('captionChkpdf').value = captionChk;
                form.target = '_blank';
                form.action = '<?php echo site_url('OperationNgo/damage_assesment_report_pdf'); ?>';
                form.submit();
            }
            else if (tabid == 2) {
                var fieldNameChk = [];
                var captionChk = [];
                $("input[name=fieldName]:checked").each(function () {
                    fieldNameChk.push($(this).val());
                    captionChk.push($(this).data('caption'));
                });
                var form = document.getElementById('buyback_productionReport_form');
                //document.getElementById('fieldNameChkpdf').value = fieldNameChk;
                //document.getElementById('captionChkpdf').value = captionChk;
                form.target = '_blank';
                form.action = '<?php echo site_url('OperationNgo/damage_assesment_report_pdf'); ?>';
                form.submit();
            }
            else if (tabid == 3) {
                var form = document.getElementById('frm_rpt_house_damage');
                form.target = '_blank';
                form.action = '<?php echo site_url('OperationNgo/damage_assesment_report_house_pdf'); ?>';
                form.submit();
            }
            else if (tabid == 4) {
                var form = document.getElementById('frm_rpt_building_damage');
                form.target = '_blank';
                form.action = '<?php echo site_url('OperationNgo/damage_assesment_report_business_pdf'); ?>';
                form.submit();
            }
            else if (tabid == 5) {
                var form = document.getElementById('frm_rpt_helpaid');
                form.target = '_blank';
                form.action = '<?php echo site_url('OperationNgo/damage_assesment_report_helpaid_pdf'); ?>';
                form.submit();
            }
        }
    </script>