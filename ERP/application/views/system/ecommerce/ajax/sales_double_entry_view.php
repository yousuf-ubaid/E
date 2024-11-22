<div class="form-group">

<input type="hidden" id="sales_id" name="sales_id" value="<?php echo $clent_sales_id ?>"/>
<input type="hidden" id="double_entry_balance" name="double_entry_balance" 
    value="<?php if( trim($clent_order_cr_dr['total_credit_value'] ?? '') !=  trim($clent_order_cr_dr['total_debit_value'] ?? '') ) { echo '1'; } ?>" />

<label class="col-sm-2 control-label">Service Type</label><!--Comments-->
<div class="col-sm-4">
   <span class="form-control"><?php echo $clent_order_detail['service_type'] ?> </span>
</div>

<label class="col-sm-2 control-label">Status</label><!--Comments-->
<div class="col-sm-4">
<span class="form-control"><?php echo $clent_order_detail['status'] ?> </span>
</div>

</div>

<div class="form-group">

<label class="col-sm-2 control-label">Date</label><!--Comments-->
<div class="col-sm-4">
    <span class="form-control"><?php echo $clent_order_detail['date_time'] ?> </span>
</div>

<label class="col-sm-2 control-label">Order</label><!--Comments-->
<div class="col-sm-4">
    <span class="form-control"><?php echo $clent_order_detail['date_time'] ?> </span>
</div>

</div>

<div class="form-group">

<label class="col-sm-2 control-label">Customer </label><!--Comments-->
<div class="col-sm-4">
    <span class="form-control"><?php echo $clent_order_detail['customer'] ?> </span>
</div>

<label class="col-sm-2 control-label">Store Name</label><!--Comments-->
<div class="col-sm-4">
    <span class="form-control"><?php echo $clent_order_detail['store'] ?> </span>
</div>

</div>

<div class="form-group">

<div class="table-responsive">
    <table id="clent_double_entry" class="<?php echo table_class() ?>">
        <thead>
        <tr>
            <!-- <th style="min-width: 10%">Clent Column</th>Code -->
            <th style="min-width: 10%">Segment Code</th><!--Code-->
            <th style="min-width: 10%">GL Code</th><!--Code-->
            <th style="min-width: 10%">Description</th><!--Code-->
            <th style="min-width: 10%">Debit</th><!--Code-->
            <th style="min-width: 10%">Credit</th><!--Code-->
        </tr>
        </thead>
        <tbody>
            <?php foreach($clent_order_cr_dr['data'] as $value){ ?>
            <tr>
                <!-- <td><?php echo $value['client_header'] ?></td> -->
                <td><?php echo $value['segement'] ?></td>
                <td><?php echo $value['gl_code'] ?></td>
                <td><?php echo $value['descripiton'] ?></td>
                <td><?php echo ($value['debit'] > 0) ?  $value['debit']: '0.00' ?></td>
                <td><?php echo ($value['credit'] > 0) ?  $value['credit']: '0.00' ?></td>

            </tr>
            <?php } ?>
            <tr <?php if(trim($clent_order_cr_dr['total_credit_value'] ?? '') != trim($clent_order_cr_dr['total_debit_value'] ?? '') ) { ?> style="background-color:#f8a6a6;" <?php } else { ?> style="background-color:#d4f6d2;"  <?php }?> >
                <td colspan="3"><span class="pull-right text-bold">Total</span></td>
                <td class="h3"><?php echo $clent_order_cr_dr['total_debit_value'] ?></td>
                <td class="h3"><?php echo $clent_order_cr_dr['total_credit_value'] ?></td>
            
            </tr>
        </tbody>
    </table>
</div>

</div>