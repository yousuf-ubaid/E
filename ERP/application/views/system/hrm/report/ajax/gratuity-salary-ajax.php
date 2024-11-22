<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_loan_lang', $primaryLanguage);



?>
<style>
    .class_1 {
        background-color: #ebebf9;
    }

    .class_2 {
        background-color: #F7FBFB;
    }

    .class_3 {
        background-color: #FDFEFD;
    }

    .class_4 {
        background-color: #EFEFF2;
    }

    .class_gross {
        background-color: #F6F3F4;
    }

    .class_tot {
        background-color: #d8d8e6;
    }
</style>


<?php
foreach ($gr_data as $gr_id=>$item){
    $cols_pan = count($item['slab_det'] ?? []);
?>

<div class="row" style="margin-top: 15px">
    <div class="col-md-5">
        <b><?php echo $gratuityMaster[$gr_id][0]['gratuityDescription']; ?></b>
    </div>
    <div class="col-md-7">
        <?php echo export_buttons('content-tbl-'.$gr_id, 'Gratuity Salary', True, false); ?>
    </div>
</div>

<div id="content-tbl-<?=$gr_id?>">
    <div class="hide"><?php echo $this->lang->line('common_company'); ?><!--Company--> - <?php echo current_companyName(); ?></div>
    <div class="hide"><?php echo $this->lang->line('hrms_reports_gratuity_salary');?><?php echo $as_of_date_str ?></div>
    <div class="hide"><b><?php echo $gratuityMaster[$gr_id][0]['gratuityDescription']; ?></b></div>
    <div style="height: 450px; margin-top: 10px">
        <table id="" class="<?php echo table_class() ?> rpt-table">
            <thead>
                <tr>
                    <th rowspan="2">#</th>
                    <th rowspan="2"><?php echo $this->lang->line('common_emp_no');?></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_employee_name');?></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_designation');?></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_joined_date');?></th>
                    <th rowspan="2"><?php echo $this->lang->line('common_no_of_years');?></th>
                    <th colspan="<?php echo $cols_pan + 2?>" style="text-align: center">
                        <?php echo $this->lang->line('common_reporting_currency');?> [ <?php echo $rpt_curr;?> ]
                    </th>
                    <th colspan="<?php echo $cols_pan + 2?>">
                        <?php echo $this->lang->line('common_local_currency');?> [ <?php echo $loc_curr;?> ]
                    </th>
                     <th rowspan="2"><?php echo $this->lang->line('common_Loan');?></th>
                         <?php
                                $hasPreviousDetail = false;
                                if($item['details_previous_month'] != 0){
                                foreach ($item['details_previous_month'] as $isPrevious) {
                                    if ($isPrevious['previous_detail'] == 1) {
                                        $hasPreviousDetail = true;
                                        break; // Exit the loop once we find a match
                                    }
                                }
                                if ($hasPreviousDetail) {?>
                                    <th colspan="<?php echo $cols_pan + 2?>" style="text-align: center">
                                    <?php echo $this->lang->line('common_previous_reporting_currency');?> [ <?php echo $rpt_curr;?> ]
                                </th>
                                <th colspan="<?php echo $cols_pan + 2?>">
                                    <?php echo $this->lang->line('common_previous_local_currency');?> [ <?php echo $loc_curr;?> ]
                                </th>
                                 <?php  
                                }
                            }
                        ?>

                </tr>
                    <th class="class_gross"><?php echo $this->lang->line('common_fixed_gross_salary');?></th>
                <?php

                $local_str = ''; 
                $n = 1;
                if (isset($item['slab_det']) && is_array($item['slab_det'])) {
                    foreach ($item['slab_det'] as $title){
                        echo '<th class="class_'.$n.'">'.$title.'</th>';
                        $local_str .= '<th class="class_'.$n.'">'.$title.'</th>';
                        $n++;
                    }
                }

                echo '<th class="class_tot">'.$this->lang->line('common_total').'</th>
                      <th class="class_gross">'. $this->lang->line('common_fixed_gross_salary').'</th>
                      '.$local_str.'
                      <th class="class_tot">'.$this->lang->line('common_total').'</th>';

                if ($hasPreviousDetail) {?>
                 <th class="class_gross"><?php echo $this->lang->line('common_fixed_gross_salary');?></th>
                <?php

                $local_str = ''; $n = 1;
                foreach ($item['slab_det'] as $title){
                    echo '<th class="class_'.$n.'">'.$title.'</th>';
                    $local_str .= '<th class="class_'.$n.'">'.$title.'</th>';
                    $n++;
                }

                echo '<th class="class_tot">'.$this->lang->line('common_total').'</th>
                      <th class="class_gross">'. $this->lang->line('common_fixed_gross_salary').'</th>
                      '.$local_str.'
                      <th class="class_tot">'.$this->lang->line('common_total').'</th>';
                }
                ?>
                <tr>

                </tr>
            </thead>

            <tbody>
            <?php
            $dPlace = 2; $gr_tot_rpt = 0; $gr_tot_loc = 0; $line = 1;
            $gr_tot_rpt_prev = 0; 
            $gr_tot_loc_prev = 0;
            foreach ($item['details'] as $key=>$detail){
                $this_cur = $detail['payCurrencyID'];
                $rpt_cnv = $currency_det[$this_cur]['rpt']['conversion'];
                $loc_cnv = $currency_det[$this_cur]['loc']['conversion'];

                $total =  round($detail['gratuityAmount'], $dPlace);
                if($total == 0){
                    continue;
                }

                $rpt_total = round(($total / $rpt_cnv), $rpt_dPlace);
                $loc_total = round(($total / $loc_cnv), $loc_dPlace);
                $gr_tot_rpt += $rpt_total;
                $gr_tot_loc += $loc_total;

                $prev_total =  round($detail['gratuityAmount'], $dPlace);
                $prev_rpt_total = round(($prev_total / $rpt_cnv), $rpt_dPlace);
                $prev_loc_total = round(($prev_total / $loc_cnv), $loc_dPlace);
                $gr_tot_rpt_prev += $prev_rpt_total;
                $gr_tot_loc_prev += $prev_loc_total;


                $fix_pay = round($detail['totFixPayment'], $dPlace);
                $rpt_fix_pay = round(($fix_pay / $rpt_cnv), $rpt_dPlace);
                $loc_fix_pay = round(($fix_pay / $loc_cnv), $loc_dPlace);

                echo '<tr>
                          <td style="text-align: right">'.$line.'</td>
                          <td>'.$detail['ECode'].'</td>                 
                          <td><div style="width: 150px">'.$detail['Ename2'].'</div></td>
                          <td>'.$detail['designation'].'</td>
                          <td><div style="width: 60px">'.$detail['joinDate'].'</div></td>                 
                          <td style="text-align: center"><div style="width: 60px">'.$detail['totalWork'].'</div></td>
                          <td style="text-align: right" class="class_gross">'.number_format($rpt_fix_pay, $rpt_dPlace).'</td>';

                $local_str = ''; $n = 1;
                foreach ($item['slab_det'] as $slab_id => $row){
                    $amount = $detail['slab'][$slab_id];
                    $amount_rpt = round(($amount / $rpt_cnv), $rpt_dPlace);
                    $amount_loc = round(($amount / $loc_cnv), $loc_dPlace);
                    echo '<td style="text-align: right" class="class_'.$n.'">'.number_format($amount_rpt, $rpt_dPlace).'</td>';
                    $local_str .= '<td style="text-align: right" class="class_'.$n.'">'.number_format($amount_loc, $loc_dPlace).'</td>';
                    $n++;
                }
                $loanDetails = $detail['loanDetails'];
                if (!empty($loanDetails)) {
                    $loanCodes='';
                    foreach ($loanDetails as $loan) {
                        $loanCode = htmlspecialchars($loan['loanCode'], ENT_QUOTES, 'UTF-8');
                        $loanID = htmlspecialchars($loan['loanID'], ENT_QUOTES, 'UTF-8');
            
                        if (!empty($loanID)) {
                            $loanCodes .= '<div style="cursor: pointer;" onclick="showLoanDetails('.$detail['empID'].')">' . $loanCode . '</div>';
                        }
                    }
                } else {
                    $loanCodes = '';
                }

                echo '<td style="text-align: right" class="class_tot"><b>'.number_format($rpt_total, $rpt_dPlace).'</b></td> 
                      <td style="text-align: right" class="class_gross">'.number_format($loc_fix_pay, $loc_dPlace).'</td>
                      '.$local_str.'
                      <td style="text-align: right" class="class_tot"><b>'.number_format($loc_total, $loc_dPlace).'</b></td> 
                      <td> '.$loanCodes.'</td>   
                      </tr>';
                  '<td style="text-align: right" class="class_tot"><b>'.number_format($loc_total, $loc_dPlace).'</b></td>';

                  if ($hasPreviousDetail) {
                      echo '<td style="text-align: right" class="class_gross">' . number_format($rpt_fix_pay, $rpt_dPlace) . '</td>';

                      $local_str = ''; $n = 1;
                      foreach ($item['slab_det'] as $slab_id => $row) {
                          $amount = $detail['slab'][$slab_id];
                          $amount_rpt = round(($amount / $rpt_cnv), $rpt_dPlace);
                          $amount_loc = round(($amount / $loc_cnv), $loc_dPlace);
                          echo '<td style="text-align: right" class="class_' . $n . '">' . number_format($amount_rpt, $rpt_dPlace) . '</td>';
                          $local_str .= '<td style="text-align: right" class="class_' . $n . '">' . number_format($amount_loc, $loc_dPlace) . '</td>';
                          $n++;
                      }

                      echo '<td style="text-align: right" class="class_tot"><b>' . number_format($rpt_total, $rpt_dPlace) . '</b></td>
                            <td style="text-align: right" class="class_gross">' . number_format($loc_fix_pay, $loc_dPlace) . '</td>
                            ' . $local_str . '
                            <td style="text-align: right" class="class_tot"><b>' . number_format($loc_total, $loc_dPlace) . '</b></td>';
                  }        
                  echo  '</tr>';
                
                $line++;

            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <?php
                    echo '<td style="text-align: right" colspan="'.($cols_pan + 7).'"><div style="padding-right: 50px; width:100%">'.$this->lang->line('common_grand_total').'</div></td>                 
                          <td style="text-align: right" class="class_tot"><b>'.number_format($gr_tot_rpt, $rpt_dPlace).'</b></td>
                          <td colspan="'.($cols_pan + 1).'"></td>
                          <td style="text-align: right" class="class_tot"><b>'.number_format($gr_tot_loc_prev, $rpt_dPlace).'</b></td>';

                // Previous Month
                if ($hasPreviousDetail) {
                    echo '<td colspan="'.($cols_pan + 1).'"></td>
                          <td style="text-align: right" class="class_tot"><b>'.number_format($gr_tot_rpt_prev, $rpt_dPlace).'</b></td>
                          <td colspan="'.($cols_pan + 1).'"></td>
                          <td style="text-align: right" class="class_tot"><b>'.number_format($gr_tot_loc, $rpt_dPlace).'</b></td>';
                }
                    
                    ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php } ?>


