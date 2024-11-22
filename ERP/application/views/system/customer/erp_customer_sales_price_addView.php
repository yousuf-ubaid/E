
    <style xmlns="http://www.w3.org/1999/html">
        .search-no-results {
            text-align: center;
            background-color: #f6f6f6;
            border: solid 1px #ddd;
            margin-top: 10px;
            padding: 1px;
        }

        .label {
            display: inline;
            padding: .2em .8em .3em;
        }

        .actionicon {
            display: inline-block;
            font-weight: normal;
            font-size: 12px;
            background-color: #89e68d;
            -moz-border-radius: 2px;
            -khtml-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            padding: 2px 5px 2px 5px;
            line-height: 14px;
            vertical-align: text-bottom;
            box-shadow: inset 0 -1px 0 #ccc;
            color: #888;
        }

        .headrowtitle {
            font-size: 11px;
            line-height: 30px;
            height: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 0 25px;
            font-weight: bold;
            text-align: left;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
            color: rgb(130, 130, 130);
            background-color: white;
            border-top: 1px solid #ffffff;
        }


        .center {
            text-align: center;
        }
    </style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
if (!empty($itemDetails)) {
    $com_currency = $this->common_data['company_data']['company_default_currency'];
    $date_format_policy = date_format_policy();
    $current_date = current_format_date();
    ?>
    <div class="table-responsive mailbox-messages">

        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">

                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong>#</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong><?php echo $this->lang->line('transaction_common_item_code') ?><!--Item Code--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong><?php echo $this->lang->line('transaction_common_item_description') ?><!--Item Description--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong><?php echo $this->lang->line('common_date_from') ?><!--Date From--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong><?php echo $this->lang->line('common_date_to') ?><!--Date To--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center"><strong><?php echo $this->lang->line('sales_maraketing_masters_default_price') ?><!--Default Price--> (<?php echo $com_currency; ?>)</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center"><strong><?php echo $this->lang->line('sales_maraketing_masters_customer_price') ?><!--Customer Price--> (<?php echo $com_currency; ?>)</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center"><strong><?php echo $this->lang->line('sales_maraketing_masters_allow_modiification') ?><!--Allow Modification--></strong></td>
            </tr>
            <tr class="task-cat-upcoming">

            </tr>


            <?php
            $x = 1;
            foreach ($itemDetails as $val) {
                if(empty($val['isActive']) || $val['isActive'] == 0) {
                    if (empty($val['cpsAutoID']) || $val['cpsAutoID'] == $cpsAutoID) {
                        ?>
                        <tr>

                            <td class="mailbox-star"><?php echo $x; ?></td>
                            <td class="mailbox-star"><?php echo $val['itemSystemCode']; ?></td>
                            <td class="mailbox-star"><?php echo $val['itemDescription']; ?></td>
                            <td class="mailbox-star">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="applicableDateFrom[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $val['applicableDateFrom']; ?>" id="applicableDateFrom"
                                           class="form-control" required>
                                </div>
                            </td>
                            <td class="mailbox-star">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="applicableDateTo[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $val['applicableDateTo']; ?>" id="applicableDateTo"
                                           class="form-control" required>
                                </div>
                            </td>
                            <td class="mailbox-star"><input style="text-align: right" name="defaultPrice[]"
                                                            value="<?php echo number_format($val['DefaultPrice'], 2); ?>"
                                                            readonly></td>
                            <td class="mailbox-star"><input style="text-align: right" name="AddsalesPrice[]"
                                                            value="<?php echo $val['salesPrice']; ?>"></td>
                            <td align="center">
                                <input class="hidden itemID" name="itemID[]" id="itemID"
                                       value="<?php echo $val['itemAutoID']; ?>">
                                <input class="hidden" name="cpriceID[]" id="cpriceID"
                                       value="<?php echo $val['customerPriceID']; ?>">
                                <?php if($val['isModificationAllowed']==1){
                                            ?>
                                            <input type="hidden" id="chkbox" class="changeMandatory" name="chkbox[]" value="1">
                                            <input id="moficable" type="checkbox" onchange="changeMandatory(this)" name="moficable[]" value="1" checked>
                                            <?php
                                        }else{
                                            ?>
                                            <input type="hidden" id="chkbox" class="changeMandatory" name="chkbox[]" value="0">
                                            <input id="moficable" type="checkbox" onchange="changeMandatory(this)" name="moficable[]" value="1">
                                            <?php
                                        } ?>

                            </td>
                        </tr>

                        <?php

                        $x++;
                    }
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <footer class="search-no-results" style="text-align: center;">
            THERE ARE NO RECORDS TO DISPLAY.
    </footer>

    <?php
}
?>

<script>
    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    });

    function changeMandatory(obj, str){
        var status = ($(obj).is(':checked')) ? 1 : 0;
        var str = $(obj).attr('data-value');
        $(obj).closest('tr').closest('tr').find('.changeMandatory').val(status);
    }
</script>

