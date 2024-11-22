<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { 
       ?>

    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
       <?php
           /* if ($type == 'html') {
                echo export_buttons('salespersonrpt', 'Sales Person Performance', True, True);
            } */ ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salespersonrpt">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Item wise Sales Person Performance Report </strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder_detail" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    
                    <tr>
                        <th><?php echo $this->lang->line('sales_markating_sales_person'); ?></th>
                        <th><?php echo $this->lang->line('transaction_common_item_code'); ?></th>
                        <th><?php echo $this->lang->line('erp_item_master_secondary_code'); ?></th>
                        <th><?php echo $this->lang->line('transaction_common_item_description'); ?></th>
                        <th><?php echo $this->lang->line('transaction_common_uom'); ?></th>
                        <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                        <th><?php echo $this->lang->line('common_unit_cost'); ?></th>
                        <th><?php echo $this->lang->line('common_total_value'); ?><!--Value--></th>
                    </tr>

                    </thead>
                    <tbody>
                    <?php
                    if ($details){
                        $value_total =0;
                        $currencyDecimalPlaces = 2;
                        foreach ($details as $key => $val) { 
                            $averageAmount=0;
                            
                            $currencyDecimalPlaces =$val['currencyDecimalPlaces'];

                            if($val['qty'] == 0){
                                $averageAmount=number_format($val['amount'], $val['currencyDecimalPlaces']);
                            }else{
                                $averageAmount=number_format($val['amount']/$val['qty'], $val['currencyDecimalPlaces']);
                            }
                            ?>
                            
                            
                                <tr>
                                    <td><a href="#" class="drill-down-cursor"
                                           onclick="openSalespersonDetaildd(<?php echo $val['salesPersonID'] ?>,<?php echo $val['itemAutoID']?>)"> <?php echo $val["salesPersonName"] ?></a>
                                    </td>
                                    <td><?php echo $val["itemSystemCode"] ?> </td>
                                    <td><?php echo $val['seconeryItemCode']; ?></td>
                                    <td><?php echo $val["itemDescription"] ?> </td>
                                    <td><?php echo $val['UnitOfMeasure']; ?></td>
                                    <td><?php echo $val['qty']; ?></td>
                                    <td class="text-right"><?php echo $averageAmount ?></td>
                                    <td class="text-right"><?php echo number_format($val['amount'], $val['currencyDecimalPlaces']); ?></td>
                                </tr>        
                        <?php 
                        $value_total +=   $val['amount'];
                        }
                        ?>
                        <tr>
                            <td colspan="7"><b><?php echo $this->lang->line('common_total'); ?><!--Total--></b></td>
                            <td class="text-right reporttotal"><?php echo number_format($value_total, $currencyDecimalPlaces) ?></td>
                        </tr>
                        <?php
                    }
                       ?>
                    
                    
                    </tbody>
                    <tfoot>


                    </tfoot>


                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>
<?php
} ?>
<script>
    $('#tbl_rpt_salesorder_detail').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>