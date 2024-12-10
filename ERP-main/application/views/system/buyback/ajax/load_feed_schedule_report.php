<?php
if ($type == 'html') { ?>
    <style>
        .headrowtitle
        {
            font-size: 11px !important;
            line-height: 20px !important;
            height: 20px !important;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 25px;
            font-weight: bold;
            text-align: left;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
            background-color: white;
            border-top: 1px solid #ffffff;
        }
        .tableFeed{
            font-size: 12px !important;
        }
        .mailbox-star{
            padding: 5px;
        }
    </style>
<?php } ?>

<?php
$companyID = $this->common_data['company_data']['company_id'];
$convertFormat = convert_date_format_sql();

$feedTypes = $this->db->query("SELECT feedScheduleID,feedAmount,ft.description as feedName,CONCAT(startDay, ' - ', endDay) as changedDate,buybackFeedtypeID FROM srp_erp_buyback_feedschedulemaster fsm LEFT JOIN srp_erp_buyback_feedtypes ft ON fsm.feedTypeID = ft.buybackFeedtypeID WHERE fsm.companyID = {$companyID} ORDER BY feedScheduleID ASC")->result_array();

$feedTypesArr = array_column($feedTypes, 'feedScheduleID');
$feedSum = [];
$colspan = '';
if (!isset($columnDrop)) {
    $CHICKS = true;
    $AGE = true;
    $FEEDUPTO = true;
    $FEEDNEXT = true;
    $FEEDVALUE = true;
    $colspan = 2;
} else{
    $CHICKS = false;
    $AGE = false;
    $FEEDUPTO = false;
    $FEEDNEXT = false;
    $FEEDVALUE = false;
}

if (isset($columnDrop)) {
    if (in_array("CHICKS", $columnDrop)) {
        $CHICKS = true;
    }

    if (in_array("AGE", $columnDrop)) {
        $AGE = true;
    }

    if (in_array("FEED UP TO", $columnDrop)) {
        $FEEDUPTO = true;
    }

    if (in_array("NEXT FEED", $columnDrop)) {
        $FEEDNEXT = true;
    }

    if (in_array("FEED VALUE", $columnDrop)) {
        $FEEDVALUE = true;
    }
    $colspan = 2;
}

