<div style="margin-top: 5%"> &nbsp; </div>

<div class="table-responsive">
    <table style="width: 100%" border="0">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 120px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;" valign="top">
                <table border="0">
                    <tr>
                        <td colspan="2">
                            <h2><strong><?php echo $this->common_data['company_data']['company_name']; ?></strong></h2>
                        </td>
                    </tr>
                    <tr>
                        <td><p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p></td>
                    </tr>
                    <tr>
                        <?php 
                        $title = ''; $code = ''; $date = '';
                        if($type == 'MAC') { 
                            $title = 'Allowance Claim'; 
                            $code = 'monthlyClaimCode'; 
                            $date = 'documentDate';
                        } ?>
                        <td>
                            <h4 style="margin-bottom: 0px"><?php echo $this->lang->line('common_monthly');?> <?php echo $title; ?></h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h5 style="margin-bottom: 0px"><?php echo $masterData[$code] .' - '. $masterData['description']; ?></h5> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><h5 style="margin-bottom: 0px">Document Date&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $masterData[$date]; ?></h5></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<hr>

<div class="table-responsive">
    <table style="width: 100%; margin-top: 2%; border: 0;">
        <tr>
            <td>Employee Name&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $details[0]['empName']?></td>
        </tr>
        <tr>
            <td>Employee Code&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $details[0]['ECode']?></td>
        </tr>
    </table>
</div>


<div class="table-responsive">
    <table class="<?php echo table_class(); ?>" style="width: 100%; margin-top: 2%">
        <thead>
        <tr>
            <th colspan="5" class="text-start">
                From&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $masterData['dateFrom'];?>
                &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;
                To&nbsp;&nbsp;:&nbsp;&nbsp;<?php echo $masterData['dateTo'];?>
            </th>
        </tr>
        <tr>
            <th class="theadtr" style="width: 5%">#</th>
            <th class="theadtr" style="width: 20%">Grouping Type</th>
            <th class="theadtr" style="width: 25%"><?php echo $this->lang->line('common_description'); ?></th>
            <th class="theadtr" style="width: 10%"><?php echo $this->lang->line('common_currency'); ?></th>
            <th class="theadtr" style="width: 10%"><?php echo $this->lang->line('common_amount'); ?></th>
        </tr>
        </thead>

        <tbody>
        <?php
        if(!empty($details)) {
            $i = 1;
            $n = 0;
            $currTot = 0;
            $last_currency = '';
           
            foreach ($details as $det) {
                
                $dPlace = $det['dPlace'];
                $amount = number_format($det['transactionAmount'], $dPlace);
                
                if ($det['transactionCurrency'] != $last_currency) {
                    $last_currency = $det['transactionCurrency'];
                }
                
                $description = empty($det['description']) ? $det['declarationDes'] : $det['description'];
                
                echo '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $det['declarationDes'] . '</td>
                    <td>' . $description . '</td>
                    <td>' . $det['transactionCurrency'] . '</td>
                    <td align="right">' . $amount . '</td>
                  </tr>';

                $currTot += number_format($det['transactionAmount'], $dPlace, '.', '');
                $n++;

                $m = $i - 1;
                if (array_key_exists($m, $details)) {
                    $next = $details[$m]['transactionCurrency'];
                    if ($next != $last_currency) {
                        if ($n > 1) {
                            echo '<tr><th colspan="3"></th><th class="" style="font-size: 10px">'.$this->lang->line('common_total').'</th><th class="theadtr" style="text-align: right;">' . number_format($currTot, $dPlace) . '</th></tr>';
                        }
                        $n = 0;
                        $currTot = 0;
                    }
                } else {
                    if ($n > 1) {
                        echo '<tr><th colspan="3"></th><th class="" style="font-size: 10px">'.$this->lang->line('common_total').'</th><th class="theadtr" style="text-align: right;">' . number_format($currTot, $dPlace) . '</th></tr>';
                    }
                }
            }
        } else {
            echo '<tr><td colspan="5">'.$this->lang->line('hrms_loan_no_data_available_in_table').'</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
