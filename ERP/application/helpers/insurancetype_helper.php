<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



if (!function_exists('load_insurancetype_action')) {
    function load_insurancetype_action($insuranceTypeID, $masterTypeID)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '';
        if($masterTypeID==0){
            $status .= '<span class="pull-right"><a href="#" onclick="sub_insurance_type('.$insuranceTypeID.')"><span title="Sub Type" rel="tooltip" class="glyphicon glyphicon-menu-hamburger" ></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="openinsuranceeditmodel('.$insuranceTypeID.')"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a> |&nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="delete_insurancetype('.$insuranceTypeID.')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>&nbsp;&nbsp;</a>';
        }else{
            $status .= '<span class="pull-right"><a href="#" onclick="opensubinsuranceeditmodel('.$insuranceTypeID.','.$masterTypeID.')"><span title="Edit" rel="tooltip" class="fa fa-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<span class="pull-right"><a href="#" onclick="delete_insurancetype('.$insuranceTypeID.')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span>&nbsp;&nbsp;</a>';
        }


        return $status;
    }
}

