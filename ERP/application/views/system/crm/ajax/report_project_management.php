<?php
$primaryLanguage = getPrimaryLanguage();
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
                            <div class="toolbar-title">
                                <i class="fa fa-file-text"
                                   aria-hidden="true"></i> <?php echo $this->lang->line('crm_project_reports'); ?>
                            </div><!--Project Reports-->
                        </div>
                        <div class="col-sm-4">
                            <span class="no-print pull-right" style="margin-top: -1%;margin-right: -5%;"> 
                                <a class="btn btn-danger btn-sm pull-right" style="padding: 4px 12px;font-size: 9px;" target="_blank" onclick="generateReportPdf('project')">
                                    <span class="fa fa-file-pdf-o" aria-hidden="true"> PDF</span> 
                                </a>
                            </span>
                            <span class="no-print pull-right" style="margin-top: -2%;margin-right: 1%;">
                                <?php  echo export_buttons('projectrpt', 'Project Report', True, false)?>
                            </span>
                                    <!--     <span class="no-print pull-right" style="margin-top: -3%;margin-right: -7%;"> <a
                                                        class="btn btn-default btn-sm no-print pull-right" target="_blank"
                                                        onclick="generateReportPdf('project')">
                                                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a></span>-->
                        </div>
                    </div>
                </div>
                <div class="post-area">
                    <article class="page-content">

                        <div class="system-settings">

                            <div class="row">
                                <div class="col-sm-12" id="projectrpt">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('common_name'); ?></th><!--Name-->
                                            <th>Category</th><!--Name-->
                                            <th><?php echo $this->lang->line('common_description'); ?></th>
                                            <!--Description-->
                                            <th><?php echo $this->lang->line('common_start_date'); ?></th>
                                            <!--Start Date-->
                                            <th><?php echo $this->lang->line('common_end_date'); ?></th><!--End Date-->
                                            <th>User Responsible</th><!--End Date-->
                                            <th><?php echo $this->lang->line('common_status'); ?></th><!--Status-->
                                            <th>Completed %</th>
                                            <th>Completed Value</th>
                                            <th><?php echo $this->lang->line('common_value'); ?></th><!--Value-->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $x = 1;
                                        $total = 0;
                                        $currency = '';
                                        if (!empty($project)) {
                                            //var_dump($project);
                                            foreach ($project as $row) {
                                                $pipelinePercentage = $this->db->query("SELECT SUM(probability) as probability FROM srp_erp_crm_pipelinedetails where sortOrder <= (SELECT sortOrder FROM srp_erp_crm_pipelinedetails where pipeLineDetailID = {$row['pipelineStageID']}) AND pipeLineID = {$row['pipelineID']}")->row('probability');
                                                if(!$pipelinePercentage) { $pipelinePercentage = 0; }
                                                $currency = $row['CurrencyCode'];
                                                ?>
                                                <tr>
                                                    <td><?php echo $x; ?></td>
                                                    <td><?php echo $row['projectName'] ?></td>
                                                    <td><?php echo $row['categorydes'] ?></td>
                                                    <td><?php echo $row['oppoDescription'] ?></td>
                                                    <td><?php echo $row['projectStartDate'] ?></td>
                                                    <td><?php echo $row['projectEndDate'] ?></td>
                                                    <td><?php echo $row['responsiblePerson'] ?></td>
                                                    <td><?php echo $row['statusDescription'] ?></td>
                                                    <td><?php echo $pipelinePercentage ?></td>
                                                    <td><?php echo number_format(($row['transactionAmount']/100) *$pipelinePercentage, 2) ?></td>
                                                    <td style="text-align: right"><?php echo number_format($row['transactionAmount'], 2) ?></td>
                                                </tr>
                                                <?php
                                                $total += $row['transactionAmount'];
                                                $x++;
                                            }
                                        } else { ?>
                                            <tr>
                                                <td colspan="11"
                                                    style="text-align: center"><?php echo $this->lang->line('common_no_records_found'); ?></td>
                                                <!--No Records Found-->
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td style="min-width: 85%  !important" class="text-right sub_total"
                                                colspan="10">
                                                <?php echo $this->lang->line('common_total'); ?><?php echo "( $currency ) " ?> </td>
                                            <!--Total-->
                                            <td style="min-width: 15% !important"
                                                class="text-right total"><?php echo number_format($total, 2); ?></td>
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