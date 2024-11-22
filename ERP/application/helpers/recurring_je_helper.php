<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*recurring journal Entry Action*/

if (!function_exists('recurring_journal_entry_action')) {
    function recurring_journal_entry_action($RJVMasterAutoId, $confirmedYN, $approvedYN, $createdUserID, $isDeleted, $confirmedByEmpID) {
        $CI =& get_instance();
        $CI->load->library('session');

        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $action .= '<li>
                        <a href="#" onclick=\'attachment_modal(' . $RJVMasterAutoId . ',"Recurring Journal Entry","RJV",' . $confirmedYN . ');\' >
                            <span class="glyphicon glyphicon-paperclip" title="Attachment" rel="tooltip"></span> Attachments
                        </a>
                    </li>';

        if ($isDeleted == 1) {
            $action .= '<li>
                            <a href="#" onclick="reOpen_contract(' . $RJVMasterAutoId . ');" >
                                <span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);" title="Re Open" rel="tooltip"></span> Re Open
                            </a>
                        </li>';
        }

        if ($confirmedYN != 1 && $isDeleted == 0) {
            $action .= '<li>
                            <a href="#" onclick=\'fetchPage("system/recurringJV/recurring_je_new",' . $RJVMasterAutoId . ',"Edit Recurring Journal Entry","Recurring Journal Entry");\' >
                                <span class="glyphicon glyphicon-pencil" title="Edit" rel="tooltip"></span> Edit
                            </a>
                        </li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) && $approvedYN == 0 && $confirmedYN == 1 && $isDeleted == 0) {
            $action .= '<li>
                            <a href="#" onclick="referback_journal_entry(' . $RJVMasterAutoId . ');" >
                                <span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);" title="Refer Back" rel="tooltip"></span> Refer Back
                            </a>
                        </li>';
        }

        $action .= '<li>
                        <a target="_blank" onclick="documentPageView_modal(\'RJV\',\'' . $RJVMasterAutoId . '\')" >
                            <span class="glyphicon glyphicon-eye-open" title="View" rel="tooltip"></span> View
                        </a>
                    </li>';

        $action .= '<li>
                        <a target="_blank" href="' . site_url('Recurring_je/recurring_journal_entry_conformation/') . '/' . $RJVMasterAutoId . '" >
                            <span class="glyphicon glyphicon-print" title="Print" rel="tooltip"></span> Print
                        </a>
                    </li>';

        if ($confirmedYN != 1 && $isDeleted == 0) {
            $action .= '<li>
                            <a href="#" onclick="delete_recurring_journal_entry(' . $RJVMasterAutoId . ',\'Invoices\');" >
                                <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" title="Delete" rel="tooltip"></span> Delete
                            </a>
                        </li>';
        }

        $action .= '</ul></div>';

        return $action;
    }
}


if (!function_exists('rjv_approval')) {
    function rjv_approval($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        } else {
            $status .= '&nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'RJV\', ' . $poID . ',\' \', ' . $approval . ')"> <span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a> &nbsp;&nbsp;';
        }


        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Journal_entry/journal_entry_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';

        return $status;
    }
}




