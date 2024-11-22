<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('fn_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('fn_man_investment_details');

$convertFormat = convert_date_format();

?>


<table class="<?php echo table_class(); ?>" id="disburseTB">
    <thead>
    <tr>
        <th style="min-width: 5%">#</th>
        <th style="width:auto"><?php echo $this->lang->line('common_date');?></th>
        <th style="width:auto"><?php echo $this->lang->line('common_bank');?></th>
        <th style="width:auto">PV Code</th>
        <th style="width:auto"><?php echo $this->lang->line('common_amount');?></th>
        <th style="width:auto"><?php echo $this->lang->line('common_narration');?></th>
        <th style="width: 5%; padding-right: 6px;"">
            <div class="pull-right">
                <!--<span class="glyphicon glyphicon-trash" onclick="removeAll_employee('in-charge')" style="color:#d15b47;"></span>-->
            </div>
        </th>
    </tr>
    </thead>

    <tbody>
    <?php
        $i = 1; $dPlace = 3; $total = 0;
        foreach($disData as $item){
            $amount = round($item['disburseAmount'], $dPlace);
            $confirmedYN = $item['confirmedYN'];
            $bankGL = $item['disburseBankGL'];
            $narration = $item['narration'];
            $total += round($amount, $dPlace);
            $date = format_date($item['disburseDate'],$convertFormat);

            $action = '';

            if( $confirmedYN == 0 ){
                $action = '<a href="#" onclick="edit_disburse(\''.$item['detID'].'\', \''.$amount.'\', \''.$date.'\', \''.$bankGL.'\', \''.$narration.'\')">                   
                            <span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span>
                        </a>';
            }

        echo '<tr>
                <td>'.$i.'</td>
                <td>'.$date.'</td>
                <td>'.$item['bankName'].' - '.$item['bankSwiftCode'].'</td>
                <td>'.$item['PVcode'].'</td>
                <td align="right">'.number_format($amount, $dPlace).'</td>
                <td align="right">'.$narration.'</td>
                <td>'.$action.'</td>
              </tr>';
            $i++;
        }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align: right"><?php echo $this->lang->line('fn_man_total_disburse');?></td>
            <td style="text-align: right"><?php echo number_format($total, $dPlace); ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<script>
    $('#disburseTB').DataTable();
</script>
<?php
