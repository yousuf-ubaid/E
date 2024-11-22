<!--Translation added by Naseek-->

<?php
$companyBanks = company_bank_account_drop();
$isNonPayroll = $this->input->post('isNonPayroll');
/*echo '<pre>';
print_r($companyBanks);
echo '</pre>';*/
$companyBanks = company_bank_account_drop();
$isNonPayroll = $this->input->post('isNonPayroll');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_payroll', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
$policy = getPolicyValues('BTPV', 'All');
$bankTransferType = getPolicyValues('PBT', 'All');

?>
<style>
    .isCheckRow{
        background: #7b969c !important;
    }
    .transCheck{f
        margin:0px !important;
    }
    #accountID{
        width : 200px !important;
    }
</style>
<input type="hidden" id="BTPVpolicy" value="<?php echo $policy ?>">
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs" style="border-top: 1px solid #f4f4f4;">
        <li class="active"><a href="#pendingTab" id="" class="" data-toggle="tab" aria-expanded="true" data-value="0"><?php echo $this->lang->line('common_pending');?><!--Pending--></a></li>
        <li class=""><a href="#processedTab" id="" class=""  data-toggle="tab" aria-expanded="false" data-value="0"><?php echo $this->lang->line('common_processed');?><!--Processed--></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="pendingTab" > <!-- /.tab-pane -->
            <form name="frm_bankTransfer" id="frm_bankTransfer" class="form-horizontal">
                <div class="row" style="border:0px solid; margin-top: 1%">
                    <div class="col-sm-12">
                        <div class="form-group" style="border:0px solid">
                            <label class="control-label col-sm-2" style="text-align: left" for="accountID"> <?php echo $this->lang->line('hrms_payroll_company_bank');?><!--Company Bank --></label>
                            <div class="col-sm-3">
                                <?php echo form_dropdown('accountID', $companyBanks, '', 'id="accountID" class="form-control select2"'); ?>
                            </div>

                            <div class="clearfix visible-xs">&nbsp;</div>

                            <label class="control-label col-md-2" style="" for="transDate"> <?php echo $this->lang->line('hrms_payroll_transfer_date');?><!--Transfer Date--> </label>
                            <div class="col-sm-2" style="text-align: right">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="transDate" value="<?php echo date('Y-m-d')?>" id="transDate" class="form-control date_picker formInput" required="">
                                </div>
                            </div>

                            <div class="clearfix visible-xs">&nbsp;</div>

                            <div class="col-sm-3 text-right text-left-xs" style="">
                                <!--<button type="button" class="btn btn-primary btn-sm pull-right" style="margin-left: 2%" onclick="selectAll()"> Select All </button>-->
                                <button type="button" class="btn btn-primary btn-sm  bankTransferBtn" style="" onclick="transfer()"> <?php echo $this->lang->line('hrms_payroll_bank_transfer');?><!--Bank Transfer--> </button>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3" style="border:0px solid; float: right">
                        <!--<label class="control-label" style="float: right; margin-right: 10%">
                            SUM : <?php /*echo $this->common_data['company_data']['company_default_currency'].'
                                    <input type="hidden" id="hiddenComDecPlace" value="'.$this->common_data['company_data']['company_default_decimal'].'">';
                            */?>
                            <span id="selectedTot"> 0.00 </span>
                        </label>-->
                        <input type="hidden" name="isNonPayroll" value="<?php echo $isNonPayroll; ?>">
                        <input type="hidden" name="bnkPayrollID" id="bnkPayrollID" value="<?php echo $payrollID ?>">
                    </div>
                </div>

                <div class=" table-responsive">
                    <table class="<?php echo table_class(); ?>"  id="bankTransferTB" style="margin-top: 2%">
                        <thead>
                        <tr>
                            <th style="width: auto"> # </th>
                            <th style="width: auto"> <?php echo $this->lang->line('hrms_payroll_emp_id');?><!--EMP ID--> </th>
                            <th style="width: auto"> <?php echo $this->lang->line('common_name');?><!--Name--> </th>
                            <th style="width: auto"> <?php echo $this->lang->line('hrms_payroll_bank_name');?><!--Bank Name--> </th>
                            <th style="width: auto"> <?php echo $this->lang->line('hrms_payroll_branch_name');?><!--Branch Name--> </th>
                            <th style="width: auto"> <?php echo $this->lang->line('hrms_payroll_swift_code');?><!--Swift Code--> </th>
                            <th style="width: auto"> <?php echo $this->lang->line('hrms_payroll_account_no');?><!--Account No--> </th>
                            <th style="width: auto"> <?php echo $this->lang->line('common_currency');?><!--Currency--> </th>
                            <th style="width: auto"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                            <th style="width: auto">
                                <input type="checkbox" id="allSelect" style="margin: 2px 0px 0px 0px">
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        $lastGroup = null;
                        $currTot = null;
                        if( !empty($bankTransferDet) ) {
                            foreach ($bankTransferDet as $key=>$data) {
                                $bankName = trim($data['bankName'] ?? '');
                                $trCurrency = trim($data['transactionCurrency'] ?? '');
                                $thisGroup = $bankName.'|'.$trCurrency;

                                if( $lastGroup != $thisGroup ){
                                echo '<tr><th colspan="10" style="font-size: 12px"> '.$trCurrency.'&nbsp; | &nbsp;'.$bankName.'</th></tr>';
                                }


                                $checkBox = '<input type="checkbox" name="transCheck[]" class="transCheck" value="' . $data['bankTransferDetailID'] . '"
                                data-local-amount="' . $data['companyLocalAmount'] . '">';
                                echo '<tr>
                                    <td>' . ($key+1) . '</td>
                                    <td>' . $data['ECode'] . '</td>
                                    <td>' . $data['acc_holderName'] . '</td>
                                    <td>' . $bankName . '</td>
                                    <td>' . $data['branchName'] . '</td>
                                    <td>' . $data['swiftCode'] . '</td>
                                    <td>' . $data['accountNo'] . '</td>
                                    <td>' . $trCurrency . '</td>
                                    <td align="right">' . number_format($data['transactionAmount'], $data['transactionCurrencyDecimalPlaces']) . '</td>
                                    <td align="center">' . $checkBox . '</td>
                                 </tr>';

                                $m = $key + 1;
                                $currTot += $data['transactionAmount'];
                                if( array_key_exists( $m , $bankTransferDet) ) {

                                    $nextGroup = trim($bankTransferDet[$m]['bankName']).'|'.trim($bankTransferDet[$m]['transactionCurrency']);

                                    if( $nextGroup != $thisGroup){
                                  $tot  =    $this->lang->line('common_total');
                                        echo '<tr>
                                                <th colspan="8" style="font-size: 12px">'.$tot.'<!--Total--></th>
                                                <th style="text-align:right">' . number_format($currTot, $data['transactionCurrencyDecimalPlaces']) . '</th>
                                                <th></th>
                                               </tr>';

                                        $currTot=0;
                                    }
                                }
                                else{
                                    $tot  =    $this->lang->line('common_total');
                                    echo '<tr>
                                            <th colspan="8" style="font-size: 12px">'.$tot.'<!--Total--></th>
                                            <th style="text-align:right">' . number_format($currTot, $data['transactionCurrencyDecimalPlaces']) . '</th>
                                            <th></th>
                                           </tr>';
                                }
                                $lastGroup = $thisGroup;


                            }
                        }
                        else{
                            $Nodata  =    $this->lang->line('common_no_data_available_in_table');
                            echo '<tr> <td colspan="10">'.$Nodata.'<!--No data available in table--></td> </tr>';
                        }
                        ?>

                        <?php
                        foreach($currencySum as $keyCurr=>$currencySumRow){
                            $grandTitle = ($keyCurr > 0 )? '' : $this->lang->line('common_grand_total')/*'Grand Total'*/;
                            echo'<tr>
                                    <th colspan="7" style="font-size: 12px">'.$grandTitle.'</th>
                                    <th style="font-size: 12px"> '.$currencySumRow['transactionCurrency'].'</th>
                                    <th style="text-align:right">'.$currencySumRow['trAmount'].'</th>
                                    <th></th>
                                </tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

        <div class="tab-pane"  id="processedTab" > <!-- /.tab-pane -->
            <div style="margin-top: 2%">&nbsp;</div>
            <table class="<?php echo table_class(); ?>"  id="bankTransferProcessedTB">
                <thead>
                <tr>
                    <th style="width: auto"> # </th>
                    <th style="width: auto"> <?php echo $this->lang->line('common_code');?><!--Code--> </th>
                    <th style="width: auto"> <?php echo $this->lang->line('common_bank');?><!--Bank--> </th>
                    <th style="width: auto"> <?php echo $this->lang->line('common_branch');?><!--Branch--> </th>
                    <th style="width: auto"> <?php echo $this->lang->line('hrms_payroll_swift_code');?><!--Swift Code--> </th>
                    <th style="width: auto"> <?php echo $this->lang->line('common_account_no');?><!--Account No--> </th>
                    <th style="width: auto"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                    <th style="width: auto">  </th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="bankProcess_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="min-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_bank_process_details');?><!--Bank Process Details--></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="frm_confirmBankTransaction"'); ?>
            <div class="modal-body">
                <div id="bankTransferDetails_div"> </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" id="print_bankTransferBtn" onclick="bankTransfer_print()"><?php echo $this->lang->line('common_print');?><!--Print--></button>
                <button type="button" class="btn btn-primary btn-sm" id="confirm_bankTransferBtn" onclick="confirm_bankTransfer()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <input type="hidden" id="editID" name="editID">
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade" id="empNonBankPay_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('hrms_payroll_bank_process_details');?><!--Bank Process Details--></h4>
            </div>
            <?php echo form_open('','role="form" class="form-horizontal" id="frm_empNonBankPay1"'); ?>
            <div class="modal-body">
                <table class="<?php echo table_class(); ?>"  id="empNoBankTB">
                    <thead>
                    <tr>
                        <th style="width: auto"> # </th>
                        <th style="width: auto"> <?php echo $this->lang->line('hrms_payroll_bank_employee_details');?><!--Employee Details--> </th>
                        <th style="width: auto"> <?php echo $this->lang->line('common_currency');?><!--Currency--> </th>
                        <th style="width: auto"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                        <th style="width: auto"> <?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                        <th style="width: 10%"> <?php echo $this->lang->line('common_action');?><!--Action--> </th>
                    </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="" id=""></td>
                            <td class="" id=""></td>
                            <td class="" id=""></td>
                            <td class="" id=""></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" id="" onclick="save_empNonBankPay()"> <?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"> <?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
            <input type="hidden" id="editID" name="editID">
            <?php echo form_close();?>
        </div>
    </div>
