<?php

class Fleet_model extends ERP_Model
{

    function Save_New_Vehicle()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $vehicleMasterID = trim($this->input->post('vehicleMasterID') ?? '');
        $VehicleNo = trim($this->input->post('VehicleNo') ?? '');
        $faid = $this->input->post('fixedasset');

        $this->db->trans_start();
        $data['bodyType'] = trim($this->input->post('fuelBodyID') ?? '');
        $data['colour'] = trim($this->input->post('colourID') ?? '');
        $data['brand_id'] = $this->input->post('vehicalebrand');
        $data['manufacturedYear'] = ($this->input->post('yearManu'));
        $data['fuelTypeID'] = ($this->input->post('fuelTypeID'));
        $data['engineCapacity'] = ($this->input->post('EngineCapacity'));
        $data['expKMperLiter'] = ($this->input->post('Speed'));
        $data['VehicleNo'] = ($this->input->post('VehicleNo'));
        $data['isActive'] = ($this->input->post('active'));
        $data['thirdPartySupplierID'] = ($this->input->post('supplier'));
        $data['transmisson'] = ($this->input->post('transmisson_type'));
        $data['faID'] = ($this->input->post('fixedasset'));
        $data['vehicle_type'] = ($this->input->post('vehicle_type'));
        if ($data['vehicle_type'] == 2)
        {
            $data['thirdPartySupplierID'] = ($this->input->post('supplier'));
        }
        else
        {
            $data['thirdPartySupplierID'] = null;
        }

