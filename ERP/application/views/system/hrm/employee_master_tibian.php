<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$boxTitle = $this->lang->line('emp_employee_employee_master');
echo head_page_employee();

$isPendingDataAvailable = 0;
$isAuthenticated = emp_master_authenticate();
if( $isAuthenticated == 0){
    $isPendingDataAvailable = isPendingDataAvailable();
}

$empCode_isAutoGenerate = getPolicyValues('ECG', 'All');



$statusCount = fetch_employeeStatusWise();
$segment_arr = fetch_employeeWiseSegment();
$segment_length = count($segment_arr);
$designation_arr = fetch_employeeWiseDesignation();
$designation_length = count($designation_arr);
$filterPost = $this->input->post('filterPost');
$alphas = range('A', 'Z');

/**** Pagination variables ***/
$isInitialLoad = 1;
$employee_list = '';
$pagination = '';
$per_page = 10;
$filterDisplay = '';
$empCount = 0;


$isFiltered = 0;
if(!empty($filterPost)){
    $s_alphaSearch = $filterPost['alphaSearch'];
    $s_searchKeyword = $filterPost['searchKeyword'];
    $s_designation = $filterPost['designation'];
    $s_segment = $filterPost['segment'];
    $s_status = $filterPost['empStatus'];
    $s_pagination = $filterPost['pagination'];

    if( !empty($s_alphaSearch) || !empty($s_searchKeyword) || !empty($s_designation) || !empty($s_segment) || !empty($s_pagination) || $s_status != '' && $s_status != 'null' ){
        $isFiltered = 1;
        $isInitialLoad = 0;
    }
}



if($isFiltered == 0){
    $data_arr = employeePagination();
    $employee_list = $data_arr['employee_list'];
    $pagination = $data_arr['pagination'];
    $per_page = $data_arr['per_page'];
    $filterDisplay = $data_arr['filterDisplay'];
    $empCount = $data_arr['empCount'];
}



/*** New Employee variables ***/
$emp_title = fetch_emp_title();
$tibian_employeeType = tibian_employeeType();
?>
<style>
    #menu ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;

    }

    #menu li {
        float: left;
    }

    #menu li div {
        display: block;
        color: black;
        text-align: center;

        text-decoration: none;
        border: 1px solid #efefef;
    }

    #menu li a:hover {
        cursor: pointer;
    }

    #designation-area{
        max-height: 300px;
        overflow-y: scroll;
    }

    #segment-area{
        max-height: 150px;
        overflow-y: scroll;
    }

    .scroll_emp{
        height: 722px;
        overflow-y: auto;
        overflow-x: hidden;
        direction:ltr;
    }

    .scroll_style::-webkit-scrollbar {
        width: 5px;
    }

    .scroll_style::-webkit-scrollbar-track {
        background: #ddd;
    }

    .scroll_style::-webkit-scrollbar-thumb {
        background: #666;
    }

    #first-in-emp-list{
        width: 2px;
        height: 0px;
        border: 0px;
    }

    .emp-status-label{
        padding: 4px 14px;
    }

    .emp-status-label:hover {
        cursor: default;
    }

    .status-list{
        font-weight: bold;
    }

    fieldset {
        border: 1px solid silver;
        border-radius: 5px;
        padding: 1%;
        padding-bottom: 15px;
        margin: auto;
        margin-bottom: 10px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/HR-plugins/styles.css'); ?>" class="employee_master_styles">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/HR-plugins/alpha-tab.css'); ?>" class="employee_master_styles">


