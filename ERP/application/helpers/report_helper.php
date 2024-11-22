<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('fetch_group_segment')) {
    function fetch_group_segment($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_groupsegment');
        $CI->db->where('status', 1);
        $CI->db->where('groupID', current_companyID());
        $CI->db->group_by('segmentID');
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            $data_arr = array('' => 'Select Segment');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                if ($id) {
                    $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                } else {
                    $data_arr[trim($row['segmentID'] ?? '') . '|' . trim($row['segmentCode'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }

            }
        }

        return $data_arr;
    }
}

if (!function_exists('itemLedgerDocumentID')) {
    function itemLedgerDocumentID($id = FALSE, $state = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select('srp_erp_itemledger.documentID as documentID,srp_erp_documentcodes.document as document');
        $CI->db->from('srp_erp_itemledger');
        $CI->db->join('srp_erp_documentcodes ', 'srp_erp_documentcodes.documentID = srp_erp_itemledger.documentID');
        $CI->db->where('srp_erp_itemledger.companyID', current_companyID());
        $CI->db->group_by('srp_erp_itemledger.documentID');
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
           // $data_arr = array('' => 'Select Document ID');
            $data_arr = [];
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr['"'.$row['documentID'].'"'] = trim($row['documentID'] ?? '') . ' | ' . trim($row['document'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('generalLedgerDocumentID')) {
    function generalLedgerDocumentID($id = FALSE, $state = TRUE)
    {
        $CI =& get_instance();
        $CI->db->select('srp_erp_generalledger.documentCode as documentID,srp_erp_documentcodes.document as document');
        $CI->db->from('srp_erp_generalledger');
        $CI->db->join('srp_erp_documentcodes ', 'srp_erp_documentcodes.documentID = srp_erp_generalledger.documentCode');
        $CI->db->where('srp_erp_generalledger.companyID', current_companyID());
        $CI->db->group_by('srp_erp_generalledger.documentCode');
        $data = $CI->db->get()->result_array();
        if ($state == TRUE) {
            // $data_arr = array('' => 'Select Document ID');
            $data_arr = [];
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr['"'.$row['documentID'].'"'] = trim($row['documentID'] ?? '') . ' | ' . trim($row['document'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('getCustomReportFormats')) {
    function getCustomReportFormats($id = FALSE, $state = TRUE)
    {
        $CI =& get_instance();
        $data_arr = array(''=>'Select Templates','8' => 'Report 1');
        return $data_arr;
    }
}

if (!function_exists('getCustomCategoryForReport')) {
    function getCustomCategoryForReport($report_id,$gl_category_arr)
    {
        $data  = array();

        //Add company id check
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_mis_report_config_rows');
        $CI->db->where('srp_erp_mis_report_config_rows.report_id',$report_id);
        $CI->db->order_by('srp_erp_mis_report_config_rows.sort_order','ASC');
        $report_rows = $CI->db->get()->result_array();

        foreach($report_rows as $report_row){

            if($report_row['header_type1'] == 1){
                $report_row['chart_of_accounts'] = get_ChartofAccounts_Category_Report($report_row['id'],$gl_category_arr);
            }else{
                $report_row['plus_minus'] = get_config_rows_plus_minus($report_row['id']);
            }

            // unique category id is needed
            $data[$report_row['sort_order']][$report_row['header_type2']][$report_row['cat_description']] = $report_row; 
        }

        return $data;
    }
}


if (!function_exists('load_template_fm_ytd_lyd_statement_report')) {
    function load_template_fm_ytd_lyd_statement_report($temMasterID,$output,$from,$to){

        $fromdate=date("Y-m",strtotime($from));
      
        $todate=date("Y-m", strtotime($to));

        $year = date("Y");
        $previousyear = $year -1;

        $lmfromdate = date('m', strtotime($from));
        
        $lfromdate =$previousyear . '-' . $lmfromdate;
      
        $lmtodate=date("m", strtotime($to));
        $ltodate =$year . '-' . $lmtodate;

      
        $CI =& get_instance();

        $masterID = 0;
        $returnData = '';
     
        $companyID = current_companyID();
        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID',$temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID',$companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

        foreach ($data as $row){
            $templateID = $row['detID'];

            if($row['itemType'] == 2){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td  colspan="14"><span class="td-main-headernew>
                <i id="sample" class="fa fa-minus-square"></i><strong>';
                $returnData .= $row['description'].'</strong></span></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder,masterID
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID}")->result_array();
 

   

                
               foreach ($subData as $sub_row){
                   $detID = $sub_row['detID'];
                   $temdetID = $sub_row['detID'];
                   $dPlace=2;
                   if($sub_row['itemType'] == 1){ /*Sub category*/
                     
                    $returnData .= '<tr class="hoverTr">';
                        $returnData .= '<td class="sub1 description_td_rpt">
                        <span class="subhoverheadTr">
                        <i id="subcat" class="fa fa-plus-square"></i><strong> 
                        '.$sub_row['description'].'</span><strong></td><tr>';


                $glData = $CI->db->query("SELECT Distinct det.glAutoID,chAcc.masterAutoID,(leg.transactionAmount * -1) trAmount,
                chAcc.GLDescription as glData
                FROM srp_erp_companyreporttemplatelinks det
                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID                                
                WHERE templateDetailID = {$detID}
                and DATE_FORMAT(leg.documentDate,'%Y-%m')>='$fromdate' 
                and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$todate'and 
                leg.transactionAmount!=0  group by leg.GLAutoID ORDER BY sortOrder")->result_array();
   
          
    
                $groupsbtotal=0;
            foreach ($glData as $gl_row){

               $glAutoID = $gl_row['glAutoID'];
                //$masterAutoID=$gl_row['masterAutoID'];
                $returnData .= '<tr class="subhoverTr">';
                $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';

                $trAmount = $CI->db->query("
                SELECT sum((leg.transactionAmount * -1)) trAmount,chAcc.GLDescription FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
WHERE   templateDetailID = {$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')>='$fromdate' 
                and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$todate' and det.glAutoID={$glAutoID} 
                group by templateDetailID,det.GLAutoID")->row('trAmount');

                $trAmount = (empty($trAmount))? 0: $trAmount;


                $ltrAmount = $CI->db->query("
                SELECT sum((leg.transactionAmount * -1)) ltrAmount,chAcc.GLDescription FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
WHERE   templateDetailID = {$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')>='$lfromdate' 
                and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$ltodate' and det.glAutoID={$glAutoID} 
                group by templateDetailID,det.GLAutoID")->row('ltrAmount');
                $ltrAmount = (empty($ltrAmount))? 0: $ltrAmount;

                //print_r($CI->db->last_query()); 
                //exit();

               

   $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
   <a href="#" class="drill-down-cursor">'.number_format($trAmount,$dPlace).'</a></td>';

   $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
   <a href="#" class="drill-down-cursor">'.number_format($ltrAmount,$dPlace).'</a></td>';

            }
         
                   }
                }


            }

    }
    return $returnData;
}
}













if (!function_exists('load_template_fm_ytd_statement_report')) {
    function load_template_fm_ytd_statement_report($temMasterID,$output,$from,$to){

        $fromdate=date("Y-m",strtotime($from));
      
        $todate=date("Y-m", strtotime($to));
      
        $CI =& get_instance();

        $masterID = 0;
        $returnData = '';
     
        $companyID = current_companyID();
        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID',$temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID',$companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

        foreach ($data as $row){
            $templateID = $row['detID'];

            if($row['itemType'] == 2){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td  colspan="14"><span class="td-main-headernew>
                <i id="sample" class="fa fa-minus-square"></i><strong>';
                $returnData .= $row['description'].'</strong></span></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder,masterID
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID}")->result_array();
 

   

                
               foreach ($subData as $sub_row){
                   $detID = $sub_row['detID'];
                   $temdetID = $sub_row['detID'];
                   $dPlace=2;
                   if($sub_row['itemType'] == 1){ /*Sub category*/
                     
                    $returnData .= '<tr class="hoverTr">';
                        $returnData .= '<td class="sub1 description_td_rpt">
                        <span class="subhoverheadTr">
                        <i id="subcat" class="fa fa-plus-square"></i><strong> 
                        '.$sub_row['description'].'</span><strong></td><tr>';





                   /* $glData = $CI->db->query("SELECT sum((leg.transactionAmount * -1)) trAmount,chAcc.GLDescription FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                    JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                    WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')>='2023-04' 
                                            and DATE_FORMAT(leg.documentDate,'%Y-%m')<='2024-03' and det.glAutoID=3682
                    group by templateDetailID   ORDER BY sortOrder")->result_array();*/


                $glData = $CI->db->query("SELECT Distinct det.glAutoID,chAcc.masterAutoID,(leg.transactionAmount * -1) trAmount,
                chAcc.GLDescription as glData
                FROM srp_erp_companyreporttemplatelinks det
                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID                                
                WHERE templateDetailID = {$detID}
                and DATE_FORMAT(leg.documentDate,'%Y-%m')>='$fromdate' 
                and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$todate'and 
                leg.transactionAmount!=0  group by leg.GLAutoID ORDER BY sortOrder")->result_array();
   
          
    
                $groupsbtotal=0;
            foreach ($glData as $gl_row){

               $glAutoID = $gl_row['glAutoID'];
                //$masterAutoID=$gl_row['masterAutoID'];
                $returnData .= '<tr class="subhoverTr">';
                $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';

                $trAmount = $CI->db->query("
                SELECT sum((leg.transactionAmount * -1)) trAmount,chAcc.GLDescription FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
WHERE   templateDetailID = {$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')>='$fromdate' 
                and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$todate' and det.glAutoID={$glAutoID} 
                group by templateDetailID,det.GLAutoID")->row('trAmount');

                $trAmount = (empty($trAmount))? 0: $trAmount;

                $groupsbtotal+=round($trAmount, $dPlace);

   $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
   <a href="#" class="drill-down-cursor">'.number_format($trAmount,$dPlace).'</a></td>';



            }
            if($groupsbtotal!='0.00')
            {
            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
            <a href="#" class="drill-down-cursor">'.number_format($groupsbtotal,$dPlace).'</a></td>';
            $returnData .= '</tr>';
                   }

                   }
                }


            }

    }
    return $returnData;
}
}


if (!function_exists('load_template_fm_ytd_budget_statement_report')) {
    function load_template_fm_ytd_budget_statement_report($temMasterID,$output,$from,$to){

        $fromdate=date("Y-m",strtotime($from));
      
        $todate=date("Y-m", strtotime($to));
      
        $CI =& get_instance();

        $masterID = 0;
        $returnData = '';
     
        $companyID = current_companyID();
        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID',$temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID',$companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

        foreach ($data as $row){
            $templateID = $row['detID'];

            if($row['itemType'] == 2){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td  colspan="14"><span class="td-main-headernew>
                <i id="sample" class="fa fa-minus-square"></i><strong>';
                $returnData .= $row['description'].'</strong></span></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder,masterID
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID}")->result_array();
 

   

                
               foreach ($subData as $sub_row){
                   $detID = $sub_row['detID'];
                   $temdetID = $sub_row['detID'];
                   $dPlace=2;
                   if($sub_row['itemType'] == 1){ /*Sub category*/
                     
                    $returnData .= '<tr class="hoverTr">';
                        $returnData .= '<td class="sub1 description_td_rpt">
                        <span class="subhoverheadTr">
                        <i id="subcat" class="fa fa-plus-square"></i><strong> 
                        '.$sub_row['description'].'</span><strong></td><tr>';





                   /* $glData = $CI->db->query("SELECT sum((leg.transactionAmount * -1)) trAmount,chAcc.GLDescription FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                    JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                    WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')>='2023-04' 
                                            and DATE_FORMAT(leg.documentDate,'%Y-%m')<='2024-03' and det.glAutoID=3682
                    group by templateDetailID   ORDER BY sortOrder")->result_array();*/


                $glData = $CI->db->query("SELECT Distinct det.glAutoID,chAcc.masterAutoID,(leg.transactionAmount * -1) trAmount,
                chAcc.GLDescription as glData
                FROM srp_erp_companyreporttemplatelinks det
                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID                                
                WHERE templateDetailID = {$detID}
                and DATE_FORMAT(leg.documentDate,'%Y-%m')>='$fromdate' 
                and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$todate' group by leg.GLAutoID ORDER BY sortOrder")->result_array();
   
          
    
                $groupsbtotal=0;$bdgroupsbtotal=0;
            foreach ($glData as $gl_row){

               $glAutoID = $gl_row['glAutoID'];
                //$masterAutoID=$gl_row['masterAutoID'];
                $returnData .= '<tr class="subhoverTr">';
                $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';

                $trAmount = $CI->db->query("
                SELECT sum((leg.transactionAmount * -1)) trAmount,chAcc.GLDescription FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
WHERE   templateDetailID = {$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')>='$fromdate' 
                and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$todate' and det.glAutoID={$glAutoID} 
                group by templateDetailID,det.GLAutoID")->row('trAmount');

                $bdAmount = $CI->db->query("SELECT  sum(ifnull(companyLocalAmount,0)) AS bdamount FROM srp_erp_budgetdetail INNER JOIN srp_erp_budgetmaster ON srp_erp_budgetdetail.budgetAutoID = srp_erp_budgetmaster.budgetAutoID 
                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_budgetdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID
                JOIN srp_erp_companyreporttemplatelinks det ON srp_erp_chartofaccounts.GLAutoID = det.glAutoID 
                WHERE srp_erp_budgetdetail.companyID ='$companyID' AND CONCAT( budgetYear, '-', LPAD( budgetMonth, 2, 0 ) ) >='$fromdate' and
                 CONCAT( budgetYear, '-', LPAD( budgetMonth, 2, 0 ) ) <='$todate' 
                 AND srp_erp_budgetmaster.approvedYN = 1  AND srp_erp_chartofaccounts.masterCategory = 'PL' and templateDetailID ={$detID} and det.glAutoID={$glAutoID}")->row('bdamount');





                $trAmount = (empty($trAmount))? 0: $trAmount;
                $bdAmount = (empty($bdAmount))? 0: $bdAmount;

                $groupsbtotal+=round($trAmount, $dPlace);
                $bdgroupsbtotal+=round($bdAmount, $dPlace);

                $variance=$trAmount-$bdAmount;
                $varianceper=($trAmount != 0 ? round(($variance / $trAmount) * 100, 2) : 0); 

   $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
   <a href="#" class="drill-down-cursor">'.number_format($trAmount,$dPlace).'</a></td>';

   $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
   <a href="#" class="drill-down-cursor">'.number_format($bdAmount,$dPlace).'</a></td>';
   $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
   <a href="#" class="drill-down-cursor">'.number_format($variance,$dPlace).'</a></td>';
   $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
   <a href="#" class="drill-down-cursor">'.$varianceper.'%</a></td>';


            }
         
                   }
                }


            }

    }
    return $returnData;
}
}

if (!function_exists('load_template_budget_month_ytdltd_fm_statement_report')) {
    function load_template_budget_month_ytdltd_fm_statement_report($month,$temMasterID,$output){
        $CI =& get_instance();

        $masterID = 0;
        $returnData = '';
     
        $companyID = current_companyID();
        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID',$temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID',$companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

           $k=1;
          
        foreach ($data as $row){
            $templateID = $row['detID'];

            if($row['itemType'] == 2){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td  colspan="14"><span class="td-main-headernew'.$k.'" onclick="generateexpaned'.$k.'()">
                <i id="sample'.$k.'" class="fa fa-minus-square"></i><strong>';
                $returnData .= $row['description'].'</strong></span></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder,masterID
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID}")->result_array();
 
                    $subDatagroup = $CI->db->query("SELECT detID, description, itemType, sortOrder
                    FROM srp_erp_companyreporttemplatedetails det
                    WHERE masterID = {$templateID} and itemType!=3  ORDER BY sortOrder")->result_array();


 if(!empty($subDatagroup)){
     $where_in_arraysub = array_column($subDatagroup, 'detID');   
     $where_insub = implode(',', $where_in_arraysub);
    }
    

                 // print_r($CI->db->last_query());  
                      // exit();
                     $m=1;
                foreach ($subData as $sub_row){
                    $detID = $sub_row['detID'];
                    $temdetID = $sub_row['detID'];
                    $dPlace=2;
                    if($sub_row['itemType'] == 1){ /*Sub category*/
                        $returnData .= '<tr class="hoverTr'.$k.'">';
                        $returnData .= '<td class="sub1 description_td_rpt">
                        <span class="subhoverheadTr'.$k.''.$m.'" onclick="generatesubcategory(\'' . $k . '\',\'' .  $m . '\')">
                        <i id="subcat'.$k.''.$m.'" class="fa fa-plus-square"></i><strong> 
                        '.$sub_row['description'].'</span><strong></td>';

                        $j = 1;
                        $thisTot1 = 0; $thisTot2=0;
                        while($j < 13) {
                          
                            $trAmount = 0; $bdamount=0;      
                            foreach ($month as $key => $value2) 
                            {
                                $key2 = substr($key,5);
                              
                                  $year = date("Y");
                                  $previousyear = $year -1;
                          
                                  $lfromdate =$previousyear . '-' . $key2;



                                $j++; 

                                $trAmount = $CI->db->query("SELECT sum((leg.transactionAmount * -1)) trAmount FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                        JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                        WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key'
                        group by templateDetailID")->row('trAmount');
                    // print_r($CI->db->last_query()); 
                    // exit();


                                $bdamount =$CI->db->query("SELECT sum((leg.transactionAmount * -1)) bdamount  FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                                JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                                WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$lfromdate'
                                group by templateDetailID")->row('bdamount');

                      //print_r($CI->db->last_query());  

                        $trAmount = (empty($trAmount))? 0: $trAmount;
                        $thisTot1 += round($trAmount, $dPlace);

                        $bdamount = (empty($bdamount))? 0: $bdamount;
                        $thisTot2 += round($bdamount, $dPlace);
                               
                            $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align:right;"><strong>'.number_format($trAmount,$dPlace).'</strong></td>';
                            $returnData .= '<td class="sub2 amount_td_rpt budgetsubhoverheadTr'.$k.''.$m.'" style="text-align:right;"><strong>'.number_format($bdamount,$dPlace).'</strong></td>';
                         
                            }

                            
                        }
                        $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align: right"><strong>'.number_format($thisTot1,$dPlace).'</strong></td>';
                        $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align: right"><strong>'.number_format($thisTot2,$dPlace).'</strong></td>';
                        $returnData .= '</tr>';







                        $glData = $CI->db->query("SELECT det.glAutoID,chAcc.masterAutoID,
                                CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                FROM srp_erp_companyreporttemplatelinks det
                                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID                                
                                WHERE templateDetailID = {$detID}    ORDER BY sortOrder")->result_array();
                  
                  //print_r($CI->db->last_query());  
                       //exit();
                      
                
                            $groupsbtotal=0;
                            $groupbudgettotal=0;
                        foreach ($glData as $gl_row){

                           $glAutoID = $gl_row['glAutoID'];
                            //$masterAutoID=$gl_row['masterAutoID'];
                            $returnData .= '<tr class="subhoverTr'.$k.''.$m.'">';
                            $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';
                               
                            
                            $i = 1; $thisTot = 0;
                            $dPlace=2;
                            
                            while($i < 3) {
                                $trAmount = 0;
                                         
                                foreach ($month as $key => $value2) 
                                {
                                    /*$trAmount = $CI->db->query("SELECT sum((transactionAmount * -1)) trAmount FROM srp_erp_generalledger 
                                                   WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key' and GLType!='0'
                                                   group by GLAutoID
                                                   
                                                   ")->row('trAmount');*/
                                                   $key2 = substr($key,5);
                              
                                                   $year = date("Y");
                                                   $previousyear = $year -1;
                                           
                                                   $lfromdate =$previousyear . '-' . $key2;

                                       
                                                   $trAmount = $CI->db->query("SELECT sum((leg.companyLocalAmount * -1)) trAmount FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                        JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                        WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key' and det.GLAutoID={$glAutoID}
                        group by templateDetailID,det.GLAutoID")->row('trAmount');


$companyLocalAmount = $CI->db->query("SELECT sum((leg.companyLocalAmount * -1)) companyLocalAmount FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$lfromdate' and det.GLAutoID={$glAutoID}
group by templateDetailID,det.GLAutoID")->row('companyLocalAmount');


                                 
                                $companyLocalAmount=0.00;

                 

                                //print_R($companyLocalAmount);
                                    //exit();
                                    //$companyLocalAmount='00.0';
                                   $companyLocalAmount = (empty($companyLocalAmount))? 0: $companyLocalAmount;




                                    $gltype=$CI->db->query("SELECT GLType FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'
                                    
                                    ")->row('GLType');

                                    if($gltype=='PLI')
                                    {
                                        $gltype='PL';
                                    }
                                    else if($gltype=='PLE')
                                    {
                                        $gltype='PL';
                                    }


                                    $gldescription=$CI->db->query("SELECT GLDescription FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'

                                    ")->row('GLDescription');

                                    /*$companylocalcurrency=$CI->db->query("SELECT companyLocalCurrency FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'
                                    ")->row('companyLocalCurrency');*/    
                             $companylocalcurrency='companyLocalAmount';


                                                 
                                                  //$trAmount = $trAmountdata[0]['trAmount'];
                                    $trAmount = (empty($trAmount))? 0: $trAmount;

                                  
                                   

                              $groupsbtotal+=round($trAmount, $dPlace);
                              $groupbudgettotal+=round($companyLocalAmount, $dPlace);
                                
                                $thisTot += round($trAmount, $dPlace);
                                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $glAutoID . '\',\'' .  $gltype . '\',\'' . $gldescription . '\',\'' .  $companylocalcurrency . '\',\'' . $key . '\')">'.number_format($trAmount,$dPlace).'</a></td>';
                                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($companyLocalAmount,$dPlace).'</td>';
                                
                              
                                
                             
                                $i++;
                            }
                           
                           
                            
                        }
                       
                            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($thisTot,$dPlace).'</td>';
                            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($groupbudgettotal,$dPlace).'</td>';
                            $returnData .= '</tr>';

                        }


                       // $returnData .= '<tr class="hoverTr">';
                        //$returnData .= '<td class="sub1 description_td_rpt"><strong> Total '.$sub_row['description'].'</strong></td>';
                       
                    }
                

                    if($sub_row['itemType'] == 3){ /*Group*/

                             
                        $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$detID}
                                            AND subCategory IN ({$where_insub})
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();



                        $where_in = '';

                        if(!empty($group_glData)){
                            $where_in_array = array_column($group_glData, 'glAutoID');
                            $where_in = implode(',', $where_in_array);
                        }
                



                        $returnData .= '<tr class="hoverTr'.$k.'">';
                        $returnData .= '<td class="sub1 description_td_rpt"><strong>'.$sub_row['description'].'</strong></td>';

                        $i = 1; $thisTot = 0;$thisTotbudget=0;
                        while($i < 13) {
                            $trAmount = 0;
                            foreach ($month as $key => $value2) 
                            {

                                $key2 = substr($key,5);
                              
                                $year = date("Y");
                                $previousyear = $year -1;
                        
                                $lfromdate =$previousyear . '-' . $key2;

                            if($where_in != '') {
                               
                                   

                                    $trAmount = $CI->db->query("SELECT sum(leg.transactionAmount * -1) trAmount 
                                    FROM srp_erp_companyreporttemplatelinks det 
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                                    JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                                    WHERE  templateDetailID IN({$where_insub}) and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key' 
                                    ;")->row('trAmount');



                                    $trAmount = (empty($trAmount) || $trAmount == null) ? 0 : $trAmount;

                                    $bdamounttotal = $CI->db->query("SELECT sum(leg.transactionAmount * -1) bdamounttotal 
                                    FROM srp_erp_companyreporttemplatelinks det 
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                                    JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                                    WHERE  templateDetailID IN({$where_insub}) and DATE_FORMAT(leg.documentDate,'%Y-%m')='$lfromdate' 
                                    ;")->row('bdamounttotal');

                                    $bdamounttotal = (empty($bdamounttotal) || $bdamounttotal == null) ? 0 : $bdamounttotal;
                                  

                               
                            }
                            $thisTot += round($trAmount, $dPlace);
                            $thisTotbudget += round($bdamounttotal, $dPlace);
                            $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmount,$dPlace).'</strong></td>';
                            $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($bdamounttotal,$dPlace).'</strong></td>';

                            $i++;

                        }
                            
                        }

                        $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTot,$dPlace).'</strong></td>';
                        $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTotbudget,$dPlace).'</strong></td>';
                        $returnData .= '</tr>';
                    }
                      
                   // }
                   $m=$m+1;
                }
            }
            else if($row['itemType'] == 3){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td><span class="td-main-headernew'.$k.'">
                <strong>';
                $returnData .= $row['description'].'</strong></span></td>';


                $a = 1; $thisTotprofit = 0;$thisTotbudprofit = 0;
                while($a < 13) {
                    $trAmountpl = 0; $trAmountplbud = 0;
                    foreach ($month as $key => $value2) 
                    {

                        $key2 = substr($key,5);
                              
                        $year = date("Y");
                        $previousyear = $year -1;
                
                        $lfromdate =$previousyear . '-' . $key2;
                       
                           

                            $trAmountpl = $CI->db->query("SELECT sum(leg.transactionAmount * -1) trAmount 
                            FROM srp_erp_companyreporttemplatelinks det 
                            JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                            JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                            WHERE  templateMasterID={$temMasterID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key' 
                            ;")->row('trAmount');


                            $trAmountplbud = $CI->db->query("SELECT sum(leg.transactionAmount * -1) trAmount 
                            FROM srp_erp_companyreporttemplatelinks det 
                            JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                            JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                            WHERE  templateMasterID={$temMasterID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$lfromdate' 
                            ;")->row('trAmount');

                            

                            $trAmountpl = (empty($trAmountpl) || $trAmountpl == null) ? 0 : $trAmountpl;
                            $trAmountplbud = (empty($trAmountplbud) || $trAmountplbud == null) ? 0 : $trAmountplbud;

                       
                    
                    $thisTotprofit += round($trAmountpl, $dPlace);
                    $thisTotbudprofit  += round($trAmountplbud, $dPlace);
                    $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmountpl,$dPlace).'</strong></td>';
                    $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmountplbud,$dPlace).'</strong></td>';

                    $a++;

                }
                    
                }

                $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTotprofit,$dPlace).'</strong></td>';
                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($thisTotbudprofit,$dPlace).'</td>';
                $returnData .= '</tr>';


            }
            
           
            $k=$k+1;
        }

        return $returnData;

    }
}








if (!function_exists('load_template_segment_fm_statement_report')) {
    function load_template_segment_fm_statement_report($month,$temMasterID,$output,$segment,$from,$to){
        $CI =& get_instance();

        $masterID = 0;
        $returnData = '';

        $fromdate=date("Y-m-d",strtotime($from));
      
        $todate=date("Y-m-d", strtotime($to));
     
        $companyID = current_companyID();
        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID',$temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID',$companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

        //print_R($segment);
       // exit();

       

           $k=1;
          
        foreach ($data as $row){
            $templateID = $row['detID'];

            if($row['itemType'] == 2){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td  colspan="14"><span class="td-main-headernew'.$k.'" onclick="generateexpaned'.$k.'()">
                <i id="sample'.$k.'" class="fa fa-minus-square"></i><strong>';
                $returnData .= $row['description'].'</strong></span></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder,masterID
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID}")->result_array();
 
                    $subDatagroup = $CI->db->query("SELECT detID, description, itemType, sortOrder
                    FROM srp_erp_companyreporttemplatedetails det
                    WHERE masterID = {$templateID} and itemType!=3  ORDER BY sortOrder")->result_array();


 if(!empty($subDatagroup)){
     $where_in_arraysub = array_column($subDatagroup, 'detID');   
     $where_insub = implode(',', $where_in_arraysub);
    }
    

                 //print_r($CI->db->last_query());  
                      // exit();
                     $m=1;
                foreach ($subData as $sub_row){
                    $detID = $sub_row['detID'];
                    $temdetID = $sub_row['detID'];
                    $dPlace=2;
                    if($sub_row['itemType'] == 1){ /*Sub category*/
                        $returnData .= '<tr class="hoverTr'.$k.'">';
                        $returnData .= '<td class="sub1 description_td_rpt">
                        <span class="subhoverheadTr'.$k.''.$m.'" onclick="generatesubcategory(\'' . $k . '\',\'' .  $m . '\')">
                        <i id="subcat'.$k.''.$m.'" class="fa fa-plus-square"></i><strong> 
                        '.$sub_row['description'].'</span><strong></td>';

                        $j = 1;
                        $thisTot1 = 0;
                        while($j < 13) {
                          
                            $trAmount = 0;       
                            foreach ($segment as $key => $value2) 
                            {
                                $segmentID=$value2['segmentID'];
                              
                                $j++; 

                                $trAmount = $CI->db->query("select sum(leg.transactionAmount * -1) trAmount  
                                FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID
                                JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID   where
                                templateDetailID ={$detID}  and leg.segmentID ={$segmentID}  AND leg.companyID = $companyID
                                and leg.documentDate BETWEEN '$fromdate' AND '$todate'
                                group by templateDetailID")->row('trAmount');



               

                        $trAmount = (empty($trAmount))? 0: $trAmount;
                        $thisTot1 += round($trAmount, $dPlace);
                               
                            $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align:right;"><strong>'.number_format($trAmount,$dPlace).'</strong></td>';
                            }

                            
                        }
                        $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align: right"><strong>'.number_format($thisTot1,$dPlace).'</strong></td>';
                        $returnData .= '</tr>';


                      




                        $glData = $CI->db->query("SELECT det.glAutoID,chAcc.masterAutoID,
                                CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                FROM srp_erp_companyreporttemplatelinks det
                                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID                                
                                WHERE templateDetailID = {$detID}    ORDER BY sortOrder")->result_array();
                  
                  //print_r($CI->db->last_query());  
                       //exit();
                      
                
                            $groupsbtotal=0;
                        foreach ($glData as $gl_row){

                           $glAutoID = $gl_row['glAutoID'];
                            //$masterAutoID=$gl_row['masterAutoID'];
                            $returnData .= '<tr class="subhoverTr'.$k.''.$m.'">';
                            $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';
                               
                            
                            $i = 1; $thisTot = 0;
                            $dPlace=2;
                            
                            while($i < 13) {
                                $trAmount = 0;
                                         
                                foreach ($segment as $key => $value2) 
                                {
                                    $segmentID=$value2['segmentID'];
                                    /*$trAmount = $CI->db->query("SELECT sum((transactionAmount * -1)) trAmount FROM srp_erp_generalledger 
                                                   WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key' and GLType!='0'
                                                   group by GLAutoID
                                                   
                                                   ")->row('trAmount');*/

                                       
                                                   $trAmount = $CI->db->query("select sum(leg.transactionAmount * -1) trAmount  
                                                   FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID
                                                   JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID   where templateDetailID ={$detID} and
                                                   leg.GLAutoID={$glAutoID} and leg.segmentID ={$segmentID}  AND leg.companyID = $companyID
                                                   and leg.documentDate BETWEEN '$fromdate' AND '$todate'")->row('trAmount');


                                                   //$trAmount='0.00';




                                    $gltype=$CI->db->query("SELECT GLType FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and segmentID ={$segmentID}
                                    
                                    ")->row('GLType');

                                    if($gltype=='PLI')
                                    {
                                        $gltype='PL';
                                    }
                                    else if($gltype=='PLE')
                                    {
                                        $gltype='PL';
                                    }


                                    $gldescription=$CI->db->query("SELECT GLDescription FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and segmentID ={$segmentID}

                                    ")->row('GLDescription');

                                    /*$companylocalcurrency=$CI->db->query("SELECT companyLocalCurrency FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'
                                    ")->row('companyLocalCurrency');*/    
                             $companylocalcurrency='companyLocalAmount';


                                                 
                                                  //$trAmount = $trAmountdata[0]['trAmount'];
                                    $trAmount = (empty($trAmount))? 0: $trAmount;
                              $groupsbtotal+=round($trAmount, $dPlace);
                                
                                $thisTot += round($trAmount, $dPlace);
                                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $glAutoID . '\',\'' .  $gltype . '\',\'' . $gldescription . '\',\'' .  $companylocalcurrency . '\',\'' . $key . '\')">'.number_format($trAmount,$dPlace).'</a></td>';
                                
                              
                                
                             
                                $i++;
                            }
                           
                           
                            
                        }
                       
                            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($thisTot,$dPlace).'</td>';
                           
                            $returnData .= '</tr>';

                        }


                       // $returnData .= '<tr class="hoverTr">';
                        //$returnData .= '<td class="sub1 description_td_rpt"><strong> Total '.$sub_row['description'].'</strong></td>';
                       
                    }
                

                    if($sub_row['itemType'] == 3){ /*Group*/

                             
                        $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$detID}
                                            AND subCategory IN ({$where_insub})
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();


               /* $group_glData = $CI->db->query("SELECT  det.glAutoID
                FROM srp_erp_companyreporttemplatelinks det
                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID  
                JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID                               
                WHERE templateDetailID IN ({$where_insub}) ORDER BY sortOrder")->result_array();*/





          //print_r($CI->db->last_query());  
          //exit();


          

                        $where_in = '';

                        if(!empty($group_glData)){
                            $where_in_array = array_column($group_glData, 'glAutoID');
                            $where_in = implode(',', $where_in_array);
                        }
                



                        $returnData .= '<tr class="hoverTr'.$k.'">';
                        $returnData .= '<td class="sub1 description_td_rpt"><strong>'.$sub_row['description'].'</strong></td>';

                        $i = 1; $thisTot = 0;
                        while($i < 13) {
                            $trAmount = 0;
                            foreach ($segment as $key => $value2) 
                            {
                                $segmentID=$value2['segmentID'];

                            if($where_in != '') {
                               
                                    /*$trAmount = $CI->db->query("SELECT sum((transactionAmount * -1)) trAmount FROM srp_erp_generalledger 
                                    WHERE   DATE_FORMAT(documentDate,'%Y-%m')='$key' and GLType!='0'   AND GLAutoID IN ({$where_in})
                                    group by documentMonth;")->row('trAmount');*/

                                    $trAmount = $CI->db->query("SELECT sum(leg.transactionAmount * -1) trAmount 
                                    FROM srp_erp_companyreporttemplatelinks det 
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                                    JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                                    WHERE  templateDetailID IN({$where_insub}) and leg.segmentID ={$segmentID} 
                                    AND leg.companyID = $companyID and leg.documentDate BETWEEN '2023-04-01' AND '2024-03-31' 
                                    ;")->row('trAmount');



                                    $trAmount = (empty($trAmount) || $trAmount == null) ? 0 : $trAmount;
                                  

                               
                            }
                            $thisTot += round($trAmount, $dPlace);
                            $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmount,$dPlace).'</strong></td>';
                           

                            $i++;

                        }
                            
                        }

                        $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTot,$dPlace).'</strong></td>';
                        $returnData .= '</tr>';
                    }
                      
                   // }
                   $m=$m+1;
                }
            }
            else if($row['itemType'] == 3){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td><span class="td-main-headernew'.$k.'">
                <strong>';
                $returnData .= $row['description'].'</strong></span></td>';


                $a = 1; $thisTotprofit = 0;
                while($a < 13) {
                    $trAmountpl = 0;
                    foreach ($segment as $key => $value2) 
                    {
                        $segmentID=$value2['segmentID'];


                            $trAmountpl = $CI->db->query("SELECT sum(leg.transactionAmount * -1) trAmount 
                            FROM srp_erp_companyreporttemplatelinks det 
                            JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                            JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                            WHERE  templateMasterID={$temMasterID} and leg.segmentID ={$segmentID} 
                            AND leg.companyID = $companyID and leg.documentDate BETWEEN '2023-04-01' AND '2024-03-31' 
                            ;")->row('trAmount');

                            //$trAmountpl =200;

                            $trAmountpl = (empty($trAmountpl) || $trAmountpl == null) ? 0 : $trAmountpl;
                          

                       
                    
                    $thisTotprofit += round($trAmountpl, $dPlace);
                    $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmountpl,$dPlace).'</strong></td>';
                   

                    $a++;

                }
                    
                }

                $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTotprofit,$dPlace).'</strong></td>';
                $returnData .= '</tr>';


            }
            
           
            $k=$k+1;
        }

        return $returnData;

    }
}



if (!function_exists('load_template_fm_statement_report')) {
    function load_template_fm_statement_report($month,$temMasterID,$output){
        $CI =& get_instance();

        $masterID = 0;
        $returnData = '';
     
        $companyID = current_companyID();
        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID',$temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID',$companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

           $k=1;
          
        foreach ($data as $row){
            $templateID = $row['detID'];

            if($row['itemType'] == 2){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td  colspan="14"><span class="td-main-headernew'.$k.'" onclick="generateexpaned'.$k.'()">
                <i id="sample'.$k.'" class="fa fa-minus-square"></i><strong>';
                $returnData .= $row['description'].'</strong></span></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder,masterID
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID}")->result_array();
 
                    $subDatagroup = $CI->db->query("SELECT detID, description, itemType, sortOrder
                    FROM srp_erp_companyreporttemplatedetails det
                    WHERE masterID = {$templateID} and itemType!=3  ORDER BY sortOrder")->result_array();


 if(!empty($subDatagroup)){
     $where_in_arraysub = array_column($subDatagroup, 'detID');   
     $where_insub = implode(',', $where_in_arraysub);
    }
    

                 // print_r($CI->db->last_query());  
                      // exit();
                     $m=1;
                foreach ($subData as $sub_row){
                    $detID = $sub_row['detID'];
                    $temdetID = $sub_row['detID'];
                    $dPlace=2;
                    if($sub_row['itemType'] == 1){ /*Sub category*/
                        $returnData .= '<tr class="hoverTr'.$k.'">';
                        $returnData .= '<td class="sub1 description_td_rpt">
                        <span class="subhoverheadTr'.$k.''.$m.'" onclick="generatesubcategory(\'' . $k . '\',\'' .  $m . '\')">
                        <i id="subcat'.$k.''.$m.'" class="fa fa-plus-square"></i><strong> 
                        '.$sub_row['description'].'</span><strong></td>';

                        $j = 1;
                        $thisTot1 = 0;
                        while($j < 13) {
                          
                            $trAmount = 0;       
                            foreach ($month as $key => $value2) 
                            {
                              
                                $j++; 

                                $trAmount = $CI->db->query("SELECT sum((leg.transactionAmount * -1)) trAmount FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                        JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                        WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key'
                        group by templateDetailID")->row('trAmount');

//print_r($CI->db->last_query());  

                        $trAmount = (empty($trAmount))? 0: $trAmount;
                        $thisTot1 += round($trAmount, $dPlace);
                               
                            $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align:right;"><strong>'.number_format($trAmount,$dPlace).'</strong></td>';
                            }

                            
                        }
                        $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align: right"><strong>'.number_format($thisTot1,$dPlace).'</strong></td>';
                        $returnData .= '</tr>';







                        $glData = $CI->db->query("SELECT det.glAutoID,chAcc.masterAutoID,
                                CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                FROM srp_erp_companyreporttemplatelinks det
                                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID                                
                                WHERE templateDetailID = {$detID}    ORDER BY sortOrder")->result_array();
                  
                  //print_r($CI->db->last_query());  
                       //exit();
                      
                
                            $groupsbtotal=0;
                        foreach ($glData as $gl_row){

                           $glAutoID = $gl_row['glAutoID'];
                            //$masterAutoID=$gl_row['masterAutoID'];
                            $returnData .= '<tr class="subhoverTr'.$k.''.$m.'">';
                            $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';
                               
                            
                            $i = 1; $thisTot = 0;
                            $dPlace=2;
                            
                            while($i < 13) {
                                $trAmount = 0;
                                         
                                foreach ($month as $key => $value2) 
                                {
                                    /*$trAmount = $CI->db->query("SELECT sum((transactionAmount * -1)) trAmount FROM srp_erp_generalledger 
                                                   WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key' and GLType!='0'
                                                   group by GLAutoID
                                                   
                                                   ")->row('trAmount');*/

                                       
                                                   $trAmount = $CI->db->query("SELECT sum((leg.transactionAmount * -1)) trAmount FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                        JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                        WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key' and det.GLAutoID={$glAutoID}
                        group by templateDetailID,det.GLAutoID")->row('trAmount');




                                    $gltype=$CI->db->query("SELECT GLType FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'
                                    
                                    ")->row('GLType');

                                    if($gltype=='PLI')
                                    {
                                        $gltype='PL';
                                    }
                                    else if($gltype=='PLE')
                                    {
                                        $gltype='PL';
                                    }


                                    $gldescription=$CI->db->query("SELECT GLDescription FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'

                                    ")->row('GLDescription');

                                    /*$companylocalcurrency=$CI->db->query("SELECT companyLocalCurrency FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'
                                    ")->row('companyLocalCurrency');*/    
                             $companylocalcurrency='companyLocalAmount';


                                                 
                                                  //$trAmount = $trAmountdata[0]['trAmount'];
                                    $trAmount = (empty($trAmount))? 0: $trAmount;
                              $groupsbtotal+=round($trAmount, $dPlace);
                                
                                $thisTot += round($trAmount, $dPlace);
                                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $glAutoID . '\',\'' .  $gltype . '\',\'' . $gldescription . '\',\'' .  $companylocalcurrency . '\',\'' . $key . '\')">'.number_format($trAmount,$dPlace).'</a></td>';
                                
                              
                                
                             
                                $i++;
                            }
                           
                           
                            
                        }
                       
                            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($thisTot,$dPlace).'</td>';
                           
                            $returnData .= '</tr>';

                        }


                       // $returnData .= '<tr class="hoverTr">';
                        //$returnData .= '<td class="sub1 description_td_rpt"><strong> Total '.$sub_row['description'].'</strong></td>';
                       
                    }
                

                    if($sub_row['itemType'] == 3){ /*Group*/

                             
                        $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$detID}
                                            AND subCategory IN ({$where_insub})
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();


               /* $group_glData = $CI->db->query("SELECT  det.glAutoID
                FROM srp_erp_companyreporttemplatelinks det
                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID  
                JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID                               
                WHERE templateDetailID IN ({$where_insub}) ORDER BY sortOrder")->result_array();*/





          //print_r($CI->db->last_query());  
          //exit();


          

                        $where_in = '';

                        if(!empty($group_glData)){
                            $where_in_array = array_column($group_glData, 'glAutoID');
                            $where_in = implode(',', $where_in_array);
                        }
                



                        $returnData .= '<tr class="hoverTr'.$k.'">';
                        $returnData .= '<td class="sub1 description_td_rpt"><strong>'.$sub_row['description'].'</strong></td>';

                        $i = 1; $thisTot = 0;
                        while($i < 13) {
                            $trAmount = 0;
                            foreach ($month as $key => $value2) 
                            {

                            if($where_in != '') {
                               
                                    /*$trAmount = $CI->db->query("SELECT sum((transactionAmount * -1)) trAmount FROM srp_erp_generalledger 
                                    WHERE   DATE_FORMAT(documentDate,'%Y-%m')='$key' and GLType!='0'   AND GLAutoID IN ({$where_in})
                                    group by documentMonth;")->row('trAmount');*/

                                    $trAmount = $CI->db->query("SELECT sum(leg.transactionAmount * -1) trAmount 
                                    FROM srp_erp_companyreporttemplatelinks det 
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                                    JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                                    WHERE  templateDetailID IN({$where_insub}) and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key' 
                                    ;")->row('trAmount');



                                    $trAmount = (empty($trAmount) || $trAmount == null) ? 0 : $trAmount;
                                  

                               
                            }
                            $thisTot += round($trAmount, $dPlace);
                            $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmount,$dPlace).'</strong></td>';
                           

                            $i++;

                        }
                            
                        }

                        $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTot,$dPlace).'</strong></td>';
                        $returnData .= '</tr>';
                    }
                      
                   // }
                   $m=$m+1;
                }
            }
            else if($row['itemType'] == 3){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td><span class="td-main-headernew'.$k.'">
                <strong>';
                $returnData .= $row['description'].'</strong></span></td>';


                $a = 1; $thisTotprofit = 0;
                while($a < 13) {
                    $trAmountpl = 0;
                    foreach ($month as $key => $value2) 
                    {

                    
                       
                           

                            $trAmountpl = $CI->db->query("SELECT sum(leg.transactionAmount * -1) trAmount 
                            FROM srp_erp_companyreporttemplatelinks det 
                            JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                            JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                            WHERE  templateMasterID={$temMasterID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key' 
                            ;")->row('trAmount');

                            //$trAmountpl =200;

                            $trAmountpl = (empty($trAmountpl) || $trAmountpl == null) ? 0 : $trAmountpl;
                          

                       
                    
                    $thisTotprofit += round($trAmountpl, $dPlace);
                    $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmountpl,$dPlace).'</strong></td>';
                   

                    $a++;

                }
                    
                }

                $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTotprofit,$dPlace).'</strong></td>';
                $returnData .= '</tr>';


            }
            
           
            $k=$k+1;
        }

        return $returnData;

    }
}

if (!function_exists('load_template_budget_fm_statement_report')) {
    function load_template_budget_fm_statement_report($month,$temMasterID,$output){
        $CI =& get_instance();

        $masterID = 0;
        $returnData = '';
     
        $companyID = current_companyID();
        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID',$temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID',$companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

           $k=1;
          
        foreach ($data as $row){
            $templateID = $row['detID'];

            if($row['itemType'] == 2){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td  colspan="14"><span class="td-main-headernew'.$k.'" onclick="generateexpaned'.$k.'()">
                <i id="sample'.$k.'" class="fa fa-minus-square"></i><strong>';
                $returnData .= $row['description'].'</strong></span></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder,masterID
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID}")->result_array();
 
                    $subDatagroup = $CI->db->query("SELECT detID, description, itemType, sortOrder
                    FROM srp_erp_companyreporttemplatedetails det
                    WHERE masterID = {$templateID} and itemType!=3  ORDER BY sortOrder")->result_array();


 if(!empty($subDatagroup)){
     $where_in_arraysub = array_column($subDatagroup, 'detID');   
     $where_insub = implode(',', $where_in_arraysub);
    }
    

                 // print_r($CI->db->last_query());  
                      // exit();
                     $m=1;
                foreach ($subData as $sub_row){
                    $detID = $sub_row['detID'];
                    $temdetID = $sub_row['detID'];
                    $dPlace=2;
                    if($sub_row['itemType'] == 1){ /*Sub category*/
                        $returnData .= '<tr class="hoverTr'.$k.'">';
                        $returnData .= '<td class="sub1 description_td_rpt">
                        <span class="subhoverheadTr'.$k.''.$m.'" onclick="generatesubcategory(\'' . $k . '\',\'' .  $m . '\')">
                        <i id="subcat'.$k.''.$m.'" class="fa fa-plus-square"></i><strong> 
                        '.$sub_row['description'].'</span><strong></td>';

                        $j = 1;
                        $thisTot1 = 0; $thisTot2=0;
                        while($j < 13) {
                          
                            $trAmount = 0; $bdamount=0;      
                            foreach ($month as $key => $value2) 
                            {
                              
                                $j++; 

                                $trAmount = $CI->db->query("SELECT sum((leg.transactionAmount * -1)) trAmount FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                        JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                        WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key'
                        group by templateDetailID")->row('trAmount');
                    // print_r($CI->db->last_query()); 
                    // exit();


                                $bdamount = $CI->db->query("SELECT  sum(ifnull(companyLocalAmount,0)) AS bdamount FROM srp_erp_budgetdetail INNER JOIN srp_erp_budgetmaster ON srp_erp_budgetdetail.budgetAutoID = srp_erp_budgetmaster.budgetAutoID 
                                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_budgetdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID
                                JOIN srp_erp_companyreporttemplatelinks det ON srp_erp_chartofaccounts.GLAutoID = det.glAutoID 
                                                   
                               WHERE srp_erp_budgetdetail.companyID =$companyID AND CONCAT( budgetYear, '-', LPAD( budgetMonth, 2, 0 ) ) ='$key' AND srp_erp_budgetmaster.approvedYN = 1  AND srp_erp_chartofaccounts.masterCategory = 'PL' and templateDetailID ={$detID}")->row('bdamount');

//print_r($CI->db->last_query());  

                        $trAmount = (empty($trAmount))? 0: $trAmount;
                        $thisTot1 += round($trAmount, $dPlace);

                        $bdamount = (empty($bdamount))? 0: $bdamount;
                        $thisTot2 += round($bdamount, $dPlace);
                               
                            $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align:right;"><strong>'.number_format($trAmount,$dPlace).'</strong></td>';
                            $returnData .= '<td class="sub2 amount_td_rpt budgetsubhoverheadTr'.$k.''.$m.'" style="text-align:right;"><strong>'.number_format($bdamount,$dPlace).'</strong></td>';
                         
                            }

                            
                        }
                        $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align: right"><strong>'.number_format($thisTot1,$dPlace).'</strong></td>';
                        $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align: right"><strong>'.number_format($thisTot2,$dPlace).'</strong></td>';
                        $returnData .= '</tr>';







                        $glData = $CI->db->query("SELECT det.glAutoID,chAcc.masterAutoID,
                                CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                FROM srp_erp_companyreporttemplatelinks det
                                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID                                
                                WHERE templateDetailID = {$detID}    ORDER BY sortOrder")->result_array();
                  
                  //print_r($CI->db->last_query());  
                       //exit();
                      
                
                            $groupsbtotal=0;
                            $groupbudgettotal=0;
                        foreach ($glData as $gl_row){

                           $glAutoID = $gl_row['glAutoID'];
                            //$masterAutoID=$gl_row['masterAutoID'];
                            $returnData .= '<tr class="subhoverTr'.$k.''.$m.'">';
                            $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';
                               
                            
                            $i = 1; $thisTot = 0;
                            $dPlace=2;
                            
                            while($i < 3) {
                                $trAmount = 0;
                                         
                                foreach ($month as $key => $value2) 
                                {
                                    /*$trAmount = $CI->db->query("SELECT sum((transactionAmount * -1)) trAmount FROM srp_erp_generalledger 
                                                   WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key' and GLType!='0'
                                                   group by GLAutoID
                                                   
                                                   ")->row('trAmount');*/

                                       
                                                   $trAmount = $CI->db->query("SELECT sum((leg.companyLocalAmount * -1)) trAmount FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                        JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                        WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key' and det.GLAutoID={$glAutoID}
                        group by templateDetailID,det.GLAutoID")->row('trAmount');


                                 
                                $companyLocalAmount=$CI->db->query("SELECT ifnull(companyLocalAmount,0) AS companyLocalAmount FROM srp_erp_budgetdetail 
                                INNER JOIN srp_erp_budgetmaster
                                 ON srp_erp_budgetdetail.budgetAutoID = srp_erp_budgetmaster.budgetAutoID 
 LEFT JOIN srp_erp_chartofaccounts ON srp_erp_budgetdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID
WHERE srp_erp_budgetdetail.companyID = $companyID AND CONCAT( budgetYear, '-', LPAD( budgetMonth, 2, 0 ) ) ='$key' AND srp_erp_budgetmaster.approvedYN = 1 AND srp_erp_chartofaccounts.GLAutoID ={$glAutoID} AND srp_erp_chartofaccounts.masterCategory = 'PL'
GROUP BY
srp_erp_chartofaccounts.masterAutoID,
 srp_erp_chartofaccounts.accountCategoryTypeID,
                    srp_erp_chartofaccounts.GLAutoID,
                    srp_erp_chartofaccounts.GLDescription")->row('companyLocalAmount');

                 

                                //print_R($companyLocalAmount);
                                    //exit();
                                    //$companyLocalAmount='00.0';
                                   $companyLocalAmount = (empty($companyLocalAmount))? 0: $companyLocalAmount;




                                    $gltype=$CI->db->query("SELECT GLType FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'
                                    
                                    ")->row('GLType');

                                    if($gltype=='PLI')
                                    {
                                        $gltype='PL';
                                    }
                                    else if($gltype=='PLE')
                                    {
                                        $gltype='PL';
                                    }


                                    $gldescription=$CI->db->query("SELECT GLDescription FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'

                                    ")->row('GLDescription');

                                    /*$companylocalcurrency=$CI->db->query("SELECT companyLocalCurrency FROM srp_erp_generalledger 
                                    WHERE  GLAutoID = {$glAutoID} and DATE_FORMAT(documentDate,'%Y-%m')='$key'
                                    ")->row('companyLocalCurrency');*/    
                             $companylocalcurrency='companyLocalAmount';


                                                 
                                                  //$trAmount = $trAmountdata[0]['trAmount'];
                                    $trAmount = (empty($trAmount))? 0: $trAmount;

                                  
                                   

                              $groupsbtotal+=round($trAmount, $dPlace);
                              $groupbudgettotal+=round($companyLocalAmount, $dPlace);
                                
                                $thisTot += round($trAmount, $dPlace);
                                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $glAutoID . '\',\'' .  $gltype . '\',\'' . $gldescription . '\',\'' .  $companylocalcurrency . '\',\'' . $key . '\')">'.number_format($trAmount,$dPlace).'</a></td>';
                                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($companyLocalAmount,$dPlace).'</td>';
                                
                              
                                
                             
                                $i++;
                            }
                           
                           
                            
                        }
                       
                            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($thisTot,$dPlace).'</td>';
                            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($groupbudgettotal,$dPlace).'</td>';
                            $returnData .= '</tr>';

                        }


                       // $returnData .= '<tr class="hoverTr">';
                        //$returnData .= '<td class="sub1 description_td_rpt"><strong> Total '.$sub_row['description'].'</strong></td>';
                       
                    }
                

                    if($sub_row['itemType'] == 3){ /*Group*/

                             
                        $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$detID}
                                            AND subCategory IN ({$where_insub})
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();


               /* $group_glData = $CI->db->query("SELECT  det.glAutoID
                FROM srp_erp_companyreporttemplatelinks det
                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID  
                JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID                               
                WHERE templateDetailID IN ({$where_insub}) ORDER BY sortOrder")->result_array();*/





          //print_r($CI->db->last_query());  
          //exit();


          

                        $where_in = '';

                        if(!empty($group_glData)){
                            $where_in_array = array_column($group_glData, 'glAutoID');
                            $where_in = implode(',', $where_in_array);
                        }
                



                        $returnData .= '<tr class="hoverTr'.$k.'">';
                        $returnData .= '<td class="sub1 description_td_rpt"><strong>'.$sub_row['description'].'</strong></td>';

                        $i = 1; $thisTot = 0;$thisTotbudget=0;
                        while($i < 13) {
                            $trAmount = 0;
                            foreach ($month as $key => $value2) 
                            {

                            if($where_in != '') {
                               
                                    /*$trAmount = $CI->db->query("SELECT sum((transactionAmount * -1)) trAmount FROM srp_erp_generalledger 
                                    WHERE   DATE_FORMAT(documentDate,'%Y-%m')='$key' and GLType!='0'   AND GLAutoID IN ({$where_in})
                                    group by documentMonth;")->row('trAmount');*/

                                    $trAmount = $CI->db->query("SELECT sum(leg.transactionAmount * -1) trAmount 
                                    FROM srp_erp_companyreporttemplatelinks det 
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                                    JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                                    WHERE  templateDetailID IN({$where_insub}) and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key' 
                                    ;")->row('trAmount');



                                    $trAmount = (empty($trAmount) || $trAmount == null) ? 0 : $trAmount;

                                    $bdamounttotal = $CI->db->query("SELECT sum(ifnull(companyLocalAmount,0)) AS bdamounttotal 
                                    FROM srp_erp_budgetdetail INNER JOIN srp_erp_budgetmaster ON srp_erp_budgetdetail.budgetAutoID = srp_erp_budgetmaster.budgetAutoID 
                                    LEFT JOIN srp_erp_chartofaccounts ON srp_erp_budgetdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                                    JOIN srp_erp_companyreporttemplatelinks det ON srp_erp_chartofaccounts.GLAutoID = det.glAutoID 
                                    WHERE srp_erp_budgetdetail.companyID =$companyID AND CONCAT( budgetYear, '-', LPAD( budgetMonth, 2, 0 ) ) ='$key' 
                                    AND srp_erp_budgetmaster.approvedYN = 1 AND srp_erp_chartofaccounts.masterCategory = 'PL' and templateDetailID IN({$where_insub})
                                    ")->row('bdamounttotal');

                                   //print_R($CI->db->last_query());
                                   //exit();

                                    $bdamounttotal = (empty($bdamounttotal) || $bdamounttotal == null) ? 0 : $bdamounttotal;
                                  

                               
                            }
                            $thisTot += round($trAmount, $dPlace);
                            $thisTotbudget += round($bdamounttotal, $dPlace);
                            $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmount,$dPlace).'</strong></td>';
                            $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($bdamounttotal,$dPlace).'</strong></td>';

                            $i++;

                        }
                            
                        }

                        $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTot,$dPlace).'</strong></td>';
                        $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTotbudget,$dPlace).'</strong></td>';
                        $returnData .= '</tr>';
                    }
                      
                   // }
                   $m=$m+1;
                }
            }
            else if($row['itemType'] == 3){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td><span class="td-main-headernew'.$k.'">
                <strong>';
                $returnData .= $row['description'].'</strong></span></td>';


                $a = 1; $thisTotprofit = 0;$thisTotbudprofit = 0;
                while($a < 13) {
                    $trAmountpl = 0; $trAmountplbud = 0;
                    foreach ($month as $key => $value2) 
                    {

                    
                       
                           

                            $trAmountpl = $CI->db->query("SELECT sum(leg.transactionAmount * -1) trAmount 
                            FROM srp_erp_companyreporttemplatelinks det 
                            JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                            JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                            WHERE  templateMasterID={$temMasterID} and DATE_FORMAT(leg.documentDate,'%Y-%m')='$key' 
                            ;")->row('trAmount');


                            $trAmountplbud = $CI->db->query("SELECT sum(ifnull(companyLocalAmount,0)) AS trAmountplbud FROM srp_erp_budgetdetail INNER JOIN srp_erp_budgetmaster 
                            ON srp_erp_budgetdetail.budgetAutoID = srp_erp_budgetmaster.budgetAutoID 
                            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_budgetdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
                            JOIN srp_erp_companyreporttemplatelinks det ON srp_erp_chartofaccounts.GLAutoID = det.glAutoID
                             WHERE srp_erp_budgetdetail.companyID =$companyID AND CONCAT( budgetYear, '-', LPAD( budgetMonth, 2, 0 ) ) ='$key'
                             AND srp_erp_budgetmaster.approvedYN = 1 AND srp_erp_chartofaccounts.masterCategory = 'PL' and templateMasterID={$temMasterID} 
                            ;")->row('trAmountplbud');

                            //$trAmountpl =200;

                            $trAmountpl = (empty($trAmountpl) || $trAmountpl == null) ? 0 : $trAmountpl;
                            $trAmountplbud = (empty($trAmountplbud) || $trAmountplbud == null) ? 0 : $trAmountplbud;

                       
                    
                    $thisTotprofit += round($trAmountpl, $dPlace);
                    $thisTotbudprofit  += round($trAmountplbud, $dPlace);
                    $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmountpl,$dPlace).'</strong></td>';
                    $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmountplbud,$dPlace).'</strong></td>';

                    $a++;

                }
                    
                }

                $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTotprofit,$dPlace).'</strong></td>';
                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($thisTotbudprofit,$dPlace).'</td>';
                $returnData .= '</tr>';


            }
            
           
            $k=$k+1;
        }

        return $returnData;

    }
}


if (!function_exists('load_template_balancesheet_ytd_statement_report')) {
    function load_template_balancesheet_ytd_statement_report($temMasterID,$output,$fromdate,$todate){

        $todatenew=date("Y-m", strtotime($todate));
       

        $CI =& get_instance();

        $masterID = 0;
        $returnData = '';
     
        $companyID = current_companyID();
        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID',$temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID',$companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

        foreach ($data as $row){
            $templateID = $row['detID'];

            if($row['itemType'] == 2){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                $returnData .= '<td  colspan="14"><span class="td-main-headernew>
                <i id="sample" class="fa fa-minus-square"></i><strong>';
                $returnData .= $row['description'].'</strong></span></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder,masterID
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID}")->result_array();
 

   

                
               foreach ($subData as $sub_row){
                   $detID = $sub_row['detID'];
                   $temdetID = $sub_row['detID'];
                   $dPlace=2;
                   if($sub_row['itemType'] == 1){ /*Sub category*/
                     
                    $returnData .= '<tr class="hoverTr">';
                        $returnData .= '<td class="sub1 description_td_rpt">
                        <span class="subhoverheadTr">
                        <i id="subcat"></i><strong> 
                        '.$sub_row['description'].'</span><strong></td><tr>';



                        if($sub_row['description']!='Equity')
                        {
                        $glData = $CI->db->query("SELECT det.glAutoID,chAcc.masterAutoID,
                                CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                FROM srp_erp_companyreporttemplatelinks det
                                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID                                
                                WHERE templateDetailID = {$detID}  ORDER BY sortOrder")->result_array();

                              
                        }
                        else
                        {
                            $glData = $CI->db->query("SELECT 7 as sortOrder,'Retained Earnings' as glData,'LIABILITIES' AS mainCategory,
                            'Equity' as subCategory,'Equity' as subsubCategory,
                            '-' as masterCategory,'Retained Earnings' as glAutoID
                            FROM
                            srp_erp_generalledger limit 1")->result_array();

                        }
   
          
    
                $groupsbtotal=0;
            foreach ($glData as $gl_row){

               $glAutoID = $gl_row['glAutoID'];
                //$masterAutoID=$gl_row['masterAutoID'];
                $returnData .= '<tr class="subhoverTr">';
                $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';

         



                if($glAutoID!='Retained Earnings')
                {
                 

               $gltype=$CI->db->query("SELECT GLType FROM srp_erp_generalledger 
               WHERE  GLAutoID = {$glAutoID} 
               
               ")->row('GLType');

               if($gltype=='BSA')
               {
                   $gltype='BS';

                   $trAmount = $CI->db->query("select sum((srp_erp_generalledger.companyLocalAmount)) companyLocalAmount 
                   FROM srp_erp_generalledger  where srp_erp_generalledger.GLAutoID = {$glAutoID}
                   and DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') >='$fromdate' 
                   and DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m')<='$todate'
                    group by srp_erp_generalledger.GLAutoID
                   ")->row('companyLocalAmount');

               }
               else if($gltype=='PLE')
               {
                   $gltype='PL';

                   $trAmount = $CI->db->query("select sum((srp_erp_generalledger.companyLocalAmount * -1)) companyLocalAmount 
                   FROM srp_erp_generalledger  where srp_erp_generalledger.GLAutoID = {$glAutoID}
                   and DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') >='$fromdate'
                   and DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m')<='$todate' 
                    group by srp_erp_generalledger.GLAutoID
                   ")->row('companyLocalAmount');
               }

               else if($gltype=='BSL')
               {
                   $gltype='BS';

                   $trAmount = $CI->db->query("select sum((srp_erp_generalledger.companyLocalAmount * -1)) companyLocalAmount 
                   FROM srp_erp_generalledger  where srp_erp_generalledger.GLAutoID = {$glAutoID}
                   and DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') >='$fromdate'
                   and DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m')<='$todate' 
                    group by srp_erp_generalledger.GLAutoID
                   ")->row('companyLocalAmount');


               }



               $gldescription=$CI->db->query("SELECT GLDescription FROM srp_erp_generalledger 
               WHERE  GLAutoID = {$glAutoID} 

               ")->row('GLDescription');


                }
                else{
                  $trAmount = $CI->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount) as 'companyLocalAmount' from srp_erp_generalledger
                  INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'BS' 
                  AND srp_erp_chartofaccounts.companyID = $companyID LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID) LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
                  WHERE 
                   DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m')<='$todatenew' 
                   AND srp_erp_generalledger.companyID = $companyID
                  ")->row('companyLocalAmount');

                }


                $trAmount = (empty($trAmount))? 0: $trAmount;

                $groupsbtotal+=round($trAmount, $dPlace);

   $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
   <a href="#" class="drill-down-cursor">'.number_format($trAmount,$dPlace).'</a></td>';



            }
            if($groupsbtotal!='0.00')
            {
            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: center">
            <a href="#" class="drill-down-cursor">'.number_format($groupsbtotal,$dPlace).'</a></td>';
            $returnData .= '</tr>';
                   }

                   }
                }


            }

    }
    return $returnData;
}
}



if (!function_exists('load_balance_template_fm_statement_report')) {
    function load_balance_template_fm_statement_report($month,$temMasterID,$output,$asof){
        $CI =& get_instance();
       
        $fromdate=date("Y-m-d", strtotime($asof) );
        

        $masterID = 0;
        $returnData = '';
    

        $companyID = current_companyID();
        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID',$temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID',$companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();
      

       /*$reportcheck = $CI->db->query("select  count(Distinct det.glAutoID) CountGl  from srp_erp_companyreporttemplatelinks det
       JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
       JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
        WHERE det.templateMasterID = $temMasterID and det.companyID=$companyID")->result_array();
       $reportval=$reportcheck[0]['CountGl'];*/

      


        $dPlace=2;  $k=1;
        foreach ($data as $row){
            $templateID = $row['detID'];

            if($row['itemType'] == 2){
                $returnData .= '<tr>';
                /*class="mini-header description_td_rpt"*/
                //$returnData .= '<td  colspan="14"><span class="td-main-header"><i class="fa fa-minus-square"></i>';
                $returnData .= '<td  colspan="14"><span class="td-main-header'.$k.'" onclick="generatebsexpaned'.$k.'()">
                <i id="sample'.$k.'" class="fa fa-plus-square"></i><strong>';
                $returnData .= $row['description'].'</strong></span></td></tr>';

              

               




                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID} ORDER BY sortOrder")->result_array();


                $subDatagroup = $CI->db->query("SELECT detID, description, itemType, sortOrder
                FROM srp_erp_companyreporttemplatedetails det
                WHERE masterID = {$templateID} and itemType!=3  ORDER BY sortOrder")->result_array();


                if(!empty($subDatagroup)){
                $where_in_arraysub = array_column($subDatagroup, 'detID');   
                $where_insub = implode(',', $where_in_arraysub);
                }
              


                    $m=1;
                foreach ($subData as $sub_row){
                    $detID = $sub_row['detID'];

                    if($sub_row['itemType'] == 1){ /*Sub category*/
                        $returnData .= '<tr class="hoverTr'.$k.'">';
                            
                        $returnData .= '<td class="sub1 description_td_rpt">
                        <span class="subhoverheadTr'.$k.''.$m.'" onclick="generatesubcategory( \'' . $k . '\',\'' .  $m . '\')">
                        <i id="subcat'.$k.''.$m.'" class="fa fa-plus-square"></i>
                        <strong>'.$sub_row['description'].'</strong></td>';

                        
                       

                        $j = 1;
                        $thisTot1 = 0;
                        while($j < 13) {
                          
                            $trAmount = 0;       
                            foreach ($month as $key => $value2) 
                            {
                              
                                $j++; 

                              
                                if($sub_row['description']!='Equity')
                                {
                                    if(($row['description']=='ASSETS')OR($row['description']=='Assets'))
                                    {
                           $trAmount = $CI->db->query("SELECT sum((leg.companyLocalAmount)) companyLocalAmount FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                           JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                           WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$key'
                           group by templateDetailID")->row('companyLocalAmount');
                                    }
                                    else{

                                        $trAmount = $CI->db->query("SELECT sum((leg.companyLocalAmount * -1)) companyLocalAmount FROM srp_erp_companyreporttemplatelinks det JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                                        JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                                        WHERE templateDetailID ={$detID} and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$key'
                                          group by templateDetailID")->row('companyLocalAmount');

                                    }


                        $trAmount = (empty($trAmount))? 0: $trAmount;

                                }
                                else{

                                    $trAmount = $CI->db->query("SELECT SUM(if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <= '$key',srp_erp_generalledger.companyLocalAmount,0) ) as 'companyLocalAmount' from srp_erp_generalledger
                                                    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'BS' 
                                                    AND srp_erp_chartofaccounts.companyID = $companyID LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID) LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
                                                    WHERE srp_erp_generalledger.documentDate <= '$fromdate' AND srp_erp_generalledger.companyID = $companyID
                                                    ")->row('companyLocalAmount');
                                                    //$trAmount = 0;
                                   
                                }
                        $thisTot1 += round($trAmount, $dPlace);
                               
                            $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align:right;"><strong>'.number_format($trAmount,$dPlace).'</strong></td>';
                            }

                            
                        }
                        $returnData .= '<td class="sub2 amount_td_rpt subhoverheadTr'.$k.''.$m.'" style="text-align: right"><strong>'.number_format($thisTot1,$dPlace).'</strong></td>';
                        $returnData .= '</tr>';


                        if($sub_row['description']!='Equity')
                        {
                        $glData = $CI->db->query("SELECT det.glAutoID,chAcc.masterAutoID,
                                CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                FROM srp_erp_companyreporttemplatelinks det
                                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID                                
                                WHERE templateDetailID = {$detID}  ORDER BY sortOrder")->result_array();

                              
                        }
                        else
                        {
                            $glData = $CI->db->query("SELECT 7 as sortOrder,'Retained Earnings' as glData,'LIABILITIES' AS mainCategory,
                            'Equity' as subCategory,'Equity' as subsubCategory,
                            '-' as masterCategory,'Retained Earnings' as glAutoID
                            FROM
                            srp_erp_generalledger limit 1")->result_array();

                        }
                 
                     // print_R($glData);
                     // exit();
                
                           $t=0;
                        foreach ($glData as $gl_row){

                           $glAutoID = $gl_row['glAutoID'];
                            
                           $t=$t++;
                           

                            //$masterAutoID=$gl_row['masterAutoID'];
                            $returnData .= '<tr class="subhoverTr'.$k.''.$m.'">';

                            
                            $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';
                               
                            
                            $i = 1; $thisTot = 0;
                            $dPlace=2;
                            
                            while($i < 13) {
                                $trAmount = 0;
                                         
                                foreach ($month as $key => $value2) 
                                {
                                    /*$trAmount = $CI->db->query("SELECT sum((companyLocalAmount * -1)) companyLocalAmount FROM srp_erp_generalledger 
                                                   WHERE  GLAutoID = {$glAutoID}  and GLType!='0'
                                                 ")->row('companyLocalAmount');*/
                                                  if($glAutoID!='Retained Earnings')
                                                  {
                                                   

                                                 $gltype=$CI->db->query("SELECT GLType FROM srp_erp_generalledger 
                                                 WHERE  GLAutoID = {$glAutoID} 
                                                 
                                                 ")->row('GLType');
             
                                                 if($gltype=='BSA')
                                                 {
                                                     $gltype='BS';

                                                     $trAmount = $CI->db->query("select sum((srp_erp_generalledger.companyLocalAmount)) companyLocalAmount 
                                                     FROM srp_erp_generalledger  where srp_erp_generalledger.GLAutoID = {$glAutoID}
                                                     and DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <='$key' 
                                                      group by srp_erp_generalledger.GLAutoID
                                                     ")->row('companyLocalAmount');

                                                 }
                                                 else if($gltype=='PLE')
                                                 {
                                                     $gltype='PL';

                                                     $trAmount = $CI->db->query("select sum((srp_erp_generalledger.companyLocalAmount * -1)) companyLocalAmount 
                                                     FROM srp_erp_generalledger  where srp_erp_generalledger.GLAutoID = {$glAutoID}
                                                     and DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <='$key' 
                                                      group by srp_erp_generalledger.GLAutoID
                                                     ")->row('companyLocalAmount');
                                                 }

                                                 else if($gltype=='BSL')
                                                 {
                                                     $gltype='BS';

                                                     $trAmount = $CI->db->query("select sum((srp_erp_generalledger.companyLocalAmount * -1)) companyLocalAmount 
                                                     FROM srp_erp_generalledger  where srp_erp_generalledger.GLAutoID = {$glAutoID}
                                                     and DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <='$key' 
                                                      group by srp_erp_generalledger.GLAutoID
                                                     ")->row('companyLocalAmount');


                                                 }
             
             
             
                                                 $gldescription=$CI->db->query("SELECT GLDescription FROM srp_erp_generalledger 
                                                 WHERE  GLAutoID = {$glAutoID} 
             
                                                 ")->row('GLDescription');


                                                  }
                                                  else{
                                                    $trAmount = $CI->db->query("SELECT SUM(if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <= '$key',srp_erp_generalledger.companyLocalAmount,0) ) as 'companyLocalAmount' from srp_erp_generalledger
                                                    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'BS' 
                                                    AND srp_erp_chartofaccounts.companyID = $companyID LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID) LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
                                                    WHERE srp_erp_generalledger.documentDate <= '$fromdate' AND srp_erp_generalledger.companyID = $companyID
                                                    ")->row('companyLocalAmount');

                                                  }


                             $companylocalcurrency='companyLocalAmount';

                                    $trAmount = (empty($trAmount))? 0: $trAmount;
                                
                                $thisTot += round($trAmount, $dPlace);

                                if($glAutoID!='Retained Earnings')
                                                  {
                                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $glAutoID . '\',\'' .  $gltype . '\',\'' . $gldescription . '\',\'' .  $companylocalcurrency . '\',\'' . $key . '\')">'.number_format($trAmount,$dPlace).'</a></td>';
                                                  }
                                     else{
                                        $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($trAmount,$dPlace).'</td>';

                                     }
                                $i++;
                            }
                           
                            
                            
                        }

                            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($thisTot,$dPlace).'</td>';
                           
                            $returnData .= '</tr>';

                        }

                    }
                

                    if($sub_row['itemType'] == 3){ /*Group*/

                             
                        $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$detID}
                                            AND subCategory IN ({$where_insub})
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();


                        $where_in = '';

                        if(!empty($group_glData)){
                            $where_in_array = array_column($group_glData, 'glAutoID');
                            $where_in = implode(',', $where_in_array);
                        }

                        $returnData .= '<tr class="hoverTr'.$k.'">';
                        $returnData .= '<td class="sub1 description_td_rpt"><strong>'.$sub_row['description'].'<strong></td>';

                        $i = 1; $thisTot = 0;
                        while($i < 13) {
                            $trAmount = 0; $trAmountfirstdata=0;$trAmountseconddata=0;
                            foreach ($month as $key => $value2) 
                            {

                                if($row['description']!='LIABILITIES')
                                {
                                
                                    $trAmount = $CI->db->query("select sum((leg.companyLocalAmount)) companyLocalAmount 
                                    FROM srp_erp_companyreporttemplatelinks det 
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                                    JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                                    WHERE  templateDetailID IN({$where_insub}) and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$key'")->row('companyLocalAmount');
                                   
                                    $trAmount = (empty($trAmount) || $trAmount == null) ? 0 : $trAmount;
                                    

                                  }
                                  else{


                                    $trAmountfirstdata = $CI->db->query("select sum((leg.companyLocalAmount * -1)) companyLocalAmount 
                                    FROM srp_erp_companyreporttemplatelinks det 
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
                                    JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
                                    WHERE  templateDetailID IN({$where_insub}) and DATE_FORMAT(leg.documentDate,'%Y-%m')<='$key'")->row('companyLocalAmount');

                                    $trAmountseconddata = $CI->db->query("SELECT SUM(if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') <= '$key',srp_erp_generalledger.companyLocalAmount,0) ) as 'companyLocalAmount' from srp_erp_generalledger
                                    INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'BS' 
                                    AND srp_erp_chartofaccounts.companyID = $companyID LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID) LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID) 
                                    WHERE srp_erp_generalledger.documentDate <= '$fromdate' AND srp_erp_generalledger.companyID = $companyID
                                    ")->row('companyLocalAmount');

                                    //$trAmount =$trAmount1;
                                    $trAmountfirstdata = (empty($trAmountfirstdata) || $trAmountfirstdata == null) ? 0 : $trAmountfirstdata;
                                    $trAmountseconddata = (empty($trAmountseconddata) || $trAmountseconddata == null) ? 0 : $trAmountseconddata;
                                   
                                    $trAmount =$trAmountfirstdata+ $trAmountseconddata;


                                  }
                                 

                            $thisTot += round($trAmount, $dPlace);
                            $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($trAmount,$dPlace).'<strong></td>';
                           

                            $i++;

                        }
                            
                        }

                        $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right"><strong>'.number_format($thisTot,$dPlace).'<strong></td>';
                        $returnData .= '</tr>';

                      
                    }
                    $m=$m+1;
                }
            }
            $k=$k+1;

        }

        return $returnData;

    }
  }










if (!function_exists('get_ChartofAccounts_Category_Report')) {
    function get_ChartofAccounts_Category_Report($config_row_id,$gl_category_arr)
    {
        $base_arr = array();

        //Add company id check
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_mis_report_config_chartofaccounts');
        $CI->db->where('srp_erp_mis_report_config_chartofaccounts.config_row_id',$config_row_id);
        $report_rows = $CI->db->get()->result_array();

        foreach($report_rows as $key => $value){
            $base_arr[] = $gl_category_arr[$value['gl_auto_id']];
        }

        return $base_arr;
    }
}

if (!function_exists('get_config_rows_plus_minus')) {
    function get_config_rows_plus_minus($config_row_id)
    {
        
        //Add company id check
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_mis_report_config_details');
        $CI->db->where('srp_erp_mis_report_config_details.config_row_id',$config_row_id);
        $report_rows = $CI->db->get()->result_array();

        return $report_rows;
    }
}


if (!function_exists('get_report_sub_category_type')) {
    function get_report_sub_category_type($type)
    {
        
        //Add company id check
        if($type == 1){
            $sub_type = 'Income';
        }elseif($type == 2){
            $sub_type = 'Expense';
        }elseif($type == 3){
            $sub_type = 'Group Total';
        }elseif($type == 4){
            $sub_type = 'Group Group Total';
        }else{
            $sub_type = '';
        }
        return $sub_type;
    }
}


if (!function_exists('reporting_structure_system_types')) {
    function reporting_structure_system_types($status = TRUE)
    {
        
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->from('srp_erp_reporting_structure_system_types');
        $data = $CI->db->get()->result_array();

        if ($status == TRUE) {
            $data_arr = array('' => 'Select System Type');
        } else {
            $data_arr = array('' => '');
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('activity_code_status')) {
    function activity_code_status($active = null){
        $status = '<center>';
        
        if ($active == 0) {
            $status .= '<span class="label label-danger">Inactive</span>';
        }
        elseif ($active == 1) {
            $status .= '<span class="label label-success">Active</span>';
        }
        else {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}



if (!function_exists('load_activity_code_action')) {
    function load_activity_code_action($id, $activity_code)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->load->library('session');
        $status = '<span class="pull-right">';
       
        if ($id) {
            $status .= '<a onclick="load_report_config(' . $id . ',\''. $activity_code . '\')"><span title="Config" rel="tooltip" class="glyphicon glyphicon-cog" style="color:black;"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick="edit_activityCode(' .$id .',\'Delete Activity Code\');"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style="color:blue;"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            $status .= '<a onclick="delete_activityCode(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('all_company_drom')) {
    function all_company_drom()
    {
        $CI = &get_instance();
        $CI->db->select("company_id, company_code, company_name");
        $CI->db->from('srp_erp_company');
        $CI->db->where('confirmedYN', 1);
        $CI->db->group_by('company_code');
        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
            $company_arr = array('' => 'Select Company');
            foreach ($query->result_array() as $row) {
                $company_arr[trim($row['company_id'] ?? '')] = trim($row['company_code'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
            return $company_arr;
        } else {
            return null;
        }
    }

    function customRound($number) { 
        if ($number == floor($number)) {        
             // If no decimal part,
         return  floor($number);     
        } else {      
               // If there is a decimal part,
            return  $number; 
        } 
    }
}






