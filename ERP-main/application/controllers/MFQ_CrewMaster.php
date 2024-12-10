<?php

class MFQ_CrewMaster extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_crew_model');
    }

    function fetch_crew()
    {
        $this->datatables->select('rowTbl.Ename1 as mastername, masterTbl.Ename1  AS  Ename1, masterTbl.crewID as crewID, masterTbl.EIdNo as EIdNo, masterTbl.serialNo as  serialNo, masterTbl.Gender AS  Gender, masterTbl.EpTelephone AS EpTelephone, masterTbl.EEmail AS EEmail, masterTbl.isFromERP as isFromERP, masterTbl.designation AS Designation', false)
            ->from('srp_erp_mfq_crews as masterTbl')
            ->join('srp_employeesdetails rowTbl','rowTbl.EIdNo = masterTbl.EIdNo', 'left')
            ->join('srp_designation designation','designation.DesignationID = masterTbl.EmpDesignationId', 'left')
            ->where('masterTbl.Erp_companyID', current_companyID());
        $this->datatables->add_column('genderDiv', '$1', 'gender_ico(Gender)');
        $this->datatables->add_column('edit', '$1', 'edit_mfq_crew(crewID, isFromERP)');

        $result = $this->datatables->generate();
        echo $result;
    }

    function fetch_sync_crew()
    {
        $this->datatables->select('masterTbl.*, masterTbl.EIdNo as primaryKey , masterTbl.Gender as genderData', false)
            ->from('srp_employeesdetails as masterTbl')
            ->where('Erp_companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_crews WHERE srp_erp_mfq_crews.EIdNo = masterTbl.EIdNo AND Erp_companyID =' . current_companyID() . ' )');

        $this->datatables->add_column('genderDiv', '$1', 'gender_ico(genderData)');
        $this->datatables->add_column('edit', '$1', 'edit(primaryKey,isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'primaryKey');

        $result = $this->datatables->generate();
        echo $result;
    }
    function fetch_link_crew()
    {
        $this->datatables->select('masterTbl.*, masterTbl.EIdNo as primaryKey , masterTbl.Gender as genderData', false)
            ->from('srp_employeesdetails as masterTbl')
            ->where('Erp_companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_crews WHERE srp_erp_mfq_crews.EIdNo = masterTbl.EIdNo AND Erp_companyID =' . current_companyID() . ' )');

        $this->datatables->add_column('genderDiv', '$1', 'gender_ico(genderData)');
        $this->datatables->add_column('edit', '$1', 'edit(primaryKey,isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="linkItem_$1" name="linkItem" type="radio"  value="$1" class="radioChk" data-itemAutoID="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'primaryKey');

        $result = $this->datatables->generate();
        echo $result;
    }

    function add_crews()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_crew_model->add_crews());
        }
    }


    function add_edit_crew()
    {
        $crewID = $this->input->post('crewID');

        $this->form_validation->set_rules('Ename1', 'Name', 'trim|required');
        $this->form_validation->set_rules('Gender', 'Gender', 'trim|required');
        $this->form_validation->set_rules('EEmail', 'Email', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            if ($crewID) {
                /** Update */
                echo json_encode($this->MFQ_crew_model->update_crew());
            } else {
                /** Insert */
                echo json_encode($this->MFQ_crew_model->insert_crew());
            }
        }
    }

    function loadCrewDetail()
    {
        $result = $this->MFQ_crew_model->get_srp_erp_mfq_crews();
        if (!empty($result)) {
            echo json_encode(array_merge(array('error' => 0, 'message' => 'loading crew detail'), $result));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'record not found!'));
        }
    }
    function link_crew()
    {
        $this->form_validation->set_rules('selectedItemsSync', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_crew_model->link_crew());
        }

    }
}
