<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('crm_advance_reporting');
echo head_page($title, false);


/*echo head_page('Advanced Reporting', false);*/
$this->load->helper('crm_helper');
$status_arr_filter = all_task_status(false);
$supplier_arr = all_supplier_drop(false);
$date_format_policy = date_format_policy();
$groupmaster_arr = all_crm_groupMaster(false);
$assignees_arr_filter = load_all_employees_taskFilter(false);
$types_arr_filter = all_campaign_types(false);
$status_arr_filter_campaign = all_campaign_status(false);
$category_arr_filter_task = load_all_categories(false);
$assignees_campaign_arr_filter = load_all_employees_campaignFilter(false);
$employees_arr = all_crm_employees_drop(true);
$status_arr_filter_leads = lead_status();
$status_arr_oppo = all_opportunities_status();
$status_arr_pro = all_project_status();
$category_arr_pro = all_projects_category();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate = current_date(FALSE);
$startdate = date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);

$seg_p = fetch_segment_v2();
$arr_employees = fetch_employees_by_company_multiple();
$arr_crm_products = all_crm_product_master();

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">

<style>
    #list-main .left-sidenav > .active > a {
        position: relative;
        z-index: 2;
        border-right: 0 !important;
    }

    #list-main .nav-list > .active > a, .nav-list > .active > a:hover {
        padding-left: 12px;
        font-weight: normal;
        color: #dd4b39;
        text-shadow: none;
        background-color: #dcdcdc;
        border-left: 3px solid #dd4b39;
    }

    #list-main .nav-list > .active > a, .nav-list > .active > a:hover, .nav-list > .active > a:focus {
        color: #dd4b39;

        background-color: rgba(239, 239, 239, 0.75);
    }

    #list-main .left-sidenav > li > a {
        display: block;
        width: 176px \9;
        margin: 0;
        padding: 4px 7px 4px 15px;
    !important;
        padding: 6px;
        font-size: 13px;

    }

    #list-main .nav-list > li > a {

        color: #222;
    }

    #list-main .nav-list > li > a, .nav-list .nav-header {

        text-shadow: 0 1px 0 rgba(255, 255, 255, .5);
    }

    #list-main .nav > li > a {
        display: block;
    }

    #list-main a, a:hover, a:active, a:focus {
        outline: 0;
    }

    #list-main .left-sidenav > .active {
        border-right: none;
        background-color: #f5f5f5;
    }

    #list-main.left-sidenav li {
        border-bottom: 1px solid #e5e5e5;
    }

    #list-main .left-sidenav li {
        border-bottom: 1px solid #e5e5e5;
    }

    #list-main li {
        line-height: 20px;
    }

    #list-main .nav-list {
        padding-right: 0px;
        padding-left: 0px;
    }

    #list-main a {
        text-decoration: none;
    }

    #list-main .left-sidenav .icon-chevron-right {
        float: right;
        margin-top: 2px;
        margin-left: -6px;
        opacity: .25;
        padding-right: 4px;

    }

    .flex {
        display: flex;
    }

    #list-main .sidebar-left {
        float: left;
    }

    article, aside, details, figcaption, figure, footer, header, hgroup, nav, section {
        display: block;
    }

    #list-main .left-sidenav {
        width: 200px;
        padding: 0;
        background-color: #fff;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        border: 1px solid #e5e5e5;
    }

    #list-main.nav-list {
        padding-right: 15px;
        padding-left: 15px;
        margin-bottom: 0;
    }

    #list-main .nav {
        margin-bottom: 20px;
        margin-left: 0;
        list-style: none;
    }

    #list-main ul, ol {
        padding: 0;
        margin: 0 0 10px 25px;
    }

    #list-main .left-sidenav li {
        border-bottom: 1px solid #e5e5e5;
    }

    form {
        margin: 0 0 20px;
    }

    fieldset {
        padding: 0;
        margin: 0;
        border: 0;
    }

    section {
        padding-top: 0;
    }

    article, aside, details, figcaption, figure, footer, header, hgroup, nav, section {
        display: block;
    }

    .past-posts .posts-holder {
        padding: 0 0 10px 4px;
        margin-right: 10px;
    }

    .past-info {
        background: #fff;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        padding: 0 0 8px 10px;
        margin-left: 2px;
    }

    .title-icon {
        margin-right: 8px;
        vertical-align: text-bottom;
    }

    article, aside, details, figcaption, figure, footer, header, hgroup, nav, section {
        display: block;
    }

    .system-settings-item {
        margin-top: 20px;
    }

    .fa-chevron-right {
        color: rgba(149, 149, 149, 0.75);
        margin-top: 4px;
    }

    .system-settings-item {
        margin-top: 20px;
    }

    .system-settings-item img {
        vertical-align: middle;
        padding-right: 5px;
        margin: 2px;
    }

    .system-settings-item a {
        padding: 10px;
        text-decoration: none;
        font-weight: bold;
    }

    .past-info #toolbar, .past-info .toolbar {
        background: #f8f8f8;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        border-radius: 3px 3px 0 0;
        -webkit-border-radius: 3px 3px 0 0;
        border: #dcdcdc solid 1px;
        padding: 5px 15px 12px 10px;
        line-height: 2;
        height: 29px;
    }

    .system-settings-item .fa {
        text-decoration: none;
        color: black;
        font-size: 16px;
        padding-right: 5px;
    }

    .system-settings-item .fa {
        text-decoration: none;
        color: black;
        font-size: 16px;
        padding-right: 5px;
    }

    .width100p {
        width: 100%;
    }

    .user-table {
        width: 100%;
    }

    .bottom10 {
        margin-bottom: 10px !important;
    }

    .btn-toolbar {
        margin-top: -2px;
    }

    table {
        max-width: 100%;
        background-color: transparent;
        border-collapse: collapse;
        border-spacing: 0;
    }

    #search_dashboard_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

