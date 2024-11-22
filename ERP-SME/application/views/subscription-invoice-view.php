<?php
$mas_data = $inv_data['mas_data'];
$det_data = $inv_data['det_data'];

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
                              <input type="hidden" name="sub_id" value="'.$mas_data['sub_id'].'" >';
                    }
                    else{
                        echo date('l, F dS, Y ', strtotime($inv_date));
                    }

                    ?>
                </div>

                <?php if($det_data[0]['itemID'] == 1) { ?>
                <div class="col-sm-6 ">
                    <div class="pull-right">
                        <span style="font-weight: bold">Due Date</span><br/>
                        <?php
                        $due_date = date('Y-m-d', strtotime($mas_data['dueDate']));
                        if ($view_type == 'E' or ($mas_data['isAmountPaid'] == 0)) {
                            echo '<div style="width: 130px;">
                                <div class="input-group" id="">
                                    <div class="input-group-addon" style="padding: 3px 10px;"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                                    <input type="text" class="form-control date-input" id="due_date" value="' . $due_date . '" name="due_date">
                                </div>
                              </div>';
                        } else {
                            echo date('l, F dS, Y ', strtotime($due_date));
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>
            </div>

            <br/>
            <div class="panel panel-default">
                <div class="panel-heading"><b>Invoice Items</b></div>
                <div style="padding-right: 5px; padding-left: 15px">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td><b>Description</b></td>
                            <td style="width: 120px; text-align: center;"><b>Amount</b></td>
                            <td style="width: 120px; text-align: center;"><b>Discount %</b></td>
                            <td style="width: 120px; text-align: center;"><b>Discount</b></td>
                            <td style="width: 120px; text-align: center;"><b>Sub Total</b></td>
                        </tr>
                        <?php
                        $total = 0;
                        foreach ($det_data as $row){
                            $amount = $amountDis =  ($view_type == 'E')? round($row['amount'], $dPlace): round($row['amountBeforeDis'], $dPlace);
                            $amount -= ($view_type == 'E')? 0: $row['discountAmount'];
                            $total += $amount;

                            $des_txt = $row['itemDescription'];

                            if($view_type == 'E'){
                                $des_txt = '<input type="text" name="inv_det_des" class="form-control form-text" value="'.$row['description'].'">';
                                $des_txt .= '<input type="hidden" name="itemID" value="'.$row['itemID'].'">';

                                $dis_per_txt = '<input type="text" name="discountPer" id="discountPer" class="form-control number form-text" onkeyup="calculate_discount(this)" value="">';
                                $dis_txt = '<input type="text" name="discountAmount" id="discountAmount" class="form-control number form-text" onkeyup="calculate_discount(this)" value="">';
                            }
                            else{
                                $dis_per_txt = $row['discountPer'];
                                $dis_per_txt = '<div style="text-align: right">'.$dis_per_txt.'</div>';
                                $dis_txt = number_format($row['discountAmount'], $dPlace);
                                $dis_txt = '<div style="text-align: right">'.$dis_txt.'</div>';
                            }

                            echo '<tr>    
                                     <td>'.$des_txt.'</td>
                                     <td style="width: 120px; text-align: right; vertical-align: middle">
                                         '.number_format($amountDis, $dPlace).'
                                         <input type="hidden" id="hidden_amount" value="'.$amountDis.'">                                      
                                         <input type="hidden" id="dPlace" value="'.$dPlace.'">                                      
                                     </td>
                                     <td style="vertical-align: middle">'.$dis_per_txt.'</td>
                                     <td style="vertical-align: middle">'.$dis_txt.'</td>
                                     <td style="width: 120px; text-align: right; vertical-align: middle" id="sub-total">'.number_format($amount, $dPlace).'</td>
                                  </tr>';
                        }
                        ?>
                        <tr>
                            <td style="text-align: right; background: #fafafb" colspan="4"><b>Total ( <?=$cur_code?> )</b></td>
                            <td style="text-align: right; background: #fafafb" ><span id="total-td"><?=number_format($total, $dPlace)?></span></td>
                        </tr>
                        </tbody>
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
                            case 4: $type = 'Credit Card'; break;
                            case 5: $type = 'Debit Card'; break;
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
                        <?php if(!$is_view_only){ ?>
                        <th>                            
                            <button type="button" onclick="open_paymentDet_modal('<?=$inv_id?>', '<?=$mas_data['invNo']?>')" title="Add payment details">
                                <i class="fa fa-plus"></i>
                            </button>
                        </th>
                        <?php } ?>
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


    $('#discountPer').numeric({decimalPlaces:2, negative:false});
    $('#discountAmount').numeric({decimalPlaces:dPlace, negative:false});

    function calculate_discount(obj) {
        let amount = Number($('#hidden_amount').val());
        let dPlace = Number($('#dPlace').val());

        let thisObj = $(obj).attr('id');
        let otherObj = (thisObj === 'discountPer')? 'discountAmount': 'discountPer';
        let thisVal = $(obj).val();

        thisVal = Number(thisVal);
        let finAmount = amount;
        $('#'+otherObj).val('');
        if(thisVal === 0){
            $('#sub-total, #total-td').text(finAmount.toFixed(dPlace));
            return false;
        }


        if(thisObj === 'discountPer'){
            if(thisVal > 100){
                myAlert('w', 'Discount percentage can not be greater than 100');
                $(obj).val('');
                thisVal = 0;
            }
            finAmount = ((thisVal/100) * amount);
            $('#'+otherObj).val(finAmount.toFixed(dPlace));
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
            $('#'+otherObj).val(finAmount.toFixed(2));
            finAmount = amount - thisVal;
        }

        $('#sub-total, #total-td').text(finAmount.toFixed(dPlace));
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
</script>
<?php
/**
 * Created by PhpStorm.
 * User: Nasik
 * Date: 5/23/2019
 * Time: 1:04 PM
 */
