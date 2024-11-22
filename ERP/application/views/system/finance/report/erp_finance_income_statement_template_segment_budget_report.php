<!---- =============================================
-- File Name : erp_finance_income_statement_template_segment_budget_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 20 - Dec 2023
-- Description : This file contains Income Statement Month Wise.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$asof=$this->lang->line('finance_common_as_of');
$datefrom=$this->lang->line('finance_common_date_from');
$dateto=$this->lang->line('finance_common_date_to');
$curr=$this->lang->line('common_currency');
$tot=$this->lang->line('common_total');

$companyid=$inv_company;
$temMasterID= $TemplateId;
$from=$from;
$to=$to;

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
            echo export_buttons('tbl_finance_tb', 'Income Statement');
        } ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Income Statement Segment Wise</div>
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
                echo '<div class="col-md-12"><strong>'.$curr.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_reporting_currency'] . '</div>';

            }
            if ($isLocCost) {
                echo '<div class="col-md-12"><strong>'.$curr.'<!--Currency-->: </strong>' . $this->common_data['company_data']['company_default_currency'] . '</div>';
            }
            ?>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div" style="overflow: auto">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <?php
                            if (!empty($segment)) {
                                foreach ($segment as $key) {
                                    echo ' <th>' . $key['segmentCode'] . '</th>';
                                }
                            }
                            ?>
                            <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                         $bodydata=load_template_segment_fm_statement_report($month,$temMasterID,$output,$segment,$from,$to);

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
                $norecfound= $this->lang->line('common_no_records_found');
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