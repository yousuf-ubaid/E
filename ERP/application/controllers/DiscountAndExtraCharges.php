<?php

class DiscountAndExtraCharges extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('DiscountAndExtraCharges_modal');
        $this->load->helper('discount_extra_charges');
    }

    function fetch_discount_extra_charges(){
        $companyid = $this->common_data['company_data']['company_id'];

        $where = "srp_erp_discountextracharges.companyID = " . $companyid . "";
        $this->datatables->select("discountExtraChargeID,Description,type,isChargeToExpense,glCode,srp_erp_chartofaccounts.GLDescription as GLDescription,srp_erp_chartofaccounts.GLSecondaryCode as GLSecondaryCode,srp_erp_chartofaccounts.systemAccountCode as systemAccountCode,isTaxApplicable");
        $this->datatables->from('srp_erp_discountextracharges');
        $this->datatables->join('srp_erp_chartofaccounts ', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_discountextracharges.glCode','left');
        $this->datatables->where($where);
        $this->datatables->add_column('isChargeToExp', '$1', 'confirm(isChargeToExpense)');
        $this->datatables->add_column('isTaxAppl', '$1', 'confirm(isTaxApplicable)');
        $this->datatables->add_column('typeDesc', '<div class="pull-left">$1</div>', 'discountType(type)');
        $this->datatables->add_column('glView', '$1', 'glCodeDesc(GLDescription,systemAccountCode,GLSecondaryCode)');
        $this->datatables->add_column('edit', '$1', 'action_disc_and_extra(discountExtraChargeID)');
        echo $this->datatables->generate();
    }

    function saveDiscountCategory(){
        $type=$this->input->post('type');
        $isChargeToExpenseval=$this->input->post('isChargeToExpenseval');
        $this->form_validation->set_rules('type', 'Type', 'trim|required');
        $this->form_validation->set_rules('Description', 'Description', 'trim|required');
        if($this->input->post('type')==1){
            if($isChargeToExpenseval==1){
                $this->form_validation->set_rules('glCode', 'GL Code', 'trim|required');
            }
        }else{
            $this->form_validation->set_rules('glCode', 'GL Code', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode( array('e', validation_errors()));
        } else {
            echo json_encode($this->DiscountAndExtraCharges_modal->saveDiscountCategory());
        }

    }

    function delete_discount_category(){
        echo json_encode($this->DiscountAndExtraCharges_modal->delete_discount_category());
    }

    function getDiscount(){
        echo json_encode($this->DiscountAndExtraCharges_modal->getDiscount());
    }

    

}