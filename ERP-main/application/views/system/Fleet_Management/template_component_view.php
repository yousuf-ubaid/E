<?php
$rig = fetch_rig();
$asset_arr = fetch_asset_utilization(1);
$thread = fetch_thread_utilization();
$physical = fetch_physical_utilization();
$statuscon = fetch_status_utilization();
$com_arr = fetch_com_utilization(1);
?>


<?php
if ($checklists)
{


    foreach ($checklists as $val)
    { ?>
        <tr>


            <td style="min-width: 20%"><?php echo form_dropdown('com_code', $com_arr, $val['asset_id'], 'class="form- selct_val select2 " onchange ="updateselectedcomitem(this)" required disabled'); ?></td>
            <td style="min-width: 5%"><input type="text" name="serial" value="<?php echo $val['serial_number'] ?>" class="form-control " required disabled></td>
            <td style="min-width: 15%"><input type="text" name="assetdes" value="<?php echo $val['description'] ?>" class="form-control " required disabled></td>
            <td style="min-width: 8%"><?php echo form_dropdown('thread_condition', $thread, $val['thread_condition_id'], 'class="form-control select2 " required disabled'); ?></td>
            <td style="min-width: 8%"><?php echo form_dropdown('physical_condition', $physical, $val['physical_condition_id'], 'class="form-control select2 " required disabled'); ?></td>
            <td style="min-width: 8%"><?php echo form_dropdown('status', $statuscon, $val['status_id'], 'class="form-control select2 " required disabled'); ?></td>
            <td style="min-width: 25%"><input type="text" name="date_from" value="<?php echo $val['date_time_from'] ?>" class="form-control datepicker" required disabled></td>
            <td style="min-width: 25%"><input type="text" name="date_to" value="<?php echo $val['date_time_to'] ?>" class="form-control datepicker " required disabled></td>
            <td style="min-width: 5%"><input type="text" name="total_hours" value="<?php echo $val['hours'] ?>" class="form-control " required disabled></td>
        </tr>
<?php
    }
} ?>