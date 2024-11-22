<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CostAllocation
{
     /**
     * Documents code
     *
     * @var CI_Controller
     */
    private $ci;

    /**
     * Documents code
     *
     * @var string
     */
    const DOCUMENT_JV = 'JV';
    const DOCUMENT_PR = 'PRQ';

    /**
     * Construct
     */
    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->model('DocumentCostAllocation_model');
        $this->ci->load->model('Journal_entry_model');
        $this->ci->load->model('Purchase_request_modal');
        $this->ci->load->library('session');
    }

    /**
     * Save document cost allocation
     *
     * @var array $data
     * @return bool
     */
    public function saveDocumentCostAllocation($data)
    { 
        $documentId = $data['documentId'];
        $masterId = $data['masterId'];
        $detailId = $data['detailId'];

        $master = $this->getDocumentMaster($documentId, $masterId);
        if (true === empty($master))
        {
            $this->ci->session->set_flashdata('e', 'Document master not found');
            return false;
        }

        $detail = $this->getDocumentDetail($documentId, $detailId);
        if (true === empty($detail))
        {
            $this->ci->session->set_flashdata('e', 'Document detail not found');
            return false;
        }

        return $this->ci->DocumentCostAllocation_model->saveDocumentCostAllocation($data);
    }

    /**
     * Delete document allocations
     *
     * @var string $documentId
     * @var integer $masterId
     * @return bool
     */
    public function deleteDocumentCostAllocation($documentId, $masterId)
    { 
        $master = $this->getDocumentMaster($documentId, $masterId);
        if (true === empty($master))
        {
            $this->ci->session->set_flashdata('e', 'Document master not found');
            return false;
        }

        return $this->ci->DocumentCostAllocation_model->deleteCostAllocation($documentId, $masterId);
        
    }

    /**
     * Get document master
     *
     * @param string $documentId
     * @param integer $documentMasterId
     * @return array
     */
    public function getDocumentMaster($documentId, $documentMasterId)
    {
        switch($documentId)
        {
            case self::DOCUMENT_JV:
                return $this->ci->Journal_entry_model->getJournalEntry($documentMasterId);
                break;
            case self::DOCUMENT_PR:
                return $this->ci->Purchase_request_modal->getPurchaseRequest($documentMasterId);
                break;
            default:
                return [];
        }
    }

    /**
     * Get document detail
     *
     * @param string $documentId
     * @param integer $documentDetailId
     * @return array
     */
    public function getDocumentDetail($documentId, $documentDetailId)
    {
        switch($documentId)
        {
            case self::DOCUMENT_JV:
                return $this->ci->Journal_entry_model->getJournalEntryDetail($documentDetailId);
                break;
            case self::DOCUMENT_PR:
                return $this->ci->Purchase_request_modal->getPurchaseRequestDetail($documentDetailId);
                break;
            default:
                return [];
        }
    }

    /**
     * Get detail amount
     *
     * @param string $documentId
     * @param array $data
     * @return float
     */
    public function getDetailAmount($documentId, $data)
    {
        switch($documentId)
        {
            case self::DOCUMENT_JV:
                return false === empty($data['debitAmount']) ? $data['debitAmount'] : $data['creditAmount'];
                break;
            case self::DOCUMENT_PR:
                return $data['totalAmount'];
                break;
            default:
                return 0;
        }
    }
}