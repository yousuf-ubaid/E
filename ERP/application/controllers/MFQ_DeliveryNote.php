<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MFQ_DeliveryNote extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('s3');
        $this->load->model('MFQ_DeliveryNote_model');
    }

    function fetch_delivery_note()
    {
        $mfqCustomerAutoID = $this->input->post('mfqCustomerAutoID');
        $DepartmentID = $this->input->post('DepartmentID');
        $date_format_policy = date_format_policy();
        $DeliveryDateFrom = $this->input->post('DeliveryDateFrom');
        $DeliveryDateTo = $this->input->post('DeliveryDateTo');
        $jobID = $this->input->post('jobID');
        $date_from_convert = input_format_date($DeliveryDateFrom, $date_format_policy);
        $date_to_convert = input_format_date($DeliveryDateTo, $date_format_policy);
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $where = 'srp_erp_mfq_deliverynote.companyID = '.$companyID.'';
        if(!empty($DeliveryDateFrom) && !empty($DeliveryDateTo)) {
            $where.= ' AND(DATE(deliveryDate) BETWEEN '.$date_from_convert.' AND '.$date_to_convert.')';
        }
        if(!empty($mfqCustomerAutoID)) {
            $where .= ' AND srp_erp_mfq_deliverynote.mfqCustomerAutoID IN ('.$mfqCustomerAutoID.') ';
        }
        if(!empty($DepartmentID)) {
            $where .= ' AND srp_erp_mfq_deliverynote.mfqSegmentID  IN ('.$DepartmentID.')';
        }

        if(!empty($jobID)) {
            $where .= ' AND srp_erp_mfq_deliverynotedetail.jobID  IN ('.$jobID.')';
        }

     
        $this->datatables->select("srp_erp_mfq_deliverynote.deliverNoteID as deliverNoteID,srp_erp_mfq_job.workProcessID as workProcessID,srp_erp_mfq_deliverynote.deliveryNoteCode as deliveryNoteCode,deliveryDate,srp_erp_mfq_customermaster.CustomerName as CustomerName,srp_erp_mfq_deliverynote.confirmedYN as confirmedYN,srp_erp_mfq_deliverynote.approvedYN as approvedYN,srp_erp_mfq_deliverynote.createdUserID as createdUserID,IFNULL(mfqsegment.segmentCode,'-') as segment", false)
            ->from('srp_erp_mfq_deliverynote')
            ->join('srp_erp_mfq_job', 'srp_erp_mfq_job.documentCode = srp_erp_mfq_deliverynote.jobreferenceNo', 'left') // 'srp_erp_mfq_job.workProcessID = srp_erp_mfq_deliverynote.jobID
            ->join('srp_erp_mfq_deliverynotedetail', 'srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID', 'left')
            ->join('srp_erp_mfq_customermaster', 'srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_deliverynote.mfqCustomerAutoID', 'left')
            ->join('srp_erp_mfq_segment mfqsegment', 'mfqsegment.mfqSegmentID = srp_erp_mfq_deliverynote.mfqSegmentID', 'left')
            ->where($where)
            ->group_by('srp_erp_mfq_deliverynote.deliverNoteID');
        $this->datatables->add_column('status', '$1', 'confirmation_status(confirmedYN)');
        $this->datatables->add_column('edit', '$1', 'load_delivery_note_action(deliverNoteID,workProcessID,confirmedYN,approvedYN,createdUserID)');
        $this->datatables->add_column('job_codes', '$1', 'delivery_note_job_codes(deliverNoteID)');
        $this->datatables->edit_column('deliveryDate', '<span >$1 </span>', 'convert_date_format(deliveryDate)');

        echo $this->datatables->generate();
        //$this->datatables->generate();

        //var_dump($this->db->last_query());exit;
    }

    function save_delivery_note_header()
    {
        $this->form_validation->set_rules('mfqCustomerAutoID', 'Customer', 'trim|required');
        $this->form_validation->set_rules('jobID[]', 'Job', 'trim|required');
        $this->form_validation->set_rules('deliveryDate', 'Delivery Date', 'trim|required|validate_date');
        //$this->form_validation->set_rules('driverName', 'Driver Name', 'trim|required');
        //$this->form_validation->set_rules('mobileNo', 'Mobile No', 'trim|required');
        ///$this->form_validation->set_rules('vehicleNo', 'Vehicle No', 'trim|required');
        $this->form_validation->set_rules('mfqsegmentID', 'Segment', 'trim|required');
//        $this->form_validation->set_rules('invoiceNote', 'Note', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            echo json_encode($this->MFQ_DeliveryNote_model->save_delivery_note_header());
        }
    }

    function load_deliveryNote_confirmation()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $deliverNoteID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('deliverNoteID') ?? '');

        $data['header'] = $this->db->query("SELECT 	dn.jobreferenceNo,dn.deliverNoteID as deliverNoteID, dn.deliveryNoteCode as deliveryNoteCode, job.documentCode as jobCode,DATE_FORMAT(dn.deliveryDate,'$convertFormat') AS deliveryDate,cus.CustomerName as CustomerName, dn.confirmedYN, dn.approvedYN, dn.createdUserID,driverName,vehicleNo,mobileNo,job.qty as detailQty,item.itemName as itemName, estm.poNumber as estmPoNumber,DATE_FORMAT(dn.confirmedDate,'$convertFormat') as confirmedDate,dn.confirmedByName, dn.note, CONCAT(company.company_code, '|', company.company_name) as companyName FROM srp_erp_mfq_deliverynote dn LEFT JOIN srp_erp_mfq_job job ON job.workProcessID = dn.jobID LEFT JOIN srp_erp_mfq_customermaster cus ON cus.mfqCustomerAutoID = dn.mfqCustomerAutoID LEFT JOIN srp_erp_mfq_itemmaster item ON item.mfqItemID = job.mfqItemID LEFT JOIN srp_erp_mfq_estimatedetail estd ON estd.estimateDetailID = job.estimateDetailID LEFT JOIN srp_erp_mfq_estimatemaster estm ON estd.estimateMasterID = estm.estimateMasterID LEFT JOIN srp_erp_company company ON company.company_id = dn.companyID WHERE dn.companyID = {$companyID} AND dn.deliverNoteID = {$deliverNoteID}")->row_array();
        $data['details'] = $this->MFQ_DeliveryNote_model->deliveryNote_confirmation_details($deliverNoteID);

        $linked_subJobs = $this->db->query("SELECT documentCode FROM srp_erp_mfq_job JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID WHERE deliveryNoteID = {$deliverNoteID}")->result_array();
        $data['linkedSubJobs'] = join(', ', array_column($linked_subJobs, 'documentCode'));
        
        $data['type'] = ($this->input->post('html')?'html':'pdf');
        $html = $this->load->view('system/mfq/ajax/delivery_note_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function load_delivery_note_header()
    {
        echo json_encode($this->MFQ_DeliveryNote_model->load_delivery_note_header());
    }

    function delivery_note_confirmation()
    {
        echo json_encode($this->MFQ_DeliveryNote_model->delivery_note_confirmation());
    }

    function referback_delivery_note()
    {
        echo json_encode($this->MFQ_DeliveryNote_model->referback_delivery_note());

    }

    function delete_delivery_note()
    {
        echo json_encode($this->MFQ_DeliveryNote_model->delete_delivery_note());
    }

    function fetch_customer_jobs()
    { //update
        $mainJobFilter = getPolicyValues('DNJF', 'All');
        if(!isset($mainJobFilter)) {
            $mainJobFilter = 1;
        }
        $mainJobID = $this->input->post('mainjobID');
        $data_arr = array();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('workProcessID,documentCode');
        $this->db->from('srp_erp_mfq_job job');
        $this->db->join('srp_erp_mfq_estimatedetail estd', 'estd.estimateDetailID = job.estimateDetailID', 'left');
        $this->db->join('srp_erp_mfq_estimatemaster estm', 'estd.estimateMasterID = estm.estimateMasterID', 'left');
        $this->db->where('job.mfqCustomerAutoID', $this->input->post("mfqCustomerAutoID"));
        if($mainJobFilter == 1) {
          //  $this->db->where('job.linkedJobID', $mainJobID);    
        }
        $this->db->where('job.mfqSegmentID', $this->input->post("mfqSegmentID"));
        $this->db->where('job.companyID', $companyID);
        $this->db->where('job.approvedYN', 1);
        /* $this->db->where('estm.orderStatus', 2); */
        // $this->db->where('job.levelNo', 2);
        $master = $this->db->get()->result_array();

        if (!empty($master)) {
            foreach ($master as $row) {
                $data_arr[trim($row['workProcessID'] ?? '')] = trim($row['documentCode'] ?? '');
            }
        }
        echo form_dropdown('jobID[]', $data_arr, '', 'class="form-control select2" id="jobID"  multiple="" ');
    }

    function fetch_customer_main_jobs()
    {
        $data_arr = array();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('workProcessID,documentCode');
        $this->db->from('srp_erp_mfq_job job');
        $this->db->join('srp_erp_mfq_estimatedetail estd', 'estd.estimateDetailID = job.estimateDetailID', 'left');
        $this->db->join('srp_erp_mfq_estimatemaster estm', 'estd.estimateMasterID = estm.estimateMasterID', 'left');
        $this->db->where('job.mfqCustomerAutoID', $this->input->post("mfqCustomerAutoID"));
        $this->db->where('job.mfqSegmentID', $this->input->post("mfqSegmentID"));
        $this->db->where('job.companyID', $companyID);
        /* $this->db->where('job.approvedYN', 1); */
        /* $this->db->where('estm.orderStatus', 2); */
        $this->db->where('job.levelNo', 1);
        $master = $this->db->get()->result_array();

        $data_arr[''] = 'Select Main Job';
        if (!empty($master)) {
            foreach ($master as $row) {
                $data_arr[trim($row['workProcessID'] ?? '')] = trim($row['documentCode'] ?? '');
            }
        }
        echo form_dropdown('mainjobID', $data_arr, '', 'class="form-control select2" id="mainjobID" onchange="get_customer_jobs(\'\',\'\',this.value)"');
    }

    function save_delivery_note_details()
    {
        $this->form_validation->set_rules('deliverNoteID', 'deliverNoteID', 'trim|required');
        $this->form_validation->set_rules('deliveryNoteDetailID[]', 'deliveryNoteDetailID', 'trim|required');
        $this->form_validation->set_rules('deliveredQty[]', 'deliveredQty', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_DeliveryNote_model->save_delivery_note_details());
        }
    }

    function load_deliveryNote_detail_view()
    {
        $deliverNoteID = trim($this->input->post('deliverNoteID') ?? '');
        $data['details'] = $this->MFQ_DeliveryNote_model->deliveryNote_confirmation_details($deliverNoteID);

        echo json_encode($this->load->view('system/mfq/ajax/delivery_note_detail_view', $data));
    }

    function delete_delivery_note_detail()
    {
        echo json_encode($this->MFQ_DeliveryNote_model->delete_delivery_note_detail());
    }
    function save_deliverynote_jobno()
    {
        $this->db->trans_start();
        $DNAutoId = $this->input->post('DNAutoID');
        $value = $this->input->post('value');
        $data['jobreferenceNo'] = str_replace('<br />', '|', $value);
        $this->db->where('deliverNoteID', $DNAutoId);
        $this->db->update('srp_erp_mfq_deliverynote', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'update Faild ' . $this->db->_error_message()));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Job No Successfully Updated '));
        }
    }

    function fetch_delivery_note_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Delivery Note');
        $this->load->database();

        $header = [  '#', 'Delivery note Code', 'Segment', 'Customer', 'Document Date', 'Status'];

        $details = $this->MFQ_DeliveryNote_model->fetch_delivery_note_details();

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A4:F4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
        $this->excel->getActiveSheet()->mergeCells("A1:J1");
        $this->excel->getActiveSheet()->mergeCells("A2:J2");
        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Delivery Note'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($details, null, 'A6');

        $filename = 'Delivery Note.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function save_est_order_status()
    {
        echo json_encode($this->MFQ_DeliveryNote_model->save_est_order_status());       
    }

    function update_dn_po_number()
    {
        $this->form_validation->set_rules('value', 'PO Number', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $value =  $this->input->post('pk');
            $poNumber =  $this->input->post('value');
            $this->db->trans_start();
            $data['poNumberDN'] = $poNumber;
            $this->db->where('deliveryNoteDetailID', $value);
            $this->db->update('srp_erp_mfq_deliverynotedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
             echo json_encode( array('e','Qty Save Failed '));
            } else {
                $this->db->trans_commit();
                echo json_encode( array('s','Qty Updated Successfully.'));
            }
        }
    }

    function upload_attachment_for_DeliveryNote()
    {
        echo json_encode($this->MFQ_DeliveryNote_model->upload_attachment_for_DeliveryNote());
    }

    function referback_delivery_note_with_validation(){
        echo json_encode($this->MFQ_DeliveryNote_model->referback_delivery_note_with_validation());
    }

    /**job attachments */
    function fetch_attachments()
    {
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);

        $this->db->select('srp_erp_documentattachments.*, srp_erp_mfq_job.documentCode as jobreferenceNo');
        $this->db->from('srp_erp_documentattachments');
        $this->db->join('srp_erp_mfq_job', 'srp_erp_mfq_job.documentID = srp_erp_documentattachments.documentID', 'left');
        $this->db->join('srp_erp_mfq_deliverynote', 'srp_erp_mfq_deliverynote.jobID = srp_erp_mfq_job.workProcessID','left');
        $this->db->where('srp_erp_mfq_job.workProcessID', $this->input->post('documentSystemCode'));
        $this->db->where('srp_erp_documentattachments.documentSystemCode', $this->input->post('documentSystemCode'));
        $this->db->where('srp_erp_documentattachments.documentID', $this->input->post('documentID'));
        $this->db->where('srp_erp_documentattachments.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->result_array();

        $result = '';
        $x = 1;
        if (!empty($data)) {
            foreach ($data as $val) {
                $burl = base_url("attachments") . '/' . $val['myFileName'];
                $type = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';
                if ($val['fileType'] == '.xlsx') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xls') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xlsxm') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.doc') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.docx') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.ppt') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.pptx') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpeg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.gif') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.png') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.txt') {
                    $type = '<i class="color fa fa-file-text-o" aria-hidden="true"></i>';
                }
              
                
                $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['jobreferenceNo'] . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td></tr>';
                
                $x++;

            }
        } else {
            $result = '<tr class="danger"><td colspan="5" class="text-center">'.$this->lang->line('common_no_attachment_found').'</td></tr>';
        }
        echo json_encode($result);
    }
}