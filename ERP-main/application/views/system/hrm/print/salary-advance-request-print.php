<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_leave_management_leave_encashment');

$dPlaces = $emp_data['trDPlace'];
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
                        <td colspan="2">
                            <h2><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h2>
                        </td>
                    </tr>
                    <tr>
                        <td><h4 style="margin-bottom: 0px"><?php echo $this->lang->line('common_salary_advance_request') ?></h4></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h5 style="margin-bottom: 0px"><?php echo $masterData['documentCode']; ?></h5> </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive" style="margin-top: 10px">
    <table style="width: 100%" >
        <tbody>
        <tr>
            <td style=""><strong><?php echo $this->lang->line('common_employee');?></strong></td>
            <td style=""><strong>:</strong></td>
            <td style=""><?php echo $emp_data['empNam']; ?></td>

            <td width="20%"><strong><?php echo $this->lang->line('common_currency');?></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $emp_data['curr_code']; ?></td>
        </tr>

        <tr>
            <td style=""><strong><?php echo $this->lang->line('common_date');?></strong></td>
            <td style=""><strong>:</strong></td>
            <td style=""><?php echo $masterData['docDate']; ?></td>

            <td width="20%"><strong><?php echo $this->lang->line('common_amount');?></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData['request_amount_str']; ?></td>
        </tr>
        <tr>
            <td style=""><strong><?php echo $this->lang->line('common_narration');?></strong></td>
            <td style=""><strong>:</strong></td>
            <td style=""><?php echo $masterData['narration']; ?></td>

            <td width="20%"><strong> </strong></td>
            <td><strong> </strong></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div><br>

<div class="row" style="margin: 10px 15px;">
    <table>
        <tr>
            <td style="width: 50%; vertical-align: top">
                <h5><?php echo $this->lang->line('common_salary_declaration_detail');?></h5>

                <table class="table table-bordered table-striped add_declarationTB">
                    <thead>
                    <tr>
                        <th class="theadtr"> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                        <th class="theadtr"> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $totPayroll = 0;
                    if( !empty($payrollSal) ){
                        foreach($payrollSal as $rowAdd){
                            echo '<tr>
                                <td>'.$rowAdd['salaryDescription'].'</td>                                  
                                <td align="right">'.number_format( $rowAdd['amount'], $dPlaces ).'</td>
                              </tr>';
                            $totPayroll += round( $rowAdd['amount'], $dPlaces);
                        }
                    }else{
                        echo '<tr><td align="center" colspan="2">'.$this->lang->line('common_no_records_found').'</td></tr>';
                    }
                    ?>
                    </tbody>

                    <?php if( !empty($payrollSal) ){ ?>
                        <tfoot><tr><td align="right" class="total-sd"><?php echo $this->lang->line('emp_salary_total');?></td>
                            <td align="right" class="total-sd"><?php echo number_format( $totPayroll, $dPlaces ) ?></td></tr></tfoot>
                    <?php } ?>
                </table>
            </td>

            <td style="width: 50%; vertical-align: top">
                &nbsp;
            </td>
        </tr>
    </table>
</div>

<?php