</style>

<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/bootstrapcolorpicker/dist/css/bootstrap-colorpicker.css'); ?>">
<script src="<?php echo base_url('plugins/bootstrapcolorpicker/dist/js/bootstrap-colorpicker.js'); ?>"></script>
<div id="filter-panel" class="collapse filter-panel">
</div>
<?php echo form_open('login/loginSubmit', ' id="frm_report_filter" class="form-horizontal" type="post" action="" name="frm_report_filter" role="form"'); ?>
<input type="hidden" name="sys" id="reportpdftype">
<div class="row taskassignee">
    <div class="col-sm-3">
    </div>

    <div class="col-sm-2 taskassignee">
        <span style="font-weight: bold;">Assignee </span>
        <br>
        <?php echo form_dropdown('assigneeid', $assignees_arr_filter, '', 'class="form-control select2 pull-right " id="assigneeid" onchange="loadreportfilterview()"'); ?>

    </div>

    <div class="col-sm-2 taskassignee">
        <span style="font-weight: bold;">Status </span>
        <br>
        <?php echo form_dropdown('statusid', $status_arr_filter, '', 'class="form-control select2 pull-right " id="statusid" onchange="loadreportfilterview()"'); ?>
    </div>

    <div class="col-sm-2 taskassignee">
        <span style="font-weight: bold;">Category </span>
        <br>
        <?php echo form_dropdown('categorytaskassignee', $category_arr_filter_task, '', 'class="form-control select2 pull-right " id="categorytaskassignee" onchange="loadreportfilterview()"'); ?>
    </div>
<br>
<br>
<br>
<div class="col-sm-3">
    </div>
    <div class="col-sm-2 taskassignee">
        <span style="font-weight: bold;">Start Date</span>
        <br>
        <div class="input-group datepic ">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <input type="text" name="datefromtask"
                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                   value=" " id="datefromtask" class="form-control">
        </div>
    </div>

    <div class="col-sm-2 taskassignee">
        <span style="font-weight: bold;">End Date</span>
        <br>
        <div class="input-group datepic">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <input type="text" name="datetotask"
                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                   value=" " id="datetotask" class="form-control">
        </div>
    </div>
  <div class="col-sm-1 taskassignee" id="search_dashboard_cancel" style="margin-top: 1%;">
                    <span class="tipped-top"><a id="cancelSearchDashboard" href="#"
                                                onclick="clearDashboardSearchFilter()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
    </div>
</div>

