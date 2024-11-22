<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('common_salary_advance_request');

$date_format_policy = date_format_policy();
$dPlaces = $emp_data['trDPlace'];
?>

<div class="modal-body" style="padding: 0px">
    <div class="row well" style="padding: 10px; margin: 10px">
        <div class="col-md-4">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_document_code');?></td>
                    <td class="bgWhite details-td" id="documentCode" width="200px"><?php echo $masterData['documentCode'] ?></td>
                </tr>
            </table>
        </div>

        <div class="col-md-5">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
                <tr>
                    <td style="width: 70px;"><?php echo $this->lang->line('common_employee');?></td>
                    <td class="bgWhite details-td" id="empNam" width="200px"><?php echo $emp_data['empNam'] ?></td>
                </tr>
            </table>
        </div>

        <div class="col-md-3">
            <table class="table table-bordered table-condensed" style="background-color: #bed4ea; font-weight: bold">
                <tr>
                    <td style="width: 150px;"><?php echo $this->lang->line('common_currency');?></td>
                    <td class="bgWhite details-td" id="curr_code" width="200px"><?php echo $emp_data['curr_code'] ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row" style="">
        <div class="col-sm-12">
            <div class="col-sm-6">
                <fieldset>
                    <legend><?php echo $this->lang->line('common_salary_declaration_detail');?></legend>

                    <table class="<?php echo table_class(); ?> add_declarationTB">
                        <thead>
                        <tr>
                            <th> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
                            <th> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
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

                        <tfoot>
                        <tr>
                            <td align="right" class="total-sd"><?php echo $this->lang->line('emp_salary_total');?></td>
                            <td align="right" class="total-sd" ><?php echo number_format( $totPayroll, $dPlaces ) ?></td></td>
                        </tr>
                        </tfoot>
                    </table>
                </fieldset>
            </div>

            <div class="col-sm-6 form-horizontal">
                <fieldset>
                    <legend><?php echo $this->lang->line('common_salary_advance_request_form');?></legend>

                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_date');?><!--Date--></label>
                        <div class="col-sm-6">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="doc_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       class="form-control" required value="<?php echo $masterData['docDate'] ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_amount');?></label>
                        <div class="col-sm-6">
                            <input type="text" name="request_amount" id="" class="form-control number" value="<?php echo $masterData['request_amount_str'] ?>" disabled />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_narration');?></label>
                        <div class="col-sm-8">
                            <textarea class="form-control" id="" name="narration" rows="2" disabled><?php echo $masterData['narration'] ?></textarea>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>

<?php
