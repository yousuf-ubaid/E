<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('confirm_aproval_EC')) {
    function confirm_aproval_EC($approved_status, $confirmed_status)
    {
        $status = '<center>';
        if ($approved_status == 0) {
            if ($confirmed_status == 0 || $confirmed_status == 3) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else $status .= '<a href="#" class="label label-danger">&nbsp;</a>';
        } elseif ($approved_status == 1) {
            if ($confirmed_status == 1) {
                $status .= '<a  class="label label-success">&nbsp;</a>';
            } else {
                $status .= '<span class="label label-success">&nbsp;</span>';
            }
        } elseif ($approved_status == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } elseif ($approved_status == 5) {
            $status .= '<span class="label label-info">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        return $status . '</center>';
    }
}

if (!function_exists('load_action_dialog')) { /*get po action list*/
    function load_action_dialog($approved, $perfomance_appraisal_id, $Level)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        if ($approved == 0) {
            $status .= '<a target="_blank" onclick=\'fetch_approval("' . $perfomance_appraisal_id . '","' . $Level . '"); \' ><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '<a onclick=\'refer_back_employee_wise_performance_confirm_dialog("' . $perfomance_appraisal_id . '");\'><span title="" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:#d15b47;" data-original-title="Refer Back"></span></a>';

        }
        return $status . '</span>';
    }
}

if (!function_exists('skills_approval_action')) { /*get po action list*/
    function skills_approval_action($approved, $perfomance_appraisal_id, $Level)
    {
        $CI =& get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        if ($approved == 0) {
            $status .= '<a target="_blank" onclick=\'fetch_skills_approval("' . $perfomance_appraisal_id . '","' . $Level . '"); \' ><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            // $status .= '<a target="_blank" onclick="documentPageView_modal(\'EC\',\'' . $EcID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        return $status . '</span>';
    }
}

if (!function_exists('kpiIndicatorProgress')) {
    function kpiIndicatorProgress($completion)
    {
        $progressColor = "background-color:#337ab7;";
        if($completion==100){
            $progressColor = "background-color:#28a745;";
        }
        return '<div title="" class="progress" style="height: 20px;">  <div class="progress-bar" role="progressbar" style="'.$progressColor.'width: ' . $completion . '%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div><span style="color: black;">' . $completion . '%</span></div>';
    }
}

if (!function_exists('kpiIndicatorStatus')) {
    function kpiIndicatorStatus($completion)
    {
        if ($completion == 0) {
            $status = 'Open';
        } elseif ($completion > 0 && $completion < 100) {
            $status = 'In-Progress';
        } elseif ($completion == 100) {
            $status = 'Completed';
        }
        return '<div>' . $status . '</div>';
    }
}

if (!function_exists('departmentLogo')) {
    function departmentLogo($departmentLogo)
    {
        $CI =& get_instance();
        if ($departmentLogo == "") {
            $departmentLogo = "/images/No_Image.png";
        } else {
            $departmentLogo = $CI->s3->createPresignedRequest($departmentLogo, '+24 hour');
        }
        return '<div><img src="' . $departmentLogo . '" style="width: 40px;height: 40px;"/></div>';
    }
}

if (!function_exists('task_description_with_logo')) {
    function task_description_with_logo($task_description, $departmentLogo)
    {
        $CI =& get_instance();
        if ($departmentLogo == "") {
            $departmentLogo = "/images/No_Image.png";
        } else {
            $departmentLogo = $CI->s3->createPresignedRequest($departmentLogo, '+24 hour');
        }
        return '<div>
                    <table>
                        <tr><td><img src="' . $departmentLogo . '" style="width: 40px;height: 40px;"/></td><td><p style="padding-left: 5px;">' . $task_description . '</p></td></tr>
                    </table>
                </div>';
    }
}