<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .wagesAmount {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 5px;
        font-weight: 700;
        color: blue;
        font-size: 20px;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }
    .tableHeader {
        border: solid 1px #e6e6e6 !important;
    }
    .center {
        text-align: center;
    }
</style>
<?php
if (!empty($batchAmount)) {
    $wagesEditAmount = 0;
    $loanAmount = 0;
    $loanDue = 0;
    $loanBal = 0;
    $depositAmount = 0;
    $depositDue = 0;
    $depositBal = 0;
    $advanceAmount = 0;
    $advanceDue = 0;
    $advanceBal = 0;
    if(!empty($editDetails))
    {
        foreach ($editDetails as $val){
            if($val['type'] == 'Loan'){
                $loanAmount = $val['transactionAmount'];
                $loanDue = $val['due_amount'];
                $loanBal = $val['balance_amount'];
            }
            if($val['type'] == 'Deposit'){
                $depositAmount = $val['transactionAmount'];
                $depositDue = $val['due_amount'];
                $depositBal = $val['balance_amount'];
            }
            if($val['type'] == 'Advance'){
                $advanceAmount = $val['transactionAmount'];
                $advanceDue = $val['due_amount'];
                $advanceBal = $val['balance_amount'];
            }
            $wagesEditAmount += $val['transactionAmount'];
        }
    }

    /*echo $farmerDepositAmount['depositAmount'];
    echo'<br>';
    echo $farmerDepositPaidAmount['depositAmount'];*/
    $batchAmount = $batchAmount + $wagesEditAmount;
    $wagesPaidAmount = $batchAmount - $wagesPaidAmount['wagesAmount'];
    $totalLoanAmount = $loanPayableAmount['loanAmount'] + $loanPayableAmount['creditAmount'] + $loanAmount - ($loanPaidAmount['loanPaidAmount'] + $loanPaidAmountRV['loanPaidAmount']);
    $totalDepositAmount = ($farmerDepositAmount['transactionAmount'] + $farmerDepositAmount['debitAmount']) /*+ $depositAmount*/ - $farmerDepositPaidAmount['depositAmount'];
    $totalAdvanceAmount = $advancePayableAmount['advanceAmount'] + $advancePayableAmount['creditAmount'] + $advanceAmount - ($advancePaidAmount['advanceAmount'] + $advancePaidAmountRV['advanceAmount']);

    $class='';
    if($wagesPaidAmount < 0){
        $class='hidden';
    } /*else{
        if($voucherDetails) {
            $wagesPaidAmount = $wagesPaidAmount - $voucherDetails['transactionAmount'];
        }
    }*/
    ?>
    <div class="row">
        <div class="col-sm-6">
            <label class="<?php echo $class ?>">Wages Payable (<?php echo $batchCode['batchCode']; ?>)</label>
        </div>
        <div class="col-sm-6">
            <div
                class="pull-right wagesAmount <?php echo $class ?>" id="wagesAmountchk"><?php echo number_format($batchAmount, 2); ?></div>
        </div>
    </div>
    <br>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <thead style="border: 1px solid #da9393;">
            <tr>
                <th class="headrowtitle tableHeader">#</th>
                <th class="headrowtitle tableHeader">Description</th>
                <th class="headrowtitle tableHeader">Batch Code</th>
                <th class="headrowtitle tableHeader center">Balance Amount</th>
                <th class="headrowtitle tableHeader center">Deduction</th>
                <th class="headrowtitle tableHeader center">Due Amount</th>
            </thead>
            <tbody>
            <tr>
                <td class="mailbox-star" width="5%">1</td>
                <td class="mailbox-star">Loan</td>
                <td class="mailbox-star" style="text-align: right"></td>
                <td class="mailbox-star" style="text-align: right"><input type="hidden" value="<?php echo $totalLoanAmount?>" id="loan_amount" name="loan_amount"><?php echo number_format($totalLoanAmount, 2) ?></td>
                <td class="mailbox-star" style="text-align: right"><input type="text" class="form-control number" id="loan_deductionAmount" value="<?php echo $loanAmount ?>" name="loan_deductionAmount" onkeyup="validate_loanAmount(<?php echo $totalLoanAmount; ?>, this.value, <?php echo $batchAmount; ?>)"></td>
                <td class="mailbox-star" style="text-align: right"><input type="text" class="form-control number" value="<?php echo $totalLoanAmount - $loanAmount;?>" id="loan_dueAmount" name="loan_dueAmount" readonly></td>
            </tr>
            <tr>
                <td class="mailbox-star" width="5%">2</td>
                <td class="mailbox-star">Deposit</td>
                <td class="mailbox-star" style="text-align: right"></td>
                <td class="mailbox-star" style="text-align: right"><input type="hidden" value="<?php echo $totalDepositAmount ?>" id="deposit_amount" name="deposit_amount"><?php echo number_format($totalDepositAmount, 2) ?></td>
                <td class="mailbox-star" style="text-align: right"><input type="text" class="form-control number" value="<?php echo $depositAmount ?>" id="deposit_deductionAmount" name="deposit_deductionAmount" onkeyup="validate_depositAmount(<?php echo $totalDepositAmount; ?>, this.value, <?php echo $batchAmount; ?>)"></td>
                <td class="mailbox-star" style="text-align: right"><input type="text" class="form-control number" value="<?php echo $totalDepositAmount + $depositAmount; ?>" id="deposit_dueAmount" name="deposit_dueAmount" readonly></td>
            </tr>
         <!--   <tr>
                <td class="mailbox-star" width="5%">3</td>
                <td class="mailbox-star">Equipment</td>
                <td class="mailbox-star" style="text-align: right"></td>
                <td class="mailbox-star" style="text-align: right">-</td>
                <td class="mailbox-star" style="text-align: right"><input type="text" class="form-control number" id="equipment_deductionAmount" name="equipment_deductionAmount" readonly></td>
                <td class="mailbox-star" style="text-align: right"><input type="text" class="form-control number" id="equipment_dueAmount" name="equipment_dueAmount" readonly></td>
            </tr> -->
            <tr>
                <td class="mailbox-star" width="5%">3</td>
                <td class="mailbox-star">Loss</td>
                <td class="mailbox-star" style="text-align: right"></td>
                <td class="mailbox-star" style="text-align: right">&nbsp;</td>
                <td class="mailbox-star" style="text-align: right">&nbsp;</td>
                <td class="mailbox-star" style="text-align: right">&nbsp;</td>
            </tr>
            <?php
            $losttot=0;
            if(!empty($lostAmount)){
                foreach($lostAmount as $val){
                ?>
                <tr>
                    <td class="mailbox-star" width="5%">&nbsp;</td>
                    <td class="mailbox-star">&nbsp;</td>
                    <td class="mailbox-star" style="text-align: right"><?php echo $val['batchCode'];  ?></td>
                    <td class="mailbox-star" style="text-align: right"><?php echo number_format($val['batchBalanceAmt'], 2) ?></td>
                    <td class="mailbox-star" style="text-align: right"><input type="hidden" name="lossedbatchID[]" value="<?php echo $val['batchMasterID']; ?>"><input type="text" class="form-control number loosval" value="<?php echo $val['transamt']; ?>" onkeyup="validate_lostAmount(<?php echo $val['batchBalanceAmt']; ?>, this.value,<?php echo $val['batchMasterID']; ?>,<?php echo $batchAmount; ?>)" id="lost_deductionAmount_<?php echo $val['batchMasterID']; ?>" name="lost_deductionAmount[]"></td>
                    <td class="mailbox-star" style="text-align: right"><input type="text" class="form-control number" value="<?php echo $val['batchBalanceAmt']+$val['transamt'] ?>" id="lost_dueAmount_<?php echo $val['batchMasterID']; ?>" name="lost_dueAmount[]" readonly></td>
                </tr>
            <?php
                    $losttot +=  $val['transamt'];
                }
            }
            ?>

            <tr>
                <td class="mailbox-star" width="5%">5</td>
                <td class="mailbox-star">Advance</td>
                <td class="mailbox-star" style="text-align: right"></td>
                <td class="mailbox-star" style="text-align: right"><input type="hidden" value="<?php echo $totalAdvanceAmount?>" id="advance_amount" name="advance_amount"><?php echo number_format($totalAdvanceAmount, 2) ?></td>
                <td class="mailbox-star" style="text-align: right"><input type="text" class="form-control number" value="<?php echo $advanceAmount ?>"  id="advance_deductionAmount" name="advance_deductionAmount" onkeyup="validate_advanceAmount(<?php echo $totalAdvanceAmount; ?>, this.value, <?php echo $batchAmount; ?>)"></td>
                <td class="mailbox-star" style="text-align: right"><input type="text" class="form-control number" value="<?php echo $totalAdvanceAmount - $advanceAmount;?>" id="advance_dueAmount" name="advance_dueAmount" readonly></td>
            </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-6">
            &nbsp;
        </div>
        <div class="col-sm-6">
            <label>Net Amount</label>
            <?php
            $valutot = $loanPaidAmount['loanPaidAmount'] + $farmerDepositPaidAmount['depositAmount']+ $losttot + $advancePaidAmount['advanceAmount'];
           // $batchAmount=$batchAmount - $valutot;

            ?>
            <div class="pull-right wagesAmount" id="netAmount_payable"><?php echo number_format($batchAmount, 2); ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            &nbsp;
        </div>
        <div class="col-sm-6">
            <label>Net Payable Amount</label>
            <div class="pull-right wagesAmount"><input type="text" class="form-control number" value="<?php echo number_format($batchAmount, 2); ?>" id="lastPaidAmount" name="lastPaidAmount" readonly></div>
        </div>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO SETTLEMENT TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        number_validation();
    });
</script>