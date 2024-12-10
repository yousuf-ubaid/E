<?php

class Mfq_standard_details extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Mfq_standard_details_modal');
    }

    function fetch_standard_details()
    {
        $this->datatables->select("mfqStandardDetailMasterID,Description,case typeID when 1 then 'Terms and Condition' when 2 then 'Warranty' when 3 then 'Validity' end as typeID", false)
            ->from('srp_erp_mfq_standarddetailsmaster');
        $this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="edit_standard_details($1)"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a> |&nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="delete_standard_details($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>&nbsp;&nbsp;</a>', 'mfqStandardDetailMasterID');
        echo $this->datatables->generate();
    }

    function save_mfq_standard_details()
    {
        echo json_encode($this->Mfq_standard_details_modal->save_mfq_standard_details());
    }

    function delete_standard_details()
    {
        echo json_encode($this->Mfq_standard_details_modal->delete_mfq_standard_details());
    }

    function load_mfq_standard_details()
    {
        echo json_encode($this->Mfq_standard_details_modal->load_mfq_standard_details());
    }
}
