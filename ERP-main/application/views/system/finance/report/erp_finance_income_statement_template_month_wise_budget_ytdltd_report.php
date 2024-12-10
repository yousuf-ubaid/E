<!---- =============================================
-- File Name : erp_finance_income_statement_month_wise_budget_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 27 - February 2017
-- Description : This file contains Income Statement Month Wise budget.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$datefrom=$this->lang->line('finance_common_date_from');
$dateto=$this->lang->line('finance_common_date_to');
$currency=$this->lang->line('common_currency');
$totalc=$this->lang->line('finance_common_total');
$grossprofit=$this->lang->line('finance_common_gross_profit');
$netproloss=$this->lang->line('finance_common_net_profit_loss');
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
            echo export_buttons('tbl_finance_tb', 'Income Statement Month Wise Budget LYD');
        } ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Income Statement Month Wise Budget LYD<!--Income Statement Month Wise Budget--></div>
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
    <div class="row">
        <div class="pull-right">
            <?php
            if ($isRptCost) {
                echo '<div class="col-md-12"><strong>'.$currency.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_reporting_currency'] . '</div>';

            }
            if ($isLocCost) {
                echo '<div class="col-md-12"><strong>'.$currency.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_default_currency'] . '</div>';
            }
            ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="table-responsive">
                    <table class="borderSpace report-table-condensed" id="tbl_report"
                           style="display: block;border-collapse: collapse;">
                        <thead class="report-header">
                        <tr>
                            <th rowspan="2">
                                <div style='width: 10%'><?php echo $this->lang->line('common_description');?><!--Description--></div>
                            </th>
                            <?php
                            if (!empty($month)) {
                                foreach ($month as $key => $val) {
                                    echo ' <th colspan="2">' . $val . '</th>';
                                }
                            }
                            ?>
                            <th rowspan="2"><?php echo $this->lang->line('finance_rs_is_total_actual');?><!--Total Actual--></th>
                            <th rowspan="2">Total LYM</th>
                        </tr>
                        <tr>
                            <?php
                            if (!empty($month)) {
                                foreach ($month as $key => $val) {
                                    $prevyear = explode('-', $val);

                                    $year  = $prevyear[0].'-'.($prevyear[1]-1);
                                    $actual =$this->lang->line('finance_rs_is_actual');
                                    $budget=$this->lang->line('finance_rs_is_budget');
                                    echo ' <th>'.$actual.'<!--Actual--></th>';
                                    echo ' <th>LYM <!--Budget--></th>';
                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                           $bodydata=load_template_budget_month_ytdltd_fm_statement_report($month,$temMasterID,$output);

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
                $norec= $this->lang->line('common_no_records_found');
                echo warning_message($norec);/*"No Records Found!"*/
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
<script type="text/javascript">
      window.onload = generateexpanednew3(); // call function with parameters on page load
      function generateexpanednew3( ) {
     
        
     //sub
       $(".subhoverTr11").css("display", "none");
       $(".subhoverTr12").css("display", "none");
       $(".subhoverTr13").css("display", "none");
       $(".subhoverTr14").css("display", "none");
       $(".subhoverTr15").css("display", "none");
       $(".subhoverTr16").css("display", "none");
       $(".subhoverTr17").css("display", "none");
       $(".subhoverTr18").css("display", "none");
       //sub
       $(".subhoverTr21").css("display", "none");
       $(".subhoverTr22").css("display", "none");
       $(".subhoverTr23").css("display", "none");
       $(".subhoverTr24").css("display", "none");
       $(".subhoverTr25").css("display", "none");
      
      }
   </script>

  <script>
    function generatesubcategory(k,m)
        {
            var d=k+m;
          
            var attname2=$('#subcat'+d).attr('class');
               

               if(attname2=='fa fa-plus-square')
       {
       $("#subcat"+d).removeClass('fa fa-plus-square').addClass('fa fa-minus-square');
       }
       else
        {
        $("#subcat"+d).removeClass('fa fa-minus-square').addClass('fa fa-plus-square');
        }

         $(".subhoverTr"+d).slideUp('fast').show();
              
        
        }
    
</script>