<!--Translation added by Naseek-->
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->load->helper('jobs_helper');
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_management');
echo head_page($title, false);

$current_date = $running_date = format_date($this->common_data['current_date']);
$floors_arr = floors_drop(1);
$floor = floors_fetch();

$data_id = $this->input->post('data_arr');

if($data_id){
    $data_id_details = get_data_attendancemaster_id_details($data_id);

    if($data_id_details){
        $running_date = $data_id_details['AttDate'];
    }
}

if(isset($master)){
    $running_date = $master['AttDate'];
}

?>

<style type="text/css">

    #empSearchLabel {
        float: right !important;
        font-weight: 600
    }

    @media (max-width: 767px) {
        #empSearchLabel {
            float: left !important;
        }

        #new-attBtn {
            margin-bottom: 30px;
        }
    }

    .trInputs {
        width: 100%;
        padding: 2px 4px;
        height: 22px;
        font-size: 12px;
        border: 0px solid #ccc;
    }

    .hideTr {
        display: none
    }

    /*     .oddTR td {
             background: #f9f9f9 !important;
         }

         .evenTR td {
             background: #ffffff !important;
         }*/

    .fixHeader_Div {
        height: 500px;
        /*      border: 1px solid #c0c0c0;*/
    }

    #attendanceReview td {
        vertical-align: middle;
    }

    #attendanceReview th {
        z-index: 10;
    }

    #attendanceReview tr:hover > td {
        background: rgba(14, 191, 70, 0.31) !important;

    }

    #attendanceReview tr:hover > td .trInputs {
        color: #000;
    }

    #attendanceReview tr:hover > td.fixed-td {
        background: rgba(14, 191, 70, 0.31) !important;
    }

    .timeBox {
        text-align: right;
        padding: 2px;
    }

    .attType {
        height: 22px;
        padding: 2px;
        font-size: 12px;
    }

    .fixed-td {
        z-index: 10;
    }

    /*.oddTR>.fixed-td{ background: #3cd6e6 !important; z-index: 10; color: #f3f3f3 }
    .evenTR>.fixed-td{ background: #97eaf4 !important; z-index: 10 }*/
    #attendanceReview tr:hover {
        background-color: #FFFFAA;
    }

    #attendanceReview tr.selected td {
        background: none repeat scroll 0 0 #FFCF8B;
        color: #000000;
    }

    .highlight {
        background-color: rgba(167, 251, 132, 0.35) !important;
        opacity: 200;
    }

    .tb thead tr {
        background: rgb(178, 203, 230);
    }

    #attendanceReview thead tr {
        background: rgb(178, 203, 230);
    }

    #attendanceReview table {
        border-collapse: separate;
        border-spacing: 0 5px;
        padding: 2px;
        line-height: 2;
        padding-left: 5px;
    }

    #attendanceReview thead th {
        background-color: rgb(197, 215, 253);;

    }

    /*  tbody td {
         // background-color: #EEEEEE;
      }*/

    /*        #attendanceReview tr td:first-child,
            #attendanceReview tr th:first-child {
                border-top-left-radius: 6px;
                border-bottom-left-radius: 6px;
            }

            #attendanceReview tr td:last-child,
            #attendanceReview tr th:last-child {
                border-top-right-radius: 6px;
                border-bottom-right-radius: 6px;
            }*/

    . attendanceReview .table > tbody > tr > td {
        padding: 4px;
    }

    .inputdisabled {
        background-color: white;
    }

    #attendanceReview .input-group-addon {

        border: 0px solid #ccc;

    }

    #attendanceReview tbody tr > td:nth-child(8),
    #attendanceReview tbody tr > td:nth-child(9) {
        background-color: #F7F8FA;
    }

    .occurrence-popover{
        padding: 4px 5px;
        font-weight: bold;
        color: #3c8dbc;
    }

    .occurrence-popover:hover{
        background: #ffff0082;
        cursor: pointer;
    }

    .att-popover-header{
        padding: .5rem .75rem;
        margin-bottom: 0;
        font-size: 1rem;
        color: inherit;
        background-color: #f7f7f7;
        border-bottom: 1px solid #ebebeb;
        border-top-left-radius: calc(.3rem - 1px);
        border-top-right-radius: calc(.3rem - 1px);
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
    <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
        <li class="active"><a href="#showdataTap" id="showdata" data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('hrms_showdata'); ?><!--Show Data--></a>
        </li>
    </ul>
    <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

        

        <div class="tab-pane active" id="showdataTap"> <!-- Start of showdataTap -->
            <div class="row">
                <div class="col-md-12" id="divtoggle1">
                    <span class="pull-right"> <a> <i class="fa fa-filter"></i>
                            <?php echo $this->lang->line('common_filters'); ?><!--Filter--> </a></span>
                </div>
            </div>
            <div class="row hide div-view1">
                <div class="col-md-6"> <!--Fetching attendance details-->
                    <div id="" class="box box-default " style="background-color: #f5f5f5;border: 1px solid #e3e3e3;">
                        <div id="" class="box-header with-border " style="padding: 5px 10px">
                            <h3 class="box-title" style="font-size: 14px">
                                <?php echo $this->lang->line('hrms_attendance_fetch_records'); ?><!--Fetch Records--></h3>
                        </div>
                        <?php echo form_open('', 'role="form" class="" id="attFetching_form" autocomplate="off"');?>

                        <input type="hidden" name="attendance_master" id="attendance_master" value="<?php echo $attendance_master ?? '' ?>" />
                        
                        <div class="box-body" style="background: #ffffff; padding: 10px 0px">
                            <div class="col-sm-12" style="padding:0px">
                                <div class="col-sm-4" style="padding-right: 10px">
                                    <div class="form-group">
                                        <label for="fromDate"><?php echo $this->lang->line('common_from'); ?>&nbsp;
                                            <?php echo $this->lang->line('common_date'); ?><!--From Date--> <?php required_mark(); ?></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="fromDate" value="<?php echo $running_date ?>"
                                                   id="fromDate" autocomplate="off"
                                                   class="form-control dateField /">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4" style="padding-right: 10px">
                                    <div class="form-group">
                                        <label for="toDate"><?php echo $this->lang->line('common_to'); ?>&nbsp;
                                            <?php echo $this->lang->line('common_date'); ?><!--To Date--> <?php required_mark(); ?></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="toDate" value="<?php echo $running_date ?>"
                                                   id="toDate" autocomplate="off"
                                                   class="form-control dateField /">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer div-view2 clearfix">
                            <button type="submit" style="margin-left:2px;" class="pull-right btn btn-primary btn-sm" id="loadBtn">
                                <i class="fa fa-arrow-circle-down"></i>
                                <?php echo $this->lang->line('hrms_attendance_load'); ?><!--Load-->
                            </button>
                            <button class="pull-right btn btn-success btn-sm" id="reUpdateBtn" onclick="reUpload_attendance_data()">
                                <i class="fa fa-arrow-circle-up"></i>
                                <?php echo $this->lang->line('hrms_attendance_reupload'); ?><!--Reupload-->
                            </button>
                            <!--<button type="submit" onclick="opentemplatefields()" style="margin-right:2px;" class="pull-right btn btn-success btn-sm">
                                <i class="fa fa-cog"></i>
                                Configure
                            </button>-->
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>


            <div id="load-attendanceReview"><!-- Pulled Data -->
                <?php
                /*$data['attData'] = array(
                    '0' => 's',
                    'tempAttData' => null,
                    'unAssignedShifts' => null,
                    'unAssignedMachineID' => null
                );
                $this->load->view('system/hrm/ajax/load_empAttemdanceReview', $data);*/
                ?>
            </div>

            <div class="row" style="margin-bottom: 1%">
                <div class="col-sm-4 col-xs-5" style="">
                    <table class="<?php echo table_class(); ?>" style="margin-top: 3px">
                        <tr>
                            <td><span class="label"
                                      style="padding: 0px 5px ;font-size: 100%;background-color: #dacff7">&nbsp;</span>&nbsp;&nbsp;
                                <?php echo $this->lang->line('hrms_attendance_shift_weekend'); ?><!-- Shift Weekend-->
                            </td>
                            <td><span class="label"
                                      style="padding: 0px 5px ;font-size: 100%;background-color:rgb(249, 177, 168);">&nbsp;</span>&nbsp;&nbsp;
                                Holiday
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-3 hidden-xs">&nbsp;</div>
               
                <div class="col-sm-3 col-xs-4">
                    <input type="text" class="form-control" id="attReview-searchItem" value=""
                           placeholder="<?php echo $this->lang->line('common_search_name'); ?> | <?php echo $this->lang->line('common_code'); ?>">
                    <!--Search Name--><!--Code-->
                </div>

                <div class="col-sm-2 col-xs-3 pull-right">
                    <!-- <button class="btn btn-success"><i class="fa fa-plus"></i> Add Employee </button> -->
                </div>
            </div>
            <h5 class="emptitle"></h5>
            <div class="table-responsive" style="padding: 0px !important;">
                <?php echo form_open('', 'role="form" class="" id="attendanceReview_form" autocomplete="off"'); ?>
                <div class="fixHeader_Div" style="max-width: 100%;">
                    <table id="attendanceReview" class="table tb "
                           style="max-width: 1750px !important;">
                        <thead class="">
                        <tr style="white-space: nowrap">
                            <th style="width: 15px;">#</th>
                            <th style="width: 100px;">
                                <?php echo $this->lang->line('hrms_attendance_employee_code'); ?><!--EMP Code--></th>
                            <th style="min-width: 120px;">
                                <?php echo $this->lang->line('hrms_attendance_employee_name'); ?><!--Emp Name--></th>
                            <th style="width: 120px;    ">
                                <?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                            <th style="width: 120px;    ">
                                <?php echo 'Shift'; ?><!--Date--></th>
                            <th style="width: 120px;    ">
                                <?php echo 'Job'; ?><!--Date--></th>
                            <!-- <th style="width: 85px">Job</th> -->
                            <th style="width: 185px">Location in</th>
                            <th style="width: 185px">Location Out</th>
                            <th style="width: 120px">
                                <?php echo $this->lang->line('hrms_attendance_on_duty_time'); ?><!--On Duty Time--></th>
                            <th style="width: 120px">Grace Period</th>
                            <th style="width: 120px">
                                <?php echo $this->lang->line('hrms_attendance_off_duty_time'); ?><!--Off Duty Time--></th>
                            <th style="z-index: 10; width: 115px">
                                <?php echo $this->lang->line('hrms_attendance_clock_in_date'); ?><!--Clock In--></th>
                            <th style="z-index: 10; width: 115px">
                                <?php echo $this->lang->line('hrms_attendance_clock_in'); ?><!--Clock In--></th>
                            <th style="z-index: 10; width: 115px">
                                <?php echo $this->lang->line('hrms_attendance_clock_out_date'); ?><!--Clock In--></th>
                            <th style="z-index: 10; width: 115px">
                                <?php echo $this->lang->line('hrms_attendance_clock_out'); ?><!--Clock Out--></th>
                            <!--<th style="z-index: 10; width: 115px">
                                <?php echo $this->lang->line('hrms_attendance_normal_time'); ?></th>--> <!--Normal Time-->
                            <th class="hide" style="z-index: 10; width: 115px">
                                <?php echo $this->lang->line('hrms_attendance_real_time'); ?><!--Real Time--></th>

                            <th style="width: 105px">
                                <?php echo $this->lang->line('hrms_attendance_present'); ?><!--Present-->
                                <!--Absent--></th>
                            <th style="width: 80px">
                                <?php echo $this->lang->line('hrms_attendance_late'); ?><!--Late--></th>
                            <th style="width: 80px">
                                <?php echo $this->lang->line('hrms_attendance_early'); ?><!--Early--></th>
                            <th style="width: 100px">
                                <?php echo $this->lang->line('hrms_attendance_over_time'); ?><!--OT Time--></th>
                            <th class="hide" style="width: 100px">Shift Hours</th>
                                <th style="width: 100px">
                                <?php echo $this->lang->line('hrms_attendance_work_time'); ?><!--Work Time--></th>
                            <th style="width: 100px;">
                                <?php echo $this->lang->line('hrms_attendance_att_time'); ?><!--ATT_Time--></th>
                            <th style="width: 100px">
                                <?php echo $this->lang->line('hrms_attendance_normal_day'); ?><!--NDay--></th>
                            <th style="width: 100px">
                                <?php echo $this->lang->line('hrms_attendance_weekend'); ?><!--Week End--></th>
                            <th style="width: 100px">
                                <?php echo $this->lang->line('hrms_attendance_holiday'); ?><!--Holiday--></th>
                            <th style="width: 80px;">
                                <?php echo $this->lang->line('hrms_attendance_ndays_ot'); ?><!--NDays OT--></th>
                            <th style="width: 95px;">
                                <?php echo $this->lang->line('hrms_attendance_weekend_ot'); ?><!--Weekend OT--></th>
                            <th style="width: 80px;">
                                <?php echo $this->lang->line('hrms_attendance_holiday_ot'); ?><!--Holiday OT--></th>
                            <th style="width: 25px; z-index: 10">
                                &nbsp;<button type="button" onclick="delete_all()" class="btn btn-danger btn-xs">Delete All</button>
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr>
                            <td colspan="21">
                                <?php echo $this->lang->line('hrms_attendance_No_data_available_in_table'); ?><!--No data available in table--></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <?php echo form_close(); ?>
            </div>
            <h5 class="emptitle"></h5>
            <div class="row">
                <div class="" style="margin-top: 1% !important;">
                    <div class="col-sm-6 col-xs-6">
                        <label>
                            Showing <span id="attReview-showingCount"> 0 </span> of
                            <span id="attReview-totalRowCount"> 0 </span> entries
                        </label>
                    </div>
                    <!--       <div class="col-sm-6 col-xs-6">
                               <button type="button" class="btn btn-primary btn-sm pull-right" onclick="save_attReview()">Save</button>
                           </div>-->
                    <div class="col-sm-6 col-xs-6">
                        <button type="button" class="btn btn-primary btn-sm pull-right" onclick="confirm_att()">
                            <?php echo $this->lang->line('common_confirm'); ?><!--  Confirm-->
                        </button>
                    </div>
                </div>
            </div>


            <!-- /.box-header -->


            <!-- /.box-body -->
            <div class="box-footer">

            </div>
            <!-- /.box-footer-->


            <div class="row" id="unAssignedDiv"> <!-- Un assigned div-->
                <div class="col-sm-6 hide" id="unAssignedShift-div" style="display: none"> <!-- Shift not assigned -->

                    <div class="box box-primary direct-chat direct-chat-primary">
                        <div class="box-header with-border">
                            <h5 style="font-weight: bolder">Un assigned employee for the shift</h5>


                        </div>
                        <div class="box-body">
                            <div class="table-responsive" style="padding: 0px !important;">
                                <table id="unAssignedShift" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="width: 15px">#</th>
                                        <th style="width: 100px">EMP Code</th>
                                        <th>Emp Name</th>
                                        <th style="width: 80px"></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> <!-- Shift not assigned -->
                </div>
                <div class="col-sm-6 hide" id="unAssignedMachine-div" style="display: none">
                    <div class="box box-primary direct-chat direct-chat-primary">
                        <div class="box-header with-border">
                            <h5 style="font-weight: bolder">Employees un assigned to machine</h5>


                        </div>
                        <div class="box-body">
                            <div class="table-responsive" style="padding: 0px !important;">
                                <table id="unAssignedMachine" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="width: 15px">#</th>
                                        <th style="width: 100px">EMP Code</th>
                                        <th>Emp Name</th>
                                        <th style="width: 80px"></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> <!-- Machine not assigned -->
                </div>

            </div>

        </div><!-- End of showdataTap -->
    </div>
</div>
    </div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="newAttendance" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="salary-cat-title">New Attendance</h4>
            </div>
            <?php echo form_open('', 'role="form" class="" id="newAttendance_form1" autocomplete="off"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="control-label" for="description">Attendance
                                Date <?php required_mark(); ?></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="attendanceDate" value="<?php echo $current_date; ?>"
                                       id="attendanceDate" class="form-control dateField">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-10">
                        <div class="form-group">
                            <label class="control-label" for="category">Attendance
                                Time <?php required_mark(); ?></label>
                            <div class="input-group bootstrap-timepicker timepicker">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                                <input id="attendanceTime" name="attendanceTime" type="text"
                                       class="form-control input-small">
                            </div>
                        </div>
                    </div>

                   
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm" id="saveBtn">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="attendanceDetail" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="padding: 10px 5px">
                <div class="row">
                    <div class="col-sm-4">
                        <h4 class="modal-title" id="salary-cat-title">
                            Staff Attendance List &nbsp;&nbsp;&nbsp; <span
                                    class="hidden-lg attendance-date-time"></span>
                        </h4>
                    </div>
                    <div class="col-sm-5 hidden-md hidden-sm hidden-xs">
                        <h4 class="modal-title attendance-date-time"></h4>
                    </div>
                    <div class="clearfix visible-xs">&nbsp;</div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="searchItem" id="searchItem" value=""
                                   style="height: 28px">
                            <span class="input-group-addon" style="">
                                <i class="glyphicon glyphicon-search" style="font-size:12px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_open('', 'role="form" class="" id="attendanceDetail_form" autocomplete="off"'); ?>
                <div class="modal-body" style="padding: 0px;">
                    <div class="box" style="margin-bottom: 0px; border: 0px;">
                        <div class="box-body" id="attDetails_div" style="min-height: 300px; padding: 0px">

                        </div>
                        <div class="overlay" id="overlay" style="background: rgba(0, 0, 0, 0.59)"><i
                                    class="fa fa-refresh fa-spin"></i></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="pull-left">
                                Showing <span id="showingCount"> 0 </span> of
                                <span id="totalRowCount"> 0 </span> entries
                            </label>
                        </div>

                        <div class="col-sm-9" style="">
                            <b> Close Attendance :</b>
                            <input type="checkbox" name="isComplete" id="isComplete"/>
                            &nbsp;&nbsp;&nbsp;
                            <input type="hidden" name="attendMasterID" id="attendMasterID"/>
                            <button type="button" class="btn btn-primary btn-sm modalBtn" id="saveAatDetail_btn"
                                    onclick="saveAttendance()">Save
                            </button>
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="attendanceview" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog" style="width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="salary-cat-title"><?php echo $this->lang->line('hrms_attendance_attendance_list')?><!--Attendance List--></h4>
            </div>
            <?php echo form_open('', 'role="form" class="" id="newAttendance_form" autocomplete="off"'); ?>
            <div class="modal-body" id="">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="divLoadPage">
                        </div>
                        <hr>


                    </div>

                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_close')?><!--Close--></button>

            </div>

            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="templatefieldsmodal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog" style="width: 60%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('hrms_attendance_template_fields')?><!--Template Fields--></h4>
            </div>
            <?php echo form_open('', 'role="form" class="" id="templatefields_form" autocomplete="off"'); ?>
            <div class="modal-body" >
                <div class="row">
                    <div class="col-sm-12">
                    <div id="templatefieldsload">

                    </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_close')?><!--Close--></button>
            </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="excelUpload_Modal" style="z-index:10000000;"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Attendance upload form</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('', 'id="attdanceUpload_form" class="form-inline"'); ?>
                    <input type="hidden" name="floorID" id="floorIDhnupload">
                    <input type="hidden" name="fromDate" id="fromDatehnupload">
                    <input type="hidden" name="toDate" id="toDatehnupload">
                    <input type="hidden" name="date_format" id="date_format_post">
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="form-group">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                 style="margin-top: 8px;">
                                <div class="form-control" data-trigger="fileinput" style="min-width: 200px; width: 100%;
                                    border-bottom-left-radius: 3px !important; border-top-left-radius: 3px !important; ">
                                    <i class="glyphicon glyphicon-file color fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span>
                                    <span class="fileinput-exists"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></span>
                                    <input type="file" name="excelUpload_file" id="excelUpload_file" accept=".csv">
                                </span>
                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id" data-dismiss="fileinput">
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-default" onclick="excel_upload_attandance()">
                            <span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="col-sm-12" style="margin-left: 3%; color: red">
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg1'); ?><br/>
                        <?php echo $this->lang->line('hrms_payroll_excel_upload_msg2'); ?>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close');?>
                </button>
            </div>
            <form role="form" id="downloadTemplate_form">
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_load_job_change" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-xl" style="" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="close_manufaturejob_modal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="">Select Manufature Job</h4>
            </div>

            <div class="modal-body" id="job_change_section">

                
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" onclick="close_manufaturejob_modal()">Close</button>
            </div>
        </div>
    </div>
</div>



<div aria-hidden="true" role="dialog" tabindex="-1" id="not_approved_attdance_model" class="modal fade in"
     style="display: none;">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Following Leave Applications are not approved. Do you want to Continue</h4>
            </div>
            <div class="modal-body" id="not_selected_asset_modal_body">
                <table class="<?php echo table_class(); ?>" cellSpacing='0' cellPadding='0' >
                    <thead>
                    <tr>
                        <th style="width: 10px;">#</th>
                        <th style="width: 80px;">Code</th>
                    </tr>
                    </thead>
                    <tbody id="not_approved_attdance">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button class="btn btn-primary" onclick="pull_attdance()" type="button">Continue</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="multiple-occ-modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-sm" style="" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mul-occ-title"></h4>
            </div>

            <div class="modal-body" >
                <div id="mul-occ-title2" style="font-weight: bold;"></div>
                <div id="multiple-occ-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $('#attendanceReview').tableHeadFixer({
        head: true
    });
    var newAttendance = $('#newAttendance');
    var attendanceDetail = $('#attendanceDetail');
    var newAttendance_form = $('#newAttendance_form');
    window.attMasterTB;
    var attendanceReview = $('#attendanceReview');


    $(document).ready(function () {
        $("#divtoggle1").click(function () {
            /*  $(".box-body").slideToggle("slow");*/
            $(".div-view1").slideToggle("slow");
        });
        $("#divtoggle2").click(function () {
            /*  $(".box-body").slideToggle("slow");*/
            $(".div-view2").slideToggle("slow");
        });

        $("#xfloorID").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Search outlet',
            includeSelectAllOption: true
        });
        $("#xfloorID").multiselect2('selectAll', false);
        $("#xfloorID").multiselect2('updateButtonText');

        $('.select2').select2();
        $('.headerclose').click(function () {
             var pageType = '<?php echo $this->input->post('master_page_url'); ?>';
            if(pageType==1){
                fetchPage('system/hrm/manual_attendance_management', 'Test', 'HRMS');
            }else{
                fetchPage('system/profile/attendance-view','Test','HRMS');
            }
            
        });

        $('#attendanceTime').timepicker({
            minuteStep: 1,
            template: 'dropdown',
            appendWidgetTo: 'body',
            showSeconds: false,
            showMeridian: true
        });


        $('.dateField').datepicker({format: 'yyyy-mm-dd'}).on('changeDate', function (ev) {
            $(this).datepicker('hide');
            if (this.id == 'attendanceDate') {
                newAttendance_form.bootstrapValidator('revalidateField', 'attDate');
            }
            if (this.id == 'fromDate') {
                $('#attFetching_form').bootstrapValidator('revalidateField', 'fromDate');
            }
            if (this.id == 'asOfDate') {
                filterAttendanceTable();
                //  $('#attFetching_form').bootstrapValidator('revalidateField', 'asOfDate');

            }
            else {
                $('#attFetching_form').bootstrapValidator('revalidateField', 'toDate');
            }
        });

        newAttendance_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                attendanceDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_attendance_attendance_date_is_required');?>.'}}}, /*Attendance date is required*/
                attendanceTime: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_attendance_attendance_time_is_required');?>.'}}}/*Attendance time is required*/
            }
        })
            .on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();


                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Employee/new_attendance'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $form.bootstrapValidator('resetForm', true);
                            newAttendance.modal('hide');
                            var attID = data[2];
                            load_attendanceTB(attID);
                            /*setTimeout(function(){
                             open_attendanceDetailModal(attID);
                             }, 400);*/

                        }
                    }, error: function () {
                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                    }
                });
            });


        $('#attPulling_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                upload_fromDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_from_date_is_required');?>.'}}}, /*From date is required*/
                upload_toDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_to_date_is_required');?>.'}}}, /*To date is required*/
                //floorID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_floor_is_required');?>.'}}}, /*Floor is required*/
                machineTypeID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('hrms_attendance_machine_is_required');?>.'}}}/*Machine is required*/
            },
            //floorID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_floor_is_required');?>.'}}}/*Floor is required*/
        })
            .on('success.form.bv', function (e) {
                e.preventDefault();

                var formData = new FormData($("#attPulling_form")[0]);

                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: formData,
                    url: '<?php echo site_url('Employee/attendancePulling'); ?>',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();


                        if (data[0] == 'w') {
                            var x = 1;
                            if (jQuery.isEmptyObject(data[2])) {
                                $('#not_approved_attdance').empty();
                            } else {
                                $('#not_approved_attdance').empty();
                                $.each(data[2], function (key, value) {
                                    if(!jQuery.isEmptyObject(value['documentCode'])){
                                        $('#not_approved_attdance').append('<tr><td>' + x + '</td> <td>' + value['documentCode'] + '</td></tr>');
                                        x++;
                                    }
                                });
                                $('#not_approved_attdance_model').modal('show');
                            }
                            /*setTimeout(function () { fetch_document(); },400);*/
                        }else{
                         myAlert(data[0], data[1]);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        //myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            });

        $('#attFetching_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                fromDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_date_is_required');?>.'}}}, /*Date is required*/
                toDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_date_is_required');?>.'}}}/*Date is required*/
            }
        })
            .on('success.form.bv', function (e) {
                $('#loadBtn').prop('disabled', false);
                e.preventDefault();
                loadDataFromTemptable();
            });

        load_attendanceTB();
        loadDataFromTemptable();


    });

    function pull_attdance(){
        var formData = new FormData($("#attPulling_form")[0]);

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: formData,
            url: '<?php echo site_url('Employee/attendancePulling'); ?>',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                $('#not_approved_attdance_model').modal('hide');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function clearfilter() {
        $('#asOfDate').val('');
        $('#filterfloor').val('').change();
        filterAttendanceTable();
    }

    function edit_attendance(attendanceDate, floorID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {attendanceDate: attendanceDate, floorID: floorID, hideedit: true},
            url: "<?php echo site_url('Employee/machineattendanceView'); ?>",
            beforeSend: function () {
                startLoad();

            },
            success: function (data) {
                $('#divLoadPage').html(data);
                $('#attendanceview').modal('show');
                if ($('#approvedYN').val() == 0) {
                    $('.hideremove').removeClass('hide');
                }
                else {
                    $('.hideremove').addClass('hide');
                }

                stopLoad();

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/

            }
        });
    }


    function filterAttendanceTable() {
        attMasterTB.ajax.reload();
    }

    function confirm_att() {
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/attendaceVerifyRecords'); ?>",
            type: 'post',
            dataType: 'json',
            data: $('#attFetching_form').serializeArray(),
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                Absentcount='';
                if(data[1]>0){
                    Absentcount='List contains '+data[1]+' Absenties.'
                }
                confirm_att1(Absentcount)

            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });

    }

    function confirm_att1(Absentcount) {
        swal({
                title: "", /*Are you sure?*/
                text: Absentcount + " <?php echo $this->lang->line('common_are_you_sure');?>  <?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*Confirm*/
            },
            function () {

                var data = $('#attFetching_form').serializeArray();
                data.push({"name": "type", "value": "manual"});

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Employee/attendance_confirmation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        if (data['error'] == 0) {
                            myAlert('s', data['message']);
                           // fetchPage('system/hrm/manual_attendance_management', '', 'HRMS');
                        } else {
                            myAlert('e', data['message']);
                        }

                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function load_attendanceTB(selectedRowID=null) {
        var selectedRowID = (selectedRowID == null) ? '<?php echo $this->input->post('page_id'); ?>' : selectedRowID;

        attMasterTB = $('#attendanceMasterTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/attendanceMachineTable'); ?>",
            "aaSorting": [[2, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;

                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);

                    if (parseInt(oSettings.aoData[x]._aData['EmpAttMasterID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }

                    x++;
                }
            },
            "aoColumns": [
                {"mData": "attendanceDate"},
                {"mData": "floorDescription"},
                {"mData": "attendanceDate"},
                {"mData": "approvedYN"},


                {"mData": "edit"}
                /*  {"mData": "AttTime"},
                 {"mData": "isClosed"},
                 {"mData": "action"}*/
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "asofDate", "value": $('#asOfDate').val()});
                aoData.push({"name": "filterDepartment", "value": $('#filterfloor').val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function open_attendanceModal() {
        newAttendance_form[0].reset();
        newAttendance_form.bootstrapValidator('resetForm', true);
        newAttendance.modal('show');
    }

   
    function delete_all(){
        var postData = $('#attFetching_form').serializeArray();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record !*/
                type: "warning", /*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*cancel*/
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/deleteall_attendanceMaster'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: postData,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0]=='s'){
                            loadDataFromTemptable();
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function delete_attendanceMaster(attID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record !*/
                type: "warning", /*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*cancel*/
            },
            function () {
                $.ajax({
                    async: true,
                    url: "<?php echo site_url('Employee/delete_attendanceMaster'); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: {'hidden-id': attID},
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            load_attendanceTB()
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'error');
                    }
                });
            }
        );
    }

    function saveAttendance() {
        var postData = $('#attendanceDetail_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Employee/save_attendanceDetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    attendanceDetail.modal('hide');
                    load_attendanceTB()
                }
            }, error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    /*** Attendance review  functions****/
    function loadDataFromTemptable() {
        var postData = $('#attFetching_form').serializeArray();

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url('Employee/load_empAttDataViewManual'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='e'){
                    myAlert(data[0], data[1]);
                }else{

                    $('#attendanceReview >tbody').html(data['tBody']);
                    $('#attReview-showingCount').text(data['rowCount']);
                    $('#attReview-totalRowCount').text(data['rowCount']);

                    makeDate_dropDown(data['date_arr']);
                    unAssignedData_manipulation(data['unAssignedMachineID'], data['unAssignedShifts']);

                    $('.timeTxt').timepicker({

                        defaultTime: false, showMeridian: true
                    }).on('changeTime.timepicker', function (e) {
                        value = e.time.value;
                        trID = $(this).closest('tr').attr('data-id');
                        masterID = $(this).closest('tr').attr('data-masterid');
                        name = $(this).attr('name');


                        updatefields(trID, masterID, value, name);
                    });

                    $(".timeTxt").change(function () {
                        var input = $(this);
                        if (input.val() == '') {
                            trID = input.closest('tr').attr('data-id');
                            masterID = input.closest('tr').attr('data-masterid');
                            name = input.attr('name');
                            value = input.val();

                            updatefields(trID, masterID, value, name);
                        }

                    });
                }



            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                $('#attendanceReview >tbody').html('');
                $('#attendanceReview').append('<tr><td colspan="21"><?php echo $this->lang->line('common_no_data_available_in_table');?></td></tr>');
                /* No data available in table */

                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function makeDate_dropDown(date_arr) {
        var searchDate = $('#searchDate');
        var options = '<option value="" selected="selected"><?php echo $this->lang->line('hrms_attendance_search_date');?></option>';
         /* Search Date */

        searchDate.empty();
        $.each(date_arr, function (val) {
            options += '<option value="' + val + '" >' + val + '</option>';
        });
        searchDate.append(options);
    }

    function unAssignedData_manipulation(unAssignedMachineID_arr, unAssignedShifts_arr) {
        var unAssignedShift_div = $('#unAssignedShift-div');
        var unAssignedMachine_div = $('#unAssignedMachine-div');

        unAssignedShift_div.hide();
        unAssignedMachine_div.hide();

        if (unAssignedMachineID_arr.length > 0) {
            unAssignedMachine_div.show();

            $('#unAssignedMachine tbody').remove();
            var unAssignedMachineTB = $('#unAssignedMachine');
            var machineDet = '';

            $.each(unAssignedMachineID_arr, function (i, row) {
                machineDet += '<tr>';
                machineDet += '<td>' + (i + 1) + '</td>';
                machineDet += '<td>' + row['ECode'] + '</td>';
                machineDet += '<td>' + row['Ename1'] + '</td>';
                machineDet += '<td></td>';
                machineDet += '</tr>';
            });

            unAssignedMachineTB.append(machineDet);
        }

        if (unAssignedShifts_arr.length > 0) {
            unAssignedShift_div.show();

            $('#unAssignedShift tbody').remove();
            var unAssignedShiftTB = $('#unAssignedShift');
            var shiftDet = '';

            $.each(unAssignedShifts_arr, function (i, row) {
                shiftDet += '<tr >';
                shiftDet += '<td>' + (i + 1) + '</td>';
                shiftDet += '<td>' + row['ECode'] + '</td>';
                shiftDet += '<td>' + row['Ename1'] + '</td>';
                shiftDet += '<td></td>';
                shiftDet += '</tr>';
            });

            unAssignedShiftTB.append(shiftDet);
        }
    }

    function save_attReview() {
        var postData = $('#attendanceReview_form').serializeArray();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url('Employee/save_attendanceReviewData'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });


    }

    $('#attendanceReview').on('click', 'tbody tr', function (event) {
        $(this).addClass('highlight').siblings().removeClass('highlight');
    });


    function opentemplatefields(){
        var attID=0;
        $.ajax({
            type: 'post',
            dataType: 'html',
            data: {'id': attID},
            url: '<?php echo site_url('Employee/load_templatefields'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#templatefieldsmodal').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_upload_excel(){
        if($("#isUploadExcel").is(':checked')){
            $("#isManualAttendance"). prop("checked", false);
            $('#exceluploadattdance').removeClass('hidden');
            $('#sendEmail').addClass('hidden');
            $('#date-format-container').show();
            $('#floor-container').show();
        }else{
            $('#exceluploadattdance').addClass('hidden');
            $('#sendEmail').removeClass('hidden');
            $('#date-format-container').hide();
            $('#floor-container').hide();
        }
    }

    function hide_upload_excel(){
        if($("#isManualAttendance").is(':checked')){
            $("#isUploadExcel"). prop("checked", false);
            $('#exceluploadattdance').addClass('hidden');
            $('#sendEmail').removeClass('hidden');
            $('#date-format-container').hide();
            $('#floor-container').show();
        }else{
            $('#floor-container').hide();
        }
    }

    function upload_excel(){
        if($('#floorID').val()==''){
            myAlert('w','Select Location');
        }else{
            $('#excelUpload_Modal').modal('show');
        }

    }


    function excel_upload_attandance(){
        var floorIDhnupload=$('#floorID').val();
        var fromDatehnupload=$('#upload_fromDate').val();
        var toDatehnupload= $('#upload_toDate').val();
        $('#floorIDhnupload').val(floorIDhnupload);
        $('#fromDatehnupload').val(fromDatehnupload);
        $('#toDatehnupload').val(toDatehnupload);
        $('#date_format_post').val($('#date_format').val());
        var formData = new FormData($("#attdanceUpload_form")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Employee/attandance_master_excelUpload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#excelUpload_Modal').modal('hide');

                    setTimeout(function(){
                        $('#sendEmail').submit();
                    }, 1500);
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }


    function downloadExcel(){
        var floorID = $('#floorID').val();
        var upload_fromDate = $('#upload_fromDate').val();
        var upload_toDate = $('#upload_toDate').val();
        var msg = '';

        if(floorID == ''){
            msg = 'Floor is required<br/>';
        }

        if(upload_fromDate == ''){
            msg += 'From date is required<br/>';
        }

        if(upload_toDate == ''){
            msg += 'To date is required<br/>';
        }

        if(msg != ''){
            myAlert('w', msg);
            return false;
        }

        var form= document.getElementById('attPulling_form');
        form.target='_blank';
        form.action='<?php echo site_url('Employee/downloadExcel'); ?>';
        form.submit();
    }

    function reUpload_attendance_data()
    {
        var formData = new FormData($("#attFetching_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Employee/attandance_reUpload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function close_manufaturejob_modal(){
        $('#modal_load_job_change').modal('hide');
    }


</script>
<?php