</div>

<div class="modal fade" id="cheque_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select Payment Voucher</h4>
            </div>
            <div class="modal-body">
                <div class="row" id="chequeteplatedrop">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script>
    var bankTransferTB;
    var bankTransferTB_setting;
    var searchVal;
    var totSelectedAmount = 0;
    var bankTransferProcessedTB;

    $(document).ready(function(){
        $('.select2').select2();
        $('.date_picker').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(ev){
            $(this).datepicker('hide');
        });

        fetch_bankTransferDet();


        var isPending = "<?php echo $isPending; ?>";
        if( isPending == 'N'){
            $('.nav-tabs li:eq(1) a').tab('show');
        }

    });

    function restore_bankTransferTB() {
        bankTransferTB_setting[0]._iDisplayLength = 10;
        bankTransferTB.search(searchVal).columns().draw();
    }

    $(document).on('change', '.transCheck', function(){
       if( $(this).prop('checked') == true ) {
           $(this).closest('tr').addClass('isCheckRow');
           totSelectedAmount += parseFloat($(this).attr('data-local-amount'));
       }
       else{
           $(this).closest('tr').removeClass('isCheckRow');
           totSelectedAmount -= parseFloat($(this).attr('data-local-amount'));
       }

       var dPlace = $('#hiddenComDecPlace').val();
       $('#selectedTot').text( commaSeparateNumber(totSelectedAmount, dPlace) );

       //getTotalCheckedValue();

    });

    $('#allSelect').change(function(){
        var transCheck = $('.transCheck');

        if( $(this).prop('checked') == true ) {
            transCheck.closest('tr').addClass('isCheckRow');
            transCheck.prop('checked', true);
        }
        else{
            transCheck.closest('tr').removeClass('isCheckRow');
            transCheck.prop('checked', false);
        }

        transCheck.each(function(){
            if( $(this).prop('checked') == true ) {
                totSelectedAmount += parseFloat($(this).attr('data-local-amount'));
            }
            else{
                totSelectedAmount -= parseFloat($(this).attr('data-local-amount'));
            }
        });

        var dPlace = $('#hiddenComDecPlace').val();
        $('#selectedTot').text( commaSeparateNumber(totSelectedAmount, dPlace) );

        //getTotalCheckedValue();

    });

    function transCheckCount_fn(){
        var count = 0;
        $('.transCheck:checked').each(function(){
            count++;
        });
        return count;
    }

    function getTotalCheckedValue(){
        var tot = 0;
        $('.transCheck:checked').each(function(){
            var thisAmount  = parseFloat($(this).attr('data-local-amount'));
            tot +=  thisAmount;
        });

        var dPlace = $('#hiddenComDecPlace').val();
        $('#selectedTot').text( commaSeparateNumber(tot, dPlace) );
    }

    function transfer(){
        var accountID = $('#accountID').val();
        var transDate = $('#transDate').val();
        var transCheckCount = transCheckCount_fn();
        //var transCheckCount =   ( commaSeparateNumber($('#selectedTot').text()) > 0 ) ? 1 : 0;
        var error = '';

        if( accountID == ''){
            error = 'Bank is required </br>';
        }
        if( transDate == ''){
            error += 'Transfer date is required</br>';
        }
        if( transCheckCount == 0) {
            error += 'Please select at least one employee to proceed</br>';
        }


        if( error == '' ){


            setTimeout( function(){
                var postData = $('#frm_bankTransfer').serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data : postData,
                    url: "<?php echo site_url('Template_paysheet/new_bankTransfer'); ?>",
                    beforeSend: function () {
                        startLoad();

                    },success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if( data[0] == 's' ){
                            setTimeout(function () {
                                $('#bankTransfer').click();
                            }, 300);
                        }
                        else if( data[0] == 'e' ) {
                            //restore_bankTransferTB()
                        }

                    },error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }, 300);

        }
        else{
            myAlert('e', error);
        }
    }

    function fetch_bankTransferDet(){
        bankTransferProcessedTB = $('#bankTransferProcessedTB').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Template_paysheet/fetch_processedBankTransfer').'?id='.$payrollID; ?>",
            "aaSorting": [[1, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
                $(".dataTables_empty").text('<?php echo $this->lang->line('common_no_data_available_in_table'); ?>')
                $(".previous a").text('<?php echo $this->lang->line('common_previous'); ?>')
                $(".next  a").text('<?php echo $this->lang->line('common_next'); ?>')
            },
            "aoColumns": [
                {"mData": "documentCode"},
                {"mData": "documentCode"},
                {"mData": "bankName"},
                {"mData": "branchName"},
                {"mData": "swiftCode"},
                {"mData": "accountNo"},
                {"mData": "amountDetails"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({'name':'isNonPayroll', 'value': '<?php echo $isNonPayroll; ?>'});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function load_bankTransfer(bankTransID){
        $('#bankProcess_modal').modal({backdrop:'static'});

        var bankTransferDetails_div = $('#bankTransferDetails_div');
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {'bankTransID':bankTransID, 'payrollMasterID':'<?php echo $payrollID ?>', 'isNonPayroll':'<?php echo $isNonPayroll; ?>'},
            url: "<?php echo site_url('Template_paysheet/pay_sheetBankTransferDet_load'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if( data[0] == 'e'){
                    myAlert(data[0], data[1]);
                }
                else{
                    bankTransferDetails_div.html('');
                    bankTransferDetails_div.html(data);
                }
            },
            error : function() {
                stopLoad();
                myAlert('e','An Error Occurred! Please Try Again.');
                return 'e';
            }
        });
    }

    function delete_bankTransfer(bankTransID, docCode) {
        swal(
            {
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {'bankTransID' : bankTransID, 'isNonPayroll':'<?php echo $isNonPayroll; ?>'},
                    url: "<?php echo site_url('Template_paysheet/pay_sheetBankTransferDet_delete'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        myAlert(data[0], data[1]);

                        if( data[0] == 's' ){
                            $('#bankTransfer').click();
                            $('.nav-tabs li:eq(1) a').tab('show');
                        }

                    },
                    error : function() {
                        stopLoad();
                        myAlert('e','An Error Occurred! Please Try Again.');
                        return 'e';
                    }
                });
            }
        );
    }

    function excelsheet(tab, single) {
        swal(
            {
                title: "Please select a format",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonColor: "#DD6B55",
                confirmButtonText: "Single",
                cancelButtonText: "Tab",
                closeOnConfirm: true,
                cancelOnConfirm: true,
                allowOutsideClick: true
            },
            function (isConfirm) {
                if(isConfirm){
                    window.location.href = single;
                } else {
                    window.location.href = tab;
                }
            }
        );
    }

    function confirm_bankTransfer(){
        var bankTransID = $('#bankTransID').val();
        var letterDet = $('#letterDet').val();

        if($('#BTPVpolicy').val()==1 || jQuery.isEmptyObject($('#BTPVpolicy').val())){
            swal(
                {
                    title: "Are you sure you want to confirm?",
                    text: "This Confirmation process will generate a Payment voucher",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55 ",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>",
                    closeOnConfirm: true,
                    cancelOnConfirm: true,
                    allowOutsideClick: true
                },
                function (isConfirm) {
                    if(isConfirm){
                        $.ajax({
                            async: false,
                            type: 'post',
                            dataType: 'json',
                            data: {'bankTransID' : bankTransID, 'letterDet' : letterDet, 'isNonPayroll':'<?php echo $isNonPayroll; ?>'},
                            url: "<?php echo site_url('Template_paysheet/confirm_bankTransfer'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();

                                myAlert(data[0], data[1]);

                                if( data[0] == 's'){
                                    setTimeout(function () {
                                        $('#confirm_bankTransferBtn').hide();
                                        bankTransferProcessedTB.ajax.reload();
                                    }, 1000);

                                }

                            },
                            error : function() {
                                stopLoad();
                                myAlert('e','An Error Occurred! Please Try Again.');
                                return 'e';
                            }
                        });
                    }
                }
            );
        }else{
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {'bankTransID' : bankTransID, 'letterDet' : letterDet, 'isNonPayroll':'<?php echo $isNonPayroll; ?>'},
                url: "<?php echo site_url('Template_paysheet/confirm_bankTransfer'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    myAlert(data[0], data[1]);

                    if( data[0] == 's'){
                        setTimeout(function () {
                            $('#confirm_bankTransferBtn').hide();
                            bankTransferProcessedTB.ajax.reload();
                        }, 1000);

                    }

                },
                error : function() {
                    stopLoad();
                    myAlert('e','An Error Occurred! Please Try Again.');
                    return 'e';
                }
            });
        }




    }

    function load_payment_voucher(bankTransferID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'bankTransferID': bankTransferID},
            url: "<?php echo site_url('Template_paysheet/load_payment_voucher'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#chequeteplatedrop').html(data);
                $('#cheque_modal').modal('show');
            }, error: function () {
                stopLoad();
            }
        });
    }

    function validate_wps(bankTransID){
        let url = "<?php echo site_url('Template_paysheet/WPS/'); ?>"+bankTransID;
        if( "<?= $bankTransferType?>" == 'WPS2') {
            url = "<?php echo site_url('Template_paysheet/WPS2/'); ?>"+bankTransID;
        }

        if( "<?= $bankTransferType?>" == 'WPS_MOL') {
            url = "<?php echo site_url('Template_paysheet/WPS_MOL/'); ?>"+bankTransID;
        }


        $.ajax({
            async: false,
            type: 'get',
            dataType: 'json',
            data: {'isValidate' : 'Y'},
            url: url,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if( data[0] == 's'){
                    window.open(url, '_blank');
                }
                else{
                    myAlert(data[0], data[1]);
                }
            },
            error : function() {
                stopLoad();
                myAlert('e','An Error Occurred! Please Try Again.');
                return 'e';
            }
        });
    }
    function sendemail_payroll(banktransferID,isnonpayroll)
    {
        swal(
            {
                title: "Are you sure?",
                text: "You want to send a payslip notification",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>",
                closeOnConfirm: true,
                cancelOnConfirm: true,
                allowOutsideClick: true
            },
            function (isConfirm) {
                if(isConfirm){
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'bankTransID' : banktransferID,'isnonpayroll':isnonpayroll,'payrollID':'<?php echo $payrollID; ?>','type':1},
                        url: "<?php echo site_url('Template_paysheet/send_payslipnotification'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                             myAlert(data[0], data[1]);
                            if( data[0] == 's'){
                                bankTransferProcessedTB.draw();
                            }

                        },
                        error : function() {
                            stopLoad();
                            myAlert('e','An Error Occurred! Please Try Again.');
                            return 'e';
                        }
                    });
                }
            }
        );





    }
</script>

<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-08-02
 * Time: 9:36 AM
 */
