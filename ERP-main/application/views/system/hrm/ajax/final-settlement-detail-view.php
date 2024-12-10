<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_final_settlement_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('employee_master', $primaryLanguage);
?>
<thead>
<tr>
    <th> <?php echo $this->lang->line('emp_description');?><!--Description--></th>
    <th> <?php echo $this->lang->line('common_narration');?></th>
    <th style="width: 85px"> <?php echo $this->lang->line('emp_salary_amount');?><!--Amount--></th>
    <th> </th>
</tr>
</thead>
<tbody>
<?php
$totAdd = 0;
if( !empty($details) ){
    foreach($details as $rowDet){
        $fsDetID = $rowDet['fsDetID'];
        $typeID = $rowDet['typeID'];
        $moreDec = $str = '';
        if(in_array($typeID, [1,7,8,12,13,14])){ /*Salary, SSO, Loan recovery, adjustment, PAYE, Leave Payment*/
            $str = '<button type="button" title="More Detail" onclick="more_fs_item('.$typeID.', '.$fsDetID.')"  class="fs-actionBtn btn btn-default btn-xs">';
            $str .= '<span class="glyphicon glyphicon-info-sign" style="color:#3c8dbc;"></span></button>';

            if ($typeID == 12){ /* Adjustment*/
                $moreDec = (!empty($rowDet['othDes']))? ' | '.$rowDet['othDes']: '';
            }
        }
        elseif (in_array($typeID, [2,6])){ /* Other Additions and Other Deductions*/
            $moreDec = (!empty($rowDet['mnDec']))? ' | '.$rowDet['mnDec']: '';
        }


        $delBtn = '';
        if($isConfirmed != 1){
            $delBtn = '<button title="Delete" onclick="delete_fs_item_confirmation('.$fsDetID.')" class="fs-actionBtn btn btn-default btn-xs"><span class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></button>';
        }


        echo '<tr>
                <td>'.$rowDet['description'].' '.$moreDec.'</td>                                  
                <td>'.$rowDet['narration'].'</td>                                                                                              
                <td style="width: 85px" align="right">'.number_format( $rowDet['amount'], $dPlace ).'</td>
                <td style="text-align: right"> '.$str.'  '.$delBtn.'</td>                                  
              </tr>';
        $totAdd += round( $rowDet['amount'], $dPlace);
    }
}else{
    echo '<tr><td align="center" colspan="4">'.$this->lang->line('common_no_records_found').'</td></tr>';
}
?>
</tbody>

<?php if( !empty($details) ){ ?>
    <tfoot><tr><td align="right" class="total-sd" colspan="2"><?php echo $this->lang->line('emp_salary_total');?></td>
        <td align="right" class="total-sd"><?php echo number_format( $totAdd, $dPlace ) ?></td><td></td></tr></tfoot>
<?php } ?>


<?php
