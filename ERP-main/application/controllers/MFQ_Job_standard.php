<?php

class MFQ_Job_standard extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_Job_standard_model');
        $this->load->helper('mfq');

    }

    function fetch_standardjobcard()
    {
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_mfq_standardjob.*,srp_erp_mfq_standardjob.documentDate as documentDate,approvedYN,jobAutoID,confirmedYN,srp_erp_currencymaster.CurrencyCode,srp_erp_mfq_standardjob.completionPercenatage as completionPercenatagesd", false)
            ->where('companyID',$companyid)
            ->from('srp_erp_mfq_standardjob')
            ->join('srp_erp_currencymaster','srp_erp_currencymaster.currencyID = srp_erp_mfq_standardjob.transactionCurrencyID','left');
        $this->datatables->add_column('status', '$1', 'confirmation_status_approved(confirmedYN,approvedYN)');
        $this->datatables->edit_column('percentage', '<span class="text-center" style="vertical-align: middle">$1</span>', 'job_status(completionPercenatagesd)');
        $this->datatables->add_column('edit', '$1', 'editJobstandard(jobAutoID,confirmedYN,approvedYN)');
        $this->datatables->edit_column('documentDate', '<span >$1 </span>', 'convert_date_format(documentDate)');

        echo $this->datatables->generate();
    }
    function save_standard_job()
    {
        $this->form_validation->set_rules('productiondate', 'Production Date', 'trim|required');
        $this->form_validation->set_rules('mfqWarehouseAutoID', 'Ware House', 'trim|required');
        $this->form_validation->set_rules('mfqSegmentID', 'Segment', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('mfqWarehouseAutoID', 'Ware House', 'trim|required');
        $this->form_validation->set_rules('mfqSegmentID', 'Segment', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $ProductionDate = strtotime($this->input->post('productiondate'));
            if(!empty($this->input->post('expirydate')))
            {
                $Expirydate = strtotime($this->input->post('expirydate'));
                if ($Expirydate > $ProductionDate) {
                    echo json_encode($this->MFQ_Job_standard_model->save_standard_job_header());
                } else {
                    echo json_encode(array('e', 'Expiry date cannot be greater than production date'));
                }
            }else
            {
                echo json_encode($this->MFQ_Job_standard_model->save_standard_job_header());
            }


        }
    }
    function fetch_mfq_standard_item()
    {
        echo json_encode($this->MFQ_Job_standard_model->fetch_mfq_standard_item());
    }
    function save_mfq_sd_job_input()
    {
        echo json_encode($this->MFQ_Job_standard_model->save_mfq_sd_job_input());
    }
    function load_mfq_standard_job_details()
    {
        $this->form_validation->set_rules('StandardJobcard', 'Standard Job Card ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_standard_model->load_mfq_standard_job_details());
        }

    }
    function fetch_mfq_standard_labourtask()
    {
        echo json_encode($this->MFQ_Job_standard_model->fetch_mfq_standard_labourtask());
    }
    function load_mfq_standard_job_details_labour()
    {
        $this->form_validation->set_rules('StandardJobcard', 'Standard Job Card ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_standard_model->load_mfq_standard_job_labour());
        }

    }
    function fetch_mfq_standard_overhead()
    {
        echo json_encode($this->MFQ_Job_standard_model->fetch_mfq_standard_overhead());
    }
    function load_mfq_standard_job_overhead()
    {
        $this->form_validation->set_rules('StandardJobcard', 'Standard Job Card ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_standard_model->load_mfq_standard_job_overhead());
        }
    }
    function fetch_mfq_standard_item_output()
    {
        echo json_encode($this->MFQ_Job_standard_model->fetch_mfq_standard_item_output());
    }
    function save_mfq_sd_job_output()
    {
        echo json_encode($this->MFQ_Job_standard_model->save_mfq_sd_job_output());
    }
    function load_mfq_standard_job_details_output()
    {
        $this->form_validation->set_rules('StandardJobcard', 'Standard Job Card ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_standard_model->load_mfq_standard_job_details_output());
        }
    }
    function standardjobcard_confirmation()
    {

        $standardjobcard = $this->input->post('standardjobcard');
        $companyID = current_companyID();
        $inputvalue = $this->db->query("SELECT SUM(transactionAmount) as inputvalue FROM `srp_erp_mfq_standardjob_items` where companyID = $companyID  And type = 1 And jobAutoID = $standardjobcard ")->row_array();
        $overheadtotal = $this->db->query("SELECT IFNULL(sum( totalValue ),0) as overheadtotal FROM `srp_erp_mfq_standardjob_overhead` where  companyID = $companyID  ANd jobAutoID = $standardjobcard")->row_array();
        $labourtotal = $this->db->query("SELECT  IFNULL(sum( totalValue ),0) as LABOUR FROM `srp_erp_mfq_standardjob_labourtask` where companyID = $companyID ANd jobAutoID = $standardjobcard")->row_array();
        $outputvalue = $this->db->query("SELECT IFNULL(sum( transactionAmount ),0) as outputvalue FROM `srp_erp_mfq_standardjob_items` where companyID = $companyID  And type = 2 And jobAutoID = $standardjobcard ")->row_array();

        $masterdetails = $this->db->query("select documentDate from srp_erp_mfq_standardjob where companyID = '{$companyID}' And jobAutoID = '{$standardjobcard}'")->row_array();
        $finaceyearvalidation = $this->db->query("SELECT * FROM `srp_erp_companyfinanceperiod` where companyID = '{$companyID}' And isActive = 1 AND ('{$masterdetails['documentDate']}' BETWEEN dateFrom AND dateTo)")->row_array();


        if(!empty($finaceyearvalidation))
        {
            $totalinput = ($inputvalue['inputvalue'] + $overheadtotal['overheadtotal'] + $labourtotal['LABOUR']);
            if((($totalinput!= null) || ($totalinput!='0')) && (($outputvalue['outputvalue']!= null) && ($outputvalue['outputvalue']!= '0')))
            {

                if (abs(($totalinput - $outputvalue['outputvalue']) / $outputvalue['outputvalue']) < 0.00001) {

                    echo json_encode($this->MFQ_Job_standard_model->standardjobcard_confirmation());
                    //echo json_encode(array(2));
                } else {

                        echo json_encode(array('error' => '4', 'message' => 'Please save your unsaved works before confirm this document'));


                    //  echo json_encode(array(1));
                }
            }else if($outputvalue['outputvalue'] == '0')
            {
                echo json_encode(array('error' => '4', 'message' => 'Please save your unsaved works before confirm this document'));
            }
            else
            {
                echo json_encode($this->MFQ_Job_standard_model->standardjobcard_confirmation());
            }

        }else
        {
            echo json_encode(array('error' => '1', 'message' => 'Document Date is not between Financial period !'));

        }



    }
    function fetch_standard_job_card_approval()
    {
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        /*DATE_FORMAT(est.documentDate,\'' . $convertFormat . '\') as documentDate*/

        $this->datatables->select('masterTbl.jobAutoID AS jobAutoID,masterTbl.documentSystemCode AS documentSystemCode,masterTbl.narration AS COMMENT,confirmedYN,masterTbl.approvedYN AS approvedYN,approvalLevelID,documentApprovedID,srp_erp_warehousemaster.wareHouseDescription,DATE_FORMAT(masterTbl.documentDate,\'' . $convertFormat . '\') as documentDate', false);
        $this->datatables->from('srp_erp_mfq_standardjob masterTbl');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = masterTbl.jobAutoID AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo', 'left');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo', 'left');
        $this->datatables->join('srp_erp_mfq_warehousemaster', 'srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = masterTbl.warehouseID', 'left');
        $this->datatables->join('srp_erp_warehousemaster', 'srp_erp_warehousemaster.wareHouseAutoID = srp_erp_mfq_warehousemaster.warehouseAutoID', 'left');

        $this->datatables->where('srp_erp_documentapproved.documentID', 'STJOB');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'STJOB');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('masterTbl.companyID', $companyID);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);

        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"STJOB",jobAutoID)');
        $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
        $this->datatables->add_column('edit', '$1', 'approval_action_sj(jobAutoID,approvalLevelID,approvedYN,documentApprovedID)');
        echo $this->datatables->generate();
    }
    function load_standardjobcard_print()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $jobAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('jobAutoID') ?? '');
        $convertFormat = convert_date_format_sql();

        $data['header'] = $this->db->query("SELECT
	srp_erp_mfq_standardjob.*,
	DATE_FORMAT( srp_erp_mfq_standardjob.documentDate, '%Y-%m-%d') AS ProductionDate,
	DATE_FORMAT( srp_erp_mfq_standardjob.createdDateTime, '%Y-%m-%d') AS createdDate,
	DATE_FORMAT( srp_erp_mfq_standardjob.expiryDate, '%Y-%m-%d') AS ExpiryDate,
	srp_erp_mfq_warehousemaster.warehouseDescription,
	Concat(segmentCode,' - ',description) as segmentdescription,
	srp_erp_mfq_standardjob.confirmedByName as confirmedByNamemfq,
	currenmaster.CurrencyName

FROM
	`srp_erp_mfq_standardjob`
	LEFT JOIN srp_erp_mfq_warehousemaster on srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_standardjob.warehouseID
	LEFT JOIN srp_erp_mfq_segment on srp_erp_mfq_segment.mfqSegmentID = srp_erp_mfq_standardjob.segmentID
	  LEFT JOIN srp_erp_currencymaster currenmaster on currenmaster.currencyID = srp_erp_mfq_standardjob.transactionCurrencyID
	where
	srp_erp_mfq_standardjob.companyID = $companyID And srp_erp_mfq_standardjob.jobAutoID = $jobAutoID ")->row_array();

        $data['row_material'] = $this->db->query("SELECT
	srp_erp_mfq_standardjob_items.*,
		CONCAT(srp_erp_itemmaster.itemSystemCode,' - ',srp_erp_mfq_standardjob_items.description) as itemdessys
FROM
	`srp_erp_mfq_standardjob_items`
	LEFT JOIN srp_erp_itemmaster on srp_erp_itemmaster.itemAutoID = srp_erp_mfq_standardjob_items.itemAutoID
	WHERE
	srp_erp_mfq_standardjob_items.companyID = $companyID
	AND jobAutoID = $jobAutoID
	AND type = 1 ")->result_array();

        $data['labour'] = $this->db->query("select
*
from
srp_erp_mfq_standardjob_labourtask
where
companyID = $companyID
AND jobAutoID = $jobAutoID")->result_array();

        $data['overhead']= $this->db->query("
select
*
from
srp_erp_mfq_standardjob_overhead
where
companyID = $companyID
AND jobAutoID = $jobAutoID")->result_array();

        $data['output'] = $this->db->query("SELECT
	srp_erp_mfq_standardjob_items.*,
	CONCAT(srp_erp_itemmaster.itemSystemCode,' - ',srp_erp_mfq_standardjob_items.description) as itemdessys,
	 DATE_FORMAT(expiryDate,'" . $convertFormat . "') AS expiryDate,
	 warehouse.wareHouseDescription
FROM
	`srp_erp_mfq_standardjob_items`
		LEFT JOIN srp_erp_itemmaster on srp_erp_itemmaster.itemAutoID = srp_erp_mfq_standardjob_items.itemAutoID
		LEFT JOIN srp_erp_warehousemaster warehouse on warehouse.wareHouseAutoID = srp_erp_mfq_standardjob_items.warehouseAutoID
	WHERE
	srp_erp_mfq_standardjob_items.companyID = $companyID
	AND jobAutoID = $jobAutoID
	AND type = 2")->result_array();

        $data['crew'] = $this->db->query("SELECT
	srp_erp_mfq_standardjob_crew.*,
	srp_erp_mfq_crews.Ename1 as name,
	 DATE_FORMAT(startDateTime,'" . $convertFormat . " %h:%i %p') AS startTime,DATE_FORMAT(endDateTime,'" . $convertFormat . " %h:%i %p') AS endTime

FROM
	`srp_erp_mfq_standardjob_crew`
	LEFT JOIN srp_erp_mfq_crews on srp_erp_mfq_crews.crewID = srp_erp_mfq_standardjob_crew.crewID
	where
	companyID = $companyID
	And jobAutoID = $jobAutoID")->result_array();


        $data['machine'] = $this->db->query("SELECT
	*,
	 DATE_FORMAT(startDateTime,'" . $convertFormat . " %h:%i %p') AS startTime,DATE_FORMAT(endDateTime,'" . $convertFormat . " %h:%i %p') AS endTime
FROM
	`srp_erp_mfq_standardjob_machine`
	where
	companyID  = $companyID
	AND jobAutoID = $jobAutoID
	")->result_array();

        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $data["type"] = "pdf";
        $html = $this->load->view('system/mfq/ajax/StandardJobCardprint',$data,true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4',$data['header']['approvedYN']);
        }
    }

    function save_standardjob_approval()
    {
        $system_code = trim($this->input->post('jobAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'STJOB', $level_id);
            if ($approvedYN) {
                echo json_encode(array('w', 'Document already approved'));
            } else {
                $this->db->select('jobAutoID');
                $this->db->where('jobAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_mfq_standardjob');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('w', 'Document already rejected'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('jobAutoID', 'Standard Job ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Job_standard_model->save_jobstandard_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('jobAutoID');
            $this->db->where('jobAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_mfq_standardjob');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                echo json_encode(array('w', 'Document already rejected'));
            } else {
                $rejectYN = checkApproved($system_code, 'STJOB', $level_id);
                if (!empty($rejectYN)) {
                    echo json_encode(array('w', 'Document already approved'));
                } else {
                    $this->form_validation->set_rules('po_status', 'Standard Job Card Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('jobAutoID', 'Standard Job ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->MFQ_Job_standard_model->save_jobstandard_approval());
                    }
                }
            }
        }
    }
    function warehousefinishgoods()
    {
        echo json_encode($this->MFQ_Job_standard_model->warehousefinishgoods());

    }
    function fetch_double_entry_standardjobcard()
    {
        $system_code = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID') ?? '');
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code') ?? '');
        $data['extra'] = $this->MFQ_Job_standard_model->fetch_double_entry_standardjobcard($system_code,$code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {

            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['approved_YN']);
        }
    }
    function save_sd_machine()
    {

            echo json_encode($this->MFQ_Job_standard_model->save_sd_machine());
        
    }
    function fetch_machine()
    {
        echo json_encode($this->MFQ_Job_standard_model->fetch_machine());
    }
    function load_mfq_standard_job_machine()
    {
        $this->form_validation->set_rules('StandardJobcard', 'Standard Job Card ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_standard_model->load_mfq_standard_job_machine());
        }
    }
    function save_mfq_crew()
    {
        echo json_encode($this->MFQ_Job_standard_model->save_mfq_crew());
    }
    function fetch_crew_details()
    {
        echo json_encode($this->MFQ_Job_standard_model->fetch_crew_details());
    }
    function referback_standardjobcard()
    {
        $jobAutoID = $this->input->post('jobAutoID');
        $this->db->select('approvedYN,jobAutoID,documentSystemCode');
        $this->db->where('jobAutoID', $jobAutoID);
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_mfq_standardjob');
        $approved_custmoer_invoice = $this->db->get()->row_array();
        if (!empty($approved_custmoer_invoice)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_custmoer_invoice['documentSystemCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($jobAutoID, 'STJOB');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }
    function delete_rawmaterial()
    {
        echo json_encode($this->MFQ_Job_standard_model->delete_rawmaterial());
    }
    function delete_labourtask()
    {
        echo json_encode($this->MFQ_Job_standard_model->delete_labourtask());
    }
    function delete_OverHead()
    {
        echo json_encode($this->MFQ_Job_standard_model->delete_OverHead());
    }
    function delete_finishgoods()
    {
    echo json_encode($this->MFQ_Job_standard_model->delete_finishgoods());
    }
    function delete_crew()
    {
        echo json_encode($this->MFQ_Job_standard_model->delete_crew());
    }
    function delete_machine()
    {
        echo json_encode($this->MFQ_Job_standard_model->delete_machine());
    }
    function save_standardjobcard_progress_value()
    {
        $this->form_validation->set_rules('jobAutoID', 'Standard Job Card ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Job_standard_model->save_standardjobcard_progress_value());
        }
    }
    function update_sj_header_details() // update the standard job card header details line by line
    {
        echo json_encode($this->MFQ_Job_standard_model->update_sj_header_details());
    }
    function update_sj_header_details_batchno()// update the standard job card header details batch no line by line
    {
        echo json_encode($this->MFQ_Job_standard_model->update_sj_header_details_batchno());
    }
    function update_sj_header_details_narration()// update the standard job card header details narration line by line
    {
        echo json_encode($this->MFQ_Job_standard_model->update_sj_header_details_narration());
    }
}
