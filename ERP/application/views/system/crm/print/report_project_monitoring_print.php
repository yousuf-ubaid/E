<?php
$primaryLanguage = getPrimaryLanguage();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="width100p">
    <section class="past-posts">
        <div class="posts-holder settings">
            <div class="past-info">
                <div id="toolbar">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center reportHeaderColor">
                                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
                            </div>
                            <div class="text-center reportHeader reportHeaderColor">Project Monitoring</div>
                        </div>
                    </div>
                </div>
                <?php
                $catfilter = $this->db->query("SELECT GROUP_CONCAT(description) as description FROM srp_erp_crm_categories WHERE categoryID IN (" . join(',', $categorypdf) . ") ")->row_array();
                ?>
                <span>Filters:- </span>
                <br>
                <span style="font-size: 12px;font-weight: bold;">From Date:-</span> <span style="font-size: 12px;"><?php echo $frmdtfilter ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 12px;font-weight: bold;">To Date:-</span> <span style="font-size: 12px;"><?php echo $todtfilter ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 12px;font-weight: bold;">Category:-</span> <span style="font-size: 12px;"><?php echo $catfilter['description']; ?></span>
                <div class="post-area">
                    <article class="page-content">
                        <div class="system-settings">
                            <div style="background-color:#b5d5ff; padding: 2px; font-size: 15px;"><b>Project Performance Tracker</b></div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php
                                    foreach($piplines as $pip){
                                        $companyID=current_companyID();
                                        $pipeLineID=$pip['pipeLineID'];
                                        if(!empty($frmDate) && !empty($toDate)){
                                            $whwre="AND projectStartDate BETWEEN '$frmDate' AND '$toDate'";
                                        }else{
                                            $dts=date("Y-01-01");
                                            $dt=date("Y-m-d");
                                            $whwre="AND projectStartDate BETWEEN '$dts' AND '$dt'";
                                        }
                                        $whcate="AND srp_erp_crm_project.categoryID IN (" . join(',', $categorypdf) . ")";
                                        $stages = $this->db->query("SELECT pipeLineDetailID as stagid,stageName FROM srp_erp_crm_pipelinedetails WHERE companyID = $companyID AND pipeLineID=$pipeLineID")->result_array();
                                        $exsist = $this->db->query("SELECT pipelineStageID FROM srp_erp_crm_project LEFT JOIN srp_employeesdetails ON srp_erp_crm_project.responsibleEmpID = srp_employeesdetails.EIdNo WHERE companyID=$companyID AND pipelineID=$pipeLineID $whwre $whcate")->result_array();
                                        if(!empty($exsist)){
                                            ?>
                                        <div style="color: darkblue; padding: 2px; font-size: 15px;"><b><?php echo $pip['pipeLineName'] ?></b></div>
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
                                                                <td style="text-align: center;"><a style="cursor: pointer;" onclick="open_project_dd_model('<?php echo $projctid['projectID'] ?>')"><?php echo $datas['pipelineStageID']; ?></a></td>
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
                                    }}
                                    ?>

                                    <br>
                                    <div style="background-color:#b5d5ff; padding: 2px; font-size: 15px;"><b>Unclosed Tasks</b></div>
                                    <br>
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
                                                    $taskidb = $this->db->query("SELECT GROUP_CONCAT(srp_erp_crm_task.taskID) as taskID FROM srp_erp_crm_assignees LEFT JOIN srp_erp_crm_task ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_task.categoryID = srp_erp_crm_categories.categoryID WHERE srp_erp_crm_assignees.companyID=$companyID AND srp_erp_crm_task.isClosed=0 AND srp_erp_crm_assignees.documentID = 2 AND empID=$empID AND srp_erp_crm_task.categoryID=$categoryID ")->row_array();
                                               if($datasb['cunt']>0) {
                                                   ?>
                                                   <td style="text-align: center;"><?php echo $datasb['cunt']; ?></td>
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
                                    <br>
                                    <div style="background-color:#b5d5ff; padding: 2px; font-size: 15px;"><b>Project Closing</b></div>
                                    <span style="font-size: 12px;font-weight: bold;">Year:-</span> <span style="font-size: 12px;"><?php echo $yearfilter ?></span>
                                    <br>
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
                                                            <td style="text-align: center;"><?php echo $datas['projectStatus']; ?></td>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <td style="text-align: center;"><?php echo $datas['projectStatus']; ?></td>
                                                        <?php

                                                    }
                                                        $totempclos+=$datas['projectStatus'];
                                                    } ?>
                                                    <td style="text-align: center;"><?php echo $totempclos; ?></td>
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
                                                    $datas = $this->db->query("SELECT COUNT(projectStatus) as projectStatus FROM srp_erp_crm_project LEFT JOIN srp_employeesdetails ON srp_erp_crm_project.responsibleEmpID = srp_employeesdetails.EIdNo WHERE companyID=$companyID $yearfltr  $monthfltr AND isClosed=1 $whcate  ")->row_array();
                                                    ?>
                                                    <td style="text-align: center;" class="total"><?php echo $datas['projectStatus']; ?></td>
                                                    <?php
                                                    $totclos+=$datas['projectStatus'];
                                                } ?>
                                                <td style="text-align: center;" class="total"><?php echo $totclos; ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>


                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</div>