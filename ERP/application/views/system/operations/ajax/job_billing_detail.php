<?php
    $contract_activity = array();
    $contract_price = get_contract_pricelist($job_id);
    $current_date = date('Y-m-d H:i:s');
    $default_date = date('Y-m-d H:i:s',strtotime($current_date));
?>

    <table class="table table-bordered table-condensed" id="billing_detail_tbl">
        <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>Date From</th>
            <th>Date To</th>
            <th>Is Standby</th>
            <th>Is NPT</th>
            <th>Hours/Mins</th>
            <th>Price List</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Total</th>
            <!-- <th>Action</th> -->
           
        </tr>
        </thead>
        <tbody id="billing_detail_section">
            
            <?php foreach($activity as $activity) { ?>
                <tr>
                    <td>
                        <input type="checkbox" name="activity_id[]" class="text-center" value="<?php echo $activity['id']  ?>">
                    </td>

                    <td>
                        <span><?php echo $activity['description'] ?> </span>
                        <?php //echo //form_dropdown('billing_activity[]', $contract_activity, '', 'class="form-control select2" id="billing_activity" onchange="change_activity_billing($(this))"'); ?>
                    </td>
                    <td>
                        <span><?php echo $activity['dateFrom'] ?> </span>
                        <!-- <input type="text" name="billing_description[]"
                                class="form-control billing_description" required> -->
                    </td>
                    
                    <td>
                        <span><?php echo $activity['dateTo'] ?> </span>
                        <!-- <input type="datetime-local" id="billing_fromDate" class="form-control billing_fromDate" name="billing_fromDate[]" value="<?php //echo //$default_date ?>"> -->
                    </td>
                    <td>
                        <span> <?php echo ($activity['isStandby'] == 1) ? 'Yes' : 'No' ?>  </span>
                        <!-- <input type="datetime-local" id="billing_toDate" class="form-control billing_toDate" name="billing_toDate[]" value="<?php //echo $default_date ?>"> -->
                    </td>
                    <td>
                        <span> <?php echo ($activity['isNpt'] == 1) ? 'Yes' : 'No' ?>  </span>
                        <!-- <div class="text-center">
                            <input type="hidden" name="billing_isStandby[]" class="hid_isStandby" value=''>
                            <input type="text" class="billing_isStandby" value="">
                        </div> -->
                    </td>
                    <td>
                        <span> <?php echo $activity['hours']  ?>  </span>
                        <input type="text" name="billing_hours_qty[]"
                                class="form-control billing_hours_qty hide" value="<?php echo $activity['hours']  ?>">

                        <!-- <div class="text-center">
                            <input type="hidden" name="billing_isNpt[]" class="hid_isNPT" value=''>
                            <input type="text" class="billing_isNPT" value="">
                        </div> -->
                    </td>

                    <td>
                        <?php echo form_dropdown('billing_price[]', $contract_price, '', 'class="form-control select2 billing_price" id="billing_price" onchange="change_pricing_billing($(this))"'); ?> 
                    </td>

                    <td>
                        <input type="text" name="billing_qty[]" class="form-control billing_qty text-right" onchange="change_price_product_qty($(this))" disabled required>
                    </td>

                    <td>
                        <input type="text" name="billing_rate[]" class="form-control billing_rate text-right" disabled required>
                    </td>

                    <td>
                        <input type="text" name="billing_rate_total[]" class="form-control billing_rate_total text-right" disabled required>
                    </td>


                </tr>

            <?php } ?>

        </tbody>
    </table>

<script>
    $('#billing_detail_tbl').DataTable();
</script>