<?php defined('BASEPATH') OR exit('No direct script access allowed');

class OverTimeSlab extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Over_time_slab_modal');
    }

    function fetch_over_time_slab()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_ot_slabsmaster.companyID = " . $companyid .  "";
        $this->datatables->select("otSlabsMasterID,Description,transactionCurrencyID,srp_erp_currencymaster.CurrencyCode as CurrencyCode");
        $this->datatables->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_ot_slabsmaster.transactionCurrencyID');
        $this->datatables->from('srp_erp_ot_slabsmaster');
        $this->datatables->where($where);
        $this->datatables->add_column('edit', '$1', 'load_OT_slab_action(otSlabsMasterID)');
        echo $this->datatables->generate();
    }

    function save_over_time_slab_header()
    {
        $this->form_validation->set_rules('Description', 'Description', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Over_time_slab_modal->save_over_time_slab_header());
        }
    }

    function fetch_over_time_slab_details()
    {
        echo json_encode($this->Over_time_slab_modal->fetch_over_time_slab_details());
    }

    function laad_over_time_slab_header()
    {
        echo json_encode($this->Over_time_slab_modal->laad_over_time_slab_header());
    }

    function load_sover_time_slab_endhour()
    {
        echo json_encode($this->Over_time_slab_modal->load_sover_time_slab_endhour());
    }

    function save_over_time_slab_detail()
    {
        $this->form_validation->set_rules('startHour', 'Start Hour', 'trim|required');
        $this->form_validation->set_rules('EndHour', 'End Hour', 'trim|required');
        $this->form_validation->set_rules('hourlyRate', 'Hourly Rate', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Over_time_slab_modal->save_over_time_slab_detail());
        }
    }

    function delete_over_time_slab_detail()
    {
        echo json_encode($this->Over_time_slab_modal->delete_over_time_slab_detail());
    }


}