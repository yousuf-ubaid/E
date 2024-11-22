<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('loadDescription')) { /*get po action list*/
    function loadDescription($documentID)
    {
        $status = '<center>';
            if ($documentID == 'QUT') {
                $status .= 'Quotation';
            } else if ($documentID == 'CNT') {
                $status .= 'Contract';
            }else if ($documentID == 'SO') {
                $status .= 'Sales Order';
            }else if ($documentID == 'CINV') {
                $status .= 'Customer Invoice';
            }else if ($documentID == 'PO') {
                $status .= 'Purchase Order';
            }else if ($documentID == 'DO') {
                $status .= 'Delivery Order';
            }else if ($documentID == 'EST') {
                $status .= 'Manufacturing Estimate';
            }else if ($documentID == 'MDN') {
                $status .= 'Manufacturing Delivery Notes';
            }

        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('defaultCheckbox')) { /*get po action list*/
    function defaultCheckbox($autoID,$isdefault)
    {
        $status = '<center>';
            if ($isdefault == 1) {
                $status .= '<div class="skin-section extraColumns"><input id="isDefaultchkbox_'.$autoID.'" type="checkbox" data-caption="" class="columnSelected isadd" name="isDefaultchkbox" value="'.$autoID.'" checked><label for="checkbox">&nbsp;</label></div>';
            } else {
                $status .= '<div class="skin-section extraColumns"><input id="isDefaultchkbox_'.$autoID.'" type="checkbox" data-caption="" class="columnSelected isadd" name="isDefaultchkbox" value="'.$autoID.'"><label for="checkbox">&nbsp;</label></div>';
            }

        $status .= '</center>';
        return $status;
    }
}






