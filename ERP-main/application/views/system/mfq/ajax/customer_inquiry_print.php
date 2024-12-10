<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div id="div_print" style="padding:5px;">
    <table width="100%">
        <tbody>
        <tr>
            <td width="200px"><img alt="Logo" style="height: 130px"
                     src="<?php echo $logo . $this->common_data['company_data']['company_logo']; ?>"></td>
            <td>
                <div style="text-align: center; font-size: 17px; line-height: 26px; margin-top: 10px;">
                    <!-- <strong> <?php echo $this->common_data['company_data']['company_name'] ?></strong><br> -->
                    <center><?php echo $this->lang->line('manufacturing_customer_inquiry_simple') ?><!--Customer Inquiry--></center>
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
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_inquiry_date') ?><!--Inquiry Date--></b></td>
            <td colspan="9" width="79"><?php echo $header["documentDate"] ?></td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_inquiry_code') ?><!--Inquiry Code--></b></td>
            <td colspan="9"><?php echo $header["ciCode"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_client_reference_no') ?><!--Client Reference No--></b></td>
            <td colspan="9"><?php echo $header["referenceNo"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('common_customer') ?><!--Customer--></b></td>
            <td colspan="9" width="214">&nbsp;<?php echo $header["CustomerName"]; ?></td>

        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('common_currency') ?><!--Currency--></b></td>
            <td colspan="9" width="214">&nbsp;<?php echo $header["CurrencyCode"]; ?></td>

        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_contact_person_name') ?><!--Contact Person Name--></b></td>
            <td colspan="2" width="214"><?php echo $header["contactPersonIN"]; ?></td>
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_phone_no') ?><!--Phone No--></b></td>
            <td colspan="1" width="214">&nbsp;<?php echo $header["customerPhoneNocustomer"]; ?></td>
            <td colspan="2"><b><?php echo $this->lang->line('common_email') ?><!--Email--></b></td>
            <td colspan="2" width="214">&nbsp;<?php echo $header["customerEmailcustomer"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('common_description') ?><!--Description--></b></td>
            <td colspan="9" width="214"><?php echo $header["description"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('common_segment') ?><!--Segment--></b></td>
            <td colspan="9" width="214"><?php echo $header["department"]; ?></td>
        </tr>

        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_actual_submission_date') ?><!--Actual Submission Date--></b></td>
            <td colspan="9" width="214"><?php echo $header["deliveryDate"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_planned_submission_date') ?><!--Planned Submission Date--></b></td>
            <td colspan="9" width="214"><?php echo $header["dueDate"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b><?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--></b></td>
            <td colspan="9" width="214"><?php echo $header["noofdaysdelaydeliverydue"]; ?></td>
        </tr>


        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:center;"><?php echo $this->lang->line('manufacturing_engineering') ?><!--Engineering-->	</td>
            <td colspan="2" style="text-align:center;"><?php echo $this->lang->line('manufacturing_purchasing') ?><!--Purchasing--></td>
            <td colspan="2" style="text-align:center;"><?php echo $this->lang->line('manufacturing_production') ?><!--Production-->	</td>
            <td colspan="3" style="text-align:center;"><?php echo $this->lang->line('manufacturing_quality_assurance_or_quality_control') ?><!--QA/QC-->	</td>
            <td colspan="2" style="text-align:center;"><?php echo $this->lang->line('manufacturing_Finance') ?><!--QA/QC-->	</td>
        </tr>
        <tr style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--> :<?php echo $header["engineeringResponsibleEmpName"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--> :<?php echo $header["purchasingResponsibleEmpName"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--> :<?php echo $header["productionResponsibleEmpName"]; ?></td>
            <td colspan="3" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--> :<?php echo $header["qaqcResponsibleEmpName"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_responsible') ?><!--Responsible--> :<?php echo $header["financeResponsibleEmpName"]; ?></td>
        </tr>
        <tr style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('common_end_date') ?><!--End Date--> :<?php echo $header["engineeringEndDate"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('common_end_date') ?><!--End Date--> :<?php echo $header["purchasingEndDate"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('common_end_date') ?><!--End Date--> :<?php echo $header["productionEndDate"]; ?></td>
            <td colspan="3" style="text-align:left;">&nbsp;<?php echo $this->lang->line('common_end_date') ?><!--End Date--> :<?php echo $header["QAQCEndDate"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('common_end_date') ?><!--End Date--> :<?php echo $header["financeEndDate"]; ?></td>
        </tr>
        <tr style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--> :<?php echo $header["engineeringSubmissionDatecon"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--> :<?php echo $header["purchasingSubmissionDatecon"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--> :<?php echo $header["productionSubmissionDatecon"]; ?></td>
            <td colspan="3" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--> :<?php echo $header["QAQCSubmissionDatecon"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_submission_date') ?><!--Submission Date--> :<?php echo $header["financeSubmissionDate"]; ?></td>
        </tr>
        <tr style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--> :<?php echo $header["Engineeringnoofdays"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--> :<?php echo $header["purchasingnoofdays"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--> :<?php echo $header["productionnoofdays"]; ?></td>
            <td colspan="3" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--> :<?php echo $header["qaqcnoofdays"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;<?php echo $this->lang->line('manufacturing_delay_in_days') ?><!--Delay In Days--> :<?php echo $header["financenoofdays"]; ?></td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="11" class="text-uppercase" style="text-align:center;"><?php echo $this->lang->line('manufacturing_item_detail') ?><!--ITEM DETAIL--></td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="3"><?php echo $this->lang->line('manufacturing_item_code') ?><!--Item Code--></td>
            <td colspan="4"><?php echo $this->lang->line('common_item_description') ?><!--Item Description--></td>
            <td><?php echo $this->lang->line('common_department') ?><!--Department--></td>
            <td>Delivery Period (Week)<!--Delivery Date--></td>
            <td><?php echo $this->lang->line('common_uom') ?><!--UOM--></td>
            <td><?php echo $this->lang->line('common_qty') ?><!--Qty--></td>
        </tr>
        <?php
        $qtyUsed = 0;
        if (!empty($itemDetail)) {
            foreach ($itemDetail as $val) {
                ?>
                <tr>
                    <td width="25%" colspan="3"><?php echo $val['itemSystemCode'] ?></td>
                    <td width="25%" colspan="4"><?php echo $val['itemDescription'] ?></td>
                    <td><?php echo $val['segment'] ?></td>
                    <td style="text-align: right"><?php echo $val['expectedDeliveryWeeks'] ?></td>
                    <td width=""><?php echo $val['UnitDes'] ?></td>
                    <td width="" class="text-right"><?php echo $val['expectedQty'] ?></td>
                </tr>
            <?php }
        }else{
            ?>
            <tr class="danger"><td colspan="11" class="text-center"><b><?php echo $this->lang->line('common_no_records_found') ?><!--No Records Found--></b></td></tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>

<br>
<div class="table-responsive">
    <br>
    <table style="width: 100%">
        <tbody>
        <?php if ($ALD_policyValue == 1) { 
            $created_user_designation = designation_by_empid($header['createdUserID']);
            $confirmed_user_designation = designation_by_empid($header['confirmedByEmpID']);
            ?>
                <tr>
                <td style="width:30%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:2%;"><strong>:</strong></td>
                <td style="width:70%;"><?php echo $header['createdUserName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $header['createdDateTime']; ?></td>
            </tr>
        <?php if($header['confirmedYN']==1){ ?>
            <tr>
                <td style="width:30%;"><b>Confirmed By </b></td>
                <td><strong>: </strong></td>
                <td style="width:70%;"><?php echo $header['confirmedByName'] . ' (' . $created_user_designation['DesDescription'] . ') on ' . $header['confirmedDate'];?></td>
            </tr>
        <?php } ?>
            <?php if(!empty($approver_details)) {
                foreach ($approver_details as $val) {
                    echo '<tr>
                            <td style="width:30%;"><b>Level '. $val['approvalLevelID'] .' Approved By</b></td>
                            <td><strong>:</strong></td>
                            <td style="width:70%;"> '. $val['Ename2'] .' ('. $val['DesDescription'] .') on '.$val['approvedDate'].'</td>
                        </tr>';
                }
            }
        } ?>
        </tbody>
    </table>
</div>