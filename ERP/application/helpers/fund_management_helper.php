<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('investmentType_drop')) {
    function investmentType_drop()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("invTypeID, description");
        $CI->db->from('srp_erp_fm_types');
        $CI->db->where('companyID', current_companyID());

        $data = $CI->db->get()->result_array();
        $data_arr = ['' => $CI->lang->line('common_select_a_option')];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['invTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('investment_master_action')) {
    function investment_master_action($id)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="edit_investment('.$id.')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('investmentCompany_drop')) {
    function investmentCompany_drop($rowData=0)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("id, company_name, currencyID");
        $CI->db->from('srp_erp_fm_companymaster');
        $CI->db->where('companyID', current_companyID());

        if($rowData == 1){
            return $CI->db->get()->result_array();
        }

        $data = $CI->db->get()->result_array();
        $data_arr = ['' => $CI->lang->line('common_select_a_option')];
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['company_name'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('industryTypes_drop')) {
    function industryTypes_drop()
    {
        $CI =& get_instance();
        $CI->db->SELECT("industrytypeID,industryTypeDescription");
        $CI->db->FROM('srp_erp_industrytypes');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Industry');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['industrytypeID'] ?? '')] = trim($row['industryTypeDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('action_docMaster')) {
    function action_docMaster($DocDesID, $DocDescription)
    {
        $DocDescription = "'" . $DocDescription . "'";
        $action = '<a onclick="edit_docMaster(' . $DocDesID . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_docMaster(' . $DocDesID . ', ' . $DocDescription . ')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('action_docSetup')) {
    function action_docSetup($DocDesID, $DocDescription)
    {
        $DocDescription = "'" . $DocDescription . "'";
        $action = '<a onclick="edit_docSetup(' . $DocDesID . ', this)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $action .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="delete_docSetup(' . $DocDesID . ', ' . $DocDescription . ')">';
        $action .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return '<span class="pull-right">' . $action . '</span>';

    }
}

if (!function_exists('system_documents_drop')) {
    function system_documents_drop($placeHolder = 0)
    {
        $CI =& get_instance();
        $CI->db->select("documentID,document");
        $CI->db->from('srp_erp_documentcodes');
        $CI->db->where_in('documentID', ['FMC', 'FMIT']);
        $data = $CI->db->get()->result_array();

        $placeHolder = ($placeHolder == 0)? 'Select Document Type': 'All';
        $data_arr = array('' => $placeHolder);
        if (!empty($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['documentID'] ?? '')] = trim($row['document'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('mandatoryStatus')) {
    function mandatoryStatus($isMandatory)
    {
        return ($isMandatory == 1) ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
    }
}

if (!function_exists('investment_det')) {
    function investment_det($invDate, $narration, $currencyCode)
    {
        $str = '<b>Currency : </b>'.$currencyCode;
        $str .= '<br/><b>Investment Date : </b>'.$invDate;
        $str .= '<br/><b>Narration : </b>'.$narration;

        return $str;
    }
}

if (!function_exists('investment_amount_det')) {
    function investment_amount_det($dPlaces, $trAmount, $disburseAmount)
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('fn_management', $primaryLanguage);
        $CI->lang->load('common', $primaryLanguage);

        // fn_man_investment fn_man_disbursed
        $str = '<b>'.$CI->lang->line('fn_man_investment').' : </b>'.number_format($trAmount, $dPlaces);
        $str .= '<br/><b>'.$CI->lang->line('fn_man_disbursed').' : </b>'.number_format($disburseAmount, $dPlaces);
        $str .= '<br/><b>'.$CI->lang->line('common_balance').' : </b>'.number_format(($trAmount - $disburseAmount), $dPlaces);

        return $str;
    }
}


if (!function_exists('get_attachment_details')) {
    function get_attachment_details($systemDocumentID, $documentSystemCode, $docSubID=0)
    {
        $companyID = current_companyID();
        $CI =& get_instance();

        $filter = ($docSubID != 0 )? " AND stTB.documentSubID=".$docSubID: "";

        $attachData = $CI->db->query("SELECT stTB.docSetupID, stTB.systemDocumentID, stTB.description, isMandatory, 
                            expireDate_req, attachmentID, docExpiryDate, sendExpiryAlertBefore, attachmentDescription, myFileName
                            FROM srp_erp_fm_documentsetup stTB                                    
                            LEFT JOIN (
                                SELECT documentSubID, attachmentID, docExpiryDate, attachmentDescription, myFileName
                                FROM srp_erp_documentattachments WHERE documentID='{$systemDocumentID}' AND companyID={$companyID}
                                AND documentSystemCode = {$documentSystemCode}                                       
                            ) attTB ON attTB.documentSubID = stTB.docSetupID
                            WHERE stTB.companyID={$companyID} AND stTB.systemDocumentID='{$systemDocumentID}' $filter")->result_array();

        //echo '<pre>'.$CI->db->last_query().'</pre>';
        return $attachData;
    }
}

if (!function_exists('get_attachment_status')) {
    function get_attachment_status($sysType, $documentSystemCode, $docSubID=0)
    {

        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('fn_management', $primaryLanguage);

        $data = get_attachment_details($sysType, $documentSystemCode, $docSubID);

        $pendingDoc = 0; $elapsedDoc = 0; $remainingDoc = 0;
        if (!empty($data)) {
            foreach ($data as $row) {

                $expiryDate = $row['docExpiryDate'];

                if ($expiryDate != null) {
                    $today = date('Y-m-d');
                    $date1 = new DateTime($expiryDate);
                    $date2 = new DateTime($today);
                    $diff = $date2->diff($date1)->format("%a");
                    $remainingDays = intval($diff);

                    if ($today < $expiryDate) {
                        $sendExpiryAlertBefore = $row['sendExpiryAlertBefore'];
                        if($sendExpiryAlertBefore > 0 && $remainingDays <= $sendExpiryAlertBefore){
                            $remainingDoc++;
                        }

                    } else {
                        $elapsedDoc++;
                    }
                }

                if (empty($row['myFileName']) && $row['isMandatory'] == 1) {
                    $pendingDoc++;
                }

            }
        }

        /*** Pending ***/
        $class = 'success'; $onclickFn = '';
        if($pendingDoc > 0){
            $onclickFn = 'onclick="get_document_status_more_details(\''.$sysType.'\', '. $documentSystemCode.', \'pending\')"';
            $class = 'danger';
        }

        $title = $CI->lang->line('fn_man_pending');
        $str = '<b>'.$title.' :</b> <div class="label label-circle-' . $class . '" style="line-height: 2" '.$onclickFn.'>' . $pendingDoc . '</div><br/>';


        /*** Elapse ***/
        if($elapsedDoc > 0){
            $onclickFn = 'onclick="get_document_status_more_details(\''.$sysType.'\', '. $documentSystemCode.', \'elapse\')"';

            $title = $CI->lang->line('fn_man_elapsed');
            $str .= '<b>'.$title.' :</b> <div class="label label-circle-danger" '.$onclickFn.'>' . $elapsedDoc . '</div><br/>';
        }


        /*** Close to expire ***/
        if($remainingDoc > 0){
            $onclickFn = 'onclick="get_document_status_more_details(\''.$sysType.'\', '. $documentSystemCode.', \'expiry\')"';
            $title = $CI->lang->line('fn_man_expiry_remain');

            $str .= '<b>'.$title.' :</b> <div class="label label-circle-warning" '.$onclickFn.'>' . $remainingDoc . '<span ><br/>';
        }

        return $str;
    }
}

if (!function_exists('document_status_drop')) {
    function document_status_drop()
    {
        $CI =& get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('fn_management', $primaryLanguage);
        $CI->lang->load('common', $primaryLanguage);

        return [
            '0' => $CI->lang->line('common_all'),
            '1' => $CI->lang->line('fn_man_pending'),
            '2' => $CI->lang->line('fn_man_elapsed'),
            '3' => $CI->lang->line('fn_man_expiry_remain'),
        ];
    }
}

if (!function_exists('financial_template_dropDown')) {
    function financial_template_dropDown()
    {
        $CI =& get_instance();
        $CI->db->select("companyReportTemplateID, description");
        $CI->db->from('srp_erp_companyreporttemplate');
        $CI->db->where('companyID', current_companyID());
        $template = $CI->db->get()->result_array();

        $template_arr = array('' => 'Select GL Templates');
        if (isset($template)) {
            foreach ($template as $row) {
                $template_arr[trim($row['companyReportTemplateID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $template_arr;
    }
}


if (!function_exists('financial_master_action')) {
    function financial_master_action($id, $confirmedYN=0)
    {
        $status = '<span class="pull-right">';

        if($confirmedYN == 0) {
            $status .= '<a onclick="edit_financial(' . $id . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp; | &nbsp;';
        }else{
            $status .= '<a onclick="view_financial(' . $id . ')"><i class="fa fa-fw fa-eye" title="View" rel="tooltip"></i></a>&nbsp; | &nbsp;';
        }
        $status .= '<a target="_blank" href="'.site_url('Fund_management/finance_submission_print').'/'.$id.'/Submission_print">';
        $status .= ' <span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        if($confirmedYN == 0){
            $status .= '&nbsp; | &nbsp;<a onclick="delete_financial('.$id.')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('statementTemplate_drop')) {
    function statementTemplate_drop($reportID)
    {
        $CI =& get_instance();
        $CI->db->select("companyReportTemplateID,description");
        $CI->db->from('srp_erp_companyreporttemplate');
        $CI->db->where('reportID', $reportID);
        if($reportID == 5)
        {
            $CI->db->where('templateType', 1);
        }
        $CI->db->where('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select template');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['companyReportTemplateID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('load_fm_statement_report')) {
    function load_fm_statement_report($masterData, $year, $temMasterID, $dPlace){
        $CI =& get_instance();

        $masterID = 0;
        $returnData = '';
        $returnData .= '<tr><td class="mini-header description_td_rpt period-header">Description</td>';
        $i = 1;
        while($i < 13){
            $thisMonth = $year.'-'.$i.'-01';
            $thisMonth = date('M', strtotime($thisMonth));
            $thisMonth = $thisMonth.'&nbsp;-&nbsp;'.$year;

            $returnData .= '<td class="mini-header description_td_rpt period-header">'.$thisMonth.'</td>';
            $i++;
        }
        $returnData .= '<td class="mini-header description_td_rpt period-header">Total</td></tr>';

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
                $returnData .= '<td class="mini-header description_td_rpt" colspan="14"><span class="td-main-header"><i class="fa fa-minus-square"></i>';
                $returnData .= $row['description'].'</span></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID} ORDER BY sortOrder")->result_array();

                foreach ($subData as $sub_row){
                    $detID = $sub_row['detID'];

                    if($sub_row['itemType'] == 1){ /*Sub category*/
                        $returnData .= '<tr class="hoverTr">';
                        $returnData .= '<td class="sub1 description_td_rpt" colspan="14"> '.$sub_row['description'].'</td></tr>';



                        $glData = $CI->db->query("SELECT det.glAutoID,  
                                CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                FROM srp_erp_companyreporttemplatelinks det
                                JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID                                
                                WHERE templateDetailID = {$detID} ORDER BY sortOrder")->result_array();


                        foreach ($glData as $gl_row){

                            $glAutoID = $gl_row['glAutoID'];

                            $returnData .= '<tr class="hoverTr">';
                            $returnData .= '<td class="sub2 description_td_rpt glDescription">'.$gl_row['glData'].'</td>';


                            $i = 1; $thisTot = 0;
                            while($i < 13) {
                                $trAmount = 0;
                                if (array_key_exists($i, $masterData)) {
                                    $masterID = $masterData[$i][0]['id'];

                                    $trAmount = $CI->db->query("SELECT (transactionAmount * -1) trAmount FROM srp_erp_fm_financialdetails 
                                                   WHERE documentMasterAutoID = {$masterID} AND GLAutoID = {$glAutoID} ")->row('trAmount');

                                    $trAmount = (empty($trAmount))? 0: $trAmount;
                                }
                                $thisTot += round($trAmount, $dPlace);
                                $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">'.number_format($trAmount,$dPlace).'</td>';
                                $i++;
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
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();

                        $where_in = '';

                        if(!empty($group_glData)){
                            $where_in_array = array_column($group_glData, 'glAutoID');
                            $where_in = implode(',', $where_in_array);
                        }

                        $returnData .= '<tr class="hoverTr">';
                        $returnData .= '<td class="sub1 description_td_rpt">'.$sub_row['description'].'</td>';

                        $i = 1; $thisTot = 0;
                        while($i < 13) {
                            $trAmount = 0;
                            if($where_in != '') {
                                if (array_key_exists($i, $masterData)) {
                                    $masterID = $masterData[$i][0]['id'];

                                    $where_in_array = array_column($group_glData, 'glAutoID');
                                    $where_in = implode(',', $where_in_array);
                                    $trAmount = $CI->db->query("SELECT SUM(IFNULL(transactionAmount,0) * -1) trAmount FROM srp_erp_fm_financialdetails 
                                                      WHERE documentMasterAutoID = {$masterID} AND GLAutoID IN ({$where_in})")->row('trAmount');

                                    $trAmount = (empty($trAmount) || $trAmount == null) ? 0 : $trAmount;
                                }
                            }

                            $thisTot += round($trAmount, $dPlace);

                            $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right">'.number_format($trAmount,$dPlace).'</td>';

                            $i++;
                        }

                        $returnData .= '<td class="sub2 sub_total_rpt amount_td_rpt" style="text-align: right">'.number_format($thisTot,$dPlace).'</td>';
                        $returnData .= '</tr>';
                    }
                }
            }
            else{
                /*Group*/

                $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$templateID}
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();

                $where_in = '';
                if(!empty($group_glData)){
                    $where_in_array = array_column($group_glData, 'glAutoID');
                    $where_in = implode(',', $where_in_array);
                }

                $returnData .= '<tr><td colspan="2">&nbsp;</td></tr>';
                $returnData .= '<tr class="hoverTr">';
                $returnData .= '<td class="mini-header"><span class="td-main-header description_td_rpt"><i class="fa fa-minus-square"></i>  '.$row['description'].'</span></td>';

                $i = 1; $thisTot = 0;
                while($i < 13) {
                    $trAmount = 0;
                    if($where_in != '') {
                        if (array_key_exists($i, $masterData)) {
                            $masterID = $masterData[$i][0]['id'];

                            $where_in_array = array_column($group_glData, 'glAutoID');
                            $where_in = implode(',', $where_in_array);
                            $trAmount = $CI->db->query("SELECT SUM(IFNULL(transactionAmount,0) * -1) trAmount FROM srp_erp_fm_financialdetails 
                                                      WHERE documentMasterAutoID = {$masterID} AND GLAutoID IN ({$where_in})")->row('trAmount');

                            $trAmount = (empty($trAmount) || $trAmount == null) ? 0 : $trAmount;
                        }
                    }

                    $thisTot += round($trAmount, $dPlace);

                    $returnData .= '<td class="sub1 total_black_rpt amount_td_rpt" style="text-align: right">'.number_format($trAmount,$dPlace).'</td>';

                    $i++;
                }

                $returnData .= '<td class="sub2 total_black_rpt amount_td_rpt" style="text-align: right">'.number_format($thisTot,$dPlace).'</td>';
                $returnData .= '</tr>';

            }

        }

        return $returnData;

    }
}

