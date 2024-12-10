<?php function findKey($array, $keySearch)
{
    // check if it's even an array
    if (!is_array($array)) return FALSE;

    // key exists
    if (array_key_exists($keySearch, $array)) return TRUE;

    // key isn't in this array, go deeper
    foreach ($array as $key => $val) {
        // return true if it's found
        if (findKey($val, $keySearch)) return TRUE;
    }

    return FALSE;
} ?>

<div style="max-height: 600px; overflow:auto;">
    <table id="xtable" class="<?php echo table_class() ?>" style="white-space: nowrap;width: 100%">
        <?php
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('hrms_leave_management', $primaryLanguage);
        $this->lang->load('common', $primaryLanguage);
        $this->lang->load('calendar', $primaryLanguage);
        ?>
        <thead>
        <tr>
            <th><?php echo $this->lang->line('hrms_leave_management_employee_name'); ?><!--Employee--></th>

            <?php if ($header) {
                foreach ($header as $value) {
                    echo '<th style="">'.$value['description'].'</th>';
                }
            } ?>
            <th><?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        <?php

        if ($details) {

            foreach ($details as $val) {

                $key = array_search($val['empID'], array_column($leaveHistory, 'empID'));
                $CI = get_instance();
                $leave = array();
                if (is_numeric($key)) {


                    $keyexist = findKey($leaveHistory, $key);
                    if ($keyexist) {
                        $leave = $leaveHistory[$key];
                    }
                }

                ?>
                <tr>
                <td><?php echo $val['Ename2'] ?></td>
                <?php if ($header) {
                    foreach ($header as $value) {
                        $string = str_replace(' ', '', $value['description']);

                        ?>

                        <td style="text-align: center"><?php
                            if ($val['confirmedYN'] != 1) {
                                $disabled = '';
                            } else {
                                $disabled = 'disabled';
                            }

                            if ($val['policyMasterID'] == 2) {
                                $hours = floor($val[$string] / 60);
                                $minutes = $val[$string] % 60;
                                $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
                                $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

                                $hours2 = isset($leave[$string]) ? floor($leave[$string] / 60) : 0;
                                $minutes2 = isset($leave[$string]) ? $leave[$string] % 60 : 0;
                                $hours2 = str_pad($hours2, 2, '0', STR_PAD_LEFT);
                                $minutes2 = str_pad($minutes2, 2, '0', STR_PAD_LEFT);
                                ?>
                                <!--  -->
                                <?php echo ($val['confirmedYN'] != 1) ? 'Balance : ' . $hours2 . 'h ' . $minutes2 . 'm' : ''; ?>
                                <input <?php echo $disabled ?>
                                    onchange="updateLeaveAdjustmentDetail(<?php echo $val['policyMasterID'] ?>,this,<?php echo $value['leaveType'] ?>,<?php echo $val['empID'] ?>)"
                                    placeholder="00" value="<?php echo $hours ?>" class="number" min="0" maxlength="2"
                                    style="width: 30px;" type="text" id="noOfHours" name="noOfHours"> :
                                <input <?php echo $disabled ?>
                                    onchange="updateLeaveAdjustmentDetail(<?php echo $val['policyMasterID'] ?>,this,<?php echo $value['leaveType'] ?>,<?php echo $val['empID'] ?>)"
                                    placeholder="00" type="text" min="0" max="60" value="<?php echo $minutes ?>"
                                    style="width: 30px;" maxlength="2" id="NoOfMinutes" class="number limit"
                                    name="NoOfMinutes">

                                <?php
                            } else {

                                ?>
                                <?php echo ($val['confirmedYN'] != 1) && !empty($leave) ? ' Balance : ' . $leave[$string] : ''; ?>

                                <input style="text-align: right;width: 60px" type="number" <?php echo $disabled; ?> value="<?php echo $val[$string] ?>"
                                       onchange="updateLeaveAdjustmentDetail(<?php echo $val['policyMasterID'] ?>,this,<?php echo $value['leaveType'] ?>,<?php echo $val['empID'] ?>)">
                                <?php
                            }
                            ?>
                        </td>
                        <?php
                    }
                    ?>
                    <td>
                        <?php if ($val['confirmedYN'] != 1) { ?>
                            <input type="text" value="<?php echo $val['comment'] ?>" name="desc" id="desc_<?php echo $val['empID'] ?>"
                                   onchange="updatecomment(this.value,<?php echo $val['empID'] ?>)">
                            <?php
                        } else { echo $val['comment']; } ?>
                    </td>
                    <td><?php if ($val['confirmedYN'] != 1) {
                            ?>
                            <button onclick="delete_adjustment(<?php echo $val['empID'] ?>)" class="btn btn-xs btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                            <?php
                        } ?>
                    </td>


                    </tr>
                    <?php
                }
            }
        }
        ?>

        </tbody>
    </table>
</div>
<hr>
<script>

    $(document).ready(function () {
        //called when key is pressed in textbox
        $(".number").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                //display error message
                return false;
            }
        });

        $('#xtable').tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 900
        });
    });
    $(function () {
        $(".limit").change(function () {
            var max = parseInt($(this).attr('max'));
            var min = parseInt($(this).attr('min'));
            if ($(this).val() > max) {
                $(this).val(max);
            }
            else if ($(this).val() < min) {
                $(this).val(min);
            }
        });
    });
</script>