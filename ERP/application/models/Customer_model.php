<?php
class Customer_model extends ERP_Model{

    
    function save_customer()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $customercode = $this->input->post('customercode');
        $customerAutoID = $this->input->post('customerAutoID');
        $customerTelephone = trim($this->input->post('customerTelephone') ?? '');
        $isSync = $this->input->post('isSync');
        $inter_company = $this->input->post('inter_company');

        $this->db->select('customerAutoID');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('interCompanyID', $inter_company);
        $this->db->where('interCompayYN',1);
        $query = $this->db->get();
        $isCompany = $query->result();

        if($isCompany){
            $this->session->set_flashdata('e', 'This company already inter connected');
            return array('status' => false);
        }

        if (!$customerAutoID) {
            $validate_customercode = $this->db->query("SELECT COUNT(customerAutoID) as customerAutoID FROM `srp_erp_customermaster` WHERE companyID = {$companyID} AND secondaryCode LIKE  '" . $customercode . "'")->row_array();
            $customerTelephonevalidation = $this->db->query("SELECT IF(customerTelephone!='',customerTelephone,0) as customerTelephone from srp_erp_customermaster where 
                                                                companyID = $companyID AND customerTelephone LIKE '{$customerTelephone}' HAVING customerTelephone > 0")->row_array();


        } else {
            $validate_customercode = $this->db->query("SELECT COUNT(customerAutoID) as customerAutoID FROM `srp_erp_customermaster` WHERE companyID = {$companyID} AND  secondaryCode LIKE  '" . $customercode . "' AND customerAutoID <> {$customerAutoID}")->row_array();


            $customerTelephonevalidation = $this->db->query("SELECT IF(customerTelephone!='',customerTelephone,0) as customerTelephone from srp_erp_customermaster where  customerAutoID <> {$customerAutoID} AND
                                                                companyID = $companyID AND customerTelephone LIKE '{$customerTelephone}' HAVING customerTelephone > 0")->row_array();

        }
        if(!empty($customerTelephonevalidation['customerTelephone'])) {
            $this->session->set_flashdata('e', 'Customer Telephone Number  Already Exist');
            return array('status' => false);
        }

  

        if(!empty($validate_customercode['customerAutoID'])){
            $this->session->set_flashdata('e', 'Customer Secondary Code Already Exist');
            return array('status' => false);
        } else {

            $this->db->trans_start();
            $isactive = 0;
            if (!empty($this->input->post('isActive'))) {
                $isactive = 1;
            }
            $liability = fetch_gl_account_desc(trim($this->input->post('receivableAccount') ?? ''));
            $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
            $data['isActive'] = $isactive;
            $data['secondaryCode'] = trim($this->input->post('customercode') ?? '');
            $data['masterID'] = trim($this->input->post('masterID') ?? '');
            if(!empty($data['masterID'])){
                $data['levelNo'] = 1;
            }else{
                $data['levelNo'] = null;
                $data['masterID'] = null;
            }
            $data['rebateGLAutoID'] = trim($this->input->post('rebateGL') ?? '');
            $data['rebatePercentage'] = trim($this->input->post('rebatePercentage') ?? '');
            $data['customerName'] = trim($this->input->post('customerName') ?? '');

            $customercountry = trim($this->input->post('customercountry') ?? '');
            $customercountryID = $this->db->query("SELECT CountryID FROM srp_erp_countrymaster WHERE CountryDes = '{$customercountry}'")->row('CountryID');
            $data['customerCountryID'] = trim($customercountryID);
            $data['customerCountry'] = trim($this->input->post('customercountry') ?? '');
            $data['customerTelephone'] = trim($this->input->post('customerTelephone') ?? '');
            $data['customerEmail'] = trim($this->input->post('customerEmail') ?? '');
            $data['customerUrl'] = trim($this->input->post('customerUrl') ?? '');
            $data['customerFax'] = trim($this->input->post('customerFax') ?? '');
            $data['customerAddress1'] = trim($this->input->post('customerAddress1') ?? '');
            $data['customerAddress2'] = trim($this->input->post('customerAddress2') ?? '');
            $data['taxGroupID'] = trim($this->input->post('customertaxgroup') ?? '');
            $data['vatIdNo'] = trim($this->input->post('vatIdNo') ?? '');
            $data['sVatNumber'] = trim($this->input->post('sVatNo') ?? '');
            $data['isSync'] = trim($isSync ?? '');

            $data['vatEligible'] = trim($this->input->post('vatEligible') ?? '');
            $data['vatNumber'] = trim($this->input->post('vatNumber') ?? '');
            $data['vatPercentage'] = trim($this->input->post('vatPercentage') ?? '');

            if($inter_company){
                $data['interCompanyID'] =  $inter_company ;
                $data['interCompayYN'] = 1;
            }
            else{
                $data['interCompanyID'] =  null ;
                $data['interCompayYN'] = 0;
            }

            $data['IdCardNumber'] = trim($this->input->post('IdCardNumber') ?? '');
            $data['partyCategoryID'] = trim($this->input->post('partyCategoryID') ?? '');
            $data['receivableAutoID'] = $liability['GLAutoID'];
            $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
            $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
            $data['receivableDescription'] = $liability['GLDescription'];
            $data['receivableType'] = $liability['subCategory'];
            $data['customerCreditPeriod'] = trim($this->input->post('customerCreditPeriod') ?? '');
            $data['customerCreditLimit'] = trim($this->input->post('customerCreditLimit') ?? '');
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

           
            $company_data = get_all_company_details();

            if (trim($this->input->post('customerAutoID') ?? '')) {
                $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
                $this->db->update('srp_erp_customermaster', $data);
                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $rebate = getPolicyValues('CMDS', 'All');

                    if($rebate==1 && $isSync == 1){
                        if($company_data['api_update_url'] != null){
                            $this->save_customer_master_sync($this->input->post('customerAutoID'),$companyID,$this->common_data['current_userID'],$this->common_data['current_date'],"update",$company_data['api_update_url']);
                        }else{
                            $this->session->set_flashdata('e', 'Customer : '.$data['customerName'].' Please enter company api update url '); 
                        }
                    }
                    
                    $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Updated Successfully.');
                    $this->db->trans_commit();

                    return array('status' => true, 'last_id' => $this->input->post('customerAutoID'));
                }
            } else {
                $this->load->library('sequence');
                $data['customerCurrencyID'] = trim($this->input->post('customerCurrency') ?? '');
                $data['customerCurrency'] = $currency_code[0];
                $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
                $this->db->insert('srp_erp_customermaster', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $rebate = getPolicyValues('CMDS', 'All');

                    if($rebate==1 && $isSync == 1){
                        if($company_data['api_create_url'] != null){
                            $this->save_customer_master_sync($last_id,$companyID,$this->common_data['current_userID'],$this->common_data['current_date'],"create",$company_data['api_create_url']);
                        }else{
                            $this->session->set_flashdata('e', 'Customer : '.$data['customerName'].' Please enter company api create url '); 
                        }
                    }

                    $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Saved Successfully.');
                    $this->db->trans_commit();

                    return array('status' => true, 'last_id' => $last_id);
                }
            }
        }
    }

