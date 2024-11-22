<div class="form-group">



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
            <th style="min-width: 10%">Segment Code</th><!--Code-->
            <th style="min-width: 10%">GL Code</th><!--Code-->
            <th style="min-width: 10%">Description</th><!--Code-->
            <th style="min-width: 10%">Debit</th><!--Code-->
            <th style="min-width: 10%">Credit</th><!--Code-->
        </tr>
        </thead>
        <tbody>
            <?php foreach($clent_order_cr_dr['data'] as $value){ ?>
            <?php if($value['final_debit_value'] != 0  ){ ?>
                <tr>
                    <td><?php echo $value['segement'] ?></td>
                    <td><?php echo $value['gl_code'] ?></td>
                    <td><?php echo $value['descripiton'] ?></td>
                    <td><?php echo (($value['final_debit_value']) ?  abs($value['final_debit_value']) : '0.00') ?></td>
                    <td>0.00</td>
                </tr>
            <?php } ?>
            <?php if($value['final_credit_value'] != 0  ){ ?>
                <tr>
                    <td><?php echo $value['segement'] ?></td>
                    <td><?php echo $value['gl_code'] ?></td>
                    <td><?php echo $value['descripiton'] ?></td>
                    <td>0.00</td>
                    <td><?php echo (($value['final_credit_value']) ?  abs($value['final_credit_value']) : '0.00') ?></td> 
                </tr>
            <?php } ?>
            <?php } ?>
            <tr <?php if(trim($clent_order_cr_dr['total_credit_value'] ?? '') != trim($clent_order_cr_dr['total_debit_value'] ?? '') ) { ?> style="background-color:#f8a6a6;" <?php } else { ?> style="background-color:#d4f6d2;"  <?php }?>>
                <td colspan="3"><span class="pull-right text-bold">Total</span></td>
                <td><?php echo $clent_order_cr_dr['total_debit_value'] ?></td>
                <td><?php echo $clent_order_cr_dr['total_credit_value'] ?></td>
            </tr>
        </tbody>
    </table>
</div>

</div>