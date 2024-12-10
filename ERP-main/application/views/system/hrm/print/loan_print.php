<div style="margin-top: 5%" > &nbsp; </div>

<div class="table-responsive">
    <table style="width: 100%" border="0px">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;" valign="top">
                <table border="0px">
                    <tr>
                        <td colspan="2">
                            <h2><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h2>
                        </td>
                    </tr>
                    <tr>
                        <td><h4 style="margin-bottom: 0px">Employee Loan </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h5 style="margin-bottom: 0px"><?php echo $masterData->loanCode; ?></h5> </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<hr>

<div class="table-responsive" style="margin-top: 2%">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:18%;"><strong>Employee</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:30%;"><?php echo $masterData->Employee; ?></td>
            <td style="width:18%;"><strong>Employee Code</strong></td>
            <td style="width:2%;"><strong>:</strong></td>
            <td style="width:30%;"><?php echo $masterData->ECode; ?></td>
        </tr>
        <tr>
            <td><strong>Currency</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->payCurrency; ?></td>
            <td><strong>Amount</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo number_format( $masterData->amount, $masterData->DecimalPlaces ); ?></td>
        </tr>
        <tr>
            <td><strong>Loan Date</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->loanDate; ?></td>
            <td><strong>Deduction Start Date</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->deductionStartingDate; ?></td>
        </tr>
        <tr>
            <td><strong>Loan Category</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->description; ?></td>
            <td><strong>Interest Percentage</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->interestPer; ?></td>
        </tr>
        <?php if( $masterData->salaryAdvanceRequestID > 0 ){?>
        <tr>
            <td><strong>Salary Advance Request </strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->ad_document_code; ?></td>
            <td><strong> </strong></td>
            <td><strong> </strong></td>
            <td></td>
        </tr>
        <?php } ?>
        <tr>
            <td><strong>Description</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->loanDescription; ?></td>
            <td><strong> </strong></td>
            <td><strong> </strong></td>
            <td></td>
        </tr>
        <tr>
            <td><strong>No. Installments</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->numberOfInstallment; ?></td>
            <td><strong>Installment Amount</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo number_format( $intAmount['transactionAmount'], $masterData->DecimalPlaces ); ?></td>
        </tr>
        <?php if( $masterData->confirmedYN == 1 ){?>
        <tr>
            <td><strong>Confirmed By</strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->confirmedByName; ?></td>
            <?php
            if( $masterData->approvedYN == 1 ){
            ?>
            <td><strong>Approved By </strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData->approvedbyEmpName; ?></td>
            <?php } ?>
        </tr>
        <?php
        }

        if( $masterData->approvedYN == 1 ){?>
            <tr>
                <td><strong>No. Settled Installments </strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $installment['settled']; ?></td>
                <td><strong>Settled Total Amount</strong></td>
                <td><strong>:</strong></td>
                <td><?php echo number_format($installment['settled'] * $installment['intAmount'], $masterData->DecimalPlaces); ?></td>
            </tr>
            <tr>
                <td><strong>No. Pending Installments</strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $installment['pending']; ?></td>
                <td><strong>Pending Total Amount</strong></td>
                <td><strong>:</strong></td>
                <td><?php echo number_format($installment['pending'] * $installment['intAmount'], $masterData->DecimalPlaces); ?></td>
            </tr>
            <tr>
                <td><strong>No. Skipped Installments</strong></td>
                <td><strong>:</strong></td>
                <td><?php echo $installment['skipped']; ?></td>
                <td colspan="3"></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>


<div class="table-responsive" style="margin-top: 2%">
    <table class="<?php echo table_class(); ?>" style="width: 100%; margin-top: 2%">
        <thead>
            <tr>
                <th class="theadtr" style="width: 5%">#</th>
                <th class="theadtr" style="width: 10%">Date</th>
                <th class="theadtr" style="width: 30%">Installment No</th>
                <th class="theadtr" style="width: 30%">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                $i=1;
                foreach($schedule as $sch){
                $status = '';
                if( $sch['isSetteled'] == 1 ){ $status = '<span class="label label-success">&nbsp; Settled &nbsp;</span>'; }
                else{
                    $status = ( $sch['skipedInstallmentID'] == 0 )? '<span class="label label-danger">&nbsp; Pending &nbsp;</span>' : '<span class="label label-warning">&nbsp; Skipped &nbsp;</span>';
                }
                echo
                '<tr>
                    <td>'.$i++ . '</td>
                    <td>'.$sch['scheduleDate'].'</td>
                    <td>'.$sch['installmentNo'].'</td>
                    <td align="center" style="font-size:12px">'.$status.'</td>
                </tr>';
                }
                ?>
            </tr>
        </tbody>
    </table>
</div>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-22
 * Time: 5:40 PM
 */