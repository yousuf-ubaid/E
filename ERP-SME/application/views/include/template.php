<?php
$this->load->view('include/header', $title);
$this->load->view($main_content,$extra);
$this->load->view('include/footer');

if(is_array($extra) && array_key_exists('js_page', $extra)){
    $this->load->view($extra['js_page']);
}