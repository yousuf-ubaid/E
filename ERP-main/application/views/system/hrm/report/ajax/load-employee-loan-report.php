<?php
/**
 * Created by PhpStorm.
 * Date: 15/4/2020
 * Time: 10:10 AM
 */

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('hrms_loan', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);

if ($details) { ?>

<div class="row" style="margin-top: 5px">
    <div class="col-md-12">
    <?php echo export_buttons('loanReport', 'Employee Loan Report', True, True); ?>
    </div>
</div>

<div class="row" style="margin-top: 5px">
    <div class="col-md-12 " id="loanReport" >
        <div class="hide"><?php echo $this->lang->line('common_company');?><!--Company--> - <?php echo current_companyName(); ?></div>
        
        <div style="height: 400px">
        <table id="loan-report-tb" class="borderSpace report-table-condensed" style="width: 100%">
            <thead class="report-header">
	            <tr>
	            	<th>#</th>
	            	<th style="min-width: 20%"><?php echo $this->lang->line('hrms_loan_employee_code');?><!--Employee Code--></th>
	                <th style="min-width: 10%"><?php echo $this->lang->line('common_employee_name');?><!--Employee Name--></th>
	                <th><?php echo $this->lang->line('common_document_code');?><!--Document Code--></th>
	                <th><?php echo $this->lang->line('hrms_loan_type');?><!--Loan Type--></th>
	                <th>Int.Percentage</th>
	                <th style="min-width: 15%"><?php echo $this->lang->line('hrms_loan_date');?><!--Segement--></th>
	                <th><?php echo $this->lang->line('hrms_loan_amount');?><!--Currency--></th>
	                <th><?php echo $this->lang->line('hrms_loan_no_of_installment');?><!--Employee Name--></th>
	                <th>Installment Amount</th>
	                <th><?php echo $this->lang->line('hrms_loan_deduction_start_date');?><!--Employee Name--></th>
	                <th>Expected End Date</th>
	                <th>Settled Amount</th>
	                <th>Settled Amount %</th>
	                <th>Balance Amount</th>
	            </tr>
            </thead>
            <tbody>
            <?php if ($details) {
            	$r = 1;
				foreach ($details as $val) {  ?>
				<tr class="hoverTr">
					<td><?php echo $r?></td>
                    <td><?php echo $val['ECode'] ?></td>
                    <td><?php echo $val['Ename2'] ?></td>
                    <td><a class="drill-down-cursor" onclick="document_drilldown(<?php echo $val['emploanID'] ?>)" > <?php echo $val['loanCode'] ?></a></td>
                    <td><?php echo $val['loanType'] ?></td>
                    <td style="text-align: right"><?php echo number_format($val['interestPer'], $val['transactionCurrencyDecimalPlaces']) ?> 
                                </td>
                    <td><?php echo $val['loanDate'] ?></td>
                    <td><?php echo $val['loanAmount'] ?></td>
                    <td><?php echo $val['numberOfInstallment'] ?></td>
                    <td style="text-align: right"><strong><?php echo number_format($val['installmentAmount'], $val['transactionCurrencyDecimalPlaces']) ?></strong></td>
                    <td><?php echo $val['deductionStartingDate'] ?></td>
                    <td><?php echo $val['expectedEndDate'] ?>
                    <td style="text-align: right"><?php echo number_format($val['settltedAmount'], $val['transactionCurrencyDecimalPlaces']) ?>
                                </td>
                    <td style="text-align: right"><?php echo number_format($val['settledAmountPercentage'], $val['transactionCurrencyDecimalPlaces']) ?></td>
                    <td style="text-align: right"><?php echo number_format($val['balance'], $val['transactionCurrencyDecimalPlaces']) ?> 
                                </td>
                </tr>	
                <?php  $r++; } 
            } ?>
            </tbody>
		</table>
		</div>

	</div>
</div>

<?php  } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found');?><!--No Records found-->.
            </div>
        </div>
    </div>

<?php } ?>	

<Script>
$('#loan-report-tb').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 0
});
function generateReportPdf() {
        var form = document.getElementById('frm_rpt_loan');
        form.target = '_blank';
        /*form.action = 'php echo site_url('template_paySheet/get_payScale_report_pdf'); ?>';*/
        form.action = '<?php echo site_url('Loan/get_loan_report/pdf/Loan_Report'); ?>';
        form.submit();
}
</Script>