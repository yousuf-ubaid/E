<div>
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
    //print_r($masters)

    ?>

    <style>
        @media screen {
            #printSection {
                display: none;
            }
        }

        @media print {
            body * {
                visibility:hidden;
            }
            #printSection, #printSection * {
                visibility:visible;
            }
            #printSection {
                position:absolute;
                left:0;
                top:0;
            }
        }
    </style>

    <script>
        function printKOTElement(elem, append, delimiter) {
            var domClone = elem.cloneNode(true);

            var $printSection = document.getElementById("printSection");

            if (!$printSection) {
                var $printSection = document.createElement("div");
                $printSection.id = "printSection";
                document.body.appendChild($printSection);
            }

            if (append !== true) {
                $printSection.innerHTML = "";
            }

            else if (append === true) {
                if (typeof(delimiter) === "string") {
                    $printSection.innerHTML += delimiter;
                }
                else if (typeof(delimiter) === "object") {
                    $printSection.appendChlid(delimiter);
                }
            }

            $printSection.appendChild(domClone);
        }

        function print_KOTpaymentReceipt() {
            printKOTElement(document.getElementById("print_content<?php echo $uniqueID; ?>"));
            window.print();
        }
    </script>

    <div id="print_content<?php echo $uniqueID; ?>">


        <table style="width: 100%">
            <tr>
                <td style="width:25%; text-align: left;">
                    <?php echo $this->lang->line('posr_ord_type') . ':'; ?><!--Ord.Type-->
                </td>
                <td style="width:30%"> <?php echo $masters['customerDescription'] ?>   </td>
                <td style="width:20%; "><?php echo $this->lang->line('posr_inv_no') . ':'; ?><!--Inv. No--> </td>
                <td style="width:25%;"
                    class="ar"><?php echo get_pos_invoice_code($masters['menuSalesID'], $masters['wareHouseAutoID']) ?> </td>
            </tr>
            <tr>
                <td style="text-align: left;"><?php echo $this->lang->line('common_date') . ':'; ?><!--Date--> </td>
                <td> <?php echo date('d/m/Y', strtotime($masters['createdDateTime'])) ?></td>
                <td><?php echo $this->lang->line('common_time'); ?><!--Time-->:</td>
                <td class="ar"><?php echo date('g:i A', strtotime($masters['createdDateTime'])) ?></td>
            </tr>
            <?php if (isset($masters['deliveryOrderID']) && $masters['deliveryOrderID']) { ?>
                <tr>
                    <td style="text-align: left;">Delivery Date</td>
                    <td> <?php echo !empty($masters['deliveryDate']) ? date('d/m/Y', strtotime($masters['deliveryDate'])) : '-'; ?></td>
                    <td>Delivery Time</td>
                    <td class="ar"><?php echo !empty($masters['deliveryTime']) ? date('g:i A', strtotime($masters['deliveryTime'])) : '-'; ?></td>
                </tr>
            <?php } ?>
            <?php if (isset($masters['cusname']) || $masters['custel']) { ?>
                <tr>
                    <td style="text-align: left;">Customer</td>
                    <td> <?php echo !empty($masters['cusname']) ? $masters['cusname'] : '-'; ?></td>
                    <td>Contact No</td>
                    <td class="ar"><?php echo !empty($masters['custel']) ? $masters['custel'] : '-'; ?></td>
                </tr>
            <?php } ?>

            <?php if (!empty($masters['diningTableDescription'])) {
                ?>
                <tr>
                    <td style="text-align: left;">Table</td>
                    <td> <?php echo $masters['diningTableDescription']; ?></td>
                    <td>Packs</td>
                    <td class="ar"><?php echo $masters['numberOfPacks'] > 0 ? $masters['numberOfPacks'] : ''; ?></td>
                </tr>

                <tr>
                    <td style="text-align: left;">waiter</td>
                    <td> <?php echo $masters['crewLastName']; ?></td>
                    <td>&nbsp;</td>
                    <td class="ar">&nbsp;</td>
                </tr>
                <?php
            } ?>
            <?php if (!empty($masters['holdRemarks'])) {
                ?>
                <tr>
                    <td>Remarks</td>
                    <td colspan="3"><?php echo $masters['holdRemarks']; ?></td>
                </tr>

                <?php
            } ?>
        </table>


        <div style="clear:both;"></div>

        <div class="vLineKOT">&nbsp;</div>
        <table style="width: 100%;" border="0">

            <tr>
                <td>#</td>
                <td>Item</td>
                <td>Qty</td>
                <!--<th>Kitchen</th>-->
            </tr>
            <tr>
                <td colspan="3">
                    <div class="vLineKOT">&nbsp;</div>
                </td>
            </tr>

            <?php
            $templateID = get_pos_templateID();
            $i = 1;

            if (!empty($invoiceList)) {
                $i = 1;
                $kotID = 0;
                foreach ($invoiceList as $item) {
                    $comboSub=get_pos_combos($item['menuSalesID'],$item['menuSalesItemID'],$item['warehouseMenuID']);
                    if ($kotID == 0 || $kotID != $item['kotID']) {
                        ?>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="kitchenHeader"><?php echo $item['kitchenName'] ?></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div class="vLineKOT">&nbsp;</div>
                            </td>
                        </tr>
                        <?php
                    }
                    $kotID = $item['kotID'];
                    ?>
                    <tr>
                        <td style="vertical-align: top;"><?php echo $i;
                            $i++; ?></td>
                        <td align="left">

                            <?php
                            echo $item['menuMasterDescription'];
                            if (!empty($item['kitchenNote'])) {
                                echo '<br/><strong>&nbsp;&nbsp;' . $item['kitchenNote'] . '</strong>';
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
                        <td class="text-center" style="vertical-align: top;">
                            <?php echo $item['qty']; ?>
                        </td>
                        <!--<td><?php /*echo $item['KOT_description'] */ ?></td>-->
                    </tr>
                    <?php
                    if(!empty($comboSub)){
                        foreach($comboSub as $cmbo){
                            ?>
                            <tr style="margin-left: 2px;">
                                <td style="vertical-align: top;">&nbsp;</td>
                                <td align="left">* <?php echo $cmbo['menuMasterDescription'] ?></td>
                                <td class="text-center" style="vertical-align: top;"> <?php echo $cmbo['qty'] ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    <tr>
                        <td>
                            <div style="margin: 2px; height: 0px; ">&nbsp;</div>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>

        </table>
    </div>

    <?php if ($print) { ?>
        <div class="vLineKOT">&nbsp;</div>

        <div id="bkpos_wrp">
            <button type="button" onclick="javascript:print_KOTpaymentReceipt()"
                    style="width:101%; cursor:pointer; font-size:12px; background-color:#FFA93C; color:#000; text-align: center; border:1px solid #FFA93C; padding: 10px 0px; font-weight:bold;">
                <i class="fa fa-print"></i> <?php echo $this->lang->line('common_print'); ?><!--Print-->
            </button>
        </div>
    <?php }
    if ($newBill) {
        ?>
        <button type="button" class="btn btn-primary btn-lg" onclick="holdAndCreateNewBill()"
                style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center;  10px 1px; margin: 5px auto 10px auto; font-weight:bold; border-radius: 0px;">
            Hold & Create New Order
        </button>
        <?php
    }
    ?>
</div>