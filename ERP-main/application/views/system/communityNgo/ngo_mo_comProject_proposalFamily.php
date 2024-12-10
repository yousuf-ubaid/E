<?php
$page_id = $datas['FamMasterID'];

$com_currency = $this->common_data['company_data']['company_default_currency'];
$rep_currency = $this->common_data['company_data']['company_reporting_currency'];

$queryag1 = $this->db->query("SELECT TotalPerZakat,isActive   FROM srp_erp_ngo_com_projectproposalzakatsetup pagz LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON pagz.AgeGroupID=srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID WHERE companyID='" . $datas['companyID'] . "' AND proposalID='".$datas['proposalID']."' AND EconStateID='" . $datas['EconStateID'] . "' AND pagz.AgeGroupID='1'");
$rowag1 = $queryag1->row();
if(isset($rowag1) && $rowag1->isActive =='1'){ $TotalPerZakat1=$rowag1->TotalPerZakat;}else{$TotalPerZakat1=0;}
$queryag2 = $this->db->query("SELECT TotalPerZakat,isActive   FROM srp_erp_ngo_com_projectproposalzakatsetup pagz LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON pagz.AgeGroupID=srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID WHERE companyID='" . $datas['companyID'] . "' AND proposalID='".$datas['proposalID']."' AND EconStateID='" . $datas['EconStateID'] . "' AND pagz.AgeGroupID='2'");
$rowag2 = $queryag2->row();
if(isset($rowag2) && $rowag2->isActive =='1'){ $TotalPerZakat2=$rowag2->TotalPerZakat;}else{$TotalPerZakat2=0;}
$queryag3 = $this->db->query("SELECT TotalPerZakat,isActive   FROM srp_erp_ngo_com_projectproposalzakatsetup pagz LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON pagz.AgeGroupID=srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID WHERE companyID='" . $datas['companyID'] . "' AND proposalID='".$datas['proposalID']."' AND EconStateID='" . $datas['EconStateID'] . "' AND pagz.AgeGroupID='3'");
$rowag3 = $queryag3->row();
if(isset($rowag3) && $rowag3->isActive =='1'){ $TotalPerZakat3=$rowag3->TotalPerZakat;}else{$TotalPerZakat3=0;}
?>
    <table class="table table-bordered table-condensed" style="background-color: #f0f3f5;">
        <tbody>
        <tr>
            <td style="width: 110px;">Project Category</td>
            <td class="bgWhite" style="width:35%"><?php echo $datas['projectName'] ?></td>
            <td>Family Name</td>
            <td class="bgWhite" colspan="2"><?php echo $datas['FamilySystemCode'].' :'.$datas['FamilyName'] ?></td>
               </tr>
        <tr>

            <td>Document Date</td>
            <td class="bgWhite"><?php echo date('d/m/Y', strtotime($datas['DocumentDate'])) ?></td>
            <td>Economic State</td>
            <td class="bgWhite" colspan="2"><?php echo $datas['EconStateDes'] ?></td>
        </tr>
        <tr>
            <td>currency</td>
            <td class="bgWhite"><?php echo $datas['CurrencyCode'] ?></td>
            <td style="width: 110px;">Estimated Date</td>
            <td colspan="2" class="bgWhite">From: <span
                    class=""><?php echo date('d/m/Y', strtotime($datas['startDate'])) ?></span> To: <span
                    class=""><?php echo date('d/m/Y', strtotime($datas['endDate'])) ?></td>

        </tr>
        </tbody>
    </table>
    <hr>
<?php
if (!empty($famZakat)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Member/s</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Gender</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Date of Birth</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Relationship</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Added Date</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Marital Status</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Amount</td>

            </tr>
            <?php
            $x = 1;
            $totZakat = 0;
            foreach ($famZakat as $val) {
                if ($val['isMove'] == 1) {
                    $moveStatus = '<span style="width:8px;height:8px;font-size: 0.73em;float: right;background-color: #00a5e6; display:inline-block;color: #00a5e6;" title="Moved To Another Family">m</span>&nbsp;';
                } else {
                    $moveStatus = '';
                }
                if ($val['isActive'] == 1) {
                    $activeState = '';
                } else {
                    if ($val['DeactivatedFor'] == 2) {
                        $INactReson = 'Migrate';
                    } else {
                        $INactReson = 'Death';
                    }
                    $activeState = '<span style="width:8px;height:8px;font-size: 0.73em;float: right;background-color:red; display:inline-block;color: red;" title="The Member Is Inactive :' . $INactReson . '">a</span>';
                }

                $memAge = trim($val['Age'] ?? '');

                if ($val['isMove'] == 1) {
                    $zakatAnt =0;
                }
                elseif ($val['isActive'] == 0 || $val['isActive'] == NULL){

                    $zakatAnt =0;
               }
               else{
                   if (0 <= $memAge && $memAge <= 5) {
                       $zakatAnt = $TotalPerZakat1;
                   } elseif (6 <= $memAge && $memAge <= 15) {
                       $zakatAnt = $TotalPerZakat2;
                   } elseif (16 <= $memAge) {
                       $zakatAnt = $TotalPerZakat3;
                   }
               }

                ?>
                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width=""><?php echo $val['CName_with_initials'] ."&nbsp;". $moveStatus ."&nbsp;&nbsp;". $activeState ?></td>
                    <td class="mailbox-star" width=""><?php echo $val['name']; ?></td>
                    <td class="mailbox-star" width=""><?php echo $val['CDOB']; ?></td>
                    <td class="mailbox-star" width=""><?php echo $val['relationship']; ?></td>
                    <td class="mailbox-star" width=""><?php echo $val['FamMemAddedDate']; ?></td>
                    <td class="mailbox-star" width=""><?php echo $val['maritalstatus']; ?></td>
                    <td class="mailbox-star" width=""><span class="pull-right"><?php echo format_number($zakatAnt, $this->common_data['company_data']['company_default_decimal']); ?></span></td>
                </tr>
                <?php

                $totZakat += $zakatAnt;
                $x++;
            }
            ?>
            </tbody>
            <tfoot >
            <tr>
                <td style="text-align: right;" class="" colspan="7">Total Zakat :</td>
                <td style="text-align: right;"> <?php echo format_number($totZakat, $this->common_data['company_data']['company_default_decimal']); ?></td>
            </tr>
            </tfoot>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}
?>

<?php
