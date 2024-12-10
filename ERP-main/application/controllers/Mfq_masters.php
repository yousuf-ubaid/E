<?php
/**
 *
 * -- =============================================
 * -- File Name : mfq_masters.php
 * -- Project Name : SME
 * -- Module Name : Manufacturing
 * -- Create date : 21 June 2017
 * -- Description : controller file for manufacturing master
 *
 * --REVISION HISTORY
 * --Date: 13-Oct 2016 : file created
 *
 * -- =============================================
 **/
defined('BASEPATH') OR exit('No direct script access allowed');

class Mfq_masters extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Mfq_master_model');
        $this->load->helper('mfq');
    }

    function save_itemCategory()
    {
        $this->form_validation->set_rules('description', 'Category Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));

        } else {
            $masterID = $this->input->post('masterID');
            $levelNo = $this->input->post('levelNo');
            $data['description'] = $this->input->post('description');
            $data['categoryType'] = $this->input->post('categoryType');
            $data['masterID'] = !empty($masterID) ? $masterID : 0;
            $data['levelNo'] = !empty($levelNo) ? $levelNo : 1;

            echo json_encode($this->Mfq_master_model->save_itemCategory($data));
        }

    }

    function load_mfq_category()
    {
        $categoryType = $this->input->post('categoryType');
        $result = $this->Mfq_master_model->load_mfq_category_all($categoryType);
        $data['category'] = $result;
        $this->load->view('system/mfq/ajax/load_mfq_category', $data);
    }

    function update_itemCategory()
    {
        $this->form_validation->set_rules('description', 'Category Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));

        } else {
            echo json_encode($this->Mfq_master_model->update_itemCategory());
        }
    }

}