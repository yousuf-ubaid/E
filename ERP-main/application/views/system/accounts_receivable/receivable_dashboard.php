<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard', $primaryLanguage);
?>
<div class="dashboard-cus-select">
    <div class="row">
        <div class="col-md-6">
            <div id="overduepayablereceivable_div"></div>
        </div>
        <div class="col-md-6">
            <div id="postdatedcheque_div"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        load_overdue_payable_receivable();
        load_postdated_cheque();
    });

    function load_overdue_payable_receivable() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID: 0, documentID: 'AR'},
            url: "<?php echo site_url('Finance_dashboard/load_overdue_payable_receivable'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#overduepayablereceivable_div").html(data);
            }, error: function () {

            }
        });
    }

    function load_postdated_cheque() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:0, documentID: 'AR'},
            url: "<?php echo site_url('Finance_dashboard/load_postdated_cheque'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#postdatedcheque_div").html(data);
            }, error: function () {

            }
        });
    }
</script>
