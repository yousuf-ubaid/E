<html>
    <?php 
        $isGroupBasedPolicy =  $group_based_tax = getPolicyValues('GBT', 'All');
        $invMaster = $invData[1];
        $invItems = $invData[2];
        $dPlace = $invMaster['transactionCurrencyDecimalPlaces'];
        if($invMaster['returnMode'] ==1){
            $returnMode = 'Exchange';
        } else if ($invMaster['returnMode'] ==2) {
            $returnMode = 'Refund';  
        };
    ?>
<head>
    <title><?php echo $returnMode; ?> Note</title>
    <style type="text/css">
        #itemTable th{
            border: 1px dashed #000;
            border-left: none;
            border-right: none;
            text-align: right !important;
            font-size: 13px;
        }

        #itemTable td{ font-size: 18px; }

        #itemBreak{ border-top: 1px dashed #000; }

        #headerTB td{ font-size: 18px}

        /*.lastTD{
            -webkit-transform:scale(1,2); !* Safari and Chrome *!
            -moz-transform:scale(1,2); !* Firefox *!
            -ms-transform:scale(1,2); !* IE 9 *!
            -o-transform:scale(1,2); !* Opera *!
            transform:scale(1,2); !* W3C *!
        }*/

        #thanking-div{
            border: 1px dashed #000;
            border-left: none;
            border-right: none;
            padding: 10px;
            font-size: 12px;
            font-weight: bolder;
        }
    </style>
</head>

<body onload="/*window.print()*/">

<?php

echo '<table width="370px" border="0">
            <tr><td align="center" style="font-size: 20px !important; font-weight: 600">'.$wHouse['wareHouseDescription'].'</td></tr>
            <tr><td align="center" style="font-size: 14px !important">'.$wHouse['warehouseAddress'].'</td></tr>
            <tr><td align="center" style="font-size: 14px !important">'.$wHouse['warehouseTel'].'</td> </tr>
            <tr><td align="center" style="font-size: 20px; font-weight: 600">'.$returnMode.' Note</td></tr>
            <tr><td align="center" style="font-size: 12px; font-weight: 600">Invoice No : '.$invMaster['invCode'].'</td></tr>
     </table>';

echo '<table id="headerTB" width="370px" border="0">
            <tr>
                <td width="50px">Date</td>
                <td>:</td>
                <td width="100px">'.date( "Y-m-d", strtotime($invMaster['createdDateTime']) ).'</td>
                <td width="50px"></td>
                <td width="50px">Operator</td>
                <td>:</td>
                <td width="100px">'.$invMaster['repName'].'</td>
            </tr>
            <tr>
                <td>Code</td>
                <td>:</td>
                <td>'.$invMaster['documentSystemCode'].'</td>
                <td></td>
                <td>Unit</td>
                <td>:</td>
                <td>'.count($invItems).'</td>
            </tr>
          </table>';
?>

<table id="itemTable" width="370px" border="0">
    <tr>
        <th style="text-align: left !important;">#</th>
        <th style="width: 160px; text-align: left !important;">Item</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Discount</th>
        <?php if($isGroupBasedPolicy == 1){?>
            <th>VAT</th>
            <?php if($isOtherTaxExist > 0) {?>
                <th>Oth Tax</th>
            <?php }?>
        <?php }?>
        <th>Amount</th>
    </tr>

    <?php
    $totalTaxAmount = 0;
    $VATTaxAmount = 0;
    $otherTax = 0;
    foreach($invItems as $key=>$item){
        $totalTaxAmount += ($item['taxAmount'] - $item['amount']);
        $VATTaxAmount += ($item['amount']);
        $discount=($item['price']*$item['discountPer'])/100;
        echo '<tr>
        <td>'.($key+1).'</td>
        <td colspan="2">'.$item['itemSystemCode'].'&nbsp;&nbsp;'.$item['itemDescription'].'</td>
        <td></td>
      </tr>
      <tr>
        <td colspan="2"></td>
         <td align="right">'.$item['qty'].'</td>
        <td align="right">'.number_format($item['price'], $dPlace).'</td>
        <td align="center">'.$discount.'</td>';
        if($isGroupBasedPolicy ==1){
            echo '<td align="right">'.number_format($item['amount'],$dPlace).'</td>';
            if($isOtherTaxExist > 0) {
                $otherTax = $item['taxAmount']-$item['amount'];
                echo '<td align="right">' .number_format($otherTax, $dPlace) . '</td>';
            }
        }
        echo '<td align="right">'.number_format(($item['price']+$item['amount']+$otherTax)-$discount, $dPlace).'</td>
      </tr>';
    }

    if($isOtherTaxExist>0){
        $seperator=8;
    }else{
        $seperator=7;
    }
    ?>
    <tr>
        <td colspan="<?php echo $seperator; ?>" id="itemBreak">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="6" class="lastTD">SUB TOTAL</td>
        <td class="lastTD" align="right"><?php echo number_format($invMaster['subTotal'], $dPlace) ?></td>
    </tr>
    <tr>
        <td colspan="6" class="lastTD">DISCOUNT</td>
        <td align="right" class="lastTD" style="/*border-bottom: 1px solid #000;*/"><?php echo number_format($invMaster['generalDiscountAmount'], $dPlace); ?></td>
    </tr>
    <?php if($isGroupBasedPolicy == 1){?>
        <tr>
            <td colspan="6" class="lastTD">VAT</td>
            <td class="lastTD" align="right"><?php echo number_format($VATTaxAmount, $dPlace) ?></td>
        </tr>
        <?php if($isOtherTaxExist > 0) { ?>
            <tr>
                <td colspan="6" class="lastTD">Oth Tax</td>
                <td class="lastTD" align="right"><?php echo number_format($totalTaxAmount, $dPlace) ?></td>
            </tr>
        <?php  }?>
    <?php }?>
    <tr>
        <td colspan="6" class="lastTD">NET TOTAL</td>
        <td align="right" class="lastTD"><?php echo number_format($invMaster['netTotal'], $dPlace) ?></td>
    </tr>
</table>


</body>
</html>


<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-11-15
 * Time: 12:35 PM
 */