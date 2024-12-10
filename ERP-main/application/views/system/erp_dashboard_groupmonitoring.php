
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        font-size: 12px;
    }
    .pagination>li>a, .pagination>li>span {
        padding: 2px 8px;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<div id="dashboard_content">

</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<!--modal report-->
<script type="text/javascript">
    $(document).ready(function () {
        loadDashboard();
    });

    function loadDashboard() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Finance_dashboard/loadDashboard_groupmonitoring"); ?>',
            dataType: 'html',
            data: {},
            beforeSend: function () {
                startLoad();
            },
            success: function (page_html) {
                stopLoad();
                $('#dashboard_content').html(page_html);
                $("html, body").animate({scrollTop: "0px"}, 10);
                check_session_status();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                $("html, body").animate({scrollTop: "0px"}, 10);
                check_session_status();
            }
        });
    }
</script>