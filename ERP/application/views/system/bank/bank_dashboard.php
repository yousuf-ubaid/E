<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard', $primaryLanguage);
?>
<div class="row" style="margin-top: 5px">
    <div class="col-md-6">
        <div id="financialposition_div"></div>
    </div>
    <div class="col-md-6">
        <div id="postdatedcheque_div"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        load_financial_position();
        load_postdated_cheque();
    });

    function load_financial_position() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:0},
            url: "<?php echo site_url('Finance_dashboard/load_financial_position'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#financialposition_div").html(data);
            }, error: function () {

            }
        });
    }

    function load_postdated_cheque() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:0},
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
