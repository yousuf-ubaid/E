<style>
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
<br>
<?php

if (!empty($batch)) { ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">#</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Area</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Sub Area</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Farmer</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Batch Code</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Start Date</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Closing Date</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Input</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Output</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Mor</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Balance</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">FVR</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Weight</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;">Age(Days)</td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align:center;">Collection</td>


            </tr>
            <?php
            $x = 1;
            $totalChicks = 0;
            $mortalityChicksTotal = 0;
            $collectiontot = 0;
            $balance = 0;
            foreach ($batch as $val) {
                $collectiontot += $val['collectionQty'];
                $balance += $val['balanceQty'];

                ?>

                <tr>
                    <td class="mailbox-star" width="5%"><?php echo $x; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['farmlocation']; ?></td>
                    <td class="mailbox-star" width="10%"><?php echo $val['subarea']; ?></td>
                    <td class="mailbox-star" width="20%"><?php echo $val['farmname']; ?></td>
                    <td class="mailbox-star" width="20%"><?php echo $val['batchCode']; ?></td>
                    <td class="mailbox-star" width="30%"><?php echo $val['batchStartDate']; ?></td>
                    <td class="mailbox-star" width="15%"><?php echo $val['batchClosingDate']; ?></td>
                    <td class="mailbox-star tableHeader center" width="1%"><?php echo $val['inputQty']; ?></td>
                    <td class="mailbox-star tableHeader center" width="1%"><?php echo $val['receivedQty']; ?></td>
                    <td class="mailbox-star tableHeader center" width="1%"><?php echo $val['mortalityQty']; ?></td>
                    <td class="mailbox-star tableHeader center" width="1%"> <?php echo $val['balanceQty']; ?></td>
                    <td class="mailbox-star tableHeader center"
                        width="1%"> <?php echo $val['fvr']; ?></td>
                    <td class="mailbox-star tableHeader center "
                        width="1%"> <?php echo $val['avgBodyWeight']; ?></td>
                    <?php if ($val['age'] >= 28 && $val['age'] <= 32) {

                        $fontcl = "#ced80f";
                    } else if ($val['age'] >= 33 && $val['age'] <= 40) {

                        $fontcl = "#0eb205";

                    } else if ($val['age'] >= 40) {
                        $fontcl = "#e00205";
                    } else {
                        $fontcl = "#333";
                    } ?>
                    <td class="mailbox-star tableHeader center" style="color:<?php echo $fontcl ?>"
                        width="1%"><strong><?php echo $val['age']; ?></strong>


                    </td>
                        <td class="mailbox-star tableHeader" style="text-align: right;">
                        <input type="text" name="amount[]" size="5" id="amount_<?php echo $val['batchID'] ?>"
                               onchange="collection_validation(this,<?php echo $val['batchID'] ?>,<?php echo $val['balanceQty']; ?>,<?php echo $collectionautoid ?>)"
                               onkeyup="collection_validation_keyup(this,<?php echo $val['batchID'] ?>,<?php echo $val['balanceQty']; ?>)"
                               class="number" value="<?php echo $val['collectionQty']; ?>">
                    </td>


                </tr>

                <?php
                $x++;
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total" colspan="10">Total</span></td>
                <td class="text-center total"><?php echo number_format($balance, 2); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="text-right total"><?php echo number_format($collectiontot, 2); ?></td>

            </tr>
            </tfoot>
        </table><!-- /.table -->
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results">THERE ARE NO RECORDS TO DISPLAY.</div>
    <?php
}
?>
<script type="text/javascript">
    var Otable;
    $(document).ready(function () {
        $('.btn-wizard').addClass('disabled');
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

    });

    function collection_validation(data, id, total, collectionautoid) {
        var updateval = 0;

        if (data.value > 0) {
            updateval = 1;
        } else {
            updateval = 0
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Buyback/add_collection_amt"); ?>',
            dataType: 'json',
            data: {
                'amount': data.value,
                'batchid': id,
                'collectionautoid': collectionautoid,
                'updatedvalue': updateval
            },
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    load_buyback_collection(collectionautoid);
                    $('.btn-wizard').removeClass('disabled');
                } else {

                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });

    }

    function collection_validation_keyup(data, id, total) {
        if (data.value > 0) {
            if (total >= data.value) {

            } else {
                $("#amount_" + id).val('');
                myAlert('w', 'You can not enter collection amount greater than selected Collection Balance Amount');
            }
        }
    }
</script>