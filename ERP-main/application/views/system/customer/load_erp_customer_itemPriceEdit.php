<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('inventory', $primaryLanguage);
if ($type == 'html') { ?>

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
    </style>
<?php }

if(!empty($customerdetail)) {
    ?>
<div class="row" style="margin-left: 2%; margin-left: 2%">
    <label><strong><?php echo $this->lang->line('common_customer');?><!--Customer--> :</strong> <?php echo $customerdetail['customerSystemCode'] . ' | ' . $customerdetail['customerName']; ?></label>
</div>
    </br>
    <?php
}
if (!empty($itemDetails)) {
    $com_currency = $this->common_data['company_data']['company_default_currency'];
    ?>
    <div class="table-responsive mailbox-messages">
        <table class="table table-hover table-striped">
            <tbody>
            <tr class="task-cat-upcoming">
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong>#</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong><?php echo $this->lang->line('transaction_common_item_code');?><!--Item Code--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong><?php echo $this->lang->line('erp_item_master_secondary_code');?><!--Secondary code--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01;"><strong><?php echo $this->lang->line('transaction_common_item_description');?><!--Item Description--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center"><strong><?php echo $this->lang->line('sales_maraketing_masters_default_price');?><!--Default Price -->(<?php echo $com_currency; ?>)</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center"><strong><?php echo $this->lang->line('sales_maraketing_masters_customer_price');?><!--Customer Price--> (<?php echo $com_currency; ?>)</strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center"><strong><?php echo $this->lang->line('sales_maraketing_masters_applicable_from');?><!--Applicable From--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center"><strong><?php echo $this->lang->line('sales_maraketing_masters_applicable_until');?><!--Applicable Until--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center"><strong><?php echo $this->lang->line('sales_maraketing_masters_allow_modiification');?><!--Allow Modification--></strong></td>
                <td class="headrowtitle" style="border-bottom: solid 1px #f76f01; text-align: center"><strong><?php echo $this->lang->line('common_active');?><!--Active--></strong></td>
            </tr>
            <tr class="task-cat-upcoming">
            </tr>


            <?php
            $x = 1;
            foreach ($itemDetails as $val) {
                ?>

                <tr>
                    <input class="hidden" name="itemAutoID[]" id="itemAutoID" value="<?php echo $val['itemAutoID']; ?>">
                    <input class="hidden" name="customerPriceID[]" id="customerPriceID" value="<?php echo $val['customerPriceID']; ?>">
                    <td class="mailbox-star"><?php echo $x; ?></td>
                    <td class="mailbox-star"><?php echo $val['itemSystemCode']; ?></td>
                    <td class="mailbox-star"><?php echo $val['seconeryItemCode']; ?></td>
                    <td class="mailbox-star"><?php echo $val['itemDescription']; ?></td>
                    <td class="mailbox-star"><input style="text-align: right" name="defaultPrice[]" value="<?php echo number_format($val['DefaultPrice'], $this->common_data['company_data']['company_default_decimal']); ?>" readonly></td>
                    <td class="mailbox-star"><input style="text-align: right" name="salesPrice[]" value="<?php echo number_format($val['salesPrice'], $this->common_data['company_data']['company_default_decimal']); ?>" readonly></td>
                    <td class="mailbox-star">
                        <?php if($val['applicableDateFrom']){
                            echo $val['applicableDateFrom'];
                        } else{
                            echo 'Not Assigned';
                        }?>
                    </td>
                    <td class="mailbox-star">
                        <?php if($val['applicableDateTo']){
                            echo $val['applicableDateTo'];
                        } else{
                            echo 'Not Assigned';
                        }?>

                    </td>
                    <td style="text-align:center;">
                      <!--  <div class="skin-section extraColumnsgreen"> -->
                            <input id="modify" type="checkbox"  class="columnSelected" onclick="modifyCustomerPrice(this,<?php echo $val['customerPriceID']; ?>)"
                                   name="modify[]" value="<?php echo $val['isModificationAllowed']; ?>"
                <?php if($val['isModificationAllowed']=='1')  {
                    echo 'checked="checked"';
                }?>
                                <?php if($View == 0)  {
                                    echo 'disabled';
                                }?>
                            >
                      <!--  </div> -->
                    </td>

                    <td style="text-align:center;">
                        <input class="active" type="hidden" name="isActive[]"
                               value=<?php echo $val['isActive'] ?>/>
                        <!-- <div class="skin-section extraColumnsgreen"> -->
                            <input id="active" type="checkbox"  class="columnSelected" onclick="deactivateCustomerPrice(this,<?php echo $val['customerPriceID']; ?>)"
                                   name="active[]" value="<?php echo $val['isActive']; ?>"
                <?php if($val['isActive']=='1')  {
                    echo 'checked="checked"';
                }?>
                <?php if($View == 0)  {
                    echo 'disabled';
                }?>
                            >
                       <!-- </div> -->
                    </td>
                </tr>

                <?php
                $x++;
            }
           ?>
            </tbody>
        </table>
    </div>
    <?php
} else { ?>
    <br>
    <div class="search-no-results" style="text-align: center;"><?php echo $this->lang->line('common_no_records_found')?><!--No Records Found-->.</div>
    <?php
}
?>

<script>
    $('.extraColumnsgreen input').iCheck({
        checkboxClass: 'icheckbox_square_relative-green',
        increaseArea: '20%'
    });

    function deactivateCustomerPrice(val ,id) {
        var a = $(val).closest('tr').find('input[type="hidden"]').val();
        if ($(val).is(':checked')) {
            $(val).closest('tr').find('input[type="hidden"]').val(1);
        }
        else {
            $(val).closest('tr').find('input[type="hidden"]').val(0);
        }
        var checked = $(val).closest('tr').find('input[type="hidden"]').val();

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure')?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_deactivate_this_price')?>",/*You want to Deactivate this Price!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes')?>!"/*Yes*/
            },
            function () {
               // alert(checked + id);
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'checkedVal': checked,'customerPriceID': id},
                    url: "<?php echo site_url('Customer/deactivate_CustomerWisePrice'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            attach_CustomerWisePrice_modal(data[2]);
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function modifyCustomerPrice(val ,id) {
        var a = $(val).closest('tr').find('input[type="hidden"]').val();
        if ($(val).is(':checked')) {
            $(val).closest('tr').find('input[type="hidden"]').val(1);
        }
        else {
            $(val).closest('tr').find('input[type="hidden"]').val(0);
        }
        var checked = $(val).closest('tr').find('input[type="hidden"]').val();

               // alert(checked + id);
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'checkedVal': checked,'customerPriceID': id},
                    url: "<?php echo site_url('Customer/modifyCustomerPrice'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            attach_CustomerWisePrice_modal(data[2]);
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
    }
</script>


