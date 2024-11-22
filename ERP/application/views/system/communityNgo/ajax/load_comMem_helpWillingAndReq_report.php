<?php
if (!empty($bothHelpMem)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('commHelpBoth', 'Community Member Help Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="commHelpBoth">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Community Member Help Report</strong></div>
            <div style="">
                <?php   foreach ($bothHelpMem as $valMem) { ?>
                <div style="border: 1px solid #628bbe; border-collapse: collapse;">

                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-striped" id="profileInfoTable"
                               style="background-color: #ffffff;width: 100%">
                            <tbody>

                            <tr>
                                <td>
                                    <strong style="color: #db5026;">Member :</strong>
                                </td>
                                <td>
                                    <?php echo $valMem['CName_with_initials']; ?>
                                </td>
                                <td>
                                    <strong style="color: #db5026;">Phone :</strong>
                                </td>
                                <td>
                                    <?php echo $valMem["PrimaryNumber"]; ?>
                                </td>
                                <td>
                                    <strong style="color: #db5026;">Address :</strong>
                                </td>
                                <td>
                                    <?php echo $valMem["C_Address"]; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong style="color: #db5026;">Gender: </strong>
                                </td>
                                <td>
                                    <?php echo $valMem["name"]; ?>
                                </td>
                                <td>
                                    <strong style="color: #db5026;">NIC NO :</strong>
                                </td>
                                <td>
                                    <?php echo $valMem["CNIC_No"]; ?>
                                </td>
                                <td>
                                    <strong style="color: #db5026;">Email :</strong>
                                </td>
                                <td>
                                    <?php echo $valMem["EmailID"]; ?>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="col-sm-12">
                        <strong style="color: #9f3d1c;">WILLING TO HELP: </strong>
                    </div>
                    <div class="col-sm-12">
                <table id="" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th colspan="3">DESCRIPTION</th>
                        <th colspan="2">WILLING IN DETAIL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $bothWillingDel = $this->db->query("SELECT srp_erp_ngo_com_memberwillingtohelp.Com_MasterID,srp_erp_ngo_com_helpcategories.helpCategoryDes,srp_erp_ngo_com_memberwillingtohelp.companyID,srp_erp_ngo_com_memberwillingtohelp.createdUserID,srp_erp_ngo_com_helpcategories.helpCategoryDes,srp_erp_ngo_com_memberwillingtohelp.helpComments FROM srp_erp_ngo_com_memberwillingtohelp INNER JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memberwillingtohelp.Com_MasterID INNER JOIN srp_erp_ngo_com_helpcategories ON srp_erp_ngo_com_helpcategories.helpCategoryID=srp_erp_ngo_com_memberwillingtohelp.helpCategoryID WHERE srp_erp_ngo_com_memberwillingtohelp.Com_MasterID ={$valMem['Com_MasterID']} ")->result_array();

                    if ($bothWillingDel) {
                        $r = 1;
                        foreach ($bothWillingDel as $val) {

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
                                <td colspan="3"><?php echo $helpCategoryDes ?></td>
                                <td colspan="2"><?php echo $helpComments ?></td>

                            </tr>
                            <?php
                            $r++;
                        }

                        ?>

                    <?php }
                    ?>
                    </tbody>
                </table>
                        </div>

                    <br>
                    <div class="col-sm-12">
                        <strong style="color: #9f3d1c;text-transform: uppercase;">Help Requirement: </strong>
                    </div>
                    <div class="col-sm-12">
                    <table id="" class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th>#</th>
                            <th colspan="3">DESCRIPTION</th>
                            <th colspan="2">NEED IN DETAIL</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $bothRequrmnetDel = $this->db->query("SELECT srp_erp_ngo_com_helprequirements.helpRequireType,srp_erp_ngo_com_memberhelprequirements.companyID,srp_erp_ngo_com_memberhelprequirements.createdUserID,srp_erp_ngo_com_memberhelprequirements.helpRequireID,srp_erp_ngo_com_memberhelprequirements.hlprDescription,srp_erp_ngo_com_helprequirements.helpRequireDesc FROM srp_erp_ngo_com_memberhelprequirements INNER JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memberhelprequirements.Com_MasterID INNER JOIN srp_erp_ngo_com_helprequirements ON srp_erp_ngo_com_helprequirements.helpRequireID=srp_erp_ngo_com_memberhelprequirements.helpRequireID WHERE srp_erp_ngo_com_memberhelprequirements.Com_MasterID ={$valMem['Com_MasterID']} ")->result_array();

                        if ($bothRequrmnetDel) {
                            $r = 1;
                            foreach ($bothRequrmnetDel as $val) {

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
                                    <td colspan="3"><?php echo $helpRqTypes ?></td>
                                    <td colspan="2"><?php echo $helpRequireDes ?></td>

                                </tr>
                                <?php
                                $r++;
                            }

                            ?>

                        <?php }
                        ?>
                        </tbody>
                    </table>
                            </div>
                </div>
                </div>
                    <br>
                <?php } ?>

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


<?php
