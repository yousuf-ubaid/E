<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Operation extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Operation_modal');
        $this->load->helpers('operation');
    }

    function save_contract_master()
    {
        $this->form_validation->set_rules('contractType', 'Contract Type', 'trim|required');
        $this->form_validation->set_rules('clientID', 'Customer Name', 'trim|required');
        $this->form_validation->set_rules('ContractNumber', 'Contract/Quotation Ref #', 'trim|required');
        $this->form_validation->set_rules('ServiceLineCode', 'Department', 'trim|required');
        $this->form_validation->set_rules('ContStartDate', 'Contract Start Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('ContEndDate', 'Contract End Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('contractStatus', 'Contract Status', 'trim|required');
        $this->form_validation->set_rules('ContCurrencyID', 'Contract Currency ID', 'trim|required');
        $this->form_validation->set_rules('contValue', 'Contract Value', 'trim|required');
        $this->form_validation->set_rules('productGLCode', 'Product GL Code', 'trim|required');
        $this->form_validation->set_rules('serviceGLCode', 'Service GL Code', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Operation_modal->save_contract_master());
        }
    }

    function fetch_contract_master_table(){

        $convertFormat = convert_date_format_sql();
        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');

        $customer_filter = '';

        if(!empty($customer)){
            $customer_filter = " AND clientID = " . $customer;
        }

        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
           // $searches = " AND (( ContractNumber Like '%$search%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (comments Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (creditNoteDate Like '%$sSearch%')) ";
        }

        $where = "contractmaster.CompanyID = " . $companyid . $customer_filter."";
        $this->datatables->select('contractUID,ContractNumber,srp_erp_segment.description as Department,DATE_FORMAT(ContStartDate,\'' . $convertFormat . '\') AS ContStartDate,DATE_FORMAT(ContEndDate,\'' . $convertFormat . '\') AS ContEndDate,contracttype.description as conType,approvedYN,contractStatus,productGLCode,serviceGLCode,contractmaster.createdUserID,contractmaster.confirmedByEmpID,contractmaster.confirmedYN as confirmedYN,srp_erp_customermaster.customerName as customerName');

        $this->datatables->where($where);
        $this->datatables->from('contractmaster');
        $this->datatables->join('srp_erp_segment','srp_erp_segment.segmentID = contractmaster.ServiceLineCode');
        $this->datatables->join('contracttype','contracttype.contractTypeId = contractmaster.contractType');
        $this->datatables->join('srp_erp_customermaster','srp_erp_customermaster.customerAutoID = contractmaster.clientID');
        $this->datatables->add_column('contractStatuss', '$1', 'check_contract_status(contractStatus)');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"OPCNT",contractUID)');
        $this->datatables->add_column('contractApproved', '$1', 'document_approval_drilldown(approvedYN,"OPCNT",contractUID)');
        $this->datatables->add_column('edit', '$1', 'load_opcontract_action(contractUID,productGLCode,serviceGLCode,approvedYN,createdUserID,confirmedByEmpID,confirmedYN)');
        echo $this->datatables->generate();
    }

    function get_contract_master_edit(){
        echo json_encode($this->Operation_modal->get_contract_master_edit());
    }

    function fetch_contract_detail_table(){

        $companyid = $this->common_data['company_data']['company_id'];
        $contractUID = $this->input->post('contractUID');

        $where = "contractdetails.CompanyID = " . $companyid ." AND contractUID = " . $contractUID ."";
        $this->datatables->select('ContractDetailID,ClientRef,OurRef,ItemDescrip,TypeID,contractdetails.UnitID as UnitID,RateCurrencyID,standardRate,srp_erp_unit_of_measure.UnitDes as UnitDes,srp_erp_currencymaster.CurrencyCode as CurrencyCode,contractUID');
        $this->datatables->where($where);
        $this->datatables->from('contractdetails');
        $this->datatables->join('srp_erp_unit_of_measure','srp_erp_unit_of_measure.UnitID = contractdetails.UnitID');
        $this->datatables->join('srp_erp_currencymaster','srp_erp_currencymaster.currencyID = contractdetails.RateCurrencyID');

        $this->datatables->add_column('contractDType', '$1', 'check_contract_Detail_Type(TypeID)');
        $this->datatables->add_column('editConDetail', '$1', 'load_opcontract_detail_action(ContractDetailID,contractUID)');
        echo $this->datatables->generate();
    }


    function save_contract_details()
    {

        $this->form_validation->set_rules('ClientRef', 'Client Reference', 'trim|required');
        $this->form_validation->set_rules('OurRef', 'Company Reference', 'trim|required');
        $this->form_validation->set_rules('ItemDescrip', 'Item Description', 'trim|required');
        $this->form_validation->set_rules('TypeID', 'Type', 'trim|required');
        $this->form_validation->set_rules('UnitID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('RateCurrencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('standardRate', 'Product Rate', 'trim|required');
        $this->form_validation->set_rules('GLCode', 'GL Code', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Operation_modal->save_contract_details());
        }
    }

    function get_contract_detail_edit(){
        echo json_encode($this->Operation_modal->get_contract_detail_edit());
    }

    function delete_contrct_detail(){
        echo json_encode($this->Operation_modal->delete_contrct_detail());
    }

    function confirm_opcontrct(){
        echo json_encode($this->Operation_modal->confirm_opcontrct());
    }

    function fetch_contract_approval()
    {
        /** rejected = 1* not rejected = 0* */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $companyID = $this->common_data['company_data']['company_id'];
            $this->datatables->select('contractmaster.contractUID as contractUID,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(ContStartDate,\'' . $convertFormat . '\') AS ContStartDate,DATE_FORMAT(ContEndDate,\'' . $convertFormat . '\') AS ContEndDate,ContractNumber,srp_erp_segment.description as Department,contracttype.description as conType');
            $this->datatables->from('contractmaster');
            $this->datatables->join('srp_erp_segment','srp_erp_segment.segmentID = contractmaster.ServiceLineCode');
            $this->datatables->join('contracttype','contracttype.contractTypeId = contractmaster.contractType');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = contractmaster.contractUID AND srp_erp_documentapproved.approvalLevelID = contractmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = contractmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'OPCNT');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'OPCNT');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('contractmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);

            $this->datatables->add_column('grvPrimaryCode', '$1', 'approval_change_modal(grvPrimaryCode,grvAutoID,documentApprovedID,approvalLevelID,approvedYN,GRV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "OPCNT", contractUID)');
            $this->datatables->add_column('edit', '$1', 'contract_action_approval(contractUID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $companyID = $this->common_data['company_data']['company_id'];
            $this->datatables->select('contractmaster.contractUID as contractUID,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(ContStartDate,\'' . $convertFormat . '\') AS ContStartDate,DATE_FORMAT(ContEndDate,\'' . $convertFormat . '\') AS ContEndDate,ContractNumber,srp_erp_segment.description as Department,contracttype.description as conType');

            $this->datatables->from('contractmaster');
            $this->datatables->join('srp_erp_segment','srp_erp_segment.segmentID = contractmaster.ServiceLineCode');
            $this->datatables->join('contracttype','contracttype.contractTypeId = contractmaster.contractType');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = contractmaster.contractUID');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'OPCNT');
            $this->datatables->where('contractmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('contractmaster.contractUID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "OPCNT", contractUID)');
            $this->datatables->add_column('edit', '$1', 'contract_action_approval(contractUID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function save_contract_approval(){
        $system_code = trim($this->input->post('contractUID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'OPCNT', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('contractUID');
                $this->db->where('contractUID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('contractmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Operation_modal->save_contract_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('contractUID');
            $this->db->where('contractUID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('contractmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'OPCNT', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Operation_modal->save_contract_approval());
                    }
                }
            }
        }
    }

    function load_contract_master_view()
    {
        $contractUID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractUID') ?? '');
        $data['extra'] = $this->Operation_modal->fetch_contract_master_detail_data($contractUID);
        $data['logo']=mPDFImage;
        $data['approval'] = $this->input->post('approval');
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/operations/erp_contract_master_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function loard_contract_detail_drop(){
        echo json_encode($this->Operation_modal->loard_contract_detail_drop());
    }

    function save_calloff()
    {

        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasure', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('createdDate', 'Created Date', 'trim|required');
        $this->form_validation->set_rules('expiryDate', 'Expired Date', 'trim|required');
        $this->form_validation->set_rules('length', 'Length', 'trim|required');
        $this->form_validation->set_rules('drawingNo', 'Drawing No', 'trim|required');
        $this->form_validation->set_rules('location', 'Location', 'trim|required');
        $this->form_validation->set_rules('WellNo', 'Well No', 'trim|required');
        $this->form_validation->set_rules('joints', 'Joints', 'trim|required');
        $this->form_validation->set_rules('RDX', 'RDX', 'trim|required');
        $this->form_validation->set_rules('productId', 'Product/Service ', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Operation_modal->save_calloff());
        }
    }

    function load_calloff_tbl(){
        $companyid = $this->common_data['company_data']['company_id'];
        $contractUID = $this->input->post('contractUID');
        $convertFormat = convert_date_format_sql();

        $where = "op_calloff_master.companyID = " . $companyid ." AND op_calloff_master.contractUID = " . $contractUID ."";
        $this->datatables->select('op_calloff_master.calloffID as calloffID,description,op_calloff_master.contractUID as contractUID,length,RDX,WellNo,drawingNo,joints,DATE_FORMAT(op_calloff_master.createdDate,\'' . $convertFormat . '\') AS createdDate,DATE_FORMAT(op_calloff_master.expiryDate,\'' . $convertFormat . '\') AS expiryDate,fieldmaster.fieldName as fieldName,IFNULL(( SUM(IFNULL(ticket.lengths, 0 )) / op_calloff_master.length )* 100, 0 ) AS percentage ');
        $this->datatables->where($where);
        $this->datatables->from('op_calloff_master');
        $this->datatables->join('fieldmaster','fieldmaster.FieldID = op_calloff_master.location');
        $this->datatables->join('(SELECT IFNULL(SUM( product_service_details.Qty ),0) AS lengths ,contractRefNo,calloffID,ticketmaster.contractUID,product_service_details.contractDetailID FROM ticketmaster LEFT JOIN product_service_details ON product_service_details.ticketidAtuto = ticketmaster.ticketidAtuto  WHERE ticketmaster.companyID =  \'' . $companyid . '\' 	AND isDeleted <> 1 AND calloffID IS NOT NULL AND ticketmaster.contractUID = \'' . $contractUID . '\'    GROUP BY calloffID,contractDetailID) ticket', '(ticket.calloffID = op_calloff_master.calloffID AND op_calloff_master.productId = ticket.contractDetailID)', 'left');
        //$this->datatables->add_column('contractDType', '$1', 'check_contract_Detail_Type(TypeID)');
        $this->datatables->group_by('op_calloff_master.calloffID');
        $this->datatables->add_column('callofactn', '$1', 'load_calloff_action(calloffID,contractUID)');
        $this->datatables->add_column('calloffprogress', '$1', 'load_progressbar_calloff(calloffID,percentage)');
        echo $this->datatables->generate();
    }

    function get_call_off_edit(){
        echo json_encode($this->Operation_modal->get_call_off_edit());
    }

    function deleteCallOff(){
        echo json_encode($this->Operation_modal->deleteCallOff());
    }


    function save_job_master()
    {
        $this->form_validation->set_rules('clientID', 'Client', 'trim|required');
        $this->form_validation->set_rules('callOffBase', 'Calloff Base Y/N', 'trim|required');
        $this->form_validation->set_rules('contractRefNo', 'Contract Ref No', 'trim|required');
        $this->form_validation->set_rules('jobNetworkNo', 'Network Ref #/PO No', 'trim|required');
        $this->form_validation->set_rules('EngID', 'Supervisor', 'trim|required');
        if($this->input->post('callOffBase')==1){
            $this->form_validation->set_rules('calloffID', 'Call Offs', 'trim|required');
        }
        $this->form_validation->set_rules('primaryUnitAssetID', 'Location', 'trim|required');
        $this->form_validation->set_rules('wellNo', 'Well No', 'trim|required');
        $this->form_validation->set_rules('comments', 'Job Description', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Operation_modal->save_job_master());
        }
    }

    function load_job_ticket_table(){

        $companyid = $this->common_data['company_data']['company_id'];
        $contractUID = $this->input->post('contractUID');
        $convertFormat = convert_date_format_sql();

        $where = "ticketmaster.companyID = " . $companyid ." AND contractUID = " . $contractUID ."";
        $this->datatables->select('ticketidAtuto,ticketNo,Timedatejobstra,Timedatejobend,DATE_FORMAT(ticketmaster.createdDateTime,\'' . $convertFormat . '\') AS createdDate,comments,primaryUnitAssetID,regNo,wellNo,contractUID,fieldmaster.fieldName as fieldName,operationLog,callOffBase,ticketmaster.approvedYN as approvedYN,ticketmaster.confirmedYN as confirmedYN,ticketmaster.createdUserID as createdUserID,ticketmaster.confirmedByEmpID as confirmedByEmpID,proformaConfirmationYN');
        $this->datatables->where($where);
        $this->datatables->from('ticketmaster');
        $this->datatables->join('fieldmaster','fieldmaster.FieldID = ticketmaster.primaryUnitAssetID');
        //$this->datatables->join('srp_erp_currencymaster','srp_erp_currencymaster.currencyID = contractdetails.RateCurrencyID');

        $this->datatables->add_column('total_prod_servc', '$1', 'total_prod_servc_tkt(ticketidAtuto,contractUID)');
        $this->datatables->add_column('job_ticket_status', '$1', 'job_ticket_status_tkt(ticketidAtuto,contractUID,ticketStatus)');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"OPJOB",ticketidAtuto)');
        $this->datatables->add_column('ticketApproved', '$1', 'document_approval_drilldown(approvedYN,"OPJOB",ticketidAtuto)');
        $this->datatables->add_column('job_ticket_action', '$1', 'job_ticket_action(ticketidAtuto,contractUID,ticketStatus,approvedYN,confirmedYN,operationLog,callOffBase,createdUserID,confirmedByEmpID,proformaConfirmationYN)');
        echo $this->datatables->generate();
    }

    function deletejobticket(){
        echo json_encode($this->Operation_modal->deletejobticket());
    }

    function load_ticket_edit(){
        echo json_encode($this->Operation_modal->load_ticket_edit());
    }

    function update_jobDetail(){
        echo json_encode($this->Operation_modal->update_jobDetail());
    }

    function load_service_add_table(){//extracted from gearsstd db web_op_ipc_contractdetails view
        $companyid = $this->common_data['company_data']['company_id'];
        $contractUID = $this->input->post('contractUID');
        $convertFormat = convert_date_format_sql();

        $where = "contractdetails.CompanyID = " . $companyid ." AND contractUID = " . $contractUID ." AND TypeID = " . 2 ." ";
        $this->datatables->select('ContractDetailID,ClientRef,ItemDescrip,standardRate,contractdetails.UnitID as unid,RateCurrencyID,srp_erp_unit_of_measure.UnitDes as UnitDes,srp_erp_currencymaster.CurrencyCode as CurrencyCode');
        $this->datatables->where($where);
        $this->datatables->from('contractdetails');
        $this->datatables->join('srp_erp_unit_of_measure','srp_erp_unit_of_measure.UnitID = contractdetails.UnitID');
        $this->datatables->join('srp_erp_currencymaster','srp_erp_currencymaster.currencyID = contractdetails.RateCurrencyID');

        $this->datatables->add_column('service_action_add', '$1', 'service_action_add(ContractDetailID)');
        echo $this->datatables->generate();

    }

    function load_product_add_table(){//extracted from gearsstd db web_op_ipc_contractdetails view
        $companyid = $this->common_data['company_data']['company_id'];
        $contractUID = $this->input->post('contractUID');
        $convertFormat = convert_date_format_sql();

        $where = "contractdetails.CompanyID = " . $companyid ." AND contractUID = " . $contractUID ." AND TypeID = " . 1 ." ";
        $this->datatables->select('ContractDetailID,ClientRef,ItemDescrip,standardRate,contractdetails.UnitID as unid,RateCurrencyID,srp_erp_unit_of_measure.UnitDes as UnitDes,srp_erp_currencymaster.CurrencyCode as CurrencyCode');
        $this->datatables->where($where);
        $this->datatables->from('contractdetails');
        $this->datatables->join('srp_erp_unit_of_measure','srp_erp_unit_of_measure.UnitID = contractdetails.UnitID');
        $this->datatables->join('srp_erp_currencymaster','srp_erp_currencymaster.currencyID = contractdetails.RateCurrencyID');

        $this->datatables->add_column('service_action_lastYN', '$1', 'service_action_lastYN(ContractDetailID)');
        $this->datatables->add_column('service_action_add', '$1', 'product_action_add(ContractDetailID)');
        echo $this->datatables->generate();

    }

    function load_operation_system_log_hd(){
        echo json_encode($this->Operation_modal->load_operation_system_log_hd());
    }

    function updatehdFields(){
        echo json_encode($this->Operation_modal->updatehdFields());
    }

    function load_operationlogbody(){
        echo json_encode($this->Operation_modal->load_operationlogbody());
    }

    function addCrewRow(){
        echo json_encode($this->Operation_modal->addCrewRow());
    }

    function delete_oplog(){
        echo json_encode($this->Operation_modal->delete_oplog());
    }

    function update_op_system_log_detail(){
        echo json_encode($this->Operation_modal->update_op_system_log_detail());
    }

    function save_product_service_detail(){
        echo json_encode($this->Operation_modal->save_product_service_detail());
    }

    function load_ticket_service(){
        echo json_encode($this->Operation_modal->load_ticket_service());
    }

    function update_op_product_service(){
        echo json_encode($this->Operation_modal->update_op_product_service());
    }

    function delete_op_product_service(){
        echo json_encode($this->Operation_modal->delete_op_product_service());
    }

    function addCrewop(){
        echo json_encode($this->Operation_modal->addCrewop());
    }

    function load_op_crew_table(){
        $companyid = $this->common_data['company_data']['company_id'];
        $ticketidAtuto = $this->input->post('ticketidAtuto');
        $convertFormat = convert_date_format_sql();

        $where = "tickets_crew.companyID = " . $companyid ." AND tickets_crew.ticketidAtuto = " . $ticketidAtuto ." ";
        $this->datatables->select('crewgroup.groupName as groupName,ticketCrewID,crewID,ticketidAtuto,tickets_crew.companyID as companyID,srp_employeesdetails.Ename2 as crewname ');
        $this->datatables->where($where);
        $this->datatables->from('tickets_crew');
        $this->datatables->join('srp_employeesdetails','srp_employeesdetails.EIdNo = tickets_crew.crewID');
        $this->datatables->join('crewgroup','crewgroup.groupID = tickets_crew.groupID');
        $this->datatables->group_by("tickets_crew.crewID");
        $this->datatables->group_by("tickets_crew.groupID");
        $this->datatables->add_column('opCrewAction', '$1', 'op_action_crew(ticketCrewID)');

        echo $this->datatables->generate();
    }

    function deleteOpCrew(){
        echo json_encode($this->Operation_modal->deleteOpCrew());
    }

    function load_op_crew_history_table(){
        $companyid = $this->common_data['company_data']['company_id'];
        $ticketidAtuto = $this->input->post('ticketidAtuto');
        $convertFormat = convert_date_format_sql();

        $where = "tickets_crew_unit_history.companyID = " . $companyid ." AND tickets_crew_unit_history.type = 1 AND tickets_crew_unit_history.ticketidAtuto = " . $ticketidAtuto ." ";
        $this->datatables->select('ticketCrewHistoryID,description,DATE_FORMAT(tickets_crew_unit_history.createdDatetime,\'' . $convertFormat . '\') AS createdDate,acrw.Ename2 as crewname,acrwcreated.Ename2 as crewcreated');
        $this->datatables->where($where);
        $this->datatables->from('tickets_crew_unit_history');
        $this->datatables->join('srp_employeesdetails acrw','acrw.EIdNo = tickets_crew_unit_history.crewID');
        $this->datatables->join('srp_employeesdetails acrwcreated','acrwcreated.EIdNo = tickets_crew_unit_history.createdUserID');

        //$this->datatables->add_column('opCrewAction', '$1', 'op_action_crew(ticketCrewID)');

        echo $this->datatables->generate();
    }

    function addAssetunitop(){
        echo json_encode($this->Operation_modal->addAssetunitop());
    }

    function load_op_asset_unit_table(){
        $companyid = $this->common_data['company_data']['company_id'];
        $ticketidAtuto = $this->input->post('ticketidAtuto');
        $convertFormat = convert_date_format_sql();

        $where = "ticketunintsmore.companyID = " . $companyid ." AND ticketunintsmore.ticketidAtuto = " . $ticketidAtuto ." ";
        $this->datatables->select('unitMoreID,Comment,srp_erp_fa_asset_master.assetDescription as assetDescription');
        $this->datatables->where($where);
        $this->datatables->from('ticketunintsmore');
        $this->datatables->join('srp_erp_fa_asset_master','srp_erp_fa_asset_master.faID = ticketunintsmore.faID');

        $this->datatables->add_column('opAssetAction', '$1', 'op_action_asset_unit(unitMoreID)');

        echo $this->datatables->generate();
    }

    function deleteOpAssetUnit(){
        echo json_encode($this->Operation_modal->deleteOpAssetUnit());
    }


    function load_op_asset_history_table(){
        $companyid = $this->common_data['company_data']['company_id'];
        $ticketidAtuto = $this->input->post('ticketidAtuto');
        $convertFormat = convert_date_format_sql();

        $where = "tickets_crew_unit_history.companyID = " . $companyid ." AND tickets_crew_unit_history.type = 2 AND tickets_crew_unit_history.ticketidAtuto = " . $ticketidAtuto ." ";
        $this->datatables->select('ticketCrewHistoryID,description,DATE_FORMAT(tickets_crew_unit_history.createdDatetime,\'' . $convertFormat . '\') AS createdDate,srp_erp_fa_asset_master.assetDescription as assetDescription,acrwcreated.Ename2 as crewcreated');
        $this->datatables->where($where);
        $this->datatables->from('tickets_crew_unit_history');
        $this->datatables->join('srp_erp_fa_asset_master','srp_erp_fa_asset_master.faID = tickets_crew_unit_history.crewID');
        $this->datatables->join('srp_employeesdetails acrwcreated','acrwcreated.EIdNo = tickets_crew_unit_history.createdUserID');

        //$this->datatables->add_column('opCrewAction', '$1', 'op_action_crew(ticketCrewID)');

        echo $this->datatables->generate();
    }

    function confirm_opticket(){
        echo json_encode($this->Operation_modal->confirm_opticket());
    }


    function load_ticket_master_view()
    {
        $ticketidAtuto = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('ticketidAtuto') ?? '');
        $data['extra'] = $this->Operation_modal->fetch_ticket_master_detail_data($ticketidAtuto);
        $data['logo']=mPDFImage;
        $data['approval'] = $this->input->post('approval');
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/operations/erp_ticket_master_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }


    function fetch_ticket_approval()
    {
        /** rejected = 1* not rejected = 0* */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $companyID = $this->common_data['company_data']['company_id'];
            $this->datatables->select('ticketmaster.ticketidAtuto as ticketidAtuto,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(ticketmaster.createdDateTime,\'' . $convertFormat . '\') AS createdDate,ticketNo,comments,primaryUnitAssetID,regNo,wellNo,contractUID,fieldmaster.fieldName as fieldName');
            $this->datatables->from('ticketmaster');
            $this->datatables->join('fieldmaster','fieldmaster.FieldID = ticketmaster.primaryUnitAssetID');
            $this->datatables->add_column('total_prod_servc', '$1', 'total_prod_servc_tkt(ticketidAtuto,contractUID)');

            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = ticketmaster.ticketidAtuto AND srp_erp_documentapproved.approvalLevelID = ticketmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = ticketmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'OPJOB');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'OPJOB');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('ticketmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            //$this->datatables->add_column('grvPrimaryCode', '$1', 'approval_change_modal(grvPrimaryCode,grvAutoID,documentApprovedID,approvalLevelID,approvedYN,GRV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "OPJOB", ticketidAtuto)');
            $this->datatables->add_column('edit', '$1', 'ticket_action_approval(ticketidAtuto,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $companyID = $this->common_data['company_data']['company_id'];
            $this->datatables->select('ticketmaster.ticketidAtuto as ticketidAtuto,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(ticketmaster.createdDateTime,\'' . $convertFormat . '\') AS createdDate,ticketNo,comments,primaryUnitAssetID,regNo,wellNo,contractUID,fieldmaster.fieldName as fieldName');

            $this->datatables->from('ticketmaster');
            $this->datatables->join('fieldmaster','fieldmaster.FieldID = ticketmaster.primaryUnitAssetID');
            $this->datatables->add_column('total_prod_servc', '$1', 'total_prod_servc_tkt(ticketidAtuto,contractUID)');

            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = ticketmaster.ticketidAtuto');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'OPJOB');
            $this->datatables->where('ticketmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('ticketmaster.ticketidAtuto');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "OPJOB", ticketidAtuto)');
            $this->datatables->add_column('edit', '$1', 'ticket_action_approval(ticketidAtuto,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }
    }


    function save_ticket_approval(){
        $system_code = trim($this->input->post('ticketidAtuto') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'OPJOB', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('ticketidAtuto');
                $this->db->where('ticketidAtuto', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('ticketmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Operation_modal->save_ticket_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('ticketidAtuto');
            $this->db->where('ticketidAtuto', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('ticketmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'OPJOB', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Operation_modal->save_ticket_approval());
                    }
                }
            }
        }
    }


    function referback_job_ticket()
    {

        $ticketidAtuto = $this->input->post('ticketidAtuto');

        $this->db->select('approvedYN,ticketNo');
        $this->db->where('ticketidAtuto', trim($ticketidAtuto));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('ticketmaster');
        $approved_tkt = $this->db->get()->row_array();
        if (!empty($approved_tkt)) {
            echo json_encode(array('e', 'The document is already approved - ' . $approved_tkt['ticketNo']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($ticketidAtuto, 'OPJOB');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }

        }

    }

    function referback_contract_op()
    {

        $contractUID = $this->input->post('contractUID');

        $this->db->select('approvedYN,ContractNumber');
        $this->db->where('contractUID', trim($contractUID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('contractmaster');
        $approved_tkt = $this->db->get()->row_array();
        if (!empty($approved_tkt)) {
            echo json_encode(array('e', 'The document is already approved - ' . $approved_tkt['ContractNumber']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($contractUID, 'OPCNT');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }

        }

    }

    function load_txt_invoice(){
        echo json_encode($this->Operation_modal->load_txt_invoice());
    }

    function confirmProformaInvoice(){
        echo json_encode($this->Operation_modal->confirmProformaInvoice());
    }

    function load_proforma_invoices_print()
    {
        $ticketidAtuto = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('ticketidAtuto') ?? '');
        $data['extra'] = $this->Operation_modal->fetch_ticket_master_detail_proforma_data($ticketidAtuto);
        $data['logo']=mPDFImage;
        $data['approval'] = $this->input->post('approval');
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/operations/erp_ticket_master_proforma_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function check_employee_attach_expired(){
        echo json_encode($this->Operation_modal->check_employee_attach_expired());
    }

    function check_asset_attach_expired(){
        echo json_encode($this->Operation_modal->check_asset_attach_expired());
    }

    function load_contract_from_customer(){
        $data_arr = array();
        $clients = $this->input->post('clients');
        if(!empty($clients)){
            $clientID = join(',', $clients);
        }else{
            $clientID=0;
        }

        $contract = $this->db->query("SELECT contractUID,ContractNumber FROM contractmaster WHERE clientID IN ($clientID) ")->result_array();
        if (!empty($contract)) {
            foreach ($contract as $row) {
                $data_arr[trim($row['contractUID'] ?? '')] = trim($row['ContractNumber'] ?? '');
            }
        }
        echo form_dropdown('contractUID[]', $data_arr, '', 'class="form-control select2" onchange="load_calloff_from_contract()" id="contractUID"  multiple="" ');
    }

    function load_calloff_from_contract(){
        $data_arr = array();
        $contract = $this->input->post('contractUID');
        if(!empty($contract)){
            $contractUID = join(',', $contract);
        }else{
            $contractUID=0;
        }

        $contract = $this->db->query("SELECT calloffID,description FROM op_calloff_master WHERE contractUID IN ($contractUID)")->result_array();
        if (!empty($contract)) {
            foreach ($contract as $row) {
                $data_arr[trim($row['calloffID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        echo form_dropdown('calloffID[]', $data_arr, '', 'class="form-control select2" id="calloffID"  multiple="" ');
    }

    function loadCallOffChart(){
        echo json_encode($this->Operation_modal->loadCallOffChart());
    }

    function loadCallOfftable_dash(){
        $companyid = $this->common_data['company_data']['company_id'];

        $client = $_POST['tableViewClient'];
        $contract = $_POST['selectedJobContract'];
        $calloff = $_POST['selectedJobCalloff'];

        $clientID = join(',', $client);
        $contractID = join(',', $contract);
        $calloffID  = join(',', $calloff);


        $wherej = "clientID IN (".$clientID.") AND ticketmaster.contractUID IN (".$contractID.") AND calloffID IN (".$calloffID.") ";
        $wheret = "clientID IN (".$clientID.") AND ticketmaster.contractUID IN (".$contractID.") AND ticketmaster.calloffID IN (".$calloffID.") ";
        $where = "op_calloff_master.calloffID IN (".$calloffID.") ";
        /*$this->datatables->select('op_calloff_master.calloffID as calloffID,op_calloff_master.description as description,IFNULL(op_calloff_master.length,0) as calloflength,SUM(IFNULL( txtmaster.ticketlength, 0 )) AS ticketlength,FORMAT((SUM(IFNULL( txtmaster.ticketlength, 0 ))/IFNULL(op_calloff_master.length,0))*100, 2)  as txtcallpercen,FORMAT( IFNULL( statusrpt.meters * txtmaster.UnitRate, 0 ), 3 ) AS metersamnt');
        $this->datatables->from('op_calloff_master');
        $this->datatables->join('(SELECT SUM( product_service_details.Qty ) AS ticketlength ,product_service_details.UnitRate AS UnitRate,calloffID,product_service_details.contractDetailID,ticketmaster.ticketidAtuto FROM ticketmaster LEFT JOIN product_service_details ON product_service_details.ticketidAtuto = ticketmaster.ticketidAtuto WHERE '.$wherej.' GROUP BY calloffID , contractDetailID) txtmaster', '(txtmaster.calloffID = op_calloff_master.calloffID AND op_calloff_master.productId = txtmaster.contractDetailID )', 'left');
        $this->datatables->join('(SELECT ticketidAtuto, ( SUM( completionMeters ) / 1000 ) AS meters FROM opstatusreport WHERE companyID='.$companyid.' GROUP BY ticketidAtuto) statusrpt', 'txtmaster.ticketidAtuto = statusrpt.ticketidAtuto', 'left');
        $this->datatables->where($where);
        $this->datatables->where('isHold', 0);
        $this->datatables->group_by('op_calloff_master.calloffID');*/
        $this->datatables->select('tbl.calloffID as calloffID,tbl.description as description,tbl.calloflength,tbl.ticketlength,tbl.txtcallpercen,tbl.metersamnt');
        $this->datatables->from('(SELECT
	`op_calloff_master`.`calloffID` AS `calloffID`,
	`op_calloff_master`.`description` AS `description`,
	IFNULL( op_calloff_master.length, 0 ) AS calloflength,
	SUM( IFNULL( txtmaster.ticketlength, 0 ) ) AS ticketlength,
	FORMAT(
		( SUM( IFNULL( txtmaster.ticketlength, 0 ) ) / IFNULL( op_calloff_master.length, 0 ) ) * 100,
		2 
	) AS txtcallpercen,
	FORMAT( IFNULL( tktmaster.meters * tktmaster.UnitRate, 0 ), 3 ) AS metersamnt 
FROM
	`op_calloff_master`
	LEFT JOIN (
	SELECT
		SUM( product_service_details.Qty ) AS ticketlength,
		product_service_details.UnitRate AS UnitRate,
		calloffID,
		product_service_details.contractDetailID,
		ticketmaster.ticketidAtuto 
	FROM
		ticketmaster
		LEFT JOIN product_service_details ON product_service_details.ticketidAtuto = ticketmaster.ticketidAtuto 
	WHERE
		'.$wherej.'
	GROUP BY
		calloffID,
		contractDetailID 
	) txtmaster ON ( `txtmaster`.`calloffID` = `op_calloff_master`.`calloffID` AND `op_calloff_master`.`productId` = txtmaster.contractDetailID )
	
	LEFT JOIN (
		SELECT
			ticketmaster.calloffID,
			ticketmaster.ticketidAtuto,
			ctkt.productId,
			cntd.standardRate AS UnitRate,
			IFNULL( SUM( statusrpt.meters ), 0 ) AS meters 
		FROM
			ticketmaster
			LEFT JOIN op_calloff_master ctkt ON ctkt.calloffID = ticketmaster.calloffID
			LEFT JOIN contractdetails cntd ON cntd.ContractDetailID = ctkt.productId
			LEFT JOIN (
			SELECT
				ticketidAtuto,
				( SUM( completionMeters ) / 1000 ) AS meters,
				Inv.invoiceCode 
			FROM
				opstatusreport
				LEFT JOIN (
				SELECT
					cinvm.invoiceCode,
					cinvd.invoiceAutoID,
					cinvmr.invoiceAutoID AS invoiceAutoIDR,
					cinvm.documentID,
					cinvd.ticketNo,
					cinvd.callOffID,
					cinvm.transactionAmount,
					cinvmr.retensionInvoiceID,
					cinvmr.invoiceCode AS invoiceCodeR,
					cinvmr.transactionAmount AS transactionAmountR 
				FROM
					srp_erp_customerinvoicedetails cinvd
					INNER JOIN srp_erp_customerinvoicemaster cinvm ON cinvm.invoiceAutoID = cinvd.invoiceAutoID
					LEFT JOIN srp_erp_customerinvoicemaster cinvmr ON cinvmr.retensionInvoiceID = cinvm.invoiceAutoID 
				WHERE
					cinvd.ticketNo IS NOT NULL 
					AND cinvd.companyID = '.$companyid.' 
				GROUP BY
					cinvd.invoiceAutoID 
				) Inv ON Inv.ticketNo = opstatusreport.ticketidAtuto 
			WHERE
				companyID = '.$companyid.' 
			GROUP BY
				ticketidAtuto 
			HAVING
				Inv.invoiceCode IS NULL 
			) statusrpt ON ticketmaster.ticketidAtuto = statusrpt.ticketidAtuto 
		WHERE
			'.$wheret.' 
		GROUP BY
			ticketmaster.calloffID 
		) tktmaster ON ( `tktmaster`.`calloffID` = `op_calloff_master`.`calloffID` )
		  
WHERE
	'.$where.' 
	AND `isHold` = 0 
GROUP BY
	`op_calloff_master`.`calloffID` 
HAVING txtcallpercen<100) tbl');
        $this->datatables->add_column('ticktdrldwn', '$1', 'call_off_action_dilldown(calloffID,description)');
        echo $this->datatables->generate();
    }

    function loadCallOfftableHold_dash(){
        $companyid = $this->common_data['company_data']['company_id'];

        $client = $_POST['tableViewClient'];
        $contract = $_POST['selectedJobContract'];
        $calloff = $_POST['selectedJobCalloff'];

        $clientID = join(',', $client);
        $contractID = join(',', $contract);
        $calloffID  = join(',', $calloff);


        $wherej = "clientID IN (".$clientID.") AND ticketmaster.contractUID IN (".$contractID.") AND calloffID IN (".$calloffID.") ";
        $where = "op_calloff_master.calloffID IN (".$calloffID.") ";
        $this->datatables->select('op_calloff_master.calloffID as calloffID,op_calloff_master.description as description,IFNULL(op_calloff_master.length,0) as calloflength,SUM(IFNULL( txtmaster.ticketlength, 0 )) AS ticketlength,FORMAT((SUM(IFNULL( txtmaster.ticketlength, 0 ))/IFNULL(op_calloff_master.length,0))*100, 2)  as txtcallpercen');
        $this->datatables->from('op_calloff_master');
        $this->datatables->join('(SELECT SUM( product_service_details.Qty ) AS ticketlength ,calloffID,product_service_details.contractDetailID FROM ticketmaster LEFT JOIN product_service_details ON product_service_details.ticketidAtuto = ticketmaster.ticketidAtuto WHERE '.$wherej.' GROUP BY calloffID , contractDetailID) txtmaster', '(txtmaster.calloffID = op_calloff_master.calloffID AND op_calloff_master.productId = txtmaster.contractDetailID )', 'left');
        $this->datatables->where($where);
        $this->datatables->where('isHold', 1);
        $this->datatables->group_by('op_calloff_master.calloffID');

        echo $this->datatables->generate();
    }

    function loadCallOfflocation(){
        $client = $_POST['tableViewClient'];
        $contract = $_POST['selectedJobContract'];
        $calloff = $_POST['selectedJobCalloff'];

        $clientID = join(',', $client);
        $contractID = join(',', $contract);
        $calloffID  = join(',', $calloff);
        $companyID = $this->common_data['company_data']['company_id'];

        $ContractRefNos = $this->db->query("SELECT
	fieldmaster.fieldName as fieldName,
	sum( RDX ) AS rdx,
	WellNo,
	SUM(IFNULL( txtmaster.ticketlength, 0 )) AS ticketlength
FROM
	op_calloff_master
	LEFT JOIN fieldmaster ON fieldmaster.FieldID = op_calloff_master.location
	LEFT JOIN ( SELECT SUM( product_service_details.Qty ) AS ticketlength, calloffID FROM ticketmaster LEFT JOIN product_service_details ON product_service_details.ticketidAtuto = ticketmaster.ticketidAtuto WHERE ticketmaster.companyID = '{$companyID}' GROUP BY calloffID , product_service_details.ticketidAtuto ) txtmaster ON txtmaster.calloffID = op_calloff_master.calloffID 
WHERE
	op_calloff_master.companyID = '{$companyID}' 
GROUP BY
	location,
	WellNo,op_calloff_master.calloffID")->result_array();

        $arr = array();
        foreach ($ContractRefNos as $key => $item) {
            $arr[$item['fieldName']][$key] = $item;
        }
        $location="<table class='table table-bordered'> ";
        $location .="<thead><tr>";
        $head2='';
        $max_count = 0; $arr2 = [];

        foreach ($arr as $key => $item){
            $location .="<th colspan='3' style='text-align: center;'>$key</th>";
            $head2 .="<th style='background-color: rgb(124, 181, 236);'>Well No</th><th style='background-color:#c09853;'>RDX</th><th style='background-color:darkgray;'>Length</th>";
            $max_count = (count($item) > $max_count)? count($item): $max_count;
            foreach ($item as $val){
                $arr2[$key][] = $val;
            }
        }
        $location .="</tr><tr>{$head2}</tr></thead><tbody>";
        $tbody = '';

        for($i=0; $i<$max_count; $i++){
            $tbody .= '<tr>';
            foreach ($arr2 as $key=>$row) {
                if(array_key_exists($i,$row)){
                    $tbody .= ' <td>'.$row[$i]['WellNo'].'</td><td>'.$row[$i]['rdx'].'</td><td>'.$row[$i]['ticketlength'].'</td>';
                }
                else{
                    $tbody .= ' <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
                }
            }
            $tbody .= '</tr>';
        }

        $location .= $tbody."</tbody></table>";
        echo $location;
        return($location)  ;
    }

    function select_master_currency(){
        echo json_encode($this->Operation_modal->select_master_currency());
    }

    function fetch_crew_master_table(){
        $companyid = $this->common_data['company_data']['company_id'];

        $where = "crewgroup.companyID = " . $companyid ."  ";
        $this->datatables->select('groupID,groupName');
        $this->datatables->where($where);
        $this->datatables->from('crewgroup');
        $this->datatables->add_column('actiongrp', '$1', 'op_action_crew_group(groupID,groupName)');

        echo $this->datatables->generate();
    }

    function save_crew_group(){
        $this->form_validation->set_rules('groupName', 'Crew Group', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Operation_modal->save_crew_group());
        }
    }

    function deleteOpCrewGroup(){
        echo json_encode($this->Operation_modal->deleteOpCrewGroup());
    }

    function update_crew_group(){
        echo json_encode($this->Operation_modal->update_crew_group());
    }

    function load_crew_members_drop(){
        echo json_encode($this->Operation_modal->load_crew_members_drop());
    }

    function load_crew_members_table(){
        $companyid = $this->common_data['company_data']['company_id'];
        $groupID=$this->input->post('groupID');
        $where = "crewmembers.companyID = " . $companyid ." AND groupID =" .$groupID." ";
        $this->datatables->select('crewmemberID,groupID,empID,srp_employeesdetails.Ename2 as empName,srp_employeesdetails.ECode as ECode,supervisorYN');
        $this->datatables->where($where);
        $this->datatables->from('crewmembers');
        $this->datatables->join('srp_employeesdetails', 'crewmembers.empID = srp_employeesdetails.EIdNo');
        $this->datatables->add_column('actionmember', '$1', 'op_action_crew_member(crewmemberID)');
        //$this->datatables->add_column('supervisorYN', '$1', 'op_action_crew_group(crewmemberID)');
        $this->datatables->add_column('supervisorYN', '$1', 'actionsupervisoryn(crewmemberID,supervisorYN)');
        //$this->datatables->add_column('supervisorYN', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="update_supervisor_yn($1,$2)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'crewmemberID,groupID');
        echo $this->datatables->generate();
    }


    function add_members_to_group(){
        $this->form_validation->set_rules('empID[]', 'Crew members', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Operation_modal->add_members_to_group());
        }
    }

    function update_supervisor_yn(){
        echo json_encode($this->Operation_modal->update_supervisor_yn());
    }

    function deleteOpCrewMember(){
        echo json_encode($this->Operation_modal->deleteOpCrewMember());
    }

    function calloffDD(){
        echo json_encode($this->Operation_modal->calloffDD());
    }

    function load_month_wise_retention(){
        echo json_encode($this->Operation_modal->load_month_wise_retention());
    }

    function monthwiseretentionDD(){
        echo json_encode($this->Operation_modal->monthwiseretentionDD());
    }

    function get_emp_job_history_report()
    {

        $from = $this->input->post('from');
        $to = $this->input->post('to');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);
        $todt = input_format_date($to, $date_format_policy);


        $this->form_validation->set_rules('from', 'From Date', 'required');
        $this->form_validation->set_rules('from', 'To Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            $data["details"] = $this->Operation_modal->get_emp_job_history_report($fromdt,$todt);
            $data["type"] = "html";

            echo $html = $this->load->view('system/operations/reports/load-employee-job-history-report', $data, true);
        }
    }

    function get_emp_job_history_report_pdf()
    {
        $from = $this->input->post('from');
        $to = $this->input->post('to');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);
        $todt = input_format_date($to, $date_format_policy);


        $data["details"] = $this->Operation_modal->get_emp_job_history_report($fromdt,$todt);
        $data["type"] = "pdf";

        $html = $this->load->view('system/operations/reports/load-employee-job-history-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    function get_asset_history_report_pdf()
    {
        $from = $this->input->post('from');
        $to = $this->input->post('to');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);
        $todt = input_format_date($to, $date_format_policy);


        $data["details"] = $this->Operation_modal->get_asset_history_report($fromdt,$todt);
        $data["type"] = "pdf";

        $html = $this->load->view('system/operations/reports/load-asset-history-report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }



    function get_asset_history_report()
    {

        $from = $this->input->post('from');
        $to = $this->input->post('to');

        $date_format_policy = date_format_policy();
        $fromdt = input_format_date($from, $date_format_policy);
        $todt = input_format_date($to, $date_format_policy);


        $this->form_validation->set_rules('from', 'From Date', 'required');
        $this->form_validation->set_rules('from', 'To Date', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            $data["details"] = $this->Operation_modal->get_asset_history_report($fromdt,$todt);
            $data["type"] = "html";

            echo $html = $this->load->view('system/operations/reports/load-asset-history-report', $data, true);
        }
    }

    function fetch_checklist_master_table(){
        $companyid = $this->common_data['company_data']['company_id'];

        $where = "srp_erp_op_checklist_master.companyID = " . $companyid ."  ";
        $this->datatables->select('id,name,view_path,companyID,status');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_op_checklist_master');
        $this->datatables->add_column('status', '$1', 'op_checklist_active(id,companyID)');
        $this->datatables->add_column('actionchk', '$1', 'op_action_checklist(id,companyID)');

        echo $this->datatables->generate();
    }

    function load_checklist_single(){
        $id = $this->input->post('id');             
        $data["details"] = $this->Operation_modal->load_checklist_single();
        if($id == 1){
            $html = $this->load->view('system/operations/checklist/templates/daily_drillers_checklist', $data);
        } else if($id == 2){
            $html = $this->load->view('system/operations/checklist/templates/daily_assistant_driller_checklist', $data);
        } else if($id == 3){
            $html = $this->load->view('system/operations/checklist/templates/fishing_checklist', $data);
        } else if($id == 4){
            $html = $this->load->view('system/operations/checklist/templates/rig_up_checklist', $data);
        } else if($id == 5){
            $html = $this->load->view('system/operations/checklist/templates/daily_crane_inspection_checklist', $data);
        } else if($id == 6){
            $html = $this->load->view('system/operations/checklist/templates/folklift_preuse_checklist', $data);
        } else if($id == 7){
            $html = $this->load->view('system/operations/checklist/templates/rig_move_checklist', $data);
        } else if($id == 8){
            $html = $this->load->view('system/operations/checklist/templates/tool_box_checklist', $data);
        } else if($id == 9){
            $html = $this->load->view('system/operations/checklist/templates/power_swivel_checklist', $data);
        } else if($id == 10){
            $html = $this->load->view('system/operations/checklist/templates/pipe_tally', $data);
        } else if($id == 11){
            $html = $this->load->view('system/operations/checklist/templates/well_program', $data);
        } else if($id == 12){
            $html = $this->load->view('system/operations/checklist/templates/supervisors_overview', $data);
        } else if($id == 13){
            $html = $this->load->view('system/operations/checklist/templates/well_program_steps_checklist', $data);
        } else if($id == 14){
            $html = $this->load->view('system/operations/checklist/templates/system_inspection', $data);
        } else if($id == 15){
            $html = $this->load->view('system/operations/checklist/templates/daily_equipment_inspection', $data);
        } else if($id == 16){
            $html = $this->load->view('system/operations/checklist/templates/rig_turn_over', $data);
        } else if($id == 18){
            $html = $this->load->view('system/operations/checklist/templates/rig_crew_inspection', $data);
        }else{
            $html = $this->load->view('system/operations/checklist/templates/system_inspection', $data);
        }        
        
        return $html;
    }

    function load_checklist_rig_turn_over(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_rig_turn_over();
        $html = $this->load->view('system/operations/checklist/templates/rig_turn_over', $data);
        
        return $html;
    }

    function load_checklist_daily_equipment_inspection(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details_cat_one"] = $this->Operation_modal->load_checklist_daily_equipment_inspection(1);
        $data["details_cat_two"] = $this->Operation_modal->load_checklist_daily_equipment_inspection(2);
        $data["details_cat_three"] = $this->Operation_modal->load_checklist_daily_equipment_inspection(3);
        $html = $this->load->view('system/operations/checklist/templates/daily_equipment_inspection', $data);
        
        return $html;
    }

    function load_checklist_rig_crew_inspection(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_rig_crew_inspection();
        $html = $this->load->view('system/operations/checklist/templates/rig_crew_inspection', $data);
        
        return $html;
    }

    function load_checklist_wellsite(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_wellsite();
        $html = $this->load->view('system/operations/checklist/templates/wellsite_checklist', $data);
        
        return $html;
    }

    function load_checklist_field_ticket(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_field_ticket();
        $html = $this->load->view('system/operations/checklist/templates/field_ticket', $data);
        
        return $html;
    }
    

    function load_checklist_drilling_service_rig_inspection(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_drilling_service_rig_inspection();
        $html = $this->load->view('system/operations/checklist/templates/drilling_service_rig_inspection', $data);
        
        return $html;
    }

    function load_checklist_system_inspection(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_system_inspection();
        $html = $this->load->view('system/operations/checklist/templates/system_inspection', $data);
        
        return $html;
    }

    function load_checklist_well_program_steps(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_well_program_steps();
        $html = $this->load->view('system/operations/checklist/templates/well_program_steps_checklist', $data);
        
        return $html;
    }

    function load_checklist_supervisors_overview(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_supervisors_overview();
        $html = $this->load->view('system/operations/checklist/templates/supervisors_overview', $data);
        
        return $html;
    }

    function load_checklist_well_program(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_well_program();
        $html = $this->load->view('system/operations/checklist/templates/well_program', $data);
        
        return $html;
    }

    function load_checklist_pipe_tally(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_pipe_tally();
        $html = $this->load->view('system/operations/checklist/templates/pipe_tally', $data);
        
        return $html;
    }

    function load_checklist_power_swivel(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details_cat_one"] = $this->Operation_modal->load_checklist_power_swivel(1);
        $data["details_cat_two"] = $this->Operation_modal->load_checklist_power_swivel(2);
        $html = $this->load->view('system/operations/checklist/templates/power_swivel_checklist', $data);
        
        return $html;
    }

    function load_checklist_fishing(){
        $id = $this->input->post('id');
       
        $data["title"] = $this->Operation_modal->load_checklist_header();        
        $data["details"] = $this->Operation_modal->load_checklist_fishing();
        $html = $this->load->view('system/operations/checklist/templates/fishing_checklist', $data);
        
        return $html;
    }

    function load_checklist_rig(){
        $id = $this->input->post('id');
        $companyID = $this->input->post('companyID');   
        
        $data["details"] = $this->Operation_modal->load_checklist_rig();
        $html = $this->load->view('system/operations/checklist/templates/rig_up_checklist', $data);
        
        return $html;
    }

    function load_checklist_daily_drillers(){
        $id = $this->input->post('id');
        $companyID = $this->input->post('companyID');   
        
        $data["details"] = $this->Operation_modal->load_checklist_driller();
        $html = $this->load->view('system/operations/checklist/templates/daily_drillers_checklist', $data);
        
        return $html;
    }

    function load_checklist_daily_assistant_driller(){
        $id = $this->input->post('id');
        $companyID = $this->input->post('companyID');   
        
        $data["details"] = $this->Operation_modal->load_checklist_assistant_driller();
        $html = $this->load->view('system/operations/checklist/templates/daily_assistant_driller_checklist', $data);
        
        return $html;
    }

    function load_checklist_crane_inspection(){
        $id = $this->input->post('id');
        $companyID = $this->input->post('companyID');   
        
        $data["details"] = $this->Operation_modal->load_checklist_crane_inspection();
        $html = $this->load->view('system/operations/checklist/templates/daily_crane_inspection_checklist', $data);
        
        return $html;
    }

    function load_checklist_forklift(){
        $id = $this->input->post('id');
        $companyID = $this->input->post('companyID');   
        
        $data["details"] = $this->Operation_modal->load_checklist_forklift();
        $html = $this->load->view('system/operations/checklist/templates/folklift_preuse_checklist', $data);
        
        return $html;
    }

    function load_checklist_rig_move(){
        $id = $this->input->post('id');
        $companyID = $this->input->post('companyID');   
        
        $data["details"] = $this->Operation_modal->load_checklist_rig_move();
        $html = $this->load->view('system/operations/checklist/templates/rig_move_checklist', $data);
        
        return $html;
    }

    function load_checklist_tool_box(){
        $id = $this->input->post('id');
        $companyID = $this->input->post('companyID');  
         
        $data["title"] = $this->Operation_modal->load_checklist_header();
        $data["details"] = $this->Operation_modal->load_checklist_tool_box();
        $html = $this->load->view('system/operations/checklist/templates/tool_box_checklist', $data);
        
        return $html;
    }

    function updateChecklistActive(){
        echo json_encode($this->Operation_modal->updateChecklistActive());
    }


}