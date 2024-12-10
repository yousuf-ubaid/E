<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('is_mandatory')) { /*get po action list*/
    function is_mandatory($con)
    {
        $status = '<center>';
            if ($con == 0) {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            } else if ($con == 1) {
                $status .= '<span class="label label-success">&nbsp;</span>';
            }

        $status .= '</center>';
        return $status;
    }
}






