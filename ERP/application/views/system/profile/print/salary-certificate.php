
<div style="font-size: 14px; font-family: Calibri">
    <div style="padding: 0px 20px">
        <strong>Date :</strong> <?php echo date('dS F Y', strtotime($repData['request_date'])); ?>
    </div>

    <div style="text-align: center">
        <h3 style="margin-bottom: 5px !important; font-weight: bold; font-size: 16px"><u>To whom it may concern</u></h3>
        <h3 style="margin-top: 0px !important; font-weight: bold; font-size: 16px"><u>Salary Certificate</u></h3>
    </div>

    <?php
    $salaryBreak = '';
    $total = 0;
    $decimal = 2;
    $currency = '';
    foreach ($salary_det as $val) {
        $decimal = ($val['transactionCurrency'] == 'OMR') ? 3: $decimal;
        $total += round($val['amount'], $decimal);
        $currency = $val['transactionCurrency'];

        $salaryBreak .= '<tr>';
        $salaryBreak .= '<td style="width: 150px; font-size: 14px; font-family: Calibri">'.$val['salaryDescription'].'</td>';
        $salaryBreak .= '<td style="text-align: right; width: 10px; font-size: 14px; font-family: Calibri">: </td>';
        $salaryBreak .= '<td style="text-align: right; width: 150px; font-size: 14px; font-family: Calibri">'.number_format($val['amount'], $decimal).' '.$val['transactionCurrency'].'</td>';
        $salaryBreak .= '</tr>';
    }


    $fraction = number_format(( $total  - (int)$total), $decimal);
    $fraction = ($currency == 'OMR')? ($fraction *1000): ($fraction *100);
    $in_word = $this->numbertowords->convert_number($total);
    $divisionBy = ($currency == 'OMR')? '1000': '100';

    $he_she = ($repData['gender'] == 2)? 'She': 'He';
    $his_her = ($repData['gender'] == 2)? 'her': 'his';
    ?>

    <p style="padding: 50px 20px; text-align:justify; font-size: 14px">
        <?php
        echo 'This  is  to  certify  that <strong>'.$repData['empTitle'].' '.$repData['Ename2'].'</strong>,  holder  of  Passport  /I.D.No. <strong>'.$repData['identity_no'].'</strong>,
    is  an  employee  of <strong>'.current_companyName(true).'</strong> the  capacity  of <strong>'.$repData['DesDescription'].'</strong> since 
    <strong>'.date('dS F Y', strtotime($repData['EDOJ'])).'</strong>. <span style="text-transform: capitalize">'.$his_her.'</span>  employment  number  is <strong>'.$repData['ECode'].'</strong>,  
    '.$he_she.'  is  drawing  a  monthly  gross salary of <strong>'.$currency.'. '.number_format($total, $decimal).' ( '.$in_word.' and '.$fraction.'/'.$divisionBy.' Only).</strong>';
        ?>
    </p>


    <div style="padding: 0px 20px; font-size: 14px; font-family: Calibri"> <strong>The salary break up as below</strong> </div>

    <div style="padding: 0px 30px">
        <table class="" style="font-size: 14px; font-family: Calibri" id="salary_certificate_table">
            <?php echo $salaryBreak; ?>
        </table>
    </div>

    <p style="padding: 10px 20px 10px"> This certificate is issued at <?php echo $his_her ?> request without any responsibility and liability on our part. </p>


    <div style="padding: 0px 15px">
        <table style="width: 100%; font-size: 14px; font-family: Calibri">
            <tr>
                <td>Yours faithfully, </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3" style="height: 80px">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: bottom"><?php echo $signature['sigName'].' <br/>'.$signature['sigDesignation'] ?></td>
                <td></td>
                <?php if(!empty($repData['issuedByName'])){ ?>
                    <td style="vertical-align: bottom; text-align: right"> Issued by:  <?php echo $repData['issuedByName']; ?> </td>
                <?php } ?>
            </tr>
        </table>
    </div>

</div>
