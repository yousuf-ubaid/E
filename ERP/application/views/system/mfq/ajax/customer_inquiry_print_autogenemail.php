<div id="div_print" style="padding:5px;">
    <table width="100%">
        <tbody>
        <tr>
            <?php echo $companydetails['company_logo']?>
            <td width="200px"><img alt="Logo" style="height: 130px"
                                   src="<?php echo mPDFImage.$companydetails['company_logo']; ?>">

                    </td>
            <td>
                <div style="text-align: center; font-size: 17px; line-height: 26px; margin-top: 10px;">
                    <strong> <?php echo $companydetails['company_name'] ?></strong><br>
                    <center>Customer Inquiry</center>
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
            <td colspan="2"><b>Inquiry Date</b></td>
            <td colspan="7" width="79"><?php echo $header["documentDate"] ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>Inquiry Code</b></td>
            <td colspan="7"><?php echo $header["ciCode"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>Client Reference No</b></td>
            <td colspan="7"><?php echo $header["referenceNo"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>Customer</b></td>
            <td colspan="7" width="214">&nbsp;<?php echo $header["CustomerName"]; ?></td>

        </tr>
        <tr>
            <td colspan="2"><b>Contact Person Name</b></td>
            <td colspan="1" width="214"><?php echo $header["contactPersonIN"]; ?></td>
            <td colspan="2"><b>Phone No</b></td>
            <td colspan="1" width="214">&nbsp;<?php echo $header["customerPhoneNocustomer"]; ?></td>
            <td colspan="2"><b>Email</b></td>
            <td colspan="1" width="214">&nbsp;<?php echo $header["customerEmailcustomer"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>Description</b></td>
            <td colspan="7" width="214"><?php echo $header["description"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>Segment</b></td>
            <td colspan="7" width="214"><?php echo $header["department"]; ?></td>
        </tr>

        <tr>
            <td colspan="2"><b>Actual Submission Date</b></td>
            <td colspan="7" width="214"><?php echo $header["deliveryDate"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>Planned Submission Date</b></td>
            <td colspan="7" width="214"><?php echo $header["dueDate"]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><b>Delay In Days</b></td>
            <td colspan="7" width="214"><?php echo $header["noofdaysdelaydeliverydue"]; ?></td>
        </tr>


        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:center;">Engineering	</td>
            <td colspan="2" style="text-align:center;">Purchasing</td>
            <td colspan="2" style="text-align:center;">Production	</td>
            <td colspan="3" style="text-align:center;">QA/QC	</td>
        </tr>
        <tr style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:left;">&nbsp;Responsible :<?php echo $header["engineeringResponsibleEmpName"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;Responsible :<?php echo $header["purchasingResponsibleEmpName"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;Responsible :<?php echo $header["productionResponsibleEmpName"]; ?></td>
            <td colspan="3" style="text-align:left;">&nbsp;Responsible :<?php echo $header["qaqcResponsibleEmpName"]; ?></td>
        </tr>
        <tr style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:left;">&nbsp;End Date :<?php echo $header["engineeringEndDate"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;End Date :<?php echo $header["purchasingEndDate"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;End Date :<?php echo $header["productionEndDate"]; ?></td>
            <td colspan="3" style="text-align:left;">&nbsp;End Date :<?php echo $header["QAQCEndDate"]; ?></td>
        </tr>
        <tr style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:left;">&nbsp;Submission Date :<?php echo $header["engineeringSubmissionDatecon"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;Submission Date :<?php echo $header["purchasingSubmissionDatecon"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;Submission Date :<?php echo $header["productionSubmissionDatecon"]; ?></td>
            <td colspan="3" style="text-align:left;">&nbsp;Submission Date :<?php echo $header["QAQCSubmissionDatecon"]; ?></td>
        </tr>
        <tr style="font-size: 12px;font-weight: bold ">
            <td colspan="2" style="text-align:left;">&nbsp;Delay In Days :<?php echo $header["Engineeringnoofdays"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;Delay In Days :<?php echo $header["purchasingnoofdays"]; ?></td>
            <td colspan="2" style="text-align:left;">&nbsp;Delay In Days :<?php echo $header["productionnoofdays"]; ?></td>
            <td colspan="3" style="text-align:left;">&nbsp;Delay In Days :<?php echo $header["qaqcnoofdays"]; ?></td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="9" style="text-align:center;">ITEM DETAIL</td>
        </tr>
        <tr bgcolor="#CCCCCC" style="font-size: 12px;font-weight: bold ">
            <td colspan="2">Item Code</td>
            <td colspan="3">Item Description</td>
            <td>Department</td>
            <td>Delivery Date</td>
            <td>UOM</td>
            <td>Qty</td>
        </tr>
        <?php
        $qtyUsed = 0;
        if (!empty($itemDetail)) {
            foreach ($itemDetail as $val) {
                ?>
                <tr>
                    <td width="25%" colspan="2"><?php echo $val['itemSystemCode'] ?></td>
                    <td width="25%" colspan="3"><?php echo $val['itemDescription'] ?></td>
                    <td><?php echo $val['segment'] ?></td>
                    <td><?php echo $val['expectedDeliveryDate'] ?></td>
                    <td width=""><?php echo $val['UnitDes'] ?></td>
                    <td width="" class="text-right"><?php echo $val['expectedQty'] ?></td>
                </tr>
            <?php }
        }else{
            ?>
            <tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
