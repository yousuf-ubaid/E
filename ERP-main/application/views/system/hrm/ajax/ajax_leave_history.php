<?php
$date_format_policy = date_format_policy();
?>
<div class="row">
    <div class="col-sm-12">
    <div class="col-sm-6">
        <div class="row">
            <div>
                <label>Employee Leaves Summary</label>
            </div>
        </div>
        <div class="row"">
        <div class="form-group col-sm-6" style="width: 40%;">
            <label class="title">From :</label>
        </div>
        <div class="form-group col-sm-4">
            <div class="input-group fromdate_date">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="fromdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo  date('01-01-Y')?>" id="fromdate" class="form-control fromdate" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-6" style="width: 40%;" >
            <label class="title">To :</label>
        </div>
        <div class="form-group col-sm-4">
            <div class="input-group todate_date">
                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                <input type="text" name="todate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo  date('31-12-Y')?>" id="todate" class="form-control todate" required>
            </div>
        </div>
    </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div id="leave_blance_report_detail">
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#fromdate').val('<?php echo  date('01-01-Y')?>');
        $('#todate').val('<?php echo  date('31-12-Y')?>');
        leaveblance_filter();
    });
    $('#balance-span').html( '10');
    $("[rel=tooltip]").tooltip();
function show_leavedetail(leavedetailID) {
    switch (leavedetailID) {
        case 1:
            $('.leaveopeningbal').removeClass('hide');
            $('.entitleduringperiod').addClass('hide');
            $('.utilized').addClass('hide')
           break;
        case 2:
            $('.leaveopeningbal').addClass('hide');
            $('.entitleduringperiod').removeClass('hide');
            $('.utilized').addClass('hide')
            break;
        case 3:
            $('.leaveopeningbal').addClass('hide');
            $('.entitleduringperiod').addClass('hide');
            $('.utilized').removeClass('hide')
        default:
            break;
    }
}
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.fromdate').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });
    $('.todate').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });
    $('.fromdate').on('dp.change', function(e){
        leaveblance_filter();
    });
    $('.todate').on('dp.change', function(e){
        leaveblance_filter();
    });
    function leaveblance_filter()
    {
        var leaveTypeID = <?php echo $leaveTypeID?>;
        var isFromEmployeeMaster = '<?php echo $isFromEmployeeMaster?>';
        var taken = '<?php echo $taken?>';
        var empID = '<?php echo $empID?>';
        var fromdate = $('#fromdate').val();
        var todate = $('#todate').val();
        $.ajax({
            async: true,
            url: "<?php echo site_url('Employee/loadleaveblanceHistory_details'); ?>",
            type: 'post',
            dataType: 'html',
            data: {'leaveTypeID': leaveTypeID,'fromdate':fromdate,'todate':todate,'isFromEmployeeMaster':isFromEmployeeMaster,'taken':taken,'empID':empID},
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#leave_blance_report_detail').html(data);

                stopLoad();
            }, error: function () {
                myAlert('e', 'error');
            }
        });
    }
</script>