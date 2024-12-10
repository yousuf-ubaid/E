<div style="padding: 0px 20px">
    <strong>Date :</strong> <?php echo date('dS F Y', strtotime($repData['request_date'])); ?>
</div>

<div style="padding: 20px 20px 0px">
    The Manager <br/>
    <?php $repData['address_to']?><br/>
    <?php //echo $repData['bankName'] ?><br/>
    <?php //echo $repData['bankCountry'] ?>
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
?>

<p style="padding-top: 30px; padding-left: 20px;"> <?php echo 'Dear Sir/Madam <br/><br/>'; ?> </p>

<p style="padding: 0px 20px 0px; text-align:justify">
    <?php
    echo 'This  is  to  certify  that <strong>'.$repData['empTitle'].' '.$repData['Ename2'].'</strong>,  holder  of  Passport  /I.D.No. <strong>'.$repData['identity_no'].'</strong>,
    is  an  employee  of <strong>'.current_companyName(true).'</strong> the  capacity  of <strong>'.$repData['DesDescription'].'</strong> since 
    <strong>'.date('dS F Y', strtotime($repData['EDOJ'])).'</strong>. '.$his_her.'  employment  number  is <strong>'.$repData['ECode'].'</strong>,  
    '.$he_she.'  is  drawing  a  monthly  gross salary of <strong>'.$currency.'. '.number_format($total, $decimal).' ( '.$in_word.' and '.$fraction.'/'.$divisionBy.' Only).</strong>';
    ?>
</p>

<p style="padding: 0px 20px 10px; text-align:justify">
    <?php
    echo 'At the request of above mentioned employee, we hereby confirm and undertake to transfer '.$his_her.'
          monthly salary less our deduction, if any, to '.$his_her.' account no. <strong>'.$repData['accountNo'].'</strong> with your bank.
          The arrangement will not be altered till such time '.$he_she.' is to be employed with us or '.$he_she.' produces a no
          objection certificate from your bank in writing whichever is earlier';
    ?>
</p>


<div style="padding: 10px 20px 10px"> This certificate is issued at <?php echo $his_her; ?> request without any responsibility and liability on our part. </div>


<div style="padding: 0px 20px">
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
            <td style="font-weight: bold; vertical-align: bottom"><?=$signature['sigName'].' <br/>'.$signature['sigDesignation'] ?></td>
            <td></td>
            <?php if(!empty($repData['issuedByName'])){ ?>
                <td style="vertical-align: bottom; text-align: right"> Issued by:  <?php echo $repData['issuedByName']; ?> </td>
            <?php } ?>
        </tr>
    </table>
</div>
