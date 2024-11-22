<?php
$this->load->helper('crm_helper');
$category_arr_filter = load_all_categories(false);
$status_arr_filter = all_task_status(false);
$groupmaster_arr = all_crm_groupMaster(false);
$employee_arr_filter = all_crm_users_responsible(false);
$issuperadmin = crm_isSuperAdmin();
$isGroupAdmin = crm_isGroupAdmin();
$all_crm_users_responsible = all_crm_users_responsible(false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate = current_date(FALSE);
$startdate = date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);

if(!empty($_POST['policy_id']))
{
  $policy = $_POST['policy_id'];
}else
{
    $policy = '';
}

$pagenew = $policy;
?>
<style>
    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    #search_dashboard_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        font-size: 12px;
    }

    .pagination > li > a, .pagination > li > span {
        padding: 2px 8px;
    }

    ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .panel.with-nav-tabs .panel-heading {
        padding: 5px 5px 0 5px;
    }

    .panel.with-nav-tabs .mainpanel {
        border-bottom: none;
    }

    .panel.with-nav-tabs .nav-justified {
        margin-bottom: -1px;
    }


    /********************************************************************/

    /*** PANEL SUCCESS ***/
    .with-nav-tabs.panel-success .nav-tabs > li > a,
    .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
        color: #3c763d;
    }

    .with-nav-tabs.panel-success .nav-tabs > .open > a,
    .with-nav-tabs.panel-success .nav-tabs > .open > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > .open > a:focus,
    .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
        color: #3c763d;
        background-color: white;
        border-color: transparent;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.active > a,
    .with-nav-tabs.panel-success .nav-tabs > li.active > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
        color: #3c763d;
        background-color: #fff;
        border-color: #d6e9c6;
        border-bottom-color: transparent;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu {
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a {
        color: #3c763d;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
        background-color: #d6e9c6;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
        color: #fff;
        background-color: #3c763d;
    }

    .panel-success > .panel-heading {
        background-color: white;
    }

    .with-nav-tabs.panel-success .mainpanel > li.active > a, .with-nav-tabs.panel-success .mainpanel > li.active > a:hover, .with-nav-tabs.panel-success .mainpanel > li.active > a:focus {
        color: #000000;
        background-color: #ecf0f5;
        border-color: #ecf0f5;
        border-bottom-color: transparent;
    }

    .check {
        opacity: 0.5;
        color: #996;

    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        font-size: 12px;
    }

    .pagination > li > a, .pagination > li > span {
        padding: 2px 8px;
    }

    .fc-time {
        display: none !important;
    }

    .boxHeaderCustom {
        height: 29px !important;
        font-size: 16px !important;
        padding: 3px 9px !important;
        font-weight: 700 !important;
        background-color: rgba(222, 218, 218, 0.16) !important;
        color: #69697b !important;
    }

    .r-icon-stats {
        text-align: center;
    }

    .r-icon-stats i {
        width: 66px;
        height: 66px;
        padding: 20px;
        text-align: center;
        color: white;
        font-size: 24px;
        display: inline-block;
        border-radius: 100%;
        vertical-align: top;
        background: #01c0c8;
        padding-left: 14px;
    }

    .r-icon-stats .bodystate {
        padding-left: 20px;
        display: inline-block;
        vertical-align: middle;
    }

    .r-icon-stats .bodystate h4 {
        margin-bottom: 0px;
        font-size: 25px;
        font-weight: 800;
    }

    .white-box {
        background: #ffffff;
        padding: 0px;
        margin-bottom: 15px;
    }

    .white-box .box-title {
        margin: 0px 0px 12px;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 14px;
    }

    .bg-organization {
        background-color: #00a65a !important;
    }

    .bg-task {
        background-color: #f39c12 !important;
    }

    .bg-leads {
        background-color: #dadd39 !important;
    }

    .bg-opportunity {
        background-color: #AF7AC5 !important;
    }

    .bg-contact {
        background-color: #00c0ef !important;
    }

    .bg-theme {
        background-color: #ff6849 !important;
    }

    .bg-project {
        background-color: #8BC34A !important;
    }

    .bg-inverse {
        background-color: #4c5667 !important;
    }

    .bg-purple {
        background-color: #9675ce !important;
    }

    .bg-white {
        background-color: #ffffff !important;
    }

    .text-muted {
        text-align: center;
    }

    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {

        padding: 4px;
    }

    th {

        font-size: 12px;
    }

    .ti-stats-up:before {
        content: "";
    }

    .numberStar {
        text-align: center;
        font-weight: 600;
        color: teal;
        margin-top: 5%;
    }

    .countstar {
        font-size: 21px;
        font-weight: bold;
        /*margin: -6px 0 0 0;*/
        white-space: nowrap;
        padding: 0;
    }
    .post-doc:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .post-doc {
        display: flex;
        width: 300px;
        height: 110px;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        border-top: 1px solid #ddd;
    }

    .post-doc-left {
        width: 90px;
        position: relative;
    }

    .post-doc-right {
        width: 70%;
        background-color: #FEFEFE;
        color: #484855;
    }

    .post-doc-right_body {
        line-height: 2;
        padding: 4px;
    }

    .post-doc-right_footer {
        justify-content: space-between;
        padding: 4px;
    }

    .post-doc-right_footer_btn {
        font-size: 12px;
        margin-right: 2px;
    }
    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 5px 0 6px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
    }

    .alpha-box {
        font-size: 14px;
        line-height: 25px;
        list-style: none outside none;
        margin: 0 0 0 12px;
        padding: 0 0 0;
        text-align: center;
        text-transform: uppercase;
        width: 24px;
    }

    ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    .alpha-box li a {
        text-decoration: none;
        color: #555;
        padding: 4px 8px 4px 8px;
    }

    .alpha-box li a.selected {
        color: #fff;
        font-weight: bold;
        background-color: #4b8cf7;
    }

    .alpha-box li a:hover {
        color: #000;
        font-weight: bold;
        background-color: #ddd;
    }

    .arrow-steps .step {
        font-size: 12px !important;
        padding: 3px 10px 7px 30px !important;
    }
    .arrow-steps .step:after, .arrow-steps .step:before {
        border-top: 13px solid transparent !important;
        border-bottom: 14px solid transparent !important;
    }


