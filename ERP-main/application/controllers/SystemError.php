<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SystemError extends CI_Controller {

  public function __construct() {

    parent::__construct();

    // load base_url
    $this->load->helper('url');
  }

  public function error_440(){
    $data['content'] = "Error Page";
    $this->load->view('error_440',$data);
  }
  public function error_404(){
    $data['content'] = "Error Page";
    $this->load->view('error_404',$data);
  }
  public function error_500(){
    $data['content'] = "Error Page";
    $this->load->view('error_500',$data);
  }

}