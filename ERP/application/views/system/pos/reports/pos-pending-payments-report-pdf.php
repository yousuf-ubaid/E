
<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
$time = time();
?>
<div id="printContainer_<?php echo $time ?>">
    <table style="width: 100%">
        <tr>
            <td style="text-align: center"><h3 style="margin-top:2px;"><?php echo $companyInfo['company_name'] ?></h3></td>
        </tr>
        <tr>
            <td style="text-align: center">
                <h4 style="margin:0;"><?php echo $companyInfo['company_address1'] . ', ' . $companyInfo['company_city'] ?></h4>
            </td>
        </tr>
    </table>
    <br>
    <h3 class="text-center">Pending Payments Report</h3>

    <table style="width: 100%">
        <tr>
            <td style="width:10%;"><strong> Date :</strong></td>
            <td style=""><?php
                $filterFrom = $this->input->post('filterFrom');
                $filterTo = $this->input->post('filterTo');
                if (!empty($filterFrom) && !empty($filterTo)) {
                    echo '  From : ' . $filterFrom . ' -  To: ' . $filterTo.'';
                } else {
                    $curDate = date('d-m-Y');
                    echo $curDate . ' (Today)';
                }
                ?></td>
        </tr>
    </table>
    <br>
    <table class="" style="width: 100%; " border="1">
        <thead>
        <tr>
            <th class=""> #</th>
            <th class="">Bill No</th>
            <th class="">Type</th>
            <th class="">Customer Name</th>
            <th class="">Telephone Number</th>
            <th class="">Invoice Code</th>
            <th class="">Invoice Amount</th>
            <th class="">Received Amount</th>
            <th class="">Balance Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        $invTot = 0;
        $recptTot = 0;
        $balTot = 0;
        if (!empty($penPayReport)) {
            foreach ($penPayReport as $val) {
                if(number_format($val['transactionAmount']-$val['receiptamnts'], $d)>0){
                    if($val['invoiceCode']=='Delivery Order'){
                        $invoiceCode='-';
                        $type='Delivery Order';
                    }else{
                        $invoiceCode=$val['invoiceCode'];
                        $type='Credit Sales';
                    }
                    $i += 1;
                    ?>
                    <tr>
                        <td class="" style="text-align: center;"><?php echo $i ?></td>
                        <td class="" style="text-align: center;"><?php echo $val['billNo'] ?></td>
                        <td class="" style="text-align: center;"><?php echo $type ?></td>
                        <td class="" style="text-align: center;"><?php echo $val['customerDetal'] ?></td>
                        <td class="" style="text-align: center;"><?php echo $val['customerTelephone'] ?></td>
                        <td class="" style="text-align: center;"><?php echo $invoiceCode ?></td>
                        <td style="text-align: right;"><?php echo number_format($val['transactionAmount'], $d); ?></td>
                        <td style="text-align: right;"><?php echo number_format($val['receiptamnts'], $d); ?></td>
                        <td style="text-align: right;"><?php echo number_format($val['transactionAmount']-$val['receiptamnts'], $d); ?></td>
                    </tr>
        <?php
                    $invTot+=$val['transactionAmount'];
                    $recptTot+=$val['receiptamnts'];
                    $balTot+=$val['transactionAmount']-$val['receiptamnts'];
                }
            }
        } else {
            ?>
            <tr>
                <td class="" style="text-align: center;" colspan="9">Records not Found</td>
            </tr>
            <?php
        }
        ?>
        </tbody>
        <tfoot>
            <tr style="font-size:12px !important;" class="t-foot">
                <td colspan="6" style="padding-right:2px;font-weight: bold; text-align: right"><strong>Total</strong></td>
                <td  style="text-align: right; font-weight: bold;"><strong><?php echo number_format($invTot, $d); ?></strong></td>
                <td  style="text-align: right; font-weight: bold;"><strong><?php echo number_format($recptTot, $d); ?></strong></td>
                <td  style="text-align: right; font-weight: bold;"><strong><?php echo number_format($balTot, $d);  ?></strong></td>
            </tr>
        </tfoot>

    </table>
</div>