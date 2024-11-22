<?php

class Access_menu_model extends CI_Model
{

    /*function loadWidet()
    {
        $result = $this->db->query("SELECT widgetID,widgetName from srp_erp_widgetmaster")->result_array();
        return $result;
    }*/

    function load_master(){
        $masterID=$this->input->post('modules');
        $result = $this->db->query("SELECT navigationMenuID,description,masterID from srp_erp_navigationmenus WHERE masterID='$masterID' and isSubExist=1 ")->result_array();
        return $result;
    }

    /*function save_navigation(){
        $level=$this->input->post('level');
        $icon=$this->input->post('icon');
        $pagetitle=$this->input->post('pagetitle');
        $description=$this->input->post('description');
        $subexist=$this->input->post('subexist');
        $type=$this->input->post('type');
        if($level==0){
            $sortOrder = $this->db->query("SELECT  MAX(sortOrder) as sortOrder from srp_erp_navigationmenus WHERE masterID IS NULL ")->row_array();
            $data = array(
                'description' => $description,
                //'masterID' => null,
                'url' => $this->input->post('url'),
                'pageTitle' => $pagetitle,
                'pageIcon' => $icon,
                'levelNo' => $level,
                'sortOrder' => $sortOrder['sortOrder']+1,
                'isSubExist' => $subexist,
                'addonDescription' => $description,
            );
            $result=$this->db->insert('srp_erp_navigationmenus', $data);
            $lastNavigationMenuID=$this->db->insert_id();
            if($result){
                if($subexist==0){
                    $dataFrmCat = array(
                        'Category' => $description,
                        'navigationMenuID' => $lastNavigationMenuID,
                    );
                    $resultFrmCat=$this->db->insert('srp_erp_formcategory', $dataFrmCat);
                    $lastFormCatID=$this->db->insert_id();
                    if($resultFrmCat){
                        $dataTempMaster = array(
                            'TempDes' => $description,
                            'TempPageName' => $pagetitle,
                            'TempPageNameLink' => $this->input->post('url'),
                            'FormCatID' => $lastFormCatID,
                            'isDefault' => 1,
                        );
                        $resultTemMaster=$this->db->insert('srp_erp_templatemaster', $dataTempMaster);
                        $lastTempMasterID=$this->db->insert_id();
                        if($resultTemMaster){
                            $companies = $this->db->query("SELECT  company_id  from srp_erp_company ")->result_array();
                            foreach($companies as $valu){
                                $dataTempMaster = array(
                                    'companyID' => $valu['company_id'],
                                    'TempMasterID' => $lastTempMasterID,
                                    'FormCatID' => $lastFormCatID,
                                    'navigationMenuID' => $lastNavigationMenuID,
                                );
                                $this->db->insert('srp_erp_templates', $dataTempMaster);
                            }
                            return array('s', ' Saved Successfully ');
                        }
                    }
                }else{
                    return array('s', ' Saved Successfully ');
                }
            }else{
                return array('s', ' Error in saving record ');
            }
        }else if($level==1){
            $modules=$this->input->post('modules');
            $url=$this->input->post('url');
            $sortOrder = $this->db->query("SELECT  IFNULL(MAX(sortOrder),0) as sortOrder from srp_erp_navigationmenus WHERE masterID='$modules' ")->row_array();
            $data = array(
                'description' => $description,
                'masterID' => $modules,
                'url' => $url,
                'pageTitle' => $pagetitle,
                'pageIcon' => $icon,
                'levelNo' => $level,
                'sortOrder' => $sortOrder['sortOrder']+1,
                'isSubExist' => $subexist,
                'addonDescription' => $description,
            );
            $result=$this->db->insert('srp_erp_navigationmenus', $data);
            $lastNavigationMenuID=$this->db->insert_id();
            if($result){
                if($subexist==0){
                    $dataFrmCat = array(
                        'Category' => $description,
                        'navigationMenuID' => $lastNavigationMenuID,
                    );
                    $resultFrmCat=$this->db->insert('srp_erp_formcategory', $dataFrmCat);
                    $lastFormCatID=$this->db->insert_id();
                    if($resultFrmCat){
                        $dataTempMaster = array(
                            'TempDes' => $description,
                            'TempPageName' => $pagetitle,
                            'TempPageNameLink' => $this->input->post('url'),
                            'FormCatID' => $lastFormCatID,
                            'isDefault' => 1,
                        );
                        $resultTemMaster=$this->db->insert('srp_erp_templatemaster', $dataTempMaster);
                        $lastTempMasterID=$this->db->insert_id();
                        if($resultTemMaster){
                            $companies = $this->db->query("SELECT  company_id  from srp_erp_company ")->result_array();
                            foreach($companies as $valu){
                                $dataTempMaster = array(
                                    'companyID' => $valu['company_id'],
                                    'TempMasterID' => $lastTempMasterID,
                                    'FormCatID' => $lastFormCatID,
                                    'navigationMenuID' => $lastNavigationMenuID,
                                );
                                $this->db->insert('srp_erp_templates', $dataTempMaster);
                            }
                            return array('s', ' Saved Successfully ');
                        }
                    }
                }else{
                    return array('s', ' Saved Successfully ');
                }
            }else{
                return array('s', ' Error in saving record ');
            }
        }else if($level==2){
            $modules=$this->input->post('modules');
            $masters=$this->input->post('masters');
            $url=$this->input->post('url');
            $sortOrder = $this->db->query("SELECT  IFNULL(MAX(sortOrder),0) as sortOrder from srp_erp_navigationmenus WHERE masterID='$masters' ")->row_array();
            $data = array(
                'description' => $description,
                'masterID' => $masters,
                'url' => $url,
                'pageTitle' => $pagetitle,
                'pageIcon' => $icon,
                'levelNo' => $level,
                'sortOrder' => $sortOrder['sortOrder']+1,
                'isSubExist' => 0,
                'addonDescription' => $description,
            );
            $result=$this->db->insert('srp_erp_navigationmenus', $data);
            $lastNavigationMenuID=$this->db->insert_id();
            if($result){
                if($subexist==0){
                    $dataFrmCat = array(
                        'Category' => $description,
                        'navigationMenuID' => $lastNavigationMenuID,
                    );
                    $resultFrmCat=$this->db->insert('srp_erp_formcategory', $dataFrmCat);
                    $lastFormCatID=$this->db->insert_id();
                    if($resultFrmCat){
                        $dataTempMaster = array(
                            'TempDes' => $description,
                            'TempPageName' => $pagetitle,
                            'TempPageNameLink' => $this->input->post('url'),
                            'FormCatID' => $lastFormCatID,
                            'isDefault' => 1,
                        );
                        $resultTemMaster=$this->db->insert('srp_erp_templatemaster', $dataTempMaster);
                        $lastTempMasterID=$this->db->insert_id();
                        if($resultTemMaster){
                            $companies = $this->db->query("SELECT  company_id  from srp_erp_company ")->result_array();
                            foreach($companies as $valu){
                                $dataTempMaster = array(
                                    'companyID' => $valu['company_id'],
                                    'TempMasterID' => $lastTempMasterID,
                                    'FormCatID' => $lastFormCatID,
                                    'navigationMenuID' => $lastNavigationMenuID,
                                );
                                $this->db->insert('srp_erp_templates', $dataTempMaster);
                            }
                            return array('s', ' Saved Successfully ');
                        }
                    }
                }else{
                    return array('s', ' Saved Successfully ');
                }
            }else{
                return array('s', ' Error in saving record ');
            }
        }
    }*/


}