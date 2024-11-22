<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$priority_arr = all_priority_new_drop();
$current_date = format_date($this->common_data['current_date']);
?>
<ul class="list-group">
    <?php
    if (!empty($todolist)) {
        foreach ($todolist as $val) {
            ?>
            <li class="list-group-item <?php if ($val['priority'] == 1) { echo 'bg-success'; } else if ($val['priority'] == 2) { echo 'bg-warning'; } else { echo 'bg-danger'; } ?>" style="padding: 5px;" id="list">
                <div class="form-check">
                    <input type="checkbox"
                           class="icheckbox_minimal-blue"
                           id="donechk_<?php echo $val['autoId'] ?>"
                           name="donechk"
                           onclick="changeDone(<?php echo $val['autoId'] ?>)"
                           value="" <?php if ($val['isCompleated'] == -1) {
                            echo 'checked';
                        } ?>>
                    <label class="form-check-label" style="margin-left: 10px" for="list1">
                        <?php echo trim_value($val['description'],40)  ?>
                    </label>
                    <div class="tools pull-right">
                        <i class="fa fa-edit" style="color:#3576ff" onclick="openEditToDoListModal(<?php echo $val['autoId'] ?>)" title="Edit List" ></i>
                        |
                        <i class="fa fa-trash-o text-red" onclick="deletetodoList(<?php echo $val['autoId'] ?>)"></i>
                    </div>
                    <small class="label label-info pull-right"
                           style="font-size: 11px; margin-right: 20px"><?php echo $val['startTime'] ?></small>
                </div>
            </li>
            <?php
        }
    }else{
        ?>
       <?php echo $this->lang->line('dashboard_record_not_found');?><!-- Record Not Found-->
    <?php
    }
    ?>
</ul>
