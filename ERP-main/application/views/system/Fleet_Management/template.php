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
            <td style="min-width: 20%"><?php echo form_dropdown('asset_code1', $asset_arr, $val['asset_id'], 'class="form- selct_val select2 " onchange ="updateselecteditem(this)" required disabled'); ?></td>
            <td style="min-width: 5%"><input type="text" name="serial1" value="<?php echo $val['serial_number'] ?>" class="form-control " required disabled></td>
            <td style="min-width: 15%"><input type="text" name="assetdes1" value="<?php echo $val['description'] ?>" class="form-control" required disabled></td>
            <td style="min-width: 8%"><?php echo form_dropdown('thread_condition1', $thread, $val['thread_condition_id'], 'class="form-control select2" required disabled'); ?></td>
            <td style="min-width: 8%"><?php echo form_dropdown('physical_condition1', $physical, $val['physical_condition_id'], 'class="form-control select2 " required disabled'); ?></td>
            <td style="min-width: 8%"><?php echo form_dropdown('status1', $statuscon, $val['status_id'], 'class="form-control select2 " required disabled'); ?></td>
            <td style="min-width: 25%"><input type="text" name="date_from1" value="<?php echo $val['date_time_from'] ?>" class="form-control datepicker" required disabled></td>
            <td style="min-width: 25%"><input type="text" name="date_to1" value="<?php echo $val['date_time_to'] ?>" class="form-control datepicker" required disabled></td>
            <td style="min-width: 5%"><input type="text" name="total_hours1" value="<?php echo $val['hours'] ?>" class="form-control" required disabled></td>
            <td style="min-width: 10%"><button type="button" onclick="deleteasset_line_record('<?php echo $val['id'] ?>')"><i class="glyphicon glyphicon-trash" style="color: red;"></i></button> <button type="button" onclick="editasset_line_record('<?php echo $val['id'] ?>')"><i class="glyphicon glyphicon-pencil" style="color: green;"></i></button> <button type="button" onclick="checklist_record('<?php echo $val['id'] ?>')"><i class="glyphicon glyphicon-list-alt" style="color: green;"></i></button></td>
        </tr>
<?php
    }
} ?>