<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = "My Employee Appraisal Completion";
?>
<style>
    .error-message {
        color: red;
    }

    .objectives-table th {
        text-align: left;
    }

    .act-btn-margin {
        margin: 0 2px;
    }

    .progress {
        position: relative;
    }

    .progress span {
        position: absolute;
        display: block;
        width: 100%;
        color: black;
        text-align: center;
    }


    .speech-bubble {
        position: relative;
        background: #00aabb;
        border-radius: .4em;
        width: auto;
        float: right;
        padding: 10px;
        color: white;
        margin: 3px 0;
        max-width: 60%;
    }

    .speech-bubble:after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        width: 0;
        height: 0;
        border: 0.438em solid transparent;
        border-left-color: #00aabb;
        border-right: 0;
        border-bottom: 0;
        margin-top: -0.219em;
        margin-right: -0.437em;
    }

    .speech-bubble2 {
        position: relative;
        background: #efefef;
        border-radius: .4em;
        width: auto;
        float: left;
        padding: 10px;
        color: black;
        margin: 3px 0;
        max-width: 60%;
    }

    .speech-bubble2:after {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 0;
        height: 0;
        border: 0.438em solid transparent;
        border-right-color: #efefef;
        border-left: 0;
        border-top: 0;
        margin-top: -0.219em;
        margin-left: -0.437em;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">
                    &nbsp;
                </div>
            </div>
            <link rel="stylesheet"
                  href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>


            <div class="row">
                <div class="col-md-12">
                    <div id="emp_completed_percentage" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    app = {};
    app.company_id = <?php echo current_companyID(); ?>;
    $(document).ready(function () {
        load_report3();
    });

    function load_report3() {
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/overall_completion_percentages_for_widget'); ?>",
            data: {},
            success: function (data) {
                var myChart = Highcharts.chart('emp_completed_percentage', {
                    chart: {
                        type: 'pie'
                    },
                    tooltip: {
                        pointFormat: 'Count: <b>{point.count}</b><br/> Percentage: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true
                        }
                    },
                    title: {
                        text: 'Employee Completion'
                    },
                    series: [{
                        name: 'Data',/*Allocated percentage*/
                        data: data
                    }]
                });
            }
        });
    }
</script>
