<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<button type="button" class="btn btn-primary btn-xs pull-right" onclick="opencurrencyModal()"><i
        class="fa fa-plus"></i> <?php echo $this->lang->line('treasury_tr_ce_add_new_currency');?><!--Add New Currency-->
</button>
<i class="fa fa-filter"></i> <span style=""><?php echo $this->lang->line('treasury_tr_ce_base_currency');?><!--Base Currency--></span>
<table class="<?php echo table_class();?>" style="margin-top: 5px">
    <thead>
    <tr>
        <th><?php echo $this->lang->line('treasury_tr_ce_currency_name');?><!--Currency Name--></th>
        <th><?php echo $this->lang->line('treasury_tr_ce_currency_code');?><!--Currency Code--></th>
        <th><?php echo $this->lang->line('treasury_tr_ce_decimal_place');?><!--Decimal Place--></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if($details){
        foreach($details as $value){
        ?>
    <tr>
        <td style="text-align: center"><?php echo $value['CurrencyName'] ?></td>
        <td style="text-align: center"><?php echo $value['CurrencyCode'] ?></td>
        <td style="text-align: right"><?php echo $value['DecimalPlaces'] ?></td>
        <td class="pull-right">
            <?php if($value['company_reporting_currencyID']==$value['currencyID']){
                ?>
                <button class="btn btn-primary btn-xs" onclick="detailcurrency(<?php echo $value['currencyassignAutoID'] ?>,1)"><?php echo $this->lang->line('treasury_tr_ce_set_conversion');?><!--Set conversion--></button>
                <?php
            }else{
                ?>
                <button class="btn btn-default btn-xs" onclick="detailcurrency(<?php echo $value['currencyassignAutoID'] ?>,0)"><i class="fa fa-eye"></i></button>
                <?php
            }?>

        </td>
    </tr>
    <?php }} ?>
    </tbody>
</table>







<div class="modal fade" id="currencyModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('treasury_tr_ce_add_currency');?><!--Add Currency--> <span id=""></span></h4></div>
            <?php echo form_open('', 'role="form" id="currency_exchange_addCurrency"'); ?>
            <div class="modal-body" id="">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="pwd"><?php echo $this->lang->line('common_currency');?><!--Currency--></label>
                            <div class="col-sm-5">
                                <span id="clearform"><?php echo form_dropdown('currencyID', dropdown_currencyAssigned(), '', 'class="form-control select2   id="currencyID" required"'); ?></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save--></button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="AddNewcurrencyModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content" id="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_update_add_new');?><!--Add New--> <span id=""></span></h4></div>
            <?php echo form_open('', 'role="form" id="currency_exchange_newCurrency"'); ?>
            <input type="hidden" id="mastercurrencyassignAutoID" name="mastercurrencyassignAutoID">
            <div class="modal-body" id="">
                <div class="row">
                    <div class="col-md-12">
                   <div class="form-group">
                    <label class="control-label col-sm-3" for="pwd"><?php echo $this->lang->line('common_currency');?><!--Currency--></label>
                    <div class="col-sm-4">
                        <span id="clearform"><?php echo form_dropdown('currency', dropdown_currencyAssignedExchangeDropdown(), '', 'class="form-control" style="width:300px" id="currency" required"'); ?></span>
                    </div>
                </div>
                        </div>
                    <div class="col-md-12" style="margin-top: 5px">
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="pwd"><?php echo $this->lang->line('treasury_common_exchange_rate');?><!--Exchange Rate--></label>
                        <div class="col-sm-4">
                            <input style="text-align: right" type="text" class="form-control" name="conversion" id="conversion"  >
                        </div>
                    </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnnewSave"><?php echo $this->lang->line('common_save');?><!--Save--></button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#currency').select2();
    $("#conversion").keydown(function (event) {
        if (event.shiftKey == true) {
            event.preventDefault();
        }
        if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190 || event.keyCode == 110) {
        } else {
            event.preventDefault();
        }
        if($(this).val().indexOf('.') !== -1 && (event.keyCode == 190 || event.keyCode == 110))
            event.preventDefault();
    });

    $('.select2').select2();

    $('#currency_exchange_addCurrency').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        //feedbackIcons   : { valid: 'glyphicon glyphicon-ok',invalid: 'glyphicon glyphicon-remove',validating: 'glyphicon glyphicon-refresh' },
        excluded: [':disabled'],
        fields: {


        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Bank_rec/save_currencyAssign'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                $form.bootstrapValidator('resetForm', true);




                $('#currencyModal').modal('hide');
                setTimeout(function(){    assignedcurrency_company() }, 300);





            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    $('#currency_exchange_newCurrency').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        //feedbackIcons   : { valid: 'glyphicon glyphicon-ok',invalid: 'glyphicon glyphicon-remove',validating: 'glyphicon glyphicon-refresh' },
        excluded: [':disabled'],
        fields: {
            currency             : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
            conversion             : {validators : {notEmpty:{message:'<?php echo $this->lang->line('treasury_common_exchange_rate_is_required');?>.'}}},/*Exchange Rate is required*/

        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Bank_rec/save_addNewcurrencyExchange'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                $form.bootstrapValidator('resetForm', true);



$('#conversion').val('');
                $('#AddNewcurrencyModal').modal('hide');
                setTimeout(function(){    detailcurrency($('#mastercurrencyassignAutoID').val())() }, 300);





            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

</script>




