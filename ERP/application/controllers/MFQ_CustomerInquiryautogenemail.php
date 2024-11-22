<?php

class MFQ_CustomerInquiryautogenemail extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_CustomerInquiryautogen_model');
    }

    public function automatedemailmanufacturingcustomerinquiry()
    {
        echo json_encode($this->MFQ_CustomerInquiryautogen_model->automatedemailmanufacturingcustomerinquiry());
    }
}
