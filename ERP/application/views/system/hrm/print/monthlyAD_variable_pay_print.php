<div style="margin-top: 5%" > &nbsp; </div>

<div class="table-responsive">
    <table style="width: 100%" border="0px">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 120px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:80%;" valign="top">
                <table border="0px">
                    <tr>
                        <td colspan="2">
                            <h2><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h2>
                        </td>
                    </tr>
                    <tr>
                        <?php if( $type == 'MA' ){ $title = 'Addition'; $code='monthlyAdditionsCode'; $date = 'dateMA';}
                        else{ $title = 'Deduction'; $code='monthlyDeductionCode'; $date = 'dateMD';} ?>
                        <td><h4 style="margin-bottom: 0px"><?php echo $this->lang->line('common_monthly');?> <?php echo $title; ?></h4></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h5 style="margin-bottom: 0px"><?php echo $masterData[$code] .' - '. $masterData['description']; ?></h5> </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h5 style="margin-bottom: 0px">
                                <?php
                                if(array_key_exists('payrollGroupDes', $masterData)){
                                    echo '<span style="font-weight: bold">'.$masterData['payrollGroupDes'].'</span> | ';
                                }
                                echo $masterData[$date];
                                ?>
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

<div class="table-responsive">
    <table class="<?php echo table_class(); ?>" style="width: 100%; margin-top: 2%">
        <thead>
        <tr>
            <th class="theadtr" style="width: 5%">#</th>
            <th class="theadtr" style="width: 10%"><?php echo $this->lang->line('common_emp_code'); ?></th>
            <th class="theadtr" style="width: 30%"><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
            <th class="theadtr" style="width: 10%"><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
            <th class="theadtr" style="width: 30%"><?php echo $this->lang->line('hrms_loan_declaration_amount'); ?><!--Declaration Amount--></th>
            <th class="theadtr" style="width: 10%"><?php echo $this->lang->line('common_no_of_unit'); ?><!--No Of Unit--></th>
            <th class="theadtr" style="width: 10%"><?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
        </tr>
        </thead>

        <tbody>
        <?php
        if( !empty($details) ) {
            $i = 1;
            $n = 0;
            $addTot = 0;
            $currTot = 0;
            $last_currency = '';
            foreach ($details as $det) {
                $dPlace = $det['dPlace'];
                $amount = number_format($det['transactionAmount'], $dPlace);
                if ($det['transactionCurrency'] != $last_currency) {
                    echo '<tr> <th class="theadtr" colspan="7">' . $det['transactionCurrency'] . '</th></tr>';
                    $last_currency = $det['transactionCurrency'];
                }

                echo '<tr>
                    <td class="paySheetDet_TD">' . $i++ . '</td>
                    <td class="paySheetDet_TD">' . $det['ECode'] . '</td>
                    <td class="paySheetDet_TD">' . $det['empName'] . '</td>
                    <td class="paySheetDet_TD">' . $det['transactionCurrency'] . '</td>
                    <td class="paySheetDet_TD" align="right">' . number_format($det['declarationAmount'], $dPlace) . '</td>
                    <td class="paySheetDet_TD" align="right">' . $det['noOfUnits'] . '</td>
                    <td class="paySheetDet_TD" align="right">' . $amount . '</td>
                  </tr>';

                $currTot += number_format($det['transactionAmount'], $dPlace, '.', '');
                $n++;

                $m = $i - 1;
                if (array_key_exists($m, $details)) {
                    $next = $details[$m]['transactionCurrency'];

                    if ($next != $last_currency) {
                        if ($n > 1) {
                        echo '<tr><th class="" style="font-size: 10px" colspan="6">'.$this->lang->line('common_total').'</th><th class="theadtr" align="right">' . number_format($currTot, $dPlace) . '</th></tr>';
                        }
                        $n = 0;
                        $currTot = 0;
                    }
                } else {
                    if ($n > 1) {
                    echo '<tr><th class="" style="font-size: 10px" colspan="6">'.$this->lang->line('common_total').'</th><th class="theadtr" align="right">' . number_format($currTot, $dPlace) . '</th></tr>';
                    }
                }
            }
        }
        else{
            echo '<tr><td colspan="6">'.$this->lang->line('hrms_loan_no_data_available_in_table').'</td></tr>';
        }


        ?>
        </tbody>
    </table>
</div>


<?php
