<?php

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
                                   <strong style="font-weight: bold;">  Tel : <?php echo $this->common_data['company_data']['company_phone'] ?></strong><br>
                                     <br>
                                     Disaster Assessment Report - House
                         </td>
                        </tr>
                    </table>
                </td>
                </tr>

        </tbody>
    </table>
</div>



 <!--<div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php /*echo $this->common_data['company_data']['company_name'] */?> </strong>
            </div>
            <div class="text-center reportHeaderColor">
                <strong><?php /*echo $this->common_data['company_data']['companyPrintAddress'] */?> </strong>
            </div>
            <div class="text-center reportHeaderColor">
                <strong>Tel : <?php /*echo $this->common_data['company_data']['companyPrintTelephone'] */?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Disaster Assessment Report - House</div>
        </div>
    </div>-->

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

    <br>
    <?php if (!empty($house)) { ?>
        <div class="row" style="margin-top: 5px">
            <div class="col-md-12 " id="housedamage">
                <div style="">
                    <table id="tbl_rpt_house_damage" class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Type Of Damage</th>
                            <th>House Type</th>
                            <th>Building Damage</th>
                            <th>Estimated Cost for Repair</th>
                            <th>Total Paid Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x = 1;
                        $totalestimatedcost = 0;
                        $totalpaidamt = 0;
                        if (!empty($house)) {
                         foreach ($house as $row) { ?>
                                <tr>
                                    <td><?php echo $x ?></td>
                                    <td><?php echo $row['fullName'] ?></td>
                                    <td><?php echo $row['address'] ?></td>
                                    <td><?php echo $row['TypeOfHouseDamage'] ?></td>
                                    <td><?php echo $row['buildingtype'] ?></td>
                                    <td><?php echo $row['damagetype'] ?></td>
                                    <td style='text-align: right'><?php echo number_format($row['reparingcost'],2)?></td>
                                    <td style='text-align: right'><?php echo number_format($row['paidamt'] ,2) ?></td>
                                </tr>
                                <?php
                                $x++;
                                $totalestimatedcost +=$row['reparingcost'];
                                $totalpaidamt +=$row['paidamt'];

                                }
                            } ?>
                            <tr>
                                <td class="text-right sub_total" colspan="6" style="text-align: right;">Total</td>
                                <td class="text-right total"  style="text-align: right;"><?php echo number_format($totalestimatedcost,2)?></td>
                                <td class="text-right total"  style="text-align: right;"><?php echo number_format($totalpaidamt,2)?></td>

                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php } else { ?>
        <br>
        <div class="row">
            <div class="col-md-12 xxcol-md-offset-2">
                <div class="alert alert-warning" role="alert">No Records found</div>
            </div>
        </div>

        <?php
    } ?>