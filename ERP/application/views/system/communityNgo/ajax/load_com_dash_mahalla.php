<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$this->load->helper('community_ngo_helper');
?>
<style>

    #search_fam_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

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

    ul.circleUl {
        list-style-type: circle;
        font-size: 14px;
        font-weight: inherit;
        font-style: italic;
    }
    li.merr-item {
        margin:0 0 10px 0;
    }
</style>
<!-- Morris.js charts -->

<script src="<?php echo base_url('plugins/morris/morris.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/community_ngo/raphael-min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/morris/morris.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/daterangepicker/daterangepicker-bs3.css'); ?>">

<?php
if (!empty($comMaster)) {

    ?>

    <div class="no_padding col-md-9" style="padding: 2px;">
        <div class="col-md-6 col-sm-6 col-xs-12" style="padding:10px;">

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">
                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_population_status');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">
                        <!-- THE Status -->
                        <div class="chart-responsive">
                            <div class="chart" id="donut_forMah_population" style="height: 250px; position: relative;"></div>
                        </div>
                        <div>
                            <li class="fa fa-circle-o text"> <?php echo $this->lang->line('common_total');?>:</li> <span><input id="memTotal" value="" style="border: none;font-weight: bold;"></span>
                            <br>
                            <li class="fa fa-circle-o text-green"> <?php echo $this->lang->line('common_male');?>:</li> <span><input id="maleTotal" value="" style="border: none;font-weight: bold;"></span>
                            <br>
                            <li class="fa fa-circle-o text-blue"> <?php echo $this->lang->line('common_female');?>:</li> <span><input id="femaleTotal" value="" style="border: none;font-weight: bold;"></span>
                        </div>
                        <!-- /.row -->

                    </div>

                </div>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">
                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_occupation-wise');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">

                        <?php if(!empty($OccupationBase)){ ?>
                            <div  class="chart-responsive">
                                <div class="row">
                                <div class="col-md-7">
                                <div id="Occupation_chart" style="height: 250px; position: relative;"></div>
                               </div>
                                <div class="col-md-5">
                                    <ul class="chart-legend clearfix" style="font-size: 12px;font-weight: bold;">
                                        <li><i class="fa fa-circle-o text-red"></i> <input id="occTypeDiv1" value="" style="border: none;"></li>
                                        <li><i class="fa fa-circle-o text-green"></i> <input id="occTypeDiv2" value="" style="border: none;;"></li>
                                        <li><i class="fa fa-circle-o text-yellow"></i> <input id="occTypeDiv3" value="" style="border: none;"></li>
                                        <li><i class="fa fa-circle-o text-aqua"></i> <input id="occTypeDiv4" value="" style="border: none;"></li>
                                        <li><i class="fa fa-circle-o text-light-blue"></i> <input id="occTypeDiv5" value="" style="border: none;"></li>
                                        <li><i class="fa fa-circle-o text-gray"></i> <input id="occTypeDiv6" value="" style="border: none;"></li>
                                    </ul>
                                </div>
                                </div>
                            </div>

                        <?php }else{ ?>

                            <div class="well well-sm" style="margin-bottom: 0px; text-align: center;">
                                <font style="color: darkgrey;"><?php echo $this->lang->line('communityngo_no_data_found');?> </font>
                            </div>

                        <?php } ?>
                    </div>

                </div>
            </div>

        </div>

        <div class="col-md-6 col-sm-6 col-xs-12" style="padding:10px;">

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">

                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_blood_group_status');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">

                        <?php if(!empty($loadBloodCount)){ ?>
                            <div class="chart">
                                <canvas id="bloodGrpDiv" style=""></canvas>
                            </div>
                        <?php }else{ ?>

                            <div>
                                <font style="color: darkgrey;"><?php echo $this->lang->line('communityngo_no_data_found');?> </font>
                            </div>

                        <?php } ?>

                    </div></div></div>

            <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
                <div class="box box-primary" style="margin-bottom: 12px;">

                    <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_family_history');?></h3><div class="box-tools pull-right"></div></div>

                    <div class="box-body">
                        <form method="post" name="form_rpt_ngoFamDash" id="form_rpt_ngoFamDash" class="form-horizontal">
                        <!-- THE History -->
                        <fieldset class="scheduler-border">
                            <div class="col-md-12">
                            <div class="form-group col-sm-8" >
                                <label class="control-label"><?php echo $this->lang->line('CommunityNgo_leader');?> :</label>

                                    <?php echo form_dropdown('FamMasterID[]', fetch_familyMaster(false), '', 'multiple  class="form-control" id="FamMasterID" required'); ?>

                            </div>
                            <div class="form-group col-sm-4" style="margin-bottom: 0px;">
                                <button type="button" class="btn btn-success pull-right" style="font-size: 11px;" onclick="get_family_detail()" name="" id=""><i class="fa fa-plus"></i> Search
                                </button>
                            </div>
                                <div class="col-sm-1 hide" id="search_fam_cancel">
                    <span class="tipped-top" style="margin-left: 60%;"><a id="cancelSearchFamily" href="#"
                                                                          onclick="clearFamilyHistory()"><img
                                src="<?php echo base_url("images/community/cancel-search.gif") ?>"></a></span>
                                </div>
                            </div>
                            </fieldset>
                            <div class="table-responsive" style="height: 325px;">
                        <div id="famDatatableDiv"></div>
                                </div>
                            </form>
                        </div>

                    </div></div></div>
        </div>

    <div class="col-md-3 col-sm-6" style=" padding: 10px; ">
        <!-- Balance-->
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
            <div class="box box-primary" style="margin-bottom: 12px;">

                <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('communityngo_status');?></h3><div class="box-tools pull-right"></div></div>

                <div class="box-body">

                    <?php if(!empty($maritalBase)){ ?>
                        <ul class="chart-responsive circleUl">
                            <li class="merr-item">
                                <div class="bg-info"><span style="color: transparent;">...</span> <?php echo $maritalSt_type1['maritalstatus']; ?> <span class="text-muted" style="float: right;"> <?php
                                        echo round((($maritalSt_type1['merrType1']/$maritalStCount)*100),0); ?> % </span></div>
                            </li>
                            <li class="merr-item">
                                <div class="bg-success"><span style="color: transparent;">...</span> <?php echo $maritalSt_type2['maritalstatus']; ?> <span class="text-muted" style="float: right;"> <?php
                                        echo round((($maritalSt_type2['merrType2']/$maritalStCount)*100),0); ?> % </span></div>
                            </li>
                            <li class="merr-item">
                                <div class="bg-secondary"><span style="color: transparent;">...</span> <?php echo $maritalSt_type3['maritalstatus']; ?> <span class="text-muted" style="float: right;"> <?php
                                        echo round((($maritalSt_type3['merrType3']/$maritalStCount)*100),0); ?> % </span></div>
                            </li>
                            <li class="merr-item">
                                <div class="bg-danger"><span style="color: transparent;">...</span> <?php echo $maritalSt_type4['maritalstatus']; ?> <span class="text-muted" style="float: right;"> <?php
                                        echo round((($maritalSt_type4['merrType4']/$maritalStCount)*100),0); ?> % </span></div>
                            </li>
                            <li class="merr-item">
                                <div class="bg-warning"><span style="color: transparent;">...</span> <?php echo $maritalSt_type5['maritalstatus']; ?> <span class="text-muted" style="float: right;"> <?php
                                        echo round((($maritalSt_type5['merrType5']/$maritalStCount)*100),0); ?> % </span></div>
                            </li>
                           </ul>
                    <?php }else{ ?>

                        <div>
                            <font style="color: darkgrey;"><?php echo $this->lang->line('communityngo_no_data_found');?> </font>
                        </div>

                    <?php } ?>


                </div>

            </div></div>
        <!-- Fee Menus-->
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
            <div class="box box-primary" style="margin-bottom: 12px;">

                <div class="box-header with-border"><h3 class="box-title"><?php echo $this->lang->line('comNgo_dash_outside_family_ancestry');?></h3><div class="box-tools pull-right"></div></div>

                <div class="box-body">
                    <?php if(!empty($loadFamAnces)){ ?>
                        <div class="chart">
                            <canvas id="FamAncesDiv" style=""></canvas>
                        </div>
                    <?php }else{ ?>

                        <div class="well well-sm" style="margin-bottom: 0px; text-align: center;">
                            <font style="color: darkgrey;"><?php echo $this->lang->line('communityngo_no_data_found');?> </font>
                        </div>

                    <?php } ?>
                </div></div></div>

    </div>

    <?php
}
?>

