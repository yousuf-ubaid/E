<div style="padding: 0px 20px">
    <?php echo $repData['documentCode']; ?><br/>
    <?php echo date('m/d/Y'); ?>
</div>

<div style="padding: 30px 20px 0px">
    The Manager <br/>
    <?php //=$repData['address_to']?><br/>
    <?php echo $accountDetails['BankName'] ?><br/>
    <?php echo $accountDetails['bnkBranchName'] ?>
</div>

<?php
$salaryBreak = '';
$total = $salary_det['grossSalary'];
$currency = $salary_det['trCur'];
$decimal = ($salary_det['trCur'] == 'OMR')? 3: 2;
$masterCom = '';// ;get_master_companyData();

$fraction = number_format(($total  - (int)$total), $decimal);
$in_word = $this->numbertowords->convert_number($total);
$divisionBy = ($currency == 'OMR')? '1000': '100';
$he_she = ($repData['gender'] == 2)? 'She': 'He';
$his_her = ($repData['gender'] == 2)? 'her': 'his';
$him_her = ($repData['gender'] == 2)? 'her': 'him';

?>

<p style="padding-top: 30px; padding-left: 20px;"> Dear Sirs, </p>
<p style="padding-left: 20px;"><b>Transfer of Salary of <?php echo $repData['empTitle'].' '.$repData['Ename2']; ?></b> </p>
<p style="padding-left: 20px;"><b>Account <?php echo $repData['accountNo']; ?></b> </p>

<p style="padding: 30px 20px 0px; text-align:justify">
    <?php
    echo 'This  is  to  certify  that <strong>'.$repData['empTitle'].' '.$repData['Ename2'].'</strong>, is our 
    employment from <strong>'.date('dS F Y', strtotime($repData['EDOJ'])).'</strong>.<span style="text-transform: capitalize">
    '.$his_her.'</span> Monthly gross salary  <strong>' .(($currency == 'OMR')? 'R.O': $currency).'. '.number_format($total, $decimal).' 
    ( '.$in_word.' and '.$fraction.'/'.$divisionBy.' )</strong> Only.'; 
  ?>
</p>

<p style="padding: 0px 20px 10px; text-align:justify">
    Please note that an additional amount may be deducted per month as per the employment agreement towards pension contribution,
    work insurance or any other deductions which subject to change.

</p>
<p style="padding: 0px 20px 10px; text-align:justify">
<?php
    echo 'We confirm that we will transfer the salary on 1st of each month commencing from '.date('Y').'. In case of leaves our
     services, we undertake to transfer '.$his_her.' final dues, if any payable, subject to provisions of labor law, net of deductions
     due to '.$him_her.' to the above-mentioned account.'; 
  ?>
</p>

<div style="padding: 10px 20px 10px"> This certificate is issued at the request of employee and does not constitute any 
obligation on the part of the company. Other than transferring the salary and final dues, if any payable. </div>


<div style="padding: 30px 20px">
    <table style="width: 100%">
        <tr>
            <td>Yours faithfully, </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" style="height: 80px">&nbsp;</td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: bottom"><?= current_companyName(true) ?></td>
            <td></td>
            <?php if(!empty($repData['issuedByName'])){ ?>
                <td style="vertical-align: bottom; text-align: right">  </td>
            <?php } ?>
        </tr>
    </table>
</div>
