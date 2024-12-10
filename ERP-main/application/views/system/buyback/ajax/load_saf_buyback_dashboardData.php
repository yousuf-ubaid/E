<?php
$current_date = current_format_date();
$companyID = $this->common_data['company_data']['company_id'];
$date_format_policy = date_format_policy();
$this->load->helper('buyback_helper');
?>
<style>
    ::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        box-shadow: inset 0 0 5px grey;
        border-radius: 10px;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #aeaeae;
        border-radius: 10px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #AB6C82;
    }

    /* .datepicker-inline {
        min-width: 100%;
        max-width: 100%;
        width: 100%;
        border: none;
    }

   .datepicker {
        background-color: inherit;
        color: #ffd485;
    }
    .datepicker table tr th{
        color: #fefefe;
        background-color: #00c56a;
    }
    .datepicker table th:hover{
        background-color: #8ae98b !important;
    }
    .datepicker table tr td:hover{
        color: #000;
        background-color: #00944b !important;

    }
    .datepicker table tr td.active.day{
        color: #000;
        background-color: #007d3e !important;
    }
    .datepicker table tr td.new.day{
        color: #585858 !important;
    }
    .datepicker table tr td.old.day{
        color: #585858 !important;
    }
*/

    .wipAmount_tble  tr{
        background-color: #FCBB60;
      //  background-color: #719e1f;
        height: 60px;
        color: white;
        font-size: 35px;
        text-align: center;
        font-family: sans-serif;
    }
