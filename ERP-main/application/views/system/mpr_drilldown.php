<?php
$this->load->helper('cookie');
$this->load->view('include/header',$title);
$this->load->view('include/top-mpr',$extra);

$this->load->view($main_content,$title,$extra,$gross_rows,$periods,$percentage_cols,$rpt_data_drilldown,$sub_data,$dPlace);

$this->load->view('include/footer');
/*$this->load->view('include/footer-pos');*/
/*$this->load->view('include/navigation',$title);
$this->load->view($main_content,$extra);
$this->load->view('include/footer');*/
