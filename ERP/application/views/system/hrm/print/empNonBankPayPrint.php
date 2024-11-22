
<div class="table-responsive">
    <table style="width: 100%" border="0px">
        <tbody>
        <tr>
            <td style="width:40%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:60%;" valign="top">
                <table border="0px">
                    <tr>
                        <td colspan="2">
                            <h2><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h2>
                        </td>
                    </tr>
                    <tr>
                        <td><h4 style="margin-bottom: 0px">Employee Direct Salary Payment</td>
                    </tr>
                    <tr>
                        <?php $date = $masterData['payrollYear']."-".$masterData['payrollMonth']."-01" ?>
                        <td colspan="2"> <h4 style="margin-bottom: 0px">Period - <?php echo  date('F ` Y',  strtotime($date )); ?></h4> </td>
                        <!--<td align="right"> <h4 style="margin-bottom: 0px"><?php /*echo  date('Y `F',  strtotime($date )); */?> </h4></td>-->
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>

<div class="table-responsive">
<table class="<?php echo table_class(); ?>" style="width: 100%;margin-top: 2%">
    <thead>
    <tr>
        <th class="theadtr" style="width: auto"> # </th>
        <th class="theadtr" style="width: 10%"> EMP ID </th>
        <th class="theadtr" style="width: 20%"> Name </th>
        <th class="theadtr" style="width: 10%"> Paid By </th>
        <th class="theadtr" style="width: 30%"> Bank Name </th>
        <th class="theadtr" style="width: 12%"> Cheque No </th>
        <th class="theadtr" style="width: 10%"> Currency </th>
        <th class="theadtr" style="width: 12%"> Amount </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $j = 0;  $n = 0; $totCurrency = 0;
    $lastCurrency = null;
    $lastpaymentvoucher = null;
    if( !empty($printData)){
        foreach($printData as $empBnk){
            $trCurrency = $empBnk['transactionCurrency'];
            $paidBy = ( $empBnk['payByBankID'] == null ) ? 'Cash' : 'Cheque';
            $bankName = ( $empBnk['payByBankID'] != null ) ? $empBnk['bankName'] : '-';
            $chequeNo = ( $empBnk['payByBankID'] != null ) ? $empBnk['chequeNo'] : '-';
            $paymentvoucher = $empBnk['payVoucherAutoId'];
           /* if($lastCurrency != $trCurrency){
                echo '<tr> <th class="theadtr" colspan="8">Currency : '.$trCurrency.'</th></tr>' ;
                $lastCurrency = $trCurrency;
                $n = 0; $totCurrency = 0;
            }*/
            if($lastpaymentvoucher != $paymentvoucher){

                echo '<tr> 
                    <th colspan="8" style="font-size: 12px">
                     '.$empBnk['PVcode'].'
         </th></tr>' ;
                $lastCurrency = $trCurrency;
                $lastpaymentvoucher = $paymentvoucher;
                $n = 0; $totCurrency = 0;
            }



            echo
                '<tr>
                        <td>' . ($j+1) . '</td>
                        <td>' . $empBnk['ECode'] . '</td>
                        <td>' . $empBnk['empName'] . '</td>
                        <td align="center">' . $paidBy . '</td>
                        <td>' . $bankName . '</td>
                        <td>' . $chequeNo . '</td>
                        <td align="center">' . $trCurrency . '</td>
                        <td align="right">' . number_format($empBnk['transactionAmount'], $empBnk['dPlace']) . '</td>
                    </tr>';
            $j++;
            $n++;
            $totCurrency += number_format($empBnk['transactionAmount'], $empBnk['dPlace'], '.', '');


            if( array_key_exists($j, $printData) ) {
                if( $printData[$j]['payVoucherAutoId'] != $lastpaymentvoucher && $n > 1) {
                    echo '<tr>
                             <th class="theadtr" colspan="7"><div align="right">Total</div></th>
                             <th class="theadtr" align="right">' . number_format($totCurrency, $empBnk['dPlace']) . '</th>
                         </tr>';

                }
            }
            else{
                if( $n > 1 ){
                    echo '<tr>
                             <th class="theadtr" colspan="7"><div align="right">Total</div></th>
                             <th class="theadtr" align="right">'.number_format($totCurrency, $empBnk['dPlace']).'</th>
                          </tr>';
                }
            }

        }
    }
    else{
        echo '<tr> <td colspan="8">No data available in table</td> </tr>';
    }
    ?>
    </tbody>
</table>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-16
 * Time: 3:42 PM
 */