<?php
$jobMasterRec = get_job_master($workProcessID);
$companyLanguage = getPolicyValues('LNG', 'All');
?>
<br>
<div id="" class="row review">
    <div class="col-md-12">
        <form method="POST" id="frm_filter" class="form-horizontal" action="" name="frm_filter">
            <input type="hidden" id="workProcessID" name="workProcessID" value="<?php echo $workProcessID ?>">
            <div id="filters"> <!--load report content-->

            </div>
        </form>
    </div>
</div>
<div id="div_print" style="padding:5px;">
    <table width="100%">
        <tbody>
        <tr>
            <td width="200px"><img alt="Logo" style="height: 130px"
                                   src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
            </td>
            <td>
                <div style="text-align: center; font-size: 17px; line-height: 26px; margin-top: 10px;">
                    <strong> <?php echo $this->common_data['company_data']['company_name'] ?></strong><br>
                    <center>Job Card</center>
                </div>
            </td>
            <td style="text-align:right;">
                <div style="text-align:right; font-size: 17px; vertical-align: top;">

                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <table width="100%" cellspacing="0" cellpadding="4" border="1">
        <tbody>
        <tr>
            <td colspan="2" width="123"><b>Job No</b></td>
            <td width="79"><?php echo $jobheader["documentCode"]; ?></td>
            <td colspan="2" width="135"><b>Customer</b></td>
            <td colspan="5" width="141"><?php echo $jobheader["CustomerName"]; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>Job Date</b></td>
            <td><?php echo convert_date_format($jobheader["documentDate"]); ?></td>
            <td colspan="2"><b>Department</b></td>
            <td colspan="5"><?php echo $jobheader["segment"]; ?></td>
        </tr>
        <!--<tr>
            <td colspan="2"><b>Quote Ref.</b></td>
            <td colspan="7" width="214"><?php /*echo $jobcardheader["quotationRef"]; */?></td>
        </tr>-->
        <tr>
            <td colspan="2"><b>Description</b></td>
            <td colspan="8"><?php echo $jobheader["description"]; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b>Input Warehouse</b></td>
            <td><?php echo $jobheader["warehouseDescription"]; ?></td>
            <td colspan="2"><b>Output Warehouse</b></td>
            <td colspan="5"><?php echo $jobheader["outputWarehouseDescription"]; ?></td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="10" style="text-align:center;">MATERIAL CONSUMPTION</td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="2">Material Consumption</td>
            <td>Part No</td>
            <td>UoM</td>
            <td>Qty Used</td>
            <td>Usage Qty</td>
            <td>Unit Cost</td>
            <td>Material Cost</td>
            <td>Mark Up%</td>
            <td>Material Charge</td>
        </tr>
        <?php
        $qtyUsed = 0;
        $unitCost = 0;
        $usageQty = 0;
        $materialCost = 0;
        $markUp = 0;
        $materialCharge = 0;
        if (!empty($material)){
        foreach ($material as $val) {
        $qtyUsed += $val['qtyUsed'];
        $unitCost += $val['unitCost'];
        $materialCost += $val['materialCost'];
        $markUp += $val['markUp'];
        $materialCharge += $val['materialCharge'];
        $usageQty += $val['usageQty'];
        ?>
        <tr>
            <td width="25%" colspan="2"><?php echo $val['itemDescription'] ?></td>
            <td width=""><?php echo $val['partNo'] ?></td>
            <td><?php echo $val['uom'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['qtyUsed'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['usageQty'] ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['unitCost'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['materialCost'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['markUp'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['materialCharge'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>

            <?php }
            }
            ?>
        </tr>
        <tr>
            <td width="" colspan="4" style="text-align: right"><strong>Total</strong></td>
            <td width="" style="text-align: right"><strong><?php echo $qtyUsed ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $usageQty ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($unitCost,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($materialCost,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($markUp,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($materialCharge,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
        </tr>

        <?php $lbmaterialCharge = 0; if($companyLanguage != 'FlowServe'){ ?>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="10" style="text-align:center;">PACKAGING</td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="2">Packaging</td>
            <td>Part No</td>
            <td>UoM</td>
            <td>Qty Used</td>
            <td>Usage Qty</td>
            <td>Unit Cost</td>
            <td>Material Cost</td>
            <td>Mark Up%</td>
            <td>Material Charge</td>
        </tr>
        <?php
        $qtyUsed = 0;
        $unitCost = 0;
        $usageQty = 0;
        $lbmaterialCost = 0;
        $lbmarkUp = 0;
        $lbmaterialCharge = 0;
        if (!empty($material)){
        foreach ($packaging as $val) {
        $qtyUsed += $val['qtyUsed'];
        $unitCost += $val['unitCost'];
        $materialCost += $val['materialCost'];
        $markUp += $val['markUp'];
        $lbmaterialCharge += $val['materialCharge'];
        $usageQty += $val['usageQty'];
        ?>
        <tr>
            <td width="25%" colspan="2"><?php echo $val['itemDescription'] ?></td>
            <td width=""><?php echo $val['partNo'] ?></td>
            <td><?php echo $val['uom'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['qtyUsed'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['usageQty'] ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['unitCost'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['materialCost'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['markUp'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['materialCharge'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>

            <?php }
            }
            ?>
        </tr>
        <tr>
            <td width="" colspan="4" style="text-align: right"><strong>Total</strong></td>
            <td width="" style="text-align: right"><strong><?php echo $qtyUsed ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $usageQty ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($unitCost,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($lbmaterialCost,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($lbmarkUp,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($lbmaterialCharge,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
        </tr>
        <?php } ?>

        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="10" style="text-align:center;">LABOUR TASKS</td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="3">Labour Tasks</td>
            <td>Activity Code</td>
            <td>UoM</td>
            <td>Department</td>
            <td>Unit Rate</td>
            <td>Total Hours</td>
            <td>Usage Hours</td>
            <td>Total Value</td>
        </tr>
        <?php
        $lt_hourlyRate = 0;
        $lt_totalHours = 0;
        $lt_totalValue = 0;
        $lt_usageHours = 0;
        if (!empty($labourTask)){
        foreach ($labourTask as $val) {
        $lt_hourlyRate += $val['hourlyRate'];
        $lt_totalHours += $val['totalHours'];
        $lt_totalValue += $val['totalValue'];
        $lt_usageHours += $val['usageHours'];
        ?>
        <tr>
            <td colspan="3"><?php echo $val['description'] ?></td>
            <td width=""><?php echo $val['activityCode'] ?></td>
            <td width=""><?php echo $val['uom'] ?></td>
            <td style=""><?php echo $val['segment'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['hourlyRate'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['totalHours'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['usageHours'] ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['totalValue'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
            <?php }
            }
            ?>
        </tr>
        <tr>
            <td width="" colspan="6" style="text-align: right"><strong>Total</strong></td>
            <td width="" style="text-align: right"><strong><?php echo $lt_hourlyRate ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $lt_totalHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $lt_usageHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($lt_totalValue,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="10" style="text-align:center;">OVERHEAD COST</td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="3">Overhead Cost</td>
            <td>Activity Code</td>
            <td>UoM</td>
            <td>Department</td>
            <td>Unit Rate</td>
            <td>Total Hours</td>
            <td>Usage Hours</td>
            <td>Total Value</td>
        </tr>
        <?php
        $oh_hourlyRate = 0;
        $oh_totalHours = 0;
        $oh_totalValue = 0;
        $oh_usageHours = 0;
        if (!empty($overhead)){
        foreach ($overhead as $val) {
        $oh_hourlyRate += $val['hourlyRate'];
        $oh_totalHours += $val['totalHours'];
        $oh_usageHours += $val['usageHours'];
        $oh_totalValue += $val['totalValue']; ?>
        <tr>
            <td colspan="3"><?php echo $val['description'] ?></td>
            <td width=""><?php echo $val['activityCode'] ?></td>
            <td width=""><?php echo $val['uom'] ?></td>
            <td style=""><?php echo $val['segment'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['hourlyRate'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['totalHours'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['usageHours'] ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['totalValue'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
            <?php }
            }
            ?>
        </tr>
        <tr>
            <td width="" colspan="6" style="text-align: right"><strong>Total</strong></td>
            <td width="" style="text-align: right"><strong><?php echo $oh_hourlyRate ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $oh_totalHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $oh_usageHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($oh_totalValue,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
        </tr>
        

        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="10" style="text-align:center;">Third Party COST</td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="3">Third Party Cost</td>
            <td>Activity Code</td>
            <td>UoM</td>
            <td>Department</td>
            <td>Unit Rate</td>
            <td>Total Hours</td>
            <td>Usage Hours</td>
            <td>Total Value</td>
        </tr>
        <?php
        $ohth_hourlyRate = 0;
        $ohth_totalHours = 0;
        $ohth_totalValue = 0;
        $ohth_usageHours = 0;
        if (!empty($thirdparty)){
        foreach ($thirdparty as $val) {
        $ohth_hourlyRate += $val['hourlyRate'];
        $ohth_totalHours += $val['totalHours'];
        $ohth_usageHours += $val['usageHours'];
        $ohth_totalValue += $val['totalValue']; ?>
        <tr>
            <td colspan="3"><?php echo $val['description'] ?></td>
            <td width=""><?php echo $val['activityCode'] ?></td>
            <td width=""><?php echo $val['uom'] ?></td>
            <td style=""><?php echo $val['segment'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['hourlyRate'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['totalHours'] ?></td>
            <td width="" style="text-align: right"><?php echo $val['usageHours'] ?></td>
            <td width="" style="text-align: right"><?php echo number_format($val['totalValue'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
            <?php }
            }
            ?>
        </tr>
        <tr>
            <td width="" colspan="6" style="text-align: right"><strong>Total</strong></td>
            <td width="" style="text-align: right"><strong><?php echo $ohth_hourlyRate ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $ohth_totalHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $ohth_usageHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($ohth_totalValue,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
        </tr>

        <?php $mc_totalValue = 0; if($companyLanguage != 'FlowServe'){ ?>
            <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
                <td colspan="10" style="text-align:center;">MACHINE</td>
            </tr>
            <?php
            $mc_hourlyRate = 0;
            $mc_totalHours = 0;
            $mc_totalValue = 0;
            $mc_usageHours = 0;
            if (!empty($machine)){
            foreach ($machine as $val) {
            $mc_hourlyRate += $val['hourlyRate'];
            $mc_totalHours += $val['totalHours'];
            $mc_usageHours += $val['usageHours'];
            $mc_totalValue += $val['totalValue']; ?>
            <tr>
                <td colspan="3"><?php echo $val['assetDescription'] ?></td>
                <td width=""><?php echo $val['activityCode'] ?></td>
                <td width=""><?php echo $val['uom'] ?></td>
                <td style=""><?php echo $val['segment'] ?></td>
                <td width="" style="text-align: right"><?php echo $val['hourlyRate'] ?></td>
                <td width="" style="text-align: right"><?php echo $val['totalHours'] ?></td>
                <td width="" style="text-align: right"><?php echo $val['usageHours'] ?></td>
                <td width="" style="text-align: right"><?php echo number_format($val['totalValue'],$this->common_data["company_data"]["company_default_decimal"]) ?></td>
                <?php }
                }
                ?>
            </tr>
            <tr>
                <td width="" colspan="6" style="text-align: right"><strong>Total</strong></td>
                <td width="" style="text-align: right"><strong><?php echo $mc_hourlyRate ?></strong></td>
                <td width="" style="text-align: right"><strong><?php echo $mc_totalHours ?></strong></td>
                <td width="" style="text-align: right"><strong><?php echo $mc_usageHours ?></strong></td>
                <td width="" style="text-align: right"><strong><?php echo number_format($mc_totalValue,$this->common_data["company_data"]["company_default_decimal"]) ?></strong></td>
            </tr>

        <?php } ?>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="10" style="text-align:center;">ITEM DETAIL</td>
        </tr>
        <?php
        $totalCost = $materialCharge + $lt_totalValue + $oh_totalValue + $ohth_totalValue + $mc_totalValue + $lbmaterialCharge;
        ?>
        <tr>
            <td width="" colspan="2"><b>Item</b></td>
            <td width="" colspan="2"><?php echo $jobheader['itemDescription']; ?></td>
            <td width="" colspan="2"><b>UoM</b></td>
            <td width=""><?php echo $jobheader['UnitDes']; ?></td>
            <td width=""><b>Qty</b></td>
            <td width="" colspan="2"><?php echo $jobheader['qty']; ?></td>
        </tr>
        <tr>
            <td width="" colspan="3" style="font-size:15px !important;color: #4a8cdb">
                <b>Estimated Cost: <?php echo number_format($estimateTotal, $this->common_data["company_data"]["company_default_decimal"]) ?></b>
            </td>
            <td width="" colspan="4"><span
                    style="font-size:15px;color: #4a8cdb"><b>Total Cost: <?php echo number_format($totalCost, $this->common_data["company_data"]["company_default_decimal"]) ?></b></span>
            </td>
            <td width="" colspan="4"><span style="font-size:15px;color: #4a8cdb"><b>Cost per unit: <?php
                        if ($jobheader['qty'] > 0) {
                            echo number_format($totalCost / $jobheader['qty'], 2);
                        } else {
                            echo '0.00';
                        }
                        ?></b></span></td>
        </tr>
        <tr>
            <td colspan="5" style="font-size:15px;color: #4a8cdb"><b>Exceeded Cost Per Unit</b></td>
            <!-- <td colspan="5"  style="font-size:15px;color: #4a8cdb"><?php // echo number_format($jobheader['exceededCost'], $this->common_data["company_data"]["company_default_decimal"]) ?> </td> -->
            <?php if($jobheader['qty'] > 0 ) { ?>
                <td colspan="5"  style="font-size:15px;color: #4a8cdb"><?php echo number_format((($totalCost / $jobheader['qty']) - $estimateTotal), $this->common_data["company_data"]["company_default_decimal"]) ?> </td>
            <?php } else { echo '0.00'; } ?>
        </tr>
        </tbody>
    </table>
</div>
<script>
    function generateReportPdf() {
        var form = document.getElementById('frm_filter');
        form.target = '_blank';
        form.action = '<?php echo site_url('MFQ_Job/fetch_job_approval_print'); ?>';
        form.submit();
    }
</script>
