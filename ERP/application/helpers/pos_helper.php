<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-04
 * Time: 3:41 PM
 */

if (!function_exists('actionCounter_fn')) {
    function actionCounter_fn($id, $counterCode, $counterName, $wareHouseID)
    {

        $counterCode = "'" . $counterCode . "'";
        $counterName = "'" . $counterName . "'";

        $edit = '<a onclick="editCounterDetail(' . $id . ', ' . $counterCode . ', ' . $counterName . ', ' . $wareHouseID . ')">
                <span class="glyphicon glyphicon-pencil"></span></a>';
        $delete = '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_counterDetails(' . $id . ', ' . $counterCode . ')">
                  <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return '<span class="pull-right">' . $edit . '' . $delete . ' </span>';
    }
}

if (!function_exists('wareHouseUser_actionFn')) {
    function wareHouseUser_actionFn($id, $userID, $empName, $wareHouseID, $wareLocation)
    {

        return '<span class="pull-right"></span>';
    }
}

if (!function_exists('actionWarehouseUser_fn')) {
    function actionWarehouseUser_fn($id, $userID, $empName, $wareHouseID, $wareLocation)
    {

        $empName = "'" . $empName . "'";
        $wareLocation = "'" . $wareLocation . "'";
        $empname1 = addslashes($empName);
        $edit = '<a title="Enable/Disable system buttons for this user." onclick="edit_button_access(\'' . $userID . '\',\'' . $wareHouseID . '\',' . $empName . ')" rel="tooltip" ><span class="glyphicon glyphicon-wrench" style="color:grey;"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="edit_wareHouseUsers(' . $id . ', ' . $userID . ',\'' . $empname1 . '\', ' . $wareHouseID . ')"><span class="glyphicon glyphicon-pencil"></span></a>';
        $delete = '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_wareHouseUsers(' . $id . ', \'' . $empname1 . '\', ' . $wareLocation . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return '<span class="pull-right" style="width: 77px;">' . $edit . '' . $delete . ' </span>';
    }
}

if (!function_exists('actionPromotion_fn')) {
    function actionPromotion_fn($promotionID, $des)
    {

        $des = "'" . $des . "'";

        $edit = '<a onclick="newPromotion(' . $promotionID . ')"><span class="glyphicon glyphicon-pencil"></span></a>';
        $delete = '&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_promotions(' . $promotionID . ', ' . $des . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return '<span class="pull-right">' . $edit . '' . $delete . ' </span>';
    }
}

if (!function_exists('load_pos_location_drop')) {
    function load_pos_location_drop()
    {
        $CI = &get_instance();
        $company = $CI->common_data['company_data']['company_id'];
        $CI->db->select("wareHouseAutoID,wareHouseCode,wareHouseLocation,wareHouseDescription");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('isPosLocation', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $company);
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('get_gpos_location')) {
    function get_gpos_location()
    {
        $CI = &get_instance();
        $q = "SELECT
                    srp_erp_warehousemaster.wareHouseAutoID as wareHouseAutoID, srp_erp_warehousemaster.wareHouseCode as wareHouseCode, srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription  
                FROM
                    `srp_erp_warehousemaster`
                    LEFT JOIN `srp_erp_pos_segmentconfig` ON `srp_erp_pos_segmentconfig`.`wareHouseAutoID` = `srp_erp_warehousemaster`.`wareHouseAutoID`
                    LEFT JOIN `srp_erp_segment` ON `srp_erp_segment`.`segmentID` = `srp_erp_pos_segmentconfig`.`segmentID` 
                WHERE
                    `srp_erp_warehousemaster`.`companyID` = '" . current_companyID() . "' 
                    AND `isPosLocation` = 1 
                    AND `srp_erp_pos_segmentconfig`.`isGeneralPOS` = 1";
        $output = $CI->db->query($q)->result_array();
        return $output;
    }
}

if (!function_exists('promotion_policies_drop')) {
    function promotion_policies_drop()
    {
        $CI = &get_instance();
        return $CI->db->select('promotionTypeID, Description')->from('srp_erp_pos_promotiontypes')->get()->result_array();
    }
}

if (!function_exists('applicableItems')) {
    function applicableItems($val)
    {
        $YN = ($val == 1) ? 'Yes' : 'No';
        return '<div align="center">' . $YN . '</div>';
    }
}

if (!function_exists('promoWarehouses')) {
    function promoWarehouses($promotionID)
    {
        /*$CI =& get_instance();
        $result = $CI->db->select('wareHouseCode, wareHouseLocation')
                     ->from('srp_erp_pos_promotionwarehouses promo')
                     ->join('srp_erp_warehousemaster house', 'house.wareHouseAutoID = promo.wareHouseID')
                     ->where('promo.promotionID', $promotionID)
                     ->get()->result_array();
        $houses = '';
        foreach($result as $row){
            $houses .=  $row['wareHouseLocation'].'<br>';
        }
        return $houses;*/
    }
}


if (!function_exists('get_warehouse_drop')) {
    function get_warehouse_drop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isPosLocation', 1);
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['wareHouseAutoID'] ?? '')] = $row['wareHouseCode'] . ' - ' . $row['wareHouseDescription'] . ' - ' . $row['wareHouseLocation'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_warehouse_yield_drop')) {
    function get_warehouse_yield_drop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyID', current_companyID());
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['wareHouseAutoID'] ?? '')] = $row['wareHouseCode'] . ' - ' . $row['wareHouseDescription'] . ' - ' . $row['wareHouseLocation'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_inventory_item_master_details')) {
    function get_inventory_item_master_details($barcode)
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_itemmaster.*");
        $CI->db->from('srp_erp_itemmaster');
        // $CI->db->join('srp_erp_itemmaster','srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID','left');
        $CI->db->where('srp_erp_itemmaster.companyID', current_companyID());
        $CI->db->where('srp_erp_itemmaster.barcode', $barcode);
        $result = $CI->db->get()->row_array();
        
        return $result;
    }
}

if (!function_exists('get_pos_reserved_item_record')) {
    function get_pos_reserved_item_record($barcode,$invoice_no,$all = null)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_inventory_itembatch_reserved');
        $CI->db->where('companyId', current_companyID());
        $CI->db->where('barcode', $barcode);
        $CI->db->where('invoice_num', $invoice_no);
        $CI->db->where('status', 0);
        $CI->db->where('document_type', 'POS_GENERAL');

        if($all){
            $result = $CI->db->get()->result_array();
        }else{
            $result = $CI->db->get()->row_array();
        }
        
        
        return $result;
    }
}

if (!function_exists('get_pos_reserved_item_record_for_batch')) {
    function get_pos_reserved_item_record_for_batch($barcode,$invoice_no,$batchNumber)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_inventory_itembatch_reserved');
        $CI->db->where('companyId', current_companyID());
        $CI->db->where('barcode', $barcode);
        $CI->db->where('invoice_num', $invoice_no);
        $CI->db->where('batchNumber', $batchNumber);
        $CI->db->where('status', 0);
        $CI->db->where('document_type', 'POS_GENERAL');
        $result = $CI->db->get()->row_array();
        return $result;

    }
}

if (!function_exists('update_batch_reserved_quantity')) {
    function update_batch_reserved_quantity($itemAutoID,$batchNumber,$invoice_no,$status)
    {
        $CI = &get_instance();

        $data = array();
        $data['status'] = $status;

        $CI->db->where('batchNumber', $batchNumber);
        $CI->db->where('itemMasterID', $itemAutoID);
        $CI->db->where('invoice_num', $invoice_no);
        $CI->db->where('companyId', current_companyID());
        $result = $CI->db->update('srp_erp_inventory_itembatch_reserved', $data);

        return $result;

    }
}



if (!function_exists('get_pos_itembatch_for_expire_order')) {
    function get_pos_itembatch_for_expire_order($wareHouseID,$itemMasterID)
    {

        $date = date('Y-m-d',strtotime(current_date()));

        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_inventory_itembatch');
        $CI->db->where('companyId', current_companyID());
        $CI->db->where('itemMasterID', $itemMasterID);
        $CI->db->where('wareHouseAutoID', $wareHouseID);
        $CI->db->where('batchExpireDate >=', "$date");
        $CI->db->where('qtr >', 0);
        $CI->db->order_by('batchExpireDate', 'ASC');
        $result = $CI->db->get()->result_array();
        
        return $result;
    }
}

if (!function_exists('get_pos_itembatch_total_qunatity')) {
    function get_pos_itembatch_total_qunatity($wareHouseID,$itemMasterID)
    {

        $date = date('Y-m-d',strtotime(current_date()));

        $CI = &get_instance();
        $CI->db->select("SUM(qtr) as itemCount");
        $CI->db->from('srp_erp_inventory_itembatch');
        $CI->db->where('companyId', current_companyID());
        $CI->db->where('itemMasterID', $itemMasterID);
        $CI->db->where('wareHouseAutoID', $wareHouseID);
        $CI->db->where('batchExpireDate >=', "$date");
        $CI->db->where('qtr >', 0);
        $result = $CI->db->get()->row('itemCount');
        
        return $result;
    }
}


if (!function_exists('get_pos_itembatch_from_batch_number')) {
    function get_pos_itembatch_from_batch_number($batch_number,$itemAutoID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_inventory_itembatch');
        $CI->db->where('companyId', current_companyID());
        $CI->db->where('batchNumber', $batch_number);
        $CI->db->where('itemMasterID', $itemAutoID);
        $result = $CI->db->get()->row_array();
        
        return $result;
    }
}


if (!function_exists('get_segment_drop')) {
    function get_segment_drop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_segment');
        $CI->db->where('companyID', current_companyID());
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['segmentID'] ?? '')] = $row['companyCode'] . ' - ' . $row['description'];
            }
        }
        return $output_arr;
    }
}


if (!function_exists('get_templateMaster_drop')) {
    function get_templateMaster_drop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_templatemaster');
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['posTemplateID'] ?? '')] = $row['posTemplateDescription'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_industryTypes_drop')) {
    function get_industryTypes_drop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_industrytypes');
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['industrytypeID'] ?? '')] = $row['industryTypeDescription'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('col_posConfig')) {
    function col_posConfig($id)
    {
        $output = '<div style="text-align: center;">';

        //$output .= '<button class="btn btn-default btn-xs" onclick="employee_profile_edit(\'' . $id . '\')" rel="tooltip" title="Edit Profile" ><i class="fa fa-edit"></i></button>';
        $output .= '<button class="btn btn-danger btn-xs" onclick="delete_segmentConfig(\'' . $id . '\')" rel="tooltip" title="Delete" ><i class="fa fa-trash"></i></button>';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('posConfig_menu')) {
    function posConfig_menu($id)
    {
        $output = '';
        $output .= '<div style="text-align: center;"><button class="btn btn-default btn-xs posConfigBtn text-yellow" onclick="setup_menu(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-bars"></i></button></div>';

        return $output;
    }
}

if (!function_exists('posConfig_crew')) {
    function posConfig_crew($id)
    {
        $output = '';
        $output .= '<div style="text-align: center;"><button class="btn btn-default btn-xs posConfigBtn text-green" onclick="load_pos_crew_config(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-user"></i></button></div>';

        return $output;
    }
}

if (!function_exists('posConfig_kot')) {
    function posConfig_kot($id)
    {
        $output = '';
        $output .= '<div style="text-align: center;"><button class="btn btn-default btn-xs posConfigBtn text-purple" onclick="load_pos_kot(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-building-o"></i></button></div>';

        return $output;
    }
}


if (!function_exists('posConfig_btn_table')) {
    function posConfig_btn_table($id, $warehouseid)
    {
        $output = '';
        $output .= '<div style="text-align: center;"><button class="btn btn-default btn-xs posConfigBtn text-blue" onclick="load_pos_room_config(\'' . $id . '\',\'' . $warehouseid . '\')" rel="tooltip" ><i class="fa fa-life-ring"></i></button></div>';

        return $output;
    }
}

if (!function_exists('table_class_pos')) {
    function table_class_pos($id = null)
    {
        switch ($id) {
            case 1:
                $output = 'table table-bordered table-striped table-hover table-condensed';

                break;
            case 2:
                $output = 'table table-bordered table-striped table-hover';
                break;

            case 3:
                $output = 'table table-bordered';
                break;
            case 4:
                $output = 'table table-striped table-hover table-condensed';
                break;
            case 5:
                $output = 'table table-bordered table-hover table-condensed';
                break;
            default:
                $output = 'table table-bordered table-striped table-condensed';
        }

        return $output;
    }
}


if (!function_exists('current_company_code')) {
    function current_company_code()
    {
        $CI = &get_instance();
        $CI->load->library('session');
        return trim($CI->session->userdata("current_company_code") ?? '');
    }
}

if (!function_exists('format_date_mysql_datetime')) {
    function format_date_mysql_datetime($date = null)
    {
        if (isset($date)) {
            if (!empty($date)) {
                return date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date)));
            }
        } else {
            return date('Y-m-d H:i:s', time());
        }
    }
}


if (!function_exists('get_segment_code')) {
    function get_segment_code($segmentID)
    {
        $CI = &get_instance();
        $CI->db->select("segmentCode");
        $CI->db->from('srp_erp_segment');
        $CI->db->where('segmentID', $segmentID);
        $result = $CI->db->get()->row_array();
        if (!empty($result)) {
            return $result['segmentCode'];
        } else {
            return null;
        }
    }
}

if (!function_exists('deleteConfirmationMsg')) {
    function deleteConfirmationMsg()
    {
        return '<div style="font-size: 12px; color: #c0392b; line-height: 21px;"><strong><i class="fa fa-trash fa-2x"></i> Confirmation</strong> <br/>Are you sure want to delete? </div>';
    }
}

if (!function_exists('confirmDocumentConfirmationMsg')) {
    function confirmDocumentConfirmationMsg()
    {
        return '<div style="font-size: 12px; color: #c0a820; line-height: 21px;"><strong><i class="fa fa-check fa-2x"></i> Confirmation</strong> <br/>Are you sure want to confirm? </div>';
    }
}

if (!function_exists('current_pc')) {
    function current_pc()/*current Client PC*/
    {
        return gethostbyaddr($_SERVER['REMOTE_ADDR']);
    }
}

if (!function_exists('user_group')) {
    function user_group()/*Group */
    {
        $CI = &get_instance();
        $CI->load->library('session');
        return trim($CI->session->userdata("usergroupID"));
    }
}


if (!function_exists('loader_div')) {
    function loader_div()
    {
        $output = '<div style="text-align: center; padding: 10px;"> <i class="fa fa-refresh fa-2x fa-spin" aria-hidden="true"></i> Loading Data</div>';
        return $output;
    }
}

if (!function_exists('item_tb_checkbox')) {
    function item_tb_checkbox($itemAutoID, $code, $description)
    {
        $output = '<div style="text-align: center">';
        $output .= '<input type="checkbox" name="selectedItems[]" class="itemChk" value="' . $itemAutoID . '" ';
        $output .= 'data-code="' . $code . '" data-description="' . $description . '"/></div>';
        return $output;
    }
}

/** Menu : image | Data Table */
if (!function_exists('menuImage')) {
    function menuImage($path)
    {
        $tmpPath = base_url($path);
        $output = '<img src="' . $tmpPath . '" style="width:50px; height:50px;">';
        return $output;
    }
}


/** Format Number  | Data Table output */
if (!function_exists('format_number_dataTable')) {
    function format_number_dataTable($number, $decimal = 2, $align = 'right')
    {
        $actual = $number;
        $number = $number > 0 ? $number : 0;
        $tmpInput = number_format($number, $decimal);
        $output = '<div style="text-align: ' . $align . '">' . $tmpInput . '</div>';
        return $output;
    }
}

/** Menu : Btn Set  | Helper */
if (!function_exists('col_btnSet')) {
    function col_btnSet($id, $categoryID = 0, $isPack = 0)
    {
        $cls = $isPack == 0 ? 'display: none; ' : '';
        $output = '<div style="text-align: right; ">';
        //$output .= '<span><input type="text" style="width: 20px;"/> </span>';
        $output .= '<span id="packBtnID_' . $id . '" style="' . $cls . '">';
        $output .= '<button class="btn btn-default btn-sm" onclick="packConfig_modal(\'' . $id . '\')" rel="tooltip"  ><i class="fa fa-coffee" aria-hidden="true"></i></button>&nbsp;&nbsp;&nbsp;';
        $output .= '</span>';

        $output .= '<button class="btn btn-default btn-sm" onclick="loadMenuDetail(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-cogs"></i></button>&nbsp;&nbsp;&nbsp;&nbsp;';
        //$output .= '<button class="btn btn-default btn-sm" onclick="" rel="tooltip" data-original-title="" title=""><i class="fa fa-cogs"></i></button>&nbsp;&nbsp;&nbsp;&nbsp;';
        $output .= '<button class="btn btn-default btn-sm" onclick="editMenu(\'' . $id . '\',\'' . $categoryID . '\')" rel="tooltip" title="Edit Menu" ><i class="fa fa-edit"></i></button>&nbsp;&nbsp;&nbsp;';
        $output .= '<button class="btn btn-danger btn-sm" onclick="deleteMenu(\'' . $id . '\')" rel="tooltip" title="Delete" ><i class="fa fa-trash"></i></button>';

        $output .= '</div>';

        return $output;
    }
}

/** get Status | Data Table */
if (!function_exists('get_active_status')) {
    function get_active_status($status)
    {
        if ($status == 1 || $status == -1) {
            $output = '<div style="text-align: center;"><span class="label label-success">Active</span>
</div>';
        } else {
            $output = '<div style="text-align: center;"><span class="label label-default">in-Active</span></div>';
        }
        return $output;
    }
}

if (!function_exists('get_warehouse_category_drop')) {
    function get_warehouse_category_drop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_menucategory');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isDeleted', 0);
        $CI->db->where('isActive', 1);
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['menuCategoryID'] ?? '')] = $row['menuCategoryDescription'];
            }
        }
        return $output_arr;
    }
}

/** Menu Setup : Btn Set  | Helper */
if (!function_exists('col_btnSet_setup')) {
    function col_btnSet_setup($id, $isActive, $categoryID = 0, $isShortCut = 0)
    {
        $output = '<div style="text-align: center;">';
        if ($isShortCut == 1) {
            $output .= '<input type="checkbox" id="shortcutActivate_' . $id . '" name="shortcutActivate" onchange="changeShortcut(' . $id . ')" data-size="mini" data-on-text="shortcut&nbsp;on" data-handle-width="60" data-off-color="default" data-on-color="info" data-off-text="shortcut&nbsp;Off" data-label-width="0" checked>';
        } else if ($isShortCut == 0) {
            $output .= '<input type="checkbox" id="shortcutActivate_' . $id . '" name="shortcutActivate" onchange="changeShortcut(' . $id . ')" data-size="mini" data-on-text="shortcut&nbsp;on" data-handle-width="60" data-off-color="default" data-on-color="info" data-off-text="shortcut&nbsp;Off" data-label-width="0">';
        }

        if ($isActive == 1) {
            $output .= ' | <input type="checkbox" id="warehousemenumasterisactive_' . $id . '" name="warehousemenumasterisactive" onchange="changewarehousemenumasterisactive(' . $id . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else if ($isActive == 0) {
            $output .= ' | <input type="checkbox" id="warehousemenumasterisactive_' . $id . '" name="warehousemenumasterisactive" onchange="changewarehousemenumasterisactive(' . $id . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }

        $output .= ' | <button class="btn btn-danger btn-xs" onclick="deleteMenu_setup(\'' . $id . '\')" rel="tooltip" title="Delete" ><i class="fa fa-trash"></i></button>';

        $output .= '</div>';

        return $output;
    }
}


/** Menu Setup : Btn Set  | Helper */
if (!function_exists('get_wareHouseMenuByCategory')) {
    function get_wareHouseMenuByCategory($categoryID)
    {
        $CI = &get_instance();
        $path = base_url();
        $CI->db->select("category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, menuMaster.isPack  , categoryMaster.bgColor , size.description as sizeDescription, size.code as sizeCode, size.colourCode, menu.kotID, menuMaster.showImageYN as showImageYN");
        $CI->db->from("srp_erp_pos_warehousemenumaster menu");
        $CI->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $CI->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $CI->db->join("srp_erp_pos_menucategory categoryMaster", "category.menuCategoryID = categoryMaster.menuCategoryID", "INNER");
        $CI->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "LEFT");
        $CI->db->where('menu.isActive', 1);
        $CI->db->where('menu.isDeleted', 0);
        $CI->db->where('categoryMaster.isActive', 1);
        $CI->db->where('menuMaster.isDeleted', 0);
        $CI->db->where('menuMaster.menuStatus', 1);
        $CI->db->where('menu.warehouseMenuCategoryID', $categoryID);
        $CI->db->order_by('menuMaster.sortOrder', 'asc');
        $CI->db->order_by('menuMaster.menuMasterDescription', 'asc');
        $result = $CI->db->get()->result_array();
        /*echo $CI->db->last_query();
        exit;*/
        return $result;
    }
}


if (!function_exists('get_add_on_list')) {
    function get_add_on_list()
    {
        $CI = &get_instance();
        $path = base_url();
        $CI->db->select("menu.warehouseMenuID as autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, menuMaster.isPack  , categoryMaster.bgColor , size.description as sizeDescription, size.code as sizeCode, size.colourCode, menu.kotID, menuMaster.showImageYN as showImageYN");
        $CI->db->from("srp_erp_pos_warehousemenumaster menu");
        $CI->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "INNER");
        $CI->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $CI->db->join("srp_erp_pos_menucategory categoryMaster", "category.menuCategoryID = categoryMaster.menuCategoryID", "INNER");
        $CI->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "LEFT");
        $CI->db->where('menu.isActive', 1);
        $CI->db->where('menu.isDeleted', 0);
        $CI->db->where('menuMaster.isDeleted', 0);
        $CI->db->where('menuMaster.isAddOn', 1);
        $CI->db->where('menu.warehouseID', get_outletID());
        $CI->db->group_by('menu.menuMasterID');
        $CI->db->order_by('menuMaster.sortOrder', 'asc');
        $CI->db->order_by('menuMaster.menuMasterDescription', 'asc');
        $result = $CI->db->get()->result_array();
        //echo $CI->db->last_query();
        return $result;
    }
}

/** BARCODE  */
if (!function_exists('get_barcode_menus')) {
    function get_barcode_menus()
    {
        $CI = &get_instance();
        $q = "SELECT
                    menuMaster.barcode, 
                    menu.warehouseMenuID,
                    `menuMaster`.`menuMasterDescription`,
                    `menuMaster`.`sellingPrice`,
                    `menuMaster`.`isPack`,
                    `categoryMaster`.`bgColor`,
                    `size`.`description` AS `sizeDescription`,
                    `size`.`code` AS `sizeCode`,
                    `size`.`colourCode`,
                    `menu`.`kotID` 
                FROM
                    `srp_erp_pos_warehousemenumaster` AS menu
                    INNER JOIN `srp_erp_pos_warehousemenucategory` `category` ON `menu`.`warehouseMenuCategoryID` = `category`.`autoID`
                    LEFT JOIN `srp_erp_pos_menumaster` `menuMaster` ON `menuMaster`.`menuMasterID` = `menu`.`menuMasterID`
                    INNER JOIN `srp_erp_pos_menucategory` `categoryMaster` ON `category`.`menuCategoryID` = `categoryMaster`.`menuCategoryID`
                    LEFT JOIN `srp_erp_pos_menusize` `size` ON `size`.`menuSizeID` = `menuMaster`.`menuSizeID` 
                WHERE
                    `menu`.`isActive` = 1 
                    AND `menu`.`isDeleted` = 0 
                    AND `menuMaster`.`isDeleted` = 0 
                    AND menu.warehouseID = '" . get_outletID() . "' 
                    AND menuMaster.barcode IS NOT NULL 
                GROUP BY
                    menuMaster.menuMasterID;";
        $result = $CI->db->query($q)->result_array();
        //echo $CI->db->last_query();
        return $result;
    }
}


