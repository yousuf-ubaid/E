<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = "Appraisal Allocation";


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
                    <div class="col-md-4">
                        <div class="form-group" style="margin-top:12px">
                            <select class="form-control" id="goals2"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="allocated_percentage" style="width:100%; height:400px;"></div>
                </div>
            </div>


            <?php echo footer_page('Right foot', 'Left foot', false); ?>
            <script>
                app = {};
                app.company_id = <?php echo current_companyID(); ?>;
                $(document).ready(function () {
                    load_goal_list2();
                    load_report2();
                });

                $('#goals2').change(function () {
                    load_report2();
                });

                function load_report2() {
                    var goal_id = $('#goals2').val();

                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/overall_allocated_percentages_for_dashboard'); ?>",
                        data: {company_id: app.company_id, goal_id: goal_id},
                        success: function (data) {

                            var myChart = Highcharts.chart('allocated_percentage', {
                                chart: {
                                    type: 'pie'
                                },
                                title: {
                                    text: '<?php echo $this->lang->line('appraisal_overall_performance_allocation'); ?>'/*Overall Performance Allocation*/
                                },
                                series: [{
                                    name: '<?php echo $this->lang->line('appraisal_allocated_percentage'); ?>',/*Allocated percentage*/
                                    colorByPoint: true,
                                    data: data.allocated_percentages
                                }]
                            });
                        }
                    });
                }

                function load_goal_list2() {
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_corporate_goals_for_dashboard'); ?>",
                        data: {company_id: app.company_id},
                        success: function (data) {
                            var dropdown_list_items = "";
                            if(data.length==0){
                                dropdown_list_items+='<option><?php echo $this->lang->line('appraisal_no_data_available'); ?></option>';/*No data available*/
                            }else{
                                data.forEach(function (item, index) {
                                    dropdown_list_items += '<option value="' + item.id + '">' + item.narration + '</option>';
                                });
                            }
                            $('#goals2').html(dropdown_list_items);
                        }
                    });
                }

            </script>
