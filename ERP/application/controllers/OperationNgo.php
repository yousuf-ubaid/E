<?php

class OperationNgo extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('OperationNgo_model');
        $this->load->helper('operation_ngo_helper');
        $this->load->library('s3');
    }

    function load_donorManagement_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');

        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND ((name Like '%" . $text . "%') OR (srp_erp_ngo_donors.email Like '%" . $text . "%') OR (CONCAT(phoneCountryCodePrimary,phoneAreaCodePrimary,phonePrimary) Like '%" . $text . "%'))";
        }
        $search_sorting = '';
        if (isset($sorting) && $sorting != '#') {
            $search_sorting = " AND name Like '" . $sorting . "%'";
        }

        $where = "srp_erp_ngo_donors.companyID = " . $companyID . $search_string . $search_sorting;

        $data['header'] = $this->db->query("SELECT contactID,name,contactImage,email,CountryDes, CONCAT(phoneCountryCodePrimary,' - ',phoneAreaCodePrimary,phonePrimary) AS MasterPrimaryNumber FROM srp_erp_ngo_donors LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = srp_erp_ngo_donors.countryID WHERE srp_erp_ngo_donors.Com_MasterID IS NULL AND $where GROUP BY srp_erp_ngo_donors.contactID ORDER BY contactID DESC ")->result_array();

        $this->load->view('system/operationNgo/ajax/load_ngo_contact_master', $data);
    }

    function save_donor_header()
    {
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        //$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('phonePrimary', 'Phone (Primary)', 'trim|required');
        $this->form_validation->set_rules('countryCodePrimary', 'Country Code (Primary) is required', 'trim|required');
        $this->form_validation->set_rules('countryID', 'Country', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_donor_header());
        }
    }


    function load_donor_header()
    {
        echo json_encode($this->OperationNgo_model->load_donor_header());
    }

    function delete_donor_master()
    {
        echo json_encode($this->OperationNgo_model->delete_donor_master());
    }

    function load_donorManagement_editView()
    {
        $convertFormat = convert_date_format_sql();
        $contactID = trim($this->input->post('contactID') ?? '');

        $data['header'] = $this->db->query("SELECT *,CountryDes,DATE_FORMAT(srp_erp_ngo_donors.createdDateTime,'" . $convertFormat . "') AS createdDate,DATE_FORMAT(srp_erp_ngo_donors.modifiedDateTime,'" . $convertFormat . "') AS modifydate,srp_erp_ngo_donors.createdUserName as contactCreadtedUser,srp_erp_ngo_donors.email as contactEmail,srp_erp_ngo_donors.fax as contactFax,srp_erp_ngo_donors.phonePrimary as contactPhonePrimary,srp_erp_ngo_donors.phoneSecondary as contactPhoneSecondary,srp_erp_statemaster.Description as province,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName FROM srp_erp_ngo_donors LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = srp_erp_ngo_donors.countryID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_donors.state LEFT JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_ngo_donors.currencyID WHERE srp_erp_ngo_donors.Com_MasterID IS NULL AND contactID = " . $contactID . "")->row_array();
        //echo $this->db->last_query();

        $this->load->view('system/operationNgo/ajax/load_ngo_contact_edit_view', $data);
    }

    function load_donor_all_notes()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $contactID = trim($this->input->post('contactID') ?? '');

        $where = "companyID = " . $companyID . " AND documentID = 1 AND documentAutoID = " . $contactID . "";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_notes');
        $this->db->where($where);
        $this->db->order_by('notesID', 'desc');
        $data['notes'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load_ngo_contact_notes', $data);
    }

    function add_donor_notes()
    {
        $this->form_validation->set_rules('contactID', 'Contact ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->add_ngo_donor_notes());
        }
    }

    function donor_image_upload()
    {
        $this->form_validation->set_rules('contactID', 'Donor ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->donor_image_upload());
        }
    }

    function ngo_attachement_upload()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        $this->form_validation->set_rules('documentAutoID', 'Document Auto ID', 'trim|required');

        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            /*   $this->db->select('companyID');
               $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
               $num = $this->db->get('srp_erp_ngo_attachments')->result_array();
               $file_name = $this->input->post('document_name') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
               $config['upload_path'] = realpath(APPPATH . '../attachments/NGO');
               $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
               $config['max_size'] = '5120'; // 5 MB
               $config['file_name'] = $file_name;

               $this->load->library('upload', $config);
               $this->upload->initialize($config);
               if (!$this->upload->do_upload("document_file")) {
                   echo json_encode(array('status' => 0, 'type' => 'w',
                       'message' => 'Upload failed ' . $this->upload->display_errors()));
               }*/

            $info = new SplFileInfo($_FILES["document_file"]["name"]);
            $fileName = $this->input->post('document_name') .$this->common_data['company_data']['company_code'].'_'. $this->input->post('documentID') . '_' . time() . '.' . $info->getExtension();
            $currentDatetime = format_date_mysql_datetime();
            $file = $_FILES['document_file'];
            if($file['error'] == 1){
                echo json_encode(array('e', 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB)'));
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if(!in_array($ext, $allowed_types)){
                echo json_encode(array('e',"The file type you are attempting to upload is not allowed. ( .{$ext} )"));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);
            if($size > 5){
                echo json_encode(array('e',"The file you are attempting to upload is larger than the permitted size. (maximum 5MB)"));
            }
            $path = "attachments/ngo/$fileName";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                echo json_encode(array('e',"Error in document upload location configuration"));
            } else {
              //  $upload_data = $this->upload->data();
                //$fileName                       = $file_name.'_'.$upload_data["file_ext"];
                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                $data['myFileName'] = $fileName;
             /*   $data['fileType'] = trim($upload_data["file_ext"]);
                $data['fileSize'] = trim($upload_data["file_size"]);*/
                $data['fileType'] = trim($ext);
                $data['fileSize'] = trim($file["size"]);
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_ngo_attachments', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $fileName . ' uploaded.'));
                }
            }
        }
    }


    function delete_donor_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');

        $this->s3->delete('attachments/ngo/'.$myFileName);
       /* $url = base_url("attachments/NGO");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {*/
       $this->db->delete('srp_erp_ngo_attachments', array('attachmentID' => trim($attachmentID)));
       echo json_encode(TRUE);
       // }
    }

    function load_donor_all_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $contactID = trim($this->input->post('contactID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 1  AND documentAutoID = " . $contactID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_attachments');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load_ngo_contact_attachments', $data);
    }

    function save_donor_project()
    {
        $this->form_validation->set_rules('projectName', 'Project Name', 'trim|required');
        $this->form_validation->set_rules('segmentID', 'Segment', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('revenueGLAutoID', 'Revenue GL', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_donor_project());
        }
    }

    function save_ngo_project_subcategory()
    {
        $this->form_validation->set_rules('projectName', 'Project Name', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('masterID', 'Master ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_ngo_project_subcategory());
        }
    }

    function load_donorProjectView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');

        $data['master'] = $this->db->query("SELECT srp_erp_ngo_projects.*,CONCAT(segmentCode,' | ',srp_erp_segment.description) as segment from srp_erp_ngo_projects LEFT JOIN srp_erp_segment on srp_erp_segment.segmentID=srp_erp_ngo_projects.segmentID  WHERE srp_erp_ngo_projects.companyID={$companyID} ORDER BY ngoProjectID DESC ")->result_array();


        $this->load->view('system/operationNgo/ajax/load_ngo_project_master', $data);
    }

    function delete_ngo_project()
    {
        echo json_encode($this->OperationNgo_model->delete_ngo_project());
    }

    function load_donor_project_data()
    {
        echo json_encode($this->OperationNgo_model->load_donor_project_data());
    }


    function save_commitments()
    {
        $transactionCurrencyID = $this->input->post('transactionCurrencyID');
        $donorsID = $this->input->post('donorsID');
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required');
        if (isset($donorsID)) {
            $this->form_validation->set_rules('donorsID', 'Donor', 'trim|required');
        }

        if ($this->input->post('transactionCurrencyID') == '' && isset($transactionCurrencyID)) {
            $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $date_format_policy = date_format_policy();
            $documentDate = $this->input->post('documentDate');
            $commitmentExpiryDate = $this->input->post('commitmentExpiryDate');
            $docDate = input_format_date($documentDate, $date_format_policy);
            $expire = input_format_date($commitmentExpiryDate, $date_format_policy);
            if ($expire < $docDate) {
                echo json_encode(array('e', 'Unable to proceed. Expiry date is greater than document date'));
                exit;
            }
            echo json_encode($this->OperationNgo_model->save_commitments());
        }
    }


    function save_donor_item_detail()
    {
        $searches = $this->input->post('search');
        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            /*  $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'WareHouse', 'trim|required');*/
            $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            $commitmentAutoId = $this->input->post('commitmentAutoId');
            $master = $this->db->query("select documentDate from srp_erp_ngo_commitmentmasters WHERE commitmentAutoId=$commitmentAutoId ")->row_array();
            $documentDate = $master['documentDate'];
            $expiryDate = $this->input->post('expiryDate');
            $date_format_policy = date_format_policy();
            foreach ($expiryDate as $key => $item) {
                $commitmentExpiryDate = $expiryDate[$key];
                $expire = input_format_date($commitmentExpiryDate, $date_format_policy);
                if ($expire < $documentDate) {
                    echo json_encode(array('e', 'Unable to proceed. Document date is greater than expiry date'));
                    exit;
                }
            }
            echo json_encode($this->OperationNgo_model->save_donor_item_detail());
        }
    }

    function load_donorCommitmentsView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('q') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $convertFormat = convert_date_format_sql();
        $filter = "";
        if ($text != '') {
            $filter = " AND (documentSystemCode LIKE '%$text%' OR name LIKE '%$text%' )";
        }

        $data['master'] = $this->db->query("SELECT srp_erp_ngo_commitmentmasters.createdUserID,documentCode,confirmedYN,srp_erp_ngo_commitmentmasters.commitmentAutoId,documentSystemCode,DATE_FORMAT(documentDate,'{$convertFormat}') AS documentDate, DATE_FORMAT(srp_erp_ngo_commitmentmasters.commitmentExpiryDate,'{$convertFormat}') AS commitmentExpiryDate, referenceNo, transactionCurrency, donorsID,IFNULL(transactionAmount, 0) as transactionAmount, transactionCurrencyDecimalPlaces, name FROM srp_erp_ngo_commitmentmasters LEFT JOIN srp_erp_ngo_donors on donorsID=contactID LEFT JOIN ( SELECT sum(transactionAmount) AS transactionAmount, commitmentAutoId FROM srp_erp_ngo_commitmentdetails GROUP BY commitmentAutoId ) srp_erp_ngo_commitmentdetails ON srp_erp_ngo_commitmentmasters.commitmentAutoId = srp_erp_ngo_commitmentdetails.commitmentAutoId WHERE srp_erp_ngo_commitmentmasters.companyID={$companyID} AND isDeleted !=1 $filter ORDER BY commitmentAutoId DESC")->result_array();


        $this->load->view('system/operationNgo/ajax/load_ngo_commitments_master.php', $data);
    }

    function load_commitmentHeader()
    {
        echo json_encode($this->OperationNgo_model->load_commitmentHeader());
    }

    function load_commitment_items_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $commitmentAutoId = trim($this->input->post('commitmentAutoId') ?? '');
        $convertFormat = convert_date_format_sql();
        $this->db->select("commitmentDetailAutoID ,commitmentAutoId ,projectID ,type ,itemAutoID ,itemSystemCode ,itemDescription ,itemCategory ,DATE_FORMAT(commitmentExpiryDate,'{$convertFormat}') AS commitmentExpiryDate ,unittransactionAmount ,transactionAmount,itemQty,defaultUOM,srp_erp_ngo_commitmentdetails.description,projectName ");
        $this->db->from('srp_erp_ngo_commitmentdetails');
        $this->db->join('srp_erp_ngo_projects', 'srp_erp_ngo_projects.ngoProjectID=srp_erp_ngo_commitmentdetails.projectID', 'left');
        $this->db->where('type', 2);
        $this->db->where('srp_erp_ngo_commitmentdetails.companyID', $companyID);
        $this->db->where('srp_erp_ngo_commitmentdetails.commitmentAutoId', $commitmentAutoId);
        $this->db->order_by('commitmentDetailAutoID', 'desc');
        $data['header'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load-commitment-item-view', $data);
    }


    function save_donor_cash_detail()
    {
        $projectID = $this->input->post('projectID');
        foreach ($projectID as $key => $project) {

            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');

            $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');

        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            $commitmentAutoId = $this->input->post('commitmentAutoId');
            $master = $this->db->query("select documentDate from srp_erp_ngo_commitmentmasters WHERE commitmentAutoId=$commitmentAutoId ")->row_array();
            $documentDate = $master['documentDate'];
            $expiryDate = $this->input->post('expiryDate');
            $date_format_policy = date_format_policy();
            foreach ($expiryDate as $index => $item) {
                $commitmentExpiryDate = $expiryDate[$index];
                $expire = input_format_date($commitmentExpiryDate, $date_format_policy);
                if ($expire < $documentDate) {
                    echo json_encode(array('e', 'Unable to proceed. Document date is greater than expiry date'));
                    exit;
                }
            }
            echo json_encode($this->OperationNgo_model->save_donor_cash_detail());
        }
    }

    function load_commitment_cash_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $commitmentAutoId = trim($this->input->post('commitmentAutoId') ?? '');
        $convertFormat = convert_date_format_sql();
        $this->db->select("commitmentDetailAutoID ,commitmentAutoId   , projectName,DATE_FORMAT(commitmentExpiryDate,'{$convertFormat}') AS commitmentExpiryDate  ,transactionAmount ,srp_erp_ngo_commitmentdetails.description ");
        $this->db->from('srp_erp_ngo_commitmentdetails');
        $this->db->join('srp_erp_ngo_projects', 'srp_erp_ngo_projects.ngoProjectID=srp_erp_ngo_commitmentdetails.projectID', 'left');

        $this->db->where('type', 1);
        $this->db->where('srp_erp_ngo_commitmentdetails.companyID', $companyID);
        $this->db->where('srp_erp_ngo_commitmentdetails.commitmentAutoId', $commitmentAutoId);

        $this->db->order_by('commitmentDetailAutoID', 'desc');
        $data['header'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load-cash-item-view', $data);
    }


    function delete_commitmentDetail()
    {
        $commitmentDetailAutoID = $this->input->post('commitmentDetailAutoID');
        $this->db->delete('srp_erp_ngo_commitmentdetails', array('commitmentDetailAutoID' => $commitmentDetailAutoID));
        echo json_encode(array('s', 'Successfully Deleted'));
    }

    function fetch_item_detail()
    {
        $commitmentDetailAutoID = $this->input->post('commitmentDetailAutoID');
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query("select commitmentDetailAutoID,commitmentAutoId ,projectID ,type ,itemAutoID ,itemSystemCode ,itemDescription ,itemCategory ,expenseGLAutoID ,expenseGLCode ,expenseSystemGLCode ,expenseGLDescription ,expenseGLType ,revenueGLAutoID ,revenueGLCode ,revenueSystemGLCode ,revenueGLDescription ,revenueGLType ,assetGLAutoID ,assetGLCode ,assetSystemGLCode ,assetGLDescription ,assetGLType ,wareHouseAutoID ,wareHouseCode ,wareHouseLocation ,wareHouseDescription ,defaultUOMID ,defaultUOM ,unitOfMeasureID ,unitOfMeasure ,conversionRateUOM ,itemQty ,description ,GLAutoID ,SystemGLCode ,GLCode ,GLDescription ,GLType ,  DATE_FORMAT(commitmentExpiryDate,'{$convertFormat}') AS commitmentExpiryDate ,unittransactionAmount ,transactionAmount ,companyLocalWacAmount ,unitcompanyLocalAmount ,companyLocalAmount ,companyLocalExchangeRate ,unitcompanyReportingAmount ,companyReportingAmount ,companyReportingExchangeRate ,unitDonoursAmount ,donorsAmount ,donorsExchangeRate from srp_erp_ngo_commitmentdetails WHERE commitmentDetailAutoID={$commitmentDetailAutoID} ")->row_array();
        echo json_encode($data);
    }

    function update_commitment_itemDetail()
    {

        $this->form_validation->set_rules("itemAutoID", 'Item', 'trim|required');
        $this->form_validation->set_rules("UnitOfMeasureID", 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules("quantityRequested", 'Quantity Requested', 'trim|required');

        $this->form_validation->set_rules("estimatedAmount", 'Estimated Amount', 'trim|required');
        $this->form_validation->set_rules("projectID", 'Project', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $commitmentAutoId = $this->input->post('commitmentAutoId');
            $master = $this->db->query("select documentDate from srp_erp_ngo_commitmentmasters WHERE commitmentAutoId=$commitmentAutoId ")->row_array();
            $documentDate = $master['documentDate'];
            $expiryDate = $this->input->post('expiryDate');
            $date_format_policy = date_format_policy();
            $commitmentExpiryDate = $expiryDate;
            $expire = input_format_date($commitmentExpiryDate, $date_format_policy);
            if ($expire < $documentDate) {
                echo json_encode(array('e', 'Unable to proceed. Document date is greater than expiry date'));
                exit;
            }

            echo json_encode($this->OperationNgo_model->update_commitment_itemDetail());
        }
    }

    function update_commitment_cash_details()
    {


        $this->form_validation->set_rules("amount", 'Amount', 'trim|required');
        $this->form_validation->set_rules("projectID", 'Project', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $commitmentAutoId = $this->input->post('commitmentAutoId');
            $master = $this->db->query("select documentDate from srp_erp_ngo_commitmentmasters WHERE commitmentAutoId=$commitmentAutoId ")->row_array();
            $documentDate = $master['documentDate'];
            $expiryDate = $this->input->post('expiryDate');
            $date_format_policy = date_format_policy();
            $commitmentExpiryDate = $expiryDate;
            $expire = input_format_date($commitmentExpiryDate, $date_format_policy);
            if ($expire < $documentDate) {
                echo json_encode(array('e', 'Unable to proceed. Document date is greater than expiry date'));
                exit;

            }
            echo json_encode($this->OperationNgo_model->update_commitment_cash_details());
        }
    }

    function delete_commitment_project()
    {
        echo json_encode($this->OperationNgo_model->delete_commitment_project());
    }

    function donor_commitment_confirmation()
    {
        echo json_encode($this->OperationNgo_model->donor_commitment_confirmation());
    }

    function load_donor_commitment_confirmation()
    {
        $commitmentAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('commitmentAutoId') ?? '');
        $data['extra'] = $this->OperationNgo_model->fetch_donor_commitment_confirmation($commitmentAutoId);
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/operationNgo/donor_commitments_print', $data, TRUE);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['confirmedYN']);
        }
    }

    function load_donorcollectionView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('q') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $filter = "";
        if ($text != '') {
            $filter = " AND (documentSystemCode LIKE '%$text%' OR name LIKE '%$text%' )";
        }
        $convertFormat = convert_date_format_sql();


        $data['master'] = $this->db->query("SELECT srp_erp_ngo_donorcollectionmaster.createdUserID,approvedYN,documentCode,confirmedYN,srp_erp_ngo_donorcollectionmaster.collectionAutoId,documentSystemCode,DATE_FORMAT(documentDate,'{$convertFormat}') AS documentDate, referenceNo, transactionCurrency, donorsID,IFNULL(transactionAmount, 0) as transactionAmount, transactionCurrencyDecimalPlaces, name FROM srp_erp_ngo_donorcollectionmaster LEFT JOIN srp_erp_ngo_donors on donorsID=contactID LEFT JOIN ( SELECT sum(transactionAmount) AS transactionAmount, collectionAutoId FROM srp_erp_ngo_donorcollectiondetails GROUP BY collectionAutoId)srp_erp_ngo_donorcollectiondetails  ON srp_erp_ngo_donorcollectionmaster.collectionAutoId = srp_erp_ngo_donorcollectiondetails.collectionAutoId WHERE srp_erp_ngo_donorcollectionmaster.companyID={$companyID} AND isDeleted !=1 $filter ORDER BY collectionAutoId DESC")->result_array();

        $this->load->view('system/operationNgo/ajax/load_ngo_collections_master.php', $data);
    }

    function save_donorCollections()
    {
        $transactionCurrencyID = $this->input->post('transactionCurrencyID');
        $donorsID = $this->input->post('donorsID');
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required');
        $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
        $this->form_validation->set_rules('financeyear_period', 'Financial Year Period', 'trim|required');
        $this->form_validation->set_rules('DCbankCode', 'Bank or Cash', 'trim|required');
        $modeOfPayment = $this->input->post('modeOfPayment');
        if ($modeOfPayment == 2) {
            $this->form_validation->set_rules('DCchequeNo', 'Cheque No', 'trim|required');
            $this->form_validation->set_rules('DCchequeDate', 'Cheque Date', 'trim|required');
        }

        if (isset($donorsID)) {
            $this->form_validation->set_rules('donorsID', 'Donor', 'trim|required');
        }

        if ($this->input->post('transactionCurrencyID') == '' && isset($transactionCurrencyID)) {
            $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_donorCollections());
        }

    }

    function load_donor_collections_confirmation()
    {
        $commitmentAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('commitmentAutoId') ?? '');
        $data['extra'] = $this->OperationNgo_model->fetch_donor_commitment_confirmation($commitmentAutoId);
        $html = $this->load->view('system/operationNgo/donor_commitments_print', $data, TRUE);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);
        }
    }

    function commitmentdetailsexist()
    {
        $commitmentAutoId = trim($this->input->post('commitmentAutoId') ?? '');
        $data = $this->db->query("select * from srp_erp_ngo_commitmentdetails WHERE commitmentAutoId={$commitmentAutoId} ")->row_array();
        echo json_encode($data);
    }

    function load_collectionHeader()
    {
        echo json_encode($this->OperationNgo_model->load_collectionHeader());
    }

    function load_collection_items_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $collectionAutoId = trim($this->input->post('collectionAutoId') ?? '');
        $convertFormat = convert_date_format_sql();
        $this->db->select("projectName,collectionDetailAutoID ,collectionAutoId ,projectID ,type ,itemAutoID ,itemSystemCode ,itemDescription ,itemCategory ,expenseGLAutoID ,expenseGLCode ,expenseSystemGLCode ,expenseGLDescription ,expenseGLType ,wareHouseAutoID ,wareHouseCode ,wareHouseLocation ,wareHouseDescription ,defaultUOMID ,defaultUOM ,unitOfMeasureID ,unitOfMeasure ,conversionRateUOM ,itemQty ,srp_erp_ngo_donorcollectiondetails.description ,GLAutoID ,SystemGLCode ,GLCode ,GLDescription ,GLType ,unittransactionAmount ,transactionAmount ,companyLocalWacAmount ,unitcompanyLocalAmount ,companyLocalAmount ,companyLocalExchangeRate ,unitcompanyReportingAmount ,companyReportingAmount ,companyReportingExchangeRate ,unitDonoursAmount ,donorsAmount ,donorsExchangeRate ");
        $this->db->from('srp_erp_ngo_donorcollectiondetails');
        $this->db->join('srp_erp_ngo_projects', 'ngoProjectID = projectID', 'left');
        $this->db->where('type', 2);
        $this->db->where('srp_erp_ngo_donorcollectiondetails.companyID', $companyID);
        $this->db->where('srp_erp_ngo_donorcollectiondetails.collectionAutoId', $collectionAutoId);
        $this->db->order_by('collectionDetailAutoID', 'desc');
        $data['header'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load-collection-item-view', $data);
    }

    function load_donor_collection_cash()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $collectionAutoId = trim($this->input->post('collectionAutoId') ?? '');
        $convertFormat = convert_date_format_sql();
        $this->db->select("projectName,collectionDetailAutoID ,collectionAutoId ,projectID ,type ,itemAutoID ,itemSystemCode ,itemDescription ,itemCategory ,expenseGLAutoID ,expenseGLCode ,expenseSystemGLCode ,expenseGLDescription ,expenseGLType ,wareHouseAutoID ,wareHouseCode ,wareHouseLocation ,wareHouseDescription ,defaultUOMID ,defaultUOM ,unitOfMeasureID ,unitOfMeasure ,conversionRateUOM ,itemQty ,srp_erp_ngo_donorcollectiondetails.description ,GLAutoID ,SystemGLCode ,GLCode ,GLDescription ,GLType,unittransactionAmount ,transactionAmount ,companyLocalWacAmount ,unitcompanyLocalAmount ,companyLocalAmount ,companyLocalExchangeRate ,unitcompanyReportingAmount ,companyReportingAmount ,companyReportingExchangeRate ,unitDonoursAmount ,donorsAmount ,donorsExchangeRate ");
        $this->db->from('srp_erp_ngo_donorcollectiondetails');
        $this->db->join('srp_erp_ngo_projects', 'ngoProjectID = projectID', 'left');
        $this->db->where('type', 1);
        $this->db->where('srp_erp_ngo_donorcollectiondetails.companyID', $companyID);
        $this->db->where('srp_erp_ngo_donorcollectiondetails.collectionAutoId', $collectionAutoId);
        $this->db->order_by('collectionDetailAutoID', 'desc');
        $data['header'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load-collection-cash-view', $data);
    }

    function load_donor_collection_confirmation()
    {
        $collectionAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('collectionAutoId') ?? '');
        $data['extra'] = $this->OperationNgo_model->fetch_donor_collection_confirmation($collectionAutoId);
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/operationNgo/donor_collection_print', $data, TRUE);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);
        }
    }

    function collectiondetailsexist()
    {
        $collectionAutoId = trim($this->input->post('collectionAutoId') ?? '');
        $data = $this->db->query("select * from srp_erp_ngo_donorcollectiondetails WHERE collectionAutoId={$collectionAutoId} ")->row_array();
        echo json_encode($data);
    }

    function save_donor_collection_item_detail()
    {
        $searches = $this->input->post('search');
        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'WareHouse', 'trim|required');
            $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->OperationNgo_model->save_donor_collection_item_detail());
        }
    }

    function save_donor_collection_cash_detail()
    {
        $projectID = $this->input->post('projectID');
        foreach ($projectID as $key => $id) {
            // $this->form_validation->set_rules("commitmentDetailAutoID[{$key}]", '', 'trim|required');
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');

            $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');

        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->OperationNgo_model->save_donor_collection_cash_detail());
        }
    }

    function fetch_donor_collection_item_detail()
    {
        $collectionDetailAutoID = $this->input->post('collectionDetailAutoID');
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query("select commitmentDetailID,commitmentAutoID,collectionDetailAutoID,collectionAutoId ,projectID ,type ,itemAutoID ,itemSystemCode ,itemDescription ,itemCategory ,expenseGLAutoID ,expenseGLCode ,expenseSystemGLCode ,expenseGLDescription ,expenseGLType ,revenueGLAutoID ,revenueGLCode ,revenueSystemGLCode ,revenueGLDescription ,revenueGLType ,assetGLAutoID ,assetGLCode ,assetSystemGLCode ,assetGLDescription ,assetGLType ,wareHouseAutoID ,wareHouseCode ,wareHouseLocation ,wareHouseDescription ,defaultUOMID ,defaultUOM ,unitOfMeasureID ,unitOfMeasure ,conversionRateUOM ,itemQty ,description ,GLAutoID ,SystemGLCode ,GLCode ,GLDescription ,GLType,unittransactionAmount ,transactionAmount ,companyLocalWacAmount ,unitcompanyLocalAmount ,companyLocalAmount ,companyLocalExchangeRate ,unitcompanyReportingAmount ,companyReportingAmount ,companyReportingExchangeRate ,unitDonoursAmount ,donorsAmount ,donorsExchangeRate from srp_erp_ngo_donorcollectiondetails WHERE collectionDetailAutoID={$collectionDetailAutoID} ")->row_array();
        echo json_encode($data);
    }

    function update_collection_itemDetail()
    {

        $this->form_validation->set_rules("itemAutoID", 'Item', 'trim|required');
        $this->form_validation->set_rules("UnitOfMeasureID", 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules("quantityRequested", 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules("wareHouseAutoID", 'WareHouse', 'trim|required');
        $this->form_validation->set_rules("estimatedAmount", 'Estimated Amount', 'trim|required');
        $this->form_validation->set_rules("projectID", 'Project', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_collection_itemDetail());
        }
    }

    function update_collection_cash_details()
    {

        $this->form_validation->set_rules("description", 'Description', 'trim|required');
        $this->form_validation->set_rules("amount", 'Amount', 'trim|required');
        $this->form_validation->set_rules("projectID", 'Project', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_collection_cash_details());
        }
    }

    function delete_collectionDetail()
    {
        $collectionDetailAutoID = $this->input->post('collectionDetailAutoID');
        $this->db->delete('srp_erp_ngo_donorcollectiondetails', array('collectionDetailAutoID' => $collectionDetailAutoID));
        echo json_encode(array('s', 'Successfully Deleted'));
    }

    function delete_collection_project()
    {
        echo json_encode($this->OperationNgo_model->delete_collection_project());
    }

    function donor_collection_confirmation()
    {
        echo json_encode($this->OperationNgo_model->donor_collection_confirmation());
    }

    function donor_collection_table_approval()
    {
        $companyID = current_companyID();
        $this->datatables->select("documentApprovedID,NAME,approvedYN,approvalLevelID,collectionAutoId,confirmedYN,documentCode,documentDate,documentSystemCode,donorsID,referenceNo,transactionAmount,transactionCurrency");
        $this->datatables->from("(SELECT documentApprovedID,srp_erp_ngo_donorcollectionmaster.approvedYN,approvalLevelID, srp_erp_ngo_donorcollectionmaster.documentCode, confirmedYN, srp_erp_ngo_donorcollectionmaster.collectionAutoId, srp_erp_ngo_donorcollectionmaster.documentSystemCode, srp_erp_ngo_donorcollectionmaster.documentDate, referenceNo, transactionCurrency, donorsID, FORMAT(IFNULL(transactionAmount, 0),transactionCurrencyDecimalPlaces ) as transactionAmount, NAME FROM srp_erp_ngo_donorcollectionmaster LEFT JOIN srp_erp_ngo_donors ON donorsID = contactID LEFT JOIN ( SELECT sum(transactionAmount) AS transactionAmount, collectionAutoId FROM srp_erp_ngo_donorcollectiondetails GROUP BY collectionAutoId ) srp_erp_ngo_donorcollectiondetails ON srp_erp_ngo_donorcollectionmaster.collectionAutoId = srp_erp_ngo_donorcollectiondetails.collectionAutoId LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_ngo_donorcollectionmaster.collectionAutoId AND approvalLevelID = currentLevelNo LEFT JOIN srp_erp_approvalusers ON levelNo = srp_erp_ngo_donorcollectionmaster.currentLevelNo WHERE isDeleted != 1 AND srp_erp_documentapproved.documentID = 'DC' AND srp_erp_approvalusers.documentID = 'DC' AND employeeID = '{$this->common_data['current_userID']}' AND srp_erp_ngo_donorcollectionmaster.approvedYN={$this->input->post('approvedYN')} AND srp_erp_ngo_donorcollectionmaster.companyID={$companyID} ORDER BY collectionAutoId DESC )t");
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "DC", collectionAutoId)');
        $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
        $this->datatables->add_column('detail', '<b>Donor Name : </b> $1 <b> <br>Total Amount : </b> $2  &nbsp;  </b> $3',
            'NAME,transactionCurrency,transactionAmount');
        $this->datatables->add_column('edit', '$1',
            'donor_collection_action(collectionAutoId,approvalLevelID,approvedYN,documentApprovedID)');
        echo $this->datatables->generate();
    }

    function save_donor_collection_approval()
    {
        $system_code = trim($this->input->post('collectionAutoId') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'DC', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('collectionAutoId');
                $this->db->where('collectionAutoId', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_ngo_donorcollectionmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('collectionAutoId', 'Donor Collection ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->OperationNgo_model->save_donor_collection_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('collectionAutoId');
            $this->db->where('collectionAutoId', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_ngo_donorcollectionmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'DC', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Donor Collection Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('collectionAutoId', 'Donor collection ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->OperationNgo_model->save_donor_collection_approval());
                    }
                }
            }
        }
    }

    function getallcommitments()
    {
        $companyID = current_companyID();
        $projectID = $this->input->post('projectID');
        $collectionAutoId = $this->input->post('collectionAutoId');
        if ($projectID == '') {
            echo json_encode(array());
            exit;
        }
        $master = $this->db->query("select transactionCurrencyID,donorsID from `srp_erp_ngo_donorcollectionmaster` WHERE collectionAutoId={$collectionAutoId} ")->row_array();
        $transactionCurrencyID = $master['transactionCurrencyID'];
        $donorsID = $master['donorsID'];

        $data = $this->db->query("SELECT projectID, srp_erp_ngo_commitmentmasters.commitmentAutoId, documentSystemCode, transactionCurrencyID FROM srp_erp_ngo_commitmentmasters INNER JOIN ( SELECT * FROM srp_erp_ngo_commitmentdetails WHERE projectID = {$projectID} ) d ON d.commitmentAutoId = srp_erp_ngo_commitmentmasters.commitmentAutoId WHERE srp_erp_ngo_commitmentmasters.companyID = {$companyID} AND confirmedYN = 1 AND transactionCurrencyID=$transactionCurrencyID AND donorsID = $donorsID GROUP BY srp_erp_ngo_commitmentmasters.commitmentAutoId")->result_array();


        echo json_encode($data);

    }

    function referback_donor_collection()
    {
        $collectionAutoId = $this->input->post('collectionAutoId');


        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($collectionAutoId, 'DC');
        if ($status == 1) {
            echo json_encode(array('s', ' Referred Back Successfully.', $status));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $status));
        }
    }

    function referback_donor_commitment()
    {
        $commitmentAutoId = $this->input->post('commitmentAutoId');

        $recordsExist = $this->db->query("select GROUP_CONCAT(documentSystemCode) as doc from (SELECT
	documentSystemCode
FROM
	`srp_erp_ngo_donorcollectiondetails`
INNER JOIN srp_erp_ngo_donorcollectionmaster on srp_erp_ngo_donorcollectiondetails.collectionAutoId=srp_erp_ngo_donorcollectionmaster.collectionAutoId AND srp_erp_ngo_donorcollectionmaster.isDeleted=0
WHERE
	srp_erp_ngo_donorcollectiondetails.commitmentAutoID = {$commitmentAutoId} 
group by srp_erp_ngo_donorcollectiondetails.commitmentAutoID ) t")->row_array();
        if (!empty($recordsExist) && $recordsExist['doc'] != '') {
            echo json_encode(array('e', 'Unable proceed . Donor collection ' . $recordsExist['doc'] . '  documents  using it for reference '));
            exit;
        }

        $dataUpdate = array(
            'confirmedYN' => 0,
            'confirmedByEmpID' => '',
            'confirmedByName' => '',
            'confirmedDate' => '',
        );

        $this->db->where('commitmentAutoId', $commitmentAutoId);
        $this->db->update('srp_erp_ngo_commitmentmasters', $dataUpdate);

        echo json_encode(array('s', ' Referred Back Successfully.'));

    }

    function fetch_itemrecode_donor()
    {
        echo json_encode($this->OperationNgo_model->fetch_itemrecode_donor());
    }

    function getallcommitmentsDetails()
    {
        $companyID = current_companyID();
        $projectID = $this->input->post('projectID');
        if ($projectID == '') {
            echo json_encode(array());
            exit;
        }
        $collectionAutoId = $this->input->post('collectionAutoId');
        $master = $this->db->query("select transactionCurrencyID,donorsID from `srp_erp_ngo_donorcollectionmaster` WHERE collectionAutoId={$collectionAutoId} ")->row_array();
        $transactionCurrencyID = $master['transactionCurrencyID'];
        $donorsID = $master['donorsID'];

        /*  $qry=  "SELECT concat( d.commitmentAutoId, ' | ', d.commitmentDetailAutoID ) AS ID, CONCAT( documentSystemCode, ' | ', d.description, ' | ', FORMAT( d.transactionAmount, transactionCurrencyDecimalPlaces ) ) AS description, transactionCurrencyID FROM srp_erp_ngo_commitmentmasters INNER JOIN ( SELECT * FROM srp_erp_ngo_commitmentdetails WHERE type = 1 AND	projectID = {$projectID} ) d ON d.commitmentAutoId = srp_erp_ngo_commitmentmasters.commitmentAutoId WHERE srp_erp_ngo_commitmentmasters.companyID = {$companyID} AND transactionCurrencyID={$transactionCurrencyID} AND confirmedYN = 1 AND donorsID=$donorsID";*/

        $qry = "SELECT transactionAmount,amount, concat( commitmentAutoId, ' | ', commitmentDetailAutoID ) AS ID, CONCAT( documentSystemCode, ' | ', description, ' | Balance ', FORMAT( transactionAmount - amount, transactionCurrencyDecimalPlaces ) ) AS description, transactionCurrencyID FROM ( SELECT d.commitmentAutoId, d.commitmentDetailAutoID, documentSystemCode, d.description, d.transactionAmount, transactionCurrencyDecimalPlaces transactionCurrencyID, transactionCurrencyDecimalPlaces, ( SELECT ifnull(sum(transactionAmount), 0) FROM srp_erp_ngo_donorcollectionmaster LEFT JOIN `srp_erp_ngo_donorcollectiondetails` ON srp_erp_ngo_donorcollectionmaster.collectionAutoId = srp_erp_ngo_donorcollectiondetails.collectionAutoId AND type = 1 AND projectID = {$projectID} WHERE  transactionCurrencyID = {$transactionCurrencyID} AND donorsID = {$donorsID} AND srp_erp_ngo_donorcollectionmaster.companyID = {$companyID} AND  commitmentDetailID=d.commitmentdetailAutoID ) AS amount FROM srp_erp_ngo_commitmentmasters INNER JOIN ( SELECT * FROM srp_erp_ngo_commitmentdetails WHERE type = 1 AND projectID = {$projectID} ) d ON d.commitmentAutoId = srp_erp_ngo_commitmentmasters.commitmentAutoId WHERE srp_erp_ngo_commitmentmasters.companyID = {$companyID} AND transactionCurrencyID = {$transactionCurrencyID} AND confirmedYN = 1 AND donorsID = {$donorsID} ) t having transactionAmount > amount";

        $data = $this->db->query($qry)->result_array();

        echo json_encode($data);

    }

    function load_beneficiaryTemplate_view()
    {
        $ngoProjectID = trim($this->input->post('ngoProjectID') ?? '');
        $template = $this->db->query("SELECT templateID FROM srp_erp_ngo_projects WHERE ngoProjectID = {$ngoProjectID}")->row_array();
        if ($template) {
            $this->load->view("system/operationNgo/template/" . $template['templateID'] . "");
        }

    }

    function load_beneficiaryManagement_view()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $country = trim($this->input->post('countryID') ?? '');
        $province = trim($this->input->post('province') ?? '');
        $distric = trim($this->input->post('district') ?? '');
        $division = trim($this->input->post('division') ?? '');
        $subdivision = trim($this->input->post('subDivision') ?? '');
        $project = trim($this->input->post('projectID') ?? '');

        $currentemployeeid = $this->common_data['current_userID'];


        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND ((fullName Like '%" . $text . "%') OR (bfm.email Like '%" . $text . "%') OR (CONCAT(phoneCountryCodePrimary,phoneAreaCodePrimary,phonePrimary) Like '%" . $text . "%'))";
        }
        $country_search = '';
        if (isset($country) && !empty($country)) {
            $country_search = " AND bfm.countryID = {$country}";
        }
        $search_sorting = '';
        if (isset($sorting) && $sorting != '#') {
            $search_sorting = " AND fullName Like '" . $sorting . "%'";
        }
        $filter_status = '';
        if (isset($status) && $status != '') {
            if ($status == 1) {
                $filter_status = " AND confirmedYN = 0";
            } elseif ($status == 2) {
                $filter_status = " AND confirmedYN = 1";
            }
        }
        $province_filter = '';
        if (isset($province) && !empty($province)) {
            $province_filter = " AND bfm.province = {$province}";
        }
        $distric_filter = '';
        if (isset($distric) && !empty($distric)) {
            $distric_filter = " AND bfm.district = {$distric}";
        }

        $division_filter = '';
        if (isset($division) && !empty($division)) {
            $division_filter = " AND bfm.division = {$division}";
        }
        $sub_division_filter = '';
        if (isset($subdivision) && !empty($subdivision)) {
            $sub_division_filter = " AND bfm.subDivision = {$subdivision}";
        }
        $project_filter = '';
        if (isset($project) && !empty($project)) {
            $project_filter = " AND bfm.projectID = $project";
        }

        $where = "bfm.companyID = " . $companyID . $search_string . $search_sorting . $filter_status . $country_search . $province_filter . $distric_filter . $division_filter . $sub_division_filter . $project_filter;

        $data['header'] = $this->db->query("SELECT benificiaryID,fullName,benificiaryImage,email,CountryDes,bfm.countryID,bfm.province,bfm.district,bfm.division,bfm.subDivision,bfm.projectID,CONCAT(phoneCountryCodePrimary,' - ',phoneAreaCodePrimary,phonePrimary) AS MasterPrimaryNumber,bfm.confirmedYN as confirmedYN,systemCode,projectID FROM srp_erp_ngo_beneficiarymaster bfm LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bfm.countryID WHERE $where  AND bfm.Com_MasterID IS NULL  ORDER BY benificiaryID DESC ")->result_array();

        $this->load->view('system/operationNgo/ajax/load_benificiary_master', $data);

    }

    function fetch_beneficiary_family_details()
    {
        $beneficiaryID = $this->input->post('beneficiaryID');

        $printHelpAndNestArray = fetch_ngo_project_shortCode($beneficiaryID);
        $testArray = array_column($printHelpAndNestArray, 'projectShortCode');

        if (in_array('DA', $testArray)) {
            $data['benificiaryArray'] = $this->OperationNgo_model->fetch_beneficiary_family_details($beneficiaryID);
            $data['type'] = 'edit';
            $this->load->view('system/operationNgo/ajax/load_beneficiary_family_damage_assesment_details', $data);
        } else {
            $data['benificiaryArray'] = $this->OperationNgo_model->fetch_beneficiary_family_details($beneficiaryID);
            $data['type'] = 'edit';
            $this->load->view('system/operationNgo/ajax/load_beneficiary_family_details', $data);
        }

    }

    function fetch_beneficiary_family_details_view()
    {
        $beneficiaryID = $this->input->post('beneficiaryID');
        $data['benificiaryArray'] = $this->OperationNgo_model->fetch_beneficiary_family_details($beneficiaryID);
        $data['type'] = 'view';
        $this->load->view('system/operationNgo/ajax/load_beneficiary_family_details', $data);
    }

    function save_beneficiary_header()
    {
        $templateType = trim($this->input->post('templateType') ?? '');
        $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        //$this->form_validation->set_rules("subProjectID", 'Sub Project', 'trim|required');
        $this->form_validation->set_rules("secondaryCode", 'Secondary Reference No', 'trim|required');
        $this->form_validation->set_rules("registeredDate", 'Registered Date', 'trim|required|validate_date');
        $this->form_validation->set_rules("emp_title", 'Title', 'trim|required');
        $this->form_validation->set_rules("fullName", 'Full Name', 'trim|required');
        $this->form_validation->set_rules("nameWithInitials", 'Name with Initials', 'trim|required');
        $this->form_validation->set_rules("countryCodePrimary", 'Phone (Primary)', 'trim|required');
        $this->form_validation->set_rules("phonePrimary", 'Phone (Primary)', 'trim|required');
        $this->form_validation->set_rules("address", 'Address', 'trim|required');
        $this->form_validation->set_rules("benificiaryType", 'Beneficiary Type', 'trim|required');
        $this->form_validation->set_rules("dateOfBirth", 'Date of Birth', 'trim|required|validate_date');
        $this->form_validation->set_rules("countryID", 'Country', 'trim|required');
        $this->form_validation->set_rules("province", 'Province / State', 'trim|required');
        $this->form_validation->set_rules("district", 'Area / District', 'trim|required');
        $this->form_validation->set_rules("division", 'Division', 'trim|required');
        $this->form_validation->set_rules("subDivision", 'Mahalla', 'trim|required');
        //$this->form_validation->set_rules("projectIDetail", 'Project', 'trim|required');


        if ($templateType == 'HN') {
            $this->form_validation->set_rules("nationalIdentityCardNo", 'NIC No', 'trim|required');
            $this->form_validation->set_rules("familyDetail", 'Family Details', 'trim|required');
            $this->form_validation->set_rules("ownLandAvailable", 'Own Land Available', 'trim|required');
            $this->form_validation->set_rules("totalcostforahouse", 'Total Cost For A House', 'trim|required');
        }
        if ($templateType == 'DA') {
            $this->form_validation->set_rules("da_occupationID", 'Occupation', 'trim|required');
            $this->form_validation->set_rules("da_jammiyahDivision", 'Jammiya Division', 'trim|required');
            $this->form_validation->set_rules("da_GnDivision", 'GN Division', 'trim|required');
            $this->form_validation->set_rules("ethnicityID", 'Ethnicity', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_beneficiary_header());
        }
    }

    function new_beneficiary_type()
    {
        $this->form_validation->set_rules('type', 'Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->new_beneficiary_type());
        }
    }

    function load_beneficiary_header()
    {
        echo json_encode($this->OperationNgo_model->load_beneficiary_header());
    }

    function save_beneficiary_familyDetails()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('nationality', 'Nationality', 'required|numeric');
        $this->form_validation->set_rules('relationshipType', 'Relationship', 'required|numeric');
        $this->form_validation->set_rules('DOB', 'Date of Birth', 'trim|required');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo $this->OperationNgo_model->save_beneficiary_familyDetails();
        }
    }

    function ajax_update_beneficiary_familydetails()
    {
        $result = $this->OperationNgo_model->xeditable_update('srp_erp_ngo_beneficiaryfamilydetails', 'empfamilydetailsID');
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'updated'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'updated Fail'));
        }
    }

    function delete_beneficiary_familydetail()
    {
        echo json_encode($this->OperationNgo_model->delete_beneficiary_familydetail());
    }

    function delete_master_notes_allDocuments()
    {
        echo json_encode($this->OperationNgo_model->delete_master_notes_allDocuments());
    }

    function load_beneficiaryManagement_editView()
    {
        $convertFormat = convert_date_format_sql();
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');

        $data['header'] = $this->db->query("SELECT *,CountryDes,DATE_FORMAT(bfm.createdDateTime,'" . $convertFormat . "') AS createdDate,DATE_FORMAT(bfm.modifiedDateTime,'" . $convertFormat . "') AS modifydate,DATE_FORMAT(bfm.registeredDate,'" . $convertFormat . "') AS registeredDate,DATE_FORMAT(bfm.dateOfBirth,'" . $convertFormat . "') AS dateOfBirth,bfm.createdUserName as contactCreadtedUser,bfm.email as contactEmail,bfm.phonePrimary as contactPhonePrimary,bfm.phoneSecondary as contactPhoneSecondary,project.projectName as projectName,benType.description as benTypeDescription,smpro.Description as provinceName,smdis.Description as districtName,smdiv.Description as divisionName,smsubdiv.Description as subDivisionName,bfm.projectID FROM srp_erp_ngo_beneficiarymaster bfm LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = bfm.countryID LEFT JOIN srp_erp_ngo_projects project ON project.ngoProjectID = bfm.projectID LEFT JOIN srp_erp_ngo_benificiarytypes benType ON benType.beneficiaryTypeID = bfm.benificiaryType LEFT JOIN srp_erp_statemaster smpro ON smpro.stateID = bfm.province LEFT JOIN srp_erp_statemaster smdis ON smdis.stateID = bfm.district LEFT JOIN srp_erp_statemaster smdiv ON smdiv.stateID = bfm.division LEFT JOIN srp_erp_statemaster smsubdiv ON smsubdiv.stateID = bfm.subDivision WHERE benificiaryID = " . $benificiaryID . "")->row_array();

        $data['projects'] = $this->db->query("SELECT projects.projectName FROM srp_erp_ngo_beneficiaryprojects bp LEFT JOIN srp_erp_ngo_projects projects ON projects.ngoProjectID = bp.projectID WHERE bp.beneficiaryID = {$benificiaryID}")->result_array();

        $this->load->view('system/operationNgo/ajax/load_ngo_beneficiary_edit_view', $data);
    }

    function load_beneficiary_all_notes()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');

        $where = "companyID = " . $companyID . " AND documentID = 5 AND documentAutoID = " . $benificiaryID . "";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_notes');
        $this->db->where($where);
        $this->db->order_by('notesID', 'desc');
        $data['notes'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load_ngo_beneficiary_notes', $data);
    }

    function add_beneficiary_notes()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->add_beneficiary_notes());
        }
    }

    function beneficiary_image_upload()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->beneficiary_image_upload());
        }
    }

    function beneficiary_image_upload_helpNest()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->beneficiary_image_upload_helpNest());
        }
    }

    function beneficiary_image_upload_helpNest_two()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->beneficiary_image_upload_helpNest_two());
        }
    }

    function fetch_province_based_countryDropdown()
    {
        $data_arr = array();
        $countyID = $this->input->post('countyID');
        $province = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE countyID = {$countyID} AND type = 1")->result_array();
        $data_arr = array('' => 'Select a Province');
        if (!empty($province)) {
            foreach ($province as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('province', $data_arr, '', 'class="form-control select2" id="province" onchange="loadcountry_District(this.value)" ');
    }

    function new_beneficiary_province()
    {
        $this->form_validation->set_rules('province_shortCode', 'Short Code', 'trim|required');
        $this->form_validation->set_rules('province_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('hd_province_countryID', 'Country', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->new_beneficiary_province());
        }
    }

    function new_beneficiary_district()
    {
        $this->form_validation->set_rules('district_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('district_shortCode', 'Short Code', 'trim|required');
        $this->form_validation->set_rules('hd_district_countryID', 'Country', 'trim|required');
        $this->form_validation->set_rules('hd_district_provinceID', 'Province', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->new_beneficiary_district());
        }
    }

    function fetch_province_based_districtDropdown()
    {
        $data_arr = array();
        $masterID = $this->input->post('masterID');
        if (!empty($masterID)) {
            $district = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 2")->result_array();
        }
        $data_arr = array('' => 'Select a District');
        if (!empty($district)) {
            foreach ($district as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('district', $data_arr, '', 'class="form-control select2" id="district" onchange="loadcountry_Division(this.value),loadcountry_jamiya_Division(this.value)" ');
    }

    function new_beneficiary_division()
    {
        $districtPolicy = fetch_ngo_policies('JD');
        $this->form_validation->set_rules('division_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('hd_division_countryID', 'Country', 'trim|required');
        $this->form_validation->set_rules('division_shortCode', 'Short Code', 'trim|required');
        $this->form_validation->set_rules('hd_division_districtID', 'District', 'trim|required');

        if (!empty($districtPolicy)) {
            $this->form_validation->set_rules('divisionTypeCode', 'Division Type', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->new_beneficiary_division());
        }
    }

    function new_beneficiary_sub_division()
    {
        $divisionPolicy = fetch_ngo_policies('GN');
        $this->form_validation->set_rules('sub_division_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('hd_sub_division_countryID', 'Country', 'trim|required');
        $this->form_validation->set_rules('sub_division_shortCode', 'Short Code', 'trim|required');
        $this->form_validation->set_rules('hd_sub_division_districtID', 'District', 'trim|required');
        if (!empty($divisionPolicy)) {
            $this->form_validation->set_rules('divisionTypeCode', 'Division Type', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->new_beneficiary_sub_division());
        }
    }

    function fetch_division_based_districtDropdown()
    {
        $data_arr = array();
        $masterID = $this->input->post('masterID');
        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 3 AND divisionTypeCode = 'DD'")->result_array();
        }
        $data_arr = array('' => 'Select a Division');
        if (!empty($division)) {
            foreach ($division as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('division', $data_arr, '', 'class="form-control select2" id="division" onchange="loadcountry_sub_Division(this.value),loadcountry_gs_sub_Division(this.value)"');
    }

    function fetch_jamiya_division_based_districtDropdown()
    {
        $data_arr = array();
        $masterID = $this->input->post('masterID');
        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 3 AND divisionTypeCode = 'JD'")->result_array();
        }
        $data_arr = array('' => 'Select a Division');
        if (!empty($division)) {
            foreach ($division as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('da_jammiyahDivision', $data_arr, '', 'class="form-control select2" id="da_jammiyahDivision"');
    }

    function fetch_jamiya_sub_division_based_divisionDropdown()
    {
        $data_arr = array();
        $masterID = $this->input->post('masterID');
        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 4 AND divisionTypeCode = 'GN'")->result_array();
        }
        $data_arr = array('' => 'Select a DN Division');
        if (!empty($division)) {
            foreach ($division as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('da_GnDivision', $data_arr, '', 'class="form-control select2" id="da_GnDivision"');
    }

    function fetch_sub_division_based_divisionDropdown()
    {
        $data_arr = array();
        $masterID = $this->input->post('masterID');
        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 4 AND divisionTypeCode = 'MH'")->result_array();
        }
        $data_arr = array('' => 'Select a Mahalla');
        if (!empty($division)) {
            foreach ($division as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('subDivision', $data_arr, '', 'class="form-control select2" id="subDivision"');
    }

    function delete_beneficiary_master()
    {
        echo json_encode($this->OperationNgo_model->delete_beneficiary_master());
    }

    function fetch_ngo_document_Master()
    {
        $this->datatables->select('t1.DocDesID as doc_ID, DocDescription as doc_Description, t1.SortOrder AS SortOrder')
            ->from('srp_erp_ngo_documentdescriptionmaster AS t1')
            ->add_column('edit', '$1', 'action_ngo_docMaster(doc_ID, doc_Description)')
            ->where('t1.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    function save_ngo_document_Master()
    {
        $descriptions = $this->input->post('description');
        foreach ($descriptions as $key => $description) {
            $this->form_validation->set_rules("description[{$key}]", 'Document', 'trim|required');
            $this->form_validation->set_rules("sortOrder[{$key}]", 'Sort Order', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->OperationNgo_model->save_ngo_document_Master());
        }
    }

    function save_ngo_document_setupMaster()
    {
        $documentIDs = $this->input->post('documentID');
        foreach ($documentIDs as $key => $documentID) {
            $this->form_validation->set_rules("documentID[{$key}]", 'NGO Document', 'trim|required');
            $this->form_validation->set_rules("ngoProjectID[{$key}]", 'ProjectID', 'trim|required');
            $this->form_validation->set_rules("descriptionID[{$key}]", 'Document', 'trim|required');
            $this->form_validation->set_rules("sortOrder[{$key}]", 'Sort Order', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->OperationNgo_model->save_ngo_document_setupMaster());
        }
    }

    function delete_ngo_document_master()
    {
        $this->form_validation->set_rules('DocDesID', 'Documents ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_ngo_document_master());
        }
    }

    function update_ngo_document_master()
    {
        $this->form_validation->set_rules('edit_description', 'Documents Description', 'trim|required');
        $this->form_validation->set_rules('edit_sortOrder', 'Sort Order', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_ngo_document_master());
        }
    }

    function fetch_ngo_document_Setup()
    {
        $this->datatables->select('dds.DocDesSetupID as DocDesSetupID,dm.description as ngoDocument,pro.projectName as projectName,ddm.DocDescription as docMasterName,dds.isMandatory as isMandatory,dds.SortOrder as SortOrder,dds.DocDesID AS DocDesID,dds.projectID AS ngoprojectID,dds.ngoDocumentID AS ngoDocumentID')
            ->from('srp_erp_ngo_documentdescriptionsetup AS dds')
            ->join('srp_erp_ngo_documentdescriptionmaster AS ddm', 'dds.DocDesID = ddm.DocDesID')
            ->join('srp_erp_ngo_documents AS dm', 'dm.documentID=dds.ngoDocumentID')
            ->join('srp_erp_ngo_projects AS pro', 'pro.ngoProjectID = dds.projectID', 'LEFT')
            ->add_column('edit', '$1', 'action_ngo_docSetup(DocDesSetupID, ngoDocument)')
            ->add_column('mandatory', '<center>$1</center>', 'ngo_mandatoryStatus(isMandatory)')
            ->where('dds.companyID', current_companyID());
        echo $this->datatables->generate();
    }

    function update_ngo_document_setup()
    {
        $this->form_validation->set_rules('edit_documentID', 'NGO Document', 'trim|required');
        $this->form_validation->set_rules('edit_ngoProjectID', 'Project', 'trim|required');
        $this->form_validation->set_rules('edit_descriptionID', 'Document', 'trim|required');
        $this->form_validation->set_rules('edit_sortOrder', 'Sort Order', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_ngo_document_setup());
        }
    }

    function load_beneficiary_documents_view()
    {
        $projectID = trim($this->input->post('projectID') ?? '');
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        $docDet = $this->OperationNgo_model->load_beneficiary_documents();
        //echo '<pre>';print_r($docDet); echo '</pre>'; die();

        $data['docDet'] = $docDet;
        $data['projectID'] = $projectID;
        $data['benificiaryID'] = $benificiaryID;
        $this->load->view('system/operationNgo/ajax/load_beneficiary_document_view', $data);
    }

    function load_beneficiary_documents_view_forEdit()
    {
        $beneficiaryID = trim($this->input->post('benificiaryID') ?? '');
        $docDet = $this->OperationNgo_model->load_beneficiary_documents();
        $data['docDet'] = $docDet;
        $this->load->view('system/operationNgo/ajax/load_beneficiary_document_view_forEdit', $data);
    }

    function save_beneficiary_document()
    {
        $this->form_validation->set_rules('document', 'Document', 'trim|required');
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_beneficiary_document());
        }
    }

    function delete_beneficiary_document()
    {
        $this->form_validation->set_rules('DocDesFormID', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_beneficiary_document());
        }
    }

    function delete_ngo_document_setup()
    {
        $this->form_validation->set_rules('DocDesSetupID', 'Documents ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_ngo_document_setup());
        }
    }

    function load_ngo_area_setup()
    {
        $countryID = trim($this->input->post('countryID') ?? '');
        $filter = '';
        if (!empty($countryID)) {
            $filter = "WHERE countryID = $countryID";
        }
        $allCountrys = $this->db->query("SELECT * FROM srp_erp_countrymaster $filter")->result_array();
        //$result = $this->OperationNgo_model->load_ngo_all_countries();
        $data['country'] = $allCountrys;
        $this->load->view('system/operationNgo/ajax/load_area_setup_view', $data);
    }

    function beneficiary_confirmation()
    {
        echo json_encode($this->OperationNgo_model->beneficiary_confirmation());
    }

    function beneficiary_familyimage_upload()
    {
        $empfamilydetailsID = $this->input->post('empfamilydetailsID');
       $itemimageExist = $this->db->query("SELECT image FROM srp_erp_ngo_beneficiaryfamilydetails WHERE empfamilydetailsID = $empfamilydetailsID ")->row_array();
        if(!empty($itemimageExist))
        {
            $this->s3->delete('uploads/NGO/beneficiaryFamilyImage/'.$itemimageExist['image']);
        }
        $info = new SplFileInfo($_FILES["document_file"]["name"]);
        $fileName = 'FD_'.$this->common_data['company_data']['company_code'].'_'. trim($this->input->post('empfamilydetailsID') ?? '') . '.' . $info->getExtension();
        $currentDatetime = format_date_mysql_datetime();
        $file = $_FILES['document_file'];
        if($file['error'] == 1){
            echo json_encode(array('e', 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB)'));
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if(!in_array($ext, $allowed_types)){
            echo json_encode(array('e',"The file type you are attempting to upload is not allowed. ( .{$ext} )"));
        }
        $size = $file['size'];
        $size = number_format($size / 1048576, 2);
        if($size > 5){
            echo json_encode(array('e',"The file you are attempting to upload is larger than the permitted size. (maximum 5MB)"));
        }
        $path = "uploads/ngo/beneficiaryFamilyImage/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload) {
            echo json_encode(array('e',"Error in document upload location configuration"));
        }

        $this->db->trans_start();
        $currentDatetime = format_date_mysql_datetime();

        $currentdate = $currentDatetime;
        $data['image'] = $fileName;
        $data['timestamp'] = $currentdate;
        $this->db->where('empfamilydetailsID', trim($this->input->post('empfamilydetailsID') ?? ''));
        $this->db->update('srp_erp_ngo_beneficiaryfamilydetails', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', "Image Upload Failed." . $this->db->_error_message()));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s','Image uploaded  Successfully.'));
        }






    /*    if (!$this->upload->do_upload("document_file")) {
            echo json_encode(array('e', 'Upload failed ' . $this->upload->display_errors()));
        } else {
            $data1 = $this->upload->data();
            $fileName = $this->input->post('empfamilydetailsID') . '_FD_' . time() . $data1["file_ext"];

            $upData = array(
                'image' => $fileName,
            );
            $result = $this->db->where('empfamilydetailsID', $this->input->post('empfamilydetailsID'))->update('srp_erp_ngo_beneficiaryfamilydetails', $upData);

            if ($result) {
                echo json_encode(array('s', 'Image uploaded successfully'));
            }
        }*/

    }

    function load_beneficiaryTypess_Master_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $data['beneficiary'] = $this->db->query("SELECT * FROM srp_erp_ngo_benificiarytypes WHERE companyID = {$companyID} ORDER BY beneficiaryTypeID DESC ")->result_array();
        $this->load->view('system/operationNgo/ajax/load_beneficeryTypes_master', $data);
    }

    function save_beneficiaryTypes_header()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_beneficiaryTypes_header());
        }
    }

    function delete_beneficiaryTypes()
    {
        echo json_encode($this->OperationNgo_model->delete_beneficiaryTypes());
    }

    function load_beneficiaryTypes_header()
    {
        echo json_encode($this->OperationNgo_model->load_beneficiaryTypes_header());
    }

    function load_ngo_area_setupDetail()
    {
        echo json_encode($this->OperationNgo_model->load_ngo_area_setupDetail());
    }

    function beneficiary_system_code_generator()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        echo json_encode($this->OperationNgo_model->beneficiary_system_code_generator($benificiaryID));
    }

    function fetch_beneficiary_relate_search()
    {
        echo json_encode($this->OperationNgo_model->fetch_beneficiary_relate_search());
    }

    function load_beneficiary_print_view()
    {
        $benificiaryID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('benificiaryID') ?? '');
        $data['extra'] = $this->OperationNgo_model->load_beneficiary_header_helpNest($benificiaryID);
        $data['benImages'] = $this->OperationNgo_model->fetch_beneficiary_multiple_images($benificiaryID);
        $data['html'] = $this->input->post('html');
        $data['approval'] = $this->input->post('approval');
        //$html = $this->load->view('system/operationNgo/ngo_beneficiary_helpnest_print', $data, true);
        $html = $this->load->view('system/operationNgo/ngo_beneficiary_helpnest_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 1);
        }
    }

    function load_damageAssesment_print_view(){

        $benificiaryID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('benificiaryID') ?? '');
        $data['extra'] = $this->OperationNgo_model->load_beneficiary_disaster_managment($benificiaryID);
        $data['benImages'] = $this->OperationNgo_model->fetch_beneficiary_multiple_images($benificiaryID);
        $data['html'] = $this->input->post('html');
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/operationNgo/ngo_beneficiary_damageAssesment_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 1);
        }
    }

    function load_projectManagement_editView()
    {
        $convertFormat = convert_date_format_sql();
        $ngoProjectID = trim($this->input->post('ngoProjectID') ?? '');

        $data['header'] = $this->db->query("SELECT pro.ngoProjectID,pro.projectName as projectName,pro.description as projectDescription,DATE_FORMAT(pro.createdDateTime,'" . $convertFormat . "') AS createdDate,DATE_FORMAT(pro.modifiedDateTime,'" . $convertFormat . "') AS modifydate,pro.createdUserName as contactCreadtedUser, CONCAT(seg.segmentCode,' - ',seg.description) as segmentFormatted,CONCAT(cha.systemAccountCode,' - ',cha.GLDescription) as glcodeFormatted,pro.projectImage FROM srp_erp_ngo_projects pro LEFT JOIN srp_erp_segment seg ON seg.segmentID = pro.segmentID LEFT JOIN srp_erp_chartofaccounts cha ON pro.revenueGLAutoID = cha.GLAutoID WHERE ngoProjectID = " . $ngoProjectID . "")->row_array();
        //echo $this->db->last_query();

        $this->load->view('system/operationNgo/ajax/load_ngo_project_edit_view', $data);
    }


    function fetch_ngo_sub_projects()
    {
        echo json_encode($this->OperationNgo_model->fetch_ngo_sub_projects());
    }

    function save_project_proposal_header()
    {


        // $this->form_validation->set_rules('projectID', 'Project', 'trim|required');
        if ($this->input->post('typepro') == 1) {
            $this->form_validation->set_rules('typepro', 'Create As', 'trim|required');
            $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required|validate_date');
            $this->form_validation->set_rules('startDate', 'Estimated Start Date', 'trim|required|validate_date');
            $this->form_validation->set_rules('endDate', 'Estimated End Date', 'trim|required|validate_date');
            $this->form_validation->set_rules('endDate', 'Estimated End Date', 'trim|required|validate_date');
            $this->form_validation->set_rules('proposalName', 'Proposal Name', 'trim|required');
            $this->form_validation->set_rules('proposalTitle', 'Proposal Title', 'trim|required');
            $this->form_validation->set_rules('province', 'Province', 'trim|required');
            $this->form_validation->set_rules('district', 'District', 'trim|required');
            $this->form_validation->set_rules('countryID', 'Country', 'trim|required');
            $this->form_validation->set_rules('division', 'Division', 'trim|required');
            //   $this->form_validation->set_rules('status', 'Status', 'trim|required');
            $this->form_validation->set_rules('contractorID', 'Contractor ', 'trim|required');
            $this->form_validation->set_rules('EstimatedDays', 'Estimated Completion Time ', 'trim|required');
            //$this->form_validation->set_rules('estimationdays', 'Estimated Completion Time ', 'trim|required');
            //$this->form_validation->set_rules('subProjectID', 'Sub Project', 'trim|required');
        } else if ($this->input->post('typepro') == 2) {
            $this->form_validation->set_rules('proposalName', 'Project Name', 'trim|required');
            $this->form_validation->set_rules('contractorIDproject', 'Contractor', 'trim|required');
            $this->form_validation->set_rules('totalprojectcost', 'Project Cost', 'trim|required');
           // $this->form_validation->set_rules('proposalTitle', 'Project Title', 'trim|required');

        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_project_proposal_header());
        }

    }

    function load_beneficery_details_view()
    {
        $data['header'] = $this->OperationNgo_model->load_project_proposal_beneficiary_details();
        $this->load->view('system/operationNgo/ajax/load-project-proposal-beneficiary-details', $data);
    }

    function load_project_proposal_donor_details_view()
    {
        $data["header"] = $this->OperationNgo_model->load_project_proposal_donor_details();
        $data["beneficiary"] = $this->OperationNgo_model->load_project_proposal_beneficiary_details();
        $this->load->view('system/operationNgo/ajax/load-project-proposal-donors-details', $data);
    }

    function check_project_proposal_details_exist()
    {
        echo json_encode($this->OperationNgo_model->load_project_proposal_beneficiary_details());
    }


    function load_project_proposal_header()
    {
        echo json_encode($this->OperationNgo_model->load_project_proposal_header());
    }

    function load_project_proposal_master_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $user = $this->common_data['current_userID'];
        $text = trim($this->input->post('q') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $filter = "";
        if ($text != '') {
            $filter = " AND (ppm.documentSystemCode LIKE '%$text%' OR ppm.proposalName LIKE '%$text%' )";
        }
        $convertFormat = convert_date_format_sql();

        $data['master'] = $this->db->query("SELECT ppm.proposalID,ppm.closedYN,ppm.proposalStageID as proposalStageID,ppm.status as status,ppm.status as proposalstatus,ppm.isConvertedToProject,ppm.projectID,ppm.documentSystemCode,pown.isEdit as isEdit,pown.isAdd as isAdd,ppm.proposalName,DATE_FORMAT(ppm.DocumentDate,'{$convertFormat}') AS DocumentDate,ppm.confirmedYN,ppm.zakatDefault,ppm.approvedYN as approvedYN,ppm.createdUserID,COUNT(bencount.proposalBeneficiaryID) as Beneficiarycount, currencyMaster.CurrencyCode
 FROM srp_erp_ngo_projectproposals ppm  
 LEFT JOIN srp_erp_currencymaster currencyMaster ON currencyMaster.CurrencyID = ppm.transactionCurrencyID
 LEFT JOIN (select *
				from 
				srp_erp_ngo_projectowners
				where
				employeeID = $user
				AND companyID = $companyID
			) pown on pown.projectID = ppm.projectID
			 
			 LEFT JOIN ( SELECT *  FROM srp_erp_ngo_projectproposalbeneficiaries WHERE companyID = $companyID ) bencount ON bencount.proposalID = ppm.proposalID 
			 WHERE ppm.companyID={$companyID}  AND IF(isAdd = 0 AND isEdit = 1 And (confirmedYN = 0 or confirmedYN = 2 or confirmedYN = 3),0,1) AND (isAdd = 1 AND isEdit = 1 or isAdd = 0 AND isEdit = 1 or isAdd = 1 AND isEdit = 0) AND pown.employeeID = $user 
	AND pown.companyID = $companyID  AND ppm.type = 1 $filter GROUP BY
	ppm.proposalID ORDER BY ppm.proposalID DESC")->result_array();
        //$data['usertype'] = $this->db->query("SELECT pown.isEdit FROM srp_erp_ngo_projectproposals pp LEFT JOIN srp_erp_ngo_projectowners pown on pown.projectID = pp.projectID where type = 1 AND pown.employeeID = $user AND pown.companyID =$companyID  GROUP BY pown.projectID")->row_array();

        $this->load->view('system/operationNgo/ajax/load_project_proposal_master_view', $data);
    }

    function fetch_project_proposal_beneficiary()
    {
        $projectID = trim($this->input->post('projectID') ?? '');
        $proposalID = trim($this->input->post('proposalID') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $country = trim($this->input->post('countryID') ?? '');
        $division = trim($this->input->post('division') ?? '');
        $subdivision = trim($this->input->post('subDivision') ?? '');
        $this->datatables->select('bm.systemCode as systemCode,bm.nameWithInitials as name,bm.benificiaryID as benificiaryID,bm.totalSqFt as totalSqFt,bm.totalCost as totalCost,bm.totalEstimatedValue as totalEstimatedValue', false)
            ->from('srp_erp_ngo_beneficiarymaster as bm')
            ->where('companyID', $companyID)
            ->where('projectID', $projectID)
            ->where('countryID', $country)
            ->where('division', $division)
            ->where('subDivision', $subdivision)
            ->where('confirmedYN', 1);
        $this->datatables->where('NOT EXISTS(SELECT proposalBeneficiaryID,beneficiaryID FROM srp_erp_ngo_projectproposalbeneficiaries WHERE srp_erp_ngo_projectproposalbeneficiaries.beneficiaryID = bm.benificiaryID AND companyID =' . current_companyID() . ' AND proposalID =  ' . $proposalID . ')');
        $this->datatables->add_column('estimatedvalue', '<div style="text-align: center;">
<input id="estimatedvalue_$1" name="totalestimatedvalue[]" type="text" class="form-control" value ="$2" ><label for="checkbox">&nbsp;</label> </div>', 'benificiaryID,totalEstimatedValue');
        $this->datatables->add_column('totalsqftadd', '<div style="text-align: center;">
<input id="totalsqft_$1" name="totalsqft[]" type="text" class="form-control" value ="$2" ><label for="checkbox">&nbsp;</label> </div>', 'benificiaryID,totalSqFt');

        $this->datatables->add_column('totalcostadd', '<div style="text-align: center;">
<input id="totalcost_$1" name="totalcost[]" type="text" class="form-control" value ="$2" > </div>', 'benificiaryID,totalCost');

        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1"  data-value="ischeacked" onchange="changeMandatory(this)" type="checkbox" class="columnSelected "  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div><input type="hidden" name="is_cheacked[]" class="changestatus-ischeacked " value= "0" ><input type="hidden" name="is_cheacked_benid[]" class="beneficiaryid-ischeacked" value="$1"> ', 'benificiaryID');
        echo $this->datatables->generate();


    }

    function fetch_project_proposal_donors()
    {
        $proposalID = trim($this->input->post('proposalID') ?? '');
        $this->datatables->select('donor.name as donorName,donor.contactID as contactID', false)
            ->from('srp_erp_ngo_donors as donor')
            ->where('companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT proposalDonourID,donorID FROM srp_erp_ngo_projectproposaldonors WHERE srp_erp_ngo_projectproposaldonors.donorID = donor.contactID AND companyID =' . current_companyID() . ' AND proposalID =  ' . $proposalID . ')');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectDonors_$1" onclick="" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'contactID');
        echo $this->datatables->generate();
    }

    function fetch_project_proposal_donors_email_send()
    {
        $proposalID = trim($this->input->post('proposalID') ?? '');
        $this->datatables->select('srp_erp_ngo_donors.name as donorName,srp_erp_ngo_donors.contactID as contactID', false)
            ->from('srp_erp_ngo_projectproposaldonors')
            ->join('srp_erp_ngo_donors', 'srp_erp_ngo_donors.contactID = srp_erp_ngo_projectproposaldonors.donorID')
            ->where('srp_erp_ngo_projectproposaldonors.companyID', current_companyID())
            ->where('srp_erp_ngo_projectproposaldonors.proposalID', $proposalID)
            ->where('srp_erp_ngo_projectproposaldonors.sendEmail', 0);
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectDonorsEmail_$1" onclick="" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'contactID');
        echo $this->datatables->generate();
    }

    function assign_beneficiary_for_project_proposal()
    {

        $this->form_validation->set_rules('selectedItemsSync[]', 'Beneficiary', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->assign_beneficiary_for_project_proposal());
        }

    }

    function assign_donors_for_project_proposal()
    {
        $this->form_validation->set_rules('selectedDonorsSync[]', 'Donor', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->assign_donors_for_project_proposal());
        }

    }

    function send_project_proposal_email()
    {
        $this->form_validation->set_rules('selectedDonorsEmailSync[]', 'Donor', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->send_project_proposal_email());
        }

    }

    function delete_project_proposal()
    {
        echo json_encode($this->OperationNgo_model->delete_project_proposal());
    }

    function load_project_proposal_print_pdf()
    {
        $proposalID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('proposalID') ?? '');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("SELECT pro.projectImage,pp.proposalName as ppProposalName,pro.projectName as proProjectName,DATE_FORMAT(pp.DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(pp.startDate,'{$convertFormat}') AS ppStartDate,DATE_FORMAT(pp.endDate,'{$convertFormat}') AS ppEndDate,DATE_FORMAT(pp.DocumentDate, '%M %Y') as subprojectName,pp.detailDescription as ppDetailDescription,pp.projectSummary as ppProjectSummary,pp.totalNumberofHouses as ppTotalNumberofHouses,pp.floorArea as ppFloorArea,pp.costofhouse as ppCostofhouse,pp.additionalCost as ppAdditionalCost,pp.EstimatedDays as ppEstimatedDays,pp.proposalTitle as ppProposalTitle,pp.processDescription as ppProcessDescription,con.supplierName as contractorName,ca.GLDescription as caBankAccName,ca.bankName as caBankName,ca.bankAccountNumber as caBankAccountNumber FROM srp_erp_ngo_projectproposals pp JOIN srp_erp_ngo_projects pro ON pp.projectID = pro.ngoProjectID LEFT JOIN srp_erp_suppliermaster con ON pp.contractorID = con.supplierAutoID LEFT JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = pp.bankGLAutoID WHERE pp.proposalID = $proposalID  ")->row_array();

        $data['detail'] = $this->db->query("SELECT ppb.beneficiaryID as ppbBeneficiaryID,ppb.totalSqFt as proposalbentotalSqFt,ppb.totalEstimatedValue as proposaltotalEstimatedValue,DATE_FORMAT(bm.registeredDate,'{$convertFormat}') AS bmRegisteredDate,DATE_FORMAT(bm.dateOfBirth,'{$convertFormat}') AS bmDateOfBirth,bm.nameWithInitials as bmNameWithInitials,bm.systemCode as bmSystemCode, CASE bm.ownLandAvailable WHEN 1 THEN 'Yes' WHEN 2 THEN 'No' END as bmOwnLandAvailable,bm.NIC as bmNIC,bm.totalEstimatedValue as totalEstimatedValue,bm.familyMembersDetail as bmFamilyMembersDetail,bm.reasoninBrief as bmReasoninBrief,bm.totalSqFt as bmTotalSqFt,bm.totalCost as bmTotalCost,bm.helpAndNestImage as bmHelpAndNestImage,bm.helpAndNestImage1 as bmHelpAndNestImage1,bm.ownLandAvailableComments as bmOwnLandAvailableComments FROM srp_erp_ngo_projectproposalbeneficiaries ppb LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON ppb.beneficiaryID = bm.benificiaryID WHERE proposalID = $proposalID ")->result_array();

        $data['images'] = $this->db->query("SELECT imageType,imageName FROM srp_erp_ngo_projectproposalimages WHERE ngoProposalID = $proposalID ")->result_array();

        $data['moto'] = $this->db->query("SELECT companyPrintTagline FROM srp_erp_company WHERE company_id = {$companyID}")->row_array();
        $data['beneficiaryhpnimag'] = $this->db->query("SELECT companyPrintTagline FROM srp_erp_company WHERE company_id = {$companyID}")->row_array();

        $data['proposalID'] = $proposalID;

        $data['output'] = 'view';

        $this->load->view('system/operationNgo/ngo_beneficiary_helpnest_print_all', $data);
    }

    function delete_project_proposal_detail()
    {
        echo json_encode($this->OperationNgo_model->delete_project_proposal_detail());
    }

    function project_proposal_confirmation()
    {
        echo json_encode($this->OperationNgo_model->project_proposal_confirmation());
    }

    function delete_project_proposal_donors_detail()
    {
        echo json_encode($this->OperationNgo_model->delete_project_proposal_donors_detail());
    }

    function referback_project_proposal()
    {
        echo json_encode($this->OperationNgo_model->referback_project_proposal());
    }

    function load_project_all_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $ngoProjectID = trim($this->input->post('ngoProjectID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 2  AND documentAutoID = " . $ngoProjectID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_attachments');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load_ngo_contact_attachments', $data);
    }

    function project_image_upload()
    {
        $this->form_validation->set_rules('ngoProjectID', 'Project ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->project_image_upload());
        }
    }

    function get_donor_commitment_status_report()
    {
        $date_format_policy = date_format_policy();
        $date = trim($this->input->post('from') ?? '');
        $contactID = $this->input->post("contactID");
        $companyID = $this->common_data['company_data']['company_id'];
        $this->form_validation->set_rules('reportType', 'Type', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $format_date = null;
            if (isset($date) && !empty($date)) {
                $format_date = input_format_date($date, $date_format_policy);
            }
            $whereDate = "";
            if (!empty($date)) {
                $whereDate = "AND documentDate <= '" . $format_date . "' ";
            }

            $whereDonors = "";
            if (!empty($contactID)) {
                $whereDonors = "AND cm.donorsID IN(" . join(',', $contactID) . ")";
            }

            $data['details'] = $this->db->query("SELECT cm.commitmentAutoId,don.name AS donorName,cmd.commitmentTotal AS commitmentTotal,cm.transactionCurrencyID,cm.transactionCurrency,cm.donorsID,collectionM.collectionTotal,cm.transactionCurrencyDecimalPlaces,cm.documentDate FROM srp_erp_ngo_commitmentmasters cm JOIN srp_erp_ngo_donors don ON cm.donorsID = don.contactID LEFT JOIN (SELECT SUM(transactionAmount) AS commitmentTotal,srp_erp_ngo_commitmentdetails.commitmentAutoId,srp_erp_ngo_commitmentmasters.transactionCurrencyID,srp_erp_ngo_commitmentmasters.confirmedYN,srp_erp_ngo_commitmentmasters.documentDate FROM srp_erp_ngo_commitmentdetails JOIN srp_erp_ngo_commitmentmasters ON srp_erp_ngo_commitmentdetails.commitmentAutoId = srp_erp_ngo_commitmentmasters.commitmentAutoId AND srp_erp_ngo_commitmentmasters.confirmedYN  = 1 $whereDate GROUP BY donorsID, transactionCurrencyID) cmd ON cmd.commitmentAutoId = cm.commitmentAutoId
LEFT JOIN (
	SELECT
		COALESCE(SUM(transactionAmount),0) AS collectionTotal,
		srp_erp_ngo_donorcollectiondetails.collectionAutoId,
		srp_erp_ngo_donorcollectionmaster.transactionCurrencyID,
		srp_erp_ngo_donorcollectionmaster.donorsID,
		srp_erp_ngo_donorcollectionmaster.documentDate
	FROM
		srp_erp_ngo_donorcollectionmaster
	LEFT JOIN srp_erp_ngo_donorcollectiondetails ON srp_erp_ngo_donorcollectiondetails.collectionAutoId = srp_erp_ngo_donorcollectionmaster.collectionAutoId
WHERE approvedYN = 1 $whereDate
	GROUP BY
			transactionCurrencyID,
donorsID
) collectionM ON collectionM.donorsID = cm.donorsID
WHERE
	cm.companyID = {$companyID}
AND cm.confirmedYN = 1 $whereDonors
GROUP BY
	cm.donorsID,
	cm.transactionCurrencyID")->result_array();
            $data["type"] = "html";
            echo $html = $this->load->view('system/operationNgo/ajax/load-donor-commitment-status-report', $data, true);
        }
    }

    function get_donor_commitment_status_report_pdf()
    {
        $date_format_policy = date_format_policy();
        $date = trim($this->input->post('from') ?? '');
        $contactID = $this->input->post("contactID");
        $companyID = $this->common_data['company_data']['company_id'];
        $this->form_validation->set_rules('reportType', 'Type', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {
            $format_date = null;
            if (isset($date) && !empty($date)) {
                $format_date = input_format_date($date, $date_format_policy);
            }
            $whereDate = "";
            if (!empty($date)) {
                $whereDate = "AND documentDate <= '" . $format_date . "' ";
            }

            $whereDonors = "";
            if (!empty($contactID)) {
                $whereDonors = "AND cm.donorsID IN(" . join(',', $contactID) . ")";
            }

            $data['details'] = $this->db->query("SELECT cm.commitmentAutoId,don.name AS donorName,cmd.commitmentTotal AS commitmentTotal,cm.transactionCurrencyID,cm.transactionCurrency,cm.donorsID,collectionM.collectionTotal,cm.transactionCurrencyDecimalPlaces,cm.documentDate FROM srp_erp_ngo_commitmentmasters cm JOIN srp_erp_ngo_donors don ON cm.donorsID = don.contactID LEFT JOIN (SELECT SUM(transactionAmount) AS commitmentTotal,srp_erp_ngo_commitmentdetails.commitmentAutoId,srp_erp_ngo_commitmentmasters.transactionCurrencyID,srp_erp_ngo_commitmentmasters.confirmedYN,srp_erp_ngo_commitmentmasters.documentDate FROM srp_erp_ngo_commitmentdetails JOIN srp_erp_ngo_commitmentmasters ON srp_erp_ngo_commitmentdetails.commitmentAutoId = srp_erp_ngo_commitmentmasters.commitmentAutoId AND srp_erp_ngo_commitmentmasters.confirmedYN  = 1 $whereDate GROUP BY donorsID, transactionCurrencyID) cmd ON cmd.commitmentAutoId = cm.commitmentAutoId
LEFT JOIN (
	SELECT
		COALESCE(SUM(transactionAmount),0) AS collectionTotal,
		srp_erp_ngo_donorcollectiondetails.collectionAutoId,
		srp_erp_ngo_donorcollectionmaster.transactionCurrencyID,
		srp_erp_ngo_donorcollectionmaster.donorsID,
		srp_erp_ngo_donorcollectionmaster.documentDate
	FROM
		srp_erp_ngo_donorcollectionmaster
	LEFT JOIN srp_erp_ngo_donorcollectiondetails ON srp_erp_ngo_donorcollectiondetails.collectionAutoId = srp_erp_ngo_donorcollectionmaster.collectionAutoId
WHERE approvedYN = 1 $whereDate
	GROUP BY
			transactionCurrencyID,
donorsID
) collectionM ON collectionM.donorsID = cm.donorsID
WHERE
	cm.companyID = {$companyID}
AND cm.confirmedYN = 1 $whereDonors
GROUP BY
	cm.donorsID,
	cm.transactionCurrencyID")->result_array();
            $data["type"] = "pdf";
            $html = $this->load->view('system/operationNgo/ajax/load-donor-commitment-status-report', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    function save_ngo_contractor()
    {
        $this->form_validation->set_rules('contractorName', 'Contractor Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_ngo_contractor());
        }
    }


    function load_project_image_view()
    {
        $ngoProposalID = trim($this->input->post('proposalID') ?? '');
        $data['docDet'] = $this->OperationNgo_model->load_project_images();
        $data['status'] = $this->db->query("select approvedYN,type,confirmedYN FROM srp_erp_ngo_projectproposals WHERE proposalID = {$ngoProposalID} ")->row_array();
        $data['ngoProposalID'] = $ngoProposalID;
        $this->load->view('system/operationNgo/ajax/load_project_image_view', $data);
    }

    function load_project_attachment_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $ngoProposalID = trim($this->input->post('proposalID') ?? '');
        $data['ngoProposalID'] = $ngoProposalID;
        $where = "companyID = " . $companyid . " AND documentID = '6' AND documentAutoID = " . $ngoProposalID . "";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_attachments');
        $this->db->where($where);
        $data['docDet'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load_attachment_view', $data);
    }

    function save_project_proposal_image()
    {
        $this->form_validation->set_rules('document', 'Document', 'trim|required');
        $this->form_validation->set_rules('ngoProposalID', 'Proposal ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_project_proposal_image());
        }
    }

    function get_donor_commitment_drilldown_report()
    {
        $type = trim($this->input->post('type') ?? '');
        if ($type == 1) {
            $data["code"] = 'CMT';
            $data["details"] = $this->OperationNgo_model->get_total_commitments_drilldown();
        } else {
            $data["code"] = 'DC';
            $data["details"] = $this->OperationNgo_model->get_total_collection_drilldown();
        }
        echo $html = $this->load->view('system/operationNgo/ajax/load-donor-commitment-status-drilldown-report', $data, true);

    }

    function delete_project_proposal_image()
    {
        $this->form_validation->set_rules('ngoProposalImageID', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_project_proposal_image());
        }
    }

    function fetch_province_based_countryDropdown_project_proposal()
    {
        $data_arr = array();
        $countyID = $this->input->post('countyID');
        $province = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE countyID = {$countyID} AND type = 1")->result_array();
        $data_arr = array('' => 'Select a Province');
        if (!empty($province)) {
            foreach ($province as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('province', $data_arr, '', 'class="form-control select2" id="province" onchange="loadcountry_District(this.value)"');
    }

    function fetch_province_based_districtDropdown_project_proposal()
    {
        $data_arr = array();
        $masterID = $this->input->post('masterID');
        if (!empty($masterID)) {
            $district = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 2")->result_array();
        }
        $data_arr = array('' => 'Select a District');
        if (!empty($district)) {
            foreach ($district as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('district', $data_arr, '', 'class="form-control select2" id="district" onchange="loadcountry_Division(this.value)"');
    }

    function fetch_division_based_districtDropdown_project_proposal()
    {
        $data_arr = array();
        $masterID = $this->input->post('masterID');
        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 3")->result_array();
        }
        $data_arr = array('' => 'Select a Division');
        if (!empty($division)) {
            foreach ($division as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('division', $data_arr, '', 'class="form-control select2" id="division" onchange="loadcountry_sub_Divisions(this.value)"');
    }

    function fetch_sub_division_based_divisionDropdown_project()
    {
        $data_arr = array();
        $divisions = $this->input->post('masterID');
        if (!empty($divisions)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$divisions} AND type = 4")->result_array();
        }
        $data_arr = array('' => 'Select a Mahalla');
        if (!empty($division)) {
            foreach ($division as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('subDivision', $data_arr, '', 'class="form-control select2" id="subDivision" onchange="fetch_sub_divisions()"');
    }

    function fetch_beneficiary_province()
    {
        echo json_encode($this->OperationNgo_model->beneficiary_province());
    }

    function fetch_beneficiary_province_area()
    {
        echo json_encode($this->OperationNgo_model->beneficiary_area());
    }

    function fetch_beneficiary_division()
    {
        echo json_encode($this->OperationNgo_model->beneficiary_division());
    }

    function fetch_beneficiary_sub_division()
    {
        echo json_encode($this->OperationNgo_model->beneficiary_sub_division());
    }

    function load_beneficiary_multiple_image_view()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        $docDet = $this->OperationNgo_model->load_beneficiary_multiple_images();
        //echo '<pre>';print_r($docDet); echo '</pre>'; die();
        $data['docDet'] = $docDet;
        $data['benificiaryID'] = $benificiaryID;
        $this->load->view('system/operationNgo/ajax/load_beneficiary_multiple_image_view', $data);
    }

    function upload_beneficiary_multiple_image()
    {
        //$this->form_validation->set_rules('document', 'Document', 'trim|required');
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->upload_beneficiary_multiple_image());
        }
    }

    function delete_beneficiary_multiple_image()
    {
        $this->form_validation->set_rules('beneficiaryImageID', 'beneficiary Image ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_beneficiary_multiple_image());
        }
    }

    function update_beneficiary_multiple_image()
    {
        $this->form_validation->set_rules('beneficiaryImageID', 'Beneficiary Image ID', 'trim|required');
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_beneficiary_multiple_image());
        }
    }

    function donor_donations_view()
    {
        $contactid = trim($this->input->post('contactID') ?? '');
        $company_id = $this->common_data['company_data']['company_id'];
        $data['details'] = $this->db->query("SELECT
                cm.commitmentAutoId,
                cm.donorsID,
                '1' AS transactionType,
                don.NAME AS donorName,
                cm.documentDate,
                cm.documentsystemcode,
                prj.projectName,
                prj.ngoProjectID,
                cm.transactionCurrencyID,
                cm.transactionCurrency,
                ROUND(sum(cmd.transactionAmount), cm.transactionCurrencyDecimalPlaces) AS commitmentTotal,
                ROUND(sum(ifnull(collectionD.collectionAmount, 0)), cm.transactionCurrencyDecimalPlaces) AS collectionAmount,
                cm.transactionCurrencyDecimalPlaces,
                cmd.commitmentDetailAutoID AS collectiondetail
            FROM
                srp_erp_ngo_commitmentmasters cm
                LEFT JOIN srp_erp_ngo_donors don ON cm.donorsID = don.contactID
                LEFT JOIN srp_erp_ngo_commitmentdetails cmd ON cmd.commitmentAutoId = cm.commitmentAutoId
                LEFT JOIN ( SELECT collectionAutoId, commitmentDetailID, sum( transactionAmount ) AS collectionAmount FROM srp_erp_ngo_donorcollectiondetails GROUP BY commitmentDetailID ) AS collectionD ON collectionD.commitmentDetailID = cmd.commitmentDetailAutoID
                LEFT JOIN srp_erp_ngo_donorcollectionmaster collectionM ON collectionD.collectionAutoId = collectionM.collectionAutoId
                LEFT JOIN srp_erp_ngo_projects prj ON cmd.projectID = prj.ngoProjectID 
            WHERE
                cm.companyID = $company_id 
                AND cmd.type = 1 
                AND cm.confirmedYN = 1 
                AND cm.donorsID = $contactid 
            GROUP BY
                cmd.commitmentAutoId,
                cmd.projectID UNION
            SELECT
                collectionD.collectionAutoId,
                collectionM.donorsID,
                '2' AS transactionType,
                don.NAME AS donorName,
                collectionM.documentDate,
                collectionM.documentsystemcode,
                prj.projectName,
                prj.ngoProjectID,
                collectionM.transactionCurrencyID,
                collectionM.transactionCurrency,
                '0' AS commitmentTotal,
                sum( collectionD.transactionAmount ) AS collectionAmount,
                collectionM.transactionCurrencyDecimalPlaces,
                collectionD.collectionDetailAutoID AS collectiondetail
            FROM
                srp_erp_ngo_donorcollectiondetails collectionD
                LEFT JOIN srp_erp_ngo_donorcollectionmaster collectionM ON collectionD.collectionAutoId = collectionM.collectionAutoId
                LEFT JOIN srp_erp_ngo_donors don ON collectionM.donorsID = don.contactID
                LEFT JOIN srp_erp_ngo_projects prj ON collectionD.projectID = prj.ngoProjectID 
            WHERE
                collectionD.companyID = $company_id 
                AND collectionD.type = 1 
                AND collectionM.approvedYN = 1 
                AND collectionM.donorsID = $contactid 
                AND ( collectionD.commitmentAutoID = 0 OR collectionD.commitmentAutoID IS NULL ) 
            GROUP BY
                collectionD.collectionAutoId,
                collectionD.projectID")->result_array();


        $data['item'] = $this->db->query("SELECT cm.commitmentAutoId,
                cm.donorsID,
                '1' AS transactionType,
                don.NAME AS donorName,
                cm.documentDate,
                cm.documentsystemcode,
                prj.projectName,
                prj.ngoProjectID,
                cm.transactionCurrencyID,
                cm.transactionCurrency,
                sum( cmd.itemQty ) AS itemQty,
                cm.transactionCurrencyDecimalPlaces,
                cmd.commitmentDetailAutoID AS collectiondetail
                FROM
                    srp_erp_ngo_commitmentmasters cm
                    LEFT JOIN srp_erp_ngo_donors don ON cm.donorsID = don.contactID
                    LEFT JOIN srp_erp_ngo_commitmentdetails cmd ON cmd.commitmentAutoId = cm.commitmentAutoId
                    LEFT JOIN ( SELECT collectionAutoId, commitmentDetailID, sum( transactionAmount ) AS collectionAmount FROM srp_erp_ngo_donorcollectiondetails GROUP BY commitmentDetailID ) AS collectionD ON collectionD.commitmentDetailID = cmd.commitmentDetailAutoID
                    LEFT JOIN srp_erp_ngo_donorcollectionmaster collectionM ON collectionD.collectionAutoId = collectionM.collectionAutoId
                    LEFT JOIN srp_erp_ngo_projects prj ON cmd.projectID = prj.ngoProjectID 
                WHERE
                    cm.companyID = $company_id
                    AND cmd.type = 2 
                    AND cm.confirmedYN = 1 
                    AND cm.donorsID =$contactid
                    
                GROUP BY
                    cmd.commitmentAutoId,
                    cmd.projectID UNION
                SELECT
                    collectionD.collectionAutoId,
                    collectionM.donorsID,
                    '2' AS transactionType,
                    don.NAME AS donorName,
                    collectionM.documentDate,
                    collectionM.documentsystemcode,
                    prj.projectName,
                    prj.ngoProjectID,
                    collectionM.transactionCurrencyID,
                    collectionM.transactionCurrency,
                    '0' AS commitmentTotal,
                    collectionM.transactionCurrencyDecimalPlaces,
                    collectionD.collectionDetailAutoID AS collectiondetail
                FROM
                    srp_erp_ngo_donorcollectiondetails collectionD
                    LEFT JOIN srp_erp_ngo_donorcollectionmaster collectionM ON collectionD.collectionAutoId = collectionM.collectionAutoId
                    LEFT JOIN srp_erp_ngo_donors don ON collectionM.donorsID = don.contactID
                    LEFT JOIN srp_erp_ngo_projects prj ON collectionD.projectID = prj.ngoProjectID 
                WHERE
                    collectionD.companyID = $company_id 
                    AND collectionD.type = 2
                    AND collectionM.approvedYN = 1 
                    AND collectionM.donorsID =$contactid
                    AND ( collectionD.commitmentAutoID = 0 OR collectionD.commitmentAutoID IS NULL ) 
                GROUP BY
                    collectionD.collectionAutoId,
                    collectionD.projectID")->result_array();


        $data['cash_collected'] = $this->db->query("SELECT
                cm.commitmentAutoId,
                cm.documentSystemCode as commitmentcode,
                dcm.collectionAutoId AS autoID,
                dcm.documentSystemCode,
                sum(dcd.transactionAmount) AS transactionAmount,
                dcm.transactionCurrencyID,
                dcm.transactionCurrency,
                dcm.donorsID,
                dcm.transactionCurrencyDecimalPlaces,
                dcm.documentSystemCode,
                don.NAME AS donorName,
                prj.projectName,
                dcd.type

                FROM
                    srp_erp_ngo_donorcollectionmaster dcm
                    JOIN srp_erp_ngo_donors don ON dcm.donorsID = don.contactID
                    join srp_erp_ngo_donorcollectiondetails dcd on dcm.collectionAutoId=dcd.collectionAutoId
                    LEFT join srp_erp_ngo_commitmentmasters cm on cm.commitmentAutoId=dcd.commitmentAutoID
                    LEFT JOIN srp_erp_ngo_projects prj ON dcd.projectID = prj.ngoProjectID 
                WHERE
                    dcm.companyID = $company_id 
                    AND dcm.donorsID = $contactid 
                    AND dcm.approvedYN = 1
                    AND dcd.type = 1
                    GROUP BY
                dcd.collectionAutoId,dcd.commitmentAutoID,dcd.projectID")->result_array();


        $this->load->view('system/operationNgo/beneficiary_donations_view', $data);
    }

    function get_donor_commitment_drilldown()
    {
        $projectid = trim($this->input->post('ngoProjectID') ?? '');
        $commitmentAutoId = trim($this->input->post('commitmentAutoId') ?? '');

        $data['cash'] = $this->db->query("SELECT
    commitmentDetailAutoID,
    commitmentAutoId,
		cm.documentSystemCode,
		cm.documentDate,
    projectID,
    type,
    srp_erp_ngo_commitmentdetails.description,
    unittransactionAmount,
    transactionAmount,
    projectName 
FROM
    srp_erp_ngo_commitmentdetails
    LEFT JOIN srp_erp_ngo_projects ON ngoProjectID = projectID 
		LEFT JOIN ( SELECT documentSystemCode,commitmentAutoId as comautoid,documentDate FROM srp_erp_ngo_commitmentmasters) AS cm ON  cm.comautoid = srp_erp_ngo_commitmentdetails.commitmentAutoId
WHERE
    commitmentAutoId = $commitmentAutoId 
    AND type =1
		AND projectID =$projectid")->result_array();

        $this->load->view('system/operationNgo/donor-commitment-view', $data);

    }

    function save_project_proposal_attachments()
    {
        $this->form_validation->set_rules('document', 'Document', 'trim|required');
        $this->form_validation->set_rules('ngoProposalID', 'Proposal ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_project_proposal_attachments());
        }
    }

    function delete_project_proposal_attachment()
    {
        $this->form_validation->set_rules('attachmentID', 'Attachment ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_project_proposal_attachment());
        }
    }

    function update_donors_issubmited_status()
    {
        // $this->form_validation->set_rules('donorID', 'Donors ID', 'trim|required');
        $this->form_validation->set_rules('proposaldonor', 'Proposal ID', 'trim|required');
        //  $this->form_validation->set_rules('status', 'Status', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_donors_is_submited_status());
        }
    }

    function update_donors_isapproved_status()
    {
        // $this->form_validation->set_rules('donorID', 'Donors ID', 'trim|required');
        $this->form_validation->set_rules('proposalID', 'Proposal ID', 'trim|required');
        // $this->form_validation->set_rules('statusapproved', 'Status', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_donors_is_approved_status());
        }
    }

    function update_donors_commited_amt()
    {
        $this->form_validation->set_rules('donorID', 'Donors ID', 'trim|required');
        $this->form_validation->set_rules('proposalID', 'Proposal ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_donors_commited_amt());
        }
    }

    function fetch_benificaries_donors()
    {
        $proposalid = $this->input->post('proposalID');
        $this->datatables->select('ppbs.proposalBeneficiaryID as proposalBeneficiaryID,ppbs.proposalID as proposalID,ppbs.beneficiaryID as beneficiaryIDs,bm.benificiaryID,bm.fullName as fullName,bm.systemCode as systemCode,bm.nameWithInitials as nameWithInitials', false)
            ->from('srp_erp_ngo_projectproposalbeneficiaries as ppbs')
            ->join('srp_erp_ngo_beneficiarymaster as bm', 'bm.benificiaryID = ppbs.beneficiaryID', 'left')
            ->where('ppbs.proposalID', $proposalid)
            ->where('ppbs.isQualified', 1)
            ->where('ppbs.companyID', current_companyID());
        $this->datatables->where('ppbs.beneficiaryID NOT in(SELECT beneficiaryID FROM srp_erp_ngo_projectproposaldonorbeneficiaries WHERE srp_erp_ngo_projectproposaldonorbeneficiaries.proposalID = ppbs.proposalID AND companyID =' . current_companyID() . ' AND donorID =  ' . $this->input->post('donorID') . ')');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="" type="checkbox" class="columnSelected donor"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'beneficiaryIDs');
        echo $this->datatables->generate();
    }

    function add_donorbeneficiary()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Beneficiary', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->OperationNgo_model->add_donor_beneficiary());
        }

    }

    function fetch_savedbeneficiaries()
    {
        $proposalid = $this->input->post('proposalID');
        $donorid = $this->input->post('donorID');

        $this->datatables->select('ngoproben.beneficiaryDonorID as beneficiaryDonorID,ngoproben.beneficiaryDonorID as beneficiaryDonorID,bm.benificiaryID as beneficiaryID,bm.nameWithInitials as nameWithInitials,bm.systemCode as systemCode');
        $this->datatables->join('srp_erp_ngo_beneficiarymaster as bm', 'bm.benificiaryID = ngoproben.beneficiaryID', 'left');
        $this->datatables->from('srp_erp_ngo_projectproposaldonorbeneficiaries ngoproben');
        $this->datatables->where('proposalID', $proposalid);
        $this->datatables->where('donorID', $donorid);
        $this->datatables->add_column('edit', '<span class="pull-right"><a href="#" onclick="delete_donor_assign_beneficiaries($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;', 'beneficiaryDonorID');
        echo $this->datatables->generate();

    }

    function delete_assign_beneficiaries()
    {
        echo json_encode($this->OperationNgo_model->delete_assign_beneficiaries());
    }

    function update_donors_date()
    {
        $this->form_validation->set_rules('donor[]', 'Donors ID', 'trim|required');
        $this->form_validation->set_rules('proposaldonor', 'Proposal ID', 'trim|required');
        $this->form_validation->set_rules('submiteddate[]', 'Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_date());
        }
    }

    function update_donors_date_approved()
    {
        $this->form_validation->set_rules('donor[]', 'Donors ID', 'trim|required');
        $this->form_validation->set_rules('proposaldonor', 'Proposal ID', 'trim|required');
        $this->form_validation->set_rules('approveddate[]', 'Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_date_approved());
        }
    }

    function project_proposal_table_approval()
    {
        $companyID = current_companyID();

        $this->datatables->select("documentApprovedID,proposalName,projectName,projectID,approvedYN,approvalLevelID,proposalID,confirmedYN,documentCode,documentDate,documentSystemCode");
        $this->datatables->from("(SELECT documentApprovedID,srp_erp_ngo_projectproposals.approvedYN,approvalLevelID, srp_erp_ngo_projectproposals.proposalName,srp_erp_ngo_projectproposals.projectID,srp_erp_documentapproved.documentCode,srp_erp_ngo_projectproposals.documentID, confirmedYN,srp_erp_ngo_projectproposals.proposalID,srp_erp_ngo_projectproposals.documentSystemCode,srp_erp_ngo_projectproposals.documentDate,srp_erp_ngo_projects.projectName FROM srp_erp_ngo_projectproposals 	LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_ngo_projectproposals.proposalID
	AND approvalLevelID = currentLevelNo LEFT JOIN srp_erp_approvalusers ON levelNo = srp_erp_ngo_projectproposals.currentLevelNo LEFT JOIN srp_erp_ngo_projects ON ngoProjectID = srp_erp_ngo_projectproposals.projectID   WHERE srp_erp_documentapproved.documentID = 'PRP' AND srp_erp_approvalusers.documentID = 'PRP' AND employeeID = '{$this->common_data['current_userID']}' AND srp_erp_ngo_projectproposals.approvedYN={$this->input->post('approvedYN')} AND srp_erp_ngo_projectproposals.companyID={$companyID} AND srp_erp_ngo_projectproposals.type = 1 ORDER BY proposalID DESC )t");
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "PRP", proposalID)');
        $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
        $this->datatables->add_column('detail', '<b>Project Description : </b> $1 <b> <br>Project Name : </b> $2 <b> <br>Document Date : </b> $3', 'proposalName,projectName,documentDate');
        $this->datatables->add_column('edit', '$1', 'projectproposal_action(proposalID,approvalLevelID,approvedYN,documentApprovedID)');
        echo $this->datatables->generate();
    }

    function load_project_proposal_confirmation()
    {
        $proposalID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('proposalID') ?? '');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("SELECT pro.projectImage,pp.proposalID as proposalID,pp.proposalName as ppProposalName,pro.projectName as proProjectName,pp.approvedbyEmpName,pp.approvedDate,DATE_FORMAT(pp.DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(pp.startDate,'{$convertFormat}') AS ppStartDate,DATE_FORMAT(pp.endDate,'{$convertFormat}') AS ppEndDate,DATE_FORMAT(pp.DocumentDate, '%M %Y') as subprojectName,pp.detailDescription as ppDetailDescription,pp.projectSummary as ppProjectSummary,pp.approvedYN as approvedYN,pp.totalNumberofHouses as ppTotalNumberofHouses,pp.floorArea as ppFloorArea,pp.costofhouse as ppCostofhouse,pp.additionalCost as ppAdditionalCost,pp.EstimatedDays as ppEstimatedDays,pp.proposalTitle as ppProposalTitle,pp.processDescription as ppProcessDescription,con.supplierName as contractorName,ca.GLDescription as caBankAccName,ca.bankName as caBankName,ca.bankAccountNumber as caBankAccountNumber FROM srp_erp_ngo_projectproposals pp JOIN srp_erp_ngo_projects pro ON pp.projectID = pro.ngoProjectID LEFT JOIN srp_erp_suppliermaster con ON pp.contractorID = con.supplierAutoID LEFT JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = pp.bankGLAutoID WHERE pp.proposalID = $proposalID  ")->row_array();
        $data['detail'] = $this->db->query("SELECT ppb.beneficiaryID as ppbBeneficiaryID,DATE_FORMAT(bm.registeredDate,'{$convertFormat}') AS bmRegisteredDate,DATE_FORMAT(bm.dateOfBirth,'{$convertFormat}') AS bmDateOfBirth,bm.nameWithInitials as bmNameWithInitials,bm.systemCode as bmSystemCode, CASE bm.ownLandAvailable WHEN 1 THEN 'Yes' WHEN 2 THEN 'No' END as bmOwnLandAvailable,bm.NIC as bmNIC,bm.familyMembersDetail as bmFamilyMembersDetail,bm.reasoninBrief as bmReasoninBrief,ppb.totalSqFt as bmTotalSqFt,ppb.totalCost as bmTotalCost,ppb.totalEstimatedValue as totalEstimatedValue,bm.helpAndNestImage as bmHelpAndNestImage,bm.helpAndNestImage1 as bmHelpAndNestImage1,bm.ownLandAvailableComments as bmOwnLandAvailableComments FROM srp_erp_ngo_projectproposalbeneficiaries ppb LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON ppb.beneficiaryID = bm.benificiaryID WHERE proposalID = $proposalID ")->result_array();
        $data['images'] = $this->db->query("SELECT imageType,imageName FROM srp_erp_ngo_projectproposalimages WHERE ngoProposalID = $proposalID ")->result_array();
        $data['moto'] = $this->db->query("SELECT companyPrintTagline FROM srp_erp_company WHERE company_id = {$companyID}")->row_array();
        $data['proposalID'] = $proposalID;
        $html = $this->load->view('system/operationNgo/ngo_pp_confirmation', $data, TRUE);
        if ($this->input->post('html')) {
            echo $html;
        }
    }

    function save_project_proposal_approval()
    {
        $system_code = trim($this->input->post('proposalid') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('project_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'PRP', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('proposalID');
                $this->db->where('proposalID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_ngo_projectproposals');
                $pp_approved = $this->db->get()->row_array();
                if (!empty($pp_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('project_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('proposalid', 'Proposal ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->OperationNgo_model->save_project_proposal_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('proposalID');
            $this->db->where('proposalID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_ngo_projectproposals');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'DC', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('project_status', 'Donor Collection Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('proposalid', 'Donor collection ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->OperationNgo_model->save_project_proposal_approval());
                    }
                }
            }
        }
    }

    function load_human_injury_assessment_view()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');

        $data['detail'] = $this->db->query("SELECT humanInjuryID,family.name,damage.Description,estimatedAmount,hia.remarks,hia.paidAmount as paidAmount FROM srp_erp_ngo_humaninjuryassesment hia join srp_erp_ngo_beneficiaryfamilydetails family ON family.empfamilydetailsID = hia.FamilyDetailsID left join srp_erp_ngo_damagetypemaster damage ON damage.damageTypeID = hia.damageTypeID AND damageSubCategory = 'HI' WHERE hia.beneficiaryID = {$benificiaryID} ")->result_array();

        $this->load->view('system/operationNgo/ajax/load_human_injury_assessment_view', $data);
    }

    function load_house_items_assessment_view()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');

        $data['header'] = $this->db->query("SELECT damageItemCategoryID,Description FROM srp_erp_ngo_damageditemcategories")->result_array();

        $data['detail'] = $this->db->query("SELECT damageItemCategoryID,itemDamagedID,damage.Description,ida.damagedAmountClient,itemDescription,Brand,ida.assessedValue,ida.paidAmount as paidAmount FROM srp_erp_ngo_itemdamagedasssesment ida left join srp_erp_ngo_damagetypemaster damage ON damage.damageTypeID = ida.damageTypeID AND damageSubCategory = 'ID' WHERE ida.beneficiaryID = {$benificiaryID} order by damageItemCategoryID ASC ")->result_array();

        $this->load->view('system/operationNgo/ajax/load_house_items_assessment_view', $data);
    }

    function load_damage_property_assessment_view()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');

        $data['detail'] = $this->db->query("SELECT businessDamagedID,ba.Description as businessName,damage.Description as damageName,propertyValue,existingItemCondition,incomeSourceType,expectations,damageCon.Description as damageConDesc,source.description as sourceDescription,bda.paidAmount as paidAmount FROM srp_erp_ngo_businessdamagedassesment bda LEFT JOIN srp_erp_ngo_business_activity ba ON ba.businessID = bda.busineesActivityID LEFT JOIN srp_erp_ngo_damagetypemaster damageCon ON damageCon.damageTypeID = bda.damageConditionID LEFT JOIN srp_erp_ngo_damagetypemaster damage ON damage.damageTypeID = bda.damageTypeID AND damage.damageSubCategory = 'BPD' LEFT JOIN srp_erp_ngo_incomesourcemaster source ON source.incomeSourceID = bda.incomeSourceType WHERE bda.beneficiaryID = {$benificiaryID} ORDER BY businessDamagedID DESC ")->result_array();

        $this->load->view('system/operationNgo/ajax/load_property_assessment_view', $data);
    }

    function load_monthly_expenditure_header_view()
    {
        $companyID = current_companyID();
        $benificiaryID = $this->input->post('benificiaryID');

        $filter = '';
        if (!empty($benificiaryID)) {
            $filter = "AND beneficiaryID = {$benificiaryID} ";
        }

        $data['monthlyExpenditure'] = $this->db->query("SELECT mem.monthlyExpenditureID,Description,bme.amount FROM srp_erp_ngo_monthlyexpendituremaster mem LEFT JOIN srp_erp_ngo_beneficiarymonthlyexpenditure bme ON mem.monthlyExpenditureID = bme.monthlyExpenditureID $filter  WHERE mem.companyID = '{$companyID}' GROUP BY mem.monthlyExpenditureID ORDER BY mem.monthlyExpenditureID ASC")->result_array();

        $data['benificiaryID'] = $benificiaryID;

        if (!empty($benificiaryID)) {
            $data['supportAssitance'] = $this->db->query("SELECT assitanceName,Organization,year,amount FROM srp_erp_ngo_beneficiary_othersupportassistance WHERE companyID = '{$companyID}' AND beneficiaryID = {$benificiaryID} ORDER BY assistanceID ASC")->result_array();
        }

        if (!empty($benificiaryID)) {
            $data['benficeryHeader'] = $this->db->query("SELECT da_meNotes,da_meGovAssistantYN,da_meSupportReceivedYN FROM srp_erp_ngo_beneficiarymaster WHERE benificiaryID = {$benificiaryID}")->row_array();
        }

        $this->load->view('system/operationNgo/ajax/load_monthly_expenditure_view', $data);
    }

    function save_beneficiary_familyDetails_damageAssessment()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('nationality', 'Nationality', 'required|numeric');
        $this->form_validation->set_rules('relationshipType', 'Relationship', 'required|numeric');
        $this->form_validation->set_rules('familyType', 'Type', 'required|numeric');
        //$this->form_validation->set_rules('DOB', 'Date of Birth', 'trim|required');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->OperationNgo_model->save_beneficiary_familyDetails_damageAssessment());
        }
    }

    function save_beneficiary_header_house_damageAssesment()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('da_typeOfhouseDamage', 'Type of Damage', 'trim|required');
        $this->form_validation->set_rules('da_housingCondition', 'Housing Condition', 'trim|required');
        $this->form_validation->set_rules('da_houseCategory', 'House Type', 'trim|required');
        $this->form_validation->set_rules('da_buildingDamages', 'Building Damages', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->OperationNgo_model->save_beneficiary_header_house_damageAssesment());
        }
    }

    function load_human_InjuryAssessment_members()
    {
        $data_arr = array();
        $benificiaryID = $this->input->post('benificiaryID');
        $this->db->select('empfamilydetailsID,name');
        $this->db->where('beneficiaryID', $benificiaryID);
        $this->db->from('srp_erp_ngo_beneficiaryfamilydetails');
        $groupEmployees = $this->db->get()->result_array();
        $data_arr = array('' => 'Select');
        if (isset($groupEmployees)) {
            foreach ($groupEmployees as $row) {
                $data_arr[trim($row['empfamilydetailsID'] ?? '')] = trim($row['name'] ?? '');
            }
        }
        echo form_dropdown('familyMembers', $data_arr, '', 'id="da_hi_familyMembers" class="form-control select2"');
    }

    function save_beneficiary_header_human_damageAssesment()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('familyMembers', 'Family Members', 'trim|required');;
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->OperationNgo_model->save_beneficiary_header_human_damageAssesment());
        }
    }


    function save_beneficiary_header_itemdamage_assesment()
    {
        $isInsuranceYN = trim($this->input->post('isInsuranceYN') ?? '');
        $damageItemCategoryID = trim($this->input->post('damageItemCategoryID') ?? '');
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('damageItemCategoryID', 'Category', 'trim|required');
        $this->form_validation->set_rules('damageTypeID', 'Type of Damage', 'trim|required');
        $this->form_validation->set_rules('damageConditionID', 'Item Condition', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Description', 'trim|required');
        if ($isInsuranceYN == 1) {
            $this->form_validation->set_rules('insuranceTypeID', 'Insurance Type', 'trim|required');
            //$this->form_validation->set_rules('insuranceRemarks', 'Insurance Remarks', 'trim|required');
        }
        if($damageItemCategoryID == 3){
            $this->form_validation->set_rules('vehicleAutoID', 'Vehicle Type', 'trim|required');

        }
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->OperationNgo_model->save_beneficiary_header_itemdamage_assesment());
        }
    }

    function save_beneficiary_header_businessProperties_assesment()
    {
        $isInsuranceYN = trim($this->input->post('isInsuranceYN') ?? '');
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('busineesActivityID', 'Business Activity', 'trim|required');;
        $this->form_validation->set_rules('damageTypeID', 'Type of Damage', 'trim|required');;
        $this->form_validation->set_rules('buildingTypeID', 'Business Type', 'trim|required');;
        $this->form_validation->set_rules('damageConditionID', 'Property Condition', 'trim|required');;
        if ($isInsuranceYN == 1) {
            $this->form_validation->set_rules('insuranceTypeID', 'Insurance Type', 'trim|required');;
            //$this->form_validation->set_rules('insuranceRemarks', 'Insurance Remarks', 'trim|required');;
        }
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->OperationNgo_model->save_beneficiary_header_businessProperties_assesment());
        }
    }

    function delete_human_injury_assessment()
    {
        echo json_encode($this->OperationNgo_model->delete_human_injury_assessment());
    }

    function delete_house_items_assessment()
    {
        echo json_encode($this->OperationNgo_model->delete_house_items_assessment());
    }

    function delete_business_properties_assessment()
    {
        echo json_encode($this->OperationNgo_model->delete_business_properties_assessment());
    }

    function check_project_shortCode()
    {
        echo json_encode($this->OperationNgo_model->check_project_shortCode());
    }

    function edit_benificiary_details()
    {
        echo json_encode($this->OperationNgo_model->edit_beneficiary_detials());
    }

    function edithumaninjury_assestment()
    {
        echo json_encode($this->OperationNgo_model->load_human_injury_assestment());
    }

    function load_item_damage()
    {
        echo json_encode($this->OperationNgo_model->load_item_damage_assetment());
    }

    function load_item_damage_bussiness_properties()
    {
        echo json_encode($this->OperationNgo_model->load_item_damage_bsp());
    }

    function fetch_da_report_project_based_countryDropdown()
    {
        $data_arr = array();
        $projectID = trim($this->input->post('projectID') ?? '');
        $country = $this->db->query("SELECT cm.countryID,cm.CountryDes FROM srp_erp_countrymaster cm JOIN srp_erp_ngo_beneficiarymaster bm ON bm.countryID = cm.countryID WHERE projectID = {$projectID} GROUP BY cm.countryID")->result_array();
        if (!empty($country)) {
            foreach ($country as $row) {
                $data_arr[trim($row['countryID'] ?? '')] = trim($row['CountryDes'] ?? '');
            }
        }
        echo form_dropdown('country', $data_arr, '', 'class="form-control select2" id="filter_country" onchange="load_projectBase_province(this.value)" multiple="" ');
    }

    function fetch_da_report_project_based_occupationdropdown()
    {
        $data_arr = array();
        $projectID = trim($this->input->post('projectID') ?? '');
        $country = $this->db->query("SELECT jb.JobCategoryID,jb.JobCatDescription FROM srp_erp_ngo_com_jobcategories jb JOIN srp_erp_ngo_beneficiarymaster bm ON bm.da_occupationID = jb.JobCategoryID WHERE projectID = {$projectID} GROUP BY jb.JobCategoryID")->result_array();
        if (!empty($country)) {
            foreach ($country as $row) {
                $data_arr[trim($row['JobCategoryID'] ?? '')] = trim($row['JobCatDescription'] ?? '');
            }
        }
        echo form_dropdown('occupation[]', $data_arr, '', 'class="form-control select2" id="filter_occupation"  multiple="" ');
    }

    function fetch_da_report_project_based_provinceDropdown()
    {
        $data_arr = array();
        $countyID = trim($this->input->post('countyID') ?? '');
        $projectID = trim($this->input->post('projectID') ?? '');
        $country = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster sm JOIN srp_erp_ngo_beneficiarymaster bm ON bm.province = sm.stateID  WHERE type = 1 AND projectID = {$projectID}")->result_array();
        if (!empty($country)) {
            foreach ($country as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('province[]', $data_arr, '', 'class="form-control select2" id="filter_province"  onchange="load_projectBase_district()" multiple="" ');
    }

    function fetch_da_report_project_based_districtDropdown()
    {
        $data_arr = array();
        $province = $this->input->post('province');
        $projectID = trim($this->input->post('projectID') ?? '');
        $where = "";
        if (!empty($province)) {
            $filterDistrict = join(',', $province);
            $where = "AND bm.province IN ($filterDistrict)";
        }
        $country = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster sm JOIN srp_erp_ngo_beneficiarymaster bm ON bm.district = sm.stateID  WHERE type = 2 AND projectID = {$projectID} $where")->result_array();

        //echo $this->db->last_query();
        if (!empty($country)) {
            foreach ($country as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('district[]', $data_arr, '', 'class="form-control select2" id="filter_district"  onchange="load_projectBase_division()" multiple="" ');
    }

    function fetch_da_report_project_based_jamiyaDropdown()
    {
        $data_arr = array();
        $district = $this->input->post('district');
        $projectID = trim($this->input->post('projectID') ?? '');
        $where = "";
        if (!empty($district)) {
            $filterDistrict = join(',', $district);
            $where = "AND bm.district IN ($filterDistrict)";
        }
        $country = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster sm JOIN srp_erp_ngo_beneficiarymaster bm ON bm.da_jammiyahDivision = sm.stateID  WHERE type = 3 AND projectID = {$projectID} $where")->result_array();

        //echo $this->db->last_query();
        if (!empty($country)) {
            foreach ($country as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('da_jammiyahDivision[]', $data_arr, '', 'class="form-control select2" id="filter_da_jammiyahDivision" multiple="" ');
    }

    function fetch_da_report_project_based_divisionDropdown()
    {
        $data_arr = array();
        $province = $this->input->post('province');
        $projectID = trim($this->input->post('projectID') ?? '');
        $where = "";
        if (!empty($province)) {
            $filterDistrict = join(',', $province);
            $where = "AND bm.district IN ($filterDistrict)";
        }
        $country = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster sm JOIN srp_erp_ngo_beneficiarymaster bm ON bm.division = sm.stateID  WHERE type = 3 AND projectID = {$projectID} $where")->result_array();

        //echo $this->db->last_query();
        if (!empty($country)) {
            foreach ($country as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('division[]', $data_arr, '', 'class="form-control select2" id="filter_division"  onchange="load_projectBase_mahalla()" multiple="" ');
    }

    function fetch_da_report_project_based_mahallaDropdown()
    {
        $data_arr = array();
        $division = $this->input->post('division');
        $projectID = trim($this->input->post('projectID') ?? '');
        $where = "";
        if (!empty($division)) {
            $filterDistrict = join(',', $division);
            $where = "AND bm.division IN ($filterDistrict)";
        }
        $country = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster sm JOIN srp_erp_ngo_beneficiarymaster bm ON bm.subDivision = sm.stateID  WHERE type = 4 AND projectID = {$projectID} $where")->result_array();

        //echo $this->db->last_query();
        if (!empty($country)) {
            foreach ($country as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('subDivision[]', $data_arr, '', 'class="form-control select2" id="filter_subDivision" multiple="" ');
    }

    function fetch_da_report_project_based_GnDivisionDropdown()
    {
        $data_arr = array();
        $division = $this->input->post('division');
        $projectID = trim($this->input->post('projectID') ?? '');
        $where = "";
        if (!empty($division)) {
            $filterDistrict = join(',', $division);
            $where = "AND bm.division IN ($filterDistrict)";
        }
        $country = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster sm JOIN srp_erp_ngo_beneficiarymaster bm ON bm.da_GnDivision = sm.stateID  WHERE type = 4 AND projectID = {$projectID} $where")->result_array();

        //echo $this->db->last_query();
        if (!empty($country)) {
            foreach ($country as $row) {
                $data_arr[trim($row['stateID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }
        echo form_dropdown('da_GnDivision[]', $data_arr, '', 'class="form-control select2" id="filter_da_GnDivision" multiple="" ');
    }

    function damage_assesment_report()
    {
        $province = $this->input->post('province');
        $date_format_policy = date_format_policy();
        $this->form_validation->set_rules('projectID', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('country', 'Country ID', 'trim|required');
        $this->form_validation->set_rules('province[]', 'Province', 'trim|required');
        $this->form_validation->set_rules('district[]', 'Distric', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
            exit();
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectID') ?? '');
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $country = trim($this->input->post('country') ?? '');
            $province = $this->input->post('province[]');
            $district = $this->input->post('district');
            $occupation = $this->input->post('occupation[]');
            $da_jammiyahDivision = $this->input->post('da_jammiyahDivision');
            $division = $this->input->post('division');
            $subDivision = $this->input->post('subDivision');
            $da_GnDivision = $this->input->post('da_GnDivision');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';
            $where_subDivision = '';
            $where_da_GnDivision = '';
            $where_da_occupation = '';
            $dateto = $this->input->post('dateto');
            $datefrom = $this->input->post('datefrom');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }


            if (!empty($occupation)) {
                $occupationSet = join(',', $occupation);
                $where_da_occupation = " AND (bm.da_occupationID IN ($occupationSet) OR bm.da_occupationID IS NULL OR bm.da_occupationID = '')";
            }

            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }
            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND (bm.province IN ($provinceSet) OR bm.province IS NULL OR bm.province = '')";
            }
            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND (bm.district IN ($districtSet) OR bm.district IS NULL OR bm.district = '')";
            }
            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }
            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '') ";
            }
            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }

            $data['damageRecord'] = $this->db->query("SELECT
	bm.benificiaryID,
	bm.fullName,
	bm.registeredDate,
	bm.address,
	bm.phonePrimary,
	cm.CountryDes,
		jb.JobCatDescription,
		sm.Description,
	CONCAT(bm.phoneAreaCodePrimary,' - ',bm.phonePrimary) as phonePrimary,
	bm.da_estimatedRepairingCost,
	humanInjury_tbl.humanInjuryAmount,
	houseItem_tbl.houseDamageLoss,
	humanInjury_tbl.humanCount,
	   houseItem_tbl.houseitemcount,
	businessItem_tbl.businessPropertyValue,
	businessItem_tbl.businessdamagecount,
	IFNULL(COUNT(bm.da_buildingDamages),0) as housedamagecount,
	smdistric.Description as distric
FROM
	srp_erp_ngo_beneficiarymaster bm
LEFT JOIN (
	SELECT
		sum(estimatedAmount) AS humanInjuryAmount,
		beneficiaryID,
		COUNT(beneficiaryID) as humanCount
	FROM
		srp_erp_ngo_humaninjuryassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_humaninjuryassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_humaninjuryassesment.beneficiaryID
) AS humanInjury_tbl ON humanInjury_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(damagedAmountClient) AS houseDamageLoss,
		beneficiaryID,
		COUNT(beneficiaryID) as houseitemcount
	FROM
		srp_erp_ngo_itemdamagedasssesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_itemdamagedasssesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_itemdamagedasssesment.beneficiaryID
) AS houseItem_tbl ON houseItem_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(propertyValue) AS businessPropertyValue,
		beneficiaryID,
		COUNT(beneficiaryID) as businessdamagecount
	FROM
		srp_erp_ngo_businessdamagedassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_businessdamagedassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_businessdamagedassesment.beneficiaryID
) AS businessItem_tbl ON businessItem_tbl.beneficiaryID = bm.benificiaryID
	LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID
		LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province
			LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district
			LEFT JOIN srp_erp_ngo_com_jobcategories jb ON jb.JobCategoryID = bm.da_occupationID 
WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $where_da_occupation $date 
	GROUP BY
	bm.benificiaryID")->result_array();


            $data['house'] = $this->db->query("SELECT
	bm.benificiaryID,
	bm.fullName,
	bm.address,
	dmtype.Description as TypeOfHouseDamage,
	buildingtype.Description as buildingtype,
	damagetype.Description as damagetype,
	bm.da_estimatedRepairingCost as reparingcost,
	bm.da_paidAmount as paidamount
FROM
	srp_erp_ngo_beneficiarymaster bm
LEFT JOIN (
	SELECT
		sum(estimatedAmount) AS humanInjuryAmount,
		beneficiaryID,
		COUNT(beneficiaryID) as humanCount
	FROM
		srp_erp_ngo_humaninjuryassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_humaninjuryassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_humaninjuryassesment.beneficiaryID
) AS humanInjury_tbl ON humanInjury_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(damagedAmountClient) AS houseDamageLoss,
		beneficiaryID,
		COUNT(beneficiaryID) as houseitemcount
	FROM
		srp_erp_ngo_itemdamagedasssesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_itemdamagedasssesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_itemdamagedasssesment.beneficiaryID
) AS houseItem_tbl ON houseItem_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(propertyValue) AS businessPropertyValue,
		beneficiaryID,
		COUNT(beneficiaryID) as businessdamagecount
	FROM
		srp_erp_ngo_businessdamagedassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_businessdamagedassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_businessdamagedassesment.beneficiaryID
) AS businessItem_tbl ON businessItem_tbl.beneficiaryID = bm.benificiaryID
	LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID
		LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province
			LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district
			LEFT JOIN srp_erp_ngo_com_jobcategories jb ON jb.JobCategoryID = bm.da_occupationID 
				LEFT JOIN srp_erp_ngo_damagetypemaster dmtype ON dmtype.damageTypeID = bm.da_typeOfhouseDamage 
	LEFT JOIN srp_erp_ngo_buildingtypemaster buildingtype ON buildingtype.buildingTypeID = bm.da_houseCategory 
	LEFT JOIN srp_erp_ngo_damagetypemaster damagetype ON damagetype.damageTypeID = bm.da_buildingDamages 
WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $where_da_occupation $date 
	GROUP BY
	bm.benificiaryID")->result_array();

            $houseRepairTotal = 0;
            $humanInjuryTotal = 0;
            $houseDamageLossTotal = 0;
            $businessPropertyTotal = 0;

            if (!empty($data['damageRecord'])) {
                foreach ($data['damageRecord'] as $row) {
                    $houseRepairTotal += $row["da_estimatedRepairingCost"];
                    $humanInjuryTotal += $row["humanInjuryAmount"];
                    $houseDamageLossTotal += $row["houseDamageLoss"];
                    $businessPropertyTotal += $row["businessPropertyValue"];
                }
            }
            $isPieChartRequired = 0;
            if (!empty($houseRepairTotal)) {
                $isPieChartRequired = 1;
            }
            if (!empty($humanInjuryTotal)) {
                $isPieChartRequired = 1;
            }
            if (!empty($houseDamageLossTotal)) {
                $isPieChartRequired = 1;
            }
            if (!empty($businessPropertyTotal)) {
                $isPieChartRequired = 1;
            }


            $piData = [
                ['name' => 'House',
                    'y' => $houseRepairTotal
                ],

                ['name' => 'Human Injury',
                    'y' => $humanInjuryTotal],
                ['name' => 'House Items',
                    'y' => $houseDamageLossTotal],
                ['name' => 'Business Property',
                    'y' => $businessPropertyTotal]
            ];

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }


            if (!empty($province)) {
                $provincesearched = join(',', $province);
            } else {
                $provincesearched = '';
            }
            if (!empty($occupation)) {
                $occupationsearched = join(',', $occupation);
            } else {
                $occupationsearched = '';
            }
            if (!empty($da_jammiyahDivision)) {
                $jamiyadivision = join(',', $da_jammiyahDivision);
            } else {
                $jamiyadivision = '';
            }
            if (!empty($division)) {
                $divisionsearched = join(',', $division);
            } else {
                $divisionsearched = '';
            }
            if (!empty($subDivision)) {
                $subdivisionserched = join(',', $subDivision);
            } else {
                $subdivisionserched = '';
            }
            if (!empty($da_GnDivision)) {
                $dagndivisionsearched = join(',', $da_GnDivision);
            } else {
                $dagndivisionsearched = '';
            }


            $data['project_id_for_house'] = $projectID;
            $data['country_id_for_house'] = $country;
            $data['province_id_for_house'] = $provincesearched;
            $data['distric_id_for_house'] = join(',', $district);
            $data['occupation_id_for_house'] = $occupationsearched;
            $data['jamiyadivision_id_for_house'] = $jamiyadivision;
            $data['division_id_for_house'] = $divisionsearched;
            $data['sub_division_id_for_house'] = $subdivisionserched;
            $data['da_gn_division_id_for_house'] = $dagndivisionsearched;
            $data['date_to_for_house'] = $dateto;
            $data['date_for_id_for_house'] = $datefrom;


            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();
            $data["type"] = "html";
            $view = $this->load->view('system/operationNgo/ajax/damage_assesment_report_view', $data, true);
            $returnData = [
                'view' => $view,
                'piData' => $piData,
                'isPieChartRequired' => $isPieChartRequired
            ];

            echo json_encode($returnData);
        }

    }

    function damage_assesment_report_pdf()
    {
        $this->form_validation->set_rules('projectID', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('country', 'Country ID', 'trim|required');
        $date_format_policy = date_format_policy();
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectID') ?? '');
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $occupation = $this->input->post('occupation[]');
            $country = trim($this->input->post('country[]') ?? '');
            $province = $this->input->post('province[]');
            $district = $this->input->post('district[]');
            $da_jammiyahDivision = $this->input->post('da_jammiyahDivision[]');
            $division = $this->input->post('division[]');
            $subDivision = $this->input->post('subDivision[]');
            $da_GnDivision = $this->input->post('da_GnDivision[]');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';
            $where_da_occupation = '';

            $where_subDivision = '';
            $where_da_GnDivision = '';
            $dateto = $this->input->post('dateto');
            $datefrom = $this->input->post('datefrom');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }


            if (!empty($occupation)) {
                $occupationSet = join(',', $occupation);
                $where_da_occupation = " AND (bm.da_occupationID IN ($occupationSet) OR bm.da_occupationID IS NULL OR bm.da_occupationID = '')";
            }

            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }
            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND (bm.province IN ($provinceSet) OR bm.province IS NULL OR bm.province = '')";
            }
            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND (bm.district IN ($districtSet) OR bm.district IS NULL OR bm.district = '')";
            }
            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }
            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '') ";
            }
            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }

            $data['damageRecord'] = $this->db->query("SELECT
	bm.benificiaryID,
	bm.fullName,
	bm.registeredDate,
	bm.address,
	bm.phonePrimary,
	cm.CountryDes,
		jb.JobCatDescription,
		sm.Description,
	CONCAT(bm.phoneAreaCodePrimary,' - ',bm.phonePrimary) as phonePrimary,
	bm.da_estimatedRepairingCost,
	humanInjury_tbl.humanInjuryAmount,
	houseItem_tbl.houseDamageLoss,
	humanInjury_tbl.humanCount,
	   houseItem_tbl.houseitemcount,
	businessItem_tbl.businessPropertyValue,
	businessItem_tbl.businessdamagecount,
	IFNULL(COUNT(bm.da_buildingDamages),0) as housedamagecount,
	smdistric.Description as distric
FROM
	srp_erp_ngo_beneficiarymaster bm
LEFT JOIN (
	SELECT
		sum(estimatedAmount) AS humanInjuryAmount,
		beneficiaryID,
		COUNT(beneficiaryID) as humanCount
	FROM
		srp_erp_ngo_humaninjuryassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_humaninjuryassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_humaninjuryassesment.beneficiaryID
) AS humanInjury_tbl ON humanInjury_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(damagedAmountClient) AS houseDamageLoss,
		beneficiaryID,
		COUNT(beneficiaryID) as houseitemcount
	FROM
		srp_erp_ngo_itemdamagedasssesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_itemdamagedasssesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_itemdamagedasssesment.beneficiaryID
) AS houseItem_tbl ON houseItem_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(propertyValue) AS businessPropertyValue,
		beneficiaryID,
		COUNT(beneficiaryID) as businessdamagecount
	FROM
		srp_erp_ngo_businessdamagedassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_businessdamagedassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_businessdamagedassesment.beneficiaryID
) AS businessItem_tbl ON businessItem_tbl.beneficiaryID = bm.benificiaryID
	LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID
		LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province
			LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district
			LEFT JOIN srp_erp_ngo_com_jobcategories jb ON jb.JobCategoryID = bm.da_occupationID 
WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $where_da_occupation $date 
	GROUP BY
	bm.benificiaryID")->result_array();

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }

            $data["type"] = "html";
            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();

            $html = $this->load->view('system/operationNgo/ajax/damage_assesment_report_pdf', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    /*  District Wise  */
    function districtWise_damage_assesment_report()
    {
        $province = $this->input->post('province');
        $date_format_policy = date_format_policy();
        $this->form_validation->set_rules('projectID', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('country', 'Country ID', 'trim|required');
        $this->form_validation->set_rules('province[]', 'Province', 'trim|required');
        $this->form_validation->set_rules('district[]', 'Distric', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
            exit();
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectID') ?? '');
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $country = trim($this->input->post('country') ?? '');
            $province = $this->input->post('province[]');
            $district = $this->input->post('district');
            $da_jammiyahDivision = $this->input->post('da_jammiyahDivision');
            $division = $this->input->post('division');
            $subDivision = $this->input->post('subDivision');
            $da_GnDivision = $this->input->post('da_GnDivision');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';
            $where_subDivision = '';
            $where_da_GnDivision = '';
            $dateto = $this->input->post('dateto');
            $datefrom = $this->input->post('datefrom');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }

            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }
            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND (bm.province IN ($provinceSet) OR bm.province IS NULL OR bm.province = '')";
            }
            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND (bm.district IN ($districtSet) OR bm.district IS NULL OR bm.district = '')";
            }
            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }
            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '') ";
            }
            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }


            $data['daDisWiseDistric'] = $this->db->query("SELECT DISTINCT smdistric.stateID as districID,sm.Description as DAprovince,smdistric.Description as DAdistrict,bm.projectID
FROM
	srp_erp_ngo_beneficiarymaster bm
	LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID
		LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province
			LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district
WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $date 
	GROUP BY
	sm.stateID,smdistric.stateID")->result_array();

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }


            if (!empty($province)) {
                $provincesearched = join(',', $province);
            } else {
                $provincesearched = '';
            }
            if (!empty($occupation)) {
                $occupationsearched = join(',', $occupation);
            } else {
                $occupationsearched = '';
            }
            if (!empty($da_jammiyahDivision)) {
                $jamiyadivision = join(',', $da_jammiyahDivision);
            } else {
                $jamiyadivision = '';
            }
            if (!empty($division)) {
                $divisionsearched = join(',', $division);
            } else {
                $divisionsearched = '';
            }
            if (!empty($subDivision)) {
                $subdivisionserched = join(',', $subDivision);
            } else {
                $subdivisionserched = '';
            }
            if (!empty($da_GnDivision)) {
                $dagndivisionsearched = join(',', $da_GnDivision);
            } else {
                $dagndivisionsearched = '';
            }


            $data['project_id_for_house'] = $projectID;
            $data['country_id_for_house'] = $country;
            $data['province_id_for_house'] = $provincesearched;
            $data['distric_id_for_house'] = join(',', $district);
            $data['occupation_id_for_house'] = $occupationsearched;
            $data['jamiyadivision_id_for_house'] = $jamiyadivision;
            $data['division_id_for_house'] = $divisionsearched;
            $data['sub_division_id_for_house'] = $subdivisionserched;
            $data['da_gn_division_id_for_house'] = $dagndivisionsearched;
            $data['date_to_for_house'] = $dateto;
            $data['date_for_id_for_house'] = $datefrom;

            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();

            $data['vehicleTypes'] = $this->db->query("SELECT * from  srp_erp_ngo_com_vehicles_master ORDER BY vehicleAutoID ASC")->result_array();

            $data["type"] = "html";
            $view = $this->load->view('system/operationNgo/ajax/districtWise_damages_report_view', $data, true);
            $returnData = [
                'view' => $view,
            ];

            echo json_encode($returnData);
        }

    }

    function districtWise_damages_report_pdf()
    {
        $this->form_validation->set_rules('projectID', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('country', 'Country ID', 'trim|required');
        $date_format_policy = date_format_policy();
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectID') ?? '');
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $country = trim($this->input->post('country[]') ?? '');
            $province = $this->input->post('province[]');
            $district = $this->input->post('district[]');
            $da_jammiyahDivision = $this->input->post('da_jammiyahDivision[]');
            $division = $this->input->post('division[]');
            $subDivision = $this->input->post('subDivision[]');
            $da_GnDivision = $this->input->post('da_GnDivision[]');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';

            $where_subDivision = '';
            $where_da_GnDivision = '';
            $dateto = $this->input->post('dateto');
            $datefrom = $this->input->post('datefrom');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }
            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }
            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND (bm.province IN ($provinceSet) OR bm.province IS NULL OR bm.province = '')";
            }
            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND (bm.district IN ($districtSet) OR bm.district IS NULL OR bm.district = '')";
            }
            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }
            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '') ";
            }
            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }

            $data['daDisWiseDistric'] = $this->db->query("SELECT DISTINCT smdistric.stateID as districID,sm.Description as DAprovince,smdistric.Description as DAdistrict,bm.projectID
FROM
	srp_erp_ngo_beneficiarymaster bm
	LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID
		LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province
			LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district
WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $date 
	GROUP BY
	sm.stateID,smdistric.stateID")->result_array();

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }

            $data["type"] = "html";
            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();

            $data['vehicleTypes'] = $this->db->query("SELECT * from  srp_erp_ngo_com_vehicles_master ORDER BY vehicleAutoID ASC")->result_array();

            $html = $this->load->view('system/operationNgo/ajax/districtWise_damages_report_pdf', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A3-L');
        }
    }

    function update_pp_beneficiary_qualified()
    {
        //$this->form_validation->set_rules('beneficiaryid', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('proposalID', 'Proposal ID', 'trim|required');
        // $this->form_validation->set_rules('isqualified', 'Is qualified', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_quaified_status_pp_beneficiaries());
        }
    }

    function load_user_assign_table()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $project_id = trim($this->input->post('ngoProjectID') ?? '');
        $data['userassignloadview'] = $this->db->query("select pro.projectOwnerID,pro.projectID,pp.projectName,pro.employeeID,emp.Ename2,pro.isAdd,pro.isDelete,pro.isView,pro.isConfirm,pro.isEdit,pro.isApproval
        from srp_erp_ngo_projectowners pro left join srp_erp_ngo_projects pp on pp.ngoProjectID = pro.projectID left join srp_employeesdetails emp on emp.EIdNo = pro.employeeID
        where  pro.projectID = $project_id AND pro.companyID = $companyID")->result_array();
        $this->load->view('system/operationNgo/ajax/load_project_users', $data);

    }

    function save_employees()
    {
        echo json_encode($this->OperationNgo_model->save_employees_details());
    }

    function update_is_add_status()
    {
        $this->form_validation->set_rules('projectOwnerID', 'Project Owner ID', 'trim|required');
        $this->form_validation->set_rules('projectid', 'projectid', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_is_add_status());
        }
    }

    function update_is_edit_status()
    {
        $this->form_validation->set_rules('projectOwnerID', 'Project Owner ID', 'trim|required');
        $this->form_validation->set_rules('projectid', 'projectid', 'trim|required');
        $this->form_validation->set_rules('statusisedit', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_is_edit_status());
        }
    }

    function update_is_confirm_status()
    {
        $this->form_validation->set_rules('projectOwnerID', 'Project Owner ID', 'trim|required');
        $this->form_validation->set_rules('projectid', 'projectid', 'trim|required');
        $this->form_validation->set_rules('statusisconfirm', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_is_confirm_status());
        }
    }

    function update_is_approval_status()
    {
        $this->form_validation->set_rules('projectOwnerID', 'Project Owner ID', 'trim|required');
        $this->form_validation->set_rules('projectid', 'projectid', 'trim|required');
        $this->form_validation->set_rules('statusisapproval', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_is_approval_status());
        }
    }

    function update_is_view_status()
    {
        $this->form_validation->set_rules('projectOwnerID', 'Project Owner ID', 'trim|required');
        $this->form_validation->set_rules('projectid', 'projectid', 'trim|required');
        $this->form_validation->set_rules('statusisview', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_is_view_status());
        }
    }

    function cheack_is_add_status_beneficiary()
    {
        echo json_encode($this->OperationNgo_model->cheack_is_add_status());
    }

    function delete_assign_usersfor_project()
    {
        echo json_encode($this->OperationNgo_model->delete_assign_usersfor_project());
    }

    function update_is_delete_status()
    {
        $this->form_validation->set_rules('projectOwnerID', 'Project Owner ID', 'trim|required');
        $this->form_validation->set_rules('projectid', 'projectid', 'trim|required');
        $this->form_validation->set_rules('statusisdelete', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_is_view_status());
        }
    }


    function damage_assesment_report_house()
    {
        $province = $this->input->post('province');
        $date_format_policy = date_format_policy();
        $this->form_validation->set_rules('projectid', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('countryid', 'Country ID', 'trim|required');
        $this->form_validation->set_rules('provinceid[]', 'Province', 'trim|required');
        $this->form_validation->set_rules('districid[]', 'Distric', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectid') ?? '');
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $country = trim($this->input->post('countryid') ?? '');
            $province = $this->input->post('provinceid[]');
            $district = $this->input->post('districid[]');
            $occupation = $this->input->post('occupation[]');
            $da_jammiyahDivision = $this->input->post('jamiyadivision');
            $division = $this->input->post('division');
            $subDivision = $this->input->post('subDivision');
            $da_GnDivision = $this->input->post('da_GnDivision');
            $typeofdamage = $this->input->post('da_typeOfhouseDamage[]');
            $housecategory = $this->input->post('da_houseCategory[]');
            $buildingdamage = $this->input->post('da_buildingDamages[]');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';
            $where_subDivision = '';
            $where_da_GnDivision = '';
            $where_da_occupation = '';
            $where_da_damagehousetype = '';
            $where_da_house_category = '';
            $where_da_building_damage = '';
            $dateto = $this->input->post('dateto');
            $datefrom = $this->input->post('datefrom');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }


            if (!empty($buildingdamage)) {
                $buildingdamageset = join(',', $buildingdamage);
                $where_da_building_damage = " AND (bm.da_buildingDamages IN ($buildingdamageset))";
            }

            if (!empty($housecategory)) {
                $housecategoryset = join(',', $housecategory);
                $where_da_house_category = " AND (bm.da_houseCategory IN ($housecategoryset))";
            }

            if (!empty($typeofdamage)) {
                $typeofdamageset = join(',', $typeofdamage);
                $where_da_damagehousetype = " AND (bm.da_typeOfhouseDamage IN ($typeofdamageset))";
            }
            if (!empty($occupation)) {
                $occupationSet = join(',', $occupation);
                $where_da_occupation = " AND (bm.da_occupationID IN ($occupationSet) OR bm.da_occupationID IS NULL OR bm.da_occupationID = '')";
            }

            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND bm.province IN ($provinceSet)";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND bm.district IN ($districtSet) ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '')";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }

            $data['house'] = $this->db->query("SELECT
	bm.benificiaryID,
	bm.fullName,
	bm.address,
	dmtype.Description as TypeOfHouseDamage,
	buildingtype.Description as buildingtype,
	damagetype.Description as damagetype,
	bm.da_estimatedRepairingCost as reparingcost,
	bm.da_paidAmount as paidamt
FROM
	srp_erp_ngo_beneficiarymaster bm
LEFT JOIN (
	SELECT
		sum(estimatedAmount) AS humanInjuryAmount,
		beneficiaryID,
		COUNT(beneficiaryID) as humanCount
	FROM
		srp_erp_ngo_humaninjuryassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_humaninjuryassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_humaninjuryassesment.beneficiaryID
) AS humanInjury_tbl ON humanInjury_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(damagedAmountClient) AS houseDamageLoss,
		beneficiaryID,
		COUNT(beneficiaryID) as houseitemcount
	FROM
		srp_erp_ngo_itemdamagedasssesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_itemdamagedasssesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_itemdamagedasssesment.beneficiaryID
) AS houseItem_tbl ON houseItem_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(propertyValue) AS businessPropertyValue,
		beneficiaryID,
		COUNT(beneficiaryID) as businessdamagecount
	FROM
		srp_erp_ngo_businessdamagedassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_businessdamagedassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_businessdamagedassesment.beneficiaryID
) AS businessItem_tbl ON businessItem_tbl.beneficiaryID = bm.benificiaryID
	LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID
		LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province
			LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district
			LEFT JOIN srp_erp_ngo_com_jobcategories jb ON jb.JobCategoryID = bm.da_occupationID 
				LEFT JOIN srp_erp_ngo_damagetypemaster dmtype ON dmtype.damageTypeID = bm.da_typeOfhouseDamage 
	LEFT JOIN srp_erp_ngo_buildingtypemaster buildingtype ON buildingtype.buildingTypeID = bm.da_houseCategory 
	LEFT JOIN srp_erp_ngo_damagetypemaster damagetype ON damagetype.damageTypeID = bm.da_buildingDamages 
WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $where_da_occupation $date $where_da_damagehousetype $where_da_house_category $where_da_building_damage
	GROUP BY
	bm.benificiaryID")->result_array();


            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }


            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();


            $data["type"] = "html";
            echo $html = $this->load->view('system/operationNgo/ajax/damage_assesment_report_house', $data, true);


        }

    }

    function damage_assesment_report_house_pdf()
    {
        $province = $this->input->post('province');
        $date_format_policy = date_format_policy();
        $this->form_validation->set_rules('projectid', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('countryid', 'Country ID', 'trim|required');
        $this->form_validation->set_rules('provinceid[]', 'Province', 'trim|required');
        $this->form_validation->set_rules('districid[]', 'Distric', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectid') ?? '');
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $country = trim($this->input->post('countryid') ?? '');
            $province = $this->input->post('provinceid[]');
            $district = $this->input->post('districid[]');
            $occupation = $this->input->post('occupation[]');
            $da_jammiyahDivision = $this->input->post('jamiyadivision');
            $division = $this->input->post('division');
            $subDivision = $this->input->post('subDivision');
            $da_GnDivision = $this->input->post('da_GnDivision');
            $typeofdamage = $this->input->post('da_typeOfhouseDamage[]');
            $housecategory = $this->input->post('da_houseCategory[]');
            $buildingdamage = $this->input->post('da_buildingDamages[]');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';
            $where_subDivision = '';
            $where_da_GnDivision = '';
            $where_da_occupation = '';
            $where_da_damagehousetype = '';
            $where_da_house_category = '';
            $where_da_building_damage = '';
            $dateto = $this->input->post('dateto');
            $datefrom = $this->input->post('datefrom');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }


            if (!empty($buildingdamage)) {
                $buildingdamageset = join(',', $buildingdamage);
                $where_da_building_damage = " AND (bm.da_buildingDamages IN ($buildingdamageset))";
            }

            if (!empty($housecategory)) {
                $housecategoryset = join(',', $housecategory);
                $where_da_house_category = " AND (bm.da_houseCategory IN ($housecategoryset))";
            }

            if (!empty($typeofdamage)) {
                $typeofdamageset = join(',', $typeofdamage);
                $where_da_damagehousetype = " AND (bm.da_typeOfhouseDamage IN ($typeofdamageset))";
            }
            if (!empty($occupation)) {
                $occupationSet = join(',', $occupation);
                $where_da_occupation = " AND (bm.da_occupationID IN ($occupationSet) OR bm.da_occupationID IS NULL OR bm.da_occupationID = '')";
            }

            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND bm.province IN ($provinceSet)";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND bm.district IN ($districtSet) ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '')";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }

            $data['house'] = $this->db->query("SELECT
	bm.benificiaryID,
	bm.fullName,
	bm.address,
	dmtype.Description as TypeOfHouseDamage,
	buildingtype.Description as buildingtype,
	damagetype.Description as damagetype,
	bm.da_estimatedRepairingCost as reparingcost,
	bm.da_paidAmount as paidamt
FROM
	srp_erp_ngo_beneficiarymaster bm
LEFT JOIN (
	SELECT
		sum(estimatedAmount) AS humanInjuryAmount,
		beneficiaryID,
		COUNT(beneficiaryID) as humanCount
	FROM
		srp_erp_ngo_humaninjuryassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_humaninjuryassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_humaninjuryassesment.beneficiaryID
) AS humanInjury_tbl ON humanInjury_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(damagedAmountClient) AS houseDamageLoss,
		beneficiaryID,
		COUNT(beneficiaryID) as houseitemcount
	FROM
		srp_erp_ngo_itemdamagedasssesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_itemdamagedasssesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_itemdamagedasssesment.beneficiaryID
) AS houseItem_tbl ON houseItem_tbl.beneficiaryID = bm.benificiaryID
LEFT JOIN (
	SELECT
		sum(propertyValue) AS businessPropertyValue,
		beneficiaryID,
		COUNT(beneficiaryID) as businessdamagecount
	FROM
		srp_erp_ngo_businessdamagedassesment
	JOIN srp_erp_ngo_beneficiarymaster ON srp_erp_ngo_beneficiarymaster.benificiaryID = srp_erp_ngo_businessdamagedassesment.beneficiaryID
	GROUP BY
		srp_erp_ngo_businessdamagedassesment.beneficiaryID
) AS businessItem_tbl ON businessItem_tbl.beneficiaryID = bm.benificiaryID
	LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bm.countryID
		LEFT JOIN srp_erp_statemaster sm ON sm.stateID = bm.province
			LEFT JOIN srp_erp_statemaster smdistric ON smdistric.stateID = bm.district
			LEFT JOIN srp_erp_ngo_com_jobcategories jb ON jb.JobCategoryID = bm.da_occupationID 
				LEFT JOIN srp_erp_ngo_damagetypemaster dmtype ON dmtype.damageTypeID = bm.da_typeOfhouseDamage 
	LEFT JOIN srp_erp_ngo_buildingtypemaster buildingtype ON buildingtype.buildingTypeID = bm.da_houseCategory 
	LEFT JOIN srp_erp_ngo_damagetypemaster damagetype ON damagetype.damageTypeID = bm.da_buildingDamages 
WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $where_da_occupation $date $where_da_damagehousetype $where_da_house_category $where_da_building_damage
	GROUP BY
	bm.benificiaryID")->result_array();

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }

            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();

            $data["type"] = "pdf";
            $html = $this->load->view('system/operationNgo/ajax/damage_assesment_report_house_pdf', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');


        }

    }

    function damage_assesment_report_business()
    {
        $province = $this->input->post('provinceidbs[]');
        $date_format_policy = date_format_policy();
        $this->form_validation->set_rules('projectidbs', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('countryidbs', 'Country ID', 'trim|required');
        $this->form_validation->set_rules('provinceidbs[]', 'Province', 'trim|required');
        $this->form_validation->set_rules('districidbs[]', 'Distric', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectidbs') ?? '');
            $businesstypeid = ($this->input->post('buildingTypeIDbs[]'));
            $damagetypeid = ($this->input->post('damageTypeIDbs[]'));
            $damageconditionid = ($this->input->post('damageConditionIDbs[]'));
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $country = trim($this->input->post('countryidbs') ?? '');
            $district = $this->input->post('districidbs[]');
            $occupation = $this->input->post('occupationbs[]');
            $da_jammiyahDivision = $this->input->post('jamiyadivisionbs');
            $division = $this->input->post('divisionbs');
            $subDivision = $this->input->post('subDivisionbs');
            $da_GnDivision = $this->input->post('da_GnDivisionbs');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';
            $where_subDivision = '';
            $where_da_GnDivision = '';
            $where_da_occupation = '';
            $where_da_businesstype = '';
            $where_da_damagecondition = '';
            $where_da_damage = '';
            $dateto = $this->input->post('datetobs');
            $datefrom = $this->input->post('datefrombs');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }

            if (!empty($damagetypeid)) {
                $damagetype = join(',', $damagetypeid);
                $where_da_damage = " AND ( bsassestment.damageTypeID IN ($damagetype) OR bsassestment.damageTypeID IS NULL OR bsassestment.damageTypeID = '')";
            }

            if (!empty($damageconditionid)) {
                $damageconditiontype = join(',', $damageconditionid);
                $where_da_damagecondition = " AND ( bsassestment.damageConditionID IN ($damageconditiontype) OR bsassestment.damageConditionID IS NULL OR bsassestment.damageConditionID = '')";
            }

            if (!empty($businesstypeid)) {
                $businesstype = join(',', $businesstypeid);
                $where_da_businesstype = " AND ( btm.buildingTypeID IN ($businesstype) OR btm.buildingTypeID IS NULL OR btm.buildingTypeID = '')";
            }

            if (!empty($occupation)) {
                $occupationSet = join(',', $occupation);
                $where_da_occupation = " AND (bm.da_occupationID IN ($occupationSet) OR bm.da_occupationID IS NULL OR bm.da_occupationID = '')";
            }

            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND bm.province IN ($provinceSet)";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND bm.district IN ($districtSet) ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '')";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }

            $data['business'] = $this->db->query("select 
bsassestment.beneficiaryID,
bm.fullName,
bm.address,
btm.Description,
dmtype.Description as damagetype,
dmtypecondition.Description as damagecondition,
bsassestment.propertyValue,
bsassestment.paidAmount,
bsactivity.Description as businessactivity
from 
srp_erp_ngo_businessdamagedassesment bsassestment
LEFT JOIN srp_erp_ngo_buildingtypemaster btm on btm.buildingTypeID = bsassestment.buildingTypeID
LEFT JOIN srp_erp_ngo_damagetypemaster dmtype on dmtype.damageTypeID = bsassestment.damageTypeID
LEFT JOIN (select * from srp_erp_ngo_damagetypemaster) dmtypecondition on dmtypecondition.damageTypeID = bsassestment.damageConditionID
LEFT JOIN srp_erp_ngo_beneficiarymaster bm on bm.benificiaryID = bsassestment.beneficiaryID
LEFT JOIN srp_erp_ngo_business_activity bsactivity on bsactivity.businessID = bsassestment.busineesActivityID
WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $where_da_occupation $date $where_da_businesstype $where_da_damage $where_da_damagecondition
	")->result_array();


            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }


            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();


            $data["type"] = "html";
            echo $html = $this->load->view('system/operationNgo/ajax/damage_assesment_report_business', $data, true);

        }
    }

    function damage_assesment_report_business_pdf()
    {
        $province = $this->input->post('provinceidbs[]');
        $date_format_policy = date_format_policy();
        $this->form_validation->set_rules('projectidbs', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('countryidbs', 'Country ID', 'trim|required');
        $this->form_validation->set_rules('provinceidbs[]', 'Province', 'trim|required');
        $this->form_validation->set_rules('districidbs[]', 'Distric', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectidbs') ?? '');
            $businesstypeid = ($this->input->post('buildingTypeIDbs[]'));
            $damagetypeid = ($this->input->post('damageTypeIDbs[]'));
            $damageconditionid = ($this->input->post('damageConditionIDbs[]'));
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $country = trim($this->input->post('countryidbs') ?? '');
            $district = $this->input->post('districidbs[]');
            $occupation = $this->input->post('occupationbs[]');
            $da_jammiyahDivision = $this->input->post('jamiyadivisionbs');
            $division = $this->input->post('divisionbs');
            $subDivision = $this->input->post('subDivisionbs');
            $da_GnDivision = $this->input->post('da_GnDivisionbs');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';
            $where_subDivision = '';
            $where_da_GnDivision = '';
            $where_da_occupation = '';
            $where_da_businesstype = '';
            $where_da_damagecondition = '';
            $where_da_damage = '';
            $dateto = $this->input->post('datetobs');
            $datefrom = $this->input->post('datefrombs');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }

            if (!empty($damagetypeid)) {
                $damagetype = join(',', $damagetypeid);
                $where_da_damage = " AND ( bsassestment.damageTypeID IN ($damagetype) OR bsassestment.damageTypeID IS NULL OR bsassestment.damageTypeID = '')";
            }

            if (!empty($damageconditionid)) {
                $damageconditiontype = join(',', $damageconditionid);
                $where_da_damagecondition = " AND ( bsassestment.damageConditionID IN ($damageconditiontype) OR bsassestment.damageConditionID IS NULL OR bsassestment.damageConditionID = '')";
            }

            if (!empty($businesstypeid)) {
                $businesstype = join(',', $businesstypeid);
                $where_da_businesstype = " AND ( btm.buildingTypeID IN ($businesstype) OR btm.buildingTypeID IS NULL OR btm.buildingTypeID = '')";
            }

            if (!empty($occupation)) {
                $occupationSet = join(',', $occupation);
                $where_da_occupation = " AND (bm.da_occupationID IN ($occupationSet) OR bm.da_occupationID IS NULL OR bm.da_occupationID = '')";
            }

            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND bm.province IN ($provinceSet)";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND bm.district IN ($districtSet) ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '')";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }

            $data['business'] = $this->db->query("select 
bsassestment.beneficiaryID,
bm.fullName,
bm.address,
btm.Description,
dmtype.Description as damagetype,
dmtypecondition.Description as damagecondition,
bsassestment.propertyValue,
bsassestment.paidAmount,
bsactivity.Description as businessactivity
from 
srp_erp_ngo_businessdamagedassesment bsassestment
LEFT JOIN srp_erp_ngo_buildingtypemaster btm on btm.buildingTypeID = bsassestment.buildingTypeID
LEFT JOIN srp_erp_ngo_damagetypemaster dmtype on dmtype.damageTypeID = bsassestment.damageTypeID
LEFT JOIN (select * from srp_erp_ngo_damagetypemaster) dmtypecondition on dmtypecondition.damageTypeID = bsassestment.damageConditionID
LEFT JOIN srp_erp_ngo_beneficiarymaster bm on bm.benificiaryID = bsassestment.beneficiaryID
LEFT JOIN srp_erp_ngo_business_activity bsactivity on bsactivity.businessID = bsassestment.busineesActivityID
WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $where_da_occupation $date $where_da_businesstype $where_da_damage $where_da_damagecondition
	")->result_array();

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }

            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();

            $data["type"] = "pdf";
            $html = $this->load->view('system/operationNgo/ajax/damage_assesment_report_building_pdf', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }

    }


    function damage_assesment_report_help_aid()
    {
        $province = $this->input->post('provinceidhelp[]');
        $date_format_policy = date_format_policy();
        $this->form_validation->set_rules('projectidhelp', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('countryidhelp', 'Country ID', 'trim|required');
        $this->form_validation->set_rules('provinceidhelp[]', 'Province', 'trim|required');
        $this->form_validation->set_rules('districidhelp[]', 'Distric', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectidhelp') ?? '');
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $country = trim($this->input->post('countryidhelp') ?? '');
            $district = $this->input->post('districidhelp[]');
            $occupation = $this->input->post('occupationhelp[]');
            $da_jammiyahDivision = $this->input->post('jamiyadivisionhelp[]');
            $division = $this->input->post('divisionhelp[]');
            $subDivision = $this->input->post('subDivisionhelp[]');
            $da_GnDivision = $this->input->post('da_GnDivisionhelp[]');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';
            $where_subDivision = '';
            $where_da_GnDivision = '';
            $where_da_occupation = '';

            $dateto = $this->input->post('datetohelp');
            $datefrom = $this->input->post('datefromhelp');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }


            if (!empty($occupation)) {
                $occupationSet = join(',', $occupation);
                $where_da_occupation = " AND (bm.da_occupationID IN ($occupationSet) OR bm.da_occupationID IS NULL OR bm.da_occupationID = '')";
            }

            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND bm.province IN ($provinceSet)";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND bm.district IN ($districtSet) ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '')";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }

            $data['helpaid'] = $this->db->query("SELECT bm.fullName,bm.address,bmsup.assitanceName,bmsup.Organization,bmsup.amount FROM srp_erp_ngo_beneficiary_othersupportassistance bmsup
            LEFT JOIN srp_erp_ngo_beneficiarymaster bm on bm.benificiaryID = bmsup.beneficiaryID
              WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $where_da_occupation $date 
	")->result_array();


            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }


            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();

            $data["type"] = "html";
            echo $html = $this->load->view('system/operationNgo/ajax/damage_assesment_report_helpaid', $data, true);
        }

    }

    function damage_assesment_report_helpaid_pdf()
    {
        $province = $this->input->post('provinceidhelp[]');
        $date_format_policy = date_format_policy();
        $this->form_validation->set_rules('projectidhelp', 'Project ID', 'trim|required');
        $this->form_validation->set_rules('countryidhelp', 'Country ID', 'trim|required');
        $this->form_validation->set_rules('provinceidhelp[]', 'Province', 'trim|required');
        $this->form_validation->set_rules('districidhelp[]', 'Distric', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo warning_message($error_message);
        } else {
            $data = array();
            $projectID = trim($this->input->post('projectidhelp') ?? '');
            $convertFormat = convert_date_format_sql();
            $companyID = current_companyID();
            $country = trim($this->input->post('countryidhelp') ?? '');
            $district = $this->input->post('districidhelp[]');
            $occupation = $this->input->post('occupationhelp[]');
            $da_jammiyahDivision = $this->input->post('jamiyadivisionhelp[]');
            $division = $this->input->post('divisionhelp[]');
            $subDivision = $this->input->post('subDivisionhelp[]');
            $da_GnDivision = $this->input->post('da_GnDivisionhelp[]');
            $where_country = '';
            $where_province = '';
            $where_district = '';
            $where_da_jammiyahDivision = '';
            $where_division = '';
            $where_subDivision = '';
            $where_da_GnDivision = '';
            $where_da_occupation = '';


            $dateto = $this->input->post('datetohelp');
            $datefrom = $this->input->post('datefromhelp');
            $datefromconvert = input_format_date($datefrom, $date_format_policy);
            $datetoconvert = input_format_date($dateto, $date_format_policy);

            $date = "";
            if (!empty($datefrom) && !empty($dateto)) {
                $date .= " AND ( bm.registeredDate >= '" . $datefromconvert . " 00:00:00' AND bm.registeredDate <= '" . $datetoconvert . " 23:59:00')";
            }


            if (!empty($occupation)) {
                $occupationSet = join(',', $occupation);
                $where_da_occupation = " AND (bm.da_occupationID IN ($occupationSet) OR bm.da_occupationID IS NULL OR bm.da_occupationID = '')";
            }

            if (!empty($country)) {
                $where_country = " AND bm.countryID = {$country}";
            }

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province = " AND bm.province IN ($provinceSet)";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district = " AND bm.district IN ($districtSet) ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision = " AND (bm.da_jammiyahDivision IN ($jammiyahDivisionSet) OR bm.da_jammiyahDivision IS NULL OR bm.da_jammiyahDivision = '')";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division = " AND (bm.division IN ($divisionSet) OR bm.division IS NULL OR bm.division = '')";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                //$where_subDivision = " AND bm.subDivision IN ($subDivisionSet)";
                $where_subDivision = " AND (bm.subDivision IN ($subDivisionSet) OR bm.subDivision IS NULL OR bm.subDivision = '') ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision = " AND (bm.da_GnDivision IN ($gnDivisionSet) OR bm.da_GnDivision IS NULL  OR bm.da_GnDivision = '' ) ";
            }

            $data['helpaid'] = $this->db->query("SELECT bm.fullName,bm.address,bmsup.assitanceName,bmsup.Organization,bmsup.amount FROM srp_erp_ngo_beneficiary_othersupportassistance bmsup
            LEFT JOIN srp_erp_ngo_beneficiarymaster bm on bm.benificiaryID = bmsup.beneficiaryID
              WHERE
	bm.projectID = {$projectID} $where_country $where_province $where_district $where_da_jammiyahDivision $where_division $where_subDivision $where_da_GnDivision $where_da_occupation $date 
	")->result_array();

            if (!empty($province)) {
                $provinceSet = join(',', $province);
                $where_province_report = " stateID IN ($provinceSet)";
            } else {
                $where_province_report = " stateID = '' ";
            }

            if (!empty($district)) {
                $districtSet = join(',', $district);
                $where_district_report = " stateID IN  ($districtSet)";
            } else {
                $where_district_report = " stateID = '' ";
            }

            if (!empty($da_jammiyahDivision)) {
                $jammiyahDivisionSet = join(',', $da_jammiyahDivision);
                $where_da_jammiyahDivision_report =
                    " stateID IN ($jammiyahDivisionSet)";
            } else {
                $where_da_jammiyahDivision_report = " stateID = '' ";
            }

            if (!empty($division)) {
                $divisionSet = join(',', $division);
                $where_division_report = " stateID IN($divisionSet)";
            } else {
                $where_division_report = " stateID = '' ";
            }

            if (!empty($subDivision)) {
                $subDivisionSet = join(',', $subDivision);
                $where_subDivision_report = " stateID IN($subDivisionSet)";
            } else {
                $where_subDivision_report = " stateID = '' ";
            }

            if (!empty($da_GnDivision)) {
                $gnDivisionSet = join(',', $da_GnDivision);
                $where_da_GnDivision_report = " stateID IN($gnDivisionSet) ";
            } else {
                $where_da_GnDivision_report = " stateID = '' ";
            }

            $data['country_drop'] = $this->db->query("SELECT CountryDes from  srp_erp_countrymaster where  countryID =$country")->result_array();
            $data['province_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_province_report ")->result_array();
            $data['area_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_district_report ")->result_array();
            $data['da_jammiyahDivision_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_jammiyahDivision_report")->result_array();
            $data['da_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_division_report")->result_array();
            $data['da_sub_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_subDivision_report")->result_array();
            $data['da_sub_gn_division_drop'] = $this->db->query("SELECT Description from  srp_erp_statemaster WHERE  $where_da_GnDivision_report")->result_array();
            $data['project'] = $this->db->query("SELECT projectName from  srp_erp_ngo_projects WHERE ngoProjectID=$projectID")->result_array();

            $data["type"] = "pdf";
            $html = $this->load->view('system/operationNgo/ajax/damage_assesment_report_helpaid_pdf', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }

    }

    function load_project_proposal_print_pdf_approval()
    {
        $proposalID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('proposalID') ?? '');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("SELECT pro.projectImage,pp.proposalID as proposalID,pp.proposalName as ppProposalName,pro.projectName as proProjectName,pp.approvedbyEmpName,pp.approvedDate,DATE_FORMAT(pp.DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(pp.startDate,'{$convertFormat}') AS ppStartDate,DATE_FORMAT(pp.endDate,'{$convertFormat}') AS ppEndDate,DATE_FORMAT(pp.DocumentDate, '%M %Y') as subprojectName,pp.detailDescription as ppDetailDescription,pp.projectSummary as ppProjectSummary,pp.approvedYN as approvedYN,pp.totalNumberofHouses as ppTotalNumberofHouses,pp.floorArea as ppFloorArea,pp.costofhouse as ppCostofhouse,pp.additionalCost as ppAdditionalCost,pp.EstimatedDays as ppEstimatedDays,pp.proposalTitle as ppProposalTitle,pp.processDescription as ppProcessDescription,con.supplierName as contractorName,ca.GLDescription as caBankAccName,ca.bankName as caBankName,ca.bankAccountNumber as caBankAccountNumber FROM srp_erp_ngo_projectproposals pp JOIN srp_erp_ngo_projects pro ON pp.projectID = pro.ngoProjectID LEFT JOIN srp_erp_suppliermaster con ON pp.contractorID = con.supplierAutoID LEFT JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = pp.bankGLAutoID WHERE pp.proposalID = $proposalID  ")->row_array();

        $data['detail'] = $this->db->query("SELECT ppb.beneficiaryID as ppbBeneficiaryID,DATE_FORMAT(bm.registeredDate,'{$convertFormat}') AS bmRegisteredDate,DATE_FORMAT(bm.dateOfBirth,'{$convertFormat}') AS bmDateOfBirth,bm.nameWithInitials as bmNameWithInitials,bm.systemCode as bmSystemCode, CASE bm.ownLandAvailable WHEN 1 THEN 'Yes' WHEN 2 THEN 'No' END as bmOwnLandAvailable,bm.NIC as bmNIC,bm.familyMembersDetail as bmFamilyMembersDetail,bm.reasoninBrief as bmReasoninBrief,ppb.totalSqFt as bmTotalSqFt,ppb.totalCost as bmTotalCost,ppb.totalEstimatedValue as totalEstimatedValue,bm.helpAndNestImage as bmHelpAndNestImage,bm.helpAndNestImage1 as bmHelpAndNestImage1,bm.ownLandAvailableComments as bmOwnLandAvailableComments FROM srp_erp_ngo_projectproposalbeneficiaries ppb LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON ppb.beneficiaryID = bm.benificiaryID WHERE proposalID = $proposalID ")->result_array();

        $data['images'] = $this->db->query("SELECT imageType,imageName FROM srp_erp_ngo_projectproposalimages WHERE ngoProposalID = $proposalID ")->result_array();

        $data['moto'] = $this->db->query("SELECT companyPrintTagline FROM srp_erp_company WHERE company_id = {$companyID}")->row_array();

        $data['proposalID'] = $proposalID;

        $data['output'] = 'view';

        $this->load->view('system/operationNgo/ngo_beneficiary_helpnest_print_approval', $data);


    }

    function convert_project_proposal_to_project()
    {

        echo json_encode($this->OperationNgo_model->convert_project_proposal_to_project());

    }

    function load_project_proposal_to_project()
    {

        echo json_encode($this->OperationNgo_model->load_project_proposal_to_project());

    }

    function load_converted_project_master_view()
    {
        $convertFormat = convert_date_format_sql();
        $text = trim($this->input->post('q') ?? '');
        $companyID = current_companyID();
        $filter = '';

        if ($text != '') {
            $filter = " AND (prmaster.documentSystemCode LIKE '%$text%' OR projectName LIKE '%$text%' )";
        }

        $data['master'] = $this->db->query("Select
prmaster.documentSystemCode,
prmaster.ngoProjectID,
prmaster.proposalID,
prmaster.masterID,
prmaster.projectName as projectname,
 DATE_FORMAT(prmaster.startDate,'" . $convertFormat . "') AS estimatedstartdate,
 DATE_FORMAT(prmaster.endDate,'" . $convertFormat . "') AS estimateenddate,
prostage.percentage as percentage,
prmaster.totalProjectValue as totalProjectValue,
prodetails.totalclaimedamt as claimedamt,
proposal.type,
currencyMaster.CurrencyCode AS CurrencyCode
From 
srp_erp_ngo_projects prmaster

LEFT JOIN srp_erp_ngo_projectproposals proposal on proposal.proposalID = prmaster.proposalID
	LEFT JOIN srp_erp_currencymaster currencyMaster ON currencyMaster.CurrencyID = proposal.transactionCurrencyID
LEFT JOIN (
SELECT 
COALESCE ( SUM( prostage.percentage ), 0 ) AS percentage,ngoProjectID,projectStageID FROM srp_erp_ngo_projectstages prostage
GROUP BY
	ngoProjectID
	)prostage on prostage.ngoProjectID = prmaster.ngoProjectID 
	
	LEFT JOIN (
	
	SELECT 
		COALESCE ( SUM( prostagedetails.amount ), 0 ) AS totalclaimedamt,
	isClaimedYN,prostagedetails.projectStageID,ngoProjectID 
	
	from srp_erp_ngo_projectstagedetails prostagedetails 
	LEFT JOIN srp_erp_ngo_projectstages projectstages on projectstages.projectStageID = prostagedetails.projectStageID
	
	where isClaimedYN =1  
	
	GROUP BY
	ngoProjectID
	
	) 
	prodetails on prodetails.projectStageID = prostage.projectStageID
	
WHERE
	prmaster.companyID = $companyID 
	AND prmaster.proposalID != '' $filter 
ORDER BY
	ngoProjectID DESC")->result_array();

        $this->load->view('system/operationNgo/ajax/load_converted_proposal_project', $data);
    }


    function proposal_cconvertion_to_project()
    {
        $this->form_validation->set_rules("proposalID", 'Proposal', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->converted_proposal_project());
        }
    }

    function fetch_project_proposal_details()
    {
        $data['proposalid'] = $this->input->post('proposalid');
        $this->load->view('system/operationNgo/ajax/converted_project_detail_view', $data);

    }

    function fetch_converted_proposal_details()
    {
        $this->form_validation->set_rules("proposalid", 'Proposal', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->load_converted_project_proposal_details());
        }

    }

    function save_converted_project_details()
    {
        $type =$this->input->post('type');
        $this->form_validation->set_rules("proposalid", 'Proposal', 'trim|required');
        $this->form_validation->set_rules("projectfrom", 'Project from', 'trim|required');
        $this->form_validation->set_rules("projectto", 'Project to', 'trim|required');
        $this->form_validation->set_rules("contractorID", 'Contractor', 'trim|required');
        // $this->form_validation->set_rules("totalproposalcost", 'Total Proposal Cost', 'trim|required');
        if($type == 1 || $type ==" ")
        {
            $this->form_validation->set_rules("totalprojectcost", 'Total Project Cost', 'trim|required');
        }else
        {
            $this->form_validation->set_rules("totalprojectcostproject", 'Total Project Cost', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_converted_project_details());
        }

    }

    function fetch_beneficiarydetails()
    {
        $proposalid = $this->input->post('proposalID');
        $data['header'] = $this->OperationNgo_model->load_project_proposal_beneficiary_details();
        $data['proposaltype'] = $this->input->post('proposaltype');
        $companyID = current_companyID();
        $data['proposalID']= $this->input->post('proposalID');
        $data['subprojectid'] = $this->db->query("SELECT masterID from srp_erp_ngo_projects where proposalID = $proposalid AND companyID = $companyID  ")->row_array();
        $data['project_beneficiaries'] = $this->db->query("SELECT
	benmaster.systemCode AS systemCode,
	benmaster.nameWithInitials,
	benmaster.address,
	CONCAT( benmaster.phoneAreaCodePrimary, ' - ', benmaster.phonePrimary ) AS beneficiarytelephone,
	CONCAT( 	projects.documentSystemCode, ' | ', 	projects.projectName ) AS projectName

FROM
	srp_erp_ngo_projectproposalbeneficiaries propbene
	LEFT JOIN srp_erp_ngo_beneficiarymaster benmaster ON benmaster.benificiaryID = propbene.beneficiaryID 
	LEFT JOIN (SELECT * from srp_erp_ngo_projects pro where pro.proposalID = $proposalid) projects ON projects.proposalID = propbene.proposalID 
WHERE
	propbene.proposalID = $proposalid 
	AND propbene.companyID = $companyID 
	AND isQualified = 1
	GROUP BY
	propbene.proposalBeneficiaryID")->result_array();
        $this->load->view('system/operationNgo/ajax/qualified_beneficiary_project_view', $data);
    }

    function fetch_donordetails()
    {

        $proposalid = $this->input->post('proposalid');
        $data['proposalid'] = $this->input->post('proposalid');
        $companyID = current_companyID();

        $data['proposaltype'] = $this->input->post('proposaltype');

        $data['project_donors'] = $this->db->query("SELECT
prodonor.	proposalID,
donorID,
donor.name as donorname,
commitedAmount,
CONCAT(phoneAreaCodePrimary,' - ',phonePrimary) as donortelephoneno,
CONCAT( 	projects.documentSystemCode, ' | ', 	projects.projectName ) AS projectName,
CurrencyCode
	from 
	srp_erp_ngo_projectproposaldonors prodonor
	LEFT JOIN srp_erp_ngo_donors donor on donor.contactID = prodonor.donorID
		LEFT JOIN srp_erp_ngo_projectproposals proposal ON proposal.proposalID = prodonor.proposalID
	LEFT JOIN srp_erp_currencymaster currencyMaster ON currencyMaster.CurrencyID = proposal.transactionCurrencyID
LEFT JOIN (SELECT * from srp_erp_ngo_projects pro where pro.proposalID = $proposalid) projects ON projects.proposalID = prodonor.proposalID 		
	where 
	prodonor.proposalID = $proposalid
	AND isApproved = 1 
	AND isSubmitted = 1
	AND prodonor.companyID = $companyID
	GROUP BY
	donorID")->result_array();


        $data["header"] = $this->OperationNgo_model->load_project_proposal_donor_details_project();
        $this->load->view('system/operationNgo/ajax/proposal_commited_donors', $data);
    }

    function fetch_proposal_details_view()
    {
        $this->form_validation->set_rules("proposalid", 'Proposal', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->load_proposal_details_view());
        }

    }

    function project_process()
    {
        $this->load->view('system/operationNgo/ajax/project_process');
    }

    function send_proposal_donors()
    {
        $this->form_validation->set_rules("proposalDonourID", 'Proposal', 'trim|required');
        $this->form_validation->set_rules("donorID", 'Proposal', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->send_proposal_donors());
        }
    }

    function update_donor_details()
    {
        $this->form_validation->set_rules('issubmitted', 'Donors ID', 'trim|required');
        $this->form_validation->set_rules('proposaldonor', 'Proposal ID', 'trim|required');
        $this->form_validation->set_rules('issubmit', 'Is qualified', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_donors_status());
        }
    }

    function load_proposal_details()
    {

        echo json_encode($this->OperationNgo_model->load_project_proposal_details());

    }

    function closedproposal_reopen()
    {

        echo json_encode($this->OperationNgo_model->closed_proposal_reopen());

    }

    function load_project_header()
    {
        echo json_encode($this->OperationNgo_model->load_project_header());
    }

    function project_details()
    {
        $this->form_validation->set_rules("projectid", 'Project Id', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->load_project_details());
        }

    }

    function fetch_project_stages()
    {
        $this->form_validation->set_rules("defaultStageID", 'Stage', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->fetch_stages_project());
        }

    }

    function save_project_stages()
    {
        $this->form_validation->set_rules("projectstages[]", 'Project Stage', 'trim|required');
        $this->form_validation->set_rules("stagedescription[]", 'Stage Description', 'trim|required');
        $this->form_validation->set_rules("percentage[]", 'Percentage', 'trim|required');
        $this->form_validation->set_rules("Amount[]", 'Amount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_project_stages());
        }


    }

    function project_steps()
    {

        $proposalid = $this->input->post('proposalid');
        $data['projectid'] = $this->db->query("SELECT projectSubID from srp_erp_ngo_projectproposals where proposalID = $proposalid")->row_array();
        $projectid =  $this->input->post('projectid');
        $companyID = current_companyID();
        $data['percentagetot'] = $this->db->query("SELECT COALESCE(SUM(percentage),0) as percentage FROM srp_erp_ngo_projectstages where  ngoProjectID = $projectid")->row_array();
        $data['project_steps_projectname'] = $this->db->query("SELECT CONCAT( projects.documentSystemCode, ' | ', projects.projectName ) AS projectName,provalue.totalProjectValue As projectvalue, CurrencyCode
 FROM srp_erp_ngo_projectproposals proposal
 LEFT JOIN srp_erp_currencymaster currencyMaster ON currencyMaster.CurrencyID = proposal.transactionCurrencyID
	    LEFT JOIN ( SELECT * FROM srp_erp_ngo_projects pro WHERE pro.proposalID = $proposalid ) projects ON projects.proposalID = proposal.proposalID
	    	LEFT JOIN srp_erp_ngo_projects provalue ON provalue.proposalID = proposal.proposalID 
        WHERE
	    proposal.proposalID = $proposalid 
	    AND proposal.companyID = $companyID")->result_array();

        $data['header'] = $this->db->query("SELECT
	project.*,
	project.projectStageID as projectStageID,
	prodetails.amount,
	prodetails.claimedInvoiceAutoID
FROM
	srp_erp_ngo_projectstages project
	LEFT JOIN(SELECT COALESCE(SUM(amount),0) AS amount,projectStageID,claimedInvoiceAutoID  FROM srp_erp_ngo_projectstagedetails GROUP BY projectStageID)prodetails on prodetails.projectStageID = project.projectStageID
WHERE
	ngoProjectID = $projectid
	GROUP BY 
	 project.projectStageID ")->result_array();
        $data['detail'] = $this->db->query("SELECT 
*,
CONCAT(chart.systemAccountCode,'-',chart.GLDescription) as gldescription
FROM 
srp_erp_ngo_projectstagedetails prodet
LEFT JOIN srp_erp_chartofaccounts chart on chart.GLAutoID = prodet.glcode")->result_array();


        $this->load->view('system/operationNgo/project_step_view', $data);
    }

    function delete_stages_project()
    {
        $this->form_validation->set_rules("projectStageID", 'Project', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_project_stages());
        }

    }

    function project_stage_details()
    {
        $this->form_validation->set_rules("projectStageID", 'Project Stage', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->fetch_project_stages());
        }

    }

    function project_stage_update()
    {
        $this->form_validation->set_rules("stagedescriptionupdate", 'Description', 'trim|required');
        $this->form_validation->set_rules("percentageupdate", 'Percentage', 'trim|required');
        $this->form_validation->set_rules("amountupdate", 'Amount', 'trim|required');
        $this->form_validation->set_rules("projectidstages", 'Project ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_project_stages());
        }

    }

    function save_project_claims()
    {
        $this->form_validation->set_rules("description[]", 'Description', 'trim|required');
        $this->form_validation->set_rules("glcode[]", 'GL Code', 'trim|required');
        $this->form_validation->set_rules("amount[]", 'Amount', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->project_claims());
        }

    }

    function fetch_project_detail()
    {
        $this->form_validation->set_rules("projectStageDetailID", 'Project Stage', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->fetch_project_detail());
        }

    }

    function delete_project_stage_steps()
    {
        $this->form_validation->set_rules("projectStageDetailID", 'Project Stage', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_project_stage_steps());
        }

    }

    function update_project_details()
    {
        $this->form_validation->set_rules("projectStageDetailID", 'Project Stage', 'trim|required');
        $this->form_validation->set_rules("descriptionedit", 'Description', 'trim|required');
        $this->form_validation->set_rules("glcodeedit", 'GL Code', 'trim|required');
        $this->form_validation->set_rules("amountedit", 'Amount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_project_details());
        }

    }

    function fetch_project_description()
    {
        $this->form_validation->set_rules("projectStageID", 'Project Stage', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->fetch_project_description());
        }
    }

    function save_project_step_details()
    {
        $this->form_validation->set_rules('projectStageDetailID[]', 'Project detail id', 'trim|required');
        $this->form_validation->set_rules('invoiceAutoID', 'Inovoice Auto id', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->OperationNgo_model->save_project_step_details());
        }
    }

    function save_project_claim_docdate_narration()
    {
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_project_claim_docdate_narration());
        }
    }

    function supplier_invoice_confirmation()
    {
        $this->form_validation->set_rules('InvoiceAutoID', 'Inovoice Auto id', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->supplier_invoice_confirmation());
        }
    }

    function load_invoice_claimed()
    {
        $projecstageid = trim($this->input->post('projectStageID') ?? '');
        $data['header'] = $this->db->query("select claimedInvoiceAutoID,invoicemaster.transactionAmount,invoicemaster.bookingInvCode,invoicemaster.comments,invoicemaster.supplierName,invoicemaster.paymentTotalAmount,prodet.isClaimedYN
 from  srp_erp_ngo_projectstagedetails prodet
LEFT JOIN(SELECT InvoiceAutoID,bookingInvCode,transactionAmount,comments,supplierName,paymentTotalAmount from srp_erp_paysupplierinvoicemaster GROUP BY InvoiceAutoID) invoicemaster on invoicemaster.InvoiceAutoID = prodet.claimedInvoiceAutoID
where 
projectStageID = $projecstageid 
AND isClaimedYN = 1
GROUP BY
claimedInvoiceAutoID")->result_array();

        $this->load->view('system/operationNgo/view_all_claimed_supplier_invoices', $data);
    }

    function load_payment_voucher_details()
    {
        $invoiceautoid = trim($this->input->post('claimedInvoiceAutoID') ?? '');
        $data['header'] = $this->db->query("select InvoiceAutoID,SUM(paymentvoucherdetails.transactionAmount) as transactionAmount,paymentvouchermaster.PVcode,paymentvouchermaster.partyName,paymentvouchermaster.PVNarration,paymentvoucherdetails.payVoucherAutoId,paymentvouchermaster.confirmedYN,paymentvouchermaster.approvedYN from srp_erp_paymentvoucherdetail paymentvoucherdetails
Left JOIN srp_erp_paymentvouchermaster paymentvouchermaster on paymentvouchermaster.payVoucherAutoId = paymentvoucherdetails.payVoucherAutoId
where 
InvoiceAutoID = $invoiceautoid
AND paymentvouchermaster.confirmedYN = 1
AND paymentvouchermaster.approvedYN = 1 GROUP BY
payVoucherAutoId")->result_array();
        $this->load->view('system/operationNgo/view_all_payment_vouchers', $data);
    }
    function fetch_project_proposal_beneficiary_project()
    {
        $projectID = trim($this->input->post('projectID') ?? '');
        $proposalID = trim($this->input->post('proposalID') ?? '');

        $this->datatables->select('bm.systemCode as systemCode,bm.nameWithInitials as name,bm.benificiaryID as benificiaryID,bm.totalSqFt as totalSqFt,bm.totalCost as totalCost,bm.totalEstimatedValue as totalEstimatedValue', false)
            ->from('srp_erp_ngo_beneficiarymaster as bm')
            ->where('companyID', current_companyID())
            ->where('projectID', $projectID)
            ->where('confirmedYN', 1);
        $this->datatables->where('NOT EXISTS(SELECT proposalBeneficiaryID,beneficiaryID FROM srp_erp_ngo_projectproposalbeneficiaries WHERE srp_erp_ngo_projectproposalbeneficiaries.beneficiaryID = bm.benificiaryID AND companyID =' . current_companyID() . ' AND proposalID =  ' . $proposalID . ')');
        $this->datatables->add_column('estimatedvalue', '<div style="text-align: center;">
<input id="estimatedvalue_$1" name="totalestimatedvalue[]" type="text" class="form-control" value ="$2" ><label for="checkbox">&nbsp;</label> </div>', 'benificiaryID,totalEstimatedValue');
        $this->datatables->add_column('totalsqftadd', '<div style="text-align: center;">
<input id="totalsqft_$1" name="totalsqft[]" type="text" class="form-control" value ="$2" ><label for="checkbox">&nbsp;</label> </div>', 'benificiaryID,totalSqFt');

        $this->datatables->add_column('totalcostadd', '<div style="text-align: center;">
<input id="totalcost_$1" name="totalcost[]" type="text" class="form-control" value ="$2" > </div>', 'benificiaryID,totalCost');

        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1"  data-value="ischeacked" onchange="changeMandatory(this)" type="checkbox" class="columnSelected "  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div><input type="hidden" name="is_cheacked[]" class="changestatus-ischeacked " value= "0" ><input type="hidden" name="is_cheacked_benid[]" class="beneficiaryid-ischeacked" value="$1"> ', 'benificiaryID');
        echo $this->datatables->generate();


    }
    function assign_beneficiary_for_project_direct()
    {

        $this->form_validation->set_rules('selectedItemsSync[]', 'Beneficiary', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->assign_beneficiary_for_project_direct());
        }

    }
    function update_donors_issubmited_status_project()
    {
         $this->form_validation->set_rules('donor[]', 'Donors ID', 'trim|required');
        $this->form_validation->set_rules('proposaldonor[]', 'Proposal ID', 'trim|required');
        //  $this->form_validation->set_rules('status', 'Status', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_donors_is_submited_status_project());
        }
    }


    /* public property damages  */


    function load_publicProperty_da_print_view(){

        $publicPropertyBeneID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('publicPropertyBeneID') ?? '');
        $data['publicProperty_da'] = $this->OperationNgo_model->load_publicProperty_disasterManagment($publicPropertyBeneID);
        $data['benificiaryArray'] = $this->OperationNgo_model->fetch_damage_ass_details_view($publicPropertyBeneID);

        $data['header'] = $this->db->query("SELECT damageItemCategoryID,Description FROM srp_erp_ngo_damageditemcategories")->result_array();

        $data['detail'] = $this->db->query("SELECT damageItemCategoryID,itemDamagedID,damage.Description,ida.damagedAmountClient,itemDescription,Brand,ida.assessedValue,ida.paidAmount as paidAmount FROM srp_erp_ngo_itemdamagedasssesment ida left join srp_erp_ngo_damagetypemaster damage ON damage.damageTypeID = ida.damageTypeID AND damageSubCategory = 'ID' WHERE ida.publicPropertyBeneID = {$publicPropertyBeneID} order by damageItemCategoryID ASC ")->result_array();

        $data['benImages'] = $this->OperationNgo_model->fetch_beneficiary_multiple_images($publicPropertyBeneID);
        $data['html'] = $this->input->post('html');
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/operationNgo/ngo_publicProperty_damageAssesment_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 1);
        }
    }

    function fetch_property_based_subDropdown()
    {
        $data_arr = array();
        $propertyType = $this->input->post('propertyType');
        $province = $this->db->query("SELECT publicPropertyID,publicPropertyDescription FROM srp_erp_ngo_publicproperty_types WHERE masterID = {$propertyType}")->result_array();
        $data_arr = array('' => 'Select a Sub Property');
        if (!empty($province)) {
            foreach ($province as $row) {
                $data_arr[trim($row['publicPropertyID'] ?? '')] = trim($row['publicPropertyDescription'] ?? '');
            }
        }
        echo form_dropdown('subPropertyId', $data_arr, '', 'class="form-control select2" id="subPropertyId"');
    }


    function save_publicProperty_header()
    {
        $templateType = trim($this->input->post('templateType') ?? '');
        $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        $this->form_validation->set_rules("secondaryCode", 'Secondary Reference No', 'trim|required');
        $this->form_validation->set_rules("propertyType", 'Property Type', 'trim|required');
        // $this->form_validation->set_rules("subPropertyId", 'Sub Property', 'trim|required');
        $this->form_validation->set_rules("registeredDate", 'Registered Date', 'trim|required|validate_date');
        $this->form_validation->set_rules("PropertyName", 'Property Name', 'trim|required');
        $this->form_validation->set_rules("PropertyShortCode", 'Property Short Code', 'trim|required');
        $this->form_validation->set_rules("countryCodePrimary", 'Phone (Primary)', 'trim|required');
        $this->form_validation->set_rules("phonePrimary", 'Phone (Primary)', 'trim|required');
        $this->form_validation->set_rules("address", 'Address', 'trim|required');
        $this->form_validation->set_rules("commencementDate", 'Commencement Date', 'trim|required|validate_date');
        $this->form_validation->set_rules("countryID", 'Country', 'trim|required');
        $this->form_validation->set_rules("province", 'Province / State', 'trim|required');
        $this->form_validation->set_rules("district", 'Area / District', 'trim|required');
        $this->form_validation->set_rules("division", 'Division', 'trim|required');
        $this->form_validation->set_rules("subDivision", 'Mahalla', 'trim|required');
        //$this->form_validation->set_rules("projectIDetail", 'Project', 'trim|required');
        //  $this->form_validation->set_rules("ownLandAvailable", 'Is Own Land', 'trim|required');
        //  $this->form_validation->set_rules("totalcostforahouse", 'Total Cost For A Property', 'trim|required');
        $this->form_validation->set_rules("da_jammiyahDivision", 'Jammiya Division', 'trim|required');
        $this->form_validation->set_rules("da_GnDivision", 'GN Division', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_publicProperty_header());
        }
    }

    function load_publicProperty_all_notes()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $publicPropertyBeneID = trim($this->input->post('publicPropertyBeneID') ?? '');

        $where = "companyID = " . $companyID . " AND documentID = 7 AND documentAutoID = " . $publicPropertyBeneID . "";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_notes');
        $this->db->where($where);
        $this->db->order_by('notesID', 'desc');
        $data['notes'] = $this->db->get()->result_array();
        $this->load->view('system/operationNgo/ajax/load_ngo_beneficiary_notes', $data);
    }

    function add_publicProperty_notes()
    {
        $this->form_validation->set_rules('publicPropertyBeneID', 'Public Property ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->add_publicProperty_notes());
        }
    }

    function publicProperty_image_upload(){
        $this->form_validation->set_rules('publicPropertyBeneID', 'public Property ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->publicProperty_image_upload());
        }
    }

    function fetch_damage_ass_details_view()
    {
        $publicPropertyBeneID = $this->input->post('publicPropertyBeneID');
        $data['benificiaryArray'] = $this->OperationNgo_model->fetch_damage_ass_details_view($publicPropertyBeneID);
        $data['type'] = 'view';
        $this->load->view('system/operationNgo/ajax/load_public_damage_ass_view', $data);
    }

    function load_publicProperty_header()
    {
        echo json_encode($this->OperationNgo_model->load_publicProperty_header());
    }


    function load_publicPropertyTemplate_view()
    {
        $ngoProjectID = trim($this->input->post('ngoProjectID') ?? '');

        $this->load->view("system/operationNgo/template/publicProperty-damage-assesment-template");
    }

    function load_monthly_expenditure_pd_view()
    {
        $companyID = current_companyID();
        $publicPropertyBeneID = $this->input->post('publicPropertyBeneID');

        $filter = '';
        if (!empty($publicPropertyBeneID)) {
            $filter = "AND publicPropertyBeneID = {$publicPropertyBeneID} ";
        }

        $data['publicPropertyBeneID'] = $publicPropertyBeneID;

        if (!empty($publicPropertyBeneID)) {
            $data['supportAssitance'] = $this->db->query("SELECT assitanceName,Organization,year,amount FROM srp_erp_ngo_beneficiary_othersupportassistance WHERE companyID = '{$companyID}' AND publicPropertyBeneID = {$publicPropertyBeneID} ORDER BY assistanceID ASC")->result_array();
        }

        if (!empty($publicPropertyBeneID)) {
            $data['benficeryHeader'] = $this->db->query("SELECT da_meNotes,da_meGovAssistantYN,da_meSupportReceivedYN FROM srp_erp_ngo_publicpropertybeneficiarymaster WHERE publicPropertyBeneID = {$publicPropertyBeneID}")->row_array();
        }

        $this->load->view('system/operationNgo/ajax/load_da_monthly_expenditure_view', $data);
    }

    function load_publicPropertyManage_view()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $country = trim($this->input->post('countryID') ?? '');
        $province = trim($this->input->post('province') ?? '');
        $distric = trim($this->input->post('district') ?? '');
        $division = trim($this->input->post('division') ?? '');
        $subdivision = trim($this->input->post('subDivision') ?? '');
        $project = trim($this->input->post('projectID') ?? '');

        $currentemployeeid = $this->common_data['current_userID'];


        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND ((PropertyName Like '%" . $text . "%') OR (bfm.email Like '%" . $text . "%') OR (CONCAT(phoneCountryCodePrimary,phoneAreaCodePrimary,phonePrimary) Like '%" . $text . "%'))";
        }
        $country_search = '';
        if (isset($country) && !empty($country)) {
            $country_search = " AND bfm.countryID = {$country}";
        }
        $search_sorting = '';
        if (isset($sorting) && $sorting != '#') {
            $search_sorting = " AND PropertyName Like '" . $sorting . "%'";
        }
        $filter_status = '';
        if (isset($status) && $status != '') {
            if ($status == 1) {
                $filter_status = " AND confirmedYN = 0";
            } elseif ($status == 2) {
                $filter_status = " AND confirmedYN = 1";
            }
        }
        $province_filter = '';
        if (isset($province) && !empty($province)) {
            $province_filter = " AND bfm.province = {$province}";
        }
        $distric_filter = '';
        if (isset($distric) && !empty($distric)) {
            $distric_filter = " AND bfm.district = {$distric}";
        }

        $division_filter = '';
        if (isset($division) && !empty($division)) {
            $division_filter = " AND bfm.division = {$division}";
        }
        $sub_division_filter = '';
        if (isset($subdivision) && !empty($subdivision)) {
            $sub_division_filter = " AND bfm.subDivision = {$subdivision}";
        }
        $project_filter = '';
        if (isset($project) && !empty($project)) {
            $project_filter = " AND bfm.projectID = $project";
        }

        $where = "bfm.companyID = " . $companyID . $search_string . $search_sorting . $filter_status . $country_search . $province_filter . $distric_filter . $division_filter . $sub_division_filter . $project_filter;

        $data['header'] = $this->db->query("SELECT publicPropertyBeneID,PropertyName,publicPropertyImage,email,CountryDes,bfm.countryID,bfm.province,bfm.district,bfm.division,bfm.subDivision,bfm.projectID,CONCAT(phoneCountryCodePrimary,' - ',phoneAreaCodePrimary,phonePrimary) AS MasterPrimaryNumber,bfm.confirmedYN as confirmedYN,systemCode,projectID FROM srp_erp_ngo_publicpropertybeneficiarymaster bfm LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bfm.countryID WHERE $where  AND bfm.Com_MasterID IS NULL  ORDER BY publicPropertyBeneID DESC ")->result_array();

        $this->load->view('system/operationNgo/ajax/load_publicPropertyManage_view', $data);

    }

    function load_publicPropertyManage_editView()
    {
        $convertFormat = convert_date_format_sql();
        $publicPropertyBeneID = trim($this->input->post('publicPropertyBeneID') ?? '');

        $data['header'] = $this->db->query("SELECT *,CountryDes,DATE_FORMAT(bfm.createdDateTime,'" . $convertFormat . "') AS createdDate,DATE_FORMAT(bfm.modifiedDateTime,'" . $convertFormat . "') AS modifydate,DATE_FORMAT(bfm.registeredDate,'" . $convertFormat . "') AS registeredDate,DATE_FORMAT(bfm.commencementDate,'" . $convertFormat . "') AS commencementDate,bfm.createdUserName as contactCreadtedUser,bfm.email as contactEmail,bfm.phonePrimary as contactPhonePrimary,bfm.phoneSecondary as contactPhoneSecondary,project.projectName as projectName,prptyType.publicPropertyDescription as propTypeDescription,smpro.Description as provinceName,smdis.Description as districtName,smdiv.Description as divisionName,smsubdiv.Description as subDivisionName,bfm.projectID FROM srp_erp_ngo_publicpropertybeneficiarymaster bfm LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = bfm.countryID LEFT JOIN srp_erp_ngo_projects project ON project.ngoProjectID = bfm.projectID LEFT JOIN srp_erp_ngo_publicproperty_types prptyType ON prptyType.publicPropertyID = bfm.propertyType LEFT JOIN srp_erp_statemaster smpro ON smpro.stateID = bfm.province LEFT JOIN srp_erp_statemaster smdis ON smdis.stateID = bfm.district LEFT JOIN srp_erp_statemaster smdiv ON smdiv.stateID = bfm.division LEFT JOIN srp_erp_statemaster smsubdiv ON smsubdiv.stateID = bfm.subDivision WHERE publicPropertyBeneID = " . $publicPropertyBeneID . "")->row_array();

        $data['projects'] = $this->db->query("SELECT projects.projectName FROM srp_erp_ngo_beneficiaryprojects bp LEFT JOIN srp_erp_ngo_projects projects ON projects.ngoProjectID = bp.projectID WHERE bp.publicPropertyBeneID = {$publicPropertyBeneID}")->result_array();

        $this->load->view('system/operationNgo/ajax/load_ngo_publicProperty_edit_view', $data);
    }

    function publicProperty_system_code_generator(){
        $publicPropertyBeneID = trim($this->input->post('publicPropertyBeneID') ?? '');
        echo json_encode($this->OperationNgo_model->publicProperty_system_code_generator($publicPropertyBeneID));
    }

    function publicProperty_confirmation(){
        echo json_encode($this->OperationNgo_model->publicProperty_confirmation());

    }

    function load_human_injury_pd_view()
    {
        $publicPropertyBeneID = trim($this->input->post('publicPropertyBeneID') ?? '');

        $data['detail'] = $this->db->query("SELECT humanInjuryID,hia.effectedPersonName,damage.Description,estimatedAmount,hia.remarks,hia.paidAmount as paidAmount FROM srp_erp_ngo_humaninjuryassesment hia left join srp_erp_ngo_damagetypemaster damage ON damage.damageTypeID = hia.damageTypeID AND damageSubCategory = 'HI' WHERE hia.publicPropertyBeneID = {$publicPropertyBeneID} ")->result_array();

        $this->load->view('system/operationNgo/ajax/load_pd_human_injury_ass_view', $data);
    }

    function load_property_damage_pd_view()
    {
        $publicPropertyBeneID = trim($this->input->post('publicPropertyBeneID') ?? '');

        $data['header'] = $this->db->query("SELECT damageItemCategoryID,Description FROM srp_erp_ngo_damageditemcategories")->result_array();

        $data['detail'] = $this->db->query("SELECT damageItemCategoryID,itemDamagedID,damage.Description,ida.damagedAmountClient,itemDescription,Brand,ida.assessedValue,ida.paidAmount as paidAmount FROM srp_erp_ngo_itemdamagedasssesment ida left join srp_erp_ngo_damagetypemaster damage ON damage.damageTypeID = ida.damageTypeID AND damageSubCategory = 'ID' WHERE ida.publicPropertyBeneID = {$publicPropertyBeneID} order by damageItemCategoryID ASC ")->result_array();

        $this->load->view('system/operationNgo/ajax/load_property_damage_ass_view', $data);
    }

    function save_property_header_damageAssesment()
    {
        $this->form_validation->set_rules('publicPropertyBeneID', 'Public Property ID', 'trim|required');
        $this->form_validation->set_rules('da_typeOfhouseDamage', 'Type of Damage', 'trim|required');
        $this->form_validation->set_rules('da_housingCondition', 'Property Condition', 'trim|required');
        $this->form_validation->set_rules('da_houseCategory', 'Property Category', 'trim|required');
        $this->form_validation->set_rules('da_buildingDamages', 'Building Damages', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->OperationNgo_model->save_property_header_damageAssesment());
        }
    }

    function save_property_header_human_damageAssesment()
    {
        $this->form_validation->set_rules('publicPropertyBeneID', 'Public Property ID', 'trim|required');
        $this->form_validation->set_rules('effectedPersonName', 'Person Name', 'trim|required');;
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->OperationNgo_model->save_property_header_human_damageAssesment());
        }
    }


    function edit_pd_humanInjury_assestment()
    {
        echo json_encode($this->OperationNgo_model->load_pd_human_injury_assestment());
    }

    function save_pd_header_itemdamage_assesment()
    {
        $isInsuranceYN = trim($this->input->post('isInsuranceYN') ?? '');
        $damageItemCategoryID = trim($this->input->post('damageItemCategoryID') ?? '');
        $this->form_validation->set_rules('publicPropertyBeneID', 'Public Property ID', 'trim|required');
        $this->form_validation->set_rules('damageItemCategoryID', 'Category', 'trim|required');
        $this->form_validation->set_rules('damageTypeID', 'Type of Damage', 'trim|required');
        $this->form_validation->set_rules('damageConditionID', 'Item Condition', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Description', 'trim|required');
        if ($isInsuranceYN == 1) {
            $this->form_validation->set_rules('insuranceTypeID', 'Insurance Type', 'trim|required');
            //$this->form_validation->set_rules('insuranceRemarks', 'Insurance Remarks', 'trim|required');
        }
        if($damageItemCategoryID == 3){
            $this->form_validation->set_rules('vehicleAutoID', 'Vehicle Type', 'trim|required');

        }
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('e', $error_message));
        } else {
            echo json_encode($this->OperationNgo_model->save_pd_header_itemdamage_assesment());
        }
    }

    function load_publicProperty_multiple_img_view()
    {
        $publicPropertyBeneID = trim($this->input->post('publicPropertyBeneID') ?? '');
        $docDet = $this->OperationNgo_model->load_publicProperty_multiple_images();
        //echo '<pre>';print_r($docDet); echo '</pre>'; die();
        $data['docDet'] = $docDet;
        $data['publicPropertyBeneID'] = $publicPropertyBeneID;
        $this->load->view('system/operationNgo/ajax/load_publicProperty_multiple_img_view', $data);
    }

    function upload_publicProperty_multiple_image()
    {
        //$this->form_validation->set_rules('document', 'Document', 'trim|required');
        $this->form_validation->set_rules('publicPropertyBeneID', 'Public Property ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->upload_publicProperty_multiple_image());
        }
    }

    function delete_publicProperty_multiple_image()
    {
        $this->form_validation->set_rules('beneficiaryImageID', 'beneficiary Image ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_publicProperty_multiple_image());
        }
    }


    function update_publicProperty_multiple_image()
    {
        $this->form_validation->set_rules('beneficiaryImageID', 'Beneficiary Image ID', 'trim|required');
        $this->form_validation->set_rules('publicPropertyBeneID', 'Public Property ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->update_publicProperty_multiple_image());
        }
    }

    function load_publicProperty_documents_view()
    {
        $projectID = trim($this->input->post('projectID') ?? '');
        $publicPropertyBeneID = trim($this->input->post('publicPropertyBeneID') ?? '');
        $docDet = $this->OperationNgo_model->load_publicProperty_documents();
        //echo '<pre>';print_r($docDet); echo '</pre>'; die();

        $data['docDet'] = $docDet;
        $data['projectID'] = $projectID;
        $data['publicPropertyBeneID'] = $publicPropertyBeneID;
        $this->load->view('system/operationNgo/ajax/load_publicProperty_document_view', $data);
    }

    function load_publicProperty_documents_view_forEdit()
    {
        $publicPropertyBeneID = trim($this->input->post('publicPropertyBeneID') ?? '');
        $docDet = $this->OperationNgo_model->load_publicProperty_documents();
        $data['docDet'] = $docDet;
        $this->load->view('system/operationNgo/ajax/load_publicProperty_document_view_forEdit', $data);
    }

    function save_publicProperty_document()
    {
        $this->form_validation->set_rules('document', 'Document', 'trim|required');
        $this->form_validation->set_rules('publicPropertyBeneID', 'Public Property ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->save_publicProperty_document());
        }
    }

    function delete_publicProperty_document()
    {
        $this->form_validation->set_rules('DocDesFormID', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->OperationNgo_model->delete_publicProperty_document());
        }
    }

    function delete_propertyDamage_master()
    {
        echo json_encode($this->OperationNgo_model->delete_propertyDamage_master());
    }


    function fetch_property_relate_search()
    {
        echo json_encode($this->OperationNgo_model->fetch_property_relate_search());
    }

}