/** Menu Setup : Btn Set  Shortcut only | Helper */
if (!function_exists('get_warehouseMenuShortcuts')) {
    function get_warehouseMenuShortcuts()
    {
        $outletID = get_outletID();
        $CI = &get_instance();
        $path = base_url();
        $CI->db->select("category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, menuMaster.isPack  , categoryMaster.bgColor , size.description as sizeDescription, size.code as sizeCode, size.colourCode, menu.kotID, menuMaster.showImageYN as showImageYN");
        $CI->db->from("srp_erp_pos_warehousemenumaster menu");
        $CI->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $CI->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "left");
        $CI->db->join("srp_erp_pos_menucategory categoryMaster", "category.menuCategoryID = categoryMaster.menuCategoryID", "INNER");
        $CI->db->join("srp_erp_pos_menusize size", "size.menuSizeID = menuMaster.menuSizeID", "LEFT");
        $CI->db->where('menu.isActive', 1);
        $CI->db->where('category.isActive', 1);
        $CI->db->where('category.isDeleted', 0);
        $CI->db->where('menu.isDeleted', 0);
        $CI->db->where('menuMaster.isDeleted', 0);
        $CI->db->where('menu.warehouseID', $outletID);
        $CI->db->where('menu.isShortcut', 1);
        $CI->db->group_by('menuMaster.menuMasterID');
        $CI->db->order_by('menuMaster.sortOrder', 'asc');
        $CI->db->order_by('menuMaster.menuMasterDescription', 'asc');
        $result = $CI->db->get()->result_array();
        //echo $CI->db->last_query();
        return $result;
    }
}


if (!function_exists('get_wareHouseMenuByCategory_All')) {
    function get_wareHouseMenuByCategory_All()
    {
        $CI = &get_instance();
        $path = base_url();
        $CI->db->select("category.autoID, menu.warehouseMenuID , menu.warehouseID, menuMaster.menuMasterDescription, concat('" . $path . "',menuMaster.menuImage) as menuImage, menuMaster.sellingPrice, menuMaster.isPack  ");
        $CI->db->from("srp_erp_pos_warehousemenumaster menu");
        $CI->db->join("srp_erp_pos_warehousemenucategory category", "menu.warehouseMenuCategoryID = category.autoID", "inner");
        $CI->db->join("srp_erp_pos_menumaster menuMaster", "menuMaster.menuMasterID = menu.menuMasterID", "inner");
        $CI->db->where('menu.isActive', 1);
        $CI->db->where('menu.isDeleted', 0);
        $CI->db->where('menuMaster.isDeleted', 0);
        $CI->db->where('category.isDeleted', 0);
        $CI->db->where('category.isActive', 1);
        $result = $CI->db->get()->result_array();
        return $result;
    }
}

/** Restaurant Pos Check Invoice Session or Get Invoice ID */
if (!function_exists('isPos_invoiceSessionExist')) {
    function isPos_invoiceSessionExist()
    {
        $CI = &get_instance();
        $invoiceID = $CI->session->userdata('pos_invoice_no');
        if (isset($invoiceID) && !empty($invoiceID)) {
            return $invoiceID;
        } else {
            return false;
        }
    }
}


/** Set Invoice ID : Session data */
if (!function_exists('set_session_invoiceID')) {
    function set_session_invoiceID($id)
    {
        $CI = &get_instance();
        $data = array('pos_invoice_no' => $id);
        $CI->session->set_userdata($data);
    }
}


if (!function_exists('load_item_drop')) {
    function load_item_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("itemAutoID,itemSystemCode,itemDescription");
        $CI->db->FROM('srp_erp_itemmaster');
        $CI->db->where('mainCategory', "Inventory");
        $CI->db->where('companyID', current_companyID());
        $item = $CI->db->get()->result_array();
        return $item;
    }
}

if (!function_exists('padZeros_saleInvoiceID')) {
    function padZeros_saleInvoiceID($id)
    {
        $result = str_pad($id, 4, "0", STR_PAD_LEFT);
        return $result;
    }
}

if (!function_exists('col_posConfig_rooms')) {
    function col_posConfig_rooms($id, $wareHouseAutoID)
    {
        $output = '';
        $output .= '<div style="text-align: center;">';
        $output .= '<button class="btn btn-default btn-sm" onclick="add_pos_tables_config(\'' . $id . '\',\'' . $wareHouseAutoID . '\')" rel="tooltip" ><img src="' . base_url('images/pos/fork-icon.png') . '"/>  Table </button> &nbsp;&nbsp; ';
        $output .= '<button class="btn btn-default btn-xs" onclick="edit_pos_room_config(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button> &nbsp;&nbsp;';
        $output .= '<button class="btn btn-danger btn-xs" onclick="delete_pos_room_config(\'' . $id . '\',\'' . $wareHouseAutoID . '\')" rel="tooltip" ><i class="fa fa-trash-o"></i></button>';
        $output .= '</div>';
        return $output;
    }
}

if (!function_exists('col_posConfig_tables')) {
    function col_posConfig_tables($id, $diningRoomMasterID)
    {
        $output = '';
        $output .= '<div style="text-align: center;"><button class="btn btn-default btn-xs" onclick="edit_pos_table_config(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button> | <button class="btn btn-danger btn-xs" onclick="delete_pos_table_config(\'' . $id . '\',\'' . $diningRoomMasterID . '\')" rel="tooltip" ><i class="fa fa-trash-o"></i></button></div>';

        return $output;
    }
}

if (!function_exists('get_totalTax')) {
    function get_totalTax()
    {
        $q = "SELECT sum(taxPercentage) percentage FROM srp_erp_pos_menutaxmaster WHERE companyID=" . current_companyID();
        $CI = &get_instance();
        $result = $CI->db->query($q)->row_array();
        $tax = !empty($result) ? $result['percentage'] : 0;
        $totalTax = $tax;
        if (empty($result['percentage'])) {
            $totalTax = 0;
        }
        return $totalTax;
    }
}

if (!function_exists('get_defaultServiceCharge')) {
    function get_defaultServiceCharge()
    {
        $serviceCharge = 0; // 5%
        return $serviceCharge;
    }
}

if (!function_exists('cancelOrderConfirmation')) {
    function cancelOrderConfirmation()
    {
        return '<div style="font-size: 12px; color: #bc9d00; line-height: 21px;"><strong><i class="fa fa-check fa-2x"></i> Confirmation </strong> <br/>Are you sure want to cancel the order? </div>';
    }
}

if (!function_exists('createNewInvoiceConfirmation')) {
    function createNewInvoiceConfirmation()
    {
        return '<div style="font-size: 12px; color: #bc9d00; line-height: 21px;"><strong><i class="fa fa-check fa-2x"></i> Confirmation </strong> <br/>Are you sure want to Create new order? <br><br>Note : You can open the current order by clicking open hold</div>';
    }
}

if (!function_exists('col_posConfig_Crews')) {
    function col_posConfig_Crews($id, $wareHouseAutoID)
    {
        $output = '';
        $output .= '<div style="text-align: center;"> <button class="btn btn-default btn-xs" onclick="edit_pos_crew_config(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button> | <button class="btn btn-danger btn-xs" onclick="delete_pos_crew_config(\'' . $id . '\',\'' . $wareHouseAutoID . '\')" rel="tooltip" ><i class="fa fa-trash-o"></i></button></div>';

        return $output;
    }
}

if (!function_exists('col_posConfig_crewRoles')) {
    function col_posConfig_crewRoles($id)
    {
        $output = '';
        $output .= '<div style="text-align: center;"><button class="btn btn-default btn-xs" onclick="edit_pos_crew_roles_config(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button> | <button class="btn btn-danger btn-xs" onclick="delete_pos_crew_roles_config(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-trash-o"></i></button></div>';

        return $output;
    }
}

if (!function_exists('col_isWaiter')) {
    function col_isWaiter($isWaiter)
    {
        $output = '';
        if ($isWaiter == 0) {
            $output = '<div class="text-center"> <i class="fa fa-times text-red" aria-hidden="true"></i></div>';
        } else if ($isWaiter == 1) {
            $output = '<div class="text-center"> <i class="fa fa-check text-green" aria-hidden="true"></i></div>';
        }

        return $output;
    }
}


if (!function_exists('load_crew_role_drop')) {
    function load_crew_role_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("crewRoleID,roleDescription");
        $CI->db->FROM('srp_erp_pos_crewroles');
        $CI->db->where('companyID', current_companyID());
        $role = $CI->db->get()->result_array();
        return $role;
    }
}

if (!function_exists('load_employee_for_crew_drop')) {
    function load_employee_for_crew_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("EIdNo,Ename1,Ename2,ECode");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        $employee = $CI->db->get()->result_array();
        return $employee;
    }
}

if (!function_exists('get_outletInfo')) {
    function get_outletInfo()
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_warehousemaster.*, srp_erp_warehouse_users.counterID");
        $CI->db->from('srp_erp_warehouse_users');
        $CI->db->join('srp_erp_warehousemaster', ' srp_erp_warehousemaster.wareHouseAutoID =srp_erp_warehouse_users.wareHouseID ', 'left');
        $CI->db->where('srp_erp_warehouse_users.userID', current_userID());
        $CI->db->where('srp_erp_warehouse_users.companyID', current_companyID());
        $CI->db->where('srp_erp_warehousemaster.isPosLocation', 1);
        $CI->db->where('srp_erp_warehousemaster.isActive', 1);
        $CI->db->where('srp_erp_warehouse_users.isActive', 1);
        $result = $CI->db->get()->row_array();
        return $result;
    }
}

if (!function_exists('get_isLocalPosEnabled')) {
    function get_isLocalPosEnabled()
    {
        $CI = &get_instance();

        $result = $CI->db->select('w.*')
            ->from('srp_erp_warehouse_users w')
            ->where('w.userID', current_userID())
            ->where('w.companyID', current_companyID())
            ->where('w.isActive', 1)
            ->get()->row_array();

        if (!empty($result)) {
            $output = $CI->db->select('*')
                ->from('srp_erp_warehousemaster')
                ->where('wareHouseAutoID', $result['wareHouseID'])->get()->row_array('');
            if ($output['isLocalPosEnabled']) {
                return array('error' => 1, 'type' => 'w', 'message' => '<i class="fa fa-wrench fa-3x"></i>&nbsp;&nbsp;<strong>Please login to Local POS.</strong> <br/><br/>Local POS has implemented to ' . $output['wareHouseDescription'] . ' - ' . $output['wareHouseCode']);
            } else {
                return array('error' => 0, 'type' => 's', 'message' => 'user can use Live POS, isLocalPosEnabled=' . $output['wareHouseCode'] . ', allow using it.');
            }
        } else {
            return array('error' => 1, 'type' => 'e', 'message' => 'You have not assigned to any outlet');
        }
    }
}


if (!function_exists('format_dateTime_pos_printFormat_date')) {
    function format_dateTime_pos_printFormat_date($date = null)
    {
        if (isset($date) && !empty($date)) {
            return date('d/m/Y G:i A', strtotime($date));
        } else {
            return date('d/m/Y h:i A', time());
        }
    }
}

if (!function_exists('wareHouseDetails')) {
    function wareHouseDetails($id)
    {
        $CI = &get_instance();
        $CI->db->select("warehouseAddress, warehouseTel, wareHouseDescription, wareHouseCode,wareHouseLocation");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('wareHouseAutoID', $id);
        $result = $CI->db->get()->row_array();
        return $result;
    }
}


if (!function_exists('btn_openHold')) {
    function btn_openHold($id, $desc = 'Open Hold', $outletID = 0)
    {
        $output = '<div style="text-align: center;">';

        $output .= '<button class="btn btn-default btn-xs" onclick="openHold_sales(\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="' . $desc . '" ><i class="fa fa-external-link" aria-hidden="true"></i> ' . $desc . '</button>';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('wowfood_status')) {
    function wowfood_status($status)
    {
        $output = '<div style="text-align: center;">';

        if ($status == 0) {
            $output .= 'Open';
        } else {
            $output .= 'Accepted';
        }

        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('btn_wowfood_preview')) {
    function btn_wowfood_preview($id, $desc = 'Open Hold', $outletID = 0)
    {
        $output = '<div style="text-align: center;">';
        $output .= '<button class="btn btn-default btn-xs" onclick="previewHoldSales(\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="Preview" ><i class="fa fa-eye" aria-hidden="true"></i> preview</button>';
        $output .= '<button class="btn btn-default btn-xs" onclick="open_wowfood_sales(\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="' . $desc . '" ><i class="fa fa-external-link" aria-hidden="true"></i> ' . $desc . '</button>&nbsp;&nbsp;';
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('payableGL_drop')) {
    function payableGL_drop()
    {
        /*un deposited cashier funds*/
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        /*$data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }*/
        return $data;
    }
}


if (!function_exists('load_menus_for_menucategory_drop')) {
    function load_menus_for_menucategory_drop($id)
    {
        $CI = &get_instance();
        $menucatid = $CI->db->query("select menuCategoryID from srp_erp_pos_warehousemenucategory where autoID=$id")->row_array();
        if ($menucatid) {
            $catid = $menucatid['menuCategoryID'];
            $menumaster = $CI->db->query("select menuMasterID,menuMasterDescription,menuCategoryID from srp_erp_pos_menumaster where menuCategoryID = $catid ")->result_array();
            return $menumaster;
        }
    }
}


if (!function_exists('isPaxBtn')) {
    function isPaxBtn($id, $categoryID = 0, $value = 0)
    {
        $checked = $value == 1 ? 'checked' : '';

        $output = '<div style="text-align: center;">';

        //$output .= $id . ' - ' . $categoryID . ' - ' . $value . '<br/>';
        $output .= '<input class="mySwitch" type="checkbox" id="isPax_' . $id . $categoryID . '"  name="isPax" 
                                   onchange="updateIsPaxValue(' . $id . ',\'m\',' . $categoryID . ')"
                                   data-size="mini" data-on-text="<i class=\'fa fa-coffee text-purple\'></i> Pax"
                                   data-handle-width="50" data-off-color="default" data-on-color="default"
                                   data-off-text="No" data-label-width="0" ' . $checked . ' >';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('showImageYN_btn')) {
    function showImageYN_btn($id, $categoryID = 0, $value = 0)
    {
        $checked = $value == 1 ? 'checked' : '';

        $output = '<div style="text-align: center;">';

        //$output .= $id . ' - ' . $categoryID . ' - ' . $value . '<br/>';
        $output .= '<input class="mySwitch" type="checkbox" id="showImageYN_' . $id . $categoryID . '"  name="showImageYN" 
                                   onchange="update_showImageYN(' . $id . ',\'m\',' . $categoryID . ')"
                                   data-size="mini" data-on-text="<i class=\'fa fa-image text-green\'></i> On"
                                   data-handle-width="50" data-off-color="default" data-on-color="default"
                                   data-off-text="Off" data-label-width="0" ' . $checked . ' >';

        $output .= '</div>';

        return $output;
    }
}


/** Pack / Pax configuration */
if (!function_exists('get_itemPackItem')) {
    function get_get_itemPackItem()
    {
        $CI = &get_instance();
        $CI->db->select("menuMaster.menuMasterID, menuMaster.menuMasterDescription, menuMaster.menuCategoryID, menuCategory.menuCategoryDescription,size.description as sizeDesc");
        $CI->db->from('srp_erp_pos_menumaster menuMaster');
        $CI->db->join('srp_erp_pos_menucategory menuCategory', 'menuCategory.menuCategoryID = menuMaster.menuCategoryID', 'left');
        $CI->db->join('srp_erp_pos_menusize size', 'size.menuSizeID = menuMaster.menuSizeID', 'left');
        $CI->db->where('menuMaster.companyID', current_companyID());
        $CI->db->where('menuMaster.isDeleted', 0);
        $CI->db->where('menuMaster.isPack', 0);
        $CI->db->where('menuCategory.isActive', 1);
        $CI->db->where('menuCategory.isDeleted', 0);
        //$CI->db->where('menuCategory.isPack', 0);
        $result = $CI->db->get()->result_array();

        //echo $CI->db->last_query();
        //exit;


        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                if (!empty($row['sizeDesc'])) {

                    $output_arr[trim($row['menuMasterID'] ?? '')] = $row['menuMasterDescription'] . ' - ' . $row['sizeDesc'];
                } else {

                    $output_arr[trim($row['menuMasterID'] ?? '')] = $row['menuMasterDescription'];
                }
            }
        }
        return $output_arr;
    }
}


if (!function_exists('col_pos_packItem')) {
    function col_pos_packItem($id)
    {
        $output = '<div style="text-align: center;">';
        /*$output .= '<button class="btn btn-default btn-xs" onclick="edit_pos_crew_config(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button>';
        $output .= '| ';*/
        $output .= '<button class="btn btn-danger btn-xs" onclick="delete_pos_packGroup(' . $id . ')" rel="tooltip" ><i class="fa fa-trash-o"></i></button>';
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('col_pos_packItemCategory')) {
    function col_pos_packItemCategory($id)
    {
        $output = '<div style="text-align: center;">';
        /*$output .= '<button class="btn btn-default btn-xs" onclick="edit_pos_crew_config(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button>';
        $output .= '| ';*/
        $output .= '<button class="btn btn-danger btn-xs" onclick="delete_pos_packItemCategory(' . $id . ')" rel="tooltip" ><i class="fa fa-trash-o"></i></button>';
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('packNoOfItems_update')) {
    function packNoOfItems_update($id, $value)
    {
        $output = '<div style="text-align: right;">';

        $output .= '<input type="number"  style="text-align: right;" value="' . $value . '" onchange="update_pack_noOfItem(this,' . $id . ')"/>';
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('packGroupQty_update')) {
    function packGroupQty($id, $value, $isRequired)
    {
        $output = '<div style="text-align: right;">';
        if ($isRequired == 0) {
            $output .= '<input type="number"  style="text-align: right;" value="' . $value . '" onchange="update_pack_noOfItem(this,' . $id . ')"/>';
        }
        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('get_glCode_rpos')) {
    function get_glCode_rpos()
    {
        $CI = &get_instance();
        $CI->db->select("chartOfAccount.GLAutoID, chartOfAccount.systemAccountCode, chartOfAccount.GLSecondaryCode, chartOfAccount.GLDescription");
        $CI->db->from('srp_erp_chartofaccounts chartOfAccount');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('subCategory', 'PLI');
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['GLAutoID'] ?? '')] = $row['systemAccountCode'] . ' - ' . $row['GLDescription'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_srp_erp_pos_menupackcategory')) {
    function get_srp_erp_pos_menupackcategory($valuePackID, $categoryID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_menupackgroupmaster packCategory');
        $CI->db->where('packCategory.packMenuID', $valuePackID);
        $CI->db->where('packCategory.groupMasterID', $categoryID);
        $result = $CI->db->get()->row_array();
        /*echo $CI->db->last_query();*/
        return $result;
    }
}

if (!function_exists('get_counterData')) {
    function get_counterData()
    {
        $CI = &get_instance();
        $where = array(
            'empID' => current_userID(),
            'companyID' => current_companyID(),
            'wareHouseID' => current_warehouseID(),
            'isClosed' => 0,
        );

        return $CI->db->select('*')->from('srp_erp_pos_shiftdetails')->where($where)->get()->row_array();
    }
}

if (!function_exists('get_totalCashSales')) {
    function get_totalCashSales($dataArray)
    {
        $counterID = isset($dataArray['counterID']) ? $dataArray['counterID'] : 0;
        $shiftID = isset($dataArray['shiftID']) ? $dataArray['shiftID'] : 0;

        $CI = &get_instance();
        //$q = "SELECT * FROM srp_erp_pos_menusalesmaster WHERE paymentMethod = 1 AND counterID = " . $counterID . " AND shiftID = " . $shiftID;
        $q = "SELECT
                    SUM(payments.amount) as NetCashSales
                FROM
                    srp_erp_pos_menusalesmaster salesMaster
                LEFT JOIN srp_erp_pos_menusalespayments payments ON payments.menuSalesID = salesMaster.menuSalesID
                WHERE
                    payments.paymentConfigMasterID = 1
                AND counterID = " . $counterID . "
                AND shiftID = " . $shiftID . " AND salesMaster.isHold = 0 AND salesMaster.isVoid = 0 ";
        //echo $q;
        $result = $CI->db->query($q)->row_array();
        return $result;
    }
}


if (!function_exists('isVegBtn')) {
    function isVegBtn($id, $categoryID = 0, $value = 0)
    {
        $checked = $value == 1 ? 'checked' : '';

        $output = '<div style="text-align: center;">';

        //$output .= $id . ' - ' . $categoryID . ' - ' . $value . '<br/>';
        $output .= '<input class="mySwitch_veg" type="checkbox" id="isVeg_' . $id . $categoryID . '"  name="isVeg" 
                                   onchange="updateIsVegValue(' . $id . ',\'m\',' . $categoryID . ')"
                                   data-size="mini" data-on-text="<i class=\'fa fa fa-circle text-green\' title=\'Vegetarian\'></i> "
                                   data-handle-width="50" data-off-color="default" data-on-color="default"
                                   data-off-text="<i class=\'fa fa fa-circle text-red\' title=\'Non-Veg\'></i>" data-label-width="0" ' . $checked . ' >';

        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('getCustomerType_drop')) {
    function getCustomerType_drop()
    {
        $CI = &get_instance();
        $CI->db->select("customerType.*");
        $CI->db->from('srp_erp_customertypemaster customerType');
        $CI->db->where('company_id', current_companyID());
        $result = $CI->db->get()->result_array();
        //$output_arr = array('' => 'Please Select');
        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['customerTypeID'] ?? '')] = $row['customerDescription'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('getCustomerType')) {
    function getCustomerType()
    {
        $CI = &get_instance();
        $CI->db->select("customerType.*");
        $CI->db->from('srp_erp_customertypemaster customerType');
        $CI->db->where('company_id', current_companyID());
        /*$CI->db->where('(customerDescription="Eat-in" OR customerDescription="Dine-in" OR  customerDescription="Delivery Orders"  OR customerDescription="Take-away" OR customerDescription="Direct sale") ', null, false);*/

        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('getCustomerTypesFromMenuSalesMaster')) {
    function getCustomerTypesFromMenuSalesMaster()
    {
        $CI = &get_instance();
        $CI->db->select("customerType.*");
        $CI->db->from('srp_erp_customertypemaster customerType');
        $CI->db->join('srp_erp_pos_menusalesmaster', 'srp_erp_pos_menusalesmaster.customerTypeID=customerType.customerTypeID');
        //        $CI->db->where('customerType.company_id', current_companyID());
        $CI->db->where('srp_erp_pos_menusalesmaster.companyID', current_companyID());
        $CI->db->group_by('srp_erp_pos_menusalesmaster.customerTypeID');
        /*$CI->db->where('(customerDescription="Eat-in" OR customerDescription="Dine-in" OR  customerDescription="Delivery Orders"  OR customerDescription="Take-away" OR customerDescription="Direct sale") ', null, false);*/

        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('getPromotion')) {
    function getPromotion()
    {
        $CI = &get_instance();
        $CI->db->select("customerType.*");
        $CI->db->from('srp_erp_customertypemaster customerType');
        $CI->db->where('company_id', current_companyID());
        $CI->db->where('customerDescription', 'Promotion');

        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('defaultCustomerType')) {
    function defaultCustomerType()
    {
        $CI = &get_instance();
        $CI->db->select("customerType.customerTypeID");
        $CI->db->from('srp_erp_customertypemaster customerType');
        $CI->db->where('company_id', current_companyID());
        $CI->db->where('isDefault', 1);
        $result = $CI->db->get()->row_array();
        if (!empty($result)) {
            return $result['customerTypeID'];
        } else {
            return null;
        }
    }
}


if (!function_exists('get_paymentMethods_drop')) {
    function get_paymentMethods_drop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_paymentmethods');
        $CI->db->where('isActive', 1);
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['paymentMethodsID'] ?? '')] = $row['paymentDescription'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_paymentMethods')) {
    function get_paymentMethods()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_paymentmethods');
        $CI->db->where('isActive', 1);
        $result = $CI->db->get()->result_array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['paymentMethodsID'] ?? '')] = $row['paymentDescription'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_cashiers')) {
    function get_cashiers()
    {

        $CI = &get_instance();
        /*$q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " GROUP BY salesMaster.createdUserID ";*/
        $q = "SELECT
                Ename2 AS empName,
                invoice.createdUserID as createdUserID 
            FROM
                srp_erp_pos_invoice invoice
                JOIN srp_employeesdetails employees ON employees.EIdNo = invoice.createdUserID 
            WHERE
                invoice.companyID = '" . current_companyID() . "' 
            GROUP BY
                invoice.createdUserID ";
        $result = $CI->db->query($q)->result_array();
        //echo $q;
        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['createdUserID'] ?? '')] = $row['empName'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_waiters')) {
    function get_waiters()
    {

        $warehouse = get_outletID();
        $CI = &get_instance();
        /*$q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " GROUP BY salesMaster.createdUserID ";*/
        $q = "SELECT
	Ename1 AS empName,
	srp_erp_pos_crewmembers.crewMemberID,
	srp_employeesdetails.EIdNo
FROM
	srp_erp_pos_menusalesmaster salesMaster	
	Join srp_erp_pos_crewmembers on srp_erp_pos_crewmembers.crewMemberID = salesMaster.waiterID
 	join srp_erp_pos_crewroles on srp_erp_pos_crewroles.crewRoleID = srp_erp_pos_crewmembers.crewRoleID
	join srp_employeesdetails on srp_employeesdetails.EIdNo=srp_erp_pos_crewmembers.EIdNo
WHERE
	salesMaster.companyID = " . current_companyID() . "
	and srp_erp_pos_crewroles.isWaiter=1
	AND salesMaster.warehouseAutoID IN ($warehouse) 
GROUP BY
	salesMaster.waiterID";
        $result = $CI->db->query($q)->result_array();
        //echo $q;
        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['crewMemberID'] ?? '')] = $row['empName'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_s3_url')) {
    function get_s3_url($logoname)
    {
        $CI = &get_instance();
        $CI->load->library('s3');
        if ($logoname != '') {
            $path = 'images/logo/' . $logoname;
            $assetmasterattachment = $CI->s3->createPresignedRequest($path, '+1 hour');
        } else {
            $assetmasterattachment = $CI->s3->createPresignedRequest('images/item/no-image.png', '+1 hour');
        }
        return $assetmasterattachment;
    }
}

if (!function_exists('get_cashiers_gpos')) {
    function get_cashiers_gpos()
    {

        $CI = &get_instance();
        $q = "SELECT
                Ename2 AS empName,
                invoice.createdUserID as createdUserID 
            FROM
                srp_erp_pos_invoice invoice
                JOIN srp_employeesdetails employees ON employees.EIdNo = invoice.createdUserID 
            WHERE
                invoice.companyID = '" . current_companyID() . "' 
            GROUP BY
                invoice.createdUserID";
        $result = $CI->db->query($q)->result_array();
        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['createdUserID'] ?? '')] = $row['empName'];
            }
        }
        return $output_arr;
    }
}


if (!function_exists('posPaymentConfig_data')) {
    function posPaymentConfig_data($onlyCardTypes = null)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $filter = '';
        if ($onlyCardTypes == 'Y') {
            $filter = "AND ( selectBoxName='masterCard' OR  selectBoxName='visaCard' )";
        }
        $result = $CI->db->query("SELECT * FROM srp_erp_pos_paymentglconfigmaster AS configMaster
                                  LEFT JOIN srp_erp_pos_paymentglconfigdetail AS det ON det.paymentConfigMasterID = configMaster.autoID AND companyID={$companyID}
                                  WHERE isActive=1 {$filter} ORDER BY autoID")->result_array();

        return $result;
    }
}


if (!function_exists('get_glInfo_for_MenuSalesMaster_update')) {
    function get_glInfo_for_MenuSalesMaster_update($paymentTypeCode = 1)
    {
        /**
         * code level mapping between srp_erp_pos_paymentmethods and srp_erp_pos_paymentglconfigmaster
         * $paymentTypeCode = from srp_erp_pos_paymentmethods.paymentMethodsID => input from restaurant pos system
         * $paymentType = srp_erp_pos_paymentglconfigmaster.autoID
         **/


        switch ($paymentTypeCode) {

            case 3:
                /**Master Card**/
                $paymentType = 3;
                break;

            case 2:
                /**Visa Card**/
                $paymentType = 4;
                break;

            case 4:
                /**Sales Commission**/
                $paymentType = 6;
                break;

            default:
                /** Cash or Un Deposited */
                $paymentType = 1;
        }

        $CI = &get_instance();
        $q = "SELECT
                    GLAutoID,
                    bankCurrencyID,
                    bankCurrencyCode,
                    bankCurrencyDecimalPlaces
                FROM
                    srp_erp_chartofaccounts
                WHERE
                    srp_erp_chartofaccounts.GLAutoID = (
                        SELECT
                            GLCode
                        FROM
                            srp_erp_pos_paymentglconfigdetail
                        WHERE
                            paymentconfigMasterID = '" . $paymentType . "' and warehouseID= '" . get_outletID() . "'
                        AND companyID = " . current_companyID() . " )";
        $result = $CI->db->query($q)->row_array();


        return $result;
    }
}

if (!function_exists('number_pad')) {
    function number_pad($input, $pad_length)
    {
        return str_pad((int)$input, $pad_length, "0", STR_PAD_LEFT);
    }
}


if (!function_exists('kitchen_status')) {
    function kitchen_status($isHold, $isOrderPending, $isOrderInProgress, $isOrderCompleted, $from = null)
    {
        $output = '';
        switch ($from) {
            case 'KOT':
                if ($isOrderPending) {
                    $output = ' <div class="text-center"><i class="fa fa-2x fa-stop text-green"></i></div>';
                } else {
                    $output = ' <div class="text-center"><i class="fa fa-2x fa-stop text-red"></i></div>';
                }
                break;

            case 'PEN':
                if ($isOrderPending) {
                    $output = ' <div class="text-center"><i class="fa fa-2x fa-check text-yellow"></i></div>';
                } else {
                    //$output = ' <div class="text-center"><i class="fa fa-2x fa-check text-red"></i></div>';
                }
                break;

            case 'PRO':
                if ($isOrderInProgress) {
                    $output = ' <div class="text-center"><i class="fa fa-2x fa-check text-blue"></i></div>';
                } else {
                    //$output = ' <div class="text-center"><i class="fa fa-2x fa-check text-red"></i></div>';
                }
                break;

            case 'COM':
                if ($isOrderCompleted) {
                    $output = ' <div class="text-center"><i class="fa fa-2x fa-check text-green"></i></div>';
                } else {
                    //$output = ' <div class="text-center"><i class="fa fa-2x fa-check text-red"></i></div>';
                }
                break;


            default:

                if ($isOrderCompleted == 1) {

                    $output = '<div class="text-center"><span class="label label-success">Ready </span></div>';
                } else if ($isOrderInProgress == 1) {

                    $output = '<div class="text-center"><span class="label label-info">Processing </span></div>';
                } else if ($isOrderPending == 1) {

                    $output = '<div class="text-center"><span class="label label-warning">Pending</span></div>';
                }
        }


        //return $isOrderPending.'-'.$isOrderInProgress.'-'.$isOrderCompleted.'-'.$output;
        return $output;
    }
}


if (!function_exists('get_menuSizes_drop')) {
    function get_menuSizes_drop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_menusize');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isActive', 1);
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'what size?');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['menuSizeID'] ?? '')] = $row['description'] . ' (' . $row['code'] . ') ';
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_taxType_drop')) {
    function get_taxType_drop()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_taxmaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('taxType', 1);
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['taxMasterAutoID'] ?? '')] = $row['taxDescription'] . ' (' . $row['taxShortCode'] . ') -  ' . $row['taxPercentage'] . '%';
            }
        }
        return $output_arr;
    }
}


