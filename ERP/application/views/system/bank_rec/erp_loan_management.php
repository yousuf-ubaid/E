<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('treasury_tr_lm_loan_management');
echo head_page($title, false);
$financeyear_arr = all_financeyear_drop(true);
$date_format_policy = date_format_policy();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();

$current_date = format_date($this->common_data['current_date']);

/*echo head_page('Loan Management',false); */?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-info">&nbsp;</span> <?php echo $this->lang->line('treasury_common_initiated');?><!--Initiated-->
                </td>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_closed');?><!--Closed-->
                </td>

            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="fetchPage('system/bank_rec/erp_loan_mgt_new','','Add Journal Entry','Journal Entry');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_create_new');?><!--Create New--> </button>
    </div>
</div><hr>
<div class="table-responsive">
    <table id="journal_entry_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 12%"><?php echo $this->lang->line('common_code');?><!--Code--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_from');?> <!--From--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_to');?> <!--To--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_bank');?><!--Bank--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('treasury_tr_lm_int');?><!--Int-->.%</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('treasury_tr_lm_cur');?><!--Cur-->.</th>
            <th style="min-width: 15%"><?php echo $this->lang->line('treasury_tr_lm_facility_limit');?><!--Facility Limit--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('treasury_tr_lm_utilized');?><!--Utilized--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('treasury_tr_lm_facility_balance');?><!--Facility Balance--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('treasury_tr_lm_setltlement');?><!--Settlement--></th>
         <!--   <th style="min-width: 15%">Due Balance</th>-->
            <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Staus--></th>
            <th style="width: 25%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="receipt_voucher_modal" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <form method="post" id="receiptvoucher_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Create Receipt Voucher</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="vouchertype" name="vouchertype" value="DirectIncome">
                    <input type="hidden" id="bankFacilityID" name="bankFacilityID">
                    <input type="hidden" id="bankID" name="bankID">
                   <!-- // <input type="hidden" id="segment" name="segment">
                    <input type="hidden" id="customerID" name="customerID"> -->
                    <input type="hidden" id="transactionCurrencyID" name="transactionCurrencyID">
                    <div class="row">
                        <div class="col-sm-4"><span style="color: black;font-family: sans-serif;" id="invoiceBal"></span></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_date'); ?>
                                <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="RVdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVdate" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('common_reference'); ?>
                                <!--Reference--> # </label>
                            <input type="text" name="referenceno" id="referenceno" class="form-control">
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="segment"><?php echo $this->lang->line('common_segment');?><!--Segment--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        if ($financeyearperiodYN == 1) {
                            ?>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_year'); ?>
                                    <!--Financial Year--> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_period'); ?>
                                    <!--Financial Period--> <?php required_mark(); //
                                    ?></label>
                                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                            </div>
                        <?php } ?>
                       
                    </div>

                    <!-- <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="RVbankCode"><?php echo $this->lang->line('accounts_receivable_common_bank_or_cash'); ?>
                                <?php required_mark(); ?></label>
                            <?php echo form_dropdown('RVbankCode', company_bank_account_drop(), '', 'class="form-control select2" id="RVbankCode" onchange="set_payment_method()" required'); ?>
                        </div>
                        <div class="form-group col-sm-4 paymentmoad">
                            <label><?php echo $this->lang->line('accounts_receivable_common_cheque_number'); ?>
                                </label>
                            <input type="text" name="RVchequeNo" id="RVchequeNo" class="form-control">
                        </div>
                        <div class="form-group col-sm-4 paymentmoad">
                            <label><?php echo $this->lang->line('accounts_receivable_common_cheque_date'); ?>
                                <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="RVchequeDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVchequeDate" class="form-control">
                            </div>
                        </div>
                    </div> -->


                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onClick="save_receiptvoucher_from()">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="payment_voucher_modal" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <form method="post" id="receiptvoucher_form1">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Create Payment Voucher</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="vouchertype1" name="vouchertype" value="DirectIncome">
                    <input type="hidden" id="bankFacilityID1" name="bankFacilityID">
                    <input type="hidden" id="bankID1" name="bankID">
                   <!-- // <input type="hidden" id="segment" name="segment">
                    <input type="hidden" id="customerID" name="customerID"> -->
                    <input type="hidden" id="transactionCurrencyID1" name="transactionCurrencyID">
                    <div class="row">
                        <div class="col-sm-4"><span style="color: black;font-family: sans-serif;" id="invoiceBal"></span></div>
                    </div>
                    <hr>
                    <div class="row">

                        <div class="form-group col-sm-4">
                            <label for="segment"><?php echo $this->lang->line('common_segment');?><!--Segment--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
                        </div>
                    </div>
                    

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onClick="save_receiptvoucher_from()">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.headerclose').click(function(){
            fetchPage('system/bank_rec/erp_loan_management','','Loan Management');
        });
      journal_entry_table();


     // $(".paymentmoad").hide();
        $('.select2').select2();

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function(ev) {
            $('#receiptvoucher_form1').bootstrapValidator('revalidateField', 'RVdate');
        });
        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'] ?? '')); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'] ?? '')); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'] ?? '')); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'] ?? '')); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

    });

    function save_receiptvoucher_from(){

        var form1= $('#receiptvoucher_form').serializeArray();

        form1.push({
                'name': 'companyFinanceYear',
                'value': $('#financeyear option:selected').text()
            });
           
            form1.push({
                'name': 'bankFacilityID',
                'value': $('#bankFacilityID').val()
            });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: form1,
            url: "<?php echo site_url('Bank_rec/save_receiptvoucher_from_LO_header'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                if (data[0] == 's') {
                   // stopLoad();
                    $("#segment").val('');
                    $("#customerID").val('');
                    $("#invoicID").val('');
                    $("#transactionCurrencyID").val('');
                    $("#referenceno").val('');
                    $("#RVbankCode").val('').change();
                    $("#RVchequeNo").val('');
                    $("#receipt_voucher_modal").modal('hide');
                    confirmReceiptVoucher_loan(data[2]);
                    journal_entry_table();
                } else {
                    stopLoad();
                    myAlert(data[0], data[1]);
                }
            },
            error: function() {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    function confirmReceiptVoucher_loan(receiptVoucherAutoId){
        if (receiptVoucherAutoId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'receiptVoucherAutoId': receiptVoucherAutoId},
            url: "<?php echo site_url('Receipt_voucher/receipt_confirmation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['error']==1){
                    myAlert('e',data['message']);
                }else if(data['error']==2){
                    myAlert('w',data['message']);
                }
                else {
                    //myAlert('s',data['message']);
                    swal("Success", "Receipt Voucher " + data['code'] + " Created Successfully " , "success");
                }
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

        }
        ;
    }

    function journal_entry_table(){
        var Otable = $('#journal_entry_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Bank_rec/bankfacilityloan'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "bankFacilityID"},
                {"mData": "facilityCode"},
                {"mData": "facilityDateFrom"},
                {"mData": "facilityDateTo"},
                {"mData": "bank"},
                {"mData": "narration"},
                {"mData": "rateOfInterest"},
                {"mData": "CurrencyShortCode"},
                {"mData": "amount"},
                {"mData": "utilized"},
                {"mData": "settlement"},
                {"mData": "balance"},  {"mData": "status"},
              /*  {"mData": "action"},*/
                {"mData": "edit"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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

    function delete_loan(id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'bankFacilityID':id},
                    url :"<?php echo site_url('Bank_rec/delete_bankloan'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        refreshNotifications(true);
                        journal_entry_table();
                        stopLoad();
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function open_receipt_voucher_modal(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'bankFacilityID': id
            },
            url: "<?php echo site_url('Bank_rec/open_receipt_voucher_modal'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
              //  $(".paymentmoad").hide();

                if(data['master']['principalGlCode'] !=null && data['master']['interestGlCode'] !=null && data['master']['libilityGlCode'] !=null){
                    $("#bankFacilityID").val(id);
                    // $("#segment").val(data['master']['segmentID']);
                    /// $("#customerID").val(data['master']['customerID']);
                        $("#transactionCurrencyID").val(data['master']['currencyID']);
                        $("#referenceno").val(data['master']['facilityCode']);
                        $("#bankID").val(data['master']['bankID']);
                        $("#RVchequeNo").val('');
                    
                    // $('#invoiceBal').html('Invoice Balance :- ' + data['balance'] + ' (' + data['master']['transactionCurrency'] + ')');
                    $("#receipt_voucher_modal").modal('show');
                }else{
                    myAlert('w','Please fill GL Mapping');
                }
                
               
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
     

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'companyFinanceYearID': companyFinanceYearID
            },
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function(data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function(val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    };
                }
            },
            error: function() {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

</script>