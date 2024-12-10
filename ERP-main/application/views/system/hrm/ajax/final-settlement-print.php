<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$title = $this->lang->line('hrms_final_settlement_title');


$fn_data = final_settlement_data($masterID);
$masterData = $fn_data['masterData'];
$isConfirmed = $masterData['confirmedYN'];
$payrollSal = $fn_data['payroll'];
$non_payrollSal = $fn_data['non_payroll'];
$docDate = convert_date_format($masterData['createdDateTime']);
$dateJoin = convert_date_format($masterData['dateOfJoin']);
$lastWorkingDay = convert_date_format($masterData['lastWorkingDay']);
$dPlaces = $masterData['trDPlace'];
$fn_items_drop = fetch_final_settlement_items();
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
                        <td><h4 style="margin-bottom: 0px"><?php echo $this->lang->line('hrms_final_settlement_title') ?></h4></td>
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

<hr/>

<div class="table-responsive" style="margin-top: 10px">
    <table style="width: 100%" >
        <tbody>
        <tr>
            <td style=""><strong><?php echo $this->lang->line('common_employee');?></strong></td>
            <td style=""><strong>:</strong></td>
            <td style=""><?php echo $masterData['ECode'].' | '.$masterData['Ename2']; ?></td>

            <td width="20%"><strong><?php echo $this->lang->line('common_date');?></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $docDate; ?></td>
        </tr>

        <tr>
            <td style=""><strong><?php echo $this->lang->line('common_currency');?></strong></td>
            <td style=""><strong>:</strong></td>
            <td style=""><?php echo get_currency_code($masterData['trCurrencyID']); ?></td>

            <td width="20%"><strong><?php echo $this->lang->line('emp_date_joined');?></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $dateJoin; ?></td>
        </tr>

        <tr>
            <td style=""><strong><?php echo $this->lang->line('emp_lastworking_date');?></strong></td>
            <td style=""><strong>:</strong></td>
            <td style=""><?php echo $lastWorkingDay; ?></td>

            <td width="20%"><strong><?php echo $this->lang->line('common_narration');?></strong></td>
            <td><strong>:</strong></td>
            <td><?php echo $masterData['narration']; ?></td>
        </tr>
        </tbody>
    </table>
</div><br>

