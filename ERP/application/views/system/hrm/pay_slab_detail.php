<?php
$paySlabDetail = $this->db->query("SELECT * FROM srp_erp_slabsdetail WHERE slabsMasterID = " . $output['slabsMasterID'] . "")->result_array();

?>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<style>
    .declarationTable td:not(:first-child) {
        width: 100px !important;
    }

    .declarationTable th:not(:first-child) {
        width: 100px !important;
    }

    /*.assetRegisterTable td:last-child,.assetRegisterTable th:last-child {
        background-color: rgba(119, 119, 119, 0.33);
    }*/

    /*.assetRegisterTable tr:not(:first-child):hover td:not(:last-child) {*/
    .declarationTable tbody td:not(:first-child):not(:last-child):hover {
        cursor: pointer !important;
        background-color: #DEDEDE;
    }
</style>
<div class="masterContainer">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed" style="background-color: #EAF2FA;">
                <tr>
                    <td style="width: 110px;"><?php echo $this->lang->line('hrms_payroll_slab_code');?><!--Slab Code--></td>
                    <td class="bgWhite"><strong><?php echo $output['documentSystemCode'] ?></strong></td>
                    <td colspan="2"><?php echo $this->lang->line('common_currency');?><!--Currency--></td>
                    <td class="bgWhite"><strong><?php echo $output['transactionCurrency'] ?></strong></td>
                    <td><?php echo $this->lang->line('common_description');?><!--Description--></td>
                    <td class="bgWhite" colspan="2"><strong><?php echo $output['Description'] ?></strong></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br>
<?php
/*if (!empty($declarationdetailTable)) {
    if ($declarationMasterTable['confirmedYN'] == '') { */ ?>
<h4>
    <?php echo $this->lang->line('hrms_payroll_slab_detail');?><!--Slab Detail-->
    <button type="button" class="btn btn-primary pull-right"
            onclick="save_salaryDeclarationDetail()"><i
            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_detail');?><!--Add Detail-->
    </button>
</h4>
<br>
<div class="row">
    <div class="col-md-12">
        <div style="overflow: auto">
            <table class="<?php echo table_class() ?>">
                <tr>
                    <td style="font-weight: 700; text-align: center">#</td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_start_range_amount');?><!--Range Start Amount--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_end_range_amount');?><!--Range End Amount--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('common_percentage');?><!--Percentage--> %</td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('hrms_payroll_threshold_amount');?><!--Threshold Amount--></td>
                    <td style="font-weight: 700; text-align: center"><?php echo $this->lang->line('common_action');?><!--Action--></td>
                </tr>
                <?php
                $i = 1;
                if (!empty($paySlabDetail)) {
                    foreach ($paySlabDetail as $val) {
                        ?>
                        <tr>
                            <td ><?php echo $i; ?></td>
                            <td style="text-align: right"><?php echo number_format($val['rangeStartAmount'], 2) ?></td>
                            <td style="text-align: right"><?php echo number_format($val['rangeEndAmount'], 2) ?></td>
                            <td style="text-align: center"><?php echo $val['percentage']; ?></td>
                            <td style="text-align: right"><?php echo number_format($val['thresholdAmount'],2) ?></td>
                            <td style="text-align: right"><a
                                    onclick="delete_item(<?php echo $val['slabsDetailID']; ?>,<?php echo $val['slabsMasterID']; ?>);"><span
                                        class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else { ?>
                    <tr>
                        <td colspan="5" style="text-align: center"><?php echo $this->lang->line('common_no_records_found');?><!--No records Found--></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
            if (!empty($declarationdetailTable)) {
                if ($declarationMasterTable['confirmedYN'] != 1) { ?>
                    <div id="sdd_footer" style="margin: 16px 0px 1px 0px;" class="pull-right">
                        <button class="btn btn-success submitWizard" onclick="confirmSalaryDeclaration()">Confirm
                        </button>
                    </div>
                <?php } else {
                    if ($declarationMasterTable['confirmedYN'] == 1 && $declarationMasterTable['approvedYN'] == 1) {
                        echo '<div class="text-success pull-right" style="margin: 16px 0px 1px 0px;"><i class="fa fa-check"></i> Confirmed &nbsp;&nbsp;&nbsp;&nbsp; &amp; &nbsp;&nbsp;&nbsp;&nbsp; <i class="fa fa-check"></i> Approved </div>  ';
                    } else {
                        echo '<div class="text-success pull-right" style="margin: 16px 0px 1px 0px;"><i class="fa fa-check"></i> Confirmed</div>  ';
                    }

                }
            } ?>
        </div>
    </div>
</div>