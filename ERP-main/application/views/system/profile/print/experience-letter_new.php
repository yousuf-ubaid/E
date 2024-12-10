<?php
$he_she = ($repData['gender'] == 2)? 'she': 'he';
$cap_He_She = ($repData['gender'] == 2)? 'He': 'She';
$his_her = ($repData['gender'] == 2)? 'her': 'his';
$Mr_Ms = ($repData['gender'] == 2)? 'Ms': 'his';

?>
<div style="padding: 0px 20px">
    <strong>Date :</strong> <?php echo date('d/m/Y'); ?><br/>
    <strong>Mr/Ms :</strong> <?php  echo $repData['address_to']; ?><br/>
    <strong>REF :</strong> <?php echo $repData['documentCode']; ?>
    
</div>

<div style="text-align: center">
    <h3 style="margin-bottom: 5px !important; font-weight: bold;"><u>RE: Experience Letter</u></h3>
</div>


<p style="padding: 50px 20px 10px; text-align:justify">
    <?php
    echo 'This is to certify that  <strong>'.$repData['empTitle'].'.'.$repData['Ename2'].'</strong> holding   
    '.$repData['identityDoc'].' <strong>'.$repData['identity_no'].'</strong>, '.$he_she.' worked for <strong>'.current_companyName(true).'</strong> , 
    as  <strong>'.$repData['DesDescription'].'</strong> from '.date('d/m/Y', strtotime($repData['EDOJ'])).' till '.date('d/m/Y').'. '
    ;
    ?>
</p>
<div style="padding: 0px 20px 10px; text-align:justify">
    <?php 
    echo 'During '.$his_her.' service, '.$he_she.' has shown great interest in the work and accomplished all required tasks with enthusiasm.'
    ?>    
</div>
<div style="padding: 0px 20px 10px; text-align:justify">
    We wish all the best at <?php echo $his_her?> endeavors and great success in the next opportunity.
</div>


<div style="padding: 80px 20px">
    <table style="width: 100%">
        <tr>
            <td></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="3" style="height: 80px">&nbsp;</td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: bottom">Human Resource Department </td>
            <td colspan="2"></td>
        </tr>
    </table>
</div>