<div class="modal fade" id="loanScheduleTab" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title loanTitle" id="myModalLabel"><?php echo $this->lang->line('common__pending_Loan_detail');?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5" style="margin-bottom: 1%">
                        <table class="table table-bordered table-striped table-condensed ">
                            <tbody><tr>
                                <td><span class="label label-success" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('hrms_loan_settled');?><!--Settled--> </td>
                                <td><span class="label label-danger" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_pending');?><!--Pending--> </td>
                                <td><span class="label label-warning" style="padding: 0px 5px ;font-size: 100%;">&nbsp;</span> <?php echo $this->lang->line('common_skipped');?><!--Skipped--> </td>
                            </tr>
                            </tbody></table>
                    </div>
                </div>
                <table id="" class="<?php echo table_class(); ?> loanScheduleTB">
                    <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 25%"><?php echo $this->lang->line('hrms_loan_deduction_date');?><!--Deduction Date--> </th>
                            <th style="min-width: 5%"><?php echo $this->lang->line('hrms_loan_installment_no');?><!--Installment No--> </th>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--> &nbsp;&nbsp;<span class="dataTableCurrency"></span></th>
                            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                        </tr>
                    </thead>
                    <tbody id="loanDetailTableBody"></tbody>
                </table>
                <hr>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.rpt-table').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

    function generateReportExcel() {
        var form = document.getElementById('frm-rpt');
        form.target = '_blank';
        form.action = '<?php echo site_url('Report/get_gratuity_salary_report/Excel/Gratuity-salary'); ?>';
        form.submit();
    }
    function showLoanDetails(empID) {
    $.ajax({
        type: 'post',
        url: '<?php echo site_url('Report/loadLoan'); ?>',
        data: { 'empID': empID },
        dataType: 'json',
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
 

            var tableBody = $('#loanDetailTableBody');
            tableBody.empty();
            let currentLoanCode = '';

            data.forEach((item, index) => {
                        var statusLabel = '';

                        if (item.isSetteled==1) {
                            statusLabel = '<span class="label label-success">Settled</span>';
                        } else if (item.isSetteled==0) {
                            statusLabel = '<span class="label label-danger">Pending</span>';
                        } else {
                            statusLabel = '<span class="label label-warning">Skipped</span>';
                        }

                        if (currentLoanCode !== item.loanCode) {
                            tableBody.append(`<tr>
                                <td colspan="5"><strong>Loan Code :${item.loanCode}</strong></td>
                            </tr>`);
                            currentLoanCode = item.loanCode; 
                        }
                        tableBody.append(`<tr>
                            <td>${index + 1}</td>
                            <td>${item.scheduleDate1}</td>
                            <td>${item.installmentNo}</td>
                            <td>${item.amount}</td>
                            <td>${statusLabel}</td>
                        </tr>`);
                      
                    });
                    $('#loanScheduleTab').modal('show');
           
        },
        error: function (xhr, status, error) {
            stopLoad();
            }
    });
}

</script>

<?php
