<?php
$convertFormat = convert_date_format_sql();
if (!empty($familyReport)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('communityRprt', 'Community Family Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="communityRprt">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center;">
                <strong>Community Family Report</strong></div>
            <?php
            if ($familyReport) {
            $r = 1;
            $totalComMm = 1;
            foreach ($familyReport as $valMas) {

            ?>
                <div style="border: 1px solid #628bbe; border-collapse: collapse;">
            <div class="row">
                <div class="col-sm-9">
                    <table class="table table-striped" id="profileInfoTable"
                           style="background-color: #ffffff;width: 100%">
                        <tbody>

                        <tr>
                            <td>
                                <strong style="color: #638bbe;">Head Of The Family: </strong>
                            </td>
                            <td>
                                <?php echo $valMas['CName_with_initials']; ?>
                            </td>
                            <td>
                                <strong style="color: #638bbe;">Phone (Primary) :</strong>
                            </td>
                            <td>
                                <?php echo $valMas['TP_Mobile']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong style="color: #638bbe;">Contact Address :</strong>
                            </td>
                            <td>
                               House No  <?php echo $valMas['HouseNo'].','. $valMas['C_Address'] ?>
                            </td>
                            <td><strong style="color: #638bbe;">Email :</strong></td>
                            <td>
                                <?php echo $valMas['EmailID'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong style="color: #638bbe;">Area :</strong>
                            </td>
                            <td>
                                <?php echo $valMas['Region'] ?>
                            </td>
                            <td>
                                <strong style="color: #638bbe;">GS Division :</strong>
                            </td>
                            <td>
                                <?php echo $valMas['diviDescription']. ' - ' .$valMas['GS_No'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong style="color: #638bbe;">Reference No :</strong>
                            </td>
                            <td>
                                <?php echo $valMas['LedgerNo'] ?>
                            </td>
                            <td>
                                <strong style="color: #638bbe;">Ledger No :</strong>
                            </td>
                            <td>
                                <?php echo $valMas['FamilySystemCode'] ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-3">
                    <div class="fileinput-new thumbnail">
                        <?php if ($valMas['CImage'] != '') { ?>
                            <img src="<?php echo base_url('uploads/NGO/communitymemberImage/' . $valMas['CImage']); ?>"
                                 id="changeImg" style="width: 125px; height: 95px;">
                            <?php
                        } else { ?>
                            <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg"
                                 style="width: 120px; height: 90px;">
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th>MEMBER/S</th>
                        <th>GENDER</th>
                        <th>DATE OF BIRTH</th>
                        <th>RELATIONSHIP</th>
                        <th>MARITAL STATUS</th>
                        <th>ADDED DATE</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $queryFMem = $this->db->query("SELECT srp_erp_ngo_com_familydetails.Com_MasterID,srp_erp_ngo_com_familydetails.FamMasterID,CName_with_initials,CDOB,CFullName,DATE_FORMAT(FamMemAddedDate,'{$convertFormat}') AS FamMemAddedDate,name,relationship,CurrentStatus,isMove,srp_erp_ngo_com_communitymaster.isActive,DeactivatedFor,srp_erp_ngo_com_maritalstatus.maritalstatusID,srp_erp_ngo_com_maritalstatus.maritalstatus FROM srp_erp_ngo_com_familydetails INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familydetails.Com_MasterID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID=srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus LEFT JOIN srp_erp_family_relationship ON srp_erp_family_relationship.relationshipID=srp_erp_ngo_com_familydetails.relationshipID WHERE srp_erp_ngo_com_familydetails.companyID='" . $valMas['companyID'] . "' AND FamMasterID='" . $valMas['FamMasterID'] . "'");
                    $rowFMem = $queryFMem->result();

                    $r = 1;
                        $totalComMm = 1;
                        foreach ($rowFMem as $valSm) {
                            if($valSm->isMove ==1 ){
                               $moveStatus= '<span onclick="get_memMoved_history('.$valSm->Com_MasterID.','.$valSm->FamMasterID.', \'' . $valSm->CName_with_initials. '\');" style="width:10px;height:10px;font-size: 0.73em;float: right;background-color: #00a5e6; display:inline-block;color: #00a5e6;" title="Moved To Another Family">m</span>';

                               }
                                else{ $moveStatus=''; }

                            if($valSm->isActive == 1){ $activeState=''; } else{
                                if($valSm->DeactivatedFor == 2){ $INactReson='Migrate';} else{$INactReson='Death';}
                                $activeState='<span style="width:10px;height:10px;font-size: 0.73em;float: right;background-color:red; display:inline-block;color: red;" title="The Member Is Inactive :'.$INactReson.'">a</span>';}

                            ?>
                            <tr>

                                <td><?php echo $r ?></td>
                                <td><?php echo $valSm->CName_with_initials ."&nbsp;". $moveStatus ."&nbsp;&nbsp;&nbsp;". $activeState ?></td>
                                <td><?php echo $valSm->name; ?></td>
                                <td><?php echo $valSm->CDOB; ?></td>
                                <td><?php echo $valSm->relationship; ?></td>
                                <td><?php echo $valSm->maritalstatus; ?></td>
                                <td><?php echo $valSm->FamMemAddedDate; ?></td>
                            </tr>
                            <?php
                            $r++;
                            $totQual = $totalComMm++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
                </div>
               <br>
                <?php
            }
                ?>
            <?php }
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
}
?>
    <div class="modal fade" id="mem_movedHistory_modal" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" style="width:400px;">
            <div class="modal-content" style="border-radius:12px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="cmntModTitle">Member - <label style="font-size: 15px;font-weight: normal;" id="memDetail"></label></h4>
                </div>
                    <div class="row modal-body">
                        <label> &nbsp;&nbsp;&nbsp; <label class="glyphicon glyphicon-link"></label> Family Links</label>
                        <div class="col-md-12" id="mem_movedId">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>

    <script>
        $('#tbl_rpt_salesorder').tableHeadFixer({
            head: true,
            foot: true,
            left: 0,
            right: 0,
            'z-index': 10
        });

        function get_memMoved_history(Com_MasterID,FamMasterID,CName_with_initials) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'Com_MasterID':Com_MasterID,'FamMasterID':FamMasterID},
                url: "<?php echo site_url('CommunityNgo/load_memberMovedHis'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#mem_movedHistory_modal').modal({backdrop: "static"});
                    $('#memDetail').html(CName_with_initials);
                    $('#mem_movedId').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

    </script>


<?php
