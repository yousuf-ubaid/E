<!--Translation added by Naseek-->
<?php


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_attendance_machine_attendance_management');
echo head_page($title, false);

$current_date = format_date($this->common_data['current_date']);
$floor=floors_fetch();
$to_day = date('Y-m-d');
$to_day = date('2019-09-13');
?>

<style type="text/css">
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

    .fixHeader_Div {
        height: 500px;
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

    .fixed-td {
        z-index: 10;
    }

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
        background-color: #303a4a;
        color: #fff;
    }

    .attendanceReview .table > tbody > tr > td {
        padding: 4px;
    }

    .input_disabled {
        background-color: white;
    }

    #attendanceReview .input-group-addon {
        border: 0px solid #ccc;
    }

    #attendanceReview tbody tr > td:nth-child(7){
        background-color: #F7F8FA;
    }

    .punch_area{
        background: #0a3544;
        color: #fff2e1;
    }

    .punch_area:hover{
        cursor: pointer;
    }

    #attendanceReview>tbody>tr>td{
        padding: 2px;
    }

    .label-confirmed{
        background-color: #3c8dbc
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row  div-view1">
    <div class="form-horizontal">
        <?php echo form_open('', 'role="form" class="" id="attFetching_form" autocomplate="off"');?>
        <div class="col-sm-12">
            <label class="col-sm-1 control-label" for="fromDate"><?=$this->lang->line('common_from').' '.$this->lang->line('common_date'); required_mark();?></label>
            <div class="col-sm-1">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="fromDate" value="<?=$to_day;?>" id="fromDate" class="form-control dateField" style="width: 75px;">
                </div>
            </div>

            <label class="col-sm-1 control-label" for="fromDate"><?=$this->lang->line('common_to').' '.$this->lang->line('common_date'); required_mark();?></label>
            <div class="col-sm-2">
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="toDate" value="<?=$to_day;?>" id="toDate" class="form-control dateField" style="width: 75px;">
                </div>
            </div>

            <div class="col-sm-1">
                <button class="btn btn-primary btn-xs" style="margin-top: 5px;">
                    <i class="fa fa-arrow-circle-down"></i> <?=$this->lang->line('hrms_attendance_load'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<hr/>

<div class="row" style="margin-bottom: 1%">
    <div class="col-sm-3 col-xs-5" style="">
        <table class="<?php echo table_class(); ?>" style="margin-top: 3px">
            <tr>
                <td>
                    <span style="padding: 0px 5px; font-size: 100%" class="label label-confirmed">&nbsp;</span> &nbsp; Confirmed
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    <span style="padding: 0px 5px; font-size: 100%" class="label label-success" > &nbsp; </span> &nbsp; Approved
                </td>
            </tr>
        </table>
    </div>
    <div class="col-sm-1 hidden-xs">&nbsp;</div>
    <div class="col-sm-6 col-xs-6">
        <select name="search_status" class="form-control pull-right" id="search_status" style="max-width:120px;" onchange="filter_table()">
            <option value="" selected="selected">All Status</option>
            <option value="1">Confirmed</option>
            <option value="2">Approved</option>
        </select>

        <select class="form-control pull-right" name="floorID" id="search_floorID" style="max-width:120px; margin-right: 10px" onchange="filter_table()">
            <option value="" selected="selected">All locations</option>
            <?php
            foreach ($floor as $item) {
                echo '<option value="' . $item['floorID'] . '">' . $item['floorDescription'] . '</option>';
            }
            ?>
        </select>

        <select name="search_type" class="form-control pull-right" id="search_type" style="max-width:120px; margin-right: 10px" onchange="filter_table()">
            <option value="" selected="selected">All Types</option>
            <option value="t1">Missing Punch</option>
            <option value="t2">More than 12H</option>
            <option value="t3">Standard</option>
            <option value="t4">On Leave</option>
            <option value="P">Present</option>
        </select>

        <select name="searchDate" class="form-control pull-right" id="searchDate" style="max-width:120px; margin-right: 10px;" onchange="filter_table()">
            <option value=""> <?= $this->lang->line('hrms_attendance_search_date'); ?><!--Search Date--></option>
        </select>
    </div>
    <div class="col-sm-2 col-xs-4">
        <input type="text" class="form-control" id="attReview-searchItem" value="" title="Press enter to search" autocomplete="off"
               placeholder="<?= $this->lang->line('common_search_name').' | '. $this->lang->line('common_code'); ?>">
    </div>
</div>
<div class="table-responsive" style="padding: 0px !important;">
    <?php echo form_open('', 'role="form" class="" id="attendanceReview_form" autocomplete="off"'); ?>
    <div class="fixHeader_Div" style="max-width: 100%;">
        <table id="attendanceReview" class="table tb " style="max-width: 1750px !important; margin-top: -1px;">
            <thead class="">
            <tr style="white-space: nowrap">
                <th style="width: 15px;">#</th>
                <th style="min-width: 120px;"><?= $this->lang->line('hrms_attendance_employee_name'); ?></th>
                <th style="width: 120px;"><?=$this->lang->line('common_date'); ?></th>
                <th style="z-index: 10; width: 115px"><?=$this->lang->line('hrms_attendance_floor'); ?></th>
                <th style="z-index: 10; width: 75px"><?=$this->lang->line('hrms_attendance_in'); ?></th>
                <th style="z-index: 10; width: 75px"><?= $this->lang->line('hrms_attendance_out'); ?></th>
                <th style="width: 60px;"><?= $this->lang->line('common_total'); ?></th>
                <th style="width: 105px"><?=$this->lang->line('common_status'); ?></th>
                <th style="width: 105px"><?=$this->lang->line('hrms_attendance_punches'); ?></th>
                <th style="width: 25px; z-index: 10">
                    <input type="checkbox" onclick="check_all()" id="check-all" class="btn" >
                </th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td colspan="10"><?= $this->lang->line('hrms_attendance_No_data_available_in_table'); ?><!--No data available in table--></td>
            </tr>
            </tbody>
        </table>
    </div>
    <?php echo form_close(); ?>
</div>

<div class="row">
    <div class="" style="margin-top: 1% !important;">
        <div class="col-sm-6 col-xs-6">
            <label>
                Showing <span id="attReview-showingCount"> 0 </span> of
                <span id="attReview-totalRowCount"> 0 </span> entries
            </label>
        </div>

        <div class="col-sm-6 col-xs-6">
            <button type="button" class="btn btn-primary btn-sm pull-right" onclick="confirm_att()">
                <?php echo $this->lang->line('common_confirm'); ?><!--  Confirm-->
            </button>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>


<script type="text/javascript">
    $('body').addClass('sidebar-collapse');

    $('#attendanceReview').tableHeadFixer({
        head: true
    });

    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/hrm/attendance_management_oman_oil', 'Test', 'HRMS');
        });


        $('.dateField').datepicker({format: 'yyyy-mm-dd'}).on('changeDate', function (ev) {
            $(this).datepicker('hide');

            if (this.id == 'fromDate') {
                $('#attFetching_form').bootstrapValidator('revalidateField', 'fromDate');
            }
            else {
                $('#attFetching_form').bootstrapValidator('revalidateField', 'toDate');
            }
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

    });

    /*** Attendance review  functions****/
    function loadDataFromTemptable() {
        let postData = $('#attFetching_form').serializeArray();

        $.ajax({
            type: 'post',
            dataType: 'json',
            data: postData,
            url: '<?php echo site_url('Employee/load_empAttDataView_oman_oil'); ?>',
            beforeSend: function () {
                startLoad();
                $('#attReview-searchItem, #search_type, #search_floorID').val('');
                $('#check-all').prop('checked', false);
            },
            success: function (data) {
                stopLoad();
                if(data[0]=='e'){
                    myAlert(data[0], data[1]);
                }else{
                    $('html, body').animate({
                        scrollTop: $("#attFetching_form").offset().top
                    }, 800);

                    $('#attendanceReview >tbody').html(data['tBody']);
                    $('#attReview-showingCount').text(data['rowCount']);
                    $('#attReview-totalRowCount').text(data['rowCount']);

                    makeDate_dropDown(data['date_arr']);


                    $('.timeTxt').timepicker({
                        defaultTime: false, showMeridian: true
                    }).on('changeTime.timepicker', function (e) {
                        value = e.time.value;
                        trID = $(this).closest('tr').attr('data-id');
                        masterID = $(this).closest('tr').attr('data-master-id');
                        name = $(this).attr('name');


                        update_fields(trID, masterID, value, name);
                    });

                    $(".timeTxt").change(function () {
                        var input = $(this);
                        if (input.val() == '') {
                            trID = input.closest('tr').attr('data-id');
                            masterID = input.closest('tr').attr('data-master-id');
                            name = input.attr('name');
                            value = input.val();

                            update_fields(trID, masterID, value, name);
                        }

                    });
                }



            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                $('#attendanceReview >tbody').html('');
                $('#attendanceReview').append('<tr><td colspan="21"><?php echo $this->lang->line('common_no_data_available_in_table');?></td></tr>');
                <!--No data available in table-->

                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function makeDate_dropDown(date_arr) {
        let searchDate = $('#searchDate');
        let options = '<option value="" selected="selected"><?php echo $this->lang->line('hrms_attendance_search_date');?></option>';
        <!--Search Date-->

        searchDate.empty();
        $.each(date_arr, function (val) {
            options += '<option value="' + val + '" >' + val + '</option>';
        });
        searchDate.append(options);
    }

    function check_all(){
        if( $('#check-all').prop('checked') ){
            $('.att-check').not('.hide-chk').prop('checked', true);
        }
        else{
            $('.att-check').not('.hide-chk').prop('checked', false);
        }
    }

    $('#attendanceReview').on('click', 'tbody tr', function (event) {
        $(this).addClass('highlight').siblings().removeClass('highlight');
    });

    function confirm_att() {
        let count = $('.att-check:checked').length;

        if(count < 1){
            myAlert('e', 'Please select at least one record to confirm');
            return false;
        }

        swal({
                title: "Are you sure?",
                text: 'You are going to confirm '+count+' records.',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                let logID_list = [];
                $('.att-check:checked').each(function(i,v){
                    logID_list.push( $(this).val() );
                });

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'logID': logID_list},
                    url: "<?php echo site_url('Employee/confirm_attendanceRec'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            let lbl = '<label class="label label-confirmed" > &nbsp; </label>';
                            $('.att-check:checked').each(function(i,v){
                                $(this).parent().html(lbl);
                            });
                        }
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'Error in confirmation process');
                    }
                });
            }
        );
    }
</script>
