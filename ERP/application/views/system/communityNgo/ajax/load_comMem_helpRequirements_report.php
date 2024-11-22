<?php
if (!empty($helpReqReport)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('communityRprt', 'Community Member Help Requirements Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="communityRprt">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Community Member Requirements Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th>MEMBER</th>
                        <th>NIC NO</th>
                        <th>GENDER</th>
                        <th>MOBILE</th>
                        <th>TYPE</th>
                        <th>HELP IN DETAIL</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($helpReqReport) {
                        $r = 1;
                        $totalQual = 1;
                        foreach ($helpReqReport as $val) {

                            if($val["helpRequireType"] == 'GOV'){
                                $helpRqTypes= 'Government Help';
                            }
                            elseif($val["helpRequireType"] == 'PVT'){
                                $helpRqTypes= 'Private Help';
                            }
                            elseif($val["helpRequireType"] == 'CONS'){
                                $helpRqTypes= 'Consultancy';
                            }
                            else{
                                $helpRqTypes ='';
                            }

                            if (isset($val["helpRequireDesc"]) && !empty($val["helpRequireDesc"])) {
                                $helpRequireDes =$val["helpRequireDesc"];
                            }
                            else{
                                $helpRequireDes ='';
                            }

                            ?>
                            <tr>

                                <td><?php echo $r ?></td>
                                <td width="180px"><?php echo $val["CName_with_initials"] ?></td>
                                <td><?php echo $val["CNIC_No"] ?></td>
                                <td><?php echo $val["name"] ?></td>
                                <td><?php echo $val["PrimaryNumber"] ?></td>
                                <td><?php echo $helpRqTypes ?></td>
                                <td><?php echo $helpRequireDes ?></td>

                            </tr>
                            <?php
                            $r++;
                            $totQual = $totalQual++;
                        }

                        ?>

                    <?php }
                    ?>
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
            <div class="alert alert-warning" role="alert">No Records Found</div>
        </div>
    </div>

    <?php
} ?>
    <script>
        $('#tbl_rpt_salesorder').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 10
        });

    </script>


<?php
