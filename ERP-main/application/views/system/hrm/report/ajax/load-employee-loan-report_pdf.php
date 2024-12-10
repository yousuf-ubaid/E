<?php

/**
 * Created by PhpStorm.
 * Date: 19/4/2020
 * Time: 7:30 PM
 */


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_loan', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>


<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h3>
                            <h4> Loan Report</h4>
                        </td>


                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>

                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>

<table id="" class="borderSpace report-table-condensed">
    <thead class="report-header">

            <tr>
         
            <th><?php echo $this->lang->line('hrms_loan_employee_code');?><!--Employee Code--></th>
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

