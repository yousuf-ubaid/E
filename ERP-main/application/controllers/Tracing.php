<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tracing extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Tracing_modal');
    }

    function trace_po_document(){
        echo json_encode($this->Tracing_modal->trace_po_document());
    }

    function trace_customer_order_document(){
        echo json_encode($this->Tracing_modal->trace_customer_order_document());
    }

    function select_tracing_documents(){
        $data['purchaseOrderID']=$this->input->post('purchaseOrderID');
        $data['DocumentID']=$this->input->post('DocumentID');
        $html = $this->load->view('system/tracing/tracing_view', $data, true);
        echo $html;
    }
    function get_tracing_data(){
        //echo json_encode($this->Tracing_modal->get_tracing_data());
        return $this->Tracing_modal->get_tracing_data();
    }

    function deleteDocumentTracing(){
        echo json_encode($this->Tracing_modal->deleteDocumentTracing());
    }

    function trace_pr_document(){
        echo json_encode($this->Tracing_modal->trace_pr_document());
    }

    function trace_grv_document(){
        echo json_encode($this->Tracing_modal->trace_grv_document());
    }

    function trace_bsi_document(){
        echo json_encode($this->Tracing_modal->trace_bsi_document());
    }

    function trace_cnt_document(){
        echo json_encode($this->Tracing_modal->trace_cnt_document());
    }

    function trace_cinv_document(){
        echo json_encode($this->Tracing_modal->trace_cinv_document());
    }

    function trace_hcinv_document(){
        echo json_encode($this->Tracing_modal->trace_hcinv_document());
    }

    function trace_do_document(){
        echo json_encode($this->Tracing_modal->trace_do_document());
    }

    function trace_slr_document(){
        echo json_encode($this->Tracing_modal->trace_slr_document());
    }

    function trace_cn_document(){
        echo json_encode($this->Tracing_modal->trace_cn_document());
    }

    function trace_dn_document(){
        echo json_encode($this->Tracing_modal->trace_dn_document());
    }


    function trace_mr_document(){
        echo json_encode($this->Tracing_modal->trace_mr_document());
    }
    function trace_mi_document(){
        echo json_encode($this->Tracing_modal->trace_mi_document());
    }
    function trace_rv_document(){
        echo json_encode($this->Tracing_modal->trace_rv_document());
    }

    function trace_job_document(){
        echo json_encode($this->Tracing_modal->trace_job_document());
    }

    function trace_MDN_document(){
        echo json_encode($this->Tracing_modal->trace_MDN_document());
    }

    function trace_MCINV_document(){
        echo json_encode($this->Tracing_modal->trace_MCINV_document());
    }
}