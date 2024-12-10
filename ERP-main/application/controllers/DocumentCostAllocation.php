<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller for document cost allocation
 */
class DocumentCostAllocation extends ERP_Controller
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('DocumentCostAllocation_model');
        $this->load->library('CostAllocation');
    }

    /**
     * Get allocation
     *
     * @return void
     */
    public function getCostAllocation()
    {
        $input = $this->input->post();
        $documentId = $this->input->post('documentId');

        $master = $this->costallocation->getDocumentMaster($documentId, $this->input->post('masterId'));
        if (true === empty($master))
        {
            $this->session->set_flashdata('e', 'Document master not found');
            echo json_encode(['status' => false]);
            return;
        }

        $detail = $this->costallocation->getDocumentDetail($documentId, $input['detailId']);
        if (true === empty($detail))
        {
            $this->session->set_flashdata('e', 'Document detail not found');
            echo json_encode(['status' => false]);
            return;
        }
        $detailAmount = $this->costallocation->getDetailAmount($documentId, $detail);
        $input['detailAmount'] = $detailAmount;
        $input['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
        $data = $this->DocumentCostAllocation_model->getCostAllocation($input);
        $this->load->view('system/finance/load_ajax_cost_allocation_view', $data);
    }

}