if (!empty($batch)) { ?>
    <div class="row" style="margin-top: 5px; margin-bottom: 5px;">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('feedScheduleReprt', 'Feed Schedule', True, True);
            } ?>
        </div>
    </div>

    <div class="row" style="margin-top: 5px">
        <div class="table-responsive mailbox-messages">
            <div id="feedScheduleReprt">
                <table class="table-hover table-striped tableFeed">
                    <thead style="border: 1px solid #da9393;">
                    <tr>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">#</th>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">Farmer</th>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">Batch Code</th>
                        <?php if($CHICKS) {
                            $colspan += 5; ?>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important; text-align: center;" colspan="5">Live Stock</th>
                        <?php } if($AGE){
                            $colspan +=1; ?>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">Age</th>
                        <?php } if($FEEDUPTO){
                            $colspan +=1; ?>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">Feed up To</th>
                        <?php } if($FEEDNEXT){
                            $colspan +=1; ?>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">Next Feed</th>
                        <?php } if($FEEDVALUE) {

                            if (!empty($feedTypes)) {
                                $colspan +=1;
                                foreach ($feedTypes as $feed) {
                                    ?>
                                    <th class="headrowtitle"
                                        style="text-align: center; border: solid 1px #e6e6e6 !important;"
                                        colspan="3"><?php echo $feed['feedName']; ?></th>
                                    <?php
                                }
                            }
                        }
                        ?>
                        <th class="headrowtitle" style="text-align: center; border: solid 1px #e6e6e6 !important;" colspan="2" rowspan="2">#</th>
                    </tr>
                    <tr>
                        <?php if($CHICKS) { ?>
                        <th class="headrowtitle" style="background-color: #a3ffa9; border: solid 1px #e6e6e6 !important; text-align: center;" title="Input">I</th>
                        <th class="headrowtitle" title="Return"  style="text-align: center; background-color: #ffafaf; border: solid 1px #e6e6e6 !important;">R</th>
                        <th class="headrowtitle" title="Grn" style="text-align: center; background-color: #c1ffc5; border: solid 1px #e6e6e6 !important;">G</th>
                        <th class="headrowtitle" title="Mortality" style="text-align: center; background-color: #ffc9c9; border: solid 1px #e6e6e6 !important;">M</th>
                        <th class="headrowtitle" title="Balance" style="text-align: center; background-color:#ddffdf; border: solid 1px #e6e6e6 !important;">B</th>

                        <?php } if($FEEDVALUE) {
                            if (!empty($feedTypes)) {
                                foreach ($feedTypes as $feed) { ?>
                                    <th class="headrowtitle"
                                        style="text-align: center; border: solid 1px #e6e6e6 !important;"
                                        title="Required">R
                                    </th>
                                    <th class="headrowtitle"
                                        style="text-align: center; border: solid 1px #e6e6e6 !important;"
                                        title="Delivered">D
                                    </th>
                                    <th class="headrowtitle"
                                        style="text-align: center; border: solid 1px #e6e6e6 !important;"
                                        title="Balance">B
                                    </th>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </tr>
                    </thead>
                    <?php
                    if ($type == 'pdf') { ?>
                        <hr>
                    <?php } ?>
                    <tbody>
                    <?php
                    $x = 1;
                    $chicksTotal = 0;
                    $booster = 0;
                    $grntot = 0;
                    $mortalitychicks = 0;
                    $feedBooster = 0;
                    $balanceFeedType = 0;
                    $format_nextInputDay = '';
                    $format_daysBeforeDay = '';
                    $sbR = 0;
                    $sbD = 0;
                    $sbB = 0;
                    $frR = 0;
                    $frD = 0;
                    $frB = 0;
                    $chicksreturn = 0;
                    $chicksbalance = 0;
                    $feedSum = [];
                    foreach ($batch as $val) {
                        $ageFilter = chicks_age_dashboard($val['batchMasterID'], '','');
                        if(empty($ageFrom) || $ageFrom < $ageFilter){
                            if(empty($ageTo) || $ageTo > $ageFilter){ ?>
                                <tr class="tableFeed">

                                    <td class="mailbox-star tableFeed" style="border: solid 1px #e6e6e6 !important;" width="5%"><?php echo $x; ?></td>

                                    <td class="mailbox-star tableFeed" style="border: solid 1px #e6e6e6 !important;" width="10%"><?php echo $val['farmerName']; ?></td>

                                    <td class="mailbox-star tableFeed" style="border: solid 1px #e6e6e6 !important;" width="10%">
                                        <a href="#"
                                           onclick="generateProductionReport(<?php echo $val['batchMasterID']; ?>)"><?php echo $val['batchCode'] ?></a>
                                    </td>

                                    <?php
                                    $chicksTotal = $this->db->query("SELECT COALESCE( sum( dpd.qty ), 0 ) AS chicksTotal FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID
                                                                            WHERE dpm.batchMasterID = {$val['batchMasterID']} AND dpd.buybackItemType = 1 AND dpm.approvedYN = 1 AND dpm.confirmedYN = 1")->row_array();

                                    if($CHICKS) {
                                        if($policy_transaction == 1 && $val['isclosed'] == 0){
                                            echo '<td class="mailbox-star ChicksTotalView"
                                style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #a8ffad;"
                                width="5%" onclick="new_dispatch_feedSchecule(' . $chicksTotal['chicksTotal'] . ',' . $val['batchMasterID'] . ')">';
                                        } else {
                                            echo '<td class="mailbox-star" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #a8ffad;" width="5%">';
                                        }

                                        if (!empty($chicksTotal)) {
                                            echo $chicksTotal['chicksTotal'];
                                        }
                                        echo '</td>';
                                    }

                                    if (!empty($chicksTotal)) {
                                        $chicksTotal = $chicksTotal['chicksTotal'];
                                    }

                                    $retutnqty = $this->db->query("SELECT COALESCE( sum( dpdr.qty ), 0 ) AS returnqty, dispatchAutoID, dpdr.returnAutoID FROM srp_erp_buyback_dispatchreturndetails dpdr 
	LEFT JOIN srp_erp_buyback_dispatchreturn retun on retun.returnAutoID = dpdr.returnAutoID 
	WHERE approvedYN  = 1 AND confirmedYN = 1 AND dpdr.buybackItemType = 1 AND retun.batchMasterID = {$val['batchMasterID']} GROUP BY dispatchAutoID")->row_array();

                                    if($CHICKS) {
                                        echo ' <td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #ffafaf;">';
                                        if (!empty($retutnqty)) {
                                            echo $retutnqty['returnqty'];
                                        }else
                                        {
                                            echo '0';
                                        }
                                        echo '</td>';
                                    }

                                    if (!empty($retutnqty)) {
                                        $chicksreturn = $retutnqty['returnqty'];
                                    }

                                    $grn = $this->db->query("SELECT COALESCE ( sum( grndetail.noOfBirds ), 0 ) AS grn FROM srp_erp_buyback_grn grn LEFT JOIN srp_erp_buyback_grndetails grndetail on grndetail.grnAutoID = grn.grnAutoID
WHERE approvedYN  = 1 AND confirmedYN = 1 AND grn.batchMasterID = {$val['batchMasterID']}")->row_array();

                                    $mortality = $this->db->query("SELECT COALESCE(sum(noOfBirds), 0) AS deadChicksTotal FROM srp_erp_buyback_mortalitymaster mm INNER JOIN srp_erp_buyback_mortalitydetails md ON mm.mortalityAutoID = md.mortalityAutoID WHERE batchMasterID ={$val['batchMasterID']} AND confirmedYN = 1")->row_array();

                                    if($CHICKS) {
                                         if($policy_transaction == 1 && $val['isclosed'] == 0) {
                                             echo ' <td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #c1ffc5;" onclick="grn_new_feedSchedule(' . $val['batchMasterID'] . ')">';
                                         } else{
                                             echo ' <td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #c1ffc5;">';
                                         }
                                        if (!empty($grn)) {
                                            echo $grn['grn'];
                                        }else
                                        {
                                            echo '0';
                                        }
                                        echo ' </td>';
                                    }

                                    if (!empty($grn)) {
                                        $grntot =  $grn['grn'];
                                        $chicksbalance = ($chicksTotal) - ($retutnqty['returnqty'] + $grn['grn']) ;
                                    }else
                                    {
                                        $chicksbalance = $chicksTotal - $grn['grn'];
                                    }

                                    if (!empty($mortality)) {
                                        $mortalitychicks =  $mortality['deadChicksTotal'];
                                        $chicksbalance = ($chicksTotal) - ($retutnqty['returnqty'] + $grn['grn'] + $mortalitychicks) ;
                                    }else
                                    {
                                        $chicksbalance =($chicksTotal) - ($retutnqty['returnqty'] + $grn['grn'] + $mortalitychicks) ;
                                    }

                                    if($CHICKS) {
                                          if($policy_transaction == 1 && $val['isclosed'] == 0) {
                                              echo ' <td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #ffc9c9;" onclick="mortality_new_modal_feedSchedule(' . $chicksbalance . ',' . $val['batchMasterID'] . ')">';
                                          } else {
                                              echo ' <td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #ffc9c9;">';
                                          }
                                        if (!empty($mortality)) {
                                            echo $mortality['deadChicksTotal'];
                                        }else
                                        {
                                            echo '0';
                                        }
                                        echo ' </td>';
                                    }

                                    if($CHICKS) {
                                        echo ' <td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color:#ddffdf;">';
                                        echo  $chicksbalance;
                                        echo ' </td>';
                                    }

                                    $chicksAge = $this->db->query("SELECT dpm.dispatchedDate,batch.closedDate FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 LEFT JOIN srp_erp_buyback_batch batch ON batch.batchMasterID = dpm.batchMasterID WHERE dpm.batchMasterID ={$val['batchMasterID']} ORDER BY dpm.dispatchAutoID ASC")->row_array();
                                    if (!empty($chicksAge)) {
                                        $dStart = new DateTime($chicksAge['dispatchedDate']);
                                        if ($chicksAge['closedDate'] != ' ') {
                                            $dEnd = new DateTime($chicksAge['closedDate']);
                                        } else {
                                            $dEnd = new DateTime(current_date());
                                        }
                                        $dDiff = $dStart->diff($dEnd);
                                        $newFormattedDate = $dDiff->days + 1;
                                    }

                                    if($AGE) {
                                        echo ' <td  class="mailbox-star" style="text-align: center;" width="5%">';
                                        if (!empty($chicksAge)) {
                                            echo $newFormattedDate;
                                        }
                                        echo ' </td>';
                                    }


                                    $nextInputDate = $this->db->query("SELECT DATE_FORMAT(max(dpm.documentDate),'%d-%m-%Y') AS documentDate,sum(qty) AS totalQty FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 2 WHERE batchMasterID ={$val['batchMasterID']}")->row_array();

                                    $dispatchFirstDate = $this->db->query("SELECT DATE_FORMAT(dpm.documentDate, ' . $convertFormat . ') AS documentDate FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 2 WHERE batchMasterID ={$val['batchMasterID']} AND dpm.companyID = {$companyID} ORDER BY dpm.documentDate ASC")->row_array();

                                    if (!empty($nextInputDate)) {
                                        if ($chicksTotal != 0) {
                                            $cumalativeFeed = ($nextInputDate['totalQty'] * 50) / $chicksTotal;
                                            $cal_nextinputDate = $cumalativeFeed * 1000;
                                            $currentAgeCalculation = $this->db->query("SELECT max(age) as currentAge FROM srp_erp_buyback_feedscheduledetail WHERE companyID = {$companyID} AND totalAmount <= {$cal_nextinputDate} ")->row_array();
                                            if (!empty($currentAgeCalculation)) {
                                                $currentAgeCalculation_days = $currentAgeCalculation['currentAge'];
                                                $nextInputDay = strtotime("+ $currentAgeCalculation_days day", strtotime($dispatchFirstDate["documentDate"]));
                                                if(empty($nextInputDay)){
                                                    //  $format_nextInputDay = $val['batchStartDate'];
                                                    $format_nextInputDay = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--- ';

                                                } else{
                                                    $format_nextInputDay = date("d-m-Y", $nextInputDay);
                                                }

                                                $daysBefore_days = ($currentAgeCalculation_days - 4);
                                                $daysBefore_date = strtotime("+ $daysBefore_days day", strtotime($dispatchFirstDate["documentDate"]));
                                                if(empty($daysBefore_date)){
                                                    //   $format_daysBeforeDay = $val['batchStartDate'];
                                                    $format_daysBeforeDay = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--- ';

                                                } else{
                                                    $format_daysBeforeDay = date("d-m-Y", $daysBefore_date);
                                                }
                                            }
                                        }

                                    }
                                    ?>
                                    <?php if ($FEEDUPTO) { ?>
                                        <td class="mailbox-star" style="border: solid 1px #e6e6e6 !important;" width="10%"><?php echo $format_nextInputDay; ?></td>
                                    <?php } if ($FEEDNEXT) { ?>
                                        <td class="mailbox-star" style="border: solid 1px #e6e6e6 !important;" width="10%"><?php echo $format_daysBeforeDay; ?></td>
                                    <?php }

                                     if ($FEEDVALUE) {
                                         if (!empty($feedTypes)) {
                                             foreach ($feedTypes as $feed) {

                                                 $feedBooster = $this->db->query("SELECT sum(qty) AS booster, dpdr.returnqty as boosterreturn FROM srp_erp_buyback_dispatchnote dpm LEFT JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID LEFT JOIN ( SELECT COALESCE ( sum( dpdr.qty ), 0 ) AS returnqty, dispatchAutoID,dpdr.returnAutoID
	
FROM srp_erp_buyback_dispatchreturndetails dpdr 
LEFT JOIN srp_erp_buyback_dispatchreturn retun on retun.returnAutoID = dpdr.returnAutoID
WHERE 
approvedYN  = 1
AND confirmedYN = 1
AND dpdr.buybackItemType = 2
AND feedType = {$feed['buybackFeedtypeID']}
GROUP BY dispatchAutoID 

) dpdr ON dpdr.dispatchAutoID = dpd.dispatchAutoID   WHERE batchMasterID ={$val['batchMasterID']} AND dpm.companyID = {$companyID} AND dpm.confirmedYN = 1 AND dpm.approvedYN = 1  AND buybackItemType = 2 AND feedType = {$feed['buybackFeedtypeID']} ")->row_array();
                                                 if (!empty($feedBooster)) {
                                                     $feedBooster = $feedBooster['booster'] - $feedBooster['boosterreturn'];
                                                 }
                                                 $booster = ($feed["feedAmount"] * $chicksTotal) / 50;

                                                 $balanceFeedType = $booster - $feedBooster;

                                                 ?>
                                                 <?php
                                                 if ($booster < 0) {
                                                     echo '<td class="mailbox-star" width="5%" style="text-align: center;  border: solid 1px #e6e6e0 !important; color: #faffff; background-color: #ff090e;">';
                                                     echo round($booster);
                                                     echo '</td>';
                                                 } else {
                                                     echo '<td class="mailbox-star" style="text-align: center; border: solid 1px #e6e6e6 !important;" width="5%">';
                                                     echo round($booster);
                                                     echo '</td>';
                                                 }
                                                 ?>
                                                 <?php
                                                 if ($feedBooster < 0) {
                                                     echo '<td class="mailbox-star" style="text-align: center;  border: solid 1px #e6e6e0 !important; color: #faffff; background-color: #ff090e;" width="3%">';
                                                     echo round($feedBooster);
                                                     echo '</td>';
                                                 } else {
                                                     echo '<td class="mailbox-star" style="text-align: center; border: solid 1px #e6e6e6 !important;" width="3%">';
                                                     echo round($feedBooster);
                                                     echo '</td>';
                                                 }
                                                 ?>
                                                 <?php
                                                 if ($balanceFeedType < 0) {
                                                     echo '<td class="mailbox-star" style="text-align: center;  border: solid 1px #e6e6e0 !important; color: #faffff; background-color: #ff090e;" width="3%">';
                                                     echo round($balanceFeedType);
                                                     echo '</td>';
                                                 } else {
                                                     echo '<td class="mailbox-star" style="text-align: center; border: solid 1px #e6e6e6 !important;" width="3%">';
                                                     echo round($balanceFeedType);
                                                     echo '</td>';
                                                 }
                                                 ?>

                                                 <?php
                                                 $feedTypeID = $feed['buybackFeedtypeID'];
                                                 if (array_key_exists($feedTypeID, $feedSum)) {
                                                     $feedSum[$feedTypeID]['booster'] = $feedSum[$feedTypeID]['booster'] + round($booster);
                                                     $feedSum[$feedTypeID]['feedBooster'] = $feedSum[$feedTypeID]['feedBooster'] + round($feedBooster);
                                                     if ($balanceFeedType > 0) {
                                                         $feedSum[$feedTypeID]['balanceFeedType'] = $feedSum[$feedTypeID]['balanceFeedType'] + round($balanceFeedType);
                                                     }
                                                 } else {
                                                     $feedSum[$feedTypeID]['booster'] = round($booster);
                                                     $feedSum[$feedTypeID]['feedBooster'] = round($feedBooster);
                                                     if ($balanceFeedType > 0) {
                                                         $feedSum[$feedTypeID]['balanceFeedType'] = round($balanceFeedType);
                                                     } else {
                                                         $feedSum[$feedTypeID]['balanceFeedType'] = 0;
                                                     }
                                                 }
                                             }
                                         }
                                     }
                                    ?>
                                    <td class="mailbox-star" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important;">
                                        <?php if ($val['isclosed'] == 1) { ?>
                                            <span class="label label-danger" style="  display: inline; padding: .2em .8em .3em;">&nbsp;</span>
                                            <?php
                                        } else { ?>
                                            <span class="label label-success" style="  display: inline; padding: .2em .8em .3em;">&nbsp;</span>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td class="mailbox-star" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important;">
                        <span class="pull-right">
                             <a href="#" onclick="feedScheduleReport_view(<?php echo $val['batchMasterID'] ?>)"><i
                                     class="fa fa-bar-chart" aria-hidden="true" title="Feed Shedule Day Wise" rel="tooltip"
                                     style="font-size: 14px"></i>
                             </a>
                        </span>
                                    </td>
                                </tr>
                         <?php
                                $x++;
                            }
                        }
                    }
                    ?>
                    <?php if ($colspan < 5) { ?>
                        <td style="" width="70%" colspan="<?php echo $colspan + 3?>">&nbsp;</td>
                        <td class="" width="30%" colspan="">&nbsp;</td>
                    <?php } ?>
                    </tbody>
                    <tfoot>

                    <tr>
                       <?php if ($FEEDVALUE) { ?>
                        <td class="text-right sub_total" colspan="<?php echo $colspan?>">Total</td>

                        <?php }
                        if(isset($feedTypes)) {
                            foreach ($feedSum as $val){
                                if ($FEEDVALUE) {
                                    ?>
                                    <td class="text-right total"
                                        style="text-align: center;"><?php echo $val['booster'] ?></td>
                                    <td class="text-right total"
                                        style="text-align: center;"><?php echo $val['feedBooster'] ?></td>
                                    <td class="text-right total"
                                        style="text-align: center;"><?php echo $val['balanceFeedType'] ?></td>
                                <?php }
                            }

                          /*  foreach ($feedTypes as $row) {
                                if (!($feedSum[$row['feedScheduleID']]['balanceFeedType'])) {
                                    $feedSum[$row['feedScheduleID']]['balanceFeedType'] = 0;
                                }

                            }*/
                        }
                        ?>
                        <?php  if ($FEEDVALUE) { ?>
                        <td class="text-right sub_total" colspan="2">&nbsp;</td>
                        <?php } ?>
                    </tr>
                    </tfoot>

                </table>
            </div>
        </div>
    </div>
    <?php

} else { ?>
    <br>
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO BATCHES TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $("[rel='tooltip']").tooltip();
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });

</script>