if (!function_exists('get_taxType')) {
    function get_taxType()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_taxmaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('taxType', 1);
        $CI->db->or_where('taxCategory', 2);
        $result = $CI->db->get()->result_array();
        //$output_arr[trim($row['taxMasterAutoID'] ?? '')] = $row['taxDescription'] . ' (' . $row['taxShortCode'] . ') -  ' . $row['taxPercentage'] . '%';
        return $result;
    }
}


if (!function_exists('get_menuSize')) {
    function get_menuSize()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_menusize');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isActive', 1);
        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('load_color_menu_size')) {
    function load_color_menu_size($colourCode)
    {

        if (!empty($colourCode)) {
            $output = '<i class="fa fa-square fa-2x" style="color:' . $colourCode . '"></i>';
        }

        return $output;
    }
}


if (!function_exists('load_edit_menu_size')) {
    function load_edit_menu_size($id)
    {
        $output = '<div style="text-align: center;">';
        $output .= '<button class="btn btn-default btn-xs" onclick="openEddMenuSizeModal(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button> | <button class="btn btn-danger btn-xs" onclick="delete_menuSize(' . $id . ')" rel="tooltip" ><i class="fa fa-trash-o"></i></button>';
        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('get_pack_group_itemss')) {
    function get_pack_group_itemss()
    {
        $CI = &get_instance();
        $CI->db->select("customerType.customerTypeID");
        $CI->db->from('srp_erp_customertypemaster customerType');
        $CI->db->where('company_id', current_companyID());
        $CI->db->where('isDefault', 1);
        $result = $CI->db->get()->row_array();
        if (!empty($result)) {
            return $result['customerTypeID'];
        } else {
            return null;
        }
    }
}

if (!function_exists('getresrestaurantTables_drop')) {
    function getresrestaurantTables_drop($warehouseID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_diningtables');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('segmentID', $warehouseID);
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Select Table');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['diningTableAutoID'] ?? '')] = $row['diningTableDescription'];
            }
        }
        return $output_arr;
    }
}


if (!function_exists('isAddOnbtn')) {
    function isAddOnbtn($id, $categoryID = 0, $value = 0)
    {
        $checked = $value == 1 ? 'checked' : '';

        $output = '<div style="text-align: center;">';

        //$output .= $id . ' - ' . $categoryID . ' - ' . $value . '<br/>';
        $output .= '<input class="mySwitch_addon" type="checkbox" id="isAddOn_' . $id . $categoryID . '"  name="isAddOn"
                                   onchange="updateIsAddOnValue(' . $id . ',\'m\',' . $categoryID . ')"
                                   data-size="mini" data-on-text="<i class=\'fa fa-check text-green\'></i>  Yes"
                                   data-handle-width="50" data-off-color="default" data-on-color="default"
                                   data-off-text="<i class=\'fa fa-times text-red\'></i>  No" data-label-width="0" ' . $checked . ' >';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('getCurrencyNotes')) {
    function getCurrencyNotes($currencyID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_currencydenomination');
        $CI->db->where('currencyID', $currencyID);
        $CI->db->where('isNote', 1);
        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('getposbankGL')) {
    function getposbankGL($paymentType)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $q = "select getposbankGL(" . $paymentType . "," . $companyID . ") as GLID";
        $result = $CI->db->query($q)->result_array();
        return $result;
    }
}


/*if (!function_exists('get_specialCustomers_drop')) {
    function get_specialCustomers_drop()
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_customers');
        //$CI->db->where('companyID', current_companyID());
        $CI->db->where('customerTypeMasterID', 1);
        $CI->db->where('isActive', 1);
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['customerID'] ?? '')] = $row['customerName'];
            }
        }
        return $output_arr;
    }
}*/

if (!function_exists('get_specialCustomers_drop')) {
    function get_specialCustomers_drop($ids = array('1'))
    {
        $result = get_specialCustomers($ids);
        $data_arr = array();

        if (!empty($result)) {
            foreach ($result as $item) {
                $data_arr[$item['customerID']] = $item['customerName'];
            }
        }
        return $data_arr;
    }
}

if (!function_exists('get_specialCustomers')) {
    function get_specialCustomers($id = array('1'), $posType = '1')
    {
        $tmpW = array();
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_customers');
        if (!empty($id)) {
            $where = '';
            foreach ($id as $val) {
                $tmpW[] = ' customerTypeMasterID  = ' . $val;
            }
            $tmpCustomerType = ' (' . join(' OR ', $tmpW) . ' ) ';
            $CI->db->where($tmpCustomerType);
        }

        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('posType', $posType);
        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('isOutletTaxEnabled')) {
    function isOutletTaxEnabled($get_outletID, $current_companyID)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 20);
        $CI->db->where('outletID', $get_outletID);
        $CI->db->where('companyID', $current_companyID);
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }
}

if (!function_exists('isOnscreenKeyboardHidden')) {
    function isOnscreenKeyboardHidden($get_outletID, $current_companyID)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 25);
        $CI->db->where('outletID', $get_outletID);
        $CI->db->where('companyID', $current_companyID);
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }
}

if (!function_exists('outletTaxActionButtons')) {
    function outletTaxActionButtons($id)
    {
        $output = '<button class="btn btn-danger btn-xs" onclick="delete_outlet_tax(' . $id . ')" rel="tooltip" title="" data-original-title="Delete"><i class="fa fa-trash"></i></button>';
        return $output;
    }
}

if (!function_exists('col_sortOrderMenu')) {
    function col_sortOrderMenu($id, $sortOrder, $source = 'm')
    {
        $output = '<span class="hide">' . $sortOrder . '</span>';
        $output .= '<div style="text-align: center; " >';
        $output .= '<input type="number" data-source="' . $source . '" onchange="updateSortOrder(this)" data-id="' . $id . '" value="' . $sortOrder . '" style="width:40px;" />';
        $output .= '</div>';
        return $output;
    }
}


if (!function_exists('btn_voidBill')) {
    function btn_voidBill($id, $desc = 'View', $outletID = 0)
    {
        $output = '<div style="text-align: center;">';
        if (paymentUpdatePolicy()) {
            $output .= '<button class="btn btn-default btn-xs" onclick="loadPrintTemplateVoid(\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="' . $desc . '" ><i class="fa fa-eye" aria-hidden="true"></i> ' . $desc . '</button> <button class="btn btn-default btn-xs" onclick="checkPosAuthentication(\'17\',\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="" ><i class="fa fa-edit" aria-hidden="true"></i> Update</button>';
        } else {
            $output .= '<button class="btn btn-default btn-xs" onclick="loadPrintTemplateVoid(\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="' . $desc . '" ><i class="fa fa-eye" aria-hidden="true"></i> ' . $desc . '</button>';
        }
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('btn_voidBillHistory')) {
    function btn_voidBillHistory($id, $outletID = 0)
    {
        $output = '<div style="text-align: center;">';

        //$output .= '<button class="btn btn-default btn-xs" onclick="unVoidBill(\'' . $id . '\')" rel="tooltip" title="' . $desc . '" ><i class="fa fa-undo" aria-hidden="true"></i> ' . $desc . '</button>';

        $output .= '&nbsp;&nbsp;<button class="btn btn-default btn-xs" onclick="loadPrintTemplateVoidHistory(\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="View"><i class="fa fa-eye" aria-hidden="true"></i> View</button>';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('all_delivery_person_drop')) {
    function all_delivery_person_drop($status = true)/*Load all Customer*/
    {
        $CI = &get_instance();
        $CI->db->select("customerID,customerName");
        $CI->db->from('srp_erp_pos_customers');
        $CI->db->where('companyID', current_companyID());
        // $CI->db->where('isActive', 1);
        $customer_arr = array();
        $customer = $CI->db->get()->result_array();
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['customerID'] ?? '')] = (trim($row['customerID'] ?? '') ? trim($row['customerName'] ?? '') : '');
            }
        }
        return $customer_arr;
    }
}

if (!function_exists('get_discount_type')) {
    function get_discount_type()
    {
        $CI = &get_instance();
        $CI->db->select("customerID,customerName");
        $CI->db->from('srp_erp_pos_customers');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isActive', 1);
        $customer_arr = array();
        $customer = $CI->db->get()->result_array();
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['customerID'] ?? '')] = (trim($row['customerID'] ?? '') ? trim($row['customerName'] ?? '') : '');
            }
        }
        return $customer_arr;
    }
}

