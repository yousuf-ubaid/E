<?php
$date_format_policy = date_format_policy();
$current_date = current_format_date();
if ($detail) {
?>
<table class="table table-bordered table-condensed">
    <thead>
    <tr>
        <th>Description</th>
        <th></th>
        <th>Checked Date</th>
        <th>Checked By</th>
    </tr>
    </thead>
    <?php
    foreach ($detail as $val) {
        if ($val["inputType"] == "checkbox") {
            ?>
            <tr>
                <td>
                    <?php echo $val["description"] ?>
                </td>
                <td class="text-center"><input type="<?php echo $val["inputType"] ?>" class="checklist"
id="<?php echo $val["criteriaID"] ?>_<?php echo $val["templateID"] ?>" value="" onclick="checkListValue(this)"></td>
                <td><div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="checkedDate[]" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" class="form-control checkedDate" required>
                    </div></td>
                <td><input type="text" name="checkedbyEmpName[]" class="form-control checkedbyEmpName" readonly>
                    <input type="hidden" name="checkedbyEmpID[]" class="checkedbyEmpID"></td>
            </tr>
            <?php
        } elseif($val["inputType"] == "text") {
            ?>
            <tr>
                <td>
                    <?php echo $val["description"] ?>
                </td>
                <td class="text-center"><input type="<?php echo $val["inputType"] ?>" class="form-control textlist"
                           id="<?php echo $val["criteriaID"] ?>_<?php echo $val["templateID"] ?>" onchange="textChangeValue(this)"></td>
                <td><div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="checkedDate[]" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" class="form-control checkedDate" required>
                    </div></td>
                <td><input type="text" name="checkedbyEmpName[]" class="form-control checkedbyEmpName" readonly>
                    <input type="hidden" name="checkedbyEmpID[]" class="checkedbyEmpID">
                </td>
            </tr>
            <?php
        }
    }
    } else {
        ?>
        <header class="infoarea">
            <div class="search-no-results">NO RECORDS FOUND
            </div>
        </header>
        <?php
    }
    ?>
    <script>
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });
        Inputmask().mask(document.querySelectorAll("input"));
        function checkListValue(element) {
           if($(element).is(":checked")){
               $(element).closest('tr').find('.checkedbyEmpName').val('<?php echo current_user() ?>');
               $(element).closest('tr').find('.checkedbyEmpID').val('<?php echo current_userID() ?>');
           }else{
               $(element).closest('tr').find('.checkedbyEmpName').val('');
               $(element).closest('tr').find('.checkedbyEmpID').val('');
           }


        }

        function textChangeValue(element) {
            if($(element).val() != ""){
                $(element).closest('tr').find('.checkedbyEmpName').val('<?php echo current_user() ?>');
                $(element).closest('tr').find('.checkedbyEmpID').val('<?php echo current_userID() ?>');
            }else{
                $(element).closest('tr').find('.checkedbyEmpName').val('');
                $(element).closest('tr').find('.checkedbyEmpID').val('');
            }
        }

    </script>