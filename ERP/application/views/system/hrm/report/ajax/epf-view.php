<?php //echo '<pre>'; print_r($epf_data); echo '</pre>';    ?>
<style>
    #rpt_table tr:hover{
        background-color: #B0BED9 !important;
    }
</style>
<table class="<?=table_class()?>" id="rpt_table">
    <thead>
    <tr>
        <th>#</th>
        <th>NIC/Passport Number</th>
        <th>Last Name</th>
        <th>Initial</th>
        <th>Member AC Number</th>
        <th>Total Contribution</th>
        <th>Employer’s Contribution</th>
        <th>Member’s Contribution</th>
        <th>Total Earnings</th>
        <th>Member Status E=Extg.  N=New V=Vacated</th>
        <th>Zone code</th>
        <th>Employer Number</th>
        <th>Contribution Year & Month</th>
        <th>Data Submission Number</th>
        <th>No of Days Worked</th>
        <th>Occupation Classification Grade</th>
    </tr>
    </thead>

    <tbody>
    <?php
    $tot_toCount = $tot_employerCont = $tot_memberCont = $tot_earnings = 0;
    foreach($epf_data as $key=>$row){
        $tot_toCount = abs($row['toCount']);
        $tot_employerCont = abs($row['employerCont']);
        $tot_memberCont = abs($row['memberCont']);
        $tot_earnings += round($row['totEarnings'], 2);

        echo '<tr>
                <td>'.($key+1).'</td>
                <td>'.$row['nic'].'</td>
                <td>'.$row['lastName'].'</td>
                <td>'.$row['initials'].'</td>
                <td>'.$row['memNumber'].'</td>                
                <td style="text-align: right">'.number_format($row['toCount'], 2).'</td>
                <td style="text-align: right">'.number_format($row['employerCont'], 2).'</td>
                <td style="text-align: right">'.number_format($row['memberCont'], 2).'</td>
                <td style="text-align: right">'.number_format($row['totEarnings'], 2).'</td>
                <td>'.$row['memStatus'].'</td>
                <td>'.$row['znCode'].'</td> 
                <td>'.$row['empNo'].'</td> 
                <td>'.$row['contPeriod'].'</td> 
                <td>'.$row['submissionID'].'</td> 
                <td>'.$row['daysWork'].'</td> 
                <td>'.$row['ocGrade'].'</td>                                
              </tr>';
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5">Total</td>
        <td style="text-align: right"><?=number_format($tot_toCount, 2)?></td>
        <td style="text-align: right"><?=number_format($tot_employerCont, 2)?></td>
        <td style="text-align: right"><?=number_format($tot_memberCont, 2)?></td>
        <td style="text-align: right"><?=number_format($tot_earnings, 2)?></td>
        <td colspan="7"></td>
    </tr>
    </tfoot>
</table>
