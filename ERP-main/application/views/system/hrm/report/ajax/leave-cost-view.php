<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);

if($is_view == 'Y'){ ?>
    <div class="col-sm-121" style="width: 100%; height: 10px">
        <button type="button" class="btn btn-danger pull-right" onclick="print_document()">
            <i class="fa fa-print"></i> <?=$this->lang->line('common_print')?>
        </button>
    </div>
<?php } else{?>
    <div class="table-responsive">
        <table style="width: 100%" border="0px">
            <tbody>
            <tr>
                <td style="width:40%;">
                    <table>
                        <tr>
                            <td>
                                <img alt="Logo" style="height: 130px"
                                     src="<?=mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width:60%;" valign="top">
                    <table border="0px">
                        <tr>
                            <td colspan="2">
                                <h2>
                                    <strong><?=$this->common_data['company_data']['company_name']; ?></strong>
                                </h2>
                            </td>
                        </tr>
                        <tr>
                            <td><h4 style="margin-bottom: 0px"><?=$this->lang->line('hrms_reports_leave_cost'); ?></h4></td>
                        </tr>
                        <tr>

                            <td colspan="2">
                                <h5 style="margin-bottom: 0px">
                                    <?=$this->lang->line('common_as_of_date').' -'.$asOfDate; ?>
                                </h5>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <br/>
<?php } ?>

<br/>

<div id="report-response" style="height: 450px;">
    <table class="<?=table_class()?>" id="rpt_tbl">
        <thead>
        <tr>
            <th class="theadtr">#</th>
            <th class="theadtr" style=""> <?=$this->lang->line('common_employee_name');?> </th>
            <th class="theadtr" style=""> <?=$this->lang->line('hrms_leave_management_leave_type');?> </th>
            <th class="theadtr" style="width: 105px"> <?=$this->lang->line('hrms_leave_management_leave_balance');?> </th>
            <th class="theadtr" style="width: 105px"> <?=$this->lang->line('common_basic_gross');?> </th>
            <th class="theadtr" style="width: 105px"> <abbr title="<?=$this->lang->line('common_no_of_working_days');?>"> <?=$this->lang->line('hrms_leave_management_no_of_day');?> </abbr> </th>
            <th class="theadtr" style="width: 100px">  <?=$this->lang->line('common_amount');?> </th>
        </tr>
        </thead>

        <tbody>
        <?php
        foreach($cur_wise as $cur_code=>$emp_data){

            $i = 1; $total = 0;
            foreach($emp_data as $row){
                $dPlace = $row['trDPlace'];
                $total += round($row['amount'], $dPlace);
                echo '<tr>
                        <td style="text-align: right">'.$i.'</td>                                                                                                   
                        <td >'.$row['empName'].'</td>
                        <td >'.$row['leave_des'].'</td>
                        <td style="text-align: right">'.round($row['leave_balance'], 2).'</td>                    
                        <td style="text-align: right">'.number_format($row['gross_amount'], $dPlace).'</td>
                        <td style="text-align: right">'.round($row['noOfWorkingDaysInMonth'], 2).'</td>
                        <td style="text-align: right">'.number_format($row['amount'], $dPlace).'</td>                                
                      </tr>';
                $i++;
            }

            echo '<tr>
                    <td class="t-foot" style="text-align: right" colspan="6"><b>'.$this->lang->line('common_total').' [ '.$cur_code.' ]</b></td> 
                    <td class="t-foot" style="text-align: right"><b>'.number_format($total, $dPlace).'</b></td>                            
                  </tr>';

        }
        ?>
        </tbody>
    </table>
</div>