<?php
$convertFormat = convert_date_format_sql();
if (!empty($commiteRprt)) {

    $comitAreaId =  $commitAreaId;
    $comitMemId =  $commit_memId;

    $filter_subArea = array("AND (srp_erp_ngo_com_committeeareawise.SubAreaId='" . $commitAreaId . "')" => $commitAreaId,"AND (srp_erp_ngo_com_committeeareawise.CommitteeHeadID='" . $commit_memId . "')" => $commit_memId);
    $set_filter_subArea = array_filter($filter_subArea);
    $where_clasubArea = join(" ", array_keys($set_filter_subArea));
?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('communityRprt', 'Committees Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="communityRprt">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center;">
                <strong>Committees Report</strong></div>
            <?php
            if ($commiteRprt) {
                $r = 1;
                $totalComMm = 1;
                foreach ($commiteRprt as $valComm) {

                    ?>
                    <div style="border: 1px solid #628bbe; border-collapse: collapse;">
                        <div class="row">
                            <div class="col-sm-12">
                                <strong>Committee : </strong>
                              &nbsp;&nbsp;
                                <strong style="color: #a44023;"><?php echo $valComm['CommitteeDes']; ?></strong>

                            </div>
                            <div class="col-sm-12">
                                <strong style="color: #db5026;">Sub Committee/s: </strong>
                            </div>
                        </div>
                        <br>
                        <?php
                        $querySubCm = $this->db->query("SELECT CommitteeAreawiseID,CommitteeAreawiseDes,CName_with_initials,CDOB,CFullName,TP_Mobile,C_Address,DATE_FORMAT(startDate,'{$convertFormat}') AS startDate,DATE_FORMAT(endDate,'{$convertFormat}') AS endDate,CurrentStatus,srp_erp_ngo_com_communitymaster.isActive,srp_erp_statemaster.stateID,srp_erp_statemaster.Description AS comAreaDesc FROM srp_erp_ngo_com_committeeareawise INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_committeeareawise.CommitteeHeadID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID=srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID=srp_erp_ngo_com_committeeareawise.SubAreaId WHERE srp_erp_ngo_com_committeeareawise.companyID='" . $valComm['companyID'] . "' AND srp_erp_ngo_com_committeeareawise.CommitteeID='" . $valComm['CommitteeID'] . "'".$where_clasubArea);
                        $rowsubCm = $querySubCm->result();
                        $subCm = 1;
                        foreach ($rowsubCm as $valSubm) {
                    ?>
                            <div class="row">
                                <div class="col-sm-12" style="color: #628bbe;">
                                    <strong><?php echo $subCm; ?>)</strong>
                                    <strong><?php echo $valSubm->CommitteeAreawiseDes; ?></strong>
                                </div>
                                </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-striped" id="profileInfoTable"
                                           style="background-color: #ffffff;width: 100%">
                                        <tbody>

                                        <tr>
                                            <td>
                                                <strong style="color: #db5026;">Head :</strong>
                                            </td>
                                            <td>
                                                <?php echo $valSubm->CName_with_initials; ?>
                                            </td>
                                            <td>
                                                <strong style="color: #db5026;">Phone (Primary) :</strong>
                                            </td>
                                            <td>
                                                <?php echo $valSubm->TP_Mobile; ?>
                                            </td>
                                            <td>
                                                <strong style="color: #db5026;">Address :</strong>
                                            </td>
                                            <td>
                                                <?php echo $valSubm->C_Address; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong style="color: #db5026;">Area: </strong>
                                            </td>
                                            <td>
                                                <?php echo $valSubm->comAreaDesc; ?>
                                            </td>
                                            <td>
                                                <strong style="color: #db5026;">Added Date :</strong>
                                            </td>
                                            <td>
                                                <?php echo $valSubm->startDate; ?>
                                            </td>
                                            <td>
                                                <strong style="color: #db5026;">Expiry :</strong>
                                            </td>
                                            <td>
                                                <?php echo $valSubm->endDate; ?>
                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <div style="">
                            <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                                <thead class="report-header">
                                <tr>
                                    <th>#</th>
                                    <th>MEMBER/S</th>
                                    <th>POSITION</th>
                                    <th>JOINED DATE</th>
                                    <th>LEFT DATE</th>
                                    <th>STATUS</th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $queryFMem = $this->db->query("SELECT CName_with_initials,CDOB,CFullName,DATE_FORMAT(joinedDate,'{$convertFormat}') AS joinedDate,DATE_FORMAT(expiryDate,'{$convertFormat}') AS expiryDate,CurrentStatus,isMemActive,CommitteePositionDes FROM srp_erp_ngo_com_committeemembers INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_committeemembers.Com_MasterID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID=srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_ngo_com_committeeposition ON srp_erp_ngo_com_committeeposition.CommitteePositionID=srp_erp_ngo_com_committeemembers.CommitteePositionID WHERE srp_erp_ngo_com_committeemembers.companyID='" . $valComm['companyID'] . "' AND srp_erp_ngo_com_committeemembers.CommitteeAreawiseID='" . $valSubm->CommitteeAreawiseID . "'");
                                $rowFMem = $queryFMem->result();
                                $r = 1;
                                $totalComMm = 1;
                                foreach ($rowFMem as $valSm) {

                                    ?>
                                    <tr>

                                        <td><?php echo $r ?></td>
                                        <td><?php echo $valSm->CName_with_initials; ?></td>
                                        <td><?php echo $valSm->CommitteePositionDes; ?></td>
                                        <td><?php echo $valSm->joinedDate; ?></td>
                                        <td><?php if ($valSm->isMemActive == 0) { echo $valSm->expiryDate; }?></td>
                                        <td>   <?php
                                            if ($valSm->isMemActive == 1) {
                                                $description = 'Active';
                                            }
                                            else {
                                                $description = 'inactive';
                                            }
                                            echo $description;
                                            ?></td>
                                    </tr>
                                    <?php
                                    $r++;
                                    $totQual = $totalComMm++;
                                }

                                ?>

                                </tbody>
                            </table>
                        </div>
                        <?php $subCm++; } ?>
                    </div>
                    <br>
                    <?php
                }
                ?>
            <?php
            }
            ?>
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