<div class="row" id="master-div">
    <div class="col-sm-12">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 page-sidebar visible-lg"><span style="font-size:24px"><?php echo $boxTitle; ?></span></div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 remove-margin employee-create-header">
            <div class="hidden-lg">
                <span style="font-size:24px"><?php echo $boxTitle; ?></span>
            </div>
            <div class="box-tools hidden-lg pull-right close-sm" style="left: 35px; top:-40px; position: relative;">
                <button class="btn btn-box-tool headerclose" style="color: #fff; margin-left: 200%;"><i class="fa fa-times"></i></button>
                <button class="btn btn-box-tool headerclose" style="color: #fff; margin-left: 200%;"><i class="fa fa-bell" aria-hidden="true"></i></button>
            </div>
            <div class="box-tools visible-lg pull-right emp-master-close-lg">
                <?php
                if(count($isPendingDataAvailable)){
                    echo '<button  id="" class="btn btn-box-tool" style="color: #fff; padding: 8px 0px 0px 0px"><i class="fa fa-bell" aria-hidden="true" onclick="openPendingDataModal()"></i></button>';
                }
                ?>

                <button class="btn btn-box-tool headerclose" style="color: #fff; padding: 8px 8px 0px 0px"><i class="fa fa-times"></i></button>
            </div>
        </div>
    </div>
</div>


<div class="col-sm-12" style="margin:20px auto">
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
            <i class="fa fa-users" aria-hidden="true"></i> <?php echo $this->lang->line('common_directory');?><!--Directory--> <span style="color:#999;" id="empTotalCount">(<?php echo $empCount; ?>)</span>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 pull-right" style="left: 2px;">
            <button type="button" class="btn btn-default pull-right" onclick="add_newEmployee()">
                <i class="fa fa-plus" style="font-size: 11px"></i>  <?php echo $this->lang->line('common_add_employee');?><!--Add Employee-->
            </button>
        </div>
    </div>
</div>