if (!function_exists('load_edit_customer_type')) {
    function load_edit_customer_type($id, $applyToAllItem = 0)
    {
        $output = '<div style="text-align: center;">';

        //$output .= '<button class="btn btn-default btn-xs" onclick="employee_profile_edit(\'' . $id . '\')" rel="tooltip" title="Edit Profile" ><i class="fa fa-edit"></i></button>';
        $output .= '<button class="btn btn-default btn-xs" onclick="edit_customerType(\'' . $id . '\')" rel="tooltip" title="Edit" ><i class="fa fa-pencil-square-o"></i></button>';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('load_edit_promotion_type')) {
    function load_edit_promotion_type($id, $applyToAllItem, $isActive)
    {
        $output = '<div style="text-align: center;">';

        if ($isActive) {
            $output .= '<button class="btn btn-default btn-xs" onclick="link_warehouse(\'' . $id . '\')" rel="tooltip" title="Add Warehouse" ><i class="fa fa-home"></i></button>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($applyToAllItem == 0) {
            $output .= '<button class="btn btn-default btn-xs" onclick="link_item(\'' . $id . '\')" rel="tooltip" title="Add Items" ><i class="fa fa-link"></i></button>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        //$output .= '<button class="btn btn-default btn-xs" onclick="employee_profile_edit(\'' . $id . '\')" rel="tooltip" title="Edit Profile" ><i class="fa fa-edit"></i></button>';
        $output .= '<button class="btn btn-default btn-xs" onclick="edit_customerType(\'' . $id . '\')" rel="tooltip" title="Edit" ><i class="fa fa-pencil-square-o"></i></button>';

        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('addWarehouseAction')) {
    function addWarehouseAction($wareHouseAutoID)
    {
        $output = '<div style="text-align: center;">';

        $output .= '<button class="btn btn-xs btn-success" onclick="addWarehouseToPromotion(' . $wareHouseAutoID . ')">Add</button>';

        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('commissionPercentageCol')) {
    function commissionPercentageCol($commissionPercentage)
    {
        $output = '<div style="text-align: center;">';

        if ($commissionPercentage == 0) {
            $output .= '-';
        } else {
            $output .= $commissionPercentage;
        }


        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('applyToAllStatus')) {
    function applyToAllStatus($applyToAllItem)
    {
        $output = '<div style="text-align: center;">';

        if ($applyToAllItem) {
            $output .= 'Yes';
        } else {
            $output .= 'No';
        }


        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('addWarehouseListAction')) {
    function addWarehouseListAction($wareHouseAutoID, $companyID, $promotionID, $isActive)
    {
        $output = '<div style="text-align: center;">';

        if ($isActive == 1) {
            $output .= '<input type="checkbox" id="warehouseStatus_' . $wareHouseAutoID . '" name="warehouseStatus" onchange="warehouseStatusSwitch(' . $wareHouseAutoID . ',' . $companyID . ',' . $promotionID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else {
            $output .= '<input type="checkbox" id="warehouseStatus_' . $wareHouseAutoID . '" name="warehouseStatus" onchange="warehouseStatusSwitch(' . $wareHouseAutoID . ',' . $companyID . ',' . $promotionID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }

        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('load_active_customer_type')) {
    function load_active_customer_type($customerID, $active)
    {
        $output = '<div style="text-align: center;">';
        if ($active == 1) {
            $output .= '<input type="checkbox" id="menueCustomerTypeIsactive_' . $customerID . '" name="menueCustomerTypeIsactive" onchange="changecustomertypeIsactive(' . $customerID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else {
            $output .= '<input type="checkbox" id="menueCustomerTypeIsactive_' . $customerID . '" name="menueCustomerTypeIsactive" onchange="changecustomertypeIsactive(' . $customerID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }


        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('all_customer_type')) {
    function all_customer_type()
    {
        $CI = &get_instance();
        $CI->db->select('customerTypeID,description');
        $CI->db->from('srp_erp_pos_customertypemaster');
        //$CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Please select');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['customerTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('delivery_customer_type')) {
    function delivery_customer_type()
    {
        $CI = &get_instance();
        $CI->db->select('customerTypeID,description');
        $CI->db->from('srp_erp_pos_customertypemaster');
        //$CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where_in('customerTypeID', array('1', '5'));
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Please select');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['customerTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('delivery_promotion_type')) {
    function delivery_promotion_type()
    {
        $CI = &get_instance();
        $CI->db->select('customerTypeID,description');
        $CI->db->from('srp_erp_pos_customertypemaster');
        //$CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where_in('customerTypeID', array('2', '3'));
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Please select');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['customerTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}


/*** setup default order type ***/
if (!function_exists('get_defaultOderType')) {
    function get_defaultOderType()
    {
        $CI = &get_instance();
        $CI->db->select('customerTypeID');
        $CI->db->from('srp_erp_customertypemaster');
        $CI->db->WHERE('isDefault', 1);
        $CI->db->WHERE('company_id', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->row_array();
        if (!empty($data)) {
            return $data['customerTypeID'];
        } else {
            return null;
        }
    }
}


if (!function_exists('load_edit_yield_master')) {
    function load_edit_yield_master($id)
    {
        $output = '<div style="text-align: center;">';

        //$output .= '<button class="btn btn-default btn-xs" onclick="employee_profile_edit(\'' . $id . '\')" rel="tooltip" title="Edit Profile" ><i class="fa fa-edit"></i></button>';
        $output .= '<button class="btn btn-default btn-xs" onclick="edit_yieldMaster(\'' . $id . '\')" rel="tooltip" title="Edit" ><i class="fa fa-pencil-square-o text-warning"></i></button> &nbsp;&nbsp; <button class="btn btn-default btn-xs" onclick=\'fetchPage("system/pos/settings/yield_details",' . $id . ',"Add Yield Details","Yield"); \' rel="tooltip" title="Add Detail" ><i class="fa fa-cogs text-purple"></i></button>';

        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('get_total_fromYield')) {
    function get_total_fromYield($id)
    {
        current_companyID();
        $d = get_company_currency_decimal();
        $q = 'SELECT SUM(cost) as totalCost FROM srp_erp_pos_menuyieldsdetails WHERE yieldID = ' . $id . ' AND companyID =' . current_companyID();
        $CI = &get_instance();
        $result = $CI->db->query($q)->row_array();
        $output = number_format($result['totalCost'], $d);

        return '<div style="text-align: right;">  ' . $output . '</div>';
    }
}


if (!function_exists('load_edit_yield_detail')) {
    function load_edit_yield_detail($id)
    {
        $output = '<div style="text-align: center;">';

        $output .= '<button class="btn btn-default btn-xs" onclick="edit_yieldDetail(\'' . $id . '\')" rel="tooltip" title="Edit" ><i class="fa fa-pencil-square-o"></i></button>';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('load_type_yield_detail')) {
    function load_type_yield_detail($id)
    {
        $output = '<div style="text-align: center;">';
        if ($id == 1) {
            $output .= '<span>Raw Material</span>';
        } else {
            $output .= '<span>Yield</span>';
        }
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('load_item_yield_detail')) {
    function load_item_yield_detail($typeAutoId, $itemAutoID)
    {
        //$output = '<div style="text-align: center;">';
        if ($typeAutoId == 1) {
            $CI = &get_instance();
            $CI->db->select('itemName');
            $CI->db->from('srp_erp_itemmaster');
            $CI->db->WHERE('itemAutoID', $itemAutoID);
            $output = $CI->db->get()->row_array();
        } else {
            $CI = &get_instance();
            $CI->db->select('Description as itemName');
            $CI->db->from('srp_erp_pos_menuyields');
            $CI->db->WHERE('yieldID', $itemAutoID);
            $output = $CI->db->get()->row_array();
        }
        //$output .= '</div>';

        return $output['itemName'];
    }
}

if (!function_exists('get_itemmaster_details')) {
    function get_itemmaster_details($itemSystemCode)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_itemmaster');
        $CI->db->WHERE('itemSystemCode', $itemSystemCode);
        $output = $CI->db->get()->row_array();

        return $output;
    }
}

if (!function_exists('get_warehouseitem_details')) {
    function get_warehouseitem_details($itemAutoID,$wareHouseID)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_warehouseitems');
        $CI->db->WHERE('itemAutoID', $itemAutoID);
        $CI->db->WHERE('wareHouseAutoID', $wareHouseID);
        $output = $CI->db->get()->row_array();

        return $output;
    }
}




if (!function_exists('convertCostAmount')) {
    function convertCostAmount($amount, $dPlace = 2)
    {
        $dPlace = get_company_currency_decimal();
        return '<div align="right">' . format_number($amount, $dPlace) . '</div>';
    }
}


if (!function_exists('loadPOS_BankLedgerInfo')) {
    /**
     * @param $paymentMethod
     *      1    Un Deposited Fund
     *      2    Credit Note
     *      3    Master Card
     *      4    Visa Card
     * @return mixed
     */
    function loadPOS_BankLedgerInfo($paymentMethod)
    {

        switch (strtolower($paymentMethod)) {

            case 'cash':
                $code = 1;
                break;

            case strtolower('creditNote'): //just i put this :P
                $code = 2;
                break;

            case 'master':
                $code = 3;
                break;

            case 'visa':
                $code = 4;
                break;

            default:
                echo 'payment mode not configured in line No.' . __LINE__ . ' in ' . basename(__FILE__);
                exit;
        }

        $CI = &get_instance();
        $q = "SELECT
                    det.ID,
                    configMaster.autoID,
                    configMaster.description,
                    det.GLCode,
                    chartOfAccount.GLAutoID,
                    chartOfAccount.bankName,
                    chartOfAccount.bankCurrencyDecimalPlaces,
                    chartOfAccount.GLSecondaryCode,
                    chartOfAccount.systemAccountCode,
                    chartOfAccount.bankCurrencyID,
                    chartOfAccount.bankCurrencyDecimalPlaces
                FROM
                    srp_erp_pos_paymentglconfigmaster AS configMaster
                LEFT JOIN srp_erp_pos_paymentglconfigdetail AS det ON det.paymentConfigMasterID = configMaster.autoID
                LEFT JOIN srp_erp_chartofaccounts AS chartOfAccount ON chartOfAccount.GLAutoID = det.GLCode
                WHERE
                    det.companyID = '" . current_companyID() . "'
                AND configMaster.isActive = 1
                AND configMaster.autoID = '" . $code . "'
                ORDER BY
                    autoID";
        $result = $CI->db->query($q)->row_array();


        return $result;
    }
}

if (!function_exists('get_active_outletInfo')) {
    function get_active_outletInfo()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isPosLocation', 1);
        $CI->db->where('isActive', 1);
        $result = $CI->db->get()->result_array();
        return $result;
    }
}


if (!function_exists('get_count_unused_wifi_password')) {
    function get_count_unused_wifi_password($outletID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_wifipasswordsetup');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isUsed', 0);
        $CI->db->where('outletID', $outletID);
        $count = $CI->db->get()->num_rows();
        return $count;
    }
}


if (!function_exists('get_menuPack_groups')) {
    function get_menuPack_groups()
    {
        $CI = &get_instance();
        $r = $CI->db->query("SELECT
                        menuMaster.menuMasterID,
                        menuMaster.menuMasterDescription,
                        menuMaster.menuCategoryID,
                        packGroupMaster.description,
                        packGroupMaster.groupMasterID
                    FROM
                        srp_erp_pos_menumaster AS menuMaster
                    LEFT JOIN srp_erp_pos_menupackgroupmaster AS packGroupMaster ON packGroupMaster.packMenuID = menuMaster.menuMasterID
                    WHERE
                        menuMaster.isPack = 1
                    AND menuMaster.companyID = '" . current_companyID() . "'
                    AND packGroupMaster.IsRequired = 0")->result_array();
        return $r;
    }
}


if (!function_exists('employees_pos_outlet_drop')) {
    function employees_pos_outlet_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->SELECT("EIdNo,Ename2");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->where('isDischarged !=', 1);
        $CI->db->where('Erp_companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Employee');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('get_industryType_drop')) {
    function get_industryType_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select("industrytypeID,industryTypeDescription");
        $CI->db->from('srp_erp_mfq_industrytypes');
        $CI->db->order_by('industryTypeDescription');
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select industry type');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['industrytypeID'] ?? '')] = trim($row['industryTypeDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('uom_drop')) {
    function uom_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select("UnitID, UnitDes, UnitShortCode");
        $CI->db->from('srp_erp_unit_of_measure');
        $CI->db->where('companyID', current_companyID());
        $CI->db->order_by('UnitDes');
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select UoM');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['UnitID'] ?? '')] = trim($row['UnitShortCode'] ?? '') . ' - ' . trim($row['UnitDes'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('check_category_subItemExist')) {
    function check_category_subItemExist($menuCategoryID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_menumaster');
        $CI->db->where('menuCategoryID', $menuCategoryID);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            return true;
        } else {
            return false;
        }
    }
}


if (!function_exists('get_subCategory')) {
    function get_subCategory($masterLevelID, $warehouseID)
    {
        $CI = &get_instance();

        $path = base_url();
        $q = "SELECT
                  whmc2.autoID,
                    srp_erp_pos_menucategory.menuCategoryID,
                    srp_erp_pos_menucategory.menuCategoryDescription AS description,
                    concat('" . $path . "',srp_erp_pos_menucategory.image) as image,
                    srp_erp_pos_menucategory.bgColor,
                    srp_erp_pos_menucategory.masterLevelID,
                    srp_erp_pos_menucategory.levelNo,
                    srp_erp_pos_menucategory.showImageYN as showImageYN
                FROM
                    srp_erp_pos_menucategory
                    INNER JOIN srp_erp_pos_warehousemenucategory whmc2 ON whmc2.menuCategoryID = srp_erp_pos_menucategory.menuCategoryID
                WHERE
                      srp_erp_pos_menucategory.companyID = '" . current_companyID() . "' 
                      AND srp_erp_pos_menucategory.masterLevelID = '" . $masterLevelID . "'                     
                      AND whmc2.warehouseID = '" . $warehouseID . "' 
                      AND whmc2.isDeleted = 0 AND whmc2.isActive = 1 
                      AND srp_erp_pos_menucategory.isDeleted = 0 
                      AND srp_erp_pos_menucategory.isActive = 1
                      ORDER BY srp_erp_pos_menucategory.sortOrder, srp_erp_pos_menucategory.menuCategoryDescription ";
        $result = $CI->db->query($q)->result_array();
        /*echo $q;
        exit;*/
        return $result;
    }
}

if (!function_exists('col_posConfig_kot')) {
    function col_posConfig_kot($outletID)
    {
        $output = '';

        //$output .= '<button class="btn btn-default btn-xs" onclick="edit_pos_crew_config(\'' . $outletID . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button> | ';
        $output .= '<div style="text-align: center;"><button class="btn btn-danger btn-xs" onclick="delete_pos_kotLocation(\'' . $outletID . '\')" rel="tooltip" ><i class="fa fa-trash-o"></i></button></div>';

        return $output;
    }
}

if (!function_exists('posConfig_outlet_tax')) {
    function posConfig_outlet_tax($outletID)
    {
        $output = '';

        //$output .= '<button class="btn btn-default btn-xs" onclick="edit_pos_crew_config(\'' . $outletID . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button> | ';
        $output .= '<div style="text-align: center;"><button class="btn btn-default btn-xs posConfigBtn text-blue" onclick="outlet_tax_modal(\'' . $outletID . '\')" rel="tooltip" ><i class="fa fa-edit"></i></button></div>';

        return $output;
    }
}

if (!function_exists('get_outletID')) {
    function get_outletID($segmentConfigID = false)
    {
        if ($segmentConfigID) {
            $CI = &get_instance();
            $CI->db->select('wareHouseAutoID');
            $CI->db->from('srp_erp_pos_segmentconfig');
            $CI->db->where('segmentConfigID', $segmentConfigID);
            $outletID = $CI->db->get()->row('wareHouseAutoID');
            return $outletID;
        } else {
            $CI = &get_instance();
            $q = "SELECT
                `w_master`.`wareHouseAutoID` AS `outletID`
            FROM ( `srp_erp_warehouse_users` `users` )
            LEFT JOIN `srp_erp_warehousemaster` `w_master` ON `w_master`.`wareHouseAutoID` = `users`.`wareHouseID`
            WHERE
                `users`.`userID` = '" . current_userID() . "'
            AND `users`.`companyID` = '" . current_companyID() . "'
            AND `w_master`.`isPosLocation` = 1
            AND users.isActive = 1";

            $result = $CI->db->query($q)->row_array();

            return $result['outletID'];
        }
    }
}


/** Data Table : warehouse menu */
if (!function_exists('kot_dropDown')) {
    function kot_dropDown($key, $kotID, $outletID)
    {

        $CI = &get_instance();

        //$outletInfo = get_outletInfo();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_kitchenlocation');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('outletID', $outletID);
        $data_array = $CI->db->get()->result_array();

        $output = '<select name="kotID" class="form-control" id="KOTLocation" onchange="update_kotID(this,' . $key . ')"> ';
        $output .= '<option value=""> Select</option>';
        foreach ($data_array as $item) {
            if (!empty($kotID) && $kotID == $item['kitchenLocationID']) {
                $selected = ' selected ';
            } else {
                $selected = ' ';
            }
            $output .= '<option ' . $selected . ' value="' . $item['kitchenLocationID'] . '"> ' . $item['description'] . '</option>';
        }

        $output .= '</select>';
        return $output;
    }
}

if (!function_exists('kot_dropDown_category')) {
    function kot_dropDown_category($outletID)
    {

        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_kitchenlocation');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('outletID', $outletID);
        $data_array = $CI->db->get()->result_array();

        $output = '<select class="form-control" name="kotID_common" id="kotID_common"> ';
        $output .= '<option value=""> Select</option>';
        foreach ($data_array as $item) {
            if (!empty($kotID) && $kotID == $item['kitchenLocationID']) {
                $selected = ' selected ';
            } else {
                $selected = ' ';
            }
            $output .= '<option ' . $selected . ' value="' . $item['kitchenLocationID'] . '"> ' . $item['description'] . '</option>';
        }

        $output .= '</select>';
        return $output;
    }
}


if (!function_exists('get_kitchenLocation')) {
    function get_kitchenLocation()
    {
        $outletInfo = get_outletInfo();

        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_kitchenlocation');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('outletID', $outletInfo['wareHouseAutoID']);
        $result = $CI->db->get()->result_array();
        return $result;
    }
}
if (!function_exists('get_kitchenLocation_default')) {
    function get_kitchenLocation_default()
    {
        $outletInfo = get_outletInfo();

        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_kitchenlocation');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('outletID', $outletInfo['wareHouseAutoID']);
        $result = $CI->db->get()->row_array();
        return $result;
    }
}

if (!function_exists('get_payment_config_master_drop')) {
    function get_payment_config_master_drop()
    {
        $CI = &get_instance();
        $CI->db->select("autoID, description, glAccountType");
        $CI->db->from('srp_erp_pos_paymentglconfigmaster');
        $CI->db->where('isActive', 1);
        $result = $CI->db->get()->result_array();
        $output_arr = array('' => 'Please Select');
        foreach ($result as $row) {
            $output_arr[trim($row['autoID'] ?? '')] = $row['description'];
        }


        return $output_arr;
    }
}

if (!function_exists('posrPaymentConfig_data')) {
    function posrPaymentConfig_data($onlyCardTypes = null)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $filter = '';
        if ($onlyCardTypes == 'Y') {
            $filter = "AND ( selectBoxName='masterCard' OR  selectBoxName='visaCard' )";
        }
        $result = $CI->db->query("SELECT * FROM srp_erp_pos_paymentglconfigdetail AS det 
                                  inner JOIN srp_erp_pos_paymentglconfigmaster AS configMaster ON det.paymentConfigMasterID = configMaster.autoID 
                                  LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = det.warehouseID
                                  
                                  WHERE configMaster.isActive=1 {$filter}  AND det.companyID={$companyID} ORDER BY wareHouseAutoID DESC, autoID")->result_array();
        /*AND warehouseID = '" . $CI->common_data['ware_houseID'] . "' */
        //echo $CI->db->last_query();

        return $result;
    }
}

if (!function_exists('get_active_outlets')) {
    function get_active_outlets()
    {
        $CI = &get_instance();
        $q = "SELECT *
                    FROM `srp_erp_pos_segmentconfig` as `config`
                    LEFT JOIN `srp_erp_warehousemaster` as `wareHouse` ON `wareHouse`.`wareHouseAutoID` = `config`.`wareHouseAutoID`
                    LEFT JOIN `srp_erp_industrytypes` as `industry` ON `industry`.`industrytypeID` = `config`.`industrytypeID`
                    LEFT JOIN `srp_erp_pos_templatemaster` as `posTemplate` ON `posTemplate`.`posTemplateID` = `config`.`posTemplateID`
                    LEFT JOIN `srp_erp_segment` as `segment` ON `segment`.`segmentID` = `config`.`segmentID`
                    WHERE `config`.`isActive` = -1
                    AND `config`.`companyID` = '" . current_companyID() . "'";

        $result = $CI->db->query($q)->result_array();
        return $result;
    }
}


if (!function_exists('get_active_outlets_drop')) {
    function get_active_outlets_drop()
    {
        $CI = &get_instance();
        $q = "SELECT *
                    FROM `srp_erp_pos_segmentconfig` as `config`
                    LEFT JOIN `srp_erp_warehousemaster` as `wareHouse` ON `wareHouse`.`wareHouseAutoID` = `config`.`wareHouseAutoID`
                    LEFT JOIN `srp_erp_industrytypes` as `industry` ON `industry`.`industrytypeID` = `config`.`industrytypeID`
                    LEFT JOIN `srp_erp_pos_templatemaster` as `posTemplate` ON `posTemplate`.`posTemplateID` = `config`.`posTemplateID`
                    LEFT JOIN `srp_erp_segment` as `segment` ON `segment`.`segmentID` = `config`.`segmentID`
                    WHERE `config`.`isActive` = -1
                    AND `config`.`companyID` = '" . current_companyID() . "'";

        $result = $CI->db->query($q)->result_array();

        $output_arr = array('' => 'Please Select');
        foreach ($result as $row) {
            $output_arr[trim($row['wareHouseAutoID'] ?? '')] = $row['wareHouseCode'] . ' - ' . $row['wareHouseDescription'] . ' - ' . $row['wareHouseLocation'];
        }


        return $output_arr;
    }
}


if (!function_exists('getChartOfAccount_serviceCharge')) {
    function getChartOfAccount_serviceCharge()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('accountCategoryTypeID<>', 4);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bank = $CI->db->get()->result_array();
        $bank_arr = array('' => 'Select GL Account');
        if (isset($bank)) {
            foreach ($bank as $row) {
                $bank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $bank_arr;
    }
}


if (!function_exists('get_menu_yield')) {
    function get_menu_yield($id)
    {
        $CI = &get_instance();
        $CI->db->select("yieldID,yielduomID,yieldUOM,yieldAmount,yieldCost,qty,itemDescription as Description, itemSystemCode")
            ->from('srp_erp_pos_menuyields')
            ->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID=srp_erp_pos_menuyields.itemAutoID', 'left');
        $CI->db->where('yieldID', $id);
        return $CI->db->get()->row_array();
    }
}


if (!function_exists('col_menu_isTaxEnabled')) {
    function col_menu_isTaxEnabled($id, $isActive)
    {
        $output = '<div style="text-align: center;">';
        if ($isActive == 1) {
            $output .= '<input type="checkbox" id="isTaxEnabled_' . $id . '" name="isTaxEnabled" onchange="change_isTaxEnabled(' . $id . ')" data-size="mini" data-on-text="Enable" data-handle-width="45" data-off-color="danger" data-on-color="primary" data-off-text="Disable" data-label-width="0" checked>';
        } else if ($isActive == 0) {
            $output .= '<input type="checkbox" id="isTaxEnabled_' . $id . '" name="isTaxEnabled" onchange="change_isTaxEnabled(' . $id . ')" data-size="mini" data-on-text="Enable" data-handle-width="45" data-off-color="danger" data-on-color="primary" data-off-text="Disable" data-label-width="0">';
        }


        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('get_paymentMethods_GLConfig')) {
    function get_paymentMethods_GLConfig()
    {
        $outletID = get_outletID();
        $CI = &get_instance();
        $CI->db->select("detailTbl.ID, masterTbl.autoID, masterTbl.description, masterTbl.glAccountType, masterTbl.image,detailTbl.isAuthRequired");
        $CI->db->from('srp_erp_pos_paymentglconfigdetail detailTbl');
        $CI->db->join('srp_erp_pos_paymentglconfigmaster masterTbl', 'masterTbl.autoID = detailTbl.paymentConfigMasterID');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('warehouseID', $outletID);
        $CI->db->order_by('sortOrder', 'asc');
        $result = $CI->db->get()->result_array();
        //echo $CI->db->last_query();
        return $result;
    }
}

if (!function_exists('pos_config_isOnTimePaymentCol')) {
    function pos_config_isOnTimePaymentCol($valueTmp, $type)
    {
        if ($valueTmp == '') {
            if ($type != 1) {
                return '<span class="text-gray">N/A</span>';
            }
        } else if ($valueTmp == 1) {
            return '<span class="text-green">On Time Commission</span>';
        } else if ($valueTmp == 0) {
            return '<span class="text-red">Late payment</span>';
        }
    }
}

if (!function_exists('get_pos_templateInfo')) {
    function get_pos_templateInfo()
    {
        $CI = &get_instance();
        $outletID = get_outletID();
        $CI->db->select('outletTemplateMasterID');
        $CI->db->from('srp_erp_pos_outlettemplatedetail');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('outletID', $outletID);
        $templateMasterID = $CI->db->get()->row('outletTemplateMasterID');
        if ($templateMasterID) {
            $templateID = $templateMasterID;
            $CI->db->select("*");
            $CI->db->from("srp_erp_pos_outlettemplatemaster");
            $CI->db->where("shortCode", "POSR");
            $CI->db->where("isDefault", $templateID);
            $result = $CI->db->get()->row_array();
        } else {
            $CI->db->select("*");
            $CI->db->from("srp_erp_pos_outlettemplatemaster");
            $CI->db->where("shortCode", "POSR");
            $CI->db->where("isDefault", 1);
            $result = $CI->db->get()->row_array();
        }
        return $result;
    }
}


if (!function_exists('get_pos_templateID')) {
    function get_pos_templateID()
    {
        $CI = &get_instance();
        $outletID = get_outletID();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_outlettemplatedetail');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('outletID', $outletID);
        $tmpResult = $CI->db->get()->row_array();

        if (!empty($tmpResult)) {

            $result = $tmpResult['outletTemplateMasterID'];
        } else {
            $CI->db->select("outletTemplateMasterID");
            $CI->db->from("srp_erp_pos_outlettemplatemaster");
            $CI->db->where("shortCode", "POSR");
            $CI->db->where("isDefault", 1);
            $result = $CI->db->get()->row('outletTemplateMasterID');
        }
        return $result;
    }
}


if (!function_exists('get_segmentconfig_posTemplateID')) {
    function get_segmentconfig_posTemplateID()
    {
        $CI = &get_instance();
        $outletID = get_outletID();
        $comapny_id = current_companyID();
        $CI->db->select('posTemplateID');
        $CI->db->from('srp_erp_pos_segmentconfig');
        $CI->db->where('companyID', $comapny_id);
        $CI->db->where('wareHouseAutoID', $outletID);
        $result = $CI->db->get();
        if ($result->num_rows() > 0) {
            $row = $result->row();
            return $row->posTemplateID;
        } else {
            return -1;
        }
    }
}


if (!function_exists('is_dineIn_order')) {
    function is_dineIn_order($menuSalesID)
    {
        $CI = &get_instance();
        $CI->db->select('cm.customerDescription');
        $CI->db->from('srp_erp_pos_menusalesmaster msm');
        $CI->db->join('srp_erp_customertypemaster cm', 'msm.customerTypeID = cm.customerTypeID');
        $CI->db->where('menuSalesID', $menuSalesID);
        $customerDescription = $CI->db->get()->row('customerDescription');
        if (strtolower(trim($customerDescription)) == 'dine-in' || strtolower(trim($customerDescription)) == 'eat-in') {
            $dine_in = true;
        } else {
            $dine_in = false;
        }
        return $dine_in;
    }
}


if (!function_exists('getSellingPricePolicy')) {
    function getSellingPricePolicy($templateID, $priceWithoutTax, $tax, $serviceCharge, $qty = 1)
    {
        switch ($templateID) {
            case 1:
                $sellingPrice = ($priceWithoutTax + $tax + $serviceCharge) * $qty;
                break;
            case 2:
                $sellingPrice = $priceWithoutTax * $qty;
                break;
            case 3:
                $sellingPrice = ($priceWithoutTax + $serviceCharge) * $qty;
                break;
            case 4:
                $sellingPrice = ($priceWithoutTax + $tax) * $qty;
                break;
            default:
                $sellingPrice = ($priceWithoutTax + $tax + $serviceCharge) * $qty;
        }
        return $sellingPrice;
    }
}


if (!function_exists('get_pos_templateView')) {
    function get_pos_templateView()
    {
        $CI = &get_instance();
        $q = "SELECT
                    templateMaster.templateLink
                FROM
                    srp_erp_pos_segmentconfig segmentConfig
                JOIN srp_erp_pos_templatemaster templateMaster ON templateMaster.posTemplateID = segmentConfig.posTemplateID
                WHERE
                    segmentConfig.companyID = '" . current_companyID() . "'
                AND segmentConfig.wareHouseAutoID = '" . get_outletID() . "'
                AND segmentConfig.isActive = - 1";

        $templateLink = $CI->db->query($q)->row('templateLink');

        if (!empty($templateLink)) {
            $output = $templateLink;
        } else {
            $output = $CI->db->select('templateLink')->from('srp_erp_pos_templatemaster')->where('isDefault', 1)->get()->row('templateLink');
            if (empty($output)) {
                $output = 'system/pos/pos_restaurant-view1';
            }
        }
        return $output;
    }
}


if (!function_exists('get_kitchenNoteSamples')) {
    function get_kitchenNoteSamples()
    {
        $CI = &get_instance();
        $outletID = get_outletID();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_kitchennotesamples');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('warehouseAutoID', $outletID);
        $tmpResult = $CI->db->get()->result_array();
        return $tmpResult;
    }
}


if (!function_exists('loadGL_records_delivery')) {
    function loadGL_records_delivery($GLCode, $type, $paymentType, $from, $expenseGLDesc = '', $liabilityGLDesc = '')
    {
        $output = '<div style="text-align: center;">';
        if ($type == 1) {
            if (!empty($GLCode)) {
                $CI = &get_instance();
                $GL = $CI->db->select('GLDescription')->from('srp_erp_chartofaccounts')->where('GLAutoID', $GLCode)->get()->row('GLDescription');
                $output .= '<div style="text-align: left;">' . $GL . '</div>';
            } else {
                if ($from == 'E') {
                    //$output .= '<span class="text-red" title="Please setup"><i class="fa fa-exclamation-triangle"></i> Empty</span>';
                    $output .= '<span class="text-gray" >N/A</span>';
                } else {
                    if ($paymentType == 1) {
                        $output .= '<span class="text-gray" >N/A</span>';
                    } else {
                        //$output .= '<span class="text-red" title="Please setup" ><i class="fa fa-exclamation-triangle"></i> Empty</span>';
                        $output .= '<span class="text-gray" >N/A</span>';
                    }
                }
            }
        } else if ($type == 3) {
            if (!empty($GLCode)) {
                $CI = &get_instance();
                $GL = $CI->db->select('GLDescription')->from('srp_erp_chartofaccounts')->where('GLAutoID', $GLCode)->get()->row('GLDescription');
                $output .= '<div style="text-align: left;">' . $GL . '</div>';
            } else {
                if ($from == 'E') {
                    $output .= '<span class="text-red" title="Please setup" ><i class="fa fa-exclamation-triangle"></i> Empty</span>';
                } else {
                    $output .= '<span class="text-gray" >N/A</span>';
                }
            }
            //$output .= $type;
        } else {
            $output .= '<span class="text-gray" >N/A</span>';
        }
        $output .= '</div>';
        return $output;
    }
}


if (!function_exists('loadPromotionOrderCol')) {
    function loadPromotionOrderCol($description, $customerTypeMasterID)
    {
        $output = '<div style="text-align: center;">';
        if ($customerTypeMasterID == 1) {
            /*Delivery */

            //$output .= '<span class="text-red" title="Please setup" > '.$description.'</span>';
            $output .= "<span class='label label-primary'>$description</span>";
        } else if ($customerTypeMasterID == 3) {
            /*Wastage  */

            //$output .= '<span class="text-red" title="Please setup" > '.$description.'</span>';
            $output .= "<span class='label label-danger'>$description</span>";
        } else {
            /*Promo*/
            $output .= "<span class='label label-warning'>$description</span>";
        }

        $output .= '</div>';
        return $output;
    }
}

if (!function_exists('get_chartOfAccountDop_pos')) {
    function get_chartOfAccountDop_pos()
    {
        $CI = &get_instance();
        $CI->db->select("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('isActive', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('companyID', current_companyID());
        $CI->db->order_by('GLDescription', 'asc');
        $result = $CI->db->get()->result_array();
        $output_arr = array();
        $output_arr[] = 'Please select';
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['GLAutoID'] ?? '')] = $row['GLDescription'] . ' (' . $row['systemAccountCode'] . ' - ' . $row['subCategory'] . ' - ' . $row['systemAccountCode'] . ')';
            }
        }
        return $output_arr;
    }
}


if (!function_exists('get_pos_outletTemplateMaster')) {
    function get_pos_outletTemplateMaster()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_outlettemplatemaster');
        $CI->db->where('shortCode', 'POSR');
        $CI->db->order_by('sortOrder', 'asc');
        $result = $CI->db->get()->result_array();
        $output_arr = array();
        $output_arr[] = 'Please select';
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['outletTemplateMasterID'] ?? '')] = $row['description'];
            }
        }
        return $output_arr;
    }
}
if (!function_exists('get_bill_payment_types')) {
    function get_bill_payment_types($menuSalesMasterID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $q = "SELECT
                    glConfigMaster.description
                FROM
                    srp_erp_pos_menusalespayments payments
                LEFT JOIN srp_erp_pos_paymentglconfigmaster glConfigMaster ON glConfigMaster.autoID = payments.paymentConfigMasterID
                WHERE
                    payments.menuSalesID ='" . $menuSalesMasterID . "' AND  payments.wareHouseAutoID = '" . $outletID . "'";
        //echo $q.'<br/>';
        $CI = &get_instance();
        $result = $CI->db->query($q)->result_array();
        return $result;
    }
}


if (!function_exists('edit_pos_customer')) {
    function edit_pos_customer($id, $isFromERP)
    {
        $status = '<span class="pull-right">';
        if ($isFromERP) {
            $status .= '<span style="color:#079f1e; font-size:13px;"><span title="Linked to ERP" rel="tooltip" class="fa fa-link"></span></span>&nbsp;&nbsp;';
        } else {
            $status .= '<span style="color:#8B0000; font-size:13px;" ><span title="Not Linked" rel="tooltip" class="fa fa-external-link"></span></span>&nbsp;&nbsp;';
            $status .= ' &nbsp; | &nbsp; ';
            $status .= '<a onclick="fetchPage(\'system/pos/masters/manage-customer\',' . $id . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('countryDiv_pos')) {
    function countryDiv_pos($country)
    {
        $countryImg = base_url() . 'images/flags/' . trim($country) . '.png';
        $output = '<div><img src="' . $countryImg . '" /> ' . $country . '</div>';
        return $output;
    }
}


if (!function_exists('trim_value_pos')) {
    function trim_value_pos($comments = '', $trimVal = 150, $placement = 'bottom')
    {
        $String = $comments;
        $truncated = (strlen($String) > $trimVal) ? substr(
            $String,
            0,
            $trimVal
        ) . '<span class="tol" rel="tooltip" data-placement="' . $placement . '" style="color:#0088cc" title="' . str_replace(
            '"',
            '&quot;',
            $String
        ) . '">... </span>' : $String;

        return $truncated;
    }
}


if (!function_exists('get_giftCardCols')) {
    function get_giftCardCols($cardMasterID, $barcode, $outletID, $cardExpiryInMonths)
    {
        $output = '<div style="text-align: center;">';

        //$output .= '<button class="btn-link" onclick="editGiftCard(' . $cardMasterID . ',\'' . $barcode . '\',' . $outletID . ',' . $cardExpiryInMonths . ')" rel="tooltip" title="Edit Profile" ><i class="fa fa-edit"></i></button>&nbsp;&nbsp;';
        $output .= '<button class="btn-link" onclick="editGiftCard(' . $cardMasterID . ')" rel="tooltip" title="Edit Profile" ><i class="fa fa-edit"></i></button>&nbsp;&nbsp;';
        $output .= '<button class="btn-link text-red" onclick="delete_GiftCard(\'' . $cardMasterID . '\')" rel="tooltip" title="Delete" ><i class="fa fa-trash"></i></button>';

        $output .= '</div>';

        return $output;
    }
}


if (!function_exists('outlet_taxes_list')) {
    function outlet_taxes_list($menuSalesID)
    {
        $CI = &get_instance();
        $CI->db->select('srp_erp_pos_menusalesoutlettaxes.*,srp_erp_pos_outlettaxmaster.*');
        $CI->db->from('srp_erp_pos_menusalesoutlettaxes');
        $CI->db->join('srp_erp_pos_outlettaxmaster', 'srp_erp_pos_outlettaxmaster.outletTaxID=srp_erp_pos_menusalesoutlettaxes.outletTaxID');
        $CI->db->where('srp_erp_pos_menusalesoutlettaxes.menuSalesID', $menuSalesID);
        return $CI->db->get()->result_array();
        //        $CI->db->get()->result_array();
        //        echo $CI->db->last_query();exit;
    }
}

if (!function_exists('get_print_template')) {
    function get_print_template($templateType = 'POS')
    {
        $CI = &get_instance();
        $CI->db->select('srp_erp_pos_printtemplatemaster.*');
        $CI->db->from('srp_erp_pos_printtemplatedetail');
        $CI->db->join('srp_erp_pos_printtemplatemaster', 'srp_erp_pos_printtemplatedetail.printTemplateMasterID = srp_erp_pos_printtemplatemaster.printTemplateMasterID');
        $CI->db->where('srp_erp_pos_printtemplatedetail.companyID', current_companyID());
        $CI->db->where('srp_erp_pos_printtemplatemaster.templateType', $templateType);
        $result = $CI->db->get()->row_array();

        switch ($templateType) {
            case "VOID":
                // DO NOT NEED THIS inside this view have have used the template
                $link = "system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint";
                break;

            case "VOIDH":
                // DO NOT NEED THIS inside this view have have used the template
                $link = "system/pos/printTemplate/restaurant-pos-dotmatric-printer-reprint-void";
                break;

            default:
                $link = "system/pos/printTemplate/restaurant-pos-dotmatric-printer";
        }

        if (!empty($result)) {
            if (!empty($result['templateLink'])) {
                $link = $result['templateLink'];
            }
        }


        return $link;
    }
}

if (!function_exists('get_bill_print_template_id')) {
    function get_bill_print_template_id($templateType = 'POS')
    {
        $CI = &get_instance();
        $CI->db->select('srp_erp_pos_printtemplatemaster.*');
        $CI->db->from('srp_erp_pos_printtemplatedetail');
        $CI->db->join('srp_erp_pos_printtemplatemaster', 'srp_erp_pos_printtemplatedetail.printTemplateMasterID = srp_erp_pos_printtemplatemaster.printTemplateMasterID');
        $CI->db->where('srp_erp_pos_printtemplatedetail.companyID', current_companyID());
        $CI->db->where('srp_erp_pos_printtemplatemaster.templateType', $templateType);
        $result = $CI->db->get()->row_array();

        if (!empty($result)) {
            if (!empty($result['printTemplateMasterID'])) {
                $id = $result['printTemplateMasterID'];
            }
        } else {
            $id = '0';
        }

        return $id;
    }
}


if (!function_exists('get_bill_all_print_template')) {
    function get_bill_all_print_template($templateType = 'POS')
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_printtemplatemaster');
        $CI->db->where('templateType', $templateType);
        $result = $CI->db->get()->result_array();

        $output_arr = array();
        $output_arr[] = 'Please select';
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['printTemplateMasterID'] ?? '')] = $row['description'];
            }
        }
        return $output_arr;
    }
}


if (!function_exists('get_history_description')) {
    function get_history_description($amount, $isRefund = 0)
    {
        if ($amount > 0) {
            return '<span class="text-green">Top Up</span>';
        } else if ($amount < 0) {
            if ($isRefund == 0) {
                return '<span class="text-red">Redeem</span>';
            } else {
                return '<span class="text-orange">Refund</span>';
            }
        }
    }
}

if (!function_exists('get_giftCardDatetime')) {
    function get_giftCardDatetime($dateTime, $format = 'd')
    {
        if ($format == 'd') {
            return date('d/m/Y', strtotime($dateTime));
        } else if ($format == 't') {
            return date('h:i A', strtotime($dateTime));
        }
    }
}

if (!function_exists('get_numberFormat')) {
    function get_numberFormat($amount)
    {
        $colorCls = '';
        if ($amount < 0) {
            $colorCls = 'text-red';
        }
        return '<div style="text-align: right; font-weight: 800" class="' . $colorCls . '">' . number_format($amount, 2) . '</div>';
    }
}

/*pos authentication*/

if (!function_exists('usergroup_action')) {
    function usergroup_action($userGroupMasterID, $description, $isActive)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_auth_usergroupdetail');
        $CI->db->where('userGroupMasterID', $userGroupMasterID);
        $CI->db->where('companyID', current_companyID());
        $result = $CI->db->get()->row_array();

        $description = "'" . $description . "'";

        $edit = '<a title="Edit" rel="tooltip" onclick="editUserGroupDetail(' . $userGroupMasterID . ', ' . $description . ')">
                <span class="glyphicon glyphicon-pencil"></span></a>';
        $addUser = '&nbsp; | &nbsp;<a title="Add Users" rel="tooltip" onclick="addUserToGroup(' . $userGroupMasterID . ', ' . $description . ')">
                <span class="glyphicon glyphicon-user"></span></a>';
        if (!empty($result)) {
            $delete = '';
        } else {
            $delete = '&nbsp; | &nbsp;<a title="Delete" rel="tooltip" onclick="delete_UserGroup(' . $userGroupMasterID . ')">
                    <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }


        return '<span class="pull-right">' . $edit . '' . $addUser . '' . $delete . ' </span>';
    }
}

if (!function_exists('load_active_usergroups')) {
    function load_active_usergroups($userGroupMasterID, $active)
    {
        $output = '<div style="text-align: center;">';
        if ($active == 1) {
            $output .= '<input type="checkbox" id="userGroupIsactive_' . $userGroupMasterID . '" name="userGroupIsactive" onchange="userGroupIsactive(' . $userGroupMasterID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else {
            $output .= '<input type="checkbox" id="userGroupIsactive_' . $userGroupMasterID . '" name="userGroupIsactive" onchange="userGroupIsactive(' . $userGroupMasterID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }
        $output .= '</div>';
        return $output;
    }
}

if (!function_exists('usergroupuser_action')) {
    function usergroupuser_action($EIdNo, $userGroupMasterID)
    {
        $CI = &get_instance();
        $CI->db->select('pos_userGroupMasterID');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('srp_employeesdetails.EIdNo', $EIdNo);
        $CI->db->where('srp_employeesdetails.pos_userGroupMasterID', $userGroupMasterID);
        $result = $CI->db->get()->row_array();
        if ($result) {
            $edit = '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="empID_' . $EIdNo . '" name="empID[]" type="checkbox"  value="' . $EIdNo . '" class="radioChk" data-empID="' . $EIdNo . '" checked><label for="checkbox">&nbsp;</label> </div></div></div>';
        } else {
            $edit = '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="empID_' . $EIdNo . '" name="empID[]" type="checkbox"  value="' . $EIdNo . '" class="radioChk" data-empID="' . $EIdNo . '"><label for="checkbox">&nbsp;</label> </div></div></div>';
        }
        return '<span class="pull-right">' . $edit . ' </span>';
    }
}

if (!function_exists('load_edit_yield_preparation')) {
    function load_edit_yield_preparation($id, $confirmed)
    {
        $status = '<div style="text-align: center">';
        if ($confirmed) {

            $status .= '<a target="_blank" onclick="documentPageView_modal(\'YPRP\',\'' . $id . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;';
            $status .= '<a target="_blank" href="' . site_url('POS_yield_preparation/yield_preparation_print/') . '/' . $id . '/' . '" ><span class="glyphicon glyphicon-print"></span></a>&nbsp;&nbsp;| &nbsp;';
        } else {
            //$status .= '<a onclick="fetchPage(\'system/pos/settings/add_yield_preparation\',' . $id . ',\'Edit Yield Preparation\',\'YPRP\');" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;';
            $status .= '<a onclick="fetchPage(\'system/pos/settings/add_yield_preparation\',' . $id . ',\'Edit Yield Preparation\',\'YPRP\');" title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp; | &nbsp;<a onclick="delete_yield_preparation(' . $id . ');" title="Delete" rel="tooltip"><span class="glyphicon glyphicon-trash text-red"></span></a>&nbsp;&nbsp;| &nbsp;';
        }
        $status .= '<a onclick=\'attachment_modal(' . $id . ',"Yield Preparation","YPRP",' . $confirmed . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>';
        $status .= '</div>';
        return $status;
    }
}

if (!function_exists('load_yield_preparation_status')) {
    function load_yield_preparation_status($confirmed)
    {
        $status = '';
        if ($confirmed) {
            $status = '<div style="text-align: center;"><span class="label label-success">Confirmed</span></div>';
        } else {
            $status = '<div style="text-align: center;"><span class="label label-danger">Open</span>';
        }
        return $status;
    }
}

if (!function_exists('get_pos_yieldmaster')) {
    function get_pos_yieldmaster()
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_pos_menuyields.*,CONCAT(itemSystemCode,\" - \",itemDescription) as item");
        $CI->db->from('srp_erp_pos_menuyields');
        $CI->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID=srp_erp_pos_menuyields.itemAutoID', 'left');
        $CI->db->where('srp_erp_pos_menuyields.companyID', current_companyID());
        $result = $CI->db->get()->result_array();
        $output_arr = array();
        $output_arr[] = 'Please select';
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['yieldID'] ?? '') . "-" . $row['yielduomID'] . "-" . $row['qty'] . "-" . $row['itemAutoID']] = $row['item'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_creditCustomers')) {
    function get_creditCustomers()
    {
        $CI = &get_instance();
        $CI->db->select("posCustomerAutoID,CustomerAutoID,CustomerName,customerTelephone");
        $CI->db->from('srp_erp_pos_customermaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isActive', 1);
        $CI->db->where('isFromERP', 1);
        $result = $CI->db->get()->result_array();
        return $result;
    }
}
if (!function_exists('all_authentication_process')) {
    function all_authentication_process()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_auth_processmaster');
        $result = $CI->db->get()->result_array();
        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['processMasterID'] ?? '')] = $row['description'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('load_active_auth_process')) {
    function load_active_auth_process($processMasterID, $active)
    {
        $output = '<div style="text-align: center;">';
        if ($active == 1) {
            $output .= '<input type="checkbox" id="processIsActive_' . $processMasterID . '" name="processIsActive" onchange="changeProcessIsActive(' . $processMasterID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else {
            $output .= '<input type="checkbox" id="processIsActive_' . $processMasterID . '" name="processIsActive" onchange="changeProcessIsActive(' . $processMasterID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }
        $output .= '</div>';
        return $output;
    }
}

if (!function_exists('load_auth_process_action')) {
    function load_auth_process_action($id, $process, $processAssignID)
    {
        $output = '<div style="text-align: center;">';
        $output .= '<a href="#" onclick="assign_user_group(\'' . $id . '\',\'' . $process . '\')" rel="tooltip" title="Add User Group" ><i class="fa fa-users text-blue" aria-hidden="true"></i></a> &nbsp; | &nbsp;<a title="Delete" rel="tooltip" onclick="delete_Aut_process(' . $id . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        if ($id == 18 || $id == 19) {
            $output .= '&nbsp; | &nbsp;<a href="#" onclick="assign_capamnt_percentage(\'' . $id . '\',\'' . $processAssignID . '\')" rel="tooltip" title="Discount Configuration" ><i class="fa fa-cogs text-blue" aria-hidden="true"></i></a>';
        }
        $output .= '</div>';
        return $output;
    }
}

if (!function_exists('all_pos_usergroup')) {
    function all_pos_usergroup($posType = 1)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_auth_usergroupmaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('posType', $posType);
        $result = $CI->db->get()->result_array();
        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['userGroupMasterID'] ?? '')] = $row['description'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('load_user_group_assign_process')) {
    function load_user_group_assign_process($id)
    {
        $output = '<div style="text-align: center;">';
        $output .= '<a href="#" onclick="delete_assigned_user_group(\'' . $id . '\')" rel="tooltip" title="Delete" ><span class="glyphicon glyphicon-trash text-red"></span></a>';
        $output .= '</div>';
        return $output;
    }
}
/*end pos authentication*/

if (!function_exists('generate_pos_invoice_no')) {
    function generate_pos_invoice_no()
    {
        $outletID = get_outletID();
        $companyID = current_companyID();

        $CI = &get_instance();
        $CI->db->select("invoiceSequenceNo");
        $CI->db->from('srp_erp_pos_menusalesmaster');
        $CI->db->where('companyID', $companyID);
        $CI->db->where('wareHouseAutoID', $outletID);
        $CI->db->order_by('invoiceSequenceNo', 'desc');
        $invoiceSequenceNo = $CI->db->get()->row('invoiceSequenceNo');
        if ($invoiceSequenceNo) {
            $serialNo = $invoiceSequenceNo + 1;
        } else {
            $serialNo = 1;
        }
        return $serialNo;
    }
}

if (!function_exists('generate_pos_invoice_code')) {
    function generate_pos_invoice_code()
    {
        $outletID = get_outletID();
        $companyID = current_companyID();
        $outletInfo = get_outletInfo();

        $CI = &get_instance();
        $CI->db->select("invoiceSequenceNo");
        $CI->db->from('srp_erp_pos_menusalesmaster');
        $CI->db->where('companyID', $companyID);
        $CI->db->where('wareHouseAutoID', $outletID);
        $CI->db->order_by('invoiceSequenceNo', 'desc');
        $invoiceSequenceNo = $CI->db->get()->row('invoiceSequenceNo');


        if ($invoiceSequenceNo) {
            $serialNo = $invoiceSequenceNo + 1;
        } else {
            $serialNo = 1;
        }
        return $outletInfo['wareHouseCode'] . str_pad($serialNo, 6, "0", STR_PAD_LEFT);
    }
}

if (!function_exists('get_pos_invoice_code')) {
    function get_pos_invoice_code($menuSalesID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $CI = &get_instance();
        $CI->db->select("invoiceCode");
        $CI->db->from('srp_erp_pos_menusalesmaster');
        $CI->db->where('menuSalesID', $menuSalesID);
        $CI->db->where('wareHouseAutoID', $outletID);
        $invoiceCode = $CI->db->get()->row('invoiceCode');
        return $invoiceCode;
    }
}

if (!function_exists('get_pos_invoice_id')) {
    function get_pos_invoice_id($menuSalesID)
    {
        $CI = &get_instance();
        $CI->db->select("invoiceSequenceNo");
        $CI->db->from('srp_erp_pos_menusalesmaster');
        $CI->db->where('menuSalesID', $menuSalesID);
        $invoiceCode = $CI->db->get()->row('invoiceSequenceNo');
        return $invoiceCode;
    }
}

if (!function_exists('column_numberFormat')) {
    function column_numberFormat($amount, $decimal = 2)
    {
        if ($amount > 0) {
            $output = '<div class="pull-right">' . number_format($amount, $decimal) . '</div>';
        } else {
            $output = '<div class="pull-right">' . $amount . '</div>';
        }
        return $output;
    }
}


if (!function_exists('get_giftCardInfo')) {
    function get_giftCardInfo($barCode)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_cardissue');
        $CI->db->where('barCode', $barCode);
        $result = $CI->db->get()->row_array();

        return $result;
    }
}

if (!function_exists('get_outletInfo_byid')) {
    function get_outletInfo_byid($outletID)
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_warehousemaster.*, srp_erp_warehouse_users.counterID");
        $CI->db->from('srp_erp_warehouse_users');
        $CI->db->join('srp_erp_warehousemaster', ' srp_erp_warehousemaster.wareHouseAutoID =srp_erp_warehouse_users.wareHouseID ', 'left');
        $CI->db->where('srp_erp_warehouse_users.companyID', current_companyID());
        $CI->db->where('srp_erp_warehousemaster.isPosLocation', 1);
        $CI->db->where('srp_erp_warehousemaster.wareHouseAutoID', $outletID);
        $CI->db->where('srp_erp_warehousemaster.isActive', 1);
        $CI->db->where('srp_erp_warehouse_users.isActive', 1);
        $result = $CI->db->get()->row_array();
        return $result['wareHouseCode'] . ' - ' . $result['wareHouseDescription'];
    }
}

