<?php
if (!empty($commReport)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('communityRprt', 'Community Member Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="communityRprt">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Community Member Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th>CODE</th>
                        <th>NAME</th>
                        <th>FAMILY LINK</th>
                        <th>NIC NO</th>
                        <th>GENDER</th>
                        <th>DOB</th>
                        <th>MOBILE</th>
                        <th>AREA</th>
                        <th>GS DIVISION</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($commReport) {

                        $r = 1;
                        $totalComMm = 1;
                        foreach ($commReport as $val) {

                           if ((empty($val['isMove'] || isset($val['isMove'])==NULL ) || (!empty($val['isMove']) && $val['isMove'])==0)) {
                            if($val['LeaderID']){

                             ?>
                                <?php
                                $femCode='<span><a onclick="fetchPage(\'system/communityNgo/ngo_mo_familyMaster_view\','.$val['FamMasterID'].',\'View Family - ' . $val['FamilySystemCode'] . ' | ' . $val['FamilyName'] . '\',\'1\',\'NGO\'); ">'.$val["FamilySystemCode"].'</a>';
                          //   $femCode= $val["FamilySystemCode"];
                            }
                            else{
                             $femCode= '';
                            }
                            ?>
                            <tr>

                                <td><?php echo $r ?></td>
                                <td><?php echo $val["MemberCode"] ?></td>
                                <td><?php echo $val["CName_with_initials"] ?></td>
                                <td><?php echo $femCode ?></td>
                                <td><?php echo $val["CNIC_No"] ?></td>
                                <td><?php echo $val["name"] ?></td>
                                <td><?php echo $val["CDOB"] ?></td>
                                <td><?php echo $val["TP_Mobile"] ?></td>
                                <td><?php echo $val["Region"] ?></td>
                                <td><?php echo $val["divDescription"] ?></td>
                            </tr>
                            <?php
                            $r++;
                            $totQual = $totalComMm++;
                    }

                      }
                    }
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
