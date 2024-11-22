<div style="margin-top: 5%" > &nbsp; </div>

<div class="table-responsive">
    <table style="width: 100%" border="0px">
        <tbody>
            <tr>
                <td style="width:100%;" valign="top" align="center">
                    <table border="0px">                   
                        <tr>
                            <td><h5 style="margin-bottom: 0px;font-weight:bold">LOAN REQUEST FORM</h45></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table style="width: 100%" border="0px">
        <tbody>
            <tr>
                <td style="width:60%;" valign="top">
                    <table  style="width: 100%" border="0px">                        
                        <tr>
                            <td><h5 style="margin-bottom: 0px;font-size:13px">Date: <?php echo $masterDataReq->loanDate; ?></h5></td>
                        </tr>
                        <tr>
                            <td><h5 style="margin-bottom: 0px;font-size:12px">Ref No: <?php echo $masterDataReq->docCode; ?></h5></td>
                        </tr>
                    </table>
                </td>
                <td style="width:40%;" align="right">
                    <table style="width: 100%">
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 70px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<table style="width: 100%">
        <tbody>
            <tr><td style="border-bottom:1px solid;border-color:#ccc">&nbsp;</td></tr>
        </tbody>
</table>                

<div class="table-responsive" style="margin-top: 2%">
    <table class="st-1" style="width: 100%;font-size:14px">
        <tbody>
        <tr>
            <td style="width:18%;font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Employee Name</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:30%;font-size:12px;"><?php echo $masterDataReq->employeename; ?></td>
            <td style="width:18%;font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Employee ID </strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:30%;font-size:12px;"><?php echo $masterDataReq->EmpSecondaryCode; ?></td>
        </tr>
        <tr>
            <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Designation</strong></td>
            <td><strong>:</strong></td>
            <td style="font-size:12px;"><?php echo $masterDataReq->DesDescription; ?></td>
            <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Department</strong></td>
            <td><strong>:</strong></td>
            <td style="font-size:12px;"><?php echo $masterDataReq->DepartmentDes; ?></td>
        </tr>
        <tr>
            <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Date of Joining</strong></td>
            <td><strong>:</strong></td>
            <td style="font-size:12px;"><?php echo $masterDataReq->empdateofjoin; ?></td>
            <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Passport no.</strong></td>
            <td><strong>:</strong></td>
            <td style="font-size:12px;"><?php echo $masterDataReq->EPassportNO; ?></td>
        </tr>
        <tr>
            <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Loan Amount Requested</strong></td>
            <td><strong>:</strong></td>
            <td style="font-size:12px;"><?php echo $masterDataReq->amount; ?> (<?php echo $masterDataReq->transactionCurrency; ?>)</td>
        </tr>       
        <tr>
            <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Reason for request </strong></td>
            <td><strong>:</strong></td>
            <td style="font-size:12px;"><?php echo $masterDataReq->loanDescription; ?></td>
        </tr> 
        
        </tbody>
    </table>
</div>


<div class="table-responsive" style="margin-top: 2%">
    <table style="width: 100%;"  cellspacing="0" cellpadding="4" >
        <tr>
            <td>
                <table style="background:#f4f4f4">
                    <tr>
                        <td style="text-align:center;font-size:12px;width:50%;padding:10px">PREVIOUS LOAN HISTORY (if applicable) <br>TO BE FILLED BY ACCOUNTANT ONLY </td>
                        <td style="text-align:center;font-size:12px;width:50%;padding:10px">LOAN APPROVED</td>
                    </tr>
                </table>
            </td>    
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td style="width:18%;font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Last loan taken (AED) </strong></td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:30%;font-size:12px;"><?php echo "" ?></td>
                        <td style="width:18%;font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Amount </strong></td>
                        <td style="width:2%;"><strong>:</strong></td>
                        <td style="width:30%;font-size:12px;"><?php echo $masterDataReq->amount; ?> (<?php echo $masterDataReq->transactionCurrency; ?>)</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Last loan taken (YYYY-MM-DD)</strong></td>
                        <td><strong>:</strong></td>
                        <td style="font-size:12px;"><?php echo "" ?></td>
                        <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>No. of installment(s) </strong></td>
                        <td><strong>:</strong></td>
                        <td style="font-size:12px;"><?php echo $masterDataReq->numberOfInstallment; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Balance Excluding last loan (AED) </strong></td>
                        <td><strong>:</strong></td>
                        <td style="font-size:12px;"><?php echo "" ?></td>
                        <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>From (YYYY-MM-DD) </strong></td>
                        <td><strong>:</strong></td>
                        <td style="font-size:12px;"><?php echo $masterDataReq->deductionStartingDate; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Recovered  Amount (AED)</strong></td>
                        <td><strong>:</strong></td>
                        <td style="font-size:12px;"><?php echo "" ?></td>
                        <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>To (YYYY-MM-DD) </strong></td>
                        <td><strong>:</strong></td>
                        <td style="font-size:12px;"><?php echo $masterDataReq->loanEndDate; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Balance to be recovered (AED) </strong></td>
                        <td><strong>:</strong></td>
                        <td style="font-size:12px;"><?php echo "" ?></td>
                        <td style="font-size:12px;background:#f4f4f4;padding:6px 10px"><strong>Monthly installment(s) </strong></td>
                        <td><strong>:</strong></td>
                        <td style="font-size:12px;"><?php echo $masterDataReq->monthlyinstallment; ?> (<?php echo $masterDataReq->transactionCurrency; ?>)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

                <table>
                    <tr>
                        <td style="height:30px">&nbsp;</td>
                    </tr>
                </table>

