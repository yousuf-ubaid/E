<!---- =============================================
-- File Name : erp_finance_balance_sheet_ytd_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 15 - September 2016
-- Description : This file contains Balance Sheet.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$asof=$this->lang->line('finance_common_as_of');

$subtot=$this->lang->line('finance_common_sub_total');
$tot=$this->lang->line('common_total');
$totcap=$this->lang->line('finance_common_total');

$companyid=$inv_company;
$temMasterID= $TemplateId;
$glcount=$glcount;
//based on year this value like this
$fromdate=$todatenew;
$todate=$fromdatenew;


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


$reportcheck = $this->db->query("select count(t1.GLAutoID) as CountGl from  (SELECT * FROM
srp_erp_chartofaccounts  WHERE companyID = $companyid
AND masterAccountYN = 0 
AND masterCategory = 'BS' 
AND GLAutoID NOT IN (SELECT GLAutoID FROM srp_erp_companyreporttemplatelinks 
WHERE templateMasterID = $temMasterID And  GLAutoID is not null  group by GLAutoID  ))t1")->result_array();
$reportval=$reportcheck[0]['CountGl'];


?>
<div class="row">
    <div class="col-md-12">
        <?php
        if ($type == 'html') {
            echo export_buttons('tbl_finance_tb', 'Balance Sheet');
        }
        ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('finance_rs_bs_balance_sheet_ytd');?><!--Balance Sheet YTD--></div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
        <?php if ($reportval==0) { ?>
            <?php if (!empty($output)) { 
                 
                ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <?php
                            if (!empty($fieldName)) {

                             
                                foreach ($fieldName as $val) {
                                    if ($val == "companyLocalAmount") {
                                        echo "<th>$subtot<!--Sub Total-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                                        echo "<th>$tot<!--Total-->(" . $this->common_data['company_data']['company_default_currency'] . ") </th>";
                                    }
                                    if ($val == "companyReportingAmount") {
                                        echo "<th>$subtot<!--Sub Total-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                        echo "<th>$tot<!--Total-->(" . $this->common_data['company_data']['company_reporting_currency'] . ") </th>";
                                    }
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                           

                                $bodydata=load_template_balancesheet_ytd_statement_report($temMasterID,$output,$fromdate,$todate);

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
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*"No Records Found!"*/
            }
            } else {
            $norecfound='GL code Mismatch ' .$reportval.'.  GL code Missing  in Template.Add GL code and TRY';
             echo warning_message($norecfound);/*"No Records Found!"*/
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