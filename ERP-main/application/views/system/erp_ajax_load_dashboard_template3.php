<div class="row">
    <div class="col-md-6">
        <div id="1C<?php echo $userDashboardID ?>"></div>
        <div id="2C<?php echo $userDashboardID ?>"></div>
        <div id="3C<?php echo $userDashboardID ?>"></div>
    </div>
    <div class="col-md-6">
        <div id="">
            <div id="4C<?php echo $userDashboardID ?>"></div>
            <div id="5C<?php echo $userDashboardID ?>"></div>
            <div id="6C<?php echo $userDashboardID ?>"></div>
        </div>
    </div>
</div>

<?php
$data['userDashboardID'] = $userDashboardID;
$this->load->view('system/dashboard/common_js',$data);
?>