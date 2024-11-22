<?php
if (!empty($qualReport)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('communityRprt', 'Community Member Qualification Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="communityRprt">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Community Member Qualification Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th>MEMBER</th>
                        <th>NIC NO</th>
                        <th>GENDER</th>
                        <th>MOBILE</th>
                        <th>QUALIFICATION</th>
                        <th>INSTITUTE</th>
                        <th>CURRENTLY READING</th>
                        <th>YEAR</th>
                        <th>REMARK</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($qualReport) {
                        $r = 1;
                        $totalQual = 1;
                        foreach ($qualReport as $val) {

                            if (isset($val["DegreeDescription"]) && !empty($val["DegreeDescription"])) {
                                $DegreeDes=$val["DegreeDescription"];
                            }
                            else{
                                $DegreeDes='';
                            }
                            if (isset($val["UniversityDescription"]) && !empty($val["UniversityDescription"])) {
                                $UniversityDes =$val["UniversityDescription"];
                            }
                            else{
                                $UniversityDes ='';
                            }
                            if (isset($val["CurrentlyReading"]) && !empty($val["CurrentlyReading"])) {
                                $CurrentlyReading =$val["CurrentlyReading"];
                            }
                            else{
                                $CurrentlyReading ='';
                            }
                            if (isset($val["Year"]) && !empty($val["Year"])) {
                                $Year =$val["Year"];
                            }
                            else{
                                $Year ='';
                            }
                            if (isset($val["Remarks"]) && !empty($val["Remarks"])) {
                                $Remarks=$val["Remarks"];
                            }
                            else{
                                $Remarks='';
                            }
                        

                            ?>
                            <tr>

                                <td><?php echo $r ?></td>
                                <td width="180px"><?php echo $val["CName_with_initials"] ?></td>
                                <td><?php echo $val["CNIC_No"] ?></td>
                                <td><?php echo $val["name"] ?></td>
                                <td><?php echo $val["PrimaryNumber"] ?></td>
                                <td><?php echo $DegreeDes ?></td>
                                <td><?php echo $UniversityDes ?></td>
                                <td>
                                    <?php if($CurrentlyReading == 1){
                                        ?>
                                        Yes
                                        <?php
                                    }elseif($CurrentlyReading == 0){
                                        ?>
                                        No
                                        <?php
                                    } else{

                                    }
                                     ?>
                                </td>
                                <td><?php echo $Year ?></td>
                                <td><?php echo $Remarks ?></td>
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
