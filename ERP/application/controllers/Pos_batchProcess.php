<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_batchProcess.php
 * -- Project Name : POS
 * -- Module Name : POS Batch
 * -- Create date : 23 October 2018
 * -- Description : Batch File .
 *
 * --REVISION HISTORY
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_batchProcess extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('pos');
        $this->load->model('Pos_batchProcess_model');
    }

    function LoadMailingList()
    {
        $this->datatables->select('srp_erp_pos_mailinglist.id as id, employeeID, email, batchlist_id, srp_erp_pos_batchlist.description as description, srp_employeesdetails.Ename2 as name', false)
            ->from('srp_erp_pos_mailinglist')
            ->join('srp_erp_pos_batchlist', 'srp_erp_pos_batchlist.id = srp_erp_pos_mailinglist.batchlist_id', 'left')
            ->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_pos_mailinglist.employeeID', 'left')
            ->where('srp_erp_pos_mailinglist.companyID', current_companyID());

        $listID = $this->input->post('listID');
        if ($listID) {
            $this->datatables->where('batchlist_id', $listID);
        }

        $this->datatables->add_column('DT_RowId', 'packItemTbl_$1', 'id')
            ->edit_column('edit', '$1', 'col_pos_mailing_list(id)');
        echo $this->datatables->generate();
        //$this->db->last_query();
    }

    function save_mailing_list()
    {
        $this->form_validation->set_rules('batchlist_id', 'Process Type', 'trim|required');
        $this->form_validation->set_rules('employeeID', 'User / Employee', 'trim|required');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 'e', 'message' => validation_errors()));
        } else {
            echo json_encode($this->Pos_batchProcess_model->save_mailing_list());
        }
    }

    function delete_mailing_list()
    {
        $id = $this->input->post('id');
        if (!empty($id)) {
            echo json_encode($this->Pos_batchProcess_model->delete_mailingList($id));
        } else {
            echo json_encode(array('status' => 'e', 'message' => 'An error has occurred when perform this operation. Message: id not found!'));
        }
    }

    function edit_camera_setup()
    {
        $id = $this->input->post('id');
        if (!empty($id)) {
            $result = $this->Pos_batchProcess_model->get_camera_setup_by_id($id);
            if (!empty($result)) {
                echo json_encode(array_merge($result, array('status' => 's', 'message' => 'loaded')));
            } else {
                echo json_encode(array_merge($result, array('status' => 'e', 'message' => 'Error loading data')));
            }
        } else {
            echo json_encode(array('status' => 'e', 'message' => 'An error has occurred when perform this operation. Message: id not found!'));
        }
    }

    function get_cctv_feed()
    {
        echo json_encode($this->Pos_batchProcess_model->get_cctv_feed());
    }


}