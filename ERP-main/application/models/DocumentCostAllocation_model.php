<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class for document cost allocation
 */
class DocumentCostAllocation_model extends ERP_Model
{
    /**
     * Save document cost allocation
     *
     * @param array $data
     * @return boolean
     */
    public function saveDocumentCostAllocation($data)
    {
        $documentID = $data['documentId'];
        $masterId = $data['masterId'];
        $detailId = $data['detailId'];

        $reportingStructureData = $this->getReportingStructureDetail($data['activityCodeID'], $data['amount']);

        $masterData = [];

        if (!empty($reportingStructureData)) {
            foreach ($reportingStructureData as $value) {
                $masterData[] = [
                    'reportingStructureDetailID' => $value['detailId'],
                    'reportingStructureID' => $value['masterId'],
                    'allocatedAmount' => $value['allocatedAmount'],
                    'documentID' => $documentID,
                    'documentmasterID' => $masterId,
                    'documentDetailID' => $detailId,
                    'companyID' => $value['companyID'],
                    'activityCodeID' => $data['activityCodeID'],
                ];
            }

            $this->db->trans_start();

            $this->db->insert_batch('srp_erp_document_cost_allocation', $masterData);

            $this->db->trans_complete();

            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'Cost allocation saved successfully');
                return true;
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('e', 'Error occurred while saving cost allocation');
                return false;
            }
        }

        return true;
    }

    /**
     * Get cost allocation
     *
     * @param array $input
     * @return array
     */
    public function getCostAllocation($input)
    {
        $allocation = $this->getAllocationFromCostAllocation($input);
        if (false === empty($allocation) && false === empty($allocation['report']))
        {
            return $allocation;
        }

        return $this->getAllocationFromReportingStructure($input);
    }

    /**
     * Get allocation from cost allocation
     *
     * @param array $input
     * @return array
     */
    private function getAllocationFromCostAllocation($input)
    {
        $this->db->select(
            'srp_erp_reporting_structure_master.description,
            srp_erp_reporting_structure_details.detail_description,
            allocatedAmount'
        );
        $this->db->from('srp_erp_document_cost_allocation');
        $this->db->join('srp_erp_reporting_structure_master', 'srp_erp_reporting_structure_master.id = srp_erp_document_cost_allocation.reportingStructureID', 'inner');
        $this->db->join('srp_erp_reporting_structure_details', 'srp_erp_reporting_structure_details.id = srp_erp_document_cost_allocation.reportingStructureDetailID', 'inner');
        $this->db->where('srp_erp_document_cost_allocation.documentID', $input['documentId']);
        $this->db->where('srp_erp_document_cost_allocation.documentDetailID', $input['detailId']);
        $result = $this->db->get()->result_array();

        $resultArr = [];
        if(false === empty($result))
        {
            foreach($result as $row)
            {
                $resultArr[$row['description']][] = $row + ['transactionCurrencyDecimalPlaces' => $input['transactionCurrencyDecimalPlaces']];
            }
        }

        $data['report'] = $resultArr;

        return $data;
    }

    /**
     * Get cost allocation from reporting structure
     *
     * @param array $input
     * @return array
     */
    private function getAllocationFromReportingStructure($input)
    { 
        $result = $this->getReportingStructureDetail($input['activityCodeID'], $input['detailAmount']);

        $resultArr = [];
        if(false === empty($result))
        {
            foreach($result as $row)
            {
                $resultArr[$row['description']][] = $row + ['transactionCurrencyDecimalPlaces' => $input['transactionCurrencyDecimalPlaces']];
            }
        }

        $data['report'] = $resultArr;

        return $data;
    }


    /**
     * Get cost allocation from reporting structure
     *
     * @param int $activityCodeID
     * @param float $amount
     * @return array
     */
    private function getReportingStructureDetail($activityCodeID, $amount)
    {
        $this->db->select(
            'srp_erp_activity_code_sub.rpt_struc_master_id as masterId,
            srp_erp_activity_code_sub.rpt_struc_detail_id as detailId,
            srp_erp_reporting_structure_master.description,
            srp_erp_activity_code_sub.rpt_struc_detail_description as detail_description,
            srp_erp_activity_code_sub.companyID,
            '.$amount.' as allocatedAmount'
        );
        $this->db->from('srp_erp_activity_code_main');
        $this->db->join('srp_erp_activity_code_sub', 'srp_erp_activity_code_main.id = srp_erp_activity_code_sub.main_id', 'inner');
        $this->db->join('srp_erp_reporting_structure_master', 'srp_erp_reporting_structure_master.id = srp_erp_activity_code_sub.rpt_struc_master_id', 'inner');
        $this->db->where('srp_erp_activity_code_main.id', $activityCodeID);
        return $this->db->get()->result_array();
    }

    /**
     * Get reporting structure
     *
     * @param integer $id
     * @return array
     */
    public function getReportingStructure($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_reporting_structure_master');
        $this->db->where('id', $id);
        return $this->db->get()->row_array(); 
    }

    /**
     * Delete cost allocation
     *
     * @param string $documentId
     * @param integer $masterId
     * @return bool
     */
    public function deleteCostAllocation($documentId, $masterId)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_document_cost_allocation');
        $this->db->where('documentID', $documentId);
        $this->db->where('documentmasterID', $masterId);
        $result = $this->db->get()->row_array(); 
        if(empty($result))
        {
            return true;
        }

        $this->db->where('documentID', $documentId);
        $this->db->where('documentmasterID', $masterId);
        $this->db->delete('srp_erp_document_cost_allocation');

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    
}