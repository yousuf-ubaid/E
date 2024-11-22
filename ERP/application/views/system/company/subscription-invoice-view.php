<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('crm', $primaryLanguage);

$invID = $mas_data['invID'];
$dPlace = $mas_data['invDecPlace'];
$cur_code = $mas_data['CurrencyCode'];
$inv_amount = round($mas_data['pay_pal_amount'], $dPlace);

$companyId = current_companyID();
$payment_method = payment_type([1,4,5], 'Select a type');

$disable_str = ($mas_data['isAmountPaid'] != 0)? 'disabled': '';
?>

<style>
    .tab-pane{
        padding: 10px 25px;
    }

    .separation {
        border-left: 3px solid #f7f4f4;
        min-height: 100px;
    }

    #payment_type{
        height: 29px;
        padding: 2px 4px;
        font-size: 12px;
        width: 171px;
    }

    #pay-pal-btn-container{
        width: 100px;
        margin: 5%;
        display: none;
    }

</style>
<div >
    <h3><?=$this->lang->line('common_invoice_no'); ?> #<?=$mas_data['invNo']?> <button type="button" class="close" data-dismiss="modal">&times;</button></h3>
    <hr/>
</div>

<div >
    <div class="row" style="margin-top: 5px">
        <div class="col-md-6">

            <div class="table-responsive">
                <span style="font-weight: bold"><?php echo $this->lang->line('common_invoice_to'); ?></span><br/>
                <?=$mas_data['company_name']?><br/>
                <?=$mas_data['companyPrintAddress']?>

                <br/><br/><span style="font-weight: bold"><?php echo $this->lang->line('common_invoice_date'); ?></span><br/>
                <?=date('l, F dS, Y ', strtotime($mas_data['createdDateTime']));?>

                <br/>
                <div class="panel panel-default">
                    <div class="panel-heading"><b><?php echo $this->lang->line('common_invoice_items'); ?></b></div>
                    <div style="padding-right: 5px; padding-left: 15px">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td><b><?=$this->lang->line('common_details'); ?></b></td>
                                <td style="width: 120px; text-align: center"><b><?=$this->lang->line('common_amount'); ?></b></td>
                                <td style="width: 120px; text-align: center;"><b>Discount %</b></td>
                                <td style="width: 120px; text-align: center;"><b>Discount</b></td>
                                <td style="width: 120px; text-align: center;"><b>Sub Total</b></td>
                            </tr>
                            <?php
                            $total = 0;
                            foreach ($det_data as $row){
                                $total += round($row['amount'], $dPlace);
                                echo '<tr>    
                                 <td>'.$row['itemDescription'].'</td>
                                 <td style="width: 120px; text-align: right">'.number_format($row['amountBeforeDis'], $dPlace).'  </td>
                                 <td style="width: 120px; text-align: right">'.number_format($row['discountPer'], 2).' </td>
                                 <td style="width: 120px; text-align: right">'.number_format($row['discountAmount'], $dPlace).' </td>
                                 <td style="width: 120px; text-align: right">'.number_format($row['amount'], $dPlace).' </td>
                              </tr>';
                            }
                            ?>
                            <tr>
                                <td colspan="4" style="text-align: right; background: #fafafb"><b>Total ( <?=$cur_code?> )</b></td>
                                <td style="text-align: right; background: #fafafb"><?=number_format($total, $dPlace)?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <br/>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="separation">
                &nbsp; <span style="font-weight: bold"><?php echo $this->lang->line('common_attachments'); ?></span>
                <br/>

                <div class="pull-right" style="padding: 10px 15px;">
                    <?php echo form_open_multipart('', 'id="pay_attachment_frm" class="form-inline"'); ?>
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="att_inv_id" name="att_inv_id" value="<?=$invID?>">
                        <input type="text" class="form-control" id="att_description" name="att_description" placeholder="Description...">
                    </div>
                    <div class="form-group">
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput" style="margin-top: 8px;">
                            <div class="form-control" data-trigger="fileinput" style="min-width: 150px">
                                <i class="glyphicon glyphicon-file color fileinput-exists"></i> <span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span>
                                        <span class="fileinput-exists"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span> </span>
                                        <input type="file" name="att_file" id="att_file">
                                    </span>
                            <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id" data-dismiss="fileinput">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-default" onclick="subscription_attachment_upload()">
                        <span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                    </button>
                    <?php echo form_close(); ?>
                </div>

                <br/>

                <div style="padding: 10px 15px;">
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                            <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                            <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                        </tr>
                        </thead>

                        <tbody id="subscription_attachment_padding" class="no-padding">
                        <?php
                        if(empty($att_data)){
                            echo '<tr class="danger"><td colspan="5" class="text-center">'.$this->lang->line('common_no_attachment_found').'</td></tr>';
                        }else{
                            echo $att_data;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <br/>

            <div class="separation" id="payment-det">
                <?php if($inv_amount > 0) { ?>
                <div class="form-group">
                    <label class="pay-input" style="margin-top: 3px;">&nbsp; <?=$this->lang->line('common_payment_type'); ?> : </label>
                    <?php echo form_dropdown('payment_type', $payment_method, $mas_data['paymentType'],
                        'class="pay-input" id="payment_type" onchange="on_payment_type_change()" '.$disable_str); ?>
                    <button class="btn btn-primary btn-sm pay-input" id="sub-pay-btn" type="button" onclick="sub_payment_type_confirm(<?=$invID?>)" <?=$disable_str?>>
                        <i class="fa fa-pay"></i> Pay
                    </button>
                    <input type="hidden" id="inv_amount" value="<?=$inv_amount?>">
                    <input type="hidden" id="inv_cur" value="<?=$cur_code?>">
                </div>

                <div style="margin-top:10px">&nbsp;</div>
            
                <br/>
                
                <div class="form-group" id="pay-pal-btn-container"> </div>
                <?php } ?>

                <div style="padding: 10px 15px;">
                    <table class="<?=table_class()?>">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Narration</th>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>
                        </thead>

                        <tbody><?=$paymentDet?></tbody>
                    </table> 
                </div>      
            </div>
        </div>
    </div>
</div>


<?php
