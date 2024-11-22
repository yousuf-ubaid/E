<?php
$paymentTypes = get_bill_payment_types($masters['menuSalesID']);
$tmpPayTypes = '';
if (!empty($paymentTypes)) {

    foreach ($paymentTypes as $paymentType) {
        $tmpPayTypes .= $paymentType['description'] . ', ';
    }

    $tmpPayTypes = '(' . rtrim($tmpPayTypes, ', ') . ')';

}

$data['paymentTypes'] = '';

$companyInfo = get_companyInfo();
$outletInfo = get_outletInfo();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$uniqueID = time();
//var_dump($invoiceList)
$deliveryInfo = get_deliveryConfirmedOrder($masters['menuSalesID']);
?>

<table style="width: 100%; font-size:14px !important; margin-bottom: 1px;">
    <tr>
        <td style="width:25%; text-align: left;">
            <?php echo $this->lang->line('posr_ord_type') . ':'; ?>
        </td>
        <td style="width:30%"> <?php echo $masters['customerDescription'] ?>   </td>
        <td style="width:20%; "><?php echo $this->lang->line('posr_inv_no') . ':'; ?> </td>
        <td style="width:25%;"
            style="text-align: left;"><?php echo get_pos_invoice_code($masters['menuSalesID'], $masters['wareHouseAutoID']) ?> </td>
    </tr>
    <tr>
        <td style="text-align: left;"><?php echo $this->lang->line('common_date') . ':'; ?></td>
        <td> <?php echo date('d/m/Y', strtotime($masters['createdDateTime'])) ?></td>
        <td><?php echo $this->lang->line('common_time'); ?> :</td>
        <td style="text-align: left;"><?php echo date('g:i A', strtotime($masters['createdDateTime'])) ?></td>
    </tr>
    <?php if (isset($masters['deliveryOrderID']) && $masters['deliveryOrderID']) { ?>
        <tr>
            <td style="text-align: left;">Delivery Date</td>
            <td> <?php echo !empty($masters['deliveryDate']) ? date('d/m/Y', strtotime($masters['deliveryDate'])) : '-'; ?></td>
            <td>Delivery Time</td>
            <td style="text-align: left;"><?php echo !empty($masters['deliveryTime']) ? date('g:i A', strtotime($masters['deliveryTime'])) : '-'; ?></td>
        </tr>
    <?php } ?>

    <tr>
        <td style="width:20%;" class="al">Name</td>
        <?php
        $menusalescust='';
        if($masters['isCreditSales']==1){
            $menusalescust=get_credit_salesCustomers($masters['menuSalesID']);
        }

        if(!empty($deliveryInfo)){
            ?>
            <td style="width:30%;" class="al"><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerName'] : '-'; ?></td>
            <?php
        }elseif(!empty($masters['cusname'])){
            ?>
            <td style="width:30%;" class="al"><?php echo $masters['cusname'] ?></td>
            <?php
        }elseif(!empty($menusalescust)){
            ?>
            <td style="width:30%;" class="al"><?php echo $menusalescust['CustomerName'] ?></td>
            <?php
        }else{
            ?>
            <td style="width:30%;" class="al">-</td>
            <?php
        }
        ?>
        <td class="al">Address</td>
        <?php
        if(!empty($deliveryInfo)){
            ?>
            <td class="al"><?php echo !empty($deliveryInfo) ? $deliveryInfo['CustomerAddress1'] : '-'; ?></td>
            <?php
        }elseif(!empty($masters['cusaddress'])){
            ?>
            <td class="al"><?php echo $masters['cusaddress']; ?></td>
            <?php
        }elseif(!empty($menusalescust)){
            ?>
            <td style="width:30%;" class="al"><?php echo $menusalescust['CustomerAddress1'] ?></td>
            <?php
        }else{
            ?>
            <td class="al">-</td>
            <?php
        }
        ?>
    </tr>

    <tr>
        <td class="al">Mobile</td>
        <?php
        if(!empty($deliveryInfo)){
            ?>
            <td class="al"><?php echo !empty($deliveryInfo) ? $deliveryInfo['phoneNo'] : '-'; ?></td>
            <?php
        }elseif(!empty($masters['custel'])){
            ?>
            <td class="al"><?php echo $masters['custel']; ?></td>
            <?php
        }elseif(!empty($menusalescust)){
            ?>
            <td style="width:30%;" class="al"><?php echo $menusalescust['customerTelephone'] ?></td>
            <?php
        }else{
            ?>
            <td class="al">-</td>
            <?php
        }
        ?>
    </tr>

    <?php if (!empty($masters['diningTableDescription'])) {
        ?>
        <tr>
            <td style="text-align: left;">Table</td>
            <td> <?php echo $masters['diningTableDescription']; ?></td>
            <td>Packs</td>
            <td class="al"><?php echo $masters['numberOfPacks'] > 0 ? $masters['numberOfPacks'] : ''; ?></td>
        </tr>

        <tr>
            <td style="text-align: left;">waiter</td>
            <td> <?php echo $masters['crewLastName']; ?></td>
            <td>&nbsp;</td>
            <td class="ar">&nbsp;</td>
        </tr>
        <?php
    } else {
        if (!empty($waiterName)) { ?>
            <tr>
                <td><?php echo $this->lang->line('posr_waiter'); ?></td>
                <td colspan="3"><?php echo $waiterName; ?></td>
            </tr>
        <?php }
    } ?>
</table>

<div class="vLineKOT" style="border-bottom: 1px dotted; height: 1px; margin: 1px;"></div>
<table style="width: 100%; font-size:14px !important;" border="0">


    <?php
    $templateID = get_pos_templateID();
    $i = 1;

    if (!empty($invoiceList)) {
        $i = 1;
        ?>
        <tr>

            <td><strong> Item</strong></td>
            <td style="text-align: left"><strong>Qty</strong></td>

        </tr>
        <?php
        foreach ($invoiceList as $item) {
            $comboSub=get_pos_combos($item['menuSalesID'],$item['menuSalesItemID'],$item['warehouseMenuID']);
            ?>

            <tr>

                <td align="left" style="font-size: 26px">
                    <?php

                    echo $item['menuMasterDescription'];
                    if (!empty($item['kitchenNote'])) {
                        echo '<br/><strong>' . $item['kitchenNote'] . '</strong>';
                    }

                    $menuSalesItemID = $item['menuSalesItemID'];
                    $output = get_add_on_byItem($menuSalesItemID);
                    if (!empty($output)) {
                        foreach ($output as $val) {
                            echo '<br/><strong>&nbsp; - ' . $val['menuMasterDescription'] . '</strong>';
                        }
                    }
                    ?>

                </td>
                <td class="text-left" style="font-size: 26px">
                    <?php echo $item['qty']; ?>
                </td>

            </tr>
            <?php
            if(!empty($comboSub)){
                foreach($comboSub as $cmbo){
                    ?>
                    <tr>

                        <td align="left" style="padding-left: 10px !important;">* <?php echo $cmbo['menuMasterDescription'] ?></td>
                        <td class="text-left"> <?php echo $cmbo['qty'] ?></td>
                    </tr>
                    <?php
                }
            }
            ?>

            <?php
        }
    }
    ?>

</table>




