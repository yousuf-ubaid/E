<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_config extends ERP_Controller
{
    private $menumaster_subcat_ids = array();
    private $menumaster_subcat_up_ids = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('Pos_config_model');
        $this->load->helper('pos');

    }

    function index()
    {

    }

    function get_srp_erp_pos_segmentConfig()
    {
        $this->datatables->select('config.segmentConfigID as id, concat(segment.companyCode, " - " , segment.description) as segmentDes, wareHouse.wareHouseDescription as wareHouseDescription, industry.industryTypeDescription, posTemplate.posTemplateDescription,config.wareHouseAutoID as warehouseid , wareHouse.wareHouseLocation  as  wareHouseLocation,wareHouse.wareHouseCode  as  wareHouseCode, wareHouse.wareHouseCode  as  wareHouseCode ', false)
            ->from('srp_erp_pos_segmentconfig as config')
            ->join('srp_erp_warehousemaster as wareHouse', 'wareHouse.wareHouseAutoID = config.wareHouseAutoID', 'LEFT')
            ->join('srp_erp_industrytypes as industry', 'industry.industrytypeID = config.industrytypeID', 'LEFT')
            ->join('srp_erp_pos_templatemaster as posTemplate', 'posTemplate.posTemplateID = config.posTemplateID', 'LEFT')
            ->join('srp_erp_segment as segment', 'segment.segmentID = config.segmentID', 'LEFT')
            ->where('config.isActive', -1)
            ->where('config.companyID', current_companyID())
            ->add_column('outletDesc', '$1 - $2 - $3', 'wareHouseCode, wareHouseDescription, wareHouseLocation')
            ->edit_column('btn_menu', '$1', 'posConfig_menu(id)')
            ->edit_column('btn_crew', '$1', 'posConfig_crew(id)')
            ->edit_column('btn_table', '$1', 'posConfig_btn_table(id,warehouseid)')
            ->edit_column('btn_kot', '$1', 'posConfig_kot(id)')
            ->edit_column('btn_outlet_tax', '$1', 'posConfig_outlet_tax(warehouseid)')
            ->edit_column('btn_set', '$1', 'col_posConfig(id)');
        $r = $this->datatables->generate();
        echo $r;
    }

    function save_posConfig()
    {
        $this->form_validation->set_rules('wareHouseAutoID', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('segmentID', 'Segment', 'required|numeric');
        $this->form_validation->set_rules('industrytypeID', 'Industry Type', 'required|numeric');
        $this->form_validation->set_rules('posTemplateID', 'POS template', 'required|numeric');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {

            $validate = $this->Pos_config_model->validate_posConfig();
            if ($validate) {
                $segmentID = $this->input->post('segmentID');
                $data = array(
                    "wareHouseAutoID" => $this->input->post('wareHouseAutoID'),
                    "industrytypeID" => $this->input->post('industrytypeID'),
                    "posTemplateID" => $this->input->post('posTemplateID'),
                    "companyID" => current_companyID(),
                    "companyCode" => current_company_code(),
                    "segmentID" => $segmentID,
                    "segmentCode" => get_segment_code($segmentID),
                    "createdUserID" => current_userID(),
                    "createdPCID" => current_pc(),
                    "createdDateTime" => format_date_mysql_datetime(),
                    "createdUserName" => current_user(),
                    "timeStamp" => format_date_mysql_datetime()
                );

                $result = $this->Pos_config_model->save_posConfig($data);
                echo json_encode($result);
            } else {
                //echo json_encode(array('error' => 1, 'message' => 'This record is already exit'));
                echo json_encode(array('error' => 1, 'message' => 'This outlet is already added'));
            }

        }
    }

    function delete_segmentConfig()
    {
        $id = $this->input->post('id');

        $validate = $this->Pos_config_model->validate_delete_segmentConfig();
        if ($validate) {
            $data['isActive'] = 0;
            $data['deletedBy'] = current_userID();
            $data['deletedDatetime'] = format_date_mysql_datetime();
            $result = $this->Pos_config_model->update_segmentConfig($id, $data);
            $this->db->delete('srp_erp_pos_segmentconfig', array('segmentConfigID' => $id));

            if ($result) {
                $tmp = array('error' => 0, 'message' => 'Record deleted', 'id' => $id);
            } else {
                $tmp = array('error' => 1, 'message' => 'Error, Insert Error, Please contact your system support team');
            }
        } else {
            $tmp = array('error' => 1, 'message' => 'Can not delete!<br/> Outlet is in operation. ');
        }

        echo json_encode($tmp);
    }


    function posConfig_menu()
    {
        $id = $this->input->post('id');
        $data['id'] = $id;
        $posConfig = $this->Pos_config_model->get_srp_erp_pos_segmentconfig_specific($id);
        $data['menuCategoryList'] = $this->Pos_config_model->getMenuCategories($posConfig['wareHouseAutoID']);
        $data['posConfig_master'] = $posConfig;
        $this->load->view('system/pos/ajax/ajax-load-pos-config-menu', $data);
    }

    function setup_menu()
    {
        $id = $this->input->post('id');
        $data['id'] = $id;
        $posConfig = $this->Pos_config_model->get_srp_erp_pos_segmentconfig_specific($id);

        $q = "SELECT wh.wareHouseDescription FROM srp_erp_pos_segmentconfig sc JOIN srp_erp_warehousemaster wh ON wh.wareHouseAutoID = sc.wareHouseAutoID WHERE sc.segmentConfigID = '" . $id . "'";
        $r = $this->db->query($q)->row_array();
        $data['outletName'] = !empty($r['wareHouseDescription']) ? $r['wareHouseDescription'] : 'Menu';
        $data['menuCategoryList'] = $this->Pos_config_model->get_warehouse_menu_categories_setup($posConfig['wareHouseAutoID']);
        $data['posConfig_master'] = $posConfig;
        $data['parentID'] = 0;
        $data['parentLevel'] = 0;
        $data['wareHouseAutoID'] = $posConfig['wareHouseAutoID'] ? $posConfig['wareHouseAutoID'] : 0;
        $data['breadcrumbs'] = get_warehouse_category_breadcrumbs(0, $posConfig['wareHouseAutoID']);

        $this->load->view('system/pos/ajax/ajax-load-pos-config-menu-setup', $data);
    }

    function pos_config_menu_warehouse()
    {
        $posConfig = null;
        $warehouse_id = $this->input->post('wareHouseID');
        $master_level_id = $this->input->post('masterLevelID');
        $level_no = $this->input->post('levelNo');
        $level_no = $level_no > 0 ? $level_no : 0;
        $master_level_id = $master_level_id > 0 ? $master_level_id : 0;
        $id = $this->input->post('id');
        $data['id'] = $id;
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menucategory');
        $this->db->where('menuCategoryID', $master_level_id);
        $result = $this->db->get()->row_array();
        $breadcrumbs = get_warehouse_category_breadcrumbs($master_level_id, $warehouse_id);
        $data['menuCategoryList'] = $this->Pos_config_model->get_warehouse_menu_categories_setup($warehouse_id, $level_no, $master_level_id);
        $data['parentID'] = $result['masterLevelID'];
        $data['parentLevel'] = $result['levelNo'];
        $data['posConfig_master'] = $posConfig;
        $data['breadcrumbs'] = $breadcrumbs;
        $data['wareHouseAutoID'] = $warehouse_id ? $warehouse_id : 0;

        $this->load->view('system/pos/ajax/ajax-load-pos-config-menu-setup', $data);
    }

    function load_warehouse_category()
    {
        $ware_house_id = $this->input->post('wareHouseID');
        $level_no = $this->input->post('levelNo');
        $master_level_id = $this->input->post('masterLevelID');

        // Sub Query
        $this->db->select('menuCategoryID');
        $this->db->from('srp_erp_pos_warehousemenucategory');
        $this->db->where('wareHouseID', $ware_house_id);
        $this->db->where('isDeleted', 0);
        $subQuery = $this->db->get_compiled_select();

        $this->db->select('mc.*');
        $this->db->from('srp_erp_pos_menucategory mc');
        $this->db->where("mc.menuCategoryID NOT IN ($subQuery)", NULL, FALSE);
        $this->db->where('mc.isDeleted', 0);
        $this->db->where('mc.isActive', 1);
        $this->db->where('mc.levelNo', $level_no);
        $this->db->where('mc.companyID', $this->common_data['company_data']['company_id']);
        if ($master_level_id > 0 && $master_level_id) {
            $this->db->where('mc.masterLevelID', $master_level_id);
        }
        $this->db->order_by('mc.sortOrder', "asc");
        $result = $this->db->get()->result_array();

        $select = "<select class='form-control select2' name='menuCategoryID' id='menuCategoryID_MenuSetup' required><option value=''>Please Select One</option>";
        foreach ($result as $row) {
            $select .= "<option value='" . $row['menuCategoryID'] . "'  >" . $row['menuCategoryDescription'] . "</option>";
        }
        $select .= "</select>";
        echo json_encode($select);
    }

    function posConfig_menu_company()
    {
        $id = current_companyID();
        $data['id'] = $id;
        $posConfig = null;
        $tmp_masterLevelID = $this->input->post('masterLevelID');
        $this->db->select('*');
        $this->db->from('srp_erp_pos_menucategory');
        $this->db->where('menuCategoryID', $tmp_masterLevelID);
        $result = $this->db->get()->row_array();

        $breadcrumbs = get_category_breadcrumbs($tmp_masterLevelID);
        $data['menuCategoryList'] = $this->Pos_config_model->getMenuCategories_company($id);
        $data['parentID'] = $result['masterLevelID'] ?? '';
        $data['parentLevel'] = $result['levelNo'] ?? '';
        $data['posConfig_master'] = $posConfig;
        $data['breadcrumbs'] = $breadcrumbs;

        $this->load->view('system/pos/ajax/ajax-load-pos-config-menu-company', $data);
    }

    function check_sub_exist_warehouse_menu_category()
    {
        $menuCategoryID = $this->input->get('menuCategoryID');
        $masterLevelID = $this->input->get('masterLevelID');
        $levelNo = $this->input->get('levelNo');
        $warehouse_id = $this->input->get('wareHouseAutoID');

        $this->db->select('*');
        $this->db->from('srp_erp_pos_menucategory mc');
        //$this->db->join('srp_erp_pos_warehousemenucategory wc', 'mc.menuCategoryID = wc.menuCategoryID','left');
        $this->db->where('masterLevelID', $menuCategoryID);
        $this->db->where('mc.isDeleted', 0);
        $output = $this->db->get()->row_array();
        $masterExist = false;

        $this->db->select('autoID');
        $this->db->from('srp_erp_pos_warehousemenucategory');
        $this->db->where('menuCategoryID', $menuCategoryID);
        $this->db->where('warehouseID', $warehouse_id);
        $this->db->where('isDeleted', 0);
        $row = $this->db->get()->row_array();
        $auto_id = isset($row['autoID']) ? $row['autoID'] : 0;
        if (!empty($output)) {
            $masterExist = true;
        }

        echo json_encode(array('masterExist' => $masterExist, 'masterLevelID' => $masterLevelID,
            'levelNo' => $levelNo + 1, 'menuCategoryID' => $menuCategoryID, 'warehouse_id' => $warehouse_id, 'autoID' => $auto_id));
    }

    function checkSubExist_menuCategory()
    {
        $menuCategoryID = $this->input->get('menuCategoryID');
        $masterLevelID = $this->input->get('masterLevelID');
        $levelNo = $this->input->get('levelNo');

        $this->db->select('*');
        $this->db->from('srp_erp_pos_menucategory');
        $this->db->where('masterLevelID', $menuCategoryID);
        $this->db->where('isDeleted', 0);
        $output = $this->db->get()->row_array();

        $masterExist = false;
        if (!empty($output)) {
            $masterExist = true;
        }
        echo json_encode(array('masterExist' => $masterExist, 'masterLevelID' => $masterLevelID, 'levelNo' => $levelNo + 1, 'menuCategoryID' => $menuCategoryID));
    }

    function addMenuCategory_company()
    {

        $this->form_validation->set_rules('menuCategoryDescription', 'Category Description', 'trim|required');
        $this->form_validation->set_rules('revenueGLAutoID', 'GL Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {


            $color = $this->input->post('bgColor');
            $id = $this->input->post('segmentConfigID');
            $pkId = $this->input->post('menuCategoryID');

            $path = 'uploads/';

            $path = 'uploads/';
            $categoryName = $this->input->post('menuCategoryDescription');


            $fileName = 'pos_menu_category_' . '_' . time();
            $config['upload_path'] = $path;
            $config['allowed_types'] = 'png|jpg|jpeg';
            $config['max_size'] = '200000';
            $config['file_name'] = $fileName;

            $fileExist = false;
            if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
                $fileExist = true;
            }

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            $this->db->trans_start();

            $tmpImagePath = $_FILES['image']['name'];
            $ext = pathinfo($tmpImagePath, PATHINFO_EXTENSION);

            if ($pkId == 0) {

                /** Insert */
                if ($fileExist && !$this->upload->do_upload("image")) {
                    echo json_encode(array('error' => 1, 'message' => 'Upload failed ' . $this->upload->display_errors()));
                } else {

                    $tmpData = $this->upload->data();

                    $data['menuCategoryDescription'] = $categoryName;
                    $data['image'] = $fileExist ? 'uploads/' . $fileName . $tmpData["file_ext"] : 'images/no-logo.png';
                    $data['companyID'] = current_companyID();
                    $data['revenueGLAutoID'] = $this->input->post('revenueGLAutoID');
                    $data['isActive'] = $this->input->post('isActive');
                    $data['bgColor'] = $color;
                    $data['masterLevelID'] = $this->input->post('masterLevelID');
                    $data['levelNo'] = $this->input->post('levelNo');
                    $data['createdPCID'] = current_pc();
                    $data['createdUserID'] = current_companyID();
                    $data['createdDateTime'] = format_date_mysql_datetime();
                    $data['createdUserName'] = current_user();
                    $data['createdUserGroup'] = user_group();
                    $data['timeStamp'] = format_date_mysql_datetime();


                    $result = $this->Pos_config_model->addMenuCategory($data);
                    $masterLevelID = isset($data['masterLevelID']) && !empty($data['masterLevelID']) ? $data['masterLevelID'] : 0;

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                    } else {
                        $this->db->trans_commit();
                        $output = UPLOAD_PATH . "/portal/" . $fileName;
                        echo json_encode(array('error' => 0, 'message' => 'Successfully added', 'img' => $output, 'code' => $id, 'addCategoryFromSub' => $this->input->post('addCategoryFromSub'), 'masterLevelID' => $masterLevelID, 'levelNo' => $this->input->post('levelNo'), 'post' => $this->input->post()));
                    }
                }
            } else {
                if ($fileExist && !$this->upload->do_upload("image")) {
                    echo json_encode(array('error' => 1, 'message' => 'Upload failed ' . $path . ' <br/>' . $this->upload->display_errors()));
                    exit;
                }
                $inherit_colour = $this->input->post('inherit_colour');
                if (isset($inherit_colour) && !empty($inherit_colour)) {
                    $this->inherit_bgColours($pkId);
                }


                if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
                    $this->upload->do_upload("image");
                    $data['image'] = 'uploads/' . $fileName . '.' . $ext;
                }

                $data['menuCategoryDescription'] = $categoryName;
                $data['isActive'] = $this->input->post('isActive');

                if ($this->input->post('isActive') == "1") {
                    $masterLevelID = $this->input->post('menuCategoryID');

                    //Active under category sub category
                    $menu_subcat_id_array = $this->Pos_config_model->get_menumaster_subcat_tree_ids_by_menucategoryid($masterLevelID);
                    $this->menumaster_subcat_buildtree_by_menucategoryid($menu_subcat_id_array);

                    if (!empty($this->menumaster_subcat_up_ids)) {
                        foreach ($this->menumaster_subcat_up_ids as $menu_subcat_id) {
                            $menu_subcat_data['isActive'] = 1;
                            $this->Pos_config_model->updateMenuCategory_isactive($menu_subcat_data, $menu_subcat_id);
                        }
                    }


                    $MenuCategoryData['isActive'] = '1';
                    $this->Pos_config_model->updateMenuCategory_isactive($MenuCategoryData, $id);
                }

                //start In-active sub cat
                if ($this->input->post('inactive_subcat') == 1) {
                    // In-active under category menu items
                    $menu_items_array = $this->Pos_config_model->get_menumaster_details_by_catid($pkId);
                    if (!empty($menu_items_array)) {
                        foreach ($menu_items_array as $menu_item) {
                            $menu_item_data['menuStatus'] = 0;
                            $menuSubCategoryID = $menu_item['menuMasterID'];
                            $this->Pos_config_model->updateMenu_byMasterID($menu_item_data, $menuSubCategoryID);
                        }
                    }

                    // In-active under category sub category
                    $menu_subcat_id_array = $this->Pos_config_model->get_menumaster_subcat_tree_ids_by_masterlevelid($pkId);
                    $this->menumaster_subcat_buildtree_by_masterlevelid($menu_subcat_id_array);

                    if (!empty($this->menumaster_subcat_ids)) {
                        foreach ($this->menumaster_subcat_ids as $menu_subcat_id) {
                            $menu_subcat_data['isActive'] = 0;
                            $this->Pos_config_model->updateMenuCategory_isactive($menu_subcat_data, $menu_subcat_id);

                            // In-active under category menu items
                            $menu_items_array = $this->Pos_config_model->get_menumaster_details_by_catid($menu_subcat_id);
                            if (!empty($menu_items_array)) {
                                foreach ($menu_items_array as $menu_item) {
                                    $menu_item_data['menuStatus'] = 0;
                                    $menuSubCategoryID = $menu_item['menuMasterID'];
                                    $this->Pos_config_model->updateMenu_byMasterID($menu_item_data, $menuSubCategoryID);
                                }
                            }
                        }
                    }
                }
                //end In-active sub cat


                $data['revenueGLAutoID'] = $this->input->post('revenueGLAutoID');
                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_companyID();
                $data['modifiedDateTime'] = format_date_mysql_datetime();
                $data['modifiedUserName'] = current_user();
                $data['bgColor'] = $color;

                $this->Pos_config_model->updateMenuCategory($data, $pkId);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('error' => 0, 'message' => 'Record updated Successfully', 'code' => $id, 'addCategoryFromSub' => $this->input->post('addCategoryFromSub')));
                }
            }
        }

    }

    function menumaster_subcat_buildtree_by_masterlevelid(array $elements)
    {
        foreach ($elements as $element) {

            $menu_subcat_id_array = $this->Pos_config_model->get_menumaster_subcat_tree_ids_by_masterlevelid($element['menuCategoryID']);
            $this->menumaster_subcat_buildtree_by_masterlevelid($menu_subcat_id_array);

            $this->menumaster_subcat_ids[] = $element['menuCategoryID'];
        }

    }

    function menumaster_subcat_buildtree_by_menucategoryid(array $elements)
    {
        foreach ($elements as $element) {

            $menu_subcat_id_array = $this->Pos_config_model->get_menumaster_subcat_tree_ids_by_menucategoryid($element['masterLevelID']);
            $this->menumaster_subcat_buildtree_by_menucategoryid($menu_subcat_id_array);

            $this->menumaster_subcat_up_ids[] = $element['menuCategoryID'];
        }

    }

    function inherit_bgColours($parentID)
    {
        if (!empty($parentID)) {
            $bgColor = $this->input->post('bgColor');
            $result = $this->getParent($parentID);
            $data = array();
            if (!empty($result)) {
                $i = 0;
                foreach ($result as $item) {
                    $data[$i]['menuCategoryID'] = $item;
                    $data[$i]['bgColor'] = $bgColor;
                    $i++;
                }
            }
            if (!empty($data)) {
                $this->db->update_batch('srp_erp_pos_menucategory', $data, 'menuCategoryID');
            }
        }
    }


    function getParent($parentID)
    {
        $keys = array($parentID);
        $this->db->select('menuCategoryID');
        $this->db->from('srp_erp_pos_menucategory');
        $this->db->where('masterLevelID', $parentID);
        $this->db->where('isDeleted', 0);
        $parentArray = $this->db->get()->result_array();
        if (!empty($parentArray)) {
            foreach ($parentArray as $tmp_key) {
                $keys[] = $tmp_key['menuCategoryID'];
                $tmpResult = $this->getChild($tmp_key['menuCategoryID']);
                if (!empty($tmpResult)) {
                    foreach ($tmpResult as $child) {
                        $keys[] = $child['menuCategoryID'];
                    }
                }
            }
        }
        return $keys;
    }

    function getChild($child)
    {
        $this->db->select('menuCategoryID');
        $this->db->from('srp_erp_pos_menucategory');
        $this->db->where('masterLevelID', $child);
        $this->db->where('isDeleted', 0);
        $parentArray = $this->db->get()->result_array();
        if (!empty($parentArray)) {
            return $parentArray;
        } else {
            return false;
        }
    }


    function addMenuCategory_setup()
    {

        $this->form_validation->set_rules('menuCategoryID', 'Category', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {

            $pkId = $this->input->post('autoID');
            $categoryID = $this->input->post('menuCategoryID');
            $segmentConfigID = $this->input->post('segmentConfigID');
            /*$menuCategory = $this->Pos_config_model->get_srp_erp_pos_menucategory_specific($categoryID);*/
            $segmentConfig = $this->Pos_config_model->get_srp_erp_pos_segmentconfig_specific($segmentConfigID);
            $warehouseID = $segmentConfig['wareHouseAutoID'];

            if ($pkId == 0) {

                $validate_menuCategory = $this->Pos_config_model->validate_menuCategory($categoryID, $warehouseID);
                if ($validate_menuCategory) {
                    $data['menuCategoryID'] = $categoryID;
                    $data['warehouseID'] = $warehouseID;
                    $data['companyID'] = current_companyID();
                    $data['createdPCID'] = current_pc();
                    $data['createdUserID'] = current_companyID();
                    $data['createdDateTime'] = format_date_mysql_datetime();
                    $data['createdUserName'] = current_user();
                    $data['createdUserGroup'] = user_group();
                    $data['timeStamp'] = format_date_mysql_datetime();

                    $wareHouseOutput = $this->Pos_config_model->add_srp_erp_pos_warehousemenucategory($data);
                    $this->Pos_config_model->menuBulkUpdate($categoryID, $warehouseID, $wareHouseOutput['id']);


                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                    } else {
                        $this->db->trans_commit();
                        echo json_encode(array('error' => 0, 'message' => 'Successfully added', 'code' => $segmentConfigID, 'warehouseID' => $warehouseID));
                    }
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'The Category already added in the list.'));
                }


            } else {

                /*if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
                    $this->upload->do_upload("image");
                    $data['image'] = 'uploads/' . $fileName . '.' . $ext;
                }

                $data['menuCategoryDescription'] = $categoryName;
                $data['isActive'] = $this->input->post('isActive');
                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_companyID();
                $data['modifiedDateTime'] = format_date_mysql_datetime();
                $data['modifiedUserName'] = current_user();


                $this->Pos_config_model->updateMenuCategory($data, $pkId);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('error' => 0, 'message' => 'Record updated Successfully', 'code' => $id));
                }*/
                echo json_encode(array('error' => 1, 'message' => 'Error', 'code' => 0));
            }
        }

    }

    function deleteCategory()
    {
        $id = $this->input->post('id');

        $deletable = $this->Pos_config_model->check_below_level_deletable_category($id);

        if ($deletable) {
            $this->db->trans_begin();
            $data['isDeleted'] = 1;
            $data['deletedBy'] = current_userID();
            $data['deletedDatetime'] = format_date_mysql_datetime();

            $this->Pos_config_model->updateMenuCategory($data, $id);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('error' => 1, 'message' => '<strong>Error</strong><br/><br/> Error while deleting.'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('error' => 0, 'message' => '<strong>Record Deleted</strong>'));
            }

        } else {
            echo json_encode(array('error' => 1, 'message' => 'Category Can not be deleted!'));
        }
    }

    function editCategory()
    {
        $id = $this->input->post('id');
        $output = $this->Pos_config_model->get_srp_erp_pos_menucategory_specific($id);
        if (!empty($output)) {
            echo json_encode(array_merge($output, array('error' => 0, 'message' => 'done')));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'An error has occurred, please contact your system support team.'));
        }

    }

    /** Menu : Load || Controller   */
    function loadMenuItems()
    {
        $id = $this->input->post('categoryID');
        $data['id'] = $id;
        $data['category'] = $this->Pos_config_model->get_srp_erp_pos_menucategory_specific($id);
        $this->load->view('system/pos/ajax/ajax-load-menu-items', $data);
    }

    function load_menu_items_warehouse()
    {
        $id = $this->input->post('categoryID');
        $auto_id = $this->input->post('autoID');
        $data['id'] = $auto_id;
        $data['category'] = $this->Pos_config_model->get_srp_erp_pos_menucategory_specific($id);
        $data['category']['warehouseID'] = $this->input->post('warehouseID');
        $this->load->view('system/pos/ajax/ajax-load-menu-items-warehouse-setup', $data);
    }

    /** Menu Setup*/
    function loadMenuItemsSetup()
    {

        $id = $this->input->post('categoryID');
        $data['id'] = $id;
        $data['category'] = $this->Pos_config_model->get_srp_erp_pos_menucategory_specific($id);
        $this->load->view('system/pos/ajax/ajax-load-menu-items-setup', $data);
    }

    function loadwarehouse_MenuItemsSetup()
    {
        $id = $this->input->post('autoID');
        $data['id'] = $id;
        $data['category'] = $this->Pos_config_model->get_srp_erp_pos_warehousemenucategory_specific($id);
        $this->load->view('system/pos/ajax/ajax-load-menu-items-setup', $data);
    }

    function kot_apply_to_all()
    {
        $kotID_common = $this->input->post('kotID_common');
        $warehouseID = $this->input->post('warehouse_autoID');
        $autoID = $this->input->post('warehouseMenuCategoryID');
        $this->db->where('warehouseMenuCategoryID', $autoID);
        $this->db->where('warehouseID', $warehouseID);
        $result = $this->db->update('srp_erp_pos_warehousemenumaster', array('kotID' => $kotID_common));
        if ($result) {
            echo json_encode(array('error' => 0, 'status' => 's', 'message' => 'updated', 'id' => $autoID));
        } else {
            echo json_encode(array('error' => 1, 'status' => 'e', 'message' => 'something went wrong!, please refresh the page and try again or contact the system support team'));
        }

    }

    /** Item Cost DataTable */
    function loadItemCostTableInfo()
    {
        $this->datatables->select('md.menuDetailID as menuDetailID, md.itemAutoID as itemAutoID, md.defaultUnitCost as defaultUnitCost,  md.UOM as UOM, md.uomID as uomID, md.unitCost as unitCost, md.cost as cost, md.qty as qty, i.itemSystemCode as itemSystemCode, i.itemName as itemName, i.defaultUnitOfMeasure as defaultUnitOfMeasure,  i.defaultUnitOfMeasureID as defaultUnitOfMeasureID, i.companyLocalWacAmount as companyLocalWacAmount, i.defaultUnitOfMeasure as defaultUnitOfMeasure, (sum(IFNULL(defaultUnitCost,0)) )/count(menuDetailID) as ItemDetail_avgCost', false)
            ->from('srp_erp_pos_menudetails as md')
            ->join('srp_erp_itemmaster as i', 'i.itemAutoID  = md.itemAutoID', 'INNER')
            ->where('md.companyID', current_companyID())
            ->group_by('md.itemAutoID')
            ->edit_column('itemMasterCost', '$1', 'getItemMasterWacInPos(menuDetailID,companyLocalWacAmount,1)')
            ->edit_column('existingWAC', '$1', 'getItemMasterWacInPos(menuDetailID,ItemDetail_avgCost,2)')
            ->edit_column('avgCost', '$1', 'genInputMenuCost(menuDetailID, ItemDetail_avgCost, companyLocalWacAmount)')
            ->add_column('DT_RowId', 'menu_row_$1', 'id');
        echo $this->datatables->generate();
    }

    function update_menu_detail_wac()
    {
        $this->form_validation->set_rules('menuDetailID', 'ID', 'trim|required');
        $this->form_validation->set_rules('WACAmount', 'Amount', 'trim|required|numeric');
        $this->form_validation->set_rules('state', 'state', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $avgCost = $this->input->post('avgCost');
            $error_message = validation_errors();
            echo json_encode(array('error' => 'e', 'message' => $error_message, 'error_code' => 0, 'avg_cost_value' => $avgCost));

        } else {
            $newAmount = $this->input->post('WACAmount');
            $state = $this->input->post('state');
            $menuDetail = $this->Pos_config_model->load_menu_detail_edit();
            $itemInfo = $this->Pos_config_model->get_srp_erp_itemmaster($menuDetail['itemAutoID']);
            $avgCost = $this->Pos_config_model->get_avg_cost_menuItem($menuDetail['itemAutoID']);

            if ($newAmount > 0) {
                if ($itemInfo['companyLocalWacAmount'] < $newAmount && $itemInfo['companyLocalWacAmount'] != 0 && false) {
                    echo json_encode(array('error' => 'w', 'message' => 'Unit Cost of <strong>' . str_replace(array('\'', '"'), array('&apos;', '&quot;'), $itemInfo['itemName']) . '</strong> can not be grater than <strong>' . $itemInfo['companyLocalWacAmount'] . '</strong>', 'avg_cost_value' => $avgCost));
                } else {
                    /*Get menu items */
                    $menuDetail = $this->Pos_config_model->get_srp_erp_pos_menudetails_byItemAutoID($menuDetail['itemAutoID']);
                    if (!empty($menuDetail)) {
                        $data = array();
                        $i = 0;

                        /** Audit Info */
                        $modifiedUserID = current_userID();
                        $modifiedPCID = current_pc();
                        $modifiedDateTime = format_date_mysql_datetime();
                        $modifiedUserName = current_user();

                        foreach ($menuDetail as $item) {
                            if ($item['defaultUomId'] > 0) {
                                $conversionRate = getUoMConvertion($item['uomID'], $item['defaultUomId']);
                                $data[$i]['defaultUomId'] = $item['defaultUomId'];
                            } else {
                                $defaultUomId = $itemInfo['defaultUnitOfMeasureID'];
                                $conversionRate = getUoMConvertion($item['uomID'], $defaultUomId);
                                $data[$i]['defaultUomId'] = $defaultUomId;
                            }

                            $data[$i]['menuDetailID'] = $item['menuDetailID'];
                            $data[$i]['unitCost'] = $newAmount / $conversionRate;
                            $data[$i]['defaultUnitCost'] = $newAmount;
                            $data[$i]['actualInventoryCost'] = $newAmount;
                            $data[$i]['cost'] = ($newAmount / $conversionRate) * $item['qty']; // Total Cost

                            /** Audit Info */
                            $data[$i]['modifiedDateTime'] = $modifiedDateTime;
                            $data[$i]['modifiedPCID'] = $modifiedPCID;
                            $data[$i]['modifiedUserID'] = $modifiedUserID;
                            $data[$i]['modifiedUserName'] = $modifiedUserName;

                            $i++;
                        }
                    }
                    if (!empty($data)) {
                        $this->db->update_batch('srp_erp_pos_menudetails', $data, 'menuDetailID');
                    }

                    echo json_encode(array('error' => 's', 'message' => 'done', 'avg_cost_value' => $avgCost));
                }
            } else {
                echo json_encode(array('error' => 'e', 'message' => 'New Recipe WAC must be grater than zero', 'avg_cost_value' => $avgCost));
            }
        }
    }


    /** Menu : Load Data Table || Controller    */
    function loadMenuItem_table()
    {
        $id = $this->input->post('menuCatID');
        $decimal = $this->common_data['company_data']['company_default_decimal'];
        $this->datatables->select('m.menuMasterID as id, m.menuCategoryID as menuCategoryID , m.menuImage as menuImage, m.menuMasterDescription as menuMasterDescription, m.menuCategoryID as menuCategoryID, m.sellingPrice  as sellingPrice , m.revenueGLAutoID as revenueGLAutoID,  coa.GLDescription as  GLDescription, m.menuStatus as menuStatus, m.menuCost as menuCost, isPack as isPack, isVeg as isVeg, ms.description AS sizeDescription, ms.code as sizeCode,m.isAddOn as AddOn, m.sortOrder as sortOrder, m.showImageYN as showImageYN', false)
            ->from('srp_erp_pos_menumaster as m')
            ->join('srp_erp_pos_menusize ms', 'ms.menuSizeID = m.menuSizeID', 'LEFT')
            ->join('srp_erp_chartofaccounts coa', 'coa.GLAutoID = m.revenueGLAutoID', 'LEFT')
            ->where('m.menuCategoryID', $id)
            ->where('m.isDeleted', 0)
            ->add_column('DT_RowId', 'menu_row_$1', 'id')
            ->edit_column('status', '$1', 'get_active_status(menuStatus)')
            ->edit_column('selPrice', '$1', 'format_number_dataTable(sellingPrice,' . $decimal . ')')
            ->edit_column('menuCostTmp', '$1', 'format_number_dataTable(menuCost)')
            ->edit_column('menuImageOut', '$1', 'menuImage(menuImage)')
            ->edit_column('isPax_btn', '$1', 'isPaxBtn(id,menuCategoryID,isPack)')
            ->edit_column('showImageYN_btn', '$1', 'showImageYN_btn(id,menuCategoryID,showImageYN)')
            ->edit_column('isVeg_btn', '$1', 'isVegBtn(id, menuCategoryID, isVeg)')
            ->edit_column('isAddOn_btn', '$1', 'isAddOnbtn(id, menuCategoryID, AddOn)')
            ->edit_column('sortOrder', '$1', 'col_sortOrderMenu(id,sortOrder)')
            ->edit_column('btn_set', '$1', 'col_btnSet(id,menuCategoryID,isPack)');
        echo $this->datatables->generate();
    }

    /** Menu Setup : Load Data Table || Controller    */
    function loadMenuItem_setup_table()
    {
        $id = $this->input->post('menuCatID');
        $this->datatables->select('w.warehouseMenuID as warehouseMenuAutoID,w.isActive as wmActive,m.menuMasterID as id, m.menuCategoryID as menuCategoryID , m.menuImage as menuImage, m.menuMasterDescription as menuMasterDescription, m.menuCategoryID as menuCategoryID, m.sellingPrice  as sellingPrice , m.revenueGLAutoID as revenueGLAutoID, m.menuStatus as menuStatus, w.kotID as kotID, w.isTaxEnabled as isTaxEnabled, w.warehouseID as warehouseID, coa.GLDescription, w.isShortcut as isShortcut ', false)
            ->from('srp_erp_pos_warehousemenumaster  as w')
            ->join('srp_erp_pos_menumaster as m', 'm.menuMasterID = w.menuMasterID', 'left')
            ->join('srp_erp_chartofaccounts as coa', 'coa.GLAutoID = m.revenueGLAutoID', 'left')
            ->where('w.warehouseMenuCategoryID', $id)
            ->where('w.isDeleted', 0)
            ->where('m.isDeleted', 0)
            ->add_column('DT_RowId', 'menuRow_setup_$1', 'warehouseMenuAutoID')
            ->edit_column('status', '$1', 'get_active_status(wmActive)')
            ->edit_column('selPrice', '$1', 'format_number_dataTable(sellingPrice)')
            ->edit_column('menuImageOut', '$1', 'menuImage(menuImage)')
            ->edit_column('menu_isTaxEnabled', '$1', 'col_menu_isTaxEnabled(warehouseMenuAutoID,isTaxEnabled)')
            ->edit_column('kotID_tmp', '$1', 'kot_dropDown(warehouseMenuAutoID,kotID,warehouseID)')
            ->edit_column('btn_set', '$1', 'col_btnSet_setup(warehouseMenuAutoID,wmActive,0,isShortcut)');
        echo $this->datatables->generate();
    }

    /** warehouse Menu */
    function changeShortcut()
    {
        $warehouseMenuID = $this->input->post('autoID');
        $this->db->where('warehouseMenuID', $warehouseMenuID);
        $result = $this->db->update('srp_erp_pos_warehousemenumaster', array('isShortcut' => $this->input->post('checkedValue')));
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'updated'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error : ' . $this->db->_error_message()));
        }
    }


    function barcode_dup_check($value)
    {
        $search_string = $value;
        $result = $this->Pos_config_model->search_barcode($search_string);
        if ($value != "") {
            if (!empty($result)) {
                $message = 'This barcode <strong>' . $this->input->post('barcode') . '</strong> already set to <strong>' . $result[0]['menuMasterDescription'] . '</strong>';
                $this->form_validation->set_message('barcode_dup_check', $message);
                return FALSE;
            }
        }
    }

    /** Menu : Add or update || Controller  */
    function addMenu()
    {
        $this->form_validation->set_rules('menuMasterDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('menuStatus', 'Status', 'trim|required');
        //$this->form_validation->set_rules('sellingPrice', 'Selling Price', 'trim|required');
        $this->form_validation->set_rules('pricewithoutTax', 'Price Without Tax', 'trim|required');
        //$this->form_validation->set_rules('preparationTime', 'Preparation Time', 'trim|required');

        $this->form_validation->set_rules('barcode', 'Barcode', 'trim|xss_clean|callback_barcode_dup_check[' . $this->input->post('barcode') . ']');

        $pkId = $this->input->post('menuMasterID');
        $id = $this->input->post('menuCategoryID');
        $menuName = $this->input->post('menuMasterDescription');

        $fileExist = false;
        if (isset($_FILES['menuImage']['name']) && !empty($_FILES['menuImage']['name'])) {
            $fileExist = true;
        }

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));

        } else {
            if ($this->input->post('pricewithoutTax') > 0) {

                $path = 'uploads/';
                $tmpImagePath = $_FILES['menuImage']['name'];
                $ext = pathinfo($tmpImagePath, PATHINFO_EXTENSION);

                $config['upload_path'] = $path;
                $config['allowed_types'] = 'png|jpg|jpeg';
                $config['max_size'] = '200000';
                $config['file_name'] = 'pos_menu_' . '_' . time();


                /*$tmpData = $this->upload->data();*/
                $fileName = 'pos_menu_' . '_' . time() . '.' . $ext;


                if ($pkId == 0) {

                    $barcode = $this->input->post('barcode');
                    $this->db->select('*');
                    $this->db->from('srp_erp_pos_menumaster');
                    $this->db->where('barcode', $barcode);
                    $where = "barcode is  NOT NULL";
                    $this->db->where($where);
                    $result_barcode = $this->db->get()->row_array();
                    if (false) { //!empty($result_barcode) ||
                        echo json_encode(array('error' => 1, 'message' => 'Barcode <strong>' . $barcode . '</strong> already assigned for <strong>' . $result_barcode['menuMasterDescription'] . '</strong>'));
                        exit;
                    }


                    if ($fileExist) {
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                    }

                    /** Insert */
                    if ($fileExist && !$this->upload->do_upload("menuImage")) {
                        echo json_encode(array('error' => 1, 'message' => 'Upload failed ' . $this->upload->display_errors()));
                    } else {

                        $tmpConfig = $this->Pos_config_model->get_srp_erp_pos_menucategory_specific($id);


                        $data['menuImage'] = $fileExist ? 'uploads/' . $fileName : 'images/no-logo.png';
                        $data['menuMasterDescription'] = $menuName;
                        $data['menuStatus'] = $this->input->post('menuStatus');
                        // $data['preparationTime'] = $this->input->post('preparationTime');
                        $data['sellingPrice'] = $this->input->post('sellingPrice');
                        $data['pricewithoutTax'] = $this->input->post('pricewithoutTax');
                        $data['sellingPrice'] = $data['pricewithoutTax'];
                        $data['menuCategoryID'] = $id;
                        $data['menuSizeID'] = $this->input->post('menuSizeID');
                        $data['TAXpercentage'] = $this->input->post('TAXpercentage');
                        $data['taxMasterID'] = $this->input->post('taxMasterID');
                        $data['revenueGLAutoID'] = $tmpConfig['revenueGLAutoID'];
                        $data['barcode'] = $barcode;

                        $data['companyID'] = current_companyID();
                        $data['createdPCID'] = current_pc();
                        $data['createdUserID'] = current_companyID();
                        $data['createdDateTime'] = format_date_mysql_datetime();
                        $data['createdUserName'] = current_user();
                        $data['createdUserGroup'] = user_group();
                        $data['timeStamp'] = format_date_mysql_datetime();


                        $this->db->trans_start();
                        $insertedDetail = $this->Pos_config_model->addMenu($data);

                        $outlets = $this->input->post('outlets');

                        if (isset($outlets) && !empty($outlets)) {
                            $menuMasterID = $this->input->post('menuMasterID');
                            foreach ($outlets as $outletID) {

                                /** Create warehouse category is not exist */
                                $warehouseCategoryID = $this->Pos_config_model->create_warehouseMenuCategory($outletID, $id);

                                /** Create warehouse menu */
                                $data_WM['warehouseMenuCategoryID'] = $warehouseCategoryID;
                                $data_WM['menuMasterID'] = $insertedDetail['menuID'];
                                //$data_WM['menuCategoryID'] = $id;
                                $data_WM['warehouseID'] = $outletID;

                                $this->Pos_config_model->create_warehouseMenu($data_WM, $warehouseCategoryID, $menuMasterID, $outletID);

                            }
                        }


                        $packGroups = $this->input->post('packGroups');

                        if (isset($packGroups) && !empty($packGroups)) {
                            foreach ($packGroups as $menuID => $packGroupID) {

                                /** get pack group detail & New added menu information  */
                                $groupInfo = $this->Pos_config_model->get_menuPackGroupMaster_specific($packGroupID);
                                $menuInfo = $this->Pos_config_model->get_srp_erp_pos_menumaster_specific($insertedDetail['menuID']);

                                /** Create menu pack item */
                                $dataPackItem['menuID'] = $insertedDetail['menuID'];
                                $dataPackItem['PackMenuID'] = $groupInfo['packMenuID'];
                                $dataPackItem['menuCategoryID'] = $menuInfo['menuCategoryID'];
                                $menuPackItemID = $this->Pos_config_model->create_menuPackItem($dataPackItem);


                                /** Create pack group detail */
                                $dataGroup['groupMasterID'] = $packGroupID;
                                $dataGroup['packMenuID'] = $groupInfo['packMenuID'];
                                $dataGroup['menuID'] = $insertedDetail['menuID'];
                                $dataGroup['menuPackItemID'] = $menuPackItemID;
                                $this->Pos_config_model->create_packGropuDetail($dataGroup);
                            }
                        }


                        $this->db->trans_complete();
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                        } else {
                            $this->db->trans_commit();
                            $output = UPLOAD_PATH . "/portal/" . $fileName;
                            echo json_encode(array('error' => 0, 'message' => 'Successfully added', 'img' => $output, 'code' => $id, 'menuID' => $insertedDetail['menuID'], 'GLCode' => $tmpConfig['revenueGLAutoID'], 'insert' => 1));
                        }
                    }
                } else {

                    $barcode = $this->input->post('barcode');
                    $this->db->select('*');
                    $this->db->from('srp_erp_pos_menumaster');
                    $this->db->where('barcode', $barcode);
                    $this->db->where_not_in('menuMasterID', $pkId);
                    $result_barcode = $this->db->get()->row_array();
                    if (!empty($result_barcode) && false) {
                        echo json_encode(array('error' => 1, 'message' => 'Barcode <strong>' . $barcode . '</strong> already assigned for <strong>' . $result_barcode['menuMasterDescription'] . '</strong>'));
                        exit;
                    }

                    $this->db->trans_start();
                    if (isset($_FILES['menuImage']) && !empty($_FILES['menuImage']['name'])) {
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        $data['menuImage'] = 'uploads/' . $fileName;
                        $this->upload->do_upload("menuImage");
                    }

                    $data['menuMasterDescription'] = $menuName;
                    $data['menuStatus'] = $this->input->post('menuStatus');

                    if ($this->input->post('menuStatus') == "1") {
                        $masterLevelID = $this->input->post('menuCategoryID');

                        //Active under category sub category
                        $menu_subcat_id_array = $this->Pos_config_model->get_menumaster_subcat_tree_ids_by_menucategoryid($masterLevelID);
                        $this->menumaster_subcat_buildtree_by_menucategoryid($menu_subcat_id_array);

                        if (!empty($this->menumaster_subcat_up_ids)) {
                            foreach ($this->menumaster_subcat_up_ids as $menu_subcat_id) {
                                $menu_subcat_data['isActive'] = 1;
                                $this->Pos_config_model->updateMenuCategory($menu_subcat_data, $menu_subcat_id);
                            }
                        }


                        $MenuCategoryData['isActive'] = '1';
                        $this->Pos_config_model->updateMenuCategory($MenuCategoryData, $id);
                    }

                    $data['pricewithoutTax'] = $this->input->post('pricewithoutTax');

                    //$data['sellingPrice'] = $this->input->post('sellingPrice'); { priceWithoutTax + totalServiceCharge + totalTaxAmount}
                    $tmpSellingPrice = $data['pricewithoutTax'];

                    $data['menuSizeID'] = $this->input->post('menuSizeID');
                    $data['TAXpercentage'] = $this->input->post('TAXpercentage');
                    $data['taxMasterID'] = $this->input->post('taxMasterID');
                    $data['revenueGLAutoID'] = $this->input->post('revenueGLAutoID');
                    $data['modifiedPCID'] = current_pc();
                    $data['modifiedUserID'] = current_companyID();
                    $data['modifiedDateTime'] = format_date_mysql_datetime();
                    $data['modifiedUserName'] = current_user();


                    $taxArray = $this->input->post('taxAmount');
                    $taxPercentage = $this->input->post('taxPercentage');
                    if (isset($taxArray) && !empty($taxArray)) {
                        $tmpTaxTotal = 0;
                        $i = 0;
                        foreach ($taxArray as $key => $taxAmount) {
                            $data_tax[$i]['menutaxID'] = $key;
                            $data_tax[$i]['taxPercentage'] = $taxPercentage[$key];
                            $data_tax[$i]['taxAmount'] = $taxAmount;
                            $tmpTaxTotal += $taxAmount;
                            $i++;
                        }
                        if (!empty($data_tax)) {
                            $this->db->update_batch('srp_erp_pos_menutaxes', $data_tax, 'menutaxID');
                            $data['totalTaxAmount'] = $tmpTaxTotal;
                            $tmpSellingPrice += $tmpTaxTotal;
                        }
                    }


                    $scArray = $this->input->post('serviceChargeAmount');
                    $serviceChargePercentage = $this->input->post('serviceChargePercentage');

                    if (isset($scArray) && !empty($scArray)) {
                        $i = 0;
                        $tmpSCTotal = 0;
                        foreach ($scArray as $key => $SC_Amount) {
                            $data_SC[$i]['menuServiceChargeID'] = $key;
                            $data_SC[$i]['serviceChargePercentage'] = $serviceChargePercentage[$key];
                            $data_SC[$i]['serviceChargeAmount'] = $SC_Amount;
                            $tmpSCTotal += $SC_Amount;
                            $tmpSellingPrice += $SC_Amount;
                            $i++;
                        }

                        if (!empty($data_SC)) {
                            $data['totalServiceCharge'] = $tmpSCTotal;
                            $this->db->update_batch('srp_erp_pos_menuservicecharge', $data_SC, 'menuServiceChargeID');
                        }
                    }

                    $outlets = $this->input->post('outlets');
                    if (isset($outlets) && !empty($outlets)) {

                        $outletIdStr = implode(", ", $outlets);
                        $menuMasterID = $this->input->post('menuMasterID');
                        //delete items not in the list
                        //$this->db->query("update srp_erp_pos_warehousemenucategory set isDeleted=1  where menuCategoryID=$id and warehouseID not in ($outletIdStr)");
                        $this->db->query("update srp_erp_pos_warehousemenumaster set isDeleted=1  where menuMasterID=$menuMasterID and warehouseID not in ($outletIdStr)");

                        foreach ($outlets as $outletID) {

                            /** Create warehouse category is not exist */
                            $warehouseCategoryID = $this->Pos_config_model->create_warehouseMenuCategory($outletID, $id);

                            /** Create warehouse menu */
                            $data_WM['warehouseMenuCategoryID'] = $warehouseCategoryID;
                            $data_WM['menuMasterID'] = $menuMasterID;
                            //$data_WM['menuCategoryID'] = $id;
                            $data_WM['warehouseID'] = $outletID;

                            $this->Pos_config_model->create_warehouseMenu($data_WM, $warehouseCategoryID, $menuMasterID, $outletID);

                        }
                    } else {
                        $menuMasterID = $this->input->post('menuMasterID');
                        //delete all items
                        //$this->db->query("update srp_erp_pos_warehousemenucategory set isDeleted=1  where menuCategoryID=$id");
                        $this->db->query("update srp_erp_pos_warehousemenumaster set isDeleted=1  where menuMasterID=$menuMasterID");
                    }


                    $data['sellingPrice'] = $tmpSellingPrice;
                    $data['barcode'] = $this->input->post('barcode');
                    $data['preparationTime'] = $this->input->post('preparationTime');

                    $result = $this->Pos_config_model->updateMenu($data, $pkId);
                    if ($result) {
                        $this->updateStatusToAllOutlet($pkId);
                    }

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                    } else {
                        $this->db->trans_commit();
                        echo json_encode(array('error' => 0, 'message' => 'Record updated Successfully', 'code' => $id, 'menuID' => $pkId, 'insert' => 0));
                    }
                }
            } else {
                echo json_encode(array('error' => 1, 'message' => 'Please type price without Tax Amount.'));
            }
        }
    }

    function copy_menus_to_warehouse()
    {
        //exit;
        $fromID = $this->input->post('fromID');
        $toID = $this->input->post('toID');
        $companyID = current_companyID();

        $query1 = $this->db->query("SELECT * FROM `srp_erp_pos_warehousemenucategory` where
warehouseID = $fromID and isActive=1 and isDeleted=0 and companyID=$companyID");

        foreach ($query1->result() as $item1) {
            $warehouseCategoryID = $item1->autoID;
            $query2 = $this->db->query("SELECT * FROM `srp_erp_pos_warehousemenumaster`
where warehouseMenuCategoryID=$warehouseCategoryID and isActive=1 and isDeleted=0 and companyID=$companyID");

            if ($query2->num_rows() > 0) {
                //checking requirment to create a new srp_erp_pos_warehousemenucategory
                $isAllItemsAlreadyThere = true;//default true; this will false even if one item not there.
                foreach ($query2->result() as $item2) {
                    $menuMasterID = $item2->menuMasterID;
                    //checking whether the menu item already there in destination warehouse.
                    $query3 = $this->db->query("SELECT * FROM `srp_erp_pos_warehousemenumaster`
where warehouseID=$toID and menuMasterID=$menuMasterID and isActive=1 and isDeleted=0 and companyID=$companyID");
                    if ($query3->num_rows() > 0) {
                        //nothing to do
                    } else {
                        $isAllItemsAlreadyThere = false;
                    }
                }
                $menuCategoryID = $item1->menuCategoryID;
                $isWarehouseMenuCategoryExistQuery = $this->db->query("SELECT * FROM `srp_erp_pos_warehousemenucategory` where
warehouseID = $toID and menuCategoryID=$menuCategoryID and companyID=$companyID and isActive=1 and isDeleted=0");
                if ($isAllItemsAlreadyThere == false) {

                    if ($isWarehouseMenuCategoryExistQuery->num_rows() > 0) {
                        //warehousemenucategory already there.
                        $warehousemenucategoryIDForWarehouseTo = $isWarehouseMenuCategoryExistQuery->row()->autoID;
                    } else {
                        //adding a new srp_erp_pos_warehousemenucategory
                        $warehousemenucategory = array(
                            "menuCategoryID" => $item1->menuCategoryID,
                            "warehouseID" => $toID,
                            "companyID" => $item1->companyID,
                            "isActive" => 1,
                            "isDeleted" => 0,
                            "createdPCID" => current_pc(),
                            "createdUserID" => current_userID(),
                            "createdDateTime" => format_date_mysql_datetime(),
                            "createdUserName" => current_user(),
                            "createdUserGroup" => user_group()
                        );
                        $this->db->insert('srp_erp_pos_warehousemenucategory', $warehousemenucategory);
                        $warehousemenucategoryIDForWarehouseTo = $this->db->insert_id();
                    }


                    //inserting data to srp_erp_pos_warehousemenumaster as neccessary.
                    foreach ($query2->result() as $item2) {
                        $menuMasterID = $item2->menuMasterID;
                        //checking whether the menu item already there in destination warehouse.
                        $query3 = $this->db->query("SELECT * FROM `srp_erp_pos_warehousemenumaster`
where warehouseID=$toID and menuMasterID=$menuMasterID and isActive=1 and isDeleted=0 and companyID=$companyID");
                        if ($query3->num_rows() > 0) {
                            //nothing to do
                        } else {
                            $warehousemenumaster = array(
                                "warehouseID" => $toID,
                                "menuMasterID" => $menuMasterID,
                                "warehouseMenuCategoryID" => $warehousemenucategoryIDForWarehouseTo,
                                "companyID" => $item1->companyID,
                                "isActive" => 1,
                                "isDeleted" => 0,
                                "createdPCID" => current_pc(),
                                "createdUserID" => current_userID(),
                                "createdDateTime" => format_date_mysql_datetime(),
                                "createdUserName" => current_user(),
                                "createdUserGroup" => user_group()
                            );
                            $this->db->insert('srp_erp_pos_warehousemenumaster', $warehousemenumaster);
                        }
                    }
                }

            } else {

                $menuCategoryID = $item1->menuCategoryID;
                $companyID = $item1->companyID;
                $query4 = $this->db->query("SELECT * FROM `srp_erp_pos_warehousemenucategory` where
warehouseID = $toID and menuCategoryID=$menuCategoryID and companyID=$companyID and isActive=1 and isDeleted=0");
                if ($query4->num_rows() > 0) {
                    //nothing to do because of the record already there.
                } else {
                    //adding a new srp_erp_pos_warehousemenucategory
                    $warehousemenucategory = array(
                        "menuCategoryID" => $item1->menuCategoryID,
                        "warehouseID" => $toID,
                        "companyID" => $item1->companyID,
                        "isActive" => 1,
                        "isDeleted" => 0,
                        "createdPCID" => current_pc(),
                        "createdUserID" => current_userID(),
                        "createdDateTime" => format_date_mysql_datetime(),
                        "createdUserName" => current_user(),
                        "createdUserGroup" => user_group()
                    );
                    $this->db->insert('srp_erp_pos_warehousemenucategory', $warehousemenucategory);
                }

            }

        }

        $data['status'] = 1;
        echo json_encode($data);

    }

    function updateStatusToAllOutlet($menuID)
    {
        $companyId = current_companyID();
        $menuStatus = $this->input->post('menuStatus');
        $data = array('isActive' => $menuStatus);
        $this->db->where('menuMasterID', $menuID);
        $this->db->where('companyID', $companyId);
        return $this->db->update('srp_erp_pos_warehousemenumaster', $data);
    }

    function getEditMenuInfo()
    {
        $id = $this->input->post('id');
        $output = $this->Pos_config_model->get_srp_erp_pos_menumaster_specific($id);
        if (!empty($output)) {
            echo json_encode(array_merge($output, array('error' => 0, 'message' => 'done')));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'An error has occurred, please contact your system support team.'));
        }
    }

    /** Menu Delete */
    function deleteMenu()
    {
        $id = $this->input->post('id');
        /*get master record */
        $master = $this->Pos_config_model->get_srp_erp_pos_menumaster_specific($id);

        /*check the menu has assigned to warehouse*/
        $this->db->select('menuMasterID');
        $this->db->from('srp_erp_pos_warehousemenumaster');
//        $this->db->where('wareHouseID',get_outletID());
        $this->db->where('isDeleted', 0);
        $this->db->where('menuMasterID', $id);
        $count = $this->db->get()->num_rows();

        if (!empty($master) && $count == 0) {
            $this->db->trans_begin();
            $data['isDeleted'] = 1;
            $data['deletedBy'] = current_userID();
            $data['deletedDatetime'] = format_date_mysql_datetime();

            $this->Pos_config_model->updateMenu($data, $id);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('error' => 1, 'message' => '<strong>Error</strong><br/><br/> Error while deleting.'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('error' => 0, 'message' => '<strong>Record Deleted</strong>'));
            }

        } else {
            echo json_encode(array('error' => 1, 'message' => 'Menu can not be deleted!'));
        }
    }

    /** Menu Setup Delete */
    function deleteMenu_setup()
    {
        $id = $this->input->post('warehouseMenuID');
        /*get master record */
        $master = $this->Pos_config_model->get_srp_erp_pos_warehousemenumaster($id);

        if (!empty($master)) {
            $this->db->trans_begin();
            $data['isDeleted'] = 1;
            $data['deletedBy'] = current_userID();
            $data['deletedDatetime'] = format_date_mysql_datetime();

            $this->Pos_config_model->update_srp_erp_pos_warehousemenumaster($data, $id);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('error' => 1, 'message' => '<strong>Error</strong><br/><br/> Error while deleting.'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('error' => 0, 'message' => '<strong>Record Deleted</strong>'));
            }

        } else {
            echo json_encode(array('error' => 1, 'message' => 'Record not found!'));
        }
    }

    function update_Menue_Category_Isactive()
    {
        echo json_encode($this->Pos_config_model->update_Menue_Category_Isactive());
    }

    function delete_menue_Category()
    {
        $id = $this->input->post('autoID');
        /*get master record */
        $master = $this->Pos_config_model->get_srp_erp_pos_warehousemenucategory($id);

        if (!empty($master)) {
            $this->db->trans_begin();
            $data['isDeleted'] = 1;
            $data['deletedBy'] = current_userID();
            $data['deletedDatetime'] = format_date_mysql_datetime();

            $this->Pos_config_model->update_srp_erp_pos_warehousemenucategory($data, $id);
            $this->Pos_config_model->update_srp_erp_pos_warehousemenumenu($data, $id); /*warehouseMenuCategoryID*/

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('error' => 1, 'message' => '<strong>Error</strong><br/><br/> Error while deleting.'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('error' => 0, 'message' => '<strong>Record Deleted</strong>'));
            }

        } else {
            echo json_encode(array('error' => 1, 'message' => 'Record not found!'));
        }
    }

    function update_Menue_Master_Isactive()
    {
        echo json_encode($this->Pos_config_model->update_Menue_Master_Isactive());
    }


    function loadMenuDetail_table()
    {
        echo json_encode($this->Pos_config_model->loadMenuDetail_table());
    }

    function load_default_uom()
    {
        echo json_encode($this->Pos_config_model->load_default_uom());
    }

    function loadMenuDetail()
    {
        $id = $this->input->post('categoryID');

        $data['id'] = $id;
        $data['category'] = $this->Pos_config_model->get_srp_erp_pos_menumaster_specific($id);
        $this->load->view('system/pos/ajax/srp_erp_pos_menudetails_view', $data);
    }

    function save_menu_details()
    {
        $this->form_validation->set_rules('menuDetailDescription', 'Menu Detail Description', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules('UOM', 'UOM', 'trim|required');
        $this->form_validation->set_rules('qty', 'qty', 'trim|required');
        $this->form_validation->set_rules('cost', 'cost', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->save_menu_details());
        }
    }

    function load_menu_detail_edit()
    {
        echo json_encode($this->Pos_config_model->load_menu_detail_edit());
    }


    function loadRooms_table()
    {
        $id = $this->input->post('wareHouseAutoID');
        $this->datatables->select('diningRoomMasterID as id,diningRoomDescription,wareHouseAutoID ', false)
            ->from('srp_erp_pos_diningroommaster')
            ->where('wareHouseAutoID', $id)
            ->edit_column('edit', '$1', 'col_posConfig_rooms(id,wareHouseAutoID)');
        echo $this->datatables->generate();
    }

    function save_rooms_info()
    {
        $this->form_validation->set_rules('diningRoomDescription', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->save_rooms_info());
        }
    }

    function edit_pos_room_config()
    {
        echo json_encode($this->Pos_config_model->edit_pos_room_config());
    }

    function delete_pos_room_config()
    {
        echo json_encode($this->Pos_config_model->delete_pos_room_config());
    }

    function loadTables_table()
    {
        $id = $this->input->post('diningRoomMasterID');
        $this->datatables->select('diningTableAutoID as id,diningTableDescription,noOfSeats,diningRoomMasterID ', false)
            ->from('srp_erp_pos_diningtables')
            ->where('diningRoomMasterID', $id)
            ->edit_column('edit', '$1', 'col_posConfig_tables(id,diningRoomMasterID)');
        echo $this->datatables->generate();
    }

    function save_tables_info()
    {
        $this->form_validation->set_rules('diningTableDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('noOfSeats', 'No Of Seats', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->save_tables_info());
        }
    }

    function edit_pos_table_config()
    {
        echo json_encode($this->Pos_config_model->edit_pos_table_config());
    }

    function delete_pos_table_config()
    {
        echo json_encode($this->Pos_config_model->delete_pos_table_config());
    }

    function delete_pos_menu_detail()
    {
        echo json_encode($this->Pos_config_model->delete_pos_menu_detail());
    }

    function loadCrew_table()
    {
        $id = $this->input->post('segmentConfigID');
        $this->datatables->select('crewMemberID as id,crewFirstName,crewLastName,EIdNo,srp_erp_pos_crewroles.roleDescription,wareHouseAutoID, segmentConfigID ', false)
            ->from('srp_erp_pos_crewmembers');
        $this->datatables->join('srp_erp_pos_crewroles', 'srp_erp_pos_crewmembers.crewRoleID = srp_erp_pos_crewroles.crewRoleID', 'left')
            ->where('segmentConfigID', $id)
            ->edit_column('edit', '$1', 'col_posConfig_Crews(id,segmentConfigID)');
        echo $this->datatables->generate();
    }

    function loadKitchenLocation_table()
    {
        $segmentConfigID = $this->input->post('outletID');
        $this->db->select('wareHouseAutoID');
        $this->db->from('srp_erp_pos_segmentconfig');
        $this->db->where('segmentConfigID', $segmentConfigID);
        $outletID = $this->db->get()->row('wareHouseAutoID');

        $this->datatables->select('kitchenLocationID, description, companyID, outletID', false)
            ->from('srp_erp_pos_kitchenlocation')
            ->where('outletID', $outletID)
            ->where('companyID', current_companyID())
            ->edit_column('edit', '$1', 'col_posConfig_kot(kitchenLocationID)');

        echo $this->datatables->generate();
        //echo $this->db->last_query();
    }

    function loadCrewRoles_table()
    {
        $this->datatables->select('crewRoleID as id,roleDescription,companyID,isWaiter ', false)
            ->from('srp_erp_pos_crewroles')
            ->where('companyID', current_companyID())
            ->edit_column('edit', '$1', 'col_posConfig_crewRoles(id)')
            ->edit_column('isWaiter_tmp', '$1', 'col_isWaiter(isWaiter)');
        echo $this->datatables->generate();
    }

    function save_crew_role_info()
    {
        $this->form_validation->set_rules('roleDescription', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->save_crew_role_info());
        }
    }

    function edit_pos_crew_roles_config()
    {
        echo json_encode($this->Pos_config_model->edit_pos_crew_roles_config());
    }

    function delete_pos_crew_roles_config()
    {
        echo json_encode($this->Pos_config_model->delete_pos_crew_roles_config());
    }

    function save_crew_info()
    {
        $this->form_validation->set_rules('crewFirstName', 'First Name', 'trim|required');
        //$this->form_validation->set_rules('crewLastName', 'Last Name', 'trim|required');
        //$this->form_validation->set_rules('EIdNo', 'Employee ID', 'trim|required');
        $this->form_validation->set_rules('crewRoleID', 'Role', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->save_crew_info());
        }
    }

    function edit_pos_crew_config()
    {
        echo json_encode($this->Pos_config_model->edit_pos_crew_config());
    }

    function delete_pos_crew_config()
    {
        echo json_encode($this->Pos_config_model->delete_pos_crew_config());
    }


    function posGL_config()
    {
        $this->form_validation->set_rules('glAutoID', 'Account Name', 'trim|required');
        $this->form_validation->set_rules('paymentTypeID', 'Payment', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_config_model->posGL_config());
        }
    }


    function fetch_menuitemfor_menucategory()
    {
        echo json_encode($this->Pos_config_model->fetch_menuitem_for_warehouse_category());
        //echo json_encode($this->Pos_config_model->fetch_menuitemfor_menucategory());
    }

    function updateIsPaxValue()
    {
        $id = $this->input->post('id');
        $where = $this->input->post('where');
        $paxValue = $this->input->post('paxValue');
        $data['isPack'] = $paxValue;

        if ($where == 'mc') {
            /**
             * mc = Menu Category
             * update Menu Item for Pax value
             */
            $this->Pos_config_model->updateMenuCategory($data, $id);
            $this->Pos_config_model->updateMenu_byCategoryID($data, $id);
            echo json_encode(array('error' => 0, 'message' => 'Pax Updated to Category & Sub Menu'));
        } else {
            $extraID = $this->input->post('extraID');
            if ($paxValue == 1) {
                $this->Pos_config_model->updateMenuCategory($data, $extraID);
            }
            $this->Pos_config_model->updateMenu($data, $id);
            echo json_encode(array('error' => 0, 'message' => 'Pax Updated', 'tmp' => $paxValue));
        }


    }

    function update_showImageYN()
    {
        $id = $this->input->post('id');
        $where = $this->input->post('where');
        $inputValue = $this->input->post('inputValue');
        $data['showImageYN'] = $inputValue;

        $status = $inputValue == 1 ? 'On' : 'Off';
        //mc = Menu Category

        if ($where == 'mc') {
            $this->Pos_config_model->updateMenuCategory($data, $id);
            echo json_encode(array('error' => 0, 'message' => 'Category Image switched ' . $status));
        } else {
            $this->Pos_config_model->updateMenu($data, $id);
            echo json_encode(array('error' => 0, 'message' => 'Menu Image switched ' . $status, 'tmp' => $inputValue));
        }


    }

    function updateIsVegValue()
    {
        $id = $this->input->post('id');
        $where = $this->input->post('where');
        $vegValue = $this->input->post('vegValue');
        $data['isVeg'] = $vegValue;

        $extraID = $this->input->post('extraID');

        $this->Pos_config_model->updateMenu($data, $id);
        echo json_encode(array('error' => 0, 'message' => 'Updated', 'tmp' => $vegValue));

    }

    function packConfig_modal_data()
    {
        $id = $this->input->post('id');
        $data['master'] = $this->Pos_config_model->get_srp_erp_pos_menumaster_specific($id);
        $this->load->view('system/pos/ajax/ajax-pos-packConfig_modal_data', $data);
    }

    function packLoadItmDetail()
    {
        $id = $this->input->post('id');
        $result = $this->Pos_config_model->get_srp_erp_pos_menumaster_specific($id);
        if (!empty($result)) {
            echo json_encode(array_merge(array('error' => 0, 'message' => 'done'), $result));
        } else {
            echo json_encode(array_merge(array('error' => 1, 'message' => 'record not found')));
        }
    }


    function save_packItems()
    {
        $this->form_validation->set_rules('pack_item', 'Item', 'trim|required');
        $this->form_validation->set_rules('PackMenuID', 'Item', 'trim|required');
        $this->form_validation->set_rules('isRequired', 'Item', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {

            $validate = $this->Pos_config_model->validate_posConfig();
            if ($validate) {

                $currentTime = format_date_mysql_datetime();
                $pc = current_pc();
                $user = current_userID();

                $menuCategoryID = $this->input->post('menuCategoryID');
                $PackMenuID = $this->input->post('PackMenuID');
                $pack_item = $this->input->post('pack_item');
                $isRequired = $this->input->post('isRequired');
                $output = $this->Pos_config_model->pack_itemExist($PackMenuID, $pack_item);


                if (empty($output)) {
                    /* Menu Pack Item */
                    $data = array(
                        "PackMenuID" => $PackMenuID,
                        "menuID" => $pack_item,
                        "menuCategoryID" => $menuCategoryID,
                        "isRequired" => $isRequired,
                        "createdBy" => $user,
                        "createdPC" => $pc,
                        "createdDatetime" => $currentTime,
                        "timestamp" => $currentTime
                    );

                    $menuPackItemID = $this->Pos_config_model->insert_srp_erp_pos_menupackitem($data);


                    if ($isRequired == 1) {
                        /*check new category exist */
                        $description = 'Required';

                        $r = $this->Pos_config_model->get_srp_erp_pos_menupackgroupmaster($PackMenuID, $description);
                        if (empty($r)) {
                            /*insert group and detail */

                            /*insert group */
                            $data = array(
                                "description" => 'Required',
                                "packMenuID" => $PackMenuID,
                                "qty" => 0,
                                "IsRequired" => 1,
                                "createdBy" => $user,
                                "createdPc" => $pc,
                                "createdDatetime" => $currentTime,
                                "timestamp" => $currentTime
                            );
                            $groupMasterID = $this->Pos_config_model->insert_srp_erp_pos_menupackgroupmaster($data);

                            /* insert detail  */
                            $data2['groupMasterID'] = $groupMasterID;
                            $data2['packMenuID'] = $PackMenuID;
                            /** Pack or combo menu ID*/
                            $data2['menuID'] = $pack_item; //$menuPackItemID
                            $data2['menuPackItemID'] = $menuPackItemID; //$menuPackItemID
                            /** selected menu item ID */
                            $data2['createdBy'] = $user;
                            $data2['createdPc'] = $pc;
                            $data2['createdDatetime'] = $currentTime;
                            $data2['timestamp'] = $currentTime;

                            $this->Pos_config_model->insert_srp_erp_pos_packgroupdetail($data2);

                        } else {

                            /*insert detail only */
                            /* insert detail  */
                            $data2['groupMasterID'] = $r['groupMasterID'];
                            $data2['packMenuID'] = $PackMenuID;
                            /** Pack or combo menu ID*/
                            $data2['menuID'] = $pack_item;
                            $data2['menuPackItemID'] = $menuPackItemID;
                            /** selected menu item ID */
                            $data2['createdBy'] = $user;
                            $data2['createdPc'] = $pc;
                            $data2['createdDatetime'] = $currentTime;
                            $data2['timestamp'] = $currentTime;

                            $this->Pos_config_model->insert_srp_erp_pos_packgroupdetail($data2);
                        }
                    }

                    /* Menu Pack Category */
                    /*
                    $qty = $this->input->post('qty');
                    $result = $this->Pos_config_model->pack_categoryExist($menuCategoryID, $PackMenuID);
                    if ($qty > 0) {
                        if (empty($result)) {
                            $data2 = array(
                                "valuePackID" => $PackMenuID,
                                "menuCategoryID" => $menuCategoryID,
                                "qty" => $this->input->post('qty'),
                                "createdDatetime" => format_date_mysql_datetime(),
                                "createdBy" => current_userID(),
                                "createdPc" => current_pc(),
                                "timestamp" => format_date_mysql_datetime(),
                            );
                            $this->Pos_config_model->insert_srp_erp_pos_menupackcategory($data2);
                        } else {
                            $updateData['qty'] = $this->input->post('qty');
                            $this->Pos_config_model->update_srp_erp_pos_menupackcategory($updateData, $result['menuPackCategoryID']);
                        }
                    }*/

                    echo json_encode(array('error' => 0, 'message' => 'Pack item added', 'code' => $PackMenuID));

                } else {
                    echo json_encode(array('error' => 1, 'message' => 'Item already exist in this pack'));
                }
            } else {
                echo json_encode(array('error' => 1, 'message' => 'This record is already exit'));
            }
        }
    }

    function load_packItem_table()
    {
        $id = $this->input->post('PackMenuID');

        $this->datatables->select('packItem.menuPackItemID as id, menuMaster.menuMasterDescription , menuCategory.menuCategoryDescription , if(packItem.isRequired=0,"Yes","No") as isRequired', false)
            ->from('srp_erp_pos_menupackitem packItem')
            ->join('srp_erp_pos_menumaster menuMaster', 'menuMaster.menuMasterID = packItem.menuID', 'left')
            ->join('srp_erp_pos_menucategory menuCategory', 'menuCategory.menuCategoryID = packItem.menuCategoryID', 'left')
            ->where('PackMenuID', $id)
            ->add_column('DT_RowId', 'packItemTbl_$1', 'id')
            ->edit_column('edit', '$1', 'col_pos_packItem(id)');
        echo $this->datatables->generate();
        //$this->db->last_query();
    }

    function load_packItem_table2()
    {
        $id = $this->input->post('PackMenuID');
        $data['list'] = $this->Pos_config_model->get_pack_itemList_group($id);
        $this->load->view('system/pos/ajax/ajax-pos-pack-group-list', $data);
    }

    function load_packGroup_table()
    {
        $id = $this->input->post('packMenuID');
        $this->datatables->select('groupMasterID as id, description, qty, IsRequired ', false)
            ->from('srp_erp_pos_menupackgroupmaster')
            ->where('packMenuID', $id)
            ->add_column('DT_RowId', 'packGroup_$1', 'id')
            ->edit_column('inputNum', '$1', 'packGroupQty(id,qty,IsRequired)')
            ->edit_column('edit', '$1', 'col_pos_packItem(id)');
        echo $this->datatables->generate();
    }

    function delete_pos_packItem()
    {
        $id = $this->input->post('id');
        $menuID = $menuPackItemID = $this->input->post('menuID');
        $packGroupDetailID = $this->input->post('packGroupDetailID');

        /*check menu is already exist in the sales tables : $menuID srp_erp_pos_valuepackdetail */


        $isExist = $this->Pos_config_model->isExistIn_srp_erp_pos_valuepackdetail($packGroupDetailID);
        if ($isExist) {
            echo json_encode(array('error' => 1, 'message' => 'Item can not be deleted, item is already added to sales'));
            exit;
        }

        $result = $this->Pos_config_model->delete_pos_packItem($menuID);

        /*delete assigned items */
        $this->Pos_config_model->delete_srp_erp_pos_packgroupdetail($id, $menuID);

        /*get pack group detail and delete pack group detail */
        $this->Pos_config_model->delete_srp_erp_pos_packgroupdetail_menuPackItemID($menuPackItemID);

        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'record deleted'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error while deleting, please contact your system support team.'));
        }
    }

    function delete_pos_packItemCategory()
    {
        $id = $this->input->post('id');
        $result = $this->Pos_config_model->delete_pos_packItemCategory($id);
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'record deleted'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error while deleting, please contact your system support team.'));
        }
    }

    function load_pack_category()
    {
        $id = $this->input->post('valuePackID');
        $this->datatables->select('packCategory.menuPackCategoryID as id, category.menuCategoryDescription as menuCategoryDescription,  packCategory.qty as qty', false)
            ->from('srp_erp_pos_menupackcategory packCategory')
            ->join('srp_erp_pos_menucategory category', 'category.menuCategoryID = packCategory.menuCategoryID', 'left')
            ->where('valuePackID', $id)
            ->add_column('DT_RowId', 'packItemCategoryTbl_$1', 'id')
            ->edit_column('edit', '$1', 'col_pos_packItemCategory(id)')
            ->edit_column('noOfItems', '$1', 'packNoOfItems_update(id,qty)');
        echo $this->datatables->generate();
    }

    function update_pack_noOfItems()
    {
        $noOfItem = $this->input->post('noOfItem');
        $id = $this->input->post('id');

        $data['qty'] = $noOfItem;

        $result = $this->Pos_config_model->get_srp_erp_pos_packgroupdetail_by_groupMasterID($id);


        if (count($result) >= $data['qty'] || true) {
            $this->Pos_config_model->update_srp_erp_pos_menupackcategory($data, $id);
            echo json_encode(array('error' => 0, 'message' => 'updated.... '));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'This Group contains only ' . count($result) . ' items, you can not select more than that'));
        }


    }

    function save_menu_item()
    {
        $this->form_validation->set_rules('menuItem', 'Description', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            $warehouseIDmenuitem = $this->input->post('warehouseIDmenuitem');
            $result = $this->Pos_config_model->get_srp_erp_pos_segmentconfig_specific($warehouseIDmenuitem);

            $data['warehouseMenuCategoryID'] = $this->input->post('wcAutoId');
            $data['menuMasterID'] = $this->input->post('menuItem');
            $data['warehouseID'] = $result['wareHouseAutoID'];

            $validate = $this->Pos_config_model->validate_warehouseItem($data);
            if ($validate) {
                echo json_encode($this->Pos_config_model->save_menu_item($data));
            } else {
                echo json_encode(array('error' => 1, 'message' => 'This menu is already exist!'));
            }

        }
    }


    function loadCompanyOutlets()
    {
        $this->datatables->select('srp_erp_warehousemaster.wareHouseAutoID as wareHouseAutoID,srp_erp_warehousemaster.companyCode as companyCode,wareHouseCode,wareHouseDescription,wareHouseLocation, IF(srp_erp_warehousemaster.isActive=1,"<span class=\'label label-success\'>Active</span>","<span class=\'label label-default\'>In-Active</span>") as outletStatus, warehouseAddress as address')
            ->from('srp_erp_warehousemaster')
            ->join('srp_erp_pos_segmentconfig', 'srp_erp_pos_segmentconfig.wareHouseAutoID = srp_erp_warehousemaster.wareHouseAutoID', 'left')
            ->where('srp_erp_warehousemaster.companyID', $this->common_data['company_data']['company_id'])
            ->where('isPosLocation', 1)
            ->where('( (srp_erp_pos_segmentconfig.isGeneralPOS=0) OR (srp_erp_pos_segmentconfig.isGeneralPOS IS NULL))')
            //$this->datatables->join('srp_countrymaster', 'srp_erp_suppliermaster.supplierCountryID = srp_countrymaster.countryID', 'left')
            ->edit_column('action', '<span class="pull-right" onclick="openwarehousemastermodel($1)"><a href="#" ><span class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a></span>', 'wareHouseAutoID');

        $output = $this->datatables->generate();
        //echo $this->db->last_query();
        echo $output;
    }

    function save_outlet()
    {


        if (!$this->input->post('warehouseredit')) {
            $this->form_validation->set_rules('warehousecode', 'Warehouse Code', 'trim|required|is_unique[srp_erp_warehousemaster.wareHouseCode]');
        }
        $this->form_validation->set_rules('warehousedescription', 'Warehouse Description', 'trim|required');
        $this->form_validation->set_rules('warehouselocation', 'Warehouse Location', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error));
        } else {
            $result = $this->Pos_config_model->save_outlet();
            /*if ($result) {
                echo json_encode(array('error' => 0, 'message' => 'Successfully updated'));
            } else {
                echo json_encode(array('error' => 1, 'message' => 'Error while updating the outlet'));
            }*/
            echo json_encode($result);
        }

    }

    function saveMenuSize()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('code', 'Code', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {

            $menuSizeID = $this->input->post('menuSizeID');
            $data['description'] = $this->input->post('description');
            $data['code'] = $this->input->post('code');
            $data['colourCode'] = $this->input->post('colourCode');
            $data['isActive'] = $this->input->post('isActive');
            $data['companyID'] = current_companyID();

            if ($menuSizeID) {
                /* update */

                $result = $this->Pos_config_model->update_srp_erp_pos_menusize($menuSizeID, $data);
                if ($result) {
                    echo json_encode(array('error' => 0, 'message' => 'updated'));
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'Error while updating, Please contact your system support team'));
                }
            } else {
                /* insert */


                $data['timestamp'] = $this->input->post('timestamp');
                $result = $this->Pos_config_model->save_srp_erp_pos_menusize($data);
                if ($result) {
                    echo json_encode(array('error' => 0, 'message' => 'successfully saved'));
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'Error while insert, Please contact your system support team'));
                }
            }


        }
    }

    function delete_menuSize()
    {
        $id = $this->input->post('id');
        $result = $this->Pos_config_model->validate_srp_erp_pos_menusize($id);

        if ($result) {
            /* delete */
            $result = $this->Pos_config_model->delete_srp_erp_pos_menusize($id);
            if ($result) {
                $tmp = array('error' => 0, 'message' => 'Record deleted');
            } else {
                $tmp = array('error' => 1, 'message' => 'error while deleting!, please contact your support team',);
            }
        } else {
            $tmp = array('error' => 1, 'message' => '<strong>Size can not be deleted! </strong><br/>This size is already assigned to a menu.');
        }
        echo json_encode($tmp);
    }

    function fetch_menu_size()
    {
        $this->datatables->select('menuSizeID,description, code,colourCode', false)
            ->from('srp_erp_pos_menusize')
            ->where('companyID', current_companyID())
            ->where('isActive', 1)
            ->edit_column('action', '$1', 'load_edit_menu_size(menuSizeID)')
            ->edit_column('menuSizeColor', '$1', 'load_color_menu_size(colourCode)');
        echo $this->datatables->generate();
    }

    function edit_menu_size()
    {
        if ($this->input->post('menuSizeID') != "") {
            echo json_encode($this->Pos_config_model->edit_menu_size());
        } else {
            echo json_encode(FALSE);
        }
    }

    function loadGropingContent()
    {
        $id = $this->input->post('menuID');
        $data['menuItemList'] = $this->Pos_config_model->get_optionalMenuPackItem($id);
        $this->load->view('system/pos/ajax/ajax-pos-modal-menu-pack-group-form', $data);
    }

    function save_menuGroup()
    {
        $this->form_validation->set_rules('groupDescription', 'Group Description', 'trim|required');
        $this->form_validation->set_rules('qty', 'Qty is required, please type Number of maximum item ', 'required|numeric');
        $this->form_validation->set_rules('menuItems[]', 'Menu item', 'required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {

            $id = $this->input->post('groupMasterID');
            if ($id) {
                /*update */
                echo json_encode(array('error' => 1, 'message' => 'update not developed'));
            } else {
                $this->db->trans_start();
                try {
                    /* insert */
                    $currentTime = format_date_mysql_datetime();
                    $pc = current_pc();
                    $user = current_userID();
                    $packMenuID = $this->input->post('menuID_groping');
                    $menuItems = $this->input->post('menuItems');
                    $desc = trim($this->input->post('groupDescription') ?? '');
                    $qty = $this->input->post('qty');

                    $isPackGroupExist = $this->Pos_config_model->validate_packGroup($desc, $packMenuID);

                    /*if (count($menuItems) < $qty) {
                        echo json_encode(array('error' => 1, 'message' => 'qty can no be greater than selected menu item count'));
                        exit;
                    }*/

                    if (!$isPackGroupExist) {
                        $data = array(
                            "description" => $desc,
                            "packMenuID" => $packMenuID,
                            "qty" => $qty,
                            "IsRequired" => 0,
                            "createdBy" => $user,
                            "createdPc" => $pc,
                            "createdDatetime" => $currentTime,
                            "timestamp" => $currentTime
                        );

                        $groupMasterID = $this->Pos_config_model->insert_srp_erp_pos_menupackgroupmaster($data);

                        if ($groupMasterID) {
                            if (!empty($menuItems)) {
                                $i = 0;
                                foreach ($menuItems as $item) {
                                    $exp_item = explode('_', $item);

                                    $data2[$i]['groupMasterID'] = $groupMasterID;
                                    $data2[$i]['packMenuID'] = $packMenuID;
                                    $data2[$i]['menuID'] = $exp_item[0];
                                    $data2[$i]['menuPackItemID'] = $exp_item[1];
                                    $data2[$i]['createdBy'] = $user;
                                    $data2[$i]['createdPc'] = $pc;
                                    $data2[$i]['createdDatetime'] = $currentTime;
                                    $data2[$i]['timestamp'] = $currentTime;
                                    $i++;
                                }
                                $this->Pos_config_model->insert_batch_srp_erp_pos_packgroupdetail($data2);
                            }

                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();
                                echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                            } else {
                                $this->db->trans_commit();
                                echo json_encode(array('error' => 0, 'message' => 'successfully inserted', 'packMenuID' => $packMenuID));
                            }
                        } else {
                            echo json_encode(array('error' => 1, 'message' => 'An error has occurred'));
                        }
                    } else {

                        /**
                         *
                         * Add to existing group
                         * Modified on 18-April-2017
                         *
                         */


                        if (!empty($menuItems)) {
                            $i = 0;
                            foreach ($menuItems as $item) {
                                $exp_item = explode('_', $item);

                                $data3[$i]['groupMasterID'] = $isPackGroupExist['groupMasterID'];
                                $data3[$i]['packMenuID'] = $packMenuID;
                                $data3[$i]['menuID'] = $exp_item[0];
                                $data3[$i]['menuPackItemID'] = $exp_item[1];
                                $data3[$i]['createdBy'] = $user;
                                $data3[$i]['createdPc'] = $pc;
                                $data3[$i]['createdDatetime'] = $currentTime;
                                $data3[$i]['timestamp'] = $currentTime;
                                $i++;
                            }

                            $this->Pos_config_model->insert_batch_srp_erp_pos_packgroupdetail($data3);

                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();
                                echo json_encode(array('error' => 1, 'message' => 'error while inserting, message: ' . $this->db->_error_message()));
                            } else {
                                $this->db->trans_commit();
                                echo json_encode(array('error' => 0, 'message' => 'successfully inserted', 'packMenuID' => $packMenuID,));

                            }


                        } else {
                            echo json_encode(array('error' => 1, 'message' => 'Please select an item to add'));
                        }


                        //echo json_encode(array('error' => 1, 'message' => 'Group name already exist for this pack/combo'));
                    }
                } catch (Exception $e) {
                    $exp = $e->getMessage();
                    $this->db->trans_rollback();
                    echo json_encode(array('error' => 0, 'message' => 'An Error (500) has occurred,<br>' . $exp . '.<br/> please contact your system support team'));
                }
            }
        }
    }

    function delete_pos_packGroup()
    {
        $id = $this->input->post('id');
        $result = $this->Pos_config_model->delete_posPackageGroup($id);
        $this->Pos_config_model->delete_posPackageGroupDetail($id);

        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'record deleted'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error while deleting, please contact your system support team.'));
        }
    }

    function updateIsAddOnValue()
    {
        $id = $this->input->post('id');
        $where = $this->input->post('where');
        $addonValue = $this->input->post('addonValue');
        $data['isAddOn'] = $addonValue;

        $extraID = $this->input->post('extraID');

        $this->Pos_config_model->updateMenu($data, $id);
        echo json_encode(array('error' => 0, 'message' => 'Updated', 'tmp' => $addonValue));

    }

    function updateSortOrder()
    {
        $r = false;
        $id = $this->input->post('id');
        $source = $this->input->post('source');
        $data['sortOrder'] = $this->input->post('sortOrder');
        if ($source == 'm') {
            $r = $this->Pos_config_model->updateMenu($data, $id);
        } else if ($source == 'mc') {
            $r = $this->Pos_config_model->updateMenuCategory($data, $id);
        }
        if ($r) {
            echo json_encode(array('error' => 0, 'message' => 'updated'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'error'));
        }
    }

    function batch_update_menu_cost()
    {
        echo '<pre>batch started<br/>';
        /** get all menus */
        $r = $this->Pos_config_model->get_srp_erp_pos_menumaster_all_active();
        if (!empty($r)) {
            /*
            print_r($r);
            echo '<pre>';*/
            $i = 0;
            foreach ($r as $item) {
                $menuMasterID = $item['menuMasterID'];
                /** update menu cost */
                $result = $this->Pos_config_model->updateTotalCost($menuMasterID);


                if ($result) {
                    $tmpData[$i]['menuID'] = $menuMasterID;
                    $tmpData[$i]['result'] = $result;

                } else {
                    $tmpData[$i]['menuID'] = $menuMasterID;
                    $tmpData[$i]['result'] = $result;
                }
                $i++;

            }

            print_r($tmpData);

        }
        ?>
        Summary
        Total Loop : <?php echo $i + 1; ?>

        End.
        <?php

    }

    function fetch_customer_type()
    {
        /*, chartOfAccount1.GLDescription as expenseGLDesc,chartOfAccount1.GLDescription as liabilityGLDesc */
        $posType = $this->input->post('posType');
        $this->datatables->select('customerID,customerName,srp_erp_pos_customers.isActive as isActive,customerTypeMasterID,srp_erp_pos_customertypemaster.description as description,commissionPercentage, isOnTimePayment as isOnTimePayment,expenseGLAutoID,liabilityGLAutoID ', false)
            ->from('srp_erp_pos_customers')
            ->join('srp_erp_pos_customertypemaster', 'srp_erp_pos_customers.customerTypeMasterID = srp_erp_pos_customertypemaster.customerTypeID', 'left')
            //->join('srp_erp_chartofaccounts chartOfAccount1', 'chartOfAccount1.GLAutoID = srp_erp_pos_customers.expenseGLAutoID', 'left')
            //->join('srp_erp_chartofaccounts chartOfAccount2', 'chartOfAccount1.GLAutoID = srp_erp_pos_customers.liabilityGLAutoID', 'left')
            ->where('srp_erp_pos_customers.companyID', current_companyID())
            ->where('srp_erp_pos_customers.posType', $posType)
            ->add_column('tmpIsOnTimePayment', '$1', 'pos_config_isOnTimePaymentCol(isOnTimePayment,customerTypeMasterID)')
            ->edit_column('PromotionOrderTypeDeliveryType', '$1', 'loadPromotionOrderCol(description,customerTypeMasterID)')
            ->edit_column('expenseGL', '$1', 'loadGL_records_delivery(expenseGLAutoID,customerTypeMasterID,isOnTimePayment,"E")')
            ->edit_column('liabilityGL', '$1', 'loadGL_records_delivery(liabilityGLAutoID,customerTypeMasterID,isOnTimePayment,"L")')
            ->edit_column('action', '$1', 'load_edit_customer_type(customerID)')
            ->edit_column('Active', '$1', 'load_active_customer_type(customerID,isActive)');

        echo $this->datatables->generate();
    }

    function fetch_delivery_setup()
    {
        /*, chartOfAccount1.GLDescription as expenseGLDesc,chartOfAccount1.GLDescription as liabilityGLDesc */
        $posType = $this->input->post('posType');
        $this->datatables->select('commissionAmount, customerID,customerName,srp_erp_pos_customers.isActive as isActive,customerTypeMasterID,srp_erp_pos_customertypemaster.description as description,commissionPercentage, isOnTimePayment as isOnTimePayment,expenseGLAutoID,liabilityGLAutoID ', false)
            ->from('srp_erp_pos_customers')
            ->join('srp_erp_pos_customertypemaster', 'srp_erp_pos_customers.customerTypeMasterID = srp_erp_pos_customertypemaster.customerTypeID', 'left')
            //->join('srp_erp_chartofaccounts chartOfAccount1', 'chartOfAccount1.GLAutoID = srp_erp_pos_customers.expenseGLAutoID', 'left')
            //->join('srp_erp_chartofaccounts chartOfAccount2', 'chartOfAccount1.GLAutoID = srp_erp_pos_customers.liabilityGLAutoID', 'left')
            ->where('srp_erp_pos_customers.companyID', current_companyID())
            ->where('srp_erp_pos_customers.posType', $posType)
            ->where_in('srp_erp_pos_customers.customerTypeMasterID', array('1','5'))
            ->add_column('tmpIsOnTimePayment', '$1', 'pos_config_isOnTimePaymentCol(isOnTimePayment,customerTypeMasterID)')
            ->add_column('amount', '$1', 'format_amount(commissionAmount)')
            ->edit_column('PromotionOrderTypeDeliveryType', '$1', 'loadPromotionOrderCol(description,customerTypeMasterID)')
            ->edit_column('expenseGL', '$1', 'loadGL_records_delivery(expenseGLAutoID,customerTypeMasterID,isOnTimePayment,"E")')
            ->edit_column('liabilityGL', '$1', 'loadGL_records_delivery(liabilityGLAutoID,customerTypeMasterID,isOnTimePayment,"L")')
            ->edit_column('action', '$1', 'load_edit_customer_type(customerID)')
            ->edit_column('Active', '$1', 'load_active_customer_type(customerID,isActive)');

        echo $this->datatables->generate();
    }

    function update_customer_type_isactive()
    {
        echo json_encode($this->Pos_config_model->update_customer_type_isactive());
    }


    function saveCustomer()
    {
        $this->form_validation->set_rules('customerName', 'Customer Name', 'trim|required');
        $this->form_validation->set_rules('customerTypeMasterID', 'Customer Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {

            if ($this->input->post('customerTypeMasterID') == 1) {
                if ($this->input->post('isOnTimePayment') == '') {
                    echo json_encode(array('error' => 1, 'message' => 'Please Select Delivery payment method'));
                    exit;
                }
                if (empty($this->input->post('expenseGLAutoID'))) {
                    echo json_encode(array('error' => 1, 'message' => 'Please Select Expense GL'));
                    exit;
                }

                if ($this->input->post('isOnTimePayment') == 0) {
                    if (empty($this->input->post('liabilityGLAutoID'))) {
                        echo json_encode(array('error' => 1, 'message' => 'Please Select Liability GL'));
                        exit;
                    }
                }

                if ($this->input->post('isOnTimePayment') == 1) {
                    if ($this->input->post('expenseGLAutoID') == 0) {
                        echo json_encode(array('error' => 1, 'message' => 'Please Select Expense GL'));
                        exit;
                    }
                }


            } else if ($this->input->post('customerTypeMasterID') == 2) {

            } else if ($this->input->post('customerTypeMasterID') == 3) {
                $_POST['isOnTimePayment'] = null;


                if ($this->input->post('expenseGLAutoID') == '') {
                    echo json_encode(array('error' => 1, 'message' => 'Please Select Expense GL'));
                    exit;
                }

            } else if ($this->input->post('customerTypeMasterID') == 5) {
                $_POST['isOnTimePayment'] = null;
                if ($this->input->post('RevenueGLAutoID') == '') {
                    echo json_encode(array('error' => 1, 'message' => 'Please Select Revenue GL'));
                    exit;
                }

                if ($this->input->post('ownDeliveryChargeBasedOn') == '') {
                    echo json_encode(array('error' => 1, 'message' => 'Please select charge based on'));
                    exit;
                }

            } else {
                $_POST['isOnTimePayment'] = null;
            }
            $Percentage = $this->input->post('commissionPercentage');
            if ($Percentage < 0) {
                echo json_encode(array('error' => 1, 'message' => 'Commission Percentage should be greater than 0'));
            } else if ($Percentage > 100) {
                echo json_encode(array('error' => 1, 'message' => 'Commission Percentage should be less than 100'));
            } else {
                $customerID = $this->input->post('customerIDhn');
                $data['customerName'] = $this->input->post('customerName');
                $data['customerTypeMasterID'] = $this->input->post('customerTypeMasterID');

                $data['isOnTimePayment'] = $this->input->post('isOnTimePayment');
                $data['expenseGLAutoID'] = $this->input->post('expenseGLAutoID');
                $data['liabilityGLAutoID'] = $this->input->post('liabilityGLAutoID');
                $data['revenueGLAutoID'] = $this->input->post('RevenueGLAutoID');

                $data['ownDeliveryBasedOn'] = $this->input->post('ownDeliveryChargeBasedOn');
                if($data['ownDeliveryBasedOn']!=''){
                    if($data['ownDeliveryBasedOn']==1){//based on amount.
                        $data['commissionAmount'] = $this->input->post('commissionAmount');
                        $data['commissionPercentage'] = 0;
                    }else if($data['ownDeliveryBasedOn']==2){//based on percentage.
                        $data['commissionPercentage'] = $this->input->post('commissionPercentage');
                        $data['commissionAmount'] = 0;
                    }
                }else{
                    $data['commissionPercentage'] = $this->input->post('commissionPercentage');
                }

                $data['companyID'] = current_companyID();

                if ($customerID) {
                    /* update */

                    $result = $this->Pos_config_model->update_srp_erp_pos_customers($customerID, $data);
                    if ($result) {
                        echo json_encode(array('error' => 0, 'message' => 'updated'));
                    } else {
                        echo json_encode(array('error' => 1, 'message' => 'Error while updating, Please contact your system support team'));
                    }
                } else {
                    /* insert */

//var_dump($data);exit;
                    $data['timestamp'] = $this->input->post('timestamp');
                    $data['posType'] = $this->input->post('posType', true);
                    $result = $this->Pos_config_model->save_srp_erp_pos_customers($data);
                    if ($result) {
                        echo json_encode(array('error' => 0, 'message' => 'successfully saved'));
                    } else {
                        echo json_encode(array('error' => 1, 'message' => 'Error while insert, Please contact your system support team'));
                    }
                }
            }

        }
    }

    function edit_customer()
    {
        echo json_encode($this->Pos_config_model->get_edit_customer());
    }

    function fetch_yield_master()
    {
        $this->datatables->select('yieldID, yielduomID, yieldUOM, yieldAmount, yieldCost, qty, CONCAT(itemDescription, " - ",itemSystemCode) as Description', false)
            ->from('srp_erp_pos_menuyields')
            ->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID=srp_erp_pos_menuyields.itemAutoID', 'left')
            ->where('srp_erp_pos_menuyields.companyID', current_companyID())
            ->edit_column('action', '$1', 'load_edit_yield_master(yieldID)')
            ->edit_column('netTotal', '$1', 'get_total_fromYield(yieldID)');
        echo $this->datatables->generate();
    }


    function saveYield()
    {
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules('yieldUOM', 'Customer UOM', 'trim|required');
        $this->form_validation->set_rules('qty', 'QTY', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {

            $this->db->select('UnitDes');
            $this->db->from('srp_erp_unit_of_measure');
            $this->db->where('UnitID', $this->input->post('yieldUOM'));
            $UnitDes = $this->db->get()->row_array();

            $yieldID = $this->input->post('yieldIDhn');
            $data['itemAutoID'] = $this->input->post('itemAutoID');
            $data['yielduomID'] = $this->input->post('yieldUOM');
            $data['yieldUOM'] = $UnitDes['UnitDes'];
            $data['qty'] = $this->input->post('qty');
            $data['companyID'] = current_companyID();

            if ($yieldID) {
                /* update */
                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_userID();
                $data['modifiedDateTime'] = current_date();
                $data['modifiedUserName'] = current_user();
                $result = $this->Pos_config_model->update_srp_erp_pos_menuyields($yieldID, $data);
                if ($result) {
                    echo json_encode(array('error' => 0, 'message' => 'updated'));
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'Error while updating, Please contact your system support team'));
                }
            } else {
                /* insert */
                $data['timestamp'] = current_date();
                $data['companyCode'] = current_companyCode();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                $data['createdDateTime'] = current_date();
                $data['createdUserName'] = current_user();
                $data['createdUserGroup'] = current_user_group();
                $result = $this->Pos_config_model->save_srp_erp_pos_menuyields($data);
                if ($result) {
                    echo json_encode(array('error' => 0, 'message' => 'successfully saved'));
                } else {
                    echo json_encode(array('error' => 1, 'message' => 'Error while insert, Please contact your system support team'));
                }
            }

        }
    }

    function edit_yieldMaster()
    {
        echo json_encode($this->Pos_config_model->get_edit_yieldMaster());
    }

    function fetch_yield_detail()
    {
        $d = get_company_currency_decimal();
        $this->datatables->select('yieldDetailID,yieldID,description,typeAutoId,qty,uom,concat("<div class=\'ar\'>",FORMAT(cost,' . $d . '),"</div>") as cost,(srp_erp_itemmaster.companyLocalWacAmount / getUoMConvertion (srp_erp_pos_menuyieldsdetails.uom,srp_erp_itemmaster.defaultUnitOfMeasureID,srp_erp_pos_menuyieldsdetails.companyID))* qty AS COST1,srp_erp_pos_menuyieldsdetails.itemAutoID,srp_erp_unit_of_measure.UnitDes as UnitDes,CONCAT(itemDescription," - ",itemSystemCode) as itemDescription', false)
            ->from('srp_erp_pos_menuyieldsdetails')
            ->join('srp_erp_unit_of_measure', 'srp_erp_pos_menuyieldsdetails.uom = srp_erp_unit_of_measure.UnitID', 'left')
            ->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID=srp_erp_pos_menuyieldsdetails.itemAutoID', 'left')
            ->where('srp_erp_pos_menuyieldsdetails.companyID', current_companyID())
            ->where('srp_erp_pos_menuyieldsdetails.yieldID', $this->input->post('yieldID'))
            ->edit_column('amount', '$1', 'convertCostAmount(COST1)')
            ->edit_column('action', '$1', 'load_edit_yield_detail(yieldDetailID)')
            ->edit_column('type', '$1', 'load_type_yield_detail(typeAutoId)')
            ->edit_column('item', '$1', 'load_item_yield_detail(typeAutoId,itemAutoID)');
        echo $this->datatables->generate();
    }

    function sum_total_yield()
    {
        $yieldID = $this->input->post('yieldID');
        // $sql = 'SELECT * FROM srp_erp_pos_menuyieldsdetails';
        $this->db->select_sum('cost');
        $this->db->where('yieldID', $yieldID);
        $this->db->from('srp_erp_pos_menuyieldsdetails');
        $result = $this->db->get()->row();
        echo json_encode($result);

    }

    function loadItemDropDown()
    {
        echo json_encode($this->Pos_config_model->loadItemDropDown());
    }

    function saveYieldDetail()
    {
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        /* $this->form_validation->set_rules('typeAutoId', 'Type', 'trim|required');
         $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');*/
        $this->form_validation->set_rules('uom', 'UOM', 'trim|required');
        $this->form_validation->set_rules('qty', 'QTY', 'trim|required');
        $this->form_validation->set_rules('cost', 'Cost', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {

            $yieldDetailID = $this->input->post('yieldDetailIDhn');
            $itemID = $this->input->post('itemAutoID');
            $itemqty = $this->input->post('qty');

            $currentStock = $this->db->query("SELECT
                                                    currentStock,
                                                    itemDescription	
                                                FROM
                                                    `srp_erp_itemmaster` 
                                                WHERE
                                                    itemAutoID = $itemID")->row_array();
            if($itemqty > $currentStock['currentStock']) {
                 echo json_encode(array('error' => 1, 'message' => $currentStock['itemDescription'].' - Out of Stock'));
            } else {
                  //$data['description'] = $this->input->post('description');
                $data['yieldID'] = $this->input->post('yieldIDhn');
                $data['typeAutoId'] = $this->input->post('typeAutoId');
                $data['itemAutoID'] = $this->input->post('itemAutoID');
                $data['qty'] = $this->input->post('qty');
                $data['uom'] = $this->input->post('uom');
                $data['cost'] = $this->input->post('cost');
                $data['unitCost'] = $this->input->post('unitCost');
                $data['companyID'] = current_companyID();

                if ($yieldDetailID) {
                    /* update */
                    $data['modifiedPCID'] = current_pc();
                    $data['modifiedUserID'] = current_userID();
                    $data['modifiedDateTime'] = current_date();
                    $data['modifiedUserName'] = current_user();
                    $result = $this->Pos_config_model->update_srp_erp_pos_menuyieldsdetails($yieldDetailID, $data);
                    if ($result) {
                        $this->db->select('sum(cost) as cost');
                        $this->db->from('srp_erp_pos_menuyieldsdetails');
                        $this->db->where('yieldID', $this->input->post('yieldIDhn'));
                        $yieldCost = $this->db->get()->row_array();

                        $datasu['yieldCost'] = $yieldCost['cost'];
                        $this->db->where('yieldID', $this->input->post('yieldIDhn'));
                        $this->db->update('srp_erp_pos_menuyields', $datasu);
                        echo json_encode(array('error' => 0, 'message' => 'updated'));
                    } else {
                        echo json_encode(array('error' => 1, 'message' => 'Error while updating, Please contact your system support team'));
                    }
                } else {
                    /* insert */
                    $data['timestamp'] = current_date();
                    //$data['companyCode'] = current_companyCode();
                    $data['createdPCID'] = current_pc();
                    $data['createdUserID'] = current_userID();
                    $data['createdDateTime'] = current_date();
                    $data['createdUserName'] = current_user();
                    $data['createdUserGroup'] = current_user_group();
                    $result = $this->Pos_config_model->save_srp_erp_pos_menuyieldsdetails($data);
                    if ($result) {
                        $this->db->select('sum(cost) as cost');
                        $this->db->from('srp_erp_pos_menuyieldsdetails');
                        $this->db->where('yieldID', $this->input->post('yieldIDhn'));
                        $yieldCost = $this->db->get()->row_array();

                        $datas['yieldCost'] = $yieldCost['cost'];
                        $this->db->where('yieldID', $this->input->post('yieldIDhn'));
                        $this->db->update('srp_erp_pos_menuyields', $datas);

                        echo json_encode(array('error' => 0, 'message' => 'successfully saved'));
                    } else {
                        echo json_encode(array('error' => 1, 'message' => 'Error while insert, Please contact your system support team'));
                    }
                }
            }                                

        }
    }

    function edit_yieldDetail()
    {
        echo json_encode($this->Pos_config_model->get_edit_yieldDetail());
    }


    function update_group_item_status()
    {
        $id = $this->input->get('id');
        $status = $this->input->get('status');

        $data['isActive'] = $status;

        $result = $this->Pos_config_model->update_srp_erp_pos_packgroupdetail($id, $data);
        if ($result) {
            $tmp = array('error' => 0, 'message' => 'updated', 'id' => $id);
        } else {
            $tmp = array('error' => 1, 'message' => 'Error while updating, Please contact your system support team');
        }
        echo json_encode($tmp);
    }

    function delete_pos_kotLocation()
    {

        $id = $this->input->post('outletID');

        $result = $this->Pos_config_model->delete_srp_erp_pos_kitchenlocation($id);
        echo json_encode($result);
    }

    function save_kotLocation()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->save_kotLocation());
        }
    }

    function update_kotID()
    {
        echo json_encode($this->Pos_config_model->update_kotID());
    }

    
    function loadChartOfAccountData()
    {
        $this->form_validation->set_rules('GLConfigMasterID', 'Payment Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {

            $GLConfigMasterID = $this->input->post('GLConfigMasterID');

            $this->db->select('glAccountType');
            $this->db->from('srp_erp_pos_paymentglconfigmaster');
            $this->db->where('autoID', $GLConfigMasterID);
            $glAccountType = $this->db->get()->row('glAccountType');

            switch ($glAccountType) {
                case 1:
                    echo json_encode(array('error' => 0, 'e' => payableGL_drop()));
                    break;
                case 2:
                    echo json_encode(array('error' => 0, 'e' => load_bank_with_card(true)));
                    break;
                case 3:
                    echo json_encode(array('error' => 0, 'e' => liabilityGL_drop()));
                    break;
                case 4:
                    echo json_encode(array('error' => 0, 'e' => expenseIncomeGL_drop()));
                    break;
                default :
                    echo json_encode(array('error' => 1, 'message' => 'Invalid account, please contact system support team'));
                    exit;
            }
        }
    }

    function saveGLConfigDetail()
    {
        $id = $this->input->post('paymentConfigMasterID');
        $this->form_validation->set_rules('paymentConfigMasterID', 'Payment Type', 'trim|required');
        if ($id != 7) {
            $this->form_validation->set_rules('GLCode', 'Account Name', 'trim|required');
        }

        $this->form_validation->set_rules('warehouseID', 'Outlet', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->saveGLConfigDetail());
        }

    }


    
    function POSR_posGL_config()
    {
        $this->form_validation->set_rules('ID', 'Auto ID', 'trim|required');
        $this->form_validation->set_rules('glAutoID', 'Account Name', 'trim|required');
        $this->form_validation->set_rules('paymentTypeID', 'Payment', 'trim|required');
        $this->form_validation->set_rules('warehouseID', 'Outlet', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_config_model->POSR_posGL_config());
        }
    }

    function load_pricing()
    {
        $menuID = $this->input->post('menuID');
        $data['menuData'] = $this->Pos_config_model->get_srp_erp_pos_menumaster_specific($menuID);
        $data['taxes'] = $this->Pos_config_model->get_menuTax($menuID);
        $data['serviceCharges'] = $this->Pos_config_model->get_menuServiceCharge($menuID);
        $this->load->view('system/pos/ajax/ajax-load-menu-pricing', $data);
    }

    function save_outlet_tax()
    {

        $this->form_validation->set_rules('outlet_id', 'Outlet ID', 'trim|required');
        $this->form_validation->set_rules('tax_taxmasterID', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('tax_taxPercentage', 'Tax Percentage', 'trim|required');
        $this->form_validation->set_rules('taxDescription', 'Description', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            $outlet_id = $this->input->post('outlet_id');
            $tax_taxPercentage = $this->input->post('tax_taxPercentage');
            $tax_taxmasterID = $this->input->post('tax_taxmasterID');
            $taxDescription = $this->input->post('taxDescription');
            echo json_encode($this->Pos_config_model->save_outlet_tax($outlet_id, $tax_taxPercentage, $tax_taxmasterID, $taxDescription));
        }
    }

    function save_menuTax()
    {
        $this->form_validation->set_rules('menuMasterID', 'Menu ID', 'trim|required');
        $this->form_validation->set_rules('taxmasterID', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('taxPercentage', 'Tax Percentage', 'trim|required');
        $this->form_validation->set_rules('taxAmount', 'Tax Amount', 'trim|required');

        $taxmasterID=$this->input->post('taxmasterID');

        if($taxmasterID!=''){
            $category = $this->db->query("select taxCategory from srp_erp_taxmaster where taxMasterAutoID=$taxmasterID")->row('taxCategory');
            if($category==2){
                $this->form_validation->set_rules('vatType', 'VAT Type', 'trim|required');
            }
        }
//        var_dump($category);
//        exit;
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->save_menuTax());
        }
    }

    function delete_menuTax()
    {
        $id = $this->input->post('id');
        $menuMasterID = $this->db->select('menuMasterID')->from('srp_erp_pos_menutaxes')->where('menutaxID', $id)->get()->row('menuMasterID');
        $result = $this->db->delete('srp_erp_pos_menutaxes', array('menutaxID' => $id), 1);

        $q = "UPDATE srp_erp_pos_menumaster
SET totalTaxAmount = IF(( SELECT sum(taxAmount) AS totalTaxAmount FROM srp_erp_pos_menutaxes WHERE menuMasterID = '" . $menuMasterID . "' )>0,( SELECT sum(taxAmount) AS totalTaxAmount FROM srp_erp_pos_menutaxes WHERE menuMasterID = '" . $menuMasterID . "' ) , 0) WHERE menuMasterID = '" . $menuMasterID . "'";
        $this->db->query($q);

        $this->Pos_config_model->update_selling_price($menuMasterID);

        if ($result) {
            echo json_encode(array('error' => 0, 'message' => '<strong>Tax Deleted</strong>'));
        } else {
            echo json_encode(array('error' => 1, 'message' => '<strong>Error while, deleting the record!, Please contact your system support team.</strong>'));
        }
    }

    function save_serviceCharge()
    {
        $this->form_validation->set_rules('menuMasterID', 'menuMasterID', 'trim|required');
        $this->form_validation->set_rules('GLAutoID', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('serviceChargePercentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('serviceChargeAmount', 'Amount', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->save_serviceCharge());
        }
    }

    function delete_serviceCharge()
    {
        $id = $this->input->post('id');
        $menuMasterID = $this->db->select('menuMasterID')->from('srp_erp_pos_menuservicecharge')->where('menuServiceChargeID', $id)->get()->row('menuMasterID');
        $result = $this->db->delete('srp_erp_pos_menuservicecharge', array('menuServiceChargeID' => $id), 1);
        $q = "UPDATE srp_erp_pos_menumaster SET totalServiceCharge = IF(( SELECT sum(serviceChargeAmount) AS totalServiceCharge FROM srp_erp_pos_menuservicecharge WHERE menuMasterID = '" . $menuMasterID . "' )>0,( SELECT sum(serviceChargeAmount) AS totalServiceCharge FROM srp_erp_pos_menuservicecharge WHERE menuMasterID = '" . $menuMasterID . "' ) , 0) WHERE menuMasterID = '" . $menuMasterID . "'";
        $this->db->query($q);

        $this->Pos_config_model->update_selling_price($menuMasterID);

        if ($result) {
            echo json_encode(array('error' => 0, 'message' => '<strong>Record Deleted</strong>'));
        } else {
            echo json_encode(array('error' => 1, 'message' => '<strong>Error while, deleting the record!, Please contact your system support team.</strong>'));
        }
    }

    function update_warehouseIsTaxEnabled()
    {
        echo json_encode($this->Pos_config_model->update_warehouseIsTaxEnabled());
    }

    function warehouse_image_upload()
    {
        $this->form_validation->set_rules('wareHouseAutoID', 'Warehouse ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Pos_config_model->warehouse_image_upload());
        }
    }

    function fetch_itemrecode_yeild()
    {
        echo json_encode($this->Pos_config_model->fetch_itemrecode_yeild());
    }

    /*pos authentication*/

    function fetch_auth_process()
    {
        $this->datatables->select('srp_erp_pos_auth_processassign.processMasterID as processMasterID,description,processAssignID,srp_erp_pos_auth_processassign.isActive as isActive,', false)
            ->from('srp_erp_pos_auth_processassign')
            ->join('srp_erp_pos_auth_processmaster', 'srp_erp_pos_auth_processmaster.processMasterID = srp_erp_pos_auth_processassign.processMasterID', 'left')
            ->where('srp_erp_pos_auth_processassign.companyID', current_companyID())
            ->where('srp_erp_pos_auth_processassign.posType', 1)
            ->edit_column('action', '$1', 'load_auth_process_action(processMasterID,description,processAssignID)')
            ->edit_column('active', '$1', 'load_active_auth_process(processMasterID,isActive)');
        echo $this->datatables->generate();
    }

    function update_auth_process_isactive()
    {
        echo json_encode($this->Pos_config_model->update_auth_process_isactive());
    }

    function addProcess()
    {
        $this->form_validation->set_rules('processMasterID[]', 'Process', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->addProcess());
        }
    }

    function fetch_assigned_user_group()
    {
        $processMasterID = $this->input->post("processMasterID");
        $posType = $this->input->post("posType");
        $this->datatables->select('userGroupDetailID,srp_erp_pos_auth_usergroupmaster.description as usergroup,wareHouseDescription as outlet, srp_erp_warehousemaster.wareHouseCode as wareHouseCode, srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription, srp_erp_warehousemaster.wareHouseLocation as wareHouseLocation', false)
            ->from('srp_erp_pos_auth_usergroupdetail')
            ->join('srp_erp_warehousemaster', 'srp_erp_warehousemaster.wareHouseAutoID = srp_erp_pos_auth_usergroupdetail.wareHouseID', 'left')
            ->join('srp_erp_pos_auth_usergroupmaster', 'srp_erp_pos_auth_usergroupmaster.userGroupMasterID = srp_erp_pos_auth_usergroupdetail.userGroupMasterID', 'left')
            ->where('srp_erp_pos_auth_usergroupdetail.companyID', current_companyID())
            ->where('srp_erp_pos_auth_usergroupdetail.processMasterID', $processMasterID)
            ->where('srp_erp_pos_auth_usergroupdetail.posType', $posType)
            ->edit_column('action', '$1', 'load_user_group_assign_process(userGroupDetailID)')
            ->edit_column('descriptionOutlet', '$1 - $2 - $3', 'wareHouseCode,wareHouseDescription,wareHouseLocation');
        echo $this->datatables->generate();
    }

    function add_user_group()
    {
        $this->form_validation->set_rules('wareHouseID', 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('userGroupMasterID[]', 'User group', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo json_encode($this->Pos_config_model->add_user_group());
        }
    }

    function delete_assigned_user_group()
    {
        echo json_encode($this->Pos_config_model->delete_assigned_user_group());
    }

    /*end pos authentication*/

    function load_wifi_password()
    {
        $id = $this->input->post('outletID');
        $this->datatables->select('id , wifiPassword,  isUsed, outletID, concat(warehouse.wareHouseCode, " - " ,warehouse.wareHouseDescription) as wareHouseDescription ', false)
            ->from('srp_erp_pos_wifipasswordsetup wifi')
            ->where('isUsed', 0);
        if (!empty($id)) {
            $this->datatables->where('outletID', $id);
        }
        $this->datatables->join('srp_erp_warehousemaster as warehouse', 'warehouse.warehouseAutoID = wifi.outletID', 'LEFT');
        echo $this->datatables->generate();
    }

    function save_wifi_password()
    {
        $this->form_validation->set_rules('outlet', 'outlet', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            if (!empty($_FILES['password_list']['tmp_name'])) {

                $pathInfo = pathinfo($_FILES['password_list']['name']);

                if ($pathInfo['extension'] == 'csv') {
                    $this->load->library('CSVReader');
                    $output = $this->csvreader->parse_file($_FILES['password_list']['tmp_name']);//path to csv file

                    /** Convert multi dimensional array to single array  */
                    array_walk_recursive($output, function ($v) use (&$result) {
                        $result[] = $v;
                    });

                    $outletID_input = $this->input->post('outlet');

                    $isUsed = 0;
                    $filesName = $_FILES['password_list']['name'];
                    $path = '-';
                    $outletID = $outletID_input;
                    $companyID = current_companyID();
                    $createdBy = current_userID();
                    $createdDatetime = format_date_mysql_datetime();


                    $finalOutput = implode(',', $result);
                    $finalOutput = explode(',', $finalOutput);

                    if (!empty($finalOutput)) {
                        $i = 0;
                        $x = 0;
                        $exsispwdarr = array();
                        foreach ($finalOutput as $pw_string) {
                            $tmp_result = $this->Pos_config_model->wifi_password_check($pw_string, $outletID, $companyID);

                            if (empty($tmp_result)) {
                                if (!empty(trim($pw_string))) {
                                    $db_data[$i]['wifiPassword'] = $pw_string;
                                    $db_data[$i]['isUsed'] = $isUsed;
                                    $db_data[$i]['filesName'] = $filesName;
                                    $db_data[$i]['path'] = $path;
                                    $db_data[$i]['outletID'] = $outletID;
                                    $db_data[$i]['wareHouseAutoID'] = $outletID;
                                    $db_data[$i]['id_store'] = $outletID;
                                    $db_data[$i]['companyID'] = $companyID;
                                    $db_data[$i]['createdBy'] = $createdBy;
                                    $db_data[$i]['createdDatetime'] = $createdDatetime;
                                    $i++;
                                }
                            } else {
                                if (!empty($tmp_result)) {
                                    array_push($exsispwdarr, $tmp_result);
                                    $x++;
                                }
                            }
                        }

                        if (true) { /*empty($exsispwdarr)*/
                            if (!empty($db_data)) {
                                $results = $this->db->insert_batch('srp_erp_pos_wifipasswordsetup', $db_data);
                                if ($results) {
                                    $errorMsg = $i > 0 ? '<br/>' . $x . ' passwords are already exist (skipped)' : '';

                                    $msg = $i == 0 ? 'e' : 's';
                                    echo json_encode(array($msg, $i . ' passwords successfully added.' . $errorMsg));
                                }
                            } else {
                                echo json_encode(array('w', 'All Password are already added'));
                            }
                        } else {
                            echo json_encode(array('e', 'Following passwords already exist, please correct the listed password and re-upload again.', $exsispwdarr));
                        }
                    }
                } else {
                    echo json_encode(array('e', 'This file format is not allowed, please select  CSV file format'));
                }
            } else {
                echo json_encode(array('e', 'Please select CSV File'));
            }
        }
    }

    function search_barcode()
    {
        $search_string = $_GET['q'];
        echo json_encode($this->Pos_config_model->search_barcode($search_string));
    }

    function load_outlet_tax_table()
    {
        $warehouseAutoID = $this->input->post("outlet_id");
        $this->datatables->select('null as id,outletTaxID as outletTaxID,taxDescription as taxDescription,taxPercentage as taxPercentage', false)
            ->from('srp_erp_pos_outlettaxmaster')
            ->where('srp_erp_pos_outlettaxmaster.warehouseAutoID', $warehouseAutoID)
            ->where('srp_erp_pos_outlettaxmaster.isDeleted', 0)
            ->add_column('action', '$1', 'outletTaxActionButtons(outletTaxID)');
        echo $this->datatables->generate();
    }

    function outlet_tax_list()
    {
        $current_warehouseID = current_warehouseID();
        $query = $this->db->query("SELECT * FROM `srp_erp_pos_outlettaxmaster` where warehouseAutoID=$current_warehouseID AND isDeleted=0");
        $data['taxes'] = $query->result();
        echo json_encode($data);
    }

    function delete_outlet_tax()
    {
        $outlet_tax_id = $this->input->post('outlet_tax_id');
        $tax_master_record = array('isDeleted' => 1);
        $this->db->where('outletTaxID', $outlet_tax_id);
        $res = $this->db->update('srp_erp_pos_outlettaxmaster', $tax_master_record);
        if ($res == true) {
            $data['status'] = 'success';
        } else {
            $data['status'] = 'failed';
        }
        echo json_encode($data);
    }

    function saveDiscountSetup()
    {
        $this->form_validation->set_rules('customerName', 'Description', 'trim|required');
        $this->form_validation->set_rules('customerTypeMasterID', 'Customer Type', 'trim|required');
        $this->form_validation->set_rules('datefrom', 'Date From', 'trim|required');
        $this->form_validation->set_rules('dateto', 'Date To', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            if ($this->input->post('customerTypeMasterID') == 1) {
                if ($this->input->post('isOnTimePayment') == '') {
                    echo json_encode(array('error' => 1, 'message' => 'Please Select Delivery payment method'));
                    exit;
                }
                if (empty($this->input->post('expenseGLAutoID'))) {
                    echo json_encode(array('error' => 1, 'message' => 'Please Select Expense GL'));
                    exit;
                }
                if ($this->input->post('isOnTimePayment') == 0) {
                    if (empty($this->input->post('liabilityGLAutoID'))) {
                        echo json_encode(array('error' => 1, 'message' => 'Please Select Liability GL'));
                        exit;
                    }
                }
                if ($this->input->post('isOnTimePayment') == 1) {
                    if ($this->input->post('expenseGLAutoID') == 0) {
                        echo json_encode(array('error' => 1, 'message' => 'Please Select Expense GL'));
                        exit;
                    }
                }
            } else if ($this->input->post('customerTypeMasterID') == 2) {

            } else if ($this->input->post('customerTypeMasterID') == 3) {
                $_POST['isOnTimePayment'] = null;
                if ($this->input->post('expenseGLAutoID') == '') {
                    echo json_encode(array('error' => 1, 'message' => 'Please Select Expense GL'));
                    exit;
                }
            } else if ($this->input->post('customerTypeMasterID') == 5) {
                $_POST['isOnTimePayment'] = null;
                if ($this->input->post('RevenueGLAutoID') == '') {
                    echo json_encode(array('error' => 1, 'message' => 'Please Select Revenue GL'));
                    exit;
                }
            } else {
                $_POST['isOnTimePayment'] = null;
            }
            $Percentage = $this->input->post('commissionPercentage');
            $applyToall = $this->input->post('applyToall');
            if ($applyToall) {
                if ($Percentage <= 0) {
                    echo json_encode(array('error' => 1, 'message' => 'Commission Percentage should be greater than 0'));
                } else if ($Percentage > 100) {
                    echo json_encode(array('error' => 1, 'message' => 'Commission Percentage should be less than 100'));
                } else {
                    echo json_encode($this->Pos_config_model->saveDiscountSetup());
                }
            } else {
                echo json_encode($this->Pos_config_model->saveDiscountSetup());
            }


        }
    }

    function fetch_promotion_type()
    {
        $convertFormat = convert_date_format_sql();
        $posType = $this->input->post('posType');
        $this->datatables->select('customerID,customerName,srp_erp_pos_customers.isActive as isActive,DATE_FORMAT(dateFrom,\'' . $convertFormat . '\') AS dateFrom,DATE_FORMAT(dateTo,\'' . $convertFormat . '\') AS dateTo,customerTypeMasterID,srp_erp_pos_customertypemaster.description as description,commissionPercentage, applyToAllItem', false)
            ->from('srp_erp_pos_customers')
            ->join('srp_erp_pos_customertypemaster', 'srp_erp_pos_customers.customerTypeMasterID = srp_erp_pos_customertypemaster.customerTypeID', 'left')
            ->where('srp_erp_pos_customers.companyID', current_companyID())
            ->where('srp_erp_pos_customers.posType', $posType)
            ->add_column('applyToAllStatus', '$1', 'applyToAllStatus(applyToAllItem)')
            ->add_column('commissionPercentageCol', '$1', 'commissionPercentageCol(commissionPercentage)')
            ->edit_column('daterange', '<strong>Date From : </strong> $1 <strong> TO : </strong> $2', 'dateFrom, dateTo')
            ->edit_column('action', '$1', 'load_edit_promotion_type(customerID, applyToAllItem, isActive)')
            ->edit_column('Active', '$1', 'load_active_customer_type(customerID,isActive)');

        echo $this->datatables->generate();
    }

    function fetch_warehouses_to_add()
    {
        $promoID = $this->input->post('promoID');
        $companyID = $this->common_data['company_data']['company_id'];
        $query = $this->db->query("SELECT wareHouseID FROM `srp_erp_pos_promotionwarehouses` WHERE companyID = $companyID and promotionID=$promoID");
        if ($query->num_rows() > 0) {
            $warehouseIDAr = array();
            foreach ($query->result() as $item) {
                $warehouseIDAr[] = $item->wareHouseID;
            }
            $already_in = implode(",", $warehouseIDAr);
        } else {
            $already_in = '0';
        }
        $notIn = "srp_erp_warehousemaster.wareHouseAutoID not in ($already_in)";
        $this->datatables->select('srp_erp_warehousemaster.wareHouseAutoID as wareHouseAutoID,srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription', false)
            ->from('srp_erp_warehousemaster')
            ->where('srp_erp_warehousemaster.companyID', $companyID)
            ->where($notIn)
            ->add_column('action', '$1', 'addWarehouseAction(wareHouseAutoID)');
        echo $this->datatables->generate();
    }

    function fetch_warehouses_list()
    {
        $promoID = $this->input->post('promoID');
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select('srp_erp_warehousemaster.wareHouseAutoID as wareHouseAutoID,srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription,
        srp_erp_pos_promotionwarehouses.isActive as isActive,
        srp_erp_pos_promotionwarehouses.companyID as companyID,
        srp_erp_pos_promotionwarehouses.promotionID as promotionID', false)
            ->from('srp_erp_warehousemaster')
            ->join('srp_erp_pos_promotionwarehouses', 'srp_erp_pos_promotionwarehouses.wareHouseID = srp_erp_warehousemaster.wareHouseAutoID')
            ->where('srp_erp_pos_promotionwarehouses.companyID', $companyID)
            ->where('srp_erp_pos_promotionwarehouses.promotionID', $promoID)
            ->add_column('action', '$1', 'addWarehouseListAction(wareHouseAutoID,companyID,promotionID,isActive)');
        echo $this->datatables->generate();
    }

    function warehouse_link_to_promo()
    {
        $promoID = $this->input->post('promoID', true);
        $wareHouseAutoID = $this->input->post('wareHouseAutoID', true);
        $isActive = $this->input->post('isActive', true);
        $companyID = $this->common_data['company_data']['company_id'];
        if ($isActive == 1) {
            $this->db->query("update srp_erp_pos_promotionwarehouses set isActive=0 where wareHouseID=$wareHouseAutoID and companyID=$companyID");
        }
        $query = $this->db->query("select * from srp_erp_pos_promotionwarehouses where wareHouseID=$wareHouseAutoID and companyID=$companyID and promotionID=$promoID");
        if ($query->num_rows() > 0) {
            $res = $this->db->query("update srp_erp_pos_promotionwarehouses set isActive=$isActive where wareHouseID=$wareHouseAutoID and companyID=$companyID and promotionID=$promoID");
        } else {
            $insert = array(
                "promotionID" => $promoID,
                "wareHouseID" => $wareHouseAutoID,
                "companyID" => $companyID,
                "isActive" => $isActive,
                "companyCode" => current_company_code(),
                "createdPCID" => current_pc(),
                "createdUserID" => current_userID(),
                "createdDateTime" => format_date_mysql_datetime(),
                "createdUserName" => current_user(),
                "createdUserGroup" => user_group(),
                "timestamp" => format_date_mysql_datetime()
            );
            $res = $this->db->insert('srp_erp_pos_promotionwarehouses', $insert);
        }
        if ($res) {
            echo json_encode(array('error' => 0, 'message' => 'Successfully added the warehouse to the promotion.'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error.'));
        }

    }

    function is_warehouse_link_to_another_promo()
    {
        $promoID = $this->input->post('promoID', true);
        $wareHouseAutoID = $this->input->post('wareHouseAutoID', true);
        $companyID = $this->common_data['company_data']['company_id'];
        $query = $this->db->query("SELECT * FROM `srp_erp_pos_promotionwarehouses` where promotionID!=$promoID and wareHouseID=$wareHouseAutoID and isActive=1 and companyID=$companyID");

        if ($query->num_rows() > 0) {
            $assignePromoID = $query->row()->promotionID;
            $Promotion = $this->db->query("select * from srp_erp_pos_customers where customerID=$assignePromoID");
            $promoName = $Promotion->row()->customerName;
            echo json_encode(array('status' => 1, 'message' => "This warehouse is assigned with $promoName. Do you want to remove it and assign with this promotion?"));
        } else {
            echo json_encode(array('status' => 2));
        }
    }

    function load_subcat()
    {
        echo json_encode($this->Pos_config_model->load_subcat());
    }

    function load_subsubcat()
    {
        echo json_encode($this->Pos_config_model->load_subsubcat());
    }

    function fetch_discount_add_item()
    {
        $docID = $this->input->post('id');
        $companyID = $this->common_data['company_data']['company_id'];

        $promotionWarehousesQuery = $this->db->query("select * from srp_erp_pos_promotionwarehouses where promotionID=$docID");
        $warehouseListArray = array();
        if ($promotionWarehousesQuery->num_rows() > 0) {
            foreach ($promotionWarehousesQuery->result() as $warehouse) {
                array_push($warehouseListArray, $warehouse->wareHouseID);
            }
        }
        $warehouseList = implode(", ", $warehouseListArray);

        $this->datatables->select('srp_erp_itemmaster.itemAutoID as itemAutoID,
                srp_erp_itemmaster.itemSystemCode as itemSystemCode,
                srp_erp_itemmaster.itemName as itemName,
                srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,
                srp_erp_itemmaster.itemImage,
                srp_erp_itemmaster.itemDescription as itemDescription,
                mainCategoryID,
                mainCategory as mainCategory,
                defaultUnitOfMeasure,
                srp_erp_itemmaster.currentStock as currentStock,
                companyLocalSellingPrice as companyLocalSellingPrice,
                srp_erp_itemmaster.companyLocalCurrency,
                srp_erp_itemmaster.companyLocalCurrencyDecimalPlaces,
                revanueDescription,costDescription,assteDescription,isActive,
                srp_erp_itemmaster.companyLocalWacAmount,
                srp_erp_itemcategory.description as SubCategoryDescription,
                subsubcat.description as SubSubCategoryDescription,
                CONCAT(srp_erp_itemmaster.currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(srp_erp_itemmaster.companyLocalWacAmount,\'  \',srp_erp_itemmaster.companyLocalCurrency) as TotalWacAmount,
                srp_erp_warehouseitems.wareHouseAutoID as wareHouseAutoID', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory', 'srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID')
            ->join('srp_erp_itemcategory subsubcat', 'srp_erp_itemmaster.subSubCategoryID = subsubcat.itemCategoryID','left')
            ->join('srp_erp_warehouseitems', 'srp_erp_warehouseitems.itemAutoID = srp_erp_itemmaster.itemAutoID');

        $this->datatables->where("NOT EXISTS(SELECT * FROM srp_erp_pos_promotionapplicableitems WHERE srp_erp_pos_promotionapplicableitems.itemAutoID = srp_erp_itemmaster.itemAutoID AND PromotionID = {$docID} AND companyID =" . $companyID . ")");
        $this->datatables->where('srp_erp_itemmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_itemmaster.isActive', 1);
        $this->datatables->where('srp_erp_itemmaster.masterApprovedYN', 1);
        $this->datatables->where_in('srp_erp_warehouseitems.wareHouseAutoID', $warehouseList);
        // $this->datatables->where('srp_erp_itemmaster.mainCategory != "Fixed Assets"');
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        } else {
            $this->datatables->where('srp_erp_itemmaster.mainCategory IN ("Inventory","Non Inventory","Service","Services")');
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'itemAutoID');
        $this->datatables->add_column('calculatedCurrentStock', '$1', 'calculateCurrentStock(itemAutoID,wareHouseAutoID)');
        echo $this->datatables->generate();
    }

    function add_discount_item()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'trim|required');
        $this->form_validation->set_rules('docID', 'Document ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Pos_config_model->add_discount_item());
        }
    }

    function fetch_discount_item_view()
    {
        $docID = $this->input->post('id');
        $compID = current_companyID();
        $promotionWarehousesQuery = $this->db->query("select * from srp_erp_pos_promotionwarehouses where promotionID=$docID");
        $warehouseListArray = array();
        if ($promotionWarehousesQuery->num_rows() > 0) {
            foreach ($promotionWarehousesQuery->result() as $warehouse) {
                array_push($warehouseListArray, $warehouse->wareHouseID);
            }
        }
        $warehouseList = implode(", ", $warehouseListArray);
        $this->datatables->select('srp_erp_pos_promotionapplicableitems.discountPercentage as discountPercentage,
        autoID as autoID,
         srp_erp_pos_promotionapplicableitems.itemAutoID as itemAutoID, 
         PromotionID as PromotionID, 
         srp_erp_itemmaster.itemSystemCode as itemSystemCode,
         srp_erp_itemmaster.itemName as itemName,
         srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,
         srp_erp_itemmaster.itemDescription as itemDescription,
         mainCategoryID, 
         mainCategory, 
         srp_erp_pos_promotionapplicableitems.isActive as promotionActive,
         srp_erp_itemcategory.description as SubCategoryDescription,
         srp_erp_warehouseitems.wareHouseAutoID as wareHouseAutoID', false)
            ->from('srp_erp_pos_promotionapplicableitems')
            ->join('srp_erp_itemmaster', 'srp_erp_pos_promotionapplicableitems.itemAutoID = srp_erp_itemmaster.itemAutoID')
            ->join('srp_erp_itemcategory', 'srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID')
            ->join('srp_erp_warehouseitems', 'srp_erp_warehouseitems.itemAutoID = srp_erp_itemmaster.itemAutoID');
        $this->datatables->where('PromotionID', $docID);
        $this->datatables->where('srp_erp_pos_promotionapplicableitems.companyID', $compID);
        $this->datatables->where('srp_erp_itemmaster.companyID', $compID);
        $this->datatables->where('srp_erp_itemmaster.isActive', 1);
        $this->datatables->where('srp_erp_itemmaster.masterApprovedYN', 1);
        $this->datatables->where_in('srp_erp_warehouseitems.wareHouseAutoID', $warehouseList);
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        } else {
            $this->datatables->where('srp_erp_itemmaster.mainCategory IN ("Inventory","Non Inventory","Service","Services")');
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->edit_column('active', '$1', 'load_status_discount_item(PromotionID, itemAutoID, promotionActive)');
        $this->datatables->add_column('percentage', '$1', 'percentageEdit(discountPercentage,autoID)');
        $this->datatables->add_column('calculatedCurrentStock', '$1', 'calculateCurrentStock(itemAutoID,wareHouseAutoID)');
        echo $this->datatables->generate();
    }

    function update_dicount_item_status()
    {
        $this->form_validation->set_rules('id', 'Document ID', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Pos_config_model->update_dicount_item_status());
        }
    }

    function update_discount_setup_status()
    {
        echo json_encode($this->Pos_config_model->update_discount_setup_status());
    }

    function save_edited_discount_percentage()
    {
        $selectedPromoItemId = $this->input->post('selectedPromoItemId', true);
        $discountPercentage = $this->input->post('discountPercentage', true);
        $promoDiscountItem = array(
            'discountPercentage' => $discountPercentage
        );
        $this->db->where('autoID', $selectedPromoItemId);
        $result = $this->db->update('srp_erp_pos_promotionapplicableitems', $promoDiscountItem);
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'successfully updated', 'result' => $result));
        }
    }

    function get_warehouses_with_menu_assigned_status()
    {
        $menuMasterID = $this->input->post('menuMasterID', true);
        $activeWarehouses = get_active_outletInfo();
        $companyID = current_companyID();
        $warehouseMenuMasterQuery = $this->db->query("SELECT * FROM `srp_erp_pos_warehousemenumaster` 
join srp_erp_warehousemaster on srp_erp_warehousemaster.wareHouseAutoID=srp_erp_pos_warehousemenumaster.warehouseID
where srp_erp_warehousemaster.companyID=$companyID 
and srp_erp_warehousemaster.isPosLocation=1
and srp_erp_warehousemaster.isActive=1
and srp_erp_pos_warehousemenumaster.isActive=1
and srp_erp_pos_warehousemenumaster.isDeleted=0
and srp_erp_pos_warehousemenumaster.menuMasterID=$menuMasterID");
        $menuAssignedWarehouses = array();
        foreach ($warehouseMenuMasterQuery->result() as $item) {
            array_push($menuAssignedWarehouses, $item->warehouseID);
        }
        $warehouseListWithMenuAssignedStatus = array();
        foreach ($activeWarehouses as $item) {
            if (in_array($item['wareHouseAutoID'], $menuAssignedWarehouses)) {
                $item['assigned'] = true;
            } else {
                $item['assigned'] = false;
            }
            array_push($warehouseListWithMenuAssignedStatus, $item);
        }
        echo json_encode($warehouseListWithMenuAssignedStatus);
    }

    function warehouseuser_button_access_details()
    {
        $companyID = current_companyID();
        $userID = $this->input->post('EIdNo', true);
        $wareHouseID = $this->input->post('wareHouseID', true);
        $query = $this->db->query("SELECT * FROM `srp_erp_warehouse_users` where wareHouseID=$wareHouseID and userID=$userID and companyID=$companyID and isActive=1");

        if ($query->num_rows() > 0) {
            $warehouseUserID = $query->row()->autoID;
        }

        $warehouseuserButtonAccessJoin = "srp_erp_pos_warehouseuser_button_access.buttonID=srp_erp_pos_buttonmaster.buttonID and srp_erp_pos_warehouseuser_button_access.warehouseUserID=$warehouseUserID";
        $action = "warehouseuserButtonEnableSwitch(userAccessID,isDisabled,buttonID,$warehouseUserID)";
        $this->datatables->select('srp_erp_pos_buttonmaster.buttonID as buttonID,
srp_erp_pos_buttonmaster.buttonText,
srp_erp_pos_warehouseuser_button_access.userAccessID as userAccessID,
srp_erp_pos_warehouseuser_button_access.warehouseUserID as warehouseUserID,
srp_erp_pos_warehouseuser_button_access.companyID,
srp_erp_pos_warehouseuser_button_access.isDisabled as isDisabled', false)
            ->from('srp_erp_pos_buttonmaster')
            ->join('srp_erp_pos_warehouseuser_button_access', $warehouseuserButtonAccessJoin, 'left')
            ->where('srp_erp_pos_buttonmaster.isDeleted', 0)
            ->add_column('action', '$1', $action);
        echo $this->datatables->generate();
    }

    function change_button_access_privilege()
    {
        $companyID = current_companyID();
        $user_access_id = $this->input->post('user_access_id', true);
        $status = $this->input->post('status', true);
        $warehouse_user_id = $this->input->post('warehouse_user_id', true);
        $button_id = $this->input->post('button_id', true);
        if ($user_access_id == '') {
            $record = array(
                "warehouseUserID" => $warehouse_user_id,
                "buttonID" => $button_id,
                "companyID" => $companyID,
                "isDisabled" => $status,
                "createdPCID" => current_pc(),
                "createdUserID" => current_userID(),
                "createdDateTime" => format_date_mysql_datetime(),
                "createdUserName" => current_user(),
                "createdUserGroup" => user_group(),
                "timestamp" => format_date_mysql_datetime()
            );
            $this->db->insert('srp_erp_pos_warehouseuser_button_access', $record);
        } else {
            $record = array(
                "warehouseUserID" => $warehouse_user_id,
                "buttonID" => $button_id,
                "companyID" => $companyID,
                "isDisabled" => $status,
                "modifiedPCID" => current_pc(),
                "modifiedUserID" => current_userID(),
                "modifiedUserName" => current_user(),
                "modifiedDateTime" => format_date_mysql_datetime(),
                "timestamp" => format_date_mysql_datetime()
            );
            $this->db->where('userAccessID', $user_access_id);
            $this->db->update('srp_erp_pos_warehouseuser_button_access', $record);
        }
        $error = $this->db->error();
        if ($error['code'] == 0) {
            $data['status'] = 'updated';
        } else {
            $data['status'] = 'error';
        }
        echo json_encode($data);
    }

    function save_new_payment_method()
    {
        $newPaymentName = $this->input->post('newPaymentName', true);
        $newPaymentType = $this->input->post('newPaymentType', true);
        $receivedIDsArray = ['1', '2', '3', '4', '5', '6', '7', '25', '32', '42'];

        //duplicate validation
        $spaceRemovedName = str_replace(' ', '', $newPaymentName);
        $query = $this->db->query("SELECT * FROM `srp_erp_pos_paymentglconfigmaster` where REPLACE(description, ' ', '') = '$spaceRemovedName'");
        if ($query->num_rows() > 0) {
            $errors['message'] = "Payment type already exist with the same name.";
            $errors['status'] = 'error';
            echo json_encode($errors);
            exit;
        }

        if (isset($_FILES['newPaymentIcon'])) {
            $errors = array();
            $file_name = $_FILES['newPaymentIcon']['name'];
            $file_size = $_FILES['newPaymentIcon']['size'];
            $file_tmp = $_FILES['newPaymentIcon']['tmp_name'];
            $file_type = $_FILES['newPaymentIcon']['type'];
            $ex = explode('.', $_FILES['newPaymentIcon']['name']);
            $file_ext = strtolower(end($ex));

            $extensions = array("jpeg", "jpg", "png");

            if (in_array($file_ext, $extensions) === false) {
                $errors['message'] = "Extension not allowed, please choose a JPEG or PNG file.";
                $errors['status'] = 'error';
                echo json_encode($errors);
                exit;
            }

            if ($file_size > 2097152) {
                $errors[] = 'File size must be excately 2 MB';
                $errors['status'] = 'error';
                echo json_encode($errors);
                exit;
            }

            if (empty($errors) == true) {
                $file_name = strtotime('now') . '.' . $file_ext;
                move_uploaded_file($file_tmp, "images/payment_type/" . $file_name);
            }
        } else {
            $file_name = '4-32.png';
        }

        $selectBoxName = str_replace(' ', '', $newPaymentName);
        $filePath = 'images/payment_type/' . $file_name;

        $lastId = $this->db->query("SELECT autoID FROM `srp_erp_pos_paymentglconfigmaster` ORDER BY autoID desc limit 1")->row('autoID');
        $lastId++;
        while (in_array($lastId, $receivedIDsArray)) {
            $lastId++;
        };

        $paymentMaster = array(
            "autoID" => $lastId,
            "description" => $newPaymentName,
            "glAccountType" => $newPaymentType,
            "image" => $filePath,
            "selectBoxName" => $selectBoxName,
            "timesstamp" => format_date_mysql_datetime()
        );


        $this->db->insert('srp_erp_pos_paymentglconfigmaster', $paymentMaster);
        $lastId = $this->db->insert_id();
        $paymentMaster = array(
            "sortOrder" => $lastId
        );
        $this->db->where('autoID', $lastId);
        $this->db->update('srp_erp_pos_paymentglconfigmaster', $paymentMaster);

        $error = $this->db->error();
        if ($error['code'] == 0) {
            $data['status'] = 'updated';
            $data['paymentDropdown'] = form_dropdown('paymentConfigMasterID', get_payment_config_master_drop(), '', 'id="paymentConfigMasterID"  class="form-control select2" onchange="ajax_load_chartOfAccountData(this)" ');
        } else {
            $errors['message'] = "DB Error.";
            $data['status'] = 'error';
        }
        echo json_encode($data);
    }

    function modify_payment_method()
    {
        $newPaymentName = $this->input->post('newPaymentName', true);
        $newPaymentType = $this->input->post('newPaymentType', true);
        $id = $this->input->post('id', true);
        $receivedIDsArray = ['1', '2', '3', '4', '5', '6', '7', '25', '32', '42'];

        //duplicate validation
        $spaceRemovedName = str_replace(' ', '', $newPaymentName);
        $query = $this->db->query("SELECT * FROM `srp_erp_pos_paymentglconfigmaster` where autoID!=$id and REPLACE(description, ' ', '') = '$spaceRemovedName'");
        if ($query->num_rows() > 0) {
            $errors['message'] = "Payment type already exist with the same name.";
            $errors['status'] = 'error';
            echo json_encode($errors);
            exit;
        }

        if (isset($_FILES['newPaymentIcon'])) {
            $errors = array();
            $file_name = $_FILES['newPaymentIcon']['name'];
            $file_size = $_FILES['newPaymentIcon']['size'];
            $file_tmp = $_FILES['newPaymentIcon']['tmp_name'];
            $file_type = $_FILES['newPaymentIcon']['type'];
            $ex = explode('.', $_FILES['newPaymentIcon']['name']);
            $file_ext = strtolower(end($ex));

            $extensions = array("jpeg", "jpg", "png");

            if (in_array($file_ext, $extensions) === false) {
                $errors['message'] = "Extension not allowed, please choose a JPEG or PNG file.";
                $errors['status'] = 'error';
                echo json_encode($errors);
                exit;
            }

            if ($file_size > 2097152) {
                $errors[] = 'File size must be excately 2 MB';
                $errors['status'] = 'error';
                echo json_encode($errors);
                exit;
            }

            if (empty($errors) == true) {
                $file_name = strtotime('now') . '.' . $file_ext;
                move_uploaded_file($file_tmp, "images/payment_type/" . $file_name);
            }
        } else {
            $file_name = '4-32.png';
        }

        $selectBoxName = str_replace(' ', '', $newPaymentName);
        $filePath = 'images/payment_type/' . $file_name;

        $paymentMaster = array(
            "description" => $newPaymentName,
            "glAccountType" => $newPaymentType,
            "image" => $filePath,
            "selectBoxName" => $selectBoxName,
            "timesstamp" => format_date_mysql_datetime()
        );


        $this->db->where('autoID',$id);
        $this->db->update('srp_erp_pos_paymentglconfigmaster', $paymentMaster);


        $error = $this->db->error();
        if ($error['code'] == 0) {
            $data['status'] = 'updated';
            $data['paymentDropdown'] = form_dropdown('paymentConfigMasterID', get_payment_config_master_drop(), '', 'id="paymentConfigMasterID"  class="form-control select2" onchange="ajax_load_chartOfAccountData(this)" ');
        } else {
            $errors['message'] = "DB Error.";
            $data['status'] = 'error';
        }
        echo json_encode($data);
    }

    function load_payment_master()
    {
        $this->datatables->select('autoID,description,isActive', false)
            ->from('srp_erp_pos_paymentglconfigmaster')
            ->add_column('enableDisable', '$1', 'paymentMasterEnableDisable(autoID,isActive)')
            ->add_column('action', '$1', 'paymentMasterAction(autoID)');
        echo $this->datatables->generate();
    }

    function delete_payment_master()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $dontDeleteIDsArray = ['1', '2', '3', '4', '5', '6', '7', '25', '32', '42'];
        if ($status == 0) {
            if (in_array($id, $dontDeleteIDsArray)) {
                $data['status'] = 'error';
                $data['message'] = 'Cannot deactivate this payment method.';
                echo json_encode($data);
                exit;
            } else {
                $query = $this->db->query("SELECT * FROM `srp_erp_pos_paymentglconfigdetail` where paymentConfigMasterID=$id");
                if ($query->num_rows() > 0) {
                    $data['status'] = 'error';
                    $data['message'] = 'Cannot deactivate this payment method.';
                    echo json_encode($data);
                    exit;
                } else {
                    $update = array('isActive' => 0);
                    $this->db->where('autoID', $id);
                    $this->db->update('srp_erp_pos_paymentglconfigmaster', $update);
                    $error = $this->db->error();
                    if ($error['code'] == 0) {
                        $data['message'] = 'Successfully deactivated.';
                        $data['status'] = 'updated';
                    } else {
                        $errors['message'] = "DB Error.";
                        $data['status'] = 'error';
                    }
                    echo json_encode($data);
                }
            }
        } elseif ($status == 1) {
            $update = array('isActive' => 1);
            $this->db->where('autoID', $id);
            $this->db->update('srp_erp_pos_paymentglconfigmaster', $update);
            $error = $this->db->error();
            if ($error['code'] == 0) {
                $data['message'] = 'Successfully activated.';
                $data['status'] = 'updated';
            } else {
                $errors['message'] = "DB Error.";
                $data['status'] = 'error';
            }
            echo json_encode($data);
        }
    }

    function get_payment_method_details(){
        $id=$this->input->post('id',true);
        $query=$this->db->query("SELECT * FROM `srp_erp_pos_paymentglconfigmaster` WHERE autoID=$id");
        echo json_encode($query->row());
    }

    function add_menuVat(){
        $this->form_validation->set_rules('menuVatPercentage', 'VAT Percentage', 'trim|required');
        $this->form_validation->set_rules('vatType2', 'VAT Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));

        }else{
            $result = $this->Pos_config_model->add_menuVat();
            echo json_encode($result);
        }
    }
}