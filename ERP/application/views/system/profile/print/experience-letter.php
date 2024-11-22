<div style="padding: 0px 20px">
    <strong>Date :</strong> <?php echo date('dS F Y'); ?>
</div>

<div style="text-align: center">
    <h3 style="margin-bottom: 5px !important; font-weight: bold;"><u>To whom it may concern</u></h3>
</div>


<p style="padding: 50px 20px 10px; text-align:justify">
    <?php
    echo 'We hereby certify that <strong>'.$repData['empTitle'].'.'.$repData['Ename2'].', Employee No.'.$repData['ECode'].'</strong>,  
    '.$repData['identityDoc'].' <strong>'.$repData['identity_no'].'</strong>,
    has been employed with us from '.date('dS F Y', strtotime($repData['EDOJ'])).' till date as <strong>'.$repData['DesDescription'].'</strong>.';
    ?>
</p>

<div style="padding: 0px 20px 10px; text-align:justify">
    This certificate is issued at the request of the above mentioned employee and
    does not constitute a financial guarantee or any other obligation of our part.
</div>


<div style="padding: 80px 20px">
    <table style="width: 100%">
        <tr>
            <td>Yours faithfully, <br/><strong><?=current_companyName(true); ?></strong></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="3" style="height: 80px">&nbsp;</td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: bottom"><?php echo $signature['sigName'].' <br/>'.$signature['sigDesignation'] ?></td>
            <td colspan="2"></td>
        </tr>
    </table>
</div>
