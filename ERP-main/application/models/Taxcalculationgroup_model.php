<?php

class Taxcalculationgroup_model extends ERP_Model
{

    function save_tax_calculation_header()
    {
        $this->db->trans_start();
        if($this->input->post('taxCalculationformulaID')){
            $this->db->select('taxCalculationformulaID');
            $this->db->where('taxType', $this->input->post('taxType'));
            $this->db->where('Description', $this->input->post('Description'));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('taxCalculationformulaID !=', $this->input->post('taxCalculationformulaID'));
            $descexist= $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
            if(!empty($descexist)){
                return array('e','Description Already Exist');
                exit;
            }
        }else{
            $this->db->select('taxCalculationformulaID');
            $this->db->where('taxType', $this->input->post('taxType'));
            $this->db->where('Description', $this->input->post('Description'));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $descexist= $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
            if(!empty($descexist)){
                return array('e','Description Already Exist');
                exit;
            }
        }
        $isClaimable = 0;
        if (!empty($this->input->post('isClaimable'))) {
            $isClaimable = 1;
        }
        $data['Description'] = trim($this->input->post('Description') ?? '');
        $data['taxType'] = trim($this->input->post('taxType') ?? '');
        $data['vatTypeID'] = trim($this->input->post('vatType') ?? '');
        $data['isClaimable'] = $isClaimable;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('taxCalculationformulaID') ?? '')) {
            $this->db->where('taxCalculationformulaID', trim($this->input->post('taxCalculationformulaID') ?? ''));
            $this->db->update('srp_erp_taxcalculationformulamaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e',$this->db->_error_message());
            } else {
                return array('s','Updated Successfully');
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['taxVatMainCategoriesAutoID'] = $this->input->post('taxVatMainCategoriesAutoID');
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_taxcalculationformulamaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e',$this->db->_error_message());
            } else {
                return array('s','Saved Successfully');
            }
        }
    }

    function load_calculation_group()
    {
        $this->db->select('*');
        $this->db->where('taxCalculationformulaID', $this->input->post('taxCalculationformulaID'));
        return $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
    }

    function save_tax_formula_detail_form(){
        $this->db->trans_start();
        if($this->input->post('formulaDetailID')){
            $this->db->select('taxCalculationformulaID');
            $this->db->where('taxCalculationformulaID', $this->input->post('taxCalculationformulaID'));
            $this->db->where('description', $this->input->post('description'));
            $this->db->where('sortOrder', $this->input->post('sortOrder'));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('formulaDetailID !=', $this->input->post('formulaDetailID'));
            $descexist= $this->db->get('srp_erp_taxcalculationformuladetails')->row_array();
            if(!empty($descexist)){
                return array('e','Description Already Exist');
                exit;
            }
        }else{
            $this->db->select('formulaDetailID');
            $this->db->where('taxCalculationformulaID', $this->input->post('taxCalculationformulaID'));
            $this->db->where('description', $this->input->post('description'));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $descexist= $this->db->get('srp_erp_taxcalculationformuladetails')->row_array();
            if(!empty($descexist)){
                return array('e','Description Already Exist');
                exit;
            }
        }

        if($this->input->post('formulaDetailID')){
            $this->db->select('taxMasters');
            $this->db->where('formulaDetailID', $this->input->post('formulaDetailID'));
            $descexist= $this->db->get('srp_erp_taxcalculationformuladetails')->row_array();

            $this->db->select('sortOrder');
            $this->db->where('formulaDetailID', $this->input->post('formulaDetailID'));
            $maxsort= $this->db->get('srp_erp_taxcalculationformuladetails')->row_array();

            /*if(!empty($descexist)){
                if($maxsort['sortOrder']>$this->input->post('sortOrder')){
                    echo $maxsort['sortOrder'];
                    return array('e','Sort order can not be changed');
                    exit;
                }

            }*/
        }

        $data['taxCalculationformulaID'] = trim($this->input->post('taxCalculationformulaID') ?? '');
        $data['taxMasterAutoID'] = trim($this->input->post('taxMasterAutoID') ?? '');
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['sortOrder'] = trim($this->input->post('sortOrder') ?? '');
        $data['taxPercentage'] = trim($this->input->post('taxpercentage') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('formulaDetailID') ?? '')) {
            $this->db->where('formulaDetailID', trim($this->input->post('formulaDetailID') ?? ''));
            $this->db->update('srp_erp_taxcalculationformuladetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e',$this->db->_error_message());
            } else {
                return array('s','Updated Successfully');
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_taxcalculationformuladetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e',$this->db->_error_message());
            } else {
                return array('s','Saved Successfully');
            }
        }
    }

    function load_formula_detail(){
        $this->db->select('*');
        $this->db->where('formulaDetailID', $this->input->post('formulaDetailID'));
        return $this->db->get('srp_erp_taxcalculationformuladetails')->row_array();
    }

    function saveFormula_tax(){
        $formulaString = $this->input->post('formulaString');
        $formula = $this->input->post('formula');
        $payGroupID = $this->input->post('payGroupID');
        $salaryCategories = $this->input->post('salaryCategoryContainer');
        $salaryCategories = (trim($salaryCategories) == '') ? null : $salaryCategories;
        $ssoCategories = $this->input->post('SSOContainer');
        $ssoCategories = (trim($ssoCategories) == '') ? null : $ssoCategories;
        $payGroupCategories = $this->input->post('payGroupContainer');
        $payGroupCategories = (trim($payGroupCategories) == '') ? null : $payGroupCategories;
        $companyID = current_companyID();
        $createdUserName = current_employee();
        $current_date = current_date();
        $current_pc = current_pc();
        $user_id = current_userID();
        $user_group = current_user_group();

       /* $data['payGroupID'] = $payGroupID;
        $data['formulaString'] = $formulaString;
        $data['ssoCategories'] = $ssoCategories;
        $data['payGroupCategories'] = $payGroupCategories;*/
        $data['formula'] = $formulaString;
        $data['taxMasters'] = $salaryCategories;
        $data['modifiedPCID'] = $current_pc;
        $data['modifiedUserID'] = $user_id;
        $data['modifiedDateTime'] = $current_date;
        $data['modifiedUserName'] = $createdUserName;
        $data['timestamp'] = $current_date;
        $this->db->where('formulaDetailID', $payGroupID)->where('companyID', $companyID)->update('srp_erp_taxcalculationformuladetails', $data);
        return ['s', 'Formula updated successfully.'];
    }

    function update_item_taxid(){
        $selectedItems=$this->input->post('selectedItems');
        $selectedItemsnotSync=$this->input->post('selectedItemsnotSync');
        $taxCalculationformulaID=$this->input->post('taxCalculationformulaID');
        $taxType=$this->input->post('taxType');
        /*if condition to check sale or purchase id and assign the id to type*/
        $type = ($taxType==1)? 'salesTaxFormulaID': 'purchaseTaxFormulaID';
        /*database transaction starts*/
        $this->db->trans_start();

        if(!empty($selectedItemsnotSync)){
            foreach($selectedItemsnotSync as $valu){
                $data[$type] = null;

                $this->db->where('itemAutoID', trim($valu));
                $this->db->update('srp_erp_itemmaster', $data);

                $this->db->where([
                    'itemAutoID'=>$valu, 'taxType'=> $taxType, 'taxFormulaID'=>$taxCalculationformulaID
                ])->delete('srp_erp_itemtaxformula');
            }
        }

        if(!empty($selectedItems)){
            /*** Get exists records***/
            $exist = $this->db->select('itemAutoID')->from('srp_erp_itemtaxformula')
                ->where([ 'taxType'=> $taxType, 'taxFormulaID'=>$taxCalculationformulaID])
                ->where_in('itemAutoID', $selectedItems)
                ->get()->result_array();

            /*$exits val will be assigned by the item auto id*/
            $exist = array_group_by($exist, 'itemAutoID');

            $int_arr = []; $dateTime = $this->common_data['current_date'];
            foreach($selectedItems as $val){
                $data[$type] = $taxCalculationformulaID;

                $this->db->where('itemAutoID', trim($val));
                $this->db->update('srp_erp_itemmaster', $data);


                if(!array_key_exists($val, $exist)){
                    $int_arr[] = [
                        'itemAutoID'=> $val, 'taxType' => $taxType, 'taxFormulaID' => $taxCalculationformulaID,
                        'createdPCID'=> $this->common_data['current_pc'], 'createdUserID'=> $this->common_data['current_userID'],
                        'createdUserName'=> $this->common_data['current_user'], 'createdDateTime'=> $dateTime
                    ];
                }

            }

            if(!empty($int_arr)){
                $this->db->insert_batch('srp_erp_itemtaxformula', $int_arr);
            }

        }

        $this->db->trans_complete();

        if($this->db->trans_status() != false ){
            return array('s','Update successfully');
        }
        else{
            return array('e','Error in process');
        }

    }

    function save_item_tax_formula(){
        $this->db->trans_start();
        if($this->input->post('itemTaxformulaID')){
            $this->db->select('itemTaxformulaID, itemAutoID,');
            $this->db->where('taxType', $this->input->post('taxType'));
            $this->db->where('Description', $this->input->post('Description'));
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('taxCalculationformulaID !=', $this->input->post('taxCalculationformulaID'));
            $this->db->get('srp_erp_itemtaxformula')->row_array();

        }
    }

    function taxCalculationView(){
        $data['view'] = $this->load->view('system/tax/tax_detail_view.php');
        return $data;

    }

}