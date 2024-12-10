<?php
$companyID = $this->common_data['company_data']['company_id'];
$convertFormat = convert_date_format_sql();
if ($type == 'html') { ?>
    <style>
        .headrowtitle
        {
            font-size: 11px !important;
            line-height: 20px !important;
            height: 20px !important;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 15px;
            text-align: center;
            font-weight: bold;
            /*text-align: left;*/
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
<?php }
if($batch){ ?>
    <div class="row" style="margin-top: 5px; margin-bottom: 5px;">
        <div class="col-md-4">
            <table>
                <tr>
                    <td><span class="label label-success">&nbsp;</span> Very Satisfactory </td>
                    <td><span class="label label-warning">&nbsp;</span> Satisfactory </td>
                    <td><span class="label label-danger">&nbsp;</span> Un Satisfactory </td>
                </tr>
            </table>
        </div>
        <div class="col-md-8">
            <?php
            if ($type == 'html') {
                echo export_buttons('batchTracingReport', 'Batch Tracing', True, True);
            } ?>
        </div>
    </div>

    <div class="row" style="margin-top: 5px">
        <div class="table-responsive mailbox-messages">
            <div id="batchTracingReport">
                <table class="table-hover table-striped tableFeed">
                    <thead style="border: 1px solid #da9393;">
                    <tr>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">#</th>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">Farmer</th>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">Batch Code</th>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important; text-align: center;" colspan="5">Live Stock</th>
                        <th class="headrowtitle" style="border: solid 1px #e6e6e6 !important;" rowspan="2">Age</th>
                        <?php if($taskTypes) {
                            if (!empty($taskTypes)) {
                                //$colspan +=1;
                                foreach ($taskTypes as $task) { ?>
                                    <th class="headrowtitle" colspan="<?php echo $tripNo['value'];?>" style="text-align: center; border: solid 1px #e6e6e6 !important;" rowspan="2"><?php echo $task['shortCode']; ?></th>
                                <?php  }
                            }
                        }
                        ?>
                        <th class="headrowtitle" style="text-align: center; border: solid 1px #e6e6e6 !important;" rowspan="2">#</th>
                    </tr>
                    <tr>
                        <th class="headrowtitle" style="background-color: #a3ffa9; border: solid 1px #e6e6e6 !important; text-align: center;" title="Input">I</th>
                        <th class="headrowtitle" title="Return"  style="text-align: center; background-color: #ffafaf; border: solid 1px #e6e6e6 !important;">R</th>
                        <th class="headrowtitle" title="Grn" style="text-align: center; background-color: #c1ffc5; border: solid 1px #e6e6e6 !important;">G</th>
                        <th class="headrowtitle" title="Mortality" style="text-align: center; background-color: #ffc9c9; border: solid 1px #e6e6e6 !important;">M</th>
                        <th class="headrowtitle" title="Balance" style="text-align: center; background-color:#ddffdf; border: solid 1px #e6e6e6 !important;">B</th>
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
                    $mortalitychicks = 0;
                    $format_nextInputDay = '';
                    $format_daysBeforeDay = '';
                    $chicksreturn = 0;
                    $chicksbalance = 0;
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
                                    $chicksTotal = $this->db->query("SELECT COALESCE( sum( dpd.qty ), 0 ) AS chicksTotal FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID WHERE dpm.confirmedYN = 1 AND dpm.approvedYN = 1 AND dpd.buybackItemType = 1 AND dpm.batchMasterID = {$val['batchMasterID']}")->row_array();

                                    echo '<td class="mailbox-star ChicksTotalView" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #a8ffad;" width="5%">';
                                    if (!empty($chicksTotal)) {
                                        echo $chicksTotal['chicksTotal'];
                                    }
                                    echo '</td>';

                                    if (!empty($chicksTotal)) {
                                        $chicksTotal = $chicksTotal['chicksTotal'];
                                    }

                                    $retutnqty = $this->db->query("SELECT COALESCE( sum( dpdr.qty ), 0 ) AS returnqty, dispatchAutoID, dpdr.returnAutoID FROM srp_erp_buyback_dispatchreturndetails dpdr LEFT JOIN srp_erp_buyback_dispatchreturn retun on retun.returnAutoID = dpdr.returnAutoID WHERE confirmedYN = 1 AND approvedYN  = 1 AND dpdr.buybackItemType = 1 AND retun.batchMasterID = {$val['batchMasterID']} GROUP BY dispatchAutoID")->row_array();
                                    echo ' <td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #ffafaf;">';
                                    if (!empty($retutnqty)) {
                                        echo $retutnqty['returnqty'];
                                    }else
                                    {
                                        echo '0';
                                    }
                                    echo '</td>';

                                    if (!empty($retutnqty)) {
                                        $chicksreturn = $retutnqty['returnqty'];
                                    }

                                    $grn = $this->db->query("SELECT COALESCE ( sum( grndetail.noOfBirds ), 0 ) AS grn FROM srp_erp_buyback_grn grn LEFT JOIN srp_erp_buyback_grndetails grndetail on grndetail.grnAutoID = grn.grnAutoID WHERE confirmedYN = 1 AND approvedYN  = 1 AND grn.batchMasterID = {$val['batchMasterID']}")->row_array();
                                    echo ' <td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #c1ffc5;">';
                                    if (!empty($grn)) {
                                        echo $grn['grn'];
                                    }else
                                    {
                                        echo '0';
                                    }
                                    echo ' </td>';

                                    if (!empty($grn)) {
                                        $chicksbalance = ($chicksTotal) - ($retutnqty['returnqty'] + $grn['grn']) ;
                                    }else
                                    {
                                        $chicksbalance = $chicksTotal - $grn['grn'];
                                    }

                                    $mortality = $this->db->query("SELECT COALESCE(sum(noOfBirds), 0) AS deadChicksTotal FROM srp_erp_buyback_mortalitymaster mm INNER JOIN srp_erp_buyback_mortalitydetails md ON mm.mortalityAutoID = md.mortalityAutoID WHERE confirmedYN = 1 AND batchMasterID ={$val['batchMasterID']}")->row_array();
                                    echo '<td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color: #ffc9c9;">';
                                    if (!empty($mortality)) {
                                        echo $mortality['deadChicksTotal'];
                                    }else
                                    {
                                        echo '0';
                                    }
                                    echo ' </td>';

                                    if (!empty($mortality)) {
                                        $mortalitychicks =  $mortality['deadChicksTotal'];
                                        $chicksbalance = ($chicksTotal) - ($retutnqty['returnqty'] + $grn['grn'] + $mortalitychicks) ;
                                    }else
                                    {
                                        $chicksbalance =($chicksTotal) - ($retutnqty['returnqty'] + $grn['grn'] + $mortalitychicks) ;
                                    }

                                    echo ' <td class="mailbox-star ChicksTotalView" width="5%" style="text-align: center; border: solid 1px #e6e6e6 !important; background-color:#ddffdf;">';
                                    echo  $chicksbalance;
                                    echo ' </td>';

                                    echo ' <td  class="mailbox-star" style="text-align: center;" width="5%">';
                                        echo $ageFilter;
                                    echo ' </td>';

                                    if($taskTypes){
                                      //  $farmVisitID = $this->db->query("SELECT numberOfVisit, farmerVisitID, batchMasterID FROM srp_erp_buyback_farmervisitreport WHERE companyID = $companyID AND batchMasterID = {$val['batchMasterID']}")->result_array();
                                        foreach ($taskTypes as $task){
                                            $output = 0;
                                            for($i = 1; $i <= $tripNo['value']; $i++){
                                                $farmVisitID = $this->db->query("SELECT value
                                                                FROM srp_erp_buyback_farmervisitreport fvr
                                                                LEFT JOIN srp_erp_buyback_visittasktypes_details visit ON visit.farmerVisitID = fvr.farmerVisitID
                                                                WHERE fvr.companyID = $companyID AND confirmedYN = 1 AND fvr.batchMasterID = {$val['batchMasterID']} 
                                                                AND numberOfVisit = $i
                                                                AND visitTaskTypeID = {$task['visitTaskTypeID']}")->row_array();

                                                if($farmVisitID){
                                                    if($farmVisitID['value'] == 1){
                                                        echo '<td style="background-color: #00a65a; border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                    } else if($farmVisitID['value'] == 2){
                                                        echo '<td style="background-color: #f39c12; border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                    } else if($farmVisitID['value'] == 3){
                                                        echo '<td style="background-color: #dd4b39; border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                    } else if($farmVisitID['value'] == 0){
                                                        echo '<td style="border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                    }
                                                } else {
                                                    echo '<td style="border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                }
                                            }
                                            /*foreach ($farmVisitID as $visitNo){
                                                $taskDetails = $this->db->query("SELECT value FROM srp_erp_buyback_visittasktypes_details WHERE companyID = $companyID AND farmerVisitID = {$visitNo['farmerVisitID']} AND visitTaskTypeID = {$task['visitTaskTypeID']} ")->row_array();
                                                if($taskDetails){
                                                    if($taskDetails['value'] == 1){
                                                        echo '<td style="background-color: #00a65a; border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                    } else if($taskDetails['value'] == 2){
                                                        echo '<td style="background-color: #f39c12; border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                    } else if($taskDetails['value'] == 3){
                                                        echo '<td style="background-color: #dd4b39; border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                    } else if($taskDetails['value'] == 0){
                                                        echo '<td style="border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                    }
                                                } else {
                                                    echo '<td style="border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                }
                                                $output += 1;
                                            }
                                            while($output < $tripNo['value']) {
                                                echo '<td style="border: solid 1px #e6e6e6 !important;">&nbsp;</td>';
                                                $output++;
                                            }*/
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
                                </tr>
                                <?php
                                $x++;
                            }
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
} else { ?>
    <br>
    <div class="alert" role="alert" style="background: #9ab9f1">THERE ARE NO BATCHES TO DISPLAY.</div>
    <?php }?>


<?php
/**
 * Created by PhpStorm.
 * User: safeena
 * Date: 4/16/2019
 * Time: 12:30 PM
 */