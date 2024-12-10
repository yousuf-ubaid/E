<style>
    #rpt_table tr:hover{
        background-color: #B0BED9 !important;
    }
</style>
<table class="<?=table_class()?>" id="rpt_table">
    <thead>
    <tr>
        <th>#</th>
        <th>Member No</th>
        <th>Member Initial</th>
        <th>Member Surname</th>
        <th>NIC No</th>
        <th>Contribution</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $tot_amount = 0;
    foreach($etf_data as $key=>$row){
        $amount = abs($row['employerC']);
        $tot_amount += round($amount, 2);

        echo '<tr>
                <td>'.($key+1).'</td>
                <td>'.$row['empNo'].''.$row['memNumber'].'</td>
                <td>'.$row['initials'].'</td>
                <td>'.$row['lastName'].'</td>
                <td>'.$row['nic'].'</td>
                <td style="text-align: right">'.number_format($amount, 2).'</td>                               
              </tr>';
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5">Total</td>
        <td style="text-align: right"><?=number_format($tot_amount, 2)?></td>
    </tr>
    </tfoot>
</table>
