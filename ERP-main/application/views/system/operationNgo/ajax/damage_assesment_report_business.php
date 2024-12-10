<?php
if ($business) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <div class="col-md-12">
                <?php if ($type == 'html') {
                    echo export_buttons('tbl_rpt_building_damage', 'Disaster Assesment Report - Building');
                } ?>
            </div>
        </div>
    </div>

<br>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="buildingdamage">
            <div style="">
                <div id="tbl_rpt_building_damage">
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

                            <div class="text-center reportHeader reportHeaderColor"><u> Disaster Assessment Report - Building </u></div>

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

                    <table id="tbl_rpt_building_damage" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Business Activity</th>
                        <th>Business Type</th>
                        <th>Type of Damage</th>
                        <th>Property Condition</th>
                        <th>Estimated Cost for Repair</th>
                        <th>Total Paid Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
                    $totalestimatedcost = 0;
                    $totalpaidamt = 0;
                    if (!empty($business)) {

                        foreach ($business as $row) { ?>
                            <tr>
                                <td><?php echo $x ?></td>
                                <td><?php echo $row['fullName'] ?></td>
                                <td><?php echo $row['address'] ?></td>
                                <td><?php echo $row['businessactivity'] ?></td>
                                <td><?php echo $row['Description'] ?></td>
                                <td><?php echo $row['damagetype'] ?></td>
                                <td><?php echo $row['damagecondition'] ?></td>
                                <td style='text-align: right'><?php echo number_format($row['propertyValue'],2)?></td>
                                <td style='text-align: right'><?php echo number_format($row['paidAmount'] ,2) ?></td>
                            </tr>
                            <?php
                            $x++;
                            $totalestimatedcost +=$row['propertyValue'];
                            $totalpaidamt +=$row['paidAmount'];

                        }


                        ?>
                        <tr>
                            <td class="text-right sub_total" colspan="7" style="text-align: right;">Total</td>
                            <td class="text-right total"  style="text-align: right;"><?php echo number_format($totalestimatedcost,2)?></td>
                            <td class="text-right total"  style="text-align: right;"><?php echo number_format($totalpaidamt,2)?></td>

                        </tr>
                        <?php

                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">No Records found</div>
        </div>
    </div>

    <?php
} ?>
<script>

</script>