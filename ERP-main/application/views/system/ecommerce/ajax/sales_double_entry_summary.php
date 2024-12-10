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

    <div class="" style="padding:10px 0px">
        <?php if($clent_order_detail['invoice_auto_id']){ ?>
            <div class="pull-right">
                <a class="btn btn-success" onclick="documentPageView_modal('BSI', <?php echo $clent_order_detail['invoice_auto_id'] ?>)"><i class="fa fa-eye"></i> &nbsp View Supplier Invoice</a>
            </div>
        <?php } ?>

        <h4>Vendor Entry </h4>
        
    </div>

    <?php  if(isset($clent_order_cr_dr['data'])){  ?>
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
                    <?php 
                        foreach($clent_order_cr_dr['data'] as $value){ 
                    ?>
                    <?php if($value['final_value'] != 0 ){ ?>
                        <tr>
                            <td><?php echo $value['segement'] ?></td>
                            <td><?php echo $value['gl_code'] ?></td>
                            <td><?php echo $value['descripiton'] ?></td>
                            <?php if($value['final_value'] < 0 ){ ?>
                                <td><?php echo (($value['final_value']) ?  abs($value['final_value']) : '0.00') ?></td>
                                <td>0.00</td>
                            <?php }else { ?>
                                <td>0.00</td>
                                <td><?php echo (($value['final_value'] > 0) ?  $value['final_value']: '0.00') ?></td>
                            <?php } ?>
                            

                        </tr>
                    <?php } ?>
                    <?php } ?>
                    <tr <?php if(trim($data_cr_dr['credit'] ?? '') != trim($data_cr_dr['debit'] ?? '') ) { ?> style="background-color:#f8a6a6;padding:10px 0px;" <?php } else { ?> style="background-color:#d4f6d2;padding:10px 0px;"  <?php }?>>
                        <td colspan="3"><span class="pull-right text-bold">Total</span></td>
                        <td><?php echo $data_cr_dr['debit'] ?></td>
                        <td><?php echo $data_cr_dr['credit'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

    <?php } else { ?>

        <div class="alert alert-danger">
            <p>No supplier mapping has been set </p>
        </div>

    <?php } ?>


    <div class="" style="padding:10px 0px">
        <?php if($clent_order_detail['customer_auto_id']){ ?>
            <div class="pull-right">
                <a class="btn btn-success" onclick="documentPageView_modal('CINV', <?php echo $clent_order_detail['customer_auto_id'] ?>)"><i class="fa fa-eye"></i> &nbsp View Customer Invoice</a>
            </div>
        <?php } ?>
        <h4>Customer Entry</h4>
    </div>

    <?php  if(isset($clent_order_cr_dr_customer['data'])){  ?>
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
                    <?php foreach($clent_order_cr_dr_customer['data'] as $value){ ?>
                    <?php if($value['final_value'] != 0 ){ ?>
                        <tr>
                            <td><?php echo $value['segement'] ?></td>
                            <td><?php echo $value['gl_code'] ?></td>
                            <td><?php echo $value['descripiton'] ?></td>
                            <?php if($value['final_value'] < 0 ){ ?>
                                <td><?php echo (($value['final_value']) ?  abs($value['final_value']) : '0.00') ?></td>
                                <td>0.00</td>
                            <?php }else { ?>
                                <td>0.00</td>
                                <td><?php echo (($value['final_value'] > 0) ?  $value['final_value']: '0.00') ?></td>
                            <?php } ?>
                            

                        </tr>
                    <?php } ?>
                    <?php } ?>
                    <tr <?php if(trim($data_cr_dr_customer['credit'] ?? '') != trim($data_cr_dr_customer['debit'] ?? '') ) { ?> style="background-color:#f8a6a6;padding:10px 0px;" <?php } else { ?> style="background-color:#d4f6d2;padding:10px 0px;"  <?php }?>>
                        <td colspan="3"><span class="pull-right text-bold">Total</span></td>
                        <td><?php echo $data_cr_dr_customer['debit'] ?></td>
                        <td><?php echo $data_cr_dr_customer['credit'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php } else { ?>

        <div class="alert alert-danger">
            <p>No Customer mapping has been set </p>
        </div>

    <?php } ?>


    <div class="" style="padding:10px 0px">
        <?php if($clent_order_detail['3pl_vendor_auto_id']){ ?>
            <div class="pull-right">
                <a class="btn btn-success" onclick="documentPageView_modal('BSI', <?php echo $clent_order_detail['3pl_vendor_auto_id'] ?>)"><i class="fa fa-eye"></i> &nbsp View 3PL Vendor Invoice</a>
            </div>
        <?php } ?>
        <h4>3PL Vendor Entry</h4>
    </div>
    <?php  if(isset($clent_order_cr_dr_3pl_vendor['data'])){  ?>
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
                    <?php foreach($clent_order_cr_dr_3pl_vendor['data'] as $value){ ?>
                    <?php if($value['final_value'] != 0 ){ ?>
                        <tr>
                            <td><?php echo $value['segement'] ?></td>
                            <td><?php echo $value['gl_code'] ?></td>
                            <td><?php echo $value['descripiton'] ?></td>
                            <?php if($value['final_value'] < 0 ){ ?>
                                <td><?php echo (($value['final_value']) ?  abs($value['final_value']) : '0.00') ?></td>
                                <td>0.00</td>
                            <?php }else { ?>
                                <td>0.00</td>
                                <td><?php echo (($value['final_value'] > 0) ?  $value['final_value']: '0.00') ?></td>
                            <?php } ?>
                            

                        </tr>
                    <?php } ?>
                    <?php } ?>
                    <tr <?php if(trim($data_cr_dr_3pl_vendor['credit'] ?? '') != trim($data_cr_dr_3pl_vendor['debit'] ?? '') ) { ?> style="background-color:#f8a6a6;padding:10px 0px;" <?php } else { ?> style="background-color:#d4f6d2;padding:10px 0px;"  <?php }?>>
                        <td colspan="3"><span class="pull-right text-bold">Total</span></td>
                        <td><?php echo $data_cr_dr_3pl_vendor['debit'] ?></td>
                        <td><?php echo $data_cr_dr_3pl_vendor['credit'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

    <?php } else { ?>


    <div class="alert alert-danger">
        <p>No 3PL vendor mapping has been set </p>
    </div>

    <?php } ?>

    <div class="" style="padding:10px 0px">
        <?php if($clent_order_detail['3pl_customer_auto_id']){ ?>
            <div class="pull-right">
                <a class="btn btn-success" onclick="documentPageView_modal('CINV', <?php echo $clent_order_detail['3pl_customer_auto_id'] ?>)" ><i class="fa fa-eye"></i> &nbsp View 3PL Customer Invoice</a>
            </div>
        <?php } ?>
        <h4>3PL Customer Entry</h4>
    </div>

    <?php  if(isset($clent_order_cr_dr_3pl_customer['data'])){  ?>
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
                    <?php foreach($clent_order_cr_dr_3pl_customer['data'] as $value){ ?>
                    <?php if($value['final_value'] != 0 ){ ?>
                        <tr>
                            <td><?php echo $value['segement'] ?></td>
                            <td><?php echo $value['gl_code'] ?></td>
                            <td><?php echo $value['descripiton'] ?></td>
                            <?php if($value['final_value'] < 0 ){ ?>
                                <td><?php echo (($value['final_value']) ?  abs($value['final_value']) : '0.00') ?></td>
                                <td>0.00</td>
                            <?php }else { ?>
                                <td>0.00</td>
                                <td><?php echo (($value['final_value'] > 0) ?  $value['final_value']: '0.00') ?></td>
                            <?php } ?>
                            

                        </tr>
                    <?php } ?>
                    <?php } ?>
                    <tr <?php if(trim($data_cr_dr_3pl_customer['credit'] ?? '') != trim($data_cr_dr_3pl_customer['debit'] ?? '') ) { ?> style="background-color:#f8a6a6;padding:10px 0px;" <?php } else { ?> style="background-color:#d4f6d2;padding:10px 0px;"  <?php }?>>
                        <td colspan="3"><span class="pull-right text-bold">Total</span></td>
                        <td><?php echo $data_cr_dr_3pl_customer['debit'] ?></td>
                        <td><?php echo $data_cr_dr_3pl_customer['credit'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php } else { ?>

        <div class="alert alert-danger">
            <p>No 3PL customer mapping has been set </p>
        </div>

    <?php } ?>

    <div class="" style="padding:10px 0px">
        <?php if($clent_order_detail['direct_receipt_auto_id']){ ?>
            <div class="pull-right">
                <a class="btn btn-success" onclick="documentPageView_modal('RV', <?php echo $clent_order_detail['direct_receipt_auto_id'] ?>)"><i class="fa fa-eye"></i> &nbsp View Direct Receipt</a>
            </div>
        <?php } ?>
        <h4>Direct Receipt Voucher Entry</h4>
    </div>

    <?php  if(isset($clent_order_cr_dr_direct_receipt['data'])){  ?>
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
                    <?php foreach($clent_order_cr_dr_direct_receipt['data'] as $value){ ?>
                    <?php if($value['final_value'] != 0 ){ ?>
                        <tr>
                            <td><?php echo $value['segement'] ?></td>
                            <td><?php echo $value['gl_code'] ?></td>
                            <td><?php echo $value['descripiton'] ?></td>
                            <?php if($value['final_value'] < 0 ){ ?>
                                <td><?php echo (($value['final_value']) ?  abs($value['final_value']) : '0.00') ?></td>
                                <td>0.00</td>
                            <?php }else { ?>
                                <td>0.00</td>
                                <td><?php echo (($value['final_value'] > 0) ?  $value['final_value']: '0.00') ?></td>
                            <?php } ?>
                            

                        </tr>
                    <?php } ?>
                    <?php } ?>
                    <tr <?php if(trim($data_cr_dr_direct_receipt['credit'] ?? '') != trim($data_cr_dr_direct_receipt['debit'] ?? '') ) { ?> style="background-color:#f8a6a6;padding:10px 0px;" <?php } else { ?> style="background-color:#d4f6d2;padding:10px 0px;"  <?php }?>>
                        <td colspan="3"><span class="pull-right text-bold">Total</span></td>
                        <td><?php echo $data_cr_dr_direct_receipt['debit'] ?></td>
                        <td><?php echo $data_cr_dr_direct_receipt['credit'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php } else { ?>

        <div class="alert alert-danger">
            <p>No Direct Receipt mapping has been set </p>
        </div>

    <?php } ?>

    <div class="" style="padding:10px 0px">
        <?php if($clent_order_detail['jv_auto_id']){ ?>
            <div class="pull-right">
                <a class="btn btn-success" onclick="documentPageView_modal('JV', <?php echo $clent_order_detail['jv_auto_id'] ?>)"><i class="fa fa-eye"></i> &nbsp View Journel Voucher</a>
            </div>
        <?php } ?>
        <h4>Journel Voucher Entry</h4>
    </div>

    <?php  if(isset($clent_order_cr_dr_jv['data'])){  ?>
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
                    <?php foreach($clent_order_cr_dr_jv['data'] as $value){ ?>
                    <?php if($value['final_value'] != 0 ){ ?>
                        <tr>
                            <td><?php echo $value['segement'] ?></td>
                            <td><?php echo $value['gl_code'] ?></td>
                            <td><?php echo $value['descripiton'] ?></td>
                            <?php if($value['final_value'] < 0 ){ ?>
                                <td><?php echo (($value['final_value']) ?  abs($value['final_value']) : '0.00') ?></td>
                                <td>0.00</td>
                            <?php }else { ?>
                                <td>0.00</td>
                                <td><?php echo (($value['final_value'] > 0) ?  $value['final_value']: '0.00') ?></td>
                            <?php } ?>
                            

                        </tr>
                    <?php } ?>
                    <?php } ?>
                    <tr <?php if(trim($data_cr_dr_jv['credit'] ?? '') != trim($data_cr_dr_jv['debit'] ?? '') ) { ?> style="background-color:#f8a6a6;padding:10px 0px;" <?php } else { ?> style="background-color:#d4f6d2;padding:10px 0px;"  <?php }?>>
                        <td colspan="3"><span class="pull-right text-bold">Total</span></td>
                        <td><?php echo $data_cr_dr_jv['debit'] ?></td>
                        <td><?php echo $data_cr_dr_jv['credit'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php } else { ?>

        <div class="alert alert-danger">
            <p>No 3PL customer mapping has been set </p>
        </div>

    <?php } ?>


    <div class="" style="padding:10px 0px">
        <?php if($clent_order_detail['dn_auto_id']){ ?>
            <div class="pull-right">
                <a class="btn btn-success" onclick="documentPageView_modal('DN', <?php echo $clent_order_detail['dn_auto_id'] ?>)"><i class="fa fa-eye"></i> &nbsp View Debit Note</a>
            </div>
        <?php } ?>
        <h4>Debit Note Entry</h4>
    </div>

    <?php  if(isset($clent_order_cr_dr_debit_note['data'])){  ?>
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
                    <?php foreach($clent_order_cr_dr_debit_note['data'] as $value){ ?>
                    <?php if($value['final_value'] != 0 ){ ?>
                        <tr>
                            <td><?php echo $value['segement'] ?></td>
                            <td><?php echo $value['gl_code'] ?></td>
                            <td><?php echo $value['descripiton'] ?></td>
                            <?php if($value['final_value'] < 0 ){ ?>
                                <td><?php echo (($value['final_value']) ?  abs($value['final_value']) : '0.00') ?></td>
                                <td>0.00</td>
                            <?php }else { ?>
                                <td>0.00</td>
                                <td><?php echo (($value['final_value'] > 0) ?  $value['final_value']: '0.00') ?></td>
                            <?php } ?>
                            

                        </tr>
                    <?php } ?>
                    <?php } ?>
                    <tr <?php if(trim($data_cr_dr_debit_note['credit'] ?? '') != trim($data_cr_dr_debit_note['debit'] ?? '') ) { ?> style="background-color:#f8a6a6;padding:10px 0px;" <?php } else { ?> style="background-color:#d4f6d2;padding:10px 0px;"  <?php }?>>
                        <td colspan="3"><span class="pull-right text-bold">Total</span></td>
                        <td><?php echo $data_cr_dr_debit_note['debit'] ?></td>
                        <td><?php echo $data_cr_dr_debit_note['credit'] ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php } else { ?>

        <div class="alert alert-danger">
            <p>No Debit Note mapping has been set </p>
        </div>

    <?php } ?>

</div>