if (!function_exists('get_outletInfo_outletID')) {
    function get_outletInfo_outletID($outletID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('isPosLocation', 1);
        $CI->db->where('wareHouseAutoID', $outletID);
        $CI->db->where('isActive', 1);
        return $CI->db->get()->row_array();
    }
}

if (!function_exists('get_outletInfo_byid2')) {
    function get_outletInfo_byid2($outletID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('wareHouseAutoID', $outletID);
        $result = $CI->db->get()->row_array();
        return $result['wareHouseCode'] . ' - ' . $result['wareHouseDescription'];
    }
}


if (!function_exists('isDeliveryConfirmedOrder')) {
    function isDeliveryConfirmedOrder($menuSalesID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_deliveryorders');
        $CI->db->where('menuSalesMasterID', $menuSalesID);
        $CI->db->where('companyID', current_companyID());
        $result = $CI->db->get()->row_array();
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('get_confirmedDeliveryOrder')) {
    function get_confirmedDeliveryOrder($menuSalesID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_deliveryorders');
        $CI->db->where('menuSalesMasterID', $menuSalesID);
        $CI->db->where('companyID', current_companyID());
        $result = $CI->db->get()->row_array();
        return $result;
    }
}

if (!function_exists('get_employeeShortName')) {
    function get_employeeShortName($currentUserID = 0)
    {
        if ($currentUserID == 0) {
            $currentUserID = current_userID();
        }
        $CI = &get_instance();
        $shortName = $CI->db->select("EmpShortCode")->from('srp_employeesdetails')->where('EIdNo', $currentUserID)->get()->row('EmpShortCode');
        return $shortName;
    }
}
if (!function_exists('get_paidAmount')) {
    function get_paidAmount($invoiceID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $CI = &get_instance();
        $q = "SELECT SUM(amount) as tmpAmount FROM srp_erp_pos_menusalespayments WHERE menuSalesID=  '" . $invoiceID . "' AND wareHouseAutoID=" . $outletID;
        $amount = $CI->db->query($q)->row('tmpAmount');
        return $amount;
    }
}

if (!function_exists('get_cashReceivedAmount')) {
    function get_cashReceivedAmount($invoiceID)
    {
        $outletID = get_outletID();
        $CI = &get_instance();
        $q = "SELECT get_cashReceivedAmount FROM srp_erp_pos_menusalesmaster WHERE menuSalesID=  '" . $invoiceID . "' AND wareHouseAutoID=" . $outletID;
        $amount = $CI->db->query($q)->row('get_cashReceivedAmount');
        return $amount;
    }
}


if (!function_exists('get_promotionDescription')) {
    function get_promotionDescription($promotionID)
    {
        $CI = &get_instance();
        $q = "SELECT customerName FROM srp_erp_pos_customers WHERE customerID=  '" . $promotionID . "'";
        $description = $CI->db->query($q)->row('customerName');
        return $description;
    }
}

if (!function_exists('get_pos_payments_by_menuSalesID')) {
    function get_pos_payments_by_menuSalesID($menuSalesID, $outletID = 0)
    {
        $CI = &get_instance();
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $CI->db->select('SUM(payments.amount) as amount, config.description as description, config.autoID,customerMaster.CustomerName , payments.paymentConfigMasterID,payments.reference');
        $CI->db->from('srp_erp_pos_menusalespayments as payments');
        $CI->db->join('srp_erp_pos_paymentglconfigmaster as config', 'config.autoID = payments.paymentConfigMasterID', 'inner');
        $CI->db->join('srp_erp_pos_customermaster as customerMaster', 'customerMaster.CustomerAutoID = payments.customerAutoID', 'left');
        $CI->db->where('menuSalesID', $menuSalesID);
        $CI->db->where('payments.wareHouseAutoID', $outletID);
        $CI->db->group_by('payments.paymentConfigMasterID');
        $result = $CI->db->get()->result_array();
        return $result;
    }
}


if (!function_exists('get_pos_holdBillCount')) {
    function get_pos_holdBillCount()
    {
        $CI = &get_instance();
        $outletID = get_outletID();
        $companyID = current_companyID();
        $counterData = get_counterData();
        $shiftID = $counterData['shiftID'];

        $q = "SELECT
                    COUNT(srp_erp_pos_menusalesmaster.menuSalesID) AS holdCount
                FROM
                    srp_erp_pos_menusalesmaster
                    LEFT JOIN srp_erp_pos_deliveryorders ON srp_erp_pos_deliveryorders.menuSalesMasterID = srp_erp_pos_menusalesmaster.menuSalesID 
                WHERE
                    srp_erp_pos_menusalesmaster.isVoid = 0 
                    AND srp_erp_pos_menusalesmaster.isHold = 1 
                    AND srp_erp_pos_deliveryorders.deliveryOrderID IS NULL 
                    AND srp_erp_pos_menusalesmaster.shiftID = '" . $shiftID . "'
                    AND srp_erp_pos_menusalesmaster.wareHouseAutoID = '" . $outletID . "'
                    AND srp_erp_pos_menusalesmaster.companyID = '" . $companyID . "'
                ORDER BY
                    srp_erp_pos_menusalesmaster.menuSalesID DESC;";
        $holdCount = $CI->db->query($q)->row('holdCount');
        //echo $CI->db->last_query();

        return $holdCount;
    }
}


if (!function_exists('btn_viewKitchenStatus')) {
    function btn_viewKitchenStatus($id)
    {
        $output = '<div style="text-align: center;"> ';

        $output .= '<button class="btn btn-default btn-xs" onclick="loadKitchenStatusPreview(\'' . $id . '\')" > <i class="fa fa-eye"></i> View </button>';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('get_kitchenStatus')) {
    function get_kitchenStatus($kotID, $isOrderPending, $isOrderInProgress, $isOrderCompleted)
    {
        if ($kotID > 0) {
            if ($isOrderCompleted == 1) {
                echo '<span class="label label-success">Completed</span>';
            } else if ($isOrderInProgress == 1) {
                echo '<span class="label label-info">Processing</span>';
            } else if ($isOrderPending == 1) {
                echo '<span class="label label-warning">Pending</span>';
            } else if ($isOrderPending == -1) {
                echo '<span class="label label-danger">Pending KOT</span>';
            }
        } else {
            echo '-';
        }
    }
}


if (!function_exists('pos_isConfirmedDeliveryOrder')) {
    function pos_isConfirmedDeliveryOrder($menuSalesID)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_deliveryorders');
        $CI->db->where('menuSalesMasterID', $menuSalesID);
        $result = $CI->db->get()->row('menuSalesMasterID');
        return $result;
    }
}

if (!function_exists('get_cashiers_drp')) {
    function get_cashiers_drp()
    {
        $CI = &get_instance();
        $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " AND salesMaster.wareHouseAutoID=" . current_warehouseID() . "  GROUP BY salesMaster.createdUserID ";
        $result = $CI->db->query($q)->result_array();

        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['createdUserID'] ?? '')] = $row['empName'];
            }
        }
        return $output_arr;
    }
}


