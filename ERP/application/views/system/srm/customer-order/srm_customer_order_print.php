<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo fetch_account_review(true,true,$approval && $extra['master']['approvedYN']); ?>
<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 75px"
                                 src="<?php echo $logo. $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name']?></strong>
                            </h3>
                            <p><?php echo $this->common_data['company_data']['company_address1'].', '.$this->common_data['company_data']['company_address2'].', '.$this->common_data['company_data']['company_city'].', '.$this->common_data['company_data']['company_country']; ?></p>
                            
                        </td>
                    </tr>
                    <!--
                    <tr>
                        <td><strong><?php //echo $this->lang->line('finance_common_journal_voucher_number');?><!--Journal Voucher Number--></strong></td>
                        <!--<td><strong>:</strong></td>
                        <td><?php //echo $extra['master']['JVcode']; ?></td>
                    </tr>-->
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
            <div class="table-responsive">
                <div style="text-align: center">
                    <h4>Customer Order</h4><!--Customer Order -->
                </div>
            </div>    
<div class="table-responsive"><br>
    <table>
        <tbody>
            <tr>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td style="width:100px"><strong>Customer Name</strong></td>
                            <td style="width:5%"><strong>:</strong></td>
                            <td style="width:60%;text-align:left">
                            <?php echo $extra['CustomerName']; ?>
                            
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="50%">
                    <table  width="100%">
                        <tr>
                            <td style="width:100px"><strong>Order ID</strong></td>
                            <td style="width:5%"><strong>:</strong></td>
                            <td style="width:60%;text-align:left">
                            <?php echo $extra['customerOrderCode']; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td style="width:100px"><strong>Customer Address</strong></td>
                            <td style="width:5%"><strong>:</strong></td>
                            <td style="width:60%;text-align:left">
                            <?php echo $extra['CustomerAddress']; ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td style="width:100px"><strong>Customer Ref No</strong></td>
                            <td style="width:5%"><strong>:</strong></td>
                            <td style="width:60%;text-align:left">
                            <?php echo $extra['referenceNumber']; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td style="width:100px"><strong>Supplier Name</strong></td>
                            <td style="width:5%"><strong>:</strong></td>
                            <td style="width:60%;text-align:left">
                            <?php echo $extra['supplierName']; ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td style="width:100px"><strong>Narration</strong></td>
                            <td style="width:5%"><strong>:</strong></td>
                            <td style="width:60%;text-align:left">
                            <?php echo $extra['narration']; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td style="width:100px"><strong>Document Date</strong></td>
                            <td style="width:5%"><strong>:</strong></td>
                            <td style="width:60%;text-align:left">
                            <?php echo $extra['documentDate']; ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td style="width:35%"><strong>Expiry Date</strong></td>
                            <td style="width:5%"><strong>:</strong></td>
                            <td style="width:60%;text-align:left">
                            <?php echo $extra['expiryDate']; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
        </tbody>
    </table>
</div>
<br>
<div class="table-responsive">
    <table id="add_new_grv_table" class="table table-bordered table-striped">
        <thead>
        
        <tr>
            <th class='theadtr' style="min-width: 5%">#</th>
            <th class='theadtr' style="min-width: 15%">Code</th>
            <th class='theadtr' style="min-width: 35%">Item Name</th>
            <th class='theadtr' style="min-width: 10%">Item Qty</th>
            <th class='theadtr' style="min-width: 15%">Unit Price (<?php echo $extra['CurrencyCode']; ?>)</th>
            <th class='theadtr' style="min-width: 20%">Total (<?php echo $extra['CurrencyCode']; ?>)</th>
        </tr>
        </thead>
        <tbody>
        <?php 
        $totalAmount=0;
        if (!empty($orderitem)) {
            for ($i = 0; $i < count($orderitem); $i++) {
                echo '<tr>';
                echo '<td style="height:30px">' . ($i + 1) . '</td>';
                echo '<td>' . $orderitem[$i]['itemSystemCode']. ' </td>';
                echo '<td>' . $orderitem[$i]['itemName']. ' </td>';
                echo '<td class="text-center">' . $orderitem[$i]['requestedQty']. ' </td>';
                echo '<td class="text-right">' . $orderitem[$i]['unitAmount']. ' </td>';
                echo '<td class="text-right">' . $orderitem[$i]['totalAmount']. ' </td>';
                //echo '<td class="text-right">' . format_number($extra['detail'][$i]['debitAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                /*echo '<td class="text-right">' . format_number($extra['detail'][$i]['creditAmount'], $extra['master']['transactionCurrencyDecimalPlaces']) . '</td>';
                if (1 == $extra['master']['confirmedYN'] && false === empty($extra['detail'][$i]['activityCodeID']))
                {
                    echo '<td class="text-right"><a onclick="allocateCost(' . $extra['detail'][$i]['JVDetailAutoID'] . ', ' . $extra['detail'][$i]['JVMasterAutoId'] . ', \'JV\', ' . $extra['detail'][$i]['activityCodeID'] . ');"><span class="glyphicon glyphicon-cog"></span></a></td>';
                }*/
                echo '</tr>';
                $totalAmount += ($orderitem[$i]['totalAmount']);
            }
        } else {
            $norecfound=$this->lang->line('common_no_records_found');
            echo '<tr class="danger"><td colspan="6" class="text-center"><b>'.$norecfound.'<!--No Records Found--></b></td></tr>';
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="5"><?php echo $this->lang->line('common_total');?><!--Total--> (<?php echo $extra['CurrencyCode']; ?>
                    )
                </td>
                <td class="text-right total"><?php echo format_number($totalAmount); ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<br>
<br>
<div class="table-responsive">
    <br>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="width:30%;"><b>
                        <?php echo $this->lang->line('common_created_by'); ?><!-- Created By --> </b></td>
                <td style="width:2%;"><strong>:</strong></td>
                <?php 
                if (!empty($orderitem)) { ?>
                <td style="width:70%;"><?php echo $orderitem[0]['createdUserName']; ?> on <?php echo $orderitem[0]['createdDateTime']; ?></td>
                <?php } ?>
            </tr>
        </tbody>
    </table>
</div>