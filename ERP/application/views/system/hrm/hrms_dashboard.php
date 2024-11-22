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
            <div id="headcount_div"></div>
        </div>
        <div class="col-md-6">
            <div id="designationhead_div"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="payrollcost_div"></div>
        </div>
        <div class="col-md-6">
            <div id="birthdayreminder_div"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        load_head_count();
        load_designation_head_count();
        load_payroll_cost();
        load_birthday_reminder();
    });

    function filter() {
        load_head_count();
        load_designation_head_count();
        load_payroll_cost();
        load_birthday_reminder();
    }

    function load_head_count() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period0').val(),userDashboardID:0},
            url: "<?php echo site_url('Finance_dashboard/load_head_count'); ?>",
            beforeSend: function () {
                $("#overlay150").show();
            },
            success: function (data) {
                $("#headcount_div").html(data);
                $("#overlay150").hide();
            }, error: function () {

            }
        });
    }

    function load_designation_head_count() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period0').val(),userDashboardID:0},
            url: "<?php echo site_url('Finance_dashboard/load_Designation_head_count'); ?>",
            beforeSend: function () {
                $("#overlay160").show();
            },
            success: function (data) {
                $("#designationhead_div").html(data);
                $("#overlay160").hide();
            }, error: function () {

            }
        });
    }

    function load_payroll_cost() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period0').val(),userDashboardID:0},
            url: "<?php echo site_url('Finance_dashboard/load_payroll_cost'); ?>",
            beforeSend: function () {
                $("#overlay170").show();
            },
            success: function (data) {
                $("#payrollcost_div").html(data);
                $("#overlay170").hide();
            }, error: function () {

            }
        });
    }

    function load_birthday_reminder() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period0').val(),userDashboardID:0},
            url: "<?php echo site_url('Finance_dashboard/birthdayReminder'); ?>",
            beforeSend: function () {
                $("#overlay1190").show();
            },
            success: function (data) {
                $("#birthdayreminder_div").html(data);
                $("#overlay1190").hide();
            }, error: function () {

            }
        });
    }
</script>
