<?php
class MFQ_Report extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_Report_model');
    }

    function get_all_job_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date To is required
            </div>';
        } else {
            $data["details"] = $this->MFQ_Report_model->get_all_job_report();
            $data["type"] = "html";
            echo $this->load->view('system/mfq/ajax/load_all_job_report', $data, true);
        }
    }

    function get_all_job_report_pdf()
    {
        $data["details"] = $this->MFQ_Report_model->get_all_job_report();
        $data["type"] = "pdf";
        $html = $this->load->view('system/mfq/ajax/load_all_job_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function get_unbilled_job_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date To is required
            </div>';
        } else {
            $data["details"] = $this->MFQ_Report_model->get_unbilled_job_report();
            $data["type"] = "html";
            echo $this->load->view('system/mfq/ajax/load_unbilled_job_report', $data, true);
        }
    }

    function get_unbilled_job_report_pdf()
    {
        $data["details"] = $this->MFQ_Report_model->get_unbilled_job_report();
        $data["type"] = "pdf";
        $html = $this->load->view('system/mfq/ajax/load_unbilled_job_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }
}