<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class QuotationPortal_modal extends CI_Model
{
    function __contruct()
    {
        parent::__contruct();
    }
    function save_sales_quotation_customer_feedback()
    {

        $value = $this->input->post('value');
        $quatationId = $this->input->post('quatationId');
        $companyID = $this->input->post('companyID');
        $comments = $this->input->post('comments');

        $this->db->select('*');
        $this->db->where("company_id",$companyID);
        $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
        $resultDb2 = $this->db->get("user")->row_array();
        $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
        $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
        $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
        $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';
        $config['cachedir'] = '';
        $config['swap_pre'] = '';
        $config['encrypt'] = FALSE;
        $config['compress'] = FALSE;
        $config['stricton'] = FALSE;
        $config['failover'] = array();
        $config['save_queries'] = TRUE;
        $databaseresult = $this->load->database($config, True);

        $valueaccept = $this->input->post('value');
        $companyid = $this->input->post('companyID');
        $quatationId = $this->input->post('quatationId');

        $data['approvedYN'] = $valueaccept;
        $data['approvalComment'] = $comments;
        $databaseresult->where('companyID', $companyid);
       $databaseresult->where('quotationAutoID', $quatationId);
       $databaseresult->update('srp_erp_crm_quotation', $data);
       $databaseresult->trans_complete();
        if ($databaseresult->trans_status() === FALSE) {
            $databaseresult->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $databaseresult->trans_commit();
            return array('s', 'Sales Quotation Accept Successfully');
        }

    }
    function save_sales_quotation_customer_feedback_comment()
    {
        $value = $this->input->post('comment');
        $quatationId = $this->input->post('quatationId');
        $companyID = $this->input->post('companyID');

        $this->db->select('*');
        $this->db->where("company_id",$companyID);
        $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
        $resultDb2 = $this->db->get("user")->row_array();
        $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
        $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
        $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
        $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';
        $config['cachedir'] = '';
        $config['swap_pre'] = '';
        $config['encrypt'] = FALSE;
        $config['compress'] = FALSE;
        $config['stricton'] = FALSE;
        $config['failover'] = array();
        $config['save_queries'] = TRUE;
        $databaseresult = $this->load->database($config, True);


        $data['approvalComment'] = $value;
        $databaseresult->where('companyID', $companyID);
        $databaseresult->where('quotationAutoID', $quatationId);
        $databaseresult->update('srp_erp_crm_quotation', $data);
        $databaseresult->trans_complete();
        if ($databaseresult->trans_status() === FALSE) {
            $databaseresult->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $databaseresult->trans_commit();
            return array('s', 'Sales Quotation Comment Added Successfully');
        }
    }
    function save_sales_qutation_accepted_item()
    {
        $DetailID = $this->input->post('DetailID');
        $val = $this->input->post('val');
        $companyID = $this->input->post('companyID');

        $this->db->select('*');
        $this->db->where("company_id",$companyID);
        $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
        $resultDb2 = $this->db->get("user")->row_array();
        $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
        $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
        $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
        $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';
        $config['cachedir'] = '';
        $config['swap_pre'] = '';
        $config['encrypt'] = FALSE;
        $config['compress'] = FALSE;
        $config['stricton'] = FALSE;
        $config['failover'] = array();
        $config['save_queries'] = TRUE;
        $databaseresult = $this->load->database($config, True);


       if($val==1)
       {
           $data['selectedByCustomer'] = 1;
       } else
       {
           $data['selectedByCustomer'] = 0;
       }

        $databaseresult->where('companyID', $companyID);
        $databaseresult->where('contractDetailsAutoID', $DetailID);
        $databaseresult->update('srp_erp_crm_quotationdetails', $data);
        $databaseresult->trans_complete();
        if ($databaseresult->trans_status() === FALSE) {
            $databaseresult->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $databaseresult->trans_commit();
            return array('s', 'Sales Quotation Item Added Successfully');
        }
    }
    function save_sales_qutation_accepted_item_remarks()
    {
        $DetailID = $this->input->post('DetailID');
        $val = $this->input->post('val');
        $companyID = $this->input->post('companyID');

        $this->db->select('*');
        $this->db->where("company_id",$companyID);
        $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
        $resultDb2 = $this->db->get("user")->row_array();
        $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
        $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
        $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
        $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';
        $config['cachedir'] = '';
        $config['swap_pre'] = '';
        $config['encrypt'] = FALSE;
        $config['compress'] = FALSE;
        $config['stricton'] = FALSE;
        $config['failover'] = array();
        $config['save_queries'] = TRUE;
        $databaseresult = $this->load->database($config, True);


        $data['remarks'] = $val;
        $databaseresult->where('companyID', $companyID);
        $databaseresult->where('contractDetailsAutoID', $DetailID);
        $databaseresult->update('srp_erp_crm_quotationdetails', $data);
        $databaseresult->trans_complete();
        if ($databaseresult->trans_status() === FALSE) {
            $databaseresult->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $databaseresult->trans_commit();
            return array('s', 'Sales Quotation Remarks Saved Successfully');
        }
    }



}