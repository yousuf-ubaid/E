<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mis_model extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function add_report_posting(){

        $doc_id = $this->input->post('doc_id');
        $mis_report_type = $this->input->post('mis_report_type');
        $report_name = $this->input->post('report_name');
        $date = $this->input->post('IncidateDateTo');
        $data = array();

        if($doc_id){

            $data['report_id'] = $doc_id;
            $data['report_name'] = $report_name;
            $data['type'] = $mis_report_type;
            $data['status'] = 0;
            $data['date'] = $date;
            $data['added_by'] = $this->common_data['current_userID'];
            $data['company_id'] = current_companyID();

            $res = $this->db->insert('srp_erp_mis_report',$data);

            $this->session->set_flashdata('s', 'Successfully added the record');
            return array('status' => true,'id' => $this->db->insert_id());
        }

    }

    function get_config_row($config_row_id){
        $record = $this->db->from('srp_erp_mis_report_config_rows')->where('id',$config_row_id)->get()->row_array();

        if($record){
            return $record;
        }
    }

    function add_config_rows(){
        
        $config_id = $this->input->post('config_id'); //not use
        $report_id = $this->input->post('report_id'); 
        $header_type1 = $this->input->post('header_type1');
        $header_type2 = $this->input->post('header_type2');
        $cat_id = $this->input->post('cat_id');
        $type = $this->input->post('type');
        $cat_description = $this->input->post('cat_description');
        $config_row_id = $this->input->post('config_row_id');
        $sort_order = $this->input->post('sort_order');
        $company_id = $this->common_data['company_data']['company_id'];
        $data = array();

        $data['config_id'] = $config_id;
        $data['header_type1'] = $header_type1;
        $data['header_type2'] = $header_type2;
        $data['report_id'] = $report_id;
        $data['cat_id'] = $cat_id;
        $data['cat_description'] = $cat_description;
        $data['sort_order'] = ($sort_order) ? $sort_order : 1;

        if($type == 'add'){

            $data['date'] = $this->common_data['current_date'];
            $data['added_by'] = $this->common_data['current_userID'];

            $res = $this->db->insert('srp_erp_mis_report_config_rows',$data);

            $this->session->set_flashdata('s', 'Successfully added the record');
            return array('status' => true);

        }elseif($type == 'edit'){

            $data['updated_date'] = $this->common_data['current_date'];
            $data['updated_by'] = $this->common_data['current_userID'];

            $res = $this->db->where('id',$config_row_id)->update('srp_erp_mis_report_config_rows',$data);

            $this->session->set_flashdata('s', 'Successfully updated the record');
            return array('status' => true);

        }

    }

    function delete_config_row(){

        $config_row_id = $this->input->post('config_row_id');

        try {

            $this->db->delete('srp_erp_mis_report_config_rows', array('id' => $config_row_id)); 

            $this->session->set_flashdata('s', 'Successfully deleted the record');
            return array('status' => true);

        }catch(Exception $e){
            $this->session->set_flashdata('e', $e->getMessage());
            return array('status' => false);
        }

    }

    function add_config_chartofaccount(){

        $config_row_id = $this->input->post('config_row_id');
        $report_id = $this->input->post('report_id');
        $selected_chart_of_account = $this->input->post('selected_chart_of_account');
        $selected_chart_of_account_text = $this->input->post('selected_chart_of_account_text');
        $is_chartofaccount = $this->input->post('is_chartofaccount');
        $data = array();

        if($is_chartofaccount == 1){
            //
            $chart_of_account_details = explode(' | ',$selected_chart_of_account_text);

            $ex_added = $this->chart_of_account_added(1,$chart_of_account_details[0]);

            if($ex_added){
                $this->session->set_flashdata('e', "Already Chart of account - $chart_of_account_details[0] added to this report");
                return array('status' => false);
            }

            $data['config_row_id'] = $config_row_id;
            $data['report_id'] = ($report_id) ? $report_id : 1;
            $data['gl_auto_id'] = $selected_chart_of_account;
            $data['gl_code'] = $chart_of_account_details[0];
            $data['gl_code_description'] = $chart_of_account_details[2];
            $data['date'] = $this->common_data['current_date'];
            $data['added_by'] = $this->common_data['current_userID'];
            $data['is_gl_code'] = $is_chartofaccount;
            $data['value'] = '1';

            $res = $this->db->insert('srp_erp_mis_report_config_chartofaccounts',$data);

            $this->session->set_flashdata('s', "Successfully added the Chart of account $chart_of_account_details[0]");
            return array('status' => true);

        }

    }

    function chart_of_account_added($report_id,$gl_code){

        $data = $this->db->where('report_id',$report_id)->where('gl_code',$gl_code)->from('srp_erp_mis_report_config_chartofaccounts')->get()->row_array();

        if($data){
            return TRUE;
        }

    }

    function add_config_detail(){

        $config_row_id = $this->input->post('config_row_id');
        $added_headers = $this->input->post('added_headers');
        $plus_minus = $this->input->post('plus_minus');
        $data = array();

        $ex_record = get_config_row_details($config_row_id,$added_headers);

        if($ex_record){
            $this->session->set_flashdata('e', "Already added the record");
            return array('status' => true);
        }

        if($config_row_id){

            $data['config_row_id'] = $config_row_id;
            $data['mapped_row_id'] = $added_headers;
            $data['value'] = $plus_minus;
            $data['date'] = $this->common_data['current_date'];
            $data['added_by'] = $this->common_data['current_userID'];
            $data['company_id'] = current_companyID();

            $res = $this->db->insert('srp_erp_mis_report_config_details',$data);

            $this->session->set_flashdata('s', "Successfully added the record");
            return array('status' => true);
        }

    }

    function delete_config_row_detail(){

        $detail_id = $this->input->post('config_detail_id');

        try {

            $this->db->delete('srp_erp_mis_report_config_details', array('id' => $detail_id)); 

            $this->session->set_flashdata('s', 'Successfully deleted the record');
            return array('status' => true);

        }catch(Exception $e){
            $this->session->set_flashdata('e', $e->getMessage());
            return array('status' => false);
        }

    }

    function delete_added_chart_of_account(){
        
        $id = $this->input->post('config_row_id');

        try {

            $this->db->delete('srp_erp_mis_report_config_chartofaccounts', array('id' => $id)); 

            $this->session->set_flashdata('s', 'Successfully deleted the record');
            return array('status' => true);

        }catch(Exception $e){
            $this->session->set_flashdata('e', $e->getMessage());
            return array('status' => false);
        }
    }



}