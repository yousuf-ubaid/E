<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="box box-info">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_external_birthday_reminder');?><!--Birthday Reminder--></h4>

        <div class="pull-right">
            Remaining Days
            <input type="text" class="numeric" name="remainingDays" id="remainingDays<?php echo $userDashboardID ?>" value="7"
                   style="width: 50px;" onchange="birthdayReminder_view<?php echo $userDashboardID ?>()" >
        </div>
    </div>

    <div class="box-body" style="display: block;width: 100%">
        <div id="birthdayReminder_view<?php echo $userDashboardID ?>"></div>
    </div>
    <div class="overlay" id="overlay119<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
</div>

<script>
    $('#remainingDays<?php echo $userDashboardID ?>').numeric();


    birthdayReminder_view<?php echo $userDashboardID ?>();


    function birthdayReminder_view<?php echo $userDashboardID ?>(){
        var days = $('#remainingDays<?php echo $userDashboardID ?>').val();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/birthdayReminder_view'); ?>",
            data: {days: days,userDashboardID:<?php echo $userDashboardID ?>},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#birthdayReminder_view<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }
</script>

<?php
