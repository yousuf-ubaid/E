<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('finance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title =$this->lang->line('finance_budget_transfer') ;
echo head_page($title, false);

/*echo head_page('Budget', false);*/
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment();
$currency_arr = all_currency_drop(true,'ID');
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed');?><!--Confirmed--> / <?php echo $this->lang->line('common_approved');?><!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed');?><!--Not Confirmed--> / <?php echo $this->lang->line('common_not_approved');?><!--Not Approved-->
                </td>
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back');?><!--Refer-back-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary pull-right" onclick="modal_budget_transfer_header();"><i
                class="fa fa-plus"></i><?php echo $this->lang->line('finance_tr_bt_create_budget_transfer');?><!-- Create Budget Transfer-->
        </button>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="budget_transfer_table" class="<?php echo table_class(); ?>">
        <thead>
        <tr>
            <th style="min-width: 5%">#</th>
            <th style="min-width: 12%"><?php echo $this->lang->line('finance_budget_transfer_code');?><!--Budget Transfer Code--></th>
            <th style="min-width: 8%"><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('finance_common_financial_year');?><!--Financial Year--></th>
            <th style="min-width: 12%"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <th style="min-width: 15%"><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed');?><!--Confirmed--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_approved'); ?><!--Approved--></th>
            <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?><!--Action--></th>
        </tr>
        </thead>
    </table>
</div>
<div class="modal fade" id="budget_transfer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('finance_budget_transfer_header');?><!--Budget Transfer Header--></h4></div>
            <?php echo form_open('', 'role="form" id="budget_transfer_form"'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('common_document_date');?><!--Document Date--> <?php required_mark(); ?></label>

                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="documentDate"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="financeyear"><?php echo $this->lang->line('finance_common_financial_year');?><!--Financial Year--></label>
                        <?php echo form_dropdown('financeYearID', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeYearID" required onchange="fetch_finance_year_period(this.value)"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="transactionCurrencyID"><?php echo $this->lang->line('common_currency');?><!--Currency--> <?php required_mark(); ?></label>
                        <!--Currency-->
                        <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_reporting_currencyID'], 'class="form-control select2" disabled id="transactionCurrencyID" '); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group ">
                            <label>
                                <?php echo $this->lang->line('common_description'); ?> <?php required_mark(); ?>
                            </label>
                            <textarea class="form-control" id="comments" name="comments" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save_change');?><!--Save changes--></button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/finance/Budget_management','','Budget');
        });
        $('.select2').select2();
        budget_transfer_table();
        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID,periodID);

        $('.datepicYear').datetimepicker({
            useCurrent: false,
            format: 'YYYY',
        }).on('dp.change', function (ev) {
            $('#budget_transfer_form').bootstrapValidator('revalidateField', 'year');
        });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#stock_transfer_form').bootstrapValidator('revalidateField', 'tranferDate');
        });
    });

    $('#budget_transfer_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            financeYearID: {validators: {notEmpty: {message: 'Financial Year is required.'}}},
            documentDate: {validators: {notEmpty: {message: 'Document Date is required.'}}},
            comments: {validators: {notEmpty: {message: 'Description is required.'}}}

        },
    }).on('success.form.bv', function (e) {
        $('#transactionCurrencyID').prop('disabled',false);
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        //data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
        /*data.push({'name' : 'stockReturnAutoID', 'value' : stockReturnAutoID });*/
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Budget_transfer/save_budget_transfer_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                 myAlert(data[0],data[1]);
                $('#transactionCurrencyID').prop('disabled',true);
                stopLoad();
                if(data[0]=='s'){
                    $form.bootstrapValidator('resetForm', true);
                    $('#budget_transfer_modal').modal('hide');
                    budget_transfer_table();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });


    function modal_budget_transfer_header() {
        $('#comments').val('');
        $('#budget_transfer_modal').modal({backdrop: "static"});
    }

    function budget_transfer_table(selectedID=null) {
        Otable = $('#budget_transfer_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Budget_transfer/fetch_budget_entry'); ?>",
            "aaSorting": [[0, 'desc']],
            "columnDefs": [

            ],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['budgetTransferAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "budgetTransferAutoID"},
                {"mData": "documentSystemCode"},
                {"mData": "createdDate"},
                {"mData": "financeYear"},
                {"mData": "CurrencyCode"},
                {"mData": "comments"},
                {"mData": "confirmed"},
                {"mData": "approved"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [6,7,8], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
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





    function referbackbudget(id) {
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
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'budgetTransferAutoID': id},
                    url: "<?php echo site_url('Budget_transfer/referback_budjet_transfer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            budget_transfer_table() ;
                        }
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

</script>