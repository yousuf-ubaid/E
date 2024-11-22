<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_attendance_view');
echo head_page($title, false);
$date_format_policy = date_format_policy();
$emp_id = current_userID();
$current_date = current_format_date();
$monthFirst = convert_date_format( date('Y-m-01') );

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

$att_drop = attendanceType_drop(true);
?>

<style>
    fieldset {
        border: 1px solid silver;
        border-radius: 0px;
        padding: 1%;
        padding-bottom: 15px;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-weight: bold !important;
        font-size: 14px;
        color: #6a6c6f;
    }

    #nav-tab{
        border-right: 1px solid;
        border-left: 1px solid;
        border-left-color: #f4f4f4;
        border-right-color: #f4f4f4;
        border-top: 1px solid #f4f4f4;
    }
</style>

<div class="nav-tabs-custom" id="nav-tab">
    <ul class="nav nav-tabs" style="" >
        <li class="active">
            <a href="#my-tab" id="" class="" data-toggle="tab" aria-expanded="true">
                <?php echo $this->lang->line('hrms_leave_management_leave_my_attendance_view');?>
            </a>
        </li>
        <li class="">
            <a href="#my-employee-tab" id="" class=""  data-toggle="tab" aria-expanded="false">
                <?php echo $this->lang->line('hrms_leave_management_leave_my_employee_attendance');?>
            </a>
        </li>
        <li class="">
            <a href="#my-employee-shift" id="" class=""  data-toggle="tab" aria-expanded="false">
                <?php echo $this->lang->line('hrms_leave_management_assign_shift');?>
            </a>
        </li>
        <li class="">
            <a href="#attendance-registry" id="" class=""  data-toggle="tab" aria-expanded="false" onclick="load_employee_attendance_section()">
                <?php echo $this->lang->line('hrms_leave_management_attendance_registry');?>
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="my-tab" >
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><?php echo $this->lang->line('common_filters');?><!--Filter--></legend>
                <?php echo form_open('', ' class="form-horizontal" id="my_filter_form" role="form"'); ?>
                <input type="hidden" name="request-type" value="my">
                <div class="row">
                    <div class="col-md-12 ">
                        <label for="inputData" class="col-md-1 control-label" style="width: 70px; text-align: left;"><?php echo $this->lang->line('common_from_date');?> </label>
                        <div class="col-md-2" style="width: 140px">
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control" id="from_date" name="from_date"  value="<?php echo $monthFirst; ?>"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required />
                            </div>
                        </div>

                        <label for="inputCodforn" class="col-md-1 control-label" style="width: 100px; text-align: right;"><?php echo $this->lang->line('common_to_date');?></label>
                        <div class="col-md-2" style="width: 140px">
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control" id="to_date" name="to_date"  value="<?php echo $current_date; ?>"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required />
                            </div>
                        </div>

                        <label for="inputCodforn" class="col-md-1 control-label" style="width: 100px; text-align: right;"><?php echo $this->lang->line('hrms_attendance_present');?></label>
                        <div class="col-md-2" style="width: 140px">
                            <?php
                            echo form_dropdown('att_type[]', $att_drop, null, 'class="form-control att_type" id="att_type_my" multiple="multiple" style="width:80px"');
                            ?>
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary btn-sm" onclick="my_data_load()" id="filterLoad"><?php echo $this->lang->line('common_load');?></button>&nbsp;
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </fieldset>

            <div id="response-container"></div>
        </div>

        <div class="tab-pane"  id="my-employee-tab" >
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><?php echo $this->lang->line('common_filters');?><!--Filter--></legend>
                <?php echo form_open('', ' class="form-horizontal" id="employee_filter_form" role="form"'); ?>
                <input type="hidden" name="request-type" value="my_employee">
                <div class="row">
                    <div class="col-md-12 ">
                        <label for="inputData" class="col-md-1 control-label" style="width: 86px; text-align: left;"><?php echo $this->lang->line('common_from_date');?> </label>
                        <div class="col-md-1" style="width: 140px">
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control" id="from_date" name="from_date"  value="<?php echo $monthFirst; ?>"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required />
                            </div>
                        </div>

                        <label for="inputCodforn" class="col-md-1 control-label" style="width: 69px; text-align: right;"><?php echo $this->lang->line('common_to_date');?></label>
                        <div class="col-md-1" style="width: 140px">
                            <div class="input-group date_pic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control" id="to_date" name="to_date"  value="<?php echo $current_date; ?>"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required />
                            </div>
                        </div>

                        <label for="inputCodforn" class="col-md-1 control-label" style="width: 70px; text-align: right;"><?php echo $this->lang->line('hrms_attendance_present');?></label>
                        <div class="col-md-2" style="width: 140px">
                            <?php
                            echo form_dropdown('att_type[]', $att_drop, null, 'class="form-control att_type" id="att_type_my_employee" multiple="multiple" style="width:80px"');
                            ?>
                        </div>

                        <label for="inputData" class="col-md-1 control-label" style="width: 87px; text-align: left;"><?php echo $this->lang->line('common_employee');?></label>
                        <div class="col-md-2" id="">
                            <?php
                            $employee = my_employee_drop();
                            $employee_arr = [];
                            if (isset($employee)) {
                                foreach ($employee as $row) {
                                    $employee_arr[trim($row['emp_id'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['emp_name'] ?? '');
                                }
                            }
                            echo form_dropdown('empID[]', $employee_arr, '', 'id="empID" multiple="multiple" class="form-control mid-width wrapItems "');
                            ?>
                        </div>

                        <label for="inputData" class="col-md-1 control-label" style="width: 70px; text-align: left;"><?php echo $this->lang->line('common_attendees');?></label>
                        <div class="col-md-2" id="">
                            <?php
                            $employee = my_assognee_drop();
                            $employee_arr = [];
                            if (isset($employee)) {
                                foreach ($employee as $row) {
                                    $employee_arr[trim($row['emp_id'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['emp_name'] ?? '');
                                }
                            }
                            echo form_dropdown('empID[]', $employee_arr, '', 'id="assigneID" multiple="multiple" class="form-control mid-width wrapItems "');
                            ?>
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary btn-sm" onclick="employee_data_load()"><?php echo $this->lang->line('common_load');?></button>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </fieldset>

            <div id="response-container-employee"></div>
        </div>

        <div class="tab-pane"  id="my-employee-shift" >
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><?php echo $this->lang->line('common_filters');?><!--Filter--></legend>
                <?php echo form_open('', ' class="form-horizontal" id="employee_filter_form_shift" role="form"'); ?>
                <input type="hidden" name="request-type" value="my_employee">
                <div class="row">
                    <div class="col-md-12 ">

                        <label for="inputData" class="col-md-1 control-label" style="width: 80px; text-align: left;"><?php echo $this->lang->line('common_status');?></label>
                        <div class="col-md-2" id="">
                            <?php
                            $status_arr = array('1' => 'Active', '2' => 'In active');
                            echo form_dropdown('status', $status_arr, '', 'id="status" class="form-control mid-width wrapItems select2"');
                            ?>
                        </div>

                        <label for="inputData" class="col-md-1 control-label" style="width: 80px; text-align: left;"><?php echo $this->lang->line('common_employee');?></label>
                        <div class="col-md-2" id="">
                            <?php
                            $employee = my_employee_drop();
                            $employee_arr = [];
                            if (isset($employee)) {
                                foreach ($employee as $row) {
                                    $employee_arr[trim($row['emp_id'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['emp_name'] ?? '');
                                }
                            }
                            echo form_dropdown('empIDShift[]', $employee_arr, '', 'id="empIDShift" multiple="multiple" class="form-control mid-width wrapItems "');
                            ?>
                        </div>
                        <label for="inputData" class="col-md-1 control-label" style="width: 80px; text-align: left;"><?php echo $this->lang->line('common_attendees');?></label>
                        <div class="col-md-2" id="">
                            <?php
                            $employee = my_assognee_drop();
                            $employee_arr = [];
                            if (isset($employee)) {
                                foreach ($employee as $row) {
                                    $employee_arr[trim($row['emp_id'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['emp_name'] ?? '');
                                }
                            }
                            echo form_dropdown('empIDShift[]', $employee_arr, '', 'id="attendeshif" multiple="multiple" class="form-control mid-width wrapItems "');
                            ?>
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary btn-sm" onclick="employee_data_load_shift()"><?php echo $this->lang->line('common_load');?></button>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </fieldset>

            <div id="response-container-employee-shift"></div>
        </div>

        <div class="tab-pane"  id="attendance-registry" >
            <div id="attendance-section"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="leave_conversation_model" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title" style="line-height: 0.428571;"><?=$this->lang->line('common_comments')?></h3>
            </div>
            <div role="form" id="" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-12" id="chat-container">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<form id="print_form" method="post" action="" target="_blank">
    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    <input type="hidden" id="print_master_id" name="masterID">
</form>


<script type="text/javascript">
    var common_an_error = '<?=$this->lang->line('common_an_error_occurred_Please_try_again')?>';

    $('#empID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 0
    });

    $('#assigneID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 0
    });

    $('#empIDShift').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 0
    });

    $('#attendeshif').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 0
    });

    $("#empID").multiselect2('selectAll', false);
    $("#assigneID").multiselect2('selectAll', false);
    $("#empIDShift").multiselect2('selectAll', false);
    $("#attendeshif").multiselect2('selectAll', false);

    $("#empID").multiselect2('updateButtonText');
    $("#assigneID").multiselect2('updateButtonText');

    $('.att_type').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 0
    });
    $(".att_type").multiselect2('selectAll', false);
    $(".att_type").multiselect2('updateButtonText');

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/profile/attendance-view','','HRMS');
        });

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.date_pic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev) {

        });
    });

    function my_data_load() {
        var post_data = $('#my_filter_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : post_data,
            url: "<?php echo site_url('Employee/my_profile_load_attendance_view'); ?>",
            beforeSend: function () {
                $('#response-container').html('');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#response-container').html( data['view'] );
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', common_an_error)
            }
        });
    }

    function employee_data_load() {
        var post_data = $('#employee_filter_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : post_data,
            url: "<?php echo site_url('Employee/my_profile_load_attendance_view'); ?>",
            beforeSend: function () {
                $('#response-container-employee').html('');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#response-container-employee').html( data['view'] );
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', common_an_error)
            }
        });
    }

    function open_leave_conversation(review_id, att_date){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : {'review_id':review_id, 'att_date':att_date, 'is_report': 0},
            url: "<?php echo site_url('Employee/load_attendance_chat'); ?>",
            beforeSend: function () {
                $('#chat-container').html('');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#chat-container').html( data['view'] );
                    $('#leave_conversation_model').modal('show');
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', common_an_error)
            }
        });
    }

    function update_comment(comment = null,masterID = null){
        var review_comment = (comment) ? comment : $.trim($('#review_comment').val());
        var review_id = (masterID) ? masterID : $('#review_id').val();

        if(review_comment == ''){
            return false;
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : {'review_id':review_id, 'review_comment':review_comment},
            url: "<?php echo site_url('Employee/update_leave_comment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#review_comment').val('').focus();
                    create_new_chat_node( data['ch_data'] );
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', common_an_error)
            }
        });
    }

    function employee_data_load_shift(){
        var post_data = $('#employee_filter_form_shift').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : post_data,
            url: "<?php echo site_url('Employee/my_profile_load_attendance_shift_view'); ?>",
            beforeSend: function () {
                $('#response-container-employee-shift').html('');
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    $('#response-container-employee-shift').html( data['view'] );
                    load_data_table();
                }
                else{
                    myAlert(data[0], data[1]);
                }
            }, error: function () {
                myAlert('e', common_an_error)
            }
        });
    }

    function load_data_table(){
        $('#employee_shift_tbl').DataTable();
    }

    function load_employee_attendance_section(){
     
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('Employee/get_attendance_emp_for_manager'); ?>",
            beforeSend: function () {
               
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#attendance-section').empty();
                $('#attendance-section').html(data);
                
            }, error: function () {
                myAlert('e', common_an_error)
            }
        });

    }

    
    function open_attendanceDetailModalManualEmployee(attendance_master,pageType){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                "attendance_master":attendance_master,
                "view":"system/hrm/attendance_management_manual",
                "pageType":pageType
            },
            url: "<?php echo site_url('Employee/get_attendance_emp_for_manager'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#attendance-section').empty();
                $('#attendance-section').html(data);
                
            }, error: function () {
                myAlert('e', common_an_error)
            }
        });
        
    }

    function open_attendanceDetailModal(attID, obj){
        $('#attendMasterID').val(attID);
        var attDetails_div = $('#attDetails_div');
        attDetails_div.append('');
        attendanceDetail.modal('show');

        var thisRow = $(obj);
        var details = attMasterTB.row(thisRow.parents('tr')).data() ;

        $('.attendance-date-time').html( details.AttDate +' | '+ details.AttTime );


        $.ajax({
            async : true,
            type : 'post',
            dataType : 'html',
            data : {'attID': attID},
            url :"<?php echo site_url('Employee/load_attendanceEmployees_new'); ?>",
            beforeSend: function () {
                $("#overlay").show();
                attDetails_div.html('');
            },
            success : function(data){
                $("#overlay").hide();
                attDetails_div.html(data);
                if (data[0] == 'e') {
                    myAlert(data[0], data[1]);
                }
            },error : function(){
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                attDetails_div.html('');
                $("#overlay").hide();
            }
        });
    }



</script>