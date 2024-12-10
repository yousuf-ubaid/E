<?php

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MFQ_Dashboard extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_Dashboard_modal');
    }

    function fetch_machine()
    {
        $companyid = current_companyID();
      /*  $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_mfq_fa_asset_master.mfq_faID,IFNULL(mfa.documentCode,'<span style=\'color: #008000 \'>Available</span>') as documentCode,assetDescription,IFNULL(mfa.hoursSpent,'-') as hoursSpent,IFNULL(DATE_FORMAT(mfa.endDate,'".$convertFormat."'),'-') AS endDate,faCode", false)
            ->from('srp_erp_mfq_fa_asset_master')
            ->join('(SELECT SUM(hoursSpent) as hoursSpent,documentCode,endDate,mfq_faID FROM srp_erp_mfq_workprocessmachines LEFT JOIN srp_erp_mfq_job ON srp_erp_mfq_job.workProcessID = srp_erp_mfq_workprocessmachines.workProcessID GROUP BY srp_erp_mfq_workprocessmachines.workProcessID,mfq_faID) mfa', 'srp_erp_mfq_fa_asset_master.mfq_faID = mfa.mfq_faID','left')->where('srp_erp_mfq_fa_asset_master.companyID',current_companyID());
        echo $this->datatables->generate();*/

        $convertFormat = convert_date_format_sql();
        $this->datatables->select("*", false)
            ->from('srp_erp_mfq_fa_asset_master as ma')
            ->join("
            (SELECT
	srp_erp_mfq_fa_asset_master.mfq_faID,
	IFNULL( mfa.documentCode, 'Available' ) AS documentCode,
	assetDescription,
	IFNULL( mfa.hoursSpent, '-' ) AS hoursSpent,
	IFNULL( DATE_FORMAT( mfa.endDate, '%d-%m-%Y' ), '-' ) AS endDate,
	faCode
FROM
	srp_erp_mfq_fa_asset_master
	LEFT JOIN (
SELECT
	SUM( hoursSpent ) AS hoursSpent,
	documentCode,
	endDate,
	mfq_faID
FROM
	srp_erp_mfq_workprocessmachines
	LEFT JOIN srp_erp_mfq_job ON srp_erp_mfq_job.workProcessID = srp_erp_mfq_workprocessmachines.workProcessID
GROUP BY
	srp_erp_mfq_workprocessmachines.workProcessID,
	mfq_faID
	) mfa ON srp_erp_mfq_fa_asset_master.mfq_faID = mfa.mfq_faID
WHERE
	srp_erp_mfq_fa_asset_master.companyID = '$companyid' UNION
SELECT
	srp_erp_mfq_fa_asset_master.mfq_faID,
	IFNULL( mfasd.documentSystemCode, 'Available' ) AS documentCode,
	assetDescription,
	IFNULL( mfasd.hoursSpent, '-' ) AS hoursSpent,
	IFNULL( DATE_FORMAT( mfasd.endDateTime, '%d-%m-%Y' ), '-' ) AS endDate,
	faCode
FROM
	srp_erp_mfq_fa_asset_master
	LEFT JOIN (
SELECT
	SUM( hoursSpent ) AS hoursSpent,
	srp_erp_mfq_standardjob.documentSystemCode,
	endDateTime,
	mfq_faID
FROM
	srp_erp_mfq_standardjob_machine
	LEFT JOIN srp_erp_mfq_standardjob ON srp_erp_mfq_standardjob.jobAutoID = srp_erp_mfq_standardjob_machine.jobAutoID
GROUP BY
	srp_erp_mfq_standardjob_machine.jobAutoID,
	mfq_faID
	) mfasd ON srp_erp_mfq_fa_asset_master.mfq_faID = mfasd.mfq_faID
WHERE
	srp_erp_mfq_fa_asset_master.companyID = '$companyid') t1
            ", 't1.mfq_faID=ma.mfq_faID');
        echo $this->datatables->generate();

    }

    function fetch_job_status()
    {
       $convertFormat = convert_date_format_sql();
      /*  $this->datatables->select("documentCode,workProcessID,DATE_FORMAT(startDate,'".$convertFormat."') AS startDate,DATE_FORMAT(endDate,'".$convertFormat."') AS endDate,description,ws.percentage as percentage,", false)
            ->from('srp_erp_mfq_job')
            ->join('(SELECT jobID,COUNT(*) as totCount,SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completedCount,(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END)/COUNT(*)) * 100 as percentage FROM srp_erp_mfq_workflowstatus GROUP BY jobID)  ws', 'ws.jobID = srp_erp_mfq_job.workProcessID')->where('srp_erp_mfq_job.companyID',current_companyID());
        $this->datatables->edit_column('percentage', '<span class="text-center" style="vertical-align: middle">$1</span>', 'job_status(percentage)');
        $this->datatables->edit_column('description', '<span class="text-center" style="vertical-align: middle">$1</span>', 'trim_value(description,5)');*/
       /* $convertFormat = convert_date_format_sql();*/
     $this->datatables->select("documentCode,workProcessID,startDate,endDate,description,IF(jobstatus.type != 1,standardjob.completionPercenatage,jobpercentage.percentage) as percentage,", false)
            ->from('getmfqjobstatus jobstatus')
          ->join('getmfqjobpercentage jobpercentage','jobpercentage.jobID = jobstatus.workProcessID AND jobstatus.type = 1','left')
            ->join('srp_erp_mfq_standardjob standardjob','standardjob.jobAutoID = jobstatus.workProcessID AND jobstatus.type = 2','left')
            ->where('jobstatus.companyID',current_companyID());
        $this->datatables->edit_column('percentage', '<span class="text-center" style="vertical-align: middle">$1</span>', 'job_status(percentage)');
        $this->datatables->edit_column('description', '<span class="text-center" style="vertical-align: middle">$1</span>', 'trim_value(description,5)');
        echo $this->datatables->generate();
    }

    function fetch_jobs_status()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->query("documentCode,workProcessID,DATE_FORMAT(startDate,'".$convertFormat."') AS startDate,DATE_FORMAT(endDate,'".$convertFormat."') AS endDate,description,ws.percentage as percentage,", false)
            ->from('srp_erp_mfq_job')
            ->join('(SELECT jobID,COUNT(*) as totCount,SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completedCount,(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END))/COUNT(*)) * 100 as percentage FROM srp_erp_mfq_workflowstatus GROUP BY jobID)  ws', 'ws.jobID = srp_erp_mfq_job.workProcessID')->where('srp_erp_mfq_job.companyID',current_companyID());
        $this->datatables->edit_column('percentage', '<span class="text-center" style="vertical-align: middle">$1</span>', 'job_status(percentage)');
        echo $this->datatables->generate();
    }

    function fetch_jobs()
    {
       echo json_encode($this->MFQ_Dashboard_modal->fetch_jobs());
    }

    function fetch_ongoing_job()
    {
        $qry = array();
        $query = '';
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $sSearch = $this->input->post('sSearch');
        $where = "ongoingjobs.companyID = ".current_companyID()." AND (IF(type = 1,( jobTbl.approvedYN != 1),( `standardjob`.`completionPercenatage` != '100' OR standardjob.completionPercenatage IS NULL ))) ";
       /* $this->datatables->select("documentCode,srp_erp_mfq_job.workProcessID,DATE_FORMAT(startDate,'".$convertFormat."') AS startDate,DATE_FORMAT(endDate,'".$convertFormat."') AS endDate,DATE_FORMAT(srp_erp_mfq_job.documentDate,'".$convertFormat."') AS documentDate,documentCode,srp_erp_mfq_job.description as description,ws.percentage as percentage,cust.CustomerName,seg.description as segment,qty,em.estimateCode,(jcm.materialCharge+jcl.totalValue+jco.totalValue) as amount,jcm.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces", false)
            ->from('srp_erp_mfq_job')
            ->join('(SELECT jobID,COUNT(*) as totCount,SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completedCount,(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END)/COUNT(*)) * 100 as percentage FROM srp_erp_mfq_workflowstatus GROUP BY jobID)  ws', 'ws.jobID = srp_erp_mfq_job.workProcessID')
            ->join('(SELECT * FROM srp_erp_mfq_estimatedetail) ed', 'ed.estimateDetailID = srp_erp_mfq_job.estimateDetailID','left')
            ->join('(SELECT * FROM srp_erp_mfq_estimatemaster) em', 'em.estimateMasterID = ed.estimateMasterID','left')
            ->join('(SELECT * FROM srp_erp_mfq_customermaster) cust', 'cust.mfqCustomerAutoID = srp_erp_mfq_job.mfqCustomerAutoID','left')
            ->join('(SELECT * FROM srp_erp_mfq_segment) seg', 'seg.mfqSegmentID = srp_erp_mfq_job.mfqSegmentID','left')
            ->join('(SELECT SUM(IFNULL(materialCharge,0)) as materialCharge,workProcessID,companyLocalCurrencyDecimalPlaces FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID) jcm', 'jcm.workProcessID = srp_erp_mfq_job.workProcessID','left')
            ->join('(SELECT SUM(IFNULL(totalValue,0)) as totalValue,workProcessID FROM srp_erp_mfq_jc_labourtask GROUP BY workProcessID) jcl', 'jcl.workProcessID = srp_erp_mfq_job.workProcessID','left')
            ->join('(SELECT SUM(IFNULL(totalValue,0)) as totalValue,workProcessID FROM srp_erp_mfq_jc_overhead GROUP BY workProcessID) jco', 'jco.workProcessID = srp_erp_mfq_job.workProcessID','left')
            ->where('srp_erp_mfq_job.companyID',current_companyID())->where('percentage != 100');
        $this->datatables->edit_column('percentage', '<div class="text-center" style="vertical-align: middle">$1%</div>', 'round_percentage(percentage)');
        $this->datatables->edit_column('description', '<div style="vertical-align: middle">$1</div>', 'trim_value(description,20)');
        $this->datatables->edit_column('amount', '<div class="text-right" style="vertical-align: middle">$1</div>', 'format_number(amount,companyLocalCurrencyDecimalPlaces)');*/
           
		$qry = $this->MFQ_Dashboard_modal->job_entry_query();
        if(!empty($qry)) {
            $query = " (SELECT SUM(totalValue) AS wipAmount, workProcessID FROM (" . join(' UNION ', $qry) . ")tbl GROUP BY workProcessID)wipCalculate";
        }
    
        if ($sSearch) {
            $where .=  " AND ((ongoingjobs.documentDate Like '%$sSearch%') OR (documentCode Like '%$sSearch%') OR (seg.description Like '%$sSearch%') OR (ongoingjobs.description Like '%$sSearch%') OR (cust.CustomerName LIKE '%$sSearch%') OR (estimateCode LIKE '%$sSearch%'))";
        }

        $this->datatables->select("documentCode,ongoingjobs.workProcessID AS workProcessID,startDate AS startDate,endDate AS endDate,ongoingjobs.documentDate AS documentDate,documentCode,ongoingjobs.description as description,
        IF(ongoingjobs.type!=2,percentage.percentage,standardjob.completionPercenatage) as percentage,cust.CustomerName AS CustomerName,seg.description as segment,qty,em.estimateCode AS estimateCode,
        IFNULL(wipAmount, 0) AS amount,
        em.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
        ((((discountedPrice/ed.companyLocalExchangeRate) * (( 100 + IFNULL( totMargin, 0 ))/ 100 )) * (( 100 - IFNULL( totDiscount, 0 ))/ 100 ))/ expectedQty) * qty AS estimateValue,
        currencymaster.CurrencyCode as CurrencyCode,
        ((IFNULL( machineCost, 0 ) + IFNULL( overheadCost, 0 ) + IFNULL( labourCost, 0 ) + IFNULL( materialCost, 0 ))* qty) AS BOMCost", false)
            ->from('get_mfqongoingjobs ongoingjobs')
            ->join('(SELECT * FROM srp_erp_mfq_estimatedetail) ed', 'ed.estimateDetailID = ongoingjobs.estimateDetailID  AND type = 1','left')
            ->join('(SELECT * FROM srp_erp_mfq_estimatemaster) em', 'em.estimateMasterID = ed.estimateMasterID AND type = 1','left')
            ->join('(SELECT * FROM srp_erp_mfq_customermaster) cust', 'cust.mfqCustomerAutoID = ongoingjobs.mfqCustomerAutoID AND type = 1','left')
            ->join('getmfqjobpercentage percentage','percentage.jobID = ongoingjobs.workProcessID AND ongoingjobs.type = 1','left')
            ->join('srp_erp_mfq_standardjob standardjob',' standardjob.jobAutoID = ongoingjobs.workProcessID AND ongoingjobs.type = 2','left')
            ->join('(SELECT * FROM srp_erp_mfq_segment) seg', 'seg.mfqSegmentID = ongoingjobs.mfqSegmentID','left');

        if(!empty($query)) {
            $this->datatables->join($query,'wipCalculate.workProcessID = ongoingjobs.workProcessID','left');
        }
        $this->datatables->join('(SELECT workProcessID, bomMasterID,closedYN, approvedYN FROM srp_erp_mfq_job) jobTbl','jobTbl.workProcessID = ongoingjobs.workProcessID AND `type` = 1','left')
            ->join('(SELECT SUM(totalValue/companyLocalExchangeRate) AS machineCost, bomMasterID FROM srp_erp_mfq_bom_machine GROUP BY bomMasterID) machine', 'machine.bomMasterID = jobTbl.bomMasterID AND `type` = 1','left')
            ->join('(SELECT SUM(totalValue/companyLocalExchangeRate) AS overheadCost, bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) overhead', 'overhead.bomMasterID = jobTbl.bomMasterID AND `type` = 1','left')
            ->join('(SELECT SUM(totalValue/companyLocalExchangeRate) AS labourCost, bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) labourtask', 'labourtask.bomMasterID = jobTbl.bomMasterID AND `type` = 1','left')
            ->join('(SELECT SUM(materialCost/companyLocalExchangeRate) AS materialCost, bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) material', 'material.bomMasterID = jobTbl.bomMasterID AND `type` = 1','left')
            ->join('srp_erp_currencymaster currencymaster','currencymaster.currencyID = ongoingjobs.companylocalcurrencyID','Left')
            ->where($where);
        $this->datatables->edit_column('currencycode', '<div class="text-center" style="vertical-align: middle">$1</div>', 'CurrencyCode');
        $this->datatables->edit_column('percentage', '<div class="text-center" style="vertical-align: middle">$1%</div>', 'round_percentage(percentage)');
        $this->datatables->edit_column('description', '<div style="vertical-align: middle">$1</div>', 'trim_value(description,20)');
        $this->datatables->edit_column('amount', '<div class="text-right" style="vertical-align: middle">$1</div>', 'format_number(amount,companyLocalCurrencyDecimalPlaces)');
        $this->datatables->edit_column('BOMAmount', '<div class="text-right" style="vertical-align: middle">$1</div>', 'format_number(BOMCost,companyLocalCurrencyDecimalPlaces)');
        $this->datatables->edit_column('amount_estimated', '<div class="text-right" style="vertical-align: middle">$1</div>', 'format_number(estimateValue,companyLocalCurrencyDecimalPlaces)');
        echo $this->datatables->generate();

         // ( IFNULL(jcm.materialCharge,0)  + IFNULL(jcl.totalValue,0)  + IFNULL(jco.totalValue,0)  ) as amount,
        // ->join('(SELECT SUM(IFNULL(materialCharge,0)/companyLocalExchangeRate) as materialCharge,workProcessID,companyLocalCurrencyDecimalPlaces FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID) jcm', 'jcm.workProcessID = ongoingjobs.workProcessID AND type = 1','left')
        // ->join('(SELECT SUM(IFNULL(totalValue,0)/companyLocalExchangeRate) as totalValue,workProcessID FROM srp_erp_mfq_jc_labourtask GROUP BY workProcessID) jcl', 'jcl.workProcessID = ongoingjobs.workProcessID AND type = 1','left')
        // ->join('(SELECT SUM(IFNULL(totalValue,0)/companyLocalExchangeRate) as totalValue,workProcessID FROM srp_erp_mfq_jc_overhead GROUP BY workProcessID) jco', 'jco.workProcessID = ongoingjobs.workProcessID AND type = 1','left')
    }

    function ongoing_job_excel(){
        $this->load->library('excel');
        //set cell A1 content with some text
        $this->excel->setActiveSheetIndex(0);
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Ongoing Job');
        // load database
        $this->load->database();
        // load model
        // get all users in array formate
        $data = $this->fetch_ongoing_job_excel();
        $header = array('Date','Job No','Division','Job Description','Client Name','Qty','Currency','BOM Cost','WIP/Cost','Estimated Selling Price','Quote Ref','Job Completion(%)');
        // Header
        $this->excel->getActiveSheet()->fromArray($header, null, 'A1');
        // Data
        $this->excel->getActiveSheet()->fromArray($data, null, 'A2');
        //set aligment to center for that merged cell (A1 to D1)
       // ob_clean();
        ob_start(); # added
        $filename = 'Ongoing job.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        ob_clean(); # remove this
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function fetch_ongoing_job_excel(){
        $qry = array();
        $query = '';
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();

        $qry = $this->MFQ_Dashboard_modal->job_entry_query();
        if(!empty($qry)) {
            $query = " LEFT JOIN(SELECT SUM(totalValue) AS wipAmount, workProcessID FROM (" . join(' UNION ', $qry) . ")tbl GROUP BY workProcessID)wipCalculate ON wipCalculate.workProcessID = ongoingjobs.workProcessID AND `type` = 1";
        }

        $result = $this->db->query("SELECT
        DATE_FORMAT(ongoingjobs.documentDate,'".$convertFormat."') AS documentDate,
        documentCode,
        seg.description AS segment,
        ongoingjobs.description AS description,
        cust.CustomerName AS CustomerName,
        qty,
        currencymaster.CurrencyCode AS CurrencyCode,
        ((IFNULL( machineCost, 0 ) + IFNULL( overheadCost, 0 ) + IFNULL( labourCost, 0 ) + IFNULL( materialCost, 0 )) * qty) AS BOMCost,
        IFNULL(wipAmount, 0) AS amount,
        /* ( IFNULL( jcm.materialCharge, 0 ) + IFNULL( jcl.totalValue, 0 ) + IFNULL( jco.totalValue, 0 ) ) AS amount, */
        ((
		((discountedPrice / ed.companyLocalExchangeRate) * (( 100 + IFNULL( totMargin, 0 ))/ 100 )) * (( 100 - IFNULL( totDiscount, 0 ))/ 100 ))/expectedQty) * qty AS estimateValue,
        em.estimateCode AS estimateCode,
         IF( ongoingjobs.type != 2, percentage.percentage, standardjob.completionPercenatage ) AS percentage
    FROM
        `get_mfqongoingjobs` `ongoingjobs`
        LEFT JOIN ( SELECT * FROM srp_erp_mfq_estimatedetail ) ed ON `ed`.`estimateDetailID` = `ongoingjobs`.`estimateDetailID` 
        AND `type` = 1
        LEFT JOIN ( SELECT * FROM srp_erp_mfq_estimatemaster ) em ON `em`.`estimateMasterID` = `ed`.`estimateMasterID` 
        AND `type` = 1
        LEFT JOIN ( SELECT * FROM srp_erp_mfq_customermaster ) cust ON `cust`.`mfqCustomerAutoID` = `ongoingjobs`.`mfqCustomerAutoID` 
        AND `type` = 1
        LEFT JOIN `getmfqjobpercentage` `percentage` ON `percentage`.`jobID` = `ongoingjobs`.`workProcessID` 
        AND `ongoingjobs`.`type` = 1
        LEFT JOIN `srp_erp_mfq_standardjob` `standardjob` ON `standardjob`.`jobAutoID` = `ongoingjobs`.`workProcessID` 
        AND `ongoingjobs`.`type` = 2
        LEFT JOIN ( SELECT * FROM srp_erp_mfq_segment ) seg ON `seg`.`mfqSegmentID` = `ongoingjobs`.`mfqSegmentID`
        /* LEFT JOIN ( SELECT SUM( IFNULL( materialCharge/companyLocalExchangeRate, 0 )) AS materialCharge, workProcessID, companyLocalCurrencyDecimalPlaces FROM srp_erp_mfq_jc_materialconsumption GROUP BY workProcessID ) jcm ON `jcm`.`workProcessID` = `ongoingjobs`.`workProcessID` 
        AND `type` = 1
        LEFT JOIN ( SELECT SUM( IFNULL( totalValue/companyLocalExchangeRate, 0 )) AS totalValue, workProcessID FROM srp_erp_mfq_jc_labourtask GROUP BY workProcessID ) jcl ON `jcl`.`workProcessID` = `ongoingjobs`.`workProcessID` 
        AND `type` = 1
        LEFT JOIN ( SELECT SUM( IFNULL( totalValue/companyLocalExchangeRate, 0 )) AS totalValue, workProcessID FROM srp_erp_mfq_jc_overhead GROUP BY workProcessID ) jco ON `jco`.`workProcessID` = `ongoingjobs`.`workProcessID` 
        AND `type` = 1 */
        LEFT JOIN ( SELECT workProcessID, bomMasterID, closedYN, approvedYN FROM srp_erp_mfq_job ) jobTbl ON `jobTbl`.`workProcessID` = `ongoingjobs`.`workProcessID` AND `type` = 1
        LEFT JOIN ( SELECT SUM( totalValue/companyLocalExchangeRate ) AS machineCost, bomMasterID FROM srp_erp_mfq_bom_machine GROUP BY bomMasterID ) machine ON `machine`.`bomMasterID` = `jobTbl`.`bomMasterID` AND `type` = 1
        LEFT JOIN ( SELECT SUM( totalValue/companyLocalExchangeRate ) AS overheadCost, bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID ) overhead ON `overhead`.`bomMasterID` = `jobTbl`.`bomMasterID` AND `type` = 1
        LEFT JOIN ( SELECT SUM( totalValue/companyLocalExchangeRate ) AS labourCost, bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID ) labourtask ON `labourtask`.`bomMasterID` = `jobTbl`.`bomMasterID` AND `type` = 1
        LEFT JOIN ( SELECT SUM( materialCost/companyLocalExchangeRate ) AS materialCost, bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID ) material ON `material`.`bomMasterID` = `jobTbl`.`bomMasterID` AND `type` = 1
        LEFT JOIN `srp_erp_currencymaster` `currencymaster` ON `currencymaster`.`currencyID` = `ongoingjobs`.`companylocalcurrencyID` 
        $query
    WHERE
        `ongoingjobs`.`companyID` = $companyID 
        AND (IF(type = 1,( jobTbl.approvedYN != 1 OR `percentage`.`percentage` IS NULL ),
        (`standardjob`.`completionPercenatage` != '100'  OR `standardjob`.`completionPercenatage` IS NULL))) ")->result_array();
        
        return $result;
    }

    function pull_from_erp(){
        echo json_encode($this->MFQ_Dashboard_modal->pull_from_erp());
    }

    function update_wac_from_erp(){
        echo json_encode($this->MFQ_Dashboard_modal->update_wac_from_erp());
    }

    function load_erp_warehouse(){
        echo json_encode($this->MFQ_Dashboard_modal->load_erp_warehouse());
    }

    function pull_from_erp_warehouse(){
        echo json_encode($this->MFQ_Dashboard_modal->pull_from_erp_warehouse());
    }

    function awarded_job_status(){
        echo json_encode($this->MFQ_Dashboard_modal->awarded_job_status());
    }

    function awarded_job_drill_down(){
        echo json_encode($this->MFQ_Dashboard_modal->awarded_job_drill_down());
    }

    function planned_job_return(){
        echo json_encode($this->MFQ_Dashboard_modal->planned_job_return());
    }

    function estimate_vs_actual_job(){
        echo json_encode($this->MFQ_Dashboard_modal->estimate_vs_actual_job());
    }

    function ongoing_job_wip_total(){
        echo json_encode($this->MFQ_Dashboard_modal->ongoing_job_wip_total());
    }

    function load_quotation_widget(){
        echo json_encode($this->MFQ_Dashboard_modal->load_quotation_widget());
    }

    function load_awarded_widget(){
        echo json_encode($this->MFQ_Dashboard_modal->load_awarded_widget());
    }

    function load_delivery_widget(){
        echo json_encode($this->MFQ_Dashboard_modal->load_delivery_widget());
    }

    function quotation_submitted_drilldown(){
        echo $this->MFQ_Dashboard_modal->quotation_submitted_drilldown();
    }

    function quotation_awarded_drilldown(){
        echo $this->MFQ_Dashboard_modal->quotation_awarded_drilldown();
    }

    function current_month_drilldown(){
        echo $this->MFQ_Dashboard_modal->current_month_drilldown();
    }

    function previous_month_drilldown(){
        echo $this->MFQ_Dashboard_modal->previous_month_drilldown();
    }

    function actuals_drilldown(){
        echo $this->MFQ_Dashboard_modal->actuals_drilldown();
    }

    function expected_drilldown(){
        echo $this->MFQ_Dashboard_modal->expected_drilldown();
    }

    function planned_job_return_drill_down(){
        echo json_encode($this->MFQ_Dashboard_modal->planned_job_return_drill_down());
    }

    function actual_drilldown()
    {
        echo json_encode($this->MFQ_Dashboard_modal->actual_drilldown());
    }

}
