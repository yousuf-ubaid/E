<?php

class Educal_erplogin extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();


    }

    public function index($employee_code = NULL, $message = NULL, $type = 'e', $isGroupUser = 0)
    {
        $session_data = null;
        $this->load->model('session_model');
        if ($employee_code) {
            if (md5($this->session->userdata('e_empID')) == $employee_code) {
                redirect('dashboard');
            } else {
                $session_data = $this->session_model->createSession($employee_code, $isGroupUser);

                if ($session_data['stats']) {
                    $this->session->set_flashdata('s', $this->session->userdata('e_empname') . ' Successfully logged into System');
                    redirect('dashboard');
                } else {
                    $this->no_permission($session_data['message']);
                    echo json_encode($session_data['message']);
                    die();
                }
            }
        } else {
            $data['title'] = 'Login';
            $data['extra'] = $message;
            $data['type'] = $type;
            $this->load->view(getLoginPage(), $data);
        }
    }

    function educal_erp_login()
    {
        $this->load->model('session_model');
        $url = "{$_SERVER['REQUEST_URI']}";
        $host = '/Educal_erplogin/educal_erp_login/';
        $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $removedURL = str_replace($host, "/", $escaped_url);
        $parameterFound = false;
        $array = explode("/", $removedURL);
        $array_new[] = $array[2];
        if (!empty($array_new)) {
            if (strpos($url, $host) !== false) {
                if (count($array_new) != 0) {
                    foreach ($array_new as $key => $value) {
                        if (empty($value)) {
                            echo json_encode(array('message' => 'Parameter can not be empty'));
                        }
                    }
                    $parameterFound = true;
                } else {
                    echo json_encode(array('message' => 'parameter can not found as expected'));
                }
            } else {
                echo json_encode(array('message' => 'Link not found'));
            }
        } else {
            echo json_encode(array('message' => 'Parameter can not be empty'));
        }

        if ($parameterFound) {
            $db2 = $this->load->database('db2', TRUE);
            $user = $db2->query("SELECT EidNo,Username,`Password` FROM `user`  where login_token = '{$array_new[0]}'")->row_array();
            $login_data['userN'] = $user['Username'];
            $login_data['passW'] = $user['Password'];
            $db2->select('*');
            $db2->where("UserName", $login_data['userN']);
            $db2->where("Password", $login_data['passW']);
            $db2->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $resultDb2 = $db2->get("user")->row_array();
            $result = "";
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
                    $result = $this->session_model->authenticateLogin($login_data);
                    if ($result['stats']) {
                        $this->index($result['data'], NULL, 'e', $resultDb2['isGroupUser']);
                    } else {
                        $this->index(FALSE, $result['message']);
                    }

                } else {
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
                    if ($result['stats']) {
                        $this->index($result['data'], NULL);
                    } else {
                        $this->index(FALSE, $result['message']);
                    }
                }
            } else {
                $this->db->select('*');
                $this->db->where("UserName", $login_data['userN']);
                //$this->db->where("Password", $login_data['passW']);
                $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
                $usernameDb2 = $this->db->get("user")->row_array();
                if ($usernameDb2['isSubscriptionDisabled'] == 1) { //if company is disabled
                    $data['title'] = 'Login';
                    $data['type'] = 'e';
                    $data['extra'] = 'Subscription is disabled';
                    echo json_encode($data['extra']);
                    die();
                }
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
                    if ($result['stats']) {
                        $this->index($result['data'], NULL);
                    } else {
                        echo json_encode($result['message']);
                        die();
                        //  $this->index(FALSE, $result['message']);
                    }
                } else {
                    $data['title'] = 'Login';
                    $data['type'] = 'e';
                    $data['extra'] = 'Wrong user name or password. Please  try again.';
                    echo json_encode($data['extra']);
                    die();
                }
            }

        }
    }
}
