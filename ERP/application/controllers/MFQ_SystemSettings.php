<?php
/**
 *
 * -- =============================================
 * -- File Name : MFQ_SystemSettings.php
 * -- Project Name : SME
 * -- Module Name : Manufacturing
 * -- Author : Mohaned Nubashir
 * -- Create date : 11 October 2017
 * -- Description : controller file for manufacturing system settings
 *
 * --REVISION HISTORY
 * --
 *
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class MFQ_SystemSettings extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_SystemSettings_modal');
        $this->load->helper('mfq_helper');
    }

    function settings_users()
    {
        $sys = $this->input->post('sys');
        $data['masterID'] = $this->input->post('masterID');
        $url = '';
        switch (trim($sys)) {
            case 'documentStatus':
                $url = 'system/mfq/mfq_document_status';
                break;
            default:
                $url = '';
        }
        $this->load->view($url, $data);
    }

    function fetch_doc_status()
    {

        $masterID = $this->input->post('masterID');
        $companyID = $this->common_data['company_data']['company_id'];
        $filter = "companyID = $companyID ";
        if ($masterID != '') {
            $filter .= "AND srp_erp_mfq_status.documentID=$masterID";
        }

        $this->datatables->select("statusColor,statusBackgroundColor,statusID,srp_erp_mfq_documents.description as document,srp_erp_mfq_status.description as description");
        $this->datatables->from('srp_erp_mfq_documents');
        $this->datatables->join('srp_erp_mfq_status', 'srp_erp_mfq_status.documentID = srp_erp_mfq_documents.documentID', 'LEFT');
        $this->datatables->where($filter);
        $this->datatables->add_column('edit', '<a onclick="editDocumentStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;
<a onclick="deleteDocumentStatus($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'statusID');
        $this->datatables->add_column('color', '<div style="text-align: center">$1</div>', 'statuscolor(statusColor)');
        $this->datatables->add_column('backgroundColor', '<div style="text-align: center">$1</div>', 'statuscolor(statusBackgroundColor)');
        echo $this->datatables->generate();
    }

    function create_document_status()
    {
        $this->form_validation->set_rules('documentID', 'Document ID', 'trim|required');
        $this->form_validation->set_rules('status', 'status', 'trim|required');
        $this->form_validation->set_rules('color', 'Status Color', 'trim|required');
        $this->form_validation->set_rules('backgroundColor', 'Background Color', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_SystemSettings_modal->create_document_status());
        }
    }

    function deleteDocumentStatus()
    {
        echo json_encode($this->MFQ_SystemSettings_modal->deleteDocumentStatus());
    }

    function get_alldocumentStatus()
    {
        $statusID = $this->input->post('statusID');
        $data = $this->db->query("select * from srp_erp_mfq_status WHERE statusID={$statusID}")->row_array();
        echo json_encode($data);
    }


}