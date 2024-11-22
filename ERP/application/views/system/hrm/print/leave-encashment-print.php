<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('hrms_leave_management_lang', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_encashment');

$dPlaces = $master_data['trDPlace'];
?>

<div style="margin-top: 5%" > &nbsp; </div>

<div class="table-responsive">
    <table style="width: 100%" border="0px">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 100px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;" valign="top">
                <table border="0px">
                    <tr>
                        <td>
                            <h4><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h4>
                        </td>
                    </tr>
                    <tr>
                        <td><h5 style="margin-bottom: 0px"><?=$title?></h5></td>
                    </tr>
                    <tr>
                        <td><h5 style="margin-bottom: 0px"><?php echo $master_data['documentCode']; ?></h5></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<hr>

<div class="table-responsive" style="margin-top: 10px">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width: 10%"><b><?php echo $this->lang->line('common_date');?></b></td>
            <td style=""><b>:</b></td>
            <td style="text-align: left !important;"><?php echo convert_date_format($master_data['encashment_date']); ?></td>
            <td style="width: 150px">&nbsp;</td>

            <td style="width: 10%"><b><?php echo $this->lang->line('common_currency');?></b></td>
            <td><b>:</b></td>
            <td style="text-align: left !important;"><?php echo get_currency_code($master_data['trCurrencyID']); ?></td>
            <td style="width: 100px">&nbsp;</td>

            <td style="width: 10%"><b><?php echo $this->lang->line('common_narration');?></b></td>
            <td style=""><b>:</b></td>
            <td style="text-align: left !important;"><?php echo $master_data['narration']; ?></td>
        </tr>
        </tbody>
    </table>
</div>


<div class="row" style="margin: 10px 15px;">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th class="theadtr"> # </th>
            <th class="theadtr" style="width: 150px"> <?php echo $this->lang->line('common_employee_name');?> </th>
            <th class="theadtr" style="width: 100px"> <?php echo $this->lang->line('hrms_leave_management_leave_type');?> </th>
            <th class="theadtr" style="width: 105px"> <?php echo $this->lang->line('hrms_leave_management_leave_balance');?> </th>
            <th class="theadtr" style="width: 105px"> <?php echo $this->lang->line('hrms_leave_management_leave_encashment_days');?> </th>
            <th class="theadtr" style="width: 105px"> <?php echo $this->lang->line('common_basic_gross');?> </th>
            <th class="theadtr" style="width: 105px"> <abbr title="<?php echo $this->lang->line('common_no_of_working_days');?>"> <?php echo $this->lang->line('hrms_leave_management_no_of_day');?> </abbr> </th>
            <th class="theadtr" style="width: 90px">  <?php echo $this->lang->line('common_amount');?> </th>
            <th class="theadtr" style="width: 170px">  <?php echo $this->lang->line('common_narration');?> </th>
        </tr>
        </thead>

        <tbody>
        <?php
        $i = 1; $dPlace = $master_data['trDPlace']; $total = 0;
        if(!empty($details)){
            foreach ($details as $row){
                $total += $row['amount'];

                echo '<tr>
                          <td style="text-align: right">'.$i.'</td>
                          <td >'.$row['empName'].'</td> 
                          <td >'.$row['description'].'</td>
                          <td style="text-align: right">'.$row['leave_balance'].'</td>
                          <td style="text-align: right">'.$row['encashment_days'].'</td>
                          <td style="text-align: right">'.number_format($row['gross_amount'], $dPlace).'</td>
                          <td style="text-align: right">'.$row['noOfWorkingDaysInMonth'].'</td>
                          <td style="text-align: right">'.number_format($row['amount'], $dPlace).'</td>
                          <td style="text-align: center">'.$row['narration'].'</td> 
                      </tr>';
                $i++;
            }

                echo '<tr>
                        <td colspan="6" class="sub_total">&nbsp;</td>                                       
                        <td class="sub_total"><b>Total</b></td>                                                    
                        <td class="sub_total" style="text-align: right">'.number_format($total, $dPlace).'</td>                                                    
                        <td colspan="2" class="sub_total">&nbsp;</td>                    
                      </tr>';

            $i++;

        }
        else{
            $no_record_found = $this->lang->line('common_no_records_found');
            echo '<tr><td colspan="11" align="center">'.$no_record_found.'</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>


<?php
