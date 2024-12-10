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
$department = fetch_emp_departments(true);

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
</style>

<fieldset class="scheduler-border">
    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters');?><!--Filter--></legend>
    <?php echo form_open('', ' class="form-horizontal" id="filter_form" role="form"'); ?>
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

            <label for="inputData" class="col-md-1 control-label" style="width: 80px; text-align: left;"><?php echo $this->lang->line('common_employee');?></label>
            <div class="col-md-2" id="">
                <?php
                $employee = all_employee_drop(False, 1);
                if (isset($employee)) {
                    foreach ($employee as $row) {
                        $employee_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                    }
                }
                echo form_dropdown('empID[]', $employee_arr, '', 'id="empID" multiple="multiple" class="form-control mid-width wrapItems "');
                ?>
            </div>

            <label for="inputCodforn" class="col-md-1 control-label" style="width: 100px; text-align: right;"><?php echo $this->lang->line('hrms_attendance_department');?></label>
            <div class="col-md-2" style="width: 140px">
                <?php
                echo form_dropdown('department_type[]', $department, null, 'class="form-control department_type" id="department_type_emp" multiple="multiple" style="width:80px"');
                ?>
            </div>

            <label for="inputCodforn" class="col-md-1 control-label" style="width: 100px; text-align: right;"><?php echo $this->lang->line('hrms_attendance_present');?></label>
            <div class="col-md-2" style="width: 140px">
                <?php
                echo form_dropdown('att_type[]', $att_drop, null, 'class="form-control att_type" id="att_type_my_employee" multiple="multiple" style="width:80px"');
                ?>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-primary btn-xs" onclick="load_data()"><?php echo $this->lang->line('common_load');?></button>
                <button type="button" class="btn btn-success btn-xs" onclick="generateReportExcel()" ><?php echo $this->lang->line('common_excel');?></button>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</fieldset>

<div id="response-container" style="margin-top: 10px;"></div>

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
    $("#empID").multiselect2('selectAll', false);
    $("#empID").multiselect2('updateButtonText');

    $('.att_type').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 0
    });
    $(".att_type").multiselect2('selectAll', false);
    $(".att_type").multiselect2('updateButtonText');

    $('.department_type').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        maxHeight: 200,
        numberDisplayed: 0
    });
    $(".department_type").multiselect2('selectAll', false);
    $(".department_type").multiselect2('updateButtonText');

    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/hrm/report/attendance-view-report','','HRMS');
        });

        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.date_pic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        }).on('dp.change', function (ev) {

        });

    });

    function load_data() {
        var post_data = $('#filter_form').serializeArray();
        var progress_defined = 4;
        var progress = 0; 

        $.ajax({
            async: true,
            type: 'post',
           // dataType: 'json',
            data : post_data,
            url: "<?php echo site_url('Employee/load_attendance_view'); ?>",
            beforeSend: function () {
                $('#response-container').html('');

                // $('#progress-bar').css('width','0%');
                $('#progress-amount').html('');
                $('#progress-bar').css('background-color','#8f6782');
                $('#progress-bar-message').text('Data is Loading Wait..!');
                
                $('#progress_bar_model').modal('show');

                startLoad();
            },
            success: function (data_received) {

                stopLoad();

                data = data_received.replaceAll('@','');
                data = JSON.parse(data);
                if (data[0] == 's') {
                    $('#response-container').html( data['view'] );

                    $('#progress-bar').css('background-color','green');
                    close_progress_bar();
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.addEventListener("progress", function(event) {

                    progress += 1;

                    if(progress > progress_defined){
                        progress = progress_defined;
                    }
          
                    let percentComplete = (((progress) / progress_defined) * 100) + '%';
                    
                    // if(percentComplete != '0%'){
                    $('#progress-bar').css('width',percentComplete);
                    $('#progress-amount').html('');
                    // }
                   
                });
                return xhr;
            }, error: function () {
                myAlert('e', common_an_error)
            }
        });

        $("#request_modal").modal({backdrop: "static"});
    }

    function open_leave_conversation(review_id, att_date){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : {'review_id':review_id, 'att_date':att_date, 'is_report': 1},
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

    function generateReportExcel() {
        var msg = '';
        if( $('#from_date').val() == '' ){
            msg += 'From Date field is required<br/>';
        }

        if( $('#to_date').val() == '' ){
            msg += 'To Date field is required';
        }
       if( $('#empID').val() == null ){
            msg += 'Employee field is required';
        }

        if( $('#att_type_my_employee').val() == null ){
            msg += 'Present field is required';
        }

        if(msg != ''){
            myAlert('e', msg);
            return false;
        }

        var form = document.getElementById('filter_form');
        form.target = '_blank';
        form.action = '<?php echo site_url('Employee/load_attendance_view/excel') ?>';
        form.submit();
    }
</script>

<?php
