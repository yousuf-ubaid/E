<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fleet extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Fleet_model');
        $this->load->helper('fleet_helper');
        $this->load->library('s3');
    }


    /* Master */
    function Save_vehicle()
    {
        $vehicleMasterID = $this->input->post('vehicleMasterID');
        $vehicaletype = $this->input->post('vehicle_type');
        $this->form_validation->set_rules('vehicalemodel', 'Vehicale Model', 'trim|required');
        $this->form_validation->set_rules('initialmileage', 'Initial Mileage', 'trim|required');
        $this->form_validation->set_rules('active', 'Status', 'trim|required');
        if ($vehicaletype == 2) {
            $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->Save_New_Vehicle());
        }
    }

    function CreateNewBrand()
    {
        $this->form_validation->set_rules('brand_description', 'Brand Description is needed', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->CreateNewBrand());
        }
    }

    function CreateNewModel()
    {
        $this->form_validation->set_rules('brandID', 'Brand Description is needed', 'trim|required');
        $this->form_validation->set_rules('model_description', 'Model Description is needed', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->CreateNewModel());
        }
    }

    function load_vehicle()
    {
        echo json_encode($this->Fleet_model->load_vehicle());
    }
    function load_assets()
    {
        echo json_encode($this->Fleet_model->load_assets());
    }
    function fetch_vehicles()
    {
        $this->datatables->select('vehicleMasterID,vehDescription,vehicleCode,VehicleNo,bodyType_description,brand_description,engineCapacity,fuel_type_description,expKMperLiter,isActive,confirmedYN,model_description', false)
            ->from('fleet_vehiclemaster');
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('asset_type_id', null);
        $this->datatables->add_column('isActive', '$1', 'vehicle_active_status(isActive)');
        $this->datatables->add_column('action', '$1', 'load_vehicle_master_action(vehicleMasterID, VehicleNo,vehicleCode,confirmedYN)');
        echo $this->datatables->generate();
    }


    function delete_vehicleMaster()
    {
        $this->form_validation->set_rules('vehicleMasterID', 'vehicleMasterID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->delete_vehicle());
        }
    }

    function delete_assetMaster()
    {
        $this->form_validation->set_rules('vehicleMasterID', 'vehicleMasterID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->delete_asset());
        }
    }
    function delete_components()
    {

        echo json_encode($this->Fleet_model->delete_component());
        // Perform deletion directly without form validation


    }
    function delete_inventory()
    {

        echo json_encode($this->Fleet_model->delete_inventory());
        // Perform deletion directly without form validation


    }


    function fetch_drivers()
    {
        $this->datatables->select('driverMasID,driverCode,driverName,drivPhoneNo,drivAddress,licenceNo,isActive', false)
            ->from('fleet_drivermaster');
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('isActive', '$1', 'driver_active_status(isActive)');
        $this->datatables->add_column('action', '$1', 'load_driver_master_action(driverMasID, driverName)');
        echo $this->datatables->generate();
    }

    function load_driver()
    {
        echo json_encode($this->Fleet_model->load_driver());
    }

    function Save_New_Driver()
    {
        $this->form_validation->set_rules('employeeName', 'Driver Name', 'required');
        $this->form_validation->set_rules('drivPhoneNo', 'Driver Phone No', 'required');
        $this->form_validation->set_rules('drivAddress', 'Driver Address', 'required');
        $this->form_validation->set_rules('licenceNo', 'Licence No', 'required');
        $this->form_validation->set_rules('liceExpireDate', 'Licence Expiry Date', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->Save_New_Driver());
        }
    }

    function delete_driverMaster()
    {
        $this->form_validation->set_rules('driverMasID', 'driverMasID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->delete_driver());
        }
    }

    function load_vehicleDetailsView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $vehicleMasterID = trim($this->input->post('vehicleMasterID') ?? '');

        $data['master'] = $this->db->query("SELECT * FROM fleet_vehiclemaster WHERE fleet_vehiclemaster.companyID = $companyID  AND fleet_vehiclemaster.vehicleMasterID = $vehicleMasterID ")->row_array();
        $this->load->view('system/Fleet_Management/ajax/load_fleet_vehicleImage', $data);
    }


    function ngo_Vehicleattachement_upload()
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
            $num = $this->db->get('fleet_attachments')->result_array();

            $fileName = current_companyCode() . '/' . current_companyCode() . '_Vehicle_' . $this->input->post('documentAutoID') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $file = $_FILES['document_file'];
            if ($file['error'] == 1) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' => 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).']));
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if (!in_array($ext, $allowed_types)) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' =>  "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if ($size > 5) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' => "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]));
            }

            $path = "attachments/FLEET/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' => 'Error in document upload location configuration']));
            }

            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');

            $docExpiryDate = trim($this->input->post('docExpiryDate') ?? '');
            $date_format_policy = date_format_policy();
            $data['docExpiryDate'] = (!empty($docExpiryDate)) ? input_format_date($docExpiryDate, $date_format_policy) : null;

            $data['myFileName'] = $fileName . '.' . $ext;
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
            $this->db->insert('fleet_attachments', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array(
                    'status' => 0,
                    'type' => 'e',
                    'message' => 'Upload failed ' . $this->db->_error_message()
                ));
            } else {
                $this->db->trans_commit();
                echo json_encode(array(
                    'status' => 1,
                    'type' => 's',
                    'message' => 'Successfully ' . trim($this->input->post('attachmentDescription') ?? '') . ' uploaded.'
                ));
            }


            /*$config['upload_path'] = realpath(APPPATH . '../attachments/FLEET');
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
                $this->db->insert('fleet_attachments', $data);
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
            }*/
        }
    }

    function load_vehicle_all_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $vehicleMasterID = trim($this->input->post('vehicleMasterID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = '7' AND documentAutoID = " . $vehicleMasterID . "";
        $this->db->select('*');
        $this->db->from('fleet_attachments');
        $this->db->where($where);
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/Fleet_Management/ajax/load_attachments', $data);
    }

    function delete_vehicle_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        /* $url = base_url("attachments/FLEET");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {
            $this->db->delete('fleet_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(TRUE);
        }*/
        $result = $this->s3->delete('attachments/FLEET/' . $myFileName);
        if ($result) {
            $this->db->delete('fleet_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    function vehicle_image_upload()
    {
        $this->form_validation->set_rules('vehicleID', 'Vehicle ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->vehicle_image_upload());
        }
    }

    function delete_vehicle_image()
    {
        $memberID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("uploads/Fleet/VehicleImg");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {
            $upData = array(
                'vehicleImage' => '',
            );
            $this->db->where('vehicleMasterID', $memberID)->update('fleet_vehiclemaster', $upData);
            echo json_encode(TRUE);
        }
    }

    function load_driverDetailsView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $driverMasID = trim($this->input->post('driverMasID') ?? '');

        $where = "fleet_drivermaster.companyID = " . $companyID . "  ";

        $data['master'] = $this->db->query("SELECT drivermaster.*,blood.BloodDescription FROM fleet_drivermaster drivermaster LEFT JOIN srp_erp_bloodgrouptype blood on BloodTypeID =  drivermaster.bloodGroup
WHERE drivermaster.companyID = $companyID AND drivermaster.driverMasID = $driverMasID")->row_array();
        $this->load->view('system/Fleet_Management/ajax/load_fleet_driverImage', $data);
    }

    function load_all_driver_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $driverMasID = trim($this->input->post('driverMasID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = '7' AND documentAutoID = " . $driverMasID . "";
        $this->db->select('*');
        $this->db->from('fleet_attachments');
        $this->db->where($where);
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/Fleet_Management/ajax/load_attachments', $data);
    }

    function ngo_driverattachement_upload()
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
            $num = $this->db->get('fleet_attachments')->result_array();
            $fileName = current_companyCode() . '/' . current_companyCode() . '_Driver_' . $this->input->post('documentAutoID') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $file = $_FILES['document_file'];
            if ($file['error'] == 1) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' => 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).']));
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if (!in_array($ext, $allowed_types)) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' =>  "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if ($size > 5) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' => "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]));
            }

            $path = "attachments/FLEET/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' => 'Error in document upload location configuration']));
            }
            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');

            $docExpiryDate = trim($this->input->post('docExpiryDate') ?? '');
            $date_format_policy = date_format_policy();
            $data['docExpiryDate'] = (!empty($docExpiryDate)) ? input_format_date($docExpiryDate, $date_format_policy) : null;

            $data['myFileName'] = $fileName . '.' . $ext;
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
            $this->db->insert('fleet_attachments', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array(
                    'status' => 0,
                    'type' => 'e',
                    'message' => 'Upload failed ' . $this->db->_error_message()
                ));
            } else {
                $this->db->trans_commit();
                echo json_encode(array(
                    'status' => 1,
                    'type' => 's',
                    'message' => 'Successfully ' . trim($this->input->post('attachmentDescription') ?? '') . ' uploaded.'
                ));
            }
            /* $file_name = 'Driver_' . $this->input->post('documentAutoID') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments/FLEET');
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


            }*/
        }
    }

    function delete_driver_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');

        /*   $url = base_url("attachments/FLEET");
        $link = "$url/$myFileName";*/
        if (!($this->s3->delete('attachments/FLEET/' . $myFileName))) {
            echo json_encode(FALSE);
        } else {
            $this->db->delete('fleet_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(TRUE);
        }
    }


    /*  Transaction */

    function save_fuelusage_header()
    {

        $date_format_policy = date_format_policy();
        $documentDate = $this->input->post('documentDate');
        $formatted_documentDate = input_format_date($documentDate, $date_format_policy);


        $this->form_validation->set_rules('documentDate', 'documentDate', 'required');
        $this->form_validation->set_rules('segment', 'segment', 'required');
        $this->form_validation->set_rules('supplierAutoID', 'supplierAuto ID ', 'required');
        $this->form_validation->set_rules('companyFinanceYearID', 'companyFinanceYear ID', 'required');
        $this->form_validation->set_rules('financeyear_period', 'financeyear_period', 'required');
        $this->form_validation->set_rules('transactionCurrencyID', 'transactionCurrency ID', 'required');
        $this->form_validation->set_rules('narration', 'narration', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($formatted_documentDate >= $financePeriod['dateFrom'] && $formatted_documentDate <= $financePeriod['dateTo']) {
                echo json_encode($this->Fleet_model->save_fuelusage());
            } else {
                echo json_encode(array('e', 'Document Date is not between Financial period !'));
            }
        }
    }

    function fetch_supplier_detail()
    {
        echo json_encode($this->Fleet_model->fetch_supplier_detail());
    }

    function fetch_fuelType()
    {
        echo json_encode($this->Fleet_model->fetch_fuelType());
    }

    function fetch_employee_detail()
    {
        echo json_encode($this->Fleet_model->fetch_employee_detail());
    }

    function fetch_fuel_usage_tble()
    {
        $this->datatables->select("fleet_fuelusagemaster.fuelusageID as fuelusageID,fleet_fuelusagemaster.transactionCurrency as transactionCurrency,fleet_fuelusagemaster.createdUserID as createdUserID,documentDate, approvedYN,documentCode,isDeleted, confirmedYN, narration ,transactionCurrency,transactionCurrencyShortCode, supplier,createdUserID,fleet_fuelusagemaster.transactionAmount,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount");
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,fuelusageID FROM fleet_fuelusagedetails GROUP BY fuelusageID) det', '(det.fuelusageID = fleet_fuelusagemaster.fuelusageID)', 'left');
        $this->datatables->from('fleet_fuelusagemaster');
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"FU",fuelusageID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN, "FU", fuelusageID)');
        $this->datatables->add_column('action', '$1', 'load_fuel_usage_action(fuelusageID, confirmedYN, isDeleted,approvedYN,createdUserID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function save_fuel_usage_detail()
    {
        $searches = $this->input->post('gl_code');
        $fuelDetailID = $this->input->post('fuelusageDetailsID_edit');
        //$this->form_validation->set_rules('fuelusageDetailsID', 'fuelusageDetailsID', 'required');
        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("vehicleMasterID[{$key}]", 'Vehicle Detail', 'required');
            $this->form_validation->set_rules("driverMasID[{$key}]", 'Driver', 'required');
            $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'required');
            $this->form_validation->set_rules("startKm[{$key}]", 'Start Km', 'required');
            $this->form_validation->set_rules("endKm[{$key}]", 'End Km', 'required');
            $this->form_validation->set_rules("FuelRate[{$key}]", 'Fuel Rate', 'required');
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'required');
            $this->form_validation->set_rules("receiptDate[{$key}]", 'Receipt Date ', 'required');
            $this->form_validation->set_rules("segment[{$key}]", 'Segment ', 'required');
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
            if (!empty($fuelDetailID)) {
                echo json_encode($this->Fleet_model->update_fuel_usage_detail());
            } else {
                echo json_encode($this->Fleet_model->save_fuel_usage_detail());
            }
        }
    }

    /*  function fetch_detail_table()
      {
          echo json_encode($this->Fleet_model->fetch_detail_table());
      }
  */
    function delete_fuelUsage_details()
    {
        echo json_encode($this->Fleet_model->delete_fuelUsage_details());
    }

    function delete_document()
    {
        $this->form_validation->set_rules('fuelusageID', 'fuelusageID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            //   echo json_encode($this->Fleet_modal->delete_document());
            echo json_encode($this->Fleet_model->delete_document());
        }
    }

    /*  function fetch_rent_item_details()
      {
          $id = $this->input->post('vehicleMasterID');
          $companyID = current_companyID();
          $data = $this->db->query("SELECT * FROM fleet_vehiclemaster WHERE vehicleMasterID ={$id} AND companyID = {$companyID}")->row_array();
          echo json_encode($data);
      }
  */

    function fetch_vehicledetails()
    {
        $id = $this->input->post('vehicleMasterID');
        $companyID = current_companyID();
        $data = $this->db->query("SELECT * FROM fleet_vehiclemaster WHERE vehicleMasterID ={$id} AND companyID = {$companyID}")->row_array();
        return $data;
    }

    function save_purchase_request_detail()
    {
        $fuelusageID = $this->input->post('fuelusageID');
        $vehicleMasterID = $this->input->post('vehicleMasterID');
        $this->form_validation->set_rules("$fuelusageID", 'fuelusageID', 'trim|required');
        $this->form_validation->set_rules("$vehicleMasterID", 'fuelusageID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->fleet_modal->save_purchase_request_detail());
        }
    }

    function fetch_fuel_usage_detail_table()
    {
        echo json_encode($this->Fleet_model->fetch_fuel_usage_detail_table());
    }

    function load_fuel_usage_header()
    {
        echo json_encode($this->Fleet_model->load_fuel_usage_header());
    }

    function fetch_fuelPurchased_edit()
    {
        echo json_encode($this->Fleet_model->fetch_fuelPurchased_edit());
    }

    function referback_FuelUsage()
    {
        $fuelusageID = $this->input->post('fuelusageID');

        $this->db->select('approvedYN,documentCode');
        $this->db->where('fuelusageID', trim($fuelusageID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('fleet_fuelusagemaster');
        $approved_purchase_request = $this->db->get()->row_array();
        if (!empty($approved_purchase_request)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_purchase_request['documentCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($fuelusageID, 'FU');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }
    }

    function load_fleet_fuel_comfirmation()
    {
        $fuelusageID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('fuelusageID') ?? '');
        $data['extra'] = $this->Fleet_model->fetch_template_data($fuelusageID);
        $data['approval'] = $this->input->post('approval');
        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }
        $html = $this->load->view('system/Fleet_Management/fleet_fuelUsage_transaction_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_fuelPurchase_request_header()
    {
        echo json_encode($this->Fleet_model->load_fuelPurchase_request_header());
    }

    function fetch_item_issue_detail_edit()
    {
        echo json_encode($this->Fleet_model->fetch_item_issue_detail_edit());
    }

    function fuel_usage_confirmation()
    {
        echo json_encode($this->Fleet_model->fuel_usage_confirmation());
    }

    function fetch_double_fuelusage()
    {
        $fuelusageID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('fuelusageID') ?? '');
        $code = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('code') ?? '');

        // $this->load->model('Double_entry_model');
        //$double_entry = $this->Double_entry_model->fetch_double_entry_customer_invoice_data($master_last_id, 'CINV');
        $data['extra'] = $this->Fleet_model->fetch_double_entry_fleet_fuel_usage_data($fuelusageID, $code);

        // $data['extra'] = $this->Fleet_model->fetch_double_entry_material_issue_data($fuelusageID, $code);
        $html = $this->load->view('system/double_entry/erp_double_entry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', 0);
        }
    }

    function fuel_usage_table_approval()
    {
        // $companyID = current_companyID();
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select("documentApprovedID,supplierName,approvedYN,approvalLevelID, fuelusageID,confirmedYN,documentCode,documentDate,documentID,supplierAutoID,referenceNumber,transactionAmount,transactionCurrency");
        $this->datatables->from("(SELECT documentApprovedID,fleet_fuelusagemaster.approvedYN,approvalLevelID, fleet_fuelusagemaster.documentID, confirmedYN, fleet_fuelusagemaster.fuelusageID, fleet_fuelusagemaster.supplierAutoID, fleet_fuelusagemaster.documentCode, fleet_fuelusagemaster.documentDate, referenceNumber, transactionCurrency, FORMAT(IFNULL(fleet_fuelusagedetails.transactionAmount, 0),transactionCurrencyDecimalPlaces ) as transactionAmount, supplierName FROM fleet_fuelusagemaster LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = fleet_fuelusagemaster.supplierAutoID LEFT JOIN ( SELECT sum(fleet_fuelusagedetails.totalAmount) AS transactionAmount, fuelusageID FROM fleet_fuelusagedetails GROUP BY fuelusageID ) fleet_fuelusagedetails ON fleet_fuelusagemaster.fuelusageID = fleet_fuelusagedetails.fuelusageID LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = fleet_fuelusagemaster.fuelusageID AND approvalLevelID = currentLevelNo LEFT JOIN srp_erp_approvalusers ON levelNo = fleet_fuelusagemaster.currentLevelNo WHERE isDeleted != 1 AND srp_erp_documentapproved.documentID = 'FU' AND srp_erp_approvalusers.documentID = 'FU' AND employeeID = '{$this->common_data['current_userID']}' AND fleet_fuelusagemaster.approvedYN={$this->input->post('approvedYN')} AND fleet_fuelusagemaster.companyID={$companyID} ORDER BY fuelusageID DESC )t");
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "FU", fuelusageID)');
        $this->datatables->add_column('level', 'Level   $1', 'approvalLevelID');
        $this->datatables->add_column(
            'details',
            '<b>Supplier Name : </b> $1 <b> <br>Total Amount : </b> $2  &nbsp;  </b> $3',
            'supplierName,transactionCurrency,transactionAmount'
        );
        $this->datatables->add_column(
            'edit',
            '$1',
            'fuel_usage_approval_action(fuelusageID,approvalLevelID,approvedYN,documentApprovedID,documentID)'
        );

        echo $this->datatables->generate();
    }

    function save_fuel_usage_approval()
    {
        $system_code = trim($this->input->post('fuelusageID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'FU', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('fuelusageID');
                $this->db->where('fuelusageID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('fleet_fuelusagemaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('fuelusageID', 'Fuel Usage ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Fleet_model->save_fuel_usage_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('fuelusageID');
            $this->db->where('fuelusageID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('fleet_fuelusagemaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'FU', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Donor Collection Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('fuelusageID', 'Fuel Usage ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Fleet_model->save_fuel_usage_approval());
                    }
                }
            }
        }
    }

    function fetch_fuelMaster()
    {
        $this->datatables->select('fuelTypeID,description as description, srp_erp_unit_of_measure.UnitDes as UoM,fuelRate as fuelRate,ROUND(fuelRate, 2) as fuelRate_search,CurrencyDecimalPlaces as CurrencyDecimalPlaces');
        $this->datatables->join('srp_erp_unit_of_measure', 'fleet_fuel_type.uomid = srp_erp_unit_of_measure.UnitID', 'left');
        $this->datatables->from('fleet_fuel_type');
        $this->datatables->add_column('fuel_price', '<div class="pull-right"> $1 </div>', 'format_number(fuelRate,CurrencyDecimalPlaces)');
        $this->datatables->add_column('action', '$1', 'action_fuelMaster(fuelTypeID)');
        echo $this->datatables->generate();
    }

    function SaveNewFuel_Master()
    {
        // $this->form_validation->set_rules('fuelType', 'Fuel Description', 'trim|required');

        $this->form_validation->set_rules("fuelType", 'Fuel Description', 'required');

        $fuelType = $this->input->post('fuelType');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $this->db->select('description');
            $this->db->where('description', trim($fuelType));
            $this->db->from('fleet_fuel_type');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Fuel Type already exist');
                echo json_encode(FALSE);
            } else {
                echo json_encode($this->Fleet_model->SaveNewFuel_Master());
            }
        }
    }

    function EditNewFuel_Master()
    {
        $this->form_validation->set_rules('fuelTypeID_edit', 'Fuel ID', 'trim|required');
        $this->form_validation->set_rules('fuelType_edit', 'Fuel Description', 'trim|required');
        $this->form_validation->set_rules('UOMid', 'Unit of Mesure', 'trim|required');
        $fuelType = $this->input->post('fuelType_edit');
        $fuelTypeID = $this->input->post('fuelTypeID_edit');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
            /*     } else {
                     $this->db->select('description');
                     $this->db->where('description', trim($fuelType) );
                  //   $this->db->where('description', trim($fuelTypeID) );
                     $this->db->from('fleet_fuel_type');
                     $po_approved = $this->db->get()->row_array();
                     if (!empty($po_approved)) {
                         $this->session->set_flashdata($msgtype = 'w', 'Fuel Type already exist');
                         echo json_encode(FALSE);
                 */
        } else {
            echo json_encode($this->Fleet_model->EditNewFuel_Master());
            //     }
        }
    }

    function edit_fuel()
    {
        $id = $this->input->post('fuelTypeID');
        // $companyID = current_companyID();
        $data = $this->db->query("SELECT * FROM fleet_fuel_type WHERE fuelTypeID ={$id}")->row_array();
        echo json_encode($data);
    }

    function delete_fuel()
    {
        $this->form_validation->set_rules('fuelTypeID', 'Fuel ID is Required', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->delete_fuel());
        }
    }


    /* ========== fuel usage Report =============== */

    function get_fuelusage_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert">
                Date To is required
            </div>';
        } else {
            $data["details"] = $this->Fleet_model->get_fuel_usage_report();
            $data["type"] = "html";
            echo $html = $this->load->view('system/Fleet_Management/Report/load_fuel_usage_report', $data, true);
        }
    }

    function get_fuel_usage_report_pdf()
    {
        $data["details"] = $this->Fleet_model->get_fuel_usage_report();

        //  var_dump($data['details']);

        $data["type"] = "pdf";
        $html = $this->load->view('system/Fleet_Management/Report/load_fuel_usage_report', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4-L');
    }

    function load_purchase_order_report()
    {
        $documentCode = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('documentCode') ?? '');
        $data['extra'] = $this->Fleet_model->fetch_report_view_data($documentCode);
        $data['approval'] = $this->input->post('approval');
        /*   if (!$this->input->post('html')) {
               $data['signature']=$this->Fleet_modal->fetch_signaturelevel();
           } else {
               $data['signature']='';
           }

   */
        $html = $this->load->view('system/Fleet_Management/fleet_fuelUsage_transaction_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }


    /* ========================== GL Configuration ============================ */

    function fetch_GL_config_table()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "companyID = " . $companyid . "";
        $this->datatables->select("fleet_glconfiguration.glConfigAutoID as glConfigAutoID, glConfigDescription as glConfigDescription,glCode as glCode, glCodeDescription as glCodeDescription");
        $this->datatables->from('fleet_glconfiguration');
        $this->datatables->add_column('Ec_detail', ' $1 - $2 ', 'glCode,glCodeDescription');
        $this->datatables->where($where);
        $this->datatables->add_column('edit', '$1', 'load_gl_config_table_action(glConfigAutoID)');
        echo $this->datatables->generate();
    }

    function save_new_GL_config()
    {
        $this->form_validation->set_rules('glConfigDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('glAutoID', 'GL Code', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->save_new_GL_config());
        }
    }

    function deleteGLconfig()
    {
        echo json_encode($this->Fleet_model->deleteGLconfig());
    }

    function editGLconfig()
    {
        echo json_encode($this->Fleet_model->editGLconfig());
    }

    function fetch_vehical_model_all()
    {
        $data_arr = array();
        $vehicalbrandid = $this->input->post('VehicleBrand');
        $companyID = $this->common_data['company_data']['company_id'];
        if (!empty($vehicalbrandid)) {
            $vehicalmodel = "SELECT modelID,description FROM fleet_brand_model WHERE brandID = {$vehicalbrandid}";
            $model = $this->db->query($vehicalmodel)->result_array();
            $data_arr = array('' => 'Select Model');
            if (!empty($model)) {
                foreach ($model as $row) {
                    $data_arr[trim($row['modelID'] ?? '')] = trim($row['description'] ?? '');
                }
            }
        }

        echo form_dropdown('vehicalemodel', $data_arr, '', 'class="form-control select2" id="vehicalemodel"');
    }

    function getSub()
    {
        $masterCat = trim($this->input->post('VehicleBrand') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $Categories = fa_asset_sub($masterCat);
        if ($status == 'true') {
            $option = '';
        } else {
            $option = '<option></option>';
        }
        // $option = '<option></option>';
        foreach ($Categories as $key => $Categoriy) {
            $option .= "<option value='{$key}'>{$Categoriy}</option>";
        }
        echo $option;
    }

    function getSubCategory()
    {
        $masterCat = trim($this->input->post('VehicleBrand') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $Categories = fa_asset_category_sub($masterCat);
        if ($status == 'true') {
            $option = '';
        } else {
            $option = '<option></option>';
        }
        // $option = '<option></option>';
        foreach ($Categories as $key => $Categoriy) {
            $option .= "<option value='{$key}'>{$Categoriy}</option>";
        }
        echo $option;
    }
    function fetch_sub_all()
    {
        $data_arr = array();
        $vehicalbrandid = $this->input->post('VehicleBrand');
        $companyID = $this->common_data['company_data']['company_id'];
        if (!empty($vehicalbrandid)) {
            $vehicalmodel = "SELECT modelID,description FROM fleet_brand_model WHERE brandID = {$vehicalbrandid}";
            $model = $this->db->query($vehicalmodel)->result_array();
            $data_arr = array('' => 'Select Sub Category');
            if (!empty($model)) {
                foreach ($model as $row) {
                    $data_arr[trim($row['modelID'] ?? '')] = trim($row['description'] ?? '');
                }
            }
        }

        echo form_dropdown('vehicalemodel', $data_arr, '', 'class="form-control select2" id="vehicalemodel"');
    }
    public function add_new_model()
    {
        $this->form_validation->set_rules('brand', 'Brand', 'trim|required');
        $this->form_validation->set_rules('Model', 'Model', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->add_new_model());
        }
    }
    public function asset_add_new_model()
    {
        $this->form_validation->set_rules('brand', 'Brand', 'trim|required');
        $this->form_validation->set_rules('Model', 'Model', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->asset_add_new_model());
        }
    }

    public function add_new_model_brand()
    {
        $this->form_validation->set_rules('brand', 'Brand', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->add_new_model_brand());
        }
    }
    public function asset_add_new_model_brand()
    {
        $this->form_validation->set_rules('brand', 'Brand', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->asset_add_new_model_brand());
        }
    }

    public function re_open_fuel_usage()
    {

        echo json_encode($this->Fleet_model->re_open_fuel_usage());
    }

    function vehicale_confirmation()
    {
        echo json_encode($this->Fleet_model->vehicale_confirmation());
    }

    function vehiclemaintenance_masterview()
    {
        $companyid = current_companyID();
        $search = $this->input->post('q');
        $convertFormat = convert_date_format_sql();

        $search_string = '';
        if (isset($search) && !empty($search)) {
            $search_string = " AND ((brand_description Like '%" . $search . "%') OR (model_description Like '%" . $search . "%')  OR (bodyType_description Like '%" . $search . "%') OR (colour_description Like '%" . $search . "%') OR (transmisson_description Like '%" . $search . "%') OR (fuel_type_description Like '%" . $search . "%') OR (manufacturedYear Like '%" . $search . "%') OR (model_description Like '%" . $search . "%') OR (vehDescription Like '%" . $search . "%') OR (VehicleNo Like '%" . $search . "%') )";
        }

        $data['master'] = $this->db->query("SELECT 
mastertbl.*,
IFNULL(DATEDIFF( fleetdetail.nextMaintenanceDate,CURDATE()),0)  AS duration,
DATE_FORMAT(fleetdetail.nextMaintenanceDate,' $convertFormat ') AS nextMaintenanceDatecon,
fleetdetail.lastMaintenanceOnKM as lastMaintenanceOnKM,
fleetdetail.nextMaintenanceONKM as nextMaintenanceONKM,
maxmeterreadin.maximumcurrentreading as maximumcurrentreading,
(fleetdetail.nextMaintenanceONKM  - maxmeterreadin.maximumcurrentreading) as exeedkm

FROM
	fleet_vehiclemaster mastertbl
	LEFT JOIN ( SELECT MAX( nextMaintenanceDate ) AS nextMaintenanceDate, vehicleMasterID, lastMaintenanceOnKM, MAX( nextMaintenanceONKM ) as nextMaintenanceONKM  FROM fleet_maintenancemaster GROUP BY vehicleMasterID ) fleetdetail ON fleetdetail.vehicleMasterID = mastertbl.vehicleMasterID 
	LEFT JOIN (SELECT MAX(current_meter_reading) as maximumcurrentreading,vehicleMasterID FROM fleet_meter_reading fleetdetail GROUP BY
	vehicleMasterID) maxmeterreadin on maxmeterreadin.vehicleMasterID = mastertbl.vehicleMasterID 
WHERE
	companyID = $companyid 
	$search_string")->result_array();
        $this->load->view('system/Fleet_Management/ajax/vehiclemaintenance_master_view', $data);
    }

    function fetch_vehical_records()
    {
        echo json_encode($this->Fleet_model->fetchvehicalemasterdettails());
    }

    function load_vehicale_maintenace_detail_view()
    {
        $companyid = current_companyID();
        $vehicalemasterid = $this->input->post('vehicalemasterid');
        $convertFormat = convert_date_format_sql();
        $data['detail'] = $this->db->query("SELECT mastertbl.*,maintenacetype.type as maintenacetypedescription,suppliermaster.supplierName,DATE_FORMAT(nextMaintenanceDate,' $convertFormat ') AS nextMaintenanceDate,DATE_FORMAT(documentDate,' $convertFormat ') AS documentDatecon FROM fleet_maintenancemaster mastertbl left join fleet_maintenancetype maintenacetype on maintenacetype.maintenanceTypeID = mastertbl.maintenanceType LEFT JOIN srp_erp_suppliermaster suppliermaster on suppliermaster.supplierAutoID = mastertbl.maintenanceCompanyID where  mastertbl.companyID = $companyid AND vehicleMasterID = $vehicalemasterid ORDER BY maintenanceMasterID DESC ")->result_array();
        $this->load->view('system/Fleet_Management/ajax/vehicale_maintenace_detail', $data);
    }

    function save_vehicalemaintenanceheader()
    {
        $vehiclemasterid = $this->input->post('vehicalemasterid');
        $currentmeterreading = $this->input->post('currentmeterreading');
        $nextmaintenance = $this->input->post('nextmaintenance');
        $vehicalemaintenaceid = $this->input->post('vehicalemaintenaceid');
        $maintenacedoneby = $this->input->post('maintenancedoneby');
        $companyid = current_companyID();


        $this->form_validation->set_rules('maintenancetype', 'Maintenance Type', 'trim|required');
        $this->form_validation->set_rules('maintenancedatefrom', 'Maintenance Date From', 'trim|required');
        $this->form_validation->set_rules('maintenancedateto', 'Maintenance Date To', 'trim|required');
        $this->form_validation->set_rules('nextmaintenance', 'Next Maintenance KM', 'trim|required');
        $this->form_validation->set_rules('nextmaintenancedate', 'Next Maintenance Date', 'trim|required');
        $this->form_validation->set_rules('transactioncurrencyid', 'Currency', 'trim|required');
        $this->form_validation->set_rules('currentmeterreading', 'Current Meterreading KM', 'trim|required');
        $this->form_validation->set_rules('maintenancedoneby', 'Maintenance By', 'trim|required');
        if ($maintenacedoneby == 2) {
            $this->form_validation->set_rules('maintenancecompany', 'Maintenance Company', 'trim|required');
            $this->form_validation->set_rules('glcode', 'Gl Code ', 'trim|required');
            $this->form_validation->set_rules('segment', 'Segment ', 'trim|required');
        }
        if ($maintenacedoneby == 1) {
            $this->form_validation->set_rules('warehouse', 'Ware House', 'trim|required');
        }



        $meterreading = $data = $this->db->query("SELECT IFNULL(MAX(current_meter_reading ),vehicalemaster.initialMilage ) AS maximumcurrentreading FROM fleet_meter_reading masterreading inner join fleet_vehiclemaster vehicalemaster on vehicalemaster.vehicleMasterID = masterreading.vehicleMasterID WHERE masterreading.vehicleMasterID = $vehiclemasterid AND masterreading.companyID = $companyid")->row_array();

        $statusisexis = $this->db->query("select maintenanceMasterID from fleet_maintenancemaster where `status` !=3  AND vehicleMasterID = $vehiclemasterid AND companyID = $companyid ")->result_array();
        if (!empty($statusisexis) && empty($vehicalemaintenaceid)) {
            echo json_encode(array('e', 'There is unclosed maintenance is exist for this vehicle cannot create a maintenance'));
        } else {
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {
                if ($currentmeterreading >= $meterreading['maximumcurrentreading']) {
                    if ($currentmeterreading > $nextmaintenance) {
                        echo json_encode(array('e', 'Next maintenance km cannot be less than current meter reading'));
                    } else {
                        echo json_encode($this->Fleet_model->save_vehicalemaintenanceheader());
                    }
                } else {
                    echo json_encode(array('e', 'Current meter reading cannot be less than existing meter reading ' . $meterreading['maximumcurrentreading'] . ' KM !'));
                }
            }
        }
    }

    function save_maintenance_crew_det()
    {
        $type = $this->input->post('crewtype');
        foreach ($type as $key => $val) {
            $this->form_validation->set_rules("crewname[{$key}]", 'Name', 'trim|required');
            $this->form_validation->set_rules("crewtype[{$key}]", 'Crew Type', 'trim|required');
        }
        $this->form_validation->set_rules("maintananceMasterIDcrew", 'Maintenance ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Fleet_model->save_maintenance_crew_det());
        }
    }

    function fetch_vehicalemaintenace_header_details()
    {
        echo json_encode($this->Fleet_model->fetch_vehicalemaintenace_header_details());
    }

    function fetch_maintenace_crew_details()
    {
        $this->form_validation->set_rules('vehicalemaintenaceid', 'Maintenance ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->fetch_maintenace_crew_details());
        }
    }

    function maintenace_crew_detail_view()
    {
        $comapnyid = current_companyID();
        $maintenanceMasterID = $this->input->post('vehicalemaintenaceid');
        $data['maintenanceMasterID'] = $maintenanceMasterID;

        if (!empty($maintenanceMasterID)) {
            $data['detail'] = $this->db->query("SELECT crewmastertbl.*,crewtype.Description as crewDescription  FROM `fleet_maintenancecrewdetails` crewmastertbl LEFT JOIN fleet_maintenancecrewtype crewtype on crewtype.crewTypeID = crewmastertbl.typeID Where  companyID = $comapnyid  and maintenanceMasterID = $maintenanceMasterID")->result_array();
        }

        $this->load->view('system/Fleet_Management/ajax/view_maintenace_crew', $data);
    }

    function maintenace_crew_detail_view_status()
    {
        $comapnyid = current_companyID();
        $maintenanceMasterID = $this->input->post('vehicalemaintenaceid');
        $data['maintenanceMasterID'] = $maintenanceMasterID;

        if (!empty($maintenanceMasterID)) {
            $data['detail'] = $this->db->query("SELECT crewmastertbl.*,crewtype.Description as crewDescription  FROM `fleet_maintenancecrewdetails` crewmastertbl LEFT JOIN fleet_maintenancecrewtype crewtype on crewtype.crewTypeID = crewmastertbl.typeID Where  companyID = $comapnyid  and maintenanceMasterID = $maintenanceMasterID")->result_array();
        }

        $this->load->view('system/Fleet_Management/ajax/view_maintenace_crew_status', $data);
    }

    function delete_maintenace_crew()
    {
        echo json_encode($this->Fleet_model->delete_maintenace_crew());
    }

    function save_maintenance_details_det()
    {
        $maintenancecriteria = $this->input->post('maintenancecriteria');
        foreach ($maintenancecriteria as $key => $val) {
            $this->form_validation->set_rules("maintenancecriteria[{$key}]", 'Maintenance Criteria', 'trim|required');
            $this->form_validation->set_rules("Qty[{$key}]", 'Qty', 'trim|required');
            $this->form_validation->set_rules("total[{$key}]", 'Total', 'trim|required');
        }
        $this->form_validation->set_rules("maintananceMasterIDdetail", 'Maintenance ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Fleet_model->save_maintenace_details());
        }
    }

    function fetch_crew_membersdropdown()
    {
        $data_arr = array();
        $maintenacecrew = $this->input->post('vehicalemaintenaceid');
        $comapnyid = current_companyID();
        $province = $this->db->query("SELECT * from fleet_maintenancecrewdetails where  maintenanceMasterID = $maintenacecrew and companyID = $comapnyid ")->result_array();
        $data_arr = array('' => 'Select a Crew Member');
        if (!empty($province)) {
            foreach ($province as $row) {
                $data_arr[trim($row['maintenanceCrewID'] ?? '')] = trim($row['name'] ?? '');
            }
        }
        echo form_dropdown('crewmember[]', $data_arr, '', 'class="form-control select2" id="crewmember"');
    }

    function maintenace_detail_view_details()
    {
        $comapnyid = current_companyID();
        $maintenanceMasterID = $this->input->post('vehicalemaintenaceid');
        $data['maintenanceMasterID'] = $maintenanceMasterID;
        if (!empty($maintenanceMasterID)) {
            $data['detail'] = $this->db->query("SELECT	mastertbl.* ,maintenacemaster.maintenanceBy,maintenacemaster.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlacesmaster,maintenacemaster.maintenanceMasterID as maintenanceMasterIDmaster,criteria.maintenanceCriteria as maintenanceCriteriadescription,crewmaintenace.name as crewmembername FROM `fleet_maintenance_detail` mastertbl LEFT JOIN fleet_maintenance_criteria criteria on criteria.maintenanceCriteriaID = mastertbl.maintenanceCriteriaID LEFT JOIN fleet_maintenancecrewdetails  crewmaintenace on crewmaintenace.maintenanceCrewID = mastertbl.crewID LEFT JOIN fleet_maintenancemaster maintenacemaster ON maintenacemaster.maintenanceMasterID = mastertbl.maintenanceMasterID  where mastertbl.companyID = $comapnyid AND mastertbl.maintenanceMasterID = $maintenanceMasterID ORDER BY mastertbl.maintenanceDetailID ASC ")->result_array();
        }

        $this->load->view('system/Fleet_Management/ajax/view_maintenace_detail_view', $data);
    }

    function maintenace_detail_view_details_view()
    {
        $comapnyid = current_companyID();
        $maintenanceMasterID = $this->input->post('vehicalemaintenaceid');
        $data['maintenanceMasterID'] = $maintenanceMasterID;
        if (!empty($maintenanceMasterID)) {
            $data['detail'] = $this->db->query("SELECT	mastertbl.* ,maintenacemaster.maintenanceBy,criteria.maintenanceCriteria as maintenanceCriteriadescription,crewmaintenace.name as crewmembername FROM `fleet_maintenance_detail` mastertbl LEFT JOIN fleet_maintenance_criteria criteria on criteria.maintenanceCriteriaID = mastertbl.maintenanceCriteriaID LEFT JOIN fleet_maintenancecrewdetails  crewmaintenace on crewmaintenace.maintenanceCrewID = mastertbl.crewID LEFT JOIN fleet_maintenancemaster maintenacemaster ON maintenacemaster.maintenanceMasterID = mastertbl.maintenanceMasterID  where mastertbl.companyID = $comapnyid AND mastertbl.maintenanceMasterID = $maintenanceMasterID ORDER BY mastertbl.maintenanceDetailID ASC ")->result_array();
        }

        $this->load->view('system/Fleet_Management/ajax/view_maintenace_detail_view_status', $data);
    }

    function delete_maintenace_details()
    {
        echo json_encode($this->Fleet_model->delete_maintenace_details());
    }

    /* function load_maintenance_all_attachments()
     {
         $companyID = $this->common_data['company_data']['company_id'];
         $vehicalemaintenaceid = trim($this->input->post('vehicalemaintenaceid') ?? '');

         $where = "companyID = " . $companyID . " AND documentID = 1  AND documentAutoID = " . $vehicalemaintenaceid . "";
         $convertFormat = convert_date_format_sql();
         $this->db->select('*');
         $this->db->from('srp_erp_buyback_attachments');
         $this->db->where($where);
         $this->db->order_by('attachmentID', 'desc');
         $data['attachment'] = $this->db->get()->result_array();
         $this->load->view('system/buyback/ajax/load_farm_all_attachments', $data);
     }*/
    function fetch_maintenacedetails()
    {
        $this->form_validation->set_rules("maintenanceMasterID", 'Maintenance ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Fleet_model->fetch_maintenace_details());
        }
    }

    function update_is_doneyn_status()
    {
        $this->form_validation->set_rules('maintenanceDetailID', 'Maintenance Detail ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->update_is_doneyn_status());
        }
    }

    function update_doneyn_comment()
    {
        $this->form_validation->set_rules('maintenanceDetailID', 'Maintenance Detail ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->update_doneyn_comment());
        }
    }

    function maxmaintenacedetails()
    {
        $this->form_validation->set_rules('vehicalemasterid', 'Vehicle ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->maxmaintenacedetails());
        }
    }

    function fetch_maintenace_number()
    {
        echo json_encode($this->Fleet_model->fetch_maintenace_number());
    }

    function fech_maintenace_status_details()
    {
        echo json_encode($this->Fleet_model->fetch_maintenace_details_status());
    }

    function save_maintenace_status()

    {
        $date_format_policy = date_format_policy();
        $status = $this->input->post('statusmaintenace');
        $maintenacemasterid = $this->input->post('maintenacemasterid');
        $companyid = current_companyID();
        $this->form_validation->set_rules('statusmaintenace', 'Status', 'trim|required');
        $statusmaintenace = $this->db->query("select status,currentMeterReading from fleet_maintenancemaster where maintenanceMasterID = $maintenacemasterid")->row_array();
        $vehicleMasterID = $this->input->post('vehicalemasteridstatus');
        $meterreading = $data = $this->db->query("SELECT MAX(current_meter_reading) as maximumcurrentreading FROM `fleet_meter_reading` WHERE vehicleMasterID = $vehicleMasterID AND companyID = $companyid")->row_array();
        $documentDate = $this->input->post('stausdate');
        $formatted_documentDate = input_format_date($documentDate, $date_format_policy);

        $finaceper = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod  WHERE isActive = 1 AND companyID = $companyid AND '$formatted_documentDate' BETWEEN dateFrom AND dateTo ")->row_array();


        if ($statusmaintenace['currentMeterReading'] >= $meterreading['maximumcurrentreading']) {
            if ($status == 3) {
                $this->form_validation->set_rules('stausdate', 'Date', 'trim|required');
                $this->form_validation->set_rules('commentstauts', 'Comment', 'trim|required');
            } else {
                if ($status == 2) {
                    $this->form_validation->set_rules('stausdate', 'Date', 'trim|required');
                }
            }
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {

                if ($statusmaintenace['status'] == 2 && $status == 1) {
                    echo json_encode(array('e', 'Cannot change the on going status to not started!'));
                } else if ($statusmaintenace['status'] == 3 && $status == 2) {
                    echo json_encode(array('e', 'Cannot change the closed status to on going!'));
                } else if ($statusmaintenace['status'] == 3 && $status == 1) {
                    echo json_encode(array('e', 'Cannot change the closed status to not started !'));
                } else {
                    if ($status == 3) {
                        if ($formatted_documentDate >= $finaceper['dateFrom'] && $formatted_documentDate <= $finaceper['dateTo']) {
                            echo json_encode($this->Fleet_model->save_maintenace_status());
                        } else {
                            echo json_encode(array('e', 'Maintenance closing date is not between Financial period !'));
                        }
                    } else {
                        echo json_encode($this->Fleet_model->save_maintenace_status());
                    }
                }
            }
        } else {
            echo json_encode(array('e', 'Current meter reading cannot be less than existing meter reading ' . $meterreading['maximumcurrentreading'] . ' KM !'));
        }
    }

    function update_qty_maintenacedetails()
    {
        $this->form_validation->set_rules('maintenanceDetailID', 'Maintenance Detail ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->update_qty_maintenacedetails());
        }
    }

    function update_unitcost_maintenacedetails()
    {
        $this->form_validation->set_rules('maintenanceDetailID', 'Maintenance Detail ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->update_unitcost_maintenacedetails());
        }
    }

    function update_maintenacetypedes_maintenacedetails()
    {
        $this->form_validation->set_rules('maintenanceDetailID', 'Maintenance Detail ID', 'trim|required');
        $this->form_validation->set_rules('maintenacetype', 'Maintenance Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->update_maintenacetypedes_maintenacedetails());
        }
    }

    function fetch_vehicalemaintenace_meter_reading_details()
    {
        echo json_encode($this->Fleet_model->fetch_vehicalemaintenace_meter_reading_details());
    }

    function save_meter_reading()
    {
        $this->form_validation->set_rules('currentreading', 'Current Reading', 'trim|required');
        $this->form_validation->set_rules('vehiclemasterid', 'Vehicle ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->save_meter_reading());
        }
    }

    function load_vehicale_maintenace_meter_reading_view()
    {
        $companyid = current_companyID();
        $vehicalemasterid = $this->input->post('vehicalemasterid');
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("select * from fleet_vehiclemaster  where vehicleMasterID = $vehicalemasterid  AND companyID =$companyid ")->row_array();


        $data['detail'] = $this->db->query("select *,DATE_FORMAT(createDateTime, '$convertFormat' ) AS createDateTimeupcon from fleet_meter_reading meterreading where vehicleMasterID = $vehicalemasterid AND companyID = $companyid ")->result_array();
        $this->load->view('system/Fleet_Management/ajax/vehicale_meter_reading_detail', $data);
    }

    function nextmaintenacekm()
    {
        $this->form_validation->set_rules('vehicalemasterid', 'Vehicle ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->nextmaintenacekm());
        }
    }

    function attachments_vehicale_maintenace()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $vehicalemasterid = trim($this->input->post('vehicalemaintenaceid') ?? '');
        $data['vehicalemasterid'] = $vehicalemasterid;
        $where = "mastertbl.companyID = " . $companyid . " AND documentID = 'MNT'  AND documentSystemCode = " . $vehicalemasterid . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('mastertbl.*,mastermaintenace.`status`');
        $this->db->from('srp_erp_documentattachments mastertbl');
        $this->db->Join('fleet_maintenancemaster mastermaintenace', ' mastermaintenace.maintenanceMasterID = mastertbl.documentSystemCode', 'left');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();

        $data['status'] = $this->db->query("select  `status` from  fleet_maintenancemaster where  maintenanceMasterID = $vehicalemasterid  AND companyID = $companyid ")->row_array();

        $this->load->view('system/Fleet_Management/ajax/attachments_vehicale_maintenace', $data);
    }

    function attachement_upload()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        $this->form_validation->set_rules('documentAutoID', 'Document Auto ID', 'trim|required');

        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = $this->input->post('document_name') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments/FLEET/Maintenance');
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
                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentSystemCode'] = trim($this->input->post('documentAutoID') ?? '');
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
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_documentattachments', $data);
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

    function delete_maintenace_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments/FLEET/Maintenance");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(false);
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        }
    }

    function load_vehicale_maintenace_criteria()
    {
        $companyid = current_companyID();

        $status = trim($this->input->post('maintenacestatus') ?? '');
        $text = trim($this->input->post('q') ?? '');
        $search_string = '';
        $filter_status = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND ((maintenanceCriteria Like '%" . $text . "%')) ";
        }
        if (isset($status) && !empty($status)) {
            if ($status == 1) {
                $filter_status = " AND status = 1 ";
            } else if ($status == 2) {
                $filter_status = " AND status = 0 ";
            }
        }


        $where_admin = "WHERE companyID = " . $companyid . $search_string . $filter_status;

        $data['detail'] = $this->db->query("select * from fleet_maintenance_criteria $where_admin ORDER BY maintenanceCriteriaID DESC ")->result_array();

        $this->load->view('system/Fleet_Management/ajax/load_maintenace_crieria_master', $data);
    }

    function delete_maintenace_criteria()
    {
        echo json_encode($this->Fleet_model->delete_maintenace_criteria());
    }

    function maintenace_criteria()
    {
        $this->form_validation->set_rules('criteriadescription', 'Criteria Description', 'trim|required');
        $this->form_validation->set_rules('active', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->maintenace_criteria());
        }
    }

    function fetchmaintenacecriteria()
    {
        echo json_encode($this->Fleet_model->fetchmaintenacecriteria());
    }
    function fetch_spareparts()
    {
        echo json_encode($this->Fleet_model->fetch_spareparts());
    }
    function save_spareparts()
    {
        $search = $this->input->post('search');
        foreach ($search as $key => $val) {
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Qty', 'trim|required');
        }
        $this->form_validation->set_rules("maintenanceCriteriaID", 'Maintenance Criteria ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Fleet_model->save_spareparts());
        }
    }
    function  fetch_maintenace_criteriadet()
    {
        echo json_encode($this->Fleet_model->fetch_maintenace_criteriadet());
    }
    function maintenace_details_exist()
    {
        $vehicalemaintenaceid = trim($this->input->post('vehicalemaintenaceid') ?? '');
        $data = $this->db->query("select * from fleet_maintenance_detail WHERE maintenanceMasterID ={$vehicalemaintenaceid} ")->row_array();
        echo json_encode($data);
    }
    function save_spareparts_additional()
    {
        $search = $this->input->post('search');
        foreach ($search as $key => $val) {
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Qty', 'trim|required');
        }
        $this->form_validation->set_rules("maintenanceCriteriaID", 'Maintenance Criteria ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Fleet_model->save_spareparts_additional());
        }
    }
    function fetch_cost_sprare_parts()
    {
        echo json_encode($this->Fleet_model->fetch_cost_sprare_parts());
    }
    function update_crew_up_maintenacedetails()
    {
        $this->form_validation->set_rules('maintenanceDetailID', 'Maintenance Detail ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->update_crew_up_maintenacedetails());
        }
    }
    function Save_Asset()
    {
        $vehicaletype = $this->input->post('vehicle_type');

        if ($vehicaletype == 2) {
            $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
                return;
            }
        }

        // Save the asset regardless of vehicle type
        echo json_encode($this->Fleet_model->Save_New_Asset());
    }

    public function fetch_assets()
    {
        $companyid  = $this->common_data['company_data']['company_id'];
        $vehiclebrand = trim($this->input->post('vehicalebrand') ?? '');
        $vehicalemodel = trim($this->input->post('vehicalemodel') ?? '');
        $locationFilter = trim($this->input->post('locationFilter') ?? '');

        // if($vehicalbrandid)
        // {
        //     $status_filter = " AND fleet_vehiclemaster.brand_id = '.$vehiclebrand.' ";

        // }
        $status_filter = "";
        if ($vehiclebrand) {
            $status_filter .= " AND fleet_vehiclemaster.brand_id = '$vehiclebrand' ";
        }
        if ($vehicalemodel) {
            $status_filter .= " AND fleet_vehiclemaster.model_id = '$vehicalemodel' "; // Adjust based on your actual database structure
        }
        if ($locationFilter) {
            $status_filter .= " AND fleet_vehiclemaster.locationID = '$locationFilter' ";
        }


        $where = "fleet_vehiclemaster.companyID = " . $companyid . $status_filter . "";


        $this->datatables->select('
            fleet_vehiclemaster.vehicleMasterID as vehicleMasterID,
            fleet_vehiclemaster.vehDescription as vehDescription,
            fleet_vehiclemaster.brand_description as brand_description,
            fleet_vehiclemaster.asset_type_id as asset_type_id,
            fleet_vehiclemaster.locationID as locationID,
            fleet_vehiclemaster.assetStatus as assetStatus,
            fleet_vehiclemaster.vehicleCode as vehicleCode,
            fleet_vehiclemaster.assetSerialNo as assetSerialNo,
            fleet_vehiclemaster.isActive as isActive,
            fleet_vehiclemaster.confirmedYN as confirmedYN,
            fleet_vehiclemaster.model_description as model_description,
            srp_erp_location.locationName as locationName,
            fleet_asset_utilization_status.status as assetStatusDesc,
        ', false)
            ->from('fleet_vehiclemaster')
            ->join(
                'srp_erp_location',
                'srp_erp_location.locationID = fleet_vehiclemaster.locationID',
                'left'
            )
            ->join(
                'fleet_asset_utilization_status',
                'fleet_asset_utilization_status.id = fleet_vehiclemaster.assetStatus',
                'left'
            )
            // ->join(
            //     'fleet_brand_model',
            //     'fleet_brand_model.brandID = fleet_vehiclemaster.brand_id',
            //     'left'
            // )
            ->where($where)
            ->where_in('fleet_vehiclemaster.asset_type_id', array(1, 2));
        $this->datatables->add_column('isActive', '$1', 'vehicle_active_status(isActive)');
        $this->datatables->add_column('assetStatusDesc', '$1', 'load_assetstatus(assetStatus, assetStatusDesc)');
        $this->datatables->add_column('action', '$1', 'load_asset_master_action(vehicleMasterID, assetSerialNo, vehicleCode, confirmedYN)');

        echo $this->datatables->generate();
    }


    function load_assetDetailsView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $vehicleMasterID = trim($this->input->post('vehicleMasterID') ?? '');

        $data['master'] = $this->db->query("SELECT * FROM fleet_vehiclemaster LEFT JOIN srp_erp_location ON fleet_vehiclemaster.locationID = srp_erp_location.locationID WHERE fleet_vehiclemaster.companyID = $companyID  AND fleet_vehiclemaster.vehicleMasterID = $vehicleMasterID ")->row_array();
        $data['asset_type_id'] = $data['master']['asset_type_id']; // Fetching asset_type_id
        $this->load->view('system/Fleet_Management/ajax/load_fleet_assetImage', $data);
    }
    public function fetch_main_category()
    {

        $type_id = $this->input->post('sub_asset_type_id');
        log_message('debug', 'Asset type ID: ' . $type_id);
        $options = '';

        // Fetch the main categories based on the asset type id
        $main_categories = $this->Fleet_model->get_main_categories($type_id);
        log_message('debug', 'Main categories: ' . print_r($main_categories, true));

        foreach ($main_categories as $category) {
            $options .= '<option value="' . $category->brand_id . '">' . $category->brand_description . '</option>';
        }

        echo $options;
    }
    public function fetch_main_category2()
    {

        $type_id = $this->input->post('asset_type');
        log_message('debug', 'Asset type ID: ' . $type_id);
        $options = '';

        // Fetch the main categories based on the asset type id
        $main_categories = $this->Fleet_model->get_main_categories($type_id);
        log_message('debug', 'Main categories: ' . print_r($main_categories, true));

        foreach ($main_categories as $category) {
            $options .= '<option value="' . $category->brand_id . '">' . $category->brand_description . '</option>';
        }

        echo $options;
    }
    function ngo_Assetattachement_upload()
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
            $num = $this->db->get('fleet_attachments')->result_array();

            $fileName = current_companyCode() . '/' . current_companyCode() . '_Asset_' . $this->input->post('documentAutoID') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $file = $_FILES['document_file'];
            if ($file['error'] == 1) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' => 'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).']));
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if (!in_array($ext, $allowed_types)) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' =>  "The file type you are attempting to upload is not allowed. ( .{$ext} )"]));
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if ($size > 5) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' => "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]));
            }

            $path = "attachments/FLEET/$fileName.$ext";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                die(json_encode(['status' => 0, 'type' => 'e', 'message' => 'Error in document upload location configuration']));
            }

            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');

            $docExpiryDate = trim($this->input->post('docExpiryDate') ?? '');
            $date_format_policy = date_format_policy();
            $data['docExpiryDate'] = (!empty($docExpiryDate)) ? input_format_date($docExpiryDate, $date_format_policy) : null;

            $data['myFileName'] = $fileName . '.' . $ext;
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
            $this->db->insert('fleet_attachments', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array(
                    'status' => 0,
                    'type' => 'e',
                    'message' => 'Upload failed ' . $this->db->_error_message()
                ));
            } else {
                $this->db->trans_commit();
                echo json_encode(array(
                    'status' => 1,
                    'type' => 's',
                    'message' => 'Successfully ' . trim($this->input->post('attachmentDescription') ?? '') . ' uploaded.'
                ));
            }


            /*$config['upload_path'] = realpath(APPPATH . '../attachments/FLEET');
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
                $this->db->insert('fleet_attachments', $data);
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
            }*/
        }
    }

    function load_asset_all_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $vehicleMasterID = trim($this->input->post('vehicleMasterID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = '7' AND documentAutoID = " . $vehicleMasterID . "";
        $this->db->select('*');
        $this->db->from('fleet_attachments');
        $this->db->where($where);
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/Fleet_Management/ajax/asset_attatchments', $data);
    }

    public function load_asset_templates()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $vehicleMasterID = trim($this->input->post('vehicleMasterID') ?? '');

        // Fetch all templates
        $this->db->select('*');
        $this->db->from('srp_erp_inspection_template_master');
        $this->db->where('companyID', $companyid);
        $data['templates'] = $this->db->get()->result_array();

        // Fetch saved template IDs for the specific vehicleMasterID
        $this->db->select('inspectionTemplate');
        $this->db->from('fleet_vehiclemaster');
        $this->db->where('vehicleMasterID', $vehicleMasterID);
        $savedTemplates = $this->db->get()->row_array();

        if (!empty($savedTemplates['inspectionTemplate'])) {
            // Convert comma-separated string to an array of template IDs
            $data['selectedTemplates'] = explode(',', $savedTemplates['inspectionTemplate']);
        } else {
            $data['selectedTemplates'] = []; // No templates selected
        }

        $data['vehicleMasterID'] = $vehicleMasterID;
        $this->load->view('system/Fleet_Management/ajax/asset_templates', $data);
    }


    public function saveAssetTemplates()
    {
        $this->db->trans_start(); // Start the transaction

        $vehicleMasterID = $this->input->post('vehicleMasterID');
        $selectedTemplates = $this->input->post('templates'); // Array of selected template IDs

        if (!empty($vehicleMasterID) && !empty($selectedTemplates)) {
            // Convert the template array to a comma-separated string
            $templateData = implode(",", $selectedTemplates);

            // Update the fleet_vehiclemaster table with the selected templates for the vehicleMasterID
            $this->db->where('vehicleMasterID', $vehicleMasterID);
            $this->db->update('fleet_vehiclemaster', ['inspectionTemplate' => $templateData]);

            if ($this->db->affected_rows() > 0) {
                $this->db->trans_commit(); // Commit the transaction
                echo 'Templates saved successfully.'; // Success message
            } else {
                $this->db->trans_rollback(); // Rollback the transaction
                echo 'No changes made or templates already saved.'; // Failure message
            }
        } else {
            echo 'Please provide valid vehicle and templates data.'; // Invalid data message
            $this->db->trans_rollback(); // Rollback if data is invalid
        }

        $this->db->trans_complete(); // Complete the transaction
    }



    function delete_asset_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        /* $url = base_url("attachments/FLEET");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(FALSE);
        } else {
            $this->db->delete('fleet_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(TRUE);
        }*/
        $result = $this->s3->delete('attachments/FLEET/' . $myFileName);
        if ($result) {
            $this->db->delete('fleet_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    function asset_image_upload()
    {
        $this->form_validation->set_rules('vehicleID', 'Vehicle ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->asset_image_upload());
        }
    }
    public function get_fleet_data()
    {
        // Load the model (adjust the model name if necessary)
        $this->load->model('Fleet_model');

        // Call the model method to get fleet data
        $fleet_data = $this->Fleet_model->get_fleet_master();

        // Send fleet data to the view
        echo json_encode($fleet_data); // Assuming you want to return JSON
    }
    public function get_inventory_data()
    {
        // Load the model (adjust the model name if necessary)
        $this->load->model('Fleet_model');

        // Call the model method to get fleet data
        $sparedata = $this->Fleet_model->get_spare_master();

        // Send fleet data to the view
        echo json_encode($sparedata); // Assuming you want to return JSON
    }


    public function save_fleet_data()
    {
        $tableData = $this->input->post('tableData');
        $this->load->model('Fleet_model');
        $duplicateFound = false;
        $duplicateItems = [];

        foreach ($tableData as $data) {
            // Check if the record already exists
            $this->db->where('component_id', $data['itemCode']);
            $this->db->where('asset_id', $data['comasset']);
            $this->db->where('type', 1);
            $existingRecord = $this->db->get('srp_erp_fa_asset_componets_inventory')->row();

            // If no existing record, proceed with insertion
            if (!$existingRecord) {
                $insertData = [
                    'component_id' => $data['itemCode'],
                    'component_name' => $data['description'],
                    'part_number' => $data['serialNo'],
                    'manufacturer' => $data['manufacture'],
                    'type' => 1,
                    'asset_id' => $data['comasset'],
                    'company_id' => current_companyID(),
                    'company_code' => $this->common_data['company_data']['company_code'],
                    'timestamp' => date('Y-m-d H:i:s')
                ];
                $this->Fleet_model->save_fleet_data($insertData);
            } else {
                $duplicateFound = true;
                $duplicateItems[] = $data['description']; // Collect duplicate item codes for reference
            }
        }

        if ($duplicateFound) {
            echo json_encode([
                'status' => 'duplicate',
                'message' => 'Some records were duplicates and were not saved.',
                'duplicates' => $duplicateItems
            ]);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'All records saved successfully.']);
        }
    }


    public function save_spare_data()
    {
        $tableData = $this->input->post('spareData');
        $this->load->model('Fleet_model');
        $insertData = array();
        $duplicateFound = false;

        foreach ($tableData as $key => $data) {
            // Check if the record already exists
            $this->db->where('inventory_id', $data['itemCode']);
            $this->db->where('asset_id', $data['asset']);
            $this->db->where('type', 2); // Ensure type 2 for inventory
            $existingRecord = $this->db->get('srp_erp_fa_asset_componets_inventory')->row();

            // If no existing record, proceed with insertion
            if (!$existingRecord) {
                $data1['inventory_id'] = $data['itemCode'];
                $data1['inventory_name'] = $data['spareDescription'];
                $data1['part_number'] = $data['partNo'];
                $data1['type'] = 2;
                $data1['asset_id'] = $data['asset'];
                $data1['company_id'] = current_companyID();
                $data1['company_code'] = $this->common_data['company_data']['company_code'];
                $data1['timestamp'] = date('Y-m-d H:i:s');

                $this->Fleet_model->save_sp_data($data1);
            } else {
                $duplicateFound = true;
            }
        }

        if ($duplicateFound) {
            echo json_encode(['status' => 'duplicate', 'message' => 'Some records were duplicates and were not saved.']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'All records saved successfully.']);
        }
    }


    function fetch_veh_data()
    {
        $vehicleMasterID = $this->input->post('vehicleMasterID');
        // $vehicaletype = $this->input->post('vehicle_type');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->fetch_veh_data());
        }
    }
    public function display_components()
    {
        // Load the Fleet_model if not already loaded
        $this->load->model('Fleet_model');

        // Fetch component data from the model
        $data['components'] = $this->Fleet_model->fetch_component_data();

        // Return the fetched data as a JSON response
        echo json_encode($data['components']);
    }
    public function display_spare()
    {
        // Load the Fleet_model if not already loaded
        $this->load->model('Fleet_model');

        // Fetch component data from the model
        $data['inventory'] = $this->Fleet_model->fetch_spare_data();

        // Return the fetched data as a JSON response
        echo json_encode($data['inventory']);
    }

    function save_uti()
    {


        echo json_encode($this->Fleet_model->save_uti_details());
    }
    public function submit_uti()
    {
        $masterId = $this->input->post('masterId');
        $proceedWithUnclosed = $this->input->post('proceedWithUnclosed'); // User confirmation flag

        echo json_encode($this->Fleet_model->submit_uti());
    }
    function get_latest_doc_number()
    {
        // Load the model if not already loaded
        $this->load->model('Fleet_model');

        // Fetch the latest document number
        $latestDoc = $this->Fleet_model->get_latest_doc();

        // Return the document number as JSON
        echo json_encode(array('doc_number' => $latestDoc));
    }

    function fetch_uti_table()
    {
        $this->datatables->select("fleet_asset_utilization.id as id,
        fleet_asset_utilization.doc_number as doc_number,
        fleet_asset_utilization.create_by_id as create_by_id,
        fleet_asset_utilization.date as date, 
        fleet_asset_utilization.description as description,
        fleet_asset_utilization.is_submitted as is_submitted");

        // $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,id FROM fleet_fuelusagedetails GROUP BY id) det', '(det.id = fleet_asset_utilization.id)', 'left');
        $this->datatables->from('fleet_asset_utilization');
        $this->datatables->where('company_id', $this->common_data['company_data']['company_id']);
        // Use the helper function to generate the submission status label
        $this->datatables->edit_column('is_submitted', '$1', 'get_submission_status_label(is_submitted),is_submitted');
        // $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'number_format(total_value,doc_numberDecimalPlaces),doc_number');
        // $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"FU",id)');
        // $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN, "FU", id)');
        $this->datatables->add_column('action', '$1', 'load_maintenance_action(id,is_submitted)');

        // $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    // public function load_uti() {
    //     echo json_encode($this->Fleet_model->load_uti());
    // }
    function edit_uti()
    {
        $masterId = $this->input->post('masterId');


        $data = array();
        // $data["grvAutoID"] = $this->input->post('grvAutoID');
        $data["checklists"] = $this->Fleet_model->load_uti();

        $html = $this->load->view('system/Fleet_Management/template', $data, true);
        echo $html;
    }
    function edit_com_uti()
    {
        $masterId = $this->input->post('masterId');


        $data = array();
        // $data["grvAutoID"] = $this->input->post('grvAutoID');
        $data["checklists"] = $this->Fleet_model->load_com_uti();

        $html = $this->load->view('system/Fleet_Management/template_component', $data, true);
        echo $html;
    }
    function edit_uti_view()
    {
        $masterId = $this->input->post('masterId');


        $data = array();
        // $data["grvAutoID"] = $this->input->post('grvAutoID');
        $data["checklists"] = $this->Fleet_model->load_uti();

        $html = $this->load->view('system/Fleet_Management/template_view', $data, true);
        echo $html;
    }
    function edit_com_uti_view()
    {
        $masterId = $this->input->post('masterId');


        $data = array();
        // $data["grvAutoID"] = $this->input->post('grvAutoID');
        $data["checklists"] = $this->Fleet_model->load_com_uti();

        $html = $this->load->view('system/Fleet_Management/template_component_view', $data, true);
        echo $html;
    }

    function fetch_editasset_line_record()
    {
        $masterId = $this->input->post('id');
        echo json_encode($this->Fleet_model->fetch_editasset_line_record());
    }
    function fetch_editcomponent_line_record()
    {
        $masterId = $this->input->post('id');
        echo json_encode($this->Fleet_model->fetch_editcomponent_line_record());
    }


    public function get_utilization($id)
    {
        $this->load->model('Fleet_model'); // Load your model here
        $utilization = $this->Fleet_model->get_utilization_by_id($id);

        if ($utilization) {
            $response = [
                'status' => 'success',
                'utilization' => $utilization
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Utilization data not found.'
            ];
        }

        echo json_encode($response);
    }


    function  delete_uti()
    {
        $this->form_validation->set_rules('id', 'id', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->delete_uti());
        }
    }
    function  delete_asset_line_record()
    {
        $this->form_validation->set_rules('id', 'id', 'trim|required');


        echo json_encode($this->Fleet_model->delete_asset_line_record());
    }
    function   delete_component_line_record()
    {
        $this->form_validation->set_rules('id', 'id', 'trim|required');


        echo json_encode($this->Fleet_model->delete_component_line_record());
    }


    function save_asset_line_item_edit()
    {

        echo json_encode($this->Fleet_model->save_asset_line_item_edit());
    }
    function save_com_line_item_edit()
    {

        echo json_encode($this->Fleet_model->save_com_line_item_edit());
    }

    function fetch_doc_header_detail()
    {
        echo json_encode($this->Fleet_model->fetch_doc_header_detail());
    }
    function load_fleet_inspection_comfirmation()
    {
        $inspectionID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('id') ?? '');
        $data['extra'] = $this->Fleet_model->fetch_inspection_data($inspectionID);
        // $data['approval'] = $this->input->post('approval');
        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }
        $html = $this->load->view('system/Fleet_Management/fleet_inspection_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']);
        }
    }

    public function get_fleet_edit_data()
    {
        $id = $this->input->post('id');

        // Validate input
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID!']);
            return;
        }

        // Fetch main data
        $this->db->where('id', $id);
        $fleetData = $this->db->get('fleet_master')->row_array();

        if (!$fleetData) {
            echo json_encode(['status' => 'error', 'message' => 'Data not found!']);
            return;
        }

        // Fetch asset data
        $this->db->where('fleet_id', $id);
        $assets = $this->db->get('fleet_assets')->result_array();

        // Fetch component data
        $this->db->where('fleet_id', $id);
        $coms = $this->db->get('fleet_components')->result_array();

        // Combine data
        $fleetData['assets'] = $assets;
        $fleetData['coms'] = $coms;

        echo json_encode(['status' => 'success', 'data' => $fleetData]);
    }

    function save_maintenanceheader()
    {
        $vehiclemasterid = $this->input->post('vehicalemasterid');
        $currentmeterreading = $this->input->post('currentmeterreading');
        $nextmaintenance = $this->input->post('nextmaintenance');
        $vehicalemaintenaceid = $this->input->post('vehicalemaintenaceid');
        $maintenacedoneby = $this->input->post('maintenancedoneby');
        $companyid = current_companyID();
        $policy = getPolicyValues('MMNT', 'All'); // Policy value to allow multiple maintenances
        $proceedWithUnclosed = $this->input->post('proceedWithUnclosed'); // User confirmation flag

        $this->form_validation->set_rules('assettype', 'Asset Type', 'trim|required'); // New validation rule
        $this->form_validation->set_rules('maintenancetype', 'Maintenance Type', 'trim|required');
        $this->form_validation->set_rules('maintenancedatefrom', 'Maintenance Date From', 'trim|required');
        $this->form_validation->set_rules('maintenancedateto', 'Maintenance Date To', 'trim|required');
        $this->form_validation->set_rules('nextmaintenance', 'Next Maintenance KM', 'trim|required');
        $this->form_validation->set_rules('nextmaintenancedate', 'Next Maintenance Date', 'trim|required');
        $this->form_validation->set_rules('transactioncurrencyid', 'Currency', 'trim|required');
        $this->form_validation->set_rules('currentmeterreading', 'Current Meterreading KM', 'trim|required');
        $this->form_validation->set_rules('maintenancedoneby', 'Maintenance By', 'trim|required');

        if ($maintenacedoneby == 2) {
            $this->form_validation->set_rules('maintenancecompany', 'Maintenance Company', 'trim|required');
            $this->form_validation->set_rules('glcode', 'Gl Code ', 'trim|required');
            $this->form_validation->set_rules('segment', 'Segment ', 'trim|required');
        }
        if ($maintenacedoneby == 1) {
            $this->form_validation->set_rules('warehouse', 'Ware House', 'trim|required');
        }
        $meterreading = $data = $this->db->query("SELECT IFNULL(MAX(current_meter_reading ),vehicalemaster.initialMilage ) AS maximumcurrentreading FROM fleet_meter_reading masterreading inner join fleet_vehiclemaster vehicalemaster on vehicalemaster.vehicleMasterID = masterreading.vehicleMasterID WHERE masterreading.vehicleMasterID = $vehiclemasterid AND masterreading.companyID = $companyid")->row_array();

        $statusisexis = $this->db->query("select maintenanceMasterID from fleet_maintenancemaster where `status` !=3  AND vehicleMasterID = $vehiclemasterid AND companyID = $companyid ")->result_array();
        $maintenanceCodeResults = $this->db->query("SELECT maintenanceCode FROM fleet_maintenancemaster WHERE `status` != 3 AND vehicleMasterID = $vehiclemasterid AND companyID = $companyid")->result_array();

        if (!empty($statusisexis) && empty($vehicalemaintenaceid)) {
            if ($policy == 1 && !$proceedWithUnclosed) {
                $maintenanceCodes = implode(', ', array_column($maintenanceCodeResults, 'maintenanceCode'));
                // Show a warning to proceed with unclosed maintenance
                echo json_encode(array('w', "There is unclosed maintenance exist for this asset. Do you want to proceed? The following maintenance codes are open: $maintenanceCodes"));
                return;
            } elseif ($policy != 1) {
                // Show an error and do not allow multiple maintenance
                echo json_encode(array('e', 'There is unclosed maintenance exist for this asset.'));
                return;
            }
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if ($currentmeterreading >= $meterreading['maximumcurrentreading']) {
                if ($currentmeterreading > $nextmaintenance) {
                    echo json_encode(array('e', 'Next maintenance km cannot be less than current meter reading'));
                } else {
                    echo json_encode($this->Fleet_model->save_maintenanceheader());
                }
            } else {
                echo json_encode(array('e', 'Current meter reading cannot be less than existing meter reading ' . $meterreading['maximumcurrentreading'] . ' KM!'));
            }
        }
    }


    public function get_asset_names()
    {
        $asset_type = $this->input->get('asset_type');
        $this->load->model('Fleet_model');
        $vehiclemasterids = $this->Fleet_model->get_assets_by_type($asset_type);
        echo json_encode(array('vehiclemasterids' => $vehiclemasterids));
    }

    public function fetch_asset_names()
    {
        $assetType = $this->input->post('assetType');

        if ($assetType) {
            // Adjust your query as needed to match your database schema
            $this->db->where('asset_type_id', $assetType);
            $query = $this->db->get('fleet_vehiclemaster');

            $assets = $query->result_array();
            echo json_encode($assets);
        } else {
            echo json_encode([]);
        }
    }


    function load_maintenance_detail_view()
    {
        // Get the current company ID
        $companyid = current_companyID();

        // Convert date format for SQL query
        $convertFormat = convert_date_format_sql();

        // Query to get all maintenance details for the current company
        $data['detail'] = $this->db->query("
    SELECT 
        mastertbl.*, 
        maintenacetype.type AS maintenacetypedescription,
        suppliermaster.supplierName,
        vehiclemaster.brand_description AS assetname,
        DATE_FORMAT(nextMaintenanceDate, '$convertFormat') AS nextMaintenanceDate,
        DATE_FORMAT(documentDate, '$convertFormat') AS documentDatecon, 
        utilizationmaster.doc_number AS utilaztionCode
    FROM 
        fleet_maintenancemaster mastertbl 
    LEFT JOIN 
        fleet_maintenancetype maintenacetype 
        ON maintenacetype.maintenanceTypeID = mastertbl.maintenanceType 
    LEFT JOIN 
        srp_erp_suppliermaster suppliermaster 
        ON suppliermaster.supplierAutoID = mastertbl.maintenanceCompanyID 
    LEFT JOIN 
        fleet_vehiclemaster vehiclemaster
        ON vehiclemaster.vehicleMasterID = mastertbl.vehicleMasterID 
    LEFT JOIN
        fleet_asset_utilization utilizationmaster
        ON utilizationmaster.id = mastertbl.utilizationID
    WHERE 
        mastertbl.companyID = $companyid 
    ORDER BY 
        maintenanceMasterID DESC
")->result_array();

        // Load the view with the maintenance details data
        $this->load->view('system/Fleet_Management/ajax/maintenance_detail', $data);
    }

    function fetch_maintenance_header_details()
    {
        echo json_encode($this->Fleet_model->fetch_maintenance_header_details());
    }


    function save_maintenance_status_with_assetstatus()

    {
        $date_format_policy = date_format_policy();
        $status = $this->input->post('statusmaintenace');
        $maintenacemasterid = $this->input->post('maintenacemasterid');
        $companyid = current_companyID();
        $this->form_validation->set_rules('statusmaintenace', 'Status', 'trim|required');
        $statusmaintenace = $this->db->query("select status,currentMeterReading from fleet_maintenancemaster where maintenanceMasterID = $maintenacemasterid")->row_array();
        $vehicleMasterID = $this->input->post('vehicalemasteridstatus');
        $meterreading = $data = $this->db->query("SELECT MAX(current_meter_reading) as maximumcurrentreading FROM `fleet_meter_reading` WHERE vehicleMasterID = $vehicleMasterID AND companyID = $companyid")->row_array();
        $documentDate = $this->input->post('stausdate');
        $formatted_documentDate = input_format_date($documentDate, $date_format_policy);

        $finaceper = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod  WHERE isActive = 1 AND companyID = $companyid AND '$formatted_documentDate' BETWEEN dateFrom AND dateTo ")->row_array();


        if ($statusmaintenace['currentMeterReading'] >= $meterreading['maximumcurrentreading']) {
            if ($status == 3) {
                $this->form_validation->set_rules('stausdate', 'Date', 'trim|required');
                $this->form_validation->set_rules('commentstauts', 'Comment', 'trim|required');
            } else {
                if ($status == 2) {
                    $this->form_validation->set_rules('stausdate', 'Date', 'trim|required');
                }
            }
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {

                if ($statusmaintenace['status'] == 2 && $status == 1) {
                    echo json_encode(array('e', 'Cannot change the on going status to not started!'));
                } else if ($statusmaintenace['status'] == 3 && $status == 2) {
                    echo json_encode(array('e', 'Cannot change the closed status to on going!'));
                } else if ($statusmaintenace['status'] == 3 && $status == 1) {
                    echo json_encode(array('e', 'Cannot change the closed status to not started !'));
                } else {
                    if ($status == 3) {
                        if ($formatted_documentDate >= $finaceper['dateFrom'] && $formatted_documentDate <= $finaceper['dateTo']) {
                            echo json_encode($this->Fleet_model->save_maintenance_status_with_assetstatus());
                        } else {
                            echo json_encode(array('e', 'Maintenance closing date is not between Financial period !'));
                        }
                    } else {
                        echo json_encode($this->Fleet_model->save_maintenance_status_with_assetstatus());
                    }
                }
            }
        } else {
            echo json_encode(array('e', 'Current meter reading cannot be less than existing meter reading ' . $meterreading['maximumcurrentreading'] . ' KM !'));
        }
    }


    function fetch_maintenance_overview()
    {
        echo json_encode($this->Fleet_model->fetch_maintenance_overview());
    }

    function fetch_asset_under_maintenance()
    {
        echo json_encode($this->Fleet_model->fetch_asset_under_maintenance());
    }
    function fetch_dashboard_asset_status()
    {
        echo json_encode($this->Fleet_model->fetch_dashboard_asset_status());
    }
    public function fetch_inspection_templates()
    {
        // Fetch vehicleMasterID from the AJAX request
        $utilizationDetailID = $this->input->post('utilizationDetailID');

        // Call the function to load the templates
        $templates = load_inspection_templates($utilizationDetailID);

        // Return the dropdown options as HTML
        echo form_dropdown('assetID', $templates, '', "class='form-control select2' id='assetID' style='width:255px;' rows='2'");
    }

    function save_templates()
    {
        if (!$this->input->post('checklistID')) {
            $this->form_validation->set_rules('assetID', 'Template', 'trim|required');
        }
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Fleet_model->save_templates());
        }
    }
    function getTemplates()
    {
        $checklistID = $this->input->post('checklistID');


        // Validate the checklistID to make sure it's not empty
        if (empty($checklistID)) {
            echo json_encode(['status' => 'error', 'message' => 'Checklist ID is required']);
            return;
        }

        $this->db->select('srp_erp_document_templates.id,srp_erp_document_templates.description, srp_erp_inspection_template_master.templateName, srp_erp_inspection_template_master.pageLink');
        $this->db->from('srp_erp_document_templates');
        $this->db->join('srp_erp_inspection_template_master', 'srp_erp_document_templates.templateMasterID = srp_erp_inspection_template_master.id', 'left');
        $this->db->where('srp_erp_document_templates.documentDetailID', $checklistID);


        $query = $this->db->get();


        // Check if the query returns results
        if ($query->num_rows() > 0) {
            // Send the result as a JSON response
            echo json_encode($query->result());
        } else {
            // Send an empty array if no results are found

            echo json_encode([]);
        }
    }

    function   deleteTemplate()
    {
        $this->form_validation->set_rules('id', 'id', 'trim|required');


        echo json_encode($this->Fleet_model->deleteTemplate());
    }

    public function load_document_templates_details()
    {
        $id = $this->input->post('id');
        $pagelink = $this->input->post('pagelink');
        $templateName = $this->input->post('templateName');

        $this->db->select('*');
        $this->db->from('srp_erp_document_templates_details');
        $this->db->where('documentTemplateID', $id);

        $query = $this->db->get();

        $this->db->select('srp_erp_document_templates.*, fleet_asset_utilization_detail.asset_id, fleet_vehiclemaster.vehDescription, fleet_vehiclemaster.vehicleCode, fleet_vehiclemaster.SerialNo');
        $this->db->from('srp_erp_document_templates');
        $this->db->join('fleet_asset_utilization_detail', 'fleet_asset_utilization_detail.id = srp_erp_document_templates.documentDetailID');
        $this->db->join('fleet_vehiclemaster', 'fleet_asset_utilization_detail.asset_id = fleet_vehiclemaster.vehicleMasterID');
        $this->db->where('srp_erp_document_templates.id', $id);

        $headerData = $this->db->get()->row();

        $newData['id'] = $id;
        $newData['headerData'] = $headerData;
        $newData['templateName'] = $templateName;



        if ($query->num_rows() > 0)
        {

            $viewData['existingData'] = $query->result();
            $viewData['headerData'] = $headerData;
            $viewData['templateName'] = $templateName;

            echo json_encode(['status' => 'success', 'html' => $this->load->view($pagelink, $viewData, true)]);
        }
        else
        {

            echo json_encode(['status' => 'success', 'html' => $this->load->view($pagelink, $newData, true)]);
        }
    }


    function save_document_templates_details()
    {

        $documentTemplateID = $this->input->post('documentTemplateID');

        $this->db->select('*');
        $this->db->from('srp_erp_document_templates_details');
        $this->db->where('documentTemplateID', $documentTemplateID);

        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {

            return array('e', 'document already exist');
        }
        else
        {
            echo json_encode($this->Fleet_model->save_document_templates_details());
        }
    }

    function edit_document_templates_details()
    {
        $documentTemplateID = $this->input->post('documentTemplateID');
        $formData = $this->input->post('formData');


        if (!$documentTemplateID)
        {
            echo json_encode(['e', 'Document Template ID is required']);
            return;
        }

        if (!is_array($formData) || empty($formData))
        {
            echo json_encode(['e', 'Incomplete data received']);
            return;
        }


        $result = $this->Fleet_model->edit_document_templates_details($documentTemplateID, $formData);
        echo json_encode($result);
    }
}
