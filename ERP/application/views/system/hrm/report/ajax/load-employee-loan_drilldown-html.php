 <?php
/**
 * Created by PhpStorm.
 * Date: 15/4/2020
 * Time: 10:10 AM
 */

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_loan', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

if ($header) { ?>

<div class="table-responsive">

	<div class="col-md-6">
    <table style="width: 100%">
        <tbody>
        	<tr>
	            <td style="width:35%;"><strong> <?php echo $this->lang->line('common_employee');?></strong></td><!--Employee-->
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo $header['Ename2'] ?></td>
        	</tr>
        	<tr>
	            <td style="width:35%;"><strong> <?php echo $this->lang->line('common_currency');?></strong></td><!--Currency-->
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo $header['transactionCurrency'] ?></td>
        	</tr>
        	<tr>
	            <td style="width:35%;"><strong> <?php echo $this->lang->line('hrms_loan_no_of_installment');?></strong></td><!--No of Installments-->
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo $header['numberOfInstallment'] ?></td>
        	</tr>
        	<tr>
	            <td style="width:35%;"><strong>No. Settled Installments</td>
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo $numberOfSettledInstallment  ?></td>
        	</tr>
        	<tr>
	            <td style="width:35%;"><strong>No. Pending Installments</strong></td>
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo $header['numberOfInstallment']-($numberOfSettledInstallment+$numberOfSkippedInstallment) ?></td>
        	</tr>
        	<tr>
	            <td style="width:35%;"><strong>No. Skipped Installments</strong></td>
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo $numberOfSkippedInstallment ?></td>
        	</tr>
        </tbody>
    </table>
	</div>

	<div class="col-md-6">
    <table style="width: 100%">
        <tbody>
        	<tr>
	            <td style="width:35%;"><strong> <?php echo $this->lang->line('hrms_loan_employee_code');?></strong></td><!--Employee Code-->
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo $header['ECode'] ?></td>
        	</tr>
        	<tr>
	            <td style="width:35%;"><strong> <?php echo $this->lang->line('common_amount');?></strong></td><!--Amount-->
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo number_format($header['loanAmount'], $header['transactionCurrencyDecimalPlaces']) ?></td>
        	</tr>
        	<tr>
	            <td style="width:30%;"><strong> Installment Amount</strong></td>
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo number_format($header['installmentAmount'], $header['transactionCurrencyDecimalPlaces']) ?></td>
        	</tr>
        	<tr>
	            <td style="width:35%;"><strong>Settled Total Amount </strong></td>
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo  number_format($header['settledAmount'], $header['transactionCurrencyDecimalPlaces'])  ?></td>
        	</tr>
        	<tr>
	            <td style="width:35%;"><strong>Pending Total Amount</strong></td>
	            <td style="width:2%;"><strong>:</strong></td>
	            <td style="width:68%;"> <?php echo  number_format($header['pendingAmount'], $header['transactionCurrencyDecimalPlaces']) ?></td>
        	</tr>
        	<tr>
	            
        	</tr>
        </tbody>
    </table>
	</div>

</div>
<br>

<div class="row" style="">
    <div class="table-responsive" style="">
        <table class="borderSpace report-table-condensed">
            <thead class="report-header">
            <tr>
                <th  style="min-width: 5%">#</th>
                <th class='' style="min-width: 15%"><?php echo $this->lang->line('common_date');?></th><!--Date-->
                <th class='' style="min-width: 35%"><?php echo $this->lang->line('hrms_loan_installment_no');?></th>
                <th class='' style="min-width: 10%"><?php echo $this->lang->line('common_status');?></th><!--Status-->
                <th class='' style="min-width: 10%"><?php echo $this->lang->line('common_remarks');?></th><!--Remarks-->
            </tr>
            </thead>
            <tbody>
            <?php	if (!empty($details)) {
            $r=1;
        		foreach ($details as  $val) { ?>
				<tr>
					<td style="text-align:center;"><?php echo $r ?></td>
					<td style='text-align: center;'><?php echo $val['scheduleDate']?></td>
					<td style='text-align:center;'><?php echo $val['installmentNo']?></td>
					<td><center>
						<?php
						if ( $val['isSetteled'] == 1 ) {
							echo	'<span class="label label-success">Settled</span>';
						}
						elseif ( $val['skipedInstallmentID'] > 0 ){
							echo	'<span class="label label-warning">Skipped</span>';
						} elseif ( $val['isSetteled']== 0 && $val['skipedInstallmentID']== 0 ) {
							echo	'<span class="label label-danger">Pending</span>';
						}else{
							'-';
						} 
						?></center>
					</td>
					<td style="text-align:center;"><?php echo $val['remark']?></td>
				</tr>
		     <?php $r++; }
		    } ?>
            </tbody>
        </table>
    </div>
</div>

<?php  } ?>