<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('mfq', $primaryLanguage);

$flowserveLanguagePolicy = getPolicyValues('LNG', 'All');

$jobMasterRec = get_job_master($workProcessID);
?>
<br>
<div id="" class="row review">
    <div class="col-md-12"><span class="no-print pull-right"> <button class="btn btn-xs" id="btn-pdf" type="button"
                                                                      onclick="generateReportPdf()"> <span
                        class="glyphicon glyphicon-print" aria-hidden="true"></span> </button> </span>
        <?php echo form_open('login/loginSubmit', ' id="frm_filter" class="form-horizontal" name="frm_filter" role="form"'); ?>
            <input type="hidden" id="workProcessID" name="workProcessID" value="<?php echo $workProcessID ?>">
            <input type="hidden" id="jobCardID" name="jobCardID" value="<?php echo $jobCardID ?>">
            <input type="hidden" id="workFlowID" name="workFlowID" value="<?php echo $workFlowID ?>">
            <input type="hidden" id="templateDetailID" name="templateDetailID" value="<?php echo $templateDetailID ?>">
            <input type="hidden" id="linkworkFlow" name="linkworkFlow" value="<?php echo $linkworkFlow ?>">
            <input type="hidden" id="templateMasterID" name="templateMasterID" value="<?php echo $templateMasterID ?>">
            <input type="hidden" id="type" name="type" value="<?php echo $type ?>">
            <div id="filters"> <!--load report content-->

            </div>
        <?php echo form_close(); ?>
    </div>
