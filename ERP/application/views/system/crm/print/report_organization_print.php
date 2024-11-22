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
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('crm_organization_report_re');?> </div><!--Organization Report-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($organization)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name');?></th><!--Name-->
                        <th><?php echo $this->lang->line('common_email');?></th><!--Email-->
                        <th><?php echo $this->lang->line('crm_phone_no');?></th><!--Phone No-->
                        <th><?php echo $this->lang->line('common_address');?></th><!--Address-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
                    foreach ($organization as $row) { ?>
                        <tr>
                            <td><?php echo $x; ?></td>
                            <td><?php echo $row['Name'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td><?php echo $row['telephoneNo'] ?></td>
                            <td><?php echo $row['shippingAddress'] ?></td>
                        </tr>
                        <?php
                        $x++;
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);
            }
            ?>
        </div>
    </div>
</div>