<div class="row" style="margin: 10px 15px;">
    <table>
        <tr>
            <td style="width: 50%; vertical-align: top">
                <h5><?php echo $this->lang->line('emp_bank_payroll');?></h5>

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
                <h5><?php echo $this->lang->line('emp_bank_non_payroll');?></h5>
                <table class="<?php echo table_class(); ?> add_declarationTB">
                    <thead>
                    <tr>
                        <th class="theadtr"> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                        <th class="theadtr"> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $totNonPayroll = 0;
                    if( !empty($non_payrollSal) ){
                        foreach($non_payrollSal as $rowAdd){
                            echo '<tr>
                                    <td>'.$rowAdd['salaryDescription'].'</td>                                  
                                    <td align="right">'.number_format( $rowAdd['amount'], $dPlaces ).'</td>
                                  </tr>';
                            $totNonPayroll += round( $rowAdd['amount'], $dPlaces);
                        }
                    }else{
                        echo '<tr><td align="center" colspan="2">'.$this->lang->line('common_no_records_found').'</td></tr>';
                    }
                    ?>
                    </tbody>

                    <?php if( !empty($non_payrollSal) ){ ?>
                        <tfoot><tr><td align="right" class="total-sd"><?php echo $this->lang->line('emp_salary_total');?></td>
                            <td align="right" class="total-sd"><?php echo number_format( $totNonPayroll, $dPlaces ) ?></td></tr></tfoot>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="row" style="margin: 10px 15px;">
    <table>
        <tr>
            <td style="width: 50%; vertical-align: top">
                <h5><?php echo $this->lang->line('common_addition');?></h5>
                <table class="<?php echo table_class(); ?>" id="addition-tb">
                    <thead>
                    <tr>
                        <th class="theadtr"> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                        <th class="theadtr"> <?php echo $this->lang->line('common_narration');?></th>
                        <th style="width: 85px" class="theadtr"> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $totAdd = 0;
                    if( !empty($addView) ){
                        foreach($addView as $rowDet){
                            $typeID = $rowDet['typeID']; $mnDec = '';
                            if($rowDet['typeID'] == 2){ /* Other Additions*/
                                $mnDec = (!empty($rowDet['mnDec']))? ' | '.$rowDet['mnDec']: '';
                            }

                            echo '<tr>
                                        <td>'.$rowDet['description'].' '.$mnDec.'</td>                                  
                                        <td>'.$rowDet['narration'].'</td>                                                                                              
                                        <td style="width: 85px" align="right">'.number_format( $rowDet['amount'], $dPlaces ).'</td>                                             
                                      </tr>';
                            $totAdd += round( $rowDet['amount'], $dPlaces);
                        }
                    }else{
                        echo '<tr><td align="center" colspan="4">'.$this->lang->line('common_no_records_found').'</td></tr>';
                    }
                    ?>
                    </tbody>

                    <?php if( !empty($addView) ){ ?>
                        <tfoot><tr><td align="right" class="total-sd" colspan="2"><?php echo $this->lang->line('emp_salary_total');?></td>
                            <td align="right" class="total-sd"><?php echo number_format( $totAdd, $dPlaces ) ?></td></tr></tfoot>
                    <?php } ?>
                </table>
            </td>

            <td style="width: 50%; vertical-align: top">
                <h5><?php echo $this->lang->line('common_deduction');?></h5>
                <table class="<?php echo table_class(); ?>" id="deduction-tb">
                    <thead>
                    <tr>
                        <th class="theadtr"> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                        <th class="theadtr"> <?php echo $this->lang->line('common_narration');?></th>
                        <th style="width: 85px" class="theadtr"> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $totDed = 0;
                    if( !empty($dedView) ){
                        foreach($dedView as $rowDet){
                            $typeID = $rowDet['typeID']; $mnDec = '';
                            if($rowDet['typeID'] == 6){ /*Other Deductions*/
                                $mnDec = (!empty($rowDet['mnDec']))? ' | '.$rowDet['mnDec']: '';
                            }

                            if ($typeID == 12){ /* Adjustment*/
                                $mnDec = (!empty($rowDet['othDes']))? ' | '.$rowDet['othDes']: '';
                            }

                            echo '<tr>
                                        <td>'.$rowDet['description'].' '.$mnDec.'</td>                                  
                                        <td>'.$rowDet['narration'].'</td>                                                                                              
                                        <td style="width: 85px" align="right">'.number_format( $rowDet['amount'], $dPlaces ).'</td>                                             
                                      </tr>';
                            $totDed += round( $rowDet['amount'], $dPlaces);
                        }
                    }else{
                        echo '<tr><td align="center" colspan="4">'.$this->lang->line('common_no_records_found').'</td></tr>';
                    }
                    ?>
                    </tbody>

                    <?php if( !empty($dedView) ){ ?>
                        <tfoot><tr><td align="right" class="total-sd" colspan="2"><?php echo $this->lang->line('emp_salary_total');?></td>
                            <td align="right" class="total-sd"><?php echo number_format( $totDed, $dPlaces ) ?></td></tr></tfoot>
                    <?php } ?>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="total-sd" style="margin: 10px 15px; font-size: 14px !important; display: flex;">
    <div style="display:inline-block; float: left; width: 50%;"><?php echo $this->lang->line('common_net_amount');?></div>
    <div style="display:inline-block; float: left; width: 50%; text-align: right"><?php echo number_format( ($totAdd+$totDed), $dPlaces ) ?></div>
</div>

<table>
    <tr>
        <td style="height:50px">&nbsp;</td>
    </tr>
</table>

<div class="table-responsive" style="padding: 0 15px;">
    <table style="width: 100%;">
        
        <tr>
            <td style="font-size:12px;">
            I, <?php echo $masterData['Ename2']; ?>, hereby accept the above calculation of my dues and labor entitlements  for the period of my employment with the company, totaling to <?php echo get_currency_code($masterData['trCurrencyID']); ?> <?php echo number_format( ($totAdd+$totDed), $dPlaces ) ?> and acquit the company of any further financial claim in this regard.
            </td>
        </tr>
    </table>
</div>

<table>
    <tr>
        <td style="height:50px">&nbsp;</td>
    </tr>
</table>

<div class="table-responsive">
    <table style="width: 33.3%;">
        <tr>           
            <td style="width:33.3%">
                <table>
                    <tr>
                        <td align="center" style="text-align:center">______________________________</td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align:center">Signature :</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<?php
