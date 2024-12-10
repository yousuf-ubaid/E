<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);
$segment = fetch_mfq_segment(true, false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>

<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/TabStylesInspiration/css/normalize.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/TabStylesInspiration/css/tabs.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/TabStylesInspiration/css/tabstyles.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/dhtmlxGantt/codebase/dhtmlxgantt.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/dhtmlxGantt/codebase/skins/dhtmlxgantt_broadway.css'); ?>" rel="stylesheet">
<script type="text/javascript"
        src="<?php echo base_url('plugins/TabStylesInspiration/js/modernizr.custom.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/TabStylesInspiration/js/cbpFWTabs.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dhtmlxGantt/codebase/dhtmlxgantt.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/dhtmlxGantt/codebase/ext/dhtmlxgantt_tooltip.js'); ?>"></script>
<style>

    .panel.with-nav-tabs .panel-heading {
        padding: 5px 5px 0 5px;
    }

    .panel.with-nav-tabs .nav-tabs {
        border-bottom: none;
    }

    .panel.with-nav-tabs .nav-justified {
        margin-bottom: -1px;
    }

    /********************************************************************/
    /*** PANEL SUCCESS ***/
    .with-nav-tabs.panel-success .nav-tabs > li > a,
    .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
        color: #3c763d;
    }

    .with-nav-tabs.panel-success .nav-tabs > .open > a,
    .with-nav-tabs.panel-success .nav-tabs > .open > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > .open > a:focus,
    .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
        color: #3c763d;
        background-color: white;
        border-color: transparent;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.active > a,
    .with-nav-tabs.panel-success .nav-tabs > li.active > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
        color: #3c763d;
        background-color: #fff;
        border-color: #d6e9c6;
        border-bottom-color: transparent;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu {
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a {
        color: #3c763d;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
        background-color: #d6e9c6;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
    .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
        color: #fff;
        background-color: #3c763d;
    }

    .panel-success > .panel-heading {
        background-color: white;
    }

    .with-nav-tabs.panel-success .nav-tabs > li.active > a, .with-nav-tabs.panel-success .nav-tabs > li.active > a:hover, .with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
        color: #000000;
        background-color: #ecf0f5;
        border-color: #ecf0f5;
        border-bottom-color: transparent;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        font-size: 12px;
    }

    .pagination > li > a, .pagination > li > span {
        padding: 2px 8px;
    }

    .content-wrap section {
        text-align: left;
    }

    #tbl_machine th {
        text-transform: uppercase;
    }

    #tble_jobstatus th {
        text-transform: uppercase;
    }

    .bubble {
        text-align: center;
        padding-top: 30px;
    }

    .bubble_number {
        font-size: 36px;
        cursor: pointer;
    }

    .bubble_text {
        font-size: 21px;
    }
    .b-1:hover{
        border-color: #e59501;
        border-width: 5px;
        border-style: solid;
    }

    .b-2:hover{
        border-color: #b446e2;
        border-width: 5px;
        border-style: solid;
    }

    .b-3:hover{
        border-color: #0d9564cc;
        border-width: 5px;
        border-style: solid;
    }
    .pxy_rfq{
        padding: 10px 3px;
        min-height: 420px;
    }
</style>

<section>
    <div class="box">
                            <div class="box-header with-border" id="box-header-with-border">
                                <h3 class="box-title" id="box-header-title">TENDER LOGS</h3>
                                <div class="box-tools pull-right">
                                    <a data-toggle="collapse" data-target="#filter-panel"><i class="fa fa-filter"></i> Filter<!--Filter--></a><button id="" class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    <button id="" class="btn btn-box-tool headerclose navdisabl" type="button"><i class="fa fa-times"></i></button>
                                </div>                                
                            </div>
                            <div class="box-body">
                            <div class="row">
                                    <div class="col-sm-12">
                                        <?php
                                            include 'mfq_tender_btn_nav.php';
                                        ?>
                                    </div>
                            </div>
                            <div class="row pt-10">
                                <div class="col-sm-6">
                                    <div class="box box-warning bg-chart-rfq">
                                        <div class="box-header with-border">
                                            <h4 class="box-title text-uppercase">
                                            TOTAL FIRM RFQ WINNING RATIO</h4>
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                            class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>  
                                       
                                        <div class="box-body pxy_rfq">
                                        <div class="table-responsive">
                                                <table id="tbl_rfq_types" class="rfqTable table table-striped table-condensed">
                                                    <thead>
                                                    <tr>
                                                        <th style="min-width: 12%" class="text-uppercase">
                                                        SI No.</th>
                                                        <th style="min-width: 12%">RFQ Type</th>
                                                        <th style="min-width: 12%">Estimator</th>
                                                        <th style="min-width: 3%">Firm</th>
                                                        <th style="min-width: 3%">Budget</th>
                                                        <th style="min-width: 3%">Remarks</th>
                                                        <th style="min-width: 3%">Total RFQ</th>                                            
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                            <tr>
                                                                <td>Completed</td>
                                                                <td class="text-center">0</td>
                                                                <td class="text-center">2</td>
                                                                <td class="text-center">0</td>
                                                                <td class="text-center">1</td>
                                                                <td class="text-center">0</td>
                                                                <td class="text-center">0</td>                                                   
                                                            </tr>
                                                            <tr>
                                                                <td>Ongoing</td>
                                                                <td class="text-center">0</td>
                                                                <td class="text-center">0</td>
                                                                <td class="text-center">0</td>
                                                                <td class="text-center">1</td>
                                                                <td class="text-center">0</td>
                                                                <td class="text-center">0</td>                                                     
                                                            </tr>
                                                    </tbody>
                                                </table>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="box box-warning bg-chart-rfq">
                                        <div class="box-header with-border">
                                            <h4 class="box-title text-uppercase">
                                                Winning Ratio<!-- Winning Ratio --></h4>
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                            class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>                               
                                        <div class="box-body" id="" style="display: block;width: 100%">
                                            <div id="mfq_winning_report"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
    </div>
                    

                    
                </div>

</section>
<script>
    $(document).ready(function () {

        generate_rfq_total_barchart();

    });

    function generate_rfq_total_barchart() {
        var clientID = $('#filter_ajs_mfqCustomerAutoID').val();
        var segmentID = $('#filter_ajs_DepartmentID').val();
        var date = $('#filter_ajs_date').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'clientID': clientID, 'segmentID': segmentID, 'date': date},
            url: "<?php echo site_url('MFQ_Dashboard/awarded_job_status'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                Highcharts.chart('mfq_winning_report', {
                    chart: {
                        type: 'pie',
                        options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                        }
                    },
                    title: {text: ''},                    
                    accessibility: {
                        point: {
                        valueSuffix: '%'
                        }
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        depth: 35,
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}'
                        }
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: 'Share',
                        data: [
                        ['Prakash', 23],
                        ['Rajesh', 18],
                        {
                            name: 'Bala',
                            y: 12,
                            sliced: true,
                            selected: true
                        },
                        ['Yunus*', 9],
                        ['Ram', 8],
                        ['Hasna', 30]
                        ]
                    }]
                });
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }    

    function actual_drilldown(category, type) 
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {category: category, type: type},
            url: "<?php echo site_url('MFQ_Dashboard/actual_drilldown'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#drilldownTitle").text("QUOTATIONS");
                $("#quotationDrilldownTable").html(data);
                $("#quotationDrilldownModal").modal('show');
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

</script>