<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$responseType = $this->uri->segment(3);
if($responseType == 'print') {
    $firstMonth = $this->input->post('firstMonth');
    $secondMonth = $this->input->post('secondMonth');
?>
    <table style="width: 100%">
        <tbody>
            <tr>
            <td style="width:20%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:80%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong>
                                    <?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; ?>
                                </strong>
                            </h3>
                            <h4>Salary Comparison</h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <?php echo date('Y F', strtotime($firstMonth)).' &nbsp;&nbsp;-&nbsp;&nbsp; '. date('Y F', strtotime($secondMonth)); ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <?php
}
if ($reportData) {
    ?>
    <span style="font-size: 12px; font-weight: bold;margin-right: 20px"><?php echo $this->lang->line('hrms_reports_first_month');?><!--FM - First Month--></span>
    <span style="margin-left:20px;font-size: 12px; font-weight: bold"><?php echo $this->lang->line('hrms_reports_scond_month');?><!--SM - Second Month--></span>
    <br/>
    <h5 class="selected-employee-det well well-sm" style="display: none; margin-bottom: 2px;"></h5>
    <div style="height: 450px">
        <table id="salaryComparisonTB" class="table table-bordered"  style="margin-top: -2px">
            <thead>
            <tr>
                <th class="first" rowspan="2" style="width:80px;"><?php echo $this->lang->line('hrms_reports_employee_id');?><!--Emp&nbsp;ID--></th>
                <th rowspan="2" style="width:180px"><?php echo $this->lang->line('hrms_reports_employee_name');?><!--Employee&nbsp;Name--></th>
                <th style="background-color: #FDFAF7" colspan="3"><?php echo $this->lang->line('hrms_reports_fixed_payments');?><!--Fixed Payments--></th>
                <th style="background-color: #EFEFF2" colspan="3"><?php echo $this->lang->line('hrms_reports_variables');?><!--Variables--></th>
                <th style="background-color: #ECF4F7" colspan="3"><?php echo $this->lang->line('hrms_reports_monthly_addition');?><!--Monthly Addition--></th>
                <th style="background-color: #F6F3F4" colspan="3"><?php echo $this->lang->line('hrms_reports_monthly_deduction');?><!--Monthly Deduction--></th>
                <th style="background-color: #F7FBFB" colspan="3"><?php echo $this->lang->line('hrms_reports_other_addition');?> <!--Other Addition--></th>
                <th style="background-color: #FDFEFD" colspan="3"><?php echo $this->lang->line('hrms_reports_other_deduction');?><!--Other Deduction--></th>
                <th style="background-color: #EFEFF2" colspan="3"><?php echo $this->lang->line('hrms_reports_net_salary');?><!--Net Salary--></th>
            </tr>
            <tr>
                <th style="background-color: #FDFAF7"><?php echo $this->lang->line('hrms_reports_first_month1');?><!--FM--></th>
                <th style="background-color: #FDFAF7"><?php echo $this->lang->line('hrms_reports_scond_month1');?><!--SM--></th>
                <th style="background-color: #FDFAF7"><?php echo $this->lang->line('hrms_reports_deferent');?><!--Diff--></th>
                <th style="background-color: #EFEFF2;"><?php echo $this->lang->line('hrms_reports_first_month1');?><!--FM--></th><!--FM--></th>
                <th style="background-color: #EFEFF2;"><?php echo $this->lang->line('hrms_reports_scond_month1');?><!--SM--></th>
                <th style="background-color: #EFEFF2;"><?php echo $this->lang->line('hrms_reports_deferent');?><!--Diff--></th>
                <th style="background-color: #ECF4F7"><?php echo $this->lang->line('hrms_reports_first_month1');?><!--FM--></th>
                <th style="background-color: #ECF4F7"><?php echo $this->lang->line('hrms_reports_scond_month1');?><!--SM--></th>
                <th style="background-color: #ECF4F7"><?php echo $this->lang->line('hrms_reports_deferent');?><!--Diff--></th>
                <th style="background-color: #F6F3F4;"><?php echo $this->lang->line('hrms_reports_first_month1');?><!--FM--></th>
                <th style="background-color: #F6F3F4;"><?php echo $this->lang->line('hrms_reports_scond_month1');?><!--SM--></th>
                <th style="background-color: #F6F3F4;"><?php echo $this->lang->line('hrms_reports_deferent');?><!--Diff--></th>
                <th style="background-color: #F7FBFB;"><?php echo $this->lang->line('hrms_reports_first_month1');?><!--FM--></th>
                <th style="background-color: #F7FBFB;"><?php echo $this->lang->line('hrms_reports_scond_month1');?><!--SM--></th>
                <th style="background-color: #F7FBFB;"><?php echo $this->lang->line('hrms_reports_deferent');?><!--Diff--></th>
                <th style="background-color: #FDFEFD;"><?php echo $this->lang->line('hrms_reports_first_month1');?><!--FM--></th>
                <th style="background-color: #FDFEFD;"><?php echo $this->lang->line('hrms_reports_scond_month1');?><!--SM--></th>
                <th style="background-color: #FDFEFD;"><?php echo $this->lang->line('hrms_reports_deferent');?><!--Diff--></th>
                <th style="background-color: #EFEFF2"><?php echo $this->lang->line('hrms_reports_first_month1');?><!--FM--></th>
                <th style="background-color: #EFEFF2"><?php echo $this->lang->line('hrms_reports_scond_month1');?><!--SM--></th>
                <th style="background-color: #EFEFF2"><?php echo $this->lang->line('hrms_reports_deferent');?><!--Diff--></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $fr_fixPayment = 0;
            $sn_fixPayment = 0;
            $diffFixPayment = 0;
            $fr_Variables = 0;
            $sn_Variables = 0;
            $DiffVariables = 0;
            $fr_MA = 0;
            $sn_MA = 0;
            $DiffMA = 0;
            $fr_MD = 0;
            $sn_MD = 0;
            $DiffMD = 0;
            $fr_OtherAdditions = 0;
            $sn_OtherAdditions = 0;
            $DiffOtherAdditions = 0;
            $fr_OtherDeductions = 0;
            $sn_OtherDeductions = 0;
            $DiffOtherAddition = 0;
            $fr_netSalary = 0;
            $sn_netSalary = 0;
            $differentSalary = 0;


            foreach ($reportData as $val) {

                $fr_fixPayment += $val['fr_amount_1'];
                $sn_fixPayment += $val['sn_amount_1'];
                $diffFixPayment += ( $val['fr_amount_1'] - $val['sn_amount_1']);
                $fr_Variables += $val['fr_amount_2'];
                $sn_Variables += $val['sn_amount_2'];
                $DiffVariables += ( $val['fr_amount_2'] - $val['sn_amount_2']);
                $fr_MA += $val['fr_amount_3'];
                $sn_MA += $val['sn_amount_3'];
                $DiffMA += ( $val['fr_amount_3'] - $val['sn_amount_3']);
                $fr_MD += $val['fr_amount_4'];
                $sn_MD += $val['sn_amount_4'];
                $DiffMD += ( $val['fr_amount_4'] - $val['sn_amount_4']);
                $fr_OtherAdditions += $val['fr_amount_5'];
                $sn_OtherAdditions += $val['sn_amount_5'];
                $DiffOtherAdditions += ( $val['fr_amount_5'] - $val['sn_amount_5']);
                $fr_OtherDeductions += $val['fr_amount_6'];
                $sn_OtherDeductions += $val['sn_amount_6'];
                $DiffOtherAddition += ( $val['fr_amount_6'] - $val['sn_amount_6']);
                $fr_netSalary += $val['fr_amount_7'];
                $sn_netSalary += $val['sn_amount_7'];
                $differentSalary += ( $val['fr_amount_7'] - $val['sn_amount_7']);;
                ?>
                <tr data-value="<?php echo $val['Ecode'].' &nbsp; - &nbsp; '.$val['Ename2']; ?>">
                    <td style="font-weight: bold"><?php echo $val['Ecode'] ?></td>
                    <td><?php echo $val['Ename2'] ?></td>
                    <td style="text-align: right;background-color: #FDFAF7"><?php echo number_format($val['fr_amount_1'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #FDFAF7"><?php echo number_format($val['sn_amount_1'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #FDFAF7;<?php echo (($val['fr_amount_1']-$val['sn_amount_1']) == 0) ? 'color:green' : 'color:red'; ?> ">
                        <?php echo number_format(($val['fr_amount_1']-$val['sn_amount_1']), $val['dPlace']) ?>
                    </td>
                    <td style="text-align: right;background-color: #EFEFF2;"><?php echo number_format($val['fr_amount_2'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #EFEFF2;"><?php echo number_format($val['sn_amount_2'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #EFEFF2;<?php echo (($val['fr_amount_2']-$val['sn_amount_2']) == 0) ? 'color:green' : 'color:red'; ?>">
                        <?php echo number_format(($val['fr_amount_2']-$val['sn_amount_2']), $val['dPlace']) ?>
                    </td>
                    <td style="text-align: right;background-color: #ECF4F7;"><?php echo number_format($val['fr_amount_3'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #ECF4F7;"><?php echo number_format($val['sn_amount_3'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #ECF4F7;<?php echo (($val['fr_amount_3']-$val['sn_amount_3']) == 0) ? 'color:green' : 'color:red'; ?>">
                        <?php echo number_format(($val['fr_amount_3']-$val['sn_amount_3']), $val['dPlace']) ?>
                    </td>
                    <td style="text-align: right;background-color: #F6F3F4;"><?php echo number_format($val['fr_amount_4'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #F6F3F4;"> <?php echo number_format($val['sn_amount_4'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #F6F3F4;<?php echo (($val['fr_amount_4']-$val['sn_amount_4']) == 0) ? 'color:green' : 'color:red'; ?>">
                        <?php echo number_format(($val['fr_amount_4']-$val['sn_amount_4']), $val['dPlace']) ?>
                    </td>
                    <td  style="text-align: right;background-color: #F7FBFB;"> <?php echo number_format($val['fr_amount_5'], $val['dPlace']) ?></td>
                    <td  style="text-align: right;background-color: #F7FBFB;"><?php echo number_format($val['sn_amount_5'], $val['dPlace']) ?></td>
                    <td  style="text-align: right;background-color: #F7FBFB;<?php echo (($val['fr_amount_5']-$val['sn_amount_5']) == 0) ? 'color:green' : 'color:red'; ?>">
                        <?php echo number_format(($val['fr_amount_5']-$val['sn_amount_5']), $val['dPlace']) ?>
                    </td>
                    <td style="text-align: right;background-color: #FDFEFD;"><?php echo number_format($val['fr_amount_6'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #FDFEFD;"><?php echo number_format($val['sn_amount_6'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #FDFEFD;<?php echo (($val['fr_amount_6']-$val['sn_amount_6']) == 0) ? 'color:green' : 'color:red'; ?>">
                        <?php echo number_format(($val['fr_amount_6']-$val['sn_amount_6']), $val['dPlace']) ?>
                    </td>
                    <td style="text-align: right;background-color: #EFEFF2;"><?php echo number_format($val['fr_amount_7'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #EFEFF2;"><?php echo number_format($val['sn_amount_7'], $val['dPlace']) ?></td>
                    <td style="text-align: right;background-color: #EFEFF2;<?php echo (($val['fr_amount_7']-$val['sn_amount_7']) == 0) ? 'color:green' : 'color:red'; ?>">
                        <?php echo number_format(($val['fr_amount_7']-$val['sn_amount_7']), $val['dPlace']) ?>
                    </td>
                </tr>

                <?php
            }
            ?>
            </tbody>

            <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td style="text-align: right;background-color: #FDFAF7;"><?php echo format_number($fr_fixPayment, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #FDFAF7;"><?php echo format_number($sn_fixPayment, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #FDFAF7;<?php echo ($diffFixPayment == 0)? 'color:green':'color:red';?>">
                    <?php echo format_number($diffFixPayment, $val['dPlace']); ?>
                </td>
                <td style="text-align: right;background-color: #EFEFF2;"><?php echo format_number($fr_Variables, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #EFEFF2;"><?php echo format_number($sn_Variables, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #EFEFF2; <?php echo ($DiffVariables == 0)? 'color:green':'color:red';?>">
                    <?php echo format_number($DiffVariables, $val['dPlace']); ?>
                </td>
                <td style="text-align: right;background-color: #ECF4F7;"><?php echo format_number($fr_MA, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #ECF4F7;"><?php echo format_number($sn_MA, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #ECF4F7;<?php echo ($DiffMA == 0)? 'color:green':'color:red';?>">
                    <?php echo format_number($DiffMA, $val['dPlace']); ?>
                </td>
                <td  style="text-align: right;background-color: #F6F3F4;"><?php echo format_number($fr_MD, $val['dPlace']); ?></td>
                <td  style="text-align: right;background-color: #F6F3F4;"><?php echo format_number($sn_MD, $val['dPlace']); ?></td>
                <td  style="text-align: right;background-color: #F6F3F4;<?php echo ($DiffMD == 0)? 'color:green':'color:red';?>">
                    <?php echo format_number($DiffMD, $val['dPlace']); ?>
                </td>
                <td  style="text-align: right;background-color: #F7FBFB;"><?php echo format_number($fr_OtherAdditions, $val['dPlace']); ?></td>
                <td  style="text-align: right;background-color: #F7FBFB;"><?php echo format_number($sn_OtherAdditions, $val['dPlace']); ?></td>
                <td  style="text-align: right;background-color: #F7FBFB;<?php echo ($DiffOtherAdditions == 0)? 'color:green':'color:red';?>">
                    <?php echo format_number($DiffOtherAdditions, $val['dPlace']); ?>
                </td>
                <td style="text-align: right;background-color: #FDFEFD;"><?php echo format_number($fr_OtherDeductions, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #FDFEFD;"><?php echo format_number($sn_OtherDeductions, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #FDFEFD;<?php echo ($DiffOtherAddition == 0)? 'color:green' :'color:red';?>">
                    <?php echo format_number($DiffOtherAddition, $val['dPlace']); ?>
                </td>
                <td style="text-align: right;background-color: #EFEFF2;"><?php echo format_number($fr_netSalary, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #EFEFF2;"><?php echo format_number($sn_netSalary, $val['dPlace']); ?></td>
                <td style="text-align: right;background-color: #EFEFF2;<?php echo ($differentSalary == 0)? 'color:green' :'color:red';?>">
                    <?php echo format_number($differentSalary, $val['dPlace']); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
    <h5 class="selected-employee-det well well-sm" style="display: none; margin-bottom: -7px;"></h5>
    <?php
}
else {
    ?>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php if ($error) {
                    echo $error;
                } else { ?>
                    No Records found.
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}

if($responseType == 'print') {
?>

<script>
    var selected_employee_det = $('.selected-employee-det');

    $('#salaryComparisonTB').on('click', 'tr', function () {
        $(this).addClass('highlight').siblings().removeClass('highlight');

        var curEmp = $(this).attr('data-value');
        if(curEmp != undefined){
            selected_employee_det.css('display', 'block');
            selected_employee_det.html(curEmp);
        }
        else{
            selected_employee_det.css('display', 'none');
        }
    });
</script>
<?php } ?>