<style>
   
</style>

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

    <label class="col-sm-2 control-label">Order ID </label><!--Comments-->
    <div class="col-sm-4">
        <span class="form-control"><?php echo $clent_order_detail['order'] ?> </span>
    </div>

</div>


<div class="form-group">

<div class="table-responsive">
    <table id="clent_double_entry" class="<?php echo table_class() ?>">
        <thead>
            <tr>
                <!-- <th style="min-width: 10%">Clent Column</th>Code -->
                <th style="min-width: 10%">Date</th><!--Code-->
                <th style="min-width: 10%">Message</th><!--Code-->
            </tr>
        </thead>
        <tbody>
            <?php foreach($processed_log as $value){ ?>
            <tr>
                <td><?php echo $value['date'] ?></td>
                <td style="color:<?php echo $value['alert_color'] ?>"><?php echo $value['message'] ?>
                    <?php if($value['status'] == 2) { ?>
                        <button type="button" class="btn btn-success pull-right" onClick="reGenerateInvoice(<?php echo $value['invoice_type'] ?>)"><i class="fa fa-check"> </i> Re-run</button>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</div>