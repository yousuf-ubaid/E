<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CommunityNgo extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('CommunityNgo_model');
        $this->load->helper('community_ngo_helper');
    }

    /* Starting Hish */

    //Area setup
    function load_ngo_area_setup()
    {
        $company_id = $this->common_data['company_data']['company_id'];
        $where = '';
        if (!empty($company_id)) {
            $where = "WHERE company_id = $company_id";
        }

        $data = $this->db->query("select * FROM srp_erp_company $where ")->row_array();

        $filter = '';
        $countryid = $data['countryID'];
        if (!empty($countryid)) {
            $filter = "WHERE countryID = $countryid ";
        }

        $allCountrys = $this->db->query("SELECT * FROM srp_erp_countrymaster $filter")->result_array();
        $data['country'] = $allCountrys;
        $this->load->view('system/communityNgo/ajax/load_ngo_region_setup', $data);
    }

    function new_sub_division()
    {
        $divisionPolicy = fetch_ngo_policies('GN');
        $this->form_validation->set_rules('sub_division_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('hd_sub_division_countryID', 'Country', 'trim|required');
        $this->form_validation->set_rules('sub_division_shortCode', 'Short Code', 'trim|required');
        $this->form_validation->set_rules('hd_sub_division_districtID', 'District', 'trim|required');
        $this->form_validation->set_rules('divisionTypeCode', 'Division Type', 'trim|required');

        $divisionTypeCode = $this->input->post('divisionTypeCode');
        if ($divisionTypeCode == 'GN') {
            $this->form_validation->set_rules('divisionNo', 'Division No', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->new_sub_division());
        }
    }

    function new_beneficiary_division()
    {
        $districtPolicy = fetch_ngo_policies('JD');
        $this->form_validation->set_rules('division_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('hd_division_countryID', 'Country', 'trim|required');
        $this->form_validation->set_rules('division_shortCode', 'Short Code', 'trim|required');
        $this->form_validation->set_rules('hd_division_districtID', 'District', 'trim|required');
        $this->form_validation->set_rules('divisionTypeCode', 'Division Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->new_beneficiary_division());
        }
    }


    function load_ngo_area_setupDetail()
    {
        echo json_encode($this->CommunityNgo_model->load_ngo_area_setupDetail());
    }

    /*Community Members*/

    function load_communityMemberDetails()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');

        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND ((MemberCode Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (division.Description Like '%" . $text . "%') OR (area.Description Like '%" . $text . "%') OR (name = '" . $text . "') OR (CNIC_No Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";
        }
        $search_sorting = '';
        if (isset($sorting)) {
            $search_sorting = " AND CName_with_initials Like '" . $sorting . "%'";
        }
        $deleted = " AND isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $search_string . $search_sorting . $deleted;

        $data['master'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,area.Description AS Region ,division.Description AS GS_Division FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster division ON division.stateID = srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster area ON area.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE $where ORDER BY Com_MasterID DESC ")->result_array();
        $this->load->view('system/communityNgo/ajax/load_com_ngo_members', $data);
    }

    function check_isNIC_available()
    {
        $Com_MasterID = $this->input->post('Com_MasterID');
        $NIC = $this->input->post('NIC');

        if ($Com_MasterID) {
            $result = $this->db->query("SELECT * FROM srp_erp_ngo_com_communitymaster
                                    WHERE Com_MasterID != {$Com_MasterID} AND CNIC_No = '$NIC' ")->result_array();
        } else {
            $result = $this->db->query("SELECT * FROM srp_erp_ngo_com_communitymaster
                                    WHERE CNIC_No = '$NIC' ")->result_array();
        }

        if ((!empty($NIC) || $NIC !='' || $NIC !=NULL) && !empty($result)) {
            echo json_encode(['e', 'NIC No has already been available. Please try again.']);
        } else {
            echo json_encode(['s', '']);
        }
    }

    function loadEmployees()
    {
        $companyID = current_companyID();

        $GenderIDArr = $this->input->post('GenderID');
        $GS_DivisionArr = $this->input->post('GS_Division');
        $RegionIDArr = $this->input->post('RegionID');
        $Com_MasterIDArr = $this->input->post('Com_MasterID');

        $GenderIDIN = "";
        if (!empty($GenderIDArr)) {
            $GenderIDIN = 'AND GenderID IN (' . join(",", $GenderIDArr) . ' )';
        }

        $GS_DivisionIN = "";
        if (!empty($GS_DivisionArr)) {
            $GS_DivisionIN = 'AND GS_Division IN (' . join(",", $GS_DivisionArr) . ' )';
        }

        $RegionIDIN = "";
        if (!empty($RegionIDArr)) {
            $RegionIDIN = 'AND RegionID IN (' . join(",", $RegionIDArr) . ' )';
        }

        $result = $this->db->query("SELECT Com_MasterID,CName_with_initials FROM srp_erp_ngo_com_communitymaster
                                    WHERE isDeleted = 0 AND companyID = {$companyID} $GenderIDIN $GS_DivisionIN $RegionIDIN")->result_array();
        $data['members'] = $result;

        echo $this->load->view('system/communityNgo/ajax/load_com_ngo_members', $data, true);

    }

    function fetch_all_member_details()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $member_filter = '';
        $gender_filter = '';
        $region_filter = '';
        $division_filter = '';

        $member = $this->input->post('Com_MasterID');
        if (!empty($member) && $member != 'null') {
            $members = array($this->input->post('Com_MasterID'));
            $whereIN = "( " . join("' , '", $members) . " )";
            $member_filter = " AND Com_MasterID IN " . $whereIN;
        }

        $GenderID = $this->input->post('GenderID');
        if (!empty($GenderID) && $GenderID != 'null') {
            $GenderID = array($this->input->post('GenderID'));
            $whereIN = "( " . join("' , '", $GenderID) . " )";
            $gender_filter = " AND srp_erp_ngo_com_communitymaster.GenderID IN " . $whereIN;
        }

        $RegionID = $this->input->post('RegionID');
        if (!empty($RegionID) && $RegionID != 'null') {
            $RegionID = array($this->input->post('RegionID'));
            $whereIN = "( " . join("' , '", $RegionID) . " )";
            $region_filter = " AND srp_erp_ngo_com_communitymaster.RegionID IN " . $whereIN;
        }

        $GS_Division = $this->input->post('GS_Division');
        if (!empty($GS_Division) && $GS_Division != 'null') {
            $GS_Division = array($this->input->post('GS_Division'));
            $whereIN = "( " . join("' , '", $GS_Division) . " )";
            $division_filter = " AND srp_erp_ngo_com_communitymaster.GS_Division IN " . $whereIN;
        }

        $isActive = $this->input->post('isActive');

        switch ($isActive) {
            case '1':
                $active_filter = "AND isActive = '1' ";
                break;
            case '0':
                $active_filter = "AND isActive = '0' ";
                break;
            default:
                $active_filter = '';
        }

        $deleted = " AND isDeleted = '0' ";
        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted;

        $this->datatables->select("Com_MasterID,MemberCode,CName_with_initials,CImage,CNIC_No,isActive,isDeleted,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,area.Description AS Region ,division.Description AS GS_Division");
        $this->datatables->join('srp_titlemaster', '(srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID)', 'left');
        $this->datatables->join('srp_erp_gender', '(srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID)', 'left');
        $this->datatables->join('srp_erp_statemaster division', '(division.stateID = srp_erp_ngo_com_communitymaster.GS_Division)', 'left');
        $this->datatables->join('srp_erp_statemaster area', '(area.stateID = srp_erp_ngo_com_communitymaster.RegionID)', 'left');
        $this->datatables->from('srp_erp_ngo_com_communitymaster');

        $this->datatables->edit_column('image', '$1', 'viewImage(CImage)');
        $this->datatables->add_column('CName_with_initials', '$1', 'view_detail_modal(Com_MasterID,MemberCode,CName_with_initials)');

        $this->datatables->where($where . $member_filter . $gender_filter . $region_filter . $division_filter . $active_filter);
        $this->datatables->add_column('status', '$1', 'member_active_status(isActive)');
        $this->datatables->add_column('edit', '$1', 'load_com_member_action(Com_MasterID,isActive,isDeleted,MemberCode,CName_with_initials)');
        echo $this->datatables->generate();
    }

    function load_memberDetailsView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $Com_MasterID = trim($this->input->post('Com_MasterID') ?? '');

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . " AND srp_erp_ngo_com_communitymaster.Com_MasterID = '" . $Com_MasterID . "' ";

        $data['master'] = $this->db->query("SELECT *,divisionc.stateID,divisionc.Description AS GS_DivisionSt,srp_erp_ngo_com_communitymaster.createdDateTime,srp_erp_ngo_com_communitymaster.createdUserName,srp_erp_ngo_com_communitymaster.modifiedDateTime FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID = srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster area ON area.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_bloodgrouptype ON srp_erp_bloodgrouptype.BloodTypeID = srp_erp_ngo_com_communitymaster.BloodGroupID LEFT JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus WHERE $where ")->row_array();
        $this->load->view('system/communityNgo/ajax/load_ngo_memberImage', $data);
    }


    // other attachments

    function fetch_other_attachments()
    {
        $this->db->where('documentAutoID', $this->input->post('other_documentSystemCode'));
        $this->db->where('documentID', $this->input->post('other_documentID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_ngo_attachments')->result_array();
        $result = '';
        $x = 1;
        if (!empty($data)) {
            foreach ($data as $val) {
                $burl = base_url("attachments") . '/' . $val['myFileName'];
                $type = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';
                if ($val['fileType'] == '.xlsx') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xls') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xlsxm') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.doc') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.docx') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.ppt') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.pptx') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpeg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.gif') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.png') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.txt') {
                    $type = '<i class="color fa fa-file-text-o" aria-hidden="true"></i>';
                }
                $link = generate_encrypt_link_only($burl);
                $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_other_attachments(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';


            }
        } else {
            $result = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }
        echo json_encode($result);
    }


    function upload_other_attachments($description = true)
    {
        if ($description) {
            $this->form_validation->set_rules('other_attachmentDescription', 'Attachment Description', 'trim|required');
            $this->form_validation->set_rules('other_documentSystemCode', 'documentSystemCode', 'trim|required');
            $this->form_validation->set_rules('other_document_name', 'document_name', 'trim|required');
            $this->form_validation->set_rules('other_documentID', 'documentID', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('other_documentID') ?? ''));
            $num = $this->db->get('srp_erp_ngo_attachments')->result_array();
            $file_name = 'Member_other_' . $this->input->post('other_documentSystemCode') . '_' . $this->input->post('other_documentID') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload("other_document_file")) {
                echo json_encode(array('status' => 0, 'type' => 'w', 'message' => 'Upload failed ' . $this->upload->display_errors()));
            } else {
                $upload_data = $this->upload->data();
                //$fileName                       = $file_name.'_'.$upload_data["file_ext"];
                $data['documentID'] = trim($this->input->post('other_documentID') ?? '');
                $data['documentAutoID'] = trim($this->input->post('other_documentSystemCode') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('other_attachmentDescription') ?? '');
                $data['myFileName'] = $file_name . $upload_data["file_ext"];
                $data['fileType'] = trim($upload_data["file_ext"]);
                $data['fileSize'] = trim($upload_data["file_size"]);
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
                    echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.'));
                }
            }
        }
    }


    function load_member_all_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $Com_MasterID = trim($this->input->post('Com_MasterID') ?? '');

        $where = "companyID = " . $companyid . " AND ( documentID = '7' OR documentID = '8') AND documentAutoID = " . $Com_MasterID . "";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_attachments');
        $this->db->where($where);
        $data['attachment'] = $this->db->get()->result_array();

        $whereSickness = "srp_erp_ngo_attachments.companyID = " . $companyid . " AND documentID = '9' AND Com_MasterID = " . $Com_MasterID . "";
        $this->db->select('*');
        $this->db->join('srp_erp_ngo_com_memberpersickness', '(srp_erp_ngo_com_memberpersickness.memPSicknessID = srp_erp_ngo_attachments.documentAutoID)', 'left');
        $this->db->from('srp_erp_ngo_attachments');
        $this->db->where($whereSickness);
        $data['sickness_attachment'] = $this->db->get()->result_array();

        $whereOccupation = "srp_erp_ngo_attachments.companyID = " . $companyid . " AND documentID = '10' AND Com_MasterID = " . $Com_MasterID . "";
        $this->db->select('*');
        $this->db->join('srp_erp_ngo_com_memjobs', '(srp_erp_ngo_com_memjobs.MemJobID = srp_erp_ngo_attachments.documentAutoID)', 'left');
        $this->db->from('srp_erp_ngo_attachments');
        $this->db->where($whereOccupation);
        $data['occupation_attachment'] = $this->db->get()->result_array();

        $whereQualification = "srp_erp_ngo_attachments.companyID = " . $companyid . " AND documentID = '11' AND Com_MasterID = " . $Com_MasterID . "";
        $this->db->select('*');
        $this->db->join('srp_erp_ngo_com_qualifications', '(srp_erp_ngo_com_qualifications.QualificationID = srp_erp_ngo_attachments.documentAutoID)', 'left');
        $this->db->from('srp_erp_ngo_attachments');
        $this->db->where($whereQualification);
        $data['qualification_attachment'] = $this->db->get()->result_array();

        $this->load->view('system/communityNgo/ajax/load_ngo_member_attachments', $data);
    }

    function load_memberStatus_attachments()
    {
        $this->db->where('documentAutoID', $this->input->post('Com_MasterID'));
        $this->db->where('documentID', '8');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_ngo_attachments')->result_array();

        $result = '';
        $x = 1;
        if (!empty($data)) {
            foreach ($data as $val) {
                $burl = base_url("attachments") . '/' . $val['myFileName'];
                $type = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';
                if ($val['fileType'] == '.xlsx') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xls') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xlsxm') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.doc') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.docx') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.ppt') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.pptx') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpeg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.gif') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.png') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.txt') {
                    $type = '<i class="color fa fa-file-text-o" aria-hidden="true"></i>';
                }

                $link = generate_encrypt_link_only($burl);
                $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_member_attachment(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';

            }
        } else {
            $result = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }
        echo json_encode($result);
    }

    function status_attachment_upload($description = true)
    {
        if ($description) {
            $this->form_validation->set_rules('status_attachmentDescription', 'Attachment Description', 'trim|required');
            $this->form_validation->set_rules('status_documentSystemCode', 'documentSystemCode', 'trim|required');
            $this->form_validation->set_rules('status_document_name', 'document_name', 'trim|required');
            $this->form_validation->set_rules('status_documentID', 'documentID', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('status_documentID') ?? ''));
            $num = $this->db->get('srp_erp_ngo_attachments')->result_array();
            $file_name = 'Member_Status_' . $this->input->post('status_documentSystemCode') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload("document_file")) {
                echo json_encode(array('status' => 0, 'type' => 'w', 'message' => 'Upload failed ' . $this->upload->display_errors()));
            } else {
                $upload_data = $this->upload->data();
                //$fileName                       = $file_name.'_'.$upload_data["file_ext"];
                $data['documentID'] = trim($this->input->post('status_documentID') ?? '');
                $data['documentAutoID'] = trim($this->input->post('status_documentSystemCode') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('status_attachmentDescription') ?? '');
                $data['myFileName'] = $file_name . $upload_data["file_ext"];
                $data['fileType'] = trim($upload_data["file_ext"]);
                $data['fileSize'] = trim($upload_data["file_size"]);
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
                    echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.'));
                }
            }
        }
    }

    function delete_member_image()
    {
        $memberID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("uploads/NGO/communitymemberImage");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {
            $upData = array(
                'CImage' => '',
            );
            $this->db->where('Com_MasterID', $memberID)->update('srp_erp_ngo_com_communitymaster', $upData);
            echo json_encode(TRUE);
        }
    }

    function delete_member_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {
            $this->db->delete('srp_erp_ngo_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(TRUE);
        }
    }

    function ngo_Memberattachement_upload()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        $this->form_validation->set_rules('documentAutoID', 'Document Auto ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_ngo_attachments')->result_array();
            $file_name = 'Member_' . $this->input->post('documentAutoID') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload("document_file")) {
                echo json_encode(array('status' => 0, 'type' => 'w',
                    'message' => 'Upload failed ' . $this->upload->display_errors()));
            } else {
                $upload_data = $this->upload->data();

                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');

                $docExpiryDate = trim($this->input->post('docExpiryDate') ?? '');
                $date_format_policy = date_format_policy();
                $data['docExpiryDate'] = (!empty($docExpiryDate)) ? input_format_date($docExpiryDate, $date_format_policy) : null;

                $data['myFileName'] = $file_name . $upload_data["file_ext"];
                $data['fileType'] = trim($upload_data["file_ext"]);
                $data['fileSize'] = trim($upload_data["file_size"]);
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
                    echo json_encode(array('status' => 0, 'type' => 'e',
                        'message' => 'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 1, 'type' => 's',
                        'message' => 'Successfully ' . trim($this->input->post('attachmentDescription') ?? '') . ' uploaded.'));
                }
            }
        }
    }

    //export to excel
    function export_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Community Member List');
        $this->load->database();
        $data = $this->fetch_member_for_excel();

        $header = ['#', 'Member Code', 'Full Name', 'Name with Initial', 'Other Name', 'Date of Birth', 'Age', 'Gender', 'NIC', ' Blood Group', 'Marital Status', 'Phone (Primary)', 'Phone (Secondary)', 'Email', 'Contact Address', 'Permanent Address', 'House No', 'Area / Mahalla', 'GS Division', 'GS No', 'Status'];
        $member = $data['member'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Community Member List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:U4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($member, null, 'A6');
        ob_clean();
        ob_start(); # added
        $filename = 'Member Details.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        ob_clean();
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function fetch_member_for_excel()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $member_filter = '';
        $gender_filter = '';
        $region_filter = '';
        $division_filter = '';
        $active_filter = '';

        $member = $this->input->post('Com_MasterID');
        if (!empty($member) && $member != 'null') {
            $whereIN = "( " . join(" , ", $member) . " )";
            $member_filter = " AND Com_MasterID IN " . $whereIN;
        }

        $GenderID = $this->input->post('GenderID');
        if (!empty($GenderID) && $GenderID != 'null') {
            $whereIN = "( " . join(" , ", $GenderID) . " )";
            $gender_filter = " AND srp_erp_ngo_com_communitymaster.GenderID IN " . $whereIN;
        }

        $RegionID = $this->input->post('RegionID');
        if (!empty($RegionID) && $RegionID != 'null') {
            $whereIN = "( " . join(" , ", $RegionID) . " )";
            $region_filter = " AND srp_erp_ngo_com_communitymaster.RegionID IN " . $whereIN;
        }

        $GS_Division = $this->input->post('GS_Division');
        if (!empty($GS_Division) && $GS_Division != 'null') {
            $whereIN = "( " . join(" , ", $GS_Division) . " )";
            $division_filter = " AND srp_erp_ngo_com_communitymaster.GS_Division IN " . $whereIN;
        }

        $isActive = $this->input->post('isActive');
        if (!empty($isActive) && $isActive != 'null') {
            $whereIN = "( " . join(" , ", $isActive) . " )";
            $active_filter = " AND srp_erp_ngo_com_communitymaster.isActive IN " . $whereIN;
        }

        $deleted = " AND isDeleted = '0' ";
        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $active_filter . $division_filter . $region_filter . $gender_filter . $member_filter;

        $details = $this->db->query("SELECT *,division.Description AS GS_Division,area.Description AS Area  FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_statemaster division ON division.stateID = srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster area ON area.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_bloodgrouptype ON srp_erp_bloodgrouptype.BloodTypeID = srp_erp_ngo_com_communitymaster.BloodGroupID LEFT JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus  WHERE $where ORDER BY Com_MasterID DESC ")->result_array();

        $data = array();

        $a = 1;
        foreach ($details as $row) {

            if ($row['isActive'] == 1) {
                $isActive = 'Active';
            } else {
                $isActive = 'In-active';
            }

            $data[] = array(
                'Num' => $a,
                'MemberCode' => $row['MemberCode'],
                'CFullName' => $row['CFullName'],
                'CName_with_initials' => $row['CName_with_initials'],
                'OtherName' => $row['OtherName'],
                'CDOB' => $row['CDOB'],
                'Age' => $row['Age'],
                'name' => $row['name'],
                'CNIC_No' => $row['CNIC_No'],
                'BloodDescription' => $row['BloodDescription'],
                'CurrentStatus' => $row['maritalstatus'],
                'TP_Mobile' => $row['TP_Mobile'],
                'TP_home' => $row['TP_home'],
                'EmailID' => $row['EmailID'],
                'C_Address' => $row['C_Address'],
                'P_Address' => $row['P_Address'],
                'HouseNo' => $row['HouseNo'],
                'Description' => $row['Area'],
                'GS_Division' => $row['GS_Division'],
                'GS_No' => $row['GS_No'],
                'isActive' => $isActive,
            );
            $a++;
        }

        return ['member' => $data];

    }

    function delete_community_members()
    {
        echo json_encode($this->CommunityNgo_model->delete_community_members());
    }

    function save_communityMember()
    {
        $this->form_validation->set_rules('TitleID', 'Title', 'trim|required');
        $this->form_validation->set_rules('CFullName', 'Full Name', 'trim|required');
        $this->form_validation->set_rules('CName_with_initials', 'Name with Initial', 'trim|required');
        $this->form_validation->set_rules('CDOB', 'Date of Birth', 'trim|required');
        $this->form_validation->set_rules('GenderID', 'Gender', 'trim|required');
        $this->form_validation->set_rules('C_Address', 'Contact Address', 'trim|required');
        $this->form_validation->set_rules('TP_Mobile', 'Phone (Primary)', 'trim|required');
        $this->form_validation->set_rules('CountryCodePrimary', 'Country Code', 'trim|required');
        $this->form_validation->set_rules('HouseNo', 'House No', 'trim|required');
        $this->form_validation->set_rules('RegionID', 'Region', 'trim|required');
        $this->form_validation->set_rules('GS_Division', 'GS Division', 'trim|required');
        $this->form_validation->set_rules('GS_No', 'GS No', 'trim|required');
        $this->form_validation->set_rules('countyID', 'Country', 'trim|required');
        //  $this->form_validation->set_rules('jammiyahDivisionID', 'Jammiyah Division', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_communityMember());
        }
    }

    function save_communityMemberStatus()
    {
        $this->form_validation->set_rules('DeactivatedFor', 'Reason', 'trim|required');
        $this->form_validation->set_rules('deactivatedDate', 'Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_communityMemberStatus());
        }
    }

    function load_member()
    {
        echo json_encode($this->CommunityNgo_model->load_member());
    }

    function get_gs_division_no()
    {
        $id = $this->input->post('division');

        if ($id) {
            $data = $this->db->query("SELECT divisionNo FROM srp_erp_statemaster  WHERE stateID = {$id} AND type = 4 ")->row_array();
            echo json_encode($data['divisionNo']);
        } else {
            echo json_encode('');
        }
    }

    public function load_memberOtherDetails_View()
    {
        $Com_MasterID = $this->input->post('Com_MasterID');
        $companyID = current_companyID();
        $data['Qualification'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_qualifications AS t1
                                        JOIN srp_erp_ngo_com_degreecategories AS t2 ON t2.DegreeID = t1.DegreeID
                                        LEFT JOIN srp_erp_ngo_com_universities AS t3 ON t3.UniversityID = t1.UniversityID
                                        LEFT JOIN srp_erp_ngo_com_grades AS t4 ON t4.gradeComID = t1.gradeComID
                                        WHERE Com_MasterID = {$Com_MasterID} AND t1.companyID={$companyID}")->result_array();

        $data['Language'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_memberlanguage AS t1
                                        JOIN srp_erp_lang_languages AS t2 ON t2.languageID = t1.LanguageID
                                        WHERE Com_MasterID = {$Com_MasterID} AND t1.companyID={$companyID}")->result_array();

        $data['Occupation'] = $this->db->query("SELECT *,t6.description AS Medium,t8.description AS Specialization FROM srp_erp_ngo_com_memjobs AS t1
                                        LEFT JOIN srp_erp_ngo_com_jobcategories AS t2 ON t2.JobCategoryID = t1.JobCategoryID
                                        LEFT JOIN srp_erp_ngo_com_occupationtypes AS t3 ON t3.OccTypeID = t1.OccTypeID
                                        LEFT JOIN srp_erp_ngo_com_grades AS t4 ON t4.gradeComID = t1.gradeComID
                                        LEFT JOIN srp_erp_ngo_com_schools AS t5 ON t1.schoolComID = t5.schoolComID
                                        LEFT JOIN srp_erp_lang_languages AS t6 ON t6.languageID = t1.LanguageID
                                        LEFT JOIN srp_erp_ngo_com_schooltypes AS t7 ON t1.schoolTypeID = t7.schoolTypeID
                                        LEFT JOIN srp_erp_ngo_com_jobspecialization AS t8 ON t1.specializationID = t8.specializationID
                                        WHERE Com_MasterID = {$Com_MasterID} AND t1.companyID={$companyID}")->result_array();

        $data['Sickness'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_memberpersickness AS t1
                                        JOIN srp_erp_ngo_com_permanent_sickness AS t2 ON t2.sickAutoID = t1.sickAutoID
                                        WHERE Com_MasterID = {$Com_MasterID} AND t1.companyID={$companyID}")->result_array();

        $data['VehicleConfig'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_membervehicles AS t1
                                        JOIN srp_erp_ngo_com_vehicles_master AS t2 ON t2.vehicleAutoID = t1.vehicleAutoID
                                        WHERE Com_MasterID = {$Com_MasterID} AND t1.companyID={$companyID}")->result_array();

        $data['HelpReqConGv'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_memberhelprequirements AS t1
                                        JOIN srp_erp_ngo_com_helprequirements AS t2 ON t2.helpRequireID = t1.helpRequireID
                                        WHERE t1.companyID={$companyID} AND Com_MasterID = {$Com_MasterID} AND helpRequireType='GOV'")->result_array();

        $data['HelpReqConPv'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_memberhelprequirements AS t1
                                        JOIN srp_erp_ngo_com_helprequirements AS t2 ON t2.helpRequireID = t1.helpRequireID
                                        WHERE t1.companyID={$companyID} AND Com_MasterID = {$Com_MasterID} AND helpRequireType='PVT' ")->result_array();

        $data['HelpReqConCs'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_memberhelprequirements AS t1
                                        JOIN srp_erp_ngo_com_helprequirements AS t2 ON t2.helpRequireID = t1.helpRequireID
                                        WHERE t1.companyID={$companyID} AND Com_MasterID = {$Com_MasterID} AND helpRequireType='CONS' ")->result_array();

        $data['willingToHelp'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_memberwillingtohelp AS t1
                                        JOIN srp_erp_ngo_com_helpcategories AS t2 ON t2.helpCategoryID = t1.helpCategoryID
                                        WHERE t1.companyID={$companyID} AND Com_MasterID = {$Com_MasterID} ")->result_array();

        $this->load->view('system/communityNgo/ajax/ngo_hi_memQualification', $data);
    }

    /*member qualification*/
    public function saveQualification()
    {
        $this->form_validation->set_rules('DegreeID', 'Qualification Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->saveQualification());
        }
    }

    function editQualification()
    {
        $id = $this->input->post('id');
        $companyID = current_companyID();
        $data = $this->db->query("SELECT * FROM srp_erp_ngo_com_qualifications WHERE QualificationID ={$id} AND companyID = {$companyID}")->row_array();
        echo json_encode($data);
    }

    public function updateQualification()
    {
        $this->form_validation->set_rules('DegreeID', 'Degree', 'required');
        $this->form_validation->set_rules('hidden-id', 'Qualification ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->updateQualification());
        }
    }

    public function deleteQualification()
    {
        $this->form_validation->set_rules('hidden-id', 'Qualification ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->deleteQualification());
        }
    }

    /*end member qualification*/

    /*Start Member Occupation*/

    function fetch_medium_based_school()
    {
        $schoolComID = $this->input->post('schoolComID');
        if (!empty($schoolComID)) {
            $medium = $this->db->query("SELECT srp_erp_lang_languages.languageID,srp_erp_lang_languages.description FROM srp_erp_ngo_com_schoolmedium INNER JOIN srp_erp_lang_languages ON srp_erp_lang_languages.languageID = srp_erp_ngo_com_schoolmedium.LanguageID WHERE schoolComID = {$schoolComID} ")->result_array();
        }

        echo '<option value="">Select a Medium</option>';
        if (!empty($medium)) {
            foreach ($medium as $row) {
                echo '<option value="' . trim($row['languageID'] ?? '') . '">' . trim($row['description'] ?? '') . '</option>';
            }
        }
    }

    function fetch_address_based_school()
    {
        $schoolComID = $this->input->post('schoolComID');
        if (!empty($schoolComID)) {
            $sch_address = $this->db->query("SELECT address FROM srp_erp_ngo_com_schools WHERE schoolComID = {$schoolComID} ")->row_array();
            if ($sch_address['address']) {
                $address = $sch_address['address'];
            } else {
                $address = '';
            }
        } else {
            $address = '';
        }
        echo $address;
    }

    function fetch_job_based_specialization()
    {
        $JobCategoryID = $this->input->post('JobCategoryID');
        if (!empty($JobCategoryID)) {
            $specialization = $this->db->query("SELECT * FROM srp_erp_ngo_com_jobspecialization  WHERE JobCategoryID = {$JobCategoryID} ")->result_array();
        }

        echo '<option value="">Select a Specialization</option>';
        if (!empty($specialization)) {
            foreach ($specialization as $row) {
                echo '<option value="' . trim($row['specializationID'] ?? '') . '">' . trim($row['description'] ?? '') . '</option>';
            }
        }
    }


    public function saveOccupation()
    {
        $OccTypeID = $this->input->post('OccTypeID');
        $this->form_validation->set_rules('OccTypeID', 'Occupation Type', 'required');

        if ($OccTypeID == 1) {
            $this->form_validation->set_rules('schoolComID', 'School', 'required');
            $this->form_validation->set_rules('schoolTypeID', 'School Type', 'required');
            $this->form_validation->set_rules('gradeComID', 'Grade', 'required');
        } else {
            $this->form_validation->set_rules('JobCategoryID', 'Job Category ', 'required');
        }


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->saveOccupation());
        }
    }

    function editOccupation()
    {
        $id = $this->input->post('id');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query("SELECT *,DATE_FORMAT(DateFrom,'$convertFormat') AS DateFrom,DATE_FORMAT(DateTo,'$convertFormat') AS DateTo FROM srp_erp_ngo_com_memjobs WHERE MemJobID ={$id} AND companyID = {$companyID}")->row_array();
        echo json_encode($data);
    }

    function check_primaryOcc_exist()
    {
        echo json_encode($this->CommunityNgo_model->check_primaryOcc_exist());
    }

    public function updateOccupation()
    {

        $OccTypeID = $this->input->post('OccTypeID');
        $this->form_validation->set_rules('OccTypeID', 'Occupation Type', 'required');
        $this->form_validation->set_rules('hidden-id-occ', 'Job Category ID', 'required');

        if ($OccTypeID == 1) {
            $this->form_validation->set_rules('schoolComID', 'School', 'required');
            $this->form_validation->set_rules('schoolTypeID', 'School Type', 'required');
            $this->form_validation->set_rules('gradeComID', 'Grade', 'required');
        } else {
            $this->form_validation->set_rules('JobCategoryID', 'Job Category ', 'required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->updateOccupation());
        }
    }

    public function deleteOccupation()
    {
        $this->form_validation->set_rules('hidden-id', 'Job Category ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->deleteOccupation());
        }
    }

    /*end member occupation*/

    /***Start of Language ***/
    function save_Language()
    {

        $this->form_validation->set_rules('LanguageID', 'Language', 'required');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $Com_MasterID = $this->input->post('Com_MasterID');
        $LanguageID = $this->input->post('LanguageID');

        $data = array(
            'Com_MasterID' => $Com_MasterID,
            'LanguageID' => $LanguageID,
            'companyID' => current_companyID(),
            'createdUserID' => current_userID(),
            'createdPCID' => current_pc(),
            'createdDateTime' => current_date()
        );

        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT MemLanguageID FROM srp_erp_ngo_com_Memberlanguage WHERE companyID={$companyID} AND Com_MasterID = '$Com_MasterID' AND LanguageID = '$LanguageID' ")->row('MemLanguageID');

        if (isset($isExist)) {
            echo json_encode(['e', 'Already available']);
        } else {
            $int = $this->db->insert('srp_erp_ngo_com_Memberlanguage', $data);

            if ($int) {
                echo json_encode(['s', 'Inserted successfully']);
            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                echo json_encode(['e', $common_failed]);
            }
        }
    }

    function deleteLanguage()
    {
        $id = $this->input->post('id');

        $del = $this->db->where('MemLanguageID', $id)->delete('srp_erp_ngo_com_Memberlanguage');

        if ($del) {
            echo json_encode(['s', 'deleted successfully']);
        } else {
            $common_failed = $this->lang->line('common_failed');/* 'failed'*/
            echo json_encode(['e', $common_failed]);
        }
    }

    /***End of Language ***/

    function save_healthStatus()
    {

        $this->form_validation->set_rules('sickAutoID', 'Sickness', 'required');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $id = $this->input->post('hidden-id-HS');
        $Com_MasterID = $this->input->post('Com_MasterID');
        $sickAutoID = $this->input->post('sickAutoID');
        $startedFrom = $this->input->post('startedFrom');
        $medicalCondition = $this->input->post('medicalCondition');
        $monthlyExpenses = $this->input->post('monthlyExpenses');
        $Sick_remark = $this->input->post('Sick_remark');

        $data = array(
            'Com_MasterID' => $Com_MasterID,
            'sickAutoID' => $sickAutoID,
            'startedFrom' => $startedFrom,
            'medicalCondition' => $medicalCondition,
            'monthlyExpenses' => $monthlyExpenses,
            'Remarks' => $Sick_remark,
            'companyID' => current_companyID(),
            'createdUserID' => current_userID(),
            'createdPCID' => current_pc(),
            'createdDateTime' => current_date()
        );

        $data_update = array(
            'Com_MasterID' => $Com_MasterID,
            'sickAutoID' => $sickAutoID,
            'startedFrom' => $startedFrom,
            'medicalCondition' => $medicalCondition,
            'monthlyExpenses' => $monthlyExpenses,
            'Remarks' => $Sick_remark,
            'companyID' => current_companyID(),
            'modifiedUserID' => current_userID(),
            'modifiedPCID' => current_pc(),
            'modifiedDateTime' => current_date()
        );

        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT memPSicknessID FROM srp_erp_ngo_com_memberpersickness WHERE companyID={$companyID} AND Com_MasterID = '$Com_MasterID' AND sickAutoID = '$sickAutoID' ")->row('memPSicknessID');

        if (empty($id)) {
            if (isset($isExist)) {
                echo json_encode(['e', 'Already available']);
            } else {
                $int = $this->db->insert('srp_erp_ngo_com_memberpersickness', $data);

                if ($int) {
                    echo json_encode(['s', 'Inserted successfully']);
                } else {
                    $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                    echo json_encode(['e', $common_failed]);
                }
            }
        } else {
            $up = $this->db->where('memPSicknessID', $id)->update('srp_erp_ngo_com_memberpersickness', $data_update);

            if ($up) {
                echo json_encode(['s', 'Updated successfully']);
            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                echo json_encode(['e', $common_failed]);
            }
        }


    }

    function edit_healthStatus()
    {
        $id = $this->input->post('id');
        $companyID = current_companyID();

        $data = $this->db->query("SELECT * FROM srp_erp_ngo_com_memberpersickness WHERE memPSicknessID ={$id} AND companyID = {$companyID}")->row_array();
        echo json_encode($data);
    }

    function delete_healthStatus()
    {
        $id = $this->input->post('id');

        $del = $this->db->where('memPSicknessID', $id)->delete('srp_erp_ngo_com_memberpersickness');

        if ($del) {
            echo json_encode(['s', 'deleted successfully']);
        } else {
            $common_failed = $this->lang->line('common_failed');/* 'failed'*/
            echo json_encode(['e', $common_failed]);
        }
    }

    /***End of Sickness ***/

    function save_vehicleStatus()
    {

        $this->form_validation->set_rules('vehicleAutoID', 'Vehicle Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $id = $this->input->post('hidden-id-vehi');
        $Com_MasterID = $this->input->post('Com_MasterID');
        $vehicleAutoID = $this->input->post('vehicleAutoID');
        $vehiDescription = $this->input->post('vehiDescription');
        $vehiStatus = $this->input->post('vehiStatus');
        $vehiRemark = $this->input->post('vehiRemark');

        $data = array(
            'Com_MasterID' => $Com_MasterID,
            'vehicleAutoID' => $vehicleAutoID,
            'vehiDescription' => $vehiDescription,
            'vehiStatus' => $vehiStatus,
            'vehiRemark' => $vehiRemark,
            'companyID' => current_companyID(),
            'createdUserID' => current_userID(),
            'createdPCID' => current_pc(),
            'createdDateTime' => current_date()
        );

        $data_update = array(
            'Com_MasterID' => $Com_MasterID,
            'vehicleAutoID' => $vehicleAutoID,
            'vehiDescription' => $vehiDescription,
            'vehiStatus' => $vehiStatus,
            'vehiRemark' => $vehiRemark,
            'companyID' => current_companyID(),
            'modifiedUserID' => current_userID(),
            'modifiedPCID' => current_pc(),
            'modifiedDateTime' => current_date()
        );

        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT memVehicleID FROM srp_erp_ngo_com_membervehicles WHERE companyID={$companyID} AND Com_MasterID = '$Com_MasterID' AND vehicleAutoID = '$vehicleAutoID' ")->row('memVehicleID');

        if (empty($id)) {
            if (isset($isExist)) {
                echo json_encode(['e', 'Already available']);
            } else {
                $int = $this->db->insert('srp_erp_ngo_com_membervehicles', $data);

                if ($int) {
                    echo json_encode(['s', 'Inserted successfully']);
                } else {
                    $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                    echo json_encode(['e', $common_failed]);
                }
            }
        } else {
            $up = $this->db->where('memVehicleID', $id)->update('srp_erp_ngo_com_membervehicles', $data_update);

            if ($up) {
                echo json_encode(['s', 'Updated successfully']);
            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                echo json_encode(['e', $common_failed]);
            }
        }


    }

    function edit_vehicleStatus()
    {
        $id = $this->input->post('id');
        $companyID = current_companyID();

        $data = $this->db->query("SELECT * FROM srp_erp_ngo_com_membervehicles WHERE memVehicleID ={$id} AND companyID = {$companyID}")->row_array();
        echo json_encode($data);
    }

    function delete_vehicleStatus()
    {
        $id = $this->input->post('id');

        $del = $this->db->where('memVehicleID', $id)->delete('srp_erp_ngo_com_membervehicles');

        if ($del) {
            echo json_encode(['s', 'deleted successfully']);
        } else {
            $common_failed = $this->lang->line('common_failed');/* 'failed'*/
            echo json_encode(['e', $common_failed]);
        }
    }

    /***End of vehicles Del ***/

    function fetch_helpType_delDropdown()
    {
        $memHelpType = $this->input->post('memHelpType');

        if ($memHelpType) {
            if($memHelpType == 1){
                $govHelp = $this->db->query("SELECT helpRequireID,helpRequireDesc FROM srp_erp_ngo_com_helprequirements WHERE helpRequireType = 'GOV' ")->result_array();

                echo '<option value="">Select a Help Des.</option>';
                foreach ($govHelp as $row) {
                    echo '<option value="' . trim($row['helpRequireID'] ?? '') . '">' . trim($row['helpRequireDesc'] ?? '') . '</option>';
                }
            }
            if($memHelpType == 2){
                $prvHelp = $this->db->query("SELECT helpRequireID,helpRequireDesc FROM srp_erp_ngo_com_helprequirements WHERE helpRequireType = 'PVT' ")->result_array();

                echo '<option value="">Select a Help Des.</option>';
                foreach ($prvHelp as $row) {
                    echo '<option value="' . trim($row['helpRequireID'] ?? '') . '">' . trim($row['helpRequireDesc'] ?? '') . '</option>';
                }
            }
            if($memHelpType == 3){
                $consHelp = $this->db->query("SELECT helpRequireID,helpRequireDesc FROM srp_erp_ngo_com_helprequirements WHERE helpRequireType = 'CONS' ")->result_array();

                echo '<option value="">Select a Help Des.</option>';
                foreach ($consHelp as $row) {
                    echo '<option value="' . trim($row['helpRequireID'] ?? '') . '">' . trim($row['helpRequireDesc'] ?? '') . '</option>';
                }
            }

        }
    }

    function save_memHeplRStatus()
    {

        $this->form_validation->set_rules('memHelpType', 'Help Type', 'required');
        $this->form_validation->set_rules('helpDelID', 'Help Description', 'required');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $id = $this->input->post('hidden-id-hlp');
        $Com_MasterID = $this->input->post('Com_MasterID');
        $helpDelID = $this->input->post('helpDelID');
        $hlprDescription = $this->input->post('hlprDescription');

        $data = array(
            'Com_MasterID' => $Com_MasterID,
            'helpRequireID' => $helpDelID,
            'hlprDescription' => $hlprDescription,
            'companyID' => current_companyID(),
            'createdUserID' => current_userID(),
            'createdPCID' => current_pc(),
            'createdDateTime' => current_date()
        );

        $data_update = array(
            'Com_MasterID' => $Com_MasterID,
            'helpRequireID' => $helpDelID,
            'hlprDescription' => $hlprDescription,
            'companyID' => current_companyID(),
            'modifiedUserID' => current_userID(),
            'modifiedPCID' => current_pc(),
            'modifiedDateTime' => current_date()
        );

        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT memHelprID FROM srp_erp_ngo_com_memberhelprequirements WHERE companyID={$companyID} AND Com_MasterID = '$Com_MasterID' AND helpRequireID = '$helpDelID' ")->row('memHelprID');

        if (empty($id)) {
            if (isset($isExist)) {
                echo json_encode(['e', 'Already available']);
            } else {
                $int = $this->db->insert('srp_erp_ngo_com_memberhelprequirements', $data);

                if ($int) {
                    echo json_encode(['s', 'Inserted successfully']);
                } else {
                    $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                    echo json_encode(['e', $common_failed]);
                }
            }
        } else {
            $up = $this->db->where('memHelprID', $id)->update('srp_erp_ngo_com_memberhelprequirements', $data_update);

            if ($up) {
                echo json_encode(['s', 'Updated successfully']);
            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                echo json_encode(['e', $common_failed]);
            }
        }


    }

    function edit_memHeplRStatus()
    {
        $id = $this->input->post('id');
        $companyID = current_companyID();

        $data = $this->db->query("SELECT * FROM srp_erp_ngo_com_memberhelprequirements LEFT JOIN srp_erp_ngo_com_helprequirements ON srp_erp_ngo_com_memberhelprequirements.helpRequireID = srp_erp_ngo_com_helprequirements.helpRequireID WHERE memHelprID ={$id} AND companyID = {$companyID}")->row_array();
        echo json_encode($data);
    }

    function delete_memHeplRStatus()
    {
        $id = $this->input->post('id');

        $del = $this->db->where('memHelprID', $id)->delete('srp_erp_ngo_com_memberhelprequirements');

        if ($del) {
            echo json_encode(['s', 'deleted successfully']);
        } else {
            $common_failed = $this->lang->line('common_failed');/* 'failed'*/
            echo json_encode(['e', $common_failed]);
        }
    }

    /***End of member help requirments ***/

    /*start of willing to help details*/

    function save_willingToHelp()
    {
        $this->form_validation->set_rules('helpCategoryID', 'Help Category', 'required');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $id = $this->input->post('hidden-id-willingToHelp');
        $Com_MasterID = $this->input->post('Com_MasterID');
        $helpCategoryID = $this->input->post('helpCategoryID');
        $helpComments= $this->input->post('helpComments');

        $data = array(
            'Com_MasterID' => $Com_MasterID,
            'helpCategoryID' => $helpCategoryID,
            'helpComments' => $helpComments,
            'companyID' => current_companyID(),
            'createdUserID' => current_userID(),
            'createdPCID' => current_pc(),
            'createdDateTime' => current_date()
        );

        $data_update = array(
            'Com_MasterID' => $Com_MasterID,
            'helpCategoryID' => $helpCategoryID,
            'helpComments' => $helpComments,
            'companyID' => current_companyID(),
            'modifiedUserID' => current_userID(),
            'modifiedPCID' => current_pc(),
            'modifiedDateTime' => current_date()
        );

        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT willingToHelpID FROM srp_erp_ngo_com_memberwillingtohelp WHERE companyID={$companyID} AND Com_MasterID = '$Com_MasterID' AND helpCategoryID = '$helpCategoryID' ")->row('willingToHelpID');

        if (empty($id)) {
            if (isset($isExist)) {
                echo json_encode(['e', 'Already available']);
            } else {
                $int = $this->db->insert('srp_erp_ngo_com_memberwillingtohelp', $data);

                if ($int) {
                    echo json_encode(['s', 'Inserted successfully']);
                } else {
                    $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                    echo json_encode(['e', $common_failed]);
                }
            }
        } else {
            $up = $this->db->where('willingToHelpID', $id)->update('srp_erp_ngo_com_memberwillingtohelp', $data_update);

            if ($up) {
                echo json_encode(['s', 'Updated successfully']);
            } else {
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                echo json_encode(['e', $common_failed]);
            }
        }
    }

    function edit_willingToHelp()
    {
        $id = $this->input->post('id');
        $companyID = current_companyID();

        $data = $this->db->query("SELECT * FROM srp_erp_ngo_com_memberwillingtohelp LEFT JOIN srp_erp_ngo_com_helpcategories ON srp_erp_ngo_com_memberwillingtohelp.helpCategoryID = srp_erp_ngo_com_helpcategories.helpCategoryID WHERE willingToHelpID ={$id} AND companyID = {$companyID}")->row_array();
        echo json_encode($data);
    }



    function delete_willingToHelp()
    {
        $id = $this->input->post('id');
        $del = $this->db->where('willingToHelpID', $id)->delete('srp_erp_ngo_com_memberwillingtohelp');

        if ($del) {
            echo json_encode(['s', 'deleted successfully']);
        } else {
            $common_failed = $this->lang->line('common_failed');/* 'failed'*/
            echo json_encode(['e', $common_failed]);
        }
    }
    /*end of willing to help details*/

    /*upload member image*/
    function member_image_upload()
    {
        $this->form_validation->set_rules('MemberID', 'Member ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->member_image_upload());
        }
    }

    function fetch_province_based_countryDropdown()
    {
        $countyID = $this->input->post('countyID');
        if ($countyID) {
            $province = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE countyID = {$countyID} AND type = 1")->result_array();

            echo '<option value="">Select a Province</option>';
            foreach ($province as $row) {
                echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
            }
        }
    }

    function fetch_province_based_districtDropdown()
    {
        $masterID = $this->input->post('masterID');

        $companyID = $this->common_data['company_data']['company_id'];

        $dataStGet = $this->db->query("SELECT srp_erp_statemaster.countyID,srp_erp_ngo_com_regionmaster.stateID,srp_erp_statemaster.type,srp_erp_statemaster.divisionTypeCode FROM srp_erp_ngo_com_regionmaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_regionmaster.stateID=srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_regionmaster.companyID = {$companyID}");
        $stateGet = $dataStGet->row();

        if (!empty($masterID)) {
            $district = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 2")->result_array();
        }

        echo '<option  value="">Select a District</option>';
        if (!empty($district)) {
            foreach ($district as $row) {
                if((!empty($stateGet) && $stateGet->type == 2) && ($stateGet->stateID == $row['stateID'])){

                    echo '<option value="' . trim($row['stateID'] ?? '') . '" selected="selected">' . trim($row['Description'] ?? '') . '</option>';
                }
                else {
                    echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
                }
            }
        }
    }

    function fetch_district_based_jammiyaDropdown()
    {
        $masterID = $this->input->post('masterID');

        $companyID = $this->common_data['company_data']['company_id'];

        $dataStGet = $this->db->query("SELECT srp_erp_statemaster.countyID,srp_erp_ngo_com_regionmaster.stateID,srp_erp_statemaster.type,srp_erp_statemaster.divisionTypeCode FROM srp_erp_ngo_com_regionmaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_regionmaster.stateID=srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_regionmaster.companyID = {$companyID}");
        $stateGet = $dataStGet->row();

        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 3 AND divisionTypeCode = 'JD'")->result_array();
        }

        echo '<option value="">Select a Jammiyah Division</option>';
        if (!empty($division)) {
            foreach ($division as $row) {
                if((!empty($stateGet) && $stateGet->type == 3 && $stateGet->divisionTypeCode =='JD') && ($stateGet->stateID == $row['stateID'])){
                    echo '<option value="' . trim($row['stateID'] ?? '') . '" selected="selected">' . trim($row['Description'] ?? '') . '</option>';
                }
                else {
                    echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
                }
            }
        }
    }

    function fetch_district_based_districtDivisionDropdown()
    {
        $masterID = $this->input->post('masterID');

        $companyID = $this->common_data['company_data']['company_id'];

        $dataStGet = $this->db->query("SELECT srp_erp_statemaster.countyID,srp_erp_ngo_com_regionmaster.stateID,srp_erp_statemaster.type,srp_erp_statemaster.divisionTypeCode FROM srp_erp_ngo_com_regionmaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_regionmaster.stateID=srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_regionmaster.companyID = {$companyID}");
        $stateGet = $dataStGet->row();

        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 3 AND divisionTypeCode = 'DD'")->result_array();
        }

        echo '<option value="">Select a District Division</option>';
        if (!empty($division)) {
            foreach ($division as $row) {
                if((!empty($stateGet) && $stateGet->type == 3 && $stateGet->divisionTypeCode =='DD') && ($stateGet->stateID == $row['stateID'])){
                    echo '<option value="' . trim($row['stateID'] ?? '') . '" selected="selected">' . trim($row['Description'] ?? '') . '</option>';
                }
                else {
                    echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
                }
            }
        }

    }

    function fetch_division_based_GS_divisionDropdown()
    {
        $masterID = $this->input->post('masterID');
        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 4 AND divisionTypeCode = 'GN'")->result_array();
        }

        echo '<option value="">Select a GS Division</option>';
        if (!empty($division)) {
            foreach ($division as $row) {
                echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
            }
        }
    }

    function fetch_division_based_division_Area_Dropdown()
    {
        $masterID = $this->input->post('masterID');
        if (!empty($masterID)) {
            $division = $this->db->query("SELECT stateID,Description FROM srp_erp_statemaster WHERE masterID = {$masterID} AND type = 4 AND divisionTypeCode = 'MH'")->result_array();
        }

        echo '<option value="">Select a Area</option>';
        if (!empty($division)) {
            foreach ($division as $row) {
                echo '<option value="' . trim($row['stateID'] ?? '') . '">' . trim($row['Description'] ?? '') . '</option>';
            }
        }
    }

    //end of community master


    /*Starting of Collection Management*/

    function fetch_collection_entry()
    {
        $this->datatables->select("CollectionMasterID,TypeID,Description,CollectionDes,srp_erp_gender.name as genName,CONCAT(revenueSystemGLCode ,' | ', revenueGLCode ,' | ', revenueGLDescription) AS revenueAccount,CONCAT(receivableSystemGLCode ,' | ', receivableGLAccount ,' | ', receivableDescription) AS receivableAccount");
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->from('srp_erp_ngo_com_collectionmaster')
            ->join('srp_erp_ngo_com_collectiontypes', 'srp_erp_ngo_com_collectionmaster.CollectionTypeID = srp_erp_ngo_com_collectiontypes.TypeID')
            ->join('srp_erp_gender', 'srp_erp_ngo_com_collectionmaster.genderID = srp_erp_gender.genderID','left');
        $this->datatables->add_column('action', '<a title="Edit Collection Setup" onclick="edit_collection_entry($1)"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<span><a title="Collection Setup Configs.." onclick="get_collectionType($2);fetchPage(\'system/communityNgo/ngo_hi_collection_details\',$1,\'$4\',\'Collection Setup\'); "><span class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a title="Delete Collection Setup" onclick="delete_collection_entry($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>', 'CollectionMasterID,TypeID,Description,CollectionDes');
        echo $this->datatables->generate();
    }

    function delete_collection_entry()
    {
        echo json_encode($this->CommunityNgo_model->delete_collection_entry());
    }

    function edit_collection_entry()
    {
        $CollectionMasterID = $this->input->post('CollectionMasterID');
        $detail = $this->db->query("select * from srp_erp_ngo_com_collectionmaster WHERE CollectionMasterID = {$CollectionMasterID}")->row_array();
        echo exit(json_encode($detail));
    }

    function save_collection_setup()
    {
        $collections = $this->input->post('CollectionTypeID');

        foreach ($collections as $key => $des) {
            $this->form_validation->set_rules("CollectionDes[{$key}]", 'Description', 'trim|required');
            $this->form_validation->set_rules("CollectionTypeID[{$key}]", 'Collection Type', 'trim|required');
            $this->form_validation->set_rules("genderID[{$key}]", 'Gender', 'required|trim');
            $this->form_validation->set_rules("revenueGLAutoID[{$key}]", 'Income Account', 'required|trim');
            $this->form_validation->set_rules("receivableAutoID[{$key}]", 'Receivable Account', 'required|trim');
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->CommunityNgo_model->save_collection_setup());
        }
    }

    function fetch_collection_details()
    {
        echo json_encode($this->CommunityNgo_model->fetch_collection_details());
    }

    function delete_details()
    {
        echo json_encode($this->CommunityNgo_model->delete_details());
    }

    function edit_details()
    {
        $CollectionDetailID = $this->input->post('CollectionDetailID');
        $detail = $this->db->query("select * from srp_erp_ngo_com_collectiondetails WHERE CollectionDetailID = {$CollectionDetailID}")->row_array();
        echo exit(json_encode($detail));
    }

    function save_collection_detail()
    {
        $this->form_validation->set_rules("companyFinanceYearID", 'Finance Year', 'required|trim');
        $this->form_validation->set_rules("PeriodTypeID", 'Period Type', 'required|trim');
        $this->form_validation->set_rules("transactionCurrencyID", 'Currency', 'required|trim');
        $this->form_validation->set_rules("segmentID", 'Segment', 'required|trim');

        $CollectionType = $this->input->post('CollectionType');
        if ($CollectionType != 3) {
            $this->form_validation->set_rules("Amount", 'Amount', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_collection_detail());
        }
    }

    /*start of customer config*/
    function save_customer_config()
    {
        $this->form_validation->set_rules('receivableAutoID', 'Receivable Account ', 'required');
        $this->form_validation->set_rules('partyCategoryID', 'Category', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_customer_config());
        }
    }

    function fetch_customer_config()
    {
        $this->datatables->select('ConfigAutoID,receivableAutoID,srp_erp_ngo_com_customerconfig.partyCategoryID,CONCAT(receivableSystemGLCode ,\' | \', receivableGLAccount ,\' | \', receivableDescription) AS receivableAccount,categoryDescription', false)
            ->from('srp_erp_ngo_com_customerconfig')
            ->join('srp_erp_partycategories', 'srp_erp_partycategories.partyCategoryID = srp_erp_ngo_com_customerconfig.partyCategoryID');
        $this->datatables->where('srp_erp_ngo_com_customerconfig.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('edit', '<a style="text-align: center;" onclick="edit_customer_config($1);" data-description="$2"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>', 'ConfigAutoID');
        echo $this->datatables->generate();
    }

    function edit_customer_config()
    {
        $id = $this->input->post('ConfigAutoID');
        $companyID = current_companyID();
        $data = $this->db->query("SELECT * FROM srp_erp_ngo_com_customerconfig WHERE ConfigAutoID ={$id} AND companyID = {$companyID}")->row_array();
        echo json_encode($data);
    }

    /*end of customer config*/

    function get_collection_member_details()
    {
        $data = array();
        $companyID = current_companyID();

        $CollectionDetailID = $this->input->post('CollectionDetailID');
        $detail = $this->db->query("select * from srp_erp_ngo_com_collectiondetails WHERE CollectionDetailID = {$CollectionDetailID}")->row_array();

        $CollectionMasterID = $this->input->post('CollectionMasterID');
        $master = $this->db->query("select * from srp_erp_ngo_com_collectionmaster WHERE CollectionMasterID = {$CollectionMasterID}")->row_array();

        $gender = $master['genderID'];
        switch ($gender) {
            case '1':
                $gender_filter = "AND GenderID = '1' ";
                break;
            case '2':
                $gender_filter = "AND GenderID = '2' ";
                break;
            default:
                $gender_filter = '';
        }

        $data['members'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_communitymaster
                                        WHERE companyID={$companyID} AND isDeleted = 0 AND isActive = 1 $gender_filter")->result_array();

        $data["CollectionAmount"] = $detail['Amount'];
        $data["CollectionType"] = $this->input->post('CollectionType');
        $data["CollectionDetailID"] = $CollectionDetailID;
        $data["CollectionMasterID"] = $CollectionMasterID;

        $this->load->view('system/communityNgo/ajax/ngo_collection_members', $data);
    }

    function get_selected_members()
    {
        $this->form_validation->set_rules("Com_MasterID[]", 'Member', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            echo '<label style="color: red;"> Member is required !</label>';
        } else {

            $companyid = $this->common_data['company_data']['company_id'];

            $Com_MasterID = $this->input->post('Com_MasterID');

            $MemberID = array();
            foreach ($Com_MasterID as $key => $memID) {
                $MemberID [] = $Com_MasterID[$key];
            }

            $In_MemberID = "'" . implode("', '", $MemberID) . "'";

            $where = "companyID = " . $companyid . " AND Com_MasterID IN ($In_MemberID)";
            $this->db->select('Com_MasterID,CName_with_initials,MemberCode');
            $this->db->from('srp_erp_ngo_com_communitymaster');
            $this->db->where($where);
            $this->db->order_by('Com_MasterID', 'desc');
            $data['Com_MemID'] = $this->db->get()->result_array();
            $data['CollectionType'] = $this->input->post('CollectionType');
            $data['CollectionAmount'] = $this->input->post('CollectionAmount');
            $this->load->view('system/communityNgo/ajax/load_ngo_selectedMembers', $data);
        }
    }

    function save_collection_members()
    {
        $this->form_validation->set_rules("Com_MasterID[]", 'Member', 'trim|required');
        $this->form_validation->set_rules("Amount[]", 'Amount', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->CommunityNgo_model->save_collection_members());
        }
    }

    function get_member_for_collection()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $OccTypeID = $this->input->post("OccTypeID");
        $age = $this->input->post("age");
        $CollectionType = $this->input->post("CollectionType");
        $FamMasterID = $this->input->post("FamMasterID");

        if ($CollectionType == 1) {

            $whereOccupation = "";
            if (!empty($OccTypeID)) {
                $whereOccupation = "AND mj.OccTypeID IN(" . join(',', $OccTypeID) . ")";
            }

            $whereAge = "";
            if (!empty($age)) {
                $whereAge = "AND SUBSTRING(Age,1,CHAR_LENGTH(Age) - 1)  >= $age ";
            }

            $whereFamMaster = "";
            if (!empty($FamMasterID)) {
                $whereFamMaster = "AND fm.FamMasterID IN(" . join(',', $FamMasterID) . ")";
            }

        } else {

            $whereGender = "";
            $whereOccupation = "";
            $whereAge = "";
            $whereFamMaster = "";
        }

        $CollectionMasterID = $this->input->post('CollectionMasterID');
        $master = $this->db->query("select * from srp_erp_ngo_com_collectionmaster LEFT JOIN srp_erp_ngo_com_collectiondetails ON srp_erp_ngo_com_collectiondetails.CollectionMasterID=srp_erp_ngo_com_collectionmaster.CollectionMasterID WHERE srp_erp_ngo_com_collectionmaster.CollectionMasterID = {$CollectionMasterID}")->row_array();

        $gender = $master['genderID'];
        switch ($gender) {
            case '1':
                $gender_filter = "AND GenderID = '1' ";
                break;
            case '2':
                $gender_filter = "AND GenderID = '2' ";
                break;
            default:
                $gender_filter = '';
        }


        $CollectionDes = $master{'CollectionDes'};
        $PeriodTypeID = $master{'PeriodTypeID'};
        $companyFinanceYearID = $master{'companyFinanceYearID'};
        $companyFinanceYear = $master{'companyFinanceYear'};

        // company finance year details
        $company_finance_year = $this->db->query("SELECT * FROM srp_erp_companyfinanceyear WHERE companyID = {$companyID} AND companyFinanceYearID = {$companyFinanceYearID} AND isActive = 1 AND isCurrent = 1 ")->row_array();

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_customermaster.communityMemberID FROM srp_erp_customerinvoicemaster LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID=srp_erp_customerinvoicemaster.customerID WHERE srp_erp_customerinvoicemaster.companyID={$companyID} AND srp_erp_customerinvoicemaster.invoiceType='Direct' AND srp_erp_customerinvoicemaster.documentID='CINV' AND srp_erp_customerinvoicemaster.invoiceNarration='". $CollectionDes. "' AND srp_erp_customerinvoicemaster.companyFinanceYearID={$companyFinanceYearID} AND srp_erp_customerinvoicemaster.companyFinanceYear='". $companyFinanceYear. "' AND srp_erp_customerinvoicemaster.FYBegin='". $company_finance_year['beginingDate']. "' AND srp_erp_customerinvoicemaster.FYEnd='". $company_finance_year['endingDate']. "'");
        $rowFP = $queryFP->result();
        $memFemInr = array();
        foreach ($rowFP as $resFP) {

            $memFemInr[] = $resFP->communityMemberID;

        }

        $in_memFeePrt = "'" . implode("', '", $memFemInr) . "'";

        if(empty($FamMasterID)){
            $data['details'] = $this->db->query("SELECT cm.Com_MasterID,CName_with_initials,MemberCode,srp_erp_statemaster.Description FROM srp_erp_ngo_com_communitymaster cm
LEFT JOIN srp_erp_ngo_com_memjobs mj ON mj.Com_MasterID = cm.Com_MasterID
LEFT JOIN srp_erp_ngo_com_occupationtypes ON mj.OccTypeID = srp_erp_ngo_com_occupationtypes.OccTypeID
LEFT JOIN srp_erp_ngo_com_regionmaster ON srp_erp_ngo_com_regionmaster.RegionID = cm.RegionID 
LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_regionmaster.stateID
WHERE  cm.isDeleted = 0 AND  cm.isActive = 1 AND cm.companyID = {$companyID} AND cm.Com_MasterID NOT IN ($in_memFeePrt)
 $whereGender $whereAge $whereOccupation $gender_filter
 ORDER BY cm.Com_MasterID ASC ")->result_array();
        }
        else{
            $data['details'] = $this->db->query("SELECT cm.Com_MasterID,CName_with_initials,MemberCode,srp_erp_statemaster.Description FROM srp_erp_ngo_com_communitymaster cm
LEFT JOIN srp_erp_ngo_com_familymaster fm ON fm.LeaderID = cm.Com_MasterID
LEFT JOIN srp_erp_ngo_com_regionmaster ON srp_erp_ngo_com_regionmaster.RegionID = cm.RegionID 
LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_regionmaster.stateID
WHERE  cm.isDeleted = 0 AND  cm.isActive = 1 AND cm.companyID = {$companyID} AND cm.Com_MasterID NOT IN ($in_memFeePrt)
 $whereFamMaster
 ORDER BY cm.Com_MasterID ASC ")->result_array();
        }


        $member_arr = [];
        if (!empty($data['details'])) {
            foreach ($data['details'] as $val) {
                $member_arr[trim($val['Com_MasterID'] ?? '')] = (trim($val['MemberCode'] ?? '') ? trim($val['MemberCode'] ?? '') . ' | ' : '') . trim($val['CName_with_initials'] ?? '');
            }
        }

        foreach ($member_arr as $key => $val) {
            echo '<option value="' . $key . '">' . $val . '</option>';
        }
    }

    /*Starting of Issue Items*/

    function fetch_purchase_request()
    {

        $companyid = $this->common_data['company_data']['company_id'];

        $where = "companyID = " . $companyid . "";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_ngo_com_itemissuemaster.itemIssueAutoID as itemIssueAutoID,companyCode,itemIssueCode,narration,requestedMemberName,confirmedYN ,DATE_FORMAT(expectedReturnDate,'.$convertFormat.') AS expectedReturnDate,transactionCurrency ,createdUserID,srp_erp_ngo_com_itemissuemaster.transactionAmount,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount,isDeleted,isReturned");
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,itemIssueAutoID FROM srp_erp_ngo_com_itemissuedetails GROUP BY itemIssueAutoID) det', '(det.itemIssueAutoID = srp_erp_ngo_com_itemissuemaster.itemIssueAutoID)', 'left');
        $this->datatables->from('srp_erp_ngo_com_itemissuemaster');
        $this->datatables->add_column('prq_detail', '<b>Member Name : </b> $2 <br> <b>Exp Return Date : </b> $3 <b><br><b>Narration : </b> $1', 'narration,requestedMemberName,expectedReturnDate,transactionCurrency');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PRQ",itemIssueAutoID)');
        $this->datatables->add_column('returned', '$1', 'return_status(isReturned)');
        $this->datatables->add_column('edit', '$1', 'load_prq_action(itemIssueAutoID,confirmedYN,isDeleted)');

        echo $this->datatables->generate();
    }

    function fetch_member_details()
    {
        echo json_encode($this->CommunityNgo_model->fetch_member_details());
    }

    function load_item_request_header()
    {
        echo json_encode($this->CommunityNgo_model->load_item_request_header());
    }

    function save_item_request_header()
    {

        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('expectedReturnDate', 'Delivery Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('issueDate', 'PRQ Date ', 'trim|required|validate_date');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $date_format_policy = date_format_policy();
            $format_expectedReturnDate = input_format_date($this->input->post('expectedReturnDate'), $date_format_policy);
            $format_POdate = input_format_date($this->input->post('issueDate'), $date_format_policy);
            if ($format_expectedReturnDate >= $format_POdate) {
                echo json_encode($this->CommunityNgo_model->save_item_request_header());
            } else {
                $this->session->set_flashdata('e', 'Expected Return Date should be greater than Issue Date');
                echo json_encode(FALSE);
            }
        }
    }

    function fetch_item_req_detail_table()
    {
        echo json_encode($this->CommunityNgo_model->fetch_item_req_detail_table());
    }

    function load_item_request_date()
    {
        echo json_encode($this->CommunityNgo_model->load_item_request_date());
    }

    function get_date_format()
    {
        $date_format_policy = date_format_policy();
        $formatDate = input_format_date($this->input->post('date'), $date_format_policy);
        echo json_encode($formatDate);
    }

    function fetch_rent_item_details()
    {
        $id = $this->input->post('rentalItemID');
        $companyID = current_companyID();
        $data = $this->db->query("SELECT * FROM srp_erp_ngo_com_rentalitems WHERE rentalItemID ={$id} AND companyID = {$companyID}")->row_array();
        echo json_encode($data);
    }

    function save_item_issue_details()
    {

        $ItemType = $this->input->post('rentalItemID');

        foreach ($ItemType as $key => $search) {

            $this->form_validation->set_rules("expectedReturnDateDetail[{$key}]", 'Expected Return Date', 'trim|required');
            //  $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required|greater_than[0]');
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
            echo json_encode($this->CommunityNgo_model->save_item_issue_details());
        }
    }

    function update_item_issue_detail()
    {
        $this->form_validation->set_rules('rentalItemID', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('expectedReturnDateDetail', 'Expected Return Date', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->CommunityNgo_model->update_item_issue_detail());
        }
    }

    function fetch_item_issue_detail_edit()
    {
        echo json_encode($this->CommunityNgo_model->fetch_item_issue_detail_edit());
    }

    function delete_itemissuemaster()
    {
        echo json_encode($this->CommunityNgo_model->delete_itemissuemaster());
    }

    function delete_item_issue_detail()
    {
        echo json_encode($this->CommunityNgo_model->delete_item_issue_detail());
    }

    function load_item_issue_conformation()
    {
        $itemIssueAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('itemIssueAutoID') ?? '');
        $data['extra'] = $this->CommunityNgo_model->fetch_template_data($itemIssueAutoID);
        $data['approval'] = $this->input->post('approval');

        $html = $this->load->view('system/communityNgo/ngo_hi_rental_item_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_returned_item_details()
    {
        $itemIssueAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('itemIssueAutoID') ?? '');
        $data['extra'] = $this->CommunityNgo_model->fetch_template_data($itemIssueAutoID);
        $html = $this->load->view('system/communityNgo/ngo_hi_returned_item_details', $data, true);
        echo $html;
    }

    function rental_issue_confirmation()
    {
        echo json_encode($this->CommunityNgo_model->rental_issue_confirmation());
    }

    function return_item_confirmation()
    {

        $companyID = current_companyID();

        $itemIssueAutoID = trim($this->input->post('itemIssueAutoID') ?? '');
        $isReturned = trim($this->input->post('isReturned') ?? '');

        $date_format_policy = date_format_policy();
        $ReturnedDate_re = $this->input->post('ReturnedDate');
        $ReturnedDate = input_format_date($ReturnedDate_re, $date_format_policy);
        $queryDt = $this->db->query("SELECT issueDate FROM srp_erp_ngo_com_itemissuemaster WHERE companyID={$companyID} AND itemIssueAutoID = '$itemIssueAutoID' ");
        $isControlDate = $queryDt->row();

        if (!empty($isControlDate) && $isControlDate->issueDate < $ReturnedDate) {

        echo json_encode($this->CommunityNgo_model->return_item_confirmation());
        } else {

            echo json_encode(array('e', 'Unable to proceed. Return date is less than issued date'));
            exit;
        }
    }

    function fetch_item_for_return()
    {
        echo json_encode($this->CommunityNgo_model->fetch_item_for_return());
    }

    function loademail()
    {
        echo json_encode($this->CommunityNgo_model->loademail());
    }

    function send_request_email()
    {
        $this->form_validation->set_rules('sender_name', 'Sender Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        $this->form_validation->set_rules('message', 'Message', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        }
        else{
            echo json_encode($this->CommunityNgo_model->send_request_email());
        }

    }

    /* end of hish */

    //access
    /* Starting Moufi */

    //family Master
    function nicDup_Check()
    {
        echo json_encode($this->CommunityNgo_model->nicDup_Check());
    }

    function save_familyMaster()
    {
        $LedgerNo = $this->input->post('LedgerNo');
        $LeaderID = $this->input->post('LeaderID');
        $FamilyName = $this->input->post('FamilyName');
        $FamAncestory = $this->input->post('FamAncestory');
        $ComEconSteID = $this->input->post('ComEconSteID');
        $femHelpNeedId = $this->input->post('femHelpNeedId');

        $this->form_validation->set_rules('FamilyAddedDate', 'Family Added Date', 'trim|required');

        if (isset($LedgerNo)) {
            $this->form_validation->set_rules('LedgerNo', 'Reference No', 'trim|required');
        }
        if (isset($LeaderID)) {
            $this->form_validation->set_rules('LeaderID', 'Leader', 'trim|required');
        }
        if (isset($FamilyName)) {
            $this->form_validation->set_rules('FamilyName', 'Family Name', 'trim|required');
        }
        if (isset($FamAncestory)) {
            $this->form_validation->set_rules('FamAncestory', 'Ancestry Status', 'trim|required');
        }

        if (isset($FamAncestory) && ($FamAncestory == 0 || $FamAncestory == '')) {

        } else {
            $this->form_validation->set_rules('AncestryCatID', 'Ancestry Type', 'trim|required');

        }
        if (isset($ComEconSteID)) {
            $this->form_validation->set_rules('ComEconSteID', 'Economic Status', 'trim|required');
        }
        if (isset($femHelpNeedId)) {
            $this->form_validation->set_rules('femHelpNeedId', 'Help Need Status', 'trim|required');
        }

        if (isset($femHelpNeedId) && ($femHelpNeedId == 0 || $femHelpNeedId == '')) {

        } else {
            $this->form_validation->set_rules('femNeededHelp', 'Needed Help Detail', 'trim|required');

        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_familyMaster());
        }
    }

    function load_familyMasterView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('femKey') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $AncestId = trim($this->input->post('AncestId') ?? '');
        $convertFormat = convert_date_format_sql();


        $filter_string = '';
        if (isset($text) && !empty($text)) {
            $filter_string = " AND ((FamilySystemCode Like '%" . $text . "%') OR (FamilyName Like '%" . $text . "%') OR (LedgerNo Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (FamilyCode Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";
        }
        $filter_sorting = '';
        if (isset($sorting) && $sorting != '#') {
            $filter_sorting = " AND FamilyName Like '" . $sorting . "%'";
        }
        $deleted = " AND srp_erp_ngo_com_familymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_familymaster.companyID = " . $companyID . $filter_string . $filter_sorting . $deleted;

        if ($AncestId == '-2' || $AncestId == null || $AncestId == '') {
            $data['familyMas'] = $this->db->query("SELECT srp_erp_ngo_com_familymaster.companyID,srp_erp_ngo_com_familymaster.createdUserID,LeaderID,FamilyCode,FamilyName,confirmedYN,srp_erp_ngo_com_familymaster.FamMasterID,FamilySystemCode,DATE_FORMAT(FamilyAddedDate,'{$convertFormat}') AS FamilyAddedDate, LedgerNo, CName_with_initials,FamAncestory,ComEconSteID,TP_home,TP_Mobile FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster on Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID  WHERE $where ORDER BY FamMasterID DESC")->result_array();
        } else if ($AncestId != '-2' || $AncestId != null || $AncestId != '') {
            $data['familyMas'] = $this->db->query("SELECT srp_erp_ngo_com_familymaster.companyID,srp_erp_ngo_com_familymaster.createdUserID,LeaderID,FamilyCode,FamilyName,confirmedYN,srp_erp_ngo_com_familymaster.FamMasterID,FamilySystemCode,DATE_FORMAT(FamilyAddedDate,'{$convertFormat}') AS FamilyAddedDate, LedgerNo, CName_with_initials,FamAncestory,ComEconSteID,TP_home,TP_Mobile FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster on Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID  WHERE $where AND srp_erp_ngo_com_familymaster.FamAncestory='" . $AncestId . "' ORDER BY FamMasterID DESC")->result_array();
        }

        $this->load->view('system/CommunityNgo/ajax/load_com_ngo_families.php', $data);
    }

    function load_ngoFamilyHeader()
    {
        echo json_encode($this->CommunityNgo_model->load_ngoFamilyHeader());
    }

    function get_FamHgender()
    {

        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_FamHgender($LeaderID);
    }

    function get_FamHaddress()
    {

        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_FamHaddress($LeaderID);
    }

    function get_FamArea()
    {

        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_FamArea($LeaderID);
    }

    function get_FamHouseNo()
    {

        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_FamHouseNo($LeaderID);
    }

    function load_ngoHouseExitDel()
    {
        echo json_encode($this->CommunityNgo_model->load_ngoHouseExitDel());
    }

    function load_ngoHouseHeader()
    {
        echo json_encode($this->CommunityNgo_model->load_ngoHouseHeader());
    }

/*    function get_FamHouseAddState()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $editFamMasID = $_POST['editFamMasID'];
        $FamHouseCn = $_POST['FamHouseCn'];

        $where = "srp_erp_ngo_com_house_enrolling.companyID = " . $companyID ;

        if (!empty($editFamMasID) || $editFamMasID != '' || $editFamMasID != NULL) {

                $data['familyHouse'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_house_enrolling WHERE FamMasterID == '" . $editFamMasID . "' $where ORDER BY FamMasterID DESC")->result_array();


            if (!empty($data['familyHouse']) && ($FamHouseCn == '1')) {

                echo json_encode(array('error' => 1, 'message' => 'Already the house enrolled to House Count.'));
            }
            else if (empty($data['familyHouse'])) {

                echo json_encode(array('error' => 4, 'message' => 'The house is not enrolled to House Count yet.'));
            } else if (empty($data['familyHouse']) && ($FamHouseCn == '1')) {

                echo json_encode(array('error' => 2, 'message' => 'The house will be enrolled to House Count.'));
            }
        }

    }*/

    function get_hEjammiyahDivisionID()
    {

        $FamHouseSt = $_POST['FamHouseSt'];
        $housesExitId = $_POST['housesExitId'];
        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_hEjammiyahDivisionID($FamHouseSt,$housesExitId,$LeaderID);
    }
    function get_hEGS_Division()
    {

        $FamHouseSt = $_POST['FamHouseSt'];
        $housesExitId = $_POST['housesExitId'];
        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_hEGS_Division($FamHouseSt,$housesExitId,$LeaderID);
    }
    function get_hEGS_No()
    {

        $FamHouseSt = $_POST['FamHouseSt'];
        $housesExitId = $_POST['housesExitId'];
        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_hEGS_No($FamHouseSt,$housesExitId,$LeaderID);
    }
    function get_hERegionID()
    {

        $FamHouseSt = $_POST['FamHouseSt'];
        $housesExitId = $_POST['housesExitId'];
        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_hERegionID($FamHouseSt,$housesExitId,$LeaderID);
    }
    function get_hEHouseNo()
    {

        $FamHouseSt = $_POST['FamHouseSt'];
        $housesExitId = $_POST['housesExitId'];
        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_hEHouseNo($FamHouseSt,$housesExitId,$LeaderID);
    }
    function get_hEC_Address()
    {

        $FamHouseSt = $_POST['FamHouseSt'];
        $housesExitId = $_POST['housesExitId'];
        $LeaderID = $_POST['LeaderID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_hEC_Address($FamHouseSt,$housesExitId,$LeaderID);
    }


    function save_familyHouseEnroll()
    {
        $FamMasterID2 = $this->input->post('FamMasterID2');
        $FamHouseSt = $this->input->post('FamHouseSt');

        $this->form_validation->set_rules('FamHouseCn', 'Enroll To House Count status', 'trim|required');
        $this->form_validation->set_rules('ownershipAutoID', 'Ownership Type', 'trim|required');

        if (isset($FamHouseSt)) {
            $this->form_validation->set_rules('FamHouseSt', 'Enroll To Existing House Count status is required', 'trim|required');
        }

        if (isset($FamHouseSt) && ($FamHouseSt == 0 || $FamHouseSt == '')) {

        } else {
            $this->form_validation->set_rules('housesExitId', 'House existing family', 'trim|required');

        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_familyHouseEnroll());
        }
    }

    //add new community
    function save_communityMem_detail()
    {

        $nameWithIni = $this->input->post('nameWithIni');

        foreach ($nameWithIni as $key => $project) {

            $this->form_validation->set_rules("TitleID[{$key}]", 'Member Title', 'trim|required');

            $this->form_validation->set_rules("nameWithIni[{$key}]", 'Member Name With Initial', 'trim|required');

            $this->form_validation->set_rules("nameWithFull[{$key}]", 'Member Full Name', 'trim|required');

            $this->form_validation->set_rules("genderID[{$key}]", 'Gender', 'trim|required');

            $this->form_validation->set_rules("newMemDOB[{$key}]", 'DOB', 'trim|required');

            $this->form_validation->set_rules("NewRelatnID[{$key}]", 'Relationship', 'trim|required');

        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            $FamMasterID = $this->input->post('FamMasterID');

            echo json_encode($this->CommunityNgo_model->save_communityMem_detail());
        }
    }

    function save_famMembers_detail()
    {

        $FamMemAddedDate = $this->input->post('FamMemAddedDate');

        $this->form_validation->set_rules("Com_MasterID[]", 'Member', 'trim|required');
        $this->form_validation->set_rules("memRelaID[]", 'Relationship', 'trim|required');


        $this->form_validation->set_rules("FamMemAddedDate", 'Family Member Added Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            $FamMasterID = $this->input->post('FamMasterID');
            $master = $this->db->query("select FamilyAddedDate from srp_erp_ngo_com_familymaster WHERE FamMasterID=$FamMasterID ")->row_array();
            $FamilyAddedDate = $master['FamilyAddedDate'];

            $date_format_policy = date_format_policy();

            $FamMemAddedDat = $FamMemAddedDate;
            $expire = input_format_date($FamMemAddedDat, $date_format_policy);
            if ($expire < $FamilyAddedDate) {
                echo json_encode(array('e', 'Unable to proceed. Member added date is greater than family added date'));
                exit;
            }

            echo json_encode($this->CommunityNgo_model->save_famMembers_detail());
        }
    }

    function load_famMembers_details_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $FamMasterID = trim($this->input->post('FamMasterID') ?? '');
        $convertFormat = convert_date_format_sql();

        $data['famName'] = $this->db->query("SELECT FamilyName FROM srp_erp_ngo_com_familymaster WHERE srp_erp_ngo_com_familymaster.companyID='" . $companyID . "' AND srp_erp_ngo_com_familymaster.FamMasterID='" . $FamMasterID . "' ")->row_array();

        $this->db->select("srp_erp_ngo_com_familydetails.Com_MasterID,srp_erp_ngo_com_familydetails.FamMasterID,FamDel_ID,FamilyName,CFullName,CName_with_initials,CurrentStatus,DATE_FORMAT(FamMemAddedDate,'{$convertFormat}') AS FamMemAddedDate,name,relationship,isMove,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,srp_erp_ngo_com_communitymaster.isActive,srp_erp_ngo_com_communitymaster.DeactivatedFor");
        $this->db->from('srp_erp_ngo_com_familydetails');
        $this->db->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_familydetails.FamMasterID', 'left');
        $this->db->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familydetails.Com_MasterID', 'left');
        $this->db->join('srp_erp_gender', 'srp_erp_gender.genderID=srp_erp_ngo_com_communitymaster.GenderID', 'left');
        $this->db->join('srp_erp_family_relationship', 'srp_erp_family_relationship.relationshipID=srp_erp_ngo_com_familydetails.relationshipID', 'left');

        $this->db->where('srp_erp_ngo_com_familydetails.companyID', $companyID);
        $this->db->where('srp_erp_ngo_com_familydetails.FamMasterID', $FamMasterID);

        $this->db->order_by('FamDel_ID', 'asc');
        $data['header'] = $this->db->get()->result_array();
        $this->load->view('system/CommunityNgo/ajax/load_com_ngo_family_members', $data);
    }

    function delete_familyMemDetail()
    {
        $FamDel_ID = $this->input->post('FamDel_ID');
        $this->db->delete('srp_erp_ngo_com_familydetails', array('FamDel_ID' => $FamDel_ID));
        echo json_encode(array('s', 'The family member successfully deleted'));
    }

    function familyMaster_exist()
    {
        $FamMasterID = trim($this->input->post('FamMasterID') ?? '');
        $data = $this->db->query("select * from srp_erp_ngo_com_familymaster WHERE FamMasterID={$FamMasterID} ")->row_array();
        echo json_encode($data);
    }

    function fetch_item_detail()
    {
        $FamDel_ID = $this->input->post('FamDel_ID');
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query("select FamDel_ID,FamMasterID ,Com_MasterID,srp_erp_ngo_com_familydetails.relationshipID,isMove,relationship, DATE_FORMAT(FamMemAddedDate,'{$convertFormat}') AS FamMemAddedDate from srp_erp_ngo_com_familydetails LEFT JOIN srp_erp_family_relationship ON srp_erp_family_relationship.relationshipID=srp_erp_ngo_com_familydetails.relationshipID WHERE FamDel_ID={$FamDel_ID} ")->row_array();
        echo json_encode($data);
    }

    function update_comFem_member_details()
    {

        $this->form_validation->set_rules("Com_MasterID", 'Member', 'trim|required');
        $this->form_validation->set_rules("relationshipID", 'Relationship', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $FamMasterID = $this->input->post('FamMasterID');
            $master = $this->db->query("select FamilyAddedDate from srp_erp_ngo_com_familymaster WHERE FamMasterID=$FamMasterID ")->row_array();
            $FamilyAddedDate = $master['FamilyAddedDate'];
            $FamMemAddedDate = $this->input->post('FamMemAddedDate');
            $date_format_policy = date_format_policy();
            $FamMemAddedDate = $FamMemAddedDate;
            $expire = input_format_date($FamMemAddedDate, $date_format_policy);
            if ($expire < $FamilyAddedDate) {
                echo json_encode(array('e', 'Unable to proceed. Member added date is greater than family added date'));
                exit;

            }
            echo json_encode($this->CommunityNgo_model->update_comFem_member_details());
        }
    }

    function delete_family_master()
    {
        echo json_encode($this->CommunityNgo_model->delete_family_master());
    }

    function familyCreate_confirmation()
    {
        echo json_encode($this->CommunityNgo_model->familyCreate_confirmation());
    }

    function load_community_family_confirmation()
    {
        $FamMasterID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('FamMasterID') ?? '');
        $data['extra'] = $this->CommunityNgo_model->fetch_commFamily_confirmation($FamMasterID);
        $html = $this->load->view('system/CommunityNgo/ngo_mo_familyDetails_print', $data, TRUE);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['confirmedYN']);
        }
    }

    function referback_family_creation()
    {
        $FamMasterID = $this->input->post('FamMasterID');

        $dataUpdate = array(
            'confirmedYN' => 0,
            'confirmedByEmpID' => '',
            'confirmedByName' => '',
            'confirmedDate' => '',
        );

        $this->db->where('FamMasterID', $FamMasterID);
        $this->db->update('srp_erp_ngo_com_familymaster', $dataUpdate);

        echo json_encode(array('s', ' Referred Back Successfully.'));

    }

    function load_familyMasterDetails()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $FamMasterID = trim($this->input->post('FamMasterID') ?? '');

        $WhereFamMasterID = " AND FamMasterID = '" . $FamMasterID . "' ";
        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $WhereFamMasterID;

        $data['master'] = $this->db->query("SELECT *,srp_erp_ngo_com_communitymaster.SerialNo AS SerialNos,srp_erp_ngo_com_familymaster.createdDateTime,LedgerNo,srp_erp_ngo_com_familymaster.createdUserName,srp_erp_ngo_com_familymaster.modifiedDateTime,areac.stateID,areac.Description AS arDescription,divisionc.stateID,divisionc.Description AS diviDescription,srp_erp_ngo_com_maritalstatus.maritalstatusID,srp_erp_ngo_com_maritalstatus.maritalstatus FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_bloodgrouptype ON srp_erp_bloodgrouptype.BloodTypeID = srp_erp_ngo_com_communitymaster.BloodGroupID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus WHERE $where  ")->row_array();

        $convertFormat = convert_date_format_sql();
        $this->db->select("srp_erp_ngo_com_familydetails.Com_MasterID,srp_erp_ngo_com_familydetails.FamMasterID,FamDel_ID,FamilyName,CName_with_initials,CDOB,Age,CFullName,DATE_FORMAT(FamMemAddedDate,'{$convertFormat}') AS FamMemAddedDate,name,relationship,CurrentStatus,isMove,srp_erp_ngo_com_communitymaster.isActive,srp_erp_ngo_com_communitymaster.DeactivatedFor,divisionc.stateID,divisionc.Description AS diviDescription,srp_erp_ngo_com_maritalstatus.maritalstatusID,srp_erp_ngo_com_maritalstatus.maritalstatus");
        $this->db->from('srp_erp_ngo_com_familydetails');
        $this->db->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_familydetails.FamMasterID', 'left');
        $this->db->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familydetails.Com_MasterID', 'left');
        $this->db->join('srp_erp_gender', 'srp_erp_gender.genderID=srp_erp_ngo_com_communitymaster.GenderID', 'left');
        $this->db->join('srp_erp_statemaster divisionc', 'divisionc.stateID=srp_erp_ngo_com_communitymaster.GS_Division', 'left');
        $this->db->join('srp_erp_ngo_com_maritalstatus', 'srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus', 'left');
        $this->db->join('srp_erp_family_relationship', 'srp_erp_family_relationship.relationshipID=srp_erp_ngo_com_familydetails.relationshipID', 'left');

        $this->db->where('srp_erp_ngo_com_familydetails.companyID', $companyID);
        $this->db->where('srp_erp_ngo_com_familydetails.FamMasterID', $FamMasterID);

        $this->db->order_by('FamDel_ID', 'asc');
        $data['loadFamMem'] = $this->db->get()->result_array();

        $this->load->view('system/communityNgo/ajax/load_familyMaster_Details', $data);
    }

    function familyMaster_attachement_upload()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        $this->form_validation->set_rules('documentAutoID', 'Document Auto ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_ngo_attachments')->result_array();
            $file_name = 'Member' . $this->input->post('documentID') . '_' . $this->input->post('documentAutoID') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload("document_file")) {
                echo json_encode(array('status' => 0, 'type' => 'w',
                    'message' => 'Upload failed ' . $this->upload->display_errors()));
            } else {
                $upload_data = $this->upload->data();

                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                $data['myFileName'] = $file_name . $upload_data["file_ext"];
                $data['fileType'] = trim($upload_data["file_ext"]);
                $data['fileSize'] = trim($upload_data["file_size"]);
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
                    echo json_encode(array('status' => 0, 'type' => 'e',
                        'message' => 'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 1, 'type' => 's',
                        'message' => 'Successfully ' . $file_name . ' uploaded.'));
                }
            }
        }
    }

    function load_family_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $Com_MasterID = trim($this->input->post('Com_MasterID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = '7' AND documentAutoID = " . $Com_MasterID . "";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_attachments');
        $this->db->where($where);
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/communityNgo/ajax/load_ngo_member_attachments', $data);
    }

    function delete_family_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {
            $this->db->delete('srp_erp_ngo_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(TRUE);
        }
    }


    function fetch_familyRelationships_list()
    {
        $FamMasterID = trim($this->input->post('FamMasterID') ?? '');
        $data['famArray'] = $this->CommunityNgo_model->fetch_commFamily_confirmation($FamMasterID);
        $this->load->view('system/communityNgo/ajax/load_familyRelationships_list', $data);
    }

    function fetch_family_for_excel()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('femKey') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $AncestId = trim($this->input->post('AncestId') ?? '');
        $convertFormat = convert_date_format_sql();

        $filter_string = '';
        if (isset($text) && !empty($text)) {
            $filter_string = " AND ((FamilySystemCode Like '%" . $text . "%') OR (FamilyName Like '%" . $text . "%') OR (LedgerNo Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (FamilyCode Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";
        }
        $filter_sorting = '';
        if (isset($sorting) && $sorting != '#') {
            $filter_sorting = " AND FamilyName Like '" . $sorting . "%'";
        }
        $deleted = " AND srp_erp_ngo_com_familymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_familymaster.companyID = " . $companyID . $filter_string . $filter_sorting . $deleted;


        if ($AncestId == '-2' || $AncestId == null || $AncestId == '') {
            $details = $this->db->query("SELECT srp_erp_ngo_com_familymaster.companyID,srp_erp_ngo_com_familymaster.createdUserID,LeaderID,FamilyCode,FamilyName,confirmedYN,srp_erp_ngo_com_familymaster.FamMasterID,FamilySystemCode,DATE_FORMAT(FamilyAddedDate,'{$convertFormat}') AS FamilyAddedDate, LedgerNo, CName_with_initials,FamAncestory,ComEconSteID,TP_home,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,Description,GS_Division,GS_No,srp_erp_ngo_com_ancestrycategory.AncestryCatID,srp_erp_ngo_com_ancestrycategory.AncestryDes FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster on Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_ancestrycategory ON srp_erp_ngo_com_ancestrycategory.AncestryCatID=srp_erp_ngo_com_familymaster.AncestryCatID WHERE $where ORDER BY FamMasterID DESC")->result_array();
        } else if ($AncestId != '-2' || $AncestId != null || $AncestId != '') {
            $details = $this->db->query("SELECT srp_erp_ngo_com_familymaster.companyID,srp_erp_ngo_com_familymaster.createdUserID,LeaderID,FamilyCode,FamilyName,confirmedYN,srp_erp_ngo_com_familymaster.FamMasterID,FamilySystemCode,DATE_FORMAT(FamilyAddedDate,'{$convertFormat}') AS FamilyAddedDate, LedgerNo, CName_with_initials,FamAncestory,ComEconSteID,TP_home,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,Description,GS_Division,GS_No,srp_erp_ngo_com_ancestrycategory.AncestryCatID,srp_erp_ngo_com_ancestrycategory.AncestryDes FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster on Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_ancestrycategory ON srp_erp_ngo_com_ancestrycategory.AncestryCatID=srp_erp_ngo_com_familymaster.AncestryCatID WHERE $where AND FamAncestory='" . $AncestId . "' ORDER BY FamMasterID DESC")->result_array();
        }

        $data = array();

        $a = 1;
        foreach ($details as $row) {

            $queryFM4 = $this->db->query("SELECT Com_MasterID FROM srp_erp_ngo_com_familydetails WHERE companyID='" . $row['companyID'] . "' AND FamMasterID='" . $row['FamMasterID'] . "'");
            $rowFM4 = $queryFM4->result();
            $femMem2 = array();
            $totalMm = 1;
            foreach ($rowFM4 as $resFM4) {
                $femMem2[] = $resFM4->Com_MasterID;

                $totMm = $totalMm++;

            }

            if ($row['FamAncestory'] == 0) {
                $description = 'Local';
            } else {
                $description = 'Outside';
            }

            $qmHousing = $this->db->query("SELECT FamMasterID FROM srp_erp_ngo_com_house_enrolling WHERE companyID='" . $row['companyID'] . "' AND FamMasterID ='" . $row['FamMasterID'] . "'");
            $datHousing= $qmHousing->row();

            if (!empty($datHousing)) {
                $hosCount = 'Yes';
            } else {
                $hosCount = 'No';
            }

            $data[] = array(
                'Num' => $a,
                'FamilySystemCode' => $row['FamilySystemCode'],
                'LedgerNo' => $row['LedgerNo'],
                'FamilyName' => $row['FamilyName'],
                'CName_with_initials' => $row['CName_with_initials'],
                'description' => $description,
                'FamilyAddedDate' => $row['FamilyAddedDate'],
                'totMm' => $totMm,
                'husCount' => $hosCount,
                'TP_Mobile' => $row['TP_Mobile'],
                'TP_home' => $row['TP_home'],
                'EmailID' => $row['EmailID'],
                'C_Address' => $row['C_Address'],
                'P_Address' => $row['P_Address'],
                'HouseNo' => $row['HouseNo'],
                'Description' => $row['Description'],
                'GS_Division' => $row['GS_Division'],
                'GS_No' => $row['GS_No'],
            );
            $a++;
        }

        return ['comFamily' => $data];

    }

    /*new Relationship category*/
    public function new_RelationCat()
    {
        $this->form_validation->set_rules('Relatn', 'Relationship Category', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->new_RelationCat());
        }
    }

    function get_memMoveState()
    {

        $editFamMasID = $this->input->post("editFamMasID");
        $edit_femMm = $this->input->post("edit_femMm");

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_memMoveState($editFamMasID, $edit_femMm);

    }

    //export family to excel
    function exportFamily_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Community Family List');
        $this->load->database();
        $data = $this->fetch_family_for_excel();

        $header = ['#', 'LEDGER NO', 'REFERENCE NO', 'FAMILY NAME', 'HEAD OF THE FAMILY', 'ANCESTRY', 'ADDED DATE', 'TOTAL MEMBERS OF FAMILY', 'HOUSE ENROLLED
        ', 'PHONE (PRIMARY)', 'PHONE (SECONDARY)', 'EMAIL', 'CONTACT ADDRESS', 'PERMANENT ADDRESS', 'HOUSE NO', 'REGION', 'GS DIVISION', 'GS NO'];
        $comFamily = $data['comFamily'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Community Family List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:Q4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:Q4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($comFamily, null, 'A6');
        ob_clean();
        ob_start(); # added
        $filename = 'Family Details.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        ob_clean();
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    //end of family Master

    /*community report details*/
    function get_communityFamily_status__pdf()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('femKey') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $AncestId = trim($this->input->post('AncestId') ?? '');
        $convertFormat = convert_date_format_sql();

        $filter_string = '';
        if (isset($text) && !empty($text)) {
            $filter_string = " AND ((FamilySystemCode Like '%" . $text . "%') OR (FamilyName Like '%" . $text . "%') OR (LedgerNo Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (FamilyCode Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";
        }
        $filter_sorting = '';
        if (isset($sorting) && $sorting != '#') {
            $filter_sorting = " AND FamilyName Like '" . $sorting . "%'";
        }
        $deleted = " AND srp_erp_ngo_com_familymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_familymaster.companyID = " . $companyID . $filter_string . $filter_sorting . $deleted;

        if ($AncestId == '-2' || $AncestId == null || $AncestId == '') {
            $data['familyMas'] = $this->db->query("SELECT srp_erp_ngo_com_familymaster.companyID,srp_erp_ngo_com_familymaster.createdUserID,LeaderID,FamilyCode,FamilyName,confirmedYN,srp_erp_ngo_com_familymaster.FamMasterID,FamilySystemCode,DATE_FORMAT(FamilyAddedDate,'{$convertFormat}') AS FamilyAddedDate, LedgerNo, CName_with_initials,FamAncestory,ComEconSteID,TP_home,TP_Mobile FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster on Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID  WHERE $where ORDER BY FamMasterID DESC")->result_array();
        } else if ($AncestId != '-2' || $AncestId != null || $AncestId != '') {
            $data['familyMas'] = $this->db->query("SELECT srp_erp_ngo_com_familymaster.companyID,srp_erp_ngo_com_familymaster.createdUserID,LeaderID,FamilyCode,FamilyName,confirmedYN,srp_erp_ngo_com_familymaster.FamMasterID,FamilySystemCode,DATE_FORMAT(FamilyAddedDate,'{$convertFormat}') AS FamilyAddedDate, LedgerNo, CName_with_initials,FamAncestory,ComEconSteID,TP_home,TP_Mobile FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster on Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID  WHERE $where AND srp_erp_ngo_com_familymaster.FamAncestory='" . $AncestId . "' ORDER BY FamMasterID DESC")->result_array();
        }
        // $data["type"] = "pdf";
        $html = $this->load->view('system/communityNgo/ajax/load_com_ngo_families_pdf', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    /*community Occupation report details*/

    function get_communityMem_status_report()
    {
        $date_format_policy = date_format_policy();

        $memberTypes = $this->input->post("memberType");
        $JobCatIds = $this->input->post("JobCatId");
        $schlIds = $this->input->post("schlId");
        $MedumIds = $this->input->post("MedumId");
        $classIds = $this->input->post("classId");

        $text = trim($this->input->post('PlaceDes') ?? '');

        if ($memberTypes == '1') {
            $JobCatId = '';
            $schlId = $schlIds;
            $MedumId = $MedumIds;
            $classId = $classIds;
        } else {
            $JobCatId = $JobCatIds;
            $schlId = '';
            $MedumId = '';
            $classId = '';
        }

        $convertFormat = convert_date_format_sql();

        $filter_re = array("AND (srp_erp_ngo_com_memjobs.JobCategoryID=" . $JobCatId . ")" => $JobCatId, "AND (srp_erp_ngo_com_memjobs.schoolComID='" . $schlId . "')" => $schlId, "AND (srp_erp_ngo_com_memjobs.LanguageID='" . $MedumId . "')" => $MedumId, "AND (srp_erp_ngo_com_memjobs.gradeComID='" . $classId . "')" => $classId);
        $set_filter_re = array_filter($filter_re);
        $where_clause = join(" ", array_keys($set_filter_re));

        $srch_string = '';
        if (isset($text) && !empty($text)) {
            if ($memberTypes == '8') {
                $srch_string = " AND ((CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";
            } else {
                $srch_string = " AND ((WorkingPlace Like '%" . $text . "%') OR (srp_erp_ngo_com_occupationtypes.Description Like '%" . $text . "%') OR (srp_erp_ngo_com_memjobs.DateFrom Like '%" . $text . "%') OR (gradeComDes Like '%" . $text . "%') OR (JobCatDescription Like '%" . $text . "%') OR(Address Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";

            }
        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_string;

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_ngo_com_memjobs.Com_MasterID  FROM srp_erp_ngo_com_memjobs WHERE srp_erp_ngo_com_memjobs.companyID={$companyID} ");
        $rowFP = $queryFP->result();
        $memInr = array();
        foreach ($rowFP as $resFP) {

            $memInr[] = $resFP->Com_MasterID;

        }

        $in_memPrt = "'" . implode("', '", $memInr) . "'";

        $this->form_validation->set_rules('memberType', 'Member Type', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            if (!empty($memberTypes)) {
                if ($memberTypes == '-1') {

                    $data['memReport'] = $this->db->query("SELECT srp_erp_ngo_com_communitymaster.companyID,srp_erp_ngo_com_communitymaster.createdUserID,srp_erp_ngo_com_memjobs.gradeComID,srp_erp_ngo_com_memjobs.WorkingPlace,srp_erp_ngo_com_memjobs.Address,DATE_FORMAT(srp_erp_ngo_com_memjobs.DateFrom,'{$convertFormat}') AS DateFrom,srp_erp_ngo_com_memjobs.DateTo,srp_erp_ngo_com_memjobs.MemJobID,srp_erp_ngo_com_memjobs.OccTypeID,srp_erp_ngo_com_memjobs.JobCategoryID,srp_erp_ngo_com_memjobs.isPrimary, CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_ngo_com_occupationtypes.OccTypeID,(srp_erp_ngo_com_occupationtypes.Description) AS OcDescription,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_grades.gradeComID,srp_erp_ngo_com_grades.gradeComDes,srp_erp_ngo_com_jobcategories.JobCategoryID,srp_erp_ngo_com_jobcategories.JobCatDescription,srp_erp_ngo_com_schools.schoolComID,srp_erp_ngo_com_schools.schoolComDes FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_ngo_com_memjobs ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memjobs.Com_MasterID  LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_occupationtypes.OccTypeID=srp_erp_ngo_com_memjobs.OccTypeID LEFT JOIN srp_erp_ngo_com_grades ON srp_erp_ngo_com_memjobs.gradeComID=srp_erp_ngo_com_grades.gradeComID LEFT JOIN srp_erp_ngo_com_schools ON srp_erp_ngo_com_memjobs.schoolComID=srp_erp_ngo_com_schools.schoolComID LEFT JOIN srp_erp_ngo_com_jobcategories ON srp_erp_ngo_com_jobcategories.JobCategoryID=srp_erp_ngo_com_memjobs.JobCategoryID WHERE $where " . $where_clause . " ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC")->result_array();

                    //   $data['memReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_memjobs.Com_MasterID,srp_erp_ngo_com_memjobs.gradeComID,srp_erp_ngo_com_memjobs.WorkingPlace,srp_erp_ngo_com_memjobs.Address,DATE_FORMAT(srp_erp_ngo_com_memjobs.DateFrom,'{$convertFormat}') AS DateFrom,srp_erp_ngo_com_memjobs.DateTo,srp_erp_ngo_com_memjobs.MemJobID,srp_erp_ngo_com_memjobs.OccTypeID,srp_erp_ngo_com_memjobs.JobCategoryID,srp_erp_ngo_com_grades.gradeComID,srp_erp_ngo_com_grades.gradeComDes,srp_erp_ngo_com_jobcategories.JobCategoryID,srp_erp_ngo_com_jobcategories.JobCatDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_memjobs ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memjobs.Com_MasterID LEFT JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_occupationtypes.OccTypeID=srp_erp_ngo_com_memjobs.OccTypeID LEFT JOIN srp_erp_ngo_com_grades ON srp_erp_ngo_com_memjobs.gradeComID=srp_erp_ngo_com_grades.gradeComID LEFT JOIN srp_erp_ngo_com_jobcategories ON srp_erp_ngo_com_jobcategories.JobCategoryID=srp_erp_ngo_com_memjobs.JobCategoryID WHERE $where " . $where_clause . " ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

                } elseif ($memberTypes == '8') {

                    $data['memReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE Com_MasterID NOT IN ($in_memPrt) AND $where ORDER BY Com_MasterID DESC ")->result_array();


                } else {
                    $MemType = $memberTypes;

                    $data['memReport'] = $this->db->query("SELECT srp_erp_ngo_com_memjobs.companyID,srp_erp_ngo_com_memjobs.createdUserID,srp_erp_ngo_com_memjobs.gradeComID,srp_erp_ngo_com_memjobs.WorkingPlace,srp_erp_ngo_com_memjobs.Address,DATE_FORMAT(srp_erp_ngo_com_memjobs.DateFrom,'{$convertFormat}') AS DateFrom,srp_erp_ngo_com_memjobs.DateTo,srp_erp_ngo_com_memjobs.MemJobID,srp_erp_ngo_com_memjobs.OccTypeID,srp_erp_ngo_com_memjobs.JobCategoryID,srp_erp_ngo_com_memjobs.isPrimary, CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_ngo_com_occupationtypes.OccTypeID,(srp_erp_ngo_com_occupationtypes.Description) AS OcDescription,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_grades.gradeComID,srp_erp_ngo_com_grades.gradeComDes,srp_erp_ngo_com_jobcategories.JobCategoryID,srp_erp_ngo_com_jobcategories.JobCatDescription,srp_erp_ngo_com_schools.schoolComID,srp_erp_ngo_com_schools.schoolComDes FROM srp_erp_ngo_com_memjobs INNER JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memjobs.Com_MasterID  LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_occupationtypes.OccTypeID=srp_erp_ngo_com_memjobs.OccTypeID LEFT JOIN srp_erp_ngo_com_grades ON srp_erp_ngo_com_memjobs.gradeComID=srp_erp_ngo_com_grades.gradeComID LEFT JOIN srp_erp_ngo_com_schools ON srp_erp_ngo_com_memjobs.schoolComID=srp_erp_ngo_com_schools.schoolComID LEFT JOIN srp_erp_ngo_com_jobcategories ON srp_erp_ngo_com_jobcategories.JobCategoryID=srp_erp_ngo_com_memjobs.JobCategoryID WHERE srp_erp_ngo_com_memjobs.companyID = {$companyID} AND srp_erp_ngo_com_memjobs.OccTypeID='" . $MemType . "' AND $where " . $where_clause . " ORDER BY srp_erp_ngo_com_memjobs.Com_MasterID DESC")->result_array();

                }
            }

            $data["type"] = "html";
            echo $html = $this->load->view('system/communityNgo/ajax/load-community-member-status-report', $data, true);
        }
    }

    function get_communityMem_status_report_pdf()
    {
        $date_format_policy = date_format_policy();
        $memberTypes = $this->input->post("memberType");
        $JobCatIds = $this->input->post("JobCatId");
        $schlIds = $this->input->post("schlId");
        $MedumIds = $this->input->post("MedumId");
        $classIds = $this->input->post("classId");
        $text = trim($this->input->post('PlaceDes') ?? '');

        if ($memberTypes == '1') {
            $JobCatId = '';
            $schlId = $schlIds;
            $MedumId = $MedumIds;
            $classId = $classIds;
        } else {
            $JobCatId = $JobCatIds;
            $schlId = '';
            $MedumId = '';
            $classId = '';
        }

        $convertFormat = convert_date_format_sql();

        $filter_re = array("AND (srp_erp_ngo_com_memjobs.JobCategoryID=" . $JobCatId . ")" => $JobCatId, "AND (srp_erp_ngo_com_memjobs.schoolComID='" . $schlId . "')" => $schlId, "AND (srp_erp_ngo_com_memjobs.LanguageID='" . $MedumId . "')" => $MedumId, "AND (srp_erp_ngo_com_memjobs.gradeComID='" . $classId . "')" => $classId);
        $set_filter_re = array_filter($filter_re);
        $where_clause = join(" ", array_keys($set_filter_re));

        $srch_string = '';
        if (isset($text) && !empty($text)) {
            if ($memberTypes == '8') {
                $srch_string = " AND ((CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";
            } else {
                $srch_string = " AND ((WorkingPlace Like '%" . $text . "%') OR (srp_erp_ngo_com_occupationtypes.Description Like '%" . $text . "%') OR (srp_erp_ngo_com_memjobs.DateFrom Like '%" . $text . "%') OR (gradeComDes Like '%" . $text . "%') OR (JobCatDescription Like '%" . $text . "%') OR(Address Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";

            }
        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_string;

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_ngo_com_memjobs.Com_MasterID  FROM srp_erp_ngo_com_memjobs WHERE srp_erp_ngo_com_memjobs.companyID={$companyID} ");
        $rowFP = $queryFP->result();
        $memInr = array();
        foreach ($rowFP as $resFP) {

            $memInr[] = $resFP->Com_MasterID;

        }

        $in_memPrt = "'" . implode("', '", $memInr) . "'";

        $this->form_validation->set_rules('memberType', 'Member Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {

            if (!empty($memberTypes)) {
                if ($memberTypes == '-1') {

                    // $data['memReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_memjobs.Com_MasterID,srp_erp_ngo_com_memjobs.gradeComID,srp_erp_ngo_com_memjobs.WorkingPlace,srp_erp_ngo_com_memjobs.Address,DATE_FORMAT(srp_erp_ngo_com_memjobs.DateFrom,'{$convertFormat}') AS DateFrom,srp_erp_ngo_com_memjobs.DateTo,srp_erp_ngo_com_memjobs.MemJobID,srp_erp_ngo_com_memjobs.OccTypeID,srp_erp_ngo_com_memjobs.JobCategoryID,srp_erp_ngo_com_grades.gradeComID,srp_erp_ngo_com_grades.gradeComDes,srp_erp_ngo_com_jobcategories.JobCategoryID,srp_erp_ngo_com_jobcategories.JobCatDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID  LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_memjobs ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memjobs.Com_MasterID LEFT JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_occupationtypes.OccTypeID=srp_erp_ngo_com_memjobs.OccTypeID LEFT JOIN srp_erp_ngo_com_grades ON srp_erp_ngo_com_memjobs.gradeComID=srp_erp_ngo_com_grades.gradeComID LEFT JOIN srp_erp_ngo_com_jobcategories ON srp_erp_ngo_com_jobcategories.JobCategoryID=srp_erp_ngo_com_memjobs.JobCategoryID WHERE $where " . $where_clause . " ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

                    $data['memReport'] = $this->db->query("SELECT srp_erp_ngo_com_communitymaster.companyID,srp_erp_ngo_com_communitymaster.createdUserID,srp_erp_ngo_com_memjobs.gradeComID,srp_erp_ngo_com_memjobs.WorkingPlace,srp_erp_ngo_com_memjobs.Address,DATE_FORMAT(srp_erp_ngo_com_memjobs.DateFrom,'{$convertFormat}') AS DateFrom,srp_erp_ngo_com_memjobs.DateTo,srp_erp_ngo_com_memjobs.MemJobID,srp_erp_ngo_com_memjobs.OccTypeID,srp_erp_ngo_com_memjobs.JobCategoryID,srp_erp_ngo_com_memjobs.isPrimary, CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_ngo_com_occupationtypes.OccTypeID,(srp_erp_ngo_com_occupationtypes.Description) AS OcDescription,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_grades.gradeComID,srp_erp_ngo_com_grades.gradeComDes,srp_erp_ngo_com_jobcategories.JobCategoryID,srp_erp_ngo_com_jobcategories.JobCatDescription,srp_erp_ngo_com_schools.schoolComID,srp_erp_ngo_com_schools.schoolComDes FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_ngo_com_memjobs ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memjobs.Com_MasterID  LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID  LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_occupationtypes.OccTypeID=srp_erp_ngo_com_memjobs.OccTypeID LEFT JOIN srp_erp_ngo_com_grades ON srp_erp_ngo_com_memjobs.gradeComID=srp_erp_ngo_com_grades.gradeComID LEFT JOIN srp_erp_ngo_com_schools ON srp_erp_ngo_com_memjobs.schoolComID=srp_erp_ngo_com_schools.schoolComID LEFT JOIN srp_erp_ngo_com_jobcategories ON srp_erp_ngo_com_jobcategories.JobCategoryID=srp_erp_ngo_com_memjobs.JobCategoryID WHERE $where " . $where_clause . " ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC")->result_array();

                } elseif ($memberTypes == '8') {

                    $data['memReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID  WHERE Com_MasterID NOT IN ($in_memPrt) AND $where ORDER BY Com_MasterID DESC ")->result_array();


                } else {
                    $MemType = $memberTypes;

                    $data['memReport'] = $this->db->query("SELECT srp_erp_ngo_com_memjobs.companyID,srp_erp_ngo_com_memjobs.createdUserID,srp_erp_ngo_com_memjobs.OccTypeID,srp_erp_ngo_com_memjobs.gradeComID,srp_erp_ngo_com_memjobs.WorkingPlace,srp_erp_ngo_com_memjobs.Address,DATE_FORMAT(srp_erp_ngo_com_memjobs.DateFrom,'{$convertFormat}') AS DateFrom,srp_erp_ngo_com_memjobs.DateTo,srp_erp_ngo_com_memjobs.MemJobID,srp_erp_ngo_com_memjobs.JobCategoryID,srp_erp_ngo_com_memjobs.isPrimary, CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_ngo_com_occupationtypes.OccTypeID,(srp_erp_ngo_com_occupationtypes.Description) AS OcDescription,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_grades.gradeComID,srp_erp_ngo_com_grades.gradeComDes,srp_erp_ngo_com_jobcategories.JobCategoryID,srp_erp_ngo_com_jobcategories.JobCatDescription,srp_erp_ngo_com_schools.schoolComID,srp_erp_ngo_com_schools.schoolComDes FROM srp_erp_ngo_com_memjobs INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memjobs.Com_MasterID  LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_occupationtypes ON srp_erp_ngo_com_occupationtypes.OccTypeID=srp_erp_ngo_com_memjobs.OccTypeID LEFT JOIN srp_erp_ngo_com_grades ON srp_erp_ngo_com_memjobs.gradeComID=srp_erp_ngo_com_grades.gradeComID LEFT JOIN srp_erp_ngo_com_schools ON srp_erp_ngo_com_memjobs.schoolComID=srp_erp_ngo_com_schools.schoolComID LEFT JOIN srp_erp_ngo_com_jobcategories ON srp_erp_ngo_com_jobcategories.JobCategoryID=srp_erp_ngo_com_memjobs.JobCategoryID WHERE srp_erp_ngo_com_memjobs.companyID = {$companyID} AND srp_erp_ngo_com_memjobs.OccTypeID='" . $MemType . "' AND $where " . $where_clause . " ORDER BY srp_erp_ngo_com_memjobs.Com_MasterID DESC")->result_array();

                }
            }

            $data["type"] = "pdf";
            $html = $this->load->view('system/communityNgo/ajax/load-community-member-status-report', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    /*community Qualification report details*/

    function get_communityMem_qual_report()
    {
        $date_format_policy = date_format_policy();

        $qualMemType = $this->input->post("qualMemType");
        $InstituteId = $this->input->post("InstituteId");

        $text = trim($this->input->post('qualSerch') ?? '');


        $convertFormat = convert_date_format_sql();

        if ($qualMemType == '-4') {
            $filter_req = array("AND (srp_erp_ngo_com_qualifications.UniversityID='" . $InstituteId . "')" => $InstituteId);
            $set_filter_req = array_filter($filter_req);
            $where_clauseq = join(" ", array_keys($set_filter_req));
        } else {
            $filter_req = array("AND (srp_erp_ngo_com_qualifications.DegreeID=" . $qualMemType . ")" => $qualMemType, "AND (srp_erp_ngo_com_qualifications.UniversityID='" . $InstituteId . "')" => $InstituteId);
            $set_filter_req = array_filter($filter_req);
            $where_clauseq = join(" ", array_keys($set_filter_req));
        }


        $srch_string = '';
        if (isset($text) && !empty($text)) {
            if ($qualMemType == '-5') {
                $srch_string = " AND ((CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";
            } else {
                $srch_string = " AND ((Year Like '%" . $text . "%') OR (Remarks Like '%" . $text . "%')  OR (UniversityDescription Like '%" . $text . "%') OR (DegreeDescription Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";

            }
        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_string;

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_ngo_com_qualifications.Com_MasterID  FROM srp_erp_ngo_com_qualifications WHERE srp_erp_ngo_com_qualifications.companyID={$companyID} ");
        $rowFP = $queryFP->result();
        $memInr = array();
        foreach ($rowFP as $resFP) {

            $memInr[] = $resFP->Com_MasterID;

        }

        $in_memPrt = "'" . implode("', '", $memInr) . "'";

        $this->form_validation->set_rules('qualMemType', 'Qualification', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            if (!empty($qualMemType)) {
                if ($qualMemType == '-4') {

                    $data['qualReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_qualifications.Com_MasterID,srp_erp_ngo_com_qualifications.UniversityID,srp_erp_ngo_com_qualifications.DegreeID,srp_erp_ngo_com_qualifications.CurrentlyReading,srp_erp_ngo_com_qualifications.Year,srp_erp_ngo_com_qualifications.Remarks,srp_erp_ngo_com_universities.UniversityID,srp_erp_ngo_com_universities.UniversityDescription,srp_erp_ngo_com_degreecategories.DegreeID,srp_erp_ngo_com_degreecategories.DegreeDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_qualifications ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_qualifications.Com_MasterID LEFT JOIN srp_erp_ngo_com_universities ON srp_erp_ngo_com_qualifications.UniversityID=srp_erp_ngo_com_universities.UniversityID LEFT JOIN srp_erp_ngo_com_degreecategories ON srp_erp_ngo_com_degreecategories.DegreeID=srp_erp_ngo_com_qualifications.DegreeID WHERE $where " . $where_clauseq . " ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

                } elseif ($qualMemType == '-5') {

                    $data['qualReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE Com_MasterID NOT IN ($in_memPrt) AND $where ORDER BY Com_MasterID DESC ")->result_array();


                } else {
                    $qualType = $qualMemType;

                    $data['qualReport'] = $this->db->query("SELECT srp_erp_ngo_com_qualifications.companyID,srp_erp_ngo_com_qualifications.createdUserID,srp_erp_ngo_com_qualifications.UniversityID,srp_erp_ngo_com_qualifications.DegreeID,srp_erp_ngo_com_qualifications.CurrentlyReading,srp_erp_ngo_com_qualifications.Year,srp_erp_ngo_com_qualifications.Remarks, CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_universities.UniversityID,srp_erp_ngo_com_universities.UniversityDescription,srp_erp_ngo_com_degreecategories.DegreeID,srp_erp_ngo_com_degreecategories.DegreeDescription FROM srp_erp_ngo_com_qualifications INNER JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_qualifications.Com_MasterID  LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_universities ON srp_erp_ngo_com_qualifications.UniversityID=srp_erp_ngo_com_universities.UniversityID LEFT JOIN srp_erp_ngo_com_degreecategories ON srp_erp_ngo_com_degreecategories.DegreeID=srp_erp_ngo_com_qualifications.DegreeID WHERE srp_erp_ngo_com_qualifications.companyID = {$companyID} AND srp_erp_ngo_com_qualifications.DegreeID='" . $qualType . "' AND $where " . $where_clauseq . " ORDER BY srp_erp_ngo_com_qualifications.Com_MasterID DESC")->result_array();

                }
            }

            $data["type"] = "html";
            echo $html = $this->load->view('system/communityNgo/ajax/load_community_qualification_report', $data, true);
        }
    }

    function get_communityMem_qual_report_pdf()
    {
        $date_format_policy = date_format_policy();

        $qualMemType = $this->input->post("qualMemType");
        $InstituteId = $this->input->post("InstituteId");

        $text = trim($this->input->post('qualSerch') ?? '');

        $convertFormat = convert_date_format_sql();

        if ($qualMemType == '-4') {
            $filter_req = array("AND (srp_erp_ngo_com_qualifications.UniversityID='" . $InstituteId . "')" => $InstituteId);
            $set_filter_req = array_filter($filter_req);
            $where_clauseq = join(" ", array_keys($set_filter_req));
        } else {
            $filter_req = array("AND (srp_erp_ngo_com_qualifications.DegreeID=" . $qualMemType . ")" => $qualMemType, "AND (srp_erp_ngo_com_qualifications.UniversityID='" . $InstituteId . "')" => $InstituteId);
            $set_filter_req = array_filter($filter_req);
            $where_clauseq = join(" ", array_keys($set_filter_req));
        }

        $srch_string = '';
        if (isset($text) && !empty($text)) {
            if ($qualMemType == '-5') {
                $srch_string = " AND ((CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";
            } else {
                $srch_string = " AND ((Year Like '%" . $text . "%') OR (Remarks Like '%" . $text . "%')  OR (UniversityDescription Like '%" . $text . "%') OR (DegreeDescription Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";

            }
        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_string;

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_ngo_com_qualifications.Com_MasterID  FROM srp_erp_ngo_com_qualifications WHERE srp_erp_ngo_com_qualifications.companyID={$companyID} ");
        $rowFP = $queryFP->result();
        $memInr = array();
        foreach ($rowFP as $resFP) {

            $memInr[] = $resFP->Com_MasterID;

        }

        $in_memPrt = "'" . implode("', '", $memInr) . "'";

        $this->form_validation->set_rules('qualMemType', 'Qualification', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            if (!empty($qualMemType)) {
                if ($qualMemType == '-4') {

                    $data['qualReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_qualifications.Com_MasterID,srp_erp_ngo_com_qualifications.UniversityID,srp_erp_ngo_com_qualifications.DegreeID,srp_erp_ngo_com_qualifications.CurrentlyReading,srp_erp_ngo_com_qualifications.Year,srp_erp_ngo_com_qualifications.Remarks,srp_erp_ngo_com_universities.UniversityID,srp_erp_ngo_com_universities.UniversityDescription,srp_erp_ngo_com_degreecategories.DegreeID,srp_erp_ngo_com_degreecategories.DegreeDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_qualifications ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_qualifications.Com_MasterID LEFT JOIN srp_erp_ngo_com_universities ON srp_erp_ngo_com_qualifications.UniversityID=srp_erp_ngo_com_universities.UniversityID LEFT JOIN srp_erp_ngo_com_degreecategories ON srp_erp_ngo_com_degreecategories.DegreeID=srp_erp_ngo_com_qualifications.DegreeID WHERE $where " . $where_clauseq . " ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

                } elseif ($qualMemType == '-5') {

                    $data['qualReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE Com_MasterID NOT IN ($in_memPrt) AND $where ORDER BY Com_MasterID DESC ")->result_array();


                } else {
                    $qualType = $qualMemType;

                    $data['qualReport'] = $this->db->query("SELECT srp_erp_ngo_com_qualifications.companyID,srp_erp_ngo_com_qualifications.createdUserID,srp_erp_ngo_com_qualifications.UniversityID,srp_erp_ngo_com_qualifications.DegreeID,srp_erp_ngo_com_qualifications.CurrentlyReading,srp_erp_ngo_com_qualifications.Year,srp_erp_ngo_com_qualifications.Remarks, CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_universities.UniversityID,srp_erp_ngo_com_universities.UniversityDescription,srp_erp_ngo_com_degreecategories.DegreeID,srp_erp_ngo_com_degreecategories.DegreeDescription FROM srp_erp_ngo_com_qualifications INNER JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_qualifications.Com_MasterID  LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_universities ON srp_erp_ngo_com_qualifications.UniversityID=srp_erp_ngo_com_universities.UniversityID LEFT JOIN srp_erp_ngo_com_degreecategories ON srp_erp_ngo_com_degreecategories.DegreeID=srp_erp_ngo_com_qualifications.DegreeID WHERE srp_erp_ngo_com_qualifications.companyID = {$companyID} AND srp_erp_ngo_com_qualifications.DegreeID='" . $qualType . "' AND $where " . $where_clauseq . " ORDER BY srp_erp_ngo_com_qualifications.Com_MasterID DESC")->result_array();

                }
            }

            $data["type"] = "pdf";
            $html = $this->load->view('system/communityNgo/ajax/load_community_qualification_report', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    /*community general report details*/

    function get_communityMem_otrReport()
    {
        $date_format_policy = date_format_policy();

        $genType = $this->input->post("genType");
        $IsAbroad = $this->input->post("IsAbroad");
        $isConvert = $this->input->post("isConvertId");
        $stateId = $this->input->post("stateId");

        $ageOps = $this->input->post("ageOps");
        $ageFrm = trim($this->input->post('ageFrm') ?? '');
        $ageTo = trim($this->input->post('ageTo') ?? '');

        $bloodGrpID = $this->input->post("bloodGrpID");
        $sicknessID = $this->input->post("sicknessID");

        $BloodGroupID = "";
        if (!empty($bloodGrpID)) {
            $BloodGroupID = "AND srp_erp_ngo_com_communitymaster.BloodGroupID IN(" . join(',', $bloodGrpID) . ")";
        }

        $sickAutoID = "";
        if (!empty($sicknessID)) {
            $sickAutoID = "AND sicknessr.sickAutoID IN(" . join(',', $sicknessID) . ")";
        }

        if ($ageOps == '1') {
            $ageOppId = '=';
        } elseif ($ageOps == '2') {
            $ageOppId = '<';
        } elseif ($ageOps == '3') {
            $ageOppId = '>';
        } elseif ($ageOps == '4') {
            $ageOppId = '<=';
        } elseif ($ageOps == '5') {
            $ageOppId = '>=';
        } elseif ($ageOps == '6') {
            $ageOppId = '';
        }

        $convertFormat = convert_date_format_sql();

        $srch_age = '';
        if (isset($ageFrm) && !empty($ageFrm)) {
            if ($ageOps == '6') {
                $srch_age = " AND (trim(Age) BETWEEN " . $ageFrm . " AND " . $ageTo . ")";

            } else {
                $srch_age = " AND (trim(Age)" . $ageOppId . "" . $ageFrm . ")";
            }

        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";
        $isMove = " AND srp_erp_ngo_com_familydetails.isMove = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_age;

        if ($genType != '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert == '-8' || $isConvert == '') && ($stateId == '-6' || $stateId == '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert == '-8' || $isConvert == '') && ($stateId == '-6' || $stateId == '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($isConvert == '-8' || $isConvert == '') && ($stateId == '-6' || $stateId == '') && ($IsAbroad != '-7' || $IsAbroad != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($stateId == '-6' || $stateId == '') && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert == '-8' || $isConvert == '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($stateId == '-6' || $stateId == '') && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($isConvert == '-8' || $isConvert == '') && ($IsAbroad != '-7' || $IsAbroad != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert != '-8' || $isConvert != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($isConvert == '-8' || $isConvert == '') && ($stateId == '-6' || $stateId == '') && $genType != '-9' && ($IsAbroad != '-7' || $IsAbroad != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert == '-8' || $isConvert == '') && $genType != '-9' && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($IsAbroad == '-7' || $IsAbroad == '') && $genType != '-9' && ($isConvert != '-8' || $isConvert != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($IsAbroad == '-7' || $IsAbroad == '') && ($stateId == '-6' || $stateId == '') && $genType != '-9' && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($stateId == '-6' || $stateId == '') && $genType != '-9' && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($isConvert == '-8' || $isConvert == '') && $genType != '-9' && ($IsAbroad != '-7' || $IsAbroad != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType != '-9' && ($stateId != '-6' || $stateId != '') && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType != '-9' && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else {
            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        }

        $data["type"] = "html";
        echo $html = $this->load->view('system/communityNgo/ajax/load_community_other_report', $data, true);

    }

    function get_communityMem_otrReport_pdf()
    {
        $date_format_policy = date_format_policy();

        $genType = $this->input->post("genType");
        $IsAbroad = $this->input->post("IsAbroad");
        $isConvert = $this->input->post("isConvertId");
        $stateId = $this->input->post("stateId");

        $ageOps = $this->input->post("ageOps");
        $ageFrm = trim($this->input->post('ageFrm') ?? '');
        $ageTo = trim($this->input->post('ageTo') ?? '');

        if ($ageOps == '1') {
            $ageOppId = '=';
        } elseif ($ageOps == '2') {
            $ageOppId = '<';
        } elseif ($ageOps == '3') {
            $ageOppId = '>';
        } elseif ($ageOps == '4') {
            $ageOppId = '<=';
        } elseif ($ageOps == '5') {
            $ageOppId = '>=';
        } elseif ($ageOps == '6') {
            $ageOppId = '';
        }


        $bloodGrpID = $this->input->post("bloodGrpID");
        $sicknessID = $this->input->post("sicknessID");

        $BloodGroupID = "";
        if (!empty($bloodGrpID)) {
            $BloodGroupID = "AND srp_erp_ngo_com_communitymaster.BloodGroupID IN(" . join(',', $bloodGrpID) . ")";
        }

        $sickAutoID = "";
        if (!empty($sicknessID)) {
            $sickAutoID = "AND sicknessr.sickAutoID IN(" . join(',', $sicknessID) . ")";
        }

        $convertFormat = convert_date_format_sql();

        $srch_age = '';
        if (isset($ageFrm) && !empty($ageFrm)) {
            if ($ageOps == '6') {
                $srch_age = " AND (trim(Age) BETWEEN " . $ageFrm . " AND " . $ageTo . ")";

            } else {
                $srch_age = " AND (trim(Age)" . $ageOppId . "" . $ageFrm . ")";
            }

        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";
        $isMove = " AND srp_erp_ngo_com_familydetails.isMove = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_age;

        if ($genType != '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert == '-8' || $isConvert == '') && ($stateId == '-6' || $stateId == '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert == '-8' || $isConvert == '') && ($stateId == '-6' || $stateId == '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($isConvert == '-8' || $isConvert == '') && ($stateId == '-6' || $stateId == '') && ($IsAbroad != '-7' || $IsAbroad != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($stateId == '-6' || $stateId == '') && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert == '-8' || $isConvert == '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($stateId == '-6' || $stateId == '') && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($isConvert == '-8' || $isConvert == '') && ($IsAbroad != '-7' || $IsAbroad != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert != '-8' || $isConvert != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType == '-9' && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($isConvert == '-8' || $isConvert == '') && ($stateId == '-6' || $stateId == '') && $genType != '-9' && ($IsAbroad != '-7' || $IsAbroad != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($IsAbroad == '-7' || $IsAbroad == '') && ($isConvert == '-8' || $isConvert == '') && $genType != '-9' && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($IsAbroad == '-7' || $IsAbroad == '') && $genType != '-9' && ($isConvert != '-8' || $isConvert != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($IsAbroad == '-7' || $IsAbroad == '') && ($stateId == '-6' || $stateId == '') && $genType != '-9' && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($stateId == '-6' || $stateId == '') && $genType != '-9' && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if (($isConvert == '-8' || $isConvert == '') && $genType != '-9' && ($IsAbroad != '-7' || $IsAbroad != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType != '-9' && ($stateId != '-6' || $stateId != '') && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else if ($genType != '-9' && ($IsAbroad != '-7' || $IsAbroad != '') && ($isConvert != '-8' || $isConvert != '') && ($stateId != '-6' || $stateId != '')) {

            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE srp_erp_ngo_com_communitymaster.GenderID='" . $genType . "' AND srp_erp_ngo_com_communitymaster.IsAbroad='" . $IsAbroad . "' AND srp_erp_ngo_com_communitymaster.isConverted='" . $isConvert . "' AND srp_erp_ngo_com_communitymaster.CurrentStatus='" . $stateId . "' AND $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        } else {
            $data['commReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,TP_Mobile,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription,familyr.LeaderID,familyr.FamilySystemCode,familyr.FamMasterID,familDel.isMove,srp_erp_ngo_com_permanent_sickness.sickAutoID,srp_erp_ngo_com_permanent_sickness.sickDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_familydetails familDel ON familDel.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_familymaster familyr ON  familyr.FamMasterID =familDel.FamMasterID LEFT JOIN srp_erp_ngo_com_memberpersickness sicknessr ON sicknessr.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_ngo_com_permanent_sickness ON srp_erp_ngo_com_permanent_sickness.sickAutoID=sicknessr.sickAutoID WHERE $where $BloodGroupID $sickAutoID ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

        }

        $data["type"] = "pdf";
        $html = $this->load->view('system/communityNgo/ajax/load_community_other_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');

    }

    /*community division report details*/

    function get_communityMem_diviReport()
    {
        $date_format_policy = date_format_policy();

        $countyID = $this->input->post("countyID");
        $provinceID = $this->input->post("provinceID");
        $districtID = $this->input->post("districtID");
        $districtDivisionID = $this->input->post("districtDivisionID");
        $areaMemId = $this->input->post("areaMemId");
        $gsDivitnId = $this->input->post("gsDivitnId");

        // $text = trim($this->input->post('divitnSerch') ?? '');

        $convertFormat = convert_date_format_sql();

        if ($areaMemId == NULL || $areaMemId == "") {

            $filter_req = array("AND (srp_erp_ngo_com_communitymaster.countyID=" . $countyID . ")" => $countyID,"AND (srp_erp_ngo_com_communitymaster.provinceID=" . $provinceID . ")" => $provinceID,"AND (srp_erp_ngo_com_communitymaster.districtID=" . $districtID . ")" => $districtID,"AND (srp_erp_ngo_com_communitymaster.districtDivisionID=" . $districtDivisionID . ")" => $districtDivisionID,"AND (srp_erp_ngo_com_communitymaster.GS_Division='" . $gsDivitnId . "')" => $gsDivitnId);
            $set_filter_req = array_filter($filter_req);
            $where_clauseq = join(" ", array_keys($set_filter_req));

        } else {

            $filter_req = array("AND (srp_erp_ngo_com_communitymaster.countyID=" . $countyID . ")" => $countyID,"AND (srp_erp_ngo_com_communitymaster.provinceID=" . $provinceID . ")" => $provinceID,"AND (srp_erp_ngo_com_communitymaster.districtID=" . $districtID . ")" => $districtID,"AND (srp_erp_ngo_com_communitymaster.districtDivisionID=" . $districtDivisionID . ")" => $districtDivisionID,"AND (srp_erp_ngo_com_communitymaster.RegionID=" . $areaMemId . ")" => $areaMemId, "AND (srp_erp_ngo_com_communitymaster.GS_Division='" . $gsDivitnId . "')" => $gsDivitnId);
            $set_filter_req = array_filter($filter_req);
            $where_clauseq = join(" ", array_keys($set_filter_req));
        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted;

            if ($areaMemId == NULL || $areaMemId == "") {
                $data['diviReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID  LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE $where " . $where_clauseq . "  ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

            } else {
                $areaType = $areaMemId;

                $data['diviReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE srp_erp_ngo_com_communitymaster.RegionID='" . $areaType . "' AND $where " . $where_clauseq . "  ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

            }

        $data["type"] = "html";
        echo $html = $this->load->view('system/communityNgo/ajax/load_community_division_report', $data, true);

    }

    function get_communityMem_diviReport_pdf()
    {
        $date_format_policy = date_format_policy();

        $countyID = $this->input->post("countyID");
        $provinceID = $this->input->post("provinceID");
        $districtID = $this->input->post("districtID");
        $districtDivisionID = $this->input->post("districtDivisionID");
        $areaMemId = $this->input->post("areaMemId");
        $gsDivitnId = $this->input->post("gsDivitnId");

        // $text = trim($this->input->post('divitnSerch') ?? '');

        $convertFormat = convert_date_format_sql();

        if ($areaMemId == NULL || $areaMemId == "") {

            $filter_req = array("AND (srp_erp_ngo_com_communitymaster.countyID=" . $countyID . ")" => $countyID,"AND (srp_erp_ngo_com_communitymaster.provinceID=" . $provinceID . ")" => $provinceID,"AND (srp_erp_ngo_com_communitymaster.districtID=" . $districtID . ")" => $districtID,"AND (srp_erp_ngo_com_communitymaster.districtDivisionID=" . $districtDivisionID . ")" => $districtDivisionID,"AND (srp_erp_ngo_com_communitymaster.GS_Division='" . $gsDivitnId . "')" => $gsDivitnId);
            $set_filter_req = array_filter($filter_req);
            $where_clauseq = join(" ", array_keys($set_filter_req));

        } else {

            $filter_req = array("AND (srp_erp_ngo_com_communitymaster.countyID=" . $countyID . ")" => $countyID,"AND (srp_erp_ngo_com_communitymaster.provinceID=" . $provinceID . ")" => $provinceID,"AND (srp_erp_ngo_com_communitymaster.districtID=" . $districtID . ")" => $districtID,"AND (srp_erp_ngo_com_communitymaster.districtDivisionID=" . $districtDivisionID . ")" => $districtDivisionID,"AND (srp_erp_ngo_com_communitymaster.RegionID=" . $areaMemId . ")" => $areaMemId, "AND (srp_erp_ngo_com_communitymaster.GS_Division='" . $gsDivitnId . "')" => $gsDivitnId);
            $set_filter_req = array_filter($filter_req);
            $where_clauseq = join(" ", array_keys($set_filter_req));
        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted;

            if ($areaMemId == NULL || $areaMemId == '') {
                $data['diviReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE $where " . $where_clauseq . "  ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();

            } else {
                $areaType = $areaMemId;

                $data['diviReport'] = $this->db->query("SELECT *,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS divDescription FROM srp_erp_ngo_com_communitymaster LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE srp_erp_ngo_com_communitymaster.RegionID='" . $areaType . "' AND $where " . $where_clauseq . "  ORDER BY srp_erp_ngo_com_communitymaster.Com_MasterID DESC ")->result_array();
            }


        $data["type"] = "pdf";
        $html = $this->load->view('system/communityNgo/ajax/load_community_division_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');

    }

    //start of helping del
    /*community Help Requirements report details*/

    function get_communityMem_helpRq_report()
    {

        $helpRqType = $this->input->post("helpRqType");
        $helpDelIds = $this->input->post("helpDelIds");

        $text = trim($this->input->post('helpRqSerch') ?? '');

        if($helpRqType == 1){
            $helpRqTypes= 'GOV';
        }
        elseif($helpRqType == 2){
            $helpRqTypes= 'PVT';
        }
        elseif($helpRqType == 3){
            $helpRqTypes= 'CONS';
        }
        else{
            $helpRqTypes ='';
        }

        $filter_req = array("AND (srp_erp_ngo_com_helprequirements.helpRequireType='" . $helpRqTypes . "')" => $helpRqTypes ,"AND (srp_erp_ngo_com_helprequirements.helpRequireID='" . $helpDelIds . "')" => $helpDelIds);
        $set_filter_req = array_filter($filter_req);
        $where_clauseq = join(" ", array_keys($set_filter_req));

        $srch_string = '';
        if (isset($text) && !empty($text)) {

            $srch_string = " AND ((helpRequireType Like '%" . $text . "%') OR (helpRequireDesc Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";


        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_string;

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_ngo_com_memberhelprequirements.Com_MasterID  FROM srp_erp_ngo_com_memberhelprequirements WHERE srp_erp_ngo_com_memberhelprequirements.companyID={$companyID} ");
        $rowFP = $queryFP->result();
        $memInr = array();
        foreach ($rowFP as $resFP) {

            $memInr[] = $resFP->Com_MasterID;

        }

        $in_memPrt = "'" . implode("', '", $memInr) . "'";

        $this->form_validation->set_rules('helpRqType', 'Requirements Type', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            $data['helpReqReport'] = $this->db->query("SELECT srp_erp_ngo_com_helprequirements.helpRequireType,srp_erp_ngo_com_memberhelprequirements.companyID,srp_erp_ngo_com_memberhelprequirements.createdUserID,srp_erp_ngo_com_memberhelprequirements.helpRequireID,srp_erp_ngo_com_memberhelprequirements.hlprDescription,CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_helprequirements.helpRequireDesc FROM srp_erp_ngo_com_memberhelprequirements INNER JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memberhelprequirements.Com_MasterID INNER JOIN srp_erp_ngo_com_helprequirements ON srp_erp_ngo_com_helprequirements.helpRequireID=srp_erp_ngo_com_memberhelprequirements.helpRequireID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE srp_erp_ngo_com_memberhelprequirements.companyID = {$companyID} AND $where " . $where_clauseq . " ORDER BY srp_erp_ngo_com_memberhelprequirements.Com_MasterID DESC")->result_array();

            $data["type"] = "html";
            echo $html = $this->load->view('system/communityNgo/ajax/load_comMem_helpRequirements_report', $data, true);
        }
    }

    function get_communityMem_helpRq_report_pdf()
    {
        $helpRqType = $this->input->post("helpRqType");
        $helpDelIds = $this->input->post("helpDelIds");

        $text = trim($this->input->post('helpRqSerch') ?? '');

        if($helpRqType == 1){
            $helpRqTypes= 'GOV';
        }
        elseif($helpRqType == 2){
            $helpRqTypes= 'PVT';
        }
        elseif($helpRqType == 3){
            $helpRqTypes= 'CONS';
        }
        else{
            $helpRqTypes ='';
        }

        $filter_req = array("AND (srp_erp_ngo_com_helprequirements.helpRequireType='" . $helpRqTypes . "')" => $helpRqTypes ,"AND (srp_erp_ngo_com_helprequirements.helpRequireID='" . $helpDelIds . "')" => $helpDelIds);
        $set_filter_req = array_filter($filter_req);
        $where_clauseq = join(" ", array_keys($set_filter_req));

        $srch_string = '';
        if (isset($text) && !empty($text)) {

            $srch_string = " AND ((helpRequireType Like '%" . $text . "%') OR (helpRequireDesc Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";


        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_string;

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_ngo_com_memberhelprequirements.Com_MasterID  FROM srp_erp_ngo_com_memberhelprequirements WHERE srp_erp_ngo_com_memberhelprequirements.companyID={$companyID} ");
        $rowFP = $queryFP->result();
        $memInr = array();
        foreach ($rowFP as $resFP) {

            $memInr[] = $resFP->Com_MasterID;

        }

        $in_memPrt = "'" . implode("', '", $memInr) . "'";

        $this->form_validation->set_rules('helpRqType', 'Requirements Type', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {

            $data['helpReqReport'] = $this->db->query("SELECT srp_erp_ngo_com_helprequirements.helpRequireType,srp_erp_ngo_com_memberhelprequirements.companyID,srp_erp_ngo_com_memberhelprequirements.createdUserID,srp_erp_ngo_com_memberhelprequirements.helpRequireID,srp_erp_ngo_com_memberhelprequirements.hlprDescription,CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_helprequirements.helpRequireDesc FROM srp_erp_ngo_com_memberhelprequirements INNER JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memberhelprequirements.Com_MasterID INNER JOIN srp_erp_ngo_com_helprequirements ON srp_erp_ngo_com_helprequirements.helpRequireID=srp_erp_ngo_com_memberhelprequirements.helpRequireID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE srp_erp_ngo_com_memberhelprequirements.companyID = {$companyID} AND $where " . $where_clauseq . " ORDER BY srp_erp_ngo_com_memberhelprequirements.Com_MasterID DESC")->result_array();

            $data["type"] = "pdf";
            $html = $this->load->view('system/communityNgo/ajax/load_comMem_helpRequirements_report', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    /*community willing to help report details*/

    function get_comMem_willingToHelp_report()
    {

        $helpCategoryID = $this->input->post("helpCategoryID");

        if($helpCategoryID == -2){
            $helpCategoryIDs= '';
        }
        else{
            $helpCategoryIDs = $helpCategoryID;
        }

        $text = trim($this->input->post('helpWillSerch') ?? '');


        $filter_req = array("AND (srp_erp_ngo_com_helpcategories.helpCategoryID='" . $helpCategoryIDs . "')" => $helpCategoryIDs);
        $set_filter_req = array_filter($filter_req);
        $where_clauseq = join(" ", array_keys($set_filter_req));

        $srch_string = '';
        if (isset($text) && !empty($text)) {

            $srch_string = " AND ((srp_erp_ngo_com_memberwillingtohelp.helpCategoryID Like '%" . $text . "%') OR (helpCategoryDes Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";


        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_string;

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_ngo_com_memberwillingtohelp.Com_MasterID  FROM srp_erp_ngo_com_memberwillingtohelp WHERE srp_erp_ngo_com_memberwillingtohelp.companyID={$companyID} ");
        $rowFP = $queryFP->result();
        $memInr = array();
        foreach ($rowFP as $resFP) {

            $memInr[] = $resFP->Com_MasterID;

        }

        $in_memPrt = "'" . implode("', '", $memInr) . "'";

        $this->form_validation->set_rules('helpCategoryID', 'Help Category', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            $data['willingHelpRprt'] = $this->db->query("SELECT srp_erp_ngo_com_memberwillingtohelp.helpCategoryID,srp_erp_ngo_com_memberwillingtohelp.companyID,srp_erp_ngo_com_memberwillingtohelp.createdUserID,srp_erp_ngo_com_helpcategories.helpCategoryDes,srp_erp_ngo_com_memberwillingtohelp.helpComments,CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_helpcategories.helpCategoryDes FROM srp_erp_ngo_com_memberwillingtohelp INNER JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memberwillingtohelp.Com_MasterID INNER JOIN srp_erp_ngo_com_helpcategories ON srp_erp_ngo_com_helpcategories.helpCategoryID=srp_erp_ngo_com_memberwillingtohelp.helpCategoryID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE srp_erp_ngo_com_memberwillingtohelp.companyID = {$companyID} AND $where " . $where_clauseq . " ORDER BY srp_erp_ngo_com_memberwillingtohelp.Com_MasterID DESC")->result_array();

            $data["type"] = "html";
            echo $html = $this->load->view('system/communityNgo/ajax/load_comMem_willingToHelp_report', $data, true);
        }
    }

    function get_comMem_willingToHelp_report_pdf()
    {

        $helpCategoryID = $this->input->post("helpCategoryID");

        if($helpCategoryID == -2){
            $helpCategoryIDs= '';
        }
        else{
            $helpCategoryIDs = $helpCategoryID;
        }

        $text = trim($this->input->post('helpWillSerch') ?? '');


        $filter_req = array("AND (srp_erp_ngo_com_helpcategories.helpCategoryID='" . $helpCategoryIDs . "')" => $helpCategoryIDs);
        $set_filter_req = array_filter($filter_req);
        $where_clauseq = join(" ", array_keys($set_filter_req));

        $srch_string = '';
        if (isset($text) && !empty($text)) {

            $srch_string = " AND ((helpCategoryID Like '%" . $text . "%') OR (helpCategoryDes Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";


        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_communitymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_communitymaster.companyID = " . $companyID . $deleted . $srch_string;

        $queryFP = $this->db->query("SELECT DISTINCT srp_erp_ngo_com_memberwillingtohelp.Com_MasterID  FROM srp_erp_ngo_com_memberwillingtohelp WHERE srp_erp_ngo_com_memberwillingtohelp.companyID={$companyID} ");
        $rowFP = $queryFP->result();
        $memInr = array();
        foreach ($rowFP as $resFP) {

            $memInr[] = $resFP->Com_MasterID;

        }

        $in_memPrt = "'" . implode("', '", $memInr) . "'";

        $this->form_validation->set_rules('helpCategoryID', 'Help Category', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            $data['willingHelpRprt'] = $this->db->query("SELECT srp_erp_ngo_com_helpcategories.helpCategoryID,srp_erp_ngo_com_memberwillingtohelp.companyID,srp_erp_ngo_com_memberwillingtohelp.createdUserID,srp_erp_ngo_com_helpcategories.helpCategoryDes,srp_erp_ngo_com_memberwillingtohelp.helpComments,CName_with_initials,TP_home,CNIC_No,TP_Mobile,EmailID,C_Address,P_Address,HouseNo,GS_Division,GS_No,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,srp_erp_ngo_com_helpcategories.helpCategoryDes FROM srp_erp_ngo_com_memberwillingtohelp INNER JOIN srp_erp_ngo_com_communitymaster on srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_memberwillingtohelp.Com_MasterID INNER JOIN srp_erp_ngo_com_helpcategories ON srp_erp_ngo_com_helpcategories.helpCategoryID=srp_erp_ngo_com_memberwillingtohelp.helpCategoryID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE srp_erp_ngo_com_memberwillingtohelp.companyID = {$companyID} AND $where " . $where_clauseq . " ORDER BY srp_erp_ngo_com_memberwillingtohelp.Com_MasterID DESC")->result_array();

            $data["type"] = "pdf";
            $html = $this->load->view('system/communityNgo/ajax/load_comMem_willingToHelp_report', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        }
    }

    /* end of community willing to help and help req .. report details*/

    function get_comMem_bothHelp_report()
    {

        $bothHelpRqID = $this->input->post("bothHelpRqID");

        if($bothHelpRqID == -1){
            $bothHelpRqIDs= '';
        }
        else{
            $bothHelpRqIDs = $bothHelpRqID;
        }

        $text = trim($this->input->post('bothHelpSerch') ?? '');


        $filter_req = array("AND (comHlpingMas.Com_MasterID='" . $bothHelpRqIDs . "')" => $bothHelpRqIDs);
        $set_filter_req = array_filter($filter_req);
        $where_clauseq = join(" ", array_keys($set_filter_req));

        $srch_string = '';
        if (isset($text) && !empty($text)) {

            $srch_string = " AND ((comHlpingMas.Com_MasterID Like '%" . $text . "%') OR (helpCategoryDes Like '%" . $text . "%') OR (hlprDescription Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";


        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND comHlpingMas.isDeleted = '0' ";

        $where = "comHlpingMas.companyID = " . $companyID . $deleted . $srch_string;


        $this->form_validation->set_rules('bothHelpRqID', 'Member', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            $data['bothHelpMem'] = $this->db->query("SELECT DISTINCT comHlpingMas.Com_MasterID,CName_with_initials,CNIC_No,C_Address,EmailID,P_Address,HouseNo,GS_Division,GS_No,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber FROM srp_erp_ngo_com_communitymaster comHlpingMas INNER JOIN srp_erp_ngo_com_memberhelprequirements helpRq ON helpRq.Com_MasterID=comHlpingMas.Com_MasterID INNER JOIN srp_erp_ngo_com_memberwillingtohelp helpWilling ON helpWilling.Com_MasterID=comHlpingMas.Com_MasterID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = comHlpingMas.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = comHlpingMas.RegionID WHERE $where " . $where_clauseq . "")->result_array();

            $data["type"] = "html";
            echo $html = $this->load->view('system/communityNgo/ajax/load_comMem_helpWillingAndReq_report', $data, true);
        }
    }

    function get_comMem_bothHelp_report_pdf()
    {

        $bothHelpRqID = $this->input->post("bothHelpRqID");

        if($bothHelpRqID == -1){
            $bothHelpRqIDs= '';
        }
        else{
            $bothHelpRqIDs = $bothHelpRqID;
        }

        $text = trim($this->input->post('bothHelpSerch') ?? '');


        $filter_req = array("AND (comHlpingMas.Com_MasterID='" . $bothHelpRqIDs . "')" => $bothHelpRqIDs);
        $set_filter_req = array_filter($filter_req);
        $where_clauseq = join(" ", array_keys($set_filter_req));

        $srch_string = '';
        if (isset($text) && !empty($text)) {

            $srch_string = " AND ((comHlpingMas.Com_MasterID Like '%" . $text . "%') OR (helpCategoryDes Like '%" . $text . "%') OR (hlprDescription Like '%" . $text . "%') OR (CName_with_initials Like '%" . $text . "%') OR (CNIC_No Like '%" . $text . "%') OR (name Like '%" . $text . "%') OR (EmailID Like '%" . $text . "%') OR (CONCAT(TP_home,TP_Mobile) Like '%" . $text . "%'))";


        }

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND comHlpingMas.isDeleted = '0' ";

        $where = "comHlpingMas.companyID = " . $companyID . $deleted . $srch_string;


        $this->form_validation->set_rules('bothHelpRqID', 'Member', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';
        } else {


            $data['bothHelpMem'] = $this->db->query("SELECT DISTINCT comHlpingMas.Com_MasterID,CName_with_initials,CNIC_No,C_Address,EmailID,P_Address,HouseNo,GS_Division,GS_No,srp_erp_gender.genderID,srp_erp_gender.name,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber FROM srp_erp_ngo_com_communitymaster comHlpingMas INNER JOIN srp_erp_ngo_com_memberhelprequirements helpRq ON helpRq.Com_MasterID=comHlpingMas.Com_MasterID INNER JOIN srp_erp_ngo_com_memberwillingtohelp helpWilling ON helpWilling.Com_MasterID=comHlpingMas.Com_MasterID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = comHlpingMas.GenderID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = comHlpingMas.RegionID WHERE $where " . $where_clauseq . "")->result_array();

            $data["type"] = "pdf";
            $html = $this->load->view('system/communityNgo/ajax/load_comMem_helpWillingAndReq_report', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');

        }
    }

    //end of helping del

    /*end of community report dropdown*/
    /*OP community */

    public function searchCommunityMem()
    {

        $convertFormat = convert_date_format_sql();

        $Com_MasterID = $_POST['Com_MasterID'];

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $querycOM1 = $CI->db->query("SELECT srp_erp_ngo_com_communitymaster.Com_MasterID,srp_erp_statemaster.stateID,C_Address,srp_erp_statemaster.countyID FROM srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_communitymaster.RegionID = srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_communitymaster.companyID='{$companyID}' AND srp_erp_ngo_com_communitymaster.Com_MasterID = '$Com_MasterID' ");
        $rescoM1 = $querycOM1->row();

        $querycOM = $CI->db->query("SELECT Com_MasterID, IFNULL(CFullName, '') fullName,CName_with_initials,TitleID,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,EmailID,CountryCodePrimary,AreaCodePrimary,TP_Mobile,CountryCodeSecondary,AreaCodeSecondary,TP_home,(srp_erp_statemaster.stateID) AS stateIDs,C_Address,srp_erp_statemaster.countyID,CNIC_No FROM srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_communitymaster.RegionID = srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_communitymaster.companyID='{$companyID}' AND srp_erp_ngo_com_communitymaster.Com_MasterID = '$Com_MasterID' ");
        $rescoM = $querycOM->result();

        $queryMas = $this->db->query("SELECT stateID,masterID FROM srp_erp_statemaster WHERE stateID ={$rescoM1->stateID} ");
        $rowMas = $queryMas->row();

        $queryMas2 = $this->db->query("SELECT stateID,masterID FROM srp_erp_statemaster WHERE stateID ={$rowMas->masterID} ");
        $rowMas2 = $queryMas2->row();

        $queryMas3 = $this->db->query("SELECT stateID,masterID FROM srp_erp_statemaster WHERE stateID ={$rowMas2->masterID} ");
        $rowMas3 = $queryMas3->row();

        $queryfamd = $this->db->query("SELECT srp_erp_ngo_com_familymaster.FamMasterID,srp_erp_ngo_com_familymaster.FamilyName,ComEconSteID FROM srp_erp_ngo_com_familydetails LEFT JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_familydetails.FamMasterID=srp_erp_ngo_com_familymaster.FamMasterID WHERE Com_MasterID ={$rescoM1->Com_MasterID} ");
        $rowfamd = $queryfamd->row();
        if (!empty($rowfamd)) {
            $FamMasterID = $rowfamd->FamMasterID;
            $FamilyName = $rowfamd->FamilyName;
        } else {
            $FamMasterID = '';
            $FamilyName = '';
        }

        $queryMas4 = $this->db->query("SELECT stateID FROM srp_erp_statemaster WHERE stateID ={$rowMas3->masterID} ");
        $rowMas4 = $queryMas4->row();

        foreach ($rescoM as $famCom) {
            echo json_encode(
                array(

                    'fullName' => $famCom->fullName,
                    'CName_with_initials' => $famCom->CName_with_initials,
                    'TitleID' => $famCom->TitleID,
                    'CDOB' => $famCom->CDOB,
                    'FamMasterID' => $FamMasterID,
                    'familyDetail' => $FamilyName,
                    'EmailID' => $famCom->EmailID,
                    'CountryCodePrimary' => $famCom->CountryCodePrimary,
                    'AreaCodePrimary' => $famCom->AreaCodePrimary,
                    'TP_home' => $famCom->TP_home,
                    'nationalIdentityCardNo' => $famCom->CNIC_No,
                    'CountryCodeSecondary' => $famCom->CountryCodeSecondary,
                    'AreaCodeSecondary' => $famCom->AreaCodeSecondary,
                    'TP_Mobile' => $famCom->TP_Mobile,
                    'C_Address' => $famCom->C_Address,
                    'countyID' => $famCom->countyID,
                    'province' => $rowMas4->stateID,
                    'district' => $rowMas3->stateID,
                    'division' => $rowMas2->stateID,
                    'subDivision' => $rowMas->stateID,

                )

            );
        }

    }


    function load_comBeneficiaryManage_view()
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
        $beneMemType = trim($this->input->post('beneMemType') ?? '');

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

        if ($beneMemType == '1') {
            $data['header'] = $this->db->query("SELECT benificiaryID,bfm.Com_MasterID,fullName,benificiaryImage,email,CountryDes,bfm.countryID,bfm.province,bfm.district,bfm.division,bfm.subDivision,bfm.projectID,CONCAT(phoneCountryCodePrimary,' - ',phoneAreaCodePrimary,phonePrimary) AS MasterPrimaryNumber,bfm.confirmedYN as confirmedYN,systemCode,projectID,fm.FamMasterID,fm.ComEconSteID FROM srp_erp_ngo_beneficiarymaster bfm LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bfm.countryID LEFT JOIN srp_erp_ngo_com_familymaster fm ON fm.FamMasterID = bfm.FamMasterID WHERE $where AND (bfm.Com_MasterID !='' OR bfm.Com_MasterID !=NULL) ORDER BY benificiaryID DESC ")->result_array();

        } else if ($beneMemType == '2') {
            $data['header'] = $this->db->query("SELECT benificiaryID,bfm.Com_MasterID,fullName,benificiaryImage,email,CountryDes,bfm.countryID,bfm.province,bfm.district,bfm.division,bfm.subDivision,bfm.projectID,CONCAT(phoneCountryCodePrimary,' - ',phoneAreaCodePrimary,phonePrimary) AS MasterPrimaryNumber,bfm.confirmedYN as confirmedYN,systemCode,projectID FROM srp_erp_ngo_beneficiarymaster bfm LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bfm.countryID WHERE $where AND bfm.Com_MasterID IS NULL ORDER BY benificiaryID DESC ")->result_array();

        } else {
            $data['header'] = $this->db->query("SELECT benificiaryID,bfm.Com_MasterID,fullName,benificiaryImage,email,CountryDes,bfm.countryID,bfm.province,bfm.district,bfm.division,bfm.subDivision,bfm.projectID,CONCAT(phoneCountryCodePrimary,' - ',phoneAreaCodePrimary,phonePrimary) AS MasterPrimaryNumber,bfm.confirmedYN as confirmedYN,systemCode,projectID FROM srp_erp_ngo_beneficiarymaster bfm LEFT JOIN srp_erp_countrymaster cm ON cm.countryID = bfm.countryID WHERE $where ORDER BY benificiaryID DESC ")->result_array();

        }

        $this->load->view('system/communityNgo/ajax/load_comBenificiary_master', $data);

    }

    public function searchCommunityBeniFem()
    {

        $convertFormat = convert_date_format_sql();

        $Com_MasterID = $_POST['Com_MasterID'];

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $querycOM1 = $CI->db->query("SELECT srp_erp_ngo_com_communitymaster.Com_MasterID,C_Address,srp_erp_statemaster.countyID FROM srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_com_communitymaster.RegionID WHERE srp_erp_ngo_com_communitymaster.companyID='{$companyID}' AND srp_erp_ngo_com_communitymaster.Com_MasterID = '$Com_MasterID' ");
        $rescoM1 = $querycOM1->row();

        $querycOM = $CI->db->query("SELECT srp_erp_ngo_com_communitymaster.Com_MasterID, IFNULL(CFullName, '') fullName,CName_with_initials,TitleID,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,GenderID,EmailID,CountryCodePrimary,AreaCodePrimary,TP_Mobile,CountryCodeSecondary,AreaCodeSecondary,TP_home,C_Address,FamMasterID,srp_erp_statemaster.countyID,CNIC_No,relationshipID FROM srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_ngo_com_familydetails ON srp_erp_ngo_com_familydetails.Com_MasterID=srp_erp_ngo_com_communitymaster.Com_MasterID INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_communitymaster.RegionID= srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_communitymaster.companyID='{$companyID}' AND srp_erp_ngo_com_communitymaster.Com_MasterID = '$Com_MasterID' ");
        $rescoM = $querycOM->result();


        foreach ($rescoM as $famCom) {
            echo json_encode(
                array(

                    'name' => $famCom->fullName,
                    'CDOB' => $famCom->CDOB,
                    'FamMasterID' => $famCom->FamMasterID,
                    'relationshipType' => $famCom->relationshipID,
                    'gender' => $famCom->GenderID,
                    'idNO' => $famCom->CNIC_No,

                )

            );
        }

    }

    function load_comBeneficiary_print_view()
    {
        $benificiaryID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('benificiaryID') ?? '');
        $data['extra'] = $this->CommunityNgo_model->load_comBeneficiary_header_helpNest($benificiaryID);
        $data['benImages'] = $this->CommunityNgo_model->fetch_comBeneficiary_multipleImages($benificiaryID);
        $data['html'] = $this->input->post('html');
        $data['approval'] = $this->input->post('approval');

        $html = $this->load->view('system/communityNgo/ngo_mo_comBeneficiaryNe_print', $data, true);

        if ($this->input->post('html')) {
            echo $html;

        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 1);
        }
    }

    function load_comBeneficiaryManage_editView()
    {
        $convertFormat = convert_date_format_sql();
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');

        $data['header'] = $this->db->query("SELECT *,CountryDes,DATE_FORMAT(bfm.createdDateTime,'" . $convertFormat . "') AS createdDate,DATE_FORMAT(bfm.modifiedDateTime,'" . $convertFormat . "') AS modifydate,DATE_FORMAT(bfm.registeredDate,'" . $convertFormat . "') AS registeredDate,DATE_FORMAT(bfm.dateOfBirth,'" . $convertFormat . "') AS dateOfBirth,bfm.createdUserName as contactCreadtedUser,bfm.email as contactEmail,bfm.phonePrimary as contactPhonePrimary,bfm.phoneSecondary as contactPhoneSecondary,project.projectName as projectName,benType.description as benTypeDescription,smpro.Description as provinceName,smdis.Description as districtName,smdiv.Description as divisionName,smsubdiv.Description as subDivisionName,bfm.projectID FROM srp_erp_ngo_beneficiarymaster bfm LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = bfm.countryID LEFT JOIN srp_erp_ngo_projects project ON project.ngoProjectID = bfm.projectID LEFT JOIN srp_erp_ngo_benificiarytypes benType ON benType.beneficiaryTypeID = bfm.benificiaryType LEFT JOIN srp_erp_statemaster smpro ON smpro.stateID = bfm.province LEFT JOIN srp_erp_statemaster smdis ON smdis.stateID = bfm.district LEFT JOIN srp_erp_statemaster smdiv ON smdiv.stateID = bfm.division LEFT JOIN srp_erp_statemaster smsubdiv ON smsubdiv.stateID = bfm.subDivision WHERE benificiaryID = " . $benificiaryID . "")->row_array();

        $data['projects'] = $this->db->query("SELECT projects.projectName FROM srp_erp_ngo_beneficiaryprojects bp LEFT JOIN srp_erp_ngo_projects projects ON projects.ngoProjectID = bp.projectID WHERE bp.beneficiaryID = {$benificiaryID}")->result_array();

        $this->load->view('system/communityNgo/ajax/load_comBeneficiary_Edit_view', $data);
    }

    function load_comBeneficiaryTemplate_view()
    {
        $ngoProjectID = trim($this->input->post('ngoProjectID') ?? '');
        $template = $this->db->query("SELECT templateID FROM srp_erp_ngo_projects WHERE ngoProjectID = {$ngoProjectID}")->row_array();
        if ($template) {
            $this->load->view("system/communityNgo/template/" . $template['templateID'] . "");
        }

    }

    function save_comBeneficiary()
    {
        $templateType = trim($this->input->post('templateType') ?? '');
        $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        //$this->form_validation->set_rules("subProjectID", 'Sub Project', 'trim|required');
        $this->form_validation->set_rules("Com_MasterID", 'Community Member', 'trim|required');
        // $this->form_validation->set_rules("EconStateID", 'Economic Status', 'trim|required');
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

        if ($templateType == 'helpAndNest') {
            $this->form_validation->set_rules("nationalIdentityCardNo", 'NIC No', 'trim|required');
            $this->form_validation->set_rules("familyDetail", 'Family Details', 'trim|required');
            $this->form_validation->set_rules("ownLandAvailable", 'Own Land Available', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_comBeneficiary());
        }
    }

    function save_comBeneficiary_familyDel()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('Com_MasterIDs', 'Community Member', 'trim|required');
        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('nationality', 'Nationality', 'required|numeric');
        $this->form_validation->set_rules('relationshipType', 'Relationship', 'required|numeric');
        $this->form_validation->set_rules('DOB', 'Date of Birth', 'trim|required');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $error_message = validation_errors();
            echo json_encode(array('error' => 1, 'message' => $error_message));
        } else {
            echo $this->CommunityNgo_model->save_comBeneficiary_familyDel();
        }
    }

    function fetch_comBeneficiary_familyDel()
    {
        $beneficiaryID = $this->input->post('beneficiaryID');
        $data['benificiaryArray'] = $this->CommunityNgo_model->fetch_comBeneficiary_familyDel($beneficiaryID);
        $data['type'] = 'edit';
        $this->load->view('system/communityNgo/ajax/load_comBeneficiary_family_detail', $data);
    }

    function fetch_comBeneficiary_familyDel_view()
    {
        $beneficiaryID = $this->input->post('beneficiaryID');
        $data['benificiaryArray'] = $this->CommunityNgo_model->fetch_comBeneficiary_familyDel($beneficiaryID);
        $data['type'] = 'view';
        $this->load->view('system/communityNgo/ajax/load_comBeneficiary_family_detail', $data);
    }

    function delete_comBeneficiary_familyDel()
    {
        echo json_encode($this->CommunityNgo_model->delete_comBeneficiary_familyDel());
    }

    function load_comBeneficiary_documents_view()
    {
        $projectID = trim($this->input->post('projectID') ?? '');
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        $docDet = $this->CommunityNgo_model->load_beneficiary_documents();
        //echo '<pre>';print_r($docDet); echo '</pre>'; die();

        $data['docDet'] = $docDet;
        $data['projectID'] = $projectID;
        $data['benificiaryID'] = $benificiaryID;
        $this->load->view('system/communityNgo/ajax/load_comBeneficiary_document_view', $data);
    }

    function comBeneficiary_confirmed()
    {
        echo json_encode($this->CommunityNgo_model->comBeneficiary_confirmed());
    }

    function load_comBeneficiary_header()
    {
        echo json_encode($this->CommunityNgo_model->load_comBeneficiary_header());
    }

    function fetch_ngoSub_projectsForCom()
    {
        echo json_encode($this->CommunityNgo_model->fetch_ngoSub_projectsForCom());
    }

    function comBeneficiary_familyImg_upload()
    {
        $output_dir = "uploads/NGO/beneficiaryFamilyImage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/NGO", 007);
            mkdir("uploads/NGO/beneficiaryFamilyImage", 007);
        }
        $fileName = $this->input->post('empfamilydetailsID') . '_FD_' . time();
        $config['upload_path'] = realpath(APPPATH . '../uploads/NGO/beneficiaryFamilyImage');
        $config['allowed_types'] = 'gif|jpg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|txt|rtf|msg';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload("document_file")) {
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
        }
    }

    function comBeneficiary_systemCode_generator()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        echo json_encode($this->CommunityNgo_model->comBeneficiary_systemCode_generator($benificiaryID));
    }

    function fetch_comBeneficiary_search()
    {
        echo json_encode($this->CommunityNgo_model->fetch_comBeneficiary_search());
    }

    function new_comBeneficiary_province()
    {
        $this->form_validation->set_rules('province_shortCode', 'Short Code', 'trim|required');
        $this->form_validation->set_rules('province_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('hd_province_countryID', 'Country', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->new_comBeneficiary_province());
        }
    }

    function new_comBeneficiary_district()
    {
        $this->form_validation->set_rules('district_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('district_shortCode', 'Short Code', 'trim|required');
        $this->form_validation->set_rules('hd_district_countryID', 'Country', 'trim|required');
        $this->form_validation->set_rules('hd_district_provinceID', 'Province', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->new_comBeneficiary_district());
        }
    }

    function new_comBeneficiary_division()
    {
        $this->form_validation->set_rules('division_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('hd_division_countryID', 'Country', 'trim|required');
        $this->form_validation->set_rules('division_shortCode', 'Short Code', 'trim|required');
        $this->form_validation->set_rules('hd_division_districtID', 'District', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->new_comBeneficiary_division());
        }
    }

    function new_comBeneficiary_type()
    {
        $this->form_validation->set_rules('type', 'Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->new_comBeneficiary_type());
        }
    }

    function fetch_comBeneficiary_province()
    {
        echo json_encode($this->CommunityNgo_model->beneficiary_province());
    }

    function fetch_comBeneficiary_province_area()
    {
        echo json_encode($this->CommunityNgo_model->beneficiary_area());
    }

    function fetch_comBeneficiary_division()
    {
        echo json_encode($this->CommunityNgo_model->beneficiary_division());
    }

    function fetch_comBeneficiary_sub_division()
    {
        echo json_encode($this->CommunityNgo_model->beneficiary_sub_division());
    }

    function delete_comBeneficiary_master()
    {
        echo json_encode($this->CommunityNgo_model->delete_comBeneficiary_master());
    }

    function load_comBeneficiary_allNotes()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');

        $where = "companyID = " . $companyID . " AND documentID = 5 AND documentAutoID = " . $benificiaryID . "";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_notes');
        $this->db->where($where);
        $this->db->order_by('notesID', 'desc');
        $data['notes'] = $this->db->get()->result_array();
        $this->load->view('system/communityNgo/ajax/load_comBeneficiary_notes', $data);
    }

    function add_comBeneficiary_notes()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->add_comBeneficiary_notes());
        }
    }

    function comBeneficiary_imgUpload()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->comBeneficiary_imgUpload());
        }
    }

    function comBeneficiary_imgUpload_helpNest()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->comBeneficiary_imgUpload_helpNest());
        }
    }

    function comBeneficiary_imgUpload_helpNest_two()
    {
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->comBeneficiary_imgUpload_helpNest_two());
        }
    }


    function comMemBen_attachment_upload()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        $this->form_validation->set_rules('documentAutoID', 'Document Auto ID', 'trim|required');

        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            $documentAutoID = $this->input->post('documentAutoID');
          /*
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_ngo_attachments')->result_array();
            $file_name = $this->input->post('document_name') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments/NGO');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);*/

            $info = new SplFileInfo($_FILES["document_file"]["name"]);
            $fileName = $this->input->post('document_name') . $this->common_data['company_data']['company_code'].'_'. $documentAutoID . '_' . time() . '.' . $info->getExtension();
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
            }

            $this->db->trans_start();
           // $upload_data = $this->upload->data();
            //$fileName                       = $file_name.'_'.$upload_data["file_ext"];
            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
            $data['myFileName'] = $fileName;
           /* $data['myFileName'] = $file_name . $upload_data["file_ext"];*/
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
                echo json_encode(array('status' => 0, 'type' => 'e',
                    'message' => 'Upload failed ' . $this->db->_error_message()));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status' => 1, 'type' => 's',
                    'message' => 'Successfully ' . $fileName . ' uploaded.'));
            }
        }

    }

    function delete_comMemBen_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments/NGO");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {
            $this->db->delete('srp_erp_ngo_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(TRUE);
        }
    }

    function load_comMemBen_all_attachment()
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
        $this->load->view('system/communityNgo/ajax/load_com_ngo_donors_attachments', $data);
    }

    function delete_comBen_masterNote_allDocument()
    {
        echo json_encode($this->CommunityNgo_model->delete_comBen_masterNote_allDocument());
    }

    function update_comBeneficiary_familyDel()
    {
        $result = $this->CommunityNgo_model->comEditable_update('srp_erp_ngo_beneficiaryfamilydetails', 'empfamilydetailsID');
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'updated'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'updated Fail'));
        }
    }

    function load_comBeneficiary_documents_view_forEdit()
    {
        $beneficiaryID = trim($this->input->post('benificiaryID') ?? '');
        $docDet = $this->CommunityNgo_model->load_beneficiary_documents();
        $data['docDet'] = $docDet;
        $this->load->view('system/communityNgo/ajax/load_comBeneficiary_docView_forEdit', $data);
    }

    function load_comBeneficiary_multipleImage_view()
    {
        $benificiaryID = trim($this->input->post('benificiaryID') ?? '');
        $docDet = $this->CommunityNgo_model->load_comBeneficiary_multipleImages();
        //echo '<pre>';print_r($docDet); echo '</pre>'; die();
        $data['docDet'] = $docDet;
        $data['benificiaryID'] = $benificiaryID;
        $this->load->view('system/communityNgo/ajax/load_comBeneficiary_multipleImage_view', $data);
    }

    function upload_comBeneficiary_multipleImage()
    {
        //$this->form_validation->set_rules('document', 'Document', 'trim|required');
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->upload_comBeneficiary_multipleImage());
        }
    }

    function delete_comBeneficiary_multipleImage()
    {
        $this->form_validation->set_rules('beneficiaryImageID', 'beneficiary Image ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->delete_comBeneficiary_multipleImage());
        }
    }

    function update_comBeneficiary_multipleImage()
    {
        $this->form_validation->set_rules('beneficiaryImageID', 'Beneficiary Image ID', 'trim|required');
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->update_comBeneficiary_multipleImage());
        }
    }

    function save_comBeneficiary_doc()
    {
        $this->form_validation->set_rules('document', 'Document', 'trim|required');
        $this->form_validation->set_rules('benificiaryID', 'Beneficiary ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_comBeneficiary_doc());
        }
    }

    function delete_comBeneficiary_doc()
    {
        $this->form_validation->set_rules('DocDesFormID', 'Document ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->delete_comBeneficiary_doc());
        }
    }

    function get_FamMemCatch()
    {

        $benificiaryIDs = $_POST['benificiaryIDs'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_FamMemCatch($benificiaryIDs);
    }

    /*end of community beneficiary*/

    function getAdded_members()
    {
        $this->form_validation->set_rules("Com_MasterID[]", 'Member', 'trim|required');

        if ($this->form_validation->run() == FALSE) {

            echo '<label style="color: red;"> Family Member is required !</label>';
        } else {

            $companyid = $this->common_data['company_data']['company_id'];

            $Com_MasterID = $this->input->post('Com_MasterID');
            $comRelMemId = array();
            foreach ($Com_MasterID as $keyr => $femMmrId) {
                $comRelMemId [] = $Com_MasterID[$keyr];

            }
            $in_memRelId = "'" . implode("', '", $comRelMemId) . "'";


            $where = "companyID = " . $companyid . " AND Com_MasterID IN ($in_memRelId)";
            // $where = "companyID = " . $companyid . "";
            $this->db->select('Com_MasterID,CName_with_initials,GenderID');
            $this->db->from('srp_erp_ngo_com_communitymaster');
            $this->db->where($where);
            $this->db->order_by('Com_MasterID', 'desc');
            $data['Com_MemID'] = $this->db->get()->result_array();
            $this->load->view('system/communityNgo/ajax/load_com_ngo_FmMemAdding', $data);
        }
    }

    /*community rental Warehouse setup */
    public function defWHControl()
    {

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->defWHControl();

    }

    public function fetch_rentWHSetup()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchRentType') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $convertFormat = convert_date_format_sql();

        $filtRent_string = '';
        if (isset($text) && !empty($text)) {
            $filtRent_string = " AND ((srp_erp_warehousemaster.wareHouseCode Like '%" . $text . "%') OR (srp_erp_warehousemaster.wareHouseDescription Like '%" . $text . "%') OR (srp_erp_warehousemaster.wareHouseLocation Like '%" . $text . "%'))";
        }

        $where = "srp_erp_ngo_com_rentwarehouse.companyID = " . $companyID . $filtRent_string;

        $data['rentalWH'] = $this->db->query("SELECT srp_erp_ngo_com_rentwarehouse.companyID,srp_erp_ngo_com_rentwarehouse.createdUserID,RentWarehouseID,srp_erp_ngo_com_rentwarehouse.wareHouseAutoID,srp_erp_ngo_com_rentwarehouse.isDefault,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseDescription,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.isPosLocation,srp_erp_warehousemaster.warehouseType,warehouseImage,warehouseAddress FROM srp_erp_ngo_com_rentwarehouse INNER JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID=srp_erp_ngo_com_rentwarehouse.wareHouseAutoID  WHERE $where ")->result_array();

        $this->load->view('system/CommunityNgo/ajax/load_com_ngo_rentWarehouseFetch.php', $data);
    }

    public function saveRentalWH_master()
    {
        $this->form_validation->set_rules('rentWareHid[]', 'Rental Warehouse Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->saveRentalWH_master());
        }
    }

    public function fetchEdit_RentWh()
    {
        $cid = $_POST['id'];
        $crid = $cid[0];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->fetchEdit_RentWh($crid);

    }

    public function edit_rentalWH()
    {
        $this->form_validation->set_rules('editrentWareHid', 'Rental Warehouse Type', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'Warehouse Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->edit_rentalWH());
        }
    }

    public function delete_rentalWH()
    {
        $this->form_validation->set_rules('hidden-id', 'Warehouse Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->delete_rentalWH());
        }
    }

    /*end of community rental warehouse setup */

    /*community rental item setup */

    public function fetch_rentItemSetups()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchRentType') ?? '');
        $itemTypeId = trim($this->input->post('itemTypeId') ?? '');
        $convertFormat = convert_date_format_sql();

        $filtRent_string = '';
        if (isset($text) && !empty($text)) {
            $filtRent_string = " AND ((srp_erp_ngo_com_rentalitems.defaultUnitOfMeasure Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.currentStock Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.rentalItemCode Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.rentalItemDes Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.RentalPrice Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.currentStock Like '%" . $text . "%'))";
        }

        if (isset($itemTypeId) && !empty($itemTypeId)) {
            $itemTypIds = " AND srp_erp_ngo_com_rentalitems.rentalItemType = $itemTypeId ";
        } else {
            $itemTypIds = '';
        }
        $deleted = " AND srp_erp_ngo_com_rentalitems.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_rentalitems.companyID = " . $companyID . $filtRent_string . $deleted . $itemTypIds;

        $data['rentalMas'] = $this->db->query("SELECT srp_erp_ngo_com_rentalitems.companyID,srp_erp_ngo_com_rentalitems.createdUserID,rentalItemID,rentalItemType,srp_erp_ngo_com_rentalitems.SortOrder,rentalStatus,srp_erp_warehouseitems.itemAutoID,srp_erp_warehouseitems.itemSystemCode, srp_erp_warehouseitems.itemDescription,srp_erp_ngo_com_rentalitems.defaultUnitOfMeasure,srp_erp_ngo_com_rentalitems.currentStock,srp_erp_ngo_com_rentalitems.reorderPoint,srp_erp_ngo_com_rentalitems.maximunQty,srp_erp_ngo_com_rentalitems.minimumQty,srp_erp_ngo_com_rentalitems.RentalPrice,srp_erp_fa_asset_master.faID,srp_erp_fa_asset_master.faCode,srp_erp_fa_asset_master.assetDescription,srp_erp_fa_asset_master.depMonth,srp_erp_fa_asset_master.companyLocalAmount FROM srp_erp_ngo_com_rentalitems LEFT JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.warehouseItemsAutoID=srp_erp_ngo_com_rentalitems.warehouseItemsAutoID LEFT JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_master.faID=srp_erp_ngo_com_rentalitems.faID WHERE $where ORDER BY srp_erp_ngo_com_rentalitems.SortOrder DESC")->result_array();

        $this->load->view('system/CommunityNgo/ajax/load_com_ngo_rentItemsFetch.php', $data);
    }

    function get_rentTypeDrop()
    {

        $rentTypeID = $_POST['rentTypeID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_rentTypeDrop($rentTypeID);
    }

    function get_rentDetails()
    {

        $rentTypeID = $_POST['rentTypeID'];
        $descriptionID = $_POST['descriptionID'];

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_rentDetails($rentTypeID, $descriptionID);
    }

    function get_rentOtrDetails()
    {

        $rentTypeID = $_POST['rentTypeID'];
        $descriptionID = $_POST['descriptionID'];

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_rentOtrDetails($rentTypeID, $descriptionID);
    }

    public function saveRentalItm_master()
    {
        $this->form_validation->set_rules('rentTypeID[]', 'Rental Type', 'trim|required');
        $this->form_validation->set_rules('descriptionID[]', 'Description', 'trim|required');
        $this->form_validation->set_rules('rentPeriodID[]', 'Period', 'trim|required');
        $this->form_validation->set_rules('defUnitOfMeasureID[]', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('rentalPrice[]', 'Rental Price', 'trim|required');
        $this->form_validation->set_rules('currentStck[]', 'Current Stock', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->saveRentalItm_master());
        }
    }

    public function fetchEdit_Item()
    {

        $crid = $_POST['rentalItemID'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->fetchEdit_Item($crid);

    }

    public function edit_rentalItm()
    {
        $this->form_validation->set_rules('edit_descriptionID', 'Documents Description', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'Rental Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->edit_rentalItm());
        }
    }

    public function delete_rentalItm()
    {
        $this->form_validation->set_rules('hidden-id', 'Rental Master ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->delete_rentalItm());
        }
    }

    //export to excel
    function exportRentSet_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Community Rental List');
        $this->load->database();
        $data = $this->fetch_rentItem_for_excel();

        $header = ['#', 'ITEM TYPE', 'ITEM CODE', 'DESCRIPTION', 'CURRENT STOCK', 'RENTAL PRICE', 'STATUS'];
        $renatalExl = $data['renatalExl'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Community Rental List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:Q4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:Q4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($renatalExl, null, 'A6');
        ob_clean();
        ob_start(); # added
        $filename = 'Rental Details.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        ob_clean();
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function fetch_rentItem_for_excel()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchRentType') ?? '');
        $itemTypeId = trim($this->input->post('itemTypeId') ?? '');

        $filtRent_string = '';
        if (isset($text) && !empty($text)) {
            $filtRent_string = " AND ((srp_erp_ngo_com_rentalitems.defaultUnitOfMeasure Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.currentStock Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.rentalItemCode Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.rentalItemDes Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.RentalPrice Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.currentStock Like '%" . $text . "%'))";
        }

        if (isset($itemTypeId) && !empty($itemTypeId)) {
            $itemTypIds = " AND srp_erp_ngo_com_rentalitems.rentalItemType = $itemTypeId ";
        } else {
            $itemTypIds = '';
        }
        $deleted = " AND srp_erp_ngo_com_rentalitems.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_rentalitems.companyID = " . $companyID . $filtRent_string . $deleted . $itemTypIds;

        $details = $this->db->query("SELECT srp_erp_ngo_com_rentalitems.companyID,srp_erp_ngo_com_rentalitems.createdUserID,rentalItemID,rentalItemType,srp_erp_ngo_com_rentalitems.SortOrder,rentalStatus,srp_erp_warehouseitems.itemAutoID,srp_erp_warehouseitems.itemSystemCode, srp_erp_warehouseitems.itemDescription,srp_erp_ngo_com_rentalitems.defaultUnitOfMeasure,srp_erp_ngo_com_rentalitems.currentStock,srp_erp_ngo_com_rentalitems.reorderPoint,srp_erp_ngo_com_rentalitems.maximunQty,srp_erp_ngo_com_rentalitems.minimumQty,srp_erp_ngo_com_rentalitems.RentalPrice,srp_erp_fa_asset_master.faID,srp_erp_fa_asset_master.faCode,srp_erp_fa_asset_master.assetDescription,srp_erp_fa_asset_master.depMonth,srp_erp_fa_asset_master.companyLocalAmount FROM srp_erp_ngo_com_rentalitems LEFT JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.warehouseItemsAutoID=srp_erp_ngo_com_rentalitems.warehouseItemsAutoID LEFT JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_master.faID=srp_erp_ngo_com_rentalitems.faID WHERE $where ORDER BY srp_erp_ngo_com_rentalitems.SortOrder DESC")->result_array();

        $data = array();

        $a = 1;
        foreach ($details as $row) {


            if ($row['rentalItemType'] == 1) {
                $rentType = 'Products / Goods';
                $rtItemCode = $row['itemSystemCode'];
                $rentItemDes = $row['itemDescription'];

            } else {
                $rentType = 'Fixed Assets';
                $rtItemCode = $row['faCode'];
                $rentItemDes = $row['assetDescription'];
            }

            if ($row['rentalStatus'] == 1) {
                $rentalState = 'Yes';
            } else {
                $rentalState = 'No';
            }

            $data[] = array(
                'Num' => $a,
                'rentType' => $rentType,
                'rtItemCode' => $rtItemCode,
                'rentItemDes' => $rentItemDes,
                'currentStock' => $row['currentStock'] . ' ' . $row['defaultUnitOfMeasure'],
                'RentalPrice' => $row['RentalPrice'],
                'rentalState' => $rentalState,

            );
            $a++;
        }

        return ['renatalExl' => $data];

    }

    /*rent items setup details*/
    function exportRentItems_pdf()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchRentType') ?? '');
        $itemTypeId = trim($this->input->post('itemTypeId') ?? '');

        $filtRent_string = '';
        if (isset($text) && !empty($text)) {
            $filtRent_string = " AND ((srp_erp_ngo_com_rentalitems.defaultUnitOfMeasure Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.currentStock Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.rentalItemCode Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.rentalItemDes Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.RentalPrice Like '%" . $text . "%') OR (srp_erp_ngo_com_rentalitems.currentStock Like '%" . $text . "%'))";
        }

        if (isset($itemTypeId) && !empty($itemTypeId)) {
            $itemTypIds = " AND srp_erp_ngo_com_rentalitems.rentalItemType = $itemTypeId ";
        } else {
            $itemTypIds = '';
        }
        $deleted = " AND srp_erp_ngo_com_rentalitems.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_rentalitems.companyID = " . $companyID . $filtRent_string . $deleted . $itemTypIds;

        $data['rentalMas'] = $this->db->query("SELECT srp_erp_ngo_com_rentalitems.companyID,srp_erp_ngo_com_rentalitems.createdUserID,rentalItemID,rentalItemType,srp_erp_ngo_com_rentalitems.SortOrder,rentalStatus,srp_erp_warehouseitems.itemAutoID,srp_erp_warehouseitems.itemSystemCode, srp_erp_warehouseitems.itemDescription,srp_erp_ngo_com_rentalitems.defaultUnitOfMeasure,srp_erp_ngo_com_rentalitems.currentStock,srp_erp_ngo_com_rentalitems.reorderPoint,srp_erp_ngo_com_rentalitems.maximunQty,srp_erp_ngo_com_rentalitems.minimumQty,srp_erp_ngo_com_rentalitems.RentalPrice,srp_erp_fa_asset_master.faID,srp_erp_fa_asset_master.faCode,srp_erp_fa_asset_master.assetDescription,srp_erp_fa_asset_master.depMonth,srp_erp_fa_asset_master.companyLocalAmount FROM srp_erp_ngo_com_rentalitems LEFT JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.warehouseItemsAutoID=srp_erp_ngo_com_rentalitems.warehouseItemsAutoID LEFT JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_master.faID=srp_erp_ngo_com_rentalitems.faID WHERE $where ORDER BY srp_erp_ngo_com_rentalitems.SortOrder DESC")->result_array();

        $html = $this->load->view('system/communityNgo/ajax/load_com_ngo_rentSetupFetchPdf', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    /*end of  rental item setup */

    /*Start of Committee Positions Master */
    public function fetch_CommitteePosition()
    {
        $this->datatables->select('CommitteePositionID, CommitteePositionDes,
            IFNULL( (SELECT count(CommitteePositionID) FROM srp_erp_ngo_com_committeemembers WHERE CommitteePositionID=t1.CommitteePositionID), 0 ) AS usageCount')
            ->from('srp_erp_ngo_com_committeeposition AS t1')
            ->add_column('edit', '$1', 'alter_CommitteePosition(CommitteePositionID, CommitteePositionDes, usageCount)')
            ->where('companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function saveCommitteePosition()
    {
        $this->form_validation->set_rules('CommitteePosition[]', 'Committee Position', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->saveCommitteePosition());
        }
    }

    public function editCommitteePosition()
    {
        $this->form_validation->set_rules('CommitteePositionDes', 'Committee Position', 'required');
        $this->form_validation->set_rules('hidden-id', 'Committee Position ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->editCommitteePosition());
        }
    }

    public function deleteCommitteePosition()
    {
        $this->form_validation->set_rules('hidden-id', 'Committee Position ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->deleteCommitteePosition());
        }
    }
    /*End of Committee Positions Master */

    /*Start of Committee Master */
    public function fetch_CommitteeMas()
    {
        $this->datatables->select('CommitteeID, CommitteeDes, isActive,
            IFNULL( (SELECT count(CommitteeID) FROM srp_erp_ngo_com_committeeareawise WHERE CommitteeID=t1.CommitteeID), 0 ) AS usageCount')
            ->from('srp_erp_ngo_com_committeesmaster AS t1')
            ->add_column('status', '$1', 'confirm(isActive)')
            ->add_column('edit', '$1', 'alter_CommitteeMas(CommitteeID, CommitteeDes, isActive, usageCount)')
            ->where('companyID', current_companyID());

        echo $this->datatables->generate();
    }

    public function saveCommitteeMas()
    {
        $this->form_validation->set_rules('Committee[]', 'Committee', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->saveCommitteeMas());
        }
    }

    public function editCommitteeMas()
    {
        $this->form_validation->set_rules('CommitteeDes', 'Committee', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('hidden-id', 'Committee ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->editCommitteeMas());
        }
    }

    public function deleteCommitteeMas()
    {
        $this->form_validation->set_rules('hidden-id', 'Committee ID', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->deleteCommitteeMas());
        }
    }
    /*End of Committee Master */
    /*Start of Committee Contents */
    function comitt_members()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $cmnte = $this->input->post('cmnte');
        $data['masterID'] = $this->input->post('masterID');
        $CommitteeAreawiseID = $data['masterID'];
        $data['CommittID'] = $this->input->post('CommittID');
        $CommittID = $data['CommittID'];


        if ($cmnte == '1') {

            $where = "srp_erp_ngo_com_committeeareawise.companyID = " . $companyID;

            $data['committeeMas'] = $this->db->query("SELECT srp_erp_ngo_com_committeeareawise.CommitteeID,CommitteeAreawiseID,SubAreaId,CommitteeHeadID,CommitteeAreawiseDes,DATE_FORMAT(startDate,'.$convertFormat.') AS startDatet,DATE_FORMAT(endDate,'.$convertFormat.') AS endDatet,srp_erp_ngo_com_communitymaster.CName_with_initials,srp_erp_ngo_com_communitymaster.RegionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description as stDescription FROM srp_erp_ngo_com_committeeareawise LEFT JOIN srp_erp_ngo_com_committeesmaster ON srp_erp_ngo_com_committeeareawise.CommitteeID=srp_erp_ngo_com_committeesmaster.CommitteeID LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_committeeareawise.CommitteeHeadID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID=srp_erp_ngo_com_committeeareawise.SubAreaId WHERE $where AND srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID = {$CommitteeAreawiseID} ")->row_array();

            $this->load->view('system/CommunityNgo/ngo_mo_committeeProgress.php', $data);
        } else if ($cmnte == '2') {

            $where = "srp_erp_ngo_com_committeemembers.companyID = " . $companyID;

            $data['committeeMas'] = $this->db->query("SELECT srp_erp_ngo_com_committeemembers.CommitteeMemID,srp_erp_ngo_com_committeemembers.CommitteePositionID,DATE_FORMAT(srp_erp_ngo_com_committeemembers.joinedDate,'.$convertFormat.') AS joinedDatet,DATE_FORMAT(srp_erp_ngo_com_committeemembers.expiryDate,'.$convertFormat.') AS expiryDatet,isMemActive,committeeMemRemark,srp_erp_ngo_com_committeeareawise.CommitteeID,srp_erp_ngo_com_committeemembers.CommitteeAreawiseID,SubAreaId,CommitteeHeadID,CommitteeAreawiseDes,DATE_FORMAT(startDate,'.$convertFormat.') AS startDatet,DATE_FORMAT(endDate,'.$convertFormat.') AS endDatet,srp_erp_ngo_com_communitymaster.CName_with_initials,srp_erp_ngo_com_communitymaster.RegionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description as stDescription FROM srp_erp_ngo_com_committeemembers INNER JOIN srp_erp_ngo_com_committeeareawise ON srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID=srp_erp_ngo_com_committeemembers.CommitteeAreawiseID LEFT JOIN srp_erp_ngo_com_committeesmaster ON srp_erp_ngo_com_committeeareawise.CommitteeID=srp_erp_ngo_com_committeesmaster.CommitteeID LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_committeeareawise.CommitteeHeadID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID=srp_erp_ngo_com_committeeareawise.SubAreaId WHERE $where AND srp_erp_ngo_com_committeemembers.CommitteeMemID = {$CommittID} ")->row_array();

            $this->load->view('system/CommunityNgo/ngo_mo_committeeMemEdit.php', $data);
        } else if ($cmnte == '3') {

            $where = "srp_erp_ngo_com_committeemembers.companyID = " . $companyID;

            $data['committeeMas'] = $this->db->query("SELECT srp_erp_ngo_com_committeemembers.CommitteeMemID,srp_erp_ngo_com_committeemembers.CommitteePositionID,DATE_FORMAT(srp_erp_ngo_com_committeemembers.joinedDate,'.$convertFormat.') AS joinedDatet,DATE_FORMAT(srp_erp_ngo_com_committeemembers.expiryDate,'.$convertFormat.') AS expiryDatet,isMemActive,committeeMemRemark,srp_erp_ngo_com_committeeareawise.CommitteeID,srp_erp_ngo_com_committeemembers.CommitteeAreawiseID,SubAreaId,CommitteeHeadID,CommitteeAreawiseDes,DATE_FORMAT(startDate,'.$convertFormat.') AS startDatet,DATE_FORMAT(endDate,'.$convertFormat.') AS endDatet,srp_erp_ngo_com_communitymaster.CName_with_initials,srp_erp_ngo_com_communitymaster.RegionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description as stDescription FROM srp_erp_ngo_com_committeemembers INNER JOIN srp_erp_ngo_com_committeeareawise ON srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID=srp_erp_ngo_com_committeemembers.CommitteeAreawiseID LEFT JOIN srp_erp_ngo_com_committeesmaster ON srp_erp_ngo_com_committeeareawise.CommitteeID=srp_erp_ngo_com_committeesmaster.CommitteeID LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_committeeareawise.CommitteeHeadID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID=srp_erp_ngo_com_committeeareawise.SubAreaId WHERE $where AND srp_erp_ngo_com_committeemembers.CommitteeMemID = {$CommittID} ")->row_array();

            $this->load->view('system/CommunityNgo/ngo_mo_committeeMemServices.php', $data);
        }

        //   $data['CommitteeID'] = $this->input->post('CommitteeID');

    }

    function fetch_subCommittees_list()
    {
        $CommitteeID = trim($this->input->post('CommitteeID') ?? '');

        echo $this->CommunityNgo_model->fetch_subCommittees_list($CommitteeID);

    }

    function get_comMaserHd()
    {

        $areaSubCmnt = $_POST['areaSubCmnt'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_comMaserHd($areaSubCmnt);
    }

    public function saveCommittee_sub()
    {
        $this->form_validation->set_rules('subCmtDesc[]', 'Description', 'trim|required');
        $this->form_validation->set_rules('areaSubCmnt[]', 'Area', 'trim|required');
        $this->form_validation->set_rules('subCmtHead[]', 'Committee Head', 'trim|required');
        $this->form_validation->set_rules('subCmtAddedDate[]', 'Added Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->saveCommittee_sub());
        }
    }

    function save_SubCmntMem()
    {
        $this->form_validation->set_rules('Com_MasterID', 'Member Name', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_SubCmntMem());
        }
    }

    function fetch_SubCmnt_members()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $CommitteeAreawiseID = trim($this->input->post('CommitteeAreawiseID') ?? '');

        $this->datatables->select("CName_with_initials,CommitteePositionDes,CommitteeMemID,joinedDate,isMemActive ");
        $this->datatables->from('srp_erp_ngo_com_committeemembers')
            ->join('srp_erp_ngo_com_committeesmaster', 'srp_erp_ngo_com_committeemembers.CommitteeID = srp_erp_ngo_com_committeesmaster.CommitteeID')
            ->join('srp_erp_ngo_com_committeeareawise', 'srp_erp_ngo_com_committeemembers.CommitteeAreawiseID = srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID')
            ->join('srp_erp_ngo_com_committeeposition', 'srp_erp_ngo_com_committeemembers.CommitteePositionID = srp_erp_ngo_com_committeeposition.CommitteePositionID')
            ->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_committeemembers.Com_MasterID = srp_erp_ngo_com_communitymaster.Com_MasterID');

        $this->datatables->where('srp_erp_ngo_com_committeemembers.companyID', $companyID);
        $this->datatables->where('srp_erp_ngo_com_committeemembers.CommitteeAreawiseID', $CommitteeAreawiseID);
        $this->datatables->add_column('isMemActive', '$1', 'active_Member(isMemActive)');

        $this->datatables->add_column('edit', '<a onclick="redirect_cmtMemPage(3,' . $CommitteeAreawiseID . ',$1)"><span title="Member Services" rel="tooltip" class="fa fa-eye" data-original-title="Service"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="redirect_cmtMemPage(2,' . $CommitteeAreawiseID . ',$1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span></a>
&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="delete_SubCmntMem($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'CommitteeMemID');
        echo $this->datatables->generate();
    }

    function delete_SubCmntMem()
    {
        echo json_encode($this->CommunityNgo_model->delete_SubCmntMem());
    }

    public function fetchEdit_subComt()
    {
        $cmtId = $_POST['id'];
        $cmId = $cmtId[0];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->fetchEdit_subComt($cmId);

    }

    function get_editComMaserHd()
    {

        $editIdCmt = $_POST['editIdCmt'];
        $areaEditCmt = $_POST['areaEditCmt'];
        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_editComMaserHd($editIdCmt, $areaEditCmt);
    }

    public function edit_subComtSave()
    {
        $this->form_validation->set_rules('editsubCmtDesc', 'Committee Description', 'trim|required');
        $this->form_validation->set_rules('hidden-id', 'Sub Committee Master ID', 'trim|required');
        $this->form_validation->set_rules('editareaSubCmnt', 'Area', 'trim|required');
        $this->form_validation->set_rules('editsubCmtHead', 'Committee Head', 'trim|required');
        $this->form_validation->set_rules('editCmtAddedDate', 'Added Date', 'trim|required');
        $this->form_validation->set_rules('editCmtExrDate', 'Expiry Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $addedDate = strtotime($this->input->post('editCmtAddedDate'));
            $exp_date = strtotime($this->input->post('editCmtExrDate'));
            if ($exp_date >= $addedDate) {
                echo json_encode($this->CommunityNgo_model->edit_subComtSave());
            } else {
                echo json_encode(array('e', 'Expiry Date should be greater than Added Date'));
            }
        }
    }

    function save_cmteMembrEdit()
    {
        $this->form_validation->set_rules('editCom_MasterID', 'Member', 'trim|required');
        $this->form_validation->set_rules('editCommtPosID', 'Position', 'trim|required|numeric');
        $this->form_validation->set_rules('editjoinedDate', 'Joined Date', 'trim|required');
        $this->form_validation->set_rules('editExpDate', 'Expiry Date', 'trim|required');

        $editCommitteeAreawiseID = $this->input->post('editCommitteeAreawiseID');

        $master = $this->db->query("select startDate from srp_erp_ngo_com_committeeareawise WHERE CommitteeAreawiseID=$editCommitteeAreawiseID ")->row_array();
        $cOMStartDate = strtotime($master['startDate']);
    //    $comtDate = input_format_date($cOMStartDate, $date_format_policy);

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            $addedDate = strtotime($this->input->post('editjoinedDate'));
            $exp_date = strtotime($this->input->post('editExpDate'));
            if ($cOMStartDate > $addedDate) {

                echo json_encode(array('e', 'Joined Date should be greater than Committee Added Date'));

            }
            else if ($exp_date < $addedDate) {
                echo json_encode(array('e', 'Expiry Date should be greater than Joined Date'));

            }
            else {
                echo json_encode($this->CommunityNgo_model->save_cmteMembrEdit());

            }
        }
    }

    //committee member services
    function fetch_comiteMem_service()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $CommitteeMemID = $this->input->post('masterID');
        $this->datatables->select("CmtMemServiceID,CmtMemService,ServiceDate,sortOrder");
        $this->datatables->from('srp_erp_ngo_com_committeememberservices');
        $this->datatables->where('companyID', $companyID);
        $this->datatables->where('CommitteeMemID', $CommitteeMemID);
        $this->datatables->add_column('edit', '<div class="hide updatediv" id="updateMemService_$1"><button id="" type="button" onclick="memService_update($1);" class=" btn btn-primary btn-xs">Save</button>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<button id="" type="button" onclick="memService_cancel($1);" class=" btn btn-default btn-xs">Cancel</button></div>
<div class="canceldiv" id="editmemService_$1"><a id="" class="CA_Alter_btn"  onclick="edit_memService($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="delete_memService($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></div>', 'CmtMemServiceID');
        $this->datatables->add_column('CmtMemService', '$1', 'cmtee_memServiceDel(CmtMemServiceID,CmtMemService,cmtmemservice,CommitteeMemID)');
        $this->datatables->add_column('ServiceDate', '$1', 'cmtee_memServiceDel(CmtMemServiceID,ServiceDate,servicedate,CommitteeMemID)');
        $this->datatables->add_column('sortOrder', '$1', 'cmtee_memServiceDel(CmtMemServiceID,sortOrder,order,CommitteeMemID)');

        echo $this->datatables->generate();
    }

    function save_comiteMemService()
    {
        $this->form_validation->set_rules('CmtMemService', 'Member Service', 'trim|required');
        $this->form_validation->set_rules('ServiceDate', 'Date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            echo json_encode($this->CommunityNgo_model->save_comiteMemService());
        }
    }

    function delete_comiteMemService()
    {
        echo json_encode($this->CommunityNgo_model->delete_comiteMemService());
    }

    /*Committee report details*/
    function get_committeeStatus_pdf()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $CommitteeID1 = trim($this->input->post('CommitteeID1') ?? '');
        $convertFormat = convert_date_format_sql();

        $deleted = " AND srp_erp_ngo_com_committeesmaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_committeesmaster.companyID = " . $companyID . $deleted;

        if ($CommitteeID1 == null || $CommitteeID1 == '') {
        } else {

            $data['cmteMasRow'] = $this->db->query("SELECT srp_erp_ngo_com_committeesmaster.companyID,srp_erp_ngo_com_committeesmaster.createdUserID,srp_erp_ngo_com_committeesmaster.CommitteeID,CommitteeDes,srp_erp_ngo_com_committeesmaster.isActive FROM srp_erp_ngo_com_committeesmaster WHERE $where AND srp_erp_ngo_com_committeesmaster.CommitteeID='" . $CommitteeID1 . "' ORDER BY srp_erp_ngo_com_committeesmaster.CommitteeID DESC")->row_array();

            $data['cmtteeMas'] = $this->db->query("SELECT srp_erp_ngo_com_committeesmaster.companyID,srp_erp_ngo_com_committeesmaster.createdUserID,srp_erp_ngo_com_committeesmaster.CommitteeID,CommitteeDes,srp_erp_ngo_com_committeesmaster.isActive,srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID,CommitteeAreawiseDes,SubAreaId,CommitteeHeadID,DATE_FORMAT(startDate,'$convertFormat') AS startDatet,DATE_FORMAT(endDate,'$convertFormat') AS endDatet,awRemark,srp_erp_ngo_com_committeeareawise.isActive AS SbActive,srp_erp_statemaster.stateID,srp_erp_statemaster.Description as stDescription, CName_with_initials,TP_home,TP_Mobile FROM srp_erp_ngo_com_committeesmaster LEFT JOIN srp_erp_ngo_com_committeeareawise ON srp_erp_ngo_com_committeeareawise.CommitteeID=srp_erp_ngo_com_committeesmaster.CommitteeID LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_committeeareawise.CommitteeHeadID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID=srp_erp_ngo_com_committeeareawise.SubAreaId WHERE $where AND srp_erp_ngo_com_committeeareawise.CommitteeID='" . $CommitteeID1 . "' ORDER BY srp_erp_ngo_com_committeesmaster.CommitteeID ASC")->result_array();
        }

        // $data["type"] = "pdf";
        $html = $this->load->view('system/communityNgo/ajax/load_com_ngo_committeeStatus_pdf', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    //export to excel

    function fetch_committee_for_excel()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $CommitteeID1 = trim($this->input->post('CommitteeID1') ?? '');
        $convertFormat = convert_date_format_sql();

        $deleted = " AND srp_erp_ngo_com_committeesmaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_committeesmaster.companyID = " . $companyID . $deleted;

        if ($CommitteeID1 == null || $CommitteeID1 == '') {
        } else {

            $details = $this->db->query("SELECT srp_erp_ngo_com_committeesmaster.companyID,srp_erp_ngo_com_committeesmaster.createdUserID,srp_erp_ngo_com_committeesmaster.CommitteeID,CommitteeDes,srp_erp_ngo_com_committeesmaster.isActive,srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID,CommitteeAreawiseDes,SubAreaId,CommitteeHeadID,DATE_FORMAT(startDate,'$convertFormat') AS startDatet,DATE_FORMAT(endDate,'$convertFormat') AS endDatet,awRemark,srp_erp_ngo_com_committeeareawise.isActive AS SbActive,srp_erp_statemaster.stateID,srp_erp_statemaster.Description as stDescription, CName_with_initials,TP_home,TP_Mobile FROM srp_erp_ngo_com_committeesmaster LEFT JOIN srp_erp_ngo_com_committeeareawise ON srp_erp_ngo_com_committeeareawise.CommitteeID=srp_erp_ngo_com_committeesmaster.CommitteeID LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_committeeareawise.CommitteeHeadID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID=srp_erp_ngo_com_committeeareawise.SubAreaId WHERE $where AND srp_erp_ngo_com_committeeareawise.CommitteeID='" . $CommitteeID1 . "' ORDER BY srp_erp_ngo_com_committeesmaster.CommitteeID ASC")->result_array();
        }

        $data = array();

        $a = 1;
        foreach ($details as $row) {

            if ($row['SbActive'] == 1) {
                $ActStaus = 'Yes';
            } else {
                $ActStaus = 'No';
            }

            $data[] = array(
                'Num' => $a,
                'CommitteeAreawiseDes' => $row['CommitteeAreawiseDes'],
                'stDescription' => $row['stDescription'],
                'CName_with_initials' => $row['CName_with_initials'],
                'startDatet' => $row['startDatet'],
                'endDatet' => $row['endDatet'],
                'ActStaus' => $ActStaus,
            );
            $a++;
        }

        return ['comCommittee' => $data];

    }

    function excel_committeeExport()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Committee List');
        $this->load->database();
        $data = $this->fetch_committee_for_excel();

        $header = ['#', 'SUB COMMITTEES', 'AREA / MAHALLA', 'COMMITTEE HEAD', 'ADDED DATE', 'EXPIRY DATE', 'IS ACTIVE'];
        $comCommittee = $data['comCommittee'];

        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray(['Committee List'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->getStyle('A4:Q4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A4:Q4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($comCommittee, null, 'A6');
        ob_clean();
        ob_start(); # added
        $filename = 'Committee.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        ob_clean();
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function get_subCommitteeState_pdf()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $CommitteeID1 = trim($this->input->post('CommitteeID') ?? '');
        $CommitteeAreawiseID = trim($this->input->post('CommitteeAreawiseID') ?? '');
        $convertFormat = convert_date_format_sql();

        $deleted = " AND srp_erp_ngo_com_committeesmaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_committeesmaster.companyID = " . $companyID . $deleted;

        if ($CommitteeID1 == null || $CommitteeID1 == '') {
        } else {

            $data['subCmtMasRw'] = $this->db->query("SELECT srp_erp_ngo_com_committeesmaster.companyID,srp_erp_ngo_com_committeesmaster.createdUserID,srp_erp_ngo_com_committeesmaster.CommitteeID,CommitteeDes,srp_erp_ngo_com_committeesmaster.isActive,srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID,CommitteeAreawiseDes,SubAreaId,CommitteeHeadID,DATE_FORMAT(startDate,'.$convertFormat.') AS startDatet,DATE_FORMAT(endDate,'.$convertFormat.') AS endDatet,awRemark,srp_erp_ngo_com_committeeareawise.isActive AS SbActive,srp_erp_statemaster.stateID,srp_erp_statemaster.Description as stDescription, CName_with_initials,TP_home,TP_Mobile FROM srp_erp_ngo_com_committeesmaster LEFT JOIN srp_erp_ngo_com_committeeareawise ON srp_erp_ngo_com_committeeareawise.CommitteeID=srp_erp_ngo_com_committeesmaster.CommitteeID LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_committeeareawise.CommitteeHeadID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID=srp_erp_ngo_com_committeeareawise.SubAreaId WHERE $where AND srp_erp_ngo_com_committeeareawise.CommitteeID='" . $CommitteeID1 . "' AND srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID='" . $CommitteeAreawiseID . "' ORDER BY srp_erp_ngo_com_committeesmaster.CommitteeID DESC")->row_array();

            $data['subCmtMems'] = $this->db->query("SELECT srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID,CommitteeAreawiseDes,SubAreaId,CommitteeHeadID,DATE_FORMAT(joinedDate,'.$convertFormat.') AS joinedDatet,DATE_FORMAT(expiryDate,'.$convertFormat.') AS expiryDatet,committeeMemRemark,isMemActive ,srp_erp_ngo_com_committeeposition.CommitteePositionID,srp_erp_ngo_com_committeeposition.CommitteePositionDes, CName_with_initials,TP_home,TP_Mobile FROM srp_erp_ngo_com_committeemembers LEFT JOIN srp_erp_ngo_com_committeeareawise ON srp_erp_ngo_com_committeeareawise.CommitteeAreawiseID=srp_erp_ngo_com_committeemembers.CommitteeAreawiseID LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_committeemembers.Com_MasterID LEFT JOIN srp_erp_ngo_com_committeeposition ON srp_erp_ngo_com_committeeposition.CommitteePositionID=srp_erp_ngo_com_committeemembers.CommitteePositionID WHERE srp_erp_ngo_com_committeemembers.companyID = " . $companyID . " AND srp_erp_ngo_com_committeemembers.CommitteeID='" . $CommitteeID1 . "' AND srp_erp_ngo_com_committeemembers.CommitteeAreawiseID='" . $CommitteeAreawiseID . "' ORDER BY srp_erp_ngo_com_committeemembers.CommitteeMemID ASC")->result_array();
        }

        // $data["type"] = "pdf";
        $html = $this->load->view('system/communityNgo/ajax/load_com_ngo_subCommitteeState_pdf', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');
    }

    /*End of Committee Contents */
    /* community content report */

    /* family content report */
    function get_communityMem_famReport()
    {
        $date_format_policy = date_format_policy();

        $FamMasRpID = $this->input->post("FamMasterID");
        $famAreaId = $this->input->post("famAreaId");
        $houseOwnshp = $this->input->post("houseOwnshp");
        $houseType = $this->input->post("houseType");

        $FamMasRrtID = "";
        if (!empty($FamMasRpID)) {
            $FamMasRrtID = "AND srp_erp_ngo_com_familymaster.FamMasterID IN(" . join(',', $FamMasRpID) . ")";
        }

        $houseOwnshpS = "";
        if (!empty($houseOwnshp)) {
            $houseOwnshpS = "AND srp_erp_ngo_com_house_enrolling.ownershipAutoID = $houseOwnshp ";
        }

        $houseTypeS = "";
        if (!empty($houseType)) {
            $houseTypeS = "AND srp_erp_ngo_com_house_enrolling.hTypeAutoID = $houseType ";
        }

        $convertFormat = convert_date_format_sql();

        $filter_req = array("AND (srp_erp_ngo_com_communitymaster.RegionID='" . $famAreaId . "')" => $famAreaId);
        $set_filter_req = array_filter($filter_req);
        $where_clauseq = join(" ", array_keys($set_filter_req));

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_familymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_familymaster.companyID = " . $companyID . $deleted;


        $data['familyReport'] = $this->db->query("SELECT *,srp_erp_ngo_com_familymaster.companyID,srp_erp_ngo_com_familymaster.FamMasterID,CName_with_initials,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS diviDescription FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_house_enrolling ON srp_erp_ngo_com_house_enrolling.FamMasterID=srp_erp_ngo_com_familymaster.FamMasterID WHERE $where $where_clauseq $FamMasRrtID $houseOwnshpS $houseTypeS ORDER BY srp_erp_ngo_com_familymaster.FamMasterID DESC ")->result_array();

        $data["type"] = "html";
        echo $html = $this->load->view('system/communityNgo/ajax/load_community_family_report', $data, true);

    }

    function get_communityMem_famReport_pdf()
    {
        $date_format_policy = date_format_policy();

        $FamMasRpID = $this->input->post("FamMasterID");
        $famAreaId = $this->input->post("famAreaId");
        $houseOwnshp = $this->input->post("houseOwnshp");
        $houseType = $this->input->post("houseType");

        $FamMasRrtID = "";
        if (!empty($FamMasRpID)) {
            $FamMasRrtID = "AND srp_erp_ngo_com_familymaster.FamMasterID IN(" . join(',', $FamMasRpID) . ")";
        }

        $houseOwnshpS = "";
        if (!empty($houseOwnshp)) {
            $houseOwnshpS = "AND srp_erp_ngo_com_house_enrolling.ownershipAutoID = $houseOwnshp ";
        }

        $houseTypeS = "";
        if (!empty($houseType)) {
            $houseTypeS = "AND srp_erp_ngo_com_house_enrolling.hTypeAutoID = $houseType ";
        }

        $convertFormat = convert_date_format_sql();

        $filter_req = array("AND (srp_erp_ngo_com_communitymaster.RegionID='" . $famAreaId . "')" => $famAreaId);
        $set_filter_req = array_filter($filter_req);
        $where_clauseq = join(" ", array_keys($set_filter_req));

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_familymaster.isDeleted = '0' ";

        $where = "srp_erp_ngo_com_familymaster.companyID = " . $companyID . $deleted;

        $data['familyReport'] = $this->db->query("SELECT *,srp_erp_ngo_com_familymaster.companyID,srp_erp_ngo_com_familymaster.FamMasterID,CName_with_initials,srp_erp_gender.name AS Gender,CONCAT(TP_Mobile,' | ',TP_home) AS PrimaryNumber,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,areac.Description AS Region,divisionc.stateID,divisionc.Description AS diviDescription FROM srp_erp_ngo_com_familymaster INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familymaster.LeaderID LEFT JOIN srp_titlemaster ON srp_titlemaster.TitleID = srp_erp_ngo_com_communitymaster.TitleID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID = srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_statemaster divisionc ON divisionc.stateID= srp_erp_ngo_com_communitymaster.GS_Division LEFT JOIN srp_erp_statemaster areac ON areac.stateID = srp_erp_ngo_com_communitymaster.RegionID LEFT JOIN srp_erp_ngo_com_house_enrolling ON srp_erp_ngo_com_house_enrolling.FamMasterID=srp_erp_ngo_com_familymaster.FamMasterID WHERE $where $where_clauseq $FamMasRrtID $houseOwnshpS $houseTypeS ORDER BY srp_erp_ngo_com_familymaster.FamMasterID DESC ")->result_array();

        $data["type"] = "pdf";
        $html = $this->load->view('system/communityNgo/ajax/load_community_family_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');

    }


    function get_totalFamHousing()
    {

        $FamMasterID = $this->input->post("FamMasterID");
        $famAreaId = $this->input->post("famAreaId");
        $houseOwnshp = $this->input->post("houseOwnshp");
        $houseType = $this->input->post("houseType");

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->get_totalFamHousing($FamMasterID, $famAreaId,$houseOwnshp,$houseType);
    }

    function get_houseEnrolling_del(){

        $companyID = $this->common_data['company_data']['company_id'];

        $FamMasterID = $this->input->post("FamMasterID");
        $famAreaId = $this->input->post("famAreaId");
        $houseOwnshp = $this->input->post("houseOwnshp");
        $houseType = $this->input->post("houseType");

        $FamMasterIDS = "";
        if (isset($FamMasterID) && !empty($FamMasterID)) {
            $FamMasterIDS = "AND hEnr.FamMasterID IN(" . join(',', $FamMasterID) . ")";
        }

        $filter_req = array("AND (cm.RegionID=" . $famAreaId . ")" => $famAreaId);
        $set_filter_req = array_filter($filter_req);
        $where_clauseA = join(" ", array_keys($set_filter_req));

        $houseOwnshpS = "";
        if (!empty($houseOwnshp)) {
            $houseOwnshpS = "AND hEnr.ownershipAutoID = $houseOwnshp ";
        }

        $houseTypeS = "";
        if (!empty($houseType)) {
            $houseTypeS = "AND hEnr.hTypeAutoID = $houseType ";
        }

        $deleted = " AND fm.isDeleted = '0' ";

        $where = "hEnr.companyID = " . $companyID . $deleted ;

        $data['housingEnrl'] = $this->db->query("SELECT hEnr.hEnrollingID,hEnr.companyID AS companyID,fm.FamilySystemCode,fm.FamilyName,hEnr.FamHouseSt,hEnr.Link_hEnrollingID,cm.CName_with_initials,cm.C_Address,onrSp.ownershipDescription,tpMas.hTypeDescription,hEnr.hESizeInPerches,hEnr.isHmElectric,hEnr.isHmWaterSup,hEnr.isHmToilet,hEnr.isHmBathroom,hEnr.isHmTelephone,hEnr.isHmKitchen FROM srp_erp_ngo_com_house_enrolling hEnr 
LEFT JOIN srp_erp_ngo_com_familymaster fm ON fm.FamMasterID = hEnr.FamMasterID 
LEFT JOIN srp_erp_ngo_com_house_ownership_master onrSp ON onrSp.ownershipAutoID = hEnr.ownershipAutoID
LEFT JOIN srp_erp_ngo_com_house_type_master tpMas ON tpMas.hTypeAutoID = hEnr.hTypeAutoID
LEFT JOIN srp_erp_ngo_com_communitymaster cm ON cm.Com_MasterID = fm.LeaderID
WHERE $where AND (hEnr.FamHouseSt = '0' OR hEnr.FamHouseSt = NULL) $FamMasterIDS  " . " $where_clauseA $houseOwnshpS $houseTypeS ORDER BY hEnr.hEnrollingID")->result_array();

        $html = $this->load->view('system/communityNgo/ajax/load_com_dash_other_housingDel.php', $data, true);

        // if ($this->input->post('html')) {
        echo $html;
        //  } else {
        //  $this->load->library('pdf');
        /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
        //  }
    }

    /*get_committees_report report details*/

    function get_committees_report()
    {

        $committRpID = $this->input->post("CommitteeID");
        $commitAreaId = $this->input->post("commitAreaId");
        $commit_memId = $this->input->post("commit_memId");

        $comtRrtID = "";
        if (!empty($committRpID)) {
            $comtRrtID = "AND srp_erp_ngo_com_committeesmaster.CommitteeID IN(" . join(',', $committRpID) . ")";
        }

        $filter_subArea = array("AND (srp_erp_ngo_com_committeeareawise.SubAreaId='" . $commitAreaId . "')" => $commitAreaId, "AND (srp_erp_ngo_com_committeeareawise.CommitteeHeadID='" . $commit_memId . "')" => $commit_memId);
        $set_filter_subArea = array_filter($filter_subArea);
        $where_clauseq = join(" ", array_keys($set_filter_subArea));

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_committeesmaster.isActive = '1' ";

        $where = "srp_erp_ngo_com_committeesmaster.companyID = " . $companyID . $deleted;

        $data['commiteRprt'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_committeesmaster INNER JOIN srp_erp_ngo_com_committeeareawise ON srp_erp_ngo_com_committeesmaster.CommitteeID=srp_erp_ngo_com_committeeareawise.CommitteeID WHERE $where " . $where_clauseq . " $comtRrtID  GROUP BY srp_erp_ngo_com_committeesmaster.CommitteeID DESC ")->result_array();
        /// $data['commiteRprt'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_committeesmaster  WHERE $where " . $comtRrtID . "   ORDER BY srp_erp_ngo_com_committeesmaster.CommitteeID DESC ")->result_array();

        $data['commitAreaId'] = $commitAreaId;
        $data['commit_memId'] = $commit_memId;

        $data["type"] = "html";
        echo $html = $this->load->view('system/communityNgo/ajax/load_committees_content_report', $data, true);

    }

    function get_committees_report_pdf()
    {

        $committRpID = $this->input->post("CommitteeID");
        $commitAreaId = $this->input->post("commitAreaId");
        $commit_memId = $this->input->post("commit_memId");

        $comtRrtID = "";
        if (!empty($committRpID)) {
            $comtRrtID = "AND srp_erp_ngo_com_committeesmaster.CommitteeID IN(" . join(',', $committRpID) . ")";
        }

        $filter_subArea = array("AND (srp_erp_ngo_com_committeeareawise.SubAreaId='" . $commitAreaId . "')" => $commitAreaId, "AND (srp_erp_ngo_com_committeeareawise.CommitteeHeadID='" . $commit_memId . "')" => $commit_memId);
        $set_filter_subArea = array_filter($filter_subArea);
        $where_clauseq = join(" ", array_keys($set_filter_subArea));

        $companyID = $this->common_data['company_data']['company_id'];

        $deleted = " AND srp_erp_ngo_com_committeesmaster.isActive = '1' ";

        $where = "srp_erp_ngo_com_committeesmaster.companyID = " . $companyID . $deleted;

        $data['commiteRprt'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_committeesmaster INNER JOIN srp_erp_ngo_com_committeeareawise ON srp_erp_ngo_com_committeesmaster.CommitteeID=srp_erp_ngo_com_committeeareawise.CommitteeID WHERE $where " . $where_clauseq . " $comtRrtID  GROUP BY srp_erp_ngo_com_committeesmaster.CommitteeID DESC ")->result_array();
        /// $data['commiteRprt'] = $this->db->query("SELECT * FROM srp_erp_ngo_com_committeesmaster  WHERE $where " . $comtRrtID . "   ORDER BY srp_erp_ngo_com_committeesmaster.CommitteeID DESC ")->result_array();

        $data['commitAreaId'] = $commitAreaId;
        $data['commit_memId'] = $commit_memId;

        $data["type"] = "pdf";
        $html = $this->load->view('system/communityNgo/ajax/load_committees_content_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4');

    }

    /*end of community content report dropdown*/
    /*community Family login dropdown*/

    function fetch_familyLog_list()
    {

        $fam_key = trim($this->input->post('logFemKey') ?? '');

        echo $this->CommunityNgo_model->fetch_LogFamilies_list($fam_key);

    }

    function comFamily_logConfig()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $cmnte = $this->input->post('cmnte');
        $data['masterID'] = $this->input->post('masterID');
        $FamMasterID = $data['masterID'];
        $data['LeaderID'] = $this->input->post('LeaderID');
        $LeaderID = $data['LeaderID'];


        if ($cmnte == '1') {

            $where = "srp_erp_ngo_com_familymaster.companyID = " . $companyID;

            $data['famLogMas'] = $this->db->query("SELECT FamMasterID,FamilySerialNo,FamilySystemCode,LeaderID,FamilyName,TP_Mobile,DATE_FORMAT(FamilyAddedDate,'.$convertFormat.') AS FamilyAddedDt,FamUsername,FamPassword,isLoginActive,NoOfLoginAttempt,srp_erp_ngo_com_communitymaster.CName_with_initials,srp_erp_ngo_com_communitymaster.RegionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description as stDescription FROM srp_erp_ngo_com_familymaster LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID=srp_erp_ngo_com_communitymaster.RegionID WHERE $where AND srp_erp_ngo_com_familymaster.FamMasterID = {$FamMasterID} ")->row_array();

            $this->load->view('system/CommunityNgo/ngo_mo_familyLoginProgress.php', $data);
        } else if ($cmnte == '2') {

            $where = "srp_erp_ngo_com_familymaster.companyID = " . $companyID;

            $data['famLogMas'] = $this->db->query("SELECT FamMasterID,FamilySerialNo,FamilySystemCode,LeaderID,FamilyName,TP_Mobile,DATE_FORMAT(FamLogCreatedDate,'.$convertFormat.') AS FamLogCreatedDt,FamUsername,FamPassword,isLoginActive,NoOfLoginAttempt,srp_erp_ngo_com_communitymaster.CName_with_initials,srp_erp_ngo_com_communitymaster.RegionID,srp_erp_statemaster.stateID,srp_erp_statemaster.Description as stDescription FROM srp_erp_ngo_com_familymaster LEFT JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_familymaster.LeaderID=srp_erp_ngo_com_communitymaster.Com_MasterID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID=srp_erp_ngo_com_communitymaster.RegionID WHERE $where AND srp_erp_ngo_com_familymaster.FamMasterID = {$FamMasterID} ")->row_array();

            $this->load->view('system/CommunityNgo/ngo_mo_familyLogConfigEdit.php', $data);
        }


    }

    function save_famLogDel()
    {
        $this->form_validation->set_rules('FamUsername', 'Family Username', 'trim|required');
        $this->form_validation->set_rules('FamPassword', 'Family Password', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_famLogDel());
        }
    }

    function fetch_famLog_del()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $FamMasterID = trim($this->input->post('FamMasterID') ?? '');

        $this->datatables->select("FamMasterID,FamUsername,FamPassword,FamLogCreatedDate,isLoginActive");
        $this->datatables->from('srp_erp_ngo_com_familymaster');
        $this->datatables->where('srp_erp_ngo_com_familymaster.companyID', $companyID);
        $this->datatables->where('srp_erp_ngo_com_familymaster.FamMasterID', $FamMasterID);
        $this->datatables->add_column('isLoginActive', '$1', 'active_famLog(isLoginActive)');

        $this->datatables->add_column('edit', '<a onclick="redirect_famLogConfPage(2,' . $FamMasterID . ',$1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span></a>
&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="delete_famLogDel($1)"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>', 'FamMasterID');
        echo $this->datatables->generate();
    }

    function save_famLogEdit()
    {
        $this->form_validation->set_rules('editFamMasterID', 'Family', 'trim|required');
        $this->form_validation->set_rules('editFamUserName', 'User Name', 'trim|required');
        $this->form_validation->set_rules('editFamPassWord', 'PassWord', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {

            echo json_encode($this->CommunityNgo_model->save_famLogEdit());
        }
    }

    function delete_famLogDel()
    {
        echo json_encode($this->CommunityNgo_model->delete_famLogDel());
    }
    /*end of community Family login dropdown*/

    /* rent update */
    function update_rentStockDel()
    {
        echo json_encode($this->CommunityNgo_model->update_rentStockDel());
    }
    /* end of rent update */

    /* Zakat project proposal */
    function load_project_proposal_master_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('q') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');
        $filter = "";
        if ($text != '') {
            $filter = " AND (ppm.documentSystemCode LIKE '%$text%' OR ppm.proposalName LIKE '%$text%' )";
        }
        $convertFormat = convert_date_format_sql();

        $data['master'] = $this->db->query("SELECT ppm.proposalID,ppm.documentSystemCode,ppm.proposalName,DATE_FORMAT(ppm.DocumentDate,'{$convertFormat}') AS DocumentDate,st.description as statusName,ppm.confirmedYN,ppm.approvedYN as approvedYN,ppm.createdUserID FROM srp_erp_ngo_projectproposals ppm LEFT JOIN srp_erp_ngo_status st ON ppm.status = st.statusID LEFT JOIN srp_erp_ngo_projects ON ppm.projectID=srp_erp_ngo_projects.ngoProjectID WHERE ppm.companyID={$companyID} AND st.documentID = 6 AND ppm.zakatDefault='1' $filter  ORDER BY ppm.proposalID DESC")->result_array();

        $this->load->view('system/communityNgo/ajax/load_comProject_proposal_master_view', $data);
    }

    function send_project_proposal_email()
    {
        $this->form_validation->set_rules('selectedDonorsEmailSync[]', 'Donor', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->send_project_proposal_email());
        }

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

    function referback_project_proposal()
    {
        echo json_encode($this->CommunityNgo_model->referback_project_proposal());
    }

    function delete_project_proposal()
    {
        echo json_encode($this->CommunityNgo_model->delete_project_proposal());
    }

    //create
    function save_project_proposal_header()
    {
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('proposalName', 'Proposal Name', 'trim|required');
        $this->form_validation->set_rules('proposalTitle', 'Proposal Title', 'trim|required');
        $this->form_validation->set_rules('projectID', 'Project', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('startDate', 'Estimated Start Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('endDate', 'Estimated End Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('province', 'Province', 'trim|required');
        $this->form_validation->set_rules('district', 'District', 'trim|required');
        $this->form_validation->set_rules('countryID', 'Country', 'trim|required');
        $this->form_validation->set_rules('division', 'Division', 'trim|required');
        //$this->form_validation->set_rules('subProjectID', 'Sub Project', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->save_project_proposal_header());
        }

    }

    function load_project_proposal_header()
    {
        echo json_encode($this->CommunityNgo_model->load_project_proposal_header());
    }

    function load_zaqath_contribution()
    {
        $data['header'] = $this->CommunityNgo_model->load_project_proposal_zaqath_details();
        $this->load->view('system/communityNgo/ajax/load_comProject_zaqath_contribution', $data);
    }

    function load_beneficery_details_view()
    {
        $data['header'] = $this->CommunityNgo_model->load_project_proposal_beneficiary_details();
        $this->load->view('system/communityNgo/ajax/load_comProject_proposal_beneficiary', $data);
    }



    function check_project_proposal_details_exist()
    {
        echo json_encode($this->CommunityNgo_model->load_project_proposal_beneficiary_details());
    }

    function fetch_ngo_sub_projects()
    {
        echo json_encode($this->CommunityNgo_model->fetch_ngo_sub_projects());
    }

    function fetch_project_proposal_zaqath()
    {
        $data['proposalID'] = trim($this->input->post('proposalID') ?? '');
        $data['EconStateID'] = trim($this->input->post('EconStateID') ?? '');

        $this->load->view('system/CommunityNgo/ajax/load_comProject_proposal_zaqAdd.php', $data);


    }

    function fetch_project_proposal_beneficiary()
    {
        $projectID = trim($this->input->post('projectID') ?? '');
        $proposalID = trim($this->input->post('proposalID') ?? '');
        $country = trim($this->input->post('countryID') ?? '');
        $division = trim($this->input->post('division') ?? '');
        $subdivision = trim($this->input->post('subDivision') ?? '');

        $this->datatables->select('bm.systemCode as systemCode,bm.nameWithInitials as name,bm.benificiaryID as benificiaryID', false)
            ->from('srp_erp_ngo_beneficiarymaster as bm')
             ->where('bm.companyID', current_companyID())
            ->where('bm.projectID', $projectID)
            ->where('bm.countryID', $country)
            ->where('bm.division', $division)
            ->where('bm.subDivision', $subdivision)
            ->where('bm.confirmedYN', 1);
        $this->datatables->where('NOT EXISTS(SELECT proposalBeneficiaryID,beneficiaryID FROM srp_erp_ngo_projectproposalbeneficiaries WHERE srp_erp_ngo_projectproposalbeneficiaries.beneficiaryID = bm.benificiaryID AND companyID =' . current_companyID() . ' AND proposalID =  ' . $proposalID . ')');
        $this->datatables->add_column('econState', '$1', 'econ_status(benificiaryID)');
       // $this->datatables->add_column('econState', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'benificiaryID');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'benificiaryID');
        echo $this->datatables->generate();


    }



    function assign_zaqath_for_project_proposal()
    {
        $this->form_validation->set_rules('EconStateID', 'Economic Status', 'required');
       // $this->form_validation->set_rules('AgeGroupID[]', 'Age Group', 'required');
        $AgeGroupID = $this->input->post('AgeGroupID');

        foreach ($AgeGroupID as $key => $project) {
            $this->form_validation->set_rules("AgeGroupID[{$key}]", 'Age Group', 'trim|required');
            $this->form_validation->set_rules("GrpPoints[{$key}]", 'Point', 'trim|required');
            $this->form_validation->set_rules("TotalPerZakat[{$key}]", 'Total Amount', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->assign_zaqath_for_project_proposal());
        }

    }

    function assign_beneficiary_for_project_proposal()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Beneficiary', 'required');
       $this->form_validation->set_rules('EconStateIDs[]', 'Economic Status', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->assign_beneficiary_for_project_proposal());
        }

    }


    function delete_project_proposal_detail()
    {
        echo json_encode($this->CommunityNgo_model->delete_project_proposal_detail());
    }

    function delete_project_zakat_detail()
    {
        echo json_encode($this->CommunityNgo_model->delete_project_zakat_detail());
    }

    function active_project_zakat_detail()
    {
        echo json_encode($this->CommunityNgo_model->active_project_zakat_detail());
    }

    function load_project_image_view()
    {
        $ngoProposalID = trim($this->input->post('proposalID') ?? '');
        $data['docDet'] = $this->CommunityNgo_model->load_project_images();
        $data['status'] = $this->db->query("select approvedYN,confirmedYN FROM srp_erp_ngo_projectproposals WHERE proposalID = {$ngoProposalID} ")->row_array();
        $data['ngoProposalID'] = $ngoProposalID;
        $this->load->view('system/communityNgo/ajax/load_comProject_image_view', $data);
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
        $this->load->view('system/communityNgo/ajax/load_comProject_attachment_view', $data);
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

    function project_proposal_confirmation()
    {
        echo json_encode($this->CommunityNgo_model->project_proposal_confirmation());
    }

    //end of create proposal

    function load_project_proposal_print_pdf()
    {
        $proposalID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('proposalID') ?? '');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("SELECT pro.projectImage,pp.proposalName as ppProposalName,pro.projectName as proProjectName,DATE_FORMAT(pp.DocumentDate,'{$convertFormat}') AS DocumentDate,DATE_FORMAT(pp.startDate,'{$convertFormat}') AS ppStartDate,DATE_FORMAT(pp.endDate,'{$convertFormat}') AS ppEndDate,DATE_FORMAT(pp.DocumentDate, '%M %Y') as subprojectName,pp.detailDescription as ppDetailDescription,pp.projectSummary as ppProjectSummary,pp.totalNumberofHouses as ppTotalNumberofHouses,pp.floorArea as ppFloorArea,pp.costofhouse as ppCostofhouse,pp.additionalCost as ppAdditionalCost,pp.EstimatedDays as ppEstimatedDays,pp.proposalTitle as ppProposalTitle,pp.processDescription as ppProcessDescription,con.name as contractorName,ca.GLDescription as caBankAccName,ca.bankName as caBankName,ca.bankAccountNumber as caBankAccountNumber FROM srp_erp_ngo_projectproposals pp JOIN srp_erp_ngo_projects pro ON pp.projectID = pro.ngoProjectID LEFT JOIN srp_erp_ngo_contractors con ON pp.contractorID = con.contractorID LEFT JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = pp.bankGLAutoID WHERE pp.proposalID = $proposalID  ")->row_array();

        $data['detail'] = $this->db->query("SELECT ppb.beneficiaryID as ppbBeneficiaryID,DATE_FORMAT(bm.registeredDate,'{$convertFormat}') AS bmRegisteredDate,DATE_FORMAT(bm.dateOfBirth,'{$convertFormat}') AS bmDateOfBirth,bm.nameWithInitials as bmNameWithInitials,bm.systemCode as bmSystemCode, CASE bm.ownLandAvailable WHEN 1 THEN 'Yes' WHEN 2 THEN 'No' END as bmOwnLandAvailable,bm.NIC as bmNIC,bm.familyMembersDetail as bmFamilyMembersDetail,bm.reasoninBrief as bmReasoninBrief,bm.totalSqFt as bmTotalSqFt,bm.totalCost as bmTotalCost,bm.helpAndNestImage as bmHelpAndNestImage,bm.helpAndNestImage1 as bmHelpAndNestImage1,bm.ownLandAvailableComments as bmOwnLandAvailableComments FROM srp_erp_ngo_projectproposalbeneficiaries ppb LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON ppb.beneficiaryID = bm.benificiaryID WHERE proposalID = $proposalID ")->result_array();

        $data['images'] = $this->db->query("SELECT imageType,imageName FROM srp_erp_ngo_projectproposalimages WHERE ngoProposalID = $proposalID ")->result_array();

        $data['moto'] = $this->db->query("SELECT companyPrintTagline FROM srp_erp_company WHERE company_id = {$companyID}")->row_array();

        $data['proposalID'] = $proposalID;

        $data['output'] = 'view';

        $this->load->view('system/operationNgo/ngo_beneficiary_helpnest_print_all', $data);
    }

    function load_proposal_family_del()
    {

        $proposalBeneficiaryID = trim($this->input->post('proposalBeneficiaryID') ?? '');

        $FamMasterID = trim($this->input->post('FamMasterID') ?? '');

        $proposalID = trim($this->input->post('proposalID') ?? '');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();

        $data['datas'] = $this->db->query("SELECT ppd.proposalID,pp.isConvertedToProject as isConvertedToProject,pp.documentSystemCode,pp.projectID,pp.proposalTitle,pp.DocumentDate,pp.startDate,pp.endDate,ppd.proposalBeneficiaryID,ppd.EconStateID,bm.systemCode as benCode,bm.nameWithInitials as name,bm.FamMasterID,ecnn.EconStateDes,bm.totalCost AS totalCost,ppd.isQualified AS isQualified,pp.approvedYN as approvedYN,pp.confirmedYN as confirmedYN,fmc.FamilySystemCode,fmc.FamilyName,cur.CurrencyCode,pro.projectName,ppd.companyID FROM srp_erp_ngo_projectproposalbeneficiaries ppd LEFT JOIN srp_erp_ngo_beneficiarymaster bm ON bm.benificiaryID = ppd.beneficiaryID LEFT JOIN srp_erp_ngo_projectproposals pp ON pp.proposalID = ppd.proposalID LEFT JOIN srp_erp_ngo_projects pro ON pro.ngoProjectID=pp.projectID LEFT JOIN srp_erp_ngo_com_familymaster fmc ON fmc.FamMasterID = bm.FamMasterID LEFT JOIN srp_erp_ngo_com_familyeconomicstatemaster ecnn ON ppd.EconStateID = ecnn.EconStateID LEFT JOIN srp_erp_companycurrencyassign cur ON pp.companyLocalCurrencyID = cur.currencyID WHERE ppd.companyID = '{$companyID}' AND ppd.proposalBeneficiaryID = '{$proposalBeneficiaryID}' AND ppd.proposalID = '{$proposalID}' AND bm.FamMasterID = '{$FamMasterID}'")->row_array();

        $this->db->select("srp_erp_ngo_com_familydetails.Com_MasterID,srp_erp_ngo_com_familydetails.FamMasterID,FamDel_ID,FamilyName,CFullName,CName_with_initials,CurrentStatus,DATE_FORMAT(FamMemAddedDate,'{$convertFormat}') AS FamMemAddedDate,name,Age,relationship,isMove,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,srp_erp_ngo_com_communitymaster.isActive,srp_erp_ngo_com_communitymaster.DeactivatedFor,srp_erp_ngo_com_maritalstatus.maritalstatusID,srp_erp_ngo_com_maritalstatus.maritalstatus");
        $this->db->from('srp_erp_ngo_com_familydetails');
        $this->db->join('srp_erp_ngo_com_familymaster', 'srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_familydetails.FamMasterID', 'left');
        $this->db->join('srp_erp_ngo_com_communitymaster', 'srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familydetails.Com_MasterID', 'left');
        $this->db->join('srp_erp_gender', 'srp_erp_gender.genderID=srp_erp_ngo_com_communitymaster.GenderID', 'left');
        $this->db->join('srp_erp_ngo_com_maritalstatus', 'srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus', 'left');
        $this->db->join('srp_erp_family_relationship', 'srp_erp_family_relationship.relationshipID=srp_erp_ngo_com_familydetails.relationshipID', 'left');

        $this->db->where('srp_erp_ngo_com_familydetails.companyID', $companyID);
        $this->db->where('srp_erp_ngo_com_familydetails.FamMasterID', $FamMasterID);

        $this->db->order_by('FamDel_ID', 'asc');
        $data['famZakat'] = $this->db->get()->result_array();

        $html = $this->load->view('system/communityNgo/ngo_mo_comProject_proposalFamily.php', $data, true);

       // if ($this->input->post('html')) {
            echo $html;
      //  } else {
          //  $this->load->library('pdf');
            /*$pdf = $this->pdf->printed($html, 'A4-L', $data['extra']['master']['approvedYN']);*/
      //  }

    }

    function fetch_beneficiarySet_edit()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $proposalBeneficiaryID = trim($this->input->post('proposalBeneficiaryID') ?? '');

        $this->db->select("ppd.proposalID,pp.isConvertedToProject as isConvertedToProject,pp.documentSystemCode,pp.proposalTitle,ppd.proposalBeneficiaryID,ppd.EconStateID,bm.systemCode as benCode,bm.nameWithInitials as name,ecoMas.EconStateDes,bm.FamMasterID,bm.Com_MasterID,bm.totalCost AS totalCost,bm.totalEstimatedValue,ppd.isQualified AS isQualified,pp.approvedYN as approvedYN,pp.confirmedYN as confirmedYN,ppd.companyID");
        $this->db->from('srp_erp_ngo_projectproposalbeneficiaries ppd');
        $this->db->join('srp_erp_ngo_beneficiarymaster bm', 'bm.benificiaryID = ppd.beneficiaryID', 'left');
        $this->db->join('srp_erp_ngo_projectproposals pp', 'pp.proposalID = ppd.proposalID', 'left');
        $this->db->join('srp_erp_ngo_com_familyeconomicstatemaster ecoMas', 'ecoMas.EconStateID = ppd.EconStateID', 'left');
        $this->db->where('ppd.companyID', $companyID);
        $this->db->where('ppd.proposalBeneficiaryID', $proposalBeneficiaryID);
        $this->db->order_by('proposalBeneficiaryID', 'desc');
        $data =  $this->db->get()->row();

      //  $data = $this->db->query("select proposalZaqathSetID, proposalID, zakEdit.EconStateID,EconStateDes, zakEdit.AgeGroupID,AgeGroup,AgeLimit, GrpPoints, ZakatAmount, TotalPerZakat, isConfirmed, isActive, companyID, companyCode FROM srp_erp_ngo_com_projectproposalzakatsetup zakEdit LEFT JOIN srp_erp_ngo_com_familyeconomicstatemaster ON srp_erp_ngo_com_familyeconomicstatemaster.EconStateID=zakEdit.EconStateID LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID=zakEdit.AgeGroupID WHERE proposalBeneficiaryID={$proposalBeneficiaryID} ")->row_array();
        echo json_encode($data);
    }

    function get_beneEdit_zakat()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $proposalBeneficiaryID = trim($this->input->post('proposalBeneficiaryID') ?? '');
        $edit_beneEcState = trim($this->input->post('edit_beneEcState') ?? '');

        $comPbene = $this->db->query("SELECT pbd.proposalID,bm.Com_MasterID,bm.FamMasterID FROM srp_erp_ngo_projectproposalbeneficiaries pbd INNER JOIN srp_erp_ngo_beneficiarymaster bm ON bm.benificiaryID = pbd.beneficiaryID WHERE pbd.companyID='" . $companyID . "' AND pbd.proposalBeneficiaryID='" .$proposalBeneficiaryID."'");
        $rowPbene = $comPbene->row();

        $queryag1 = $this->db->query("SELECT TotalPerZakat,pagz.isActive AS isActive FROM srp_erp_ngo_com_projectproposalzakatsetup pagz LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON pagz.AgeGroupID=srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID WHERE companyID='" . $companyID . "' AND proposalID='".$rowPbene->proposalID."' AND EconStateID='" . $edit_beneEcState . "' AND pagz.AgeGroupID='1'");
        $rowag1 = $queryag1->row();
        if(isset($rowag1) && $rowag1->isActive =='1'){ $TotalPerZakat1=$rowag1->TotalPerZakat;}else{$TotalPerZakat1=0;}
        $queryag2 = $this->db->query("SELECT TotalPerZakat,pagz.isActive AS isActive  FROM srp_erp_ngo_com_projectproposalzakatsetup pagz LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON pagz.AgeGroupID=srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID WHERE companyID='" . $companyID . "' AND proposalID='".$rowPbene->proposalID."' AND EconStateID='" . $edit_beneEcState . "' AND pagz.AgeGroupID='2'");
        $rowag2 = $queryag2->row();
        if(isset($rowag2) && $rowag2->isActive =='1'){ $TotalPerZakat2=$rowag2->TotalPerZakat;}else{$TotalPerZakat2=0;}
        $queryag3 = $this->db->query("SELECT TotalPerZakat,pagz.isActive AS isActive  FROM srp_erp_ngo_com_projectproposalzakatsetup pagz LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON pagz.AgeGroupID=srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID WHERE companyID='" . $companyID . "' AND proposalID='".$rowPbene->proposalID."' AND EconStateID='" . $edit_beneEcState . "' AND pagz.AgeGroupID='3'");
        $rowag3 = $queryag3->row();
        if(isset($rowag3) && $rowag3->isActive =='1'){$TotalPerZakat3=$rowag3->TotalPerZakat; }else{$TotalPerZakat3=0;}

        $queryFD = $this->db->query("SELECT fd.Com_MasterID,fd.FamMasterID,FamDel_ID,FamilyName,CFullName,CName_with_initials,CurrentStatus,isMove,cm.isActive,cm.DeactivatedFor,COUNT(IF(0 <= TRIM(cm.Age) && TRIM(cm.Age) <= 5,1,NULL))  smal,COUNT(IF(6 <= TRIM(cm.Age) && TRIM(cm.Age) <= 15,1,NULL))  medi,COUNT(IF(16 <= TRIM(cm.Age),1,NULL)) larg FROM srp_erp_ngo_com_familydetails fd
  LEFT JOIN srp_erp_ngo_com_familymaster fm ON fm.FamMasterID=fd.FamMasterID LEFT JOIN srp_erp_ngo_com_communitymaster cm ON cm.Com_MasterID=fd.Com_MasterID WHERE fd.companyID='" . $companyID . "' AND fd.FamMasterID='" . $rowPbene->FamMasterID . "' AND cm.isActive='1' AND fd.isMove='0'");
        $rowag = $queryFD->row();

        $zakatAnt1= $TotalPerZakat1*$rowag->smal;

        $zakatAnt2=$TotalPerZakat2*$rowag->medi;

        $zakatAnt3=$TotalPerZakat3*$rowag->larg;

        $zakatAnt=($zakatAnt1+$zakatAnt2+$zakatAnt3);

        echo json_encode($zakatAnt);
    }

    function update_beneficiary_edit()
    {

        $this->form_validation->set_rules("edit_beneEcState", 'Economic Status', 'trim|required');
        $this->form_validation->set_rules("edit_beneTotZakAmnt", 'Zakat Amount', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->update_beneficiary_edit());
        }
    }

    function fetch_zakatSet_edit()
    {
        $proposalZaqathSetID = trim($this->input->post('proposalZaqathSetID') ?? '');

        $data = $this->db->query("select proposalZaqathSetID, proposalID, zakEdit.EconStateID,EconStateDes, zakEdit.AgeGroupID,AgeGroup,AgeLimit, GrpPoints, ZakatAmount, TotalPerZakat, isConfirmed, isActive, companyID, companyCode FROM srp_erp_ngo_com_projectproposalzakatsetup zakEdit LEFT JOIN srp_erp_ngo_com_familyeconomicstatemaster ON srp_erp_ngo_com_familyeconomicstatemaster.EconStateID=zakEdit.EconStateID LEFT JOIN srp_erp_ngo_com_zakatagegroupmaster ON srp_erp_ngo_com_zakatagegroupmaster.AgeGroupID=zakEdit.AgeGroupID WHERE proposalZaqathSetID={$proposalZaqathSetID} ")->row_array();
        echo json_encode($data);
    }

    function update_zakatSet_edit()
    {

        $this->form_validation->set_rules("edit_EconStateID", 'Economic Status', 'trim|required');
        $this->form_validation->set_rules("edit_ZakAmount", 'Zakat Amount', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->update_zakatSet_edit());
        }
    }

    /* end of Zakat project proposal */

    function get_com_uploadsInfo(){

        $date_from = $this->input->post('dateUFrom');
        $date_To = $this->input->post('dateUTo');
        $ComUploadType = $this->input->post('ComUploadType');
        $isSubmited = $this->input->post('ComUploadSubmited');

        if ($ComUploadType == '0' || $ComUploadType == NULL || $ComUploadType == '') {
            $ComUploadTps = '';

        } else {
            $ComUploadTps = $ComUploadType;
        }

        if ($isSubmited == '0' || $isSubmited == NULL || $isSubmited == '') {
            $isSubmiteds = '';

        } else {
            $isSubmiteds = $isSubmited;
        }

        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $filter_ne = array("AND (srp_erp_ngo_com_uploads.ComUploadType=" . $ComUploadTps . ")" => $ComUploadTps,"AND (srp_erp_ngo_com_uploads.ComUploadSubmited=" . $isSubmiteds . ")" => $isSubmiteds);
        $set_filter_ne = array_filter($filter_ne);
        $where_notices = join(" ", array_keys($set_filter_ne));

        $data['uploadDate'] = $this->db->query("SELECT srp_erp_ngo_com_uploads.ComUploadID,familyUplaod, srp_erp_ngo_com_uploads.ComUploadType,(DATE_FORMAT(UploadPublishedDate,'{$convertFormat}'))as UploadPublishedDt,ComUploadSubject,ComUploadDescription, (DATE_FORMAT(ComUploadExpireDate,'{$convertFormat}'))as ComUploadExpireDate,  ComUploadSubmited FROM srp_erp_ngo_com_uploads WHERE srp_erp_ngo_com_uploads.companyID = {$companyID}  AND IF(familyUplaod=1 ,familyUplaod_active=1,familyUplaod_active=0) AND srp_erp_ngo_com_uploads.UploadPublishedDate >='".$date_from."' AND srp_erp_ngo_com_uploads.UploadPublishedDate <='".$date_To."' $where_notices GROUP BY srp_erp_ngo_com_uploads.UploadPublishedDate ORDER BY srp_erp_ngo_com_uploads.UploadPublishedDate DESC")->result_array();

        $this->load->view('system/communityNgo/ajax/load_community_uploads_view', $data);
    }

    public function saveUploadData_masCom()
    {
        $this->form_validation->set_rules('ComUploadType[]', 'Upload Type', 'trim|required');
        $this->form_validation->set_rules('ComUploadSubject[]', 'Subject', 'trim|required');
        $this->form_validation->set_rules('ComUpload_url[]', 'Page URL', 'trim|required');
        $this->form_validation->set_rules('ComUploader[]', 'Uploader Name', 'trim|required');
        $this->form_validation->set_rules('UploadPublishedDate[]', 'Publish Date', 'trim|required');
        $this->form_validation->set_rules('uploadGSDiviID[]', 'GS Divisions', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $date_format_policy = date_format_policy();
            $pubUpDate = input_format_date($this->input->post('UploadPublishedDate') , $date_format_policy);
            $expUpDate = input_format_date($this->input->post('ComUploadExpireDate') , $date_format_policy);

            if($pubUpDate < $expUpDate || $pubUpDate == $expUpDate){
            echo json_encode($this->CommunityNgo_model->saveUploadData_masCom());
            }
            else{
                echo json_encode(array('e', 'Upload file Expiry Date should be equal or less than Publish Date!'));
            }

        }
    }

    function delete_comUpload()
    {
        echo json_encode($this->CommunityNgo_model->delete_comUpload());
    }

    public function edit_comUpload(){

        $ComUploadID = $this->input->post('ComUploadID');

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->edit_comUpload($ComUploadID);
    }

    public function editGS_comUpload(){
        $ComUploadID = $this->input->post('ComUploadID');

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->editGS_comUpload($ComUploadID);
    }

    public function load_uploadVideo(){

        $ComUpload_url = $this->input->post('ComUpload_url');

        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i';

        if (preg_match($longUrlRegex, $ComUpload_url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($shortUrlRegex, $ComUpload_url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
        echo 'https://www.youtube.com/embed/' . $youtube_id ;

    }

    /* user uploads management */

    function get_user_uploadsInfo(){

        $date_from = $this->input->post('dateUsrFrom');
        $date_To = $this->input->post('dateUsrTo');
        $ComUploadType = $this->input->post('ComUpldUserType');
        $isSubmited = $this->input->post('ComUpldUserSubmited');

        if ($ComUploadType == '0' || $ComUploadType == NULL || $ComUploadType == '') {
            $ComUploadTps = '';

        } else {
            $ComUploadTps = $ComUploadType;
        }

        if ($isSubmited == '0' || $isSubmited == NULL || $isSubmited == '') {
            $isSubmiteds = '';

        } else {
            $isSubmiteds = $isSubmited;
        }

        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();

        $filter_ne = array("AND (srp_erp_ngo_com_uploads.ComUploadType=" . $ComUploadTps . ")" => $ComUploadTps,"AND (srp_erp_ngo_com_uploads.ComUploadSubmited=" . $isSubmiteds . ")" => $isSubmiteds);
        $set_filter_ne = array_filter($filter_ne);
        $where_notices = join(" ", array_keys($set_filter_ne));

        $data['userUploadByDate'] = $this->db->query("SELECT srp_erp_ngo_com_uploads.ComUploadID,familyUplaod, srp_erp_ngo_com_uploads.ComUploadType,(DATE_FORMAT(UploadPublishedDate,'{$convertFormat}'))as UploadPublishedDt,ComUploadSubject,ComUploadDescription, (DATE_FORMAT(ComUploadExpireDate,'{$convertFormat}'))as ComUploadExpireDate,  ComUploadSubmited FROM srp_erp_ngo_com_uploads WHERE srp_erp_ngo_com_uploads.companyID = {$companyID} AND familyUplaod='1' AND srp_erp_ngo_com_uploads.UploadPublishedDate >='".$date_from."' AND srp_erp_ngo_com_uploads.UploadPublishedDate <='".$date_To."' $where_notices GROUP BY srp_erp_ngo_com_uploads.UploadPublishedDate ORDER BY srp_erp_ngo_com_uploads.UploadPublishedDate DESC")->result_array();

        $this->load->view('system/communityNgo/ajax/load_community_user_uploads_view', $data);
    }

    public function save_user_UpldData_masCom()
    {
        $this->form_validation->set_rules('ComUploadType[]', 'Upload Type', 'trim|required');
        $this->form_validation->set_rules('ComUploadSubject[]', 'Subject', 'trim|required');
        $this->form_validation->set_rules('ComUpload_url[]', 'Page URL', 'trim|required');
        $this->form_validation->set_rules('ComUploader[]', 'Uploader Name', 'trim|required');
        $this->form_validation->set_rules('UploadPublishedDate[]', 'Publish Date', 'trim|required');
        $this->form_validation->set_rules('uploadGSDiviID[]', 'GS Divisions', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $date_format_policy = date_format_policy();
            $pubUpDate = input_format_date($this->input->post('UploadPublishedDate') , $date_format_policy);
            $expUpDate = input_format_date($this->input->post('ComUploadExpireDate') , $date_format_policy);

            if($pubUpDate < $expUpDate || $pubUpDate == $expUpDate){
                echo json_encode($this->CommunityNgo_model->save_user_UpldData_masCom());
            }
            else{
                echo json_encode(array('e', 'Upload file Expiry Date should be equal or less than Publish Date!'));
            }

        }
    }

    function delete_user_comUpload()
    {
        echo json_encode($this->CommunityNgo_model->delete_user_comUpload());
    }

    public function edit_user_comUpload(){

        $ComUploadID = $this->input->post('ComUploadID');

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->edit_user_comUpload($ComUploadID);
    }

    public function editGS_user_comUpload(){
        $ComUploadID = $this->input->post('ComUploadID');

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->editGS_user_comUpload($ComUploadID);
    }

    public function load_user_uploadVideo(){

        $ComUpload_url = $this->input->post('ComUpload_url');

        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i';

        if (preg_match($longUrlRegex, $ComUpload_url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($shortUrlRegex, $ComUpload_url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
        echo 'https://www.youtube.com/embed/' . $youtube_id ;

    }


    //community donors
    function load_com_donorManage_view()
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

        $data['header'] = $this->db->query("SELECT contactID,name,contactImage,email,CountryDes, CONCAT(phoneCountryCodePrimary,' - ',phoneAreaCodePrimary,phonePrimary) AS MasterPrimaryNumber FROM srp_erp_ngo_donors LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = srp_erp_ngo_donors.countryID WHERE $where GROUP BY srp_erp_ngo_donors.contactID ORDER BY contactID DESC ")->result_array();

        $this->load->view('system/communityNgo/ajax/load_com_ngo_donor_master', $data);
    }

    function delete_community_donor_mast()
    {
        echo json_encode($this->CommunityNgo_model->delete_community_donor_mast());
    }


    function save_com_donor_header()
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
            echo json_encode($this->CommunityNgo_model->save_com_donor_header());
        }
    }

    function load_com_donor_header()
    {
        echo json_encode($this->CommunityNgo_model->load_com_donor_header());
    }

    function load_comDonorManage_editView()
    {
        $convertFormat = convert_date_format_sql();
        $contactID = trim($this->input->post('contactID') ?? '');

        $data['header'] = $this->db->query("SELECT *,CountryDes,DATE_FORMAT(srp_erp_ngo_donors.createdDateTime,'" . $convertFormat . "') AS createdDate,DATE_FORMAT(srp_erp_ngo_donors.modifiedDateTime,'" . $convertFormat . "') AS modifydate,Com_MasterID,srp_erp_ngo_donors.createdUserName as contactCreadtedUser,srp_erp_ngo_donors.email as contactEmail,srp_erp_ngo_donors.fax as contactFax,srp_erp_ngo_donors.phonePrimary as contactPhonePrimary,srp_erp_ngo_donors.phoneSecondary as contactPhoneSecondary,srp_erp_statemaster.Description as province,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName FROM srp_erp_ngo_donors LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.countryID = srp_erp_ngo_donors.countryID LEFT JOIN srp_erp_statemaster ON srp_erp_statemaster.stateID = srp_erp_ngo_donors.state LEFT JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = srp_erp_ngo_donors.currencyID WHERE contactID = " . $contactID . "")->row_array();
        //echo $this->db->last_query();

        $this->load->view('system/communityNgo/ajax/load_com_ngo_donor_edit_view', $data);
    }

    function load_com_donor_all_notes()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $contactID = trim($this->input->post('contactID') ?? '');

        $where = "companyID = " . $companyID . " AND documentID = 1 AND documentAutoID = " . $contactID . "";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_notes');
        $this->db->where($where);
        $this->db->order_by('notesID', 'desc');
        $data['notes'] = $this->db->get()->result_array();
        $this->load->view('system/communityNgo/ajax/load_com_ngo_donor_notes', $data);
    }

    function add_com_donor_notes()
    {
        $this->form_validation->set_rules('contactID', 'Contact ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->add_com_donor_notes());
        }
    }

    function com_donor_image_upload()
    {
        $this->form_validation->set_rules('contactID', 'Donor ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->com_donor_image_upload());
        }
    }

    function delete_comDonor_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');

        $this->s3->delete('attachments/ngo/'.$myFileName);

     /*   $url = base_url("attachments/NGO");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {*/
            $this->db->delete('srp_erp_ngo_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(TRUE);
      //  }
    }

    function load_com_donor_all_attachment()
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
        $this->load->view('system/communityNgo/ajax/load_com_ngo_donors_attachments', $data);
    }

    function com_donor_donationsView()
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
	sum( cmd.transactionAmount ) AS commitmentTotal,
	sum( ifnull( collectionD.collectionAmount, 0 ) ) AS collectionAmount,
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

        $this->load->view('system/communityNgo/com_beneficiary_donations_view', $data);
    }
    //end of community donors

    function load_memberMovedHis()
    {

        $companyID = $this->common_data['company_data']['company_id'];

        $convertFormat = convert_date_format_sql();

        $Com_MasterIDs = $this->input->post('Com_MasterID');
        $FamMasterID = $this->input->post('FamMasterID');

        $data['memMovedHis'] = $this->db->query("SELECT srp_erp_ngo_com_familydetails.Com_MasterID,srp_erp_ngo_com_familydetails.FamMasterID,srp_erp_ngo_com_familymaster.FamilyName,CName_with_initials,CDOB,CFullName,DATE_FORMAT(FamMemAddedDate,'{$convertFormat}') AS FamMemAddedDate,name,relationship,CurrentStatus,isMove,srp_erp_ngo_com_communitymaster.isActive,DeactivatedFor,srp_erp_ngo_com_maritalstatus.maritalstatusID,srp_erp_ngo_com_maritalstatus.maritalstatus FROM srp_erp_ngo_com_familydetails INNER JOIN srp_erp_ngo_com_familymaster ON srp_erp_ngo_com_familymaster.FamMasterID=srp_erp_ngo_com_familydetails.FamMasterID INNER JOIN srp_erp_ngo_com_communitymaster ON srp_erp_ngo_com_communitymaster.Com_MasterID=srp_erp_ngo_com_familydetails.Com_MasterID LEFT JOIN srp_erp_gender ON srp_erp_gender.genderID=srp_erp_ngo_com_communitymaster.GenderID LEFT JOIN srp_erp_ngo_com_maritalstatus ON srp_erp_ngo_com_maritalstatus.maritalstatusID = srp_erp_ngo_com_communitymaster.CurrentStatus LEFT JOIN srp_erp_family_relationship ON srp_erp_family_relationship.relationshipID=srp_erp_ngo_com_familydetails.relationshipID WHERE srp_erp_ngo_com_familydetails.companyID='" . $companyID . "' AND srp_erp_ngo_com_familydetails.Com_MasterID='" . $Com_MasterIDs . "'")->result_array();

        $this->load->view('system/communityNgo/ajax/load_com_mem_movedHistory', $data);

    }

    //create com donors
    public function searchCommunity_donor()
    {

        $convertFormat = convert_date_format_sql();

        $Com_MasterID = $_POST['Com_MasterID'];

        $CI =& get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $querycOM1 = $CI->db->query("SELECT srp_erp_ngo_com_communitymaster.Com_MasterID,srp_erp_ngo_com_communitymaster.postalCode,srp_erp_statemaster.stateID,C_Address,srp_erp_statemaster.countyID FROM srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_communitymaster.RegionID = srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_communitymaster.companyID='{$companyID}' AND srp_erp_ngo_com_communitymaster.Com_MasterID = '$Com_MasterID' ");
        $rescoM1 = $querycOM1->row();

        $querycOM = $CI->db->query("SELECT Com_MasterID, IFNULL(CFullName, '') fullName,CName_with_initials,TitleID,srp_erp_ngo_com_communitymaster.postalCode,DATE_FORMAT(CDOB,'{$convertFormat}') AS CDOB,EmailID,CountryCodePrimary,AreaCodePrimary,TP_Mobile,CountryCodeSecondary,AreaCodeSecondary,TP_home,(srp_erp_statemaster.stateID) AS stateIDs,C_Address,srp_erp_statemaster.countyID,CNIC_No FROM srp_erp_ngo_com_communitymaster INNER JOIN srp_erp_statemaster ON srp_erp_ngo_com_communitymaster.RegionID = srp_erp_statemaster.stateID WHERE srp_erp_ngo_com_communitymaster.companyID='{$companyID}' AND srp_erp_ngo_com_communitymaster.Com_MasterID = '$Com_MasterID' ");
        $rescoM = $querycOM->result();

        $queryMas = $this->db->query("SELECT stateID,masterID FROM srp_erp_statemaster WHERE stateID ={$rescoM1->stateID} ");
        $rowMas = $queryMas->row();

        $queryMas2 = $this->db->query("SELECT stateID,masterID FROM srp_erp_statemaster WHERE stateID ={$rowMas->masterID} ");
        $rowMas2 = $queryMas2->row();

        $queryMas3 = $this->db->query("SELECT stateID,masterID FROM srp_erp_statemaster WHERE stateID ={$rowMas2->masterID} ");
        $rowMas3 = $queryMas3->row();

        $queryMas4 = $this->db->query("SELECT stateID FROM srp_erp_statemaster WHERE stateID ={$rowMas3->masterID} ");
        $rowMas4 = $queryMas4->row();

        foreach ($rescoM as $donorCom) {
            echo json_encode(
                array(

                    'EmailID' => $donorCom->EmailID,
                    'CountryCodePrimary' => $donorCom->CountryCodePrimary,
                    'AreaCodePrimary' => $donorCom->AreaCodePrimary,
                    'TP_home' => $donorCom->TP_home,
                    'nationalIdentityCardNo' => $donorCom->CNIC_No,
                    'CountryCodeSecondary' => $donorCom->CountryCodeSecondary,
                    'AreaCodeSecondary' => $donorCom->AreaCodeSecondary,
                    'TP_Mobile' => $donorCom->TP_Mobile,
                    'C_Address' => $donorCom->C_Address,
                    'countyID' => $donorCom->countyID,
                    'province' => $rowMas4->stateID,
                    'district' => $rowMas3->stateID,
                    'postalCode' => $donorCom->postalCode,

                )

            );
        }

    }

    /* end of moufi */

    /* starting */

    function Save_new_notice(){

        $this->form_validation->set_rules("NoticeTypeID", 'Notice Type', 'trim|required');
        $this->form_validation->set_rules("NoticePublishedDate", 'Published Date', 'trim|required');
        $this->form_validation->set_rules("noticestateID[]", 'DS Division', 'trim|required');
        $this->form_validation->set_rules("NoticeExpireDate", 'Announcement Expiry Date ', 'trim|required');

        $type = $this->input->post('NoticeTypeID');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $date_format_policy = date_format_policy();
            $pubDate = input_format_date($this->input->post('NoticePublishedDate') , $date_format_policy);
            $expDate = input_format_date($this->input->post('NoticeExpireDate') , $date_format_policy);

            if($pubDate < $expDate || $pubDate == $expDate){
                if ($type == 1) {

                    $this->form_validation->set_rules("DeadPerson", 'Dead Person', 'trim|required');
                    $this->form_validation->set_rules("deathInformer", 'Informer', 'trim|required');

                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->CommunityNgo_model->Save_new_notice());
                    }


                } else if ($type == 2) {

                    $this->form_validation->set_rules("bayanVenueDate", 'Venue Date', 'trim|required');
                    $this->form_validation->set_rules("VenuePlace", 'Venue Place', 'trim|required');
                    $this->form_validation->set_rules("Speaker", 'Speaker', 'trim|required');
                    $this->form_validation->set_rules("BayanSubject", 'Subject', 'trim|required');

                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->CommunityNgo_model->Save_new_notice());
                    }

                } else if ($type == 3) {

                    $this->form_validation->set_rules("NoticeSubject", 'Subject', 'trim|required');
                    $this->form_validation->set_rules("NoticeDescription", 'Description', 'trim|required');

                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors()));
                    } else {
                        echo json_encode($this->CommunityNgo_model->Save_new_notice());
                    }
                }
            } else {
                echo json_encode(array('e', 'Announcement Expiry Date should be equal or less than Publish Date!'));
            }
        }

    }

    function get_notice_announcement(){
        $data["output"] = $this->CommunityNgo_model->get_announcements();
        echo $html = $this->load->view('system/communityNgo/ajax/load_ngo_communityNoticeBoard', $data, true);
    }

    function load_noticeboard_attachments(){
        $companyid = $this->common_data['company_data']['company_id'];
        $vehicleMasterID = trim($this->input->post('NoticeID') ?? '');

        $where = "companyID = " . $companyid . " AND NoticeID = " . $vehicleMasterID . "";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_com_noticeboardattachments');
        $this->db->where($where);
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/communityNgo/ajax/load_NoticeBoardAttachments', $data);
    }

    function delete_noticeboard_attachments(){
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('attachmentFileName');
        $url = base_url("attachments/NGO/NoticeBoard");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {
            $this->db->delete('srp_erp_ngo_com_noticeboardattachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(TRUE);
        }
    }

    function editNoticeAnnouncement(){

        echo json_encode($this->CommunityNgo_model->editNoticeAnnouncement());

    }

    public function editNoticeAnnouncementGSDivision(){
        $id = $this->input->post('NoticeID');

        $this->load->model('CommunityNgo_model');
        $this->CommunityNgo_model->editNoticeAnnouncementGSDivision($id);
    }

    function delete_notice(){
        $this->form_validation->set_rules('NoticeID', 'Notice ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CommunityNgo_model->delete_notice());
        }
    }

    function ngo_NoticeBoardAttachment_upload(){
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('NoticeID', 'Notice Auto ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('NoticeID', trim($this->input->post('NoticeID') ?? ''));
            $num = $this->db->get('srp_erp_ngo_com_noticeboardattachments')->result_array();
            $file_name = 'Announcement_' . $this->input->post('NoticeID') . '_' . (count($num) + 1);

            $config['upload_path'] = realpath(APPPATH . '../attachments/NGO/NoticeBoard');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload("document_file")) {
                echo json_encode(array('status' => 0, 'type' => 'w',
                    'message' => 'Upload failed ' . $this->upload->display_errors()));
            } else {
                $upload_data = $this->upload->data();

              //  $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['NoticeID'] = trim($this->input->post('NoticeID') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');

                $docExpiryDate = trim($this->input->post('docExpiryDate') ?? '');
                $date_format_policy = date_format_policy();
                $data['docExpiryDate'] = (!empty($docExpiryDate)) ? input_format_date($docExpiryDate, $date_format_policy) : null;

                $data['attachmentFileName'] = $file_name . $upload_data["file_ext"];
                $data['fileType'] = trim($upload_data["file_ext"]);
                $data['fileSize'] = trim($upload_data["file_size"]);
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
                $this->db->insert('srp_erp_ngo_com_noticeboardattachments', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 0, 'type' => 'e',
                        'message' => 'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 1, 'type' => 's',
                        'message' => 'Successfully ' . trim($this->input->post('attachmentDescription') ?? '') . ' uploaded.'));
                }
            }
        }
    }
    /* end */

}

