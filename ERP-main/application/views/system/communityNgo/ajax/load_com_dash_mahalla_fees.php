<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<style>

    #profileInfoTable tr td:first-child {
        color: #095db3;
    }

    #profileInfoTable tr td:nth-child(2) {
        /* font-weight: bold;*/
    }

    #recordInfoTable tr td:first-child {
        color: #095db3;
    }

    #recordInfoTable tr td:nth-child(2) {
        font-weight: bold;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #50749f;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #638bbe;
    }

    .textColor {
        color: #638bbe;
    }
</style>
<!-- Morris.js charts -->

<script src="<?php echo base_url('plugins/morris/morris.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/community_ngo/raphael-min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/morris/morris.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/daterangepicker/daterangepicker-bs3.css'); ?>">

<?php
if (!empty($master)) {

    ?>

    <div class="no_padding col-md-9" style="padding: 2px;">
        <div class="col-md-6 col-sm-6 col-xs-12" style="padding:10px;">

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">

                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_lastSevenDaysCollection');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">

                        <?php if(!empty($loadWeekFee)){ ?>
                            <div class="chart">
                                <canvas id="weeklyFeeData" style=""></canvas>
                            </div>
                        <?php }else{ ?>

                            <div class="well well-sm" style="margin-bottom: 0px; text-align: center;">
                                <font style="color: darkgrey;"><?php echo $this->lang->line('comNgo_dash_lastSevenDays_noCollection');?> </font>
                            </div>

                        <?php } ?>
                    </div></div></div>

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">

                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_collectionStatus');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">
                        <!-- THE Status -->
                        <div class="chart-responsive">
                            <div class="chart" id="donut_forFee" style="height: 250px; position: relative;"></div>
                        </div>
                        <div>
                            <li class="fa fa-circle-o text"> <?php echo $this->lang->line('comNgo_dash_invoices');?>:</li> <span><input id="invoiceVal" value="" style="border: none;font-weight: bold;"></span>
                            <br>
                            <li class="fa fa-circle-o text-green"> <?php echo $this->lang->line('comNgo_dash_total_cr');?>:</li> <span><input id="crVal" value="" style="border: none;font-weight: bold;"></span>
                            <br>
                            <li class="fa fa-circle-o text-blue"> <?php echo $this->lang->line('comNgo_dash_total_dr');?>:</li> <span><input id="drVal" value="" style="border: none;font-weight: bold;"></span>
                        </div>


                        <!-- /.row -->

                    </div></div></div>

        </div>

        <div class="col-md-6 col-sm-6 col-xs-12" style="padding:10px;">

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">

                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_todayFeeCollections');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">

                        <?php if(!empty($todayFeeDel)){ ?>
                            <div class="chart">
                                <canvas id="todayFeeData" style=""></canvas>
                            </div>
                        <?php }else{ ?>

                            <div>
                                <font style="color: darkgrey;"><?php echo $this->lang->line('comNgo_dash_no_collections');?> </font>
                            </div>

                        <?php } ?>

                    </div></div></div>

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">

                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_paymentsHistory');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">
                        <!-- THE History -->
                        <div class="well well-sm row-fluid" style="margin-bottom: 5px;">
                            <div class="form-group" style="padding: 3px;  margin-bottom: 0px;">
                                <label class="control-label" for="daterange-btn"><?php echo $this->lang->line('comNgo_dash_receiptDateRange');?>:</label>
                                <div class="input-group" style="width:100% !important; height:28px !important;">
                                    <button type="button" class="btn btn-default btn-xs btn-block btn-flat" id="daterange-btn" style="background-color: white; height:28px !important;">
                                        <span id="spanLid"></span>
                                        <i class="fa fa-caret-down pull-right" style="margin-top: 3px;"></i>
                                    </button>
                                </div>
                                <div id="receiptDateDiv" style="display:none;">
                                    <input type="text" name="dateFrom" id="dateFrom" value="">
                                    <input type="text" name="datesTo" id="datesTo" value="">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" style="height: 395px;">
                        <div id="paymentsDatatableDiv">
                        </div>
                        </div>

                    </div></div></div>
        </div>

    </div>

    <div class="col-md-3 col-sm-6" style=" padding: 10px; ">
        <!-- Balance-->
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
            <div class="box box-primary" style="margin-bottom: 12px;">

                <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_generalLedger');?></h3><div class="box-tools pull-right"></div></div>

                <div class="box-body">
                    <div class="ScrollDivNB" style="max-height:300px; overflow-y: auto; padding: 0px;">
                        <?php
                        //get details
                        $invoiceAmnt=0;
                        $PaidAmnt=0;
                        foreach($glSumFeeDel as $row_sub) {


                            $querySum = $this->db->query("SELECT  srp_erp_customerinvoicemaster.invoiceNarration,SUM(srp_erp_customerinvoicedetails.transactionAmount) AS transactionAmnt ,srp_erp_customermaster.communityMemberID FROM srp_erp_customerinvoicedetails
                            LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicedetails.invoiceAutoID=srp_erp_customerinvoicemaster.invoiceAutoID
                            LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
                            WHERE srp_erp_customerinvoicemaster.companyID = '".$row_sub['companyID']."' AND srp_erp_customermaster.communityMemberID IS NOT NULL AND invoiceNarration='".$row_sub['invoiceNarration']."' ");
                            $reSumFee = $querySum->row();

                            $queryCol = $this->db->query("SELECT  srp_erp_customerinvoicemaster.invoiceNarration,SUM(srp_erp_customerreceiptdetail.transactionAmount) AS PaidAmnt ,srp_erp_customermaster.communityMemberID FROM srp_erp_customerreceiptdetail
                            LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID=srp_erp_customerreceiptdetail.invoiceAutoID
                            LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
                            WHERE srp_erp_customerreceiptdetail.companyID = '".$row_sub['companyID']."' AND srp_erp_customermaster.communityMemberID IS NOT NULL AND invoiceNarration='".$row_sub['invoiceNarration']."' ");
                            $reColFee = $queryCol->row();

                            ?>
                            <div>
                                <div class="box box-info collapsed-box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"
                                            style="font-size: 12px;font-weight: bold;"><?php echo $row_sub['invoiceNarration']; ?></h3>

                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <!-- /.box-tools -->
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">

                                        <div id="glclassDatatableDiv" style="overflow-x:auto; margin: 1%;">
                                            <table id="glclassDatatables" class="nowrap" cellspacing="0" width="100%"
                                                   border="1"
                                                   style="border: 1px; border-collapse: collapse;font-size: 13px;">
                                                <thead>
                                                <tr>
                                                    <th>Dr</th>
                                                    <th>Cr</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td style="text-align: right;"><?php echo format_number($reSumFee->transactionAmnt, $this->common_data['company_data']['company_default_decimal']); ?></td>
                                                    <td style="text-align: right;"></td>
                                                </tr>
                                                <tr style="background-color:#cbf1ff;">
                                                    <td style="text-align: right;"></td>
                                                    <td style="text-align: right;"><?php echo format_number($reColFee->PaidAmnt, $this->common_data['company_data']['company_default_decimal']); ?></td>

                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                            <?php
                            $invoiceAmnt += $reSumFee->transactionAmnt;
                            $PaidAmnt += $reColFee->PaidAmnt;

                        }
                        ?>
                        <div class="box box-info collapsed-box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title" style="font-size: 12px;font-weight: bold;"><?php echo $this->lang->line('comNgo_dash_net_total');?></h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div id="glclsDTabDiv" style="overflow-x:auto; margin: 1%;">
                                    <table id="glclsDTabs" class="nowrap" cellspacing="0" width="100%" border="1"
                                           style="border: 1px; border-collapse: collapse;font-size: 13px;">
                                        <thead>
                                        <tr>
                                            <th>Dr</th>
                                            <th>Cr</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td style="text-align: right;"><?php echo format_number($invoiceAmnt, $this->common_data['company_data']['company_default_decimal']); ?></td>
                                            <td style="text-align: right;"></td>
                                        </tr>
                                        <tr style="background-color:#cbf1ff;">
                                            <td style="text-align: right;"></td>

                                            <td style="text-align: right;"><?php echo format_number($PaidAmnt, $this->common_data['company_data']['company_default_decimal']); ?></td>
                                        </tr>
                                        </tbody>
                                        <?php $balanceAmnt=($invoiceAmnt-$PaidAmnt); ?>
                                        <tfoot><tr>
                                            <td style="text-align:right;"><strong><?php echo $this->lang->line('communityngo_balance');?> </strong></td>
                                            <td style="text-align: right;"><?php echo format_number($balanceAmnt, $this->common_data['company_data']['company_default_decimal']);?></td></tr>
                                        </tfoot>


                                    </table>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>

            </div></div>
        <!-- Fee Menus-->
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
            <div class="box box-primary" style="margin-bottom: 12px;">

                <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_contact');?></h3><div class="box-tools pull-right"></div></div>

                <div class="box-body">
                    <div class="table-responsive" style="height: 225px;">
                        <table class="table no-margin">
                            <thead>
                            <tr style="font-size: 13px;">
                                <th>#</th>
                                <th><?php echo $this->lang->line('communityngo_sender_message');?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                </div></div></div>

    </div>

    <?php
}
?>

<script>

    $(function() {

        var start=moment();
        var end =moment();

        function cb(start, end) {
            document.getElementById('dateFrom').value = start.format('YYYY-MM-DD');
            document.getElementById('datesTo').value = end.format('YYYY-MM-DD');
            $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

            getMahalla_payment_details();
        }

        $('#daterange-btn').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

    });

    function getMahalla_payment_details() {

        var date_from =document.getElementById('dateFrom').value;
        var date_To=document.getElementById('datesTo').value;

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('CommunityNgoDashboard/get_mahalla_paymentsInfo'); ?>",
            data: {'date_from':date_from,'date_To':date_To},
            success: function (data) {

                $('#paymentsDatatableDiv').html(data);

            }
        });

    }

