<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
$title = $this->lang->line('common_double_entry').' '.$this->lang->line('hrms_final_settlement_title');

$dr_total = 0;
$cr_total = 0;
?>
<div style="margin-top: 5%" > &nbsp; </div>


<div class="table-responsive">
    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 80px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;" valign="top">
                <table>
                    <tr>
                        <td colspan="2">
                            <h4><strong><?php echo $this->common_data['company_data']['company_name'].' ('.current_companyCode().').'; ?></strong></h4>
                            <h5>Double Entry for Final Settlement</h5>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo $this->lang->line('common_document_code');?></strong></td>
                        <td><?php echo $masterData['documentCode']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <hr/>
</div>


<div class="table-responsive"><br>
    <table class="table table-bordered table-striped" style="border: 1px solid #c0c0c0">
        <thead>
        <tr>
            <th class="theadtr" style="min-width: 5%" rowspan="2">#</th>
            <th class="theadtr" colspan="6">GL Details</th>
            <th class="theadtr" colspan="2"> Amount </th>
        </tr>
        <tr>
            <th class="theadtr" style="min-width: 10%">GL Code</th>
            <th class="theadtr" style="min-width: 10%">Secondary Code</th>
            <th class="theadtr" style="min-width: 30%">GL Code Description</th>
            <th class="theadtr" style="min-width: 5%">Type</th>
            <th class="theadtr" style="min-width: 10%">Segment</th>
            <th class="theadtr" style="min-width: 10%">Currency</th>
            <th class="theadtr" style="min-width: 15%">Debit </th>
            <th class="theadtr" style="min-width: 15%">Credit </th>
        </tr>
        </thead>

        <tbody>

        <?php
        if(!empty($detail)){
            foreach ($detail as $key=>$row){
                $dr_amount = ($row['amount_type'] == 'dr')? $row['transactionAmount']: 0;
                $cr_amount = ($row['amount_type'] == 'cr')? $row['transactionAmount']: 0;
                $dr_total += $dr_amount;
                $cr_total += $cr_amount;
                $n = $key +1;
                $style = ($n == count($detail))? 'style="border-bottom: 1px solid #DBDBDB !important;"': '';
                echo '<tr '.$style.'>
                          <td>'.$n.'</td>
                          <td>'.$row['systemGLCode'].'</td>
                          <td>'.$row['GLCode'].'</td>
                          <td>'.$row['GLDescription'].'</td>
                          <td style="text-align:center;">'.$row['GLType'].'</td>
                          <td style="text-align:center;">'.$row['segmentCode'].'</td>
                          <td style="text-align:center;">'.$row['transactionCurrency'].'</td>
                          <td class="text-right">'.number_format($dr_amount, $dPlace).'</td>
                          <td class="text-right">'.number_format($cr_amount, $dPlace).'</td>
                      <tr/>';
            }
        }else{
            echo '<tr class="danger"><td colspan="9" class="text-center">No Records Found</td></tr>';
        }
        ?>
        </tbody>

        <tfoot>
        <tr>
            <td class="text-right sub_total" colspan="7">Double Entry Total (<?php echo $trCurr; ?>) </td>
            <td class="text-right total"><?php echo format_number($dr_total, $dPlace); ?></td>
            <td class="text-right total"><?php echo format_number($cr_total, $dPlace); ?></td>
        </tr>
        </tfoot>
    </table>
</div>

<?php
