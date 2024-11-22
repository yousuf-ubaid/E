<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="box box-danger">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_revenue_detail_analysis');?><!--Revenue Detail Analysis--></h4>

        <div class="box-tools pull-right">
            <strong class="btn-box-tool"><?php echo $this->lang->line('common_currency');?><!--Currency--> : (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)</strong>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                    class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                    class="fa fa-times"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;height: 250px;overflow: auto "
         id="revenuedetailanalysis<?php echo $userDashboardID; ?>">
        <?php
        $color = '' ;
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        if (!empty($revenueDetailAnalysis)) {
            foreach ($revenueDetailAnalysis as $key => $val) {
                
                $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];

                $percentage = 0;
                if (!empty($totalRevenue)) {
                    $percentage = (($val["companyReportingAmount"] / $totalRevenue) * 100);
                } else {
                    $percentage = 0;
                }

                ?>
                <div class="progress-group">
                    <span class="progress-text"><?php echo $val["subCategory"] ?></span>
                    <span class="progress-number"><span
                            style="color: #4B94C0"><?php echo number_format($val["companyReportingAmount"], $val['companyReportingCurrencyDecimalPlaces']) ?></span> ► <b><span
                                class="text-orange"><?php echo round($percentage) . "%" ?></span></b></span>

                    <div class="progress sm">
                        <div class="progress-bar"
                             style="background-color: <?php echo $color ?>;width: <?php echo $percentage . "%" ?>;"></div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="overlay" id="overlay2<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>
