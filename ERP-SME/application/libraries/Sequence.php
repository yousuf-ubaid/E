<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sequence{
    
    function __construct($config){
        $CI = & get_instance();
        $CI->load->database($config, FALSE, TRUE);
    }

    function sequence_generator($documentID,$count,$company_id,$company_code){
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
}