</script>
<script type="text/javascript">

    //start weekly fee
    <?php if(!empty($loadWeekFee)){ ?>
    $(function () {
        //Weekly Fee Collection
        var areaChartDataFeeDel = {
            labels: [<?php foreach(array_reverse($loadWeekFee) as $row){echo "'".$row['RVdate']."'," ;} ?>],
            datasets: [
                {
                    label: "Week Collections",
                    fillColor: "rgba(60,141,188,0.6)",
                    strokeColor: "rgba(60,141,188,0.8)",
                    pointColor: "#3b8bba",
                    pointStrokeColor: "rgba(60,141,188,1)",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(60,141,188,1)",
                    data: [<?php foreach(array_reverse($loadPerWeekFee) as $row){echo "'".$row['recAmountPaid']."'," ;} ?>]
                }
            ]
        };

        var areaChartFeeDel = document.getElementById('weeklyFeeData').getContext('2d');
        new Chart(areaChartFeeDel).Line(areaChartDataFeeDel);


    });
    <?php } ?>

    //end of last seven details of fee
    <?php if(!empty($todayFeeDel)){ ?>
    $(function () {

        //Today Fee Collection
        var tdyFeeData = {

            labels: [<?php foreach(array_reverse($get_feeCatsList) as $resFee){ echo "'".$resFee['invoiceNarration']."',";} ?>],
            datasets: [
                {
                    label: "Today Fee Collections",
                    fillColor: "rgba(60,141,188,0.7)",
                    strokeColor: "rgba(60,141,188,0.7)",
                    pointColor: "#3b8bba",
                    pointStrokeColor: "rgba(60,141,188,0.7)",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(60,141,188,0.7)",
                    data: [<?php foreach(array_reverse($todayFeeDel) as $row){echo "'".$row['todayAmntPaid']."'," ;} ?>]
                }
            ]
        };

        var barChartTdyFee = $("#todayFeeData").get(0).getContext("2d");
        var barChart = new Chart(barChartTdyFee);
        var barChartTdyFeeData = tdyFeeData;

        var barChartTdyOptions = {
            responsive: true,
            maintainAspectRatio: true
        };

        barChartTdyOptions.datasetFill = false;
        barChart.Bar(barChartTdyFeeData, barChartTdyOptions);

    });
    <?php } ?>
    //end of Today Fee Collection

    //fee donut chart

    $(function () {

        <?php
        $crPecrnt = round((($PaidAmnt/$invoiceAmnt)*100),2);
        $drPecrnt = round((($balanceAmnt/$invoiceAmnt)*100), 2);

        ?>

        document.getElementById('invoiceVal').value =  '<?php echo format_number($invoiceAmnt, $this->common_data['company_data']['company_default_decimal']); ?>';
        document.getElementById('drVal').value = '<?php echo format_number($balanceAmnt, $this->common_data['company_data']['company_default_decimal']).' ('.$drPecrnt.'% )'; ?>';
        document.getElementById('crVal').value = '<?php echo format_number($PaidAmnt, $this->common_data['company_data']['company_default_decimal']).' ('.$crPecrnt.'% )'; ?>';
        //DONUT CHART
        var donut = new Morris.Donut({
            element: 'donut_forFee',
            resize: true,
            colors: ["#00a65a","#3c8dbc"],
            data: [
                {label: "Net Credit (%)", value: '<?php echo $crPecrnt; ?>'},
                {label: "Net Debit (%)", value: '<?php echo $drPecrnt; ?>'}

            ],
            hideHover: 'auto'
        });

    });

    //end of fee donut chart

</script>

<?php
