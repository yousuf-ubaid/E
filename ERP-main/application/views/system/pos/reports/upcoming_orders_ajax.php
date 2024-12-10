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

?>


<div style=" ">
   <div style="text-align: center; font-size: 17px; font-weight: bold">  <?php echo $masters['wareHouseDescription'] ?> </div>
   <div style="font-size: 17px; font-weight: bold"> Customer : <?php echo $masters['pos_customerName'].' - '.$masters['customerTelephone'] ?> </div>
    <div style="font-size: 17px; font-weight: bold"> Delivery Date : <?php echo $masters['deliveryDate'].' - Time : '.  date('h:i:s A', strtotime($masters['deliveryTime'])) ?> </div>
    <?php if($masters['deliveryType'] == 'Delivery'){ ?>
    <div style="font-size: 12px; font-weight: bold"> Customer Address : <?php echo $masters['CustomerAddress1'] ?> </div>
    <div style="font-size: 12px; font-weight: bold"> Landmark : <?php echo $masters['landMarkLocation'] ?> </div>
    <?php } ?>

<table style="width: 100%; font-size:14px !important; margin-bottom: 1px;">
    <tr>
        <td style="width:25%; text-align: left;"> Dispatch Type : </td>
        <td style="width:30%"> <?php echo $masters['deliveryType'] ?>   </td>
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
    } ?>
</table>

<div class="vLineKOT" style="border-bottom: 1px dotted; height: 1px; margin: 1px; width: 500px"></div>
<table style="width: 100%; font-size:14px !important;" border="0">
    <?php
    $templateID = get_pos_templateID();
    $i = 1;

    if (!empty($invoiceList)) {
        $i = 1;
        ?>
        <tr>
            <td><strong>#</strong></td>
            <td><strong> Item</strong></td>
            <td style="text-align: left"><strong>Qty</strong></td>
        </tr>
        <?php
        foreach ($invoiceList as $item) { ?>

            <tr>
                <td style="vertical-align: top;">
                    <?php echo $i;
                    $i++; ?>
                </td>
                <td align="left">
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
                <td class="text-left">
                    <?php echo $item['qty']; ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>
</div>

<?php
