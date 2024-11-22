<?php

class MFQ_UserGroup extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_Usergroup_model');
    }

    function fetch_usergroup()
    {
        $this->datatables->select('userGroupID,srp_erp_mfq_usergroups.description,srp_erp_mfq_usergroups.isDefault,isActive,IFNULL(CASE	WHEN groupType = "1" THEN
	"RFQ"
	WHEN groupType = "2" THEN
	"Estimate"
	WHEN groupType = "3" THEN
	"Job Card"
	END,\'-\')  as grouptyperej,
    IFNULL(srp_erp_segment.segmentCode,\'-\') as segmentCode', false)
            ->from('srp_erp_mfq_usergroups')
            ->join('srp_erp_mfq_segment mfqsegment','mfqsegment.mfqSegmentID = srp_erp_mfq_usergroups.segmentID','left')
            ->join('srp_erp_segment','srp_erp_segment.segmentID	 = mfqsegment.segmentID','left');
        $this->datatables->add_column('status', '$1', 'usergroupstatus(isActive)');
        $this->datatables->add_column('type', '$1', 'isdefaultstatus(isDefault)');
        $this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="add_userGroupDetail($1)"><span title="Add" rel="tooltip" class="fa fa-plus"></span></a> &nbsp; |&nbsp;  <span class="pull-right"><a href="#" onclick="edit_usergroup($1)"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a> |&nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="delete_userGroupDetaildatatable($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>&nbsp;&nbsp;</a> ', 'userGroupID');
        echo $this->datatables->generate();

    }

    function fetch_employees()
    {
        $this->datatables->select('empdetails.Ename2 as employeename,empdetails.EEmail as email, empdetails.EIdNo as primaryKey', false)
            ->from('srp_employeesdetails as empdetails')
            ->where('Erp_companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_usergroupdetails WHERE srp_erp_mfq_usergroupdetails.empID = empdetails.EIdNo AND Erp_companyID =' . current_companyID() . ' AND userGroupID =  ' . $this->input->post('userGroupID') . ')');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'primaryKey');
        echo $this->datatables->generate();
    }

    function save_mfq_user()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_Usergroup_model->save_mfq_usergroup());
        }
    }

    function edit_mfq_user()
    {
        echo json_encode($this->MFQ_Usergroup_model->edit_mfq_usergroup());
    }

    function link_employee()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Employee', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_Usergroup_model->link_employees());
        }

    }

    function fetch_savedusergroup()
    {
        $usergroup = $this->input->post('userGroupID');

        $this->datatables->select('srp_erp_mfq_usergroupdetails.employeeNavigationID as employeeNavigationID,srp_employeesdetails.EIdNo as primaryKey,srp_employeesdetails.Ename2 as employeename,srp_employeesdetails.EEmail as email');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_mfq_usergroupdetails.empID', 'left');
        $this->datatables->from('srp_erp_mfq_usergroupdetails');
        $this->datatables->where('userGroupID', $usergroup);
        $this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="delete_userGroupDetail($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;', 'employeeNavigationID');
        echo $this->datatables->generate();

    }

    function delete_employee()
    {
        echo json_encode($this->MFQ_Usergroup_model->delete_employees());
    }

    function delete_details_group_table()
    {
        echo json_encode($this->MFQ_Usergroup_model->delete_group_detail());
    }
}
