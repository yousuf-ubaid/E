<?php

class journeyplan_model extends ERP_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function load_vehicale_details()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = current_companyID();
        $vehicalemasterid = trim($this->input->post('vehicalemasterid') ?? '');
        $data = $this->db->query("select *,CASE WHEN `isActive` = \"1\" THEN \"Active\" ELSE \"In Actice\" END vehicalestatus from fleet_vehiclemaster where companyID = $companyid AND vehicleMasterID = $vehicalemasterid")->row_array();

        $data['vehicaleimagenew'] = $this->s3->createPresignedRequest('uploads/Fleet/VehicleImg/' . $data['vehicleImage'], '1 hour');

        return $data;
    }

    function load_driver_details()
    {
        $companyid = current_companyID();
        $drivermasterid = trim($this->input->post('drivermasterid') ?? '');
        $data = $this->db->query("select * from fleet_drivermaster  where companyID = $companyid AND driverMasID = $drivermasterid")->row_array();
        return $data;
    }

    function fetch_employee_details()
    {
        $companyid = current_companyID();
        $employeeid = trim($this->input->post('employeeid') ?? '');
        $data = $this->db->query("select * from srp_employeesdetails where Erp_companyID = $companyid AND EIdNo = $employeeid")->row_array();
        return $data;
    }

    function fetch_jp_number()
    {
        $companyid = current_companyID();
        $serialno = $this->db->query("SELECT IF( isnull( MAX( serialNumber ) ), 1, ( MAX( serialNumber ) + 1 ) ) AS serialNumber FROM srp_erp_journeyplan_master WHERE companyID = $companyid")->row_array();
        $company_code = $this->common_data['company_data']['company_code'];

        $data = ($company_code . '/' . 'JP' . str_pad($serialno['serialNumber'], 6, '0', STR_PAD_LEFT));

        return $data;

    }

    function save_journeyplan_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $company_code = $this->common_data['company_data']['company_code'];
        $companyID = $this->common_data['company_data']['company_id'];
        $departuredate = $this->input->post('departuredate');
        $jpmasterid = trim($this->input->post('jpmasterid') ?? '');
        $curDate = format_date_mysql_datetime();
        $format_departuredate = null;
        if (isset($departuredate) && !empty($departuredate)) {
            $format_departuredate = input_format_date($departuredate, $date_format_policy);
        }


        $data['departureDate'] = $format_departuredate;
        $data['vehicleID'] = $this->input->post('vehiclenumber');
        $data['driverID'] = $this->input->post('drivername');
        $data['driverMobileNumber'] = $this->input->post('phonenumber');
        $data['commentsForDriver'] = $this->input->post('commentfordrivers');
        $data['offlineTrackingRefNo'] = $this->input->post('offlinetrackingnumber');
        if ($this->input->post('employeeID')) {
            $empdet = explode('|', trim($this->input->post('employee_det') ?? ''));
            $data['journeyManagerName'] = $empdet[1];
            $data['journeyManagerEmpID'] = trim($this->input->post('employeeID') ?? '');
            $data['journeyManagerOfficeNo'] = $this->input->post('jmphonenumber');
            $data['journeyManagerMobileNo'] = $this->input->post('jmphonenumbermob');
        } else {
            $data['journeyManagerName'] = trim($this->input->post('journeymanager') ?? '');
            $data['journeyManagerEmpID'] = NULL;
            $data['journeyManagerOfficeNo'] = $this->input->post('jmphonenumber');
            $data['journeyManagerMobileNo'] = $this->input->post('jmphonenumbermob');
        }

        $data['reasonForNightDriving'] = $this->input->post('reasonnightdriving');
        $data['vehicleDailyCheck'] = $this->input->post('vehicaledailychk');
        $data['counsellingForDriver'] = $this->input->post('counsellingdr');
        $data['companyID'] = $companyID;
        $data['timestamp'] = $curDate;
        if ($jpmasterid) {
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedDate'] = $this->common_data['current_date'];
            $this->db->where('journeyPlanMasterID', $jpmasterid);
            $this->db->update('srp_erp_journeyplan_master', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error while updating the Journey Plan!');
            } else {
                $this->db->trans_commit();
                return array('s', 'Journey Plan updated Successfully!', $jpmasterid);
            }
        } else {
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdDate'] = $this->common_data['current_date'];
            $data['documentID'] = 'JP';
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNumber ) ), 1, ( MAX( serialNumber ) + 1 ) ) AS serialNumber FROM srp_erp_journeyplan_master WHERE companyID = $companyID")->row_array();
            $data['serialNumber'] = $serial['serialNumber'];
            $data['documentCode'] = ($company_code . '/' . 'JP' . str_pad($data['serialNumber'], 6, '0', STR_PAD_LEFT));
            $this->db->insert('srp_erp_journeyplan_master', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error Occured' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', '' . $data['documentCode'] . ' Journey Plan created successfully.', $last_id);
            }
        }
    }

    function save_journeyplan_header_map_tour()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $company_code = $this->common_data['company_data']['company_code'];
        $companyID = $this->common_data['company_data']['company_id'];
        $departuredate = $this->input->post('departuredate');
        $jpmasterid = trim($this->input->post('jpmasterid') ?? '');
        $curDate = format_date_mysql_datetime();
        $format_departuredate = null;
        if (isset($departuredate) && !empty($departuredate)) {
            $format_departuredate = input_format_date($departuredate, $date_format_policy);
        }

        $data['departureDate'] = $format_departuredate;
        $data['driverID'] = $this->input->post('drivername');
        $data['journeyTypeID'] = $this->input->post('tourType');
        $data['driverMobileNumber'] = $this->input->post('phonenumber');
        $data['guideName'] = $this->input->post('guideName');
        $data['guidePhoneNumber'] = $this->input->post('guideNumber');
        $data['guestName'] = $this->input->post('guestName');
        $data['agentName'] = $this->input->post('villaHost');
        $data['roomNo'] = $this->input->post('roomNo');
        $data['depatureTime'] = $this->input->post('pickupTime');
        $data['noOfPassengers'] = $this->input->post('noofpassengers');
        $commentsForDriver = $this->input->post('commentfordrivers');
        $data['commentsForDriver'] = str_replace('<br />', PHP_EOL, $commentsForDriver);
        $data['offlineTrackingRefNo'] = $this->input->post('offlinetrackingnumber');
        $data['transactionCurrrencyID'] = $this->input->post('transactionCurrencyID');
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        if ($this->input->post('employeeID')) {
            $empdet = explode('|', trim($this->input->post('employee_det') ?? ''));
            $data['journeyManagerName'] = $empdet[1];
            $data['journeyManagerEmpID'] = trim($this->input->post('employeeID') ?? '');
            $data['journeyManagerOfficeNo'] = $this->input->post('jmphonenumber');
            $data['journeyManagerMobileNo'] = $this->input->post('jmphonenumbermob');
        } else {
            $data['journeyManagerName'] = trim($this->input->post('journeymanager') ?? '');
            $data['journeyManagerEmpID'] = NULL;
            $data['journeyManagerOfficeNo'] = $this->input->post('jmphonenumber');
            $data['journeyManagerMobileNo'] = $this->input->post('jmphonenumbermob');
        }
        if ($this->input->post('vehicleID')>0) {
            $data['vehicleID'] = $this->input->post('vehicleID');
            $data['vehicleNumber'] = $this->input->post('vehicle_Num');
            $data['ivmsNumber'] = '';
        } else {
            $data['vehicleNumber'] = $this->input->post('vehiclenumber');
            $data['ivmsNumber'] = $this->input->post('ivmsNumber_add');
            $data['vehicleID'] = '';
        }

        $reasonForNightDriving = $this->input->post('reasonnightdriving');
        $data['reasonForNightDriving'] = str_replace('<br />', PHP_EOL, $reasonForNightDriving);
        $data['vehicleDailyCheck'] = $this->input->post('vehicaledailychk');
        $data['counsellingForDriver'] = $this->input->post('counsellingdr');
        $data['companyID'] = $companyID;
        $data['timestamp'] = $curDate;
        if ($jpmasterid) {
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedDate'] = $this->common_data['current_date'];
            $this->db->where('journeyPlanMasterID', $jpmasterid);
            $this->db->update('srp_erp_journeyplan_master', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error while updating the Journey Plan!');
            } else {
                $this->db->trans_commit();
                return array('s', 'Journey Plan updated Successfully!', $jpmasterid);
            }
        } else {
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdDate'] = $this->common_data['current_date'];
            $data['documentID'] = 'JP';
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNumber ) ), 1, ( MAX( serialNumber ) + 1 ) ) AS serialNumber FROM srp_erp_journeyplan_master WHERE companyID = $companyID")->row_array();
            $data['serialNumber'] = $serial['serialNumber'];
            $data['documentCode'] = ($company_code . '/' . 'JP' . str_pad($data['serialNumber'], 6, '0', STR_PAD_LEFT));
            $this->db->insert('srp_erp_journeyplan_master', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error Occured' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', '' . $data['documentCode'] . ' Journey Plan created successfully.', $last_id);
            }
        }
    }

    function fetch_jp_header_details()
    {
        $companyid = current_companyID();
        $jpmasterid = trim($this->input->post('jpnumber') ?? '');
        $convertFormat = convert_date_format_sql();
        $data = $this->db->query("SELECT jpmaster.*,DATE_FORMAT(jpmaster.departureDate,'.$convertFormat. %h:%i:%s') AS departureDateconverted,driver.driverName,driver.driverCode,vehicalemaster.vehDescription,vehicalemaster.vehicleCode,vehicalemaster.ivmsNo FROM
	srp_erp_journeyplan_master jpmaster LEFT JOIN fleet_drivermaster driver on driver.driverMasID =  jpmaster.driverID LEFT JOIN fleet_vehiclemaster vehicalemaster on vehicalemaster.vehicleMasterID =  jpmaster.vehicleID
WHERE jpmaster.companyID = $companyid AND jpmaster.journeyPlanMasterID = $jpmasterid")->row_array();
        return $data;
    }

    function save_jp_details()
    {
        $this->db->trans_start();
        $restyn = $this->input->post('restyn');
        $jpnumber = $this->input->post('jpnumberadddetail');
        $arrivedDate = $this->input->post('arrivedate');
        $dateDepart = $this->input->post('departdate');
        $placename = $this->input->post('placenames');
        $arivetime = $this->input->post('arrivetime');
        $departtime = $this->input->post('departtime');
        $sleep = $this->input->post('sleepmotelname');
        $date_format_policy = date_format_policy();

        $this->db->delete('srp_erp_journeyplan_routedetails', array('journeyPlanMasterID' => $jpnumber));
        foreach ($restyn as $key => $val) {
            $format_arrivedDate = null;
            $format_dateDepart = null;
            if (isset($arrivedDate[$key]) && !empty($arrivedDate[$key])) {
                $format_arrivedDate = input_format_date($arrivedDate[$key], $date_format_policy);
            }
            if (isset($dateDepart[$key]) && !empty($dateDepart[$key])) {
                $format_dateDepart = input_format_date($dateDepart[$key], $date_format_policy);
            }
            $data[$key]['journeyPlanMasterID'] = $jpnumber;
            $data[$key]['placeName'] = $placename[$key];
            if ($key == 0) {
                $data[$key]['dateArived'] = '';
                $data[$key]['timeArrive'] = '';
            } else {
                $data[$key]['dateArived'] = $format_arrivedDate;
                $data[$key]['timeArrive'] = $arivetime[$key];
            }

            $data[$key]['dateDepart'] = $format_dateDepart;
            $data[$key]['timeDepart'] = $departtime[$key];
            $data[$key]['restTick'] = $val;
            $data[$key]['sleep'] = $sleep[$key];
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdDate'] = $this->common_data['current_date'];
        }
        $this->db->insert_batch('srp_erp_journeyplan_routedetails', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Journey Plan Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Journey Plan Detail :  Saved Successfully.');
        }
    }

    function fetch_item_jv_table()
    {
        $companyid = current_companyID();
        $jpnumber = $this->input->post('jpnumber');
        $convertFormat = convert_date_format_sql();

        $data['detail'] = $this->db->query("select *,DATE_FORMAT( dateArived, '$convertFormat' ) AS dateArivedcon,DATE_FORMAT( dateDepart, '$convertFormat' ) AS dateDepartcon  from srp_erp_journeyplan_routedetails where companyID = $companyid  And journeyPlanMasterID = $jpnumber")->result_array();
        return $data;
    }

    function save_jp_passanger_details()
    {
        $this->db->trans_start();
        $jpnumber = $this->input->post('jpnumberadd');
        $passanger = $this->input->post('passangername');
        $conatctno = $this->input->post('contactno');
        $this->db->delete('srp_erp_journeyplan_passengerdetails', array('journeyPlanMasterID' => $jpnumber));
        foreach ($passanger as $key => $val) {

            $data[$key]['journeyPlanMasterID'] = $jpnumber;
            $data[$key]['passengerName'] = $val;
            $data[$key]['contactNo'] = $conatctno[$key];
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdDate'] = $this->common_data['current_date'];

        }

        $datapassenger['noOfPassengers'] = sizeof($passanger);
        $this->db->where('journeyPlanMasterID', $jpnumber);
        $this->db->update('srp_erp_journeyplan_master', $datapassenger);

        $this->db->insert_batch('srp_erp_journeyplan_passengerdetails', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Journey Plan Passanger Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Journey Plan Passanger Detail :  Saved Successfully.');
        }
    }

    function fetch_passenger_detail_tbl()
    {
        $companyid = current_companyID();
        $jpnumber = $this->input->post('jpnumber');
        $data['detail'] = $this->db->query("select * from srp_erp_journeyplan_passengerdetails where companyID = $companyid AND journeyPlanMasterID = $jpnumber ")->result_array();
        return $data;
    }

    function jp_confirmation()
    {
        {
            $jpmasterid = trim($this->input->post('jpnumber') ?? '');
            $this->db->select('journeyPlanMasterID');
            $this->db->where('journeyPlanMasterID', $jpmasterid);
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_journeyplan_master');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->load->library('approvals');
                $this->db->select('journeyPlanMasterID, documentID,documentCode');
                $this->db->where('journeyPlanMasterID', $jpmasterid);
                $this->db->from('srp_erp_journeyplan_master');
                $app_data = $this->db->get()->row_array();

                $validate_code = validate_code_duplication($app_data['documentCode'], 'documentCode', $jpmasterid,'journeyPlanMasterID', 'srp_erp_journeyplan_master');
                if(!empty($validate_code)) {
                    $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    return false;
                }

                $approvals_status = $this->approvals->CreateApproval('JP', $app_data['journeyPlanMasterID'], $app_data['documentCode'], 'Journey Plan', 'srp_erp_journeyplan_master', 'journeyPlanMasterID');
                if ($approvals_status == 1) {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );
                    $this->db->where('journeyPlanMasterID', trim($this->input->post('jpnumber') ?? ''));
                    $this->db->update('srp_erp_journeyplan_master', $data);

                    return array('error' => 0, 'message' => 'document successfully confirmed');
                } else {
                    return array('error' => 1, 'message' => 'Approval setting are not configured!, please contact your system team.');
                }
            }
        }
    }

    function fetch_jp_details($jpmasterid)
    {
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();


        $data['detail'] = $this->db->query("SELECT
	mastertbl.*,
	DATE_FORMAT( departureDate, '%d-%m-%Y' ) AS departureDatecon,
	fleettbl.driverName,
	vehicalemaster.ivmsNo as invmsnovehicalemaster,
vehicalemaster.VehicleNo as VehicleNo,
vehicalemaster.vehDescription as vehDescription,
DATE_FORMAT(vehicalemaster.licenseDate,'$convertFormat') as licensedate,
DATE_FORMAT(vehicalemaster.insuranceDate,'$convertFormat') as insurancedate,
DATE_FORMAT(mastertbl.approvedDate,'$convertFormat') as approvedDatemaster,
DATE_FORMAT(mastertbl.createdDate,'$convertFormat') as createdDate,
	currency.DecimalPlaces as deci,
	tourtyype.description as tourdescription,
	CASE
	
	WHEN mastertbl.vehicleDailyCheck = \"1\" THEN
	\"Yes\" 
	ELSE 
		\"No\"
	END vehicleDailyCheckyn,
	CASE
WHEN mastertbl.counsellingForDriver = \"1\" THEN
	\"Yes\" 
	ELSE 
		\"No\"
	END counsellingForDriveryn 
FROM
	srp_erp_journeyplan_master  mastertbl
	left join fleet_drivermaster fleettbl on fleettbl.driverMasID = mastertbl.driverID
	LEFT JOIN fleet_vehiclemaster vehicalemaster ON vehicalemaster.vehicleMasterID = mastertbl.vehicleID 
	LEFT JOIN srp_erp_currencymaster currency on currency.currencyID = mastertbl.transactionCurrrencyID
	LEFT JOIN srp_erp_journeyplan_tourtype tourtyype on tourtyype.tourTypeID = mastertbl.journeyTypeID
WHERE
	mastertbl.companyID = $companyid 
	AND journeyPlanMasterID = $jpmasterid")->row_array();


        $data['passengerdet'] = $this->db->query("SELECT
	* 
FROM
	`srp_erp_journeyplan_passengerdetails`
	where 
	companyID = $companyid
	AND journeyPlanMasterID = $jpmasterid")->result_array();

        $data['routedetail'] = $this->db->query("SELECT
	*,
	CONCAT(DATE_FORMAT( dateDepart, '%d-%m-%Y' ),' - ',timeDepart) as departureDatecon,
		CONCAT(DATE_FORMAT( dateArived, '%d-%m-%Y' ),' - ',timeArrive) as arrivedcon,
	CASE
WHEN restTick = \"1\" THEN
	\"Yes\" 
	ELSE 
		\"No\"
	END restTick 
FROM
	`srp_erp_journeyplan_routedetails` 
WHERE
	companyID = $companyid 
	AND journeyPlanMasterID = $jpmasterid")->result_array();

        $data['tourcharge'] = $this->db->query("SELECT
	amount,
	remarks,
	CONCAT(itemSystemCode,' | ', itemName) as itemdesc
FROM
	`srp_erp_journeyplancharges` jpmaster
	LEFT join srp_erp_itemmaster itemmaster on jpmaster.itemAutoID = itemmaster.itemAutoID
	where 
	chargeType = 1 And journeyPlanMasterID = $jpmasterid AND jpmaster.companyID = $companyid
")->result_array();
        $data['additionalcharge'] = $this->db->query("SELECT
	amount,
	remarks,
	CONCAT(itemSystemCode,' | ', itemName) as itemdesc
FROM
	`srp_erp_journeyplancharges` jpmaster
	LEFT join srp_erp_itemmaster itemmaster on jpmaster.itemAutoID = itemmaster.itemAutoID
	where 
	chargeType = 2 And journeyPlanMasterID =$jpmasterid  AND jpmaster.companyID = $companyid
")->result_array();



        return $data;
    }

    function save_jp_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('jurneyplanid') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = '';
        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'JP');

        if ($approvals_status == 1) {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];


            $this->db->where('journeyPlanMasterID', $system_id);
            $this->db->update('srp_erp_journeyplan_master', $data);
            $this->session->set_flashdata('s', 'Journey Plan Approved Sucessfully.');
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

    function save_jp_status()
    {

        $this->db->trans_start();
        $jpmasterid = $this->input->post('masterid');

        $this->db->select('status');
        $this->db->where('journeyPlanMasterID', $jpmasterid);
        $this->db->from('srp_erp_journeyplan_master');
        $jpmaster = $this->db->get()->row_array();
        if ($jpmaster['status'] == 3) {
            return array('e', 'You cannot change the status journey plan already closed');
        } else {

            $data['status'] = $this->input->post('status');
            $data['statusComments'] = $this->input->post('comment');
            $data['journeyPlanMasterID'] = $jpmasterid;
            $data['companyID'] = current_companyID();
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_journeyplanstatus', $data);


            $dataup['status'] = $this->input->post('status');
            $dataup['statusComment'] = $this->input->post('comment');

            if ($dataup['status'] == 3) {
                $dataup['closedComment'] = $this->input->post('comment');
                $dataup['closedByEmpID'] = $this->common_data['current_userID'];
                $dataup['closedByEmpName'] = $this->common_data['current_user'];
                $dataup['closedByDate'] = $this->common_data['current_date'];
            }
            if ($dataup['status'] == 4) {
                $dataup['canceledYN'] = $this->input->post('status');
                $dataup['canceledDate'] = $this->common_data['current_date'];
                $dataup['canceledEmpId'] = $this->common_data['current_userID'];
                $dataup['canceledEmpName'] = $this->common_data['current_userID'];
                $dataup['canceledComment'] = $this->input->post('comment');
            }

            $this->db->where('journeyPlanMasterID', $jpmasterid);
            $this->db->update('srp_erp_journeyplan_master', $dataup);


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error while updating the Journey Plan!');
            } else {
                $this->db->trans_commit();
                return array('s', 'Journey Plan Status updated Successfully!', $jpmasterid);
            }
        }


    }

    public function jpdetails($jpid)
    {

        $this->db->select('*');

        $this->db->from('srp_erp_journeyplan_master');

        $this->db->where('journeyPlanMasterID', $jpid);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result_array();
        } else {
            return 0;
        }
    }

    function checkRevenueGL_tour_price()
    {
        $companyid = current_companyID();
        $itemAutoID = $this->input->post('itemAutoID');
        if ($itemAutoID) {
            $data = $this->db->query("SELECT revanueGLAutoID FROM srp_erp_itemmaster WHERE companyID = $companyid AND itemAutoID = $itemAutoID")->row_array();
            if (empty($data) || $data['revanueGLAutoID'] == 0) {
                return array('e', 'Revenue GL is not set for selected Item');
            }
        }
    }

    function save_tour_price_details()
    {
        $this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        $jpnumber = $this->input->post('jpnumberadd');
        $itemAutoID = $this->input->post('itemAutoID');
        $chargeType = $this->input->post('chargeType');
        $amount = $this->input->post('amount');
        $remarks = $this->input->post('remarks');
        $this->db->delete('srp_erp_journeyplancharges', array('journeyPlanMasterID' => $jpnumber, 'chargeType' => $chargeType));
        $currencyDetails = $this->db->query("SELECT transactionCurrrencyID, companyLocalCurrencyID, companyReportingCurrencyID FROM srp_erp_journeyplan_master WHERE companyID = $companyID AND journeyPlanMasterID = $jpnumber")->row_array();
        foreach ($itemAutoID as $key => $val) {
            $data[$key]['journeyPlanMasterID'] = $jpnumber;
            $data[$key]['chargeType'] = $chargeType;
            $data[$key]['itemAutoID'] = $val;
            $data[$key]['remarks'] = $remarks[$key];
            $data[$key]['transactionCurrencyID'] = $currencyDetails['transactionCurrrencyID'];
            $data[$key]['transactionExchangeRate'] = 1;
            $data[$key]['amount'] = $amount[$key];
            $data[$key]['companyLocalCurrencyID'] = $currencyDetails['companyLocalCurrencyID'];
            $default_currency = currency_conversionID($data[$key]['transactionCurrencyID'], $data[$key]['companyLocalCurrencyID']);
            $data[$key]['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data[$key]['companyLocalAmount'] = ($data[$key]['amount'] / $data[$key]['companyLocalExchangeRate']);
            $data[$key]['companyReportingCurrencyID'] = $currencyDetails['companyReportingCurrencyID'];
            $reporting_currency = currency_conversionID($data[$key]['transactionCurrencyID'], $data[$key]['companyReportingCurrencyID']);
            $data[$key]['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data[$key]['companyReportingAmount'] =  ($data[$key]['amount'] / $data[$key]['companyReportingExchangeRate']);
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['createdUserID'] = $this->common_data['current_userID'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdDate'] = $this->common_data['current_date'];
        }

        $this->db->insert_batch('srp_erp_journeyplancharges', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Journey Plan Tour Charges Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Journey Plan Tour Charges Detail :  Saved Successfully.');
        }
    }

    function fetch_tour_price_details()
    {
        $jpnumber = $this->input->post('jpnumber');
        $chargeType = $this->input->post('chargeType');
        $companyid = current_companyID();
        $data['details'] = $this->db->query("SELECT srp_erp_journeyplancharges.amount, srp_erp_journeyplancharges.remarks, srp_erp_journeyplancharges.itemAutoID, CONCAT(item.itemSystemCode, ' | ', item.itemDescription) as itemDescription  FROM srp_erp_journeyplancharges LEFT JOIN srp_erp_itemmaster item ON item.itemAutoID = srp_erp_journeyplancharges.itemAutoID WHERE item.companyID = $companyid AND journeyPlanMasterID = $jpnumber AND chargeType = $chargeType")->result_array();

        return $data;
    }

    function fetch_tour_price_items()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyid = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query("SELECT itemAutoID, CONCAT(itemSystemCode, ' | ', itemDescription) as itemDescription, itemSystemCode FROM srp_erp_itemmaster  WHERE srp_erp_itemmaster.companyID = $companyid  AND mainCategory = 'Service' AND (itemSystemCode LIKE  '" . $search_string . "' OR itemDescription LIKE '" . $search_string . "')")->result_array();
        //echo $this->db->last_query();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["itemDescription"], 'data' => $val['itemSystemCode'] , 'itemAutoID' => $val['itemAutoID'] );
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }
    function fetch_additionalCharges_items()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyid = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query("SELECT itemAutoID, CONCAT(itemSystemCode, ' | ', itemDescription) as itemDescription, itemSystemCode FROM srp_erp_itemmaster  WHERE srp_erp_itemmaster.companyID = $companyid  AND mainCategory = 'Service' AND (itemSystemCode LIKE  '" . $search_string . "' OR itemDescription LIKE '" . $search_string . "')")->result_array();
        //echo $this->db->last_query();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["itemDescription"], 'data' => $val['itemSystemCode'] , 'itemAutoID' => $val['itemAutoID'] );
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function jp_create_customer_invoice()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $journeyPlanMasterID = trim($this->input->post('journeyPlanMasterID') ?? '');
        $customerInvoiceDate = trim($this->input->post('customerInvoiceDate') ?? '');
        $invoiceDate = input_format_date($customerInvoiceDate, $date_format_policy);
        $customerID = trim($this->input->post('customerID') ?? '');

        $customerDetails = $this->db->query("SELECT * FROM srp_erp_customermaster WHERE companyID = $companyID AND customerAutoID = $customerID")->row_array();
        $tourPriceDetails = $this->db->query("SELECT currency.CurrencyCode, documentCode, transactionCurrrencyID, companyLocalCurrencyID, companyReportingCurrencyID, details.amount as amount 
                    FROM srp_erp_journeyplan_master master 
                    LEFT JOIN (
                                SELECT journeyPlanMasterID, SUM(amount) as amount FROM srp_erp_journeyplancharges GROUP BY journeyPlanMasterID
                    ) details ON details.journeyPlanMasterID = master.journeyPlanMasterID 
                    LEFT JOIN srp_erp_currencymaster currency ON currency.currencyID = master.transactionCurrrencyID
                    WHERE 
                        master.companyID = $companyID 
                        AND master.journeyPlanMasterID = $journeyPlanMasterID")->row_array();

        if (empty($tourPriceDetails['amount'])) {
            return array('w', 'There are no records to confirm this document!');
            exit;
        }
        $this->load->library('Approvals');
        $data['documentID'] = "CINV";
        $data['isSytemGenerated'] = 1;
        $data['invoiceDate'] = $invoiceDate;
        $data['invoiceDueDate'] = $invoiceDate;
        $data['invoiceCode'] = $this->sequence->sequence_generator($data['documentID']);
        $data['referenceNo'] = $tourPriceDetails['documentCode'];
        $data['invoiceNarration'] = 'Generated By Journey Plan - ' . $tourPriceDetails['documentCode'];
        $financeYearDetails=get_financial_year($invoiceDate);
        $financePeriodDetails=get_financial_period_date_wise($invoiceDate);
        $data['companyFinanceYearID'] = $financeYearDetails['companyFinanceYearID'];
        $data['companyFinanceYear'] = $financeYearDetails['beginingDate'] . ' - ' . $financeYearDetails['endingDate'];
        $data['FYBegin'] = $financeYearDetails['beginingDate'];
        $data['FYEnd'] = $financeYearDetails['endingDate'];
        $data['companyFinancePeriodID'] = $financePeriodDetails['companyFinancePeriodID'];
        $data['FYPeriodDateFrom'] = $financePeriodDetails['dateFrom'];
        $data['FYPeriodDateTo'] = $financePeriodDetails['dateTo'];
        $data['contactPersonNumber'] = $customerDetails['customerTelephone'];
        $data['customerID'] = $customerDetails['customerAutoID'];
        $data['customerSystemCode'] = $customerDetails['customerSystemCode'];
        $data['customerName'] = $customerDetails['customerName'];
        $data['customerAddress'] = $customerDetails['customerAddress1'] . ' ' . $customerDetails['customerAddress2'];
        $data['customerTelephone'] = $customerDetails['customerTelephone'];
        $data['customerFax'] = $customerDetails['customerFax'];
        $data['customerEmail'] = $customerDetails['customerEmail'];
        $data['customerReceivableAutoID'] = $customerDetails['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customerDetails['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customerDetails['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customerDetails['receivableDescription'];
        $data['customerReceivableType'] = $customerDetails['receivableType'];
        $data['transactionCurrencyID'] = $tourPriceDetails['transactionCurrrencyID'];
        $data['transactionCurrency'] = $tourPriceDetails['CurrencyCode'];
        $data['transactionExchangeRate'] = 1;
        $data['transactionAmount'] = $tourPriceDetails['amount'];
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $tourPriceDetails['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $data['companyReportingExchangeRate']);
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['customerCurrencyID'] = $customerDetails['customerCurrencyID'];
        $data['customerCurrency'] = $customerDetails['customerCurrency'];
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] =  $customer_currency['conversion'];
        $data['customerCurrencyAmount'] = ($data['transactionAmount'] / $data['customerCurrencyExchangeRate']);
        $data['customerCurrencyDecimalPlaces'] = $customerDetails['customerCurrencyDecimalPlaces'];

        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $this->db->insert('srp_erp_customerinvoicemaster', $data);
        $last_id = $this->db->insert_id();


        $tourDetails = $this->db->query("SELECT item.*, charges.transactionCurrencyID, charges.companyLocalAmount, charges.amount, charges.companyReportingAmount FROM srp_erp_journeyplancharges charges LEFT JOIN srp_erp_itemmaster item ON item.itemAutoID = charges.itemAutoID WHERE charges.companyID = $companyID AND journeyPlanMasterID = $journeyPlanMasterID")->result_array();
        $x = 0;
        foreach ($tourDetails as $details) {
            $invoiceDetails[$x]['invoiceAutoID'] = $last_id;
            $invoiceDetails[$x]['type'] = 'Item';
            $invoiceDetails[$x]['itemAutoID'] = $details['itemAutoID'];
            $invoiceDetails[$x]['itemSystemCode'] = $details['itemSystemCode'];
            $invoiceDetails[$x]['itemDescription'] = $details['itemDescription'];
            $invoiceDetails[$x]['itemCategory'] = $details['mainCategory'];
            $invoiceDetails[$x]['expenseGLAutoID'] = $details['costGLAutoID'];
            $invoiceDetails[$x]['expenseSystemGLCode'] = $details['costSystemGLCode'];
            $invoiceDetails[$x]['expenseGLCode'] = $details['costGLCode'];
            $invoiceDetails[$x]['expenseGLDescription'] = $details['costDescription'];
            $invoiceDetails[$x]['expenseGLType'] = $details['costType'];
            $invoiceDetails[$x]['revenueGLAutoID'] = $details['revanueGLAutoID'];
            $invoiceDetails[$x]['revenueGLCode'] = $details['revanueGLCode'];
            $invoiceDetails[$x]['revenueSystemGLCode'] = $details['revanueSystemGLCode'];
            $invoiceDetails[$x]['revenueGLDescription'] = $details['revanueDescription'];
            $invoiceDetails[$x]['revenueGLType'] = $details['revanueType'];
            $invoiceDetails[$x]['assetGLAutoID'] = $details['assteGLAutoID'];
            $invoiceDetails[$x]['assetGLCode'] = $details['assteGLCode'];
            $invoiceDetails[$x]['assetSystemGLCode'] = $details['assteSystemGLCode'];
            $invoiceDetails[$x]['assetGLDescription'] = $details['assteDescription'];
            $invoiceDetails[$x]['defaultUOMID'] = $details['defaultUnitOfMeasureID'];
            $invoiceDetails[$x]['defaultUOM'] = $details['defaultUnitOfMeasure'];
            $invoiceDetails[$x]['unitOfMeasureID'] = '';
            $invoiceDetails[$x]['unitOfMeasure'] = '';
            $invoiceDetails[$x]['conversionRateUOM'] = 1;
            $invoiceDetails[$x]['requestedQty'] = 1;
            $invoiceDetails[$x]['transactionAmount'] = $details['amount'];
            $invoiceDetails[$x]['companyLocalAmount'] = $details['companyLocalAmount'];
            $invoiceDetails[$x]['companyReportingAmount'] = $details['companyReportingAmount'];
            $customer_currency_dt = currency_conversionID($details['transactionCurrencyID'], $customerDetails['customerCurrencyID']);
            $invoiceDetails[$x]['customerAmount'] = ($details['amount'] / $customer_currency_dt['conversion']);

            $invoiceDetails[$x]['companyID'] = $this->common_data['company_data']['company_id'];
            $invoiceDetails[$x]['companyCode'] = $this->common_data['company_data']['company_code'];
            $invoiceDetails[$x]['createdUserGroup'] = $this->common_data['user_group'];
            $invoiceDetails[$x]['createdPCID'] = $this->common_data['current_pc'];
            $invoiceDetails[$x]['createdUserID'] = $this->common_data['current_userID'];
            $invoiceDetails[$x]['createdUserName'] = $this->common_data['current_user'];
            $invoiceDetails[$x]['createdDateTime'] = $this->common_data['current_date'];

            $x++;
        }
        $this->db->insert_batch('srp_erp_customerinvoicedetails', $invoiceDetails);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', $this->db->_error_message());
            exit;
        } else {
            $autoApproval= get_document_auto_approval('CINV');

            if($autoApproval==0){
                $approvals_status = $this->approvals->auto_approve($last_id, 'srp_erp_customerinvoicemaster','invoiceAutoID', 'CINV',$data['invoiceCode'],$data['invoiceDate']);
            }elseif($autoApproval==1){
                $approvals_status = $this->approvals->CreateApproval("CINV", $last_id, $data['invoiceCode'], 'Invoice', 'srp_erp_customerinvoicemaster', 'invoiceAutoID',0,$data['invoiceDate']);
            }else{
                return array('e', 'Approval levels are not set for this document');
                exit;
            }

            if ($approvals_status == 1) {







                $dataConfirm = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user'],
                );
                $this->db->where('invoiceAutoID', $last_id);
                $this->db->update('srp_erp_customerinvoicemaster', $dataConfirm);

            } elseif($approvals_status == 3){
                return array('w', 'There are no users exist to perform approval for this document.');
                exit;
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Invoice Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
                exit;
            } else {
                $this->load->model('Invoice_model');
                $result = $this->Invoice_model->save_invoice_approval(0, $last_id, 1, 'Auto Approved');
                if($result){
                    $this->db->trans_commit();
                    return array('s', 'Document confirmed successfully');
                } else {
                    return array('e', 'An Error Occurred while approving the document!');
                }
            }
        }

    }

    function add_new_tour_type()
    {
        $tourType_description = trim($this->input->post('tourType_description') ?? '');

        $companyID = current_companyID();
        $isExist = $this->db->query("SELECT tourTypeID FROM srp_erp_journeyplan_tourtype WHERE companyID = $companyID AND description='$tourType_description' ")->row('modelID');

        if (isset($isExist)) {
            return array('e', 'This Tour Type already Exists');
        } else {

            $data = array(
                'description' => $tourType_description,
                'companyID' => $companyID,
                'createdUserGroup' =>  $this->common_data['user_group'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdDateTime' => $this->common_data['current_date'],
                'createdUserName' => $this->common_data['current_user']
            );

            $this->db->insert('srp_erp_journeyplan_tourtype', $data);
            if ($this->db->affected_rows() > 0) {
                $tourid = $this->db->insert_id();
                return array('s', 'Tour Type is created successfully.', $tourid);
            } else {
                return array('e', 'Error in Model Creating');
            }
        }
    }

}