<?php
    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('sales_maraketing_transaction', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);

    $total_value_sum_1 = 0;
    $total_value_sum_2 = 0;
    $total_margin = 0;
    $total_markup = 0;
    $total_commision = 0;
   // $transaction_currency = '('.$master['transactionCurrency'].')';
?>

<div class="row">
    <div class="col-md-12">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td></span>
                    <?php echo $item_details['itemSystemCode']; ?><!--Confirmed--> -
                    <?php echo $item_details['itemDescription']; ?><!--Approved--></td>
                
            </tr>
        </table>
    </div>
   
</div>
<table class="table table-bordered table-striped table-condesed">
    <thead>
    <tr>
        <th  class=""> #</th>
        <th  class=""> PO Code</th>
        <th  class=""> PO Date</th>
        <th  class=""> Qty</th>
        <th  class=""> Unit Amount</th>
        <th  class=""> Discount Amount</th>
    </tr>
    
    </thead>
    <tbody >
        <?php foreach($item_Data as $key=>$data) { 
            
            ?>
            <tr class="">
                <td><?php echo $key+1 ?></td>
                <td><?php echo $data['purchaseOrderCode'] ?></td>
                <td><?php echo $data['documentDate'] ?></td>
                <td class="text-right"><?php echo $data['requestedQty'] ?></td>
                <td class="text-right"><b><?php echo $data['companyLocalCurrency'] ?>:</b>  <?php echo $data['unitAmount'] ?></td>
                <td class="text-right"><b><?php echo $data['companyLocalCurrency'] ?>:</b>  <?php echo $data['discountAmount'] ?></td>
                
                
                
            </tr>
        <?php } ?>
        
        

    </tbody>
    <tfoot id="table_tfoot">
        
    </tfoot>
</table>

<script>

</script>