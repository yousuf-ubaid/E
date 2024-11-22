<?php
$mas_data = $inv_data['mas_data'];

$dPlace = $mas_data['invDecPlace'];
$cur_code = $mas_data['CurrencyCode'];
$payment_method = [ 0 => 'Un paid', 1 => 'Bank Transfer', 2 => 'Pay Pal' ];
?>

<style>
    .form-text{
        font-size: 11px;
        padding: 3px 12px;
        height: 28px;
    }

    .date-input{
        padding: 3px 12px;
        height: 28px;
    }

    .separation {
        border-left: 3px solid #f7f4f4;
        min-height: 60px;
    }

    #payment_type{
        height: 29px;
        padding: 2px 4px;
        font-size: 12px;
        width: 110px;
    }

    .bootBox-btn-margin{
        margin-right: 10px;
    }

    .bootbox-confirm{
        z-index: 999999 !important;
    }
</style>

<div >
    <h3>Invoice No #<?=$mas_data['invNo']?> <button type="button" class="close" data-dismiss="modal">&times;</button></h3>
    <hr/>
</div>


<div class="row" style="margin-top: 5px">
    <div class="col-md-<?=($view_type != 'E')?'6':'12'?>">
        <div class="table-responsive">
            <span style="font-weight: bold">Invoiced To</span><br/>
            <?=$mas_data['company_name']?><br/>
            <?=$mas_data['companyPrintAddress']?>

            <br/><br/>
            <div class="row">
                <div class="col-sm-6">
                    <span style="font-weight: bold">Invoice Date</span><br/>
                    <?php
                    $inv_date = date('Y-m-d', strtotime($mas_data['invDate']));
                    if($view_type == 'E'){
                        echo '<div style="width: 130px;">
                                <div class="input-group" id="">
                                    <div class="input-group-addon" style="padding: 3px 10px;"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                                    <input type="text" class="form-control date-input" id="inv_date" value="'.$inv_date.'" name="inv_date">
                                </div>
                              </div>
                              <input type="hidden" name="company_id" value="'.$company_id.'" >';
                    }
                    else{
                        echo date('l, F dS, Y ', strtotime($inv_date));
                    }
                    ?>
                    <input type="hidden" id="dPlace" value="<?=$dPlace?>">
                </div>
            </div>

            <br/>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b>Invoice Items</b>
                    <button type="button" class="pull-right" onclick="add_new_row()"><i class="fa fa-plus"></i></button>
                </div>
                <div style="padding-right: 5px; padding-left: 15px">
                    <table class="table" id="inv-tb">
                        <tbody>
                        <tr>
                            <td style="width: 20px"></td>
                            <td><b>Description</b></td>
                            <td style="width: 120px; text-align: center;"><b>Amount</b></td>
                            <td style="width: 120px; text-align: center;"><b>Discount %</b></td>
                            <td style="width: 120px; text-align: center;"><b>Discount</b></td>
                            <td style="width: 120px; text-align: center;"><b>Sub Total</b></td>
                        </tr>
                        <?php
                        $total = 0;
                        if(!empty($det_data)){
                            foreach ($det_data as $row){
                                $amount = $amountDis =  ($view_type == 'E')? round($row['amount'], $dPlace): round($row['amountBeforeDis'], $dPlace);
                                $amount -= ($view_type == 'E')? 0: $row['discountAmount'];
                                $total += $amount;

                                $des_txt = $row['itemDescription'];

                                if($view_type == 'E'){
                                    $des_txt = '<input type="text" name="description[]" class="form-control form-text" value="'.$row['description'].'">';

                                    $amount_txt = '<input type="text" name="amount[]" class="form-control amount number form-text" onchange="clear_discounts(this)" value="">';
                                    $dis_per_txt = '<input type="text" name="discountPer[]" class="form-control discountPer number form-text" onkeyup="calculate_discount(this)" value="">';
                                    $dis_txt = '<input type="text" name="discountAmount[]" class="form-control discountAmount number form-text" onkeyup="calculate_discount(this)" value="">';
                                }
                                else{
                                    $amount_txt = number_format($amountDis, $dPlace);
                                    $dis_per_txt = $row['discountPer'];
                                    $dis_per_txt = '<div style="text-align: right">'.$dis_per_txt.'</div>';
                                    $dis_txt = number_format($row['discountAmount'], $dPlace);
                                    $dis_txt = '<div style="text-align: right">'.$dis_txt.'</div>';
                                }

                                echo '<tr>    
                                     <td style="width: 20px"></td>
                                     <td>'.$des_txt.'</td>
                                     <td style="width: 120px; text-align: right; vertical-align: middle">'.$amount_txt.'</td>
                                     <td style="vertical-align: middle">'.$dis_per_txt.'</td>
                                     <td style="vertical-align: middle">'.$dis_txt.'</td>
                                     <td style="width: 120px; text-align: right; vertical-align: middle" class="sub-total">'.number_format($amount, $dPlace).'</td>
                                  </tr>';
                            }
                        }
                        else{ ?>
                            <tr>
                                <td style="width: 20px"></td>
                                <td>
                                    <input type="text" name="description[]" class="form-control form-text" value="">
                                </td>
                                <td style="width: 120px; text-align: right; vertical-align: middle">
                                    <input type="text" name="amount[]" class="form-control amount number form-text" onchange="clear_discounts(this)" value="">
                                </td>
                                <td style="vertical-align: middle">
                                    <input type="text" name="discountPer[]" class="form-control discountPer number form-text" onkeyup="calculate_discount(this)" value="">
                                </td>
                                <td style="vertical-align: middle">
                                    <input type="text" name="discountAmount[]" class="form-control discountAmount number form-text" onkeyup="calculate_discount(this)" value="">
                                </td>
                                <td style="width: 120px; text-align: right; vertical-align: middle" class="sub-total"><?=number_format(0, $dPlace)?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="text-align: right; background: #fafafb" colspan="5"><b>Total ( <?=$cur_code?> )</b></td>
                            <td style="text-align: right; background: #fafafb" ><span id="total-td"><?=number_format($total, $dPlace)?></span></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <br/>
            </div>
        </div>
    </div>

    <?php if($view_type != 'E'){?>
    <div class="col-md-6">
        <div class="separation">
            &nbsp; <span style="font-weight: bold">Attachments</span>
            <br/>

            <div style="padding: 10px 15px;">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>File Name</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                    </thead>

                    <tbody id="subscription_attachment_padding" class="no-padding">
                        <?=$att_view?>
                    </tbody>
                </table>
            </div>
        </div>

        <br/>

        <div class="separation">
            <?php if($mas_data['paymentType'] == 0){ ?>
                <div class="alert alert-warning" style="color: #8a6d3b !important; background-color: #fcf8e3 !important; border-color: #faebcc; margin: 10px;">
                    Payment not received
                </div>
            <?php } else{ ?>
                <div class="form-group" style="margin-bottom: 8px;">
                    <label class="pay-input" style="margin-top: 3px;">&nbsp; Payment Type : </label>
                    <?php
                        $type = '';
                        switch ($mas_data['paymentType']){
                            case 1: $type = 'Bank Transfer'; break;
                            case 2: $type = 'Pay Pal'; break;
                            case 3: $type = 'Manually Updated'; break;
                            default : $type = '-';
                        }
                    ?>
                    <label style="margin-top: 3px; font-weight: normal"><?=$type?></label>
                </div>
                <div class="form-group">
                    <label class="pay-input" style="">&nbsp; Payment Received Date : </label>
                    <label style="font-weight: normal">&nbsp;<?=date('F dS Y', strtotime($mas_data['payRecDate']))?> </label>
                </div>
            <?php } ?>
        </div>

        <br/>

        <div class="separation">
            &nbsp; <span style="font-weight: bold">Payment details</span>

            <div style="padding: 10px 15px;">
                <table class="<?=table_class()?>">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Narration</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>
                            <button type="button" onclick="open_paymentDet_modal('<?=$inv_id?>', '<?=$mas_data['invNo']?>')" title="Add payment details">
                                <i class="fa fa-plus"></i>
                            </button>
                        </th>
                    </tr>
                    </thead>

                    <tbody id="pay-det-body">
                    <?=$paymentDet?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<input type="hidden" id="company_name_txt" value="<?=$mas_data['company_name']?>"/>

<script>
    let company_name_txt = $('#company_name_txt').val();
    let dPlace = Number($('#dPlace').val());
    let sub_tot = 0;
    sub_tot = sub_tot.toFixed(dPlace);

    $('#inv_date, #due_date').datepicker({
        format: "yyyy-mm-dd",
        viewMode: "months",
        minViewMode: "days"
    }).on('changeDate', function (ev) {
        $(this).datepicker('hide');

        if($(this).attr('id') == 'due_date'){
            update_dueDate();
        }
    });

    $('.discountPer').numeric({decimalPlaces:2, negative:false});
    $('.amount,.discountAmount').numeric({decimalPlaces:dPlace, negative:false});


    function calculate_discount(obj) {
        let amount = Number($(obj).closest('tr').find('.amount').val());

        let objType = ($(obj).hasClass('discountPer'))? 'discountPer': 'discountAmount';
        let otherObj = (objType === 'discountAmount')? 'discountPer': 'discountAmount';
        otherObj = $(obj).closest('tr').find('.'+otherObj);
        let thisVal = $(obj).val();

        thisVal = Number(thisVal);
        let finAmount = amount;
        $(otherObj).val('');
        if(thisVal === 0){
            $(obj).closest('tr').find('.sub-total').text(finAmount.toFixed(dPlace));
            calculate_tot();
            return false;
        }

        if(objType === 'discountPer'){
            if(thisVal > 100){
                myAlert('w', 'Discount percentage can not be greater than 100');
                $(obj).val('');
                thisVal = 0;
            }
            finAmount = ((thisVal/100) * amount);
            $(otherObj).val(finAmount.toFixed(dPlace));
            finAmount = amount - finAmount;
        }
        else{
            if(thisVal > finAmount){
                finAmount = finAmount.toFixed(dPlace);
                myAlert('w', 'Discount amount can not be greater than '+finAmount);
                $(obj).val('');
                thisVal = 0;
            }
            finAmount = (thisVal/amount) * 100;
            $(otherObj).val(finAmount.toFixed(2));
            finAmount = amount - thisVal;
        }

        $(obj).closest('tr').find('.sub-total').text(finAmount.toFixed(dPlace));

        calculate_tot();
    }

    function clear_discounts(obj){
        $(obj).closest('tr').find('.discountAmount').val('');
        $(obj).closest('tr').find('.discountPer').val('');
        calculate_tot();
    }

    function calculate_tot(){
        let tot = 0;
        $('.sub-total').each(function(i, itm){

            tot += Number($(itm).text());
        });
        $('#total-td').text(tot.toFixed(dPlace));
    }

    function update_dueDate(){
        <?php if($view_type == 'V' and ($mas_data['isAmountPaid'] == 0)){ ?>
            bootbox.confirm({
                title: '<strong>Confirmation!</strong>',
                message: 'Do you want to change the invoice due date?<br/>',
                buttons: {
                    'cancel': {
                        label: 'Cancel',
                        className: 'btn-default pull-right'
                    },
                    'confirm': {
                        label: 'Yes Proceed',
                        className: 'btn-primary pull-right bootBox-btn-margin'
                    }
                },
                callback: function(result) {
                    if (result) {
                        let due_date = $('#due_date').val();

                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'due_date': due_date, 'sub_id': '<?=$mas_data['sub_id']?>', 'inv_id': '<?=$inv_id?>'},
                            url: "<?php echo site_url('Dashboard/update_invoice_dueDate'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                myAlert(data[0], data[1]);
                                if(data[0] == 's'){
                                    fetch_subscription_history(data['company_id']);
                                    subscription_tb.ajax.reload();
                                }
                                stopLoad();
                            }, error: function () {
                                alert('An Error Occurred! Please Try Again.');
                                stopLoad();
                            }
                        });
                    }
                }
            });
        <?php } ?>
    }

    function open_paymentDet_modal(invID, invNo){
        $('#paymentDet_frm')[0].reset();
        $('#paymentDet_companyName').text( company_name_txt );
        $('#pay_company_id').val('<?=$company_id?>');
        $('#pay_inv_id').val(invID);
        $('#pay_invNo').val(invNo);
        $('#paymentDet_modal').modal('show');
    }

    function add_new_row() {
        let str = '<tr><td style="width: 20px"><span onclick="delete_row(this)" ><i class="fa fa-trash delete-icon" ></i></span></td>';
        str += '<td><input type="text" name="description[]" class="form-control form-text" value=""></td>';
        str += '<td style="width: 120px; text-align: right; vertical-align: middle">';
        str += '<input type="text" name="amount[]" class="form-control amount number form-text" onchange="clear_discounts(this, )" value=""></td>';
        str += '<td style="vertical-align: middle">';
        str += '<input type="text" name="discountPer[]" class="form-control discountPer number form-text" onkeyup="calculate_discount(this)" value=""></td>';
        str += '<td style="vertical-align: middle">';
        str += '<input type="text" name="discountAmount[]" class="form-control discountAmount number form-text" onkeyup="calculate_discount(this)" value=""></td>';
        str += '<td style="width: 120px; text-align: right; vertical-align: middle" class="sub-total">'+sub_tot+'</td></tr>';


        $('#inv-tb').append(str);

        $('.discountPer').numeric({decimalPlaces:2, negative:false});
        $('.amount,.discountAmount').numeric({decimalPlaces:dPlace, negative:false});
    }


    function delete_row(obj){
        $(obj).closest('tr').remove();
        calculate_tot();
    }
</script>
<?php
/**
 * Created by PhpStorm.
 * User: Nasik
 * Date: 5/23/2019
 * Time: 1:04 PM
 */
