<?php
if ($helpaid) { ?>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12">
        <div class="col-md-12">
            <?php if ($type == 'html') {
                echo export_buttons('tbl_rpt_helpaid_damage', 'Disaster Assesment Report - Help Aid');
            } ?>
        </div>
    </div>
</div>

<br>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12 " id="helpaid">
        <div class="col-md-12 " id="buildingdamage">
            <div style="">
                <div id="tbl_rpt_helpaid_damage">
                    <div class="row hide">
                        <div class="col-md-12">
                            <div class="text-center reportHeaderColor">
                                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
                            </div>
                            <div class="text-center reportHeaderColor">
                                <strong><?php echo $this->common_data['company_data']['company_address1'] . ' ' . $this->common_data['company_data']['company_address2'] . ' ' . $this->common_data['company_data']['company_city'] . ' ' . $this->common_data['company_data']['company_country']; ?> </strong>
                            </div>
                            <div class="text-center reportHeaderColor">
                                <strong>Tel
                                    : <?php echo $this->common_data['company_data']['company_phone'] ?> </strong>
                            </div>

                            <div class="text-center reportHeader reportHeaderColor"><u>Disaster Assessment Report - Help
                                    Aid</u></div>

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
                    <div style="">
                        <table id="tbl_rpt_helpaid_damage" class="borderSpace report-table-condensed"
                               style="width: 100%">
                            <thead class="report-header">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Assistance Name</th>
                                <th>Organization</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $x = 1;
                            $totalpaidamt = 0;
                            if (!empty($helpaid)) {

                                foreach ($helpaid as $row) { ?>
                                    <tr>
                                        <td><?php echo $x ?></td>
                                        <td><?php echo $row['fullName'] ?></td>
                                        <td><?php echo $row['address'] ?></td>
                                        <td><?php echo $row['assitanceName'] ?></td>
                                        <td><?php echo $row['Organization'] ?></td>
                                        <td style='text-align: right'><?php echo number_format($row['amount'], 2) ?></td>
                                    </tr>
                                    <?php
                                    $x++;
                                    $totalpaidamt += $row['amount'];

                                }
                                ?>
                                <tr>
                                    <td class="text-right sub_total" colspan="5" style="text-align: right;">Total</td>
                                    <td class="text-right total"
                                        style="text-align: right;"><?php echo number_format($totalpaidamt, 2) ?></td>

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