<div class="row campaingn">
    <div class="col-sm-5">

    </div>
    <div class="col-sm-2 campaingn">
          <span style="font-weight: bold;">Assignee </span>
        <br>
        <?php echo form_dropdown('assigneescamp', $assignees_campaign_arr_filter, '', 'class="form-control select2 pull-right " id="assigneescamp" onchange="loadreportfilterview()"'); ?>
    </div>

    <div class="col-sm-2 campaingn">
        <span style="font-weight: bold;">Status </span>
    <br>
        <?php echo form_dropdown('statusidcamp', $status_arr_filter_campaign, '', 'class="form-control select2 pull-right " id="statusidcamp" onchange="loadreportfilterview()"'); ?>
    </div>
    <div class="col-sm-2 campaingn">
         <span style="font-weight: bold;">Category </span>
    <br>
        <?php echo form_dropdown('typeID', $types_arr_filter, '', 'class="form-control select2 pull-right " id="typeID" onchange="loadreportfilterview()"'); ?>

    </div>



    <div class="col-sm-1 campaingn hide" id="search_dashboard_cancelcamp">
                    <span class="tipped-top"><a id="cancelSearchDashboard" href="#"
                                                onclick="clearDashboardSearchFiltercamp()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
    </div>
</div>

<div class="row s_target mb-3">   
    <div class="col-sm-5">
    </div>
    <div class="col-sm-2 s_target">
        <span style="font-weight: bold;">Start Dates</span>
        <br>
        <div class="input-group datepicsales ">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <input type="text" name="datefromtasksales"
                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                   value=" " id="datefromtasksales" class="form-control">
        </div>
    </div>

    <div class="col-sm-2 s_target">
        <span style="font-weight: bold;">End Dates</span>
        <br>
        <div class="input-group datepicsales">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <input type="text" name="datetotasksales"
                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                   value=" " id="datetotasksales" class="form-control">
        </div>
    </div>
    <div class="col-sm-1 s_target" id="search_dashboard_cancel2" style="margin-top: 1%;">
                    <span class="tipped-top"><a id="cancelSearchDashboard" href="#"
                                                onclick="clearDashboardSearchFilter('sales')"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
    </div>
</div>

<div class="row mT20 s_target">
    <div class="col-sm-5">

    </div>
    <div class="col-sm-2 s_target">
          <span style="font-weight: bold;">Segment</span>
        <br>
        <?php echo form_dropdown('segmentID', $seg_p, '', 'class="form-control select2 pull-right " id="segmentID" onchange="loadreportfilterview()"'); ?>
    </div>

    <div class="col-sm-2 s_target">
        <span style="font-weight: bold;">Products</span>
    <br>
        <?php echo form_dropdown('arr_crm_productsID', $arr_crm_products, '', 'class="form-control select2 pull-right " id="arr_crm_productsID" onchange="loadreportfilterview()"'); ?>
    </div>
    <div class="col-sm-2 s_target">
         <span style="font-weight: bold;">Employee</span>
    <br>
        <?php echo form_dropdown('filter_userID', $arr_employees, '', 'class="form-control select2 pull-right " id="filter_userID" onchange="loadreportfilterview()"'); ?>

    </div>

    <div class="col-sm-1 s_target " id="search_dashboard_cancelcamp2">
                    <span class="tipped-top"><a id="cancelSearchDashboard" href="#"
                                                onclick="clearDashboardSearchFiltercampSales()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
    </div>
</div>

<div class="row leadsrpt">
    <div class="col-sm-5">

    </div>
    <div class="col-sm-2 leadsrpt">
    </div>

    <div class="col-sm-2 leadsrpt">
        <span style="font-weight: bold;">Employee </span>
        <br>
        <?php echo form_dropdown('leadsrptuser', $employees_arr, '', 'class="form-control select2 pull-right " id="leadsrptuser" onchange="loadreportfilterview()"'); ?>
    </div>

    <div class="col-sm-2 leadsrpt">
        <span style="font-weight: bold;">Status </span>
        <br>
        <?php echo form_dropdown('leadsstatus', $status_arr_filter_leads, '', 'class="form-control select2 pull-right " id="leadsstatus" onchange="loadreportfilterview()"'); ?>
    </div>



    <div class="col-sm-1 leadsstatus hide" id="search_dashboard_cancellead">
                    <span class="tipped-top"><a id="cancelSearchDashboard" href="#"
                                                onclick="clearDashboardSearchFilterlead()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
    </div>
