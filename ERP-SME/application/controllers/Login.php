<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index($result=NULL){
		if ($this->session->has_userdata('sme_company_status')){
			header('Location: '.site_url('Dashboard'));
		}else{
			$data['title']          = 'Login';
        	$data['main_content']   = 'login_page';
        	$data['extra']          = $result;
        	$this->load->view('login_page',$data);
		}	
	}

	public function loginSubmit(){
		$this->form_validation->set_rules('Username', 'Username', 'trim|required');
        $this->form_validation->set_rules('Password', 'Password', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
        	$result['message'] 	= validation_errors();
        	$result['type'] 	= 'danger';
            $this->index($result);
        } else {
	        	$login_data['Username'] = $this->input->post('Username');
	            $login_data['Password'] = $this->input->post('Password');

	            $usern = $login_data['Username'];
	            $pass = $login_data['Password'];
                $passwordx = md5("{$pass}GEARSSME");

                $user_data = $this->db->query("SELECT * FROM srp_erp_companyadminusers WHERE Username = '$usern' AND Password = '$passwordx'")->row_array();
                if (!empty($user_data)) {
                    $session_data = array(
                        'log_userID'    	 => $user_data['UserID'],
                        'username'    		 => $user_data['Username'],
                        'sme_company_userDisplayName'    => $user_data['Fullname'],
                        'adminType'    		 => $user_data['adminType'],
                        'sme_company_status' => TRUE
                    );
                    $this->session->set_userdata($session_data);
                    header('Location: '.site_url('Dashboard'));
                }
                else {
                    $result['message'] 	= $login_data['Username'] . " is not existing in the System.";
                    $result['type'] 	= 'warning';
                    $this->index($result);
                }
        }
	}

	function logout(){
		//$this->session->sess_destroy();
        $user_data = ['log_userID', 'username', 'sme_company_userDisplayName', 'adminType', 'sme_company_status'];
        $this->session->unset_userdata($user_data);
		$result['message'] 	='Successfully Logout form System';
		$result['type'] 	='success';
		$this->index($result);
	}
}
