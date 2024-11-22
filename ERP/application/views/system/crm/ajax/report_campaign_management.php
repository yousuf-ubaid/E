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
                                   aria-hidden="true"></i> <?php echo $this->lang->line('crm_campaign_reports'); ?>
                            </div><!--Campaign Reports-->
                        </div>
                        <div class="col-sm-4">
                              <span class="no-print pull-right" style="margin-top: -1%;margin-right: -5%;"> <a class="btn btn-danger btn-sm pull-right" style="padding: 4px 12px;font-size: 9px;" target="_blank" onclick="generateReportPdf('campaign')">
                                <span class="fa fa-file-pdf-o" aria-hidden="true"> PDF
            </span> </a></span>
                            <span class="no-print pull-right" style="margin-top: -2%;margin-right: 1%;">
                                      <?php  echo export_buttons('campaignrpt', 'Campaign Report', True, false)?>
                              </span>
                        <!--<span class="no-print pull-right" style="margin-top: -3%;margin-right: -7%;"> <a
                                    class="btn btn-default btn-sm no-print pull-right" target="_blank"
                                    onclick="generateReportPdf('campaign')">
                                <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a></span>-->
                        </div>
                    </div>
                </div>
                <div class="post-area">
                    <article class="page-content">

                        <div class="system-settings">

                            <div class="row">
                                <div class="col-sm-12" id="campaignrpt">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('common_name'); ?></th><!--Name-->
                                            <th><?php echo $this->lang->line('common_type'); ?></th><!--Type-->
                                            <th><?php echo $this->lang->line('common_start_date'); ?></th>
                                            <!--Start Date-->
                                            <th><?php echo $this->lang->line('common_end_date'); ?></th><!--End Date-->
                                            <th>Assignee</th><!--End Date-->
                                            <th><?php echo $this->lang->line('common_status'); ?></th><!--Status-->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $x = 1;
                                        if (!empty($campaign)) {
                                            foreach ($campaign as $row) { ?>
                                                <tr>
                                                    <td><?php echo $x; ?></td>
                                                    <td><?php echo $row['name'] ?></td>
                                                    <td><?php echo $row['categoryDescription'] ?></td>
                                                    <td><?php echo $row['startDate'] ?></td>
                                                    <td><?php echo $row['endDate'] ?></td>
                                                    <td><?php echo $row['empnameassignee'] ?></td>
                                                    <td><?php echo $row['statusDescription'] ?></td>
                                                </tr>
                                                <?php
                                                $x++;
                                            }
                                        } else { ?>
                                            <tr>
                                                <td colspan="6"
                                                    style="text-align: center"><?php echo $this->lang->line('common_no_records_found'); ?></td>
                                                <!--No Records Found-->
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
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