<div class="row" style="margin-left: -10px; margin-right: -2px">
    <div class="container1">
        <div class="row">
            <div class="col-sm-12" id="filter-display">
                <div class="col-sm-8" id="filter-text"></div>
                <div class="col-sm-2 pull-right">
                    <div class="btn-group pull-right">
                        <!--<button type="button" class="btn btn-default"><i class="fa fa-align-justify" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-default"><i class="fa fa-user" aria-hidden="true"></i></button>-->
                    </div>
                </div>
            </div>
            <div class="clearfix visible-xs visible-sm">&nbsp;</div>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 page-sidebar">
                <aside>
                    <div class="white-container mb0">
                        <div class="widget sidebar-widget jobs-search-widget" style="margin-top: -30px;">
                            <div class="widget-content">
                                <input type="text" id="searchKey" class="form-control mt10" onkeyup="employee_search(this)"
                                       placeholder="<?php echo $this->lang->line('common_search');?>" ><!--Search-->
                            </div>
                        </div>

                        <div class="widget sidebar-widget jobs-filter-widget">
                            <div class="widget-content">

                                <h6 style="font-size: 16px;"><?php echo $this->lang->line('emp_employee_status'); ?><!--Employee Status--></h6>
                                <div>
                                    <ul class="filter-list" id="status-area">
                                        <li class="status-list" onclick="selectFilter('status-list', this)" data-val="0">
                                            <a href="#"><?php echo $this->lang->line('emp_active').'<span> ('.$statusCount['activeEmp'].') </span>'; ?><!--Active--></a>
                                        </li>
                                        <li class="status-list" onclick="selectFilter('status-list', this)" data-val="2">
                                            <a href="#"><?php echo $this->lang->line('emp_not_confirmed').'<span> ('.$statusCount['notConfirmed'].') </span>'; ?>
                                                <!--Not Confirmed--></a>
                                        </li>
                                        <li class="status-list" onclick="selectFilter('status-list', this)" data-val="1">
                                            <a href="#"><?php echo $this->lang->line('emp_discharged').'<span> ('.$statusCount['discharged'].') </span>'; ?>
                                                <!--Discharged--></a>
                                        </li>
                                    </ul>

                                    <a href="#" class="toggle"></a>
                                </div>

                                <h6 style="font-size: 16px; margin-top: 50px;"><?php echo $this->lang->line('common_segment');?><!--Segment--></h6>
                                <div>
                                    <ul class="filter-list scroll_style" id="segment-area">
                                        <?php
                                        if(!empty($segment_arr)){
                                            foreach($segment_arr as $seg){
                                                $description = toolTip_filter($seg['description'], 15);
                                                $segmentID = $seg['segmentID'];
                                                $fn = 'class="segment-list" onclick="selectFilter(\'segment-list\', this)" data-val="'.$segmentID.'"';
                                                $fn .= 'data-text="'.trim($seg['description'] ?? '').'"';
                                                echo '<li '.$fn.'><a href="#">'.$description.' <span>('.$seg['empCount'].')</span></a></li>';
                                            }
                                        }
                                        ?>
                                    </ul>

                                    <a href="#" class="toggle"></a>
                                </div>

                                <h6 style="font-size: 16px; margin-top: 50px;"><?php echo $this->lang->line('common_designation');?><!--Designation--></h6>
                                <div>
                                    <ul class="filter-list scroll_style" id="designation-area" >
                                        <?php
                                        if(!empty($designation_arr)){
                                            foreach($designation_arr as $designation){
                                                $description = toolTip_filter($designation['DesDescription'], 15, 18);
                                                $designationID = $designation['DesignationID'];
                                                $fn = 'class="designation-list" onclick="selectFilter(\'designation-list\', this)" data-val="'.$designationID.'"';
                                                $fn .= 'data-text="'.trim($designation['DesDescription'] ?? '').'"';
                                                echo '<li '.$fn.'><a href="#">'.$description.' <span>('.$designation['empCount'].')</span></a></li>';
                                            }
                                        }
                                        ?>
                                    </ul>

                                    <a href="#" class="toggle"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 page-content">
                <div id="alphas" class="">
                    <div class="row">
                        <div class="col-md-12 alpha-tab-container">
                            <div class="clearfix visible-xs visible-sm col-xs-1">&nbsp;</div>
                            <div class="col-lg-11 col-xs-9 alpha-tab">
                                <div class="alpha-tab-content active scroll_emp scroll_style" id="">
                                    <div class="row " id="employee-list" style="padding-right: 1%; padding-left: 3%;">
                                        <?php echo $employee_list; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-xs-3 alpha-tab-menu tabs-right">
                                <div class="list-group">
                                    <?php
                                    foreach ($alphas as $key => $val) {
                                        //$active = ($key == 0)? 'active' : '';
                                        $active = '';
                                        $onClick = 'onclick="employeeMasterFilter(\''.$val.'\',\'yes\', this)"';
                                        $dataVal = 'data-value="'.$val.'"';
                                        ?>
                                        <a href="#" class="list-group-item alpha-list text-center <?php echo $active;?>" <?php echo  $dataVal; ?> <?php echo  $onClick; ?>>
                                            <span class="glyphicon"><?php echo $val; ?></span>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12" style="padding-right: 5px;">
                    <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
                        <p id="filterDisplay"><?php echo $filterDisplay; ?></p>

                        <nav>
                            <ul class="list-inline" id="pagination-ul">
                                <?php echo $pagination; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add_newEmployee" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php echo form_open('', 'role="form" id="employee_form" autocomplete="off"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"> <?php echo $this->lang->line('emp_add_new_employee');?><!--Add New Employee--> </h3>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="tibian_type"><?php echo $this->lang->line('common_type'); ?><?php required_mark(); ?></label>
                                <?php echo form_dropdown('tibian_type', $tibian_employeeType, '', 'class="form-control" id="tibian_type"'); ?>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="EmpSecondaryCode">
                                    <?php echo $this->lang->line('emp_secondary_code'); ?>
                                    <?php ($empCode_isAutoGenerate != 1)? required_mark(): ''; ?>
                                </label>
                                <input type="text" class="form-control" id="EmpSecondaryCode" name="EmpSecondaryCode"
                                       value="" <?=($empCode_isAutoGenerate == 1)? 'readonly': ''?>>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="emp_title"><?php echo $this->lang->line('emp_title'); ?><?php required_mark(); ?></label>
                                <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" id="add-title"
                                            style="height: 29px; padding: 2px 10px;">
                                        <i class="fa fa-plus" style="font-size: 11px"></i>
                                    </button>
                                </span>
                                    <?php echo form_dropdown('emp_title', $emp_title, '', 'class="form-control" id="emp_title"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <fieldset>
                                    <legend> <?php echo $this->lang->line('emp_name_primary')?> <!--Name Primary--> </legend>
                                    <?php echo form_open('', 'role="form" id="employmentData_form" autocomplete="off"'); ?>

                                    <div class="form-group col-sm-12">
                                        <label for="firstName">
                                            <?php echo $this->lang->line('common_emp_first_name'); ?><?php required_mark(); ?></label>
                                        <input type="text" class="form-control" id="firstName" name="firstName">
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="familyName">
                                            <?php echo $this->lang->line('common_emp_family_name'); ?><?php required_mark(); ?></label>
                                        <input type="text" class="form-control" id="familyName" name="familyName">
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-sm-6">
                                <fieldset>
                                    <legend><?php echo $this->lang->line('emp_name_in_arabic'); ?><!--Name in Arabic--></legend>
                                    <?php echo form_open('', 'role="form" id="employmentData_form" autocomplete="off"'); ?>

                                    <div class="form-group col-sm-12">
                                        <label for="firstName_other">
                                            <?php echo $this->lang->line('common_emp_first_name'); ?></label>
                                        <input type="text" class="form-control" id="firstName_other" name="firstName_other">
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="familyName_other">
                                            <?php echo $this->lang->line('common_emp_family_name'); ?></label>
                                        <input type="text" class="form-control" id="familyName_other" name="familyName_other">
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="emp_email"><?php echo $this->lang->line('emp_primary_e-mail'); ?><?php required_mark(); ?></label>

                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                    <input type="email" class="form-control " id="emp_email" name="emp_email">
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="emp_gender"><?php echo $this->lang->line('emp_gender'); ?></label>

                                <div class="form-control">
                                    <label class="radio-inline">
                                        <input type="radio" name="emp_gender" value="1" id="male" class="gender"
                                               checked="checked"><?php echo $this->lang->line('common_male'); ?><!--Male-->
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="emp_gender" value="2" id="feMale" class="gender"><?php echo $this->lang->line('common_female'); ?><!--Female-->
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="emp-history-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 65%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"> <?php echo $this->lang->line('emp_employee_history'); ?><!--Employee History--> </h3>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12" id="historyData">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="title-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('emp_new_employee_title'); ?><!--New Employee Title--></h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_title'); ?><!--Title--></label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="add-emp-title" name="add-emp-title">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="title-btn"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approval-pending-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">Pending Employee Data</h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <table class="<?php echo table_class() ?>">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('emp_employee_name'); ?></th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        if(!empty($isPendingDataAvailable)){
                            $l = 1;
                            foreach ($isPendingDataAvailable as $emp){
                                echo '<tr>
                                       <td style="width: 25px; text-align: right">'.$l.'</td> 
                                       <td>'.$emp['empShtrCode'].' - '.$emp['Ename2'].'</td> 
                                       <td style="width: 60px"> <button onclick="edit_empDet_with_pending_approval('.$emp['EIdNo'].')">Load</button></td> 
                                   </tr>';
                                $l++;
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url('plugins/HR-plugins/scripts.js'); ?>" class="employee_master_styles"></script>
<!--<script type="text/javascript" src="<?php /*echo base_url('plugins/HR-plugins/jquery-ui.js'); */?>" class="employee_master_styles" id="jquery-ui-file"></script>-->

<script type="text/javascript">
    var isSearchedWithTextBox = null;
    var isInitialLoad = '<?php  echo $isInitialLoad; ?>';
    var segmentLength = '<?php  echo $segment_length; ?>';
    var designation_length = '<?php  echo $designation_length; ?>';
    var per_page = '<?php  echo $per_page; ?>';
    var lastKeyWordSearch = '';
    var data_paginationFromInitial = 0;
    var searchRequest = null;
    var error_occurred_str = '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.';/*An Error Occurred! Please Try Again*/

    if(segmentLength > 10){
        /*$('#segment-area').slimScroll({
         height: '200px',
         size : '3px',
         alwaysVisible: true,
         wheelStep:  '4',
         barClass : 'filter-slim-scroll-bar',
         wrapperClass : 'filter-slim-scroll-wrapper'
         });*/
    }

    if(designation_length > 10){
        /*$('#designation-area').slimScroll({
         size : '3px',
         alwaysVisible: true,
         wheelStep:  '4',
         barClass : 'filter-slim-scroll-bar',
         wrapperClass : 'filter-slim-scroll-wrapper'
         });*/
    }

    $('.filter-slim-scroll-wrapper').css('height', 'auto');

    employee_list_scroll();

    /** Trigger first alphabet **/
    /*$('.list-group a:first').trigger("click");*/

    $("div.alpha-tab-menu>div.list-group>a").click(function (e) {
        e.preventDefault();

        if($(this).hasClass('alpha-active')){
            $(this).removeClass("active alpha-active");
            $('.remove-filter-alpha').remove();
        }
        else{

            $(this).siblings('a.active').removeClass("active alpha-active");
            $(this).addClass("active alpha-active");
            $('.remove-filter-alpha').remove();

            var str = '<span class="remove-filter-alpha"><i class="fa fa-times" style=" color: black; background: none;"></i></span>';
            $(this).append(str);
        }

    });


    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/employee_master_tibian', 'Test', 'HRMS');
        });

        $("#employee_form").bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                tibian_type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_type_is_required');?>.'}}},
                emp_title: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_title_is_required');?>.'}}},/*Title is required*/
                <?php if($empCode_isAutoGenerate != 1){ ?>
                EmpSecondaryCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_secondary_code_is_required');?>.'}}},
                <?php } ?>
                firstName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_first_name_is_required');?>.'}}},
                familyName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_family_name_is_required');?>.'}}},
                emp_email: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_e_mail_required');?>.'}}},/*E-Mail required*/
                emp_gender: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_gender_is_required');?>.'}}}/*Gender is required*/
            },
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var requestUrl = '<?php echo site_url('Employee/new_empSave/tibian'); ?>';

            var formData = $("#employee_form").serializeArray();

            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: formData,
                url: requestUrl,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#add_newEmployee').modal('hide');
                        setTimeout(function(){
                            edit_empDet(data[2]);
                        },500);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        });
    });

    function selectFilter(filterType, obj){
        isSearchedWithTextBox = null;
        $('.'+filterType).removeClass('active-list');
        $('.'+filterType+'-remove-filter').remove();
        var removeSpan = '<span class="remove-filter '+filterType+'-remove-filter" onclick="removeFilterItem(this, \''+filterType+'\')"><i class="fa fa-times"></i></span>';
        $(obj).addClass('active-list');
        $('.'+filterType+'.active-list').after(removeSpan);

        window.localStorage.setItem('emp-master-'+filterType, $(obj).attr('data-val'));

        filterText();

    }

    function removeFilterItem(obj, filterType){
        $(obj).parent().find('li').removeClass('active-list');
        $(obj).remove();
        window.localStorage.setItem('emp-master-'+filterType, '');

        filterText();
    }

    function filterText(){
        per_page = 0;

        var alpha = $('.list-group-item.active').attr('data-value');
        employeeMasterFilter(alpha);

        var segment = $('.segment-list.active-list').attr('data-text');
        var designation = $('.designation-list.active-list').attr('data-text');
        var empStatus = $('.status-list.active-list').attr('data-text');
        var str = '';

        if(segment != undefined){
            str += '<li ><?php echo $this->lang->line('common_segment');?> > '+segment +' &nbsp;&nbsp;&nbsp;&nbsp;</li>';<!--Segment-->
        }

        var separatorStr = '<li class="divider-vertical-menu"><div class="docs-toolbar-small-separator goog-toolbar-separator goog-inline-block"';
        separatorStr += 'id="slideLayoutSeparator" aria-disabled="true" role="separator" style="user-select: none;">&nbsp;</div></li>';
        if(designation != undefined){
            var separator = (str != '')? separatorStr : '';
            var separator2 = (str != '')? '&nbsp;&nbsp;&nbsp;&nbsp;' : '';
            str += separator+'<li >'+separator2+' Designation > '+designation +'</li>';
        }

        str = '<ul class="filter-item-ul"> '+str;
        str += '</ul>';

        $('#filter-text').html(str);
    }

    function pagination(obj){
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        window.localStorage.setItem('emp-master-pagination', data_pagination);

        var alpha = $('.list-group-item.active').attr('data-value');
        employeeMasterFilter(alpha);
    }

    function employee_search(obj){
        isSearchedWithTextBox = 1;
        var keyword = $.trim($(obj).val());
        if(keyword != lastKeyWordSearch){
            lastKeyWordSearch = keyword;
            per_page = 0;
            var alpha = $('.list-group-item.active').attr('data-value');
            employeeMasterFilter(alpha);
        }

        window.localStorage.setItem('emp-master-searchKeyword', keyword);
        window.localStorage.setItem('emp-master-pagination', 0);
    }

    function employeeMasterFilter(letter, isFromAlphas=null, obj=null){

        if(isInitialLoad == 1){
            if(searchRequest){
                searchRequest.abort();
            }
            if(isFromAlphas == 'yes'){
                isSearchedWithTextBox = null;
                per_page = 0;
                window.localStorage.setItem('emp-master-alpha-search', letter);
                if($(obj).hasClass('alpha-active')){
                    letter = '';
                    window.localStorage.setItem('emp-master-alpha-search', '');
                }
            }

            var searchKey = $.trim($('#searchKey').val());
            var segment = $('.segment-list.active-list').attr('data-val');
            var designation = $('.designation-list.active-list').attr('data-val');
            var empStatus = $('.status-list.active-list').attr('data-val');

            var data_pagination = 0;

            if(isFromAlphas == 'no' ){
                data_pagination = data_paginationFromInitial;
                per_page = 10;
            }
            else{
                data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
            }

            var uriSegment = ( data_pagination == undefined ) ? per_page :  ((parseInt(data_pagination)-1)*per_page);
            var dataPost = [{'name': 'letter', 'value':letter}];

            dataPost.push({'name': 'searchKey', 'value':searchKey});
            dataPost.push({'name': 'segment', 'value':segment});
            dataPost.push({'name': 'designation', 'value':designation});
            dataPost.push({'name': 'empStatus', 'value':empStatus});
            dataPost.push({'name': 'data_pagination', 'value':data_pagination});

            searchRequest = $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: dataPost,
                url: '<?php echo site_url("Employee/employeeMasterFilter"); ?>/'+uriSegment,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#employee-list').html(data['employee_list']);
                    $('#pagination-ul').html(data['pagination']);
                    $('#filterDisplay').html(data['filterDisplay']);
                    $('#empTotalCount').html('('+data['empCount']+')');
                    per_page = data['per_page'];
                    setTimeout(function(){ employee_list_scroll(); },300);
                    //$("html, body").animate({scrollTop: "0px"}, 10);

                }, error: function (xhr, textStatus, errorThrown) {
                    stopLoad();
                    if (xhr.status != 0) {
                        myAlert('e', error_occurred_str);
                    }
                }
            });
        }

    }

    function employee_list_scroll(){
        /*$('#employee-list').slimScroll({
         scrollTo: '0px',
         height : '715px',
         size : '3px',
         alwaysVisible: true,
         barClass : 'filter-slim-scroll-bar-large',
         wrapperClass : 'filter-slim-scroll-wrapper-employee-list',
         railVisible: true,
         railColor: '#222',
         position: 'left',
         wheelStep:  '5'
         });*/
        $('#first-in-emp-list').show().focus();
        $('#first-in-emp-list').hide();

        if(isSearchedWithTextBox == 1){
            $('#searchKey').focus();
        }

    }

    function add_newEmployee(){
        $('#add_newEmployee').modal('show');

        $('#employee_form')[0].reset();
        $('#employee_form').bootstrapValidator("resetForm",true);
    }

    function edit_empDet(empID) {
        fetchPage('system/hrm/employee_create_tibian', empID, 'HRMS', '', '');
    }

    function edit_empDet_with_pending_approval(empID){
        $('#approval-pending-modal').modal('hide');
        setTimeout(function(){
            edit_empDet(empID);
        }, 300);
    }

    function excelDownload() {
        var form = document.getElementById('filterForm');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#filterForm').serializeArray();
        form.action = '<?php echo site_url('Employee/export_excel'); ?>';
        form.submit();
    }

    function clear_all_filters() {
        $('#isDischarged').val("");
        $('#segment').multiselect2('deselectAll', false);
        $('#segment').multiselect2('updateButtonText');
        $('#employeeCode').multiselect2('deselectAll', false);
        $('#employeeCode').multiselect2('updateButtonText');
        window.localStorage.removeItem("isDischarged");
        window.localStorage.removeItem("employeeCode");
        window.localStorage.removeItem("segment");
        fetchEmployees();
    }

    $('#add-title').click(function () {
        $('#add-emp-title').val('');
        $('#title-modal').modal({backdrop: 'static'});
    });

    $('#title-btn').click(function (e) {
        e.preventDefault();
        var title = $.trim($('#add-emp-title').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'title': title},
            url: '<?php echo site_url("Employee/new_empTitle"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                var emp_title = $('#emp_title');
                if (data[0] == 's') {
                    emp_title.append('<option value="' + data[2] + '">' + title + '</option>');
                    emp_title.val(data[2]);
                    $('#title-modal').modal('hide');
                }


            }, error: function () {
                myAlert('e', error_occurred_str);
                stopLoad();
            }
        });
    });

    function loadData(code, id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {code:code, id:id},
            url: '<?php echo site_url("Employee/employeeHistory"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#emp-history-modal').modal('show');
                $('#historyData').html(data);

                if(code == 'LA'){
                    $('#historyData').append( '<div id="leave-balance" style="margin-top: 10px"></div><div id="leave-history" style="margin-top: 10px"></div>' );
                }


            }, error: function () {
                myAlert('e', error_occurred_str);
                stopLoad();
            }
        });
    }

    function leaveBalanceModal(balance, leaveTypeID, empID) {

        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/loadLeaveBalanceHistory'); ?>",
            type: 'post',
            dataType: 'html',
            data: {'leaveTypeID': leaveTypeID, isFromEmployeeMaster: 'Y', empID:empID},
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#leave-balance').html(balance+' <span id="balance-span"> </span>');
                $('#leave-history').html(data);

                stopLoad();
            }, error: function () {
                myAlert('e', 'error');
            }
        });
    }

    function openPendingDataModal(){
        $('#approval-pending-modal').modal('show');
    }

