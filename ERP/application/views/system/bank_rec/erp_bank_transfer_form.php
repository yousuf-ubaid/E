<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$chequeRegister = getPolicyValues('CRE', 'All');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$financeyear_arr = all_financeyear_drop(true);
$financeyearperiodYN = getPolicyValues('FPC', 'All');
?>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title" id="bankTransferHead"><?php if($_POST['bankTransferAutoID'] ?? '' == ''){ echo $this->lang->line('treasury_create_bank_transaction') /*Create Bank Transaction*/ ;}else{echo $this->lang->line('treasury_create_bank_transaction') /*Edit Bank Transaction*/;} ?> <span id=""></span></h4></div>
<?php echo form_open('','role="form" id="bank_transaction_form"'); ?>
<div class="modal-body" id="">
<input type="hidden" id="decimal" name="decimal" value="2">
<input type="hidden" id="fromBankCurrencyID" value="<?php echo $master['fromBankCurrencyID'] ?? null ?>" name="fromBankCurrencyID">
<input type="hidden" id="toBankCurrencyID" value="<?php echo $master['toBankCurrencyID'] ?? null ?>" name="toBankCurrencyID">
<input type="hidden" id="toBankCurrencyCode" value="<?php echo $master['tocurrency'] ?? null ?>" name="toBankCurrencyCode">
<input type="hidden" id="fromBankCurrencyCode" value="<?php echo $master['fromcurrency'] ?? null ?>" name="fromBankCurrencyCode">
<input type="hidden" id="bankTransferAutoID" name="bankTransferAutoID" value="<?php echo $bankTransferAutoID; ?>">
<div class="row">
    <div class="form-group col-sm-4"><label for=""><?php echo $this->lang->line('common_document_date');?><!--Document Date--></label>
        <div class="input-group">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <input type="text" name="transferedDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo (!empty($master)) ? $master['transferedDate']: $current_date; ?>" id="transferedDate"
                   class="form-control" required>
            </div>
    </div>
    <?php if($financeyearperiodYN==1){ ?>
        <div class="form-group col-sm-4">
            <label for="financeyear"><?php echo $this->lang->line('treasury_common_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
            <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" onchange="fetch_finance_year_period(this.value)"'); ?>
        </div>
        <div class="form-group col-sm-4"><label for="financeyear"><?php echo $this->lang->line('treasury_common_financial_period');?><!--Financial Period--> <?php required_mark(); ?></label>
            <?php echo form_dropdown('financeyear_period', array('' => 'Select Finance Period'), '', 'class="form-control" id="financeyear_period"'); ?>
        </div>
    <?php } ?>

</div>
<div class="row">
    <div class="form-group col-sm-4"><label for="description"><?php echo $this->lang->line('treasury_common_reference_no');?><!--Reference No--></label> <textarea class="form-control" id="referenceNo" name="referenceNo" rows="1"><?php echo $master['referenceNo'] ?? '' ?></textarea></div>
    <div class="form-group col-sm-4"><label for="description"><?php echo $this->lang->line('common_narration');?><!--Narration--></label> <textarea class="form-control" id="description" name="description" rows="1"><?php echo $master['narration'] ?? '' ?></textarea></div>
</div>

<legend style="font-size: 14px"><?php echo $this->lang->line('common_from');?><!--From--></legend>
<div class="row">
    <div class="form-group col-sm-4"><label for=""><?php echo $this->lang->line('treasury_common_bank_account');?><!--Bank Account --></label> <?php echo form_dropdown('bankFrom', company_bank_account_drop(), $master['fromBankGLAutoID'] ?? null, 'class="form-control select2" onchange="fetch_cheque_number(this.value)" id="bankFrom" required"'); ?>
    </div>
    <div class="col-sm-3">
        <div class="form-group"><label for="creditPeriod"><?php echo $this->lang->line('common_amount');?><!--Amount--></label>
            <div class="input-group">
                <div class="input-group-addon"><span id="fromcurrency"><?php echo $master['fromcurrency'] ?? null ?></span></div>
                <input type="text" min="0" style="text-align: right" class="form-control number" id="fromAmount" value="<?php echo $master['transferedAmount'] ?? null ?>" onkeypress="return validateFloatKeyPress(this,event)" onchange="gettransferedAmount(this.value)" name="fromAmount" placeholder="00"></div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group"><label for="creditPeriod"><?php echo $this->lang->line('treasury_common_exchange_rate');?><!--Exchange Rate--></label>
            <div class="input-group"><input type="text" onchange="cal_exchangeAmount(this.value)" style="text-align: right"  class="form-control" value="<?php echo $master['exchangeRate'] ?? null ?>" id="conversion" name="conversion" placeholder="00"></div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group"><label for="creditPeriod"><?php echo $this->lang->line('treasury_ap_br_un_book_balance');?><!--Book Balance--></label>
            <div class="input-group"><input type="text" style="text-align: right" readonly value="<?php echo $master['fromBankCurrentBalance'] ?? null ?>" class="form-control" id="fromBankCurrentBalance" name="fromBankCurrentBalance" placeholder="00">
            </div>
        </div>
    </div>
</div>
<div class="row paymentType">
    <div class="form-group col-sm-4">
        <label for="">
            Type <?php required_mark(); ?>
        </label>
        <select name="transferType" class="form-control" onchange="showChequeDetails()" id="transferType" tabindex="-1">
            <?php if($master['transferType'] ?? '' == 1){echo 'Selected';} ?>
            <option value="" <?php if($master['transferType'] ?? '' ==''){echo 'Selected';} ?>>Select Transfer Type</option>
            <option value="1" <?php if($master['transferType'] ?? '' ==1){echo 'Selected';} ?>>Bank Transfer Letter</option>
            <option value="2" <?php if($master['transferType'] ?? '' ==2){echo 'Selected';} ?>>Cheque</option>
            <option value="3" <?php if($master['transferType'] ?? '' ==3){echo 'Selected';} ?>>ATM</option>
            <option value="4" <?php if($master['transferType'] ?? '' ==4){echo 'Selected';} ?>>Online Transfer</option>
        </select>
    </div>
</div>
<div class="row hidden chequerow">

    <?php if($chequeRegister==1){ ?>
        <div class="form-group col-sm-4">
            <label for="description">
                Cheque No <?php required_mark(); ?>
            </label>
            <?php echo form_dropdown('chequeRegisterDetailID', $cheque, $master['chequeRegisterDetailID'] ?? null, 'class="form-control" id="chequeRegisterDetailID"'); ?>
        </div>
    <?php }else{ ?>
        <div class="form-group col-sm-4">
            <label for="description">
                Cheque No <?php required_mark(); ?>
            </label>
            <input type="text" class="form-control" id="chequeNo" value="<?php echo $master['chequeNo'] ?? null ?>"  name="chequeNo">
        </div>
    <?php } ?>
    <div class="form-group col-sm-4">
        <label for="">
            Cheque Date <?php required_mark(); ?>
        </label>
        <div class="input-group datepic">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
            <input type="text" name="chequeDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo (!empty($master)) ? $master['chequeDate']: $current_date; ?>" id="chequeDate"
                   class="form-control">
        </div>
    </div>
    <div class="form-group col-sm-4">
        <label for="description">
            Name On Cheque <?php required_mark(); ?>
        </label>
        <input type="text" class="form-control" id="nameOnCheque" value="<?php echo $master['nameOnCheque'] ?? null ?>"  name="nameOnCheque">
    </div>
</div>
<div class="row chequerow">
    <?php
    $checked='';
    if(is_array($master) && $master['accountPayeeOnly']==1){
        $checked= 'checked';
    }
    ?>
    <div class="form-group col-sm-1" style="padding-right: 0px;">
        <label class="title">Payee Only
    </div>
    <div class="form-group col-sm-1" style="padding-left: 0px;">
        <div class="col-sm-1">
            <div class="skin skin-square">
                <div class="skin-section extraColumns"><input id="accountPayeeOnly" type="checkbox"
                                                              data-caption="" class="columnSelected"
                                                              name="accountPayeeOnly" value="1" <?php echo $checked ?>><label
                        for="checkbox">&nbsp;</label></div>
            </div>
        </div>
    </div>
</div>
<legend style="font-size: 14px"><?php echo $this->lang->line('common_to');?><!--To--></legend>
<div class="row">
    <div class="form-group col-sm-4"><label for=""><?php echo $this->lang->line('treasury_common_bank_account');?><!--Bank Account--> </label> <?php echo form_dropdown('bankTo', company_bank_account_drop(), $master['toBankGLAutoID'] ?? null, 'class="form-control select2" onchange="bankchange()" id="bankTo" required"'); ?>
    </div>
    <div class="col-sm-3">
        <div class="form-group"><label for="creditPeriod"><?php echo $this->lang->line('common_amount');?><!--Amount--></label>
            <div class="input-group">
                <div class="input-group-addon"><span id="tocurrency"><?php echo $master['tocurrency'] ?? null ?></span></div> <input value="<?php echo $master['toBankCurrencyAmount'] ?? null?>" type="text" style="text-align: right" class="form-control" readonly id="toAmount" name="toAmount" placeholder="00"></div>
        </div>
    </div>
</div>
    </div>
    <div class="modal-footer">
    <!--    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>-->
        <?php if(empty($master) || $master['confirmedYN']!=1){?>

        <button type="submit" class="btn btn-primary btn-sm" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save--></button>
        <button  type="button" class="btn hide btn-success btn-sm" onclick="confirmation()" id="btnconfim"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
        <?php }else{
            if($master['confirmedYN']==1){
                $msg='Confirmed &';
            }
            if($master['approvedYN'] !==1){
                $msg=' Approved &';

            }
            echo "<span style='color: darkgreen'>".substr($msg, 0, -1)."</span>";
        } ?>
    </div>
    </form>

<script>
    showChequeDetails();
    $('.extraColumns input').iCheck({
        checkboxClass: 'icheckbox_square_relative-blue',
        radioClass: 'iradio_square_relative-blue',
        increaseArea: '20%'
    });
    <?php
    if(empty($master)){ ?>
    FinanceYearID       = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
    DateFrom            = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
    DateTo              = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
    periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
    currency_decimal= 0 ;

    <?php }else{?>
    FinanceYearID       = <?php echo json_encode(trim($master['companyFinanceYearID'] ?? '')); ?>;
    DateFrom            = <?php echo json_encode(trim($master['FYPeriodDateFrom'] ?? '')); ?>;
    DateTo              = <?php echo json_encode(trim($master['FYPeriodDateTo'] ?? '')); ?>;
    periodID = <?php echo json_encode(trim($master['companyFinancePeriodID'] ?? '')); ?>;
    $('#financeyear').val(FinanceYearID);
    $('#btnconfim').removeClass('hide');
    currency_decimal = '<?php echo $master['fromDecimalPlaces'] ?? 0 ?>';
    <?php } ?>

    fetch_finance_year_period(FinanceYearID,periodID);
    $('.select2').select2();
    Inputmask().mask(document.querySelectorAll("input"));

    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {
        $('#bank_transaction_form').bootstrapValidator('revalidateField', 'chequeDate');
    });

    $('#bank_transaction_form').bootstrapValidator({
        live            : 'enabled',
        message         : '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded        : [':disabled'],
        fields          : {

            description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}},/*Description is required*/
            //financeyear: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_final_year_is_required');?>.'}}},/*Financial year is required*/
            //financeyear_period: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_financial_period_is_required');?>.'}}},/*Financial period is required*/
            bankFrom: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_from_bank_is_required');?>.'}}},/*From bank is required*/
            bankTo: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_to_bank_is_required');?>.'}}},/*To bank is required*/
            fromAmount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_from_amount_is_required');?>.'}}},/*From amount is required*/
            toAmount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('treasury_common_to_amount_is_required');?>.'}}},/*To amount is required*/
            // transferType: {validators: {notEmpty: {message: 'Type is required.'}}}
        }
    }).on('success.form.bv', function(e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name' : 'companyFinanceYear', 'value' : $('#financeyear option:selected').text()});
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : data,
            url :"<?php echo site_url('Bank_rec/save_bank_transaction'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){

                $('#btnSave').prop("disabled", false);
                refreshNotifications(true);
                stopLoad();
                /*       $('#bankrecModal').modal('hide');*/
                if (data['status']) {

                   /* $form.bootstrapValidator('resetForm', true);*/
                    bank_rec();
                    $('#btnconfim').removeClass('hide');
                    $('#bankTransferAutoID').val(data['masterID']);
                   // $('#bankTransactionModal').modal('hide');
                }
            },error : function(){
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    })

    function cal_exchangeAmount(exchangeAmount){
        if(exchangeAmount =='' ){
            $('#conversion').val(0);
        }
        gettransferedAmount($('#fromAmount').val());


    }

    function confirmationx(){
        if ($('#bankTransferAutoID').val() !=0) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document !*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async : true,
                        type : 'post',
                        dataType : 'json',
                        data : {'bankTransferAutoID':$('#bankTransferAutoID').val()},
                        url  :"<?php echo site_url('Bank_rec/bank_transfer_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            refreshNotifications(true);
                            stopLoad();
                            bank_rec();
                            $('#bankTransactionModal').modal('hide');

                        },error : function(){
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        };
    }

    function confirmation(){
        if ($('#bankTransferAutoID').val() !=0) {

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document !*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {

                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: $('#bank_transaction_form').serializeArray(),
                        url: "<?php echo site_url('Bank_rec/bank_transfer_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();

                            bank_rec();
                            $('#bankTransactionModal').modal('hide');

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

    }

    function showChequeDetails(){
        var transferType = $('#transferType').val();
        if(transferType==2){
            $('.chequerow').removeClass('hidden');
        }else{
            $('.chequerow').addClass('hidden');
        }
    }

    function fetch_cheque_number(GLAutoID) {
        var bankTransferAutoID = $('#bankTransferAutoID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'GLAutoID': GLAutoID,'bankTransferAutoID': bankTransferAutoID},
            url: "<?php echo site_url('Chart_of_acconts/fetch_cheque_number'); ?>",
            success: function (data) {
               /*  if (data) {
                    if (!jQuery.isEmptyObject(data['bankCheckNumber'])) {
                        $("#chequeNo").val((parseFloat(data['bankCheckNumber']) + 1));
                    }
                } */
                if (data['master']) {
                    if(data['master']['bankCheckNumber']!='NaN')
                    {
                        if(bankTransferAutoID > 0){
                            $("#chequeNo").val((parseFloat(data['master']['bankCheckNumber'])));
                        }else{
                            $("#chequeNo").val((parseFloat(data['master']['bankCheckNumber']) + 1));
                        }
                    }else
                    {
                        $("#chequeNo").val(" ");
                    }

                    if (data['master']['isCash'] == 1) {
                        $('.paymentType').addClass('hide');
                    } else {
                        $('.paymentType').removeClass('hide');
                    }
                }
                if (data['detail']) {
                    $('#chequeRegisterDetailID').empty();
                    var mySelect = $('#chequeRegisterDetailID');
                    mySelect.append($('<option></option>').val('').html('Select Cheque no'));
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        $.each(data['detail'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['chequeRegisterDetailID']).html(text['chequeNo'] + ' - ' + text['description']));
                        });
                    }
                }
                bankchange();
            }
        });
    }
</script>