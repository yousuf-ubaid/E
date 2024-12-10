<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * action button group for asset transfer actions.
 *
 * @param int $id          
 * @param int $confirmedYN 
 *
 * @return string  
 */
if (!function_exists('getAssetTransferAction')) /*get AssetTransfer action list*/
{ 
    function getAssetTransferAction(int $id, int $confirmedYN): string
    {
        $CI =&get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('assetmanagementnew', $primaryLanguage);

        $editAssetTransfer = $CI->lang->line('assetmanagement_edit_asset_transfer');
        $viewAssetTransfer = $CI->lang->line('assetmanagement_view_asset_transfer');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        if ($confirmedYN == 1) {
            $status .= '<li><a onclick="fetchPage(\'system/asset_management/add_new_asset_transfer\', ' . $id . ', \'' . $viewAssetTransfer . '\', \'FAT\');"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';
        } else {
            $status .= '<li><a onclick="fetchPage(\'system/asset_management/add_new_asset_transfer\', ' . $id . ', \'' . $editAssetTransfer . '\', \'FAT\');"><span class="glyphicon glyphicon-pencil" style="color:#116f5e;"></span> Edit</a></li>';
            $status .= '<li><a onclick="deleteAssetTransfer(' . $id . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a></li>';
            $status .= '<li><a onclick="fetchPage(\'system/asset_management/add_new_asset_transfer\', ' . $id . ', \'' . $editAssetTransfer . '\', \'FAT\');"><span class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></span> View</a></li>';
        }
        
        $status .= '</ul></div>';

        return $status;
    }
}