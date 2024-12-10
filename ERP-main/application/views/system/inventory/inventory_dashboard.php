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
    <div class="row" style="margin-top: 5px">
        <div class="col-md-6">
            <div id="fastmovingitem_div"></div>
        </div>
        <div class="col-md-6">
            <div id="avgpurchasing_div"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        load_fast_moving_item();
        //load_raw_materials_avg_purchase();
    });

    function filter() {
        //load_raw_materials_avg_purchase();
    }

    function load_fast_moving_item() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {period: $('#period0').val(),userDashboardID:0},
            url: "<?php echo site_url('Finance_dashboard/load_fast_moving_item'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#fastmovingitem_div").html(data);
            }, error: function () {

            }
        });
    }

    function load_raw_materials_avg_purchase() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period0').val(),userDashboardID:0},
            url: "<?php echo site_url('Finance_dashboard/load_raw_materials_avg_purchase'); ?>",
            beforeSend: function () {
                $("#overlay2050").show();
            },
            success: function (data) {
                $("#avgpurchasing_div").html(data);
                $("#overlay2050").hide();
            }, error: function () {

            }
        });
    }
</script>
