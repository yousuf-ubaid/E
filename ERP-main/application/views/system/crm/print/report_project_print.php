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
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('crm_project_report_re');?></div><!--Project Report-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($project)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name');?></th><!--Name-->
                        <th>Category</th><!--Name-->
                        <th><?php echo $this->lang->line('common_description');?></th><!--Description-->
                        <th><?php echo $this->lang->line('common_start_date');?></th><!--Start Date-->
                        <th><?php echo $this->lang->line('common_end_date');?></th><!--End Date-->
                        <th>User Responsible</th>
                        <th><?php echo $this->lang->line('common_status');?></th><!--Status-->
                        <th>Completed %</th>
                        <th>Completed Value</th>
                        <th><?php echo $this->lang->line('common_value');?></th><!--Value-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
                    $total = 0;
                    foreach ($project as $row) {
                        $pipelinePercentage = $this->db->query("SELECT SUM(probability) as probability FROM srp_erp_crm_pipelinedetails where sortOrder <= (SELECT sortOrder FROM srp_erp_crm_pipelinedetails where pipeLineDetailID = {$row['pipelineStageID']}) AND pipeLineID = {$row['pipelineID']}")->row('probability');
                        if(!$pipelinePercentage) { $pipelinePercentage = 0; }
                        $currency =$row['CurrencyCode'];
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
                    ?>
                    </tbody>
                    <tfoot >
                    <tr>
                        <td style="min-width: 85%  !important" class="text-right sub_total" colspan="10">
                            <?php echo $this->lang->line('common_total');?>  <?php echo "( $currency ) "?> </td><!--Total-->
                        <td style="min-width: 15% !important"
                            class="text-right total"><?php echo number_format($total, 2); ?></td>
                    </tr>
                    </tfoot>
                </table>
                <?php
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*No Records Found!*/
            }
            ?>
        </div>
    </div>
</div>