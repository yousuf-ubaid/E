<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    Private $main;


    public function index($employee_code = NULL, $message = NULL, $type = 'e', $isGroupUser = 0)
    {
        $session_data = null;
        $this->load->model('session_model');
        if ($employee_code) {
            if (false === empty($this->session->userdata('e_empID')) && md5($this->session->userdata('e_empID')) == $employee_code) {
                redirect('dashboard');
            } else {
                $session_data = $this->session_model->createSession($employee_code, $isGroupUser);

                if ($session_data['stats']) {
                    $this->session->set_flashdata('s', $this->session->userdata('e_empname') . ' Successfully logged into System');
                    redirect('dashboard');
                } else {
                    $this->no_permission($session_data['message']);
                }
            }
        } else {
            $data['title'] = 'Login';
            $data['extra'] = $message;
            $data['type'] = $type;
            $this->load->view(getLoginPage(), $data);
        }
    }

    function forget_password()
    {
        $data['title'] = 'Forget Password';
        $data['extra'] = NULL;
        $data['type'] = 'e';
        $this->load->view('forget_password', $data);
    }

    public function login_pin($id)
    {
        $this->load->model('session_model');
        if ($this->session->userdata('e_empID')) {
            redirect('dashboard');
        } else {
            $this->no_permission_pin($id);
        }

    }

    function no_permission($extra='')
    {
        $this->application_session_destroy();
        $data['title'] = 'Login';
        $data['extra'] = $extra;
        $data['type'] = 'e';
        $this->load->view(getLoginPage(), $data);
    }

    function no_permission_pin($id)
    {
        $this->application_session_destroy();
        $data['title'] = 'Login';
        $data['extra'] = NULL;
        $data['adminMasterID'] = $id;
        $this->load->view('login_pin_page', $data);
    }

    function no_permission_forgot_password()
    {
        $this->application_session_destroy();
        $data['title'] = 'Reset Password';
        $data['extra'] = NULL;
        $this->load->view('reset_password', $data);
    }

    function session_expaide()
    {
        echo "session_expaide";
    }

    public function logout()
    {

        $companyID = $this->session->userdata('companyID');
        $userID = trim($this->session->userdata("empID") ?? '');
        $username = trim($this->session->userdata("username") ?? '');
        $this->setDb();
        $companyInfo = get_companyInformation($companyID);
        $data['title'] = 'Login';
        $data['extra'] = NULL;

        $db2 = $this->load->database('db2', TRUE);
        if($companyID){
            $dataAdit['empID'] = $userID;
            $dataAdit['transactionType'] = 2;
            $dataAdit['companyID'] = $companyID;
            $dataAdit['remarks'] = 'Logged out from the system';
            $dataAdit['createdUserID'] = $userID;
            $dataAdit['createdUserName'] = $username;
            $dataAdit['createdPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $dataAdit['createdDateTime'] = fetch_current_time_by_timezone($companyID);
            $db2->insert('system_audit_log', $dataAdit);
        }


        $this->application_session_destroy();
        if (is_array($companyInfo) && $companyInfo['productID'] == 2) {
            header('Location:' . site_url('gears'));
            exit;
        } else {
            if(hstGeras==1){
                header('Location:' . site_url('gears'));
                exit;
            }else{
                $this->load->view(getLoginPage(), $data);
            }
        }
    }

    protected function setDb()
    {
        $companyID = $this->session->userdata('companyID');
        $companyInfo = get_companyInformation($companyID);
        if (!empty($companyInfo)) {
            $config['hostname'] = trim($this->encryption->decrypt($companyInfo["host"]));
            $config['username'] = trim($this->encryption->decrypt($companyInfo["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($companyInfo["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($companyInfo["db_name"]));
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
            $this->load->database($config, FALSE, TRUE);
        }
    }

    public function gears()
    {
        $this->application_session_destroy();
        $data['title'] = 'Login';
        $data['extra'] = NULL;
        $this->load->view('login_page_2', $data);
    }


    function session_status()
    {
        $output = ($this->session->userdata("empID")) ? 1 : 0;
        echo json_encode(array('status' => $output, 'csrf' => $this->security->get_csrf_hash()));
    }

    function company_configuration()
    {
        $data['title'] = 'Welcome Dashboard';
        $data['main_content'] = 'system/configuration/company_configuration';
        $data['extra'] = NULL;
        $this->load->view('include/template', $data);
    }

    function loginSubmit()
    {
        if($this->input->get('token')){
            $token = $this->input->get('token');

            $this->db->select('*');
            $this->db->where("login_token", $token);
            $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $resultDb2 = $this->db->get("user")->row_array();
            if(empty($resultDb2)){
                $data['title'] = 'Login';
                $data['type'] = 'e';
                $data['extra'] = 'Invalid token';
                $this->load->view(getLoginPage(), $data);
                return false;
            }
            else{
                $login_data['userN'] = $resultDb2['Username'];
                $this->login_setup($resultDb2, $login_data, $token);
            }
        }
        else{
            $this->encryption->initialize(array('driver' => 'mcrypt'));
            $this->load->model('session_model');
            $this->form_validation->set_rules('Username', 'Username', 'trim|required');
            $this->form_validation->set_rules('Password', 'Password', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $this->index(FALSE, validation_errors());
            } else {
                $login_data['userN'] = $this->input->post('Username');
                $login_data['passW'] = md5($this->input->post('Password'));
                $this->db->select('*');
                $this->db->where("UserName", $login_data['userN']);
                $this->db->where("Password", $login_data['passW']);
                $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
                $resultDb2 = $this->db->get("user")->row_array();
                $this->login_setup($resultDb2, $login_data);
            }
        }

    }

    function login_setup($resultDb2, $login_data, $token=null,$isspurgo=null){
        $result = "";
        $this->load->model('session_model');
        if ($resultDb2) {
            if ($resultDb2['isSubscriptionDisabled'] == 1) { //if company is disabled
                $data['title'] = 'Login';
                $data['type'] = 'e';
                $data['extra'] = 'Subscription is disabled';
                $this->load->view(getLoginPage(), $data);
                return false;
            }

            if ($resultDb2['isGroupUser'] == 1) {
                $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
                $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
                $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
                $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
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
                $this->load->database($config, FALSE, TRUE);
                $result = $this->session_model->authenticateLogin($login_data, $token);
                if($isspurgo == 1)
                {
                    /************Spur Go Activation Start************/
                    $this->db->query("UPDATE srp_employeesdetails AS mas_tb JOIN (SELECT EIdNo from srp_employeesdetails where Erp_companyID = {$resultDb2['companyID']}) AS empTB ON mas_tb.EIdNo = empTB.EIdNo
                                       SET mas_tb.isActive = 1");
                    /************Spur Go Activation End************/
                }
                if ($result['stats']) {
                    $subscription_Exp = $this->session_model->check_subscription_status($resultDb2['companyID']);
                    if($subscription_Exp[0]!='e')
                    {
                        $emp_where = [ 'Erp_companyID'=> $resultDb2['companyID'], 'EIdNo'=> $resultDb2['empID'] ];
                        $this->db->select('EIdNo,Erp_companyID,Ename2');
                        $this->db->where($emp_where);                    
                        $employeesdetail = $this->db->get("srp_employeesdetails")->row_array();
                        $date_time = date('Y-m-d H:i:s');

                        $db2 = $this->load->database('db2', TRUE);

                        $dataAdit['empID'] = $employeesdetail['EIdNo'];
                        $dataAdit['transactionType'] = 0;
                        $dataAdit['companyID'] = $employeesdetail['Erp_companyID'];
                        $dataAdit['remarks'] = 'Logged In to system';
                        $dataAdit['createdUserID'] = $employeesdetail['EIdNo'];
                        $dataAdit['createdUserName'] = $employeesdetail['Ename2'];
                        $dataAdit['createdPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                        $dataAdit['createdDateTime'] = fetch_current_time_by_timezone( $employeesdetail['Erp_companyID']);
                        $db2->insert('system_audit_log', $dataAdit);

                        $this->db->where($emp_where)->update('srp_employeesdetails', ['last_login'=> $date_time]);
                    }

                    $this->index($result['data'], NULL, 'e', $resultDb2['isGroupUser']);
                } else {
                    $this->index(FALSE, $result['message']);
                }

            }
            else {

                $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
                $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
                $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
                $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
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
                $this->load->database($config, FALSE, TRUE);
                if($isspurgo == 1)
                {
                    /************Spur Go Activation Start************/
                    $this->db->query("UPDATE srp_employeesdetails AS mas_tb JOIN (SELECT EIdNo from srp_employeesdetails where Erp_companyID = {$resultDb2['companyID']}) AS empTB ON mas_tb.EIdNo = empTB.EIdNo
                                       SET mas_tb.isActive = 1");
                    /************Spur Go Activation End************/
                }
                $result = $this->session_model->authenticateLogin($login_data, $token);

                if ($result['stats']) {
                    $subscription_Exp = $this->session_model->check_subscription_status($resultDb2['companyID']);
                    if($subscription_Exp[0]!='e')
                    {
                        $date_time = date('Y-m-d H:i:s');
                        $emp_where = [ 'Erp_companyID'=> $resultDb2['companyID'], 'EIdNo'=> $resultDb2['empID'] ];

                        $this->db->select('EIdNo,Erp_companyID,Ename2');
                        $this->db->where($emp_where);                        
                        $employeesdetail = $this->db->get("srp_employeesdetails")->row_array();


                        $db2 = $this->load->database('db2', TRUE);
                        $dataAdit['empID'] = $employeesdetail['EIdNo'];
                        $dataAdit['transactionType'] = 0;
                        $dataAdit['companyID'] = $employeesdetail['Erp_companyID'];
                        $dataAdit['remarks'] = 'Logged In to system';
                        $dataAdit['createdUserID'] = $employeesdetail['EIdNo'];
                        $dataAdit['createdUserName'] = $employeesdetail['Ename2'];
                        $dataAdit['createdPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                        $dataAdit['createdDateTime'] = fetch_current_time_by_timezone($employeesdetail['Erp_companyID']);
                        $db2->insert('system_audit_log', $dataAdit);

                        $this->db->where($emp_where)->update('srp_employeesdetails', ['last_login'=> $date_time]);
                    }

                    $this->index($result['data'], NULL);
                } else {
                    $this->index(FALSE, $result['message']);
                }
            }
        }
        else {
            $this->db->select('*');
            $this->db->where("UserName", $login_data['userN']);
            $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $usernameDb2 = $this->db->get("user")->row_array();

            if (is_array($usernameDb2) && $usernameDb2['isSubscriptionDisabled'] == 1) { //if company is disabled
                $data['title'] = 'Login';
                $data['type'] = 'e';
                $data['extra'] = 'Subscription is disabled';
                $this->load->view(getLoginPage(), $data);
                return false;
            }

            if (is_array($usernameDb2)) {
                $config['hostname'] = trim($this->encryption->decrypt($usernameDb2["host"]));
                $config['username'] = trim($this->encryption->decrypt($usernameDb2["db_username"]));
                $config['password'] = trim($this->encryption->decrypt($usernameDb2["db_password"]));
                $config['database'] = trim($this->encryption->decrypt($usernameDb2["db_name"]));
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
                $this->load->database($config, FALSE, TRUE);
                if($isspurgo == 1)
                {
                    /************Spur Go Activation Start************/
                    $this->db->query("UPDATE srp_employeesdetails AS mas_tb JOIN (SELECT EIdNo from srp_employeesdetails where Erp_companyID = {$resultDb2['companyID']}) AS empTB ON mas_tb.EIdNo = empTB.EIdNo
                                       SET mas_tb.isActive = 1");
                    /************Spur Go Activation End************/
                }
                $result = $this->session_model->authenticateLoginUserName($login_data);

                if ($result['stats']) {
                    $this->index($result['data']);
                } else {
                    $this->index(FALSE, $result['message']);
                }
            }
            else {
                $data['title'] = 'Login';
                $data['type'] = 'e';
                $data['extra'] = 'Wrong user name or password. Please  try again.';
                $this->load->view(getLoginPage(), $data);
            }
        }
    }


    function loginSubmit_gears()
    {
        $this->encryption->initialize(array('driver' => 'mcrypt'));
        $this->load->model('session_model');
        $this->form_validation->set_rules('Username', 'Username', 'trim|required');
        $this->form_validation->set_rules('Password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Login';
            $data['type'] = 'e';
            $data['extra'] = 'Please enter the username & password.';
            $this->load->view('login_page_2', $data);
        } else {
            $login_data['userN'] = $this->input->post('Username');
            $login_data['passW'] = md5($this->input->post('Password'));
            $this->db->select('*');
            $this->db->where("UserName", $login_data['userN']);
            $this->db->where("Password", $login_data['passW']);
            $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $resultDb2 = $this->db->get("user")->row_array();
            $result = "";
            if ($resultDb2) {
                $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
                $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
                $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
                $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
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
                $this->load->database($config, FALSE, TRUE);
                $result = $this->session_model->authenticateLogin($login_data);

                $companyID = $resultDb2['company_id'];
                $this->db->select('productID');
                $this->db->from('srp_erp_company');
                $this->db->where('company_id', $companyID);
                $productID = $this->db->get()->row('productID');
                if ($result['stats']) {
                    if ($productID == 2) {
                        $subscription_Exp = $this->session_model->check_subscription_status($resultDb2['companyID']);
                        if($subscription_Exp[0]!='e')
                        {
                            $date_time = date('Y-m-d H:i:s');
                            $emp_where = [ 'Erp_companyID'=> $resultDb2['companyID'], 'EIdNo'=> $resultDb2['empID'] ];
                            $this->db->select('EIdNo,Erp_companyID,Ename2');                            
                            $this->db->where($emp_where);                            
                            $employeesdetail = $this->db->get("srp_employeesdetails")->row_array();


                            $db2 = $this->load->database('db2', TRUE);
                            $dataAdit['empID'] = $employeesdetail['EIdNo'];
                            $dataAdit['transactionType'] = 0;
                            $dataAdit['companyID'] = $employeesdetail['Erp_companyID'];
                            $dataAdit['remarks'] = 'Logged In to system';
                            $dataAdit['createdUserID'] = $employeesdetail['EIdNo'];
                            $dataAdit['createdUserName'] = $employeesdetail['Ename2'];
                            $dataAdit['createdPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
                            $dataAdit['createdDateTime'] = fetch_current_time_by_timezone($employeesdetail['Erp_companyID']);
                            $db2->insert('system_audit_log', $dataAdit);
 

                            $this->db->where($emp_where)->update('srp_employeesdetails', ['last_login'=> $date_time]);
                        }

                        $this->index($result['data'], NULL, 'e', $resultDb2['isGroupUser']);
                    } else {
                        $data['title'] = 'Login';
                        $data['type'] = 'e';
                        $data['extra'] = 'You are not authorize to use this product.';
                        $this->load->view('login_page_2', $data);
                    }

                } else {
                    $data['title'] = 'Login';
                    $data['type'] = 'e';
                    $data['extra'] = $result['message'];
                    $this->load->view('login_page_2', $data);
                }


            } else {
                $this->db->select('*');
                $this->db->where("UserName", $login_data['userN']);
                //$this->db->where("Password", $login_data['passW']);
                $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
                $usernameDb2 = $this->db->get("user")->row_array();


                if ($usernameDb2) {
                    $config['hostname'] = trim($this->encryption->decrypt($usernameDb2["host"]));
                    $config['username'] = trim($this->encryption->decrypt($usernameDb2["db_username"]));
                    $config['password'] = trim($this->encryption->decrypt($usernameDb2["db_password"]));
                    $config['database'] = trim($this->encryption->decrypt($usernameDb2["db_name"]));
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
                    $this->load->database($config, FALSE, TRUE);
                    $result = $this->session_model->authenticateLoginUserName($login_data);

                    $companyID = $resultDb2['company_id'];
                    $this->db->select('productID');
                    $this->db->from('srp_erp_company');
                    $this->db->where('company_id', $companyID);
                    $productID = $this->db->get()->row('productID');
                    if ($result['stats']) {
                        if ($productID == 2) {

                            $this->index($result['data'], NULL);
                        } else {
                            $data['title'] = 'Login';
                            $data['type'] = 'e';
                            $data['extra'] = 'You are not authorize to use this product.';
                            $this->load->view('login_page_2', $data);
                        }

                    } else {
                        $data['title'] = 'Login';
                        $data['type'] = 'e';
                        $data['extra'] = $result['message'];
                        $this->load->view('login_page_2', $data);
                    }


                } else {
                    $data['title'] = 'Login';
                    $data['type'] = 'e';
                    $data['extra'] = 'Wrong user name or password. Please  try again.';
                    $this->load->view('login_page_2', $data);
                }

            }
        }
    }

    function forgetPasswordSubmit()
    {
        $this->load->library('email_manual');

        $this->form_validation->set_rules('email', 'email', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Login';
            $data['extra'] = validation_errors();
            $this->load->view('forget_password', $data);
        } else {
            $this->db->select('*');
            $this->db->where("email", $this->input->post('email'));
            $result = $this->db->get("user")->row_array();

            if ($result) {
                $PIN = rand(10000, 99999);
                $encryptValue = trim(sha1($PIN));
                $param['randNum'] = trim($encryptValue);
                $param['id'] = trim($result["empID"]);
                $param['autoID'] = trim($result["EidNo"]);
                $update = $this->db->where("email", $this->input->post('email'))->update('user', array('randNum' => trim($encryptValue)));
                
                if ($update) {
                    $element_arr = array();

                    $param['emailSubject'] = 'Forgot Password';
                    $param['empEmail'] = $this->input->post('email');
                    $param['empName'] = $result['Username'];
                    $param['companyID'] = $result['companyID'];
                    $param['empID'] = $result['empID'];
                    $param['type'] = 'ForgotPassword';

                    $element_arr[] = $param;

                    $result = $this->email_manual->set_email_forgot_email($element_arr);

                    if ($result) {
                        $data['title'] = 'Login';
                        $data['extra'] = 'An email has been sent to your mail inbox, Use the password reset link in the mail to reset your password';
                        $data['type'] = 's';
                        $this->load->view('forget_password', $data);
                    } else {
                        $data['title'] = 'Login';
                        $data['type'] = 'e';
                        $data['extra'] = 'Error occurred in email sending';
                        $this->load->view('forget_password', $data);
                    }
                } else {
                    $data['title'] = 'Login';
                    $data['type'] = 'e';
                    $data['extra'] = 'Error occurred';
                    $this->load->view('forget_password', $data);
                }
            } else {
                $data['title'] = 'Login';
                $data['extra'] = 'Your email is not registered with the system';
                $data['type'] = 'e';
                $this->load->view('forget_password', $data);
            }
        }
    }

    function loginPinSubmit()
    {
        $this->encryption->initialize(array('driver' => 'mcrypt'));
        $this->load->model('session_model');
        $this->form_validation->set_rules('pinNumber', 'Pin Number', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', validation_errors());
            redirect('pin_login/' . $this->input->post('adminMasterID'));
        } else {

            $this->db->select('*');
            $this->db->where("adminMasterID", $this->input->post('adminMasterID'));
            $this->db->where("pinNumber", $this->input->post('pinNumber'));
            $pinRec = $this->db->get("srp_erp_companyadminmaster")->row_array();
            if ($pinRec) {
                $this->db->select('*');
                $this->db->where("isSystemAdmin", 1);
                $this->db->where("user . companyID", $pinRec["companyID"]);
                $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
                $resultDb2 = $this->db->get("user")->row_array();
                $result = "";
                if ($resultDb2) {
                    $login_data['userN'] = $resultDb2['Username'];
                    $login_data['passW'] = $resultDb2['Password'];
                    $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
                    $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
                    $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
                    $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
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
                    $this->load->database($config, FALSE, TRUE);
                    $result = $this->session_model->authenticateLogin($login_data);
                }
                if ($result['stats']) {
                    $this->main = $this->load->database('db2', TRUE);
                    $this->main->set('pinNumber', null);
                    $this->main->where("adminMasterID", $this->input->post('adminMasterID'));
                    $this->main->update("srp_erp_companyadminmaster");
                    $this->index($result['data'], NULL);
                } else {
                    $this->session->set_flashdata('msg', 'Error Occurred');
                    redirect('pin_login/' . $this->input->post('adminMasterID'));
                }
            } else {
                $this->session->set_flashdata('msg', 'Invalid PIN Number');
                redirect('pin_login/' . $this->input->post('adminMasterID'));
            }
        }
    }

    function reset_password($randNum, $empID, $autoID)
    {
        $password = $this->input->post('Password');
        if (isset($password)) {
            $this->form_validation->set_rules('Password', 'Password', 'trim|required');
            $this->form_validation->set_rules('ConfirmPassword', 'Confirm Password', 'trim|required|matches[Password]');
            if ($this->form_validation->run() == FALSE) {
                $data['title'] = 'Login';
                $data['extra'] = validation_errors();
                $this->load->view('reset_password', $data);
            } else {
                $this->db->select('*');
                $this->db->where("randNum", $randNum);
                $this->db->where("empID", $empID);
                $this->db->where("EidNo", $autoID);
                $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
                $result = $this->db->get("user")->row_array();
                if ($result) {
                    $update = $this->db->where("EidNo", $autoID)->update('user', array('randNum' => null, 'Password' => md5($this->input->post('Password'))));
                    if ($update) {
                        $login_data['userN'] = $result['Username'];
                        $login_data['passW'] = $result['Password'];
                        $config['hostname'] = trim($this->encryption->decrypt($result["host"]));
                        $config['username'] = trim($this->encryption->decrypt($result["db_username"]));
                        $config['password'] = trim($this->encryption->decrypt($result["db_password"]));
                        $config['database'] = trim($this->encryption->decrypt($result["db_name"]));
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
                        $this->load->database($config, FALSE, TRUE);
                        $updateEmp = $this->db->where("EidNo", $empID)->update('srp_employeesdetails', array('Password' => md5($this->input->post('Password'))));
                        if ($updateEmp) {
                            $this->session->set_flashdata('msg', 'Successfully Password Changed');
                            redirect('LoginPage');
                        } else {
                            $data['title'] = 'Login';
                            $data['extra'] = 'Error Occurred';
                            $data['type'] = 'e';
                            $this->load->view('reset_password', $data);
                        }
                    }
                } else {
                    $data['title'] = 'Login';
                    $data['extra'] = 'Invalid Token';
                    $data['type'] = 'e';
                    $this->load->view('reset_password', $data);
                }
            }
        } else {
            $Session_data = null;
            $this->load->model('session_model');
            if ($this->session->userdata('e_empID')) {
                redirect('dashboard');
            } else {
                $this->no_permission_forgot_password();
            }
        }
    }

    public function under_construction()
    {
        $this->application_session_destroy();
        $data['title'] = 'Login';
        $data['extra'] = NULL;
        $this->load->view('site_under_construction', $data);

    }
    public function connection(){
        $companyID = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $companyInfo = get_companyInformation($companyID);
        $dbConfig="";
        if (!empty($companyInfo)) {
            $hostname = trim($this->encryption->decrypt($companyInfo["host"]));
            $username = trim($this->encryption->decrypt($companyInfo["db_username"]));
            $password = trim($this->encryption->decrypt($companyInfo["db_password"]));
            $database = trim($this->encryption->decrypt($companyInfo["db_name"]));

            $dbConfig=$hostname.'|'.$username.'|'.$password.'|'.$database;
            $dbConfig= base64_encode(base64_encode($dbConfig));
        }
        echo $dbConfig;
    }

    public function privacy_policy(){
        $this->load->view('privacy-policy');
    }

    function application_session_destroy(){
        $session_keys = [
            'empID', 'username','loginusername','companyID','companyType','company_link_id','branchID',
            'usergroupID','company_code','company_name','company_logo','emplangid','emplanglocationid',
            'isGroupUser','userType','status','subscription_expire_notification','subscription_dates',
            'navigationMasterId'
        ];
        $this->session->unset_userdata($session_keys);
    }
    function login_submitspur_go()
    {
            $token = ($_GET['Token']);
            $this->db->select('*');
            $this->db->where("login_token", $token);
            $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $resultDb2 = $this->db->get("user")->row_array();
            if(empty($resultDb2)){
                $data['title'] = 'Login';
                $data['type'] = 'e';
                $data['extra'] = 'Invalid token';
                $this->load->view(getLoginPage(), $data);
                return false;
            }
            else{
                $login_data['userN'] = $resultDb2['Username'];
                $this->login_setup($resultDb2, $login_data, $token,1);
            }

    }

    function encrypt_value($val){
        //for debugging purpose
        echo $this->encryption->encrypt($val);
    }

    function error($error_code){

        if(ENVIRONMENT == 'development'){
            if($error_code == 440){
                return $this->load->view('errors/html/error_440');
            }elseif($error_code == 403){
                return $this->load->view('errors/html/error_403');
            }elseif($error_code == 404){
                return $this->load->view('errors/html/error_404');
            }else{
                return $this->load->view('errors/html/error_500');
            }
        }
    }
   
}
