
<?php
$notes = '';
$checkedAnyGovernmentYes = '';
$checkedAnyGovernmentNo = '';
$checkedAnySupportYes = '';
$checkedAnySupportNo = '';
if (!empty($benficeryHeader)) {
    $notes = $benficeryHeader['da_meNotes'];

    if ($benficeryHeader['da_meSupportReceivedYN'] == 1) {
        $checkedAnySupportYes = 'checked';
    } else if ($benficeryHeader['da_meSupportReceivedYN'] == 2) {
        $checkedAnySupportNo = 'checked';
    }

    if ($benficeryHeader['da_meGovAssistantYN'] == 1) {
        $checkedAnyGovernmentYes = 'checked';
    } else if ($benficeryHeader['da_meGovAssistantYN'] == 2) {
        $checkedAnyGovernmentNo = 'checked';
    }
}
?>
<br>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-2">
        <label class="title">Any Other Supports Received</label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <label for="checkbox">Yes&nbsp;&nbsp;</label>
                    <input id="da_meSupportReceivedYes" type="radio" data-caption="" class="columnSelected"
                           name="da_meSupportReceivedYN" value="1" <?php echo $checkedAnySupportYes; ?>>
                </div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <label for="checkbox">No&nbsp;&nbsp;</label>
                    <input id="da_meSupportReceivedNo" type="radio" data-caption="" class="columnSelected"
                           name="da_meSupportReceivedYN" value="2" <?php echo $checkedAnySupportNo; ?>>
                </div>
            </label>
        </div>
    </div>
    <div class="form-group col-sm-2">
        <label class="title">Government Assessment done</label>
    </div>
    <div class="form-group col-sm-4">
        <div class="skin-section extraColumns">
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <label for="checkbox">Yes&nbsp;&nbsp;</label>
                    <input id="da_meGovAssistantYes" type="radio" data-caption="" class="columnSelected"
                           name="da_meGovAssistantYN" value="1" <?php echo $checkedAnyGovernmentYes; ?>>
                </div>
            </label>
            <label class="radio-inline">
                <div class="skin-section extraColumnsgreen">
                    <label for="checkbox">No&nbsp;&nbsp;</label>
                    <input id="da_meGovAssistantNo" type="radio" data-caption="" class="columnSelected"
                           name="da_meGovAssistantYN" value="2" <?php echo $checkedAnyGovernmentNo; ?>>
                </div>
            </label>
        </div>
    </div>
</div>
<div class="row" style="margin-top: 10px;">
    <div class="form-group col-sm-6">
        <table class="table table-bordered table-condensed no-color" id="income_add_table">
            <thead>
            <tr>
                <th>Type of Assistance</th>
                <th>Name of Organization</th>
                <th>Amount</th>
                <th style="width: 40px;">
                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more_income()"><i
                            class="fa fa-plus"></i></button>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($supportAssitance)) {
                foreach ($supportAssitance as $roll) { ?>
                    <tr>
                        <td style=""><input type="text" name="me_assitanceName[]" placeholder="Assistance"
                                            class="form-control" value="<?php echo $roll['assitanceName']; ?>"></td>
                        <td style=""><input type="text" name="me_Organization[]" placeholder="Organization"
                                            class="form-control" value="<?php echo $roll['Organization']; ?>"></td>
                        <td style=""><input type="text" name="me_year[]" placeholder="Amount"
                                            class="form-control" value="<?php echo $roll['amount']; ?>"></td>
                        <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                    </tr>
                    <?php
                }
            } else { ?>
                <tr>
                    <td style=""><input type="text" name="me_assitanceName[]" placeholder="Assistance"
                                        class="form-control"></td>
                    <td style=""><input type="text" name="me_Organization[]" placeholder="Organization"
                                        class="form-control"></td>
                    <td style=""><input type="text" name="me_year[]" placeholder="Amount"
                                        class="form-control"></td>
                    <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>
    </div>
    <div class="form-group col-sm-2">
        <label class="title">Notes</label>
    </div>
    <div class="form-group col-sm-4">
                <textarea class="form-control" id="da_meNotes" name="da_meNotes"
                          rows="4"><?php echo $notes; ?></textarea>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.extraColumnsgreen input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

        number_validation();
    });

    function add_more_income() {
        var appendData = $('#income_add_table tbody tr:first').clone();
        appendData.find('input,select,textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $("#income_add_table").append(appendData);
        Inputmask().mask(document.querySelectorAll("input"));
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

</script>
<?php
