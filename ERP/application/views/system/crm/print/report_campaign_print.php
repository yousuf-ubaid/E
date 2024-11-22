<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div id="tbl_unbilled_grv">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('crm_campaign_report_re');?> </div><!--Campaign Report-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($campaign)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name');?></th><!--Name-->
                        <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_start_date');?></th><!--Start Date-->
                        <th><?php echo $this->lang->line('common_end_date');?></th><!--End Date-->
                        <th>Assignee</th><!--End Date-->
                        <th><?php echo $this->lang->line('common_status');?></th><!--Status-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
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
                    ?>
                    </tbody>
                </table>
                <?php
            } else {
                $norecfound= $this->lang->line('common_no_records_found');
                echo warning_message($norecfound);
            }
            ?>
        </div>
    </div>
</div>