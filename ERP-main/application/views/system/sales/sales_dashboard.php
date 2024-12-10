<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard', $primaryLanguage);
?>
<div class="dashboard-cus-select">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <select id="period0" onchange="filter()">
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
    <div class="row" style="margin-top: 5px">
        <div class="col-md-6">
            <div id="saleslog_div"></div>
        </div>
        <div class="col-md-6">
            <div id="customerorder_div"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        load_sales_log();
        load_customer_order_analysis();
    });

    function filter() {
        load_sales_log();
        load_customer_order_analysis();
    }

    function load_sales_log() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {period: $('#period0').val(),userDashboardID:0},
            url: "<?php echo site_url('Finance_dashboard/load_sales_log'); ?>",
            beforeSend: function () {
                $("#overlay180").show();
            },
            success: function (data) {
                $("#saleslog_div").html(data);
                $("#overlay180").hide();
            }, error: function () {

            }
        });
    }

    function load_customer_order_analysis() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:0},
            url: "<?php echo site_url('Finance_dashboard/load_customer_order_analysis'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#customerorder_div").html(data);
            }, error: function () {

            }
        });
    }
</script>