</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<link href='<?php echo base_url('plugins/fullcalender/lib/cupertino/jquery-ui.min.css'); ?>' rel='stylesheet'/>
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.min.css'); ?>' rel='stylesheet'/>
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.print.min.css'); ?>' rel='stylesheet' media='print'/>

<script type="text/javascript" src="<?php echo base_url('plugins/fullcalender/fullcalendar.min.js'); ?>"></script>
<section class="content" id="ajax_body_container">
    <div id="dashboard_content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel with-nav-tabs panel-success" style="border: none;">
                    <div class="panel-heading">
                        <ul class="nav nav-tabs mainpanel">

                          <?php if($pagenew == 'dashboardtask'){?>
                            <li>
                                <a onclick="dashboardclick()" id="" data-id="0"
                                   href="#17erp_ajax_load_dashboard_template1" data-toggle="tab"
                                   aria-expanded="true"><span><i class="fa fa-tachometer" aria-hidden="true"
                                                                 style="color: #ffbe00;font-size: 16px;"></i> <>Dashboard</span></a>
                            </li>
                            <?php }else {?>
                              <li class="active">
                                  <a onclick="dashboardclick()" id="" data-id="0"
                                     href="#17erp_ajax_load_dashboard_template1" data-toggle="tab"
                                     aria-expanded="true"><span><i class="fa fa-tachometer" aria-hidden="true"
                                                                   style="color: #ffbe00;font-size: 16px;"></i> Dashboard</span></a>
                              </li>
                            <?php }?>

                            <li class="">
                                <a onclick="dashboard_new_click()" id="" data-id="0"
                                   href="#crm_dashboard2" data-toggle="tab"
                                   aria-expanded="true"><span><i class="fa fa-tachometer" aria-hidden="true"
                                                                 style="color: #F44336;font-size: 16px;"></i> Dashboard 2</span></a>
                            </li>
                            <?php if($pagenew == 'dashboardtask'){?>
                            <li class="active">
                                <a id="" data-id="0"
                                   href="#17erp_ajax_load_dashboard_template2" data-toggle="tab"
                                   aria-expanded="true"><span><i class="fa fa-calendar" aria-hidden="true"
                                                                 style="color: #E74C3C;font-size: 16px;"></i>&nbspCalendar</span></a>
                            </li>
                            <?php }else {?>

                                <li class=" ">
                                    <a id="" data-id="0"
                                       href="#17erp_ajax_load_dashboard_template2" data-toggle="tab"
                                       aria-expanded="true"><span><i class="fa fa-calendar" aria-hidden="true"
                                                                     style="color: #E74C3C;font-size: 16px;"></i>&nbspCalendar</span></a>
                                </li>
                            <?php }?>
                        </ul>
                    </div>
                    <div class="panel-body" style="background-color: #ecf0f5;">
                        <div class="tab-content">
                            <?php if($pagenew == 'dashboardtask'){?>
                            <div class="tab-pane" id="17erp_ajax_load_dashboard_template1">
                                <?php }else {?>
                                <div class="tab-pane active" id="17erp_ajax_load_dashboard_template1">
                                <?php }?>
                                <div class="box box-warning">
                                    <div class="box-header with-border">
                                        <div class="row" style="margin-top: 5px">
                                            <div class="col-md-12" id="">
                                                <div class="col-sm-9">
                                                    <h4 class="box-title">Dashboard</h4>
                                                </div>
                                                <?php
                                                if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
                                                    ?>
                                                    <!--<div class="col-sm-2">
                                                        <span style="font-weight: bold;">Users Group</span>
                                                        <br>
                                                        <?php /*echo form_dropdown('groupID', $groupmaster_arr, '', 'class="form-control select2 pull-right" id="groupID" onchange="load_group_members()" multiple=""'); */?>
                                                    </div>-->
                                                    <div class="col-sm-2">
                                                        <span style="font-weight: bold;">Users</span>
                                                        <br>
                                                        <!--<div id="div_groupemployee">
                                                            <select name="groupEmployeeID[]" id="groupEmployeeID"
                                                                    class="form-control select2"
                                                                    onchange="employeeDashboard()" multiple="">
                                                            </select>
                                                        </div>-->
                                                        <?php echo form_dropdown('groupEmployeeID[]', $all_crm_users_responsible, '', 'class="form-control" id="groupEmployeeID" onchange="load_group_members()" multiple="" '); ?>

                                                    </div>
                                                   <!-- <div class="col-sm-2">
                                                        <div id="div_groupemployee">
                                                            <select name="groupEmployeeID[]" id="groupEmployeeID"
                                                                    class="form-control select2"
                                                                    onchange="employeeDashboard()" multiple="">
                                                            </select>
                                                        </div>
                                                    </div>-->
                                                    <div class="col-sm-1 hide" id="search_dashboard_cancel">
                    <span class="tipped-top" style="margin-left: 60%;"><a id="cancelSearchDashboard" href="#"
                                                                          onclick="clearDashboardSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>" style="margin-top: 37%;"> </a></span>
                                                    </div>
                                                <?php } else {?>
                                                    <div class="col-sm-2">
                                                        <span style="font-weight: bold;">Assignee</span>
                                                        <br>
                                                        <?php echo form_dropdown('permissiontype', array('1'=>'All','2'=>'Assign For Me'), '', 'class="form-control select2 pull-right" id="permissiontype" onchange="permissionwisetbl()"'); ?>
                                                    </div>
                                                <?php }?>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-2 col-xs-6">
                                                <div class="white-box">
                                                    <div class="r-icon-stats">
                                                        <i class="ti-stats-up bg-contact">
                                                            <div id="total_contacts" class="countstar">0</div>
                                                        </i>
                                                    </div>
                                                    <div class="bodystate">

                                                        <a class="link-person noselect" href="#"
                                                           onclick="totaldoccounts_contact()">
                                                            <div class="numberStar">CONTACTS</div>
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-xs-6">
                                                <div class="white-box">
                                                    <div class="r-icon-stats">
                                                        <i class="ti-stats-up bg-organization">
                                                            <div id="total_organization" class="countstar">0</div>
                                                        </i>
                                                    </div>
                                                    <div class="bodystate">
                                                        <a class="link-person noselect" href="#"
                                                           onclick="totalcountorganizationview_dashboard()">
                                                            <div class="numberStar">ORGANIZATIONS</div>
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-xs-6">
                                                <div class="white-box">
                                                    <div class="r-icon-stats">
                                                        <i class="ti-stats-up bg-task">
                                                            <div id="total_tasks" class="countstar">0</div>
                                                        </i>
                                                    </div>
                                                    <div class="bodystate">
                                                        <a class="link-person noselect" href="#"
                                                           onclick="totaltask_dashboard()">
                                                            <div class="numberStar">TASKS</div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-xs-6">
                                                <div class="white-box">
                                                    <div class="r-icon-stats">
                                                        <i class="ti-stats-up bg-leads">
                                                            <div id="total_lead" class="countstar">0</div>
                                                        </i>
                                                    </div>
                                                    <div class="bodystate">
                                                            <a class="link-person noselect" href="#"
                                                               onclick="totalleadscount_dashboard()">
                                                                <div class="numberStar">LEADS</div>
                                                            </a>
                                                        </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-xs-6">
                                                <div class="white-box">
                                                    <div class="r-icon-stats">
                                                        <i class="ti-stats-up bg-opportunity">
                                                            <div id="total_opportunity" class="countstar">0</div>
                                                        </i>
                                                    </div>
                                                    <div class="bodystate">
                                                        <a class="link-person noselect" href="#"
                                                           onclick="totalopportunities_dashboard()">
                                                            <div class="numberStar">OPPORTUNITIES</div>
                                                        </a>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-xs-6">
                                                <div class="white-box">
                                                    <div class="r-icon-stats">
                                                        <i class="ti-stats-up bg-project">
                                                            <div id="total_projects" class="countstar">0</div>
                                                        </i>
                                                    </div>
                                                    <div class="bodystate">
                                                        <a class="link-person noselect" href="#"
                                                           onclick="totalprojectcount_dashboard()">
                                                            <div class="numberStar">PROJECTS</div>
                                                        </a>
                                                    </div>
                                                   <!-- <div class="bodystate">
                                                        <div class="numberStar">PROJECTS</div>
                                                    </div>-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div style="border: 1px solid rgba(158, 158, 158, 0.24); padding: 5px">
                                                    <div style="line-height: 3;">
                                                                   <span style="font-size: 16px;line-height: 1px">
                                                                        Reports for Year </span>
                                                               <span class="pull-right">
                                                                   <?php
                                                                   $reportsYear = $this->db->query("SELECT DISTINCT YEAR(createdDateTime) AS year FROM srp_erp_crm_contactmaster ORDER BY YEAR(createdDateTime) DESC")->result_array();
                                                                   ?>
                                                                   <?php
                                                                   if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
                                                                   ?>
                                                                   <select name="reportYear" id="reportYear" onchange="dashboard_reports_year()">
                                                                       <?php
                                                                       $currentYear = date('Y');
                                                                       for ($x = 0; $x <= 5; $x++) {
                                                                           ?>
                                                                       <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                       <?php
                                                                           $currentYear=$currentYear-1;
                                                                       }

                                                                       ?>
                                                                   </select>
                                                                   <?php } else {?>
                                                                       <select name="reportYear" id="reportYear" onchange="dashboard_reports_year_user_count()">
                                                                           <?php
                                                                           $currentYear = date('Y');
                                                                           for ($x = 0; $x <= 5; $x++) {
                                                                               ?>
                                                                               <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                               <?php
                                                                               $currentYear=$currentYear-1;
                                                                           }

                                                                           ?>
                                                                       </select>
                                                                   <?php }?>
                                                               </span>

                                                        <div id="allTopReports"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div style="border: 1px solid rgba(158, 158, 158, 0.24); padding: 5px">
                                                    <div style="line-height: 3;">
                                                        <span style="font-size: 16px;line-height: 1px">
                                                                        Leads & Opportunities Rate</span>
                                                               <span class="pull-right">
                                        <?php
                                        $leadsourceYear = $this->db->query("SELECT DISTINCT YEAR(createdDateTime) AS year FROM srp_erp_crm_leadmaster  ORDER BY YEAR(createdDateTime) DESC")->result_array();
                                        ?>
                                                                   <?php
                                                                   if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
                                                                   ?>
                                                                   <select name="leads_opportunitiesYear"
                                                                           id="leads_opportunitiesYear"
                                                                           onchange="Lead_Generation_Rate()">
                                                                       <?php
                                                                       $currentYear = date('Y');
                                                                       for ($x = 0; $x <= 5; $x++) {
                                                                           ?>
                                                                           <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                           <?php
                                                                           $currentYear=$currentYear-1;
                                                                       }

                                                                       ?>
                                                                   </select>
                                                                   <?php } else {?>
                                                                       <select name="leads_opportunitiesYear"
                                                                               id="leads_opportunitiesYear"
                                                                               onchange="Lead_Generation_Rate_count()">
                                                                           <?php
                                                                           $currentYear = date('Y');
                                                                           for ($x = 0; $x <= 5; $x++) {
                                                                               ?>
                                                                               <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                               <?php
                                                                               $currentYear=$currentYear-1;
                                                                           }

                                                                           ?>
                                                                       </select>
                                                                   <?php }?>
                                                               </span>

                                                        <div id="leadOpporunityGeneration">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div style="border: 1px solid rgba(158, 158, 158, 0.24); padding: 5px">
                                                    <div style="line-height: 3;">
                                                                   <span style="font-size: 16px;line-height: 1px">
                                                                        Top 10 Leads</span>
                                                               <span class="pull-right">
                                                                     <?php
                                                                     $reportsYear = $this->db->query("SELECT DISTINCT YEAR(createdDateTime) AS year FROM srp_erp_crm_leadproducts ORDER BY YEAR(createdDateTime) DESC")->result_array();
                                                                     ?>
                                                                   <?php
                                                                   if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
                                                                   ?>
                                                                   <select name="toptenleadsYear" id="toptenleadsYear"
                                                                           onchange="topten_year_change()">
                                                                       <?php
                                                                       $currentYear = date('Y');
                                                                       for ($x = 0; $x <= 5; $x++) {
                                                                           ?>
                                                                           <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                           <?php
                                                                           $currentYear=$currentYear-1;
                                                                       }

                                                                       ?>
                                                                   </select>
                                                                   <?php } else {?>
                                                                   <select name="toptenleadsYear" id="toptenleadsYear"
                                                                           onchange="topten_year_change_permision()">
                                                                       <?php
                                                                       $currentYear = date('Y');
                                                                       for ($x = 0; $x <= 5; $x++) {
                                                                           ?>
                                                                           <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                           <?php
                                                                           $currentYear=$currentYear-1;
                                                                       }

                                                                       ?>
                                                                   </select>
                                                                   <?php }?>
                                                               </span>
                                                    </div>
                                                    <div style="height: 300px" id="topBestLeads"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div style="border: 1px solid rgba(158, 158, 158, 0.24); padding: 5px">
                                                    <div style="line-height: 3;">
                                                                   <span
                                                                       style="font-size: 16px;line-height: 1px;text-align: center">
                                                                        Lead Source</span>
                                                                <span class="pull-right">
                               <?php
                               $leadsourceYear = $this->db->query("SELECT DISTINCT YEAR(createdDateTime) AS year FROM srp_erp_crm_leadmaster  ORDER BY YEAR(createdDateTime) DESC")->result_array();
                               ?>
                                                                    <?php
                                                                    if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
                                                                    ?>
                                                                    <select name="leadsourceYear"
                                                                            id="leadsourceYear"
                                                                            onchange="leadsource_year_change()">
                                                                        <?php
                                                                        $currentYear = date('Y');
                                                                        for ($x = 0; $x <= 5; $x++) {
                                                                            ?>
                                                                            <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                            <?php
                                                                            $currentYear=$currentYear-1;
                                                                        }

                                                                        ?>
                                                                    </select>
                                                                    <?php }else {?>
                                                                        <select name="leadsourceYear"
                                                                                id="leadsourceYear"
                                                                                onchange="lead_source_count()">
                                                                            <?php
                                                                            $currentYear = date('Y');
                                                                            for ($x = 0; $x <= 5; $x++) {
                                                                                ?>
                                                                                <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                                <?php
                                                                                $currentYear=$currentYear-1;
                                                                            }

                                                                            ?>
                                                                        </select>
                                                                    <?php }?>
                                                               </span>
                                                    </div>
                                                    <div style="height: 300px" id="topLeadsSource"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div style="border: 1px solid rgba(158, 158, 158, 0.24); padding: 5px">
                                                    <div style="line-height: 3;">
                                                                   <span
                                                                       style="font-size: 16px;line-height: 1px;text-align: center">
                                                                        Opportunities </span>
                                                                <span class="pull-right">
                                                              <?php
                                                              $reportsYear = $this->db->query("SELECT DISTINCT YEAR(createdDateTime) AS year FROM srp_erp_crm_opportunity ORDER BY YEAR(createdDateTime) DESC")->result_array();
                                                              ?>
                                                                    <?php
                                                                    if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
                                                                    ?>
                                                                    <select name="opportunitiesYear"
                                                                            id="opportunitiesYear"
                                                                            onchange="opportunity_year_change()">
                                                                        <?php
                                                                        $currentYear = date('Y');
                                                                        for ($x = 0; $x <= 5; $x++) {
                                                                            ?>
                                                                            <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                            <?php
                                                                            $currentYear=$currentYear-1;
                                                                        }

                                                                        ?>
                                                                    </select>
                                                                    <?php }else {?>
                                                                        <select name="opportunitiesYear"
                                                                                id="opportunitiesYear"
                                                                                onchange="best_opportunity_count()">
                                                                            <?php
                                                                            $currentYear = date('Y');
                                                                            for ($x = 0; $x <= 5; $x++) {
                                                                                ?>
                                                                                <option value="<?php echo $currentYear ?>"><?php echo $currentYear ?></option>
                                                                                <?php
                                                                                $currentYear=$currentYear-1;
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    <?php }?>
                                                               </span>
                                                    </div>
                                                    <div style="height: 300px" id="topTenOpportunities"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div
                                                    style=" padding: 5px; text-align: center;">
                                                    <h4>Our Team</h4>

                                                    <div id="ourCrmTeam"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--<hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div style="border: 1px solid rgba(158, 158, 158, 0.24); padding: 5px">
                                                    <h4> Quick Reports </h4>
                                                    <ul class="list-group" style="margin-bottom: 0px">
                                                        <li class="list-group-item"><i
                                                                class="fa fa-link"
                                                                aria-hidden="true"></i> &nbsp;&nbsp;<a
                                                                href="#">Opportunities</a></li>
                                                        <li class="list-group-item"><i
                                                                class="fa fa-link"
                                                                aria-hidden="true"></i> &nbsp;&nbsp;<a
                                                                href="#">Projects</a></li>
                                                        <li class="list-group-item"><i
                                                                class="fa fa-link"
                                                                aria-hidden="true"></i> &nbsp;&nbsp;<a
                                                                href="#">Leads</a></li>
                                                        <li class="list-group-item"><i
                                                                class="fa fa-link"
                                                                aria-hidden="true"></i> &nbsp;&nbsp;<a
                                                                href="#">Events</a></li>
                                                        <li class="list-group-item"><i
                                                                class="fa fa-link"
                                                                aria-hidden="true"></i> &nbsp;&nbsp;<a
                                                                href="#">Organizations</a></li>
                                                        <li class="list-group-item"><i
                                                                class="fa fa-link"
                                                                aria-hidden="true"></i> &nbsp;&nbsp;<a
                                                                href="#">Contacts</a></li>
                                                        <li class="list-group-item"><i
                                                                class="fa fa-link"
                                                                aria-hidden="true"></i> &nbsp;&nbsp;<a
                                                                href="#">Tasks</a></li>
                                                    </ul>

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            </div>
                                        </div>-->

                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane" id="crm_dashboard2">
                                <div class="row" style="margin-top: 5px">
                                    <div class="col-md-12" id="1T17">
                                        <div class="box box-warning">
                                            <div class="box-header with-border">
                                                <div class="col-sm-7">
                                                    <h4 class="box-title">&nbsp;</h4>
                                                </div>
                                                <!-- /.box-tools -->
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div
                                                        style="border: 1px solid rgba(158, 158, 158, 0.24); padding: 5px">
                                                        <div style="line-height: 3;">
                                                                   <span style="font-size: 16px;line-height: 1px;">
                                                                        Leads Converted to Opportunities </span>

                                                            <div id="dashboard2_cTotalOpprtunity"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div
                                                        style="border: 1px solid rgba(158, 158, 158, 0.24); padding: 5px">
                                                        <div style="line-height: 3;">
                                                                   <span style="font-size: 16px;line-height: 1px;">
                                                                        Opportunities Converted to Project </span>

                                                            <div id="dashboard2_cTotalProject"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="overlay" id="overlay117" style="display: none;"><i
                                                    class="fa fa-refresh fa-spin"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                  <?php if($pagenew == 'dashboardtask'){?>

                            <div class="tab-pane active" id="17erp_ajax_load_dashboard_template2">
                                     <?php }else {?>

                                <div class="tab-pane" id="17erp_ajax_load_dashboard_template2">
                            <?php }?>
                                <div class="row" style="margin-top: 5px">
                                    <div class="col-md-12" id="1T17">
                                        <div class="box box-warning">
                                            <div class="box-header with-border">
                                                <div class="col-sm-1">
                                                    <h4 class="box-title">Calendar</h4>
                                                </div>

                                                <div class="col-sm-3 filtercalander" >

                                                </div>

                                                <div class="col-sm-2 datefilter hide">
                                                    <span style="font-weight: bold;">Start Date</span>
                                                    <br>
                                                    <div class="input-group datepic ">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                        <input type="text" name="datefrom"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 datefilter hide">
                                                    <span style="font-weight: bold;">End Date</span>
                                                    <br>
                                                    <div class="input-group datepic">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                        <input type="text" name="dateto"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                                                    </div>
                                                </div>
                                                <?php
                                                if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {?>
                                                    <div class="col-sm-2" style="margin-right: 1%;">
                                                        <span style="font-weight: bold;">Users</span>
                                                        <br>
                                                        <?php echo form_dropdown('calEmployee', $employee_arr_filter, '', 'class="form-control" multiple id="calEmployee"'); ?>
                                                    </div>
                                                <?php }else {?>
                                                    <div class="col-sm-2">
                                                        <span style="font-weight: bold;">Assignee </span>
                                                        <br>
                                                        <?php echo form_dropdown('permissiontypecal', array('1'=>'All','2'=>'Assign For Me'), '', 'class="form-control pull-right" id="permissiontypecal"'); ?>
                                                    </div>
                                                <?php }?>

                                                <div class="col-sm-2">
                                                    <span style="font-weight: bold;">Category</span>
                                                    <?php echo form_dropdown('Category', $category_arr_filter, '', 'class="form-control" id="db_filter_categoryID"'); ?>
                                                </div>
                                                <div class="col-sm-2">
                                                    <span style="font-weight: bold;">Status</span>
                                                    <?php echo form_dropdown('statusID', $status_arr_filter, '', 'class="form-control" id="db_filter_statusID"'); ?>
                                                </div>
                                                <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                                                </div>
                                                <!-- /.box-tools -->
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body calanderview" style="display: block;width: 100%">
                                                <div class="row ">
                                                    <div class="col-md-12">
                                                        <div id='crm_calendar'></div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="box-body tasklistview hide" style="display: block;width: 100%">
                                                <div class="row ">
                                                    <div class="col-md-12">
                                                        <div id='crm_calendar_report'></div>
                                                    </div>
                                                </div>

                                            </div>

                                        <!--    <div class="row tasklistview hide">
                                                <div class="col-md-12">
                                                    <div id='crm_calendar_report'></div>
                                                </div>
                                            </div>-->

                                            <div class="overlay" id="overlay117" style="display: none;"><i
                                                    class="fa fa-refresh fa-spin"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="contactddmodal" tabindex="2" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" style="width: 75%" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">View Contacts<span class="myModalLabel"></span>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="contactview"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="contactddmodal_moredetail" tabindex="2" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" style="width: 75%" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">View Contact<span class="myModalLabel"></span>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="contactscrm_more_view"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="organizationdmodal_moredetail" tabindex="2" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" style="width: 70%" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">View Organizations<span class="myModalLabel"></span>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="organizayionview"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>

                   </div>
                    </div>
                  <!--  <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                    </div>-->
                 </div>
                </div>
            </div>
        </div>
</section>

<div class="modal fade" id="contact_count_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">CONTACTS</h4>
            </div>
            <div class="modal-body">
                <div id="contacts_view">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="organization_count_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Organizations</h4>
            </div>
            <div class="modal-body">
                <div id="organization_view">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="lead_count_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Leads</h4>
            </div>
            <div class="modal-body">
                <div id="lead_view">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="opportunities_view_count_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Opportunities</h4>
            </div>
            <div class="modal-body">
                <div id="Opportunities_view">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="project_view_count_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Projects</h4>
            </div>
            <div class="modal-body">
                <div id="project_view">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="task_view_count_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Task</h4>
            </div>
            <div class="modal-body">
                <div id="task_view">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var subtaskTaskview = 0;
    $(document).ready(function () {

        our_crm_team();

        <?php if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) { ?>
        dashboardTotalDocuments_Count();
        dashboard_reports_year();
        Lead_Generation_Rate();
        lead_source();
        best_opportunity();
        best_leads();

        $('#groupEmployeeID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
          //  selectAllValue: 'select-all-value',
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
       // $("#groupEmployeeID").multiselect2('selectAll', false);
        //$("#groupEmployeeID").multiselect2('updateButtonText');

        <?php } else {?>
        permissionwisetbl();
        <?php }?>

        $('#calEmployee').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            subtask_task_rpt_dashboard();
        });



        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('#crm_calendar').fullCalendar('render');
        });

        Highcharts.getOptions().plotOptions.pie.colors = (function () {
            var colors = ['#5DADE2', '#85C1E9', '#AED6F1', '#D6EAF8'],
                base = Highcharts.getOptions().colors[0],
                i;

            for (i = 0; i < 10; i += 1) {
                // Start out with a darkened base color (negative brighten), and end
                // up with a much brighter color
                colors.push(Highcharts.Color(base).brighten((i - 3) / 7).get());
            }
            return colors;
        }());




        $('#crm_calendar').fullCalendar({
            customButtons: {
                myCustomButton: {
                    text: 'Task List',
                    click: function() {
                        subtask_task_rpt_dashboard(1);
                    }
                }
            },
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,myCustomButton'
            },
            defaultDate: new Date(),
            navLinks: true, // can click day/week names to navigate views
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: {
                url: '<?php echo site_url('crm/allCalenderEvents'); ?>',
                data: function () {
                    return {
                        category: $("#db_filter_categoryID").val(),
                        status: $("#db_filter_statusID").val(),
                        employees: $("#calEmployee").val(),
                        permissiontype:$("#permissiontypecal").val()
                    };


                },
                type: "POST",
                cache: false

            },
            dayClick: function (date) {
                swal({
                        title: "Are you sure?",
                        text: "You want to create a task!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#00A65A",
                        confirmButtonText: "Create Task"
                    },
                    function () {
                        fetchPage('system/crm/create_new_task', '', 'Create Task', 2, date.format());
                    });

            },
            eventRender: function (event, element) {
                /*                element.find(".fc-content").append("<i style='color: white; font-size: 12px' class='fa fa-eye pull-right closeon' aria-hidden='true' title='View'></i>");*/
                element.find(".fc-content").click(function () {
                    viewEvent(event._id);
                });
            }
            //

    });
    });

    $('.sidebar-toggle').click(function () {
        //do something
        dashboardclick();
    });

    $('#db_filter_categoryID').change(function () {
        $('#search_cancel').removeClass('hide');
        $('#crm_calendar').fullCalendar('refetchEvents');
        if(subtaskTaskview == 1)
        {
            subtask_task_rpt_dashboard();
        }


    });

    $('#calEmployee').change(function () {
        $('#search_cancel').removeClass('hide');
        $('#crm_calendar').fullCalendar('refetchEvents');
        if(subtaskTaskview == 1)
        {
            subtask_task_rpt_dashboard();
        }

    });
    $('#permissiontypecal').change(function () {
        $('#search_cancel').removeClass('hide');
        $('#crm_calendar').fullCalendar('refetchEvents');
        if(subtaskTaskview == 1)
        {
            subtask_task_rpt_dashboard();
        }

    });

    $('#db_filter_statusID').change(function () {
        $('#search_cancel').removeClass('hide');
        $('#crm_calendar').fullCalendar('refetchEvents');
        if(subtaskTaskview == 1)
        {
            subtask_task_rpt_dashboard();
        }
    });


    //load_group_members();
    $("#groupID").change(function () {
        if ((this.value)) {
            load_group_members(this.value);
            return false;
        }

    });
    function viewEvent(id) {
        fetchPage('system/crm/task_edit_view', id, 'View Task', 'dashboardtask','Dashboard');
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#db_filter_statusID').val('');
        $('#db_filter_categoryID').val('');
        $('#calEmployee').val('');
        $('#permissiontypecal').val(1);
        $('#calEmployee').multiselect2('deselectAll', false);
        $('#calEmployee').multiselect2('updateButtonText');
        $('#crm_calendar').fullCalendar('refetchEvents');
        if(subtaskTaskview == 1)
        {

            subtask_task_rpt_dashboard();
        }

    }

    function clearDashboardSearchFilter() {
        $('#search_dashboard_cancel').addClass('hide');
        $('#groupID').multiselect2('deselectAll', false);
        $('#groupID').multiselect2('updateButtonText');
        $('#groupEmployeeID').multiselect2('deselectAll', false);
        $('#groupEmployeeID').multiselect2('updateButtonText');
        employeeDashboard();


    }

    function dashboardTotalDocuments_Count() {
        var employeeID = $('#groupEmployeeID').val();
        var groupID = $('#groupID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {employeeID: employeeID,groupID:groupID},
            url: "<?php echo site_url('CrmLead/dashboardTotalDocuments_Count'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#total_contacts').html(data['contact']);
                    $('#total_organization').html(data['organization']);
                    $('#total_lead').html(data['lead']);
                    $('#total_opportunity').html(data['opportunity']);
                    $('#total_tasks').html(data['task']);
                    $('#total_projects').html(data['project']);
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function best_leads() {
        var year = $('#toptenleadsYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID},
            url: "<?php echo site_url('CrmLead/load_dashboard_bestleads'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#topBestLeads').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function best_leads_permission() {
        var year = $('#toptenleadsYear').val();
        var permissiontype = $('#permissiontype').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, permissiontype: permissiontype},
            url: "<?php echo site_url('CrmLead/load_dashboard_bestleads'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#topBestLeads').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function best_opportunity() {
        var year = $('#opportunitiesYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID},
            url: "<?php echo site_url('CrmLead/load_dashboard_bestOpportunities'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#topTenOpportunities').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function lead_source() {
        var year = $('#leadsourceYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {employeeID: employeeID, year: year},
            url: "<?php echo site_url('CrmLead/load_dashboard_leadSource'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#topLeadsSource').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function dashboard_reports_year() {
        var year = $('#reportYear').val();
        var groupID = $('#groupID').val();
        var employeeID = $('#groupEmployeeID').val();
        var permissiontype = $('#permissiontype').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID,groupID:groupID},
            url: "<?php echo site_url('CrmLead/load_dashboard_reportsOfYear'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#allTopReports').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function Lead_Generation_Rate() {
        var year = $('#leads_opportunitiesYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID},
            url: "<?php echo site_url('CrmLead/load_dashboard_leadGenerationRate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#leadOpporunityGeneration').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function our_crm_team() {
        var masterID = $('#groupID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {masterID:masterID},
            url: "<?php echo site_url('CrmLead/load_dashboard_crmTeam'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ourCrmTeam').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

  function load_group_members() {
      $('#search_dashboard_cancel').removeClass('hide');
      employeeDashboard();

    }

    function dashboard_new_opprtunityConvertedTotal() {
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID},
            url: "<?php echo site_url('CrmLead/dashboard_new_opprtunityConvertedTotal'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#dashboard2_cTotalOpprtunity').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function dashboard_new_projectConvertedTotal() {
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID},
            url: "<?php echo site_url('CrmLead/dashboard_new_projectConvertedTotal'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#dashboard2_cTotalProject').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }



    function employeeDashboard() {
        dashboardTotalDocuments_Count();
        best_opportunity();
        best_leads();
        lead_source();
        dashboard_reports_year();
        Lead_Generation_Rate();
        our_crm_team();

    }

    function dashboard_new_click() {
        dashboard_new_opprtunityConvertedTotal();
        dashboard_new_projectConvertedTotal();
    }

    function opportunity_year_change() {
        best_opportunity();
    }

    function topten_year_change() {
        best_leads();
    }
    function topten_year_change_permision()
    {
        best_leads_permission();
    }

    function leadsource_year_change() {
        lead_source();
    }

    function leads_opportunities_year_change() {

    }
    
    function opencontactmodal() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            url: '<?php echo site_url('Crm/fetch_contacts_dashboard'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#contactview").html(data);
                $('#contactddmodal').modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function openorganizationmodal() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            url: '<?php echo site_url('Crm/fetch_organization_dashboard'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#organizayionview").html(data);
                $('#organizationdmodal_moredetail').modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function permissionwisetbl() {//according to permissionwise filtering
        dashboardTotalDocuments_Count_permission();
        dashboard_reports_year_user_count();
        Lead_Generation_Rate_count();
        lead_source_count();
        best_opportunity_count();
        best_leads_permission();
        //best_opportunity();
       // best_leads();
        //lead_source();
        //dashboard_reports_year();
        //Lead_Generation_Rate();
       // our_crm_team();

    }
    function dashboardTotalDocuments_Count_permission() {
        var permissiontype = $('#permissiontype').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {permissiontype: permissiontype},
            url: "<?php echo site_url('CrmLead/dashboardTotalDocuments_Count'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#total_contacts').html(data['contact']);
                    $('#total_organization').html(data['organization']);
                    $('#total_lead').html(data['lead']);
                    $('#total_opportunity').html(data['opportunity']);
                    $('#total_tasks').html(data['task']);
                    $('#total_projects').html(data['project']);
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function dashboard_reports_year_user_count() {
        var year = $('#reportYear').val();
        var groupID = $('#groupID').val();
        var employeeID = $('#groupEmployeeID').val();
        var permissiontype = $('#permissiontype').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year,permission :permissiontype},
            url: "<?php echo site_url('CrmLead/load_dashboard_reportsOfYear'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#allTopReports').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function Lead_Generation_Rate_count() {
        var year = $('#leads_opportunitiesYear').val();
        var permissiontype = $('#permissiontype').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, permission: permissiontype},
            url: "<?php echo site_url('CrmLead/load_dashboard_leadGenerationRate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#leadOpporunityGeneration').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function lead_source_count() {
        var year = $('#leadsourceYear').val();
        var permissiontype = $('#permissiontype').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {permission: permissiontype, year: year},
            url: "<?php echo site_url('CrmLead/load_dashboard_leadSource'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#topLeadsSource').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function best_opportunity_count() {
        var year = $('#opportunitiesYear').val();
        var permissiontype = $('#permissiontype').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, permission: permissiontype},
            url: "<?php echo site_url('CrmLead/load_dashboard_bestOpportunities'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#topTenOpportunities').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function totaldoccounts(datefrom,dateto)
    {

        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID,datefrom:datefrom,dateto:dateto},
            url: "<?php echo site_url('Crm/load_contacts_dashboard'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#contact_count_model').modal('show');
                $('#contacts_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function totaldoccounts_contact()
    {
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {employeeID: employeeID},
            url: "<?php echo site_url('Crm/load_contacts_dashboard_contact_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#contact_count_model').modal('show');
                $('#contacts_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function totalcountorganizationview(datefrom,dateto)
    {

        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID,datefrom:datefrom,dateto:dateto},
            url: "<?php echo site_url('Crm/load_organizationdd'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#organization_count_model').modal('show');
                $('#organization_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function lead_edit_view(path,leadID,heading,project)
        {
            $('#lead_count_model').modal('hide');
            setTimeout(function(){  fetchPage(path,leadID,heading,'dashboardlead','dashboardlead') }, 50);
        }
    function organization_edit_view(path,organizationID,heading,project)
    {
        $('#organization_count_model').modal('hide');
        setTimeout(function(){  fetchPage(path,organizationID,heading,'dashboardorganization','dashboardorganization') }, 50);
    }
    function contact_edit_view(path,contactID,heading,project)
    {
        $('#contact_count_model').modal('hide');
        setTimeout(function(){  fetchPage(path,contactID,heading,'dashboardcontact','dashboardcontact') }, 50);
    }
    function opp_edit_view(path,opportunityID,heading,project)
    {
        $('#opportunities_view_count_model').modal('hide');
        setTimeout(function(){  fetchPage(path,opportunityID,heading,'CRM','dashbardopp','dashbardopp') }, 50);
    }
    function pro_edit_view(path,projectID,heading,project)
    {
        $('#project_view_count_model').modal('hide');
        setTimeout(function(){  fetchPage(path,projectID,heading,'CRM','projectdashboard','projectdashboard') }, 50);
    }
    function task_edit_view(path,projectID,heading,project)
    {
        $('#task_view_count_model').modal('hide');
        setTimeout(function(){  fetchPage(path,projectID,heading,'dashboardtask','dashboardtask') }, 50);
    }
    function totalleadscount(datefrom,dateto)
    {
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID,datefrom:datefrom,dateto:dateto,permissiontype:permissiontype},
            url: "<?php echo site_url('CrmLead/load_leads_dashboard_dd'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#lead_count_model').modal('show');
                $('#lead_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function totaloppcount(datefrom,dateto)
    {
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID,datefrom:datefrom,dateto:dateto,permissiontype:permissiontype},
            url: "<?php echo site_url('CrmLead/load_opportunityManagement_view_dashboard'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#opportunities_view_count_model').modal('show');
                $('#Opportunities_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function totalprojectcount(datefrom,dateto)
    {
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID,datefrom:datefrom,dateto:dateto,permissiontype:permissiontype},
            url: "<?php echo site_url('CrmLead/load_projectManagement_view_dashboard_dd'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_view_count_model').modal('show');
                $('#project_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function subtask_task_rpt_dashboard(type)
    {
        $('.filtercalander').addClass('hide');
        $('.calanderview').addClass('hide');
        $('.datefilter').removeClass('hide');
        $('.tasklistview').removeClass('hide');
        subtaskTaskview = 1;
        var permissiontypecal = $("#permissiontypecal").val();
        var status = $("#db_filter_statusID").val();
        var employee =$("#calEmployee").val();
        var catergoryid = $("#db_filter_categoryID").val();
        var StartDate  = $("#datefrom").val();
        var EndDate   = $("#dateto").val();

        $.ajax({
                type: "POST",
                url: "<?php echo site_url('Crm/crm_subtask_task_dashboardrpt'); ?>",
                data:{'permissiontype':permissiontypecal,'status':status,'employee':employee,'catergoryid':catergoryid,StartDate:StartDate,EndDate:EndDate},
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#crm_calendar_report').html(data)


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });

    }
    function clander_navigation_back()
    {
       $('.calanderview').removeClass('hide');
       $('.tasklistview').addClass('hide');
       $('.filtercalander').removeClass('hide');
       $('.datefilter').addClass('hide');

    }
    function totalcountorganizationview_dashboard()
    {

        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {employeeID: employeeID},
            url: "<?php echo site_url('Crm/load_dashboard_organizationdd_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#organization_count_model').modal('show');
                $('#organization_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function totalleadscount_dashboard()
    {
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {employeeID: employeeID,permissiontype:permissiontype},
            url: "<?php echo site_url('CrmLead/load_leads_dashboard_leads'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#lead_count_model').modal('show');
                $('#lead_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function totalopportunities_dashboard()
    {
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {employeeID: employeeID,permissiontype:permissiontype},
            url: "<?php echo site_url('CrmLead/load_opportunityManagement_view_dashboard_oppo'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#opportunities_view_count_model').modal('show');
                $('#Opportunities_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function totalprojectcount_dashboard()
    {
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID,permissiontype:permissiontype},
            url: "<?php echo site_url('CrmLead/load_projectManagement_view_dashboard_dd_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_view_count_model').modal('show');
                $('#project_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function totaltask_dashboard()
    {
        $('#task_view_count_model').modal('show');
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {employeeID: employeeID,permissiontype:permissiontype},
            url: "<?php echo site_url('CrmLead/load_task_view_dashboard_dd_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#task_view_count_model').modal('show');
                $('#task_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }



</script>