</div>
<div class="row opprpt">
    <div class="col-sm-3">

    </div>
    <!--<div class="col-sm-2 opprpt">

    </div>-->

    <div class="col-sm-2 opprpt">
        <span style="font-weight: bold;">Status </span>
        <br>
        <?php echo form_dropdown('opporstatusid', $status_arr_oppo, '', 'class="form-control select2 pull-right " id="opporstatusid" onchange="loadreportfilterview()"'); ?>
    </div>

    <div class="col-sm-2 opprpt">
        <span style="font-weight: bold;">Employee </span>
        <br>
        <?php echo form_dropdown('responsiblePersonEmpIDopprpt', $employees_arr, '', 'class="form-control select2" id="responsiblePersonEmpIDopprpt" onchange="loadreportfilterview()" '); ?>
    </div>
    <div class="col-sm-2 opprpt">
        <span style="font-weight: bold;">Sort By Value </span>
        <br>
        <?php echo form_dropdown('sortbyval', array('1'=>'Ascending','2'=>'Descending'), '', 'class="form-control select2" id="sortbyval" onchange="loadreportfilterview()" '); ?>
    </div>


    <div class="col-sm-1 opprpt hide" id="search_dashboard_cancelopprpt">
                    <span class="tipped-top"><a id="cancelSearchDashboard" href="#"
                                                onclick="clearDashboardSearchFilteropprpt()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
    </div>
</div>
<div class="row pro">
    <div class="col-sm-5">

    </div>
    <div class="col-sm-2 pro">
        <span style="font-weight: bold;">Status </span>
        <br>
        <?php echo form_dropdown('prostatusid', $status_arr_pro, '', 'class="form-control select2 pull-right " id="prostatusid" onchange="loadreportfilterview()"'); ?>
    </div>

    <div class="col-sm-2 pro">
        <span style="font-weight: bold;">Employee </span>
<br>
        <?php echo form_dropdown('responsiblePersonEmpIDpro', $employees_arr, '', 'class="form-control select2" id="responsiblePersonEmpIDpro" onchange="loadreportfilterview()" '); ?>
    </div>
    <div class="col-sm-2 pro">
        <span style="font-weight: bold;">Category </span>
        <br>
        <?php echo form_dropdown('catergorypro', $category_arr_pro, '', 'class="form-control select2" id="catergorypro" onchange="loadreportfilterview()" '); ?>
    </div>


    <div class="col-sm-1 pro hide" id="search_dashboard_cancelpro">
                    <span class="tipped-top"><a id="cancelSearchDashboard" href="#"
                                                onclick="clearDashboardSearchFilterpro()"><img
                                src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
    </div>
</div>
<?php echo form_close(); ?>
<div class="row" style="margin-top: 1%;">
    <div class="col-md-12">
        <div id="list-main" class="top15 ">
            <aside class="sidebar-left col-3 col-col-lg-3 col-col-sm-3 col-col-md-3 col-col-xl-3" style="width: 17%;">
                <ul id="list" class="nav nav-list left-sidenav">
                    <li class="contact" value="1"><a href="#" onclick="configuration_page('contact','html')"><?php echo $this->lang->line('crm_contact_reports');?> <i
                                class="fa fa-chevron-right pull-right"></i></a></li><!--Contact Reports-->
                    <li class="organization" value="2"><a href="#" onclick="configuration_page('organization','html')"><?php echo $this->lang->line('crm_organization_reports');?> <i
                                class="fa fa-chevron-right pull-right"></i></a></li><!--Organization Reports-->
                    <li class="task" value="3"><a href="#" onclick="configuration_page('task','html')"><?php echo $this->lang->line('crm_task_reports');?> <i
                                class="fa fa-chevron-right pull-right"></i></a></li><!--Task Reports-->
                    <li class="campaign" value="4"><a href="#" onclick="configuration_page('campaign','html')"> <i
                                class="fa fa-chevron-right pull-right"></i><?php echo $this->lang->line('crm_campaign_reports');?> </a></li><!--Campaign Reports-->
                    <li class="leadnew" value="5"><a href="#" onclick="configuration_page('leadnew','html')"><?php echo $this->lang->line('crm_lead_reports');?> <i
                                class="fa fa-chevron-right pull-right"></i></a></li><!--Lead Reports-->
                    <li class="opportunity" value="6"><a href="#" onclick="configuration_page('opportunity','html')"><?php echo $this->lang->line('crm_opportunity_reports');?> <i
                                class="fa fa-chevron-right pull-right"></i></a></li><!--Opportunity Reports-->
                    <li class="project" value="7"><a href="#" onclick="configuration_page('project','html')"><?php echo $this->lang->line('crm_project_reports');?> <i
                                class="fa fa-chevron-right pull-right"></i></a></li><!--Project Reports-->
                    <li class="projectmoni" value="8"><a href="#" onclick="configuration_page('projectmoni','html')">Project Monitoring <i
                                class="fa fa-chevron-right pull-right"></i></a></li>
                    <li class="sales_target" value="9"><a href="#" onclick="configuration_page('sales_target','html')">Sales Target <i
                                class="fa fa-chevron-right pull-right"></i></a></li>
                </ul>
            </aside>
            <div id="load_configuration_view" class="col-9 col-lg-9 col-sm-9 col-md-9 col-xl-9" style="width: 81%;">
                <fieldset>

                </fieldset>
            </div>
        </div>
    </div>
