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
$project_all_drop = load_all_projects(false);

if (!empty($_POST['policy_id'])) {
    $policy = $_POST['policy_id'];
} else {
    $policy = '';
}

$pagenew = $policy;
$date_format_policy = date_format_policy();
$project = load_all_project();
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
        box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
    }

    .post-doc {
        display: flex;
        width: 300px;
        height: 110px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
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

    .applicationsusermanual {
        line-height: 0;
        list-style-type: none;
        margin: 0;
        padding: 0;
        width: 100%;
        background-color: #B3D4FC;
    }

    .applicationsusermanual li {
        height: 147px;
        margin: 0 8px 8px 0;
        width: 147px;
        background: none repeat scroll 0 0 #FFFFFF;
        display: inline-block;
        float: left;
        border-radius: 20px;
    }

    .applicationsusermanual li a {
        color: #8397A6;
        display: inline-block;
        height: 100%;
        position: relative;
        width: 100%;
    }

    .applicationsusermanual li a span {
        bottom: 20px;
        display: inline-block;
        font-size: 14px;
        position: absolute;
        text-align: center;
        text-transform: uppercase;
        width: 100%;
    }

    .fontcssusermanual {
        font-size: 48px;
        margin-top: 40px;
        margin-left: 50px;
    }

    .boxnameusermanual {

        text-align: center;
        margin-top: 20px;
        padding: 0px 0px 10px 0;
    }

    .listname2usermanual {
        background-color: #696CFF;
        margin: 5px;
        padding: 10px;
        color: white;
        margin-top: 12px;
        font-size: 12px;
        text-align: center;
        width: 100px;
        margin: auto;
        border-radius: 15px;
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


                            <li class="active">
                                <a id="" data-id="0"
                                   href="#17erp_ajax_load_dashboard_template1" data-toggle="tab"
                                   aria-expanded="true"><span><i class="fa fa-tachometer" aria-hidden="true"
                                                                 style="color: #ffbe00;font-size: 16px;"></i> Dashboard</span></a>
                            </li>

                            <li class="">
                                <a id="" data-id="0"
                                   href="#17erp_ajax_load_dashboard_template2" data-toggle="tab"
                                   aria-expanded="true"><span><i class="fa fa-calendar" aria-hidden="true"
                                                                 style="color: #E74C3C;font-size: 16px;"></i>&nbspCalendar</span></a>
                            </li>
                        </ul>
                    </div>


                    <div class="panel-body" style="background-color: #ecf0f5;">

                        <div class="tab-content">
                            <div class="tab-pane active" id="17erp_ajax_load_dashboard_template1">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="box box-warning">
                                            <div class="box-header with-border">
                                                <div class="row" style="margin-top: 5px">
                                                    <div class="col-md-12" id="">
                                                        <h4 style="font-size: 100%;" class="box-title"><strong>Project /
                                                                Task Schedule</strong></h4>

                                                    </div>
                                                    <br>
                                                    <br>
                                                    <div class="form-group col-sm-4">
                                                        <div class="custom_padding">
                                                            <label for="supplierPrimaryCode">Date</label><br><!--Date-->
                                                            <label for="supplierPrimaryCode">From </label><!--From-->
                                                            <input type="text" name="filter_date_from"
                                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                                   size="16" onchange="del_order_tbl.draw()" value=""
                                                                   id="filter_date_from"
                                                                   class="input-small">
                                                            <label for="supplierPrimaryCode">&nbsp;&nbsp;To&nbsp;&nbsp;</label>
                                                            <!--To-->
                                                            <input type="text" name="filter_date_to"
                                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                                   size="16" onchange="del_order_tbl.draw()" value=""
                                                                   id="filter_date_to"
                                                                   class="input-small">
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-sm-4">
                                                        <label for=" "> Project </label><br>
                                                        <?php echo form_dropdown('project[]', $project, '', 'class="form-control" id="project" multiple="multiple"'); ?>
                                                    </div>
                                                    <div class="col-md-1" style="margin-top: 25px">
                                                        <button type="button" class="btn btn-primary"
                                                                onclick="Task_Schedule()">
                                                            <i class="fa fa-plus"></i> Generate
                                                        </button>
                                                    </div>

                                                    <div class="col-md-12" style="margin-top: 1%">
                                                        <div class="projecttaskshedule" id="projecttaskshedule">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="box box-success">
                                            <div class="box-header with-border">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <h4 style="font-size: 100%;" class="box-title"><strong>Timesheet
                                                                Management</strong></h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-12" style="margin-left: -9px;">
                                                    <div class="col-md-3">
                                                        <div class="responsiveimg">
                                                            <ul class="applicationsusermanual">
                                                                <li>
                                                                    <a onclick="mytimesheet(<?php echo current_userID() ?>)"
                                                                       class="a">
                                                                        <i style="color: #1c2e3b"
                                                                           class="fa fa-calendar fontcssusermanual"
                                                                           aria-hidden="true"></i>

                                                                        <div class="boxnameusermanual">My Timesheet
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="responsiveimg">
                                                            <ul class="applicationsusermanual">
                                                                <li>
                                                                    <a onclick="approvaltimesheet()" class="a">
                                                                        <i style="color: #409600"
                                                                           class="fa fa-check-circle fontcssusermanual"
                                                                           aria-hidden="true"></i>

                                                                        <div class="boxnameusermanual">Approve Timesheet
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="tab-pane" id="17erp_ajax_load_dashboard_template2">
                                <div class="row" style="margin-top: 5px">
                                    <div class="col-md-12" id="1T17">
                                        <div class="box box-warning">
                                            <div class="box-header with-border">
                                                <div class="col-sm-1">
                                                    <h4 class="box-title">Calendar</h4>
                                                </div>

                                                <div class="col-sm-3 filtercalander">

                                                </div>

                                                <div class="col-sm-2 datefilter hide">
                                                    <span style="font-weight: bold;">Start Date</span>
                                                    <br>
                                                    <div class="input-group datepic ">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="datefrom"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               value="<?php echo $start_date; ?>" id="datefrom"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 datefilter hide">
                                                    <span style="font-weight: bold;">End Date</span>
                                                    <br>
                                                    <div class="input-group datepic">
                                                        <div class="input-group-addon"><i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="dateto"
                                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                               value="<?php echo $current_date; ?>" id="dateto"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                                <?php
                                                if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) { ?>
                                                    <div class="col-sm-2 hide" style="margin-right: 1%;">
                                                        <span style="font-weight: bold;">Users</span>
                                                        <br>
                                                        <?php echo form_dropdown('calEmployee', $employee_arr_filter, '', 'class="form-control" multiple id="calEmployee"'); ?>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="col-sm-2 hide">
                                                        <span style="font-weight: bold;">Assignee </span>
                                                        <br>
                                                        <?php echo form_dropdown('permissiontypecal', array('1' => 'All', '2' => 'Assign For Me'), '', 'class="form-control pull-right" id="permissiontypecal"'); ?>
                                                    </div>
                                                <?php } ?>

                                                <div class="col-sm-2">
                                                    <span style="font-weight: bold;">Projects</span>
                                                    <?php echo form_dropdown('projects[]', $project_all_drop, '',
                                                        ' class="form-control " multiple id="projects"'); ?>
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
                                                        <div id='pm_calendar'></div>
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
                    Close
                </button>
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
                    Close
                </button>
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
                    Close
                </button>
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
                    Close
                </button>
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
                    Close
                </button>
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
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="mytimesheetmodel">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Timesheet Management - My Timesheet</h4>
            </div>
            <?php echo form_open('', 'role="form" id="mytimesheetfrm"'); ?>
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2" style="width: 9%;">
                        <label class="title">Date From</label>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-2">
                            <div class="input-group datepic_mytimesheet">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="datefrommytask"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="datefrommytask"
                                       class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group col-sm-2" style="width: 9%;">
                            <label class="title">Date To</label>
                        </div>
                        <div class="form-group col-sm-2">
                            <div class="input-group datepic_mytimesheet">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="datetomytask"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="datetomytask"
                                       class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group col-sm-2" style="width: 9%;">
                            <button type="button" onclick="generate_mytimesheet(<?php echo current_userID(); ?>)"
                                    class="btn btn-primary pull-right">Generate
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <div id="my_timesheetview">

                            </div>
                        </div>
                    </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="mytimesheet_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 95%;z-index: 1000000000;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close timesheethide" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">My Timesheets</h4>
            </div>
            <form class="form-horizontal" id="submit-mytimesheet">
                <input type="hidden" name="timesheetmasterID" id="timesheetmasterID">
                <div class="modal-body">
                    <div id="my_timesheetview_view">

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="button" onclick="mytimesheet_submit();">Submit</button>
                    <button class="btn btn-default timesheethide" type="button">Close</button>
                </div>

        </div>
        </form>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="mytimesheet_modal_approval">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Timesheet Management - Approval Timesheet</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div id="my_timesheetview_approval">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="mytimesheet_modal_approval_view_all">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title">Timesheet Management - Approval Timesheet</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div id="my_timesheetview_approval_viewall">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var subtaskTaskview = 0;
    $(document).ready(function () {

     
        Task_Schedule();
        $('#projects').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('#project').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        <?php if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) { ?>
/*         dashboardTotalDocuments_Count();
        dashboard_reports_year();
        Lead_Generation_Rate();
        lead_source();
        best_opportunity();
        best_leads(); */

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
   /*      permissionwisetbl(); */
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
           
        });
        $('.datepic_mytimesheet').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('#pm_calendar').fullCalendar('render');
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


        $('#pm_calendar').fullCalendar({
            customButtons: {
                myCustomButton: {
                    text: 'Task List',
                    click: function () {
                        //subtask_task_rpt_dashboard(1);
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
                url: '<?php echo site_url('WidgetDashboard/allCalenderEvents'); ?>',
                data: function () {
                    return {
                        projects: $("#projects").val()
                    };


                },
                type: "POST",
                cache: false

            },
            dayClick: function (date) {
                /*swal({
                        title: "Are you sure?",
                        text: "You want to create a task!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#00A65A",
                        confirmButtonText: "Create Task"
                    },
                    function () {
                        fetchPage('system/crm/create_new_task', '', 'Create Task', 2, date.format());
                    });*/

            },
            eventRender: function (event, element) {
                /*                element.find(".fc-content").append("<i style='color: white; font-size: 12px' class='fa fa-eye pull-right closeon' aria-hidden='true' title='View'></i>");*/
                element.find(".fc-content").click(function () {
                    //viewEvent(event._id);
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
        $('#pm_calendar').fullCalendar('refetchEvents');
       

    });

    $('#calEmployee').change(function () {
        $('#search_cancel').removeClass('hide');
        $('#pm_calendar').fullCalendar('refetchEvents');
      

    });
    $('#permissiontypecal').change(function () {
        $('#search_cancel').removeClass('hide');
        $('#pm_calendar').fullCalendar('refetchEvents');
     
    });

    $('#db_filter_statusID').change(function () {
        $('#search_cancel').removeClass('hide');
        $('#pm_calendar').fullCalendar('refetchEvents');
    
    });


    //load_group_members();
    $("#groupID").change(function () {
        if ((this.value)) {
            load_group_members(this.value);
            return false;
        }

    });

    function viewEvent(id) {
        fetchPage('system/crm/task_edit_view', id, 'View Task', 'dashboardtask', 'Dashboard');
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#db_filter_statusID').val('');
        $('#db_filter_categoryID').val('');
        $('#calEmployee').val('');
        $('#permissiontypecal').val(1);
        $('#calEmployee').multiselect2('deselectAll', false);
        $('#calEmployee').multiselect2('updateButtonText');
        $('#pm_calendar').fullCalendar('refetchEvents');
      

    }

    function clearDashboardSearchFilter() {
        $('#search_dashboard_cancel').addClass('hide');
        $('#groupID').multiselect2('deselectAll', false);
        $('#groupID').multiselect2('updateButtonText');
        $('#groupEmployeeID').multiselect2('deselectAll', false);
        $('#groupEmployeeID').multiselect2('updateButtonText');
        employeeDashboard();


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

   
   

    function our_crm_team() {
        var masterID = $('#groupID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {masterID: masterID},
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
       
        best_opportunity();
        best_leads();
        lead_source();
     
        our_crm_team();

    }

    function dashboard_new_click() {
        dashboard_new_opprtunityConvertedTotal();
        dashboard_new_projectConvertedTotal();
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
       
      
       
       
     
        /* best_leads_permission(); */
        //best_opportunity();
        // best_leads();
        //lead_source();
        //dashboard_reports_year();
        //Lead_Generation_Rate();
        // our_crm_team();

    }

  






  

    function totaldoccounts(datefrom, dateto) {

        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID, datefrom: datefrom, dateto: dateto},
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


    function totaldoccounts_contact() {
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

    function totalcountorganizationview(datefrom, dateto) {

        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {year: year, employeeID: employeeID, datefrom: datefrom, dateto: dateto},
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

    function lead_edit_view(path, leadID, heading, project) {
        $('#lead_count_model').modal('hide');
        setTimeout(function () {
            fetchPage(path, leadID, heading, 'dashboardlead', 'dashboardlead')
        }, 50);
    }

    function organization_edit_view(path, organizationID, heading, project) {
        $('#organization_count_model').modal('hide');
        setTimeout(function () {
            fetchPage(path, organizationID, heading, 'dashboardorganization', 'dashboardorganization')
        }, 50);
    }

    function contact_edit_view(path, contactID, heading, project) {
        $('#contact_count_model').modal('hide');
        setTimeout(function () {
            fetchPage(path, contactID, heading, 'dashboardcontact', 'dashboardcontact')
        }, 50);
    }

    function opp_edit_view(path, opportunityID, heading, project) {
        $('#opportunities_view_count_model').modal('hide');
        setTimeout(function () {
            fetchPage(path, opportunityID, heading, 'CRM', 'dashbardopp', 'dashbardopp')
        }, 50);
    }

    function pro_edit_view(path, projectID, heading, project) {
        $('#project_view_count_model').modal('hide');
        setTimeout(function () {
            fetchPage(path, projectID, heading, 'CRM', 'projectdashboard', 'projectdashboard')
        }, 50);
    }

    function task_edit_view(path, projectID, heading, project) {
        $('#task_view_count_model').modal('hide');
        setTimeout(function () {
            fetchPage(path, projectID, heading, 'dashboardtask', 'dashboardtask')
        }, 50);
    }

    function totalleadscount(datefrom, dateto) {
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                year: year,
                employeeID: employeeID,
                datefrom: datefrom,
                dateto: dateto,
                permissiontype: permissiontype
            },
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

    function totaloppcount(datefrom, dateto) {
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                year: year,
                employeeID: employeeID,
                datefrom: datefrom,
                dateto: dateto,
                permissiontype: permissiontype
            },
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

    function totalprojectcount(datefrom, dateto) {
        var permissiontype = $('#permissiontype').val();
        var year = $('#reportYear').val();
        var employeeID = $('#groupEmployeeID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                year: year,
                employeeID: employeeID,
                datefrom: datefrom,
                dateto: dateto,
                permissiontype: permissiontype
            },
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

    

    function clander_navigation_back() {
        $('.calanderview').removeClass('hide');
        $('.tasklistview').addClass('hide');
        $('.filtercalander').removeClass('hide');
        $('.datefilter').addClass('hide');

    }

   






    function mytimesheet(userID) {
        $('#datefrommytask').val('');
        $('#datetomytask').val('');
        $('#my_timesheetview').html('');
        load_mytimesheet(userID)
        $('#mytimesheetmodel').modal('show');
    }

    function generate_mytimesheet(userID) {
        var data = $('#mytimesheetfrm').serializeArray();
        data.push({"name": "user", "value": userID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/generate_mytimesheet'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1])
                load_mytimesheet(userID);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function load_mytimesheet(userID) {
        var data = $('#mytimesheetfrm').serializeArray();
        data.push({"name": "user", "value": userID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: data,
            url: "<?php echo site_url('Boq/load_timesheet_mytimesheet'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#my_timesheetview').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });


    }

    function view_mytimesheets(timesheetID) {
        $('#timesheetmasterID').val(timesheetID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {timesheetID: timesheetID},
            url: "<?php echo site_url('Boq/load_timesheet_mytimesheet_submit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#my_timesheetview_view').html(data);
                $('#mytimesheet_modal').modal('show');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });


    }

    $(".timesheethide").click(function () {
        $('#mytimesheet_modal').modal('hide');
    });

    function mytimesheet_submit() {
        var mytimesheet = $('#timesheetmasterID').val();
        var data = $('#submit-mytimesheet').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/mytimesheet_submit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    view_mytimesheets(mytimesheet);
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function approvaltimesheet() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('Boq/load_timesheet_mytimesheet_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#my_timesheetview_approval').html(data);
                $('#mytimesheet_modal_approval').modal('show');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function view_all_approvals(timesheetMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            data: {'timesheetMasterID': timesheetMasterID},
            dataType: 'html',
            url: "<?php echo site_url('Boq/load_timesheet_mytimesheet_approval_viewall'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#my_timesheetview_approval_viewall').html(data);
                $('#mytimesheet_modal_approval_view_all').modal('show');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function approvatimesheet(timesheetDetailID, timesheetMasterID, currentlevelno) {

        swal({
                title: "Are you sure",
                text: "You want to Approve this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        timesheetDetailID: timesheetDetailID,
                        timesheetMasterID: timesheetMasterID,
                        currentlevelno: currentlevelno
                    },
                    url: "<?php echo site_url('Boq/timesheet_approval'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            view_all_approvals(timesheetMasterID);
                            approvaltimesheet();

                        }

                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });


    }

    function Task_Schedule() {
        var filter_date_from = $('#filter_date_from').val();
        var filter_date_to = $('#filter_date_to').val();
        var project = $('#project').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {filter_date_from:filter_date_from,filter_date_to:filter_date_to,project:project},
            url: "<?php echo site_url('Boq/load_projecttaskschedule'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#projecttaskshedule').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

</script>