</div>
<div class="pb-5" id="div_print" style="padding:5px;">
    <table width="100%">
        <tbody>
        <tr>
            <td width="200px"><img alt="Logo" style="height: 130px"
                                   src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
            </td>
            <td>
                <div style="text-align: center; font-size: 17px; line-height: 26px; margin-top: 10px;">
                    <strong> <?php echo $this->common_data['company_data']['company_name'] ?></strong><br>
                    <center><?php echo $this->lang->line('manufacturing_job_card') ?><!--Job Card--></center>
                </div>
            </td>
            <td style="text-align:right;">
                <div style="text-align:right; font-size: 17px; vertical-align: top;">

                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <table width="100%" cellspacing="0" cellpadding="5" border="2" id="material_consumption">
        <tbody>
        <tr>
            <td colspan="2" width="123"><b><?php echo $this->lang->line('manufacturing_job_no') ?><!--Job No--></b></td>
            <td width="79"><?php echo $type == 2 ? $jobheader["documentCode"] : ""; ?></td>
            <td colspan="2" width="135"><b><?php echo $this->lang->line('common_customer') ?><!--Customer--></b></td>
            <td colspan="8" width="141"><?php echo $type == 2 ? $jobheader["CustomerName"] : ""; ?>
            </td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_job_date') ?><!--Job Date--></b></td>
            <td><?php echo $type == 2 ? convert_date_format($jobheader["documentDate"]) : ""; ?></td>
            <td colspan="2"><b><?php echo $this->lang->line('common_department') ?><!--Department--></b></td>
            <td colspan="8"><?php echo $type == 2 ? $jobheader["segment"] : ""; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_quote_reference') ?><!--Quote Ref.--></b></td>
            <td colspan="11" width="214"><?php echo $type == 2 ? $jobcardheader["quotationRef"] ?? '' : ""; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('common_description') ?><!--Description--></b></td>
            <td colspan="11"><?php echo $type == 2 ? $jobcardheader["description"] ?? ''  : ""; ?>
            </td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="13" style="text-align:center;"><?php echo $this->lang->line('manufacturing_material_consumption')?><!--MATERIAL CONSUMPTION--></td>
        </tr>
        <tr bgcolor="#CCCCCC" class="text-center" style="font-size: 12px;font-weight: bold; padding:5px;">
            <td colspan="2" rowspan="2"><?php echo $this->lang->line('manufacturing_material_consumption')?><!--Material Consumption--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_part_no')?><!--Part No--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_unit_of_measure_short')?><!--UoM--></td>
            <td colspan="3"><?php echo 'Estimated' ?> </td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_quantity_used')?><!--Qty Used--></td>
            <td rowspan="2"><?php echo $this->lang->line('common_unit_cost')?><!--Unit Cost--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_usage_qty')?><!--Usage Qty--></td>
         
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_material_cost')?><!--Material Cost--></td>
            <?php if($flowserveLanguagePolicy != 'FlowServe'){ ?>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_mark_up')?><!--Mark Up%--></td>
            <?php } ?>
            <td rowspan="2" colspan="3"><?php echo $this->lang->line('manufacturing_material_change')?><!--Material Charge--></td>
          
        </tr>
        <tr bgcolor="#CCCCCC" class="text-center table" style="font-size: 12px;font-weight: bold ; padding:5px;">
                <td><?php echo 'Qty'?><!--Material Charge--></td>
                <td><?php echo 'Unit Price'?><!--Material Charge--></td>
                <td><?php echo 'Material Cost'?><!--Material Charge--></td>
        </tr>
        <?php
        $qtyUsed = 0;
        $usageQty = 0;
        $unitCost = 0;
        $materialCost = 0;
        $markUp = 0;
        $materialCharge = 0;
        if (!empty($material)){
            foreach ($material as $val) {
                $qtyUsed += $val['qtyUsed'];
                $usageQty += $val['usageQty'];
                $unitCost += $val['unitCost'];
                $materialCost += $val['materialCost'];
                $markUp += $val['markUp'];
                $materialCharge += $val['materialCharge'];
                ?>
                <tr>
                    <td width="25%" colspan="2"><?php echo $val['itemDescription'] ?></td>
                    <td width=""><?php echo $val['partNo'] ?></td>
                    
                    <td><?php echo $val['uom'] ?></td>
                    <td width="" style="text-align: right"><?php echo number_format($val['bomItemQty'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                    <td width="" style="text-align: right"><?php echo number_format($val['bomItemUnit'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                    <td width="" style="text-align: right"><?php echo number_format($val['bomItemValue'], $this->common_data['company_data']['company_default_decimal']) ?></td>

                    <td width="" style="text-align: right"><?php echo $val['qtyUsed'] ?></td>
                    <td width="" style="text-align: right"><?php echo number_format($val['unitCost']) ?></td>
                    <td width="" style="text-align: right"><?php echo $val['usageQty'] ?></td>
              
                    <td width="" style="text-align: right"><?php echo number_format($val['materialCost'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                    <?php if($flowserveLanguagePolicy != 'FlowServe'){ ?>
                        <td width="" style="text-align: right"><?php echo number_format($val['markUp']) ?></td>
                    <?php } ?>
                    <td width="" colspan="3" style="text-align: right"><?php echo number_format($val['materialCharge'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                   
            <?php 
            }
        } ?>
        </tr>
        <tr>
            <td width="" colspan="<?php echo ($flowserveLanguagePolicy != 'FlowServe') ? 7 : 8 ?>" style="text-align: right"><strong><?php echo $this->lang->line('common_total') ?><!--Total--></strong></td>
            <?php if($flowserveLanguagePolicy != 'FlowServe'){ ?>
                <td width="" style="text-align: right"><strong><?php echo $qtyUsed ?></strong></td>
            <?php } ?>
            <td width="" style="text-align: right"><strong><?php echo number_format($unitCost) ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $usageQty ?></strong></td>
      
            <td width="" style="text-align: right"><strong><?php echo number_format($materialCost, $this->common_data['company_data']['company_default_decimal']) ?></strong></td>
            <?php if($flowserveLanguagePolicy != 'FlowServe'){ ?>
                <td width="" style="text-align: right"><strong><?php echo number_format($markUp) ?></strong></td>
            <?php } ?>
        
            <td width="" colspan="3"  style="text-align: right"><strong><?php echo number_format($materialCharge, $this->common_data['company_data']['company_default_decimal']) ?></strong></td>
            <!-- <td></td> -->
        </tr>

        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="13" style="text-align:center;"><?php echo $this->lang->line('manufacturing_labour_tasks')?><!--LABOUR TASKS--></td>
        </tr>
        <tr bgcolor="#CCCCCC"  class="text-center table"  style="font-size: 12px;font-weight: bold ">
            <td colspan="3" rowspan="2"><?php echo $this->lang->line('manufacturing_labour_tasks')?><!--Labour Tasks--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_activity_code')?><!--Activity Code--></td>
            <td rowspan="2"><?php echo $this->lang->line('common_uom')?><!--UoM--></td>
            <td rowspan="2"><?php echo $this->lang->line('common_department')?><!--Department--></td>
            <td colspan="3"><?php echo 'Estimated' ?><!--Department--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_unit_rate')?><!--Unit Rate--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_total_hours')?><!--Total Hours--></td>
            <td rowspan="2"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_total_value')?><!--Total Value--></td>
                
        </tr>
        <tr bgcolor="#CCCCCC" class="text-center" style="font-size: 12px;font-weight: bold ; padding:5px;">
                <td><?php echo 'Qty'?><!--Material Charge--></td>
                <td><?php echo 'Unit Price'?><!--Material Charge--></td>
                <td><?php echo 'Material Cost'?><!--Material Charge--></td>
        </tr>
        <?php

        $lt_hourlyRate = 0;
        $lt_totalHours = 0;
        $lt_usageHours = 0;
        $lt_totalValue = 0;
        if (!empty($labourTask)){
            foreach ($labourTask as $val) {
                $lt_hourlyRate += $val['hourlyRate'];
                $lt_totalHours += $val['totalHours'];
                $lt_usageHours += $val['usageHours'];
                $lt_totalValue += $val['totalValue'];
                ?>
                <tr>
                    <td colspan="3"><?php echo $val['description'] ?></td>
                    <td width=""><?php echo $val['activityCode'] ?></td>
                    <td width=""><?php echo $val['uom'] ?></td>
                    <td style=""><?php echo $val['segment'] ?></td>

                    <td style=""><?php  echo $val['bomItemQty'] ?></td>
                    <td style=""><?php  echo $val['bomItemUnit'] ?></td>
                    <td style=""><?php  echo $val['bomItemValue'] ?></td> 
        
                    <td width="" style="text-align: right"><?php echo $val['hourlyRate'] ?></td>
                    <td width="" style="text-align: right"><?php echo $val['totalHours'] ?></td>
                    <td width="" style="text-align: right"><?php echo $val['usageHours'] ?></td>
                    <td width="" style="text-align: right"><?php echo number_format($val['totalValue'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                    <td></td>
                </tr>
            <?php
            }
        } ?>
        <tr>
            <td width="" colspan="9" style="text-align: right"><strong><?php echo $this->lang->line('common_total')?><!--Total--></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $lt_hourlyRate ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $lt_totalHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $lt_usageHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($lt_totalValue, $this->common_data['company_data']['company_default_decimal']) ?></strong></td>
            <td></td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="13" style="text-align:center;"><?php echo $this->lang->line('manufacturing_overhead_cost')?><!--OVERHEAD COST--></td>
        </tr>
        <tr bgcolor="#CCCCCC"  class="text-center table" style="font-size: 12px;font-weight: bold ">
            <td rowspan="2" colspan="3"><?php echo $this->lang->line('manufacturing_overhead_cost')?><!--Overhead Cost--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_activity_code')?><!--Activity Code--></td>
            <td rowspan="2"><?php echo $this->lang->line('common_uom')?><!--UoM--></td>
            <td rowspan="2"><?php echo $this->lang->line('common_department')?><!--Department--></td>
            <td colspan="3"><?php echo 'Estimated' ?><!--Department--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_unit_rate')?><!--Unit Rate--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_total_hours')?><!--Total Hours--></td>
            <td rowspan="2"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_total_value')?><!--Total Value--></td>
            <td></td>
        </tr>
        <tr bgcolor="#CCCCCC" class="text-center" style="font-size: 12px;font-weight: bold ; padding:5px;">
                <td><?php echo 'Qty'?><!--Material Charge--></td>
                <td><?php echo 'Unit Price'?><!--Material Charge--></td>
                <td><?php echo 'Material Cost'?><!--Material Charge--></td>
        </tr>
        <?php
        $oh_hourlyRate = 0;
        $oh_totalHours = 0;
        $oh_usageHours = 0;
        $oh_totalValue = 0;
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
                    <td style=""><?php echo $val['bomItemQty'] ?></td>
                    <td style=""><?php echo $val['bomItemUnit'] ?></td>
                    <td style=""><?php echo $val['bomItemValue'] ?></td>
                    <td width="" style="text-align: right"><?php echo $val['hourlyRate'] ?></td>
                    <td width="" style="text-align: right"><?php echo $val['totalHours'] ?></td>
                    <td width="" style="text-align: right"><?php echo $val['usageHours'] ?></td>
                    <td width="" style="text-align: right"><?php echo number_format($val['totalValue'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                    <td></td>
                </tr>
            <?php 
            }
        } 
        
        ?>

        <tr>
            <td width="" colspan="9" style="text-align: right"><strong><?php echo $this->lang->line('common_total')?><!--Total--></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $oh_hourlyRate ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $oh_totalHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $oh_usageHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($oh_totalValue, $this->common_data['company_data']['company_default_decimal']) ?></strong></td>
 
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="13" style="text-align:center;"><?php echo 'THIRD PARTY SERVICE'?><!--OVERHEAD COST--></td>
        </tr>
        <tr bgcolor="#CCCCCC"  class="text-center"  style="font-size: 12px;font-weight: bold ">
            <td colspan="3" rowspan="2"><?php echo 'Third Party Cost' ?><!--Overhead Cost--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_activity_code')?><!--Activity Code--></td>
            <td rowspan="2"<?php echo $this->lang->line('common_uom')?><!--UoM--></td>
            <td rowspan="2"><?php echo $this->lang->line('common_department')?><!--Department--></td>
            <td colspan="3"><?php echo 'Estimated' ?><!--Department--></td>
            <td rowspan="2"><?php echo $this->lang->line('manufacturing_unit_rate')?><!--Unit Rate--></td>
            <?php if($flowserveLanguagePolicy != 'FlowServe'){ ?>
                <td rowspan="2"><?php echo $this->lang->line('manufacturing_total_hours')?><!--Total Hours--></td>
                <td rowspan="2"><?php echo $this->lang->line('common_usage_hours')?><!--Usage Hours--></td>
                <td rowspan="2"><?php echo $this->lang->line('manufacturing_total_value')?><!--Total Value--></td>
            <?php }else {  ?>
                <td rowspan="2" colspan="3"><?php echo $this->lang->line('manufacturing_total_value')?><!--Total Value--></td>
            <?php } ?>
            <td></td>
            
        </tr>

        <tr bgcolor="#CCCCCC" class="text-center" style="font-size: 12px;font-weight: bold ; padding:5px;">
            <td><?php echo 'Qty'?><!--Material Charge--></td>
            <td><?php echo 'Unit Price'?><!--Material Charge--></td>
            <td><?php echo 'Material Cost'?><!--Material Charge--></td>
        </tr>
        
        <?php 
         $third_oh_hourlyRate = 0;
         $third_oh_totalHours = 0;
         $third_oh_usageHours = 0;
         $third_oh_totalValue = 0;
        if (!empty($thiredparty)){
            foreach ($thiredparty as $val) {
                $third_oh_hourlyRate += $val['hourlyRate'];
                $third_oh_totalHours += $val['totalHours'];
                $third_oh_usageHours += $val['usageHours'];
                $third_oh_totalValue += $val['totalValue']; ?>
                <tr>
                    <td colspan="3"><?php echo $val['description'] ?></td>
                    <td width=""><?php echo $val['activityCode'] ?></td>
                    <td width=""><?php echo $val['uom'] ?></td>
                    <td style=""><?php echo $val['segment'] ?></td>
                    <td style=""><?php echo $val['bomItemQty'] ?></td>
                    <td style=""><?php echo $val['bomItemUnit'] ?></td>
                    <td style=""><?php echo $val['bomItemValue'] ?></td>
                    <td width="" style="text-align: right"><?php echo $val['hourlyRate'] ?></td>
                    <?php if($flowserveLanguagePolicy != 'FlowServe'){ ?>
                        <td width="" style="text-align: right"><?php echo $val['totalHours'] ?></td>
                        <td width="" style="text-align: right"><?php echo $val['usageHours'] ?></td>
                    <?php } ?>
                    <td colspan="3" width="" style="text-align: right"><?php echo number_format($val['totalValue'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                    <td></td>
                </tr>
            <?php 
            }
        } ?>
        
        <tr>
            <td width="" colspan="9" style="text-align: right"><strong><?php echo $this->lang->line('common_total')?><!--Total--></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $third_oh_hourlyRate ?></strong></td>
            <?php if($flowserveLanguagePolicy != 'FlowServe'){ ?>
                <td width="" style="text-align: right"><strong><?php echo $third_oh_totalHours ?></strong></td>
                <td width="" style="text-align: right"><strong><?php echo $third_oh_usageHours ?></strong></td>
            <?php } ?>
            <td colspan="3" width="" style="text-align: right"><strong><?php echo number_format($third_oh_totalValue, $this->common_data['company_data']['company_default_decimal']) ?></strong></td>
            <td></td>
        </tr>
        
        <?php  $mc_totalValue = 0;
        if($flowserveLanguagePolicy != 'FlowServe'){ ?>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="11" style="text-align:center;"><?php echo $this->lang->line('manufacturing_machine');?><!--MACHINE--></td>
        </tr>
        <?php
        $mc_hourlyRate = 0;
        $mc_totalHours = 0;
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
                    <td width="" style="text-align: right"><?php echo number_format($val['totalValue'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                    <td></td>
                </tr>
            <?php 
            }
        } ?>
        
    
        <tr>
            <td width="" colspan="9" style="text-align: right"><strong><?php echo $this->lang->line('common_total')?><!--Total--></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $mc_hourlyRate ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $mc_totalHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo $mc_usageHours ?></strong></td>
            <td width="" style="text-align: right"><strong><?php echo number_format($mc_totalValue, $this->common_data['company_data']['company_default_decimal']) ?></strong></td>
            <td></td>
        </tr>
        <?php } ?>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="13" style="text-align:center;"><?php echo $this->lang->line('manufacturing_item_detail')?><!--ITEM DETAIL--></td>
        </tr>
        <?php
        $totalCost = $materialCharge + $lt_totalValue + $oh_totalValue + $mc_totalValue + $third_oh_totalValue;
        ?>
        <tr>
            <td width="" colspan="2"><b><?php echo $this->lang->line('common_item')?><!--Item--></b></td>
            <td width="" colspan="2"><?php echo $type == 2 ? $jobMasterRec['itemDescription'] : ""; ?></td>
            <td width="" colspan="3"><b><?php echo $this->lang->line('common_uom')?><!--UoM--></b></td>
            <td width=""><?php echo $type == 2 ? $jobMasterRec['UnitDes'] : ""; ?></td>
            <td width="" colspan="4"><b><?php echo $this->lang->line('common_qty')?><!--Qty--></b></td>
            <td width="" colspan="1"><?php echo $type == 2 ? $jobMasterRec['qty'] : ""; ?></td>
          
        </tr>
        <tr></tr>
        <tr>
            <td width="" colspan="3">
                <span style="font-size:15px;color: #4a8cdb"><b><?php echo 'Estimated Cost'?><!--Total Cost:--> <?php echo number_format($estimateTotal, $this->common_data['company_data']['company_default_decimal']) ?></b></span>
            </td>
            <td width="" colspan="5">
                <span style="font-size:15px;color: #4a8cdb"><b><?php echo $this->lang->line('manufacturing_total_cost')?><!--Total Cost:--> <?php echo number_format($totalCost, $this->common_data['company_data']['company_default_decimal']) ?></b></span>
            </td>
            <td width="" colspan="5">
                <span style="font-size:15px;color: #4a8cdb"><b><?php echo $this->lang->line('manufacturing_cost_per_unit')?><!--Cost per unit:-->
                     <?php
                    if ($jobMasterRec['qty'] ?? null > 0) {
                        echo number_format($totalCost / $jobMasterRec['qty'], $this->common_data['company_data']['company_default_decimal']);
                    } else {
                        echo number_format(0, $this->common_data['company_data']['company_default_decimal']);
                    }
                    ?></b>
                </span>
            </td>
     
        </tr>
        </tbody>
    </table>
</div>
<script>

    var companyPolicy = '<?php echo $flowserveLanguagePolicy ?>';

    function generateReportPdf() {
        var form = document.getElementById('frm_filter');
        form.target = '_blank';
        form.action = '<?php echo site_url('MFQ_Job_Card/fetch_jobcard_print'); ?>';
        form.submit();
    }
</script>

