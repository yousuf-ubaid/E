<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sequence_mobile{

    function sequence_generator($documentID,$count=null){
        $CI = & get_instance();

        $company_id = current_companyID();
        $company_code = current_companyCode();
        $userID = current_userID();
        $name = current_user();

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
                'createdUserGroup'   => '',
                'createdUserID'      => $userID,
                'createdUserName'    => $name,
                'createdPCID'        => 'mobile',
                'createdDateTime'    => Date('Y-m-d'),
                'modifiedUserID'     => $userID,
                'modifiedUserName'   => '',
                'modifiedPCID'       => '',
                'modifiedDateTime'   => '',
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
        }
        else{
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo']=($data['serialNo']+1);
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
        return ($company_code.'/'.$code.str_pad($number,$data['formatLength'], '0', STR_PAD_LEFT));
    }

    function sequence_generator_location($documentID, $companyFinanceYearID = null, $locationid = NULL, $documentYear = NULL, $documentMonth = NULL, $count = 0, $companyData = null)
    {    //location wise code generator
        $CI = &get_instance();
        if(empty($companyData)) {
            $companyID = $CI->common_data['company_data']['company_id'];
        } else {
            $companyID = $companyData['companyID'];
        }

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

            $company_id = $companyData['companyID'];
            $company_code = $companyData['companyCode'];
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
                    'companyCode' => $companyData['companyCode'],
                    'createdUserGroup' => $companyData['createdUserGroup'],
                    'createdUserID' => $companyData['createdUserID'],
                    'createdUserName' => $companyData['createdUserName'],
                    'createdPCID' => $companyData['createdPCID'],
                    'createdDateTime' => $companyData['createdDateTime'],
                    'modifiedUserID' => $companyData['createdUserID'],
                    'modifiedUserName' => $companyData['createdUserName'],
                    'modifiedPCID' => $companyData['createdPCID'],
                    'modifiedDateTime' => $companyData['createdDateTime'],
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
            $companyID = $companyData['companyID'];
            $company_code = $companyData['companyCode'];

            $location = $CI->db->query("SELECT locationCode FROM `srp_erp_location` where companyID = $companyID AND locationID = $locationid")->row_array();
            $sqlSerialNo = $CI->db->query("select serialNo from `srp_erp_locationdocumentcodemaster` where documentID='{$documentID}' AND companyID = $companyID AND locationID = $locationid AND financeYearID = 0 ")->row_array();
            $serialNo = $sqlSerialNo['serialNo'] + 1;
            $CI = &get_instance();
            $code = '';

            $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
            $CI->db->from('srp_erp_locationdocumentcodemaster');
            $CI->db->where('documentID', $documentID);
            $CI->db->where('companyID', $companyID);
            $CI->db->where('locationID', $locationid);
            $CI->db->where('financeYearID', 0);
            $data = $CI->db->get()->row_array();

            if (empty($data)) {
                $data_arr = [
                    'documentID' => $documentID,
                    'prefix' => $documentID,
                    'companyID' => $companyID,
                    'locationID' => $locationid,
                    'financeYearID' => 0,
                    'companyCode' => $companyData['companyCode'],
                    'createdUserGroup' => $companyData['createdUserGroup'],
                    'createdUserID' => $companyData['createdUserID'],
                    'createdUserName' => $companyData['createdUserName'],
                    'createdPCID' => $companyData['createdPCID'],
                    'createdDateTime' => $companyData['createdDateTime'],
                    'modifiedUserID' => $companyData['createdUserID'],
                    'modifiedUserName' => $companyData['createdUserName'],
                    'modifiedPCID' => $companyData['createdPCID'],
                    'modifiedDateTime' => $companyData['createdDateTime'],
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
                $CI->db->query("UPDATE srp_erp_locationdocumentcodemaster SET serialNo = {$serialNo}  WHERE documentID='{$documentID}' AND companyID = '{$companyID}' AND locationID = '{$locationid}' and financeYearID = '0' ");
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

    function sequence_generator_fin($documentID, $companyFinanceYearID = NULL, $documentYear = NULL, $documentMonth = NULL, $count = 0, $companyData=null)
    {
        $CI = &get_instance();
        if(!empty($companyData)) {
            $companyID = $companyData['companyID'];
            $company_code = $companyData['companyCode'];
            $createdUserGroup = $companyData['createdUserGroup'];
            $createdPCID = $companyData['createdPCID'];
            $createdUserID = $companyData['createdUserID'];
            $createdUserName = $companyData['createdUserName'];
            $createdDateTime = $companyData['createdDateTime'];
        } else {
            $companyID = $CI->common_data['company_data']['company_id'];
            $company_code = $CI->common_data['company_data']['company_code'];
            $createdUserGroup = $CI->common_data['user_group'];
            $createdPCID = $CI->common_data['current_pc'];
            $createdUserID = $CI->common_data['current_userID'];
            $createdUserName = $CI->common_data['current_user'];
            $createdDateTime = $CI->common_data['current_date'];
        }

        $CI->db->query("LOCK TABLES srp_erp_financeyeardocumentcodemaster WRITE, srp_erp_documentcodemaster WRITE");
        //$policy    = getPolicyValues('DC', 'All');
        //$isFinance = $CI->db->query("select * from `srp_erp_documentcodes` where isFinance=1 AND documentID='{$documentID}'")->row_array();
        $isFinanceYN = $CI->db->query("select isFYBasedSerialNo from `srp_erp_documentcodemaster` where documentID='{$documentID}' AND companyID = $companyID ")->row_array();

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

            /*if ($isFinance['documentTable'] == '') {
              var_dump('document table  not found');
              exit;
            }*/

            //$table        = $isFinance['documentTable'];
            //$sqlSerialNo  = $CI->db->query("SELECT IF( isnull(MAX(serialNo)), 1 , ( MAX(serialNo) + 1) ) as serialNo FROM {$table} WHERE companyFinanceYearID={$companyFinanceYearID}  ")->row_array();
            $sqlSerialNo = $CI->db->query("select serialNo from `srp_erp_financeyeardocumentcodemaster` where documentID='{$documentID}' AND companyID = $companyID AND financeYearID = $companyFinanceYearID  ")->row_array();
            $serialNo = $sqlSerialNo['serialNo'] + 1;
            $CI = &get_instance();
            $code = '';

            $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
            $CI->db->from('srp_erp_financeyeardocumentcodemaster');
            $CI->db->where('documentID', $documentID);
            $CI->db->where('companyID', $companyID);
            $CI->db->where('financeYearID', $companyFinanceYearID);
            $data = $CI->db->get()->row_array();
            if (empty($data)) {
                $data_arr = [
                    'documentID' => $documentID,
                    'prefix' => $documentID,
                    'companyID' => $companyID,
                    'financeYearID' => $companyFinanceYearID,
                    'companyCode' => $company_code,
                    'createdUserGroup' => $createdUserGroup,
                    'createdUserID' => $createdUserID,
                    'createdUserName' => $createdUserName,
                    'createdPCID' => $createdPCID,
                    'createdDateTime' => $createdDateTime,
                    'modifiedUserID' => $createdUserID,
                    'modifiedUserName' => $createdUserName,
                    'modifiedPCID' => $createdPCID,
                    'modifiedDateTime' => $createdDateTime,
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
                $CI->db->query("UPDATE srp_erp_financeyeardocumentcodemaster SET serialNo = {$serialNo}  WHERE documentID='{$documentID}' AND companyID = '{$companyID}' AND financeYearID = '{$companyFinanceYearID}'");
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
            $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
            $CI->db->from('srp_erp_documentcodemaster');
            $CI->db->where('documentID', $documentID);
            $CI->db->where('companyID', $companyID);
            $data = $CI->db->get()->row_array();
            if (empty($data)) {
                $data_arr = [
                    'documentID' => $documentID,
                    'prefix' => $documentID,
                    'companyID' => $companyID,
                    'companyCode' => $company_code,
                    'createdUserGroup' => $createdUserGroup,
                    'createdUserID' => $createdUserID,
                    'createdUserName' => $createdUserName,
                    'createdPCID' => $createdPCID,
                    'createdDateTime' => $createdDateTime,
                    'modifiedUserID' => $createdUserID,
                    'modifiedUserName' => $createdUserName,
                    'modifiedPCID' => $createdPCID,
                    'modifiedDateTime' => $createdDateTime,
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
                $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$companyID}'");
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