    function save_customer_master_sync($customerId ,$companyid ,$currentUser,$currentDate,$type,$url){

        if($type=="create"){
            $data['customerId'] = $customerId;
            $data['status'] = 1;
            $data['companyId'] = $companyid;
            $data['createdBy'] = $currentUser;
            $data['createdAt'] = $currentDate;
            $data['venderStatus'] = 0;
            $data['type'] = $type;
            $data['callBackUrl'] = $url;
            $this->db->insert('srp_erp_customer_master_sync', $data);
          
        }else{
            $data['customerId'] = $customerId;
            $data['status'] = 1;
            $data['companyId'] = $companyid;
            $data['updatedBy'] = $currentUser;
            $data['updatedAt'] = $currentDate;
            $data['venderStatus'] = 0;
            $data['type'] = $type;
            $data['callBackUrl'] = $url;
            $this->db->insert('srp_erp_customer_master_sync', $data);
            //$this->db->trans_complete();
        }

    }


    function customer_sync_responce($company_id)
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_customer_master_sync');
        $CI->db->where('venderStatus', 0);
        $CI->db->where('companyId', $company_id);
        return $CI->db->get()->result_array();
    }

    function customer_sync($id)
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_customermaster');
        $CI->db->where('customerAutoID', $id);
        return $CI->db->get()->row_array();
    }

    function customer_sync_update($id,$status = 1){
        
        $data['venderStatus']=$status;
        $data['syncDate']=$this->common_data['current_date'];
        $this->db->where('id', $id);
        $this->db->update('srp_erp_customer_master_sync', $data);
        $this->db->trans_complete();

        return true;

    }

    function save_customer_buyback()
    {
        $customercode = $this->input->post('customercode');
        $customerAutoID = $this->input->post('customerAutoID');
        if (!$customerAutoID) {
            $validate_customercode = $this->db->query("SELECT COUNT(customerAutoID) as customerAutoID FROM `srp_erp_customermaster` WHERE secondaryCode LIKE  '" . $customercode . "'")->row_array();
        } else {
            $validate_customercode = $this->db->query("SELECT COUNT(customerAutoID) as customerAutoID FROM `srp_erp_customermaster` WHERE secondaryCode LIKE  '" . $customercode . "' AND customerAutoID <> {$customerAutoID}")->row_array();
        }
        if(!empty($validate_customercode['customerAutoID'])){
            $this->session->set_flashdata('e', 'Customer Secondary Code Already Exist');
            return array('status' => false);
        } else {
            $this->db->trans_start();
            $isactive = 0;
            if (!empty($this->input->post('isActive'))) {
                $isactive = 1;
            }
            $liability = fetch_gl_account_desc(trim($this->input->post('receivableAccount') ?? ''));
            $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
            $data['isActive'] = $isactive;
            $data['secondaryCode'] = trim($this->input->post('customercode') ?? '');
            $data['customerName'] = trim($this->input->post('customerName') ?? '');
            $data['customerCountry'] = trim($this->input->post('customercountry') ?? '');
            $data['customerTelephone'] = trim($this->input->post('customerTelephone') ?? '');
            $data['customerEmail'] = trim($this->input->post('customerEmail') ?? '');
            $data['customerUrl'] = trim($this->input->post('customerUrl') ?? '');
            $data['customerFax'] = trim($this->input->post('customerFax') ?? '');
            $data['customerAddress1'] = trim($this->input->post('customerAddress1') ?? '');
            $data['customerAddress2'] = trim($this->input->post('customerAddress2') ?? '');
            $data['taxGroupID'] = trim($this->input->post('customertaxgroup') ?? '');
            $data['vatEligible'] = trim($this->input->post('vatEligible') ?? '');
            $data['vatIdNo'] = trim($this->input->post('vatIdNo') ?? '');
            $data['IdCardNumber'] = trim($this->input->post('IdCardNumber') ?? '');
            $data['partyCategoryID'] = trim($this->input->post('partyCategoryID') ?? '');
            $data['receivableAutoID'] = $liability['GLAutoID'];
            $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
            $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
            $data['receivableDescription'] = $liability['GLDescription'];
            $data['receivableType'] = $liability['subCategory'];
            $data['customerCreditPeriod'] = trim($this->input->post('customerCreditPeriod') ?? '');
            $data['customerCreditLimit'] = trim($this->input->post('customerCreditLimit') ?? '');
            $data['locationID'] = trim($this->input->post('locationID') ?? '');
            $data['subLocationID'] = trim($this->input->post('subLocationID') ?? '');
            $data['rebateGLAutoID'] = trim($this->input->post('rebateGL') ?? '');
            $data['rebatePercentage'] = trim($this->input->post('rebatePercentage') ?? '');
            $data['creditToleranceAmount'] = trim($this->input->post('customerToleranceAmount') ?? '');
            $data['creditTolerancePercentage'] = trim($this->input->post('customerTolerancePercentage') ?? '');
            $data['sVatNumber'] = trim($this->input->post('sVatNo') ?? '');

            if (trim($this->input->post('customerAutoID') ?? '')) {
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
                $this->db->update('srp_erp_customermaster', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('customerAutoID'));
                }
            } else {
                $this->load->library('sequence');
                $data['customerCurrencyID'] = trim($this->input->post('customerCurrency') ?? '');
                $data['customerCurrency'] = $currency_code[0];
                $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
                $this->db->insert('srp_erp_customermaster', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Saved Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id);
                }
            }
        }
    }

    function fetch_sales_person_details(){
        $data = array();
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(datefrom,\''.$convertFormat.'\') AS datefrom,DATE_FORMAT(dateTo,\''.$convertFormat.'\') AS dateTo');
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        $data['detail'] = $this->db->get('srp_erp_salespersontarget')->result_array();
        return $data;
    }

    function load_customer_header()
    {
        $this->db->select('*');
        $this->db->where('customerAutoID', $this->input->post('customerAutoID'));
        return $this->db->get('srp_erp_customermaster')->row_array();
    }

    function laad_sale_person_header()
    {
        $this->db->select('*');
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        return $this->db->get('srp_erp_salespersonmaster')->row_array();
    }




    function delete_customer()
    {
        $customerAutoID = $this->input->post('customerAutoID');
        $data['deletedYN'] = 1;
        $data['isActive'] = 0;
        $data['deleteByEmpID'] = $this->common_data['current_userID'];
        $data['deletedDatetime'] = $this->common_data['current_date'];

        $this->db->where('customerAutoID', $customerAutoID);
        $this->db->update('srp_erp_customermaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            $this->session->set_flashdata('s', 'Customer Record Deleted Successfully');
            return true;
        } else {
            $this->session->set_flashdata('e', 'Failed to delete Customer');
        }

     /*   $this->db->where('customerAutoID', $this->input->post('customerAutoID'));
        $result = $this->db->delete('srp_erp_customermaster');
        $this->session->set_flashdata('s', 'Record Deleted Successfully');
        return true;*/
    }



    
    function delete_sales_person()
    {
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        $result = $this->db->delete('srp_erp_salespersonmaster');
        return array('status'=>1,'type'=>'s', 'message'=>'Record Deleted successfully');
    }

    function saveCategory()
    {
        if (empty($this->input->post('partyCategoryID'))) {
            $this->db->select('partyCategoryID');
            $this->db->where('categoryDescription', $this->input->post('categoryDescription'));
            $this->db->where('partyType', 1);
            $this->db->where('companyID', current_companyID());
            $category = $this->db->get('srp_erp_partycategories')->row_array();
            if (empty($category)) {
                $this->db->set('categoryDescription', $this->input->post('categoryDescription'));
                $this->db->set('partyType', 1);
                $this->db->set('companyID', current_companyID());
                $this->db->set('companyCode', current_companyCode());
                $this->db->set('createdUserGroup', current_user_group());
                $this->db->set('createdPCID', current_pc());
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserName', current_user());
                $this->db->set('createdDateTime', $this->common_data['current_date']);
                $result = $this->db->insert('srp_erp_partycategories');

                if ($result) {
                    return array('s', 'Record added successfully');
                } else {
                    return array('e', 'Error in adding Record');
                }
            } else {
                return array('e', 'Category Already Exist');
            }
        } else {
            $this->db->select('partyCategoryID');
            $this->db->where('categoryDescription', $this->input->post('categoryDescription'));
            $this->db->where('partyType', 1);
            $category = $this->db->get('srp_erp_partycategories')->row_array();
            if (empty($category)) {
                $data['categoryDescription'] = $this->input->post('categoryDescription');
                $data['modifiedPCID'] = current_pc();
                $data['modifiedUserID'] = current_userID();
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = current_user();

                $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
                $result = $this->db->update('srp_erp_partycategories', $data);


                if ($result) {
                    return array('s', 'Record Updated successfully');
                } else {
                    return array('e', 'Error in Updating Record');
                }
            } else {
                return array('e', 'Category Already Exist');
            }
        }
    }

    function laad_sale_target()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(datefrom,\''.$convertFormat.'\') AS datefrom,DATE_FORMAT(dateTo,\''.$convertFormat.'\') AS dateTo');
        $this->db->where('targetID', $this->input->post('targetID'));
        return $this->db->get('srp_erp_salespersontarget')->row_array();
    }

    function fetch_employee_detail()
    {
        $this->db->select('*');
        $this->db->where('EIdNo', $this->input->post('employee_id'));
        return $this->db->get('srp_employeesdetails')->row_array();
    }

    function getCategory()
    {
        $this->db->select('*');
        $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
        return $this->db->get('srp_erp_partycategories')->row_array();
    }

    function delete_category()
    {
        $this->db->where('partyCategoryID', $this->input->post('partyCategoryID'));
        $result = $this->db->delete('srp_erp_partycategories');
        if ($result) {
            return array('s', 'Record Deleted successfully');
        }
    }

    function delete_sales_target()
    {
        $this->db->where('targetID', $this->input->post('targetID'));
        $result = $this->db->delete('srp_erp_salespersontarget');
        return array('status'=>1,'type'=>'s', 'message'=>'Record Deleted successfully');
    }

    function fetch_template_data(){
        $data = array();
        $this->db->select('*');
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        $data['head'] = $this->db->get('srp_erp_salespersonmaster')->row_array();
        $this->db->select('*');
        $this->db->where('salesPersonID', $this->input->post('salesPersonID'));
        $data['detail'] = $this->db->get('srp_erp_salespersontarget')->result_array();
        return $data;
    }

    function img_uplode(){
        /*$attachment_file                = $_FILES["img_file"];
        $info                           = new SplFileInfo($_FILES["img_file"]["name"]);
        $fileName = 'rep_'.trim($this->input->post('salesPersonID') ?? '').'.'.$info->getExtension();
        $output_dir = "images/sales_person/";
        if (!file_exists($output_dir)) {
            mkdir("images/sales_person/", 007);
        } 
        move_uploaded_file($_FILES["img_file"]["tmp_name"],$output_dir.$fileName);  */

        $this->load->library('s3');
        $salesPersonID = $this->input->post('salesPersonID');
        $companyid = current_companyID();
        $itemimageexist = $this->db->query("SELECT
	salesPersonImage 
FROM
	`srp_erp_salespersonmaster`
	where 
	companyID = $companyid 
	AND salesPersonID = $salesPersonID")->row_array();
        if(!empty($itemimageexist))
        {
            $this->s3->delete($itemimageexist['salesPersonImage']);
        }
        $attachment_file                = $_FILES["img_file"];
        $info                           = new SplFileInfo($_FILES["img_file"]["name"]);
        $fileName = 'rep_'.trim($this->input->post('salesPersonID') ?? '').'_'.$this->common_data['company_data']['company_code']. '.'.$info->getExtension();

        $path = "images/sales_person/$fileName";
        $s3Upload = $this->s3->upload($attachment_file['tmp_name'], $path);

        if (!$s3Upload) {
            return array('e', "Error in document upload location configuration");
        }

        $this->db->where('salesPersonID', trim($this->input->post('salesPersonID') ?? ''));
        $this->db->update('srp_erp_salespersonmaster', array('salesPersonImage'=>$path));
        return array('status' => 1,'type' => 's','message' => 'image upload successfully');
    }

    //get sales target end amount
    function load_sales_target_endamount()
    {
        $this->db->select_max('toTargetAmount');
        $this->db->where('salesPersonID', $this->input->get('salesPersonID'));
        return $this->db->get('srp_erp_salespersontarget')->row_array();
    }

    function save_customer_percentage(){
        $updateArray = array();
        for($x = 0; $x < sizeof($this->input->post("customerAutoID")); $x++){
            $updateArray[] = array(
                'customerAutoID'=>$this->input->post("customerAutoID")[$x],
                'capAmount' => $this->input->post("capAmount")[$x],
                'finCompanyPercentage' => $this->input->post("finCompanyPercentage")[$x],
                'pvtCompanyPercentage' => $this->input->post("pvtCompanyPercentage")[$x],
            );
        }
        $this->db->trans_start();
        $this->db->update_batch('srp_erp_customermaster', $updateArray, 'customerAutoID');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e',"Percentage Update Failed");
        } else {
            $this->db->trans_commit();
            return array('s',"Percentage Updated Successfully");
        }
    }

    function save_customer_otherlang()
    {

        $customercode = $this->input->post('customercode');
        $customerAutoID = $this->input->post('customerAutoID');
        if (!$customerAutoID) {
            $validate_customercode = $this->db->query("SELECT COUNT(customerAutoID) as customerAutoID FROM `srp_erp_customermaster` WHERE secondaryCode LIKE  '" . $customercode . "'")->row_array();
        } else {
            $validate_customercode = $this->db->query("SELECT COUNT(customerAutoID) as customerAutoID FROM `srp_erp_customermaster` WHERE secondaryCode LIKE  '" . $customercode . "' AND customerAutoID <> {$customerAutoID}")->row_array();
        }
        if(!empty($validate_customercode['customerAutoID'])){
            $this->session->set_flashdata('e', 'Customer Secondary Code Already Exist');
            return array('status' => false);
        } else {
            $this->db->trans_start();
            $isactive = 0;
            if (!empty($this->input->post('isActive'))) {
                $isactive = 1;
            }
            $liability = fetch_gl_account_desc(trim($this->input->post('receivableAccount') ?? ''));
            $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
            $data['isActive'] = $isactive;
            $data['secondaryCode'] = trim($this->input->post('customercode') ?? '');
            $data['customerNameOtherLang'] = trim($this->input->post('customerNameothers') ?? '');
            $data['customerAddress1OtherLang'] = trim($this->input->post('customerAddress1others') ?? '');
            $data['customerAddress2OtherLang'] = trim($this->input->post('customerAddress2othres') ?? '');
            $data['rebateGLAutoID'] = trim($this->input->post('rebateGL') ?? '');
            $data['rebatePercentage'] = trim($this->input->post('rebatePercentage') ?? '');
            $data['customerName'] = trim($this->input->post('customerName') ?? '');
            $data['customerCountry'] = trim($this->input->post('customercountry') ?? '');
            $data['customerTelephone'] = trim($this->input->post('customerTelephone') ?? '');
            $data['customerEmail'] = trim($this->input->post('customerEmail') ?? '');
            $data['customerUrl'] = trim($this->input->post('customerUrl') ?? '');
            $data['customerFax'] = trim($this->input->post('customerFax') ?? '');
            $data['customerAddress1'] = trim($this->input->post('customerAddress1') ?? '');
            $data['customerAddress2'] = trim($this->input->post('customerAddress2') ?? '');
            $data['taxGroupID'] = trim($this->input->post('customertaxgroup') ?? '');
            $data['vatEligible'] = trim($this->input->post('vatEligible') ?? '');
            $data['vatIdNo'] = trim($this->input->post('vatIdNo') ?? '');
            $data['IdCardNumber'] = trim($this->input->post('IdCardNumber') ?? '');
            $data['partyCategoryID'] = trim($this->input->post('partyCategoryID') ?? '');
            $data['receivableAutoID'] = $liability['GLAutoID'];
            $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
            $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
            $data['receivableDescription'] = $liability['GLDescription'];
            $data['receivableType'] = $liability['subCategory'];
            $data['customerCreditPeriod'] = trim($this->input->post('customerCreditPeriod') ?? '');
            $data['customerCreditLimit'] = trim($this->input->post('customerCreditLimit') ?? '');
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($this->input->post('customerAutoID') ?? '')) {
                $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
                $this->db->update('srp_erp_customermaster', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('customerAutoID'));
                }
            } else {
                $this->load->library('sequence');
                $data['customerCurrencyID'] = trim($this->input->post('customerCurrency') ?? '');
                $data['customerCurrency'] = $currency_code[0];
                $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
                $this->db->insert('srp_erp_customermaster', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Customer : ' . $data['customerName'] . ' Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Customer : ' . $data['customerName'] . ' Saved Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id);
                }
            }
        }
    }

    function save_customerPriceSetup_header()
    {
        $date_format_policy = date_format_policy();
        $docDate = $this->input->post('documentDate');
        $documentDate = input_format_date($docDate, $date_format_policy);

        $cpsAutoID = $this->input->post('cpsAutoID');
        $narration = $this->input->post('narration');
        $customerAutoID = $this->input->post('customerAutoID');
        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];

        $data['documentDate'] = $documentDate;
        $data['narration'] = $narration;
        $data['customerAutoID'] = $customerAutoID;

     if($cpsAutoID){
         $data['modifiedPCID'] = $this->common_data['current_pc'];
         $data['modifiedUserID'] = $this->common_data['current_userID'];
         $data['modifiedUserName'] = $this->common_data['current_user'];
         $data['modifiedDateTime'] = $this->common_data['current_date'];
         $this->db->where('cpsAutoID', $cpsAutoID);
         $this->db->update('srp_erp_customeritempricesetup', $data);
         $last_id = $cpsAutoID;

     } else {

         $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM srp_erp_customeritempricesetup WHERE  companyID = {$companyID}")->row_array();
         $data['serialNo'] = $serial['serialNo'];
         $data['documentSystemCode'] = ($company_code . '/' . 'CPS' . str_pad($data['serialNo'], 6, '0', STR_PAD_LEFT));
         $data['documentID'] = 'CPS';

         $data['companyID'] = $this->common_data['company_data']['company_id'];
         $data['createdUserGroup'] = $this->common_data['user_group'];
         $data['createdPCID'] = $this->common_data['current_pc'];
         $data['createdUserID'] = $this->common_data['current_userID'];
         $data['createdDateTime'] = $this->common_data['current_date'];
         $data['createdUserName'] = $this->common_data['current_user'];
         $this->db->insert('srp_erp_customeritempricesetup', $data);
         $last_id = $this->db->insert_id();
     }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sales Price :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sales Price :  Saved Successfully.',$last_id);
        }
    }

    function save_customerPriceSetup_header_new()
    {
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $docDate = $this->input->post('documentDate');
        $documentDate = input_format_date($docDate, $date_format_policy);
        $applicableDateFrom = $this->input->post('applicableDateFrom');
        $applicableDate_From = null;
        $applicableDate_To = null;
        if(!empty($applicableDateFrom)){
            $applicableDate_From = input_format_date($applicableDateFrom, $date_format_policy);
        }
        $applicableDateTo = $this->input->post('applicableDateTo');
        if(!empty($applicableDateTo)){
            $applicableDate_To = input_format_date($applicableDateTo, $date_format_policy);
        }

        $cpsAutoID = $this->input->post('cpsAutoID');
        $narration = $this->input->post('narration');
        $customerAutoID = $this->input->post('customerAutoID');
        $items = $this->input->post('itemTo');
        $currency = $this->input->post('currency');

        $data['documentDate'] = $documentDate;
        $data['narration'] = $narration;

        if($cpsAutoID){
            if(!empty($applicableDate_From) || !empty($applicableDate_To)){
                $details = $this->db->query("select * from srp_erp_customeritemprices WHERE companyID = {$companyID} AND cpsAutoID = {$cpsAutoID}")->result_array();
                foreach ($details as $val)
                {
                    $updateDetails['applicableDateFrom'] = $applicableDate_From;
                    $updateDetails['applicableDateTo'] = $applicableDate_To;

                    $this->db->where('companyID', $companyID);
                    $this->db->where('cpsAutoID', $cpsAutoID);
                    $this->db->where('customerPriceID', $val['customerPriceID']);
                    $this->db->update('srp_erp_customeritemprices', $updateDetails);
                }
            }

             $data['modifiedPCID'] = $this->common_data['current_pc'];
             $data['modifiedUserID'] = $this->common_data['current_userID'];
             $data['modifiedUserName'] = $this->common_data['current_user'];
             $data['modifiedDateTime'] = $this->common_data['current_date'];
             $this->db->where('cpsAutoID', $cpsAutoID);
             $this->db->update('srp_erp_customeritempricesetup', $data);
             $last_id = $cpsAutoID;

        } else {
             $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM srp_erp_customeritempricesetup WHERE  companyID = {$companyID}")->row_array();
             $data['serialNo'] = $serial['serialNo'];
             $data['documentSystemCode'] = ($company_code . '/' . 'CPS' . str_pad($data['serialNo'], 6, '0', STR_PAD_LEFT));
             $data['documentID'] = 'CPS';
             $data['companyID'] = $this->common_data['company_data']['company_id'];
             $data['createdUserGroup'] = $this->common_data['user_group'];
             $data['createdPCID'] = $this->common_data['current_pc'];
             $data['createdUserID'] = $this->common_data['current_userID'];
             $data['createdDateTime'] = $this->common_data['current_date'];
             $data['createdUserName'] = $this->common_data['current_user'];
             $this->db->insert('srp_erp_customeritempricesetup', $data);
             $last_id = $this->db->insert_id();


             foreach ($customerAutoID as $customer) {
                 foreach ($items as $item){
                     $itemdata = $this->db->query("SELECT companyLocalSellingPrice, companyLocalCurrencyID FROM srp_erp_itemmaster WHERE companyID = {$companyID} AND itemAutoID = {$item}")->row_array();

                     $details['cpsAutoID'] = $last_id;
                     $details['customerAutoID'] = $customer;
                     $details['itemAutoID'] = $item;
                     $details['currencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                     $details['currencyCode'] = $this->common_data['company_data']['company_default_currency'];
                     $details['salesPrice'] = $itemdata['companyLocalSellingPrice'];
                     $details['applicableDateFrom'] = $applicableDate_From;
                     $details['applicableDateTo'] = $applicableDate_To;
                     $details['isActive'] = 0;
                     $details['isModificationAllowed'] = 1;

                     $details['companyID'] = $this->common_data['company_data']['company_id'];
                     $details['createdUserGroup'] = $this->common_data['user_group'];
                     $details['createdPCID'] = $this->common_data['current_pc'];
                     $details['createdUserID'] = $this->common_data['current_userID'];
                     $details['createdDateTime'] = $this->common_data['current_date'];
                     $details['createdUserName'] = $this->common_data['current_user'];

                     $this->db->insert('srp_erp_customeritemprices', $details);
                 }
             }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sales Price :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sales Price :  Saved Successfully.',$last_id);
        }
    }


    function Save_Customer_ItemPrice()
    {
        $date_format_policy = date_format_policy();
        $itemAutoIDs = $this->input->post('itemID');
        $cpsAutoID = $this->input->post('cpsAutoID');
        $salesPrice = $this->input->post('AddsalesPrice');
        $appDateFrom = $this->input->post('applicableDateFrom');
        $moficable = $this->input->post('chkbox');
        $appDateTo = $this->input->post('applicableDateTo');
        $customerAutoID = $this->input->post('customerAutoID');
        $companyID = $this->common_data['company_data']['company_id'];
        foreach ($itemAutoIDs  as $key => $itemAutoID) {
            if($itemAutoID != '' && $salesPrice[$key] != '')
            {
                $customerPriceID = $this->db->query("SELECT customerPriceID FROM srp_erp_customeritemprices WHERE itemAutoID = {$itemAutoID} AND cpsAutoID = $cpsAutoID AND customerAutoID = {$customerAutoID} AND companyID = $companyID")->row_array();

                if(!empty($appDateFrom[$key])){
                    $applicableDateFrom = input_format_date($appDateFrom[$key], $date_format_policy);
                } else{
                    $applicableDateFrom = null;
                }
                if(!empty($appDateTo[$key])){
                    $applicableDateTo = input_format_date($appDateTo[$key], $date_format_policy);
                } else{
                    $applicableDateTo = null;
                }

                $data['applicableDateFrom'] = $applicableDateFrom;
                $data['applicableDateTo'] = $applicableDateTo;
                $data['isModificationAllowed'] = $moficable[$key];
                $data['salesPrice'] = $salesPrice[$key];

                if($customerPriceID){
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $this->db->where('customerPriceID', $customerPriceID['customerPriceID']);
                    $this->db->update('srp_erp_customeritemprices', $data);
                } else {
                    $data['customerAutoID'] = $customerAutoID;
                    $data['cpsAutoID'] = $cpsAutoID;
                    $data['itemAutoID'] = $itemAutoID;
                    $data['currencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $data['currencyCode'] = $this->common_data['company_data']['company_default_currency'];
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $this->db->insert('srp_erp_customeritemprices', $data);
                }
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sales Price :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sales Price :  Saved Successfully.');
        }
    }

    function load_customerSalesPrice_header()
    {
        $cpsAutoID = $this->input->post('cpsAutoID');
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate');
        $this->db->from('srp_erp_customeritempricesetup');
        $this->db->where('cpsAutoID', trim($cpsAutoID));
        $data['header'] = $this->db->get()->row_array();

        $cusDetails = $this->db->query("SELECT DISTINCT(srp_erp_customeritemprices.customerAutoID) AS customerAutoID, DATE_FORMAT(applicableDateFrom, ' $convertFormat ') AS applicableDateFrom, DATE_FORMAT(applicableDateTo,' $convertFormat ') AS applicableDateTo 
                            FROM `srp_erp_customeritemprices` 
                            LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customeritemprices.customerAutoID
                            WHERE cpsAutoID = {$cpsAutoID}")->result_array();

        $data['item'] = $this->db->query("SELECT DISTINCT(srp_erp_customeritemprices.itemAutoID) AS itemAutoID, itemSystemCode, itemDescription 
                            FROM `srp_erp_customeritemprices` 
                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_customeritemprices.itemAutoID
                            WHERE cpsAutoID = {$cpsAutoID}")->result_array();
        if(!empty($cusDetails)){
            foreach ($cusDetails as $val){
                $data['customer'][] = $val['customerAutoID'];
                $data['applicableDateFrom'] = $val['applicableDateFrom'];
                $data['applicableDateTo'] = $val['applicableDateTo'];
            }
        }

        return $data;
    }

    function load_Customer_PriceConfirmation($cpsAutoID)
    {
        $compID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $data['master'] = $this->db->query("select cps.cpsAutoID,  CONCAT(cm.customerSystemCode, ' | ',cm.customerName) AS customer, DATE_FORMAT(cps.documentDate,'$convertFormat') AS documentDate, cps.documentSystemCode, cps.narration, cps.confirmedYN, cps.confirmedByEmpID, cps.confirmedByName, cps.confirmedDate, cm.customerCurrencyDecimalPlaces, cps.approvedYN, cps.approvedDate, cps.approvedbyEmpName FROM srp_erp_customeritempricesetup cps LEFT JOIN srp_erp_customermaster cm ON cm.customerAutoID = cps.customerAutoID WHERE cpsAutoID = $cpsAutoID ")->row_array();

        $data['itemPriceDetails'] =  $this->db->query("SELECT cip.customerPriceID, im.itemSystemCode, im.itemDescription, im.companyLocalSellingPrice, cip.salesPrice, cip.isModificationAllowed, DATE_FORMAT(applicableDateFrom,' $convertFormat ') AS applicableDateFrom, DATE_FORMAT(applicableDateTo,' $convertFormat ') AS applicableDateTo
                                                              FROM srp_erp_customeritemprices cip  
                                                              LEFT JOIN srp_erp_customeritempricesetup cps on cps.cpsAutoID = cip.cpsAutoID
                                                              LEFT JOIN srp_erp_itemmaster im on im.itemAutoID = cip.itemAutoID
                                                              WHERE cps.companyID = $compID AND cps.cpsAutoID = $cpsAutoID ORDER BY cip.customerPriceID ASC")->result_array();
        return $data;
    }

    function customer_SalesPrice_confirmation()
    {
        $cpsAutoID = trim($this->input->post('cpsAutoID') ?? '');

        $this->db->select('cpsAutoID');
        $this->db->where('cpsAutoID', $cpsAutoID);
        $this->db->from('srp_erp_customeritemprices');
        $results = $this->db->get()->result_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $this->load->library('approvals');
            $this->db->select('*');
            $this->db->where('cpsAutoID', trim($this->input->post('cpsAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_customeritempricesetup');
            $row = $this->db->get()->row_array();
            if (!empty($row)) {
                return array('w', 'Document already confirmed');
            } else {
                $this->db->select('*');
                $this->db->where('cpsAutoID', trim($this->input->post('cpsAutoID') ?? ''));
                $this->db->from('srp_erp_customeritempricesetup');
                $row = $this->db->get()->row_array();
                $approvals_status = $this->approvals->CreateApproval('CPS', $row['cpsAutoID'], $row['documentSystemCode'], 'Customer Price Setup', 'srp_erp_customeritempricesetup', 'cpsAutoID',0 ,$row['documentDate']);
                if ($approvals_status == 1) {
                    $cpsAutoID = trim($this->input->post('cpsAutoID') ?? '');

                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        //    'transactionAmount' => '',
                    );
                    $this->db->where('cpsAutoID', $cpsAutoID);
                    $this->db->update('srp_erp_customeritempricesetup', $data);

                    return array('s', 'Customer Price Setup : Confirmed Successfully. ');

                } else if ($approvals_status == 3) {
                    return array('w', 'There are no users exist to perform approval for this document.');
                } else {
                    return array('e', 'something went wrong');
                }
            }
        }
    }


    function delete_customerSalesPrice_document()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customeritemprices');
        $this->db->where('cpsAutoID', trim($this->input->post('cpsAutoID') ?? ''));
        $detailData = $this->db->get()->row_array();
        if (empty($detailData)) {
            $this->db->delete('srp_erp_customeritemprices', array('cpsAutoID' =>  trim($this->input->post('cpsAutoID') ?? '')));
            $this->db->delete('srp_erp_customeritempricesetup', array('cpsAutoID' =>  trim($this->input->post('cpsAutoID') ?? '')));
            $this->session->set_flashdata('s', 'Customer Price Setup Document Deleted Successfully.');
            return true;
        } else {
            $this->db->delete('srp_erp_customeritemprices', array('cpsAutoID' =>  trim($this->input->post('cpsAutoID') ?? '')));
            $this->db->delete('srp_erp_customeritempricesetup', array('cpsAutoID' =>  trim($this->input->post('cpsAutoID') ?? '')));
            $this->session->set_flashdata('s', 'Customer Price Setup Document Deleted Successfully.');
            return true;
        }
    }

    function save_CustomerPriceSetup_approval()
    {
        $compID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('cpsAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'CPS');

        if ($approvals_status == 1) {
            $details = $this->db->query("select * from srp_erp_customeritemprices WHERE cpsAutoID = {$system_id} AND companyID = $compID ")->result_array();
            foreach ($details as $val)
            {
                $activePriceAvailable = $this->db->query("select customerPriceID from srp_erp_customeritemprices 
                        WHERE companyID = {$compID} AND itemAutoID = {$val['itemAutoID']} AND customerAutoID = {$val['customerAutoID']} AND isActive = 1")->row_array();
                if($activePriceAvailable){
                    $deact['isActive'] = 0;
                    $this->db->where('customerPriceID', $activePriceAvailable['customerPriceID']);
                    $this->db->update('srp_erp_customeritemprices', $deact);
                }
            }

            $detail['isActive'] = 1;
            $this->db->where('cpsAutoID', $system_id);
            $this->db->update('srp_erp_customeritemprices', $detail);

            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $this->db->where('cpsAutoID', $system_id);
            $this->db->update('srp_erp_customeritempricesetup', $data);
            $this->session->set_flashdata('s', 'Customer Price Setup Approved Successfully.');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function deactivate_CustomerWisePrice()
    {
        $compID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $customerPriceID = trim($this->input->post('customerPriceID') ?? '');
        $checkedVal = trim($this->input->post('checkedVal') ?? '');

        $cusID = $this->db->query("SELECT customerAutoID FROM srp_erp_customeritemprices WHERE customerPriceID = $customerPriceID AND companyID = $compID ")->row_array();

        $detail['isActive'] = $checkedVal;
        $detail['deactivatedByEmpID'] = $this->common_data['current_userID'];
        $detail['deactivatedDate'] = $this->common_data['current_date'];
        $this->db->where('customerPriceID', $customerPriceID);
        $this->db->where('companyID', $compID);
        $this->db->update('srp_erp_customeritemprices', $detail);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sales Price :  Deactivation Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sales Price :  Deactivated Successfully.' , $cusID['customerAutoID']);
        }
    }

    function modifyCustomerPrice()
    {
        $compID = $this->common_data['company_data']['company_id'];
        $this->db->trans_start();
        $customerPriceID = trim($this->input->post('customerPriceID') ?? '');
        $checkedVal = trim($this->input->post('checkedVal') ?? '');

        $cusID = $this->db->query("SELECT customerAutoID FROM srp_erp_customeritemprices WHERE customerPriceID = $customerPriceID AND companyID = $compID ")->row_array();

        $detail['isModificationAllowed'] = $checkedVal;
      //  $detail['deactivatedByEmpID'] = $this->common_data['current_userID'];
      //  $detail['deactivatedDate'] = $this->common_data['current_date'];
        $this->db->where('customerPriceID', $customerPriceID);
        $this->db->where('companyID', $compID);
        $this->db->update('srp_erp_customeritemprices', $detail);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sales Price :  Modification Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sales Price :  Modification Successfully.' , $cusID['customerAutoID']);
        }
    }

    function delete_Customer_itemprice()
    {
        $customerPriceID = trim($this->input->post('customerPriceID') ?? '');

        $this->db->where('customerPriceID', $customerPriceID);
        $result = $this->db->delete('srp_erp_customeritemprices');
        $this->session->set_flashdata('s', 'Customer Item Price Deleted Successfully.');
         return true;
    }
    function delete_Customer_itemprice_all()
    {
        $cpsAutoID = trim($this->input->post('cpsAutoID') ?? '');

        $this->db->where('cpsAutoID', $cpsAutoID);
        $result = $this->db->delete('srp_erp_customeritemprices');
        $this->session->set_flashdata('s', 'Customer Item Price Deleted Successfully.');
         return true;
    }

    function fetch_CustomerPrice_details($cpsAutoID)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $customer = $this->db->query("SELECT srp_erp_customeritemprices.customerAutoID, customerSystemCode, customerName, cpsAutoID, customerPriceID, srp_erp_customermaster.partyCategoryID, IFNULL(categoryDescription, 'Uncategorized' ) as categoryDescription 
                                FROM `srp_erp_customeritemprices` 
                                LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customeritemprices.customerAutoID
                                LEFT JOIN srp_erp_partycategories ON srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID
                                WHERE srp_erp_customeritemprices.companyID = {$companyID} AND cpsAutoID = {$cpsAutoID} 
                                GROUP BY srp_erp_customeritemprices.customerAutoID")->result_array();
        $detail = array();
        foreach ($customer as $cus){
            $customerAutoID = $cus['customerAutoID'];
            $data =  $this->db->query("SELECT srp_erp_customeritemprices.customerAutoID, srp_erp_itemmaster.itemAutoID AS ids, srp_erp_customeritemprices.itemAutoID AS itemAutoID, salesPrice, customerPriceID FROM `srp_erp_customeritemprices` 
                                 LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_customeritemprices.itemAutoID
                                WHERE srp_erp_customeritemprices.companyID = {$companyID} AND cpsAutoID = {$cpsAutoID} AND customerAutoID = {$customerAutoID}
                                GROUP BY srp_erp_customeritemprices.itemAutoID")->result_array();

            $detail[$cus['customerAutoID']]['customerAutoID'] = $cus['customerAutoID'];
            $detail[$cus['customerAutoID']]['partyCategoryID'] = $cus['categoryDescription'];
//            $detail[$cus['customerAutoID']][$cus['partyCategoryID'] . '_val'] = $cus['partyCategoryID'];
            $detail[$cus['customerAutoID']]['customerSystemCode'] = $cus['customerSystemCode'] . ' | ' . $cus['customerName'];
            $detail[$cus['customerAutoID']]['cpsAutoID'] = $cus['cpsAutoID'];
            foreach ($data as $var) {
                $detail[$cus['customerAutoID']][$var['itemAutoID']] = $var['salesPrice'];
                $detail[$cus['customerAutoID']][$var['ids'] . '_1'] = $var['ids'];
                $detail[$cus['customerAutoID']][$var['ids'] . '_customerPriceID'] = $var['customerPriceID'];
            }
        }
//        echo '<pre>'; print_r($detail);
        return $detail;
    }

    function update_customer_price()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $cpsAutoID = trim($this->input->post('cpsAutoID') ?? '');
        $customerAutoID = trim($this->input->post('customerAutoID') ?? '');
        $customerPriceID = trim($this->input->post('customerPriceID') ?? '');
        $customerPrice = trim($this->input->post('customerPrice') ?? '');
        $itemAutoID = trim($this->input->post('itemID') ?? '');

        $data['salesPrice'] = $customerPrice;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $this->db->where('companyID', $companyID);
        $this->db->where('cpsAutoID', $cpsAutoID);
        $this->db->where('customerAutoID', $customerAutoID);
        $this->db->where('customerPriceID', $customerPriceID);
        $this->db->where('itemAutoID', $itemAutoID);
        $this->db->update('srp_erp_customeritemprices', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }

    }

    /* Function added */
    function export_excel_customer_master(){
        $rebate = getPolicyValues('CRP', 'All');; // policy for customer rebate process
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
       // $deletedYN_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
       /* $deleted = $this->input->post('deletedYN');

        if ($deleted == 1) {
            $deletedYN_filter = " AND deletedYN = " . $deleted;
        } else {
            $deletedYN_filter = " AND (deletedYN IS NULL OR deletedYN = " . $deleted . ")";
        }*/

        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join(" , ", $customer[0]) . " )";
            $customer_filter = " AND customerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join(" , ", $category[0]) . " )";
            $category_filter = " AND srp_erp_customermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join(" , ", $currency[0]) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_customermaster.companyID = " . $companyid . $customer_filter . $category_filter . $currency_filter. "";

    $result = $this->db->query("SELECT customerSystemCode,customerName,secondaryCode,concat(`customerAddress1`,customerAddress2, customerCountry) AS address, customerEmail,customerTelephone,customerUrl,customerFax,srp_erp_partycategories.categoryDescription AS category, tax.Description AS Description,vatIdNo, customerCreditPeriod, customerCreditLimit,  IdCardNumber,CONCAT_WS('|',receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType) AS receivableAccount, customerCurrency,`cust`.`Amount` AS `Amount`, `cust`.`partyCurrencyDecimalPlaces` AS `partyCurrencyDecimalPlaces`,rebate.rebateGLCode AS rebateGLCode, rebatePercentage, srp_erp_customermaster.deletedYN as deletedYN
         FROM
            `srp_erp_customermaster`
        LEFT JOIN srp_erp_partycategories ON srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID
        LEFT JOIN ( SELECT taxGroupID, Description FROM srp_erp_taxgroup WHERE taxType = 1 AND companyID = $companyid GROUP BY taxGroupID ) tax ON tax.taxGroupID = srp_erp_customermaster.taxGroupID
        LEFT JOIN ( SELECT GLAutoID,CONCAT_WS('|',systemAccountCode,GLSecondaryCode,GLDescription,subCategory) AS rebateGLCode  FROM srp_erp_chartofaccounts WHERE  companyID = $companyid GROUP BY GLAutoID ) rebate ON rebate.GLAutoID = srp_erp_customermaster.rebateGLAutoID
        LEFT JOIN (
        SELECT
            sum( srp_erp_generalledger.transactionAmount / srp_erp_generalledger.partyExchangeRate ) AS Amount, partyAutoID, partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE 
            partyType = 'CUS' AND subLedgerType = 3 GROUP BY partyAutoID ) cust ON `cust`.`partyAutoID` = `srp_erp_customermaster`.`customerAutoID`
        WHERE
         $where
          AND  ( deletedYN IS NULL OR `deletedYN` = 0  )
        ORDER BY `customerAutoID` DESC 
        ")->result_array();

        $data = array();
        if($rebate == 1){
            $a = 1;
            foreach ($result as $row)
            {
                $data[] = array(
                    'Num' => $a,
                    'customerSystemCode' => $row['customerSystemCode'],
                    'customerName' => $row['customerName'],
                    'secondaryCode' => $row['secondaryCode'],
                    'address' => $row['address'],
                    'customerEmail' => $row['customerEmail'],
                    'customerTelephone' => $row['customerTelephone'],
                    'customerUrl' => $row['customerUrl'],
                    'customerFax' => $row['customerFax'],
                    'Description' => $row['Description'],
                    'category' => $row['category'],  
                    'vatIdNo' => $row['vatIdNo'],  
                    'customerCreditPeriod' => $row['customerCreditPeriod'],  
                    'customerCreditLimit' => $row['customerCreditLimit'],  
                    'IdCardNumber' => $row['IdCardNumber'],
                    'receivableAccount' => $row['receivableAccount'],
                    'customerCurrency' => $row['customerCurrency'], 
                    'amt' =>  number_format($row['Amount'],$row['partyCurrencyDecimalPlaces']),
                    'rebateGLCode' => $row['rebateGLCode'], 
                    'rebatePercentage' => $row['rebatePercentage'], 
                );
                $a++;
            }
        }else{
            $a = 1;
            foreach ($result as $row)
            {
                $data[] = array(
                    'Num' => $a,
                    'customerSystemCode' => $row['customerSystemCode'],
                    'customerName' => $row['customerName'],
                    'secondaryCode' => $row['secondaryCode'],
                    'address' => $row['address'],
                    'customerEmail' => $row['customerEmail'],
                    'customerTelephone' => $row['customerTelephone'],
                    'customerUrl' => $row['customerUrl'],
                    'customerFax' => $row['customerFax'],
                    'Description' => $row['Description'], 
                    'category' => $row['category'], 
                    'vatIdNo' => $row['vatIdNo'],  
                    'customerCreditPeriod' => $row['customerCreditPeriod'],  
                    'customerCreditLimit' => $row['customerCreditLimit'],  
                    'IdCardNumber' => $row['IdCardNumber'],
                    'receivableAccount' => $row['receivableAccount'],
                    'customerCurrency' => $row['customerCurrency'], 
                    'amt' =>  number_format($row['Amount'],$row['partyCurrencyDecimalPlaces']),
                );
                $a++;
            }
        }

        return ['customers' => $data];
    }

    function export_excel_customer_master_arabic(){
        $rebate = getPolicyValues('CRP', 'All');; // policy for customer rebate process
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';
       // $deletedYN_filter = '';
        $customer = $this->input->post('customerCode');
        $category = $this->input->post('category');
        $currency = $this->input->post('currency');
       /* $deleted = $this->input->post('deletedYN');

        if ($deleted == 1) {
            $deletedYN_filter = " AND deletedYN = " . $deleted;
        } else {
            $deletedYN_filter = " AND (deletedYN IS NULL OR deletedYN = " . $deleted . ")";
        }*/

        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join(" , ", $customer[0]) . " )";
            $customer_filter = " AND customerAutoID IN " . $whereIN;
        }
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join(" , ", $category[0]) . " )";
            $category_filter = " AND srp_erp_customermaster.partyCategoryID IN " . $whereIN;
        }
        if (!empty($currency)) {
            $currency = array($this->input->post('currency'));
            $whereIN = "( " . join(" , ", $currency[0]) . " )";
            $currency_filter = " AND customerCurrencyID IN " . $whereIN;
        }
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_customermaster.companyID = " . $companyid . $customer_filter . $category_filter . $currency_filter. "";

    $result = $this->db->query("SELECT
    customerSystemCode,
    customerName,
    customerNameOtherLang,
    secondaryCode,
    customerAddress1,
    customerAddress1OtherLang,
    customerAddress2,
    customerAddress2OtherLang,
    customerCountry,
    customerEmail,
    customerTelephone,
    customerUrl,
    customerFax,
    srp_erp_partycategories.categoryDescription AS category,
    tax.Description AS taxGroup,
    IdCardNumber,
    vatIdNo,
    customerCreditPeriod,
    customerCreditLimit,
    CONCAT_WS( '|', receivableSystemGLCode, receivableGLAccount, receivableDescription, receivableType ) AS receivableAccount,
    customerCurrency,
    `cust`.`Amount` AS `Amount`, 
    `cust`.`partyCurrencyDecimalPlaces` AS `partyCurrencyDecimalPlaces`,
    rebate.rebateGLCode AS rebateGLCode,
    rebatePercentage,
  srp_erp_customermaster.deletedYN as deletedYN 
FROM
    `srp_erp_customermaster`
LEFT JOIN srp_erp_partycategories ON srp_erp_customermaster.partyCategoryID = srp_erp_partycategories.partyCategoryID
LEFT JOIN ( SELECT taxGroupID, Description FROM srp_erp_taxgroup WHERE taxType = 1 AND companyID = 13 GROUP BY taxGroupID ) tax ON tax.taxGroupID = srp_erp_customermaster.taxGroupID
LEFT JOIN ( SELECT GLAutoID,CONCAT_WS('|',systemAccountCode,GLSecondaryCode,GLDescription,subCategory) AS rebateGLCode  FROM srp_erp_chartofaccounts WHERE  companyID = 13 GROUP BY GLAutoID ) rebate ON rebate.GLAutoID = srp_erp_customermaster.rebateGLAutoID
LEFT JOIN (
        SELECT
            sum( srp_erp_generalledger.transactionAmount / srp_erp_generalledger.partyExchangeRate ) AS Amount, partyAutoID, partyCurrencyDecimalPlaces FROM srp_erp_generalledger WHERE 
            partyType = 'CUS' AND subLedgerType = 3 GROUP BY partyAutoID ) cust ON `cust`.`partyAutoID` = `srp_erp_customermaster`.`customerAutoID`
    WHERE
         $where
         AND ( deletedYN IS NULL OR `deletedYN` = 0  )
  ORDER BY `customerAutoID` DESC        
        ")->result_array();

        $data = array();
        if($rebate == 1){
            $a = 1;
            foreach ($result as $row)
            {
                $data[] = array(
                    'Num' => $a,
                    'customerSystemCode' => $row['customerSystemCode'],
                    'customerName' => $row['customerName'],
                    'customerNameOtherLang' => $row['customerNameOtherLang'],
                    'secondaryCode' => $row['secondaryCode'],
                    'customerAddress1' => $row['customerAddress1'],
                    'customerAddress1OtherLang' => $row['customerAddress1OtherLang'],
                    'customerAddress2' => $row['customerAddress2'],
                    'customerAddress2OtherLang' => $row['customerAddress2OtherLang'],
                    'customerCountry' => $row['customerCountry'],
                    'customerEmail' => $row['customerEmail'],
                    'customerTelephone' => $row['customerTelephone'],
                    'customerUrl' => $row['customerUrl'],
                    'customerFax' => $row['customerFax'],
                    'taxGroup' => $row['taxGroup'],
                     'category' => $row['category'], 
                    'vatIdNo' => $row['vatIdNo'],  
                    'customerCreditPeriod' => $row['customerCreditPeriod'],  
                    'customerCreditLimit' => $row['customerCreditLimit'],  
                    'IdCardNumber' => $row['IdCardNumber'],
                    'receivableAccount' => $row['receivableAccount'],
                    'customerCurrency' => $row['customerCurrency'], 
                    'amt' =>  number_format($row['Amount'],$row['partyCurrencyDecimalPlaces']),
                    'rebateGLCode' => $row['rebateGLCode'], 
                    'rebatePercentage' => $row['rebatePercentage'], 
                );
                $a++;
            }
        }else{
            $a = 1;
            foreach ($result as $row)
            {
                $data[] = array(
                    'Num' => $a,
                    'customerSystemCode' => $row['customerSystemCode'],
                    'customerName' => $row['customerName'],
                    'customerNameOtherLang' => $row['customerNameOtherLang'],
                    'secondaryCode' => $row['secondaryCode'],
                    'customerAddress1' => $row['customerAddress1'],
                    'customerAddress1OtherLang' => $row['customerAddress1OtherLang'],
                    'customerAddress2' => $row['customerAddress2'],
                    'customerAddress2OtherLang' => $row['customerAddress2OtherLang'],
                    'customerCountry' => $row['customerCountry'],
                    'customerEmail' => $row['customerEmail'],
                    'customerTelephone' => $row['customerTelephone'],
                    'customerUrl' => $row['customerUrl'],
                    'customerFax' => $row['customerFax'],
                    'taxGroup' => $row['taxGroup'],
                     'category' => $row['category'], 
                    'vatIdNo' => $row['vatIdNo'],  
                    'customerCreditPeriod' => $row['customerCreditPeriod'],  
                    'customerCreditLimit' => $row['customerCreditLimit'],  
                    'IdCardNumber' => $row['IdCardNumber'],
                    'receivableAccount' => $row['receivableAccount'],
                    'customerCurrency' => $row['customerCurrency'], 
                    'amt' =>  number_format($row['Amount'],$row['partyCurrencyDecimalPlaces']),
                );
                $a++;
            }
        }

        return ['customers_arabic' => $data];
    }
    /* End  Function */  
}
