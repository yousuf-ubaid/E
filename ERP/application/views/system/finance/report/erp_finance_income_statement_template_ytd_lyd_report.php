<!---- =============================================
-- File Name : erp_finance_income_statement_ytd_budget_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 26 - February 2017
-- Description : This file contains Income Statement.

-- REVISION HISTORY
-- =============================================-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$datefrom=$this->lang->line('finance_common_date_from');
$dateto=$this->lang->line('finance_common_date_to');
$totc=$this->lang->line('finance_common_total');
$companyid=$inv_company;
$temMasterID= $TemplateId;
$isRptCost = false;
$isLocCost = false;
if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
    }
    if (in_array("companyLocalAmount", $fieldName)) {
        $isLocCost = true;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_finance_tb', 'Income Statement YTD - LYD');
        } ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Income Statement YTD - LYD<!--Income Statement YTD - LYD--></div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$datefrom<!--Date From-->: </strong>" . $from . " - <strong>$dateto<!--Date To-->: </strong>" . $to ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br>
            <strong><i>Segment:</i></strong> <?php echo join(",", $segmentfilter) ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <?php
                            if (!empty($fieldName)) {
                                foreach ($fieldName as $val) {
                                    if ($val == "companyLocalAmount") {
                                        $actualtot=$this->lang->line('finance_common_actual_total');
                                        $budgettot=$this->lang->line('finance_common_budget_total');
                                        $variance=$this->lang->line('finance_common_budget_variance');

                                        echo "<th>$actualtot<!--Actual Total-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                                        echo "<th>LYTD<!--Budget Total--> </th>";
                                       /* echo "<th>$variance<!--Variance-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                                        echo "<th>$variance<!--Variance-->(%) </th>";*/
                                    }
                                    if ($val == "companyReportingAmount") {
                                        $actualtot=$this->lang->line('finance_common_actual_total');
                                        $budgettot=$this->lang->line('finance_common_budget_total');
                                        $variance=$this->lang->line('finance_common_budget_variance');
                                        echo "<th>$actualtot<!--Actual Total-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                        echo "<th>LYTD<!--Budget Total--> </th>";
                                       /* echo "<th>$variance<!--Varience-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                        echo "<th>$variance<!--Variance-->(%) </th>";*/
                                    }
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                       $bodydata=load_template_fm_ytd_lyd_statement_report($temMasterID,$output,$from,$to);

                       echo $bodydata; 
                        
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <?php
            } else {
                $norecfo=$this->lang->line('common_no_records_found');
                echo warning_message($norecfo);/*"No Records Found!"*/
            }
            ?>
        </div>
    </div>
</div>
<script>
    /* $(document).ready(function() {
     $('#demo').dragtable();
     });*/
    /*$('#tbl_report').tableHeadFixer({
        head: true,
        foot: false,
        left: 0,
        right: 0,
        'z-index': 0
    });*/
</script>