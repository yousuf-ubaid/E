<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();

?>

    <link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>

    <div class="row">
        <div class="col-sm-12 table-responsive mailbox-messages">
            <?php
            if (!empty($Com_MemID)) {
                $r = 1;
                if ($CollectionType == 3) { // Cash collaboration tax (sittu tax)
                    ?>
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr class="task-cat-upcoming" style="font-size:12px; color: black;font-weight: bold;">
                            <td style="border-bottom: solid 1px #f76f01;">#</td>
                            <td style="border-bottom: solid 1px #f76f01;">Member</td>
                            <td style="border-bottom: solid 1px #f76f01;">Total Amount</td>
                            <td style="border-bottom: solid 1px #f76f01;">Tax Percentage</td>
                            <td style="border-bottom: solid 1px #f76f01;">Amount</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($Com_MemID as $row) {
                            ?>
                            <tr>
                                <td class="mailbox-star"><?php echo $r; ?></td>
                                <td><?php echo $row["MemberCode"] . ' | ' . $row["CName_with_initials"]; ?></td>
                                <td style="width: 250px">
                                    <input type="text" onfocus="this.select();" name="TotalAmount[]"
                                           onkeyup="change_amount(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)" placeholder="0.00"
                                           class="form-control number TotalAmount input-mini" required>
                                </td>
                                <td style="width: 150px">
                                    <div class="input-group">
                                        <input type="text" name="Percentage[]" placeholder="0.00" value=""
                                               onkeyup="cal_percentage(this)" onfocus="this.select();"
                                               class="form-control number Percentage" required>
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </td>
                                <td style="width: 250px;">
                                    <input type="text" name="Amount[]" placeholder="0.00" value=""
                                           onkeyup="cal_Amount(this)" onfocus="this.select();"
                                           class="form-control number Amount" readonly>
                                </td>
                            </tr>
                            <?php
                            $r++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php

                } else if ($CollectionType == 1) { // Sandha
                    ?>
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr class="task-cat-upcoming" style="font-size:12px; color: black;font-weight: bold;">
                            <td style="border-bottom: solid 1px #f76f01;">#</td>
                            <td style="border-bottom: solid 1px #f76f01;">Member</td>
                            <td style="border-bottom: solid 1px #f76f01;">Amount</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($Com_MemID as $row) {
                            ?>
                            <tr>
                                <td class="mailbox-star"><?php echo $r; ?></td>
                                <td><?php echo $row["CName_with_initials"]; ?></td>
                                <td>
                                    <input type="text" name="Amount[]" class="form-control"
                                           value="<?php echo $CollectionAmount; ?>"
                                           placeholder="<?php echo $this->lang->line('communityngo_collection_Amount'); ?>"
                                           required>
                                </td>
                            </tr>
                            <?php
                            $r++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php

                } else { // Marriage Tax
                    ?>
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr class="task-cat-upcoming" style="font-size:12px; color: black;font-weight: bold;">
                            <td style="border-bottom: solid 1px #f76f01;">#</td>
                            <td style="border-bottom: solid 1px #f76f01;">Member</td>
                            <td style="border-bottom: solid 1px #f76f01;">Amount</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($Com_MemID as $row) {
                            ?>
                            <tr>
                                <td class="mailbox-star"><?php echo $r; ?></td>
                                <td><?php echo $row["CName_with_initials"]; ?></td>
                                <td>
                                    <input type="text" name="Amount[]" class="form-control"
                                           value="<?php echo $CollectionAmount; ?>"
                                           placeholder="<?php echo $this->lang->line('communityngo_collection_Amount'); ?>"
                                           required readonly>
                                </td>
                            </tr>
                            <?php
                            $r++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php
                }
            } else {
            } ?>

        </div>
    </div>

    <script>

        $(document).ready(function () {

            $('.select2').select2();
            $("[rel=tooltip]").tooltip();

            tax_total = 0;
        });

        function change_amount(element) {
            $(element).closest('tr').find('.Percentage').val('');
            $(element).closest('tr').find('.Amount').val('');
        }

        function validateFloatKeyPress(el, evt) {
            //alert(currency_decimal);
            var charCode = (evt.which) ? evt.which : event.keyCode;
            var number = el.value.split('.');
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            //just one dot
            if (number.length > 1 && charCode == 46) {
                return false;
            }
            //get the carat position
            var caratPos = getSelectionStart(el);
            var dotPos = el.value.indexOf(".");
            if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
                return false;
            }
            return true;
        }

        function getSelectionStart(o) {
            if (o.createTextRange) {
                var r = document.selection.createRange().duplicate()
                r.moveEnd('character', o.value.length)
                if (r.text == '') return o.value.length
                return o.value.lastIndexOf(r.text)
            } else return o.selectionStart
        }

        function cal_percentage(element) {
            if (element.value < 0 || element.value > 100 || element.value == '') {
                swal("Cancelled", "Percentage should be between 0 - 100", "error");
                $(element).closest('tr').find('.Percentage').val('');
                $(element).closest('tr').find('.Amount').val('');
            } else {
                var TotalAmount = parseFloat($(element).closest('tr').find('.TotalAmount').val());
                if (TotalAmount) {
                    $(element).closest('tr').find('.Amount').val((TotalAmount / 100) * parseFloat(element.value))
                }
            }
        }

        function cal_Amount(element) {
            var TotalAmount = parseFloat($(element).closest('tr').find('.TotalAmount').val());
            if (element.value > TotalAmount) {
                myAlert('w', 'Percentage amount should be less than or equal to Amount');
                $(element).closest('tr').find('.Percentage').val('');
                $(element).val('')
            } else {
                if (TotalAmount) {
                    $(element).closest('tr').find('.Percentage').val(((parseFloat(element.value) / TotalAmount) * 100).toFixed(3))
                }
            }
        }


    </script>

<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 3/20/2018
 * Time: 12:02 PM
 */