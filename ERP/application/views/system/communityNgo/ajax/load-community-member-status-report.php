<?php
if (!empty($memReport)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('communityRprt', 'Community Member Occupation Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="communityRprt">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Community Member Occupation Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th>MEMBER</th>
                        <th>NIC NO</th>
                        <th>GENDER</th>
                        <th>MOBILE</th>
                        <th>OCCUPATION TYPE</th>
                        <th>IsPrimary</th>
                        <th>GRADE</th>
                        <th>JOB</th>
                        <th>PLACE</th>
                        <th>ADDRESS</th>
                        <th>DATE FROM</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($memReport) {
                        $r = 1;
                        $totalMrm = 1;
                        foreach ($memReport as $val) {

                            if (isset($val["OcDescription"]) && !empty($val["OcDescription"])) {
                              $OccTypeID=$val["OcDescription"];
                            }
                            else{
                                $OccTypeID='';
                            }
                            if (isset($val["gradeComDes"]) && !empty($val["gradeComDes"])) {
                                $gradeComDes =$val["gradeComDes"];
                            }
                            else{
                                $gradeComDes ='';
                            }
                            if (isset($val["JobCatDescription"]) && !empty($val["JobCatDescription"])) {
                                $JobCatDescription =$val["JobCatDescription"];
                            }
                            else{
                                $JobCatDescription ='';
                            }

                            if (isset($val["OccTypeID"]) && !empty($val["OccTypeID"]) && ($val["OccTypeID"] =='1')) {
                                $WorkingPlace = $val["schoolComDes"];
                            }
                            else{
                                if (isset($val["WorkingPlace"]) && !empty($val["WorkingPlace"])) {
                                    $WorkingPlace = $val["WorkingPlace"];
                                } else {
                                    $WorkingPlace = '';
                                }
                            }
                            if (isset($val["Address"]) && !empty($val["Address"])) {
                                $Address=$val["Address"];
                            }
                            else{
                                $Address='';
                            }
                            if (isset($val["DateFrom"]) && !empty($val["DateFrom"])) {

                                $DateFrom = $val["DateFrom"];
                            }
                            else{
                                $DateFrom = '';
                            }
                            if ((isset($val["isPrimary"]) && !empty($val["isPrimary"])) && $val["isPrimary"]==1) {

                              $isPrimary = 'Yes';
                            }
                            else{
                                $isPrimary = '';
                            }

                                ?>
                                <tr>

                                    <td><?php echo $r ?></td>
                                    <td width="180px"><?php echo $val["CName_with_initials"] ?></td>
                                    <td><?php echo $val["CNIC_No"] ?></td>
                                    <td><?php echo $val["name"] ?></td>
                                    <td><?php echo $val["PrimaryNumber"] ?></td>
                                    <td><?php echo $OccTypeID ?></td>
                                    <td><?php echo $isPrimary ?></td>
                                    <td><?php echo $gradeComDes ?></td>
                                    <td><?php echo $JobCatDescription ?></td>
                                    <td><?php echo $WorkingPlace ?></td>
                                    <td><?php echo $Address ?></td>
                                    <td><?php echo $DateFrom ?></td>
                                </tr>
                                <?php
                            $r++;
                            $totMr = $totalMrm++;
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