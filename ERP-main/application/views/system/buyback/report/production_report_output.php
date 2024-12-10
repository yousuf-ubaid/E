<?php echo head_page($_POST["page_name"], true);
$this->load->helper('buyback_helper');

$yearfilter = load_yearfilter_dashboard();
$itemFilter = load_productionStatement_itemTypes();
$financeyear_arr = all_financeyear_drop();
$companyFinanceYearID = $this->common_data['company_data']['companyFinanceYearID'];
?>
    <div id="filter-panel" class="collapse filter-panel">
        <?php echo form_open('login/loginSubmit', ' name="Production_Report" id="Production_Report" class="form-horizontal" role="form"'); ?>
        <div class="col-md-12" style="margin-bottom: 2%">
            <label for="inputData" class="col-md-1 control-label">Items :</label>
            <div class="col-md-2">
                <?php echo form_dropdown('buybackItemID[]', $itemFilter, '', 'multiple  class="form-control" id="buybackItemID" onchange="get_Production_Report()"'); ?>
            </div>

            <label for="inputData" class="col-md-2 control-label">Financial Year :</label>
            <div class="col-md-2">
                <div class="form-group">
                    <?php echo form_dropdown('financeyear', $financeyear_arr, $companyFinanceYearID, 'class="form-control" id="financeyear" onchange="get_Production_Report()" required'); ?>
                </div>
            </div>

        </div>
        <?php echo form_close(); ?>
    </div>

    <div id="div_production_report">
    </div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/buyback/report/production_report_output','', '<?php echo $_POST["page_name"] ?>')
            });
            $('#buybackItemID').multiselect2({
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true,
                selectAllValue: 'select-all-value',
                //enableFiltering: true
                buttonWidth: 150,
                maxHeight: 200,
                numberDisplayed: 1
            });
            $("#buybackItemID").multiselect2('selectAll', false);
            $("#buybackItemID").multiselect2('updateButtonText');

            $('.select2').select2();
            get_Production_Report();


        });

        function get_Production_Report() {
            var data = $("#Production_Report").serialize();
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('buyback/get_Production_Report') ?>",
                data: $("#Production_Report").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_production_report").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateReportPdf() {
            var form = document.getElementById('Production_Report');
            form.target = '_blank';
            form.action = '<?php echo site_url('buyback/get_Production_Report_pdf'); ?>';
            form.submit();
        }

    </script>