        $data['vehDescription'] = ($this->input->post('vehDescription'));
        $data['initialMilage'] = ($this->input->post('initialmileage'));
        $data['ivmsNo'] = ($this->input->post('ivmsno'));
        $data['transmisson_description'] = ($this->input->post('transmissontypedescription'));
        $date_format_policy = date_format_policy();
        $register = input_format_date($this->input->post('registerDate'), $date_format_policy);
        $data['registerDate'] = $register;
        $date_format_policy = date_format_policy();
        $insurance = input_format_date($this->input->post('insuranceDate'), $date_format_policy);
        $data['insuranceDate'] = $insurance;
        $date_format_policy = date_format_policy();
        $licence = input_format_date($this->input->post('lisenceDate'), $date_format_policy);
        $data['licenseDate'] = $licence;
        $fuel = $data['fuelTypeID'];
        $fuelDes = $this->db->query("SELECT description FROM fleet_fuel_type WHERE fuelTypeID = $fuel ")->row_array();
        $data['fuel_type_description'] = $fuelDes['description'];
        $vehicle = $data['bodyType'];
        $vehicleDes = $this->db->query("SELECT description FROM fleet_fuel_body WHERE fuelBodyID = $vehicle ")->row_array();
        $data['bodyType_description'] = $vehicleDes['description'];
        $color = $data['colour'];
        $colorDes = $this->db->query("SELECT description FROM fleet_colour WHERE colourID = $color ")->row_array();
        $data['colour_description'] = $colorDes['description'];
        $data['brand_description'] = $this->input->post('vehicalebranddescription');
        $data['model_id'] = $this->input->post('vehicalemodel');
        $data['model_description'] = $this->input->post('vehicalemodeldescription');
        $data['chessiNo'] = $this->input->post('chessino');
        /* if (trim($this->input->post('vehicleMasterID') ?? '')) {
             $descexist = $this->db->query("SELECT vehicleMasterID FROM fleet_vehiclemaster WHERE VehicleNo='$VehicleNo' AND vehicleMasterID !=$vehicleMasterID AND companyID = $companyID; ")->row_array();
         } else {
             $descexist = $this->db->query("SELECT vehicleMasterID FROM fleet_vehiclemaster WHERE VehicleNo='$VehicleNo' AND companyID = $companyID; ")->row_array();
         }*/
        if ($vehicleMasterID)
        {
            $data['modifiedpc'] = $this->common_data['current_pc'];
            $data['modifieduser'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('vehicleMasterID', trim($this->input->post('vehicleMasterID') ?? ''));
            $this->db->update('fleet_vehiclemaster', $data);
            if (!empty($faid))
            {
                $isassetexist = $this->db->query("select faID from  fleet_vehiclemaster where companyID = $companyID  AND faid = $faid AND vehicleMasterID != $vehicleMasterID")->row_array();
            }

            $descexist = $this->db->query("SELECT vehicleMasterID FROM fleet_vehiclemaster WHERE VehicleNo='$VehicleNo' AND vehicleMasterID !=$vehicleMasterID AND companyID = $companyID; ")->row_array();

            if (isset($descexist))
            {
                return array('e', 'Vehicle Number Already Exist');
            }
            if (isset($isassetexist))
            {
                return array('e', 'Fixed Asset already added');
            }
            else
            {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    return array('e', 'Error while updating the vehicle!');
                }
                else
                {
                    $this->db->trans_commit();
                    //  return array('s', 'Vehicle Updated Successfully.');
                    return array('s', 'Vehicle updated Successfully!', $vehicleMasterID);
                }
            }
        }
        else
        {
            if (!empty($faid))
            {
                $isassetexist = $this->db->query("select faID from  fleet_vehiclemaster where companyID = $companyID  AND faid = $faid")->row_array();
            }

            $descexist = $this->db->query("SELECT vehicleMasterID FROM fleet_vehiclemaster WHERE VehicleNo='$VehicleNo' AND companyID = $companyID; ")->row_array();
            if (isset($descexist))
            {
                return array('e', 'Vehicle Number Already Exist');
            }

            if (isset($isassetexist))
            {
                return array('e', 'Fixed Asset already added');
            }
            else
            {
                $serial = $this->db->query("select IF ( isnull(MAX(SerialNo)), 1, (MAX(SerialNo) + 1) ) AS SerialNo FROM `fleet_vehiclemaster` WHERE companyID={$companyID}")->row_array();

                $data['SerialNo'] = $serial['SerialNo'];
                $data['vehicleCode'] = 'VEH';;
                $company_code = $this->common_data['company_data']['company_code'];

                $data['vehicleCode'] = ($company_code . '/' . 'VEH' . str_pad(
                    $data['SerialNo'],
                    6,
                    '0',
                    STR_PAD_LEFT
                ));

                $data['vehicleMasterID'] = trim($this->input->post('vehicleMasterID') ?? '');

                $data['companyID'] = $companyID;
                $data['createdpc'] = $this->common_data['current_pc'];
                $data['createuser'] = $this->common_data['current_userID'];
                $data['createDateTime'] = $this->common_data['current_date'];
                $this->db->insert('fleet_vehiclemaster', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    return array('e', 'Error while creating the vehicle!');
                }
                else
                {
                    $this->db->trans_commit();
                    return array('s', 'Vehicle is created Successfully!', $last_id);
                }
            }
        }
    }

    function CreateNewBrand()
    {
        /*       $title = trim($this->input->post('brand_description') ?? '');
              // $companyID = current_companyID();
               $isExist = $this->db->query("SELECT description FROM fleet_brand_master WHERE description='$title' ")->row('brandID');

               if (isset($isExist)) {
                   return array('error' => 1, 'This Brand is already Exists');
               } else {

                   $data = array(
                       'description' => $title,
                       'CreatedPC' => $this->common_data['current_pc'],
                       'createDateTime' => $this->common_data['current_date'],
                       'createuser' => $this->common_data['current_userID'],
                       'createdUserGroup' => $this->common_data['user_group']
                   );

                   $this->db->insert('fleet_brand_master', $data);
                   if ($this->db->affected_rows() > 0) {
                       $brandID = $this->db->insert_id();
                       return array('error' => 0, 'Brand is created successfully.', $brandID);
                   } else {
                       return array('error' => 1, 'Error in Brand Creating');
                   }
               }


       */
        $data['description'] = trim($this->input->post('brand_description') ?? '');
        $data['createdpc'] = $this->common_data['current_pc'];
        $data['createuser'] = $this->common_data['current_userID'];
        $data['createDateTime'] = $this->common_data['current_date'];
        $data['createdUserGroup'] = $this->common_data['user_group'];

        $this->db->insert('fleet_brand_master', $data);
        // $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('error' => 1, 'message' => 'Error while creating new Brand!');
        }
        else
        {
            $this->db->trans_commit();
            return array('error' => 0, 'message' => 'New Brand is created Successfully!');
        }
    }

    function CreateNewModel()
    {
        $data['description'] = trim($this->input->post('model_description') ?? '');
        $data['brandID'] = trim($this->input->post('brandID') ?? '');
        $data['createdpc'] = $this->common_data['current_pc'];
        $data['createuser'] = $this->common_data['current_userID'];
        $data['createDateTime'] = $this->common_data['current_date'];
        $data['createdUserGroup'] = $this->common_data['user_group'];

        $this->db->insert('fleet_brand_model', $data);
        // $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('error' => 1, 'message' => 'Error while creating new Model!');
        }
        else
        {
            $this->db->trans_commit();
            return array('error' => 0, 'message' => 'New Model is created Successfully!');
        }
    }

    function load_vehicle()
    {
        $convertFormat = convert_date_format_sql();

        $vehicleMasterID = $this->input->post('vehicleMasterID');
        $data = $this->db->query("select * ,DATE_FORMAT(registerDate,'{$convertFormat}') AS registerDate,DATE_FORMAT(insuranceDate,'{$convertFormat}') AS insuranceDate, DATE_FORMAT(licenseDate,'{$convertFormat}') AS lisenceDate from fleet_vehiclemaster WHERE vehicleMasterID = {$vehicleMasterID} ")->row_array();

        return $data;
    }
    function load_assets()
    {
        $convertFormat = convert_date_format_sql();

        $vehicleMasterID = $this->input->post('vehicleMasterID');
        $data = $this->db->query("select * ,DATE_FORMAT(registerDate,'{$convertFormat}') AS registerDate,DATE_FORMAT(insuranceDate,'{$convertFormat}') AS insuranceDate, DATE_FORMAT(licenseDate,'{$convertFormat}') AS lisenceDate from fleet_vehiclemaster WHERE vehicleMasterID = {$vehicleMasterID} ")->row_array();

        return $data;
    }


    function delete_vehicle()
    {
        $vehicleMasterID = trim($this->input->post('vehicleMasterID') ?? '');

        $this->db->trans_start();

        $this->db->where('vehicleMasterID', $vehicleMasterID)->delete('fleet_vehiclemaster');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }

    function delete_asset()
    {
        $vehicleMasterID = trim($this->input->post('vehicleMasterID') ?? '');

        // Check if the asset is assigned to an inspection
        $this->db->select('id');
        $this->db->from('fleet_asset_utilization_detail');
        $this->db->where('asset_id', $vehicleMasterID);
        $asset_in_inspection = $this->db->get()->row_array();

        if (!empty($asset_in_inspection))
        {
            // Asset is assigned to an inspection
            return array('e', 'Asset is already assigned to inspection.');
        }

        // Begin transaction for deletion
        $this->db->trans_start();

        // Delete the asset from fleet_vehiclemaster
        $this->db->where('vehicleMasterID', $vehicleMasterID)->delete('fleet_vehiclemaster');

        $this->db->trans_complete();

        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }


    /* DRIVER MASTER PAGE */

    function Save_New_Driver()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $driverMasID = trim($this->input->post('driverMasID') ?? '');


        $data['driverMasID'] = $this->input->post('driverMasID');

        $data['OtherName'] = $this->input->post('OtherName');
        $data['driverAge'] = $this->input->post('driverAge');
        $data['drivPhoneNo'] = $this->input->post('drivPhoneNo');
        $data['drivAddress'] = $this->input->post('drivAddress');
        $data['isActive'] = $this->input->post('active');
        $data['licenceNo'] = $this->input->post('licenceNo');
        $data['driveDescript'] = $this->input->post('driveDescript');
        $data['bloodGroup'] = $this->input->post('bloodGroup');

        $date_format_policy = date_format_policy();
        $licenceExpire = input_format_date($this->input->post('liceExpireDate'), $date_format_policy);
        $data['liceExpireDate'] = $licenceExpire;


        if ($this->input->post('employeeID'))
        {
            $empdet = explode('|', trim($this->input->post('employee_det') ?? ''));
            $data['driverName'] = $empdet[1];
            $data['empID'] = trim($this->input->post('employeeID') ?? '');
        }
        else
        {
            $data['driverName'] = trim($this->input->post('employeeName') ?? '');
            $data['empID'] = NULL;
        }
        if ($driverMasID)
        {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('driverMasID', trim($this->input->post('driverMasID') ?? ''));
            $this->db->update('fleet_drivermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Driver Update Failed');
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Driver Update Successfully.');
            }
        }
        else
        {
            $serial = $this->db->query("select IF ( isnull(MAX(SerialNo)), 1, (MAX(SerialNo) + 1) ) AS SerialNo FROM `fleet_drivermaster` WHERE companyID={$companyID}")->row_array();

            $data['SerialNo'] = $serial['SerialNo'];
            $data['driverCode'] = 'DRV';;
            $company_code = $this->common_data['company_data']['company_code'];

            $data['driverCode'] = ($company_code . '/' . 'DRV' . str_pad(
                $data['SerialNo'],
                6,
                '0',
                STR_PAD_LEFT
            ));

            $data['companyID'] = $companyID;
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('fleet_drivermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Driver Enrollment Failed');
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Driver Enrolled Successfully.', $last_id);
            }
        }
    }

    function load_driver()
    {
        $convertFormat = convert_date_format_sql();

        $driverMasID = $this->input->post('driverMasID');
        $data = $this->db->query("select * ,DATE_FORMAT(liceExpireDate,'{$convertFormat}') AS liceExpireDate from fleet_drivermaster WHERE driverMasID = {$driverMasID} ")->row_array();

        return $data;
    }

    function delete_driver()
    {
        $driverMasID = trim($this->input->post('driverMasID') ?? '');

        $this->db->trans_start();

        $this->db->where('driverMasID', $driverMasID)->delete('fleet_drivermaster');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }

    function vehicle_image_upload()
    {
        $this->db->trans_start();

        $vehicleID =  trim($this->input->post('vehicleID') ?? '');
        $company_code =  $this->common_data['company_data']['company_code'];
        $companyid = current_companyID();
        $itemimageexist = $this->db->query("SELECT vehicleImage FROM `fleet_vehiclemaster` WHERE companyID = $companyid AND vehicleMasterID = $vehicleID ")->row_array();
        if (!empty($itemimageexist))
        {
            $this->s3->delete('uploads/Fleet/VehicleImg/' . $company_code . '/' . $itemimageexist['vehicleImage']);
        }
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = $company_code . '/VM_' . $this->common_data['company_data']['company_code'] . '_' . trim($this->input->post('vehicleID') ?? '') . '.' . $info->getExtension();
        $file = $_FILES['files'];
        if ($file['error'] == 1)
        {
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if (!in_array($ext, $allowed_types))
        {
            return array('e', "The file type you are attempting to upload is not allowed. ( .{$ext} )");
        }
        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if ($size > 5)
        {
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");
        }

        $path = "uploads/Fleet/VehicleImg/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload)
        {
            return array('e', "Error in document upload location configuration");
        }
        $currentDatetime = format_date_mysql_datetime();
        $currentdate = $currentDatetime;

        $this->db->trans_start();
        $currentDatetime = format_date_mysql_datetime();
        $data['vehicleImage'] = $fileName;
        $data['timestamp'] = $currentdate;

        $this->db->where('vehicleMasterID', trim($this->input->post('vehicleID') ?? ''));
        $this->db->update('fleet_vehiclemaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');
        }

        /*$output_dir = "uploads/Fleet/VehicleImg/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/Fleet", 007);
            mkdir("uploads/Fleet/VehicleImg/", 007);
        }
        $currentDatetime = format_date_mysql_datetime();
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $currentdate = $currentDatetime;
        $fileName = 'Vehicle_' . trim($this->input->post('vehicleID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['vehicleImage'] = $fileName;
        $data['timestamp'] = $currentdate;

        $this->db->where('vehicleMasterID', trim($this->input->post('vehicleID') ?? ''));
        $this->db->update('fleet_vehiclemaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');


        }*/
    }

    /*--------------- Transaction -------------------*/

    function fetch_supplier_detail()
    {
        $this->db->select('*');
        $this->db->where('supplierAutoID', $this->input->post('supplierAutoID'));
        return $this->db->get('srp_erp_suppliermaster')->row_array();
    }

    function fetch_fuelType()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $driverMasID = $this->input->post('vehicleMasterID');
        $data = $this->db->query("select *,fleet_fuel_type.fuelRate from fleet_vehiclemaster LEFT JOIN fleet_fuel_type ON fleet_fuel_type.fuelTypeID = fleet_vehiclemaster.fuelTypeID WHERE vehicleMasterID = {$driverMasID} AND fleet_vehiclemaster.companyID = $companyID  ")->result_array();

        return $data;
    }


    function fetch_employee_detail()
    {
        $this->db->select('*');
        $this->db->where('EIdNo', $this->input->post('EIdNo'));
        return $this->db->get('srp_employeesdetails')->row_array();
    }

    function save_fuelusage()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $segmentID = explode('|', trim($this->input->post('segment') ?? ''));
        $FinanceYear = $this->input->post('companyFinanceYearID');
        $FinancePeriod = $this->input->post('financeyear_period');
        $Supplier = $this->input->post('supplierAutoID');
        $fuelusageID = $this->input->post('fuelusageID');
        $currencycode = explode('|', trim($this->input->post('currency_code') ?? ''));
        $company_code = $this->common_data['company_data']['company_code'];
        $supplierdetails = $this->db->query("SELECT * FROM `srp_erp_suppliermaster` WHERE supplierAutoID = $Supplier AND companyID = $companyID")->row_array();

        $data['segmentID'] = trim($segmentID[0] ?? '');
        $data['segmentCode'] = trim($segmentID[1] ?? '');
        $narration = $this->input->post('narration');
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['referenceNumber'] = $this->input->post('referenceNumber');
        $data['transactionCurrencyID'] = $this->input->post('transactionCurrencyID');
        $data['transactionCurrency'] = trim($currencycode[0] ?? '');
        $data['supplierAutoID'] = $Supplier;
        $data['createdUserGroup'] = current_user_group();
        $data['supplierSystemCode'] = $supplierdetails['supplierSystemCode'];
        $data['supplierliabilityAutoID'] = $supplierdetails['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplierdetails['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplierdetails['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplierdetails['liabilityDescription'];
        $data['supplierliabilityType'] = $supplierdetails['liabilityType'];
        $data['companyFinanceYearID'] = $this->input->post('companyFinanceYearID');
        $data['companyFinancePeriodID'] = $this->input->post('financeyear_period');
        $data['transactionExchangeRate'] = 1;
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['supplier'] = $supplierdetails['supplierName'];
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyID'] = $companyID;
        $data['companyCode'] = $company_code;

        $date_format_policy = date_format_policy();
        $licenceExpire = input_format_date($this->input->post('documentDate'), $date_format_policy);
        $data['documentDate'] = $licenceExpire;
        $data['linkedToIOUYN'] = $this->input->post('linktoioubook');

        if ($fuelusageID)
        {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
            $update = $this->db->update('fleet_fuelusagemaster', $data);
            $last_id = $this->db->insert_id();

            if ($update)
            {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    return array('e', 'Error in fuel usage Update ' . $this->db->_error_message());
                }
                else
                {
                    $this->db->trans_commit();
                    return array('s', 'Fuel Usage Updated successfully.', $fuelusageID);
                }
            }
        }
        else
        {
            $serial = $this->db->query("select IF ( isnull(MAX(SerialNo)), 1, (MAX(SerialNo) + 1) ) AS SerialNo FROM `fleet_fuelusagemaster` WHERE companyID={$companyID}")->row_array();

            $data['SerialNo'] = $serial['SerialNo'];
            $data['documentID'] = 'FU';


            $data['documentCode'] = ($company_code . '/' . 'FU' . str_pad(
                $data['SerialNo'],
                6,
                '0',
                STR_PAD_LEFT
            ));

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('fleet_fuelusagemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Error in fuel usage insertion failed ' . $this->db->_error_message());
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Fuel Usage Inserted successfully.', $last_id);
            }
        }
    }


    function fetch_po_detail_table()
    {
        $this->db->select('transactionCurrency,');
        $this->db->where('fuelusageID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->from('srp_erp_purchaseordermaster');
        $data['currency'] = $this->db->get()->row_array();
        $this->db->select('*');
        $this->db->where('fuelusageID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->from('srp_erp_purchaseorderdetails');
        $data['detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('fuelusageID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->from('srp_erp_purchaseordertaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_document()
    {
        $this->db->select('*');
        $this->db->from('fleet_fuelusagedetails');
        $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
        $datas = $this->db->get()->row_array();
        if ($datas)
        {
            $this->session->set_flashdata('e', 'please delete all detail records before deleting this document.');
            return true;
        }
        else
        {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
            $this->db->update('fleet_fuelusagemaster', $data);
            $this->session->set_flashdata('s', ' Deleted Successfully');
            return true;
        }
    }

    function delete_fuelUsage_details()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $fuelusageDetailsID = $this->input->post('fuelusageDetailsID');

        //   $isExist = $this->db->query("SELECT CollectionDetailID FROM srp_erp_ngo_com_collectionmemsetup WHERE companyID={$companyID} AND CollectionDetailID = '$CollectionDetailID' ")->row('CollectionDetailID');

        $output = $this->db->delete('fleet_fuelusagedetails', array('fuelusageDetailsID' => trim($this->input->post('fuelusageDetailsID') ?? '')));
        if ($output)
        {
            return array('s', 'Collection Setup : Deleted Successfully.');
        }
        else
        {
            return array('e', 'Error in deleting process');
        }
    }

    function save_fuel_usage_detail()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $fuelusageDetailsID = $this->input->post('fuelusageDetailsID');
        $fuelusageID = $this->input->post('fuelusageID');
        $vehicleMasterID = $this->input->post('vehicleMasterID');
        $fuelTypeID = $this->input->post('fuelTypeID');
        //  var_dump($fuelTypeID);
        $fuelRate = ($this->input->post('FuelRate'));
        $startKm = $this->input->post('startKm');
        $endKm = $this->input->post('endKm');
        $totalAmount = $this->input->post('amount');
        $comment = $this->input->post('comment');
        $gl_category = $this->input->post('gl_code');
        $driver = $this->input->post('driverMasID');
        $date_format_policy = date_format_policy();
        $receiptDate = $this->input->post('receiptDate');
        $vehicale_det_detail = $this->input->post('vehicale_details');
        $segment_arr = $this->input->post('segment');
        $vehicaledt_arr = $this->input->post('driver_details');
        $fueltyype = $this->input->post('FuelType');
        $expectedkm = $this->input->post('expKmLiter');
        $this->db->trans_start();

        foreach ($gl_category as $key => $gl_category)
        {
            $segment = explode('|', trim($segment_arr[$key]));
            $driverdet = explode('|', trim($vehicaledt_arr[$key]));
            $vehicale_det = explode('|', trim($vehicale_det_detail[$key]));
            $glData = $this->db->query("SELECT config.glAutoID as GLAutoID,chart.systemAccountCode as SystemGLCode,chart.GLSecondaryCode as GLCode,chart.GLDescription as GLDescription,chart.subCategory as GLType FROM fleet_glconfiguration config INNER JOIN srp_erp_chartofaccounts chart on chart.GLAutoID = config.glAutoID WHERE glConfigAutoID = $gl_category AND config.companyID = $companyID")->row_array();

            $data['fuelusageID'] = $fuelusageID;
            $data['vehicleMasterID'] = $vehicleMasterID[$key];
            $data['receiptDate'] = input_format_date($receiptDate[$key], $date_format_policy);
            $data['fuelTypeID'] = $fuelTypeID[$key];
            $data['fuelRate'] = $fuelRate[$key];
            $data['startKm'] = $startKm[$key];
            $data['endKm'] = $endKm[$key];
            $data['totalAmount'] = $totalAmount[$key];
            $data['comment'] = $comment[$key];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['vehicleCode'] = trim($vehicale_det[0] ?? '');
            $data['VehicleNo'] = trim($vehicale_det[1] ?? '');
            $data['GLAutoID'] = $glData['GLAutoID'];
            $data['SystemGLCode'] = $glData['SystemGLCode'];
            $data['GLCode'] = $glData['GLCode'];
            $data['GLDescription'] = $glData['GLDescription'];
            $data['GLType'] = $glData['GLType'];
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['driverMasID'] = $driver[$key];
            $data['driverCode'] = trim($driverdet[0] ?? '');
            $data['driverName'] = trim($driverdet[1] ?? '');
            $data['FuelType'] = $fueltyype[$key];
            $data['glConfigAutoID'] = $gl_category;
            $data['expKMperLiter'] = $expectedkm[$key];
            $this->db->insert('fleet_fuelusagedetails', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', 'Fuel Details :  Save Failed ' . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Fuel Details :  Saved Successfully.');
        }
    }

    function update_fuel_usage_detail()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $fuelusageDetailsID = $this->input->post('fuelusageDetailsID_edit');
        $fuelusageID = $this->input->post('fuelusageID');
        $vehicleMasterID = $this->input->post('vehicleMasterID');
        $fuelTypeID = $this->input->post('fuelTypeID');
        $fuelRate = $this->input->post('FuelRate');
        $startKm = $this->input->post('startKm');
        $endKm = $this->input->post('endKm');
        $totalAmount = $this->input->post('amount');
        $comment = $this->input->post('comment');
        $gl_category = $this->input->post('gl_code');
        $driver = $this->input->post('driverMasID');


        $date_format_policy = date_format_policy();
        $receiptDate = $this->input->post('receiptDate');

        foreach ($gl_category as $key => $gl_category)
        {

            /*      $glData = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE GLAutoID ={$gl_code} AND companyID = {$companyID}  ")->row_array();
                  $data['PLSystemGLCode'] = $glData['GLSecondaryCode'];
                  $data['PLGLCode'] = $glData['GLAutoID'][$key];
                  $data['PLDescription'] = $glData['GLDescription'];
                  $data['PLType'] = $glData['subCategory'];
                  $data['PLGLAutoID'] = $gl_code;
      */
            $glData = $this->db->query("SELECT * FROM fleet_glconfiguration WHERE glConfigAutoID ={$gl_category} AND companyID = {$companyID}")->row_array();
            $data['PLDescription'] = $glData['glCodeDescription'];
            $data['PLGLAutoID'] = $glData['glAutoID'];
            $data['glConfigAutoID'] = $gl_category;

            $item_arr = $this->db->query("SELECT * FROM fleet_vehiclemaster WHERE vehicleMasterID ={$vehicleMasterID[$key]} ")->row_array();
            $data['VehicleNo'] = $item_arr['VehicleNo'];
            $data['vehicleCode'] = $item_arr['vehicleCode'];

            $Fuel = $this->db->query("SELECT * FROM fleet_fuel_type WHERE fuelTypeID ={$fuelTypeID[$key]} ")->row_array();
            $data['FuelType'] = $Fuel['description'];

            $driverData = $this->db->query("SELECT * FROM fleet_drivermaster WHERE driverMasID ={$driver[$key]} ")->row_array();
            $data['driverCode'] = $driverData['driverCode'];
            $data['driverName'] = $driverData['driverName'];
            $data['receiptDate'] = input_format_date($receiptDate[$key], $date_format_policy);


            $data['fuelusageID'] = $fuelusageID;
            $data['driverMasID'] = $driver[$key];
            $data['vehicleMasterID'] = $vehicleMasterID[$key];
            $data['fuelTypeID'] = $fuelTypeID[$key];
            $data['fuelRate'] = $fuelRate[$key];
            $data['startKm'] = $startKm[$key];
            $data['endKm'] = $endKm[$key];
            $data['totalAmount'] = $totalAmount[$key];
            $data['comment'] = $comment[$key];

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('fuelusageDetailsID', $fuelusageDetailsID);
            $this->db->update('fleet_fuelusagedetails', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', 'Fuel Details :  Update Failed ' . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Fuel Details :  Updated Successfully.');
        }
    }

    function load_fuelUsageDetail()
    {

        $convertFormat = convert_date_format_sql();
        $this->db->select('transactionCurrency');
        $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
        $this->db->from('fleet_fuelusagemaster');
        $data['currency'] = $this->db->get()->row_array();
        $this->db->select('*,DATE_FORMAT(receiptDate,\'' . $convertFormat . '\') AS receiptDate');
        $this->db->where('fuelusageDetailsID', trim($this->input->post('fuelusageDetailsID') ?? ''));
        $this->db->from('fleet_fuelusagedetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function load_fuel_usage_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate');
        $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
        $this->db->from('fleet_fuelusagemaster');
        return $this->db->get()->row_array();
    }

    function fetch_fuel_usage_detail_table()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('transactionCurrency');
        $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
        $this->db->from('fleet_fuelusagemaster');
        $data['currency'] = $this->db->get()->row_array();

        $this->db->select('*,CONCAT(vehicleCode,\'-\',VehicleNo) as vehicale,DATE_FORMAT(receiptDate,\'' . $convertFormat . '\') AS receiptDate');
        $this->db->join('fleet_glconfiguration', '(fleet_glconfiguration.glConfigAutoID = fleet_fuelusagedetails.glConfigAutoID)', 'left');
        $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
        $this->db->from('fleet_fuelusagedetails');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_fuelPurchased_edit()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(fleet_fuelusagedetails.receiptDate,\'' . $convertFormat . '\') AS receiptDate');
        $this->db->where('fuelusageDetailsID', trim($this->input->post('fuelusageDetailsID') ?? ''));
        $this->db->from('fleet_fuelusagedetails');
        $this->db->join('fleet_fuelusagemaster', 'fleet_fuelusagemaster.fuelusageID = fleet_fuelusagedetails.fuelusageID');
        return $this->db->get()->row_array();
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'FU');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }


    function fetch_template_data($fuelusageID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('fuelusageID,transactionCurrency, transactionCurrencyDecimalPlaces,referenceNumber,companyFinanceYear,companyFinancePeriod,documentCode, DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,supplier,narration,segmentCode,approvedYN');
        $this->db->where('fuelusageID', $fuelusageID);
        $this->db->from('fleet_fuelusagemaster');
        $data['master'] = $this->db->get()->row_array();

        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);


        $this->db->select('fleet_fuelusagedetails.segmentCode as segmentCode,CONCAT(vehicleCode,\'-\',VehicleNo) as vehicale,fleet_fuelusagedetails.driverName as driverName,fleet_fuel_type.description as fuel,fleet_glconfiguration.glConfigDescription,fuelusageDetailsID,vehicleMasterID,driverName,VehicleNo,vehicleCode,startKm,endKm,totalAmount,comment,fleet_fuelusagedetails.fuelRate,DATE_FORMAT(receiptDate,\'' . $convertFormat . '\') AS receiptDate');
        $this->db->join('fleet_glconfiguration', '(fleet_glconfiguration.glConfigAutoID = fleet_fuelusagedetails.glConfigAutoID)', 'left');
        $this->db->join('fleet_fuel_type', '(fleet_fuel_type.fuelTypeID = fleet_fuelusagedetails.fuelTypeID)', 'left');
        $this->db->where('fuelusageID', $fuelusageID);
        $this->db->from('fleet_fuelusagedetails');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $fuelusageID);
        $this->db->where('documentID', 'FU');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
    }

    function load_fuelPurchase_request_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate');
        $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
        $this->db->from('fleet_fuelusagemaster');
        return $this->db->get()->row_array();
    }

    function fetch_item_issue_detail_edit()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,fleet_fuelusagedetails.segmentID as segmentIDdet,fleet_fuelusagedetails.segmentCode as segmentCodedet,DATE_FORMAT(fleet_fuelusagedetails.receiptDate,\'' . $convertFormat . '\') AS receiptDate');
        $this->db->where('fuelusageDetailsID', trim($this->input->post('fuelusageDetailsID') ?? ''));
        $this->db->from('fleet_fuelusagedetails');
        $this->db->join('fleet_vehiclemaster', 'fleet_vehiclemaster.vehicleMasterID = fleet_fuelusagedetails.vehicleMasterID');
        return $this->db->get()->row_array();
    }

    public function array_group_sum($data)
    {
        $groups = array();
        $key = 0;
        foreach ($data as $item)
        {
            $key = $item['gl_auto_id'] . $item['segment_id'];
            if (!array_key_exists($key, $groups))
            {
                $groups[$key] = array(
                    'auto_id' => $item['auto_id'],
                    'gl_auto_id' => $item['gl_auto_id'],
                    'gl_code' => $item['gl_code'],
                    'secondary' => $item['secondary'],
                    'gl_desc' => $item['gl_desc'],
                    'gl_type' => $item['gl_type'],
                    'segment' => $item['segment'],
                    'segment_id' => $item['segment_id'],
                    'gl_dr' => $item['gl_dr'],
                    'gl_cr' => $item['gl_cr'],
                    'amount_type' => $item['amount_type'],
                    'isAddon' => $item['isAddon'],
                    'subLedgerType' => $item['subLedgerType'],
                    'subLedgerDesc' => $item['subLedgerDesc'],
                    'partyContractID' => $item['partyContractID'],
                    'partyType' => $item['partyType'],
                    'partyAutoID' => $item['partyAutoID'],
                    'partySystemCode' => $item['partySystemCode'],
                    'partyName' => $item['partyName'],
                    'partyCurrencyID' => $item['partyCurrencyID'],
                    'partyCurrency' => $item['partyCurrency'],
                    'transactionExchangeRate' => $item['transactionExchangeRate'],
                    'companyLocalExchangeRate' => $item['companyLocalExchangeRate'],
                    'companyReportingExchangeRate' => $item['companyReportingExchangeRate'],
                    'partyExchangeRate' => $item['partyExchangeRate'],
                    'partyCurrencyAmount' => $item['partyCurrencyAmount'],
                    'partyCurrencyDecimalPlaces' => $item['partyCurrencyDecimalPlaces'],
                );
            }
            else
            {
                $groups[$key]['gl_dr'] = $groups[$key]['gl_dr'] + $item['gl_dr'];
                $groups[$key]['gl_cr'] = $groups[$key]['gl_cr'] + $item['gl_cr'];
            }
            $key++;
        }
        $groups = array_values($groups);
        return $groups;
    }

    function fetch_double_entry_fleet_fuel_usage_data($fuelusageID, $code = null)
    {
        $gl_array = array();
        $cost_arr = array();
        $assat_arr = array();
        $gl_array['gl_detail'] = array();
        $this->db->select('*');
        $this->db->where('fleet_fuelusagemaster.fuelusageID', $fuelusageID);
        $this->db->join('fleet_fuelusagedetails', 'fleet_fuelusagedetails.fuelusageID=fleet_fuelusagemaster.fuelusageID');
        $master = $this->db->get('fleet_fuelusagemaster')->row_array();

        $this->db->select('*');
        // $this->db->where('itemCategory !=', 'Non Inventory');
        $this->db->where('fuelusageID', $fuelusageID);
        $detail = $this->db->get('fleet_fuelusagedetails')->result_array();
        for ($i = 0; $i < count($detail); $i++)
        {
            $assa_data_arr['auto_id'] = 0;
            $assa_data_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
            $assa_data_arr['gl_code'] = $detail[$i]['BLSystemGLCode'];
            $assa_data_arr['secondary'] = $detail[$i]['BLGLCode'];
            $assa_data_arr['gl_desc'] = $detail[$i]['BLDescription'];
            $assa_data_arr['gl_type'] = $detail[$i]['BLType'];
            $assa_data_arr['segment_id'] = $detail[$i]['segmentID'];
            $assa_data_arr['segment'] = $detail[$i]['segmentCode'];
            //   $assa_data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            //    $assa_data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $assa_data_arr['gl_dr'] = 0;
            $assa_data_arr['gl_cr'] = $detail[$i]['totalAmount'];
            $assa_data_arr['amount_type'] = 'cr';
            $assa_data_arr['isAddon'] = 0;
            $assa_data_arr['subLedgerType'] = 0;
            $assa_data_arr['subLedgerDesc'] = null;
            $assa_data_arr['partyContractID'] = null;
            $assa_data_arr['partyType'] = null;
            $assa_data_arr['partyAutoID'] = null;
            $assa_data_arr['partySystemCode'] = null;
            $assa_data_arr['partyName'] = null;
            $assa_data_arr['partyCurrencyID'] = null;
            $assa_data_arr['partyCurrency'] = null;
            $assa_data_arr['transactionExchangeRate'] = 1;
            $assa_data_arr['companyLocalExchangeRate'] = 1;
            $assa_data_arr['companyReportingExchangeRate'] = 1;
            $assa_data_arr['partyExchangeRate'] = 1;
            $assa_data_arr['partyCurrencyAmount'] = 1;
            $assa_data_arr['partyCurrencyDecimalPlaces'] = 2;
            array_push($assat_arr, $assa_data_arr);

            $data_arr['auto_id'] = 0;
            $data_arr['gl_auto_id'] = $detail[$i]['PLGLAutoID'];
            $data_arr['gl_code'] = $detail[$i]['PLSystemGLCode'];
            $data_arr['secondary'] = $detail[$i]['PLGLCode'];
            $data_arr['gl_desc'] = $detail[$i]['PLDescription'];
            $data_arr['gl_type'] = $detail[$i]['PLType'];
            $data_arr['segment_id'] = $detail[$i]['segmentID'];
            $data_arr['segment'] = $detail[$i]['segmentCode'];
            //   $data_arr['projectID'] = isset($detail[$i]['projectID']) ? $detail[$i]['projectID'] : null;
            //   $data_arr['projectExchangeRate'] = isset($detail[$i]['projectExchangeRate']) ? $detail[$i]['projectExchangeRate'] : null;
            $data_arr['gl_dr'] = $detail[$i]['totalAmount'];
            $data_arr['gl_cr'] = 0;
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = null;
            $data_arr['partyAutoID'] = null;
            $data_arr['partySystemCode'] = null;
            $data_arr['partyName'] = null;
            $data_arr['partyCurrencyID'] = null;
            $data_arr['partyCurrency'] = null;
            $data_arr['transactionExchangeRate'] = null;
            $data_arr['companyLocalExchangeRate'] = null;
            $data_arr['companyReportingExchangeRate'] = null;
            $data_arr['partyExchangeRate'] = null;
            $data_arr['partyCurrencyAmount'] = null;
            $data_arr['partyCurrencyDecimalPlaces'] = null;
            $data_arr['amount_type'] = 'dr';
            array_push($cost_arr, $data_arr);
        }

        $assat_arr = $this->array_group_sum($assat_arr);
        $cost_arr = $this->array_group_sum($cost_arr);

        $gl_array['gl_detail'] = $assat_arr;
        foreach ($cost_arr as $key => $value)
        {
            array_push($gl_array['gl_detail'], $value);
        }

        $gl_array['currency'] = $master['companyLocalCurrency'];
        $gl_array['decimal_places'] = $master['companyLocalCurrencyDecimalPlaces'];
        $gl_array['code'] = 'FU';
        $gl_array['name'] = 'Fuel Usage';
        $gl_array['primary_Code'] = $master['BLSystemGLCode'];
        $gl_array['date'] = $master['documentDate'];
        $gl_array['finance_year'] = $master['companyFinanceYear'];
        $gl_array['finance_period'] = $master['companyFinancePeriod'];
        $gl_array['master_data'] = $master;
        return $gl_array;
    }

    function fuel_usage_confirmation()
    {
        $this->db->select('fuelusageDetailsID');
        $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
        $this->db->from('fleet_fuelusagedetails');
        $record = $this->db->get()->result_array();
        if (empty($record))
        {
            return array('w', 'There are no records to confirm this document!');
        }
        else
        {
            $this->load->library('Approvals');
            $this->db->select('*');
            $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('fleet_fuelusagemaster');
            $row = $this->db->get()->row_array();
            if (!empty($row))
            {
                return array('w', 'Document already confirmed');
            }
            else
            {
                $this->db->select('*');
                $this->db->where('fuelusageID', trim($this->input->post('fuelusageID') ?? ''));
                $this->db->from('fleet_fuelusagemaster');
                $row = $this->db->get()->row_array();

                $validate_code = validate_code_duplication($row['documentCode'], 'documentCode', trim($this->input->post('fuelusageID') ?? ''), 'fuelusageID', 'fleet_fuelusagemaster');
                if (!empty($validate_code))
                {
                    $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    return array(false, 'error');
                }

                $approvals_status = $this->approvals->CreateApproval('FU', $row['fuelusageID'], $row['documentCode'], 'Fuel Usage', 'fleet_fuelusagemaster', 'fuelusageID');
                if ($approvals_status == 1)
                {
                    $fuelusageID = trim($this->input->post('fuelusageID') ?? '');

                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        //    'transactionAmount' => '',
                    );
                    $this->db->where('fuelusageID', $fuelusageID);
                    $this->db->update('fleet_fuelusagemaster', $data);

                    return array('s', 'Fuel Usage : Confirmed Successfully. ');
                }
                else if ($approvals_status == 3)
                {
                    return array('w', 'There are no users exist to perform approval for this document.');
                }
                else
                {
                    return array('e', 'something went wrong');
                }
            }
        }
    }

    function save_fuel_usage_approval()
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        $system_id = trim($this->input->post('fuelusageID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'FU');
        $comapnyid = current_companyID();

        if ($approvals_status == 1)
        {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $date_format_policy = date_format_policy();
            $convertdateformat = convert_date_format_sql();

            $masterFuel = $this->db->query("SELECT * from fleet_fuelusagemaster where fuelusageID = $system_id AND companyID = $comapnyid")->row_array();
            $documentDate = $masterFuel['documentDate'];
            $documetdateconverted = input_format_date($documentDate, $date_format_policy);

            $finacialYearid = $this->db->query("SELECT companyFinanceYearID,companyFinancePeriodID,dateFrom,dateTo,concat(DATE_FORMAT(dateFrom,'{$convertdateformat}'),\" - \", DATE_FORMAT(dateTo,'{$convertdateformat}'))as companyFinanceYear FROM srp_erp_companyfinanceperiod WHERE '$documetdateconverted' BETWEEN dateFrom AND dateTo AND isActive = 1 AND companyID = $comapnyid")->row_array();
            $supplierid = $masterFuel['supplierAutoID'];
            $supplierdetails = $this->db->query("select *,concat(supplierAddress1,\" , \", supplierAddress2)as supplieraddress from  srp_erp_suppliermaster where supplierAutoID =       {$supplierid} AND companyID = $comapnyid")->row_array();

            $currencymaster = $this->db->query("select CurrencyCode from srp_erp_currencymaster where currencyID = '{$masterFuel['transactionCurrencyID']}'")->row_array();

            $detailtbl = $this->db->query("select * from fleet_fuelusagedetails where companyID = $comapnyid AND fuelusageID = $system_id")->result_array();

            if ($masterFuel['linkedToIOUYN'] != 1)
            {
                if (empty($finacialYearid))
                {
                    $this->session->set_flashdata('e', 'Date not between financial year');
                    return false;
                    exit();
                }
                else
                {
                    $this->db->where('fuelusageID', $system_id);
                    $this->db->update('fleet_fuelusagemaster', $data);
                    /*      $segmentcode = explode('|', $segment);*/
                    $datas['documentID'] = 'BSI';
                    $datas['isSytemGenerated'] = 1;
                    $datas['invoiceType'] = 'Standard';
                    $datas['companyFinanceYearID'] = $finacialYearid['companyFinanceYearID'];
                    $datas['companyFinanceYear'] = $finacialYearid['companyFinanceYear'];
                    $datas['FYBegin'] = $finacialYearid['dateFrom'];
                    $datas['FYEnd'] = $finacialYearid['dateTo'];
                    $datas['companyFinancePeriodID'] = $finacialYearid['companyFinancePeriodID'];
                    $datas['comments'] = $masterFuel['narration'] . '(' . $masterFuel['documentCode'] . ')';
                    $datas['supplierID'] = $masterFuel['supplierAutoID'];
                    $datas['supplierCode'] = $masterFuel['supplierSystemCode'];
                    $datas['supplierName'] = $masterFuel['supplier'];
                    $datas['supplierAddress'] = $supplierdetails['supplierAddress1'];
                    $datas['supplierTelephone'] = $supplierdetails['supplierTelephone'];
                    $datas['supplierFax'] = $supplierdetails['supplierFax'];
                    $datas['supplierliabilityAutoID'] = $masterFuel['supplierliabilityAutoID'];
                    $datas['supplierliabilitySystemGLCode'] = $masterFuel['supplierliabilitySystemGLCode'];
                    $datas['supplierliabilityGLAccount'] = $masterFuel['supplierliabilityGLAccount'];
                    $datas['supplierliabilityDescription'] = $masterFuel['supplierliabilityDescription'];
                    $datas['supplierliabilityType'] = $masterFuel['supplierliabilityType'];
                    $datas['bookingInvCode'] = 0;
                    $datas['bookingDate'] = $documetdateconverted;
                    $datas['invoiceDate'] = $documetdateconverted;
                    $datas['invoiceDueDate'] = $documetdateconverted;
                    $datas['invoiceDueDate'] = $documetdateconverted;
                    $datas['segmentID'] = $masterFuel['segmentID'];
                    $datas['segmentCode'] = $masterFuel['segmentCode'];
                    $datas['transactionCurrencyID'] = $masterFuel['transactionCurrencyID'];
                    $datas['transactionCurrency'] = $currencymaster['CurrencyCode'];
                    $datas['transactionExchangeRate'] = 1;
                    $datas['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($datas['transactionCurrencyID']);
                    $datas['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $datas['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $default_currency = currency_conversionID($datas['transactionCurrencyID'], $datas['companyLocalCurrencyID']);
                    $datas['companyLocalExchangeRate'] = $default_currency['conversion'];
                    $datas['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $datas['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $datas['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $reporting_currency = currency_conversionID($datas['transactionCurrencyID'], $datas['companyReportingCurrencyID']);
                    $datas['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $datas['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                    $datas['supplierCurrencyID'] = $supplierdetails['supplierCurrencyID'];
                    $datas['supplierCurrency'] = $supplierdetails['supplierCurrency'];
                    $supplierCurrency = currency_conversionID($datas['transactionCurrencyID'], $datas['supplierCurrencyID']);
                    $datas['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                    $datas['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                    $datas['companyID'] = $comapnyid;
                    $datas['companyCode'] = $this->common_data['company_data']['company_code'];
                    $datas['createdUserGroup'] = $this->common_data['user_group'];
                    $datas['createdPCID'] = $this->common_data['current_pc'];
                    $datas['createdUserID'] = $this->common_data['current_userID'];
                    $datas['createdDateTime'] = $this->common_data['current_date'];
                    $datas['createdUserName'] = $this->common_data['current_user'];
                    $datas['modifiedPCID'] = $this->common_data['current_pc'];
                    $datas['modifiedUserID'] = $this->common_data['current_userID'];
                    $datas['modifiedDateTime'] = $this->common_data['current_date'];
                    $datas['modifiedUserName'] = $this->common_data['current_user'];
                    $datas['timestamp'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_paysupplierinvoicemaster', $datas);
                    $last_id = $this->db->insert_id();
                    $this->db->trans_complete();


                    foreach ($detailtbl as $val)
                    {
                        $gldes = $this->db->query("select systemAccountCode,GLSecondaryCode,GLDescription,subCategory from srp_erp_chartofaccounts where companyID = $comapnyid And GLAutoID = {$val['GLAutoID']}")->row_array();

                        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
                        $this->db->where('InvoiceAutoID', $last_id);
                        $master_suplier = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

                        $datadetail['InvoiceAutoID'] = $last_id;
                        $datadetail['grvType'] = 'Standard';
                        $datadetail['systemGLCode'] = $val['SystemGLCode'];
                        $datadetail['GLCode'] = $val['GLCode'];
                        $datadetail['GLAutoID'] = $val['GLAutoID'];
                        $datadetail['GLDescription'] = $val['GLDescription'];
                        $datadetail['GLType'] = $val['GLType'];
                        $datadetail['description'] = 'Fuel Usage';
                        $datadetail['transactionAmount'] = $val['totalAmount'];
                        $datadetail['transactionExchangeRate'] = $masterFuel['transactionExchangeRate'];
                        $datadetail['companyLocalAmount'] = ($val['totalAmount'] / $masterFuel['companyLocalExchangeRate']);
                        $datadetail['companyLocalExchangeRate'] = $masterFuel['companyLocalExchangeRate'];
                        $datadetail['companyReportingAmount'] = ($val['totalAmount'] / $masterFuel['companyReportingExchangeRate']);
                        $datadetail['companyReportingExchangeRate'] = $masterFuel['companyReportingExchangeRate'];
                        $datadetail['supplierAmount'] = ($val['totalAmount'] / $master_suplier['supplierCurrencyExchangeRate']);
                        $datadetail['supplierCurrencyExchangeRate'] = $master_suplier['supplierCurrencyExchangeRate'];
                        $datadetail['segmentID'] = $val['segmentID'];
                        $datadetail['segmentCode'] = $val['segmentCode'];

                        $datadetail['companyCode'] = $this->common_data['company_data']['company_code'];
                        $datadetail['companyID'] = $this->common_data['company_data']['company_id'];
                        $datadetail['modifiedPCID'] = $this->common_data['current_pc'];
                        $datadetail['modifiedUserID'] = $this->common_data['current_userID'];
                        $datadetail['modifiedUserName'] = $this->common_data['current_user'];
                        $datadetail['modifiedDateTime'] = $this->common_data['current_date'];
                        $datadetail['createdUserGroup'] = $this->common_data['user_group'];
                        $datadetail['createdPCID'] = $this->common_data['current_pc'];
                        $datadetail['createdUserID'] = $this->common_data['current_userID'];
                        $datadetail['createdUserName'] = $this->common_data['current_user'];
                        $datadetail['createdDateTime'] = $this->common_data['current_date'];

                        $this->db->insert('srp_erp_paysupplierinvoicedetail', $datadetail);
                        $this->supplier_invoice_confirmation($last_id);
                        $this->db->trans_complete();
                    }
                }
            }
            else
            {
                $this->db->where('fuelusageID', $system_id);
                $this->db->update('fleet_fuelusagemaster', $data);
            }
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Fuel Usage Approved Successfully.');
            return true;
        }
    }

    function SaveNewFuel_Master()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $fuelTypeID = trim($this->input->post('fuelTypeID') ?? '');


        $data['description'] = $this->input->post('fuelType');
        $data['fuelRate'] = $this->input->post('fuel_rate');
        $data['uomid'] = $this->input->post('UOMid');


        if ($fuelTypeID)
        {

            $data['modifiedpc'] = $this->common_data['current_pc'];
            $data['modifieduser'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('fuelTypeID', trim($this->input->post('fuelTypeID') ?? ''));
            $update = $this->db->update('fleet_fuel_type', $data);
            if ($update)
            {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    return array('error' => 1, 'Fuel Update Failed ');
                }
                else
                {
                    $this->db->trans_commit();

                    return array('error' => 0, 'Fuel Updated Successfully.');
                }
            }
        }
        else
        {

            $data['createdpc'] = $this->common_data['current_pc'];
            $data['createduser'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $this->db->insert('fleet_fuel_type', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();

                return array('error' => 1, 'message' => 'Failed to add Fuel Type Master', $last_id);
            }
            else
            {
                $this->db->trans_commit();

                return array('error' => 0, 'message' => 'Fuel Type Master added successfully.', $last_id);
            }
        }
    }

    function EditNewFuel_Master()
    {
        $fuelType = $this->input->post('fuelType_edit');
        $fuelTypeID = $this->input->post('fuelTypeID_edit');


        $this->db->select('*');
        $this->db->where('description', trim($fuelType));
        //      $this->db->where('fuelTypeID_edit', trim($fuelType));
        $this->db->from('fleet_fuel_type');
        $po_approved = $this->db->get()->row_array();


        if (!empty($po_approved && $po_approved['fuelTypeID'] != $fuelTypeID))
        {
            return array('error' => 1, 'message' => 'Fuel Description already exist');
        }
        else
        {
            $companyID = $this->common_data['company_data']['company_id'];
            $fuelTypeID = trim($this->input->post('fuelTypeID_edit') ?? '');


            $data['description'] = $this->input->post('fuelType_edit');
            $data['fuelRate'] = $this->input->post('fuel_rate_edit');
            $data['uomid'] = $this->input->post('UOMid');
            $data['modifiedpc'] = $this->common_data['current_pc'];
            $data['modifieduser'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('fuelTypeID', trim($this->input->post('fuelTypeID_edit') ?? ''));
            $update = $this->db->update('fleet_fuel_type', $data);
            if ($update)
            {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    return array('error' => 1, 'message' => 'Fuel Update Failed ');
                }
                else
                {
                    $this->db->trans_commit();

                    return array('error' => 0, 'message' => 'Fuel Updated Successfully.');
                }
            }
        }
    }

    function delete_fuel()
    {
        $fuelTypeID = trim($this->input->post('fuelTypeID') ?? '');

        $this->db->trans_start();

        $this->db->where('fuelTypeID', $fuelTypeID)->delete('fleet_fuel_type');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }

    /*===================== Report ============================*/

    function get_fuel_usage_report()
    {
        $convertFormat = convert_date_format_sql();
        $supplierID = $this->input->post('supplierAutoID');
        $vehicleID = $this->input->post('vehicleMasterID');

        $supplierIDs = "'" . implode("', '", $supplierID) . "'";
        $vehicleIDs = "'" . implode("', '", $vehicleID) . "'";

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        if (!empty($datefrom) && !empty($dateto))
        {
            $date .= " AND ( documentDate BETWEEN '" . $datefromconvert . "' AND '" . $datetoconvert . " ')";
        }

        $qry = "SELECT
          documentCode,
          fleet_fuelusagemaster.segmentCode,
          fleet_fuelusagemaster.transactionCurrencyDecimalPlaces,
          documentID,
          fleet_fuelusagemaster.confirmedYN,
          fleet_fuelusagemaster.approvedYN,
          fleet_fuelusagemaster.fuelusageID,
          supplierAutoID,supplier,
         fleet_fuelusagemaster.documentDate,
         fleet_fuelusagedetails.fuelusageDetailsID,
         fleet_fuelusagedetails.expKMperLiter,
         fleet_fuelusagedetails.VehicleNo,
         fleet_fuelusagedetails.fuelTypeID,
         fleet_fuelusagedetails.startKm,
         fleet_fuelusagedetails.endKm,
         fleet_fuelusagedetails.totalAmount,
         fleet_fuelusagedetails.FuelType,
         fleet_fuelusagedetails.fuelRate
      FROM
          fleet_fuelusagemaster
      LEFT JOIN fleet_fuelusagedetails ON fleet_fuelusagedetails.fuelusageID = fleet_fuelusagemaster.fuelusageID
      WHERE
          fleet_fuelusagemaster.approvedYN = 1
          AND
          fleet_fuelusagemaster.companyID = " . current_companyID() . "
         $date
          AND fleet_fuelusagedetails.vehicleMasterID IN ($vehicleIDs)
          AND fleet_fuelusagemaster.supplierAutoID IN ($supplierIDs)";
        $output = $this->db->query($qry)->result_array();

        //  var_dump($output);
        return $output;
    }

    function fetch_report_view_data($documentCode)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('fuelusageID,transactionCurrency, transactionCurrencyDecimalPlaces,referenceNumber,companyFinanceYear,companyFinancePeriod,documentCode, DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,supplier,narration,segmentCode');
        $this->db->where('documentCode', $documentCode);
        $this->db->from('fleet_fuelusagemaster');
        $data['master'] = $this->db->get()->row_array();

        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $fuelID = $this->db->query("SELECT * FROM fleet_fuelusagemaster WHERE documentCode ='" . $documentCode . "'")->row_array();
        $fuelusageID = $fuelID['fuelusageID'];

        $this->db->select('fleet_glconfiguration.glConfigDescription,fleet_fuel_type.description as fuel,fuelusageDetailsID,vehicleMasterID,driverName,VehicleNo,vehicleCode,startKm,endKm,totalAmount,comment,fleet_fuelusagedetails.fuelRate,DATE_FORMAT(receiptDate,\'' . $convertFormat . '\') AS receiptDate,fleet_fuelusagedetails.segmentCode as segmentCode,VehicleNo,vehicleCode,CONCAT(vehicleCode,\'-\',VehicleNo) as vehicale');
        $this->db->join('fleet_glconfiguration', '(fleet_glconfiguration.glConfigAutoID = fleet_fuelusagedetails.glConfigAutoID)', 'left');
        $this->db->join('fleet_fuel_type', '(fleet_fuel_type.fuelTypeID = fleet_fuelusagedetails.fuelTypeID)', 'left');
        $this->db->where('fuelusageID', $fuelusageID);
        $this->db->from('fleet_fuelusagedetails');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $fuelusageID);
        $this->db->where('documentID', 'FU');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
    }

    /* ============= GL Configuration ======================= */

    function save_new_GL_config()
    {
        $this->db->trans_start();
        $glConfigDescription = $this->input->post('glConfigDescription');
        $glConfigAutoID = $this->input->post('glConfigAutoID');
        $companyID = $this->common_data['company_data']['company_id'];
        $glcd = explode('|', trim($this->input->post('GLCode') ?? ''));
        $data['glConfigDescription'] = trim_desc($this->input->post('glConfigDescription'));
        $data['glAutoID'] = trim_desc($this->input->post('glAutoID'));
        $data['glCode'] = trim_desc($glcd[0]);
        $data['glCodeDescription'] = trim_desc($glcd[1]);
        if (trim($this->input->post('glConfigAutoID') ?? ''))
        {
            $descexist = $this->db->query("SELECT glConfigAutoID FROM fleet_glconfiguration WHERE glConfigDescription='$glConfigDescription' AND glConfigAutoID !=$glConfigAutoID AND companyID = $companyID; ")->row_array();
        }
        else
        {
            $descexist = $this->db->query("SELECT glConfigAutoID FROM fleet_glconfiguration WHERE glConfigDescription='$glConfigDescription' AND companyID = $companyID; ")->row_array();
        }

        if (trim($this->input->post('glConfigAutoID') ?? ''))
        {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            if ($descexist)
            {
                return array('e', 'Description Already Exist');
            }
            else
            {
                $this->db->where('glConfigAutoID', trim($this->input->post('glConfigAutoID') ?? ''));
                $this->db->update('fleet_glconfiguration', $data);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                return array('e', 'GL Category Updating  Failed');
            }
            else
            {
                return array('s', 'GL Category Updated Successfully');
            }
        }
        else
        {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            if ($descexist)
            {
                return array('e', 'Description Already Exist');
            }
            else
            {
                $this->db->insert('fleet_glconfiguration', $data);
            }
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                return array('e', 'GL Configuration Save Failed');
            }
            else
            {
                return array('s', 'GL Configuration Saved Successfully');
            }
        }
    }

    function deleteGLconfig()
    {
        $this->db->select('fuelusageDetailsID');
        $this->db->where('glConfigAutoID', trim($this->input->post('glConfigAutoID') ?? ''));
        $this->db->where('companyID', current_companyID());
        $this->db->from('fleet_fuelusagedetails');
        $categoryExsist = $this->db->get()->row_array();
        if ($categoryExsist)
        {
            return array('e', 'GL has been used');
        }
        else
        {
            $this->db->delete('fleet_glconfiguration', array('glConfigAutoID' => trim($this->input->post('glConfigAutoID') ?? '')));
            return array('s', 'Deleted Successfully');
        }
    }

    function editGLconfig()
    {
        $this->db->select('*');
        $this->db->where('glConfigAutoID', trim($this->input->post('glConfigAutoID') ?? ''));
        $this->db->from('fleet_glconfiguration');
        return $this->db->get()->row_array();
    }



    function add_new_model()
    {
        $brand = trim($this->input->post('brand') ?? '');
        $model = trim($this->input->post('Model') ?? '');

        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT modelID FROM fleet_brand_model WHERE brandID = $brand AND description='$model' ")->row('modelID');

        if (isset($isExist))
        {
            return array('e', 'This Vehicle Model already Exists');
        }
        else
        {

            $data = array(
                'description' => $model,
                'brandID' => $brand,


                'createdUserGroup' => current_user_group(),
                'createduser' => current_pc(),
                'createdpc' => current_employee(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('fleet_brand_model', $data);
            if ($this->db->affected_rows() > 0)
            {
                $modelid = $this->db->insert_id();
                return array('s', 'Vehicle Model is created successfully.', $modelid);
            }
            else
            {
                return array('e', 'Error in Model Creating');
            }
        }
    }

    function asset_add_new_model()
    {
        $brand = trim($this->input->post('brand') ?? '');
        $model = trim($this->input->post('Model') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $isExist = $this->db->query("SELECT modelID FROM fleet_brand_model WHERE brandID = $brand AND description='$model' ")->row('modelID');

        if (isset($isExist))
        {
            return array('e', 'This Sub Category already Exists');
        }
        else
        {

            $data = array(
                'description' => $model,
                'brandID' => $brand,
                'aset_type' => $type,
                'createdUserGroup' => current_user_group(),
                'createduser' => current_pc(),
                'createdpc' => current_employee(),
                'createdDateTime' => current_date()
            );

            $this->db->insert('fleet_brand_model', $data);
            if ($this->db->affected_rows() > 0)
            {
                $modelid = $this->db->insert_id();
                return array('s', 'Sub category is created successfully.', $modelid);
            }
            else
            {
                return array('e', 'Error in Sub Category Creating');
            }
        }
    }

    function add_new_model_brand()
    {
        $brand = trim($this->input->post('brand') ?? '');

        $companyid = current_companyID();

        $isExist = $this->db->query("SELECT brandID FROM fleet_brand_master WHERE description='$brand' AND companyID = $companyid ")->row('brandID');

        if (isset($isExist))
        {
            return array('e', 'This Vehicle Brand already Exists');
        }
        else
        {

            $data = array(
                'description' => $brand,
                'status' => 'A',

                'createdUserGroup' => current_user_group(),
                'createduser' => current_pc(),
                'createdpc' => current_employee(),
                'createdDateTime' => current_date(),
                'companyID' => current_companyID()
            );

            $this->db->insert('fleet_brand_master', $data);
            if ($this->db->affected_rows() > 0)
            {
                $brandid = $this->db->insert_id();
                return array('s', 'Brand is created successfully.', $brandid);
            }
            else
            {
                return array('e', 'Error in Model Creating');
            }
        }
    }


    function re_open_fuel_usage()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('fuelusageID', trim($this->input->post('Fuelusageid') ?? ''));
        $this->db->update('fleet_fuelusagemaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function vehicale_confirmation()
    {
        $this->db->trans_start();
        $vehicalemastrerid = $this->input->post('vehicleMasterID');
        $data = array(
            'confirmedYN' => 1,
            'confirmedDate' => $this->common_data['current_date'],
            'confimedbyEmpID' => $this->common_data['current_userID'],
            'confimedbyEmpName' => $this->common_data['current_user']
        );
        $this->db->where('vehicleMasterID', $vehicalemastrerid);
        $this->db->update('fleet_vehiclemaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('error' => 1, 'message' => 'some went wrong!');
        }
        else
        {
            $this->db->trans_commit();
            return array('error' => 0, 'message' => 'Document confirmed successfully ');
        }
    }

    function fetchvehicalemasterdettails()
    {
        $companyid = current_companyID();
        $vehicalemasterid = $this->input->post('vehicalemasterid');
        $data = $this->db->query("select mastervehicale.*,CONCAT( brand_description, ' - ', model_description ) AS titlevehicale,CONCAT('Create Maintenance (',brand_description, ' - ', model_description,')') AS titlevehicalecreate,IFNULL(meterreading.maximumpreviousreading,'-') as maximumpreviousreading FROM fleet_vehiclemaster mastervehicale LEFT JOIN (SELECT IFNULL( MAX( current_meter_reading ),0) AS maximumpreviousreading,vehicleMasterID FROM fleet_meter_reading meterreadingmaster where  vehicleMasterID = $vehicalemasterid AND companyID = $companyid)meterreading on meterreading.vehicleMasterID = mastervehicale.vehicleMasterID WHERE mastervehicale.companyID = $companyid AND mastervehicale.vehicleMasterID = $vehicalemasterid")->row_array();
        $data['vehicaleIMG'] = $this->s3->createPresignedRequest('uploads/Fleet/VehicleImg/' . $data['vehicleImage'], '1 hour');

        return $data;
    }

    function save_vehicalemaintenanceheader()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $mainternacedatefrom = $this->input->post('maintenancedatefrom');
        $mainternacedateto = $this->input->post('maintenancedateto');
        $mainternacedate = $this->input->post('nextmaintenancedate');
        $vehiclemaintenaceid = $this->input->post('vehicalemaintenaceid');
        $vehiclemasterid = $this->input->post('vehicalemasterid');
        $documentdate = $this->input->post('documentdate');
        $lastmaintenacedate = $this->input->post('lastmaintenancedate');
        $datestatus = $this->input->post('stausdate');
        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $currentmeaterreading = $this->input->post('currentmeterreading');
        $maintenacedoneby = $this->input->post('maintenancedoneby');

        $segment = explode('|', trim($this->input->post('segment') ?? ''));

        $format_mainternacedatefrom = null;
        $format_mainternacedateto = null;
        $format_mainternacedate = null;
        $format_documentdate = null;
        $format_last_maintnenacedate = null;
        $format_datestatus = null;

        $initialmilage = $this->db->query("SELECT initialMilage FROM `fleet_vehiclemaster` where vehicleMasterID = $vehiclemasterid And companyID = $companyID")->row_array();
        $milage = $this->db->query("SELECT
	meterreadingmaster.*,
	IFNULL( MAX( current_meter_reading ), IFNULL( vehicalemasterdetail.initialMilage, 0 ) ) AS maximumpreviousreading 
FROM
	fleet_meter_reading meterreadingmaster
	INNER JOIN fleet_vehiclemaster vehicalemasterdetail ON vehicalemasterdetail.vehicleMasterID = meterreadingmaster.vehicleMasterID 
WHERE
	meterreadingmaster.vehicleMasterID = $vehiclemasterid
	AND meterreadingmaster.companyID = $companyID")->row_array();



        if (isset($datestatus) && !empty($datestatus))
        {
            $format_datestatus = input_format_date($datestatus, $date_format_policy);
        }

        if (isset($mainternacedatefrom) && !empty($mainternacedatefrom))
        {
            $format_mainternacedatefrom = input_format_date($mainternacedatefrom, $date_format_policy);
        }
        if (isset($lastmaintenacedate) && !empty($lastmaintenacedate))
        {
            $format_last_maintnenacedate = input_format_date($lastmaintenacedate, $date_format_policy);
        }
        if (isset($documentdate) && !empty($documentdate))
        {
            $format_documentdate = input_format_date($documentdate, $date_format_policy);
        }
        if (isset($mainternacedate) && !empty($mainternacedate))
        {
            $format_mainternacedate = input_format_date($mainternacedate, $date_format_policy);
        }
        if (isset($mainternacedateto) && !empty($mainternacedateto))
        {
            $format_mainternacedateto = input_format_date($mainternacedateto, $date_format_policy);
        }




        $data['status'] = 1;
        $data['maintenanceBy'] = $maintenacedoneby;
        if ($data['maintenanceBy'] == 2)
        {
            $data['maintenanceCompanyID'] = $this->input->post('maintenancecompany');
            $data['warehouseAutoID'] = null;
            $data['expenseGLAutoID'] = $this->input->post('glcode');
            $data['supplierDocRefNo'] = $this->input->post('supplierreferenceno');
        }
        else if ($data['maintenanceBy'] == 1)
        {
            $data['maintenanceCompanyID'] = null;
            $data['warehouseAutoID'] = $this->input->post('warehouse');
            $data['expenseGLAutoID'] = null;
            $data['supplierDocRefNo'] = null;
        }

        else
        {
            $data['maintenanceCompanyID'] = null;
            $data['expenseGLAutoID'] = null;
            $data['supplierDocRefNo'] = null;
        }


        $data['vehicleMasterID'] = $vehiclemasterid;
        $data['segmentID'] = $segment[0];
        $data['transactionCurrencyID'] = $this->input->post('transactioncurrencyid');
        $data['lastMaintenanceDate'] = $format_last_maintnenacedate;
        $data['currentMeterReading'] = $currentmeaterreading;
        $data['maintenanceType'] = $this->input->post('maintenancetype');
        $data['lastMaintenanceOnKM'] = $this->input->post('maintenancekm');
        $data['nextMaintenanceONKM'] = $this->input->post('nextmaintenance');
        $data['maintenanceDateFrom'] = $format_mainternacedatefrom;
        $data['maintenanceDateTo'] = $format_mainternacedateto;
        $data['nextMaintenanceDate'] = $format_mainternacedate;
        $data['documentDate'] = $format_documentdate;
        $data['companyID'] = $companyID;

        $data['comment'] = $this->input->post('commentvehicalmainte');

        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];


        if (!empty($this->input->post('maintenancekm')))
        {
            $dataup['previous_meter_reading'] = $milage['maximumpreviousreading'];
            $dataup['reading_amount'] =   ($currentmeaterreading -  $milage['maximumpreviousreading']);
        }
        else
        {
            $dataup['previous_meter_reading'] = $milage['maximumpreviousreading'];
            $dataup['reading_amount'] =   ($currentmeaterreading - $milage['maximumpreviousreading']);
        }
        $dataup['vehicleMasterID'] =   $vehiclemasterid;
        $dataup['current_meter_reading'] =   $currentmeaterreading;
        $dataup['createdUserGroup'] = $this->common_data['user_group'];
        $dataup['companyID'] = $companyID;
        $dataup['createdpc'] = $this->common_data['current_pc'];
        $dataup['createDateTime'] = $this->common_data['current_date'];
        $dataup['createuser'] = $this->common_data['current_user'];
        $this->db->insert('fleet_meter_reading', $dataup);

        if ($vehiclemaintenaceid)
        {
            $data['modifiedpc'] = $this->common_data['current_pc'];
            $data['modifieduser'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('maintenanceMasterID', $vehiclemaintenaceid);
            $this->db->update('fleet_maintenancemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Error in vehicle maintenace Update ' . $this->db->_error_message());
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Vehicle Maintenace updated successfully.', $vehiclemaintenaceid);
            }
        }
        else
        {
            $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM fleet_maintenancemaster WHERE companyID={$companyID}")->row_array();
            $data['serialNo'] = $serial['serialNo'];
            $data['maintenanceCode'] = ($company_code . '/' . 'MNT' . str_pad(
                $data['serialNo'],
                6,
                '0',
                STR_PAD_LEFT
            ));
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdpc'] = $this->common_data['current_pc'];
            $data['createDateTime'] = $this->common_data['current_date'];
            $data['createuser'] = $this->common_data['current_user'];
            $this->db->insert('fleet_maintenancemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Error Occured' . $this->db->_error_message());
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Vehicle Maintenace created successfully.', $last_id);
            }
        }
    }

    function save_maintenance_crew_det()
    {
        $this->db->trans_start();
        $crewname = $this->input->post('crewname');
        $selectedemployee = $this->input->post('selectedemployee');
        $type = $this->input->post('crewtype');
        $maintenacemasterid = $this->input->post('maintananceMasterIDcrew');

        foreach ($type as $key => $val)
        {
            $empdet = explode('|', $crewname[$key]);
            $data[$key]['maintenanceMasterID'] = $maintenacemasterid;


            if ($selectedemployee[$key])
            {
                $data[$key]['name'] = ((array_key_exists(1, $empdet))) ? $empdet[1] : $empdet[0];
                $data[$key]['empID'] = $selectedemployee[$key];
            }
            else
            {
                $data[$key]['name'] = $crewname[$key];
                $data[$key]['empID'] = null;
            }
            $data[$key]['typeID'] = $val;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];
        }
        $this->db->insert_batch('fleet_maintenancecrewdetails', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', 'Maintenance Crew Detail :  Save Failed ' . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Maintenance Crew Detail :  Saved Successfully.');
        }
    }
    function fetch_vehicalemaintenace_header_details()
    {
        $companyid = current_companyID();
        $maintenanceMasterID = $this->input->post('maintenanceMasterID');
        $convertFormat = convert_date_format_sql();
        $data = $this->db->query("SELECT 
mastertbl.*,mastertbl.currentMeterReading as currentMeterReadingmastertbl,segment.segmentCode,DATE_FORMAT(maintenanceDateFrom,' $convertFormat ') AS maintenanceDateFromcon,DATE_FORMAT(maintenanceDateTo,' $convertFormat ') AS maintenanceDateTocon,DATE_FORMAT(nextMaintenanceDate,' $convertFormat ') AS nextMaintenanceDatecon,DATE_FORMAT(documentDate,' $convertFormat ') AS documentDatecon,DATE_FORMAT(lastMaintenanceDate,' $convertFormat ') AS lastMaintenanceDatecon
FROM
fleet_maintenancemaster mastertbl
LEFT JOIN srp_erp_segment segment on segment.segmentID = mastertbl.segmentID
where 
mastertbl.companyID = $companyid
AND maintenanceMasterID = $maintenanceMasterID")->row_array();
        return $data;
    }


    function fetch_maintenace_crew_details()
    {
        $companyid = current_companyID();
        $maintenanceMasterID = $this->input->post('vehicalemaintenaceid');
        $data['detail'] = $this->db->query("select * from fleet_maintenancecrewdetails WHERE companyID  = $companyid  AND maintenanceMasterID  = $maintenanceMasterID")->result_array();
        return $data;
    }
    function delete_maintenace_crew()
    {

        $maintenanceCrewID = trim($this->input->post('maintenanceCrewID') ?? '');
        $companyid = current_companyID();

        $crewexist = $this->db->query("select maintenanceDetailID from fleet_maintenance_detail where  companyID = $companyid  AND crewID = $maintenanceCrewID")->row_array();
        if (!empty($crewexist))
        {
            return array('e', 'Employee assigned to a maintenance cannot delete !');
        }
        else
        {
            $this->db->delete('fleet_maintenancecrewdetails', array('maintenanceCrewID' => $maintenanceCrewID));
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Error while deleting!');
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Employee deleted successfully');
            }
        }
    }
    function save_maintenace_details()
    {
        $this->db->trans_start();
        $maintenancecriteria = $this->input->post('maintenancecriteria');
        $unitcost = $this->input->post('unitcost');
        $Qty = $this->input->post('Qty');
        $total = $this->input->post('total');
        $crewmember = $this->input->post('crewmember');
        $maintananceMasterIDdetail = $this->input->post('maintananceMasterIDdetail');
        $companyID = current_companyID();

        $this->db->select('*');
        $this->db->where('maintenanceMasterID', $maintananceMasterIDdetail);
        $master_record = $this->db->get('fleet_maintenancemaster')->row_array();



        foreach ($maintenancecriteria as $key => $val)
        {
            $data[$key]['maintenanceMasterID'] = $maintananceMasterIDdetail;
            $data[$key]['maintenanceCriteriaID'] = $val;
            $data[$key]['maintenanceQty'] = $Qty[$key];
            $data[$key]['unitCost'] = $unitcost[$key];
            $data[$key]['maintenanceAmount'] = $total[$key];
            $data[$key]['crewID'] = $crewmember[$key];

            $data[$key]['companyID'] = current_companyID();
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createuser'] = $this->common_data['current_userID'];
            $data[$key]['createdpc'] = $this->common_data['current_pc'];
            $data[$key]['createDateTime'] = $this->common_data['current_date'];


            $data[$key]['maintenanceAmountLocal'] = ($data[$key]['maintenanceAmount'] / $master_record['companyLocalExchangeRate']);
            $data[$key]['maintenanceAmountReporting'] = ($data[$key]['maintenanceAmount'] / $master_record['companyReportingExchangeRate']);
            $data[$key]['unitCostLocal'] = ($data[$key]['unitCost'] / $master_record['companyLocalExchangeRate']);
            $data[$key]['unitCostReporting'] = ($data[$key]['unitCost'] / $master_record['companyReportingExchangeRate']);
        }

        $this->db->insert_batch('fleet_maintenance_detail', $data);



        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', 'Maintenance  Detail :  Save Failed ' . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Maintenance Detail :  Saved Successfully.');
        }
    }
    function delete_maintenace_details()
    {
        $maintenancedetailID = trim($this->input->post('maintenanceDetailID') ?? '');
        $this->db->delete('fleet_maintenancespareparts', array('maintenanceDetailID' => $maintenancedetailID));
        $this->db->delete('fleet_maintenance_detail', array('maintenanceDetailID' => $maintenancedetailID));

        return true;
    }
    function fetch_maintenace_details()
    {
        $maintenanceDetailID = $this->input->post('maintenanceMasterID');
        $companyid = current_companyID();
        $data = $this->db->query("select 
mastertbl.*,
maintenacecriteria.maintenanceCriteria as maintenanceCriteriadescription,
crewdetails.name as crename 
from 
fleet_maintenance_detail mastertbl
LEft join fleet_maintenance_criteria maintenacecriteria on maintenacecriteria.maintenanceCriteriaID = mastertbl.maintenanceCriteriaID
LEFT JOIN fleet_maintenancecrewdetails crewdetails on crewdetails.maintenanceCrewID = mastertbl.crewID
where 
mastertbl.maintenanceDetailID = $maintenanceDetailID
And mastertbl.companyID = $companyid")->row_array();
        return $data;
    }
    function update_is_doneyn_status()
    {

        $this->db->trans_start();
        $maintenanceDetailID = trim($this->input->post('maintenanceDetailID') ?? '');
        $status = trim($this->input->post('status') ?? '');

        $data['doneYN'] = $status;
        $this->db->where('maintenanceDetailID', $maintenanceDetailID);
        $this->db->update('fleet_maintenance_detail', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', "Done Status Update Failed." . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Done status updated successfully!.');
        }
    }

    function update_doneyn_comment()
    {

        $this->db->trans_start();
        $maintenanceDetailID = trim($this->input->post('maintenanceDetailID') ?? '');
        $comments = trim($this->input->post('Comment') ?? '');

        $data['comment'] = $comments;
        $this->db->where('maintenanceDetailID', $maintenanceDetailID);
        $this->db->update('fleet_maintenance_detail', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', "Comment Update Failed." . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Comment updated successfully!.');
        }
    }
    function maxmaintenacedetails()
    {
        $convertFormat = convert_date_format_sql();

        $vehicleMasterID = $this->input->post('vehicalemasterid');
        $companyID = current_companyID();
        $data = $this->db->query("SELECT MAX(DATE_FORMAT(maintenanceDateTo,'{$convertFormat}')) as Maxdate
from 
fleet_maintenancemaster
where 
vehicleMasterID = $vehicleMasterID
And companyID = $companyID")->row_array();
        return $data;
    }
    function fetch_maintenace_number()
    {

        $companyid = current_companyID();
        $serialno = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM fleet_maintenancemaster WHERE companyID = $companyid")->row_array();
        $company_code = $this->common_data['company_data']['company_code'];

        $data = ($company_code . '/' . 'MNT' . str_pad($serialno['serialNumber'], 6, '0', STR_PAD_LEFT));

        return $data;
    }
    function save_maintenace_status()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $datestatus = $this->input->post('stausdate');
        $statusmaintenace = $this->input->post('statusmaintenace');
        $supplierinvoicecode  = null;
        $maintenacemasterid = $this->input->post('maintenacemasterid');
        $companyID = $this->common_data['company_data']['company_id'];
        $format_datestatus = null;
        $masterrecords = $this->db->query("select * from fleet_maintenancemaster WHERE companyID  = $companyID AND maintenanceMasterID = $maintenacemasterid")->row_array();
        if (isset($datestatus) && !empty($datestatus))
        {
            $format_datestatus = input_format_date($datestatus, $date_format_policy);
        }

        $this->db->select('maintenanceDetailID');
        $this->db->where('maintenanceMasterID', $maintenacemasterid);
        $this->db->where('companyID', $companyID);
        $this->db->from('fleet_maintenance_detail');
        $record = $this->db->get()->result_array();
        if ((empty($record)) && $statusmaintenace == 3)
        {
            return array('w', 'There are no records to close this maintenance!');
        }
        else
        {
            $data['status'] = $this->input->post('statusmaintenace');
            if ($data['status'] == 2)
            {
                $data['startedDate'] = $format_datestatus;
                $data['startedByEmpID'] = current_userID();
                $data['startingComment'] = $this->input->post('commentstauts');
            }
            else if ($data['status'] == 3)
            {
                if ($data['status'] == 3  && $masterrecords['maintenanceBy'] == 2)
                {
                    $data['closedDate'] = $format_datestatus;
                    $data['closedByEmpID'] = current_userID();
                    $data['closingComment'] = $this->input->post('commentstauts');
                    $date_format_policy = date_format_policy();
                    $finaceper = $this->db->query("SELECT *,CONCAT(dateFrom,' - ',dateTo) as companyFinanceYear FROM srp_erp_companyfinanceperiod  WHERE isActive = 1 AND companyID = $companyID AND '$format_datestatus' BETWEEN dateFrom AND dateTo ")->row_array();
                    $supplier_arr = $this->fetch_supplier_data($masterrecords['maintenanceCompanyID']);
                    $currencymaster = $this->db->query("select CurrencyCode from srp_erp_currencymaster where currencyID = '{$masterrecords['transactionCurrencyID']}'")->row_array();
                    $segment = $this->db->query("SELECT * FROM `srp_erp_segment` where segmentID = '{$masterrecords['segmentID']}' AND companyID = $companyID")->row_array();
                    $datas['documentID'] = 'BSI';
                    $datas['isSytemGenerated'] = '1';
                    $datas['supplierInvoiceNo'] = $masterrecords['supplierDocRefNo'];
                    $datas['documentOrigin'] = 'MNT';
                    $datas['documentOriginAutoID'] = $masterrecords['maintenanceMasterID'];
                    $datas['invoiceType'] = 'Standard';
                    $datas['companyFinanceYearID'] = $finaceper['companyFinanceYearID'];
                    $datas['companyFinanceYear'] = $finaceper['companyFinanceYear'];
                    $datas['FYBegin'] = $finaceper['dateFrom'];
                    $datas['FYEnd'] = $finaceper['dateTo'];
                    $datas['companyFinancePeriodID'] = $finaceper['companyFinancePeriodID'];
                    $datas['comments'] = 'Vehicle Maintenace' . '(' . $masterrecords['maintenanceCode'] . ')';
                    $datas['RefNo'] = $masterrecords['maintenanceCode'];
                    $datas['supplierID'] = $masterrecords['maintenanceCompanyID'];
                    $datas['supplierCode'] = $supplier_arr['supplierSystemCode'];
                    $datas['supplierName'] = $supplier_arr['supplierName'];
                    $datas['supplierAddress'] = $supplier_arr['supplierAddress1'];
                    $datas['supplierTelephone'] = $supplier_arr['supplierTelephone'];
                    $datas['supplierFax'] =  $supplier_arr['supplierFax'];
                    $datas['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
                    $datas['supplierliabilitySystemGLCode'] =   $supplier_arr['liabilitySystemGLCode'];
                    $datas['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
                    $datas['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
                    $datas['supplierliabilityType'] = $supplier_arr['liabilityType'];
                    $datas['bookingInvCode'] = 0;
                    $datas['bookingDate'] = $format_datestatus;
                    $datas['invoiceDate'] = $format_datestatus;
                    $datas['invoiceDueDate'] = $format_datestatus;
                    $datas['invoiceDueDate'] = $format_datestatus;

                    $datas['segmentID'] = $masterrecords['segmentID'];
                    $datas['segmentCode'] = $segment['segmentCode'];

                    $datas['transactionCurrencyID'] = $masterrecords['transactionCurrencyID'];
                    $datas['transactionCurrency'] = $currencymaster['CurrencyCode'];
                    $datas['transactionExchangeRate'] = 1;
                    $datas['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($datas['transactionCurrencyID']);
                    $datas['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $datas['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $default_currency = currency_conversionID($datas['transactionCurrencyID'], $datas['companyLocalCurrencyID']);
                    $datas['companyLocalExchangeRate'] = $default_currency['conversion'];
                    $datas['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $datas['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $datas['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $reporting_currency = currency_conversionID($datas['transactionCurrencyID'], $datas['companyReportingCurrencyID']);
                    $datas['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $datas['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                    $datas['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                    $datas['supplierCurrency'] = $supplier_arr['supplierCurrency'];
                    $supplierCurrency = currency_conversionID($datas['transactionCurrencyID'], $datas['supplierCurrencyID']);
                    $datas['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                    $datas['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                    $datas['companyID'] = $companyID;

                    $datas['companyCode'] = $this->common_data['company_data']['company_code'];
                    $datas['createdUserGroup'] = $this->common_data['user_group'];
                    $datas['createdPCID'] = $this->common_data['current_pc'];
                    $datas['createdUserID'] = $this->common_data['current_userID'];
                    $datas['createdDateTime'] = $this->common_data['current_date'];
                    $datas['createdUserName'] = $this->common_data['current_user'];
                    $datas['modifiedPCID'] = $this->common_data['current_pc'];
                    $datas['modifiedUserID'] = $this->common_data['current_userID'];
                    $datas['modifiedDateTime'] = $this->common_data['current_date'];
                    $datas['modifiedUserName'] = $this->common_data['current_user'];
                    $datas['timestamp'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_paysupplierinvoicemaster', $datas);
                    $last_id = $this->db->insert_id();


                    $detailtblmaintenace =  $this->db->query(" SELECT
	SUM(fleet_maintenance_detail.maintenanceAmount) as maintenanceAmount,
	SUM(fleet_maintenance_detail.maintenanceQty) as maintenanceQty,
	SUM(fleet_maintenance_detail.unitCost) as unitCost,
	SUM(fleet_maintenance_detail.unitCostLocal) as unitCostLocal,
	SUM(fleet_maintenance_detail.unitCostReporting) as unitCostReporting,
	SUM(fleet_maintenance_detail.maintenanceAmountLocal) as maintenanceAmountLocal,
	SUM(fleet_maintenance_detail.maintenanceAmountReporting) as maintenanceAmountReporting
FROM
	`fleet_maintenance_detail`
WHERE
	fleet_maintenance_detail.companyID = $companyID
    AND maintenanceMasterID = $maintenacemasterid
    GROUP BY
maintenanceMasterID")->row_array();

                    $gldes = $this->db->query("SELECT systemAccountCode as systemGLCode,GLSecondaryCode as GLCode,GLAutoID as GLAutoID,GLDescription as GLDescription,subCategory as GLType FROM srp_erp_chartofaccounts WHERE companyID = $companyID AND GLAutoID = '{$masterrecords['expenseGLAutoID']}' ")->row_array();

                    $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
                    $this->db->where('InvoiceAutoID', $last_id);
                    $master_suplier = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

                    $datadetail['InvoiceAutoID'] = $last_id;
                    $datadetail['grvType'] = 'Standard';
                    $datadetail['systemGLCode'] = $gldes['systemGLCode'];
                    $datadetail['GLCode'] = $gldes['GLCode'];
                    $datadetail['GLAutoID'] = $gldes['GLAutoID'];
                    $datadetail['GLDescription'] = $gldes['GLDescription'];
                    $datadetail['GLType'] = $gldes['GLType'];
                    $datadetail['description'] = $masterrecords['comment'];
                    $datadetail['transactionAmount'] = $detailtblmaintenace['maintenanceAmount'];
                    $datadetail['transactionExchangeRate'] = $masterrecords['transactionExchangeRate'];
                    $datadetail['companyLocalAmount'] = ($detailtblmaintenace['maintenanceAmount'] / $masterrecords['companyLocalExchangeRate']);
                    $datadetail['companyLocalExchangeRate'] = $masterrecords['companyLocalExchangeRate'];
                    $datadetail['companyReportingAmount'] = ($detailtblmaintenace['maintenanceAmount'] / $masterrecords['companyReportingExchangeRate']);
                    $datadetail['companyReportingExchangeRate'] = $masterrecords['companyReportingExchangeRate'];
                    $datadetail['supplierAmount'] = ($detailtblmaintenace['maintenanceAmount'] / $master_suplier['supplierCurrencyExchangeRate']);
                    $datadetail['supplierCurrencyExchangeRate'] = $master_suplier['supplierCurrencyExchangeRate'];
                    $datadetail['segmentID'] = $masterrecords['segmentID'];
                    $datadetail['segmentCode'] = $segment['segmentCode'];

                    $datadetail['companyCode'] = $this->common_data['company_data']['company_code'];
                    $datadetail['companyID'] = $this->common_data['company_data']['company_id'];
                    $datadetail['modifiedPCID'] = $this->common_data['current_pc'];
                    $datadetail['modifiedUserID'] = $this->common_data['current_userID'];
                    $datadetail['modifiedUserName'] = $this->common_data['current_user'];
                    $datadetail['modifiedDateTime'] = $this->common_data['current_date'];
                    $datadetail['createdUserGroup'] = $this->common_data['user_group'];
                    $datadetail['createdPCID'] = $this->common_data['current_pc'];
                    $datadetail['createdUserID'] = $this->common_data['current_userID'];
                    $datadetail['createdUserName'] = $this->common_data['current_user'];
                    $datadetail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_paysupplierinvoicedetail', $datadetail);
                    $this->supplier_invoice_confirmation($last_id);
                    $invoiceid = $this->db->query("SELECT bookingInvCode FROM `srp_erp_paysupplierinvoicemaster` where InvoiceAutoID = $last_id and companyID = $companyID")->row_array();
                    $supplierinvoicecode = $invoiceid['bookingInvCode'];
                }
                else
                {
                    $this->db->select('maintenanceDetailID');
                    $this->db->where('maintenanceMasterID', $maintenacemasterid);
                    $this->db->where('companyID', $companyID);
                    $this->db->from('fleet_maintenance_detail');
                    $record = $this->db->get()->result_array();
                    if ((empty($record)) && $statusmaintenace == 3)
                    {
                        return array('w', 'There are no records to close this maintenance!');
                    }
                    else
                    {
                        $data['closedDate'] = $format_datestatus;
                        $data['closedByEmpID'] = current_userID();
                        $data['closingComment'] = $this->input->post('commentstauts');

                        $finaceper = $this->db->query("SELECT
	srp_erp_companyfinanceperiod.*,
	CONCAT( dateFrom, ' - ', dateTo ) AS companyFinanceYear,
	beginingDate,
	endingDate
FROM
	srp_erp_companyfinanceperiod 
	LEFT JOIn srp_erp_companyfinanceyear on srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID
WHERE
	srp_erp_companyfinanceperiod.isActive = 1 
	AND srp_erp_companyfinanceperiod.companyID = 13
	AND '$format_datestatus' BETWEEN dateFrom 
	AND dateTo ")->row_array();

                        $details_arr = $this->db->query("SELECT
	spareparts.*,
	chart.GLSecondaryCode AS PLType,
	chart.GLDescription AS PLDescription,
	chart.GLSecondaryCode AS PLGLCode,
	chart.systemAccountCode AS PLSystemGLCode,
	COALESCE ( SUM( spareparts.qtyRequired ), 0 ) AS qtyUpdatedIssued,
	COALESCE ( SUM( spareparts.totalCost ), 0 ) AS UpdatedTotalValue,
	chartbl.systemAccountCode AS BLSystemGLCode,
	chartbl.GLSecondaryCode AS BLGLCode,
	chartbl.GLDescription AS BLDescription,
	chartbl.GLSecondaryCode AS BLType,
	mastertb.maintenanceMasterID as maintenanceMasterID,
	mastertb.maintenanceCode as documentSystemCode,
	mastertb.documentDate AS documentDatemaintenace,
	warehouse.wareHouseCode AS wareHouseCode,
	warehouse.wareHouseLocation AS wareHouseLocation,
	warehouse.wareHouseDescription AS wareHouseDescription,
	itemmaster.itemSystemCode as itemSystemCode,
	itemmaster.itemDescription as itemDescription,
	unit.UnitShortCode as UnitShortCode,
	mastertb.transactionCurrencyID as transactionCurrencyID,
	currencytransaction.CurrencyCode as transactionCurrency,
	mastertb.transactionExchangeRate as transactionExchangeRate,
	mastertb.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
	mastertb.companyLocalCurrencyID as companyLocalCurrencyID,
	currencycompany.CurrencyCode as companyLocalCurrency,
	mastertb.companyLocalExchangeRate as companyLocalExchangeRate,
	mastertb.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
	itemmaster.companyLocalWacAmount as currentlWacAmount,
	mastertb.companyReportingCurrencyID as companyReportingCurrencyID,
	currencycompanyreporting.CurrencyCode as companyReportingCurrency,
    mastertb.companyReportingExchangeRate as companyReportingExchangeRate,
    mastertb.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces,
    segment.segmentCode as segmentCode,
    itemmaster.mainCategory as itemCategory,
    mastertb.segmentID as segmentID
	
FROM
	fleet_maintenancespareparts spareparts
	LEFT JOIN fleet_maintenance_detail detailtb ON detailtb.maintenanceDetailID = spareparts.maintenanceDetailID
	LEFT JOIN fleet_maintenancemaster mastertb ON mastertb.maintenanceMasterID = detailtb.maintenanceMasterID
	LEFT JOIN srp_erp_warehousemaster warehouse ON warehouse.wareHouseAutoID = mastertb.warehouseAutoID
	LEFT JOIN srp_erp_unit_of_measure unit ON unit.UnitID = spareparts.uomID
	LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = spareparts.itemAutoID
	LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = spareparts.costGLAutoID
	LEFT JOIN srp_erp_chartofaccounts chartbl ON chartbl.GLAutoID = spareparts.assetGLAutoID
	LEFT JOIN srp_erp_currencymaster currencycompany ON currencycompany.currencyID = mastertb.companyLocalCurrencyID 
	LEFT JOIN srp_erp_currencymaster currencytransaction ON currencytransaction.currencyID = mastertb.transactionCurrencyID 
	LEFT JOIN srp_erp_currencymaster currencycompanyreporting ON currencycompanyreporting.currencyID = mastertb.companyReportingCurrencyID 
	LEFT JOIN srp_erp_segment segment on segment.segmentID = mastertb.segmentID
WHERE
	mastertb.maintenanceMasterID = $maintenacemasterid
	AND mastertb.companyID = $companyID
	AND spareparts.selectedYN = 1
GROUP BY
	`spareparts`.`itemAutoID`")->result_array();


                        $item_arr = array();
                        $itemledger_arr = array();
                        $costgl_generalledgerr = array();
                        $transaction_loc_tot = 0;
                        $company_rpt_tot = 0;
                        $supplier_cr_tot = 0;
                        $company_loc_tot = 0;
                        for ($i = 0; $i < count($details_arr); $i++)
                        {
                            if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory' or $details_arr[$i]['itemCategory'] == 'Service')
                            {
                                $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                                $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                                $item_arr[$i]['currentStock'] = ($item['currentStock'] - ($details_arr[$i]['qtyUpdatedIssued'] / 1));
                                $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                                $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                                $qty = ($details_arr[$i]['qtyUpdatedIssued'] / 1);
                                $itemSystemCode = $details_arr[$i]['itemAutoID'];
                                $location = $details_arr[$i]['wareHouseLocation'];
                                $wareHouseAutoID = $details_arr[$i]['warehouseAutoID'];
                                $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

                                $itemledger_arr[$i]['documentID'] = 'MNT';
                                $itemledger_arr[$i]['documentCode'] = 'MNT';
                                $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['maintenanceMasterID'];
                                $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['documentSystemCode'];
                                $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['documentDatemaintenace'];
                                $itemledger_arr[$i]['referenceNumber'] = '';
                                $itemledger_arr[$i]['companyFinanceYearID'] = $finaceper['companyFinanceYearID'];
                                $itemledger_arr[$i]['companyFinanceYear'] =  $finaceper['companyFinanceYearID'];
                                $itemledger_arr[$i]['FYBegin'] = $finaceper['dateFrom'];
                                $itemledger_arr[$i]['FYEnd'] = $finaceper['dateTo'];
                                $itemledger_arr[$i]['FYPeriodDateFrom'] = $finaceper['beginingDate'];
                                $itemledger_arr[$i]['FYPeriodDateTo'] = $finaceper['endingDate'];
                                $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['warehouseAutoID'];
                                $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                                $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                                $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                                /*    $itemledger_arr[$i]['projectID'] = $details_arr[$i]['projectID'];
                         $itemledger_arr[$i]['projectExchangeRate'] = $details_arr[$i]['projectExchangeRate'];*/
                                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                                $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['UnitShortCode'];
                                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['UnitShortCode'];
                                $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['qtyUpdatedIssued'] * -1);
                                $itemledger_arr[$i]['convertionRate'] = 1;
                                $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                                $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['uomID'];
                                $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['uomID'];
                                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['UnitShortCode'];
                                $itemledger_arr[$i]['convertionRate'] = 1;
                                $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['costGLAutoID'];
                                $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                                $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                                $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                                $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];

                                $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['assetGLAutoID'];
                                $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                                $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                                $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                                $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];

                                $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['transactionCurrencyID'];
                                $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['transactionCurrency'];
                                $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['transactionExchangeRate'];
                                $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['transactionCurrencyDecimalPlaces'];
                                $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['UpdatedTotalValue'], $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']) * -1);
                                $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                                $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                                $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                                $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                                $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyLocalExchangeRate']), $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']) * -1);

                                $itemledger_arr[$i]['companyLocalWacAmount'] = round($details_arr[$i]['currentlWacAmount'], $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                                $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                                $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                                $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                                $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                                $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']) * -1);
                                $itemledger_arr[$i]['companyReportingWacAmount'] = round(($itemledger_arr[$i]['companyLocalWacAmount'] / $itemledger_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);

                                $itemledger_arr[$i]['confirmedYN'] = 1;
                                $itemledger_arr[$i]['confirmedByEmpID'] = current_userID();
                                $itemledger_arr[$i]['confirmedByName'] = current_user();
                                $itemledger_arr[$i]['confirmedDate'] = $format_datestatus;
                                $itemledger_arr[$i]['approvedYN'] = 1;
                                $itemledger_arr[$i]['approvedDate'] = $format_datestatus;
                                $itemledger_arr[$i]['approvedbyEmpID'] = current_userID();
                                $itemledger_arr[$i]['approvedbyEmpName'] = current_user();
                                $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                                $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                                $itemledger_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                                $itemledger_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                                $itemledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                                $itemledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                                $itemledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                                $itemledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                                $itemledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                                $itemledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                                $itemledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                                $itemledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                                $itemledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                            }
                        }

                        if (!empty($item_arr))
                        {
                            $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                        }

                        if (!empty($itemledger_arr))
                        {
                            $itemledger_arr = array_values($itemledger_arr);
                            $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                        }


                        $costgl_generalledgerr = $this->db->query("SELECT
	'MNT' AS documentCode,
	maintenancemaster.maintenanceMasterID AS documentMasterAutoID,
	maintenancemaster.maintenancecode AS documentSystemCode,
	maintenancemaster.documentDate AS documentdate,
	YEAR ( maintenancemaster.documentdate ) AS documentYear,
	MONTH ( maintenancemaster.documentdate ) AS documentMonth,
	maintenancemaster.COMMENT AS documentNarration,
	spareparts.costGLAutoID AS GLAutoID,
	chart.systemAccountCode AS systemGLCode,
	chart.GLSecondaryCode AS GLCode,
	chart.GLDescription AS GLDescription,
	chart.GLSecondaryCode AS GLType,
	'dr' AS amount_type,
	maintenancemaster.transactionCurrencyID AS transactionCurrencyID,
	currencymaster.CurrencyCode AS transactionCurrency,
	maintenancemaster.transactionExchangeRate AS transactionExchangeRate,
	round( sum( spareparts.totalCost ), maintenancemaster.transactionCurrencyDecimalPlaces ) AS transactionAmount,
	maintenancemaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
	maintenancemaster.companyLocalCurrencyID AS companyLocalCurrencyID,
	currencymaster.CurrencyCode AS companyLocalCurrency,
	maintenancemaster.companyLocalExchangeRate AS companyLocalExchangeRate,
	round( sum( spareparts.totalCostLocal ), maintenancemaster.companyLocalCurrencyDecimalPlaces ) AS companyLocalAmount,
	maintenancemaster.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,
	maintenancemaster.companyReportingCurrencyID AS companyReportingCurrencyID,
	currencymastercompanyreporting.CurrencyCode AS companyReportingCurrency,
	maintenancemaster.companyReportingExchangeRate AS companyReportingExchangeRate,
	maintenancemaster.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces,
	round( sum( spareparts.totalCostReporting ), maintenancemaster.companyReportingCurrencyDecimalPlaces ) AS companyReportingAmount,
	maintenancemaster.closedByEmpID AS confirmedByEmpID,
	empdetail.Ename2 AS confirmedByName,
	maintenancemaster.closedDate AS confirmedDate,
	maintenancemaster.closedDate AS approvedDate,
	maintenancemaster.closedByEmpID AS approvedbyEmpID,
	empdetail.Ename2 AS approvedbyEmpName,
	maintenancemaster.segmentID AS segmentID,
	segmentmaint.segmentCode AS segmentCode,
	maintenancemaster.companyID AS companyID
FROM
	fleet_maintenancespareparts spareparts
	LEFT JOIN fleet_maintenance_detail maintenancedetails ON maintenancedetails.maintenanceDetailID = spareparts.maintenanceDetailID
	LEFT JOIN fleet_maintenancemaster maintenancemaster ON maintenancemaster.maintenanceMasterID = maintenancedetails.maintenanceMasterID
	LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = spareparts.itemAutoID
	LEFT JOIN srp_erp_segment segment ON segment.segmentID = maintenanceMaster.segmentID
	LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = spareparts.costGLAutoID
	LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = maintenancemaster.transactionCurrencyID
	LEFT JOIN srp_erp_currencymaster currencymastercompany ON currencymastercompany.currencyID = maintenancemaster.companyLocalCurrencyID
	LEFT JOIN srp_erp_currencymaster currencymastercompanyreporting ON currencymastercompanyreporting.currencyID = maintenancemaster.companyReportingCurrencyID
	LEFT JOIN srp_employeesdetails empdetail ON empdetail.EIdNo = maintenancemaster.closedByEmpID
	LEFT JOIN srp_erp_segment segmentmaint ON segmentmaint.segmentID = maintenancemaster.segmentID 
WHERE
	itemmaster.mainCategory = 'inventory' 
	AND maintenancemaster.maintenanceMasterID = $maintenacemasterid 
	AND maintenancemaster.companyID = $companyID
	AND spareparts.selectedYN = 1
GROUP BY
	spareparts.costGLAutoID")->result_array();


                        $assetgl_genralledger = $this->db->query("select 
'MNT' as documentCode,
maintenancemaster.maintenanceMasterID as documentMasterAutoID,
maintenancemaster.maintenancecode as documentSystemCode,
maintenancemaster.documentDate as documentdate,
year(maintenancemaster.documentdate) as documentYear,
month(maintenancemaster.documentdate)as documentMonth,
maintenancemaster.comment as  documentNarration,
spareparts.costGLAutoID as GLAutoID,
chart.systemAccountCode as systemGLCode,
chart.GLSecondaryCode as GLCode,
chart.GLDescription as GLDescription,
chart.GLSecondaryCode as GLType,
'cr' as amount_type,
maintenancemaster.transactionCurrencyID as transactionCurrencyID,
currencymaster.CurrencyCode as transactionCurrency,
maintenancemaster.transactionExchangeRate as transactionExchangeRate,
round(sum(spareparts.totalCost*-1),maintenancemaster.transactionCurrencyDecimalPlaces) as transactionAmount,
maintenancemaster.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
maintenancemaster.companyLocalCurrencyID as companyLocalCurrencyID,
currencymaster.CurrencyCode as companyLocalCurrency,
maintenancemaster.companyLocalExchangeRate as companyLocalExchangeRate,
round(sum(spareparts.totalCostLocal*-1),maintenancemaster.companyLocalCurrencyDecimalPlaces) as companyLocalAmount,
maintenancemaster.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
maintenancemaster.companyReportingCurrencyID as companyReportingCurrencyID,
currencymastercompanyreporting.CurrencyCode as companyReportingCurrency,
maintenancemaster.companyReportingExchangeRate as companyReportingExchangeRate,
maintenancemaster.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces,
round(sum(spareparts.totalCostReporting*-1),maintenancemaster.companyReportingCurrencyDecimalPlaces) as companyReportingAmount,
maintenancemaster.closedByEmpID as confirmedByEmpID,
empdetail.Ename2 as confirmedByName,
maintenancemaster.closedDate as confirmedDate,
maintenancemaster.closedDate as approvedDate,
maintenancemaster.closedByEmpID AS approvedbyEmpID,
empdetail.Ename2 AS approvedbyEmpName,
maintenancemaster.segmentID AS segmentID,
segmentmaint.segmentCode AS segmentCode,
maintenancemaster.companyID AS companyID
FROM
	fleet_maintenancespareparts spareparts
	left JOIN fleet_maintenance_detail maintenancedetails ON maintenancedetails.maintenanceDetailID = spareparts.maintenanceDetailID
	left JOIN fleet_maintenancemaster maintenancemaster ON maintenancemaster.maintenanceMasterID = maintenancedetails.maintenanceMasterID
	left JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = spareparts.itemAutoID
	left JOIN srp_erp_segment segment ON segment.segmentID = maintenanceMaster.segmentID
	left JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = spareparts.assetGLAutoID
	left JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = maintenancemaster.transactionCurrencyID
	left JOIN srp_erp_currencymaster currencymastercompany ON currencymastercompany.currencyID = maintenancemaster.companyLocalCurrencyID
	left JOIN srp_erp_currencymaster currencymastercompanyreporting ON currencymastercompanyreporting.currencyID = maintenancemaster.companyReportingCurrencyID
	left JOIN srp_employeesdetails empdetail ON empdetail.EIdNo = maintenancemaster.closedByEmpID
	left JOIN srp_erp_segment segmentmaint ON segmentmaint.segmentID = maintenancemaster.segmentID 
WHERE
	itemmaster.mainCategory = 'inventory' 
	AND maintenancemaster.maintenanceMasterID = $maintenacemasterid
    AND maintenancemaster.companyID = $companyID
    AND spareparts.selectedYN = 1
	group by spareparts.assetGLAutoID")->result_array();



                        for ($i = 0; $i < count($costgl_generalledgerr); $i++)
                        {
                            $generalledger_arr[$i]['documentCode'] = $costgl_generalledgerr[$i]['documentCode'];
                            $generalledger_arr[$i]['documentMasterAutoID'] = $costgl_generalledgerr[$i]['documentMasterAutoID'];
                            $generalledger_arr[$i]['documentSystemCode'] = $costgl_generalledgerr[$i]['documentSystemCode'];
                            $generalledger_arr[$i]['documentDate'] = $costgl_generalledgerr[$i]['documentdate'];
                            $generalledger_arr[$i]['documentYear'] = $costgl_generalledgerr[$i]['documentYear'];
                            $generalledger_arr[$i]['documentMonth'] = $costgl_generalledgerr[$i]['documentMonth'];
                            $generalledger_arr[$i]['documentNarration'] = $costgl_generalledgerr[$i]['documentNarration'];
                            $generalledger_arr[$i]['chequeNumber'] = '';
                            $generalledger_arr[$i]['transactionCurrencyID'] = $costgl_generalledgerr[$i]['transactionCurrencyID'];
                            $generalledger_arr[$i]['transactionCurrency'] = $costgl_generalledgerr[$i]['transactionCurrency'];
                            $generalledger_arr[$i]['transactionExchangeRate'] = $costgl_generalledgerr[$i]['transactionExchangeRate'];
                            $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $costgl_generalledgerr[$i]['transactionCurrencyDecimalPlaces'];
                            $generalledger_arr[$i]['companyLocalCurrencyID'] =  $costgl_generalledgerr[$i]['companyLocalCurrencyID'];
                            $generalledger_arr[$i]['companyLocalCurrency'] =  $costgl_generalledgerr[$i]['companyLocalCurrency'];
                            $generalledger_arr[$i]['companyLocalExchangeRate'] = $costgl_generalledgerr[$i]['companyLocalExchangeRate'];
                            $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $costgl_generalledgerr[$i]['companyLocalCurrencyDecimalPlaces'];
                            $generalledger_arr[$i]['companyReportingCurrencyID'] = $costgl_generalledgerr[$i]['companyReportingCurrencyID'];
                            $generalledger_arr[$i]['companyReportingCurrency'] = $costgl_generalledgerr[$i]['companyReportingCurrency'];
                            $generalledger_arr[$i]['companyReportingExchangeRate'] = $costgl_generalledgerr[$i]['companyReportingExchangeRate'];
                            $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $costgl_generalledgerr[$i]['companyReportingCurrencyDecimalPlaces'];
                            $generalledger_arr[$i]['confirmedByEmpID'] = $costgl_generalledgerr[$i]['confirmedByEmpID'];
                            $generalledger_arr[$i]['confirmedByName'] = $costgl_generalledgerr[$i]['confirmedByName'];
                            $generalledger_arr[$i]['confirmedDate'] = $costgl_generalledgerr[$i]['confirmedDate'];
                            $generalledger_arr[$i]['approvedDate'] = $costgl_generalledgerr[$i]['approvedDate'];
                            $generalledger_arr[$i]['approvedbyEmpID'] = $costgl_generalledgerr[$i]['approvedbyEmpID'];
                            $generalledger_arr[$i]['approvedbyEmpName'] = $costgl_generalledgerr[$i]['approvedbyEmpName'];
                            $generalledger_arr[$i]['companyID'] = $costgl_generalledgerr[$i]['companyID'];
                            $generalledger_arr[$i]['companyCode'] = '';
                            $generalledger_arr[$i]['transactionAmount'] = $costgl_generalledgerr[$i]['transactionAmount'];
                            $generalledger_arr[$i]['companyLocalAmount'] = $costgl_generalledgerr[$i]['companyLocalAmount'];
                            $generalledger_arr[$i]['companyReportingAmount'] = $costgl_generalledgerr[$i]['companyReportingAmount'];
                            $generalledger_arr[$i]['amount_type'] = $costgl_generalledgerr[$i]['amount_type'];
                            $generalledger_arr[$i]['GLAutoID'] = $costgl_generalledgerr[$i]['GLAutoID'];
                            $generalledger_arr[$i]['systemGLCode'] = $costgl_generalledgerr[$i]['systemGLCode'];
                            $generalledger_arr[$i]['GLCode'] = $costgl_generalledgerr[$i]['GLCode'];
                            $generalledger_arr[$i]['GLDescription'] = $costgl_generalledgerr[$i]['GLDescription'];
                            $generalledger_arr[$i]['GLType'] = $costgl_generalledgerr[$i]['GLType'];
                            $generalledger_arr[$i]['segmentID'] = $costgl_generalledgerr[$i]['segmentID'];
                            $generalledger_arr[$i]['segmentCode'] = $costgl_generalledgerr[$i]['segmentCode'];
                            $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                            $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                            $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                            $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                            $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                            $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                            $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                            $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                            $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                        }

                        for ($i = 0; $i < count($assetgl_genralledger); $i++)
                        {
                            $generalledger_arr_asset[$i]['documentCode'] = $assetgl_genralledger[$i]['documentCode'];
                            $generalledger_arr_asset[$i]['documentMasterAutoID'] = $assetgl_genralledger[$i]['documentMasterAutoID'];
                            $generalledger_arr_asset[$i]['documentSystemCode'] = $assetgl_genralledger[$i]['documentSystemCode'];
                            $generalledger_arr_asset[$i]['documentDate'] = $assetgl_genralledger[$i]['documentdate'];
                            $generalledger_arr_asset[$i]['documentYear'] = $assetgl_genralledger[$i]['documentYear'];
                            $generalledger_arr_asset[$i]['documentMonth'] = $assetgl_genralledger[$i]['documentMonth'];
                            $generalledger_arr_asset[$i]['documentNarration'] = $assetgl_genralledger[$i]['documentNarration'];
                            $generalledger_arr_asset[$i]['chequeNumber'] = '';
                            $generalledger_arr_asset[$i]['transactionCurrencyID'] = $assetgl_genralledger[$i]['transactionCurrencyID'];
                            $generalledger_arr_asset[$i]['transactionCurrency'] = $assetgl_genralledger[$i]['transactionCurrency'];
                            $generalledger_arr_asset[$i]['transactionExchangeRate'] = $assetgl_genralledger[$i]['transactionExchangeRate'];
                            $generalledger_arr_asset[$i]['transactionCurrencyDecimalPlaces'] = $assetgl_genralledger[$i]['transactionCurrencyDecimalPlaces'];
                            $generalledger_arr_asset[$i]['companyLocalCurrencyID'] =  $assetgl_genralledger[$i]['companyLocalCurrencyID'];
                            $generalledger_arr_asset[$i]['companyLocalCurrency'] =  $assetgl_genralledger[$i]['companyLocalCurrency'];
                            $generalledger_arr_asset[$i]['companyLocalExchangeRate'] = $assetgl_genralledger[$i]['companyLocalExchangeRate'];
                            $generalledger_arr_asset[$i]['companyLocalCurrencyDecimalPlaces'] = $assetgl_genralledger[$i]['companyLocalCurrencyDecimalPlaces'];
                            $generalledger_arr_asset[$i]['companyReportingCurrencyID'] = $assetgl_genralledger[$i]['companyReportingCurrencyID'];
                            $generalledger_arr_asset[$i]['companyReportingCurrency'] = $assetgl_genralledger[$i]['companyReportingCurrency'];
                            $generalledger_arr_asset[$i]['companyReportingExchangeRate'] = $assetgl_genralledger[$i]['companyReportingExchangeRate'];
                            $generalledger_arr_asset[$i]['companyReportingCurrencyDecimalPlaces'] = $assetgl_genralledger[$i]['companyReportingCurrencyDecimalPlaces'];
                            $generalledger_arr_asset[$i]['confirmedByEmpID'] = $assetgl_genralledger[$i]['confirmedByEmpID'];
                            $generalledger_arr_asset[$i]['confirmedByName'] = $assetgl_genralledger[$i]['confirmedByName'];
                            $generalledger_arr_asset[$i]['confirmedDate'] = $assetgl_genralledger[$i]['confirmedDate'];
                            $generalledger_arr_asset[$i]['approvedDate'] = $assetgl_genralledger[$i]['approvedDate'];
                            $generalledger_arr_asset[$i]['approvedbyEmpID'] = $assetgl_genralledger[$i]['approvedbyEmpID'];
                            $generalledger_arr_asset[$i]['approvedbyEmpName'] = $assetgl_genralledger[$i]['approvedbyEmpName'];
                            $generalledger_arr_asset[$i]['companyID'] = $assetgl_genralledger[$i]['companyID'];
                            $generalledger_arr_asset[$i]['companyCode'] = '';
                            $generalledger_arr_asset[$i]['transactionAmount'] = $assetgl_genralledger[$i]['transactionAmount'];
                            $generalledger_arr_asset[$i]['companyLocalAmount'] = $assetgl_genralledger[$i]['companyLocalAmount'];
                            $generalledger_arr_asset[$i]['companyReportingAmount'] = $assetgl_genralledger[$i]['companyReportingAmount'];
                            $generalledger_arr_asset[$i]['amount_type'] = $assetgl_genralledger[$i]['amount_type'];
                            $generalledger_arr_asset[$i]['GLAutoID'] = $assetgl_genralledger[$i]['GLAutoID'];
                            $generalledger_arr_asset[$i]['systemGLCode'] = $assetgl_genralledger[$i]['systemGLCode'];
                            $generalledger_arr_asset[$i]['GLCode'] = $assetgl_genralledger[$i]['GLCode'];
                            $generalledger_arr_asset[$i]['GLDescription'] = $assetgl_genralledger[$i]['GLDescription'];
                            $generalledger_arr_asset[$i]['GLType'] = $assetgl_genralledger[$i]['GLType'];
                            $generalledger_arr_asset[$i]['segmentID'] = $assetgl_genralledger[$i]['segmentID'];
                            $generalledger_arr_asset[$i]['segmentCode'] = $assetgl_genralledger[$i]['segmentCode'];
                            $generalledger_arr_asset[$i]['createdUserGroup'] = $this->common_data['user_group'];
                            $generalledger_arr_asset[$i]['createdPCID'] = $this->common_data['current_pc'];
                            $generalledger_arr_asset[$i]['createdUserID'] = $this->common_data['current_userID'];
                            $generalledger_arr_asset[$i]['createdDateTime'] = $this->common_data['current_date'];
                            $generalledger_arr_asset[$i]['createdUserName'] = $this->common_data['current_user'];
                            $generalledger_arr_asset[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                            $generalledger_arr_asset[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                            $generalledger_arr_asset[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                            $generalledger_arr_asset[$i]['modifiedUserName'] = $this->common_data['current_user'];
                        }

                        if (!empty($generalledger_arr))
                        {
                            $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                        }

                        if (!empty($generalledger_arr_asset))
                        {
                            $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr_asset);
                        }
                    }
                }
            }
            $this->db->where('maintenanceMasterID', $maintenacemasterid);
            $this->db->where('companyID', $companyID);
            $this->db->update('fleet_maintenancemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', "Status Update Failed." . $this->db->_error_message());
            }
            else
            {
                return array('s', 'Status updated successfully!.', $supplierinvoicecode);
            }
        }
    }

    //     function fetch_maintenace_details_status()
    //     {
    //         $companyid = current_companyID();
    //         $maintenanceMasterID = $this->input->post('maintenanceMasterID');
    //         $convertFormat = convert_date_format_sql();

    //         $data = $this->db->query("SELECT *,DATE_FORMAT(startedDate,'{$convertFormat}') AS startedDatecon,DATE_FORMAT(closedDate,'{$convertFormat}') AS closedDatecon
    // FROM
    // 	`fleet_maintenancemaster`
    // 	where 
    // 	maintenanceMasterID = $maintenanceMasterID AND companyID = $companyid")->row_array();

    //         return $data;
    //     }
    function fetch_maintenace_details_status()
    {
        $companyid = current_companyID();
        $maintenanceMasterID = $this->input->post('maintenanceMasterID');
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query("
        SELECT 
            fleet_maintenancemaster.*, 
            DATE_FORMAT(fleet_maintenancemaster.startedDate, '{$convertFormat}') AS startedDatecon, 
            DATE_FORMAT(fleet_maintenancemaster.closedDate, '{$convertFormat}') AS closedDatecon,
            assetMaster.assetStatus
        FROM fleet_maintenancemaster
        LEFT JOIN fleet_vehiclemaster AS assetMaster 
            ON fleet_maintenancemaster.vehicleMasterID = assetMaster.vehicleMasterID
        WHERE fleet_maintenancemaster.maintenanceMasterID = {$maintenanceMasterID}
          AND fleet_maintenancemaster.companyID = {$companyid}
    ")->row_array();

        return $data;
    }

    function update_qty_maintenacedetails()
    {

        $this->db->trans_start();
        $maintenanceDetailID = trim($this->input->post('maintenanceDetailID') ?? '');
        $maintenancemasterID = trim($this->input->post('maintenanceMasterID') ?? '');
        $companyid = current_companyID();

        $this->db->select('*');
        $this->db->where('maintenanceMasterID', $maintenancemasterID);
        $master_record = $this->db->get('fleet_maintenancemaster')->row_array();



        $total = $this->db->query("select unitCost from fleet_maintenance_detail where companyID = $companyid And maintenanceDetailID = $maintenanceDetailID")->row_array();

        $qty = trim($this->input->post('qty') ?? '');

        $data['maintenanceQty'] = $qty;
        $data['maintenanceAmount'] = $qty * $total['unitCost'];

        $data['maintenanceAmountLocal'] = ($data['maintenanceAmount'] / $master_record['companyLocalExchangeRate']);
        $data['maintenanceAmountReporting'] = ($data['maintenanceAmount'] / $master_record['companyReportingExchangeRate']);


        $this->db->where('maintenanceDetailID', $maintenanceDetailID);
        $this->db->where('companyID', $companyid);
        $this->db->update('fleet_maintenance_detail', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', "Qty Update Failed." . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Qty updated successfully!.');
        }
    }
    function update_unitcost_maintenacedetails()
    {

        $this->db->trans_start();
        $maintenanceDetailID = trim($this->input->post('maintenanceDetailID') ?? '');
        $maintenancemasterID = trim($this->input->post('maintenanceMasterID') ?? '');
        $unitcost = trim($this->input->post('unitcost') ?? '');
        $companyid = current_companyID();

        $this->db->select('*');
        $this->db->where('maintenanceMasterID', $maintenancemasterID);
        $master_record = $this->db->get('fleet_maintenancemaster')->row_array();

        $total = $this->db->query("select maintenanceQty from fleet_maintenance_detail where companyID = $companyid And maintenanceDetailID = $maintenanceDetailID")->row_array();
        $data['unitCost'] = $unitcost;
        $data['maintenanceAmount'] = $unitcost * $total['maintenanceQty'];


        $data['maintenanceAmountLocal'] = ($data['maintenanceAmount'] / $master_record['companyLocalExchangeRate']);
        $data['maintenanceAmountReporting'] = ($data['maintenanceAmount'] / $master_record['companyReportingExchangeRate']);
        $data['unitCostLocal'] = ($data['unitCost'] / $master_record['companyLocalExchangeRate']);
        $data['unitCostReporting'] = ($data['unitCost'] / $master_record['companyReportingExchangeRate']);
        $this->db->where('maintenanceDetailID', $maintenanceDetailID);
        $this->db->where('companyID', $companyid);
        $this->db->update('fleet_maintenance_detail', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', "Unit Cost Update Failed." . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Unit Cost updated successfully!.');
        }
    }
    function update_maintenacetypedes_maintenacedetails()
    {
        $this->db->trans_start();
        $maintenanceDetailID = trim($this->input->post('maintenanceDetailID') ?? '');
        $maintenacetype = trim($this->input->post('maintenacetype') ?? '');
        $companyid = current_companyID();

        $data['maintenanceCriteriaID'] = $maintenacetype;
        $this->db->where('maintenanceDetailID', $maintenanceDetailID);
        $this->db->where('companyID', $companyid);
        $this->db->update('fleet_maintenance_detail', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', "Maintenace Type Update Failed." . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Maintenace Type updated successfully!.');
        }
    }
    function fetch_vehicalemaintenace_meter_reading_details()
    {
        $companyid = current_companyID();
        $vehicalemasterid = $this->input->post('vehiclemasterid');
        $data = $this->db->query("SELECT meterreadingmaster.*,IFNULL( MAX( current_meter_reading ), IFNULL(vehicalemasterdetail.initialMilage,0)  ) AS maximumpreviousreading  FROM fleet_meter_reading meterreadingmaster
inner JOIN fleet_vehiclemaster vehicalemasterdetail on vehicalemasterdetail.vehicleMasterID = meterreadingmaster.vehicleMasterID WHERE meterreadingmaster.vehicleMasterID = $vehicalemasterid")->row_array();
        return $data;
    }
    function save_meter_reading()
    {
        $this->db->trans_start();
        $companyid = current_companyID();
        $data['vehicleMasterID'] = $this->input->post('vehiclemasterid');
        $data['previous_meter_reading'] = $this->input->post('previousreading');
        $data['current_meter_reading'] = $this->input->post('currentreading');
        $data['reading_amount'] = $this->input->post('amountreading');
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['companyID'] = $companyid;
        $data['createdpc'] = $this->common_data['current_pc'];
        $data['createDateTime'] = $this->common_data['current_date'];
        $data['createuser'] = $this->common_data['current_user'];
        $this->db->insert('fleet_meter_reading', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', 'Error Occured' . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Vehicle Meter Reading updated successfully.');
        }
    }
    function nextmaintenacekm()
    {
        $vehicleMasterID = $this->input->post('vehicalemasterid');

        $data = $this->db->query("SELECT MAX(currentMeterReading) as maximumcurrentreading FROM `fleet_maintenancemaster` WHERE vehicleMasterID = $vehicleMasterID")->row_array();
        return $data;
    }
    function delete_maintenace_criteria()
    {
        $this->db->delete('fleet_maintenance_criteria', array('maintenanceCriteriaID' => trim($this->input->post('userID') ?? '')));
        return array('s', 'Maintenance Criteria deleted successfully.');
    }
    function maintenace_criteria()
    {
        $this->db->trans_start();

        $description = $this->input->post('criteriadescription');
        $maitenacecriteriaid = $this->input->post('maintenacecriteriaid');
        $status = $this->input->post('active');
        $companyid = current_companyID();

        $data['maintenanceCriteria'] = $description;
        $data['status'] = $status;

        if ($maitenacecriteriaid)
        {
            $data['modifiedpc'] = $this->common_data['current_pc'];
            $data['modifieduser'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('maintenanceCriteriaID', $maitenacecriteriaid);
            $this->db->update('fleet_maintenance_criteria', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Error in Maintenance Criteria Update ' . $this->db->_error_message());
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Maintenance Criteria updated successfully.', $maitenacecriteriaid);
            }
        }
        else
        {
            $data['companyID'] = $companyid;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['companyID'] = $companyid;
            $data['createdpc'] = $this->common_data['current_pc'];
            $data['createDateTime'] = $this->common_data['current_date'];
            $data['createuser'] = $this->common_data['current_userID'];
            $this->db->insert('fleet_maintenance_criteria', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Error Occured' . $this->db->_error_message());
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Maintenance Criteria Added successfully.');
            }
        }
    }
    function fetchmaintenacecriteria()
    {
        $maintenacecriteriaid = $this->input->post('maintenanceCriteriaID');
        $companyid  = current_companyID();
        $data = $this->db->query("SELECT * FROM `fleet_maintenance_criteria` where companyID = $companyid AND maintenanceCriteriaID = $maintenacecriteriaid")->row_array();
        return $data;
    }
    function fetch_spareparts()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyid = $this->common_data['company_data']['company_id'];
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT mainCategory,mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match" , isSubitemExist FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '" OR barcode LIKE "' . $search_string . '") AND mainCategory !=\'Fixed Assets\' AND companyID = "' . $companyid . '" AND isActive="1"')->result_array();
        if (!empty($data))
        {
            foreach ($data as $val)
            {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'isSubitemExist' => $val['isSubitemExist'], 'revanueGLCode' => $val['revanueGLCode'], 'mainCategory' => $val['mainCategory']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }
    function save_spareparts()
    {
        $this->db->trans_start();
        $searches = $this->input->post('search');
        $maintenanceCriteriaID = $this->input->post('maintenanceCriteriaID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemAutoID = $this->input->post('itemAutoID');
        $quantityRequested = $this->input->post('quantityRequested');
        $description = $this->input->post('description');
        $this->db->delete('fleet_criteriaspareparts', array('maintenanceCriteriaID' => $maintenanceCriteriaID));

        foreach ($searches as $key => $val)
        {
            $data[$key]['maintenanceCriteriaID'] =  $maintenanceCriteriaID;
            $data[$key]['itemAutoID'] =  $itemAutoID[$key];
            $data[$key]['qtyRequired'] =  $quantityRequested[$key];
            $data[$key]['uomID'] =  $UnitOfMeasureID[$key];
            $data[$key]['qtyRequired'] =  $quantityRequested[$key];
            $data[$key]['comments'] =  $description[$key];


            $data[$key]['companyID'] =  current_companyID();
            $data[$key]['createuser'] = $this->common_data['current_userID'];
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdpc'] = $this->common_data['current_pc'];
            $data[$key]['createDateTime'] = $this->common_data['current_date'];
        }
        $this->db->insert_batch('fleet_criteriaspareparts', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', ' Spare Parts :  Save Failed ' . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', ' Spare Parts :  Saved Successfully.');
        }
    }
    function fetch_maintenace_criteriadet()
    {
        $companyid = current_companyID();
        $maintenacecritiria = $this->input->post('maintenacecritiria');
        $maintenacecritiriadetail = $this->input->post('maintenanceDetailID');

        if (!empty($maintenacecritiriadetail))
        {
            $data['statusaddedyn'] = $this->db->query("select sparePartsAddedYN from fleet_maintenance_detail where  companyID = $companyid  And maintenanceDetailID = $maintenacecritiriadetail")->row_array();
        }


        $data['detail'] = $this->db->query("select sparepartsmaster.*,itemmaster.mainCategory as mainCategory,sparepartsmaster.comments as commentscriteria,itemDescription,itemSystemCode,sparepartsmaster.itemAutoID as itemAutoIDspareparts,itemmaster.defaultUnitOfMeasureID as defaultUOMID from fleet_criteriaspareparts sparepartsmaster 
left join srp_erp_itemmaster itemmaster on itemmaster.itemAutoID = sparepartsmaster.itemAutoID  where  sparepartsmaster.companyID = $companyid And maintenanceCriteriaID = $maintenacecritiria")->result_array();

        if (!empty($maintenacecritiriadetail))
        {
            $data['addsparedet'] = $this->db->query("SELECT sparepartsmaster.*,sparepartsmaster.comments AS commentscriteria,itemmaster.mainCategory as mainCategory,itemDescription,itemSystemCode,sparepartsmaster.itemAutoID AS itemAutoIDspareparts,
itemmaster.defaultUnitOfMeasureID AS defaultUOMID  FROM fleet_maintenancespareparts sparepartsmaster LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = sparepartsmaster.itemAutoID WHERE
sparepartsmaster.companyID = $companyid AND sparePartsCriteriaID = $maintenacecritiria And maintenanceDetailID = $maintenacecritiriadetail")->result_array();
        }





        return $data;
    }
    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function save_spareparts_additional()
    {
        $this->db->trans_start();
        $searches = $this->input->post('search');
        $maintenanceCriteriaID = $this->input->post('maintenanceCriteriaID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $itemAutoID = $this->input->post('itemAutoID');
        $quantityRequested = $this->input->post('quantityRequested');
        $unitcost = $this->input->post('cost');
        $isrequired = $this->input->post('isRequired');
        $description = $this->input->post('description');
        $maintenacemasteridadd = $this->input->post('maintenacemasteridadd');
        $maintenacemasterdetailid = $this->input->post('maintenacemasterdetailid');
        $companyid = current_companyID();

        $this->db->select('*');
        $this->db->where('maintenanceMasterID', $maintenacemasteridadd);
        $this->db->where('companyID', $companyid);
        $master_record = $this->db->get('fleet_maintenancemaster')->row_array();

        $this->db->trans_start();

        $where = [
            'companyID' => current_companyID(),
            'sparePartsCriteriaID' => $maintenanceCriteriaID,
            'maintenanceDetailID' => $maintenacemasterdetailid
        ];

        $this->db->delete('fleet_maintenancespareparts', $where);

        foreach ($searches as $key => $val)
        {
            $itemmaster = $this->db->query("select assteGLAutoID,costGLAutoID from srp_erp_itemmaster where companyID = $companyid And itemAutoID = $itemAutoID[$key]")->row_array();
            $maintenacemasterspare = $this->db->query("select * from  fleet_maintenancemaster where  maintenanceMasterID = $maintenacemasteridadd  And companyID = $companyid")->row_array();

            $data_setup = array(
                'sparePartsCriteriaID' => $maintenanceCriteriaID,
                'itemAutoID' => $itemAutoID[$key],
                'assetGLAutoID' => $itemmaster['assteGLAutoID'],
                'costGLAutoID' => $itemmaster['costGLAutoID'],
                'unitCostLocal' => $unitcost[$key] / 1,
                'unitCostReporting' => $unitcost[$key] / $maintenacemasterspare['companyReportingExchangeRate'],
                'warehouseAutoID' => $maintenacemasterspare['warehouseAutoID'],
                'maintenanceDetailID' => $maintenacemasterdetailid,
                'totalCost' => $quantityRequested[$key] * $unitcost[$key],
                'totalCostLocal' => $quantityRequested[$key] * $unitcost[$key] / 1,
                'totalCostReporting' => $quantityRequested[$key] * $unitcost[$key] / $maintenacemasterspare['companyReportingExchangeRate'],
                'qtyRequired' => $quantityRequested[$key],
                'unitCost' => $unitcost[$key],
                'uomID' => $UnitOfMeasureID[$key],
                'comments' => $description[$key],
                'selectedYN' => $isrequired[$key],
                'companyID' => current_companyID(),
                'createuser' => $this->common_data['current_userID'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdpc' => $this->common_data['current_pc'],
                'createDateTime' =>  $this->common_data['current_date']
            );
            $this->db->insert('fleet_maintenancespareparts', $data_setup);
        }

        $maintenacesum = $this->db->query("select sum(totalCost) as unitCost from fleet_maintenancespareparts where maintenanceDetailID = $maintenacemasterdetailid AND selectedYN = 1 AND companyID = $companyid")->row_array();
        $dataupdate['sparePartsAddedYN'] = 1;
        if ($master_record['maintenanceBy'] == 1)
        {

            $dataupdate['unitCost'] = $maintenacesum['unitCost'];
            $dataupdate['unitCostLocal'] = $maintenacesum['unitCost'] / 1;
            $dataupdate['unitCostReporting'] = $maintenacesum['unitCost'] / $master_record['companyReportingExchangeRate'];
            $dataupdate['maintenanceAmount'] = $maintenacesum['unitCost'] * 1;
            $dataupdate['maintenanceAmountLocal'] = $maintenacesum['unitCost'] * 1 / 1;
            $dataupdate['maintenanceAmountReporting'] = $maintenacesum['unitCost'] * 1 / $master_record['companyReportingExchangeRate'];
        }
        $this->db->where('maintenanceDetailID', $maintenacemasterdetailid);
        $this->db->where('companyID', $companyid);
        $this->db->update('fleet_maintenance_detail', $dataupdate);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', ' Spare Parts :  Save Failed ' . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', ' Spare Parts :  Saved Successfully.');
        }
    }
    function fetch_cost_sprare_parts()
    {
        $itemAutoID = $this->input->post('itemautoid');
        $companyid = current_companyID();
        $data = $this->db->query("select companyLocalWacAmount,currentStock from srp_erp_itemmaster where itemAutoID  = $itemAutoID AND companyID = $companyid")->row_array();
        return $data;
    }
    function supplier_invoice_confirmation($InvoiceAutoID)
    {
        $this->db->select('InvoiceAutoID');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $results = $this->db->get()->row_array();
        if (empty($results))
        {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        }
        else
        {
            $this->db->select('InvoiceAutoID');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed))
            {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return array('status' => true);
            }
            else
            {
                $this->db->trans_start();
                $system_id = trim($this->input->post('InvoiceAutoID') ?? '');
                $this->db->select('bookingInvCode,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $InvoiceAutoID);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                $lenth = strlen($master_dt['bookingInvCode']);
                if ($lenth == 1)
                {
                    $invcod = array(
                        'bookingInvCode' => $this->sequence->sequence_generator_fin('BSI', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']),
                    );
                    $this->db->where('InvoiceAutoID', $InvoiceAutoID);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $invcod);
                }

                $this->load->library('Approvals');
                $this->db->select('InvoiceAutoID, bookingInvCode,transactionCurrency,transactionExchangeRate,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $InvoiceAutoID);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_data = $this->db->get()->row_array();

                $approvals_status = $this->approvals->CreateApproval('BSI', $master_data['InvoiceAutoID'], $master_data['bookingInvCode'], 'Supplier Invoice', 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID');
                if ($approvals_status == 1)
                {
                    $transa_total_amount = 0;
                    $loca_total_amount = 0;
                    $rpt_total_amount = 0;
                    $supplier_total_amount = 0;
                    $t_arr = array();
                    $tra_tax_total = 0;
                    $loca_tax_total = 0;
                    $rpt_tax_total = 0;
                    $sup_tax_total = 0;
                    $this->db->select('sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount,sum(supplierAmount) as supplierAmount');
                    $this->db->where('InvoiceAutoID', $InvoiceAutoID);
                    $data_arr = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

                    $transa_total_amount += $data_arr['transactionAmount'];
                    $loca_total_amount += $data_arr['companyLocalAmount'];
                    $rpt_total_amount += $data_arr['companyReportingAmount'];
                    $supplier_total_amount += $data_arr['supplierAmount'];

                    $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,taxPercentage');
                    $this->db->where('InvoiceAutoID', $InvoiceAutoID);
                    $tax_arr = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
                    for ($x = 0; $x < count($tax_arr); $x++)
                    {
                        $tax_total_amount = (($tax_arr[$x]['taxPercentage'] / 100) * $transa_total_amount);
                        $t_arr[$x]['taxDetailAutoID'] = $tax_arr[$x]['taxDetailAutoID'];
                        $t_arr[$x]['transactionAmount'] = $tax_total_amount;
                        $t_arr[$x]['supplierCurrencyAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['supplierCurrencyExchangeRate']);
                        $t_arr[$x]['companyLocalAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyLocalExchangeRate']);
                        $t_arr[$x]['companyReportingAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyReportingExchangeRate']);
                        $tra_tax_total = $t_arr[$x]['transactionAmount'];
                        $sup_tax_total = $t_arr[$x]['supplierCurrencyAmount'];
                        $loca_tax_total = $t_arr[$x]['companyLocalAmount'];
                        $rpt_tax_total = $t_arr[$x]['companyReportingAmount'];
                    }
                    /*updating transaction amount using the query used in the master data table */
                    $companyID = current_companyID();
                    $r1 = "SELECT
                                srp_erp_paysupplierinvoicemaster.InvoiceAutoID,
                                    `srp_erp_paysupplierinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
                                    `srp_erp_paysupplierinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
                                    `srp_erp_paysupplierinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
                                    `srp_erp_paysupplierinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
                                    `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyExchangeRate` AS `supplierCurrencyExchangeRate`,
                                    `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyDecimalPlaces` AS `supplierCurrencyDecimalPlaces`,
                                    `srp_erp_paysupplierinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
                                    (
                                        (
                                            (
                                                IFNULL(addondet.taxPercentage, 0) / 100
                                            ) * IFNULL(det.transactionAmount, 0)
                                        ) + IFNULL(det.transactionAmount, 0)
                                    ) AS total_value
                                FROM
                                    `srp_erp_paysupplierinvoicemaster`
                                LEFT JOIN (
                                    SELECT
                                        SUM(transactionAmount) AS transactionAmount,
                                        InvoiceAutoID
                                    FROM
                                        srp_erp_paysupplierinvoicedetail
                                    GROUP BY
                                        InvoiceAutoID
                                ) det ON (
                                    `det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                                )
                                LEFT JOIN (
                                    SELECT
                                        SUM(taxPercentage) AS taxPercentage,
                                        InvoiceAutoID
                                    FROM
                                        srp_erp_paysupplierinvoicetaxdetails
                                    GROUP BY
                                        InvoiceAutoID
                                ) addondet ON (
                                    `addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                                )
                                WHERE
                                    `companyID` = $companyID
                                    AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $InvoiceAutoID ";
                    $totalValue = $this->db->query($r1)->row_array();
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'], $totalValue['companyLocalCurrencyDecimalPlaces'])),
                        'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'], $totalValue['companyReportingCurrencyDecimalPlaces'])),
                        'supplierCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['supplierCurrencyExchangeRate'], $totalValue['supplierCurrencyDecimalPlaces'])),
                        'transactionAmount' => (round($totalValue['total_value'], $totalValue['transactionCurrencyDecimalPlaces'])),
                    );
                    $this->db->where('InvoiceAutoID', $InvoiceAutoID);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                    if (!empty($t_arr))
                    {
                        $this->db->update_batch('srp_erp_paysupplierinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
                    }
                }
                else if ($approvals_status == 3)
                {
                    $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
                    return array('status' => true);
                }
                else
                {
                    $this->session->set_flashdata('e', 'Confirmation failed.');
                    return array('status' => false);
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    /* $this->session->set_flashdata('e', 'Supplier Invoice Confirmed failed ' . $this->db->_error_message());*/
                    $this->db->trans_rollback();
                    return array('status' => false);
                }
                else
                {
                    /*$this->session->set_flashdata('s', 'Supplier Invoice Confirmed Successfully.');*/
                    $this->db->trans_commit();
                    return array('status' => true);
                }
            }
        }
    }

    function update_crew_up_maintenacedetails()
    {

        $crewid = $this->input->post('crewid');
        $companyid = current_companyID();
        $maintenanceDetailID = $this->input->post('maintenanceDetailID');

        $data['crewID'] = $crewid;


        $this->db->where('maintenanceDetailID', $maintenanceDetailID);
        $this->db->where('companyID', $companyid);
        $this->db->update('fleet_maintenance_detail', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', "Crew member Update Failed." . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Crew member updated successfully!.');
        }
    }
    function Save_New_Asset()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $vehicleMasterID = trim($this->input->post('vehicleMasterID') ?? '');
        $assetSerialNo = trim($this->input->post('SerialNo') ?? '');
        $faid = $this->input->post('fixedasset');
        $asset_ty = $this->input->post('asset_type');
        $location = $this->input->post('location');



        $assetstatus = $this->input->post('assetstatus');
        $this->db->trans_start();
        $data['manufacturedYear'] = ($this->input->post('yearManu'));
        $data['brand_id'] = $this->input->post('vehicalebrand');
        $data['assetSerialNo'] = ($this->input->post('SerialNo'));
        $data['isActive'] = ($this->input->post('active'));
        $data['thirdPartySupplierID'] = ($this->input->post('supplier'));
        $data['asset_type_id'] = ($this->input->post('asset_type'));
        $data['faID'] = ($this->input->post('fixedasset'));

        $data['vehicle_type'] = $this->input->post('vehicle_type');
        if ($data['vehicle_type'] == 2)
        {
            $data['thirdPartySupplierID'] = ($this->input->post('supplier'));
        }
        else
        {
            $data['thirdPartySupplierID'] = null;
        }

        $data['vehDescription'] = ($this->input->post('vehDescription'));

        $date_format_policy = date_format_policy();
        $register = input_format_date($this->input->post('registerDate'), $date_format_policy);
        $data['registerDate'] = $register;
        $date_format_policy = date_format_policy();
        $insurance = input_format_date($this->input->post('insuranceDate'), $date_format_policy);
        $data['insuranceDate'] = $insurance;
        $data['locationID'] = $location;
        $data['assetStatus'] = $assetstatus;
        $date_format_policy = date_format_policy();
        // $licence = input_format_date($this->input->post('lisenceDate'), $date_format_policy);
        // $data['licenseDate'] = $licence;


        $data['brand_description'] = $this->input->post('vehicalebranddescription');
        $data['model_id'] = $this->input->post('vehicalemodel');
        $data['model_description'] = $this->input->post('vehicalemodeldescription');


        if ($vehicleMasterID)
        {
            $data['modifiedpc'] = $this->common_data['current_pc'];
            $data['modifieduser'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('vehicleMasterID', trim($this->input->post('vehicleMasterID') ?? ''));
            $this->db->update('fleet_vehiclemaster', $data);
            if (!empty($faid))
            {
                $isassetexist = $this->db->query("select faID from  fleet_vehiclemaster where companyID = $companyID  AND faid = $faid AND vehicleMasterID != $vehicleMasterID")->row_array();
            }

            $descexist = $this->db->query("SELECT vehicleMasterID FROM fleet_vehiclemaster WHERE assetSerialNo='$assetSerialNo' AND vehicleMasterID !=$vehicleMasterID AND companyID = $companyID; ")->row_array();

            if (isset($descexist))
            {
                return array('e', 'Serial Number Already Exist');
            }
            if (isset($isassetexist))
            {
                return array('e', 'Fixed Asset already added');
            }
            else
            {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();
                    return array('e', 'Error while updating the Asset!');
                }
                else
                {
                    $this->db->trans_commit();
                    //  return array('s', 'Vehicle Updated Successfully.');
                    return array('s', 'Asset updated Successfully!', $vehicleMasterID);
                }
            }
        }
        else
        {
            if (!empty($faid))
            {
                $isassetexist = $this->db->query("select faID from  fleet_vehiclemaster where companyID = $companyID  AND faid = $faid")->row_array();
            }

            $descexist = $this->db->query("SELECT vehicleMasterID FROM fleet_vehiclemaster WHERE assetSerialNo='$assetSerialNo' AND companyID = $companyID; ")->row_array();
            if (isset($descexist))
            {
                return array('e', 'Serial Number Already Exist');
            }

            if (isset($isassetexist))
            {
                return array('e', 'Fixed Asset already added');
            }
            else
            {


                // // Determine the prefix based on the asset_type
                $prefix = ($asset_ty == 1) ? 'AST' : 'COM';



                $serial = $this->db->query("select IF ( isnull(MAX(SerialNo)), 1, (MAX(SerialNo) + 1) ) AS SerialNo FROM `fleet_vehiclemaster` WHERE companyID={$companyID}")->row_array();

                $data['SerialNo'] = $serial['SerialNo'];
                // $data['vehicleCode'] = 'AST';;
                $company_code = $this->common_data['company_data']['company_code'];

                $data['vehicleCode'] = ($company_code . '/' .  $prefix . str_pad(
                    $data['SerialNo'],
                    6,
                    '0',
                    STR_PAD_LEFT
                ));

                $data['vehicleMasterID'] = trim($this->input->post('vehicleMasterID') ?? '');

                $data['companyID'] = $companyID;
                $data['createdpc'] = $this->common_data['current_pc'];
                $data['createuser'] = $this->common_data['current_userID'];
                $data['createDateTime'] = $this->common_data['current_date'];
                $this->db->insert('fleet_vehiclemaster', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE)
                {
                    $this->db->trans_rollback();

                    return array('e', 'Error while creating the Asset!');
                }
                else
                {

                    $this->db->trans_commit();
                    return array('s', 'Asset is created Successfully!', $last_id);
                }
            }
        }
    }

    function asset_add_new_model_brand()
    {
        $brand = trim($this->input->post('brand') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $companyid = current_companyID();

        $isExist = $this->db->query("SELECT brandID FROM fleet_brand_master WHERE description='$brand' AND companyID = $companyid ")->row('brandID');

        if (isset($isExist))
        {
            return array('e', 'This Main Category Already Exists');
        }
        else
        {

            $data = array(
                'description' => $brand,
                'status' => 'A',
                'asset_type' => $type,
                'createdUserGroup' => current_user_group(),
                'createduser' => current_pc(),
                'createdpc' => current_employee(),
                'createdDateTime' => current_date(),
                'companyID' => current_companyID()
            );

            $this->db->insert('fleet_brand_master', $data);
            if ($this->db->affected_rows() > 0)
            {
                $brandid = $this->db->insert_id();
                return array('s', 'Main category is created successfully.', $brandid);
            }
            else
            {
                return array('e', 'Error in Main Category Creating');
            }
        }
    }

    function get_fleet_master()
    {



        $this->db->select('vehicleMasterID,vehicleCode, SerialNo, manufacturedYear,vehDescription,assetSerialNo');
        $this->db->from('fleet_vehiclemaster');
        $this->db->where('asset_type_id', 2);
        // Setting asset_type_id to 1
        $query = $this->db->get();
        return $query->result();
    }
    // function get_fleet_master()
    // {



    //     $this->db->select('vehicleMasterID,vehicleCode, SerialNo, manufacturedYear,vehDescription,assetSerialNo');
    //     $this->db->from('fleet_vehiclemaster');
    //     $this->db->where('asset_type_id', 2);
    //     $this->db->where('vehicleMasterID NOT IN (SELECT component_id FROM srp_erp_fa_asset_componets_inventory WHERE component_id = fleet_vehiclemaster.vehicleMasterID)');
    //     // Setting asset_type_id to 1
    //     $query = $this->db->get();
    //     return $query->result();
    // }
    function get_spare_master()
    {
        $companyid = current_companyID();

        $this->db->select('itemAutoID, itemSystemCode, itemName, itemDescription, partNo, mainCategory, mainCategoryID, companyID,masterApprovedYN');
        $this->db->from('srp_erp_itemmaster');
        // $this->db->join('fleet_vehiclemaster', 'fleet_vehiclemaster.companyID = srp_erp_itemmaster.companyID AND fleet_vehiclemaster.asset_type_id = 1', 'inner');
        // $this->db->where('mainCategory', 'inventory');
        $this->db->where('srp_erp_itemmaster.companyID', $companyid);
        $this->db->where('srp_erp_itemmaster.masterApprovedYN', 1);

        $query = $this->db->get();
        return $query->result();
    }

    function asset_image_upload()
    {
        $this->db->trans_start();

        $vehicleID =  trim($this->input->post('vehicleID') ?? '');
        $company_code =  $this->common_data['company_data']['company_code'];
        $companyid = current_companyID();
        $itemimageexist = $this->db->query("SELECT vehicleImage FROM `fleet_vehiclemaster` WHERE companyID = $companyid AND vehicleMasterID = $vehicleID ")->row_array();
        if (!empty($itemimageexist))
        {
            $this->s3->delete('uploads/Fleet/VehicleImg/' . $company_code . '/' . $itemimageexist['vehicleImage']);
        }
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = $company_code . '/VM_' . $this->common_data['company_data']['company_code'] . '_' . trim($this->input->post('vehicleID') ?? '') . '.' . $info->getExtension();
        $file = $_FILES['files'];
        if ($file['error'] == 1)
        {
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if (!in_array($ext, $allowed_types))
        {
            return array('e', "The file type you are attempting to upload is not allowed. ( .{$ext} )");
        }
        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if ($size > 5)
        {
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");
        }

        $path = "uploads/Fleet/VehicleImg/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload)
        {
            return array('e', "Error in document upload location configuration");
        }
        $currentDatetime = format_date_mysql_datetime();
        $currentdate = $currentDatetime;

        $this->db->trans_start();
        $currentDatetime = format_date_mysql_datetime();
        $data['vehicleImage'] = $fileName;
        $data['timestamp'] = $currentdate;

        $this->db->where('vehicleMasterID', trim($this->input->post('vehicleID') ?? ''));
        $this->db->update('fleet_vehiclemaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        }
        else
        {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');
        }

        /*$output_dir = "uploads/Fleet/VehicleImg/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/Fleet", 007);
            mkdir("uploads/Fleet/VehicleImg/", 007);
        }
        $currentDatetime = format_date_mysql_datetime();
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $currentdate = $currentDatetime;
        $fileName = 'Vehicle_' . trim($this->input->post('vehicleID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['vehicleImage'] = $fileName;
        $data['timestamp'] = $currentdate;

        $this->db->where('vehicleMasterID', trim($this->input->post('vehicleID') ?? ''));
        $this->db->update('fleet_vehiclemaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');


        }*/
    }
    public function get_main_categories($type_id)
    {
        $this->db->select('brandID as brand_id, description as brand_description');
        $this->db->from('fleet_brand_master');
        $this->db->where('asset_type', $type_id);
        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_veh_data()
    {
        $this->db->select('vehicleMasterID as vehicleMasterID, vehDescription as vehDescription, SerialNo as SerialNo, vehicleCode as vehicleCode,manufacturedYear as manufacturedYear ');
        $this->db->from('fleet_vehiclemaster');

        $query = $this->db->get();

        return $query->result();
    }

    public function fetch_component_data()
    {
        // Select the required fields from the database

        $this->db->select('
            srp_erp_fa_asset_componets_inventory.id as id, 
            srp_erp_fa_asset_componets_inventory.component_id as componentID, 
            srp_erp_fa_asset_componets_inventory.component_name as componentName, 
            srp_erp_fa_asset_componets_inventory.manufacturer as manufacturer, 
            fleet_vehiclemaster.vehicleMasterID as vehicleMasterID,
            fleet_vehiclemaster.vehicleCode as vehicleCode,
            fleet_vehiclemaster.vehDescription as vehDescription,
            fleet_vehiclemaster.SerialNo as SerialNo,
             fleet_vehiclemaster.assetSerialNo as assetSerialNo,
            fleet_vehiclemaster.manufacturedYear as manufacturedYear,
         
          
            ');

        // From the components inventory table
        $this->db->from('srp_erp_fa_asset_componets_inventory');

        // Join with the vehicle master table on the appropriate fields
        $this->db->join('fleet_vehiclemaster', 'srp_erp_fa_asset_componets_inventory.component_id = fleet_vehiclemaster.vehicleMasterID', 'left');

        // Add the conditions to select type 2 records and match asset_id
        $this->db->where('srp_erp_fa_asset_componets_inventory.type', 1);
        $this->db->where('srp_erp_fa_asset_componets_inventory.asset_id', $this->input->get('comasset'));


        // Execute the query
        $query = $this->db->get();

        // Return the result as an array of objects
        return $query->result();
    }
    public function fetch_spare_data()
    {
        // Select the required fields from the database

        $this->db->select('
            srp_erp_fa_asset_componets_inventory.id as id, 
            srp_erp_fa_asset_componets_inventory.inventory_id as inventory_id, 
            srp_erp_fa_asset_componets_inventory.inventory_name as inventory_name, 
            srp_erp_fa_asset_componets_inventory.part_number as part_number,
            fleet_vehiclemaster.vehicleMasterID as vehicleMasterID,
            fleet_vehiclemaster.vehicleCode as vehicleCode,
            fleet_vehiclemaster.vehDescription as vehDescription,
            fleet_vehiclemaster.SerialNo as SerialNo,
              fleet_vehiclemaster.assetSerialNo as assetSerialNo,
            fleet_vehiclemaster.manufacturedYear as manufacturedYear,
            srp_erp_itemmaster.itemSystemCode as itemCode
        ');


        // From the components inventory table
        $this->db->from('srp_erp_fa_asset_componets_inventory');

        // Join with the vehicle master table on the appropriate fields
        $this->db->join('fleet_vehiclemaster', 'srp_erp_fa_asset_componets_inventory.asset_id = fleet_vehiclemaster.vehicleMasterID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_fa_asset_componets_inventory.inventory_id = srp_erp_itemmaster.itemAutoID', 'left');

        // Add the conditions to select type 2 records and match asset_id
        $this->db->where('srp_erp_fa_asset_componets_inventory.type', 2);
        $this->db->where('srp_erp_fa_asset_componets_inventory.asset_id', $this->input->get('asset'));

        // Execute the query
        $query = $this->db->get();

        // Return the result as an array of objects
        return $query->result();
    }

    public function save_fleet_data($data)
    {


        $this->db->insert('srp_erp_fa_asset_componets_inventory', $data);
    }
    public function save_sp_data($data)
    {

        // print_r($data);
        // exit();
        $this->db->insert('srp_erp_fa_asset_componets_inventory', $data);
    }

    function delete_component()
    {
        // $companyID = $this->common_data['company_data']['company_id'];
        $component = trim($this->input->post('id') ?? ''); // Change 'componentID' to 'id' to match the AJAX data

        $this->db->where('id', $component);
        // $this->db->where('company_id', $companyID);
        $output = $this->db->delete('srp_erp_fa_asset_componets_inventory');

        if ($output)
        {
            return array('s', 'Component deleted successfully.');
        }
        else
        {
            return array('e', 'Error in deleting process.');
        }
    }
    function delete_inventory()
    {
        // $companyID = $this->common_data['company_data']['company_id'];
        $inventory = trim($this->input->post('id') ?? ''); // Change 'inventoryID' to 'id' to match the AJAX data

        $this->db->where('id', $inventory);
        // $this->db->where('company_id', $companyID);
        $output = $this->db->delete('srp_erp_fa_asset_componets_inventory');


        if ($output)
        {
            return array('s', 'Inventory deleted successfully.');
        }
        else
        {
            return array('e', 'Error in deleting process.');
        }
    }
    function save_uti_details()
    {
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $docNumber = $this->input->post('docNumber');
        $masterid = $this->input->post('masterid');
        $documentDate = input_format_date($this->input->post('documentDate'), $date_format_policy);

        $jobNumber = $this->input->post('jobNumber');
        $description = $this->input->post('description');
        $rig1 = $this->input->post('rig');
        $rigname = $this->input->post('rigname');
        $well = $this->input->post('well');
        $assets = $this->input->post('assets'); // Get asset data
        $coms = $this->input->post('coms');

        // Ensure they are arrays
        if (!is_array($assets))
        {
            $assets = [];
        }
        if (!is_array($coms))
        {
            $coms = [];
        }

        // Validate completeness of assets and components
        $hasCompleteAsset = false;
        $hasCompleteComponent = false;

        foreach ($assets as $asset)
        {
            if ($asset['asset_id'] || $asset['serial_number'] || $asset['description'] || $asset['thread_condition_id'] || $asset['physical_condition_id'] || $asset['date_time_from'] || $asset['date_time_to'] || $asset['hours'] || $asset['status_id'])
            {
                if ($asset['asset_id'] && $asset['serial_number'] && $asset['description'] && $asset['thread_condition_id'] && $asset['physical_condition_id'] && $asset['date_time_from'] && $asset['date_time_to'] && $asset['hours'] && $asset['status_id'])
                {
                    $hasCompleteAsset = true;
                }
                else
                {
                    return array('e', 'All asset fields must be completed if any field is filled.');
                }
            }
        }

        foreach ($coms as $com)
        {
            if ($com['asset_id'] || $com['serial_number'] || $com['description'] || $com['thread_condition_id'] || $com['physical_condition_id'] || $com['date_time_from'] || $com['date_time_to'] || $com['hours'] || $com['status_id'])
            {
                if ($com['asset_id'] && $com['serial_number'] && $com['description'] && $com['thread_condition_id'] && $com['physical_condition_id'] && $com['date_time_from'] && $com['date_time_to'] && $com['hours'] && $com['status_id'])
                {
                    $hasCompleteComponent = true;
                }
                else
                {
                    return array('e', 'All component fields must be completed if any field is filled.');
                }
            }
        }


        // Check if there are existing records in fleet_asset_utilization_detail
        $this->db->where('master_id', $masterid);
        $existingDetailsCount = $this->db->count_all_results('fleet_asset_utilization_detail');

        if ($existingDetailsCount === 0 && !$hasCompleteAsset && !$hasCompleteComponent)
        {
            return array('e', 'You must add at least one complete asset or component.');
        }

        $this->db->trans_start();

        if ($masterid)
        {

            $data1 = [
                'date' => $documentDate,
                'job_num' => $jobNumber,
                'description' => $description,
                'rig_id' => $rig1,
                'rig_name' => $rigname,
                'well_name' => $well,
                'company_id' => $companyID,
                'is_submitted' => 0,
                'is_saved' => 1
            ];

            $this->db->where('id', $masterid);
            $this->db->update('fleet_asset_utilization', $data1);

            if (count($assets) > 0)
            {
                foreach ($assets as $asset)
                {
                    $exists = $this->db->get_where('fleet_asset_utilization_detail', [
                        'master_id' => $masterid,
                        'asset_id' => $asset['asset_id'],
                        'serial_number' => $asset['serial_number'],
                        'description' => $asset['description']
                    ])->num_rows();

                    if ($exists > 0)
                    {
                        $this->db->trans_rollback();
                        return array('e', 'Asset with the same code, serial number, and description already exists. Please check and try again.');
                    }

                    $dateTimeFrom = input_format_date($asset['date_time_from'], $date_format_policy);
                    $dateTimeTo = input_format_date($asset['date_time_to'], $date_format_policy);
                    $assetData = [
                        'master_id' => $masterid,
                        'asset_id' => $asset['asset_id'],
                        'serial_number' => $asset['serial_number'],
                        'description' => $asset['description'],
                        'thread_condition_id' => $asset['thread_condition_id'],
                        'physical_condition_id' => $asset['physical_condition_id'],
                        'date_time_from' => $dateTimeFrom,
                        'date_time_to' => $dateTimeTo,
                        'hours' => $asset['hours'],
                        'asset_type' => 1, // Set this as needed
                        'time_stamp' => date('Y-m-d H:i:s'),
                        'status_id' => $asset['status_id']
                    ];
                    $this->db->insert('fleet_asset_utilization_detail', $assetData);
                }
            }


            if (count($coms) > 0)
            {
                foreach ($coms as $com)
                {
                    $exists = $this->db->get_where('fleet_asset_utilization_detail', [
                        'master_id' => $masterid,
                        'asset_id' => $com['asset_id'],
                        'serial_number' => $com['serial_number'],
                        'description' => $com['description']
                    ])->num_rows();

                    if ($exists > 0)
                    {
                        $this->db->trans_rollback();
                        return array('e', 'Component with the same code, serial number, and description already exists. Please check and try again.');
                    }

                    $dateTimeFrom = input_format_date($com['date_time_from'], $date_format_policy);
                    $dateTimeTo = input_format_date($com['date_time_to'], $date_format_policy);
                    $comData = [
                        'master_id' => $masterid,
                        'asset_id' => $com['asset_id'],
                        'serial_number' => $com['serial_number'],
                        'description' => $com['description'],
                        'thread_condition_id' => $com['thread_condition_id'],
                        'physical_condition_id' => $com['physical_condition_id'],
                        'date_time_from' => $dateTimeFrom,
                        'date_time_to' => $dateTimeTo,
                        'hours' => $com['hours'],
                        'asset_type' => 2, // Set this as needed
                        'time_stamp' => date('Y-m-d H:i:s'),
                        'status_id' => $com['status_id']
                    ];
                    $this->db->insert('fleet_asset_utilization_detail', $comData);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Utilization Details: Update Failed ' . $this->db->_error_message());
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Utilization Details: Updated Successfully.', $masterid);
            }
        }
        else
        {
            // Create new record
            if (!$hasCompleteAsset && !$hasCompleteComponent)
            {
                return array('e', 'You must add at least one complete asset or component.');
            }

            $data = [
                'doc_number' => $docNumber,
                'date' => $documentDate,
                'job_num' => $jobNumber,
                'description' => $description,
                'rig_id' => $rig1,
                'rig_name' => $rigname,
                'well_name' => $well,
                'company_id' => $companyID,
                'create_by_id' => $this->common_data['current_userID'],
                'create_by_name' => $this->common_data['current_user'],
                'is_submitted' => 0,
                'is_saved' => 1
            ];

            $this->db->insert('fleet_asset_utilization', $data);
            $masterId = $this->db->insert_id();

            // Add new assets
            if (!empty($assets))
            {
                foreach ($assets as $asset)
                {
                    $exists = $this->db->get_where('fleet_asset_utilization_detail', [
                        'master_id' => $masterId,
                        'asset_id' => $asset['asset_id'],
                        'serial_number' => $asset['serial_number'],
                        'description' => $asset['description']
                    ])->num_rows();

                    if ($exists > 0)
                    {
                        $this->db->trans_rollback();
                        return array('e', 'Asset with the same code, serial number, and description already exists. Please check and try again.');
                    }

                    $dateTimeFrom = input_format_date($asset['date_time_from'], $date_format_policy);
                    $dateTimeTo = input_format_date($asset['date_time_to'], $date_format_policy);
                    $assetData = [
                        'master_id' => $masterId,
                        'asset_id' => $asset['asset_id'],
                        'serial_number' => $asset['serial_number'],
                        'description' => $asset['description'],
                        'thread_condition_id' => $asset['thread_condition_id'],
                        'physical_condition_id' => $asset['physical_condition_id'],
                        'date_time_from' => $dateTimeFrom,
                        'date_time_to' => $dateTimeTo,
                        'hours' => $asset['hours'],
                        'asset_type' => 1,
                        'time_stamp' => date('Y-m-d H:i:s'),
                        'status_id' => $asset['status_id']
                    ];
                    $this->db->insert('fleet_asset_utilization_detail', $assetData);
                }
            }

            // Add new components
            if (!empty($coms))
            {
                foreach ($coms as $com)
                {
                    $exists = $this->db->get_where('fleet_asset_utilization_detail', [
                        'master_id' => $masterId, // Use $masterId here for new records
                        'asset_id' => $com['asset_id'],
                        'serial_number' => $com['serial_number'],
                        'description' => $com['description']
                    ])->num_rows();

                    if ($exists > 0)
                    {
                        $this->db->trans_rollback();
                        return array('e', 'Component with the same code, serial number, and description already exists. Please check and try again.');
                    }

                    $dateTimeFrom = input_format_date($com['date_time_from'], $date_format_policy);
                    $dateTimeTo = input_format_date($com['date_time_to'], $date_format_policy);
                    $comData = [
                        'master_id' => $masterId, // Correct masterId here
                        'asset_id' => $com['asset_id'],
                        'serial_number' => $com['serial_number'],
                        'description' => $com['description'],
                        'thread_condition_id' => $com['thread_condition_id'],
                        'physical_condition_id' => $com['physical_condition_id'],
                        'date_time_from' => $dateTimeFrom,
                        'date_time_to' => $dateTimeTo,
                        'hours' => $com['hours'],
                        'asset_type' => 2, // Set this as needed
                        'time_stamp' => date('Y-m-d H:i:s'),
                        'status_id' => $com['status_id']
                    ];
                    $this->db->insert('fleet_asset_utilization_detail', $comData);
                }
            }


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Utilization Details: Save Failed ' . $this->db->_error_message());
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Utilization Details: Saved Successfully.', $masterId);
            }
        }
    }


    public function get_latest_doc()
    {
        $this->db->select('doc_number');
        $this->db->from('fleet_asset_utilization');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $latestDoc = $this->db->get()->row_array();


        return isset($latestDoc['doc_number']) ? $latestDoc['doc_number'] : '';
    }


    public function submit_uti()
    {
        $masterId = $this->input->post('masterId');
        $companyID = $this->common_data['company_data']['company_id'];
        $companyCode = $this->common_data['company_data']['company_code'];
        $transactionCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $policy = getPolicyValues('MMNT', 'All');
        $proceedWithUnclosed = $this->input->post('proceedWithUnclosed');
        // $vehicalemaintenaceid = $this->input->post('vehicalemaintenaceid');
        $companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($transactionCurrencyID, $companyLocalCurrencyID);
        $companyLocalExchangeRate = $default_currency['conversion'];
        $companyLocalCurrencyDecimalPlaces = $default_currency['DecimalPlaces'];

        $companyReportingCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($transactionCurrencyID, $companyReportingCurrencyID);
        $companyReportingExchangeRate = $reporting_currency['conversion'];
        $companyReportingCurrencyDecimalPlaces = $reporting_currency['DecimalPlaces'];

        // Start the transaction
        $this->db->trans_start();

        // Select records from fleet_asset_utilization_detail
        $this->db->select('asset_id, asset_type, physical_condition_id, thread_condition_id, status_id, hours');
        $this->db->from('fleet_asset_utilization_detail');
        $this->db->where('master_id', $masterId);
        $assets = $this->db->get()->result_array();

        // Fetch status information
        $this->db->select('id, create_maintainance, related_to');
        $this->db->from('fleet_asset_utilization_status');
        $statusData = $this->db->get()->result_array();
        $statusMap = [];
        foreach ($statusData as $status)
        {
            $statusMap[$status['id']] = $status['create_maintainance'];
        }

        // Check conditions and create maintenance records if necessary
        foreach ($assets as $asset)
        {
            $assetId = $asset['asset_id'];
            $assetType = $asset['asset_type'];
            $hours = $asset['hours'];
            $physicalConditionId = $asset['physical_condition_id'];
            $threadConditionId = $asset['thread_condition_id'];
            $statusId = $asset['status_id'];

            // Check if maintenance needs to be created based on the conditions
            if (
                (isset($statusMap[$physicalConditionId]) && $statusMap[$physicalConditionId] == 1) ||
                (isset($statusMap[$threadConditionId]) && $statusMap[$threadConditionId] == 1) ||
                (isset($statusMap[$statusId]) && $statusMap[$statusId] == 1)
            )
            {
                $statusisexis = $this->db->query("SELECT maintenanceMasterID FROM fleet_maintenancemaster WHERE `status` != 3 AND vehicleMasterID = $assetId AND companyID = $companyID")->result_array();
                $maintenanceCodeResults = $this->db->query("SELECT maintenanceCode FROM fleet_maintenancemaster WHERE `status` != 3 AND vehicleMasterID = $assetId AND companyID = $companyID")->result_array();

                // Check for unclosed maintenance and policy
                if (!empty($statusisexis)) /* && empty($vehicalemaintenaceid) */
                {
                    if ($policy == 1 && !$proceedWithUnclosed)
                    {
                        // $maintenanceCodes = implode(', ', array_column($maintenanceCodeResults, 'maintenanceCode'));

                        echo json_encode(array('w', "There is unclosed maintenance exist for this asset. Do you want to proceed?"));                        // $this->db->trans_rollback();
                        exit;
                    }
                    elseif ($policy != 1)
                    {
                        echo json_encode(array('e', 'There is unclosed maintenance exist for this asset.'));
                        // $this->db->trans_rollback();
                        exit;
                    }
                }

                // Retrieve next serial number
                $this->db->select('IFNULL(MAX(serialNo), 0) + 1 AS serialNo');
                $this->db->from('fleet_maintenancemaster');
                $this->db->where('companyID', $companyID);
                $serialData = $this->db->get()->row_array();
                $serialNo = $serialData['serialNo'];

                // Generate maintenance code
                $maintenanceCode = $companyCode . '/MNT' . str_pad($serialNo, 6, '0', STR_PAD_LEFT);
                $initialmilage = $this->db->query("SELECT initialMilage FROM `fleet_vehiclemaster` where vehicleMasterID = $assetId And companyID = $companyID")->row_array();
                $milage = $this->db->query("SELECT
                    meterreadingmaster.*,
                IFNULL( MAX( current_meter_reading ), IFNULL( vehiclemasterdetail.initialMilage, 0 ) ) AS maximumpreviousreading
                FROM
                    fleet_meter_reading meterreadingmaster
                    INNER JOIN fleet_vehiclemaster vehiclemasterdetail ON vehiclemasterdetail.vehicleMasterID = meterreadingmaster.vehicleMasterID
                WHERE
                    meterreadingmaster.vehicleMasterID = $assetId
                    AND meterreadingmaster.companyID = $companyID")->row_array();
                $meterreading = $data = $this->db->query("SELECT IFNULL(MAX(current_meter_reading ),vehiclemaster.initialMilage ) AS maximumcurrentreading FROM fleet_meter_reading masterreading inner join fleet_vehiclemaster vehiclemaster on vehiclemaster.vehicleMasterID = masterreading.vehicleMasterID WHERE masterreading.vehicleMasterID = $assetId AND masterreading.companyID = $companyID")->row_array();

                // Prepare maintenance data
                $maintenanceData = array(
                    'companyID' => $companyID,
                    'assetType' => $assetType,
                    'vehicleMasterID' => $assetId,
                    'utilizationID' => $masterId,
                    'currentMeterReading' => $milage['maximumpreviousreading'] + $hours,
                    'maintenanceBy' => 1,
                    'companyLocalCurrencyID' => $companyLocalCurrencyID,
                    'companyLocalExchangeRate' => $companyLocalExchangeRate,
                    'companyLocalCurrencyDecimalPlaces' => $companyLocalCurrencyDecimalPlaces,
                    'companyReportingCurrencyID' => $companyReportingCurrencyID,
                    'companyReportingExchangeRate' => $companyReportingExchangeRate,
                    'companyReportingCurrencyDecimalPlaces' => $companyReportingCurrencyDecimalPlaces,
                    'documentDate' => $this->common_data['current_date'],
                    'createuser' => $this->common_data['current_userID'],
                    'serialNo' => $serialNo,
                    'maintenanceCode' => $maintenanceCode,
                    'transactionCurrencyID' => $transactionCurrencyID,
                    'status' => 1,
                    'maintenanceType' => 1
                );

                // Insert maintenance record
                if (!$this->db->insert('fleet_maintenancemaster', $maintenanceData))
                {
                    $this->db->trans_rollback();
                    return array('e', 'Utilization Details: Submit Failed');
                }
            }
        }

        // Update the fleet_asset_utilization table
        $data = array(
            'is_submitted' => 1,
            'submitted_by_id' => $this->common_data['current_userID'],
            'submitted_date' => $this->common_data['current_date']
        );
        $this->db->where('id', $masterId);
        $this->db->update('fleet_asset_utilization', $data);

        // Complete the transaction
        $this->db->trans_complete();

        // Check the transaction status and return the appropriate response
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', 'Utilization Details: Submit Failed');
        }
        else
        {
            $this->db->trans_commit();
            return array('s', 'Utilization Details: Submitted Successfully');
        }
    }


    public function submit()
    {

        $masterId = $this->input->post('masterId');


        $this->db->select('is_submitted');
        $this->db->from('fleet_asset_utilization');
        $this->db->where('id', $masterId);
        $currentState = $this->db->get()->row_array();

        if ($currentState['is_submitted'] == 1)
        {

            return array('i', 'Utilization Details: Already Submitted');
        }
        else
        {

            $this->db->trans_start();



            $data = array(
                'is_submitted' => 1,
                'submitted_by_id' => $this->common_data['current_userID'],
                'submitted_date' => $this->common_data['current_date']
            );

            $this->db->where('id', $masterId);
            $this->db->update('fleet_asset_utilization', $data);


            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {

                $this->db->trans_rollback();
                return array('e', 'Utilization Details: Submit Failed');
            }
            else
            {


                $this->db->trans_commit();
                return array('s', 'Utilization Details: Submitted Successfully');
            }
        }
    }

    function fetch_doc_header_detail()
    {
        $masterId = $this->input->post('masterId');
        $mainData = $this->db->query("SELECT * FROM fleet_asset_utilization WHERE id = {$masterId}")->row_array();
        return $mainData;
    }
    public function load_uti()
    {
        $masterId = $this->input->post('masterId');

        // Fetch main utilization data
        $mainData = $this->db->query("SELECT * FROM fleet_asset_utilization WHERE id = {$masterId}")->row_array();

        // Fetch asset details
        $assetData = $this->db->query("SELECT * FROM fleet_asset_utilization_detail WHERE master_id = {$masterId} AND asset_type = 1")->result_array();


        return $assetData;
    }
    public function load_com_uti()
    {
        $masterId = $this->input->post('masterId');

        // Fetch main utilization data
        $mainData = $this->db->query("SELECT * FROM fleet_asset_utilization WHERE id = {$masterId}")->row_array();

        $comData = $this->db->query("SELECT * FROM fleet_asset_utilization_detail WHERE master_id = {$masterId} AND asset_type = 2")->result_array();



        return $comData;
    }
    public function get_utilization_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('fleet_asset_utilization');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $utilization = $query->row_array();

        if ($utilization)
        {
            $this->db->select('*');
            $this->db->from('fleet_utilization_assets');
            $this->db->where('utilization_id', $id);
            $asset_query = $this->db->get();
            $utilization['assets'] = $asset_query->result_array();

            $this->db->select('*');
            $this->db->from('fleet_utilization_components');
            $this->db->where('utilization_id', $id);
            $component_query = $this->db->get();
            $utilization['components'] = $component_query->result_array();
        }

        return $utilization;
    }
    function delete_uti()
    {
        $ID = trim($this->input->post('id') ?? '');

        $this->db->trans_start();

        $this->db->where('id', $ID)->delete('fleet_asset_utilization');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }


    function fetch_inspection_data($inspectionID)
    {
        $convertFormat = convert_date_format_sql();

        // Fetching master data
        $this->db->select('id, doc_number, company_id, description, job_num, rig_name, well_name, DATE_FORMAT(date, \'' . $convertFormat . '\') AS date, is_submitted, is_saved, submitted_date');
        $this->db->where('id', $inspectionID);
        $this->db->from('fleet_asset_utilization');
        $data['master'] = $this->db->get()->row_array();

        // Fetching detail data with multiple joins for different statuses
        $this->db->select('
            fleet_asset_utilization_detail.master_id AS master_id,
            fleet_asset_utilization_detail.id AS id,
            fleet_asset_utilization_detail.asset_id AS asset_id,
            fleet_asset_utilization_detail.serial_number AS serial_number,
            fleet_asset_utilization_detail.description AS description,
            fleet_asset_utilization_detail.thread_condition_id AS thread_condition_id,
            fleet_asset_utilization_detail.physical_condition_id AS physical_condition_id,
            fleet_asset_utilization_detail.hours AS hours,
            fleet_asset_utilization_detail.asset_type AS asset_type,
            fleet_asset_utilization_detail.status_id AS status_id,
            tread_status.status AS tread_status_description,
            physical_status.status AS physical_status_description,
            general_status.status AS general_status_description,
            DATE_FORMAT(fleet_asset_utilization_detail.date_time_from, \'' . $convertFormat . '\') AS date_time_from, 
            DATE_FORMAT(fleet_asset_utilization_detail.date_time_to, \'' . $convertFormat . '\') AS date_time_to');

        // Joining for tread_condition_id
        $this->db->join(
            'fleet_asset_utilization_status AS tread_status',
            'tread_status.id = fleet_asset_utilization_detail.thread_condition_id',
            'left'
        );

        // Joining for physical_condition_id
        $this->db->join(
            'fleet_asset_utilization_status AS physical_status',
            'physical_status.id = fleet_asset_utilization_detail.physical_condition_id',
            'left'
        );

        // Joining for status_id
        $this->db->join(
            'fleet_asset_utilization_status AS general_status',
            'general_status.id = fleet_asset_utilization_detail.status_id',
            'left'
        );

        // Applying the where condition
        $this->db->where('master_id', $inspectionID);
        $this->db->from('fleet_asset_utilization_detail');
        $data['detail'] = $this->db->get()->result_array();

        return $data;
    }




    // function fetch_inspection_data($inspectionID)
    // {
    //     $convertFormat = convert_date_format_sql();

    //     $this->db->select('id, doc_number, company_id, description, job_num, rig_name, well_name, DATE_FORMAT(date, \'' . $convertFormat . '\') AS date, is_submitted, is_saved, submitted_date');
    //     $this->db->where('id', $inspectionID);
    //     $this->db->from('fleet_asset_utilization');
    //     $data['master'] = $this->db->get()->row_array();


    //     $this->db->select('
    //         fleet_asset_utilization_detail.master_id AS master_id,
    //         fleet_asset_utilization_detail.id AS id,
    //         fleet_asset_utilization_detail.asset_id AS asset_id,
    //         fleet_asset_utilization_detail.serial_number AS serial_number,
    //         fleet_asset_utilization_detail.description AS description,
    //         fleet_asset_utilization_detail.thread_condition_id AS thread_condition_id,
    //         fleet_asset_utilization_detail.physical_condition_id AS physical_condition_id,
    //         fleet_asset_utilization_detail.hours AS hours,
    //         fleet_asset_utilization_detail.asset_type AS asset_type,
    //         fleet_asset_utilization_detail.status_id AS status_id,
    //         tread_status.status AS tread_status_description,
    //         physical_status.status AS physical_status_description,
    //         general_status.status AS general_status_description,
    //         DATE_FORMAT(fleet_asset_utilization_detail.date_time_from, \'' . $convertFormat . '\') AS date_time_from, 
    //         DATE_FORMAT(fleet_asset_utilization_detail.date_time_to, \'' . $convertFormat . '\') AS date_time_to');


    //     $this->db->join('fleet_asset_utilization_status AS tread_status', 
    //         'tread_status.id = fleet_asset_utilization_detail.thread_condition_id', 
    //         'left');


    //     $this->db->join('fleet_asset_utilization_status AS physical_status', 
    //         'physical_status.id = fleet_asset_utilization_detail.physical_condition_id', 
    //         'left');


    //     $this->db->join('fleet_asset_utilization_status AS general_status', 
    //         'general_status.id = fleet_asset_utilization_detail.status_id', 
    //         'left');


    //     $this->db->where('master_id', $inspectionID);
    //     $this->db->from('fleet_asset_utilization_detail');
    //     $data['detail'] = $this->db->get()->result_array();

    //     return $data;
    // }




    function fetch_editasset_line_record()
    {

        $this->db->select('*');
        $this->db->where('id', $this->input->post('id'));
        return $this->db->get('fleet_asset_utilization_detail')->row_array();
    }
    function fetch_editcomponent_line_record()
    {

        $this->db->select('*');
        $this->db->where('id', $this->input->post('id'));
        return $this->db->get('fleet_asset_utilization_detail')->row_array();
    }
    function save_asset_line_item_edit()
    {
        $this->db->trans_start();

        // Retrieve posted data
        $asset_id = trim($this->input->post('asset_code_edit') ?? '');
        $serial_number = trim($this->input->post('serial_no_edit') ?? '');
        $description = trim($this->input->post('description_edit') ?? '');
        $thread_condition_id = trim($this->input->post('thread_condition_edit') ?? '');
        $physical_condition_id = trim($this->input->post('physical_condition_edit') ?? '');
        $status_id = trim($this->input->post('status_edit') ?? '');
        $date_time_from = trim($this->input->post('date_from_edit') ?? '');
        $date_time_to = trim($this->input->post('date_to_edit') ?? '');
        $hours = trim($this->input->post('hours_edit') ?? '');
        $master_id = trim($this->input->post('master_id') ?? '');
        $asset_edit_master_id = trim($this->input->post('asset_edit_master_id') ?? '');

        // Check if the asset_id exists in the fleet_vehiclemaster table
        $this->db->where('vehicleMasterID', $asset_id);
        $asset_exists = $this->db->get('fleet_vehiclemaster')->num_rows();

        if ($asset_exists === 0)
        {
            // Asset ID does not exist
            $this->db->trans_rollback();
            return array('e', 'Asset does not exist.');
        }

        // Check if an asset with the same master_id, asset_id, serial_number, and description already exists
        $this->db->where('id !=', $asset_edit_master_id);
        $exists = $this->db->get_where('fleet_asset_utilization_detail', [
            'master_id' => $master_id,
            'asset_id' => $asset_id,
            'serial_number' => $serial_number,
            'description' => $description
        ])->num_rows();

        // If record exists, return an error
        if ($exists > 0)
        {
            $this->db->trans_rollback();
            return array('e', 'Asset already exists.');
        }

        // Prepare data for update
        $data = [
            'asset_id' => $asset_id,
            'serial_number' => $serial_number,
            'description' => $description,
            'thread_condition_id' => $thread_condition_id,
            'physical_condition_id' => $physical_condition_id,
            'status_id' => $status_id,
            'date_time_from' => $date_time_from,
            'date_time_to' => $date_time_to,
            'hours' => $hours
        ];

        // Perform the update
        $this->db->where('id', $asset_edit_master_id);
        $this->db->update('fleet_asset_utilization_detail', $data);

        // Complete the transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', 'Update failed. Please try again.');
        }
        else
        {
            return array('s', 'Asset details updated successfully.');
        }
    }


    function save_com_line_item_edit()
    {
        $this->db->trans_start();

        // Retrieve posted data
        $asset_id = trim($this->input->post('asset_code_edit_com') ?? '');
        $serial_number = trim($this->input->post('serial_no_edit_com') ?? '');
        $description = trim($this->input->post('description_edit_com') ?? '');
        $thread_condition_id = trim($this->input->post('thread_condition_edit_com') ?? '');
        $physical_condition_id = trim($this->input->post('physical_condition_edit_com') ?? '');
        $status_id = trim($this->input->post('status_edit_com') ?? '');
        $date_time_from = trim($this->input->post('date_from_edit_com') ?? '');
        $date_time_to = trim($this->input->post('date_to_edit_com') ?? '');
        $hours = trim($this->input->post('hours_edit_com') ?? '');
        $master_id = trim($this->input->post('master_id') ?? '');
        $component_edit_master_id = trim($this->input->post('component_edit_master_id') ?? '');

        // Check if the asset_id exists in the fleet_vehiclemaster table
        $this->db->where('vehicleMasterID', $asset_id);
        $asset_exists = $this->db->get('fleet_vehiclemaster')->num_rows();

        if ($asset_exists === 0)
        {
            // Asset ID does not exist
            $this->db->trans_rollback();
            return array('e', 'component does not exist.');
        }

        // Check if a record with the same master_id, asset_id, serial_number, and description already exists
        $this->db->where('id !=', $component_edit_master_id);
        $exists = $this->db->get_where('fleet_asset_utilization_detail', [
            'master_id' => $master_id,
            'asset_id' => $asset_id,
            'serial_number' => $serial_number,
            'description' => $description
        ])->num_rows();

        // If record exists, return an error
        if ($exists > 0)
        {
            $this->db->trans_rollback();
            return array('e', 'Component already exists.');
        }

        // Prepare data for update
        $data = [
            'asset_id' => $asset_id,
            'serial_number' => $serial_number,
            'description' => $description,
            'thread_condition_id' => $thread_condition_id,
            'physical_condition_id' => $physical_condition_id,
            'status_id' => $status_id,
            'date_time_from' => $date_time_from,
            'date_time_to' => $date_time_to,
            'hours' => $hours
        ];

        // Perform the update
        $this->db->where('id', $component_edit_master_id);
        $this->db->update('fleet_asset_utilization_detail', $data);

        // Complete the transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return array('e', 'Update failed. Please try again.');
        }
        else
        {
            return array('s', 'Component details updated successfully.');
        }
    }


    function delete_component_line_record()
    {
        $ID = trim($this->input->post('id') ?? '');

        $this->db->trans_start();

        $this->db->where('id', $ID)->delete('fleet_asset_utilization_detail');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }
    public function delete_asset_line_record()
    {
        $ID = trim($this->input->post('id') ?? '');

        $this->db->trans_start();

        $this->db->where('id', $ID)->delete('fleet_asset_utilization_detail');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }
    function save_maintenanceheader()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $mainternacedatefrom = $this->input->post('maintenancedatefrom');
        $mainternacedateto = $this->input->post('maintenancedateto');
        $mainternacedate = $this->input->post('nextmaintenancedate');
        $vehiclemaintenaceid = $this->input->post('vehicalemaintenaceid');
        $vehiclemasterid = $this->input->post('vehicalemasterid');
        $assetType = $this->input->post('assettype');
        $documentdate = $this->input->post('documentdate');
        $lastmaintenacedate = $this->input->post('lastmaintenancedate');
        $datestatus = $this->input->post('stausdate');
        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $currentmeaterreading = $this->input->post('currentmeterreading');
        $maintenacedoneby = $this->input->post('maintenancedoneby');


        $segment = explode('|', trim($this->input->post('segment') ?? ''));

        $format_mainternacedatefrom = null;
        $format_mainternacedateto = null;
        $format_mainternacedate = null;
        $format_documentdate = null;
        $format_last_maintnenacedate = null;
        $format_datestatus = null;

        $initialmilage = $this->db->query("SELECT initialMilage FROM `fleet_vehiclemaster` where vehicleMasterID = $vehiclemasterid And companyID = $companyID")->row_array();
        $milage = $this->db->query("SELECT
	meterreadingmaster.*,
	IFNULL( MAX( current_meter_reading ), IFNULL( vehicalemasterdetail.initialMilage, 0 ) ) AS maximumpreviousreading 
FROM
	fleet_meter_reading meterreadingmaster
	INNER JOIN fleet_vehiclemaster vehicalemasterdetail ON vehicalemasterdetail.vehicleMasterID = meterreadingmaster.vehicleMasterID 
WHERE
	meterreadingmaster.vehicleMasterID = $vehiclemasterid
	AND meterreadingmaster.companyID = $companyID")->row_array();



        if (isset($datestatus) && !empty($datestatus))
        {
            $format_datestatus = input_format_date($datestatus, $date_format_policy);
        }

        if (isset($mainternacedatefrom) && !empty($mainternacedatefrom))
        {
            $format_mainternacedatefrom = input_format_date($mainternacedatefrom, $date_format_policy);
        }
        if (isset($lastmaintenacedate) && !empty($lastmaintenacedate))
        {
            $format_last_maintnenacedate = input_format_date($lastmaintenacedate, $date_format_policy);
        }
        if (isset($documentdate) && !empty($documentdate))
        {
            $format_documentdate = input_format_date($documentdate, $date_format_policy);
        }
        if (isset($mainternacedate) && !empty($mainternacedate))
        {
            $format_mainternacedate = input_format_date($mainternacedate, $date_format_policy);
        }
        if (isset($mainternacedateto) && !empty($mainternacedateto))
        {
            $format_mainternacedateto = input_format_date($mainternacedateto, $date_format_policy);
        }




        $data['status'] = 1;
        $data['maintenanceBy'] = $maintenacedoneby;
        if ($data['maintenanceBy'] == 2)
        {
            $data['maintenanceCompanyID'] = $this->input->post('maintenancecompany');
            $data['warehouseAutoID'] = null;
            $data['expenseGLAutoID'] = $this->input->post('glcode');
            $data['supplierDocRefNo'] = $this->input->post('supplierreferenceno');
        }
        else if ($data['maintenanceBy'] == 1)
        {
            $data['maintenanceCompanyID'] = null;
            $data['warehouseAutoID'] = $this->input->post('warehouse');
            $data['expenseGLAutoID'] = null;
            $data['supplierDocRefNo'] = null;
        }

        else
        {
            $data['maintenanceCompanyID'] = null;
            $data['expenseGLAutoID'] = null;
            $data['supplierDocRefNo'] = null;
        }


        $data['vehicleMasterID'] = $vehiclemasterid;
        $data['assetType'] = $assetType;
        $data['segmentID'] = $segment[0];
        $data['transactionCurrencyID'] = $this->input->post('transactioncurrencyid');
        $data['lastMaintenanceDate'] = $format_last_maintnenacedate;
        $data['currentMeterReading'] = $currentmeaterreading;
        $data['maintenanceType'] = $this->input->post('maintenancetype');
        $data['lastMaintenanceOnKM'] = $this->input->post('maintenancekm');
        $data['nextMaintenanceONKM'] = $this->input->post('nextmaintenance');
        $data['maintenanceDateFrom'] = $format_mainternacedatefrom;
        $data['maintenanceDateTo'] = $format_mainternacedateto;
        $data['nextMaintenanceDate'] = $format_mainternacedate;
        $data['documentDate'] = $format_documentdate;
        $data['companyID'] = $companyID;

        $data['comment'] = $this->input->post('commentvehicalmainte');

        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];


        if (!empty($this->input->post('maintenancekm')))
        {
            $dataup['previous_meter_reading'] = $milage['maximumpreviousreading'];
            $dataup['reading_amount'] =   ($currentmeaterreading -  $milage['maximumpreviousreading']);
        }
        else
        {
            $dataup['previous_meter_reading'] = $milage['maximumpreviousreading'];
            $dataup['reading_amount'] =   ($currentmeaterreading - $milage['maximumpreviousreading']);
        }
        $dataup['vehicleMasterID'] =   $vehiclemasterid;
        $dataup['current_meter_reading'] =   $currentmeaterreading;
        $dataup['createdUserGroup'] = $this->common_data['user_group'];
        $dataup['companyID'] = $companyID;
        $dataup['createdpc'] = $this->common_data['current_pc'];
        $dataup['createDateTime'] = $this->common_data['current_date'];
        $dataup['createuser'] = $this->common_data['current_user'];
        $this->db->insert('fleet_meter_reading', $dataup);

        if ($vehiclemaintenaceid)
        {
            $data['modifiedpc'] = $this->common_data['current_pc'];
            $data['modifieduser'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('maintenanceMasterID', $vehiclemaintenaceid);
            $this->db->update('fleet_maintenancemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Error in asset maintenance update ' . $this->db->_error_message());
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Asset maintenance updated successfully.', $vehiclemaintenaceid);
            }
        }
        else
        {
            $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM fleet_maintenancemaster WHERE companyID={$companyID}")->row_array();
            $data['serialNo'] = $serial['serialNo'];
            $data['maintenanceCode'] = ($company_code . '/' . 'MNT' . str_pad(
                $data['serialNo'],
                6,
                '0',
                STR_PAD_LEFT
            ));
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdpc'] = $this->common_data['current_pc'];
            $data['createDateTime'] = $this->common_data['current_date'];
            $data['createuser'] = $this->common_data['current_user'];
            $this->db->insert('fleet_maintenancemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', 'Error Occured' . $this->db->_error_message());
            }
            else
            {
                $this->db->trans_commit();
                return array('s', 'Asset maintenance created successfully.', $last_id);
            }
        }
    }

    function fetch_maintenance_header_details()
    {
        $companyid = current_companyID();
        $maintenanceMasterID = $this->input->post('maintenanceMasterID');
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query("
        SELECT 
            mastertbl.*,
            mastertbl.currentMeterReading as currentMeterReadingmastertbl,
            segment.segmentCode,
            DATE_FORMAT(maintenanceDateFrom, '$convertFormat') AS maintenanceDateFromcon,
            DATE_FORMAT(maintenanceDateTo, '$convertFormat') AS maintenanceDateTocon,
            DATE_FORMAT(nextMaintenanceDate, '$convertFormat') AS nextMaintenanceDatecon,
            DATE_FORMAT(documentDate, '$convertFormat') AS documentDatecon,
            DATE_FORMAT(lastMaintenanceDate, '$convertFormat') AS lastMaintenanceDatecon,
            utilizationmaster.doc_number AS utilaztionCode
        FROM
            fleet_maintenancemaster mastertbl
        LEFT JOIN 
            srp_erp_segment segment 
           ON segment.segmentID = mastertbl.segmentID    
        LEFT JOIN
            fleet_asset_utilization utilizationmaster
            ON utilizationmaster.id = mastertbl.utilizationID          
        WHERE 
            mastertbl.companyID = $companyid
            AND maintenanceMasterID = $maintenanceMasterID
    ")->row_array();

        return $data;
    }

    public function get_assets_by_type($asset_type)
    {
        $this->db->select('vehicleMasterID, vehDescription');
        $this->db->from('fleet_vehiclemaster');
        $this->db->where('asset_type_id', $asset_type);
        $query = $this->db->get();
        return $query->result_array();
    }

    function save_maintenance_status_with_assetstatus()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $datestatus = $this->input->post('stausdate');
        $assetstatus = $this->input->post('assetstatus');
        $statusmaintenace = $this->input->post('statusmaintenace');
        $supplierinvoicecode  = null;
        $maintenacemasterid = $this->input->post('maintenacemasterid');
        $companyID = $this->common_data['company_data']['company_id'];
        $format_datestatus = null;
        $masterrecords = $this->db->query("select * from fleet_maintenancemaster WHERE companyID  = $companyID AND maintenanceMasterID = $maintenacemasterid")->row_array();
        $vehicleMasterID = $masterrecords['vehicleMasterID'];
        if (isset($datestatus) && !empty($datestatus))
        {
            $format_datestatus = input_format_date($datestatus, $date_format_policy);
        }

        $this->db->select('maintenanceDetailID');
        $this->db->where('maintenanceMasterID', $maintenacemasterid);
        $this->db->where('companyID', $companyID);
        $this->db->from('fleet_maintenance_detail');
        $record = $this->db->get()->result_array();
        if ((empty($record)) && $statusmaintenace == 3)
        {
            return array('w', 'There are no records to close this maintenance!');
        }
        else
        {
            $data['status'] = $this->input->post('statusmaintenace');
            if ($data['status'] == 2)
            {
                $data['startedDate'] = $format_datestatus;
                $data['startedByEmpID'] = current_userID();
                $data['startingComment'] = $this->input->post('commentstauts');
                $assetData['assetStatus'] =  $assetstatus;
            }
            else if ($data['status'] == 3)
            {
                if ($data['status'] == 3  && $masterrecords['maintenanceBy'] == 2)
                {
                    $data['closedDate'] = $format_datestatus;
                    $data['closedByEmpID'] = current_userID();
                    $data['closingComment'] = $this->input->post('commentstauts');
                    $date_format_policy = date_format_policy();
                    $finaceper = $this->db->query("SELECT *,CONCAT(dateFrom,' - ',dateTo) as companyFinanceYear FROM srp_erp_companyfinanceperiod  WHERE isActive = 1 AND companyID = $companyID AND '$format_datestatus' BETWEEN dateFrom AND dateTo ")->row_array();
                    $supplier_arr = $this->fetch_supplier_data($masterrecords['maintenanceCompanyID']);
                    $currencymaster = $this->db->query("select CurrencyCode from srp_erp_currencymaster where currencyID = '{$masterrecords['transactionCurrencyID']}'")->row_array();
                    $segment = $this->db->query("SELECT * FROM `srp_erp_segment` where segmentID = '{$masterrecords['segmentID']}' AND companyID = $companyID")->row_array();
                    $datas['documentID'] = 'BSI';
                    $datas['isSytemGenerated'] = '1';
                    $datas['supplierInvoiceNo'] = $masterrecords['supplierDocRefNo'];
                    $datas['documentOrigin'] = 'MNT';
                    $datas['documentOriginAutoID'] = $masterrecords['maintenanceMasterID'];
                    $datas['invoiceType'] = 'Standard';
                    $datas['companyFinanceYearID'] = $finaceper['companyFinanceYearID'];
                    $datas['companyFinanceYear'] = $finaceper['companyFinanceYear'];
                    $datas['FYBegin'] = $finaceper['dateFrom'];
                    $datas['FYEnd'] = $finaceper['dateTo'];
                    $datas['companyFinancePeriodID'] = $finaceper['companyFinancePeriodID'];
                    $datas['comments'] = 'Vehicle Maintenace' . '(' . $masterrecords['maintenanceCode'] . ')';
                    $datas['RefNo'] = $masterrecords['maintenanceCode'];
                    $datas['supplierID'] = $masterrecords['maintenanceCompanyID'];
                    $datas['supplierCode'] = $supplier_arr['supplierSystemCode'];
                    $datas['supplierName'] = $supplier_arr['supplierName'];
                    $datas['supplierAddress'] = $supplier_arr['supplierAddress1'];
                    $datas['supplierTelephone'] = $supplier_arr['supplierTelephone'];
                    $datas['supplierFax'] =  $supplier_arr['supplierFax'];
                    $datas['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
                    $datas['supplierliabilitySystemGLCode'] =   $supplier_arr['liabilitySystemGLCode'];
                    $datas['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
                    $datas['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
                    $datas['supplierliabilityType'] = $supplier_arr['liabilityType'];
                    $datas['bookingInvCode'] = 0;
                    $datas['bookingDate'] = $format_datestatus;
                    $datas['invoiceDate'] = $format_datestatus;
                    $datas['invoiceDueDate'] = $format_datestatus;
                    $datas['invoiceDueDate'] = $format_datestatus;

                    $datas['segmentID'] = $masterrecords['segmentID'];
                    $datas['segmentCode'] = $segment['segmentCode'];

                    $datas['transactionCurrencyID'] = $masterrecords['transactionCurrencyID'];
                    $datas['transactionCurrency'] = $currencymaster['CurrencyCode'];
                    $datas['transactionExchangeRate'] = 1;
                    $datas['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($datas['transactionCurrencyID']);
                    $datas['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $datas['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                    $default_currency = currency_conversionID($datas['transactionCurrencyID'], $datas['companyLocalCurrencyID']);
                    $datas['companyLocalExchangeRate'] = $default_currency['conversion'];
                    $datas['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $datas['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                    $datas['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $reporting_currency = currency_conversionID($datas['transactionCurrencyID'], $datas['companyReportingCurrencyID']);
                    $datas['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $datas['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                    $datas['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
                    $datas['supplierCurrency'] = $supplier_arr['supplierCurrency'];
                    $supplierCurrency = currency_conversionID($datas['transactionCurrencyID'], $datas['supplierCurrencyID']);
                    $datas['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                    $datas['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                    $datas['companyID'] = $companyID;

                    $datas['companyCode'] = $this->common_data['company_data']['company_code'];
                    $datas['createdUserGroup'] = $this->common_data['user_group'];
                    $datas['createdPCID'] = $this->common_data['current_pc'];
                    $datas['createdUserID'] = $this->common_data['current_userID'];
                    $datas['createdDateTime'] = $this->common_data['current_date'];
                    $datas['createdUserName'] = $this->common_data['current_user'];
                    $datas['modifiedPCID'] = $this->common_data['current_pc'];
                    $datas['modifiedUserID'] = $this->common_data['current_userID'];
                    $datas['modifiedDateTime'] = $this->common_data['current_date'];
                    $datas['modifiedUserName'] = $this->common_data['current_user'];
                    $datas['timestamp'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_paysupplierinvoicemaster', $datas);
                    $last_id = $this->db->insert_id();


                    $detailtblmaintenace =  $this->db->query(" SELECT
                        SUM(fleet_maintenance_detail.maintenanceAmount) as maintenanceAmount,
                        SUM(fleet_maintenance_detail.maintenanceQty) as maintenanceQty,
                        SUM(fleet_maintenance_detail.unitCost) as unitCost,
                        SUM(fleet_maintenance_detail.unitCostLocal) as unitCostLocal,
                        SUM(fleet_maintenance_detail.unitCostReporting) as unitCostReporting,
                        SUM(fleet_maintenance_detail.maintenanceAmountLocal) as maintenanceAmountLocal,
                        SUM(fleet_maintenance_detail.maintenanceAmountReporting) as maintenanceAmountReporting
                    FROM
                        `fleet_maintenance_detail`
                    WHERE
                        fleet_maintenance_detail.companyID = $companyID
                        AND maintenanceMasterID = $maintenacemasterid
                        GROUP BY
                    maintenanceMasterID")->row_array();

                    $gldes = $this->db->query("SELECT systemAccountCode as systemGLCode,GLSecondaryCode as GLCode,GLAutoID as GLAutoID,GLDescription as GLDescription,subCategory as GLType FROM srp_erp_chartofaccounts WHERE companyID = $companyID AND GLAutoID = '{$masterrecords['expenseGLAutoID']}' ")->row_array();

                    $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
                    $this->db->where('InvoiceAutoID', $last_id);
                    $master_suplier = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

                    $datadetail['InvoiceAutoID'] = $last_id;
                    $datadetail['grvType'] = 'Standard';
                    $datadetail['systemGLCode'] = $gldes['systemGLCode'];
                    $datadetail['GLCode'] = $gldes['GLCode'];
                    $datadetail['GLAutoID'] = $gldes['GLAutoID'];
                    $datadetail['GLDescription'] = $gldes['GLDescription'];
                    $datadetail['GLType'] = $gldes['GLType'];
                    $datadetail['description'] = $masterrecords['comment'];
                    $datadetail['transactionAmount'] = $detailtblmaintenace['maintenanceAmount'];
                    $datadetail['transactionExchangeRate'] = $masterrecords['transactionExchangeRate'];
                    $datadetail['companyLocalAmount'] = ($detailtblmaintenace['maintenanceAmount'] / $masterrecords['companyLocalExchangeRate']);
                    $datadetail['companyLocalExchangeRate'] = $masterrecords['companyLocalExchangeRate'];
                    $datadetail['companyReportingAmount'] = ($detailtblmaintenace['maintenanceAmount'] / $masterrecords['companyReportingExchangeRate']);
                    $datadetail['companyReportingExchangeRate'] = $masterrecords['companyReportingExchangeRate'];
                    $datadetail['supplierAmount'] = ($detailtblmaintenace['maintenanceAmount'] / $master_suplier['supplierCurrencyExchangeRate']);
                    $datadetail['supplierCurrencyExchangeRate'] = $master_suplier['supplierCurrencyExchangeRate'];
                    $datadetail['segmentID'] = $masterrecords['segmentID'];
                    $datadetail['segmentCode'] = $segment['segmentCode'];

                    $datadetail['companyCode'] = $this->common_data['company_data']['company_code'];
                    $datadetail['companyID'] = $this->common_data['company_data']['company_id'];
                    $datadetail['modifiedPCID'] = $this->common_data['current_pc'];
                    $datadetail['modifiedUserID'] = $this->common_data['current_userID'];
                    $datadetail['modifiedUserName'] = $this->common_data['current_user'];
                    $datadetail['modifiedDateTime'] = $this->common_data['current_date'];
                    $datadetail['createdUserGroup'] = $this->common_data['user_group'];
                    $datadetail['createdPCID'] = $this->common_data['current_pc'];
                    $datadetail['createdUserID'] = $this->common_data['current_userID'];
                    $datadetail['createdUserName'] = $this->common_data['current_user'];
                    $datadetail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_paysupplierinvoicedetail', $datadetail);
                    $this->supplier_invoice_confirmation($last_id);
                    $invoiceid = $this->db->query("SELECT bookingInvCode FROM `srp_erp_paysupplierinvoicemaster` where InvoiceAutoID = $last_id and companyID = $companyID")->row_array();
                    $supplierinvoicecode = $invoiceid['bookingInvCode'];
                }
                else
                {
                    $this->db->select('maintenanceDetailID');
                    $this->db->where('maintenanceMasterID', $maintenacemasterid);
                    $this->db->where('companyID', $companyID);
                    $this->db->from('fleet_maintenance_detail');
                    $record = $this->db->get()->result_array();
                    if ((empty($record)) && $statusmaintenace == 3)
                    {
                        return array('w', 'There are no records to close this maintenance!');
                    }
                    else
                    {
                        $data['closedDate'] = $format_datestatus;
                        $data['closedByEmpID'] = current_userID();
                        $data['closingComment'] = $this->input->post('commentstauts');

                        $finaceper = $this->db->query("SELECT
                            srp_erp_companyfinanceperiod.*,
                            CONCAT( dateFrom, ' - ', dateTo ) AS companyFinanceYear,
                            beginingDate,
                            endingDate
                        FROM
                            srp_erp_companyfinanceperiod 
                            LEFT JOIn srp_erp_companyfinanceyear on srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID
                        WHERE
                            srp_erp_companyfinanceperiod.isActive = 1 
                            AND srp_erp_companyfinanceperiod.companyID = 13
                            AND '$format_datestatus' BETWEEN dateFrom 
                            AND dateTo ")->row_array();

                        $details_arr = $this->db->query("SELECT
                            spareparts.*,
                            chart.GLSecondaryCode AS PLType,
                            chart.GLDescription AS PLDescription,
                            chart.GLSecondaryCode AS PLGLCode,
                            chart.systemAccountCode AS PLSystemGLCode,
                            COALESCE ( SUM( spareparts.qtyRequired ), 0 ) AS qtyUpdatedIssued,
                            COALESCE ( SUM( spareparts.totalCost ), 0 ) AS UpdatedTotalValue,
                            chartbl.systemAccountCode AS BLSystemGLCode,
                            chartbl.GLSecondaryCode AS BLGLCode,
                            chartbl.GLDescription AS BLDescription,
                            chartbl.GLSecondaryCode AS BLType,
                            mastertb.maintenanceMasterID as maintenanceMasterID,
                            mastertb.maintenanceCode as documentSystemCode,
                            mastertb.documentDate AS documentDatemaintenace,
                            warehouse.wareHouseCode AS wareHouseCode,
                            warehouse.wareHouseLocation AS wareHouseLocation,
                            warehouse.wareHouseDescription AS wareHouseDescription,
                            itemmaster.itemSystemCode as itemSystemCode,
                            itemmaster.itemDescription as itemDescription,
                            unit.UnitShortCode as UnitShortCode,
                            mastertb.transactionCurrencyID as transactionCurrencyID,
                            currencytransaction.CurrencyCode as transactionCurrency,
                            mastertb.transactionExchangeRate as transactionExchangeRate,
                            mastertb.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
                            mastertb.companyLocalCurrencyID as companyLocalCurrencyID,
                            currencycompany.CurrencyCode as companyLocalCurrency,
                            mastertb.companyLocalExchangeRate as companyLocalExchangeRate,
                            mastertb.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
                            itemmaster.companyLocalWacAmount as currentlWacAmount,
                            mastertb.companyReportingCurrencyID as companyReportingCurrencyID,
                            currencycompanyreporting.CurrencyCode as companyReportingCurrency,
                            mastertb.companyReportingExchangeRate as companyReportingExchangeRate,
                            mastertb.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces,
                            segment.segmentCode as segmentCode,
                            itemmaster.mainCategory as itemCategory,
                            mastertb.segmentID as segmentID
                            
                        FROM
                            fleet_maintenancespareparts spareparts
                            LEFT JOIN fleet_maintenance_detail detailtb ON detailtb.maintenanceDetailID = spareparts.maintenanceDetailID
                            LEFT JOIN fleet_maintenancemaster mastertb ON mastertb.maintenanceMasterID = detailtb.maintenanceMasterID
                            LEFT JOIN srp_erp_warehousemaster warehouse ON warehouse.wareHouseAutoID = mastertb.warehouseAutoID
                            LEFT JOIN srp_erp_unit_of_measure unit ON unit.UnitID = spareparts.uomID
                            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = spareparts.itemAutoID
                            LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = spareparts.costGLAutoID
                            LEFT JOIN srp_erp_chartofaccounts chartbl ON chartbl.GLAutoID = spareparts.assetGLAutoID
                            LEFT JOIN srp_erp_currencymaster currencycompany ON currencycompany.currencyID = mastertb.companyLocalCurrencyID 
                            LEFT JOIN srp_erp_currencymaster currencytransaction ON currencytransaction.currencyID = mastertb.transactionCurrencyID 
                            LEFT JOIN srp_erp_currencymaster currencycompanyreporting ON currencycompanyreporting.currencyID = mastertb.companyReportingCurrencyID 
                            LEFT JOIN srp_erp_segment segment on segment.segmentID = mastertb.segmentID
                        WHERE
                            mastertb.maintenanceMasterID = $maintenacemasterid
                            AND mastertb.companyID = $companyID
                            AND spareparts.selectedYN = 1
                        GROUP BY
                            `spareparts`.`itemAutoID`")->result_array();


                        $item_arr = array();
                        $itemledger_arr = array();
                        $costgl_generalledgerr = array();
                        $transaction_loc_tot = 0;
                        $company_rpt_tot = 0;
                        $supplier_cr_tot = 0;
                        $company_loc_tot = 0;
                        for ($i = 0; $i < count($details_arr); $i++)
                        {
                            if ($details_arr[$i]['itemCategory'] == 'Inventory' or $details_arr[$i]['itemCategory'] == 'Non Inventory' or $details_arr[$i]['itemCategory'] == 'Service')
                            {
                                $item = fetch_item_data($details_arr[$i]['itemAutoID']);
                                $item_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                                $item_arr[$i]['currentStock'] = ($item['currentStock'] - ($details_arr[$i]['qtyUpdatedIssued'] / 1));
                                $item_arr[$i]['companyLocalWacAmount'] = $item['companyLocalWacAmount'];
                                $item_arr[$i]['companyReportingWacAmount'] = $item['companyReportingWacAmount'];
                                $qty = ($details_arr[$i]['qtyUpdatedIssued'] / 1);
                                $itemSystemCode = $details_arr[$i]['itemAutoID'];
                                $location = $details_arr[$i]['wareHouseLocation'];
                                $wareHouseAutoID = $details_arr[$i]['warehouseAutoID'];
                                $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock -{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemSystemCode}'");

                                $itemledger_arr[$i]['documentID'] = 'MNT';
                                $itemledger_arr[$i]['documentCode'] = 'MNT';
                                $itemledger_arr[$i]['documentAutoID'] = $details_arr[$i]['maintenanceMasterID'];
                                $itemledger_arr[$i]['documentSystemCode'] = $details_arr[$i]['documentSystemCode'];
                                $itemledger_arr[$i]['documentDate'] = $details_arr[$i]['documentDatemaintenace'];
                                $itemledger_arr[$i]['referenceNumber'] = '';
                                $itemledger_arr[$i]['companyFinanceYearID'] = $finaceper['companyFinanceYearID'];
                                $itemledger_arr[$i]['companyFinanceYear'] =  $finaceper['companyFinanceYearID'];
                                $itemledger_arr[$i]['FYBegin'] = $finaceper['dateFrom'];
                                $itemledger_arr[$i]['FYEnd'] = $finaceper['dateTo'];
                                $itemledger_arr[$i]['FYPeriodDateFrom'] = $finaceper['beginingDate'];
                                $itemledger_arr[$i]['FYPeriodDateTo'] = $finaceper['endingDate'];
                                $itemledger_arr[$i]['wareHouseAutoID'] = $details_arr[$i]['warehouseAutoID'];
                                $itemledger_arr[$i]['wareHouseCode'] = $details_arr[$i]['wareHouseCode'];
                                $itemledger_arr[$i]['wareHouseLocation'] = $details_arr[$i]['wareHouseLocation'];
                                $itemledger_arr[$i]['wareHouseDescription'] = $details_arr[$i]['wareHouseDescription'];
                                /*    $itemledger_arr[$i]['projectID'] = $details_arr[$i]['projectID'];
                         $itemledger_arr[$i]['projectExchangeRate'] = $details_arr[$i]['projectExchangeRate'];*/
                                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                                $itemledger_arr[$i]['defaultUOM'] = $details_arr[$i]['UnitShortCode'];
                                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['UnitShortCode'];
                                $itemledger_arr[$i]['transactionQTY'] = ($details_arr[$i]['qtyUpdatedIssued'] * -1);
                                $itemledger_arr[$i]['convertionRate'] = 1;
                                $itemledger_arr[$i]['currentStock'] = $item_arr[$i]['currentStock'];
                                $itemledger_arr[$i]['itemAutoID'] = $details_arr[$i]['itemAutoID'];
                                $itemledger_arr[$i]['itemSystemCode'] = $details_arr[$i]['itemSystemCode'];
                                $itemledger_arr[$i]['itemDescription'] = $details_arr[$i]['itemDescription'];
                                $itemledger_arr[$i]['defaultUOMID'] = $details_arr[$i]['uomID'];
                                $itemledger_arr[$i]['transactionUOMID'] = $details_arr[$i]['uomID'];
                                $itemledger_arr[$i]['transactionUOM'] = $details_arr[$i]['UnitShortCode'];
                                $itemledger_arr[$i]['convertionRate'] = 1;
                                $itemledger_arr[$i]['PLGLAutoID'] = $details_arr[$i]['costGLAutoID'];
                                $itemledger_arr[$i]['PLSystemGLCode'] = $details_arr[$i]['PLSystemGLCode'];
                                $itemledger_arr[$i]['PLGLCode'] = $details_arr[$i]['PLGLCode'];
                                $itemledger_arr[$i]['PLDescription'] = $details_arr[$i]['PLDescription'];
                                $itemledger_arr[$i]['PLType'] = $details_arr[$i]['PLType'];

                                $itemledger_arr[$i]['BLGLAutoID'] = $details_arr[$i]['assetGLAutoID'];
                                $itemledger_arr[$i]['BLSystemGLCode'] = $details_arr[$i]['BLSystemGLCode'];
                                $itemledger_arr[$i]['BLGLCode'] = $details_arr[$i]['BLGLCode'];
                                $itemledger_arr[$i]['BLDescription'] = $details_arr[$i]['BLDescription'];
                                $itemledger_arr[$i]['BLType'] = $details_arr[$i]['BLType'];

                                $itemledger_arr[$i]['transactionCurrencyID'] = $details_arr[$i]['transactionCurrencyID'];
                                $itemledger_arr[$i]['transactionCurrency'] = $details_arr[$i]['transactionCurrency'];
                                $itemledger_arr[$i]['transactionExchangeRate'] = $details_arr[$i]['transactionExchangeRate'];
                                $itemledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $details_arr[$i]['transactionCurrencyDecimalPlaces'];
                                $itemledger_arr[$i]['transactionAmount'] = (round($details_arr[$i]['UpdatedTotalValue'], $itemledger_arr[$i]['transactionCurrencyDecimalPlaces']) * -1);
                                $itemledger_arr[$i]['companyLocalCurrencyID'] = $details_arr[$i]['companyLocalCurrencyID'];
                                $itemledger_arr[$i]['companyLocalCurrency'] = $details_arr[$i]['companyLocalCurrency'];
                                $itemledger_arr[$i]['companyLocalExchangeRate'] = $details_arr[$i]['companyLocalExchangeRate'];
                                $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $details_arr[$i]['companyLocalCurrencyDecimalPlaces'];
                                $itemledger_arr[$i]['companyLocalAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyLocalExchangeRate']), $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']) * -1);

                                $itemledger_arr[$i]['companyLocalWacAmount'] = round($details_arr[$i]['currentlWacAmount'], $itemledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                                $itemledger_arr[$i]['companyReportingCurrencyID'] = $details_arr[$i]['companyReportingCurrencyID'];
                                $itemledger_arr[$i]['companyReportingCurrency'] = $details_arr[$i]['companyReportingCurrency'];
                                $itemledger_arr[$i]['companyReportingExchangeRate'] = $details_arr[$i]['companyReportingExchangeRate'];
                                $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $details_arr[$i]['companyReportingCurrencyDecimalPlaces'];
                                $itemledger_arr[$i]['companyReportingAmount'] = (round(($details_arr[$i]['UpdatedTotalValue'] / $details_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']) * -1);
                                $itemledger_arr[$i]['companyReportingWacAmount'] = round(($itemledger_arr[$i]['companyLocalWacAmount'] / $itemledger_arr[$i]['companyReportingExchangeRate']), $itemledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);

                                $itemledger_arr[$i]['confirmedYN'] = 1;
                                $itemledger_arr[$i]['confirmedByEmpID'] = current_userID();
                                $itemledger_arr[$i]['confirmedByName'] = current_user();
                                $itemledger_arr[$i]['confirmedDate'] = $format_datestatus;
                                $itemledger_arr[$i]['approvedYN'] = 1;
                                $itemledger_arr[$i]['approvedDate'] = $format_datestatus;
                                $itemledger_arr[$i]['approvedbyEmpID'] = current_userID();
                                $itemledger_arr[$i]['approvedbyEmpName'] = current_user();
                                $itemledger_arr[$i]['segmentID'] = $details_arr[$i]['segmentID'];
                                $itemledger_arr[$i]['segmentCode'] = $details_arr[$i]['segmentCode'];
                                $itemledger_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                                $itemledger_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                                $itemledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                                $itemledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                                $itemledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                                $itemledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                                $itemledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                                $itemledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                                $itemledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                                $itemledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                                $itemledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                            }
                        }

                        if (!empty($item_arr))
                        {
                            $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                        }

                        if (!empty($itemledger_arr))
                        {
                            $itemledger_arr = array_values($itemledger_arr);
                            $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                        }


                        $costgl_generalledgerr = $this->db->query("SELECT
                            'MNT' AS documentCode,
                            maintenancemaster.maintenanceMasterID AS documentMasterAutoID,
                            maintenancemaster.maintenancecode AS documentSystemCode,
                            maintenancemaster.documentDate AS documentdate,
                            YEAR ( maintenancemaster.documentdate ) AS documentYear,
                            MONTH ( maintenancemaster.documentdate ) AS documentMonth,
                            maintenancemaster.COMMENT AS documentNarration,
                            spareparts.costGLAutoID AS GLAutoID,
                            chart.systemAccountCode AS systemGLCode,
                            chart.GLSecondaryCode AS GLCode,
                            chart.GLDescription AS GLDescription,
                            chart.GLSecondaryCode AS GLType,
                            'dr' AS amount_type,
                            maintenancemaster.transactionCurrencyID AS transactionCurrencyID,
                            currencymaster.CurrencyCode AS transactionCurrency,
                            maintenancemaster.transactionExchangeRate AS transactionExchangeRate,
                            round( sum( spareparts.totalCost ), maintenancemaster.transactionCurrencyDecimalPlaces ) AS transactionAmount,
                            maintenancemaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                            maintenancemaster.companyLocalCurrencyID AS companyLocalCurrencyID,
                            currencymaster.CurrencyCode AS companyLocalCurrency,
                            maintenancemaster.companyLocalExchangeRate AS companyLocalExchangeRate,
                            round( sum( spareparts.totalCostLocal ), maintenancemaster.companyLocalCurrencyDecimalPlaces ) AS companyLocalAmount,
                            maintenancemaster.companyLocalCurrencyDecimalPlaces AS companyLocalCurrencyDecimalPlaces,
                            maintenancemaster.companyReportingCurrencyID AS companyReportingCurrencyID,
                            currencymastercompanyreporting.CurrencyCode AS companyReportingCurrency,
                            maintenancemaster.companyReportingExchangeRate AS companyReportingExchangeRate,
                            maintenancemaster.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces,
                            round( sum( spareparts.totalCostReporting ), maintenancemaster.companyReportingCurrencyDecimalPlaces ) AS companyReportingAmount,
                            maintenancemaster.closedByEmpID AS confirmedByEmpID,
                            empdetail.Ename2 AS confirmedByName,
                            maintenancemaster.closedDate AS confirmedDate,
                            maintenancemaster.closedDate AS approvedDate,
                            maintenancemaster.closedByEmpID AS approvedbyEmpID,
                            empdetail.Ename2 AS approvedbyEmpName,
                            maintenancemaster.segmentID AS segmentID,
                            segmentmaint.segmentCode AS segmentCode,
                            maintenancemaster.companyID AS companyID
                        FROM
                            fleet_maintenancespareparts spareparts
                            LEFT JOIN fleet_maintenance_detail maintenancedetails ON maintenancedetails.maintenanceDetailID = spareparts.maintenanceDetailID
                            LEFT JOIN fleet_maintenancemaster maintenancemaster ON maintenancemaster.maintenanceMasterID = maintenancedetails.maintenanceMasterID
                            LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = spareparts.itemAutoID
                            LEFT JOIN srp_erp_segment segment ON segment.segmentID = maintenancemaster.segmentID
                            LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = spareparts.costGLAutoID
                            LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = maintenancemaster.transactionCurrencyID
                            LEFT JOIN srp_erp_currencymaster currencymastercompany ON currencymastercompany.currencyID = maintenancemaster.companyLocalCurrencyID
                            LEFT JOIN srp_erp_currencymaster currencymastercompanyreporting ON currencymastercompanyreporting.currencyID = maintenancemaster.companyReportingCurrencyID
                            LEFT JOIN srp_employeesdetails empdetail ON empdetail.EIdNo = maintenancemaster.closedByEmpID
                            LEFT JOIN srp_erp_segment segmentmaint ON segmentmaint.segmentID = maintenancemaster.segmentID 
                        WHERE
                            itemmaster.mainCategory = 'inventory' 
                            AND maintenancemaster.maintenanceMasterID = $maintenacemasterid 
                            AND maintenancemaster.companyID = $companyID
                            AND spareparts.selectedYN = 1
                        GROUP BY
                            spareparts.costGLAutoID")->result_array();


                        $assetgl_genralledger = $this->db->query(
                            "select 'MNT' as documentCode,
                            maintenancemaster.maintenanceMasterID as documentMasterAutoID,
                            maintenancemaster.maintenancecode as documentSystemCode,
                            maintenancemaster.documentDate as documentdate,
                            year(maintenancemaster.documentdate) as documentYear,
                            month(maintenancemaster.documentdate)as documentMonth,
                            maintenancemaster.comment as  documentNarration,
                            spareparts.costGLAutoID as GLAutoID,
                            chart.systemAccountCode as systemGLCode,
                            chart.GLSecondaryCode as GLCode,
                            chart.GLDescription as GLDescription,
                            chart.GLSecondaryCode as GLType,
                            'cr' as amount_type,
                            maintenancemaster.transactionCurrencyID as transactionCurrencyID,
                            currencymaster.CurrencyCode as transactionCurrency,
                            maintenancemaster.transactionExchangeRate as transactionExchangeRate,
                            round(sum(spareparts.totalCost*-1),maintenancemaster.transactionCurrencyDecimalPlaces) as transactionAmount,
                            maintenancemaster.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,
                            maintenancemaster.companyLocalCurrencyID as companyLocalCurrencyID,
                            currencymaster.CurrencyCode as companyLocalCurrency,
                            maintenancemaster.companyLocalExchangeRate as companyLocalExchangeRate,
                            round(sum(spareparts.totalCostLocal*-1),maintenancemaster.companyLocalCurrencyDecimalPlaces) as companyLocalAmount,
                            maintenancemaster.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
                            maintenancemaster.companyReportingCurrencyID as companyReportingCurrencyID,
                            currencymastercompanyreporting.CurrencyCode as companyReportingCurrency,
                            maintenancemaster.companyReportingExchangeRate as companyReportingExchangeRate,
                            maintenancemaster.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces,
                            round(sum(spareparts.totalCostReporting*-1),maintenancemaster.companyReportingCurrencyDecimalPlaces) as companyReportingAmount,
                            maintenancemaster.closedByEmpID as confirmedByEmpID,
                            empdetail.Ename2 as confirmedByName,
                            maintenancemaster.closedDate as confirmedDate,
                            maintenancemaster.closedDate as approvedDate,
                            maintenancemaster.closedByEmpID AS approvedbyEmpID,
                            empdetail.Ename2 AS approvedbyEmpName,
                            maintenancemaster.segmentID AS segmentID,
                            segmentmaint.segmentCode AS segmentCode,
                            maintenancemaster.companyID AS companyID
                            FROM
                                fleet_maintenancespareparts spareparts
                                left JOIN fleet_maintenance_detail maintenancedetails ON maintenancedetails.maintenanceDetailID = spareparts.maintenanceDetailID
                                left JOIN fleet_maintenancemaster maintenancemaster ON maintenancemaster.maintenanceMasterID = maintenancedetails.maintenanceMasterID
                                left JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = spareparts.itemAutoID
                                left JOIN srp_erp_segment segment ON segment.segmentID = maintenancemaster.segmentID
                                left JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = spareparts.assetGLAutoID
                                left JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = maintenancemaster.transactionCurrencyID
                                left JOIN srp_erp_currencymaster currencymastercompany ON currencymastercompany.currencyID = maintenancemaster.companyLocalCurrencyID
                                left JOIN srp_erp_currencymaster currencymastercompanyreporting ON currencymastercompanyreporting.currencyID = maintenancemaster.companyReportingCurrencyID
                                left JOIN srp_employeesdetails empdetail ON empdetail.EIdNo = maintenancemaster.closedByEmpID
                                left JOIN srp_erp_segment segmentmaint ON segmentmaint.segmentID = maintenancemaster.segmentID 
                            WHERE
                                itemmaster.mainCategory = 'inventory' 
                                AND maintenancemaster.maintenanceMasterID = $maintenacemasterid
                                AND maintenancemaster.companyID = $companyID
                                AND spareparts.selectedYN = 1
                                group by spareparts.assetGLAutoID"
                        )->result_array();



                        for ($i = 0; $i < count($costgl_generalledgerr); $i++)
                        {
                            $generalledger_arr[$i]['documentCode'] = $costgl_generalledgerr[$i]['documentCode'];
                            $generalledger_arr[$i]['documentMasterAutoID'] = $costgl_generalledgerr[$i]['documentMasterAutoID'];
                            $generalledger_arr[$i]['documentSystemCode'] = $costgl_generalledgerr[$i]['documentSystemCode'];
                            $generalledger_arr[$i]['documentDate'] = $costgl_generalledgerr[$i]['documentdate'];
                            $generalledger_arr[$i]['documentYear'] = $costgl_generalledgerr[$i]['documentYear'];
                            $generalledger_arr[$i]['documentMonth'] = $costgl_generalledgerr[$i]['documentMonth'];
                            $generalledger_arr[$i]['documentNarration'] = $costgl_generalledgerr[$i]['documentNarration'];
                            $generalledger_arr[$i]['chequeNumber'] = '';
                            $generalledger_arr[$i]['transactionCurrencyID'] = $costgl_generalledgerr[$i]['transactionCurrencyID'];
                            $generalledger_arr[$i]['transactionCurrency'] = $costgl_generalledgerr[$i]['transactionCurrency'];
                            $generalledger_arr[$i]['transactionExchangeRate'] = $costgl_generalledgerr[$i]['transactionExchangeRate'];
                            $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $costgl_generalledgerr[$i]['transactionCurrencyDecimalPlaces'];
                            $generalledger_arr[$i]['companyLocalCurrencyID'] =  $costgl_generalledgerr[$i]['companyLocalCurrencyID'];
                            $generalledger_arr[$i]['companyLocalCurrency'] =  $costgl_generalledgerr[$i]['companyLocalCurrency'];
                            $generalledger_arr[$i]['companyLocalExchangeRate'] = $costgl_generalledgerr[$i]['companyLocalExchangeRate'];
                            $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $costgl_generalledgerr[$i]['companyLocalCurrencyDecimalPlaces'];
                            $generalledger_arr[$i]['companyReportingCurrencyID'] = $costgl_generalledgerr[$i]['companyReportingCurrencyID'];
                            $generalledger_arr[$i]['companyReportingCurrency'] = $costgl_generalledgerr[$i]['companyReportingCurrency'];
                            $generalledger_arr[$i]['companyReportingExchangeRate'] = $costgl_generalledgerr[$i]['companyReportingExchangeRate'];
                            $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $costgl_generalledgerr[$i]['companyReportingCurrencyDecimalPlaces'];
                            $generalledger_arr[$i]['confirmedByEmpID'] = $costgl_generalledgerr[$i]['confirmedByEmpID'];
                            $generalledger_arr[$i]['confirmedByName'] = $costgl_generalledgerr[$i]['confirmedByName'];
                            $generalledger_arr[$i]['confirmedDate'] = $costgl_generalledgerr[$i]['confirmedDate'];
                            $generalledger_arr[$i]['approvedDate'] = $costgl_generalledgerr[$i]['approvedDate'];
                            $generalledger_arr[$i]['approvedbyEmpID'] = $costgl_generalledgerr[$i]['approvedbyEmpID'];
                            $generalledger_arr[$i]['approvedbyEmpName'] = $costgl_generalledgerr[$i]['approvedbyEmpName'];
                            $generalledger_arr[$i]['companyID'] = $costgl_generalledgerr[$i]['companyID'];
                            $generalledger_arr[$i]['companyCode'] = '';
                            $generalledger_arr[$i]['transactionAmount'] = $costgl_generalledgerr[$i]['transactionAmount'];
                            $generalledger_arr[$i]['companyLocalAmount'] = $costgl_generalledgerr[$i]['companyLocalAmount'];
                            $generalledger_arr[$i]['companyReportingAmount'] = $costgl_generalledgerr[$i]['companyReportingAmount'];
                            $generalledger_arr[$i]['amount_type'] = $costgl_generalledgerr[$i]['amount_type'];
                            $generalledger_arr[$i]['GLAutoID'] = $costgl_generalledgerr[$i]['GLAutoID'];
                            $generalledger_arr[$i]['systemGLCode'] = $costgl_generalledgerr[$i]['systemGLCode'];
                            $generalledger_arr[$i]['GLCode'] = $costgl_generalledgerr[$i]['GLCode'];
                            $generalledger_arr[$i]['GLDescription'] = $costgl_generalledgerr[$i]['GLDescription'];
                            $generalledger_arr[$i]['GLType'] = $costgl_generalledgerr[$i]['GLType'];
                            $generalledger_arr[$i]['segmentID'] = $costgl_generalledgerr[$i]['segmentID'];
                            $generalledger_arr[$i]['segmentCode'] = $costgl_generalledgerr[$i]['segmentCode'];
                            $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                            $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                            $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                            $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                            $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                            $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                            $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                            $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                            $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
                        }

                        for ($i = 0; $i < count($assetgl_genralledger); $i++)
                        {
                            $generalledger_arr_asset[$i]['documentCode'] = $assetgl_genralledger[$i]['documentCode'];
                            $generalledger_arr_asset[$i]['documentMasterAutoID'] = $assetgl_genralledger[$i]['documentMasterAutoID'];
                            $generalledger_arr_asset[$i]['documentSystemCode'] = $assetgl_genralledger[$i]['documentSystemCode'];
                            $generalledger_arr_asset[$i]['documentDate'] = $assetgl_genralledger[$i]['documentdate'];
                            $generalledger_arr_asset[$i]['documentYear'] = $assetgl_genralledger[$i]['documentYear'];
                            $generalledger_arr_asset[$i]['documentMonth'] = $assetgl_genralledger[$i]['documentMonth'];
                            $generalledger_arr_asset[$i]['documentNarration'] = $assetgl_genralledger[$i]['documentNarration'];
                            $generalledger_arr_asset[$i]['chequeNumber'] = '';
                            $generalledger_arr_asset[$i]['transactionCurrencyID'] = $assetgl_genralledger[$i]['transactionCurrencyID'];
                            $generalledger_arr_asset[$i]['transactionCurrency'] = $assetgl_genralledger[$i]['transactionCurrency'];
                            $generalledger_arr_asset[$i]['transactionExchangeRate'] = $assetgl_genralledger[$i]['transactionExchangeRate'];
                            $generalledger_arr_asset[$i]['transactionCurrencyDecimalPlaces'] = $assetgl_genralledger[$i]['transactionCurrencyDecimalPlaces'];
                            $generalledger_arr_asset[$i]['companyLocalCurrencyID'] =  $assetgl_genralledger[$i]['companyLocalCurrencyID'];
                            $generalledger_arr_asset[$i]['companyLocalCurrency'] =  $assetgl_genralledger[$i]['companyLocalCurrency'];
                            $generalledger_arr_asset[$i]['companyLocalExchangeRate'] = $assetgl_genralledger[$i]['companyLocalExchangeRate'];
                            $generalledger_arr_asset[$i]['companyLocalCurrencyDecimalPlaces'] = $assetgl_genralledger[$i]['companyLocalCurrencyDecimalPlaces'];
                            $generalledger_arr_asset[$i]['companyReportingCurrencyID'] = $assetgl_genralledger[$i]['companyReportingCurrencyID'];
                            $generalledger_arr_asset[$i]['companyReportingCurrency'] = $assetgl_genralledger[$i]['companyReportingCurrency'];
                            $generalledger_arr_asset[$i]['companyReportingExchangeRate'] = $assetgl_genralledger[$i]['companyReportingExchangeRate'];
                            $generalledger_arr_asset[$i]['companyReportingCurrencyDecimalPlaces'] = $assetgl_genralledger[$i]['companyReportingCurrencyDecimalPlaces'];
                            $generalledger_arr_asset[$i]['confirmedByEmpID'] = $assetgl_genralledger[$i]['confirmedByEmpID'];
                            $generalledger_arr_asset[$i]['confirmedByName'] = $assetgl_genralledger[$i]['confirmedByName'];
                            $generalledger_arr_asset[$i]['confirmedDate'] = $assetgl_genralledger[$i]['confirmedDate'];
                            $generalledger_arr_asset[$i]['approvedDate'] = $assetgl_genralledger[$i]['approvedDate'];
                            $generalledger_arr_asset[$i]['approvedbyEmpID'] = $assetgl_genralledger[$i]['approvedbyEmpID'];
                            $generalledger_arr_asset[$i]['approvedbyEmpName'] = $assetgl_genralledger[$i]['approvedbyEmpName'];
                            $generalledger_arr_asset[$i]['companyID'] = $assetgl_genralledger[$i]['companyID'];
                            $generalledger_arr_asset[$i]['companyCode'] = '';
                            $generalledger_arr_asset[$i]['transactionAmount'] = $assetgl_genralledger[$i]['transactionAmount'];
                            $generalledger_arr_asset[$i]['companyLocalAmount'] = $assetgl_genralledger[$i]['companyLocalAmount'];
                            $generalledger_arr_asset[$i]['companyReportingAmount'] = $assetgl_genralledger[$i]['companyReportingAmount'];
                            $generalledger_arr_asset[$i]['amount_type'] = $assetgl_genralledger[$i]['amount_type'];
                            $generalledger_arr_asset[$i]['GLAutoID'] = $assetgl_genralledger[$i]['GLAutoID'];
                            $generalledger_arr_asset[$i]['systemGLCode'] = $assetgl_genralledger[$i]['systemGLCode'];
                            $generalledger_arr_asset[$i]['GLCode'] = $assetgl_genralledger[$i]['GLCode'];
                            $generalledger_arr_asset[$i]['GLDescription'] = $assetgl_genralledger[$i]['GLDescription'];
                            $generalledger_arr_asset[$i]['GLType'] = $assetgl_genralledger[$i]['GLType'];
                            $generalledger_arr_asset[$i]['segmentID'] = $assetgl_genralledger[$i]['segmentID'];
                            $generalledger_arr_asset[$i]['segmentCode'] = $assetgl_genralledger[$i]['segmentCode'];
                            $generalledger_arr_asset[$i]['createdUserGroup'] = $this->common_data['user_group'];
                            $generalledger_arr_asset[$i]['createdPCID'] = $this->common_data['current_pc'];
                            $generalledger_arr_asset[$i]['createdUserID'] = $this->common_data['current_userID'];
                            $generalledger_arr_asset[$i]['createdDateTime'] = $this->common_data['current_date'];
                            $generalledger_arr_asset[$i]['createdUserName'] = $this->common_data['current_user'];
                            $generalledger_arr_asset[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                            $generalledger_arr_asset[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                            $generalledger_arr_asset[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                            $generalledger_arr_asset[$i]['modifiedUserName'] = $this->common_data['current_user'];
                        }

                        if (!empty($generalledger_arr))
                        {
                            $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                        }

                        if (!empty($generalledger_arr_asset))
                        {
                            $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr_asset);
                        }
                    }
                }
            }
            $this->db->where('maintenanceMasterID', $maintenacemasterid);
            $this->db->where('companyID', $companyID);
            $this->db->update('fleet_maintenancemaster', $data);

            // If $assetData['assetStatus'] is set, update the fleet_vehiclemaster
            if (isset($assetData['assetStatus']))
            {
                $this->db->where('vehicleMasterID', $vehicleMasterID);
                $this->db->where('companyID', $companyID);
                $this->db->update('fleet_vehiclemaster', $assetData);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return array('e', "Status Update Failed." . $this->db->_error_message());
            }
            else
            {
                return array('s', 'Status updated successfully!.', $supplierinvoicecode);
            }
        }
    }
    function fetch_maintenance_overview()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        // Construct the query to get the total maintenance count
        $this->db->select('COUNT(*) as totalMaintenanceCount');
        $this->db->from('fleet_maintenancemaster');
        $this->db->where('companyID', $companyID);
        $totalQuery = $this->db->get();

        // Get the total maintenance count
        $totalMaintenanceCount = $totalQuery->row()->totalMaintenanceCount;

        // Construct the query to get the count per maintenance type
        $this->db->select('maintenanceType.maintenanceTypeID, maintenanceType.type, COALESCE(COUNT(maintenanceMaster.maintenanceType), 0) as total');
        $this->db->from('fleet_maintenancetype as maintenanceType');
        $this->db->join('fleet_maintenancemaster as maintenanceMaster', 'maintenanceMaster.maintenanceType = maintenanceType.maintenanceTypeID AND maintenanceMaster.companyID = ' . $this->db->escape($companyID), 'left');
        $this->db->group_by('maintenanceType.maintenanceTypeID');

        // Execute the query for maintenance types
        $maintenanceTypesQuery = $this->db->get();

        // Construct the query to get the count per maintenance status
        $this->db->select('status, COALESCE(COUNT(*), 0) as total');
        $this->db->from('fleet_maintenancemaster');
        $this->db->where('companyID', $companyID);
        $this->db->group_by('status');

        // Execute the query for maintenance status
        $statusQuery = $this->db->get();

        // Initialize status counts
        $maintenanceStatus = [
            ['status' => 1, 'total' => 0], // Due for Maintenance
            ['status' => 2, 'total' => 0], // Ongoing
        ];

        // Update the status counts with actual values from the query
        foreach ($statusQuery->result() as $row)
        {
            if ($row->status == 1)
            {
                $maintenanceStatus[0]['total'] = $row->total;
            }
            elseif ($row->status == 2)
            {
                $maintenanceStatus[1]['total'] = $row->total;
            }
        }

        // Combine the results
        $result = array(
            'totalMaintenanceCount' => $totalMaintenanceCount,
            'maintenanceTypes' => $maintenanceTypesQuery->result(),
            'maintenanceStatus' => $maintenanceStatus
        );

        // Return the result as an array with the total count, type data, and status data
        return $result;
    }


    function fetch_asset_under_maintenance()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        // Construct the query to get the assets under maintenance
        $this->db->select('
            assetMaster.vehDescription,
            assetMaster.vehicleCode,
            location.locationName,
            assetStatus.id AS assetStatusID,
            assetStatus.status AS assetStatusDesc
        ');
        $this->db->from('fleet_maintenancemaster AS maintenaceMaster');
        $this->db->join('fleet_vehiclemaster AS assetMaster', 'maintenaceMaster.vehicleMasterID = assetMaster.vehicleMasterID', 'left');
        $this->db->join('srp_erp_location AS location', 'assetMaster.locationID = location.locationID', 'left');
        $this->db->join('fleet_asset_utilization_status AS assetStatus', 'assetMaster.assetStatus = assetStatus.id', 'left');
        $this->db->where('maintenaceMaster.companyID', $companyID);

        // Execute the query
        $query = $this->db->get();

        // Fetch the results
        $assets = $query->result();

        // Prepare the results with status badges
        foreach ($assets as $asset)
        {
            $asset->status = load_assetstatus($asset->assetStatusID, $asset->assetStatusDesc);
        }

        // Return the results as an array
        return $assets;
    }

    function fetch_dashboard_asset_status()
    {
        $companyID = $this->common_data['company_data']['company_id'];


        $this->db->select('
        assetStatus.id AS assetStatusID,
        assetStatus.status AS assetStatusDesc,
        COUNT(*) AS total
    ');
        $this->db->from('fleet_maintenancemaster AS maintenaceMaster');
        $this->db->join('fleet_vehiclemaster AS assetMaster', 'maintenaceMaster.vehicleMasterID = assetMaster.vehicleMasterID', 'left');
        $this->db->join('fleet_asset_utilization_status AS assetStatus', 'assetMaster.assetStatus = assetStatus.id', 'left');
        $this->db->where('maintenaceMaster.companyID', $companyID);
        $this->db->group_by('assetStatus.id');


        $query = $this->db->get();


        $assetStatusCounts = $query->result();


        return $assetStatusCounts;
    }
    public function save_templates()
    {
        $this->db->trans_start();

        $documentMasterAutoID = $this->input->post('documentMasterAutoID');
        $checklistID = $this->input->post('checklistID');
        $assetID = $this->input->post('assetID');
        $description = $this->input->post('description');
        $companyid = current_companyID();
        $userGroup = $this->common_data['user_group'];
        $createdPC = $this->common_data['current_pc'];
        $createUser = $this->common_data['current_user'];


        $this->db->select('id');
        $this->db->from('srp_erp_document_templates');
        $this->db->where('documentMasterAutoID', $documentMasterAutoID);
        $this->db->where('documentDetailID', $checklistID);
        $this->db->where('templateMasterID', $assetID);
        $this->db->where('companyID', $companyid);
        $existingRecord = $this->db->get();

        if ($existingRecord->num_rows() > 0)
        {

            return array('e', 'Template is already loaded to this asset');
        }


        $this->db->select('doc_code');
        $this->db->where('id', $documentMasterAutoID);
        $query = $this->db->get('fleet_asset_utilization');

        if ($query->num_rows() > 0)
        {
            // $doc_code = $query->row()->doc_code ;
            $data = array(
                'documentCode' => 'UT',
                'documentMasterAutoID' => $documentMasterAutoID,
                'documentDetailID' => $checklistID,
                'templateMasterID' => $assetID,
                'description' => $description,
                'companyID' => $companyid,
                'createdUserGroup' => $userGroup,
                'createuser' => $createUser,
                'createdpc' => $createdPC,
                'createDateTime' => date('Y-m-d H:i:s'),
                'modifieduser' => $createUser,
                'modifiedpc' => $createdPC,
                'modifiedDateTime' => date('Y-m-d H:i:s'),
                'timestamp' => date('Y-m-d H:i:s')
            );


            $this->db->insert('srp_erp_document_templates', $data);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Template loaded successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Error in loading template');
        }
    }

    function deleteTemplate()
    {
        $ID = trim($this->input->post('id') ?? '');

        $this->db->trans_start();

        $this->db->where('id', $ID)->delete('srp_erp_document_templates');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Records deleted successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Error in deleting process');
        }
    }

    function save_document_templates_details()
    {
        $documentTemplateID = $this->input->post('documentTemplateID');
        $companyid = current_companyID();
        $userGroup = $this->common_data['user_group'];
        $createdPC = $this->common_data['current_pc'];
        $createUser = $this->common_data['current_user'];
        $formData = $this->input->post('formData');


        if (!$documentTemplateID)
        {


            return array('e', 'Document Template ID is required');
        }


        $this->db->select('*');
        $this->db->from('srp_erp_document_templates_details');
        $this->db->where('documentTemplateID', $documentTemplateID);
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {

            return array('e', 'Document already exists');
        }


        if (!is_array($formData) || empty($formData))
        {

            return array('e', 'Incomplete data received');
        }

        $this->db->trans_start();


        foreach ($formData as $inputId => $inputValue)
        {
            $data = [
                'documentTemplateID' => $documentTemplateID,
                'uniqueKeyID' => $inputId,
                'contentValue' => $inputValue,
                'companyID' => $companyid,
                'createdUserGroup' => $userGroup,
                'createuser' => $createUser,
                'createdpc' => $createdPC,
                'createDateTime' => date('Y-m-d H:i:s'),
                'timestamp' => date('Y-m-d H:i:s')
            ];


            $this->db->insert('srp_erp_document_templates_details', $data);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() == true)
        {
            $this->db->trans_commit();
            return array('s', 'Data saved successfully');
        }
        else
        {
            $this->db->trans_rollback();
            return array('e', 'Database transaction failed');
        }
    }

    function edit_document_templates_details($documentTemplateID, $formData)
    {
        $companyid = current_companyID();
        $userGroup = $this->common_data['user_group'];
        $createdPC = $this->common_data['current_pc'];
        $createUser = $this->common_data['current_user'];

        $this->db->trans_start();

        foreach ($formData as $inputId => $inputValue)
        {
            $data = [
                'contentValue' => $inputValue,
                'modifieduser' => $createUser,
                'modifiedpc' => $createdPC,
                'modifiedDateTime' => date('Y-m-d H:i:s'),
            ];

            $this->db->where('documentTemplateID', $documentTemplateID);
            $this->db->where('uniqueKeyID', $inputId);
            $this->db->update('srp_erp_document_templates_details', $data);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE)
        {
            return ['s', 'Data updated successfully'];
        }
        else
        {
            return ['e', 'Database transaction failed'];
        }
    }
}
