<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard', $primaryLanguage);
?>
<div class="dashboard-cus-select">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <select id="period0" class="dashboard-cus-select" onchange="filter()">
                    <?php
                    $company_type = $this->session->userdata("companyType");
                    if($company_type==1) {
                        $years = get_last_two_financial_year();
                    }else
                    {
                        $years = get_last_two_financial_year_group();
                    }
                    $countYears = count($years);
                    $i = 0;
                    if ($years) {
                        foreach ($years as $val) {
                            echo '<option value="' . $i . '">' . $val["beginingDate"] . "-" . $val["endingDate"] . '</option>';
                            $i++;
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="overallperformance_div" style="margin-top: 5px"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="performancesummary_div"></div>
        </div>
        <div class="col-md-6">
            <div id="revenuedetail_div"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        load_overall_performance();
        load_performance_summary();
        load_revenue_detail_analysis_by_glcode();
    });

    function filter() {
        load_overall_performance();
        load_performance_summary();
        load_revenue_detail_analysis_by_glcode();
    }

    function load_overall_performance() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {period: $('#period0').val(), userDashboardID: 0},
            url: "<?php echo site_url('Finance_dashboard/load_overall_performance'); ?>",
            beforeSend: function () {
                $("#overallperformance_div").show();
                $("#overlay10").show();
            },
            success: function (data) {
                $("#overallperformance_div").html(data);
                $("#overlay10").hide();
            }, error: function () {

            }
        });
    }

    function load_performance_summary() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {period: $('#period0').val(), userDashboardID: 0},
            url: "<?php echo site_url('Finance_dashboard/load_performance_summary'); ?>",
            beforeSend: function () {
                $("#overlay30").show();
            },
            success: function (data) {
                $("#performancesummary_div").html(data);
                $("#overlay30").hide();
            }, error: function () {

            }
        });
    }

    function load_revenue_detail_analysis_by_glcode() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period0').val(), userDashboardID: 0},
            url: "<?php echo site_url('Finance_dashboard/load_revenue_detail_analysis_by_glcode'); ?>",
            beforeSend: function () {
                $("#overlay100").show();
            },
            success: function (data) {
                $("#revenuedetail_div").html(data);
                $("#overlay100").hide();
            }, error: function () {

            }
        });
    }

</script>