</div>

<?php echo form_open('login/loginSubmit', ' id="frm_report_filterPprojectmoni" class="form-horizontal" type="post" action="" name="frm_report_filterPprojectmoni" role="form"'); ?>
<div class="row">
    <input type="hidden" name="sys" id="reportpdftypepdf">
    <input type="hidden" name="dateyear" id="dateyearpdf">
    <input type="hidden" name="dateto" id="datetopdf">
    <input type="hidden" name="datefrom" id="datefrompdf">
    <input type="hidden" name="category[]" id="categoryIDpdf">
</div>
<?php echo form_close(); ?>

<div class="modal fade" id="project_dd_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Project Drill Down</h4>
            </div>
            <div class="modal-body">
                <div id="ProjectddMaster_view">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="task_dd_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Task Drill Down</h4>
            </div>
            <div class="modal-body">
                <div id="taskddMaster_view">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">

    $(document).ready(function () {
        $('.select2').select2();
        page_name = <?php echo json_encode(trim($this->input->post('page_name'))); ?>;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('#groupID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            configuration_page('task','html');
        });

        $('.datepicsales').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            configuration_page('sales_target','html');
        });


        $('#groupEmployeeID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        if(page_name=='CRM'){
            configuration_page('contact', 'html');
        }else{
            configuration_page('projectmoni', 'html');
        }


    });

    function configuration_page(sys, page) {
        var groupEmployeeID = $('#groupEmployeeID').val();
        var groupID = $('#groupID').val();
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();
        var dateyear = $('#dateyear').val();
        var category = $('#categoryID').val();
        var assigneeid =  $('#assigneeid').val();
        var statusid =  $('#statusid').val();
        var campcatid =  $('#typeID').val();
        var campstatusid = $('#statusidcamp').val();
        var catergorytask = $('#categorytaskassignee').val();
        var assigneescamp = $('#assigneescamp').val();
        var leadsrptuser = $('#leadsrptuser').val();
        var leadsstatus = $('#leadsstatus').val();
        var opporstatusid = $('#opporstatusid').val();
        var filter_userID = $('#filter_userID').val();
        var segmentID = $('#segmentID').val();
        var arr_crm_productsID = $('#arr_crm_productsID').val();
        var responsiblePersonEmpIDopprpt = $('#responsiblePersonEmpIDopprpt').val();
        var prostatusid = $('#prostatusid').val();
        var responsiblePersonEmpIDpro = $('#responsiblePersonEmpIDpro').val();
        var catergorypro = $('#catergorypro').val();
        var datefromtask = $('#datefromtask').val();
        var datetotask = $('#datetotask').val();
        var datefromtasksales = $('#datefromtasksales').val();
        var datetotasksales = $('#datetotasksales').val();
        var sortbyval = $('#sortbyval').val();
        
        if(sys=='task'){
            $('.taskassignee').removeClass('hidden');
        }else{
            $('.taskassignee').addClass('hidden');
        }
        if(sys == 'campaign')
        {
            $('.campaingn').removeClass('hidden');
        }else
        {
            $('.campaingn').addClass('hidden');
        }
        if(sys == 'leadnew')
        {
            $('.leadsrpt').removeClass('hidden');
        }else
        {
            $('.leadsrpt').addClass('hidden');
        }
        if(sys == 'opportunity') {
            $('.opprpt').removeClass('hidden');
        }else
        {
            $('.opprpt').addClass('hidden');
        }
        if(sys == 'project') {
            $('.pro').removeClass('hidden');
        }else
        {
            $('.pro').addClass('hidden');
        }
        if(sys == 'sales_target') {
            $('.s_target').removeClass('hidden');
        }else
        {
            $('.s_target').addClass('hidden');
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {sys: sys, groupEmployeeID: groupEmployeeID, page: page, groupID: groupID, datefrom: datefrom, dateto: dateto, dateyear: dateyear, category: category,assigneeid:assigneeid,statusid:statusid,typeID:campcatid,statusidcamp:campstatusid,categorytaskassignee:catergorytask,assigneescamp:assigneescamp,leadsrptuser:leadsrptuser,leadsstatus:leadsstatus,opporstatusid:opporstatusid,responsiblePersonEmpIDopprpt:responsiblePersonEmpIDopprpt,prostatusid:prostatusid,responsiblePersonEmpIDpro:responsiblePersonEmpIDpro,filter_userID:filter_userID,arr_crm_productsID:arr_crm_productsID,segmentID:segmentID,catergorypro:catergorypro,datefromtask:datefromtask,datefromtasksales:datefromtasksales,datetotasksales:datetotasksales, datetotask: datetotask,sortbyvalue:sortbyval},
            url: "<?php echo site_url('CrmLead/reports_management'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#load_configuration_view').html(data);
                $('#list-main li').removeClass('active');
                $('.' + sys).addClass('active');


                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_contact(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'contactID': id},
                    url: "<?php echo site_url('Crm/delete_contact_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        getContactManagement_tableView();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getContactManagement_tableView();
    }

    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('.contactsorting').removeClass('selected');
        $('#searchTask').val('');
        getContactManagement_tableView();
    }

    function load_contact_filter(value, id) {
        $('.contactsorting').removeClass('selected');
        $('#sorting_' + id).addClass('selected');
        $('#search_cancel').removeClass('hide');
        getContactManagement_tableView(value)
    }

    function clearDashboardSearchFilter(view = null) {
       
        if(view == 'sales'){
            $('#datefromtasksales').val(' ');
            $('#datetotasksales').val(' ');
        }else{
            $('#search_dashboard_cancel').addClass('hide');
            $('#assigneeid').val(null).trigger('change');
            $('#categorytaskassignee').val(null).trigger('change');
            $('#statusid').val(null).trigger('change');
            $('#datefromtask').val(' ');
            $('#datetotask').val(' ');
        }

        loadreportfilterview();
    }

    function clearDashboardSearchFiltercamp() {
        $('#search_dashboard_cancel').addClass('hide');
        $('#typeID').val(null).trigger('change');
        $('#statusidcamp').val(null).trigger('change');
        $('#assigneescamp').val(null).trigger('change');


        loadreportfilterview();
    }

    function clearDashboardSearchFiltercampSales(){
        
        $('#filter_userID').val(null).trigger('change');
        $('#segmentID').val(null).trigger('change');
        $('#arr_crm_productsID').val(null).trigger('change');

        loadreportfilterview();
    }

    function clearDashboardSearchFilterlead() {
        $('#search_dashboard_cancellead').addClass('hide');
        $('#leadsrptuser').val(null).trigger('change');
        $('#leadsstatus').val(null).trigger('change');


        loadreportfilterview();
    }
    function clearDashboardSearchFilteropprpt() {
        $('#search_dashboard_cancellead').addClass('hide');
        $('#opporstatusid').val(null).trigger('change');
        $('#responsiblePersonEmpIDopprpt').val(null).trigger('change');
        $('#sortbyval').val(1).trigger('change');


        loadreportfilterview();
    }
    function clearDashboardSearchFilterpro() {
        $('#search_dashboard_cancelpro').addClass('hide');
        $('#prostatusid').val(null).trigger('change');
        $('#responsiblePersonEmpIDpro').val(null).trigger('change');
        $('#catergorypro').val(null).trigger('change');
        loadreportfilterview();
    }

    function load_group_members() {
        $('#search_dashboard_cancel').removeClass('hide');
        var masterID = $('#groupID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("CrmLead/load_dashboard_groupEmployees"); ?>',
            dataType: 'html',
            data: {masterID: masterID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_groupemployee').html(data);
                $('#groupEmployeeID').multiselect2({
                    enableCaseInsensitiveFiltering: true,
                    includeSelectAllOption: true,
                    numberDisplayed: 1,
                    buttonWidth: '180px',
                    maxHeight: '30px'
                });
                $("#groupEmployeeID").multiselect2('selectAll', false);
                $("#groupEmployeeID").multiselect2('updateButtonText');
                loadreportfilterview();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function loadreportfilterview() {
        id = $("#list").find('.active').val();
        if (id == 1) {
            configuration_page('contact', 'html');
        } else if (id == 2) {
            configuration_page('organization', 'html');
        } else if (id == 3) {
            $('#search_dashboard_cancel').removeClass('hide');
            configuration_page('task', 'html');
        } else if (id == 4) {
            $('#search_dashboard_cancelcamp').removeClass('hide');
            configuration_page('campaign', 'html');
        } else if (id == 5) {
            $('#search_dashboard_cancellead').removeClass('hide');
            configuration_page('leadnew', 'html');
        } else if (id == 6) {
            $('#search_dashboard_cancelopprpt').removeClass('hide');
            configuration_page('opportunity', 'html');
        } else if (id == 7) {
            $('#search_dashboard_cancelpro').removeClass('hide');
            configuration_page('project', 'html');
        } else if (id == 9) {
            $('#search_dashboard_cancel2').removeClass('hide');
            configuration_page('sales_target', 'html');
        }
    }
    /*call report content pdf*/
    function generateReportPdf(reportType) {
        var categoryID = $('#categoryID').val();
        var groupID = [];
        var groupEmployeeID = [];
        $('#reportpdftype').val('');
        $('#reportpdftype').val(reportType);
        var form = document.getElementById('frm_report_filter');
        document.getElementById('assigneeid');
        document.getElementById('statusid');
        document.getElementById('dateyear');
        document.getElementById('dateto');
        document.getElementById('datefrom');
        document.getElementById('categoryID');
        document.getElementById('assigneescamp');
        document.getElementById('categorytaskassignee');
        document.getElementById('typeID');
        document.getElementById('statusidcamp');
        document.getElementById('leadsrptuser');
        document.getElementById('leadsstatus');
        document.getElementById('opporstatusid');
        document.getElementById('responsiblePersonEmpIDopprpt');
        document.getElementById('prostatusid');
        document.getElementById('responsiblePersonEmpIDpro');
        document.getElementById('catergorypro');
        form.target = '_blank';
        form.action = '<?php echo site_url('CrmLead/reports_management'); ?>';
        form.submit();
    }

    function generateReportPdfmoni(reportType) {
        $('#reportpdftypepdf').val('');
        $('#reportpdftypepdf').val(reportType);
        var dateyearpdf=$('#dateyear').val();
        $('#dateyearpdf').val(dateyearpdf);
        var dateto=$('#dateto').val();
        $('#datetopdf').val(dateto);
        var datefrom=$('#datefrom').val();
        $('#datefrompdf').val(datefrom);
        var categoryID=$('#categoryID').val();
        $('#categoryIDpdf').val(categoryID);

        var form = document.getElementById('frm_report_filterPprojectmoni');

        form.post = '1';
        form.target = '_blank';
        form.action = '<?php echo site_url('CrmLead/reports_management'); ?>';
        form.submit();
    }

    function open_project_dd_model(projectIds,category){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'projectIds': projectIds ,'category': category},
            url: "<?php echo site_url('CrmLead/load_projectManagement_view_idwise'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_dd_model').modal('show');
                $('#ProjectddMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_project_edit(path,projectID,heading,project){
        $('#project_dd_model').modal('hide');
        setTimeout(function(){  fetchPage(path,projectID,heading,'CRMDD') }, 50);

    }

    function open_task_dd_model(taskIds){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'taskIds': taskIds},
            url: "<?php echo site_url('CrmLead/load_taskManagement_view_idwise'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#task_dd_model').modal('show');
                $('#taskddMaster_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_task_edit(path,projectID,heading,project){
        $('#task_dd_model').modal('hide');
        setTimeout(function(){  fetchPage(path,projectID,heading,'CRMTSK') }, 40);

    }
</script>