<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sequence
{

    function Sequence()
    {
        $CI = &get_instance();
    }

    function sequence_generator_fin($documentID, $companyFinanceYearID = NULL, $documentYear = NULL, $documentMonth = NULL, $count = 0, $purchaseOrderID = NULL)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $company_code = $CI->common_data['company_data']['company_code'];
        $CI->db->query("LOCK TABLES srp_erp_financeyeardocumentcodemaster WRITE, srp_erp_documentcodemaster WRITE, srp_erp_purchaseordermaster WRITE, srp_erp_serializationtypes WRITE");
        //$policy    = getPolicyValues('DC', 'All');
        //$isFinance = $CI->db->query("select * from `srp_erp_documentcodes` where isFinance=1 AND documentID='{$documentID}'")->row_array();
        $isFinanceYN = $CI->db->query("select isFYBasedSerialNo from `srp_erp_documentcodemaster` where documentID='{$documentID}' AND companyID = $companyID ")->row_array();

        $CI->db->select('serializationType');
        $CI->db->where('documentID', 'PO');
        $CI->db->where('companyID', $companyID);
        $CI->db->from('srp_erp_documentcodemaster');
        $serializationType = $CI->db->get()->row_array();

        if ($isFinanceYN['isFYBasedSerialNo'] == 1) {
            if ($companyFinanceYearID == NULL) {
                var_dump('Finance year  not found');
                exit;
            }
            if ($documentYear == NULL) {
                var_dump('document Year  not found');
                exit;
            }
            if ($documentMonth == NULL) {
                var_dump('document Month  not found');
                exit;
            }

            $sqlSerialNo = $CI->db->query("select serialNo from `srp_erp_financeyeardocumentcodemaster` where documentID='{$documentID}' AND companyID = $companyID AND financeYearID = $companyFinanceYearID  ")->row_array();
            $serialNo = $sqlSerialNo['serialNo'] + 1;
            $CI = &get_instance();
            $code = '';
            $company_id = $CI->common_data['company_data']['company_id'];
            $company_code = $CI->common_data['company_data']['company_code'];
            $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
            $CI->db->from('srp_erp_financeyeardocumentcodemaster');
            $CI->db->where('documentID', $documentID);
            $CI->db->where('companyID', $company_id);
            $CI->db->where('financeYearID', $companyFinanceYearID);
            $data = $CI->db->get()->row_array();
            if (empty($data)) {
                $data_arr = [
                    'documentID' => $documentID,
                    'prefix' => $documentID,
                    'companyID' => $company_id,
                    'financeYearID' => $companyFinanceYearID,
                    'companyCode' => $CI->common_data['company_data']['company_code'],
                    'createdUserGroup' => $CI->common_data['user_group'],
                    'createdUserID' => $CI->common_data['current_userID'],
                    'createdUserName' => $CI->common_data['current_user'],
                    'createdPCID' => $CI->common_data['current_pc'],
                    'createdDateTime' => $CI->common_data['current_date'],
                    'modifiedUserID' => $CI->common_data['current_userID'],
                    'modifiedUserName' => $CI->common_data['current_user'],
                    'modifiedPCID' => $CI->common_data['current_pc'],
                    'modifiedDateTime' => $CI->common_data['current_date'],
                    'startSerialNo' => 1,
                    'formatLength' => 6,
                    'format_1' => 'prefix',
                    'format_2' => '/',
                    'format_3' => 'YYYY',
                    'format_4' => NULL,
                    'format_5' => NULL,
                    'format_6' => NULL,
                    'serialNo' => 1,
                ];

                $CI->db->insert('srp_erp_financeyeardocumentcodemaster', $data_arr);
                $data = $data_arr;
            } else {
                $CI->db->query("UPDATE srp_erp_financeyeardocumentcodemaster SET serialNo = {$serialNo}  WHERE documentID='{$documentID}' AND companyID = '{$company_id}' AND financeYearID = '{$companyFinanceYearID}'");
                $data['serialNo'] = $serialNo;
            }
            if ($data['format_1']) {
                if ($data['format_1'] == 'prefix') {
                    $code .= $data['prefix'];
                }
                if ($data['format_1'] == 'yyyy') {
                    $code .= $documentYear;
                }
                if ($data['format_1'] == 'mm') {
                    $code .= $documentYear;
                }
                if ($data['format_1'] == 'yy') {
                    $year = substr($documentYear, -2);
                    $code .= $year;
                }
            }
            if ($data['format_2']) {
                $code .= $data['format_2'];
            }
            if ($data['format_3']) {
                if ($data['format_3'] == 'mm') {
                    $code .= $documentMonth;
                }
                if ($data['format_3'] == 'yyyy') {
                    $code .= $documentYear;
                }
                if ($data['format_3'] == 'prefix') {
                    $code .= $data['prefix'];
                }
                if ($data['format_3'] == 'yy') {
                    $year = substr($documentYear, -2);
                    $code .= $year;
                }
            }
            if ($data['format_4']) {
                $code .= $data['format_4'];
            }
            if ($data['format_5']) {
                if ($data['format_5'] == 'mm') {
                    $code .= $documentMonth;
                }
                if ($data['format_5'] == 'yyyy') {
                    $code .= $documentYear;
                }
                if ($data['format_5'] == 'prefix') {
                    $code .= $data['prefix'];
                }
                if ($data['format_5'] == 'yy') {
                    $year = substr($documentYear, -2);
                    $code .= $year;
                }
            }
            if ($data['format_6']) {
                $code .= $data['format_6'];
            }
            if ($count == 0) {
                $number = $data['serialNo'];
            } else {
                $number = $count;
            }
            $CI->db->query("UNLOCK TABLES");
            return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));

        } else {
            $CI = &get_instance();
            $code = '';
            $company_id = $CI->common_data['company_data']['company_id'];
            $company_code = $CI->common_data['company_data']['company_code'];
            // var_dump($purchaseOrderID);
            if($serializationType['serializationType'] == 5 && $documentID == 'PO'){

                $CI->db->select('itemCategoryID');
                $CI->db->where('purchaseOrderID', $purchaseOrderID);
                $CI->db->from('srp_erp_purchaseordermaster');
                $catID = $CI->db->get()->row_array();

                $CI->db->select('serializationID');
                $CI->db->where('typeID', $catID['itemCategoryID']);
                $CI->db->from('srp_erp_serializationtypes');
                $serializationID = $CI->db->get()->row_array();

                $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
                $CI->db->from('srp_erp_financeyeardocumentcodemaster');
                $CI->db->where('srp_erp_financeyeardocumentcodemaster.documentID', $documentID);
                $CI->db->where('srp_erp_financeyeardocumentcodemaster.companyID', $company_id);
                $CI->db->where('srp_erp_financeyeardocumentcodemaster.serializationID', $serializationID['serializationID']);
                $data = $CI->db->get()->row_array();
    
                if (empty($data)) {
                    $data_arr = [
                        'documentID' => $documentID,
                        'prefix' => $documentID,
                        'companyID' => $company_id,
                        'companyCode' => $CI->common_data['company_data']['company_code'],
                        'createdUserGroup' => $CI->common_data['user_group'],
                        'createdUserID' => $CI->common_data['current_userID'],
                        'createdUserName' => $CI->common_data['current_user'],
                        'createdPCID' => $CI->common_data['current_pc'],
                        'createdDateTime' => $CI->common_data['current_date'],
                        'modifiedUserID' => $CI->common_data['current_userID'],
                        'modifiedUserName' => $CI->common_data['current_user'],
                        'modifiedPCID' => $CI->common_data['current_pc'],
                        'modifiedDateTime' => $CI->common_data['current_date'],
                        'startSerialNo' => 1,
                        'formatLength' => 6,
                        'format_1' => 'prefix',
                        'format_2' => NULL,
                        'format_3' => NULL,
                        'format_4' => NULL,
                        'format_5' => NULL,
                        'format_6' => NULL,
                        'serialNo' => 1,
                    ];
    
                    $CI->db->insert('srp_erp_financeyeardocumentcodemaster', $data_arr);
                    $data = $data_arr;
                } else {
                    $serializationTypeID = $serializationID['serializationID'];
                    $CI->db->query("UPDATE srp_erp_financeyeardocumentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'AND serializationID = '{$serializationTypeID}'");
                    $data['serialNo'] = ($data['serialNo'] + 1);
                }
                if ($data['format_1']) {
                    if ($data['format_1'] == 'prefix') {
                        $code .= $data['prefix'];
                    }
                    if ($data['format_1'] == 'yyyy') {
                        $code .= $documentYear;
                    }
                    if ($data['format_1'] == 'yy') {
                        $documentYear = date('Y');
                        $code .= substr($documentYear, -2);
                    }
                    if ($data['format_1'] == 'mm') {
                        $code .= date('m');
                    }
                }
                if ($data['format_2']) {
                    $code .= $data['format_2'];
                }
                if ($data['format_3']) {
                    if ($data['format_3'] == 'mm') {
                        $code .= $documentMonth;
                    }
                    if ($data['format_3'] == 'yyyy') {
                        $code .= $documentYear;
                    }
                    if ($data['format_3'] == 'prefix') {
                        $code .= $data['prefix'];
                    }
                    if ($data['format_3'] == 'yy') {
                        $documentYear = date('Y');
                        $code .= substr($documentYear, -2);
                    }
                }
                if ($data['format_4']) {
                    $code .= $data['format_4'];
                }
                if ($data['format_5']) {
                    if ($data['format_5'] == 'mm') {
                        $code .= $documentMonth;
                    }
                    if ($data['format_5'] == 'yyyy') {
                        $code .= $documentYear;
                    }
                    if ($data['format_5'] == 'prefix') {
                        $code .= $data['prefix'];
                    }
                    if ($data['format_5'] == 'yy') {
                        $documentYear = date('Y');
                        $code .= substr($documentYear, -2);
                    }
                }
                if ($data['format_6']) {
                    $code .= $data['format_6'];
                }
                if ($count == 0) {
                    $number = $data['serialNo'];
                } else {
                    $number = $count;
                }
                $CI->db->query("UNLOCK TABLES");
                return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
            }
            else{
                $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
                $CI->db->from('srp_erp_documentcodemaster');
                $CI->db->where('srp_erp_documentcodemaster.documentID', $documentID);
                $CI->db->where('srp_erp_documentcodemaster.companyID', $company_id);
                $data = $CI->db->get()->row_array();

                if (empty($data)) {
                    $data_arr = [
                        'documentID' => $documentID,
                        'prefix' => $documentID,
                        'companyID' => $company_id,
                        'companyCode' => $CI->common_data['company_data']['company_code'],
                        'createdUserGroup' => $CI->common_data['user_group'],
                        'createdUserID' => $CI->common_data['current_userID'],
                        'createdUserName' => $CI->common_data['current_user'],
                        'createdPCID' => $CI->common_data['current_pc'],
                        'createdDateTime' => $CI->common_data['current_date'],
                        'modifiedUserID' => $CI->common_data['current_userID'],
                        'modifiedUserName' => $CI->common_data['current_user'],
                        'modifiedPCID' => $CI->common_data['current_pc'],
                        'modifiedDateTime' => $CI->common_data['current_date'],
                        'startSerialNo' => 1,
                        'formatLength' => 6,
                        'format_1' => 'prefix',
                        'format_2' => NULL,
                        'format_3' => NULL,
                        'format_4' => NULL,
                        'format_5' => NULL,
                        'format_6' => NULL,
                        'serialNo' => 1,
                    ];
    
                    $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
                    $data = $data_arr;
                } else {
                    $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
                    $data['serialNo'] = ($data['serialNo'] + 1);
                }

                if ($data['format_1']) {
                    if ($data['format_1'] == 'prefix') {
                        $code .= $data['prefix'];
                    }
                    if ($data['format_1'] == 'yyyy') {
                        $code .= $documentYear;
                    }
                    if ($data['format_1'] == 'yy') {
                        $documentYear = date('Y');
                        $code .= substr($documentYear, -2);
                    }
                    if ($data['format_1'] == 'mm') {
                        $code .= date('m');
                    }
                }
                if ($data['format_2']) {
                    $code .= $data['format_2'];
                }
                if ($data['format_3']) {
                    if ($data['format_3'] == 'mm') {
                        $code .= $documentMonth;
                    }
                    if ($data['format_3'] == 'yyyy') {
                        $code .= $documentYear;
                    }
                    if ($data['format_3'] == 'prefix') {
                        $code .= $data['prefix'];
                    }
                    if ($data['format_3'] == 'yy') {
                        $documentYear = date('Y');
                        $code .= substr($documentYear, -2);
                    }
                }
                if ($data['format_4']) {
                    $code .= $data['format_4'];
                }
                if ($data['format_5']) {
                    if ($data['format_5'] == 'mm') {
                        $code .= $documentMonth;
                    }
                    if ($data['format_5'] == 'yyyy') {
                        $code .= $documentYear;
                    }
                    if ($data['format_5'] == 'prefix') {
                        $code .= $data['prefix'];
                    }
                    if ($data['format_5'] == 'yy') {
                        $documentYear = date('Y');
                        $code .= substr($documentYear, -2);
                    }
                }
                if ($data['format_6']) {
                    $code .= $data['format_6'];
                }
                if ($count == 0) {
                    $number = $data['serialNo'];
                } else {
                    $number = $count;
                }
                $CI->db->query("UNLOCK TABLES");
                return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
            }            
        }
    }

    function sequence_generator($documentID, $count = 0)
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $CI->common_data['company_data']['company_id'];
        $company_code = $CI->common_data['company_data']['company_code'];
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $company_id,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_1'] == 'yy') {
                $documentYear = date('Y');
                $code .= substr($documentYear, -2);
            }
            if ($data['format_1'] == 'mm') {
                $code .= date('m');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_job($documentID, $count = 0 ,$id = null)
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $CI->common_data['company_data']['company_id'];
        $company_code = $CI->common_data['company_data']['company_code'];
   
     
        if ($id ) {
            // Fetch existing data based on the ID and company
            $CI->db->select('documentID, prefix, serialNo, formatLength, format_1, format_2, format_3, format_4, format_5, format_6');
            $CI->db->from('srp_erp_documentcodemaster');
            $CI->db->where('documentID', $documentID);
            $CI->db->where('companyID', $company_id);
            $data = $CI->db->get()->row_array();
   
            if (empty($data)) {
                return false; // ID provided but no matching data found
            }
        }else{
            $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
            $CI->db->from('srp_erp_documentcodemaster');
            $CI->db->where('documentID', $documentID);
            $CI->db->where('companyID', $company_id);
            $data = $CI->db->get()->row_array();
            if (empty($data)) {
                $data_arr = [
                    'documentID' => $documentID,
                    'prefix' => $documentID,
                    'companyID' => $company_id,
                    'companyCode' => $CI->common_data['company_data']['company_code'],
                    'createdUserGroup' => $CI->common_data['user_group'],
                    'createdUserID' => $CI->common_data['current_userID'],
                    'createdUserName' => $CI->common_data['current_user'],
                    'createdPCID' => $CI->common_data['current_pc'],
                    'createdDateTime' => $CI->common_data['current_date'],
                    'modifiedUserID' => $CI->common_data['current_userID'],
                    'modifiedUserName' => $CI->common_data['current_user'],
                    'modifiedPCID' => $CI->common_data['current_pc'],
                    'modifiedDateTime' => $CI->common_data['current_date'],
                    'startSerialNo' => 1,
                    'formatLength' => 6,
                    'format_1' => 'prefix',
                    'format_2' => NULL,
                    'format_3' => NULL,
                    'format_4' => NULL,
                    'format_5' => NULL,
                    'format_6' => NULL,
                    'serialNo' => 1,
                ];
   
                $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
                $data = $data_arr;
            } else {
                $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
                $data['serialNo'] = ($data['serialNo'] + 1);
            }
 
        }
     
 
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_1'] == 'yy') {
                $documentYear = date('Y');
                $code .= substr($documentYear, -2);
            }
            if ($data['format_1'] == 'mm') {
                $code .= date('m');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_item(
        $documentID,
        $count,
        $companyid,
        $compcode,
        $subCat,
        $subsubCat,
        $subsubsubCat
    )
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $companyid;
        $company_code = $compcode;
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);

        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => 'subCat',
                'format_4' => 'subsubCat',
                'format_5' => 'subsubsubCat',
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $data_arr['companyID'] = $company_id;

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {

            $CI->db->set('serialNo', 'serialNo + 1', FALSE);
            $CI->db->where('documentID', $documentID);

            $CI->db->where('companyID', $company_id);

            $CI->db->update('srp_erp_documentcodemaster');

            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {

            if ($data['format_3'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_3'] == 'subCat')
            {
                $code.= '/'. $subCat;
            }
        }
        if ($data['format_4']) {

            if ($data['format_4'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_4'] == 'subsubCat')
            {
                $code.= '/'. $subsubCat;
            }
        }
        if ($data['format_5']) {

            if ($data['format_5'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_5'] == 'subsubsubCat')
            {
                $code.= '/'. $subsubsubCat;
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_temp($documentID, $count = 0)
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $CI->common_data['company_data']['company_id'];
        $company_code = $CI->common_data['company_data']['company_code'];
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $company_id,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
         $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_1'] == 'yy') {
                $documentYear = date('Y');
                $code .= substr($documentYear, -2);
            }
            if ($data['format_1'] == 'mm') {
                $code .= date('m');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_for_seconday_code($documentID, $count = 0)
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $CI->common_data['company_data']['company_id'];
        $company_code = $CI->common_data['company_data']['company_code'];
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $company_id,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
         //$CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_1'] == 'yy') {
                $documentYear = date('Y');
                $code .= substr($documentYear, -2);
            }
            if ($data['format_1'] == 'mm') {
                $code .= date('m');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }
    function mfq_sequence_generator($documentID, $count = 0, $segmentID = null)
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $CI->common_data['company_data']['company_id'];
        $company_code = $CI->common_data['company_data']['company_code'];
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $company_id,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }

        if ($segmentID) {
            $segmentID = $segmentID . '/';
        }
        return ($company_code . '/' . $segmentID . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function mfq_sequence_generator_flowserve($documentID, $count = 0, $segmentID = null)
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $CI->common_data['company_data']['company_id'];
        $company_code = $CI->common_data['company_data']['company_code'];
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $company_id,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
           // $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = 1;
        } else {
            $number = $count+1;
        }

        if ($segmentID) {
            $segmentID = $segmentID . '/';
        }
        return ($company_code . '/' . $segmentID . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_byback($documentID, $count = 0, $compid = 0, $companyDe = 0)
    {
        $CI = &get_instance();
        $code = '';
        if ($compid == 0) {
            $company_id = $CI->common_data['company_data']['company_id'];
            $company_code = $CI->common_data['company_data']['company_code'];
        } else {
            $company_id = $compid;
            $company_code = $companyDe;
        }
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $company_id,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }


    function sequence_generator_group($documentID, $count, $companyid, $compcode, $groupId = null)
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $companyid;
        $company_code = $compcode;
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        if($groupId)
        {
            $CI->db->where('groupID', $groupId);
        }
        else
        {
            $CI->db->where('companyID', $company_id);
        }
        
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            if($groupId)
            {
                $data_arr['groupID'] = $groupId;
            }
            else
            {
                $data_arr['companyID'] = $company_id;
            }

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {

            $CI->db->set('serialNo', 'serialNo + 1', FALSE);
            $CI->db->where('documentID', $documentID);
            
            if($groupId)
            {
                $CI->db->where('groupID', $groupId);
            }
            else
            {
                $CI->db->where('companyID', $company_id);
            }
            
            $CI->db->update('srp_erp_documentcodemaster');

            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_group_item(
        $documentID,
        $count,
        $companyid,
        $compcode,
        $subCat,
        $subsubCat,
        $subsubsubCat,
        $groupId = null
    )
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $companyid;
        $company_code = $compcode;
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        if($groupId)
        {
            $CI->db->where('groupID', $groupId);
        }
        else
        {
            $CI->db->where('companyID', $company_id);
        }
        
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => 'subCat',
                'format_4' => 'subsubCat',
                'format_5' => 'subsubsubCat',
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            if($groupId)
            {
                $data_arr['groupID'] = $groupId;
            }
            else
            {
                $data_arr['companyID'] = $company_id;
            }

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {

            $CI->db->set('serialNo', 'serialNo + 1', FALSE);
            $CI->db->where('documentID', $documentID);
            
            if($groupId)
            {
                $CI->db->where('groupID', $groupId);
            }
            else
            {
                $CI->db->where('companyID', $company_id);
            }
            
            $CI->db->update('srp_erp_documentcodemaster');

            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {

            if ($data['format_3'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_3'] == 'subCat')
            {
                $code.= '/'. $subCat;
            }
        }
        if ($data['format_4']) {

            if ($data['format_4'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_4'] == 'subsubCat')
            {
                $code.= '/'. $subsubCat;
            }
        }
        if ($data['format_5']) {
            
            if ($data['format_5'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_5'] == 'subsubsubCat')
            {
                $code.= '/'. $subsubsubCat;
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_location($documentID, $companyFinanceYearID = null, $locationid = NULL, $documentYear = NULL, $documentMonth = NULL, $count = 0)
    {    //location wise code generator
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $CI->db->query("LOCK TABLES srp_erp_locationdocumentcodemaster WRITE, srp_erp_documentcodemaster READ, srp_erp_location READ");

        $isFinanceYN = $CI->db->query("select isFYBasedSerialNo from `srp_erp_documentcodemaster` where documentID='{$documentID}' AND companyID = $companyID ")->row_array();

        if (($isFinanceYN['isFYBasedSerialNo'] == 1)) {
            if ($companyFinanceYearID == NULL) {
                var_dump('Finance year  not found');
                exit;
            }
            if ($documentYear == NULL) {
                var_dump('document Year  not found');
                exit;
            }
            if ($documentMonth == NULL) {
                var_dump('document Month  not found');
                exit;
            }
            $location = $CI->db->query("SELECT locationCode FROM `srp_erp_location` where companyID = $companyID AND locationID = $locationid")->row_array();
            $sqlSerialNo = $CI->db->query("select serialNo from `srp_erp_locationdocumentcodemaster` where documentID='{$documentID}' AND companyID = $companyID AND locationID = $locationid AND financeYearID = $companyFinanceYearID")->row_array();

            $serialNo = $sqlSerialNo['serialNo'] + 1;
            $CI = &get_instance();
            $code = '';
            $company_id = $CI->common_data['company_data']['company_id'];
            $company_code = $CI->common_data['company_data']['company_code'];
            $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
            $CI->db->from('srp_erp_locationdocumentcodemaster');
            $CI->db->where('documentID', $documentID);
            $CI->db->where('companyID', $company_id);
            $CI->db->where('locationID', $locationid);
            $CI->db->where('financeYearID', $companyFinanceYearID);
            $data = $CI->db->get()->row_array();
            if (empty($data)) {
                $data_arr = [
                    'documentID' => $documentID,
                    'prefix' => $documentID,
                    'companyID' => $company_id,
                    'financeYearID' => $companyFinanceYearID,
                    'locationID' => $locationid,
                    'companyCode' => $CI->common_data['company_data']['company_code'],
                    'createdUserGroup' => $CI->common_data['user_group'],
                    'createdUserID' => $CI->common_data['current_userID'],
                    'createdUserName' => $CI->common_data['current_user'],
                    'createdPCID' => $CI->common_data['current_pc'],
                    'createdDateTime' => $CI->common_data['current_date'],
                    'modifiedUserID' => $CI->common_data['current_userID'],
                    'modifiedUserName' => $CI->common_data['current_user'],
                    'modifiedPCID' => $CI->common_data['current_pc'],
                    'modifiedDateTime' => $CI->common_data['current_date'],
                    'startSerialNo' => 1,
                    'formatLength' => 6,
                    'format_1' => 'prefix',
                    'format_2' => '/',
                    'format_3' => 'Location',
                    'format_4' => '/',
                    'format_5' => 'yyyy',
                    'format_6' => '/',
                    'serialNo' => 1,
                ];

                $CI->db->insert('srp_erp_locationdocumentcodemaster', $data_arr);
                $data = $data_arr;
            } else {
                $CI->db->query("UPDATE srp_erp_locationdocumentcodemaster SET serialNo = {$serialNo}  WHERE documentID='{$documentID}' AND companyID = '{$company_id}' AND financeYearID = '{$companyFinanceYearID}' AND locationID = '{$locationid}'");
                $data['serialNo'] = $serialNo;
            }
            if ($data['format_1']) {
                if ($data['format_1'] == 'prefix') {
                    $code .= $data['prefix'];
                }
                if ($data['format_1'] == 'yyyy') {
                    $code .= $documentYear;
                }
                if ($data['format_1'] == 'Location') {
                    $code .= $location['locationCode'];
                }
            }
            if ($data['format_2']) {
                $code .= $data['format_2'];
            }
            if ($data['format_3']) {
                if ($data['format_3'] == 'mm') {
                    $code .= $documentMonth;
                }
                if ($data['format_3'] == 'yyyy') {
                    $code .= $documentYear;
                }
                if ($data['format_3'] == 'prefix') {
                    $code .= $data['prefix'];
                }
                if ($data['format_3'] == 'Location') {
                    $code .= $location['locationCode'];
                }
            }
            if ($data['format_4']) {
                $code .= $data['format_4'];
            }
            if ($data['format_5']) {
                if ($data['format_5'] == 'mm') {
                    $code .= $documentMonth;
                }
                if ($data['format_5'] == 'yyyy') {
                    $code .= $documentYear;
                }
                if ($data['format_5'] == 'prefix') {
                    $code .= $data['prefix'];
                }
                if ($data['format_5'] == 'Location') {
                    $code .= $location['locationCode'];
                }
            }
            if ($data['format_6']) {
                $code .= $data['format_6'];
            }
            if ($count == 0) {
                $number = $data['serialNo'];
            } else {
                $number = $count;
            }
            $CI->db->query("UNLOCK TABLES");
            return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));

        } else {
            $CI = &get_instance();
            $companyID = $CI->common_data['company_data']['company_id'];
            $company_code = $CI->common_data['company_data']['company_code'];

            $location = $CI->db->query("SELECT locationCode FROM `srp_erp_location` where companyID = $companyID AND locationID = $locationid")->row_array();
            $sqlSerialNo = $CI->db->query("select serialNo from `srp_erp_locationdocumentcodemaster` where documentID='{$documentID}' AND companyID = $companyID AND locationID = $locationid AND financeYearID = 0 ")->row_array();
            $serialNo = $sqlSerialNo['serialNo'] + 1;
            $CI = &get_instance();
            $code = '';
            $company_id = $CI->common_data['company_data']['company_id'];
            $company_code = $CI->common_data['company_data']['company_code'];
            $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
            $CI->db->from('srp_erp_locationdocumentcodemaster');
            $CI->db->where('documentID', $documentID);
            $CI->db->where('companyID', $company_id);
            $CI->db->where('locationID', $locationid);
            $CI->db->where('financeYearID', 0);
            $data = $CI->db->get()->row_array();
            if (empty($data)) {
                $data_arr = [
                    'documentID' => $documentID,
                    'prefix' => $documentID,
                    'companyID' => $company_id,
                    'locationID' => $locationid,
                    'financeYearID' => 0,
                    'companyCode' => $CI->common_data['company_data']['company_code'],
                    'createdUserGroup' => $CI->common_data['user_group'],
                    'createdUserID' => $CI->common_data['current_userID'],
                    'createdUserName' => $CI->common_data['current_user'],
                    'createdPCID' => $CI->common_data['current_pc'],
                    'createdDateTime' => $CI->common_data['current_date'],
                    'modifiedUserID' => $CI->common_data['current_userID'],
                    'modifiedUserName' => $CI->common_data['current_user'],
                    'modifiedPCID' => $CI->common_data['current_pc'],
                    'modifiedDateTime' => $CI->common_data['current_date'],
                    'startSerialNo' => 1,
                    'formatLength' => 6,
                    'format_1' => 'prefix',
                    'format_2' => '/',
                    'format_3' => 'Location',
                    'format_4' => '/',
                    'format_5' => NULL,
                    'format_6' => NULL,
                    'serialNo' => 1,
                ];

                $CI->db->insert('srp_erp_locationdocumentcodemaster', $data_arr);
                $data = $data_arr;
            } else {
                $CI->db->query("UPDATE srp_erp_locationdocumentcodemaster SET serialNo = {$serialNo}  WHERE documentID='{$documentID}' AND companyID = '{$company_id}' AND locationID = '{$locationid}' and financeYearID = '0' ");
                $data['serialNo'] = $serialNo;
            }
            if ($data['format_1']) {
                if ($data['format_1'] == 'prefix') {
                    $code .= $data['prefix'];
                }
                if ($data['format_1'] == 'yyyy') {
                    $code .= $documentYear;
                }
                if ($data['format_1'] == 'Location') {
                    $code .= $location['locationCode'];
                }
            }
            if ($data['format_2']) {
                $code .= $data['format_2'];
            }
            if ($data['format_3']) {
                if ($data['format_3'] == 'mm') {
                    $code .= $documentMonth;
                }
                if ($data['format_3'] == 'yyyy') {
                    $code .= $documentYear;
                }
                if ($data['format_3'] == 'prefix') {
                    $code .= $data['prefix'];
                }
                if ($data['format_3'] == 'Location') {
                    $code .= $location['locationCode'];

                }
            }
            if ($data['format_4']) {
                $code .= $data['format_4'];
            }
            if ($data['format_5']) {
                if ($data['format_5'] == 'mm') {
                    $code .= $documentMonth;
                }
                if ($data['format_5'] == 'yyyy') {
                    $code .= $documentYear;
                }
                if ($data['format_5'] == 'prefix') {
                    $code .= $data['prefix'];
                }
                if ($data['format_5'] == 'Location') {
                    $code .= $location['locationCode'];

                }
            }
            if ($data['format_6']) {
                $code .= $data['format_6'];
            }
            if ($count == 0) {
                $number = $data['serialNo'];
            } else {
                $number = $count;
            }
            $CI->db->query("UNLOCK TABLES");
            return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));

        }
    }

    function sequence_generator_employee($type){
        $CI = &get_instance();
        $companyID = current_companyID();

        $result = $CI->db->query("SELECT prefix, serialNo FROM srp_erp_tibian_employeetype 
                      LEFT JOIN (
                         SELECT tibianType, MAX(serialNo) AS serialNo  FROM srp_employeesdetails 
                         WHERE Erp_companyID = {$companyID} AND tibianType = {$type}
                      ) AS empTB ON empTB.tibianType = srp_erp_tibian_employeetype.id
                      WHERE id = {$type} ")->row_array();
        $prefix = $result['prefix'].'-';
        $serialNo = $result['serialNo'] + 1;
        $code = $prefix.str_pad($serialNo, 4, '0', STR_PAD_LEFT);

        return [
            'code' => $code,
            'serialNo' => $serialNo
        ];

    }

    function sequence_generator_mobile($documentID, $count, $company_id, $company_code, $usergroupID, $user_id, $name2, $current_pc, $currentTime)
    {
        $CI = &get_instance();
        $code = '';

        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $company_id,
                'companyCode' => $company_code,
                'createdUserGroup' => $usergroupID,
                'createdUserID' => $user_id,
                'createdUserName' => $name2,
                'createdPCID' => $current_pc,
                'createdDateTime' => $currentTime,
                'modifiedUserID' => $user_id,
                'modifiedUserName' => $name2,
                'modifiedPCID' => $current_pc,
                'modifiedDateTime' => $currentTime,
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_1'] == 'yy') {
                $documentYear = date('Y');
                $code .= substr($documentYear, -2);
            }
            if ($data['format_1'] == 'mm') {
                $code .= date('m');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }
    function sequence_generator_scheduleservices($documentID, $count,$company_id,$company_code)
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $company_id;
        $company_code = $company_code;
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $company_id,
                'companyCode' => $company_code,
                'createdUserGroup' => NULL,
                'createdUserID' => NULL,
                'createdUserName' => NULL,
                'createdPCID' => NULL,
                'createdDateTime' =>  NULL,
                'modifiedUserID' => NULL,
                'modifiedUserName' =>  NULL,
                'modifiedPCID' =>  NULL,
                'modifiedDateTime' =>  NULL,
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_1'] == 'yy') {
                $documentYear = date('Y');
                $code .= substr($documentYear, -2);
            }
            if ($data['format_1'] == 'mm') {
                $code .= date('m');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_spurgo($documentID,$count,$company_id,$company_code){
        $CI = & get_instance();
        $code = '';
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = array(
                'documentID'         => $documentID,
                'prefix'             => $documentID,
                'companyID'          => $company_id,
                'companyCode'        => $company_code,
                'createdUserGroup'   => 1,
                'createdUserID'      => 'Root',
                'createdUserName'    => 'Root',
                'createdPCID'        => gethostbyaddr($_SERVER['REMOTE_ADDR']),
                'createdDateTime'    => date('Y-m-d h:i:s'),
                'modifiedUserID'     => 'Root',
                'modifiedUserName'   => 'Root',
                'modifiedPCID'       => gethostbyaddr($_SERVER['REMOTE_ADDR']),
                'modifiedDateTime'   => date('Y-m-d h:i:s'),
                'startSerialNo'      => 1,
                'formatLength'       => 6,
                'format_1'           => 'prefix',
                'format_2'           => NULL,
                'format_3'           => NULL,
                'format_4'           => NULL,
                'format_5'           => NULL,
                'format_6'           => NULL,
                'serialNo'           => 1
            );

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        }else{
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo']=($data['serialNo']+1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] =='prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] =='yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] =='mm') {
                $code .= date('m');
            }
            if ($data['format_3'] =='yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            $code .= $data['format_5'];
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count==0) {
            $number = $data['serialNo'];
        }else{
            $number = $count;
        }
        return ($company_code.'/'.$code.str_pad($number,$data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_item_with_sub_sub_category_temp(
        $documentID,
        $count,
        $companyid,
        $compcode,
        $subCat,
        $subsubCat,
        $subsubsubCat,
        $mainCategoryID
    )
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $companyid;
        $company_code = $compcode;
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);

        $data = $CI->db->get()->row_array();
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_itemcategory');
        $CI->db->where('itemCategoryID', $mainCategoryID);
        $CI->db->where('companyID', $company_id);
        $data_item_category = $CI->db->get()->row_array();

        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => 'subCat',
                'format_4' => 'subsubCat',
                'format_5' => 'subsubsubCat',
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $data_arr['companyID'] = $company_id;

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
          
            $data_item_category_serialNoCategory = ($data_item_category['serialNoCategory'] + 1);
            // $CI->db->set('serialNoCategory', 'serialNoCategory + 1', FALSE);
            // $CI->db->where('itemCategoryID', $mainCategoryID);
            // $CI->db->where('companyID', $company_id);
            // $CI->db->update('srp_erp_itemcategory');
          
        }
        //print_r($subCat);exit;
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {

            if ($data['format_3'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_3'] == 'subCat')
            {
                $code.= $subCat;
                
            }
        }
        if ($data['format_4']) {

            if ($data['format_3'] == 'subCat')
            {
                if ($data['format_2']) {
                    $code .= $data['format_2'];
                }
                
            }

            if ($data['format_4'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_4'] == 'subsubCat')
            {
                $code.= $subsubCat;
            }
        }
        if ($data['format_5']) {

            if ($data['format_4'] == 'subsubCat')
            {
                if ($data['format_2']) {
                    $code .= $data['format_2'];
                }
            }
           
            if ($data['format_5'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_5'] == 'subsubsubCat')
            {
                $code.= $subsubsubCat;
                
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data_item_category_serialNoCategory;
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_item_temp(
        $documentID,
        $count,
        $companyid,
        $compcode,
        $subCat,
        $subsubCat,
        $subsubsubCat
    )
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $companyid;
        $company_code = $compcode;
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);

        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $data_arr['companyID'] = $company_id;

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {

            // $CI->db->set('serialNo', 'serialNo + 1', FALSE);
            // $CI->db->where('documentID', $documentID);

            // $CI->db->where('companyID', $company_id);

            // $CI->db->update('srp_erp_documentcodemaster');

            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {

            if ($data['format_3'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            // if ($data['format_3'] == 'subCat')
            // {
            //     $code.= '/'. $subCat;
            // }
        }
        if ($data['format_4']) {

            if ($data['format_4'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            // if ($data['format_4'] == 'subsubCat')
            // {
            //     $code.= '/'. $subsubCat;
            // }
        }
        if ($data['format_5']) {

            if ($data['format_5'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            // if ($data['format_5'] == 'subsubsubCat')
            // {
            //     $code.= '/'. $subsubsubCat;
            // }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_item_with_sub_sub_category(
        $documentID,
        $count,
        $companyid,
        $compcode,
        $subCat,
        $subsubCat,
        $subsubsubCat,
        $mainCategoryID
    )
    {
        $CI = &get_instance();
        $code = '';
        $company_id = $companyid;
        $company_code = $compcode;
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);

        $data = $CI->db->get()->row_array();
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_itemcategory');
        $CI->db->where('itemCategoryID', $mainCategoryID);
        $CI->db->where('companyID', $company_id);
        $data_item_category = $CI->db->get()->row_array();

        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyCode' => $CI->common_data['company_data']['company_code'],
                'createdUserGroup' => $CI->common_data['user_group'],
                'createdUserID' => $CI->common_data['current_userID'],
                'createdUserName' => $CI->common_data['current_user'],
                'createdPCID' => $CI->common_data['current_pc'],
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $CI->common_data['current_userID'],
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => 'subCat',
                'format_4' => 'subsubCat',
                'format_5' => 'subsubsubCat',
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $data_arr['companyID'] = $company_id;

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
          
            $data_item_category_serialNoCategory = ($data_item_category['serialNoCategory'] + 1);
            $CI->db->set('serialNoCategory', 'serialNoCategory + 1', FALSE);
            $CI->db->where('itemCategoryID', $mainCategoryID);
            $CI->db->where('companyID', $company_id);
            $CI->db->update('srp_erp_itemcategory');
          
        }
        
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {

            if ($data['format_3'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_3'] == 'subCat')
            {
                $code.= $subCat;
                
            }
        }
        if ($data['format_4']) {

            if ($data['format_3'] == 'subCat')
            {
                if ($data['format_2']) {
                    $code .= $data['format_2'];
                }
                
            }

            if ($data['format_4'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_4'] == 'subsubCat')
            {
                $code.= $subsubCat;
            }
        }
        if ($data['format_5']) {

            if ($data['format_4'] == 'subsubCat')
            {
                if ($data['format_2']) {
                    $code .= $data['format_2'];
                }
            }
           
            if ($data['format_5'] == 'prefix')
            {
                $code .= $data['prefix'];
            }

            if ($data['format_5'] == 'subsubsubCat')
            {
                $code.= $subsubsubCat;
                
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data_item_category_serialNoCategory;
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

}