</style>
    <script type="text/javascript" src="<?php echo base_url('plugins/highchart/highcharts-more.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('plugins/highchart/modules/solid-gauge.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/morris/morris.min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/community_ngo/raphael-min.js'); ?>"></script>
    <script src="<?php echo base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/morris/morris.css'); ?>">

    <?php
    if($theme == 1){ ?>
        <style>
            .fcrtext{
                color: black;
            }
            .BatchStatusDetails{
                background-color: white;
            }
            .farmLog_heading{
                color: black;
            }
            .calanderField{
                background-color: white;
                color: black;
            }
            .calenderTitle{
                background-color: white;
                color: black;
            }
            .calenderDetails{
                background-color: white;
                color: black;
            }
            .calculateFeed{
                background-color: white;
                color: black;
            }
            .datepicker table th{
                background-color: white;
            }
        </style>
    <?php } else if($theme == 2){ ?>
        <style>
            .age{
                background-color: #2a2a2b;
                color: white;
            }
            .BatchStatusDetails{
                background-color: #2a2a2b;
                color: white;
            }
            .calanderField{
                background-color: #2a2a2b;
                color: white;
            }
            .calenderTitle{
                background-color: #2a2a2b;
                color: #E0E0E3;
            }
            .calenderDetails{
                background-color: #2a2a2b;
                color: #E0E0E3;
            }
            .calculateFeed{
                background-color: #2a2a2b;
                color: #E0E0E3;
            }
            .datepicker table th{
                background-color: #2a2a2b;
            }
            .fcrtext{
                color: white;
            }
            .farmLog_heading{
                color: #E0E0E3;
            }

            th {
                background-color: black;
                color: white;
            }

            .table-striped > tbody > tr:nth-child(2n) > td, .table-striped > tbody > tr:nth-child(2n) > th {
                background-color: #2a2a2b;
                color: white;
            }
            .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
                background-color:  #2a2a2b;
                color: white;
            }
            .highcharts-background {
                fill: #2a2a2b;
                font-family: sans-serif;
            }
        </style>
   <?php } ?>

    <div class="col-md-12" >
        <div class="col-md-1" >
        </div>
        <div class="col-md-12">
            <div class="col-md-10 table-responsive" style="padding-top:20px; padding-bottom: 20px">
                <div id="weeklyReportBarChart" style="min-width: 400px; height: 300px; margin: 0 auto"></div>
            </div>
            <div class="col-md-2" style="padding-top:5%; padding-bottom: 20px ">
                <div class="table-responsive" style="background-color: #d6d6d6; min-width: 100px; height: 155px; margin: 0 auto; text-align: center !important;">
                    <div style=" color: #685D79; padding-top: 2%;">
                        <span style="font-size: large; font-weight: bold">Input</span>
                        <br>
                        <span style="font-size: larger; font-weight: bolder"><?php echo array_sum($columnChicks) ?></span>
                    </div>
                    <div style=" color: #AB6C82; padding-top: 2%;">
                        <span style="font-size: large; font-weight: bold">Output</span>
                        <br>
                        <span style="font-size: larger; font-weight: bolder"><?php echo array_sum($columnLiveBirds) ?></span>
                    </div>
                    <div style=" color: #D8737F; padding-top: 2%;">
                        <span style="font-size: large; font-weight: bold">Mortality</span>
                        <br>
                        <span style="font-size: larger; font-weight: bolder"><?php echo array_sum($columnMortal) ?></span>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <div class="col-md-12" style="padding-top: 5px">
        <div class="col-md-12 table-responsive">
            <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top:20px; padding-bottom: 20px ">
                <div id="weightFeedContainer" style=" height: 450px; margin: 0 auto"></div>
            </div>
        </div>
    </div>
    <div class=" col-md-12" style="margin-bottom: 20px">
        <div class="col-md-6">
            <div style="border: 1px solid rgba(158, 158, 158, 0.24); height: 420px;  padding: 2%">
                <table id="fcr_tbl" class="" style="width: 100%; margin-top: 2%; border: none;  box-shadow: 0px 2px 2px 0px #807979">
                    <thead style="border: none">
                    <tr style="height: 25px; font-size: 20px; background-color: #AB6C82; color: white; text-align: center">
                        <td colspan="3">Mortality Percentage</td>
                    </tr>
                    <tr style="height: 1px">
                    </tr>
                    <tr style=" background-color: #AB6C82; height: 25px; color: white; text-align: center">
                        <td width="33%">Yearly</td>
                        <td width="33%">Monthly</td>
                        <td width="33%">Today</td>
                    </tr>
                    <tr style="background-color: #AB6C82; height: 20px; color: white; font-size: 20px; text-align: center">
                        <?php
                        if($MortalityPercentage){
                            foreach ($MortalityPercentage as $val){?>
                                <td style="font-size: 25px;"><b><?php echo $val ?></b>%</td>
                            <?php   }
                        }
                        ?>
                    </tr>
                    </thead>
                </table>
                <table id="fcr_tbl" class="" style="width: 100%; margin-top: 10%; border: none;  box-shadow: 0px 2px 2px 0px #807979">
                    <thead style="border: none">
                    <tr style="height: 25px; font-size: 20px; background-color: #D8737F; color: white; text-align: center">
                        <td colspan="3">Overall FCR</td>
                    </tr>
                    <tr style="height: 1px">
                    </tr>
                    <tr style=" background-color: #D8737F; height: 25px; color: white; text-align: center">
                        <td width="33%">Yearly</td>
                        <td width="33%">Monthly</td>
                        <td width="33%">Today</td>
                    </tr>
                    <tr style="background-color: #D8737F; height: 20px; color: white; font-size: 20px; text-align: center">
                        <?php
                        if($feedRate){
                            foreach ($feedRate as $val){?>
                                <td style="font-size: 25px;"><b><?php echo $val ?></b></td>
                            <?php   }
                        }
                        ?>
                    </tr>
                    </thead>
                </table>
                <table id="" class="wipAmount_tble" style="width: 100%; margin-top: 10%;  box-shadow: 0px 2px 2px 0px #807979">
                    <thead style="border: none">
                    <tr style="height: 25px; font-size: 20px">
                        <td>Overall WIP Amount</td>
                    </tr>
                    <tr style="height: 1px">
                    </tr>
                    <tr>
                        <td class="pull-right" style="padding-right: 10px; font-size: 35px;"><b><?php echo $WIPAmount; ?></b></td>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div style="padding:5px; border: 1px solid rgba(158, 158, 158, 0.24); height: 420px">
                <div style="padding: 0px" class="col-sm-12 yearlyProfitChart" id="yearlyProfitChart">
                </div>
            </div>
        </div>
    </div>
    <div class=" col-md-12" style="margin-bottom: 20px">
        <div class="col-md-6">
            <div style="padding:5px; border: 1px solid rgba(158, 158, 158, 0.24); height: 430px">
                <div id="BatchStatus"></div>
                <div style="padding: 0px" class="col-sm-12 BatchStatusDetails">

                    <div class="col-sm-4">
                        <li class="fa fa-circle-o" style="width: 80px; color:#AB6C82;">&nbsp <b>Input:</b></li><span><input class="BatchStatusDetails" id="InputTotal" value="" style="border: none;font-weight: bold; width: 50px; text-align: center" disabled></span>
                    </div>
                    <div class="col-sm-4">
                        <li class="fa fa-circle-o" style="width: 80px; color:#D8737F;">&nbsp <b>Output:</b></li><span><input class="BatchStatusDetails" id="OutputTotal" value="" style="border: none;font-weight: bold; width: 50px; text-align: center" disabled></span>
                    </div>
                    <div class="col-sm-4">
                        <li class="fa fa-circle-o" style="width: 80px; color:#FCBB60;"> <b>Mortality:</b></li><span><input class="BatchStatusDetails" id="MortalTotal" value="" style="border: none;font-weight: bold; width: 50px; text-align: center" disabled></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div style="border: 1px solid rgba(158, 158, 158, 0.24); height: 430px">
                <div class="box-header with-border">
                        <div class="col-sm-6">
                            <h3 class="box-title farmLog_heading">Farm Log</h3>
                        </div>
                        <label>Filter Age : &nbsp </label>
                        <input class="age text-center" id="ageFrom" name="ageFrom" placeholder="From" style="width: 45px; border-radius: 2px; border: 1px solid rgba(60, 60, 60, 0.55);" autocomplete="off">
                        <label class="">-</label>
                        <input class="age text-center" id="ageTo" name="ageTo" placeholder="To" style="width: 45px; border-radius: 2px; border: 1px solid rgba(60, 60, 60, 0.55);" autocomplete="off">
                        <button type="button" class=" btn-xs" style="background-color: transparent; color: #00a5e6; border: none"
                                onclick="tableData()">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                        <button type="button" class=" btn-xs" style="background-color: transparent; color: #00a5e6; border: none"
                                    onclick="CleartableData()">
                            <i class="fa fa-paint-brush" aria-hidden="true"></i></i>
                        </button>
                    </div>
                <div class="box-body table-responsive" style="height: 350px; overflow: auto; margin-top: 5px">
                        <table id="tabledata" class="<?php echo table_class(); ?>" style="padding-top: 5px; border: 1px solid rgba(202,202,202,0.24);">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 45%">Farm</th>
                                <th style="min-width: 45%">Batch</th>
                                <th style="min-width: 20%;">Age</th>
                                <th style="min-width: 30%">Input</th>
                                <th style="min-width: 30%;">Output</th>
                                <th style="min-width: 30%;">Balance</th>
                            </tr>
                            </thead>
                            <tbody id="farmlog_details"></tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
    <div class=" col-md-12" style="margin-bottom: 20px">
        <div class="col-md-6" >
            <div style="border: 1px solid rgba(158, 158, 158, 0.24); height: 450px; padding: 2%">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Overdue Payables</a>
                        </li>
                        <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Overdue Receivables</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active table-responsive" id="tab_1" style="height: 350px; overflow: auto;">
                            <table id="overdue_payable" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 48%">Farm</th>
                                    <th style="min-width: 48%">Currency</th>
                                    <th style="min-width: 15%">Amount</th>
                                </tr>
                                </thead>
                                <tbody id="overdue_payable_details"></tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_2" style="height: 350px; overflow: auto;">
                            <table id="overdue_receivable" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 48%">Name</th>
                                    <th style="min-width: 48%">Currency</th>
                                    <th style="min-width: 15%">Amount</th>
                                </tr>
                                </thead>
                                <tbody id="overdue_receiveable_details"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 ">
            <div style="border: 1px solid rgba(158, 158, 158, 0.24); height: 450px;  padding: 2%">
                <div class="box-header with-border">
                    <h4 class="box-title">Item Master</h4>
                </div>
                <div class="box-body table-responsive" style="height: 380px; overflow: auto;">
                    <table id="itemTableBBDashboard" class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th style="min-width: 45%">Item Description</th>
                            <th style="min-width: 25%">UOM</th>
                            <th style="min-width: 30%;">Current Stock</th>
                        </tr>
                        </thead>
                        <tbody id="item_table"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function get_calDateformat() {
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        }
        function convertDate(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth()+1)).slice(-2),
                day  = ("0" + date.getFullYear()).slice(-4);
            var formatDate = [ date.getDate(), mnth, day ].join("-");

            $("#DashCalendarPick").val(formatDate);
        }

        $(function () {
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            tableData();
            ItemMasterTableData();
            load_overdue_payable();
            load_overdue_receivable();

            <?php if ($columnChart) { ?>
            Highcharts.chart('weeklyReportBarChart', {
                <?php  if($theme == 2){ ?>

                colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
                    '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
                legend: {
                    itemStyle: {
                        color: '#E0E0E3',
                    }
                },
                <?php } else{?>
                colors: ['#685D79', '#AB6C82', '#D8737F', '#FCBB60', '#685D79', '#AB6C82', '#D8737F', '#FCBB60','#685D79', '#AB6C82', '#D8737F', '#FCBB60'],
                <?php }?>
                title: {
                    text: 'Monthly Report',
                    style: {
                        color:
                            <?php  if($theme == 1){
                                ?>'black'<?php
                        }
                        else {
                        ?>  '#E0E0E3',
                        fontSize: '20px'<?php
                        } ?>
                        ,
                    }
                },
                xAxis: {
                    <?php  if($theme == 2){
                    ?>
                    labels: {
                        style: {
                            color: '#E0E0E3'
                        }
                    },
                    <?php } ?>
                    categories: [
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec'
                    ],
                    crosshair: true
                },
                yAxis: {
                    <?php  if($theme == 2){
                    ?>
                    labels: {
                        style: {
                            color: '#E0E0E3'
                        }
                    },
                    <?php } ?>
                    min: 0,
                    title: {
                        text: 'Amount'
                    }
                },
                tooltip: {
                    <?php  if($theme == 2){ ?>
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    style: {
                        color: '#F0F0F0'
                    },
                    <?php } ?>
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: false,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    type: 'column',
                    name: 'Input',
                    data: [
                        <?php foreach ($columnChicks as $val){
                        echo $val . ',';
                        } ?>
                    ]
                }, {
                    type: 'column',
                    name: 'Output',
                    data: [<?php foreach ($columnLiveBirds as $val){
                        echo $val . ',';
                    } ?>]
                }, {
                    type: 'column',
                    name: 'Mortality',
                    data: [<?php foreach ($columnMortal as $val){
                        echo $val . ',';
                        } ?>]
                },{
                    type: 'spline',
                    name: 'Input',
                    data: [
                        <?php foreach ($columnChicks as $val){
                        echo $val . ',';
                        } ?>
                    ],
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                }, {
                    type: 'spline',
                    name: 'Output',
                    data: [<?php foreach ($columnLiveBirds as $val){
                        echo $val . ',';
                        } ?>
                    ],
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                }, {
                    type: 'spline',
                    name: 'Mortality',
                    data: [<?php foreach ($columnMortal as $val){
                        echo $val . ',';
                        } ?>
                    ],
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                }]
            });
      <?php  } ?>

            Highcharts.chart('weightFeedContainer', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: 'Feed And Weight of Farm Batches (<?php echo $month ?>)'
                },

                xAxis: [{
                    reversed: false,
                    labels: {
                        step: 1
                    }
                }, { // mirror axis on right side
                    opposite: true,
                    reversed: false,
                    linkedTo: 0,
                    labels: {
                        step: 1
                    }
                }],
                yAxis: {
                    labels: {
                        formatter: function () {
                            return Math.abs(this.value);
                        }
                    },
                   /* plotLines: [{
                        color: '#FF0000',
                        width: 2,
                        value: 4,
                        zIndex: 5
                    }],
                   */
                    plotLines: [{
                        color: '#FF0000',
                        width: 1,
                        dashStyle: 'dash',
                        value: <?php echo ($feedPlotline *(-1)) ?>,
                        zIndex: 5
                    }, {
                        color: '#FF0000',
                        width: 1,
                        dashStyle: 'dash',
                        value: <?php echo $weightPlotline ?>,
                        zIndex: 5
                    }]
                },

                plotOptions: {
                    series: {
                        stacking: 'normal'
                    }
                },

                tooltip: {
                    headerFormat: '',
                    pointFormat: 'Farm:{point.name}<br>Batch:{point.batch}'
                },

                series: [{
                    name: 'Feed',
                    color: '#685D79',
                    data:  [<?php
                        if(!empty($sactterFeildReport)){

                        foreach ($sactterFeildReport as $buy) { ?>
                        {"name":"<?php echo $buy['description'] ?>","batch":"<?php echo $buy['batchCode']; ?> <br> Visit Code: <?php echo $buy['documentSystemCode'] ?> <br> Visit No: <?php echo $buy['numberOfVisit'] ?> <br> Feed: <?php echo ($buy['avgFeedperBird']) ?>", "y":<?php if(!empty($buy['avgFeedperBird'])){ echo ($buy['avgFeedperBird'] *(-1)); } else{ echo 0; } ?>},

                        <?php  }
                        }
                        ?>],
                }, {
                    name: 'Weight',
                    color: '#AB6C82',
                    data:  [<?php
                        if(!empty($sactterFeildReport)){

                        foreach ($sactterFeildReport as $buy) { ?>
                        {"name":"<?php echo $buy['description'] ?>","batch":"<?php echo $buy['batchCode']; ?> <br> Visit Code: <?php echo $buy['documentSystemCode'] ?> <br> Visit No: <?php echo $buy['numberOfVisit'] ?>  <br> Weight: <?php echo $buy['avgBodyWeight'] ?>","y":<?php if(!empty($buy['avgBodyWeight'])){ echo $buy['avgBodyWeight']; } else{ echo 0; } ?>},

                        <?php  }
                        }
                        ?>],
                }]

            });

            <?php
            if(!empty($totalChicksCount)) {
                $occ1Pecrnt = round((($input_chicks / $totalChicksCount) * 100), 0);
                $occ2Pecrnt = round((($output_chicks / $totalChicksCount) * 100), 0);
                $occ3Pecrnt = round((($mortality_chicks / $totalChicksCount) * 100), 0);
            } else{
                $occ1Pecrnt = 0;
                $occ2Pecrnt = 0;
                $occ3Pecrnt = 0;
            } ?>

            var charcolor = {
                type: 'solidgauge',
                marginTop: 30,
            }
            var chartype = {
                type: 'solidgauge',
                marginTop: 30,
                height: 380,
                fontFamily: 'serif',
            }
            var chartitle = {
                text: 'Batch Status (<?php echo $month ?>)',
                style: {
                    color:
                        <?php  if($theme == 1){
                            ?>'black'<?php
                    }
                    else {
                    ?>
                    '#E0E0E3',
                    fontSize: '20px'<?php
                    } ?>
                    ,
                    fontSize: '18px'
                }
            }
            var chartooltip = {
                borderWidth: 0,
                backgroundColor: 'none',
                shadow: false,
                style: {
                    fontSize: '14px'
                },
                pointFormat: '<span class="batchstatus text-center" style=" font-size:1em; color: {point.color};">{series.name}</span><br><span class="text-center" style="font-size:2.2em; color: {point.color}; font-weight: bold">{point.y}%</span>',
                positioner: function (labelWidth, labelHeight) {
                    return {
                        x: 255 - labelWidth / 2,
                        y: 170
                    };
                }
            }
            var chartpane = {
                startAngle: 0,
                endAngle: 360,
                background: [{ // Track for Move
                    outerRadius: '102%',
                    innerRadius: '78%',
                    backgroundColor: Highcharts.Color('#685D79').setOpacity(0.3).get(),
                    borderWidth: 0
                }, { // Track for Exercise
                    outerRadius: '77%',
                    innerRadius: '53%',
                    backgroundColor: Highcharts.Color('#AB6C82').setOpacity(0.3).get(),
                    borderWidth: 0
                }, { // Track for Stand
                    outerRadius: '52%',
                    innerRadius: '28%',
                    backgroundColor: Highcharts.Color('#D8737F').setOpacity(0.3).get(),
                    borderWidth: 0
                }]
            }
            var chartyaxis = {
                min: 0,
                max: 100,
                lineWidth: 0,
                tickPositions: []
            }
            var chartplotOptions= {
                solidgauge: {
                    borderWidth: '33px',
                    dataLabels: {
                        enabled: false
                    },
                    linecap: 'round',
                    stickyTracking: false
                }
            }
            var chartseries = [{
                name: 'Input',
                <?php  if($theme == 1){
                    ?>borderColor: '#AB6C82',<?php
                }
                else {
                ?>
                borderColor: '#2b908f',
                <?php
                } ?>
                data: [{
                   <?php  if($theme == 1){
                        ?>color: '#AB6C82',<?php
                        }
                        else {
                        ?>
                color: '#2b908f',
                    <?php
                    } ?>
                   //
                    radius: '90%',
                    innerRadius: '90%',
                    y: <?php echo $occ1Pecrnt; ?>
                }]
            }, {
                name: 'Output',
                <?php  if($theme == 1){
                    ?>borderColor: '#D8737F',<?php
                }
                else {
                ?>
                borderColor: '#90ee7e',
                <?php
                } ?>
                data: [{
                    <?php  if($theme == 1){
                        ?>color: '#D8737F',<?php
                    }
                    else {
                    ?>
                    color: '#90ee7e',
                    <?php
                    } ?>
                    radius: '65%',
                    innerRadius: '65%',
                    y: <?php echo $occ2Pecrnt; ?>
                }]
            }, {
                name: 'Mortality',
                <?php  if($theme == 1){
                    ?>borderColor: '#FCBB60',<?php
                }
                else {
                ?>
                borderColor: '#f45b5b',
                <?php
                } ?>
                data: [{
                    <?php  if($theme == 1){
                        ?>color: '#FCBB60',<?php
                    }
                    else {
                    ?>
                    color: '#f45b5b',
                    <?php
                    } ?>
                    radius: '40%',
                    innerRadius: '40%',
                    y: <?php echo $occ3Pecrnt; ?>
                }]
            }]
            Highcharts.chart('BatchStatus', {
                    colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
                        '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],

                    chart:chartype,
                    title: chartitle,
                    tooltip: chartooltip,
                    pane:chartpane,
                    yAxis: chartyaxis,
                    plotOptions:chartplotOptions,
                    series: chartseries
                },
                /**
                 * In the chart load callback, add icons on top of the circular shapes
                 */
                function callback() {
                });
            document.getElementById('InputTotal').value =  '<?php echo $occ1Pecrnt; ?>%';
            document.getElementById('OutputTotal').value = '<?php echo $occ2Pecrnt; ?>%';
            document.getElementById('MortalTotal').value = '<?php echo$occ3Pecrnt; ?>%';

            <?php if ($AreaChart) { ?>
            Highcharts.chart('yearlyProfitChart', {
                chart: {
                    type: 'area'
                },
                colors: ['#685D79', '#FCBB60', '#AB6C82', '#D8737F', '#685D79', '#AB6C82', '#D8737F', '#FCBB60','#685D79', '#AB6C82', '#D8737F', '#FCBB60'],
                title: {
                    text: 'Profit Batch Comparison'
                },
                xAxis: {
                    title: {
                        text: 'Months'
                    },
                    <?php  if($theme == 2){
                    ?>
                    labels: {
                        style: {
                            color: '#E0E0E3'
                        }
                    },
                    <?php } ?>
                    categories: [
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec'
                    ],
                    crosshair: true
                },
                yAxis: {
                    title: {
                        text: 'Amount'
                    },
                    labels: {
                    }
                },
                tooltip: {
                    pointFormat: '{series.name} <b>{point.y:,.0f}</b><br/> Profit Batches'
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'This Year',
                    data: [
                        <?php
                            foreach ($areaThisYear as $val){
                                echo $val . ',';
                            }
                        ?>
                    ]
                },{
                    name: 'Last Year',
                    data: [
                        <?php
                        foreach ($areaLastYear as $val){
                            echo $val . ',';
                        }
                        ?>
                    ]
                }]
            });
            <?php  } ?>
        });


        function load_overdue_payable(){
            $.ajax({
                async: true,
                type: "POST",
                data: {type: 'payable'},
                url: "<?php echo site_url('BuybackDashboard/fetch_overdue_payable_farm') ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#overdue_payable_details").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        }

        function load_overdue_receivable(){
            $.ajax({
                async: true,
                type: "POST",
                data: {type: 'receivable'},
                url: "<?php echo site_url('BuybackDashboard/fetch_overdue_payable_farm') ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#overdue_receiveable_details").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        }

        function CleartableData() {
            $('#ageFrom').val('');
            $('#ageTo').val('');
            tableData();
        }

        function tableData() {
            var ageFrom = $('#ageFrom').val();
            var ageTo = $('#ageTo').val();

            $.ajax({
                async: true,
                type: "POST",
                data: {'ageFrom': ageFrom, 'ageTo': ageTo},
                url: "<?php echo site_url('BuybackDashboard/fetch_FarmLog') ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#farmlog_details").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }

        function ItemMasterTableData() {
            $.ajax({
                async: true,
                type: "POST",
                url: "<?php echo site_url('BuybackDashboard/fetch_buyback_item') ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                   // alert(data);
                    stopLoad();
                    $("#item_table").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
        }

        $('#overdue_payable, #overdue_receivable, #itemTableBBDashboard, #tabledata').tableHeadFixer({
            head: true,
            foot: false,
            left: 0,
            right: 0,
            'z-index': 10
        });

    </script>

<?php
