<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('treasury', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$date_format_policy = date_format_policy();
$title = $this->lang->line('treasury_tr_bt_bank_transfer');
echo head_page($title, true);
/*echo head_page('Bank Transfer', false);*/
?>
<style>
    legend {

        margin-bottom: 10px;
    }
    </style>
<div id="filter-panel" class="collapse filter-panel">
        <div class="row">
            <div class="form-group col-sm-2">
                <label><?php echo $this->lang->line('common_from'); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="transferDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="transferDateFrom" class="form-control"  >
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label><?php echo $this->lang->line('common_to'); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="transferDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="transferDateTo" class="form-control" >
                </div>
            </div>
            
         
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from') . ' ' . $this->lang->line('common_account'); ?></label> <br>
                <!--Customer Name-->
                <?php echo form_dropdown('fromAccount[]', company_bank_account_name_drop(null, null, 1), '', 'class="form-control" id="fromAccount" onchange="bank_rec()" multiple="multiple"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_to') . ' ' . $this->lang->line('common_account'); ?></label> <br>
                <!--Customer Name-->
                <?php echo form_dropdown('toAccount[]', company_bank_account_name_drop(null, null, 1), '', 'class="form-control" id="toAccount" onchange="bank_rec()" multiple="multiple"'); ?>
            </div>

            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status'); ?> </label><br>
                <!--Status-->
                <div> <?php echo form_dropdown('status', array('all' => $this->lang->line('common_all') /*'All'*/, '1' => $this->lang->line('common_draft') /*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'common_confirmed'*/, '3' => $this->lang->line('common_approved') /*'Approved'*/, '4' => $this->lang->line('common_refer_back')), '', 'class="form-control" id="status" onchange="bank_rec()"'); ?></div>
            </div>

            <div class="form-group text-center col-sm-2">
                <button type="button" class="btn btn-default" onclick="clear_all_filters()" style="margin-top: +13%;"><i class="fa fa-ban"></i> Clear filters
                </button>
            </div>
        </div>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> / <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--> / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>

            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="open_bank_transaction()"><i
                class="fa fa-plus"></i> <?php echo $this->lang->line('treasury_tr_bt_new_bank_transaction');?><!--New Bank Transaction-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="transactionTable" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo $this->lang->line('treasury_common_bt_code');?><!--BT Code--></th>
            <th> <?php echo $this->lang->line('common_date');?><!--Date--></th>
            <th><?php echo $this->lang->line('treasury_common_ref_no');?><!--Ref No-->.</th>
            <th><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
            <th><?php echo $this->lang->line('common_from');?><!--From--></th>
            <th><?php echo $this->lang->line('common_to');?><!--To--></th>
            <th> <?php echo $this->lang->line('common_amount');?><!--Amount--></th>
            <th> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th ><?php echo $this->lang->line('common_approved');?><!--Approved--></th>
            <th></th>

        </tr>
        </thead>
    </table>
</div>

<!--modal bank Transaction -->
<div class="modal fade" id="bankTransactionModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 80%">
        <div class="modal-content" id="loadBankTransferFrom">
       <!--     <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create Bank Transaction <span id=""></span></h4></div>
            <?php /*echo form_open('','role="form" id="bank_transaction_form"'); */?>
            <div class="modal-body" id="">-->
<!--

<input type="hidden" id="decimal" name="decimal">
                <input type="hidden" id="fromBankCurrencyID" name="fromBankCurrencyID">
                <input type="hidden" id="toBankCurrencyID" name="toBankCurrencyID">
                <input type="hidden" id="toBankCurrencyCode" name="toBankCurrencyCode">


                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Document Date</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="transferedDate" value="<?php /*echo date('Y-m-d'); */?>" id="transferedDate" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group col-sm-4"><label for="financeyear">Finance
                                Year <?php /*required_mark(); */?></label> <?php /*echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); */?>
                        </div>
                        <div class="form-group col-sm-4"><label for="financeyear">Finance Year
                                Period <?php /*required_mark(); */?></label> <?php /*echo form_dropdown('financeyear_period', array('' => 'Select Finance Period'), '', 'class="form-control" id="financeyear_period" required'); */?>
                        </div>



                    </div>
                <div class="row">
                    <div class="form-group col-sm-4"><label for="description">Reference No</label> <textarea
                            class="form-control" id="referenceNo" name="referenceNo" rows="1"></textarea></div>
                    <div class="form-group col-sm-4"><label for="description">Narration</label> <textarea
                            class="form-control" id="description" name="description" rows="1"></textarea></div>
                    </div>



                    <legend style="font-size: 14px">From</legend>
                <div class="row">
                    <div class="form-group col-sm-4"><label for="">Bank
                            Account </label> <?php /*echo form_dropdown('bankFrom', company_bank_account_drop(), '', 'class="form-control" onchange="bankchange()" id="bankFrom" required"'); */?>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="creditPeriod">Amount</label>
                            <div class="input-group">
                                <div class="input-group-addon"><span id="fromcurrency"></span></div>
                                <input type="text" style="text-align: right" class="form-control" id="fromAmount" onchange="gettransferedAmount(this.value)" name="fromAmount" placeholder="00">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="creditPeriod">Exchange Rate</label>
                            <div class="input-group">
                                <input type="text" style="text-align: right" readonly class="form-control" id="conversion" name="conversion" placeholder="00">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="creditPeriod">Book Balance</label>
                            <div class="input-group">
                                <input type="text" style="text-align: right" readonly class="form-control" id="fromBankCurrentBalance" name="fromBankCurrentBalance" placeholder="00">
                            </div>
                        </div>
                    </div>



                </div>
                <legend style="font-size: 14px">To</legend>
                <div class="row">
                    <div class="form-group col-sm-4"><label for="">Bank
                            Account </label> <?php /*echo form_dropdown('bankTo', company_bank_account_drop(), '', 'class="form-control" onchange="bankchange()" id="bankTo" required"'); */?>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="creditPeriod">Amount</label>
                            <div class="input-group">
                                <div class="input-group-addon"><span id="tocurrency"></span></div>
                                <input type="text" style="text-align: right" class="form-control" readonly id="toAmount" name="toAmount" placeholder="00">
                            </div>
                        </div>
                    </div>


                </div>

-->
            <!--</div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSave">Save</button>
            </div>
            </form>-->
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div class="modal fade" id="cheque_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('accounts_payable_tr_pv_select_template');?><!--Select Template--></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="bankTransferAutoIDIdchk">
                <div class="row" id="chequeteplatedrop">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_cheque()"><?php echo $this->lang->line('accounts_payable_tr_pv_print');?><!--Print--></button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Otable;
    var currency_decimal;
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/bank_rec/erp_bank_transaction','','Bank Transfer');
        });
        bank_rec();
        currency_decimal=3;

        $('#fromAccount').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '165px',
            maxHeight: '30px'
        });
        $('#toAccount').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '165px',
            maxHeight: '30px'
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function(ev) {
            bank_rec();
        });
    });

    function bank_transaction_edit(bankTransferAutoID){
        loadform(bankTransferAutoID);
        $('#bankTransactionModal').modal({backdrop: "static"});
    }


    function loadform(bankTransferAutoID){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'html',
            data : {bankTransferAutoID:bankTransferAutoID},
            url :"<?php echo site_url('Bank_rec/getbankTransferform'); ?>",
            beforeSend: function () {

            },
            success : function(data){
              $('#loadBankTransferFrom').html(data);

            },error : function(){

            }
        });
    }

    function gettransferedAmount(amount){
        var conversion=$('#conversion').val();
        var decimal= currency_decimal;
        if(conversion !=''){
           exchangeamount=amount*conversion;
            $('#toAmount').val(exchangeamount.toFixed(decimal));
        }
    }

    function bankchange(){
        var bankTo=$('#bankTo').val();
        var bankFrom=$('#bankFrom').val();
        if(bankTo!==''){
            getcurrencyID(bankTo,'');
        }
        if(bankFrom!==''){
            getcurrencyID('',bankFrom);
        }

        if(bankTo!='' && bankFrom!=''){
            getexchangerate(bankTo,bankFrom);

        }

        if(bankFrom==''){
            $('#fromcurrency').html('');
            $('#fromAmount').val('');
            $('#conversion').val('');
            $('#fromBankCurrentBalance').val('');
            $('#tocurrency').html('');
            $('#toAmount').val('');
        }
        else if(bankTo==''){

            $('#tocurrency').html('');
            $('#toAmount').val('');
        }
        else{
            getDecimalPlaces(bankFrom)
        }


    }

