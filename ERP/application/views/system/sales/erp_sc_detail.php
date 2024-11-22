<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);



echo form_open('', 'role="form" id="sales_commission_detail_form"');
//$num = count($extra['sales_person']);
/*echo "<pre>";
print_r($extra['invoice']);
echo "</pre>";
exit;*/

$norecordfound = $this->lang->line('common_no_records_found');

if (empty($extra['invoice'])) {
    echo "<div class='alert alert-danger' role='alert'><center>$norecordfound<!--No record found--></center></div>";
} else {
    foreach ($extra['invoice'] as $key => $val) {
        $salesPersonID = $val['salesperson']["salesPersonID"];
        $salesPersonName = $val['salesperson']["SalesPersonCode"] . ' - ' . $val['salesperson']["SalesPersonName"];
        $invoice_total = 0;
        $percentage = $val['salesperson']["percentage"];
        $commission_total = 0; ?>
        <div class="box box-default collapsed-box box-solid" style="margin-bottom: 5px">
            <div class="box-header with-border" style="padding: 6px">
                <h3 class="box-title" style="font-size: 14px"><span class="glyphicon glyphicon-hand-right"
                                                                    aria-hidden="true"></span>
                    &nbsp;&nbsp;<?php echo $salesPersonName ?>
                </h3>
                <div class="box-tools pull-right">
                    <button data-widget="collapse" class="btn btn-box-tool" style="color:#fff;padding: 2px"><i
                                class="fa fa-clone fa-plus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped table-condensed table-row-select"
                       style="margin-top: -0px !important;"
                       id="table_<?php echo $salesPersonID ?>">
                    <thead>
                    <tr>
                        <th style="width: 20px;">&nbsp;</th>
                        <th><?php echo $this->lang->line('sales_markating_transaction_customer_invoice');?></th><!--Customer Invoice-->
                        <th style="width: 150px;"><?php echo $this->lang->line('common_total');?></th><!--Total-->
                        <th style="width: 50px;"><?php echo $this->lang->line('common_action');?></th><!--Action-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $x = 1;
                    if (!empty($val['invoice'])) {
                        foreach ($val['invoice'] as $value) {
                            $checked = $value["checked"] == 1 ? "checked" : "";
                            $amount = number_format($value['companyLocalAmount'], $value['companyLocalCurrencyDecimalPlaces']);
                            echo "<tr><td>{$x}</td><td>{$value['invoiceCode']} - {$value['invoiceDate']} - {$value['customerName']} - {$value['invoiceNarration']}</td><td>{$value['companyLocalCurrency']} <span class='pull-right'>{$amount}</span></td><td><center><div class='skin skin-square'><div class='skin-section' id='extraColumns'><input type='checkbox' class='checkbox_{$value['salesPersonID']}' data-amount='{$value['companyLocalAmount']}' data-sales_person='{$value['salesPersonID']}' name='isActive[]' value='{$value['invoiceAutoID']}|{$value['salesPersonID']}|{$value['companyLocalAmount']}' $checked onchange='calculate_invoice_total({$value['salesPersonID']})'><label for='checkbox'>&nbsp;</label></div></div></center></td></tr>";
                            if ($value["checked"] == 1) {
                                $invoice_total += $value['companyLocalAmount'];
                            }

                            $x++;
                        }

                    } else {
                        $norecordfound = $this->lang->line('common_no_records_found');
                        echo "<tr class='danger'><td colspan='4'><center>$norecordfound<!--No record found--></center></td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <?php echo $this->lang->line('common_description');?> <!--Description-->
                        <textarea class="form-control" rows="2"
                                  name="description_<?php echo $salesPersonID ?>"></textarea><br>
                        <table class="table table-bordered table-striped table-condensed table-row-select">
                            <thead>
                            <tr>
                                <th style="min-width: 5px">#</th>
                               <!-- <th>Date from</th>
                                <th>Date to</th>-->
                                <th style="min-width: 50px"><?php echo $this->lang->line('sales_markating_transaction_sales_commission_start_amount');?> <span class="currency">( <?php echo $this->common_data['company_data']['company_default_currency'];?>   )</span></th><!--Start Amount-->
                                <th style="min-width: 50px"><?php echo $this->lang->line('sales_markating_transaction_sales_commission_end_amount');?> <span class="currency">( <?php echo $this->common_data['company_data']['company_default_currency'];?>   )</span></th><!--End Amount-->
                                <th style="min-width: 20px"><?php echo $this->lang->line('common_percentage');?></th><!--Percentage-->
                            </tr>
                            </thead>
                            <tbody id="table_body">
                            <?php $x = 1;
                            if (!empty($val['salestarget'])) {
                                foreach ($val['salestarget'] as $value) {
                                    $amount = number_format($value['fromTargetAmount'], $extra['header']['transactionCurrencyDecimalPlaces']) . ' - ' . number_format($value['toTargetAmount'], $extra['header']['transactionCurrencyDecimalPlaces']);
                                    $startamount = number_format($value['fromTargetAmount'], $extra['header']['transactionCurrencyDecimalPlaces']);
                                    $endamount = number_format($value['toTargetAmount'], $extra['header']['transactionCurrencyDecimalPlaces']);
                                    echo "<tr><td>{$x}</td><td><span class='pull-right'>{$startamount}</span></td><td><span class='pull-right'>{$endamount}</span></td><td class='pull-right'>{$value['percentage']}</td></tr>";
                                    $x++;
                                    /*if ($value['fromTargetAmount'] < $invoice_total) {
                                        $percentage = $value['percentage'];
                                    }*/
                                }
                            }else{
                                echo "<tr class='danger'><td colspan='4'><center>No record found</center></td></tr>";
                            }
                            $commission_total = (($invoice_total / 100) * $percentage);
                            $netCommision = $commission_total + $val['salesperson']["adjustment"];
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <td class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_commission_invoice_total');?> <span class="currency">( <?php echo $this->common_data['company_data']['company_default_currency'];?>  )</span></td><!--Invoice total-->
                                <td class="text-right" id="invoice_total_display_<?php echo $salesPersonID ?>">
                                    <span><?php echo number_format($invoice_total, $extra['header']['transactionCurrencyDecimalPlaces']); ?></span>
                                    <input type="hidden" class="form-control number"
                                           id="invoice_total_<?php echo $salesPersonID ?>"
                                           name="invoice_total_<?php echo $salesPersonID ?>"
                                           value="<?php echo $invoice_total; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><?php echo $this->lang->line('common_percentage');?> <span class="currency">( <?php echo $this->common_data['company_data']['company_default_currency'];?>  )</span></td><!--Percentage-->
                                <td class="text-right">
                                    <div class="input-group pull-right" style="width: 150px"><input type="text"
                                                                                                    class="form-control number"
                                                                                                    onkeyup="calculate_percentage(<?php echo $salesPersonID ?>)"
                                                                                                    value="<?php echo $percentage; ?>"
                                                                                                    id="percentage_<?php echo $salesPersonID ?>"
                                                                                                    name="percentage_<?php echo $salesPersonID ?>">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_commission_commission_total');?> <span class="currency">( <?php echo $this->common_data['company_data']['company_default_currency'];?>  )</span></td><!--Commission total-->
                                <td class="text-right"
                                    id="commission_total_<?php echo $salesPersonID ?>"><?php echo number_format($commission_total, $extra['header']['transactionCurrencyDecimalPlaces']); ?></td>
                            </tr>
                            <tr>
                                <td class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_commission_adjustment');?> <span class="currency">( <?php echo $this->common_data['company_data']['company_default_currency'];?>  )</span></td><!--Adjustments-->
                                <td class="text-right">
                                    <div class="input-group pull-right" style="width: 150px">
                                        <div class="input-group-addon currency">( <?php echo $this->common_data['company_data']['company_default_currency'];?> )</div>
                                        <input type="text" class="form-control number"
                                               onkeyup="calculate_percentage(<?php echo $salesPersonID ?>)"
                                               value="<?php echo $val['salesperson']["adjustment"] ?>"
                                               id="adjustment_<?php echo $salesPersonID ?>"
                                               name="adjustment_<?php echo $salesPersonID ?>">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right"><?php echo $this->lang->line('sales_markating_transaction_sales_net_commission');?> <span class="currency">( <?php echo $this->common_data['company_data']['company_default_currency'];?>  )</span></td><!--Net Commission-->
                                <td class="text-right"
                                    id="net_commission_<?php echo $salesPersonID ?>"><?php echo number_format($netCommision, $extra['header']['transactionCurrencyDecimalPlaces']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} ?>
<hr>
<div class="text-right m-t-xs">
    <button class="btn btn-primary-new size-lg" type="button" onclick="save_sale_commission()"><?php echo $this->lang->line('common_save');?></button><!--Save-->
</div>
</form>
<script type="text/javascript">
    //number_validation();
    $("input.number").numeric();
    var sales_target = <?php echo json_encode($extra['sales_target']); ?>;
    function calculate_percentage(salesPersonID) {
        var percentage = $('#percentage_' + salesPersonID).val();
        if (percentage >= 100) {
            myAlert('w', 'Percentage exceeded 100%', 1000);
        }
        var invoice_total = $('#invoice_total_' + salesPersonID).val();
        var adjustment = $('#adjustment_' + salesPersonID).val();
        if (!adjustment) {
            adjustment = 0;
        }
        var commission_total = ((parseFloat(invoice_total) / 100) * parseFloat(percentage));
        $('#commission_total_' + salesPersonID).text(parseFloat(commission_total).formatMoney(2, '.', ','));
        var net_commission = (((parseFloat(invoice_total) / 100) * parseFloat(percentage)) + parseFloat(adjustment));
        $('#net_commission_' + salesPersonID).text(parseFloat(net_commission).formatMoney(2, '.', ','));
    }

    function calculate_invoice_total(salesPersonID) {
        var percentage = 0;
        var invoice_total = 0;
        $('#table_' + salesPersonID + ' input:checked').each(function () {
            invoice_total += parseFloat($(this).data('amount'));
        });
        $('#invoice_total_display_' + salesPersonID + ' span').text(invoice_total.formatMoney(2, '.', ','));
        $('#invoice_total_' + salesPersonID).val(invoice_total);
        /*for (var i = 0; i < sales_target.length; i++) {
            if (sales_target[i]['salesPersonID'] == salesPersonID) {
                if (parseFloat(sales_target[i]['fromTargetAmount']) < invoice_total) {
                    percentage = sales_target[i]['percentage'];
                }
            }
        }*/
        $.each(sales_target[salesPersonID], function(index, item) {
            if ((parseFloat(item.fromTargetAmount) <= invoice_total) && (invoice_total <= parseFloat(item.toTargetAmount))) {
                percentage = item.percentage;
            }
        });
        $('#percentage_' + salesPersonID).val(percentage);
        calculate_percentage(salesPersonID);
    }

    function save_sale_commission() {
        var data = $('#sales_commission_detail_form').serializeArray();
        data.push({'name': 'salesCommisionID', 'value': salesCommisionID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Sales/sales_commission_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {

                }
            },
            error: function () {

            }
        });
    }
</script>