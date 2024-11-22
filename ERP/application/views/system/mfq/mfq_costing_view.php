<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

if (!empty($details)) {
    ?>
    <?php
    $x = 1;
    foreach ($details as $val) {
        ?>

        <tr>
            <input class="hidden" name="costingID[]" id="costingID" value="<?php echo $val['costingID']; ?>">
            <input class="hidden" name="categoryID[]" id="categoryID" value="<?php echo $val['categoryID']; ?>">
            <td class="mailbox-star"><?php echo $x; ?></td>
            <td class="mailbox-star"><?php echo $val['category']; ?></td>
            <td style="text-align:center;">
                <input id="modify" type="checkbox"  class="skin-section extraColumnsgreen columnSelected" onclick="enableCostEntry(this,<?php echo $val['costingID']; ?>)"
                       name="isEntryEnabled[]" value="<?php echo $val['isEntryEnabled']; ?>"
                    <?php if($val['isEntryEnabled']=='1')  {
                        echo 'checked="checked"';
                    }?>
                >
            </td>

            <td style="text-align:center;">
                <input class="manualEntry" type="hidden" name="isManualEntry[]"
                       value=<?php echo $val['linkedDocEntry'] ?>/>
                <input id="manualEntry" type="checkbox"  class="entryEnabled columnSelected" onclick="enableManualCost(this,<?php echo $val['costingID']; ?>)"
                       name="manualEntry[]" value="<?php echo $val['manualEntry']; ?>"
                    <?php if($val['manualEntry']=='1')  {
                        echo 'checked="checked"';
                    }?>
                    <?php if($val['isEntryEnabled']=='0')  {
                        echo 'disabled';
                    }?>
                >
            </td>

            <td style="text-align:center;">
                <input class="linkedDocEntry" type="hidden" name="isLinkedDocEntry[]"
                       value=<?php echo $val['linkedDocEntry'] ?>/>
                    <input id="linkedDocEntry" type="checkbox"  class="entryEnabled columnSelected" onclick="enableLinkedDocCost(this,<?php echo $val['costingID']; ?>)"
                           name="linkedDocEntry[]" value="<?php echo $val['linkedDocEntry']; ?>"
                        <?php if($val['linkedDocEntry']=='1')  {
                            echo 'checked="checked"';
                        }?>
                        <?php if($val['isEntryEnabled']=='0')  {
                            echo 'disabled';
                        }?>
                    >
            </td>
        </tr>

        <?php
        $x++;
    }
    ?>
    <?php
} else { ?>
    <br>
    <div class="search-no-results" style="text-align: center;">Costing Entry Setup Not Configured</div>
    <?php
}
?>

    <script>
        $('.extraColumnsgreen input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            increaseArea: '20%'
        });

        function enableCostEntry(val ,id) {
            var a = $(val).closest('tr').find('input[type="hidden"]').val();
            if ($(val).is(':checked')) {
                $(val).closest('tr').find('input[type="hidden"]').val(1);
                $(val).closest('tr').find('.entryEnabled').prop('disabled', false);
            }
            else {
                $(val).closest('tr').find('input[type="hidden"]').val(0);
                $(val).closest('tr').find('.entryEnabled').prop('disabled', true);
            }
            var checked = $(val).closest('tr').find('input[type="hidden"]').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'checkedVal': checked,'costingID': id},
                url: "<?php echo site_url('MFQ_Costing/enable_cost_entry'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function enableManualCost(val ,id) {
            var a = $(val).closest('tr').find('input[type="hidden"]').val();
            if ($(val).is(':checked')) {
                $(val).closest('tr').find('input[type="hidden"]').val(1);
            }
            else {
                $(val).closest('tr').find('input[type="hidden"]').val(0);
            }
            var checked = $(val).closest('tr').find('input[type="hidden"]').val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'checkedVal': checked,'costingID': id},
                url: "<?php echo site_url('MFQ_Costing/enable_manual_cost_entry'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function enableLinkedDocCost(val ,id) {
            var a = $(val).closest('tr').find('input[type="hidden"]').val();
            if ($(val).is(':checked')) {
                $(val).closest('tr').find('input[type="hidden"]').val(1);
            }
            else {
                $(val).closest('tr').find('input[type="hidden"]').val(0);
            }
            var checked = $(val).closest('tr').find('input[type="hidden"]').val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'checkedVal': checked,'costingID': id},
                url: "<?php echo site_url('MFQ_Costing/enable_linkedDoc_cost_entry'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    </script>