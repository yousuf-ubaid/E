<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="box box-info">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_external_contract_reminder');?><!--Birthday Reminder--></h4>

        <div class="pull-right">
            Remaining Days
            <input type="text" class="numeric" name="remainingDays" id="remainingDaysContract<?php echo $userDashboardID ?>" 
                   style="width: 50px;" onchange="contractReminder_view<?php echo $userDashboardID ?>()" >
        </div>
    </div>

    <div class="box-body" style="display: block;width: 100%">
        <div id="contractReminder_view<?php echo $userDashboardID ?>"></div>
    </div>
</div>

<script>
    $('#remainingDaysContract<?php echo $userDashboardID ?>').numeric();


    contractReminder_view<?php echo $userDashboardID ?>();


    function contractReminder_view<?php echo $userDashboardID ?>(){
        var days = $('#remainingDaysContract<?php echo $userDashboardID ?>').val();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/contractReminder_view'); ?>",
            data: {days: days,userDashboardID:<?php echo $userDashboardID ?>},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#contractReminder_view<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }

</script>

<?php