</script>

<?php

if($isFiltered == 1){
    $s_alphaSearch = $filterPost['alphaSearch'];
    $s_searchKeyword = $filterPost['searchKeyword'];
    $s_designation = $filterPost['designation'];
    $s_segment = $filterPost['segment'];
    $s_status = $filterPost['empStatus'];


    if( !empty($s_searchKeyword) ){
        echo "<script> $('#searchKey').val(\"".$s_searchKeyword."\"); </script>";
    }

    if( !empty($s_designation) ){
        echo "<script> $('.designation-list[data-val=\"".$s_designation."\"]').click(); </script>";
    }

    if( !empty($s_segment) ){
        echo "<script> $('.segment-list[data-val=\"".$s_segment."\"]').click(); </script>";
    }

    if( $s_status != '' && $s_status != 'null' ){
        echo "<script> $('.status-list[data-val=\"".$s_status."\"]').click(); </script>";
    }

    if( !empty($s_alphaSearch)){
        echo "<script> $('.alpha-list[data-value=\"".$s_alphaSearch."\"]').click(); </script>";
    }



    if( !empty($s_pagination)){
        echo "<script> data_paginationFromInitial = $s_pagination; </script>";
    }

    echo "<script>
            setTimeout(function(){
                isInitialLoad = 1;
                per_page=0;
                employeeMasterFilter('".$s_alphaSearch."', 'no');
            }, 400);
         </script>";

}

?>

<?php
