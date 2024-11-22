<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Template_paySheet_model extends ERP_Model
{
    function createTemplate()
    {
        $this->form_validation->set_rules('templateName', 'Template Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        } else {
            $tName = trim($this->input->post('templateName') ?? '');
            $payrollType = trim($this->input->post('payrollType') ?? '');
            $isTemplateNameAlreadyExist = $this->isTemplateNameAlreadyExist($tName);

            /*print_r($isTemplateNameAlreadyExist);
            die();*/
            if (is_array($isTemplateNameAlreadyExist) && count($isTemplateNameAlreadyExist) > 0) {
                return array('e', 'This Template Name is already exists');
            } else {
                //Get template no
                $query = $this->db->select('serialNo')->from('srp_erp_pay_template')->where('companyID', current_companyID())
                    ->order_by('templateID', 'desc')->get();
                $lastTempArray = $query->row_array();
                $lastTempNo = $lastTempArray['serialNo'] ?? '';
                $lastTempNo = ($lastTempNo == null) ? 1 : $lastTempNo + 1;

                //Generate template Code
                $this->load->library('sequence');
                $tCode = $this->sequence->sequence_generator('PAYT', $lastTempNo);


                $isConform = $this->input->post('isConform');

                $data = array(
                    'documentCode' => $tCode,
                    'serialNo' => $lastTempNo,
                    'templateDescription' => $tName,
                    'isNonPayroll' => $payrollType,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                    'createdPCID' => $this->common_data['current_pc'],
                    'createdUserGroup' => $this->common_data['user_group'],
                    'createdUserID' => $this->common_data['current_userID'],
                    'createdUserName' => $this->common_data['current_user'],
                    'createdDateTime' => $this->common_data['current_date']
                );

                if ($isConform == 1) {
                    $data['confirmedYN'] = 1;
                    $data['confirmedByEmpID'] = $this->common_data['current_userID'];
                    $data['confirmedByName'] = $this->common_data['current_user'];
                    $data['confirmedDate'] = $this->common_data['current_date'];
                }

                $this->db->insert('srp_erp_pay_template', $data);

                if ($this->db->affected_rows() > 0) {
                    $insertID = $this->db->insert_id();
                    return array('s', $tCode . ' Created Successfully', $insertID);
                } else {
                    return array('e', 'Error insert process');
                }
            }
        }
    }

    function cloneTemplate()
    {

        $this->form_validation->set_rules('clone_templateName', 'Template Name', 'trim|required');
        $this->form_validation->set_rules('original-templateNameID', 'Original Template', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        } else {
            $tName = trim($this->input->post('clone_templateName') ?? '');
            $cloneID = trim($this->input->post('original-templateNameID') ?? '');
            $payrollType = trim($this->input->post('clone_templateType') ?? '');
            $isTemplateNameAlreadyExist = $this->isTemplateNameAlreadyExist($tName);

            /*print_r($isTemplateNameAlreadyExist);
            die();*/
            if (count($isTemplateNameAlreadyExist) > 0) {
                return array('e', 'This Template Name is already exists');
            } else {
                //Get template no
                $query = $this->db->select('serialNo')->from('srp_erp_pay_template')->where('companyID', current_companyID())
                    ->order_by('templateID', 'desc')->get();
                $lastTempArray = $query->row_array();
                $lastTempNo = $lastTempArray['serialNo'];
                $lastTempNo = ($lastTempNo == null) ? 1 : $lastTempNo + 1;

                //Generate template Code
                $this->load->library('sequence');
                $tCode = $this->sequence->sequence_generator('PAYT', $lastTempNo);

                $companyID = current_companyID();
                $companyCode = current_companyCode();
                $createdPCID = current_pc();
                $createdUserID = current_userID();
                $createdUserGroup = current_user_group();
                $createdUserName = current_employee();
                $createdDateTime = $this->common_data['current_date'];


                $data = array(
                    'documentCode' => $tCode,
                    'serialNo' => $lastTempNo,
                    'templateDescription' => $tName,
                    'isNonPayroll' => $payrollType,
                    'companyID' => $companyID,
                    'companyCode' => $companyCode,
                    'createdPCID' => $createdPCID,
                    'createdUserGroup' => $createdUserGroup,
                    'createdUserID' => $createdUserID,
                    'createdUserName' => $createdUserName,
                    'createdDateTime' => $createdDateTime
                );

                $this->db->trans_start();

                $this->db->insert('srp_erp_pay_template', $data);
                $insertID = $this->db->insert_id();

                $this->db->query("INSERT INTO srp_erp_pay_templatedetail ( templateID, tempFieldID, detailType, columnName, captionName,
                                  salaryCatID, sortOrder, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime,
                                  createdUserName)

                                  SELECT {$insertID}, tempFieldID, detailType, columnName, captionName, salaryCatID, sortOrder, {$companyID},
                                  '{$companyCode}', '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}', '{$createdUserName}'
                                  FROM srp_erp_pay_templatedetail WHERE templateID={$cloneID} AND companyID={$companyID} ");


                $this->db->trans_complete();

                if ($this->db->trans_status() === true) {
                    $this->db->trans_commit();
                    return array('s', $tCode . ' Clone Created Successfully', $insertID);
                } else {
                    $this->db->trans_rollback();
                    return array('e', 'Error clone created process');
                }
            }
        }
    }

    function isTemplateNameAlreadyExist($tName)
    {
        $query = $this->db->select('templateID')->from('srp_erp_pay_template')
            ->where('templateDescription', $tName)
            ->where('companyID', $this->common_data['company_data']['company_id'])->get();
        return $query->row_array();
    }

    function templateStatus($id)
    {
        $query = $this->db->select('documentCode AS code, isDefault, confirmedYN, isNonPayroll')->from('srp_erp_pay_template')->where('templateID', $id)->get();
        return $query->row();
    }

    function referBack()
    {
        $id = $this->input->post('referID');
        $details = $this->templateStatus($id);
        $companyID = current_companyID();

        /****  srp_erp_companypolicymaster tables 'companypolicymasterID' column value ***/
        $companyPolicyMasterID = ($details->isNonPayroll != 'Y') ? 5 : 6;

        $this->db->trans_start();
        $defaultTemplateInPolicy = $this->db->query("SELECT `value` FROM srp_erp_companypolicy WHERE companyID={$companyID}
                                                   AND companypolicymasterID={$companyPolicyMasterID}")->row('value');

        if ($defaultTemplateInPolicy == $id) {
            $this->db->query("UPDATE srp_erp_companypolicy SET `value`=0 WHERE companyID={$companyID} AND companypolicymasterID={$companyPolicyMasterID}");
        }

        $updateDetail = array(
            'confirmedYN' => 2,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );


        $this->db->where('templateID', $id)->update('srp_erp_pay_template', $updateDetail);
        $deleteWhere_arr = array(
            'companypolicymasterID' => $companyPolicyMasterID,
            'systemValue' => $id,
            'companyID' => $companyID
        );
        $this->db->where($deleteWhere_arr)->delete('srp_erp_companypolicymaster_value');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', '[' . $details->code . '] Refer backed successfully');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in refer back process [' . $details->code . ']');
        }

        /*if ($details->isDefault == 1) {
            return array('e', $details->code . ' is the default template,</br>Please change another template as default and try again');
        } else {

        }*/
    }

    function statusChangePaysheetTemplate()
    {
        $templateID = $this->input->post('hidden-id');
        $payrollType = $this->input->post('payrollType');
        $details = $this->templateStatus($templateID);

        if ($details->confirmedYN == 1) {

            $this->db->where('companyID', current_companyID())->where('isNonPayroll', $payrollType)
                ->update('srp_erp_pay_template', array('isDefault' => 0));
            $this->db->where('templateID', $templateID)->update('srp_erp_pay_template', array('isDefault' => 1));
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Status changed successfully');
            } else {
                return array('e', 'Error in status change process');
            }
        } else {
            return array('e', '[ ' . $details->code . ' ] is not confirmed yet');
        }
    }

    function deleteTemplate()
    {
        $id = $this->input->post('referID');
        $details = $this->templateStatus($id);

        if ($details->confirmedYN == 1) {
            return array('e', $details->code . ' is confirmed, you can not delete this');
        } else {

            $this->db->where('templateID', $id)->delete('srp_erp_pay_template');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Deleted successfully');
            } else {
                return array('e', 'Error in delete process [' . $details->code . ']');
            }
        }
    }

    function templateHeaderDetails($templateID = null)
    {
        $templateID = ($templateID == null) ? $this->input->post('templateId') : $templateID;
        $query = $this->db->select('templateID, documentCode, templateDescription, confirmedYN, isNonPayroll')
            ->from('srp_erp_pay_template')->where('templateID', $templateID)->get();
        return $query->row_array();
    }

    function templateDetails($pra1 = 0, $without_last_month='N')
    {
        $templateID = ($this->input->post('templateId') != null) ? $this->input->post('templateId') : $pra1;
        $this->db->select('tempFieldID, columnName, captionName, detailType, IF( sortOrder IS NULL, 0, sortOrder) AS sortOrder, det.salaryCatID AS catID,
                fields.payGroupID AS payID, fieldName, isCalculate,
                CASE detailType
                        WHEN \'A\' THEN CONCAT(\'AD_\', `fields`.salaryCatID)
                        WHEN \'D\' THEN CONCAT(\'AD_\', `fields`.salaryCatID)
                        WHEN \'G\' THEN CONCAT(detailType ,\'_\', `fields`.payGroupID)
                        ELSE NULL
                END AS groupKey
                ')
            ->from('srp_erp_pay_templatedetail AS det')
            ->join('srp_erp_pay_templatefields AS fields', 'fields.id = det.tempFieldID')
            ->where('templateID', $templateID);

            if ($without_last_month == 'Y') {
                $this->db->where("fields.fieldName <> 'lastMonthNetPay'");
            }
            
        $query =  $this->db->order_by('sortOrder', 'ASC')->get();

        return $query->result_array();
    }

    function templateCalculationColumns($pra1 = 0)
    {
        $companyID = current_companyID();
        $templateID = ($this->input->post('templateId') != null) ? $this->input->post('templateId') : $pra1;
        $query = $this->db->query("SELECT tempFieldID,
                                        CASE detailType
                                            WHEN 'A' THEN CONCAT('AD_', `fields`.salaryCatID)
                                            WHEN 'D' THEN CONCAT('AD_', `fields`.salaryCatID)
                                            WHEN 'G' THEN CONCAT(detailType ,'_', `fields`.payGroupID)
                                            ELSE NULL
                                      END AS headerName
                                    FROM srp_erp_pay_templatedetail AS det
                                    JOIN srp_erp_pay_templatefields AS `fields` ON `fields`.id = det.tempFieldID
                                    WHERE templateID = {$templateID}  AND det.companyID = {$companyID} AND detailType != 'H'
                                    ORDER BY sortOrder ASC")->result_array();

        return $query;
    }

    function templateDetailsGroupBY($pra1 = 0)
    {
        $templateID = ($this->input->post('templateId') != null) ? $this->input->post('templateId') : $pra1;
        $header = $this->db->query("select tempFieldID, columnName, captionName, detailType, IF( sortOrder IS NULL, 0, sortOrder) AS sortOrder,
                                    det.salaryCatID AS catID, fieldName
                                    FROM srp_erp_pay_templatedetail AS det
                                    JOIN srp_erp_pay_templatefields AS fields ON fields.id = det.tempFieldID
                                    WHERE templateID={$templateID}  AND detailType='H' ORDER BY sortOrder ASC ")->result_array();

        $addition = $this->db->query("select tempFieldID, columnName, captionName, detailType, IF( sortOrder IS NULL, 0, sortOrder) AS sortOrder,
                                    det.salaryCatID AS catID, fieldName
                                    FROM srp_erp_pay_templatedetail AS det
                                    JOIN srp_erp_pay_templatefields AS fields ON  fields.id = det.tempFieldID
                                    WHERE templateID={$templateID}  AND detailType='A' ORDER BY sortOrder ASC ")->result_array();

        $deduction = $this->db->query("select tempFieldID, columnName, captionName, detailType, IF( sortOrder IS NULL, 0, sortOrder) AS sortOrder,
                                    det.salaryCatID AS catID, fieldName
                                    FROM srp_erp_pay_templatedetail AS det
                                    JOIN srp_erp_pay_templatefields AS fields ON fields.id = det.tempFieldID
                                    WHERE templateID={$templateID}  AND detailType='D'  ORDER BY sortOrder ASC ")->result_array();

        return array(
            'H' => $header,
            'A' => $addition,
            'D' => $deduction
        );
    }

    function templateFields()
    {
        $fieldType = $this->input->post('fieldType');
        $templateID = $this->input->post('templateID');
        $companyID = $this->common_data['company_data']['company_id'];

        if ($fieldType == 'H') {
            $query = $this->db->query("SELECT fieldName, caption, captionName, id, IF( det.tempFieldID = temp.id, 'Y', 'N') AS checked, temp.salaryCatID,
                                       IF( sortOrder IS NULL, 100, sortOrder ) AS orderAsc
                                       FROM srp_erp_pay_templatefields AS temp
                                       LEFT JOIN srp_erp_pay_templatedetail AS det ON det.tempFieldID = temp.id  AND det.templateID = '$templateID'
                                       AND det.companyID = '$companyID'
                                       WHERE temp.fieldType = 'H' ORDER BY orderAsc");
        } else if ($fieldType == 'G') {
            $query = $this->db->query("SELECT fieldName, caption, captionName, id, IF( det.tempFieldID = temp.id, 'Y', 'N') AS checked, temp.salaryCatID,
                                       IF( sortOrder IS NULL, 100, sortOrder ) AS orderAsc, temp.payGroupID
                                       FROM srp_erp_pay_templatefields AS temp
                                       LEFT JOIN srp_erp_pay_templatedetail AS det ON det.tempFieldID = temp.id  AND det.templateID = '$templateID'
                                       WHERE temp.fieldType = '$fieldType' AND temp.companyID = '$companyID'
                                       ORDER BY orderAsc");
        } else {
            $query = $this->db->query("SELECT fieldName, caption, captionName, id, IF( det.tempFieldID = temp.id, 'Y', 'N') AS checked, temp.salaryCatID,
                                       IF( sortOrder IS NULL, 100, sortOrder ) AS orderAsc
                                       FROM srp_erp_pay_templatefields AS temp
                                       LEFT JOIN srp_erp_pay_templatedetail AS det ON det.tempFieldID = temp.id  AND det.templateID = '$templateID'
                                       WHERE temp.fieldType = '$fieldType' AND temp.companyID = '$companyID'
                                        UNION
                                            SELECT
                                            fieldName, caption, captionName, id, IF ( det2.tempFieldID = fixedTemp.id, 'Y', 'N' ) AS checked,
                                            '', IF ( sortOrder IS NULL, 100, sortOrder ) AS orderAsc
                                            FROM  srp_erp_pay_templatefields AS fixedTemp
                                            LEFT JOIN srp_erp_pay_templatedetail AS det2 ON det2.tempFieldID = fixedTemp.id AND det2.templateID = '$templateID'
                                            WHERE fixedTemp.isCalculate = 1 AND fixedTemp.fieldType='$fieldType'
                                       ORDER BY orderAsc");
        }
        //echo $this->db->last_query();
        return $query->result_array();
    }

    function templateDetailsSave()
    {
        $this->form_validation->set_rules('tName', 'Template Name', 'trim|required');
        $this->form_validation->set_rules('templateID', 'Template ', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        } else {
            $templateID = trim($this->input->post('templateID') ?? '');
            $masterDetails = $this->templateHeaderDetails($templateID);

            if ($masterDetails['confirmedYN'] == 1) {
                return ['e', 'This template is already confirmed</br>You can not make changes on this.'];
                die();
            }

            $tName = trim($this->input->post('tName') ?? '');
            $isTemplateNameAlreadyExist = $this->isTemplateNameAlreadyExist($tName);
            $error = 0;

            if (count($isTemplateNameAlreadyExist) == 0) {
                $error = 0;
            } else {
                if ($isTemplateNameAlreadyExist['templateID'] != $templateID) {
                    $error = 0;
                    return array('e', 'This Template Name is already exists');
                }
            }


            if ($error == 0) {
                $tCode = $this->input->post('tCode');
                $postID = $this->input->post('postID');
                $postDetailColumn = $this->input->post('postDetailColumn');
                $postSortOrder = $this->input->post('postSortOrder');
                $postDetailCaption = $this->input->post('postDetailCaption');
                $postType = $this->input->post('postType');
                $postCatID = $this->input->post('postCatID');
                $isConform = $this->input->post('isConform');
                $data = array();
                $j = 0;

                $this->db->trans_start();

                $updateArray = array(
                    'templateDescription' => $tName,
                    'modifiedPCID' => $this->common_data['current_pc'],
                    'modifiedUserID' => $this->common_data['current_userID'],
                    'modifiedUserName' => $this->common_data['current_user'],
                    'modifiedDateTime' => $this->common_data['current_date']
                );

                if ($isConform == 1) {
                    $updateArray['confirmedYN'] = 1;
                    $updateArray['confirmedByEmpID'] = $this->common_data['current_userID'];
                    $updateArray['confirmedByName'] = $this->common_data['current_user'];
                    $updateArray['confirmedDate'] = $this->common_data['current_date'];

                    /****  srp_erp_companypolicymaster tables 'companypolicymasterID' column value ***/
                    $companyPolicyMasterID = ($masterDetails['isNonPayroll'] != 'Y') ? 5 : 6;

                    $policyMasterData = array(
                        'companypolicymasterID' => $companyPolicyMasterID,
                        'value' => $tName,
                        'systemValue' => $templateID,
                        'companyID' => current_companyID()
                    );

                    $this->db->insert('srp_erp_companypolicymaster_value', $policyMasterData);

                }


                $this->db->where('templateID', $templateID)->update('srp_erp_pay_template', $updateArray);
                $this->db->where('templateID', $templateID)->delete('srp_erp_pay_templatedetail');

                if ($postID != null) {
                    foreach ($postID as $itm) {
                        $data[$j]['templateID'] = $templateID;
                        $data[$j]['detailType'] = $postType[$j];
                        $data[$j]['salaryCatID'] = $postCatID[$j];
                        $data[$j]['tempFieldID'] = $itm;
                        $data[$j]['columnName'] = $postDetailColumn[$j];
                        $data[$j]['captionName'] = $postDetailCaption[$j];
                        $data[$j]['sortOrder'] = $postSortOrder[$j];
                        $data[$j]['companyID'] = $this->common_data['company_data']['company_id'];
                        $data[$j]['companyCode'] = $this->common_data['company_data']['company_code'];
                        $data[$j]['createdPCID'] = $this->common_data['current_pc'];
                        $data[$j]['createdUserID'] = $this->common_data['current_userID'];
                        $data[$j]['createdUserGroup'] = $this->common_data['user_group'];
                        $data[$j]['createdUserName'] = $this->common_data['current_user'];
                        $data[$j]['createdDateTime'] = $this->common_data['current_date'];
                        $j++;
                    }

                    $this->db->insert_batch('srp_erp_pay_templatedetail', $data);
                }

                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Failed to Update [ ' . $tCode . ' ] ');
                } else {
                    $this->db->trans_commit();
                    return array('s', '[ ' . $tCode . ' ] Updated successfully');
                }
            }
        }
    }

    function templateCaptionUpdate()
    {
        $this->form_validation->set_rules('captionName', 'Caption Name', 'trim|required');
        $this->form_validation->set_rules('captionUpdateID', 'Caption ID', 'trim|required');
        $this->form_validation->set_rules('captionUpdateTmpID', 'Template', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        } else {
            $captionName = $this->input->post('captionName');
            $captionUpdateID = $this->input->post('captionUpdateID');
            $captionUpdateTmpID = $this->input->post('captionUpdateTmpID');

            $updateArray = array(
                'captionName' => $captionName,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => $this->common_data['current_date']
            );

            $this->db->where('tempFieldID', $captionUpdateID)
                ->where('templateID', $captionUpdateTmpID)
                ->update('srp_erp_pay_templatedetail', $updateArray);

            //echo $this->db->last_query();
            if ($this->db->affected_rows() > 0) {
                return array('s', '[' . $captionName . '] is updated successfully');
            } else {
                return array('e', 'Error in updated process');
            }
        }
    }

    function templateSortOrderUpdate()
    {

        $this->form_validation->set_rules('sortOrderUpdateTmpID', 'Template ID', 'trim|required');
        $this->form_validation->set_rules('sortOrderID[]', 'Sort order ID', 'trim|required');
        $this->form_validation->set_rules('sortOrder[]', 'Sort order', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        } else {
            $sortOrder = $this->input->post('sortOrder');
            $sortOrderID = $this->input->post('sortOrderID');
            $updateTmpID = $this->input->post('sortOrderUpdateTmpID');
            $updateColumn = $this->input->post('updateColumn');
            $errorCount = 0;

            foreach ($sortOrderID as $key => $orderID) {
                $updateArray = array(
                    'sortOrder' => $sortOrder[$key],
                    'modifiedPCID' => $this->common_data['current_pc'],
                    'modifiedUserID' => $this->common_data['current_userID'],
                    'modifiedUserName' => $this->common_data['current_user'],
                    'modifiedDateTime' => $this->common_data['current_date']
                );


                $this->db->where('tempFieldID', $sortOrderID[$key])->where('templateID', $updateTmpID)
                    ->update('srp_erp_pay_templatedetail', $updateArray);

                if ($this->db->affected_rows() == 0) {
                    $errorCount++;
                }
            }

            if ($errorCount == 0) {
                return array('s', $updateColumn . ' are updated successfully');
            } else {
                return array('e', $updateColumn . ' Error in updated process');
            }
        }
    }

    function getDefault()
    {
        $company_ID = current_companyID();
        $templateID = $this->db->query("SELECT templateID FROM srp_erp_pay_template WHERE companyID = {$company_ID} AND isDefault=1")->row('templateID');

        if (empty($templateID)) {
            $templateID = $this->db->query("SELECT templateID FROM srp_erp_pay_template WHERE companyID = {$company_ID} ")->row('templateID');
        }

        return $templateID;
    }

    function insertFromSalaryProcessTables($payrollMasterID, $payDateMin, $payDateMax, $isNonPayroll)
    {
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = $this->common_data['current_date'];

        $localCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $localCurrency = $this->common_data['company_data']['company_default_currency'];
        $localDPlace = $this->common_data['company_data']['company_default_decimal'];
        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
        $repCurrency = $this->common_data['company_data']['company_reporting_currency'];
        $repCurDPlace = $this->common_data['company_data']['company_reporting_decimal'];


        $insertTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrolldetail' : 'srp_erp_payrolldetail';
        $masterTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollheaderdetails' : 'srp_erp_payrollheaderdetails';
        //$attPayIDColumn = ($isNonPayroll == 'Y') ? 'nonPayrollID' : 'payrollID';


        if ($isNonPayroll == 'N') {
            //add monthly loan deductions
            $loan_salaryCategory = getDefaultPayroll('LO'); //get system default loan deduction details
            if (!empty($loan_salaryCategory)) {
                $defaultLoanID = $loan_salaryCategory['salaryCategoryID'];

                $this->db->query("INSERT INTO srp_erp_payrolldetail ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, salCatID, GLCode,
                          transactionCurrencyID, transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,
                          companyLocalCurrency, companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency,
                          companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdUserGroup, createdPCID,
                          createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)
                          SELECT {$payrollMasterID} AS  payrollMasterID, sched.ID, 'LO', 'SD', sched.empID, 'D', {$defaultLoanID}, GLCode, sched.transactionCurrencyID,
                          sched.transactionCurrency, sched.transactionER,  (sched.transactionAmount*-1), sched.transactionCurrencyDecimalPlaces, sched.companyLocalCurrencyID,
                          sched.companyLocalCurrency, convLocal.conversion,  round( ((sched.transactionAmount*-1) /convLocal.conversion), sched.companyLocalCurrencyDecimalPlaces),
                          sched.companyLocalCurrencyDecimalPlaces, sched.companyReportingCurrencyID, sched.companyReportingCurrency, convRepo.conversion, 
                          round( ((sched.transactionAmount*-1) /convRepo.conversion), sched.companyReportingCurrencyDecimalPlaces), sched.companyReportingCurrencyDecimalPlaces,
                          {$companyID}, '{$companyCode}', '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}', '{$createdUserName}',
                          seg.segmentID, seg.segmentCode
                          FROM srp_erp_pay_emploan AS loan
                          JOIN srp_erp_pay_emploan_schedule AS sched ON loan.ID = sched.loanID AND sched.companyID = {$companyID}
                          JOIN (
                                SELECT EmpID, segmentID, payCurrencyID FROM {$masterTB} WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                          ) AS empTB ON empTB.EmpID=loan.empID
                          JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID
                          JOIN srp_erp_pay_loan_category AS cat ON cat.loanID = loan.loanCatID
                          JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                          AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                          JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                          AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                          WHERE loan.companyID = {$companyID} AND approvedYN = 1 AND isClosed = 0 AND skipedInstallmentID = 0 AND isSetteled != 1 AND
                          scheduleDate >= '$payDateMin' AND  scheduleDate < '$payDateMax' ORDER BY loan.ID ASC");
            }


           
            // add variable pay additions - Salam air OT
            $variablePay = getDefaultPayroll('VP', $isNonPayroll); //get system default over time addition details


            if (!empty($variablePay)) {
                $default_VP_PayID = $variablePay['salaryCategoryID'];
                $GLCode = $variablePay['GLCode'];

                $this->db->query("INSERT INTO {$insertTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, salCatID, GLCode,
                          transactionCurrencyID, transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,
                          companyLocalCurrency, companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency,
                          companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdUserGroup, createdPCID,
                          createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)

                          SELECT {$payrollMasterID}, monthlyAdditionDetailID, 'VP', 'SD', empTB.empID, 'A', {$default_VP_PayID}, {$GLCode}, addDet.transactionCurrencyID,
                          addDet.transactionCurrency, 1 as trER,
                          round(  ( IF(ISNULL(intHRAmount), 0, intHRAmount) + IF(ISNULL(lclLYHRAmount), 0, lclLYHRAmount) + IF(ISNULL(intLyAmount), 0, intLyAmount) +
                          IF(ISNULL(totalblockAmount), 0, totalblockAmount) + IF(ISNULL(fixedAmnt), 0, fixedAmnt)), addDet.transactionCurrencyDecimalPlaces) AS amntVar,
                          addDet.transactionCurrencyDecimalPlaces, addDet.companyLocalCurrencyID, addDet.companyLocalCurrency, convLocal.conversion,
                          round(  (( IF(ISNULL(intHRAmount), 0, intHRAmount) + IF(ISNULL(lclLYHRAmount), 0, lclLYHRAmount) + IF(ISNULL(intLyAmount), 0, intLyAmount) +
                          IF(ISNULL(totalblockAmount), 0, totalblockAmount) + IF(ISNULL(fixedAmnt), 0, fixedAmnt))/ convLocal.conversion),
                          addDet.companyLocalCurrencyDecimalPlaces) AS amntVarLoc, addDet.companyLocalCurrencyDecimalPlaces,
                          addDet.companyReportingCurrencyID, addDet.companyReportingCurrency, convRepo.conversion,
                          round(  (( IF(ISNULL(intHRAmount), 0, intHRAmount) + IF(ISNULL(lclLYHRAmount), 0, lclLYHRAmount) + IF(ISNULL(intLyAmount), 0, intLyAmount) +
                          IF(ISNULL(totalblockAmount), 0, totalblockAmount) + IF(ISNULL(fixedAmnt), 0, fixedAmnt))/ convRepo.conversion),
                          addDet.companyReportingCurrencyDecimalPlaces) AS amntVarLoc, addDet.companyReportingExchangeRate,
                          {$companyID}, '{$companyCode}', '{$createdUserGroup}', '{$createdPCID}', {$createdUserID},  '{$createdDateTime}', '{$createdUserName}',
                          empTB.segmentID, segTB.segmentCode
                          FROM srp_erp_ot_monthlyadditionsmaster AS addMaster
                          JOIN srp_erp_ot_monthlyadditiondetail AS addDet
                          ON addDet.monthlyAdditionsMasterID = addMaster.monthlyAdditionsMasterID AND addDet.companyID={$companyID}
                          JOIN (
                                SELECT EmpID, segmentID, payCurrencyID FROM {$masterTB} WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                          ) AS empTB ON empTB.EmpID=addDet.empID
                          LEFT JOIN(
                                SELECT employeeNo, SUM(transactionAmount) AS fixedAmnt
                                FROM srp_erp_ot_pay_fixedelementdeclration
                                WHERE companyID ={$companyID} AND effectiveDate < '{$payDateMax}' GROUP BY employeeNo
                          ) AS fixedDecTB ON fixedDecTB.employeeNo = addDet.empID
                          JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                          AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                          JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                          AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                          JOIN srp_erp_segment AS segTB ON segTB.segmentID = empTB.segmentID AND segTB.companyID={$companyID}
                          WHERE addMaster.companyID={$companyID} AND dateMA  BETWEEN '{$payDateMin}' AND '{$payDateMax}'");
            }

            // add Expense claim
            $selectedExpClam = $this->input->post('selectedExpClam');
            $expenseClaim = getDefaultPayroll('EC', $isNonPayroll); //get system default expense Claim details
            if (!empty($expenseClaim) && !empty($selectedExpClam)) {
                $defaultExpID = $expenseClaim['salaryCategoryID'];

                $this->db->query("INSERT INTO {$insertTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, salCatID, GLCode,
                          transactionCurrencyID, transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,
                          companyLocalCurrency, companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency,
                          companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdUserGroup, createdPCID,
                          createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)

                          SELECT {$payrollMasterID}, expenseClaimMasterAutoID, 'EC', 'SD', claimedByEmpID, 'A', {$defaultExpID}, glAutoID, empCuID, empCu, 1 AS empER,
                          empAmnt, empDP, localCuID, localCu, localER, localAmnt, localDP, repoCuID, repoCu, reER, repoAmnt, repoDP, {$companyID}, '{$companyCode}',
                          '{$createdUserGroup}', '{$createdPCID}', {$createdUserID},  '{$createdDateTime}', '{$createdUserName}', segmentID, segmentCode
                          FROM (
                              SELECT claimDet.expenseClaimMasterAutoID, claimedByEmpID, glAutoID, empCurrencyID AS empCuID,
                              empCurrency AS empCu, round(empCurrencyAmount, empCurrencyDecimalPlaces) AS empAmnt, empCurrencyDecimalPlaces AS empDP,
                              {$localCurrencyID} AS localCuID, '{$localCurrency}' AS localCu, convLocal.conversion AS localER,
                              round(empCurrencyAmount / convLocal.conversion, {$localDPlace}) AS localAmnt, {$localDPlace} AS localDP, {$repCurrencyID} AS repoCuID,
                              '{$repCurrency}' AS repoCu, convRepo.conversion AS reER, round(empCurrencyAmount / convRepo.conversion, {$repCurDPlace}) AS repoAmnt,
                              {$repCurDPlace} AS repoDP, claimDet.segmentID, segTB.segmentCode, DATE_FORMAT(expenseClaimDate,'%Y-%m-01') AS claimFirstDate
                              FROM  srp_erp_expenseclaimmaster AS claimMaster
                              JOIN srp_erp_expenseclaimdetails AS claimDet ON claimDet.expenseClaimMasterAutoID=claimMaster.expenseClaimMasterAutoID
                              JOIN srp_erp_expenseclaimcategories AS expCat ON expCat.expenseClaimCategoriesAutoID = claimDet.expenseClaimCategoriesAutoID
                              AND expCat.companyID = {$companyID}
                              JOIN (
                                  SELECT EmpID FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID} AND payrollMasterID = {$payrollMasterID}
                              )AS payHead ON payHead.EmpID = claimMaster.claimedByEmpID
                              JOIN srp_erp_segment AS segTB ON segTB.segmentID = claimDet.segmentID AND segTB.companyID={$companyID}
                              JOIN srp_erp_currencymaster AS currencyMaster ON currencyMaster.currencyID = empCurrencyID
                              JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empCurrencyID
                              AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                              JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empCurrencyID
                              AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                              WHERE approvedYN = 1  AND addedToSalary = 0 AND addedForPayment = 0 AND claimDet.expenseClaimMasterAutoID IN ({$selectedExpClam})
                          ) AS dataMaster WHERE claimFirstDate < '{$payDateMax}'
                          ");
            }

            // add Invoice Commission
            $selectedCommission = $this->input->post('selectedCommission');
            $commission = getDefaultPayroll('SC', $isNonPayroll); //get system default Invoice Claim details


            if (!empty($commission) && !empty($selectedCommission)) {
                $defaultExpID = $commission['salaryCategoryID'];

                $this->db->query("INSERT INTO {$insertTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, salCatID, GLCode,
                          transactionCurrencyID, transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,
                          companyLocalCurrency, companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency,
                          companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdUserGroup, createdPCID,
                          createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)

                          SELECT {$payrollMasterID}, commissionAutoID, 'SC', 'SD', empID, 'A', {$defaultExpID}, glAutoID, empCuID, empCu, 1 AS empER,
                          empAmnt, empDP, localCuID, localCu, localER, localAmnt, localDP, repoCuID, repoCu, reER, repoAmnt, repoDP, {$companyID}, '{$companyCode}',
                          '{$createdUserGroup}', '{$createdPCID}', {$createdUserID},  '{$createdDateTime}', '{$createdUserName}', segmentID, segmentCode
                          FROM (
                                SELECT 
                                    det.commissionAutoID,
                                    det.salesPersonEmpID AS empID, 
                                    GLCode as glAutoID, 
                                    srp_erp_customerinvoicemaster.segmentID, 
                                    segTB.segmentCode, 
                                    payCurrencyID AS empCuID,
                                    payCurrency AS empCu, 
                                    round(SUM(commissionAmount/convEmp.conversion), DecimalPlaces) AS empAmnt, 
                                    DecimalPlaces AS empDP,
                                    {$localCurrencyID} AS localCuID, 
                                    '{$localCurrency}' AS localCu, 
                                    convLocal.conversion AS localER,
                                    round(SUM((commissionAmount/convEmp.conversion)/convLocal.conversion), {$localDPlace}) AS localAmnt, 
                                    {$localDPlace} AS localDP, {$repCurrencyID} AS repoCuID,
                                    '{$repCurrency}' AS repoCu, 
                                    convRepo.conversion AS reER, 
                                    round((SUM(commissionAmount/convEmp.conversion)/convRepo.conversion), {$repCurDPlace}) AS repoAmnt,
                                    {$repCurDPlace} AS repoDP, 
                                    DATE_FORMAT(invoiceDate,'%Y-%m-01') AS claimFirstDate
                                FROM  srp_erp_invoice_commission_detail AS det
                                    JOIN srp_erp_invoice_commision mast ON det.commissionAutoID = mast.commissionAutoID
                                    JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = mast.invoiceID
                                    JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = det.salesPersonEmpID
                                JOIN (
                                    SELECT EmpID FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID} AND payrollMasterID = {$payrollMasterID}
                                )AS payHead ON payHead.EmpID = det.salesPersonEmpID
                                JOIN srp_erp_segment AS segTB ON segTB.segmentID = srp_erp_customerinvoicemaster.segmentID AND segTB.companyID={$companyID}
                                LEFT JOIN srp_erp_pay_salarycategories ON srp_erp_pay_salarycategories.payrollCatID = 8 AND srp_erp_pay_salarycategories.companyID = {$companyID}
                                JOIN srp_erp_currencymaster AS currencyMaster ON currencyMaster.currencyID = srp_employeesdetails.payCurrencyID
                                JOIN srp_erp_companycurrencyconversion AS convEmp ON convEmp.masterCurrencyID = {$localCurrencyID}
                                AND convEmp.companyID={$companyID} AND convEmp.subCurrencyID = srp_employeesdetails.payCurrencyID
                                JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = srp_employeesdetails.payCurrencyID
                                AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                                JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = srp_employeesdetails.payCurrencyID
                                AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                                WHERE mast.approvedYN = 1  AND PVAutoID = 0 AND payrollID = 0 AND det.commissionAutoID IN ({$selectedCommission})
                                GROUP BY det.commissionAutoID, det.salesPersonEmpID
                            ) AS dataMaster WHERE claimFirstDate < '{$payDateMax}'
                        ");
            }
        }

        // add no pay deductions    
        $noPayField = ($isNonPayroll == 'Y') ? 'noPaynonPayrollAmount' : 'noPayAmount';
        $payCatID = ($isNonPayroll == 'Y') ? 'nonPayrollSalaryCategoryID' : 'salaryCategoryID';


        // add Balance payment addition
        $BP_salaryCategory = getDefaultPayroll('BP', $isNonPayroll); //get system default balance payment addition details

        if (!empty($BP_salaryCategory)) {
            
            $defaultBPID = $BP_salaryCategory['salaryCategoryID'];
            $isPayrollCategory_val = ($isNonPayroll == 'Y') ? 2 : 1;

            $this->db->query("INSERT INTO {$insertTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, salCatID, GLCode,
                        transactionCurrencyID, transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,
                        companyLocalCurrency, companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency,
                        companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdUserGroup, createdPCID,
                        createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)
                        SELECT {$payrollMasterID}, '', 'BP', 'SD', balancePay.empID, 'A', {$defaultBPID}, GLCode, payCurrencyID, currencyMaster.CurrencyCode,
                        1 AS trER, round(balanceAmount, currencyMaster.DecimalPlaces) AS BPAmount, currencyMaster.DecimalPlaces,
                        {$localCurrencyID}, '{$localCurrency}', convLocal.conversion AS localER, round(balanceAmount / convLocal.conversion, {$localDPlace}), {$localDPlace},
                        {$repCurrencyID}, '{$repCurrency}', convRepo.conversion AS reER, round(balanceAmount / convRepo.conversion, {$repCurDPlace}), {$repCurDPlace},
                        {$companyID}, '{$companyCode}', '{$createdUserGroup}', '{$createdPCID}', {$createdUserID},  '{$createdDateTime}', '{$createdUserName}',
                        segTB.segmentID, segTB.segmentCode
                        FROM srp_erp_pay_balancepayment AS balancePay
                        JOIN {$masterTB} AS empTB ON empTB.EmpID=balancePay.empID AND empTB.companyID={$companyID} AND
                        payrollMasterID={$payrollMasterID}
                        JOIN srp_erp_salarydeclarationmaster AS decMas ON decMas.salarydeclarationMasterID = balancePay.sdMasterID
                        JOIN srp_erp_currencymaster AS currencyMaster ON currencyMaster.currencyID = empTB.payCurrencyID
                        JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                        AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                        JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                        AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                        JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID=balancePay.salaryCatID AND salCat.companyID={$companyID}
                        JOIN srp_erp_segment AS segTB ON segTB.segmentID = empTB.segmentID AND segTB.companyID={$companyID}
                        WHERE balancePay.companyID={$companyID} AND dueDate >= '{$payDateMin}' AND dueDate < '{$payDateMax}' AND decMas.isPayrollCategory = {$isPayrollCategory_val}");
        }

        $absentAsNoPay = getPolicyValues('HANOP','All');
        $attendanceCycleDay = getPolicyValues('HACDAY','All');

        if($absentAsNoPay == 1){

            $last_month = date('Y-m-d',strtotime('-1 month',strtotime($payDateMin)));

            if($attendanceCycleDay == 31){
                $attendanceMin = $payDateMin;
            } else {
                $attendanceMin = substr($last_month, 0, -2).($attendanceCycleDay + 1);
            }
            
            $attendanceMax = substr($payDateMin, 0, -2).($attendanceCycleDay);

            $payDateMin = $attendanceMin;
            $payDateMax = $attendanceMax;
        }

        

         //add monthly additions
         $this->db->query("INSERT INTO {$insertTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, salCatID, detailType, GLCode,
                transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, transactionAmount, companyLocalCurrencyID,
                companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyLocalAmount, companyReportingCurrencyID, companyReportingCurrency,
                companyReportingER, companyReportingCurrencyDecimalPlaces, companyReportingAmount, companyID, companyCode, createdUserGroup, createdPCID,
                createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)
                SELECT {$payrollMasterID} AS  payrollMasterID, monthlyAdditionDetailID, 'MA',
                IF(mnthDet.categoryID = 0 OR mnthDet.categoryID IS NULL, 'MA', 'SD'), mnthDet.empID,
                IF(mnthDet.categoryID = 0 OR mnthDet.categoryID IS NULL, NULL, mnthDet.categoryID),
                'A', GLCode, transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, transactionAmount,
                companyLocalCurrencyID, companyLocalCurrency, convLocal.conversion, companyLocalCurrencyDecimalPlaces, 
                round((transactionAmount/convLocal.conversion) , companyLocalCurrencyDecimalPlaces), companyReportingCurrencyID, companyReportingCurrency, 
                convRepo.conversion, companyReportingCurrencyDecimalPlaces, round((transactionAmount/convRepo.conversion) , companyReportingCurrencyDecimalPlaces),
                {$companyID}, '{$companyCode}', '$createdUserGroup', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}', '{$createdUserName}', seg.segmentID, seg.segmentCode
                FROM srp_erp_pay_monthlyadditionsmaster AS mnthMaster
                JOIN srp_erp_pay_monthlyadditiondetail AS mnthDet ON mnthDet.monthlyAdditionsMasterID = mnthMaster.monthlyAdditionsMasterID
                AND mnthDet.companyID = {$companyID}
                JOIN (
                    SELECT EmpID, segmentID, payCurrencyID FROM {$masterTB} WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                ) AS empTB ON empTB.EmpID=mnthDet.empID
                JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID                           
                JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                WHERE dateMA >= '$payDateMin' AND dateMA <= '$payDateMax' AND confirmedYN = 1 AND mnthMaster.companyID = {$companyID}
                AND isNonPayroll='{$isNonPayroll}' ORDER BY mnthMaster.monthlyAdditionsMasterID ASC");


        //add monthly deductions
        $this->db->query("INSERT INTO {$insertTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, salCatID, detailType, GLCode,
                transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, transactionAmount, companyLocalCurrencyID,
                companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyLocalAmount, companyReportingCurrencyID, companyReportingCurrency,
                companyReportingER, companyReportingCurrencyDecimalPlaces, companyReportingAmount, companyID, companyCode, createdUserGroup, createdPCID,
                createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)
                SELECT {$payrollMasterID} AS  payrollMasterID, monthlyDeductionDetailID, 'MD',
                IF(mnthDet.categoryID = 0 OR mnthDet.categoryID IS NULL, 'MD', 'SD'), mnthDet.empID,
                IF(mnthDet.categoryID = 0 OR mnthDet.categoryID IS NULL, NULL, mnthDet.categoryID),
                'D', GLCode, transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces,
                (transactionAmount *-1), companyLocalCurrencyID, companyLocalCurrency, convLocal.conversion, companyLocalCurrencyDecimalPlaces,
                round((transactionAmount/convLocal.conversion) * -1 , companyLocalCurrencyDecimalPlaces),  companyReportingCurrencyID, companyReportingCurrency, 
                convRepo.conversion, companyReportingCurrencyDecimalPlaces, round((transactionAmount/convRepo.conversion) * -1 , companyReportingCurrencyDecimalPlaces), 
                {$companyID}, '{$companyCode}', '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}', '{$createdUserName}', 
                seg.segmentID, seg.segmentCode
                FROM srp_erp_pay_monthlydeductionmaster AS mnthMaster
                JOIN srp_erp_pay_monthlydeductiondetail AS mnthDet ON mnthDet.monthlyDeductionMasterID = mnthMaster.monthlyDeductionMasterID
                AND mnthDet.companyID = {$companyID}
                JOIN (
                    SELECT EmpID, segmentID, payCurrencyID FROM {$masterTB} WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                ) AS empTB ON empTB.EmpID=mnthDet.empID
                JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID                          
                JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                WHERE dateMD >= '$payDateMin' AND dateMD <= '$payDateMax' AND confirmedYN = 1 AND mnthMaster.companyID = {$companyID}
                AND isNonPayroll='{$isNonPayroll}' ORDER BY mnthMaster.monthlyDeductionMasterID ASC");

         // add over time addition         
        // WHERE attendanceTB.companyID={$companyID} AND {$attPayIDColumn} = 0 AND attendanceDate < '{$payDateMax}' AND paymentOT != 0  
        $this->db->query("INSERT INTO srp_erp_payrolldetail ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, salCatID, GLCode,
                        transactionCurrencyID, transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,
                        companyLocalCurrency, companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency,
                        companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdUserGroup, createdPCID,
                        createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)
                        SELECT {$payrollMasterID}, attendanceTB.ID, 'OT', 'SD', empTB.empID, 'A', catTB.salaryCategoryID, GLCode, payCurrencyID, currencyMaster.CurrencyCode,
                        1 AS trER, round(SUM(paymentOT), currencyMaster.DecimalPlaces) AS OTAmount, currencyMaster.DecimalPlaces, {$localCurrencyID}, '{$localCurrency}',
                        convLocal.conversion AS localER, round(SUM(paymentOT) / convLocal.conversion, {$localDPlace}), {$localDPlace},
                        {$repCurrencyID}, '{$repCurrency}', convRepo.conversion AS reER, round(SUM(paymentOT) / convRepo.conversion, {$repCurDPlace}), {$repCurDPlace},
                        {$companyID}, '{$companyCode}', '{$createdUserGroup}', '{$createdPCID}', {$createdUserID},  '{$createdDateTime}', '{$createdUserName}',
                        empTB.segmentID, segTB.segmentCode
                        FROM srp_erp_pay_empattendancereview AS attendanceTB
                        JOIN (
                            SELECT EmpID, segmentID, payCurrencyID FROM {$masterTB} WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                        ) AS empTB ON empTB.EmpID=attendanceTB.empID
                        JOIN srp_erp_pay_salarycategories AS catTB ON catTB.salaryCategoryID = attendanceTB.salaryCategoryID AND catTB.companyID={$companyID}
                        JOIN srp_erp_currencymaster AS currencyMaster ON currencyMaster.currencyID = empTB.payCurrencyID
                        JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                        AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                        JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                        AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                        JOIN srp_erp_segment AS segTB ON segTB.segmentID = empTB.segmentID AND segTB.companyID={$companyID}                          
                        
                        WHERE attendanceTB.companyID={$companyID} AND attendanceDate >= '{$payDateMin}' AND attendanceDate < '{$payDateMax}' AND paymentOT != 0
                        GROUP BY empTB.empID, catTB.salaryCategoryID");



        //WHERE attendanceTB.companyID={$companyID} AND {$attPayIDColumn} = 0 AND attendanceDate < '{$payDateMax}' AND {$noPayField} != 0
        $this->db->query("INSERT INTO {$insertTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, salCatID, GLCode, transactionCurrencyID,
                          transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID, companyLocalCurrency,
                          companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency,
                          companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdUserGroup, createdPCID,
                          createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)
                          SELECT {$payrollMasterID}, attendanceTB.ID, 'NO-PAY', 'SD', empTB.empID, 'D', catTB.salaryCategoryID, GLCode, payCurrencyID,
                          currencyMaster.CurrencyCode, 1 AS trER, round(({$noPayField}) * -1, currencyMaster.DecimalPlaces) AS NPAmount, currencyMaster.DecimalPlaces,
                          {$localCurrencyID}, '{$localCurrency}', convLocal.conversion AS localER, round(({$noPayField}) *-1 / convLocal.conversion, {$localDPlace}),
                          {$localDPlace}, {$repCurrencyID}, '{$repCurrency}', convRepo.conversion AS reER, round(({$noPayField} * -1) / convRepo.conversion,
                          {$repCurDPlace}), {$repCurDPlace}, {$companyID}, '{$companyCode}', '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}',
                          '{$createdUserName}', empTB.segmentID, segTB.segmentCode
                          FROM srp_erp_pay_empattendancereview AS attendanceTB
                          JOIN (
                                SELECT EmpID, segmentID, payCurrencyID FROM {$masterTB} WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                          ) AS empTB ON empTB.EmpID=attendanceTB.empID
                          JOIN srp_erp_pay_salarycategories AS catTB ON catTB.salaryCategoryID = attendanceTB.{$payCatID} AND catTB.companyID={$companyID}
                          JOIN srp_erp_currencymaster AS currencyMaster ON currencyMaster.currencyID = empTB.payCurrencyID
                          JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                          AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                          JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                          JOIN srp_erp_segment AS segTB ON segTB.segmentID = empTB.segmentID AND segTB.companyID={$companyID}
                          AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                          
                          WHERE attendanceTB.companyID={$companyID} AND attendanceDate >= '{$payDateMin}' AND attendanceDate < '{$payDateMax}' AND {$noPayField} != 0
                          AND (leaveMasterID = 0 OR leaveMasterID IS NULL)
                          "); #GROUP BY empTB.empID, catTB.salaryCategoryID

        /*No-pay from leave */
        $selectedNoPay = $this->input->post('selectedNoPay');

        if (!empty($selectedNoPay)) {
            $this->db->query("INSERT INTO {$insertTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, salCatID, GLCode, transactionCurrencyID,
                          transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID, companyLocalCurrency,
                          companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency,
                          companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdUserGroup, createdPCID,
                          createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)
                          SELECT {$payrollMasterID}, attendanceTB.ID, 'NO-PAY', 'SD', empTB.empID, 'D', catTB.salaryCategoryID, GLCode, payCurrencyID,
                          currencyMaster.CurrencyCode, 1 AS trER, round(({$noPayField}) * -1, currencyMaster.DecimalPlaces) AS NPAmount, currencyMaster.DecimalPlaces,
                          {$localCurrencyID}, '{$localCurrency}', convLocal.conversion AS localER, round(({$noPayField}) *-1 / convLocal.conversion, {$localDPlace}),
                          {$localDPlace}, {$repCurrencyID}, '{$repCurrency}', convRepo.conversion AS reER, round(({$noPayField} * -1) / convRepo.conversion,
                          {$repCurDPlace}), {$repCurDPlace}, {$companyID}, '{$companyCode}', '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}',
                          '{$createdUserName}', empTB.segmentID, segTB.segmentCode
                          FROM srp_erp_pay_empattendancereview AS attendanceTB
                          JOIN (
                                SELECT EmpID, segmentID, payCurrencyID FROM {$masterTB} WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                          ) AS empTB ON empTB.EmpID=attendanceTB.empID
                          JOIN srp_erp_pay_salarycategories AS catTB ON catTB.salaryCategoryID = attendanceTB.{$payCatID} AND catTB.companyID={$companyID}
                          JOIN srp_erp_currencymaster AS currencyMaster ON currencyMaster.currencyID = empTB.payCurrencyID
                          JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                          AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                          JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                          JOIN srp_erp_segment AS segTB ON segTB.segmentID = empTB.segmentID AND segTB.companyID={$companyID}
                          AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                          WHERE attendanceTB.companyID={$companyID} AND attendanceTB.ID IN ({$selectedNoPay}) AND leaveMasterID != 0 ");
            //GROUP BY empTB.empID, catTB.salaryCategoryID
        }


    }

    function ssoCal($payrollMasterID, $payDateMin)
    {
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = current_date();
        $salary_categories_arr = salary_categories(array('A', 'D'));

        $ssoData = $this->db->query("SELECT ssoTB.socialInsuranceID, formulaString, expenseGlAutoID, liabilityGlAutoID, masterTB.payGroupID, isSlabApplicable,
                                     SlabID, payGroupCategories, masterTB.Description
                                     FROM srp_erp_socialinsurancemaster AS ssoTB
                                     JOIN srp_erp_paygroupmaster AS masterTB ON masterTB.socialInsuranceID=ssoTB.socialInsuranceID AND masterTB.companyID={$companyID}
                                     JOIN srp_erp_paygroupformula AS formulaTB ON formulaTB.payGroupID=masterTB.payGroupID AND formulaTB.companyID={$companyID}
                                     JOIN (
                                        SELECT socialInsuranceMasterID AS ssoID FROM srp_erp_socialinsurancedetails WHERE companyID={$companyID}
                                        GROUP BY socialInsuranceMasterID
                                     ) AS ssoDetail ON ssoDetail.ssoID = ssoTB.socialInsuranceID
                                     WHERE ssoTB.companyID={$companyID}")->result_array();

        foreach ($ssoData as $key => $ssoRow) {
            $caption = trim($ssoRow['Description'] ?? '');
            $isSlabApplicable = trim($ssoRow['isSlabApplicable'] ?? '');
            $slabID = trim($ssoRow['SlabID'] ?? '');
            $SSO_ID = trim($ssoRow['socialInsuranceID'] ?? '');
            $payGroupID = trim($ssoRow['payGroupID'] ?? '');
            $formula = trim($ssoRow['formulaString'] ?? '');
            $expenseGL = trim($ssoRow['expenseGlAutoID'] ?? '');
            $liabilityGL = trim($ssoRow['liabilityGlAutoID'] ?? '');

            if (!empty($formula) && $formula != null) {
                $getBalancePay = ($isSlabApplicable == 1) ? 'N' : 'Y';
                $formulaBuilder = formulaBuilder_to_sql($ssoRow, $salary_categories_arr, $payDateMin, $payGroupID, $getBalancePay);

                if (array_key_exists(0, $formulaBuilder)) {
                    if ($formulaBuilder[0] == 'e') {
                        $formulaBuilder[1] = $formulaBuilder[1] . 'Check the \'' . $caption . '\' formula of SSO.';
                        $formulaBuilder['SSO'] = $SSO_ID;
                        return $formulaBuilder;
                        break;
                    }
                }

                $formulaDecode = $formulaBuilder['formulaDecode'];
                $select_str2 = $formulaBuilder['select_str2'];
                $whereInClause = $formulaBuilder['whereInClause'];

                $select_str2 = (trim($select_str2) == '') ? '' : $select_str2 . ',';


                if ($isSlabApplicable == 1) {
                    $slabData = $this->db->query("SELECT startRangeAmount strAmount, endRangeAmount endAmount, formulaString, payGroupCategories,
                                                  slabMaster.description, slabMaster.ssoSlabMasterID
                                                  FROM srp_erp_ssoslabmaster AS slabMaster
                                                  JOIN srp_erp_ssoslabdetails AS slabDet ON slabMaster.ssoSlabMasterID = slabDet.ssoSlabMasterID
                                                  AND slabDet.companyID={$companyID}
                                                  WHERE slabMaster.companyID={$companyID} AND slabMaster.ssoSlabMasterID={$slabID}")->result_array();


                    if (!empty($slabData)) {
                        foreach ($slabData as $keySlab => $slabRow) {
                            $slabCaption = $slabRow['description'];
                            $ssoSlabMasterID = $slabRow['ssoSlabMasterID'];
                            $formulaBuilder_slab = formulaBuilder_to_sql($slabRow, $salary_categories_arr, $payDateMin, $payGroupID);

                            if (array_key_exists(0, $formulaBuilder)) {
                                if ($formulaBuilder[0] == 'e') {
                                    $formulaBuilder[1] = $formulaBuilder[1] . 'Check the \'' . $slabCaption . '\' formula of SSO slab.';
                                    $formulaBuilder['sso_slab'] = $ssoSlabMasterID;
                                    return $formulaBuilder;
                                    break;
                                }
                            }

                            $formulaDecode_slab = $formulaBuilder_slab['formulaDecode'];
                            $select_str_slab = $formulaBuilder_slab['select_str2'];
                            $select_str_slab = ($select_str_slab == '') ? '' : $select_str_slab . ', ';
                            $whereInClause_slab = $formulaBuilder_slab['whereInClause'];

                            $strAmount = $slabRow['strAmount'];
                            $endAmount = $slabRow['endAmount'];

                            $this->db->query("INSERT INTO srp_erp_payrolldetail ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, GLCode, liabilityGL,
                                              transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                              companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                              companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER,
                                              companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdPCID, createdUserID, createdUserGroup, createdUserName,
                                              createdDateTime, segmentID, segmentCode)

                                              SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', 'PAY_GROUP', calculationTB.empID, 'G', '{$expenseGL}', $liabilityGL,
                                              round( (IF( ({$formulaDecode_slab}) < 1, 0, (({$formulaDecode_slab}) * -1))), trDPlace ) AS trAmount, trCuID, trCu, 1, trDPlace,
                                              round( (IF( ({$formulaDecode_slab}) < 1, 0, ((({$formulaDecode_slab})/ locCuER) * -1)) ) , locCuDPlace ) AS localAmount, locCuID,
                                              locCu, locCuER, locCuDPlace,
                                              round( (IF( ({$formulaDecode_slab}) < 1, 0, ((({$formulaDecode_slab})/ repCuER) * -1)) ) , repCuDPlace ) AS reportingAmount,
                                              repCuID, repCu, repCuER, repCuDPlace,
                                              {$companyID}, '{$companyCode}', '{$createdPCID}', '{$createdUserID}', '{$createdUserGroup}', '{$createdUserName}',
                                              '{$createdDateTime}', seg.segmentID, seg.segmentCode
                                              FROM (
                                                    SELECT payDet.empID, {$select_str_slab}
                                                    transactionCurrencyID AS trCuID, transactionCurrency AS trCu, transactionER AS trER, transactionCurrencyDecimalPlaces
                                                    AS trDPlace, companyLocalCurrencyID AS locCuID , companyLocalCurrency AS locCu, companyLocalER AS locCuER,
                                                    companyLocalCurrencyDecimalPlaces AS locCuDPlace, companyReportingCurrencyID AS repCuID, companyReportingCurrency AS repCu,
                                                    companyReportingER AS repCuER, companyReportingCurrencyDecimalPlaces AS repCuDPlace
                                                    FROM srp_erp_payrolldetail AS payDet
                                                    JOIN srp_erp_socialinsurancedetails AS ssoDet ON ssoDet.empID = payDet.empID AND ssoDet.companyID={$companyID}
                                                    AND socialInsuranceMasterID={$SSO_ID}
                                                    WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID}
                                                    {$whereInClause_slab}  GROUP BY payDet.empID, salCatID, detailType
                                              ) calculationTB
                                              JOIN (
                                                    SELECT EmpID, segmentID FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                                              ) AS empTB ON empTB.EmpID=calculationTB.empID
                                              JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID AND seg.companyID = {$companyID}
                                              WHERE calculationTB.empID IN (
                                                   SELECT empID FROM (
                                                       SELECT calculationTB.empID, round( ({$formulaDecode}), trDPlace) AS trAmount FROM (
                                                            SELECT payDet.empID, {$select_str2} transactionCurrencyDecimalPlaces AS trDPlace
                                                            FROM srp_erp_payrolldetail AS payDet
                                                            JOIN srp_erp_socialinsurancedetails AS ssoDet ON ssoDet.empID = payDet.empID AND ssoDet.companyID={$companyID}
                                                            AND socialInsuranceMasterID={$SSO_ID}
                                                            WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID}
                                                            {$whereInClause}  GROUP BY payDet.empID, salCatID, detailType
                                                       ) calculationTB GROUP BY empID
                                                   ) AS currentMonthAmountTB WHERE trAmount > {$strAmount} and trAmount <= {$endAmount}
                                              ) GROUP BY calculationTB.empID");

                        }
                    }

                } else {

                    $this->db->query("INSERT INTO srp_erp_payrolldetail ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, GLCode, liabilityGL,
                                   transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                   companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                   companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces,
                                   companyID, companyCode, createdPCID, createdUserID, createdUserGroup, createdUserName, createdDateTime, segmentID, segmentCode)

                                   SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', 'PAY_GROUP', calculationTB.empID, 'G', '{$expenseGL}', $liabilityGL,
                                   round( (IF( ({$formulaDecode}) < 1, 0, (({$formulaDecode})  * -1))), transactionCurrencyDecimalPlaces)AS transactionAmount,
                                   transactionCurrencyID, transactionCurrency,
                                   transactionER, transactionCurrencyDecimalPlaces,
                                   round( (IF( ({$formulaDecode}) < 1, 0, (( ({$formulaDecode}) / companyLocalER) * -1 ))) , companyLocalCurrencyDecimalPlaces  )AS localAmount,
                                   companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                   round( (IF( ({$formulaDecode}) < 1, 0, ((({$formulaDecode})/ companyReportingER) * -1) )),
                                   companyReportingCurrencyDecimalPlaces  )AS reportingAmount,
                                   companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces,
                                   {$companyID}, '{$companyCode}', '{$createdPCID}', '{$createdUserID}', '{$createdUserGroup}', '{$createdUserName}', '{$createdDateTime}',
                                   seg.segmentID, seg.segmentCode
                                   FROM (
                                        SELECT payDet.empID, fromTB, detailType, salCatID, {$select_str2}
                                        transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                        companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                        companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces
                                        FROM srp_erp_payrolldetail AS payDet
                                        JOIN srp_erp_socialinsurancedetails AS ssoDet ON ssoDet.empID = payDet.empID AND ssoDet.companyID={$companyID}
                                        AND socialInsuranceMasterID={$SSO_ID}
                                        WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID}
                                        {$whereInClause}  GROUP BY payDet.empID, salCatID, detailType
                                   ) calculationTB
                                   JOIN (
                                        SELECT EmpID, segmentID FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                                   ) AS empTB ON empTB.EmpID=calculationTB.empID
                                   JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID AND seg.companyID = {$companyID}
                                   GROUP BY empID");

                    /*echo $this->db->last_query();
                    die();*/
                }

            }
        }

    }

    function payeeCal($payrollMasterID, $isNonPayroll)
    {
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = current_date();
        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup();

        $payeeData = $this->db->query("SELECT payeeTB.payeeMasterID, formulaString, liabilityGlAutoID, masterTB.payGroupID, SlabID, payGroupCategories, payeeTB.Description
                                     FROM srp_erp_payeemaster AS payeeTB
                                     JOIN srp_erp_paygroupmaster AS masterTB ON masterTB.payeeID=payeeTB.payeeMasterID AND masterTB.companyID={$companyID}
                                     JOIN srp_erp_paygroupformula AS formulaTB ON formulaTB.payGroupID=masterTB.payGroupID AND formulaTB.companyID={$companyID}
                                     WHERE payeeTB.companyID={$companyID} AND payeeTB.isNonPayroll='{$isNonPayroll}'")->result_array();


        foreach ($payeeData as $key => $payeeRow) {

            $caption = trim($payeeRow['Description'] ?? '');
            $payGroupID = trim($payeeRow['payGroupID'] ?? '');
            $payeeMasterID = trim($payeeRow['payeeMasterID'] ?? '');
            $formula = trim($payeeRow['formulaString'] ?? '');
            $slabID = trim($payeeRow['SlabID'] ?? '');
            $liabilityGL = trim($payeeRow['liabilityGlAutoID'] ?? '');

            if (!empty($formula) && $formula != null) {
                $formulaBuilder = payGroup_formulaBuilder_to_sql('decode', $payeeRow, $salary_categories_arr, $payGroup_arr);

                if (array_key_exists(0, $formulaBuilder)) {
                    if ($formulaBuilder[0] == 'e') {
                        $formulaBuilder[1] = $formulaBuilder[1] . 'Check the \'' . $caption . '\' formula of PAYEE.';
                        $formulaBuilder['Payee'] = $payGroupID;
                        return $formulaBuilder;
                        break;
                    }
                }

                $formulaDecode = $formulaBuilder['formulaDecode'];
                //$select_str1 = $formulaBuilder['select_str1'];  ".$select_str1." ,
                $select_salCat_str = trim($formulaBuilder['select_salaryCat_str'] ?? '');
                $select_group_str = trim($formulaBuilder['select_group_str'] ?? '');
                $whereInClause = trim($formulaBuilder['whereInClause'] ?? '');
                $whereInClause_group = trim($formulaBuilder['whereInClause_group'] ?? '');


                if ($whereInClause != '' && $select_salCat_str != '') {
                    $select_salCat_str .= ',';
                    $whereInClause = 'salCatID IN (' . $whereInClause . ')';

                }

                if ($whereInClause_group != '' && $select_group_str != '') {
                    $select_group_str .= ',';
                    $whereInClause_group = 'detailTBID IN (' . $whereInClause . ') AND fromTB = \'PAY_GROUP\'';
                }

                if ($whereInClause != '' && $whereInClause_group != '') {
                    $whereIN = $whereInClause . ' OR ' . $whereInClause_group;
                } else {
                    $whereIN = $whereInClause . ' ' . $whereInClause_group;
                }

                $whereIN = (trim($whereIN) == '') ? '' : 'AND (' . $whereIN . ')';

                $detailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrolldetail' : 'srp_erp_payrolldetail';

                $payeeFormula = '(IF( slabDet.percentage = 0, slabDet.thresholdAmount, 	(trAmount * (slabDet.percentage/100)) - slabDet.thresholdAmount ))';

                $this->db->query("INSERT INTO {$detailTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, liabilityGL,
                                  transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces,
                                  companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces,
                                  companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces,
                                  companyID, companyCode, createdPCID, createdUserID, createdUserGroup, createdUserName, createdDateTime, segmentID, segmentCode)

                                  SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', 'PAY_GROUP', calculationGROUP.empID, 'G', {$liabilityGL},
                                  round( ($payeeFormula) *-1 , dPlace )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, dPlace,
                                  round( ($payeeFormula/ companyLocalER) *-1  , dPlace )AS localAmount, companyLocalCurrencyID , companyLocalCurrency, companyLocalER,
                                  localDPlace,
                                  round( ($payeeFormula / companyReportingER ) *-1, dPlace )AS reportingAmount, companyReportingCurrencyID, companyReportingCurrency,
                                  companyReportingER, repoDPlace,
                                  {$companyID}, '{$companyCode}', '{$createdPCID}', '{$createdUserID}', '{$createdUserGroup}', '{$createdUserName}', '{$createdDateTime}',
                                  seg.segmentID, seg.segmentCode
                                  FROM
                                  (
                                      SELECT  calculationTB.empID, (" . $formulaDecode . ") AS trAmount, {$slabID} AS slbID,
                                      transactionCurrencyID, transactionCurrency, transactionER, dPlace,
                                      companyLocalCurrencyID , companyLocalCurrency, companyLocalER, localDPlace,
                                      companyReportingCurrencyID, companyReportingCurrency, companyReportingER, repoDPlace
                                      FROM
                                      (
                                              SELECT payDet.empID, fromTB, detailType, salCatID, {$select_salCat_str} {$select_group_str}
                                              transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces AS dPlace,
                                              companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces AS localDPlace,
                                              companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces AS repoDPlace
                                              FROM {$detailTB} AS payDet
                                              JOIN srp_erp_socialinsurancedetails AS empTB ON empTB.empID = payDet.empID AND empTB.companyID={$companyID} AND
                                              payeeID={$payeeMasterID}
                                              WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID} {$whereIN}
                                              GROUP BY payDet.empID, salCatID, detailType
                                      ) calculationTB GROUP BY empID
                                  ) AS calculationGROUP
                                  JOIN srp_erp_slabsdetail AS slabDet ON slabDet.slabsMasterID = calculationGROUP.slbID
                                  AND round(calculationGROUP.trAmount) BETWEEN rangeStartAmount AND rangeEndAmount AND slabDet.companyID={$companyID}
                                  JOIN (
                                        SELECT EmpID, segmentID FROM srp_erp_payrollheaderdetails WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID}
                                  ) AS empTB ON empTB.EmpID=calculationGROUP.empID
                                  JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID AND seg.companyID = {$companyID}");

            }
        }
    }

    function payGroup_temporary_calculation($payrollMasterID, $isNonPayroll, $payDateMin = null)
    {
        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdUserID = current_userID();
        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup(1);


        $payGroups = $this->db->query("SELECT temp.payGroupID, formulaString, payGroupCategories, caption
                                       FROM srp_erp_pay_templatefields AS temp
                                       JOIN srp_erp_paygroupformula AS formulaTB ON formulaTB.payGroupID=temp.payGroupID AND formulaTB.companyID={$companyID}
                                       WHERE temp.fieldType = 'G' AND temp.companyID = {$companyID} AND isCalculate =1")->result_array();


        foreach ($payGroups as $key => $payRow) {

            $caption = trim($payRow['caption'] ?? '');
            $payGroupID = trim($payRow['payGroupID'] ?? '');
            $formula = trim($payRow['formulaString'] ?? '');

            if (!empty($formula) && $formula != null) {
                $formulaBuilder = payGroup_formulaBuilder_to_sql('decode', $payRow, $salary_categories_arr, $payGroup_arr, $payGroupID, $payDateMin);
                if (array_key_exists(0, $formulaBuilder)) {
                    if ($formulaBuilder[0] == 'e') {
                        $formulaBuilder[1] = $formulaBuilder[1] . 'Check the \'' . $caption . '\' formula of Pay group.';
                        $formulaBuilder['pay_group'] = $payGroupID;
                        return $formulaBuilder;
                        break;
                    }
                }
                $formulaDecode = $formulaBuilder['formulaDecode'];
                $select_monthlyAD_str = trim($formulaBuilder['select_monthlyAD_str'] ?? '');
                $select_salCat_str = trim($formulaBuilder['select_salaryCat_str'] ?? '');
                $select_group_str = trim($formulaBuilder['select_group_str'] ?? '');
                $whereInClause = trim($formulaBuilder['whereInClause'] ?? '');
                $where_MA_MD_Clause = $formulaBuilder['where_MA_MD_Clause'];
                $whereInClause_group = trim($formulaBuilder['whereInClause_group'] ?? '');


                $where_MA_MD_Clause_str = '';
                if (!empty($where_MA_MD_Clause)) {
                    if (count($where_MA_MD_Clause) > 1) {
                        $where_MA_MD_Clause_str = ' calculationTB = \'' . $where_MA_MD_Clause[0] . '\' OR calculationTB = \'' . $where_MA_MD_Clause[1] . '\'';
                    } else {
                        $where_MA_MD_Clause_str = ' calculationTB = \'' . $where_MA_MD_Clause[0] . '\'';
                    }
                }


                if ($select_monthlyAD_str != '') {
                    $select_monthlyAD_str .= ',';
                }

                if ($whereInClause != '' && $select_salCat_str != '') {
                    $select_salCat_str .= ',';
                    $whereInClause = 'salCatID IN (' . $whereInClause . ') AND calculationTB = \'SD\'';

                }

                if ($whereInClause_group != '' && $select_group_str != '') {
                    $select_group_str .= ',';
                    $whereInClause_group = 'detailTBID IN (' . $whereInClause_group . ') AND fromTB = \'PAY_GROUP\'';
                }


                if ($whereInClause != '' && $whereInClause_group != '') {
                    $whereIN = $whereInClause . ' OR ' . $whereInClause_group;
                } else {
                    $whereIN = $whereInClause . ' ' . $whereInClause_group;
                }

                if (trim($whereIN) == '') {
                    $whereIN = (trim($where_MA_MD_Clause_str) == '') ? '' : 'AND (' . $where_MA_MD_Clause_str . ' )';
                } else {
                    $MA_MD_Clause_str_join = (trim($where_MA_MD_Clause_str) == '') ? '' : ' OR ' . $where_MA_MD_Clause_str;
                    $whereIN = 'AND (' . $whereIN . ' ' . $MA_MD_Clause_str_join . ')';
                }


                $payGroupDetailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrolldetailpaygroup' : 'srp_erp_payrolldetailpaygroup';
                $detailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrolldetail' : 'srp_erp_payrolldetail';


                $this->db->query("INSERT INTO {$payGroupDetailTB} ( payrollMasterID, detailTBID, fromTB, empID, detailType, transactionCurrencyID,
                                  transactionCurrency, transactionCurrencyDecimalPlaces, transactionAmount, segmentID, segmentCode, companyID,
                                  companyCode, createdUserID)

                                  SELECT {$payrollMasterID}, {$payGroupID}, 'PAY_GROUP', calculationTB.empID, ' ',transactionCurrencyID, transactionCurrency,
                                  transactionCurrencyDecimalPlaces, round((" . $formulaDecode . "), transactionCurrencyDecimalPlaces) AS transactionAmount,
                                  segmentID, segmentCode, {$companyID}, '{$companyCode}', '{$createdUserID}'
                                  FROM (
                                        SELECT payDet.empID, fromTB, detailType, salCatID, " . $select_salCat_str . " " . $select_group_str . " " . $select_monthlyAD_str . "
                                        transactionCurrencyID, transactionCurrency, transactionCurrencyDecimalPlaces, srp_erp_segment.segmentID, srp_erp_segment.segmentCode
                                        FROM {$detailTB} AS payDet
                                        JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = payDet.empID AND empTB.Erp_companyID={$companyID}
                                        JOIN srp_erp_segment ON srp_erp_segment.segmentID = empTB.segmentID AND srp_erp_segment.companyID = {$companyID}
                                        WHERE payDet.companyID = {$companyID} AND payrollMasterID = {$payrollMasterID} {$whereIN}
                                        GROUP BY payDet.empID, salCatID, payDet.fromTB, detailTBID
                                  ) calculationTB
                                  GROUP BY empID ");


            }
        }

        return ['s'];
    }

    function fetchPaySheetData($payrollMasterID, $isNonPayroll, $lastMonth_arr, $segmentID, $templateID = null)  /*($payrollMasterID, $isNonPayroll=null )*/
    {
        $companyID = current_companyID();
        $from_approval = $this->input->post('from_approval');
        if ($isNonPayroll != 'Y') {
            $headerDetailTableName = 'srp_erp_payrollheaderdetails';
            $detailTableName = 'srp_erp_payrolldetail';
            $payGroupDetailTableName = 'srp_erp_payrolldetailpaygroup';
            $bankDetailTableName = 'srp_erp_pay_banktransfer';
        } else {
            $headerDetailTableName = 'srp_erp_non_payrollheaderdetails';
            $detailTableName = 'srp_erp_non_payrolldetail';
            $payGroupDetailTableName = 'srp_erp_non_payrolldetailpaygroup';
            $bankDetailTableName = 'srp_erp_pay_non_banktransfer';
        }

        $segmentIDFilter = '';
        $segmentIDFilter2 = '';

        $lastYear = $lastMonth_arr[0];
        $lastMonth = $lastMonth_arr[1];

        if (!empty($segmentID)) {
            $commaList = implode(', ', $segmentID);
            $segmentIDFilter = ' AND empTB.segmentID IN ("' . $commaList . '" )';
            $segmentIDFilter2 = ' AND pay2.segmentID IN ("' . $commaList . '" )';
        }

        /************************************************************************
         *
         ************************************************************************/
        $str1 = $str2 = '';
        $empCount = null;

        $isGroupAccess = 0;
        if ($from_approval != 'Y') {
            $isGroupAccess = getPolicyValues('PAC', 'All');
        }

        if ($isGroupAccess == 1) {
            $currentEmp = current_userID();
            $str1 = "JOIN (
                        SELECT groupID FROM srp_erp_payrollgroupincharge
                        WHERE companyID={$companyID} AND companyID={$companyID} AND empID={$currentEmp}
                     ) AS accessTB ON accessTB.groupID = empTB.accessGroupID";

            $str2 = "JOIN (
                         SELECT groupID FROM srp_erp_payrollgroupincharge
                         WHERE companyID={$companyID} AND companyID={$companyID} AND empID={$currentEmp}
                     ) AS accessTB ON accessTB.groupID = pay2.accessGroupID";

            $empCount = $this->db->query("SELECT COUNT(payrollMasterID) AS empCount FROM $headerDetailTableName WHERE companyID={$companyID}
                                          AND payrollMasterID={$payrollMasterID} ")->row('empCount');
        }

        $sortOrder_policy = getPolicyValues('PSO', 'All');

        $sortOrder = 'empID DESC';
        switch ($sortOrder_policy){
            case 1: $sortOrder = 'ECode ASC'; break;
            case 2: $sortOrder = 'ECode DESC'; break;
        }


        $info = $this->db->query("SELECT empTB.*, empTB.transactionAmount AS netTrans , fromTB, calculationTB, detailType, salCatID, pay.detailTBID,
                                  IF( ISNULL(SUM(pay.transactionAmount)), 0, SUM(pay.transactionAmount) ) AS transactionAmount,
                                  pay.transactionCurrencyDecimalPlaces, lastNetSalary, pay.contractCode,pay.contractRef,
                                  (IF(ISNULL(lastNetSalary), 0, lastNetSalary)-empTB.transactionAmount) AS difference, seg.segmentCode AS emp_segmentCode
                                  FROM {$headerDetailTableName} AS empTB
                                  JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                  LEFT JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                  LEFT JOIN (
                                        SELECT headerDet.EmpID AS employeeID, headerDet.transactionAmount AS lastNetSalary FROM srp_erp_payrollmaster AS masterTB
                                        JOIN {$headerDetailTableName} AS headerDet ON headerDet.payrollMasterID=masterTB.payrollMasterID
                                        AND headerDet.companyID={$companyID}
                                        WHERE payrollYear={$lastYear} AND payrollMonth={$lastMonth} AND masterTB.companyID={$companyID}
                                  ) AS headerDetLast ON headerDetLast.employeeID=empTB.EmpID
                                  JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID {$segmentIDFilter}
                                  {$str1}
                                  WHERE pay.payrollMasterID = {$payrollMasterID} AND empTB.payrollMasterID = {$payrollMasterID} AND pay.companyID = {$companyID}
                                  AND fromTB != 'PAY_GROUP'
                                  GROUP BY pay.empID, pay.salCatID, pay.calculationTB
                                  UNION
                                        SELECT empTB.*, empTB.transactionAmount AS netTrans, fromTB, calculationTB, detailType, salCatID, pay.detailTBID,
                                        pay.transactionAmount AS transactionAmount, pay.transactionCurrencyDecimalPlaces, lastNetSalary,pay.contractCode,pay.contractRef,
                                        (IF(ISNULL(lastNetSalary), 0, lastNetSalary)-empTB.transactionAmount) AS difference, seg.segmentCode AS emp_segmentCode
                                        FROM {$headerDetailTableName} AS empTB
                                        JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                        LEFT JOIN (
                                            SELECT headerDet.EmpID AS employeeID, headerDet.transactionAmount AS lastNetSalary FROM srp_erp_payrollmaster AS masterTB
                                            JOIN {$headerDetailTableName} AS headerDet ON headerDet.payrollMasterID=masterTB.payrollMasterID
                                            AND headerDet.companyID={$companyID}
                                            WHERE payrollYear={$lastYear} AND payrollMonth={$lastMonth} AND masterTB.companyID={$companyID}
                                        ) AS headerDetLast ON headerDetLast.employeeID=empTB.EmpID
                                        JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID {$segmentIDFilter}
                                        {$str1}
                                        WHERE fromTB = 'PAY_GROUP' AND pay.payrollMasterID = {$payrollMasterID} AND empTB.payrollMasterID = {$payrollMasterID}
                                  UNION
                                        SELECT pay2.*, pay2.transactionAmount AS netTrans, fromTB, fromTB AS calculationTB, detailType, '' AS salCatID, detailTBID,
                                        payGroup.transactionAmount, payGroup.transactionCurrencyDecimalPlaces, lastNetSalary,lastNetSalary,lastNetSalary,
                                        (IF(ISNULL(lastNetSalary), 0, lastNetSalary)-pay2.transactionAmount) AS difference, '' AS emp_segmentCode
                                        FROM {$payGroupDetailTableName}  AS payGroup
                                        JOIN {$headerDetailTableName} AS pay2 ON payGroup.empID=pay2.empID AND pay2.companyID={$companyID} AND
                                        pay2.payrollMasterID={$payrollMasterID}
                                        LEFT JOIN (
                                            SELECT headerDet.EmpID AS employeeID, headerDet.transactionAmount AS lastNetSalary FROM srp_erp_payrollmaster AS masterTB
                                            JOIN {$headerDetailTableName} AS headerDet ON headerDet.payrollMasterID=masterTB.payrollMasterID
                                            AND headerDet.companyID={$companyID}
                                            WHERE payrollYear={$lastYear} AND payrollMonth={$lastMonth} AND masterTB.companyID={$companyID}
                                        ) AS headerDetLast ON headerDetLast.employeeID=payGroup.empID
                                        JOIN srp_erp_segment AS seg ON seg.segmentID = pay2.segmentID {$segmentIDFilter2}
                                        {$str2}
                                        WHERE payGroup.companyID={$companyID} AND payGroup.payrollMasterID={$payrollMasterID}
                                  ORDER BY {$sortOrder}")->result_array(); //ORDER BY ABS(secondaryCode) DESC

        //    echo '<pre>';
        //    print_r($info); exit;                                        
        // echo $this->db->last_query();
        if (isset($info)) {
            $nonZeroColumns = array();
            if ($templateID != null) {
                $columnWiseSum = $this->db->query("SELECT fromTB, CONCAT('AD_',salCatID) AS headerCode, sum(pay.transactionAmount) AS transactionAmount
                                  FROM {$headerDetailTableName} AS empTB
                                  JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                  LEFT JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                  JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID {$segmentIDFilter}
                                  {$str1}
                                  WHERE pay.payrollMasterID = {$payrollMasterID} AND empTB.payrollMasterID = {$payrollMasterID} AND pay.companyID = {$companyID}
                                  AND fromTB != 'PAY_GROUP'
                                  GROUP BY pay.salCatID, pay.calculationTB
                                  UNION
                                        SELECT fromTB, CONCAT('G_',pay.detailTBID) AS headerCode, SUM(pay.transactionAmount) AS transactionAmount
                                        FROM {$headerDetailTableName} AS empTB
                                        JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                        JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID {$segmentIDFilter}
                                        {$str1}
                                        WHERE fromTB = 'PAY_GROUP' AND pay.payrollMasterID = {$payrollMasterID} AND empTB.payrollMasterID = {$payrollMasterID}
                                        GROUP BY pay.detailTBID
                                  UNION
                                        SELECT fromTB, CONCAT('G_',payGroup.detailTBID) AS headerCode, SUM(payGroup.transactionAmount) AS transactionAmount
                                        FROM {$payGroupDetailTableName}  AS payGroup
                                        JOIN {$headerDetailTableName} AS pay2 ON payGroup.empID=pay2.empID AND pay2.companyID={$companyID} AND
                                        pay2.payrollMasterID={$payrollMasterID}
                                        JOIN srp_erp_segment AS seg ON seg.segmentID = pay2.segmentID {$segmentIDFilter2}
                                        {$str2}
                                        WHERE payGroup.companyID={$companyID} AND payGroup.payrollMasterID={$payrollMasterID}
                                        GROUP BY payGroup.detailTBID")->result_array();


                foreach ($columnWiseSum as $columnWiseSumKey => $columnWiseSumRow) {
                    $headerCode = $columnWiseSumRow['headerCode'];
                    $transactionAmount = $columnWiseSumRow['transactionAmount'];

                    if ($transactionAmount != 0) {
                        array_push($nonZeroColumns, $headerCode);
                    }
                }

            }


            $bankDet = $this->db->query("SELECT empID, GROUP_CONCAT(br_acc SEPARATOR ' | ') emp_bank_det FROM (
                                      SELECT empID, CONCAT(branchName, '  -  ',accountNo ) AS br_acc
                                      FROM {$bankDetailTableName} WHERE payrollMasterID = {$payrollMasterID}
                                    ) AS bankDet
                                    GROUP BY empID")->result_array();

            if(!empty($bankDet)){
                $bankDet = array_group_by($bankDet, 'empID');
            }
            else{
                $bankDet = [];
            }


            $dataArray = array();
            $i = 0;
            $j = 0;
            $ECode = '';


            foreach ($info as $row) {
                $tmpECode = $row['EmpID'];

                if ($ECode != $tmpECode) {
                    $j = 0;
                    $i++;

                    switch ($row['Gender']) {
                        case '1':
                            $gender = 'Male';
                            break;

                        case '2':
                            $gender = 'Female';
                            break;

                        default :
                            $gender = '-';
                    }

                    //$dataArray[$i]['empDet'] = $row;
                    $thisEmp_bankDet = '';
                    if(!empty($bankDet)){
                        $thisEmp_bankDet = (array_key_exists($row['EmpID'], $bankDet))? $bankDet[$row['EmpID']][0]['emp_bank_det']: '';
                    }


                    $dataArray[$i]['empDet'] = array(
                        'E_ID' => $row['EmpID'],
                        'ECode' => $row['ECode'],
                        'Ename1' => $row['Ename1'],
                        'Ename2' => $row['Ename2'],
                        'Ename3' => $row['Ename3'],
                        'Ename4' => $row['Ename4'],
                        'EmpShortCode' => $row['EmpShortCode'],
                        'Designation' => $row['Designation'],
                        'Gender' => $gender,
                        'EcTel' => $row['Tel'],
                        'EcMobile' => $row['Mobile'],
                        'EDOJ' => $row['DOJ'],
                        'payCurrency' => $row['payCurrency'],
                        'nationality' => $row['nationality'],
                        'lastMonthNetPay' => $row['lastNetSalary'],
                        'difference' => $row['difference'],
                        'dPlaces' => $row['transactionCurrencyDecimalPlaces'],
                        'segmentID' => $row['emp_segmentCode'],
                        'payrollHeaderDetID' => $row['payrollHeaderDetID'],
                        'secondaryCode' => $row['secondaryCode'],
                        'payComments' => $row['payComment'],
                        'project' => $row['contractCode'].' <br> '.$row['contractRef'],
                        'projectRef' => $row['contractRef'],
                        'bankDetails' => $thisEmp_bankDet,
                        'totDayAbsent' => $row['totDayAbsent'],
                        'totDayPresent'=> $row['totDayPresent'],
                        'totOTHours' => $row['totOTHours']
                    );

                    $ECode = $row['EmpID'];
                }

                if ($row['calculationTB'] == 'SD') {
                    $cat = $row['salCatID'];
                } else if ($row['fromTB'] == 'PAY_GROUP') {
                    $cat = 'G_' . $row['detailTBID'];
                } else {
                    $cat = $row['fromTB'];
                }

                $dataArray[$i]['empSalDec'][$j] = array(
                    'catID' => $cat,
                    'catType' => $row['detailType'],
                    'amount' => $row['transactionAmount'],
                );
                $j++;
            }

            return ['empDet' => $dataArray, 'nonZeroColumns' => $nonZeroColumns, 'empCount' => $empCount];

        } else {
            return 'There is no record.';
        }
    }

    function currencyWiseSum($payrollMasterID, $isNonPayroll, $segmentID = []) /*($payrollMasterID, $isNonPayroll=null)*/
    {
        $companyID = current_companyID();
        $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';
        if (!empty($segmentID)) {
            $commaList = implode(', ', $segmentID);
            $segmentIDFilter = ' AND segmentID IN ("'.$commaList.'")';
        } else {
            $segmentIDFilter = '';
        }


        $query = $this->db->query("SELECT transactionCurrency AS currency, transactionCurrencyDecimalPlaces AS dPlace, SUM(transactionAmount) AS amount
                                   FROM {$tableName}
                                   WHERE companyID = {$companyID}
                                   AND payrollMasterID = {$payrollMasterID} {$segmentIDFilter}
                                   GROUP BY transactionCurrency");

        return $query->result_array();
    }

    function loadPaySheetData($isNonPayroll)
    {
        $processTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        $where = array(
            'payrollYear' => $this->input->post('payYear'),
            'payrollMonth' => $this->input->post('payMonth'),
            'companyID' => current_companyID()
        );

        $query = $this->db->select('payrollMasterID')->from($processTB)->where($where)->get();
        return $query->row_array();
    }

    function getPayrollDetails($payrollID, $isNonPayroll = null)
    {
        $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        $convertFormat = convert_date_format_sql();
        $query = $this->db->select('payrollYear, payrollMonth, documentCode,processDate as processDatentcon, narration,DATE_FORMAT(processDate,\'' . $convertFormat . '\') AS processDate, confirmedYN,
            confirmedByName, approvedYN, approvedbyEmpName, isBankTransferProcessed, templateID, DATE_FORMAT(visibleDate,\'' . $convertFormat . '\') AS visibleDate,
            LAST_DAY(CONCAT(payrollYear,"-",payrollMonth,"-01")) AS payrollLastDate')
            ->from($tableName)->where('payrollMasterID', $payrollID)->where('companyID', current_companyID())->get();
        return $query->row_array();
    }

    function getPayrollApproveLevel($payrollID)
    {
        $query = $this->db->select('payrollYear, payrollMonth, t1.documentCode, narration, processDate, approvalLevelID')
            ->from('srp_erp_payrollmaster t1')
            ->join('srp_erp_documentapproved t2', 't1.payrollMasterID=t2.documentSystemCode')
            ->where('departmentID', 'SP')
            ->where('payrollMasterID', $payrollID)->get();
        return $query->row_array();
    }

    function update_PaySheet($payrollDet, $financeData, $isNonPayroll)
    {
        $payrollID = $this->input->post('hidden_payrollID');
        $narration = $this->input->post('payNarration'); //str_replace("'", '&#39', $this->input->post('payNarration'));
        $isConfirm = $this->input->post('isConfirm');
        $templateID = $this->input->post('templateId');
        $date_format_policy = date_format_policy();
        $prssngDat = $this->input->post('processingDate');
        $processingDate = input_format_date($prssngDat, $date_format_policy);
        $visibleDate = $this->input->post('visibleDate');
        $visibleDate = input_format_date($visibleDate, $date_format_policy);


        $payrollMonth = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));

        $companyID = $this->common_data['company_data']['company_id'];
        $createdPCID = $this->common_data['current_pc'];
        $createdUserID = $this->common_data['current_userID'];
        $createdUserName = $this->common_data['current_user'];
        $createdDateTime = $this->common_data['current_date'];

        $updateData = array(
            'financialYearID' => $financeData[1],
            'financialPeriodID' => $financeData[2],
            'processDate' => $processingDate,
            'visibleDate' => $visibleDate,
            'templateID' => $templateID,
            'narration' => $narration,
            'modifiedPCID' => $createdPCID,
            'modifiedUserID' => $createdUserID,
            'modifiedUserName' => $createdUserName,
            'modifiedDateTime' => $createdDateTime
        );

        $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';

        $this->db->where('payrollMasterID', $payrollID)
            ->where('companyID', $companyID)
            ->update($tableName, $updateData);

        if ($this->db->affected_rows() > 0) {
            if ($isConfirm == 1) {
                return $this->confirm_payrollDetails($payrollID, $isNonPayroll);
            } else {
                return array('s', $payrollMonth . ' payroll is updated successfully');
            }
        } else {
            return array('e', 'Failed to update [ ' . $payrollMonth . ' ] payroll');
        }


    }

    function confirm_payrollDetails($payrollID, $isNonPayroll)
    {
        $payrollDet = $this->getPayrollDetails($payrollID, $isNonPayroll);
        $payrollMonth = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));

        if ($payrollDet['confirmedYN'] != 1) {
            $isAllProcessUpdated = $this->isAllSalaryProcessTablesUpdated($payrollID, $payrollDet['payrollYear'], $payrollDet['payrollMonth'], $isNonPayroll);

            if ($isAllProcessUpdated == 'Y') {

                $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
                $docCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
                $documentDate = $payrollDet['processDatentcon'];

                $validate_code = validate_code_duplication($payrollDet['documentCode'], 'documentCode', $payrollID,'payrollMasterID', $tableName);
                if(!empty($validate_code)) {
                    return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                }

                $isAutoApproval = get_document_auto_approval($docCode);
                $this->load->library('approvals'); // Document Confirmation also created with the approvals library

                if ($isAutoApproval == 0) {
                   $this->approvals->auto_approve($payrollID, $tableName, 'payrollMasterID', $docCode, $payrollDet['documentCode'], $documentDate);
                    return $this->double_entries($payrollID, $isNonPayroll);
                }

                $approvals_status = $this->approvals->CreateApproval($docCode, $payrollID, $payrollDet['documentCode'], 'Payroll Approval', $tableName,
                    'payrollMasterID', 0, $documentDate);

                if ($approvals_status == 3) {
                    return array('w', 'There is no user exists to perform Payroll approval for this company.');
                } elseif ($approvals_status == 1) {
                    return array('s', 'Create Approval : ' . $payrollDet['documentCode']);
                } else {
                    return array('w', 'some thing went wrong', $approvals_status);
                }
            } else {
                return $isAllProcessUpdated;
            }
        } else {
            return array('e', '[ ' . $payrollMonth . ' ] payroll is already confirmed');
        }
    }

    function isAllSalaryProcessTablesUpdated($payrollMasterID, $payYear, $payMonth, $isNonPayroll)
    {

        return 'Y';
        die();

        $companyID = current_companyID();
        $payrollMonth = date('Y - F', strtotime($payYear . '-' . $payMonth . '-01'));
        $payDate_arr = $this->createDate($payYear, $payMonth);
        $payDateMin = $payDate_arr['minDate'];
        $payDateMax = $payDate_arr['maxDate'];

        $errorRefreshMsg = null;
        $errorConfirmMsg = null;
        $separator = null;
        $err_count = 0;

        //Verify salary declaration
        $query = $this->db->query("SELECT CONCAT(Ename1,' | ',ECode) AS empName, confirmedYN
                                   FROM srp_erp_pay_salarydeclartion  AS sal_decl
                                   JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = sal_decl.employeeNo
                                   WHERE companyID = '$companyID' AND effectiveDate < '$payDateMax'
                                   AND id NOT IN (
                                        SELECT pay.detailTBID from srp_erp_payrolldetail AS pay
                                        JOIN srp_erp_pay_salarydeclartion AS sal ON sal.id = pay.detailTBID
                                        WHERE pay.payrollMasterID = '$payrollMasterID' AND  pay.fromTB = 'SD'
                                   )
                                   GROUP BY employeeNo, confirmedYN");
        $salDeclaration = $query->result_array();

        if ($salDeclaration != null) {
            foreach ($salDeclaration as $sal) {
                $separator = ($err_count > 0) ? '<br>' : '';
                if ($sal['confirmedYN'] == 1) {
                    $errorRefreshMsg .= $separator . 'Salary Declaration of [ ' . $sal['empName'] . ' ]';
                    $err_count++;
                } else {
                    $errorConfirmMsg .= $separator . 'Salary Declaration of [ ' . $sal['empName'] . ' ]';
                    $err_count++;
                }
            }
        }

        //Verify monthly additions
        $query = $this->db->select('monthlyAdditionsMasterID, monthlyAdditionsCode, confirmedYN')
            ->from('srp_erp_pay_monthlyadditionsmaster')
            ->where('dateMA >= ', $payDateMin)->where('dateMA < ', $payDateMax)->where('isProcessed != ', 1)->where('companyID', $companyID)
            ->order_by('monthlyAdditionsMasterID')->get();
        $mAddition = $query->result_array();

        if ($mAddition != null) {
            foreach ($mAddition as $addDet) {
                $separator = ($err_count > 0) ? '<br>' : '';
                if ($addDet['confirmedYN'] == 1) {
                    $errorRefreshMsg .= $separator . 'Monthly Addition [ ' . $addDet['monthlyAdditionsCode'] . ' ]';
                    $err_count++;
                } else {
                    $errorConfirmMsg .= $separator . 'Monthly Addition [ ' . $addDet['monthlyAdditionsCode'] . ' ]';
                    $err_count++;
                }
            }
        }

        //Verify monthly deductions
        $query_ded = $this->db->select('monthlyDeductionMasterID, monthlyDeductionCode, confirmedYN')
            ->from('srp_erp_pay_monthlydeductionmaster')
            ->where('dateMD >= ', $payDateMin)->where('dateMD < ', $payDateMax)->where('isProcessed != ', 1)->where('companyID', $companyID)
            ->order_by('monthlyDeductionMasterID')->get();
        $mDeduction = $query_ded->result_array();

        if ($mDeduction != null) {
            //print_r($mDeduction);
            foreach ($mDeduction as $deductDet) {
                $separator = ($err_count > 0) ? '<br>' : '';
                if ($deductDet['confirmedYN'] == 1) {
                    $errorRefreshMsg .= $separator . 'Monthly Deduction [ ' . $deductDet['monthlyDeductionCode'] . ' ]';
                    $err_count++;
                } else {
                    $errorConfirmMsg .= $separator . 'Monthly Deduction  [ ' . $deductDet['monthlyDeductionCode'] . ' ]';
                    $err_count++;
                }
            }

        }

        //Verify loan deductions
        $loanDeduct = $this->db->query("SELECT loan.loanCode FROM srp_erp_pay_emploan AS loan
                                        JOIN srp_erp_pay_emploan_schedule AS sched ON loan.ID = sched.loanID
                                        WHERE loan.companyID = {$companyID} AND approvedYN = 1 AND isClosed = 0 AND
                                        skipedInstallmentID = 0  AND isSetteled=0 AND scheduleDate >= '$payDateMin'
                                        AND scheduleDate < '$payDateMax' ORDER BY loan.ID ASC")->result_array();
        if ($loanDeduct != null) {
            //print_r($loanDeduct);
            foreach ($loanDeduct as $deductDet) {
                $separator = ($err_count > 0) ? '<br>' : '';
                $errorRefreshMsg .= $separator . 'Loan Deduction [ ' . $deductDet['loanCode'] . ' ]';
                $err_count++;
            }

        }


        if ($err_count > 0) {
            $errorMsg = null;
            if ($errorRefreshMsg != null) {
                $errorMsg .= 'Please refresh to update following<p>' . $errorRefreshMsg;
            }
            if ($errorConfirmMsg != null) {
                $nxtLine = ($errorRefreshMsg != null) ? '<p></p>' : '';
                $errorMsg .= $nxtLine . '<p>Please confirm  following <br>' . $errorConfirmMsg;
            }
            $errorMsg .= '<p>to confirm [ ' . $payrollMonth . ' ] payroll.</p>';
            return (array('e', $errorMsg));
        } else {
            return 'Y';
        }


    }

    function currentMonthPaysheetData_status($payYear, $payMonth, $isNonPayroll)
    {
        $companyID = current_companyID();
        $payrollMonth = date('Y - F', strtotime($payYear . '-' . $payMonth . '-01'));
        $empList = $this->input->post('selectedEmployees');
        $payDate_arr = $this->createDate($payYear, $payMonth);
        $payDateMin = $payDate_arr['minDate'];
        $payDateMax = $payDate_arr['maxDate'];
        $errorConfirmMsg = null;
        $separator = null;
        $err_count = 0;


        if ($isNonPayroll != 'Y') {
            $isPayrollCategory = '1';
        } else {
            $isPayrollCategory = '2';
        }

        //check employee assigned to primary contract
        $emp_arr = explode(',',$empList);
        $emp_contract_arr = array();

        $payroll_project = getPolicyValues('PAYPR', 'All');

        if($payroll_project == 1){
            if($emp_arr){

                foreach($emp_arr as $employeeID){
    
                    $this->db->where('empID',$employeeID);
                    $this->db->where('companyID',$companyID);
                    $this->db->where('isPrimary',1);
                    $query_emp = $this->db->from('srp_erp_contractcrew')->get()->row_array();
    
                    $this->db->where('EIdNo',$employeeID);
                    $emp_details = $this->db->from('srp_employeesdetails')->get()->row_array();
    
                    if(empty($query_emp)){
                        $separator = ($err_count > 0) ? '<br><br><b>' : '';
                        $errorConfirmMsg .= $emp_details['Ename1'].' '.$emp_details['Ename2'] . '</b> -- No Primary Contract is Present, Please assigned one before continue <br>';
                        $err_count++;
                    }
                }
    
            }
        }

        //Verify salary declaration
        $query = $this->db->query("SELECT documentSystemCode, confirmedYN, approvedYN, CONCAT(Ename2,' | ',ECode) AS empName,employeeNo
                                   FROM srp_erp_salarydeclarationmaster  AS masterTB
                                   JOIN srp_erp_salarydeclarationdetails AS detTB ON detTB.declarationMasterID = masterTB.salarydeclarationMasterID
                                   JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = detTB.employeeNo AND Erp_companyID={$companyID}
                                   WHERE masterTB.companyID = {$companyID} AND detTB.payDate < '$payDateMax' AND approvedYN != 1
                                   AND isPayrollCategory={$isPayrollCategory} AND employeeNo IN ({$empList})
                                   GROUP BY empTB.EIdNo");
        $salDeclaration = $query->result_array();


        if ($salDeclaration != null) {
            foreach ($salDeclaration as $sal) {
                $separator = ($err_count > 0) ? '<br>' : '';
                $errorConfirmMsg .= $separator . 'Salary Declaration of [ ' . $sal['empName'] . ' ] -  Document Code[ ' . $sal['documentSystemCode'] . ' ]';
                $err_count++;
            }
        }


        //Verify monthly additions
        $mAddition = $this->db->query("SELECT monthlyAdditionsCode
                                       FROM srp_erp_pay_monthlyadditionsmaster AS masterTB
                                       JOIN srp_erp_pay_monthlyadditiondetail AS detailTB
                                       ON detailTB.monthlyAdditionsMasterID=masterTB.monthlyAdditionsMasterID
                                       WHERE dateMA >= '{$payDateMin}' AND dateMA < '{$payDateMax}' AND confirmedYN != 1 AND masterTB.companyID = {$companyID}
                                       AND isNonPayroll = '{$isNonPayroll}' AND detailTB.empID IN ({$empList})
                                       GROUP BY masterTB.monthlyAdditionsMasterID
                                       ORDER BY masterTB.monthlyAdditionsMasterID")->result_array();


        if (!empty($mAddition)) {
            foreach ($mAddition as $addDet) {
                $separator = ($err_count > 0) ? '<br>' : '';
                $errorConfirmMsg .= $separator . 'Monthly Addition [ ' . $addDet['monthlyAdditionsCode'] . ' ]';
                $err_count++;
            }
        }


        //Verify monthly deductions
        $mDeduction = $this->db->query("SELECT monthlyDeductionCode
                                       FROM srp_erp_pay_monthlydeductionmaster AS masterTB
                                       JOIN srp_erp_pay_monthlydeductiondetail AS detailTB
                                       ON detailTB.monthlyDeductionMasterID=masterTB.monthlyDeductionMasterID
                                       WHERE dateMD >= '{$payDateMin}' AND dateMD < '{$payDateMax}' AND confirmedYN != 1 AND masterTB.companyID = {$companyID}
                                       AND isNonPayroll = '{$isNonPayroll}' AND detailTB.empID IN ({$empList})
                                       GROUP BY masterTB.monthlyDeductionMasterID
                                       ORDER BY masterTB.monthlyDeductionMasterID")->result_array();

        if ($mDeduction != null) {
            foreach ($mDeduction as $deductDet) {
                $separator = ($err_count > 0) ? '<br>' : '';
                $errorConfirmMsg .= $separator . 'Monthly Deduction  [ ' . $deductDet['monthlyDeductionCode'] . ' ]';
                $err_count++;
            }

        }


        if ($isNonPayroll != 'Y') {
            //Verify Variable pay monthly additions
            $variablePay = $this->db->query("SELECT monthlyAdditionsCode FROM srp_erp_ot_monthlyadditionsmaster AS masterTB
                                             JOIN srp_erp_ot_monthlyadditiondetail AS detailTB
                                             ON detailTB.monthlyAdditionsMasterID=masterTB.monthlyAdditionsMasterID
                                             WHERE dateMA >= '{$payDateMin}' AND dateMA < '{$payDateMax}' AND confirmedYN != 1
                                             AND masterTB.companyID = {$companyID} AND detailTB.empID IN ({$empList})
                                             GROUP BY masterTB.monthlyAdditionsMasterID
                                             ORDER BY masterTB.monthlyAdditionsMasterID")->result_array();


            if ($variablePay != null) {
                foreach ($variablePay as $vPayDet) {
                    $separator = ($err_count > 0) ? '<br>' : '';
                    $errorConfirmMsg .= $separator . 'Monthly OT addition  [ ' . $vPayDet['monthlyAdditionsCode'] . ' ]';
                    $err_count++;
                }

            }
        }

        //Verify loan deductions
        /*$loanDeduct = $this->db->query("SELECT loan.loanCode FROM srp_erp_pay_emploan AS loan
                                        JOIN srp_erp_pay_emploan_schedule AS sched ON loan.ID = sched.loanID
                                        WHERE loan.companyID = {$companyID} AND approvedYN = 1 AND isClosed = 0 AND
                                        skipedInstallmentID = 0  AND isSetteled=0 AND scheduleDate >= '$payDateMin'
                                        AND scheduleDate < '$payDateMax' ORDER BY loan.ID ASC")->result_array();
        if ($loanDeduct != null) {
            //print_r($loanDeduct);
            foreach ($loanDeduct as $deductDet) {
                $separator = ($err_count > 0) ? '<br>' : '';
                $errorRefreshMsg .= $separator . 'Loan Deduction [ ' . $deductDet['loanCode'] . ' ]';
                $err_count++;
            }

        }*/


        if ($err_count > 0) {
            $errorMsg = null;
            if ($errorConfirmMsg != null) {
                $errorMsg .= '<p>Please confirm / approve following <br>' . $errorConfirmMsg;
            }
            $errorMsg .= '<p>to process [ ' . $payrollMonth . ' ] payroll.</p>';
            return array('e', $errorMsg, 'isNeed_model_error');
        } else {
            return array('s', '');
        }


    }

    function payroll_delete()
    {
        $payrollMasterID = $this->input->post('delID');
        $isNonPayroll = $this->input->post('isNonPayroll');

        $payrollDet = $this->getPayrollDetails($payrollMasterID, $isNonPayroll);
        $payrollMonth = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));

        if ($payrollDet['confirmedYN'] == 1) {
            return array('e', '[ ' . $payrollMonth . ' ] is already confirmed you can not delete this');
        } else {
            $this->db->trans_start();

            $payDateMin = $payrollMonthDate = date('Y-m-d', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));
            $payDateMax = date('Y-m-d', strtotime($payrollMonthDate . ' +1 month'));

            $this->updatesSalaryProcessTables($payrollMasterID, 0, $isNonPayroll, $payDateMin, $payDateMax);

            if ($isNonPayroll != 'Y') {
                $masterTableName = 'srp_erp_payrollmaster';
                $headerDetailTableName = 'srp_erp_payrollheaderdetails';
                $detailTableName = 'srp_erp_payrolldetail';
                $payGroupDetailTableName = 'srp_erp_payrolldetailpaygroup';
            } else {
                $masterTableName = 'srp_erp_non_payrollmaster';
                $headerDetailTableName = 'srp_erp_non_payrollheaderdetails';
                $detailTableName = 'srp_erp_non_payrolldetail';
                $payGroupDetailTableName = 'srp_erp_non_payrolldetailpaygroup';
            }

            $this->db->where('payrollMasterID', $payrollMasterID);
            $this->db->delete($headerDetailTableName);

            $this->db->where('payrollMasterID', $payrollMasterID);
            $this->db->delete($detailTableName);

            $this->db->delete($payGroupDetailTableName, 'payrollMasterID=' . $payrollMasterID);

            $this->db->where('payrollMasterID', $payrollMasterID);
            $this->db->delete($masterTableName);


            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Failed to delete [ ' . $payrollMonth . ' ] payroll');
            } else {
                $this->db->trans_commit();
                return array('s', '[ ' . $payrollMonth . ' ] payroll is deleted successfully');
            }

        }
    }

    function updatesSalaryProcessTables($payrollMasterID, $isProcess, $isNonPayroll, $payDateMin, $payDateMax, $empID = null)
    {
        $companyID = current_companyID();
        $updateTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrolldetail' : 'srp_erp_payrolldetail';
        $headerTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollheaderdetails' : 'srp_erp_payrollheaderdetails';
        $whereClause1 = '';

        if ($isProcess == 0) {
            $whereClause1 = ($empID != null) ? ' AND payDet.empID =' . $empID . ' ' : '';
            $whereClause2 = ($empID != null) ? ' payDet.empID !=' . $empID . ' ' : ' payrollMasterID!=' . $payrollMasterID . ' ';
            //update monthly addition processed select query
            $mAD_str = "SELECT monthlyAdditionsMasterID FROM {$updateTB} AS payDet
                        JOIN srp_erp_pay_monthlyadditiondetail AS monthDet
                        ON monthDet.monthlyAdditionDetailID=payDet.detailTBID AND monthDet.companyID={$companyID}
                        WHERE payDet.companyID={$companyID} AND fromTB='MA' AND payrollMasterID = {$payrollMasterID}
                        {$whereClause1} AND monthlyAdditionsMasterID NOT IN (
                            SELECT monthlyAdditionsMasterID FROM srp_erp_payrolldetail AS payDet
                            JOIN srp_erp_pay_monthlyadditiondetail AS monthDet
                            ON monthDet.monthlyAdditionDetailID=payDet.detailTBID AND monthDet.companyID={$companyID}
                            WHERE payDet.companyID={$companyID} AND fromTB='MA'  AND {$whereClause2}
                            GROUP BY monthlyAdditionsMasterID
						) GROUP BY monthlyAdditionsMasterID";

            //update monthly deduction processed select query
            $mDe_str = "SELECT monthlyDeductionMasterID FROM {$updateTB} AS payDet
                        JOIN srp_erp_pay_monthlydeductiondetail AS monthDet
                        ON monthDet.monthlyDeductionDetailID=payDet.detailTBID AND monthDet.companyID={$companyID}
                        WHERE payDet.companyID={$companyID} AND fromTB='MD' AND payrollMasterID = {$payrollMasterID}
                        {$whereClause1} AND monthlyDeductionMasterID NOT IN (
                            SELECT monthlyDeductionMasterID FROM srp_erp_payrolldetail AS payDet
                            JOIN srp_erp_pay_monthlydeductiondetail AS monthDet
                            ON monthDet.monthlyDeductionDetailID=payDet.detailTBID AND monthDet.companyID={$companyID}
                            WHERE payDet.companyID={$companyID} AND fromTB='MD'  AND {$whereClause2}
                            GROUP BY monthlyDeductionMasterID
						) GROUP BY monthlyDeductionMasterID";

            //Salam air OT - Variable Pay select query
            $OT_str = "SELECT monthlyAdditionsMasterID FROM {$updateTB} AS payDet
		               JOIN srp_erp_ot_monthlyadditiondetail AS addDet
		               ON addDet.monthlyAdditionDetailID=payDet.detailTBID AND addDet.companyID={$companyID}
		               WHERE payDet.companyID={$companyID} AND fromTB='VP' AND payrollMasterID = {$payrollMasterID}
		               {$whereClause1} AND monthlyAdditionsMasterID NOT IN (
                                SELECT monthlyAdditionsMasterID
                                FROM {$updateTB} AS payDet
                                JOIN srp_erp_ot_monthlyadditiondetail AS addDet
                                ON addDet.monthlyAdditionDetailID=payDet.detailTBID AND addDet.companyID={$companyID}
                                WHERE payDet.companyID={$companyID} AND fromTB='VP' AND {$whereClause2}
                                GROUP BY monthlyAdditionsMasterID
                       ) GROUP BY monthlyAdditionsMasterID";

        } 
        else {

            //update monthly addition processed select query
            $mAD_str = "SELECT monthDet.monthlyAdditionsMasterID FROM {$updateTB} AS payroll
                        JOIN srp_erp_pay_monthlyadditiondetail AS monthDet ON payroll.detailTBID = monthDet.monthlyAdditionDetailID
                        AND monthDet.companyID={$companyID}
                        WHERE fromTB = 'MA' AND payroll.payrollMasterID = {$payrollMasterID}
                        GROUP BY monthDet.monthlyAdditionsMasterID";

            //update monthly deduction processed select query
            $mDe_str = "SELECT monthDet.monthlyDeductionMasterID FROM {$updateTB} AS payroll
                        JOIN srp_erp_pay_monthlydeductiondetail AS monthDet ON payroll.detailTBID = monthDet.monthlyDeductionDetailID
                        AND monthDet.companyID={$companyID}
                        WHERE fromTB = 'MD' AND payroll.payrollMasterID = {$payrollMasterID}
                        GROUP BY monthDet.monthlyDeductionMasterID";

            //Salam air OT - Variable Pay select query
            $OT_str = "SELECT monthlyAdditionsMasterID FROM {$updateTB} AS payDetail
                       JOIN srp_erp_ot_monthlyadditiondetail AS monthDet ON monthDet.monthlyAdditionDetailID = detailTBID
                       AND monthDet.companyID={$companyID}
                       WHERE payrollMasterID={$payrollMasterID} AND fromTB='VP' AND payDetail.companyID={$companyID}
                       GROUP BY monthlyAdditionsMasterID";

        }

        //update monthly addition processed
        $this->db->query("UPDATE srp_erp_pay_monthlyadditionsmaster JOIN ({$mAD_str}) AS monthDet2
                          ON srp_erp_pay_monthlyadditionsmaster.monthlyAdditionsMasterID = monthDet2.monthlyAdditionsMasterID
                          SET isProcessed = {$isProcess}");


        //update monthly deduction processed
        $this->db->query("UPDATE srp_erp_pay_monthlydeductionmaster JOIN ({$mDe_str}) AS monthDet2
                          ON srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID = monthDet2.monthlyDeductionMasterID
                          SET isProcessed = {$isProcess}");
        /*echo $this->db->last_query();
        die();*/


        //update loan installment settled
        $this->db->query("UPDATE srp_erp_pay_emploan_schedule JOIN (
                              SELECT scheduleTB.ID FROM {$updateTB} AS payroll
                              JOIN srp_erp_pay_emploan_schedule AS scheduleTB ON payroll.detailTBID = scheduleTB.ID
                              AND scheduleTB.companyID={$companyID}
                              WHERE fromTB = 'LO' AND payroll.payrollMasterID = '$payrollMasterID'
                          )AS scheduleTB2 ON srp_erp_pay_emploan_schedule.ID = scheduleTB2.ID
                          SET isSetteled = '$isProcess'");


        //Salam air OT - Variable Pay
        $this->db->query("UPDATE srp_erp_ot_monthlyadditionsmaster SET isProcessed = {$isProcess}
                          WHERE monthlyAdditionsMasterID IN ( {$OT_str} )");


        //update expense claim
        $this->db->query("UPDATE srp_erp_expenseclaimmaster JOIN (
                              SELECT expenseClaimMasterAutoID FROM {$updateTB} AS payDet
                              JOIN srp_erp_expenseclaimmaster AS claimMaster ON claimMaster.expenseClaimMasterAutoID = detailTBID
                              AND claimMaster.companyID={$companyID}
                              WHERE payrollMasterID={$payrollMasterID} AND fromTB='EC' AND payDet.companyID={$companyID}
                              {$whereClause1} GROUP BY expenseClaimMasterAutoID
                          )  AS claimMaster
                          ON srp_erp_expenseclaimmaster.expenseClaimMasterAutoID = claimMaster.expenseClaimMasterAutoID
                          SET addedToSalary = {$isProcess}");

        //update Invoice Commision
        $updatePayrollIDD = 0;
        if($isProcess == 1) {
            $updatePayrollIDD = $payrollMasterID;
        }  
        $this->db->query("UPDATE srp_erp_invoice_commission_detail JOIN (
                SELECT commissionDetailID FROM {$updateTB} AS payDet
                JOIN srp_erp_invoice_commission_detail AS claimMaster ON claimMaster.commissionAutoID = detailTBID
                AND claimMaster.companyID={$companyID} AND claimMaster.salesPersonEmpID = payDet.empID
                WHERE payrollMasterID={$payrollMasterID} AND fromTB='SC' AND payDet.companyID={$companyID}
                {$whereClause1}
            )  AS claimMaster
            ON srp_erp_invoice_commission_detail.commissionDetailID = claimMaster.commissionDetailID
            SET payrollID = {$updatePayrollIDD}");

        //Update attendance review table for NO-PAY and OT fetched record
        $reviewTBUpdateVal = ($isProcess == 0) ? $isProcess : $payrollMasterID;
        $whereClause = ($isNonPayroll == 'Y') ? ' noPaynonPayrollAmount != 0 ' : '( paymentOT != 0 OR noPayAmount != 0 )';
        $updateColumn = ($isNonPayroll == 'Y') ? 'nonPayrollID' : 'payrollID';
        $empWhere = ($empID != null) ? 'AND EmpID=' . $empID : '';
        /*$this->db->query("UPDATE srp_erp_pay_empattendancereview
                          JOIN (
                                SELECT ID
                                FROM srp_erp_pay_empattendancereview AS attendanceTB
                                JOIN (
                                   SELECT EmpID FROM {$headerTB} WHERE companyID={$companyID} AND payrollMasterID={$payrollMasterID} {$empWhere}
                                ) AS empTB ON empTB.EmpID=attendanceTB.empID
                                JOIN srp_erp_pay_salarycategories AS catTB ON catTB.salaryCategoryID = attendanceTB.salaryCategoryID AND catTB.companyID={$companyID}
                                WHERE attendanceTB.companyID={$companyID} AND attendanceDate >= '{$payDateMin}' AND attendanceDate < '{$payDateMax}'
                                AND {$whereClause}
                          ) AS t2 ON t2.ID=srp_erp_pay_empattendancereview.ID
                          SET {$updateColumn} = {$reviewTBUpdateVal}");*/


        $empWhere = ($empID != null) ? 'AND payDet.empID=' . $empID : '';
        //WHERE payrollMasterID={$payrollMasterID} AND payDet.companyID={$companyID} AND fromTB IN ('OT', 'NO-PAY')
        $this->db->query("UPDATE srp_erp_pay_empattendancereview JOIN (
                              SELECT attendanceTB.ID AS attendanceID FROM {$updateTB} AS payDet
                              JOIN srp_erp_pay_empattendancereview AS attendanceTB ON attendanceTB.ID = detailTBID
                              AND attendanceTB.companyID={$companyID}
                              WHERE payrollMasterID={$payrollMasterID} AND fromTB='NO-PAY' AND payDet.companyID={$companyID}
                             
                              {$empWhere}
                          )  AS attendanceTB
                          ON srp_erp_pay_empattendancereview.ID = attendanceTB.attendanceID
                          SET {$updateColumn} = {$reviewTBUpdateVal}");
    }

    function payroll_refresh()
    {
        $payrollMasterID = $this->input->post('payrollID');
        $payrollDet = $this->getPayrollDetails($payrollMasterID);
        $payrollMonth = date('Y - F', strtotime($payrollDet['payrollYear'] . '-' . $payrollDet['payrollMonth'] . '-01'));

        if ($payrollDet['confirmedYN'] == 1) {
            return array('e', '[ ' . $payrollMonth . ' ] is already confirmed you can not refresh this');
        } else {
            return $this->payrollConfirmProcess($payrollMasterID, $payrollDet['payrollYear'], $payrollDet['payrollMonth']);
        }
    }

    function payroll_bankTransfer($payrollMasterID, $isNonPayroll, $isInitial=0)
    {
        //$payrollMasterID = $this->input->post('payrollID');

        $companyID = $this->common_data['company_data']['company_id'];
        $companyCode = $this->common_data['company_data']['company_code'];
        $createdPCID = $this->common_data['current_pc'];
        $createdUserID = $this->common_data['current_userID'];
        $createdUserGroup = $this->common_data['user_group'];
        $createdUserName = $this->common_data['current_user'];
        $createdDateTime = $this->common_data['current_date'];

        if($isInitial == 0){
            $this->db->trans_start();
        }


        if ($isNonPayroll != 'Y') {
            $payrollTB = 'srp_erp_payrollmaster';
            $headerTB = 'srp_erp_payrollheaderdetails';
            $salaryAccounts = 'srp_erp_pay_salaryaccounts';
            $bankTrTB = 'srp_erp_pay_banktransfer';
            $nonBankTrTB = 'srp_erp_payroll_salarypayment_without_bank';
        } else {
            $payrollTB = 'srp_erp_non_payrollmaster';
            $headerTB = 'srp_erp_non_payrollheaderdetails';
            $salaryAccounts = 'srp_erp_non_pay_salaryaccounts';
            $bankTrTB = 'srp_erp_pay_non_banktransfer';
            $nonBankTrTB = 'srp_erp_non_payroll_salarypayment_without_bank';
        }

        $query = $this->db->query("SELECT header.EmpID, transactionAmount, transactionCurrency, transactionCurrencyDecimalPlaces, transactionCurrencyDecimalPlaces,
                                   transactionER, transactionCurrencyID, companyLocalCurrencyID,companyLocalAmount, companyLocalCurrency, companyLocalCurrencyDecimalPlaces,
                                   companyLocalER, companyReportingCurrencyID, companyReportingAmount, companyReportingCurrency, companyReportingCurrencyDecimalPlaces,
                                   companyReportingER
                                   FROM {$headerTB} AS header
                                   WHERE payrollMasterID = {$payrollMasterID}  ORDER BY header.EmpID ASC");

        $details = $query->result_array();

        $payMonth = $this->db->query("SELECT CONCAT(payrollYear, '-', payrollMonth) AS payroll, payrollMonth, payrollYear FROM srp_erp_payrollmaster WHERE CompanyID = {$companyID} AND payrollMasterID = {$payrollMasterID}")->row_array();

        if (!empty($details)) {
            $empID = null;
            foreach ($details as $det) {
                $empID = $det['EmpID'];
                $bankDet = $this->db->select('acc.bankID, bankCode, branchCode, bankName, acc.accountNo AS bnkAccountNo, accountHolderName, toBankPercentage, branchName, bankSwiftCode')
                    ->from($salaryAccounts . ' AS acc')
                    ->join('srp_erp_pay_bankmaster bnk', 'acc.bankID=bnk.bankID')
                    ->join('srp_erp_pay_bankbranches brn', 'acc.branchID=brn.branchID')
                    ->where('employeeNo', $empID)
                    ->where('acc.isActive', 1)->get();

                $bankAcc = $bankDet->result_array();

                if (!empty($bankAcc)) {
                    $totPer = 0;
                    $totTransactionAmount = $det['transactionAmount'];
                    $totLocalAmount = $det['companyLocalAmount'];
                    $totReportingAmount = $det['companyReportingAmount'];
                    $insertData = array();
                    $j = 0;
                    foreach ($bankAcc as $acc_percentage) {
                        $totPer += $acc_percentage['toBankPercentage'];
                    }

                    $totPer = ($totPer > 0) ? $totPer : 1;

                    /*echo '<pre>'; print_r($acc['bnkAccountNo']); echo '</pre>';*/
                    foreach ($bankAcc as $acc) {

                        $per = $acc['toBankPercentage'];
                        $trAmount = ($per / $totPer) * $totTransactionAmount;
                        $localAmount = ($per / $totPer) * $totLocalAmount;
                        $reportingAmount = ($per / $totPer) * $totReportingAmount;

                        $insertData[$j]['payrollMasterID'] = $payrollMasterID;
                        $insertData[$j]['empID'] = $empID;
                        $insertData[$j]['bankID'] = $acc['bankID'];
                        $insertData[$j]['bankCode'] = $acc['bankCode'];
                        $insertData[$j]['bankName'] = $acc['bankName'];
                        $insertData[$j]['accountNo'] = $acc['bnkAccountNo'];
                        $insertData[$j]['swiftCode'] = $acc['bankSwiftCode'];
                        $insertData[$j]['branchCode'] = $acc['branchCode'];
                        $insertData[$j]['acc_holderName'] = $acc['accountHolderName'];
                        $insertData[$j]['branchName'] = $acc['branchName'];
                        $insertData[$j]['salaryTransferPer'] = $per;
                        $insertData[$j]['transactionCurrencyID'] = $det['transactionCurrencyID'];
                        $insertData[$j]['transactionCurrency'] = $det['transactionCurrency'];
                        $insertData[$j]['transactionAmount'] = $trAmount;
                        $insertData[$j]['transactionER'] = $det['transactionER'];
                        $insertData[$j]['transactionCurrencyDecimalPlaces'] = $det['transactionCurrencyDecimalPlaces'];
                        $insertData[$j]['companyLocalCurrencyID'] = $det['companyLocalCurrencyID'];
                        $insertData[$j]['companyLocalCurrency'] = $det['companyLocalCurrency'];
                        $insertData[$j]['companyLocalAmount'] = $localAmount;
                        $insertData[$j]['companyLocalER'] = $det['companyLocalER'];
                        $insertData[$j]['companyLocalCurrencyDecimalPlaces'] = $det['companyLocalCurrencyDecimalPlaces'];
                        $insertData[$j]['companyReportingCurrencyID'] = $det['companyReportingCurrencyID'];
                        $insertData[$j]['companyReportingCurrency'] = $det['companyReportingCurrency'];
                        $insertData[$j]['companyReportingAmount'] = $reportingAmount;
                        $insertData[$j]['companyReportingER'] = $det['companyReportingER'];
                        $insertData[$j]['companyReportingCurrencyDecimalPlaces'] = $det['companyReportingCurrencyDecimalPlaces'];
                        $insertData[$j]['companyID'] = $companyID;
                        $insertData[$j]['companyCode'] = $companyCode;
                        $insertData[$j]['createdPCID'] = $createdPCID;
                        $insertData[$j]['createdUserGroup'] = $createdUserGroup;
                        $insertData[$j]['createdUserID'] = $createdUserID;
                        $insertData[$j]['createdUserName'] = $createdUserName;
                        $insertData[$j]['createdDateTime'] = $createdDateTime;

                        $j++;
                    }

                    $this->db->insert_batch($bankTrTB, $insertData);

                    if ($isNonPayroll != 'Y') {
                        $payrollDate = $payMonth['payrollYear'] . '-' . $payMonth['payrollMonth'] . '-01';
                        $splitSalary = $this->db->query("SELECT
                                            srp_erp_splitsalarymaster.splitSalaryMasterID,
                                            splitSalaryID,
                                            IFNULL( SUM(monthlyDeduction), 0 ) AS monthlyDeduction,
                                            bankMasterID
                                    FROM srp_erp_splitsalary
                                    JOIN srp_erp_splitsalarymaster ON srp_erp_splitsalarymaster.splitSalaryMasterID = srp_erp_splitsalary.splitSalaryMasterID 
                                    WHERE
                                        srp_erp_splitsalarymaster.approvedYN = 1
                                        AND empID = {$empID} 
                                        AND '{$payrollDate}' BETWEEN startDate AND srp_erp_splitsalarymaster.endDate
                                        GROUP BY empID,bankMasterID")->result_array();

                        if (!empty($splitSalary)) {
                            $split_total = 0;
                            foreach ($splitSalary as $val) {
                                $splitAccountAmount = $this->db->query("SELECT bankID, bankTransferDetailID, transactionAmount FROM srp_erp_pay_banktransfer WHERE empID = {$empID} AND bankID = {$val['bankMasterID']} AND payrollMasterID ={$payrollMasterID} ORDER BY salaryTransferPer ASC")->row_array();

                                if($splitAccountAmount) {
                                    $deductedAmount['transactionAmount'] = $splitAccountAmount['transactionAmount'] + $val['monthlyDeduction'];
                                    $this->db->where('empID', $empID);
                                    $this->db->where('bankID', $splitAccountAmount['bankID']);
                                    $this->db->where('bankTransferDetailID', $splitAccountAmount['bankTransferDetailID']);
                                    $this->db->update('srp_erp_pay_banktransfer', $deductedAmount);
                                } else {
                                    $bankdDet = $this->db->select('acc.bankID, bankCode, branchCode, bankName, acc.accountNo AS bnkAccountNo, accountHolderName, toBankPercentage, branchName, bankSwiftCode')
                                        ->from($salaryAccounts . ' AS acc')
                                        ->join('srp_erp_pay_bankmaster bnk', 'acc.bankID=bnk.bankID')
                                        ->join('srp_erp_pay_bankbranches brn', 'acc.branchID=brn.branchID')
                                        ->where('employeeNo', $empID)
                                        ->where('acc.bankID', $splitAccountAmount['bankID'])->get();
                                    $splitBankAcc = $bankdDet->row_array();

                                    $splitSalaryinsert['payrollMasterID'] = $payrollMasterID;
                                    $splitSalaryinsert['empID'] = $empID;
                                    $splitSalaryinsert['bankID'] = $splitAccountAmount['bankID'];

                                    $splitSalaryinsert['bankCode'] = $splitBankAcc['bankCode'];
                                    $splitSalaryinsert['bankName'] = $splitBankAcc['bankName'];
                                    $splitSalaryinsert['accountNo'] = $splitBankAcc['bnkAccountNo'];
                                    $splitSalaryinsert['swiftCode'] = $splitBankAcc['bankSwiftCode'];
                                    $splitSalaryinsert['branchCode'] = $splitBankAcc['branchCode'];
                                    $splitSalaryinsert['acc_holderName'] = $splitBankAcc['accountHolderName'];
                                    $splitSalaryinsert['branchName'] = $splitBankAcc['branchName'];

                                    $splitSalaryinsert['salaryTransferPer'] = 0;
                                    $splitSalaryinsert['transactionCurrencyID'] = $det['transactionCurrencyID'];
                                    $splitSalaryinsert['transactionCurrency'] = $det['transactionCurrency'];
                                    $splitSalaryinsert['transactionAmount'] = $val['monthlyDeduction'];
                                    $splitSalaryinsert['transactionER'] = $det['transactionER'];
                                    $splitSalaryinsert['transactionCurrencyDecimalPlaces'] = $det['transactionCurrencyDecimalPlaces'];
                                    $splitSalaryinsert['companyLocalCurrencyID'] = $det['companyLocalCurrencyID'];
                                    $splitSalaryinsert['companyLocalCurrency'] = $det['companyLocalCurrency'];
                                    $splitSalaryinsert['companyLocalAmount'] = $localAmount;
                                    $splitSalaryinsert['companyLocalER'] = $det['companyLocalER'];
                                    $splitSalaryinsert['companyLocalCurrencyDecimalPlaces'] = $det['companyLocalCurrencyDecimalPlaces'];
                                    $splitSalaryinsert['companyReportingCurrencyID'] = $det['companyReportingCurrencyID'];
                                    $splitSalaryinsert['companyReportingCurrency'] = $det['companyReportingCurrency'];
                                    $splitSalaryinsert['companyReportingAmount'] = $reportingAmount;
                                    $splitSalaryinsert['companyReportingER'] = $det['companyReportingER'];
                                    $splitSalaryinsert['companyReportingCurrencyDecimalPlaces'] = $det['companyReportingCurrencyDecimalPlaces'];
                                    $splitSalaryinsert['companyID'] = $companyID;
                                    $splitSalaryinsert['companyCode'] = $companyCode;
                                    $splitSalaryinsert['createdPCID'] = $createdPCID;
                                    $splitSalaryinsert['createdUserGroup'] = $createdUserGroup;
                                    $splitSalaryinsert['createdUserID'] = $createdUserID;
                                    $splitSalaryinsert['createdUserName'] = $createdUserName;
                                    $splitSalaryinsert['createdDateTime'] = $createdDateTime;
                                    $this->db->insert('srp_erp_pay_banktransfer', $splitSalaryinsert);
                                }

                                $split_total += $val['monthlyDeduction'];
                            }

                            $primaryAcc = $this->db->query("SELECT bankTransferDetailID, employeeNo, isPrimary, srp_erp_pay_salaryaccounts.bankID, transactionAmount
                                                FROM srp_erp_pay_salaryaccounts 
                                                JOIN srp_erp_pay_banktransfer ON srp_erp_pay_banktransfer.empID = srp_erp_pay_salaryaccounts.employeeNo AND srp_erp_pay_banktransfer.bankID = srp_erp_pay_salaryaccounts.bankID
                                                WHERE isPrimary = 1 AND employeeNo = {$empID} AND payrollMasterID = {$payrollMasterID} ORDER BY salaryTransferPer DESC")->row_array();

                            $deductedAmountPrimary['transactionAmount'] = $primaryAcc['transactionAmount'] - $split_total;
                            $this->db->where('empID', $empID);
                            $this->db->where('bankID', $primaryAcc['bankID']);
                            $this->db->where('bankTransferDetailID', $primaryAcc['bankTransferDetailID']);
                            $this->db->update('srp_erp_pay_banktransfer', $deductedAmountPrimary);
                        }
                    }
                }
                else {
                    $transferData = array(
                        'payrollMasterID' => $payrollMasterID,
                        'empID' => $empID,
                        'transactionCurrency' => $det['transactionCurrency'],
                        'transactionAmount' => $det['transactionAmount'],
                        'transactionER' => $det['transactionER'],
                        'transactionCurrencyDecimalPlaces' => $det['transactionCurrencyDecimalPlaces'],
                        'companyLocalCurrency' => $det['companyLocalCurrency'],
                        'companyLocalAmount' => $det['companyLocalAmount'],
                        'companyLocalER' => $det['companyLocalER'],
                        'companyLocalCurrencyDecimalPlaces' => $det['companyLocalCurrencyDecimalPlaces'],
                        'companyReportingCurrency' => $det['companyReportingCurrency'],
                        'companyReportingAmount' => $det['companyReportingAmount'],
                        'companyReportingER' => $det['companyReportingER'],
                        'companyReportingCurrencyDecimalPlaces' => $det['companyReportingCurrencyDecimalPlaces'],
                        'companyID' => $companyID,
                        'companyCode' => $companyCode,
                        'createdPCID' => $createdPCID,
                        'createdUserGroup' => $createdUserGroup,
                        'createdUserID' => $createdUserID,
                        'createdUserName' => $createdUserName,
                        'createdDateTime' => $createdDateTime
                    );
                    $this->db->insert($nonBankTrTB, $transferData);
                }
            }
        }


        $this->db->where('payrollMasterID', $payrollMasterID)->update($payrollTB, ['isBankTransferProcessed' => 1]);

        if($isInitial == 0){
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Bank transfer process failed');
            } else {
                $this->db->trans_commit();
                return array('s', 'Bank transfer process completed');
            }
        }

        return true;
    }

    function payroll_empWithoutBank($payrollMasterID, $isNonPayroll, $paidStatus = 0)
    {
        $orderBy = '';
        if ($isNonPayroll != 'Y') {
            $headerTB = 'srp_erp_payrollheaderdetails';
            $paymentTB = 'srp_erp_payroll_salarypayment_without_bank';
        } else {
            $headerTB = 'srp_erp_non_payrollheaderdetails';
            $paymentTB = 'srp_erp_non_payroll_salarypayment_without_bank';
        }
        if($paidStatus == 1)
        {
            $orderBy.='ORDER BY sal.payVoucherAutoId ASC';
        }else
        {
            $orderBy.='ORDER BY sal.transactionCurrency';
        }

        return $this->db->query("SELECT sal.empID, ECode, isPaid,sal.payVoucherAutoId,
                                 Ename2 AS empName,
                                 sal.transactionCurrency, sal.transactionAmount, sal.transactionCurrencyDecimalPlaces AS dPlace,
                                 payByBankID, sal.bankName, chequeNo,srp_erp_paymentvouchermaster.PVcode,notificationYN
                                 FROM {$paymentTB} AS sal
                                 JOIN {$headerTB} AS header ON sal.empID = header.EmpID
                                 LEFT JOIN srp_erp_paymentvouchermaster ON srp_erp_paymentvouchermaster.payVoucherAutoId = sal.payVoucherAutoId
                                 WHERE header.payrollMasterID={$payrollMasterID} AND sal.payrollMasterID={$payrollMasterID}
                                 AND isPaid = {$paidStatus}
                                 $orderBy
                                ")->result_array();
    }

    function isBankTransferredProcessed()
    {
        $payrollMasterID = $this->input->post('payrollID');

        $query = $this->db->query("SELECT payrollMasterID FROM srp_erp_pay_banktransfer WHERE payrollMasterID={$payrollMasterID} LIMIT 1");

        if ($query->row_array() == null) {
            return false;
        } else {
            return true;
        }
    }

    function payroll_bankTransferPendingData($payrollMasterID, $isNonPayroll)
    {
        //CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) AS empName
        if ($isNonPayroll != 'Y') {
            $bankTransfer = 'srp_erp_pay_banktransfer';
            $headerTB = 'srp_erp_payrollheaderdetails';
        } else {
            $bankTransfer = 'srp_erp_pay_non_banktransfer';
            $headerTB = 'srp_erp_non_payrollheaderdetails';
        }

        $companyID = current_companyID();
        $query = $this->db->query("SELECT bankTransferDetailID, trans.empID, ECode, trans.bankName,
                                    Ename2 AS empName,
                                    accountNo, acc_holderName, salaryTransferPer, trans.branchName, swiftCode, trans.transactionAmount, trans.transactionCurrency,
                                    trans.transactionCurrencyDecimalPlaces, trans.companyLocalCurrency, trans.companyLocalAmount, trans.companyLocalCurrencyDecimalPlaces
                                    FROM {$bankTransfer} AS trans
                                    JOIN {$headerTB} AS header ON trans.empID = header.EmpID AND header.companyID={$companyID}
                                    WHERE trans.payrollMasterID = {$payrollMasterID} AND header.payrollMasterID = {$payrollMasterID} AND bankTransferID IS NULL
                                    AND trans.companyID={$companyID}
                                    AND trans.transactionAmount > 0
                                    ORDER BY trans.bankName, trans.transactionCurrency");

        return $query->result_array();

    }

    function payroll_bankTransferPendingData_currencyWiseSum($payrollMasterID, $isNonPayroll)
    {

        $companyID = current_companyID();
        if ($isNonPayroll != 'Y') {
            $headerTB = 'srp_erp_payrollheaderdetails';
            $bankTrTB = 'srp_erp_pay_banktransfer';
        } else {
            $headerTB = 'srp_erp_non_payrollheaderdetails';
            $bankTrTB = 'srp_erp_pay_non_banktransfer';
        }
        $query = $this->db->query("SELECT  FORMAT(SUM(trans.transactionAmount) , trans.transactionCurrencyDecimalPlaces) AS trAmount, trans.transactionCurrency
                                   FROM {$bankTrTB} AS trans
                                   JOIN {$headerTB} AS header ON trans.empID = header.EmpID AND header.companyID={$companyID}
                                   WHERE trans.payrollMasterID = {$payrollMasterID} AND header.payrollMasterID={$payrollMasterID} AND bankTransferID IS NULL
                                   AND trans.companyID={$companyID}
                                   GROUP BY trans.transactionCurrencyID");
        //echo $this->db->last_query();

        return $query->result_array();
    }

    function processed_bankTransferData($payrollMasterID, $bankTransID, $isNonPayroll)
    {
        if ($isNonPayroll != 'Y') {
            $headerTB = 'srp_erp_payrollheaderdetails';
            $bankTrTB = 'srp_erp_pay_banktransfer';
        } else {
            $headerTB = 'srp_erp_non_payrollheaderdetails';
            $bankTrTB = 'srp_erp_pay_non_banktransfer';
        }

        $sortOrder_policy = getPolicyValues('PSO', 'All');
        $sortOrder = 'header.empID DESC';
        switch ($sortOrder_policy){
            case 1: $sortOrder = 'header.ECode ASC'; break;
            case 2: $sortOrder = 'header.ECode DESC'; break;
        }

        $query = $this->db->query("SELECT bankTransferDetailID, trans.empID, header.ECode, header.Ename2 AS empName, header.EmpShortCode AS name_in_bank_slip, trans.bankName,
                                    trans.accountNo, acc_holderName, salaryTransferPer, trans.branchName, trans.swiftCode, trans.transactionAmount, trans.transactionCurrency,
                                    trans.transactionCurrencyDecimalPlaces, trans.companyLocalCurrency, trans.companyLocalAmount, trans.bankID,
                                    trans.companyLocalCurrencyDecimalPlaces, trans.bankCode, trans.branchCode,EEmail,empdetail.Nid,acc.ibancode,header.segmentID
                                    FROM {$bankTrTB} AS trans
                                    JOIN {$headerTB} AS header ON trans.empID = header.EmpID    
                                    LEFT JOIN srp_employeesdetails empdetail ON empdetail.EIdNo = header.EmpID     
                                    LEFT JOIN srp_erp_pay_salaryaccounts as acc ON trans.empID = acc.employeeNo AND trans.bankID = acc.bankID                       
                                    WHERE trans.payrollMasterID = {$payrollMasterID} AND header.payrollMasterID = {$payrollMasterID} AND bankTransferID = {$bankTransID}
                                    ORDER BY trans.bankName, trans.transactionCurrency, {$sortOrder}");


        return $query->result_array();

    }



    function processed_bankTransferData_currencyWiseSum($payrollMasterID, $bankTransID)
    {
        $query = $this->db->query("SELECT  FORMAT(SUM(trans.transactionAmount) , trans.transactionCurrencyDecimalPlaces) trAmount, trans.transactionCurrency
                                   FROM srp_erp_pay_banktransfer AS trans
                                   JOIN srp_erp_payrollheaderdetails AS header ON trans.empID = header.EmpID
                                   WHERE trans.payrollMasterID = {$payrollMasterID} AND header.payrollMasterID = {$payrollMasterID}
                                   AND bankTransferID = {$bankTransID} GROUP BY trans.transactionCurrency");
        return $query->result_array();

    }

    function new_bankTransfer()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $companyCode = $this->common_data['company_data']['company_code'];
        $createdPCID = $this->common_data['current_pc'];
        $createdUserID = $this->common_data['current_userID'];
        $createdUserGroup = $this->common_data['user_group'];
        $createdUserName = $this->common_data['current_user'];
        $createdDateTime = $this->common_data['current_date'];

        $bnkPayrollID = $this->input->post('bnkPayrollID');
        $accountID = $this->input->post('accountID');
        $transDate = $this->input->post('transDate');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $transCheck = $this->input->post('transCheck[]');

        if ($isNonPayroll != 'Y') {
            $bankTransferTB = 'srp_erp_pay_banktransfermaster';
            $bankTransferDetTB = 'srp_erp_pay_banktransfer';
        } else {
            $bankTransferTB = 'srp_erp_pay_non_banktransfermaster';
            $bankTransferDetTB = 'srp_erp_pay_non_banktransfer';
        }

        $this->db->trans_start();

        //Generate document Code
        $lastDocumentNo = $this->db->query("SELECT documentNo FROM {$bankTransferTB} WHERE companyID={$companyID}
                                            ORDER BY bankTransferID DESC LIMIT 1")->row_array();
        $nextDocumentNo = ($lastDocumentNo['documentNo'] != null) ? $lastDocumentNo['documentNo'] + 1 : 1;
        $this->load->library('sequence');
        $nextDocumentCode = $this->sequence->sequence_generator('BTL', $nextDocumentNo);


        $query_Bank = $this->db->query("SELECT bankName, bankBranch, bankSwiftCode, bankAccountNumber FROM srp_erp_chartofaccounts WHERE GLAutoID={$accountID}");
        $bankDet = $query_Bank->row_array();

        $bankData = array(
            'payrollMasterID' => $bnkPayrollID,
            'documentCode' => $nextDocumentCode,
            'documentNo' => $nextDocumentNo,
            'accountID' => $accountID,
            'bankName' => $bankDet['bankName'],
            'branchName' => $bankDet['bankBranch'],
            'swiftCode' => $bankDet['bankSwiftCode'],
            'accountNo' => $bankDet['bankAccountNumber'],
            'transferDate' => $transDate,
            'companyID' => $companyID,
            'companyCode' => $companyCode,
            'createdPCID' => $createdPCID,
            'createdUserGroup' => $createdUserGroup,
            'createdUserID' => $createdUserID,
            'createdUserName' => $createdUserName,
            'createdDateTime' => $createdDateTime

        );


        $this->db->insert($bankTransferTB, $bankData);
        $masterID = $this->db->insert_id();

        foreach ($transCheck as $checkDet) {
            $updateDet = array(
                'bankTransferID' => $masterID,
                'modifiedPCID' => $createdPCID,
                'modifiedUserID' => $createdUserID,
                'modifiedUserName' => $createdUserName,
                'modifiedDateTime' => $createdDateTime
            );
            $this->db->where('bankTransferDetailID', $checkDet);
            $this->db->update($bankTransferDetTB, $updateDet);

        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Process Failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Successfully Processed');
        }
    }

    function get_bankTransferMasterDet($bankTransID, $isNonPayroll)
    {
        $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_pay_banktransfermaster' : 'srp_erp_pay_non_banktransfermaster';
        return $this->db->query("SELECT * FROM {$tableName} WHERE bankTransferID = {$bankTransID}")->row_array();
    }

    function pay_sheetBankTransferDet_delete()
    {
        $bankTransID = $this->input->post('bankTransID');
        $isNonPayroll = $this->input->post('isNonPayroll');

        $getDet = $this->get_bankTransferMasterDet($bankTransID, $isNonPayroll);

        if ($getDet['confirmedYN'] == 1) {
            return array('e', 'This bank transfer is confirmed, you can not delete this');
        } else {

            $bankTransferDet = ($isNonPayroll == 'Y') ? 'srp_erp_pay_non_banktransfer' : 'srp_erp_pay_banktransfer';
            $bankTransferMaster = ($isNonPayroll == 'Y') ? 'srp_erp_pay_non_banktransfermaster' : 'srp_erp_pay_banktransfermaster';

            $this->db->trans_start();

            $this->db->where('bankTransferID', $bankTransID);
            $this->db->update($bankTransferDet, array('bankTransferID' => null));

            $this->db->delete($bankTransferMaster, array('bankTransferID' => $bankTransID));

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Fail to delete bank transfer');
            } else {
                $this->db->trans_commit();
                return array('s', 'Successfully deleted');
            }
        }
    }

    function confirm_bankTransfer()
    {
        $bankTransID = $this->input->post('bankTransID');
        $letterDet = $this->input->post('letterDet');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $companyID = current_companyID();
        $this->form_validation->set_rules('bankTransID', 'Bank Transfer ID ', 'trim|required|numeric');
        $this->form_validation->set_rules('letterDet', 'Letter ', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            return array('e', validation_errors());
        }
        $getDet = $this->get_bankTransferMasterDet($bankTransID, $isNonPayroll);

        if ($getDet['confirmedYN'] == 1) {
            return array('e', '[' . $getDet['documentCode'] . ' ] is already confirmed');
        }

        $payrollProcessID = $this->db->query("SELECT payrollMasterID FROM `srp_erp_pay_banktransfermaster` where bankTransferID = $bankTransID")->row('payrollMasterID');
       
        $updateDet = array(
            'letterDet' => $letterDet,
            'confirmedYN' => 1,
            'confirmedByEmpID' => $this->common_data['current_userID'],
            'confirmedByName' => $this->common_data['current_user'],
            'confirmedDate' => $this->common_data['current_date'],
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $this->common_data['current_userID'],
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => $this->common_data['current_date']
        );
     
        $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_pay_banktransfermaster' : 'srp_erp_pay_non_banktransfermaster';
        $tableNamePRM = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        $tableNamePRD = ($isNonPayroll != 'Y') ? 'srp_erp_pay_banktransfer' : 'srp_erp_pay_non_banktransfer';
        $policy = getPolicyValues('BTPV', 'All');
        $companyID = current_companyID();



      
        $this->db->trans_start();

        if ($policy == 1 || $policy == null) {
            $is_auto_approve = get_document_auto_approval('PV');

            $bank_tr = $this->db->query("SELECT $tableName.*, $tableNamePRM.financialYearID, $tableNamePRM.financialPeriodID, fin_tb.beginingDate,
                                    fin_tb.endingDate, CONCAT( fin_tb.beginingDate, ' - ', fin_tb.endingDate ) AS companyFinanceYear,
                                    last_day(date(concat_ws('-', $tableNamePRM.payrollYear, $tableNamePRM.payrollMonth, 1))) as PVdate 
                                    FROM $tableName 
                                    LEFT JOIN $tableNamePRM ON $tableName.payrollMasterID = $tableNamePRM.payrollMasterID 
                                    LEFT JOIN srp_erp_companyfinanceyear AS fin_tb ON $tableNamePRM.financialYearID = fin_tb.companyFinanceYearID 
                                    WHERE bankTransferID = {$bankTransID}")->row_array();
            $companyID = current_companyID();
            $glDetailD = $this->db->query("SELECT coa.* FROM srp_erp_chartofaccounts coa
                                JOIN srp_erp_companycontrolaccounts cnt on coa.GLAutoID=cnt.GLAutoID
                                where coa.companyID=$companyID and cnt.controlAccountType='PCA'")->row_array();

            $tr_det = $this->db->query("SELECT bankTransferDetailID, bankTransferID, payrollMasterID, empID, bankID, bankCode, bankName, swiftCode, branchName, 
                                acc_holderName, accountNo, salaryTransferPer, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, 
                                SUM(transactionAmount) AS transactionAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, 
                                SUM(companyLocalAmount) AS companyLocalAmount, companyReportingCurrency, companyReportingCurrencyID, companyReportingER, companyReportingCurrencyDecimalPlaces, 
                                SUM(companyReportingAmount) AS companyReportingAmount, companyID, companyCode, segmentID, segmentCode 
                                FROM $tableNamePRD WHERE bankTransferID = {$bankTransID} GROUP BY transactionCurrencyID")->result_array();

            $this->load->library('approvals');
            $this->load->library('sequence');


                        foreach ($tr_det as $val) {                         
                            $payvoucherID = $this->db->query("SELECT
                                                              MAX(payVoucherAutoId) as payVoucherAutoId,
                                                              PVcode,
                                                              transactionCurrencyID
                                                              FROM 
                                                              `srp_erp_paymentvouchermaster` 
                                                              WHERE 
                                                              companyID = $companyID 
                                                              AND isDeleted = 1 
                                                              AND payrollMasterID = {$val['payrollMasterID']} 
                                                              AND transactionCurrencyID  = {$val['transactionCurrencyID']} 
                                                              AND bankGLAutoID = {$bank_tr['accountID']}
                                                              GROUP BY transactionCurrencyID")->row_array();
                            if(!empty($payvoucherID)){ 
                            $data['PVbankCode'] = trim($bank_tr['accountID'] ?? '');
                            $bank_detail = fetch_gl_account_desc($data['PVbankCode']);
                            $data['documentID'] = 'PV';
                            $data['companyFinanceYearID'] = trim($bank_tr['financialYearID'] ?? '');
                            $data['companyFinanceYear'] = trim($bank_tr['companyFinanceYear'] ?? '');
                            $data['FYBegin'] = trim($bank_tr['beginingDate'] ?? '');
                            $data['FYEnd'] = trim($bank_tr['endingDate'] ?? '');
                            $data['companyFinancePeriodID'] = trim($bank_tr['financialPeriodID'] ?? '');
                            /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
                            $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
                            $data['PVdate'] = trim($bank_tr['transferDate'] ?? '');
                            $data['PVNarration'] = 'Bank transfer ' . $bank_tr['documentCode'];
                            $data['accountPayeeOnly'] = 0;
                            /* $data['segmentID'] = trim($segment[0] ?? '');
                             $data['segmentCode'] = trim($segment[1] ?? '');*/
                            $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
                            $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
                            $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
                            $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
                            $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
                            $data['PVbank'] = $bank_detail['bankName'];
                            $data['PVbankBranch'] = $bank_detail['bankBranch'];
                            $data['PVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
                            $data['PVbankAccount'] = $bank_detail['bankAccountNumber'];
                            $data['PVbankType'] = $bank_detail['subCategory'];
                            $data['isDeleted'] = 0;
                            /*if ($bank_detail['isCash'] == 1) {
                                $data['PVchequeNo'] = null;
                                $data['PVchequeDate'] = null;
                            } else {
                                $data['PVchequeNo'] = trim($this->input->post('PVchequeNo') ?? '');
                                $data['PVchequeDate'] = trim($PVchequeDate);
                            }*/
                            $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
                            $data['pvType'] = 'Direct';
                            $data['isSytemGenerated'] = '1';
                            $data['referenceNo'] = $bank_tr['documentCode'];
                            $data['modifiedPCID'] = $this->common_data['current_pc'];
                            $data['modifiedUserID'] = $this->common_data['current_userID'];
                            $data['modifiedUserName'] = $this->common_data['current_user'];
                            $data['modifiedDateTime'] = $this->common_data['current_date'];
                            $data['transactionCurrencyID'] = trim($val['transactionCurrencyID'] ?? '');
                            $data['transactionCurrency'] = trim($val['transactionCurrency'] ?? '');
                            $data['transactionExchangeRate'] = $val['transactionER'];
                            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
                            $data['companyLocalCurrencyID'] = $val['companyLocalCurrencyID'];
                            $data['companyLocalCurrency'] = $val['companyLocalCurrency'];
                            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
                            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
                            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                            $data['companyReportingCurrency'] = $val['companyReportingCurrency'];
                            $data['companyReportingCurrencyID'] = $val['companyReportingCurrencyID'];
                            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
                            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                            $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
                            $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                            $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
                            $data['partyType'] = 'DIR';
                            $data['partyName'] = trim($this->input->post('partyName') ?? '');
                            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
                            $data['partyCurrency'] = $data['companyLocalCurrency'];
                            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
                            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
                            $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
                            $data['partyExchangeRate'] = $partyCurrency['conversion'];
                            $data['bankTransferID'] = $bankTransID;
            
                            $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];
            
                            $data['companyCode'] = $this->common_data['company_data']['company_code'];
                            $data['companyID'] = $this->common_data['company_data']['company_id'];
                            $data['createdUserGroup'] = $this->common_data['user_group'];
                            $data['createdPCID'] = $this->common_data['current_pc'];
                            $data['createdUserID'] = $this->common_data['current_userID'];
                            $data['createdUserName'] = $this->common_data['current_user'];
                            $data['createdDateTime'] = $this->common_data['current_date'];
                            $data['confirmedYN'] = 1;
                            $data['confirmedByEmpID'] = $this->common_data['current_userID'];
                            $data['confirmedByName'] = $this->common_data['current_user'];
                            $data['confirmedDate'] = $this->common_data['current_date'];
            
                            $type = substr($data['pvType'], 0, 3);
                            //$data['PVcode'] = $this->sequence->sequence_generator($data['documentID']);
                            $invYear = date("Y", strtotime($data['PVdate']));
                            $invMonth = date("m", strtotime($data['PVdate']));
                            //$data['PVcode'] = $this->sequence->sequence_generator_fin($data['documentID'], $data['companyFinanceYearID'], $invYear, $invMonth);
                            
                            $this->db->where('payVoucherAutoId', $payvoucherID['payVoucherAutoId']);
                            $this->db->update('srp_erp_paymentvouchermaster', $data);
                            $last_id = $payvoucherID['payVoucherAutoId'];
            
            
                            $dataD['payVoucherAutoId'] = $last_id;
                            $dataD['type'] = 'GL';
                            $dataD['referenceNo'] = $bank_tr['documentCode'];
                            $dataD['GLAutoID'] = $glDetailD['GLAutoID'];
                            $dataD['systemGLCode'] = $glDetailD['systemAccountCode'];
                            $dataD['GLCode'] = $glDetailD['GLSecondaryCode'];
                            $dataD['GLDescription'] = $glDetailD['GLDescription'];
                            $dataD['GLType'] = $glDetailD['subCategory'];
                            $dataD['description'] = 'Bank transfer ' . $bank_tr['documentCode'];
                            $dataD['transactionCurrencyID'] = $data['transactionCurrencyID'];
                            $dataD['transactionCurrency'] = $data['transactionCurrency'];
                            $dataD['transactionExchangeRate'] = $data['transactionExchangeRate'];
                            $dataD['transactionAmount'] = $val['transactionAmount'];
                            $dataD['transactionCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
                            $dataD['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
                            $dataD['companyLocalCurrency'] = $data['companyLocalCurrency'];
                            $dataD['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];
                            //$dataD['companyLocalAmount'] = $dataD['transactionAmount']/$dataD['companyLocalExchangeRate'];
                            $dataD['companyLocalAmount'] = round(($dataD['transactionAmount'] / $dataD['companyLocalExchangeRate']), $data['companyLocalCurrencyDecimalPlaces']);
                            $dataD['companyLocalCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
                            $dataD['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];
                            $dataD['companyReportingCurrency'] = $data['companyReportingCurrency'];
                            $dataD['companyReportingExchangeRate'] = $data['companyReportingExchangeRate'];
                            //$dataD['companyReportingAmount'] = $dataD['transactionAmount']/$dataD['companyReportingExchangeRate'];
                            $dataD['companyReportingAmount'] = round(($dataD['transactionAmount'] / $dataD['companyReportingExchangeRate']), $data['companyReportingCurrencyDecimalPlaces']);
                            $dataD['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces'];
                            $dataD['segmentID'] = $val['segmentID'];
                            $dataD['segmentCode'] = $val['segmentCode'];
                            $dataD['companyID'] = $val['companyID'];
                            $dataD['companyCode'] = $val['companyCode'];
                            $dataD['createdUserGroup'] = $this->common_data['user_group'];
                            $dataD['createdPCID'] = $this->common_data['current_pc'];
                            $dataD['createdUserID'] = $this->common_data['current_userID'];
                            $dataD['createdUserName'] = $this->common_data['current_user'];
                            $dataD['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_paymentvoucherdetail', $dataD);
            
                            if ($is_auto_approve == 0) {
                                $approvals_status = $this->approvals->auto_approve($last_id, 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $payvoucherID['PVcode'], $data['PVdate']);
            
                                if($approvals_status == 1){
                                    $this->bank_transfer_pv_auto_approvals($last_id);
                                }
                                else{
                                    $this->db->trans_rollback();
                                    return array('e', 'Fail to confirm [' . $getDet['documentCode'] . ' ].<br/>Error in payment voucher auto approvals.');
                                }
                            }
                            else{
                                $approvals_status = $this->approvals->CreateApproval('PV', $last_id, $payvoucherID['PVcode'], 'Payment Voucher', 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 0, $data['PVdate'], 0, $data['PVdate']);
            
                                if ($approvals_status == 3) {
                                    $this->db->trans_rollback();
                                    return ['w', 'There are no users exist to perform \'payment voucher\' approval for this company.'];
                                }
                                else if($approvals_status != 1){
                                    $this->db->trans_rollback();
                                    return ['e', 'Fail to confirm [' . $getDet['documentCode'] . ' ].<br/>Error in payment voucher auto approvals.'];
                                }
                            }
                        }else { 
                            $data['PVbankCode'] = trim($bank_tr['accountID'] ?? '');
                            $bank_detail = fetch_gl_account_desc($data['PVbankCode']);
                            $data['documentID'] = 'PV';
                            $data['companyFinanceYearID'] = trim($bank_tr['financialYearID'] ?? '');
                            $data['companyFinanceYear'] = trim($bank_tr['companyFinanceYear'] ?? '');
                            $data['FYBegin'] = trim($bank_tr['beginingDate'] ?? '');
                            $data['FYEnd'] = trim($bank_tr['endingDate'] ?? '');
                            $data['companyFinancePeriodID'] = trim($bank_tr['financialPeriodID'] ?? '');
                            /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
                            $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
                            $data['PVdate'] = trim($bank_tr['transferDate'] ?? '');
                            $data['PVNarration'] = 'Bank transfer ' . $bank_tr['documentCode'];
                            $data['accountPayeeOnly'] = 0;
                            /* $data['segmentID'] = trim($segment[0] ?? '');
                             $data['segmentCode'] = trim($segment[1] ?? '');*/
                            $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
                            $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
                            $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
                            $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
                            $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
                            $data['PVbank'] = $bank_detail['bankName'];
                            $data['PVbankBranch'] = $bank_detail['bankBranch'];
                            $data['PVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
                            $data['PVbankAccount'] = $bank_detail['bankAccountNumber'];
                            $data['PVbankType'] = $bank_detail['subCategory'];
                            /*if ($bank_detail['isCash'] == 1) {
                                $data['PVchequeNo'] = null;
                                $data['PVchequeDate'] = null;
                            } else {
                                $data['PVchequeNo'] = trim($this->input->post('PVchequeNo') ?? '');
                                $data['PVchequeDate'] = trim($PVchequeDate);
                            }*/
                            $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
                            $data['pvType'] = 'Direct';
                            $data['isSytemGenerated'] = '1';
                            $data['referenceNo'] = $bank_tr['documentCode'];
                            $data['modifiedPCID'] = $this->common_data['current_pc'];
                            $data['modifiedUserID'] = $this->common_data['current_userID'];
                            $data['modifiedUserName'] = $this->common_data['current_user'];
                            $data['modifiedDateTime'] = $this->common_data['current_date'];
                            $data['transactionCurrencyID'] = trim($val['transactionCurrencyID'] ?? '');
                            $data['transactionCurrency'] = trim($val['transactionCurrency'] ?? '');
                            $data['transactionExchangeRate'] = $val['transactionER'];
                            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
                            $data['companyLocalCurrencyID'] = $val['companyLocalCurrencyID'];
                            $data['companyLocalCurrency'] = $val['companyLocalCurrency'];
                            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
                            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
                            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                            $data['companyReportingCurrency'] = $val['companyReportingCurrency'];
                            $data['companyReportingCurrencyID'] = $val['companyReportingCurrencyID'];
                            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
                            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                            $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
                            $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                            $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
                            $data['partyType'] = 'DIR';
                            $data['partyName'] = trim($this->input->post('partyName') ?? '');
                            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
                            $data['partyCurrency'] = $data['companyLocalCurrency'];
                            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
                            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
                            $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
                            $data['partyExchangeRate'] = $partyCurrency['conversion'];
                            $data['bankTransferID'] = $bankTransID;
            
                            $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];
            
                            $data['companyCode'] = $this->common_data['company_data']['company_code'];
                            $data['companyID'] = $this->common_data['company_data']['company_id'];
                            $data['createdUserGroup'] = $this->common_data['user_group'];
                            $data['createdPCID'] = $this->common_data['current_pc'];
                            $data['createdUserID'] = $this->common_data['current_userID'];
                            $data['createdUserName'] = $this->common_data['current_user'];
                            $data['createdDateTime'] = $this->common_data['current_date'];
                            $data['confirmedYN'] = 1;
                            $data['confirmedByEmpID'] = $this->common_data['current_userID'];
                            $data['confirmedByName'] = $this->common_data['current_user'];
                            $data['confirmedDate'] = $this->common_data['current_date'];
            
                            $type = substr($data['pvType'], 0, 3);
                            //$data['PVcode'] = $this->sequence->sequence_generator($data['documentID']);
                            $invYear = date("Y", strtotime($data['PVdate']));
                            $invMonth = date("m", strtotime($data['PVdate']));
                            $data['PVcode'] = $this->sequence->sequence_generator_fin($data['documentID'], $data['companyFinanceYearID'], $invYear, $invMonth);
            
                            $result = $this->db->insert('srp_erp_paymentvouchermaster', $data);
                            $last_id = $this->db->insert_id();
            
            
                            $dataD['payVoucherAutoId'] = $last_id;
                            $dataD['type'] = 'GL';
                            $dataD['referenceNo'] = $bank_tr['documentCode'];
                            $dataD['GLAutoID'] = $glDetailD['GLAutoID'];
                            $dataD['systemGLCode'] = $glDetailD['systemAccountCode'];
                            $dataD['GLCode'] = $glDetailD['GLSecondaryCode'];
                            $dataD['GLDescription'] = $glDetailD['GLDescription'];
                            $dataD['GLType'] = $glDetailD['subCategory'];
                            $dataD['description'] = 'Bank transfer ' . $bank_tr['documentCode'];
                            $dataD['transactionCurrencyID'] = $data['transactionCurrencyID'];
                            $dataD['transactionCurrency'] = $data['transactionCurrency'];
                            $dataD['transactionExchangeRate'] = $data['transactionExchangeRate'];
                            $dataD['transactionAmount'] = $val['transactionAmount'];
                            $dataD['transactionCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
                            $dataD['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
                            $dataD['companyLocalCurrency'] = $data['companyLocalCurrency'];
                            $dataD['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];
                            //$dataD['companyLocalAmount'] = $dataD['transactionAmount']/$dataD['companyLocalExchangeRate'];
                            $dataD['companyLocalAmount'] = round(($dataD['transactionAmount'] / $dataD['companyLocalExchangeRate']), $data['companyLocalCurrencyDecimalPlaces']);
                            $dataD['companyLocalCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
                            $dataD['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];
                            $dataD['companyReportingCurrency'] = $data['companyReportingCurrency'];
                            $dataD['companyReportingExchangeRate'] = $data['companyReportingExchangeRate'];
                            //$dataD['companyReportingAmount'] = $dataD['transactionAmount']/$dataD['companyReportingExchangeRate'];
                            $dataD['companyReportingAmount'] = round(($dataD['transactionAmount'] / $dataD['companyReportingExchangeRate']), $data['companyReportingCurrencyDecimalPlaces']);
                            $dataD['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces'];
                            $dataD['segmentID'] = $val['segmentID'];
                            $dataD['segmentCode'] = $val['segmentCode'];
                            $dataD['companyID'] = $val['companyID'];
                            $dataD['companyCode'] = $val['companyCode'];
                            $dataD['createdUserGroup'] = $this->common_data['user_group'];
                            $dataD['createdPCID'] = $this->common_data['current_pc'];
                            $dataD['createdUserID'] = $this->common_data['current_userID'];
                            $dataD['createdUserName'] = $this->common_data['current_user'];
                            $dataD['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_paymentvoucherdetail', $dataD);
            
                            if ($is_auto_approve == 0) {
                                $approvals_status = $this->approvals->auto_approve($last_id, 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $data['PVcode'], $data['PVdate']);
            
                                if($approvals_status == 1){
                                    $this->bank_transfer_pv_auto_approvals($last_id);
                                }
                                else{
                                    $this->db->trans_rollback();
                                    return array('e', 'Fail to confirm [' . $getDet['documentCode'] . ' ].<br/>Error in payment voucher auto approvals.');
                                }
                            }
                            else{
                                $approvals_status = $this->approvals->CreateApproval('PV', $last_id, $data['PVcode'], 'Payment Voucher', 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 0, $data['PVdate'], 0, $data['PVdate']);
            
                                if ($approvals_status == 3) {
                                    $this->db->trans_rollback();
                                    return ['w', 'There are no users exist to perform \'payment voucher\' approval for this company.'];
                                }
                                else if($approvals_status != 1){
                                    $this->db->trans_rollback();
                                    return ['e', 'Fail to confirm [' . $getDet['documentCode'] . ' ].<br/>Error in payment voucher auto approvals.'];
                                }
                            }
                        }
                    }

                   
     
               
            

            
        }

        $this->db->where('bankTransferID', $bankTransID)->update($tableName, $updateDet);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Fail to confirm [' . $getDet['documentCode'] . ' ]');
        } else {
            $this->db->trans_commit();
            return array('s', 'Successfully confirmed [' . $getDet['documentCode'] . ' ]');
        }
    }

    function bank_transfer_pv_auto_approvals($pv_auto_id){
        $this->load->model('Double_entry_model');
        $ledger_arr = array();
        $double_entry = $this->Double_entry_model->fetch_double_entry_payment_voucher_data($pv_auto_id, 'PV');
        for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
            $ledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
            $ledger_arr[$i]['documentCode'] = $double_entry['code'];
            $ledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['PVcode'];
            $ledger_arr[$i]['documentDate'] = $double_entry['master_data']['PVdate'];
            $ledger_arr[$i]['documentType'] = $double_entry['master_data']['pvType'];
            $ledger_arr[$i]['documentYear'] = $double_entry['master_data']['PVdate'];
            $ledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['PVdate']));
            $ledger_arr[$i]['documentNarration'] = $double_entry['master_data']['PVNarration'];
            $ledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['PVchequeNo'];
            $ledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
            $ledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
            $ledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
            $ledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
            $ledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
            $ledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
            $ledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
            $ledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
            $ledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
            $ledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
            $ledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
            $ledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
            $ledger_arr[$i]['partyContractID'] = '';
            $ledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
            $ledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
            $ledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
            $ledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
            $ledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
            $ledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
            $ledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
            $ledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
            $ledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
            $ledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
            $ledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
            $ledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
            $ledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
            $ledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
            $ledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
            $ledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
            $amount = $double_entry['gl_detail'][$i]['gl_dr'];
            if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
            }
            $ledger_arr[$i]['transactionAmount'] = round($amount, $ledger_arr[$i]['transactionCurrencyDecimalPlaces']);
            $ledger_arr[$i]['companyLocalAmount'] = round(($ledger_arr[$i]['transactionAmount'] / $ledger_arr[$i]['companyLocalExchangeRate']), $ledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
            $ledger_arr[$i]['companyReportingAmount'] = round(($ledger_arr[$i]['transactionAmount'] / $ledger_arr[$i]['companyReportingExchangeRate']), $ledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
            $ledger_arr[$i]['partyCurrencyAmount'] = round(($ledger_arr[$i]['transactionAmount'] / $ledger_arr[$i]['partyExchangeRate']), $ledger_arr[$i]['partyCurrencyDecimalPlaces']);
            $ledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
            $ledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
            $ledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
            $ledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
            $ledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
            $ledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
            $ledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
            $ledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
            $ledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
            $ledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
            $ledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
            $ledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
            $ledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
            $ledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
            $ledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $ledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
            $ledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
            $ledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
            $ledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
            $ledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
            $ledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
            $ledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
            $ledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
        }

        $amount = payment_voucher_total_value($double_entry['master_data']['payVoucherAutoId'], $double_entry['master_data']['transactionCurrencyDecimalPlaces'], 0);
        $bankledger_arr['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
        $bankledger_arr['documentDate'] = $double_entry['master_data']['PVdate'];
        $bankledger_arr['transactionType'] = 2;
        $bankledger_arr['bankName'] = $double_entry['master_data']['PVbank'];
        $bankledger_arr['bankGLAutoID'] = $double_entry['master_data']['bankGLAutoID'];
        $bankledger_arr['bankSystemAccountCode'] = $double_entry['master_data']['bankSystemAccountCode'];
        $bankledger_arr['bankGLSecondaryCode'] = $double_entry['master_data']['bankGLSecondaryCode'];
        $bankledger_arr['documentType'] = 'PV';
        $bankledger_arr['documentSystemCode'] = $double_entry['master_data']['PVcode'];
        $bankledger_arr['modeofPayment'] = $double_entry['master_data']['modeOfPayment'];
        $bankledger_arr['chequeNo'] = $double_entry['master_data']['PVchequeNo'];
        $bankledger_arr['chequeDate'] = $double_entry['master_data']['PVchequeDate'];
        $bankledger_arr['memo'] = $double_entry['master_data']['PVNarration'];
        $bankledger_arr['partyType'] = $double_entry['master_data']['partyType'];
        $bankledger_arr['partyAutoID'] = $double_entry['master_data']['partyID'];
        $bankledger_arr['partyCode'] = $double_entry['master_data']['partyCode'];
        $bankledger_arr['partyName'] = $double_entry['master_data']['partyName'];
        $bankledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
        $bankledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
        $bankledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
        $bankledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
        $bankledger_arr['transactionAmount'] = $amount;
        $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['partyCurrencyID'];
        $bankledger_arr['partyCurrency'] = $double_entry['master_data']['partyCurrency'];
        $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['partyExchangeRate'];
        $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['partyCurrencyDecimalPlaces'];
        $bankledger_arr['partyCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['partyCurrencyExchangeRate']);
        $bankledger_arr['bankCurrencyID'] = $double_entry['master_data']['bankCurrencyID'];
        $bankledger_arr['bankCurrency'] = $double_entry['master_data']['bankCurrency'];
        $bankledger_arr['bankCurrencyExchangeRate'] = $double_entry['master_data']['bankCurrencyExchangeRate'];
        $bankledger_arr['bankCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['bankCurrencyExchangeRate']);
        $bankledger_arr['bankCurrencyDecimalPlaces'] = $double_entry['master_data']['bankCurrencyDecimalPlaces'];
        $bankledger_arr['companyID'] = $double_entry['master_data']['companyID'];
        $bankledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
        $bankledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
        $bankledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
        $bankledger_arr['createdPCID'] = $this->common_data['current_pc'];
        $bankledger_arr['createdUserID'] = $this->common_data['current_userID'];
        $bankledger_arr['createdDateTime'] = $this->common_data['current_date'];
        $bankledger_arr['createdUserName'] = $this->common_data['current_user'];
        $bankledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
        $bankledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
        $bankledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
        $bankledger_arr['modifiedUserName'] = $this->common_data['current_user'];

        $this->db->insert('srp_erp_bankledger', $bankledger_arr);

        if (!empty($ledger_arr)) {
            $this->db->insert_batch('srp_erp_generalledger', $ledger_arr);
            $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, 
                                             sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
            $this->db->where('documentCode', 'PV');
            $this->db->where('documentMasterAutoID', $pv_auto_id);
            $totals = $this->db->get('srp_erp_generalledger')->row_array();
            if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                $generalledger_arr = array();
                $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                $ERGL = fetch_gl_account_desc($ERGL_ID);
                $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
                $generalledger_arr['documentCode'] = $double_entry['code'];
                $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['PVcode'];
                $generalledger_arr['documentDate'] = $double_entry['master_data']['PVdate'];
                $generalledger_arr['documentType'] = $double_entry['master_data']['pvType'];
                $generalledger_arr['documentYear'] = $double_entry['master_data']['PVdate'];
                $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['PVdate']));
                $generalledger_arr['documentNarration'] = $double_entry['master_data']['PVNarration'];
                $generalledger_arr['chequeNumber'] = $double_entry['master_data']['PVchequeNo'];
                $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr['partyContractID'] = '';
                $generalledger_arr['partyType'] = $double_entry['master_data']['partyType'];
                $generalledger_arr['partyAutoID'] = $double_entry['master_data']['partyID'];
                $generalledger_arr['partySystemCode'] = $double_entry['master_data']['partyCode'];
                $generalledger_arr['partyName'] = $double_entry['master_data']['partyName'];
                $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['partyCurrencyID'];
                $generalledger_arr['partyCurrency'] = $double_entry['master_data']['partyCurrency'];
                $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['partyExchangeRate'];
                $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['partyCurrencyDecimalPlaces'];
                $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                $generalledger_arr['transactionAmount'] = round(($totals['transaction_total'] * -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total'] * -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total'] * -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total'] * -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                $generalledger_arr['amount_type'] = null;
                $generalledger_arr['documentDetailAutoID'] = 0;
                $generalledger_arr['GLAutoID'] = $ERGL_ID;
                $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                $generalledger_arr['GLType'] = $ERGL['subCategory'];
                $generalledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                $generalledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                $generalledger_arr['subLedgerType'] = 0;
                $generalledger_arr['subLedgerDesc'] = null;
                $generalledger_arr['isAddon'] = 0;
                $generalledger_arr['createdUserGroup'] = $this->common_data['user_group'];
                $generalledger_arr['createdPCID'] = $this->common_data['current_pc'];
                $generalledger_arr['createdUserID'] = $this->common_data['current_userID'];
                $generalledger_arr['createdDateTime'] = $this->common_data['current_date'];
                $generalledger_arr['createdUserName'] = $this->common_data['current_user'];
                $generalledger_arr['modifiedPCID'] = $this->common_data['current_pc'];
                $generalledger_arr['modifiedUserID'] = $this->common_data['current_userID'];
                $generalledger_arr['modifiedDateTime'] = $this->common_data['current_date'];
                $generalledger_arr['modifiedUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_generalledger', $generalledger_arr);
            }
        }
    }

    function getTotalBankTransferAmount($bankTransferID, $isNonPayroll)
    {
        $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_pay_banktransfer' : 'srp_erp_pay_non_banktransfer';
        return $this->db->query("SELECT companyLocalCurrency lo_currency, companyLocalCurrencyDecimalPlaces lo_dPlace,
                                 sum(companyLocalAmount) lo_amount
                                 FROM {$tableName} WHERE bankTransferID={$bankTransferID} ")->row_array();
    }

    function get_month_name($inp)
    {
        return date("F", strtotime(date("d-$inp-y")));
    }

    function get_empPaySlipDet($empID, $payrollID, $isNonPayroll)
    {
        $companyID = current_companyID();
        if ($isNonPayroll != 'Y') {
            $headerTB = 'srp_erp_payrollheaderdetails';
            $payDetailTB = 'srp_erp_payrolldetail';
            $bankTrTB = 'srp_erp_pay_banktransfer';
            $withoutBankTB = 'srp_erp_payroll_salarypayment_without_bank';
        } else {
            $headerTB = 'srp_erp_non_payrollheaderdetails';
            $payDetailTB = 'srp_erp_non_payrolldetail';
            $bankTrTB = 'srp_erp_pay_non_banktransfer';
            $withoutBankTB = 'srp_erp_non_payroll_salarypayment_without_bank';
        }

        $headerDet = $this->db->query("SELECT Ename2 AS empName, Designation, secondaryCode, DOJ,gradeDescription,
                                        if(transactionCurrency = null , transactionCurrency, payCurrency) AS transactionCurrency,
                                        if(transactionCurrencyDecimalPlaces = null, transactionCurrencyDecimalPlaces,
                                        (SELECT DecimalPlaces FROM srp_erp_currencymaster WHERE CurrencyCode = payCurrency )) AS dPlace
                                        FROM {$headerTB} 
                                        left JOIN srp_erp_employeegrade ON srp_erp_employeegrade.gradeID = {$headerTB}.gradeID
                                        WHERE payrollMasterID={$payrollID} AND EmpID={$empID} AND
                                        {$headerTB}.companyID={$companyID}")->row_array();

        if(true){
            $systemTypeID = 2; //division
        } else{
            $systemTypeID = 6; //department
        }                                

        $headerDet2 = $this->db->query("SELECT srp_erp_payroll_employee_reporting_structure.*,srp_erp_reporting_structure_details.detail_code, srp_erp_reporting_structure_details.detail_description
                        FROM srp_erp_payroll_employee_reporting_structure
                        JOIN srp_erp_reporting_structure_details ON srp_erp_reporting_structure_details.id = srp_erp_payroll_employee_reporting_structure.reportingStructureDetailID
                        JOIN srp_erp_reporting_structure_master ON srp_erp_payroll_employee_reporting_structure.reportingStructureID = srp_erp_reporting_structure_master.id 
                        WHERE
                        srp_erp_reporting_structure_master.systemTypeID = {$systemTypeID} AND payrollID = {$payrollID} AND empID = {$empID}")->row_array();

        $headerDet_grade = $this->db->query("SELECT srp_erp_employeegrade.gradeDescription
                            FROM srp_employeesdetails
                            JOIN srp_erp_employeegrade ON srp_erp_employeegrade.gradeID = srp_employeesdetails.gradeID 
                            WHERE
                            EIdNo = {$empID}")->row_array();
                            

        $date_info = $this->db->query("SELECT srp_erp_payrollmaster.payrollMonth,srp_erp_payrollmaster.payrollYear,srp_erp_payrollheaderdetails.totDayAbsent
                        FROM srp_erp_payrollmaster  
                        LEFT JOIN srp_erp_payrollheaderdetails ON srp_erp_payrollheaderdetails.payrollMasterID =   srp_erp_payrollmaster.payrollMasterID               
                        WHERE
                        srp_erp_payrollmaster.payrollMasterID = {$payrollID}")->row_array();

        
        $dt = "{$date_info['payrollYear']}-{$date_info['payrollMonth']}-23"; // Define the date string
        $start_dt = date("Y-m-01", strtotime($dt));
        $end_dt = date("Y-m-t", strtotime($dt));

        $days_between = (strtotime($end_dt) - strtotime($start_dt)) / (60 * 60 * 24); //get days count
        $working_days = ($days_between - $date_info['totDayAbsent']);
        //echo $working_days;

        $payslip_date_info = array(
            'start_dt' => $start_dt,
            'end_dt' => $end_dt,
            'paid_days' => $working_days,
            'absent_days' => $date_info['totDayAbsent']
        );


        //salary Declarations
        $salaryDec_A = $this->db->query("SELECT salaryDescription, detailType, sum(pay.transactionAmount) AS transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace, fromTB,pay.salCatID,cat.salaryCategoryID AS salaryCategoryID
                                        FROM  {$payDetailTB} AS pay
                                        JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                        WHERE payrollMasterID={$payrollID} AND EmpID={$empID} AND 
                                        ( fromTB = 'SD' OR  fromTB = 'VP' OR fromTB = 'BP' OR fromTB = 'EC' OR (fromTB = 'OT' AND transactionAmount > 0) ) 
                                        AND detailType = 'A' AND transactionAmount != 0  AND pay.companyID={$companyID} 
                                        GROUP BY pay.salCatID
                                        HAVING transactionAmount != 0")->result_array();

        $salaryDec_D = $this->db->query("SELECT salaryDescription, detailType, sum(pay.transactionAmount) AS transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace
                                        FROM  {$payDetailTB} AS pay
                                        JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                        WHERE payrollMasterID={$payrollID} AND EmpID={$empID}
                                        AND (fromTB = 'SD' OR  fromTB = 'NO-PAY') AND detailType = 'D'  AND transactionAmount != 0 AND pay.companyID={$companyID}
                                        GROUP BY pay.salCatID
                                        UNION ALL
                                        SELECT salaryDescription, detailType, sum(pay.transactionAmount) AS transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace
                                        FROM  {$payDetailTB} AS pay
                                        JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                        WHERE payrollMasterID={$payrollID} AND EmpID={$empID}
                                        AND fromTB = 'OT' AND detailType = 'A'  AND transactionAmount < 0 AND pay.companyID={$companyID}
                                        GROUP BY pay.salCatID 
                                        HAVING transactionAmount != 0")->result_array();

        //Monthly Addition
        $monthAdd = $this->db->query("SELECT monthlyDeclaration AS description, detailType, pay.transactionAmount, pay.transactionCurrencyDecimalPlaces AS dPlace
                                        FROM  {$payDetailTB} AS pay
                                        JOIN srp_erp_pay_monthlyadditiondetail AS mAdd ON mAdd.monthlyAdditionDetailID = pay.detailTBID
                                        JOIN srp_erp_pay_monthlydeclarationstypes AS monDec ON monDec.monthlyDeclarationID = mAdd.declarationID
                                        WHERE payrollMasterID={$payrollID} AND pay.EmpID={$empID} AND pay.companyID={$companyID}
                                        AND fromTB = 'MA' ")->result_array();

        //Monthly Deduction
        $monthDec = $this->db->query("SELECT monthlyDeclaration AS description, detailType, pay.transactionAmount, pay.transactionCurrencyDecimalPlaces AS dPlace
                                        FROM  {$payDetailTB} AS pay
                                        JOIN srp_erp_pay_monthlydeductiondetail AS mDed ON mDed.monthlyDeductionDetailID = pay.detailTBID
                                        JOIN srp_erp_pay_monthlydeclarationstypes AS monDec ON monDec.monthlyDeclarationID = mDed.declarationID
                                        WHERE payrollMasterID={$payrollID} AND pay.EmpID={$empID} AND pay.companyID={$companyID}
                                        AND fromTB = 'MD' ")->result_array();

        //SSO Payee
        $sso_payee = $this->db->query("SELECT grMaster.description, detailType, pay.transactionAmount, pay.transactionCurrencyDecimalPlaces AS dPlace
                                        FROM  {$payDetailTB} AS pay
                                        JOIN srp_erp_paygroupmaster AS grMaster ON grMaster.payGroupID = pay.detailTBID
                                        LEFT JOIN (
                                            SELECT * FROM srp_erp_socialinsurancemaster WHERE companyID={$companyID}
                                        ) AS ssoMaster ON ssoMaster.socialInsuranceID = grMaster.socialInsuranceID
                                        WHERE payrollMasterID={$payrollID} AND pay.EmpID={$empID} AND (employerContribution = 0  OR employerContribution is null)
                                        AND fromTB = 'PAY_GROUP' GROUP BY detailTBID ")->result_array();

        $employerContributions = [];
        $OT_data = '';
        $isNonPayroll = $this->uri->segment(5);
        /*Get only for Envoy template (Not for Non payroll) */
        if ($isNonPayroll != 'Y') {
            $template = getPolicyValues('PT', 'SP');
            if ($template == 'Envoy') {
                $tempData = $this->db->query("SELECT template_tb.id, transactionAmount FROM srp_erp_sso_reporttemplatefields AS template_tb
                                        LEFT JOIN srp_erp_sso_reporttemplatedetails AS setup_tb ON setup_tb.reportID = template_tb.id  
                                        AND setup_tb.companyID={$companyID}
                                        LEFT JOIN srp_erp_payrolldetail AS pay ON setup_tb.reportValue=pay.detailTBID 
                                        AND pay.payrollMasterID={$payrollID} AND pay.empID={$empID} 
                                        WHERE template_tb.id IN (6, 7, 18) ")->result_array();

                foreach ($tempData as $tRow) {
                    $employerContributions[$tRow['id']] = $tRow['transactionAmount'];
                }


                /**** Get Over time hours and minutes */
                $OT_data = $this->db->query("SELECT CONCAT(
                    FLOOR(SUM(hourorDays) / 60),
                    'h ',
                    MOD (SUM(hourorDays), 60),
                    'm'
                ) AS otHour, salCatID
                                FROM srp_erp_payrolldetail payTb
                                JOIN (
                                    SELECT ID AS attRVID, hourorDays, otDet.empID 
                                    FROM srp_erp_pay_empattendancereview attTB 
                                    JOIN srp_erp_generalotdetail otDet ON attTB.generalOTID = otDet.generalOTMasterID
	AND attTB.empID = otDet.empID AND otDet.salaryCategoryID = attTB.salaryCategoryID
                                    WHERE paymentOT != 0 AND attTB.companyID={$companyID} AND attTB.empID={$empID} AND hourorDays != 0
                                    GROUP BY ID
                                ) AS otTB ON otTB.empID=payTb.empID AND payTb.detailTBID=otTB.attRVID
                                WHERE payrollMasterID={$payrollID} AND fromTB='OT' GROUP BY payTb.salCatID")->result_array();

            }

            if ($template == 'Aitken') {
                $tempData = $this->db->query("SELECT template_tb.id, transactionAmount FROM srp_erp_sso_reporttemplatefields AS template_tb
                                        LEFT JOIN srp_erp_sso_reporttemplatedetails AS setup_tb ON setup_tb.reportID = template_tb.id
                                        AND setup_tb.companyID={$companyID}
                                        LEFT JOIN srp_erp_payrolldetail AS pay ON setup_tb.reportValue=pay.detailTBID
                                        AND pay.payrollMasterID={$payrollID} AND pay.empID={$empID}
                                        WHERE template_tb.id IN (6, 7, 18) ")->result_array();

                foreach ($tempData as $tRow) {
                    $employerContributions[$tRow['id']] = $tRow['transactionAmount'];
                }


                /**** Get Over time hours and minutes */
                $OT_data = $this->db->query("SELECT CONCAT(
		FLOOR(SUM(hourorDays) / 60),
		'h ',
		MOD (SUM(hourorDays), 60),
		'm'
	) AS otHour, salCatID
                                FROM srp_erp_payrolldetail payTb
                                JOIN (
                                    SELECT ID AS attRVID, hourorDays, otDet.empID
                                    FROM srp_erp_pay_empattendancereview attTB
                                    JOIN srp_erp_generalotdetail otDet ON attTB.generalOTID = otDet.generalOTMasterID
	AND attTB.empID = otDet.empID AND otDet.salaryCategoryID = attTB.salaryCategoryID
                                    WHERE paymentOT != 0 AND attTB.companyID={$companyID} AND attTB.empID={$empID} AND hourorDays != 0
                                    GROUP BY ID
                                ) AS otTB ON otTB.empID=payTb.empID AND payTb.detailTBID=otTB.attRVID
                                WHERE payrollMasterID={$payrollID} AND fromTB='OT' GROUP BY payTb.salCatID")->result_array();

            }
        }


        //Loan Deduction
        $loanDed = $this->db->query("SELECT installmentNo, loan.loanCode, loanDescription, detailType, pay.transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace
                                        FROM  {$payDetailTB} AS pay
                                        JOIN srp_erp_pay_emploan_schedule AS loan_sch ON loan_sch.ID = pay.detailTBID
                                        JOIN srp_erp_pay_emploan AS loan ON loan.ID = loan_sch.loanID
                                        WHERE payrollMasterID={$payrollID} AND pay.EmpID={$empID} AND pay.companyID={$companyID}
                                        AND fromTB = 'LO'")->result_array();

        $loanIntPending = $this->db->query("SELECT loan.loanCode, loanDescription, count(l_sched.ID) AS pending_Int,
                                            sum(l_sched.transactionAmount) as trAmount
                                            FROM srp_erp_pay_emploan AS loan
                                            JOIN srp_erp_pay_emploan_schedule AS l_sched ON loan.ID = l_sched.loanID
                                            WHERE loan.empID = {$empID} AND l_sched.empID = {$empID} AND approvedYN = 1
                                            AND isClosed != 1 AND isSetteled = 0 AND skipedInstallmentID = 0
                                            GROUP BY loan.loanCode")->result_array();

        //Bank transfer
        $bankTransferDed = $this->db->query("SELECT bankName, accountNo, transactionCurrency, transactionAmount, salaryTransferPer,
                                             transactionCurrencyDecimalPlaces AS dPlace, swiftCode
                                             FROM {$bankTrTB}
                                             WHERE payrollMasterID={$payrollID} AND empID={$empID} AND companyID={$companyID}")->result_array();

        //Salary Paid by cash / cheque
        $salaryNonBankTransfer = $this->db->query("SELECT * FROM {$withoutBankTB} WHERE payrollMasterID={$payrollID} AND empID ={$empID} 
                                                   AND companyID={$companyID}")->row_array();

        return array(
            'headerDet' => $headerDet,
            'headerDet2' => $headerDet2,
            'payslip_date_info' => $payslip_date_info,
            'headerDet_grade' => $headerDet_grade,
            'salaryDec_A' => $salaryDec_A,
            'salaryDec_D' => $salaryDec_D,
            'monthAdd' => $monthAdd,
            'monthDec' => $monthDec,
            'sso_payee' => $sso_payee,
            'loanDed' => $loanDed,
            'loanIntPending' => $loanIntPending,
            'bankTransferDed' => $bankTransferDed,
            'salaryNonBankTransfer' => $salaryNonBankTransfer,
            'employerContributions' => $employerContributions,
            'OT_data' => $OT_data
        );
    }

    function save_payroll_employees_reporting_structure($payrollMasterID){

        $this->db->query("INSERT INTO srp_erp_payroll_employee_reporting_structure (
            empID,
            payrollID,
            reportingStructureID,
            reportingStructureDetailID,
            companyID
        
        ) SELECT
            srp_erp_employee_reporting_structure.empID,
            srp_erp_payrollheaderdetails.payrollMasterID AS payrollID,
            srp_erp_employee_reporting_structure.reportingStructureID,
            srp_erp_employee_reporting_structure.reportingStructureDetailID,
            srp_erp_payrollheaderdetails.companyID
        FROM
            srp_erp_payrollheaderdetails
        JOIN srp_erp_employee_reporting_structure ON srp_erp_payrollheaderdetails.EmpID = srp_erp_employee_reporting_structure.empID
        WHERE
            srp_erp_payrollheaderdetails.payrollMasterID = $payrollMasterID");

        //$this->db->trans_complete();
        /*if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            return array('s', 'Added...');/*save_payroll_employees_reporting_structure*/
        /*}*/
    }

    function update_attendance_details($payrollMasterID){

        $date_range = $this->get_start_end_date($payrollMasterID);

        //var_dump($date_range['startdate']);

        $this->db->query("UPDATE srp_erp_payrollheaderdetails
        JOIN (
        SELECT
            payrollHeaderDetID,
            absentDays,
            presentdays,
            totalOTHrs 
        FROM
            srp_erp_payrollheaderdetails headertble
            LEFT JOIN (
        SELECT
            empID AS empid,
            count( * ) AS presentdays 
        FROM
            srp_erp_pay_empattendancereview 
        WHERE
            ( attendanceDate BETWEEN '".$date_range['startdate']."' AND '".$date_range['enddate']."' ) 
            AND presentTypeID = 1 
        GROUP BY
            empID 
            ) presentdays ON headertble.empID = presentdays.empID
            LEFT JOIN (
        SELECT
            empID,
            count( * ) AS absentDays 
        FROM
            srp_erp_pay_empattendancereview 
        WHERE
            ( attendanceDate BETWEEN '".$date_range['startdate']."' AND '".$date_range['enddate']."' ) 
            AND ( presentTypeID = 6 OR presentTypeID = 4 ) -- or presentTypeID=6)
            
        GROUP BY
            empID 
            ) absentDays ON headertble.empID = absentDays.empID
            LEFT JOIN ( SELECT empID, sum( othours ) / 60 AS totalOThrs FROM srp_erp_pay_empattendancereview WHERE ( attendanceDate BETWEEN '".$date_range['startdate']."' AND '".$date_range['enddate']."' ) GROUP BY empID ) othrs ON headertble.empID = othrs.empID 
        WHERE
            payrollMasterID = $payrollMasterID 
            ) caltbl ON srp_erp_payrollheaderdetails.payrollHeaderDetID = caltbl.payrollHeaderDetID 
            SET srp_erp_payrollheaderdetails.totDayAbsent = caltbl.absentDays,
            srp_erp_payrollheaderdetails.totDayPresent = caltbl.presentdays,
        srp_erp_payrollheaderdetails.totOTHours = caltbl.totalOTHrs");

        /*$this->db->trans_complete();
        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            return array('s', 'Added');
        }*/
    }

    function get_start_end_date($payrollMasterID){

        return $this->db->query("SELECT
            payrollmasterID,
            payrollyear,
            payrollmonth,
            STR_TO_DATE( CONCAT( payrollyear, '-', payrollmonth, '-01' ), '%Y-%m-%d' ) AS startdate,
            LAST_DAY( STR_TO_DATE( CONCAT( payrollyear, '-', payrollmonth, '-01' ), '%Y-%m-%d' ) ) AS enddate 
        FROM
            srp_erp_payrollmaster
        WHERE
            payrollmasterID = $payrollMasterID")->row_array();
    }

    function get_EmpNonBankTransferDet($empID, $payrollMasterID, $isNonPayroll)
    {
        if ($isNonPayroll != 'Y') {
            $headerTB = 'srp_erp_payrollheaderdetails';
            $payTB = 'srp_erp_payroll_salarypayment_without_bank';
        } else {
            $headerTB = 'srp_erp_non_payrollheaderdetails';
            $payTB = 'srp_erp_non_payroll_salarypayment_without_bank';
        }
        return $this->db->query("SELECT sal.empID, ECode, CONCAT( Ename1, ' ', Ename2, ' ', Ename3, ' ', Ename4) AS empName,
                                 sal.transactionCurrency, sal.transactionAmount, sal.transactionCurrencyDecimalPlaces AS dPlace,
                                 isPaid, payByBankID, bankName, chequeNo
                                 FROM {$payTB} AS sal
                                 JOIN {$headerTB} AS header ON sal.empID = header.EmpID
                                 WHERE sal.empID = {$empID}  AND header.payrollMasterID={$payrollMasterID}
                                 AND sal.payrollMasterID={$payrollMasterID} ORDER BY sal.transactionCurrency")->row_array();
    }

    function save_empNonBankPay()
    {
        $empID = $this->input->post('hidden_empID');
        $payrollMasterID = $this->input->post('hidden_payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $payType = $this->input->post('payType');
        $empPayBank = $this->input->post('empPayBank');
        $chequeNo = $this->input->post('chequeNo');
        $paymentDate = $this->input->post('paymentDate');
        $bankName['bankName'] = null;

        if ($payType == 'By Cheque') {
            $bankName = $this->db->query("SELECT bankName FROM srp_erp_chartofaccounts WHERE GLAutoID={$empPayBank}")->row_array();
        }
        $where = array('empID' => $empID, 'payrollMasterID' => $payrollMasterID);
        $upData = array(
            'isPaid' => 1,
            'payByBankID' => $empPayBank,
            'bankName' => $bankName['bankName'],
            'chequeNo' => $chequeNo,
            'processedDate' => $paymentDate
        );

        $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_payroll_salarypayment_without_bank' : 'srp_erp_non_payroll_salarypayment_without_bank';
        $this->db->where($where)->update($tableName, $upData);


        if ($this->db->affected_rows() > 0) {
            return array('s', 'Salary transfer is done.');
        } else {
            return array('e', 'Error in process');
        }
    }

    function double_entries($paysheetID, $isNonPayroll)
    {
        $payMasted_det = $this->getPayrollDetails($paysheetID, $isNonPayroll);
        $narration = str_replace("'", '&#39', $payMasted_det['narration']);

        if ($payMasted_det['approvedYN'] == 1) {
            if ($isNonPayroll != 'Y') {
                $masterTableName = 'srp_erp_payrollmaster';
                $detailTableName = 'srp_erp_payrolldetail';
                $docCode = 'SP';
            } else {
                $masterTableName = 'srp_erp_non_payrollmaster';
                $detailTableName = 'srp_erp_non_payrolldetail';
                $docCode = 'SPN';
            }

            $companyID = current_companyID();
            $userGroup = current_user_group();
            $PC_ID = current_pc();
            $userID = current_userID();
            $time = $this->common_data['current_date'];
            $userName = current_employee();


            $this->db->trans_start();

            /************** Debit entries *************/
            $amountStr = 'round( sum( IF( fromTB = \'PAY_GROUP\', (t1.transactionAmount * -1), t1.transactionAmount )), t1.transactionCurrencyDecimalPlaces )';
            $amountLocalStr = 'round( sum( IF( fromTB = \'PAY_GROUP\', (t1.companyLocalAmount * -1), t1.companyLocalAmount )), t1.companyLocalCurrencyDecimalPlaces )';
            $amountRepoStr = 'round( sum( IF( fromTB = \'PAY_GROUP\', (t1.companyReportingAmount * -1), t1.companyReportingAmount )), t1.companyReportingCurrencyDecimalPlaces )';


            $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth,
                              documentNarration, GLAutoID, systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID,
                              transactionCurrency, transactionCurrencyDecimalPlaces, transactionExchangeRate, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency,
                              companyLocalCurrencyDecimalPlaces, companyLocalExchangeRate, companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency,
                              companyReportingCurrencyDecimalPlaces, companyReportingExchangeRate, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate,
                              approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode,contractAutoID,contractCode,contractRef, createdUserGroup, createdPCID, createdUserID,
                              createdDateTime, createdUserName)

                              SELECT '{$docCode}', t1.payrollMasterID, t2.documentCode, LAST_DAY(CONCAT(payrollYear,'-',payrollMonth,'-01')), payrollYear, payrollMonth,
                              '{$narration}', GLCode, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, IF( {$amountStr} > 0, 'dr', 'cr' ),
                              {$amountStr}, t1.transactionCurrencyID, t1.transactionCurrency, t1.transactionCurrencyDecimalPlaces, t1.transactionER,
                              {$amountLocalStr}, t1.companyLocalCurrencyID, t1.companyLocalCurrency, t1.companyLocalCurrencyDecimalPlaces, t1.companyLocalER,
                              {$amountRepoStr}, t1.companyReportingCurrencyID, t1.companyReportingCurrency, t1.companyReportingCurrencyDecimalPlaces, t1.companyReportingER,
                              t2.confirmedByEmpID, t2.confirmedByName, t2.confirmedDate, t2.approvedDate, t2.approvedbyEmpID, t2.approvedbyEmpName, t1.segmentID,
                              t1.segmentCode, t2.companyID, t2.companyCode,t1.contractAutoID,t1.contractCode,t1.contractRef,'{$userGroup}', '{$PC_ID}', {$userID}, '{$time}', '{$userName}'
                              FROM {$detailTableName} t1
                              JOIN {$masterTableName} t2 ON t2.payrollMasterID = t1.payrollMasterID AND t2.companyID = {$companyID}
                              JOIN srp_erp_chartofaccounts t3 ON t3.GLAutoID = t1.GLCode AND t3.companyID = {$companyID}
                              WHERE t1.companyID = {$companyID} AND t1.payrollMasterID = {$paysheetID} AND GLCode != 0 AND GLCode IS NOT NULL
                              GROUP BY GLCode, t1.transactionCurrency, t1.segmentID,t1.contractAutoID");

            /************* Credit entries ***************/
            $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth,
                              documentNarration, GLAutoID, systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID,
                              transactionCurrency, transactionCurrencyDecimalPlaces, transactionExchangeRate, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency,
                              companyLocalCurrencyDecimalPlaces, companyLocalExchangeRate, companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency,
                              companyReportingCurrencyDecimalPlaces, companyReportingExchangeRate, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate,
                              approvedbyEmpID, approvedbyEmpName, segmentID, segmentCode, companyID, companyCode,contractAutoID,contractCode,contractRef,createdUserGroup, createdPCID, createdUserID,
                              createdDateTime, createdUserName)

                              SELECT '{$docCode}', t1.payrollMasterID, t2.documentCode, LAST_DAY(CONCAT(payrollYear,'-',payrollMonth,'-01')), payrollYear, payrollMonth,
                              '{$narration}', liabilityGL, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, 'cr',
                              round( sum(t1.transactionAmount), t1.transactionCurrencyDecimalPlaces ), t1.transactionCurrencyID, t1.transactionCurrency,
                              t1.transactionCurrencyDecimalPlaces, t1.transactionER, round( sum(t1.companyLocalAmount), t1.companyLocalCurrencyDecimalPlaces ),
                              t1.companyLocalCurrencyID, t1.companyLocalCurrency, t1.companyLocalCurrencyDecimalPlaces, t1.companyLocalER,
                              round( sum(t1.companyReportingAmount), t1.companyReportingCurrencyDecimalPlaces ), t1.companyReportingCurrencyID,
                              t1.companyReportingCurrency, t1.companyReportingCurrencyDecimalPlaces, t1.companyReportingER, t2.confirmedByEmpID, t2.confirmedByName,
                              t2.confirmedDate, t2.approvedDate, t2.approvedbyEmpID, t2.approvedbyEmpName, t1.segmentID, t1.segmentCode, t2.companyID, t2.companyCode,t1.contractAutoID,t1.contractCode,t1.contractRef,
                              '{$userGroup}', '{$PC_ID}', {$userID}, '{$time}', '{$userName}'
                              FROM {$detailTableName} t1
                              JOIN {$masterTableName} t2 ON t2.payrollMasterID = t1.payrollMasterID AND t2.companyID = {$companyID}
                              JOIN srp_erp_chartofaccounts t3 ON t3.GLAutoID = t1.liabilityGL AND t3.companyID = {$companyID}
                              WHERE t1.companyID = {$companyID} AND t1.payrollMasterID = {$paysheetID} AND liabilityGL != 0 AND liabilityGL IS NOT NULL
                              GROUP BY liabilityGL, t1.transactionCurrency, t1.segmentID,t1.contractAutoID");


            /************  Company Payroll Control Account ID [$payrollCA_data] *************/
            $payrollCA_data = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory FROM srp_erp_chartofaccounts
                                                WHERE GLAutoID = (
                                                  SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'PCA' AND companyID = {$companyID}
                                                ) AND companyID={$companyID} ")->row_array();


            $GLAutoID = $payrollCA_data['GLAutoID'];
            $systemAccountCode = $payrollCA_data['systemAccountCode'];
            $GLSecondaryCode = $payrollCA_data['GLSecondaryCode'];
            $GLDescription = $payrollCA_data['GLDescription'];
            $subCategory = $payrollCA_data['subCategory'];

            $this->db->query("INSERT INTO srp_erp_generalledger (documentCode, documentMasterAutoID, documentSystemCode, documentDate, documentYear, documentMonth,
                              documentNarration, GLAutoID, systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount, transactionCurrencyID,
                              transactionCurrency, transactionCurrencyDecimalPlaces, transactionExchangeRate, companyLocalAmount, companyLocalCurrencyID, companyLocalCurrency,
                              companyLocalCurrencyDecimalPlaces, companyLocalExchangeRate, companyReportingAmount, companyReportingCurrencyID, companyReportingCurrency,
                              companyReportingCurrencyDecimalPlaces, companyReportingExchangeRate, confirmedByEmpID, confirmedByName, confirmedDate, approvedDate,
                              approvedbyEmpID, approvedbyEmpName, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName)

                              SELECT '{$docCode}', documentMasterAutoID, masterTB.documentCode, documentDate, documentYear, documentMonth, '{$narration}', {$GLAutoID},
                              '{$systemAccountCode}', '{$GLSecondaryCode}', '{$GLDescription}', '{$subCategory}', IF( (SUM(trAmount) > 1), 'dr', 'cr' ),
                              round( SUM(trAmount), dPlace), transactionCurrencyID,
                              transactionCurrency, dPlace, 1, round( SUM(localAmount), localDPlace), companyLocalCurrencyID, companyLocalCurrency, localDPlace, localER,
                              round( SUM(repotingAmount), reportingDPlace), companyReportingCurrencyID, companyReportingCurrency, reportingDPlace, repotingER,
                              confirmedByEmpID, confirmedByName, confirmedDate, approvedDate, approvedbyEmpID, approvedbyEmpName, companyID, companyCode, createdUserGroup,
                              createdPCID, createdUserID, createdDateTime, createdUserName
                              FROM
                              (
                                    SELECT documentMasterAutoID, documentCode, documentDate, documentYear, documentMonth, (transactionAmount*-1) AS trAmount,
                                    transactionCurrencyID, transactionCurrency, transactionCurrencyDecimalPlaces AS dPlace,
                                    (companyLocalAmount *- 1) AS localAmount, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate AS localER,
                                    companyLocalCurrencyDecimalPlaces AS localDPlace, (companyReportingAmount *- 1) AS repotingAmount, companyReportingCurrencyID,
                                    companyReportingCurrency, companyReportingExchangeRate AS repotingER, companyReportingCurrencyDecimalPlaces AS reportingDPlace
                                    FROM srp_erp_generalledger
                                    WHERE documentMasterAutoID={$paysheetID} AND documentCode ='{$docCode}' AND companyID={$companyID}
                              ) AS calTable
                              JOIN {$masterTableName} AS masterTB ON masterTB.payrollMasterID=calTable.documentMasterAutoID
                              AND calTable.documentMasterAutoID={$paysheetID} AND calTable.documentCode ='{$docCode}' AND masterTB.companyID={$companyID}
                              GROUP BY calTable.transactionCurrencyID ");


            $this->db->trans_complete();
            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                return array('s', $this->lang->line('hrms_payroll_payroll_process_double_entry_successfully_done'));/*Payroll process double entry successfully done*/
            } else {
                $this->db->trans_rollback();
                $paysheetCode = $payMasted_det['documentCode'];
                return array('s', 'Paysheet [ ' . $paysheetCode . ' ] Approved');
            }
        } else {
            return array('e', $this->lang->line('hrms_payroll_please_approve_all_the_levels_and_try_again')/*'Please approve all the levels and try again'*/);
        }
    }

    function check_financeYear($year, $month)
    {
        $msg = null;
        $date_text1 = $year . '-' . $month . '-01';
        $periodStart = date('Y-m-d', strtotime($date_text1));
        $periodEnd = date('Y-m-t', strtotime($date_text1));

        $financePeriod_arr = $this->db->select('companyFinancePeriodID, companyFinanceYearID')->from('srp_erp_companyfinanceperiod')
            ->where(
                array(
                    'dateFrom' => $periodStart,
                    'dateTo' => $periodEnd,
                    'isActive' => 1,
                    //'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row_array();

        $financeYearID = $this->db->select('companyFinanceYearID')->from('srp_erp_companyfinanceyear')
            ->where(
                array(
                    'companyFinanceYearID' => $financePeriod_arr['companyFinanceYearID'],
                    'isActive' => 1,
                    //'isCurrent' => 1,
                    'companyID' => current_companyID()
                )
            )->get()->row('companyFinanceYearID');


        if (empty($financeYearID)) {
            $msg = 'Payroll Date does not match with the financial year</br>';
        }

        if (empty($financePeriod_arr)) {
            $msg .= 'Payroll Date does not match with the financial period';
        }

        if ($msg == null) {
            return array('s', $financeYearID, $financePeriod_arr['companyFinancePeriodID']);
        } else {

            return array('e', $msg);
        }

    }

    function payrollAccountReview($payrollMasterID, $isNonPayroll = null)
    {
        $companyID = current_companyID();

        if ($isNonPayroll != 'Y') {
            $masterTableName = 'srp_erp_payrollmaster';
            $detailTableName = 'srp_erp_payrolldetail';
        } else {
            $masterTableName = 'srp_erp_non_payrollmaster';
            $detailTableName = 'srp_erp_non_payrolldetail';
        }

        $this->db->trans_start();
        $amountStr = 'round( SUM( IF( fromTB = \'PAY_GROUP\', (t1.transactionAmount * - 1), t1.transactionAmount )), t1.transactionCurrencyDecimalPlaces )';

        /************** Debit entries *************/
        $this->db->query("INSERT INTO srp_erp_payaccountreview_temp ( payrollMasterID, GLAutoID, systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount,
                          transactionCurrencyID, transactionCurrency, transactionCurrencyDecimalPlaces, segmentID, segmentCode, companyID, companyCode,contractAutoID,contractCode,contractRef )

                          SELECT t1.payrollMasterID, GLCode, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, IF( {$amountStr} > 0, 'dr', 'cr' ),
                          {$amountStr}, t1.transactionCurrencyID, t1.transactionCurrency, t1.transactionCurrencyDecimalPlaces,
                          t1.segmentID, t1.segmentCode, t2.companyID, t2.companyCode,t1.contractAutoID,t1.contractCode,t1.contractRef
                          FROM {$detailTableName} t1
                          JOIN {$masterTableName} t2 ON t2.payrollMasterID = t1.payrollMasterID AND t2.companyID = {$companyID}
                          JOIN srp_erp_chartofaccounts t3 ON t3.GLAutoID = t1.GLCode AND t3.companyID = {$companyID}
                          WHERE t1.companyID = {$companyID} AND t1.payrollMasterID = {$payrollMasterID} AND GLCode != 0 AND GLCode IS NOT NULL
                          GROUP BY GLCode, t1.transactionCurrency, t1.segmentID,t1.contractAutoID");


        /************* Credit entries / liability GL **************/
        $this->db->query("INSERT INTO srp_erp_payaccountreview_temp ( payrollMasterID, GLAutoID, systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount,
                          transactionCurrencyID, transactionCurrency, transactionCurrencyDecimalPlaces, segmentID, segmentCode, companyID, companyCode,contractAutoID,contractCode,contractRef )

                          SELECT t1.payrollMasterID, liabilityGL, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, 'cr',
                          round( SUM(t1.transactionAmount), t1.transactionCurrencyDecimalPlaces ), t1.transactionCurrencyID, t1.transactionCurrency,
                          t1.transactionCurrencyDecimalPlaces, t1.segmentID, t1.segmentCode, t2.companyID, t2.companyCode,t1.contractAutoID,t1.contractCode,t1.contractRef
                          FROM {$detailTableName} t1
                          JOIN {$masterTableName} t2 ON t2.payrollMasterID = t1.payrollMasterID AND t2.companyID = {$companyID}
                          JOIN srp_erp_chartofaccounts t3 ON t3.GLAutoID = t1.liabilityGL AND t3.companyID = {$companyID}
                          WHERE t1.companyID = {$companyID} AND t1.payrollMasterID = {$payrollMasterID} AND liabilityGL != 0 AND liabilityGL IS NOT NULL
                          GROUP BY liabilityGL, t1.transactionCurrency, t1.segmentID,t1.contractAutoID");

        /************  Company Payroll Control Account ID [$payrollCA_data] *************/
        $payrollCA_data = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory FROM srp_erp_chartofaccounts
                                            WHERE GLAutoID = (
                                              SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'PCA' AND companyID = {$companyID}
                                            ) AND companyID={$companyID} ")->row_array();


        $GLAutoID = $payrollCA_data['GLAutoID'];
        $systemAccountCode = $payrollCA_data['systemAccountCode'];
        $GLSecondaryCode = $payrollCA_data['GLSecondaryCode'];
        $GLDescription = $payrollCA_data['GLDescription'];
        $subCategory = $payrollCA_data['subCategory'];

        $this->db->query("INSERT INTO srp_erp_payaccountreview_temp ( payrollMasterID, GLAutoID, systemGLCode, GLCode, GLDescription, GLType, amount_type, transactionAmount,
                          transactionCurrencyID, transactionCurrency, transactionCurrencyDecimalPlaces, companyID, companyCode )

                          SELECT payrollMasterID, {$GLAutoID}, '{$systemAccountCode}', '{$GLSecondaryCode }', '{$GLDescription}', '{$subCategory}',
                          IF( ((SUM(amount) *-1) > 1), 'dr', 'cr' ), round(SUM(amount) *-1, dPlace), transactionCurrencyID, transactionCurrency, dPlace, companyID, companyCode
                          FROM (
                              SELECT payrollMasterID,  transactionAmount  AS amount,
                              transactionCurrencyID, transactionCurrency, IFNULL(transactionCurrencyDecimalPlaces,3) AS dPlace, companyID, companyCode
                              FROM srp_erp_payaccountreview_temp WHERE payrollMasterID={$payrollMasterID} AND companyID={$companyID}
                          ) AS calTable
                          GROUP BY calTable.transactionCurrencyID");

        $review_arr = $this->db->query("SELECT systemGLCode, GLCode, GLDescription, GLType, round(SUM(transactionAmount),transactionCurrencyDecimalPlaces) AS amount,
                                        amount_type, segmentCode, transactionCurrency, transactionCurrencyDecimalPlaces AS dPlace,contractAutoID,contractCode,contractRef
                                        FROM srp_erp_payaccountreview_temp
                                        WHERE payrollMasterID={$payrollMasterID} GROUP BY segmentCode, transactionCurrency, GLAutoID, amount_type,contractAutoID
                                        ORDER BY GLCode")->result_array();

        $this->db->where('payrollMasterID', $payrollMasterID)->where('companyID', $companyID)->delete('srp_erp_payaccountreview_temp');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Some thing went wrong, please contact software support');
        } else {
            $this->db->trans_commit();
            return array('s', $review_arr);
        }

    }

    function get_empPaySlipDetSelectedEmp($payrollID)
    {
        $companyID = current_companyID();
        //CONCAT(IFNULL(Ename1, ''),' ', IFNULL(Ename2, ''),' ',IFNULL(Ename3, ''),' ',IFNULL(Ename4, '')) AS empName
        $headerDet = $this->db->query("SELECT Ename2 AS empName, Designation,EmpID,
                                        if(transactionCurrency = null , transactionCurrency, payCurrency) AS transactionCurrency,
                                        if(transactionCurrencyDecimalPlaces = null, transactionCurrencyDecimalPlaces,
                                        (SELECT DecimalPlaces FROM srp_erp_currencymaster WHERE CurrencyCode = payCurrency )) AS dPlace
                                        FROM srp_erp_payrollheaderdetails WHERE payrollMasterID={$payrollID} AND
                                        srp_erp_payrollheaderdetails.companyID={$companyID} GROUP BY srp_erp_payrollheaderdetails.EmpID")->result_array();

        //salary Declarations
        $salaryDec_A = $this->db->query("SELECT salaryDescription, detailType, sum(pay.transactionAmount) AS transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  srp_erp_payrolldetail AS pay
                                        JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                        WHERE payrollMasterID={$payrollID}
                                        AND fromTB = 'SD' AND detailType = 'A' AND transactionAmount != 0 AND pay.companyID={$companyID}
                                        GROUP BY pay.salCatID,pay.EmpID ")->result_array();

        $salaryDec_D = $this->db->query("SELECT salaryDescription, detailType, sum(pay.transactionAmount) AS transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  srp_erp_payrolldetail AS pay
                                        JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                        WHERE payrollMasterID={$payrollID}
                                        AND fromTB = 'SD' AND detailType = 'D'  AND transactionAmount != 0 AND pay.companyID={$companyID}
                                        GROUP BY pay.salCatID,pay.EmpID ")->result_array();

        //Monthly Addition
        $monthAdd = $this->db->query("SELECT monthlyDeclaration AS description, detailType, pay.transactionAmount, pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  srp_erp_payrolldetail AS pay
                                        JOIN srp_erp_pay_monthlyadditiondetail AS mAdd ON mAdd.monthlyAdditionDetailID = pay.detailTBID
                                        JOIN srp_erp_pay_monthlydeclarationstypes AS monDec ON monDec.monthlyDeclarationID = mAdd.declarationID
                                        WHERE payrollMasterID={$payrollID} AND pay.companyID={$companyID}
                                        AND fromTB = 'MA'  GROUP BY pay.EmpID ")->result_array();

        //Monthly Deduction
        $monthDec = $this->db->query("SELECT monthlyDeclaration AS description, detailType, pay.transactionAmount, pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  srp_erp_payrolldetail AS pay
                                        JOIN srp_erp_pay_monthlydeductiondetail AS mDed ON mDed.monthlyDeductionDetailID = pay.detailTBID
                                        JOIN srp_erp_pay_monthlydeclarationstypes AS monDec ON monDec.monthlyDeclarationID = mDed.declarationID
                                        WHERE payrollMasterID={$payrollID} AND pay.companyID={$companyID}
                                        AND fromTB = 'MD' GROUP BY pay.EmpID")->result_array();

        //SSO Payee
        $sso_payee = $this->db->query("SELECT grMaster.description, detailType, pay.transactionAmount, pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  srp_erp_payrolldetail AS pay
                                        JOIN srp_erp_paygroupmaster AS grMaster ON grMaster.payGroupID = pay.detailTBID
                                        LEFT JOIN (
                                            SELECT * FROM srp_erp_socialinsurancemaster WHERE companyID={$companyID}
                                        ) AS ssoMaster ON ssoMaster.socialInsuranceID = grMaster.socialInsuranceID
                                        WHERE payrollMasterID={$payrollID} AND (employerContribution = 0  OR employerContribution is null)
                                        AND fromTB = 'PAY_GROUP' GROUP BY detailTBID,pay.EmpID ")->result_array();

        //Loan Deduction
        $loanDed = $this->db->query("SELECT installmentNo, loan.loanCode, loanDescription, detailType, pay.transactionAmount,
                                        pay.transactionCurrencyDecimalPlaces AS dPlace,pay.EmpID
                                        FROM  srp_erp_payrolldetail AS pay
                                        JOIN srp_erp_pay_emploan_schedule AS loan_sch ON loan_sch.ID = pay.detailTBID
                                        JOIN srp_erp_pay_emploan AS loan ON loan.ID = loan_sch.loanID
                                        WHERE payrollMasterID={$payrollID} AND pay.companyID={$companyID}
                                        AND fromTB = 'LO' GROUP BY pay.EmpID")->result_array();

        $loanIntPending = $this->db->query("SELECT loan.loanCode, loanDescription, count(l_sched.ID) AS pending_Int,
                                            sum(l_sched.transactionAmount) as trAmount,loan.empID
                                            FROM srp_erp_pay_emploan AS loan
                                            JOIN srp_erp_pay_emploan_schedule AS l_sched ON loan.ID = l_sched.loanID
                                            WHERE approvedYN = 1
                                            AND isClosed != 1 AND isSetteled = 0 AND skipedInstallmentID = 0
                                            GROUP BY loan.loanCode,loan.empID")->result_array();

        //Bank transfer
        $bankTransferDed = $this->db->query("SELECT bankName, accountNo, transactionCurrency, transactionAmount, salaryTransferPer,
                                             transactionCurrencyDecimalPlaces AS dPlace, swiftCode,empID
                                             FROM srp_erp_pay_banktransfer
                                             WHERE payrollMasterID={$payrollID} AND companyID={$companyID} GROUP BY empID")->result_array();

        //Salary Paid by cash / cheque
        $salaryNonBankTransfer = $this->db->query("SELECT * FROM srp_erp_payroll_salarypayment_without_bank
                                                   WHERE payrollMasterID={$payrollID} AND companyID={$companyID} GROUP BY empID")->result_array();;

        return array(
            'headerDet' => array_group_by($headerDet, 'EmpID'),
            'salaryDec_A' => array_group_by($salaryDec_A, 'EmpID'),
            'salaryDec_D' => array_group_by($salaryDec_D, 'EmpID'),
            'monthAdd' => array_group_by($monthAdd, 'EmpID'),
            'monthDec' => array_group_by($monthDec, 'EmpID'),
            'sso_payee' => array_group_by($sso_payee, 'EmpID'),
            'loanDed' => array_group_by($loanDed, 'EmpID'),
            'loanIntPending' => array_group_by($loanIntPending, 'empID'),
            'bankTransferDed' => array_group_by($bankTransferDed, 'empID'),
            'salaryNonBankTransfer' => array_group_by($salaryNonBankTransfer, 'empID')
        );
    }

    function getDefault_paySlipTemplate($isNonPayroll)
    {
        /*$companyID = current_companyID();
        $description = ($isNonPayroll != 'Y') ? 'Payslip Template' : 'Non Payslip Template';

        $templateID = $this->db->query("SELECT IF( companyValue IS NULL, defaultValue, companyValue ) AS templateID
                                        FROM srp_erp_companypolicymaster AS masterTB
                                        LEFT JOIN (
                                            SELECT companypolicymasterID, `value` AS companyValue FROM srp_erp_companypolicy WHERE companyID={$companyID}
                                        ) AS policyTB ON policyTB.companypolicymasterID=masterTB.companypolicymasterID
                                        WHERE companyPolicyDescription='{$description}'")->row('templateID');
        return $templateID;*/

        /*$documentCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
        $code = ($isNonPayroll != 'Y') ? 'PT' : 'NPT';
        return $this->common_data['company_policy'][$code][$documentCode][0]["policyvalue"];*/
    }

    function fetchPaySheetData_employee($payrollMasterID, $empID, $isNonPayroll)
    {
        $companyID = current_companyID();
        if ($isNonPayroll != 'Y') {
            $headerDetailTableName = 'srp_erp_payrollheaderdetails';
            $detailTableName = 'srp_erp_payrolldetail';
            $payGroupDetailTableName = 'srp_erp_payrolldetailpaygroup';
        } else {
            $headerDetailTableName = 'srp_erp_non_payrollheaderdetails';
            $detailTableName = 'srp_erp_non_payrolldetail';
            $payGroupDetailTableName = 'srp_erp_non_payrolldetailpaygroup';
        }

        $info = $this->db->query("SELECT empTB.*, empTB.transactionAmount AS netTrans , fromTB, calculationTB, detailType, salCatID, pay.detailTBID,
                                  sum(pay.transactionAmount) AS transactionAmount, pay.transactionCurrencyDecimalPlaces, seg.segmentCode AS emp_segmentCode
                                  FROM {$headerDetailTableName} AS empTB
                                  JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                  LEFT JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = pay.salCatID
                                  LEFT JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID
                                  WHERE pay.payrollMasterID = {$payrollMasterID} AND empTB.payrollMasterID = {$payrollMasterID} AND pay.companyID = {$companyID}
                                  AND fromTB != 'PAY_GROUP' AND pay.empID={$empID}
                                  GROUP BY pay.empID, pay.salCatID, pay.calculationTB
                                  UNION
                                        SELECT empTB.*, empTB.transactionAmount AS netTrans, fromTB, calculationTB, detailType, salCatID, pay.detailTBID,
                                        pay.transactionAmount AS transactionAmount, pay.transactionCurrencyDecimalPlaces, seg.segmentCode AS emp_segmentCode
                                        FROM {$headerDetailTableName} AS empTB
                                        JOIN {$detailTableName} AS pay ON empTB.EmpID=pay.empID
                                        LEFT JOIN srp_erp_segment AS seg ON seg.segmentID = empTB.segmentID
                                        WHERE fromTB = 'PAY_GROUP' AND pay.payrollMasterID = {$payrollMasterID} AND empTB.payrollMasterID = {$payrollMasterID}
                                        AND pay.empID={$empID}
                                  UNION
                                        SELECT pay2.*, pay2.transactionAmount AS netTrans, fromTB, fromTB AS calculationTB, detailType, '' AS salCatID,
                                        detailTBID, payGroup.transactionAmount, payGroup.transactionCurrencyDecimalPlaces, '' AS emp_segmentCode
                                        FROM {$payGroupDetailTableName}  AS payGroup
                                        JOIN {$headerDetailTableName} AS pay2 ON payGroup.empID=pay2.empID AND pay2.companyID={$companyID} AND
                                        pay2.payrollMasterID={$payrollMasterID}
                                        WHERE payGroup.companyID={$companyID} AND payGroup.payrollMasterID={$payrollMasterID}
                                        AND payGroup.empID={$empID}
                                  ORDER BY empID DESC")->result_array();

        //echo $this->db->last_query() .' <p>';


        if (isset($info)) {

            $dataArray = array();
            $i = 0;
            $j = 0;
            $ECode = '';

            foreach ($info as $row) {
                $tmpECode = $row['EmpID'];

                if ($ECode != $tmpECode) {
                    $j = 0;
                    $i++;

                    switch ($row['Gender']) {
                        case '1':
                            $gender = 'Male';
                            break;

                        case '2':
                            $gender = 'Female';
                            break;

                        default :
                            $gender = '-';
                    }

                    //$dataArray[$i]['empDet'] = $row;
                    $dataArray[$i]['empDet'] = array(
                        'E_ID' => $row['EmpID'],
                        'ECode' => $row['ECode'],
                        'Ename1' => $row['Ename1'],
                        'Ename2' => $row['Ename2'],
                        'Ename3' => $row['Ename3'],
                        'Ename4' => $row['Ename4'],
                        'EmpShortCode' => $row['EmpShortCode'],
                        'Designation' => $row['Designation'],
                        'Gender' => $gender,
                        'EcTel' => $row['Tel'],
                        'EcMobile' => $row['Mobile'],
                        'EDOJ' => $row['DOJ'],
                        'payCurrency' => $row['payCurrency'],
                        'nationality' => $row['nationality'],
                        'dPlaces' => $row['transactionCurrencyDecimalPlaces'],
                        'segmentID' => $row['emp_segmentCode']
                    );

                    $ECode = $row['EmpID'];
                }


                if ($row['calculationTB'] == 'SD') {
                    $cat = $row['salCatID'];
                } else if ($row['fromTB'] == 'PAY_GROUP') {
                    $cat = 'G_' . $row['detailTBID'];
                } else {
                    $cat = $row['fromTB'];
                }

                $dataArray[$i]['empSalDec'][$j] = array(
                    'catID' => $cat,
                    'catType' => $row['detailType'],
                    'amount' => $row['transactionAmount'],
                );
                $j++;

            }
            return $dataArray;

        } else {
            return 'There is no record.';
        }
    }

    public function sendProgress($percentage) {
        echo '+';
        flush();
        // ob_flush();
    }

    function insertPaySheetDataBasedOnEmployee()
    {
        ob_end_clean(); // Clear output buffer
        $payYear = $this->input->post('payYear');
        $payMonth1 = $payMonth = $this->input->post('payMonth');
        $narration = $this->input->post('payNarration');
        //$narration = str_replace("'", '&#39', $narration);
        $selectedEmployees = $this->input->post('selectedEmployees');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $date_format_policy = date_format_policy();
        $processingDate = $this->input->post('processingDate');
        $processingDate = input_format_date($processingDate, $date_format_policy);
        $visibleDate = $this->input->post('visibleDate');
        $visibleDate = input_format_date($visibleDate, $date_format_policy);

        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = current_date();

        if ($isNonPayroll != 'Y') {
            $payMasterTB = 'srp_erp_payrollmaster';
            $headerDetailTB = 'srp_erp_payrollheaderdetails';
            $detailTB = 'srp_erp_payrolldetail';
            $salaryDeclartionTB = 'srp_erp_pay_salarydeclartion';
            $payrollCode = 'SP';
        } else {
            $payMasterTB = 'srp_erp_non_payrollmaster';
            $headerDetailTB = 'srp_erp_non_payrollheaderdetails';
            $detailTB = 'srp_erp_non_payrolldetail';
            $salaryDeclartionTB = 'srp_erp_non_pay_salarydeclartion';
            $payrollCode = 'SPN';
        }

        //Generate document Code
        $lastDocumentNo = $this->db->query("SELECT documentNo FROM {$payMasterTB} WHERE companyID={$companyID} ORDER BY payrollMasterID DESC LIMIT 1")->row_array();
        $nextDocumentNo = ($lastDocumentNo['documentNo'] != null) ? $lastDocumentNo['documentNo'] + 1 : 1;
        $this->load->library('sequence');
        $nextDocumentCode = $this->sequence->sequence_generator($payrollCode, $nextDocumentNo);

        $this->sendProgress(1);

        $masterData = array(
            'documentCode' => $nextDocumentCode,
            'documentNo' => $nextDocumentNo,
            'payrollYear' => $payYear,
            'payrollMonth' => $payMonth,
            'processDate' => $processingDate,
            'visibleDate' => $visibleDate,
            'narration' => $narration,
            'companyID' => $companyID,
            'companyCode' => $companyCode,
            'createdPCID' => $createdPCID,
            'createdUserGroup' => $createdUserGroup,
            'createdUserID' => $createdUserID,
            'createdUserName' => $createdUserName,
            'createdDateTime' => $createdDateTime
        );

        //$this->db->trans_start();

        $this->db->insert($payMasterTB, $masterData);
        $payrollMasterID = $this->db->insert_id();       

        $payDate_arr = $this->createDate($payYear, $payMonth);
        $payDateMin = $payDate_arr['minDate'];
        $payDateMax = $payDate_arr['maxDate'];


        //insert header details for srp_erp_payroll header details table
        /**/
        $strAccessGroup = ''; 
        $this->db->query("INSERT INTO {$headerDetailTB} ( payrollMasterID, EmpID, accessGroupID, ECode, Ename1, Ename2, Ename3, Ename4, EmpShortCode,
                          secondaryCode, Designation, Gender, Tel, Mobile, DOJ, payCurrencyID, payCurrency, nationality, segmentID, segmentCode, companyID, companyCode)
                          SELECT {$payrollMasterID}, EIdNo,  IFNULL(groupID,0), ECode, Ename1, Ename2, Ename3, Ename4, EmpShortCode, EmpSecondaryCode, DesDescription,
                          Gender, EpTelephone, EcMobile, EDOJ, payCurrencyID, payCurrency, srp_nationality.Nationality, empTB.segmentID, segmentCode, {$companyID},
                          '{$companyCode}'
                          FROM srp_employeesdetails AS empTB
                          JOIN srp_designation ON srp_designation.DesignationID = empTB.EmpDesignationId AND srp_designation.Erp_companyID={$companyID}
                          JOIN srp_erp_segment ON srp_erp_segment.segmentID = empTB.segmentID AND srp_erp_segment.companyID={$companyID}
                          LEFT JOIN srp_nationality ON srp_nationality.NId = empTB.Nid
                          JOIN(
                                SELECT employeeNo FROM {$salaryDeclartionTB} WHERE companyID = {$companyID} AND payDate < '{$payDateMax}'
                                GROUP BY employeeNo
                          ) AS declaration ON declaration.employeeNo = empTB.EIdNo
                          LEFT JOIN (
                                SELECT EIdNo AS empID, dischargedDate,
                                IF( isDischarged != 1, 0,
                                    CASE
                                        WHEN '{$payDateMin}' <= DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 0
                                        WHEN '{$payDateMin}' > DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 1
                                    END
                                )AS isDischargedStatus
                                FROM srp_employeesdetails WHERE Erp_companyID ={$companyID} AND EIdNo IN ({$selectedEmployees})
                          ) AS dischargedStatusTB ON dischargedStatusTB.empID = empTB.EIdNo
                          LEFT JOIN (
                               SELECT employeeID, groupID FROM srp_erp_payrollgroupemployees WHERE companyID={$companyID}
                          ) AS groupEmp ON groupEmp.employeeID = empTB.EIdNo
                          WHERE  EIdNo IN ({$selectedEmployees}) AND empTB.Erp_companyID = {$companyID} AND isDischargedStatus != 1
                          AND EIdNo NOT IN (
                              SELECT empID FROM srp_erp_payrollmaster AS payMaster
                              JOIN {$headerDetailTB} AS payDet ON payDet.payrollMasterID = payMaster.payrollMasterID AND payDet.companyID={$companyID}
                              WHERE payMaster.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth1}
                          )");

        $countEmp = $this->db->query("SELECT COUNT(EmpID) AS empCount FROM {$headerDetailTB} WHERE payrollMasterID={$payrollMasterID}")->row('empCount');
        
        if($payrollMasterID){
            $this->save_payroll_employees_reporting_structure($payrollMasterID);
            $this->update_attendance_details($payrollMasterID);
        }

        $this->sendProgress(2);     


        if ($countEmp < 1) {
            $this->db->trans_rollback();
            return ['w', 'There is no employee to proceed'];
            die();

        }

        $localCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];

        $salaryProportionFormulaDays = getPolicyValues('SPF', 'All');
        $salaryProportionDays = (empty($salaryProportionFormulaDays)) ? 365 : $salaryProportionFormulaDays;


        $totalWorkedDays = getPolicyValues('SCD', 'All');
        $totalWorkedDays = (empty($totalWorkedDays)) ? 'LAST_DAY(effectiveDate)' : $totalWorkedDays;

        if ($totalWorkedDays == 'LAST_DAY(effectiveDate)') {
            $totalWorkedDays = ' DATEDIFF( LAST_DAY(effectiveDate), effectiveDate )+1 ';
        } else {
            $totalWorkedDays = "( 30 - DATE_FORMAT(effectiveDate, '%d') )+1";
        }

        $transactionAmount = "IF (
                                DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                                round(
                                  ( ((transactionAmount * 12) / {$salaryProportionDays}) *  ({$totalWorkedDays}) ), transactionCurrencyDecimalPlaces
                                ),
                                transactionAmount
                             )";

        $transactionAmount_sal = "IF (
                                DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                                round(
                                  ( ((transactionAmount * 12) / {$salaryProportionDays}) *  ({$totalWorkedDays}) ), transactionCurrencyDecimalPlaces
                                ),
                                (SUM(transactionAmount) - leave_salary_det.salAmount1)
                             )";

        $localAmount = "IF (
                            DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                            round(
                                ( ( ((transactionAmount * 12) / {$salaryProportionDays})  *  ({$totalWorkedDays}) ) / convLocal.conversion),
                                companyLocalCurrencyDecimalPlaces
                            ),
                            round( (transactionAmount/ convLocal.conversion), companyLocalCurrencyDecimalPlaces)                            
                         )";

        $reportAmount = "IF (
                            DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                            round(
                                ( ( ((transactionAmount * 12) / {$salaryProportionDays})  *  ({$totalWorkedDays}) ) / convRepo.conversion),
                                companyReportingCurrencyDecimalPlaces
                            ),
                            round( (transactionAmount/ convRepo.conversion), companyReportingCurrencyDecimalPlaces)
                         )";

        if ($salaryProportionDays == 1) {
            $transactionAmount = "IF (
                                    DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                                    round(
                                      ( (transactionAmount / DATE_FORMAT(LAST_DAY(effectiveDate), '%d') ) *  ( DATEDIFF( LAST_DAY(effectiveDate), effectiveDate )+1 ) )
                                      , transactionCurrencyDecimalPlaces
                                    ),
                                    transactionAmount
                                 )";

            $localAmount = "IF (
                            DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                            round(                                
                                ( ( (transactionAmount / DATE_FORMAT(LAST_DAY(effectiveDate), '%d') ) *  ( DATEDIFF( LAST_DAY(effectiveDate), effectiveDate )+1 ) ) / convLocal.conversion),
                                companyLocalCurrencyDecimalPlaces
                            ),
                            round( (transactionAmount/ convLocal.conversion), companyLocalCurrencyDecimalPlaces)
                         )";

            $reportAmount = "IF (
                            DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                            round(                               
                                ( ( (transactionAmount / DATE_FORMAT(LAST_DAY(effectiveDate), '%d') ) *  ( DATEDIFF( LAST_DAY(effectiveDate), effectiveDate )+1 ) ) / convRepo.conversion),
                                companyReportingCurrencyDecimalPlaces
                            ),
                            round( (transactionAmount/ convRepo.conversion), companyReportingCurrencyDecimalPlaces)
                         )";
        }
        $this->sendProgress(3);   

        //add salary declaration
        $this->db->query("INSERT INTO {$detailTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, salCatID, detailType, GLCode,
                     transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, transactionAmount,
                     companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyLocalAmount,
                     companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID,
                     companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName, segmentID, segmentCode )
                     SELECT {$payrollMasterID} AS  payrollMasterID, id, 'SD', 'SD', employeeNo, cat.salaryCategoryID, salaryCategoryType, cat.GLCode,
                     transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, {$transactionAmount} AS trAmount,
                     companyLocalCurrencyID, companyLocalCurrency, convLocal.conversion, companyLocalCurrencyDecimalPlaces, {$localAmount} AS companyLocalAmount1,
                     companyReportingCurrencyID, companyReportingCurrency, convRepo.conversion, {$reportAmount} AS companyReportingAmount1,
                     companyReportingCurrencyDecimalPlaces, {$companyID}, '{$companyCode}',
                     '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}', '{$createdUserName}' ,seg.segmentID, seg.segmentCode
                     FROM {$salaryDeclartionTB} AS declaration
                     JOIN (
                        SELECT EmpID, segmentID, payCurrencyID FROM {$headerDetailTB} WHERE payrollMasterID={$payrollMasterID} AND companyID={$companyID}
                     )AS empTB ON declaration.employeeNo = empTB.EmpID
                     JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = declaration.salaryCategoryID
                     JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID
                     JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                     AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                     JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                     AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                     WHERE declaration.companyID = {$companyID}  AND payDate < '$payDateMax' AND NOT EXISTS (
                        SELECT de.empID FROM srp_erp_pay_leaveencashment AS ms
                        JOIN srp_erp_pay_leaveencashmentdetails AS de ON de.encash_masterID = ms.masterID
                        WHERE ms.companyID = {$companyID} AND ms.document_type = 2 AND de.salary_pay_date = '{$payDateMin}'
                        AND empTB.EmpID = de.empID
                     )
                     ORDER BY EmpID ASC");


        ///add leave salary details
        $this->db->query("INSERT INTO {$detailTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, salCatID, detailType, GLCode,
            transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, transactionAmount,
            companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyLocalAmount,
            companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID,
            companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName, segmentID, segmentCode )
            SELECT {$payrollMasterID} AS  payrollMasterID, id, 'SD', 'SD', employeeNo, cat.salaryCategoryID, salaryCategoryType, cat.GLCode,
            transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, {$transactionAmount_sal} AS trAmount,
            companyLocalCurrencyID, companyLocalCurrency, convLocal.conversion, companyLocalCurrencyDecimalPlaces, {$localAmount} AS companyLocalAmount1,
            companyReportingCurrencyID, companyReportingCurrency, convRepo.conversion, {$reportAmount} AS companyReportingAmount1,
            companyReportingCurrencyDecimalPlaces, {$companyID}, '{$companyCode}',
            '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}', '{$createdUserName}' ,seg.segmentID, seg.segmentCode
            FROM {$salaryDeclartionTB} AS declaration
            JOIN (
            SELECT EmpID, segmentID, payCurrencyID FROM {$headerDetailTB} WHERE payrollMasterID={$payrollMasterID} AND companyID={$companyID}
            )AS empTB ON declaration.employeeNo = empTB.EmpID
            JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = declaration.salaryCategoryID
            JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID
            JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
            AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
            JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
            AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}

            JOIN (
                SELECT sal.empID as empid1, SUM(sal.amount) as salAmount1,sal.salaryCategoryID FROM srp_erp_pay_leaveencashment AS ms1
                JOIN srp_erp_pay_leaveencashmentsalarydetails AS sal ON sal.encash_masterID = ms1.masterID                         
                WHERE ms1.companyID = {$companyID} AND ms1.document_type = 2 AND ms1.approvedYN= 1  AND sal.salary_pay_date = '{$payDateMin}' GROUP BY sal.salaryCategoryID 	
            ) AS leave_salary_det ON empTB.EmpID = leave_salary_det.empid1 AND declaration.salaryCategoryID = leave_salary_det.salaryCategoryID

            WHERE declaration.companyID = {$companyID}  AND payDate < '$payDateMax' GROUP BY declaration.salaryCategoryID
     
            ORDER BY EmpID ASC");
        
        $this->sendProgress(4);

        //update nopay for absent / unapproved /
        //acording to the no pay policy
        $absentAsNoPay = getPolicyValues('HANOP','All');
        $attendanceCycleDay = getPolicyValues('HACDAY','All');

        if($absentAsNoPay == 1){
            $last_month = date('Y-m-d',strtotime('-1 month',strtotime($payDateMin)));

            if($attendanceCycleDay == 31){
                $attendanceMin = $payDateMin;
            } else {
                $attendanceMin = substr($last_month, 0, -2).($attendanceCycleDay + 1);
            }
            
            $attendanceMax = substr($payDateMin, 0, -2).($attendanceCycleDay);

            $this->update_record_as_nopay($payrollMasterID, $attendanceMin, $attendanceMax, $isNonPayroll);

            //update balance payments
            $this->update_emp_balance_payments($payrollMasterID, $attendanceMin, $attendanceMax, $isNonPayroll);
           
        }

        //print_r($attendanceMin); exit;

        //write 0 amount for the employees who are got paid leave salary AND ms1.approvedYN= 1
        // $this->db->query("INSERT INTO {$detailTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, salCatID, detailType, GLCode,
        //              transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, transactionAmount,
        //              companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyLocalAmount,
        //              companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID,
        //              companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName, segmentID, segmentCode )
        //              SELECT {$payrollMasterID} AS  payrollMasterID, id, 'SD', 'SD', employeeNo, cat.salaryCategoryID, salaryCategoryType, cat.GLCode,
        //              transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, 0 AS trAmount,
        //              companyLocalCurrencyID, companyLocalCurrency, convLocal.conversion, companyLocalCurrencyDecimalPlaces, 0 AS companyLocalAmount1,
        //              companyReportingCurrencyID, companyReportingCurrency, convRepo.conversion, 0 AS companyReportingAmount1,
        //              companyReportingCurrencyDecimalPlaces, {$companyID}, '{$companyCode}',
        //              '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}', '{$createdUserName}' ,seg.segmentID, seg.segmentCode
        //              FROM {$salaryDeclartionTB} AS declaration
        //              JOIN (
        //                 SELECT EmpID, segmentID, payCurrencyID FROM {$headerDetailTB} WHERE payrollMasterID={$payrollMasterID} AND companyID={$companyID}
        //              )AS empTB ON declaration.employeeNo = empTB.EmpID
        //              JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = declaration.salaryCategoryID
        //              JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID
        //              JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
        //              AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
        //              JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
        //              AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
        //              JOIN (
        //                 SELECT de.empID FROM srp_erp_pay_leaveencashment AS ms
        //                 JOIN srp_erp_pay_leaveencashmentdetails AS de ON de.encash_masterID = ms.masterID
        //                 WHERE ms.companyID = {$companyID} AND ms.document_type = 2 AND de.salary_pay_date = '{$payDateMin}'	
        //              ) AS leave_salary ON empTB.EmpID = leave_salary.empID
        //              WHERE declaration.companyID = {$companyID}  AND payDate < '$payDateMax' 
        //              GROUP BY empTB.EmpID ");

        $this->sendProgress(5);   
        //Insert data from M-Addition / M-Deduction / Loan Schedule tables
        $this->insertFromSalaryProcessTables($payrollMasterID, $payDateMin, $payDateMax, $isNonPayroll);

        // if($absentAsNoPay == 1){
        //     print_r($attendanceMax); exit;
        //     $this->insertFromSalaryProcessTables($payrollMasterID, $attendanceMin, $attendanceMax, $isNonPayroll);
        // }else{
        //     $this->insertFromSalaryProcessTables($payrollMasterID, $payDateMin, $payDateMax, $isNonPayroll);
        // }
        

        $this->sendProgress(6); 
        //Update inserted record as processed on M-Addition / M-Deduction / Loan Schedule tables
        $this->updatesSalaryProcessTables($payrollMasterID, 1, $isNonPayroll, $payDateMin, $payDateMax);

    
        if ($isNonPayroll != 'Y') {

             //Update inserted record as processed on M-Addition / M-Deduction / Loan Schedule tables
            $this->updatesSalaryProcessTablesFromContract($payrollMasterID, 1, $isNonPayroll, $payDateMin, $payDateMax);

        }

        $this->sendProgress(7);  
        //SSO calculation
        if ($isNonPayroll != 'Y') {
            $this->ssoCal($payrollMasterID, $payDateMin);
        }

        $this->sendProgress(8);   

        //Payee calculation
        $this->payeeCal($payrollMasterID, $isNonPayroll);

        //Group calculation
        $process = $this->payGroup_temporary_calculation($payrollMasterID, $isNonPayroll, $payDateMin);
        if ($process[0] != 's') {
            return $process;
        }

        //Update net salary transaction amount
        $this->db->query("UPDATE {$headerDetailTB} tab1
                          JOIN (
                             SELECT empID, SUM(transactionAmount) AS totAmount, transactionCurrencyID, transactionCurrency,
                             transactionER, transactionCurrencyDecimalPlaces
                             FROM {$detailTB} AS payrollDet
                             WHERE NOT EXISTS (
                                SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                                JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID={$companyID}
                                WHERE payrollDet.detailTBID = groupMaster.payGroupID AND payrollDet.fromTB='PAY_GROUP'
                                AND groupMaster.companyID={$companyID} AND SSO_TB.employerContribution > 0
                             ) AND payrollDet.payrollMasterID={$payrollMasterID}
                             GROUP BY empID LIMIT {$countEmp}
                          ) tab2
                          ON tab1.empID = tab2.empID AND tab1.payrollMasterID =  {$payrollMasterID}
                          SET tab1.transactionCurrencyID = tab2.transactionCurrencyID,
                          tab1.transactionCurrency = tab2.transactionCurrency,
                          tab1.transactionER = tab2.transactionER,
                          tab1.transactionCurrencyDecimalPlaces = tab2.transactionCurrencyDecimalPlaces,
                          tab1.transactionAmount = tab2.totAmount");

        $this->sendProgress(9);

        //Update net salary local amount
        $this->db->query("UPDATE {$headerDetailTB} tab1
                          JOIN (
                             SELECT empID, SUM(companyLocalAmount) AS totAmount, companyLocalCurrencyID, companyLocalCurrency,
                             companyLocalER, companyLocalCurrencyDecimalPlaces
                             FROM {$detailTB} AS payrollDet
                             WHERE NOT EXISTS (
                                SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                                JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID
                                AND SSO_TB.companyID={$companyID}
                                WHERE payrollDet.detailTBID = groupMaster.payGroupID AND payrollDet.fromTB='PAY_GROUP'
                                AND groupMaster.companyID={$companyID} AND SSO_TB.employerContribution > 0
                             ) AND payrollDet.payrollMasterID={$payrollMasterID}
                             GROUP BY empID LIMIT {$countEmp}
                          ) tab2
                          ON tab1.empID = tab2.empID AND tab1.payrollMasterID =  {$payrollMasterID}
                          SET tab1.companyLocalCurrencyID = tab2.companyLocalCurrencyID,
                          tab1.companyLocalCurrency = tab2.companyLocalCurrency,
                          tab1.companyLocalER = tab2.companyLocalER,
                          tab1.companyLocalCurrencyDecimalPlaces = tab2.companyLocalCurrencyDecimalPlaces,
                          tab1.companyLocalAmount = tab2.totAmount");

        //Update net salary reporting amount
        $this->db->query("UPDATE {$headerDetailTB} tab1
                          JOIN (
                             SELECT empID, SUM(companyReportingAmount) AS totAmount, companyReportingCurrencyID, companyReportingCurrency,
                             companyReportingER, companyReportingCurrencyDecimalPlaces
                             FROM {$detailTB} AS payrollDet
                             WHERE NOT EXISTS (
                                SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                                JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID
                                AND SSO_TB.companyID={$companyID}
                                WHERE payrollDet.detailTBID = groupMaster.payGroupID AND payrollDet.fromTB='PAY_GROUP'
                                AND groupMaster.companyID={$companyID} AND SSO_TB.employerContribution > 0
                             ) AND payrollDet.payrollMasterID={$payrollMasterID}
                             GROUP BY empID LIMIT {$countEmp}
                          ) tab2
                          ON tab1.empID = tab2.empID AND tab1.payrollMasterID =  {$payrollMasterID}
                          SET tab1.companyReportingCurrencyID = tab2.companyReportingCurrencyID,
                          tab1.companyReportingCurrency = tab2.companyReportingCurrency,
                          tab1.companyReportingER = tab2.companyReportingER,
                          tab1.companyReportingCurrencyDecimalPlaces = tab2.companyReportingCurrencyDecimalPlaces,
                          tab1.companyReportingAmount = tab2.totAmount");

        $this->payroll_bankTransfer($payrollMasterID, $isNonPayroll, 1);



        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Failed to Update payroll');
        } else {
            $this->db->trans_commit();
            $this->sendProgress(10);   
            $monthName = date('F', mktime(0, 0, 0, ($payMonth - 1), 10));

            return array('s', '[ ' . $payYear . ' ' . $monthName . ' ] generated successfully', $payrollMasterID);
        }

    }


    function updatesSalaryProcessTablesFromContract($payrollMasterID, $isProcess, $isNonPayroll, $payDateMin, $payDateMax, $empID = null){

        $companyID = current_companyID();

        try {
            if($payrollMasterID){

                $this->db->where('payrollMasterID',$payrollMasterID);
                $payroll_records = $this->db->from('srp_erp_payrolldetail')->get()->result_array();
    
                foreach($payroll_records as $pay){
    
                    $empID = $pay['empID'];
                    $payrollDetailID = $pay['payrollDetailID'];
                    $data_arr = array();
    
                    $this->db->where('cc.empID',$empID);
                    $this->db->where('cc.companyID',$companyID);
                    $this->db->where('cc.isPrimary',1);
                    $this->db->join('srp_erp_contractmaster as sc','cc.contractAutoID = sc.contractAutoID','left');
                    $query_emp = $this->db->from('srp_erp_contractcrew as cc')->get()->row_array();
    
                    $data_arr['contractAutoID'] = $query_emp['contractAutoID'];
                    $data_arr['contractCode'] = $query_emp['contractCode'];
                    $data_arr['contractRef'] = $query_emp['referenceNo'];
    
                    //update
                    $this->db->where('payrollDetailID',$payrollDetailID);
                    $update = $this->db->update('srp_erp_payrolldetail',$data_arr);
    
                }

                return true;
    
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        

    }

    function insertPaySheetDataBasedOnEmployee_period_base($period_arr)
    {

        $pay_group = $this->input->post('p_group');
        $period_id = $this->input->post('period_id');
        $narration = $this->input->post('payNarration');
        $selectedEmployees = $this->input->post('selectedEmployees');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $date_format_policy = date_format_policy();
        $processingDate = $this->input->post('processingDate');
        $processingDate = input_format_date($processingDate, $date_format_policy);
        $visibleDate = $this->input->post('visibleDate');
        $visibleDate = input_format_date($visibleDate, $date_format_policy);

        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = current_date();

        if ($isNonPayroll != 'Y') {
            $payMasterTB = 'srp_erp_payrollmaster';
            $headerDetailTB = 'srp_erp_payrollheaderdetails';
            $detailTB = 'srp_erp_payrolldetail';
            $salaryDeclartionTB = 'srp_erp_pay_salarydeclartion';
            $payrollCode = 'SP';
        } else {
            $payMasterTB = 'srp_erp_non_payrollmaster';
            $headerDetailTB = 'srp_erp_non_payrollheaderdetails';
            $detailTB = 'srp_erp_non_payrolldetail';
            $salaryDeclartionTB = 'srp_erp_non_pay_salarydeclartion';
            $payrollCode = 'SPN';
        }

        //Generate document Code
        $lastDocumentNo = $this->db->query("SELECT documentNo FROM {$payMasterTB} WHERE companyID={$companyID} ORDER BY payrollMasterID DESC LIMIT 1")->row_array();
        $nextDocumentNo = ($lastDocumentNo['documentNo'] != null) ? $lastDocumentNo['documentNo'] + 1 : 1;
        $this->load->library('sequence');
        $nextDocumentCode = $this->sequence->sequence_generator($payrollCode, $nextDocumentNo);

        $masterData = array(
            'documentCode' => $nextDocumentCode,
            'documentNo' => $nextDocumentNo,
            'payrollGroupID' => $pay_group,
            'periodID' => $period_id,
            'payrollYear' => 0,
            'payrollMonth' => 0,
            'processDate' => $processingDate,
            'visibleDate' => $visibleDate,
            'narration' => $narration,
            'companyID' => $companyID,
            'companyCode' => $companyCode,
            'createdPCID' => $createdPCID,
            'createdUserGroup' => $createdUserGroup,
            'createdUserID' => $createdUserID,
            'createdUserName' => $createdUserName,
            'createdDateTime' => $createdDateTime
        );

        $this->db->trans_start();

        $this->db->insert($payMasterTB, $masterData);
        $payrollMasterID = $this->db->insert_id();

        $payDateMin = $period_arr['dateFrom'];
        $payDateMax = $period_arr['dateTo'];


        //insert header details for srp_erp_payroll header details table
        $strAccessGroup = '';
        $this->db->query("INSERT INTO {$headerDetailTB} ( payrollMasterID, EmpID, accessGroupID, ECode, Ename1, Ename2, Ename3, Ename4, EmpShortCode,
                          secondaryCode, Designation, Gender, Tel, Mobile, DOJ, payCurrencyID, payCurrency, nationality, segmentID, segmentCode, companyID, companyCode)
                          SELECT {$payrollMasterID}, EIdNo,  IFNULL(groupID,0), ECode, Ename1, Ename2, Ename3, Ename4, EmpShortCode, EmpSecondaryCode, DesDescription,
                          Gender, EpTelephone, EcMobile, EDOJ, payCurrencyID, payCurrency, srp_nationality.Nationality, empTB.segmentID, segmentCode, {$companyID},
                          '{$companyCode}'
                          FROM srp_employeesdetails AS empTB
                          JOIN srp_designation ON srp_designation.DesignationID = empTB.EmpDesignationId AND srp_designation.Erp_companyID={$companyID}
                          JOIN srp_erp_segment ON srp_erp_segment.segmentID = empTB.segmentID AND srp_erp_segment.companyID={$companyID}
                          LEFT JOIN srp_nationality ON srp_nationality.NId = empTB.Nid
                          JOIN(
                                SELECT employeeNo FROM {$salaryDeclartionTB} WHERE companyID = {$companyID} AND payDate < '{$payDateMax}'
                                GROUP BY employeeNo
                          ) AS declaration ON declaration.employeeNo = empTB.EIdNo
                          LEFT JOIN (
                                SELECT EIdNo AS empID, dischargedDate,
                                IF( isDischarged != 1, 0,
                                    CASE
                                        WHEN '{$payDateMin}' <= DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 0
                                        WHEN '{$payDateMin}' > DATE_FORMAT(dischargedDate, '%Y-%m-01') THEN 1
                                    END
                                )AS isDischargedStatus
                                FROM srp_employeesdetails WHERE Erp_companyID ={$companyID} AND EIdNo IN ({$selectedEmployees})
                          ) AS dischargedStatusTB ON dischargedStatusTB.empID = empTB.EIdNo
                          LEFT JOIN (
                               SELECT employeeID, groupID FROM srp_erp_payrollgroupemployees WHERE companyID={$companyID}
                          ) AS groupEmp ON groupEmp.employeeID = empTB.EIdNo
                          WHERE  EIdNo IN ({$selectedEmployees}) AND empTB.Erp_companyID = {$companyID} AND isDischargedStatus != 1
                          AND EIdNo NOT IN (
                              SELECT empID FROM srp_erp_payrollmaster AS payMaster
                              JOIN {$headerDetailTB} AS payDet ON payDet.payrollMasterID = payMaster.payrollMasterID AND payDet.companyID={$companyID}
                              WHERE payMaster.companyID={$companyID} AND payrollGroupID = {$pay_group} AND periodID = {$period_id} 
                          )");

        $countEmp = $this->db->query("SELECT COUNT(EmpID) AS empCount FROM {$headerDetailTB} WHERE payrollMasterID={$payrollMasterID}")->row('empCount');

        if ($countEmp < 1) {
            $this->db->trans_rollback();
            return ['w', 'There is no employee to proceed'];
        }

        $localCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];

        $salaryProportionFormulaDays = getPolicyValues('SPF', 'All');
        $salaryProportionDays = (empty($salaryProportionFormulaDays)) ? 365 : $salaryProportionFormulaDays;


        $totalWorkedDays = getPolicyValues('SCD', 'All');
        $totalWorkedDays = (empty($totalWorkedDays)) ? 'LAST_DAY(effectiveDate)' : $totalWorkedDays;

        if ($totalWorkedDays == 'LAST_DAY(effectiveDate)') {
            $totalWorkedDays = ' DATEDIFF( LAST_DAY(effectiveDate), effectiveDate )+1 ';
        } else {
            $totalWorkedDays = "( 30 - DATE_FORMAT(effectiveDate, '%d') )+1";
        }

        $transactionAmount = "IF (
                                DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                                round(
                                  ( ((transactionAmount * 12) / {$salaryProportionDays}) *  ({$totalWorkedDays}) ), transactionCurrencyDecimalPlaces
                                ),
                                transactionAmount
                             )";

        $localAmount = "IF (
                            DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                            round(
                                ( ( ((transactionAmount * 12) / {$salaryProportionDays})  *  ({$totalWorkedDays}) ) / convLocal.conversion),
                                companyLocalCurrencyDecimalPlaces
                            ),
                            round( (transactionAmount/ convLocal.conversion), companyLocalCurrencyDecimalPlaces)                            
                         )";

        $reportAmount = "IF (
                            DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                            round(
                                ( ( ((transactionAmount * 12) / {$salaryProportionDays})  *  ({$totalWorkedDays}) ) / convRepo.conversion),
                                companyReportingCurrencyDecimalPlaces
                            ),
                            round( (transactionAmount/ convRepo.conversion), companyReportingCurrencyDecimalPlaces)
                         )";

        if ($salaryProportionDays == 1) {
            $transactionAmount = "IF (
                                    DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                                    round(
                                      ( (transactionAmount / DATE_FORMAT(LAST_DAY(effectiveDate), '%d') ) *  ( DATEDIFF( LAST_DAY(effectiveDate), effectiveDate )+1 ) )
                                      , transactionCurrencyDecimalPlaces
                                    ),
                                    transactionAmount
                                 )";

            $localAmount = "IF (
                            DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                            round(                                
                                ( ( (transactionAmount / DATE_FORMAT(LAST_DAY(effectiveDate), '%d') ) *  ( DATEDIFF( LAST_DAY(effectiveDate), effectiveDate )+1 ) ) / convLocal.conversion),
                                companyLocalCurrencyDecimalPlaces
                            ),
                            round( (transactionAmount/ convLocal.conversion), companyLocalCurrencyDecimalPlaces)
                         )";

            $reportAmount = "IF (
                            DATE_FORMAT(effectiveDate, '%Y-%m-01') = '{$payDateMin}' AND (DATE_FORMAT(effectiveDate, '%d') != '01'),
                            round(                               
                                ( ( (transactionAmount / DATE_FORMAT(LAST_DAY(effectiveDate), '%d') ) *  ( DATEDIFF( LAST_DAY(effectiveDate), effectiveDate )+1 ) ) / convRepo.conversion),
                                companyReportingCurrencyDecimalPlaces
                            ),
                            round( (transactionAmount/ convRepo.conversion), companyReportingCurrencyDecimalPlaces)
                         )";
        }

        //add salary declaration
        $this->db->query("INSERT INTO {$detailTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, salCatID, detailType, GLCode,
                     transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, transactionAmount,
                     companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyLocalAmount,
                     companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID,
                     companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName, segmentID, segmentCode )
                     SELECT {$payrollMasterID} AS  payrollMasterID, id, 'SD', 'SD', employeeNo, cat.salaryCategoryID, salaryCategoryType, cat.GLCode,
                     transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, {$transactionAmount} AS trAmount,
                     companyLocalCurrencyID, companyLocalCurrency, convLocal.conversion, companyLocalCurrencyDecimalPlaces, {$localAmount} AS companyLocalAmount1,
                     companyReportingCurrencyID, companyReportingCurrency, convRepo.conversion, {$reportAmount} AS companyReportingAmount1,
                     companyReportingCurrencyDecimalPlaces, {$companyID}, '{$companyCode}',
                     '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}', '{$createdUserName}' ,seg.segmentID, seg.segmentCode
                     FROM {$salaryDeclartionTB} AS declaration
                     JOIN (
                        SELECT EmpID, segmentID, payCurrencyID FROM {$headerDetailTB} WHERE payrollMasterID={$payrollMasterID} AND companyID={$companyID}
                     )AS empTB ON declaration.employeeNo = empTB.EmpID
                     JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = declaration.salaryCategoryID
                     JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID
                     JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                     AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                     JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                     AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                     WHERE declaration.companyID = {$companyID}  AND payDate < '$payDateMax' AND NOT EXISTS (
                        SELECT de.empID FROM srp_erp_pay_leaveencashment AS ms
                        JOIN srp_erp_pay_leaveencashmentdetails AS de ON de.encash_masterID = ms.masterID
                        WHERE ms.companyID = {$companyID} AND ms.document_type = 2 AND de.salary_pay_date = '{$payDateMin}'
                        AND empTB.EmpID = de.empID
                     )
                     ORDER BY EmpID ASC");

        //write 0 amount for the employees who are got paid leave salary
        $this->db->query("INSERT INTO {$detailTB} ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, salCatID, detailType, GLCode,
                     transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, transactionAmount,
                     companyLocalCurrencyID, companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyLocalAmount,
                     companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID,
                     companyCode, createdUserGroup, createdPCID, createdUserID, createdDateTime, createdUserName, segmentID, segmentCode )
                     SELECT {$payrollMasterID} AS  payrollMasterID, id, 'SD', 'SD', employeeNo, cat.salaryCategoryID, salaryCategoryType, cat.GLCode,
                     transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, 0 AS trAmount,
                     companyLocalCurrencyID, companyLocalCurrency, convLocal.conversion, companyLocalCurrencyDecimalPlaces, 0 AS companyLocalAmount1,
                     companyReportingCurrencyID, companyReportingCurrency, convRepo.conversion, 0 AS companyReportingAmount1,
                     companyReportingCurrencyDecimalPlaces, {$companyID}, '{$companyCode}',
                     '{$createdUserGroup}', '{$createdPCID}', {$createdUserID}, '{$createdDateTime}', '{$createdUserName}' ,seg.segmentID, seg.segmentCode
                     FROM {$salaryDeclartionTB} AS declaration
                     JOIN (
                        SELECT EmpID, segmentID, payCurrencyID FROM {$headerDetailTB} WHERE payrollMasterID={$payrollMasterID} AND companyID={$companyID}
                     )AS empTB ON declaration.employeeNo = empTB.EmpID
                     JOIN srp_erp_pay_salarycategories AS cat ON cat.salaryCategoryID = declaration.salaryCategoryID
                     JOIN srp_erp_segment seg ON seg.segmentID = empTB.segmentID
                     JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
                     AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
                     JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
                     AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
                     JOIN (
                        SELECT de.empID FROM srp_erp_pay_leaveencashment AS ms
                        JOIN srp_erp_pay_leaveencashmentdetails AS de ON de.encash_masterID = ms.masterID
                        WHERE ms.companyID = {$companyID} AND ms.document_type = 2 AND de.salary_pay_date = '{$payDateMin}'	
                     ) AS leave_salary ON empTB.EmpID = leave_salary.empID
                     WHERE declaration.companyID = {$companyID}  AND payDate < '$payDateMax' 
                     GROUP BY empTB.EmpID ");

        //Insert data from M-Addition / M-Deduction / Loan Schedule tables
        $this->insertFromSalaryProcessTables($payrollMasterID, $payDateMin, $payDateMax, $isNonPayroll);

        //Update inserted record as processed on M-Addition / M-Deduction / Loan Schedule tables
        $this->updatesSalaryProcessTables($payrollMasterID, 1, $isNonPayroll, $payDateMin, $payDateMax);

        //SSO calculation
        if ($isNonPayroll != 'Y') {
            $this->ssoCal($payrollMasterID, $payDateMin);
        }

        //Payee calculation
        $this->payeeCal($payrollMasterID, $isNonPayroll);

        //Group calculation
        $process = $this->payGroup_temporary_calculation($payrollMasterID, $isNonPayroll, $payDateMin);
        if ($process[0] != 's') {
            return $process;
        }

        //Update net salary transaction amount
        $this->db->query("UPDATE {$headerDetailTB} tab1
                          JOIN (
                             SELECT empID, SUM(transactionAmount) AS totAmount, transactionCurrencyID, transactionCurrency,
                             transactionER, transactionCurrencyDecimalPlaces
                             FROM {$detailTB} AS payrollDet
                             WHERE NOT EXISTS (
                                SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                                JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID={$companyID}
                                WHERE payrollDet.detailTBID = groupMaster.payGroupID AND payrollDet.fromTB='PAY_GROUP'
                                AND groupMaster.companyID={$companyID} AND SSO_TB.employerContribution > 0
                             ) AND payrollDet.payrollMasterID={$payrollMasterID}
                             GROUP BY empID LIMIT {$countEmp}
                          ) tab2
                          ON tab1.empID = tab2.empID AND tab1.payrollMasterID =  {$payrollMasterID}
                          SET tab1.transactionCurrencyID = tab2.transactionCurrencyID,
                          tab1.transactionCurrency = tab2.transactionCurrency,
                          tab1.transactionER = tab2.transactionER,
                          tab1.transactionCurrencyDecimalPlaces = tab2.transactionCurrencyDecimalPlaces,
                          tab1.transactionAmount = tab2.totAmount");


        //Update net salary local amount
        $this->db->query("UPDATE {$headerDetailTB} tab1
                          JOIN (
                             SELECT empID, SUM(companyLocalAmount) AS totAmount, companyLocalCurrencyID, companyLocalCurrency,
                             companyLocalER, companyLocalCurrencyDecimalPlaces
                             FROM {$detailTB} AS payrollDet
                             WHERE NOT EXISTS (
                                SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                                JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID
                                AND SSO_TB.companyID={$companyID}
                                WHERE payrollDet.detailTBID = groupMaster.payGroupID AND payrollDet.fromTB='PAY_GROUP'
                                AND groupMaster.companyID={$companyID} AND SSO_TB.employerContribution > 0
                             ) AND payrollDet.payrollMasterID={$payrollMasterID}
                             GROUP BY empID LIMIT {$countEmp}
                          ) tab2
                          ON tab1.empID = tab2.empID AND tab1.payrollMasterID =  {$payrollMasterID}
                          SET tab1.companyLocalCurrencyID = tab2.companyLocalCurrencyID,
                          tab1.companyLocalCurrency = tab2.companyLocalCurrency,
                          tab1.companyLocalER = tab2.companyLocalER,
                          tab1.companyLocalCurrencyDecimalPlaces = tab2.companyLocalCurrencyDecimalPlaces,
                          tab1.companyLocalAmount = tab2.totAmount");

        //Update net salary reporting amount
        $this->db->query("UPDATE {$headerDetailTB} tab1
                          JOIN (
                             SELECT empID, SUM(companyReportingAmount) AS totAmount, companyReportingCurrencyID, companyReportingCurrency,
                             companyReportingER, companyReportingCurrencyDecimalPlaces
                             FROM {$detailTB} AS payrollDet
                             WHERE NOT EXISTS (
                                SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                                JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID
                                AND SSO_TB.companyID={$companyID}
                                WHERE payrollDet.detailTBID = groupMaster.payGroupID AND payrollDet.fromTB='PAY_GROUP'
                                AND groupMaster.companyID={$companyID} AND SSO_TB.employerContribution > 0
                             ) AND payrollDet.payrollMasterID={$payrollMasterID}
                             GROUP BY empID LIMIT {$countEmp}
                          ) tab2
                          ON tab1.empID = tab2.empID AND tab1.payrollMasterID =  {$payrollMasterID}
                          SET tab1.companyReportingCurrencyID = tab2.companyReportingCurrencyID,
                          tab1.companyReportingCurrency = tab2.companyReportingCurrency,
                          tab1.companyReportingER = tab2.companyReportingER,
                          tab1.companyReportingCurrencyDecimalPlaces = tab2.companyReportingCurrencyDecimalPlaces,
                          tab1.companyReportingAmount = tab2.totAmount");

        $this->payroll_bankTransfer($payrollMasterID, $isNonPayroll, 1);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Failed to Update payroll');
        } else {
            $this->db->trans_commit();
            $monthName = date('F', mktime(0, 0, 0, ($payMonth - 1), 10));
            return array('s', '[ ' . $payYear . ' ' . $monthName . ' ] generated successfully', $payrollMasterID);
        }

    }

    function update_record_as_nopay($payrollMasterID, $payDateMin, $payDateMax, $isNonPayroll){
    
       
        $companyID = current_companyID();
        $selectedEmployees = $this->input->post('selectedEmployees');

        $get_empattendance_absent = get_attendance_review_records($payDateMin,$payDateMax,1,null,$selectedEmployees);
        $get_empattendance_unapproved = get_attendance_review_records($payDateMin,$payDateMax,null,1,$selectedEmployees);

        //Get No pay salary category id
        $salaryCategoryRecord = get_salary_category();
        $salaryCategoryID = null;

        if($salaryCategoryRecord){
            $salaryCategoryID = $salaryCategoryRecord['salaryCategoryID'];
        }

        foreach($get_empattendance_unapproved as $emp_record){
            // if unapproved added it to seperate table
            $noPayAmount = $this->assign_no_pay_amount($emp_record['empID'],$emp_record['attendanceDate'],$salaryCategoryID,$payDateMax);

            // un approved flag for next month
            $this->unapproved_attendance_add($emp_record,$noPayAmount);

        }

       

        foreach($get_empattendance_absent as $emp_record){
            // absent means no pay 
            $this->assign_no_pay_amount($emp_record['empID'],$emp_record['attendanceDate'],$salaryCategoryID,$payDateMax);

        }

    }

    function update_emp_balance_payments($payrollMasterID, $payDateMin, $payDateMax, $isNonPayroll){

        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = $this->common_data['current_date'];

        $localCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $localCurrency = $this->common_data['company_data']['company_default_currency'];
        $localDPlace = $this->common_data['company_data']['company_default_decimal'];
        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
        $repCurrency = $this->common_data['company_data']['company_reporting_currency'];
        $repCurDPlace = $this->common_data['company_data']['company_reporting_decimal'];

        $selectedEmployees = $this->input->post('selectedEmployees');
        $masterTB = 'srp_erp_payrollheaderdetails';
        $defaultBPID = null;

        $selected_emp_arr = explode(',',$selectedEmployees);

        $salary_aries_record = get_salary_category(5);
        if($salary_aries_record){
            $defaultBPID = $salary_aries_record['salaryCategoryID'];
        }

        // print_r($defaultBPID); exit;


        $this->db->query("INSERT INTO srp_erp_payrolldetail ( payrollMasterID, detailTBID, fromTB, calculationTB, empID, detailType, salCatID, GLCode,
            transactionCurrencyID, transactionCurrency, transactionER, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,
            companyLocalCurrency, companyLocalER, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency,
            companyReportingER, companyReportingAmount, companyReportingCurrencyDecimalPlaces, companyID, companyCode, createdUserGroup, createdPCID,
            createdUserID, createdDateTime, createdUserName, segmentID, segmentCode)
            SELECT {$payrollMasterID}, balancePay.id, 'BPA', 'SD', balancePay.empID, 'A', {$defaultBPID}, GLCode, payCurrencyID, currencyMaster.CurrencyCode,
            1 AS trER, round(balancePay.noPayAmount, currencyMaster.DecimalPlaces) AS BPAmount, currencyMaster.DecimalPlaces,
            {$localCurrencyID}, '{$localCurrency}', convLocal.conversion AS localER, round(balancePay.noPayAmount / convLocal.conversion, {$localDPlace}), {$localDPlace},
            {$repCurrencyID}, '{$repCurrency}', convRepo.conversion AS reER, round(balancePay.noPayAmount / convRepo.conversion, {$repCurDPlace}), {$repCurDPlace},
            {$companyID}, '{$companyCode}', '{$createdUserGroup}', '{$createdPCID}', {$createdUserID},  '{$createdDateTime}', '{$createdUserName}',
            segTB.segmentID, segTB.segmentCode
            FROM srp_erp_pay_unapproved_attendance AS balancePay
            JOIN {$masterTB} AS empTB ON empTB.EmpID=balancePay.empID AND empTB.companyID={$companyID} AND
            payrollMasterID={$payrollMasterID}

            JOIN srp_erp_currencymaster AS currencyMaster ON currencyMaster.currencyID = empTB.payCurrencyID
            JOIN srp_erp_companycurrencyconversion AS convLocal ON convLocal.masterCurrencyID = empTB.payCurrencyID
            AND convLocal.companyID={$companyID} AND convLocal.subCurrencyID = {$localCurrencyID}
            JOIN srp_erp_companycurrencyconversion AS convRepo ON convRepo.masterCurrencyID = empTB.payCurrencyID
            AND convRepo.companyID={$companyID} AND convRepo.subCurrencyID = {$repCurrencyID}
            JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = {$defaultBPID} AND salCat.companyID={$companyID}
            JOIN srp_erp_segment AS segTB ON segTB.segmentID = empTB.segmentID AND segTB.companyID={$companyID}
            WHERE balancePay.companyID={$companyID} AND balancePay.approvedYN = 1 AND balancePay.presentTypeID != 6 AND balancePay.isPaid IS NULL AND balancePay.empID IN ($selectedEmployees)");

        //update
        $data = array();
        $data['isPaid'] = 1;
        $data['payrollID'] = $payrollMasterID;

        $this->db->where('companyID',$companyID)->where_in('empID',$selected_emp_arr)->where('approvedYN',1)->where('isPaid IS NULL',null)->update('srp_erp_pay_unapproved_attendance',$data);

        return TRUE;

    }

    function unapproved_attendance_add($empRecord,$noPayAmount){

        //check record exists
        $empID = $empRecord['empID'];
        $empViewID = $empRecord['ID'];
        $attendanceDate = $empRecord['attendanceDate'];
        $no_pay_amount = $noPayAmount['noPayAmount'];

        $companyID = current_companyID();
        $companyCode = current_companyCode();
        $createdDateTime = current_date();

        $ex_record = $this->db->where('empViewID',$empViewID)->where('companyID',$companyID)->from('srp_erp_pay_unapproved_attendance')->get()->row_array();

        if(!$ex_record){

            $data = array();

            $data['empViewID'] = $empViewID;
            $data['empID'] = $empID;
            $data['attendanceDate'] = $attendanceDate;
            $data['presentTypeID'] = $empRecord['presentTypeID'];
            $data['noPayAmount'] = $no_pay_amount;
            $data['createdDate'] = $createdDateTime;
            $data['companyID'] = $companyID;
            $data['companyCode'] = $companyCode;

            $res = $this->db->insert('srp_erp_pay_unapproved_attendance',$data);

        }

        return TRUE;

    }

    function assign_no_pay_amount($empID,$attendanceDate,$salaryCategoryID = null,$payMaxDate = null){

        $detail = array();
        $companyID = current_companyID();
        $detail_arr = $this->db->query("SELECT *, masterTB.id AS masterID
                             FROM srp_erp_nopaysystemtable AS masterTB
                             INNER JOIN srp_erp_nopayformula AS fromulaTB ON fromulaTB.nopaySystemID =masterTB.id AND companyID={$companyID} ")->result_array();

        //Check employee for existing leaves
        // $attendanceDate = '2024-01-09';
        $leaveEx = $this->db->query("
            SELECT masterTB.*,CASE
                WHEN '{$attendanceDate}' BETWEEN masterTB.startDate AND masterTB.endDate THEN 'Yes'
                ELSE 'No'
                END AS is_between
            FROM srp_erp_leavemaster AS masterTB
            WHERE empID = '{$empID}' AND companyID = {$companyID} AND approvedYN = 1 
        ")->row_array();
        
        if ($detail_arr) {
            foreach ($detail_arr as $key => $row) {

                $isNonPayroll = $row['isNonPayroll'];
                $table = ($isNonPayroll != 'Y') ? 'srp_erp_pay_salarydeclartion' : 'srp_erp_non_pay_salarydeclartion';
                $classTitle = explode(' ', $row['description']);
                $formulaText = '';
                $formula = trim($row['formulaString'] ?? '');
                $lastInputType = '';
                $formulaBuilder = $this->formulaBuilder_to_sql_OT($formula,$payMaxDate);
                $formulaDecodeOT = $formulaBuilder['formulaDecode'];
                $select_str2 = $formulaBuilder['select_str2'];
                $whereInClause = $formulaBuilder['whereInClause'];
                $wherePayMaxDate = '';

                if($payMaxDate){
                    $wherePayMaxDate = "AND salDec.effectiveDate <= '{$payMaxDate}'";
                }

                $as = $this->db->query("SELECT calculationTB.employeeNo, '$isNonPayroll' AS type, (({$formulaDecodeOT } ) )AS transactionAmount, transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyLocalER) , companyLocalCurrencyDecimalPlaces  )AS localAmount, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, round( ((" . $formulaDecodeOT . " ) / companyReportingER)   , companyReportingCurrencyDecimalPlaces  )AS reportingAmount, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces, seg.segmentID, seg.segmentCode FROM ( SELECT employeeNo, " . $select_str2 . " , transactionCurrencyID, transactionCurrency, transactionER, transactionCurrencyDecimalPlaces, companyLocalCurrencyID , companyLocalCurrency, companyLocalER, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingER, companyReportingCurrencyDecimalPlaces FROM {$table} AS salDec JOIN srp_erp_pay_salarycategories AS salCat ON salCat.salaryCategoryID = salDec.salaryCategoryID AND salCat.companyID ={$companyID} WHERE salDec.companyID = {$companyID}  AND employeeNo={$empID} AND salDec.salaryCategoryID  IN (" . $whereInClause . ") {$wherePayMaxDate} GROUP BY employeeNo, salDec.salaryCategoryID ) calculationTB JOIN srp_employeesdetails AS emp ON emp.EIdNo = calculationTB.employeeNo AND emp.Erp_companyID = {$companyID} JOIN srp_erp_segment seg ON seg.segmentID = emp.segmentID AND seg.companyID = {$companyID} GROUP BY employeeNo")->row_array();
                
                $detail['noPayAmount'] = ($as['transactionAmount'] != '' ? $as['transactionAmount'] : 0);
                $detail['presentTypeID'] = 6;
                $detail['paymentOT'] = 0;
                $detail['salaryCategoryID'] = $salaryCategoryID;
                
            }

            if($leaveEx && $leaveEx['is_between'] == 'Yes'){
                $detail['noPayAmount'] = 0;
                $detail['presentTypeID'] = 5;
                $detail['paymentOT'] = 0;
                $detail['salaryCategoryID'] = null;
            }

  
            if (!empty($detail)) {
                $this->db->where('empID', $empID);
                $this->db->where('attendanceDate', $attendanceDate);
                $this->db->update('srp_erp_pay_empattendancereview', $detail);
            }

            return $detail;

        }

    }

    function formulaBuilder_to_sql_OT($formula,$date = null)
    {
        $salary_categories_arr = salary_categories(array('A', 'D'));
        $formulaText = '';
        $salaryCatID = array();
        $formulaDecode_arr = array();
        $operand_arr = operand_arr();

        $formula_arr = explode('|', $formula); // break the formula

        $n = 0;
        foreach ($formula_arr as $formula_row) {

            if (trim($formula_row) != '') {
                if (in_array($formula_row, $operand_arr)) { //validate is a operand
                    $formulaText .= $formula_row;
                    $formulaDecode_arr[] = $formula_row;
                } else {

                    $elementType = $formula_row[0];

                    if ($elementType == '_') {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $formulaText .= (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];
                        $formulaDecode_arr[] = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                    } else if ($elementType == '#') {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $salaryCatID[$n]['ID'] = $catArr[1];

                        $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                        $new_array = array_map(function ($k) use ($salary_categories_arr) {
                            return $salary_categories_arr[$k];
                        }, $keys);

                        $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                        $formulaText .= $salaryDescription;

                        $salaryDescription_arr = explode(' ', $salaryDescription);
                        $salaryDescription_arr = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription_arr);
                        $salaryCatID[$n]['cat'] = implode('_', $salaryDescription_arr) . '_' . $n;
                        $formulaDecode_arr[] = 'SUM(' . $salaryCatID[$n]['cat'] . ')';

                        $n++;

                    }else{

                        $catArr = explode('!', $formula_row);
                        $description = '';

                        if($catArr[1] == 'NMD'){
                            if(empty($date)){
                                $date = date('Y-m-d');
                            }
                           
                            $lastdate = date("Y-m-t", strtotime($date));

                            $endDate = explode('-',$lastdate)[2];
                            $formulaDecode_arr[] = (is_numeric($endDate)) ? $endDate : '_';
                        }

                    }
                }
            }

        }

        $formulaDecode = implode(' ', $formulaDecode_arr);

        $select_str2 = '';
        $whereInClause = '';
        $separator = '';

        foreach ($salaryCatID as $key1 => $row) {
            $select_str2 .= $separator . 'IF(salDec.salaryCategoryID=' . $row['ID'] . ', SUM(transactionAmount) , 0 ) AS ' . $row['cat'] . '';
            $whereInClause .= $separator . ' ' . $row['ID'];
            $separator = ',';
        }

        return array(
            'formulaDecode' => $formulaDecode,
            'select_str2' => $select_str2,
            'whereInClause' => $whereInClause,
        );

    }

    function delete_PayrollEmp()
    {
        $empID = $this->input->post('empID');
        $payrollID = $this->input->post('payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');

        $status = $this->getPayrollDetails($payrollID, $isNonPayroll);

        if ($status['confirmedYN'] != 1) {

            if ($isNonPayroll != 'Y') {
                $headerDetailTableName = 'srp_erp_payrollheaderdetails';
                $detailTableName = 'srp_erp_payrolldetail';
                $payGroupDetailTableName = 'srp_erp_payrolldetailpaygroup';
            } else {
                $headerDetailTableName = 'srp_erp_non_payrollheaderdetails';
                $detailTableName = 'srp_erp_non_payrolldetail';
                $payGroupDetailTableName = 'srp_erp_non_payrolldetailpaygroup';
            }

            $this->db->trans_start();

            $payDateMin = $payrollMonthDate = date('Y-m-d', strtotime($status['payrollYear'] . '-' . $status['payrollMonth'] . '-01'));
            $payDateMax = date('Y-m-d', strtotime($payrollMonthDate . ' +1 month'));
            $this->updatesSalaryProcessTables($payrollID, 0, $isNonPayroll, $payDateMin, $payDateMax, $empID);


            $this->db->query("DELETE FROM {$headerDetailTableName} WHERE payrollMasterID={$payrollID} AND EmpID={$empID}");
            $this->db->query("DELETE FROM {$detailTableName} WHERE payrollMasterID={$payrollID} AND empID={$empID}");
            $this->db->query("DELETE FROM {$payGroupDetailTableName} WHERE payrollMasterID={$payrollID} AND empID={$empID}");

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Failed to delete the employee'];
            } else {
                $this->db->trans_commit();

                return ['s', 'Employee deleted successfully.'];
            }
        } else {
            return ['e', 'This Payroll is already confirmed.<br/>You can not delete this employee'];
        }


    }

    function update_payrollEmpComment()
    {
        $comment = $this->input->post('comment');
        $payrollHeaderDetID = $this->input->post('payrollHeaderDetID');

        $isNonPayroll = $this->input->post('isNonPayroll');
        $headerDetailTableName = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';

        $data = ['payComment' => $comment];

        $this->db->trans_start();
        $this->db->where('payrollHeaderDetID', $payrollHeaderDetID)->update($headerDetailTableName, $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'Error in comment update process'];
        } else {
            $this->db->trans_commit();
            return ['s', 'Comment saved successfully.'];
        }
    }

    function createDate($year, $month)
    {
        $minDate = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
        $maxDate = date('Y-m-d', strtotime($minDate . ' +1 month'));

        return ['minDate' => $minDate, 'maxDate' => $maxDate];
    }

    function get_localization()
    {
        $segment = $this->input->post('segmentID');
        $output = $this->db->query("SELECT SUM(CASE WHEN srp_nationality.countryID = " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END) AS localEmployee,
        SUM(CASE WHEN srp_nationality.countryID != " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END) AS expatriateEmployee,
        (SUM(CASE WHEN srp_nationality.countryID = " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END) + SUM(CASE WHEN srp_nationality.countryID != " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END)) as totalEmployee,
        ROUND(((SUM(CASE WHEN srp_nationality.countryID = " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END)/(SUM(CASE WHEN srp_nationality.countryID = " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END) + SUM(CASE WHEN srp_nationality.countryID != " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END)))*100),0) as localization,
        seg.description
         FROM srp_employeesdetails LEFT JOIN (SELECT * FROM srp_erp_segment WHERE companyID = " . current_companyID() . ") seg ON seg.segmentID = srp_employeesdetails.segmentID LEFT JOIN srp_erp_company ON srp_employeesdetails.Erp_companyID = srp_erp_company.company_id LEFT JOIN srp_nationality ON srp_nationality.NId=srp_employeesdetails.NId  WHERE srp_employeesdetails.Erp_companyID = " . current_companyID() . " AND srp_employeesdetails.segmentID IN (" . join(',', $segment) . ") AND isDischarged = 0 AND empConfirmedYN = 1 AND isSystemAdmin = 0 GROUP BY srp_employeesdetails.segmentID")->result_array();
        return $output;
    }


    function get_salary_trend()
    {
        $month = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dece');
        $query = array();
        foreach ($month as $key => $val) {
            $query[] = "SUM(if(payrollMonth = $key,transactionAmount,0)) as $val";
        }
        $year = $this->input->post('year');
        $payrollMasterID = $this->db->query("SELECT * FROM srp_erp_payrollmaster WHERE payrollYear IN (" . join(',', $year) . ")")->result_array();
        $payrollMasterID = array_column($payrollMasterID, 'payrollMasterID');
        $output = $this->db->query("SELECT * FROM (SELECT " . join(',', $query) . ",'Expatriate' as description,payrollYear  FROM srp_erp_payrolldetail AS payrollDet
INNER JOIN srp_erp_payrollmaster ON srp_erp_payrollmaster.payrollMasterID = payrollDet.payrollMasterID
LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EidNo = payrollDet.empID AND isDischarged = 0
LEFT JOIN srp_nationality ON srp_nationality.NId=srp_employeesdetails.NId
                             WHERE NOT EXISTS (
            SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                                JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID=" . current_companyID() . "
                                WHERE payrollDet.detailTBID = groupMaster.payGroupID AND payrollDet.fromTB='PAY_GROUP'
        AND groupMaster.companyID=" . current_companyID() . " AND SSO_TB.employerContribution > 0
                             ) and payrollDet.payrollMasterID IN (" . join(',', $payrollMasterID) . ") AND payrollDet.companyID = " . current_companyID() . " AND srp_nationality.countryID != " . $this->common_data['company_data']['countryID'] . "  GROUP BY payrollYear
                          UNION SELECT " . join(',', $query) . ",'Local' as description,payrollYear  FROM srp_erp_payrolldetail AS payrollDet
INNER JOIN srp_erp_payrollmaster ON srp_erp_payrollmaster.payrollMasterID = payrollDet.payrollMasterID
LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EidNo = payrollDet.empID
LEFT JOIN srp_nationality ON srp_nationality.NId=srp_employeesdetails.NId
                             WHERE NOT EXISTS (
            SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                                JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID=" . current_companyID() . "
                                WHERE payrollDet.detailTBID = groupMaster.payGroupID AND payrollDet.fromTB='PAY_GROUP'
        AND groupMaster.companyID=" . current_companyID() . " AND SSO_TB.employerContribution > 0
                             ) and payrollDet.payrollMasterID IN (" . join(',', $payrollMasterID) . ") AND payrollDet.companyID = " . current_companyID() . " AND srp_nationality.countryID = " . $this->common_data['company_data']['countryID'] . "  GROUP BY payrollYear
                          UNION SELECT " . join(',', $query) . ",'Overall' as description,payrollYear  FROM srp_erp_payrolldetail AS payrollDet
INNER JOIN srp_erp_payrollmaster ON srp_erp_payrollmaster.payrollMasterID = payrollDet.payrollMasterID
LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EidNo = payrollDet.empID
LEFT JOIN srp_nationality ON srp_nationality.NId=srp_employeesdetails.NId
                             WHERE NOT EXISTS (
            SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                                JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID=" . current_companyID() . "
                                WHERE payrollDet.detailTBID = groupMaster.payGroupID AND payrollDet.fromTB='PAY_GROUP'
        AND groupMaster.companyID=" . current_companyID() . " AND SSO_TB.employerContribution > 0
                             ) and payrollDet.payrollMasterID IN (" . join(',', $payrollMasterID) . ") AND payrollDet.companyID = " . current_companyID() . " GROUP BY payrollYear
                          ) a GROUP BY a.payrollYear,a.description")->result_array();
        return $output;
    }


    function get_leave_history_report()
    {
        $empID = $this->input->post('empID');
        $empID = join(',', $empID);
        $leaveTypeID = $this->input->post('leaveTypeID');
        $leaveTypeID = join(',', $leaveTypeID);

        $date_format_policy = date_format_policy();
        $date_from = $this->input->post('date_from');
        $date_from = input_format_date($date_from, $date_format_policy);
        $date_to = $this->input->post('date_to');
        $date_to = input_format_date($date_to, $date_format_policy);
        $date = "";
        $companyID = current_companyID();
        if (!empty($date_from) && !empty($date_to)) {
            $date .= " AND ( startDate BETWEEN '" . $date_from . " 00:00:00' AND '" . $date_to . " 00:00:00')";
        }

        $qry = "SELECT leaveMasterID, documentCode, CONCAT( srp_employeesdetails.Ename2, ' - ', ECode ) AS empname,
                srp_employeesdetails.Ename2 as Ename2, srp_erp_leavetype.description, DATE(startDate) AS startDate,
                DATE(endDate) AS endDate, days, comments
                FROM srp_erp_leavemaster
                LEFT JOIN srp_erp_leavetype ON srp_erp_leavetype.leaveTypeID = srp_erp_leavemaster.leaveTypeID
                LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_leavemaster.empID
                WHERE approvedYN = 1 AND srp_erp_leavemaster.companyID = '{$companyID}' AND empID IN ({$empID}) 
	            AND (srp_erp_leavemaster.cancelledYN != 1 OR cancelledYN is null) {$date}
	            AND srp_erp_leavemaster.leaveTypeID IN ({$leaveTypeID}) ";

        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_onleave_history_report()
    {
        $empID = $this->input->post('empID_ol');
        $empID = join(',', $empID);
        $leaveTypeID = $this->input->post('leaveTypeID_ol');
        $leaveTypeID = join(',', $leaveTypeID);

        $date_format_policy = date_format_policy();
        $currentdate = $this->input->post('current_date');
        $current_date = input_format_date($currentdate, $date_format_policy);
        $date = "";
        $companyID = current_companyID();
        if (!empty($currentdate)) {
            //$date .= " AND ( startDate BETWEEN '" . $date_from . " 00:00:00' AND '" . $date_to . " 00:00:00')";
            $date .= " AND ( '".$current_date."' BETWEEN startDate AND endDate )";

        }

        $qry = "SELECT leaveMasterID, documentCode, CONCAT( srp_employeesdetails.Ename2, ' - ', ECode ) AS empname,
                srp_employeesdetails.Ename2 as Ename2, srp_erp_leavetype.description, DATE(startDate) AS startDate,
                DATE(endDate) AS endDate, days, comments
                FROM srp_erp_leavemaster
                LEFT JOIN srp_erp_leavetype ON srp_erp_leavetype.leaveTypeID = srp_erp_leavemaster.leaveTypeID
                LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_leavemaster.empID
                WHERE approvedYN = 1 AND srp_erp_leavemaster.companyID = '{$companyID}' AND empID IN ({$empID}) 
	            AND (srp_erp_leavemaster.cancelledYN != 1 OR cancelledYN is null) {$date}
	            AND srp_erp_leavemaster.leaveTypeID IN ({$leaveTypeID}) ";

        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function load_payment_voucher($bankTransferID)
    {
        $this->db->select('bankTransferID,payVoucherAutoId,PVcode');
        $this->db->where('companyID', current_companyID());
        $this->db->where('bankTransferID', $bankTransferID);
        $this->db->from('srp_erp_paymentvouchermaster');
        $data = $this->db->get()->result_array();
        return $data;
    }

    function get_salary_process_report()
    {
        $companyID = current_companyID();
        $filter_payroll = $filter = $groupBy = $select = "";
        $payrollMonth = trim($this->input->post('payrollMonth') ?? '');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $segment = $this->input->post('segmentID');
        $groupBy = $this->input->post('groupBy');
        $departmentID = $this->input->post('departmentID');

        $pYear = date('Y', strtotime($payrollMonth));
        $pMonth = date('m', strtotime($payrollMonth));

        if ($groupBy == 1) {
            $segmentID = join(',', $segment);
            $filter_payroll .= " AND segmentID IN ({$segmentID})";
            $filter .= " AND srp_employeesdetails.segmentID IN ({$segmentID})";
            $groupBy .= "srp_employeesdetails.segmentID,";
            $select = "CONCAT(
                        'Currency : ',
                        transactionCurrency,
                        ' : Segment : ',
                        IFNULL( srp_erp_segment.segmentCode, ' - ' )) currency,";
        }
        if($groupBy == 2) {
            $filter .= " AND srp_empdepartments.DepartmentMasterID IN (" . join(',', $departmentID) . ")";
            $groupBy .= "srp_empdepartments.DepartmentMasterID,";
            $select = "CONCAT(
                        'Currency : ',
                        transactionCurrency,
                        ' : Department : ',
                        IFNULL( DepartmentDes, ' - ' )) currency,";
        }

        $masterTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollmaster' : 'srp_erp_non_payrollmaster';
        $headerTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrollheaderdetails' : 'srp_erp_non_payrollheaderdetails';
        $detailTB = ($isNonPayroll != 'Y') ? 'srp_erp_payrolldetail' : 'srp_erp_non_payrolldetail';

        $payrollID_arr = $this->db->query("SELECT payrollMasterID FROM {$headerTB} AS t1
                                           JOIN (
                                              SELECT payrollMasterID AS payID FROM {$masterTB} WHERE companyID={$companyID}
                                              AND payrollYear={$pYear} AND payrollMonth={$pMonth} AND approvedYN=1
                                           ) AS payrollMaster ON payrollMaster.payID = t1.payrollMasterID
                                           WHERE companyID={$companyID} {$filter_payroll} GROUP BY payrollMasterID")->result_array();

        $payrollID_arr = implode(',', array_column($payrollID_arr, 'payrollMasterID'));

        $categorysalary = $this->db->query("SELECT salaryCategoryType,salaryCategoryID,salaryDescription,deductionPercntage,companyContributionPercentage
                                      FROM srp_erp_pay_salarycategories 
                                      WHERE companyID='{$companyID}' AND isPayrollCategory=1 ORDER BY salaryCategoryType ASC")->result_array();

        $querycat = '';
        if ($categorysalary) {
            foreach ($categorysalary as $cat) {
                $salaryDescription = str_replace(' ', '', $cat['salaryDescription']);
                $salaryDescription = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription);

                $querycat .= "SUM(IF(srp_erp_payrolldetail.salCatID = " . $cat['salaryCategoryID'] . " , transactionAmount, 0)) as " . $salaryDescription . ",";

            }
        }

        $sql = $this->db->query("SELECT {$masterTB}.payrollMasterID, EIdNo, ECode, EmpDesignationId, Ename1, Ename2, Ename3, 
                            Ename4, EmpShortCode, srp_employeesdetails.segmentID, {$select}
                            transactionCurrencyDecimalPlaces,detailType, $querycat salCatID,transactionCurrency, 
                            srp_empdepartments.DepartmentMasterID, DepartmentDes, srp_erp_segment.segmentCode
        FROM {$masterTB}
            LEFT JOIN {$detailTB} ON {$masterTB}.payrollMasterID = {$detailTB}.payrollMasterID
            LEFT JOIN srp_employeesdetails ON empID = EidNo
            LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = srp_employeesdetails.segmentID
            LEFT JOIN srp_erp_pay_salarycategories on srp_erp_pay_salarycategories.salaryCategoryID = srp_erp_payrolldetail.salCatID
            LEFT JOIN srp_empdepartments ON srp_employeesdetails.EIdNo = srp_empdepartments.EmpID AND isPrimary = 1
            LEFT JOIN srp_departmentmaster ON srp_departmentmaster.DepartmentMasterID = srp_empdepartments.DepartmentMasterID         
        WHERE approvedYN = 1 AND {$masterTB}.payrollMasterID IN ({$payrollID_arr}) {$filter} 
                AND {$masterTB}.companyID = '{$companyID}'
                AND NOT EXISTS (
                    SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                    JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID='{$companyID}'
                    WHERE {$detailTB}.detailTBID = groupMaster.payGroupID AND {$detailTB}.fromTB='PAY_GROUP'
                    AND groupMaster.companyID='{$companyID}' AND SSO_TB.employerContribution > 0
                ) and {$detailTB}.payrollMasterID IN ({$payrollID_arr}) 
                AND {$detailTB}.empID=srp_employeesdetails.EIdNo
        GROUP BY EIdNo, currency")->result_array();

        $sql2 = $this->db->query("SELECT  {$select} $querycat transactionCurrencyDecimalPlaces
               FROM {$masterTB}
               LEFT JOIN {$detailTB} ON {$masterTB}.payrollMasterID = {$detailTB}.payrollMasterID
               LEFT JOIN srp_employeesdetails ON empID = EidNo
                LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = srp_employeesdetails.segmentID
                LEFT JOIN srp_erp_pay_salarycategories ON srp_erp_pay_salarycategories.salaryCategoryID = srp_erp_payrolldetail.salCatID
                LEFT JOIN srp_empdepartments ON srp_employeesdetails.EIdNo = srp_empdepartments.EmpID AND isPrimary = 1
                LEFT JOIN srp_departmentmaster ON srp_departmentmaster.DepartmentMasterID = srp_empdepartments.DepartmentMasterID 
               WHERE approvedYN = 1 AND {$masterTB}.payrollMasterID IN ({$payrollID_arr}) {$filter} AND {$masterTB}.companyID = '{$companyID}'
               AND NOT EXISTS (
                   SELECT payGroupID FROM srp_erp_paygroupmaster AS groupMaster
                   JOIN srp_erp_socialinsurancemaster AS SSO_TB ON SSO_TB.socialInsuranceID=groupMaster.socialInsuranceID AND SSO_TB.companyID='{$companyID}'
                   WHERE {$detailTB}.detailTBID = groupMaster.payGroupID AND {$detailTB}.fromTB='PAY_GROUP' AND
                   groupMaster.companyID='{$companyID}' AND SSO_TB.employerContribution > 0
               ) and {$detailTB}.payrollMasterID IN ({$payrollID_arr})
               AND {$detailTB}.empID=srp_employeesdetails.EIdNo 
             GROUP BY currency")->result_array();

        $data['details'] = $sql;
        $data['currency'] = $sql2;
        $data['categorysalary'] = $categorysalary;

        return $data;
    }
    
    function get_EmpNonBankTransferDet_new($empID, $payrollMasterID, $isNonPayroll)
    {
        if ($isNonPayroll != 'Y') {
            $headerTB = 'srp_erp_payrollheaderdetails';
            $payTB = 'srp_erp_payroll_salarypayment_without_bank';
        } else {
            $headerTB = 'srp_erp_non_payrollheaderdetails';
            $payTB = 'srp_erp_non_payroll_salarypayment_without_bank';
        }
        return $this->db->query("SELECT sal.empID, ECode, CONCAT( Ename1, ' ', Ename2, ' ', Ename3, ' ', Ename4) AS empName,
                                 sal.transactionCurrency, sal.transactionAmount, sal.transactionCurrencyDecimalPlaces AS dPlace,
                                 isPaid, payByBankID, bankName, chequeNo
                                 FROM {$payTB} AS sal
                                 JOIN {$headerTB} AS header ON sal.empID = header.EmpID
                                 WHERE sal.empID IN ({$empID}) 	AND isPaid = 1  AND header.payrollMasterID={$payrollMasterID}
                                 AND sal.payrollMasterID={$payrollMasterID} ORDER BY sal.transactionCurrency")->result_array();
    }
    function save_empNonBankPay_new()
    {
        $empID = $this->input->post('hidden_empID');
        $payrollMasterID = $this->input->post('hidden_payrollID');
        $isNonPayroll = $this->input->post('isNonPayroll');
        $payType = $this->input->post('payType');
        $paymentDate = $this->input->post('paymentDate');
        $companyFinance =  $this->get_finance_details($paymentDate);
        if(empty($companyFinance))
        {
            return array('e', 'Selected Date Is Not In Finance year');
            exit();
        }

        $bankName['bankName'] = null;
        $empPayBank = null;
        $chequeNo = null;
        $empPayBank = $this->input->post('empPayBank');
        if ($payType == 'By Cheque') {

            $chequeNo = $this->input->post('chequeNo');
        }
        $bankName = $this->db->query("SELECT bankName FROM srp_erp_chartofaccounts WHERE GLAutoID={$empPayBank}")->row_array();
        $employee = explode(',',$empID);
        foreach ($employee as $empval) {
            $where = array('empID' => $empval, 'payrollMasterID' => $payrollMasterID);
            $upData = array(
                'isPaid' => 1,
                'payByBankID' => $empPayBank,
                'bankName' => $bankName['bankName'],
                'chequeNo' => $chequeNo,
                'processedDate' => $paymentDate
            );

            $tableName = ($isNonPayroll != 'Y') ? 'srp_erp_payroll_salarypayment_without_bank' : 'srp_erp_non_payroll_salarypayment_without_bank';
            $this->db->where($where)->update($tableName, $upData);

        }
        if ($this->db->affected_rows() > 0) {
           $this->generatepaymentvoucher($empID,$payrollMasterID,$payType);
            return array('s', 'Salary transfer is done.');



        } else {
            return array('e', 'Error in process');
        }
    }
    function generatepaymentvoucher($empID,$payrollMasterID,$payType)
    {
        $companyID = current_companyID();
        $empID = $empID;
        $payrollID = $payrollMasterID;
     /*   $transactionAmount = trim($this->input->post('transactionAmount') ?? '');*/
        $withoutbankdet = $this->db->query("select 
                                                payByBankID,
                                                bankName,
                                                chequeNo,
                                                processedDate,
                                                srp_erp_currencymaster.currencyID,
                                                srp_erp_payrollmaster.documentCode
                                                from 
                                                srp_erp_payroll_salarypayment_without_bank
                                                LEFT JOIN srp_erp_currencymaster on srp_erp_currencymaster.CurrencyCode = srp_erp_payroll_salarypayment_without_bank.transactionCurrency
                                                LEFT JOIN srp_erp_payrollmaster ON srp_erp_payrollmaster.payrollMasterID = srp_erp_payroll_salarypayment_without_bank.payrollMasterID
                                                WHERE srp_erp_payroll_salarypayment_without_bank.companyID = $companyID 
                                                and srp_erp_payroll_salarypayment_without_bank.payrollMasterID = $payrollID
                                                And empID IN ($empID)")->result_array();

        $processedDate = array_unique(array_column($withoutbankdet,'processedDate'));
        $transactioncurrencyID = array_unique(array_column($withoutbankdet,'currencyID'));
        $bankID = array_unique(array_column($withoutbankdet,'payByBankID'));
        $chequeno = array_unique(array_column($withoutbankdet,'chequeNo'));
        $chequeDate= array_unique(array_column($withoutbankdet,'processedDate'));
        $payrollHeadercode = array_unique(array_column($withoutbankdet,'documentCode'));

        $segmentID = $this->db->query("select 
                                           segmentCode,
                                           segmentID
                                           from 
                                           srp_erp_segment 
                                           where 
                                           companyID = $companyID 
                                           AND isDefault =1 ")->row_array();

/*
        echo '<pre>'; print_r($transactioncurrencyID);
        exit();*/

        $companyFinance = $this->get_finance_details($processedDate[0]);
        if(empty($companyFinance))
        {
            return array('e', 'Selected Date Is Not In Finance year');
            exit();
        }else
        {
            $this->db->trans_start();
            $this->load->library('approvals');
            $this->load->library('sequence');
            $companyID = current_companyID();
            $glDetailD = $this->db->query("SELECT coa.* FROM srp_erp_chartofaccounts coa
                                JOIN srp_erp_companycontrolaccounts cnt on coa.GLAutoID=cnt.GLAutoID
                                where coa.companyID=$companyID and cnt.controlAccountType='PCA'")->row_array();
            foreach ($transactioncurrencyID as $val)
            {
                $bank_detail = fetch_gl_account_desc($bankID[0]);
                
                $currencyCode = $this->db->query("select CurrencyCode from srp_erp_currencymaster where currencyID = $val ")->row('CurrencyCode');
                $transactionamount = $this->db->query("select 
                                                           IFNULL(sum(transactionAmount),0) as transactionAmount,
                                                           GROUP_CONCAT(empID) as employee,
                                                           CurrencyCode
                                                           from 
                                                           srp_erp_payroll_salarypayment_without_bank
                                                           LEFT JOIN srp_erp_currencymaster ON srp_erp_currencymaster.CurrencyCode = srp_erp_payroll_salarypayment_without_bank.transactionCurrency
                                                           WHERE 
                                                           companyID = $companyID 
                                                           AND payrollMasterID = $payrollID
                                                           AND currencyID = $val
                                                           AND empID IN ($empID)")->row_array();

                $payvoucherID = $this->db->query("SELECT
                                                           MAX(payVoucherAutoId) as payVoucherAutoId,
                                                           PVcode,
                                                           transactionCurrencyID
                                                           FROM 
                                                           `srp_erp_paymentvouchermaster` 
                                                           WHERE 
                                                           companyID = $companyID 
                                                           AND isDeleted = 1 
                                                           AND emppayrollMasterID = {$payrollID} 
                                                           AND transactionCurrencyID  = {$val} 
                                                           AND bankGLAutoID = {$bank_detail['GLAutoID']}
                                                           GROUP BY transactionCurrencyID")->row_array();

                if(!empty($payvoucherID)){ 
                    $data['documentID'] = 'PV';
                    $data['companyFinanceYearID'] = $companyFinance['companyFinanceYearID'];
                    $data['companyFinanceYear'] = $companyFinance['companyFinanceYear'];
                    $data['FYBegin'] = ($companyFinance['beginingDate']);
                    $data['FYEnd'] = ($companyFinance['endingDate']);
                    $data['companyFinancePeriodID'] = ($companyFinance['companyFinancePeriodID']);
                    $data['PVdate'] = $processedDate[0];
                    $data['PVNarration'] = 'Employee Without Bank Details';
                    $data['accountPayeeOnly'] = 0;
                    $data['emppayrollMasterID'] = $payrollMasterID;
    
                   // $invYear = date("Y", strtotime($data['PVdate']));
                   // $invMonth = date("m", strtotime($data['PVdate']));
                   // $data['PVcode'] = $this->sequence->sequence_generator_fin($data['documentID'], $data['companyFinanceYearID'], $invYear, $invMonth);
                  
    
                    $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
                    $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
                    $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
                    $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
                    $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
                    $data['PVbank'] = $bank_detail['bankName'];
                    $data['PVbankBranch'] = $bank_detail['bankBranch'];
                    $data['PVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
                    $data['PVbankAccount'] = $bank_detail['bankAccountNumber'];
                    $data['PVbankType'] = $bank_detail['subCategory'];
                    if($chequeno[0]!=null)
                    {
                        $data['PVchequeDate'] = $chequeDate[0];
                        $data['PVchequeNo'] = $chequeno[0];
                    }else {
                        $data['PVchequeDate'] = null;
                        $data['PVchequeNo'] = null;
                    }
                    $data['modeOfPayment'] = ($payType == 'By Cash' ? 1 : 2);
                    $data['pvType'] = 'Direct';
                    $data['isSytemGenerated'] = '1';
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $data['transactionCurrencyID'] = $val;
                    $data['transactionExchangeRate'] = 1;
                    $data['transactionCurrency'] = $currencyCode;
                    $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
                    $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
                    $data['companyLocalExchangeRate'] = $default_currency['conversion'];
                    $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
                    $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                    $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
                    $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                    $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
                    $data['partyType'] = 'DIR';
                    $data['partyName'] = 'Payroll Transfer';
                    $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
                    $data['partyCurrency'] = $data['companyLocalCurrency'];
                    $data['partyExchangeRate'] = $data['transactionExchangeRate'];
                    $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
                    $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
                    $data['partyExchangeRate'] = $partyCurrency['conversion'];
                    $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['confirmedYN'] = 1;
                    $data['confirmedByEmpID'] = $this->common_data['current_userID'];
                    $data['confirmedByName'] = $this->common_data['current_user'];
                    $data['confirmedDate'] = $this->common_data['current_date'];
                    $data['segmentID'] = $segmentID['segmentID'];
                    $data['segmentCode'] = $segmentID['segmentCode'];
                    $data['referenceNo'] = $payrollHeadercode[0];
                    $data['isDeleted'] = 0;
                   
                    $this->db->where('payVoucherAutoId', $payvoucherID['payVoucherAutoId']);
                    $this->db->update('srp_erp_paymentvouchermaster', $data);
                    $last_id = $payvoucherID['payVoucherAutoId'];
                
                    $dataD['payVoucherAutoId'] = $last_id;
                    $dataD['type'] = 'GL';
                    $dataD['referenceNo'] = $payrollHeadercode[0];
                    $dataD['GLAutoID'] = $glDetailD['GLAutoID'];
                    $dataD['systemGLCode'] = $glDetailD['systemAccountCode'];
                    $dataD['GLCode'] = $glDetailD['GLSecondaryCode'];
                    $dataD['GLDescription'] = $glDetailD['GLDescription'];
                    $dataD['GLType'] = $glDetailD['subCategory'];
                    $dataD['description'] = 'Employee Without Bank Details';
                    $dataD['transactionCurrencyID'] = $data['transactionCurrencyID'];
                    $dataD['transactionCurrency'] = $data['transactionCurrency'];
                    $dataD['transactionExchangeRate'] = $data['transactionExchangeRate'];
    
                    $dataD['transactionAmount'] = $transactionamount['transactionAmount'];
    
                    $dataD['transactionCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
                    $dataD['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
                    $dataD['companyLocalCurrency'] = $data['companyLocalCurrency'];
                    $dataD['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];
                    //$dataD['companyLocalAmount'] = $dataD['transactionAmount']/$dataD['companyLocalExchangeRate'];
                    $dataD['companyLocalAmount'] = round(($dataD['transactionAmount'] / $dataD['companyLocalExchangeRate']), $data['companyLocalCurrencyDecimalPlaces']);
                    $dataD['companyLocalCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
                    $dataD['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];
                    $dataD['companyReportingCurrency'] = $data['companyReportingCurrency'];
                    $dataD['companyReportingExchangeRate'] = $data['companyReportingExchangeRate'];
                    //$dataD['companyReportingAmount'] = $dataD['transactionAmount']/$dataD['companyReportingExchangeRate'];
                    $dataD['companyReportingAmount'] = round(($dataD['transactionAmount'] / $dataD['companyReportingExchangeRate']), $data['companyReportingCurrencyDecimalPlaces']);
                    $dataD['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces'];
                    $dataD['segmentID'] = $segmentID['segmentID'];
                    $dataD['segmentCode'] = $segmentID['segmentCode'];
                    $dataD['companyID'] = $companyID;
                    $dataD['companyCode'] = current_companyCode();
                    $dataD['createdUserGroup'] = $this->common_data['user_group'];
                    $dataD['createdPCID'] = $this->common_data['current_pc'];
                    $dataD['createdUserID'] = $this->common_data['current_userID'];
                    $dataD['createdUserName'] = $this->common_data['current_user'];
                    $dataD['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_paymentvoucherdetail', $dataD);

                }else{ 
                    $data['documentID'] = 'PV';
                    $data['companyFinanceYearID'] = $companyFinance['companyFinanceYearID'];
                    $data['companyFinanceYear'] = $companyFinance['companyFinanceYear'];
                    $data['FYBegin'] = ($companyFinance['beginingDate']);
                    $data['FYEnd'] = ($companyFinance['endingDate']);
                    $data['companyFinancePeriodID'] = ($companyFinance['companyFinancePeriodID']);
                    $data['PVdate'] = $processedDate[0];
                    $data['PVNarration'] = 'Employee Without Bank Details';
                    $data['accountPayeeOnly'] = 0;
                    $data['emppayrollMasterID'] = $payrollMasterID;
    
                    $invYear = date("Y", strtotime($data['PVdate']));
                    $invMonth = date("m", strtotime($data['PVdate']));
                    $data['PVcode'] = $this->sequence->sequence_generator_fin($data['documentID'], $data['companyFinanceYearID'], $invYear, $invMonth);
                  
    
                    $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
                    $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
                    $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
                    $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
                    $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
                    $data['PVbank'] = $bank_detail['bankName'];
                    $data['PVbankBranch'] = $bank_detail['bankBranch'];
                    $data['PVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
                    $data['PVbankAccount'] = $bank_detail['bankAccountNumber'];
                    $data['PVbankType'] = $bank_detail['subCategory'];
                    if($chequeno[0]!=null)
                    {
                        $data['PVchequeDate'] = $chequeDate[0];
                        $data['PVchequeNo'] = $chequeno[0];
                    }else {
                        $data['PVchequeDate'] = null;
                        $data['PVchequeNo'] = null;
                    }
                    $data['modeOfPayment'] = ($payType == 'By Cash' ? 1 : 2);
                    $data['pvType'] = 'Direct';
                    $data['isSytemGenerated'] = '1';
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $data['transactionCurrencyID'] = $val;
                    $data['transactionExchangeRate'] = 1;
                    $data['transactionCurrency'] = $currencyCode;
                    $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
                    $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
                    $data['companyLocalExchangeRate'] = $default_currency['conversion'];
                    $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
                    $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                    $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
                    $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                    $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
                    $data['partyType'] = 'DIR';
                    $data['partyName'] = 'Payroll Transfer';
                    $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
                    $data['partyCurrency'] = $data['companyLocalCurrency'];
                    $data['partyExchangeRate'] = $data['transactionExchangeRate'];
                    $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
                    $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
                    $data['partyExchangeRate'] = $partyCurrency['conversion'];
                    $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['confirmedYN'] = 1;
                    $data['confirmedByEmpID'] = $this->common_data['current_userID'];
                    $data['confirmedByName'] = $this->common_data['current_user'];
                    $data['confirmedDate'] = $this->common_data['current_date'];
                    $data['segmentID'] = $segmentID['segmentID'];
                    $data['segmentCode'] = $segmentID['segmentCode'];
                    $data['referenceNo'] = $payrollHeadercode[0];
                    $this->db->insert('srp_erp_paymentvouchermaster', $data);
                    $last_id = $this->db->insert_id();
    
                    $dataD['payVoucherAutoId'] = $last_id;
                    $dataD['type'] = 'GL';
                    $dataD['referenceNo'] = $payrollHeadercode[0];
                    $dataD['GLAutoID'] = $glDetailD['GLAutoID'];
                    $dataD['systemGLCode'] = $glDetailD['systemAccountCode'];
                    $dataD['GLCode'] = $glDetailD['GLSecondaryCode'];
                    $dataD['GLDescription'] = $glDetailD['GLDescription'];
                    $dataD['GLType'] = $glDetailD['subCategory'];
                    $dataD['description'] = 'Employee Without Bank Details';
                    $dataD['transactionCurrencyID'] = $data['transactionCurrencyID'];
                    $dataD['transactionCurrency'] = $data['transactionCurrency'];
                    $dataD['transactionExchangeRate'] = $data['transactionExchangeRate'];
    
                    $dataD['transactionAmount'] = $transactionamount['transactionAmount'];
    
                    $dataD['transactionCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
                    $dataD['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
                    $dataD['companyLocalCurrency'] = $data['companyLocalCurrency'];
                    $dataD['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];
                    //$dataD['companyLocalAmount'] = $dataD['transactionAmount']/$dataD['companyLocalExchangeRate'];
                    $dataD['companyLocalAmount'] = round(($dataD['transactionAmount'] / $dataD['companyLocalExchangeRate']), $data['companyLocalCurrencyDecimalPlaces']);
                    $dataD['companyLocalCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
                    $dataD['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];
                    $dataD['companyReportingCurrency'] = $data['companyReportingCurrency'];
                    $dataD['companyReportingExchangeRate'] = $data['companyReportingExchangeRate'];
                    //$dataD['companyReportingAmount'] = $dataD['transactionAmount']/$dataD['companyReportingExchangeRate'];
                    $dataD['companyReportingAmount'] = round(($dataD['transactionAmount'] / $dataD['companyReportingExchangeRate']), $data['companyReportingCurrencyDecimalPlaces']);
                    $dataD['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces'];
                    $dataD['segmentID'] = $segmentID['segmentID'];
                    $dataD['segmentCode'] = $segmentID['segmentCode'];
                    $dataD['companyID'] = $companyID;
                    $dataD['companyCode'] = current_companyCode();
                    $dataD['createdUserGroup'] = $this->common_data['user_group'];
                    $dataD['createdPCID'] = $this->common_data['current_pc'];
                    $dataD['createdUserID'] = $this->common_data['current_userID'];
                    $dataD['createdUserName'] = $this->common_data['current_user'];
                    $dataD['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_paymentvoucherdetail', $dataD);

                    $autoApproval = get_document_auto_approval('PV');
                    if($autoApproval == 0){
                        $approvals_status = $this->approvals->auto_approve($last_id, 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $data['PVcode'], $data['PVdate']);

                        if($approvals_status == 1){
                            $this->Payment_voucher_model->save_pv_approval(0, $last_id, 1, 'Auto Approved');
                        }
                    }else{
                        $approvals_status = $this->approvals->CreateApproval('PV',$last_id, $data['PVcode'], 'Payment Voucher', 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 0, $data['PVdate'], 0, $data['PVdate']);
                    }




                }
               
                $this->db->trans_complete();
                $this->db->query("UPDATE srp_erp_payroll_salarypayment_without_bank 
                                         SET payVoucherAutoId = $last_id
                                         WHERE
	                                     companyID = $companyID 
	                                     AND payrollMasterID = $payrollID
	                                     AND empID IN ({$transactionamount['employee']})");

            }

        }
    }
    function get_finance_details($processedDate)
    {
        $companyID = current_companyID();
        return  $this->db->query("SELECT
	                                            srp_erp_companyfinanceperiod.companyFinanceYearID,
	                                            CONCAT( beginingDate, ' - ',endingDate ) AS companyFinanceYear,
	                                            beginingDate,
	                                            endingDate,
	                                            companyFinancePeriodID
                                                FROM
	                                            srp_erp_companyfinanceperiod
	                                            LEFT JOIN srp_erp_companyfinanceyear ON srp_erp_companyfinanceyear.companyFinanceYearID=srp_erp_companyfinanceperiod.companyFinanceYearID
                                                WHERE
	                                            srp_erp_companyfinanceperiod.companyID =$companyID
	                                            AND srp_erp_companyfinanceperiod.isActive = 1
	                                            AND '{$processedDate}' BETWEEN dateFrom AND dateTo")->row_array();
    }
    function send_payslipnotification()
    {
        $companyID = current_companyID();
        $type = trim($this->input->post('type') ?? '');
        $payrollMasterID  = trim($this->input->post('payrollID') ?? '');
        $bankTransID  = trim($this->input->post('bankTransID') ?? '');
        $isNonPayroll = trim($this->input->post('isnonpayroll') ?? '');
        $empID_wb = trim($this->input->post('employeeID') ?? '');

        if($type ==1)
        {
            $banktransferDetail = $this->processed_bankTransferData_email($payrollMasterID,$bankTransID,$isNonPayroll);
        }else{
            $banktransferDetail = $this->db->query("select 
                                                        EEmail,
                                                        EIdNo as empID,
                                                        Ename2 as empName
                                                        from 
                                                        srp_employeesdetails 
                                                        where 
                                                        Erp_companyID = $companyID 
                                                        AND EIdNo IN ($empID_wb)")->result_array();
        }
        $payrolldetail = $this->db->query("SELECT monthname(str_to_date(payrollMonth,'%m')) as payrollMonth,payrollYear from srp_erp_payrollmaster where companyID = $companyID AND payrollMasterID = $payrollMasterID")->row_array();
        $msg = ($type==1?"Your salary for {$payrolldetail['payrollMonth']} {$payrolldetail['payrollYear']} has been credited to your bank a/c <br>  Please find attached pay slip for {$payrolldetail['payrollMonth']} {$payrolldetail['payrollYear']}":"Please find attached pay slip for {$payrolldetail['payrollMonth']} {$payrolldetail['payrollYear']}");

        if(!empty($banktransferDetail))
        {
            foreach ($banktransferDetail as $email)
            {
                if($email['EEmail']!='')
                {
                    $data['masterData'] = $this->getPayrollDetails($payrollMasterID, $isNonPayroll);
                    $documentCode = ($isNonPayroll != 'Y') ? 'SP' : 'SPN';
                    $code = ($isNonPayroll != 'Y') ? 'PT' : 'NPT';
                    $template = getPolicyValues($code, $documentCode);
                    if ($template == 'Envoy') {
                        $data['details'] = $this->get_empPaySlipDet($email['empID'], $payrollMasterID, $isNonPayroll);
                        $pageSize = 'A5';
                        if (!empty($data['details'])) {
                            $this->load->model('Employee_model');
                            $html = $this->load->view('system/hrm/print/pay_slip_print_envoy', $data, true);
                        } else {
                            $html = 'No data';
                        }
                    }else if ($template == 'Aitken') {
                        $data['details'] = $this->get_empPaySlipDet($email['empID'], $payrollMasterID, $isNonPayroll);
                        $pageSize = 'A5';
                        if (!empty($data['details'])) {
                            $this->load->model('Employee_model');
                            $html = $this->load->view('system/hrm/print/pay_slip_print_aitken', $data, true);
                        } else {
                            $html = 'No data';
                        }
                    } else if ($template == 0) {
                        $data['details'] = $this->get_empPaySlipDet($email['empID'], $payrollMasterID, $isNonPayroll);
                        $pageSize = 'A4';
                        if (!empty($data['details'])) {
                            $this->load->model('Employee_model');
                            $data['leaveDet'] = false;
                            $html = $this->load->view('system/hrm/print/paySlipPrint', $data, true);
                        } else {
                            $html = 'No data';
                        }
                    } else {

                        $data['details'] = $this->fetchPaySheetData_employee($payrollMasterID, $email['empID'], $isNonPayroll);
                        $pageSize = 'A5';
                        if (!empty($data['details'])) {
                            $data['header_det'] = $this->templateDetails($template);
                            $html = $this->load->view('system/hrm/print/paySlipPrint_template', $data, true);
                        } else {
                            $html = 'No data';
                        }
                    }

                    $this->load->library('pdf');
                    $path = UPLOAD_PATH.base_url().'/uploads/payslip/Pay slip - '. $email['empName'].' - '.$payrolldetail['payrollMonth'].' '.$payrolldetail['payrollYear'].''.".pdf";
                    $this->pdf->save_pdf($html, $pageSize, 1, $path);


                    $param = array();
                    $param["empName"] = $email['empName'];
                    $param["body"] = ''.$msg.'
                                        <table border="0px">
                                          </table>';
                    $mailData = [
                        'approvalEmpID' => '',
                        'documentCode' => '',
                        'toEmail' => $email['EEmail'],
                        'subject' => 'Your salary for '.$payrolldetail['payrollMonth'].' '.$payrolldetail['payrollYear'].'',
                        'param' => $param
                    ];
                    send_approvalEmail($mailData,1,$path);
                    @unlink($path);
                    $token_android = firebaseToken($email['empID'], 'android');
                    $token_ios = firebaseToken($email['empID'], 'apple');
                    $this->load->library('firebase_notification');
                    if(!empty($token_android)) {
                        $this->firebase_notification->sendFirebasePushNotification("Payslip has been generated", 'You can view your payslip for '.$payrolldetail['payrollMonth'].' '.$payrolldetail['payrollYear'].'', $token_android, 10," ", " ", $bankTransID, "android");
                    }
                    if(!empty($token_ios)) {
                        $this->firebase_notification->sendFirebasePushNotification("Payslip has been generated", 'You can view your payslip for '.$payrolldetail['payrollMonth'].' '.$payrolldetail['payrollYear'].'', $token_ios, 10, " ", " ", $bankTransID, "apple");
                    }
                }
            }
            if($type ==1)
            {
                if ($isNonPayroll != 'Y') {
                    $bankTrTB = 'srp_erp_pay_banktransfermaster';
                } else {
                    $bankTrTB = 'srp_erp_pay_non_banktransfermaster';
                }
            $this->db->query("UPDATE $bankTrTB SET notificationYN = 1 where companyID = $companyID AND bankTransferID =$bankTransID");

            }else {
                if ($isNonPayroll != 'Y') {
                    $bankTrTB = 'srp_erp_payroll_salarypayment_without_bank';
                } else {
                    $bankTrTB = 'srp_erp_non_payroll_salarypayment_without_bank';
                }
                $this->db->query("UPDATE $bankTrTB 
                                         SET notificationYN = 1
                                         WHERE
	                                     companyID = $companyID 
	                                     AND payrollMasterID = $payrollMasterID
	                                     AND empID IN ({$empID_wb})");
            }
            return array('s', 'Email Send Successfully.');
        }
    }
    function processed_bankTransferData_email($payrollMasterID, $bankTransID, $isNonPayroll)
    {
        if ($isNonPayroll != 'Y') {
            $headerTB = 'srp_erp_payrollheaderdetails';
            $bankTrTB = 'srp_erp_pay_banktransfer';
        } else {
            $headerTB = 'srp_erp_non_payrollheaderdetails';
            $bankTrTB = 'srp_erp_pay_non_banktransfer';
        }

        $sortOrder_policy = getPolicyValues('PSO', 'All');
        $sortOrder = 'header.empID DESC';
        switch ($sortOrder_policy){
            case 1: $sortOrder = 'header.ECode ASC'; break;
            case 2: $sortOrder = 'header.ECode DESC'; break;
        }

        $query = $this->db->query("SELECT bankTransferDetailID, trans.empID, header.ECode, header.Ename2 AS empName, header.EmpShortCode AS name_in_bank_slip, trans.bankName,
                                    accountNo, acc_holderName, salaryTransferPer, trans.branchName, swiftCode, trans.transactionAmount, trans.transactionCurrency,
                                    trans.transactionCurrencyDecimalPlaces, trans.companyLocalCurrency, trans.companyLocalAmount, trans.bankID,
                                    trans.companyLocalCurrencyDecimalPlaces, trans.bankCode, trans.branchCode,EEmail
                                    FROM {$bankTrTB} AS trans
                                    JOIN {$headerTB} AS header ON trans.empID = header.EmpID    
                                    LEFT JOIN srp_employeesdetails empdetail ON empdetail.EIdNo = header.EmpID                           
                                    WHERE trans.payrollMasterID = {$payrollMasterID} AND header.payrollMasterID = {$payrollMasterID} AND bankTransferID = {$bankTransID}
                                    ORDER BY trans.bankName, trans.transactionCurrency, {$sortOrder}");


        return $query->result_array();

    }

}