if (!function_exists('get_pos_policy')) {
    function get_pos_policy()
    {

        $CI = &get_instance();
        $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . "  GROUP BY salesMaster.createdUserID ";
        return $CI->db->query($q)->result_array();
    }
}


if (!function_exists('get_cashiers_drp_admin')) {
    function get_cashiers_drp_admin()
    {

        $CI = &get_instance();
        $q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
 FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . "  GROUP BY salesMaster.createdUserID ";
        $result = $CI->db->query($q)->result_array();


        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['createdUserID'] ?? '')] = $row['empName'];
            }
        }
        return $output_arr;
    }
}


if (!function_exists('get_deliveryConfirmedOrder')) {
    function get_deliveryConfirmedOrder($menuSalesID)
    {
        $CI = &get_instance();
        $CI->db->select("d.*,c.CustomerAddress1, c.CustomerName");
        $CI->db->from('srp_erp_pos_deliveryorders as d');
        $CI->db->join('srp_erp_pos_customermaster as c', 'c.posCustomerAutoID = d.posCustomerAutoID', 'left');
        $CI->db->where('d.menuSalesMasterID', $menuSalesID);
        $CI->db->where('d.companyID', current_companyID());
        $result = $CI->db->get()->row_array();
        return $result;
    }
}

if (!function_exists('get_outlet_printer')) {
    function get_outlet_printer()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_outletprinters');
        $CI->db->where('warehouseID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $result = $CI->db->get()->row_array();
        return $result["printerID"];
    }
}


if (!function_exists('get_outletFilterInfo')) {
    function get_outletFilterInfo($outletInput)
    {
        $outlets = get_active_outletInfo();
        $tmpArray = array();
        if (!empty($outletInput)) {
            foreach ($outletInput as $k => $c) {
                $key = array_search($c, array_column($outlets, 'wareHouseAutoID'));
                $tmpArray[] = $outlets[$key]['wareHouseCode'] . '-' . $outlets[$key]['wareHouseDescription'];
            }
        }
        return '<strong>' . join(', ', $tmpArray) . '</strong>';
    }
}


if (!function_exists('get_royalty_percentage')) {
    function get_royalty_percentage()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_franchisemaster');
        $CI->db->where('warehouseAutoID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $royaltyPercentage = $CI->db->get()->row('royaltyPercentage');
        return $royaltyPercentage;
    }
}


if (!function_exists('get_kitchenLimit')) {
    function get_kitchenLimit()
    {
        $limit = 10;
        return $limit;
    }
}


if (!function_exists('updateKOT_alarm')) {
    function updateKOT_alarm($invoiceID_array, $itemID_array)
    {

        $CI = &get_instance();
        if (!empty($invoiceID_array)) {
            $i = 0;
            foreach ($invoiceID_array as $val) {
                $data[$i]['menuSalesID'] = $val;
                $data[$i]['KOTAlarm'] = 1;
                $i++;
            }
        }
        if (!empty($data)) {
            $CI->db->update_batch('srp_erp_pos_menusalesmaster', $data, 'menuSalesID');
        }

        if (!empty($itemID_array)) {
            $i = 0;
            foreach ($itemID_array as $val) {
                $data2[$i]['menuSalesItemID'] = $val;
                $data2[$i]['KOTAlarm'] = 1;
                $i++;
            }
        }
        if (!empty($data2)) {
            $CI->db->update_batch('srp_erp_pos_menusalesitems', $data2, 'menuSalesItemID');
        }
    }
}

if (!function_exists('generateGiftCardSerialNo')) {
    function generateGiftCardSerialNo()
    {
        $CI = &get_instance();
        $q = "SELECT max(giftCardReceiptID) as receiptSN FROM srp_erp_pos_cardtopup";
        $t = $CI->db->query($q)->row('receiptSN');
        $result = $t + 1;
        return $result;
    }
}

if (!function_exists('get_category_breadcrumbs')) {
    function get_category_breadcrumbs($id = 0)
    {
        //echo 'fuck' . $id . '<br/>';
        $CI = &get_instance();
        /*
         *

        <div>...</div>
    	<a href="#"><div>label 1</div></a>
    	<a href="#"><div>A very very long label 2 to truncate</div></a>
    	<a href="#"><div>label 3</div></a>
        <a href="#"><div>A very very long label 4 to truncate</div></a>
        <a href="#"><div>label 5</div></a>
        <a href="#"><div>label 6</div></a>

        */
        $breadcrumbs = '<div><div id="bc1" class="myBreadcrumb">
                                <a href="#" onclick="refreshCategory()">Root Category </a>';
        //$breadcrumbs = '<ul class="breadcrumb">  <li onclick="refreshCategory()"><a href="#">Root Category </a></li>';
        $i = 0;
        $bc_tmp = array();
        while (true) {

            if ($id == 0) {
                break;
            }

            if ($id > 0) {
                $category = $CI->db->query("SELECT levelNo,menuCategoryID,masterLevelID,menuCategoryDescription FROM srp_erp_pos_menucategory WHERE  menuCategoryID='" . $id . "' ")->row_array('masterLevelID');

                if (!empty($category)) {


                    $id = $category['masterLevelID'];
                    $bc_tmp[$i]['menuCategoryID'] = $category['menuCategoryID'];
                    $bc_tmp[$i]['levelNo'] = $category['levelNo'];
                    $bc_tmp[$i]['masterLevelID'] = $id;
                    $bc_tmp[$i]['menuCategoryDescription'] = $category['menuCategoryDescription'];


                    $i++;
                    if ($i == 10) {
                        break;
                    }
                } else {
                    $id = 0;
                }
            }
        }

        if (!empty($bc_tmp)) {
            $count = count($bc_tmp);
            $bc_tmp = array_reverse($bc_tmp);
            $i = 0;
            foreach ($bc_tmp as $key => $item) {
                if ($key == ($count - 1)) {
                    //$breadcrumbs .= '<li class="active text-red">' . $item['menuCategoryDescription'] . '</li>';
                    $breadcrumbs .= '<a href="#" class="active"><div>' . $item['menuCategoryDescription'] . '</div></a>';
                } else {
                    $breadcrumbs .= '<a href="#" onclick="checkSubExist(\'' . $item['menuCategoryID'] . '\',\'' . $item['masterLevelID'] . '\',\'' . $item['levelNo'] . '\')" ><div>' . $item['menuCategoryDescription'] . '</div></a>';
                    // $breadcrumbs .= '<li><a onclick="checkSubExist(\'' . $item['menuCategoryID'] . '\',\'' . $item['masterLevelID'] . '\',\'' . $item['levelNo'] . '\')" href="#">' . $item['menuCategoryDescription'] . '</a></li>';
                }
                $i++;
            }
        }
        //var_dump($bc_tmp);
        //exit;

        //$breadcrumbs .= '</ul>';
        $breadcrumbs .= '</div> </div>';

        return $breadcrumbs;
    }
}

if (!function_exists('get_warehouse_category_breadcrumbs')) {
    function get_warehouse_category_breadcrumbs($id, $warehouse_id)
    {
        $CI = &get_instance();
        $breadcrumbs = '<div><div id="bc1" class="myBreadcrumb">
                                <a href="#" onclick="refreshCategory(' . $warehouse_id . ')">Root Category </a>';
        $i = 0;
        $bc_tmp = array();
        while (true) {

            if ($id == 0) {
                break;
            }

            if ($id > 0) {
                $category = $CI->db->query("SELECT levelNo,srp_erp_pos_menucategory.menuCategoryID,masterLevelID,menuCategoryDescription,warehouseID 
FROM srp_erp_pos_menucategory 
JOIN srp_erp_pos_warehousemenucategory ON srp_erp_pos_menucategory.menuCategoryID = srp_erp_pos_warehousemenucategory.menuCategoryID 
WHERE  srp_erp_pos_menucategory.menuCategoryID='" . $id . "' AND srp_erp_pos_warehousemenucategory.warehouseID='" . $warehouse_id . "' ")->row_array('masterLevelID');

                if (!empty($category)) {

                    $id = $category['masterLevelID'];
                    $bc_tmp[$i]['menuCategoryID'] = $category['menuCategoryID'];
                    $bc_tmp[$i]['levelNo'] = $category['levelNo'];
                    $bc_tmp[$i]['masterLevelID'] = $id;
                    $bc_tmp[$i]['menuCategoryDescription'] = $category['menuCategoryDescription'];

                    $i++;
                    if ($i == 10) {
                        break;
                    }
                } else {
                    $id = 0;
                }
            }
        }

        if (!empty($bc_tmp)) {
            $count = count($bc_tmp);
            $bc_tmp = array_reverse($bc_tmp);
            $i = 0;
            foreach ($bc_tmp as $key => $item) {
                if ($key == ($count - 1)) {
                    $breadcrumbs .= '<a href="#" class="active"><div>' . $item['menuCategoryDescription'] . '</div></a>';
                } else {
                    $breadcrumbs .= '<a href="#" onclick="checkSubExist(\'' . $item['menuCategoryID'] . '\',\'' . $item['masterLevelID'] . '\',\'' . $item['levelNo'] . '\',\'' . $warehouse_id . '\')" ><div>' . $item['menuCategoryDescription'] . '</div></a>';
                }
                $i++;
            }
        }
        $breadcrumbs .= '</div> </div>';

        return $breadcrumbs;
    }
}


if (!function_exists('get_segmentConfig')) {
    function get_segmentConfig()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from("srp_erp_pos_segmentconfig");
        $CI->db->where("wareHouseAutoID", get_outletID());
        $CI->db->where("companyID", current_companyID());
        $CI->db->where("isActive", -1);
        $result = $CI->db->get()->row_array();
        return $result;
    }
}


if (!function_exists('outlet_generateMissingItemsToOutlets')) {
    function outlet_generateMissingItemsToOutlets($companyID, $outletID)
    {
        $q = "INSERT INTO srp_erp_warehouseitems (
                warehouseAutoID,
                wareHouseLocation,
                wareHouseDescription,
                itemAutoID,
                itemSystemCode,
                itemDescription,
                unitOfMeasureID,
                unitOfMeasure,
                currentStock,
                companyID,
                companyCode,
                `TIMESTAMP`) (
                
                    SELECT
                    wm.wareHouseAutoID AS warehoseAutoID,
                    wm.wareHouseLocation AS wareHouseLocation,
                    wm.wareHouseDescription AS wareHouseDescription,
                    itemmaster.ItemAutoID AS itemautoID,
                    itemmaster.itemSystemCode AS itemSystemCode,
                    itemmaster.itemDescription AS itemDescription,
                    itemmaster.defaultUnitOfMeasureID AS unitOfMeasureID,
                    itemmaster.defaultUnitOfMeasure AS unitOfMeasure,
                    0 AS currentStock,
                    company.company_id AS companyID,
                    company.company_code AS companyCode,
                    NOW( ) AS `TIMESTAMP` 
                FROM
                    srp_erp_itemmaster itemmaster
                    LEFT JOIN srp_erp_warehousemaster wm ON wm.wareHouseAutoID = '" . $outletID . "'
                    LEFT JOIN srp_erp_company company ON company.company_id = '" . $companyID . "'
                WHERE
                    itemmaster.companyID = '" . $companyID . "' 
                    AND ( itemmaster.mainCategory != 'service' OR itemmaster.maincategory != 'Fixed Assets' ) 
                    AND itemmaster.itemautoiD NOT IN ( SELECT warehouseItems.itemAutoID FROM srp_erp_warehouseitems AS warehouseItems WHERE warehouseItems.warehouseAutoID = '" . $outletID . "' AND warehouseItems.companyID = '" . $companyID . "' )
                )";
        $CI = &get_instance();
        $result = $CI->db->query($q);
        return $result;
    }
}


if (!function_exists('get_policy_sampleBill')) {
    function get_policy_sampleBill()
    {
        $CI = &get_instance();
        $CI->load->libaray('pos_policy');
        $CI->pos_policy->isSampleBillRequired();
    }
}

if (!function_exists('is_show_KOT_button')) {
    function is_show_KOT_button()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 5);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = empty($policyID) ? true : false;

        return $policyID;
    }
}

if (!function_exists('isLocalPOSEnabled')) {
    function isLocalPOSEnabled()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', current_userID());
        $CI->db->where('isLocalPosSyncEnable', 1);
        $r = $CI->db->get()->row('isLocalPosSyncEnable');
        return $r;
    }
}

if (!function_exists('is_show_price_in_KOT_print')) {
    function is_show_price_in_KOT_print()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 11);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }
}

if (!function_exists('wastage_cost_policy')) {
    function wastage_cost_policy()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 26);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }
}

if (!function_exists('paymentUpdatePolicy')) {
    function paymentUpdatePolicy()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 28);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }
}

if (!function_exists('shiftCloseReport')) {
    function shiftCloseReport()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 27);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }
}


if (!function_exists('is_show_hold_reference_before_KOT')) {
    function is_show_hold_reference_before_KOT()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 12);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }
}

if (!function_exists('show_item_level_discount')) {
    function show_item_level_discount()
    {


        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 6);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = empty($policyID) ? false : true;

        return $policyID;
    }
}

if (!function_exists('is_wifi_password_in_bill')) {
    function is_wifi_password_in_bill()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 7);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;
        return $policyID;
    }
}

if (!function_exists('is_cctv_feed_active')) {
    function is_cctv_feed_active()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 8);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = empty($policyID) ? false : true;
        return $policyID;
    }
}


if (!function_exists('get_random_wifi_password')) {
    function get_random_wifi_password()
    {
        $q = "SELECT *  FROM
                    srp_erp_pos_wifipasswordsetup 
                WHERE
                    isUsed = 0  AND outletID = '" . get_outletID() . "'  AND companyID = '" . current_companyID() . "' 
                ORDER BY rand()  LIMIT 1";
        $CI = &get_instance();
        $result = $CI->db->query($q)->row_array();
        return $result;
    }
}


if (!function_exists('update_wifi_password')) {
    function update_wifi_password($id, $menuSalesID)
    {
        $data['menuSalesID'] = $menuSalesID;
        $data['isUsed'] = 1;

        $CI = &get_instance();
        $CI->db->where('id', $id);
        $result = $CI->db->update('srp_erp_pos_wifipasswordsetup', $data);
        return $result;
    }
}


if (!function_exists('get_waiter_list')) {
    function get_waiter_list()
    {
        $outletID = get_outletID();
        $companyID = current_companyID();
        $q = "SELECT
                    crew.crewMemberID,
                    crew.crewFirstName,
                    crew.crewLastName,
                    crew.EIdNo
                FROM
                    srp_erp_pos_crewmembers crew
                    LEFT JOIN srp_erp_pos_crewroles role ON role.crewRoleID = crew.crewRoleID
                    WHERE role.isWaiter =1 AND crew.companyID = '" . $companyID . "' and crew.wareHouseAutoID = '" . $outletID . "'";
        $CI = &get_instance();
        $result = $CI->db->query($q)->result_array();
        return $result;
    }
}


if (!function_exists('getUoMConvertion')) {
    function getUoMConvertion($transactionUoM, $defaultUoM)
    {
        if ($transactionUoM > 0 && $defaultUoM > 0) {
            $companyID = current_companyID();
            $q = "SELECT getUoMConvertion($transactionUoM,$defaultUoM,$companyID) as conversionRate";
            $CI = &get_instance();
            $conversionRate = $CI->db->query($q)->row('conversionRate');
            return $conversionRate;
        } else {
            return 1;
        }
    }
}


if (!function_exists('get_warehouseInfo')) {
    function get_warehouseInfo($invoiceID, $outletID = 0)
    {
        if ($outletID == 0) {
            $outletID = get_outletID();
        }
        $CI = &get_instance();
        $CI->db->select("warehouseImage,wareHouseDescription,warehouseAddress,warehouseTel,pos_footNote");
        $CI->db->from("srp_erp_pos_menusalesmaster");
        $CI->db->join("srp_erp_warehousemaster ", "srp_erp_warehousemaster.wareHouseAutoID = srp_erp_pos_menusalesmaster.wareHouseAutoID");
        $CI->db->where('srp_erp_pos_menusalesmaster.menuSalesID', $invoiceID);
        $CI->db->where('srp_erp_pos_menusalesmaster.wareHouseAutoID', $outletID);
        $result = $CI->db->get()->row_array();

        return $result;
    }
}


if (!function_exists('getItemMasterWacInPos')) {
    function getItemMasterWacInPos($menuDetailID, $wacAmount, $input = 0)
    {
        $html = '';
        if ($input == 1) {
            $html = '<input type="hidden" value="' . $wacAmount . '" id="avg_' . $menuDetailID . '">';
        } else if ($input == 2) {
            $html = '<input type="hidden" value="' . $wacAmount . '" id="input_' . $menuDetailID . '">';
        }
        return $html . "<div class='text-right f-wac'>" . number_format($wacAmount, 2) . "</div>";
    }
}

if (!function_exists('genInputMenuCost')) {
    function genInputMenuCost($id, $amount, $itemMasterWAC)
    {
        if (number_format($amount, 2) == number_format($itemMasterWAC, 2) || $itemMasterWAC == null || $amount == null || $itemMasterWAC < 0) {
            if ($itemMasterWAC < 0) {
                $cls = 'b-r';
            } else {
                $cls = '';
            }
            return "<div class='text-right f-wac' >  &nbsp;&nbsp; <input onchange = \"update_menu_detail_wac($id, this, 0)\" class='form-control w-100 $cls' type='text' value='" . number_format($amount, 2) . "'/></div>";
        } else {
            /** Allow update  */
            return "<div class='text-right f-wac'><button class='btn btn-xs btn-default' onclick='click_menu_detail_wac($id,1)'>&nbsp;&nbsp;<i class=\"fa fa-arrow-right\" ></i>&nbsp;&nbsp;</button>  &nbsp;&nbsp; <input id='input_$id' onchange=\"update_menu_detail_wac($id,this,1)\" class='form-control b-r w-100' type='text' value='" . number_format($amount, 2) . "'/></div>";
        }
    }
}

if (!function_exists('generate_menu')) {
    function generate_menu($menu)
    {
        $CI = &get_instance();
        $CI->load->library('pos_policy');
        $isPriceRequired = $CI->pos_policy->isPriceRequired();

        $btn_style = $menu['showImageYN'] == 1 ? ' background-color:#ffffff; background-image: url(' . $menu['menuImage'] . '); background-size: cover; background-repeat: no-repeat;     color: white; text-shadow: 0px 0px 4px black, 0 0 7px #000000, 0 0 3px #000000;' : ' background-color:' . $menu['bgColor'] . ';';

        $image_text = $menu['showImageYN'] == 1 ? ' image_text' : '';

        $tmpPack = $menu['isPack'] == 1 ? 1 : 0;
        $js_script = 'checkisKOT(' . $menu['warehouseMenuID'] . ', ' . $tmpPack . ', \'' . $menu['kotID'] . '\', \'' . addslashes($menu['menuMasterDescription']) . '\')';
        $Price = isset($isPriceRequired) && $isPriceRequired ? '<br>' . $menu['sellingPrice'] : '';

        if (!empty($menu['sizeDescription'])) {
            if (!empty($menu['colourCode'])) {
                $size = '<br/><div><span style="font-weight: 600; font-size: 12px;" >' . $menu['sizeDescription'] . '</span></div>';
            } else {
                $size = '<br/><h6>';
                $size .= '<span style="background-color:' . $menu['bgColor'] . '; " class="label label-default">' . $menu['sizeDescription'] . '</span>';
                $size .= '</h6>';
            }
        } else {
            $size = '';
        }

        if ($menu['isPack'] == 1) {
            $packInfo = '<br/>';
            $packInfo .= '<span rel="tooltip" title="pack" >';
            $packInfo .= '<i class="fa fa-star " style="color:darkgoldenrod"></i>';
            $packInfo .= '<i class="fa fa-star " style="color:darkgoldenrod"></i>';
            $packInfo .= '<i class="fa fa-star " style="color:darkgoldenrod"></i>';
            $packInfo .= '</span>';
        } else {
            $packInfo = '';
        }

        $html = '<div class="btnStyleCustom">';
        $html .= '<button data-code="' . $menu['warehouseMenuID'] . '" data-pack="' . $menu['isPack'] . '" value="item' . $menu['warehouseMenuID'] . '" style="' . $btn_style . '" class="itemButton glass" onclick="' . $js_script . '">';
        $html .= '<div class="proname' . $image_text . '">' . $menu['menuMasterDescription'] . $Price . $size . $packInfo . '</div>';
        $html .= '</button></div>';

        return $html;
    }
}