<!--<div class="table-responsive">
    <table style="width: 100%;">
        <tr>
            <td style="font-size:12px;font-weight:bold">
            MANAGEMENT APPROVAL ONLY: 
            </td>
        </tr>
    </table>    
    <table style="width: 100%;">
        <tr>
            <td style="width:33.3%">
                <table class="<?php //echo table_class(); ?>">
                    <tr>
                        <td align="center" style="text-align:center">Line Manager’s Approval</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center;border:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center;border:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center;border:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center">Signature & Date </td>
                    </tr>
                </table>
            </td>
            <td style="width:33.3%">
                <table class="<?php //echo table_class(); ?>">
                    <tr>
                        <td align="center" style="text-align:center">Line Manager’s Approval</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center;border:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center;border:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center;border:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center">Signature & Date </td>
                    </tr>
                </table>
            </td>
            <td style="width:33.3%">
                <table class="<?php //echo table_class(); ?>">
                    <tr>
                        <td align="center" style="text-align:center">Line Manager’s Approval</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center;border:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center;border:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center;border:0">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center">Signature & Date </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div> -->

                <table>
                    <tr>
                        <td style="height:30px">&nbsp;</td>
                    </tr>
                </table>

<div class="table-responsive">
    <table style="width: 100%;">
        <tr>
            <td style="font-size:12px;font-weight:bold">
            EMPLOYEE ACKNOWLEDGEMENT:
            </td>
        </tr>
       
        <tr>
            <td style="font-size:12px;">
            I hereby acknowledge the receipt of the above sanctioned amount as a PERSONAL LOAN that shall be deducted from my monthly salary in agreed time period. In the event of my failure to pay the loan pack during my tenure of employment with <?php echo $this->common_data['company_data']['company_name']; ?>, the company may deduct the balance amount from my END OF SERVICE BENEFITS.
            </td>
        </tr>
    </table>
</div>

                <table>
                    <tr>
                        <td style="height:30px">&nbsp;</td>
                    </tr>
                </table>

<div class="table-responsive">
    <table style="width: 100%;">
        <tr>
            <td style="width:33.3%">
                <table>
                    <tr>
                        <td align="right" style="text-align:right">Full name :</td>
                        <td align="left" style="text-align:left">____________________</td>
                    </tr>
                </table>
            </td>
            <td style="width:33.3%">
                <table>
                    <tr>
                        <td align="right" style="text-align:right">Signature :</td>
                        <td align="left" style="text-align:left">____________________</td>
                    </tr>
                </table>
            </td>
            <td style="width:33.3%">
                <table>
                    <tr>
                        <td align="right" style="text-align:right">Date :</td>
                        <td align="left" style="text-align:left">____________________</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>


<br>
<br>
<br>
<br>

<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
         <?php if ($masterData->confirmedYN==1) { ?>
        <tr>
            <td style="width:30%;"><b>Confirmed By </b></td>
            <td><strong>:</strong></td>
            <td style="width:70%;"><?php echo $masterData->confirmedByName; ?></td>
        </tr>
        <?php } ?>
        <?php if ($masterData->approvedYN) { ?>
            <tr>
                <td style="width:30%;"><b>Electronically Approved By </b></td><!--Electronically Approved By-->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $masterData->approvedbyEmpName; ?></td>
            </tr>
            <tr>
                <td style="width:30%;"><b>Electronically Approved Date</b></td><!--Electronically Approved Date -->
                <td><strong>:</strong></td>
                <td style="width:70%;"><?php echo $masterData->approvedDate; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php