<script>


    $('#FamMasterID').multiselect2({
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 200,
        maxHeight: 200,
        numberDisplayed: 1
    });
    $("#FamMasterID").multiselect2('selectAll', false);
    $("#FamMasterID").multiselect2('updateButtonText');

    function get_family_detail() {

        var FamMasterID =document.getElementById('FamMasterID').value;
       // var areaMemId =document.getElementById('areaMemId').value;

        $('#search_fam_cancel').removeClass('hide');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('CommunityNgoDashboard/get_family_details'); ?>",
            data: $("#form_rpt_ngoFamDash").serialize(),
            success: function (data) {

                $('#famDatatableDiv').html(data);

            }
        });

    }

</script>
<script type="text/javascript">


    //blood group counts
    <?php if(!empty($loadBloodCount)){ ?>
    $(function () {

        var bloodTypesData = {

            labels: [<?php foreach(array_reverse($loadBloodDes) as $resBloDes){ echo "'".$resBloDes['BloodDescription']."',";} ?>],
            datasets: [
                {
                    label: "<?php echo $this->lang->line('comNgo_dash_blood_group_status');?>",
                    fillColor: "rgba(180, 31, 8, 0.6)",
                    strokeColor: "rgba(180, 31, 8,0.7)",
                    pointColor: "#D2691E",
                    pointStrokeColor: "rgba(180, 31, 8,0.7)",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(180, 31, 8,0.7)",
                    data: [<?php foreach(array_reverse($loadBloodCount) as $row){echo "'".$row['NoOfGrpMem']."'," ;} ?>]
                }
            ]
        };

        var barChartbloodTypes = $("#bloodGrpDiv").get(0).getContext("2d");
        var barChart = new Chart(barChartbloodTypes);
        var barChartbloodTypesData = bloodTypesData;

        var barChartTdyOptions = {
            responsive: true,
            maintainAspectRatio: true
        };

        barChartTdyOptions.datasetFill = false;
        barChart.Bar(barChartbloodTypesData, barChartTdyOptions);

    });
    <?php } ?>
    //end of blood group counts

    //population donut chart

    $(function () {

        <?php
        $malesPecrnt = round((($males/$members)*100),0);
        $femalesPecrnt = round((($females/$members)*100), 0);

        ?>

        document.getElementById('memTotal').value =  '<?php echo $members; ?>';
        document.getElementById('maleTotal').value = '<?php echo $males.' ('.$malesPecrnt.'% )'; ?>';
        document.getElementById('femaleTotal').value = '<?php echo $females .' ('.$femalesPecrnt.'% )'; ?>';
        //DONUT CHART
        var donut = new Morris.Donut({
            element: 'donut_forMah_population',
            resize: true,
            colors: ["#00a65a","#3c8dbc"],
            data: [
                {label: "Male (%)", value: '<?php echo $malesPecrnt; ?>'},
                {label: "Female (%)", value: '<?php echo $femalesPecrnt; ?>'}

            ],
            hideHover: 'auto'
        });

    });

    //end of fee donut chart

    //occupation donut chart
    <?php if(!empty($OccupationBase)){ ?>

    $(function () {

        <?php
        $occ1Pecrnt = round((($occupation_type1['occType1']/$occupationTot)*100),0);
        $occ2Pecrnt = round((($occupation_type2['occType2']/$occupationTot)*100), 0);
        $occ3Pecrnt = round((($occupation_type3['occType3']/$occupationTot)*100), 0);
        $occ4Pecrnt = round((($occupation_type4['occType4']/$occupationTot)*100), 0);
        $occ5Pecrnt = round((($occupation_type5['occType5']/$occupationTot)*100), 0);
        $occ6Pecrnt = round((($occupation_type6['occType6']/$occupationTot)*100), 0);

        ?>

        document.getElementById('occTypeDiv1').value =  '<?php echo $occupation_type1['Description'].'(%) '.$occ1Pecrnt; ?>';
        document.getElementById('occTypeDiv2').value =  '<?php echo $occupation_type2['Description'].'(%) '.$occ2Pecrnt; ?>';
        document.getElementById('occTypeDiv3').value =  '<?php echo $occupation_type3['Description'].'(%) '.$occ3Pecrnt; ?>';
        document.getElementById('occTypeDiv4').value =  '<?php echo $occupation_type4['Description'].'(%) '.$occ4Pecrnt; ?>';
        document.getElementById('occTypeDiv5').value =  '<?php echo $occupation_type5['Description'].'(%) '.$occ5Pecrnt; ?>';
        document.getElementById('occTypeDiv6').value =  '<?php echo $occupation_type6['Description'].'(%) '.$occ6Pecrnt; ?>';

        //DONUT CHART
        var donut = new Morris.Donut({
            element: 'Occupation_chart',
            resize: true,
            colors: ["#f56954","#00a65a","#f39c12","#00c0ef","#3c8dbc","#d2d6de"],
            data: [
                {label: "<?php echo $occupation_type1['Description']; ?> (%)", value: '<?php echo $occ1Pecrnt; ?>'},
                {label: "<?php echo $occupation_type2['Description']; ?> (%)", value: '<?php echo $occ2Pecrnt; ?>'},
                {label: "<?php echo $occupation_type3['Description']; ?> (%)", value: '<?php echo $occ3Pecrnt; ?>'},
                {label: "<?php echo $occupation_type4['Description']; ?> (%)", value: '<?php echo $occ4Pecrnt; ?>'},
                {label: "<?php echo $occupation_type5['Description']; ?> (%)", value: '<?php echo $occ5Pecrnt; ?>'},
                {label: "<?php echo $occupation_type6['Description']; ?> (%)", value: '<?php echo $occ6Pecrnt; ?>'}

            ],
            hideHover: 'auto'
        });

    });

    // end of occupation donut chart
    <?php } ?>

    //start Family Ancestry
    <?php if(!empty($loadFamAnces)){ ?>
    $(function () {
        //Weekly Fee Collection
        var areaChartDataFeeDel = {
            labels: [<?php foreach(array_reverse($loadFamAnces) as $row){echo "'".$row['AncestryDes']."'," ;} ?>],
            datasets: [
                {
                    label: "<?php echo $this->lang->line('comNgo_dash_outside_family_ancestry');?>",
                    fillColor: "rgba(60,141,188,0.6)",
                    strokeColor: "rgba(60,141,188,0.8)",
                    pointColor: "#3b8bba",
                    pointStrokeColor: "rgba(60,141,188,1)",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(60,141,188,1)",
                    data: [<?php foreach(array_reverse($loadPerFamilyAnces) as $row){echo "'".$row['count']."'," ;} ?>]
                }
            ]
        };

        var areaChartFeeDel = document.getElementById('FamAncesDiv').getContext('2d');
        new Chart(areaChartFeeDel).Line(areaChartDataFeeDel);


    });
    <?php } ?>


    function clearFamilyHistory() {

        $('#search_fam_cancel').addClass('hide');
        $('#FamMasterID').multiselect2('deselectAll', false);
        $('#famDatatableDiv').html('');
        get_family_detail();
    }

</script>

<?php
