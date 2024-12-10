<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = "Raw material average purchase";
$item_arr = fetch_item_dropdown(false);
?>
<style>
    .highcharts-figure, .highcharts-data-table table {
    min-width: 360px; 
    max-width: 800px;
    margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #EBEBEB;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }
    .highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
    }
    .highcharts-data-table th {
        font-weight: 600;
    padding: 0.5em;
    }
    .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
    }
    .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
    }
    .highcharts-data-table tr:hover {
    background: #f1f7ff;
    }
</style>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<!-- <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/> -->
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">
                    &nbsp;
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-sm-6">
                        <label><?php echo $this->lang->line('common_item'); ?><!--Item--></label>
                        <?php echo form_dropdown('items', $item_arr, '', '  class="form-control select2" id="items"  onchange = "load_chart()" '); ?>
                    </div>
                    <div class="form-group col-sm-3">
                        <label><?php //echo $this->lang->line('common_item'); ?>Order By</label>
                        <?php echo form_dropdown('orderby', array(1=>'Daily',2=>'Weekly',3=>'Monthly'), '3', '  class="form-control select2" id="orderby"  onchange = "load_chart()" '); ?>
                    </div>
                </div>
            </div>
            <!-- <link rel="stylesheet" href="<?php //echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/> -->
            <div class="row">
                <div class="col-md-12">
                    <div id="" style="width:100%; height:400px;">

                        <figure class="highcharts-figure">
                            <div id="container"></div>
                           <!-- <p class="highcharts-description">
                                Highcharts has extensive support for time series, and will adapt
                                intelligently to the input data. Click and drag in the chart to zoom in
                                and inspect the data.
                            </p>-->
                        </figure>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<script>

    $(document).ready(function () {
        $(".select2").select2();
        load_chart();
    });

    function load_chart() {
        var item = $("#items").val();
        var orderby = $("#orderby").val();



        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Inventory/calculate_average_purcahse_of_raw_material'); ?>",
            data: {item:item,orderby:orderby},
            success:  function (data) {

                loopdata = [];
                if (!jQuery.isEmptyObject(data)) {
                               
                    $.each(data, function (key, value) {
                        $.each(value, function (key, value) {
                            if(key == '0'){
                                loopdata.push(value);
                            }
                        });
                    });
                }

                Highcharts.chart('container', {
                    chart: {
                        zoomType: 'x'
                    },
                    title: {
                        text: 'Raw Materials Average Purchase over time'
                    },
                    subtitle: {
                        text: document.ontouchstart === undefined ?
                            'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
                    },
                    xAxis: {
                        categories:  loopdata
                    },
                    yAxis: {
                        title: {
                            text: 'Average Purchase'
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    plotOptions: {
                        area: {
                            fillColor: {
                                linearGradient: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 0,
                                    y2: 1
                                },
                                stops: [
                                    [0, Highcharts.getOptions().colors[0]],
                                    [1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                ]
                            },
                            marker: {
                                radius: 2
                            },
                            lineWidth: 1,
                            states: {
                                hover: {
                                    lineWidth: 1
                                }
                            },
                            threshold: null
                        }
                    },

                    series: [{
                        type: 'area',
                        name: 'Average Purchase',
                        data: data
                    }]
                });
            }
        });
    }
</script>
