<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Journeyplan extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Journeyplan_model');
        $this->load->helper('journeyplan_helper');
        $this->load->library('s3');
    }

    function fetch_vehicale_details()
    {
        $this->form_validation->set_rules('vehicalemasterid', 'Vehical Number', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Journeyplan_model->load_vehicale_details());
        }

    }
    function fetch_driver_details()
    {
        $this->form_validation->set_rules('drivermasterid', 'Driver', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Journeyplan_model->load_driver_details());
        }

    }
    function fetch_employee_details()
    {
        $this->form_validation->set_rules('employeeid', 'Employee', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Journeyplan_model->fetch_employee_details());
        }
    }
    function fetch_jp_number()
    {
        echo json_encode($this->Journeyplan_model->fetch_jp_number());
    }
    function save_journeyplan_header()
    {
        $this->form_validation->set_rules('vehiclenumber', 'Vehicle Number', 'trim|required');
        $this->form_validation->set_rules('drivername', 'Driver Name', 'trim|required');
        $this->form_validation->set_rules('phonenumber', 'Driver Telephone Number', 'trim|required');
        $this->form_validation->set_rules('journeymanager', 'Journey Manager', 'trim|required');
        $this->form_validation->set_rules('jmphonenumber', 'Journey Manager Office Phone Number', 'trim|required');
        $this->form_validation->set_rules('jmphonenumbermob', 'Journey Manager Mobile Number', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Journeyplan_model->save_journeyplan_header());
        }
    }

    function save_journeyplan_header_map_tour()
    {
        $this->form_validation->set_rules('tourType', 'Type of tour', 'trim|required');
        $this->form_validation->set_rules('vehiclenumber', 'Vehicle Number', 'trim|required');
        $this->form_validation->set_rules('noofpassengers', 'No of Passengers', 'trim|required');
        $this->form_validation->set_rules('drivername', 'Driver Name', 'trim|required');
        $this->form_validation->set_rules('phonenumber', 'Driver Telephone Number', 'trim|required');
        $this->form_validation->set_rules('guideName', 'Guide Name', 'trim|required');
        $this->form_validation->set_rules('guideNumber', 'Guide Phone Number', 'trim|required');
        $this->form_validation->set_rules('journeymanager', 'Journey Manager', 'trim|required');
        $this->form_validation->set_rules('jmphonenumber', 'Journey Manager Office Phone Number', 'trim|required');
        $this->form_validation->set_rules('jmphonenumbermob', 'Journey Manager Mobile Number', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Journeyplan_model->save_journeyplan_header_map_tour());
        }
    }

    function load_journeyplan_masterview()
    {
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $text = trim($this->input->post('q') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $jpstatus = trim($this->input->post('jpstatus') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND ((jpmaster.documentCode Like '%" . $text . "%') OR (vehicalemaster.vehDescription Like '%" . $text . "%')  OR (vehicalemaster.vehicleCode Like '%" . $text . "%') OR (arrive.placeName Like '%" . $text . "%') OR (depart.placeName Like '%" . $text . "%') OR (driver.driverName Like '%" . $text . "%') )";
        }


        $filter_status = '';
        if (isset($status) && !empty($status)) {
            if ($status == 1) {
                $filter_status = " AND jpmaster.confirmedYN = 0 AND jpmaster.approvedYN = 0";
            } else if ($status == 2) {
                $filter_status = " AND jpmaster.confirmedYN = 1 AND jpmaster.approvedYN = 0";
            } elseif ($status == 3) {
                $filter_status = " AND jpmaster.confirmedYN = 1 AND jpmaster.approvedYN = 1";
            }

        }
        $filter_status_jp = '';
        if (isset($jpstatus) && !empty($jpstatus)) {
            if ($jpstatus == 1) {
                $filter_status_jp = " AND jpmaster.status = 0";
            } else if ($jpstatus == 2) {
                $filter_status_jp = " AND jpmaster.status = 2";
            } elseif ($jpstatus == 3) {
                $filter_status_jp = " AND jpmaster.status = 3";
            }elseif ($jpstatus == 4) {
                $filter_status_jp = " AND jpmaster.status = 4";
            }elseif ($jpstatus == 5) {
                $filter_status_jp = " AND jpmaster.status = 5";
            }


        }

        $where_admin = "WHERE jpmaster.companyID = " . $companyid . $search_string . $filter_status .$filter_status_jp;

        $data['master'] = $this->db->query("SELECT jpmaster.*,DATE_FORMAT(	depart.dateDepart ,'$convertFormat') AS datedep,DATE_FORMAT(arrive.dateArived ,'$convertFormat') AS arriveda,driver.driverName,driver.driverCode,vehicalemaster.vehDescription,vehicalemaster.vehicleCode,vehicalemaster.ivmsNo,rout.JP_RouteDetailsID as JP_RouteDetailsIDmax ,routmin.JP_RouteDetailsID as JP_RouteDetailsIDmin,arrive.placeName as arriveplace,depart.placeName as departplace,depart.timeDepart as departime,arrive.timeArrive as arrivetime FROM srp_erp_journeyplan_master jpmaster LEFT JOIN fleet_drivermaster driver ON driver.driverMasID = jpmaster.driverID LEFT JOIN fleet_vehiclemaster vehicalemaster ON vehicalemaster.vehicleMasterID = jpmaster.vehicleID LEFT JOIN ( SELECT MAX(JP_RouteDetailsID) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) rout ON rout.journeyPlanMasterID = jpmaster.journeyPlanMasterID LEFT JOIN ( SELECT MIN(JP_RouteDetailsID) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) routmin ON routmin.journeyPlanMasterID = jpmaster.journeyPlanMasterID LEFT JOIN srp_erp_journeyplan_routedetails arrive on arrive.JP_RouteDetailsID = rout.JP_RouteDetailsID LEFT JOIN srp_erp_journeyplan_routedetails depart on depart.JP_RouteDetailsID = routmin.JP_RouteDetailsID $where_admin ORDER BY journeyPlanMasterID DESC")->result_array();
        $this->load->view('system/journeyplan/ajax/journey_planmasterview',$data);
    }
    function load_journeyplan_masterview_maps_tour()
    {
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $text = trim($this->input->post('q') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $jpstatus = trim($this->input->post('jpstatus') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND ((jpmaster.documentCode Like '%" . $text . "%') OR (vehicalemaster.vehDescription Like '%" . $text . "%')  OR (vehicalemaster.vehicleCode Like '%" . $text . "%') OR (arrive.placeName Like '%" . $text . "%') OR (depart.placeName Like '%" . $text . "%') OR (driver.driverName Like '%" . $text . "%') )";
        }


        $filter_status = '';
        if (isset($status) && !empty($status)) {
            if ($status == 1) {
                $filter_status = " AND jpmaster.confirmedYN = 0 AND jpmaster.approvedYN = 0";
            } else if ($status == 2) {
                $filter_status = " AND jpmaster.confirmedYN = 1 AND jpmaster.approvedYN = 0";
            } elseif ($status == 3) {
                $filter_status = " AND jpmaster.confirmedYN = 1 AND jpmaster.approvedYN = 1";
            }

        }
        $filter_status_jp = '';
        if (isset($jpstatus) && !empty($jpstatus)) {
            if ($jpstatus == 1) {
                $filter_status_jp = " AND jpmaster.status = 0";
            } else if ($jpstatus == 2) {
                $filter_status_jp = " AND jpmaster.status = 2";
            } elseif ($jpstatus == 3) {
                $filter_status_jp = " AND jpmaster.status = 3";
            }elseif ($jpstatus == 4) {
                $filter_status_jp = " AND jpmaster.status = 4";
            }elseif ($jpstatus == 5) {
                $filter_status_jp = " AND jpmaster.status = 5";
            }


        }

        $where_admin = "WHERE jpmaster.companyID = " . $companyid . $search_string . $filter_status .$filter_status_jp;

        $data['master'] = $this->db->query("SELECT jpmaster.*,DATE_FORMAT(	depart.dateDepart ,'$convertFormat') AS datedep,DATE_FORMAT(arrive.dateArived ,'$convertFormat') AS arriveda,driver.driverName,driver.driverCode,vehicalemaster.vehDescription,vehicalemaster.vehicleCode,vehicalemaster.ivmsNo,rout.JP_RouteDetailsID as JP_RouteDetailsIDmax ,routmin.JP_RouteDetailsID as JP_RouteDetailsIDmin,arrive.placeName as arriveplace,depart.placeName as departplace,depart.timeDepart as departime,arrive.timeArrive as arrivetime FROM srp_erp_journeyplan_master jpmaster LEFT JOIN fleet_drivermaster driver ON driver.driverMasID = jpmaster.driverID LEFT JOIN fleet_vehiclemaster vehicalemaster ON vehicalemaster.vehicleMasterID = jpmaster.vehicleID LEFT JOIN ( SELECT MAX(JP_RouteDetailsID) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) rout ON rout.journeyPlanMasterID = jpmaster.journeyPlanMasterID LEFT JOIN ( SELECT MIN(JP_RouteDetailsID) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) routmin ON routmin.journeyPlanMasterID = jpmaster.journeyPlanMasterID LEFT JOIN srp_erp_journeyplan_routedetails arrive on arrive.JP_RouteDetailsID = rout.JP_RouteDetailsID LEFT JOIN srp_erp_journeyplan_routedetails depart on depart.JP_RouteDetailsID = routmin.JP_RouteDetailsID $where_admin ORDER BY journeyPlanMasterID DESC")->result_array();
        $this->load->view('system/journeyplan/ajax/journey_planmasterview_maps_tour',$data);
    }
    function load_jp_header()
    {
        echo json_encode($this->Journeyplan_model->fetch_jp_header_details());
    }
    function load_detail_view()
    {
        $data['jpnumber'] = $this->input->post('jpnumber');
        $this->load->view('system/journeyplan/ajax/passenger_jp',$data);

    }
    function save_jp_details()
    {
        $restyn = $this->input->post('restyn');
        foreach ($restyn as $key => $val) {
            $this->form_validation->set_rules("placenames[{$key}]", 'Place Names', 'trim|required');
            $this->form_validation->set_rules("restyn[{$key}]", 'Rest', 'trim|required');
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
            echo json_encode($this->Journeyplan_model->save_jp_details());
        }

    }
    function fetch_item_jv_table()
    {
        echo json_encode($this->Journeyplan_model->fetch_item_jv_table());
    }
    function fetch_passanger_details()
    {
        $data['jpnumber'] = $this->input->post('jpnumber');
        $this->load->view('system/journeyplan/ajax/passenger_jp_detail',$data);
    }
    function save_jp_passanger_details()
    {
        $passenger = $this->input->post('passangername');
        foreach ($passenger as $key => $val) {
            $this->form_validation->set_rules("passangername[{$key}]", 'Passanger Name', 'trim|required');
            $this->form_validation->set_rules("contactno[{$key}]", 'Contact Number', 'trim|required');
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
            echo json_encode($this->Journeyplan_model->save_jp_passanger_details());
        }

    }
    function fetch_passenger_detail_tbl()
    {
        echo json_encode($this->Journeyplan_model->fetch_passenger_detail_tbl());
    }
    function jp_confirmation()
    {
        echo json_encode($this->Journeyplan_model->jp_confirmation());
    }
    function load_jp_view()
    {
        $jpmasterid = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('journeyPlanMasterID') ?? '');
        $data['extra'] = $this->Journeyplan_model->fetch_jp_details($jpmasterid);
        $data['approval'] = $this->input->post('approval');

            $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $data['passengersimg'] = $this->s3->createPresignedRequest('images/crm/icon-list-contact.png', '1 hour');
        $html = $this->load->view('system/journeyplan/jp_print_view',$data,true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdfnew = $this->load->view('system/journeyplan/jp_print',$data,true);
           $pdf = $this->pdf->printed($pdfnew, 'A4');
        }
    }
    function jp_approval()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $this->datatables->select('masterTbl.journeyPlanMasterID AS journeyPlanMasterID,masterTbl.documentCode as documentCode,driver.driverName as drivername,depart.placeName as placedepart,arrive.placeName as arrivedepart,approvalLevelID,documentApprovedID,masterTbl.approvedYN as approvedYNmster', false);
        $this->datatables->join('(SELECT MAX( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID) rout', 'rout.journeyPlanMasterID = masterTbl.journeyPlanMasterID', 'left');
        $this->datatables->join('(SELECT MIN( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) routmin', 'routmin.journeyPlanMasterID = masterTbl.journeyPlanMasterID', 'left');
        $this->datatables->from('srp_erp_journeyplan_master masterTbl');
        $this->datatables->join('fleet_drivermaster driver', 'driver.driverMasID = masterTbl.driverID', 'left');
        $this->datatables->join('fleet_vehiclemaster vehicalemaster', 'vehicalemaster.vehicleMasterID = masterTbl.vehicleID', 'left');
        $this->datatables->join('srp_erp_journeyplan_routedetails arrive', 'arrive.JP_RouteDetailsID = rout.JP_RouteDetailsID', 'left');
        $this->datatables->join('srp_erp_journeyplan_routedetails depart', 'depart.JP_RouteDetailsID = routmin.JP_RouteDetailsID', 'left');


        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = masterTbl.journeyPlanMasterID AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'JP');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'JP');
        $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
        $this->datatables->add_column('confirmed', "<div style='text-align: center'>Level $1</div>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYNmster,"JP",journeyPlanMasterID)');
        $this->datatables->add_column('edit', '$1', 'load_jp_action(journeyPlanMasterID,approvalLevelID,approvedYNmster,documentApprovedID,JP)');
        echo $this->datatables->generate();
    }

    function jp_approval_map_tour()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $this->datatables->select('masterTbl.journeyPlanMasterID AS journeyPlanMasterID,masterTbl.documentCode as documentCode,driver.driverName as drivername,depart.placeName as placedepart,arrive.placeName as arrivedepart,approvalLevelID,documentApprovedID,masterTbl.approvedYN as approvedYNmster', false);
        $this->datatables->join('(SELECT MAX( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID) rout', 'rout.journeyPlanMasterID = masterTbl.journeyPlanMasterID', 'left');
        $this->datatables->join('(SELECT MIN( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) routmin', 'routmin.journeyPlanMasterID = masterTbl.journeyPlanMasterID', 'left');
        $this->datatables->from('srp_erp_journeyplan_master masterTbl');
        $this->datatables->join('fleet_drivermaster driver', 'driver.driverMasID = masterTbl.driverID', 'left');
        $this->datatables->join('fleet_vehiclemaster vehicalemaster', 'vehicalemaster.vehicleMasterID = masterTbl.vehicleID', 'left');
        $this->datatables->join('srp_erp_journeyplan_routedetails arrive', 'arrive.JP_RouteDetailsID = rout.JP_RouteDetailsID', 'left');
        $this->datatables->join('srp_erp_journeyplan_routedetails depart', 'depart.JP_RouteDetailsID = routmin.JP_RouteDetailsID', 'left');


        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = masterTbl.journeyPlanMasterID AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'JP');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'JP');
        $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
        $this->datatables->add_column('confirmed', "<div style='text-align: center'>Level $1</div>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYNmster,"JP",journeyPlanMasterID)');
        $this->datatables->add_column('edit', '$1', 'load_jp_map_tour_action(journeyPlanMasterID,approvalLevelID,approvedYNmster,documentApprovedID,JP)');
        echo $this->datatables->generate();
    }

    function fetch_passanger_details_approval()
    {
        $data['jpnumber'] = $this->input->post('jpnumber');
        $this->load->view('system/journeyplan/ajax/passenger_jp_detail_approval',$data);
    }
    function load_detail_view_approval()
    {
        $data['jpnumber'] = $this->input->post('jpnumber');
        $this->load->view('system/journeyplan/ajax/passenger_jp_approval',$data);

    }
    function save_jpapproval()
    {
        $system_code = trim($this->input->post('jurneyplanid') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'JP', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('journeyPlanMasterID');
                $this->db->where('journeyPlanMasterID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_journeyplan_master');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('jurneyplanid', 'Joureny Plan ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Journeyplan_model->save_jp_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('journeyPlanMasterID');
            $this->db->where('journeyPlanMasterID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_journeyplan_master');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'JP', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    $this->form_validation->set_rules('comments', 'Comment', 'trim|required');

                    $this->form_validation->set_rules('jurneyplanid', 'Journey Plan', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Journeyplan_model->save_jp_approval());
                    }
                }
            }
        }
    }
    function save_jp_status()
    {
        $status = $this->input->post('status');
        $this->form_validation->set_rules('masterid', 'Jorney Plan ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        if($status == 3)
        {
            $this->form_validation->set_rules('comment', 'Comment', 'trim|required');
        }
        if ($status == 4)
        {
            $this->form_validation->set_rules('comment', 'Comment', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Journeyplan_model->save_jp_status());
        }
    }
    function jp_referback()
    {
        $jpautoid = $this->input->post('jurneyplanid');

        $this->db->select('approvedYN,documentID');
        $this->db->where('journeyPlanMasterID', trim($jpautoid));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_journeyplan_master');
        $approved_iou_voucher = $this->db->get()->row_array();
        if (!empty($approved_iou_voucher)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_iou_voucher['iouCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($jpautoid, 'JP');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }
    }

    function load_jp_tour_price()
    {
        $data['view'] = !empty($this->input->post('view')?1:0);
        $data['jpnumber'] = $this->input->post('jpnumber');
        $this->load->view('system/journeyplan/ajax/tour_price',$data);
    }

    function checkRevenueGL_tour_price()
    {
        echo json_encode($this->Journeyplan_model->checkRevenueGL_tour_price());
    }

    function save_tour_price_details()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        foreach ($itemAutoID as $key => $val) {
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("remarks[{$key}]", 'Remark', 'trim|required');
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
            echo json_encode($this->Journeyplan_model->save_tour_price_details());
        }
    }

    function fetch_tour_price_details()
    {
        echo json_encode($this->Journeyplan_model->fetch_tour_price_details());
    }

    function fetch_tour_price_items()
    {
        echo json_encode($this->Journeyplan_model->fetch_tour_price_items());
    }
    function fetch_additionalCharges_items()
    {
        echo json_encode($this->Journeyplan_model->fetch_additionalCharges_items());
    }

    function load_jp_additional_charges()
    {
        $data['view'] = !empty($this->input->post('view')?1:0);
        $data['jpnumber'] = $this->input->post('jpnumber');
        $this->load->view('system/journeyplan/ajax/additional_charges',$data);
    }

    function jp_create_customer_invoice()
    {
        $this->form_validation->set_rules('journeyPlanMasterID', 'journeyPlanMasterID', 'trim|required');
        $this->form_validation->set_rules('customerInvoiceDate', 'Customer Invoice Date', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $journeyPlanMasterID = $this->input->post('journeyPlanMasterID');
            $invoiceCreated = $this->db->query("SELECT invoiceAutoID, invoiceCode FROM srp_erp_customerinvoicemaster INNER JOIN srp_erp_journeyplan_master ON srp_erp_journeyplan_master.documentCode = srp_erp_customerinvoicemaster.referenceNo WHERE journeyPlanMasterID = $journeyPlanMasterID")->row_array();
            if($invoiceCreated){
                echo json_encode(array('e', 'Invoice already created (' . $invoiceCreated['invoiceCode'] . ')'));
            } else {
                echo json_encode($this->Journeyplan_model->jp_create_customer_invoice());
            }
        }
    }

    function add_new_tour_type()
    {
        $this->form_validation->set_rules('tourType_description', 'Tour Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Journeyplan_model->add_new_tour_type());
        }
    }
    function load_jp_view_tour()
    {
        $jpmasterid = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('journeyPlanMasterID') ?? '');
        $data['extra'] = $this->Journeyplan_model->fetch_jp_details($jpmasterid);
        $data['approval'] = $this->input->post('approval');

        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $data['passengersimg'] = $this->s3->createPresignedRequest('images/crm/icon-list-contact.png', '1 hour');
        $html = $this->load->view('system/journeyplan/jp_print_view_map_tour',$data,true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdfnew = $this->load->view('system/journeyplan/jp_print_tour',$data,true);
            $pdf = $this->pdf->printed($pdfnew, 'A4');
        }
    }
}
