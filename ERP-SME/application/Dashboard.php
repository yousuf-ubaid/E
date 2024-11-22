<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    Private $main;
    private $db_name;
    private $db_username;
    private $db_password;
    private $db_host;

    function __construct()
    {
        parent::__construct();
        $CI =& get_instance();
        if (!$CI->session->has_userdata('status')) {
            header('Location: ' . site_url('login/logout'));
        }
        $this->load->model('Dashboard_model');
        $this->load->helper('configuration');
        $this->load->library('s3');

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

    function get_db_array()
    {
        $config['hostname'] = trim($this->encryption->decrypt($this->db_host));
        $config['username'] = trim($this->encryption->decrypt($this->db_username));
        $config['password'] = trim($this->encryption->decrypt($this->db_password));
        $config['database'] = trim($this->encryption->decrypt($this->db_name));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = TRUE;
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

    function load_Financial_year()
    {
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);
        $this->datatables->select("companyFinanceYearID,beginingDate,endingDate,comments,isActive,isCurrent,isClosed");
        $this->datatables->where('companyID', trim($this->input->post('company_id')));
        $this->datatables->from('srp_erp_companyfinanceyear');
        $this->datatables->add_column('financial_year', '<center> $1- $2 </center>', 'beginingDate,endingDate');
        $this->datatables->add_column('active_status', '$1', 'confirm(isActive)');
        $this->datatables->add_column('current_status', '$1', 'confirm(isCurrent)');
        $this->datatables->add_column('closed_status', '$1', 'confirm(isClosed)');
        $this->datatables->add_column('status', '$1', 'load_Financial_year_status(companyFinanceYearID,isActive)');
        $this->datatables->add_column('current', '$1', 'load_Financial_year_current(companyFinanceYearID,isCurrent)');
        $this->datatables->add_column('close', '$1', 'load_Financial_year_close(companyFinanceYearID,isClosed)');
        $this->datatables->add_column('action', '<span class="pull-right"><a onclick="openisactiveeditmodel($1)"><span class="glyphicon glyphicon-pencil"></span></a></span>', 'companyFinanceYearID');
        echo $this->datatables->generate('json', 'ISO-8859-1');
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
        $this->form_validation->set_rules('host', 'host', 'trim|required');
        $this->form_validation->set_rules('db_name', 'Database Name', 'trim|required');
        $this->form_validation->set_rules('db_username', 'Database User Name', 'trim|required');
        $this->form_validation->set_rules('db_password', 'Database Password', 'trim|required');
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

    function fetch_admin_users_dataTable()
    {
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);

        $this->datatables->select("Gender,ECode,UserName,Ename1,Ename2,EIdNo,description as userGroupID, EDOJ AS date_join, IF(Gender=1, 'Male', 'Female') AS gender_str")
            ->from('srp_employeesdetails')
            ->join('srp_erp_employeenavigation', 'srp_erp_employeenavigation.empID = srp_employeesdetails.EIdNo', 'left')
            ->join('srp_erp_usergroups', 'srp_erp_usergroups.userGroupID = srp_erp_employeenavigation.userGroupID', 'left')
            ->where('Erp_companyID', trim($this->input->post('company_id')))
            ->group_by("EIdNo")
            ->add_column('action', '$1', 'admin_users_action(EIdNo,userGroupID)');
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
        echo json_encode($this->Dashboard_model->save_addNewcurrencyExchange());
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
        $str = '<span><button type="button" onclick="loadCompanyAdminUsers($1)" class="btn btn-xs btn-danger">Request Admin Pin</button></span> &nbsp;';
        $str .= '<span class=""><a class="btn btn-xs btn-info" href="' . base_url() . 'index.php/companyAdmin/AddCompany/$1"><span class="glyphicon glyphicon-pencil"></span></a></span> ';
        $str .= '<span class=""><a class="btn btn-xs btn-info" href="' . base_url() . 'index.php/Dashboard/show_company_template/$1">';
        $str .= '<span class="glyphicon glyphicon-menu-hamburger"></span></a></span>';

        $this->datatables->set_database('db2');


        $this->datatables->select('company_id, company_code, company_name, company_logo, legalName, sub_status', true)
            ->from('company_subscription_view AS sub_view')
            ->where('adminType', current_userType());
        $this->datatables->add_column('company_detail', '<h4> $1 ( $2 ) <small>$3</small></h4>', 'company_name,company_code,legalName');
        $this->datatables->add_column('isDisabled_str', '$1', 'company_status_str(company_id,sub_status,company_code,company_name)');
        $this->datatables->add_column('img', "<center><img class='img-thumbnail' src='$2/$1' style='width:90px;height: 80px;'><center>", 'company_logo,server_path("images/logo/")');
        $this->datatables->add_column('edit', $str, 'company_id');
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
        $this->datatables->select('company_id, CONCAT( company_code, \'-\', company_name ) AS com_name, company_country, subscriptionNo, registeredDate, 
            subscriptionStartDate, nextRenewalDate,  lastRenewedDate, CurrencyCode, subscriptionAmount, subscriptionCurrency AS curr_id, company_code, company_name,
            FORMAT( subscriptionAmount, IFNULL(DecimalPlaces,2) ) AS subAmount, FORMAT( implementationAmount, IFNULL(DecimalPlaces,2) ) AS impAmount, 
            IF ( paymentEnabled = 1, \'Yes\', \'No\' ) AS paymentEnabled, company_email, company_phone, companyPrintTelephone, lastAccDate,
            IF( isSubscriptionExpire(company_id) > 0, 3, isSubscriptionDisabled) AS isSubscriptionDisabled')
            ->from('company_subscription_view AS sub_view')
            ->join("(SELECT companyID, MAX(createdDateTime) AS lastAccDate
                    FROM system_audit_log GROUP BY companyID) AS acc_tb", 'sub_view.company_id = acc_tb.companyID', 'left')
            ->where('adminType', current_userType());

        if($paymentType !== ''){
            $paymentType = explode(',', $paymentType);
            $this->datatables->where_in('IFNULL(paymentEnabled,0)', $paymentType);
        }

        if($subType !== ''){
            $subType = explode(',', $subType);
                $this->datatables->where_in('IF( isSubscriptionExpire(company_id) > 0, 3, isSubscriptionDisabled)', $subType);
        }

        $this->datatables->edit_column('com_name', '<div style="width: 200px">$1</div>', 'com_name')
            ->add_column('company_det', '<b>Email ID</b>: $1<br/><b>Phone No</b>: $2<br/><b>Mobile No</b>: $3<br/>', 'company_email,company_phone,companyPrintTelephone')
            ->edit_column('subscriptionAmount', '<div style="text-align: right">$1</div>', 'subAmount')
            ->edit_column('implementationAmount', '<div style="text-align: right">$1</div>', 'impAmount')
            ->edit_column('currencyCode', '<div style="text-align: center">$1</div>', 'CurrencyCode')
            ->edit_column('paymentEnabled', '<div style="text-align: center">$1</div>', 'paymentEnabled')
            ->add_column('subscription_status', '$1', 'company_status_str(company_id,isSubscriptionDisabled,company_code,company_name)')
            ->add_column('edit', '$1', 'subscription_action(company_id,com_name,subscriptionAmount,curr_id)');
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
        $is_paymentEnabled = $this->db->get_where('srp_erp_company', ['company_id'=>$company_id])->row('paymentEnabled');

        $sub_data = $this->db->query("SELECT * FROM (
                             -- Initial subscription 
                             SELECT his.subscriptionID, subscriptionStartDate, nextRenewalDate, lastRenewedDate, invNo, inv_data.invID,
                             FORMAT( IFNULL(invTotal, subscriptionAmount), 2 ) AS invoice_am, 1 AS inv_type, inv_data.isAmountPaid, his.dueDate,paymentType
                             FROM companysubscriptionhistory AS his 
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
                            inv_data.invID, FORMAT( IFNULL(invTotal, com_tb.implementationAmount), 2 ) AS invoice_am, 2 AS inv_type,
                            inv_data.isAmountPaid, '' AS dueDate ,paymentType
                            FROM companysubscriptionhistory AS his 
                            JOIN srp_erp_company AS com_tb ON com_tb.company_id = his.companyID
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
                             FORMAT( invTotal, 2 ) AS invoice_am, 0 AS inv_type, inv_data.isAmountPaid, his.dueDate,inv_data.paymentType
                             FROM companysubscriptionhistory AS his 
                             JOIN subscription_invoice_master AS inv_data ON inv_data.subscriptionID = his.subscriptionID	 
                             WHERE his.companyID = {$company_id} AND his.subscriptionID > (
                                  SELECT MIN(subscriptionID) FROM companysubscriptionhistory WHERE companyID = {$company_id}
                             ) ORDER BY his.subscriptionID 
                         ) t3 ")->result_array();


        echo json_encode(['s', 'att_data'=>$sub_data, 'is_paymentEnabled'=>$is_paymentEnabled]);
    }

    function load_invoice(){
        $inv_id = $this->input->post('inv_id');
        $inv_data = $this->Dashboard_model->load_invoice_view($inv_id);

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
                $link = $this->s3->getMyAuthenticatedURL("{$company_code}/subscription/".$row['fileName'], 3600);

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

        $data['inv_data'] = $inv_data;
        $data['att_view'] = $att_view;
        $data['inv_id'] = $inv_id;
        $data['view_type'] = 'V';
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

        $invNoData = $this->Dashboard_model->generate_subscription_inv_no(1);
        $serialNo = $invNoData['serialNo'];
        $invNo = $invNoData['inv_no'];
        $inv_id = null;

        $master_data = [
            'subscriptionID'=> $sub_id, 'invNo'=> $invNo, 'invDate'=>$inv_date, 'invCur'=> $currency,
            'invDecPlace'=> $dPlace, 'serialNo'=> $serialNo, 'invTotal'=> $amount, 'companyID'=> $company_id
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
            'invID'=> $inv_id, 'itemID'=> $itemID, 'itemDescription'=> $inv_det_des, 'amount'=> $amount, 'companyID'=> $company_id,
        ];

        $detail_id = null;
        foreach($detail as $column=>$new_val){
            $audit_log[] = [
                'tableName' => 'subscription_invoice_details', 'columnName'=> $column, 'old_val'=> '',
                'display_old_val'=> '', 'new_val'=> $new_val, 'display_new_val'=> $new_val,
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
            $this->load->helper('host');



           $company = $this->db->query("SELECT confirmedYN FROM srp_erp_company WHERE company_id = {$company_id}")->row('confirmedYN');
           if($company == 1){
                foreach ($int_arr as $mailData){
                    send_subscription_mail($mailData);
                    //echo $this->load->view('email_subscription_template', $mailData, true);
                    //echo '<pre>'; print_r($mailData); echo '</pre>';
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

        $master_data = $this->db->query("SELECT sub_his.subscriptionID AS sub_id, sub_his.companyID, sub_his.nextRenewalDate, com_tb.subscriptionAmount,
                                  com_tb.implementationAmount, cur_mas.currencyID, cur_mas.CurrencyCode, cur_mas.DecimalPlaces AS invDecPlace, 
                                  com_tb.company_name, companyPrintAddress, company_email, com_tb.isInitialSubscriptionConfirmed, sub_his.dueDate
                                  FROM srp_erp_company AS com_tb
                                  JOIN (
                                      SELECT subscriptionID, companyID, nextRenewalDate, dueDate FROM companysubscriptionhistory
                                      WHERE subscriptionID = {$sub_id} 
                                  ) AS sub_his ON sub_his.companyID = com_tb.company_id
                                  JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID=com_tb.subscriptionCurrency")->row_array();

        if($master_data['isInitialSubscriptionConfirmed'] == 0){
            die( json_encode(['e', 'Subscription details not confirmed yet']) );
        }

        $companyID = $master_data['companyID'];
        $invoice_status = $this->db->query("SELECT invNo FROM companysubscriptionhistory AS his 
                                            JOIN (
                                                SELECT subscriptionID, in_mas.invID, invNo, invTotal 
                                                FROM subscription_invoice_master AS in_mas 
                                                JOIN subscription_invoice_details AS in_det ON in_mas.invID = in_det.invID
                                                WHERE in_mas.companyID = {$companyID}  AND in_det.itemID = {$itemID}
                                                ORDER BY subscriptionID LIMIT 1 
                                            ) AS inv_data ON inv_data.subscriptionID = his.subscriptionID 
                                            WHERE companyID = {$companyID} AND his.subscriptionID = (
                                                SELECT MIN(subscriptionID) FROM companysubscriptionhistory WHERE companyID = {$companyID}
                                            )")->row('invNo');

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
            echo json_encode(['s', 'Due date Updated successfully.']);
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
        $date_time =  date('Y-m-d H:i:s');

        $data = [
            'isSubscriptionDisabled'=> $status, 'modifiedPCID'=> current_pc(), 'modifiedUserID'=> current_userID(),
            'modifiedDateTime'=> $date_time, 'timestamp'=> $date_time
        ];

        $this->db->trans_start();

        $this->db->where(['company_id'=>$com_id])->update('srp_erp_company', $data);

        $int_data = [
            'subStatus'=> $status, 'comment'=> $sub_comment, 'companyID'=> $com_id, 'createdPCID'=> current_pc(),
            'createdUserID'=> current_userID(), 'createdDateTime'=> $date_time, 'timestamp'=> $date_time
        ];
        $this->db->insert('subscription_status_history', $int_data);

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

        $this->db->trans_start();

        $this->db->where(['company_id'=>$com_id])->update('srp_erp_company', $data);

        $old_val = ($payEnable == 1)? 0: 1;
        $audit_log = [
            'tableName' => 'srp_erp_company', 'columnName'=> 'paymentEnabled', 'old_val'=> $old_val,
            'display_old_val'=> $old_val, 'new_val'=> $payEnable, 'display_new_val'=> $payEnable, 'rowID'=> $com_id,
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

                $link = $this->s3->getMyAuthenticatedURL("{$company_code}/subscription/".$row['fileName'], 3600);
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
        $sub = $this->db->select('CONCAT( company_code, \'-\', company_name ) AS com_name, company_country, subscriptionNo, registeredDate, 
            subscriptionStartDate, nextRenewalDate,  lastRenewedDate, CurrencyCode, subscriptionCurrency AS curr_id,
            FORMAT( subscriptionAmount, IFNULL(DecimalPlaces,2) ) AS subAmount, FORMAT( implementationAmount, IFNULL(DecimalPlaces,2) ) AS impAmount, 
            IF ( paymentEnabled = 1, \'Yes\', \'No\' ) AS paymentEnabled, company_email, company_phone, companyPrintTelephone, lastAccDate,
            IF( isSubscriptionExpire(company_id) > 0, 3, isSubscriptionDisabled) AS isSubscriptionDisabled')
            ->from('company_subscription_view AS sub_view')
            ->join("(SELECT companyID, MAX(createdDateTime) AS lastAccDate
                    FROM system_audit_log GROUP BY companyID) AS acc_tb", 'sub_view.company_id = acc_tb.companyID', 'left')
            ->where('adminType', current_userType())->get()->result_array();


        $header = [
            '#', 'Company Name', 'Email ID', 'Phone No', 'Mobile No', 'Country', 'Subscription ID', 'Registered Date',
            'Subscription Start Date', 'Subscription Amount', 'Implementation Amount', 'Next Renewal Date', 'Last Renewed Date',
            'Currency', 'Subscription', 'Payment Enabled', 'Last Access Date',
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

            $det[] = [
                $i,
                $row['com_name'], $row['company_email'], $row['company_phone'], $row['companyPrintTelephone'], $row['company_country'],
                $row['subscriptionNo'], $row['registeredDate'], $row['subscriptionStartDate'], $row['subAmount'], $row['impAmount'],
                $row['nextRenewalDate'], $row['lastRenewedDate'], $row['CurrencyCode'], $subscription_status, $row['paymentEnabled'], $row['lastAccDate'],
            ];
            $i++;
        }

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Company Subscription');
        $styleArray = [
            'font' => ['bold' => true, 'size' => 13, 'name' => 'Calibri']
        ];

        $this->excel->getActiveSheet()->getStyle('A1:Q1')->applyFromArray($styleArray);
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

        $this->datatables->select("com.company_id AS company_id, company_name, company_code, Fullname, tableName, columnName, 
                                rowID, display_old_val, display_new_val, lg_tb.`timestamp` AS log_time")
            ->from('srp_erp_audit_log AS lg_tb')
            ->join("srp_erp_company AS com", 'com.company_id = lg_tb.companyID')
            ->join("srp_erp_companyadminusers AS ad_tb", 'ad_tb.UserID = lg_tb.userID');
        if(!empty($column_drop)){
            $this->datatables->join("srp_erp_audit_display_columns AS aud_col", 'aud_col.tbl_name = lg_tb.tableName 
                    AND aud_col.col_name = lg_tb.columnName ');
        }
        $this->datatables->where('ad_tb.adminType', current_userType())->where("DATE(lg_tb.`timestamp`) BETWEEN '{$from}' AND '{$to}'");
        if(!empty($column_drop)){
            $column_drop = explode(',', $column_drop);
            $this->datatables->where_in("aud_col.id", $column_drop);
        }

        if(!empty($company_drop)){
            $company_drop = explode(',', $company_drop);
            $this->datatables->where_in("com.company_id", $company_drop);
        }
        $this->datatables->edit_column('company_name', '$1 ( $2 )', 'company_name, company_code')
            ->edit_column('tableName', '$1.$2', 'tableName, columnName');
        echo $this->datatables->generate();
    }

    function fetch_company_warehouse(){
        $this->datatables->set_database($this->get_db_array(), FALSE, TRUE);

        $this->datatables->select("wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation,
        IF(isActive=1, 'ACTIVE', 'INACTIVE') AS isActive_str, IF(isActive=1, 'success', 'danger') AS class ");
        $this->datatables->from('srp_erp_warehousemaster')
        ->where('companyID', trim($this->input->post('company_id')))
        ->where('isPosLocation', 1)
        ->add_column('wr_status', '<div align="center"><span class="label label-$2">$1</span></div>', 'isActive_str, class');
        echo $this->datatables->generate('json', 'ISO-8859-1');
    }

    function fetch_company_subscription_history(){
        $company_id = trim($this->input->post('company_id'));
        $this->datatables->select("id, Fullname, createdDateTime, comment, subStatus, labelClass");
        $this->datatables->from("subscription_status_history_view AS his")
            ->join('srp_erp_companyadminusers AS us', 'us.UserID=his.createdUserID')
            ->add_column('sub_status', '<div align="center"><span class="label label-$1">$2</span></div>', 'labelClass,subStatus');
        echo $this->datatables->generate('json', 'ISO-8859-1');
    }

    function latest_history(){
        $company_id = trim($this->input->post('company_id'));

        $comment = $this->db->query("SELECT `comment` FROM subscription_status_history WHERE companyID = {$company_id} 
                                                ORDER BY id DESC LIMIT 1")->row('comment');

        echo json_encode(['comment'=> $comment]);

    }
}
