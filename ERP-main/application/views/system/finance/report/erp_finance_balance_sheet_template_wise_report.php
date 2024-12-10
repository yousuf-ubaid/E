<!---- =============================================
-- File Name : erp_finance_balance_sheet_template_wise_report.php
-- Project Name : SME ERP
-- Module Name : Report - Finance
-- Create date : 06 - DEC 2023
-- Description : This file contains Income Statement Month Wise.
--by :Divya

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$asof=$this->lang->line('finance_common_as_of');
$currency=$this->lang->line('common_currency');
$tot=$this->lang->line('common_total');
$totc=$this->lang->line('finance_common_total');
$companyid=$inv_company;
$temMasterID= $TemplateId;
$glcount=$glcount;
/*$reportcheck = $this->db->query("select  count(Distinct det.glAutoID) CountGl  from srp_erp_companyreporttemplatelinks det
       JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID 
       JOIN srp_erp_generalledger leg ON leg.GLAutoID=det.glAutoID 
        WHERE det.templateMasterID = $temMasterID and det.companyID=$companyid")->result_array();*/


        $reportcheck = $this->db->query("select count(t1.GLAutoID) as CountGl from  (SELECT * FROM
        srp_erp_chartofaccounts  WHERE companyID = $companyid
AND masterAccountYN = 0 
AND masterCategory = 'BS' 
AND GLAutoID NOT IN (SELECT GLAutoID FROM srp_erp_companyreporttemplatelinks 
WHERE templateMasterID = $temMasterID And  GLAutoID is not null  group by GLAutoID  ))t1")->result_array();
   $reportval=$reportcheck[0]['CountGl'];

   


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
    <?php if ($reportval==0) { ?>
        <?php echo export_buttons('tbl_finance_tb', 'Balance Sheet'); ?>
        <?php } ?>
    </div>
