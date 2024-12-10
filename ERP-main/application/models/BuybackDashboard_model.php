<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class BuybackDashboard_model extends ERP_Model
{
    function __contruct()
    {
        parent::__contruct();
        $this->load->helper('buyback_helper');
    }

    function buybackDashSum_Count()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $companyFinanceYearID = $this->input->post('FinanceYear');
        $FinanceYearData = $this->db->query("SELECT YEAR(beginingDate) as year FROM srp_erp_companyfinanceyear WHERE companyFinanceYearID = $companyFinanceYearID")->row_array();
        $FinanceYear = $FinanceYearData['year'];

        $farm_count = $this->db->query("SELECT COUNT(*) as activeFarmCount FROM srp_erp_buyback_farmmaster where companyID = '{$companyID}' AND isActive=1")->row_array();
        $batch_count = $this->db->query("SELECT COUNT(*) as activeBatchesCount FROM srp_erp_buyback_batch LEFT JOIN srp_erp_buyback_farmmaster fm ON fm.farmID = srp_erp_buyback_batch.farmID AND fm.isActive = 1 where fm.companyID = '{$companyID}' AND confirmedYN='0' AND isclosed='0' AND YEAR(batchStartDate) = {$FinanceYear}")->row_array();

        $data['farms'] = $farm_count['activeFarmCount'];
        $data['batches'] = $batch_count['activeBatchesCount'];

        $batchID = $this->db->query("SELECT batchMasterID FROM srp_erp_buyback_batch where companyID = '{$companyID}' AND isclosed='1' AND YEAR(batchStartDate) = $FinanceYear ORDER BY batchMasterID ASC ")->result_array();

        $a = 0;
        $ind = 0;
        $index = 0;
        $profBatch = array();
        $lossBatch = array();
        $b = 0;
        $aa = 1;

        foreach ($batchID as $key){
            $wages = wagesPayableAmount($key['batchMasterID'], FALSE);
            if($wages['transactionAmount'] >= 0){
                $a += 1;
                $profBatch[$ind++]= $key['batchMasterID'];

            } else{
                $b += 1;
                $lossBatch[$index++]= $key['batchMasterID'];

            }

            $aa ++;
        }
        $data['profit'] = $a;
        $data['loss'] = $b;
        $data['Profitid'] = $profBatch;
        $data['Lossid'] = $lossBatch;

        $TodoDate = current_date();
        $data['pendingToDo'] = $this->fetchPendingTasksToDo($TodoDate);
        $data['countTodo'] = count($data['pendingToDo']);

        return $data;
    }

    function fetch_filter_date()
    {
        $convertFormat=convert_date_format_sql();
        $this->db->select('DATE_FORMAT(beginingDate,\''.$convertFormat.'\') AS beginingDate,DATE_FORMAT(endingDate,\''.$convertFormat.'\') AS endingDate');
        $this->db->from('srp_erp_companyfinanceyear');
        $this->db->where('companyFinanceYearID',$this->input->post('year'));
        $this->db->where('isClosed',0);
        $this->db->where('isActive',1);
        return $this->db->get()->row_array();
    }

    function feedScheduleCalendar($feed)
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();

        $batchID = $this->db->query("SELECT batchMasterID FROM srp_erp_buyback_batch where companyID = '{$companyID}' AND isclosed='0' ORDER BY batchMasterID ASC ")->result_array();
        $aa = 1;
        $id = array();
        $idCount = 0;
        foreach ($batchID as $key) {
            $this->db->select('*, DATE_FORMAT(dpm.documentDate,\'' . $convertFormat . '\') AS documentDate');
            $this->db->from("srp_erp_buyback_dispatchnote dpm");
            $this->db->join('srp_erp_buyback_dispatchnotedetails dpd', 'dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 2');
            $this->db->where("dpm.companyID", $companyID);
            $this->db->where("batchMasterID", $key['batchMasterID']);
            $this->db->order_by("dpm.documentDate ASC");
            $dispatch = $this->db->get()->result_array();

            $this->db->select("sum(qty) AS chicksTotal");
            $this->db->from("srp_erp_buyback_dispatchnote dpm");
            $this->db->join('srp_erp_buyback_dispatchnotedetails dpd', 'dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1', 'LEFT');
            $this->db->where("dpm.companyID", $companyID);
            $this->db->where("batchMasterID", $key['batchMasterID']);
            $chicks = $this->db->get()->row_array();

            $this->db->select('DATE_FORMAT(dpm.documentDate,\'' . $convertFormat . '\') AS documentDate');
            $this->db->from("srp_erp_buyback_dispatchnote dpm");
            $this->db->join('srp_erp_buyback_dispatchnotedetails dpd', 'dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 2');
            $this->db->where("dpm.companyID", $companyID);
            $this->db->where("batchMasterID", $key['batchMasterID']);
            $this->db->order_by("dpm.documentDate ASC");
            $dispatchFirstDate = $this->db->get()->row_array();

            if(($chicks['chicksTotal'])==''){
                $chicks['chicksTotal'] = 1;
            }

            $balanceFeed = 0;
            $a = 1;
            foreach ($dispatch as $row) {
                if ($balanceFeed == 0) {
                    $balanceFeed = $row["qty"];
                } else {
                    $balanceFeed += $row["qty"];
                }
                $cumalativeFeed = (($balanceFeed * 50) / $chicks['chicksTotal'])*1000;

                $currentAgeCalculation = $this->db->query("SELECT max(age) as currentAge FROM srp_erp_buyback_feedscheduledetail WHERE companyID = {$companyID} AND totalAmount <= {$cumalativeFeed} ")->row_array();

                if (!empty($currentAgeCalculation)) {
                    $currentAgeCalculation_days = $currentAgeCalculation['currentAge'];
                    $nextInputDay = strtotime("+ $currentAgeCalculation_days day", strtotime($dispatchFirstDate['documentDate']));
                    $format_nextInputDay = date("d-m-Y", $nextInputDay);
                }

                if($format_nextInputDay == $feed){
                    $array['id'] = $key['batchMasterID'];
                    $array['chicks'] = $chicks['chicksTotal'];
                    $id[$idCount++] = $array;
                }

                $a++ ;
            }

            $aa++;
        }

        $feedTypes = $this->db->query("SELECT feedAmount,ft.description as feedName FROM srp_erp_buyback_feedschedulemaster fsm LEFT JOIN srp_erp_buyback_feedtypes ft ON fsm.feedTypeID = ft.buybackFeedtypeID WHERE fsm.companyID = {$companyID} ORDER BY feedScheduleID ASC")->result_array();
        $b = 1;
        if(!empty($id) && !empty($feedTypes)){
            $dataarray = array();
            foreach ($id as $keydata) {
                foreach ($feedTypes as $feed) {
                    $booster = ($feed["feedAmount"] * $keydata['chicks']) / 50;

                    if ($booster < 0) {
                        $boost = round($booster);
                    } else {
                        $boost = round($booster);
                    }
                    $data['type'] = $feed['feedName'];
                    $data['feed'] = $boost;
                    $dataarray[$idCount++] = $data;
                }

                if(!empty($dataarray)){
                    foreach ($dataarray as $key){
                        echo '<div class="col-sm-6">
                            <div class="clearfix">
                                <span class="pull-left" style="padding: 10px">';
                        echo trim($key['type'] ?? ''),': ', trim($key['feed'] ?? '');
                        echo ' </span>
</div>
</div>';
                        $b++;
                    }
                }
            }
        }
        else{
            foreach ($feedTypes as $feed) {
                echo '<div class="col-sm-6">
                            <div class="clearfix">
                                <span class="pull-left" style="padding: 10px">';
                echo trim($feed['feedName'] ?? ''),': ', 0;
                echo ' </span>
</div>
</div>';
                $b++;
            }
        }
    }

    function FarmLogData($ageFrom,$ageTo){
        $this->db->select('srp_erp_buyback_batch.batchMasterID AS batchID,srp_erp_buyback_batch.isclosed ,batchCode,srp_erp_buyback_farmmaster.description, COALESCE(sum(srp_erp_buyback_dispatchnotedetails.qty), 0) AS chicksTotal', false)
            ->from('srp_erp_buyback_batch')
            ->join('srp_erp_buyback_farmmaster', 'srp_erp_buyback_farmmaster.farmID = srp_erp_buyback_batch.farmID','LEFT')
            ->join('srp_erp_buyback_dispatchnote', 'srp_erp_buyback_dispatchnote.batchMasterID = srp_erp_buyback_batch.batchMasterID','LEFT')
            ->join('srp_erp_buyback_dispatchnotedetails', 'srp_erp_buyback_dispatchnotedetails.dispatchAutoID = srp_erp_buyback_dispatchnote.dispatchAutoID AND buybackItemType = 1','INNER')
            ->where('srp_erp_buyback_batch.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_buyback_batch.isclosed', 0)
            ->group_by('srp_erp_buyback_batch.batchMasterID');
        $farmLogTableData =  $this->db->get()->result_array();

        if(!empty($farmLogTableData)){
            $a = 1;
            $chickTot = 0;
            $balanceTot = 0;
            $outputTot = 0;
            foreach ($farmLogTableData as $var){
                $balance = chicks_balance_dashboard($var['batchID'], $var['chicksTotal']);
                $age = chicks_age_dashboard($var['batchID'],$ageFrom,$ageTo);
                $balanceChicksTotal = $this->db->query("SELECT COALESCE(sum(grndetail.noOfBirds ),0) AS grn FROM srp_erp_buyback_grn grn LEFT JOIN srp_erp_buyback_grndetails grndetail on grndetail.grnAutoID = grn.grnAutoID WHERE confirmedYN = 1 AND approvedYN  = 1 AND grn.batchMasterID = {$var['batchID']}")->row_array();

                if(!empty($age)){
                    echo '<tr class="task-cat-upcoming">';
                    echo '<td>';
                    echo $a ;
                    echo '</td><td>';
                    echo $var['description'];
                    echo '</td><td>';
                    echo  $var['batchCode'];
                    echo '</td><td class="text-right">';
                    echo  $age;
                    echo '</td><td class="text-right">';
                    echo  $var['chicksTotal'];
                    echo '</td><td class="text-right">';
                    echo  $balanceChicksTotal['grn'];
                    echo '</td><td class="text-right">';
                    echo  $balance;
                    echo '</td>
                </tr>';
                $chickTot +=  $var['chicksTotal'];
                $balanceTot += $balance;
                $outputTot += $balanceChicksTotal['grn'];
                $a ++;
                }
            }

            echo '<tr class="task-cat-upcoming">';
            echo '<td></td><td colspan="3" ><b>Total</b></td><td class="text-right"><b>';
            echo  $chickTot;
            echo '</b></td><td class="text-right"><b>';
            echo  $outputTot;
            echo '</b></td><td class="text-right"><b>';
            echo  $balanceTot;
            echo '</b></td>
                </tr>';

        } else {
            echo '<tr><td colspan="7"><strong><center>No data Found</center></strong></td> </tr>';
        }
    }

    function fetchBatchProfitLossData(){
        $companyID = $this->common_data['company_data']['company_id'];
        $idset = $this->input->post('id');
        $ids = explode(',', $idset);
        $a = 1;
        $b = 0;
        $totalAmount = 0;

        $farm = $this->input->post('farmerid');
        if(empty($farm)){
            $farmer = 0;
        }else{
            $farmer = "'" . implode("', '", $farm) . "'";
        }

        $current_date = current_format_date();
        $date_format_policy = date_format_policy();

        $date_To = $this->input->post('date_To');
        $date_from = $this->input->post('date_from');
        $datefromconvert = input_format_date($date_from, $date_format_policy);
        $datetoconvert = input_format_date($date_To, $date_format_policy);
        $date = "";
        if (!empty($date_from) && !empty($date_To)) {
            $date .= " AND ( batchStartDate BETWEEN '" . $datefromconvert . "' AND '" . $datetoconvert . " ')";
        }

   //     var_dump($datefromconvert,$date_from);
      //  var_dump($ids);

        if(!empty($idset)){
            foreach ($ids as $id) {
                $batchData = $this->db->query("SELECT srp_erp_buyback_batch.batchMasterID, srp_erp_buyback_batch.batchCode, srp_erp_buyback_batch.batchStartDate, srp_erp_buyback_farmmaster.description, COALESCE(sum(srp_erp_buyback_dispatchnotedetails.qty), 0) AS chicksTotal 
                                                    FROM srp_erp_buyback_batch 
                                                    LEFT JOIN srp_erp_buyback_farmmaster ON srp_erp_buyback_farmmaster.farmID = srp_erp_buyback_batch.farmID 
                                                    LEFT JOIN srp_erp_buyback_dispatchnote ON srp_erp_buyback_dispatchnote.batchMasterID = srp_erp_buyback_batch.batchMasterID 
                                                    INNER JOIN srp_erp_buyback_dispatchnotedetails ON srp_erp_buyback_dispatchnotedetails.dispatchAutoID = srp_erp_buyback_dispatchnote.dispatchAutoID AND buybackItemType = 1 
                                                    where srp_erp_buyback_batch.companyID = '{$companyID}' AND srp_erp_buyback_batch.isclosed='1' $date AND srp_erp_buyback_batch.batchMasterID = $id                                                 
                                                    AND srp_erp_buyback_farmmaster.farmID IN ($farmer)")->row_array();

                if ($batchData['batchMasterID'] != '') {
                    $TotbalanceChick = $this->db->query("SELECT COALESCE(sum(srp_erp_buyback_grndetails.noOfBirds), 0) AS balanceChicksTotal
                                                    FROM srp_erp_buyback_grn 
                                                    INNER JOIN srp_erp_buyback_grndetails ON srp_erp_buyback_grndetails.grnAutoID = srp_erp_buyback_grn.grnAutoID
                                                    where batchMasterID = $id ")->row_array();

                    $TotdeadChick = $this->db->query("SELECT COALESCE(sum(noOfBirds), 0) AS deadChicksTotal
                                                    FROM srp_erp_buyback_mortalitymaster 
                                                    INNER JOIN srp_erp_buyback_mortalitydetails ON srp_erp_buyback_mortalitydetails.mortalityAutoID = srp_erp_buyback_mortalitymaster.mortalityAutoID
                                                    where batchMasterID = $id ")->row_array();

                    $totalChicks = 0;
                    if (!empty($TotbalanceChick)) {
                        $totalChicks = ($batchData['chicksTotal'] - ($TotbalanceChick['balanceChicksTotal'] + $TotdeadChick['deadChicksTotal']));
                    }

                    $wages = wagesPayableAmount($batchData['batchMasterID'], FALSE);
                    $wagesPayable = $wages['transactionAmount'];
                    $amount = number_format($wagesPayable, 2);


                    $age = chicks_age_dashboard($batchData['batchMasterID'], '', '');
                    if (empty($age)) {
                        $age = 0;
                    }
                    echo '<tr>';
                    echo '<td>';
                    echo $a;
                    echo '</td>';
                    echo '<td>';
                    echo $batchData['description'];
                    echo '</td>';
                    echo '<td><a onclick="generateProductionReport_preformance(';
                    echo $batchData['batchMasterID'];
                    echo ')">';
                    echo $batchData['batchCode'];
                    echo '</a>';
                    echo '</td>';
                    echo '<td>';
                    echo $batchData['batchStartDate'];
                    echo '</td>';
                    echo '<td class="text-right">';
                    echo $batchData['chicksTotal'];
                    echo '</td>';
                    echo '<td class="text-right">';
                    echo $TotbalanceChick['balanceChicksTotal'];
                    echo '</td>';
                    echo '<td class="text-right">';
                    echo $totalChicks;
                    echo '</td>';
                    echo '<td class="text-right">';
                    echo $age;
                    echo '</td>';
                    echo '<td class="pull-right">';
                    echo $amount;
                    echo '</td>';
                    echo '</tr>';
                    $totalAmount += $wagesPayable;
                    $b += 1;
                    $a++;
                }
            }
        }

        if($b == 0){
            echo '<tr><td colspan="9"><strong><center>No data Found</center></strong></td></tr>';
        }else {
            echo '<hr>';
            echo '<tr>';
            echo '<td colspan="8"><strong>';
            echo 'Total Amount';
            echo '</strong></td>';
            echo '<td class="pull-right reporttotal">';
            echo number_format($totalAmount, 2);
            echo '</td>';
            echo '</tr>';
        }
    }

    function fcr_data(){
        $companyID = $this->common_data['company_data']['company_id'];
        $FinanceYearData = $this->db->query("SELECT YEAR(beginingDate) as year, MONTH(beginingDate) as month, DAY(beginingDate) as day FROM srp_erp_companyfinanceyear WHERE companyID = $companyID AND isCurrent = 1")->row_array();

        $period = "";
        $data_app = array();
        for ($a = 1; $a <= 3; $a++) {
            if ($a == 1){
                $period .= " AND ( srp_erp_buyback_itemledger.YEAR(documentDate) == '" . $FinanceYearData['year'] . "'  ')";
            } elseif ($a == 2){
                $period .= " AND ( srp_erp_buyback_itemledger.YEAR(documentDate) == '" . $FinanceYearData['month'] . "'  ')";
            }else{
                $period .= " AND ( srp_erp_buyback_itemledger.YEAR(documentDate) == '" . $FinanceYearData['day'] . "'  ')";
            }

            $chicksTotal = $this->db->query("SELECT sum( transactionQTY ) AS chicksTotal FROM srp_erp_buyback_itemledger 
                                              LEFT JOIN srp_erp_buyback_batch ON srp_erp_buyback_batch.batchMasterID = srp_erp_buyback_itemledger.batchID 
                                              WHERE srp_erp_buyback_itemledger.companyID = $companyID 
                                                AND srp_erp_buyback_batch.isclosed = 1
                                                AND srp_erp_buyback_itemledger.buybackItemType = 1 
                                                AND srp_erp_buyback_itemledger.documentCode = 'BBDPN' 
                                                $period")->row_array();

            $feedTotal = $this->db->query("SELECT sum( transactionQTY ) AS feedTotal 
                                                  FROM srp_erp_buyback_itemledger 
                                                  LEFT JOIN srp_erp_buyback_batch ON srp_erp_buyback_batch.batchMasterID = srp_erp_buyback_itemledger.batchID 
                                                  WHERE srp_erp_buyback_itemledger.companyID = $companyID 
                                                    AND srp_erp_buyback_batch.isclosed = 1
                                                    AND `documentCode` = 'BBDPN' 
                                                    AND `buybackItemType` = 2 
                                                    $period")->row_array();

            $birdstotalcount = $this->db->query("SELECT sum( noOfBirds ) AS birdstotalcount, sum( transactionQTY ) AS birdskgsweight 
                                                  FROM srp_erp_buyback_itemledger 
                                                  LEFT JOIN srp_erp_buyback_batch ON srp_erp_buyback_batch.batchMasterID = srp_erp_buyback_itemledger.batchID 
                                                  WHERE srp_erp_buyback_itemledger.companyID = $companyID
                                                    AND srp_erp_buyback_batch.isclosed = 1
                                                    AND `documentCode` = 'BBGRN' 
                                                    $period")->row_array();

            $feedTot = ($chicksTotal['chicksTotal'] + $birdstotalcount['birdstotalcount']) / 2;
            $feedPer = ($feedTot == 0) ? '0' : ($feedTotal['feedTotal'] * 50) / $feedTot;
            $feedPercentage = number_format($feedPer, 2);

            $weightPer = ($birdstotalcount['birdstotalcount'] == 0) ? '0' : ($birdstotalcount['birdskgsweight'] / $birdstotalcount['birdstotalcount']);
            $weightPercentage = round($weightPer, 2);

            $fcrdata = ($weightPercentage == 0) ? '0' : ($feedPercentage / $weightPercentage);
            $data_app['feedRate'] = number_format($fcrdata, 2);
        }
    }

    function monthlyChartDetails($FinanceYear)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $data[] = array();
        for($x = 1; $x <= 12; $x++)
        {
            $columnMortal =  $this->db->query("SELECT sum( noOfBirds ) AS noOfBirds, MONTH(documentDate) AS documentMonth, YEAR(documentDate) AS documentYear FROM srp_erp_buyback_mortalitymaster LEFT JOIN srp_erp_buyback_mortalitydetails ON srp_erp_buyback_mortalitydetails.mortalityAutoID = srp_erp_buyback_mortalitymaster.mortalityAutoID WHERE srp_erp_buyback_mortalitymaster.companyID = $companyID AND confirmedYN = 1 AND YEAR(documentDate) = $FinanceYear AND MONTH(documentDate) = $x")->row_array();
            if(!empty($columnMortal['noOfBirds'])){
                $data['columnMortal'][] = $columnMortal['noOfBirds'];
            } else{
                $data['columnMortal'][] = 0;
            }
            $columnChicks =   $this->db->query("SELECT sum( qty ) AS noOfChicks, MONTH(documentDate) AS documentMonth, YEAR(documentDate) AS documentYear FROM srp_erp_buyback_dispatchnote LEFT JOIN srp_erp_buyback_dispatchnotedetails ON srp_erp_buyback_dispatchnotedetails.dispatchAutoID = srp_erp_buyback_dispatchnote.dispatchAutoID WHERE srp_erp_buyback_dispatchnote.companyID = $companyID AND confirmedYN = 1 AND YEAR(documentDate) = $FinanceYear AND srp_erp_buyback_dispatchnotedetails.buybackItemType = 1 AND MONTH(documentDate) = $x")->row_array();
            if(!empty($columnChicks['noOfChicks'])){
                $data['columnChicks'][] = $columnChicks['noOfChicks'];
            } else{
                $data['columnChicks'][] = 0;
            }

            $columnLiveBirds =   $this->db->query("SELECT sum( noOfBirds ) AS noOfliveBirds, MONTH(documentDate) AS documentMonth, YEAR(documentDate) AS documentYear FROM srp_erp_buyback_grn LEFT JOIN srp_erp_buyback_grndetails ON srp_erp_buyback_grndetails.grnAutoID = srp_erp_buyback_grn.grnAutoID WHERE srp_erp_buyback_grn.companyID = $companyID AND confirmedYN = 1 AND YEAR(documentDate) = $FinanceYear AND MONTH(documentDate) = $x")->row_array();
            if(!empty($columnLiveBirds['noOfliveBirds'])){
                $data['columnLiveBirds'][] = $columnLiveBirds['noOfliveBirds'];
            } else{
                $data['columnLiveBirds'][] = 0;
            }
        }

        return $data;
    }

    function yearProfitDetails($FinanceYear){
        $companyID = $this->common_data['company_data']['company_id'];
        $data[] = array();
        $lastYear = $FinanceYear - 1;

        for($x = 1; $x <= 12; $x++)
        {
            /*YEAR BEFOR DATA*/
            $batch =  $this->db->query("SELECT batchMasterID FROM srp_erp_buyback_batch WHERE companyID = {$companyID} AND isclosed = 1 AND approvedYN = 1 AND YEAR(closedDate) = {$lastYear} AND MONTH(closedDate) = {$x}")->result_array();
            $a = 0;
            foreach ($batch as $val){
                $wages = wagesPayableAmount($val['batchMasterID'], FALSE);
                if($wages['transactionAmount'] > 0){
                    $a += 1;
                }
            }
            $data['lastYear'][] = $a;

            /*SELECTED YEAR DATA*/
            $batch =  $this->db->query("SELECT batchMasterID FROM srp_erp_buyback_batch WHERE companyID = {$companyID} AND isclosed = 1 AND approvedYN = 1 AND YEAR(closedDate) = {$FinanceYear} AND MONTH(closedDate) = {$x}")->result_array();
            $b = 0;
            foreach ($batch as $val){
                $wages = wagesPayableAmount($val['batchMasterID'], FALSE);
                if($wages['transactionAmount'] > 0){
                    $b += 1;
                }
            }
            $data['thisYear'][] = $b;
        }
        return $data;
    }

    function fetchToDoListDetails()
    {
        $data = array();
        $index = 0;
        $TodoDate = $this->input->post('TodoDate');
        $companyID = $this->common_data['company_data']['company_id'];
        $batchdata = $this->db->query("SELECT fm.farmID,batch.isclosed, CONCAT(fm.farmSystemCode, ' | ', fm.description) as farm, batch.batchCode, batch.batchMasterID, batchStartDate, dpdetails.dispatchedDate
                                FROM srp_erp_buyback_batch batch 
                                LEFT JOIN srp_erp_buyback_farmmaster fm ON fm.farmID = batch.farmID
                                LEFT JOIN (SELECT dpm.dispatchedDate, srp_erp_buyback_batch.batchMasterID
                                        FROM srp_erp_buyback_dispatchnote dpm
                                        INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 
                                        LEFT JOIN srp_erp_buyback_batch ON srp_erp_buyback_batch.batchMasterID = dpm.batchMasterID
                                        WHERE dpm.companyID = $companyID) 
                                  dpdetails on dpdetails.batchMasterID =  batch.batchMasterID
                                WHERE fm.companyID = '{$companyID}' AND batch.companyID = '{$companyID}' /*AND batch.isclosed='0'*/ AND fm.isActive = 1
                                ORDER BY batch.batchMasterID ASC ")->result_array();

        foreach ($batchdata as $val)
        {
            if (!empty($val['dispatchedDate'])) {
                $dStart = new DateTime($val['dispatchedDate']);
                if(!empty($TodoDate))
                {
                    $dEnd = new DateTime($TodoDate);
                } else
                {
                    $dEnd = new DateTime(current_date());
                }
                $dDiff = $dStart->diff($dEnd);
                $newFormattedDate = $dDiff->days + 1;

                $todoDetails = $this->db->query("SELECT taskMaster.description as task, task.isActive, taskMaster.tasktypeID, details.feedscheduledetailID
                                        FROM srp_erp_buyback_tasktypes_details task
                                        INNER JOIN srp_erp_buyback_feedscheduledetail details ON task.feedscheduledetailID = details.feedscheduledetailID
                                        LEFT JOIN srp_erp_buyback_tasktypes_master taskMaster ON task.tasktypeID = taskMaster.tasktypeID
                                        WHERE details.companyID = $companyID AND task.companyID = $companyID AND task.isActive = 1 AND details.age = $newFormattedDate")->result_array();
                if($todoDetails)
                {
                    foreach ($todoDetails as $toDayDo)
                    {
                        $taskDone = $this->db->query("SELECT task.todoTaskAutoID, taskComment, taskDate, linkedDocumentCode FROM srp_erp_buyback_tasks_done task
                                      WHERE companyID = $companyID AND tasktypeID = {$toDayDo['tasktypeID']} AND feedscheduledetailID = {$toDayDo['feedscheduledetailID']} AND batchMasterID = {$val['batchMasterID']}")->row_array();

                        if($val['isclosed'] == 0) {
                            $data[$index]['tasktypeID'] = $toDayDo['tasktypeID'];
                            $data[$index]['feedscheduledetailID'] = $toDayDo['feedscheduledetailID'];
                            $data[$index]['farmID'] = $val['farmID'];
                            $data[$index]['farm'] = $val['farm'];
                            $data[$index]['batch'] = $val['batchCode'];
                            $data[$index]['batchMasterID'] = $val['batchMasterID'];
                            $data[$index]['description'] = $toDayDo['task'];
                            $data[$index]['taskID'] = $taskDone['todoTaskAutoID'];
                            $data[$index]['linkedDocument'] = $taskDone['linkedDocumentCode'];
                            $data[$index]['taskComment'] = $taskDone['taskComment'];
                            $data[$index]['taskDate'] = $taskDone['taskDate'];
                            $data[$index]['batchAge'] = $newFormattedDate;
                            $index++;

                        } else if($val['isclosed'] == 1 && !empty($taskDone)) {
                            $data[$index]['tasktypeID'] = $toDayDo['tasktypeID'];
                            $data[$index]['feedscheduledetailID'] = $toDayDo['feedscheduledetailID'];
                            $data[$index]['farmID'] = $val['farmID'];
                            $data[$index]['farm'] = $val['farm'];
                            $data[$index]['batch'] = $val['batchCode'];
                            $data[$index]['batchMasterID'] = $val['batchMasterID'];
                            $data[$index]['description'] = $toDayDo['task'];
                            $data[$index]['taskID'] = $taskDone['todoTaskAutoID'];
                            $data[$index]['linkedDocument'] = $taskDone['linkedDocumentCode'];
                            $data[$index]['taskComment'] = $taskDone['taskComment'];
                            $data[$index]['taskDate'] = $taskDone['taskDate'];
                            $data[$index]['batchAge'] = $newFormattedDate;
                            $index++;
                        }
                    }
                }
            }
        }
        return $data;
    }

    function fetchPendingTasksToDo($TodoDate)
    {
        $data = array();
        $index = 0;
        $date_format_policy = date_format_policy();
        $TodoDate = input_format_date($TodoDate, $date_format_policy);
        $companyID = $this->common_data['company_data']['company_id'];
        $todoDetails = $this->db->query("SELECT taskMaster.description as task, taskMaster.tasktypeID, details.age, details.feedscheduledetailID
                                        FROM srp_erp_buyback_tasktypes_details task
                                        INNER JOIN srp_erp_buyback_feedscheduledetail details ON task.feedscheduledetailID = details.feedscheduledetailID
                                        LEFT JOIN srp_erp_buyback_tasktypes_master taskMaster ON task.tasktypeID = taskMaster.tasktypeID
                                        WHERE task.companyID = $companyID AND details.companyID = $companyID AND task.isActive = 1 ORDER BY age ASC")->result_array();

        $batchdata = $this->db->query("SELECT fm.farmID, CONCAT(fm.farmSystemCode, ' | ', fm.description) as farm, batch.batchCode, batch.batchMasterID, batchStartDate
                                FROM srp_erp_buyback_batch batch 
                                LEFT JOIN srp_erp_buyback_farmmaster fm ON fm.farmID = batch.farmID
                                WHERE fm.companyID = '{$companyID}' AND batch.companyID = '{$companyID}' AND batch.isclosed='0' AND fm.isActive = 1
                                ORDER BY batch.batchMasterID ASC ")->result_array();

        foreach ($batchdata as $value)
        {
            $batchage = 0;
            $chicksAge = $this->db->query("SELECT dpm.dispatchedDate,batch.closedDate FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 LEFT JOIN srp_erp_buyback_batch batch ON batch.batchMasterID = dpm.batchMasterID WHERE dpm.companyID = '{$companyID}' AND dpm.batchMasterID ={$value['batchMasterID']} ORDER BY dpm.dispatchAutoID ASC")->row_array();
            if (!empty($chicksAge['dispatchedDate'])) {
                if($chicksAge['dispatchedDate'] < $TodoDate){
                    $dStart = new DateTime($chicksAge['dispatchedDate']);
                    if (!empty($TodoDate)) {
                        $dEnd = new DateTime($TodoDate);
                    } else {
                        $dEnd = new DateTime(current_date());
                    }
                    $dDiff = $dStart->diff($dEnd);
                    $batchage = $dDiff->days + 1;
                }
            }
            foreach ($todoDetails as $val)
            {
                if($val['age'] < $batchage){
                   $taskDone = $this->db->query("SELECT task.todoTaskAutoID, taskComment, taskDate, linkedDocumentCode FROM srp_erp_buyback_tasks_done task
                                      WHERE companyID = $companyID AND tasktypeID = {$val['tasktypeID']} AND feedscheduledetailID = {$val['feedscheduledetailID']} AND batchMasterID = {$value['batchMasterID']}")->row_array();

                   if(empty ($taskDone)){
                       $current_age = ($val['age']) - 1;
                       $task_date = strtotime("+ $current_age day", strtotime($chicksAge['dispatchedDate']));

                       $data[$index]['tasktypeID'] = $val['tasktypeID'];
                       $data[$index]['feedscheduledetailID'] = $val['feedscheduledetailID'];
                       $data[$index]['farmID'] = $value['farmID'];
                       $data[$index]['farm'] = $value['farm'];
                       $data[$index]['batch'] = $value['batchCode'];
                       $data[$index]['batchMasterID'] = $value['batchMasterID'];
                       $data[$index]['description'] = $val['task'];
                       $data[$index]['batchAge'] = $val['age'];
                       $data[$index]['task_date'] = $task_date;
                       $index++;
                   }
               }
            }
        }
        return $data;
    }

    function Save_TaskDone()
    {
        $feedscheduledetailID = trim($this->input->post('feedscheduledetailID') ?? '');
        $tasktypeID = trim($this->input->post('tasktypeID') ?? '');
        $farmMasID = trim($this->input->post('farmMasID') ?? '');
        $batchID = trim($this->input->post('batchID') ?? '');
        $taskDescription = trim($this->input->post('taskDescription') ?? '');
        $taskComment = trim($this->input->post('taskComment') ?? '');
        $DocumentAutoID = trim($this->input->post('DocumentAutoID') ?? '');


        $data['farmID'] = $farmMasID;
        $data['feedscheduledetailID'] = $feedscheduledetailID;
        $data['batchMasterID'] = $batchID;
        $data['linkedDocumentCode'] = $DocumentAutoID;
        $data['tasktypeID'] = $tasktypeID;
        $data['taskName'] = $taskDescription;
        $data['taskComment'] = $taskComment;
        $data['taskDate'] = $this->common_data['current_date'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] =  $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];

        $this->db->insert('srp_erp_buyback_tasks_done', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', 'Failed to Update task ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Task Updated Successfully.');

        }
    }

    function load_batch_detail()
    {
        $a = 1;
        $companyID = $this->common_data['company_data']['company_id'];
        $companyFinanceYearID = trim($this->input->post('companyFinanceYearID') ?? '');
        $FinanceYearData = $this->db->query("SELECT YEAR(beginingDate) as year FROM srp_erp_companyfinanceyear WHERE companyFinanceYearID = $companyFinanceYearID")->row_array();
        $FinanceYear = $FinanceYearData['year'];
        $farmID = $this->input->post('farmID');
        if(empty($farmID)){
            $farmer = 0;
        }else{
            $farmer = "'" . implode("', '", $farmID) . "'";
        }
        $batch = $this->db->query("SELECT *, fm.description as farmName, farmSystemCode FROM srp_erp_buyback_batch batch LEFT JOIN srp_erp_buyback_farmmaster fm ON fm.farmID = batch.farmID where batch.companyID = '{$companyID}' AND confirmedYN = '0' AND isclosed = '0' AND batch.farmID IN ($farmer) AND YEAR(batchStartDate) = $FinanceYear")->result_array();

        if(!empty($batch)){
            $totalChicks = 0;
            $totalOutput = 0;
            $totalBalance = 0;
            foreach ($batch as $value) {
                $chicksTotalbatch = $this->db->query("SELECT COALESCE(sum(qty), 0) AS chicksTotal FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 WHERE dpm.companyID = '{$companyID}' AND batchMasterID ={$value['batchMasterID']}")->row_array();
                $balancechicksTotal = $this->db->query("SELECT COALESCE(sum(grnd.noOfBirds), 0) AS balanceChicksTotal FROM srp_erp_buyback_grn grn INNER JOIN srp_erp_buyback_grndetails grnd ON grnd.grnAutoID = grn.grnAutoID WHERE grn.companyID = '{$companyID}' AND confirmedYN = 1 AND approvedYN = 1 AND batchMasterID ={$value['batchMasterID']}")->row_array();
                $mortalityChicks = $this->db->query("SELECT COALESCE(sum(noOfBirds), 0) AS deadChicksTotal FROM srp_erp_buyback_mortalitymaster mm INNER JOIN srp_erp_buyback_mortalitydetails md ON mm.mortalityAutoID = md.mortalityAutoID WHERE  mm.companyID = '{$companyID}' AND batchMasterID ={$value['batchMasterID']}")->row_array();
                $age = chicks_age_dashboard($value['batchMasterID'],'','');

                echo '<tr>';
                echo '<td>' . $a . '</td>';
                echo '<td>' . $value['farmSystemCode'] . ' | ' . $value['farmName'] . '</td>';
                echo '<td>' . $value['batchCode'] . '</td>';
                echo '<td>' . $value['batchStartDate'] . '</td>';
                echo '<td class="text-right">';
                if($age){
                    echo $age;
                } else {
                    echo " - ";
                }
                echo '</td>';
                echo '<td class="text-right">' . $chicksTotalbatch["chicksTotal"] . '</td>';
                echo '<td class="text-right">' . $balancechicksTotal["balanceChicksTotal"] . '</td>';
                echo '<td class="text-right">';
                $Chicks = ($chicksTotalbatch['chicksTotal'] - ($balancechicksTotal['balanceChicksTotal'] + $mortalityChicks['deadChicksTotal']));
                echo $Chicks;
                echo '</td>';
                echo '</tr>';
                $totalChicks += $chicksTotalbatch["chicksTotal"];
                $totalOutput += $balancechicksTotal["balanceChicksTotal"];
                $totalBalance += $Chicks;
                $a ++;
            }
            echo '<hr><tr><td></td><td colspan="4"><strong>Total</strong></td><td class="text-right reporttotal">' . $totalChicks . '</td><td class="text-right reporttotal">' . $totalOutput . '</td><td class="text-right reporttotal">' . $totalBalance . '</td></tr>';
        } else{
            echo '<tr><td colspan="7"><strong><center>No data Found</center></strong></td></tr>';
        }

    }

    function load_farm_detail()
    {
        $a = 1;
        $locationID = $this->input->post('locationID');
        $subLocationID = $this->input->post('subLocationID');
        $subLocation = "";
        $location = "";
        if($subLocationID) {
            $subLocation = " AND fm.subLocationID IN (" . join(',', $subLocationID) . ")";
        }
        if($locationID){
            $location = " AND fm.locationID IN (" . join(',', $locationID) . ")";
        }

        $companyID = $this->common_data['company_data']['company_id'];

        $farmDetails = $this->db->query("SELECT fm.farmID, fm.description, fm.farmSystemCode, fm.farmType, area.description as area, subArea.description as subArea, fm.phoneMobile, fm.isActive
                                                FROM srp_erp_buyback_farmmaster fm
                                                LEFT JOIN srp_erp_buyback_locations area ON area.locationID = fm.locationID
                                                LEFT JOIN srp_erp_buyback_locations subArea ON subArea.locationID = fm.subLocationID
                                                WHERE fm.companyID = $companyID AND fm.isActive = 1 $subLocation $location
                                                GROUP BY fm.farmID")->result_array();
        if(!empty($farmDetails)){
            foreach ($farmDetails as $value) {
                if($value['farmType'] == 1){
                    $farmType = 'Third Party';
                } else{
                    $farmType = 'Own';
                }
                echo '<tr>';
                echo '<td>' . $a . '</td>';
                echo '<td>' . $value['farmSystemCode'] . '</td>';
                echo '<td>' . $value['description'] . '</td>';
                echo '<td>' . $farmType . '</td>';
                echo '<td>' . $value['area'] . '</td>';
                echo '<td>' . $value["subArea"] . '</td>';
                echo '<td>' . $value["phoneMobile"] . '</td>';
                echo '<td class="text-align: center">';
                if($value['isActive'] == 1){
                    echo 'Active';
                } else{
                    echo 'In-active';
                }
                echo '</td>';
                echo '</tr>';
                $a ++;
            }
        } else{
            echo '<tr><td colspan="7"><strong><center>No data Found</center></strong></td></tr>';
        }

    }

    function viewNextInputBatch()
    {
        $date = $this->input->post('date');
        $feedtype = trim($this->input->post('feedtype') ?? '');
        $locationID = $this->input->post('locationID');
        $subLocationID = $this->input->post('subLocationID');

       /* var_dump($date, $feedtype,$locationID, $subLocationID);*/

        $location = '';
        $subLocation = '';
        if($subLocationID) {
            $subLocation = " AND fm.subLocationID IN (" . join(',', $subLocationID) . ")";
        }
        if($locationID){
            $location = " AND fm.locationID IN (" . join(',', $locationID) . ")";
        }
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $format_daysBeforeDay = '';
        $a = 1;
        $grandTotalBalance = 0;
        $batchID = $this->db->query("SELECT batch.batchMasterID,batchCode, fm.farmSystemCode, fm.description as farmName, farmArea.area, farmsubArea.subArea
                                          FROM srp_erp_buyback_batch batch
                                          LEFT JOIN srp_erp_buyback_farmmaster fm ON fm.farmID = batch.farmID 
                                          LEFT JOIN (SELECT description as area, locationID FROM srp_erp_buyback_locations WHERE companyID = {$companyID}) farmArea ON  farmArea.locationID = fm.locationID
                                          LEFT JOIN (SELECT description as subArea, locationID FROM srp_erp_buyback_locations WHERE companyID = {$companyID}) farmsubArea ON  farmsubArea.locationID = fm.subLocationID
                                          where batch.companyID = '{$companyID}' AND isclosed='0' {$location} {$subLocation}
                                          ORDER BY batch.batchMasterID ASC ")->result_array();
        if(!empty($batchID)){
            foreach ($batchID as $val) {
                $nextInputDate = $this->db->query("SELECT DATE_FORMAT(max(dpm.documentDate),'%d-%m-%Y') AS documentDate,sum(qty) AS totalQty FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 2 WHERE batchMasterID = {$val['batchMasterID']}")->row_array();

                $dispatchFirstDate = $this->db->query("SELECT DATE_FORMAT(dpm.documentDate, ' . $convertFormat . ') AS documentDate FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 2 WHERE dpm.companyID = {$companyID} AND batchMasterID = {$val['batchMasterID']} ORDER BY dpm.documentDate ASC")->row_array();

                $chicksTotal = $this->db->query("SELECT COALESCE (sum( dpd.qty ), 0 ) AS chicksTotal FROM srp_erp_buyback_dispatchnote dpm
	                                                    INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID
	                                                    WHERE dpm.confirmedYN = 1 AND dpm.approvedYN = 1 AND dpd.buybackItemType = 1 AND dpm.batchMasterID = {$val['batchMasterID']}")->row_array();

                if (!empty($nextInputDate)) {
                    if ($chicksTotal['chicksTotal'] != 0) {
                        $cumalativeFeed = ($nextInputDate['totalQty'] * 50) / $chicksTotal['chicksTotal'];
                        $cal_nextinputDate = $cumalativeFeed * 1000;
                        $currentAgeCalculation = $this->db->query("SELECT max(age) as currentAge FROM srp_erp_buyback_feedscheduledetail WHERE companyID = {$companyID} AND totalAmount <= {$cal_nextinputDate} ")->row_array();
                        if (!empty($currentAgeCalculation)) {
                            $currentAgeCalculation_days = $currentAgeCalculation['currentAge'];

                            $daysBefore_days = ($currentAgeCalculation_days - 4);
                            $daysBefore_date = strtotime("+ $daysBefore_days day", strtotime($dispatchFirstDate["documentDate"]));
                            $format_daysBeforeDay = date("Y-m-d", $daysBefore_date);
                        }
                    }
                }
                if($date == $format_daysBeforeDay){
                    $age = chicks_age_dashboard($val['batchMasterID'], '', '');
                    $feedTypes = $this->db->query("SELECT feedScheduleID,feedAmount,ft.description as feedName,CONCAT(startDay, ' - ', endDay) as changedDate,buybackFeedtypeID FROM srp_erp_buyback_feedschedulemaster fsm LEFT JOIN srp_erp_buyback_feedtypes ft ON fsm.feedTypeID = ft.buybackFeedtypeID WHERE fsm.companyID = {$companyID} AND fsm.feedScheduleID = {$feedtype}")->row_array();

                    $feedBooster = $this->db->query("SELECT sum(qty) AS booster, dpdr.returnqty as boosterreturn 
FROM srp_erp_buyback_dispatchnote dpm 
LEFT JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID 
LEFT JOIN ( SELECT COALESCE ( sum( dpdr.qty ), 0 ) AS returnqty, dispatchAutoID,dpdr.returnAutoID
FROM srp_erp_buyback_dispatchreturndetails dpdr 
LEFT JOIN srp_erp_buyback_dispatchreturn retun on retun.returnAutoID = dpdr.returnAutoID
WHERE 
approvedYN  = 1
AND confirmedYN = 1
AND dpdr.buybackItemType = 2
AND feedType = {$feedTypes['buybackFeedtypeID']}
GROUP BY dispatchAutoID 
) dpdr ON dpdr.dispatchAutoID = dpd.dispatchAutoID   
WHERE dpm.companyID = {$companyID} AND dpm.confirmedYN = 1 AND dpm.approvedYN = 1  AND buybackItemType = 2 AND feedType = {$feedTypes['buybackFeedtypeID']} AND batchMasterID ={$val['batchMasterID']}")->row_array();

                    if (!empty($feedBooster)) {
                        $feedBooster = $feedBooster['booster'] - $feedBooster['boosterreturn'];
                    }
                    $booster = ($feedTypes["feedAmount"] * $chicksTotal['chicksTotal']) / 50;
                    $balanceFeedType = $booster - $feedBooster;
                    $balanceFeedType = round($balanceFeedType);

                    if($chicksTotal['chicksTotal'] != 0){
                        echo '<tr>';
//                echo '<td>' . $a . '</td>';
                        echo '<td>' . $format_daysBeforeDay . '</td>';
                        echo '<td>' . $val['farmSystemCode'] . ' | ' . $val['farmName'] . '</td>';
                        echo '<td>' . $val['batchCode'] . '</td>';
                        echo '<td>' . $val['area'] . '</td>';
                        echo '<td>' . $val['subArea'] . '</td>';
                        echo '<td class="text-right">' . $age . '</td>';
                        echo '<td>' . $feedTypes['feedName'] . '</td>';
                        echo '<td class="text-right">' . $balanceFeedType . '</td>';
                        echo '</tr>';

                        $grandTotalBalance += $balanceFeedType;
                        $a ++;
                    }
                }
            }
        }
       if($a > 1){
           echo '<hr><tr><td colspan="7" class="text-right"><strong>Total</strong></td><td class="text-right reporttotal">' . $grandTotalBalance . '</td></tr>';
       } else {
           echo '<tr><td colspan="8"><strong><center>No data Found</center></strong></td></tr>';
       }
    }

    function fetch_buyback_item(){
        $companyID = $this->common_data['company_data']['company_id'];
        $itemDetails = $this->db->query("SELECT CONCAT(IM.itemSystemCode,' - ',IM.itemDescription) AS itemName,IM.reorderPoint as point,IM.defaultUnitOfMeasure,
                                                IM.currentStock as currentStock
                                                FROM srp_erp_buyback_itemmaster BIM
                                                LEFT JOIN srp_erp_itemmaster IM ON IM.itemAutoID = BIM.itemAutoID
                                                WHERE BIM.companyID = {$companyID}")->result_array();
        if(!empty($itemDetails)){
            foreach ($itemDetails as $item){
                echo '<tr>';
                echo '<td>' . $item['itemName'] . '</td>';
                echo '<td>' . $item['defaultUnitOfMeasure'] . '</td>';
                echo '<td class="pull-right">';
                if ($item['currentStock'] > $item['point']) {
                    echo "<span class='text-green'>" . $item['currentStock'] . '  ' . $item['defaultUnitOfMeasure'] . "</span>";
                } else {
                    echo "<span class='text-red'>" . $item['currentStock'] . '  ' . $item['defaultUnitOfMeasure'] . "</span>";
                }
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3"><strong><center>No data Found</center></strong></td> </tr>';
        }
    }

    function overdue_payable_farm(){
        $companyID = $this->common_data['company_data']['company_id'];
        $payable = $this->db->query("SELECT fm.farmID as farmID,CONCAT(fm.farmSystemCode,'-',fm.description) as farmerName, cm.CurrencyCode as currency, cm.DecimalPlaces as DecimalPlaces
                                                FROM srp_erp_buyback_farmmaster fm
                                                LEFT JOIN srp_erp_currencymaster cm ON fm.farmerCurrencyID = cm.currencyID
                                                WHERE fm.companyID = {$companyID}")->result_array();

        $totalPayable = 0;
        $totalReceivable = 0;
        foreach ($payable as $var){
            $batch = $this->db->query("SELECT batchMasterID FROM srp_erp_buyback_batch WHERE companyID = {$companyID} AND isclosed = 1 AND farmID = {$var['farmID']} ")->result_array();
            $profitAmount = 0;
            $lossAmount = 0;
            foreach ($batch as $val){
                $wages = wagesPayableAmount($val['batchMasterID'], TRUE);
                $batchProfORLoss = wagesPayableAmount($val['batchMasterID'], FALSE);
                if ($batchProfORLoss['transactionAmount'] > 0) {
                    $profitAmount += $wages['transactionAmount'];
                } else  if ($batchProfORLoss['transactionAmount'] < 0) {
                    $lossAmount += $wages['transactionAmount'];
                }
            }

            if($profitAmount != 0 && $this->input->post('type') == 'payable'){
                echo '<tr>';
                echo '<td>' . $var['farmerName'] . '</td>';
                echo '<td>' . $var['currency'] . '</td>';
                echo '<td class="pull-right">' . number_format($profitAmount , $var['DecimalPlaces']) . '</td>';
                echo '</tr>';
                $totalPayable += $profitAmount;

            } else if($lossAmount != 0 && $this->input->post('type') == 'receivable'){
                echo '<tr>';
                echo '<td>' . $var['farmerName'] . '</td>';
                echo '<td>' . $var['currency'] . '</td>';
                echo '<td class="pull-right">' . number_format($lossAmount, $var['DecimalPlaces']) . '</td>';
                echo '</tr>';
                $totalReceivable += $lossAmount;
            }
        }

        if($this->input->post('type') == 'payable'){
            echo '<tr>';
            echo '<td colspan="2"><b>Total</b></td>';
            echo '<td class="pull-right"><b>' . number_format($totalPayable , $var['DecimalPlaces']) . '</b></td>';
            echo '</tr>';
        } else if($this->input->post('type') == 'receivable'){
            echo '<tr>';
            echo '<td colspan="2"><b>Total</b></td>';
            echo '<td class="pull-right"><b>' . number_format($totalReceivable , $var['DecimalPlaces']) . '</b></td>';
            echo '</tr>';
        }
    }



}












