<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
?>

<table class="<?php echo table_class() ?>">
    <thead>
    <tr>
        <th><?php echo $this->lang->line('common_code');?></th>
        <th><?php echo $this->lang->line('common_description');?></th>
        <th><?php echo $this->lang->line('hrms_final_settlement_loan_amount');?></th>
        <th><?php echo $this->lang->line('hrms_final_settlement_pending_amount');?></th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    <?php
    $totPending = 0;
    foreach ($loanDetails as $row){
        $totPending += round($row['totPending'], $dPlace);
        echo '<tr>                    
                 <td>'.$row['loanCode'].'</td>
                 <td>'.$row['description'].'</td>
                 <td align="right">'.number_format($row['amount'], $dPlace).'</td>                  
                 <td align="right">'.number_format($row['totPending'], $dPlace).'</td>
                 <td align="center">
                    <input type="checkbox" name="loans[]" checked value="'.$row['ID'].'" />
                 </td>
              </tr>';
    }

    if(count($loanDetails) > 1) {
        echo '<tr>
                <td colspan="3" class="total-sd" align="right">Total</td> 
                <td class="total-sd" align="right">' . number_format($totPending, $dPlace) . '</td> 
                <td></td>
              </tr>';
    }
    ?>
    </tbody>
</table>

<?php
