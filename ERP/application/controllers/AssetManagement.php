<?php

class AssetManagement extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('AssetManagemnt_model');
        $this->load->helper('asset_management');
        $this->load->library('s3');
    }

    function load_assetmaster()
    {
        $mainCatId = $this->input->post('mainCatId');
        $subCatId = $this->input->post('subCatId');
        $location = $this->input->post('locationFilter');
        $companyid = $this->common_data['company_data']['company_id'];
        $locationFilter = "";
        if ($location != '') {
            $locationFilter = "AND currentLocation = $location ";
        }
        $Maincatfilter="";
        if ($mainCatId != '') {
            $Maincatfilter = "AND faCatID = $mainCatId ";
        }
        $Subcatfilter="";
        if ($subCatId != '') {
            $Subcatfilter = "AND faSubCatID = $subCatId ";
        }
            $where = "srp_erp_fa_asset_master.companyID = " . $companyid . " $locationFilter $Maincatfilter $Subcatfilter";
        $this->datatables->select('faID as faID,faCode,assetDescription,faUnitSerialNo,faCatID,faSubCatID,faSubCatID2,srp_erp_fa_asset_master.confirmedYN AS confirmedYN,srp_erp_fa_asset_master.approvedYN as approvedYN,masterCategory.description masterCategoryDesc,subCategory.description subCategoryDesc,companyLocalAmount,companyLocalCurrencyDecimalPlaces,srp_erp_fa_asset_master.isFromGRV AS isFromGRV,srp_erp_location.locationName as locationName,srp_erp_fa_asset_master.createdUserID as createdUser,srp_erp_fa_asset_master.confirmedByEmpID as confirmedByEmp', false)
            ->where($where)
            ->from('srp_erp_fa_asset_master')
            ->join('srp_erp_itemcategory masterCategory', 'srp_erp_fa_asset_master.faCatID = masterCategory.itemCategoryID','left')
            ->join('srp_erp_itemcategory subCategory', 'srp_erp_fa_asset_master.faSubCatID = subCategory.itemCategoryID', 'left')
            ->join('srp_erp_location', 'srp_erp_location.locationID = srp_erp_fa_asset_master.currentLocation', 'left');
        $this->datatables->add_column('confirmed', '$1', 'confirm(confirmedYN)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"FA",faID)');
        $this->datatables->edit_column('companyLocalAmount', '$1', 'format_number(companyLocalAmount,companyLocalCurrencyDecimalPlaces)');
        $this->datatables->add_column('edit', '$1', 'edit_asset(faID,confirmedYN,approvedYN,isFromGRV,createdUser,confirmedByEmp)');
        echo $this->datatables->generate();
    }

    function save_asset()
    {
        $this->form_validation->set_rules('assetType', 'Asset Type', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('assetDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('MANUFACTURE', 'Manufacture', 'trim|required');
        $this->form_validation->set_rules('dateAQ', 'Date Acquired', 'trim|required');
        $this->form_validation->set_rules('dateDEP', 'Depreciation Date Start', 'trim|required');
        $this->form_validation->set_rules('depMonth', 'Life time in years', 'trim|required');
        $this->form_validation->set_rules('DEPpercentage', 'DEP %', 'trim|required');
        $this->form_validation->set_rules('COSTUNIT', 'Unit Price (Local)', 'trim|required');
        $this->form_validation->set_rules('transactionCurrency', 'Currency', 'trim|required');
        $this->form_validation->set_rules('faCatID', 'Main Catrgory', 'trim|required');
        $this->form_validation->set_rules('faSubCatID', 'Sub Catrgory', 'trim|required');
        $this->form_validation->set_rules('currentLocation', 'Asset Location', 'trim|required');
        $this->form_validation->set_rules('postDate', 'Post Date', 'trim|required');
        if($this->input->post('accDepAmount')>0){
            $this->form_validation->set_rules('accDepDate', 'Acc Dep Date', 'trim|required');
        }

        $company_id = $this->common_data['company_data']['company_id'];

        $companyFinancePeriods = $this->db->query("SELECT
        srp_erp_companyfinanceperiod.dateFrom,
        srp_erp_companyfinanceperiod.dateTo
        FROM
        srp_erp_companyfinanceyear
        INNER JOIN srp_erp_companyfinanceperiod ON srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID
        WHERE
        srp_erp_companyfinanceyear.isActive = '1' AND
        srp_erp_companyfinanceyear.companyID = '{$company_id}' AND
        srp_erp_companyfinanceperiod.isActive = 1 AND
        srp_erp_companyfinanceperiod.companyID = '{$company_id}'")->result_array();

        $dateAQ = $this->input->post('dateAQ');
        $dateDEP = $this->input->post('dateDEP');
        $postDate = $this->input->post('postDate');
        $accDepDate = $this->input->post('accDepDate');
        $isFinanceyearValid = false;
        $ifFinanceMsg = "<p>The date you have selected is not included in any active Financial periods</p>";
        foreach ($companyFinancePeriods as $companyFinancePeriod) {
            if (($companyFinancePeriod['dateFrom'] <= $dateAQ) && ($companyFinancePeriod['dateTo'] >= $dateAQ)) {
                $isFinanceyearValid = true;
            }
        }
        if($this->input->post('accDepAmount')>0) {
            foreach ($companyFinancePeriods as $companyFinancePeriod) {
                if (($companyFinancePeriod['dateFrom'] <= $accDepDate) && ($companyFinancePeriod['dateTo'] >= $accDepDate)) {
                    $isFinanceyearValid = true;
                }
            }
        }

        if ($isFinanceyearValid == false) {
            $this->session->set_flashdata('e', $ifFinanceMsg);
            echo json_encode(FALSE);
            exit();
        }
        /*Finance period check*/

        /*Depreciating Date  > Date Aeq */
        if ($dateAQ > $postDate) {
            $this->session->set_flashdata('e', '<p>Asset Capitalized Date must be greater then Date Acquired.</p>');
            echo json_encode(FALSE);
            exit();
        }
        /**/


        /*Depreciating Date  > post Date */
        if ($dateDEP < $postDate) {
            $this->session->set_flashdata('e', '<p>Depreciating Date must be greater then Asset Capitalized Date.</p>');
            echo json_encode(FALSE);
            exit();
        }
        /**/

        /*Is post to GL*/
        $isPostToGL = $this->input->post('isPostToGL');
        if (isset($isPostToGL)) {
            $this->form_validation->set_rules('postGLAutoID', 'Post to GL Code', 'trim|required');
        }

        /*if own Assets*/
        $assetType = $this->input->post('assetType');
        if ($assetType == 1) {
            $this->form_validation->set_rules('COSTGLCODEdes', 'Cost Account', 'trim|required');
            $this->form_validation->set_rules('ACCDEPGLCODEdes', 'Acc Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DEPGLCODEdes', 'Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DISPOGLCODEdes', 'Disposal GL Code', 'trim|required');
        }

        if ($assetType == 2) {
            $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->save_asset());
        }
    }

    function save_asset_user()
    {
        echo json_encode($this->AssetManagemnt_model->save_asset());
    }

    function getSubCategoryJson()
    {
        $masterCat = $this->input->post('masterCategory');
        $company_code = $this->common_data['company_data']['company_code'];
        $this->db->SELECT("itemCategoryID,description");
        $this->db->from('srp_erp_itemcategory');
        $this->db->where('companyCode', $company_code);
        $this->db->where_in('masterID', $masterCat);

        $datas = $this->db->get()->result_array();

        $r = array();
        foreach ($datas as $key => $data) {
            $out['value'] = $data['itemCategoryID'];
            $out['label'] = $data['description'];
            array_push($r, $out);
        }
        echo json_encode($r);
    }

    function getSubCategory()
    {
        $masterCat = trim($this->input->post('masterCategory') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $Categories = fa_asset_category_sub($masterCat);
        if($status=='true'){
            $option = '';
        }else{
            $option = '<option></option>';
        }
       // $option = '<option></option>';
        foreach ($Categories as $key => $Categoriy) {
            $option .= "<option value='{$key}'>{$Categoriy}</option>";
        }
        echo $option;
    }

    function delete_asset()
    {
        $this->form_validation->set_rules('faID', 'ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->delete_asset());
        }
    }

    function assetConfirm()
    {
        $this->form_validation->set_rules('assetType', 'Asset Type', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('assetDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('MANUFACTURE', 'Manufacture', 'trim|required');
        $this->form_validation->set_rules('dateAQ', 'Date Acquired', 'trim|required');
        $this->form_validation->set_rules('dateDEP', 'Depreciation Date Start', 'trim|required');
        $this->form_validation->set_rules('depMonth', 'Life time in years', 'trim|required');
        $this->form_validation->set_rules('DEPpercentage', 'DEP %', 'trim|required');
        $this->form_validation->set_rules('COSTUNIT', 'Unit Price (Local)', 'trim|required');
        $this->form_validation->set_rules('transactionCurrency', 'Currency', 'trim|required');
        $this->form_validation->set_rules('faCatID', 'Main Catrgory', 'trim|required');
        $this->form_validation->set_rules('faSubCatID', 'Sub Catrgory', 'trim|required');
        $this->form_validation->set_rules('currentLocation', 'Asset Location', 'trim|required');
        $this->form_validation->set_rules('postDate', 'Post Date', 'trim|required');
        if($this->input->post('accDepAmount')>0){
            $this->form_validation->set_rules('accDepDate', 'Acc Dep Date', 'trim|required');
        }

        /*Finance period check*/
        /* $FYBegin = $this->common_data['company_data']['FYBegin'];
         $FYEnd = $this->common_data['company_data']['FYEnd'];*/
        $company_id = $this->common_data['company_data']['company_id'];

        /*$companyFinanceYearID = $this->db->query("SELECT companyFinanceYearID FROM srp_erp_companyfinanceyear WHERE isActive='1' AND `companyID` = '{$company_id}'")->result_array();

        exit;*/

        $companyFinancePeriods = $this->db->query("SELECT
        srp_erp_companyfinanceperiod.dateFrom,
        srp_erp_companyfinanceperiod.dateTo
        FROM
        srp_erp_companyfinanceyear
        INNER JOIN srp_erp_companyfinanceperiod ON srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID
        WHERE
        srp_erp_companyfinanceyear.isActive = '1' AND
        srp_erp_companyfinanceyear.companyID = '{$company_id}' AND
        srp_erp_companyfinanceperiod.isActive = 1 AND
        srp_erp_companyfinanceperiod.companyID = '{$company_id}'")->result_array();

        $dateAQ = $this->input->post('dateAQ');
        $dateDEP = $this->input->post('dateDEP');
        $postDate = $this->input->post('postDate');
        $accDepDate = $this->input->post('accDepDate');
        $isFinanceyearValid = false;
        $ifFinanceMsg = "<p>The date you have selected is not included in any active Financial periods</p>";
        //$ifFinanceMsg .= "<p>Actived Finanaced Period are.</p>";
        foreach ($companyFinancePeriods as $companyFinancePeriod) {
            // $ifFinanceMsg .= "<p>{$companyFinancePeriod['dateFrom']} - {$companyFinancePeriod['dateTo']}</p>";
            if (($companyFinancePeriod['dateFrom'] <= $dateAQ) && ($companyFinancePeriod['dateTo'] >= $dateAQ)) {
                $isFinanceyearValid = true;
            }
        }
        if($this->input->post('accDepAmount')>0) {
            foreach ($companyFinancePeriods as $companyFinancePeriod) {
                // $ifFinanceMsg .= "<p>{$companyFinancePeriod['dateFrom']} - {$companyFinancePeriod['dateTo']}</p>";
                if (($companyFinancePeriod['dateFrom'] <= $accDepDate) && ($companyFinancePeriod['dateTo'] >= $accDepDate)) {
                    $isFinanceyearValid = true;
                }
            }
        }

        if ($isFinanceyearValid == false) {
            $this->session->set_flashdata($msgtype = 'e', $ifFinanceMsg);
            echo json_encode(FALSE);
            exit();
        }
        /*Finance period check*/

        /*Depreciating Date  > Date Aeq */
        if ($dateAQ > $postDate) {
            $this->session->set_flashdata($msgtype = 'e', '<p>Asset Capitalized Date must be greater then Date Acquired.</p>');
            echo json_encode(FALSE);
            exit();
        }
        /**/

        /*Asset Capitalized Date  > Acc Dep Date */
        /*      if ($accDepDate > $postDate) {
                  $this->session->set_flashdata($msgtype = 'e', '<p>Asset Capitalized Date must be greater then Acc Dep Date.</p>');
                  echo json_encode(FALSE);
                  exit();
              }*/
        /**/

        /*Depreciating Date  > post Date */
        if ($dateDEP < $postDate) {
            $this->session->set_flashdata($msgtype = 'e', '<p>Depreciating Date must be greater then Asset Capitalized Date.</p>');
            echo json_encode(FALSE);
            exit();
        }
        /**/

        /*Is post to GL*/
        $isPostToGL = $this->input->post('isPostToGL');
        if (isset($isPostToGL)) {
            $this->form_validation->set_rules('postGLAutoID', 'Post to GL Code', 'trim|required');
        }

        /*if own Assets*/
        $assetType = $this->input->post('assetType');
        if ($assetType == 1) {
            $this->form_validation->set_rules('COSTGLCODEdes', 'Cost Account', 'trim|required');
            $this->form_validation->set_rules('ACCDEPGLCODEdes', 'Acc Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DEPGLCODEdes', 'Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DISPOGLCODEdes', 'Disposal GL Code', 'trim|required');
        }

        if ($assetType == 2) {
            $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->assetConfirm());
        }

    }

    function fetch_asset_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuser = current_userID();
        if ($approvedYN == 0) {
            $this->datatables->select('faID,srp_erp_fa_asset_master.companyCode,faCode,assetDescription,segmentCode,confirmedYN,srp_erp_documentapproved.approvedYN AS approvedYN,documentApprovedID,approvalLevelID');
            $this->datatables->from('srp_erp_fa_asset_master');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_fa_asset_master.faID AND srp_erp_documentapproved.approvalLevelID = srp_erp_fa_asset_master.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_fa_asset_master.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'FA');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'FA');
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_fa_asset_master.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->add_column('faCode', '<a onclick=\'fetch_approval("$2","$3","$4","$5"); \'>$1</a>', 'faCode,faID,documentApprovedID,approvalLevelID,approvedYN');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "FA", faID)');
            $this->datatables->add_column('edit', '$1', 'ast_action_approval(faID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        } else
        {
            $this->datatables->select('faID,srp_erp_fa_asset_master.companyCode,faCode,assetDescription,segmentCode,confirmedYN,srp_erp_documentapproved.approvedYN AS approvedYN,documentApprovedID,approvalLevelID');
            $this->datatables->from('srp_erp_fa_asset_master');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_fa_asset_master.faID');


            $this->datatables->where('srp_erp_documentapproved.documentID', 'FA');
            $this->datatables->where('srp_erp_fa_asset_master.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('srp_erp_fa_asset_master.faID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('faCode', '<a onclick=\'fetch_approval("$2","$3","$4","$5"); \'>$1</a>', 'faCode,faID,documentApprovedID,approvalLevelID,approvedYN');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "FA", faID)');
            $this->datatables->add_column('edit', '$1', 'ast_action_approval(faID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function load_asset_conformation()
    {
        $faID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('faID') ?? '');
        $data['type'] = $this->input->post();
        $systemcode = '';
        $data['extra'] = $this->AssetManagemnt_model->fetch_template_data($faID);

        if ($data['extra']['docOrigin'] == 'PV')
        {
            $documentid  = $this->db->query("SELECT PVcode as documentsystemcode FROM `srp_erp_paymentvouchermaster` where payVoucherAutoId = '{$data['extra']['docOriginSystemCode']}'")->row_array();
            $data['systemcode'] = $documentid['documentsystemcode'];

        }
        if ($data['extra']['docOrigin'] == 'GRV')
        {
            $documentid  = $this->db->query("SELECT grvPrimaryCode as documentsystemcode FROM `srp_erp_grvmaster` where grvAutoID = '{$data['extra']['docOriginSystemCode']}'")->row_array();
            $data['systemcode'] = $documentid['documentsystemcode'];

        }
        if ($data['extra']['docOrigin'] == 'BSI')
        {
            $documentid  = $this->db->query("SELECT bookingInvCode as documentsystemcode FROM `srp_erp_paysupplierinvoicemaster` where InvoiceAutoID = '{$data['extra']['docOriginSystemCode']}'")->row_array();
            $data['systemcode'] = $documentid['documentsystemcode'];

        }


        $html = $this->load->view('system/asset_management/asset_approve_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);
        }
    }

    function save_asset_approval()
    {
        $company_id = $this->common_data['company_data']['company_id'];
        $this->load->library('Approvals');
        $system_code = trim($this->input->post('faID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'FA', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('faID');
                $this->db->where('faID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_fa_asset_master');
                $po_approved = $this->db->get()->row_array();

                $assetMaster = $this->db->query("SELECT * FROM srp_erp_fa_asset_master WHERE faID='{$system_code}'")->row_array();

                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        $currentMonthYear = date('m-Y');
                        $currentMonth = date('m');

                        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'FA');

                        $maxlevel = $this->db->query("SELECT MAX(levelNo) as maxLevel FROM srp_erp_approvalusers where documentID = 'FA' AND companyID = '{$company_id}'")->row_array();

                        $depriciationRunLastMonth = $this->db->query("SELECT depMonthYear FROM srp_erp_fa_depmaster where companyID = '{$company_id}' ORDER BY depMonthYear DESC limit 1")->row_array();

                        $faAssetDetail = $this->db->query("SELECT DATE_FORMAT(dateDEP,'%m-%Y') as dateDEP,accDepAmount FROM srp_erp_fa_asset_master WHERE faID = '{$system_code}'")->row_array();
                        $depStartmonth = $faAssetDetail['dateDEP'];

                        if ($maxlevel['maxLevel'] == $level_id) {
                            if ($status == 1) {
                                $this->AssetManagemnt_model->post_to_gl($system_code);
                                if($faAssetDetail['accDepAmount']>0){
                                    $this->AssetManagemnt_model->add_depreciation($system_code);
                                }
                            }

                            if ($assetMaster['assetType'] == 2) {
                                $this->data['status'] = 'success';
                                $this->data['month'] = '';
                                if($faAssetDetail['accDepAmount']>0) {
                                    $this->data['accDep'] = 1;
                                }
                                echo json_encode($this->data);
                                exit();

                            }

                            if ($depriciationRunLastMonth) {
                                if ($depStartmonth == $currentMonthYear) {
                                    $depreciationRunMonthOnly = explode('/', $depriciationRunLastMonth['depMonthYear']);
                                    if ($depreciationRunMonthOnly[0] == $currentMonthYear) {
                                        $this->data['status'] = 'userMessage';
                                        $this->data['month'] = 'currentMonth';
                                        $this->data['faID'] = $system_code;
                                        echo json_encode($this->data);
                                        exit();
                                    } else {
                                        $this->data['status'] = 'success';
                                        $this->data['month'] = '';
                                        if($faAssetDetail['accDepAmount']>0) {
                                            $this->data['accDep'] = 1;
                                        }
                                        echo json_encode($this->data);
                                        exit();
                                    }
                                } else if (($depStartmonth < $currentMonthYear) && ($depriciationRunLastMonth['depMonthYear'] >= $depStartmonth)) {
                                    $this->data['status'] = 'backDate';
                                    $this->data['month'] = 'backDate';
                                    $this->data['faID'] = $system_code;
                                    echo json_encode($this->data);
                                    exit();
                                } else {
                                    $this->data['status'] = 'success';
                                    $this->data['month'] = '';
                                    if($faAssetDetail['accDepAmount']>0) {
                                        $this->data['accDep'] = 1;
                                    }
                                    echo json_encode($this->data);
                                    exit();
                                }
                            } else {
                                $this->data['status'] = 'success';
                                $this->data['month'] = '';
                                if($faAssetDetail['accDepAmount']>0) {
                                    $this->data['accDep'] = 1;
                                }
                                echo json_encode($this->data);
                                exit();
                            }

                        } else {
                            $this->data['status'] = 'success';
                            $this->data['month'] = '';
                            if($faAssetDetail['accDepAmount']>0) {
                                $this->data['accDep'] = 1;
                            }
                            echo json_encode($this->data);
                            exit();
                        }
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('faID');
            $this->db->where('faID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_fa_asset_master');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'FA', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'FA');
                        $this->data['status'] = 'success';
                        $this->data['month'] = '';
                        echo json_encode($this->data);
                        exit();

                    }
                }
            }
        }
    }

    function assetDepGenerate()
    {
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Period ', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->assetDepGenerate());
        }
    }

    function assetDepGenerate_oldAssets()
    {
        echo json_encode($this->AssetManagemnt_model->assetDepGenerate_oldAssets());
    }

    function assetDepGenerate_oldAssets_backdate()
    {
        echo json_encode($this->AssetManagemnt_model->assetDepGenerate_oldAssets_backdate());
    }


    function load_asset_dep_generated()
    {
        $search = $this->input->post('sSearch');
        $where_search = '';
        if($search){
            $where_search = "AND ((srp_erp_fa_assetdepreciationperiods.faCode LIKE '%$search%') OR (srp_erp_fa_asset_master.DEPGLCODE LIKE '%$search%') OR (srp_erp_fa_asset_master.costGLCode LIKE '%$search%') OR (srp_erp_fa_asset_master.segmentCode LIKE '%$search%') OR (srp_erp_fa_asset_master.assetDescription LIKE '%$search%') OR (srp_erp_fa_asset_master.postDate LIKE '%$search%'))";
        }
        $companyID = $this->common_data['company_data']['company_id'];
        $where= "srp_erp_fa_asset_master.companyID = {$companyID} $where_search";

        $this->datatables->select("srp_erp_fa_assetdepreciationperiods.depMasterAutoID,
srp_erp_fa_assetdepreciationperiods.DepreciationPeriodsID,
srp_erp_fa_assetdepreciationperiods.faFinanceCatID,
srp_erp_fa_assetdepreciationperiods.faMainCategory,
srp_erp_fa_assetdepreciationperiods.faSubCategory,
srp_erp_fa_assetdepreciationperiods.faID,
srp_erp_fa_assetdepreciationperiods.faCode,
srp_erp_fa_assetdepreciationperiods.assetDescription,
srp_erp_fa_assetdepreciationperiods.depMonth,
srp_erp_fa_assetdepreciationperiods.depPercent,
srp_erp_fa_assetdepreciationperiods.depMonthYear,
srp_erp_fa_assetdepreciationperiods.companyFinanceYearID,
srp_erp_fa_assetdepreciationperiods.companyFinanceYear,
srp_erp_fa_assetdepreciationperiods.FYBegin,
srp_erp_fa_assetdepreciationperiods.FYEnd,
srp_erp_fa_assetdepreciationperiods.FYPeriodDateFrom,
srp_erp_fa_assetdepreciationperiods.FYPeriodDateTo,
srp_erp_fa_assetdepreciationperiods.transactionCurrency as DeptransactionCurrency,
srp_erp_fa_assetdepreciationperiods.transactionExchangeRate as DeptransactionExchangeRate,
srp_erp_fa_assetdepreciationperiods.transactionAmount as DeptransactionAmount,
srp_erp_fa_assetdepreciationperiods.transactionCurrencyDecimalPlaces as DeptransactionCurrencyDecimalPlaces,
srp_erp_fa_assetdepreciationperiods.companyLocalCurrency as DepcompanyLocalCurrency,
srp_erp_fa_assetdepreciationperiods.companyLocalExchangeRate as DepcompanyLocalExchangeRate,
srp_erp_fa_assetdepreciationperiods.companyLocalAmount as DepcompanyLocalAmount,
srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces as DepcompanyLocalCurrencyDecimalPlaces,
srp_erp_fa_assetdepreciationperiods.companyReportingCurrency as DepcompanyReportingCurrency,
srp_erp_fa_assetdepreciationperiods.companyReportingExchangeRate as DepcompanyReportingExchangeRate,
srp_erp_fa_assetdepreciationperiods.companyReportingAmount as DepcompanyReportingAmount,
srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyDecimalPlaces as DepcompanyReportingCurrencyDecimalPlaces,
srp_erp_fa_asset_master.transactionCurrency,
srp_erp_fa_asset_master.transactionCurrencyExchangeRate,
srp_erp_fa_asset_master.transactionAmount,
srp_erp_fa_asset_master.transactionCurrencyDecimalPlaces,
srp_erp_fa_asset_master.companyLocalCurrency,
srp_erp_fa_asset_master.companyLocalExchangeRate,
srp_erp_fa_asset_master.companyLocalAmount as companyLocalAmount,
srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
srp_erp_fa_asset_master.companyReportingCurrency,
srp_erp_fa_asset_master.companyReportingExchangeRate,
srp_erp_fa_asset_master.companyReportingAmount as companyReportingAmount,
srp_erp_fa_asset_master.companyReportingDecimalPlaces as companyReportingDecimalPlaces,
srp_erp_fa_asset_master.DEPGLCODE,
srp_erp_fa_asset_master.costGLCode,
srp_erp_fa_asset_master.ACCDEPGLCODE,
DATE_FORMAT(srp_erp_fa_asset_master.postDate,'%Y-%m-%d') AS postDate,
srp_erp_fa_asset_master.segmentCode,
DATE_FORMAT(srp_erp_fa_asset_master.dateDEP,'%Y-%m-%d') AS dateDEP
", false);
        $this->datatables->from('srp_erp_fa_assetdepreciationperiods');
        $this->datatables->join('srp_erp_fa_asset_master', 'srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID');
        $this->datatables->where('srp_erp_fa_assetdepreciationperiods.depMasterAutoID', $this->input->post('depMasterAutoID'));
        $this->datatables->where($where);
        $this->datatables->edit_column('companyLocalAmount', '<div class="pull-right"> $1 </div>', 'format_number(companyLocalAmount,companyLocalCurrencyDecimalPlaces)');
        $this->datatables->edit_column('companyReportingAmount', '<div class="pull-right"> $1 </div>', 'format_number(companyReportingAmount,companyReportingDecimalPlaces)');
        $this->datatables->edit_column('DepcompanyLocalAmount', '<div class="pull-right"> $1 </div>', 'format_number(DepcompanyLocalAmount,DepcompanyLocalCurrencyDecimalPlaces)');
        $this->datatables->edit_column('DepcompanyReportingAmount', '<div class="pull-right"> $1 </div>', 'format_number(DepcompanyReportingAmount,DepcompanyReportingCurrencyDecimalPlaces)');
        echo $this->datatables->generate();
    }

    function delete_asset_depreciation()
    {
        $this->form_validation->set_rules('depMasterAutoID', 'ID', 'trim|required');

        $depMasterAutoID = $this->input->post('depMasterAutoID');
        $ifConfirmed = $this->db->query("SELECT * FROM srp_erp_fa_depmaster WHERE depMasterAutoID='{$depMasterAutoID}' AND confirmedYN='1'")->row_array();

        if ($ifConfirmed) {
            $this->session->set_flashdata($msgtype = 'e', 'You cannot delete. Depreciation Already Confirmed.');
            exit();
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->delete_asset_depreciation());
        }
    }

    function load_asset_dep_master()
    {
        $this->datatables->select('
                ROUND(assetdepreciationperiods.companyLocalAmount, 2) as companyLocalAmount,
                ROUND(assetdepreciationperiods.companyReportingAmount, 2) as companyReportingAmount,        
                FORMAT(
                assetdepreciationperiods.transactionAmount,
                assetdepreciationperiods.transactionCurrencyDecimalPlaces
            ) AS sumtransactionAmount,
            
            FORMAT(
                assetdepreciationperiods.companyLocalAmount
                ,
                assetdepreciationperiods.companyLocalCurrencyDecimalPlaces
            ) AS sumcompanyLocalAmount,
            FORMAT(
                assetdepreciationperiods.companyReportingAmount,
                assetdepreciationperiods.companyReportingCurrencyDecimalPlaces
            ) AS sumcompanyReportingAmount,
            
        srp_erp_fa_depmaster.depMasterAutoID,
        srp_erp_fa_depmaster.depCode as depCode,
        srp_erp_fa_depmaster.depDate as depDate,
        srp_erp_fa_depmaster.depMonthYear as depMonthYear,
        srp_erp_fa_depmaster.transactionCurrency as transactionCurrency,
        srp_erp_fa_depmaster.confirmedYN AS confirmedYN,
        srp_erp_fa_depmaster.approvedYN AS approvedYN,
        srp_erp_fa_depmaster.depMasterAutoID AS depMasterAutoID,srp_erp_fa_depmaster.createdUserID AS createdUserID,srp_erp_fa_depmaster.confirmedByEmpID as confirmedByEmp', false);
        $this->datatables->from('srp_erp_fa_depmaster');
        //$this->datatables->join('srp_erp_fa_assetdepreciationperiods', 'srp_erp_fa_depmaster.depMasterAutoID= srp_erp_fa_assetdepreciationperiods.depMasterAutoID');
        $this->datatables->join('(SELECT depMasterAutoID,SUM(transactionAmount) as transactionAmount,transactionCurrencyDecimalPlaces,SUM(companyLocalAmount) as companyLocalAmount ,companyLocalCurrencyDecimalPlaces,SUM(companyReportingAmount) as companyReportingAmount,companyReportingCurrencyDecimalPlaces FROM srp_erp_fa_assetdepreciationperiods
            GROUP BY
            `srp_erp_fa_assetdepreciationperiods`.`depMasterAutoID` ) as assetdepreciationperiods', 'srp_erp_fa_depmaster.depMasterAutoID= assetdepreciationperiods.depMasterAutoID');
        $this->datatables->group_by('srp_erp_fa_depmaster.depCode');
        $this->datatables->where('srp_erp_fa_depmaster.companyID', current_companyID());
        $this->datatables->where('srp_erp_fa_depmaster.depType', 0);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"FAD",depMasterAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"FAD",depMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'view_depreciation(depMasterAutoID,confirmedYN,approvedYN,createdUserID,confirmedByEmp)');
        echo $this->datatables->generate();
    }

    function load_asset_dep_master_adhoc()
    {
        $this->datatables->select('
        ROUND(assetdepreciationperiods.companyLocalAmount, 2) as companyLocalAmount,
        ROUND(assetdepreciationperiods.companyReportingAmount, 2) as companyReportingAmount,
        FORMAT(assetdepreciationperiods.transactionAmount , assetdepreciationperiods.transactionCurrencyDecimalPlaces ) AS sumtransactionAmount,
	FORMAT(assetdepreciationperiods.companyLocalAmount , assetdepreciationperiods.companyLocalCurrencyDecimalPlaces ) AS sumcompanyLocalAmount,
	FORMAT(assetdepreciationperiods.companyReportingAmount , assetdepreciationperiods.companyReportingCurrencyDecimalPlaces ) AS sumcompanyReportingAmount,
srp_erp_fa_depmaster.depCode as depCode,
srp_erp_fa_depmaster.depMasterAutoID,
srp_erp_fa_depmaster.depDate as depDate,
srp_erp_fa_depmaster.depMonthYear as depMonthYear,
srp_erp_fa_depmaster.transactionCurrency as transactionCurrency,
srp_erp_fa_depmaster.confirmedYN AS confirmedYN,
srp_erp_fa_depmaster.approvedYN AS approvedYN,
srp_erp_fa_depmaster.depMasterAutoID AS depMasterAutoID,srp_erp_fa_depmaster.createdUserID AS createdUserID,srp_erp_fa_depmaster.confirmedByEmpID as confirmedByEmp');
        $this->datatables->from('srp_erp_fa_depmaster');
//        $this->datatables->join('srp_erp_fa_assetdepreciationperiods', 'srp_erp_fa_depmaster.depMasterAutoID= srp_erp_fa_assetdepreciationperiods.depMasterAutoID');
        $this->datatables->join('(SELECT depMasterAutoID, SUM(transactionAmount) as transactionAmount, transactionCurrencyDecimalPlaces, SUM(companyLocalAmount) as companyLocalAmount, companyLocalCurrencyDecimalPlaces, SUM(companyReportingAmount) as companyReportingAmount, companyReportingCurrencyDecimalPlaces FROM srp_erp_fa_assetdepreciationperiods GROUP BY depMasterAutoID) as assetdepreciationperiods', 'srp_erp_fa_depmaster.depMasterAutoID= assetdepreciationperiods.depMasterAutoID');
        $this->datatables->group_by('srp_erp_fa_depmaster.depCode');
        $this->datatables->where('srp_erp_fa_depmaster.companyID', current_companyID());
        $this->datatables->where('srp_erp_fa_depmaster.depType', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm(confirmedYN)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"FAD",depMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'view_depreciation_adhoc(depMasterAutoID,confirmedYN,approvedYN,createdUserID,confirmedByEmp)');
        echo $this->datatables->generate();
    }

    function groupToAsset()
    {
        $this->AssetManagemnt_model->groupToAsset();
    }

    function fetchGLCode()
    {
        $faCatID = $this->input->post('faCatID');
        $gls = $this->db->query("SELECT faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID,faDISPOGLAutoID FROM srp_erp_itemcategory WHERE itemCategoryID='{$faCatID}'")->result_array();

        echo json_encode($gls);
    }

    function editAsset()
    {
        $faID = $this->input->post('faID');
        $data = $this->db->query("SELECT CONCAT(segmentID, '|', segmentCode) segment ,srp_erp_fa_asset_master.* FROM `srp_erp_fa_asset_master` WHERE `faID` = '$faID'")->row_array();
        echo json_encode($data);
    }

    function assetDepConfirm()
    {
        $depMasterAutoID = trim($this->input->post('depMasterAutoID') ?? '');
        echo json_encode($this->AssetManagemnt_model->assetDepConfirm());
    }

    function fetch_depreciation_approval()
    {
        /*
                 * rejected = 1
                 * not rejected = 0
                 * */
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('depMasterAutoID,srp_erp_fa_depmaster.companyCode,depCode,depType,DATE_FORMAT(depDate,\'%Y-%m-%d\') AS depDate,confirmedYN,srp_erp_documentapproved.approvedYN AS approvedYN,documentApprovedID,approvalLevelID,FYPeriodDateFrom,FYPeriodDateTo');
            $this->datatables->from('srp_erp_fa_depmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_fa_depmaster.depMasterAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_fa_depmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_fa_depmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'FAD');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'FAD');
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_fa_depmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->group_by('srp_erp_fa_depmaster.depMasterAutoID');
            $this->datatables->add_column('depType', '$1', 'depreicationType(depType)');
            $this->datatables->add_column('depCode', '<a onclick=\'fetch_approval("$2","$3","$4","$5"); \'>$1</a>', 'depCode,depMasterAutoID,documentApprovedID,approvalLevelID,approvedYN');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "FAD", depMasterAutoID)');
            $this->datatables->add_column('edit', '$1', 'dep_action_approval(depMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('depMasterAutoID,srp_erp_fa_depmaster.companyCode,depCode,depType,DATE_FORMAT(depDate,\'%Y-%m-%d\') AS depDate,confirmedYN,srp_erp_documentapproved.approvedYN AS approvedYN,documentApprovedID,approvalLevelID,FYPeriodDateFrom,FYPeriodDateTo');
            $this->datatables->from('srp_erp_fa_depmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_fa_depmaster.depMasterAutoID');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'FAD');
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->where('srp_erp_fa_depmaster.companyID', $companyID);

            $this->datatables->group_by('srp_erp_fa_depmaster.depMasterAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('depType', '$1', 'depreicationType(depType)');
            $this->datatables->add_column('depCode', '<a onclick=\'fetch_approval("$2","$3","$4","$5"); \'>$1</a>', 'depCode,depMasterAutoID,documentApprovedID,approvalLevelID,approvedYN');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "FAD", depMasterAutoID)');
            $this->datatables->add_column('edit', '$1', 'dep_action_approval(depMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');

            echo $this->datatables->generate();
        }

    }

    function load_dep_conformation()
    {
        $depMasterAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('depMasterAutoID') ?? '');
        $data['total'] = $this->AssetManagemnt_model->fetch_dep_total($depMasterAutoID);
        $data['depMasterAutoID'] = $depMasterAutoID;
        $html = $this->load->view('system/asset_management/asset_dep_approve', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        }
    }

    function save_depreciation_approval()
    {
        $this->load->library('Approvals');
        $system_code = trim($this->input->post('depMasterAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $company_id = current_companyID();

        $this->db->select('depMasterAutoID');
        $this->db->where('depMasterAutoID', trim($system_code));
        $this->db->where('confirmedYN', 2);
        $this->db->from('srp_erp_fa_depmaster');
        $po_approved = $this->db->get()->row_array();
        if (!empty($po_approved)) {
            $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
            echo json_encode(FALSE);
        } else {
            $rejectYN = checkApproved($system_code, 'FAD', $level_id);
            if (!empty($rejectYN)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                if ($this->input->post('status') == 2) {
                    $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                }
                $this->form_validation->set_rules('status', 'Status', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $this->session->set_flashdata($msgtype = 'e', validation_errors());
                    echo json_encode(FALSE);
                } else {

                    $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'FAD');

                    $maxlevel = $this->db->query("SELECT MAX(levelNo) as maxLevel FROM srp_erp_approvalusers where documentID = 'FAD' AND companyID = '{$company_id}'")->row_array();

                    if ($maxlevel['maxLevel'] == $level_id) {
                        if ($status == 1) {
                            $this->AssetManagemnt_model->save_depreciation_approval($system_code);
                        }
                    }

                    echo true;
                }
            }
        }
        exit;
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'FAD', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('depMasterAutoID');
                $this->db->where('depMasterAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_fa_depmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);

                    } else {

                        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'FAD');

                        $maxlevel = $this->db->query("SELECT MAX(levelNo) as maxLevel FROM srp_erp_approvalusers where documentID = 'FAD' AND companyID = '{$company_id}'")->row_array();

                        if ($maxlevel['maxLevel'] == $level_id) {
                            if ($status == 1) {
                                $this->AssetManagemnt_model->post_to_gl($system_code);
                            }
                        }

                        echo true;
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('depMasterAutoID');
            $this->db->where('depMasterAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_fa_depmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'FAD', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'FAD');

                        if ($status == 1) {
                            $this->AssetManagemnt_model->save_depreciation_approval($system_code);
                        }

                        echo true;
                    }
                }
            }
        }
    }

    function fetch_asset_register()
    {
        $fiancecategory = $_POST['fiancecategory'];
        $mainCategory = $_POST['mainCategory'];
        /*$subCategory = $_POST['subCategory'];*/
        $wh = '';
        if ($fiancecategory) {
            //$wh .= "AND srp_erp_fa_asset_master.faCatID IN ($fiancecategory)";
        }

        if ($mainCategory) {
            $wh .= "AND srp_erp_fa_asset_master.faCatID IN ($mainCategory)";//faSubCatID
        }

        /*if ($subCategory) {
            $wh .= "AND srp_erp_fa_asset_master.faSubCatID IN ($subCategory)";//faSubCatID2
        }*/

        $date = $_POST['dateAsOf'];

        $this->datatables->select('@LocalAmountDep :=
IF (
	ISNULL(`LocalAmountDep`),
	0,
	`LocalAmountDep`
) AS LocalAmountDep,
 FORMAT(
	@LocalAmountDep,
	srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces
) AS companyLocalAmountDep,
 FORMAT(
	`srp_erp_fa_asset_master`.`companyLocalAmount`,
	srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces
) AS companyLocalAmount,
 		@ntbTransection :=(

	IF (
		ISNULL(
			`srp_erp_fa_asset_master`.`companyLocalAmount`
		),
		0,

	IF (
		ISNULL(
			`srp_erp_fa_asset_master`.`companyLocalAmount`
		),
		0,
		`srp_erp_fa_asset_master`.`companyLocalAmount`
	)
	) -
	IF (
		ISNULL(`LocalAmountDep`),
		0,
		`LocalAmountDep`
	)
) AS ntbTransection,
 FORMAT(
	@ntbTransection,
	srp_erp_fa_asset_master.transactionCurrencyDecimalPlaces
) AS netBookTransectionValue,
 @ReportingDepAmount :=
IF (
	ISNULL(`ReportingDepAmount`),
	0,
	`ReportingDepAmount`
) AS `ReportingDepAmount`,
 FORMAT(
	@ReportingDepAmount,
	srp_erp_fa_asset_master.companyReportingDecimalPlaces
) AS totalReportingDepAmount,
 FORMAT(
	`srp_erp_fa_asset_master`.`companyReportingAmount`,
	srp_erp_fa_asset_master.companyReportingDecimalPlaces
) AS companyReportingAmount,
 @nbvReporting := (

	IF (
		ISNULL(
			`srp_erp_fa_asset_master`.`companyReportingAmount`
		),
		0,
		`srp_erp_fa_asset_master`.`companyReportingAmount`
	) -
	IF (
		ISNULL(`ReportingDepAmount`),
		0,
		`ReportingDepAmount`
	)
) AS nbvReporting,
 FORMAT(
	@nbvReporting,
	srp_erp_fa_asset_master.companyReportingDecimalPlaces
) netBookRepotingValue,
 `srp_erp_fa_asset_master`.`faCode`,
 `srp_erp_fa_asset_master`.`costGLCode`,
 `srp_erp_fa_asset_master`.`faID` AS faID,
 `srp_erp_fa_asset_master`.`faUnitSerialNo`,
 `srp_erp_fa_asset_master`.`dateAQ` AS dateAQ,
 `srp_erp_fa_asset_master`.`dateDEP` AS dateDEP,
 `srp_erp_fa_asset_master`.`transactionCurrencyDecimalPlaces` AS transactionCurrencyDecimalPlaces,
 `srp_erp_fa_asset_master`.`companyReportingDecimalPlaces` AS companyReportingDecimalPlaces,
 `srp_erp_fa_asset_master`.`assetDescription` AS assetDescription,
 `srp_erp_fa_asset_master`.`serialNo` AS serialNo,
 `srp_erp_itemcategory`.`description` AS description', false);
        $this->datatables->from('srp_erp_fa_asset_master
LEFT JOIN srp_erp_itemcategory ON srp_erp_fa_asset_master.faCatID = srp_erp_itemcategory.itemCategoryID
LEFT JOIN (
	SELECT
		SUM(
			srp_erp_fa_assetdepreciationperiods.companyLocalAmount
		) LocalAmountDep,
		SUM(
			`srp_erp_fa_assetdepreciationperiods`.`companyReportingAmount`
		) ReportingDepAmount,
		faID
	FROM
		srp_erp_fa_depmaster
	INNER JOIN srp_erp_fa_assetdepreciationperiods ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID
	WHERE
		srp_erp_fa_depmaster.approvedYN = 1
	AND srp_erp_fa_depmaster.depDate <= "' . $date . '"
	GROUP BY
		faID
) depAmountQry ON srp_erp_fa_asset_master.faID = depAmountQry.faID');
        $this->datatables->where('	srp_erp_fa_asset_master.approvedYN = 1
        AND srp_erp_fa_asset_master.assetType = 1
AND srp_erp_fa_asset_master.postDate <="' . $date . '" AND (
	srp_erp_fa_asset_master.disposedDate >= "' . $date . '"
	OR srp_erp_fa_asset_master.disposedDate IS NULL
) ' . $wh . '')
            //$this->datatables->group_by('');
            /* ->edit_column('totaltransectionDepAmount', '$1', 'format_number(totaltransectionDepAmount,transactionCurrencyDecimalPlaces)')
             ->edit_column('netBookTransectionValue', '$1', 'format_number(netBookTransectionValue,transactionCurrencyDecimalPlaces)')
             ->edit_column('transactionAmount', '$1', 'format_number(transactionAmount,transactionCurrencyDecimalPlaces)')
             ->edit_column('companyReportingAmount', '$1', 'format_number(companyReportingAmount,companyReportingDecimalPlaces)')
             ->edit_column('totalReportingDepAmount', '$1', 'format_number(totalReportingDepAmount,companyReportingDecimalPlaces)')
             ->edit_column('netBookRepotingValue', '$1', 'format_number(netBookRepotingValue,companyReportingDecimalPlaces)')*/
            ->edit_column('dateDEP', '$1', 'format_date(dateDEP)')
            ->edit_column('dateAQ', '$1', 'format_date(dateAQ)')
            ->edit_column('action', '<a href="#" onclick="assetMasterView($1)"><i class="glyphicon glyphicon-eye-open"></i></a>', 'faID');
        $this->datatables->generate();
        echo $this->datatables->last_query();

    }

    function referback_asset()
    {
        echo json_encode($this->AssetManagemnt_model->referback_asset());
    }

    function assetRegisterSummaryGenerated()
    {
        $array['date'] = $this->input->post('dateAsOf');
        return $this->load->view('system/asset_management/asset_register_summary_generated', $array);
    }

    function view_asset_register()
    {
        $datas = $this->view_assets($_POST);
        return $this->load->view('system/asset_management/view_asset_register', compact('datas'));
    }

    function view_assets($params = array())
    {
        $categroy = $this->input->post('categroy');
        $startFinanceYear = $this->input->post('startFinanceYear');
        $endFinanceYear = $this->input->post('endFinanceYear');
        $lastFinanceYear = $this->input->post('lastFinanceYear');
        $dateAsOf = $this->input->post('dateAsOf');
        $type = $this->input->post('type');


        $companyId = $this->common_data['company_data']['company_id'];
        if ($type == 'pre_year') {
            $data = $this->db->query("SELECT srp_erp_fa_asset_master.faID, srp_erp_fa_asset_master.faCatID, DATE_FORMAT(srp_erp_fa_asset_master.disposedDate,'%Y-%m-%d') AS disposedDate,DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,'%Y-%m-%d') AS dateAQ,DATE_FORMAT(srp_erp_fa_asset_master.postDate,'%Y-%m-%d') AS postDate, mastercategoryTabel.description AS masterDescription, subcategoryTabel.description AS subDescription, srp_erp_fa_asset_master.companyLocalAmount AS companyLocalAmount,companyLocalCurrencyDecimalPlaces, srp_erp_fa_asset_master.transactionAmount, srp_erp_fa_asset_master.faCode, srp_erp_fa_asset_master.assetDescription FROM `srp_erp_fa_asset_master` JOIN `srp_erp_itemcategory` AS `mastercategoryTabel` ON `srp_erp_fa_asset_master`.`faCatID` = `mastercategoryTabel`.`itemCategoryID` JOIN `srp_erp_itemcategory` AS `subcategoryTabel` ON `srp_erp_fa_asset_master`.`faSubCatID` = `subcategoryTabel`.`itemCategoryID` WHERE `srp_erp_fa_asset_master`.`companyID` = '{$companyId}' AND `srp_erp_fa_asset_master`.`approvedYN` = '1' AND `srp_erp_fa_asset_master`.`faCatID` = '{$categroy}' AND `srp_erp_fa_asset_master`.`postDate` <= '{$lastFinanceYear}' AND (`srp_erp_fa_asset_master`.`disposedDate` >= '{$lastFinanceYear}' OR `srp_erp_fa_asset_master`.`disposedDate` IS NULL)")->result_array();

        } elseif ($type == 'cur_year') {
            $data = $this->db->query("SELECT srp_erp_fa_asset_master.faID, srp_erp_fa_asset_master.faCatID, DATE_FORMAT(srp_erp_fa_asset_master.disposedDate,'%Y-%m-%d') AS disposedDate,DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,'%Y-%m-%d') AS dateAQ,DATE_FORMAT(srp_erp_fa_asset_master.postDate,'%Y-%m-%d') AS postDate, mastercategoryTabel.description AS masterDescription, subcategoryTabel.description AS subDescription, srp_erp_fa_asset_master.companyLocalAmount AS companyLocalAmount,companyLocalCurrencyDecimalPlaces, srp_erp_fa_asset_master.transactionAmount, srp_erp_fa_asset_master.faCode, srp_erp_fa_asset_master.assetDescription FROM `srp_erp_fa_asset_master` JOIN `srp_erp_itemcategory` AS `mastercategoryTabel` ON `srp_erp_fa_asset_master`.`faCatID` = `mastercategoryTabel`.`itemCategoryID` JOIN `srp_erp_itemcategory` AS `subcategoryTabel` ON `srp_erp_fa_asset_master`.`faSubCatID` = `subcategoryTabel`.`itemCategoryID` WHERE `srp_erp_fa_asset_master`.`companyID` = '{$companyId}' AND `srp_erp_fa_asset_master`.`approvedYN` = '1' AND `srp_erp_fa_asset_master`.`faCatID` = '$categroy' AND `srp_erp_fa_asset_master`.`postDate` >= '{$startFinanceYear}' AND `srp_erp_fa_asset_master`.`postDate` <= '{$dateAsOf}'")->result_array();

        } elseif ($type == 'disposal') {
            $data = $this->db->query("SELECT srp_erp_fa_asset_master.faID, srp_erp_fa_asset_master.faCatID, DATE_FORMAT(srp_erp_fa_asset_master.disposedDate,'%Y-%m-%d') AS disposedDate,DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,'%Y-%m-%d') AS dateAQ,DATE_FORMAT(srp_erp_fa_asset_master.postDate,'%Y-%m-%d') AS postDate, mastercategoryTabel.description AS masterDescription, subcategoryTabel.description AS subDescription, srp_erp_fa_asset_master.companyLocalAmount AS companyLocalAmount,companyLocalCurrencyDecimalPlaces, srp_erp_fa_asset_master.transactionAmount, srp_erp_fa_asset_master.faCode, srp_erp_fa_asset_master.assetDescription FROM `srp_erp_fa_asset_master` JOIN `srp_erp_itemcategory` AS `mastercategoryTabel` ON `srp_erp_fa_asset_master`.`faCatID` = `mastercategoryTabel`.`itemCategoryID` JOIN `srp_erp_itemcategory` AS `subcategoryTabel` ON `srp_erp_fa_asset_master`.`faSubCatID` = `subcategoryTabel`.`itemCategoryID` WHERE `srp_erp_fa_asset_master`.`companyID` = '{$companyId}' AND `srp_erp_fa_asset_master`.`approvedYN` = '1' AND `srp_erp_fa_asset_master`.`disposed` = '1' AND `srp_erp_fa_asset_master`.`faCatID` = '{$categroy}' AND `srp_erp_fa_asset_master`.`disposedDate` >= '{$startFinanceYear}' AND `srp_erp_fa_asset_master`.`disposedDate` <= '{$dateAsOf}'")->result_array();
        }

        return $data;
    }

    function view_dep_register()
    {
        $datas = $this->view_dep($_POST);
        return $this->load->view('system/asset_management/view_dep_register', compact('datas'));
    }

    public function view_dep($params = array())
    {
        $categroy = $this->input->post('categroy');
        $startFinanceYear = $this->input->post('startFinanceYear');
        $endFinanceYear = $this->input->post('endFinanceYear');
        $lastFinanceYear = $this->input->post('lastFinanceYear');
        $dateAsOf = $this->input->post('dateAsOf');
        $type = $this->input->post('type');

        $companyId = $this->common_data['company_data']['company_id'];

        if ($type == 'pre_year') {
            $data = $this->db->query("SELECT FORMAT((
		srp_erp_fa_assetdepreciationperiods.companyLocalAmount
	),srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces) AS companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces,
	masterCategoryTable.description AS masterDescription,
	subCategoryTable.description AS subDescription,
	srp_erp_fa_asset_master.faID,
	srp_erp_fa_asset_master.faCode,
	srp_erp_fa_asset_master.assetDescription,
	srp_erp_fa_asset_master.depMonth,
	DATE_FORMAT(srp_erp_fa_asset_master.dateDEP,'%Y-%m-%d') AS dateDEP,
	DATE_FORMAT(srp_erp_fa_asset_master.postDate,'%Y-%m-%d') AS postDate,
	DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,'%Y-%m-%d') AS dateAQ FROM `srp_erp_fa_depmaster` JOIN `srp_erp_fa_assetdepreciationperiods` ON `srp_erp_fa_depmaster`.`depMasterAutoID` = `srp_erp_fa_assetdepreciationperiods`.`depMasterAutoID` LEFT JOIN `srp_erp_itemcategory` `masterCategoryTable` ON `srp_erp_fa_assetdepreciationperiods`.`faMainCategory` = `masterCategoryTable`.`itemCategoryID` LEFT JOIN `srp_erp_itemcategory` `subCategoryTable` ON `srp_erp_fa_assetdepreciationperiods`.`faSubCategory` = `subCategoryTable`.`itemCategoryID` LEFT JOIN `srp_erp_fa_asset_master` ON `srp_erp_fa_assetdepreciationperiods`.`faID` = `srp_erp_fa_asset_master`.`faID` WHERE `srp_erp_fa_asset_master`.`companyID` = '{$companyId}' AND `srp_erp_fa_depmaster`.`approvedYN` = 1 AND `srp_erp_fa_asset_master`.`faCatID` = '{$categroy}' AND `srp_erp_fa_depmaster`.`depDate` <= '{$lastFinanceYear}' AND (`srp_erp_fa_asset_master`.`disposedDate` >= '{$lastFinanceYear}' OR `srp_erp_fa_asset_master`.`disposedDate` IS NULL)")->result_array();

        } elseif ($type == 'cur_year') {

            $data = $this->db->query("SELECT FORMAT((
		srp_erp_fa_assetdepreciationperiods.companyLocalAmount
	),srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces) AS companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces,
	masterCategoryTable.description AS masterDescription,
	subCategoryTable.description AS subDescription,
	srp_erp_fa_asset_master.faID,
	srp_erp_fa_asset_master.faCode,
	srp_erp_fa_asset_master.assetDescription,
	srp_erp_fa_asset_master.depMonth,
	DATE_FORMAT(srp_erp_fa_asset_master.dateDEP,'%Y-%m-%d') AS dateDEP,
	DATE_FORMAT(srp_erp_fa_asset_master.postDate,'%Y-%m-%d') AS postDate,
	DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,'%Y-%m-%d') AS dateAQ FROM `srp_erp_fa_depmaster` JOIN `srp_erp_fa_assetdepreciationperiods` ON `srp_erp_fa_depmaster`.`depMasterAutoID` = `srp_erp_fa_assetdepreciationperiods`.`depMasterAutoID` LEFT JOIN `srp_erp_itemcategory` `masterCategoryTable` ON `srp_erp_fa_assetdepreciationperiods`.`faMainCategory` = `masterCategoryTable`.`itemCategoryID` LEFT JOIN `srp_erp_itemcategory` `subCategoryTable` ON `srp_erp_fa_assetdepreciationperiods`.`faSubCategory` = `subCategoryTable`.`itemCategoryID` LEFT JOIN `srp_erp_fa_asset_master` ON `srp_erp_fa_assetdepreciationperiods`.`faID` = `srp_erp_fa_asset_master`.`faID` WHERE `srp_erp_fa_asset_master`.`companyID` = '{$companyId}' AND `srp_erp_fa_depmaster`.`approvedYN` = 1 AND `srp_erp_fa_asset_master`.`faCatID` = '{$categroy}' AND `srp_erp_fa_depmaster`.`depDate` >= '{$startFinanceYear}' AND `srp_erp_fa_depmaster`.`depDate` <= '{$dateAsOf}'")->result_array();
        } elseif ($type == 'disposal') {

            $data = $this->db->query("SELECT FORMAT((
		srp_erp_fa_assetdepreciationperiods.companyLocalAmount
	),srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces) AS companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces,
	masterCategoryTable.description AS masterDescription,
	subCategoryTable.description AS subDescription,
	srp_erp_fa_asset_master.faID,
	srp_erp_fa_asset_master.faCode,
	srp_erp_fa_asset_master.assetDescription,
	srp_erp_fa_asset_master.depMonth,
	DATE_FORMAT(srp_erp_fa_asset_master.dateDEP,'%Y-%m-%d') AS dateDEP,
	DATE_FORMAT(srp_erp_fa_asset_master.postDate,'%Y-%m-%d') AS postDate,
	DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,'%Y-%m-%d') AS dateAQ FROM `srp_erp_fa_depmaster` JOIN `srp_erp_fa_assetdepreciationperiods` ON `srp_erp_fa_depmaster`.`depMasterAutoID` = `srp_erp_fa_assetdepreciationperiods`.`depMasterAutoID` LEFT JOIN `srp_erp_itemcategory` `masterCategoryTable` ON `srp_erp_fa_assetdepreciationperiods`.`faMainCategory` = `masterCategoryTable`.`itemCategoryID` LEFT JOIN `srp_erp_itemcategory` `subCategoryTable` ON `srp_erp_fa_assetdepreciationperiods`.`faSubCategory` = `subCategoryTable`.`itemCategoryID` LEFT JOIN `srp_erp_fa_asset_master` ON `srp_erp_fa_assetdepreciationperiods`.`faID` = `srp_erp_fa_asset_master`.`faID` WHERE `srp_erp_fa_asset_master`.`companyID` = '{$companyId}' AND `srp_erp_fa_depmaster`.`approvedYN` = 1 AND `srp_erp_fa_asset_master`.`faCatID` = '{$categroy}' AND `srp_erp_fa_asset_master`.`disposed` = '1' AND `srp_erp_fa_asset_master`.`disposedDate` >= '{$startFinanceYear}' AND `srp_erp_fa_asset_master`.`disposedDate` <= '{$dateAsOf}'")->result_array();
        }
        return $data;
    }

    public function load_attachment()
    {
        $documentID = $this->input->post('documentID');
        $documentSystemCode = $this->input->post('documentSystemCode');
        $path = base_url();
        $this->datatables->select('attachmentID,documentID,attachmentDescription, myFileName,docExpiryDate,fileType,dateofIssued')
            ->from('srp_erp_documentattachments')
            ->where('srp_erp_documentattachments.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_documentattachments.documentID', $documentID)
            ->where('srp_erp_documentattachments.documentSystemCode', $documentSystemCode)
            //->edit_column('myFileName', '<a target="_blank" class="pull-right" href="../$1"><span class="glyphicon glyphicon-paperclip"></span></a>', 'myFileName')
            ->edit_column('myFileName', '$1', 'generate_aws_encryption_link(myFileName,attachmentDescription)')
            ->add_column('delete', '$1', 'delete_attachment(attachmentID)');

        echo $this->datatables->generate();
    }

    function save_attachment()
    {
        $this->form_validation->set_rules('document_description', 'document_description', 'trim|required');
//        $this->form_validation->set_rules('dateissued', 'dateissued', 'trim|required');
//        $this->form_validation->set_rules('dateexpired', 'dateexpired', 'trim|required');
        /*$this->form_validation->set_rules('file', 'document_file', 'trim|required');*/
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->save_attachment());
        }
    }

    function load_disposal()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('assetdisposalMasterAutoID AS assetdisposalMasterAutoID,serialNo,segmentCode,companyCode,documentID,disposalDocumentCode,disposalDocumentDate,narration,confirmedYN AS confirmedYN,approvedYN AS approvedYN,confirmedByEmpID as confirmedByEmpID,createdUserID as createdUserID')
            ->where('srp_erp_fa_asset_disposalmaster.companyID', $this->common_data['company_data']['company_id'])
            ->from('srp_erp_fa_asset_disposalmaster');
        $this->datatables->add_column('confirmed', '$1', 'confirm(confirmedYN)');
        $this->datatables->add_column('approved', '$1', 'confirm(approvedYN)');
        $this->datatables->add_column('edit', '$1', 'load_asset_disposal_action(assetdisposalMasterAutoID,confirmedYN,approvedYN,createdUserID,confirmedByEmpID)');
        $this->datatables->edit_column('disposalDocumentDate', '<span >$1 </span>', 'convert_date_format(disposalDocumentDate)');
        echo $this->datatables->generate();
    }

    function load_selected_disposal_asset()
    {

        $assetdisposalMasterAutoID = $_POST['assetdisposalMasterAutoID'];
        $this->datatables->select('srp_erp_fa_asset_disposaldetail.faCode,srp_erp_fa_asset_disposaldetail.assetDescription,FORMAT(srp_erp_fa_asset_disposaldetail.netBookValueLocalAmount,srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces) As netBookValueLocalAmount,FORMAT(srp_erp_fa_asset_disposaldetail.companyLocalAmount,srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces) AS disposalAmount,FORMAT(srp_erp_fa_asset_disposaldetail.depLocalAmount,srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces) AS depLocalAmount, DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,\'%Y/%m/%d\') AS dateAQ ,FORMAT(accLocalAmount,srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces) AS accLocalAmount,FORMAT(netBookValueLocalAmount,srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces)AS netBookValueLocalAmount,srp_erp_fa_asset_master.serialNo,srp_erp_fa_asset_disposaldetail.assetDisposalDetailAutoID AS assetDisposalDetailAutoID,srp_erp_fa_asset_master.faID AS faID,FORMAT(srp_erp_fa_asset_master.companyLocalAmount,srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces) AS companyLocalAmount')
            ->from('srp_erp_fa_asset_disposaldetail')
            ->join('srp_erp_fa_asset_master', 'srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID')
            ->where('srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID', '' . $assetdisposalMasterAutoID . '');
        $this->datatables->add_column('edit', '$1', 'remove_asset_to_diposal(assetDisposalDetailAutoID,faID)');
        echo $this->datatables->generate();
    }

    function asset_image_upload()
    {
        $this->form_validation->set_rules('faID', 'Document Id is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->asset_image_upload());
        }
    }

    function delete_attachment()
    {
        $this->form_validation->set_rules('faID', 'Document Id is missing', 'trim|required');
        $this->form_validation->set_rules('index', 'Document Id is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->delete_attachment());
        }
    }

    function get_asset_details()
    {
        $data['faID'] = $this->input->post('faId');
        $this->load->view('system/asset_management/add_new_asset', $data);
    }

    function load_not_disposed_asset()
    {
        $companyId = current_companyID();
        //$documentDate = $this->input->post('documentDate');
        $date_format_policy = date_format_policy();
        $docdt = $this->input->post('documentDate');
        $documentDate = input_format_date($docdt, $date_format_policy);
        $segment = explode('|', trim($this->input->post('segment') ?? ''));


        $this->datatables->select(" @accLocalAmount := FORMAT(IF(ISNULL(accLocalAmount),0,accLocalAmount),companyLocalCurrencyDecimalPlaces) AS accLocalAmount,
            srp_erp_fa_asset_master.faID AS faID,
            srp_erp_fa_asset_master.faCode,
            srp_erp_fa_asset_master.serialNo,
            FORMAT(srp_erp_fa_asset_master.companyLocalAmount,companyLocalCurrencyDecimalPlaces) AS companyLocalAmount,
            DATE_FORMAT(srp_erp_fa_asset_master.dateAQ,'%Y/%m/%d') AS dateAQ,
            srp_erp_fa_asset_master.dateDEP,
            srp_erp_fa_asset_master.DEPpercentage,
            srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces,
            srp_erp_fa_asset_master.assetDescription,
            srp_erp_fa_asset_master.salvageAmount AS salvageAmount,
            @nbv := (
            srp_erp_fa_asset_master.companyLocalAmount - IF(ISNULL(accLocalAmount),0,accLocalAmount)
            ) nbv,
            FORMAT(IF (ISNULL(@nbv), 0 ,@nbv),companyLocalCurrencyDecimalPlaces) AS netBookValueLocalAmount")
                    ->from(" srp_erp_fa_asset_master
            LEFT JOIN (
                SELECT
                    srp_erp_fa_assetdepreciationperiods.faID,
                    SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS  accLocalAmount
                FROM
                    srp_erp_fa_assetdepreciationperiods
                INNER JOIN srp_erp_fa_depmaster ON srp_erp_fa_assetdepreciationperiods.depMasterAutoID = srp_erp_fa_depmaster.depMasterAutoID
                WHERE
                    srp_erp_fa_assetdepreciationperiods.companyID = '{$companyId}'
                AND srp_erp_fa_depmaster.approvedYN = '1'
                GROUP BY
                    faID
            ) assetdepreciationperiods ON srp_erp_fa_asset_master.faID = assetdepreciationperiods.faID")
                        ->where("  srp_erp_fa_asset_master.companyID = '{$companyId}'
            AND srp_erp_fa_asset_master.approvedYN = 1
            AND ((
                    srp_erp_fa_asset_master.disposed <> 1
                    OR srp_erp_fa_asset_master.disposed IS NULL
                )
                AND (
                    srp_erp_fa_asset_master.selectedForDisposal IS NULL
                    OR srp_erp_fa_asset_master.selectedForDisposal <> 1
                )) AND srp_erp_fa_asset_master.segmentID = '$segment[0]' AND srp_erp_fa_asset_master.assetType=1  AND srp_erp_fa_asset_master.postDate <=  '{$documentDate}' ");
                    $this->datatables->add_column('disposalAmount', '<input class="disposalAmount form-control input-sm" style="width: 79px;padding: 0px 2px;height: 24px;" id="disposalAmount_$1" name="disposalAmount" value="$2">', 'faID,salvageAmount');
                    $this->datatables->add_column('add', '$1', 'add_asset_to_diposal(faID)');
                    echo $this->datatables->generate();
    }

    function get_asset_disposal_details()
    {
        $convertFormat = convert_date_format_sql();
        $data['assetdisposalMasterAutoID'] = $this->input->post('assetdisposalMasterAutoID');
        $data['datas'] = $this->db->query("SELECT CONCAT(segmentID, '|', segmentCode) segment ,srp_erp_fa_asset_disposalmaster.*,DATE_FORMAT(srp_erp_fa_asset_disposalmaster.disposalDocumentDate,'.$convertFormat.') AS disposalDocumentDate FROM `srp_erp_fa_asset_disposalmaster` WHERE `assetdisposalMasterAutoID` = '{$data['assetdisposalMasterAutoID']}'")->row_array();
        $this->load->view('system/asset_management/add_new_disposal', $data);
    }

    function get_asset_dep_details()
    {
        $data['depMasterAutoID'] = $this->input->post('depMasterAutoID');
        $data['confirmedYN'] = $this->input->post('confirmedYN');
        $data['total'] = $this->AssetManagemnt_model->fetch_dep_total($data['depMasterAutoID']);

        $depMaster = $this->db->query("SELECT * FROM `srp_erp_fa_depmaster` WHERE `depMasterAutoID` = '{$data['depMasterAutoID']}'")->row_array();

        $data['header'] = '<table class="table table-bordered table-condensed" style="background-color: #f0f3f5;">
      
          
            <tbody>
            <tr>
                <td style="width: 110px;">Dep Month Year</td>
                <td class="bgWhite" style="width:35%">' . $depMaster['depMonthYear'] . '</td>
                <td style="width: 110px;">Financial Year</td>
                <td colspan="2" class="bgWhite">From: <span class="">' . date('d/m/Y', strtotime($depMaster['FYBegin'])) . '</span> To: <span class="">' . date('d/m/Y', strtotime($depMaster['FYEnd'])) . '</td>
            </tr>
            <tr>
                <td>Doc Code</td>
                <td class="bgWhite">' . $depMaster['depCode'] . '</td>
                <td>Financial Period</td>
                <td class="bgWhite" colspan="2">From: <span class="">' . date('d/m/Y', strtotime($depMaster['FYPeriodDateFrom'])) . '</span> To: <span class="">' . date('d/m/Y', strtotime($depMaster['FYPeriodDateTo'])) . '</span></td>
            </tr>
            <tr>
                <td>Doc Date</td>
                <td class="bgWhite">' . date('d/m/Y', strtotime($depMaster['depDate'])) . '</td>
                <td></td>
                <td class="bgWhite" colspan="2"></td>
            </tr>
            </tbody>
        </table>';

        $this->load->view('system/asset_management/asset_depreciation_generated', $data);
    }

    function get_asset_dep_details_adhoc()
    {
        $data['depMasterAutoID'] = $this->input->post('depMasterAutoID');
        $data['confirmedYN'] = $this->input->post('confirmedYN');

        $depMaster = $this->db->query("SELECT * FROM `srp_erp_fa_depmaster` WHERE `depMasterAutoID` = '{$data['depMasterAutoID']}'")->row_array();

        $data['header'] = '<table class="table table-bordered table-condensed" style="background-color: #f0f3f5;">
            <tbody>
            <tr>
                <td style="width: 110px;">Dep Month Year</td>
                <td class="bgWhite" style="width:35%">' . $depMaster['depMonthYear'] . '</td>
                <td style="width: 110px;">Financial Year</td>
                <td colspan="2" class="bgWhite">From: <span class="">' . date('d/m/Y', strtotime($depMaster['FYBegin'])) . '</span> To: <span class="">' . date('d/m/Y', strtotime($depMaster['FYEnd'])) . '</td>
            </tr>
            <tr>
                <td>Doc Code</td>
                <td class="bgWhite">' . $depMaster['depCode'] . '</td>
                <td>Financial Period</td>
                <td class="bgWhite" colspan="2">From: <span class="">' . date('d/m/Y', strtotime($depMaster['FYPeriodDateFrom'])) . '</span> To: <span class="">' . date('d/m/Y', strtotime($depMaster['FYPeriodDateTo'])) . '</span></td>
            </tr>
            <tr>
                <td>Doc Date</td>
                <td class="bgWhite">' . date('d/m/Y', strtotime($depMaster['depDate'])) . '</td>
                <td></td>
                <td class="bgWhite" colspan="2"></td>
            </tr>
            </tbody>
        </table>';

        $this->load->view('system/asset_management/asset_depreciation_generated_adhoc', $data);
    }

    function get_asset_dep_generate()
    {
        $this->load->view('system/asset_management/asset_depreciation');
    }

    /*Disposal*/
    function save_disposal_header()
    {
        $this->form_validation->set_rules('companyFinanceYearID', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        $this->form_validation->set_rules('disposalDocumentDate', 'Document Date', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');


        $financeyear_period = trim($this->input->post('financeyear_period') ?? '');
        //$disposalDocumentDate = trim($this->input->post('disposalDocumentDate') ?? '');
        $period = explode('|', $financeyear_period);

        $date_format_policy = date_format_policy();
        $disoslDocDte = $this->input->post('disposalDocumentDate');
        $disposalDocumentDate = input_format_date($disoslDocDte, $date_format_policy);

        // echo $period[0].'-'.$disposalDocumentDate.'-'.$period[1];

        if (($period[0] > $disposalDocumentDate) || ($period[1] < $disposalDocumentDate)) {
            $this->session->set_flashdata($msgtype = 'e', '<p>Disposal Document Date should be between Financial period.</p>');
            echo json_encode(FALSE);
            exit();
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->save_disposal_header());
        }
    }

    function add_to_disposal()
    {
        $this->form_validation->set_rules('assetdisposalMasterAutoID', 'ID', 'trim|required');
        $this->form_validation->set_rules('faId', 'Asset', 'trim|required');
        $this->form_validation->set_rules('disposalAmount', 'Disposal Amount', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->AssetManagemnt_model->add_to_disposal());
        }
    }

    function remove_asset_from_disposal()
    {
        $this->form_validation->set_rules('assetDisposalDetailAutoID', 'Asset', 'trim|required');
        $this->form_validation->set_rules('faId', 'ID', 'trim|required');

        $assetDisposalDetailAutoID = $this->input->post('assetDisposalDetailAutoID');

        /*if COnfirmed*/
        $Detail = $this->db->query("SELECT *
FROM
srp_erp_fa_asset_disposaldetail
INNER JOIN srp_erp_fa_asset_disposalmaster ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID
WHERE
srp_erp_fa_asset_disposaldetail.assetDisposalDetailAutoID = '{$assetDisposalDetailAutoID}' AND
srp_erp_fa_asset_disposalmaster.confirmedYN = '1'
")->row_array();

        if ($Detail) {
            $this->session->set_flashdata($msgtype = 'e', 'You cannot remove. This Disposal Already Confirmed.');
            exit(json_encode(FALSE));
        }


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->remove_asset_from_disposal());
        }

    }

    function assetDisposalConfirm()
    {
        $pId = trim($this->input->post('assetdisposalMasterAutoID') ?? '');
        echo json_encode($this->AssetManagemnt_model->assetDisposalConfirm());
    }

    function feed_disposal_approva_table()
    {
        /*
          * rejected = 1
          * not rejected = 0
         *
          * */
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuser = current_userID();
        $convertFormat = convert_date_format_sql();
        if ($approvedYN == 2) {
            $this->datatables->select('assetdisposalMasterAutoID,srp_erp_fa_asset_disposalmaster.companyCode,disposalDocumentCode,disposalDocumentDate,confirmedYN,0 AS approvedYN,0 AS documentApprovedID,rejectedLevel AS approvalLevelID,FYPeriodDateFrom,FYPeriodDateTo,DATE_FORMAT(disposalDocumentDate,\'' . $convertFormat . '\') AS disposalDocumentDate');
            $this->datatables->from('srp_erp_fa_asset_disposalmaster');
            $this->datatables->join('srp_erp_approvalreject', 'srp_erp_approvalreject.systemID = srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_fa_asset_disposalmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_fa_asset_disposalmaster.currentLevelNo');
            $this->datatables->where('srp_erp_approvalreject.documentID', 'ADSP');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'ADSP');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            /*$this->datatables->where('srp_erp_approvalreject.approvedYN', trim($this->input->post('approvedYN') ?? ''));*/
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_fa_asset_disposalmaster.companyID', $companyID);
            $this->datatables->group_by('srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('disposalDocumentCode', '<a onclick=\'fetch_approval("$2","$3","$4","1"); \'>$1</a>', 'disposalDocumentCode,assetdisposalMasterAutoID,documentApprovedID,approvalLevelID,approvedYN');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "ADSP", assetdisposalMasterAutoID)');
            $this->datatables->add_column('edit', '$1', 'dep_action_approval(assetdisposalMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,1)');
        } else {
            if($approvedYN == 0)
            {
                $this->datatables->select('assetdisposalMasterAutoID,srp_erp_fa_asset_disposalmaster.companyCode,disposalDocumentCode,disposalDocumentDate,confirmedYN,srp_erp_documentapproved.approvedYN AS approvedYN,documentApprovedID,approvalLevelID,FYPeriodDateFrom,FYPeriodDateTo,DATE_FORMAT(disposalDocumentDate,\'' . $convertFormat . '\') AS disposalDocumentDate');
                $this->datatables->from('srp_erp_fa_asset_disposalmaster');
                $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID');
                $this->datatables->where('srp_erp_documentapproved.documentID', 'ADSP');
                $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
                $this->datatables->where('srp_erp_fa_asset_disposalmaster.companyID', $this->common_data['company_data']['company_id']);
                $this->datatables->group_by('srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID');
                $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
                $this->datatables->add_column('disposalDocumentCode', '<a onclick=\'fetch_approval("$2","$3","$4","$5"); \'>$1</a>', 'disposalDocumentCode,assetdisposalMasterAutoID,documentApprovedID,approvalLevelID,approvedYN');
                $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "ADSP", assetdisposalMasterAutoID)');
                $this->datatables->add_column('edit', '$1', 'dep_action_approval(assetdisposalMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            }else
            {
                $this->datatables->select('assetdisposalMasterAutoID,srp_erp_fa_asset_disposalmaster.companyCode,disposalDocumentCode,disposalDocumentDate,confirmedYN,srp_erp_documentapproved.approvedYN AS approvedYN,documentApprovedID,approvalLevelID,FYPeriodDateFrom,FYPeriodDateTo,DATE_FORMAT(disposalDocumentDate,\'' . $convertFormat . '\') AS disposalDocumentDate');
                $this->datatables->from('srp_erp_fa_asset_disposalmaster');
                $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID');
                $this->datatables->where('srp_erp_documentapproved.documentID', 'ADSP');
                $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
                $this->datatables->where('srp_erp_fa_asset_disposalmaster.companyID', $this->common_data['company_data']['company_id']);
                $this->datatables->group_by('srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID');
                $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
                $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
                $this->datatables->add_column('disposalDocumentCode', '<a onclick=\'fetch_approval("$2","$3","$4","$5"); \'>$1</a>', 'disposalDocumentCode,assetdisposalMasterAutoID,documentApprovedID,approvalLevelID,approvedYN');
                $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "ADSP", assetdisposalMasterAutoID)');
                $this->datatables->add_column('edit', '$1', 'dep_action_approval(assetdisposalMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            }

        }
        echo $this->datatables->generate();
    }

    function save_disposal_approval()
    {
        $this->load->library('Approvals');
        $system_code = trim($this->input->post('assetdisposalMasterAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $company_id = current_companyID();
        $company_code = $this->common_data['company_data']['company_code'];
        $current_date = current_date();
        $current_pc = $this->common_data['current_pc'];
        $current_userID = $this->common_data['current_userID'];
        $current_employee = current_employee();
        $current_user_group = current_user_group();
        $current_user = $this->common_data['current_user'];

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'ADSP', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('assetdisposalMasterAutoID');
                $this->db->where('assetdisposalMasterAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_fa_asset_disposalmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'ADSP');
                        $maxlevel = $this->db->query("SELECT MAX(levelNo) as maxLevel FROM srp_erp_approvalusers where documentID = 'ADSP' AND companyID = '{$company_id}'")->row_array();

                        if ($maxlevel['maxLevel'] == $level_id) {
                            if ($status == 1) {
                                $this->AssetManagemnt_model->save_disposal_approval($system_code);
                            }
                        }
                        echo true;
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('assetdisposalMasterAutoID');
            $this->db->where('assetdisposalMasterAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_fa_asset_disposalmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'ADSP', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'ADSP');
                        if ($status == 1) {
                            $this->AssetManagemnt_model->save_disposal_approval($system_code);
                        }
                        echo true;
                    }
                }
            }
        }
        $this->db->select('srp_erp_fa_asset_disposalmaster.*,srp_erp_fa_asset_disposaldetail.*');
        $this->db->from('srp_erp_fa_asset_disposalmaster');
        $this->db->where('srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID', $system_code);
        $this->db->join('srp_erp_fa_asset_disposaldetail', 'srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID');
        $query = $this->db->get();
        $assetDisposalHeader = $query->result_array();
        $assetDisposalHeader = $assetDisposalHeader[0];

        if (!empty($assetDisposalHeader['interCompanyID']) && $assetDisposalHeader['interCompanyID'] != '0'){

            $this->db->select('*');
            $this->db->from('srp_erp_customermaster');
            $this->db->where('companyID',$assetDisposalHeader['companyID']);
            $this->db->where('interCompanyID',$assetDisposalHeader['interCompanyID']);
            $query=$this->db->get();
            $checkCustomerMaster=$query->row_array();
    
            $this->db->select('*');
            $this->db->from('srp_erp_suppliermaster');
            $this->db->where('companyID',$assetDisposalHeader['interCompanyID']);
            $this->db->where('interCompanyID',$assetDisposalHeader['companyID']);
            $query=$this->db->get();
            $checkSupplierMaster=$query->row_array();
    
            if (empty($checkCustomerMaster) || empty($checkSupplierMaster)) {
                echo json_encode(['status' => 'error', 'message' => 'Inter Company Customer not found']);
                return;
            }
    
            $cutomerHeader['customerID']=$checkCustomerMaster['customerAutoID'];
            $cutomerHeader['customerName']=$checkCustomerMaster['customerName'];
            $cutomerHeader['customerSystemCode']=$checkCustomerMaster['customerSystemCode'];
            $cutomerHeader['customerTelephone']=$checkCustomerMaster['customerTelephone'];
            $cutomerHeader['customerAddress']=$checkCustomerMaster['customerAddress1'];
            $cutomerHeader['customerFax']=$checkCustomerMaster['customerFax'];
            $cutomerHeader['customerEmail']=$checkCustomerMaster['customerEmail'];
            $cutomerHeader['customerReceivableAutoID']=$checkCustomerMaster['receivableAutoID'];
            $cutomerHeader['customerReceivableSystemGLCode']=$checkCustomerMaster['receivableSystemGLCode'];
            $cutomerHeader['customerReceivableGLAccount']=$checkCustomerMaster['receivableGLAccount'];
            $cutomerHeader['customerReceivableDescription']=$checkCustomerMaster['receivableDescription'];
            $cutomerHeader['customerReceivableType']=$checkCustomerMaster['receivableType'];
    
            $cutomerHeader['invoiceType']='DirectIncome';
            $cutomerHeader['documentID']='CINV';
            $cutomerHeader['invoiceDate']=$assetDisposalHeader['disposalDocumentDate'];
            $cutomerHeader['invoiceDueDate']=$assetDisposalHeader['disposalDocumentDate'];
            $cutomerHeader['customerInvoiceDate']=$assetDisposalHeader['disposalDocumentDate'];
            $cutomerHeader['acknowledgementDate']=$assetDisposalHeader['disposalDocumentDate'];
            $cutomerHeader['supplyDate']=$assetDisposalHeader['disposalDocumentDate'];
            $cutomerHeader['companyFinanceYearID']=$assetDisposalHeader['companyFinanceYearID'];
            $cutomerHeader['companyFinanceYear']=$assetDisposalHeader['companyFinanceYear'];
            $cutomerHeader['FYBegin']=$assetDisposalHeader['FYBegin'];
            $cutomerHeader['FYEnd']=$assetDisposalHeader['FYEnd'];
            $cutomerHeader['FYPeriodDateFrom']=$assetDisposalHeader['FYPeriodDateFrom'];
            $cutomerHeader['FYPeriodDateTo']=$assetDisposalHeader['FYPeriodDateTo'];
            $cutomerHeader['companyFinancePeriodID']=$assetDisposalHeader['companyFinancePeriodID'];
            $cutomerHeader['segmentID']=$assetDisposalHeader['segmentID'];
            $cutomerHeader['segmentCode']=$assetDisposalHeader['segmentCode'];
            $cutomerHeader['companyID']=$assetDisposalHeader['companyID'];
            $cutomerHeader['interCompanyID']=$assetDisposalHeader['interCompanyID'];
            $cutomerHeader['relatedDocumentID']=$assetDisposalHeader['documentID'];
            $cutomerHeader['relatedDocumentMasterID']=$assetDisposalHeader['assetdisposalMasterAutoID'];
            $cutomerHeader['confirmedByEmpID']=$assetDisposalHeader['confirmedByEmpID'];
            $cutomerHeader['confirmedByName']=$assetDisposalHeader['confirmedByName'];
            $cutomerHeader['confirmedDate']=$assetDisposalHeader['confirmedDate'];
            $cutomerHeader['confirmedYN']=$assetDisposalHeader['confirmedYN'];
            $cutomerHeader['currentLevelNo']=$assetDisposalHeader['currentLevelNo'];
    
    
            $cutomerHeader['createdPCID'] = $current_pc;
            $cutomerHeader['createdUserID'] = $current_userID;
            $cutomerHeader['createdUserName'] = $current_user;
            $cutomerHeader['createdDateTime'] = $current_date;
            $cutomerHeader['timestamp'] = $current_date;
            $cutomerHeader['companyCode']=$company_code;
    
            $this->db->select('*');
            $this->db->from('srp_erp_company');
            $this->db->where('company_id',$company_id);
            $query=$this->db->get();
            $companydetail=$query->row_array();
    
            $cutomerHeader['companyLocalCurrencyID']=$companydetail['company_default_currencyID'];
            $cutomerHeader['companyLocalCurrency']=$companydetail['company_default_currency'];
            $cutomerHeader['companyLocalCurrencyDecimalPlaces']=$companydetail['company_default_decimal'];
            $cutomerHeader['companyReportingCurrencyID']=$companydetail['company_reporting_currencyID'];
            $cutomerHeader['companyReportingCurrency']=$companydetail['company_reporting_currency'];
            $cutomerHeader['companyReportingCurrencyDecimalPlaces']=$companydetail['company_reporting_decimal'];
            $cutomerHeader['invoiceCode'] = $this->sequence->sequence_generator('CINV');
    
            $this->db->insert('srp_erp_customerinvoicemaster', $cutomerHeader);
            $last_id=$this->db->insert_id();
    
            $customerInvoiceDetail['invoiceAutoID']=$last_id;
            $customerInvoiceDetail['transactionAmount']=$assetDisposalHeader['netBookValueTransactionAmount'];
            $customerInvoiceDetail['companyID']=$assetDisposalHeader['companyID'];
            $customerInvoiceDetail['companyCode']=$company_code;
            $customerInvoiceDetail['segmentID']=$assetDisposalHeader['segmentID'];
            $customerInvoiceDetail['segmentCode']=$assetDisposalHeader['segmentCode'];
            $customerInvoiceDetail['description']=$assetDisposalHeader['assetDescription'];
            $customerInvoiceDetail['type']='GL';
    
    
            $customerInvoiceDetail['createdPCID'] = $current_pc;
            $customerInvoiceDetail['createdUserID'] = $current_userID;
            $customerInvoiceDetail['createdUserName'] = $current_user;
            $customerInvoiceDetail['createdDateTime'] = $current_date;
            $customerInvoiceDetail['timestamp'] = $current_date;
    
            $this->db->select('coa.*');
            $this->db->from('srp_erp_companycontrolaccounts cca');
            $this->db->join('srp_erp_chartofaccounts coa', 'cca.GLAutoID = coa.GLAutoID', 'inner');
            $this->db->where('cca.controlAccountType', 'ADSP');
            $this->db->where('cca.companyID', $assetDisposalHeader['companyID']);
            $query = $this->db->get();
            $companyGL = $query->row_array();
    
    
            $customerInvoiceDetail['revenueGLAutoID']=$companyGL['GLAutoID'];
            $customerInvoiceDetail['revenueGLCode']=$companyGL['GLSecondaryCode'];
            $customerInvoiceDetail['revenueSystemGLCode']=$companyGL['systemAccountCode'];
            $customerInvoiceDetail['revenueGLDescription']=$companyGL['GLDescription'];
            $customerInvoiceDetail['revenueGLType']=$companyGL['subCategory'];
    
            $this->db->insert('srp_erp_customerinvoicedetails', $customerInvoiceDetail);
            $last_customer_invoice=$this->db->insert_id();
    
            // $response = ['status' => 'success', 'message' => 'Customer Invoice created'];
            
            // Create approval
            $this->approvals->auto_approve($last_id, 'srp_erp_customerinvoicemaster','invoiceAutoID', 'CINV',$cutomerHeader['invoiceCode'],$assetDisposalHeader['disposalDocumentDate']);
    
            // $response['approvalMessage'] = 'Customer Invoice Approved';
    
            $this->db->select('*');
            $this->db->from('srp_erp_fa_asset_master');
            $this->db->where('faID',$assetDisposalHeader['faID']);
            $query=$this->db->get();
            $assetHeader=$query->row_array();
    
            $this->db->select('*');
            $this->db->from('srp_erp_company');
            $this->db->where('company_id',$assetDisposalHeader['interCompanyID']);
            $query=$this->db->get();
            $companycode=$query->row_array();
    
            $newAsset['companyID']=$assetDisposalHeader['interCompanyID'];
            $newAsset['companyCode']=$companycode['company_code'];
            $newAsset['segmentID']=$assetHeader['segmentID'];
            $newAsset['segmentCode']=$assetHeader['segmentCode'];
            $newAsset['documentID']=$assetHeader['documentID'];
            $newAsset['faCode']=$this->sequence->sequence_generator('FA');
            $newAsset['assetDescription']=$assetHeader['assetDescription'];
            $newAsset['dateAQ']=$assetHeader['dateAQ'];
            $newAsset['dateDEP']=$assetHeader['dateDEP'];
            $newAsset['depMonth']=$assetHeader['depMonth'];
            $newAsset['DEPpercentage']=$assetHeader['DEPpercentage'];
            $newAsset['transactionCurrencyID']=$assetHeader['transactionCurrencyID'];
            $newAsset['transactionCurrency']=$assetHeader['transactionCurrency'];
            $newAsset['transactionCurrencyExchangeRate']=$assetHeader['transactionCurrencyExchangeRate'];
            $newAsset['transactionAmount']=$assetHeader['transactionAmount'];
            $newAsset['salvageAmount']=$assetHeader['salvageAmount'];
            $newAsset['transactionCurrencyDecimalPlaces']=$assetHeader['transactionCurrencyDecimalPlaces'];
            $newAsset['companyLocalCurrencyID']=$assetHeader['companyLocalCurrencyID'];
            $newAsset['companyLocalCurrency']=$assetHeader['companyLocalCurrency'];
            $newAsset['companyLocalAmount']=$assetHeader['companyLocalAmount'];
            $newAsset['companyLocalExchangeRate']=$assetHeader['companyLocalExchangeRate']; 
            $newAsset['assetType']=$assetHeader['assetType']; 
            $newAsset['companyLocalCurrencyDecimalPlaces']=$assetHeader['companyLocalCurrencyDecimalPlaces'];
            $newAsset['companyReportingCurrencyID']=$assetHeader['companyReportingCurrencyID'];
            $newAsset['companyReportingCurrency']=$assetHeader['companyReportingCurrency'];
            $newAsset['companyReportingExchangeRate']=$assetHeader['companyReportingExchangeRate'];
            $newAsset['companyReportingAmount']=$assetHeader['companyReportingAmount'];
            $newAsset['companyReportingDecimalPlaces']=$assetHeader['companyReportingDecimalPlaces'];
            $newAsset['manufacture']=$assetHeader['manufacture'];
            $newAsset['currentLocation']=$assetHeader['currentLocation'];
        
    
            $newAsset['createdPCID'] = $current_pc;
            $newAsset['createdUserID'] = $current_userID;
            $newAsset['createdUserName'] = $current_user;
            $newAsset['createdDateTime'] = $current_date;
            $newAsset['timestamp'] = $current_date;
    
            $this->db->insert('srp_erp_fa_asset_master', $newAsset);
            echo true;
        }
        return;
    }

    function load_disposal_conformation()
    {
        $assetdisposalMasterAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('assetdisposalMasterAutoID') ?? '');
        $data['extra'] = $assetdisposalMasterAutoID;
        $html = $this->load->view('system/asset_management/asset_disposal_approve', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        }
    }

    function deleteDisposal()
    {
        $this->form_validation->set_rules('assetdisposalMasterAutoID', 'ID', 'trim|required');


        $assetdisposalMasterAutoID = $this->input->post('assetdisposalMasterAutoID');

        $disposal = $this->db->query("SELECT * FROM `srp_erp_fa_asset_disposalmaster` WHERE `confirmedYN` = '1' AND `assetdisposalMasterAutoID` = '{$assetdisposalMasterAutoID}'")->row_array();

        if ($disposal) {
            $this->session->set_flashdata($msgtype = 'e', '<p>Disposal is Already Confirmed.</p>');
            exit(json_encode(FALSE));
        }


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->deleteDisposal());
        }
    }

    function referback_grv()
    {
        $depMasterAutoID = $this->input->post('depMasterAutoID');

        $this->db->select('approvedYN,depCode');
        $this->db->where('depMasterAutoID', trim($depMasterAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_fa_depmaster');
        $asset_dep = $this->db->get()->row_array();
        if (!empty($asset_dep)) {
            echo json_encode(array('e', 'The document already approved - ' . $asset_dep['depCode']));
        }else
        {
            $this->load->library('Approvals');
            $status = $this->approvals->approve_delete($depMasterAutoID, 'FAD');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function referback_disposal()
    {
        $assetdisposalMasterAutoID = $this->input->post('assetdisposalMasterAutoID');

        $this->db->select('approvedYN,disposalDocumentCode');
        $this->db->where('assetdisposalMasterAutoID', trim($assetdisposalMasterAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_fa_asset_disposalmaster');
        $asset_disposal = $this->db->get()->row_array();
        if (!empty($asset_disposal)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_stock_adjustment['disposalDocumentCode']));
        }else
        {
            $this->load->library('Approvals');

            $status = $this->approvals->approve_delete($assetdisposalMasterAutoID, 'ADSP');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }



    }

    function load_asset_dipriciation_view()
    {
        $data['extra'] = array("depMasterAutoID" => trim($this->input->post('depMasterAutoID') ?? ''));
        $data['total'] = $this->AssetManagemnt_model->fetch_dep_total($this->input->post('depMasterAutoID'));
        $html = $this->load->view('system/asset_management/asset_depriciation_view_print.php', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        }

    }

    function load_asset_dipriciation_adhoc_view()
    {
        $data['extra'] = array("depMasterAutoID" => trim($this->input->post('depMasterAutoID') ?? ''));
        $data['total'] = $this->AssetManagemnt_model->fetch_dep_total(trim($this->input->post('depMasterAutoID') ?? ''));
        $html = $this->load->view('system/asset_management/asset_depriciation_view_adhoc_print.php', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        }

    }

    function load_asset_disposal_view()
    {
        $data['assetdisposalMasterAutoID'] = $this->input->post('assetdisposalMasterAutoID');
        $data['datas'] = $this->db->query("SELECT * FROM `srp_erp_fa_asset_disposalmaster` WHERE `assetdisposalMasterAutoID` = '{$data['assetdisposalMasterAutoID']}'")->row_array();
        $html = $this->load->view('system/asset_management/asset_disposal_view_print.php', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        }

    }

    function assetMonthlyDepreciationSummary()
    {
        $array['financeyear'] = $this->input->post('financeyear');
        return $this->load->view('system/asset_management/asset_monthly_depreciation_generator', $array);
    }

    function assetDepGenerate_CurrentMonth()
    {
        echo json_encode($this->AssetManagemnt_model->assetDepGenerate_CurrentMonth());
    }

    function location_master()
    {
        $this->datatables->select('locationID,locationName', false)
            ->from('srp_erp_location')
            ->add_column('edit', '$1', 'action_asset_location(locationID,locationName)')
            ->where('companyID', current_companyID())
            ->where('isCostLocation', 0);
        echo $this->datatables->generate();
    }

    public function saveAssetLocation()
    {
        $this->form_validation->set_rules('location[]', 'Location', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->AssetManagemnt_model->saveAssetLocation());
        }
    }

    public function deleteAssetLocation()
    {
        echo json_encode($this->AssetManagemnt_model->deleteAssetLocation());
    }

    function updateAssetLocation()
    {
        $this->form_validation->set_rules('assetLocationDesc', 'Description', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->AssetManagemnt_model->updateAssetLocation());
        }
    }


    function custodian_master()
    {
        $this->datatables->select('id,custodianName', false)
            ->from('srp_erp_fa_custodian')
            ->add_column('edit', '$1', 'action_asset_custodian(id,custodianName)')
            ->where('companyID', current_companyID());
        echo $this->datatables->generate();
    }
    public function save_Asset_Custodian()
    { 
        $this->form_validation->set_rules('CustodianTypes[]', 'Custodian', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->AssetManagemnt_model->saveAssetCustodian());
        }
    }
    public function deleteCustodian()
    {
        echo json_encode($this->AssetManagemnt_model->deleteAssetCustodian());
    }
    function updateAssetCustodian()
    {
        $this->form_validation->set_rules('assetCustodianTypes', 'CustodianTypes', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->AssetManagemnt_model->updateAssetCustodian());
        }
    }
    function generate_asset_pdf()
    {
        $html = $this->load->view('system/asset_management/get_asset_register_pdf', $_POST, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4-L');
    }

    function generate_asset_register_summary_pdf()
    {
        $html = $this->load->view('system/asset_management/generate_asset_register_summary_pdf', $_POST, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4-L');
    }


    function asset_monthly_depreciation_generator_pdf()
    {
        $html = $this->load->view('system/asset_management/asset_monthly_depreciation_generator_pdf', $_POST, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4-L');
    }

    function edit_attachment()
    {
        echo json_encode($this->AssetManagemnt_model->edit_attachment());
    }


    function updateAttachment()
    {
        $issueDate = $this->input->post('dateissuededit');
        $expiryDate = $this->input->post('dateexpirededit');

        if ($issueDate < $expiryDate) {
            $this->form_validation->set_rules('document_descriptionedit', 'Description', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $error_message = validation_errors();
                echo json_encode(array('error' => 1, 'message' => $error_message));
            } else {

                $attachmentID = $this->input->post('attachmentIDhn');



                $data['attachmentDescription'] = $this->input->post('document_descriptionedit');
                $data['docExpiryDate'] = $this->input->post('dateexpirededit');
                $data['dateofIssued'] = $this->input->post('dateissuededit');

                if ($attachmentID) {
                    /* update */
                    $result = $this->AssetManagemnt_model->updateAttachment($attachmentID, $data);
                    if ($result) {
                        
                        echo json_encode(array('s', 'updated'));
                    } else {
                        echo json_encode(array('e', 'Error while updating, Please contact your system support team'));
                    }
                }

            }
        } else {
            echo json_encode(array('e', 'Date Issue Should be grater than date of expiry'));
        }

    }
    function location_master_code()
    {
        $this->datatables->select('locationID,locationName,IFNULL(locationCode,\'-\') as locationCode,concat(\'Add Employee (\',locationCode,\' - \',locationName,\')\') as loccodename,locationType', false)
            ->from('srp_erp_location')
            ->add_column('locationtype_status', '$1', 'locationtype_status(locationType)')

            ->add_column('edit', '$1', 'action_asset_location_code(locationID,locationName,locationCode,loccodename,locationType)')
            ->where('companyID', current_companyID())
            ->where('isCostLocation', 1);
        echo $this->datatables->generate();
    }
    public function saveAssetLocationcode()
    {
        $this->form_validation->set_rules('location[]', 'Location', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->AssetManagemnt_model->saveAssetLocationcode());
        }
    }
    function updateAssetLocationcode()
    {
        $this->form_validation->set_rules('assetLocationDesc', 'Description', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->AssetManagemnt_model->updateAssetLocationcode());
        }
    }
    function fetch_employees()
    {
        $this->datatables->select('empdetails.Ename2 as employeename,empdetails.EEmail as email, empdetails.EIdNo as primaryKey', false)
            ->from('srp_employeesdetails as empdetails')
            ->where('Erp_companyID', current_companyID());
       $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_locationemployees WHERE srp_erp_locationemployees.empID = empdetails.EIdNo AND Erp_companyID =' . current_companyID() . ' AND locationID =  ' . $this->input->post('locationid') . ')');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'primaryKey');
        echo $this->datatables->generate();
    }

    function fetch_savedemplocation()
    {
        $locationid = $this->input->post('locationid');

        $this->datatables->select('srp_erp_locationemployees.autoID as employeelocationid,srp_employeesdetails.EIdNo as primaryKey,srp_employeesdetails.Ename2 as employeename,srp_employeesdetails.EEmail as email,srp_erp_locationemployees.locationID as locationIDs,srp_erp_locationemployees.empID as empIDs');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_locationemployees.empID', 'left');
        $this->datatables->from('srp_erp_locationemployees');
        $this->datatables->where('srp_erp_locationemployees.locationID', $locationid);
        $this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="delete_emplocation_code($1,$2,$3)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;','employeelocationid,locationIDs,empIDs');
        echo $this->datatables->generate();

    }

    function link_employee_asset_code()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Employee', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->link_employees());
        }

    }
    function delete_employee()
    {
        echo json_encode($this->AssetManagemnt_model->delete_employees());
    }
    public function deleteAssetLocationcode()
    {
        echo json_encode($this->AssetManagemnt_model->deleteAssetLocationcode());
    }

    function save_request_header()
    {
        $this->form_validation->set_rules('location', 'Location', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Request Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('segmentID', 'Segment', 'trim|required');
        $this->form_validation->set_rules('requestedByEmpID', 'requestedByEmpID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->save_request_header());
        }
    }

    // function load_request_header()
    // {
    //     echo json_encode($this->AssetManagemnt_model->load_request_header());
    // }
    
    function save_request_details()
    {
      
        $itemDescriptions = $this->input->post('itemDescription');
        $uom= $this->input->post('UOMID');
        $contractIDs = $this->input->post('contractID');
        $requestedQtys = $this->input->post('requestedQTY');
        $comments = $this->input->post('comments');
   
        $this->form_validation->set_rules('itemdescription[]', 'Item Description', 'required');
        $this->form_validation->set_rules('project[]', 'Project', 'required');
        $this->form_validation->set_rules('UOMID[]', 'UOMID');
        $this->form_validation->set_rules('requestedQty[]', 'Requested Quantity', 'required');
        $this->form_validation->set_rules('comments[]', 'Comments', 'required');

        if ($this->form_validation->run() == FALSE) {
            // If validation fails, return validation errors
            echo json_encode(array('e', validation_errors()));
        } else {
            
            // Save data to the database
            $this->load->model('AssetManagemnt_model'); // Adjust the model name if needed
            $result = $this->AssetManagemnt_model->save_request_details();
    
            // Return result
            echo json_encode($result);
        }
    }
    
   

    function load_request() {
        
        $this->datatables->select('arn.id as id, arn.documentID, arn.documentDate, arn.requestedByEmpID, emp.Ename2 as requestedByName, arn.confirmedYN as confirmedYN, arn.approvedYN as approvedYN', false);

        $this->datatables->from('srp_erp_asset_request_note as arn');
        $this->datatables->join('srp_employeesdetails as emp', 'emp.EIdNo = arn.requestedByEmpID', 'left');
         $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"ARN",id)');
         $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"ARN",id)');
        $this->datatables->add_column('edit', '$1', 'action_asset_request(id,confirmedYN,approvedYN,$isDeleted)'); 
       
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }
    
 
    function delete_asset_request()
    {
        echo json_encode($this->AssetManagemnt_model->delete_asset_request());
    }
   
  
    
    function fetch_arn_table() {
        $masterID = $this->input->post('masterID');
    
        if ($masterID) {
            // Fetch details based on the provided masterID
            $detailQuery = $this->db
                ->select('detailsID, itemDescription, contractID,srp_erp_contractmaster.contractCode as contractCode,requestedQTY, comments')
                ->where('masterID', $masterID)
                ->join('srp_erp_contractmaster','srp_erp_asset_request_details.contractID = srp_erp_contractmaster.contractAutoID')
                ->get('srp_erp_asset_request_details');
    
            $data['detail'] = $detailQuery->result_array();
        } else {
            $data['detail'] = array(); // Handle case where no masterID is provided
        }
    
        // Return JSON response
        echo json_encode($data);
    }

    function delete_asset_request_detail()
    {
        echo json_encode($this->AssetManagemnt_model->delete_asset_request_detail());
    }

    function fetch_asset_request_detail()
    {
        echo json_encode($this->AssetManagemnt_model->fetch_asset_request_detail());
    }

    function update_asset_request()
    {
        $this->form_validation->set_rules('itemdescription[]', 'Item Description', 'required');
        $this->form_validation->set_rules('project[]', 'Project', 'required');
        // $this->form_validation->set_rules('UOMID[]', 'UOMID');
        $this->form_validation->set_rules('requestedQty[]', 'Requested Quantity', 'required');
        $this->form_validation->set_rules('comments[]', 'Comments', 'required');
  if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->AssetManagemnt_model->update_asset_request());
        }
    }

    function load_asset_request_confirmation(){
        $masterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('masterID') ?? '');
     
        $data['extra'] = $this->AssetManagemnt_model->fetch_arn_template_data($masterID);
       
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/asset_management/asset_request_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function asset_request_confirmation()
    {
        $this->db->select('requestedByEmpID');
        $this->db->where('masterID', trim($this->input->post('masterID') ?? ''));
        $this->db->from('srp_erp_asset_request_note');
        $empid = $this->db->get()->row_array();
        if($empid){
            $this->db->select('managerID');
            $this->db->where('empID', trim($empid['requestedByEmpID'] ?? ''));
            $this->db->where('active', 1);
            $this->db->from('srp_erp_employeemanagers');
            $managerid = $this->db->get()->row_array();
            if(!empty($managerid)){
                echo json_encode($this->Expense_claim_modal->expense_claim_confirmation());
            }else{
                echo json_encode(array('w','Reporting manager not available for this employee'));
            }
        }
    }
}