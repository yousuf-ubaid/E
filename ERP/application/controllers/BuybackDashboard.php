<?php

class BuybackDashboard extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('BuybackDashboard_model');
        $this->load->helper('buyback_helper');
    }

    function buybackDashSum_Count()
    {
        echo json_encode($this->BuybackDashboard_model->buybackDashSum_Count());
    }

    function fetch_filter_date()
    {
        echo json_encode($this->BuybackDashboard_model->fetch_filter_date());
    }

    function buybackDashboard_Data()
    {
        $companyID = $this->common_data['company_data']['company_id'];

       // $data['theme'] = $this->input->post('themeSec');
        $data['theme'] = 1;
        $companyFinanceYearID = $this->input->post('FinanceYear');
        $FinanceYearData = $this->db->query("SELECT YEAR(beginingDate) as year FROM srp_erp_companyfinanceyear WHERE companyFinanceYearID = $companyFinanceYearID")->row_array();
        $FinanceYear = $FinanceYearData['year'];
        $financeyear_period = $this->input->post('financeyear_period');
        $period = "";
        if ($financeyear_period) {
            $FinancePeriodData = $this->db->query("SELECT dateFrom,dateTo FROM srp_erp_companyfinanceperiod WHERE companyFinancePeriodID = $financeyear_period")->row_array();
            $period .= " AND ( documentDate BETWEEN '" . $FinancePeriodData['dateFrom'] . "' AND '" . $FinancePeriodData['dateTo'] . " ')";
        }
        $data['month'] = load_dashboard_monthTitle($financeyear_period);

        // Batch Status //
        $input_chicks = $this->db->query("SELECT COALESCE ( sum( qty ), 0 ) AS inputChicks 
                                                FROM srp_erp_buyback_dispatchnote dpm 
                                                INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 
                                                where dpm.companyID  = {$companyID} AND YEAR(documentDate) = {$FinanceYear} {$period}")->row_array();
        $output_chicks = $this->db->query("SELECT sum( noOfBirds ) AS outputChicks
                                    FROM `srp_erp_buyback_itemledger` 
                                    WHERE `companyID` = $companyID 
                                        AND `documentCode` = 'BBGRN'
                                        AND YEAR(documentDate) = $FinanceYear
                                        {$period}")->row_array();
        $mortality_chicks = $this->db->query("SELECT COALESCE
                                            ( sum( noOfBirds ), 0 ) AS mortalChicks 
                                        FROM srp_erp_buyback_mortalitymaster mm
                                        INNER JOIN srp_erp_buyback_mortalitydetails md ON mm.mortalityAutoID = md.mortalityAutoID 
                                        WHERE mm.companyID  = $companyID  AND YEAR(documentDate) = $FinanceYear $period")->row_array();

        $data['input_chicks'] = $input_chicks['inputChicks'];
        $data['output_chicks'] = $output_chicks['outputChicks'];
        $data['mortality_chicks'] = $mortality_chicks['mortalChicks'];
        $data['totalChicksCount'] = $data['input_chicks'] + $data['output_chicks'] + $data['mortality_chicks'];
        // End of Batch Status //

        // Feed Verses Weight Chart //
        $data['sactterFeildReport'] = $this->db->query("SELECT documentSystemCode,numberOfVisit, srp_erp_buyback_farmervisitreportdetails.avgBodyWeight, srp_erp_buyback_farmervisitreportdetails.avgFeedperBird, srp_erp_buyback_batch.batchCode, srp_erp_buyback_farmmaster.description
                                                          FROM srp_erp_buyback_farmervisitreport
                                                          INNER JOIN (
                                                          SELECT batchMasterID, MAX(numberOfVisit) as visitNo FROM srp_erp_buyback_farmervisitreport WHERE srp_erp_buyback_farmervisitreport.confirmedYN = 1 GROUP BY batchMasterID
                                                          )fvr ON fvr.batchMasterID = srp_erp_buyback_farmervisitreport.batchMasterID AND fvr.visitNo = srp_erp_buyback_farmervisitreport.numberOfVisit
                                                          LEFT JOIN srp_erp_buyback_batch ON srp_erp_buyback_farmervisitreport.batchMasterID = srp_erp_buyback_batch.batchMasterID
                                                          LEFT JOIN srp_erp_buyback_farmmaster ON srp_erp_buyback_farmmaster.farmID = srp_erp_buyback_batch.farmID
                                                          LEFT JOIN srp_erp_buyback_farmervisitreportdetails ON srp_erp_buyback_farmervisitreportdetails.farmerVisitMasterID = srp_erp_buyback_farmervisitreport.farmerVisitID
                                                          WHERE srp_erp_buyback_batch.companyID = $companyID AND srp_erp_buyback_farmervisitreport.confirmedYN = 1 AND srp_erp_buyback_batch.isclosed = 0
                                                           AND YEAR(srp_erp_buyback_farmervisitreport.documentDate) = $FinanceYear
                                                          GROUP BY srp_erp_buyback_farmervisitreport.farmerVisitID")->result_array();

        $Plotline = $this->db->query("SELECT MAX(feedscheduledetailID) as feedscheduledetailID, MAX(totalAmount) as feed, MAX(maxBodyWeight) as weight FROM srp_erp_buyback_feedscheduledetail WHERE companyID = $companyID")->row_array();
        $data['feedPlotline'] = $Plotline['feed'] / 1000;
        $data['weightPlotline'] = $Plotline['weight'] / 1000;

        // End of Feed Verses Weight Chart //

        // column Chart //
        $data['columnChart'] = $this->BuybackDashboard_model->monthlyChartDetails($FinanceYear);
        $data['columnMortal'] = $data['columnChart']['columnMortal'];
        $data['columnChicks'] = $data['columnChart']['columnChicks'];
        $data['columnLiveBirds'] = $data['columnChart']['columnLiveBirds'];
        // End of column Chart //

        // Area Chart //
        $data['AreaChart'] = $this->BuybackDashboard_model->yearProfitDetails($FinanceYear);
        $data['areaThisYear'] = $data['AreaChart']['thisYear'];
        $data['areaLastYear'] = $data['AreaChart']['lastYear'];
        // End of Area Chart //

        // calendar //
        $data['feedTypes'] = $this->db->query("SELECT feedScheduleID,feedAmount,ft.description as feedName,CONCAT(startDay, ' - ', endDay) as changedDate,buybackFeedtypeID FROM srp_erp_buyback_feedschedulemaster fsm LEFT JOIN srp_erp_buyback_feedtypes ft ON fsm.feedTypeID = ft.buybackFeedtypeID WHERE fsm.companyID = {$companyID} ORDER BY feedScheduleID ASC")->result_array();
        // End Calender //

        // FCR Data //
        $FinanceYearData = $this->db->query("SELECT YEAR(beginingDate) as year, MONTH(beginingDate) as month, DAY(beginingDate) as day FROM srp_erp_companyfinanceyear WHERE companyID = $companyID AND isCurrent = 1")->row_array();
        $period = "";
        $periodMortal = "";
        $index = 0;
        $data_fcr = array();
        $data_mortality = array();
        for ($a = 1; $a <= 3; $a++) {
            if ($a == 1){
                $period .= " AND ( YEAR(srp_erp_buyback_itemledger.documentDate) = '" . $FinanceYearData['year'] . " ')";
            } elseif ($a == 2){
                $period .= " AND ( MONTH(srp_erp_buyback_itemledger.documentDate) = '" . $FinanceYearData['month'] . " ')";
            }else{
                $period .= " AND ( DAY(srp_erp_buyback_itemledger.documentDate) = '" . $FinanceYearData['day'] . " ')";
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
                                                    AND `buybackItemType` = 2 
                                                    AND `documentCode` = 'BBDPN' 
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
            $data_fcr[$index++] = number_format($fcrdata, 2);
        }
         $data['feedRate'] =$data_fcr;
        // End OF FCR Data //

        // Mortality percentage //
        for ($a = 1; $a <= 3; $a++) {
            if ($a == 1){
                $periodMortal .= " AND ( YEAR(srp_erp_buyback_mortalitymaster.documentDate) = '" . $FinanceYearData['year'] . " ')";
                $period .= " AND ( YEAR(srp_erp_buyback_itemledger.documentDate) = '" . $FinanceYearData['year'] . " ')";
            } elseif ($a == 2){
                $periodMortal .= " AND ( MONTH(srp_erp_buyback_mortalitymaster.documentDate) = '" . $FinanceYearData['month'] . " ')";
                $period .= " AND ( YEAR(srp_erp_buyback_itemledger.documentDate) = '" . $FinanceYearData['month'] . " ')";
            }else{
                $periodMortal .= " AND ( DAY(srp_erp_buyback_mortalitymaster.documentDate) = '" . $FinanceYearData['day'] . " ')";
                $period .= " AND ( YEAR(srp_erp_buyback_itemledger.documentDate) = '" . $FinanceYearData['day'] . " ')";
            }

            $chicksTotal = $this->db->query("SELECT sum( transactionQTY ) AS chicksTotal FROM srp_erp_buyback_itemledger 
                                              LEFT JOIN srp_erp_buyback_batch ON srp_erp_buyback_batch.batchMasterID = srp_erp_buyback_itemledger.batchID 
                                              WHERE srp_erp_buyback_itemledger.companyID = $companyID 
                                                AND srp_erp_buyback_batch.isclosed = 1 
                                                AND srp_erp_buyback_itemledger.documentCode = 'BBDPN' 
                                                AND srp_erp_buyback_itemledger.buybackItemType = 1 
                                                $period")->row_array();


            $MortalChickTotal = $this->db->query("SELECT sum( noOfBirds ) AS MortalChicksTotal FROM srp_erp_buyback_mortalitymaster 
                                              INNER JOIN srp_erp_buyback_mortalitydetails ON srp_erp_buyback_mortalitydetails.mortalityAutoID = srp_erp_buyback_mortalitymaster.mortalityAutoID 
                                              WHERE srp_erp_buyback_mortalitymaster.companyID = $companyID 
                                                AND srp_erp_buyback_mortalitymaster.confirmedYN = 1 $periodMortal")->row_array();

            if(!empty($chicksTotal['chicksTotal'])){
                $mortalPer = ($MortalChickTotal['MortalChicksTotal'] / $chicksTotal['chicksTotal'])*100;
                $mortalPer = number_format($mortalPer, 2);
            } else{
                $mortalPer = 0;
            }
            $data_mortality[$index++] = $mortalPer;
            $data['MortalityPercentage'] =$data_mortality;
        }
        // End Of Mortality percentage //

        $qry = "SELECT batch.batchMasterID, wipamt.workinprogressamount as workinprogressamount  FROM srp_erp_buyback_batch batch 
LEFT JOIN srp_erp_chartofaccounts c1 ON c1.GLAutoID = batch.WIPGLAutoID
LEFT JOIN (SELECT batchMasterID,confirmedYN,approvedYN FROM srp_erp_buyback_dispatchnotedetails dpd INNER JOIN srp_erp_buyback_dispatchnote dpm ON dpm.dispatchAutoID = dpd.dispatchAutoID AND buybackItemType = 1 WHERE dpm.confirmedYN = 1 AND dpm.approvedYN = 1 GROUP BY batchMasterID) chicksTotaltbl ON chicksTotaltbl.batchMasterID = batch.batchMasterID 
LEFT JOIN (SELECT sum( dpd.totalActualCost ) AS workinprogressamount,dpm.dispatchAutoID,batchMasterID FROM srp_erp_buyback_dispatchnotedetails dpd INNER JOIN srp_erp_buyback_dispatchnote dpm ON dpm.dispatchAutoID = dpd.dispatchAutoID GROUP BY batchMasterID) wipamt ON wipamt.batchMasterID = batch.batchMasterID 
WHERE batch.companyID = $companyID AND chicksTotaltbl.confirmedYN = 1 AND chicksTotaltbl.approvedYN = 1 AND batch.isclosed = 0 GROUP BY batch.batchMasterID";
        $output = $this->db->query($qry)->result_array();

        $WIPAmount = 0;
        foreach ($output as $var){
            $WIPAmount += $var['workinprogressamount'];
        }
        $data['WIPAmount'] = number_format($WIPAmount, 2);

        $this->load->view('system/buyback/ajax/load_saf_buyback_dashboardData', $data);
    }

    function fetch_FarmLog()
    {
        $ageFrom = $this->input->post('ageFrom');
        $ageTo = $this->input->post('ageTo');
        json_encode($this->BuybackDashboard_model->FarmLogData($ageFrom, $ageTo));
    }

    function feedScheduleCalenderData(){
        $feedUpTo = $this->input->post('feedUpTo');
        $this->form_validation->set_rules('feedUpTo', 'Notice ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
             json_encode($this->BuybackDashboard_model->feedScheduleCalendar($feedUpTo));
        }
    }

    public function allCalenderEvents()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $batchID = $this->db->query("SELECT batchMasterID,batchCode FROM srp_erp_buyback_batch where companyID = '{$companyID}' AND isclosed='0' ORDER BY batchMasterID ASC ")->result_array();
        $date = array();
        $databatch = array();
        foreach ($batchID as $val) {
            $nextInputDate = $this->db->query("SELECT DATE_FORMAT(max(dpm.documentDate),'%d-%m-%Y') AS documentDate,sum(qty) AS totalQty FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 2 WHERE batchMasterID ={$val['batchMasterID']}")->row_array();

            $dispatchFirstDate = $this->db->query("SELECT DATE_FORMAT(dpm.documentDate, ' . $convertFormat . ') AS documentDate FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 2 WHERE dpm.companyID = {$companyID} AND batchMasterID ={$val['batchMasterID']} ORDER BY dpm.documentDate ASC")->row_array();

            $chicksTotal = $this->db->query("SELECT COALESCE (sum( dpd.qty ), 0 ) AS chicksTotal FROM srp_erp_buyback_dispatchnote dpm
                                        	INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID
                                        	WHERE dpm.confirmedYN = 1 AND dpm.approvedYN = 1 AND dpm.batchMasterID = {$val['batchMasterID']} AND dpd.buybackItemType = 1")->row_array();

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

                        $date[] = $format_daysBeforeDay;
                        $databatch[] = array(
                            'NextFeedDate' => $format_daysBeforeDay,
                            'batchMasterID' => $val['batchMasterID'],
                            'chicksTotal' => $chicksTotal['chicksTotal'],
                        );
                    }
                }
            }
        }

        $formatDate = array_unique($date);
        $feedTypes = $this->db->query("SELECT feedScheduleID,feedAmount,ft.description as feedName,CONCAT(startDay, ' - ', endDay) as changedDate,buybackFeedtypeID FROM srp_erp_buyback_feedschedulemaster fsm LEFT JOIN srp_erp_buyback_feedtypes ft ON fsm.feedTypeID = ft.buybackFeedtypeID WHERE fsm.companyID = {$companyID} ORDER BY feedScheduleID ASC")->result_array();
        foreach ($formatDate as $date){
            $feedSum = [];
            foreach ($databatch as $value){
                if ($date == $value['NextFeedDate']){

                    if (!empty($feedTypes)) {
                        $event_array = array();

                        foreach ($feedTypes as $feed) {

                            $feedBooster = $this->db->query("SELECT sum(qty) AS booster, dpdr.returnqty as boosterreturn
 FROM srp_erp_buyback_dispatchnote dpm LEFT JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID LEFT JOIN ( SELECT COALESCE ( sum( dpdr.qty ), 0 ) AS returnqty, dispatchAutoID,dpdr.returnAutoID
FROM srp_erp_buyback_dispatchreturndetails dpdr 
LEFT JOIN srp_erp_buyback_dispatchreturn retun on retun.returnAutoID = dpdr.returnAutoID
WHERE confirmedYN = 1
AND approvedYN  = 1
AND  dpdr.buybackItemType = 2
AND feedType = {$feed['buybackFeedtypeID']}
GROUP BY dispatchAutoID 
) dpdr ON dpdr.dispatchAutoID = dpd.dispatchAutoID   
WHERE dpm.companyID = {$companyID} AND dpm.confirmedYN = 1 AND dpm.approvedYN = 1 AND buybackItemType = 2 AND feedType = {$feed['buybackFeedtypeID']} AND batchMasterID ={$value['batchMasterID']}")->row_array();

                            if (!empty($feedBooster)) {
                                $feedBooster = $feedBooster['booster'] - $feedBooster['boosterreturn'];
                            }
                            $booster = ($feed["feedAmount"] * $value['chicksTotal']) / 50;

                            $balanceFeedType = $booster - $feedBooster;

                            $feedTypeID = $feed['buybackFeedtypeID'];
                            if (array_key_exists($feedTypeID, $feedSum)) {
                                if($balanceFeedType > 0)
                                {
                                    $feedSum[$feedTypeID]['balanceFeedType'] = $feedSum[$feedTypeID]['balanceFeedType'] + round($balanceFeedType);
                                }
                            } else {
                                if($balanceFeedType > 0)
                                {
                                    $feedSum[$feedTypeID]['balanceFeedType'] = round($balanceFeedType);

                                }
                            }
                        }
                    }
                }
            }
            foreach ($feedTypes as $row) {
          /*      if(!($feedSum[$row['feedScheduleID']]['balanceFeedType'])){
                    $feedSum[$row['feedScheduleID']]['balanceFeedType'] = 0;
                }*/
                if(empty($feedSum[$row['buybackFeedtypeID']]['balanceFeedType'])){
                    $feedSum[$row['buybackFeedtypeID']]['balanceFeedType'] = 0;
                }
                if( $row['buybackFeedtypeID']  == 1){
                    $color = '#685D79';
                }elseif( $row['buybackFeedtypeID']  == 2) {
                    $color = '#AB6C82';
                }elseif( $row['buybackFeedtypeID']  == 3) {
                    $color = '#D8737F';
                }elseif( $row['buybackFeedtypeID']  == 4) {
                    $color = '#FCBB60';
                }

                $event_array2[] = array(
                    'id' => $value['batchMasterID'],
                    'title' => $row['feedName'] . ' - ' . $feedSum[$row['buybackFeedtypeID']]['balanceFeedType'],
                    'start' => $date,
                    'end' => $date,
                    'color' => $color,
                    'fetchdate' => $date,
                    'feedtype' => $row['feedScheduleID'],
                );
            }
        }
        $arr = array_merge($event_array2);
        echo json_encode($arr);
    }

    function viewNextInputBatch()
    {
        $this->form_validation->set_rules("date", 'date', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->BuybackDashboard_model->viewNextInputBatch());
        }
    }

    function fetchBatchProfitLoss(){
        echo json_encode($this->BuybackDashboard_model->fetchBatchProfitLossData());
    }

    function fetch_buyback_item(){
        json_encode($this->BuybackDashboard_model->fetch_buyback_item());
    }

    function load_todayDoList()
    {
        $data['TodoDate'] = $this->input->post('TodoDate');
        $data['type'] = 'html';
        $data['details'] = $this->BuybackDashboard_model->fetchToDoListDetails();
        $data['pendingTasks'] = $this->BuybackDashboard_model->fetchPendingTasksToDo($data['TodoDate']);
        $this->load->view('system/buyback/ajax/load_dashboard_todo_list', $data);
    }

    function load_todayDoList_pdf()
    {
        $data['TodoDate'] = $this->input->post('TodoDate');
        $data['type'] = 'pdf';
        $data['details'] = $this->BuybackDashboard_model->fetchToDoListDetails();
        $html = $this->load->view('system/buyback/ajax/load_dashboard_todo_list_pdf', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function fetch_linkDocument_DropDown()
    {
        $data_arr = array();
        $tasktypeID = $this->input->post('tasktypeID');
        $batch = $this->input->post('batch');
        $companyID = $this->common_data['company_data']['company_id'];

        $docu = "SELECT DocumentCode FROM srp_erp_buyback_tasktypes_master WHERE companyID = {$companyID} AND tasktypeID = {$tasktypeID}";
        $documentType = $this->db->query($docu)->row_array();
        if(!empty($documentType['DocumentCode']))
        {
            if($documentType['DocumentCode'] == "BBFVR")
            {
                $Master = $this->db->query("SELECT farmerVisitID as DocumentAutoID, documentSystemCode FROM srp_erp_buyback_farmervisitreport WHERE companyID = $companyID AND confirmedYN = 1 AND batchMasterID = $batch ORDER BY farmerVisitID")->result_array();
            }
            if($documentType['DocumentCode'] == "BBDPN")
            {
                $Master = $this->db->query("SELECT dispatchAutoID as DocumentAutoID, documentSystemCode FROM srp_erp_buyback_dispatchnote WHERE companyID = $companyID AND approvedYN = 1 AND documentID = 'BBDPN' AND batchMasterID = $batch ORDER BY dispatchAutoID")->result_array();
            }
            if($documentType['DocumentCode'] == "BBGRN")
            {
                $Master = $this->db->query("SELECT grnAutoID as DocumentAutoID, documentSystemCode FROM srp_erp_buyback_grn WHERE companyID = $companyID AND approvedYN = 1 AND documentID = 'BBGRN' AND batchMasterID = $batch ORDER BY grnAutoID")->result_array();
            }
            if($documentType['DocumentCode'] == "BBRV")
            {
                $Master = $this->db->query("SELECT pvMasterAutoID as DocumentAutoID, documentSystemCode FROM srp_erp_buyback_paymentvouchermaster WHERE companyID = $companyID AND approvedYN = 1 AND documentID = 'BBRV' AND (BatchID = $batch OR BatchID = 0) ORDER BY pvMasterAutoID")->result_array();
            }
            if($documentType['DocumentCode'] == "BBPV")
            {
                $Master = $this->db->query("SELECT pvMasterAutoID as DocumentAutoID, documentSystemCode FROM srp_erp_buyback_paymentvouchermaster WHERE companyID = $companyID AND approvedYN = 1 AND documentID = 'BBPV' AND (BatchID = $batch OR BatchID = 0) ORDER BY pvMasterAutoID")->result_array();
            }
            if($documentType['DocumentCode'] == "BBSV")
            {
                $Master = $this->db->query("SELECT pvMasterAutoID as DocumentAutoID, documentSystemCode FROM srp_erp_buyback_paymentvouchermaster WHERE companyID = $companyID AND approvedYN = 1 AND documentID = 'BBSV' AND (BatchID = $batch OR BatchID = 0) ORDER BY pvMasterAutoID")->result_array();
            }
            if($documentType['DocumentCode'] == "BBDR")
            {
                $Master = $this->db->query("SELECT returnAutoID as DocumentAutoID, documentSystemCode FROM srp_erp_buyback_dispatchreturn WHERE companyID = $companyID AND approvedYN = 1 AND documentID = 'BBDR' AND batchMasterID = $batch ORDER BY returnAutoID")->result_array();
            }

            $data_arr = array('' => 'Select Document');
            if (!empty($Master)) {
                foreach ($Master as $row) {
                    $data_arr[trim($row['documentSystemCode'] ?? '')] = trim($row['documentSystemCode'] ?? '');
                }
            }
            echo form_dropdown('DocumentAutoID', $data_arr, '', 'class="form-control select2 linkDocumentDiv" id="DocumentAutoID"');
        } else {
            echo form_dropdown('DocumentAutoID', 'No Document Type Assigned', '', 'class="form-control select2 linkDocumentDiv" id="DocumentAutoID" disabled');
        }
    }

    function Save_TaskDone()
    {
        $this->form_validation->set_rules("tasktypeID", 'Task ID', 'required');
        $this->form_validation->set_rules("feedscheduledetailID", 'Feed ID', 'required');
        $this->form_validation->set_rules("farmMasID", 'Farm ID', 'required');
        $this->form_validation->set_rules("batchID", 'Batch ID', 'required');
        $this->form_validation->set_rules("taskDescription", 'Task Type', 'required');
        $this->form_validation->set_rules("taskComment", 'Comment', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
                echo json_encode($this->BuybackDashboard_model->Save_TaskDone());
        }
    }

    function load_batch_detailView()
    {
        $this->form_validation->set_rules("companyFinanceYearID", 'Financial Year', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->BuybackDashboard_model->load_batch_detail());
        }
    }
    function load_farm_detailView()
    {
        $this->form_validation->set_rules("companyFinanceYearID", 'Financial Year', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->BuybackDashboard_model->load_farm_detail());
        }
    }

    function fetch_farmModal_sublocationDropdown()
    {
        $data_arr = array();
        $locationID = $this->input->post('locationid');
        $modalType = $this->input->post('modalType');
        $comapnyid = $this->common_data['company_data']['company_id'];
        $where = " ";
        if (!empty($locationID)) {
            $filtersublocation = join(',', $locationID);
            $where = " AND (masterID IN ($filtersublocation) OR masterID  IS NULL OR masterID  = '')";
        }

        $location = $this->db->query("SELECT locationID,description FROM srp_erp_buyback_locations WHERE companyID = $comapnyid $where AND masterID!=0")->result_array();
        if (!empty($location)) {
            foreach ($location as $row) {
                $data_arr[trim($row['locationID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        if($modalType == 'calander'){
            echo form_dropdown('cal_subLocationID[]', $data_arr, '', 'class="form-control" id="cal_subLocationID" onchange="viewEvent(\'\',\'\')" multiple="" ');
        } else {
            echo form_dropdown('subLocationID[]', $data_arr, '', 'class="form-control" id="filter_sublocation" onchange="Farm_View_model()" multiple="" ');
        }
    }

    function fetch_overdue_payable_farm(){
        json_encode($this->BuybackDashboard_model->overdue_payable_farm());
    }

}

