<?php echo head_page($_POST["page_name"], true);
$this->load->helper('buyback_helper');

$yearfilter = load_yearfilter_dashboard();
$financeyear_arr = all_financeyear_drop();
$companyFinanceYearID = $this->common_data['company_data']['companyFinanceYearID'];
?>
    <div id="filter-panel" class="collapse filter-panel">
        <?php echo form_open('login/loginSubmit', ' name="buyback_monthly_Summary" id="buyback_monthly_Summary" class="form-horizontal" role="form"'); ?>
        <div class="col-md-12" style="margin-bottom: 2%; margin-left: -7%">
            <label for="inputData" class="col-md-2 control-label" >Financial Year :</label>
            <div class="col-md-2">
                <div class="form-group">
                    <?php echo form_dropdown('financeyear', $financeyear_arr, $companyFinanceYearID, 'class="form-control" id="financeyear" onchange="getMonthlySummary()" required'); ?>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>

    <div id="div_monthly_report">
    </div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/buyback/report/monthly_summary','', '<?php echo $_POST["page_name"] ?>')
            });
            $('.select2').select2();
            getMonthlySummary();
        });

        function getMonthlySummary() {
            var data = $("#buyback_monthly_Summary").serialize();
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('buyback/get_buyback_monthly_summary') ?>",
                data: data,
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_monthly_report").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateReportPdf() {
            var form = document.getElementById('buyback_monthly_Summary');
            form.target = '_blank';
            form.action = '<?php echo site_url('buyback/get_buyback_monthly_summary_pdf'); ?>';
            form.submit();
        }

    </script>



<?php
/**
 * Created by PhpStorm.
 * User: l
 * Date: 4/4/2019
 * Time: 9:54 AM
 */