function getcurrencyID(bankTo,bankFrom){
    $.ajax({
        async : true,
        type : 'post',
        dataType : 'json',
        data : {bankTo:bankTo,bankFrom:bankFrom},
        url :"<?php echo site_url('Bank_rec/getcurrencyID'); ?>",
        beforeSend: function () {

        },
        success : function(data){
            if(bankFrom !=''){
                $('#fromcurrency').text(data['fromBankCurrencyCode']);
                currency_decimal=data['bankCurrencyDecimalplaces'];
            }
            if(bankTo !=''){
                $('#tocurrency').text(data['toBankCurrencyCode']);
            }


        },error : function(){

        }
    });
}

    function   getexchangerate(bankTo,bankFrom){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {bankTo:bankTo,bankFrom:bankFrom},
            url :"<?php echo site_url('Bank_rec/getexchangerate'); ?>",
            beforeSend: function () {

            },
            success : function(data){
                $('#conversion').prop('readonly', false);
                $('#fromcurrency').text(data[0]['subCurrencyCode']);
                $('#tocurrency').text(data[0]['masterCurrencyCode']);

                //$('#fromcurrency').text(data[0]['masterCurrencyCode']);
               // $('#tocurrency').text(data[0]['subCurrencyCode']);

                $('#conversion').val(data[0]['conversion']);
                $('#decimal').val(data['decimal']);
                $('#fromBankCurrencyID').val(data['fromBankCurrencyID']);
                $('#toBankCurrencyCode').val(data[0]['masterCurrencyCode']);
                $('#fromBankCurrencyCode').val(data[0]['subCurrencyCode']);
                $('#toBankCurrencyID').val(data['toBankCurrencyID']);
                $('#fromBankCurrentBalance').val(data['fromBankCurrentBalance']);
                gettransferedAmount($('#fromAmount').val());
                $('#bank_transaction_form').bootstrapValidator('revalidateField', 'toAmount');
                if(data['fromBankCurrencyID']==data['toBankCurrencyID']){
                    $('#conversion').prop('readonly', true);
                }

            },error : function(){

            }
        });

    }

    function open_bank_transaction() {
        loadform('');
        $('#bankTransactionModal').modal({backdrop: "static"});
    }

    function bank_rec() {
        var Otable = $('#transactionTable').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Bank_rec/fetch_bank_transaction'); ?>",
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
            "columnDefs": [
                {"width": "2%", "targets": 0},
                {"width": "6%", "targets": 1},
                {"width": "6%", "targets": 2},
                {"width": "7%", "targets": 3},
                {"width": "18%", "targets": 5},
                {"width": "18%", "targets": 6},
                {"width": "6%", "targets": 8},
                {"width": "6%", "targets": 9},
                {"width": "8%", "targets":10}
            ],
            "aoColumns": [
                {"mData": "bankTransferAutoID"},
                {"mData": "bankTransferCode"},
                {"mData": "transferedDate"},
                {"mData": "referenceNo"},
                {"mData": "narration"},
                {"mData": "frombank"},
                {"mData": "toBank"},
                {"mData": "transferedAmount"},
                {"mData": "confirm"},
                {"mData": "approvedYN"},
                {"mData": "edit"},

            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({ "name": "transferDateFrom","value": $("#transferDateFrom").val()});
                aoData.push({ "name": "transferDateTo","value": $("#transferDateTo").val()});
                aoData.push({ "name": "fromAccount","value": $("#fromAccount").val()});
                aoData.push({ "name": "toAccount","value": $("#toAccount").val()});
                aoData.push({ "name": "status","value": $("#status").val()});
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
    function fetch_finance_year_period(companyFinanceYearID,select_value){

        $.ajax({
            async       :true,
            type        :'post',
            dataType    :'json',
            data        :{'companyFinanceYearID':companyFinanceYearID,'fyDepartmentID':'BT'},
            url         :"<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success : function(data){
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom']+' - '+text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    };
                }
            },error : function(){
                swal("Cancelled", "Your "+value+" file is safe :)", "error");
            }
        });
    }

    function referbackgrv(bankTransferAutoID){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>",/*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async : true,
                    type : 'post',
                    dataType : 'json',
                    data : {'bankTransferAutoID':bankTransferAutoID},
                    url :"<?php echo site_url('Bank_rec/refer_bank_transaction'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            bank_rec();
                        }
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
        });
    }

    function delete_item(id,value){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('treasury_common_you_want_to_delete_this_record');?>",/*You want to delete this record!*/
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
                    data : {'bankTransferAutoID':id},
                    url :"<?php echo site_url('Bank_rec/delete_banktransfer_master'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success : function(data){
                        refreshNotifications(true);
                        stopLoad();
                        bank_rec();
                    },error : function(){
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function getDecimalPlaces(bankFrom){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'bankFrom':bankFrom},
            url :"<?php echo site_url('Bank_rec/getDecimalPlaces'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
               //alert(data['bankCurrencyDecimalPlaces']);
                currency_decimal= data['bankCurrencyDecimalPlaces'];
            },error : function(){
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }


    function validateFloatKeyPress(el, evt) {
        if(currency_decimal==0){
            currency_decimal=3
        }
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if(number.length>1 && charCode == 46){
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if( caratPos > dotPos && dotPos>-(currency_decimal-1) && (number[1] && number[1].length > (currency_decimal-1))){
            return false;
        }
        return true;
    }

    //thanks: http://javascript.nwbox.com/cursor_position/
    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function cheque_print_modal(id,count,coaChequeTemplateID) {
        if(count=1){
            var bankTransferAutoID=id;
            var coaChequeTemplateID=coaChequeTemplateID;
            if(coaChequeTemplateID==''){
                myAlert('e', 'Select Template');
            }else{
                window.open("<?php echo site_url('Bank_rec/cheque_print') ?>" +'/'+ bankTransferAutoID +'/'+ coaChequeTemplateID);
            }
        }else{
            $('#cheque_modal').modal('show');
            $('#bankTransferAutoIDIdchk').val(id);
            load_Cheque_templates(id)
        }

    }

    function load_Cheque_templates(id){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'bankTransferAutoID': id},
            url: "<?php echo site_url('Bank_rec/load_Cheque_templates'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#chequeteplatedrop').html(data);
            }, error: function () {
                stopLoad();
            }
        });
    }

    function print_cheque(){
        var bankTransferAutoID=$('#bankTransferAutoIDIdchk').val();
        var coaChequeTemplateID=$('#coaChequeTemplateID').val();
        if(coaChequeTemplateID==''){
            myAlert('e', 'Select Template');
        }else{
            window.open("<?php echo site_url('Bank_rec/cheque_print') ?>" +'/'+ bankTransferAutoID +'/'+ coaChequeTemplateID);
        }

    }

    function clear_all_filters() {
        $('#transferDateFrom').val("");
        $('#transferDateTo').val("");
        $('#fromAccount').multiselect2('deselectAll', false);
        $('#fromAccount').multiselect2('updateButtonText');
        $('#toAccount').multiselect2('deselectAll', false);
        $('#toAccount').multiselect2('updateButtonText');
        $('#status').val('all').change();
        bank_rec();
    }
</script>