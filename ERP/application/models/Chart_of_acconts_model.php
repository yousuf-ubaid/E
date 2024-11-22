<?php
class Chart_of_acconts_model extends ERP_Model
{

    function save_chart_of_accont()
    {
        $this->db->trans_start();
        $controlAccountUpdate = $this->input->post('controlAccountUpdate');
        if ($controlAccountUpdate == 0)
        {  //if not control account update
            $isActive = 0;
            if (!empty($this->input->post('isActive')))
            {
                $isActive = 1;
            }

            $isDefaultlBank = 0;
            if (!empty($this->input->post('isDefaultlBank')))
            {
                $isDefaultlBank = 1;
            }

            if ($isDefaultlBank == 1)
            {
                if (trim($this->input->post('GLAutoID') ?? ''))
                {
                    $this->db->select('GLAutoID');
                    $this->db->where('isDefaultlBank', 1);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('GLAutoID !=', $this->input->post('GLAutoID'));
                    $exsist = $this->db->get('srp_erp_chartofaccounts')->row_array();

                    if (!empty($exsist))
                    {
                        $this->session->set_flashdata('e', 'Default bank already exist');
                        $this->db->trans_rollback();
                        return array('status' => false);
                    }
                }
                else
                {
                    $this->db->select('GLAutoID');
                    $this->db->where('isDefaultlBank', 1);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $exsist = $this->db->get('srp_erp_chartofaccounts')->row_array();
                    if (!empty($exsist))
                    {
                        $this->session->set_flashdata('e', 'Default bank already exist');
                        $this->db->trans_rollback();
                        return array('status' => false);
                    }
                }
            }



            $account_type             = explode('|', trim($this->input->post('account_type') ?? ''));
            $data['accountCategoryTypeID']              = trim($this->input->post('accountCategoryTypeID') ?? '');
            $data['masterCategory']                     = trim($account_type[0] ?? '');
            $data['subCategory']                        = trim($account_type[1] ?? '');
            $data['CategoryTypeDescription']            = trim($account_type[2] ?? '');


            $data['isBank']                             = trim($this->input->post('isBank') ?? '');
            $data['isCard']                             = trim($this->input->post('isCard') ?? '');
            $data['isCash']                             = trim($this->input->post('isCash') ?? '');
            /*$data['authourizedSignatureLevel']          = trim($this->input->post('authourizedSignatureLevel') ?? '');*/
            if ($data['isCash'] == 1)
            {
                $data['bankAccountNumber']                  = 'N/A';
                $data['bankName']                           = trim($this->input->post('GLDescription') ?? '');
                $data['bankBranch']                         = '-';
            }
            else
            {
                $data['bankAccountNumber']                  = trim($this->input->post('bankAccountNumber') ?? '');
                $data['bankName']                           = trim($this->input->post('bankName') ?? '');
                $data['bankBranch']                         = trim($this->input->post('bank_branch') ?? '');
                $data['bankAddress']                         = trim($this->input->post('bank_address') ?? '');
            }


            $data['bankSwiftCode']                      = trim($this->input->post('bank_swift_code') ?? '');
            $data['bankCheckNumber']                    = trim($this->input->post('bankCheckNumber') ?? '');
            $data['masterAccountYN']                    = trim($this->input->post('masterAccountYN') ?? '');
            $data['bankCurrencyCode']                   = trim($this->input->post('bankCurrencyCode') ?? '');
            /*if currencyCode set get currencyID*/
            if ($data['isCash'] == 0)
            {
                $data['bankCurrencyID']                  = $this->input->post('bankCurrencyCode');
                $data['bankCurrencyCode']                = get_currency_code($data['bankCurrencyCode']);
            }
            else
            {
                $data['bankCurrencyID'] = '';
            }

            if ($data['isCash'] == 1)
            {
                $data['bankCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            }
            if ($data['bankCurrencyID'] != '' && $data['bankCurrencyID'] == 1)
            {
                $data['bankCurrencyDecimalPlaces'] = 3;
            }

            $data['approvedYN'] = 1;
            $data['isActive'] = $isActive;
            $data['isDefaultlBank'] = $isDefaultlBank;
        }
        $data['masterAutoID']                       = trim($this->input->post('masterAccount') ?? '');
        $data['GLSecondaryCode']                    = trim($this->input->post('GLSecondaryCode') ?? '');
        $data['GLDescription']                      = trim($this->input->post('GLDescription') ?? '');
        if ($data['masterAccountYN'] == 1)
        {
            $data['masterAccount']                  = '';
            $data['masterAccountDescription']       = '';
        }
        else
        {
            $master_account                         = explode('|', trim($this->input->post('masterAccount_dec') ?? ''));
            $data['masterAccount']                  = trim($master_account[0] ?? '');
            $data['masterAccountDescription']       = trim($master_account[2] ?? '');
        }
        $data['modifiedPCID']                       = $this->common_data['current_pc'];
        $data['modifiedUserID']                       = $this->common_data['current_userID'];
        $data['modifiedUserName']                   = $this->common_data['current_user'];
        $data['modifiedDateTime']                   = $this->common_data['current_date'];
        if (trim($this->input->post('GLAutoID') ?? ''))
        {
            $this->db->where('GLAutoID', trim($this->input->post('GLAutoID') ?? ''));
            $this->db->update('srp_erp_chartofaccounts', $data);
            if ($controlAccountUpdate == 1)
            { /*conreol account = 1 update srp_erp_companycontrolaccounts */
                $this->db->update('srp_erp_companycontrolaccounts', array(
                    'GLSecondaryCode' => $data['GLSecondaryCode'],
                    'GLDescription' => $data['GLDescription']
                ), array('GLAutoID' => trim($this->input->post('GLAutoID') ?? '')));
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->session->set_flashdata('e', 'Ledger : ' . $data['GLDescription'] . ' Update Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            }
            else
            {
                $this->session->set_flashdata('s', 'Ledger : ' . $data['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('GLAutoID'));
            }
        }
        else
        {
            $this->load->library('sequence');
            $this->load->library('Approvals');
            $data['isActive']                       = 1;
            $data['companyID']                      = $this->common_data['company_data']['company_id'];
            $data['companyCode']                    = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup']               = $this->common_data['user_group'];
            $data['createdPCID']                    = $this->common_data['current_pc'];
            $data['createdUserID']                  = $this->common_data['current_userID'];
            $data['createdUserName']                = $this->common_data['current_user'];
            $data['createdDateTime']                = $this->common_data['current_date'];
            $data['systemAccountCode']              = $this->sequence->sequence_generator($data['subCategory']);
            $data['approvedYN']                     = 1;
            $data['approvedbyEmpID']                = $this->common_data['current_userID'];
            $data['approvedbyEmpName']              = $this->common_data['current_user'];
            $data['approvedDate']                   = $this->common_data['current_date'];
            $data['approvedComment']                = 'Auto approved';
            $data['confirmedYN']                    = 1;
            $data['confirmedDate']                  = $this->common_data['current_date'];
            $data['confirmedbyEmpID']               = $this->common_data['current_userID'];
            $data['confirmedbyName']                = $this->common_data['current_user'];
            $this->db->insert('srp_erp_chartofaccounts', $data);
            $last_id = $this->db->insert_id();
            //$status = $this->approvals->CreateApproval('GL',$last_id,$data['systemAccountCode'],'Chart Of Accont','srp_erp_chartofaccounts','GLAutoID',1);
            // if ($status==1) {
            //     $data['approvedYN']             = 1;
            //     $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            //     $data['approvedbyEmpName']      = $this->common_data['current_user'];
            //     $data['approvedDate']           = $this->common_data['current_date'];
            //     $data['approvedComment']        = 'Auto approved';
            //     $this->db->where('GLAutoID', $last_id);
            //     $this->db->update('srp_erp_chartofaccounts', $data);
            // }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->session->set_flashdata('e', 'Ledger  : ' . $data['GLDescription'] . ' Save Failed ');
                $this->db->trans_rollback();
                return array('status' => false);
            }
            else
            {
                $this->session->set_flashdata('w', '');
                $this->session->set_flashdata('s', 'Ledger : ' . $data['GLDescription'] . ' Added Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    /**SMSD  */
    function save_signature_authority()
    {
        $this->db->trans_start();
        $employeeID = $this->input->post('employee');
        $GLAutoID = trim($this->input->post('GLAutoID') ?? '');
        $companyID = current_companyID();

        /*$this->db->select('GLAutoID');
        $this->db->from('srp_erp_chartofaccounts');
        $this->db->where('companyID', $companyID);
        $this->db->where('GLSecondaryCode', $glsecndcode);
        $gl = $this->db->get()->row_array();*/

        // print_r($gl['GLAutoID']); exit;

        $this->db->select('id');
        $this->db->from('srp_erp_chartofaccount_signatures');
        $this->db->where('companyID', $companyID);
        $this->db->where('empID', $employeeID);
        $isexist = $this->db->get()->row_array();


        if (!empty($isexist))
        {
            $this->session->set_flashdata('w', 'Authorized Employee already exists');
            return array('w', 'Authorized Employee already exists');
        }
        else
        {
            $data['glAutoID'] = $GLAutoID  /* $gl['GLAutoID']*/;
            $data['empID']    = $employeeID;
            $data['companyID']    = $this->common_data['company_data']['company_id'];
            $data['companyCode']  = $this->common_data['company_data']['company_code'];

            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID']      = $this->common_data['current_pc'];
            $data['createdUserID']    = $this->common_data['current_userID'];
            $data['createdUserName']  = $this->common_data['current_user'];
            $data['createdDateTime']  = $this->common_data['current_date'];
            $data['timestamp']        = $this->common_data['current_date'];

            $this->db->insert('srp_erp_chartofaccount_signatures', $data);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                $this->session->set_flashdata('e', 'Authorized Employee Save Failed');
                $this->db->trans_rollback();
                return array('e', 'Authorized Employee Save Failed');
            }
            else
            {
                $this->session->set_flashdata('w', '');
                $this->session->set_flashdata('s', 'Authorized Employee Added Successfully');
                $this->db->trans_commit();
                return array('s', 'successfully completed');
            }
        }
    }

    /**SMSD  */
    function fetch_signature_authority()
    {
        $companyID = current_companyID();

        $this->db->select('empID');
        $this->db->where('deletedYN', 0);
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_chartofaccount_signatures');
        $emp = $this->db->get()->result_array();

        $data = array();

        foreach ($emp as $row)
        {

            $this->db->select('EIdNo,ECode,Ename2');
            $this->db->where('EIdNo', $row['empID']);
            $this->db->where('Erp_companyID', $companyID);
            $this->db->from('srp_employeesdetails');
            $result = $this->db->get()->row_array();

            if (!empty($result))
            {
                $data[] = $result;
            }
        }
        // print_r($data);exit;

        return $data;
    }
    /**SMSD  */
    function delete_author()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $employeeID = $this->input->post('EIdNo');

        $data['deletedYN'] = 1;
        $data['deleteByEmpID'] = $this->common_data['current_userID'];
        $data['deletedDatetime'] = $this->common_data['current_date'];

        $this->db->where('empID', $employeeID);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_chartofaccount_signatures', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE)
        {
            $this->session->set_flashdata('s', 'Authorized Employee Deleted Successfully');
            return true;
        }
        else
        {
            $this->session->set_flashdata('e', 'Failed to delete Authorized Employee');
        }

        /*  $this->db->where('EIdNo', $employeeID);
            $this->db->where('companyID', $companyID);
            $result= $this->db->delete('srp_erp_chartofaccount_signatures');
            $this->session->set_flashdata('s', 'Record Deleted Successfully');
            return true;*/
    }


    function load_chart_of_accont_header()
    {
        $this->db->select('*');
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        return $this->db->get('srp_erp_chartofaccounts')->row_array();
    }

    function fetch_master_account()
    {
        $this->db->select('GLSecondaryCode,GLDescription,systemAccountCode,GLSecondaryCode,GLAutoID');
        $this->db->where('accountCategoryTypeID', trim($this->input->post('accountCategoryTypeID') ?? ''));
        $this->db->where('subCategory', trim($this->input->post('subCategory') ?? ''));
        $this->db->where('masterAccountYN', 1);
        $this->db->where('deletedYN', 0);
        $this->db->where('GLAutoID<>', trim($this->input->post('GLAutoID') ?? ''));
        //$this->db->where('(masterAccountYN = 1 or  controllAccountYN = 1)');
        $this->db->where('companyId', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_chartofaccounts')->result_array();
    }

    function delete_chart_of_accont()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $GLAutoID = $this->input->post('GLAutoID');
        $coaintegration = $this->db->query("SELECT 	GLAutoID FROM srp_erp_chartofaccounts 
	        JOIN ( SELECT GLAutoID AS existGLAutoID FROM checkcoaerpeducalintegration GROUP BY GLAutoID ) 
	            glExist ON glExist.existGLAutoID = srp_erp_chartofaccounts.GLAutoID 
            WHERE companyID = {$companyID} AND GLAutoID = {$GLAutoID} AND deletedYN = 0")->result_array();
        $subAccountExist = $this->db->query("SELECT GLAutoID FROM srp_erp_chartofaccounts WHERE companyID = {$companyID} AND masterAutoID = {$GLAutoID} AND deletedYN = 0")->row_array();
        // print_r($subAccountExist); exit();
        if ($coaintegration)
        {
            $this->session->set_flashdata('w', 'You cannot delete this account. This account has been linked with transactions.');
        }
        else if ($subAccountExist)
        {
            $this->session->set_flashdata('w', 'Delete Sub Accounts Before Deleting this Chart of Account');
        }
        else
        {
            $data['deletedYN'] = 1;
            $data['isActive'] = 0;
            $data['deleteByEmpID'] = $this->common_data['current_userID'];
            $data['deletedDatetime'] = $this->common_data['current_date'];

            $this->db->where('GLAutoID', $GLAutoID);
            $this->db->update('srp_erp_chartofaccounts', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === TRUE)
            {
                $this->session->set_flashdata('s', 'Chart of Account Deleted Successfully');
                return true;
            }
            else
            {
                $this->session->set_flashdata('e', 'Failed to delete Chart of Account');
            }
        }
        /*  $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        $result= $this->db->delete('srp_erp_chartofaccounts');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;*/
    }

    function fetch_cheque_number()
    {
        $PvID = $this->input->post('PvID');
        $bankTransferAutoID = $this->input->post('bankTransferAutoID');
        $comapnyID = current_companyID();
        $GLAutoID = $this->input->post('GLAutoID');
        $documentID_filter = '';
        if (($PvID != '') && !empty($PvID))
        {
            $documentID_filter = " or ( documentID='PV' AND documentMasterAutoID=$PvID ) ";
        }
        if (($bankTransferAutoID != '') && !empty($bankTransferAutoID))
        {
            $documentID_filter = " or ( documentID='BT' AND documentMasterAutoID=$bankTransferAutoID ) ";
        }
        $this->db->select('bankCheckNumber,isCash');
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        $master = $this->db->get('srp_erp_chartofaccounts')->row_array();

        /* $this->db->SELECT("chequeRegisterDetailID,chequeNo,srp_erp_chequeregister.description");
        $this->db->join('srp_erp_chequeregister', 'srp_erp_chequeregister.chequeRegisterID = srp_erp_chequeregisterdetails.chequeRegisterID','left');
        $this->db->where('srp_erp_chequeregisterdetails.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('srp_erp_chequeregisterdetails.status !=', 2);
        $this->db->where('srp_erp_chequeregister.bankGLAutoID ', $this->input->post('GLAutoID'));
        $this->db->FROM('srp_erp_chequeregisterdetails');*/
        $detail = $this->db->query("SELECT chequeRegisterDetailID,chequeNo,srp_erp_chequeregister.description FROM `srp_erp_chequeregisterdetails`
                                         LEFT JOIN srp_erp_chequeregister ON srp_erp_chequeregister.chequeRegisterID = srp_erp_chequeregisterdetails.chequeRegisterID
                                         WHERE srp_erp_chequeregisterdetails.companyID =$comapnyID
                                         AND (srp_erp_chequeregisterdetails.status != 2 AND (srp_erp_chequeregisterdetails.STATUS != 1 $documentID_filter) )
                                         AND srp_erp_chequeregister.bankGLAutoID =$GLAutoID ")->result_array();

        $data['master'] = $master;
        $data['detail'] = $detail;

        return $data;
    }

    function reOpen_chart_of_accont()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $GLAutoID = $this->input->post('GLAutoID');
        $ActiveMasterAccount = $this->db->query("SELECT mast.deletedYN, mast.GLAutoID FROM srp_erp_chartofaccounts det
                                                        INNER JOIN srp_erp_chartofaccounts mast ON mast.GLAutoID = det.masterAutoID 
                                                        WHERE det.companyID = {$companyID} AND det.GLAutoID = {$GLAutoID} AND mast.deletedYN = 1")->row_array();
        if ($ActiveMasterAccount)
        {
            $this->session->set_flashdata('w', 'Re open master Accounts Before Re opening this Chart of Account');
        }
        else
        {
            $data['deletedYN'] = 0;
            $data['isActive'] = 1;
            $data['deleteByEmpID'] = Null;
            $data['deletedDatetime'] = Null;

            $this->db->where('GLAutoID', $GLAutoID);
            $this->db->update('srp_erp_chartofaccounts', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === TRUE)
            {
                $this->session->set_flashdata('s', 'Chart of Account Re Opened Successfully');
                return true;
            }
            else
            {
                $this->session->set_flashdata('e', 'Failed to Re Open Chart of Account');
            }
        }
    }

    /* Function added */
    function export_excel_chartofaccounts_master()
    {

        $companyid = $this->common_data['company_data']['company_id'];
        $result = $this->db->query("SELECT
        srp_erp_chartofaccounts.GLAutoID,
        srp_erp_chartofaccounts.systemAccountCode,
        srp_erp_chartofaccounts.GLSecondaryCode,
        srp_erp_chartofaccounts.GLDescription,
        srp_erp_chartofaccounts.CategoryTypeDescription as categorytype,
        srp_erp_chartofaccounts.subCategory, 
        srp_erp_chartofaccounts.controllAccountYN,
        srp_erp_chartofaccounts.isBank,
        srp_erp_chartofaccounts.bankName,
        srp_erp_chartofaccounts.bankBranch,
        srp_erp_chartofaccounts.bankShortCode,
        srp_erp_chartofaccounts.bankSwiftCode,
        srp_erp_chartofaccounts.bankCurrencyCode,
          masteraccounts.*,
          companyReportingAmount,
            companyReportingCurrencyDecimalPlaces,
        IF (
            glExist.existGLAutoID IS NOT NULL,
            1,
            0
        ) AS dataExist
        FROM
            srp_erp_chartofaccounts
        LEFT JOIN (
            SELECT
                GLAutoID as masterGLAutoID,
            systemAccountCode as masterSystemAccountCode,
            GLSecondaryCode as masterGLsecondaryCode,
            GLDescription AS masterGLDescription
            FROM
                srp_erp_chartofaccounts
          where masterAccountYN=1
        ) masteraccounts on srp_erp_chartofaccounts.masterAutoID=masteraccounts.masterGLAutoID
        LEFT JOIN (
            SELECT
                SUM(companyReportingAmount) AS companyReportingAmount,
                GLAutoID,
                companyReportingCurrencyDecimalPlaces
            FROM
                srp_erp_generalledger
            WHERE
                companyID = $companyid
            GROUP BY
                srp_erp_generalledger.GLAutoID
        ) gl ON (
            gl.GLAutoID = srp_erp_chartofaccounts.GLAutoID
        )
        LEFT JOIN (
            SELECT
                GLAutoID AS existGLAutoID
            FROM
                checkchartofaccountgl
            WHERE
                companyID = $companyid
            GROUP BY
                GLAutoID
        ) glExist ON glExist.existGLAutoID = srp_erp_chartofaccounts.GLAutoID
        WHERE
            srp_erp_chartofaccounts.companyID = $companyid
        AND srp_erp_chartofaccounts.masterAccountYN=0
        AND srp_erp_chartofaccounts.deletedYN = 0
        GROUP BY
            srp_erp_chartofaccounts.GLAutoID
        ")->result_array();

        $data = array();
        $a = 1;
        foreach ($result as $row)
        {
            $data[] = array(
                'Num' => $a,
                'systemAccountCode' => $row['systemAccountCode'],
                'GLSecondaryCode' => $row['GLSecondaryCode'],
                'GLDescription' => $row['GLDescription'],
                'categorytype' => $row['categorytype'],
                'subCategory' => $row['subCategory'],
                'bankCurrencyCode' => $row['bankCurrencyCode'],
                'masterSystemAccountCode' => $row['masterSystemAccountCode'],
                'masterGLsecondaryCode' => $row['masterGLsecondaryCode'],
                'masterGLDescription' => $row['masterGLDescription'],
                'controllAccountYN' => $row['controllAccountYN'],
                'isBank' => $row['isBank'],
                'bankName' => $row['bankName'],
                'bankBranch' => $row['bankBranch'],
                'bankShortCode' => $row['bankShortCode'],
                'bankSwiftCode' => $row['bankSwiftCode'],
                'companyReportingAmount' => number_format($row['companyReportingAmount'], $row['companyReportingCurrencyDecimalPlaces']),
                'dataExist' => $row['dataExist'],

            );
            $a++;
        }

        return ['chartofaccounts' => $data];
    }
    /* End  Function */
}
