<?php
$primaryLanguage = getPrimaryLanguage();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$category_arr = all_projects_category_multiple();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="width100p">
    <section class="past-posts">
        <div class="posts-holder settings">
            <div class="past-info">
                <div id="toolbar">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="toolbar-title" >
                                <i class="fa fa-file-text"
                                   aria-hidden="true"></i> Project Monitoring
                            </div><!--Project Reports-->
                        </div>
                        <div class="col-sm-4">
                               <span class="no-print pull-right" style="margin-top: -1%;margin-right: -5%;"> <a class="btn btn-danger btn-sm pull-right" style="padding: 4px 12px;font-size: 9px;" target="_blank" onclick="generateReportPdf('projectmoni')">
                                <span class="fa fa-file-pdf-o" aria-hidden="true"> PDF
            </span> </a></span>
                            <span class="no-print pull-right" style="margin-top: -2%;margin-right: 1%;">
                                      <?php  echo export_buttons('projectmoniteringrpt', 'Project Monitoring', True, false)?>
                              </span>
                        </div>
                    </div>
                </div>
                <div class="post-area">
                    <article class="page-content">
                        <div style="background-color:#b5d5ff; padding: 2px; font-size: 15px;"><b>Project Performance Tracker</b></div>
                        <div class="col-sm-12">
                            <div class="form-group col-sm-4">
                                <label for="">Date From</label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="datefrom"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php  echo $datefrm ?>" id="datefrom" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Date To</label>
                                <div class="input-group datepicto">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="dateto"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php if(!empty($datetu)){echo $datetu ;}else{ echo $current_date ;} ; ?>" id="dateto" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="">Category</label>
                                <br>
                                <?php echo form_dropdown('categoryID[]', $category_arr, '', ' multiple class="form-control"  id="categoryID"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group col-sm-4">

                            </div>
                            <div class="form-group col-sm-4">

                            </div>
                            <div class="form-group col-sm-2" style="padding-left: 165px;">
                                <button type="button" class="btn btn-primary pull-right" style="margin-top: 20px;" onclick="configuration_page('projectmoni','html')">Search</button>
                            </div>
                        </div>

                        <div class="system-settings">

                            <div class="row">
                                <div class="col-sm-12" id="projectmoniteringrpt">
                                    <?php
                                    foreach($piplines as $pip){
                                        $companyID=current_companyID();
                                        $pipeLineID=$pip['pipeLineID'];
                                        $stages = $this->db->query("SELECT pipeLineDetailID as stagid,stageName FROM srp_erp_crm_pipelinedetails WHERE companyID = $companyID AND pipeLineID=$pipeLineID")->result_array();
                                        if(!empty($frmDate) && !empty($toDate)){
                                            $whwre="AND projectStartDate BETWEEN '$frmDate' AND '$toDate'";
                                        }else{
                                            $dts=date("Y-01-01");
                                            $dt=date("Y-m-d");
                                            $whwre="AND projectStartDate BETWEEN '$dts' AND '$dt'";
                                        }
                                        if(!empty($category)){
                                            $catdd=$category;
                                            $whcate="AND srp_erp_crm_project.categoryID IN (" . join(',', $category) . ")";
                                        }else{
                                            $cat=$this->db->query("SELECT `categoryID` FROM `srp_erp_crm_categories` WHERE `documentID` = 9 AND `companyID` = '$companyID'")->result_array();
                                            $cata=array();
                                            foreach($cat as $cal){
                                                array_push($cata,$cal['categoryID']);
                                            }
                                            $whcate="AND srp_erp_crm_project.categoryID IN (" . join(',', $cata) . ")";
                                            $catdd=$cata;
                                        }
                                        $exsist = $this->db->query("SELECT pipelineStageID FROM srp_erp_crm_project LEFT JOIN srp_employeesdetails ON srp_erp_crm_project.responsibleEmpID = srp_employeesdetails.EIdNo WHERE companyID=$companyID AND pipelineID=$pipeLineID  $whwre $whcate ")->result_array();
                                        if(!empty($exsist)){
                                            ?>
                                        <!--<div style=" color: darkblue; padding: 2px; font-size: 15px;"><b><?php /*echo $pip['pipeLineName'] */?></b></div>-->
                                            <table style="width: 100%">
                                                    <tbody>
                                                    <tr>
                                                        <td style="width:60%;">
                                                            <table>
                                                                <tr>
                                                                    <td>
                                                                        <b style="color: darkblue;font-size: 15px;"><?php echo $pip['pipeLineName'] ?></b>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                            </table>

                                            <table class="table table-striped table-bordered">

                                            <thead>
                                            <tr>
                                                <th>Project Owner</th>
                                                <?php
                                                foreach($stages as $stg){
                                                    ?>
                                                    <th><?php echo $stg['stageName']; ?></th>
                                                    <?php
                                                }
                                                ?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if(!empty($satusbody)){
                                            foreach($satusbody as $nam){
                                            ?>
                                                <tr>
                                                    <td><?php echo $nam['ename']; ?></td>
                                                    <?php
                                                    foreach($stages as $stg){
                                                        $empid=$nam['responsibleEmpID'];
                                                        $stgid=$stg['stagid'];
                                                        if(!empty($frmDate) && !empty($toDate)){
                                                            $whwre="AND projectStartDate BETWEEN '$frmDate' AND '$toDate'";
                                                        }else{
                                                            $dts=date("Y-01-01");
                                                            $dt=date("Y-m-d");
                                                            $whwre="AND projectStartDate BETWEEN '$dts' AND '$dt'";
                                                        }
                                                        $datas = $this->db->query("SELECT COUNT(pipelineStageID) as pipelineStageID FROM srp_erp_crm_project LEFT JOIN srp_employeesdetails ON srp_erp_crm_project.responsibleEmpID = srp_employeesdetails.EIdNo WHERE companyID=$companyID AND pipelineStageID=$stgid AND pipelineID=$pipeLineID AND responsibleEmpID=$empid $whwre $whcate ")->row_array();
                                                        $projctid = $this->db->query("SELECT GROUP_CONCAT(projectID) as projectID FROM srp_erp_crm_project WHERE companyID=$companyID AND pipelineStageID=$stgid AND pipelineID=$pipeLineID AND responsibleEmpID=$empid $whwre ")->row_array();
                                                        if($datas['pipelineStageID']>0){
                                                            ?>
                                                            <td style="text-align: center;"><a style="cursor: pointer;" onclick="open_project_dd_model('(<?php echo $projctid['projectID'] ?>)','(<?php echo join(',', $catdd) ?>)')"><?php echo $datas['pipelineStageID']; ?></a></td>
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <td style="text-align: center;"><?php echo $datas['pipelineStageID']; ?></td>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                            }
                                            }?>
                                            </tbody>
                                            <tfoot>

                                            </tfoot>
                                        </table>
                                        <?php
                                    }
                                    }
                                    ?>
                                    <hr>
                                    <div style="background-color:#b5d5ff; padding: 2px; font-size: 15px;"><b>Unclosed Tasks</b></div>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Assignees</th>
                                            <?php foreach($uncloshead as $valueun){
                                                ?>
                                                <th><?php echo $valueun['description']; ?></th>
                                                <?php
                                            } ?>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach($unclosbody as $unclosedb){
                                            ?>
                                            <tr>
                                                <td><?php echo $unclosedb['Ename2']; ?></td>
                                               <?php
                                               $emptot=0;
                                                foreach($uncloshead as $headbody){
                                                    $empID=$unclosedb['empID'];
                                                    $categoryID=$headbody['categoryID'];
                                                    $companyID=current_companyID();
                                                    if(!empty($frmDate) && !empty($toDate)){
                                                        $whwre="AND srp_erp_crm_task.starDate BETWEEN '$frmDate' AND '$toDate'";
                                                    }else{
                                                        $dts=date("Y-01-01");
                                                        $dt=date("Y-m-d");
                                                        $whwre="AND srp_erp_crm_task.starDate BETWEEN '$dts' AND '$dt'";
                                                    }
                                                    $datasb = $this->db->query("SELECT empID,srp_erp_crm_categories.textColor,srp_erp_crm_categories.backGroundColor,COUNT(srp_erp_crm_task.categoryID) as cunt FROM srp_erp_crm_assignees LEFT JOIN srp_erp_crm_task ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_task.categoryID = srp_erp_crm_categories.categoryID WHERE srp_erp_crm_assignees.companyID=$companyID AND srp_erp_crm_task.isClosed=0 AND srp_erp_crm_assignees.documentID = 2 AND empID=$empID AND srp_erp_crm_task.categoryID=$categoryID $whwre ")->row_array();
                                                    $taskidb = $this->db->query("SELECT GROUP_CONCAT(srp_erp_crm_task.taskID) as taskID FROM srp_erp_crm_assignees LEFT JOIN srp_erp_crm_task ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_task.categoryID = srp_erp_crm_categories.categoryID WHERE srp_erp_crm_assignees.companyID=$companyID AND srp_erp_crm_task.isClosed=0 AND srp_erp_crm_assignees.documentID = 2 AND empID=$empID AND srp_erp_crm_task.categoryID=$categoryID $whwre ")->row_array();
                                               if($datasb['cunt']>0) {
                                                   ?>
                                                   <td style="text-align: center;"><a style="cursor: pointer;" onclick="open_task_dd_model('<?php echo $taskidb['taskID'] ?>')"><?php echo $datasb['cunt']; ?></a></td>
                                                   <?php
                                               }else{
                                                   ?>
                                                   <td style="text-align: center;"><?php echo $datasb['cunt']; ?></td>
                                                <?php
                                               }
                                                    $emptot+=$datasb['cunt'];
                                                }
                                               ?>
                                                <td style="text-align: center;"><?php echo $emptot; ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>

                                        </tbody>
                                    </table>
                                    <hr>
                                    <div style="background-color:#b5d5ff; padding: 2px; font-size: 15px;"><b>Project Closing</b></div>
                                    <div class="form-group col-sm-4">
                                        <label for="">Year</label>
                                        <div class="input-group datepicy">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="dateyear" value="<?php  if(!empty($year)){echo $year;}else{ echo '2018'; }  ?>" id="dateyear" class="form-control">
                                        </div>
                                    </div>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Project Owners</th>
                                            <th>JAN</th>
                                            <th>FEB</th>
                                            <th>MAR</th>
                                            <th>APR</th>
                                            <th>MAY</th>
                                            <th>JUNE</th>
                                            <th>JULY</th>
                                            <th>AUG</th>
                                            <th>SEPT</th>
                                            <th>OCT</th>
                                            <th>NOV</th>
                                            <th>DEC</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if(!empty($Closingbody)){
                                            foreach($Closingbody as $nam){
                                                ?>
                                                <tr>
                                                    <td><?php echo $nam['ename']; ?></td>

                                                    <?php
                                                    $totempclos=0;
                                                    for ($x = 1; $x <= 12; $x++) {
                                                        $companyID=current_companyID();

                                                        $empid=$nam['responsibleEmpID'];
                                                        $yearfltr='';
                                                        $monthfltr='';
                                                        if(!empty($year)){
                                                            $yearfltr='AND YEAR(closedDate)='."$year";
                                                            $monthfltr='AND MONTH(closedDate)='."$x";
                                                        }
                                                        $datas = $this->db->query("SELECT COUNT(projectStatus) as projectStatus FROM srp_erp_crm_project LEFT JOIN srp_employeesdetails ON srp_erp_crm_project.responsibleEmpID = srp_employeesdetails.EIdNo WHERE companyID=$companyID $yearfltr  $monthfltr AND isClosed=1  AND responsibleEmpID=$empid $whcate  ")->row_array();
                                                        $projctid = $this->db->query("SELECT GROUP_CONCAT(projectID) as projectID FROM srp_erp_crm_project WHERE companyID=$companyID $yearfltr  $monthfltr AND isClosed=1 AND responsibleEmpID=$empid ")->row_array();
                                                        if($datas['projectStatus']>0){
                                                        ?>
                                                        <td style="text-align: center;"><a style="cursor: pointer;" onclick="open_project_dd_model('(<?php echo $projctid['projectID'] ?>)','(<?php echo join(',', $catdd) ?>)')"><?php echo $datas['projectStatus']; ?></a></td>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <td style="text-align: center;"><?php echo $datas['projectStatus']; ?></td>
                                                        <?php

                                                    }
                                                        $totempclos+=$datas['projectStatus'];
                                                    } ?>
                                                    <td><?php echo $totempclos; ?></td>
                                                </tr>
                                                <?php
                                            }} ?>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td>Total</td>
                                                <?php
                                                $totclos=0;
                                                for ($x = 1; $x <= 12; $x++) {
                                                    $companyID=current_companyID();
                                                    $yearfltr='';
                                                    $monthfltr='';
                                                    if(!empty($year)){
                                                        $yearfltr='AND YEAR(closedDate)='."$year";
                                                        $monthfltr='AND MONTH(closedDate)='."$x";
                                                    }
                                                    $datas = $this->db->query("SELECT COUNT(projectStatus) as projectStatus FROM srp_erp_crm_project LEFT JOIN srp_employeesdetails ON srp_erp_crm_project.responsibleEmpID = srp_employeesdetails.EIdNo WHERE companyID=$companyID $yearfltr  $monthfltr AND isClosed=1 $whcate ")->row_array();
                                                    ?>
                                                    <td style="text-align: center;"><?php echo $datas['projectStatus']; ?></td>
                                                    <?php
                                                    $totclos+=$datas['projectStatus'];
                                                } ?>
                                                <td><?php echo $totclos; ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <hr>
                                    <div id="ProjectClosingChart" style="min-width: 390px; height: 400px; max-width: 600px; margin: 0 auto"></div>


                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });
    $('.datepicto').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });
    $('.datepicy').datetimepicker({
        useCurrent: false,
        format: 'YYYY',
    }).on('dp.change', function (ev) {
        configuration_page('projectmoni','html')
    });

    var sel_cat = <?php echo json_encode($category) ?>;

    $('#categoryID').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: true,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 200,
        numberDisplayed: 1
    });

    if(sel_cat.length > 0){
        $('#categoryID').val([<?php echo $category2 ?>]);
        $('#categoryID').multiselect2('refresh');

    }else{
        $("#categoryID").multiselect2('selectAll', false);
        $("#categoryID").multiselect2('updateButtonText');
    }

    generPichart();

    function generPichart() {

        Highcharts.chart('ProjectClosingChart', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
            },
            title: {
                text: 'Project Closing Chart'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                        distance: -50,
                        filter: {
                            property: 'percentage',
                            operator: '>',
                            value: 4
                        }
                    }

                }
            },
            series: [{
                name: 'Share',
                //data:  [{"name":"House","y":495000},{"name":"Human Injury","y":145400},{"name":"House Items","y":639500},{"name":"Business Property","y":9965000}]
                data:  <?php echo $piData ?>


    }]
        });
    }
</script>