</div>
<div id="tbl_finance_tb">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Balance Sheet Template Wise</div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . $from ?></div>
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
         <?php if ($reportval==0) { ?>
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <?php
                            if (!empty($month)) {
                                foreach ($month as $key => $val) {
                                    echo ' <th>' . $val . '</th>';
                                }
                            }
                            ?>
                             <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                      // if($glcount==$reportval)
                      // {
                       $bodydata=load_balance_template_fm_statement_report($month,$temMasterID,$output,$from);

                       echo $bodydata;
                     //  }
                     

                     //exit();

                      
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

<script  type="text/javascript">
     function generatebsexpaned1()
    {
     
       //Main head
       $(".hoverTr1").slideUp('fast').show();
       var attname=$('#sample1').attr('class');
       //alert(attname);
       if(attname=='fa fa-plus-square')
       {
       $("#sample1").removeClass('fa fa-plus-square').addClass('fa fa-minus-square');
       }
       else
       {
        $("#sample1").removeClass('fa fa-minus-square').addClass('fa fa-plus-square');
       }
              //Subhead
        $(".subhoverheadTr11").slideUp('fast').show();
       $(".subhoverheadTr12").slideUp('fast').show();
       $(".subhoverheadTr13").slideUp('fast').show();
       $(".subhoverheadTr14").slideUp('fast').show();
       $(".subhoverheadTr15").slideUp('fast').show();
       $(".subhoverheadTr16").slideUp('fast').show();
       $(".subhoverheadTr17").slideUp('fast').show();


      


        //gl sub
       $(".subhoverTr11").css("display", "none");
       $(".subhoverTr12").css("display", "none");
       $(".subhoverTr13").css("display", "none");
       $(".subhoverTr14").css("display", "none");
       $(".subhoverTr15").css("display", "none");
       $(".subhoverTr16").css("display", "none");
       $(".subhoverTr17").css("display", "none");
    }
    function generatebsexpaned2()
    {
      
        $(".hoverTr2").slideUp('fast').show();
        var attname1=$('#sample2').attr('class');

        if(attname1=='fa fa-plus-square')
       {
       $("#sample2").removeClass('fa fa-plus-square').addClass('fa fa-minus-square');
       }
       else
       {
        $("#sample2").removeClass('fa fa-minus-square').addClass('fa fa-plus-square');
       }
       


       $(".subhoverheadTr21").slideUp('fast').show();
       $(".subhoverheadTr22").slideUp('fast').show();
       $(".subhoverheadTr23").slideUp('fast').show();
       $(".subhoverheadTr24").slideUp('fast').show();
       $(".subhoverheadTr25").slideUp('fast').show();
       $(".subhoverheadTr26").slideUp('fast').show();


       //gl sub
       $(".subhoverTr21").css("display", "none");
       $(".subhoverTr22").css("display", "none");
       $(".subhoverTr23").css("display", "none");
       $(".subhoverTr24").css("display", "none");
       $(".subhoverTr25").css("display", "none");
     
     
    }

    function generatebsexpaned3()
    {
      
        $(".hoverTr3").slideUp('fast').show();
        var attname1=$('#sample3').attr('class');

        if(attname1=='fa fa-plus-square')
       {
       $("#sample3").removeClass('fa fa-plus-square').addClass('fa fa-minus-square');
       }
       else
       {
        $("#sample3").removeClass('fa fa-minus-square').addClass('fa fa-plus-square');
       }
       


       $(".subhoverheadTr31").slideUp('fast').show();
       $(".subhoverheadTr32").slideUp('fast').show();
       $(".subhoverheadTr33").slideUp('fast').show();
       $(".subhoverheadTr34").slideUp('fast').show();
       $(".subhoverheadTr35").slideUp('fast').show();
       $(".subhoverheadTr36").slideUp('fast').show();


       //gl sub
       $(".subhoverTr31").css("display", "none");
       $(".subhoverTr32").css("display", "none");
       $(".subhoverTr33").css("display", "none");
       $(".subhoverTr34").css("display", "none");
       $(".subhoverTr35").css("display", "none");
     
     
    }
 
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
<script type="text/javascript">
      window.onload = generatebsexpanednew3(); // call function with parameters on page load

      function generatebsexpanednew3(){
      
        $(".hoverTr1").slideUp('fast').show();
       $(".hoverTr2").slideUp('fast').show();
       //addtional
       $(".hoverTr3").slideUp('fast').show();
       $(".hoverTr4").slideUp('fast').show();
       $(".hoverTr5").slideUp('fast').show();
       $(".hoverTr6").slideUp('fast').show();
           
         //head
         $(".subhoverheadTr11").slideUp('fast').show();
        $(".subhoverheadTr12").slideUp('fast').show();
       $(".subhoverheadTr13").slideUp('fast').show();
       $(".subhoverheadTr14").slideUp('fast').show();
       $(".subhoverheadTr21").slideUp('fast').show();
       $(".subhoverheadTr22").slideUp('fast').show();
       //add head
       $(".subhoverheadTr15").slideUp('fast').show();
       $(".subhoverheadTr16").slideUp('fast').show();
       $(".subhoverheadTr23").slideUp('fast').show();
       $(".subhoverheadTr24").slideUp('fast').show();
       $(".subhoverheadTr25").slideUp('fast').show();
       $(".subhoverheadTr31").slideUp('fast').show();
       $(".subhoverheadTr32").slideUp('fast').show();
       $(".subhoverheadTr33").slideUp('fast').show();

       //sub
       $(".subhoverTr11").css("display", "none");
       $(".subhoverTr12").css("display", "none");
       $(".subhoverTr13").css("display", "none");
       $(".subhoverTr14").css("display", "none");
       $(".subhoverTr15").css("display", "none");
       $(".subhoverTr16").css("display", "none");
       $(".subhoverTr17").css("display", "none");
       $(".subhoverTr18").css("display", "none");
       $(".subhoverTr19").css("display", "none");
       $(".subhoverTr20").css("display", "none");
     
        //sub
        $(".subhoverTr21").css("display", "none");
       $(".subhoverTr22").css("display", "none");
       $(".subhoverTr23").css("display", "none");
       $(".subhoverTr24").css("display", "none");
       $(".subhoverTr25").css("display", "none");
       $(".subhoverTr26").css("display", "none");
       $(".subhoverTr27").css("display", "none");

       $(".subhoverTr31").css("display", "none");
       $(".subhoverTr32").css("display", "none");
       $(".subhoverTr33").css("display", "none");
       $(".subhoverTr34").css("display", "none");
       $(".subhoverTr35").css("display", "none");
       $(".subhoverTr36").css("display", "none");
       $(".subhoverTr37").css("display", "none");
      

      
      }
   </script>
  