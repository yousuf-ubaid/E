<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div id="tbl_unbilled_grv">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor"><strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('crm_contacts_reports');?> </div><!--Contacts Report-->
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($contact)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name');?></th><!--Name-->
                        <th><?php echo $this->lang->line('common_email');?></th><!--Email-->
                        <th><?php echo $this->lang->line('crm_mobile_no');?></th><!--Mobile No-->
                        <th><?php echo $this->lang->line('crm_home_no');?></th><!--Home No-->
                        <th><?php echo $this->lang->line('crm_role');?></th><!--Role-->
                        <th><?php echo $this->lang->line('crm_organization');?></th><!--Organization-->
                    </tr>
                    </thead>
                    <?php
                    $x =1;
                    foreach ($contact as $row) { ?>
                        <tr>
                            <td><?php echo $x; ?></td>
                            <td><?php echo $row['fullname'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td><?php echo $row['phoneMobile'] ?></td>
                            <td><?php echo $row['phoneHome'] ?></td>
                            <td><?php echo $row['occupation'] ?></td>
                            <td><?php
                                if ($row['organization'] == '') {
                                    echo $row['linkedorganization'];
                                } else {
                                    echo $row['organization'];
                                }
                                ?></td>
                        </tr>
                        <?php
                        $x++;
                    }
                    ?>
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