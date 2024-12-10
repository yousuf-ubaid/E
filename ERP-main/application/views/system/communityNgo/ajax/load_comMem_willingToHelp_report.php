<?php
if (!empty($willingHelpRprt)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('communityWilling', 'Community Member Willing To Help Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="communityWilling">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Community Member Willing To Help Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th>MEMBER</th>
                        <th>NIC NO</th>
                        <th>GENDER</th>
                        <th>MOBILE</th>
                        <th>WILLING TO HELP</th>
                        <th>HELP IN DETAIL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($willingHelpRprt) {
                        $r = 1;
                        $totalQual = 1;
                        foreach ($willingHelpRprt as $val) {

                            if (isset($val["helpCategoryDes"]) && !empty($val["helpCategoryDes"])) {
                                $helpCategoryDes =$val["helpCategoryDes"];
                            }
                            else{
                                $helpCategoryDes ='';
                            }

                            if (isset($val["helpComments"]) && !empty($val["helpComments"])) {
                                $helpComments =$val["helpComments"];
                            }
                            else{
                                $helpComments ='';
                            }

                            ?>
                            <tr>

                                <td><?php echo $r ?></td>
                                <td width="180px"><?php echo $val["CName_with_initials"] ?></td>
                                <td><?php echo $val["CNIC_No"] ?></td>
                                <td><?php echo $val["name"] ?></td>
                                <td><?php echo $val["PrimaryNumber"] ?></td>
                                <td><?php echo $helpCategoryDes ?></td>
                                <td><?php echo $helpComments ?></td>

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
