<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    Private $main;
    private $db_name;
    private $db_username;
    private $db_password;
    private $db_host;

    public $date_time;

    private $sys_types = [];

    function __construct()
    {
        parent::__construct();
        $CI =& get_instance();
        if (!$CI->session->has_userdata('sme_company_status')) {
            header('Location: ' . site_url('login/logout'));
        }

        $this->date_time = date('Y-m-d H:i:s');
        $this->load->model('Dashboard_model');
        $this->load->helper('configuration');
        $this->load->library('s3');

        if($this->uri->segment(2) == 'AddCompany'){
            $this->sys_types = company_type('Select a type');
        }        

        $this->encryption->initialize(array('driver' => 'mcrypt'));
        $companyID = $this->input->post('company_id');
        if(isset($companyID)) {
            $this->main = $this->load->database('db2', TRUE);
            $this->main->select("*");
            $this->main->from("srp_erp_company");
            $this->main->where("company_id", $companyID);
            $r = $this->main->get()->row_array();
            if (!empty($r)) {
                $this->db_host = $r['host'];
                $this->db_name = $r['db_name'];
                $this->db_password = $r['db_password'];
                $this->db_username = $r['db_username'];
            }
        }
    }

    function get_db_array($isDecrypted=false)
    {
        $this->db_host = trim($this->db_host);
        $this->db_username = trim($this->db_username);
        $this->db_password = trim($this->db_password);
        $this->db_name = trim($this->db_name);


        $config['hostname'] = ($isDecrypted)? $this->db_host : decryptData($this->db_host);
        $config['username'] = ($isDecrypted)? $this->db_username : decryptData($this->db_username);
        $config['password'] = ($isDecrypted)? $this->db_password : decryptData($this->db_password);
        $config['database'] = ($isDecrypted)? $this->db_name : decryptData($this->db_name);
        $config['dbdriver'] = 'mysqli';    
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        return $config;
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['main_content'] = 'dashboard_page';
        $data['extra'] = NULL;
        $this->load->view('include/template', $data);
    }

    /**Created by Shafri */
    public function showAll_adminCompanies()
    {
        $companiesList = $this->Dashboard_model->loadAllCompanies();
        $tmpData['companiesList'] = $companiesList;
        $data['title'] = 'Company Admin';
        $data['main_content'] = 'company_admin_view';
        $data['extra'] = $tmpData;
        $this->load->view('include/template', $data);

    }

    public function add_company($id = NULL)
    {
        $data['title'] = 'Company';
        $data['main_content'] = 'company_page';
        $data['sys_types'] = $this->sys_types;        
        $data['extra'] = NULL;
        $this->Dashboard_model->set_company_credential($id);
        $this->load->view('include/template', $data);
    }

    public function show_company_template($id)
    {
        $data['title'] = 'Template Setup';
        $data['main_content'] = 'template_configuration_management';
        $data['extra'] = NULL;
        $data['companyID'] = $id;
        $this->load->view('include/template', $data);
    }

    function fetch_warehouse()
    {
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);
        $this->datatables->select('wareHouseAutoID,companyCode,wareHouseCode,wareHouseDescription,wareHouseLocation')
            ->where('companyID', trim($this->input->post('company_id')))
            ->from('srp_erp_warehousemaster')
            ->edit_column('action', '<span class="pull-right" onclick="openwarehousemastermodel($1)"><a href="#" ><span class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a></span>', 'wareHouseAutoID');
        echo $this->datatables->generate();
    }

    function fetch_company()
    {
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);
        $this->datatables->select('company_id,company_code,company_name,company_start_date,company_url,company_email,company_phone,company_address1,company_address2,company_city,company_province,company_postalcode,company_country,company_logo,legalName')
            ->from('srp_erp_company');
        $this->datatables->add_column('company_detail', '<h4> $1 ( $2 ) <small>$3</small></h4>', 'company_name,company_code,legalName');
        $this->datatables->add_column('img', "<center><img class='img-thumbnail' src='$2/$1' style='width:90px;height: 80px;'><center>", 'company_logo,server_path("images/logo/")');
        $this->datatables->add_column('edit', '<spsn class="pull-right"><a href="' . base_url() . 'index.php/Dashboard/add_company/$1"><span class="glyphicon glyphicon-pencil"></span></a>', 'company_id');
        echo $this->datatables->generate();
    }

    function load_segment()
    {
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);
        $this->datatables->select("segmentID,segmentCode,description,status");
        $this->datatables->where('companyID', trim($this->input->post('company_id')));
        $this->datatables->from('srp_erp_segment');
        echo $this->datatables->generate('json', 'ISO-8859-1');
    }

    function load_financial_year()
    {
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);

        $str = '<span class="pull-right"><a onclick="open_period_modal($1)"><span class="glyphicon glyphicon-pencil"></span></a></span>';

        $this->datatables->select("companyFinanceYearID,beginingDate,endingDate,comments,isActive,isCurrent,isClosed")
            ->where('companyID', trim($this->input->post('company_id')))
            ->from('srp_erp_companyfinanceyear')
            ->add_column('financial_year', '<div style="text-align: center"> $1- $2 </div>', 'beginingDate,endingDate')
            ->add_column('active_status', '$1', 'load_financial_year_status(companyFinanceYearID,isActive)')
            ->add_column('current_status', '$1', 'load_financial_year_current(companyFinanceYearID,isCurrent)')
            ->add_column('closed_status', '$1', 'load_financial_year_close(companyFinanceYearID,isClosed)')    
            ->add_column('action', $str, 'companyFinanceYearID');
        echo $this->datatables->generate('json', 'ISO-8859-1');
    }

    function load_finance_period()
    {
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);

        $this->datatables->select("companyFinancePeriodID,companyFinanceYearID,dateFrom,dateTo,isActive,isCurrent,isClosed");
        $this->datatables->where('companyFinanceYearID', $this->input->post('companyFinanceYearID'));        
        $this->datatables->from('srp_erp_companyfinanceperiod');
        $this->datatables->add_column('status', '$1', 'load_financial_year_isactive_status(companyFinancePeriodID,isActive)');
        $this->datatables->add_column('current', '$1', 'load_financial_year_isactive_current(companyFinancePeriodID,isCurrent,companyFinanceYearID)');
        $this->datatables->add_column('closed', '$1', 'load_financialperiod_isclosed_closed(companyFinancePeriodID,isClosed)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_is_closed_is_current_class(isClosed,isCurrent)');
        echo $this->datatables->generate();
    }

    function save_warehousemaster()
    {
        if (!$this->input->post('warehouseredit')) {
            $this->form_validation->set_rules('warehousecode', 'Warehouse Code', 'trim|required|max_length[5]');
        }
        $this->form_validation->set_rules('warehousedescription', 'Warehouse Description', 'trim|required');
        $this->form_validation->set_rules('warehouselocation', 'Warehouse Location', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Dashboard_model->save_warehousemaster());
        }
    }

    function save_company()
    {
        if ($this->input->post('companyid')) {
            $this->form_validation->set_rules('companycode', 'Company Code', 'trim|required');
        }
        else {
            $this->form_validation->set_rules('companycode', 'Company Code', 'trim|required|is_unique[srp_erp_company.company_code]');
        }


        $this->form_validation->set_rules('companyname', 'Company Name', 'trim|required');
        $this->form_validation->set_rules('companystartdate', 'Company Start Date', 'trim|required');
        //$this->form_validation->set_rules('companyurl', 'Company URL', 'trim|required');
        //$this->form_validation->set_rules('companyemail', 'Company Email', 'trim|required');
        //$this->form_validation->set_rules('companyphone', 'Company Phone', 'trim|required');
        $this->form_validation->set_rules('companyaddress1', 'Company Address 1', 'trim|required');
        $this->form_validation->set_rules('companyaddress2', 'Company Address 2', 'trim|required');
        $this->form_validation->set_rules('companycity', 'Company City', 'trim|required');
        //$this->form_validation->set_rules('companypostalcode', 'Company Postal Code', 'trim|required');
        //$this->form_validation->set_rules('companyprovince', 'Company Province', 'trim|required');
        // //$this->form_validation->set_rules('companycountry', 'Company Country', 'trim|required');
        $this->form_validation->set_rules('company_default_currencyID', 'Default Currency', 'trim|required');
        $this->form_validation->set_rules('company_reporting_currencyID', 'Reporting Currency', 'trim|required');
        $this->form_validation->set_rules('timezone', 'Timezone', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Dashboard_model->save_company_master());
        }
    }

    function save_subscription()
    {
        $this->form_validation->set_rules('company_id', 'company id', 'trim|required');
        $this->form_validation->set_rules('registeredDate', 'Registered date', 'trim|required');
        $this->form_validation->set_rules('currencyID', 'Currency ID', 'trim|required');
        $this->form_validation->set_rules('subscriptionStartDate', 'Subscription start date', 'trim|required');
        $this->form_validation->set_rules('subscriptionAmount', 'Subscription amount', 'trim|required');
        $this->form_validation->set_rules('paymentEnabled', 'Payment Enabled', 'trim|required');
        /*$this->form_validation->set_rules('nextRenewalDate', 'Next renewal date', 'trim|required');
        $this->form_validation->set_rules('lastRenewedDate', 'Last renewed date', 'trim|required');
        $this->form_validation->set_rules('implementationAmount', 'Implementation amount', 'trim|required');*/

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Dashboard_model->save_company_subscription());
        }
    }

    function save_company_host()
    {
        if(current_userType() == 1) {
            $this->form_validation->set_rules('pbs_contract', 'Contract', 'trim|required');
        }        
        $this->form_validation->set_rules('db_name', 'Database Name', 'trim|required'); 

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Dashboard_model->save_company_host());
        }
    }

    function save_financial_year()
    {
        $this->form_validation->set_rules('beginningdate', 'Beginning Date', 'trim|required');
        $this->form_validation->set_rules('endingdate', 'Ending Date', 'trim|required');
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Dashboard_model->save_financial_year());
        }
    }

    function fetch_notifications()
    {
        $data = array();
        if ($this->session->flashdata('s'))
            $data[] = array('t' => 'success', 'm' => $this->session->flashdata('s'), 'h' => 'Success Message');

        if ($this->session->flashdata('e'))
            $data[] = array('t' => 'error', 'm' => $this->session->flashdata('e'), 'h' => 'Error Message');

        if ($this->session->flashdata('w'))
            $data[] = array('t' => 'warning', 'm' => $this->session->flashdata('w'), 'h' => 'Warning Message');

        if ($this->session->flashdata('i'))
            $data[] = array('t' => 'info', 'm' => $this->session->flashdata('i'), 'h' => 'Information Message');

        echo json_encode($data);
    }

    function save_nav_menu()
    {
        echo json_encode($this->Dashboard_model->save_nav_menu());
    }

    function make_admin()
    {
        echo json_encode($this->Dashboard_model->make_admin());
    }

    function fetch_admin_users()
    {
        echo json_encode($this->Dashboard_model->fetch_admin_users());
    }

    function fetch_users()
    {        
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);
        $user_status = trim($this->input->post('user_status'));
        $login_status = trim($this->input->post('login_status'));
        $company_id = trim($this->input->post('company_id'));

        $hr_setup = $this->config->item('is_hr_setup');
        $empColumnName = ($hr_setup)? 'employeeSystemID': 'empID';
        
        $this->datatables->select("Gender,ECode,UserName,Ename2,EIdNo,description as userGroupID, EDOJ AS date_join, 
            IF(Gender=1, 'Male', 'Female') AS gender_str, IF(isDischarged=1, 'Discharge', 'Active') AS discharge_str,
            IF(isDischarged=1, 'label-danger', 'label-success') AS discharge_class, userType, last_login, 
             NoOfLoginAttempt, empTb.isActive AS isActive,isDischarged")
            ->from('srp_employeesdetails AS empTb')
            ->join('srp_erp_employeenavigation', "srp_erp_employeenavigation.{$empColumnName} = empTb.EIdNo", 'left')
            ->join('srp_erp_usergroups', 'srp_erp_usergroups.userGroupID = srp_erp_employeenavigation.userGroupID', 'left')            
            ->where('Erp_companyID', $company_id)
            ->group_by("EIdNo")
            ->edit_column('discharge_str', '<div align="center"><span class="label $1">$2</span></div>', 'discharge_class,discharge_str')
            ->add_column('login_act', '$1', 'login_action(EIdNo,Ename2,UserName,NoOfLoginAttempt,isActive)')
            ->add_column('last_login_str', '<div style="width: 120px">$1</div>', 'last_login')
            ->add_column('user_type_str', '<div style="width: 120px">$1</div>', "user_type_table(EIdNo,userType)")
            ->add_column('action', '$1', 'users_action(EIdNo,userGroupID,Ename2,UserName,isDischarged)');

        if($user_status != ''){
            $this->datatables->where('isDischarged', $user_status);
        }

        if($login_status == 1){
            $this->datatables->where('NoOfLoginAttempt < 4');
        }

        if($login_status == 2){
            $this->datatables->where('NoOfLoginAttempt = 4');
        }

        echo $this->datatables->generate('json', 'ISO-8859-1');
    }

    function fetch_assigned_modulus()
    {
        echo json_encode($this->Dashboard_model->fetch_assigned_modulus());
    }

    function remove_modul()
    {
        echo json_encode($this->Dashboard_model->remove_modul());
    }

    function fetch_user_group()
    {
        echo json_encode($this->Dashboard_model->fetch_user_group());
    }

    function fetch_assigned_currency()
    {
        echo json_encode($this->Dashboard_model->fetch_assigned_currency());
    }

    function load_company_header()
    {
        echo json_encode($this->Dashboard_model->load_company_header());
    }

    function load_company_host_detail()
    {
        echo json_encode($this->Dashboard_model->load_company_host_detail());
    }

    function load_company_subscription_detail()
    {
        echo json_encode($this->Dashboard_model->load_company_subscription_detail());
    }

    function save_user()
    {
        $this->form_validation->set_rules('Ename1', 'First Name', 'trim|required');
        $this->form_validation->set_rules('Ename2', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('Gender', 'Gender', 'trim|required');
        $this->form_validation->set_rules('EDOJ', 'EDOJ', 'trim|required');
        $this->form_validation->set_rules('EEmail', 'UserName', 'trim|required|is_unique[user.UserName]');
        $this->form_validation->set_rules('Password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Dashboard_model->save_user());
        }
    }

    function is_unique($username)
    {
        // $username recieves the parameter automatically
        //Go on with your query as bellow
        //$db2 = $this->load->database('db2');
        $result = $this->db->query("SELECT * FROM user WHERE UserName = '".$username."'")->row_array();
        if($result){
            return false;
        }else{
            return true;
        }
    }

    function save_segment()
    {
        if (!$this->input->post('segmentID')) {
            $this->form_validation->set_rules('segmentcode', 'Segment Code', 'trim|required|max_length[3]');
        }
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Dashboard_model->save_segment());
        }
    }

    function set_conversion()
    {
        $data['mastercurrencyassignAutoID'] = $this->input->post('mastercurrencyassignAutoID');
        $data['currency_arr'] = $this->Dashboard_model->dropdown_currencyAssignedExchangeDropdown();
        $data['details'] = $this->Dashboard_model->detail_assignedcurrency_company();
        $html = $this->load->view('set_exchang_rates', $data, true);
        echo $html;
    }

    function update_currencyexchange()
    {
        $this->form_validation->set_rules('currencyConversionAutoID', 'Conversion', 'trim|required');
        $this->form_validation->set_rules('mastercurrencyassignAutoID', 'Conversion', 'trim|required');
        $this->form_validation->set_rules('subcurrencyassignAutoID', 'Conversion', 'trim|required');
        $this->form_validation->set_rules('conversion', 'Conversion', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Dashboard_model->update_currencyexchange());
        }
    }

    function save_addNewcurrencyExchange()
    {
        $this->form_validation->set_rules('currency', 'Currency', 'trim|required');
        $this->form_validation->set_rules('conversion', 'Conversion', 'trim|required|validate_numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Dashboard_model->save_addNewcurrencyExchange());
        }

    }

    function load_company_conformation()
    {
        $company_id = trim($this->input->post('companyid'));
        $data['extra'] = $this->Dashboard_model->fetch_template_data($company_id);
        $html = $this->load->view('erp_company_print', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', 1);
        }
    }

    function company_confirmation()
    {
        echo json_encode($this->Dashboard_model->company_confirmation());
    }

    function fetch_company_companyAdmin()
    {
        $str = '<div style="text-align: center; width: 100%">';
        $str .= '<button type="button" onclick="loadCompanyAdminUsers($1)" class="btn btn-xs btn-danger">Request Admin Pin</button>';
        $str .= '<a class="btn btn-xs btn-info datatTblBtn" href="'.base_url().'index.php/companyAdmin/AddCompany/$1"><i class="fa fa-pencil"></i></a>';
        $str .= '<a class="btn btn-xs btn-info datatTblBtn" href="'.base_url().'index.php/Dashboard/show_company_template/$1">';
        $str .= '<i class="fa fa-align-justify"></i></a>';
        $str .= '<a class="btn btn-xs btn-info datatTblBtn" href="'.base_url().'index.php/flush-data/$1"><i class="fa fa-pencil-square-o"></i></a>';
        $str .= '&nbsp;<button type="button" onclick="open_tokenChange_modal($1,\'$2\',\'$3\',\'$4\')" class="btn btn-xs btn-primary"><span title="Update Token" rel="tooltip" class="fa fa-refresh "></span></button>';
        $str .= '</div>';

        $this->datatables->set_database('db2');

        $sys_company_type = $this->input->post('sys_company_type');

        $this->datatables->select('company_id, company_code, company_name, company_logo, legalName, sub_status, 
            cType.description AS tyDes, company_business_name,supportToken', true)
            ->from('company_subscription_view AS sub_view')
            ->join('system_company_type AS cType', 'cType.id=sub_view.isPartnerCompany', 'left')
            ->where('adminType', current_userType());
            
        if($sys_company_type > 0){
            $this->datatables->where('isPartnerCompany', $sys_company_type);
        }
        $this->datatables->add_column('company_detail', '<h4> $1 ( $2 ) <small>$3</small></h4>', 'company_name,company_code,legalName');
        $this->datatables->add_column('isDisabled_str', '$1', 'company_status_str(company_id,sub_status,\'company_table\')');
        $this->datatables->add_column('img', "<center><img class='img-thumbnail' src='$2/$1'><center>", 'company_logo,server_path("images/logo/")');
        $this->datatables->add_column('edit', $str, 'company_id,company_name,company_code,supportToken');
        echo $this->datatables->generate();
    }

    function loadCompanyAdminUsers()
    {
        $companyID = $this->input->post('companyid');
        $data['companyInfo'] = $this->Dashboard_model->get_srp_erp_company_specific($companyID);
        $data['adminList'] = $this->Dashboard_model->load_srp_erp_companyadminmaster($companyID);
        $this->load->view('companyAdmin/ajax/load-company-admin-view', $data);
    }

    function save_companyAdmin()
    {
        try {
            $this->form_validation->set_rules('adminName', 'Name', 'trim|required');
            $this->form_validation->set_rules('adminEmail', 'Email', 'trim|required');
            $this->form_validation->set_rules('companyID', 'companyID', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $output = json_encode(array('error' => 1, 'message' => validation_errors()));
            } else {

                $adminName = $this->input->post('adminName');
                $adminEmail = $this->input->post('adminEmail');
                $companyID = $this->input->post('companyID');

                if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Invalid email format";
                    $output = json_encode(array('error' => 1, 'message' => $emailErr));

                } else {
                    $this->db->select("*");
                    $this->db->from("srp_erp_companyadminmaster");
                    $this->db->where("adminEmail", $adminEmail);
                    $this->db->where("companyID", $companyID);
                    $r = $this->db->get()->row_array();
                    if (!empty($r)) {
                        $output = json_encode(array('error' => 1, 'message' => 'This email address is already added to this company.'));
                    } else {

                        $date = date('Y-m-d H:i:s');

                        $dataTmp['adminName'] = $adminName;
                        $dataTmp['adminEmail'] = $adminEmail;
                        $dataTmp['pinNumber'] = null;
                        $dataTmp['companyID'] = $companyID;
                        $dataTmp['isActive'] = 1;
                        $dataTmp['createdBy'] = 1;
                        $dataTmp['createdDatetime'] = $date;
                        $dataTmp['createdPC'] = $_SERVER['REMOTE_ADDR'];
                        $dataTmp['timestamp'] = $date;

                        $result = $this->Dashboard_model->save_companyAdmin($dataTmp);

                        if ($result) {
                            $output = json_encode(array('error' => 0, 'message' => 'Admin name saved.', 'code' => $companyID));
                        } else {
                            $output = json_encode(array('error' => 1, 'message' => 'Error while saving. Please try again'));
                        }
                    }
                }
            }

            echo $output;

        } catch (Exception $e) {

            echo json_encode(array('error' => 1, 'message' => $e->getMessage()));
        }
    }

    function request_pin()
    {
        $id = $this->input->post('id');
        $info = $this->Dashboard_model->get_srp_erp_companyadminmaster_specific($id);

        $PIN = rand(10000, 99999);
        $updatePIN = $this->Dashboard_model->update_pin($id, $PIN);

        $data = array(
            'empEmail' => $info["adminEmail"],
            'empName' => $info["adminName"],
            'emailSubject' => 'Request for PIN',
            'emailBody' => 'Please login to system using the PIN:' . $PIN,
            'id' => $id
        );

        $output = $this->sendEmail($data);
        if ($output) {
            echo json_encode(array('error' => 0, 'message' => 'PIN: ' . $PIN));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error occurred'));
        }
    }

    /**Created by shahmy */

    public function showAllmodulesView()
    {
        $data['title'] = 'Module';
        $data['main_content'] = 'company_module_view_all';
        $data['extra'] = NULL;
        $this->load->view('include/template', $data);
    }

    function showAllmodules()
    {
        $module = $this->Dashboard_model->showAllmodules();
        $tmpData['modules'] = $module;
        $this->load->view('company_module_view', $tmpData);

    }

    function showAllInvoicesView()
    {
        $invoice = $this->Dashboard_model->showAllInvoicesByCompanyID();
        $tmpData['invoices'] = $invoice;
        $this->load->view('company_invoices_view', $tmpData);
    }

    function getModuleDetail()
    {
        echo json_encode($this->Dashboard_model->getModuleDetail());
    }

    function update_moduleDescirption()
    {
        echo json_encode($this->Dashboard_model->update_moduleDescirption());
    }

    function sendEmail($param)/*send mail*/
    {
        $config['mailtype'] = "html";
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.sparkpostmail.com';
        $config['smtp_user'] = 'SMTP_Injection';
        $config['smtp_pass'] = '6d911d3e2ffe9faabc3af1e289eb067908deb1c5';
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = '587';
        $condig['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $this->load->library('email',$config);
        $this->email->from('noreply@spur-int.com', 'Spur');
        if (!empty($param)) {
            $this->email->to($param["empEmail"],$param["empName"]);
            $this->email->subject($param["emailSubject"]);
            $this->email->message($this->load->view('template', $param, TRUE));
        }
        $result = $this->email->send();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    function sendEmail2($param)/*send mail*/
    {
        $this->load->library('MY_PHPMailer');
        $mail = new MY_PHPMailer();
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.sparkpostmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'SMTP_Injection';                 // SMTP username
        $mail->Password = '6d911d3e2ffe9faabc3af1e289eb067908deb1c5';                           // SMTP password
        $mail->Port = 587;                                    // TCP port to connect to
        $mail->setFrom('noreply@spur-int.com', 'Cloud Spur');
        $mail->addEmbeddedImage('images/Votexcloudsme.png', 'logo_1u');
        $mail->addEmbeddedImage('images/VotexLogo.png', 'logo_2u');
        $mail->isHTML(true);
        //$mail->SMTPDebug  = 2;
        if (!empty($param)) {
            //$mail->SMTPDebug = 3;                               // Enable verbose debug output
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->addAddress($param["empEmail"], $param["empName"]);     // Add a recipient
            // Set email format to HTML
            $mail->Subject = $param["emailSubject"];
            $msg = "<div style='width: 80%;margin: auto;background-color:#fbfbfb ;padding: 2%;font-family: sans-serif;'><img src='cid:logo_1u' style='width:16%;display: block;'><hr><h2 style='text-align: center;'>" . $param["emailSubject"] . "</h2> <br><b>Hi " . $param["empName"] . "</b> <br><br> <p>" . $param["emailBody"] . "</p><br><br><br><p><em>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox.</em></p><hr><img src='cid:logo_2u' style='width:16%;display: block;margin: auto;'><br><p style='text-align: center;'></p></div>";
            $mail->Body = $msg;
            $result = $mail->send();
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function showAllNavigation()
    {
        $data['title'] = 'Navigation Menu';
        $data['main_content'] = 'erp_navigation_group_setup';
        $data['extra'] = NULL;
        $this->load->view('include/template', $data);
    }

    public function showAllInvoices(){
        $data['title'] = 'Invoices Menu';
        $data['main_content'] = 'company_invoices';
        $data['extra'] = NULL;
        $this->load->view('include/template', $data);
    }

    public function load_company_header_view($id = NULL)
    {
        $companyID = $this->input->post('companyid');
        $this->Dashboard_model->set_company_credential($companyID);
        $data['currency_arr'] = $this->Dashboard_model->fetch_currency_arr();
        $data['countrys'] = $this->Dashboard_model->load_country_drop();
        $data['school_arr'] = $this->Dashboard_model->fetch_school_arr();
        $data['all_currency_drop'] = $this->Dashboard_model->all_currency_drop();
        $data['company_id'] = $companyID;
        $this->load->view('company_header', $data);
    }

    function load_navigation_usergroup_setup()
    {
        $data["menu"] = $this->Dashboard_model->load_navigation_usergroup_setup();
        $data["module"] = $this->Dashboard_model->load_navigation_module();
        $html = $this->load->view('ajax-erp_navigation_group_setup', $data, true);
        echo $html;
    }

    function save_navigation()
    {
        $type = $this->input->post('type');
        $level = $this->input->post('level');
        $subexist = $this->input->post('subexist');
        $this->form_validation->set_rules('icon', 'Icon', 'trim|required');
        $this->form_validation->set_rules('pagetitle', 'Page Title', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('type', 'Type', 'trim|required');

        if ($type == 2 && $level == 1 || $level == 2) {
            $this->form_validation->set_rules('modules', 'Modules', 'trim|required');
        }
        if ($subexist == 0) {
            $this->form_validation->set_rules('url', 'URL', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Dashboard_model->save_navigation());
        }
    }

    public function test()
    {
        $dbFile = "C:/wamp/www/portal/uploads/access/ras.mdb"; // Path to MS Access DB file
        try {
            $db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};"
                . "DBQ=$dbFile; Uid=; Pwd=;");

            $sql = "SELECT * FROM ras_AttRecord";
            $result = $db->query($sql);
            $columns = $result->fetchAll(PDO::FETCH_ASSOC);
            var_dump($columns);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function all_currency_drop()
    {
        echo json_encode($this->Dashboard_model->all_currency_drop());
    }

    function load_master()
    {
        echo json_encode($this->Dashboard_model->load_master());
    }

    function subscription(){
        $companiesList = $this->Dashboard_model->loadAllCompanies();
        $tmpData['companiesList'] = $companiesList;
        $data['title'] = 'Subscription';
        $data['main_content'] = 'subscription_view';
        $data['extra'] = $tmpData;
        $this->load->view('include/template', $data);
    }

    function fetch_company_subscription_data(){
        $subType = $this->input->post('subType');
        $paymentType = $this->input->post('paymentType');
        $com_type = $this->input->post('com_type');
        $typeCount = $this->db->select('count(id) tCount')->get('system_company_type')->row('tCount');
        
        $this->datatables->select('company_id, CONCAT( company_code, \'-\', company_name ) AS com_name, company_country, subscriptionNo, registeredDate, 
            subscriptionStartDate, nextRenewalDate,  lastRenewedDate, CurrencyCode, subscriptionAmount, subscriptionCurrency AS curr_id, company_code, company_name,
            FORMAT( subscriptionAmount, IFNULL(DecimalPlaces,2) ) AS subAmount, FORMAT( implementationAmount, IFNULL(DecimalPlaces,2) ) AS impAmount, 
            IF ( paymentEnabled = 1, \'Yes\', \'No\' ) AS paymentEnabled, company_email, company_phone, companyPrintTelephone, last_access_date, company_business_name,
            IF (isSubscriptionDisabled = 0, isSubscriptionExpire(company_id), isSubscriptionDisabled) AS isSubscriptionDisabled, cType.description AS tyDes')
            ->from('company_subscription_view AS sub_view')
            ->join('system_company_type AS cType', 'cType.id=sub_view.isPartnerCompany', 'left')
            ->where('adminType', current_userType());
 
        if($com_type !== ''){
            $com_type = explode(',', $com_type);    
            if($typeCount != count($com_type)){
                $this->datatables->where_in('sub_view.isPartnerCompany', $com_type);
            }
        }
        
        if($paymentType !== ''){            
            $paymentType = explode(',', $paymentType);
            $this->datatables->where_in('IFNULL(paymentEnabled,0)', $paymentType);
        }

        if($subType !== ''){
            $subType = explode(',', $subType);
                $this->datatables->where_in('IF(isSubscriptionDisabled = 0, isSubscriptionExpire(company_id), isSubscriptionDisabled)', $subType);
        }

        $this->datatables->edit_column('com_name', '<div style="width: 200px">$1</div>', 'com_name')
            ->add_column('company_det', '<b>Email ID</b>: $1<br/><b>Phone No</b>: $2<br/><b>Mobile No</b>: $3<br/>', 'company_email,company_phone,companyPrintTelephone')
            ->edit_column('subscriptionAmount', '<div style="text-align: right">$1</div>', 'subAmount')
            ->edit_column('implementationAmount', '<div style="text-align: right">$1</div>', 'impAmount')
            ->edit_column('currencyCode', '<div style="text-align: center">$1</div>', 'CurrencyCode')
            ->edit_column('paymentEnabled', '<div style="text-align: center">$1</div>', 'paymentEnabled')
            ->add_column('subscription_status', '$1', 'company_status_str(company_id,isSubscriptionDisabled,\'subscription_tb\')')
            ->add_column('daysLeftForExpire', '<div style="text-align: center">$1</div>', 'daysLeftForExpire(company_id,isSubscriptionDisabled)')
            ->add_column('edit', '$1', 'subscription_action(company_id)');
        echo $this->datatables->generate();
    }

    function update_subscription_amount(){
        $this->form_validation->set_rules('sub_company_id', 'Company_id', 'trim|required');
        $this->form_validation->set_rules('sub_amount', 'Amount', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $company_id = $this->input->post('sub_company_id');
        $amount = $this->input->post('sub_amount');

        if($amount == 0 || empty($amount)){
            die( json_encode(['e', 'Amount field is not valid']) );
        }

        $date_time = date('Y-m-d H:i:s');
        $pc = current_pc(); $user_id = current_userID(); $user_name = current_userName();

        $dPlace = $this->db->query("SELECT srp_erp_currencymaster.DecimalPlaces AS dPlace
                        FROM srp_erp_company AS com_tb 
                        JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = com_tb.subscriptionCurrency 
                        WHERE com_tb.company_id = {$company_id} ")->row('dPlace');

        $dPlace = (empty($dPlace))? 2: $dPlace;
        $amount = round($amount, $dPlace);

        $data = [
            'subscriptionAmount'=> $amount, 'modifiedPCID'=> $pc, 'modifiedUserID'=> $user_id,
            'modifiedDateTime'=> $date_time, 'modifiedUserName'=> $user_name, 'timestamp'=> $date_time,
        ];

        $this->db->trans_start();

        $old_val = $this->db->get_where('srp_erp_company', ['company_id'=>$company_id])->row('subscriptionAmount');
        if($old_val != $amount){
            $audit_log = [
                'tableName' => 'srp_erp_company', 'columnName'=> 'subscriptionAmount', 'old_val'=> $old_val,
                'display_old_val'=> $old_val, 'new_val'=> $amount, 'display_new_val'=> $amount, 'rowID'=> $company_id,
                'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];

            $this->db->insert('srp_erp_audit_log', $audit_log);
        }

        $this->db->where(['company_id'=>$company_id])->update('srp_erp_company', $data);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Subscription amount updated successfully.']);
        }else{
            echo json_encode(['e', 'Error in subscription amount update process.']);
        }
    }

    function subscription_history_data(){
        $company_id = $this->input->post('company_id');
        $company_data = $this->db->get_where('srp_erp_company', ['company_id'=>$company_id])->row_array();

        if($company_data['isInitialSubscriptionConfirmed'] == 0){
            //die( json_encode(['e', 'Initial subscription not confirmed yet.']) );
        }

        $is_paymentEnabled = $company_data['paymentEnabled'];
        $isInitialSubscriptionConfirmed = $company_data['isInitialSubscriptionConfirmed'];

        $sub_data = $this->db->query("SELECT t1.*, pay_ty.inv_id AS online_pay FROM (
                            SELECT * FROM (
                            -- Initial subscription 
                            SELECT his.subscriptionID, subscriptionStartDate, nextRenewalDate, lastRenewedDate, invNo, inv_data.invID,
                            FORMAT( IFNULL(invTotal, subscriptionAmount), dPlace) AS invoice_am, 1 AS inv_type, inv_data.isAmountPaid, his.dueDate,paymentType
                            FROM companysubscriptionhistory AS his 
                            JOIN  (
                                SELECT company_id, DecimalPlaces AS dPlace 
                                FROM srp_erp_company AS com 
                                JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = com.subscriptionCurrency 
                                AND com.company_id = {$company_id} AND com.isInitialSubscriptionConfirmed = 1
                            ) AS com_tb ON com_tb.company_id = his.companyID                               
                            LEFT JOIN (
                                SELECT subscriptionID, in_mas.invID, invNo, invTotal, in_mas.isAmountPaid,	in_mas.paymentType
                                FROM subscription_invoice_master AS in_mas 
                                JOIN subscription_invoice_details AS in_det ON in_mas.invID = in_det.invID
                                WHERE in_mas.companyID = {$company_id}  AND in_det.itemID = 1
                                ORDER BY subscriptionID LIMIT 1 
                            ) AS inv_data ON inv_data.subscriptionID = his.subscriptionID
                            WHERE companyID = {$company_id}  AND his.subscriptionID = (
                                SELECT MIN(subscriptionID) FROM companysubscriptionhistory WHERE companyID = {$company_id}
                            )  
                         ) t1
                         UNION ALL
                         SELECT * FROM (
                             -- Initial Implementation 
                            SELECT his.subscriptionID, his.subscriptionStartDate, '' AS nextRenewalDate, his.lastRenewedDate, invNo,
                            inv_data.invID, FORMAT( IFNULL(invTotal, com_tb.implementationAmount), DecimalPlaces ) AS invoice_am, 2 AS inv_type,
                            inv_data.isAmountPaid, '' AS dueDate ,paymentType
                            FROM companysubscriptionhistory AS his 
                            JOIN srp_erp_company AS com_tb ON com_tb.company_id = his.companyID AND com_tb.isInitialSubscriptionConfirmed = 1
                            JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = com_tb.subscriptionCurrency
                            LEFT JOIN (
                                SELECT subscriptionID, in_mas.invID, invNo, invTotal, in_mas.isAmountPaid,in_mas.paymentType
                                FROM subscription_invoice_master AS in_mas 
                                JOIN subscription_invoice_details AS in_det ON in_mas.invID = in_det.invID
                                WHERE in_mas.companyID = {$company_id}  AND in_det.itemID = 2
                                ORDER BY subscriptionID LIMIT 1 
                            ) AS inv_data ON inv_data.subscriptionID = his.subscriptionID
                            WHERE companyID = {$company_id}  AND com_tb.implementationAmount > 0 AND his.subscriptionID = (
                                SELECT MIN(subscriptionID) FROM companysubscriptionhistory WHERE companyID = {$company_id}
                            )  
                         ) t2
                         UNION ALL
                         SELECT * FROM (
                            -- Annual subscription amount
                            SELECT his.subscriptionID, subscriptionStartDate, nextRenewalDate, lastRenewedDate, invNo, inv_data.invID,
                            FORMAT( IFNULL(invTotal, subscriptionAmount), dPlace ) AS invoice_am, 1 AS inv_type, inv_data.isAmountPaid, 
                            his.dueDate, inv_data.paymentType
                            FROM companysubscriptionhistory AS his JOIN  (
                                SELECT company_id, DecimalPlaces AS dPlace 
                                FROM srp_erp_company AS com 
                                JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = com.subscriptionCurrency 
                                AND com.company_id = {$company_id}
                            ) AS com_tb ON com_tb.company_id = his.companyID                             
                            LEFT JOIN subscription_invoice_master AS inv_data ON inv_data.subscriptionID = his.subscriptionID	 
                            WHERE his.companyID = {$company_id} AND his.subscriptionID > (
                                SELECT MIN(subscriptionID) FROM companysubscriptionhistory WHERE companyID = {$company_id}
                            ) ORDER BY his.subscriptionID 
                         ) t3 
                         UNION ALL
                         -- Ad hoc invoices
                         SELECT '', '', '', '', invNo, invID, FORMAT( invTotal, invDecPlace ) AS invoice_am, 3 AS inv_type, isAmountPaid, 
                         '', paymentType
                         FROM subscription_invoice_master WHERE companyID = {$company_id} AND subscriptionID = 0
                         
                         ) t1
                         LEFT JOIN (
                            SELECT inv_id FROM subscription_invoice_payment_details WHERE companyID = {$company_id} AND pay_type IN (2,4,5)
                            GROUP BY inv_id                             
                        ) AS pay_ty ON pay_ty.inv_id = t1.invID
                         ORDER BY invID DESC")->result_array();                         

        echo json_encode(['s', 'att_data'=>$sub_data, 'is_paymentEnabled'=>$is_paymentEnabled, 'isInitialSubscriptionConfirmed'=>$isInitialSubscriptionConfirmed]);
    }

    function load_invoice(){
        $inv_id = $this->input->post('inv_id');
        $inv_data = $this->Dashboard_model->load_invoice_view($inv_id);

        if($inv_data['status'] == 'error'){
            die( json_encode(['e', $inv_data['msg']]) );
        }

        $company_id = $inv_data['mas_data']['companyID'];
        $company_code = $inv_data['mas_data']['company_code'];
        $att_data = $this->db->query("SELECT attachmentID, attachmentDescription, fileName, fileType FROM documentattachments  
                        WHERE documentSystemCode = {$inv_id} AND companyID = {$company_id}")->result_array();

        $att_view = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        if(!empty($att_data)){
            $i = 1;
            $att_view = '';

            $this->load->library('s3');
            foreach ($att_data as $row) {
                $link = $this->s3->createPresignedRequest("{$company_code}/subscription/".$row['fileName'], '+1 hour');

                $att_view .= '<tr class="">
                              <td>'.$i.'</td>
                              <td >'.$row['fileName'].'</td>
                              <td>'.$row['attachmentDescription'].'</td>
                              <td class="text-center">'.file_type_icon($row['fileType']).'</td>
                              <td class="text-center">                                            
                                 <a target="_blank" href="' . $link . '" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>                                  
                              </td>
                          </tr>';
                $i++;
            }
        }

        $data['inv_id'] = $inv_id;
        $data['inv_data'] = $inv_data;
        $data['att_view'] = $att_view;
        $data['inv_id'] = $inv_id;
        $data['view_type'] = 'V';
        $data['company_id'] = $company_id;
        $data['is_view_only'] = $this->input->post('is_view_only');
        $data['paymentDet'] = $this->invoice_payment_details_view($inv_id, $inv_data['mas_data']['invDecPlace']);
        $view = $this->load->view('subscription-invoice-view.php', $data, true);

        echo json_encode(['s', 'view'=>$view]);
    }

    function initial_invoice_generate(){
        $this->form_validation->set_rules('sub_id', 'Subscription ID', 'trim|required');
        $this->form_validation->set_rules('inv_date', 'Invoice date', 'trim|required');
        $this->form_validation->set_rules('itemID', 'Item ID', 'trim|required');

        $itemID = $this->input->post('itemID');
        if($itemID == 1){
            $this->form_validation->set_rules('due_date', 'Due date', 'trim|required');
        }
        $this->form_validation->set_rules('inv_det_des', 'Item description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $sub_id = $this->input->post('sub_id');
        $inv_date = $this->input->post('inv_date');
        $inv_det_des = $this->input->post('inv_det_des');
        $discountPer = $this->input->post('discountPer');
        $discountAmount = $this->input->post('discountAmount');

        if($discountPer > 100){
            die( json_encode(['e', 'Discount percentage can not be greater than 100']) );
        }

        /*Check invoice already generated */
        $inv_status = $this->db->query("SELECT itm_type.description FROM subscription_invoice_master AS inv_mas 
                                JOIN subscription_invoice_details AS inv_det ON inv_mas.invID = inv_det.invID
                                JOIN system_invoice_item_type itm_type ON itm_type.type_id = inv_det.itemID
                                WHERE inv_mas.subscriptionID = {$sub_id} AND inv_det.itemID = {$itemID} ")->row('description');

        if(!empty($inv_status)){
            die( json_encode(['e', "Invoice already generated for {$inv_status}.<p>Please refresh the page and check."]) );
        }

        $this->db->trans_start();

        $sub_data = $this->db->query("SELECT sub_tb.companyID, com_tb.subscriptionCurrency, com_tb.implementationAmount, 
                                 com_tb.company_name, sub_tb.subscriptionAmount, srp_erp_currencymaster.DecimalPlaces, 
                                 companyPrintAddress, company_email, srp_erp_currencymaster.CurrencyCode
                                 FROM companysubscriptionhistory AS sub_tb
                                 JOIN srp_erp_company AS com_tb ON com_tb.company_id = sub_tb.companyID
                                 JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = com_tb.subscriptionCurrency
                                 WHERE sub_tb.subscriptionID={$sub_id}")->row_array();


        $company_id = $sub_data['companyID'];
        $currency = $sub_data['subscriptionCurrency'];
        $dPlace = $sub_data['DecimalPlaces'];
        $date_time = date('Y-m-d H:i:s');
        $pc = current_pc(); $user_id = current_userID();

        $amount = ($itemID == 1)? $sub_data['subscriptionAmount']: $sub_data['implementationAmount'];
        if($amount <= 0){
            die( json_encode(['e', 'Invoicing amount not valid.']) );
        }

        if($discountAmount > $amount){
            die( json_encode(['e', 'Discount amount can not be greater than '.number_format($amount, $dPlace)]) );
        }

        $invNoData = $this->Dashboard_model->generate_subscription_inv_no(1);
        $serialNo = $invNoData['serialNo'];
        $invNo = $invNoData['inv_no'];
        $inv_id = null;

        $sub_total = $amount;
        if($discountAmount != ''){
            $sub_total = round(($amount - $discountAmount), $dPlace);
        }
        else{
            if($discountPer != ''){
                $sub_total =  $amount - (($discountPer / 100) * $amount);
                $sub_total = round($sub_total, $dPlace);
            }
        }



        $master_data = [
            'subscriptionID'=> $sub_id, 'invNo'=> $invNo, 'invDate'=>$inv_date, 'invCur'=> $currency,
            'invDecPlace'=> $dPlace, 'serialNo'=> $serialNo, 'invTotal'=> $sub_total, 'companyID'=> $company_id
        ];

        $audit_log = [];
        foreach($master_data as $column=>$new_val){
            $audit_log[] = [
                'tableName' => 'subscription_invoice_master', 'columnName'=> $column, 'old_val'=> '',
                'display_old_val'=> '', 'new_val'=> $new_val, 'display_new_val'=> $new_val,
                'rowID'=> &$inv_id, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $def = [
            'createdPCID'=> $pc, 'createdUserID'=> $user_id, 'createdDateTime'=> $date_time, 'timestamp'=> $date_time
        ];

        $master_data = array_merge($master_data, $def);

        $this->db->insert('subscription_invoice_master', $master_data);
        $inv_id = $this->db->insert_id();

        $detail  = [
            'invID'=> $inv_id, 'itemID'=> $itemID, 'itemDescription'=> $inv_det_des, 'amountBeforeDis'=> $amount,
            'discountAmount'=> $discountAmount, 'discountPer'=> $discountPer, 'amount'=> $sub_total, 'companyID'=> $company_id,
        ];

        $detail_id = null;
        foreach($detail as $column=>$new_val){
            $new_val_dis = $new_val;
            switch($column){
                case 'itemID':
                    $new_val_dis = $this->db->select('description')->from('system_invoice_item_type')->where('type_id', $new_val)->get()->row('description');
                break;
            }

            $audit_log[] = [
                'tableName' => 'subscription_invoice_details', 'columnName'=> $column, 'old_val'=> '',
                'display_old_val'=> '', 'new_val'=> $new_val, 'display_new_val'=> $new_val_dis,
                'rowID'=> &$detail_id, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $detail = array_merge($detail, $def);

        $this->db->insert('subscription_invoice_details', $detail);
        $detail_id = $this->db->insert_id();

        $master_data['company_name'] = $sub_data['company_name'];
        $master_data['companyPrintAddress'] = $sub_data['companyPrintAddress'];
        $master_data['CurrencyCode'] = $sub_data['CurrencyCode'];

        $int_arr[$inv_id]['toEmail'] = $sub_data['company_email'];
        $int_arr[$inv_id]['subject'] = 'Subscription Amount';
        $int_arr[$inv_id]['mas_data'] = $master_data;
        $int_arr[$inv_id]['det_data'][] = $detail;

        if($itemID == 1){
            $sub_history_data['dueDate'] = $this->input->post('due_date');
        }
        $sub_history_data['isInvoiceGenerated'] = 1;

        foreach($sub_history_data as $column=>$new_val){
            $old_val = $old_val_display = '';
            if($column == 'dueDate'){
                $old_val = $this->db->get_where('companysubscriptionhistory', ['subscriptionID'=>$sub_id])->row('dueDate');
                if($old_val == $new_val){
                    continue;
                }
                $old_val_display = $old_val;
            }

            $audit_log[] = [
                'tableName' => 'companysubscriptionhistory', 'columnName'=> $column, 'old_val'=> $old_val,
                'display_old_val'=> $old_val_display, 'new_val'=> $new_val, 'display_new_val'=> $new_val,
                'rowID'=> $sub_id, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }


        $this->db->insert_batch('srp_erp_audit_log', $audit_log);


        $sub_history_data['modifiedPCID'] = $pc;
        $sub_history_data['modifiedUserID'] = $user_id;
        $sub_history_data['modifiedUserName'] = current_userName();
        $sub_history_data['modifiedDateTime'] = $date_time;

        $this->db->where(['subscriptionID'=>$sub_id])->update('companysubscriptionhistory', $sub_history_data);


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in invoice generation']);
        }
        else {
            $this->db->trans_commit();

            if($itemID == 1){
                $this->load->helper('host');
                $confirmedYN = $this->db->query("SELECT confirmedYN FROM srp_erp_company WHERE company_id = {$company_id}")->row('confirmedYN');
                if($confirmedYN == 1){
                    foreach ($int_arr as $mailData){
                        send_subscription_mail($mailData);
                        //echo $this->load->view('email_subscription_template', $mailData, true);
                        //echo '<pre>'; print_r($mailData); echo '</pre>';
                    }
                }
            }

            $is_next_inv = ($itemID == 1 && $sub_data['implementationAmount'] >= 0)? 1: 0;
            $txt = ($itemID == 1)? 'Subscription':  'Implementation';
            die( json_encode(['s', $txt.' invoice generation successfully done.', 'is_implementation_billing'=>$is_next_inv, 'company_id'=>$company_id]) );
        }
    }

    function fetch_data_implementation_inv_generation(){
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $company_id = $this->input->post('company_id');
        $sub_id = $this->db->query("SELECT subscriptionID FROM companysubscriptionhistory WHERE companyID={$company_id} 
                                            ORDER BY subscriptionID ASC LIMIT 1")->row('subscriptionID');

        $_POST['sub_id'] = $sub_id;
        $_POST['itemID'] = 2;

        return $this->build_initial_invoice();
    }

    function build_initial_invoice(){
        $this->form_validation->set_rules('sub_id', 'Subscription ID', 'trim|required');
        $this->form_validation->set_rules('itemID', 'Invoice Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $sub_id = $this->input->post('sub_id');
        $itemID = $this->input->post('itemID');

        $master_data = $this->db->query("SELECT sub_his.subscriptionID AS sub_id, com_tb.company_id, sub_his.nextRenewalDate, sub_his.subscriptionAmount,
                                  com_tb.implementationAmount, cur_mas.currencyID, cur_mas.CurrencyCode, cur_mas.DecimalPlaces AS invDecPlace, 
                                  com_tb.company_name, companyPrintAddress, company_email, com_tb.isInitialSubscriptionConfirmed, sub_his.dueDate
                                  FROM srp_erp_company AS com_tb
                                  JOIN (
                                      SELECT subscriptionID, companyID, nextRenewalDate, dueDate, subscriptionAmount 
                                      FROM companysubscriptionhistory WHERE subscriptionID = {$sub_id} 
                                  ) AS sub_his ON sub_his.companyID = com_tb.company_id
                                  JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID=com_tb.subscriptionCurrency")->row_array();

        if($master_data['isInitialSubscriptionConfirmed'] == 0){
            die( json_encode(['e', 'Subscription details not confirmed yet']) );
        }

        $companyID = $master_data['company_id'];

        $invoice_status = $this->db->query("SELECT invNo FROM companysubscriptionhistory AS his 
                                            JOIN (
                                                SELECT subscriptionID, in_mas.invID, invNo, invTotal 
                                                FROM subscription_invoice_master AS in_mas 
                                                JOIN subscription_invoice_details AS in_det ON in_mas.invID = in_det.invID
                                                WHERE in_mas.companyID = {$companyID}  AND in_det.itemID = {$itemID}
                                                ORDER BY subscriptionID LIMIT 1 
                                            ) AS inv_data ON inv_data.subscriptionID = his.subscriptionID 
                                            WHERE companyID = {$companyID} AND his.subscriptionID = {$sub_id}")->row('invNo');

        if(!empty($invoice_status)){
            die( json_encode(['e', 'Already invoice generated.']) );
        }


        $date_time = date('Y-m-d H:i:s');
        $description = ($itemID == 1)? 'Subscription Amount': 'Implementation Amount';
        $amount = ($itemID == 1)? $master_data['subscriptionAmount'] : $master_data['implementationAmount'] ;
        $invNo = $this->Dashboard_model->generate_subscription_inv_no();

        if($amount <= 0){
            die( json_encode(['e', 'Invoicing amount is not valid']) );
        }

        $master_data['invNo'] = $invNo;
        $master_data['invDate'] = $date_time;
        $detail = [ 'itemID' => $itemID, 'itemDescription'=> $description, 'description'=> $description, 'amount'=> $amount ];

        $inv_data['toEmail'] = $master_data['company_email'];
        $inv_data['subject'] = $description;
        $inv_data['mas_data'] = $master_data;
        $inv_data['det_data'][] = $detail;

        $data['company_id'] = $companyID;
        $data['inv_data'] = $inv_data;
        $data['view_type'] = 'E';

        $built_view = $this->load->view('subscription-invoice-view.php', $data, true);

        echo json_encode(['s', 'built_view'=> $built_view]);
    }

    function verify_subscription_payment(){
        $this->form_validation->set_rules('inv_id', 'Invoice ID', 'trim|required');
        if ($this->form_validation->run() == false) {
            die( json_encode(['e', validation_errors()]) );
        }

        $inv_id = $this->input->post('inv_id');
        $where = ['invID'=>$inv_id];
        $inv_status = $this->db->get_where('subscription_invoice_master', $where)->row('isAmountPaid');


        if($inv_status != -1){
            die( json_encode(['e', 'This invoice not in verification status']) );
        }

        $date_time = date('Y-m-d H:i:s');
        $data = [
            'isAmountPaid'=> 1, 'modifiedPCID'=> current_pc(), 'modifiedUserID'=> current_userID(),
            'modifiedDateTime'=> $date_time, 'timestamp'=> $date_time
        ];

        $this->db->trans_start();
        $this->db->where($where)->update('subscription_invoice_master', $data);
        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Payment verified successfully.']);
        }else{
            echo json_encode(['e', 'Error in payment verification process.']);
        }
    }

    function update_invoice_dueDate(){
        $this->form_validation->set_rules('sub_id', 'Subscription ID', 'trim|required');
        $this->form_validation->set_rules('due_date', 'Due date', 'trim|required');
        $this->form_validation->set_rules('inv_id', 'Invoice ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $inv_id = $this->input->post('inv_id');

        $payment_status = $this->db->get_where('subscription_invoice_master', ['invID'=>$inv_id])->row_array();
        $company_id = $payment_status['companyID'];
        if($payment_status['isAmountPaid'] != 0){
            die( json_encode(['e', 'Payment already received.You can not change the due date.']) );
        }

        $sub_id = $this->input->post('sub_id');
        $due_date = $this->input->post('due_date');
        $date_time = date('Y-m-d H:i:s');
        $user_id = current_userID();

        $sub_history_data = [
            'dueDate' => $due_date, 'modifiedPCID' => current_pc(), 'modifiedUserID' => $user_id,
            'modifiedUserName' => current_userName(), 'modifiedDateTime' => $date_time
        ];

        $this->db->trans_start();

        $old_val = $this->db->get_where('companysubscriptionhistory', ['subscriptionID'=>$sub_id])->row('dueDate');
        if($old_val != $due_date){
            $audit_log = [
                'tableName' => 'companysubscriptionhistory', 'columnName'=> 'dueDate', 'old_val'=> $old_val,
                'display_old_val'=> $old_val, 'new_val'=> $due_date, 'display_new_val'=> $due_date, 'rowID'=> $sub_id,
                'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
            $this->db->insert('srp_erp_audit_log', $audit_log);
        }

        $this->db->where(['subscriptionID'=>$sub_id])->update('companysubscriptionhistory', $sub_history_data);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Due date Updated successfully.', 'company_id'=> $company_id]);
        }else{
            echo json_encode(['e', 'Error in due date update process.']);
        }
    }

    function mark_as_paid_amt(){
        $inv_id = $this->input->post('inv_id');
        $mark_as_paid =  $this->input->post('ismarkaspaid') ;
        $user_id = current_userID();
        $date_time = date('Y-m-d H:i:s');
        $isAmountPaid = ($mark_as_paid == 3) ? 1 : 0;

        $data = [
            'paymentType'=> $mark_as_paid, 'isAmountPaid'=> $isAmountPaid,
            'payRecDate'=> ($isAmountPaid)? $date_time: null,
        ];

        $old_records = $this->db->get_where('subscription_invoice_master', ['invID'=>$inv_id])->row_array();
        $audit_log = [];
        foreach($data as $column=>$new_val){
            $old_val = (!empty($old_records))? $old_records[$column]: '';
            $old_display_val = $old_val;
            $new_display_val = $new_val;
            if($column == 'paymentType'){
                $old_display_val = ($old_val == 3)? 'Manual': '';
                $new_display_val = ($new_val == 3)? 'Manual': '';
            }

            $audit_log[] = [
                'tableName' => 'subscription_invoice_master', 'columnName'=> $column, 'old_val'=> $old_val,
                'display_old_val'=> $old_display_val, 'new_val'=> $new_val, 'display_new_val'=> $new_display_val,
                'rowID'=> $inv_id, 'companyID'=> $old_records['companyID'], 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $data2 = [
            'modifiedPCID'=> current_pc(), 'modifiedUserID'=> $user_id, 'modifiedDateTime'=> $date_time, 'timestamp'=> $date_time
        ];

        $data = array_merge($data, $data2);

        $this->db->trans_start();

        $this->db->where(['invID'=>$inv_id])->update('subscription_invoice_master', $data);
        $this->db->insert_batch('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Payment Updated successfully.']);
        }else{
            echo json_encode(['e', 'Error in payment update.']);
        }
    }

    function companyStatusChange(){
        $this->form_validation->set_rules('com_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('status', 'Subscription Status', 'trim|required');

        $status = $this->input->post('status');

        if($status != 0){
            $this->form_validation->set_rules('sub_comment', 'Comment', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $com_id = $this->input->post('com_id');
        $sub_comment = $this->input->post('sub_comment');
        $user_id = current_userID();
        $date_time =  date('Y-m-d H:i:s');
        $audit_log = [];

        $old_val = $this->db->get_where('srp_erp_company', ['company_id'=>$com_id])->row('isSubscriptionDisabled');

        $this->db->trans_start();

        if($old_val != $status){
            $old_val_dis = status_display_val($old_val);
            $new_val_dis = status_display_val($status);

            $audit_log[] = [
                'tableName' => 'srp_erp_company', 'columnName'=> 'isSubscriptionDisabled', 'old_val'=> $old_val,
                'display_old_val'=> $old_val_dis, 'new_val'=> $status, 'display_new_val'=> $new_val_dis, 'rowID'=> $com_id,
                'companyID'=> $com_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $data = [
            'isSubscriptionDisabled'=> $status, 'modifiedPCID'=> current_pc(), 'modifiedUserID'=> $user_id,
            'modifiedDateTime'=> $date_time, 'timestamp'=> $date_time
        ];
        $this->db->where(['company_id'=>$com_id])->update('srp_erp_company', $data);

        $int_data = [
            'subStatus'=> $status, 'comment'=> $sub_comment, 'companyID'=> $com_id, 'createdPCID'=> current_pc(),
            'createdUserID'=> $user_id, 'createdDateTime'=> $date_time, 'timestamp'=> $date_time
        ];
        $this->db->insert('subscription_status_history', $int_data);
        $auto_id = $this->db->insert_id();

        $new_val_dis = status_display_val($status);
        $audit_log[] = [
            'tableName' => 'subscription_status_history', 'columnName'=> 'subStatus', 'old_val'=> '',
            'display_old_val'=> '', 'new_val'=> $status, 'display_new_val'=> $new_val_dis, 'rowID'=> $auto_id,
            'companyID'=> $com_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
        ];
        $audit_log[] = [
            'tableName' => 'subscription_status_history', 'columnName'=> 'comment', 'old_val'=> '',
            'display_old_val'=> '', 'new_val'=> $sub_comment, 'display_new_val'=> $sub_comment, 'rowID'=> $auto_id,
            'companyID'=> $com_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
        ];

        $this->db->insert_batch('srp_erp_audit_log', $audit_log);


        $this->db->trans_complete();

        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Company status updated successfully']);
        }else{
            echo json_encode(['e', 'Error in company status update process.']);
        }
    }

    function update_paymentEnabled(){
        $com_id = $this->input->post('company_id');
        $payEnable = $this->input->post('paymentEnabled');
        $date_time = date('Y-m-d H:i:s');
        $user_id = current_userID();

        $data = [
            'paymentEnabled'=> $payEnable, 'modifiedPCID'=> current_pc(), 'modifiedUserID'=> $user_id,
            'modifiedDateTime'=> $date_time, 'timestamp'=> $date_time
        ];

        $old_val = $this->db->get_where('srp_erp_company', ['company_id'=>$com_id])->row('paymentEnabled');

        if($old_val == $payEnable){
            $old_val = ($old_val == 1)? 'Enabled': 'Disabled';
            die( json_encode(['e', "Already payment {$old_val}"]) );
        }

        $this->db->trans_start();

        $this->db->where(['company_id'=>$com_id])->update('srp_erp_company', $data);

        $old_val_dis = ($old_val == 1)? 'Yes': 'No';
        $new_val_dis = ($payEnable == 1)? 'Yes': 'No';
        $audit_log = [
            'tableName' => 'srp_erp_company', 'columnName'=> 'paymentEnabled', 'old_val'=> $old_val,
            'display_old_val'=> $old_val_dis, 'new_val'=> $payEnable, 'display_new_val'=> $new_val_dis, 'rowID'=> $com_id,
            'companyID'=> $com_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
        ];

        $this->db->insert('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();

        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Company payment enabled status updated successfully']);
        }else{
            echo json_encode(['e', 'Error in company payment enabled status update process.']);
        }
    }

    function upload_attachments(){
        $this->form_validation->set_rules('up-company-id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('attachmentDescription', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $company_id = $this->input->post('up-company-id');
        $dateTime = date('Y-m-d H:i:s');
        $expireDate = $this->input->post('expireDate');
        $expireDate = (!empty($expireDate)) ? $expireDate : null;

        if (empty($_FILES['document_file']['name'])) {
            die( json_encode(['e', 'File upload field is empty']) );
        }

        $file = $_FILES['document_file'];
        $att_des = trim($this->input->post('attachmentDescription'));

        if($file['error'] == 1){
            die( json_encode(['e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB).", $file]) );
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if(!in_array($ext, $allowed_types)){
            die( json_encode(['e', "The file type you are attempting to upload is not allowed. ( .{$ext} )"]) );
        }

        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if($size > 5){
            die( json_encode(['e', "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )"]) );
        }

        $company_code = $this->db->get_where('srp_erp_company', ['company_id'=>$company_id])->row('company_code');

        $fileName = "SUB_{$company_id}_".time().".$ext";
        $fileName_s3 = "{$company_code}/subscription/{$fileName}";
        $input = $this->s3->inputFile($file['tmp_name']);
        $s3Upload = $this->s3->putMyObject($input, $fileName_s3);

        if (!$s3Upload) {
            die( json_encode(['e', 'Error in document upload location configuration']) );
        }

        $this->db->trans_start();

        $emp_id = current_userID();
        $inv_data = [
            'documentID' => 'SUB', 'documentSystemCode' => $company_id, 'attachmentDescription' => $att_des, 'docExpiryDate' => $expireDate,
            'fileName' => $fileName, 'fileType' => $ext, 'fileSize' => $size, 'companyID' => $company_id,
            'createdPCID' => current_pc(),  'adminUserID' => $emp_id, 'createdDateTime' => $dateTime, 'timestamp' => $dateTime
        ];

        $this->db->insert('documentattachments', $inv_data);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Attachment successfully uploaded']);
        }else{
            echo json_encode(['e', 'Error in document attachment upload']);
        }
    }

    function subscription_attachment_view($company_id){
        $att_data = $this->db->query("SELECT attachmentID, attachmentDescription, fileName, fileType, docExpiryDate FROM documentattachments  
                        WHERE documentSystemCode = {$company_id} AND documentID = 'SUB' AND companyID = {$company_id}")->result_array();

        $view = '';
        if(!empty($att_data)){
            $i = 1;
            $company_code = $this->db->get_where('srp_erp_company', ['company_id'=>$company_id])->row('company_code');
            foreach ($att_data as $row) {

                $link = $this->s3->createPresignedRequest("{$company_code}/subscription/".$row['fileName'], '+1 hour');
                                $delete_str = '<span class="delete-rel-items"> &nbsp; | &nbsp; </span>
                                  <a onclick="sub_attachment_delete(' . $row['attachmentID'] . ',\'' . $row['fileName'] . '\')" title="Delete" class="delete-rel-items">
                                     <span rel="tooltip" class="glyphicon glyphicon-trash delete-icon"></span>
                                  </a>';

                $view .= '<tr class="">
                              <td>'.$i.'</td>
                              <td >'.$row['fileName'].'</td>
                              <td>'.$row['attachmentDescription'].'</td>
                              <td>'.$row['docExpiryDate'].'</td>
                              <td class="text-center">'.file_type_icon($row['fileType']).'</td>
                              <td class="text-center">                                            
                                 <a target="_blank" href="' . $link . '" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>
                                 '.$delete_str.'
                              </td>
                          </tr>';
                $i++;
            }
        }
        else{
            $view = '<tr class="danger"><td colspan="6" class="text-center">No Attachment Found</td></tr>';
        }

        return $view;
    }

    function load_subscription_attachment_view(){
        $company_id = $this->input->get('company_id');
        echo $this->subscription_attachment_view($company_id);
    }

    function subscription_attachment_delete() {
        $company_id = trim($this->input->post('company_id'));
        $attachmentID = trim($this->input->post('attachmentID'));
        $fileName = trim($this->input->post('fileName'));

        $company_code = $this->db->get_where('srp_erp_company', ['company_id'=>$company_id])->row('company_code');
        $fileName = "{$company_code}/subscription/$fileName";

        $result = $this->s3->deleteMyObject($fileName);
        if ($result) {
            $this->db->delete('documentattachments', ['attachmentID' => $attachmentID]);

            echo json_encode(['s', 'Attachment successfully deleted',]);
        } else {
            echo json_encode(['e', 'Error in attachment delete process']);
        }
    }

    function company_subscription_excel(){
        $subType = $this->input->post('subType');
        $paymentType = $this->input->post('paymentType');
        $com_type = $this->input->post('com_type');
        $text_search = trim($this->input->post('text-search'));
        $typeCount = $this->db->select('count(id) tCount')->get('system_company_type')->row('tCount');
        $this->db->select('company_id,CONCAT( company_code, \'-\', company_name ) AS com_name, company_country, subscriptionNo, registeredDate, 
            subscriptionStartDate, nextRenewalDate,  lastRenewedDate, CurrencyCode, subscriptionCurrency AS curr_id,
            FORMAT( subscriptionAmount, IFNULL(DecimalPlaces,2) ) AS subAmount, FORMAT( implementationAmount, IFNULL(DecimalPlaces,2) ) AS impAmount, 
            IF ( paymentEnabled = 1, \'Yes\', \'No\' ) AS paymentEnabled, company_email, company_phone, companyPrintTelephone, lastAccDate,
            IF (isSubscriptionDisabled = 0, isSubscriptionExpire(company_id), isSubscriptionDisabled) AS isSubscriptionDisabled, cType.description AS tyDes')
            ->from('company_subscription_view AS sub_view')            
            ->join("(SELECT companyID, MAX(createdDateTime) AS lastAccDate
                    FROM system_audit_log GROUP BY companyID) AS acc_tb", 'sub_view.company_id = acc_tb.companyID', 'left')
            ->join('system_company_type AS cType', 'cType.id=sub_view.isPartnerCompany', 'left')
            ->where('adminType', current_userType());
        if($com_type !== ''){   
                if($typeCount != count($com_type)){
                    $this->datatables->where_in('sub_view.isPartnerCompany', $com_type);
                }
            }
            
            if($paymentType !== ''){            
                $this->datatables->where_in('IFNULL(paymentEnabled,0)', $paymentType);
            }
    
            if($subType !== ''){
                    $this->datatables->where_in('IF(isSubscriptionDisabled = 0, isSubscriptionExpire(company_id), isSubscriptionDisabled)', $subType);
            }

        if(!empty($paymentType)){
            $this->db->where_in('IF (isSubscriptionDisabled = 0, isSubscriptionExpire(company_id), isSubscriptionDisabled)', $subType);
        }

        if($text_search){
            $this->db->where("(CONCAT( company_code, '-', company_name ) LIKE '%{$text_search}%' OR `company_country` LIKE '%{$text_search}%' OR 
                IF ( paymentEnabled = 1, 'Yes', 'No' ) LIKE '%{$text_search}%' OR `subscriptionNo` LIKE '%{$text_search}%' OR `registeredDate` LIKE '%{$text_search}%' 
                OR `subscriptionStartDate` LIKE '%{$text_search}%' OR `subscriptionAmount` LIKE '%{$text_search}%' OR `nextRenewalDate` LIKE '%{$text_search}%' OR 
                `lastRenewedDate` LIKE '%{$text_search}%' OR `lastAccDate` LIKE '%{$text_search}%' )");
        }

        $sub = $this->db->order_by("CONCAT( company_code, '-', company_name )")->get()->result_array();

        $header = [
            '#', 'Company Name', 'Email ID', 'Phone No', 'Mobile No', 'Country', 'Type', 'Subscription ID', 'Registered Date',
            'Subscription Start Date', 'Subscription Amount', 'Implementation Amount', 'Next Renewal Date', 'Last Renewed Date',
            'Currency', 'Subscription', 'Days Left to Expire', 'Payment Enabled', 'Last Access Date',
        ];

        $det = []; $i = 1;
        foreach ($sub as $row){
            $subscription_status = '';
            switch ($row['isSubscriptionDisabled']){
                case 0: $subscription_status = 'Active'; break;
                case 1: $subscription_status = 'Inactive'; break;
                case 2: $subscription_status = 'On Hold'; break;
                case 3: $subscription_status = 'Expire'; break;
            }

            $days_left = daysLeftForExpire($row['company_id'], $row['isSubscriptionDisabled']);

            $det[] = [
                $i,
                $row['com_name'], $row['company_email'], $row['company_phone'], $row['companyPrintTelephone'], $row['company_country'],
                $row['tyDes'], $row['subscriptionNo'], $row['registeredDate'], $row['subscriptionStartDate'], $row['subAmount'], 
                $row['impAmount'], $row['nextRenewalDate'], $row['lastRenewedDate'], $row['CurrencyCode'], $subscription_status, $days_left,
                $row['paymentEnabled'], $row['lastAccDate'],
            ];
            $i++;
        }

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Company Subscription');
        $styleArray = [
            'font' => ['bold' => true, 'size' => 13, 'name' => 'Calibri']
        ];

        $this->excel->getActiveSheet()->getStyle('A1:S1')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->fromArray($header, null, 'A1');
        $this->excel->getActiveSheet()->fromArray($det, null, 'A2');

        $filename = 'Company Subscription.xls';
        header('Content-Type: application/vnd.ms-excel;charset=utf-16');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    function audit_log(){
        $companiesList = $this->Dashboard_model->loadAllCompanies();
        $tmpData['companiesList'] = $companiesList;
        $data['title'] = 'Audit log';
        $data['main_content'] = 'audit_log_view';
        $data['extra'] = $tmpData;
        $this->load->view('include/template', $data);
    }

    function fetch_audit_report(){
        $from = $this->input->post('from_date');
        $to = $this->input->post('to_date');
        $column_drop = $this->input->post('column_drop');
        $company_drop = $this->input->post('company_drop');
        $userType = current_userType();

        $column_where = (!empty($column_drop))? "AND aud_col.id IN ({$column_drop})": "";
        $company_where = "";
        if(!empty($company_drop)){
            $company_drop = explode(',', $company_drop);
            $company_drop = "'".join("','", $company_drop)."'";
            $company_where = "AND com.company_id IN ({$company_drop})";
        }

        $this->datatables->select("company_id, company_name, Fullname, tableName, columnName, rowID, display_old_val, 
                                 display_new_val, log_time")
            ->from("(SELECT com.company_id AS company_id, CONCAT(company_name, '( ', company_code, ' )') AS company_name, 
                    Fullname, tableName, columnName, rowID, display_old_val, display_new_val, lg_tb.timestamp AS log_time 
                    FROM srp_erp_audit_log AS lg_tb 
                    JOIN srp_erp_company AS com ON com.company_id = lg_tb.companyID 
                    JOIN srp_erp_companyadminusers AS ad_tb ON ad_tb.UserID = lg_tb.userID 
                    JOIN srp_erp_audit_display_columns AS aud_col ON aud_col.tbl_name = lg_tb.tableName 
                    AND aud_col.col_name = lg_tb.columnName 
                    WHERE ad_tb.adminType = {$userType}  AND DATE(lg_tb.`timestamp`) BETWEEN '{$from}' AND '{$to}' 
                    $column_where $company_where 
                    UNION ALL
                    SELECT '' AS company_id, '' company_name, Fullname, tableName, columnName,  
                    rowID, display_old_val, display_new_val, lg_tb.timestamp AS log_time 
                    FROM srp_erp_audit_log AS lg_tb                    
                    JOIN srp_erp_companyadminusers AS ad_tb ON ad_tb.UserID = lg_tb.userID 
                    JOIN srp_erp_audit_display_columns AS aud_col ON aud_col.tbl_name = lg_tb.tableName 
                    AND aud_col.col_name = lg_tb.columnName 
                    WHERE ad_tb.adminType = {$userType}  AND DATE(lg_tb.`timestamp`) BETWEEN '{$from}' AND '{$to}'
                    AND companyID = 0 $column_where ) AS t1");
        $this->datatables->edit_column('tableName', '$1.$2', 'tableName, columnName');
        echo $this->datatables->generate();
    }

    function fetch_company_warehouse(){
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);

        $this->datatables->select("wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation, isActive ");
        $this->datatables->from('srp_erp_warehousemaster')
        ->where('companyID', trim($this->input->post('company_id')))
        ->where('isPosLocation', 1)
        ->add_column('wr_status', '$1', 'warehouse_action(wareHouseAutoID,wareHouseDescription,isActive)');
        echo $this->datatables->generate('json', 'ISO-8859-1');
    }

    function fetch_company_subscription_history(){
        $company_id = trim($this->input->post('company_id'));
        $this->datatables->select("id, Fullname, createdDateTime, comment, subStatus, labelClass");
        $this->datatables->from("subscription_status_history_view AS his")
            ->join('srp_erp_companyadminusers AS us', 'us.UserID=his.createdUserID')
            ->where('his.companyID', $company_id)
            ->add_column('sub_status', '<div align="center"><span class="label label-$1">$2</span></div>', 'labelClass,subStatus');
        echo $this->datatables->generate('json', 'ISO-8859-1');
    }

    function latest_history(){
        $company_id = trim($this->input->post('company_id'));

        $comment = $this->db->query("SELECT `comment` FROM subscription_status_history WHERE companyID = {$company_id} 
                                                ORDER BY id DESC LIMIT 1")->row('comment');

        echo json_encode(['comment'=> $comment]);
    }

    function delete_invoice(){
        $inv_id = $this->input->post('autoID');
        $user_id = current_userID();
        $date_time = date('Y-m-d H:i:s');


        $is_paid = $this->db->select('payAutoID')->where('inv_id', $inv_id)
                        ->get('subscription_invoice_payment_details')->row('payAutoID');

        if($is_paid){
            die(json_encode(['e', 'This invoice has payment details,You can not delete this invoice.']));
        }

        $old_records = $this->db->get_where('subscription_invoice_master', ['invID'=>$inv_id])->row_array();

        $data = [ 'invID', 'subscriptionID', 'invNo', 'invDate', 'invTotal', 'invCur', 'invDecPlace', 'isAmountPaid',
                  'paymentType', 'payRecDate', 'serialNo', 'paypalOrderID', 'paypalPayeeMailID', 'paypalMerchantID',
                  'paypalPaymentsID', 'paypalExchangeRate', 'paypalFee', 'paypalNetAmount', 'paypalPayerID' ];
        $audit_log = [];
        foreach($data as $column){
            $old_val = (!empty($old_records))? $old_records[$column]: '';
            $old_display_val = $old_val;

            switch($column){
                case 'invCur':
                    $old_display_val = $this->db->select('CurrencyCode')->from('srp_erp_currencymaster')->where('currencyID', $old_val)->get()->row('CurrencyCode');
                break;
            }

            $audit_log[] = [
                'tableName' => 'subscription_invoice_master', 'columnName'=> $column, 'old_val'=> $old_val,
                'display_old_val'=> $old_display_val, 'new_val'=> '', 'display_new_val'=> '', 'rowID'=> $inv_id,
                'is_deleted' => 1, 'companyID'=> $old_records['companyID'], 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $old_det_records = $this->db->get_where('subscription_invoice_details', ['invID'=>$inv_id])->row_array();

        $det_data = [ 'autoID', 'invID', 'itemID', 'itemDescription', 'amount', 'amountBeforeDis',
                      'discountPer', 'discountAmount'  ];
        $audit_log = [];
        foreach($det_data as $column){
            $old_val = (!empty($old_det_records))? $old_det_records[$column]: '';
            $old_display_val = $old_val;

            switch($column){
                case 'itemID':
                    $old_display_val = $this->db->select('description')->from('system_invoice_item_type')
                                            ->where('type_id', $old_val)->get()->row('description');
                break;
            }

            $audit_log[] = [
                'tableName' => 'subscription_invoice_details', 'columnName'=> $column, 'old_val'=> $old_val,
                'display_old_val'=> $old_display_val, 'new_val'=> '', 'display_new_val'=> '', 'rowID'=> $old_det_records['autoID'],
                'is_deleted' => 1, 'companyID'=> $old_records['companyID'], 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $this->db->trans_start();

        $this->db->delete('subscription_invoice_master', ['invID'=>$inv_id]);
        $this->db->delete('subscription_invoice_details', ['invID'=>$inv_id]);

        $this->db->insert_batch('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Invoice deleted successfully.']);
        }else{
            echo json_encode(['e', 'Error in invoice delete process']);
        }
    }

    function delete_subscription(){
        $autoID = $this->input->post('autoID');
        $user_id = current_userID();
        $date_time = date('Y-m-d H:i:s');

        $existing_inv = $this->db->get_where('subscription_invoice_master', ['subscriptionID'=>$autoID])->row('invNo');

        if($existing_inv){
            $msg = "There are invoice/s related this subscription record,Please delete the invoice first and try again.<br/>( {$existing_inv} )";
            die(json_encode(['e', $msg]));
        }


        $old_records = $this->db->get_where('companysubscriptionhistory', ['subscriptionID'=>$autoID])->row_array();
        $companyID = $old_records['companyID'];
        $old_nxt = $old_records['isNextSubscriptionGenerated'];

        /**** Check succeeding records ***/
        $succeeding_his = $this->db->query("SELECT count(subscriptionID) cn FROM companysubscriptionhistory 
                                    WHERE subscriptionID > {$autoID} AND companyID = {$companyID}")->row('cn');

        if($succeeding_his > 0){
            die( json_encode(['e', 'please delete succeeding subscriptions before delete this record']));
        }


        $is_first_subscription = $this->db->query("SELECT MIN(subscriptionID) AS min_sub FROM companysubscriptionhistory 
                                            WHERE companyID = {$companyID}")->row('min_sub');

        $data = [ 'subscriptionID', 'subscriptionStartDate', 'nextRenewalDate', 'renewedYN', 'lastRenewedDate',
            'dueDate', 'expiryEmailSent', 'subscriptionAmount', 'isNextSubscriptionGenerated', 'isInvoiceGenerated'];

        if($is_first_subscription == $autoID){
            $data = [];
        }

        $audit_log = [];
        foreach($data as $column){
            $old_val = (!empty($old_records))? $old_records[$column]: '';
            $old_display_val = $old_val;

            switch($column){
                case 'renewedYN':
                case 'expiryEmailSent':
                case 'isNextSubscriptionGenerated':
                case 'isInvoiceGenerated':
                    $old_display_val = ($old_val == 1)? 'Yes': 'No';
                break;
            }

            $audit_log[] = [
                'tableName' => 'companysubscriptionhistory', 'columnName'=> $column, 'old_val'=> $old_val,
                'display_old_val'=> $old_display_val, 'new_val'=> '', 'display_new_val'=> '', 'rowID'=> $autoID,
                'is_deleted' => 1, 'companyID'=> $companyID, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }


        $this->db->trans_start();

        if($is_first_subscription == $autoID){
            /************************************************************************************************************
             * IF this is the first subscription then just update srp_erp_company.isInitialSubscriptionConfirmed column
             * and companysubscriptionhistory.isNextSubscriptionGenerated column to zero
             ************************************************************************************************************/
            $upDate = [
                'isInitialSubscriptionConfirmed'=> 0, 'modifiedPCID' => current_pc(), 'modifiedUserID' => $user_id,
                'modifiedDateTime' => $date_time, 'modifiedUserName' => current_userName(), 'timestamp' => $date_time,
            ];
            $this->db->where('company_id', $companyID)->update('srp_erp_company', $upDate);

            $audit_log[] = [
                'tableName' => 'srp_erp_company', 'columnName'=> 'isInitialSubscriptionConfirmed', 'old_val'=> 1,
                'display_old_val'=> 'Yes', 'new_val'=> 0, 'display_new_val'=> 'No', 'rowID'=> $companyID,
                'is_deleted' => 0, 'companyID'=> $companyID, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];


            $upDate = [
                'isNextSubscriptionGenerated'=> 0, 'modifiedPCID' => current_pc(), 'modifiedUserID' => $user_id,
                'modifiedDateTime' => $date_time, 'modifiedUserName' => current_userName(), 'timestamp' => $date_time,
            ];
            $this->db->where('subscriptionID', $autoID)->update('companysubscriptionhistory', $upDate);

            if($old_nxt == 1){
                $audit_log[] = [
                    'tableName' => 'companysubscriptionhistory', 'columnName'=> 'isNextSubscriptionGenerated', 'old_val'=> 1,
                    'display_old_val'=> 'Yes', 'new_val'=> 0, 'display_new_val'=> 'No', 'rowID'=> $autoID,
                    'is_deleted' => 0, 'companyID'=> $companyID, 'userID'=> $user_id, 'timestamp'=> $date_time,
                ];
            }


        }
        else{ // if not first subscription of this company, delete the record
            $this->db->delete('companysubscriptionhistory', ['subscriptionID'=>$autoID]);

            $last_his = $this->db->query("SELECT MAX(subscriptionID) last_his FROM companysubscriptionhistory 
                                    WHERE companyID = {$companyID}")->row('last_his');

            $upDate = [
                'isNextSubscriptionGenerated'=> 0, 'modifiedPCID' => current_pc(), 'modifiedUserID' => $user_id,
                'modifiedDateTime' => $date_time, 'modifiedUserName' => current_userName(), 'timestamp' => $date_time,
            ];

            $this->db->where( ['subscriptionID'=>$last_his])->update('companysubscriptionhistory', $upDate);

            $audit_log[] = [
                'tableName' => 'companysubscriptionhistory', 'columnName'=> 'isNextSubscriptionGenerated', 'old_val'=> 1,
                'display_old_val'=> 'Yes', 'new_val'=> 0, 'display_new_val'=> 'No', 'rowID'=> $last_his,
                'is_deleted' => 0, 'companyID'=> $companyID, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $this->db->insert_batch('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Subscription deleted successfully.']);
        }else{
            echo json_encode(['e', 'Error in subscription delete process']);
        }
    }

    function generate_subscription(){
        $companyID = $this->input->post('company_id');
        $to_day = date('Y-m-d');

        $nxt_sub = $this->db->query("SELECT sub_his.subscriptionID, sub_his.companyID, sub_his.nextRenewalDate, com_tb.subscriptionAmount,
                                  cur_mas.currencyID, cur_mas.CurrencyCode, cur_mas.DecimalPlaces, com_tb.company_name, companyPrintAddress, company_email
                                  FROM srp_erp_company AS com_tb
                                  JOIN (
                                      SELECT subscriptionID, companyID, nextRenewalDate FROM companysubscriptionhistory
                                      WHERE companyID = {$companyID} AND isNextSubscriptionGenerated = 0
                                  ) AS sub_his ON sub_his.companyID = com_tb.company_id
                                  JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID=com_tb.subscriptionCurrency 
                                  WHERE com_tb.paymentEnabled = 1 AND com_tb.isSubscriptionDisabled = 0")->row_array(); //AND nextRenewalDate <= '{$to_day}'

        if (empty($nxt_sub)) {
            die(json_encode(['e', 'Subscription related detail not found.']));
        }

        $pc = current_pc();
        $user_id = current_userID();
        $user_name = current_userName();
        $date_time = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $old_sub_update = [
            'isNextSubscriptionGenerated' => 1, 'modifiedPCID' => $pc, 'modifiedUserID' => $user_id,
            'modifiedUserName' => $user_name, 'modifiedDateTime' => $date_time, 'timestamp' => $date_time
        ];

        $old_sub_id = $nxt_sub['subscriptionID'];
        $company_id = $nxt_sub['companyID'];
        $nextRenewalDate = $nxt_sub['nextRenewalDate'];
        $sub_startDate = date('Y-m-d', strtotime("$nextRenewalDate"));
        $due_date = date('Y-m-d', strtotime("$nextRenewalDate +14 days"));
        $nextRenewalDate = date('Y-m-d', strtotime("$sub_startDate +1 year"));
        $sub_amount = round($nxt_sub['subscriptionAmount'], $nxt_sub['DecimalPlaces']);

        /******** Update old subscription data ********/
        $this->db->where(['subscriptionID' => $old_sub_id])->update('companysubscriptionhistory', $old_sub_update);

        $sub_history_data = [
            'subscriptionStartDate' => $sub_startDate, 'dueDate' => $due_date, 'nextRenewalDate' => $nextRenewalDate, 'subscriptionAmount' => $sub_amount,
            'isInvoiceGenerated' => 1, 'companyID' => $company_id, 'createdPCID' => $pc, 'createdUserID' => $user_id, 'createdUserName' => $user_name,
            'createdDateTime' => $date_time, 'timestamp' => $date_time
        ];

        $this->db->insert('companysubscriptionhistory', $sub_history_data);
        $sub_id = $this->db->insert_id();


        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            echo json_encode(['s', 'Subscription successfully created', 'sub_id' => $sub_id]);
        } else {
            echo json_encode(['e', 'Error in subscription create process.']);
        }
    }

    function user_password_rest()
    {
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('user_id', 'User ID', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $companyID = $this->input->post('company_id');
        $user_id = $this->input->post('user_id');
        $password = md5($this->input->post('password'));
        $password_str = $this->input->post('password');


        $is_QHSE_integrated = is_QHSE_integrated($companyID);
        $this->load->database( $this->get_db_array(), false, true );

        if($is_QHSE_integrated == 'Y') {
            $userData = $this->db->query("SELECT Ename2 AS empName,  UserName, integratedUserID
                                FROM srp_employeesdetails AS empTB
                                JOIN srp_erp_system_integration_user AS usr ON usr.empID = empTB.EIdNo
                                WHERE empTB.EIdNo = {$user_id} AND integratedSystem = 'QHSE'")->row_array();

            if(!empty($userData)){
                $url = 'api/v1/user/update/'.$userData['integratedUserID'];

                $QHSE_user = [
                    'name'=> $userData['empName'], 'email' => $userData['UserName'], 'password' => $password_str,
                    'password_confirmation' => $password_str, 'activeYN' => 1
                ];

                //Update user in QHSE DB
                $res_data = $this->Dashboard_model->QHSE_api_requests($companyID, $QHSE_user, $url, $is_put=true);

                if($res_data['status'] == 'e'){
                    $error_msg = 'QHSE Error - '.$res_data['message'];
                    die(json_encode(['e', $error_msg, 'http_code'=> $res_data['http_code']]));
                }
            }
        }

        /*** Update company DB ***/
        $this->db->where([
            'EIdNo' => $user_id, 'Erp_companyID' => $companyID
        ])->update('srp_employeesdetails', ['Password'=>$password]);


        /*** Update central DB ***/
        $db2 = $this->load->database('db2', TRUE);
        $db2->where([
            'empID' => $user_id, 'companyID' => $companyID
        ])->update('user', ['Password'=>$password]);

        echo json_encode(['s', 'Password updated successfully.']);
    }

    function reset_login_attempts(){
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('userID', 'User ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            http_response_code( 500 );
            die( json_encode(['e', validation_errors()]) );
        }

        $companyID = $this->input->post('company_id');
        $user_id = $this->input->post('userID');
        $type = $this->input->post('type');

        $msg = ($type == 4)? 'Login inactivated successfully.': 'Login activated successfully.';
        $isActive = ($type == 4)? 0: 1;

        $this->load->database( $this->get_db_array(), false, true );
        /*** Update company DB ***/
        $this->db->where([
            'EIdNo' => $user_id, 'Erp_companyID' => $companyID
        ])->update('srp_employeesdetails', ['NoOfLoginAttempt'=> $type, 'isActive'=> $isActive]);
        
        echo json_encode(['s', $msg]);
    }

    function update_single_subscription_amount(){
        $sub_id = $this->input->post('pk');
        $amount = $this->input->post('value');
        $inv_type = $this->input->post('inv_type');

        if(empty($sub_id)){
            http_response_code( 500 );
            die('Subscription ID is required');
        }

        if($amount == 0 || empty($amount) || !is_numeric($amount)){
            http_response_code( 500 );
            die('Amount field is not valid');
        }

        $date_time = date('Y-m-d H:i:s');
        $pc = current_pc(); $user_id = current_userID(); $user_name = current_userName();

        $invNo = $this->db->query("SELECT invNo FROM subscription_invoice_master AS mas 
                                   JOIN subscription_invoice_details AS det ON mas.invID = det.invID  
                                   WHERE subscriptionID = {$sub_id} AND det.itemID = {$inv_type}")->row('invNo');

        if(!empty($invNo)){
            http_response_code( 500 );
            die("Already invoice generated for this subscription/ implementation ( {$invNo} )");
        }

        $amount_column = ($inv_type == 1)? 'his.subscriptionAmount': 'com_tb.implementationAmount';
        $old_data = $this->db->query("SELECT {$amount_column} AS amount, companyID, curMas.DecimalPlaces AS dPlace
                                      FROM companysubscriptionhistory AS his
                                      JOIN srp_erp_company AS com_tb ON com_tb.company_id = his.companyID
                                      JOIN srp_erp_currencymaster AS curMas ON curMas.currencyID = com_tb.subscriptionCurrency 
                                      WHERE subscriptionID = {$sub_id} ")->row_array();
        $company_id = $old_data['companyID'];
        $dPlace = $old_data['dPlace'];
        $dPlace = (empty($dPlace))? 2: $dPlace;
        $amount = round($amount, $dPlace);

        $amount_column = ($inv_type == 1)? 'subscriptionAmount': 'implementationAmount';
        $table = ($inv_type == 1)? 'companysubscriptionhistory': 'srp_erp_company';
        $rowID = ($inv_type == 1)? $sub_id: $company_id;
        $data = [
            $amount_column=> $amount, 'modifiedPCID'=> $pc, 'modifiedUserID'=> $user_id,
            'modifiedDateTime'=> $date_time, 'modifiedUserName'=> $user_name, 'timestamp'=> $date_time,
        ];

        $this->db->trans_start();

        $old_val = $old_data['amount'];
        if($old_val != $amount){
            $audit_log = [
                'tableName' => $table, 'columnName'=> $amount_column, 'old_val'=> $old_val,
                'display_old_val'=> $old_val, 'new_val'=> $amount, 'display_new_val'=> $amount,
                'rowID'=> $rowID, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];

            $this->db->insert('srp_erp_audit_log', $audit_log);
        }

        $where = ($inv_type == 1)? ['subscriptionID'=> $sub_id]: ['company_id'=> $company_id];
        $this->db->where($where)->update($table, $data);

        if($inv_type == 1){
            $max_id = $this->db->query("SELECT MAX(subscriptionID) max_id FROM companysubscriptionhistory 
                                    WHERE companyID= {$company_id}")->row('max_id');

            if($max_id == $sub_id){
                if($old_val != $amount){
                    $audit_log = [
                        'tableName' => 'srp_erp_company', 'columnName'=> 'subscriptionAmount', 'old_val'=> $old_val,
                        'display_old_val'=> $old_val, 'new_val'=> $amount, 'display_new_val'=> $amount,
                        'rowID'=> $company_id, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
                    ];

                    $this->db->insert('srp_erp_audit_log', $audit_log);

                    $this->db->where(['company_id'=> $company_id])->update('srp_erp_company', ['subscriptionAmount'=>$amount]);
                }
            }
        }

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode([
                'id'=> $this->input->post('tb_id'),
                'amount'=> number_format($amount, $dPlace)
            ]);
        }else{
            http_response_code( 500 );
            $msg = ($inv_type == 1)? 'subscription': 'implementation';
            echo "Error in {$msg} amount update process.";
        }
    }

    function add_payment_details(){
        $this->form_validation->set_rules('com_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('inv_id', 'Invoice ID', 'trim|required');
        $this->form_validation->set_rules('pay_type', 'Payment type', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('pay_date', 'Pay date', 'trim|required');
        $this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $company_id = $this->input->post('com_id');
        $inv_id = $this->input->post('inv_id');
        $pay_type = $this->input->post('pay_type');
        $amount = $this->input->post('amount');
        $pay_date = $this->input->post('pay_date');
        $narration = $this->input->post('narration');
    
        $invData = $this->db->select('invTotal, invDecPlace')->where('invID', $inv_id)
                        ->get('subscription_invoice_master')->row_array();
        
        if(empty($invData)){
            die( json_encode(['e', 'Invoice master not found']) );
        }
        
        $dPlace = $invData['invDecPlace'];
        $invTotal = round($invData['invTotal'], $dPlace);
        $amount = round($amount, $dPlace);

        $paidSum = $this->db->select('SUM(amount) AS paidSum')->where('inv_id', $inv_id)
                        ->get('subscription_invoice_payment_details')->row('paidSum');
        
        if($invTotal < ($paidSum + $amount)) {
            $inv_balance = number_format( ($invTotal - $paidSum), $dPlace);
            die( json_encode(['e', "You can not pay more than the invoice balance amount <b>[ {$inv_balance} ]</b>."]) );         
        }
      
        $data = [
            'inv_id'=> $inv_id, 'pay_type'=> $pay_type, 'amount'=> $amount,
            'pay_date'=> $pay_date, 'narration'=> $narration
        ];

        $pc = current_pc(); $user_id = current_userID(); $date_time = date('Y-m-d H:i:s');
        $auto_id = null; $audit_log = [];

        foreach($data as $column=>$new_val){
            $new_val_display = $new_val;

            if($column == 'pay_type'){
                $new_val_display = $this->db->get_where('system_payment_types', ['id'=> $new_val])
                                        ->row('pay_description');                
            }

            $audit_log[] = [
                'tableName' => 'subscription_invoice_payment_details', 'columnName'=> $column, 'old_val'=> '',
                'display_old_val'=> '', 'new_val'=> $new_val, 'display_new_val'=> $new_val_display,
                'rowID'=> &$auto_id, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $def = [
            'companyID'=> $company_id, 'createdPCID'=> $pc, 'createdUserID'=> $user_id,
            'createdDateTime'=> $date_time, 'timestamp'=> $date_time
        ];

        $data = array_merge($data, $def);

        $this->db->trans_start();

        $this->db->insert('subscription_invoice_payment_details', $data);
        $auto_id = $this->db->insert_id();

        $this->db->insert_batch('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            $view = $this->invoice_payment_details_view($inv_id, $dPlace);
            echo json_encode(['s', 'Invoice payment details added successfully.', 'pay_det_view'=> $view]);
        }else{
            echo json_encode(['e', 'Error in invoice payment details adding process.']);
        }
    }

    function invoice_payment_details_view($inv_id, $dPlace){
        $is_view_only = $this->input->post('is_view_only');
        $cols_pan = ($is_view_only)? 5: 6;        

        $data = $this->db->select("payAutoID,narration,pay_date,amount,pt.pay_description, invPay.pay_type")
                ->join('system_payment_types AS pt', 'pt.id=invPay.pay_type', 'left')
                ->where(['inv_id'=> $inv_id])
                ->get('subscription_invoice_payment_details AS invPay')
                ->result_array();

        if(empty($data)){
            return '<tr><td colspan="'.$cols_pan.'"  style="text-align: center">No records found</td></tr>';
        }
        
        $i = 1; $paid_tot = 0; $view = '';
        foreach ($data as $row){
            $paid_tot += round($row['amount'], $dPlace);
            $view .= '<tr>
                         <td>'.$i.'</td>                        
                         <td>'.$row['pay_description'].'</td>                        
                         <td>'.$row['narration'].'</td>                        
                         <td>'.$row['pay_date'].'</td>                        
                         <td align="right">'.number_format($row['amount'], $dPlace).'</td>';

            if(!$is_view_only){
                $str = '<span rel="tooltip" class="glyphicon glyphicon-trash delete-icon" 
                            onclick="delete_invoice_payment_det('.$row['payAutoID'].')" title="Delete">
                        </span>';
                
                //delete option not applicable for pay-pal and debit,credit cards
                $str = (in_array($row['pay_type'], [2,4,5]))? '': $str;

                $view .= '<td align="right">'.$str.'</td>';       
            }

            $view .= '</tr>';

            $i++;
        }

        $str = (!$is_view_only)? '<td></td>': '';
        $view .= '<tr>
                    <td colspan="4" align="right"><b>Total Payment</b></td>
                    <td align="right"><b>'.number_format($paid_tot, $dPlace).'</b></td>
                    '.$str.'
                  </tr>';


        return $view;
    }

    function delete_invoice_payment_det(){
        $id = $this->input->post('id');

        $old_records = $this->db->query("SELECT pay_type, inv_id, narration, pay_date, amount, companyID
                           FROM subscription_invoice_payment_details WHERE payAutoID = {$id}")->row_array();

        if( !in_array($old_records['pay_type'], [1,3,6]) ){
            die( json_encode(['e', 'You can not delete this payment.<br/>Only the manual entires can be delete.']) );
        }

        $companyID = $old_records['companyID'];
        unset($old_records['companyID']);
        $user_id = current_userID(); $date_time = date('Y-m-d H:i:s');
        $audit_log = [];

        foreach($old_records as $column=>$old_val){
            $old_display_val = $old_val;

            if($column == 'pay_type'){
                $old_display_val = $this->db->get_where('system_payment_types', ['id'=> $old_val])
                                        ->row('pay_description');                 
            }

            $audit_log[] = [
                'tableName' => 'subscription_invoice_payment_details', 'columnName'=> $column, 'old_val'=> $old_val,
                'display_old_val'=> $old_display_val, 'new_val'=> '', 'display_new_val'=> '', 'rowID'=> $id,
                'is_deleted' => 1, 'companyID'=> $companyID, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $this->db->trans_start();
        if(!empty($audit_log)){
            $this->db->insert_batch('srp_erp_audit_log', $audit_log);
        }

        $this->db->where('payAutoID', $id)->delete('subscription_invoice_payment_details');

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            $inv_id = $old_records['inv_id'];
            $dPlace = $this->db->get_where('subscription_invoice_master', ['invID'=>$inv_id])->row('invDecPlace');
            $view = $this->invoice_payment_details_view($inv_id, $dPlace);
            echo json_encode(['s', 'Payment detail deleted successfully.', 'pay_det_view'=> $view]);
        }else{
            echo json_encode(['e', 'Error in payment detail delete process.']);
        }
    }

    function product_master(){
        $companiesList = $this->Dashboard_model->loadAllCompanies();
        $tmpData['companiesList'] = $companiesList;
        $data['title'] = 'Product Master';
        $data['main_content'] = 'product_master_view';
        $data['extra'] = $tmpData;
        $this->load->view('include/template', $data);
    }

    function new_product(){
        $this->form_validation->set_rules('description', 'Description', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $product_id = $this->input->post('product_id');
        $description = trim($this->input->post('description'));

        $this->db->select('description')->from('product_master')->where('description', $description);
        if(!empty($product_id)){
            $this->db->where("id <> {$product_id}");
        }
        $isExists = $this->db->get()->row('description');

        if(!empty($isExists)){
            die( json_encode(['e', 'This description already exist.']) );
        }

        $auto_id = null;
        $old_val = '';
        if(!empty($product_id)){
            $old_val = $this->db->get_where('product_master', ['id'=>$product_id])->row('description');
            $auto_id = $product_id;
        }

        $pc = current_pc(); $user_id = current_userID(); $date_time = date('Y-m-d H:i:s');

        $audit_log = [
            'tableName' => 'product_master', 'columnName'=> 'description', 'old_val'=> $old_val,
            'display_old_val'=> $old_val, 'new_val'=> $description, 'display_new_val'=> $description,
            'rowID'=> &$auto_id, 'companyID'=> '', 'userID'=> $user_id, 'timestamp'=> $date_time,
        ];

        $this->db->trans_start();

        if(empty($product_id)){
            $data = [
                'description'=> $description, 'createdPCID'=> $pc, 'createdUserID'=> $user_id,
                'createdDateTime'=> $date_time, 'timestamp'=> $date_time
            ];

            $this->db->insert('product_master', $data);
            $auto_id = $this->db->insert_id();
        }
        else{
            $data = [
                'description'=> $description, 'modifiedPCID'=> $pc, 'modifiedUserID'=> $user_id,
                'modifiedDateTime'=> $date_time, 'timestamp'=> $date_time
            ];

            $this->db->where('id', $product_id)->update('product_master', $data);
        }

        $this->db->insert('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Product create successfully.']);
        }else{
            echo json_encode(['e', 'Error in product create process.']);
        }

    }

    function fetch_product_master(){
        $action = '<div align="right"><span onclick="setup_product($1, \'$2\')"><i class="fa fa-cogs" rel="tooltip"></i></span>';
        $action .= ' &nbsp; | &nbsp; <span onclick="edit_product($1, \'$2\')"><i class="fa fa-pencil" style="color:#2f6dbd;" rel="tooltip"></i></span>';
        $action .= ' &nbsp; | &nbsp; <span onclick="confirm_delete_product($1, \'$2\')" class="delete-icon"><i class="fa fa-trash" rel="tooltip"></i></span>';
        $action .= '</div>';

        $this->datatables->select('id,description')
            ->from('product_master')
            ->edit_column('action', $action, 'id,description');
        echo $this->datatables->generate();
    }

    function delete_product(){
        $product_id = $this->input->post('id');
        $records = $this->db->get_where('product_company', ['product_id'=> $product_id])->row('id');

        if(!empty($records)){
            die( json_encode(['e', 'Companies assigned to this products.You can not delete this product.']) );
        }  
        
        if($this->input->post('verify') == 0){
            $records = $this->db->limit(1)->get_where('product_navigations', ['productID'=> $product_id])->row('id');

            if(!empty($records)){
                die( json_encode([
                    'w', 'Navigation setup is done for this product.Are sure you want to delete this product?', 'id'=> $product_id
                ]));
            } 
        }
         

        $old_val = $this->db->get_where('product_master', ['id'=>$product_id])->row('description');

        $user_id = current_userID(); $date_time = date('Y-m-d H:i:s');

        $audit_log = [
            'tableName' => 'product_master', 'columnName'=> 'description', 'old_val'=> $old_val,
            'display_old_val'=> $old_val, 'new_val'=> '', 'display_new_val'=> '', 'is_deleted' => 1,
            'rowID'=> $product_id, 'companyID'=> '', 'userID'=> $user_id, 'timestamp'=> $date_time,
        ];

        $this->db->trans_start();

        $this->db->where('id', $product_id)->delete('product_master');

        $this->db->insert('srp_erp_audit_log', $audit_log);

        $this->db->where('productID', $product_id)->delete('product_navigations');

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Product deleted successfully.']);
        }else{
            echo json_encode(['e', 'Error in product delete process.']);
        }
    }

    function get_companies_for_product(){
        $userType = current_userType();
        
        $companies = $this->db->query("SELECT company_id, company_code, company_name  
                            FROM srp_erp_company WHERE adminType = {$userType}
                            AND company_id NOT IN (
                                SELECT company_id FROM product_company WHERE product_id > 0
                            )")->result_array();

        echo json_encode(['s', 'company'=> $companies]);
    }

    function assign_product(){
        $this->form_validation->set_rules('product_id', 'Product ID', 'trim|required');
        $this->form_validation->set_rules('company_drop[]', 'Company', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $product_id = $this->input->post('product_id');
        $company_drop = $this->input->post('company_drop');

        $exist = $this->db->select("CONCAT(company_name, ' [ ', company_code, ' ]') AS com_name")
                    ->where_in('prTb.company_id', $company_drop)
                    ->join('srp_erp_company AS com', 'company_id')
                    ->get('product_company AS prTb')
                    ->result_array();
        if($exist){
            $companies = '<br/> - &nbsp; '. implode('<br/> - &nbsp; ', array_column($exist, 'com_name'));
            die( json_encode(['e', 'Following companies already assign to another product'.$companies]) );
        }

        $product_name = $this->db->get_where('product_master', ['id'=>$product_id])->row('description');

        $pc = current_pc(); $user_id = current_userID(); $date_time = date('Y-m-d H:i:s');
        $data = []; $audit_log = [];
        foreach ($company_drop as $company){
            $data[] = [
                'product_id'=> $product_id, 'company_id'=> $company, 'createdPCID'=> $pc,
                'createdUserID'=> $user_id, 'createdDateTime'=> $date_time, 'timestamp'=> $date_time
            ];

            $audit_log[] = [
                'tableName' => 'product_company', 'columnName'=> 'product_id', 'old_val'=> '',
                'display_old_val'=> '', 'new_val'=> $product_id, 'display_new_val'=> $product_name,
                'rowID'=> 0, 'companyID'=> $company, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $this->db->trans_start();

        $this->db->insert_batch('product_company', $data);
        $this->db->insert_batch('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Product successfully assigned to company.']);
        }else{
            echo json_encode(['e', 'Error in product assigned process.']);
        }
    }

    function fetch_product_company(){
        $action = '<div align="right"> ';
        //$action .= '<span onclick="remove_company($1)" class="delete-icon"><i class="fa fa-trash" rel="tooltip"></i></span>';
        $action .= '</div>';

        $this->datatables->select('id,company_code, company_name')
            ->from('product_company AS pr')
            ->join('srp_erp_company AS com', 'com.company_id = pr.company_id')
            ->where('pr.product_id', $this->input->post('pr_id'))
            ->edit_column('action', $action, 'id,');
        echo $this->datatables->generate();
    }

    function remove_company_from_product(){
        $id = $this->input->post('id');
        $user_id = current_userID(); $date_time = date('Y-m-d H:i:s');

        $product_data = $this->db->query("SELECT description, company_id, product_id FROM product_master                        
                                    JOIN product_company ON product_company.product_id = product_master.id 
                                    WHERE product_company.id = {$id} ")->row_array();

        $audit_log = [
            'tableName' => 'product_company', 'columnName'=> 'product_id', 'old_val'=> $product_data['product_id'],
            'display_old_val'=> $product_data['description'], 'new_val'=> '', 'display_new_val'=> '', 'is_deleted' => 1,
            'rowID'=> $id, 'companyID'=> $product_data['company_id'], 'userID'=> $user_id, 'timestamp'=> $date_time,
        ];

        $this->db->trans_start();

        $this->db->where('id', $id)->delete('product_company');
        $this->db->insert('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Company successfully removed.']);
        }else{
            echo json_encode(['e', 'Error in company removing process.']);
        }
    }

    function build_ad_hoc_invoice(){
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $companyID = $this->input->post('company_id');

        $master_data = $this->db->query("SELECT com_tb.company_id,com_tb.subscriptionCurrency AS currencyID, cur_mas.CurrencyCode,  
                                  com_tb.company_name, companyPrintAddress, company_email, cur_mas.DecimalPlaces AS invDecPlace
                                  FROM srp_erp_company AS com_tb                                   
                                  LEFT JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID=com_tb.subscriptionCurrency
                                  WHERE com_tb.company_id = {$companyID}")->row_array();

        if( empty($master_data) ){
            die( json_encode(['e', 'Company details not found']) );
        }

        if( empty($master_data['currencyID']) ){
            die( json_encode(['e', 'Subscription currency not configured.']) );
        }

        $date_time = date('Y-m-d H:i:s');
        $invNo = $this->Dashboard_model->generate_subscription_inv_no();

        $master_data['invNo'] = $invNo;
        $master_data['invDate'] = $date_time;


        $inv_data['toEmail'] = $master_data['company_email'];
        $inv_data['mas_data'] = $master_data;

        $data['company_id'] = $companyID;
        $data['inv_data'] = $inv_data;
        $data['view_type'] = 'E';

        $built_view = $this->load->view('ad-hoc-invoice-view', $data, true);

        echo json_encode(['s', 'built_view'=> $built_view]);
    }

    function generate_ad_hoc_invoice(){
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('inv_date', 'Invoice date', 'trim|required');
        $this->form_validation->set_rules('description[]', 'description', 'trim|required');
        $this->form_validation->set_rules('amount[]', 'amount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $company_id = $this->input->post('company_id');
        $inv_date = $this->input->post('inv_date');
        $description = $this->input->post('description');
        $amount = $this->input->post('amount');
        $discountPer = $this->input->post('discountPer');
        $discountAmount = $this->input->post('discountAmount');


        $this->db->trans_start();

        $company_data = $this->db->query("SELECT com_tb.subscriptionCurrency,  srp_erp_currencymaster.CurrencyCode,  
                                 srp_erp_currencymaster.DecimalPlaces, com_tb.company_name, companyPrintAddress, company_email
                                 FROM srp_erp_company AS com_tb 
                                 JOIN srp_erp_currencymaster ON srp_erp_currencymaster.currencyID = com_tb.subscriptionCurrency
                                 WHERE com_tb.company_id={$company_id}")->row_array();


        $currency = $company_data['subscriptionCurrency'];
        $dPlace = $company_data['DecimalPlaces'];
        $date_time = date('Y-m-d H:i:s');
        $pc = current_pc(); $user_id = current_userID();

        $invNoData = $this->Dashboard_model->generate_subscription_inv_no(1);
        $serialNo = $invNoData['serialNo'];
        $invNo = $invNoData['inv_no'];
        $inv_id = null;

        $master_data = [
            'subscriptionID'=> 0, 'invNo'=> $invNo, 'invDate'=>$inv_date, 'invCur'=> $currency,
            'invDecPlace'=> $dPlace, 'serialNo'=> $serialNo, 'invTotal'=> 0, 'companyID'=> $company_id
        ];

        $audit_log = []; $inv_total_arr_key = null;
        foreach($master_data as $column=>$new_val){
            if($column == 'invTotal'){
                $inv_total_arr_key = count($audit_log);
            }

            $audit_log[] = [
                'tableName' => 'subscription_invoice_master', 'columnName'=> $column, 'old_val'=> '',
                'display_old_val'=> '', 'new_val'=> $new_val, 'display_new_val'=> $new_val,
                'rowID'=> &$inv_id, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }

        $def = [
            'createdPCID'=> $pc, 'createdUserID'=> $user_id, 'createdDateTime'=> $date_time, 'timestamp'=> $date_time
        ];

        $master_data = array_merge($master_data, $def);

        $this->db->insert('subscription_invoice_master', $master_data);
        $inv_id = $this->db->insert_id();

        $err_msg = ''; $inv_total = 0;
        foreach ($description as $key=>$row){
            $this_amount = round($amount[$key], $dPlace);
            $this_disAmount = round($discountAmount[$key], $dPlace);
            $this_disPer = $discountPer[$key];

            $line = $key + 1;
            if($this_disPer > 100){
                $err_msg .= 'Discount percentage can not be greater than 100. on line no '.$line.' <br/>';
                continue;
            }

            if($this_amount <= 0){
                $err_msg .= 'Invoicing amount not valid. on line no '.$line.' <br/>';
                continue;
            }

            if($this_disAmount > $this_amount){
                $err_msg .= 'Discount amount can not be greater than '.number_format($this_amount, $dPlace).'. on line no '.$line.'<br/>';
                continue;
            }

            $sub_total = $this_amount;
            if($discountAmount != ''){
                $sub_total = round(($this_amount - $this_disAmount), $dPlace);
            }

            $inv_total += $sub_total;

            $detail = [
                'invID'=> $inv_id, 'itemID'=> 0, 'itemDescription'=> $row, 'amountBeforeDis'=> $this_amount,
                'discountAmount'=> $this_disAmount, 'discountPer'=> $this_disPer, 'amount'=> $sub_total, 'companyID'=> $company_id,
            ];

            $detail_id = null;
            foreach($detail as $column=>$new_val){
                $new_val_dis = $new_val;

                $audit_log[] = [
                    'tableName' => 'subscription_invoice_details', 'columnName'=> $column, 'old_val'=> '',
                    'display_old_val'=> '', 'new_val'=> $new_val, 'display_new_val'=> $new_val_dis,
                    'rowID'=> &$detail_id, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
                ];
            }

            $detail = array_merge($detail, $def);

            $this->db->insert('subscription_invoice_details', $detail);
            $detail_id = $this->db->insert_id();
        }

        if($err_msg != ''){
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in invoice generation<br/>'.$err_msg]);
        }

        //Update invoice total
        $this->db->where('invID', $inv_id)->update('subscription_invoice_master', ['invTotal'=> $inv_total]);
        $audit_log[$inv_total_arr_key]['new_val'] = $inv_total;
        $audit_log[$inv_total_arr_key]['display_new_val'] = $inv_total;


        $this->db->insert_batch('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in invoice generation']);
        }
        else {
            $this->db->trans_commit();
            die( json_encode(['s', 'Ad hoc invoice generated successfully.', 'company_id'=>$company_id]) );
        }
    }

    function fetch_company_product(){
        $action = '<div align="right"> ';
        $action .= '<span onclick="remove_product_from_company($1)" class="delete-icon"><i class="fa fa-trash" rel="tooltip"></i></span>';
        $action .= '</div>';

        $this->datatables->select('pr_com.id AS id, pr_mas.description AS description')
            ->from('product_company AS pr_com')
            ->join('product_master AS pr_mas', 'pr_mas.id = pr_com.product_id')
            ->where('pr_com.company_id', $this->input->post('company_id'))
            ->edit_column('action', $action, 'id,');
        echo $this->datatables->generate();
    }

    function get_product_for_company(){
        $company_id = $this->input->post('company_id');
        $products = $this->db->query("SELECT id, description  
                            FROM product_master WHERE id NOT IN (
                                SELECT product_id FROM product_company WHERE company_id = {$company_id}
                            )")->result_array();

        echo json_encode(['s', 'products'=> $products]);
    }

    function assign_product_to_company(){
        $this->form_validation->set_rules('products[]', 'Product ID', 'trim|required');
        $this->form_validation->set_rules('company_id', 'Company', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $products = $this->input->post('products');
        $company_id = $this->input->post('company_id');

        $pc = current_pc(); $user_id = current_userID(); $date_time = date('Y-m-d H:i:s');
        $data = []; $audit_log = [];
        foreach ($products as $product_id){
            $data[] = [
                'product_id'=> $product_id, 'company_id'=> $company_id, 'createdPCID'=> $pc,
                'createdUserID'=> $user_id, 'createdDateTime'=> $date_time, 'timestamp'=> $date_time
            ];

            $product_name = $this->db->get_where('product_master', ['id'=>$product_id])->row('description');

            $audit_log[] = [
                'tableName' => 'product_company', 'columnName'=> 'product_id', 'old_val'=> '',
                'display_old_val'=> '', 'new_val'=> $product_id, 'display_new_val'=> $product_name,
                'rowID'=> 0, 'companyID'=> $company_id, 'userID'=> $user_id, 'timestamp'=> $date_time,
            ];
        }


        $this->db->trans_start();

        $this->db->insert_batch('product_company', $data);
        $this->db->insert_batch('srp_erp_audit_log', $audit_log);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Product successfully assigned to company.']);
        }else{
            echo json_encode(['e', 'Error in product assigned process.']);
        }
    }

    function change_warehouse_status(){
        $this->form_validation->set_rules('warehouseID', 'Warehouse ID', 'trim|required');
        $this->form_validation->set_rules('company_id', 'Company', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $warehouseID = $this->input->post('warehouseID');
        $company_id = $this->input->post('company_id');
        $status = $this->input->post('status');
        $date_time = date('Y-m-d H:i:s');

        $data = [
            'isActive'=> $status, 'modifiedPCID'=> current_pc(), 'modifiedUserID'=> current_userID(),
            'modifiedDateTime'=> $date_time, 'modifiedUserName'=> current_userName(), 'timestamp'=> $date_time
        ];

        $this->load->database($this->get_db_array(), FALSE, TRUE);

        $this->db->trans_start();

        $this->db->where('wareHouseAutoID', $warehouseID)->where('companyID', $company_id)
                    ->update('srp_erp_warehousemaster', $data);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Status changed successfully.']);
        }else{
            echo json_encode(['e', 'Error in status change process.']);
        }
    }

    function create_outlet(){
        $this->form_validation->set_rules('pos_type', 'POS Type', 'trim|required');
        $this->form_validation->set_rules('pos_segment', 'segment', 'trim|required');

        $pos_type = trim($this->input->post('pos_type'));
        if($pos_type == 0){
            $this->form_validation->set_rules('posTemplateID', 'Template ID', 'trim|required');
        }

        $this->form_validation->set_rules('outlet_code', 'Outlet Code', 'trim|required');
        $this->form_validation->set_rules('outlet_name', 'Outlet Name', 'trim|required');
        $this->form_validation->set_rules('outlet_location', 'Outlet Location', 'trim|required');
        $this->form_validation->set_rules('company_id', 'Company', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
            return;
        }

        $segment = trim($this->input->post('pos_segment'));
        $templateID = trim($this->input->post('posTemplateID'));
        $templateID = ($pos_type == 0)? $templateID: null;
        $industryID = ($pos_type == 0)? 1: null;
        $_code = trim($this->input->post('outlet_code'));
        $_name = trim($this->input->post('outlet_name'));
        $company_id = $this->input->post('company_id');

        $this->load->database($this->get_db_array(), FALSE, TRUE);

        $is_exists = $this->db->select('wareHouseCode, wareHouseDescription')->from('srp_erp_warehousemaster')
            ->where("companyID={$company_id} AND (wareHouseCode='{$_code}' OR wareHouseDescription='{$_name}')")
            ->get()->row_array();

        if(!empty($is_exists)){
            $msg = '';
            if($is_exists['wareHouseCode'] == \strtolower($_code)){
                $msg = 'Outlet code is already exist.<br/>';
            }

            if($is_exists['wareHouseDescription'] == \strtolower($_name)){
                $msg .= 'Outlet name is already exist.<br/>';
            }
            echo json_encode(['e', $msg]);
            return;
        }

        $location = $this->input->post('outlet_location');
        $_address = $this->input->post('outlet_address');
        $_tel = $this->input->post('outlet_tel');
        $foot_note = $this->input->post('foot_note');
        $date_time = date('Y-m-d H:i:s');

        $company_code = $this->db->get_where('srp_erp_company', ['company_id'=> $company_id])->row('company_code');

        $data = [
            'wareHouseCode'=> $_code, 'wareHouseDescription'=> $_name, 'wareHouseLocation'=> $location,
            'warehouseAddress'=> $_address, 'pos_footNote'=> $foot_note, 'warehouseTel'=> $_tel,
            'createdPCID'=> current_pc(), 'createdUserID'=> current_userID(), 'createdDateTime'=> $date_time,
            'createdUserName'=> current_userName(), 'companyID'=> $company_id, 'companyCode'=> $company_code,
            'isPosLocation'=> 1, 'isActive'=> 1, 'timestamp'=> $date_time
        ];

        $this->db->trans_start();

        $this->db->insert('srp_erp_warehousemaster', $data);
        $id = $this->db->insert_id();

        $segment_code = $this->db->get_where('srp_erp_segment', ['segmentID'=> $segment])->row('segmentCode');

        $pos_segment_data = [
            'wareHouseAutoID'=> $id, 'industrytypeID'=> $industryID, 'posTemplateID'=> $templateID,
            'companyID'=> $company_id, 'companyCode'=> $company_code, 'segmentID'=> $segment, 'segmentCode'=> $segment_code,
            'isGeneralPOS'=> $pos_type, 'isActive'=> -1, 'createdPCID'=> current_pc(), 'createdUserID' => current_userID(),
            'createdDateTime'=> $date_time, 'createdUserName'=> current_userName(), 'timeStamp'=> $date_time
        ];
        $this->db->insert('srp_erp_pos_segmentconfig', $pos_segment_data);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Outlet created successfully.']);
        }else{
            echo json_encode(['e', 'Error in process.']);
        }

    }

    function get_pos_template_master_drop(){
        $this->load->database($this->get_db_array(), FALSE, TRUE);

        $this->db->select("*");
        $this->db->from('srp_erp_pos_templatemaster');
        $result_template = $this->db->get()->result_array();

        $this->db->select("segmentID, CONCAT(segmentCode,' - ',description) AS description");
        $this->db->from('srp_erp_segment')->where('companyID', $this->input->post('company_id'));
        $result_segment = $this->db->get()->result_array();

        echo json_encode(['s', 'drop_template'=> $result_template, 'drop_segment'=> $result_segment]);
    }
    function fetch_companydetail()
    {

        $colnames = ($_GET['colnames']);
        $updadate_colname = ($_GET['updadate_colname']);
        $companyID =  ($_GET['companyID']);
        $CI =& get_instance();
        $db2 = $CI->load->database('db2', TRUE);
        $db2->select('*');
        $db2->where('host is NOT NULL', NULL, FALSE);
        $db2->where('db_username is NOT NULL', NULL, FALSE);
        //$db2->where('db_password is NOT NULL', NULL, FALSE);
        $db2->where('db_name is NOT NULL', NULL, FALSE);
        $db2->where_in('company_id',array($companyID));
        $companyInfo = $db2->get("srp_erp_company")->result_array();
        foreach ($companyInfo as $val) {

            $config['hostname'] = trim($this->encryption->decrypt($val["host"]));
            $config['username'] = trim($this->encryption->decrypt($val["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($val["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($val["db_name"]));
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = (ENVIRONMENT !== 'production');
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = TRUE;

            echo $val['company_name'] . ' - ' .$config['database'];

            $this->load->database($config, FALSE, TRUE);
            $employeesdetail = $this->db->query("select EIdNo,$colnames,Erp_companyID from srp_employeesdetails where Erp_companyID = {$val['company_id']} ")->result_array();

            $this->db2 = $this->load->database('db2', TRUE);
            foreach ($employeesdetail as $updateval)
            {

                $data[$updadate_colname] = $updateval[$colnames];
                $this->db2->where('empID', $updateval['EIdNo']);
                $this->db2->where('companyID',$updateval['Erp_companyID']);
                $this->db2->update('user', $data);
                $this->db2->trans_complete();
            }
        }

    }

    function get_noOf_module_user(){
        $this->load->database($this->get_db_array(), FALSE, TRUE);

        $companyID = $this->input->post('company_id');
        $noOfUsers = $this->db->get_where('srp_erp_company', ['company_id'=> $companyID])->row('noOfUsers');

        echo json_encode(['s', 'noOfUsers'=> $noOfUsers]);
    }

    function update_noOf_module_user(){
        $this->load->database($this->get_db_array(), FALSE, TRUE);

        $companyID = $this->input->post('pk');
        $value = $this->input->post('value');
        $value = (empty($value))? 0: $value;

        $old_val = $this->db->get_where('srp_erp_company', ['company_id'=> $companyID])->row('noOfUsers');

        $audit_log = [
            'tableName' => 'srp_erp_company', 'columnName'=> 'noOfUsers', 'old_val'=> $old_val,
            'display_old_val'=> $old_val, 'new_val'=> $value, 'display_new_val'=> $value, 'rowID'=> $companyID,
            'companyID'=> $companyID, 'userID'=> current_userID(), 'timestamp'=> date('Y-m-d H:i:s'),
        ];
        $this->main->insert('srp_erp_audit_log', $audit_log);

        $this->db->where(['company_id'=> $companyID])->update('srp_erp_company', ['noOfUsers'=> $value]);

        echo json_encode(['s', 'Successfully updated number of Module Users']);
    }

    function update_userType()
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $companyID = $this->input->post('company_id');
        $type = $this->input->post('type');
        $EIdNo = $this->input->post('empID');
        
        $this->db->trans_start();

        if ($type != 1) {            
            $this->db->where('EIdNo', $EIdNo)->update('srp_employeesdetails', ['userType'=> $type]);            
        } 
        else {
            $noOfUsers = $this->db->get_where('srp_erp_company', ['company_id'=> $companyID])->row('noOfUsers');    
            $noOfActive = $this->db->query("SELECT count(EIdNo) AS usercount FROM srp_employeesdetails WHERE 
                            Erp_companyID = $companyID AND userType = 1 AND isDischarged = 0")->row('usercount');
                                    
            if ($noOfActive < $noOfUsers || $noOfUsers == 0) { 
                $this->db->where('EIdNo', $EIdNo)->update('srp_employeesdetails', ['userType'=> $type]);
            } else if ($noOfActive >= $noOfUsers) {
                die( json_encode(['w', 'Maximum user count exceeded']));
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {            
            echo json_encode(['e', 'User Type Update Failed.<br/>' . $this->db->_error_message()]);

        } else {            
            echo json_encode(['s', 'User Type Updated Successfully.']);
        }
    }

    function payment_det(){ 
        $data['title'] = 'Payment Details';
        $data['main_content'] = 'payment-det-main-view';
        $data['extra'] = null;
        
        $this->load->view('include/template', $data);        
    }

    function fetch_payment_data(){ 
        $frm_date = $this->input->post('frm_date');
        $to_date = $this->input->post('to_date');
        $paymentType = $this->input->post('paymentType');
        $inv_type = $this->input->post('inv_type');
 
        $this->datatables->select("inv_mas.invID AS invID, CONCAT( company_code, '-', company_name ) AS com_name, inv_mas.invNo AS invNo, 
                inv_mas.invTotal AS invTotal, inv_mas.invDecPlace AS decPlc, cur_mas.CurrencyCode AS cur, inv_mas.invDate AS invDate,
                inv_mas.isAmountPaid, itemDescription, IF(inv_det.itemID = 1, sub_his.dueDate, '') AS dueDate, 
                DATE_FORMAT(inv_pay.pay_date, '%Y-%m-%d') AS pay_date, inv_mas.TransRefNo AS TransRefNo,
                CASE 
                    WHEN inv_det.itemID = 1 THEN 'Subscription'
                    WHEN inv_det.itemID = 2 THEN 'Implementation'
                    ELSE 'Ad hoc'
                END AS invoiceType,
                payType.pay_description AS paymentType, inv_pay.amount AS paidAmount")
            ->from('subscription_invoice_master AS inv_mas')
            ->join('(
                    SELECT invID, itemID, itemDescription FROM subscription_invoice_details GROUP BY invID                
                ) AS inv_det', 'inv_det.invID = inv_mas.invID')
            ->join('srp_erp_company AS com', 'com.company_id = inv_mas.companyID')
            ->join('srp_erp_currencymaster AS cur_mas', 'cur_mas.currencyID=inv_mas.invCur')
            ->join('subscription_invoice_payment_details AS inv_pay', 'inv_mas.invID=inv_pay.inv_id', 'left')
            ->join('system_payment_types AS payType', 'payType.id=inv_pay.pay_type', 'left')            
            ->join('companysubscriptionhistory AS sub_his', 'sub_his.subscriptionID = inv_mas.subscriptionID', 'left')
            ->where('com.adminType', current_userType());

        if( $frm_date ){
            $this->datatables->where("DATE(inv_mas.invDate) >= '{$frm_date}'");            
        }

        if( $to_date ){
            $this->datatables->where("DATE(inv_mas.invDate) <= '{$to_date}'");
        }
 
        if($paymentType !== ''){
            $paymentType = explode(',', $paymentType);
            $this->datatables->where_in('payType.id', $paymentType);
        }

        if($inv_type !== ''){
            $inv_type = explode(',', $inv_type);
            $this->datatables->where_in('inv_det.itemID', $inv_type);
        }

        $this->datatables->edit_column('com_name', '<div style="width: 200px">$1</div>', 'com_name')
            ->edit_column('invNo', '<span class="label-invoice" onclick="open_invoice($1)">$2</span>', 'invID,invNo')
            ->edit_column('invTotal', '<div style="text-align: right">$1 $2</div>', 'cur,number_format(invTotal,decPlc)')
            ->edit_column('paidAmount', '<div style="text-align: right">$1 $2</div>', 'cur,number_format(paidAmount,decPlc)');
        echo $this->datatables->generate();
    }

    function payment_logs(){         
        $data['title'] = 'Payment Log';
        $data['main_content'] = 'payment-log';
        $data['extra'] = null;
        
        $this->load->view('include/template', $data);
    }

    function fetch_payment_log(){
        $frm_date = $this->input->post('frm_date');
        $to_date = $this->input->post('to_date');
        $paymentType = $this->input->post('paymentType');
        $inv_type = $this->input->post('inv_type');
 
        $this->datatables->select("inv_mas.invID AS invID, CONCAT( company_code, '-', company_name ) AS com_name, 
                inv_mas.invNo AS invNo, itemDescription, inv_mas.TransRefNo AS TransRefNo, 
                payType.pay_description AS paymentType, 
                CASE 
                    WHEN inv_det.itemID = 1 THEN 'Subscription'
                    WHEN inv_det.itemID = 2 THEN 'Implementation'
                    ELSE 'Ad hoc'
                END AS invoiceType,                
                pay_log.summary AS summary, payLog, pay_log.createdDateTime AS logDate, pay_log.id AS payLogID")
            ->from('payment_log AS pay_log')
            ->join('subscription_invoice_master AS inv_mas', 'pay_log.invID=inv_mas.invID')
            ->join('(
                    SELECT invID, itemID, itemDescription FROM subscription_invoice_details GROUP BY invID                
                ) AS inv_det', 'inv_det.invID = inv_mas.invID')
            ->join('srp_erp_company AS com', 'com.company_id = inv_mas.companyID')                    
            ->join('system_payment_types AS payType', 'payType.id=pay_log.paymentType')            
            ->join('companysubscriptionhistory AS sub_his', 'sub_his.subscriptionID = inv_mas.subscriptionID', 'left')
            ->where('com.adminType', current_userType());

        if( $frm_date ){
            $this->datatables->where("DATE(pay_log.createdDateTime) >= '{$frm_date}'");            
        }

        if( $to_date ){
            $this->datatables->where("DATE(pay_log.createdDateTime) <= '{$to_date}'");
        }
 
        if($paymentType !== ''){
            $paymentType = explode(',', $paymentType);
            $this->datatables->where_in('payType.id', $paymentType);
        }

        if($inv_type !== ''){
            $inv_type = explode(',', $inv_type);
            $this->datatables->where_in('inv_det.itemID', $inv_type);
        }

        $this->datatables->edit_column('com_name', '<div style="width: 200px">$1</div>', 'com_name')
            ->edit_column('invNo', '<span class="label-invoice" onclick="open_invoice($1)">$2</span>', 'invID,invNo')
            ->edit_column('payLog', '$1', 'payLogStr(payLogID, payLog)');
        echo $this->datatables->generate();
    }

    function logDecode(){
        $logID = $this->input->post('logID');

        $logData = $this->db->get_where('payment_log', ['id'=> $logID])->row_array();
        $invNo = $this->db->select('invNo')->where('invID', $logData['invID'])
                            ->get('subscription_invoice_master')->row('invNo');
        
        $log_str = json_decode($logData['payLog']);

        echo '<div >';
        echo '<h3>Invoice No #'.$invNo.' <button type="button" class="close" data-dismiss="modal">&times;</button></h3>';
        echo '<hr/></div>';

        echo '<pre>';
        print_r($log_str);
        echo '</pre>'; 
    }

    function nav_setup(){
        $nav_modules = get_navigation_modules();
        $sub_nav = $this->get_sub_modules($nav_modules);
        $module_sort_order = $this->db->select('MAX(sortOrder) sortOrder')->where(['masterID'=> null])
                                ->get('srp_erp_navigationmenus')->row('sortOrder');
    
        $data['title'] = 'Navigation Setup';
        $data['main_content'] = 'nav-setup/nav-setup-container';
        $data['nav_modules'] = $nav_modules;
        $data['sub_nav'] = $sub_nav;
        $data['module_sort_order'] = ($module_sort_order + 1);
        $data['extra'] = ['js_page' => 'nav-setup/nav-setup-js'];
        
        $this->load->view('include/template', $data);
    }

    function get_sub_modules($modules){       
        $modules = array_keys($modules);
        $modules = implode(',', $modules); 

        $sub_nav = $this->db->query("SELECT navigationMenuID, `description`, masterID, moduleID,
                        sortOrder, pageIcon
                        FROM srp_erp_navigationmenus WHERE masterID IN ({$modules})
                        ORDER BY masterID, sortOrder")->result_array();

        $sub_nav = array_group_by($sub_nav, 'masterID');
        
        return $sub_nav;
    } 

    function load_nav_sub_modules(){
        $module_id = $this->input->post('module_id');
        $sub_id = $this->input->post('sub_id');

        $sub_nav = $this->db->select('navigationMenuID,description,sortOrder')
                        ->where(['masterID'=> $module_id])
                        ->get('srp_erp_navigationmenus')->result_array(); 

        $sort_order = 1;
        $sort_arr = [];
        $html = '<option value=""> None </option>';        
        foreach($sub_nav as $row){
            $sort_arr[] = $row['sortOrder'];
            $selected = ($sub_id == $row['navigationMenuID'])? 'selected': '';
            $html .= '<option value="'.$row['navigationMenuID'].'" '.$selected.'>'.$row['description'].'</option>';
        }
        
        $sort_order = ($sort_arr)? (max($sort_arr) + 1) : $sort_order;
        echo json_encode(['s', "html"=> $html, 'sort_order'=> $sort_order]);
    }

    function get_nav_sub_module_sortOrder(){
        $module_id = $this->input->post('sub_module_id');

        $sort_order = $this->db->select('MAX(sortOrder) sortOrder')->where(['masterID'=> $module_id])
                        ->get('srp_erp_navigationmenus')->row('sortOrder');                    

        $sort_order += 1;
        echo json_encode(['s', 'sort_order'=> $sort_order]);
    }

    function erp_navigation_save(){
        $nav_edit_id = $this->input->post('nav_edit_id');
        $sub_module = $this->input->post('sub_module');
 
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'sortOrder', 'trim|required');
        $this->form_validation->set_rules('page_url', 'Page Url', 'trim|required');
        if ($sub_module) {
            $this->form_validation->set_rules('pr_module', 'Module', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $description = $this->input->post('description');
        $pr_module = $this->input->post('pr_module');
        $isExternal = $this->input->post('isExternal');
        $isBasic = $this->input->post('isBasic');
        $page_url = $this->input->post('page_url');
        $pageIcons = $this->input->post('pageIcons');
        $sortOrder = $this->input->post('sortOrder');        
        $isGroup = $this->input->post('isGroup');
        $isReport = $this->input->post('isReport');
        $reportID = $this->input->post('reportID');
        $createPageLink = $this->input->post('createPage');
        $documentCode = $this->input->post('documentCode');
        $templateKey = $this->input->post('templateKey');

        $masterID = $levelNo = 0;
        if( empty($pr_module) ){
            $masterID = null; 
        }

        if( !empty($pr_module) && empty($sub_module) ){
            $masterID = $pr_module;
            $levelNo = 1;
        }

        if( !empty($pr_module) && !empty($sub_module) ){
            $masterID = $sub_module;
            $levelNo = 2;            
        }

        $isSubExist = ($levelNo < 2)? 1: 0;
        $pr_module = (empty($pr_module))? 0: $pr_module;

        $data = [
            'description'=> $description, 'masterID'=> $masterID, 'moduleID'=> $pr_module, 'isExternalLink'=> $isExternal, 
            'languageID'=> $masterID, 'basicYN'=> $isBasic, 'url'=> $page_url, 'pageID'=> $reportID, 'pageTitle'=> $description, 
            'pageIcon'=> $pageIcons, 'levelNo'=> $levelNo, 'sortOrder'=> $sortOrder, 'isSubExist'=> $isSubExist, 
            'isGroup'=> $isGroup, 'timestamp'=> date('Y-m-d H:i:s')
        ];        

        $this->main = $this->load->database('db2', TRUE);
        $this->main->trans_start();
                
        $this->main->insert('srp_erp_navigationmenus', $data);
        $nav_id = $this->main->insert_id();
        $data['navigationMenuID'] = $nav_id;
        $clientDB_data['nav_data'] = $data;

        if($levelNo == 2){
            $this->main->insert('srp_erp_formcategory', [
                'Category'=> $description, 'navigationMenuID'=> $nav_id
            ]);
            $form_id = $this->main->insert_id();
            $clientDB_data['frm_data'] = [
                'FormCatID'=> $form_id, 'Category'=> $description, 'navigationMenuID'=> $nav_id
            ];
     
            $temp_data = [
                'TempDes'=> $description, 'TempPageName'=> $description, 'TempPageNameLink'=> $page_url,
                'createPageLink'=> $createPageLink, 'FormCatID'=> $form_id, 'isReport'=> $isReport, 'isDefault'=> 1,
                'documentCode'=> $documentCode, 'templateKey'=> $templateKey
            ];
            $this->main->insert('srp_erp_templatemaster', $temp_data);
            $temp_data['TempMasterID'] = $this->main->insert_id();
            $clientDB_data['temp_data'] = $temp_data;
        }        
        $this->main->trans_complete();
        if ($this->main->trans_status() == true){
            $failed_db = $this->clientDB_navigation_add_update('add', $clientDB_data);

            $new_module = ($masterID == null && empty($nav_edit_id))? $nav_id: null;            
            $failed_db = ($failed_db)? implode('<br/> &nbsp; - &nbsp; ', $failed_db): null;
            
            echo json_encode([
                's', 'Success added the navigation', 'new_module'=> $new_module, 'failed_db'=> $failed_db
            ]);
        }
        else {            
            echo json_encode(['e', 'Error in navigation create process']);
        }    
    }

    function erp_navigation_update(){
        $this->form_validation->set_rules('nav_edit_id', 'Navigation ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('sortOrder', 'sortOrder', 'trim|required');
        $this->form_validation->set_rules('page_url', 'Page Url', 'trim|required');        

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $nav_id = $this->input->post('nav_edit_id');   
        $description = $this->input->post('description');
        $isExternal = $this->input->post('isExternal');
        $isBasic = $this->input->post('isBasic');
        $page_url = $this->input->post('page_url');
        $pageIcons = $this->input->post('pageIcons');
        $sortOrder = $this->input->post('sortOrder');
        $isGroup = $this->input->post('isGroup');
        $isReport = $this->input->post('isReport');
        $reportID = $this->input->post('reportID');
        $createPageLink = $this->input->post('createPage');
        $documentCode = $this->input->post('documentCode');
        $templateKey = $this->input->post('templateKey');

        $nav_data = [
            'description'=> $description, 'isExternalLink'=> $isExternal, 'basicYN'=> $isBasic, 
            'url'=> $page_url, 'pageID'=> $reportID, 'pageTitle'=> $description, 'pageIcon'=> $pageIcons, 
            'sortOrder'=> $sortOrder, 'isGroup'=> $isGroup, 'timestamp'=> $this->date_time
        ];

        $clientDB_data['nav_id'] = $nav_id;
        $clientDB_data['nav_data'] = $nav_data;

        $this->main = $this->load->database('db2', TRUE);

        $this->main->trans_start();

        $this->main->where('navigationMenuID', $nav_id)->update('srp_erp_navigationmenus', $nav_data);

        $this->main->where('navigationMenuID', $nav_id)
                ->update('srp_erp_formcategory', ['Category'=> $description]);

        $form_id = $this->main->select('FormCatID')->where('navigationMenuID', $nav_id)
                        ->get('srp_erp_formcategory')->row('FormCatID');
 
        if($form_id){
            $temp_data = [
                'TempDes'=> $description, 'TempPageName'=> $description, 'TempPageNameLink'=> $page_url,
                'createPageLink'=> $createPageLink, 'isReport'=> $isReport, 'documentCode'=> $documentCode, 
                'templateKey'=> $templateKey
            ];
            $this->main->where(['FormCatID'=> $form_id, 'isDefault'=> 1])
                ->update('srp_erp_templatemaster', $temp_data);

            $this->main->where('FormCatID', $form_id)->update('srp_erp_templatemaster', [
                'TempDes'=> $description
            ]);

            $clientDB_data['form_id'] = $form_id;
            $clientDB_data['temp_data'] = $temp_data;
        }

        $this->main->trans_complete();
        if ($this->main->trans_status() == true){
            $failed_db = $this->clientDB_navigation_add_update('update', $clientDB_data);
            $failed_db = ($failed_db)? implode('<br/> &nbsp; - &nbsp; ', $failed_db): null;
            
            echo json_encode(['s', 'Success update the navigation details', 'failed_db'=> $failed_db]);
        }
        else {
            echo json_encode(['e', 'Error in navigation details update process']);
        }    
    }

    function delete_navigation(){
        $this->form_validation->set_rules('nav_id', 'Navigation ID', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $id = $this->input->post('nav_id');

        //check whether navigation assigned to product
        $is_used = $this->db->select('pm.description')->where('p.navID', $id)
            ->join('product_master AS pm', 'p.productID=pm.id')
            ->get('product_navigations AS p')->result_array();

        if($is_used){
            $msg = 'This navigation used in following products.<br> &nbsp; - ';
            $msg .= implode(' &nbsp; - ', array_column($is_used, 'description'));
            die( json_encode(['e', $msg]) );
        }

        //check sub exists for this navigation
        $sub_exists = $this->db->select('description')->where('masterID', $id)
            ->get('srp_erp_navigationmenus')->result_array();
        if($sub_exists){
            $msg = 'This navigation has following sub navigations.<br/> &nbsp; - ';
            $msg .= implode('<br/> &nbsp; - ', array_column($sub_exists, 'description'));
            die( json_encode(['e', $msg]) );
        }        
        
        $nav_data = $this->db->query("SELECT t.FormCatID, t.TempMasterID
            FROM srp_erp_formcategory AS f
            JOIN srp_erp_templatemaster AS t ON f.FormCatID=t.FormCatID
            WHERE f.navigationMenuID = {$id}")->row_array();

        $this->main = $this->load->database('db2', TRUE);
        
        $this->main->trans_start();
        
        $clientDB_data['navID'] = $id;
        
        if($nav_data){
            $clientDB_data['nav_data'] = $nav_data;

            if($nav_data['TempMasterID']){
                $this->main->delete('srp_erp_templatemaster', ['TempMasterID'=> $nav_data['TempMasterID']]);
            }

            if($nav_data['FormCatID']){
                $this->main->delete('srp_erp_formcategory', ['FormCatID'=> $nav_data['FormCatID']]);
            }
        }

        $this->main->delete('srp_erp_navigationmenus', ['navigationMenuID'=> $id]);
        
        $this->main->trans_complete();
        if ($this->main->trans_status() == true){
            $failed_db = $this->clientDB_navigation_add_update('delete', $clientDB_data);
            $failed_db = ($failed_db)? implode('<br/> &nbsp; - &nbsp; ', $failed_db): null;

            echo json_encode(['s', 'Navigation deleted successfully', 'failed_db'=> $failed_db]);
        }
        else {
            echo json_encode(['e', 'Error in navigation delete process']);
        }
    }

    function clientDB_navigation_add_update($type, $data){
        $clientDb = get_clientDB();        

        $failed_db = [];
        $processed_db = [];    
        foreach ($clientDb as $key => $com) {
            $dbHost = decryptData($com['host']);
            $dbName = decryptData($com['db_name']);
            $prDB = "{$dbHost}|{$dbName}";
                        
            if( in_array($prDB, $processed_db) ){
                continue;
            }

            $processed_db[] = $prDB;     
            
            $this->db_host = $dbHost;
            $this->db_name = $dbName;
            $this->db_password = decryptData($com['db_password']);
            $this->db_username = decryptData($com['db_username']);

            $this->datatables->set_database($this->get_db_array(true), FALSE, TRUE);

            $this->db->trans_start();

            if($type == 'add'){
                $this->db->insert('srp_erp_navigationmenus', $data['nav_data']);
                if(array_key_exists('frm_data', $data)){
                    $this->db->insert('srp_erp_formcategory', $data['frm_data']);
                    $this->db->insert('srp_erp_templatemaster', $data['temp_data']);
                }            
            }

            if($type == 'update'){
                if(array_key_exists('nav_data', $data) && array_key_exists('nav_id', $data)){
                    $this->db->where('navigationMenuID', $data['nav_id'])->update('srp_erp_navigationmenus', $data['nav_data']);
                }
    
                if(array_key_exists('form_id', $data)){
                    $description = $data['nav_data']['description'];
    
                    $this->db->where('navigationMenuID', $data['nav_id'])
                         ->update('srp_erp_formcategory', [ 'Category'=> $description ]);
    
                    $this->db->where(['FormCatID'=> $data['form_id'], 'isDefault'=> 1])
                        ->update('srp_erp_templatemaster', $data['temp_data']);
                        
                   $this->db->where('FormCatID',$data['form_id'])->update('srp_erp_templatemaster', [
                       'TempDes'=> $description
                   ]);
                }
            }

            if($type == 'delete'){
                if(array_key_exists('nav_data', $data)){
                    $nav_data = $data['nav_data'];
                    if($nav_data['TempMasterID']){
                        $this->db->delete('srp_erp_templatemaster', ['TempMasterID'=> $nav_data['TempMasterID']]);
                    }
        
                    if($nav_data['FormCatID']){
                        $this->db->delete('srp_erp_formcategory', ['FormCatID'=> $nav_data['FormCatID']]);
                    }
                }

                if(array_key_exists('navID', $data)){
                    $this->db->delete('srp_erp_navigationmenus1', ['navigationMenuID'=> $data['navID']]);
                }
            }
            
            $this->db->trans_complete();
            if ($this->db->trans_status() == false){
                $failed_db[] = $dbHost.' | '.$dbName;
            }
        }
 
        return $failed_db;
    }

    function search_nav(){
        $searchKey = $this->input->get('searchKey');
        
        $arr = $this->db->select("nav.navigationMenuID AS id, nav.description AS desp,
                CONCAT(
                    nav.description, 
                    IF(navM.description != '' AND navM.description IS NOT NULL, CONCAT(' > ', navM.description), ''),
                    IF(navMod.description != '' AND navMod.description IS NOT NULL AND navMod.navigationMenuID != navM.navigationMenuID, 
                        CONCAT(' > ', navMod.description), ''
                    ),
                    ' > ', nav.url
                ) AS text")
                ->join('srp_erp_navigationmenus AS navM', 'navM.navigationMenuID=nav.masterID', 'LEFT')
                ->join('srp_erp_navigationmenus AS navMod', 'navMod.navigationMenuID=nav.moduleID', 'LEFT')
                ->where("nav.description LIKE '%$searchKey%' OR navMod.description LIKE '%$searchKey%' OR nav.url LIKE '%$searchKey%'")
                ->order_by('nav.navigationMenuID')->limit(50)                
                ->get('srp_erp_navigationmenus AS nav')->result();    
        
        $rt['items'] = $arr;
        echo json_encode($rt);
    }
    
    function get_navigation_det(){
        $nav_id = $this->input->post('nav_id');

        $nav_data = $this->db->select('nav.*, t.createPageLink,t.isReport,t.isDefault,t.documentCode,t.templateKey')
                        ->where(['nav.navigationMenuID'=> $nav_id])
                        ->join('srp_erp_formcategory AS f', 'f.navigationMenuID=nav.navigationMenuID', 'left')
                        ->join('srp_erp_templatemaster AS t', 't.FormCatID = f.FormCatID', 'left')
                        ->get('srp_erp_navigationmenus AS nav')->row_array();

        if(empty($nav_data) ){
            die( json_encode(['e', "Navigation details not found"]) );
        }

        $nav_data['basicYN'] = (empty($nav_data['basicYN']))? 0: $nav_data['basicYN'];
        $nav_data['isGroup'] = (empty($nav_data['isGroup']))? 0: $nav_data['isGroup'];
        $nav_data['isReport'] = (empty($nav_data['isReport']))? 0: $nav_data['isReport'];
        $nav_data['pageID'] = ($nav_data['isReport'] == 0)? '': $nav_data['pageID'];
        $nav_data['isExternalLink'] = (empty($nav_data['isExternalLink']))? 0: $nav_data['isExternalLink'];

        echo json_encode(['s', 'nav_data'=> $nav_data]);
    }

    function nav_module_view($id=0, $productID=0){
        $isProductVew = ($id == 0)? 'N': 'Y';
        $module_id = ($id == 0)? $this->input->post('id'): $id;
        $config = $this->input->post('config');
        
        if($isProductVew == 'N'){
            $this->main = $this->load->database('db2', TRUE);
        }

        $join_type = ( in_array($config, ['Y', 'V']) )? 'LEFT': '';
        $pr_tbl = ($productID == -1)? 'temporary_product_navigations': 'product_navigations';

        $nav_data = $this->main->query("SELECT navigationMenuID, `description`, pageIcon, masterID, 
                                lev1.sortOrder, IFNULL(n.sortOrder , 100) AS srOrder,
                                IF(navID IS NULL, 'N', 'Y') AS checked
                                FROM srp_erp_navigationmenus AS lev1
                                {$join_type} JOIN (
                                    SELECT navID, sortOrder FROM {$pr_tbl} 
                                    WHERE productID = {$productID} AND moduleID = {$module_id}
                                ) AS n ON n.navID = lev1.navigationMenuID
                                WHERE levelNo = 1 AND masterID = {$module_id}
                                ORDER BY masterID, srOrder")->result_array();


        $sub_data = $this->main->query("SELECT lev2.*
                        FROM srp_erp_navigationmenus AS lev1
                        JOIN (
                            SELECT navigationMenuID, `description`, masterID, pageIcon, lev2.sortOrder, 
                            IFNULL(n.sortOrder , 100) AS srOrder, IF(navID IS NULL, 'N', 'Y') AS checked
                            FROM srp_erp_navigationmenus AS lev2
                            {$join_type} JOIN (
                                SELECT navID, sortOrder FROM {$pr_tbl} WHERE productID = {$productID}  
                            ) AS n ON n.navID = lev2.navigationMenuID
                            WHERE levelNo = 2
                            ORDER BY sortOrder ASC
                        ) AS lev2 ON lev1.navigationMenuID = lev2.masterID
                        WHERE levelNo = 1 AND lev1.masterID = {$module_id}
                        ORDER BY lev1.masterID, lev1.sortOrder, lev2.sortOrder, lev2.srOrder")->result_array();

        $title = $this->main->select('pageIcon,description')->where(['navigationMenuID'=> $module_id])
                          ->get('srp_erp_navigationmenus')->row_array();                            
        
        $sub_data = array_group_by($sub_data, 'masterID');
        $cls = ($isProductVew == 'Y')? 'list-group-item': 'treeview';
        $module_str = ($module_id)? " <span class='prod-sub-cls'> &nbsp; >  &nbsp; {$title['description']} </span>": '';
                        
        $view = ''; $master_arr = []; 
        foreach($nav_data as $row){        
            $masterID = $row['navigationMenuID'];
            $master_arr[] = (int)$masterID;
            $view .= '<li class="'.$cls.' active module-search-li-'.$module_id.'" data-search="'.$row['description'].'" >';
            $view .= '<i class="'.$row['pageIcon'].'"></i> &nbsp; '.$row['description'].' ' .$module_str;

            if($isProductVew == 'Y'){
                $sortOrder = ($row['checked'] == 'Y')? $row['srOrder']: $row['sortOrder'];
                $checked = ($row['checked'] == 'Y')? 'checked': '';

                $view .= '<span class="pull-right" style="margin-right: 120px">';                  
                if($config == 'Y'){
                    $view .= '<input type="hidden" name="moduleID['.$masterID.']" value="'.$module_id.'"/>';
                    $view .= '<input type="checkbox" name="navID[]" class="prod-chk-all prod-chk-'.$masterID.'" id="sub-nav-master-'.$masterID.'"';
                    $view .= ' onclick="check_sub_navs(this, '.$masterID.', '.$module_id.')" value="'.$masterID.'" '.$checked.'/>';
                    $view .= '<input type="text" name="sort_order['.$masterID.']" class="nav-sort-order number" placeholder="sort order" ';
                    $view .= ' value="'.$sortOrder.'"/>';
                }
                $view .= '</span>';
            }
            
            $view .= '</li>';
            
            $module_str_sub = '';
            if($isProductVew == 'Y'){
                $module_str_sub = "<span class='prod-sub-cls pull-right prod-top-level'> &nbsp; >  &nbsp; {$row['description']} | ";
                $module_str_sub = $title['description'].' &nbsp; | &nbsp; '.$row['description'];
                $module_str_sub = $title['description'].' &nbsp; | &nbsp; '.$row['description'];
            }

            $sub_nav = (array_key_exists($masterID, $sub_data))? $sub_data[$masterID]: [];
            
            foreach($sub_nav as $sub){
                $view .= '<li class="'.$cls.' treeview-sub module-search-li-'.$module_id.'" title="'.$module_str_sub.'" ';
                $view .= ' data-search="'.$sub['description'].'" >';
                $view .= ' <i class="'.$sub['pageIcon'].'"></i> &nbsp; '.$sub['description'];
                
                if($isProductVew == 'Y'){
                    $sub_sortOrder = ($sub['checked'] == 'Y')? $sub['srOrder']: $sub['sortOrder'];
                    $sub_checked = ($sub['checked'] == 'Y')? 'checked': '';

                    $sub_id = $sub['navigationMenuID'];
                    $view .= '<span class="pull-right" style="margin-right: 120px">';
                    if($config == 'Y'){
                        $view .= '<input type="hidden" name="moduleID['.$sub_id.']" value="'.$module_id.'"/>';                    
                        $view .= '<input type="checkbox" name="navID[]" class="sub-nav-check-'.$masterID.' prod-chk-all prod-chk-'.$sub_id.'" ';
                        $view .= 'value="'.$sub_id.'" onclick="check_master_navs(this, '.$masterID.', '.$module_id.')" '.$sub_checked.'/>';
                        $view .= '<input type="text" name="sort_order['.$sub_id.']" class="nav-sort-order number" placeholder="sort order" ';
                        $view .= ' value="'.$sub_sortOrder.'"/>';
                    }
                    $view .= '</span>';
                    $view .= '<span class="pull-right sub-nav-description" style="margin-right: 120px"> '.$module_str_sub.'</span>';
                }                
                $view .= '</li>';
            }
        }

        if($isProductVew == 'Y'){
            return ['master_arr'=> $master_arr, 'view'=> $view];
        }

        $view = '<ul class="sidebar-menu">'.$view.'</ul>';

        echo json_encode(['s', "view"=> $view, 'title'=> $title]);
    }

    function load_navigation_from_client(){
        $companyID = $this->input->post('company_id');

        $this->main->query("CREATE TEMPORARY TABLE temporary_product_navigations (productID INT,navID INT, 
                            sortOrder INT, moduleID INT)");

        $this->load->database($this->get_db_array(), FALSE, TRUE);

        $client_data = $this->db->select('navigationMenuID,IFNULL(masterID,0) AS moduleID,sortOrder')
                            ->get('srp_erp_navigationmenus')->result_array();

        $arr = [];
        foreach($client_data as $row){
            $arr[] = [
                'productID'=> -1, 'navID'=> $row['navigationMenuID'],
                'sortOrder'=> $row['sortOrder'], 'moduleID'=> $row['moduleID']
            ];
        }                   
        
        if(!empty($arr)){
            $this->main->insert_batch('temporary_product_navigations', $arr);
        }
    }

    function load_product_modules(){            
        $productID = $this->input->post('productID');
        $config = $this->input->post('config');
        
        $this->main = $this->load->database('db2', TRUE);

        $pr_tbl = 'product_navigations';        
        if($productID == -1){
            $this->load_navigation_from_client();
            $pr_tbl = 'temporary_product_navigations';
        }

        $join_type = ($config == 'Y')? 'LEFT': '';
        $modules = $this->main->query("SELECT moduleID, `description`, pageIcon, m.sortOrder,
                            IF(navID IS NULL, 'N', 'Y') AS checked, IFNULL(n.sortOrder, 100) AS srOrder
                            FROM navigation_modules m
                            {$join_type} JOIN (
                                SELECT navID, sortOrder
                                FROM {$pr_tbl} WHERE productID = {$productID} AND moduleID = 0
                            ) n ON n.navID = m.moduleID                            
                            ORDER BY srOrder, `description`")->result_array();

        $sub_data = [];
        foreach($modules as $row){
            $id = $row['moduleID'];
            $sub_data[$id] = $this->nav_module_view($id, $productID);
        }
        $data['nav_modules_arr'] = $modules;
        $data['sub_data'] = $sub_data;
        
        if($productID == -1){
            $this->main->query("DROP TEMPORARY TABLE temporary_product_navigations");
        }

        $view = $this->load->view('nav-setup/product-modules-view', $data, true);
        echo json_encode(['s', 'view'=> $view, "sub_data"=> $sub_data]);
    }

    function save_product_nav(){        
        $this->form_validation->set_rules('productID', 'Product', 'trim|required');    
        if ($this->form_validation->run() == FALSE) {            
            die( json_encode(['e', validation_errors()]) );     
        }         

        $productID = $this->input->post('productID');
        $moduleID = $this->input->post('moduleID');
        $navID = $this->input->post('navID');
        $sort_order = $this->input->post('sort_order');

        $mainDB = $this->load->database('db2', TRUE);
        
        $mainDB->trans_start();
        
        $mainDB->query("CREATE TEMPORARY TABLE temporary_product_navigations (productID INT,navID INT)");

        
        $mainDB->query("INSERT INTO temporary_product_navigations
                        SELECT productID, navID FROM product_navigations                        
                        WHERE productID = {$productID}");
        
        $mainDB->delete('product_navigations', ['productID'=> $productID]);
        
        if($navID){
            $date_time = date('Y-m-d H:i:s'); $pc = current_pc(); $user_id = current_userID();

            $arr = [ 
                'productID'=> $productID, 'createdPCID'=> $pc, 
                'createdUserID'=> $user_id, 'createdDateTime'=> $date_time
            ];
            
            $data = [];
            foreach($navID as $nav){
                $order = (empty($sort_order[$nav]))? 0: $sort_order[$nav];
                $arr['navID'] = $nav;
                $arr['moduleID'] = $moduleID[$nav];
                $arr['sortOrder'] = $order;
                $data[] = $arr;
            }

            $mainDB->insert_batch('product_navigations', $data);
        }

        $un_selected = $mainDB->query("SELECT navID FROM temporary_product_navigations
                        WHERE productID = {$productID} AND navID NOT IN (
                            SELECT navID FROM product_navigations
                            WHERE productID = {$productID}
                        )")->result_array();

        $selected = $mainDB->query("SELECT navID FROM product_navigations
                        WHERE productID = {$productID} AND navID NOT IN (
                            SELECT navID FROM temporary_product_navigations
                            WHERE productID = {$productID}
                        )")->result_array();
        
        $mainDB->query("DROP TEMPORARY TABLE temporary_product_navigations");
        
        $mainDB->trans_complete();
        if ($mainDB->trans_status() == true){
            $failed_db = $this->clientDB_nav_update_on_product($productID, $un_selected, $selected);
            $failed_db = ($failed_db)? implode('<br/> &nbsp; - &nbsp; ', $failed_db): null;

            echo json_encode(['s', 'Product navigation updated successfully', 'failed_db'=> $failed_db]);
        }
        else {
            echo json_encode(['e', 'Error in product navigation updated.']);
        }        
    }

    function clientDB_nav_update_on_product($productID, $un_selected, $selected){
        if(empty($un_selected) && empty($selected)){
            return null;
        }

        $this_prCompany = $this->db->select('company_id')->where('product_id', $productID)
                            ->get('product_company')->result_array();
        if(empty($this_prCompany)){
            return null;
        }
        
        $this_prCompany = array_column($this_prCompany, 'company_id');        
        $clientDb = get_clientDB($this_prCompany);
        $navID = $this->input->post('navID');            

        $failed_db = [];
        $processed_db = [];    
        foreach ($clientDb as $key => $com) {
            $dbHost = decryptData($com['host']);
            $dbName = decryptData($com['db_name']);
            $prDB = "{$dbHost}|{$dbName}";
                        
            if( in_array($prDB, $processed_db) ){
                continue;
            }

            $processed_db[] = $prDB;            
            
            $this->db_host = $dbHost;
            $this->db_name = $dbName;
            $this->db_password = decryptData($com['db_password']);
            $this->db_username = decryptData($com['db_username']);

            $this->datatables->set_database($this->get_db_array(true), FALSE, TRUE);

            $this->db->trans_start();

            $this->db->where(['isActive'=> 1])->update('srp_erp_navigationmenus', ['isActive'=> 0]);
            if($navID){
                $this->db->where_in('navigationMenuID', $navID)->update('srp_erp_navigationmenus', ['isActive'=> 1]);
            }
            
            $this->db->trans_complete();
            if ($this->db->trans_status() == false){
                $failed_db[] = $dbHost.' | '.$dbName;
            }
        }
 
        return $failed_db;
    }

    function clientDB_activate_navigations(){
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('productID', 'Product', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }  
    
        $company_id = $this->input->post('company_id');
        $productID = $this->input->post('productID');
        $warning_verify = $this->input->post('warning_verify');

        $this->load->database($this->get_db_array(), FALSE, TRUE);
        
        if($warning_verify == 0){
            $this->clientDB_nav_verify($company_id);
        }
        
        $this->main->trans_begin();

        /* Delete ff already assign to a product */
        $this->main->where('company_id', $company_id)->delete('product_company');

        $pc = current_pc(); $user_id = current_userID();
        $this->main->insert('product_company', [
            'product_id'=> $productID, 'company_id'=> $company_id, 'createdPCID'=> $pc,
            'createdUserID'=> $user_id, 'createdDateTime'=> $this->date_time, 'timestamp'=> $this->date_time
        ]);

        if ($this->main->trans_status() === FALSE){
            $this->main->trans_rollback();
            die( json_encode(['e', 'Error in product assining.']) );
        }

        if($productID == -1){
            $this->form_validation->set_rules('navID[]', 'navigation', 'required');
        
            if ($this->form_validation->run() == FALSE) {
                die( json_encode(['e', 'Please select atlease one module/naviagtion to proceed.']) );
            }

            $navData = [];
            $nav = $this->input->post('navID');
            $sortOrder = $this->input->post('sort_order');
            foreach($nav as $key=>$id){
                $order = (empty($sortOrder[$id]))? 0: $sortOrder[$id];
                $navData[] = ['navID'=> $id, 'sortOrder'=> $order];
            }
        }
        else{
            $navData = $this->main->select('navID, sortOrder')
                            ->where('productID', $productID)->order_by('navID, sortOrder')
                            ->get('product_navigations')->result_array();

            if(empty($navData)){
                die( json_encode(['e', 'Navigation not config for this product.']) );
            }
        }         
        
        $arr = [];
        foreach($navData as $row){
            $arr[] = [
                'navigationMenuID'=> $row['navID'],                 
                'sortOrder'=> $row['sortOrder'],
                'isActive'=> 1,
            ];
        }

        $this->db->trans_start();

        $this->db->where('isActive', 1)->update('srp_erp_navigationmenus', ['isActive'=> 0]);

        $this->db->where('isActive', 0)->update_batch('srp_erp_navigationmenus', $arr, 'navigationMenuID');

        $this->config_nav_template($company_id);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() == true){
            $this->main->trans_commit(); //commit the main db transaction
            echo json_encode(['s', 'Navigations activted successfully.']);
        }
        else {
            echo json_encode(['e', 'Error in navigation activating process.']);
        }
    }

    function clientDB_nav_verify($company_id){
        $warning_msg = '';

        $activeNavCount = $this->db->select('COUNT(navigationMenuID) AS navCnt')
            ->get_where('srp_erp_navigationmenus', ['isActive'=> 1])->row('navCnt');        
        if($activeNavCount > 0){
            $warning_msg = ' &nbsp; - Navigation already activated in the Client DB.';
        }

        $companies = $this->db->select("company_id, company_name ,company_code")                        
                            ->get('srp_erp_company')->result_array();
        if(count($companies) > 1){
            $com_arr = array_column($companies, 'company_id');

            $com_product = $this->main->select('pc.company_id, p.description')
                ->where_in('pc.company_id', $com_arr)
                ->join('product_master p', 'pc.product_id=p.id')
                ->get('product_company AS pc')->result_array();

            $com_product = array_group_by($com_product, 'company_id');

            $warning_msg .= ($warning_msg != '')? '<br/><br/>': '';
            $warning_msg .= ' &nbsp; - Following companies in this client DB.<br/>';
            
            $warning_msg .= '<table class="'.table_class().'" style="margin-left: 10px">';
            $warning_msg .= '<thead><tr><th>Company</th><th>Product</th></tr></thead>';
            foreach($companies as $row){
                $com_id = $row['company_id'];
                $prod = ( array_key_exists($com_id, $com_product) )? $com_product[$com_id][0]['description']: '';
                $warning_msg .= '<tr>                                    
                        <td>'.$row['company_name'].' [ '.$row['company_code'].' ]</td>
                        <td>'.$prod.'</td>
                    </tr>';
            }
            $warning_msg .= '</table>';
        }
        
        if($warning_msg != ''){
            die( json_encode(['w', $warning_msg]) );
        }
        
    }

    function config_nav_template($company_id){        
        $this->db->where('companyID', $company_id)->delete('srp_erp_templates');
        
        $tem_data = $this->db->select('t.TempMasterID, t.FormCatID, n.navigationMenuID, t.templateKey
                      ')
                    ->join('srp_erp_formcategory AS f', 'f.navigationMenuID=n.navigationMenuID')
                    ->join('srp_erp_templatemaster AS t', 'f.FormCatID=t.FormCatID AND t.isDefault = 1')
                    ->where('n.isActive = 1')
                    ->get('srp_erp_navigationmenus n')->result_array();
        
        $date_time = $this->date_time;
        $pc = current_pc(); $user_id = current_userID();
        foreach($tem_data as $key=>$row){
            $tem_data[$key]['companyID'] = $company_id;
            $tem_data[$key]['createdUserID'] = $user_id;
            $tem_data[$key]['CreatedPC'] = $pc;
            $tem_data[$key]['CreatedDate'] = $date_time;
            $tem_data[$key]['Timestamp'] = $date_time;
        }
        
        $this->db->insert_batch('srp_erp_templates', $tem_data);   
    }

    function load_nav_template_setup_view(){
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $company_id = $this->input->post('company_id');

        $temp = $this->db->query("SELECT TempMasterID, TempDes, TempPageName, TempPageNameLink,
                            createPageLink, ft.*
                            FROM srp_erp_templatemaster AS tm
                            JOIN (
                                SELECT tm.FormCatID, t.TempMasterID AS curntTemplate
                                FROM srp_erp_templates AS t
                                JOIN srp_erp_templatemaster AS tm ON tm.FormCatID = t.FormCatID 
                                WHERE t.companyID = {$company_id}
                                GROUP BY FormCatID HAVING COUNT(tm.TempMasterID) > 1
                            ) AS ft ON ft.FormCatID = tm.FormCatID 
                            ORDER BY FormCatID")->result_array();
        
        $data['templates'] = $temp;
        $view = $this->load->view('nav-setup/comapny-change-template', $data, true);
        echo json_encode( ['s', 'view'=> $view] );
    }

    function client_default_template_setup(){
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        
        $company_id = $this->input->post('company_id');
        $formCat = $this->input->post('formCat');

        $arr = []; $pc = current_pc(); $user_id = current_userID();
        foreach($formCat as $formID=> $templateID){
            $arr[] = [
                'TempMasterID'=> $templateID, 'FormCatID'=> $formID, 'ModifiedPC'=> $pc,  
                'modifiedUserID'=> $user_id, 'Timestamp'=> $this->date_time
            ];
        }

        $this->db->trans_start();

        $this->db->where('companyID', $company_id)->update_batch('srp_erp_templates', $arr, 'FormCatID');
        
        $this->db->trans_complete();
        if ($this->db->trans_status() == true){
            echo json_encode(['s', 'Successfully update the templates.']);
        }
        else {
            echo json_encode(['e', 'Error in template update process.']);
        }        
    }

    function load_template_management_view(){
        $id = $this->input->get('nav_id');

        $det = $this->db->select('n.description AS navDes, mas.description AS masterDes, 
                mod.description AS moduleDes, f.FormCatID')
            ->where('n.navigationMenuID', $id)
            ->join('srp_erp_formcategory AS f', 'f.navigationMenuID=n.navigationMenuID')            
            ->join('srp_erp_navigationmenus AS mas', 'mas.navigationMenuID=n.masterID')            
            ->join('srp_erp_navigationmenus AS mod', 'mod.navigationMenuID=n.moduleID')            
            ->get('srp_erp_navigationmenus AS n')->row_array();

        if(empty($det)){
            die( json_encode(['e', 'Details not found.']) );            
        }

        $data['det'] = $det;
        $view = $this->load->view('nav-setup/template-management-view', $data, true);

        echo json_encode(['s', 'view'=> $view]);
    }

    function fetch_nav_templates(){ 
        $formCatID = $this->input->post('formCatID');
        $str = '<div style="text-align: right"><button class="btn btn-default btn-xs" ';
        $str .= 'onclick="edit_template(this)"><i class="fa fa-pencil edit-icon"></i></button>';
        $str .= ' &nbsp; <button class="btn btn-default btn-xs" onclick="delete_template_conf($1, \'$2\')">';
        $str .= '<i class="fa fa-trash delete-icon"></i></button></div>';

        $this->datatables->select("TempMasterID, TempDes, TempPageName, TempPageNameLink, createPageLink, 
                FormCatID,IF(isDefault=1, 'Yes', 'No') AS isDefault, IF(isDefault=1, 'success', 'primary') AS dftCls")
            ->from('srp_erp_templatemaster') 
            ->where('FormCatID', $formCatID)            
            ->edit_column('isDefault', '<div style="text-align: center"><span class="label label-$2"> $1 </lable></div>', 'isDefault, dftCls');        
        $this->datatables->edit_column('action', $str, 'TempMasterID,TempPageName');
        echo $this->datatables->generate();
    }

    function template_save(){
        $this->form_validation->set_rules('formCatID', 'Form Category ID', 'trim|required');
        $this->form_validation->set_rules('nav_description', 'Navigation Description', 'trim|required');
        $this->form_validation->set_rules('template_name', 'Template Name', 'trim|required');
        $this->form_validation->set_rules('page_url', 'Page Url', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }
        
        $formCatID = $this->input->post('formCatID');
        $nav_description = $this->input->post('nav_description');
        $template_name = $this->input->post('template_name');
        $page_url = $this->input->post('page_url');
        $create_page_url = $this->input->post('create_page_url');

        $mainDB = $this->load->database('db2', TRUE);

        $isReport = $mainDB->select('isReport')
                        ->where(['FormCatID'=> $formCatID, 'isReport'=> 1])
                        ->get('srp_erp_templatemaster')->row('isReport');
        $isReport = (empty($isReport))? 0: 1;

        $data = [
            'TempDes'=> $nav_description, 'TempPageName'=> $template_name, 'TempPageNameLink'=> $page_url, 
            'createPageLink'=> $create_page_url, 'FormCatID'=> $formCatID, 'isReport'=> $isReport
        ];        
        
        $mainDB->trans_start();

        $mainDB->insert('srp_erp_templatemaster', $data);
        $data['TempMasterID'] = $mainDB->insert_id();

        $mainDB->trans_complete();
        if ($mainDB->trans_status() == true){
            $failed_db = $this->clientDB_template_CRUD('add', $data);
            $failed_db = ($failed_db)? implode('<br/> &nbsp; - &nbsp; ', $failed_db): null;
            
            echo json_encode(['s', 'Template added successfully.', 'failed_db'=> $failed_db]);
        }
        else {
            echo json_encode(['e', 'Failed to create template.']);
        }
    }

    function template_update(){
        $this->form_validation->set_rules('formCatID', 'Form Category ID', 'trim|required');        
        $this->form_validation->set_rules('templateID', 'Template Master ID', 'trim|required');        
        $this->form_validation->set_rules('template_name', 'Template Name', 'trim|required');
        $this->form_validation->set_rules('page_url', 'Page Url', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }
        
        $formCatID = $this->input->post('formCatID');
        $templateID = $this->input->post('templateID');
        $nav_description = $this->input->post('nav_description');
        $template_name = $this->input->post('template_name');
        $page_url = $this->input->post('page_url');
        $create_page_url = $this->input->post('create_page_url');

        $data = [ 
            'TempPageName'=> $template_name, 
            'TempPageNameLink'=> $page_url, 
            'createPageLink'=> $create_page_url
        ];

        $mainDB = $this->load->database('db2', TRUE);
        
        $mainDB->trans_start();

        $mainDB->where(['FormCatID'=> $formCatID, 'TempMasterID'=> $templateID])
            ->update('srp_erp_templatemaster', $data);
        
        $mainDB->trans_complete();
        if ($mainDB->trans_status() == true){
            $failed_db = $this->clientDB_template_CRUD('update', $data);
            $failed_db = ($failed_db)? implode('<br/> &nbsp; - &nbsp; ', $failed_db): null;
            echo json_encode(['s', 'Template updated successfully.', 'failed_db'=> $failed_db]);
        }
        else {
            echo json_encode(['e', 'Failed to update template details.']);
        }
    }

    function template_delete(){              
        $this->form_validation->set_rules('templateID', 'Template Master ID', 'trim|required');         
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }        
        
        $templateID = $this->input->post('templateID');

        $mainDB = $this->load->database('db2', TRUE);
        
        $mainDB->trans_start();
        
        $isDefault = $mainDB->select('isDefault')
                        ->where(['TempMasterID'=> $templateID, 'isDefault'=> 1])
                        ->get('srp_erp_templatemaster')->row('isDefault');

        if($isDefault){
            die( json_encode(['e', 'This template marked as default.<br/>You can not delete this template.']) );
        }
        
        $mainDB->where('TempMasterID', $templateID)->delete('srp_erp_templatemaster');
        
        $mainDB->trans_complete();
        if ($mainDB->trans_status() == true){
            $failed_db = $this->clientDB_template_CRUD('delete', $templateID);
            $failed_db = ($failed_db)? implode('<br/> &nbsp; - &nbsp; ', $failed_db): null;
            echo json_encode(['s', 'Template deleted successfully.', 'failed_db'=> $failed_db]);
        }
        else {
            echo json_encode(['e', 'Failed to delete template details.']);
        }
    }

    function clientDB_template_CRUD($type, $data){
        $clientDb = get_clientDB();
        
        $failed_db = [];
        $processed_db = [];
        foreach ($clientDb as $key => $com) {
            $dbHost = decryptData($com['host']);
            $dbName = decryptData($com['db_name']);
            $prDB = "{$dbHost}|{$dbName}";
                        
            if( in_array($prDB, $processed_db) ){
                continue;
            }

            $processed_db[] = $prDB;            
            
            $this->db_host = $dbHost;
            $this->db_name = $dbName;
            $this->db_password = decryptData($com['db_password']);
            $this->db_username = decryptData($com['db_username']);

            $this->datatables->set_database($this->get_db_array(true), FALSE, TRUE);

            $this->db->trans_start();

            
            if($type == 'add'){
                $this->db->insert('srp_erp_templatemaster', $data);     
            }

            if($type == 'update'){
                $formCatID = $this->input->post('formCatID');
                $templateID = $this->input->post('templateID');
                
                $this->db->where(['FormCatID'=> $formCatID, 'TempMasterID'=> $templateID])
                    ->update('srp_erp_templatemaster', $data);
            }

            if($type == 'delete'){
                $this->db->where('TempMasterID', $data)->delete('srp_erp_templatemaster');
            }
            
            $this->db->trans_complete();
            if ($this->db->trans_status() == false){
                $failed_db[] = $dbHost.' | '.$dbName;
            }
        }
 
        return $failed_db;
    }

    function undo_discharge(){
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('empID', 'Employee ID', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }
        
        $company_id = $this->input->post('company_id');
        $empID = $this->input->post('empID');

        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);

        // get final settlment status
        $status = $this->db->get_where('srp_erp_pay_finalsettlementmaster', ['empID'=> $empID])->row_array();
        if($status['confirmedYN'] == 1){
            $msg = "Final settlment document <b>[ {$status['documentCode']} ]</b> of this employee is already confirmed.";
            $msg .= '<br/>Referback the document and try again.';
            die( json_encode(['e', $msg]) );
        }
        
                
        // Check rejoin was perform with this record        
        $this->db->select('Ename2, ECode')->where('previousEmpID', $empID);
        $reJoinedEmp = $this->db->get('srp_employeesdetails')->row_array();        
        if (!empty($reJoinedEmp)) {
            $msg = "This employee is already rejoined with following name.<br/>";
            $msg .= "<b>{$reJoinedEmp['Ename2']} [ {$reJoinedEmp['ECode']} ]<b/>";
            die( json_encode(['e', $msg]) );
        }

        $empData = $this->db->get_where('srp_employeesdetails', ['EIdNo'=> $empID])->row_array();


        $data = [
            'isDischarged' => 0, 'isLeft' => 0, 'dischargedByEmpID' => null, 'dischargedDate' => null, 
            'DateLeft' => null, 'lastWorkingDate' => null, 'dischargedComment' => '', 'LeftComment' => '', 
            'ModifiedPC' => current_pc(), 'ModifiedUserName' => current_userName(), 'Timestamp' => date('Y-m-d H:i:s')
        ];

        if($this->input->post('verify') == 0){
            if($empData['userType'] == 1){
                //If this employee is a module user check active module user count
                $this->db->select('noOfUsers')->where('company_id', $company_id);
                $maxModuleUsers = $this->db->get('srp_erp_company')->row('noOfUsers');

                
                $this->db->select('COUNT(EIdNo) AS userCount')->where([
                    'Erp_companyID'=> $company_id, 'userType'=> 1, 'isDischarged'=> 0
                ]);
                $activeModuleUsers = $this->db->get('srp_employeesdetails')->row('userCount');
                
                if ($maxModuleUsers > 0 && $activeModuleUsers >= $maxModuleUsers) {
                    $msg = 'Since this user is a module user, ';
                    $msg .= 'Maximum allowed Module user count exceeded.<br/>';
                    $msg .= '<b>Do you want to activate this user as a basic user?</b>';
                    die( json_encode(['modEx', $msg, 'empID'=> $empID]));
                }
            }
        }
        else{
            $data['userType'] = 0;
        }

        $this->db->trans_start();
        
        $this->db->where('EIdNo', $empID)->update('srp_employeesdetails', $data);

        $fsID = $status['masterID'];
        $this->db->delete('srp_erp_pay_finalsettlementleavepaydetails', ['fsMasterID '=> $fsID]);
        $this->db->delete('srp_erp_pay_finalsettlementmoredetails', ['fsMasterID '=> $fsID]);
        $this->db->delete('srp_erp_pay_finalsettlementdetail', ['fsMasterID '=> $fsID]);    
        $this->db->delete('srp_erp_pay_finalsettlementmaster', ['masterID'=> $fsID]);

        $this->db->trans_complete();
        if ($this->db->trans_status() == true){            
            $msg = "{$empData['Ename2']} <b>[ {$empData['ECode']} </b> ] is reactivated successfully.";
            echo json_encode(['s', $msg]);
        }
        else {
            echo json_encode(['e', 'Error in employee reactivation process.']);
        }        
    }

    function activate_finance_year(){
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('id', 'Year ID', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }
        
        $company_id = $this->input->post('company_id');
        $yearID = $this->input->post('id');

        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);

        // get finance year status
        $status = $this->db->get_where('srp_erp_companyfinanceyear', ['companyFinanceYearID'=> $yearID])->row_array();

        if(empty($status)){
            die( json_encode(['e', 'Finance year data is not found.']) );
        }

        $yearStart = $status['beginingDate'];
        $yearEnd = $status['endingDate'];
        if($status['isClosed'] == 0){
            $msg = "Finance year <b>[ {$yearStart} - {$yearEnd} ]</b> is already in active status.";            
            die( json_encode(['e', $msg]) );
        } 

        $this->db->trans_start();
        $userID = current_userID();
        $userName = current_userName();
        $pc = current_pc();
        $this->db->where('companyFinanceYearID', $yearID)->update('srp_erp_companyfinanceyear', [
            'isActive'=> 1, 'isClosed'=> 0, 'closedByEmpID'=> null, 'closedByEmpName'=> null,
            'modifiedPCID'=> $pc, 'modifiedUserID'=> $userID,
            'modifiedUserName'=> $userName, 'modifiedDateTime'=> $this->date_time
        ]);

        
        $toDay = date('Y-m-d');

        /* Activate period that are less than or equal to current date */
        $this->db->where('companyFinanceYearID', $yearID)
        ->where("dateFrom <= '{$toDay}'")
        ->update('srp_erp_companyfinanceperiod', [
            'isActive'=> 1, 'isClosed'=> 0, 'closedByEmpID'=> null, 'closedByEmpName'=> '', 'closedDate'=> null,
            'modifiedPCID'=> $pc, 'modifiedUserID'=> $userID,
            'modifiedUserName'=> $userName, 'modifiedDateTime'=> $this->date_time
        ]);


        /* Reome close flag that are greatr than to current date */
        $this->db->where('companyFinanceYearID', $yearID)
        ->where("dateFrom > '{$toDay}'")
        ->update('srp_erp_companyfinanceperiod', [
            'isClosed'=> 0, 'closedByEmpID'=> null, 'closedByEmpName'=> '', 'closedDate'=> null,
            'modifiedPCID'=> $pc, 'modifiedUserID'=> $userID,
            'modifiedUserName'=> $userName, 'modifiedDateTime'=> $this->date_time
        ]);

        $this->db->trans_complete();
        if ($this->db->trans_status() == true){
            echo json_encode(['s', "Financial Year <b>[ {$yearStart} - {$yearEnd} ]</b>: Activated Successfully"]);
        }
        else {
            echo json_encode(['e', 'Failed to activate the finance year']);
        } 
    }
    
    function activate_finance_period(){
        $this->form_validation->set_rules('company_id', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('id', 'Period ID', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }
        
        $company_id = $this->input->post('company_id');
        $periodID = $this->input->post('id');
        $yearID = $this->input->post('id');

        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);

        // get finance year,period status
        $this->db->select('fy.companyFinanceYearID AS yearID, fy.isClosed AS yearClose, fy.beginingDate, 
            fy.endingDate, fp.dateFrom AS periodStart, fp.dateTo AS periodEnd, fp.isClosed AS periodClose')
        ->where('fp.companyFinancePeriodID', $periodID)
        ->join('srp_erp_companyfinanceyear AS fy', 'fp.companyFinanceYearID=fy.companyFinanceYearID');
        $status = $this->db->get('srp_erp_companyfinanceperiod AS fp')->row_array();
        
        if(empty($status)){
            die( json_encode(['e', 'Finance period data is not found.']) );
        }

        $yearStart = $status['beginingDate'];
        $yearEnd = $status['endingDate'];
        if($status['yearClose'] == 1){
            $msg = "Your trying to activate a period that's finance Year "; 
            $msg .= "<br/><b>[ {$yearStart} - {$yearEnd} ]</b> is already closed.";                   
            die( json_encode(['e', $msg]) );
        } 

        $periodStart = $status['periodStart'];
        $periodEnd = $status['periodEnd'];
        if($status['periodClose'] == 0){
            $msg = "Finance period <b>[ {$periodStart} - {$periodEnd} ]</b> is already in active status.";            
            die( json_encode(['e', $msg]) );
        }

        $toDay = date('Y-m-d');
        if($periodStart > $toDay){
            $msg = "Future finance period <b>[ {$periodStart} - {$periodEnd} ]</b> can not be active.";            
            die( json_encode(['e', $msg]) );
        }        

        $this->db->trans_start();         
        $this->db->where('companyFinancePeriodID', $periodID)            
            ->update('srp_erp_companyfinanceperiod', [
            'isActive'=> 1, 'isClosed'=> 0, 'closedByEmpID'=> null, 'closedByEmpName'=> '', 'closedDate'=> null,            
            'modifiedPCID'=> current_pc(), 'modifiedUserID'=> current_userID(),
            'modifiedUserName'=> current_userName(), 'modifiedDateTime'=> $this->date_time
        ]);

        $this->db->trans_complete();
        if ($this->db->trans_status() == true){
            echo json_encode(['s', "Financial period <b>[ {$periodStart} - {$periodEnd} ]</b>: Activated Successfully."]);
        }
        else {
            echo json_encode(['e', 'Failed to activate the finance period.']);
        } 
    }

    function cron_log(){
        $nav_modules = get_navigation_modules();
        $sub_nav = $this->get_sub_modules($nav_modules);
        $module_sort_order = $this->db->select('MAX(sortOrder) sortOrder')->where(['masterID'=> null])
                                ->get('srp_erp_navigationmenus')->row('sortOrder');
    
        $data['title'] = 'Cron Job Log';
        $data['main_content'] = 'cron-job-log/cron-job-view';
        $data['nav_modules'] = $nav_modules;
        $data['sub_nav'] = $sub_nav;
        $data['module_sort_order'] = ($module_sort_order + 1);
        $data['extra'] = null;
        
        $this->load->view('include/template', $data);        
    }

    function fetch_cron_log(){
        $frm_date = $this->input->post('frm_date');
        $to_date = $this->input->post('to_date');
        $paymentType = $this->input->post('paymentType');
        $inv_type = $this->input->post('inv_type');
 
        $this->datatables->select("id, msg, processed_qry, process_company_list, created_at")
            ->from('cron_job_log')
            ->where('adminType', current_userType());

        if( $frm_date ){
            $this->datatables->where("DATE(created_at) >= '{$frm_date}'");            
        }

        if( $to_date ){
            $this->datatables->where("DATE(created_at) <= '{$to_date}'");
        }
  

        $this->datatables->edit_column('com_name_list', '<div style="width: 200px">$1</div>', 'list_comnpay(process_company_list)');
        echo $this->datatables->generate();
    }

    function update_company_token(){
        $this->form_validation->set_rules('token_companyID', 'Company ID', 'trim|required');
        $this->form_validation->set_rules('supportToken', 'Support Token', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $com_id = $this->input->post('token_companyID');
        $supportToken = $this->input->post('supportToken');

        $this->db->trans_start();

        $data['supportToken'] =$supportToken;
        $this->db->where(['company_id'=>$com_id])->update('srp_erp_company', $data);

        $this->db->trans_complete();

        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Company token updated successfully']);
        }else{
            echo json_encode(['e', 'Error in company token update process.']);
        }
    }
}