if (!function_exists('generate_menuCategory')) {
    function generate_menuCategory($Category, $parent)
    {
        $btn_style = $Category['showImageYN'] == 1 ? ' background-color:#ffffff; background-image: url(' . $Category['image'] . '); background-size: cover; background-repeat: no-repeat;     color: white; text-shadow: 0px 0px 4px black, 0 0 7px #000000, 0 0 3px #000000;' : ' background-color:' . $Category['bgColor'] . ';';

        $image_text = $Category['showImageYN'] == 1 ? ' image_text' : '';
        $style = !empty($Category['bgColor']) ? 'background-color:' . $Category['bgColor'] : '';
        $html = '<div class="btnStyleCustom">';
        $html .= '<button type="button" data-toggle="tab" tabindex="-1" data-parent="' . $parent . '" onclick="set_categoryInfo(' . $parent . ', ' . $Category['autoID'] . ')" style="' . $btn_style . '" id="categoryBtnID_' . $Category['autoID'] . '" href="#pilltab' . $Category['autoID'] . '" class="itemButton btnCategoryTab glass">';
        $html .= '<div class="proname' . $image_text . '">';
        $html .= str_replace("'", "&#39;", $Category['description']);
        $html .= '</div>';
        $html .= '</button></div>';
        return $html;
    }
}

if (!function_exists('generate_menuCategory_v2')) {
    function generate_menuCategory_v2($Category, $parent)
    {
        $btn_style = $Category['showImageYN'] == 1 ? ' background-color:#ffffff; background-image: url(' . $Category['image'] . '); background-size: cover; background-repeat: no-repeat;     color: white; text-shadow: 0px 0px 4px black, 0 0 7px #000000, 0 0 3px #000000;' : ' background-color:' . $Category['bgColor'] . ';';

        $image_text = $Category['showImageYN'] == 1 ? ' image_text' : '';
        $style = !empty($Category['bgColor']) ? 'background-color:' . $Category['bgColor'] : '';
        $html = '<div class="btnStyleCustom">';
        $html .= '<button type="button" data-toggle="tab" tabindex="-1" data-parent="' . $parent . '" onclick="set_categoryInfo(' . $parent . ', ' . $Category['autoID'] . ')" style="' . $btn_style . '" id="categoryBtnID_' . $Category['autoID'] . '" href="#pilltab' . $Category['autoID'] . '" class="itemButton btnCategoryTab glass">';
        $html .= '<div class="proname' . $image_text . '">';
        $html .= '</div>';
        $html .= '</button><div class="proname" style="text-align: center">';
        $html .= str_replace("'", "&#39;", $Category['description']);
        $html .= '</div></div>';
        return $html;
    }
}


if (!function_exists('col_pos_cameraSetup')) {
    function col_pos_cameraSetup($id)
    {
        $output = '<div style="text-align: center;">';
        $output .= '<button class="btn btn-default btn-xs" onclick="edit_camera_setup(\'' . $id . '\')" rel="tooltip" ><i class="fa fa-pencil-square-o"></i></button>';
        $output .= ' &nbsp; ';
        $output .= '<button class="btn btn-danger btn-xs" onclick="delete_camera_setup(' . $id . ')" rel="tooltip" ><i class="fa fa-trash-o"></i></button>';
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('col_pos_mailing_list')) {
    function col_pos_mailing_list($id)
    {
        $output = '<div style="text-align: center;">';
        $output .= '<button class="btn btn-danger btn-xs" onclick="delete_mailing_list(' . $id . ')" rel="tooltip" ><i class="fa fa-trash-o"></i></button>';
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('get_add_on_byItem')) {
    function get_add_on_byItem($menuSalesItemID)
    {
        $CI = &get_instance();
        $CI->db->select("menuMasterDescription,srp_erp_pos_menumaster.sellingPrice");
        $CI->db->from("srp_erp_pos_menusalesitems");
        $CI->db->join("srp_erp_pos_menumaster ", "srp_erp_pos_menumaster.menuMasterID = srp_erp_pos_menusalesitems.menuID");
        $CI->db->where('srp_erp_pos_menusalesitems.parentMenuSalesItemID', $menuSalesItemID);
        $output = $CI->db->get()->result_array();

        return $output;
    }
}

if (!function_exists('drop_general_pos_print_templates')) {
    function drop_general_pos_print_templates()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_printtemplatemaster');
        $CI->db->where('templateType', 'POSGEN');
        $result = $CI->db->get()->result_array();
        $output_arr = array();
        $output_arr[] = 'Please select';
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['printTemplateMasterID'] ?? '')] = $row['description'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('get_general_pos_print_default')) {
    function get_general_pos_print_default()
    {
        $CI = &get_instance();

        $CI->db->select("*");
        $CI->db->from("srp_erp_pos_printtemplatemaster");
        $CI->db->where("templateType", "POSGEN");
        $CI->db->where("isDefault", 1);
        $result2 = $CI->db->get()->row_array();
        if (!empty($result2)) {
            /** DB Default */
            $template = $result2['templateLink'];
        } else {
            /** System Default */
            $template = 'system/pos/printTemplate/gen-pos-invoice-print';
        }

        return $template;
    }
}

if (!function_exists('get_general_pos_print_defaultID')) {
    function get_general_pos_print_defaultID()
    {
        $CI = &get_instance();

        $CI->db->select("printTemplateMasterID");
        $CI->db->from("srp_erp_pos_printtemplatemaster");
        $CI->db->where("templateType", "POSGEN");
        $CI->db->where("isDefault", 1);
        $result2 = $CI->db->get()->row_array();
        if (!empty($result2)) {
            /** DB Default */
            $template = $result2['printTemplateMasterID'];
        } else {
            /** System Default */
            $template = 0;
        }

        return $template;
    }
}

if (!function_exists('get_general_pos_print_templates')) {
    function get_general_pos_print_templates($outletID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_pos_segmentconfig');
        $CI->db->join('srp_erp_pos_printtemplatemaster', 'srp_erp_pos_printtemplatemaster.printTemplateMasterID = srp_erp_pos_segmentconfig.generalPrintTemplateID', 'left');
        $CI->db->where('wareHouseAutoID', $outletID);
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isGeneralPOS', 1);
        $result = $CI->db->get()->row_array();
        if (!empty($result)) {

            if (!empty($result['templateLink'])) {
                /** Custom Template  */
                $template = $result['templateLink'];
            } else {
                $template = get_general_pos_print_default();
            }
        } else {
            $template = get_general_pos_print_default();
        }
        return $template;
    }
}

if (!function_exists('general_pos_footer_note')) {
    function general_pos_footer_note()
    {
        $CI = &get_instance();
        $note = '<div style="text-align: center">';
        $note .= $CI->common_data['company_data']['company_address1'];
        $note .= $CI->common_data['company_data']['company_address2'];
        $note .= 'Tel :' . $CI->common_data['company_data']['company_phone'] . '<br>';
        $note .= 'Email :' . $CI->common_data['company_data']['company_email'] . '<br>';
        $note .= '<b>' . $CI->common_data['company_data']['companyPrintTagline'] . '</b>';
        $note .= '</div>';
        return $note;
    }
}

if (!function_exists('status_BOT')) {
    function status_BOT($BOT, $source)
    {
        $output = '<div> ';
        if ($source == 1) {
            $output .= '<i class="fa fa-tablet text-red f-21" aria-hidden="true"></i> Tablet &nbsp;&nbsp;';
            if ($BOT == 1) {
                $output .= '<span class="label label-success">Completed</span>';
            } else {
                $output .= '<span class="label label-warning">Pending</span>';
            }
        } else {
            $output = '<div> <i class="fa fa-desktop text-primary f-15" aria-hidden="true"></i> POS  </div>';
        }
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('get_company_currency_decimal')) {
    function get_company_currency_decimal()
    {
        $CI = &get_instance();
        $d = 2;
        $CI->db->select("company_default_decimal");
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', current_companyID());
        $company_default_decimal = $CI->db->get()->row('company_default_decimal');
        if (!empty($company_default_decimal)) {
            $d = $company_default_decimal;
        }

        return $d;
    }
}

if (!function_exists('get_pos_paymentConfigID_cash')) {
    function get_pos_paymentConfigID_cash()
    {
        $CI = &get_instance();
        $CI->db->select("ID");
        $CI->db->from('srp_erp_pos_paymentglconfigdetail');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('paymentConfigMasterID', 1);
        $CI->db->where('warehouseID', get_outletID());
        $cashCashD = $CI->db->get()->row('ID');
        return $cashCashD;
    }
}

if (!function_exists('get_all_pos_customers')) {
    function get_all_pos_customers($status = true, $withTelNo = false, $frmCustomerMastr = false)/*Load all Customer*/
    {
        $CI = &get_instance();
        $CI->db->select("posCustomerAutoID,CustomerName,customerTelephone,CustomerAutoID");
        $CI->db->from('srp_erp_pos_customermaster');
        $CI->db->where('companyID', current_companyID());
        $customer_arr = array();
        $customer = $CI->db->get()->result_array();
        if (isset($customer)) {

            foreach ($customer as $row) {
                $tel = ($withTelNo) ? ' - ' . trim($row['customerTelephone'] ?? '') : '';
                if ($frmCustomerMastr) {
                    if (!empty($row['CustomerAutoID'])) {
                        $customer_arr[trim($row['CustomerAutoID'] ?? '')] = (trim($row['CustomerAutoID'] ?? '') ? trim($row['CustomerName'] ?? '') . $tel : '');
                    }
                } else {
                    $customer_arr[trim($row['posCustomerAutoID'] ?? '')] = (trim($row['posCustomerAutoID'] ?? '') ? trim($row['CustomerName'] ?? '') . $tel : '');
                }
            }
        }
        return $customer_arr;
    }
}

if (!function_exists('get_exist_erp_pos_customers')) {
    function get_exist_erp_pos_customers($IsActive = null)
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_pos_customermaster.posCustomerAutoID,
        IFNULL(srp_erp_customermaster.CustomerName,srp_erp_pos_customermaster.CustomerName) as CustomerName,
        IFNULL(srp_erp_customermaster.customerTelephone,srp_erp_pos_customermaster.customerTelephone) as customerTelephone,
        srp_erp_pos_customermaster.CustomerAutoID");
        $CI->db->from('srp_erp_pos_customermaster');
        $CI->db->join('srp_erp_customermaster', 'srp_erp_pos_customermaster.CustomerAutoID=srp_erp_customermaster.CustomerAutoID', 'left');
        $CI->db->where('srp_erp_pos_customermaster.companyID', current_companyID());
        if ($IsActive == 1) {
            $CI->db->where('srp_erp_pos_customermaster.isActive', 1);
        }
        $customer = $CI->db->get()->result_array();

        $customer_arr = array();
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['posCustomerAutoID'] ?? '')] = (trim($row['posCustomerAutoID'] ?? '') ? trim($row['CustomerName'] ?? '') . " - " . $row['customerTelephone'] : '');
            }
        }
        return $customer_arr;
    }
}

if (!function_exists('dropdown_yeield_preparationitems')) {
    function dropdown_yeield_preparationitems()
    {
        $CI = &get_instance();
        $CI->db->select('srp_erp_itemmaster.itemAutoID as itemAutoID,srp_erp_itemmaster.itemSystemCode as itemSystemCode,srp_erp_itemmaster.itemDescription as itemDescription ');
        $CI->db->from('srp_erp_pos_menuyieldpreparation');
        $CI->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_pos_menuyieldpreparation.itemAutoID');
        $CI->db->WHERE('srp_erp_pos_menuyieldpreparation.companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' | ' . trim($row['itemDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('open_delevery_order')) {
    function open_delevery_order()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 9);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = empty($policyID) ? true : false;

        return $policyID;
    }
}

if (!function_exists('open_delevery_person')) {
    function open_delevery_person()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 10);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        if (!empty($policyID)) {
            $policyID = 1;
        } else {
            $policyID = 0;
        }

        return $policyID;
    }
}

if (!function_exists('get_employeeList')) {
    function get_employeeList()
    {
        $CI = &get_instance();
        $list = $CI->db->select("EIdNo as employeeID, Ename2 as name, EEmail as email")
            ->from('srp_employeesdetails')
            ->where('Erp_companyID', current_companyID())
            ->get()
            ->result_array();
        return $list;
    }
}


if (!function_exists('get_schedulerList')) {
    function get_schedulerList()
    {
        $CI = &get_instance();
        $list = $CI->db->select("id, description")
            ->from('srp_erp_pos_batchlist')
            ->where('companyID', current_companyID())
            ->get()
            ->result_array();
        $o = array();
        if (!empty($list)) {
            foreach ($list as $s) {
                $o[trim($s['id'] ?? '')] = $s['description'];
            }
        }
        return $o;
    }
}

if (!function_exists('btn_voidBill_gpos')) {
    function btn_voidBill_gpos($id, $desc, $outletID, $documentSystemCode, $invoiceCode)
    {
        $output = '<div style="text-align: center;">';

        $output .= '<a  onclick="loadPrintTemplateVoid_gpos(\'' . $id . '\',\'' . $documentSystemCode . '\',\'' . $invoiceCode . '\')" rel="tooltip" title="' . $desc . '" ><span title="' . $desc . '" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn" data-original-title="' . $desc . '"></span></a>';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('btn_voidBill_gpos_history')) {
    function btn_voidBill_gpos_history($id, $desc, $outletID, $documentSystemCode, $invoiceCode)
    {
        $output = '<div style="text-align: center;">';

        $output .= '<a onclick="loadPrintTemplateVoid_gpos_history(\'' . $id . '\',\'' . $documentSystemCode . '\',\'' . $invoiceCode . '\')" rel="tooltip" title="' . $desc . '" ><span title="' . $desc . '" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn" data-original-title="' . $desc . '"></span></a>';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('get_pos_combos')) {
    function get_pos_combos($menuSalesID, $menuSalesItemID, $warehouseMenuID)
    {
        $CI = &get_instance();
        $CI->db->select('srp_erp_pos_valuepackdetail.valuePackDetailID,srp_erp_pos_valuepackdetail.qty,menuMaster.menuMasterDescription');
        $CI->db->from('srp_erp_pos_valuepackdetail');
        $CI->db->join('srp_erp_pos_menumaster menuMaster', 'menuMaster.menuMasterID = srp_erp_pos_valuepackdetail.menuID', 'left');
        $CI->db->WHERE('srp_erp_pos_valuepackdetail.menuSalesID', $menuSalesID);
        $CI->db->WHERE('srp_erp_pos_valuepackdetail.menuSalesItemID', $menuSalesItemID);
        $CI->db->WHERE('srp_erp_pos_valuepackdetail.warehouseMenuID', $warehouseMenuID);
        $data = $CI->db->get()->result_array();
        return $data;
    }
}


if (!function_exists('round_off_option')) {
    function round_off_option()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 13);
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        if (!empty($policyID)) {
            $policyID = 1;
        } else {
            $policyID = 0;
        }

        return $policyID;
    }
}


if (!function_exists('get_credit_salesCustomers')) {
    function get_credit_salesCustomers($menuSalesID)
    {
        $CI = &get_instance();
        $CI->db->select('cusm.customerName as CustomerName,cusm.customerAddress1 as CustomerAddress1,cusm.customerTelephone');
        $CI->db->from('srp_erp_pos_menusalespayments');
        $CI->db->join('srp_erp_customermaster cusm', 'cusm.customerAutoID = srp_erp_pos_menusalespayments.customerAutoID', 'left');
        $CI->db->where('srp_erp_pos_menusalespayments.menuSalesID', $menuSalesID);
        $CI->db->where('srp_erp_pos_menusalespayments.wareHouseAutoID', get_outletID());
        $CI->db->limit(1);
        $customers = $CI->db->get()->row_array();

        return $customers;
    }
}

if (!function_exists('getCustomerMaster')) {
    function getCustomerMaster($customerID)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_customermaster');
        $CI->db->where('srp_erp_customermaster.customerAutoID', $customerID);
        $customers = $CI->db->get()->row_array();

        return $customers;
    }
}


if (!function_exists('dd')) {
    function dd()
    {
        $args = func_get_args();
        echo "<pre>";
        foreach ($args as $arg) {
            print_r($arg);
        }
        echo "</pre>";
        die();
    }
}

if (!function_exists('removeFirstZero')) {
    function removeFirstZero($number)
    {
        if (is_numeric($number)) {
            $firstCharacter = $number[0];
            if ($firstCharacter === "0") {
                $number = ltrim($number, $number[0]);
            }
            return $number;
        }
        return 0;
    }
}

if (!function_exists('show_all_closed_bills_in_teminal')) {
    function show_all_closed_bills_in_teminal()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 16); // your new policy ID
        $CI->db->where('outletID', get_outletID());
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        $policyID = !empty($policyID) ? true : false;

        return $policyID;
    }
}

