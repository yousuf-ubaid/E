<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Operation_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_contract_master()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $ContStartDate = trim($this->input->post('ContStartDate') ?? '');
        $ContEndDate = trim($this->input->post('ContEndDate') ?? '');
        $ContractStartDate = input_format_date($ContStartDate, $date_format_policy);
        $ContractEndDate = input_format_date($ContEndDate, $date_format_policy);
        if($ContractStartDate>$ContractEndDate){
            return array('e', 'Contract Start Date should be less than contract End date');
            exit;
        }

        if (trim($this->input->post('contractUID') ?? '')) {
            $contractUID=$this->input->post('contractUID');
            $ContractNumber=$this->input->post('ContractNumber');
            $ContractNumber = $this->db->query("SELECT * FROM contractmaster WHERE contractUID!='{$contractUID}' AND ContractNumber='{$ContractNumber}' ")->row_array();
            if(!empty($ContractNumber)){
                return array('e', 'Contract/Quotation Ref already exist');
                exit;
            }
        }else{
            $ContractNumber=$this->input->post('ContractNumber');
            $ContractNumber = $this->db->query("SELECT * FROM contractmaster WHERE ContractNumber='{$ContractNumber}' ")->row_array();
            if(!empty($ContractNumber)){
                return array('e', 'Contract/Quotation Ref already exist');
                exit;
            }
        }

        //$ServiceLineCode = explode('|', trim($this->input->post('ServiceLineCode') ?? ''));

        $data['contractType'] = $this->input->post('contractType');
        $data['documentCode'] = 'OPCNT';
        $data['clientID'] = $this->input->post('clientID');
        $data['ContractNumber'] = $this->input->post('ContractNumber');
        $data['ServiceLineCode'] = trim($this->input->post('ServiceLineCode') ?? '');
        $data['ContStartDate'] = $ContractStartDate;
        $data['ContEndDate'] = $ContractEndDate;
        $data['contractStatus'] = $this->input->post('contractStatus');
        $data['ContCurrencyID'] = $this->input->post('ContCurrencyID');
        $data['contValue'] = $this->input->post('contValue');
        $data['productGLCode'] = $this->input->post('productGLCode');
        $data['serviceGLCode'] = $this->input->post('serviceGLCode');

        if (trim($this->input->post('contractUID') ?? '')) {

            $data['modifiedPc'] = $this->common_data['current_pc'];
            $data['modifiedUser'] = $this->common_data['current_userID'];
            $data['timestamp'] = $this->common_data['current_date'];

            $this->db->where('contractUID', trim($this->input->post('contractUID') ?? ''));
            $this->db->update('contractmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Contract Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Contract Updated Successfully');
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdPcID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('contractmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Contract Insert Failed');
            } else {
                $this->db->trans_commit();
                return array('s', 'Contract Inserted Successfully');
            }
        }
    }

    function get_contract_master_edit(){
        $this->db->select('*');
        $this->db->where('contractUID', trim($this->input->post('contractUID') ?? ''));
        $this->db->from('contractdetails');
        $dtl= $this->db->get()->result_array();
        $dtlexist=0;
        if(!empty($dtl)){
            $dtlexist=1;
        }

        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(ContStartDate,\'' . $convertFormat . '\') AS ContStartDate,DATE_FORMAT(ContEndDate,\'' . $convertFormat . '\') AS ContEndDate,' .$dtlexist. ' as DetailExsist ');
        $this->db->where('contractUID', trim($this->input->post('contractUID') ?? ''));
        $this->db->from('contractmaster');
        return $this->db->get()->row_array();
    }

    function save_contract_details(){
        $this->db->trans_start();

        $data['ClientRef'] = $this->input->post('ClientRef');
        $data['OurRef'] = $this->input->post('OurRef');
        $data['ItemDescrip'] = $this->input->post('ItemDescrip');
        $data['TypeID'] = $this->input->post('TypeID');
        $data['UnitID'] = $this->input->post('UnitID');
        $data['RateCurrencyID'] = $this->input->post('RateCurrencyID');
        $data['standardRate'] = $this->input->post('standardRate');
        $data['GLCode'] = $this->input->post('GLCode');


        if (trim($this->input->post('ContractDetailID') ?? '')) {

            $data['modifiedPc'] = $this->common_data['current_pc'];
            $data['modifiedUser'] = $this->common_data['current_userID'];
            $data['timeStamp'] = $this->common_data['current_date'];

            $this->db->where('ContractDetailID', trim($this->input->post('ContractDetailID') ?? ''));
            $this->db->update('contractdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Contract Details Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Contract Details Updated Successfully');
            }
        } else {
            $this->load->library('sequence');
            $data['contractUID'] = $this->input->post('contractUID');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdPcID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('contractdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Contract Details Insert Failed');
            } else {
                $this->db->trans_commit();
                return array('s', 'Contract Details Inserted Successfully');
            }
        }
    }

    function get_contract_detail_edit(){
        $this->db->select('*');
        $this->db->where('ContractDetailID', trim($this->input->post('ContractDetailID') ?? ''));
        $this->db->from('contractdetails');
        return $this->db->get()->row_array();
    }

    function delete_contrct_detail(){
        $data=$this->db->delete('contractdetails', array('ContractDetailID' => trim($this->input->post('ContractDetailID') ?? '')));
        if($data){
            return array('s', 'Contract Details Deleted Successfully');
        }
    }

    function confirm_opcontrct(){
        $system_code = trim($this->input->post('contractUID') ?? '');

        $this->db->select('contractUID');
        $this->db->where('contractUID', $system_code);
        $this->db->from('contractdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            return array('error' => 1, 'message' => 'There are no records to confirm this document!');
            exit;
        }else {
            $this->load->library('Approvals');
            $this->db->select('contractUID, ContractNumber,ContStartDate');
            $this->db->where('contractUID', $system_code);
            $this->db->from('contractmaster');
            $opcnt_data = $this->db->get()->row_array();

            $autoApproval= get_document_auto_approval('OPCNT');
            if($autoApproval==0){
                $approvals_status = $this->approvals->auto_approve($opcnt_data['contractUID'], 'contractmaster','contractUID', 'OPCNT',$opcnt_data['ContractNumber'],$opcnt_data['ContStartDate']);
            }elseif($autoApproval==1){
                $approvals_status = $this->approvals->CreateApproval('OPCNT', $opcnt_data['contractUID'], $opcnt_data['ContractNumber'], 'Operations Contract', 'contractmaster', 'contractUID',0,$opcnt_data['ContStartDate']);
            }else{
                return array('error' => 1, 'message' => 'Approval levels are not set for this document.');
                exit;
            }

            if ($approvals_status == 1) {
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user']
                );
                $this->db->where('contractUID', $system_code);
                $this->db->update('contractmaster', $data);



                $autoApproval= get_document_auto_approval('OPCNT');

                if($autoApproval==0) {
                    return array('error' => 0, 'message' => 'Document confirmed successfully ');
                   /* $result = $this->save_grv_approval(0, $opcnt_data['contractUID'], 1, 'Auto Approved');
                    if($result){
                        return array('error' => 0, 'message' => 'Document confirmed successfully ');
                    }*/
                }else{
                    return array('error' => 0, 'message' => 'Document confirmed successfully ');
                }

            } else if ($approvals_status == 3) {
                return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document.');
            } else {
                return array('error' => 1, 'message' => 'some went wrong!');
            }
        }
    }



    function save_contract_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('contractUID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['contractUID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        $company_id = $this->common_data['company_data']['company_id'];

        $maxLevel = $this->approvals->maxlevel('OPCNT');

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'OPCNT');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function fetch_contract_master_detail_data($contractUID){
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->db->select('contractUID,ContractNumber,srp_erp_segment.description as Department,DATE_FORMAT(ContStartDate,\'' . $convertFormat . '\') AS ContStartDate,DATE_FORMAT(ContEndDate,\'' . $convertFormat . '\') AS ContEndDate,contracttype.description as conType,approvedYN,contractStatus,productGLCode,serviceGLCode,approvedbyEmpName,approvedDate');
        $this->db->where('contractUID', $contractUID);
        $this->db->from('contractmaster');
        $this->db->join('srp_erp_segment','srp_erp_segment.segmentID = contractmaster.ServiceLineCode');
        $this->db->join('contracttype','contracttype.contractTypeId = contractmaster.contractType');
        $data['master'] = $this->db->get()->row_array();

        $this->db->select('ContractDetailID,ClientRef,OurRef,ItemDescrip,TypeID,contractdetails.UnitID as UnitID,RateCurrencyID,standardRate,srp_erp_unit_of_measure.UnitDes as UnitDes,srp_erp_currencymaster.CurrencyCode as CurrencyCode,contractUID');
        $this->db->where('contractUID', $contractUID);
        $this->db->from('contractdetails');
        $this->db->join('srp_erp_unit_of_measure','srp_erp_unit_of_measure.UnitID = contractdetails.UnitID');
        $this->db->join('srp_erp_currencymaster','srp_erp_currencymaster.currencyID = contractdetails.RateCurrencyID');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('op_calloff_master.calloffID as calloffID,description,op_calloff_master.contractUID as contractUID,length,RDX,WellNo,drawingNo,joints,DATE_FORMAT(op_calloff_master.createdDate,\'' . $convertFormat . '\') AS createdDate,DATE_FORMAT(op_calloff_master.expiryDate,\'' . $convertFormat . '\') AS expiryDate,fieldmaster.fieldName as fieldName,IFNULL(( ticket.lengths / op_calloff_master.length )* 100, 0 ) AS percentage');
        $this->db->where('op_calloff_master.contractUID', $contractUID);
        $this->db->where('op_calloff_master.companyID', $companyid);
        $this->db->from('op_calloff_master');
        $this->db->join('fieldmaster','fieldmaster.FieldID = op_calloff_master.location');
        $this->db->join('(SELECT IFNULL( SUM( length ), 0 ) AS lengths,contractRefNo,calloffID,contractUID FROM ticketmaster WHERE companyID =  \'' . $companyid . '\' 	AND isDeleted <> 1 AND calloffID IS NOT NULL AND contractUID = \'' . $contractUID . '\'    GROUP BY calloffID,contractUID) ticket', '(ticket.calloffID = op_calloff_master.calloffID AND op_calloff_master.contractUID = ticket.contractUID)', 'left');
        $data['calloff'] = $this->db->get()->result_array();
        return $data;
    }

    function loard_contract_detail_drop()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $this->db->select('ContractDetailID,ItemDescrip,contractUID');
        $this->db->where('contractUID', $this->input->post('contractUID'));
        $this->db->where('CompanyID', $companyID);
        $this->db->from('contractdetails');
        return $contractdetail = $this->db->get()->result_array();
    }

    function save_calloff(){
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $createdDate = trim($this->input->post('createdDate') ?? '');
        $expiryDate = trim($this->input->post('expiryDate') ?? '');
        $createdDat = input_format_date($createdDate, $date_format_policy);
        $expiryDat = input_format_date($expiryDate, $date_format_policy);
        $companyID = $this->common_data['company_data']['company_id'];
        if ($expiryDat < $createdDat) {
            return array('e', 'Exipry Date can not be less than created Date');
            exit;
        }
        $filter='';
        if (trim($this->input->post('calloffID') ?? '')) {
            $calloffID=$this->input->post('calloffID');
            $filter=" AND calloffID != $calloffID";
        }

        $description= $this->input->post('description');
        $desc = $this->db->query("SELECT * FROM op_calloff_master WHERE companyID='{$companyID}' AND description = '{$description}' $filter ")->row_array();
        if ($desc) {
            return array('e', 'description already exist');
            exit;
        }

        $data['description'] = $this->input->post('description');
        $data['productId'] = $this->input->post('productId');
        $data['location'] = $this->input->post('location');
        $data['length'] = $this->input->post('length');
        $data['RDX'] = $this->input->post('RDX');
        $data['WellNo'] = $this->input->post('WellNo');
        $data['drawingNo'] = $this->input->post('drawingNo');
        $data['joints'] = $this->input->post('joints');
        $data['unitOfMeasure'] = $this->input->post('unitOfMeasure');
        $data['isHold'] = $this->input->post('isHold');
        $data['createdDate'] = $createdDat;
        $data['expiryDate'] = $expiryDat;


        if (trim($this->input->post('calloffID') ?? '')) {

            $this->db->where('calloffID', trim($this->input->post('calloffID') ?? ''));
            $this->db->update('op_calloff_master', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Call off Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Call off Updated Successfully');
            }
        } else {
            $this->load->library('sequence');
            $data['contractUID'] = $this->input->post('contractUID');
            $data['companyID'] = $this->common_data['company_data']['company_id'];

            $this->db->insert('op_calloff_master', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Call off Insert Failed');
            } else {
                $this->db->trans_commit();
                return array('s', 'Call off Inserted Successfully');
            }
        }
    }

    function get_call_off_edit(){
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(createdDate,\'' . $convertFormat . '\') AS createdDate,DATE_FORMAT(expiryDate,\'' . $convertFormat . '\') AS expiryDate');
        $this->db->where('calloffID', trim($this->input->post('calloffID') ?? ''));
        $this->db->from('op_calloff_master');
        return $this->db->get()->row_array();
    }

    function deleteCallOff(){
        $this->db->select('ticketidAtuto');
        $this->db->where('calloffID', trim($this->input->post('calloffID') ?? ''));
        $this->db->from('ticketmaster');
        $tktmastr= $this->db->get()->result_array();

        if(!empty($tktmastr)){
            return array('e', 'Call Off has been used in tickets');
        }else{
            $data=$this->db->delete('op_calloff_master', array('calloffID' => trim($this->input->post('calloffID') ?? '')));
            if($data){
                return array('s', 'Call Off Deleted Successfully');
            }
        }

    }


    function save_job_master(){
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $companyID=$this->common_data['company_data']['company_id'];
        $data['clientID'] = $this->input->post('clientID');
        $data['contractRefNo'] = $this->input->post('contractRefNo');
        $data['jobNetworkNo'] = $this->input->post('jobNetworkNo');
        $data['serviceLine'] = $this->input->post('serviceLine');

        $data['primaryUnitAssetID'] = $this->input->post('primaryUnitAssetID');
        $data['wellNo'] = $this->input->post('wellNo');
        $data['comments'] = $this->input->post('comments');
        $data['callOffBase'] = $this->input->post('callOffBase');
        $data['EngID'] = $this->input->post('EngID');
        $data['operationLog'] = $this->input->post('operationLog');
        if($this->input->post('callOffBase')==1){
            $data['calloffID'] = $this->input->post('calloffID');
        }else{
            $data['calloffID'] = null;
        }


        if (trim($this->input->post('ticketidAtuto') ?? '')) {
            $data['modifiedPc'] = $this->common_data['current_pc'];
            $data['modifiedUser'] = $this->common_data['current_userID'];

            $this->db->where('ticketidAtuto', trim($this->input->post('ticketidAtuto') ?? ''));
            $this->db->update('ticketmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Job Ticket Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Job Ticket Updated Successfully');
            }
        } else {
            $this->load->library('sequence');
            $data['contractUID'] = $this->input->post('contractUID');
            $data['companyID'] = $this->common_data['company_data']['company_id'];

            $data['createdPcID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $data['documentID'] = 'OPJOB';
            $data['estimatedServiceValue'] = '0.000';
            $data['estimatedProductValue'] = '0.000';
            $data['ticketMonth'] = date("n");
            $data['ticketYear'] = date("Y");

            $LasticketNo = $this->db->query("SELECT max(serialNo) AS serialNo FROM ticketmaster WHERE companyID = '{$companyID}' ORDER BY serialNo DESC ")->row_array();
            $LasticketNo1 = (int)$LasticketNo['serialNo'] + 1;
            $lenghtofLasttktNo = strlen($LasticketNo1);
            switch ($lenghtofLasttktNo) {
                case 1:
                    $finalTktNo = '000' . $LasticketNo1;
                    break;
                case 2:
                    $finalTktNo = '00' . $LasticketNo1;
                    break;
                case 3:
                    $finalTktNo = '0' . $LasticketNo1;
                    break;
                default:
                    $finalTktNo = $LasticketNo1;
            }

            $m = date("n");
            $y = date("y");
            $data['ticketNo'] = "$y/$finalTktNo";
            $data['serialNo'] = $LasticketNo1;
            $data['revenueMonth'] = date("n");
            $data['revenueYear'] = date("Y");
            $this->db->insert('ticketmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Job Ticket Insert Failed');
            } else {
                $this->db->trans_commit();
                return array('s', 'Job Ticket Inserted Successfully');
            }
        }
    }

    function deletejobticket(){
        $data=$this->db->delete('ticketmaster', array('ticketidAtuto' => trim($this->input->post('ticketidAtuto') ?? '')));
        if($data){
            return array('s', 'Job Ticket Deleted Successfully');
        }
    }

    function load_ticket_edit(){
        $convertFormat = convert_date_format_sql();
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $this->db->select('ticketmaster.*,DATE_FORMAT(submissionDate,\'' . $convertFormat . '\') AS submissionDat,det.completionMeters as completionMeters');
        $this->db->where('ticketmaster.ticketidAtuto', trim($this->input->post('ticketidAtuto') ?? ''));
        $this->db->from('ticketmaster');
        $this->db->join('(SELECT IFNULL(SUM(completionMeters),0) as completionMeters,opStatusReportID,ticketidAtuto FROM opstatusreport  GROUP BY ticketidAtuto) det', '(det.ticketidAtuto = ticketmaster.ticketidAtuto)', 'left');
        $data= $this->db->get()->row_array();

        if(!empty($data)){
            if((empty($data['length']) || $data['length']==null) && !empty($data['completionMeters'])){
                $datas['length'] = $data['completionMeters'];
                $this->db->where('ticketidAtuto', trim($ticketidAtuto));
                $this->db->update('ticketmaster', $datas);
            }
        }

        return $data;
    }

    function update_jobDetail(){
        $valu=$this->input->post('valu');
        $fieldName=$this->input->post('fieldName');
        if($fieldName=='submissionDate'){
            $date_format_policy = date_format_policy();
            $submissionDate = trim($this->input->post('valu') ?? '');
            $submissionDat = input_format_date($submissionDate, $date_format_policy);
            $data[$fieldName] = $submissionDat;
        }else{
            $data[$fieldName] = $valu;
        }

        $this->db->where('ticketidAtuto', trim($this->input->post('ticketidAtuto') ?? ''));
        $rslt=$this->db->update('ticketmaster', $data);

        if($rslt){
            return array('s', 'Job Ticket Updated Successfully');
        }
    }


    function load_operation_system_log_hd(){
        $contractUID=$this->input->post('contractUID');
        $convertFormat = convert_date_format_sql();


        $this->db->select('ticketmaster.confirmedYN as confirmedYN,srp_erp_customermaster.customerName as customerName,DATE_FORMAT(ticketmaster.expectedEndDate,\'' . $convertFormat . '\') AS expectedEndDate,activity,poNumber,productId, op_calloff_master.length,ticketNo,fieldmaster.fieldName,op_calloff_master.wellNo,clientID,DATE_FORMAT(ticketmaster.Timedatejobstra,\'' . $convertFormat . '\') AS Timedatejobstra,DATE_FORMAT(ticketmaster.Timedatejobend,\'' . $convertFormat . '\') AS Timedatejobend,op_calloff_master.RDX,drawingNo,joints');
        $this->db->where('ticketidAtuto', trim($this->input->post('ticketidAtuto') ?? ''));
        $this->db->from('ticketmaster');
        $this->db->join('srp_erp_customermaster','srp_erp_customermaster.customerAutoID = ticketmaster.clientID');
        $this->db->join('op_calloff_master','op_calloff_master.calloffID = ticketmaster.calloffID');
        $this->db->join('fieldmaster','fieldmaster.FieldID = op_calloff_master.location');
        return $this->db->get()->row_array();

    }

    function updatehdFields(){
        $fieldname=$this->input->post('fieldname');
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $valu=$this->input->post('valu');
        if($fieldname=='productId'){
            $this->db->select('calloffID');
            $this->db->where('ticketidAtuto', trim($this->input->post('ticketidAtuto') ?? ''));
            $this->db->from('ticketmaster');
            $calloffID= $this->db->get()->row_array();
            $calloffID=$calloffID['calloffID'];

            $cdata['productId'] = $valu;
            $this->db->where('calloffID', trim($calloffID));
            $rslt=$this->db->update('op_calloff_master', $cdata);
        }else if($fieldname=='expectedEndDate'){
            $date_format_policy = date_format_policy();
            $expectedEndDa = $valu;
            $expectedEndDate = input_format_date($expectedEndDa, $date_format_policy);
            $valu=$expectedEndDate;
        }

        $data[$fieldname] = $valu;
        $this->db->where('ticketidAtuto', trim($ticketidAtuto));
        $rslts=$this->db->update('ticketmaster', $data);
        if($rslts){
            return array('s', 'Job Ticket Head Updated Successfully');
        }
    }

    function load_operationlogbody(){
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $companyID=$this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $details = $this->db->query("SELECT opStatusReportID,IFNULL(joints,' ') as joints, IFNULL(tieIn,' ') as tieIn, IFNULL(straight,' ') as straight, IFNULL(completionMeters,' ') as completionMeters, IFNULL(remarks,' ') as remarks, IFNULL(jointPercentage,' ') as jointPercentage,DATE_FORMAT(opstatusreport.startedDated,  '$convertFormat'  ) AS startedDat ,srp_employeesdetails.Ename2 as empnam,isUsed FROM opstatusreport LEFT JOIN srp_employeesdetails ON opstatusreport.empID=srp_employeesdetails.EIdNo   WHERE companyID='{$companyID}' AND ticketidAtuto = '{$ticketidAtuto}' ")->result_array();
        return $details;
    }

    function addCrewRow(){
        $empID=$this->input->post('empID');
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $companyID=$this->common_data['company_data']['company_id'];

        if(empty($empID)){
            return array('e', 'Select Crew');
        }else{
            $data['ticketidAtuto'] = $ticketidAtuto;
            $data['companyID'] = $companyID;
            $data['empID'] = $empID;
            $data['startedDated'] = date('Y-m-d');;

            $rslts=$this->db->insert('opstatusreport', $data);
            if($rslts){
                return array('s', 'Successfully Created');
            }else{
                return array('e', 'insert failed');
            }
        }
    }

    function delete_oplog(){
        $data=$this->db->delete('opstatusreport', array('opStatusReportID' => trim($this->input->post('opStatusReportID') ?? '')));
        if($data){
            return array('s', 'Deleted Successfully');
        }else{
            return array('e', 'Deletion Failed');
        }
    }


    function update_op_system_log_detail(){
        $opStatusReportID=$this->input->post('opStatusReportID');
        $valu=$this->input->post('valu');
        $fieldname=$this->input->post('fieldname');


        if($fieldname=='startedDated'){
            $date_format_policy = date_format_policy();
            $startedDated = $valu;
            $startedDated = input_format_date($startedDated, $date_format_policy);
            $valu=$startedDated;
        }

        $data[$fieldname] = $valu;
        $this->db->where('opStatusReportID', trim($opStatusReportID));
        $rslts=$this->db->update('opstatusreport', $data);

        if($data){
            return array('s', 'Successfully Inserted');
        }else{
            return array('e', 'insert failed');
        }
    }


    function save_product_service_detail(){
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $contractUID=$this->input->post('contractUID');
        $ContractDetailID=$this->input->post('ContractDetailID');
        $typeId=$this->input->post('typeId');
        $companyID=$this->common_data['company_data']['company_id'];

        $this->db->select('*');
        $this->db->where('ContractDetailID', $ContractDetailID);
        $this->db->from('contractdetails');
        $contractdetails= $this->db->get()->row_array();

        $this->db->select('callOffBase,calloffID');
        $this->db->where('ticketidAtuto', trim($ticketidAtuto));
        $this->db->from('ticketmaster');
        $callOffBase= $this->db->get()->row_array();

        $totalused = $this->db->query("SELECT
	IFNULL( SUM( completionMeters ), 0 ) AS completionSum ,
op_calloff_master.productId
FROM
	opstatusreport
	INNER JOIN ticketmaster ON ticketmaster.ticketidAtuto = opstatusreport.ticketidAtuto
	INNER JOIN op_calloff_master ON op_calloff_master.calloffID = ticketmaster.calloffID
WHERE
	opstatusreport.companyID = '{$companyID}'
	AND opstatusreport.ticketidAtuto = '{$ticketidAtuto}'
	AND op_calloff_master.productId = '{$ContractDetailID}' 
	AND isUsed = 0 
GROUP BY
	opstatusreport.ticketidAtuto")->row_array();

        $callLength=0;
        $totalused =$totalused ['completionSum'];
        if($callOffBase['callOffBase']==1 && $totalused>0){
            $totalused=$totalused/1000;
        }

        $totalCharge=  $totalused*$contractdetails['standardRate'];
        $unitRate=     $contractdetails['standardRate'];
        $Discount=     $contractdetails['discount'];

        if($callOffBase['callOffBase']==1){
            $calloffID=$callOffBase['calloffID'];
            $calloffLength = $this->db->query("SELECT calloffID,length FROM op_calloff_master WHERE companyID='{$companyID}' AND calloffID = '{$calloffID}'")->row_array();
            $totalLength = $calloffLength['length'];
            $callLength=$totalLength;
            $percentage = ($totalused/$totalLength)*100;
            $percentage=   round($percentage,3);



            $totalPercentage = $this->db->query("SELECT IFNULL(sum(percentage),0) as totalPercentage FROM ticketmaster INNER JOIN product_service_details ON ticketmaster.ticketidAtuto = product_service_details.ticketidAtuto WHERE ticketmaster.companyID='{$companyID}' AND calloffID = '{$calloffID}' AND contractDetailID = '{$ContractDetailID}' AND typeId='{$typeId}'")->row_array();
            if(!empty($totalPercentage) && $totalPercentage['totalPercentage'] >= 100){
                return array('e', 'This product has already been billed 100% based on the Length');
                exit();
            }

        }else{
            $percentage=   0;
        }
        if($typeId==1){
            $lstynused = $this->db->query("SELECT * FROM ticketmaster INNER JOIN product_service_details ON ticketmaster.ticketidAtuto = product_service_details.ticketidAtuto WHERE ticketmaster.companyID='{$companyID}' AND calloffID = '{$calloffID}' AND contractDetailID = '{$ContractDetailID}' AND lastYN=1 AND typeId=1")->row_array();
            if(!empty($lstynused)){
                return array('e', 'This product has already been marked as billing completed in Job Ticket '.$lstynused['ticketNo']);
                exit();
            }

            $data['lastYN'] = $this->input->post('lastYN');
        }else{
            $data['lastYN'] = 0;
        }
        $data['companyID'] = $companyID;
        $data['typeId'] = $typeId;
        $data['ticketidAtuto'] = $ticketidAtuto;
        $data['contractUID'] = $contractUID;
        $data['contractDetailID'] = $ContractDetailID;
        $data['CustomerID'] = $contractdetails['CustomerID'];
        $data['OurReferance'] = $contractdetails['OurRef'];
        $data['clientReference'] = $contractdetails['ClientRef'];
        $data['contractDetailID'] = $contractdetails['ContractDetailID'];
        $data['Unit'] = $contractdetails['UnitID'];
        $data['UnitRate'] = $contractdetails['standardRate'];
        $data['RateCurrency'] = $contractdetails['RateCurrencyID'];
        $data['timestamp'] = date('Y-m-d G:i:s');
        $data['Qty'] = $totalused;
        if($callOffBase['callOffBase']==1 && $totalused>0) {
            $data['calloffLength'] = $callLength;
        }else{
            $data['calloffLength'] = 0;
        }
        $data['TotalCharges'] = $totalused*$contractdetails['standardRate'];
        $data['Description'] = $contractdetails['ItemDescrip'];
        $data['percentage'] = $percentage;
        $data['discount'] = $contractdetails['discount'];
        $data['GLCode'] = $contractdetails['GLCode'];
        $data['addedDate'] = date('Y-m-d');

        $rslts=$this->db->insert('product_service_details', $data);
        $last_id = $this->db->insert_id();
        if($rslts){
            if($callOffBase['callOffBase']==1 && $totalused>0){
                $statusRptId = $this->db->query("SELECT
	opStatusReportID 
FROM
	opstatusreport 
	INNER JOIN ticketmaster ON ticketmaster.ticketidAtuto = opstatusreport.ticketidAtuto
	INNER JOIN op_calloff_master ON op_calloff_master.calloffID = ticketmaster.calloffID
WHERE
	opstatusreport.companyID = '{$companyID}'
	AND opstatusreport.ticketidAtuto = '{$ticketidAtuto}'
	AND op_calloff_master.productId = '{$ContractDetailID}' 
	AND isUsed = 0 ")->result_array();
                foreach ($statusRptId as $val){
                    $opStatusReportID=$val['opStatusReportID'];
                    $datau['TicketproductID'] = $last_id;
                    $datau['isUsed'] = 1;

                    $this->db->where('opStatusReportID', trim($opStatusReportID));
                    $rslts=$this->db->update('opstatusreport', $datau);
                }
            }
            return array('s', 'Successfully Inserted');
        }else{
            return array('e', 'Insert Failed');
        }
    }

    function load_ticket_service(){
        $fltrtype=$this->input->post('fltrtype');
        $filterDate=$this->input->post('filterDate');
        $contractUID=$this->input->post('contractUID');
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $typeid=$this->input->post('typeid');
        $companyID=$this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $where="";


        $serviceDetails = $this->db->query("SELECT TicketproductID,typeId,contractUID,contractDetailID,Description,OurReferance,clientReference,RateCurrency,UnitRate,IFNULL(Qty,'') as Qty,TotalCharges,GLCode,DATE_FORMAT(addedDate,  '$convertFormat'  ) AS addedDat, IFNULL(comments,'') as comments,IFNULL(percentage,'') as percentage,IFNULL(discount,'') as discount,srp_erp_unit_of_measure.UnitShortCode as UnitShortCode FROM product_service_details Left JOIN srp_erp_unit_of_measure ON product_service_details.Unit = srp_erp_unit_of_measure.UnitID  WHERE product_service_details.companyID='{$companyID}' AND ticketidAtuto = '{$ticketidAtuto}' AND contractUID = '{$contractUID}' AND typeId = '$typeid' $where")->result_array();
        return $serviceDetails;
    }

    function update_op_product_service(){
        $TicketproductID=$this->input->post('TicketproductID');
        $valu=$this->input->post('valu');
        $fieldname=$this->input->post('fieldname');


        if($fieldname=='addedDate'){
            $date_format_policy = date_format_policy();
            $addedDate = $valu;
            $addedDated = input_format_date($addedDate, $date_format_policy);
            $valu=$addedDated;
        }

        $this->db->select('ifnull(UnitRate,0) as UnitRate,ifnull(Qty,0) as Qty,ifnull(discount,0) as discount,ifnull(calloffLength,0) as calloffLength');
        $this->db->where('TicketproductID', $TicketproductID);
        $this->db->from('product_service_details');
        $dtls= $this->db->get()->row_array();

        if($fieldname=='Qty'){
            $totcharg=($dtls['UnitRate']*$valu)-$dtls['discount'];
            $percentage=0;
            if($dtls['calloffLength']>0){
                $percentage = $valu/$dtls['calloffLength']*100;
                $percentage=   round($percentage,3);
            }


            $dataw['TotalCharges'] = $totcharg;
            $dataw['percentage'] = $percentage;
            $this->db->where('TicketproductID', trim($TicketproductID));
            $this->db->update('product_service_details', $dataw);
        }

        if($fieldname=='discount'){
            $totcharg=($dtls['UnitRate']*$dtls['Qty'])-$valu;
            $dataw['TotalCharges'] = $totcharg;
            $this->db->where('TicketproductID', trim($TicketproductID));
            $this->db->update('product_service_details', $dataw);
        }

        if($fieldname=='percentage'){
            if($dtls['calloffLength']>0){
                $totqty=($valu*$dtls['calloffLength'])/100;

                $dataq['Qty'] = $totqty;
                $this->db->where('TicketproductID', trim($TicketproductID));
                $this->db->update('product_service_details', $dataq);
            }
        }

        $data[$fieldname] = $valu;
        $this->db->where('TicketproductID', trim($TicketproductID));
        $rslts=$this->db->update('product_service_details', $data);

        if($rslts){
            return array('s', 'Successfully Inserted');
        }else{
            return array('e', 'insert failed');
        }
    }

    function delete_op_product_service(){
        $data=$this->db->delete('product_service_details', array('TicketproductID' => trim($this->input->post('TicketproductID') ?? '')));
        if($data){
            $datau['TicketproductID'] = null;
            $datau['isUsed'] = 0;

            $this->db->where('TicketproductID', trim($this->input->post('TicketproductID') ?? ''));
            $rslts=$this->db->update('opstatusreport', $datau);
            return array('s', 'Deleted Successfully');
        }else{
            return array('e', 'Deletion Failed');
        }
    }

    function addCrewop(){
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $groupID=$this->input->post('ticketCrewEmp');
        $companyID=$this->common_data['company_data']['company_id'];
        if(empty($groupID)){
            return array('e', 'Select Crew Group');
        }else{
            $this->db->select('crewID');
            $this->db->where('ticketidAtuto', trim($ticketidAtuto));
            $this->db->where('companyID', trim($companyID));
            $this->db->where('groupID', trim($groupID));
            $this->db->from('tickets_crew');
            $ticketCrew= $this->db->get()->row_array();
            if(!empty($ticketCrew)){
                return array('e', 'Crew Group already exsist');
            }else{

                $crewmembers=$this->db->query("SELECT empID FROM crewmembers WHERE groupID = $groupID AND companyID=$companyID ")->result_array();
                $success=0;
                foreach ($crewmembers as $crw){
                    $ticketCrewEmp=$crw['empID'];

                    $data['crewID'] = $ticketCrewEmp;
                    $data['ticketidAtuto'] = $ticketidAtuto;
                    $data['companyID'] = $companyID;
                    $data['timestamp'] = date('Y-m-d');
                    $data['groupID'] = $groupID;
                    $rslts=$this->db->insert('tickets_crew', $data);
                    if($rslts){
                        $datah['crewID'] = $ticketCrewEmp;
                        $datah['type'] = 1;
                        $datah['ticketidAtuto'] = $ticketidAtuto;
                        $datah['description'] = 'Crew Added';
                        $datah['companyID'] = $companyID;
                        $datah['createdPcID'] = $this->common_data['current_pc'];
                        $datah['createdUserID'] = $this->common_data['current_userID'];
                        $datah['createdDatetime'] = $this->common_data['current_date'];
                        $rslts=$this->db->insert('tickets_crew_unit_history', $datah);
                        $success++;
                    }
                }

                if($success>0){
                    return array('s', 'Crew Successfully Inserted');
                }



            }
        }
    }


    function deleteOpCrew(){
        $this->db->select('*');
        $this->db->where('ticketCrewID', trim($this->input->post('ticketCrewID') ?? ''));
        $this->db->from('tickets_crew');
        $ticketCrew= $this->db->get()->row_array();

        $data=$this->db->delete('tickets_crew', array('ticketCrewID' => trim($this->input->post('ticketCrewID') ?? '')));
        if($data){
            $datah['crewID'] = $ticketCrew['crewID'];
            $datah['type'] = 1;
            $datah['ticketidAtuto'] = $ticketCrew['ticketidAtuto'];
            $datah['description'] = 'Crew Deleted';
            $datah['companyID'] = $ticketCrew['companyID'];
            $datah['createdPcID'] = $this->common_data['current_pc'];
            $datah['createdUserID'] = $this->common_data['current_userID'];
            $datah['createdDatetime'] = $this->common_data['current_date'];
            $rslts=$this->db->insert('tickets_crew_unit_history', $datah);

            return array('s', 'Deleted Successfully');
        }else{
            return array('e', 'Deletion Failed');
        }
    }

    function addAssetunitop(){
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $faID=$this->input->post('faID');
        $Comment=$this->input->post('Comment');
        $companyID=$this->common_data['company_data']['company_id'];


        if(empty($faID)){
            return array('e', 'Select Asset');
        }else{
            $this->db->select('unitMoreID');
            $this->db->where('ticketidAtuto', trim($ticketidAtuto));
            $this->db->where('companyID', trim($companyID));
            $this->db->where('faID', trim($faID));
            $this->db->from('ticketunintsmore');
            $ticketUnits= $this->db->get()->row_array();
            if(!empty($ticketUnits)){
                return array('e', 'Asset already exsist');
            }else{
                $data['faID'] = $faID;
                $data['ticketidAtuto'] = $ticketidAtuto;
                $data['companyID'] = $companyID;
                $data['Comment'] = $Comment;
                $data['timestamp'] = date('Y-m-d');
                $rslts=$this->db->insert('ticketunintsmore', $data);
                if($rslts){
                    $datah['crewID'] = $faID;
                    $datah['type'] = 2;
                    $datah['ticketidAtuto'] = $ticketidAtuto;
                    $datah['description'] = 'Asset Added';
                    $datah['companyID'] = $companyID;
                    $datah['createdPcID'] = $this->common_data['current_pc'];
                    $datah['createdUserID'] = $this->common_data['current_userID'];
                    $datah['createdDatetime'] = $this->common_data['current_date'];
                    $rslts=$this->db->insert('tickets_crew_unit_history', $datah);
                    return array('s', 'Successfully Inserted');
                }else{
                    return array('e', 'Insert failed');
                }
            }
        }
    }


    function deleteOpAssetUnit(){
        $this->db->select('*');
        $this->db->where('unitMoreID', trim($this->input->post('unitMoreID') ?? ''));
        $this->db->from('ticketunintsmore');
        $ticketUnit= $this->db->get()->row_array();

        $data=$this->db->delete('ticketunintsmore', array('unitMoreID' => trim($this->input->post('unitMoreID') ?? '')));
        if($data){
            $datah['crewID'] = $ticketUnit['faID'];
            $datah['type'] = 2;
            $datah['ticketidAtuto'] = $ticketUnit['ticketidAtuto'];
            $datah['description'] = 'Asset Unit Deleted';
            $datah['companyID'] = $ticketUnit['companyID'];
            $datah['createdPcID'] = $this->common_data['current_pc'];
            $datah['createdUserID'] = $this->common_data['current_userID'];
            $datah['createdDatetime'] = $this->common_data['current_date'];
            $rslts=$this->db->insert('tickets_crew_unit_history', $datah);

            return array('s', 'Deleted Successfully');
        }else{
            return array('e', 'Deletion Failed');
        }
    }


    function confirm_opticket(){
        $system_code = trim($this->input->post('ticketidAtuto') ?? '');

        $this->db->select('ticketidAtuto');
        $this->db->where('ticketidAtuto', $system_code);
        $this->db->from('product_service_details');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            return array('error' => 1, 'message' => 'There are no records to confirm this document!');
            exit;
        }else {
            $this->load->library('Approvals');
            $this->db->select('ticketidAtuto, ticketNo,createdDateTime');
            $this->db->where('ticketidAtuto', $system_code);
            $this->db->from('ticketmaster');
            $optkt_data = $this->db->get()->row_array();

            $autoApproval= get_document_auto_approval('OPJOB');
            if($autoApproval==0){
                $approvals_status = $this->approvals->auto_approve($optkt_data['ticketidAtuto'], 'ticketmaster','ticketidAtuto', 'OPJOB',$optkt_data['ticketNo'],$optkt_data['createdDateTime']);
            }elseif($autoApproval==1){
                $approvals_status = $this->approvals->CreateApproval('OPJOB', $optkt_data['ticketidAtuto'], $optkt_data['ticketNo'], 'Ticket Master', 'ticketmaster', 'ticketidAtuto',0,$optkt_data['createdDateTime']);
            }else{
                return array('error' => 1, 'message' => 'Approval levels are not set for this document.');
                exit;
            }

            if ($approvals_status == 1) {
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user']
                );
                $this->db->where('ticketidAtuto', $system_code);
                $this->db->update('ticketmaster', $data);



                $autoApproval= get_document_auto_approval('OPJOB');

                if($autoApproval==0) {
                    return array('error' => 0, 'message' => 'Document confirmed successfully ');
                    /* $result = $this->save_grv_approval(0, $optkt_data['contractUID'], 1, 'Auto Approved');
                     if($result){
                         return array('error' => 0, 'message' => 'Document confirmed successfully ');
                     }*/
                }else{
                    return array('error' => 0, 'message' => 'Document confirmed successfully ');
                }

            } else if ($approvals_status == 3) {
                return array('error' => 2, 'message' => 'There are no users exist to perform approval for this document.');
            } else {
                return array('error' => 1, 'message' => 'some went wrong!');
            }
        }
    }


    function fetch_ticket_master_detail_data($ticketidAtuto){


        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $where = "ticketmaster.companyID = " . $companyID ." AND ticketmaster.ticketidAtuto = " . $ticketidAtuto ."";
        $this->db->select('ticketidAtuto,ticketNo,Timedatejobstra,Timedatejobend,DATE_FORMAT(ticketmaster.createdDateTime,\'' . $convertFormat . '\') AS createdDate,comments,primaryUnitAssetID,regNo,wellNo,contractUID,fieldmaster.fieldName as fieldName,operationLog,callOffBase,ticketmaster.approvedYN,ticketmaster.approvedDate,ticketmaster.approvedbyEmpID,ticketmaster.approvedbyEmpName');
        $this->db->where($where);
        $this->db->from('ticketmaster');
        $this->db->join('fieldmaster','fieldmaster.FieldID = ticketmaster.primaryUnitAssetID');
        $data['master'] = $this->db->get()->row_array();

        $dat=date('Y-m-d');
        $data['service'] = $this->db->query("SELECT TicketproductID,typeId,contractUID,contractDetailID,Description,OurReferance,clientReference,RateCurrency,UnitRate,Qty,TotalCharges,GLCode,DATE_FORMAT(addedDate,  '$convertFormat'  ) AS addedDat, IFNULL(comments,'') as comments,percentage,discount,srp_erp_unit_of_measure.UnitShortCode as UnitShortCode FROM product_service_details Left JOIN srp_erp_unit_of_measure ON product_service_details.Unit = srp_erp_unit_of_measure.UnitID  WHERE product_service_details.companyID='{$companyID}' AND ticketidAtuto = '{$ticketidAtuto}'  AND typeId = '2' ")->result_array();
        $data['product'] = $this->db->query("SELECT TicketproductID,typeId,contractUID,contractDetailID,Description,OurReferance,clientReference,RateCurrency,UnitRate,Qty,TotalCharges,GLCode,DATE_FORMAT(addedDate,  '$convertFormat'  ) AS addedDat, IFNULL(comments,'') as comments,percentage,discount,srp_erp_unit_of_measure.UnitShortCode as UnitShortCode FROM product_service_details Left JOIN srp_erp_unit_of_measure ON product_service_details.Unit = srp_erp_unit_of_measure.UnitID  WHERE product_service_details.companyID='{$companyID}' AND ticketidAtuto = '{$ticketidAtuto}'  AND typeId = '1' ")->result_array();

        $wherecrew = "tickets_crew.companyID = " . $companyID ." AND tickets_crew.ticketidAtuto = " . $ticketidAtuto ." ";
        $this->db->select('ticketCrewID,crewID,ticketidAtuto,companyID,srp_employeesdetails.Ename2 as crewname');
        $this->db->where($wherecrew);
        $this->db->from('tickets_crew');
        $this->db->join('srp_employeesdetails','srp_employeesdetails.EIdNo = tickets_crew.crewID');
        $data['crew'] = $this->db->get()->result_array();

        $where = "ticketunintsmore.companyID = " . $companyID ." AND ticketunintsmore.ticketidAtuto = " . $ticketidAtuto ." ";
        $this->db->select('unitMoreID,Comment,srp_erp_fa_asset_master.assetDescription as assetDescription');
        $this->db->where($where);
        $this->db->from('ticketunintsmore');
        $this->db->join('srp_erp_fa_asset_master','srp_erp_fa_asset_master.faID = ticketunintsmore.faID');
        $data['asset'] = $this->db->get()->result_array();

        return $data;
    }


    function save_ticket_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('ticketidAtuto') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['ticketidAtuto'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        $company_id = $this->common_data['company_data']['company_id'];

        $maxLevel = $this->approvals->maxlevel('OPJOB');

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;

        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'OPJOB');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function load_txt_invoice(){
        $ticketidAtuto=$this->input->post('ticketidAtuto');
        $companyID = $this->common_data['company_data']['company_id'];

        $convertFormat = convert_date_format_sql();

        $tktinv = $this->db->query("SELECT
	invoiceCode, 
	DATE_FORMAT(invoiceDate,'$convertFormat') AS invoiceDate
FROM
	(
	SELECT
		invoiceAutoID,
		ticketmaster.ticketidAtuto 
	FROM
		srp_erp_customerinvoicedetails
		INNER JOIN ticketmaster ON ticketmaster.ticketNo = srp_erp_customerinvoicedetails.ticketNo 
	WHERE
		ticketmaster.ticketidAtuto = '$ticketidAtuto' 
		AND ticketmaster.companyID='$companyID'
	GROUP BY
		srp_erp_customerinvoicedetails.ticketNo 
	) t
	INNER JOIN srp_erp_customerinvoicemaster ON t.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID")->result_array();
        return $tktinv;
    }

    function confirmProformaInvoice(){
        $ticketidAtuto=$this->input->post('ticketidAtuto');


        $data = array(
            'proformaConfirmationYN' => 1,
            'proformaConfirmedDate' => $this->common_data['current_date'],
            'proformaConfirmedByEmpID' => $this->common_data['current_userID'],
            'proformaConfirmedByName' => $this->common_data['current_user']
        );
        $this->db->where('ticketidAtuto', $ticketidAtuto);
        $result=$this->db->update('ticketmaster', $data);

        if($result) {
            return array('s','Successfully Confirmed');
        }else{
            return array('e','Proforma Confirmation failed');
        }
    }


    function fetch_ticket_master_detail_proforma_data($ticketidAtuto){


        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();


        $this->db->select('ticketmaster.calloffID as callofId,ticketNo,ticketmaster.confirmedYN as confirmedYN,srp_erp_customermaster.customerName as customerName,srp_erp_customermaster.customerAddress1 as customerAddress1,srp_erp_customermaster.customerCountry as customerCountry,DATE_FORMAT(ticketmaster.expectedEndDate,\'' . $convertFormat . '\') AS expectedEndDate,activity,poNumber,productId, op_calloff_master.length,ticketNo,fieldmaster.fieldName,op_calloff_master.wellNo,clientID,DATE_FORMAT(ticketmaster.createdDateTime,\'' . $convertFormat . '\') AS createdDate,DATE_FORMAT(ticketmaster.Timedatejobend,\'' . $convertFormat . '\') AS Timedatejobend,op_calloff_master.RDX,drawingNo,joints,comments,contractRefNo,ticketmaster.createdDateTime as createdDateTime');
        $this->db->where('ticketidAtuto', trim($ticketidAtuto));
        $this->db->from('ticketmaster');
        $this->db->join('srp_erp_customermaster','srp_erp_customermaster.customerAutoID = ticketmaster.clientID');
        $this->db->join('op_calloff_master','op_calloff_master.calloffID = ticketmaster.calloffID');
        $this->db->join('fieldmaster','fieldmaster.FieldID = op_calloff_master.location');
        $data['master'] = $this->db->get()->row_array();

        $dat=date('Y-m-d');
        $data['service'] = $this->db->query("SELECT TicketproductID,typeId,contractUID,contractDetailID,Description,OurReferance,clientReference,RateCurrency,UnitRate,Qty,TotalCharges,GLCode,DATE_FORMAT(addedDate,  '$convertFormat'  ) AS addedDat, IFNULL(comments,'') as comments,percentage,discount,srp_erp_unit_of_measure.UnitShortCode as UnitShortCode FROM product_service_details Left JOIN srp_erp_unit_of_measure ON product_service_details.Unit = srp_erp_unit_of_measure.UnitID  WHERE product_service_details.companyID='{$companyID}' AND ticketidAtuto = '{$ticketidAtuto}'  AND typeId = '2' AND addedDate<='$dat'")->result_array();
        $data['product'] = $this->db->query("SELECT
	*,
	srp_erp_unit_of_measure.UnitShortCode AS uom,
	MONTH ( addedDate ) AS addedmnth 
FROM
	product_service_details
	LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = product_service_details.Unit 
WHERE
	ticketidAtuto = $ticketidAtuto 
	AND product_service_details.companyID = '$companyID' 
	AND typeId = '1' ")->result_array();

        $wherecrew = "tickets_crew.companyID = " . $companyID ." AND tickets_crew.ticketidAtuto = " . $ticketidAtuto ." ";
        $this->db->select('ticketCrewID,crewID,ticketidAtuto,companyID,srp_employeesdetails.Ename2 as crewname');
        $this->db->where($wherecrew);
        $this->db->from('tickets_crew');
        $this->db->join('srp_employeesdetails','srp_employeesdetails.EIdNo = tickets_crew.crewID');
        $data['crew'] = $this->db->get()->result_array();

        $where = "ticketunintsmore.companyID = " . $companyID ." AND ticketunintsmore.ticketidAtuto = " . $ticketidAtuto ." ";
        $this->db->select('unitMoreID,Comment,srp_erp_fa_asset_master.assetDescription as assetDescription');
        $this->db->where($where);
        $this->db->from('ticketunintsmore');
        $this->db->join('srp_erp_fa_asset_master','srp_erp_fa_asset_master.faID = ticketunintsmore.faID');
        $data['asset'] = $this->db->get()->result_array();

        return $data;
    }

    function check_employee_attach_expired(){
        $groupID=$this->input->post('groupID');
        $companyID=current_companyID();
        $currDate=current_date(false);

        $crewmembers=$this->db->query("SELECT
	empID
FROM
	crewmembers
WHERE
	groupID = $groupID 
	AND companyID=$companyID
	 ")->result_array();

        if(empty($crewmembers)){
            return array('e','Selected Group has no member added',0);
            exit;
        }else{

            $ticketEmp=array_column($crewmembers, 'empID');
            $ticketCrewEmp=join(",",$ticketEmp);
            $docexp = $this->db->query("SELECT

    CONCAT(srp_employeesdetails.ECode,' - ',srp_employeesdetails.Ename2) as empdtl,
	DocDesFormID,
	srp_documentdescriptionmaster.DocDescription as DocDescription,
	'-' as datdiff,
	'Document Expired' as descp
FROM
	srp_documentdescriptionforms
	LEFT join srp_documentdescriptionmaster ON srp_documentdescriptionforms.DocDesID = srp_documentdescriptionmaster.DocDesID
	LEFT join srp_employeesdetails ON srp_documentdescriptionforms.PersonID = srp_employeesdetails.EIdNo
WHERE
	PersonID IN ($ticketCrewEmp)
	AND PersonType='E'
	AND srp_documentdescriptionforms.isDeleted=0
	AND srp_documentdescriptionforms.isActive=1
	AND srp_documentdescriptionforms.Erp_companyID = '$companyID' 
	AND expireDate<'$currDate'
	
	Union ALL 
	
	SELECT
	CONCAT(srp_employeesdetails.ECode,' - ',srp_employeesdetails.Ename2) as empdtl,
	DocDesFormID,
	srp_documentdescriptionmaster.DocDescription as DocDescription,
	DATEDIFF(expireDate,'$currDate') as datdiff,
	'Document nearing to Expire' as descp
FROM
	srp_documentdescriptionforms
	LEFT join srp_documentdescriptionmaster ON srp_documentdescriptionforms.DocDesID = srp_documentdescriptionmaster.DocDesID
	LEFT join srp_employeesdetails ON srp_documentdescriptionforms.PersonID = srp_employeesdetails.EIdNo
	
WHERE
	PersonID IN ($ticketCrewEmp)
	AND PersonType='E'
	AND srp_documentdescriptionforms.isDeleted=0
	AND srp_documentdescriptionforms.isActive=1
	AND srp_documentdescriptionforms.Erp_companyID = '$companyID' 
	HAVING datdiff<30
	 ")->result_array();





            if(!empty($docexp)){
                return array('e','expiry',2,$docexp);
                exit;
            }

        }



        return array('s','success',0);
    }


    function check_asset_attach_expired(){
        $faID=$this->input->post('faID');
        $companyID=current_companyID();
        $currDate=current_date(false);

        $docexp = $this->db->query("SELECT
	attachmentID,
	attachmentDescription
FROM
	srp_erp_documentattachments
WHERE
	documentSystemCode = $faID 
	AND documentID='AST'
	AND companyID = '$companyID' 
	AND docExpiryDate<'$currDate'
	 ")->row_array();

        if(!empty($docexp)){
            $DocDescription=$docexp['attachmentDescription'];
            return array('e','Document '.$DocDescription.' has expired',1);
            exit;
        }

        $docpendingexp = $this->db->query("SELECT
	attachmentID,
	attachmentDescription,
	DATEDIFF(docExpiryDate,'$currDate') as datdiff
FROM
	srp_erp_documentattachments
WHERE
	documentSystemCode = $faID 
	AND documentID='AST'
	AND companyID = '$companyID' 
	HAVING datdiff<30
	 ")->row_array();

        if(!empty($docpendingexp)){
            $DocDescription=$docpendingexp['attachmentDescription'];
            $datdiff=$docpendingexp['datdiff'];
            return array('e','Selected asset has documents which will expire in '.$datdiff.' days',1);
            exit;
        }

        return array('s','success',0);
    }


    function loadCallOffChart(){
        $contractUID = $this->input->post('contractUID');
        $customerAutoID = $this->input->post('customerAutoID');
        $calloffID = $this->input->post('calloffID');

        if (empty($customerAutoID)) {

        }

        if (empty($contractUID)) {

        }

        if (empty($calloffID)) {

        }

        $clientID = join(',', $customerAutoID);
        $contractID = join(',', $contractUID);
        $calloffID  = join(',', $calloffID);

        $ContractRefNos = $this->db->query("SELECT
	op_calloff_master.description,IFNULL(op_calloff_master.length,0) as calloflength,SUM(IFNULL( txtmaster.ticketlength, 0 )) AS ticketlength
FROM
	op_calloff_master
	LEFT JOIN (
	SELECT
		SUM( product_service_details.Qty ) AS ticketlength,
		calloffID,
		product_service_details.contractDetailID  
	FROM
		ticketmaster
		LEFT JOIN product_service_details ON product_service_details.ticketidAtuto = ticketmaster.ticketidAtuto 
	WHERE
		clientID IN ({$clientID}) 
		AND ticketmaster.contractUID IN ({$contractID}) 
		AND calloffID IN ({$calloffID})
		GROUP BY
		calloffID , contractDetailID
	) txtmaster ON txtmaster.calloffID = op_calloff_master.calloffID AND `op_calloff_master`.`productId` = txtmaster.contractDetailID
WHERE
	op_calloff_master.calloffID IN ({$calloffID})
	GROUP BY op_calloff_master.calloffID
	 ")->result_array();

        $datas = array();
        foreach ($ContractRefNos as $ContractRefNo) {
            $datas['description'][] = $ContractRefNo['description'];
            $datas['calloflength'][] = (float)$ContractRefNo['calloflength'];
            $datas['ticketlength'][] = (float)$ContractRefNo['ticketlength'];

        }

        return $datas;
    }

    function select_master_currency(){
        $contractUID=$this->input->post('contractUID');

        $this->db->select('ContCurrencyID');
        $this->db->where('contractUID', $contractUID);
        $this->db->from('contractmaster');
        return $this->db->get()->row_array();
    }

    function save_crew_group(){
        $companyID=current_companyID();
        $groupName=$this->input->post('groupName');
        $this->db->select('groupName');
        $this->db->where('groupName', trim($groupName));
        $this->db->where('companyID', trim($companyID));
        $this->db->from('crewgroup');
        $ticketCrew= $this->db->get()->row_array();

        if(!empty($ticketCrew)){
            return array('e','Crew Group already exsist');
        }else{
            $data['timestamp'] = $this->common_data['current_date'];
            $data['groupName'] = $groupName;
            $data['companyID'] = $companyID;
            $rslts=$this->db->insert('crewgroup', $data);
            if($rslts){
                return array('s','Crew Group saved successfully');
            }
        }
    }

    function deleteOpCrewGroup(){
        $groupID=$this->input->post('groupID');
        $companyID=current_companyID();

        $this->db->select('groupID');
        $this->db->where('groupID', trim($groupID));
        $this->db->where('companyID', trim($companyID));
        $this->db->from('tickets_crew');
        $groupIDs= $this->db->get()->row_array();

        if(!empty($groupIDs)){
            return array('e','Crew group has been pulled in tickets');
        }else{
            $data=$this->db->delete('crewgroup', array('groupID' => trim($groupID)));
            if($data){
                $this->db->delete('crewmembers', array('groupID' => trim($groupID)));
                return array('s','Group Deleted successfully');
            }
        }
    }

    function update_crew_group(){
        $groupID=$this->input->post('groupID');
        $groupName=$this->input->post('groupName');
        $companyID=current_companyID();

        $this->db->select('groupName');
        $this->db->where('groupName', trim($groupName));
        $this->db->where('groupID!=', trim($groupID));
        $this->db->where('companyID', trim($companyID));
        $this->db->from('crewgroup');
        $ticketCrew= $this->db->get()->row_array();

        if(!empty($ticketCrew)){
            return array('e','Crew Group already exsist');
        }else{
            $data['groupName'] = $groupName;

            $this->db->where('groupID', $groupID);
            $result=$this->db->update('crewgroup', $data);
            if($result){
                return array('s','Crew Group updated successfully');
            }
        }
    }

    function load_crew_members_drop(){
        $companyID=current_companyID();
        $this->db->select('empID');
        $this->db->where('companyID', trim($companyID));
        $this->db->from('crewmembers');
        $crewmembers= $this->db->get()->result_array();
        $where='';
        if(!empty($crewmembers)){
            $empID = array_column($crewmembers, 'empID');
            $empID=join(",",$empID);
            $where='AND EIdNo NOT IN('.$empID.')';
        }

        $members = $this->db->query("SELECT
	EIdNo,
	ECode,
	Ename2
FROM
	srp_employeesdetails
WHERE
        isActive=1
	AND isDischarged=0
	AND Erp_companyID = '$companyID' 
	$where
	 ")->result_array();

        return $members;
    }

    function add_members_to_group(){
        $empID=$this->input->post('empID');
        $groupID=$this->input->post('groupID');
        $companyID=current_companyID();
        $i = 0;
        foreach ($empID as $val){
            $data[$i]['groupID'] = $groupID;
            $data[$i]['empID'] = $val;
            $data[$i]['companyID'] = $companyID;
            $data[$i]['timestamp'] = $this->common_data['current_date'];
            $i++;
        }
        $result = $this->db->insert_batch('crewmembers', $data);
        if($result){
            return array('s','Crew members added successfully');
        }
    }

    function update_supervisor_yn(){
        $crewmemberID=$this->input->post('crewmemberID');
        $valu=$this->input->post('valu');
        $groupID=$this->input->post('groupID');
        $companyID=current_companyID();

        if($valu==1){
            $data['supervisorYN'] = 0;
            $this->db->where('companyID', $companyID);
            $this->db->where('groupID', $groupID);
            $rslt=$this->db->update('crewmembers', $data);
            if($rslt){
                $datas['supervisorYN'] = 1;
                $this->db->where('crewmemberID', $crewmemberID);
                $rslts=$this->db->update('crewmembers', $datas);

                if($rslts){
                    return array('s', 'Supervisor successfully added.');
                }
            }
        }else{
            $data['supervisorYN'] = 0;
            $this->db->where('crewmemberID', $crewmemberID);
            $rslts=$this->db->update('crewmembers', $data);

            if($rslts){
                return array('s', 'Supervisor successfully removed.');
            }
        }
    }

    function deleteOpCrewMember(){
        $crewmemberID=$this->input->post('crewmemberID');
        $companyID=current_companyID();
        $groupID=$this->input->post('groupID');

        $this->db->select('groupID');
        $this->db->where('groupID', trim($groupID));
        $this->db->where('companyID', trim($companyID));
        $this->db->from('tickets_crew');
        $groupIDs= $this->db->get()->row_array();

        if(!empty($groupIDs)){
            return array('e','Crew group has been pulled in tickets');
        }else{
            $rslts=$this->db->delete('crewmembers', array('crewmemberID' => trim($crewmemberID)));
            if($rslts){
                return array('s', 'Crew Deleted successfully.');
            }
        }
    }


    function calloffDD(){
        $calloffID=$this->input->post('calloffID');
        $companyID=current_companyID();
        $calloffdd = $this->db->query("SELECT
	ticketmaster.ticketidAtuto,
	ticketmaster.ticketNo,
	ticketmaster.contractUID,
	Inv.invoiceAutoID,
	Inv.invoiceAutoIDR,
	Inv.documentID,
	IFNULL(Inv.invoiceCode,' ') as invoiceCode,
	IFNULL(Inv.transactionAmount,0) as transactionAmount,
	IFNULL(Inv.invoiceCodeR,' ') as invoiceCodeR,
	IFNULL(Inv.transactionAmountR,0) as transactionAmountR

FROM
	ticketmaster
	LEFT JOIN (
	SELECT
		cinvm.invoiceCode,
		cinvd.invoiceAutoID,
		cinvmr.invoiceAutoID as invoiceAutoIDR,
		cinvm.documentID,
		cinvd.ticketNo,
		cinvd.callOffID,
		cinvm.transactionAmount,
		cinvmr.retensionInvoiceID,
		cinvmr.invoiceCode as invoiceCodeR,
		cinvmr.transactionAmount as transactionAmountR	
	FROM
		srp_erp_customerinvoicedetails cinvd
		INNER JOIN srp_erp_customerinvoicemaster cinvm ON cinvm.invoiceAutoID = cinvd.invoiceAutoID 
		LEFT JOIN srp_erp_customerinvoicemaster cinvmr ON cinvmr.retensionInvoiceID=cinvm.invoiceAutoID
	WHERE
		cinvd.ticketNo IS NOT NULL 
		AND cinvd.companyID = $companyID 
	GROUP BY
		cinvd.invoiceAutoID 
	) Inv ON Inv.ticketNo=ticketmaster.ticketidAtuto AND Inv.callOffID=ticketmaster.calloffID
	
WHERE
	ticketmaster.calloffID = $calloffID 
	AND ticketmaster.companyID = $companyID 
	 ")->result_array();

        return $calloffdd;
    }


    function load_month_wise_retention(){
        $mnthyearfltr=$this->input->post('mnthyearfltr');
        $companyID=current_companyID();
        $invoice = $this->db->query("SELECT
yr,
mnth,
FORMAT(transactionAmount, transactionAmountDecimalPlaces) as transactionAmount,
transactionAmountcurrency,
transactionAmountDecimalPlaces,
	CASE
    WHEN mnth = 1 THEN 'January'
    WHEN mnth = 2 THEN 'Febuary'
    WHEN mnth = 3 THEN 'March'
    WHEN mnth = 4 THEN 'April'
    WHEN mnth = 5 THEN 'May'
    WHEN mnth = 6 THEN 'June'
    WHEN mnth = 7 THEN 'Jully'
    WHEN mnth = 8 THEN 'Auguest'
    WHEN mnth = 9 THEN 'September'
    WHEN mnth = 10 THEN 'October'
    WHEN mnth = 11 THEN 'November'
    WHEN mnth = 12 THEN 'December'
    ELSE '-'
END AS mnthdesc
FROM
	(
	SELECT YEAR
		( srp_erp_customerinvoicemaster.invoiceDate ) AS yr,
		MONTH ( srp_erp_customerinvoicemaster.invoiceDate ) AS mnth,
		(
			SUM( srp_erp_customerinvoicemaster.transactionAmount - ( IFNULL( retensionTransactionAmount, 0 ) ) - IFNULL( srp_erp_customerinvoicemaster.rebateAmount, 0 ) ) - (
				IFNULL( SUM( pvd.transactionAmount ), 0 ) + IFNULL( SUM( cnd.transactionAmount ), 0 ) + IFNULL( SUM( ca.transactionAmount ), 0 ) 
			) 
		) AS transactionAmount,
		srp_erp_customerinvoicemaster.transactionCurrency AS transactionAmountcurrency,
		TC.DecimalPlaces AS transactionAmountDecimalPlaces 
	FROM
		`srp_erp_customerinvoicemaster`
		LEFT JOIN (
		SELECT
			IFNULL( SUM( srp_erp_customerreceiptdetail.transactionAmount ), 0 ) AS transactionAmount,
			srp_erp_customerreceiptdetail.invoiceAutoID,
			srp_erp_customerreceiptdetail.receiptVoucherAutoID 
		FROM
			srp_erp_customerreceiptdetail
			INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` 
			AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 
		WHERE
			`srp_erp_customerreceiptdetail`.`companyID` = $companyID 
		GROUP BY
			srp_erp_customerreceiptdetail.invoiceAutoID 
		) pvd ON ( pvd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` )
		LEFT JOIN (
		SELECT
			SUM( srp_erp_creditnotedetail.transactionAmount ) AS transactionAmount,
			invoiceAutoID,
			srp_erp_creditnotedetail.creditNoteMasterAutoID 
		FROM
			srp_erp_creditnotedetail
			INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` 
			AND `srp_erp_creditnotemaster`.`approvedYN` = 1 
		WHERE
			`srp_erp_creditnotedetail`.`companyID` = $companyID 
		GROUP BY
			srp_erp_creditnotedetail.invoiceAutoID 
		) cnd ON ( cnd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` )
		LEFT JOIN (
		SELECT
			SUM( srp_erp_rvadvancematchdetails.transactionAmount ) AS transactionAmount,
			srp_erp_rvadvancematchdetails.InvoiceAutoID,
			srp_erp_rvadvancematchdetails.receiptVoucherAutoID 
		FROM
			srp_erp_rvadvancematchdetails
			INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` 
			AND `srp_erp_rvadvancematch`.`confirmedYN` = 1 
		WHERE
			`srp_erp_rvadvancematchdetails`.`companyID` = $companyID 
		GROUP BY
			srp_erp_rvadvancematchdetails.InvoiceAutoID 
		) ca ON ( ca.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID` )
		LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_customerinvoicemaster.companyReportingCurrencyID )
		LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_customerinvoicemaster.companyLocalCurrencyID )
		LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) TC ON ( TC.currencyID = srp_erp_customerinvoicemaster.transactionCurrencyID ) 
	WHERE
		`srp_erp_customerinvoicemaster`.`companyID` = $companyID 
		AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 
		AND srp_erp_customerinvoicemaster.isOpYN = 1 
		AND srp_erp_customerinvoicemaster.retensionInvoiceID IS NOT NULL 
		AND YEAR(srp_erp_customerinvoicemaster.invoiceDate)= $mnthyearfltr
	GROUP BY
		MONTH ( srp_erp_customerinvoicemaster.invoiceDate ),
		YEAR ( srp_erp_customerinvoicemaster.invoiceDate ) 
	ORDER BY
		YEAR ( srp_erp_customerinvoicemaster.invoiceDate ) DESC,
	MONTH ( srp_erp_customerinvoicemaster.invoiceDate ) ASC 
	) fnl
")->result_array();

        return $invoice;
    }


    function monthwiseretentionDD(){
        $year=$this->input->post('year');
        $month=$this->input->post('month');
        $companyID=current_companyID();
        $invoice = $this->db->query("SELECT
bookingInvCode,
yr,
mnth,
FORMAT(transactionAmount, transactionAmountDecimalPlaces) as transactionAmount,
transactionAmountcurrency,
transactionAmountDecimalPlaces,
documentID,
invoiceAutoID
FROM
	(
	SELECT 
	    srp_erp_customerinvoicemaster.invoiceCode AS bookingInvCode,
	    srp_erp_customerinvoicemaster.documentID,
	    srp_erp_customerinvoicemaster.invoiceAutoID,
	    YEAR( srp_erp_customerinvoicemaster.invoiceDate ) AS yr,
		MONTH ( srp_erp_customerinvoicemaster.invoiceDate ) AS mnth,
		(
			SUM( srp_erp_customerinvoicemaster.transactionAmount - ( IFNULL( retensionTransactionAmount, 0 ) ) - IFNULL( srp_erp_customerinvoicemaster.rebateAmount, 0 ) ) - (
				IFNULL( SUM( pvd.transactionAmount ), 0 ) + IFNULL( SUM( cnd.transactionAmount ), 0 ) + IFNULL( SUM( ca.transactionAmount ), 0 ) 
			) 
		) AS transactionAmount,
		srp_erp_customerinvoicemaster.transactionCurrency AS transactionAmountcurrency,
		TC.DecimalPlaces AS transactionAmountDecimalPlaces 
	FROM
		`srp_erp_customerinvoicemaster`
		LEFT JOIN (
		SELECT
			IFNULL( SUM( srp_erp_customerreceiptdetail.transactionAmount ), 0 ) AS transactionAmount,
			srp_erp_customerreceiptdetail.invoiceAutoID,
			srp_erp_customerreceiptdetail.receiptVoucherAutoID 
		FROM
			srp_erp_customerreceiptdetail
			INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` 
			AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 
		WHERE
			`srp_erp_customerreceiptdetail`.`companyID` = $companyID 
		GROUP BY
			srp_erp_customerreceiptdetail.invoiceAutoID 
		) pvd ON ( pvd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` )
		LEFT JOIN (
		SELECT
			SUM( srp_erp_creditnotedetail.transactionAmount ) AS transactionAmount,
			invoiceAutoID,
			srp_erp_creditnotedetail.creditNoteMasterAutoID 
		FROM
			srp_erp_creditnotedetail
			INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` 
			AND `srp_erp_creditnotemaster`.`approvedYN` = 1 
		WHERE
			`srp_erp_creditnotedetail`.`companyID` = $companyID 
		GROUP BY
			srp_erp_creditnotedetail.invoiceAutoID 
		) cnd ON ( cnd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` )
		LEFT JOIN (
		SELECT
			SUM( srp_erp_rvadvancematchdetails.transactionAmount ) AS transactionAmount,
			srp_erp_rvadvancematchdetails.InvoiceAutoID,
			srp_erp_rvadvancematchdetails.receiptVoucherAutoID 
		FROM
			srp_erp_rvadvancematchdetails
			INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` 
			AND `srp_erp_rvadvancematch`.`confirmedYN` = 1 
		WHERE
			`srp_erp_rvadvancematchdetails`.`companyID` = $companyID 
		GROUP BY
			srp_erp_rvadvancematchdetails.InvoiceAutoID 
		) ca ON ( ca.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID` )
		LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_customerinvoicemaster.companyReportingCurrencyID )
		LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_customerinvoicemaster.companyLocalCurrencyID )
		LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) TC ON ( TC.currencyID = srp_erp_customerinvoicemaster.transactionCurrencyID ) 
	WHERE
		`srp_erp_customerinvoicemaster`.`companyID` = $companyID 
		AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 
		AND srp_erp_customerinvoicemaster.isOpYN = 1 
		AND srp_erp_customerinvoicemaster.retensionInvoiceID IS NOT NULL 
		AND YEAR(srp_erp_customerinvoicemaster.invoiceDate)= $year
		AND MONTH(srp_erp_customerinvoicemaster.invoiceDate)= $month
	GROUP BY
		srp_erp_customerinvoicemaster.invoiceAutoID
	ORDER BY
		YEAR ( srp_erp_customerinvoicemaster.invoiceDate ) DESC,
	MONTH ( srp_erp_customerinvoicemaster.invoiceDate ) ASC 
	) fnl
")->result_array();

        return $invoice;
    }


    function get_emp_job_history_report($fromdt,$todt){
        $companyID=current_companyID();
        $emphistory = $this->db->query("SELECT
	tcuh.ticketCrewHistoryID,
	tcuh.crewID,
	tcuh.description,
	tcuh.ticketidAtuto,
	srp_employeesdetails.Ename2,
	srp_employeesdetails.ECode,
	DATE_FORMAT( tcuh.createdDatetime, '%Y-%m-%d' ) AS startdate,
	IFNULL(credlt.endate,'$todt') as endate,
	ticketmaster.comments,
	ticketmaster.ticketNo,
	op_calloff_master.length,
	op_calloff_master.description as callof
FROM
	tickets_crew_unit_history tcuh
	INNER JOIN srp_employeesdetails ON tcuh.crewID = srp_employeesdetails.EIdNo
	LEFT JOIN ticketmaster ON tcuh.ticketidAtuto=ticketmaster.ticketidAtuto
	LEFT JOIN op_calloff_master ON op_calloff_master.calloffID=ticketmaster.calloffID
	LEFT JOIN (
	SELECT
		DATE_FORMAT( tickets_crew_unit_history.createdDatetime, '%Y-%m-%d' ) AS endate,
		ticketidAtuto,
		crewID 
	FROM
		tickets_crew_unit_history 
	WHERE
		tickets_crew_unit_history.type = 1 
		AND tickets_crew_unit_history.companyID = $companyID 
		AND description = 'Crew Deleted' 
	) credlt ON credlt.crewID = tcuh.crewID 
	AND credlt.ticketidAtuto = tcuh.ticketidAtuto 
WHERE
	tcuh.type = 1 
	AND tcuh.companyID = $companyID 
	AND tcuh.createdDatetime BETWEEN '$fromdt' AND '$todt' 
	AND tcuh.description = 'Crew Added'")->result_array();


        return $emphistory;
    }


    function get_asset_history_report($fromdt,$todt){
        $companyID=current_companyID();
        $assethistory = $this->db->query("SELECT
	tcuh.ticketCrewHistoryID,
	tcuh.crewID,
	tcuh.description,
	tcuh.ticketidAtuto,
	srp_erp_fa_asset_master.assetDescription,
	srp_erp_fa_asset_master.faCode,
	DATE_FORMAT( tcuh.createdDatetime, '%Y-%m-%d' ) AS startdate,
	IFNULL(credlt.endate,'$todt') as endate,
	ticketmaster.comments,
	ticketmaster.ticketNo,
	op_calloff_master.length,
	op_calloff_master.description as callof
FROM
	tickets_crew_unit_history tcuh
	INNER JOIN srp_erp_fa_asset_master ON tcuh.crewID = srp_erp_fa_asset_master.faID
	LEFT JOIN ticketmaster ON tcuh.ticketidAtuto=ticketmaster.ticketidAtuto
	LEFT JOIN op_calloff_master ON op_calloff_master.calloffID=ticketmaster.calloffID
	LEFT JOIN (
	SELECT
		DATE_FORMAT( tickets_crew_unit_history.createdDatetime, '%Y-%m-%d' ) AS endate,
		ticketidAtuto,
		crewID 
	FROM
		tickets_crew_unit_history 
	WHERE
		tickets_crew_unit_history.type = 2 
		AND tickets_crew_unit_history.companyID = $companyID 
		AND description = 'Asset Unit Deleted' 
	) credlt ON credlt.crewID = tcuh.crewID 
	AND credlt.ticketidAtuto = tcuh.ticketidAtuto 
WHERE
	tcuh.type = 2 
	AND tcuh.companyID = $companyID 
	AND tcuh.createdDatetime BETWEEN '$fromdt' AND '$todt'
	 AND tcuh.description = 'Asset Added'")->result_array();


        return $assethistory;
    }

    function load_checklist_single(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_daily_equipment_inspection($cid){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name, category_id FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND category_id='$cid' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }



    function load_checklist_rig_crew_inspection(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_wellsite(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_field_ticket(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_rig_turn_over(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }






    function load_checklist_system_inspection(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_well_program_steps(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_supervisors_overview(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_well_program(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_pipe_tally(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_power_swivel($cid){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND category_id='$cid' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_fishing(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_assistant_driller(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_driller(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_crane_inspection(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name, image_path FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_forklift(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name,image_path FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_rig(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_rig_move(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_tool_box(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, master_id, qtn_name FROM srp_erp_op_checklist_questiions WHERE status=1 AND master_id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function load_checklist_header(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $result = $this->db->query("SELECT id, name, document_reference_code FROM srp_erp_op_checklist_master WHERE id='$id' AND companyID = '$companyID' ")->result_array();
    
        return $result;
    }

    function updateChecklistActive(){
        $companyID=current_companyID();
        $id=$this->input->post('id');

        $CheckCurrentStatus =  $this->db->query("SELECT status FROM srp_erp_op_checklist_master WHERE id='$id' AND companyID = '$companyID' ")->row()->status;
        
        if($CheckCurrentStatus == '1'){
            $data['status'] = 0;
        } else{
            $data['status'] = 1;
        }        

        $this->db->where('id', $id);
        $this->db->where('companyID', $companyID);
        $result=$this->db->update('srp_erp_op_checklist_master', $data);
        if($result){
            return array('s','Updated successfully');
        }
        return $result;
    }

}
