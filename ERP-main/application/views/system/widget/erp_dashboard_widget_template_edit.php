<!---- =============================================
-- File Name : erp_item_counting_report.php
-- Project Name : SME ERP
-- Module Name : Report - Inventory
-- Create date : 15 - September 2016
-- Description : This file contains Item counting.

-- REVISION HISTORY
-- =============================================-->
<?php
$imageName=$userDashboardID->imageName;
echo '
<div class="col-xs-4 col-md-2">
    <div class="listrap-toggle">
        <span></span>
        <a href="#" class="thumbnail">
        <img src="' . base_url("images/$imageName") . '" alt="' . $userDashboardID->panelDescription . '">
        </a>
    </div>
</div>'
?>

<script>
    loadTemplate(<?php echo $userDashboardID->templateID; ?>,<?php echo $userDashboard ?>);

</script>