if (!function_exists('btn_Bill_History')) {
    function btn_Bill_History($id, $desc = 'View', $outletID = 0)
    {
        $output = '<div style="text-align: center;">';

        $output .= '<button class="btn btn-default btn-xs" onclick="loadPrintTemplateBillHistory(\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="' . $desc . '" ><i class="fa fa-eye" aria-hidden="true"></i> ' . $desc . '</button>';

        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('btn_hold_preview')) {
    function btn_hold_preview($id, $desc = 'Open Hold', $outletID = 0)
    {
        $output = '<div style="text-align: center;">';
        $output .= '<button class="btn btn-default btn-xs" onclick="previewHoldSales(\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="Preview" ><i class="fa fa-eye" aria-hidden="true"></i> preview</button>';
        $output .= '<button class="btn btn-default btn-xs" onclick="openHold_sales(\'' . $id . '\',\'' . $outletID . '\')" rel="tooltip" title="' . $desc . '" ><i class="fa fa-external-link" aria-hidden="true"></i> ' . $desc . '</button>&nbsp;&nbsp;';
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('get_gp_margin')) {
    function get_gp_margin($gp = 0, $sales = 0)
    {
        $gp_margin = 0;
        $colorCls = '';
        if ($gp != 0 && $sales != 0) {
            $gp_margin = $gp / $sales * 100;
        }

        if ($gp_margin < 0) {
            $colorCls = 'text-red';
        }
        return '<div style="text-align: right; font-weight: 800"  class="' . $colorCls . '">' . number_format($gp_margin, 2) . '%</div>';
    }
}

if (!function_exists('reprint_salesdetail_print')) {
    function reprint_salesdetail_print($outletID)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_pos_policydetail');
        $CI->db->where('posPolicyMasterID', 17);
        $CI->db->where('outletID', $outletID);
        $CI->db->where('companyID', current_companyID());
        $CI->db->limit(1);
        $policyID = $CI->db->get()->row('posPolicyID');
        if (!empty($policyID)) {
            $policyID = 1;
        } else {
            $policyID = 0;
        }

        return $policyID;
    }
}

if (!function_exists('actionSuperAdmin')) {
    function actionSuperAdmin($autoID, $checked, $WHID)
    {
        $output = '<div style="text-align: center;">';
        $chek = '';
        if ($checked == 1) {
            $chek = 'checked';
        }
        $output .= '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="superAdminYN_' . $autoID . '" type="checkbox" class="columnSelected suprAdmin" ' . $chek . '  value="' . $autoID . '" ><label for="checkbox">&nbsp;</label> </div></div></div>';
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('actionWarehouseAdmin')) {
    function actionWarehouseAdmin($autoID, $checked, $WHID)
    {

        $chek = '';
        if ($checked == 1) {
            $chek = 'checked';
        }

        $output = '<div style="text-align: center;">';
        $output .= '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="wareHouseAdminYN_' . $autoID . '" type="checkbox" class="columnSelected warehouseadmn" ' . $chek . '  value="' . $autoID . '_' . $WHID . '" ><label for="checkbox">&nbsp;</label> </div></div></div>';
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('loyalty_action')) {
    function loyalty_action($cardMasterID, $isActive)
    {
        $output = '<div style="text-align: center;">';
        $output .= '<button class="btn btn-default btn-xs" onclick="edit_loyalty(\'' . $cardMasterID . '\')" rel="tooltip" title="Edit" ><i class="fa fa-edit"></i></button>';
        $output .= '</div>';
        return $output;
    }
}

if (!function_exists('load_active_loyalty')) {
    function load_active_loyalty($isActive)
    {

        $status = '<center>';
        if ($isActive == 1) {
            $status .= '<span class="label label-success">&nbsp;</span>';
        } else {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_rpos_active_loyalty')) {
    function load_rpos_active_loyalty($cardMasterID, $active)
    {
        $output = '<div style="text-align: center;">';
        if ($active == 1) {
            $output .= '<input type="checkbox" id="cardSetupID_' . $cardMasterID . '" name="cardSetupID" onchange="update_card_active(' . $cardMasterID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else {
            $output .= '<input type="checkbox" id="cardSetupID_' . $cardMasterID . '" name="cardSetupID" onchange="update_card_active(' . $cardMasterID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }
        $output .= '</div>';
        return $output;
    }
}

if (!function_exists('load_active_points')) {
    function load_active_points($pointSetupID, $active)
    {
        $output = '<div style="text-align: center;">';
        if ($active == 1) {
            $output .= '<input type="checkbox" id="pointSetupID_' . $pointSetupID . '" name="pointSetupID" onchange="update_point_active(' . $pointSetupID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else {
            $output .= '<input type="checkbox" id="pointSetupID_' . $pointSetupID . '" name="pointSetupID" onchange="update_point_active(' . $pointSetupID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }
        $output .= '</div>';
        return $output;
    }
}

if (!function_exists('load_pos_loyalty_customer_drop')) {
    function load_pos_loyalty_customer_drop()
    {
        $CI = &get_instance();
        $company = $CI->common_data['company_data']['company_id'];


        $data = $CI->db->query("SELECT customerType, `cardMasterID`, IF(customerType=0,`srp_erp_customermaster`.`CustomerAutoID`,srp_erp_pos_customermaster.posCustomerAutoID) AS `customerID`,	
	                              IF(customerType=0,`srp_erp_customermaster`.`customerTelephone`,srp_erp_pos_customermaster.customerTelephone) AS `customerTelephone`,
	                              IF(customerType=0,`srp_erp_customermaster`.`customerName`,srp_erp_pos_customermaster.customerName) AS `customerName`
                                  FROM `srp_erp_pos_loyaltycard`
	                              LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_pos_loyaltycard`.`customerID` AND customerType = 0
			                      LEFT JOIN `srp_erp_pos_customermaster` ON `srp_erp_pos_customermaster`.`posCustomerAutoID` = `srp_erp_pos_loyaltycard`.`customerID` AND customerType = 1
                                  WHERE  `srp_erp_pos_loyaltycard`.`companyID` = $company")->result_array();

        return $data;
    }
}
if (!function_exists('load_general_active_loyalty')) {
    function load_general_active_loyalty($cardMasterID, $active)
    {
        $output = '<div style="text-align: center;">';
        if ($active == 1) {
            $output .= '<input type="checkbox" id="cardSetupID_' . $cardMasterID . '" name="cardSetupID" onchange="update_card_active_general(' . $cardMasterID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else {
            $output .= '<input type="checkbox" id="cardSetupID_' . $cardMasterID . '" name="cardSetupID" onchange="update_card_active_general(' . $cardMasterID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }
        $output .= '</div>';
        return $output;
    }
}
if (!function_exists('fetch_company_country')) {
    function fetch_company_country()
    {
        $CI = &get_instance();
        $company = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT countryCode, CountryDes,countryID FROM srp_erp_countrymaster WHERE countryID = ( SELECT countryID FROM srp_erp_company WHERE company_id = $company )")->row_array();

        return $data;
    }
}

if (!function_exists('get_delivery_persons')) {
    function get_delivery_persons()
    {
        $CI = &get_instance();
        $company = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("select * from srp_erp_pos_crewmembers
JOIN srp_erp_pos_crewroles on srp_erp_pos_crewroles.crewRoleID=srp_erp_pos_crewmembers.crewRoleID
where srp_erp_pos_crewroles.isDeliveryPerson=1 AND srp_erp_pos_crewroles.companyID=$company")->result_array();
        return $data;
    }
}

if (!function_exists('get_delivery_person_name')) {
    function get_delivery_person_name($crewMemberID)
    {
        $CI = &get_instance();
        $company = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("select * from srp_erp_pos_crewmembers
JOIN srp_erp_pos_crewroles on srp_erp_pos_crewroles.crewRoleID=srp_erp_pos_crewmembers.crewRoleID
where srp_erp_pos_crewroles.isDeliveryPerson=1 AND srp_erp_pos_crewroles.companyID=$company and srp_erp_pos_crewmembers.crewMemberID=$crewMemberID")->row();
        return $data;
    }
}

if (!function_exists('get_hold_bill_amount')) {
    function get_hold_bill_amount($menuSalesID)
    {
        $CI = &get_instance();
        $menusalesmaster = $CI->db->query("select srp_erp_pos_menusalesmaster.*,srp_erp_customertypemaster.customerDescription from srp_erp_pos_menusalesmaster 
join srp_erp_customertypemaster on srp_erp_customertypemaster.customerTypeID=srp_erp_pos_menusalesmaster.customerTypeID
where menuSalesID=$menuSalesID")->row_array();
        //$query = $CI->db->query("select * from srp_erp_pos_menusalesitems where menuSalesID=$menuSalesID");
        $query = $CI->db->query("select * from srp_erp_pos_menusalesitems
join srp_erp_pos_warehousemenumaster on srp_erp_pos_warehousemenumaster.warehouseMenuID =srp_erp_pos_menusalesitems.warehouseMenuID
join srp_erp_pos_menumaster on srp_erp_pos_menumaster.menuMasterID=srp_erp_pos_warehousemenumaster.menuMasterID
where menuSalesID=$menuSalesID");
        $total = 0;
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                //                $templateID = get_pos_templateID();
                //                $sellingPrice = getSellingPricePolicy($templateID,$row->pricewithoutTax ,$row->totalTaxAmount ,$row->totalServiceCharge);
                //                $total+=$sellingPrice;
                //$total += $row->menuSalesPrice + $row->totalMenuTaxAmount;
                $total += ($row->pricewithoutTax + $row->totalTaxAmount) * $row->qty;
                if (trim(strtolower($menusalesmaster['customerDescription'])) == 'dine-in' || trim(strtolower($menusalesmaster['customerDescription'])) == 'eat-in') {
                    $total += $row->totalServiceCharge;
                }
            }
        }
        if ($menusalesmaster['discountPer'] != 0) {
            $total -= $total * ($menusalesmaster['discountPer'] / 100);
        }
        $outletID = get_outletID();
        $query = $CI->db->query("SELECT 
 srp_erp_pos_outlettaxmaster.taxPercentage,
 srp_erp_pos_outlettaxmaster.outletTaxID,
 srp_erp_pos_outlettaxmaster.taxMasterID,
 srp_erp_pos_outlettaxmaster.taxDescription,
 srp_erp_taxmaster.supplierGLAutoID
 FROM `srp_erp_pos_outlettaxmaster` 
JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID=srp_erp_pos_outlettaxmaster.taxMasterID
where srp_erp_pos_outlettaxmaster.warehouseAutoID=$outletID AND srp_erp_pos_outlettaxmaster.isDeleted=0");
        $promotional_discount = 0; // has not set for now.
        $outlet_tax_list = $query->result();
        $amount_with_discount = $total - $promotional_discount;
        $tax_amount_total = 0;
        $current_companyID = current_companyID();
        $isOutletTaxEnabled = isOutletTaxEnabled($outletID, $current_companyID);
        if ($isOutletTaxEnabled == true) {
            foreach ($outlet_tax_list as $outlet_tax) {
                $taxPercentage = $outlet_tax->taxPercentage;
                $tax_amount = ($amount_with_discount / 100) * $taxPercentage;
                $tax_amount_total += $tax_amount;
            }
            $netTotal = $total + $tax_amount_total;
        } else {
            $netTotal = $total;
        }
        //        $q = "SELECT SUM(amount) as tmpAmount FROM srp_erp_pos_menusalespayments WHERE menuSalesID = '" . $menuSalesID . "'";
        //        $advancePayment = $CI->db->query($q)->row('tmpAmount');
        //        $netTotal=$netTotal - $advancePayment;
        return '<div style="text-align: right">' . number_format($netTotal, $CI->common_data['company_data']['company_default_decimal']) . '</div>';
    }
}

if (!function_exists('daybook_email_action')) {
    function daybook_email_action($dayBookEmailID)
    {
        $output = '<i title="Edit" onclick="open_email_edit_dialog.call(this)" data-id="' . $dayBookEmailID . '" class="glyphicon glyphicon-pencil corporate-goal-edit act-btn-margin" style="color: #3c8dbc;"></i>';
        $output .= '<i title="Remove" onclick="delete_email.call(this)" data-id="' . $dayBookEmailID . '" class="glyphicon glyphicon-trash act-btn-margin btn-task-delete" style="color: #ff3f3a;"></i>';
        return $output;
    }
}

if (!function_exists('get_cashiers_productmix')) {
    function get_cashiers_productmix()
    {
        $comapny_id = current_companyID();

        $CI = &get_instance();
        /*$q = "SELECT Ename2 as empName ,  salesMaster.createdUserID
            FROM srp_erp_pos_menusalesmaster salesMaster JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID WHERE salesMaster.companyID=" . current_companyID() . " GROUP BY salesMaster.createdUserID ";*/
        $q = "SELECT Ename2 as empName , salesMaster.createdUserID 
              FROM srp_erp_pos_menusalesmaster salesMaster 
              JOIN srp_employeesdetails employees ON employees.EIdNo = salesMaster.createdUserID 
              WHERE salesMaster.companyID=$comapny_id  GROUP BY salesMaster.createdUserID";
        $result = $CI->db->query($q)->result_array();
        //echo $q;
        $output_arr = array();
        if (isset($result)) {
            foreach ($result as $row) {
                $output_arr[trim($row['createdUserID'] ?? '')] = $row['empName'];
            }
        }
        return $output_arr;
    }
}

if (!function_exists('load_status_discount_item')) {
    function load_status_discount_item($customerID, $itemAutoID, $active)
    {
        $output = '<div style="text-align: center;">';
        if ($active == 1) {
            $output .= '<input type="checkbox" id="discountItemActive_' . $customerID . '_' . $itemAutoID . '" name="discountItemActive" onchange="changediscountItemActive(' . $customerID . ',' . $itemAutoID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        } else {
            $output .= '<input type="checkbox" id="discountItemActive_' . $customerID . '_' . $itemAutoID . '" name="discountItemActive" onchange="changediscountItemActive(' . $customerID . ',' . $itemAutoID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }
        $output .= '</div>';

        return $output;
    }
}

if (!function_exists('percentageEdit')) {
    function percentageEdit($discountPercentage, $autoID)
    {
        $output = '<div style="text-align: center;">';
        $output .= '<input value="' . $discountPercentage . '" style="width: 45px;" onclick="loadDiscountEditDialog.call(this)" data-promo_item_id="' . $autoID . '" readonly/>';
        $output .= '</div>';
        return $output;
    }
}

if (!function_exists('appraisalRatingAction')) {
    function appraisalRatingAction($appraisalRatingID)
    {
        $output = '<i title="Edit" onclick="openRatingEdit.call(this)" data-id="' . $appraisalRatingID . '" class="glyphicon glyphicon-pencil corporate-goal-edit act-btn-margin" style="color: #3c8dbc;"></i>';
        $output .= '<i title="Remove" onclick="deleteRating.call(this)" data-id="' . $appraisalRatingID . '" class="glyphicon glyphicon-trash act-btn-margin btn-task-delete" style="color: #ff3f3a;"></i>';
        return $output;
    }
}

if (!function_exists('getAppraisalRatingData')) {
    function getAppraisalRatingData()
    {
        $CI = &get_instance();
        $company = $CI->common_data['company_data']['company_id'];
        $CI->db->select("appraisalRatingID,ratedValue,rating,description");
        $CI->db->from('erp_srp_apr_appraisal_rating');
        $CI->db->where('isDeleted', 0);
        $CI->db->where('companyID', $company);
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('calculateCurrentStock')) {
    function calculateCurrentStock($itemAutoID, $wareHouseAutoID)
    {
        $CI = &get_instance();
        $company = $CI->common_data['company_data']['company_id'];
        $query = $CI->db->query("SELECT
                                    t1.itemAutoID,
                                    t1.itemSystemCode,
                                    t1.itemDescription,
                                    IFNULL(SUM(t1.transactionQTY/t1.convertionRate),0) AS currentStock,
                                    t2.mainCategory as mainCategory
                                FROM
                                    srp_erp_itemledger t1
                                JOIN srp_erp_itemmaster t2 ON t1.itemAutoID = t2.itemAutoID
                                WHERE
                                    t2.companyID = '{$company}'
                                AND t1.itemAutoID = '{$itemAutoID}'
                                AND isActive = 1
                                GROUP BY
                                    t1.itemAutoID");
        if ($query->num_rows() > 0) {
            return $query->row()->currentStock;
        } else {
            return "-";
        }
    }
}

if (!function_exists('get_warehouse_user_details')) {
    function get_warehouse_user_details()
    {
        $CI = &get_instance();
        $userID = current_userID();
        $row = $CI->db->query("select * from srp_erp_warehouse_users where userID=$userID and isActive=1")->row();
        return $row;
    }
}

if (!function_exists('getWaiterNameByMenuSalesID')) {
    function getWaiterNameColumnByMenuSalesID($menuSalesID)
    {
        $CI = &get_instance();
        $query = $CI->db->query("SELECT * FROM `srp_employeesdetails` 
join srp_erp_pos_crewmembers on srp_erp_pos_crewmembers.EIdNo=srp_employeesdetails.EIdNo
join srp_erp_pos_menusalesmaster on srp_erp_pos_menusalesmaster.waiterID=srp_erp_pos_crewmembers.crewMemberID
WHERE srp_erp_pos_menusalesmaster.menuSalesID=$menuSalesID");
        if ($query->num_rows() > 0) {
            $name = $query->row()->Ename1;
            $element = '<div>' . $name . '</div>';
        } else {
            $element = '<div></div>';
        }
        return $element;
    }
}

if (!function_exists('warehouseuserButtonEnableSwitch')) {
    function warehouseuserButtonEnableSwitch($userAccessID, $isDisabled, $buttonID, $warehouseUserID)
    {
        $element = '';
        if ($isDisabled) {
            $status = '';
        } else {
            $status = 'checked';
        }
        $element = '<input type="checkbox" data-warehouse_user_id="' . $warehouseUserID . '" data-button_id="' . $buttonID . '" data-user_access_id="' . $userAccessID . '" onchange="accessControlSwitchToggle.call(this)" class="accessControlSwitch"
                                               
                                               onchange="changeMenueCategoryIsactive()"
                                               data-size="mini"
                                               data-on-text="Enabled"
                                               data-handle-width="45" data-off-color="danger" data-on-color="success"
                                               data-off-text="Disabled"
                                               data-label-width="0" ' . $status . '>';
        return $element;
    }
}

if (!function_exists('paymentMasterEnableDisable')) {
    function paymentMasterEnableDisable($autoID, $isActive)
    {


        if ($isActive) {
            $status = 'checked';
        } else {
            $status = '';
        }
        $element = '<input type="checkbox" data-autoid="' . $autoID . '" onchange="deletePaymentMaster.call(this)" class="accessControlSwitch"                                               
                                               onchange="changeMenueCategoryIsactive()"
                                               data-size="mini"
                                               data-on-text="Enabled"
                                               data-handle-width="45" data-off-color="danger" data-on-color="success"
                                               data-off-text="Disabled"
                                               data-label-width="0" ' . $status . '>';
        return $element;
    }
}

if (!function_exists('paymentMasterAction')) {
    function paymentMasterAction($autoID)
    {
        $element = '<div style="text-align: center;"><i class="fa fa-edit" data-id="' . $autoID . '" onclick="editPaymentModalDialog.call(this)"></i></div>';
        return $element;
    }
}

if (!function_exists('get_gpos_location_with_status')) {
    function get_gpos_location_with_status()
    {
        $CI = &get_instance();
        $q = "SELECT
                    srp_erp_warehousemaster.wareHouseAutoID as wareHouseAutoID, srp_erp_warehousemaster.wareHouseCode as wareHouseCode, srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription ,IF((srp_erp_warehousemaster.isActive = 1), 'Active', 'Inactive')as isActive 
                FROM
                    `srp_erp_warehousemaster`
                    LEFT JOIN `srp_erp_pos_segmentconfig` ON `srp_erp_pos_segmentconfig`.`wareHouseAutoID` = `srp_erp_warehousemaster`.`wareHouseAutoID`
                    LEFT JOIN `srp_erp_segment` ON `srp_erp_segment`.`segmentID` = `srp_erp_pos_segmentconfig`.`segmentID` 
                WHERE
                    `srp_erp_warehousemaster`.`companyID` = '" . current_companyID() . "' 
                    AND `isPosLocation` = 1 
                    AND `srp_erp_pos_segmentconfig`.`isGeneralPOS` = 1";
        $output = $CI->db->query($q)->result_array();
        return $output;
    }
}

if (!function_exists('load_pos_location_drop_with_status')) {
    function load_pos_location_drop_with_status()
    {
        $CI = &get_instance();
        $company = $CI->common_data['company_data']['company_id'];
        $CI->db->select("wareHouseAutoID,wareHouseCode,wareHouseLocation,wareHouseDescription,IF((isActive = 1), 'Active', 'Inactive')as isActive");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('isPosLocation', 1);
        //$CI->db->where('isActive', 1);
        $CI->db->where('companyID', $company);
        return $CI->db->get()->result_array();
    }
}

if (!function_exists('get_active_outletInfo_with_status')) {
    function get_active_outletInfo_with_status()
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_warehousemaster.*,IF((isActive = 1), 'Active', 'Inactive')as isActive");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isPosLocation', 1);
        //$CI->db->where('isActive', 1);
        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('format_amount')) {
    function format_amount($amount)
    {
        $d = get_company_currency_decimal();
        $val = round($amount, $d);
        return $val;
    }
}

if (!function_exists('getMenusalesTax')) {
    function getMenusalesTax($menuSalesID)
    {
        $CI = &get_instance();
        $CI->db->select("SUM(srp_erp_pos_menusalestaxes.taxAmount) as sameTaxCategoryTotal,
        srp_erp_pos_menusalestaxes.*,
srp_erp_pos_menusalesitems.*,
srp_erp_taxmaster.*
        ");
        $CI->db->from('srp_erp_pos_menusalestaxes');
        $CI->db->join('srp_erp_taxmaster', 'srp_erp_taxmaster.taxMasterAutoID=srp_erp_pos_menusalestaxes.taxmasterID');
        $CI->db->join('srp_erp_pos_menusalesitems', 'srp_erp_pos_menusalestaxes.menuSalesItemID=srp_erp_pos_menusalesitems.menuSalesItemID');
        $CI->db->where('srp_erp_pos_menusalestaxes.companyID', current_companyID());
        $CI->db->where('srp_erp_pos_menusalestaxes.menuSalesID', $menuSalesID);
        $CI->db->group_by("srp_erp_pos_menusalestaxes.taxmasterID");
        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('generateMenusalesTax')) {
    function generateMenusalesTax($menuSalesID)
    {
        $CI = &get_instance();
        $warehouseID = get_outletID();
        $query = $CI->db->query("SELECT
                    tm.taxShortCode,
                    msi.salesPriceSubTotal,
                    SUM(mt.taxAmount)*msi.qty as sameTaxCategoryTotal,
                    msm.wareHouseAutoID as wareHouseAutoID,
                    msm.menuSalesID as menuSalesID,
                    msi.menuSalesItemID as menuSalesItemID,
                    msi.menuID as menuID,
                    mt.menuTaxID as menutaxID,
                    mt.taxmasterID as taxmasterID,
                    if(tm.taxcategory=2,tm.outputVatGLAccountAutoID,tm.supplierGLAutoID) as GLAutoID,
                    mt.taxPercentage as taxPercentage,
                    IF(msi.discountPer>0,IF(msm.promotionDiscount>0, (msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100)),IF(msm.promotionDiscount>0, (msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100)) * ((100-msm.promotionDiscount)/100), msi.qty * (mt.taxAmount - (mt.taxAmount*msi.discountPer/100)) * ((100-msm.discountPer)/100))) as taxAmount,	
                    msi.qty * mt.taxAmount as beforeDiscountTaxAmount,
                    msm.discountPer as menusalesDiscount,
                    msm.promotionDiscount as menusalesPromotionalDiscount,
                    mt.taxAmount as unitMenuTaxAmount,
                    msi.qty as menusalesItemQty,
                    msm.companyID as companyID,
                    msm.companyCode as companyCode,
                    msm.createdUserGroup as createdUserGroup,
                    msm.createdPCID as createdPCID,
                    msm.createdUserID as createdUserID,
                    CURRENT_TIMESTAMP() as createdDateTime,
                    msm.createdUserName as createdUserName,
                    null as modifiedPCID,
                    null as modifiedUserID,
                    null as modifiedDateTime,
                    null as modifiedUserName,
                    CURRENT_TIMESTAMP() as `timestamp`
                
                FROM
                    srp_erp_pos_menusalesmaster msm
                INNER JOIN srp_erp_pos_menusalesitems msi ON msi.menuSalesID = msm.menuSalesID AND msi.wareHouseAutoID = msm.wareHouseAutoID
                INNER JOIN srp_erp_pos_warehousemenumaster whm ON whm.menuMasterID = msi.menuID
                INNER JOIN srp_erp_pos_menutaxes mt ON mt.menuMasterID = msi.menuID
                INNER JOIN srp_erp_taxmaster	tm ON mt.taxmasterID = tm.taxMasterAutoID
                WHERE
                    msm.menuSalesID = '$menuSalesID' 
                    AND msm.wareHouseAutoID = '$warehouseID'
                    AND whm.warehouseID = msm.wareHouseAutoID 
                    AND whm.isDeleted = 0 AND whm.isActive = 1 
                    AND whm.isTaxEnabled=1
                    GROUP BY mt.taxmasterID");

        $result = $query->result_array();
        return $result;
    }

    if (!function_exists('discount_column_salesdetails')) {
        function discount_column_salesdetails($discountAmount, $generalDiscountAmount, $promotiondiscountAmount)
        {
            return $discountAmount + $generalDiscountAmount + $promotiondiscountAmount;
        }
    }

    if (!function_exists('totalreturn_column_salesdetails')) {
        function totalreturn_column_salesdetails($totalreturn,$invoiceID)
        {
            $CI = &get_instance();
            $d = $CI->common_data['company_data']['company_default_decimal'];
            $content = '';
            if (number_format($totalreturn, $d) > 0) {
                $content='<td class="ar"><button class="btn btn-xs btn-link" onclick="load_return_pos_invoices(\''.$invoiceID.'\')"> '. number_format($totalreturn, $d) .'</button></td>';
            } else {
                $content = '<td class="ar">'.number_format($totalreturn, $d).'</td>';
            }
            return $content;
        }
    }

    if (!function_exists('viewbillcolumn')) {
        function viewbillcolumn($invoiceID,$documentSystemCode)
        {                           
            return '<button class="btn btn-xs btn-link" onclick="invoicePrint('.$invoiceID.', \''.$documentSystemCode.'\', \'PNT\')">  View bill </button>';
           
        }
    }

    if (!function_exists('viewreturnbillcolumn')) {
        function viewreturnbillcolumn($returnID,$documentSystemCode)
        {                           
            return '<button class="btn btn-xs btn-link" onclick="return_print('.$returnID.', \''.$documentSystemCode.'\')">  View Return </button>';
           
        }
    }

    if (!function_exists('load_item_from_different_warehouses')) {
        function load_item_from_different_warehouses($barcode)
        {
            $CI = &get_instance();
            $company = $CI->common_data['company_data']['company_id'];
            $CI->db->select("*");
            $CI->db->from('srp_erp_warehouseitems as i');
            $CI->db->join('srp_erp_warehousemaster as wh','i.wareHouseAutoID = wh.wareHouseAutoID','left');
            $CI->db->where('i.barCodeNo', $barcode);
            $CI->db->where('i.ActiveYN', 1);
            $CI->db->where('i.companyID', current_companyID());
            return $CI->db->get()->result_array();
        }
    }

    if (!function_exists('get_uom_details_items')) {
        function get_uom_details_items($itemAutoID,$shortCode)
        {
            $CI =& get_instance();
            $companyID = $CI->common_data['company_data']['company_id'];
            $conversion_arr = array();
            $conversion_results = array();
    
            $CI->db->select('defaultUnitOfMeasureID');
            $CI->db->where('itemAutoID', $itemAutoID);
            $CI->db->from('srp_erp_itemmaster as im');
            $defaultUnitOfMeasureID = $CI->db->get()->row('defaultUnitOfMeasureID');
    
            if($defaultUnitOfMeasureID){
    
                $CI->db->select('srp_erp_unit_of_measure.UnitID,UnitShortCode,UnitDes,conversion');
                $CI->db->from('srp_erp_unitsconversion');
                $CI->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_unitsconversion.subUnitID');
                $CI->db->where('masterUnitID',$defaultUnitOfMeasureID);
                if($shortCode){
                    $CI->db->where('UnitShortCode',$shortCode);
                    $CI->db->where('srp_erp_unitsconversion.companyID',$companyID);
                    $conversion_results =  $CI->db->get()->row_array();
                }else{
                    $CI->db->where('srp_erp_unitsconversion.companyID',$companyID);
                    $conversion_results =  $CI->db->get()->result_array();
                }
               
    
            }

            return $conversion_results;
        }
    }

    if (!function_exists('load_item_from_invoice')) {
        function load_item_from_invoice($invoiceID,$company_id)
        {
            $CI = &get_instance();
            $CI->db->select("pi.*");
            $CI->db->from('srp_erp_pos_invoicedetail as pi');
            // $CI->db->join('srp_erp_itemmaster as im','pi.itemAutoID = im.itemAutoID','left');
            $CI->db->where('pi.invoiceID', $invoiceID);
            $CI->db->where('pi.companyID', $company_id);
            return $CI->db->get()->result_array();
        }
    }

    if (!function_exists('get_posconfig_details')) {
        function get_posconfig_details($key,$wareHouseID,$type = null)
        {
            $CI = &get_instance();
            $company = $CI->common_data['company_data']['company_id'];

            $CI->db->select("pi.*");
            $CI->db->from('srp_erp_pos_paymentglconfigdetail as pi');
            if($type){
                $CI->db->where('pi.ID', $key);
            }else{
                $CI->db->where('pi.paymentConfigMasterID', $key);
            }
           
            $CI->db->where('pi.warehouseID', $wareHouseID);
            return $CI->db->get()->row_array();
        }
    }


}
