<?php
$d = $this->common_data['company_data']['company_default_decimal'];
$netTotal = 0;
?>

<div id="printContainer_itemizedSalesReport">
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
    <div style="margin:4px 0; text-align: center;">
        <?php
        if (isset($cashier) && !empty($cashier)) {
            echo 'Cashier: ';
            $tmpArray = array();
            foreach ($cashier as $c) {
                $tmpArray[] = $cashierTmp[$c];
            }
            echo join(', ', $tmpArray);
        }
        ?>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        	<span>
         Date : <strong>
            <?php
            $filterFrom = $this->input->post('filterFrom');
            $filterTo = $this->input->post('filterTo');
            if (!empty($filterFrom) && !empty($filterTo)) {
                echo '  <i>from : </i>' . $filterFrom . ' - <i> To: </i>' . $filterTo;
            } else {
                $curDate = date('d-m-Y');
                echo $curDate . ' (Today)';
            }
            ?>
        </strong>
        </div>
    </div>
    <br>
    <table class="<?php //echo table_class_pos() ?>" style="width: 100%; " border="1">
        <thead>
        <tr>
            <th class="al"> Category</th>
            <th class="al"> Sub Category</th>
            <th class="al"> Sub Sub Category</th>
            <th class="al"> Invoice Code</th>
            <th class="al"> Item Code</th>
            <th class="al"> Secondary Code</th>
            <th class="al"> Description</th>
            <th class="al"> Barcode</th>
            <th style="text-align: center;"> Qty</th>
            <th > Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $grandTotal = 0;

        if (!empty($itemizedSalesReport)) {
            foreach ($itemizedSalesReport as $val) {

                $qty=0;
                $price=0;

                if($val['qty']>0){
                    $qty=$val['qty'];
                }else{
                    $qty=$val['qty'];
                }

                if($val['price']>0){
                    $price=$val['price'];
                }else{
                    $price=$val['price'];
                }
                if($price>0){
                    ?>
                    <tr>
                        <td class="al"><?php echo $val['mainCategory'] ?></td>
                        <td class="al"><?php echo $val['subCategory'] ?></td>
                        <td class="al"><?php echo $val['subsubCategory'] ?></td>
                        <td class="al"><?php echo $val['invoiceCode'] ?></td>
                        <td class="al"><?php echo $val['itemSystemCode'] ?></td>
                        <td class="al"><?php echo $val['seconeryItemCode'] ?></td>
                        <td class="al"><?php echo $val['itemDescription'] ?></td>
                        <td class="al"><?php echo $val['barcode'] ?></td>
                        <td style="text-align: center;"><?php echo $qty ?></td>
                        <td class="ar" style="text-align: right;"><?php echo number_format($price, $d); ?></td>
                    </tr>
                    <?php
                    $grandTotal += $price;
                }
            }
        }else{
            ?>
            <tr>
                <td colspan="9" style="text-align: center;">No Records Found</td>
            </tr>

            <?php
        }

        ?>
        </tbody>
        <tfoot>
        <tr>
            <th  style="font-size:12px !important;" colspan="9"> Grand Total </th>
            <th style="font-size:12px !important;text-align: right"><?php echo number_format($grandTotal, $d); ?></th>
        </tr>
        </tfoot>
    </table>
    <br>
    <hr>

    <div style="margin-top:20px; border:1px solid #a3a3a3; padding:5px;">
        <div style="font-weight: bold;">Refund Details</div>
            <table class="table table-bordered table-condensed table-striped customTbl">
                <thead>
                    <tr>
                        <th class="al"> Category</th>
                        <th class="al"> Sub Category</th>
                        <th class="al"> Sub Sub Category</th>
                        <th class="al"> Invoice Code</th>
                        <th class="al"> Secondary Code</th>
                        <th class="al"> Item Code</th>
                        <th class="al"> Description</th>
                        <th class="al"> Barcode</th>
                        <th style="text-align: center;"> Qty</th>
                        <th > Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $tot_ref_amount = 0;
                    $tot_amnt = 0;
                    if(!empty($refund)){
                        foreach($refund as $res){
                            if($res['returnMode'] == 2 && $res['returnprice'] > 0){
                                $tot_amnt += $res['returnprice'];
                            }
                            if($res['returnprice'] > 0){ ?>
                                <tr>
                                    <td class="al"><?php echo $res['mainCategory'] ?></td>
                                    <td class="al"><?php echo $res['subCategory'] ?></td>
                                    <td class="al"><?php echo $res['subsubCategory'] ?></td>
                                    <td class="al"><?php echo $res['invoiceCode'] ?></td>
                                    <td class="al"><?php echo $res['seconeryItemCode'] ?></td>
                                    <td class="al"><?php echo $res['itemSystemCode'] ?></td>
                                    <td class="al"><?php echo $res['itemDescription'] ?></td>
                                    <td class="al"><?php echo $res['barcode'] ?></td>
                                    <td class="al"><?php echo $res['returnqty'] ?></td>
                                    <td style="text-align: right;"><?php echo number_format($res['returnprice'], $d); ?></td>
                                </tr>
                            <?php
                                $tot_ref_amount += $res['returnprice'];
                            }
                        }
                    }else{
                        ?>
                            <tr>
                                <td colspan="10" style="text-align: center;">No Records Found</td>
                            </tr>
                        <?php
                    }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9"></td>
                        <td class="ar"> <?php echo number_format($tot_ref_amount, $d) ?></td>
                    </tr>
                </tfoot>
            </table>
            <div style="font-weight: bold;">Total Cash Collection (Cash-Refund): <?php echo number_format($cash_collection-$tot_amnt, $d); ?></div>
        </div>
    </div>
    <hr>
    <div style="margin:4px 0px">
        <h6><strong>Printed by :</strong> <?php echo current_user() ?></h6>
    </div>
    <div style="margin:4px 0px">
        <h6><strong>Printed Date :</strong> <?php echo date('d/m/Y'); ?> <?php echo date("h:i A"); ?></h6>
    </div>
</div>
